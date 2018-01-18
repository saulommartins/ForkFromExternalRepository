<?php

    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
    include_once CLA_PERSISTENTE;

    class TTCEMGRPSD extends Persistente {

        public function TTCEMGRPSD()
        {
            parent::Persistente();
        }

        public function recuperaExportacao($metodo, &$rsRecordSet, $stFiltro = "", $stOrder = "", $boTransacao = "")
        {
            return $this->executaRecupera($metodo, $rsRecordSet, $stFiltro, $stOrder, $boTransacao);
        }

        public function montaRecuperaExportacao10()
        {
            return "
                SELECT  10 AS tipo_registro,
                        restos_a_pagar.*,
                        (CASE WHEN restos_a_pagar.exercicio_liquidacao::integer < 2017
                               AND restos_a_pagar.exercicio_empenho != restos_a_pagar.exercicio_liquidacao
                              THEN 1
                              WHEN restos_a_pagar.exercicio_liquidacao = '2017'
                              THEN 2
                        END) AS tipo_pagamento,
                        COALESCE(restos_a_pagar.vl_liquidacao_paga, 0.00) AS vl_pago
                  FROM  (
	                        SELECT  empenho.exercicio AS exercicio_empenho, 
	                                empenho.cod_empenho,
	                                empenho.cod_entidade,
	                                REPLACE(TO_CHAR(empenho.dt_empenho, 'ddmmyyyy'), '-', '') as data_empenho,
	                                
									LPAD(configuracao_orgao.valor, 2, '0') AS cod_orgao,
									LPAD(despesa.num_orgao::VARCHAR, 5, '0') AS cod_unidade,
									LPAD(LPAD(despesa.num_orgao::VARCHAR, 2, '0')||LPAD(despesa.num_unidade::VARCHAR, 2, '0'),5,'0')::VARCHAR AS cod_sub_unidade,
									LPAD(LPAD(empenho.cod_empenho::VARCHAR, 10, '0')||LPAD(empenho.exercicio::VARCHAR, 5, '0'),15,'0')::VARCHAR AS cod_reduzido_rsp,

	                                empenho_anulado.cod_empenho AS empenho_anulado,
	                                nota_liquidacao.exercicio AS exercicio_liquidacao,

	                                COALESCE( SUM(nota_liquidacao_item.vl_total), 0.00) AS vl_liquidado,
	                                COALESCE( SUM(nota_liquidacao_item_anulado.vl_anulado), 0.00) AS vl_anulado,
	                                COALESCE( SUM(nota_liquidacao_paga.vl_pago), 0.00) AS vl_liquidacao_paga
	        
	                          FROM  empenho.empenho

	                          JOIN  empenho.pre_empenho
	                            ON  pre_empenho.exercicio = empenho.exercicio
	                           AND  pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho

	                          JOIN  empenho.pre_empenho_despesa
	                            ON  pre_empenho_despesa.exercicio = pre_empenho.exercicio
	                           AND  pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho

	                          JOIN  orcamento.despesa
	                            ON  despesa.exercicio = pre_empenho_despesa.exercicio
	                           AND  despesa.cod_despesa = pre_empenho_despesa.cod_despesa

							  JOIN  administracao.configuracao_entidade as configuracao_orgao
							    ON  configuracao_orgao.exercicio = empenho.exercicio
							   AND  configuracao_orgao.cod_entidade = empenho.cod_entidade
							   AND  configuracao_orgao.parametro = 'tcemg_codigo_orgao_entidade_sicom'  

	                          LEFT  JOIN empenho.empenho_anulado
	                            ON  empenho_anulado.exercicio = empenho.exercicio
	                           AND  empenho_anulado.cod_entidade = empenho.cod_entidade
	                           AND  empenho_anulado.cod_empenho = empenho.cod_empenho

	                          LEFT  JOIN empenho.nota_liquidacao
	                            ON  nota_liquidacao.exercicio_empenho = empenho.exercicio
	                           AND  nota_liquidacao.cod_entidade = empenho.cod_entidade
	                           AND  nota_liquidacao.cod_empenho = empenho.cod_empenho

	                          LEFT  JOIN empenho.nota_liquidacao_item
	                            ON  nota_liquidacao_item.exercicio = nota_liquidacao.exercicio
	                           AND  nota_liquidacao_item.cod_entidade = nota_liquidacao.cod_entidade
	                           AND  nota_liquidacao_item.cod_nota = nota_liquidacao.cod_nota

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

	                         WHERE  empenho.exercicio::integer < 2017
	                           AND  empenho.exercicio::integer > 2011
	                           AND  empenho_anulado.cod_empenho is null
	                           AND  pre_empenho.exercicio::integer > 2011
	                           AND  despesa.cod_recurso in (101, 201, 102, 202, 118, 218, 119, 219)
	                           AND  nota_liquidacao_paga_anulada.cod_nota IS NULL

	                         GROUP  BY empenho.exercicio,
									   empenho.cod_empenho,
									   empenho.cod_entidade,
									   empenho.dt_empenho,
									   configuracao_orgao.valor,
									   despesa.num_orgao,
									   despesa.num_unidade,
									   empenho_anulado.cod_empenho,
									   nota_liquidacao.exercicio

                        ) AS restos_a_pagar
                 WHERE  restos_a_pagar.exercicio_empenho != restos_a_pagar.exercicio_liquidacao
            ";
        }

        public function montaRecuperaExportacao11()
        {
            return "
                SELECT  11 AS tipo_registro,
						restos_a_pagar.*
				  FROM  (
							SELECT  empenho.exercicio AS exercicio_empenho, 
									empenho.cod_empenho,
									empenho.cod_entidade,
									empenho.dt_empenho,
									despesa.cod_recurso,
								
									empenho_anulado.cod_empenho AS empenho_anulado,
									nota_liquidacao.exercicio AS exercicio_liquidacao,
									LPAD(LPAD(empenho.cod_empenho::VARCHAR, 10, '0')||LPAD(empenho.exercicio::VARCHAR, 5, '0'),15,'0')::VARCHAR AS cod_reduzido_rsp,
									COALESCE( SUM(nota_liquidacao_paga.vl_pago), 0.00) AS vl_liquidacao_paga

							  FROM  empenho.empenho

							  JOIN  empenho.pre_empenho
							    ON  pre_empenho.exercicio = empenho.exercicio
							   AND  pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho

							  JOIN  empenho.pre_empenho_despesa
							    ON  pre_empenho_despesa.exercicio = pre_empenho.exercicio
							   AND  pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho

							  JOIN  orcamento.despesa
							    ON  despesa.exercicio = pre_empenho_despesa.exercicio
							   AND  despesa.cod_despesa = pre_empenho_despesa.cod_despesa

							  JOIN  administracao.configuracao_entidade as configuracao_orgao
							    ON  configuracao_orgao.exercicio = empenho.exercicio
							   AND  configuracao_orgao.cod_entidade = empenho.cod_entidade
							   AND  configuracao_orgao.parametro = 'tcemg_codigo_orgao_entidade_sicom'  

							  LEFT  JOIN empenho.empenho_anulado
							    ON  empenho_anulado.exercicio = empenho.exercicio
							   AND  empenho_anulado.cod_entidade = empenho.cod_entidade
							   AND  empenho_anulado.cod_empenho = empenho.cod_empenho

							  LEFT  JOIN empenho.nota_liquidacao
							    ON  nota_liquidacao.exercicio_empenho = empenho.exercicio
							   AND  nota_liquidacao.cod_entidade = empenho.cod_entidade
							   AND  nota_liquidacao.cod_empenho = empenho.cod_empenho

							  LEFT  JOIN empenho.nota_liquidacao_item
							    ON  nota_liquidacao_item.exercicio = nota_liquidacao.exercicio
							   AND  nota_liquidacao_item.cod_entidade = nota_liquidacao.cod_entidade
							   AND  nota_liquidacao_item.cod_nota = nota_liquidacao.cod_nota

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

							 WHERE  empenho.exercicio::integer < 2017
							   AND  empenho_anulado.cod_empenho is null
							   AND  pre_empenho.exercicio::integer > 2011
							   AND  despesa.cod_recurso in (101, 201, 102, 202, 118, 218, 119, 219)
							   AND  nota_liquidacao_paga_anulada.cod_nota IS NULL

							 GROUP  BY empenho.exercicio,
									   empenho.cod_empenho,
									   empenho.cod_entidade,
									   empenho.dt_empenho,
									   despesa.cod_recurso,
									   empenho_anulado.cod_empenho,
									   nota_liquidacao.exercicio

						) AS restos_a_pagar
					 WHERE  restos_a_pagar.exercicio_empenho != restos_a_pagar.exercicio_liquidacao
            ";
        }

        

    }