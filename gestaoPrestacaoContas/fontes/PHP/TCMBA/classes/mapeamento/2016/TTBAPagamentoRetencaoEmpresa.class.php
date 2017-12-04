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
    * Extensão da Classe de mapeamento Arquivo: PagRetEmpres.txt
    * Data de Criação: 02/09/2015

    * @author Analista: Gelson Wolvowski Gonçalves
    * @author Desenvolvedor: Arthur Cruz

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TTBAPagamentoRetencaoEmpresa.class.php 63896 2015-11-03 19:03:23Z michel $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTBAPagamentoRetencaoEmpresa extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function __construct()
    {
        parent::Persistente();
    }

    public function recuperaDadosTribunal(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaDadosTribunal().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaDadosTribunal()
    {
        $stSql = " SELECT 1 AS tipo_registro
                        , pagamento.cod_ordem AS num_pagamento
                        , pagamento.exercicio_ordem AS ano_criacao
                        , despesa.num_orgao AS cod_orgao
                        , despesa.num_unidade AS unidade_orcamentaria
                        , pagamento.cod_empenho AS num_empenho
                        , pagamento.cod_empenho AS num_sub_empenho
                        , pagamento.nom_conta_retencao AS nome_conta_retencao
                        , REPLACE(pagamento.cod_estrutural_retencao,'.','') AS conta_contabil
                        , COALESCE(SUM(pagamento.vl_pago_retencao),0.00) AS vl_pagamento_retencao
                        , ".$this->getDado('stExercicio').$this->getDado('inMes')." AS competencia
                        , to_char(pagamento.timestamp_pagamento,'dd/mm/yyyy') AS dt_pagamento_retencao
                        , ".$this->getDado('inCodGestora')." AS unidade_gestora
                        , REPLACE(pagamento.cod_estrutural_plano_pagamento,'.','') AS conta_contabil_pagadora
                        , pagamento.nom_conta_plano_pagamento AS nome_conta_pagadora
                        , pagamento_tipo_documento_pagamento.cod_tipo AS tipo_pagamento
                        , pagamento_tipo_documento_pagamento.num_documento AS detalhe_tipo_pagamento

                     FROM empenho.fn_relatorio_pagamento_ordem_nota_empenho('".$this->getDado('stExercicio')."','".$this->getDado('stEntidade')."','',0,'',0,'',0,TRUE,TRUE) AS pagamento

               INNER JOIN empenho.pre_empenho_despesa
                       ON pagamento.exercicio_empenho	= pre_empenho_despesa.exercicio
                      AND pagamento.cod_pre_empenho 	= pre_empenho_despesa.cod_pre_empenho

               INNER JOIN orcamento.despesa
                       ON despesa.exercicio    = pre_empenho_despesa.exercicio
                      AND despesa.cod_despesa  = pre_empenho_despesa.cod_despesa

               INNER JOIN orcamento.conta_despesa
                       ON conta_despesa.cod_conta = pre_empenho_despesa.cod_conta
                      AND conta_despesa.exercicio = pre_empenho_despesa.exercicio
                      AND conta_despesa.cod_estrutural NOT LIKE ('3.1.%')

                LEFT JOIN tcmba.pagamento_tipo_documento_pagamento
                       ON pagamento_tipo_documento_pagamento.cod_entidade = pagamento.cod_entidade
                      AND pagamento_tipo_documento_pagamento.exercicio    = pagamento.exercicio
                      AND pagamento_tipo_documento_pagamento.timestamp    = pagamento.timestamp_pagamento
                      AND pagamento_tipo_documento_pagamento.cod_nota     = pagamento.cod_nota

                    WHERE to_date(to_char(pagamento.timestamp_pagamento,'dd/mm/yyyy'),'dd/mm/yyyy') BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy')
                                                                                                        AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
                      AND pagamento.cod_estrutural_plano_pagamento ILIKE '1.1.1.1.1.01%'

                 GROUP BY tipo_registro
                        , num_pagamento
                        , pagamento.exercicio_ordem
                        , pagamento.exercicio
                        , despesa.num_orgao
                        , despesa.num_unidade
                        , pagamento.cod_empenho
                        , pagamento.nom_conta_retencao
                        , pagamento.cod_estrutural_retencao
                        , dt_pagamento_retencao
                        , pagamento.cod_estrutural_plano_pagamento
                        , pagamento.nom_conta_plano_pagamento 
                        , pagamento_tipo_documento_pagamento.cod_tipo
                        , pagamento_tipo_documento_pagamento.num_documento

                 ORDER BY num_empenho
                        , dt_pagamento_retencao ";
        return $stSql;
    }

    function recuperaLogErro(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaLogErro().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    function montaRecuperaLogErro()
    {
        $stSql = " SELECT count(to_char(pagamento.timestamp_pagamento,'yyyy')) registros
                        , count(pagamento_tipo_documento_pagamento.exercicio) as obrigatorio

                     FROM empenho.fn_relatorio_pagamento_ordem_nota_empenho('".$this->getDado('stExercicio')."','".$this->getDado('stEntidade')."','',0,'',0,'',0,TRUE,TRUE) AS pagamento

                LEFT JOIN tcmba.pagamento_tipo_documento_pagamento
                       ON pagamento_tipo_documento_pagamento.cod_entidade = pagamento.cod_entidade
                      AND pagamento_tipo_documento_pagamento.exercicio    = pagamento.exercicio
                      AND pagamento_tipo_documento_pagamento.timestamp    = pagamento.timestamp_pagamento
                      AND pagamento_tipo_documento_pagamento.cod_nota     = pagamento.cod_nota

                    WHERE to_date(to_char(pagamento.timestamp_pagamento,'dd/mm/yyyy'),'dd/mm/yyyy') BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy')
                                                                                                        AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
                      AND pagamento.cod_estrutural_plano_pagamento ILIKE '1.1.1.1.1.01%'
                      ";
        return $stSql;
    }

    public function __destruct() {}
}

?>