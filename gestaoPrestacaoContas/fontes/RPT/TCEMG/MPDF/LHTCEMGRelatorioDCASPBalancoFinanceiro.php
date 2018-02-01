<div class="text_align_right margin-bottom-30"> EXERCÍDIO: <?= $_data['exercicio'] ?> </div>
<div class="text_align_center"> BALANÇO FINANCEIRO </div>
<div class="text_align_center margin-bottom-30"> <b> INGRESSOS </b> </div>

<table>
	<thead>
		<tr>
			<th width="60%" style="background: none;"></th>
			<th width="10%" style="background: none;"> <b> Nota </b> </th>
			<th width="15%" style="background: none;"> 
				<p> <b> Exercício </b> </p>
				<p> <b> Atual </b> </p>
			</th>
			<th width="15%" style="background: none;"> 
				<p> <b> Exercício </b> </p>
				<p> <b> Anterior </b> </p>
			</th>
		</tr>
	</thead>

	<tbody>
		<tr>
			<td> <b> Receita Orçamentária (I) </b> </td>
		</tr>
		
		<tr>
			<td class="padding-left-30"> <b> Ordinária </b> </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_rec_orc_recurso_ordinario_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_rec_orc_recurso_ordinario_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-30"> <b> Vinculada </b> </td>
		</tr>

		<tr>
			<td> Recursos Vinculados à Educação </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_rec_orc_recursos_vinculado_educacao_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_rec_orc_recursos_vinculado_educacao_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>
		
		<tr>
			<td> Recursos Vinculados à Saúde </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_rec_orc_recursos_vinculado_saude_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_rec_orc_recursos_vinculado_saude_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td> Recursos Vinculados à Previdência Social - RPPS </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_rec_orc_recursos_vinculado_rpps_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_rec_orc_recursos_vinculado_rpps_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td> Recursos Vinculados à Previdência Social - RGPS </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_rec_orc_recursos_vinculado_rgps_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_rec_orc_recursos_vinculado_rgps_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td> Recursos Vinculados à Assistencia Social </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_rec_orc_recursos_vinculado_assist_social_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_rec_orc_recursos_vinculado_assist_social_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td> Outras Destinações de Recursos </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_rec_orc_outra_destinac_recurso_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_rec_orc_outra_destinac_recurso_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td>
				<td class="height-20" colspan="4"> </td>
			</td>
		</tr>

		<tr>
			<td> <b> Transferências Financeiras Concedidas (VII) </b> </td>
		</tr>

		<tr>
			<td> Transferências Concedidas para a Execução Orçamentária </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_trans_finan_execucao_orcamentaria_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_trans_finan_execucao_orcamentaria_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>
		
		<tr>
			<td> Transferências Concedidas Independentes de Execução Orçamentária </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_trans_finan_indepen_execucao_orcamentaria_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_trans_finan_indepen_execucao_orcamentaria_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td> Transferências Concedidas para Aportes de recursos para o RPPS </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_trans_finan_recebida_aporte_rec_rpps_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_trans_finan_recebida_aporte_rec_rpps_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td> Transferências Concedidas para Aportes de recursos para o RGPS </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_trans_finan_recebida_aporte_rec_rgps_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_trans_finan_recebida_aporte_rec_rgps_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td>
				<td class="height-20" colspan="4"> </td>
			</td>
		</tr>

		<tr>
			<td> <b> Pagamentos Extraorçamentários (VIII) </b> </td>
		</tr>

		<tr>
			<td> Pagamentos de Restos a Pagar Não Processados </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_inscri_resto_pagar_nao_processado_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_inscri_resto_pagar_nao_processado_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td> Pagamentos de Restos a Pagar Processados </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_inscri_resto_pagar_processado_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_inscri_resto_pagar_processado_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td> Depósitos Restituíveis e Valores Vinculados </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_depo_restituivel_vinculado_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_depo_restituivel_vinculado_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td> Outros Pagamentos Orçamentários </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_outr_recebimento_extraorcamentario_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_outr_recebimento_extraorcamentario_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td>
				<td class="height-20" colspan="4"> </td>
			</td>
		</tr>

		<tr>
			<td> <b> Saldo para o Exercício Seguinte (IX) </b> </td>
		</tr>

		<tr>
			<td> Caixa e Equivalentes de Caixa </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_sal_exerc_anterior_caixa_equivalente_caixa_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_sal_exerc_anterior_caixa_equivalente_caixa_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>
		
		<tr>
			<td> Depósitos restituíveis e Valores Vinculados </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_sal_exerc_anterior_deposito_restitui_valor_vinculado_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_sal_exerc_anterior_deposito_restitui_valor_vinculado_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td>
				<td class="height-20" colspan="4"> </td>
			</td>
		</tr>

		<tr>
			<td> <b> TOTAL (X) = (VI + VII + VIII + IX) </b> </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_total_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_total_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>
	</tbody>
</table>

<pagebreak></pagebreak>

<div class="text_align_right margin-bottom-30"> EXERCÍDIO: <?= $_data['exercicio'] ?> </div>
<div class="text_align_center margin-bottom-30"> <b> DISPÊNDIOS </b> </div>

<table>
	<thead>
		<tr>
			<th width="60%" style="background: none;"></th>
			<th width="10%" style="background: none;"> <b> Nota </b> </th>
			<th width="15%" style="background: none;"> 
				<p> <b> Exercício </b> </p>
				<p> <b> Atual </b> </p>
			</th>
			<th width="15%" style="background: none;"> 
				<p> <b> Exercício </b> </p>
				<p> <b> Anterior </b> </p>
			</th>
		</tr>
	</thead>

	<tbody>
		<tr>
			<td> <b> Despesa Orçamentária (I) </b> </td>
		</tr>
		
		<tr>
			<td class="padding-left-30"> <b> Ordinária </b> </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_desp_orc_recurso_ordinario_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_desp_orc_recurso_ordinario_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-30"> <b> Vinculada </b> </td>
		</tr>

		<tr>
			<td> Recursos Vinculados à Educação </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_desp_orc_recursos_vinculado_educacao_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_desp_orc_recursos_vinculado_educacao_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>
		
		<tr>
			<td> Recursos Vinculados à Saúde </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_desp_orc_recursos_vinculado_saude_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_desp_orc_recursos_vinculado_saude_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td> Recursos Vinculados à Previdência Social - RPPS </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_desp_orc_recursos_vinculado_rpps_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_desp_orc_recursos_vinculado_rpps_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td> Recursos Vinculados à Previdência Social - RGPS </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_desp_orc_recursos_vinculado_rgps_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_desp_orc_recursos_vinculado_rgps_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td> Recursos Vinculados à Assistencia Social </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_desp_orc_recursos_vinculado_assist_social_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_desp_orc_recursos_vinculado_assist_social_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td> Outras Destinações de Recursos </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_desp_orc_outra_destinac_recurso_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_desp_orc_outra_destinac_recurso_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td>
				<td class="height-20" colspan="4"> </td>
			</td>
		</tr>

		<tr>
			<td> <b> Transferências Financeiras Concedidas (VII) </b> </td>
		</tr>

		<tr>
			<td> Transferências Concedidas para a Execução Orçamentária </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_trans_finan_execucao_orcamentaria_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_trans_finan_execucao_orcamentaria_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>
		
		<tr>
			<td> Transferências Concedidas Independentes de Execução Orçamentária </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_trans_finan_indepen_execucao_orcamentaria_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_trans_finan_indepen_execucao_orcamentaria_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td> Transferências Concedidas para Aportes de recursos para o RPPS </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_trans_finan_recebida_aporte_rec_rpps_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_trans_finan_recebida_aporte_rec_rpps_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td> Transferências Concedidas para Aportes de recursos para o RGPS </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_trans_finan_recebida_aporte_rec_rgps_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_trans_finan_recebida_aporte_rec_rgps_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td>
				<td class="height-20" colspan="4"> </td>
			</td>
		</tr>

		<tr>
			<td> <b> Pagamentos Extraorçamentários (VIII) </b> </td>
		</tr>

		<tr>
			<td> Pagamentos de Restos a Pagar Não Processados </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_pag_restos_pagar_nao_processado_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_pag_restos_pagar_nao_processado_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td> Pagamentos de Restos a Pagar Processados </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_pag_restos_pagar_processado_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_pag_restos_pagar_processado_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td> Depósitos Restituíveis e Valores Vinculados </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_depo_restituivel_vinculado_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_depo_restituivel_vinculado_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td> Outros Pagamentos Orçamentários </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_outr_pagamento_extraorcamentario_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_outr_pagamento_extraorcamentario_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td>
				<td class="height-20" colspan="4"> </td>
			</td>
		</tr>

		<tr>
			<td> <b> Saldo para o Exercício Seguinte (IX) </b> </td>
		</tr>

		<tr>
			<td> Caixa e Equivalentes de Caixa </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_sal_exerc_anterior_caixa_equivalente_caixa_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_sal_exerc_anterior_caixa_equivalente_caixa_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>
		
		<tr>
			<td> Depósitos restituíveis e Valores Vinculados </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_sal_exerc_anterior_deposito_restitui_valor_vinculado_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_sal_exerc_anterior_deposito_restitui_valor_vinculado_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td>
				<td class="height-20" colspan="4"> </td>
			</td>
		</tr>

		<tr>
			<td> <b> TOTAL (X) = (VI + VII + VIII + IX) </b> </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_total_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_total_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>
	</tbody>
</table>
