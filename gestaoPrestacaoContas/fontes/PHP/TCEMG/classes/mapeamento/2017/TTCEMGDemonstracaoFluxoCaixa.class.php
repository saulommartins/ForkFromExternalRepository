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

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once (CLA_PERSISTENTE);

class TTCEMGDemonstracaoFluxoCaixa extends Persistente {
  public function TTCEMGDemonstracaoFluxoCaixa() {
    parent::Persistente();
  }

  public function recuperaDadosDFC10(&$rsRecordSet) {
    $obErro = new Erro;
    $obConexao = new Conexao;
    $rsRecordSet = new RecordSet;
    $boTransacao = new Transacao();

    $sql = $this->montaRecuperaDadosDFC10();
    $this->setDebug($sql);
    $obErro = $obConexao->executaSQL($rsRecordSet, $sql, $boTransacao);

    return $obErro;
  }

  private function montaRecuperaDadosDFC10() {
  	$sql = "SELECT 10 as tipo_registro,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlReceitaDerivadaOriginaria'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_receita_derivada_originaria,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlTransCorrenteRecebida'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_transf_corrente_recebida,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlOutrosIngressosOperacionais'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_outro_ingresso_operacional,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlTotalIngressosAtivOperacionais'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_tot_ingresso_atividade_operacional ";
  	$sql.= $this->montaFromValoresContabeis();
  	$sql.= "WHERE configuracao_dcasp_arquivo.nome_arquivo_pertencente = 'DFC'
			            AND configuracao_dcasp_registros.exercicio = '" . $this->getDado('exercicio') . "'
                  AND configuracao_dcasp_registros.tipo_registro = 10
            GROUP BY configuracao_dcasp_arquivo.nome_arquivo_pertencente";
  	return $sql;
  }

  public function recuperaDadosDFC20(&$rsRecordSet) {
    $obErro = new Erro;
    $obConexao = new Conexao;
    $rsRecordSet = new RecordSet;
    $boTransacao = new Transacao();

    $sql = $this->montaRecuperaDadosDFC20();
    $this->setDebug($sql);
    $obErro = $obConexao->executaSQL($rsRecordSet, $sql, $boTransacao);

    return $obErro;
  }

  private function montaRecuperaDadosDFC20() {
    $sql = "SELECT 20 as tipo_registro,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlDesembolsoPessoalDespesas'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_desembolso_pessoal_demais_despesa,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlDesembolsoJurosEncargDivida'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_desembolso_juro_encargo_divida,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlDesembolsoTransfConcedidas'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_desembolso_transferencia_concedida,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlOutrosDesembolsos'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_outro_desembolso_operacional,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlTotalDesembolsosAtivOperacionais'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_tot_desembolso_atividade_operacional ";
  	$sql.= $this->montaFromValoresContabeis();
  	$sql.= "WHERE configuracao_dcasp_arquivo.nome_arquivo_pertencente = 'DFC'
			            AND configuracao_dcasp_registros.exercicio = '" . $this->getDado('exercicio') . "'
                  AND configuracao_dcasp_registros.tipo_registro = 20
            GROUP BY configuracao_dcasp_arquivo.nome_arquivo_pertencente";
  	return $sql;
  }

  public function recuperaDadosDFC30(&$rsRecordSet) {
    $obErro = new Erro;
    $obConexao = new Conexao;
    $rsRecordSet = new RecordSet;
    $boTransacao = new Transacao();

    $sql = $this->montaRecuperaDadosDFC30();
    $this->setDebug($sql);
    $obErro = $obConexao->executaSQL($rsRecordSet, $sql, $boTransacao);

    return $obErro;
  }

  private function montaRecuperaDadosDFC30() {
    $sql = "SELECT 30 as tipo_registro,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlFluxoCaixaLiquidoOperacional'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_fluxo_caixa_liq_atividade_operacional ";
  	$sql.= $this->montaFromValoresContabeis();
  	$sql.= "WHERE configuracao_dcasp_arquivo.nome_arquivo_pertencente = 'DFC'
			            AND configuracao_dcasp_registros.exercicio = '" . $this->getDado('exercicio') . "'
                  AND configuracao_dcasp_registros.tipo_registro = 30
            GROUP BY configuracao_dcasp_arquivo.nome_arquivo_pertencente";
  	return $sql;
  }

  public function recuperaDadosDFC40(&$rsRecordSet) {
    $obErro = new Erro;
    $obConexao = new Conexao;
    $rsRecordSet = new RecordSet;
    $boTransacao = new Transacao();

    $sql = $this->montaRecuperaDadosDFC40();
    $this->setDebug($sql);
    $obErro = $obConexao->executaSQL($rsRecordSet, $sql, $boTransacao);

    return $obErro;
  }

  private function montaRecuperaDadosDFC40() {
    $sql = "SELECT 40 as tipo_registro,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlAlienacaoBens'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_alienacao_bens,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlAmortizacaoEmprestimoConcedido'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_amortizacao_empres_financ_concedido,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlOutrosIngressos'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_outro_ingresso_investimento,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlTotalIngressosAtividaInvestimento'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_total_ingresso_atividade_investimento ";
  	$sql.= $this->montaFromValoresContabeis();
  	$sql.= "WHERE configuracao_dcasp_arquivo.nome_arquivo_pertencente = 'DFC'
			            AND configuracao_dcasp_registros.exercicio = '" . $this->getDado('exercicio') . "'
                  AND configuracao_dcasp_registros.tipo_registro = 40
            GROUP BY configuracao_dcasp_arquivo.nome_arquivo_pertencente";
  	return $sql;
  }

  public function recuperaDadosDFC50(&$rsRecordSet) {
    $obErro = new Erro;
    $obConexao = new Conexao;
    $rsRecordSet = new RecordSet;
    $boTransacao = new Transacao();

    $sql = $this->montaRecuperaDadosDFC50();
    $this->setDebug($sql);
    $obErro = $obConexao->executaSQL($rsRecordSet, $sql, $boTransacao);

    return $obErro;
  }

  private function montaRecuperaDadosDFC50() {
    $sql = "SELECT 50 as tipo_registro,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlAquisicaoAtivoNaoCirculante'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_aquisicao_ativo_circulante,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlConcessaoEmpresFinanciamento'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_concessao_emprestimo_financiamento,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlOutrosDesembolsos'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_outro_desembolso_investimento,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlTotalDesembolsoAtividaInvestimento'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_tot_desembolso_atividade_investimento ";
  	$sql.= $this->montaFromValoresContabeis();
  	$sql.= "WHERE configuracao_dcasp_arquivo.nome_arquivo_pertencente = 'DFC'
			            AND configuracao_dcasp_registros.exercicio = '" . $this->getDado('exercicio') . "'
                  AND configuracao_dcasp_registros.tipo_registro = 50
            GROUP BY configuracao_dcasp_arquivo.nome_arquivo_pertencente";
  	return $sql;
  }

  public function recuperaDadosDFC60(&$rsRecordSet) {
    $obErro = new Erro;
    $obConexao = new Conexao;
    $rsRecordSet = new RecordSet;
    $boTransacao = new Transacao();

    $sql = $this->montaRecuperaDadosDFC60();
    $this->setDebug($sql);
    $obErro = $obConexao->executaSQL($rsRecordSet, $sql, $boTransacao);

    return $obErro;
  }

  private function montaRecuperaDadosDFC60() {
    $sql = "SELECT 60 as tipo_registro,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlFluxoCaixaLiquidoInvestimento'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_fluxo_caixa_liq_atividade_investimento ";
  	$sql.= $this->montaFromValoresContabeis();
  	$sql.= "WHERE configuracao_dcasp_arquivo.nome_arquivo_pertencente = 'DFC'
			            AND configuracao_dcasp_registros.exercicio = '" . $this->getDado('exercicio') . "'
                  AND configuracao_dcasp_registros.tipo_registro = 60
            GROUP BY configuracao_dcasp_arquivo.nome_arquivo_pertencente";
  	return $sql;
  }

  public function recuperaDadosDFC70(&$rsRecordSet) {
    $obErro = new Erro;
    $obConexao = new Conexao;
    $rsRecordSet = new RecordSet;
    $boTransacao = new Transacao();

    $sql = $this->montaRecuperaDadosDFC70();
    $this->setDebug($sql);
    $obErro = $obConexao->executaSQL($rsRecordSet, $sql, $boTransacao);

    return $obErro;
  }

  private function montaRecuperaDadosDFC70() {
    $sql = "SELECT 70 as tipo_registro,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlOperacoesCredito'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_operacao_credito,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlIntegralizacaoDependentes'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_integra_capital_social_empresa_dependente,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlTransCapitalRecebida'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_transferencia_capital_recebida,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlOutrosIngressosFinanciamento'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_outro_ingresso_financiamento,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlTotalIngressoAtividaFinanciamento'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_tot_ingresso_atividade_financiamento ";
  	$sql.= $this->montaFromValoresContabeis();
  	$sql.= "WHERE configuracao_dcasp_arquivo.nome_arquivo_pertencente = 'DFC'
			            AND configuracao_dcasp_registros.exercicio = '" . $this->getDado('exercicio') . "'
                  AND configuracao_dcasp_registros.tipo_registro = 70
            GROUP BY configuracao_dcasp_arquivo.nome_arquivo_pertencente";
  	return $sql;
  }

  public function recuperaDadosDFC80(&$rsRecordSet) {
    $obErro = new Erro;
    $obConexao = new Conexao;
    $rsRecordSet = new RecordSet;
    $boTransacao = new Transacao();

    $sql = $this->montaRecuperaDadosDFC80();
    $this->setDebug($sql);
    $obErro = $obConexao->executaSQL($rsRecordSet, $sql, $boTransacao);

    return $obErro;
  }

  private function montaRecuperaDadosDFC80() {
    $sql = "SELECT 80 as tipo_registro,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlAmortizacaoRefinanciamento'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_amortizacao_refinanciamento_divida,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlOutrosDesembolsosFinanciamento'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_outro_desembolso_financiamento,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlTotalDesembolsoAtividaFinanciamento'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_tot_desembolso_atividade_financiamento ";
  	$sql.= $this->montaFromValoresContabeis();
  	$sql.= "WHERE configuracao_dcasp_arquivo.nome_arquivo_pertencente = 'DFC'
			            AND configuracao_dcasp_registros.exercicio = '" . $this->getDado('exercicio') . "'
                  AND configuracao_dcasp_registros.tipo_registro = 80
            GROUP BY configuracao_dcasp_arquivo.nome_arquivo_pertencente";
  	return $sql;
  }

  public function recuperaDadosDFC90(&$rsRecordSet) {
    $obErro = new Erro;
    $obConexao = new Conexao;
    $rsRecordSet = new RecordSet;
    $boTransacao = new Transacao();

    $sql = $this->montaRecuperaDadosDFC90();
    $this->setDebug($sql);
    $obErro = $obConexao->executaSQL($rsRecordSet, $sql, $boTransacao);

    return $obErro;
  }

  private function montaRecuperaDadosDFC90() {
    $sql = "SELECT 90 as tipo_registro,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlFluxoCaixaFinanciamento'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_fluxo_caixa_liq_atividade_financiamento ";
  	$sql.= $this->montaFromValoresContabeis();
  	$sql.= "WHERE configuracao_dcasp_arquivo.nome_arquivo_pertencente = 'DFC'
			            AND configuracao_dcasp_registros.exercicio = '" . $this->getDado('exercicio') . "'
                  AND configuracao_dcasp_registros.tipo_registro = 90
            GROUP BY configuracao_dcasp_arquivo.nome_arquivo_pertencente";
  	return $sql;
  }

  public function recuperaDadosDFC100(&$rsRecordSet) {
    $obErro = new Erro;
    $obConexao = new Conexao;
    $rsRecordSet = new RecordSet;
    $boTransacao = new Transacao();

    $sql = $this->montaRecuperaDadosDFC100();
    $this->setDebug($sql);
    $obErro = $obConexao->executaSQL($rsRecordSet, $sql, $boTransacao);

    return $obErro;
  }

  private function montaRecuperaDadosDFC100() {
    $sql = "SELECT 100 as tipo_registro,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlGeracaoLiquidaEquivalenteCaixa'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_geracao_liq_caixa_equivalente_caixa ";
  	$sql.= $this->montaFromValoresContabeis();
  	$sql.= "WHERE configuracao_dcasp_arquivo.nome_arquivo_pertencente = 'DFC'
			            AND configuracao_dcasp_registros.exercicio = '" . $this->getDado('exercicio') . "'
                  AND configuracao_dcasp_registros.tipo_registro = 100
            GROUP BY configuracao_dcasp_arquivo.nome_arquivo_pertencente";
  	return $sql;
  }

  public function recuperaDadosDFC110(&$rsRecordSet) {
    $obErro = new Erro;
    $obConexao = new Conexao;
    $rsRecordSet = new RecordSet;
    $boTransacao = new Transacao();

    $sql = $this->montaRecuperaDadosDFC110();
    $this->setDebug($sql);
    $obErro = $obConexao->executaSQL($rsRecordSet, $sql, $boTransacao);

    return $obErro;
  }

  private function montaRecuperaDadosDFC110() {
    $sql = "SELECT 110 as tipo_registro,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlCaixaEquivalenteCaixaInicial'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_caixa_equivalente_caixa_inicial,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlCaixaEquivalenteCaixaFinal'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_caixa_equivalente_caixa_final ";
  	$sql.= $this->montaFromValoresContabeis();
  	$sql.= "WHERE configuracao_dcasp_arquivo.nome_arquivo_pertencente = 'DFC'
			            AND configuracao_dcasp_registros.exercicio = '" . $this->getDado('exercicio') . "'
                  AND configuracao_dcasp_registros.tipo_registro = 110
            GROUP BY configuracao_dcasp_arquivo.nome_arquivo_pertencente";
  	return $sql;
  }

  private function montaFromValoresContabeis() {
    return "FROM tcemg.configuracao_dcasp_registros
				         JOIN tcemg.configuracao_dcasp_arquivo using (seq_arquivo)
		             LEFT JOIN (SELECT CASE
				                             WHEN valor_lancamento.tipo_valor = 'C'
		                                   THEN replace(plano_conta_credito.cod_estrutural, '.', '')
	                                   ELSE replace(plano_conta_debito.cod_estrutural, '.', '')
                                   END as conta,
		                               lancamento.exercicio,
                                   lancamento.cod_entidade,
	                                 valor_lancamento.vl_lancamento as valor
                            FROM contabilidade.lancamento
				                         JOIN contabilidade.valor_lancamento ON valor_lancamento.exercicio = lancamento.exercicio
                    				          AND valor_lancamento.cod_entidade = lancamento.cod_entidade
                    				          AND valor_lancamento.tipo = lancamento.tipo
                    				          AND valor_lancamento.cod_lote = lancamento.cod_lote
                    				          AND valor_lancamento.sequencia = lancamento.sequencia
                                 LEFT JOIN contabilidade.conta_credito ON conta_credito.cod_lote = valor_lancamento.cod_lote
                    				          AND conta_credito.tipo = valor_lancamento.tipo
                    				          AND conta_credito.sequencia = valor_lancamento.sequencia
                    				          AND conta_credito.exercicio = valor_lancamento.exercicio
                    				          AND conta_credito.tipo_valor = valor_lancamento.tipo_valor
                    				          AND conta_credito.cod_entidade = valor_lancamento.cod_entidade
                                 LEFT JOIN contabilidade.conta_debito ON conta_debito.cod_lote = valor_lancamento.cod_lote
                    				          AND conta_debito.tipo = valor_lancamento.tipo
                    				          AND conta_debito.sequencia = valor_lancamento.sequencia
                    				          AND conta_debito.exercicio = valor_lancamento.exercicio
                    				          AND conta_debito.tipo_valor = valor_lancamento.tipo_valor
                    				          AND conta_debito.cod_entidade = valor_lancamento.cod_entidade
		                             LEFT JOIN contabilidade.plano_analitica AS plano_analitica_credito ON plano_analitica_credito.exercicio = conta_credito.exercicio
				                              AND plano_analitica_credito.cod_plano = conta_credito.cod_plano
                                 LEFT JOIN contabilidade.plano_conta AS plano_conta_credito ON plano_conta_credito.exercicio = plano_analitica_credito.exercicio
				                              AND plano_conta_credito.cod_conta = plano_analitica_credito.cod_conta
                                 LEFT JOIN contabilidade.plano_analitica AS plano_analitica_debito ON plano_analitica_debito.exercicio = conta_debito.exercicio
				                              AND plano_analitica_debito.cod_plano = conta_debito.cod_plano
                                 LEFT JOIN contabilidade.plano_conta AS plano_conta_debito ON plano_conta_debito.exercicio = plano_analitica_debito.exercicio
				                              AND plano_conta_debito.cod_conta = plano_analitica_debito.cod_conta
                           ) AS contabil ON configuracao_dcasp_registros.exercicio = contabil.exercicio
                      AND replace(configuracao_dcasp_registros.conta_contabil, '.', '') = contabil.conta
                      AND contabil.cod_entidade IN (" . $this->getDado('entidade') . ") ";
  }

  public function __destruct() {}

}
