<div class="text_align_center"> <b> DEMONSTRAÇÃO DAS VARIAÇÕES PATRIMONIAIS </b> </div>
<div class="text_align_right margin-bottom-30"> Exercício: <?= $_data['exercicio'] ?> </div>

<table>
	<thead>
		<tr>
			<th width="40%" style="background: none;"></th>
			<th width="20%" style="background: none;"> <b> Nota </b> </th>
			<th width="20%" style="background: none;"> <b> Exercício Atual </b> </th>
			<th width="20%" style="background: none;"> <b> Exercício Anterior </b> </th>
		</tr>
		<tr>
			<th class="text_align_left" style="font-weight: normal; background: none;"> <b> Variações Patrimoniais Aumentativas </b> </th>
			<th colspan="3" style="background: none;"></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="padding-left-15"> Impostos, Taxas e Contribuições de Melhoria </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_impostos_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_impostos_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>
		
		<tr>
			<td class="padding-left-15"> Contribuições </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_contribuicoes_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_contribuicoes_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-15"> Exploração e Venda de Bens, Serviçõs e Direitos </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_exploracao_vendas_direitos_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_exploracao_vendas_direitos_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td colspan="4" class="height-10"></td>
		</tr>

		<tr>
			<td class="padding-left-15"> Variações Patrimoniais Aumentativas Financeiras </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_variacoes_aumentativas_financeiras_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_variacoes_aumentativas_financeiras_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td colspan="4" class="height-10"></td>
		</tr>

		<tr>
			<td class="padding-left-15"> Transferências e Delegações Recebidas </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_transf_delegacoes_recebidas_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_transf_delegacoes_recebidas_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-15"> Valorização e Ganhos com Ativos e Desincorporação de Passivos </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_valorizacao_ativo_desincor_passivo_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_valorizacao_ativo_desincor_passivo_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-15"> Outras Variações Patrimoniais Aumentativas </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_outras_variacoes_patri_aumentativas_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_outras_variacoes_patri_aumentativas_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="italico"> <b> Total das Variações Patrimoniais Aumentativas (I) </b> </td>
			<td></td>
			<td class="text_align_right border_top"> <?= number_format($_data[10]['vl_total_vp_aumentativas_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right border_top"> <?= number_format($_data[10]['vl_total_vp_aumentativas_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td colspan="4" class="height-10"></td>
		</tr>

		<tr>
			<td> <b> Variações Patrimoniais Diminutivas </b></td>
			<td colspan="3"></td>
		</tr>

		<tr>
			<td class="padding-left-15"> Pessoal e Encargos </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_diminutiva_pessoa_encargos_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_diminutiva_pessoa_encargos_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-15"> Beneficios Previdenciários e Assistenciais </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_prev_assistenciais_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_prev_assistenciais_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-15"> Uso de Bens, Serviços e Consumo de Capital Fixo </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_servico_capital_fixo_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_servico_capital_fixo_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td colspan="4" class="height-10"></td>
		</tr>

		<tr>
			<td class="padding-left-15"> Variações Patrimoniais Diminutivas Financeiras </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_diminutiva_variacoes_financeiras_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_diminutiva_variacoes_financeiras_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>
		
		<tr>
			<td class="padding-left-15"> Transferências e Delegações Concedidas </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_transf_concedidas_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_transf_concedidas_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-15"> Desvalorização e Perdas de Ativos e Incorporação de Passivos </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_desvalo_ativo_incorpo_passivo_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_desvalo_ativo_incorpo_passivo_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-15"> Tributárias </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_tributarias_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_tributarias_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-15"> Custo das Mercadorias e Produtos Vendidos, e dos Serviços Prestados </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_mercadoria_vendido_servicos_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_mercadoria_vendido_servicos_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-15"> Outras Variações Patrimoniais Diminutivas </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_outras_variacoes_patri_diminutivas_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_outras_variacoes_patri_diminutivas_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="italico"> <b> Total das Variações Patrimoniais Diminutivas (II) </b> </td>
			<td></td>
			<td class="border_top text_align_right"> <?= number_format($_data[20]['vl_total_vp_diminutivas_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="border_top text_align_right"> <?= number_format($_data[20]['vl_total_vp_diminutivas_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td colspan="4" class="height-10"></td>
		</tr>

		<tr>
			<td colspan="4" class="height-10"></td>
		</tr>

		<tr>
			<td> <b> RESULTADO PATRIMONIAL DO PERÍODO (III) = (I - II)</b></td>
			<td></td>
			<td class="border_top text_align_right"> <?= number_format($_data['vl_resultado_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="border_top text_align_right"> <?= number_format($_data['vl_resultado_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>
	</tbody>
</table>
