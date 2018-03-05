<div class="text_align_center"> <b> DEMONSTRAÇÃO DOS FLUXOS DE CAIXA </b> </div>
<div class="text_align_right"> Exercício: <?= $_data["exercicio_atual"] ?></div>
<BR />
<table class="separated">
	<thead>
	<tr>
		<th width="76%" style="background: none;">
		</th>
		<th width="15%" style="background: none;">
			<p> Nota </p>
		</th>
		<th width="15%" style="background: none;">
			<p> Exercício Atual</p>
		</th>
		<th width="15%" style="background: none;">
			<p> Exercício Anterior</p>
		</th>
	</tr>
	</thead>

	<tbody>
	<tr>
		<td> FLUXOS DE CAIXA DAS ATIVIDADES  </td>
		<td colspan="3"></td>
	</tr>
	<tr>
		<td> OPERACIONAIS  </td>
		<td colspan="3"></td>
	</tr>
	<tr>
		<td> <b> Ingressos </b> </td>
		<td colspan="3"></td>
	</tr>

	<tr>
		<td class="padding-left-30"> Receitas derivadas e originárias </td>
		<td class="text_align_right"> </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_atual']]['vl_receita_derivada_originaria'], 2, ',', '.') ?> </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_anterior']]['vl_receita_derivada_originaria'], 2, ',', '.') ?> </td>

	</tr>
	<tr>
		<td class="padding-left-30"> Transferências correntes recebidas </td>
		<td class="text_align_right"> </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_atual']]['vl_transf_corrente_recebida'], 2, ',', '.') ?> </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_anterior']]['vl_transf_corrente_recebida'], 2, ',', '.') ?> </td>

	</tr>

	<tr>
		<td> <b> Desembolsos </b> </td>
		<td colspan="3"></td>
	</tr>

	<tr>
		<td class="padding-left-30">  Pessoal e demais despesas </td>
		<td class="text_align_right"> </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_atual']]['vl_desembolso_pessoal_demais_despesa'], 2, ',', '.') ?> </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_anterior']]['vl_desembolso_pessoal_demais_despesa'], 2, ',', '.') ?> </td>

	</tr>
	<tr>
		<td class="padding-left-30">  Juros e encargos da dívida </td>
		<td class="text_align_right"> </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_atual']]['vl_desembolso_juro_encargo_divida'], 2, ',', '.') ?> </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_anterior']]['vl_desembolso_juro_encargo_divida'], 2, ',', '.') ?> </td>

	</tr>
	<tr>
		<td class="padding-left-30">   Transferências concedidas </td>
		<td class="text_align_right"> </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_atual']]['vl_desembolso_transferencia_concedida'], 2, ',', '.') ?> </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_anterior']]['vl_desembolso_transferencia_concedida'], 2, ',', '.') ?> </td>

	</tr>


	<tr>
		<td class="height-10" colspan="4"> </td>
	</tr>

	<tr>
		<td> <b> Fluxo de caixa líquido das atividades operacionais (I) </b> </td>
		<td class="text_align_right "> </td>
		<td class="text_align_right border_top"> <?= number_format($_data[$_data['exercicio_atual']]['vl_fluxo_caixa_liq_atividade_operacional'], 2, ',', '.') ?> </td>
		<td class="text_align_right border_top"> <?= number_format($_data[$_data['exercicio_anterior']]['vl_fluxo_caixa_liq_atividade_operacional'], 2, ',', '.') ?> </td>
	</tr>

	<tr>
		<td class="height-10" colspan="4"> </td>
	</tr>


	<tr>
		<td> FLUXOS DE CAIXA DAS ATIVIDADES  </td>
		<td colspan="3"></td>
	</tr>
	<tr>
		<td> INVESTIMENTO  </td>
		<td colspan="3"></td>
	</tr>
	<tr>
		<td> <b> Ingressos </b> </td>
		<td colspan="3"></td>
	</tr>

	<tr>
		<td class="padding-left-30">  Alienação de bens </td>
		<td class="text_align_right"> </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_atual']]['vl_alienacao_bens'], 2, ',', '.') ?> </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_anterior']]['vl_alienacao_bens'], 2, ',', '.') ?> </td>

	</tr>
	<tr>
		<td class="padding-left-30"> Amortização de empréstimos e financiamentos concedidos </td>
		<td class="text_align_right"> </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_atual']]['vl_amortizacao_empres_financ_concedido'], 2, ',', '.') ?> </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_anterior']]['vl_amortizacao_empres_financ_concedido'], 2, ',', '.') ?> </td>

	</tr>


	<tr>
		<td> <b> Desembolsos </b> </td>
		<td colspan="3"></td>
	</tr>

	<tr>
		<td class="padding-left-30"> Aquisição de ativo não circulante </td>
		<td class="text_align_right"> </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_atual']]['vl_aquisicao_ativo_circulante'], 2, ',', '.') ?> </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_anterior']]['vl_aquisicao_ativo_circulante'], 2, ',', '.') ?> </td>

	</tr>
	<tr>
		<td class="padding-left-30"> Concessão de empréstimos e financiamentos </td>
		<td class="text_align_right"> </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_atual']]['vl_concessao_emprestimo_financiamento'], 2, ',', '.') ?> </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_anterior']]['vl_concessao_emprestimo_financiamento'], 2, ',', '.') ?> </td>

	</tr>
	<tr>
		<td class="padding-left-30"> Outros desembolsos de investimentos </td>
		<td class="text_align_right"> </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_atual']]['vl_outro_desembolso_investimento'], 2, ',', '.') ?> </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_anterior']]['vl_outro_desembolso_investimento'], 2, ',', '.') ?> </td>

	</tr>


	<tr>
		<td> <b> Fluxo de caixa líquido das atividades de investimento (II) </b> </td>
		<td class="text_align_right "> </td>
		<td class="text_align_right border_top"> <?= number_format($_data[$_data['exercicio_atual']]['vl_fluxo_caixa_liq_atividade_investimento'], 2, ',', '.') ?> </td>
		<td class="text_align_right border_top"> <?= number_format($_data[$_data['exercicio_anterior']]['vl_fluxo_caixa_liq_atividade_investimento'], 2, ',', '.') ?> </td>
	</tr>

	<tr>
		<td class="height-10" colspan="4"> </td>
	</tr>


	<tr>
		<td> FLUXOS DE CAIXA DAS ATIVIDADES DE </td>
		<td colspan="3"></td>
	</tr>
	<tr>
		<td> FINANCIAMENTO  </td>
		<td colspan="3"></td>
	</tr>
	<tr>
		<td> <b> Ingressos </b> </td>
		<td colspan="3"></td>
	</tr>

	<tr>
		<td class="padding-left-30"> Operações de crédito </td>
		<td class="text_align_right"> </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_atual']]['vl_operacao_credito'], 2, ',', '.') ?> </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_anterior']]['vl_operacao_credito'], 2, ',', '.') ?> </td>

	</tr>
	<tr>
		<td class="padding-left-30"> Integralização do capital social de empresas
			dependentes </td>
		<td class="text_align_right"> </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_atual']]['vl_integra_capital_social_empresa_dependente'], 2, ',', '.') ?> </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_anterior']]['vl_integra_capital_social_empresa_dependente'], 2, ',', '.') ?> </td>

	</tr>
	<tr>
		<td class="padding-left-30"> Transferências de capital recebidas </td>
		<td class="text_align_right"> </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_atual']]['vl_transferencia_capital_recebida'], 2, ',', '.') ?> </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_anterior']]['vl_transferencia_capital_recebida'], 2, ',', '.') ?> </td>

	</tr>
	<tr>
		<td class="padding-left-30"> Outros ingressos de financiamento </td>
		<td class="text_align_right"> </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_atual']]['vl_outro_ingresso_financiamento'], 2, ',', '.') ?> </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_anterior']]['vl_outro_ingresso_financiamento'], 2, ',', '.') ?> </td>

	</tr>


	<tr>
		<td> <b> Desembolsos </b> </td>
		<td colspan="3"></td>
	</tr>

	<tr>
		<td class="padding-left-30"> Amortização /Refinanciamento da dívida </td>
		<td class="text_align_right"> </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_atual']]['vl_amortizacao_refinanciamento_divida'], 2, ',', '.') ?> </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_anterior']]['vl_amortizacao_refinanciamento_divida'], 2, ',', '.') ?> </td>

	</tr>
	<tr>
		<td class="padding-left-30"> Outros desembolsos de financiamentos </td>
		<td class="text_align_right"> </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_atual']]['vl_outro_desembolso_financiamento'], 2, ',', '.') ?> </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_anterior']]['vl_outro_desembolso_financiamento'], 2, ',', '.') ?> </td>

	</tr>
	<tr>
		<td> <b> Fluxo de caixa líquido das atividades de financiamento (III) </b> </td>
		<td class="text_align_right "> </td>
		<td class="text_align_right border_top"> <?= number_format($_data[$_data['exercicio_atual']]['vl_tot_desembolso_atividade_financiamento'], 2, ',', '.') ?> </td>
		<td class="text_align_right border_top"> <?= number_format($_data[$_data['exercicio_anterior']]['vl_tot_desembolso_atividade_financiamento'], 2, ',', '.') ?> </td>
	</tr>

	<tr>
		<td class="height-10" colspan="4"> </td>
	</tr>

	<tr>
		<td> <b> GERAÇÃO LÍQUIDA DE CAIXA E EQUIVALENTE DE CAIXA (I+II+III) </b> </td>
		<td class="text_align_right "> </td>
		<td class="text_align_right border_top"> <?= number_format($_data[$_data['exercicio_atual']]['vl_caixa_equivalente_caixa_inicial'], 2, ',', '.') ?> </td>
		<td class="text_align_right border_top"> <?= number_format($_data[$_data['exercicio_anterior']]['vl_caixa_equivalente_caixa_inicial'], 2, ',', '.') ?> </td>
	</tr>

	<tr>
		<td class="height-10" colspan="4"> </td>
	</tr>

	<tr>
		<td> Caixa e Equivalentes de caixa inicial </td>
		<td class="text_align_right"> </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_atual']]['valor_ano_atual'], 2, ',', '.') ?> </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_anterior']]['valor_ano_anterior'], 2, ',', '.') ?> </td>

	</tr>

	<tr>
		<td> Caixa e Equivalente de caixa final </td>
		<td class="text_align_right"> </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_atual']]['vl_caixa_equivalente_caixa_final'], 2, ',', '.') ?> </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_anterior']]['vl_caixa_equivalente_caixa_final'], 2, ',', '.') ?> </td>

	</tr>
	</tbody>
</table>

<div class="height-10"> </div>

<pagebreak></pagebreak>

<div class="text_align_center"> <b> QUADRO DE RECEITAS DERIVADAS E ORIGINÁRIAS </b> </div>
<div class="text_align_right"> Exercício: <?= $_data["exercicio_atual"] ?></div>
<BR />
<table class="separated">
	<thead>
	<tr>
		<th width="76%" style="background: none;">
		</th>
		<th width="15%" style="background: none;">
			<p> Exercício Atual</p>
		</th>
		<th width="15%" style="background: none;">
			<p> Exercício Anterior</p>
		</th>
	</tr>
	</thead>

	<tbody>
	<tr>
		<td></td>
		<td colspan="2"></td>
	</tr>
	<tr>
		<td> RECEITAS DERIVADAS E ORIGINÁRIAS </td>
		<td colspan="2"></td>
	</tr>

	<tr>
		<td class="height-10" colspan="3"> </td>
	</tr>
	<tr>
		<td class="padding-left-30"> Receita Tributária </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_atual']]['vl_receita_tributaria'], 2, ',', '.') ?> </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_anterior']]['vl_receita_tributaria'], 2, ',', '.') ?> </td>

	</tr>
	<tr>
		<td class="padding-left-30"> Receita de Contribuições </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_atual']]['vl_receita_contribuicoes'], 2, ',', '.') ?> </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_anterior']]['vl_receita_contribuicoes'], 2, ',', '.') ?> </td>

	</tr>
	<tr>
		<td class="padding-left-30"> Receita Patrimonial </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_atual']]['vl_receita_patrimonial'], 2, ',', '.') ?> </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_anterior']]['vl_receita_patrimonial'], 2, ',', '.') ?> </td>

	</tr>
	<tr>
		<td class="padding-left-30"> Receita Agropecuária </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_atual']]['vl_receita_agropecuaria'], 2, ',', '.') ?> </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_anterior']]['vl_receita_agropecuaria'], 2, ',', '.') ?> </td>

	</tr>
	<tr>
		<td class="padding-left-30"> Receita Industrial </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_atual']]['vl_receita_industrial'], 2, ',', '.') ?> </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_anterior']]['vl_receita_industrial'], 2, ',', '.') ?> </td>

	</tr>
	<tr>
		<td class="padding-left-30"> Receita de Serviços </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_atual']]['vl_receita_servicos'], 2, ',', '.') ?> </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_anterior']]['vl_receita_servicos'], 2, ',', '.') ?> </td>

	</tr>
	<tr>
		<td class="padding-left-30"> Remuneração das Disponibilidades </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_atual']]['vl_remuneracao_disponibilidades'], 2, ',', '.') ?> </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_anterior']]['vl_remuneracao_disponibilidades'], 2, ',', '.') ?> </td>
	</tr>
	<tr>
		<td class="padding-left-30"> Outras Receitas Derivadas e Originárias </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_atual']]['vl_outras_receiras_derivadas_originarias'], 2, ',', '.') ?> </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_anterior']]['vl_outras_receiras_derivadas_originarias'], 2, ',', '.') ?> </td>

	</tr>



	<tr>
		<td class="height-10" colspan="3"> </td>
	</tr>

	<tr>
		<td> <b> Total das Receitas Derivadas e Originárias </b> </td>
		<td class="text_align_right border_top"> <?= number_format($_data[$_data['exercicio_atual']]['vl_total_receiras_derivadas_originarias'], 2, ',', '.') ?> </td>
		<td class="text_align_right border_top"> <?= number_format($_data[$_data['exercicio_anterior']]['vl_total_receiras_derivadas_originarias'], 2, ',', '.') ?> </td>
	</tr>

	</tbody>
</table>


<div class="height-10"> </div>

<pagebreak></pagebreak>


<div class="text_align_center"> <b> QUADRO DE TRANSFERÊNCIAS RECEBIDAS E CONCEDIDAS </b> </div>
<div class="text_align_right"> Exercício: <?= $_data["exercicio_atual"] ?></div>
<BR />
<table class="separated">
	<thead>
	<tr>
		<th width="76%" style="background: none;">
		</th>
		<th width="15%" style="background: none;">
			<p> Exercício Atual</p>
		</th>
		<th width="15%" style="background: none;">
			<p> Exercício Anterior</p>
		</th>
	</tr>
	</thead>

	<tbody>
	<tr>
		<td></td>
		<td colspan="2"></td>
	</tr>
	<tr>
		<td> TRANSFERÊNCIAS CORRENTES RECEBIDAS </td>
		<td colspan="2"></td>
	</tr>

	<tr>
		<td class="height-10" colspan="3"> Intergovernamentais </td>
	</tr>
	<tr>
		<td class="padding-left-30"> da União </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_atual']]['vl_inter_transf_corrente_rec_uniao'], 2, ',', '.') ?> </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_anterior']]['vl_inter_transf_corrente_rec_uniao'], 2, ',', '.') ?> </td>

	</tr>
	<tr>
		<td class="padding-left-30"> de Estados e Distrito Federal </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_atual']]['vl_inter_transf_corrente_rec_estado_df'], 2, ',', '.') ?> </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_anterior']]['vl_inter_transf_corrente_rec_estado_df'], 2, ',', '.') ?> </td>

	</tr>
	<tr>
		<td class="padding-left-30"> de Municípios </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_atual']]['vl_inter_transf_corrente_rec_municipios'], 2, ',', '.') ?> </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_anterior']]['vl_inter_transf_corrente_rec_municipios'], 2, ',', '.') ?> </td>

	</tr>
	<tr>
		<td class="height-10" colspan="3"> Intergovernamentais </td>
	</tr>
	<tr>
		<td class="padding-left-30"> Outras transferências correntes recebidas </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_atual']]['vl_inter_transf_corrente_rec_municipios'], 2, ',', '.') ?> </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_anterior']]['vl_inter_transf_corrente_rec_municipios'], 2, ',', '.') ?> </td>

	</tr>

	<tr>
		<td class="height-10" colspan="3"> </td>
	</tr>

	<tr>
		<td> <b> Total das Transferências Correntes Recebidas </b> </td>
		<td class="text_align_right border_top"> <?= number_format($_data[$_data['exercicio_atual']]['vl_total_transf_corrente_recebidas'], 2, ',', '.') ?> </td>
		<td class="text_align_right border_top"> <?= number_format($_data[$_data['exercicio_anterior']]['vl_total_transf_corrente_recebidas'], 2, ',', '.') ?> </td>
	</tr>

	<tr>
		<td class="height-10" colspan="3"> </td>
	</tr>


	<tr>
		<td> TRANSFERÊNCIAS CONCEDIDAS </td>
		<td colspan="2"></td>
	</tr>

	<tr>
		<td class="height-10" colspan="3"> Intergovernamentais </td>
	</tr>
	<tr>
		<td class="padding-left-30"> a União </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_atual']]['vl_inter_transf_corrente_conc_uniao'], 2, ',', '.') ?> </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_anterior']]['vl_inter_transf_corrente_conc_uniao'], 2, ',', '.') ?> </td>

	</tr>
	<tr>
		<td class="padding-left-30"> a Estados e Distrito Federal </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_atual']]['vl_inter_transf_corrente_conc_estado_df'], 2, ',', '.') ?> </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_anterior']]['vl_inter_transf_corrente_conc_estado_df'], 2, ',', '.') ?> </td>

	</tr>
	<tr>
		<td class="padding-left-30"> a Municípios </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_atual']]['vl_inter_transf_corrente_conc_municipios'], 2, ',', '.') ?> </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_anterior']]['vl_inter_transf_corrente_conc_municipios'], 2, ',', '.') ?> </td>

	</tr>
	<tr>
		<td class="height-10" colspan="3"> Intergovernamentais </td>
	</tr>
	<tr>
		<td class="padding-left-30"> Outras transferências concedidas </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_atual']]['vl_inter_transf_corrente_conc_outras'], 2, ',', '.') ?> </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_anterior']]['vl_inter_transf_corrente_conc_outras'], 2, ',', '.') ?> </td>

	</tr>

	<tr>
		<td class="height-10" colspan="3"> </td>
	</tr>

	<tr>
		<td> <b> Total das Transferências Concedidas </b> </td>
		<td class="text_align_right border_top"> <?= number_format($_data[$_data['exercicio_atual']]['vl_total_transf_corrente_concedidas'], 2, ',', '.') ?> </td>
		<td class="text_align_right border_top"> <?= number_format($_data[$_data['exercicio_anterior']]['vl_total_transf_corrente_concedidas'], 2, ',', '.') ?> </td>
	</tr>
	</tbody>
</table>


<div class="height-10"> </div>

<pagebreak></pagebreak>


<div class="text_align_center"> <b> QUADRO DE DESEMBOLSOS DE PESSOAL E DEMAIS DESPESAS POR FUNÇÃO </b> </div>
<div class="text_align_right"> Exercício: <?= $_data["exercicio_atual"] ?></div>
<BR />
<table class="separated">
	<thead>
	<tr>
		<th width="76%" style="background: none;">
		</th>
		<th width="15%" style="background: none;">
			<p> Exercício Atual</p>
		</th>
		<th width="15%" style="background: none;">
			<p> Exercício Anterior</p>
		</th>
	</tr>
	</thead>

	<tbody>
	<tr>
		<td></td>
		<td colspan="2"></td>
	</tr>
	<tr>
		<td class="padding-left-30"> Legislativa </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_atual']]['vl_despesa_fun_legislativa'], 2, ',', '.') ?> </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_anterior']]['vl_despesa_fun_legislativa'], 2, ',', '.') ?> </td>

	</tr>
	<tr>
		<td class="padding-left-30"> Judiciária </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_atual']]['vl_despesa_fun_judiciaria'], 2, ',', '.') ?> </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_anterior']]['vl_despesa_fun_judiciaria'], 2, ',', '.') ?> </td>

	</tr>
	<tr>
		<td class="padding-left-30"> Essencial à Justiça </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_atual']]['vl_despesa_fun_essencial_justica'], 2, ',', '.') ?> </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_anterior']]['vl_despesa_fun_essencial_justica'], 2, ',', '.') ?> </td>

	</tr>

	<tr>
		<td class="padding-left-30"> Administração </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_atual']]['vl_despesa_fun_administracao'], 2, ',', '.') ?> </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_anterior']]['vl_despesa_fun_administracao'], 2, ',', '.') ?> </td>

	</tr>
	<tr>
		<td class="padding-left-30"> Defesa Nacional </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_atual']]['vl_despesa_fun_defesa_nacional'], 2, ',', '.') ?> </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_anterior']]['vl_despesa_fun_defesa_nacional'], 2, ',', '.') ?> </td>

	</tr>
	<tr>
		<td class="padding-left-30"> Segurança Pública </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_atual']]['vl_despesa_fun_segurança_publica'], 2, ',', '.') ?> </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_anterior']]['vl_despesa_fun_segurança_publica'], 2, ',', '.') ?> </td>

	</tr>
	<tr>
		<td class="padding-left-30"> Relações Exteriores </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_atual']]['vl_despesa_fun_relacoes_exteriores'], 2, ',', '.') ?> </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_anterior']]['vl_despesa_fun_relacoes_exteriores'], 2, ',', '.') ?> </td>

	</tr>
	<tr>
		<td class="padding-left-30"> Assistência Social </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_atual']]['vl_despesa_fun_assistencia_social'], 2, ',', '.') ?> </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_anterior']]['vl_despesa_fun_assistencia_social'], 2, ',', '.') ?> </td>

	</tr>
	<tr>
		<td class="padding-left-30"> Previdência Social </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_atual']]['vl_despesa_fun_previdencia_social'], 2, ',', '.') ?> </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_anterior']]['vl_despesa_fun_previdencia_social'], 2, ',', '.') ?> </td>

	</tr>
	<tr>
		<td class="padding-left-30"> Saúde </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_atual']]['vl_despesa_fun_saude'], 2, ',', '.') ?> </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_anterior']]['vl_despesa_fun_saude'], 2, ',', '.') ?> </td>
	</tr>
	<tr>
		<td class="padding-left-30"> Trabalho </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_atual']]['vl_despesa_fun_trabalho'], 2, ',', '.') ?> </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_anterior']]['vl_despesa_fun_trabalho'], 2, ',', '.') ?> </td>
	</tr>
	<tr>
		<td class="padding-left-30"> Educação </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_atual']]['vl_despesa_fun_educacao'], 2, ',', '.') ?> </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_anterior']]['vl_despesa_fun_educacao'], 2, ',', '.') ?> </td>
	</tr>
	<tr>
		<td class="padding-left-30"> Cultura </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_atual']]['vl_despesa_fun_cultura'], 2, ',', '.') ?> </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_anterior']]['vl_despesa_fun_cultura'], 2, ',', '.') ?> </td>
	</tr>
	<tr>
		<td class="padding-left-30"> Direitos da Cidadania </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_atual']]['vl_despesa_fun_direitos_cidadania'], 2, ',', '.') ?> </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_anterior']]['vl_despesa_fun_direitos_cidadania'], 2, ',', '.') ?> </td>

	</tr>
	<tr>
		<td class="padding-left-30"> Urbanismo </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_atual']]['vl_despesa_fun_urbanismo'], 2, ',', '.') ?> </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_anterior']]['vl_despesa_fun_urbanismo'], 2, ',', '.') ?> </td>
	</tr>
	<tr>
		<td class="padding-left-30"> Habitação </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_atual']]['vl_despesa_fun_habitacao'], 2, ',', '.') ?> </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_anterior']]['vl_despesa_fun_habitacao'], 2, ',', '.') ?> </td>
	</tr>

	<tr>
		<td class="padding-left-30"> Saneamento </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_atual']]['vl_despesa_fun_saneamento'], 2, ',', '.') ?> </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_anterior']]['vl_despesa_fun_saneamento'], 2, ',', '.') ?> </td>
	</tr>

	<tr>
		<td class="padding-left-30"> Gestão Ambiental </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_atual']]['vl_despesa_fun_gestao_ambiental'], 2, ',', '.') ?> </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_anterior']]['vl_despesa_fun_gestao_ambiental'], 2, ',', '.') ?> </td>
	</tr>

	<tr>
		<td class="padding-left-30"> Ciência e Tecnologia </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_atual']]['vl_despesa_fun_ciencia_tecnologia'], 2, ',', '.') ?> </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_anterior']]['vl_despesa_fun_ciencia_tecnologia'], 2, ',', '.') ?> </td>
	</tr>

	<tr>
		<td class="padding-left-30"> Agricultura </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_atual']]['vl_despesa_fun_agricultura'], 2, ',', '.') ?> </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_anterior']]['vl_despesa_fun_agricultura'], 2, ',', '.') ?> </td>
	</tr>

	<tr>
		<td class="padding-left-30"> Organização Agrária </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_atual']]['vl_despesa_fun_organizacao_agraria'], 2, ',', '.') ?> </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_anterior']]['vl_despesa_fun_organizacao_agraria'], 2, ',', '.') ?> </td>
	</tr>
	<tr>
		<td class="padding-left-30"> Indústria </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_atual']]['vl_despesa_fun_industria'], 2, ',', '.') ?> </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_anterior']]['vl_despesa_fun_industria'], 2, ',', '.') ?> </td>
	</tr>
	<tr>
		<td class="padding-left-30"> Comércio e Serviços </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_atual']]['vl_despesa_fun_comercio_servicos'], 2, ',', '.') ?> </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_anterior']]['vl_despesa_fun_comercio_servicos'], 2, ',', '.') ?> </td>
	</tr>
	<tr>
		<td class="padding-left-30"> Comunicações </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_atual']]['vl_despesa_fun_comunicacoes'], 2, ',', '.') ?> </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_anterior']]['vl_despesa_fun_comunicacoes'], 2, ',', '.') ?> </td>
	</tr>
	<tr>
		<td class="padding-left-30"> Energia </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_atual']]['vl_despesa_fun_energia'], 2, ',', '.') ?> </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_anterior']]['vl_despesa_fun_energia'], 2, ',', '.') ?> </td>
	</tr>
	<tr>
		<td class="padding-left-30"> Transporte </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_atual']]['vl_despesa_fun_transporte'], 2, ',', '.') ?> </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_anterior']]['vl_despesa_fun_transporte'], 2, ',', '.') ?> </td>
	</tr>
	<tr>
		<td class="padding-left-30"> Desporto e Lazer </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_atual']]['vl_despesa_fun_desporto_lazer'], 2, ',', '.') ?> </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_anterior']]['vl_despesa_fun_desporto_lazer'], 2, ',', '.') ?> </td>
	</tr>
	<tr>
		<td class="padding-left-30"> Encargos Especiais </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_atual']]['vl_despesa_fun_encargos_especiais'], 2, ',', '.') ?> </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_anterior']]['vl_despesa_fun_encargos_especiais'], 2, ',', '.') ?> </td>
	</tr>

	<tr>
		<td class="height-10" colspan="3"> </td>
	</tr>

	<tr>
		<td> <b> Total dos Desembolsos de Pessoal e Demais Despesas por Função </b> </td>
		<td class="text_align_right border_top"> <?= number_format($_data[$_data['exercicio_atual']]['vl_total_desembolso_despesa_funcao'], 2, ',', '.') ?> </td>
		<td class="text_align_right border_top"> <?= number_format($_data[$_data['exercicio_anterior']]['vl_total_desembolso_despesa_funcao'], 2, ',', '.') ?> </td>
	</tr>
	</tbody>
</table>


<div class="height-10"> </div>

<pagebreak></pagebreak>




<div class="text_align_center"> <b> QUADRO DE JUROS E ENCARGOS DA DÍVIDA </b> </div>
<div class="text_align_right"> Exercício: <?= $_data["exercicio_atual"] ?></div>
<BR />
<table class="separated">
	<thead>
	<tr>
		<th width="76%" style="background: none;">
		</th>
		<th width="15%" style="background: none;">
			<p> Exercício Atual</p>
		</th>
		<th width="15%" style="background: none;">
			<p> Exercício Anterior</p>
		</th>
	</tr>
	</thead>

	<tbody>
	<tr>
		<td></td>
		<td colspan="2"></td>
	</tr>
	<tr>
		<td class="padding-left-30"> Juros e Correção Monetária da Dívida Interna </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_atual']]['vl_juros_correcao_monetaria_divida_interna'], 2, ',', '.') ?> </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_anterior']]['vl_juros_correcao_monetaria_divida_interna'], 2, ',', '.') ?> </td>
	</tr>
	<tr>
		<td class="padding-left-30"> Juros e Correção Monetária da Dívida Externa </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_atual']]['vl_juros_correcao_monetaria_divida_externa'], 2, ',', '.') ?> </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_anterior']]['vl_juros_correcao_monetaria_divida_externa'], 2, ',', '.') ?> </td>
	</tr>
	<tr>
		<td class="padding-left-30"> Outros Encargos da Dívida </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_atual']]['vl_outros_encargos_da_divida'], 2, ',', '.') ?> </td>
		<td class="text_align_right"> <?= number_format($_data[$_data['exercicio_anterior']]['vl_outros_encargos_da_divida'], 2, ',', '.') ?> </td>
	</tr>

	<tr>
		<td class="height-10" colspan="3"> </td>
	</tr>

	<tr>
		<td> <b> Total dos Juros e Encargos da Dívida </b> </td>
		<td class="text_align_right border_top"> <?= number_format($_data[$_data['exercicio_atual']]['vl_total_correcao_monetaria'], 2, ',', '.') ?> </td>
		<td class="text_align_right border_top"> <?= number_format($_data[$_data['exercicio_anterior']]['vl_total_correcao_monetaria'], 2, ',', '.') ?> </td>
	</tr>
	</tbody>
</table>
