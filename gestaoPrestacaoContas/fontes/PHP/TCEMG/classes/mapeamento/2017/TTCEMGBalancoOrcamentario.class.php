<?php

	class TTCEMGBalancoOrcamentario
	{
	    public function TTCEMGBalancoOrcamentario()
	    {
	        $this->setDado('exercicio', Sessao::getExercicio() );
	    }

	    public function recuperaExportacao($metodo, &$rsRecordSet, $stFiltro = "", $stOrder = "", $boTransacao = "")
	    {
	    	return $this->executaRecupera($metodo, $rsRecordSet, $stFiltro, $stOrder, $boTransacao);
	    }

	    private function montaRecuperaExportacao10()
	    {
	    	$stSql  = "
	    		SELECT  10 as tipo_registro, 
        				SUM(
						    CASE WHEN configuracao_dcasp_arquivo.nome_tag = 'vlRecTributaria'
								 THEN receita_orcamentaria.valor
							 	 ELSE 0
						     END
				        ) as vl_rec_tributaria,

				        SUM(
						   	CASE WHEN configuracao_dcasp_arquivo.nome_tag = 'vlRecContribuicoes'
							 	 THEN receita_orcamentaria.valor
							 	 ELSE 0
						    END
				        ) as vl_rec_contribuicoes,

				        SUM(
						   	CASE WHEN configuracao_dcasp_arquivo.nome_tag = 'vlRecPatrimonial'
							 	 THEN receita_orcamentaria.valor
							 	 ELSE 0
						    END
				        ) as vl_rec_patrimonial,

				        SUM(
						   	CASE WHEN configuracao_dcasp_arquivo.nome_tag = 'vlRecAgropecuaria'
							 	 THEN receita_orcamentaria.valor
							 	 ELSE 0
						    END
				        ) as vl_rec_agropecuaria,

				        SUM(
						   	CASE WHEN configuracao_dcasp_arquivo.nome_tag = 'vlRecIndustrial'
							 	 THEN receita_orcamentaria.valor
							 	 ELSE 0
						    END
				        ) as vl_rec_industrial,

				        SUM(
						   	CASE WHEN configuracao_dcasp_arquivo.nome_tag = 'vlRecServicos'
							 	 THEN receita_orcamentaria.valor
							 	 ELSE 0
						    END
				        ) as vl_rec_servicos,

				        SUM(
						   	CASE WHEN configuracao_dcasp_arquivo.nome_tag = 'vlTransfCorrentes'
							 	 THEN receita_orcamentaria.valor
							 	 ELSE 0
						    END
				        ) as vl_transf_correntes,
			
				        SUM(
						   	CASE WHEN configuracao_dcasp_arquivo.nome_tag = 'vlOutrasRecCorrentes'
							 	 THEN receita_orcamentaria.valor
							 	 ELSE 0
						    END
				        ) as vl_outras_rec_correntes,
			
				        SUM(
						   	CASE WHEN configuracao_dcasp_arquivo.nome_tag = 'vlOperacoesCredito'
							 	 THEN receita_orcamentaria.valor
							 	 ELSE 0
						    END
				        ) as vl_operacoes_credito,

				        SUM(
						   	CASE WHEN configuracao_dcasp_arquivo.nome_tag = 'vlAlienacaoBens'
							 	 THEN receita_orcamentaria.valor
							 	 ELSE 0
						    END
				        ) as vl_alienacao_bens,

				        SUM(
						   	CASE WHEN configuracao_dcasp_arquivo.nome_tag = 'vlAmortizacaoEmprestimo'
							 	 THEN receita_orcamentaria.valor
							 	 ELSE 0
						    END
				        ) as vl_amortizacao_emprestimo,

				        SUM(
						   	CASE WHEN configuracao_dcasp_arquivo.nome_tag = 'vlTransfCapital'
							 	 THEN receita_orcamentaria.valor
							 	 ELSE 0
						    END
				        ) as vl_transf_capital,

				        SUM(
						   	CASE WHEN configuracao_dcasp_arquivo.nome_tag = 'vlOutrasRecCapital'
							 	 THEN receita_orcamentaria.valor
							 	 ELSE 0
						    END
				        ) as vl_outras_rec_capital,
				        
				        SUM(
						   	CASE WHEN configuracao_dcasp_arquivo.nome_tag = 'vlRecurArrecaExercicioAnterior'
							 	 THEN receita_orcamentaria.valor
							 	 ELSE 0
						    END
				        ) as vl_recur_arreca_exercicio_anterior,
				        
				        SUM(
						   	CASE WHEN configuracao_dcasp_arquivo.nome_tag = 'vlOperaCreditoRefinaInternasMobiliaria'
							 	 THEN receita_orcamentaria.valor
							 	 ELSE 0
						    END
				        ) as vl_opera_credito_refina_internas_mobiliaria,
				        
				        SUM(
						   	CASE WHEN configuracao_dcasp_arquivo.nome_tag = 'vlOperaCreditoRefinaInternasContratual'
							 	 THEN receita_orcamentaria.valor
							 	 ELSE 0
						    END
				        ) as vl_opera_credito_refina_internas_contratual,
				        
				        SUM(
						   	CASE WHEN configuracao_dcasp_arquivo.nome_tag = 'vlOperaCreditoRefinaExternasMobiliaria'
							 	 THEN receita_orcamentaria.valor
							 	 ELSE 0
						    END
				        ) as vl_opera_credito_refina_externas_mobiliaria,
				        
				        SUM(
						   	CASE WHEN configuracao_dcasp_arquivo.nome_tag = 'vlOperaCreditoRefinaExternasContratual'
							 	 THEN receita_orcamentaria.valor
							 	 ELSE 0
						    END
				        ) as vl_opera_credito_refina_externas_contratual,

				        SUM(
						   	CASE WHEN configuracao_dcasp_arquivo.nome_tag = 'vlDeficit'
							 	 THEN receita_orcamentaria.valor
							 	 ELSE 0
						    END
				        ) as vl_deficit,
				        
				        SUM(
						   	CASE WHEN configuracao_dcasp_arquivo.nome_tag = 'vlTotalQuadroReceita'
							 	 THEN receita_orcamentaria.valor
							 	 ELSE 0
						    END
				        ) as vl_total_quadro_receita

        	";

	    	$stSql .= $this->montaRecuperaValoresOrcamentarios();

	    	$stSql .= "
	    		WHERE  configuracao_dcasp_arquivo.nome_arquivo_pertencente = 'BO'
  				  AND  configuracao_dcasp_registros.exercicio = '2017'
				GROUP  BY configuracao_dcasp_arquivo.nome_arquivo_pertencente
	    	";

	    	return $stSql;
	    }

	    private function montaRecuperaValorPorCampo()
	    {
	        return "";
	    }

	    private function montaRecuperaValoresContabeis()
	    {
	    	return "  
				  FROM  tcemg.configuracao_dcasp_registros
				  JOIN  tcemg.configuracao_dcasp_arquivo using (seq_arquivo)

				  LEFT  JOIN (
				      SELECT 
				              CASE
				                  WHEN valor_lancamento.tipo_valor = 'C' 
				                  THEN replace(plano_conta_credito.cod_estrutural, '.', '')
				                  ELSE replace(plano_conta_debito.cod_estrutural, '.', '')
				              END as conta,
				              lancamento.exercicio, 
				              valor_lancamento.vl_lancamento as valor 
				      
				        FROM  contabilidade.lancamento

				        JOIN  contabilidade.valor_lancamento
				          ON  valor_lancamento.exercicio = lancamento.exercicio
				         AND  valor_lancamento.cod_entidade = lancamento.cod_entidade
				         AND  valor_lancamento.tipo = lancamento.tipo
				         AND  valor_lancamento.cod_lote = lancamento.cod_lote
				         AND  valor_lancamento.sequencia = lancamento.sequencia

				        LEFT  JOIN contabilidade.conta_credito
				          ON  conta_credito.cod_lote = valor_lancamento.cod_lote
				         AND  conta_credito.tipo = valor_lancamento.tipo
				         AND  conta_credito.sequencia = valor_lancamento.sequencia
				         AND  conta_credito.exercicio = valor_lancamento.exercicio
				         AND  conta_credito.tipo_valor = valor_lancamento.tipo_valor
				         AND  conta_credito.cod_entidade = valor_lancamento.cod_entidade

				        LEFT  JOIN contabilidade.conta_debito
				          ON  conta_debito.cod_lote = valor_lancamento.cod_lote
				         AND  conta_debito.tipo = valor_lancamento.tipo
				         AND  conta_debito.sequencia = valor_lancamento.sequencia
				         AND  conta_debito.exercicio = valor_lancamento.exercicio
				         AND  conta_debito.tipo_valor = valor_lancamento.tipo_valor
				         AND  conta_debito.cod_entidade = valor_lancamento.cod_entidade

				        -- plano de contas de crédito
				        LEFT  JOIN contabilidade.plano_analitica as plano_analitica_credito
				          ON  plano_analitica_credito.exercicio = conta_credito.exercicio
				         AND  plano_analitica_credito.cod_plano = conta_credito.cod_plano

				        LEFT  JOIN contabilidade.plano_conta as plano_conta_credito
				          ON  plano_conta_credito.exercicio = plano_analitica_credito.exercicio
				         AND  plano_conta_credito.cod_conta = plano_analitica_credito.cod_conta

				        -- plano de contas de débito
				        LEFT  JOIN contabilidade.plano_analitica as plano_analitica_debito
				          ON  plano_analitica_debito.exercicio = conta_debito.exercicio
				         AND  plano_analitica_debito.cod_plano = conta_debito.cod_plano

				        LEFT  JOIN contabilidade.plano_conta as plano_conta_debito
				          ON  plano_conta_debito.exercicio = plano_analitica_debito.exercicio
				         AND  plano_conta_debito.cod_conta = plano_analitica_debito.cod_conta

				  ) AS contabil

				 ON configuracao_dcasp_registros.exercicio = contabil.exercicio
				AND replace(configuracao_dcasp_registros.conta_contabil, '.', '') = contabil.conta
	    	";
	    }

	    private function montaRecuperaValoresOrcamentarios()
	    {
	    	return "
				  FROM  tcemg.configuracao_dcasp_registros
				  JOIN  tcemg.configuracao_dcasp_arquivo using (seq_arquivo)

				  LEFT  JOIN (
				      SELECT  distinct
				              replace(conta_receita.cod_estrutural, '.', '') as conta, 
				              arrecadacao_receita.cod_arrecadacao, 
				              arrecadacao_receita.exercicio,
				              receita.cod_conta,
				              arrecadacao_receita.vl_arrecadacao as valor

				        FROM  tesouraria.arrecadacao_receita

				        JOIN  orcamento.receita
				          ON  arrecadacao_receita.cod_receita = receita.cod_receita
				         AND  arrecadacao_receita.exercicio = receita.exercicio

				        JOIN  orcamento.conta_receita
				          ON  conta_receita.cod_conta = receita.cod_conta
				         AND  conta_receita.exercicio = receita.exercicio
				  
				  ) AS receita_orcamentaria

				 ON configuracao_dcasp_registros.exercicio = receita_orcamentaria.exercicio
				AND replace(configuracao_dcasp_registros.conta_orc_receita, '.', '') = receita_orcamentaria.conta

				  LEFT  JOIN (
				      SELECT  distinct
				              empenho.exercicio, 
				              empenho.cod_empenho, 
				              nota_liquidacao_item.vl_total as valor,
				              replace(conta_despesa.cod_estrutural, '.', '') as conta
				     
				        FROM  orcamento.despesa

				        JOIN  orcamento.conta_despesa
				          ON  despesa.cod_conta = conta_despesa.cod_conta
				         AND  despesa.exercicio = conta_despesa.exercicio

				        JOIN  empenho.pre_empenho_despesa 
				          ON  conta_despesa.cod_conta = pre_empenho_despesa.cod_conta
				         AND  conta_despesa.exercicio = pre_empenho_despesa.exercicio
				       
				        JOIN  empenho.pre_empenho
				          ON  pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
				         AND  pre_empenho_despesa.exercicio = pre_empenho.exercicio
				         
				        JOIN  empenho.empenho
				          ON  pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
				         AND  pre_empenho.exercicio = empenho.exercicio
				         
				        JOIN  empenho.nota_liquidacao
				          ON  empenho.exercicio = nota_liquidacao.exercicio_empenho
				         AND  empenho.cod_entidade = nota_liquidacao.cod_entidade
				         AND  empenho.cod_empenho  = nota_liquidacao.cod_empenho
				      
				        JOIN  empenho.nota_liquidacao_item
				          ON  nota_liquidacao.exercicio = nota_liquidacao_item.exercicio
				         AND  nota_liquidacao.cod_entidade = nota_liquidacao_item.cod_entidade
				         AND  nota_liquidacao.cod_nota = nota_liquidacao_item.cod_nota
				       
				  ) AS despesa_orcamentaria

				   ON  configuracao_dcasp_registros.exercicio = despesa_orcamentaria.exercicio
				  AND  replace(configuracao_dcasp_registros.conta_orc_despesa, '.', '') = despesa_orcamentaria.conta";
	    }

	}