<?php

    // ini_set("display_errors", 1);
    // error_reporting(E_ALL);

    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

    include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php" );
    include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoUsuario.class.php" );
    include_once ( CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGRelatorioBalancoPatrimonial.class.php");

    include_once CLA_MPDF;


    $obMPDF = new FrameWorkMPDF(6, 55, 19);
    $obMPDF->setCodEntidades($request->get('inCodEntidade'));
    $obMPDF->setDataInicio($request->get("stDataInicial"));
    $obMPDF->setDataFinal($request->get("stDataFinal"));
    $obMPDF->use_kwt = true; 

    $obMPDF->setNomeRelatorio("DCASP - Balanço Patrimonial");

    $obTTCEMGRelatorioBalancoPatrimonial = new TTCEMGRelatorioBalancoPatrimonial();
    $obTTCEMGRelatorioBalancoPatrimonial->setDado('exercicio'    , Sessao::getExercicio());
    $obTTCEMGRelatorioBalancoPatrimonial->setDado('entidades'    , implode(',',$_REQUEST['inCodEntidade']));
    // $obTTCEMGRelatorioBalancoPatrimonial->setDado('dtInicial'    , $_REQUEST['stDataInicial']);
    // $obTTCEMGRelatorioBalancoPatrimonial->setDado('dtFinal'      , $_REQUEST['stDataFinal']);



    $stDataInicialExercicioAtual = substr($_REQUEST['stDataInicial'], 0, 6) . Sessao::getExercicio();
    $stDataFinalExercicioAtual = substr($_REQUEST['stDataFinal'], 0, 6) . Sessao::getExercicio();
    $obTTCEMGRelatorioBalancoPatrimonial->setDado('stDataInicialExercicioAtual', $stDataInicialExercicioAtual);
    $obTTCEMGRelatorioBalancoPatrimonial->setDado('stDataFinalExercicioAtual', $stDataFinalExercicioAtual);

    $stDataInicialExercicioAnterior = substr($_REQUEST['stDataInicial'], 0, 6) . (intval(Sessao::getExercicio()) - 1);
    $stDataFinalExercicioAnterior = substr($_REQUEST['stDataFinal'], 0, 6) . (intval(Sessao::getExercicio()) - 1);
    $obTTCEMGRelatorioBalancoPatrimonial->setDado('stDataInicialExercicioAnterior', $stDataInicialExercicioAnterior);
    $obTTCEMGRelatorioBalancoPatrimonial->setDado('stDataFinalExercicioAnterior', $stDataFinalExercicioAnterior);
    
    $_data['exercicio'] = Sessao::read('exercicio');

    $rsRecordSet10 = new RecordSet;
    $obTTCEMGRelatorioBalancoPatrimonial->recuperaDadosBalancoPatrimonial("montaRecuperaDadosBalancoPatrimonial10", $rsRecordSet10);
    $_data[10] = $rsRecordSet10->getObjeto();


    $_data[10]['total_ativo_circulante_exercicio_atual'] = (
        $_data[10]['vl_ativo_circ_caixa_equival_caixa_exercicio_atual']
        + $_data[10]['vl_ativo_circ_cred_curto_prazo_exercicio_atual']
        + $_data[10]['vl_ativo_circ_invest_aplic_temp_curto_prazo_exercicio_atual']
        + $_data[10]['vl_ativo_circ_estoque_exercicio_atual']
        + $_data[10]['vl_ativo_circ_vpd_paga_antecipado_exercicio_atual']
    );

    $_data[10]['total_ativo_circulante_exercicio_anterior'] = (
        $_data[10]['vl_ativo_circ_caixa_equival_caixa_exercicio_anterior']
        + $_data[10]['vl_ativo_circ_cred_curto_prazo_exercicio_anterior']
        + $_data[10]['vl_ativo_circ_invest_aplic_temp_curto_prazo_exercicio_anterior']
        + $_data[10]['vl_ativo_circ_estoque_exercicio_anterior']
        + $_data[10]['vl_ativo_circ_vpd_paga_antecipado_exercicio_anterior']
    );

    $_data[10]['total_ativo_nao_circulante_exercicio_atual'] = (
        $_data[10]['vl_ativo_nao_circ_real_longo_prazo_exercicio_atual']
        + $_data[10]['vl_ativo_nao_circ_cred_longo_prazo_exercicio_atual']
        + $_data[10]['vl_ativo_nao_circ_invest_temp_longo_prazo_exercicio_atual']
        + $_data[10]['vl_ativo_nao_circ_estoque_exercicio_atual']
        + $_data[10]['vl_ativo_nao_circ_vpd_pago_antecipado_exercicio_atual']
        + $_data[10]['vl_ativo_nao_circ_investimento_exercicio_atual']
        + $_data[10]['vl_ativo_nao_circ_imobilizado_exercicio_atual']
        + $_data[10]['vl_ativo_nao_circ_intangivel_exercicio_atual']
    );

    $_data[10]['total_ativo_nao_circulante_exercicio_anterior'] = (
        $_data[10]['vl_ativo_nao_circ_real_longo_prazo_exercicio_anterior']
        + $_data[10]['vl_ativo_nao_circ_cred_longo_prazo_exercicio_anterior']
        + $_data[10]['vl_ativo_nao_circ_invest_temp_longo_prazo_exercicio_anterior']
        + $_data[10]['vl_ativo_nao_circ_estoque_exercicio_anterior']
        + $_data[10]['vl_ativo_nao_circ_vpd_pago_antecipado_exercicio_anterior']
        + $_data[10]['vl_ativo_nao_circ_investimento_exercicio_anterior']
        + $_data[10]['vl_ativo_nao_circ_imobilizado_exercicio_anterior']
        + $_data[10]['vl_ativo_nao_circ_intangivel_exercicio_anterior']
    );

    $_data[10]['total_ativo_exercicio_atual'] = (
        $_data[10]['total_ativo_circulante_exercicio_atual']
        + $_data[10]['total_ativo_nao_circulante_exercicio_atual']
    );

    $_data[10]['total_ativo_exercicio_anterior'] = (
        $_data[10]['total_ativo_circulante_exercicio_anterior']
        + $_data[10]['total_ativo_nao_circulante_exercicio_anterior']
    );


    $rsRecordSet20 = new RecordSet;
    $obTTCEMGRelatorioBalancoPatrimonial->recuperaDadosBalancoPatrimonial("montaRecuperaDadosBalancoPatrimonial20", $rsRecordSet20);
    $_data[20] = $rsRecordSet20->getObjeto();

    $_data[20]['total_passivo_circulante_exercicio_atual'] = (
        $_data[20]['vl_pass_circ_obrig_trab_previdenc_assist_pagar_curto_prazo_exercicio_atual']
        + $_data[20]['vl_pass_circ_emprest_financ_curto_prazo_exercicio_atual']
        + $_data[20]['vl_pass_circ_fornec_contas_pagar_curto_prazo_exercicio_atual']
        + $_data[20]['vl_pass_circ_obrig_fiscais_curto_prazo_exercicio_atual']
        + $_data[20]['vl_pass_circ_obrig_repart_outros_entes_exercicio_atual']
        + $_data[20]['vl_pass_circ_provisoes_curto_prazo_exercicio_atual']
        + $_data[20]['vl_pass_circ_dms_obrigacoes_curto_prazo_exercicio_atual']
    );

    $_data[20]['total_passivo_circulante_exercicio_anterior'] = (
        $_data[20]['vl_pass_circ_obrig_trab_previdenc_assist_pagar_curto_prazo_exercicio_anterior']
        + $_data[20]['vl_pass_circ_emprest_financ_curto_prazo_exercicio_anterior']
        + $_data[20]['vl_pass_circ_fornec_contas_pagar_curto_prazo_exercicio_anterior']
        + $_data[20]['vl_pass_circ_obrig_fiscais_curto_prazo_exercicio_anterior']
        + $_data[20]['vl_pass_circ_obrig_repart_outros_entes_exercicio_anterior']
        + $_data[20]['vl_pass_circ_provisoes_curto_prazo_exercicio_anterior']
        + $_data[20]['vl_pass_circ_dms_obrigacoes_curto_prazo_exercicio_anterior']
    );

    $_data[20]['total_passivo_nao_circulante_exercicio_atual'] = (
        $_data[20]['vl_pass_nao_circ_obrig_trab_prev_assist_pagar_longro_prazo_exercicio_atual']
        + $_data[20]['vl_pass_nao_circ_emp_financ_longo_prazo_exercicio_atual']
        + $_data[20]['vl_pass_nao_circ_fornec_contas_pagar_longo_prazo_exercicio_atual']
        + $_data[20]['vl_pass_nao_circ_obrig_fisc_longo_prazo_exercicio_atual']
        + $_data[20]['vl_pass_nao_circ_provisoes_longo_prazo_exercicio_atual']
        + $_data[20]['vl_pass_nao_circ_dms_obrig_longo_prazo_exercicio_atual']
        + $_data[20]['vl_pass_nao_circ_resultado_deferido_exercicio_atual']
    );

    $_data[20]['total_passivo_nao_circulante_exercicio_anterior'] = (
        $_data[20]['vl_pass_nao_circ_obrig_trab_prev_assist_pagar_longro_prazo_exercicio_anterior']
        + $_data[20]['vl_pass_nao_circ_emp_financ_longo_prazo_exercicio_anterior']
        + $_data[20]['vl_pass_nao_circ_fornec_contas_pagar_longo_prazo_exercicio_anterior']
        + $_data[20]['vl_pass_nao_circ_obrig_fisc_longo_prazo_exercicio_anterior']
        + $_data[20]['vl_pass_nao_circ_provisoes_longo_prazo_exercicio_anterior']
        + $_data[20]['vl_pass_nao_circ_dms_obrig_longo_prazo_exercicio_anterior']
        + $_data[20]['vl_pass_nao_circ_resultado_deferido_exercicio_anterior']
    );

    $_data[20]['total_patrimonio_liquido_exercicio_atual'] = (
        $_data[20]['vl_patri_liq_patr_social_capital_social_exercicio_atual']
        + $_data[20]['vl_patri_liq_adiant_futuro_aumento_capital_exercicio_atual']
        + $_data[20]['vl_patri_liq_reservas_capital_exercicio_atual']
        + $_data[20]['vl_patri_liq_ajuste_aval_patrimonial_exercicio_atual']
        + $_data[20]['vl_patri_liq_reservas_lucros_exercicio_atual']
        + $_data[20]['vl_patri_liq_demais_reservas_exercicio_atual']
        + $_data[20]['vl_patri_liq_resultado_exercicio_exercicio_atual']
        + $_data[20]['vl_patri_liq_acoes_cotas_tesouraria_exercicio_atual']
    );

    $_data[20]['total_patrimonio_liquido_exercicio_anterior'] = (
        $_data[20]['vl_patri_liq_patr_social_capital_social_exercicio_anterior']
        + $_data[20]['vl_patri_liq_adiant_futuro_aumento_capital_exercicio_anterior']
        + $_data[20]['vl_patri_liq_reservas_capital_exercicio_anterior']
        + $_data[20]['vl_patri_liq_ajuste_aval_patrimonial_exercicio_anterior']
        + $_data[20]['vl_patri_liq_reservas_lucros_exercicio_anterior']
        + $_data[20]['vl_patri_liq_demais_reservas_exercicio_anterior']
        + $_data[20]['vl_patri_liq_resultado_exercicio_exercicio_anterior']
        + $_data[20]['vl_patri_liq_acoes_cotas_tesouraria_exercicio_anterior']
    );

    $_data[20]['total_passivo_patrimonio_liquido_exercicio_atual'] = (
        $_data[20]['total_passivo_circulante_exercicio_atual']
        + $_data[20]['total_passivo_nao_circulante_exercicio_atual']
        + $_data[20]['total_patrimonio_liquido_exercicio_atual']
    );
    
    $_data[20]['total_passivo_patrimonio_liquido_exercicio_anterior'] = (
        $_data[20]['total_passivo_circulante_exercicio_anterior']
        + $_data[20]['total_passivo_nao_circulante_exercicio_anterior']
        + $_data[20]['total_patrimonio_liquido_exercicio_anterior']
    );

    $rsRecordSet30 = new RecordSet;
    $obTTCEMGRelatorioBalancoPatrimonial->recuperaDadosBalancoPatrimonial("montaRecuperaDadosBalancoPatrimonial30", $rsRecordSet30);
    $_data[30] = $rsRecordSet30->getObjeto();

    $_data[30]['total_ativo_exercicio_atual'] = (
        $_data[30]['vl_ativo_financeiro_exercicio_atual']
        + $_data[30]['vl_ativo_permanente_exercicio_atual']
    );

    $_data[30]['total_ativo_exercicio_anterior'] = (
        $_data[30]['vl_ativo_financeiro_exercicio_anterior']
        + $_data[30]['vl_ativo_permanente_exercicio_anterior']
    );


    $rsRecordSet40 = new RecordSet;
    $obTTCEMGRelatorioBalancoPatrimonial->recuperaDadosBalancoPatrimonial("montaRecuperaDadosBalancoPatrimonial40", $rsRecordSet40);
    $_data[40] = $rsRecordSet40->getObjeto();


    $_data[40]['total_passivo_exercicio_atual'] = (
        $_data[40]['vl_passivo_financeiro_exercicio_atual']
        + $_data[40]['vl_passivo_permanente_exercicio_atual']
    );

    $_data[40]['total_passivo_exercicio_anterior'] = (
        $_data[40]['vl_passivo_financeiro_exercicio_anterior']
        + $_data[40]['vl_passivo_permanente_exercicio_anterior']
    );

    $_data[40]['saldo_patrimonial_exercicio_atual'] = (
        $_data[30]['total_ativo_exercicio_atual']
        - $_data[40]['total_passivo_exercicio_atual']
    );

    $_data[40]['saldo_patrimonial_exercicio_anterior'] = (
        $_data[30]['total_ativo_exercicio_anterior']
        - $_data[40]['total_passivo_exercicio_anterior']
    );

    $rsRecordSet60 = new RecordSet;
    $obTTCEMGRelatorioBalancoPatrimonial->recuperaDadosBalancoPatrimonial("montaRecuperaDadosBalancoPatrimonial60", $rsRecordSet60);
    $_data[60] = $rsRecordSet60->getObjeto();

    $_data[60]['total_atos_potenciais_ativos_exercicio_atual'] = (
        $_data[60]['vl_ato_poten_ativ_garan_contragaran_recebida_exercicio_atual']
        + $_data[60]['vl_ato_poten_ativ_dir_conven_outros_instru_congeneres_exercicio_atual']
        + $_data[60]['vl_ato_poten_ativ_direitos_contratuais_exercicio_atual']
        + $_data[60]['vl_ato_poten_ativ_outros_atos_potenc_ativo_exercicio_atual']
    );

    $_data[60]['total_atos_potenciais_ativos_exercicio_anterior'] = (
        $_data[60]['vl_ato_poten_ativ_garan_contragaran_recebida_exercicio_anterior']
        + $_data[60]['vl_ato_poten_ativ_dir_conven_outros_instru_congeneres_exercicio_anterior']
        + $_data[60]['vl_ato_poten_ativ_direitos_contratuais_exercicio_anterior']
        + $_data[60]['vl_ato_poten_ativ_outros_atos_potenc_ativo_exercicio_anterior']
    );

    $_data[60]['total_atos_potenciais_passivos_exercicio_atual'] = (
        $_data[60]['vl_ato_poten_pass_garan_contragaran_concedida_exercicio_atual']
        + $_data[60]['vl_ato_poten_pass_obrig_conven_outros_instru_congeneres_exercicio_atual']
        + $_data[60]['vl_ato_poten_pass_obrigacoes_contratuais_exercicio_atual']
        + $_data[60]['vl_ato_poten_pass_outros_atos_potenc_passivo_exercicio_atual']
    );

    $_data[60]['total_atos_potenciais_passivos_exercicio_anterior'] = (
        $_data[60]['vl_ato_poten_pass_garan_contragaran_concedida_exercicio_anterior']
        + $_data[60]['vl_ato_poten_pass_obrig_conven_outros_instru_congeneres_exercicio_anterior']
        + $_data[60]['vl_ato_poten_pass_obrigacoes_contratuais_exercicio_anterior']
        + $_data[60]['vl_ato_poten_pass_outros_atos_potenc_passivo_exercicio_anterior']
    );

    $rsRecordSet71 = new RecordSet;
    $obTTCEMGRelatorioBalancoPatrimonial->recuperaDadosBalancoPatrimonial("montaRecuperaDadosBalancoPatrimonial71", $rsRecordSet71);
    $_data[71] = $rsRecordSet71->getElementos();

    // include_once '../../../../../../gestaoPrestacaoContas/fontes/RPT/TCEMG/MPDF/LHTCEMGRelatorioDCASPBalancoPatrimonial.php';

    $obMPDF->setConteudo($_data);
    $obMPDF->gerarRelatorio();

?>