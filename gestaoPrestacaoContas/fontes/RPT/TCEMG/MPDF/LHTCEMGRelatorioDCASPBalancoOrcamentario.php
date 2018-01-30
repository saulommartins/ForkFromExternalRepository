<div class="text_align_center"> <b> BALANÇO ORÇAMENTÁRIO </b> </div>
<div class="text_align_center margin-bottom-30"> ORÇAMENTOS FISCAL E DA SEGURIDADE SOCIAL </div>

<table class="separated">
	<thead>
		<tr>
			<th width="40%" style="background: none;"> 
				RECEITAS ORÇAMENTÁRIAS
			</th>
			<th width="10%" style="background: none;"> 
				<p> Previsão </p>
				<p> Inicial </p>
				(a)
			</th>
			<th width="10%" style="background: none;">
				<p> Previsão </p>
				<p> Atualizada </p>
				(b)
			</th>
			<th width="10%" style="background: none;"> 
				<p> Receitas Realizadas </p>
				(c) 
			</th>
			<th width="10%" style="background: none;"> 
				<p> Saldo </p>
				<p> (d) = (c-b) </p>
			</th>
		</tr>
	</thead>

	<tbody>
		<tr>
			<td> <b> Receitas Correntes (I) </b> </td>
			<td colspan="4"></td>
		</tr>
		
		<tr>
			<td class="padding-left-30"> Receita Tributária </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_rec_tributaria_previsao_inicial'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_rec_tributaria_previsao_atualizada'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_rec_tributaria_realizada'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_rec_tributaria_realizada'] - $_data[10]['vl_rec_tributaria_previsao_atualizada'], 2, ',', '.') ?> </td>
		</tr>
		
		<tr>
			<td class="padding-left-30"> Receita de Contribuições </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_rec_contribuicoes_previsao_inicial'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_rec_contribuicoes_previsao_atualizada'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_rec_contribuicoes_realizada'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_rec_contribuicoes_realizada'] - $_data[10]['vl_rec_contribuicoes_previsao_atualizada'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-30"> Receita Patrimonial </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_rec_patrimonial_previsao_inicial'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_rec_patrimonial_previsao_atualizada'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_rec_patrimonial_realizada'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_rec_patrimonial_realizada'] - $_data[10]['vl_rec_patrimonial_previsao_atualizada'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-30"> Receita Agropecuária </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_rec_agropecuaria_previsao_inicial'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_rec_agropecuaria_previsao_atualizada'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_rec_agropecuaria_realizada'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_rec_agropecuaria_realizada'] - $_data[10]['vl_rec_agropecuaria_previsao_atualizada'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-30"> Receita Industrial </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_rec_industrial_previsao_inicial'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_rec_industrial_previsao_atualizada'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_rec_industrial_realizada'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_rec_industrial_realizada'] - $_data[10]['vl_rec_industrial_previsao_atualizada'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-30"> Receita de Serviços </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_rec_servicos_previsao_inicial'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_rec_servicos_previsao_atualizada'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_rec_servicos_realizada'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_rec_servicos_realizada'] - $_data[10]['vl_rec_servicos_previsao_atualizada'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-30"> Transferências Correntes </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_transf_correntes_previsao_inicial'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_transf_correntes_previsao_atualizada'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_transf_correntes_realizada'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_transf_correntes_realizada'] - $_data[10]['vl_transf_correntes_previsao_atualizada'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-30"> Outras Receitas Correntes </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_outras_rec_correntes_previsao_inicial'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_outras_rec_correntes_previsao_atualizada'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_outras_rec_correntes_realizada'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_outras_rec_correntes_realizada'] - $_data[10]['vl_outras_rec_correntes_previsao_atualizada'], 2, ',', '.') ?> </td>
		</tr>
		
		<tr>
			<td class="height-10" colspan="5"> </td>
		</tr>

		<tr>
			<td> <b> Receitas de Capital (II) </b> </td>
			<td colspan="4"></td>
		</tr>
		
		<tr>
			<td class="padding-left-30"> Operações de Crédito </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_operacoes_credito_previsao_inicial'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_operacoes_credito_previsao_atualizada'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_operacoes_credito_realizada'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_operacoes_credito_realizada'] - $_data[10]['vl_operacoes_credito_previsao_atualizada'], 2, ',', '.') ?> </td>
		</tr>
		
		<tr>
			<td class="padding-left-30"> Alienação de Bens </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_alienacao_bens_previsao_inicial'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_alienacao_bens_previsao_atualizada'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_alienacao_bens_realizada'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_alienacao_bens_realizada'] - $_data[10]['vl_alienacao_bens_previsao_atualizada'], 2, ',', '.') ?> </td>
		</tr>
		
		<tr>
			<td class="padding-left-30"> Amortizações de Empréstimos </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_amortizacao_emprestimo_previsao_inicial'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_amortizacao_emprestimo_previsao_atualizada'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_amortizacao_emprestimo_realizada'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_amortizacao_emprestimo_realizada'] - $_data[10]['vl_amortizacao_emprestimo_previsao_atualizada'], 2, ',', '.') ?> </td>
		</tr>
		
		<tr>
			<td class="padding-left-30"> Transferências de Capital </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_transf_capital_previsao_inicial'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_transf_capital_previsao_atualizada'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_transf_capital_realizada'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_transf_capital_realizada'] - $_data[10]['vl_transf_capital_previsao_atualizada'], 2, ',', '.') ?> </td>
		</tr>
		
		<tr>
			<td class="padding-left-30"> Outras Receitas de Capital </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_outras_rec_capital_previsao_inicial'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_outras_rec_capital_previsao_atualizada'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_outras_rec_capital_realizada'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_outras_rec_capital_realizada'] - $_data[10]['vl_outras_rec_capital_previsao_atualizada'], 2, ',', '.') ?> </td>
		</tr>
		
		<tr>
			<td class="height-10" colspan="5"> </td>
		</tr>

		<tr>
			<td> <b> SUBTOTAL DAS RECEITAS (III) = (I + II) </b> </td>
			<td class="text_align_right border_top"> <?= number_format($_data[10]['vl_subtotal_previsao_inicial'], 2, ',', '.') ?> </td>
			<td class="text_align_right border_top"> <?= number_format($_data[10]['vl_subtotal_previsao_atualizada'], 2, ',', '.') ?> </td>
			<td class="text_align_right border_top"> <?= number_format($_data[10]['vl_subtotal_realizada'], 2, ',', '.') ?> </td>
			<td class="text_align_right border_top"> <?= number_format($_data[10]['vl_subtotal_saldo'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="height-10" colspan="5"> </td>
		</tr>

		<tr>
			<td> <b> Operações de Crédito / Refinanciamento (IV) </b> </td>
			<td colspan="4"></td>
		</tr>

		<tr>
			<td class="padding-left-30"> Operações de Cŕedito Internas </td>
			<td colspan="4"></td>
		</tr>

		<tr>
			<td class="padding-left-60"> Mobiliária </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_opera_credito_refina_internas_mobiliaria_previsao_inicial'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_opera_credito_refina_internas_mobiliaria_previsao_atualizada'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_opera_credito_refina_internas_mobiliaria_realizada'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_opera_credito_refina_internas_mobiliaria_realizada'] - $_data[10]['vl_opera_credito_refina_internas_mobiliaria_previsao_atualizada'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-60"> Contratual </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_opera_credito_refina_internas_contratual_previsao_inicial'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_opera_credito_refina_internas_contratual_previsao_atualizada'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_opera_credito_refina_internas_contratual_realizada'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_opera_credito_refina_internas_contratual_realizada'] - $_data[10]['vl_opera_credito_refina_internas_contratual_previsao_atualizada'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-30"> Operações de Cŕedito Externas </td>
			<td colspan="4"></td>
		</tr>

		<tr>
			<td class="padding-left-60"> Mobiliária </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_opera_credito_refina_externas_mobiliaria_previsao_inicial'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_opera_credito_refina_externas_mobiliaria_previsao_atualizada'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_opera_credito_refina_externas_mobiliaria_realizada'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_opera_credito_refina_externas_mobiliaria_realizada'] - $_data[10]['vl_opera_credito_refina_externas_mobiliaria_previsao_atualizada'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-60"> Contratual </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_opera_credito_refina_externas_contratual_previsao_inicial'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_opera_credito_refina_externas_contratual_previsao_atualizada'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_opera_credito_refina_externas_contratual_realizada'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_opera_credito_refina_externas_contratual_realizada'] - $_data[10]['vl_opera_credito_refina_externas_contratual_previsao_atualizada'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="height-10" colspan="5"> </td>
		</tr>

		<tr>
			<td> <b> SUBTOTAL COM REFINANCIAMENTO (V) = (III + IV) </b> </td>
			<td class="text_align_right border_top"> <?= number_format($_data[10]['vl_subtotal_com_refinanciamento_previsao_inicial'], 2, ',', '.') ?> </td>
			<td class="text_align_right border_top"> <?= number_format($_data[10]['vl_subtotal_com_refinanciamento_previsao_atualizada'], 2, ',', '.') ?> </td>
			<td class="text_align_right border_top"> <?= number_format($_data[10]['vl_subtotal_com_refinanciamento_realizada'], 2, ',', '.') ?> </td>
			<td class="text_align_right border_top"> <?= number_format($_data[10]['vl_subtotal_com_refinanciamento_saldo'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="height-10" colspan="5"> </td>
		</tr>

		<tr>
			<td> Déficit (VI) </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_deficit_previsao_inicial'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_deficit_previsao_atualizada'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_deficit_realizada'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_deficit_saldo'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="height-10" colspan="5"> </td>
		</tr>

		<tr>
			<td> <b> TOTAL (VII) = (V + VI) </b> </td>
			<td class="text_align_right border_top"> <?= number_format($_data[10]['total_previsao_inicial'], 2, ',', '.') ?> </td>
			<td class="text_align_right border_top"> <?= number_format($_data[10]['total_previsao_atualizada'], 2, ',', '.') ?> </td>
			<td class="text_align_right border_top"> <?= number_format($_data[10]['total_realizado'], 2, ',', '.') ?> </td>
			<td class="text_align_right border_top"> <?= number_format($_data[10]['total_saldo'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="height-10" colspan="5"> </td>
		</tr>

		<tr>
			<td> 
				<b>
					Saldos de Exercícios Anteriores
					(Utilizados para Créditos Adicionais)
				</b>
			</td>
			<td colspan="4"> </td>
		</tr>

		<tr>
			<td class="padding-left-60"> Recursos Arrecadados em Exercícios Anteriores </td>
			<td class="text_align_right"> <?= number_format("0.00", 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format("0.00", 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format("0.00", 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format("0.00", 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-60"> Superávit Financeiro </td>
			<td class="text_align_right"> <?= number_format("0.00", 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format("0.00", 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_saldo_exercicio_anterior_superavit_finan'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_saldo_exercicio_anterior_superavit_finan'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-60"> Reabertura de Créditos Adicionais </td>
			<td class="text_align_right"> <?= number_format("0.00", 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format("0.00", 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_saldo_exercicio_anterior_reabertura_credito_adicio'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_saldo_exercicio_anterior_reabertura_credito_adicio'], 2, ',', '.') ?> </td>
		</tr>
	</tbody>
</table>

<div class="height-10"> </div>

<table class="separated">
	<thead>
		<tr>
			<th width="22%" style="background: none;"> 
				DESPESAS ORÇAMENTÁRIAS
			</th>
			<th width="13%" style="background: none;"> 
				<p> Dotação </p>
				<p> Inicial </p>
				(e)
			</th>
			<th width="13%" style="background: none;">
				<p> Dotação </p>
				<p> Atualizada </p>
				(f)
			</th>
			<th width="13%" style="background: none;"> 
				<p> Despesas </p>
				<p> Empenhadas </p>
				(g) 
			</th>
			<th width="13%" style="background: none;"> 
				<p> Despesas </p>
				<p> Liquidadas </p>
				(h)
			</th>
			<th width="13%" style="background: none;"> 
				<p> Despesas </p>
				<p> Pagas </p>
				(i)
			</th>
			<th width="13%" style="background: none;"> 
				<p> Saldo da </p>
				<p> Dotação </p>
				(j) = (f - g)
			</th>
		</tr>
	</thead>

	<tbody>
		<tr>
			<td> <b> Despesas Correntes (VIII) </b> </td>
			<td colspan="4"></td>
		</tr>
		
		<tr>
			<td class="padding-left-30"> Pessoal e Encargos Sociais </td>
			<td class="text_align_right"> <?= number_format($_data[30]['vl_pessoal_encar_social_dotacao_inicial'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[30]['vl_pessoal_encar_social_dotacao_atualizada'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[30]['vl_pessoal_encar_social_despesas_empenhadas'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[30]['vl_pessoal_encar_social_despesas_liquidadas'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[30]['vl_pessoal_encar_social_despesas_pagas'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[30]['vl_pessoal_encar_social_dotacao_atualizada'] - $_data[30]['vl_pessoal_encar_social_despesas_empenhadas'], 2, ',', '.') ?> </td>
		</tr>
		
		<tr>
			<td class="padding-left-30"> Juros e Encargos da Dívida </td>
			<td class="text_align_right"> <?= number_format($_data[30]['vl_juros_encar_dividas_dotacao_inicial'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[30]['vl_juros_encar_dividas_dotacao_atualizada'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[30]['vl_juros_encar_dividas_despesas_empenhadas'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[30]['vl_juros_encar_dividas_despesas_liquidadas'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[30]['vl_juros_encar_dividas_despesas_pagas'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[30]['vl_juros_encar_dividas_dotacao_atualizada'] - $_data[30]['vl_juros_encar_dividas_despesas_empenhadas'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-30"> Outras Despesas Correntes </td>
			<td class="text_align_right"> <?= number_format($_data[30]['vl_outras_desp_correntes_dotacao_inicial'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[30]['vl_outras_desp_correntes_dotacao_atualizada'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[30]['vl_outras_desp_correntes_despesas_empenhadas'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[30]['vl_outras_desp_correntes_despesas_liquidadas'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[30]['vl_outras_desp_correntes_despesas_pagas'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[30]['vl_outras_desp_correntes_dotacao_atualizada'] - $_data[30]['vl_outras_desp_correntes_despesas_empenhadas'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="height-10" colspan="7"> </td>
		</tr>

		<tr>
			<td> <b> Despesas de Capital (IX) </b> </td>
			<td colspan="4"></td>
		</tr>
		
		<tr>
			<td class="padding-left-30"> Investimentos </td>
			<td class="text_align_right"> <?= number_format($_data[30]['vl_investimentos_dotacao_inicial'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[30]['vl_investimentos_dotacao_atualizada'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[30]['vl_investimentos_despesas_empenhadas'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[30]['vl_investimentos_despesas_liquidadas'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[30]['vl_investimentos_despesas_pagas'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[30]['vl_investimentos_dotacao_atualizada'] - $_data[30]['vl_investimentos_despesas_empenhadas'], 2, ',', '.') ?> </td>
		</tr>
		
		<tr>
			<td class="padding-left-30"> Inversões Financeiras </td>
			<td class="text_align_right"> <?= number_format($_data[30]['vl_inver_financeira_dotacao_inicial'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[30]['vl_inver_financeira_dotacao_atualizada'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[30]['vl_inver_financeira_despesas_empenhadas'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[30]['vl_inver_financeira_despesas_liquidadas'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[30]['vl_inver_financeira_despesas_pagas'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[30]['vl_inver_financeira_dotacao_atualizada'] - $_data[30]['vl_inver_financeira_despesas_empenhadas'], 2, ',', '.') ?> </td>
		</tr>
	
		<tr>
			<td class="padding-left-30"> Amortização da Dívida </td>
			<td class="text_align_right"> <?= number_format($_data[30]['vl_amortiza_divida_dotacao_inicial'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[30]['vl_amortiza_divida_dotacao_atualizada'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[30]['vl_amortiza_divida_despesas_empenhadas'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[30]['vl_amortiza_divida_despesas_liquidadas'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[30]['vl_amortiza_divida_despesas_pagas'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[30]['vl_amortiza_divida_dotacao_atualizada'] - $_data[30]['vl_amortiza_divida_despesas_empenhadas'], 2, ',', '.') ?> </td>
		</tr>
		
		<tr>
			<td class="height-10" colspan="7"> </td>
		</tr>

		<tr>
			<td> <b> Reserva de Contigência (X) </b> </td>
			<td class="text_align_right"> <?= number_format($_data[30]['vl_reserva_contingencia_dotacao_inicial'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[30]['vl_reserva_contingencia_dotacao_atualizada'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[30]['vl_reserva_contingencia_despesas_empenhadas'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[30]['vl_reserva_contingencia_despesas_liquidadas'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[30]['vl_reserva_contingencia_despesas_pagas'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[30]['vl_reserva_contingencia_dotacao_atualizada'] - $_data[30]['vl_reserva_contingencia_despesas_empenhadas'], 2, ',', '.') ?> </td>
		</tr>
		
		<tr>
			<td class="height-10" colspan="7"> </td>
		</tr>

		<tr>
			<td> <b> SUBTOTAL DAS DESPESAS (XII) = (VII + IX + X) </b> </td>
			<td class="text_align_right border_top"> <?= number_format($_data[30]['vl_subtotal_despesas_inicial'], 2, ',', '.') ?> </td>
			<td class="text_align_right border_top"> <?= number_format($_data[30]['vl_subtotal_despesas_atualizada'], 2, ',', '.') ?> </td>
			<td class="text_align_right border_top"> <?= number_format($_data[30]['vl_subtotal_despesas_empenhadas'], 2, ',', '.') ?> </td>
			<td class="text_align_right border_top"> <?= number_format($_data[30]['vl_subtotal_despesas_liquidadas'], 2, ',', '.') ?> </td>
			<td class="text_align_right border_top"> <?= number_format($_data[30]['vl_subtotal_despesas_pagas'], 2, ',', '.') ?> </td>
			<td class="text_align_right border_top"> <?= number_format($_data[30]['vl_subtotal_despesas_atualizada'] - $_data[30]['vl_subtotal_despesas_empenhadas'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="height-10" colspan="7"> </td>
		</tr>

		<tr>
			<td> <b> Amortização da Dívida / Refinanciamento (XII) </b> </td>
			<td colspan="6"></td>
		</tr>
		
		<tr>
			<td class="padding-left-30"> Amortização da Dívida Interna </td>
			<td colspan="6"></td>
		</tr>

		<tr>
			<td class="padding-left-60"> Dívida Mobiliária </td>
			<td class="text_align_right"> <?= number_format($_data[30]['vl_amortiza_divida_inter_mobiliaria_dotacao_inicial'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[30]['vl_amortiza_divida_inter_mobiliaria_dotacao_atualizada'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[30]['vl_amortiza_divida_inter_mobiliaria_despesas_empenhadas'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[30]['vl_amortiza_divida_inter_mobiliaria_despesas_liquidadas'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[30]['vl_amortiza_divida_inter_mobiliaria_despesas_pagas'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[30]['vl_amortiza_divida_inter_mobiliaria_dotacao_atualizada'] - $_data[30]['vl_amortiza_divida_inter_mobiliaria_despesas_empenhadas'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-60"> Outras Dívidas </td>
			<td class="text_align_right"> <?= number_format($_data[30]['vl_amortiza_outras_dividas_internas_dotacao_inicial'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[30]['vl_amortiza_outras_dividas_internas_dotacao_atualizada'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[30]['vl_amortiza_outras_dividas_internas_despesas_empenhadas'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[30]['vl_amortiza_outras_dividas_internas_despesas_liquidadas'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[30]['vl_amortiza_outras_dividas_internas_despesas_pagas'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[30]['vl_amortiza_outras_dividas_internas_dotacao_atualizada'] - $_data[30]['vl_amortiza_outras_dividas_internas_despesas_empenhadas'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-30"> Amortização da Dívida Externa </td>
			<td colspan="6"></td>
		</tr>

		<tr>
			<td class="padding-left-60"> Dívida Mobiliária </td>
			<td class="text_align_right"> <?= number_format($_data[30]['vl_amortiza_divida_exter_mobiliaria_dotacao_inicial'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[30]['vl_amortiza_divida_exter_mobiliaria_dotacao_atualizada'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[30]['vl_amortiza_divida_exter_mobiliaria_despesas_empenhadas'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[30]['vl_amortiza_divida_exter_mobiliaria_despesas_liquidadas'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[30]['vl_amortiza_divida_exter_mobiliaria_despesas_pagas'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[30]['vl_amortiza_divida_exter_mobiliaria_dotacao_atualizada'] - $_data[30]['vl_amortiza_divida_exter_mobiliaria_despesas_empenhadas'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-60"> Outras Dívidas </td>
			<td class="text_align_right"> <?= number_format($_data[30]['vl_amortiza_outras_dividas_externas_dotacao_inicial'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[30]['vl_amortiza_outras_dividas_externas_dotacao_atualizada'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[30]['vl_amortiza_outras_dividas_externas_despesas_empenhadas'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[30]['vl_amortiza_outras_dividas_externas_despesas_liquidadas'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[30]['vl_amortiza_outras_dividas_externas_despesas_pagas'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[30]['vl_amortiza_outras_dividas_externas_dotacao_atualizada'] - $_data[30]['vl_amortiza_outras_dividas_externas_despesas_empenhadas'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="height-10" colspan="7"> </td>
		</tr>

		<tr>
			<td> <b> SUBTOTAL COM REFINANCIAMENTO (XII) = (XI + XII) </b> </td>
			<td class="text_align_right border_top"> <?= number_format($_data[30]['vl_subtotal_com_refinanciamento_dotacao_inicial'], 2, ',', '.') ?> </td>
			<td class="text_align_right border_top"> <?= number_format($_data[30]['vl_subtotal_com_refinanciamento_dotacao_atualizada'], 2, ',', '.') ?> </td>
			<td class="text_align_right border_top"> <?= number_format($_data[30]['vl_subtotal_com_refinanciamento_despesas_empenhadas'], 2, ',', '.') ?> </td>
			<td class="text_align_right border_top"> <?= number_format($_data[30]['vl_subtotal_com_refinanciamento_despesas_liquidadas'], 2, ',', '.') ?> </td>
			<td class="text_align_right border_top"> <?= number_format($_data[30]['vl_subtotal_com_refinanciamento_despesas_pagas'], 2, ',', '.') ?> </td>
			<td class="text_align_right border_top"> <?= number_format($_data[30]['vl_subtotal_com_refinanciamento_despesas_atualizada'] - $_data[30]['vl_subtotal_com_refinanciamento_despesas_empenhadas'], 2, ',', '.') ?> </td>
		</tr>
		
		<tr>
			<td class="height-10" colspan="7"> </td>
		</tr>

		<tr>
			<td> Superávit (XIII) </td>
			<td class="text_align_right"> <?= number_format($_data[30]['vl_superavit_dotacao_inicial'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[30]['vl_superavit_dotacao_atualizada'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[30]['vl_superavit_despesas_empenhadas'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[30]['vl_superavit_despesas_liquidadas'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[30]['vl_superavit_despesas_pagas'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[30]['vl_superavit_saldo'], 2, ',', '.') ?> </td>
		</tr>
		
		<tr>
			<td class="height-10" colspan="7"> </td>
		</tr>

		<tr>
			<td> <b> TOTAL (XIV) = (XII + XIII) </b> </td>
			<td class="text_align_right border_top"> <?= number_format($_data[30]['total_dotacao_inicial'], 2, ',', '.') ?> </td>
			<td class="text_align_right border_top"> <?= number_format($_data[30]['total_dotacao_atualizada'], 2, ',', '.') ?> </td>
			<td class="text_align_right border_top"> <?= number_format($_data[30]['total_despesas_empenhadas'], 2, ',', '.') ?> </td>
			<td class="text_align_right border_top"> <?= number_format($_data[30]['total_despesas_liquidadas'], 2, ',', '.') ?> </td>
			<td class="text_align_right border_top"> <?= number_format($_data[30]['total_despesas_pagas'], 2, ',', '.') ?> </td>
			<td class="text_align_right border_top"> <?= number_format($_data[30]['total_saldo_dotacao'], 2, ',', '.') ?> </td>
		</tr>
	
		<tr>
			<td class="height-10" colspan="7"> </td>
		</tr>

		<tr>
			<td> <b> Reserva do RPPS </b> </td>
			<td class="text_align_right border_bottom"> <?= number_format($_data[30]['vl_reserva_rpps_dotacao_inicial'], 2, ',', '.') ?> </td>
			<td class="text_align_right border_bottom"> <?= number_format($_data[30]['vl_reserva_rpps_dotacao_atualizada'], 2, ',', '.') ?> </td>
			<td class="text_align_right border_bottom"> <?= number_format($_data[30]['vl_reserva_rpps_despesas_empenhadas'], 2, ',', '.') ?> </td>
			<td class="text_align_right border_bottom"> <?= number_format($_data[30]['vl_reserva_rpps_despesas_liquidadas'], 2, ',', '.') ?> </td>
			<td class="text_align_right border_bottom"> <?= number_format($_data[30]['vl_reserva_rpps_despesas_pagas'], 2, ',', '.') ?> </td>
			<td class="text_align_right border_bottom"> <?= number_format($_data[30]['vl_reserva_rpps_dotacao_atualizada'] - $_data[30]['vl_reserva_rpps_despesas_empenhadas'], 2, ',', '.') ?> </td>
		</tr>

	</tbody>
</table>