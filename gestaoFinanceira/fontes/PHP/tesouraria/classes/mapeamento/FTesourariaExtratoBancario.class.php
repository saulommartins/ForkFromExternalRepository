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
    * Classe de mapeamento da tabela FN_RELATORIO_EXTRATO_BANCARIO
    * Data de Criação: 16/11//2005

    * @author Analista: Lucas Leusin Oiagem
    * @author Desenvolvedor: Jose Eduardo Porto

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2007-07-26 21:49:47 -0300 (Qui, 26 Jul 2007) $

    * Casos de uso: uc-02.04.10
*/

/*
$Log$
Revision 1.19  2007/07/27 00:49:47  vitor
Bug#9760#

Revision 1.18  2007/07/25 22:06:19  vitor
Bug#9760#

Revision 1.17  2007/05/30 19:24:51  bruce
Bug #9116#

Revision 1.16  2007/04/30 21:38:57  cako
Bug #9103#

Revision 1.15  2006/11/14 20:54:21  cleisson
Bug #7233# (corrigido pelo cako)

Revision 1.14  2006/09/05 09:43:47  jose.eduardo
Bug #6316#

Revision 1.13  2006/07/05 20:38:37  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class FTesourariaExtratoBancario extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function __construct()
    {
        parent::Persistente();
    }

    function montaRecuperaTodos()
    {
        $stSql  = "select *                                                                               \n";
        $stSql .= "  from tesouraria.fn_relatorio_extrato_bancario (" . $this->getDado("inCodPlano") .",  \n";
        $stSql .= "  '" . $this->getDado("stExercicio") . "',                                             \n";
        $stSql .= "  '" . $this->getDado("stEntidade")."',                                                \n";
        $stSql .= "  '" . $this->getDado("stDataInicial")."',                                             \n";
        $stSql .= "  '" . $this->getDado("stDataFinal")."',                                               \n";
        $stSql .= "  '" . $this->getDado("botcems")."') as retorno(                                       \n";
        $stSql .= "     hora                text                                                          \n";
        $stSql .= "    ,data                text                                                          \n";
        $stSql .= "    ,descricao           varchar                                                       \n";
        $stSql .= "    ,valor               numeric                                                       \n";
        $stSql .= "    ,cod_lote            numeric                                                       \n";
        $stSql .= "    ,cod_arrecadacao     numeric                                                       \n";
        $stSql .= "    ,tipo_valor          varchar                                                       \n";
        $stSql .= "    ,situacao            varchar                                                       \n";
        $stSql .= "    ,cod_situacao        varchar                                                       \n";
        $stSql .= "  )                                                                                      ";
    
        return $stSql;
    
    }

    function recuperaDadosBancarios(&$rsRecordSet, $stCondicao = "", $stOrder = "",  $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        if ($stOrder == "") {
            $stOrder .= " ORDER BY 1";
        }
        $stSql = $this->montaRecuperaDadosBancarios().$stCondicao.$stOrder;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }

    function montaRecuperaDadosBancarios()
    {
        $stSql = " SELECT
                        PA.cod_plano,
                        PC.nom_conta,
                        PC.cod_estrutural,
                        (select publico.fn_nivel(PC.cod_estrutural)) as nivel,
                        PB.cod_banco,
                        PB.cod_agencia,
                        PB.conta_corrente,
                        MA.nom_agencia,
                        MB.nom_banco
                    FROM
                        contabilidade.plano_conta       as PC,
                        contabilidade.plano_analitica   as PA,
                        contabilidade.plano_banco       as PB
                        LEFT JOIN monetario.agencia as MA ON(
                            PB.cod_banco    = MA.cod_banco      AND
                            PB.cod_agencia  = MA.cod_agencia
                        )
                        LEFT JOIN monetario.banco as MB ON(
                            MA.cod_banco    = MB.cod_banco
                        )
                    WHERE
                        PC.cod_conta    = PA.cod_conta      AND
                        PC.exercicio    = PA.exercicio      AND
                        PA.cod_plano    = PB.cod_plano      AND
                        PA.exercicio    = PB.exercicio      AND
                        PB.exercicio = '".$this->getDado("stExercicio")."'
                 ";
        if ( ( $this->getDado("inCodPlanoInicial") ) and ( $this->getDado("inCodPlanoFinal") ) ) {
            $stSql .= " AND PA.cod_plano BETWEEN " . $this->getDado("inCodPlanoInicial") . " AND " . $this->getDado("inCodPlanoFinal") ;
        } elseif ( $this->getDado("inCodPlanoInicial") ) {
            $stSql .= " AND PA.cod_plano =" . $this->getDado("inCodPlanoInicial") ;
        } elseif ( $this->getDado("inCodPlanoFinal") ) {
            $stSql .= " AND PA.cod_plano =" . $this->getDado("inCodPlanoFinal") ;
        }
    
        return $stSql;
    }
    
    public function recuperaSaldoAnteriorAtual(&$rsRecordSet, $stCondicao = "", $stOrder = "",  $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
    
        if ($stOrder != "") {
            if( !strstr( $stOrder, "ORDER BY" ) )
                $stOrder = " ORDER BY ".$stOrder;
        }
        $stSql = $this->montaRecuperaSaldoAnteriorAtual().$stCondicao;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }

    public function montaRecuperaSaldoAnteriorAtual()
    {
        $stSql = "
          SELECT *
            FROM tesouraria.fn_saldo_conta_tesouraria ( '". $this->getDado("stExercicio") ."'
                                                      , ". $this->getDado("inCodPlano") ."
                                                      , '". $this->getDado("stDtInicial")."'
                                                      , '". $this->getDado("stDtFinal")."'
                                                      , ". $this->getDado("boMovimentacao")."
                                                      )
        \n";
        return $stSql;
    
    }

}
