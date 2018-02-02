<?php

    ini_set("display_errors", 1);
    error_reporting(E_ALL);

    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
    include_once ( CLA_PERSISTENTE );

    class TTCEMGRelatorioBalancoFinanceiro extends Persistente
    {
        public function recuperaDadosBalancoFinanceiro($metodo, &$rsRecordSet, $stFiltro = "", $stOrder = "", $boTransacao = "")
        {
            return $this->executaRecupera($metodo, $rsRecordSet, $stFiltro, $stOrder, $boTransacao);
        }

        public function montaRecuperaDadosBalancoFinanceiro10()
        {
        	return " 
                SELECT  -- INGRESSOS -> RECEITA ORÇAMENTÁRIA -> ORDINÁRIA
                        SUM (
                            CASE
                                WHEN campos.nome_tag = 'vlRecOrcamenRecurOrdinarios'
                                 AND campos.cod_recurso IN (100)
                                 AND campos.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(campos.valor, 0.00)
                                ELSE 0.00
                             END
                        ) AS vl_rec_orc_recurso_ordinario_exercicio_anterior,
                        SUM ( 
                            CASE
                                WHEN campos.nome_tag = 'vlRecOrcamenRecurOrdinarios'
                                 AND campos.cod_recurso IN (100)
                                 AND campos.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(campos.valor, 0.00)
                                ELSE 0.00
                             END
                        ) AS vl_rec_orc_recurso_ordinario_exercicio_atual,

                        -- INGRESSOS -> RECEITA ORÇAMENTÁRIA -> VINCULADA -> RECURSOS VINCULADOS À EDUCAÇÃO
                        SUM (
                            CASE
                                WHEN campos.nome_tag = 'vlRecOrcamenRecurVincuEducacao'
                                 AND campos.cod_recurso IN (101, 113, 118, 119, 122, 143, 144, 145, 146, 147)
                                 AND campos.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(campos.valor, 0.00)
                                ELSE 0.00
                             END
                        ) AS vl_rec_orc_recursos_vinculado_educacao_exercicio_anterior,
                        SUM ( 
                            CASE
                                WHEN campos.nome_tag = 'vlRecOrcamenRecurVincuEducacao'
                                 AND campos.cod_recurso IN (101, 113, 118, 119, 122, 143, 144, 145, 146, 147)
                                 AND campos.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(campos.valor, 0.00)
                                ELSE 0.00
                             END
                        ) AS vl_rec_orc_recursos_vinculado_educacao_exercicio_atual,
                        -- INGRESSOS -> RECEITA ORÇAMENTÁRIA -> VINCULADA -> RECURSOS VINCULADOS À SAÚDE
                        SUM (
                            CASE
                                WHEN campos.nome_tag = 'vlRecOrcamenRecurVincuSaude'
                                 AND campos.cod_recurso IN (102, 112, 123, 148, 149, 150, 151, 152, 153, 154, 155)
                                 AND campos.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(campos.valor, 0.00)
                                ELSE 0.00
                             END
                        ) AS vl_rec_orc_recursos_vinculado_saude_exercicio_anterior,
                        SUM ( 
                            CASE
                                WHEN campos.nome_tag = 'vlRecOrcamenRecurVincuSaude'
                                 AND campos.cod_recurso IN (102, 112, 123, 148, 149, 150, 151, 152, 153, 154, 155)
                                 AND campos.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(campos.valor, 0.00)
                                ELSE 0.00
                             END
                        ) AS vl_rec_orc_recursos_vinculado_saude_exercicio_atual,
                        -- INGRESSOS -> RECEITA ORÇAMENTÁRIA -> VINCULADA -> RECURSOS VINCULADOS À PREVIDÊNCIA SOCIAL - RPPS
                        SUM (
                            CASE
                                WHEN campos.nome_tag = 'vlRecOrcamenRecurVincuRPPS'
                                 AND campos.cod_recurso IN (102, 103)
                                 AND campos.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(campos.valor, 0.00)
                                ELSE 0.00
                             END
                        ) AS vl_rec_orc_recursos_vinculado_rpps_exercicio_anterior,
                        SUM ( 
                            CASE
                                WHEN campos.nome_tag = 'vlRecOrcamenRecurVincuRPPS'
                                 AND campos.cod_recurso IN (102, 103)
                                 AND campos.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(campos.valor, 0.00)
                                ELSE 0.00
                             END
                        ) AS vl_rec_orc_recursos_vinculado_rpps_exercicio_atual,
                        -- INGRESSOS -> RECEITA ORÇAMENTÁRIA -> VINCULADA -> RECURSOS VINCULADOS À PREVIDÊNCIA SOCIAL - RGPS
                        SUM (
                            CASE
                                WHEN campos.nome_tag = 'vlRecOrcamenRecurVincuRGPS'
                                 AND campos.cod_recurso IN (102, 103)
                                 AND campos.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(campos.valor, 0.00)
                                ELSE 0.00
                             END
                        ) AS vl_rec_orc_recursos_vinculado_rgps_exercicio_anterior,
                        SUM ( 
                            CASE
                                WHEN campos.nome_tag = 'vlRecOrcamenRecurVincuRGPS'
                                 AND campos.cod_recurso IN (102, 103)
                                 AND campos.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(campos.valor, 0.00)
                                ELSE 0.00
                             END
                        ) as vl_rec_orc_recursos_vinculado_rgps_exercicio_atual,
                        -- INGRESSOS -> RECEITA ORÇAMENTÁRIA -> VINCULADA -> RECURSOS VINCULADOS À ASSISTÊNCIA SOCIAL
                        SUM (
                            CASE
                                WHEN campos.nome_tag = 'vlRecOrcamenRecurVincuAssistSocial'
                                 AND campos.cod_recurso IN (129, 142, 156)
                                 AND campos.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(campos.valor, 0.00)
                                ELSE 0.00
                             END
                        ) AS vl_rec_orc_recursos_vinculado_assist_social_exercicio_anterior,
                        SUM ( 
                            CASE
                                WHEN campos.nome_tag = 'vlRecOrcamenRecurVincuAssistSocial'
                                 AND campos.cod_recurso IN (129, 142, 156)
                                 AND campos.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(campos.valor, 0.00)
                                ELSE 0.00
                             END
                        ) AS vl_rec_orc_recursos_vinculado_assist_social_exercicio_atual,
                        -- INGRESSOS -> RECEITA ORÇAMENTÁRIA -> VINCULADA -> OUTRAS DESTINAÇÕES DE RECURSOS
                        SUM (
                            CASE
                                WHEN campos.nome_tag = 'vlRecOrcamenOutrasDestRecursos'
                                 AND campos.cod_recurso IN (116, 117, 124, 157, 158, 190, 191, 192, 193)
                                 AND campos.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(campos.valor, 0.00)
                                ELSE 0.00
                             END
                        ) AS vl_rec_orc_outra_destinac_recurso_exercicio_anterior,
                        SUM ( 
                            CASE
                                WHEN campos.nome_tag = 'vlRecOrcamenOutrasDestRecursos'
                                 AND campos.cod_recurso IN (116, 117, 124, 157, 158, 190, 191, 192, 193)
                                 AND campos.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(campos.valor, 0.00)
                                ELSE 0.00
                             END
                        ) AS vl_rec_orc_outra_destinac_recurso_exercicio_atual,
                        -- INGRESSOS -> TRANSFERÊNCIAS FINANCEIRAS RECEBIDAS -> TRANSFERÊNCIAS RECEBIDAS PARA A EXECUÇÃO ORÇAMENTÁRIA
                        SUM (
                            CASE
                                WHEN campos.nome_tag = 'vlTransFinanExecuOrcamentaria'
                                 AND campos.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(campos.valor, 0.00)
                                ELSE 0.00
                             END
                        ) AS vl_trans_finan_execucao_orcamentaria_exercicio_anterior,
                        SUM ( 
                            CASE
                                WHEN campos.nome_tag = 'vlTransFinanExecuOrcamentaria'
                                 AND campos.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(campos.valor, 0.00)
                                ELSE 0.00
                             END
                        ) AS vl_trans_finan_execucao_orcamentaria_exercicio_atual,
                        -- INGRESSOS -> TRANSFERÊNCIAS FINANCEIRAS RECEBIDAS -> TRANSFERÊNCIAS RECEBIDAS INDEPENDENTES DE EXECUÇÃO ORÇAMENTÁRIA
                        SUM (
                            CASE
                                WHEN campos.nome_tag = 'vlTransFinanIndepenExecuOrcamentaria'
                                 AND campos.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(campos.valor, 0.00)
                                ELSE 0.00
                             END
                        ) AS vl_trans_finan_indepen_execucao_orcamentaria_exercicio_anterior,
                        SUM ( 
                            CASE
                                WHEN campos.nome_tag = 'vlTransFinanIndepenExecuOrcamentaria'
                                 AND campos.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(campos.valor, 0.00)
                                ELSE 0.00
                             END
                        ) AS vl_trans_finan_indepen_execucao_orcamentaria_exercicio_atual,
                        -- INGRESSOS -> TRANSFERÊNCIAS FINANCEIRAS RECEBIDAS -> TRANSFERÊNCIAS RECEBIDAS PARA APORTES DE RECURSOS PARA O RPPS
                        SUM (
                            CASE
                                WHEN campos.nome_tag = 'vlTransFinanReceAportesRPPS'
                                 AND campos.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(campos.valor, 0.00)
                                ELSE 0.00
                             END
                        ) AS vl_trans_finan_recebida_aporte_rec_rpps_exercicio_anterior,
                        SUM ( 
                            CASE
                                WHEN campos.nome_tag = 'vlTransFinanReceAportesRPPS'
                                 AND campos.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(campos.valor, 0.00)
                                ELSE 0.00
                             END
                        ) AS vl_trans_finan_recebida_aporte_rec_rpps_exercicio_atual,
                        -- INGRESSOS -> TRANSFERÊNCIAS FINANCEIRAS RECEBIDAS -> TRANSFERÊNCIAS RECEBIDAS PARA APORTES DE RECURSOS PARA O RGPS
                        SUM (
                            CASE
                                WHEN campos.nome_tag = 'vlTransFinanReceAportesRGPS'
                                 AND campos.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(campos.valor, 0.00)
                                ELSE 0.00
                             END
                        ) AS vl_trans_finan_recebida_aporte_rec_rgps_exercicio_anterior,
                        SUM ( 
                            CASE
                                WHEN campos.nome_tag = 'vlTransFinanReceAportesRGPS'
                                 AND campos.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(campos.valor, 0.00)
                                ELSE 0.00
                             END
                        ) AS vl_trans_finan_recebida_aporte_rec_rgps_exercicio_atual,
                        -- INGRESSOS -> RECEBIMENTOS EXTRAORÇAMENTÁRIOS -> INSCRIÇÃO DE RESTOS A PAGAR NÃO PROCESSADOS
                        SUM (
                            CASE
                                WHEN campos.nome_tag = 'vlIncriRSPNaoProcessado'
                                 AND campos.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(campos.valor, 0.00)
                                ELSE 0.00
                             END
                        ) AS vl_inscri_resto_pagar_nao_processado_exercicio_anterior,
                        SUM ( 
                            CASE
                                WHEN campos.nome_tag = 'vlIncriRSPNaoProcessado'
                                 AND campos.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(campos.valor, 0.00)
                                ELSE 0.00
                             END
                        ) AS vl_inscri_resto_pagar_nao_processado_exercicio_atual,
                        -- INGRESSOS -> RECEBIMENTOS EXTRAORÇAMENTÁRIOS -> INSCRIÇÃO DE RESTOS A PAGAR PROCESSADOS
                        SUM (
                            CASE
                                WHEN campos.nome_tag = 'vlIncriRSPProcessado'
                                 AND campos.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(campos.valor, 0.00)
                                ELSE 0.00
                             END
                        ) AS vl_inscri_resto_pagar_processado_exercicio_anterior,
                        SUM ( 
                            CASE
                                WHEN campos.nome_tag = 'vlIncriRSPProcessado'
                                 AND campos.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(campos.valor, 0.00)
                                ELSE 0.00
                             END
                        ) AS vl_inscri_resto_pagar_processado_exercicio_atual,
                        -- INGRESSOS -> RECEBIMENTOS EXTRAORÇAMENTÁRIOS -> DEPÓSITOS RESTITUÍVEIS E VALORES VINCULADOS
                        SUM (
                            CASE
                                WHEN campos.nome_tag = 'vlDepoRestituVinculados'
                                 AND campos.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(campos.valor, 0.00)
                                ELSE 0.00
                             END
                        ) AS vl_depo_restituivel_vinculado_exercicio_anterior,
                        SUM ( 
                            CASE
                                WHEN campos.nome_tag = 'vlDepoRestituVinculados'
                                 AND campos.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(campos.valor, 0.00)
                                ELSE 0.00
                             END
                        ) AS vl_depo_restituivel_vinculado_exercicio_atual,
                        -- INGRESSOS -> RECEBIMENTOS EXTRAORÇAMENTÁRIOS -> OUTROS RECEBIMENTOS ORÇAMENTÁRIOS
                        SUM (
                            CASE
                                WHEN campos.nome_tag = 'vlOutrosRecExtraorcamentario'
                                 AND campos.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(campos.valor, 0.00)
                                ELSE 0.00
                             END
                        ) AS vl_outr_recebimento_extraorcamentario_exercicio_anterior,
                        SUM ( 
                            CASE
                                WHEN campos.nome_tag = 'vlOutrosRecExtraorcamentario'
                                 AND campos.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(campos.valor, 0.00)
                                ELSE 0.00
                             END
                        ) AS vl_outr_recebimento_extraorcamentario_exercicio_atual,
                        -- INGRESSOS -> SALDO DO EXERCÍCIO ANTERIOR -> CAIXA E EQUIVALENTES DE CAIXA
                        SUM (
                            CASE
                                WHEN campos.nome_tag = 'vlSaldoExerAnteriorCaixaEquiCaixa'
                                 AND campos.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(campos.valor, 0.00)
                                ELSE 0.00
                             END
                        ) AS vl_sal_exerc_anterior_caixa_equivalente_caixa_exercicio_anterior,
                        SUM ( 
                            CASE
                                WHEN campos.nome_tag = 'vlSaldoExerAnteriorCaixaEquiCaixa'
                                 AND campos.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(campos.valor, 0.00)
                                ELSE 0.00
                             END
                        ) AS vl_sal_exerc_anterior_caixa_equivalente_caixa_exercicio_atual,
                        -- INGRESSOS -> SALDO DO EXERCÍCIO ANTERIOR -> DEPÓSITOS RESTITUÍVEIS E VALORES VINCULADOS
                        SUM (
                            CASE
                                WHEN campos.nome_tag = 'vlSaldoExerAnteriorDepoRestVinculados'
                                 AND campos.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(campos.valor, 0.00)
                                ELSE 0.00
                             END
                        ) AS vl_sal_exerc_anterior_deposito_restitui_valor_vinculado_exercicio_anterior,
                        SUM ( 
                            CASE
                                WHEN campos.nome_tag = 'vlSaldoExerAnteriorDepoRestVinculados'
                                 AND campos.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(campos.valor, 0.00)
                                ELSE 0.00
                             END
                        ) AS vl_sal_exerc_anterior_deposito_restitui_valor_vinculado_exercicio_atual
                  FROM  (

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
                                            receita.cod_recurso,
                                            SUM(arrecadacao_receita.vl_arrecadacao) AS valor

                                      FROM  tesouraria.arrecadacao_receita

                                      JOIN  orcamento.receita
                                        ON  arrecadacao_receita.cod_receita = receita.cod_receita
                                       AND  arrecadacao_receita.exercicio = receita.exercicio

                                      JOIN  orcamento.conta_receita
                                        ON  conta_receita.cod_conta = receita.cod_conta
                                       AND  conta_receita.exercicio = receita.exercicio
                                       
                                      LEFT  JOIN tesouraria.arrecadacao_estornada
                                        ON  arrecadacao_estornada.cod_arrecadacao = arrecadacao_receita.cod_arrecadacao
                                       AND  arrecadacao_estornada.exercicio = arrecadacao_receita.exercicio
                                       AND  arrecadacao_estornada.timestamp_arrecadacao = arrecadacao_receita.timestamp_arrecadacao

                                      LEFT  JOIN tesouraria.arrecadacao_receita_dedutora
                                        ON  arrecadacao_receita_dedutora.cod_arrecadacao = arrecadacao_receita.cod_arrecadacao
                                       AND  arrecadacao_receita_dedutora.timestamp_arrecadacao = arrecadacao_receita.timestamp_arrecadacao
                                       AND  arrecadacao_receita_dedutora.cod_receita = arrecadacao_receita.cod_receita
                                       AND  arrecadacao_receita_dedutora.exercicio = arrecadacao_receita.exercicio

                                     WHERE  (arrecadacao_receita.exercicio = '".($this->getDado('exercicio') - 1)."'
                                        OR  arrecadacao_receita.timestamp_arrecadacao 
                                                BETWEEN '".$this->getDado('dtInicial')."' AND '".$this->getDado('dtFinal')."')
                                       AND  receita.cod_entidade IN (".$this->getDado('entidades').")
                                       AND  arrecadacao_estornada.cod_arrecadacao IS NULL
                                       AND  arrecadacao_receita_dedutora.cod_arrecadacao IS NULL

                                     GROUP  BY conta_receita.cod_estrutural,
                                               arrecadacao_receita.exercicio,
                                               receita.vl_original,
                                               receita.cod_recurso

                                  )  AS totais_receitas

                            JOIN  tcemg.configuracao_dcasp_registros
                              ON  configuracao_dcasp_registros.exercicio = '".$this->getDado('exercicio')."'
                             AND  replace(configuracao_dcasp_registros.conta_orc_receita, '.', '') = replace(totais_receitas.cod_estrutural, '.', '')

                            JOIN  tcemg.configuracao_dcasp_arquivo using (seq_arquivo)

                           WHERE  configuracao_dcasp_arquivo.nome_arquivo_pertencente = 'BF'
                             AND  configuracao_dcasp_arquivo.tipo_registro = 10 

                ) AS campos
            ";
        }
        
        public function montaRecuperaDadosBalancoFinanceiro20()
        {
        	return "
                SELECT  -- DISPÊNDIOS -> DESPESA ORÇAMENTÁRIA -> ORDINÁRIA
                        SUM (
                            CASE
                                WHEN campos.nome_tag = 'vlDespOrcamenRecurOrdinarios'
                                 AND campos.cod_recurso IN (100, 200)
                                 AND campos.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(campos.valor, 0.00)
                                ELSE 0.00
                             END
                        ) AS vl_desp_orc_recurso_ordinario_exercicio_anterior,
                        SUM ( 
                            CASE
                                WHEN campos.nome_tag = 'vlDespOrcamenRecurOrdinarios'
                                 AND campos.cod_recurso IN (100, 200)
                                 AND campos.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(campos.valor, 0.00)
                                ELSE 0.00
                             END
                        ) AS vl_desp_orc_recurso_ordinario_exercicio_atual,
                        -- DISPÊNDIOS -> DESPESA ORÇAMENTÁRIA -> VINCULADA -> RECURSOS DESTINADOS À EDUCAÇÃO
                        SUM (
                            CASE
                                WHEN campos.nome_tag = 'vlDespOrcamenRecurVincuEducacao'
                                 AND campos.cod_recurso IN (101, 201, 113, 213, 118, 218, 119, 219, 122, 222, 143, 243, 144, 244, 145, 245, 146, 246, 147, 247, 289)
                                 AND campos.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(campos.valor, 0.00)
                                ELSE 0.00
                             END
                        ) AS vl_desp_orc_recursos_vinculado_educacao_exercicio_anterior,
                        SUM ( 
                            CASE
                                WHEN campos.nome_tag = 'vlDespOrcamenRecurVincuEducacao'
                                 AND campos.cod_recurso IN (101, 201, 113, 213, 118, 218, 119, 219, 122, 222, 143, 243, 144, 244, 145, 245, 146, 246, 147, 247, 289)
                                 AND campos.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(campos.valor, 0.00)
                                ELSE 0.00
                             END
                        ) AS vl_desp_orc_recursos_vinculado_educacao_exercicio_atual,
                        -- DISPÊNDIOS -> DESPESA ORÇAMENTÁRIA -> VINCULADA -> RECURSOS DESTINADOS À SAÚDE
                        SUM (
                            CASE
                                WHEN campos.nome_tag = 'vlDespOrcamenRecurVincuSaude'
                                 AND campos.cod_recurso IN (102, 202, 112, 212, 123, 223, 148, 248, 149, 249, 150, 250, 151, 251, 152, 252, 153, 253, 154, 254, 155, 255, 288)
                                 AND campos.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(campos.valor, 0.00)
                                ELSE 0.00
                             END
                        ) AS vl_desp_orc_recursos_vinculado_saude_exercicio_anterior,
                        SUM ( 
                            CASE
                                WHEN campos.nome_tag = 'vlDespOrcamenRecurVincuSaude'
                                 AND campos.cod_recurso IN (102, 202, 112, 212, 123, 223, 148, 248, 149, 249, 150, 250, 151, 251, 152, 252, 153, 253, 154, 254, 155, 255, 288)
                                 AND campos.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(campos.valor, 0.00)
                                ELSE 0.00
                             END
                        ) AS vl_desp_orc_recursos_vinculado_saude_exercicio_atual,  
                        -- DISPÊNDIOS -> DESPESA ORÇAMENTÁRIA -> VINCULADA -> RECURSOS DESTINADOS À PREVIDÊNCIA SOCIAL - RPPS
                        SUM (
                            CASE
                                WHEN campos.nome_tag = 'vlDespOrcamenRecurVincuRPPS'
                                 AND campos.cod_recurso IN (100, 103, 200, 203)
                                 AND campos.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(campos.valor, 0.00)
                                ELSE 0.00
                             END
                        ) AS vl_desp_orc_recursos_vinculado_rpps_exercicio_anterior,
                        SUM ( 
                            CASE
                                WHEN campos.nome_tag = 'vlDespOrcamenRecurVincuRPPS'
                                 AND campos.cod_recurso IN (100, 103, 200, 203)
                                 AND campos.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(campos.valor, 0.00)
                                ELSE 0.00
                             END
                        ) AS vl_desp_orc_recursos_vinculado_rpps_exercicio_atual,  
                        -- DISPÊNDIOS -> DESPESA ORÇAMENTÁRIA -> VINCULADA -> RECURSOS DESTINADOS À PREVIDÊNCIA SOCIAL - RGPS
                        SUM (
                            CASE
                                WHEN campos.nome_tag = 'vlDespOrcamenRecurVincuRGPS'
                                 AND campos.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(campos.valor, 0.00)
                                ELSE 0.00
                             END
                        ) AS vl_desp_orc_recursos_vinculado_rgps_exercicio_anterior,
                        SUM ( 
                            CASE
                                WHEN campos.nome_tag = 'vlDespOrcamenRecurVincuRGPS'
                                 AND campos.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(campos.valor, 0.00)
                                ELSE 0.00
                             END
                        ) AS vl_desp_orc_recursos_vinculado_rgps_exercicio_atual,  
                        -- DISPÊNDIOS -> DESPESA ORÇAMENTÁRIA -> VINCULADA -> RECURSOS DESTINADOS À ASSISTÊNCIA SOCIAL
                        SUM (
                            CASE
                                WHEN campos.nome_tag = 'vlDespOrcamenRecurVincuAssistSocial'
                                 AND campos.cod_recurso IN (129, 229, 142, 242, 156, 256)
                                 AND campos.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(campos.valor, 0.00)
                                ELSE 0.00
                             END
                        ) AS vl_desp_orc_recursos_vinculado_assist_social_exercicio_anterior,
                        SUM ( 
                            CASE
                                WHEN campos.nome_tag = 'vlDespOrcamenRecurVincuAssistSocial'
                                 AND campos.cod_recurso IN (129, 229, 142, 242, 156, 256)
                                 AND campos.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(campos.valor, 0.00)
                                ELSE 0.00
                             END
                        ) AS vl_desp_orc_recursos_vinculado_assist_social_exercicio_atual, 
                        -- DISPÊNDIOS -> DESPESA ORÇAMENTÁRIA -> VINCULADA -> OUTRAS DESTINAÇÕES DE RECURSOS
                        SUM (
                            CASE
                                WHEN campos.nome_tag = 'vlOutrasDespOrcamenDestRecursos'
                                 AND campos.cod_recurso IN (116, 216, 117, 217, 124, 224, 157, 257, 158, 258, 190, 290, 191, 291, 192, 292, 193, 293)
                                 AND campos.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(campos.valor, 0.00)
                                ELSE 0.00
                             END
                        ) AS vl_desp_orc_outra_destinac_recurso_exercicio_anterior,
                        SUM ( 
                            CASE
                                WHEN campos.nome_tag = 'vlOutrasDespOrcamenDestRecursos'
                                 AND campos.cod_recurso IN (116, 216, 117, 217, 124, 224, 157, 257, 158, 258, 190, 290, 191, 291, 192, 292, 193, 293)
                                 AND campos.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(campos.valor, 0.00)
                                ELSE 0.00
                             END
                        ) AS vl_desp_orc_outra_destinac_recurso_exercicio_atual, 
                        -- DISPÊNDIOS -> TRANSFERÊNCIAS FINANCEIRAS CONCEDIDAS -> TRANSFERÊNCIAS CONCEDIDAS PARA A EXECUÇÃO ORÇAMENTÁRIA
                        SUM (
                            CASE
                                WHEN campos.nome_tag = 'vlTransFinanConcExecOrcamentaria'
                                 AND campos.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(campos.valor, 0.00)
                                ELSE 0.00
                             END
                        ) AS vl_trans_finan_execucao_orcamentaria_exercicio_anterior,
                        SUM ( 
                            CASE
                                WHEN campos.nome_tag = 'vlTransFinanConcExecOrcamentaria'
                                 AND campos.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(campos.valor, 0.00)
                                ELSE 0.00
                             END
                        ) AS vl_trans_finan_execucao_orcamentaria_exercicio_atual, 
                        -- DISPÊNDIOS -> TRANSFERÊNCIAS FINANCEIRAS CONCEDIDAS -> TRANSFERÊNCIAS CONCEDIDAS INDEPENDENTES DE EXECUÇÃO ORÇAMENTÁRIA
                        SUM (
                            CASE
                                WHEN campos.nome_tag = 'vlTransFinanConcIndepenExecOrcamentaria'
                                 AND campos.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(campos.valor, 0.00)
                                ELSE 0.00
                             END
                        ) AS vl_trans_finan_indepen_execucao_orcamentaria_exercicio_anterior,
                        SUM ( 
                            CASE
                                WHEN campos.nome_tag = 'vlTransFinanConcIndepenExecOrcamentaria'
                                 AND campos.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(campos.valor, 0.00)
                                ELSE 0.00
                             END
                        ) AS vl_trans_finan_indepen_execucao_orcamentaria_exercicio_atual, 
                        -- DISPÊNDIOS -> TRANSFERÊNCIAS FINANCEIRAS CONCEDIDAS -> TRANSFERÊNCIAS CONCEDIDAS PARA APORTES DE RECURSOS PARA O RPPS
                        SUM (
                            CASE
                                WHEN campos.nome_tag = 'vlTransFinanConcAportesRecuRPPS'
                                 AND campos.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(campos.valor, 0.00)
                                ELSE 0.00
                             END
                        ) AS vl_trans_finan_concedida_aporte_rec_rpps_exercicio_anterior,
                        SUM ( 
                            CASE
                                WHEN campos.nome_tag = 'vlTransFinanConcAportesRecuRPPS'
                                 AND campos.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(campos.valor, 0.00)
                                ELSE 0.00
                             END
                        ) AS vl_trans_finan_concedida_aporte_rec_rpps_exercicio_atual, 
                        -- DISPÊNDIOS -> TRANSFERÊNCIAS FINANCEIRAS CONCEDIDAS -> TRANSFERÊNCIAS CONCEDIDAS PARA APORTES DE RECURSOS PARA O RGPS
                        SUM (
                            CASE
                                WHEN campos.nome_tag = 'vlTransFinanConcAportesRecuRGPS'
                                 AND campos.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(campos.valor, 0.00)
                                ELSE 0.00
                             END
                        ) AS vl_trans_finan_concedida_aporte_rec_rgps_exercicio_anterior,
                        SUM ( 
                            CASE
                                WHEN campos.nome_tag = 'vlTransFinanConcAportesRecuRGPS'
                                 AND campos.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(campos.valor, 0.00)
                                ELSE 0.00
                             END
                        ) AS vl_trans_finan_concedida_aporte_rec_rgps_exercicio_atual, 
                        -- DISPÊNDIOS -> PAGAMENTOS EXTRAORÇAMENTÁRIOS -> PAGAMENTOS DE RESTOS A PAGAR NÃO PROCESSADOS
                        SUM (
                            CASE
                                WHEN campos.nome_tag = 'vlPagRSPNaoProcessado'
                                 AND campos.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(campos.valor, 0.00)
                                ELSE 0.00
                             END
                        ) AS vl_pag_restos_pagar_nao_processado_exercicio_anterior,
                        SUM ( 
                            CASE
                                WHEN campos.nome_tag = 'vlPagRSPNaoProcessado'
                                 AND campos.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(campos.valor, 0.00)
                                ELSE 0.00
                             END
                        ) AS vl_pag_restos_pagar_nao_processado_exercicio_atual, 
                        -- DISPÊNDIOS -> PAGAMENTOS EXTRAORÇAMENTÁRIOS -> PAGAMENTOS DE RESTOS A PAGAR PROCESSSADOS
                        SUM (
                            CASE
                                WHEN campos.nome_tag = 'vlPagRSPProcessado'
                                 AND campos.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(campos.valor, 0.00)
                                ELSE 0.00
                             END
                        ) AS vl_pag_restos_pagar_processado_exercicio_anterior,
                        SUM ( 
                            CASE
                                WHEN campos.nome_tag = 'vlPagRSPProcessado'
                                 AND campos.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(campos.valor, 0.00)
                                ELSE 0.00
                             END
                        ) AS vl_pag_restos_pagar_processado_exercicio_atual, 
                        -- DISPÊNDIOS -> PAGAMENTOS EXTRAORÇAMENTÁRIOS -> DEPÓSITOS RESTITUÍVEIS E VALORES VINCULADOS
                        SUM (
                            CASE
                                WHEN campos.nome_tag = 'vlDeposRestVinculados'
                                 AND campos.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(campos.valor, 0.00)
                                ELSE 0.00
                             END
                        ) AS vl_depo_restituivel_vinculado_exercicio_anterior,
                        SUM ( 
                            CASE
                                WHEN campos.nome_tag = 'vlDeposRestVinculados'
                                 AND campos.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(campos.valor, 0.00)
                                ELSE 0.00
                             END
                        ) AS vl_depo_restituivel_vinculado_exercicio_atual, 
                        -- DISPÊNDIOS -> PAGAMENTOS EXTRAORÇAMENTÁRIOS -> OUTROS PAGAMENTOS ORÇAMENTÁRIOS
                        SUM (
                            CASE
                                WHEN campos.nome_tag = 'vlOutrosPagExtraorcamentarios'
                                 AND campos.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(campos.valor, 0.00)
                                ELSE 0.00
                             END
                        ) AS vl_outr_pagamento_extraorcamentario_exercicio_anterior,
                        SUM ( 
                            CASE
                                WHEN campos.nome_tag = 'vlOutrosPagExtraorcamentarios'
                                 AND campos.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(campos.valor, 0.00)
                                ELSE 0.00
                             END
                        ) AS vl_outr_pagamento_extraorcamentario_exercicio_atual, 
                        -- DISPÊNDIOS -> SALDO PARA O EXERCÍCIO SEGUINTE -> CAIXA E EQUIVALENTES DE CAIXA
                        SUM (
                            CASE
                                WHEN campos.nome_tag = 'vlSaldoExerAtualCaixaEquiCaixa'
                                 AND campos.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(campos.valor, 0.00)
                                ELSE 0.00
                             END
                        ) AS vl_sal_exerc_atual_caixa_equivalente_caixa_exercicio_anterior,
                        SUM ( 
                            CASE
                                WHEN campos.nome_tag = 'vlSaldoExerAtualCaixaEquiCaixa'
                                 AND campos.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(campos.valor, 0.00)
                                ELSE 0.00
                             END
                        ) AS vl_sal_exerc_atual_caixa_equivalente_caixa_exercicio_atual, 
                        -- DISPÊNDIOS -> SALDO PARA O EXERCÍCIO SEGUINTE -> DEPÓSITOS RESTITUÍVEIS E VALORES VINCULADOS
                        SUM (
                            CASE
                                WHEN campos.nome_tag = 'vlSaldoExerAtualDepoRestVinculados'
                                 AND campos.exercicio = '".($this->getDado('exercicio') - 1)."'
                                THEN COALESCE(campos.valor, 0.00)
                                ELSE 0.00
                             END
                        ) AS vl_sal_exerc_atual_deposito_restitui_valor_vinculado_exercicio_anterior,
                        SUM ( 
                            CASE
                                WHEN campos.nome_tag = 'vlSaldoExerAtualDepoRestVinculados'
                                 AND campos.exercicio = '".$this->getDado('exercicio')."'
                                THEN COALESCE(campos.valor, 0.00)
                                ELSE 0.00
                             END
                        ) AS vl_sal_exerc_atual_deposito_restitui_valor_vinculado_exercicio_atual
                  FROM  (
                        SELECT  configuracao_dcasp_arquivo.nome_tag, configuracao_dcasp_registros.conta_orc_despesa, despesas.*
                          FROM  tcemg.configuracao_dcasp_registros
                          JOIN  tcemg.configuracao_dcasp_arquivo USING (seq_arquivo)

                          LEFT  JOIN (
                            SELECT  pre_empenho.exercicio,
                                    pre_empenho.cod_pre_empenho, 
                                    pre_empenho_despesa.cod_despesa, 
                                    conta_despesa.cod_estrutural,
                                    despesa.cod_recurso,
                                    COALESCE(SUM(nota_liquidacao_paga.vl_pago), 0.00) AS valor
                                    

                              FROM  empenho.pre_empenho

                              JOIN  empenho.item_pre_empenho
                                ON  item_pre_empenho.exercicio = pre_empenho.exercicio
                               AND  item_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                             
                              JOIN  empenho.pre_empenho_despesa
                                ON  pre_empenho_despesa.exercicio = pre_empenho.exercicio
                               AND  pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
                               
                              JOIN  orcamento.despesa
                                ON  despesa.exercicio = pre_empenho.exercicio
                               AND  despesa.cod_despesa = pre_empenho_despesa.cod_despesa

                              JOIN  orcamento.conta_despesa
                                ON  conta_despesa.exercicio = despesa.exercicio
                               AND  conta_despesa.cod_conta = despesa.cod_conta
                                                           
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

                             WHERE  (pre_empenho.exercicio = '".($this->getDado('exercicio') - 1)."'
                                OR  empenho.dt_empenho BETWEEN '".$this->getDado('dtInicial')."' AND '".$this->getDado('dtFinal')."')

                               AND  empenho_anulado.cod_empenho IS NULL
                               AND  nota_liquidacao_paga_anulada.cod_nota IS NULL
                               AND  despesa.cod_entidade IN (".$this->getDado('entidades').")

                             GROUP  BY pre_empenho.cod_pre_empenho, 
                                       pre_empenho.exercicio, 
                                       pre_empenho_despesa.cod_despesa, 
                                       conta_despesa.cod_estrutural,
                                       despesa.cod_recurso

                          ) AS despesas

                            ON  REPLACE(despesas.cod_estrutural, '.', '') = REPLACE(configuracao_dcasp_registros.conta_orc_despesa, '.', '')

                         WHERE  configuracao_dcasp_arquivo.nome_arquivo_pertencente = 'BF'
                           AND  configuracao_dcasp_arquivo.tipo_registro = 20
                           AND  configuracao_dcasp_registros.exercicio = '".$this->getDado('exercicio')."'
                  ) AS campos
            ";
        }
    }

?>