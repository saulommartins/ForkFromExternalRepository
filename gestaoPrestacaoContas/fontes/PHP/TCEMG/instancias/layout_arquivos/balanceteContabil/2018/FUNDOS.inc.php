<?php

ini_set("display_errors", 1);
error_reporting(E_ALL);

include_once CAM_GPC_TCEMG_MAPEAMENTO.Sessao::getExercicio() . "/TTCEMGArquivoFundos.class.php";

$rsRecuperaFundos10 = new RecordSet();
$rsRecuperaFundos11 = new RecordSet();
$rsRecuperaFundos20 = new RecordSet();

$obTTCEMGArquivoFundos = new TTCEMGArquivoFundos();
$obTTCEMGArquivoFundos->setDado('exercicio', Sessao::getExercicio());
$obTTCEMGArquivoFundos->setDado('entidade', $stEntidades);
$obTTCEMGArquivoFundos->setDado('mes', $arFiltro['inMes']);

// REGISTRO 10
$obTTCEMGArquivoFundos->recuperaRegistro("10", $rsRecuperaFundos10);

// REGISTRO 11
$obTTCEMGArquivoFundos->recuperaRegistro("11", $rsRecuperaFundos11);

// REGISTRO 20
$obTTCEMGArquivoFundos->recuperaRegistro("20", $rsRecuperaFundos20);

$exibe99 = true;

foreach($rsRecuperaFundos10->getElementos() as $key => $arFundo) {
    $exibe99 = false;

    $rsBloco10 = new RecordSet();
    $rsBloco10->preenche(array($arFundo));

    $obExportador->roUltimoArquivo->addBloco($rsBloco10);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_fundo");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(8);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cnpj");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("descricao");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(120);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("contabilidade_centralizada");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("plano");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
}


foreach($rsRecuperaFundos11->getElementos() as $key => $arFundo) {

    $exibe99 = false;
    $rsBloco11 = new RecordSet();
    $rsBloco11->preenche(array($arFundo));

    $obExportador->roUltimoArquivo->addBloco($rsBloco11);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_fundo");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(8);

    if ($arFundo['cod_unidade_sub'] != null) {
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_unidade_sub");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
    } else {
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_unidade");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(5);
    }

}



foreach($rsRecuperaFundos20->getElementos() as $key => $arFundo) {

    $exibe99 = false;
    $rsBloco20 = new RecordSet();
    $rsBloco20->preenche(array($arFundo));

    $obExportador->roUltimoArquivo->addBloco($rsBloco20);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_fundo");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(8);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("data_extincao");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
}

if ($exibe99) {
    $arRecordSet99 = array(
        '0' => array(
            'tipo_registro' => '99',
        )
    );

    $rsRegistro99 = new RecordSet();
    $rsRegistro99->preenche($arRecordSet99);

    $obExportador->roUltimoArquivo->addBloco($rsRegistro99);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
}

$rsRecuperaFundos10 = null;
$rsRecuperaFundos11 = null;
$rsRecuperaFundos20 = null;