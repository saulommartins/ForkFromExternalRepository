<?php

    /* includes de sistema */
    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

    /* includes de regra de negócio */
    include_once CAM_FW_PDF."RRelatorio.class.php";
    include_once CAM_GF_TES_MAPEAMENTO.'TTesourariaReciboExtra.class.php';
    
    // include_once CAM_GF_EMP_NEGOCIO."REmpenhoRelatorioOrdemPagamento.class.php";
    
    // $obRRelatorio->executaFrameOculto("OCGeraRelatorioOrdemPagamento.php");
    $arFiltro = Sessao::read('filtroRelatorio');
    $rsRecordSet = new RecordSet;

    $obTReciboExtra = new TTesourariaReciboExtra;
    $obTReciboExtra->recuperaDadosReciboEmissao($rsRecordSet, "", $arFiltro['stExercicioEmpenho'], 'D', $arFiltro['inCodigoOrdem']);

    $arDados = $rsRecordSet->getObjeto();
    $arDados['txtValor'] = str_replace('.',',', $arDados['txtValor']);
    Sessao::write('post', $arDados);

    $obRRelatorio = new RRelatorio;
    $obRRelatorio->executaFrameOculto(CAM_GF_TES_INSTANCIAS . 'reciboDespesaExtra/OCRelatorioReciboDespesaExtra.php');
