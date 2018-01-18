<?php

	ini_set("display_errors", 1);
    error_reporting(E_ALL);

	include_once CAM_GPC_TCEMG_MAPEAMENTO.Sessao::getExercicio()."/TTCEMGBalancoOrcamentario.class.php";

	$rsRecordSetBO10 = new RecordSet();
	$rsRecordSetBO20 = new RecordSet();
	$rsRecordSetBO30 = new RecordSet();
	$rsRecordSetBO40 = new RecordSet();
	$rsRecordSetBO50 = new RecordSet();

	$obTTCEMGBalancoOrcamentario = new TTCEMGBalancoOrcamentario();
	$obTTCEMGBalancoOrcamentario->setDado('exercicio', Sessao::getExercicio());
	$obTTCEMGBalancoOrcamentario->setDado('entidades', $stEntidades);

	//Tipo Registro 10
	$obTTCEMGBalancoOrcamentario->recuperaExportacao("montaRecuperaExportacao10", $rsRecordSetBO10);

	//Tipo Registro 20
	$obTTCEMGBalancoOrcamentario->recuperaExportacao("montaRecuperaExportacao20", $rsRecordSetBO20);

	//Tipo Registro 30
	$obTTCEMGBalancoOrcamentario->recuperaExportacao("montaRecuperaExportacao30", $rsRecordSetBO30);

	//Tipo Registro 40
	$obTTCEMGBalancoOrcamentario->recuperaExportacao("montaRecuperaExportacao40", $rsRecordSetBO40);

	//Tipo Registro 50
	$obTTCEMGBalancoOrcamentario->recuperaExportacao("montaRecuperaExportacao50", $rsRecordSetBO50);


	//10 
	if (count($rsRecordSetBO10->getElementos()) > 0) {
	        
	    foreach ($rsRecordSetBO10->getElementos() as $arBO10) {
	        $retornaVazio = false;
	       
	        $arBO10['fase'] = 1;

	        if ($arBO10['vl_total_quadro_receita'] > $arBO10['vl_orcado']) {
	        	$arBO10['fase'] = 2;
	        }

	        $rsTotalDasDespesas = new RecordSet();
	        $obTTCEMGBalancoOrcamentario->recuperaExportacao("montaRecuperaTotalDespesas", $rsTotalDasDespesas);
			$vlTotalDespesas = $rsTotalDasDespesas->getObjeto();

	        if ($vlTotalDespesas['valor'] > $arBO10['vl_total_quadro_receita']) {
	        	$arBO10['vl_deficit'] = $arBO10['vl_total_quadro_receita'] - $vlTotalDespesas['valor'];
	        }

	        $rsBloco = new RecordSet();
	        $rsBloco->preenche(array($arBO10));
	        
	        $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
	        $obExportador->roUltimoArquivo->addBloco($rsBloco);

	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
	        
	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("fase");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
	        
	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_rec_tributaria");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
	        
	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_rec_contribuicoes");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
	        
	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_rec_patrimonial");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
	        
	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_rec_agropecuaria");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
	        
	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_rec_industrial");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
	        
	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_rec_servicos");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
	        
	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_transf_correntes");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
	        
	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_outras_rec_correntes");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
	        
	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_operacoes_credito");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
	        
	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_alienacao_bens");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
	        
	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_amortizacao_emprestimo");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
	        
	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_transf_capital");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
	        
	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_outras_rec_capital");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
	        
	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_recur_arreca_exercicio_anterior");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
	        
	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_opera_credito_refina_internas_mobiliaria");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
	        
	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_opera_credito_refina_internas_contratual");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
	        
	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_opera_credito_refina_externas_mobiliaria");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
	        
	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_opera_credito_refina_externas_mobiliaria");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
	        
	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_opera_credito_refina_externas_contratual");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
	        
	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_deficit");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
	        
	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_total_quadro_receita");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
	        
		}
	}

	//20 
	if (count($rsRecordSetBO20->getElementos()) > 0) {
	        
	    foreach ($rsRecordSetBO20->getElementos() as $arBO20) {
	        
	        $rsBloco = new RecordSet();
	        $rsBloco->preenche(array($arBO20));
	        
	        $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
	        $obExportador->roUltimoArquivo->addBloco($rsBloco);
	        
	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
	        
	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("fase");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
	        
	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_saldo_exercicio_anterior_superavit_finan");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
	        
	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_saldo_exercicio_anterior_reabertura_credito_adicio");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
	        
	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_total_saldo_exercicios_anteriores");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
		}
	}
	
	//30 
	if (count($rsRecordSetBO30->getElementos()) > 0) {
	        
	    foreach ($rsRecordSetBO30->getElementos() as $arBO30) {
	        
	        $rsBloco = new RecordSet();
	        $rsBloco->preenche(array($arBO30));
	        
	        $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
	        $obExportador->roUltimoArquivo->addBloco($rsBloco);
	        
	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
	        
	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("fase");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
	        
	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_pessoal_encar_social");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
	        
	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_juros_encar_dividas");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
	        
	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_outras_desp_correntes");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
	        
	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_investimentos");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
	        
	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_inver_financeira");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
	        
	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_amortiza_divida");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
	        
	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_reserva_contingencia");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
	        
	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_reserva_rpps");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
	        
	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_amortiza_divida_inter_mobiliaria");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
	        
	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_amortiza_outras_dividas_internas");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
	        
	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_amortiza_divida_exter_mobiliaria");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
	        
	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_amortiza_outras_dividas_externas");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
	        
	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_superavit");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
	        
	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_total_quadro_despesa");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
		}
	}


	//40 
	if (count($rsRecordSetBO40->getElementos()) > 0) {
	        
	    foreach ($rsRecordSetBO40->getElementos() as $arBO40) {

	        $rsBloco = new RecordSet();
	        $rsBloco->preenche(array($arBO40));
	        
	        $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
	        $obExportador->roUltimoArquivo->addBloco($rsBloco);
	        
	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
	        
	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("fase");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
	        
	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_rsp_nao_proces_pessoal_encar_sociais");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
	        
	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_rsp_nao_proces_juros_encar_dividas");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
	        
	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_rsp_nao_proces_outras_desp_correntes");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
	        
	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_rsp_nao_proces_investimentos");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
	        
	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_rsp_nao_proces_inver_financeira");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
	        
	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_rsp_nao_proces_amortiza_divida");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
	        
	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_total_execu_rsp_nao_processado");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
		}
	}

	//50 
	if (count($rsRecordSetBO50->getElementos()) > 0) {
	        
	    foreach ($rsRecordSetBO50->getElementos() as $arBO50) {

	        $rsBloco = new RecordSet();
	        $rsBloco->preenche(array($arBO50));
	        
	        $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
	        $obExportador->roUltimoArquivo->addBloco($rsBloco);
	        
	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
	        
	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("fase");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
	        
	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_rsp_proces_liq_pessoal_encar_sociais");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
	        
	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_rsp_proces_liq_juros_encar_dividas");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
	        
	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_rsp_proces_liq_outras_desp_correntes");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
	        
	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_rsp_proces_liq_investimentos");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
	        
	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_rsp_proces_liq_inver_financeira");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
	        
	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_rsp_proces_liq_amortiza_divida");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
	        
	        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_total_execu_rsp_proce_nao_proce_liquidado");
	        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
	        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
		}
	}


	$rsRecordSetBO10 = null;
	$rsRecordSetBO20 = null;
	$rsRecordSetBO30 = null;
	$rsRecordSetBO40 = null;
	$rsRecordSetBO50 = null;
	
	$obTTCEMGBalancoOrcamentario = null;