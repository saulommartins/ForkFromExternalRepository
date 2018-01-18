<?php

	include_once CAM_GPC_TCEMG_MAPEAMENTO.Sessao::getExercicio()."/TTCEMGRPSD.class.php";

	$rsRecordSetRPSD10 = new RecordSet();
	$rsRecordSetRPSD11 = new RecordSet();

	$obTTCEMGRPSD = new TTCEMGRPSD();
	$obTTCEMGRPSD->setDado('exercicio', Sessao::getExercicio());
	$obTTCEMGRPSD->setDado('entidades', $stEntidades);

	//Tipo Registro 10
	$obTTCEMGRPSD->recuperaExportacao("montaRecuperaExportacao10", $rsRecordSetRPSD10);

	//10 
	if (count($rsRecordSetRPSD10->getElementos()) > 0) {
	        
	    foreach ($rsRecordSetRPSD10->getElementos() as $arRPSD10) {
	        $retornaVazio = false;

	        $rsBloco = new RecordSet();
	        $rsBloco->preenche(array($arRPSD10));
	        
	        $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
	        $obExportador->roUltimoArquivo->addBloco($rsBloco);

	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
	        
	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_reduzido_rsp");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(15);
	        
	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_entidade");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
	        
	        //Código da unidade do empenho
	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_unidade");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(5);
	        
	        //Codigo da unidade de origem - sem alteração
	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_unidade");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(5);
	        
	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_empenho");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(22);
	        
	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("exercicio_empenho");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(4);
	        
	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("data_empenho");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(8);
	        
	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_pagamento");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
	        
	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_pago");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);


			//Tipo Registro 11
			$obTTCEMGRPSD->recuperaExportacao("montaRecuperaExportacao11", $rsRecordSetRPSD11, " AND restos_a_pagar.cod_empenho = " . $arRPSD10['cod_empenho']);

			if (count($rsRecordSetRPSD11->getElementos()) > 0) {
			    foreach ($rsRecordSetRPSD11->getElementos() as $arRPSD11) {
			        $rsBloco11 = new RecordSet();
			        $rsBloco11->preenche(array($arRPSD11));
			        
			        $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
			        $obExportador->roUltimoArquivo->addBloco($rsBloco11);
			        
			        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
			        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
			        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
			        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

			        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_reduzido_rsp");
			        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
			        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
			        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(15);

			        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_recurso");
			        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
			        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
			        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);

			        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_liquidacao_paga");
			        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
			        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
			        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
			    }
		    }
	    }
	}

	if ($retornaVazio) {
		
		$rsRecordSetRPSD99 = new RecordSet();
		$arRecordSetRPSD99 = array(
		    '0' => array(
		        'tipo_registro' => '99',
		    )
		);

		$rsRecordSetRPSD99 = new RecordSet();
		$rsRecordSetRPSD99->preenche($arRecordSetRPSD99);

	    $obExportador->roUltimoArquivo->addBloco($rsRecordSetRPSD99);
	    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
	    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
	    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
	}


	$rsRecordSetRPSD10 = null;
	$rsRecordSetRPSD11 = null;
	
	$obTTCEMGRPSD = null;