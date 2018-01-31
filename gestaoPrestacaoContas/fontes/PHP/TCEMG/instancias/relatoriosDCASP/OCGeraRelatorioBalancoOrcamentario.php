<?php

    ini_set("display_errors", 1);
    error_reporting(E_ALL);

    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

    include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php" );
    include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoUsuario.class.php" );
    include_once ( CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGRelatorioBalancoOrcamentario.class.php");

    include_once CLA_MPDF;

    $obMPDF = new FrameWorkMPDF(6, 55, 17);
    $obMPDF->setCodEntidades($request->get('inCodEntidade'));
    $obMPDF->setDataInicio($request->get("stDataInicial"));
    $obMPDF->setDataFinal($request->get("stDataFinal"));

    $obMPDF->setNomeRelatorio("DCASP - Balanço Orçamentário");

    $obTTCEMGRelatorioBalancoOrcamentario = new TTCEMGRelatorioBalancoOrcamentario();
    $obTTCEMGRelatorioBalancoOrcamentario->setDado('exercicio'    , Sessao::getExercicio());
    $obTTCEMGRelatorioBalancoOrcamentario->setDado('dtInicial'    , $_REQUEST['stDataInicial']);
    $obTTCEMGRelatorioBalancoOrcamentario->setDado('dtFinal'      , $_REQUEST['stDataFinal']);
    $obTTCEMGRelatorioBalancoOrcamentario->setDado('entidades'    , implode(',',$_REQUEST['inCodEntidade']));


    $rsRecordSet10 = new RecordSet;
    $obTTCEMGRelatorioBalancoOrcamentario->recuperaDadosBalancoOrcamentario("montaRecuperaDadosBalancoOrcamentario10", $rsRecordSet10);
    $_data['10'] = $rsRecordSet10->getObjeto();

    $_data[10]['vl_subtotal_previsao_inicial'] = (
        $_data[10]['vl_rec_tributaria_previsao_inicial']   
        + $_data[10]['vl_rec_contribuicoes_previsao_inicial']      
        + $_data[10]['vl_rec_patrimonial_previsao_inicial']   
        + $_data[10]['vl_rec_agropecuaria_previsao_inicial'] 
        + $_data[10]['vl_rec_industrial_previsao_inicial']         
        + $_data[10]['vl_rec_servicos_previsao_inicial']      
        + $_data[10]['vl_transf_correntes_previsao_inicial'] 
        + $_data[10]['vl_outras_rec_correntes_previsao_inicial']   
        + $_data[10]['vl_operacoes_credito_previsao_inicial'] 
        + $_data[10]['vl_alienacao_bens_previsao_inicial']   
        + $_data[10]['vl_amortizacao_emprestimo_previsao_inicial'] 
        + $_data[10]['vl_transf_capital_previsao_inicial']    
        + $_data[10]['vl_outras_rec_capital_previsao_inicial']
    );

    $_data[10]['vl_subtotal_previsao_atualizada'] = (
        $_data[10]['vl_rec_tributaria_previsao_atualizada']   
        + $_data[10]['vl_rec_contribuicoes_previsao_atualizada']     
        + $_data[10]['vl_rec_patrimonial_previsao_atualizada']   
        + $_data[10]['vl_rec_agropecuaria_previsao_atualizada'] 
        + $_data[10]['vl_rec_industrial_previsao_atualizada']         
        + $_data[10]['vl_rec_servicos_previsao_atualizada']      
        + $_data[10]['vl_transf_correntes_previsao_atualizada'] 
        + $_data[10]['vl_outras_rec_correntes_previsao_atualizada']   
        + $_data[10]['vl_operacoes_credito_previsao_atualizada'] 
        + $_data[10]['vl_alienacao_bens_previsao_atualizada']   
        + $_data[10]['vl_amortizacao_emprestimo_previsao_atualizada'] 
        + $_data[10]['vl_transf_capital_previsao_atualizada']    
        + $_data[10]['vl_outras_rec_capital_previsao_atualizada']
    );

    $_data[10]['vl_subtotal_realizada'] = (
        $_data[10]['vl_rec_tributaria_realizada']
        + $_data[10]['vl_rec_contribuicoes_realizada']
        + $_data[10]['vl_rec_patrimonial_realizada']
        + $_data[10]['vl_rec_agropecuaria_realizada']
        + $_data[10]['vl_rec_industrial_realizada']
        + $_data[10]['vl_rec_servicos_realizada']
        + $_data[10]['vl_transf_correntes_realizada']
        + $_data[10]['vl_outras_rec_correntes_realizada']
        + $_data[10]['vl_operacoes_credito_realizada']
        + $_data[10]['vl_alienacao_bens_realizada']
        + $_data[10]['vl_amortizacao_emprestimo_realizada']
        + $_data[10]['vl_transf_capital_realizada']
        + $_data[10]['vl_outras_rec_capital_realizada']
    );

    $_data[10]['vl_subtotal_saldo'] = ($_data[10]['vl_subtotal_realizada'] - $_data[10]['vl_subtotal_previsao_atualizada']);


    $_data[10]['vl_subtotal_com_refinanciamento_previsao_inicial'] = (
        $_data[10]['vl_subtotal_previsao_inicial']
        + $_data[10]['vl_opera_credito_refina_internas_mobiliaria_previsao_inicial'] 
        + $_data[10]['vl_opera_credito_refina_internas_contratual_previsao_inicial'] 
        + $_data[10]['vl_opera_credito_refina_externas_mobiliaria_previsao_inicial']
        + $_data[10]['vl_opera_credito_refina_externas_contratual_previsao_inicial']
    );
    
    $_data[10]['vl_subtotal_com_refinanciamento_previsao_atualizada'] = (
        $_data[10]['vl_subtotal_previsao_atualizada']
        + $_data[10]['vl_opera_credito_refina_internas_mobiliaria_previsao_atualizada'] 
        + $_data[10]['vl_opera_credito_refina_internas_contratual_previsao_atualizada'] 
        + $_data[10]['vl_opera_credito_refina_externas_mobiliaria_previsao_atualizada']
        + $_data[10]['vl_opera_credito_refina_externas_contratual_previsao_atualizada']
    );
    
    $_data[10]['vl_subtotal_com_refinanciamento_realizada'] = (
        $_data[10]['vl_subtotal_realizada']
        + $_data[10]['vl_opera_credito_refina_internas_mobiliaria_realizada'] 
        + $_data[10]['vl_opera_credito_refina_internas_contratual_realizada'] 
        + $_data[10]['vl_opera_credito_refina_externas_mobiliaria_realizada']
        + $_data[10]['vl_opera_credito_refina_externas_contratual_realizada']
    );

    // $_data[10]['vl_subtotal_com_refinanciamento_saldo'] = ();
    $_data[10]['vl_subtotal_com_refinanciamento_saldo'] = ($_data[10]['vl_subtotal_com_refinanciamento_realizada'] - $_data[10]['vl_subtotal_com_refinanciamento_previsao_atualizada']);


    $rsRecordSet20 = new RecordSet;
    $obTTCEMGRelatorioBalancoOrcamentario->recuperaDadosBalancoOrcamentario("montaRecuperaDadosBalancoOrcamentario20", $rsRecordSet20);
    $_data[20] = $rsRecordSet20->getObjeto();

    $rsRecordSet30 = new RecordSet;
    $obTTCEMGRelatorioBalancoOrcamentario->recuperaDadosBalancoOrcamentario("montaRecuperaDadosBalancoOrcamentario30", $rsRecordSet30);
    $_data[30] = $rsRecordSet30->getObjeto();

    $_data[30]['vl_subtotal_despesas_inicial'] = (
        $_data[30]['vl_pessoal_encar_social_dotacao_inicial']
        + $_data[30]['vl_juros_encar_dividas_dotacao_inicial']
        + $_data[30]['vl_outras_desp_correntes_dotacao_inicial']
        + $_data[30]['vl_outras_desp_correntes_dotacao_inicial']
        + $_data[30]['vl_investimentos_dotacao_inicial']
        + $_data[30]['vl_inver_financeira_dotacao_inicial']
        + $_data[30]['vl_amortiza_divida_dotacao_inicial']
        + $_data[30]['vl_reserva_contingencia_dotacao_inicial']
    );

    $_data[30]['vl_subtotal_despesas_atualizada'] = (
        $_data[30]['vl_pessoal_encar_social_dotacao_atualizada']
        + $_data[30]['vl_juros_encar_dividas_dotacao_atualizada']
        + $_data[30]['vl_outras_desp_correntes_dotacao_atualizada']
        + $_data[30]['vl_outras_desp_correntes_dotacao_atualizada']
        + $_data[30]['vl_investimentos_dotacao_atualizada']
        + $_data[30]['vl_inver_financeira_dotacao_atualizada']
        + $_data[30]['vl_amortiza_divida_dotacao_atualizada']
        + $_data[30]['vl_reserva_contingencia_dotacao_atualizada']
    );;
    
    $_data[30]['vl_subtotal_despesas_empenhadas'] = (
        $_data[30]['vl_pessoal_encar_social_despesas_empenhadas']
        + $_data[30]['vl_juros_encar_dividas_despesas_empenhadas']
        + $_data[30]['vl_outras_desp_correntes_despesas_empenhadas']
        + $_data[30]['vl_outras_desp_correntes_despesas_empenhadas']
        + $_data[30]['vl_investimentos_despesas_empenhadas']
        + $_data[30]['vl_inver_financeira_despesas_empenhadas']
        + $_data[30]['vl_amortiza_divida_despesas_empenhadas']
        + $_data[30]['vl_reserva_contingencia_despesas_empenhadas']
    );;
    
    $_data[30]['vl_subtotal_despesas_liquidadas'] = (
        $_data[30]['vl_pessoal_encar_social_despesas_liquidadas']
        + $_data[30]['vl_juros_encar_dividas_despesas_liquidadas']
        + $_data[30]['vl_outras_desp_correntes_despesas_liquidadas']
        + $_data[30]['vl_outras_desp_correntes_despesas_liquidadas']
        + $_data[30]['vl_investimentos_despesas_liquidadas']
        + $_data[30]['vl_inver_financeira_despesas_liquidadas']
        + $_data[30]['vl_amortiza_divida_despesas_liquidadas']
        + $_data[30]['vl_reserva_contingencia_despesas_liquidadas']
    );;
    
    $_data[30]['vl_subtotal_despesas_pagas'] = (
        $_data[30]['vl_pessoal_encar_social_despesas_pagas']
        + $_data[30]['vl_juros_encar_dividas_despesas_pagas']
        + $_data[30]['vl_outras_desp_correntes_despesas_pagas']
        + $_data[30]['vl_outras_desp_correntes_despesas_pagas']
        + $_data[30]['vl_investimentos_despesas_pagas']
        + $_data[30]['vl_inver_financeira_despesas_pagas']
        + $_data[30]['vl_amortiza_divida_despesas_pagas']
        + $_data[30]['vl_reserva_contingencia_despesas_pagas']
    );;

    $_data[30]['vl_subtotal_com_refinanciamento_dotacao_inicial'] = (
        $_data[30]['vl_subtotal_despesas_inicial']
        + $_data[30]['vl_amortiza_divida_inter_mobiliaria_dotacao_inicial']
        + $_data[30]['vl_amortiza_outras_dividas_internas_dotacao_inicial']
        + $_data[30]['vl_amortiza_divida_exter_mobiliaria_dotacao_inicial']
        + $_data[30]['vl_amortiza_outras_dividas_externas_dotacao_inicial']
    );

    $_data[30]['vl_subtotal_com_refinanciamento_dotacao_atualizada'] = (
        $_data[30]['vl_subtotal_despesas_atualizada']
        + $_data[30]['vl_amortiza_divida_inter_mobiliaria_dotacao_atualizada']
        + $_data[30]['vl_amortiza_outras_dividas_internas_dotacao_atualizada']
        + $_data[30]['vl_amortiza_divida_exter_mobiliaria_dotacao_atualizada']
        + $_data[30]['vl_amortiza_outras_dividas_externas_dotacao_atualizada']
    );

    $_data[30]['vl_subtotal_com_refinanciamento_despesas_empenhadas'] = (
        $_data[30]['vl_subtotal_despesas_empenhadas']
        + $_data[30]['vl_amortiza_divida_inter_mobiliaria_despesas_empenhadas']
        + $_data[30]['vl_amortiza_outras_dividas_internas_despesas_empenhadas']
        + $_data[30]['vl_amortiza_divida_exter_mobiliaria_despesas_empenhadas']
        + $_data[30]['vl_amortiza_outras_dividas_externas_despesas_empenhadas']
    );

    $_data[30]['vl_subtotal_com_refinanciamento_despesas_liquidadas'] = (
        $_data[30]['vl_subtotal_despesas_liquidadas']
        + $_data[30]['vl_amortiza_divida_inter_mobiliaria_despesas_liquidadas']
        + $_data[30]['vl_amortiza_outras_dividas_internas_despesas_liquidadas']
        + $_data[30]['vl_amortiza_divida_exter_mobiliaria_despesas_liquidadas']
        + $_data[30]['vl_amortiza_outras_dividas_externas_despesas_liquidadas']
    );

    $_data[30]['vl_subtotal_com_refinanciamento_despesas_pagas'] = (
        $_data[30]['vl_subtotal_despesas_pagas']
        + $_data[30]['vl_amortiza_divida_inter_mobiliaria_despesas_pagas']
        + $_data[30]['vl_amortiza_outras_dividas_internas_despesas_pagas']
        + $_data[30]['vl_amortiza_divida_exter_mobiliaria_despesas_pagas']
        + $_data[30]['vl_amortiza_outras_dividas_externas_despesas_pagas']
    );

    $_data[10]['vl_deficit_previsao_inicial'] = 0;
    $_data[10]['vl_deficit_previsao_atualizada'] = 0;
    $_data[10]['vl_deficit_realizada'] = 0;
    $_data[10]['vl_deficit_saldo'] = 0;

    $deficitPrevisaoInicial = $_data[10]['vl_subtotal_previsao_inicial'] - $_data[30]['vl_subtotal_despesas_inicial'];
    $deficitPrevisaoAtualizada = $_data[10]['vl_subtotal_previsao_atualizada'] - $_data[30]['vl_subtotal_despesas_atualizada'];
    $deficitRealizada = $_data[10]['vl_subtotal_realizada'] - $_data[30]['vl_subtotal_despesas_pagas'];
    $deficitSaldo = $_data[10]['vl_deficit_realizada'] - $_data[30]['vl_deficit_previsao_atualizada'];

    if ($deficitPrevisaoInicial < 0) {
        $_data[10]['vl_deficit_previsao_inicial'] = $deficitPrevisaoInicial;
    }

    if ($deficitPrevisaoAtualizada < 0) {
        $_data[10]['vl_deficit_previsao_atualizada'] = $deficitPrevisaoAtualizada;
    }

    if ($deficitRealizada < 0) {
        $_data[10]['vl_deficit_realizada'] = $deficitRealizada;
    }

    if ($deficitSaldo < 0) {
        $_data[10]['vl_deficit_saldo'] = $deficitSaldo;
    }

    $_data[10]['total_previsao_inicial'] = $_data[10]['vl_deficit_previsao_inicial'] + $_data[10]['vl_subtotal_com_refinanciamento_previsao_inicial'];
    $_data[10]['total_previsao_atualizada'] = $_data[10]['vl_deficit_previsao_atualizada'] + $_data[10]['vl_subtotal_com_refinanciamento_previsao_atualizada'];
    $_data[10]['total_realizado'] = $_data[10]['vl_deficit_realizada'] + $_data[10]['vl_subtotal_com_refinanciamento_realizada'];
    $_data[10]['total_saldo'] = $_data[10]['vl_deficit_saldo'] + $_data[10]['vl_subtotal_com_refinanciamento_saldo'];

    $_data[30]['vl_superavit_dotacao_inicial'] = 0;
    $_data[30]['vl_superavit_dotacao_atualizada'] = 0;
    $_data[30]['vl_superavit_despesas_empenhadas'] = 0;
    $_data[30]['vl_superavit_despesas_liquidadas'] = 0;
    $_data[30]['vl_superavit_despesas_pagas'] = 0;
    $_data[30]['vl_superavit_saldo'] = 0;

    $superavitDotacaoInicial = $_data[10]['total_previsao_inicial'] - $_data[30]['vl_subtotal_com_refinanciamento_dotacao_inicial'];
    $superavitDotacaoAtualizada = $_data[10]['total_previsao_atualizada'] - $_data[30]['vl_subtotal_com_refinanciamento_dotacao_atualizada'];
    $superavitDespesasEmpenhadas = $_data[10]['total_previsao_atualizada'] - $_data[30]['vl_subtotal_com_refinanciamento_despesas_empenhadas'];
    $superavitDespesasLiquidadas = $_data[10]['total_previsao_atualizada'] - $_data[30]['vl_subtotal_com_refinanciamento_despesas_liquidadas'];
    $superavitDespesasPagas = $_data[10]['total_realizado'] - $_data[30]['vl_subtotal_com_refinanciamento_despesas_pagas'];

    if ($superavitDotacaoInicial > 0) {
        $_data[30]['vl_superavit_dotacao_inicial'] = $superavitDotacaoInicial;
    }

    if ($superavitDotacaoAtualizada > 0) {
        $_data[30]['vl_superavit_dotacao_atualizada'] = $superavitDotacaoAtualizada;
    }

    if ($superavitDespesasEmpenhadas > 0) {
        $superavitSaldo = $superavitDotacaoAtualizada - $superavitDespesasEmpenhadas;
        $_data[30]['vl_superavit_despesas_empenhadas'] = $superavitDespesasEmpenhadas;
    }

    if ($superavitDespesasLiquidadas > 0) {
        $_data[30]['vl_superavit_despesas_liquidadas'] = $superavitDespesasLiquidadas;
    }

    if ($superavitDespesasPagas > 0) {
        $_data[30]['vl_superavit_despesas_pagas'] = $superavitDespesasPagas;
    }

    if ($superavitSaldo > 0) {
        $_data[30]['vl_superavit_saldo'] = $superavitSaldo;
    }

    $_data[30]['total_dotacao_inicial'] = ($_data[30]['vl_subtotal_com_refinanciamento_dotacao_inicial'] + $_data[30]['vl_superavit_dotacao_inicial']);
    $_data[30]['total_dotacao_atualizada'] = ($_data[30]['vl_subtotal_com_refinanciamento_dotacao_atualizada'] + $_data[30]['vl_superavit_dotacao_atualizada']);
    $_data[30]['total_despesas_empenhadas'] = ($_data[30]['vl_subtotal_com_refinanciamento_despesas_empenhadas'] + $_data[30]['vl_superavit_despesas_empenhadas']);
    $_data[30]['total_despesas_liquidadas'] = ($_data[30]['vl_subtotal_com_refinanciamento_despesas_liquidadas'] + $_data[30]['vl_superavit_despesas_liquidadas']);
    $_data[30]['total_despesas_pagas'] = ($_data[30]['vl_subtotal_com_refinanciamento_despesas_pagas'] + $_data[30]['vl_superavit_despesas_pagas']);
    $_data[30]['total_saldo_dotacao'] = ($_data[30]['vl_subtotal_com_refinanciamento_saldo'] + $_data[30]['vl_superavit_saldo']);


    $obMPDF->setConteudo($_data);
    $obMPDF->gerarRelatorio();

?>