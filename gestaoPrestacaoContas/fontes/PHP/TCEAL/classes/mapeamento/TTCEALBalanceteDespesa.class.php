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
    * Extensão da Classe de Mapeamento TTCEALBalanceteDespesa
    *
    * Data de Criação: 04/07/2014
    *
    * @author: Evandro Melos
    *
    $Id: TTCEALBalanceteDespesa.class.php 65563 2016-05-31 20:36:59Z michel $
    *
    * @ignore
    *
*/
class TTCEALBalanceteDespesa extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCEALBalanceteDespesa()
    {
        parent::Persistente();        
        $this->setDado('exercicio',Sessao::getExercicio());
    }
    
    public function recuperaBalanceteDespesa(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaBalanceteDespesa().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaBalanceteDespesa()
    {
        $stSql ="SELECT DISTINCT
                          codigo_ua
                        , cod_und_gestora
                        , LPAD(cod_orgao::VARCHAR, 2, '0')               AS cod_orgao
                        , LPAD(cod_unid_orcamentaria::VARCHAR, 4, '0')   AS cod_unid_orcamentaria
                        , LPAD(cod_funcao::VARCHAR, 2, '0')              AS cod_funcao
                        , LPAD(cod_subfuncao::VARCHAR, 3, '0')           AS cod_subfuncao
                        , LPAD(cod_programa::VARCHAR, 4, '0')            AS cod_programa
                        , LPAD(cod_proj_atividade::VARCHAR, 4, '0')      AS cod_proj_atividade
                        , RPAD(REPLACE(cod_conta_despesa,'.',''),16,'0') AS cod_conta_despesa
                        , RPAD('622110000000000',17,'0')                 AS cod_conta_contabil
                        , LPAD(cod_rec_vinculado::VARCHAR, 9, '0')       AS cod_rec_vinculado
                        , SUM(dotacao_inicial)                           AS dotacao_inicial
                        , SUM(atualizacao_monetaria)                     AS atualizacao_monetaria
                        , SUM(credito_sup_superavit)                     AS credito_sup_superavit
                        , SUM(credito_sup_excesso_arrecadacao)           AS credito_sup_excesso_arrecadacao
                        , SUM(credito_sup_op_credito)                    AS credito_sup_op_credito
                        , SUM(credito_sup_reducao)                       AS credito_sup_reducao
                        , SUM(cred_esp_superavit)                        AS cred_esp_superavit
                        , SUM(cred_esp_excesso_arrecadacao)              AS cred_esp_excesso_arrecadacao
                        , SUM(cred_esp_op_credito)                       AS cred_esp_op_credito
                        , SUM(cred_esp_reducao)                          AS cred_esp_reducao
                        , SUM(credito_extraordinario)                    AS credito_extraordinario
                        , SUM(reducao_dotacoes)                          AS reducao_dotacoes
                        , SUM(dotacao_atualizada)                        AS dotacao_atualizada
                        , SUM(sup_rec_vinculado)                         AS sup_rec_vinculado
                        , SUM(red_rec_vinculado)                         AS red_rec_vinculado
                        , SUM(valor_empenhado)                           AS valor_empenhado
                        , SUM(valor_liquidado)                           AS valor_liquidado
                        , SUM(valor_pago)                                AS valor_pago
                        , COALESCE(SUM(valor_limitado_LRF),'0.00')       AS valor_limitado_LRF
                        , COALESCE(SUM(valor_rec_LRF),'0.00')            AS valor_rec_LRF
                        , COALESCE(SUM(valor_prev_LRF),'0.00')           AS valor_prev_LRF
                        , SUM(saldo_dotacao)                             AS saldo_dotacao
                        , SUM(cron_desenv_mensal1)                       AS cron_desenv_mensal1
                        , SUM(cron_desenv_mensal2)                       AS cron_desenv_mensal2
                FROM(
                    SELECT
                        (SELECT CASE WHEN valor = '' THEN '0000' ELSE valor END as valor
                                FROM administracao.configuracao_entidade
                                WHERE exercicio    = '".$this->getDado('exercicio')."'
                                AND cod_entidade IN (".$this->getDado('cod_entidade').")
                                AND cod_modulo   = 62
                                AND parametro    = 'tceal_configuracao_unidade_autonoma'
                        ) AS codigo_ua
                        , (SELECT PJ.cnpj
                                FROM orcamento.entidade
                                JOIN sw_cgm
                                    ON sw_cgm.numcgm = entidade.numcgm
                                JOIN sw_cgm_pessoa_juridica AS PJ
                                    ON sw_cgm.numcgm = PJ.numcgm
                                WHERE entidade.exercicio    = '".$this->getDado('exercicio')."'
                                AND entidade.cod_entidade IN (".$this->getDado('cod_entidade').")
                        ) AS cod_und_gestora
                        , LPAD(cod_orgao::VARCHAR, 2, '0')   AS cod_orgao
                        , LPAD(cod_unid_orcamentaria::VARCHAR, 4, '0') AS cod_unid_orcamentaria
                        , cod_funcao
                        , cod_subfuncao
                        , cod_programa
                        , cod_proj_atividade
                        , classificacao as cod_conta_despesa
                        , cod_rec_vinculado
                        , saldo_inicial as dotacao_inicial
                        , 0.00 as atualizacao_monetaria
                        , CASE WHEN tipo_suplementacao = 5 THEN
                                suplementacoes
                            ELSE
                                0.00
                          END AS credito_sup_superavit
                        , CASE WHEN tipo_suplementacao = 4 THEN
                                suplementacoes
                            ELSE
                                0.00
                          END AS credito_sup_excesso_arrecadacao
                        , CASE WHEN tipo_suplementacao = 2 THEN
                                suplementacoes
                            ELSE
                                0.00
                          END AS credito_sup_op_credito
                        , CASE WHEN tipo_suplementacao = 1 THEN
                                suplementacoes
                            ELSE
                                0.00
                          END AS credito_sup_reducao
                        , CASE WHEN tipo_suplementacao = 10 THEN
                                suplementacoes
                            ELSE
                                0.00
                          END AS cred_esp_superavit
                            , CASE WHEN tipo_suplementacao = 9 THEN
                                suplementacoes
                            ELSE
                                0.00
                          END AS cred_esp_excesso_arrecadacao
                        , CASE WHEN tipo_suplementacao = 7 THEN
                                suplementacoes
                            ELSE
                                0.00
                          END AS cred_esp_op_credito
                        , CASE WHEN tipo_suplementacao = 6 THEN
                                suplementacoes
                            ELSE
                                0.00
                          END AS cred_esp_reducao
                        , CASE WHEN tipo_suplementacao = 11 THEN
                                suplementacoes
                            ELSE
                                0.00
                          END AS credito_extraordinario
                        , reducoes as reducao_dotacoes
                        , ((saldo_inicial + suplementacoes) - reducoes) as dotacao_atualizada
                        , suplementacoes as sup_rec_vinculado
                        , reducoes as red_rec_vinculado
                        , empenhado_mes as valor_empenhado
                        , liquidado_mes as valor_liquidado
                        , pago_mes as valor_pago
                        , 0.00::numeric as valor_limitado_LRF
                        , 0.00::numeric as valor_rec_LRF
                        , 0.00::numeric as valor_prev_LRF
                        , ABS(saldo_inicial - (empenhado_ano + anulado_ano)) as saldo_dotacao
                        , CASE WHEN periodo = ".$this->getDado('primeiro_mes')." THEN
                                vl_previsto
                            ELSE
                                0.00
                          END as cron_desenv_mensal1
                        , CASE WHEN periodo = ".$this->getDado('segundo_mes')." THEN
                                vl_previsto
                            ELSE
                                0.00
                          END as cron_desenv_mensal2
                        FROM tceal.fn_balancete_depesa('".$this->getDado('exercicio')."'
                                                        ,''
                                                        ,'".$this->getDado('dtInicial')."'
                                                        ,'".$this->getDado('dtFinal')."'
                                                        ,'".$this->getDado('cod_entidade')."'
                                                        ,'','','','','','', 0, 0) 
                                as resultado(
                                classificacao          varchar,
                                cod_reduzido           varchar,
                                descricao              varchar,
                                cod_orgao              integer,
                                nom_orgao              varchar,
                                cod_unid_orcamentaria  integer,
                                nom_unidade            varchar,
                                cod_funcao             integer,
                                cod_subfuncao          integer,
                                cod_programa           integer,
                                cod_proj_atividade     integer,
                                cod_rec_vinculado      integer,
                                tipo_suplementacao     integer,
                                periodo                integer,
                                vl_previsto            numeric,
                                saldo_inicial          numeric,
                                suplementacoes         numeric,
                                reducoes               numeric,
                                empenhado_mes          numeric,
                                empenhado_ano          numeric,
                                anulado_mes            numeric,
                                anulado_ano            numeric,
                                pago_mes               numeric,
                                pago_ano               numeric,
                                liquidado_mes          numeric,
                                liquidado_ano          numeric,
                                tipo_conta             varchar,
                                nivel                  integer
                                )
            ) as arquivo
            GROUP BY codigo_ua
                    , cod_und_gestora
                    , cod_orgao
                    , cod_unid_orcamentaria
                    , cod_funcao
                    , cod_subfuncao
                    , cod_programa
                    , cod_proj_atividade
                    , cod_conta_despesa
                    , cod_rec_vinculado

            ORDER BY cod_conta_despesa , cod_funcao, cod_programa, cod_proj_atividade , cod_rec_vinculado

            ";
        
        return $stSql;
    }
}
?>
