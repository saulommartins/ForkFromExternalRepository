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

class TTCEMGBalancoPatrimonial extends Persistente {
  public function TTCEMGBalancoPatrimonial() {
    parent::Persistente();
  }

  public function recuperaDadosBP10(&$rsRecordSet) {
    $obErro = new Erro;
    $obConexao = new Conexao;
    $rsRecordSet = new RecordSet;
    $boTransacao = new Transacao();

    $sql = $this->montaRecuperaDadosBP10();
    $this->setDebug($sql);
    $obErro = $obConexao->executaSQL($rsRecordSet, $sql, $boTransacao);

    return $obErro;
  }

  private function montaRecuperaDadosBP10() {
  	$sql = "SELECT 10 as tipo_registro,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlAtivoCircuCaixaEquiCaixa'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_ativo_circ_caixa_equival_caixa,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlAtivoCircuCrediCurtoPrazo'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_ativo_circ_cred_curto_prazo,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlAtivoCircuInvestAplicacaoCurtoPrazo'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_ativo_circ_invest_aplic_temp_curto_prazo,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlAtivoCircuEstoques'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_ativo_circ_estoque,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlAtivoCircuVPDAntecipada'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_ativo_circ_vpd_paga_antecipado,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlAtivoNaoCircuCrediLongoPrazo'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_ativo_nao_circ_cred_longo_prazo,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlAtivoNaoCircuInvesTempoLongoPrazo'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_ativo_nao_circ_invest_temp_longo_prazo,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlAtivoNaoCircuEstoques'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_ativo_nao_circ_estoque,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlAtivoNaoCircuVPDAntecipada'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_ativo_nao_circ_vpd_pago_antecipado,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlAtivoNaoCircuInvestimentos'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_ativo_nao_circ_investimento,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlAtivoNaoCircuImobilizado'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_ativo_nao_circ_imobilizado,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlAtivoNaoCircuIntagivel'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_ativo_nao_circ_intangivel,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlTotalAtivo'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_total_ativo ";
  	$sql.= $this->montaFromValoresContabeis();
  	$sql.= "WHERE configuracao_dcasp_arquivo.nome_arquivo_pertencente = 'BP'
			            AND configuracao_dcasp_registros.exercicio = '" . $this->getDado('exercicio') . "'
                  AND configuracao_dcasp_registros.tipo_registro = 10
            GROUP BY configuracao_dcasp_arquivo.nome_arquivo_pertencente";
  	return $sql;
  }

  public function recuperaDadosBP20(&$rsRecordSet) {
    $obErro = new Erro;
    $obConexao = new Conexao;
    $rsRecordSet = new RecordSet;
    $boTransacao = new Transacao();

    $sql = $this->montaRecuperaDadosBP20();
    $this->setDebug($sql);
    $obErro = $obConexao->executaSQL($rsRecordSet, $sql, $boTransacao);

    return $obErro;
  }

  private function montaRecuperaDadosBP20() {
    $sql = "SELECT 20 as tipo_registro,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlPassivoCirculaTrabPreviCurtoPrazo'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_pass_circ_obrig_trab_previdenc_assist_pagar_curto_prazo,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlPassivoCirculaEmpreFinanCurtoPrazo'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_pass_circ_emprest_financ_curto_prazo,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlPassivoCirculaFornecedoresCurtoPrazo'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_pass_circ_fornec_contas_pagar_curto_prazo,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlPassivoCirculaObrigaFiscaisCurtoPrazo'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_pass_circ_obrig_fiscais_curto_prazo,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlPassivoCirculaObrigacoesOutrosEntes'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_pass_circ_obrig_repart_outros_entes,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlPassivoCirculaProvisoesCurtoPrazo'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_pass_circ_provisoes_curto_prazo,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlPassivoCirculaDemaisObrigaCurtoPrazo'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_pass_circ_dms_obrigacoes_curto_prazo,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlPassivoNaoCirculaTrabPreviLongoPrazo'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_pass_nao_circ_obrig_trab_prev_assist_pagar_longro_prazo,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlPassivoNaoCirculaEmpreFinanLongoPrazo'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_pass_nao_circ_emp_financ_longo_prazo,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlPassivoNaoCirculaFornecedoresLongoPrazo'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_pass_nao_circ_fornec_contas_pagar_longo_prazo,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlPassivoNaoCirculaObrigaFiscaisLongoPrazo'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_pass_nao_circ_obrig_fisc_longo_prazo,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlPassivoNaoCirculaProvisoesLongoPrazo'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_pass_nao_circ_provisoes_longo_prazo,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlPassivoNaoCirculaDemaisObrigaLongoPrazo'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_pass_nao_circ_dms_obrig_longo_prazo,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlPassivoNaoCirculaResulDiferido'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_pass_nao_circ_resultado_deferido,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlPatriLiquidoCapitalSocial'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_patri_liq_patr_social_capital_social,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlPatriLiquidoAdianFuturoCapital'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_patri_liq_adiant_futuro_aumento_capital,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlPatriLiquidoReservaCapital'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_patri_liq_reservas_capital,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlPatriLiquidoAjustAvaliacao'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_patri_liq_ajuste_aval_patrimonial,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlPatriLiquidoReservaLucros'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_patri_liq_reservas_lucros,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlPatriLiquidoDemaisReservas'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_patri_liq_demais_reservas,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlPatriLiquidoResultExercicio'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_patri_liq_resultado_exercicio,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlPatriLiquidoResultAcumExerAnteriores'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_patri_liq_resultado_acumulado_exerc_anterior,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlPatriLiquidoAcoesCotas'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_patri_liq_acoes_cotas_tesouraria,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlTotalPassivo'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_total_passivo_patri_liquido ";
  	$sql.= $this->montaFromValoresContabeis();
  	$sql.= "WHERE configuracao_dcasp_arquivo.nome_arquivo_pertencente = 'BP'
			            AND configuracao_dcasp_registros.exercicio = '" . $this->getDado('exercicio') . "'
                  AND configuracao_dcasp_registros.tipo_registro = 20
            GROUP BY configuracao_dcasp_arquivo.nome_arquivo_pertencente";
  	return $sql;
  }

  public function recuperaDadosBP30(&$rsRecordSet) {
    $obErro = new Erro;
    $obConexao = new Conexao;
    $rsRecordSet = new RecordSet;
    $boTransacao = new Transacao();

    $sql = $this->montaRecuperaDadosBP30();
    $this->setDebug($sql);
    $obErro = $obConexao->executaSQL($rsRecordSet, $sql, $boTransacao);

    return $obErro;
  }

  private function montaRecuperaDadosBP30() {
  	$sql = "SELECT 30 as tipo_registro,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlAtivoFinanceiro'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_ativo_financeiro,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlAtivoPermanente'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_ativo_permanente,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlTotalAtivoFinanceiroPermanente'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_tot_ativo_financeiro_permanente ";
  	$sql.= $this->montaFromValoresContabeis();
  	$sql.= "WHERE configuracao_dcasp_arquivo.nome_arquivo_pertencente = 'BP'
			            AND configuracao_dcasp_registros.exercicio = '" . $this->getDado('exercicio') . "'
                  AND configuracao_dcasp_registros.tipo_registro = 30
            GROUP BY configuracao_dcasp_arquivo.nome_arquivo_pertencente";
  	return $sql;
  }

  public function recuperaDadosBP40(&$rsRecordSet) {
    $obErro = new Erro;
    $obConexao = new Conexao;
    $rsRecordSet = new RecordSet;
    $boTransacao = new Transacao();

    $sql = $this->montaRecuperaDadosBP40();
    $this->setDebug($sql);
    $obErro = $obConexao->executaSQL($rsRecordSet, $sql, $boTransacao);

    return $obErro;
  }

  private function montaRecuperaDadosBP40() {
    $sql = "SELECT 40 as tipo_registro,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlPassivoFinanceiro'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_passivo_financeiro,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlPassivoPermanente'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_passivo_permanente,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlTotalPassivoFinanceiroPermanente'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_tot_passivo_financeiro_permanente ";
  	$sql.= $this->montaFromValoresContabeis();
  	$sql.= "WHERE configuracao_dcasp_arquivo.nome_arquivo_pertencente = 'BP'
			            AND configuracao_dcasp_registros.exercicio = '" . $this->getDado('exercicio') . "'
                  AND configuracao_dcasp_registros.tipo_registro = 40
            GROUP BY configuracao_dcasp_arquivo.nome_arquivo_pertencente";
  	return $sql;
  }

  public function recuperaDadosBP50(&$rsRecordSet) {
    $obErro = new Erro;
    $obConexao = new Conexao;
    $rsRecordSet = new RecordSet;
    $boTransacao = new Transacao();

    $sql = $this->montaRecuperaDadosBP50();
    $this->setDebug($sql);
    $obErro = $obConexao->executaSQL($rsRecordSet, $sql, $boTransacao);

    return $obErro;
  }

  private function montaRecuperaDadosBP50() {
  	$sql = "SELECT 50 as tipo_registro,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlTotalAtivoFinanceiroPermanente'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END)
                   -
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlTotalPassivoFinanceiroPermanente'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_saldo_patrim_quadro_ativ_passivo_financeiro_permanente ";
  	$sql.= $this->montaFromValoresContabeis();
  	$sql.= "WHERE configuracao_dcasp_arquivo.nome_arquivo_pertencente = 'BP'
			            AND configuracao_dcasp_registros.exercicio = '" . $this->getDado('exercicio') . "'
                  AND configuracao_dcasp_registros.tipo_registro = 50
            GROUP BY configuracao_dcasp_arquivo.nome_arquivo_pertencente";
  	return $sql;
  }

  public function recuperaDadosBP60(&$rsRecordSet) {
    $obErro = new Erro;
    $obConexao = new Conexao;
    $rsRecordSet = new RecordSet;
    $boTransacao = new Transacao();

    $sql = $this->montaRecuperaDadosBP60();
    $this->setDebug($sql);
    $obErro = $obConexao->executaSQL($rsRecordSet, $sql, $boTransacao);

    return $obErro;
  }

  private function montaRecuperaDadosBP60() {
    $sql = "SELECT 60 as tipo_registro,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlAtosPotenAtivosGaranContragaRecebida'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_ato_poten_ativ_garan_contragaran_recebida,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlAtosPotenAtivosDirConveOutrosInstrumentos'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_ato_poten_ativ_dir_conven_outros_instru_congeneres,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlAtosPotenAtivosDireitosContratuais'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_ato_poten_ativ_direitos_contratuais,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlAtosPotenAtivosOutrosAtos'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_ato_poten_ativ_outros_atos_potenc_ativo,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlAtosPotenPassivoGaranContragaConcedida'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_ato_poten_pass_garan_contragaran_concedida,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlAtosPotenPassivoObrigConveOutrosInstrumentos'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_ato_poten_pass_obrig_conven_outros_instru_congeneres,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlAtosPotenPassivoObrigacoesContratuais'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_ato_poten_pass_obrigacoes_contratuais,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlAtosPotenPassivoOutrosAtos'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_ato_poten_pass_outros_atos_potenc_passivo ";
  	$sql.= $this->montaFromValoresContabeis();
  	$sql.= "WHERE configuracao_dcasp_arquivo.nome_arquivo_pertencente = 'BP'
			            AND configuracao_dcasp_registros.exercicio = '" . $this->getDado('exercicio') . "'
                  AND configuracao_dcasp_registros.tipo_registro = 60
            GROUP BY configuracao_dcasp_arquivo.nome_arquivo_pertencente";
  	return $sql;
  }

  public function recuperaDadosBP70(&$rsRecordSet) {
    $obErro = new Erro;
    $obConexao = new Conexao;
    $rsRecordSet = new RecordSet;
    $boTransacao = new Transacao();

    $sql = $this->montaRecuperaDadosBP70();
    $this->setDebug($sql);
    $obErro = $obConexao->executaSQL($rsRecordSet, $sql, $boTransacao);

    return $obErro;
  }

  private function montaRecuperaDadosBP70() {
  	$sql = "SELECT 70 as tipo_registro,
                   SUM (CASE
                          WHEN contabil.conta = '1.1.0.0.0.00.00'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END)
                   -
                   SUM (CASE
                          WHEN contabil.conta = '1.2.0.0.0.00.00'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_tot_superavit_deficit_financeiro ";
  	$sql.= $this->montaFromValoresContabeis();
  	$sql.= "WHERE configuracao_dcasp_arquivo.nome_arquivo_pertencente = 'BP'
			            AND configuracao_dcasp_registros.exercicio = '" . $this->getDado('exercicio') . "'
                  AND configuracao_dcasp_registros.tipo_registro = 70
            GROUP BY configuracao_dcasp_arquivo.nome_arquivo_pertencente";
  	return $sql;
  }

  public function recuperaDadosBP71(&$rsRecordSet) {
    $obErro = new Erro;
    $obConexao = new Conexao;
    $rsRecordSet = new RecordSet;
    $boTransacao = new Transacao();

    $sql = $this->montaRecuperaDadosBP71();
    $this->setDebug($sql);
    $obErro = $obConexao->executaSQL($rsRecordSet, $sql, $boTransacao);

    return $obErro;
  }

  private function montaRecuperaDadosBP71() {
  	$sql = "SELECT 71 as tipo_registro,
                   contabil.cod_recurso AS cod_font_recurso,
                   SUM (CASE
                          WHEN contabil.conta = '1.1.0.0.0.00.00'
                            THEN COALESCE(contabil.vl_recurso, 0)
                          ELSE 0
                        END)
                   -
                   SUM (CASE
                          WHEN contabil.conta = '1.2.0.0.0.00.00'
                            THEN COALESCE(contabil.vl_recurso, 0)
                          ELSE 0
                        END) AS vl_saldo_fonte
            FROM tcemg.configuracao_dcasp_registros
				         INNER JOIN tcemg.configuracao_dcasp_arquivo USING (seq_arquivo)
		             LEFT JOIN (SELECT CASE
				                             WHEN valor_lancamento_recurso.tipo_valor = 'C'
		                                   THEN replace(plano_conta_credito.cod_estrutural, '.', '')
	                                   ELSE replace(plano_conta_debito.cod_estrutural, '.', '')
                                   END as conta,
		                               lancamento.exercicio,
                                   lancamento.cod_entidade,
	                                 valor_lancamento_recurso.vl_recurso,
                                   valor_lancamento_recurso.cod_recurso
                            FROM contabilidade.lancamento
                                 INNER JOIN contabilidade.valor_lancamento_recurso ON valor_lancamento_recurso.exercicio = lancamento.exercicio
                                       AND valor_lancamento_recurso.cod_entidade = lancamento.cod_entidade
                                       AND valor_lancamento_recurso.tipo = lancamento.tipo
                                       AND valor_lancamento_recurso.cod_lote = lancamento.cod_lote
                                       AND valor_lancamento_recurso.sequencia = lancamento.sequencia
                                 LEFT JOIN contabilidade.conta_credito ON conta_credito.cod_lote = valor_lancamento_recurso.cod_lote
                    				          AND conta_credito.tipo = valor_lancamento_recurso.tipo
                    				          AND conta_credito.sequencia = valor_lancamento_recurso.sequencia
                    				          AND conta_credito.exercicio = valor_lancamento_recurso.exercicio
                    				          AND conta_credito.tipo_valor = valor_lancamento_recurso.tipo_valor
                    				          AND conta_credito.cod_entidade = valor_lancamento_recurso.cod_entidade
                                 LEFT JOIN contabilidade.conta_debito ON conta_debito.cod_lote = valor_lancamento_recurso.cod_lote
                    				          AND conta_debito.tipo = valor_lancamento_recurso.tipo
                    				          AND conta_debito.sequencia = valor_lancamento_recurso.sequencia
                    				          AND conta_debito.exercicio = valor_lancamento_recurso.exercicio
                    				          AND conta_debito.tipo_valor = valor_lancamento_recurso.tipo_valor
                    				          AND conta_debito.cod_entidade = valor_lancamento_recurso.cod_entidade
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
                      AND contabil.cod_entidade IN (" . $this->getDado('entidade') . ")
                      AND contabil.cod_recurso IN (100, 101, 102, 116, 117, 118, 119, 122, 123, 124, 129, 142, 143, 144, 145, 146, 147, 148, 149, 150, 151, 152, 153, 154, 155, 156, 157, 190, 191, 192)
            WHERE configuracao_dcasp_arquivo.nome_arquivo_pertencente = 'BP'
			            AND configuracao_dcasp_registros.exercicio = '" . $this->getDado('exercicio') . "'
                  AND configuracao_dcasp_registros.tipo_registro = 71
            GROUP BY configuracao_dcasp_arquivo.nome_arquivo_pertencente,
                     contabil.cod_recurso,
                     contabil.vl_recurso";
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
