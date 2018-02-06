<?php

    ini_set("display_errors", 1);
    error_reporting(E_ALL);

    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

    include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php" );
    include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoUsuario.class.php" );
    include_once ( CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGRelatorioBalancoFinanceiro.class.php");

    include_once CLA_MPDF;

    $obMPDF = new FrameWorkMPDF(6, 55, 18);
    $obMPDF->setCodEntidades($request->get('inCodEntidade'));
    $obMPDF->setDataInicio($request->get("stDataInicial"));
    $obMPDF->setDataFinal($request->get("stDataFinal"));
    $obMPDF->use_kwt = true; 

    $obMPDF->setNomeRelatorio("DCASP - Balanço Financeiro");

    $obTTCEMGRelatorioBalancoFinanceiro = new TTCEMGRelatorioBalancoFinanceiro();
    $obTTCEMGRelatorioBalancoFinanceiro->setDado('exercicio'    , Sessao::getExercicio());
    $obTTCEMGRelatorioBalancoFinanceiro->setDado('dtInicial'    , $_REQUEST['stDataInicial']);
    $obTTCEMGRelatorioBalancoFinanceiro->setDado('dtFinal'      , $_REQUEST['stDataFinal']);
    $obTTCEMGRelatorioBalancoFinanceiro->setDado('entidades'    , implode(',',$_REQUEST['inCodEntidade']));

    $_data['exercicio'] = Sessao::read('exercicio');

    $rsRecordSet10 = new RecordSet;
    $obTTCEMGRelatorioBalancoFinanceiro->recuperaDadosBalancoFinanceiro("montaRecuperaDadosBalancoFinanceiro10", $rsRecordSet10);
    $_data[10] = $rsRecordSet10->getObjeto();

    $_data[10]['vl_total_exercicio_atual'] = (
        $_data[10]['vl_rec_orc_recurso_ordinario_exercicio_atual']
        + $_data[10]['vl_rec_orc_recursos_vinculado_educacao_exercicio_atual']
        + $_data[10]['vl_rec_orc_recursos_vinculado_saude_exercicio_atual']
        + $_data[10]['vl_rec_orc_recursos_vinculado_rpps_exercicio_atual']
        + $_data[10]['vl_rec_orc_recursos_vinculado_rgps_exercicio_atual']
        + $_data[10]['vl_rec_orc_recursos_vinculado_assist_social_exercicio_atual']
        + $_data[10]['vl_rec_orc_outra_destinac_recurso_exercicio_atual']
        + $_data[10]['vl_trans_finan_execucao_orcamentaria_exercicio_atual']
        + $_data[10]['vl_trans_finan_indepen_execucao_orcamentaria_exercicio_atual']
        + $_data[10]['vl_trans_finan_recebida_aporte_rec_rpps_exercicio_atual']
        + $_data[10]['vl_trans_finan_recebida_aporte_rec_rgps_exercicio_atual']
        + $_data[10]['vl_inscri_resto_pagar_nao_processado_exercicio_atual']
        + $_data[10]['vl_inscri_resto_pagar_processado_exercicio_atual']
        + $_data[10]['vl_depo_restituivel_vinculado_exercicio_atual']
        + $_data[10]['vl_outr_recebimento_extraorcamentario_exercicio_atual']
        + $_data[10]['vl_sal_exerc_anterior_caixa_equivalente_caixa_exercicio_atual']
        + $_data[10]['vl_sal_exerc_anterior_deposito_restitui_valor_vinculado_exercicio_atual']
    );

    $_data[10]['vl_total_exercicio_anterior'] = (
        $_data[10]['vl_rec_orc_recurso_ordinario_exercicio_anterior']
        + $_data[10]['vl_rec_orc_recursos_vinculado_educacao_exercicio_anterior']
        + $_data[10]['vl_rec_orc_recursos_vinculado_saude_exercicio_anterior']
        + $_data[10]['vl_rec_orc_recursos_vinculado_rpps_exercicio_anterior']
        + $_data[10]['vl_rec_orc_recursos_vinculado_rgps_exercicio_anterior']
        + $_data[10]['vl_rec_orc_recursos_vinculado_assist_social_exercicio_anterior']
        + $_data[10]['vl_rec_orc_outra_destinac_recurso_exercicio_anterior']
        + $_data[10]['vl_trans_finan_execucao_orcamentaria_exercicio_anterior']
        + $_data[10]['vl_trans_finan_indepen_execucao_orcamentaria_exercicio_anterior']
        + $_data[10]['vl_trans_finan_recebida_aporte_rec_rpps_exercicio_anterior']
        + $_data[10]['vl_trans_finan_recebida_aporte_rec_rgps_exercicio_anterior']
        + $_data[10]['vl_inscri_resto_pagar_nao_processado_exercicio_anterior']
        + $_data[10]['vl_inscri_resto_pagar_processado_exercicio_anterior']
        + $_data[10]['vl_depo_restituivel_vinculado_exercicio_anterior']
        + $_data[10]['vl_outr_recebimento_extraorcamentario_exercicio_anterior']
        + $_data[10]['vl_sal_exerc_anterior_caixa_equivalente_caixa_exercicio_anterior']
        + $_data[10]['vl_sal_exerc_anterior_deposito_restitui_valor_vinculado_exercicio_anterior']
    );

    $rsRecordSet20 = new RecordSet;
    $obTTCEMGRelatorioBalancoFinanceiro->recuperaDadosBalancoFinanceiro("montaRecuperaDadosBalancoFinanceiro20", $rsRecordSet20);
    $_data[20] = $rsRecordSet20->getObjeto();


    $_data[20]['vl_total_exercicio_atual'] = (
        $_data[20]['vl_desp_orc_recurso_ordinario_exercicio_atual']
        + $_data[20]['vl_desp_orc_recursos_vinculado_educacao_exercicio_atual']
        + $_data[20]['vl_desp_orc_recursos_vinculado_saude_exercicio_atual']
        + $_data[20]['vl_desp_orc_recursos_vinculado_rpps_exercicio_atual']
        + $_data[20]['vl_desp_orc_recursos_vinculado_rgps_exercicio_atual']
        + $_data[20]['vl_desp_orc_recursos_vinculado_assist_social_exercicio_atual']
        + $_data[20]['vl_desp_orc_outra_destinac_recurso_exercicio_atual']
        + $_data[20]['vl_trans_finan_execucao_orcamentaria_exercicio_atual']
        + $_data[20]['vl_trans_finan_indepen_execucao_orcamentaria_exercicio_atual']
        + $_data[20]['vl_trans_finan_concedida_aporte_rec_rpps_exercicio_atual']
        + $_data[20]['vl_trans_finan_concedida_aporte_rec_rgps_exercicio_atual']
        + $_data[20]['vl_pag_restos_pagar_nao_processado_exercicio_atual']
        + $_data[20]['vl_pag_restos_pagar_processado_exercicio_atual']
        + $_data[20]['vl_depo_restituivel_vinculado_exercicio_atual']
        + $_data[20]['vl_outr_pagamento_extraorcamentario_exercicio_atual']
        + $_data[20]['vl_sal_exerc_atual_caixa_equivalente_caixa_exercicio_atual']
        + $_data[20]['vl_sal_exerc_atual_deposito_restitui_valor_vinculado_exercicio_atual']
    );


    $_data[20]['vl_total_exercicio_anterior'] = (
        $_data[20]['vl_desp_orc_recurso_ordinario_exercicio_anterior']
        + $_data[20]['vl_desp_orc_recursos_vinculado_educacao_exercicio_anterior']
        + $_data[20]['vl_desp_orc_recursos_vinculado_saude_exercicio_anterior']
        + $_data[20]['vl_desp_orc_recursos_vinculado_rpps_exercicio_anterior']
        + $_data[20]['vl_desp_orc_recursos_vinculado_rgps_exercicio_anterior']
        + $_data[20]['vl_desp_orc_recursos_vinculado_assist_social_exercicio_anterior']
        + $_data[20]['vl_desp_orc_outra_destinac_recurso_exercicio_anterior']
        + $_data[20]['vl_trans_finan_execucao_orcamentaria_exercicio_anterior']
        + $_data[20]['vl_trans_finan_indepen_execucao_orcamentaria_exercicio_anterior']
        + $_data[20]['vl_trans_finan_concedida_aporte_rec_rpps_exercicio_anterior']
        + $_data[20]['vl_trans_finan_concedida_aporte_rec_rgps_exercicio_anterior']
        + $_data[20]['vl_pag_restos_pagar_nao_processado_exercicio_anterior']
        + $_data[20]['vl_pag_restos_pagar_processado_exercicio_anterior']
        + $_data[20]['vl_depo_restituivel_vinculado_exercicio_anterior']
        + $_data[20]['vl_outr_pagamento_extraorcamentario_exercicio_anterior']
        + $_data[20]['vl_sal_exerc_atual_caixa_equivalente_caixa_exercicio_anterior']
        + $_data[20]['vl_sal_exerc_atual_deposito_restitui_valor_vinculado_exercicio_anterior']
    );

    // include_once '../../../../../../gestaoPrestacaoContas/fontes/RPT/TCEMG/MPDF/LHTCEMGRelatorioDCASPBalancoFinanceiro.php';

    $obMPDF->setConteudo($_data);
    $obMPDF->gerarRelatorio();

?>