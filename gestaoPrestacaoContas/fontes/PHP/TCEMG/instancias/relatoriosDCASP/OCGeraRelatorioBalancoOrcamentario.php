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

    $obTTCEMGRelatorioDCASPBalancoOrcamentario = new TTCEMGRelatorioDCASPBalancoOrcamentario();
    $obTTCEMGRelatorioDCASPBalancoOrcamentario->setDado('exercicio'    , Sessao::getExercicio());
    $obTTCEMGRelatorioDCASPBalancoOrcamentario->setDado('dtInicial'    , $_REQUEST['stDataInicial']);
    $obTTCEMGRelatorioDCASPBalancoOrcamentario->setDado('dtFinal'      , $_REQUEST['stDataFinal']);
    // $obTTCEMGRelatorioDCASPBalancoOrcamentario->setDado('cod_conta'    , implode(',',$_REQUEST['inCodContaSelecionados']));

    $rsRecordSet10 = new RecordSet();
    $obTTCEMGRelatorioDCASPBalancoOrcamentario->montaRecuperaDadosBalancoOrcamentario10($rsRecordSet10);

    $arDados['10'] = $rsRecordSet10->getElementos();
    /*if ($rsRecordSet->getNumLinhas() >= 1){
        /*foreach ($rsRecordSet->getElementos() as $value) {
            switch ($value['nivel']) {
                case 1:
                    $arAux['nivel']             = 1;
                    $arAux['cod_funcao']        = $value['cod_funcao'];
                    $arAux['cod_subfuncao']     = '';
                    $arAux['cod_programa']      = '';
                    $arAux['descricao']         = $value['descricao'];
                    $arAux['valor_pagamento']   = number_format($value['valor_pagamento'],2,',','.');            
                    $arDados["arReceitas"][]    = $arAux;
                break;
                
                case 2:
                    $arAux['nivel']             = 2;
                    $arAux['cod_funcao']        = '';
                    $arAux['cod_subfuncao']     = $value['cod_subfuncao'];
                    $arAux['cod_programa']      = '';
                    $arAux['descricao']         = $value['descricao'];
                    $arAux['valor_pagamento']   = number_format($value['valor_pagamento'],2,',','.');
                    $arDados["arReceitas"][]    = $arAux;
                break;
                
                case 4:
                    $arAux['nivel']             = 4;
                    $arAux['cod_funcao']        = '';
                    $arAux['cod_subfuncao']     = '';
                    $arAux['cod_programa']      = $value['cod_programa'];
                    $arAux['descricao']         = $value['descricao'];
                    $arAux['valor_pagamento']   = number_format($value['valor_pagamento'],2,',','.');
                    $arDados["arReceitas"][]    = $arAux;
                break;
            }
        }
    } else {
        $arDados['sem_registro'] = 'Não existem registros!';
    }*/

    $obMPDF->setConteudo($arDados);

    $obMPDF->gerarRelatorio();

?>