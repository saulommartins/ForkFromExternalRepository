<?php

	ini_set("display_errors", 1);
	error_reporting(E_ALL);

	include_once CAM_GPC_TCEMG_MAPEAMENTO.Sessao::getExercicio()."/TTCEMGArquivoMensalIDE.class.php";

	$rsRecordSetIDE = new RecordSet();
	$obTTCEMGArquivoMensalIDE = new TTCEMGArquivoMensalIDE();
	$obTTCEMGArquivoMensalIDE->setDado('exercicio',Sessao::getExercicio());
	$obTTCEMGArquivoMensalIDE->setDado('entidades',$stEntidades);
	$obTTCEMGArquivoMensalIDE->setDado('mes', $inMes);

	$tipoDemonstracaoContabil = 1;
	$entidadesExploded = explode(",", $stEntidades);
	
	if (count($entidadesExploded) > 1) {
		$tipoDemonstracaoContabil = 2;
	}

	$obTTCEMGArquivoMensalIDE->recuperaDadosExportacao($rsRecordSetIDE);

	foreach ((array) $rsRecordSetIDE->arElementos as $key => $blocoIDE) {

		$blocoIDE['tipo_demonstracao'] = $tipoDemonstracaoContabil;

	    $rsBloco = new RecordSet();
		$rsBloco->preenche(array($blocoIDE));

		$obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
		$obExportador->roUltimoArquivo->addBloco($rsBloco);
	    
	    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_municipio");
	    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
	    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(5);
	    
	    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cnpj_orgao");
	    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
	    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);
	    
	    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_orgao");
	    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
	    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
	    
	    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_orgao");
	    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
	    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

	    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_demonstracao");
	    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
	    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
	    
	    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("exercicio_referencia");
	    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
	    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
	    
	    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("mes_referencia");
	    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
	    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
	    
	    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("data_geracao");
	    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
	    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
	    
	    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_remessa");
	    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
	    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(20);
	}

	$rsRecordSetIDE = null;
	$obTTCEMGArquivoMensalIDE = null;

?>