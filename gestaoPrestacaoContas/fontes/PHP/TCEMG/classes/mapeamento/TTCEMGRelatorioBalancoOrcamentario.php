<?php


	include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
	include_once ( CLA_PERSISTENTE );

	class TTCEMGRelatorioBalancoOrcamentario extends Persistente
	{
	    public function recuperaDadosBalancoOrcamentario($metodo, $rsRecordSet, $stFiltro = "", $stOrder = "", $boTransacao = "")
	    {
	        return $this->executaRecupera($metodo, $rsRecordSet, $stFiltro, $stOrder, $boTransacao);
	    }

	    public function montaRecuperaDadosBalancoOrcamentario10()
	    {
	        return "
			        SELECT  campos.seq_arquivo,
							campos.nome_tag, 
							COALESCE(
								SUM(
								    CASE 
								        WHEN campos.fase = 1
								        THEN campos.valor
								        ELSE 0.00
								     END
								), 0.00
							) as previsaoInicial,
							COALESCE(
								SUM(
								    CASE 
						                WHEN campos.fase = 2
										THEN campos.valor
										ELSE 0.00
								     END
								), 0.00
							) as previsaoAtualizada,
							COALESCE(
								SUM(campos.valor), 0.00
							) as receitasRealizadas
				      FROM  (
							SELECT  configuracao_dcasp_arquivo.nome_tag,
									configuracao_dcasp_arquivo.seq_arquivo,
			                        configuracao_dcasp_arquivo.nome_arquivo_pertencente,
			                        configuracao_dcasp_arquivo.tipo_registro,
			                        totais_receitas.*,
			                        CASE  
			                        	  WHEN totais_receitas.valor > totais_receitas.vl_original
			                              THEN 2
			                              ELSE 1
			                         END AS fase
			                  FROM  tcemg.configuracao_dcasp_arquivo
			                  LEFT  JOIN  tcemg.configuracao_dcasp_registros using (seq_arquivo)

			                  LEFT  JOIN (
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

			                           WHERE  arrecadacao_receita.exercicio = '2017'
			                             AND  receita.cod_entidade IN (1, 2, 3, 4, 5, 6, 7, 8)

	                         		   GROUP  BY conta_receita.cod_estrutural,
	                                   			 arrecadacao_receita.exercicio,
	                                   			 receita.vl_original
		                    		)  AS totais_receitas
					            ON  configuracao_dcasp_registros.exercicio = '2017'
					           AND  replace(configuracao_dcasp_registros.conta_orc_receita, '.', '') = 
					           		replace(totais_receitas.cod_estrutural, '.', '')
							)  AS campos

				  	 WHERE  campos.nome_arquivo_pertencente = 'BO'
					   AND  campos.tipo_registro = 10
					 GROUP  BY campos.seq_arquivo, campos.nome_tag
					 ORDER  BY campos.seq_arquivo";
	    }

	    public function __destruct(){}

	}