<?php
/**
    * Página de Formulario de Seleção de Impressora para Relatorio
    * Data de Criação   : 05/05/2005

    * @author Analista: Valtair
    * @author Desenvolvedor: Carlos Adriano
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php" );
include_once( CAM_GF_CONT_NEGOCIO."RContabilidadeRelatorioAnexo15.class.php" );
include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php" );

$preview = new PreviewBirt(2,9,17);
$preview->setVersaoBirt( '2.5.0' );

if ($_REQUEST['inCodEntidade'] != "") {
    $stEntidades = "";
    $inCount = 0;
    foreach ($_REQUEST['inCodEntidade'] as $key => $valor) {
        $stEntidades .= $valor.",";
        $inCount++;
    }

    if ($stEntidades != "") {
        $stEntidades = substr( $stEntidades, 0, strlen($stEntidades) - 1 ) . "";
    }
}

$obTOrcamentoEntidade = new TOrcamentoEntidade;
$obTOrcamentoEntidade->setDado( 'exercicio'   , Sessao::getExercicio()  );
$obTOrcamentoEntidade->recuperaEntidades( $rsEntidade, "and e.cod_entidade in (".implode(',',$_REQUEST['inCodEntidade']).")" );

if (count($_REQUEST['inCodEntidade']) == 1 ) {
    $preview->addParametro( 'nom_entidade', $rsEntidade->getCampo('nom_cgm') );
    if (preg_match( "/prefeitura.*/i", $rsEntidade->getCampo('nom_cgm')) || $boConfirmaFundo > 0) {
        $preview->addParametro( 'poder' , 'Executivo' );
    } else {
        $preview->addParametro( 'poder' , 'Legislativo' );
    }
} else {
    while (!$rsEntidade->eof()) {
        if (preg_match( "/prefeitura.*/i", $rsEntidade->getCampo('nom_cgm')) ) {
            $preview->addParametro( 'nom_entidade', $rsEntidade->getCampo('nom_cgm') );
            $preview->addParametro( 'poder' , 'Executivo' );
            break;
        }
        $rsEntidade->proximo();
    }
}

//verifica o periodo se for por mes seta o nome do mes OU para o ano todo
if ( isset($_POST['stMes']) ) {
    switch ($_POST['stMes']) {
    case 1:
       $stPeriodo =  'JANEIRO';
        break;
    case 2:
        $stPeriodo = 'FEVEREIRO';
        break;
    case 3:
        $stPeriodo = 'MARÇO';
        break;
    case 4:
       $stPeriodo =  'ABRIL';
        break;
    case 5:
       $stPeriodo =  'MAIO';
        break;
    case 6:
      $stPeriodo =   'JUNHO';
        break;
    case 7:
       $stPeriodo =  'JULHO';
        break;
    case 8:
       $stPeriodo =  'AGOSTO';
        break;
    case 9:
      $stPeriodo =   'SETEMBRO';
        break;
    case 10:
      $stPeriodo =   'OUTUBRO';
        break;
    case 11:
       $stPeriodo =  'NOVEMBRO';
        break;
    case 12:
       $stPeriodo =  'DEZEMBRO';
        break;
    }
} else {
    list($dia,$mes,$ano) = explode('/', $_POST['stDataFinal'] );
    if ( ($_POST['stDataInicial'] == "01/01/".$ano) && ($_POST['stDataFinal'] == "31/12/".$ano) ) {
        $stPeriodo = "JANEIRO A DEZEMBRO";
    }
}

$preview->addParametro('exercicio'      , Sessao::getExercicio());
$preview->addParametro("periodo"        , $stPeriodo );
$preview->addParametro('cod_entidade'   , implode(',', $_REQUEST['inCodEntidade']));
$preview->addParametro('dt_inicial'     , $_REQUEST['stDataInicial']);
$preview->addParametro('dt_final'       , $_REQUEST['stDataFinal']);
$preview->addParametro('cod_acao'       , Sessao::read('acao'));
$preview->addParametro('data_inicial_nota',implode('-',array_reverse(explode('/', $_REQUEST['stDataInicial']))));
$preview->addParametro('data_final_nota'  ,implode('-',array_reverse(explode('/', $_REQUEST['stDataFinal']))));


$preview->setNomeArquivo($stNomeArquivo);
$preview->setNomeRelatorio($stNomeArquivo);

$arAssinaturas = Sessao::read('assinaturas');

$preview->addAssinaturas(Sessao::read('assinaturas'));

$preview->preview();
