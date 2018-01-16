<?php

	include_once CAM_GPC_TCEMG_MAPEAMENTO.Sessao::getExercicio()."/TTCEMGBalancoOrcamentario.class.php";

	$rsRecordSetBO10 = new RecordSet();
	// $rsRecordSetBO11 = new RecordSet();
	// $rsRecordSetBO12 = new RecordSet();
	// $rsRecordSetBO13 = new RecordSet();
	// $rsRecordSetBO14 = new RecordSet();
	// $rsRecordSetBO15 = new RecordSet();
	// $rsRecordSetBO16 = new RecordSet();

	$obTTCEMGBalancoOrcamentario = new TTCEMGBalancoOrcamentario();
	$obTTCEMGBalancoOrcamentario->setDado('exercicio',Sessao::getExercicio());
	$obTTCEMGBalancoOrcamentario->setDado('entidades',$stEntidades);
	$obTTCEMGBalancoOrcamentario->setDado('mes', $stMes);
	// $obTTCEMGBalancoOrcamentario->setDado('dataInicial', $stDataInicial);
	// $obTTCEMGBalancoOrcamentario->setDado('dataFinal',   $stDataFinal);

	//Tipo Registro 10
	// $obTTCEMGBalancoOrcamentario->recuperaExportacao($rsRecordSetBO10);

	//Tipo Registro 11
	// $obTTCEMGBalancoOrcamentario->recuperaDetalhamento11($rsRecordSetBO11);

	//Tipo Registro 12
	// $obTTCEMGBalancoOrcamentario->recuperaDetalhamento12($rsRecordSetBO12);

	//Tipo Registro 13
	// $obTTCEMGBalancoOrcamentario->recuperaDetalhamento13($rsRecordSetBO13);

	//Tipo Registro 14
	// $obTTCEMGBalancoOrcamentario->recuperaDetalhamento14($rsRecordSetBO14);

	//Tipo Registro 15
	//$obTTCEMGBalancoOrcamentario->recuperaDetalhamento15($rsRecordSetBO15);

	//Tipo Registro 16
	// $obTTCEMGBalancoOrcamentario->recuperaDetalhamento16($rsRecordSetBO16);

	//Tipo Registro 99
	/*$arRecordSetBO99 = array(
	    '0' => array(
	        'tipo_registro' => '99',
	    )
	);

	$rsRecordSetBO99 = new RecordSet();
	$rsRecordSetBO99->preenche($arRecordSetBO99);*/

	//10 
	if (count($rsRecordSetBO10->getElementos()) > 0) {
	        
	    foreach ($rsRecordSetBO10->getElementos() as $arBO10) {
	        $inCount++;
	        
	        /*$arBO10 = array(
	        	'tipo_registro' => 10,
	        	''
	        );*/
	       
	        $rsBloco = new RecordSet();
	        $rsBloco->preenche(array($arBO10));
	        
	        $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
	        $obExportador->roUltimoArquivo->addBloco($rsBloco);
	        
	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
	        
	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_rec_tributaria");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
	        
	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_rec_contribuicoes");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
	        
	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_rec_patrimonial");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
	        
	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_rec_agropecuaria");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
	        
	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_rec_industrial");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
	        
	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_rec_servicos");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
	        
	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_transf_correntes");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
	        
	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_outras_rec_correntes");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
	        
	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_operacoes_credito");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
	        
	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_alienacao_bens");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
	        
	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_amortizacao_emprestimo");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
	        
	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_transf_capital");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
	        
	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_outras_rec_capital");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
	        
	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_recur_arreca_exercicio_anterior");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
	        
	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_opera_credito_refina_internas_mobiliaria");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
	        
	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_opera_credito_refina_internas_contratual");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
	        
	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_opera_credito_refina_externas_mobiliaria");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
	        
	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_opera_credito_refina_externas_mobiliaria");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
	        
	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_opera_credito_refina_externas_contratual");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
	        
	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_deficit");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
	        
	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_total_quadro_receita");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
	        
		}
	}/* else {
	    $obExportador->roUltimoArquivo->addBloco($rsRecordSetBO99);
	    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
	    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
	    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
	}*/

	$rsRecordSetBO10 = null;
	// $rsRecordSetBO11 = null;
	// $rsRecordSetBO12 = null;
	// $rsRecordSetBO13 = null;
	// $rsRecordSetBO14 = null;
	// $rsRecordSetBO15 = null;
	// $rsRecordSetBO16 = null;
	$rsRecordSetBO99 = null;
	$obTTCEMGBalancoOrcamentario = null;

