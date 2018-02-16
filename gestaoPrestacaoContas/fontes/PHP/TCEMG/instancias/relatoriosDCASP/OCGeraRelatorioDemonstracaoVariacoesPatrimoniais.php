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
    $obTTCEMGRelatorioDemonstracaoVariacoesPatrimoniais->setDado('dtInicial'    , $_REQUEST['stDataInicial']);
    $obTTCEMGRelatorioDemonstracaoVariacoesPatrimoniais->setDado('dtFinal'      , $_REQUEST['stDataFinal']);
    $obTTCEMGRelatorioDemonstracaoVariacoesPatrimoniais->setDado('entidades'    , implode(',',$_REQUEST['inCodEntidade']));

    $rsRecorset10 = new Recordset;
    $obTTCEMGRelatorioDemonstracaoVariacoesPatrimoniais->recuperaDados("montaRecuperaRegistro10" . $tipoRelatorio, $rsRecorset10);
    $_data[10] = $rsRecorset10->getObjeto();

    $rsRecorset20 = new Recordset;
    $obTTCEMGRelatorioDemonstracaoVariacoesPatrimoniais->recuperaDados("montaRecuperaRegistro20" . $tipoRelatorio, $rsRecorset20);
    $_data[20] = $rsRecorset10->getObjeto();

    $_data['exercicio'] = Sessao::read('exercicio');
    
    include_once '../../../../../../gestaoPrestacaoContas/fontes/RPT/TCEMG/MPDF/LHTCEMGRelatorioDCASPDemonstracaoVariacoesPatrimoniais'.$tipoRelatorio.'.php';

    // $obMPDF->setConteudo($_data);
    // $obMPDF->gerarRelatorio();