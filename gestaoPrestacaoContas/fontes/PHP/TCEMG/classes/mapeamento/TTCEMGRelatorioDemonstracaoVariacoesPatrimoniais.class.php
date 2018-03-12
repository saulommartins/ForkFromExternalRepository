<?php

    // ini_set("display_errors", 1);
    // error_reporting(E_ALL);

    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
    include_once ( CLA_PERSISTENTE );

    class TTCEMGRelatorioDemonstracaoVariacoesPatrimoniais extends Persistente
    {
        public function recuperaDados($metodo, &$rsRecordSet, $stFiltro = "", $stOrder = "", $boTransacao = "")
        {
            if (method_exists($this, $metodo)) {
                return $this->executaRecupera($metodo, $rsRecordSet, $stFiltro, $stOrder, $boTransacao);
            }

            return $rsRecordSet;
        }

        public function montaRecuperaRegistro10Sintetico()
        {
            return "
            	SELECT  -- VARIAÇÕES PATRIMONIAIS AUMENTATIVAS -> IMPOSTOS, TAXAS E CONTRIBUIÇÕES DE MELHORIA
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlImpostos'
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) AS vl_impostos_exercicio_anterior,
                        SUM ( 
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlImpostos'
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) AS vl_impostos_exercicio_atual,
                        -- VARIAÇÕES PATRIMONIAIS AUMENTATIVAS -> CONTRIBUIÇÕES
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlContribuicoes'
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) AS vl_contribuicoes_exercicio_anterior,
                        SUM ( 
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlContribuicoes'
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) AS vl_contribuicoes_exercicio_atual,
                        -- VARIAÇÕES PATRIMONIAIS AUMENTATIVAS -> EXPLORAÇÃO E VENDA DE BENS, SERVIÇOS E DIREITOS
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlExploracoVendasDireitos'
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) AS vl_exploracao_vendas_direitos_exercicio_anterior,
                        SUM ( 
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlExploracoVendasDireitos'
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) AS vl_exploracao_vendas_direitos_exercicio_atual,
                        -- VARIAÇÕES PATRIMONIAIS AUMENTATIVAS -> VARIAÇÕES PATRIMONIAIS AUMENTATIVAS FINANCEIRAS
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlVariacoesAumentativasFinanceiras'
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_variacoes_aumentativas_financeiras_exercicio_anterior,
                        SUM ( 
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlVariacoesAumentativasFinanceiras'
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_variacoes_aumentativas_financeiras_exercicio_atual,
                        -- VARIAÇÕES PATRIMONIAIS AUMENTATIVAS -> TRANSFERÊNCIAS E DELEGAÇÕES RECEBIDAS
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlTransfDelegacoesRecebidas'
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_transf_delegacoes_recebidas_exercicio_anterior,
                        SUM ( 
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlTransfDelegacoesRecebidas'
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_transf_delegacoes_recebidas_exercicio_atual,
                        -- VARIAÇÕES PATRIMONIAIS AUMENTATIVAS -> VALORIZAÇÃO E GANHOS COM ATIVOS E DESINCORPORAÇÃO DE PASSIVOS
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlValorizacaoAtivoDesincorPassivo'
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_valorizacao_ativo_desincor_passivo_exercicio_anterior,
                        SUM ( 
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlValorizacaoAtivoDesincorPassivo'
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_valorizacao_ativo_desincor_passivo_exercicio_atual,
                        -- VARIAÇÕES PATRIMONIAIS AUMENTATIVAS -> OUTRAS VARIAÇÕES PATRIMONIAIS AUMENTATIVAS
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlOutrasVariacoesPatriAumentativas'
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_outras_variacoes_patri_aumentativas_exercicio_anterior,
                        SUM ( 
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlOutrasVariacoesPatriAumentativas'
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_outras_variacoes_patri_aumentativas_exercicio_atual,
                        -- VARIAÇÕES PATRIMONIAIS AUMENTATIVAS -> TOTAL DAS VARIAÇÕES PATRIMONIAIS AUMENTATIVAS
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlTotalVPAumentativas'
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_total_vp_aumentativas_exercicio_anterior,
                        SUM ( 
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlTotalVPAumentativas'
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_total_vp_aumentativas_exercicio_atual
  				  FROM  tcemg.configuracao_dcasp_registros
				  JOIN  tcemg.configuracao_dcasp_arquivo using (seq_arquivo)

				        ".$this->montaConsultaContabil()."
				    ON  REPLACE(configuracao_dcasp_registros.conta_contabil, '.', '') = contabil.conta
				   AND  configuracao_dcasp_registros.exercicio = contabil.exercicio

                  LEFT JOIN tcemg.configuracao_dcasp_recursos 
					ON configuracao_dcasp_arquivo.exercicio = configuracao_dcasp_arquivo.exercicio 
					AND configuracao_dcasp_arquivo.tipo_registro = configuracao_dcasp_arquivo.tipo_registro 
					AND configuracao_dcasp_arquivo.cod_arquivo = configuracao_dcasp_arquivo.cod_arquivo 
					AND configuracao_dcasp_arquivo.seq_arquivo = configuracao_dcasp_arquivo.seq_arquivo
					
				 WHERE  configuracao_dcasp_arquivo.nome_arquivo_pertencente = 'DVP'
				   AND  configuracao_dcasp_registros.tipo_registro = 10
				   
                   -- Verificando tipo de recurso - caso existir algum vinculado ao configuracao_dcasp_arquivo -- 
                   AND ( 
                        CASE WHEN (configuracao_dcasp_recursos.cod_recurso IS NOT NULL ) THEN 
				            configuracao_dcasp_recursos.cod_recurso = contabil.cod_recurso
                        ELSE true
						END
                    ) = true
				 GROUP  BY configuracao_dcasp_arquivo.nome_arquivo_pertencente
            ";
        }

        public function montaRecuperaRegistro10Analitico()
        {
            return "
            	SELECT  -- VARIAÇÕES PATRIMONIAIS AUMENTATIVAS -> IMPOSTOS, TAXAS E CONTRIBUIÇÕES DE MELHORIA -> IMPOSTOS
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlImpostos'
                                 AND (contabil.conta LIKE '4110%' OR contabil.conta LIKE '4111%' OR contabil.conta LIKE '4114%' OR contabil.conta LIKE '4119%')
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) AS vl_impostos_exercicio_anterior,
                        SUM ( 
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlImpostos'
                                 AND (contabil.conta LIKE '4110%' OR contabil.conta LIKE '4111')
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) AS vl_impostos_exercicio_atual,

                        -- VARIAÇÕES PATRIMONIAIS AUMENTATIVAS -> IMPOSTOS, TAXAS E CONTRIBUIÇÕES DE MELHORIA -> TAXAS
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlImpostos'
                                 AND (contabil.conta LIKE '4112%')
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) AS vl_taxas_exercicio_anterior,
                        SUM ( 
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlImpostos'
                                 AND (contabil.conta LIKE '4112%')
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) AS vl_taxas_exercicio_atual,

                        -- VARIAÇÕES PATRIMONIAIS AUMENTATIVAS -> IMPOSTOS, TAXAS E CONTRIBUIÇÕES DE MELHORIA -> CONTRIBUIÇÕES DE MELHORIAS
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlImpostos'
                                 AND (contabil.conta LIKE '4113%' or contabil.conta LIKE '412%')
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) AS vl_contribuicoes_melhoria_exercicio_anterior,
                        SUM ( 
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlImpostos'
                                 AND (contabil.conta LIKE '4113%' or contabil.conta LIKE '412%')
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) AS vl_contribuicoes_melhoria_exercicio_atual,

                        -- VARIAÇÕES PATRIMONIAIS AUMENTATIVAS -> CONTRIBUIÇÕES -> CONTRIBUIÇÕES SOCIAIS
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlContribuicoes'
                                 AND (contabil.conta LIKE '421%')
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_contribuicoes_sociais_exercicio_anterior,
                        SUM ( 
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlContribuicoes'
                                 AND (contabil.conta LIKE '421%')
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_contribuicoes_sociais_exercicio_atual,

                        -- VARIAÇÕES PATRIMONIAIS AUMENTATIVAS -> CONTRIBUIÇÕES -> CONTRIBUIÇÕES DE INTERVENÇÃO NO DOMÍNIO ECONÔMICO
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlContribuicoes'
                                 AND (contabil.conta LIKE '422%')
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_contribuicoes_dominio_economico_exercicio_anterior,
                        SUM ( 
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlContribuicoes'
                                 AND (contabil.conta LIKE '422%')
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_contribuicoes_dominio_economico_exercicio_atual,

                        -- VARIAÇÕES PATRIMONIAIS AUMENTATIVAS -> CONTRIBUIÇÕES -> CONTRIBUIÇÃO DE ILUMINAÇÃO PÚBLICA
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlContribuicoes'
                                 AND (contabil.conta LIKE '423%')
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_contribuicao_iluminacao_publica_exercicio_anterior,
                        SUM ( 
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlContribuicoes'
                                 AND (contabil.conta LIKE '423%')
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_contribuicao_iluminacao_publica_exercicio_atual,

                        -- VARIAÇÕES PATRIMONIAIS AUMENTATIVAS -> CONTRIBUIÇÕES -> CONTRIBUIÇÕES DE INTERESSE DAS CATEGORIAS PROFISSIONAIS
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlContribuicoes'
                                 AND (contabil.conta LIKE '42401%')
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_contribuicoes_cat_profissionais_exercicio_anterior,
                        SUM ( 
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlContribuicoes'
                                 AND (contabil.conta LIKE '42401%')
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_contribuicoes_cat_profissionais_exercicio_atual,

                        -- VARIAÇÕES PATRIMONIAIS AUMENTATIVAS -> EXPLORAÇÃO E VENDA DE BENS, SERVIÇOS E DIREITOS -> VENDAS DE MERCADORIAS
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlExploracoVendasDireitos'
                                 AND (contabil.conta LIKE '431%')
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_vendas_mercadorias_exercicio_anterior,
                        SUM ( 
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlExploracoVendasDireitos'
                                 AND (contabil.conta LIKE '431%')
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_vendas_mercadorias_exercicio_atual,

                        -- VARIAÇÕES PATRIMONIAIS AUMENTATIVAS -> EXPLORAÇÃO E VENDA DE BENS, SERVIÇOS E DIREITOS -> VENDAS DE PRODUTOS
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlExploracoVendasDireitos'
                                 AND (contabil.conta LIKE '432%')
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_vendas_products_exercicio_anterior,
                        SUM ( 
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlExploracoVendasDireitos'
                                 AND (contabil.conta LIKE '432%')
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_vendas_products_exercicio_atual,

                        -- VARIAÇÕES PATRIMONIAIS AUMENTATIVAS -> EXPLORAÇÃO E VENDA DE BENS, SERVIÇOS E DIREITOS -> EXPLORAÇÃO DE BENS, DIREITOS E PRESTAÇÃO DE SERVIÇOS
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlExploracoVendasDireitos'
                                 AND (contabil.conta LIKE '433%')
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_exploracao_bens_direitos_servicos_exercicio_anterior,
                        SUM ( 
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlExploracoVendasDireitos'
                                 AND (contabil.conta LIKE '433%')
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_exploracao_bens_direitos_servicos_exercicio_atual,

                        -- VARIAÇÕES PATRIMONIAIS AUMENTATIVAS -> VARIAÇÕES PATRIMONIAIS AUMENTATIVAS FINANCEIRAS -> JUROS E ENCARGOS DE EMPRÉTIMOS E FINANCIAMENTOS CONCEDIDOS
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlVariacoesAumentativasFinanceiras'
                                 AND (contabil.conta LIKE '441%')
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_juros_encargos_emprestimos_exercicio_anterior,
                        SUM ( 
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlVariacoesAumentativasFinanceiras'
                                 AND (contabil.conta LIKE '441%')
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_juros_encargos_emprestimos_exercicio_atual,

                        -- VARIAÇÕES PATRIMONIAIS AUMENTATIVAS -> VARIAÇÕES PATRIMONIAIS AUMENTATIVAS FINANCEIRAS -> JUROS E ENCARGOS DE MORA
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlVariacoesAumentativasFinanceiras'
                                 AND (contabil.conta LIKE '442%')
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_juros_encargos_mora_exercicio_anterior,
                        SUM ( 
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlVariacoesAumentativasFinanceiras'
                                 AND (contabil.conta LIKE '442%')
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_juros_encargos_mora_exercicio_atual,

                        -- VARIAÇÕES PATRIMONIAIS AUMENTATIVAS -> VARIAÇÕES PATRIMONIAIS AUMENTATIVAS FINANCEIRAS -> VARIAÇÕES MONETÁRIAS E CAMBIAIS
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlVariacoesAumentativasFinanceiras'
                                 AND (contabil.conta LIKE '443%')
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_variacoes_monetarias_cambiais_exercicio_anterior,
                        SUM ( 
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlVariacoesAumentativasFinanceiras'
                                 AND (contabil.conta LIKE '443%')
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_variacoes_monetarias_cambiais_exercicio_atual,

                        -- VARIAÇÕES PATRIMONIAIS AUMENTATIVAS -> VARIAÇÕES PATRIMONIAIS AUMENTATIVAS FINANCEIRAS -> DESCONTOS FINANCEIROS OBTIDOS
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlVariacoesAumentativasFinanceiras'
                                 AND (contabil.conta LIKE '444%')
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_descontos_finan_obtidos_exercicio_anterior,
                        SUM ( 
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlVariacoesAumentativasFinanceiras'
                                 AND (contabil.conta LIKE '444%')
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_descontos_finan_obtidos_exercicio_atual,

                        -- VARIAÇÕES PATRIMONIAIS AUMENTATIVAS -> VARIAÇÕES PATRIMONIAIS AUMENTATIVAS FINANCEIRAS -> REMUNERAÇÃO DE DEPÓSITOS BANCÁRIOS E APLICAÇÕES FINANCEIRAS
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlVariacoesAumentativasFinanceiras'
                                 AND (contabil.conta LIKE '445%')
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_remuneracao_depos_aplicacoes_financeiras_exercicio_anterior,
                        SUM ( 
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlVariacoesAumentativasFinanceiras'
                                 AND (contabil.conta LIKE '445%')
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_remuneracao_depos_aplicacoes_financeiras_exercicio_atual,

                        -- VARIAÇÕES PATRIMONIAIS AUMENTATIVAS -> VARIAÇÕES PATRIMONIAIS AUMENTATIVAS FINANCEIRAS -> OUTRAS VARIAÇÕES PATRIMONIAIS AUMENTATIVAS - FINANCEIRAS
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlVariacoesAumentativasFinanceiras'
                                 AND (contabil.conta LIKE '449%')
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_out_var_patr_aument_exercicio_anterior,
                        SUM ( 
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlVariacoesAumentativasFinanceiras'
                                 AND (contabil.conta LIKE '449%')
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_out_var_patr_aument_exercicio_atual,


                        -- VARIAÇÕES PATRIMONIAIS AUMENTATIVAS -> TRANSFERÊNCIAS E DELEGAÇÕES RECEBIDAS -> TRANSFERÊNCIAS INTRAGOVERNAMENTAIS
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlTransfDelegacoesRecebidas'
                                 AND (contabil.conta LIKE '451%')
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_transferencias_intragovernamentais_exercicio_anterior,
                        SUM ( 
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlTransfDelegacoesRecebidas'
                                 AND (contabil.conta LIKE '451%')
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_transferencias_intragovernamentais_exercicio_atual,

                        -- VARIAÇÕES PATRIMONIAIS AUMENTATIVAS -> TRANSFERÊNCIAS E DELEGAÇÕES RECEBIDAS -> TRANSFERÊNCIAS INTERGOVERNAMENTAIS
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlTransfDelegacoesRecebidas'
                                 AND (contabil.conta LIKE '452%')
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_transferencias_intergovernamentais_exercicio_anterior,
                        SUM ( 
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlTransfDelegacoesRecebidas'
                                 AND (contabil.conta LIKE '452%')
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_transferencias_intergovernamentais_exercicio_atual,

                        -- VARIAÇÕES PATRIMONIAIS AUMENTATIVAS -> TRANSFERÊNCIAS E DELEGAÇÕES RECEBIDAS -> TRANSFERÊNCIAS DAS INSTITUIÇÕES PRIVADAS
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlTransfDelegacoesRecebidas'
                                 AND (contabil.conta LIKE '453%')
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_transferencias_instituicoes_privadas_exercicio_anterior,
                        SUM ( 
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlTransfDelegacoesRecebidas'
                                 AND (contabil.conta LIKE '453%')
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_transferencias_instituicoes_privadas_exercicio_atual,

                        -- VARIAÇÕES PATRIMONIAIS AUMENTATIVAS -> TRANSFERÊNCIAS E DELEGAÇÕES RECEBIDAS -> TRANSFERÊNCIAS DAS INSTITUIÇÕES MULTIGOVERNAMENTAIS
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlTransfDelegacoesRecebidas'
                                 AND (contabil.conta LIKE '454%')
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_transf_inst_multigover_exercicio_anterior,
                        SUM ( 
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlTransfDelegacoesRecebidas'
                                 AND (contabil.conta LIKE '454%')
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_transf_inst_multigover_exercicio_atual,

                        -- VARIAÇÕES PATRIMONIAIS AUMENTATIVAS -> TRANSFERÊNCIAS E DELEGAÇÕES RECEBIDAS -> TRANSFERÊNCIAS DE CONSÓRCIOS PÚBLICOS
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlTransfDelegacoesRecebidas'
                                 AND (contabil.conta LIKE '455%')
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_transferencias_consorcios_publicos_exercicio_anterior,
                        SUM ( 
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlTransfDelegacoesRecebidas'
                                 AND (contabil.conta LIKE '455%')
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_transferencias_consorcios_publicos_exercicio_atual,

                        -- VARIAÇÕES PATRIMONIAIS AUMENTATIVAS -> TRANSFERÊNCIAS E DELEGAÇÕES RECEBIDAS -> TRANSFERÊNCIAS DO EXTERIOR
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlTransfDelegacoesRecebidas'
                                 AND (contabil.conta LIKE '456%')
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_transferencias_exterior_exercicio_anterior,
                        SUM ( 
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlTransfDelegacoesRecebidas'
                                 AND (contabil.conta LIKE '456%')
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_transferencias_exterior_exercicio_atual,

                        -- VARIAÇÕES PATRIMONIAIS AUMENTATIVAS -> TRANSFERÊNCIAS E DELEGAÇÕES RECEBIDAS -> EXECUÇÃO ORÇAMENTÁRIA DELEGADA DE ENTES
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlTransfDelegacoesRecebidas'
                                 AND (contabil.conta LIKE '457%')
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_execucao_orcamentaria_delegada_exercicio_anterior,
                        SUM ( 
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlTransfDelegacoesRecebidas'
                                 AND (contabil.conta LIKE '457%')
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_execucao_orcamentaria_delegada_exercicio_atual,

                        -- VARIAÇÕES PATRIMONIAIS AUMENTATIVAS -> TRANSFERÊNCIAS E DELEGAÇÕES RECEBIDAS -> TRANSFERÊNCIAS DE PESSOAS FÍSICAS
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlTransfDelegacoesRecebidas'
                                 AND (contabil.conta LIKE '458%')
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_transferencias_pessoas_fisicas_exercicio_anterior,
                        SUM ( 
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlTransfDelegacoesRecebidas'
                                 AND (contabil.conta LIKE '458%')
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_transferencias_pessoas_fisicas_exercicio_atual,

                        -- VARIAÇÕES PATRIMONIAIS AUMENTATIVAS -> TRANSFERÊNCIAS E DELEGAÇÕES RECEBIDAS -> OUTRAS TRANSFERÊNCIAS E DELEGAÇÕES RECEBIDAS
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlTransfDelegacoesRecebidas'
                                 AND (contabil.conta LIKE '459%')
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_outr_transf_deleg_rec_exercicio_anterior,
                        SUM ( 
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlTransfDelegacoesRecebidas'
                                 AND (contabil.conta LIKE '459%')
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_outr_transf_deleg_rec_exercicio_atual,

                        -- VARIAÇÕES PATRIMONIAIS AUMENTATIVAS -> VALORIZAÇÃO E GANHOS COM ATIVOS E DESINCORPORAÇÃO DE PASSIVOS -> REAVALIAÇÃO DE ATIVOS
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlValorizacaoAtivoDesincorPassivo'
                                 AND (contabil.conta LIKE '461%')
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_reavaliacao_ativos_exercicio_anterior,
                        SUM ( 
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlValorizacaoAtivoDesincorPassivo'
                                 AND (contabil.conta LIKE '461%')
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_reavaliacao_ativos_exercicio_atual,

                        -- VARIAÇÕES PATRIMONIAIS AUMENTATIVAS -> VALORIZAÇÃO E GANHOS COM ATIVOS E DESINCORPORAÇÃO DE PASSIVOS -> GANHOS COM ALIENAÇÃO
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlValorizacaoAtivoDesincorPassivo'
                                 AND (contabil.conta LIKE '462%')
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_valorizacao_ativo_desincor_passivo_exercicio_anterior,
                        SUM ( 
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlValorizacaoAtivoDesincorPassivo'
                                 AND (contabil.conta LIKE '462%')
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_valorizacao_ativo_desincor_passivo_exercicio_atual,

                        -- VARIAÇÕES PATRIMONIAIS AUMENTATIVAS -> VALORIZAÇÃO E GANHOS COM ATIVOS E DESINCORPORAÇÃO DE PASSIVOS -> GANHOS COM INCORPORAÇÃO DE ATIVOS
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlValorizacaoAtivoDesincorPassivo'
                                 AND (contabil.conta LIKE '463%')
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_ganhos_incorporacao_ativo_exercicio_anterior,
                        SUM ( 
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlValorizacaoAtivoDesincorPassivo'
                                 AND (contabil.conta LIKE '463%')
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_ganhos_incorporacao_ativo_exercicio_atual,

                        -- VARIAÇÕES PATRIMONIAIS AUMENTATIVAS -> VALORIZAÇÃO E GANHOS COM ATIVOS E DESINCORPORAÇÃO DE PASSIVOS -> DESINCORPORAÇÃO DE PASSIVOS
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlValorizacaoAtivoDesincorPassivo'
                                 AND (contabil.conta LIKE '464%')
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_desincorporacao_passivos_exercicio_anterior,
                        SUM ( 
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlValorizacaoAtivoDesincorPassivo'
                                 AND (contabil.conta LIKE '464%')
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_desincorporacao_passivos_exercicio_atual,

                        -- VARIAÇÕES PATRIMONIAIS AUMENTATIVAS -> VALORIZAÇÃO E GANHOS COM ATIVOS E DESINCORPORAÇÃO DE PASSIVOS -> REVERSÃO DE REDUÇÃO AO VALOR RECUPERÁVEL
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlValorizacaoAtivoDesincorPassivo'
                                 AND (contabil.conta LIKE '465%')
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_reversao_reducao_valor_recuperavel_exercicio_anterior,
                        SUM ( 
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlValorizacaoAtivoDesincorPassivo'
                                 AND (contabil.conta LIKE '465%')
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_reversao_reducao_valor_recuperavel_exercicio_atual,

                        -- VARIAÇÕES PATRIMONIAIS AUMENTATIVAS -> OUTRAS VARIAÇÕES PATRIMONIAIS AUMENTATIVAS -> VPA A CLASSIFICAR
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlOutrasVariacoesPatriAumentativas'
                                 AND (contabil.conta LIKE '491%')
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_vpa_classificar_exercicio_anterior,
                        SUM ( 
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlOutrasVariacoesPatriAumentativas'
                                 AND (contabil.conta LIKE '491%')
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_vpa_classificar_exercicio_atual,

                        -- VARIAÇÕES PATRIMONIAIS AUMENTATIVAS -> OUTRAS VARIAÇÕES PATRIMONIAIS AUMENTATIVAS -> RESULTADO POSITIVO DE PARTICIPAÇÕES
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlOutrasVariacoesPatriAumentativas'
                                 AND (contabil.conta LIKE '492%')
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_result_positivo_participacoes_exercicio_anterior,
                        SUM ( 
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlOutrasVariacoesPatriAumentativas'
                                 AND (contabil.conta LIKE '492%')
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_result_positivo_participacoes_exercicio_atual,

                        -- VARIAÇÕES PATRIMONIAIS AUMENTATIVAS -> OUTRAS VARIAÇÕES PATRIMONIAIS AUMENTATIVAS -> REVERSÃO DE PROVISÕES E AJUSTES PARA PERDAS
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlOutrasVariacoesPatriAumentativas'
                                 AND (contabil.conta LIKE '497%')
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_reversao_provisoes_ajustes_perdas_exercicio_anterior,
                        SUM ( 
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlOutrasVariacoesPatriAumentativas'
                                 AND (contabil.conta LIKE '497%')
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_reversao_provisoes_ajustes_perdas_exercicio_atual,

                        -- VARIAÇÕES PATRIMONIAIS AUMENTATIVAS -> OUTRAS VARIAÇÕES PATRIMONIAIS AUMENTATIVAS -> DIVERSAS VARIAÇÕES PATRIMONIAIS AUMENTATIVAS
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlOutrasVariacoesPatriAumentativas'
                                 AND (contabil.conta LIKE '499%')
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_diver_var_patrim_aument_exercicio_anterior,
                        SUM ( 
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlOutrasVariacoesPatriAumentativas'
                                 AND (contabil.conta LIKE '499%')
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_diver_var_patrim_aument_exercicio_atual,
                        -- VARIAÇÕES PATRIMONIAIS AUMENTATIVAS -> TOTAL DAS VARIAÇÕES PATRIMONIAIS AUMENTATIVAS
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlTotalVPAumentativas'
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_total_vp_aumentativas_exercicio_anterior,
                        SUM ( 
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlTotalVPAumentativas'
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_total_vp_aumentativas_exercicio_atual

  				  FROM  tcemg.configuracao_dcasp_registros
				  JOIN  tcemg.configuracao_dcasp_arquivo using (seq_arquivo)
				  
                  LEFT JOIN tcemg.configuracao_dcasp_recursos 
					ON configuracao_dcasp_arquivo.exercicio = configuracao_dcasp_arquivo.exercicio 
					AND configuracao_dcasp_arquivo.tipo_registro = configuracao_dcasp_arquivo.tipo_registro 
					AND configuracao_dcasp_arquivo.cod_arquivo = configuracao_dcasp_arquivo.cod_arquivo 
					AND configuracao_dcasp_arquivo.seq_arquivo = configuracao_dcasp_arquivo.seq_arquivo

				        ".$this->montaConsultaContabil()."
				    ON  REPLACE(configuracao_dcasp_registros.conta_contabil, '.', '') = contabil.conta
				   AND  configuracao_dcasp_registros.exercicio = contabil.exercicio

				 WHERE  configuracao_dcasp_arquivo.nome_arquivo_pertencente = 'DVP'
				   AND  configuracao_dcasp_registros.tipo_registro = 10
				 GROUP  BY configuracao_dcasp_arquivo.nome_arquivo_pertencente

                   -- Verificando tipo de recurso - caso existir algum vinculado ao configuracao_dcasp_arquivo -- 
                   AND ( 
                        CASE WHEN (configuracao_dcasp_recursos.cod_recurso IS NOT NULL ) THEN 
				            configuracao_dcasp_recursos.cod_recurso = contabil.cod_recurso
                        ELSE true
						END
                    ) = true            ";
        }

        public function montaRecuperaRegistro20Sintetico()
        {
            return "
            	SELECT  -- VARIAÇÕES PATRIMONIAIS DIMINUTIVAS -> PESSOAL E ENCARGOS
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlDiminutivaPessoaEncargos'
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_diminutiva_pessoa_encargos_exercicio_anterior,
                        SUM ( 
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlDiminutivaPessoaEncargos'
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_diminutiva_pessoa_encargos_exercicio_atual,
                        -- VARIAÇÕES PATRIMONIAIS DIMINUTIVAS -> BENEFÍCIOS PREVIDENCIÁRIOS E ASSISTENCIAIS
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlPrevAssistenciais'
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_prev_assistenciais_exercicio_anterior,
                        SUM ( 
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlPrevAssistenciais'
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_prev_assistenciais_exercicio_atual,                        
                        -- VARIAÇÕES PATRIMONIAIS DIMINUTIVAS -> USO DE BENS, SERVIÇOS E CONSUMO DE CAPITAL FIXO
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlServicosCapitalFixo'
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_servico_capital_fixo_exercicio_anterior,
                        SUM ( 
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlServicosCapitalFixo'
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_servico_capital_fixo_exercicio_atual,
                        -- VARIAÇÕES PATRIMONIAIS DIMINUTIVAS -> VARIÇÕES PATRIMONIAIS DIMINUTIVAS FINANCEIRAS
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlDiminutivaVariacoesFinanceiras'
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_diminutiva_variacoes_financeiras_exercicio_anterior,
                        SUM ( 
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlDiminutivaVariacoesFinanceiras'
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_diminutiva_variacoes_financeiras_exercicio_atual,
                        -- VARIAÇÕES PATRIMONIAIS DIMINUTIVAS -> TRANSFERÊNCIAS E DELEGAÇÕES CONCEDIDAS
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlTransfConcedidas'
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_transf_concedidas_exercicio_anterior,
                        SUM ( 
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlTransfConcedidas'
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_transf_concedidas_exercicio_atual,
                        -- VARIAÇÕES PATRIMONIAIS DIMINUTIVAS -> DESVALORIZAÇÃO E PERDAS DE ATIVOS E INCORPORAÇÃO DE PASSIVOS
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlDesvaloAtivoIncorpoPassivo'
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_desvalo_ativo_incorpo_passivo_exercicio_anterior,
                        SUM ( 
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlDesvaloAtivoIncorpoPassivo'
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_desvalo_ativo_incorpo_passivo_exercicio_atual,
                        -- VARIAÇÕES PATRIMONIAIS DIMINUTIVAS -> TRIBUTÁRIAS
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlTributarias'
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_tributarias_exercicio_anterior,
                        SUM ( 
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlTributarias'
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_tributarias_exercicio_atual,
                        -- VARIAÇÕES PATRIMONIAIS DIMINUTIVAS -> CUSTO DAS MERCADORIAS E PRODUTOS VENDIDOS, E DOS SERVIÇOS PRESTADOS
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlMercadoriaVendidoServicos'
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_mercadoria_vendido_servicos_exercicio_anterior,
                        SUM ( 
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlMercadoriaVendidoServicos'
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_mercadoria_vendido_servicos_exercicio_atual,
                        -- VARIAÇÕES PATRIMONIAIS DIMINUTIVAS -> OUTRAS VARIAÇÕES PATRIMONIAIS DIMINUTIVAS
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlOutrasVariacoesPatriDiminutivas'
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_outras_variacoes_patri_diminutivas_exercicio_anterior,
                        SUM ( 
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlOutrasVariacoesPatriDiminutivas'
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_outras_variacoes_patri_diminutivas_exercicio_atual,
                        -- VARIAÇÕES PATRIMONIAIS DIMINUTIVAS -> TOTAL DAS VARIAÇÕES PATRIMONIAIS DIMINUTIVAS
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlTotalVPDiminutivas'
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_total_vp_diminutivas_exercicio_anterior,
                        SUM ( 
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlTotalVPDiminutivas'
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_total_vp_diminutivas_exercicio_atual

  				  FROM  tcemg.configuracao_dcasp_registros
				  JOIN  tcemg.configuracao_dcasp_arquivo using (seq_arquivo)

                  LEFT JOIN tcemg.configuracao_dcasp_recursos 
					ON configuracao_dcasp_arquivo.exercicio = configuracao_dcasp_arquivo.exercicio 
					AND configuracao_dcasp_arquivo.tipo_registro = configuracao_dcasp_arquivo.tipo_registro 
					AND configuracao_dcasp_arquivo.cod_arquivo = configuracao_dcasp_arquivo.cod_arquivo 
					AND configuracao_dcasp_arquivo.seq_arquivo = configuracao_dcasp_arquivo.seq_arquivo
					
				        ".$this->montaConsultaContabil()."
				    ON  REPLACE(configuracao_dcasp_registros.conta_contabil, '.', '') = contabil.conta
				   AND  configuracao_dcasp_registros.exercicio = contabil.exercicio

				 WHERE  configuracao_dcasp_arquivo.nome_arquivo_pertencente = 'DVP'
				   AND  configuracao_dcasp_registros.tipo_registro = 20

                   -- Verificando tipo de recurso - caso existir algum vinculado ao configuracao_dcasp_arquivo -- 
                   AND ( 
                        CASE WHEN (configuracao_dcasp_recursos.cod_recurso IS NOT NULL ) THEN 
				            configuracao_dcasp_recursos.cod_recurso = contabil.cod_recurso
                        ELSE true
						END
                    ) = true  
				 GROUP  BY configuracao_dcasp_arquivo.nome_arquivo_pertencente
            ";
        }

        public function montaRecuperaRegistro20Analitico()
        {
            return "
            	SELECT  -- VARIAÇÕES PATRIMONIAIS DIMINUTIVAS -> PESSOAL E ENCARGOS -> REMUNERAÇÃO A PESSOAL
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlDiminutivaPessoaEncargos'
                                 AND contabil.conta LIKE '311%'
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_remuneracao_pessoal_exercicio_anterior,
            	      	SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlDiminutivaPessoaEncargos'
                                 AND contabil.conta LIKE '311%'
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_remuneracao_pessoal_exercicio_atual,
            	      
            	        -- VARIAÇÕES PATRIMONIAIS DIMINUTIVAS -> PESSOAL E ENCARGOS -> ENCARGOS PATRONAIS
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlDiminutivaPessoaEncargos'
                                 AND contabil.conta LIKE '312%'
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_encargos_patronais_exercicio_anterior,
            	      	SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlDiminutivaPessoaEncargos'
                                 AND contabil.conta LIKE '312%'
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_encargos_patronais_exercicio_atual,
            	      
            	        -- VARIAÇÕES PATRIMONIAIS DIMINUTIVAS -> PESSOAL E ENCARGOS -> BENEFÍCIOS A PESSOAL
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlDiminutivaPessoaEncargos'
                                 AND contabil.conta LIKE '313%'
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_beneficios_pessoal_exercicio_anterior,
            	      	SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlDiminutivaPessoaEncargos'
                                 AND contabil.conta LIKE '313%'
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_beneficios_pessoal_exercicio_atual,
            	      
            	        -- VARIAÇÕES PATRIMONIAIS DIMINUTIVAS -> PESSOAL E ENCARGOS -> OUTRAS VARIAÇÕES PATRIMONIAIS DIMINUTIVAS - PESSOAL E ENCARGOS
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlDiminutivaPessoaEncargos'
                                 AND contabil.conta LIKE '319%'
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_outras_pessoal_encargos_exercicio_anterior,
            	      	SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlDiminutivaPessoaEncargos'
                                 AND contabil.conta LIKE '319%'
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_outras_pessoal_encargos_exercicio_atual,
            	      

            	        -- VARIAÇÕES PATRIMONIAIS DIMINUTIVAS -> BENEFÍCIOS PREVIDENCIÁRIOS E ASSISTENCIAIS -> APOSENTADORIAS E REFORMAS
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlPrevAssistenciais'
                                 AND contabil.conta LIKE '321%'
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_aposentadorias_reformas_exercicio_anterior,
            	      	SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlPrevAssistenciais'
                                 AND contabil.conta LIKE '321%'
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_aposentadorias_reformas_exercicio_atual,

            	        -- VARIAÇÕES PATRIMONIAIS DIMINUTIVAS -> BENEFÍCIOS PREVIDENCIÁRIOS E ASSISTENCIAIS -> PENSÕES
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlPrevAssistenciais'
                                 AND contabil.conta LIKE '322%'
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_pensoes_exercicio_anterior,
            	      	SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlPrevAssistenciais'
                                 AND contabil.conta LIKE '322%'
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_pensoes_exercicio_atual,

            	        -- VARIAÇÕES PATRIMONIAIS DIMINUTIVAS -> BENEFÍCIOS PREVIDENCIÁRIOS E ASSISTENCIAIS -> BENEFÍCIOS DE PRESTAÇÃO CONTINUADA
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlPrevAssistenciais'
                                 AND contabil.conta LIKE '323%'
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_prestacao_continuada_exercicio_anterior,
            	      	SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlPrevAssistenciais'
                                 AND contabil.conta LIKE '323%'
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_prestacao_continuada_exercicio_atual,

            	        -- VARIAÇÕES PATRIMONIAIS DIMINUTIVAS -> BENEFÍCIOS PREVIDENCIÁRIOS E ASSISTENCIAIS -> BENEFÍCIOS EVENTUAIS
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlPrevAssistenciais'
                                 AND contabil.conta LIKE '324%'
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_beneficios_eventuais_exercicio_anterior,
            	      	SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlPrevAssistenciais'
                                 AND contabil.conta LIKE '324%'
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_beneficios_eventuais_exercicio_atual,

            	        -- VARIAÇÕES PATRIMONIAIS DIMINUTIVAS -> BENEFÍCIOS PREVIDENCIÁRIOS E ASSISTENCIAIS -> POLÍTICAS PÚBLICAS DE TRANSFERÊNCIA DE RENDA
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlPrevAssistenciais'
                                 AND contabil.conta LIKE '325%'
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_pol_pub_transf_renda_exercicio_anterior,
            	      	SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlPrevAssistenciais'
                                 AND contabil.conta LIKE '325%'
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_pol_pub_transf_renda_exercicio_atual,

            	        -- VARIAÇÕES PATRIMONIAIS DIMINUTIVAS -> BENEFÍCIOS PREVIDENCIÁRIOS E ASSISTENCIAIS -> OUTROS BENEFÍCIOS PREVIDENCIÁRIOS E ASSISTENCIAIS
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlPrevAssistenciais'
                                 AND contabil.conta LIKE '329%'
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_outros_ben_prev_assist_exercicio_anterior,
            	      	SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlPrevAssistenciais'
                                 AND contabil.conta LIKE '329%'
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_outros_ben_prev_assist_exercicio_atual,

            	        -- VARIAÇÕES PATRIMONIAIS DIMINUTIVAS -> USO DE BENS, SERVIÇOS E CONSUMO DE CAPITAL FIXO -> USO DE MATERIAL DE CONSUMO
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlServicosCapitalFixo'
                                 AND contabil.conta LIKE '331%'
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_material_consumo_exercicio_anterior,
            	      	SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlServicosCapitalFixo'
                                 AND contabil.conta LIKE '331%'
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_material_consumo_exercicio_atual,

            	        -- VARIAÇÕES PATRIMONIAIS DIMINUTIVAS -> USO DE BENS, SERVIÇOS E CONSUMO DE CAPITAL FIXO -> SERVIÇOS
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlServicosCapitalFixo'
                                 AND contabil.conta LIKE '332%'
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_servicos_exercicio_anterior,
            	      	SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlServicosCapitalFixo'
                                 AND contabil.conta LIKE '332%'
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_servicos_exercicio_atual,

            	        -- VARIAÇÕES PATRIMONIAIS DIMINUTIVAS -> USO DE BENS, SERVIÇOS E CONSUMO DE CAPITAL FIXO -> DEPRECIAÇÃO, AMORTIZAÇÃO E EXAUSTÃO
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlServicosCapitalFixo'
                                 AND contabil.conta LIKE '333%'
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_depre_amort_exaustao_exercicio_anterior,
            	      	SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlServicosCapitalFixo'
                                 AND contabil.conta LIKE '333%'
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_depre_amort_exaustao_exercicio_atual,


            	        -- VARIAÇÕES PATRIMONIAIS DIMINUTIVAS -> VARIAÇÕES PATRIMONIAIS DIMINUTIVAS FINANCEIRAS -> JUROS E ENCARGOS DE EMPRÉTIMOS E FINANCIAMENTOS OBTIDOS
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlDiminutivaVariacoesFinanceiras'
                                 AND contabil.conta LIKE '341%'
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_juros_encarg_empres_finan_obtidos_exercicio_anterior,
            	      	SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlDiminutivaVariacoesFinanceiras'
                                 AND contabil.conta LIKE '341%'
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_juros_encarg_empres_finan_obtidos_exercicio_atual,

            	        -- VARIAÇÕES PATRIMONIAIS DIMINUTIVAS -> VARIAÇÕES PATRIMONIAIS DIMINUTIVAS FINANCEIRAS -> JUROS E ENCARGOS DE MORA
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlDiminutivaVariacoesFinanceiras'
                                 AND contabil.conta LIKE '342%'
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_juros_encargos_mora_exercicio_anterior,
            	      	SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlDiminutivaVariacoesFinanceiras'
                                 AND contabil.conta LIKE '342%'
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_juros_encargos_mora_exercicio_atual,

            	        -- VARIAÇÕES PATRIMONIAIS DIMINUTIVAS -> VARIAÇÕES PATRIMONIAIS DIMINUTIVAS FINANCEIRAS -> VARIAÇÕES MONETÁRIAS E CAMBIAIS
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlDiminutivaVariacoesFinanceiras'
                                 AND contabil.conta LIKE '343%'
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_variacoes_monetarias_cambiais_exercicio_anterior,
            	      	SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlDiminutivaVariacoesFinanceiras'
                                 AND contabil.conta LIKE '343%'
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_variacoes_monetarias_cambiais_exercicio_atual,

            	        -- VARIAÇÕES PATRIMONIAIS DIMINUTIVAS -> VARIAÇÕES PATRIMONIAIS DIMINUTIVAS FINANCEIRAS -> DESCONTOS FINANCEIROS CONCEDIDOS
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlDiminutivaVariacoesFinanceiras'
                                 AND contabil.conta LIKE '344%'
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_descontos_financeiros_concedidos_exercicio_anterior,
            	      	SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlDiminutivaVariacoesFinanceiras'
                                 AND contabil.conta LIKE '344%'
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_descontos_financeiros_concedidos_exercicio_atual,

            	        -- VARIAÇÕES PATRIMONIAIS DIMINUTIVAS -> VARIAÇÕES PATRIMONIAIS DIMINUTIVAS FINANCEIRAS -> OUTRAS VARIAÇÕES PATRIMONIAIS DIMINUTIVAS - FINANCERAS
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlDiminutivaVariacoesFinanceiras'
                                 AND contabil.conta LIKE '349%'
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_outras_var_patri_dim_exercicio_anterior,
            	      	SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlDiminutivaVariacoesFinanceiras'
                                 AND contabil.conta LIKE '349%'
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_outras_var_patri_dim_exercicio_atual,


            	        -- VARIAÇÕES PATRIMONIAIS DIMINUTIVAS -> TRANSFERÊNCIAS E DELEGAÇÕES CONCEDIDAS -> TRANSFERÊNCIAS INTRAGOVERNAMENTAIS
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlTransfConcedidas'
                                 AND contabil.conta LIKE '351%'
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_transf_intragov_exercicio_anterior,
            	      	SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlTransfConcedidas'
                                 AND contabil.conta LIKE '351%'
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_transf_intragov_exercicio_atual,

            	        -- VARIAÇÕES PATRIMONIAIS DIMINUTIVAS -> TRANSFERÊNCIAS E DELEGAÇÕES CONCEDIDAS -> TRANSFERÊNCIAS INTERGOVERNAMENTAIS
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlTransfConcedidas'
                                 AND contabil.conta LIKE '352%'
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_transf_intergov_exercicio_anterior,
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlTransfConcedidas'
                                 AND contabil.conta LIKE '352%'
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_transf_intergov_exercicio_atual,

            	        -- VARIAÇÕES PATRIMONIAIS DIMINUTIVAS -> TRANSFERÊNCIAS E DELEGAÇÕES CONCEDIDAS -> TRANSFERÊNCIAS A INSTITUIÇÕES PRIVADAS
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlTransfConcedidas'
                                 AND contabil.conta LIKE '353%'
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_transf_inst_priv_exercicio_anterior,
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlTransfConcedidas'
                                 AND contabil.conta LIKE '353%'
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_transf_inst_priv_exercicio_atual,

            	        -- VARIAÇÕES PATRIMONIAIS DIMINUTIVAS -> TRANSFERÊNCIAS E DELEGAÇÕES CONCEDIDAS -> TRANSFERÊNCIAS A INSTITUIÇÕES MULTIGOVERNAMENTAIS
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlTransfConcedidas'
                                 AND contabil.conta LIKE '354%'
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_transf_inst_multigov_exercicio_anterior,
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlTransfConcedidas'
                                 AND contabil.conta LIKE '354%'
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_transf_inst_multigov_exercicio_atual,

            	        -- VARIAÇÕES PATRIMONIAIS DIMINUTIVAS -> TRANSFERÊNCIAS E DELEGAÇÕES CONCEDIDAS -> TRANSFERÊNCIAS A CONSÓRCIOS PÚBLICOS
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlTransfConcedidas'
                                 AND contabil.conta LIKE '355%'
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_transf_consor_pub_exercicio_anterior,
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlTransfConcedidas'
                                 AND contabil.conta LIKE '355%'
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_transf_consor_pub_exercicio_atual,

            	        -- VARIAÇÕES PATRIMONIAIS DIMINUTIVAS -> TRANSFERÊNCIAS E DELEGAÇÕES CONCEDIDAS -> TRANSFERÊNCIAS AO EXTERIOR
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlTransfConcedidas'
                                 AND contabil.conta LIKE '356%'
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_transf_exterior_exercicio_anterior,
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlTransfConcedidas'
                                 AND contabil.conta LIKE '356%'
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_transf_exterior_exercicio_atual,

            	        -- VARIAÇÕES PATRIMONIAIS DIMINUTIVAS -> TRANSFERÊNCIAS E DELEGAÇÕES CONCEDIDAS -> EXECUÇÃO ORÇAMENTÁRIA DELEGADA DE ENTES
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlTransfConcedidas'
                                 AND contabil.conta LIKE '3571%'
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_exec_orc_dele_entes_exercicio_anterior,
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlTransfConcedidas'
                                 AND contabil.conta LIKE '3571%'
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_exec_orc_dele_entes_exercicio_atual,

            	        -- VARIAÇÕES PATRIMONIAIS DIMINUTIVAS -> TRANSFERÊNCIAS E DELEGAÇÕES CONCEDIDAS -> OUTRAS TRANSFERÊNCIAS E DELEGAÇÕES CONCEDIDAS
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlTransfConcedidas'
                                 AND contabil.conta LIKE '3572%'
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_outras_transf_dele_conce_exercicio_anterior,
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlTransfConcedidas'
                                 AND contabil.conta LIKE '3572%'
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_outras_transf_dele_conce_exercicio_atual,

            	        -- VARIAÇÕES PATRIMONIAIS DIMINUTIVAS -> DESVALORIZAÇÃO E PERDAS DE ATIVOS E INCORPORAÇÃO DE PASSIVOS -> REDUÇÃO A VALOR RECUPERÁVEL E AJUSTE PARA PERDAS
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlDesvaloAtivoIncorpoPassivo'
                                 AND contabil.conta LIKE '361%'
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_redu_vl_recu_ajs_perdas_exercicio_anterior,
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlDesvaloAtivoIncorpoPassivo'
                                 AND contabil.conta LIKE '361%'
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_redu_vl_recu_ajs_perdas_exercicio_atual,

            	        -- VARIAÇÕES PATRIMONIAIS DIMINUTIVAS -> DESVALORIZAÇÃO E PERDAS DE ATIVOS E INCORPORAÇÃO DE PASSIVOS -> PERDAS COM ALIENAÇÃO
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlDesvaloAtivoIncorpoPassivo'
                                 AND contabil.conta LIKE '362%'
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_perdas_alienacao_exercicio_anterior,
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlDesvaloAtivoIncorpoPassivo'
                                 AND contabil.conta LIKE '362%'
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_perdas_alienacao_exercicio_atual,

            	        -- VARIAÇÕES PATRIMONIAIS DIMINUTIVAS -> DESVALORIZAÇÃO E PERDAS DE ATIVOS E INCORPORAÇÃO DE PASSIVOS -> PERDAS INVOLUNTÁRIAS
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlDesvaloAtivoIncorpoPassivo'
                                 AND contabil.conta LIKE '363%'
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_perdas_involuntarias_exercicio_anterior,
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlDesvaloAtivoIncorpoPassivo'
                                 AND contabil.conta LIKE '363%'
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_perdas_involuntarias_exercicio_atual,

            	        -- VARIAÇÕES PATRIMONIAIS DIMINUTIVAS -> DESVALORIZAÇÃO E PERDAS DE ATIVOS E INCORPORAÇÃO DE PASSIVOS -> INCORPORAÇÃO DE PASSIVOS
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlDesvaloAtivoIncorpoPassivo'
                                 AND contabil.conta LIKE '364%'
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_incorp_passivos_exercicio_anterior,
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlDesvaloAtivoIncorpoPassivo'
                                 AND contabil.conta LIKE '364%'
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_incorp_passivos_exercicio_atual,

            	        -- VARIAÇÕES PATRIMONIAIS DIMINUTIVAS -> DESVALORIZAÇÃO E PERDAS DE ATIVOS E INCORPORAÇÃO DE PASSIVOS -> DESINCORPORAÇÃO DE ATIVOS
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlDesvaloAtivoIncorpoPassivo'
                                 AND contabil.conta LIKE '365%'
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_desincorporacao_ativos_exercicio_anterior,
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlDesvaloAtivoIncorpoPassivo'
                                 AND contabil.conta LIKE '365%'
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_desincorporacao_ativos_exercicio_atual,


            	        -- VARIAÇÕES PATRIMONIAIS DIMINUTIVAS -> TRIBUTÁRIAS -> IMPOSTOS, TAXAS E CONTRIBUIÇÕES DE MELHORIA
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlTributarias'
                                 AND contabil.conta LIKE '371%'
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_impostos_taxas_contrib_exercicio_anterior,
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlTributarias'
                                 AND contabil.conta LIKE '371%'
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_impostos_taxas_contrib_exercicio_atual,

            	        -- VARIAÇÕES PATRIMONIAIS DIMINUTIVAS -> TRIBUTÁRIAS -> CONTRIBUIÇÕES
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlTributarias'
                                 AND contabil.conta LIKE '372%'
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_contribuicoes_exercicio_anterior,
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlTributarias'
                                 AND contabil.conta LIKE '372%'
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_contribuicoes_exercicio_atual,


            	        -- VARIAÇÕES PATRIMONIAIS DIMINUTIVAS -> CUSTO DAS MERCADORIAS E PRODUTOS VENDIDOS, E DOS SERVIÇOS PRESTADOS -> CUSTOS DAS MERCADORIAS VENDIDAS
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlMercadoriaVendidoServicos'
                                 AND contabil.conta LIKE '381%'
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_custos_mercad_vendidas_exercicio_anterior,
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlMercadoriaVendidoServicos'
                                 AND contabil.conta LIKE '381%'
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_custos_mercad_vendidas_exercicio_atual,

            	        -- VARIAÇÕES PATRIMONIAIS DIMINUTIVAS -> CUSTO DAS MERCADORIAS E PRODUTOS VENDIDOS, E DOS SERVIÇOS PRESTADOS -> CUSTOS DOS PRODUTOS VENDIDOS
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlMercadoriaVendidoServicos'
                                 AND contabil.conta LIKE '382%'
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_custos_prod_vendidos_exercicio_anterior,
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlMercadoriaVendidoServicos'
                                 AND contabil.conta LIKE '382%'
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_custos_prod_vendidos_exercicio_atual,

            	        -- VARIAÇÕES PATRIMONIAIS DIMINUTIVAS -> CUSTO DAS MERCADORIAS E PRODUTOS VENDIDOS, E DOS SERVIÇOS PRESTADOS -> CUSTOS DOS SERVIÇOS PRESTADOS
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlMercadoriaVendidoServicos'
                                 AND contabil.conta LIKE '383%'
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_custos_serv_prestados_exercicio_anterior,
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlMercadoriaVendidoServicos'
                                 AND contabil.conta LIKE '383%'
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_custos_serv_prestados_exercicio_atual,
                        

            	        -- VARIAÇÕES PATRIMONIAIS DIMINUTIVAS -> OUTRAS VARIAÇÕES PATRIMONIAIS DIMINUTIVAS -> PREMIAÇÕES
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlOutrasVariacoesPatriDiminutivas'
                                 AND contabil.conta LIKE '391%'
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_premiacoes_exercicio_anterior,
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlOutrasVariacoesPatriDiminutivas'
                                 AND contabil.conta LIKE '391%'
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_premiacoes_exercicio_atual,
                        
            	        -- VARIAÇÕES PATRIMONIAIS DIMINUTIVAS -> OUTRAS VARIAÇÕES PATRIMONIAIS DIMINUTIVAS -> RESULTADO NEGATIVO DE PARTICIPAÇÕES
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlOutrasVariacoesPatriDiminutivas'
                                 AND contabil.conta LIKE '392%'
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_result_negativo_particip_exercicio_anterior,
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlOutrasVariacoesPatriDiminutivas'
                                 AND contabil.conta LIKE '392%'
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_result_negativo_particip_exercicio_atual,
                        
            	        -- VARIAÇÕES PATRIMONIAIS DIMINUTIVAS -> OUTRAS VARIAÇÕES PATRIMONIAIS DIMINUTIVAS -> INCENTIVOS
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlOutrasVariacoesPatriDiminutivas'
                                 AND contabil.conta LIKE '394%'
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_incentivos_exercicio_anterior,
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlOutrasVariacoesPatriDiminutivas'
                                 AND contabil.conta LIKE '394%'
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_incentivos_exercicio_atual,
                        
            	        -- VARIAÇÕES PATRIMONIAIS DIMINUTIVAS -> OUTRAS VARIAÇÕES PATRIMONIAIS DIMINUTIVAS -> SUBVENÇÕES ECONÔMICAS
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlOutrasVariacoesPatriDiminutivas'
                                 AND contabil.conta LIKE '395%'
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_subvencoes_exercicio_anterior,
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlOutrasVariacoesPatriDiminutivas'
                                 AND contabil.conta LIKE '395%'
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_subvencoes_exercicio_atual,
                        
            	        -- VARIAÇÕES PATRIMONIAIS DIMINUTIVAS -> OUTRAS VARIAÇÕES PATRIMONIAIS DIMINUTIVAS -> PARTICIPAÇÕES E CONTRIBUIÇÕES
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlOutrasVariacoesPatriDiminutivas'
                                 AND contabil.conta LIKE '396%'
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_participacoes_contribuicoes_exercicio_anterior,
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlOutrasVariacoesPatriDiminutivas'
                                 AND contabil.conta LIKE '396%'
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_participacoes_contribuicoes_exercicio_atual,
                        
            	        -- VARIAÇÕES PATRIMONIAIS DIMINUTIVAS -> OUTRAS VARIAÇÕES PATRIMONIAIS DIMINUTIVAS -> CONSTITUIÇÃO DE PROVISÕES
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlOutrasVariacoesPatriDiminutivas'
                                 AND contabil.conta LIKE '397%'
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_provisoes_contribuicoes_exercicio_anterior,
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlOutrasVariacoesPatriDiminutivas'
                                 AND contabil.conta LIKE '397%'
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_provisoes_contribuicoes_exercicio_atual,
                        
            	        -- VARIAÇÕES PATRIMONIAIS DIMINUTIVAS -> OUTRAS VARIAÇÕES PATRIMONIAIS DIMINUTIVAS -> DIVERSAS VARIAÇÕES PATRIMONIAIS DIMINUTIVAS
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlOutrasVariacoesPatriDiminutivas'
                                 AND contabil.conta LIKE '399%'
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_div_varia_patrimoni_diminutivas_exercicio_anterior,
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlOutrasVariacoesPatriDiminutivas'
                                 AND contabil.conta LIKE '399%'
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_div_varia_patrimoni_diminutivas_exercicio_atual,
                        -- VARIAÇÕES PATRIMONIAIS DIMINUTIVAS -> TOTAL DAS VARIAÇÕES PATRIMONIAIS DIMINUTIVAS
                        SUM (
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlTotalVPDiminutivas'
                                 AND contabil.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_total_vp_diminutivas_exercicio_anterior,
                        SUM ( 
                            CASE
                                WHEN configuracao_dcasp_arquivo.nome_tag = 'vlTotalVPDiminutivas'
                                 AND contabil.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(contabil.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_total_vp_diminutivas_exercicio_atual

  				  FROM  tcemg.configuracao_dcasp_registros
				  JOIN  tcemg.configuracao_dcasp_arquivo using (seq_arquivo)

                  LEFT JOIN tcemg.configuracao_dcasp_recursos 
					ON configuracao_dcasp_arquivo.exercicio = configuracao_dcasp_arquivo.exercicio 
					AND configuracao_dcasp_arquivo.tipo_registro = configuracao_dcasp_arquivo.tipo_registro 
					AND configuracao_dcasp_arquivo.cod_arquivo = configuracao_dcasp_arquivo.cod_arquivo 
					AND configuracao_dcasp_arquivo.seq_arquivo = configuracao_dcasp_arquivo.seq_arquivo
					
				        ".$this->montaConsultaContabil()."
				    ON  REPLACE(configuracao_dcasp_registros.conta_contabil, '.', '') = contabil.conta
				   AND  configuracao_dcasp_registros.exercicio = contabil.exercicio

				 WHERE  configuracao_dcasp_arquivo.nome_arquivo_pertencente = 'DVP'
				   AND  configuracao_dcasp_registros.tipo_registro = 20
		   
                   -- Verificando tipo de recurso - caso existir algum vinculado ao configuracao_dcasp_arquivo -- 
                   AND ( 
                        CASE WHEN (configuracao_dcasp_recursos.cod_recurso IS NOT NULL ) THEN 
				            configuracao_dcasp_recursos.cod_recurso = despesas.cod_recurso
                        ELSE true
						END
                    ) = true
				 GROUP  BY configuracao_dcasp_arquivo.nome_arquivo_pertencente
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
						    CASE
								WHEN valor_lancamento.tipo_valor = 'C'
							    THEN plano_recurso_credito.cod_recurso
								ELSE plano_recurso_debito.cod_recurso
						    END AS cod_recurso,
							lancamento.exercicio,
							lancamento.cod_entidade,
		  					lote.dt_lote,
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
								      
					   LEFT JOIN contabilidade.plano_recurso AS plano_recurso_credito 
						 ON plano_recurso_credito.exercicio = plano_analitica_credito.exercicio
						AND plano_recurso_credito.cod_plano = plano_analitica_credito.cod_plano
							      
						      
					   LEFT JOIN contabilidade.plano_analitica AS plano_analitica_debito 
						 ON plano_analitica_debito.exercicio = conta_debito.exercicio
					    AND plano_analitica_debito.cod_plano = conta_debito.cod_plano
								      
					   LEFT JOIN contabilidade.plano_conta AS plano_conta_debito 
						 ON plano_conta_debito.exercicio = plano_analitica_debito.exercicio
						AND plano_conta_debito.cod_conta = plano_analitica_debito.cod_conta
						
					   LEFT JOIN contabilidade.plano_recurso AS plano_recurso_debito 
						 ON plano_recurso_debito.exercicio = plano_analitica_debito.exercicio
						AND plano_recurso_debito.cod_plano = plano_analitica_debito.cod_plano
						
                      LEFT JOIN contabilidade.plano_conta_encerrada AS plano_conta_encerrada_credito
                         ON plano_conta_encerrada_credito.cod_conta = plano_conta_credito.cod_conta
                        AND plano_conta_encerrada_credito.exercicio = plano_conta_credito.exercicio

                       LEFT JOIN contabilidade.plano_conta_encerrada AS plano_conta_encerrada_debito
                         ON plano_conta_encerrada_debito.cod_conta = plano_conta_debito.cod_conta
                        AND plano_conta_encerrada_debito.exercicio = plano_conta_debito.exercicio

					  WHERE (lote.dt_lote BETWEEN '".$this->getDado('stDataInicialExercicioAtual')."' AND '".$this->getDado('stDataFinalExercicioAtual')."'
                         OR lote.dt_lote BETWEEN '".$this->getDado('stDataInicialExercicioAnterior')."' AND '".$this->getDado('stDataFinalExercicioAnterior')."')
					  	AND lancamento.cod_entidade IN (".$this->getDado('entidades').")
                        
				      ORDER BY valor_lancamento.vl_lancamento
				) AS contabil 
        	";
        }
    }
