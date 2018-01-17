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

class TTCEMGDemonstracaoVariacaoPatrimonial extends Persistente {
  public function TTCEMGDemonstracaoVariacaoPatrimonial() {
    parent::Persistente();
  }

  public function recuperaDadosDVP10(&$rsRecordSet) {
    $obErro = new Erro;
    $obConexao = new Conexao;
    $rsRecordSet = new RecordSet;
    $boTransacao = new Transacao();

    $sql = $this->montaRecuperaDadosDVP10();
    $this->setDebug($sql);
    $obErro = $obConexao->executaSQL($rsRecordSet, $sql, $boTransacao);

    return $obErro;
  }

  private function montaRecuperaDadosDVP10() {
  	$sql = "SELECT 10 as tipo_registro,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlImpostos'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_impostos,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlContribuicoes'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_contribuicoes,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlExploracoVendasDireitos'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_exploracao_vendas_bens_serv_direitos,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlVariacoesAumentativasFinanceiras'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_variacoes_aumentativas_financeiras,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlTransfDelegacoesRecebidas'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_transf_delegacoes_recebidas,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlValorizacaoAtivoDesincorPassivo'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_valoriz_ativo_desincorporacao_passivos,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlOutrasVariacoesPatriAumentativas'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_outras_variacoes_patrim_aumentativas,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlTotalVPAumentativas'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_total_variacoes_patrimon_aumentativas ";
  	$sql.= $this->montaFromValoresContabeis();
  	$sql.= "WHERE configuracao_dcasp_arquivo.nome_arquivo_pertencente = 'DVP'
			            AND configuracao_dcasp_registros.exercicio = '" . $this->getDado('exercicio') . "'
                  AND configuracao_dcasp_registros.tipo_registro = 10
            GROUP BY configuracao_dcasp_arquivo.nome_arquivo_pertencente";
  	return $sql;
  }

  public function recuperaDadosDVP20(&$rsRecordSet) {
    $obErro = new Erro;
    $obConexao = new Conexao;
    $rsRecordSet = new RecordSet;
    $boTransacao = new Transacao();

    $sql = $this->montaRecuperaDadosDVP20();
    $this->setDebug($sql);
    $obErro = $obConexao->executaSQL($rsRecordSet, $sql, $boTransacao);

    return $obErro;
  }

  private function montaRecuperaDadosDVP20() {
  	$sql = "SELECT 20 as tipo_registro,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlDiminutivaPessoaEncargos'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_variacao_patrim_diminu_pessoal_encargos,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlPrevAssistenciais'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_beneficio_previdenciario_assistencial,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlServicosCapitalFixo'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_uso_bens_servicos_consumo_capital_fixo,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlDiminutivaVariacoesFinanceiras'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_varia_patri_diminutiva_financeira,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlTransfConcedidas'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_transferencia_delegacoes_concedidas,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlDesvaloAtivoIncorpoPassivo'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_desvalo_perdas_ativ_incorporacao_passivos,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlTributarias'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_varia_patrimonial_diminutiva_tributaria,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlMercadoriaVendidoServicos'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_custo_mercado_produ_vendido_servi_prestado,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlOutrasVariacoesPatriDiminutivas'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_outras_varia_patrim_diminutiva,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlTotalVPDiminutivas'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_total_variacoes_patrimoniais ";
  	$sql.= $this->montaFromValoresContabeis();
  	$sql.= "WHERE configuracao_dcasp_arquivo.nome_arquivo_pertencente = 'DVP'
			            AND configuracao_dcasp_registros.exercicio = '" . $this->getDado('exercicio') . "'
                  AND configuracao_dcasp_registros.tipo_registro = 20
            GROUP BY configuracao_dcasp_arquivo.nome_arquivo_pertencente";
  	return $sql;
  }

  public function recuperaDadosDVP30(&$rsRecordSet) {
    $obErro = new Erro;
    $obConexao = new Conexao;
    $rsRecordSet = new RecordSet;
    $boTransacao = new Transacao();

    $sql = $this->montaRecuperaDadosDVP30();
    $this->setDebug($sql);
    $obErro = $obConexao->executaSQL($rsRecordSet, $sql, $boTransacao);

    return $obErro;
  }

  private function montaRecuperaDadosDVP30() {
  	$sql = "SELECT 30 as tipo_registro,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlResultadoPatrimonialPeriodo'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_resultado_patrimonial_periodo ";
  	$sql.= $this->montaFromValoresContabeis();
  	$sql.= "WHERE configuracao_dcasp_arquivo.nome_arquivo_pertencente = 'DVP'
			            AND configuracao_dcasp_registros.exercicio = '" . $this->getDado('exercicio') . "'
                  AND configuracao_dcasp_registros.tipo_registro = 30
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
