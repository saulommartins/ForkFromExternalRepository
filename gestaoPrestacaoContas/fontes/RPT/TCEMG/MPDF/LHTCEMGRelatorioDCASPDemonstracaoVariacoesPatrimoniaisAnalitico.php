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
			<th class="text_align_left" style="background: none; font-weight: normal;"> VARIAÇÕES PATRIMONIAIS AUMENTATIVAS </th>
			<th colspan="3" style="background: none;"></th>
		</tr>
	</thead>
	<tbody>
		
		<tr>
			<td> <b> Impostos, Taxas e Contribuições de Melhoria </b> </td>
			<td colspan="3"></td>
		</tr>
		
		<tr>
			<td class="padding-left-15"> Impostos </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_impostos_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_impostos_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>
		
		<tr>
			<td class="padding-left-15"> Taxas </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_taxas_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_taxas_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>
		
		<tr>
			<td class="padding-left-15"> Contribuições de Melhoria </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_contribuicoes_melhoria_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_contribuicoes_melhoria_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>
	
		<tr>
			<td colspan="4" class="height-10"></td>
		</tr>

		<tr>
			<td> <b> Contribuições </b> </td>
			<td colspan="3"></td>
		</tr>

		<tr>
			<td class="padding-left-15"> Contribuições Sociais </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_contribuicoes_sociais_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_contribuicoes_sociais_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-15"> Contribuições de Intervenção no Domínio Econômico </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_contribuicoes_dominio_economico_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_contribuicoes_dominio_economico_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-15"> Contribuição de Iluminação Pública </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_contribuicao_iluminacao_publica_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_contribuicao_iluminacao_publica_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-15"> Contribuições de Interesse das Categorias Profissionais </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_contribuicoes_cat_profissionais_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_contribuicoes_cat_profissionais_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>
		
		<tr>
			<td colspan="4" class="height-10"></td>
		</tr>

		<tr>
			<td> <b> Exploração e Venda de Bens, Serviços e Direitos </b> </td>
			<td colspan="3"></td>
		</tr>

		<tr>
			<td class="padding-left-15"> Vendas de Mercadorias </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_vendas_mercadorias_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_vendas_mercadorias_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-15"> Vendas de Produtos </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_vendas_products_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_vendas_products_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-15"> Exploração de Bens, Direitos e Prestação de Serviços </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_exploracao_bens_direitos_servicos_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_exploracao_bens_direitos_servicos_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td colspan="4" class="height-10"></td>
		</tr>

		<tr>
			<td colspan="4" class="height-10"></td>
		</tr>

		<tr>
			<td> <b> Variações Patrimoniais Aumentativas Financeiras </b> </td>
			<td colspan="3"></td>
		</tr>

		<tr>
			<td colspan="4" class="height-10"></td>
		</tr>

		<tr>
			<td class="padding-left-15"> Juros e Encargos de Empréstimos e Financiamentos Concedidos </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_juros_encargos_emprestimos_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_juros_encargos_emprestimos_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-15"> Juros e Encargos de Mora </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_juros_encargos_mora_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_juros_encargos_mora_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-15"> Descontos Financeiros Obtidos </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_variacoes_monetarias_cambiais_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_variacoes_monetarias_cambiais_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-15"> Remuneração de Depósitos Bancários e Aplicações Financeiras </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_remuneracao_depos_aplicacoes_financeiras_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_remuneracao_depos_aplicacoes_financeiras_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-15"> Outras Variações Patrimoniais Aumentativas Financeiras </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_out_var_patr_aument_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_out_var_patr_aument_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td colspan="4" class="height-10"></td>
		</tr>

		<tr>
			<td> <b> Transferências e Delegaões Recebidas </b> </td>
			<td colspan="3"></td>
		</tr>

		<tr>
			<td class="padding-left-15"> Transferências Intragovernamentais </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_transferencias_intragovernamentais_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_transferencias_intragovernamentais_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-15"> Transferências Intergovernamentais </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_transferencias_intergovernamentais_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_transferencias_intergovernamentais_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-15"> Transferências das Instituições Privadas </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_transferencias_instituicoes_privadas_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_transferencias_instituicoes_privadas_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-15"> Transferências das Instituições Multigovernamentais </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_transf_inst_multigover_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_transf_inst_multigover_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-15"> Transferências de Consórcios Públicos </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_transferencias_consorcios_publicos_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_transferencias_consorcios_publicos_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-15"> Transferências do Exterior </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_transferencias_exterior_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_transferencias_exterior_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-15"> Execução Orçamentária Delegada de Entes </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_execucao_orcamentaria_delegada_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_execucao_orcamentaria_delegada_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-15"> Transferências de Pessoas Físicas </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_transferencias_pessoas_fisicas_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_transferencias_pessoas_fisicas_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-15"> Outras Transferências e Delegações Recebidas </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_outr_transf_deleg_rec_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_outr_transf_deleg_rec_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td colspan="4" class="height-10"></td>
		</tr>

		<tr>
			<td> <b> Valorização e Ganhos com Ativos e Desincorporação de Passivos </b> </td>
			<td colspan="3"></td>
		</tr>

		<tr>
			<td class="padding-left-15"> Reavaliação de Ativos </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_reavaliacao_ativos_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_reavaliacao_ativos_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-15"> Ganhos com Alienação </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_valorizacao_ativo_desincor_passivo_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_valorizacao_ativo_desincor_passivo_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-15"> Ganhos com Incorporação de Ativos </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_ganhos_incorporacao_ativo_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_ganhos_incorporacao_ativo_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-15"> Desincorporação de Passivos </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_desincorporacao_passivos_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_desincorporacao_passivos_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-15"> Reversão de Redução ao Valor Recuperável </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_reversao_reducao_valor_recuperavel_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_reversao_reducao_valor_recuperavel_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td colspan="4" class="height-10"></td>
		</tr>

		<tr>
			<td> <b> Outras Variações Patrimoniais Aumentativas </b> </td>
			<td colspan="3"></td>
		</tr>

		<tr>
			<td class="padding-left-15"> VPA a classificar </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_vpa_classificar_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_vpa_classificar_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-15"> Resultado Positivo de Participações </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_result_positivo_participacoes_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_result_positivo_participacoes_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-15"> Reversão de Provisões e Ajustes para Perdas </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_reversao_provisoes_ajustes_perdas_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_reversao_provisoes_ajustes_perdas_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-15"> Diversas Variações Patrimoniais Aumentativas </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_diver_var_patrim_aument_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[10]['vl_diver_var_patrim_aument_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td colspan="4" class="height-10"></td>
		</tr>

		<tr>
			<td class="italico"> <b> Total das Variações Patrimoniais Aumentativas (I) </b> </td>
			<td></td>
			<td class="text_align_right border_top"> <?= number_format($_data[10]['vl_total_vp_aumentativas_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right border_top"> <?= number_format($_data[10]['vl_total_vp_aumentativas_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>
	</tbody>
</table>

<pagebreak></pagebreak>

<table>
	<tbody>
		<tr>
			<td colspan="4" class="height-10"></td>
		</tr>

		<tr>
			<td> VARIAÇÕES PATRIMONIAIS DIMINUTIVAS </td>
			<td colspan="3"></td>
		</tr>

		<tr>
			<td colspan="4" class="height-10"></td>
		</tr>

		<tr>
			<td> <b> Pessoal e Encargos </b> </td>
			<td colspan="3"></td>
		</tr>

		<tr>
			<td class="padding-left-15"> Remuneração a Pessoal </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_remuneracao_pessoal_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_remuneracao_pessoal_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-15"> Encargos Patronais </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_encargos_patronais_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_encargos_patronais_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-15"> Benefícios a Pessoal </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_beneficios_pessoal_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_beneficios_pessoal_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-15"> Outras Variações Patrimoniais Diminutivas - Pessoal e Encargos </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_outras_pessoal_encargos_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_outras_pessoal_encargos_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td colspan="4" class="height-10"></td>
		</tr>

		<tr>
			<td> <b> Benefícios Previdenciários e Assistenciais </b> </td>
			<td colspan="3"></td>
		</tr>

		<tr>
			<td class="padding-left-15"> Aposentadorias e Reformas </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_aposentadorias_reformas_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_aposentadorias_reformas_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-15"> Pensões </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_pensoes_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_pensoes_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-15"> Benefícios de Prestação Continuada </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_prestacao_continuada_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_prestacao_continuada_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-15"> Benefícios Eventuais </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_beneficios_eventuais_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_beneficios_eventuais_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-15"> Políticas Públicas de Transferência de Renda </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_pol_pub_transf_renda_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_pol_pub_transf_renda_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-15"> Outros Benefícios Previdenciários e Assistenciais </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_outros_ben_prev_assist_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_outros_ben_prev_assist_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td colspan="4" class="height-10"></td>
		</tr>

		<tr>
			<td colspan="4" class="height-10"></td>
		</tr>

		<tr>
			<td> <b> Uso de Bens, Serviços e Consumo de Capital Fixo </b> </td>
			<td colspan="3"></td>
		</tr>

		<tr>
			<td class="padding-left-15"> Uso de Material de Consumo </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_material_consumo_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_material_consumo_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-15"> Serviços </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_servicos_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_servicos_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-15"> Depreciação, Amortização e Exaustão </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_depre_amort_exaustao_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_depre_amort_exaustao_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td colspan="4" class="height-10"></td>
		</tr>

		<tr>
			<td> <b> Variações Patrimoniais Diminutivas Financeiras </b> </td>
			<td colspan="3"></td>
		</tr>
		
		<tr>
			<td class="padding-left-15"> Juros e Encargos de Empréstimos e Financiamentos Obtidos </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_juros_encarg_empres_finan_obtidos_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_juros_encarg_empres_finan_obtidos_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>
		
		<tr>
			<td class="padding-left-15"> Juros e Encargos de Mora </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_juros_encargos_mora_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_juros_encargos_mora_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>
		
		<tr>
			<td class="padding-left-15"> Variações Monetárias e Cambiais </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_variacoes_monetarias_cambiais_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_variacoes_monetarias_cambiais_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>
		
		<tr>
			<td class="padding-left-15"> Descontos Financeiros Concedidos </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_descontos_financeiros_concedidos_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_descontos_financeiros_concedidos_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>
		
		<tr>
			<td class="padding-left-15"> Outras Variações Patrimoniais Diminutivas – Financeiras </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_outras_var_patri_dim_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_outras_var_patri_dim_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>
		
		<tr>
			<td colspan="4" class="height-10"></td>
		</tr>

		<tr>
			<td> <b> Transferências e Delegações Concedidas </b> </td>
			<td colspan="3"></td>
		</tr>
		
		<tr>
			<td class="padding-left-15"> Transferências Intragovernamentais </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_transf_intragov_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_transf_intragov_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-15"> Transferências Intergovernamentais </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_transf_intergov_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_transf_intergov_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-15"> Transferências a Instituições Privadas </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_transf_inst_priv_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_transf_inst_priv_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-15"> Transferências a Instituições Multigovernamentais </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_transf_inst_multigov_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_transf_inst_multigov_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-15"> Transferências a Consórcios Públicos </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_transf_consor_pub_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_transf_consor_pub_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-15"> Transferências ao Exterior </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_transf_exterior_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_transf_exterior_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-15"> Execução Orçamentária Delegada de Entes </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_exec_orc_dele_entes_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_exec_orc_dele_entes_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-15"> Outras Transferências e Delegações Concedidas </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_outras_transf_dele_conce_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_outras_transf_dele_conce_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td colspan="4" class="height-10"></td>
		</tr>

		<tr>
			<td colspan="4" class="height-10"></td>
		</tr>

		<tr>
			<td> <b> Desvalorização e Perdas de Ativos e Incorporação de Passivos </b> </td>
			<td colspan="3"></td>
		</tr>

		<tr>
			<td class="padding-left-15"> Redução a Valor Recuperável e Ajuste para Perdas </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_redu_vl_recu_ajs_perdas_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_redu_vl_recu_ajs_perdas_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-15"> Perdas com Alienação </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_perdas_alienacao_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_perdas_alienacao_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-15"> Perdas Involuntárias </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_perdas_involuntarias_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_perdas_involuntarias_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-15"> Incorporação de Passivos </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_incorp_passivos_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_incorp_passivos_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-15"> Desincorporação de Ativos </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_desincorporacao_ativos_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_desincorporacao_ativos_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td colspan="4" class="height-10"></td>
		</tr>

		<tr>
			<td> <b> Tributárias </b> </td>
			<td colspan="3"></td>
		</tr>

		<tr>
			<td class="padding-left-15"> Impostos, Taxas e Contribuições de Melhoria </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_impostos_taxas_contrib_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_impostos_taxas_contrib_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-15"> Contribuições </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_contribuicoes_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_contribuicoes_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td colspan="4" class="height-10"></td>
		</tr>

		<tr>
			<td> <b> Custo das Mercadorias e Produtos Vendidos, e dos Serviços Prestados </b> </td>
			<td colspan="3"></td>
		</tr>

		<tr>
			<td class="padding-left-15"> Custos das Mercadorias Vendidas </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_custos_mercad_vendidas_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_custos_mercad_vendidas_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-15"> Custos dos Produtos Vendidos </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_custos_prod_vendidos_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_custos_prod_vendidos_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-15"> Custos dos Serviços Prestados </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_custos_serv_prestados_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_custos_serv_prestados_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td colspan="4" class="height-10"></td>
		</tr>

		<tr>
			<td> <b> Outras Variações Patrimoniais Diminutivas </b> </td>
			<td colspan="3"></td>
		</tr>

		<tr>
			<td class="padding-left-15"> Premiações </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_premiacoes_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_premiacoes_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-15"> Resultado Negativo de Participações </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_result_negativo_particip_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_result_negativo_particip_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-15"> Incentivos </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_incentivos_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_incentivos_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-15"> Subvenções Econômicas </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_subvencoes_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_subvencoes_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-15"> Participações e Contribuições </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_participacoes_contribuicoes_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_participacoes_contribuicoes_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-15"> Constituição de Provisões </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_provisoes_contribuicoes_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_provisoes_contribuicoes_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td class="padding-left-15"> Diversas Variações Patrimoniais Diminutivas </td>
			<td></td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_div_varia_patrimoni_diminutivas_exercicio_atual'], 2, ',', '.') ?> </td>
			<td class="text_align_right"> <?= number_format($_data[20]['vl_div_varia_patrimoni_diminutivas_exercicio_anterior'], 2, ',', '.') ?> </td>
		</tr>

		<tr>
			<td colspan="4" class="height-10"></td>
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
