<?php

    ini_set("display_errors", 1);
    error_reporting(E_ALL);

    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
    include_once ( CLA_PERSISTENTE );

    class TTCEMGRelatorioBalancoPatrimonial extends Persistente
    {
        public function recuperaDadosBalancoPatrimonial($metodo, &$rsRecordSet, $stFiltro = "", $stOrder = "", $boTransacao = "")
        {
            return $this->executaRecupera($metodo, $rsRecordSet, $stFiltro, $stOrder, $boTransacao);
        }

        public function montaRecuperaDadosBalancoPatrimonial10()
        {
        	return "
        		SELECT 	-- ATIVO -> ATIVO CIRCULANTE -> CAIXA E EQUIVALENTES DE CAIXA
        				SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlAtivoCircuCaixaEquiCaixa'
								 AND contabil.exercicio = '".$this->getDado('exercicio')."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
				       	) AS vl_ativo_circ_caixa_equival_caixa_exercicio_atual,
				    	SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlAtivoCircuCaixaEquiCaixa'
								 AND contabil.exercicio = '".(intval($this->getDado('exercicio')) - 1)."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
					    ) AS vl_ativo_circ_caixa_equival_caixa_exercicio_anterior,
        		        -- ATIVO -> ATIVO CIRCULANTE -> CRÉDITOS A CURTO PRAZO
        		        SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlAtivoCircuCrediCurtoPrazo'
								 AND contabil.exercicio = '".$this->getDado('exercicio')."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
				       	) AS vl_ativo_circ_cred_curto_prazo_exercicio_atual,
				    	SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlAtivoCircuCrediCurtoPrazo'
								 AND contabil.exercicio = '".(intval($this->getDado('exercicio')) - 1)."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
					    ) AS vl_ativo_circ_cred_curto_prazo_exercicio_anterior,
        		        -- ATIVO -> ATIVO CIRCULANTE -> INVESTIMENTOS E APLICAÇÕES TEMPORÁRIAS A CURTO PRAZO
        		        SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlAtivoCircuInvestAplicacaoCurtoPrazo'
								 AND contabil.exercicio = '".$this->getDado('exercicio')."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
				       	) AS vl_ativo_circ_invest_aplic_temp_curto_prazo_exercicio_atual,
				    	SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlAtivoCircuInvestAplicacaoCurtoPrazo'
								 AND contabil.exercicio = '".(intval($this->getDado('exercicio')) - 1)."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
					    ) AS vl_ativo_circ_invest_aplic_temp_curto_prazo_exercicio_anterior,
        		        -- ATIVO -> ATIVO CIRCULANTE -> ESTOQUES
        		        SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlAtivoCircuEstoques'
								 AND contabil.exercicio = '".$this->getDado('exercicio')."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
				       	) AS vl_ativo_circ_estoque_exercicio_atual,
				    	SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlAtivoCircuEstoques'
								 AND contabil.exercicio = '".(intval($this->getDado('exercicio')) - 1)."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
					    ) AS vl_ativo_circ_estoque_exercicio_anterior,
        		        -- ATIVO -> ATIVO CIRCULANTE -> VPD PAGAS ANTECIPADAMENTE
        		        SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlAtivoCircuVPDAntecipada'
								 AND contabil.exercicio = '".$this->getDado('exercicio')."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
				       	) AS vl_ativo_circ_vpd_paga_antecipado_exercicio_atual,
				    	SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlAtivoCircuVPDAntecipada'
								 AND contabil.exercicio = '".(intval($this->getDado('exercicio')) - 1)."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
					    ) AS vl_ativo_circ_vpd_paga_antecipado_exercicio_anterior,
        		        -- ATIVO -> ATIVO NÃO CIRCULANTE -> REALIZÁVEL A LONGO PRAZO
        		        SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlAtivoNaoCircuRealLongoPrazo'
								 AND contabil.exercicio = '".$this->getDado('exercicio')."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
				       	) AS vl_ativo_nao_circ_real_longo_prazo_exercicio_atual,
				    	SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlAtivoNaoCircuRealLongoPrazo'
								 AND contabil.exercicio = '".(intval($this->getDado('exercicio')) - 1)."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
					    ) AS vl_ativo_nao_circ_real_longo_prazo_exercicio_anterior,
        		        -- ATIVO -> ATIVO NÃO CIRCULANTE -> CRÉDITOS A LONGO PRAZO
        		        SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlAtivoNaoCircuCrediLongoPrazo'
								 AND contabil.exercicio = '".$this->getDado('exercicio')."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
				       	) AS vl_ativo_nao_circ_cred_longo_prazo_exercicio_atual,
				    	SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlAtivoNaoCircuCrediLongoPrazo'
								 AND contabil.exercicio = '".(intval($this->getDado('exercicio')) - 1)."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
					    ) AS vl_ativo_nao_circ_cred_longo_prazo_exercicio_anterior,
        		        -- ATIVO -> ATIVO NÃO CIRCULANTE -> INVESTIMENTOS TEMPORÁRIOS A LONGO PRAZO
        		        SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlAtivoNaoCircuInvesTempoLongoPrazo'
								 AND contabil.exercicio = '".$this->getDado('exercicio')."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
				       	) AS vl_ativo_nao_circ_invest_temp_longo_prazo_exercicio_atual,
				    	SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlAtivoNaoCircuInvesTempoLongoPrazo'
								 AND contabil.exercicio = '".(intval($this->getDado('exercicio')) - 1)."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
					    ) AS vl_ativo_nao_circ_invest_temp_longo_prazo_exercicio_anterior,
        		        -- ATIVO -> ATIVO NÃO CIRCULANTE -> ESTOQUES
        		        SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlAtivoNaoCircuEstoques'
								 AND contabil.exercicio = '".$this->getDado('exercicio')."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
				       	) AS vl_ativo_nao_circ_estoque_exercicio_atual,
				    	SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlAtivoNaoCircuEstoques'
								 AND contabil.exercicio = '".(intval($this->getDado('exercicio')) - 1)."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
					    ) AS vl_ativo_nao_circ_estoque_exercicio_anterior,
        		        -- ATIVO -> ATIVO NÃO CIRCULANTE -> VPD PAGAS ANTECIPADAMENTE
        		        SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlAtivoNaoCircuVPDAntecipada'
								 AND contabil.exercicio = '".$this->getDado('exercicio')."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
				       	) AS vl_ativo_nao_circ_vpd_pago_antecipado_exercicio_atual,
				    	SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlAtivoNaoCircuVPDAntecipada'
								 AND contabil.exercicio = '".(intval($this->getDado('exercicio')) - 1)."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
					    ) AS vl_ativo_nao_circ_vpd_pago_antecipado_exercicio_anterior,
        		        -- ATIVO -> ATIVO NÃO CIRCULANTE -> INVESTIMENTOS
        		        SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlAtivoNaoCircuInvestimentos'
								 AND contabil.exercicio = '".$this->getDado('exercicio')."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
				       	) AS vl_ativo_nao_circ_investimento_exercicio_atual,
				    	SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlAtivoNaoCircuInvestimentos'
								 AND contabil.exercicio = '".(intval($this->getDado('exercicio')) - 1)."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
					    ) AS vl_ativo_nao_circ_investimento_exercicio_anterior,
        		        -- ATIVO -> ATIVO NÃO CIRCULANTE -> IMOBILIZADO
        		        SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlAtivoNaoCircuImobilizado'
								 AND contabil.exercicio = '".$this->getDado('exercicio')."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
				       	) AS vl_ativo_nao_circ_imobilizado_exercicio_atual,
				    	SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlAtivoNaoCircuImobilizado'
								 AND contabil.exercicio = '".(intval($this->getDado('exercicio')) - 1)."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
					    ) AS vl_ativo_nao_circ_imobilizado_exercicio_anterior,
        		        -- ATIVO -> ATIVO NÃO CIRCULANTE -> INTANGÍVEL
        		        SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlAtivoNaoCircuIntagivel'
								 AND contabil.exercicio = '".$this->getDado('exercicio')."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
				       	) AS vl_ativo_nao_circ_intangivel_exercicio_atual,
				    	SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlAtivoNaoCircuIntagivel'
								 AND contabil.exercicio = '".(intval($this->getDado('exercicio')) - 1)."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
					    ) AS vl_ativo_nao_circ_intangivel_exercicio_anterior

				  FROM  tcemg.configuracao_dcasp_registros
				  
				  JOIN  tcemg.configuracao_dcasp_arquivo using (seq_arquivo)

				  ".$this->montaConsultaContabil()."
				    ON  REPLACE(configuracao_dcasp_registros.conta_contabil, '.', '') = contabil.conta

	             WHERE  configuracao_dcasp_arquivo.nome_arquivo_pertencente = 'BP'
				   AND  configuracao_dcasp_registros.exercicio = '".$this->getDado('exercicio')."'
				   AND  configuracao_dcasp_registros.tipo_registro = 10
				 GROUP  BY configuracao_dcasp_arquivo.nome_arquivo_pertencente
        	";
        }
        
        public function montaRecuperaDadosBalancoPatrimonial20()
        {
        	return "
        		SELECT 	-- PASSIVO E PATRIMÔNIO LÍQUIDO -> PASSIVO CIRCULANTE -> OBRIGAÇÕES TRAB., PREV. E ASSISTENCIAIS A PAGAR A CURTO PRAZO
        				SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlPassivoCirculaTrabPreviCurtoPrazo'
								 AND contabil.exercicio = '".$this->getDado('exercicio')."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
				       	) AS vl_pass_circ_obrig_trab_previdenc_assist_pagar_curto_prazo_exercicio_atual,
				    	SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlPassivoCirculaTrabPreviCurtoPrazo'
								 AND contabil.exercicio = '".(intval($this->getDado('exercicio')) - 1)."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
					    ) AS vl_pass_circ_obrig_trab_previdenc_assist_pagar_curto_prazo_exercicio_anterior,
        		     	-- PASSIVO E PATRIMÔNIO LÍQUIDO -> PASSIVO CIRCULANTE -> EMPRÉSTIMOS E FINANCIAMENTOS A CURTO PRAZO
        				SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlPassivoCirculaEmpreFinanCurtoPrazo'
								 AND contabil.exercicio = '".$this->getDado('exercicio')."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
				       	) AS vl_pass_circ_emprest_financ_curto_prazo_exercicio_atual,
				    	SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlPassivoCirculaEmpreFinanCurtoPrazo'
								 AND contabil.exercicio = '".(intval($this->getDado('exercicio')) - 1)."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
					    ) AS vl_pass_circ_emprest_financ_curto_prazo_exercicio_anterior,
        		     	-- PASSIVO E PATRIMÔNIO LÍQUIDO -> PASSIVO CIRCULANTE -> FORNECEDORES E CONTAS A PAGAR A CURTO PRAZO
        				SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlPassivoCirculaFornecedoresCurtoPrazo'
								 AND contabil.exercicio = '".$this->getDado('exercicio')."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
				       	) AS vl_pass_circ_fornec_contas_pagar_curto_prazo_exercicio_atual,
				    	SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlPassivoCirculaFornecedoresCurtoPrazo'
								 AND contabil.exercicio = '".(intval($this->getDado('exercicio')) - 1)."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
					    ) AS vl_pass_circ_fornec_contas_pagar_curto_prazo_exercicio_anterior,
        		     	-- PASSIVO E PATRIMÔNIO LÍQUIDO -> PASSIVO CIRCULANTE -> OBRIGAÇÕES FISCAIS A CURTO PRAZO
        				SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlPassivoCirculaObrigaFiscaisCurtoPrazo'
								 AND contabil.exercicio = '".$this->getDado('exercicio')."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
				       	) AS vl_pass_circ_obrig_fiscais_curto_prazo_exercicio_atual,
				    	SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlPassivoCirculaObrigaFiscaisCurtoPrazo'
								 AND contabil.exercicio = '".(intval($this->getDado('exercicio')) - 1)."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
					    ) AS vl_pass_circ_obrig_fiscais_curto_prazo_exercicio_anterior,
        		     	-- PASSIVO E PATRIMÔNIO LÍQUIDO -> PASSIVO CIRCULANTE -> OBRIGAÇÕES DE REPARTIÇÕES A OUTROS ENTES
        				SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlPassivoCirculaObrigacoesOutrosEntes'
								 AND contabil.exercicio = '".$this->getDado('exercicio')."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
				       	) AS vl_pass_circ_obrig_repart_outros_entes_exercicio_atual,
				    	SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlPassivoCirculaObrigacoesOutrosEntes'
								 AND contabil.exercicio = '".(intval($this->getDado('exercicio')) - 1)."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
					    ) AS vl_pass_circ_obrig_repart_outros_entes_exercicio_anterior,
        		     	-- PASSIVO E PATRIMÔNIO LÍQUIDO -> PASSIVO CIRCULANTE -> PROVISÕES A CURTO PRAZO
        				SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlPassivoCirculaProvisoesCurtoPrazo'
								 AND contabil.exercicio = '".$this->getDado('exercicio')."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
				       	) AS vl_pass_circ_provisoes_curto_prazo_exercicio_atual,
				    	SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlPassivoCirculaProvisoesCurtoPrazo'
								 AND contabil.exercicio = '".(intval($this->getDado('exercicio')) - 1)."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
					    ) AS vl_pass_circ_provisoes_curto_prazo_exercicio_anterior,
        		     	-- PASSIVO E PATRIMÔNIO LÍQUIDO -> PASSIVO CIRCULANTE -> DEMAIS OBRIGAÇÕES A CURTO PRAZO
        				SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlPassivoCirculaDemaisObrigaCurtoPrazo'
								 AND contabil.exercicio = '".$this->getDado('exercicio')."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
				       	) AS vl_pass_circ_dms_obrigacoes_curto_prazo_exercicio_atual,
				    	SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlPassivoCirculaDemaisObrigaCurtoPrazo'
								 AND contabil.exercicio = '".(intval($this->getDado('exercicio')) - 1)."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
					    ) AS vl_pass_circ_dms_obrigacoes_curto_prazo_exercicio_anterior,
        		     	-- PASSIVO E PATRIMÔNIO LÍQUIDO -> PASSIVO NÃO CIRCULANTE -> OBRIGAÇÕES TRAB., PREV. E ASSISTENCIAIS A PAGAR A LONGO PRAZO
        				SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlPassivoNaoCirculaTrabPreviLongoPrazo'
								 AND contabil.exercicio = '".$this->getDado('exercicio')."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
				       	) AS vl_pass_nao_circ_obrig_trab_prev_assist_pagar_longro_prazo_exercicio_atual,
				    	SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlPassivoNaoCirculaTrabPreviLongoPrazo'
								 AND contabil.exercicio = '".(intval($this->getDado('exercicio')) - 1)."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
					    ) AS vl_pass_nao_circ_obrig_trab_prev_assist_pagar_longro_prazo_exercicio_anterior,
        		     	-- PASSIVO E PATRIMÔNIO LÍQUIDO -> PASSIVO NÃO CIRCULANTE -> EMPRÉSTIMOS E FINANCIAMENTOS A LONGO PRAZO
        				SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlPassivoNaoCirculaEmpreFinanLongoPrazo'
								 AND contabil.exercicio = '".$this->getDado('exercicio')."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
				       	) AS vl_pass_nao_circ_emp_financ_longo_prazo_exercicio_atual,
				    	SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlPassivoNaoCirculaEmpreFinanLongoPrazo'
								 AND contabil.exercicio = '".(intval($this->getDado('exercicio')) - 1)."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
					    ) AS vl_pass_nao_circ_emp_financ_longo_prazo_exercicio_anterior,
        		     	-- PASSIVO E PATRIMÔNIO LÍQUIDO -> PASSIVO NÃO CIRCULANTE -> FORNECEDORES E CONTAS A PAGAR A LONGO PRAZO
        				SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlPassivoNaoCirculaFornecedoresLongoPrazo'
								 AND contabil.exercicio = '".$this->getDado('exercicio')."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
				       	) AS vl_pass_nao_circ_fornec_contas_pagar_longo_prazo_exercicio_atual,
				    	SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlPassivoNaoCirculaFornecedoresLongoPrazo'
								 AND contabil.exercicio = '".(intval($this->getDado('exercicio')) - 1)."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
					    ) AS vl_pass_nao_circ_fornec_contas_pagar_longo_prazo_exercicio_anterior,
        		     	-- PASSIVO E PATRIMÔNIO LÍQUIDO -> PASSIVO NÃO CIRCULANTE -> OBRIGAÇÕES FISCAIS A LONGO PRAZO
        				SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlPassivoNaoCirculaObrigaFiscaisLongoPrazo'
								 AND contabil.exercicio = '".$this->getDado('exercicio')."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
				       	) AS vl_pass_nao_circ_obrig_fisc_longo_prazo_exercicio_atual,
				    	SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlPassivoNaoCirculaObrigaFiscaisLongoPrazo'
								 AND contabil.exercicio = '".(intval($this->getDado('exercicio')) - 1)."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
					    ) AS vl_pass_nao_circ_obrig_fisc_longo_prazo_exercicio_anterior,
        		     	-- PASSIVO E PATRIMÔNIO LÍQUIDO -> PASSIVO NÃO CIRCULANTE -> PROVISÕES A LONGO PRAZO
        				SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlPassivoNaoCirculaProvisoesLongoPrazo'
								 AND contabil.exercicio = '".$this->getDado('exercicio')."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
				       	) AS vl_pass_nao_circ_provisoes_longo_prazo_exercicio_atual,
				    	SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlPassivoNaoCirculaProvisoesLongoPrazo'
								 AND contabil.exercicio = '".(intval($this->getDado('exercicio')) - 1)."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
					    ) AS vl_pass_nao_circ_provisoes_longo_prazo_exercicio_anterior,
        		     	-- PASSIVO E PATRIMÔNIO LÍQUIDO -> PASSIVO NÃO CIRCULANTE -> DEMAIS OBRIGAÇÕES A LONGO PRAZO
        				SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlPassivoNaoCirculaDemaisObrigaLongoPrazo'
								 AND contabil.exercicio = '".$this->getDado('exercicio')."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
				       	) AS vl_pass_nao_circ_dms_obrig_longo_prazo_exercicio_atual,
				    	SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlPassivoNaoCirculaDemaisObrigaLongoPrazo'
								 AND contabil.exercicio = '".(intval($this->getDado('exercicio')) - 1)."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
					    ) AS vl_pass_nao_circ_dms_obrig_longo_prazo_exercicio_anterior,
        		     	-- PASSIVO E PATRIMÔNIO LÍQUIDO -> PASSIVO NÃO CIRCULANTE -> RESULTADO DIFERIDO
        				SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlPassivoNaoCirculaResulDiferido'
								 AND contabil.exercicio = '".$this->getDado('exercicio')."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
				       	) AS vl_pass_nao_circ_resultado_deferido_exercicio_atual,
				    	SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlPassivoNaoCirculaResulDiferido'
								 AND contabil.exercicio = '".(intval($this->getDado('exercicio')) - 1)."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
					    ) AS vl_pass_nao_circ_resultado_deferido_exercicio_anterior,
        		     	-- PASSIVO E PATRIMÔNIO LÍQUIDO -> PATRIMÔNIO LÍQUIDO -> PATRIMÔNIO SOCIAL E CAPITAL SOCIAL
        				SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlPatriLiquidoCapitalSocial'
								 AND contabil.exercicio = '".$this->getDado('exercicio')."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
				       	) AS vl_patri_liq_patr_social_capital_social_exercicio_atual,
				    	SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlPatriLiquidoCapitalSocial'
								 AND contabil.exercicio = '".(intval($this->getDado('exercicio')) - 1)."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
					    ) AS vl_patri_liq_patr_social_capital_social_exercicio_anterior,
        		     	-- PASSIVO E PATRIMÔNIO LÍQUIDO -> PATRIMÔNIO LÍQUIDO -> ADIANTAMENTO PARA FUTURO AUMENTO DE CAPITAL
        				SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlPatriLiquidoAdianFuturoCapital'
								 AND contabil.exercicio = '".$this->getDado('exercicio')."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
				       	) AS vl_patri_liq_adiant_futuro_aumento_capital_exercicio_atual,
				    	SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlPatriLiquidoAdianFuturoCapital'
								 AND contabil.exercicio = '".(intval($this->getDado('exercicio')) - 1)."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
					    ) AS vl_patri_liq_adiant_futuro_aumento_capital_exercicio_anterior,
        		     	-- PASSIVO E PATRIMÔNIO LÍQUIDO -> PATRIMÔNIO LÍQUIDO -> RESERVAS DE CAPITAL
        				SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlPatriLiquidoReservaCapital'
								 AND contabil.exercicio = '".$this->getDado('exercicio')."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
				       	) AS vl_patri_liq_reservas_capital_exercicio_atual,
				    	SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlPatriLiquidoReservaCapital'
								 AND contabil.exercicio = '".(intval($this->getDado('exercicio')) - 1)."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
					    ) AS vl_patri_liq_reservas_capital_exercicio_anterior,
        		     	-- PASSIVO E PATRIMÔNIO LÍQUIDO -> PATRIMÔNIO LÍQUIDO -> AJUSTES DE AVALIAÇÃO PATRIMONIAL
        				SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlPatriLiquidoAjustAvaliacao'
								 AND contabil.exercicio = '".$this->getDado('exercicio')."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
				       	) AS vl_patri_liq_ajuste_aval_patrimonial_exercicio_atual,
				    	SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlPatriLiquidoAjustAvaliacao'
								 AND contabil.exercicio = '".(intval($this->getDado('exercicio')) - 1)."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
					    ) AS vl_patri_liq_ajuste_aval_patrimonial_exercicio_anterior,
        		     	-- PASSIVO E PATRIMÔNIO LÍQUIDO -> PATRIMÔNIO LÍQUIDO -> RESERVAS DE LUCROS
        				SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlPatriLiquidoReservaLucros'
								 AND contabil.exercicio = '".$this->getDado('exercicio')."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
				       	) AS vl_patri_liq_reservas_lucros_exercicio_atual,
				    	SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlPatriLiquidoReservaLucros'
								 AND contabil.exercicio = '".(intval($this->getDado('exercicio')) - 1)."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
					    ) AS vl_patri_liq_reservas_lucros_exercicio_anterior,
        		     	-- PASSIVO E PATRIMÔNIO LÍQUIDO -> PATRIMÔNIO LÍQUIDO -> DEMAIS RESERVAS
        				SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlPatriLiquidoDemaisReservas'
								 AND contabil.exercicio = '".$this->getDado('exercicio')."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
				       	) AS vl_patri_liq_demais_reservas_exercicio_atual,
				    	SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlPatriLiquidoDemaisReservas'
								 AND contabil.exercicio = '".(intval($this->getDado('exercicio')) - 1)."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
					    ) AS vl_patri_liq_demais_reservas_exercicio_anterior,
        		     	-- PASSIVO E PATRIMÔNIO LÍQUIDO -> PATRIMÔNIO LÍQUIDO -> RESULTADOS ACUMULADOS
        				SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlPatriLiquidoResultExercicio'
								 AND contabil.exercicio = '".$this->getDado('exercicio')."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
				       	) AS vl_patri_liq_resultado_exercicio_exercicio_atual,
				    	SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlPatriLiquidoResultExercicio'
								 AND contabil.exercicio = '".(intval($this->getDado('exercicio')) - 1)."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
					    ) AS vl_patri_liq_resultado_exercicio_exercicio_anterior,
        		     	-- PASSIVO E PATRIMÔNIO LÍQUIDO -> PATRIMÔNIO LÍQUIDO -> (-) AÇÕES / COTAS EM TESOURARIA
        				SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlPatriLiquidoAcoesCotas'
								 AND contabil.exercicio = '".$this->getDado('exercicio')."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
				       	) AS vl_patri_liq_acoes_cotas_tesouraria_exercicio_atual,
				    	SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlPatriLiquidoAcoesCotas'
								 AND contabil.exercicio = '".(intval($this->getDado('exercicio')) - 1)."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
					    ) AS vl_patri_liq_acoes_cotas_tesouraria_exercicio_anterior

				  FROM  tcemg.configuracao_dcasp_registros
				  
				  JOIN  tcemg.configuracao_dcasp_arquivo using (seq_arquivo)

				  ".$this->montaConsultaContabil()."

				    ON  REPLACE(configuracao_dcasp_registros.conta_contabil, '.', '') = contabil.conta

				 WHERE  configuracao_dcasp_arquivo.nome_arquivo_pertencente = 'BP'
				   AND  configuracao_dcasp_registros.exercicio = '".$this->getDado('exercicio')."'
				   AND  configuracao_dcasp_registros.tipo_registro = 20
				 GROUP  BY configuracao_dcasp_arquivo.nome_arquivo_pertencente
        	";
        }

        public function montaRecuperaDadosBalancoPatrimonial30()
        {
        	return "
    			SELECT 	-- PASSIVOS FINANCEIROS E PERMANENTES -> ATIVO -> ATIVO FINANCEIRO
						SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlAtivoFinanceiro'
								 AND contabil.exercicio = '".$this->getDado('exercicio')."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
						) AS vl_ativo_financeiro_exercicio_atual,
						SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlAtivoFinanceiro'
								 AND contabil.exercicio = '".(intval($this->getDado('exercicio')) - 1)."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
						) AS vl_ativo_financeiro_exercicio_anterior,
				        -- PASSIVOS FINANCEIROS E PERMANENTES -> ATIVO -> ATIVO PERMANENTE
						SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlAtivoPermanente'
								 AND contabil.exercicio = '".$this->getDado('exercicio')."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
						) AS vl_ativo_permanente_exercicio_atual,
						SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlAtivoPermanente'
								 AND contabil.exercicio = '".(intval($this->getDado('exercicio')) - 1)."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
						) AS vl_ativo_permanente_exercicio_anterior

				  FROM  tcemg.configuracao_dcasp_registros
				  JOIN  tcemg.configuracao_dcasp_arquivo using (seq_arquivo)

				  ".$this->montaConsultaContabil()."
				   ON REPLACE(configuracao_dcasp_registros.conta_contabil, '.', '') = contabil.conta

				WHERE configuracao_dcasp_arquivo.nome_arquivo_pertencente = 'BP'
				  AND configuracao_dcasp_registros.exercicio = '".$this->getDado('exercicio')."'
				  AND configuracao_dcasp_registros.tipo_registro = 30
				GROUP BY configuracao_dcasp_arquivo.nome_arquivo_pertencente
        	";
        }

        public function montaRecuperaDadosBalancoPatrimonial40()
        {
        	return "
    			SELECT 	-- PASSIVOS FINANCEIROS E PERMANENTES -> PASSIVO -> PASSIVO FINANCEIRO
						SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlPassivoFinanceiro'
								 AND contabil.exercicio = '".$this->getDado('exercicio')."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
						) AS vl_passivo_financeiro_exercicio_atual,
						SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlPassivoFinanceiro'
								 AND contabil.exercicio = '".(intval($this->getDado('exercicio')) - 1)."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
						) AS vl_passivo_financeiro_exercicio_anterior,
				        -- PASSIVOS FINANCEIROS E PERMANENTES -> PASSIVO -> PASSIVO PERMANENTE
						SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlPassivoPermanente'
								 AND contabil.exercicio = '".$this->getDado('exercicio')."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
						) AS vl_passivo_permanente_exercicio_atual,
						SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlPassivoPermanente'
								 AND contabil.exercicio = '".(intval($this->getDado('exercicio')) - 1)."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
						) AS vl_passivo_permanente_exercicio_anterior

				  FROM  tcemg.configuracao_dcasp_registros
				  JOIN  tcemg.configuracao_dcasp_arquivo using (seq_arquivo)

				  ".$this->montaConsultaContabil()."
				   ON REPLACE(configuracao_dcasp_registros.conta_contabil, '.', '') = contabil.conta

				WHERE configuracao_dcasp_arquivo.nome_arquivo_pertencente = 'BP'
				  AND configuracao_dcasp_registros.exercicio = '".$this->getDado('exercicio')."'
				  AND configuracao_dcasp_registros.tipo_registro = 40
				GROUP BY configuracao_dcasp_arquivo.nome_arquivo_pertencente
        	";
        }

        public function montaRecuperaDadosBalancoPatrimonial60()
        {
        	return "
    			SELECT 	-- QUADRO DAS CONTAS DE COMPENSAÇÃO -> ATOS POTENCIAIS ATIVOS -> GARANTIAS E CONTRAGARANTIAS RECEBIDAS
						SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlAtosPotenAtivosGaranContragaRecebida'
								 AND contabil.exercicio = '".$this->getDado('exercicio')."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
						) AS vl_ato_poten_ativ_garan_contragaran_recebida_exercicio_atual,
						SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlAtosPotenAtivosGaranContragaRecebida'
								 AND contabil.exercicio = '".(intval($this->getDado('exercicio')) - 1)."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
						) AS vl_ato_poten_ativ_garan_contragaran_recebida_exercicio_anterior,
				        -- QUADRO DAS CONTAS DE COMPENSAÇÃO -> ATOS POTENCIAIS ATIVOS -> DIREITOS CONVENIADOS E OURTOS INSTRUMENTOS CONGÊNERES
						SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlAtosPotenAtivosDirConveOutrosInstrumentos'
								 AND contabil.exercicio = '".$this->getDado('exercicio')."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
						) AS vl_ato_poten_ativ_dir_conven_outros_instru_congeneres_exercicio_atual,
						SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlAtosPotenAtivosDirConveOutrosInstrumentos'
								 AND contabil.exercicio = '".(intval($this->getDado('exercicio')) - 1)."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
						) AS vl_ato_poten_ativ_dir_conven_outros_instru_congeneres_exercicio_anterior,
				        -- QUADRO DAS CONTAS DE COMPENSAÇÃO -> ATOS POTENCIAIS ATIVOS -> DIREITOS CONTRATUAIS
						SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlAtosPotenAtivosDireitosContratuais'
								 AND contabil.exercicio = '".$this->getDado('exercicio')."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
						) AS vl_ato_poten_ativ_direitos_contratuais_exercicio_atual,
						SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlAtosPotenAtivosDireitosContratuais'
								 AND contabil.exercicio = '".(intval($this->getDado('exercicio')) - 1)."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
						) AS vl_ato_poten_ativ_direitos_contratuais_exercicio_anterior,
				        -- QUADRO DAS CONTAS DE COMPENSAÇÃO -> ATOS POTENCIAIS ATIVOS -> OUTROS ATOS POTENCIAIS ATIVOS
						SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlAtosPotenAtivosOutrosAtos'
								 AND contabil.exercicio = '".$this->getDado('exercicio')."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
						) AS vl_ato_poten_ativ_outros_atos_potenc_ativo_exercicio_atual,
						SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlAtosPotenAtivosOutrosAtos'
								 AND contabil.exercicio = '".(intval($this->getDado('exercicio')) - 1)."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
						) AS vl_ato_poten_ativ_outros_atos_potenc_ativo_exercicio_anterior,
				        -- QUADRO DAS CONTAS DE COMPENSAÇÃO -> ATOS POTENCIAIS PASSIVOS -> GARANTIAS E CONTRAGARANTIAS CONCEDIDAS
						SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlAtosPotenPassivoGaranContragaConcedida'
								 AND contabil.exercicio = '".$this->getDado('exercicio')."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
						) AS vl_ato_poten_pass_garan_contragaran_concedida_exercicio_atual,
						SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlAtosPotenPassivoGaranContragaConcedida'
								 AND contabil.exercicio = '".(intval($this->getDado('exercicio')) - 1)."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
						) AS vl_ato_poten_pass_garan_contragaran_concedida_exercicio_anterior,
				        -- QUADRO DAS CONTAS DE COMPENSAÇÃO -> ATOS POTENCIAIS PASSIVOS -> OBRIGAÇÕES CONVENIADAS E OUTROS INSTRUMENTOS CONGÊNERES
						SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlAtosPotenPassivoObrigConveOutrosInstrumentos'
								 AND contabil.exercicio = '".$this->getDado('exercicio')."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
						) AS vl_ato_poten_pass_obrig_conven_outros_instru_congeneres_exercicio_atual,
						SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlAtosPotenPassivoObrigConveOutrosInstrumentos'
								 AND contabil.exercicio = '".(intval($this->getDado('exercicio')) - 1)."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
						) AS vl_ato_poten_pass_obrig_conven_outros_instru_congeneres_exercicio_anterior,
				        -- QUADRO DAS CONTAS DE COMPENSAÇÃO -> ATOS POTENCIAIS PASSIVOS -> OBRIGAÇÕES CONTRATUAIS
						SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlAtosPotenPassivoObrigacoesContratuais'
								 AND contabil.exercicio = '".$this->getDado('exercicio')."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
						) AS vl_ato_poten_pass_obrigacoes_contratuais_exercicio_atual,
						SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlAtosPotenPassivoObrigacoesContratuais'
								 AND contabil.exercicio = '".(intval($this->getDado('exercicio')) - 1)."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
						) AS vl_ato_poten_pass_obrigacoes_contratuais_exercicio_anterior,
				        -- QUADRO DAS CONTAS DE COMPENSAÇÃO -> ATOS POTENCIAIS PASSIVOS -> OUTROS ATOS POTENCIAIS PASSIVOS
						SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlAtosPotenPassivoOutrosAtos'
								 AND contabil.exercicio = '".$this->getDado('exercicio')."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
						) AS vl_ato_poten_pass_outros_atos_potenc_passivo_exercicio_atual,
						SUM (
						    CASE
								WHEN configuracao_dcasp_arquivo.nome_tag = 'vlAtosPotenPassivoOutrosAtos'
								 AND contabil.exercicio = '".(intval($this->getDado('exercicio')) - 1)."'
								THEN COALESCE(contabil.valor, 0.00)
								ELSE 0.00
						    END
						) AS vl_ato_poten_pass_outros_atos_potenc_passivo_exercicio_anterior

				  FROM  tcemg.configuracao_dcasp_registros
				  JOIN  tcemg.configuracao_dcasp_arquivo using (seq_arquivo)

				  ".$this->montaConsultaContabil()."
				   ON REPLACE(configuracao_dcasp_registros.conta_contabil, '.', '') = contabil.conta

				WHERE configuracao_dcasp_arquivo.nome_arquivo_pertencente = 'BP'
				  AND configuracao_dcasp_registros.exercicio = '".$this->getDado('exercicio')."'
				  AND configuracao_dcasp_registros.tipo_registro = 60
				GROUP BY configuracao_dcasp_arquivo.nome_arquivo_pertencente
        	";
        }

        private function montaConsultaContabil()
        {
        	return "
			  	LEFT JOIN 
			  	(
					SELECT CASE
								WHEN valor_lancamento.tipo_valor = 'C'
							    THEN REPLACE(plano_conta_credito.cod_estrutural, '.', '')
								ELSE REPLACE(plano_conta_debito.cod_estrutural, '.', '')
						    END AS conta,
							lancamento.exercicio,
							lancamento.cod_entidade,
							valor_lancamento.vl_lancamento as valor
					   
					   FROM contabilidade.lancamento

					   JOIN contabilidade.lote
						 ON lote.exercicio = lancamento.exercicio
						AND lote.cod_entidade = lancamento.cod_entidade
						AND lote.tipo = lancamento.tipo
						AND lote.cod_lote = lancamento.cod_lote
						
					   JOIN contabilidade.valor_lancamento 
						 ON valor_lancamento.exercicio = lancamento.exercicio
						AND valor_lancamento.cod_entidade = lancamento.cod_entidade
						AND valor_lancamento.tipo = lancamento.tipo
						AND valor_lancamento.cod_lote = lancamento.cod_lote
						AND valor_lancamento.sequencia = lancamento.sequencia
						 
					   LEFT JOIN contabilidade.conta_credito 
						 ON conta_credito.cod_lote = valor_lancamento.cod_lote
						AND conta_credito.tipo = valor_lancamento.tipo
						AND conta_credito.sequencia = valor_lancamento.sequencia
						AND conta_credito.exercicio = valor_lancamento.exercicio
						AND conta_credito.tipo_valor = valor_lancamento.tipo_valor
						AND conta_credito.cod_entidade = valor_lancamento.cod_entidade
						  
					   LEFT JOIN contabilidade.conta_debito 
					     ON conta_debito.cod_lote = valor_lancamento.cod_lote
						AND conta_debito.tipo = valor_lancamento.tipo
						AND conta_debito.sequencia = valor_lancamento.sequencia
						AND conta_debito.exercicio = valor_lancamento.exercicio
						AND conta_debito.tipo_valor = valor_lancamento.tipo_valor
						AND conta_debito.cod_entidade = valor_lancamento.cod_entidade

					   LEFT JOIN contabilidade.plano_analitica AS plano_analitica_credito 
					     ON plano_analitica_credito.exercicio = conta_credito.exercicio
						AND plano_analitica_credito.cod_plano = conta_credito.cod_plano
						 
					   LEFT JOIN contabilidade.plano_conta AS plano_conta_credito 
						 ON plano_conta_credito.exercicio = plano_analitica_credito.exercicio
						AND plano_conta_credito.cod_conta = plano_analitica_credito.cod_conta
								      
					   LEFT JOIN contabilidade.plano_analitica AS plano_analitica_debito 
						 ON plano_analitica_debito.exercicio = conta_debito.exercicio
					    AND plano_analitica_debito.cod_plano = conta_debito.cod_plano
								      
					   LEFT JOIN contabilidade.plano_conta AS plano_conta_debito 
						 ON plano_conta_debito.exercicio = plano_analitica_debito.exercicio
						AND plano_conta_debito.cod_conta = plano_analitica_debito.cod_conta

					  WHERE (lancamento.exercicio = '".(intval($this->getDado('exercicio')) - 1)."' 
					  	 OR lote.dt_lote BETWEEN '".$this->getDado('dtInicial')."' AND '".$this->getDado('dtFinal')."')
					  	AND lancamento.cod_entidade IN (".$this->getDado('entidades').")
				      ORDER BY valor_lancamento.vl_lancamento
				) AS contabil 
        	";
        }


        public function montaRecuperaDadosBalancoPatrimonial71()
        {
        	return "
        		SELECT  recurso.cod_recurso, 
        				TRIM(recurso.nom_recurso) AS nom_recurso, 
        				SUM(
        					CASE
        						WHEN contabil.exercicio = '".$this->getDado('exercicio')."'
        						THEN contabil.valor
        						ELSE 0.00
        					END
        				) AS exercicio_atual,
        				SUM(
        					CASE
        						WHEN contabil.exercicio = '".(intval($this->getDado('exercicio')) - 1)."'
        						THEN contabil.valor
        						ELSE 0.00
        					END
    					) AS exercicio_anterior
        				
				  FROM  orcamento.recurso
				  LEFT  JOIN 
						(
							 SELECT CASE
									    WHEN valor_lancamento.tipo_valor = 'C'
									    THEN REPLACE(plano_conta_credito.cod_estrutural, '.', '')
									    ELSE REPLACE(plano_conta_debito.cod_estrutural, '.', '')
							        END AS conta,
							        CASE
									    WHEN valor_lancamento.tipo_valor = 'C'
									    THEN plano_conta_recurso_credito.cod_recurso
									    ELSE plano_conta_recurso_debito.cod_recurso
								    END AS cod_recurso,
							        lancamento.exercicio,
							        lancamento.cod_entidade,
							        valor_lancamento.vl_lancamento as valor
							   
							   FROM contabilidade.lancamento

							   JOIN contabilidade.lote
								 ON lote.exercicio = lancamento.exercicio
								AND lote.cod_entidade = lancamento.cod_entidade
								AND lote.tipo = lancamento.tipo
								AND lote.cod_lote = lancamento.cod_lote
								
							   JOIN contabilidade.valor_lancamento 
								 ON valor_lancamento.exercicio = lancamento.exercicio
								AND valor_lancamento.cod_entidade = lancamento.cod_entidade
								AND valor_lancamento.tipo = lancamento.tipo
								AND valor_lancamento.cod_lote = lancamento.cod_lote
								AND valor_lancamento.sequencia = lancamento.sequencia
								 
							   LEFT JOIN contabilidade.conta_credito 
								 ON conta_credito.cod_lote = valor_lancamento.cod_lote
								AND conta_credito.tipo = valor_lancamento.tipo
								AND conta_credito.sequencia = valor_lancamento.sequencia
								AND conta_credito.exercicio = valor_lancamento.exercicio
								AND conta_credito.tipo_valor = valor_lancamento.tipo_valor
								AND conta_credito.cod_entidade = valor_lancamento.cod_entidade
		  
							   LEFT JOIN contabilidade.conta_debito 
								 ON conta_debito.cod_lote = valor_lancamento.cod_lote
								AND conta_debito.tipo = valor_lancamento.tipo
								AND conta_debito.sequencia = valor_lancamento.sequencia
								AND conta_debito.exercicio = valor_lancamento.exercicio
								AND conta_debito.tipo_valor = valor_lancamento.tipo_valor
								AND conta_debito.cod_entidade = valor_lancamento.cod_entidade

							   LEFT JOIN contabilidade.plano_analitica AS plano_analitica_credito 
								 ON plano_analitica_credito.exercicio = conta_credito.exercicio
								AND plano_analitica_credito.cod_plano = conta_credito.cod_plano
								 
							   LEFT JOIN contabilidade.plano_conta AS plano_conta_credito 
								 ON plano_conta_credito.exercicio = plano_analitica_credito.exercicio
								AND plano_conta_credito.cod_conta = plano_analitica_credito.cod_conta

							   LEFT JOIN contabilidade.plano_recurso AS plano_conta_recurso_credito
								 ON plano_conta_recurso_credito.cod_plano = plano_analitica_credito.cod_plano
								AND plano_conta_recurso_credito.exercicio = plano_analitica_credito.exercicio

							   LEFT JOIN contabilidade.plano_analitica AS plano_analitica_debito 
								 ON plano_analitica_debito.exercicio = conta_debito.exercicio
								AND plano_analitica_debito.cod_plano = conta_debito.cod_plano
										      
							   LEFT JOIN contabilidade.plano_conta AS plano_conta_debito 
								 ON plano_conta_debito.exercicio = plano_analitica_debito.exercicio
								AND plano_conta_debito.cod_conta = plano_analitica_debito.cod_conta

 							   LEFT JOIN contabilidade.plano_recurso AS plano_conta_recurso_debito
								 ON plano_conta_recurso_debito.cod_plano = plano_analitica_debito.cod_plano
								AND plano_conta_recurso_debito.exercicio = plano_analitica_debito.exercicio
								    
						      WHERE (lancamento.exercicio = '".(intval($this->getDado('exercicio')) - 1)."' 
						      	 OR lote.dt_lote BETWEEN '".$this->getDado('dtInicial')."' AND '".$this->getDado('dtFinal')."')
								AND lancamento.cod_entidade IN (".$this->getDado('entidades').")
							  ORDER BY valor_lancamento.vl_lancamento
						) AS contabil 

					 ON recurso.exercicio = '2017'
					AND contabil.cod_recurso = recurso.cod_recurso

				GROUP BY recurso.cod_recurso, recurso.nom_recurso
				ORDER BY recurso.cod_recurso
        	";
        }
    }

?>