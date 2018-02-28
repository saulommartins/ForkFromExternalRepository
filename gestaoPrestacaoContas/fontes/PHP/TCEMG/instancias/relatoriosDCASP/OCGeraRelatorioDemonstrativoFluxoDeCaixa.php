<?php

ini_set("display_errors", 0);
error_reporting(E_ALL);

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

include_once '../../../../../../gestaoPrestacaoContas/fontes/PHP/TCEMG/classes/mapeamento/TTCMGRelatorioDemostracaoFlucoCaixa.class.php';

include_once ( CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGRelatorioDemostracaoFlucoCaixa.class.php");

include_once CLA_MPDF;


$rsRecordSet = new RecordSet;
$obTTCEMGRelatoriDemonstracaoFluxoCaixa = new TTCEMGRelatorioDemonstracaoFluxoCaixa();
$obTTCEMGRelatoriDemonstracaoFluxoCaixa->setDado('exercicio', Sessao::getExercicio());
$obTTCEMGRelatoriDemonstracaoFluxoCaixa->setDado('entidades'    , implode(',',$_REQUEST['inCodEntidade']));

$stDataInicialExercicioAtual = substr($_REQUEST['stDataInicial'], 0, 6) . Sessao::getExercicio();
$stDataFinalExercicioAtual = substr($_REQUEST['stDataFinal'], 0, 6) . Sessao::getExercicio();
$obTTCEMGRelatoriDemonstracaoFluxoCaixa->setDado('stDataInicialExercicioAtual', $stDataInicialExercicioAtual);
$obTTCEMGRelatoriDemonstracaoFluxoCaixa->setDado('stDataFinalExercicioAtual', $stDataFinalExercicioAtual);

$stDataInicialExercicioAnterior = substr($_REQUEST['stDataInicial'], 0, 6) . (intval(Sessao::getExercicio()) - 1);
$stDataFinalExercicioAnterior = substr($_REQUEST['stDataFinal'], 0, 6) . (intval(Sessao::getExercicio()) - 1);
$obTTCEMGRelatoriDemonstracaoFluxoCaixa->setDado('stDataInicialExercicioAnterior', $stDataInicialExercicioAnterior);
$obTTCEMGRelatoriDemonstracaoFluxoCaixa->setDado('stDataFinalExercicioAnterior', $stDataFinalExercicioAnterior);

$rsRecordSet = new RecordSet;
$obTTCEMGRelatoriDemonstracaoFluxoCaixa->recuperaDadosDemonstracaoFluxoCaixa("sqlDemostracaoFluxoCaixa", $rsRecordSet);
$res = $rsRecordSet->getElementos();

$_data = array();
$_data['exercicio_atual'] = Sessao::read('exercicio');
$_data['exercicio_anterior'] = (Sessao::read('exercicio') - 1);

foreach ($res as $item) {
	if (!isset($_data[$item["exercicio"]])) {
		$_data[$item["exercicio"]] = array();
	}
	$_data[$item["exercicio"]] = $item;
}

$obMPDF = new FrameWorkMPDF(6, 55, 22);

$obMPDF->setNomeRelatorio("DCASP - Demonstrativo do Fluxo de Caixa");

$obMPDF->setConteudo($_data);
$obMPDF->gerarRelatorio();

?>
