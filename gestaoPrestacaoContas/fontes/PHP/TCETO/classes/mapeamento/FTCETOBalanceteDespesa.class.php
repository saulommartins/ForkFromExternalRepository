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
    * Extensão da Classe de Mapeamento TTCETOBalanceteDespesa
    *
    * Data de Criação: 07/11/2014
    *
    * @author: Franver Sarmento de Moraes
    *
    * $Id: FTCETOBalanceteDespesa.class.php 60998 2014-11-27 19:07:36Z franver $
    *
    * @ignore
    *
*/
class FTCETOBalanceteDespesa extends Persistente
{
    /**
    * Método Construtor
    * @access Public
    */
    public function FTCETOBalanceteDespesa()
    {
        parent::Persistente();
    }
    /**
     * Método para trazer todos os registros de Projeto Atividade, para o TCETO
     * @access Public
     * @param  Object  $rsRecordSet Objeto RecordSet
     * @param  String  $stCondicao  String de condição do SQL (WHERE)
     * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
     * @param  Boolean $boTransacao
     * @return Object  Objeto Erro
    */
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
        $stSql = "
              SELECT
            DISTINCT cod_und_gestora AS id_unidade_gestora
                   , LPAD(cod_orgao::VARCHAR, 2, '0') AS id_orgao
                   , LPAD(cod_unid_orcamentaria::VARCHAR, 4, '0') AS id_unidade_orcamentaria
                   , LPAD(cod_funcao::VARCHAR, 2, '0') AS id_funcao
                   , LPAD(cod_subfuncao::VARCHAR, 3, '0') AS id_subfuncao
                   , LPAD(cod_programa::VARCHAR, 4, '0') AS id_programa
                   , LPAD(cod_proj_atividade::VARCHAR, 4, '0') AS id_projeto_atividade
                   , REPLACE(cod_conta_despesa::VARCHAR, '.', '') AS id_rubrica_despesa
                   , cod_rec_vinculado AS id_recurso_vinculado
                   , nivel
                   , CASE WHEN nivel > 5 THEN
                        0.00 
                      ELSE 
                        SUM(dotacao_inicial)       
                      END AS dotacao_inicial
                   , SUM(atualizacao_monetaria)            as atualizacao_monetaria
                   , SUM(credito_sup_reducao)              as credito_suplementar_reducao_dotacao
                   , SUM(credito_sup_superavit)            as credito_suplementar_superavit_financeiro
                   , SUM(credito_sup_excesso_arrecadacao)  as credito_suplementar_excesso_arrecadacao
                   , SUM(credito_sup_op_credito)           as credito_suplementar_operacao_credito
                   , SUM(cred_esp_reducao)                 as credito_especial_reducao_dotacao
                   , SUM(cred_esp_superavit)               as credito_especial_superavit_financeiro
                   , SUM(cred_esp_excesso_arrecadacao)     as credito_especial_excesso_arrecadacao
                   , SUM(cred_esp_op_credito)              as credito_especial_operacao_credito
                   , SUM(credito_extraordinario)           as credito_extraordinario
                   , SUM(reducao_dotacoes)                 as reducao_dotacao_orcamentaria
                   , SUM(sup_rec_vinculado)                as suplemento_recurso_vinculado
                   , SUM(red_rec_vinculado)                as reducao_recurso_vinculado
                   , SUM(cron_desenv_mensal1)              as cronograma_desenvolvimento_mensal1
                   , SUM(cron_desenv_mensal2)              as cronograma_desenvolvimento_mensal2
                   , SUM(valor_empenhado)                  as valor_empenhado
                   , SUM(valor_liquidado)                  as valor_liquidado
                   , SUM(valor_pago)                       as valor_pago
                   , COALESCE(SUM(valor_limitado_LRF),'0.00') as valor_limitado_LRF
                   , COALESCE(SUM(valor_rec_LRF),'0.00')   as valor_recomposicao_dotacao_LRF
                   , COALESCE(SUM(valor_prev_LRF),'0.00')  as valor_previsto_realizado_termino_exercicio_LRF
                   , 0.00 AS aumento_movimento_orcamento_qdd
                   , 0.00 AS reducao_movimento_orcamento_qdd
                FROM (SELECT (SELECT PJ.cnpj
                                FROM orcamento.entidade
                                JOIN sw_cgm
                                  ON sw_cgm.numcgm = entidade.numcgm
                                JOIN sw_cgm_pessoa_juridica AS PJ
                                  ON sw_cgm.numcgm = PJ.numcgm
                               WHERE entidade.exercicio = '".$this->getDado('exercicio')."'
                                 AND entidade.cod_entidade IN (".$this->getDado('cod_entidade').")
                             ) AS cod_und_gestora
                           , LPAD(cod_orgao::VARCHAR, 2, '0') AS cod_orgao
                           , LPAD(cod_unid_orcamentaria::VARCHAR, 4, '0') AS cod_unid_orcamentaria
                           , cod_funcao
                           , cod_subfuncao
                           , cod_programa
                           , cod_proj_atividade
                           , classificacao as cod_conta_despesa
                           , cod_rec_vinculado
                           , saldo_inicial as dotacao_inicial
                           , 0.00 as atualizacao_monetaria
                           , CASE WHEN tipo_suplementacao = 5
                                  THEN suplementacoes
                                  ELSE 0.00
                              END AS credito_sup_superavit
                           , CASE WHEN tipo_suplementacao = 4
                                  THEN suplementacoes
                                  ELSE 0.00
                              END AS credito_sup_excesso_arrecadacao
                           , CASE WHEN tipo_suplementacao = 2
                                  THEN suplementacoes
                                  ELSE 0.00
                              END AS credito_sup_op_credito
                           , CASE WHEN tipo_suplementacao = 1
                                  THEN suplementacoes
                                  ELSE 0.00
                              END AS credito_sup_reducao
                           , CASE WHEN tipo_suplementacao = 10
                                  THEN suplementacoes
                                  ELSE 0.00
                              END AS cred_esp_superavit
                           , CASE WHEN tipo_suplementacao = 9
                                  THEN suplementacoes
                                  ELSE 0.00
                              END AS cred_esp_excesso_arrecadacao
                           , CASE WHEN tipo_suplementacao = 7
                                  THEN suplementacoes
                                  ELSE 0.00
                              END AS cred_esp_op_credito
                           , CASE WHEN tipo_suplementacao = 6
                                  THEN suplementacoes
                                  ELSE 0.00
                              END AS cred_esp_reducao
                           , CASE WHEN tipo_suplementacao = 11
                                  THEN suplementacoes
                                  ELSE 0.00
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
                           , CASE WHEN periodo = ".$this->getDado('primeiro_mes')."
                                  THEN vl_previsto
                                  ELSE 0.00
                              END AS cron_desenv_mensal1
                           , CASE WHEN periodo = ".$this->getDado('segundo_mes')."
                                  THEN vl_previsto
                                  ELSE 0.00
                              END AS cron_desenv_mensal2
                           , nivel
                        FROM tceto.fn_balancete_depesa('".$this->getDado('exercicio')."','".$this->getDado('dtInicial')."','".$this->getDado('dtFinal')."','".$this->getDado('cod_entidade')."') 
                          AS resultado(classificacao         VARCHAR
                                     , cod_reduzido          VARCHAR
                                     , descricao             VARCHAR
                                     , cod_orgao             INTEGER
                                     , nom_orgao             VARCHAR
                                     , cod_unid_orcamentaria INTEGER
                                     , nom_unidade           VARCHAR
                                     , cod_funcao            INTEGER
                                     , cod_subfuncao         INTEGER
                                     , cod_programa          INTEGER
                                     , cod_proj_atividade    INTEGER
                                     , cod_rec_vinculado     INTEGER
                                     , tipo_suplementacao    INTEGER
                                     , periodo               INTEGER
                                     , vl_previsto           NUMERIC
                                     , saldo_inicial         NUMERIC
                                     , suplementacoes        NUMERIC
                                     , reducoes              NUMERIC
                                     , empenhado_mes         NUMERIC
                                     , empenhado_ano         NUMERIC
                                     , anulado_mes           NUMERIC
                                     , anulado_ano           NUMERIC
                                     , pago_mes              NUMERIC
                                     , pago_ano              NUMERIC
                                     , liquidado_mes         NUMERIC
                                     , liquidado_ano         NUMERIC
                                     , tipo_conta            VARCHAR
                                     , nivel                 INTEGER
                                     )
                     ) as arquivo
            GROUP BY cod_und_gestora
                   , cod_orgao
                   , cod_unid_orcamentaria
                   , cod_funcao
                   , cod_subfuncao
                   , cod_programa
                   , cod_proj_atividade
                   , cod_conta_despesa
                   , cod_rec_vinculado
                   , nivel
            ORDER BY id_rubrica_despesa
        ";
        return $stSql;
    }
}
?>