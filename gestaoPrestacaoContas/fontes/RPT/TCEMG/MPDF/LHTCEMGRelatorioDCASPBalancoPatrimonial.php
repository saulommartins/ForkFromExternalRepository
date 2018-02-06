<div class="text_align_center"> <b> BALANÇO PATRIMONIAL </b> </div>
<div class="text_align_right margin-bottom-30"> Exercício: <?= $_data['exercicio'] ?> </div>

<table>
	<thead>
		<tr>
			<th width="40%" style="background: none;"> ATIVO </th>
			<th width="20%" style="background: none;"> <b> Nota </b> </th>
			<th width="20%" style="background: none;"> <b> Exercício Atual </b> </th>
			<th width="20%" style="background: none;"> <b> Exercício Anterior </b> </th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td> <b> Ativo Circulante </b></td>
			<td colspan="3"></td>
		</tr>

		<tr>
			<td class="padding-left-15"> Caixa e Equivalentes de Caixa </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_ativo_circ_caixa_equival_caixa_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_ativo_circ_caixa_equival_caixa_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>
		
		<tr>
			<td class="padding-left-15"> Créditos a Curto Prazo </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_ativo_circ_cred_curto_prazo_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_ativo_circ_cred_curto_prazo_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-15"> Investimentos e Aplicações Temporárias a Curto Prazo </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_ativo_circ_invest_aplic_temp_curto_prazo_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_ativo_circ_invest_aplic_temp_curto_prazo_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-15"> Estoques </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_ativo_circ_estoque_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_ativo_circ_estoque_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-15"> VPD Pagas Antecipadamente </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_ativo_circ_vpd_paga_antecipado_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_ativo_circ_vpd_paga_antecipado_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="italico"> <b> Total do Ativo Circulante </b> </td>
			<td></td>
			<td class="text_align_right border_top"> <?= number_format($_data[10]['total_ativo_circulante_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right border_top"> <?= number_format($_data[10]['total_ativo_circulante_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="height-10"></td>
		</tr>

		<tr>
			<td> <b> Ativo Não Circulante </b></td>
			<td colspan="3"></td>
		</tr>

		<tr>
			<td class="padding-left-15"> Realizável a Longo Prazo </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_ativo_nao_circ_real_longo_prazo_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_ativo_nao_circ_real_longo_prazo_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>
		
		<tr>
			<td class="padding-left-15"> Créditos a Longo Prazo </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_ativo_nao_circ_cred_longo_prazo_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_ativo_nao_circ_cred_longo_prazo_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-15"> Investimentos Temporários a Longo Prazo </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_ativo_nao_circ_invest_temp_longo_prazo_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_ativo_nao_circ_invest_temp_longo_prazo_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-15"> Estoques </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_ativo_nao_circ_estoque_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_ativo_nao_circ_estoque_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-15"> VPD Pagas Antecipadamente </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_ativo_nao_circ_vpd_pago_antecipado_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_ativo_nao_circ_vpd_pago_antecipado_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-15"> Investimentos </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_ativo_nao_circ_investimento_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_ativo_nao_circ_investimento_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-15"> Imobilizado </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_ativo_nao_circ_imobilizado_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_ativo_nao_circ_imobilizado_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-15"> Intangível </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_ativo_nao_circ_intangivel_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_ativo_nao_circ_intangivel_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="italico"> <b> Total do Ativo Não Circulante </b> </td>
			<td></td>
			<td class="text_align_right border_top"> <?= number_format($_data[10]['total_ativo_nao_circulante_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right border_top"> <?= number_format($_data[10]['total_ativo_nao_circulante_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>
		
		<tr>
			<td class="height-10"></td>
		</tr>

		<tr>
			<td class="italico"> <b> TOTAL DO ATIVO </b> </td>
			<td></td>
			<td class="text_align_right border_top"> <?= number_format($_data[10]['total_ativo_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right border_top"> <?= number_format($_data[10]['total_ativo_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="height-10"></td>
		</tr>

		<tr>
			<td> PASSIVO E PATRIMÔNIO LÍQUIDO </td>
			<td colspan="3"></td>
		</tr>

		<tr>
			<td class="height-10"></td>
		</tr>

		<tr>
			<td> <b> Passivo Circulante </b> </td>
			<td colspan="3"></td>
		</tr>

		<tr>
			<td class="padding-left-15"> Obrigações Trab., Prev. e Assistencias a Pagar a Curto Prazo </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_pass_circ_obrig_trab_previdenc_assist_pagar_curto_prazo_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_pass_circ_obrig_trab_previdenc_assist_pagar_curto_prazo_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>
		
		<tr>
			<td class="padding-left-15"> Empréstimos e Financiamentos a Curto Prazo </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_pass_circ_emprest_financ_curto_prazo_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_pass_circ_emprest_financ_curto_prazo_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>
		
		<tr>
			<td class="padding-left-15"> Fornecedores e Contas a Pagar a Curto Prazo </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_pass_circ_fornec_contas_pagar_curto_prazo_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_pass_circ_fornec_contas_pagar_curto_prazo_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-15"> Obrigações Fiscais a Curto Prazo </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_pass_circ_obrig_fiscais_curto_prazo_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_pass_circ_obrig_fiscais_curto_prazo_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-15"> Obrigações de Repartições a Outros Entes </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_pass_circ_obrig_repart_outros_entes_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_pass_circ_obrig_repart_outros_entes_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-15"> Provisões a Curto Prazo </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_pass_circ_provisoes_curto_prazo_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_pass_circ_provisoes_curto_prazo_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-15"> Demais Obrigações a Curto Prazo </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_pass_circ_dms_obrigacoes_curto_prazo_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_pass_circ_dms_obrigacoes_curto_prazo_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td> <b> Total do Passivo Circulante </b> </td>
			<td></td>
			<td class="text_align_right border_top"><?= number_format($_data[20]['total_passivo_circulante_exercicio_atual'], 2, ',', '.') ?></td>
			<td class="text_align_right border_top"><?= number_format($_data[20]['total_passivo_circulante_exercicio_anterior'], 2, ',', '.') ?></td>
		</tr>

		<tr>
			<td class="height-10"></td>
		</tr>

		<tr>
			<td> <b> Passivo Não Circulante </b> </td>
			<td colspan="3"></td>
		</tr>

		<tr>
			<td class="padding-left-15"> Obrigações Trab., Prev. e Assistenciais a Pagar a Longo Prazo </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_pass_nao_circ_obrig_trab_prev_assist_pagar_longro_prazo_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_pass_nao_circ_obrig_trab_prev_assist_pagar_longro_prazo_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-15"> Empréstimos e Financiamentos a Longo Prazo </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_pass_nao_circ_emp_financ_longo_prazo_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_pass_nao_circ_emp_financ_longo_prazo_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-15"> Fornecedores e Contas a Pagar a Longo Prazo </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_pass_nao_circ_fornec_contas_pagar_longo_prazo_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_pass_nao_circ_fornec_contas_pagar_longo_prazo_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-15"> Obrigações Fiscais a Longo Prazo </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_pass_nao_circ_obrig_fisc_longo_prazo_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_pass_nao_circ_obrig_fisc_longo_prazo_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-15"> Provisões a Longo Prazo </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_pass_nao_circ_provisoes_longo_prazo_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_pass_nao_circ_provisoes_longo_prazo_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-15"> Demais Obrigações a Longo Prazo </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_pass_nao_circ_dms_obrig_longo_prazo_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_pass_nao_circ_dms_obrig_longo_prazo_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-15"> Resultado Diferado </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_pass_nao_circ_resultado_deferido_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_pass_nao_circ_resultado_deferido_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td> <b> Total do Passivo Não Circulante </b> </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['total_passivo_nao_circulante_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['total_passivo_nao_circulante_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>
		
		<tr>
			<td class="height-10"></td>
		</tr>

		<tr>
			<td> <b> Patrimônio Líquido </b> </td>
			<td colspan="3"></td>
		</tr>

		<tr>
			<td class="padding-left-15"> Patrimônio Social e Capital Social </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_patri_liq_patr_social_capital_social_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_patri_liq_patr_social_capital_social_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>
		
		<tr>
			<td class="padding-left-15"> Adiantamento Para Futuro Aumento de Capital </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_patri_liq_adiant_futuro_aumento_capital_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_patri_liq_adiant_futuro_aumento_capital_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>
		
		<tr>
			<td class="padding-left-15"> Reservas de Capital </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_patri_liq_reservas_capital_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_patri_liq_reservas_capital_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-15"> Ajustes de Avaliação Patrimonial </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_patri_liq_ajuste_aval_patrimonial_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_patri_liq_ajuste_aval_patrimonial_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>
		
		<tr>
			<td class="padding-left-15"> Reservas de Lucros </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_patri_liq_reservas_lucros_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_patri_liq_reservas_lucros_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-15"> Demais Reservas </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_patri_liq_demais_reservas_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_patri_liq_demais_reservas_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>
		
		<tr>
			<td class="padding-left-15"> Resultados Acumulados </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_patri_liq_resultado_exercicio_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_patri_liq_resultado_exercicio_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>
		
		<tr>
			<td class="padding-left-15"> (-) Ações/Cotas em Tesouraria </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_patri_liq_acoes_cotas_tesouraria_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_patri_liq_acoes_cotas_tesouraria_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>
		
		<tr>
			<td class="italico"> <b> Total do Patrimônio Líquido </b> </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['total_patrimonio_liquido_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['total_patrimonio_liquido_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>
		
		<tr>
			<td class="height-10"></td>
		</tr>

		<tr>
			<td> <b> TOTAL DO PASSIVO E DO PATRIMÔNIO LÍQUIDO </b> </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['total_passivo_patrimonio_liquido_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['total_passivo_patrimonio_liquido_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>
		
	</tbody>
</table>

<pagebreak></pagebreak>

<div class="text_align_center">
	<p> <b> QUADRO DOS ATIVOS E PASSIVOS FINANCEIROS E PERMANENTES </b> </p>
	<p> <b> (Lei nº 4.320/1964) </b> </p>
</div>

<div class="text_align_right margin-bottom-30"> Exercício: <?= $_data['exercicio'] ?> </div>

<table>
	<thead>
		<tr>
			<th width="40%" style="background: none;"> </th>
			<th width="20%" style="background: none;"> </th>
			<th width="20%" style="background: none;"> <b> Exercício Atual </b> </th>
			<th width="20%" style="background: none;"> <b> Exercício Anterior </b> </th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td> <b> Ativo (I) </b></td>
			<td colspan="3"></td>
		</tr>

		<tr>
			<td class="padding-left-15"> Ativo Financeiro </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[30]['vl_ativo_financeiro_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[30]['vl_ativo_financeiro_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-15"> Ativo Permanente </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[30]['vl_ativo_permanente_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[30]['vl_ativo_permanente_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="italico"> <b> Total do Ativo </b> </td>
			<td></td>
			<td class="border_top text_align_right"> <?= number_format($_data[30]['total_ativo_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="border_top text_align_right"> <?= number_format($_data[30]['total_ativo_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>
		
		<tr>
			<td class="height-10"></td>
		</tr>

		<tr>
			<td> <b> Passivo (II) </b></td>
			<td colspan="3"></td>
		</tr>

		<tr>
			<td class="padding-left-15"> Passivo Financeiro </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[40]['vl_passivo_financeiro_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[40]['vl_passivo_financeiro_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-15"> Passivo Permanente </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[40]['vl_passivo_permanente_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[40]['vl_passivo_permanente_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="italico"> <b> Total do Passivo </b> </td>
			<td></td>
			<td class="border_top text_align_right"> <?= number_format($_data[40]['total_passivo_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="border_top text_align_right"> <?= number_format($_data[40]['total_passivo_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="height-10"></td>
		</tr>

		<tr>
			<td> <b> Saldo Patrimonial (III) = (I - II) </b> </td>
			<td></td>
			<td class="border_top text_align_right"> <?= number_format($_data[40]['saldo_patrimonial_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="border_top text_align_right"> <?= number_format($_data[40]['saldo_patrimonial_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>
	</tbody>
</table>

<div class="height-10"></div>

<div class="text_align_center">
	<p> <b> QUADRO DAS CONTAS DE COMPENSAÇÃO </b> </p>
	<p> <b> (Lei nº 4.320/1964) </b> </p>
</div>

<div class="text_align_right margin-bottom-30"> Exercício: <?= $_data['exercicio'] ?> </div>

<table>
	<thead>
		<tr>
			<th width="60%" style="background: none;"></th>
			<th width="20%" style="background: none;"> <b> Exercício Atual </b> </th>
			<th width="20%" style="background: none;"> <b> Exercício Anterior </b> </th>
		</tr>
	</thead>

	<tbody>
		<tr>
			<td> <b> Atos Potenciais Ativos </b> </td>
			<td colspan="2"></td>
		</tr>

		<tr>
			<td class="padding-left-15"> Garantias e Contragarantias recebidas </td>
			<td class="text_align_right"> <?= number_format($_data[60]['vl_ato_poten_ativ_garan_contragaran_recebida_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[60]['vl_ato_poten_ativ_garan_contragaran_recebida_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>
		
		<tr>
			<td class="padding-left-15"> Direitos Conveniados e outros instrumentos congêneres </td>
			<td class="text_align_right"> <?= number_format($_data[60]['vl_ato_poten_ativ_dir_conven_outros_instru_congeneres_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[60]['vl_ato_poten_ativ_dir_conven_outros_instru_congeneres_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-15"> Direitos Contratuais </td>
			<td class="text_align_right"> <?= number_format($_data[60]['vl_ato_poten_ativ_direitos_contratuais_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[60]['vl_ato_poten_ativ_direitos_contratuais_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-15"> Outros atos potenciais ativos </td>
			<td class="text_align_right"> <?= number_format($_data[60]['vl_ato_poten_ativ_outros_atos_potenc_ativo_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[60]['vl_ato_poten_ativ_outros_atos_potenc_ativo_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="italico"> <b> Total dos Atos Potenciais Ativos </b> </td>
			<td class="text_align_right border_top"> <?= number_format($_data[60]['total_atos_potenciais_ativos_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right border_top"> <?= number_format($_data[60]['total_atos_potenciais_ativos_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td> <b> Atos Potenciais Passivos </b> </td>
			<td colspan="2"></td>
		</tr>

		<tr>
			<td class="padding-left-15"> Garantias e Contragarantias concedias </td>
			<td class="text_align_right"> <?= number_format($_data[60]['vl_ato_poten_pass_garan_contragaran_concedida_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[60]['vl_ato_poten_pass_garan_contragaran_concedida_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>
		
		<tr>
			<td class="padding-left-15"> Obrigações conveniadas e outros instrumentos congêneres </td>
			<td class="text_align_right"> <?= number_format($_data[60]['vl_ato_poten_pass_obrig_conven_outros_instru_congeneres_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[60]['vl_ato_poten_pass_obrig_conven_outros_instru_congeneres_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-15"> Obrigações contratuais </td>
			<td class="text_align_right"> <?= number_format($_data[60]['vl_ato_poten_pass_obrigacoes_contratuais_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[60]['vl_ato_poten_pass_obrigacoes_contratuais_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-15"> Outros atos potenciais passivos </td>
			<td class="text_align_right"> <?= number_format($_data[60]['vl_ato_poten_pass_outros_atos_potenc_passivo_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[60]['vl_ato_poten_pass_outros_atos_potenc_passivo_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="italico"> <b> Total dos Atos Potenciais Passivos </b> </td>
			<td class="text_align_right border_top"> <?= number_format($_data[60]['total_atos_potenciais_passivos_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right border_top"> <?= number_format($_data[60]['total_atos_potenciais_passivos_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>


	</tbody>
</table>

<pagebreak></pagebreak>

<div class="text_align_center">
	<p> <b> QUADRO DO SUPERÁVIT/DÉFICIT FINANCEIRO </b> </p>
	<p> <b> (Lei nº 4.320/1964) </b> </p>
</div>

<div class="text_align_right margin-bottom-30"> Exercício: <?= $_data['exercicio'] ?> </div>

<table>
	<thead>
		<tr>
			<th width="10%" style="background: none;"></th>
			<th width="50%" style="background: none;"></th>
			<th width="20%" style="background: none;"> <b> Exercício Atual </b> </th>
			<th width="20%" style="background: none;"> <b> Exercício Anterior </b> </th>
		</tr>
	</thead>

	<tbody>
		<tr>
			<td colspan="2" class="text_align_center"> FONTES DE RECURSOS </td>
			<td colspan="2"></td>
		</tr>

		<?php
			$totalExercicioAtual = 0;
			$totalExercicioAnterior = 0;

			foreach ($_data[71] as $key => $recurso) {

				$totalExercicioAtual += $recurso['exercicio_atual'];
				$totalExercicioAnterior += $recurso['exercicio_anterior'];

				?>
					<tr>
						<td class="text_align_center"> <?= $recurso['cod_recurso'] ?> </td>
						<td> <?= $recurso['nom_recurso'] ?> </td>
						<td class="text_align_right"> <?= number_format($recurso['exercicio_atual'], 2, ',', '.') ?> </td>
						<td class="text_align_right"> <?= number_format($recurso['exercicio_anterior'], 2, ',', '.') ?> </td>
					</tr>
				<?php
			}
		?>

		<tr>
			<td colspan="2" class="italico"> <b> Total das Fontes de Recursos </b> </td>
			<td class="text_align_right border_top"> <?= number_format($totalExercicioAtual, 2, ',', '.') ?> </td>
			<td class="text_align_right border_top"> <?= number_format($totalExercicioAnterior, 2, ',', '.') ?> </td>
		</tr>
	</tbody>
</table>
