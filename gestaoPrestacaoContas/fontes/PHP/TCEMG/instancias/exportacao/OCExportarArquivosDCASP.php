<?php

	set_time_limit(0);
	include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
	include_once CLA_EXPORTADOR;
	include_once CAM_GPC_TCEMG_MAPEAMENTO.'TTCEMGConfigurarIDE.class.php';

	//Define o nome dos arquivos PHP
	$stPrograma = "ExportarDCASP" ;
	$pgFilt     = "FL".$stPrograma.".php";
	$pgForm     = "FM".$stPrograma.".php";
	$pgProc     = "PR".$stPrograma.".php";
	$pgOcul     = "OC".$stPrograma.".php";

	SistemaLegado::BloqueiaFrames();

	$obExportador = new Exportador();

	$arFiltro = Sessao::read('filtroRelatorio');
	$stEntidades = implode(",",$arFiltro['inCodEntidade']);
	$inMes = $arFiltro['inMes'];

	SistemaLegado::retornaInicialFinalMesesPeriodicidade($arDatasInicialFinal,'',$inMes,Sessao::getExercicio());

	foreach($arFiltro['arArquivosSelecionados'] AS $stArquivo) {
	    $obExportador->addArquivo($stArquivo);
	    $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
	    $arNomArquivo = explode('.',$stArquivo);
	    include_once(CAM_GPC_TCEMG_INSTANCIAS."layout_arquivos/DCASP/".Sessao::getExercicio()."/".$arNomArquivo[0].".inc.php");
	}

	if ( $arFiltro['stTipoExport'] == 'compactados') {
	    $obTTCEMGConfigurarIDE = new TTCEMGConfigurarIDE();
	    $obTTCEMGConfigurarIDE->setDado('exercicio', Sessao::getExercicio());
	    $obTTCEMGConfigurarIDE->setDado('entidades', $stEntidades);
	    $obTTCEMGConfigurarIDE->recuperaDadosExportacao($rsRecordSet);
	    
	    if ($rsRecordSet->inNumLinhas > 0) {
	        $inCodMunicipio = str_pad($rsRecordSet->getCampo('codmunicipio'), 5, '0', STR_PAD_LEFT);
	        $inCodOrgao = str_pad($rsRecordSet->getCampo('codorgao'), 2, '0', STR_PAD_LEFT);
	        $inMes = str_pad($inMes, 2, '0', STR_PAD_LEFT);
	    } else {
	        SistemaLegado::alertaAviso($pgFilt."?".Sessao::getId()."&stAcao=$stAcao", "É necessário configurar a IDE para gerar um arquivo compactado.", "", "aviso", Sessao::getId(), "../");
	        die;
	    }
	    $obExportador->setNomeArquivoZip('BALANCETE_'.$inCodMunicipio.'_'.$inCodOrgao.'_'.$inMes.'_'.Sessao::getExercicio().'.zip');
	}

	$obExportador->show();
	SistemaLegado::LiberaFrames();

?>
