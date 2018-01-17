<?php

    ini_set("display_errors", 1);
    error_reporting(E_ALL);
    
    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
    include_once CLA_PERSISTENTE;

    class TTCEMGBalancoOrcamentario extends Persistente {

        public function TTCEMGBalancoOrcamentario()
        {
            parent::Persistente();
        }

        public function recuperaExportacao($metodo, &$rsRecordSet, $stFiltro = "", $stOrder = "", $boTransacao = "")
        {
            return $this->executaRecupera($metodo, $rsRecordSet, $stFiltro, $stOrder, $boTransacao);
        }

        public function montaRecuperaExportacao10()
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
                        0 as vl_deficit,
                        SUM(receita_orcamentaria.valor) as vl_total_quadro_receita,
                        COALESCE(SUM(receita_orcamentaria.vl_original), 0) as vl_orcado
            ";

            $stSql .= $this->montaRecuperaValoresOrcamentarios();

            $stSql .= "
                WHERE  configuracao_dcasp_arquivo.nome_arquivo_pertencente = 'BO'
                    AND  configuracao_dcasp_arquivo.tipo_registro = 10
                    AND  configuracao_dcasp_registros.exercicio = '2017'
                GROUP  BY configuracao_dcasp_arquivo.nome_arquivo_pertencente
            ";

            return $stSql;
        }

        public function montaRecuperaExportacao30()
        {
            $stSql  = "
                SELECT  30 as tipo_registro, 
                        SUM(
                            CASE WHEN configuracao_dcasp_arquivo.nome_tag = 'vlPessoalEncarSociais'
                                 THEN receita_orcamentaria.valor
                                  ELSE 0
                             END
                        ) as vl_pessoal_encar_social,

                        SUM(
                               CASE WHEN configuracao_dcasp_arquivo.nome_tag = 'vlJurosEncarDividas'
                                  THEN receita_orcamentaria.valor
                                  ELSE 0
                            END
                        ) as vl_juros_encar_dividas,

                        SUM(
                               CASE WHEN configuracao_dcasp_arquivo.nome_tag = 'vlOutrasDespCorrentes'
                                  THEN receita_orcamentaria.valor
                                  ELSE 0
                            END
                        ) as vl_outras_desp_correntes,
                        
                        SUM(
                               CASE WHEN configuracao_dcasp_arquivo.nome_tag = 'vlInvestimentos'
                                  THEN receita_orcamentaria.valor
                                  ELSE 0
                            END
                        ) as vl_investimentos,
                        
                        SUM(
                               CASE WHEN configuracao_dcasp_arquivo.nome_tag = 'vlInverFinanceira'
                                  THEN receita_orcamentaria.valor
                                  ELSE 0
                            END
                        ) as vl_inver_financeira,

                        SUM(
                               CASE WHEN configuracao_dcasp_arquivo.nome_tag = 'vlAmortizaDivida'
                                  THEN receita_orcamentaria.valor
                                  ELSE 0
                            END
                        ) as vl_amortiza_divida,

                        SUM(
                               CASE WHEN configuracao_dcasp_arquivo.nome_tag = 'vlReservaContingencia'
                                  THEN receita_orcamentaria.valor
                                  ELSE 0
                            END
                        ) as vl_reserva_contingencia,

                        SUM(
                               CASE WHEN conta_receita.cod_estrutural like '9.9.9.9.99.99%'
                                     AND receita_orcamentaria.cod_subfuncao = 997
                                  THEN receita_orcamentaria.valor
                                  ELSE 0
                            END
                        ) as vl_reserva_rpps,

                        SUM(
                               CASE WHEN configuracao_dcasp_arquivo.nome_tag = 'vlAmortizaDividaInterMobiliaria'
                                  THEN receita_orcamentaria.valor
                                  ELSE 0
                            END
                        ) as vl_amortiza_divida_inter_mobiliaria,

                        SUM(
                               CASE WHEN conta_receita.cod_estrutural like '4.6.9.0.76.00%' OR 
                                         conta_receita.cod_estrutural like '4.6.9.0.77.00%'
                                  THEN receita_orcamentaria.valor
                                  ELSE 0
                            END
                        ) as vl_amortiza_outras_dividas_internas,

                        SUM(
                               CASE WHEN configuracao_dcasp_arquivo.nome_tag = 'vlAmortizaDividaExterMobiliaria'
                                  THEN receita_orcamentaria.valor
                                  ELSE 0
                            END
                        ) as vl_amortiza_divida_exter_mobiliaria,

                        SUM(
                               CASE WHEN conta_receita.cod_estrutural like '4.6.9.0.76.00%' OR 
                                         conta_receita.cod_estrutural like '4.6.9.0.77.00%'
                                  THEN receita_orcamentaria.valor
                                  ELSE 0
                            END
                        ) as vl_amortiza_outras_dividas_externas,

                        SUM(
                               CASE WHEN configuracao_dcasp_arquivo.nome_tag = 'vlSuperavit'
                                  THEN receita_orcamentaria.valor
                                  ELSE 0
                            END
                        ) as vl_superavit,

                        SUM(
                               CASE WHEN configuracao_dcasp_arquivo.nome_tag = 'vlTotalQuadroDespesa'
                                  THEN receita_orcamentaria.valor
                                  ELSE 0
                            END
                        ) as vl_total_quadro_despesa,

            ";

            $stSql .= $this->montaRecuperaValoresOrcamentarios();

            $stSql .= "
                WHERE  configuracao_dcasp_arquivo.nome_arquivo_pertencente = 'BO'
                  AND  configuracao_dcasp_arquivo.tipo_registro = 30
                    AND  configuracao_dcasp_registros.exercicio = '2017'
                GROUP  BY configuracao_dcasp_arquivo.nome_arquivo_pertencente
            ";

            return $stSql;
        }

        public function montaRecuperaExportacao40()
        {
            $stSql = "
                SELECT  40 as tipo_registro, 
                        SUM(
                               CASE WHEN conta_receita.cod_estrutural like '9.9.9.9.99.99%' AND 
                                         conta_receita.cod_subfuncao = 997
                                  THEN receita_orcamentaria.valor
                                  ELSE 0
                            END
                        ) as vl_reserva_rpps
            ";

            $stSql .= $this->montaRecuperaValoresOrcamentarios();

            $stSql .= "
                WHERE  configuracao_dcasp_arquivo.nome_arquivo_pertencente = 'BO'
                  AND  configuracao_dcasp_arquivo.tipo_registro = 30
                    AND  configuracao_dcasp_registros.exercicio = '2017'
                GROUP  BY configuracao_dcasp_arquivo.nome_arquivo_pertencente
            ";

            return $stSql;
        }

        public function montaRecuperaExportacao50()
        {
            $stSql = "
                SELECT  50 AS tipo_registro,
                		restos_a_pagar.fase,
                		SUM(
                			CASE WHEN cod_estrutural like '3.1%'
                				 THEN restos_a_pagar.valor_liquidado
                				 ELSE 0
            				 END 
                		) AS vl_rsp_nao_proces_pessoal_encar_sociais,
                		
                		SUM(
                			CASE WHEN cod_estrutural like '3.2%'
                				 THEN restos_a_pagar.valor_liquidado
                				 ELSE 0
            				 END 
                		) AS vl_rsp_nao_proces_juros_encar_dividas,
                		
                		SUM(
                			CASE WHEN cod_estrutural like '3.3%'
                				 THEN restos_a_pagar.valor_liquidado
                				 ELSE 0
            				 END 
                		) AS vl_rsp_nao_proces_outras_desp_correntes,
                		
                		SUM(
                			CASE WHEN cod_estrutural like '4.4%'
                				 THEN restos_a_pagar.valor_liquidado
                				 ELSE 0
            				 END 
                		) AS vl_rsp_nao_proces_investimentos,
                		
                		SUM(
                			CASE WHEN cod_estrutural like '4.5%'
                				 THEN restos_a_pagar.valor_liquidado
                				 ELSE 0
            				 END 
                		) AS vl_rsp_nao_proces_inver_financeira,
                		
                		SUM(
                			CASE WHEN cod_estrutural like '4.6%'
                				 THEN restos_a_pagar.valor_liquidado
                				 ELSE 0
            				 END 
                		) AS vl_rsp_nao_proces_amortiza_divida,
                		
                		SUM(restos_a_pagar.valor_liquidado) AS vl_total_execu_rsp_nao_processado

                  FROM  (
                    SELECT  DISTINCT 
                            (
                                CASE 
                                     WHEN  empenho.exercicio = '2017'
                                      AND  (empenho_anulado_item.cod_empenho IS NOT NULL 
                                       OR  nota_liquidacao_item_anulado.cod_nota IS NOT NULL)
                                     THEN  5
                                       
                                     WHEN  empenho.exercicio = '2017' 
                                      AND  nota_liquidacao_paga.vl_pago IS NOT NULL
                                     THEN  4

                                     WHEN  empenho.exercicio = '2017'
                                      AND  nota_liquidacao.dt_liquidacao IS NOT NULL
                                     THEN  3

                                     WHEN  empenho.exercicio::integer < 2017
                                      AND  empenho.dt_empenho = '31/12/2016'
                                     THEN  2

                                     WHEN  empenho.exercicio::integer < 2017
                                     THEN  1
                                END 
                            ) as fase,
                            empenho.cod_empenho,
                            empenho.exercicio,
                            empenho.cod_entidade, 
                            conta_despesa.cod_estrutural, 
                            SUM(empenho_anulado_item.vl_anulado) as valor_empenho_anulado,
                            SUM(nota_liquidacao_item.vl_total) as valor_liquidado,
                            SUM(nota_liquidacao_paga.vl_pago) as valor_pago,
                            SUM(nota_liquidacao_item_anulado.vl_anulado) as valor_nota_anulacao
        
                      FROM  empenho.empenho

                      JOIN  empenho.pre_empenho
                        ON  pre_empenho.exercicio = empenho.exercicio
                       AND  pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho

                      JOIN  empenho.pre_empenho_despesa 
                        ON  pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
                       AND  pre_empenho_despesa.exercicio = pre_empenho.exercicio
                                 
                      JOIN  orcamento.conta_despesa
                        ON  conta_despesa.cod_conta = pre_empenho_despesa.cod_conta
                       AND  conta_despesa.exercicio = pre_empenho_despesa.exercicio
                                                 
                      JOIN  orcamento.despesa
                        ON  despesa.cod_conta = conta_despesa.cod_conta
                       AND  despesa.exercicio = conta_despesa.exercicio

                      LEFT  JOIN empenho.restos_pre_empenho
                        ON  restos_pre_empenho.exercicio = pre_empenho.exercicio 
                       AND  restos_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho

                      LEFT  JOIN empenho.nota_liquidacao
                        ON  nota_liquidacao.exercicio = empenho.exercicio
                       AND  nota_liquidacao.cod_entidade = empenho.cod_entidade
                       AND  nota_liquidacao.cod_empenho = empenho.cod_empenho

                      LEFT  JOIN empenho.nota_liquidacao_item
                        ON  nota_liquidacao_item.exercicio = nota_liquidacao.exercicio
                       AND  nota_liquidacao_item.cod_entidade = nota_liquidacao.cod_entidade
                       AND  nota_liquidacao_item.cod_nota = nota_liquidacao.cod_nota

                      LEFT  JOIN empenho.nota_liquidacao_paga
                        ON  nota_liquidacao_paga.exercicio = nota_liquidacao.exercicio
                       AND  nota_liquidacao_paga.cod_entidade = nota_liquidacao.cod_entidade
                       AND  nota_liquidacao_paga.cod_nota = nota_liquidacao.cod_nota

                      LEFT  JOIN empenho.empenho_anulado
                        ON  empenho_anulado.exercicio = empenho.exercicio
                       AND  empenho_anulado.cod_empenho = empenho.cod_empenho
                       AND  empenho_anulado.cod_entidade = empenho.cod_entidade
                                            
                      LEFT  JOIN empenho.empenho_anulado_item
                        ON  empenho_anulado_item.exercicio = empenho_anulado.exercicio
                       AND  empenho_anulado_item.cod_entidade = empenho_anulado.cod_entidade
                       AND  empenho_anulado_item.cod_empenho = empenho_anulado.cod_empenho
                       AND  empenho_anulado_item.timestamp = empenho_anulado.timestamp
                                            
                      LEFT  JOIN empenho.nota_liquidacao_item_anulado
                        ON  nota_liquidacao_item_anulado.exercicio = nota_liquidacao_item.exercicio
                       AND  nota_liquidacao_item_anulado.cod_nota = nota_liquidacao_item.cod_nota
                       AND  nota_liquidacao_item_anulado.cod_entidade = nota_liquidacao_item.cod_entidade
                       AND  nota_liquidacao_item_anulado.num_item = nota_liquidacao_item.num_item
                       AND  nota_liquidacao_item_anulado.cod_pre_empenho = nota_liquidacao_item.cod_pre_empenho
                       AND  nota_liquidacao_item_anulado.exercicio_item = nota_liquidacao_item.exercicio_item

                     GROUP  BY nota_liquidacao_paga.vl_pago, 
                               empenho_anulado_item.cod_empenho, 
                               nota_liquidacao_item_anulado.cod_nota, 
                               empenho.cod_empenho,
                               empenho.exercicio, 
                               empenho.cod_entidade, 
                               conta_despesa.cod_estrutural, 
                               nota_liquidacao_item.cod_nota, 
                               nota_liquidacao.dt_liquidacao, 
                               nota_liquidacao_item_anulado.timestamp
                  ) AS restos_a_pagar
              GROUP BY restos_a_pagar.fase
            ";

            return $stSql;
        }

        public function montaRecuperaValoresContabeis()
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

        public function montaRecuperaValoresOrcamentarios()
        {
            $entidades = $this->getDado('entidades');

            return "
                  FROM  tcemg.configuracao_dcasp_registros
                  JOIN  tcemg.configuracao_dcasp_arquivo using (seq_arquivo)

                  LEFT  JOIN (
                      SELECT  distinct
                              replace(conta_receita.cod_estrutural, '.', '') as conta, 
                              conta_receita.cod_estrutural,
                              arrecadacao_receita.cod_arrecadacao, 
                              arrecadacao_receita.exercicio,
                              receita.cod_conta,
                              receita.cod_entidade,
                              receita.vl_original,
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
                  AND receita_orcamentaria.cod_entidade IN (".$entidades.")

                  LEFT  JOIN (
                      SELECT  distinct
                              empenho.exercicio, 
                                empenho.cod_entidade,
                              empenho.cod_empenho, 
                              nota_liquidacao_item.vl_total as valor,
                              replace(conta_despesa.cod_estrutural, '.', '') as conta,
                              despesa.cod_subfuncao
                     
                        FROM  orcamento.despesa

                        JOIN  orcamento.conta_despesa
                          ON  conta_despesa.cod_conta = despesa.cod_conta
                         AND  conta_despesa.exercicio = despesa.exercicio

                        JOIN  empenho.pre_empenho_despesa 
                          ON  pre_empenho_despesa.cod_conta = conta_despesa.cod_conta
                         AND  pre_empenho_despesa.exercicio = conta_despesa.exercicio
                       
                        JOIN  empenho.pre_empenho
                          ON  pre_empenho.cod_pre_empenho = pre_empenho_despesa.cod_pre_empenho
                         AND  pre_empenho.exercicio = pre_empenho_despesa.exercicio
                         
                        JOIN  empenho.empenho
                          ON  empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                         AND  empenho.exercicio = pre_empenho.exercicio
                         
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
                  AND  replace(configuracao_dcasp_registros.conta_orc_despesa, '.', '') = despesa_orcamentaria.conta
                    AND  despesa_orcamentaria.cod_entidade IN (".$entidades.")";
        }

        public function montaRecuperaTotalDespesas()
        {
            return "
                  SELECT  SUM(nota_liquidacao_item.vl_total) as valor
                 
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

                   WHERE  nota_liquidacao.exercicio = '2017'
                        AND  empenho.cod_entidade IN (".$this->getDado('entidades').")
            ";
        }
    }