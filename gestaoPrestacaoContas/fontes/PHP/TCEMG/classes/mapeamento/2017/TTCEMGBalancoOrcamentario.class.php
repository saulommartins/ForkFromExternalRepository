<?php

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
                            campos.fase,
                            SUM(
                                CASE  WHEN campos.nome_tag = 'vlRecTributaria'
                                      THEN campos.valor
                                      ELSE 0
                                 END
                            ) AS vl_rec_tributaria,
                            SUM(
                                CASE  WHEN campos.nome_tag = 'vlRecContribuicoes'
                                      THEN campos.valor
                                      ELSE 0
                                 END
                            ) AS vl_rec_contribuicoes,
                            SUM(
                                CASE  WHEN campos.nome_tag = 'vlRecPatrimonial'
                                      THEN campos.valor
                                      ELSE 0
                                 END
                            ) AS vl_rec_patrimonial,
                            SUM(
                                CASE  WHEN campos.nome_tag = 'vlRecAgropecuaria'
                                      THEN campos.valor
                                      ELSE 0
                                 END
                            ) AS vl_rec_agropecuaria,
                            SUM(
                                CASE  WHEN campos.nome_tag = 'vlRecIndustrial'
                                      THEN campos.valor
                                      ELSE 0
                                 END
                            ) AS vl_rec_industrial,
                            SUM(
                                CASE  WHEN campos.nome_tag = 'vlRecServicos'
                                      THEN campos.valor
                                      ELSE 0
                                 END
                            ) AS vl_rec_servicos,
                            SUM(
                                CASE  WHEN campos.nome_tag = 'vlTransfCorrentes'
                                      THEN campos.valor
                                      ELSE 0
                                 END
                            ) AS vl_transf_correntes,
                            SUM(
                                CASE  WHEN campos.nome_tag = 'vlOutrasRecCorrentes'
                                      THEN campos.valor
                                      ELSE 0
                                 END
                            ) AS vl_outras_rec_correntes,
                            SUM(
                                CASE  WHEN campos.nome_tag = 'vlOperacoesCredito'
                                      THEN campos.valor
                                      ELSE 0
                                 END
                            ) AS vl_operacoes_credito,
                            SUM(
                                CASE  WHEN campos.nome_tag = 'vlAlienacaoBens'
                                      THEN campos.valor
                                      ELSE 0
                                 END
                            ) AS vl_alienacao_bens,
                            SUM(
                                CASE  WHEN campos.nome_tag = 'vlAmortizacaoEmprestimo'
                                      THEN campos.valor
                                      ELSE 0
                                 END
                            ) AS vl_amortizacao_emprestimo,
                            SUM(
                                CASE  WHEN campos.nome_tag = 'vlTransfCapital'
                                      THEN campos.valor
                                      ELSE 0
                                 END
                            ) AS vl_transf_capital,
                            SUM(
                                CASE  WHEN campos.nome_tag = 'vlOutrasRecCapital'
                                      THEN campos.valor
                                      ELSE 0
                                 END
                            ) AS vl_outras_rec_capital,
                            SUM(
                                CASE  WHEN campos.nome_tag = 'vlRecurArrecaExercicioAnterior'
                                      THEN campos.valor
                                      ELSE 0
                                 END
                            ) AS vl_recur_arreca_exercicio_anterior,
                            SUM(
                                CASE  WHEN campos.nome_tag = 'vlOperaCreditoRefinaInternasMobiliaria'
                                      THEN campos.valor
                                      ELSE 0
                                 END
                            ) AS vl_opera_credito_refina_internas_mobiliaria,
                            SUM(
                                CASE  WHEN campos.nome_tag = 'vlOperaCreditoRefinaInternasContratual'
                                      THEN campos.valor
                                      ELSE 0
                                 END
                            ) AS vl_opera_credito_refina_internas_contratual,
                            SUM(
                                CASE  WHEN campos.nome_tag = 'vlOperaCreditoRefinaExternasMobiliaria'
                                      THEN campos.valor
                                      ELSE 0
                                 END
                            ) AS vl_opera_credito_refina_externas_mobiliaria,
                            SUM(
                                CASE  WHEN campos.nome_tag = 'vlOperaCreditoRefinaExternasContratual'
                                      THEN campos.valor
                                      ELSE 0
                                 END
                            ) AS vl_opera_credito_refina_externas_contratual,
                            0 AS vl_deficit,
                            SUM(campos.valor) AS vl_total_quadro_receita,
                            COALESCE(SUM(campos.vl_original), 0) AS vl_orcado
                      FROM (
            ";

            $stSql .= $this->montaRecuperaValoresReceitasOrcamentarias();

            $stSql .= "
                ) AS campos

                WHERE  campos.nome_arquivo_pertencente = 'BO'
                  AND  campos.tipo_registro = 10           
                GROUP  BY campos.fase
            ";

            return $stSql;
        }

        public function montaRecuperaExportacao20()
        {
        	return "
        		SELECT  20 AS tipo_registro,
            				3 AS fase,
        						COALESCE(vlSaldoExercicioAnteriorSuperavitFinan.valor, 0.00) AS vl_saldo_exercicio_anterior_superavit_finan, 
        						COALESCE(vlSaldoExercicioAnteriorReaberturaCreditoAdicio.valor, 0.00) AS vl_saldo_exercicio_anterior_reabertura_credito_adicio,
        						COALESCE(SUM(vlSaldoExercicioAnteriorSuperavitFinan.valor + vlSaldoExercicioAnteriorReaberturaCreditoAdicio.valor), 0.00) AS vl_total_saldo_exercicios_anteriores
    				  FROM (
    					      SELECT  SUM(valor_lancamento.vl_lancamento) AS valor
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
        							LEFT  JOIN contabilidade.plano_analitica AS plano_analitica_credito
        							  ON  plano_analitica_credito.exercicio = conta_credito.exercicio
        							 AND  plano_analitica_credito.cod_plano = conta_credito.cod_plano

        							LEFT  JOIN contabilidade.plano_conta AS plano_conta_credito
        							  ON  plano_conta_credito.exercicio = plano_analitica_credito.exercicio
        							 AND  plano_conta_credito.cod_conta = plano_analitica_credito.cod_conta

        							-- plano de contas de débito
        							LEFT  JOIN contabilidade.plano_analitica AS plano_analitica_debito
        							  ON  plano_analitica_debito.exercicio = conta_debito.exercicio
        							 AND  plano_analitica_debito.cod_plano = conta_debito.cod_plano

        							LEFT  JOIN contabilidade.plano_conta AS plano_conta_debito
        							  ON  plano_conta_debito.exercicio = plano_analitica_debito.exercicio
        							 AND  plano_conta_debito.cod_conta = plano_analitica_debito.cod_conta

    					       WHERE  (plano_conta_credito.cod_estrutural LIKE '1%' OR plano_conta_debito.cod_estrutural LIKE '2%')
    					         AND  lancamento.exercicio = '".$this->getDado('exercicio')."'
                       AND  lancamento.cod_entidade IN (".$this->getDado('entidades').")

    					 ) AS vlSaldoExercicioAnteriorSuperavitFinan,
    					 (
      						  SELECT  SUM(suplementacao_suplementada.valor) AS valor
      							  FROM  orcamento.suplementacao

        							JOIN  orcamento.suplementacao_suplementada
        							  ON  suplementacao_suplementada.exercicio = suplementacao.exercicio
        							 AND  suplementacao_suplementada.cod_suplementacao = suplementacao.cod_suplementacao

      						   WHERE  EXTRACT(MONTH FROM suplementacao.dt_suplementacao) > 8
      							   AND  suplementacao.exercicio = '".$this->getDado('exercicio')."'
    					 ) AS vlSaldoExercicioAnteriorReaberturaCreditoAdicio

    					GROUP BY vlSaldoExercicioAnteriorSuperavitFinan.valor, vlSaldoExercicioAnteriorReaberturaCreditoAdicio.valor
        	";
        }

        public function montaRecuperaExportacao30()
        {
            $stSql  = "
                      SELECT  30 AS tipo_registro,
                              campos.fase as fase,
                              SUM(
                                  CASE WHEN campos.nome_tag = 'vlPessoalEncarSociais'
                                 THEN campos.valor
                                 ELSE 0
                                   END
                              ) AS vl_pessoal_encar_social,

                              SUM(
                                  CASE WHEN campos.nome_tag = 'vlJurosEncarDividas'
                                 THEN campos.valor
                                 ELSE 0
                                  END
                              ) AS vl_juros_encar_dividas,

                              SUM(
                                  CASE WHEN campos.nome_tag = 'vlOutrasDespCorrentes'
                                 THEN campos.valor
                                 ELSE 0
                                  END
                              ) AS vl_outras_desp_correntes,

                              SUM(
                                  CASE WHEN campos.nome_tag = 'vlInvestimentos'
                                 THEN campos.valor
                                 ELSE 0
                                  END
                              ) AS vl_investimentos,

                              SUM(
                                  CASE WHEN campos.nome_tag = 'vlInverFinanceira'
                                 THEN campos.valor
                                 ELSE 0
                                  END
                              ) AS vl_inver_financeira,

                              SUM(
                                  CASE WHEN campos.nome_tag = 'vlAmortizaDivida'
                                 THEN campos.valor
                                 ELSE 0
                                  END
                              ) AS vl_amortiza_divida,

                              SUM(
                                  CASE WHEN campos.nome_tag = 'vlReservaContingencia'
                                 THEN campos.valor
                                 ELSE 0
                                  END
                              ) AS vl_reserva_contingencia,

                              SUM(
                                  CASE WHEN campos.cod_estrutural LIKE '9.9.9.9.99.99%'
                                  AND campos.cod_subfuncao = 997
                                 THEN campos.valor
                                 ELSE 0
                                  END
                              ) AS vl_reserva_rpps,

                              SUM(
                                  CASE WHEN campos.nome_tag = 'vlAmortizaDividaInterMobiliaria'
                                 THEN campos.valor
                                 ELSE 0
                                  END
                              ) AS vl_amortiza_divida_inter_mobiliaria,

                              SUM(
                                  CASE WHEN campos.cod_estrutural LIKE '4.6.9.0.76.00%'
                                   OR campos.cod_estrutural LIKE '4.6.9.0.77.00%'
                                 THEN campos.valor
                                 ELSE 0
                                  END
                              ) AS vl_amortiza_outras_dividas_internas,

                              SUM(
                                  CASE WHEN campos.nome_tag = 'vlAmortizaDividaExterMobiliaria'
                                 THEN campos.valor
                                 ELSE 0
                                  END
                              ) AS vl_amortiza_divida_exter_mobiliaria,

                              SUM(
                                  CASE WHEN campos.cod_estrutural LIKE '4.6.9.0.76.00%'
                                   OR campos.cod_estrutural LIKE '4.6.9.0.77.00%'
                                 THEN campos.valor
                                 ELSE 0
                                  END
                              ) AS vl_amortiza_outras_dividas_externas,

                              SUM(
                                  CASE WHEN campos.nome_tag = 'vlSuperavit'
                                 THEN campos.valor
                                 ELSE 0
                                  END
                              ) AS vl_superavit,

                              SUM(campos.valor) AS vl_total_quadro_despesa

                        FROM (
            ";

            $stSql .= $this->montaRecuperaValoresDespesasOrcamentarias();

            $stSql .= "
                    ) AS campos

                WHERE  campos.nome_arquivo_pertencente = 'BO'
                  AND  campos.tipo_registro = 30           
                GROUP  BY campos.fase
            ";

            return $stSql;
        }


        public function montaRecuperaExportacao40()
        {
            $stSql = "
                SELECT  40 AS tipo_registro,
                        nao_processados.fase,
                        SUM(
                            CASE WHEN configuracao_dcasp_arquivo.nome_tag = 'vlRSPNaoProcesPessoalEncarSociais'
                                 THEN nao_processados.valor_empenho
                                 ELSE 0
                             END 
                        ) AS vl_rsp_nao_proces_pessoal_encar_sociais,
                        
                        SUM(
                            CASE WHEN configuracao_dcasp_arquivo.nome_tag = 'vlRSPNaoProcesJurosEncarDividas'
                                 THEN nao_processados.valor_empenho
                                 ELSE 0
                             END 
                        ) AS vl_rsp_nao_proces_juros_encar_dividas,
                        
                        SUM(
                            CASE WHEN configuracao_dcasp_arquivo.nome_tag = 'vlRSPNaoProcesOutrasDespCorrentes'
                                 THEN nao_processados.valor_empenho
                                 ELSE 0
                             END 
                        ) AS vl_rsp_nao_proces_outras_desp_correntes,
                        
                        SUM(
                            CASE WHEN configuracao_dcasp_arquivo.nome_tag = 'vlRSPNaoProcesInvestimentos'
                                 THEN nao_processados.valor_empenho
                                 ELSE 0
                             END 
                        ) AS vl_rsp_nao_proces_investimentos,
                        
                        SUM(
                            CASE WHEN configuracao_dcasp_arquivo.nome_tag = 'vlRSPNaoProcesInverFinanceira'
                                 THEN nao_processados.valor_empenho
                                 ELSE 0
                             END 
                        ) AS vl_rsp_nao_proces_inver_financeira,
                        
                        SUM(
                            CASE WHEN configuracao_dcasp_arquivo.nome_tag = 'vlRSPNaoProcesAmortizaDivida'
                                 THEN nao_processados.valor_empenho
                                 ELSE 0
                             END 
                        ) AS vl_rsp_nao_proces_amortiza_divida,
                        
                        SUM(nao_processados.valor_empenho) AS vl_total_execu_rsp_nao_processado

            ";
            
            $stSql .= $this->montaRecuperaValoresNaoProcessados();

            $stSql .= "
                WHERE  configuracao_dcasp_arquivo.nome_arquivo_pertencente = 'BO'
                  AND  configuracao_dcasp_arquivo.tipo_registro = 40
                  AND  configuracao_dcasp_registros.exercicio = '".$this->getDado('exercicio')."'
                  AND  nao_processados.fase IS NOT NULL
                GROUP  BY nao_processados.fase, configuracao_dcasp_arquivo.nome_arquivo_pertencente
            ";

            return $stSql;
        }

        public function montaRecuperaExportacao50()
        {
            $stSql = "
                SELECT  50 AS tipo_registro,
                        nao_processados.fase,
                        SUM(
                            CASE WHEN configuracao_dcasp_arquivo.nome_tag = 'vlRSPProcesLiqPessoalEncarSociais'
                                 THEN nao_processados.valor_liquidado
                                 ELSE 0
                             END 
                        ) AS vl_rsp_proces_liq_pessoal_encar_sociais,
                        
                        SUM(
                            CASE WHEN configuracao_dcasp_arquivo.nome_tag = 'vlRSPProcesLiqJurosEncarDividas'
                                 THEN nao_processados.valor_liquidado
                                 ELSE 0
                             END 
                        ) AS vl_rsp_proces_liq_juros_encar_dividas,
                        
                        SUM(
                            CASE WHEN configuracao_dcasp_arquivo.nome_tag = 'vlRSPProcesLiqOutrasDespCorrentes'
                                 THEN nao_processados.valor_liquidado
                                 ELSE 0
                             END 
                        ) AS vl_rsp_proces_liq_outras_desp_correntes,
                        
                        SUM(
                            CASE WHEN configuracao_dcasp_arquivo.nome_tag = 'vlRSPProcesLiqInvestimentos'
                                 THEN nao_processados.valor_liquidado
                                 ELSE 0
                             END 
                        ) AS vl_rsp_proces_liq_investimentos,
                        
                        SUM(
                            CASE WHEN configuracao_dcasp_arquivo.nome_tag = 'vlRSPProcesLiqInverFinanceira'
                                 THEN nao_processados.valor_liquidado
                                 ELSE 0
                             END 
                        ) AS vl_rsp_proces_liq_inver_financeira,
                        
                        SUM(
                            CASE WHEN configuracao_dcasp_arquivo.nome_tag = 'vlRSPProcesLiqAmortizaDivida'
                                 THEN nao_processados.valor_liquidado
                                 ELSE 0
                             END 
                        ) AS vl_rsp_proces_liq_amortiza_divida,
                        
                        SUM(nao_processados.valor_liquidado) AS vl_total_execu_rsp_proce_nao_proce_liquidado
            ";
            
            $stSql .= $this->montaRecuperaValoresNaoProcessados();

            $stSql .= "
                WHERE  configuracao_dcasp_arquivo.nome_arquivo_pertencente = 'BO'
                  AND  configuracao_dcasp_arquivo.tipo_registro = 50
                  AND  configuracao_dcasp_registros.exercicio = '".$this->getDado('exercicio')."'
                  AND  nao_processados.valor_liquidado IS NOT NULL
                  AND  nao_processados.valor_empenho_anulado IS NULL
                GROUP  BY nao_processados.fase, configuracao_dcasp_arquivo.nome_arquivo_pertencente
            ";

            return $stSql;

        }

        public function montaRecuperaValoresNaoProcessados()
        {
            return "
                    FROM  tcemg.configuracao_dcasp_registros
                    JOIN  tcemg.configuracao_dcasp_arquivo using (seq_arquivo)

                    LEFT  JOIN(
                            SELECT  DISTINCT 
                                    (
                                        CASE 
                                             WHEN  empenho.exercicio = '".$this->getDado('exercicio')."'
                                              AND  (empenho_anulado_item.cod_empenho IS NOT NULL 
                                               OR  nota_liquidacao_item_anulado.cod_nota IS NOT NULL)
                                             THEN  5
                                               
                                             WHEN  empenho.exercicio = '".$this->getDado('exercicio')."' 
                                              AND  nota_liquidacao_paga.vl_pago IS NOT NULL
                                             THEN  4

                                             WHEN  empenho.exercicio = '".$this->getDado('exercicio')."'
                                              AND  nota_liquidacao.dt_liquidacao IS NOT NULL
                                             THEN  3

                                             WHEN  empenho.exercicio::integer < ".$this->getDado('exercicio')."
                                              AND  empenho.dt_empenho = '31/12/2016'
                                             THEN  2

                                             WHEN  empenho.exercicio::integer < ".$this->getDado('exercicio')."
                                             THEN  1
                                        END 
                                    ) as fase,
                                    
                                    empenho.cod_empenho,
                                    empenho.exercicio,
                                    empenho.cod_entidade, 
                                    conta_despesa.cod_estrutural,
                                    replace(conta_despesa.cod_estrutural, '.', '') AS conta,
                                    
                                    SUM(item_pre_empenho.vl_total) AS valor_empenho,
                                    SUM(empenho_anulado_item.vl_anulado) AS valor_empenho_anulado,
                                    SUM(nota_liquidacao_item.vl_total) AS valor_liquidado,
                                    SUM(nota_liquidacao_paga.vl_pago) AS valor_pago,
                                    SUM(nota_liquidacao_item_anulado.vl_anulado) AS valor_nota_anulacao

                              FROM  empenho.empenho

                              JOIN  empenho.pre_empenho
                                ON  pre_empenho.exercicio = empenho.exercicio
                               AND  pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho

                              JOIN  empenho.item_pre_empenho
                                ON  item_pre_empenho.exercicio = pre_empenho.exercicio
                               AND  item_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho

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

                             WHERE  empenho.cod_entidade IN (".$this->getDado('entidades').")

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
                    ) AS nao_processados
                    ON configuracao_dcasp_registros.exercicio = nao_processados.exercicio
                    AND replace(configuracao_dcasp_registros.conta_orc_despesa, '.', '') = nao_processados.conta
            ";
        }

        public function montaRecuperaValoresReceitasOrcamentarias()
        {
          return "
                SELECT  configuracao_dcasp_arquivo.nome_tag,
                        configuracao_dcasp_arquivo.nome_arquivo_pertencente,
                        configuracao_dcasp_arquivo.tipo_registro,
                        totais_receitas.*,
                        CASE WHEN totais_receitas.valor > totais_receitas.vl_original
                             THEN 2
                             ELSE 1
                         END AS fase
                  FROM  (
                          SELECT  conta_receita.cod_estrutural,
                                  arrecadacao_receita.exercicio,
                                  receita.vl_original,
                                  SUM(arrecadacao_receita.vl_arrecadacao) AS valor

                            FROM  tesouraria.arrecadacao_receita

                            JOIN  orcamento.receita
                              ON  arrecadacao_receita.cod_receita = receita.cod_receita
                             AND  arrecadacao_receita.exercicio = receita.exercicio

                            JOIN  orcamento.conta_receita
                              ON  conta_receita.cod_conta = receita.cod_conta
                             AND  conta_receita.exercicio = receita.exercicio

                           WHERE  arrecadacao_receita.exercicio = '".$this->getDado('exercicio')."'
                             AND  receita.cod_entidade IN (".$this->getDado('entidades').")

                           GROUP  BY conta_receita.cod_estrutural,
                                     arrecadacao_receita.exercicio,
                                     receita.vl_original
                        )  AS totais_receitas

                  JOIN  tcemg.configuracao_dcasp_registros
                    ON  configuracao_dcasp_registros.exercicio = '".$this->getDado('exercicio')."'
                   AND  replace(configuracao_dcasp_registros.conta_orc_receita, '.', '') = replace(totais_receitas.cod_estrutural, '.', '')

                  JOIN  tcemg.configuracao_dcasp_arquivo using (seq_arquivo)
          ";
        }

        public function montaRecuperaValoresDespesasOrcamentarias()
        {
          return "
                  SELECT  DISTINCT
                          totais_despesas.*,
                          configuracao_dcasp_arquivo.nome_tag,
                          configuracao_dcasp_arquivo.nome_arquivo_pertencente,
                          configuracao_dcasp_arquivo.tipo_registro,
                          CASE  WHEN totais_despesas.valor > totais_despesas.vl_original
                                THEN 2
                                ELSE 1
                          END AS fase
                    FROM  (
                            SELECT  despesas.cod_estrutural, 
                                    despesas.vl_original, 
                                    despesas.cod_despesa,
                                    despesas.dt_criacao,
                                    despesas.cod_subfuncao,
                                    COALESCE(SUM(empenhos.vl_liquidacao_paga), 0.00) AS valor
                              FROM  (
                                      SELECT  conta_despesa.cod_estrutural, 
                                              despesa.cod_despesa, 
                                              despesa.vl_original, 
                                              despesa.dt_criacao,
                                              despesa.cod_subfuncao
                                        FROM  orcamento.despesa

                                        JOIN  orcamento.conta_despesa
                                          ON  conta_despesa.exercicio = despesa.exercicio
                                         AND  conta_despesa.cod_conta = despesa.cod_conta

                                       WHERE  despesa.exercicio = '".$this->getDado('exercicio')."'
                                         AND  despesa.cod_entidade IN (".$this->getDado('entidades').")
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

                                       WHERE  pre_empenho.exercicio = '".$this->getDado('exercicio')."'
                                         AND  empenho_anulado.cod_empenho IS NULL
                                         AND  nota_liquidacao_paga_anulada.cod_nota IS NULL

                                       GROUP  BY pre_empenho.cod_pre_empenho, pre_empenho.exercicio, pre_empenho_despesa.cod_despesa
                                    ) AS empenhos

                                  ON  empenhos.cod_despesa = despesas.cod_despesa

                               GROUP  BY despesas.cod_estrutural, despesas.vl_original, despesas.cod_despesa, despesas.dt_criacao, despesas.cod_subfuncao
                        )  AS totais_despesas

                  JOIN  tcemg.configuracao_dcasp_registros
                    ON  configuracao_dcasp_registros.exercicio = '".$this->getDado('exercicio')."'
                   AND  REPLACE(configuracao_dcasp_registros.conta_orc_despesa, '.', '') = REPLACE(totais_despesas.cod_estrutural, '.', '')

                  JOIN  tcemg.configuracao_dcasp_arquivo USING (seq_arquivo)
          ";
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

                   WHERE  nota_liquidacao.exercicio = '".$this->getDado('exercicio')."'
                     AND  empenho.cod_entidade IN (".$this->getDado('entidades').")
            ";
        }
    }