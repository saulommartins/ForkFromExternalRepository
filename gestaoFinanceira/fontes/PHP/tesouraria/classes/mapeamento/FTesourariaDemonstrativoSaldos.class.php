<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
?>
<?php
/**
    * Classe Mapeamento de Função Relatório Demonstrativo Saldos
    * Data de Criação   : 24/08/2006

    * @author Analista:
    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @ignore

    $Revision: 30668 $
    $Name$
    $Autor:$
    $Date: 2007-12-05 15:12:56 -0200 (Qua, 05 Dez 2007) $

    * Casos de uso: uc-02.04.24
*/
/*
$Log$
Revision 1.6  2006/12/05 16:21:22  cako
Bug #7239#

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class FTesourariaDemonstrativoSaldos extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function FTesourariaDemonstrativoSaldos()
    {
        parent::Persistente();
    }

    public function recuperaDemonstrativoSaldos(&$rsRecordSet, $stOrderBy = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stFiltro    = "";

        if ($stOrderBy == "estrutural") {
            $stFiltro = " ORDER BY cod_estrutural ASC ";
        } elseif ($stOrderBy == "reduzido") {
            $stFiltro = " ORDER BY cod_plano ASC ";
        } elseif ($stOrderBy == "recurso") {
            $stFiltro = " ORDER BY cod_recurso ASC ";
        }

        $stSql = $this->montaRecuperaDemonstrativoSaldos().$stFiltro;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaDemonstrativoSaldos()
    {
        $stSql  = "SELECT *                                                                                        \n";
        $stSql .= "FROM tesouraria.fn_relatorio_demostrativo_saldos('".$this->getDado("stExercicio")."',           \n";
        $stSql .= "                                               '".$this->getDado("inCodEntidade")."',           \n";
        $stSql .= "                                               '".$this->getDado("dtDataInicio")."',            \n";
        $stSql .= "                                               '".$this->getDado("dtDataFim")."',               \n";
        $stSql .= "                                               '".$this->getDado("stCodEstruturalInicio")."',   \n";
        $stSql .= "                                               '".$this->getDado("stCodEstruturalFim")."',      \n";
        $stSql .= "                                               '".$this->getDado("inCodReduzidoInicio")."',     \n";
        $stSql .= "                                               '".$this->getDado("inCodReduzidoFim")."',        \n";
        $stSql .= "                                               '".$this->getDado("inCodRecurso")."',            \n";
        $stSql .= "                                               '".$this->getDado("boSemMovimento")."',          \n";
        $stSql .= "                                               '".$this->getDado("stDestinacaoRecurso")."',     \n";
        $stSql .= "                                               '".$this->getDado("inCodDetalhamento")."',       \n";
        $stSql .= "                                               '".$this->getDado("boUtilizaEstruturalTCE")."'   \n";
        $stSql .= ") as retorno( exercicio          VARCHAR                                                        \n";
        $stSql .= "             ,cod_estrutural     VARCHAR                                                        \n";
        $stSql .= "             ,cod_plano          INTEGER                                                        \n";
        $stSql .= "             ,nom_conta          VARCHAR                                                        \n";
        $stSql .= "             ,saldo_anterior     NUMERIC                                                        \n";
        $stSql .= "             ,vl_credito         NUMERIC                                                        \n";
        $stSql .= "             ,vl_debito          NUMERIC                                                        \n";
        $stSql .= "             ,cod_recurso        INTEGER                                                        \n";
        $stSql .= "             ,nom_recurso        VARCHAR                                                        \n";
        $stSql .= ")                                                                                               \n";

        return $stSql;
    }

    public function recuperaDemonstrativoSaldosAgrupadoContaCorrente(&$rsRecordSet, $stOrderBy = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stFiltro    = "";

        if ($stOrderBy == "estrutural") {
            $stFiltro = " ORDER BY cod_estrutural ASC ";
        } elseif ($stOrderBy == "reduzido") {
            $stFiltro = " ORDER BY cod_plano ASC ";
        } elseif ($stOrderBy == "recurso") {
            $stFiltro = " ORDER BY cod_recurso ASC ";
        }

        $stSql = $this->montaRecuperaDemonstrativoSaldosAgrupadoContaCorrente().$stFiltro;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaDemonstrativoSaldosAgrupadoContaCorrente()
    {
        $stSql  = " SELECT *                                                                                        
                    FROM tesouraria.fn_relatorio_demostrativo_saldos_conta_corrente('".$this->getDado("stExercicio")."',
                                                       '".$this->getDado("inCodEntidade")."',
                                                       '".$this->getDado("dtDataInicio")."',
                                                       '".$this->getDado("dtDataFim")."',
                                                       '".$this->getDado("stCodEstruturalInicio")."',
                                                       '".$this->getDado("stCodEstruturalFim")."',
                                                       '".$this->getDado("inCodReduzidoInicio")."',
                                                       '".$this->getDado("inCodReduzidoFim")."',
                                                       '".$this->getDado("inCodRecurso")."',
                                                       '".$this->getDado("boSemMovimento")."',
                                                       '".$this->getDado("stDestinacaoRecurso")."',     
                                                       '".$this->getDado("inCodDetalhamento")."',
                                                       '".$this->getDado("boUtilizaEstruturalTCE")."'
                    ) as retorno(   exercicio          VARCHAR
                                    ,cod_estrutural    VARCHAR
                                    ,des_conta          VARCHAR                                                                     
                                    ,nom_conta          VARCHAR                                                        
                                    ,saldo_anterior     NUMERIC                                                        
                                    ,vl_credito         NUMERIC                                                        
                                    ,vl_debito          NUMERIC                                                                     
                    )   
                    ORDER BY cod_estrutural ASC  
        ";

        return $stSql;
    }
}
