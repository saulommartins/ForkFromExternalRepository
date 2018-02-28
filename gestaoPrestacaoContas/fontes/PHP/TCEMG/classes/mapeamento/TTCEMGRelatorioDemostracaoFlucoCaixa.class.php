<?php

ini_set("display_errors", 1);
error_reporting(E_ALL);

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCEMGRelatorioDemonstracaoFluxoCaixa extends Persistente
{
	public function recuperaDadosDemonstracaoFluxoCaixa($metodo, &$rsRecordSet, $stFiltro = "",
		$stOrder =
		"",
		$boTransacao = "")
	{
		return $this->executaRecupera($metodo, $rsRecordSet, $stFiltro, $stOrder, $boTransacao);
	}



	public function sqlDemostracaoFluxoCaixa() {
		$sql = "SELECT 
                   contabil.exercicio,
                   -- FLUXOS DE CAIXA DAS ATIVIDADES OPERACIONAIS --
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
                        
                   (SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlReceitaDerivadaOriginaria'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END)
                     +
                     SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlTransCorrenteRecebida'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END)
                   )
                   -
                   (SUM (CASE
                            WHEN configuracao_dcasp_arquivo.nome_tag = 'vlDesembolsoPessoalDespesas'
                              THEN COALESCE(contabil.valor, 0)
                            ELSE 0
                          END)
                     +
                     SUM (CASE
                            WHEN configuracao_dcasp_arquivo.nome_tag = 'vlDesembolsoJurosEncargDivida'
                              THEN COALESCE(contabil.valor, 0)
                            ELSE 0
                          END)
                     +
                     SUM (CASE
                            WHEN configuracao_dcasp_arquivo.nome_tag = 'vlDesembolsoTransfConcedidas'
                              THEN COALESCE(contabil.valor, 0)
                            ELSE 0
                          END)
                     +
                     SUM (CASE
                            WHEN configuracao_dcasp_arquivo.nome_tag = 'vlOutrosDesembolsos'
                              THEN COALESCE(contabil.valor, 0)
                            ELSE 0
                          END)) AS vl_fluxo_caixa_liq_atividade_operacional,

                   -- FLUXOS DE CAIXA DAS ATIVIDADES INVESTIMENTO --
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
                        
                   ((SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlAlienacaoBens'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) 
                    +
                     SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlAmortizacaoEmprestimoConcedido'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END)
                    )
                    -
                    (
                     SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlAquisicaoAtivoNaoCirculante'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) 
                     +
                     SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlConcessaoEmpresFinanciamento'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END)
                     +
                     SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlOutrosDesembolsos'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END)
                    )) AS vl_fluxo_caixa_liq_atividade_investimento,
                        
                   -- FLUXOS DE CAIXA DAS ATIVIDADES DE FINANCIAMENTO --
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
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlAmortizacaoRefinanciamento'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_amortizacao_refinanciamento_divida,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlOutrosDesembolsosFinanciamento'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_outro_desembolso_financiamento,

                        
                   ((
                    SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlOperacoesCredito'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) 
                    +
                    SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlIntegralizacaoDependentes' 
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END)
                    +
                    SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlTransCapitalRecebida'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END)
                    +
                    SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlOutrosIngressosFinanciamento'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END)
                   )
                   -
                   (
                    SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlAmortizacaoRefinanciamento'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END)
                    +
                    SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlOutrosDesembolsosFinanciamento'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END)
                   )
                   
                   ) AS vl_tot_desembolso_atividade_financiamento,

                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlCaixaEquivalenteCaixaInicial'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_caixa_equivalente_caixa_inicial,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlCaixaEquivalenteCaixaFinal'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_caixa_equivalente_caixa_final,
                        
                   -- QUADRO DE RECEITAS DERIVADAS E ORIGINÁRIAS -- 

                    SUM (CASE
                          WHEN contabil.conta LIKE '11%'
                             AND contabil.tipo_valor = 'C'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_receita_tributaria,
                    SUM (CASE
                          WHEN contabil.conta LIKE '12%'
                             AND contabil.tipo_valor = 'C'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_receita_contribuicoes,
                    SUM (CASE
                          WHEN contabil.conta LIKE '13%'
                             AND contabil.tipo_valor = 'C'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_receita_patrimonial,
                    SUM (CASE
                          WHEN contabil.conta LIKE '14%'
                             AND contabil.tipo_valor = 'C'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_receita_agropecuaria,
                    SUM (CASE
                          WHEN contabil.conta LIKE '15%'
                             AND contabil.tipo_valor = 'C'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_receita_industrial,
                    SUM (CASE
                          WHEN contabil.conta LIKE '16%'
                             AND contabil.tipo_valor = 'C'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_receita_servicos,
                    SUM (CASE
                          WHEN contabil.conta LIKE 'NAO_DEFINIDO%'
                             AND contabil.tipo_valor = 'C'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_remuneracao_disponibilidades,
                    SUM (CASE
                          WHEN contabil.conta LIKE '19%'
                             AND contabil.tipo_valor = 'C'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) AS vl_outras_receiras_derivadas_originarias,
                    (
                        SUM (CASE
                          WHEN contabil.conta LIKE '11%'
                             AND contabil.tipo_valor = 'C'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) 
                    +
                    SUM (CASE
                          WHEN contabil.conta LIKE '12%'
                             AND contabil.tipo_valor = 'C'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) 
                    +
                    SUM (CASE
                          WHEN contabil.conta LIKE '13%'
                             AND contabil.tipo_valor = 'C'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) 
                    +
                    SUM (CASE
                          WHEN contabil.conta LIKE '14%'
                             AND contabil.tipo_valor = 'C'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) 
                    +
                    SUM (CASE
                          WHEN contabil.conta LIKE '15%'
                             AND contabil.tipo_valor = 'C'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) 
                    +
                    SUM (CASE
                          WHEN contabil.conta LIKE '16%'
                             AND contabil.tipo_valor = 'C'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) 
                    +
                    SUM (CASE
                          WHEN contabil.conta LIKE 'NAO_DEFINIDO%'
                             AND contabil.tipo_valor = 'C'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END) 
                    +
                    SUM (CASE
                          WHEN contabil.conta LIKE '19%'
                             AND contabil.tipo_valor = 'C'
                            THEN COALESCE(contabil.valor, 0)
                          ELSE 0
                        END)
                    ) AS vl_total_receiras_derivadas_originarias
                                 
                        ";
		$sql.= $this->montaFromValoresContabeis();

		$sql.= "WHERE configuracao_dcasp_arquivo.nome_arquivo_pertencente = 'DFC'
		          AND contabil.exercicio = configuracao_dcasp_arquivo.exercicio 
                  AND contabil.exercicio IN ('" . ($this->getDado('exercicio') - 1) . "','" . $this->getDado('exercicio') . "')

            GROUP BY contabil.exercicio ";
		error_log("sql = ".$sql,3,"/tmp/relatorio_fluxobaixa.log");
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
                                   valor_lancamento.vl_lancamento as valor,
                                   valor_lancamento.tipo_valor
                            FROM contabilidade.lancamento
                                 JOIN contabilidade.valor_lancamento ON valor_lancamento.exercicio = lancamento.exercicio
                                      AND valor_lancamento.cod_entidade = lancamento.cod_entidade
                                      AND valor_lancamento.tipo = lancamento.tipo
                                      AND valor_lancamento.cod_lote = lancamento.cod_lote
                                      AND valor_lancamento.sequencia = lancamento.sequencia
                                 JOIN contabilidade.lote
                                      ON lote.exercicio = lancamento.exercicio
                                      AND lote.cod_entidade = lancamento.cod_entidade
                                      AND lote.tipo = lancamento.tipo
                                      AND lote.cod_lote = lancamento.cod_lote
                                      AND (
                                          lote.dt_lote BETWEEN '".$this->getDado('stDataInicialExercicioAtual')."' 
                                          AND 
                                          '".$this->getDado('stDataFinalExercicioAtual')."'
                                          OR 
                                          lote.dt_lote BETWEEN '".$this->getDado('stDataInicialExercicioAnterior')."' 
                                          AND '".$this->getDado('stDataFinalExercicioAnterior')."'
                                      )
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
                                 LEFT JOIN contabilidade.plano_conta_encerrada AS plano_conta_encerrada_credito
                                      ON plano_conta_encerrada_credito.cod_conta = plano_conta_credito.cod_conta
                                      AND plano_conta_encerrada_credito.exercicio = plano_conta_credito.exercicio
                                 LEFT JOIN contabilidade.plano_conta_encerrada AS plano_conta_encerrada_debito
                                      ON plano_conta_encerrada_debito.exercicio = plano_conta_debito.exercicio
                                      AND plano_conta_encerrada_debito.cod_conta = plano_conta_debito.cod_conta
                                      
                                 WHERE 
                                      plano_conta_encerrada_debito.cod_conta IS NULL
                                      AND plano_conta_encerrada_credito.cod_conta IS NULL
                           ) AS contabil ON configuracao_dcasp_registros.exercicio = contabil.exercicio
                      AND replace(configuracao_dcasp_registros.conta_contabil, '.', '') = contabil.conta
                      AND contabil.cod_entidade IN (" . $this->getDado('entidades')




			. ") ";
	}



}
