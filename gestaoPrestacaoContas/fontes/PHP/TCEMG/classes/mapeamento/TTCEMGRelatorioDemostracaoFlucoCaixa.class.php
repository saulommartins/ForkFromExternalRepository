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
                   receita_despesa.exercicio,
                   receita_despesa.cod_recurso,
                   -- FLUXOS DE CAIXA DAS ATIVIDADES OPERACIONAIS --
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlReceitaDerivadaOriginaria'
	                            THEN COALESCE(receita_despesa.valor, 0)
                          ELSE 0
                        END) AS vl_receita_derivada_originaria,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlTransCorrenteRecebida'
                            THEN COALESCE(receita_despesa.valor, 0)
                          ELSE 0
                        END) AS vl_transf_corrente_recebida,

                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlDesembolsoPessoalDespesas'
                            THEN COALESCE(receita_despesa.valor, 0)
                          ELSE 0
                        END) AS vl_desembolso_pessoal_demais_despesa,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlDesembolsoJurosEncargDivida'
                            THEN COALESCE(receita_despesa.valor, 0)
                          ELSE 0
                        END) AS vl_desembolso_juro_encargo_divida,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlDesembolsoTransfConcedidas'
                            THEN COALESCE(receita_despesa.valor, 0)
                          ELSE 0
                        END) AS vl_desembolso_transferencia_concedida,
                        
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlOutrosDesembolsos'
                            THEN COALESCE(receita_despesa.valor, 0)
                          ELSE 0
                        END) AS vl_outro_desembolso_operacional,
                        
                   (SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlReceitaDerivadaOriginaria'
                            THEN COALESCE(receita_despesa.valor, 0)
                          ELSE 0
                        END)
                     +
                     SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlTransCorrenteRecebida'
                            THEN COALESCE(receita_despesa.valor, 0)
                          ELSE 0
                        END)
                   )
                   -
                   (SUM (CASE
                            WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlDesembolsoPessoalDespesas'
                              THEN COALESCE(receita_despesa.valor, 0)
                            ELSE 0
                          END)
                     +
                     SUM (CASE
                            WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlDesembolsoJurosEncargDivida'
                              THEN COALESCE(receita_despesa.valor, 0)
                            ELSE 0
                          END)
                     +
                     SUM (CASE
                            WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlDesembolsoTransfConcedidas'
                              THEN COALESCE(receita_despesa.valor, 0)
                            ELSE 0
                          END)
                     +
                     SUM (CASE
                            WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlOutrosDesembolsos'
                              THEN COALESCE(receita_despesa.valor, 0)
                            ELSE 0
                          END)) AS vl_fluxo_caixa_liq_atividade_operacional,

                   -- FLUXOS DE CAIXA DAS ATIVIDADES INVESTIMENTO --
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlAlienacaoBens'
                            THEN COALESCE(receita_despesa.valor, 0)
                          ELSE 0
                        END) AS vl_alienacao_bens,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlAmortizacaoEmprestimoConcedido'
                            THEN COALESCE(receita_despesa.valor, 0)
                          ELSE 0
                        END) AS vl_amortizacao_empres_financ_concedido,

                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlAquisicaoAtivoNaoCirculante'
                            THEN COALESCE(receita_despesa.valor, 0)
                          ELSE 0
                        END) AS vl_aquisicao_ativo_circulante,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlConcessaoEmpresFinanciamento'
                            THEN COALESCE(receita_despesa.valor, 0)
                          ELSE 0
                        END) AS vl_concessao_emprestimo_financiamento,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlOutrosDesembolsos'
                            THEN COALESCE(receita_despesa.valor, 0)
                          ELSE 0
                        END) AS vl_outro_desembolso_investimento,
                        
                   ((SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlAlienacaoBens'
                            THEN COALESCE(receita_despesa.valor, 0)
                          ELSE 0
                        END) 
                    +
                     SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlAmortizacaoEmprestimoConcedido'
                            THEN COALESCE(receita_despesa.valor, 0)
                          ELSE 0
                        END)
                    )
                    -
                    (
                     SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlAquisicaoAtivoNaoCirculante'
                            THEN COALESCE(receita_despesa.valor, 0)
                          ELSE 0
                        END) 
                     +
                     SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlConcessaoEmpresFinanciamento'
                            THEN COALESCE(receita_despesa.valor, 0)
                          ELSE 0
                        END)
                     +
                     SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlOutrosDesembolsos'
                            THEN COALESCE(receita_despesa.valor, 0)
                          ELSE 0
                        END)
                    )) AS vl_fluxo_caixa_liq_atividade_investimento,
                        
                   -- FLUXOS DE CAIXA DAS ATIVIDADES DE FINANCIAMENTO --
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlOperacoesCredito'
                            THEN COALESCE(receita_despesa.valor, 0)
                          ELSE 0
                        END) AS vl_operacao_credito,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlIntegralizacaoDependentes' 
                            THEN COALESCE(receita_despesa.valor, 0)
                          ELSE 0
                        END) AS vl_integra_capital_social_empresa_dependente,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlTransCapitalRecebida'
                            THEN COALESCE(receita_despesa.valor, 0)
                          ELSE 0
                        END) AS vl_transferencia_capital_recebida,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlOutrosIngressosFinanciamento'
                            THEN COALESCE(receita_despesa.valor, 0)
                          ELSE 0
                        END) AS vl_outro_ingresso_financiamento,

                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlAmortizacaoRefinanciamento'
                            THEN COALESCE(receita_despesa.valor, 0)
                          ELSE 0
                        END) AS vl_amortizacao_refinanciamento_divida,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlOutrosDesembolsosFinanciamento'
                            THEN COALESCE(receita_despesa.valor, 0)
                          ELSE 0
                        END) AS vl_outro_desembolso_financiamento,

                        
                   ((
                    SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlOperacoesCredito'
                            THEN COALESCE(receita_despesa.valor, 0)
                          ELSE 0
                        END) 
                    +
                    SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlIntegralizacaoDependentes' 
                            THEN COALESCE(receita_despesa.valor, 0)
                          ELSE 0
                        END)
                    +
                    SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlTransCapitalRecebida'
                            THEN COALESCE(receita_despesa.valor, 0)
                          ELSE 0
                        END)
                    +
                    SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlOutrosIngressosFinanciamento'
                            THEN COALESCE(receita_despesa.valor, 0)
                          ELSE 0
                        END)
                   )
                   -
                   (
                    SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlAmortizacaoRefinanciamento'
                            THEN COALESCE(receita_despesa.valor, 0)
                          ELSE 0
                        END)
                    +
                    SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlOutrosDesembolsosFinanciamento'
                            THEN COALESCE(receita_despesa.valor, 0)
                          ELSE 0
                        END)
                   )
                   
                   ) AS vl_tot_desembolso_atividade_financiamento,

                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlCaixaEquivalenteCaixaInicial'
                            THEN COALESCE(receita_despesa.valor, 0)
                          ELSE 0
                        END) AS vl_caixa_equivalente_caixa_inicial,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlCaixaEquivalenteCaixaFinal'
                            THEN COALESCE(receita_despesa.valor, 0)
                          ELSE 0
                        END) AS vl_caixa_equivalente_caixa_final,
                        
                   -- QUADRO DE RECEITAS DERIVADAS E ORIGINÁRIAS -- 

                    SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlRecTributaria'
                            THEN COALESCE(receita_despesa.valor, 0)
                          ELSE 0
                        END) AS vl_receita_tributaria,
                    SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlRecContribuicoes'
                            THEN COALESCE(receita_despesa.valor, 0)
                          ELSE 0
                        END) AS vl_receita_contribuicoes,
                    SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlRecPatrimonial'
                            THEN COALESCE(receita_despesa.valor, 0)
                          ELSE 0
                        END) AS vl_receita_patrimonial,
                    SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlRecAgropecuaria'
                            THEN COALESCE(receita_despesa.valor, 0)
                          ELSE 0
                        END) AS vl_receita_agropecuaria,
                    SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlRecIndustrial'
                            THEN COALESCE(receita_despesa.valor, 0)
                          ELSE 0
                        END) AS vl_receita_industrial,
                    SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlRecServicos'
                            THEN COALESCE(receita_despesa.valor, 0)
                          ELSE 0
                        END) AS vl_receita_servicos,
                    SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlRemuneracaoDisponibilidades'
                            THEN COALESCE(receita_despesa.valor, 0)
                          ELSE 0
                        END) AS vl_remuneracao_disponibilidades,
                    SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlOutrasRecDerivadasOriginarias'
                            THEN COALESCE(receita_despesa.valor, 0)
                          ELSE 0
                        END) AS vl_outras_receiras_derivadas_originarias,
                    (
	                    SUM (CASE
	                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlRecTributaria'
	                            THEN COALESCE(receita_despesa.valor, 0)
	                          ELSE 0
	                        END)
	                    +
	                    SUM (CASE
	                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlRecContribuicoes'
	                            THEN COALESCE(receita_despesa.valor, 0)
	                          ELSE 0
	                        END)
	                    +
	                    SUM (CASE
	                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlRecPatrimonial'
	                            THEN COALESCE(receita_despesa.valor, 0)
	                          ELSE 0
	                        END)
	                    +
	                    SUM (CASE
	                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlRecAgropecuaria'
	                            THEN COALESCE(receita_despesa.valor, 0)
	                          ELSE 0
	                        END)
	                    +
	                    SUM (CASE
	                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlRecIndustrial'
	                            THEN COALESCE(receita_despesa.valor, 0)
	                          ELSE 0
	                        END)
	                    +
	                    SUM (CASE
	                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlRecServicos'
	                            THEN COALESCE(receita_despesa.valor, 0)
	                          ELSE 0
	                        END)
	                    +
	                    SUM (CASE
	                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlRemuneracaoDisponibilidades'
	                            THEN COALESCE(receita_despesa.valor, 0)
	                          ELSE 0
	                        END)
	                    +
	                    SUM (CASE
	                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlOutrasRecDerivadasOriginarias'
	                            THEN COALESCE(receita_despesa.valor, 0)
	                          ELSE 0
	                        END)
                    ) AS vl_total_receiras_derivadas_originarias,

                   -- QUADRO DE TRANSFERÊNCIAS RECEBIDAS E CONCEDIDAS -- 

                    SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlInterTransfCorrenteRecDaUniao'
                            THEN COALESCE(receita_despesa.valor, 0)
                          ELSE 0
                        END) AS vl_inter_transf_corrente_rec_uniao,
                    SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlInterTransfCorrenteRecEstadosEDistritoFederal'
                            THEN COALESCE(receita_despesa.valor, 0)
                          ELSE 0
                        END) AS vl_inter_transf_corrente_rec_estado_df,
                    SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlInterTransfCorrenteRecMunicipios'
                            THEN COALESCE(receita_despesa.valor, 0)
                          ELSE 0
                        END) AS vl_inter_transf_corrente_rec_municipios,
                    SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlInterTransfCorrenteRecOutras'
                            THEN COALESCE(receita_despesa.valor, 0)
                          ELSE 0
                        END) AS vl_inter_transf_corrente_rec_municipios,
                    (
	                    SUM (CASE
	                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlInterTransfCorrenteRecDaUniao'
	                            THEN COALESCE(receita_despesa.valor, 0)
	                          ELSE 0
	                        END)
	                    +
	                    SUM (CASE
	                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlInterTransfCorrenteRecEstadosEDistritoFederal'
	                            THEN COALESCE(receita_despesa.valor, 0)
	                          ELSE 0
	                        END)
	                    +
	                    SUM (CASE
	                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlInterTransfCorrenteRecMunicipios'
	                            THEN COALESCE(receita_despesa.valor, 0)
	                          ELSE 0
	                        END)
	                    +
	                    SUM (CASE
	                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlInterTransfCorrenteRecOutras'
	                            THEN COALESCE(receita_despesa.valor, 0)
	                          ELSE 0
	                        END)
                     ) AS vl_total_transf_corrente_recebidas,
                    SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlInterTransfCorrenteConcDaUniao'
                            THEN COALESCE(receita_despesa.valor, 0)
                          ELSE 0
                        END) AS vl_inter_transf_corrente_conc_uniao,
                    SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlInterTransfCorrenteConcEstadosEDistritoFederal'
                            THEN COALESCE(receita_despesa.valor, 0)
                          ELSE 0
                        END) AS vl_inter_transf_corrente_conc_estado_df,
                    SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlInterTransfCorrenteConcMunicipios'
                            THEN COALESCE(receita_despesa.valor, 0)
                          ELSE 0
                        END) AS vl_inter_transf_corrente_conc_municipios,
                    (
	                    SUM (CASE
	                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlInterTransfCorrenteConcOutras'
	                            THEN COALESCE(receita_despesa.valor, 0)
	                          ELSE 0
	                        END) AS vl_inter_transf_corrente_conc_outras,
	                    SUM (CASE
	                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlInterTransfCorrenteConcDaUniao'
	                            THEN COALESCE(receita_despesa.valor, 0)
	                          ELSE 0
	                        END)
	                    +
	                    SUM (CASE
	                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlInterTransfCorrenteConcEstadosEDistritoFederal'
	                            THEN COALESCE(receita_despesa.valor, 0)
	                          ELSE 0
	                        END)
	                    +
	                    SUM (CASE
	                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlInterTransfCorrenteConcMunicipios'
	                            THEN COALESCE(receita_despesa.valor, 0)
	                          ELSE 0
	                        END)
	                    +
	                    SUM (CASE
	                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlInterTransfCorrenteConcOutras'
	                            THEN COALESCE(receita_despesa.valor, 0)
	                          ELSE 0
	                        END)
                     ) AS vl_total_transf_corrente_concedidas,

                   -- QUADRO DE DESEMBOLSOS DE PESSOAL E DEMAIS DESPESAS POR FUNÇÃO  -- 

                    SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlDepesaFunLegislativa'
                            THEN COALESCE(receita_despesa.valor, 0)
                          ELSE 0
                        END) AS vl_despesa_fun_legislativa,
                    SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlDepesaFunJudiciaria'
                            THEN COALESCE(receita_despesa.valor, 0)
                          ELSE 0
                        END) AS vl_despesa_fun_judiciaria,
                    SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlDepesaFunEssencialJustica'
                            THEN COALESCE(receita_despesa.valor, 0)
                          ELSE 0
                        END) AS vl_despesa_fun_essencial_justica,
                    SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlDepesaFunAdministracao'
                            THEN COALESCE(receita_despesa.valor, 0)
                          ELSE 0
                        END) AS vl_despesa_fun_administracao,
                    SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlDepesaFunDefesaNacional'
                            THEN COALESCE(receita_despesa.valor, 0)
                          ELSE 0
                        END) AS vl_despesa_fun_defesa_nacional,
                    SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlDepesaFunSegurançaPublica'
                            THEN COALESCE(receita_despesa.valor, 0)
                          ELSE 0
                        END) AS vl_despesa_fun_segurança_publica,
                    SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlDepesaFunRelacoesExteriores'
                            THEN COALESCE(receita_despesa.valor, 0)
                          ELSE 0
                        END) AS vl_despesa_fun_relacoes_exteriores,
                    SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlDepesaFunAssistenciaSocial'
                            THEN COALESCE(receita_despesa.valor, 0)
                          ELSE 0
                        END) AS vl_despesa_fun_assistencia_social,
                    SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlDepesaFunPrevidenciaSocial'
                            THEN COALESCE(receita_despesa.valor, 0)
                          ELSE 0
                        END) AS vl_despesa_fun_previdencia_social,
                    SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlDepesaFunSaude'
                            THEN COALESCE(receita_despesa.valor, 0)
                          ELSE 0
                        END) AS vl_despesa_fun_saude,
                    SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlDepesaFunTrabalho'
                            THEN COALESCE(receita_despesa.valor, 0)
                          ELSE 0
                        END) AS vl_despesa_fun_trabalho,
                    SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlDepesaFunEducacao'
                            THEN COALESCE(receita_despesa.valor, 0)
                          ELSE 0
                        END) AS vl_despesa_fun_educacao,
                    SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlDepesaFunCultura'
                            THEN COALESCE(receita_despesa.valor, 0)
                          ELSE 0
                        END) AS vl_despesa_fun_cultura,
                    SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlDepesaFunDireitosCidadania'
                            THEN COALESCE(receita_despesa.valor, 0)
                          ELSE 0
                        END) AS vl_despesa_fun_direitos_cidadania,
                    SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlDepesaFunUrbanismo'
                            THEN COALESCE(receita_despesa.valor, 0)
                          ELSE 0
                        END) AS vl_despesa_fun_urbanismo,
                    SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlDepesaFunHabitacao'
                            THEN COALESCE(receita_despesa.valor, 0)
                          ELSE 0
                        END) AS vl_despesa_fun_habitacao,
                    SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlDepesaFunSaneamento'
                            THEN COALESCE(receita_despesa.valor, 0)
                          ELSE 0
                        END) AS vl_despesa_fun_saneamento,
                    SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlDepesaFunGestaoAmbiental'
                            THEN COALESCE(receita_despesa.valor, 0)
                          ELSE 0
                        END) AS vl_despesa_fun_gestao_ambiental,
                    SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlDepesaFunCienciaTecnologia'
                            THEN COALESCE(receita_despesa.valor, 0)
                          ELSE 0
                        END) AS vl_despesa_fun_ciencia_tecnologia,
                    SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlDepesaFunAgricultura'
                            THEN COALESCE(receita_despesa.valor, 0)
                          ELSE 0
                        END) AS vl_despesa_fun_agricultura,
                    SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlDepesaFunOrganizacaoAgraria'
                            THEN COALESCE(receita_despesa.valor, 0)
                          ELSE 0
                        END) AS vl_despesa_fun_organizacao_agraria,
                    SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlDepesaFunIndustria'
                            THEN COALESCE(receita_despesa.valor, 0)
                          ELSE 0
                        END) AS vl_despesa_fun_industria,
                    SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlDepesaFunComercioServicos'
                            THEN COALESCE(receita_despesa.valor, 0)
                          ELSE 0
                        END) AS vl_despesa_fun_comercio_servicos,
                    SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlDepesaFunComunicacoes'
                            THEN COALESCE(receita_despesa.valor, 0)
                          ELSE 0
                        END) AS vl_despesa_fun_comunicacoes,
                    SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlDepesaFunEnergia'
                            THEN COALESCE(receita_despesa.valor, 0)
                          ELSE 0
                        END) AS vl_despesa_fun_energia,
                    SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlDepesaFunTransporte'
                            THEN COALESCE(receita_despesa.valor, 0)
                          ELSE 0
                        END) AS vl_despesa_fun_transporte,
                    SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlDepesaFunDesportoLazer'
                            THEN COALESCE(receita_despesa.valor, 0)
                          ELSE 0
                        END) AS vl_despesa_fun_desporto_lazer,
                    SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlDepesaFunEncargosEspeciais'
                            THEN COALESCE(receita_despesa.valor, 0)
                          ELSE 0
                        END) AS vl_despesa_fun_encargos_especiais,
                        
                    (
	                    SUM (CASE
	                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlDepesaFunLegislativa'
	                            THEN COALESCE(receita_despesa.valor, 0)
	                          ELSE 0
	                        END)
	                    +
	                    SUM (CASE
	                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlDepesaFunJudiciaria'
	                            THEN COALESCE(receita_despesa.valor, 0)
	                          ELSE 0
	                        END)
	                    +
	                    SUM (CASE
	                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlDepesaFunEssencialJustica'
	                            THEN COALESCE(receita_despesa.valor, 0)
	                          ELSE 0
	                        END)
	                    +
	                    SUM (CASE
	                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlDepesaFunAdministracao'
	                            THEN COALESCE(receita_despesa.valor, 0)
	                          ELSE 0
	                        END)
	                    +
	                    SUM (CASE
	                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlDepesaFunDefesaNacional'
	                            THEN COALESCE(receita_despesa.valor, 0)
	                          ELSE 0
	                        END)
	                    +
	                    SUM (CASE
	                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlDepesaFunSegurançaPublica'
	                            THEN COALESCE(receita_despesa.valor, 0)
	                          ELSE 0
	                        END)
	                    +
	                    SUM (CASE
	                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlDepesaFunRelacoesExteriores'
	                            THEN COALESCE(receita_despesa.valor, 0)
	                          ELSE 0
	                        END)
	                    +
	                    SUM (CASE
	                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlDepesaFunAssistenciaSocial'
	                            THEN COALESCE(receita_despesa.valor, 0)
	                          ELSE 0
	                        END)
	                    +
	                    SUM (CASE
	                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlDepesaFunPrevidenciaSocial'
	                            THEN COALESCE(receita_despesa.valor, 0)
	                          ELSE 0
	                        END)
	                    +
	                    SUM (CASE
	                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlDepesaFunSaude'
	                            THEN COALESCE(receita_despesa.valor, 0)
	                          ELSE 0
	                        END)
	                    +
	                    SUM (CASE
	                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlDepesaFunTrabalho'
	                            THEN COALESCE(receita_despesa.valor, 0)
	                          ELSE 0
	                        END)
	                    +
	                    SUM (CASE
	                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlDepesaFunEducacao'
	                            THEN COALESCE(receita_despesa.valor, 0)
	                          ELSE 0
	                        END)
	                    +
	                    SUM (CASE
	                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlDepesaFunCultura'
	                            THEN COALESCE(receita_despesa.valor, 0)
	                          ELSE 0
	                        END)
	                    +
	                    SUM (CASE
	                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlDepesaFunDireitosCidadania'
	                            THEN COALESCE(receita_despesa.valor, 0)
	                          ELSE 0
	                        END)
	                    +
	                    SUM (CASE
	                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlDepesaFunUrbanismo'
	                            THEN COALESCE(receita_despesa.valor, 0)
	                          ELSE 0
	                        END)
	                    +
	                    SUM (CASE
	                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlDepesaFunHabitacao'
	                            THEN COALESCE(receita_despesa.valor, 0)
	                          ELSE 0
	                        END)
	                    +
	                    SUM (CASE
	                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlDepesaFunSaneamento'
	                            THEN COALESCE(receita_despesa.valor, 0)
	                          ELSE 0
	                        END)
	                    +
	                    SUM (CASE
	                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlDepesaFunGestaoAmbiental'
	                            THEN COALESCE(receita_despesa.valor, 0)
	                          ELSE 0
	                        END)
	                    +
	                    SUM (CASE
	                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlDepesaFunCienciaTecnologia'
	                            THEN COALESCE(receita_despesa.valor, 0)
	                          ELSE 0
	                        END)
	                    +
	                    SUM (CASE
	                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlDepesaFunAgricultura'
	                            THEN COALESCE(receita_despesa.valor, 0)
	                          ELSE 0
	                        END)
	                    +
	                    SUM (CASE
	                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlDepesaFunOrganizacaoAgraria'
	                            THEN COALESCE(receita_despesa.valor, 0)
	                          ELSE 0
	                        END)
	                    +
	                    SUM (CASE
	                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlDepesaFunIndustria'
	                            THEN COALESCE(receita_despesa.valor, 0)
	                          ELSE 0
	                        END)
	                    +
	                    SUM (CASE
	                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlDepesaFunComercioServicos'
	                            THEN COALESCE(receita_despesa.valor, 0)
	                          ELSE 0
	                        END)
	                    +
	                    SUM (CASE
	                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlDepesaFunComunicacoes'
	                            THEN COALESCE(receita_despesa.valor, 0)
	                          ELSE 0
	                        END)
	                    +
	                    SUM (CASE
	                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlDepesaFunEnergia'
	                            THEN COALESCE(receita_despesa.valor, 0)
	                          ELSE 0
	                        END)
	                    +
	                    SUM (CASE
	                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlDepesaFunTransporte'
	                            THEN COALESCE(receita_despesa.valor, 0)
	                          ELSE 0
	                        END)
	                    +
	                    SUM (CASE
	                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlDepesaFunDesportoLazer'
	                            THEN COALESCE(receita_despesa.valor, 0)
	                          ELSE 0
	                        END)
	                    +
	                    SUM (CASE
	                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlDepesaFunEncargosEspeciais'
	                            THEN COALESCE(receita_despesa.valor, 0)
	                          ELSE 0
	                        END) 
                    ) AS vl_total_desembolso_despesa_funcao,

                   -- QUADRO DE JUROS E ENCARGOS DA DÍVIDA  -- 

                    SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlJurosCorrecaoMonetariaDividaInterna'
                            THEN COALESCE(receita_despesa.valor, 0)
                          ELSE 0
                        END) AS vl_juros_correcao_monetaria_divida_interna,
                    SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlJurosCorrecaoMonetariaDividaExterna'
                            THEN COALESCE(receita_despesa.valor, 0)
                          ELSE 0
                        END) AS vl_juros_correcao_monetaria_divida_externa,
                    SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlOutrosEncargosDaDivida'
                            THEN COALESCE(receita_despesa.valor, 0)
                          ELSE 0
                        END) AS vl_outros_encargos_da_divida,

                    (
	                    SUM (CASE
	                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlJurosCorrecaoMonetariaDividaInterna'
	                            THEN COALESCE(receita_despesa.valor, 0)
	                          ELSE 0
	                        END)
	                    +
	                    SUM (CASE
	                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlJurosCorrecaoMonetariaDividaExterna'
	                            THEN COALESCE(receita_despesa.valor, 0)
	                          ELSE 0
	                        END)
	                    +
	                    SUM (CASE
	                          WHEN configuracao_dcasp_arquivo.nome_tag = 'rel_vlOutrosEncargosDaDivida'
	                            THEN COALESCE(receita_despesa.valor, 0)
	                          ELSE 0
                        END)
                    ) AS vl_total_correcao_monetaria
                        
                        ";
		$sql.= $this->montaFromValoresContabeis();

		$sql.= "WHERE configuracao_dcasp_arquivo.nome_arquivo_pertencente = 'DFC'
		          AND receita_despesa.exercicio = configuracao_dcasp_arquivo.exercicio 
                  AND receita_despesa.exercicio IN ('" . ($this->getDado('exercicio') - 1) . "','" . $this->getDado('exercicio') . "')

				 AND (
					CASE WHEN configuracao_dcasp_recursos.cod_recurso is not null
					     THEN receita_despesa.cod_recurso = configuracao_dcasp_recursos.cod_recurso
					     ELSE true
					 END
				 )
            GROUP BY receita_despesa.exercicio, receita_despesa.cod_recurso ";
		error_log("sql = ".$sql,3,"/tmp/relatorio_fluxobaixa.log");
		return $sql;
	}

	private function montaFromValoresContabeis() {
		return "FROM tcemg.configuracao_dcasp_registros
                 JOIN tcemg.configuracao_dcasp_arquivo using (seq_arquivo)
                 LEFT JOIN (SELECT contas_despesa_receita.conta,
                                   contas_despesa_receita.exercicio,
                                   contas_despesa_receita.cod_entidade,
                                   contas_despesa_receita.valor,
                                   contas_despesa_receita.tipo_valor,
                                   contas_despesa_receita.cod_recurso
                          FROM 
                          (
	                          (          
	                          SELECT  replace(conta_receita.cod_estrutural, '.', '') as conta,
	                                  arrecadacao_receita.exercicio,
	                                  receita.cod_entidade,
	                                  receita.vl_original,
	                                  'C' AS tipo_valor,
	                                  receita.cod_recurso,
	                                  SUM(arrecadacao_receita.vl_arrecadacao) AS valor
	
	                            FROM  tesouraria.arrecadacao_receita
	
	                            JOIN  orcamento.receita
	                              ON  arrecadacao_receita.cod_receita = receita.cod_receita
	                             AND  arrecadacao_receita.exercicio = receita.exercicio
	
	                            JOIN  orcamento.conta_receita
	                              ON  conta_receita.cod_conta = receita.cod_conta
	                             AND  conta_receita.exercicio = receita.exercicio
	
	                           GROUP  BY conta_receita.cod_estrutural,
	                                     arrecadacao_receita.exercicio,
	                                     receita.cod_entidade,
	                                     receita.vl_original,
	                                     tipo_valor,
	                                     receita.cod_recurso
                              )
	                          UNION
	                          (
                                  SELECT  replace(despesas.cod_estrutural, '.', '') as conta,
				                        despesas.exercicio,
	                                    despesas.cod_entidade, 
	                                    despesas.vl_original, 
	                                    'D' AS tipo_valor,
	                                    despesas.cod_recurso,
	                                    COALESCE(SUM(empenhos.vl_liquidacao_paga), 0.00) AS valor
	                              FROM  (
	                                      SELECT  conta_despesa.cod_estrutural, 
	                                              despesa.cod_despesa, 
	                                              despesa.vl_original, 
	                                              despesa.cod_entidade,
	                                              despesa.exercicio,
	                                              despesa.cod_recurso
	                                        FROM  orcamento.despesa
	
	                                        JOIN  orcamento.conta_despesa
	                                          ON  conta_despesa.exercicio = despesa.exercicio
	                                         AND  conta_despesa.cod_conta = despesa.cod_conta
	
                                  ) AS despesas
	
                                  LEFT  JOIN (
                                      SELECT  pre_empenho.exercicio,
                                              pre_empenho.cod_pre_empenho, 
                                              pre_empenho.exercicio, 
                                              pre_empenho_despesa.cod_despesa, 
                                              COALESCE(SUM(nota_liquidacao_item.vl_total), 0.00) AS vl_liquidado,
                                              COALESCE(SUM(nota_liquidacao_item_anulado.vl_anulado), 0.00) AS vl_anulado,
                                              COALESCE(SUM(nota_liquidacao_paga.vl_pago), 0.00) AS vl_liquidacao_paga
                              
                                        FROM  empenho.pre_empenho
                            
                                        JOIN  empenho.pre_empenho_despesa
                                          ON  pre_empenho_despesa.exercicio = pre_empenho.exercicio
                                         AND  pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho

                                        JOIN  empenho.empenho
                                          ON  empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                                         AND  empenho.exercicio = pre_empenho.exercicio

                                        LEFT  JOIN empenho.empenho_anulado
                                          ON  empenho_anulado.exercicio = empenho.exercicio
                                         AND  empenho_anulado.cod_entidade = empenho.cod_entidade
                                         AND  empenho_anulado.cod_empenho = empenho.cod_empenho

                                        LEFT  JOIN empenho.nota_liquidacao
                                          ON  empenho.exercicio = nota_liquidacao.exercicio_empenho
                                         AND  empenho.cod_entidade = nota_liquidacao.cod_entidade
                                         AND  empenho.cod_empenho  = nota_liquidacao.cod_empenho

                                        LEFT  JOIN empenho.nota_liquidacao_item
                                          ON  nota_liquidacao.exercicio = nota_liquidacao_item.exercicio
                                         AND  nota_liquidacao.cod_entidade = nota_liquidacao_item.cod_entidade
                                         AND  nota_liquidacao.cod_nota = nota_liquidacao_item.cod_nota

                                        LEFT  JOIN empenho.nota_liquidacao_item_anulado
                                          ON  nota_liquidacao_item_anulado.exercicio = nota_liquidacao_item.exercicio
                                         AND  nota_liquidacao_item_anulado.cod_nota = nota_liquidacao_item.cod_nota
                                         AND  nota_liquidacao_item_anulado.num_item = nota_liquidacao_item.num_item
                                         AND  nota_liquidacao_item_anulado.exercicio_item = nota_liquidacao_item.exercicio_item
                                         AND  nota_liquidacao_item_anulado.cod_pre_empenho = nota_liquidacao_item.cod_pre_empenho
                                         AND  nota_liquidacao_item_anulado.cod_entidade = nota_liquidacao_item.cod_entidade

                                        LEFT  JOIN empenho.nota_liquidacao_paga
                                          ON  nota_liquidacao_paga.exercicio = nota_liquidacao.exercicio
                                         AND  nota_liquidacao_paga.cod_entidade = nota_liquidacao.cod_entidade
                                         AND  nota_liquidacao_paga.cod_nota = nota_liquidacao.cod_nota

                                        LEFT  JOIN empenho.nota_liquidacao_paga_anulada
                                          ON  nota_liquidacao_paga_anulada.exercicio = nota_liquidacao_paga.exercicio
                                         AND  nota_liquidacao_paga_anulada.cod_entidade = nota_liquidacao_paga.cod_entidade
                                         AND  nota_liquidacao_paga_anulada.cod_nota = nota_liquidacao_paga.cod_nota

                                       WHERE empenho_anulado.cod_empenho IS NULL
                                         AND  nota_liquidacao_paga_anulada.cod_nota IS NULL

                                       GROUP  BY pre_empenho.cod_pre_empenho, pre_empenho.exercicio, pre_empenho_despesa.cod_despesa
                                    ) AS empenhos

                                  ON  empenhos.cod_despesa = despesas.cod_despesa

                                  GROUP  BY conta,
	                                    despesas.cod_entidade, 
	                                    despesas.exercicio,
	                                    despesas.vl_original, 
	                                    tipo_valor,
	                                    despesas.cod_recurso
	                          )
	                      ) AS contas_despesa_receita
                 ) AS receita_despesa ON configuracao_dcasp_registros.exercicio = receita_despesa.exercicio
                 AND receita_despesa.conta in (replace(configuracao_dcasp_registros.conta_orc_receita, '.', ''), replace(configuracao_dcasp_registros.conta_orc_despesa, '.', ''))
                 AND receita_despesa.cod_entidade IN (" . $this->getDado('entidades'). ")  
                 
                 LEFT JOIN tcemg.configuracao_dcasp_recursos using (seq_arquivo) ";
	}



}
