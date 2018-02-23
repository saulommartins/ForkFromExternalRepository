<?php

    ini_set("display_errors", 1);
    error_reporting(E_ALL);

    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

    include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php" );
    include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoUsuario.class.php" );
    include_once ( CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGRelatorioDemonstracaoVariacoesPatrimoniais.class.php");

    include_once CLA_MPDF;

    $codRelatorio = 20;
    $tipoRelatorio = "Analitico";
    
    if ($request->get('stTipoRelatorio') == "Sintetico") {
        $codRelatorio = 21;
        $tipoRelatorio = "Sintetico";
    }

    $obMPDF = new FrameWorkMPDF(6, 55, $codRelatorio);
    $obMPDF->setCodEntidades($request->get('inCodEntidade'));
    $obMPDF->setDataInicio($request->get("stDataInicial"));
    $obMPDF->setDataFinal($request->get("stDataFinal"));
    $obMPDF->use_kwt = true; 

    $obMPDF->setNomeRelatorio("DCASP - Demonstração das Variações Patrimoniais");

    $obTTCEMGRelatorioDemonstracaoVariacoesPatrimoniais = new TTCEMGRelatorioDemonstracaoVariacoesPatrimoniais();
    $obTTCEMGRelatorioDemonstracaoVariacoesPatrimoniais->setDado('exercicio'    , Sessao::getExercicio());
    $obTTCEMGRelatorioDemonstracaoVariacoesPatrimoniais->setDado('entidades'    , implode(',',$_REQUEST['inCodEntidade']));


    $stDataInicialExercicioAtual = substr($_REQUEST['stDataInicial'], 0, 6) . Sessao::getExercicio();
    $stDataFinalExercicioAtual = substr($_REQUEST['stDataFinal'], 0, 6) . Sessao::getExercicio();
    $obTTCEMGRelatorioDemonstracaoVariacoesPatrimoniais->setDado('stDataInicialExercicioAtual', $stDataInicialExercicioAtual);
    $obTTCEMGRelatorioDemonstracaoVariacoesPatrimoniais->setDado('stDataFinalExercicioAtual', $stDataFinalExercicioAtual);

    $stDataInicialExercicioAnterior = substr($_REQUEST['stDataInicial'], 0, 6) . (intval(Sessao::getExercicio()) - 1);
    $stDataFinalExercicioAnterior = substr($_REQUEST['stDataFinal'], 0, 6) . (intval(Sessao::getExercicio()) - 1);
    $obTTCEMGRelatorioDemonstracaoVariacoesPatrimoniais->setDado('stDataInicialExercicioAnterior', $stDataInicialExercicioAnterior);
    $obTTCEMGRelatorioDemonstracaoVariacoesPatrimoniais->setDado('stDataFinalExercicioAnterior', $stDataFinalExercicioAnterior);

    $rsRecorset10 = new Recordset;
    $obTTCEMGRelatorioDemonstracaoVariacoesPatrimoniais->recuperaDados("montaRecuperaRegistro10" . $tipoRelatorio, $rsRecorset10);
    $_data[10] = $rsRecorset10->getObjeto();

    $rsRecorset20 = new Recordset;
    $obTTCEMGRelatorioDemonstracaoVariacoesPatrimoniais->recuperaDados("montaRecuperaRegistro20" . $tipoRelatorio, $rsRecorset20);
    $_data[20] = $rsRecorset10->getObjeto();

    $_data['exercicio'] = Sessao::read('exercicio');

    $_data['vl_resultado_exercicio_atual'] = ($_data[10]['vl_total_vp_aumentativas_exercicio_atual'] - $_data[20]['vl_total_vp_diminutivas_exercicio_atual']);
    $_data['vl_resultado_exercicio_anterior'] = ($_data[10]['vl_total_vp_aumentativas_exercicio_anterior'] - $_data[20]['vl_total_vp_diminutivas_exercicio_anterior']);
    
    // include_once '../../../../../../gestaoPrestacaoContas/fontes/RPT/TCEMG/MPDF/LHTCEMGRelatorioDCASPDemonstracaoVariacoesPatrimoniais'.$tipoRelatorio.'.php';

    $obMPDF->setConteudo($_data);
    $obMPDF->gerarRelatorio();