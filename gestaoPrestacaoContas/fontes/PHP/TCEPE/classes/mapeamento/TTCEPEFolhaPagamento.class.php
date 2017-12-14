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
    * 
    * Data de Criação   : 02/11/2014

    * @author Analista:
    * @author Desenvolvedor:  Lisiane Morais
    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCEPEFolhaPagamento extends Persistente
{

    /*
     * Método Construtor
     *
     * @return void
     */
    public function TTCEPEFolhaPagamento()
    {
        parent::Persistente();
    }


    /**
    * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaUnidadeOrcamentaria.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
    */
    public function recuperaFolhaPagamento(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        
        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaFolhaPagamento().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaFolhaPagamento()
    { 
        $stSql = " SELECT * FROM tcepe.recupera_folha_pagamento ('".$this->getDado('stDataInicial')."',
                                                                   '".$this->getDado('stDataFinal')."',
                                                                   ".$this->getDado('inCodPeriodoMovimentacao').",
                                                                   '".$this->getDado('stEntidade')."'
                                                                ) AS retorno
                                                                (
                                                                    reservado_tce INTEGER,
                                                                    matricula INTEGER,
                                                                    cod_cargo INTEGER,
                                                                    cpf VARCHAR,
                                                                    mes_folha TEXT,
                                                                    ano_folha TEXT,
                                                                    cod_fonte_recurso INTEGER,
                                                                    horas_semanais INTEGER,
                                                                    tipo_folha INTEGER,
                                                                    valor_previdencia NUMERIC,
                                                                    valor_irrf NUMERIC
                                                                )
                ";
        return $stSql;
    }

}
?>