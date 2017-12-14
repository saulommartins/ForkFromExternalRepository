<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
?>
<?php
/**
    * Página de Formulario de Vinculação da Receita com Característica Peculiar - TCE-RS
    * Data de Criação   : 09/01/2008

    * @author Analista: Tonismar Régis Bernardo
    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @ignore

    $Id:$

    * Casos de uso: uc-06.06.19
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( TTRS."TTCERSCaracPeculiarReceita.class.php" );
include_once( TTRS."TTCERSReceitaCaracPeculiarReceita.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "VincularCaracteristicaPeculiar";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$obForm = new Form;
$obForm->setAction                  ( $pgProc );
$obForm->setTarget                  ( "oculto" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

//$obROrcamentoReceita = new ROrcamentoReceita;
$obTTCERSCaracPeculiarReceita = new TTCERSCaracPeculiarReceita;
$obTTCERSReceitaCaracPeculiarReceita = new TTCERSReceitaCaracPeculiarReceita;

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

$arLink = Sessao::read('link');
//Monta sessao com os valores do filtro
if ($_GET["pg"] and  $_GET["pos"]) {
    $arLink["pg"]  = $_GET["pg"];
    $arLink["pos"] = $_GET["pos"];
} elseif ( is_array($arLink) ) {
    $_GET = $arLink;
    $_REQUEST = $arLink;
} else {
    foreach ($_REQUEST as $key => $valor) {
        $arLink[$key] = $valor;
    }
}
Sessao::write('link', $arLink);

if ($_REQUEST['stCodReceita']) {
    $arClassificacao = explode( "." , $_REQUEST['stCodReceita'] );
    $inCount         = count( $arClassificacao );
    //busca o codigo da Classificacao que sera inserido
    //o codigo sera o ultimo do array que nao possua valor igual a zero
    for ($inPosicao = $inCount; $inPosicao >= 0; $inPosicao--) {
        if ($arClassificacao[$inPosicao] != 0) {
            break;
        }
    }
    //remonta a Classificacao de Receita, colocanco '0' na ultima casa com valor
    for ($inPosicaoNew = 0; $inPosicaoNew <= $inPosicao; $inPosicaoNew++) {
            $stVerificaClassReceita .= $arClassificacao[$inPosicaoNew].".";
    }
    $stVerificaClassReceita = substr( $stVerificaClassReceita, 0, strlen( $stVerificaClassReceita ) - 1 );

    //$obROrcamentoReceita->obROrcamentoClassificacaoReceita->setMascClassificacao( $stVerificaClassReceita );
    $stFiltro .= " AND CR.mascara_classificacao like '". $stVerificaClassReceita ."%'";
    $stLink .= '&stCodReceita='.$_REQUEST['stCodReceita'];
}

if ($_REQUEST['inCodRecurso']) {
    $stFiltro .= " AND R.cod_recurso =".$_REQUEST['inCodRecurso'];
    $stLink .= '&inCodRecurso=' .  $_REQUEST['inCodRecurso'];
}

if ($_REQUEST['inCodUso'] && $_REQUEST['inCodDestinacao'] && $_REQUEST['inCodEspecificacao']) {
    $stFiltro .= " AND R.masc_recurso_red like '".$_REQUEST['inCodUso'].".".$_REQUEST['inCodDestinacao'].".".$_REQUEST['inCodEspecificacao']."%' ";
    $stLink .= "&inCodUso=".$_REQUEST['inCodUso'];
    $stLink .= "&inCodDestinacao=".$_REQUEST['inCodDestinacao'];
    $stLink .= "&inCodEspecificaca=".$_REQUEST['inCodEspecificacao'];
}

if ($_REQUEST['inCodDetalhamento']) {
    $stFiltro .= " AND R.cod_detalhamento = ".$_REQUEST['inCodDetalhamento'];
    $stLink .= "&inCodDetalhamento=".$_REQUEST['inCodDetalhamento'];
}

if ($_REQUEST['inCodReceitaInicial']) {
    $stFiltro .= " AND o.cod_receita >=".$_REQUEST['inCodReceitaInicial'];
    $stLink .= '&inCodReceitaInicial='.$_REQUEST['inCodReceitaInicial'];
}
if ($_REQUEST['inCodReceitaFinal']) {
    $stFiltro .= " AND o.cod_receita <=".$_REQUEST['inCodReceitaFinal'];
    $stLink .= '&inCodReceitaFinal='.  $_REQUEST['inCodReceitaFinal'];
}

if ($_REQUEST['stDescricao']) {
    //$obROrcamentoReceita->obROrcamentoClassificacaoReceita->setDescricao( $_REQUEST['stDescricao'] );
    $stFiltro .= " AND lower(CR.descricao) like lower('%". $_REQUEST['stDescricao'] ."%')";
    $stLink .= '&stDescricao='.$_REQUEST['stDescricao'];
}

$stFiltro .= " AND o.exercicio = '".Sessao::getExercicio()."' ";

$stLink .= "&stAcao=".$stAcao;

//$obROrcamentoReceita->listar( $rsLista, "O.cod_conta,O.cod_receita" );

//$stFiltro .= " AND CR.mascara_classificacao not like '9.%' "; // diferente de dedutoras
$stFiltro .= " AND CR.mascara_classificacao like '9.%' ";

$obTTCERSReceitaCaracPeculiarReceita->setDado('exercicio', Sessao::getExercicio() );
$obTTCERSReceitaCaracPeculiarReceita->recuperaRelacionamento( $rsLista, $stFiltro, " O.cod_receita, CR.mascara_classificacao ");

Sessao::write('receitas', $rsLista->arElementos);
$obLista = new Lista;
$obLista->setTitulo( "Receitas");
$obLista->setRecordSet( $rsLista );

$obLista->addCabecalho("&nbsp;", 5);
$obLista->addCabecalho("Receita", 55);
$obLista->addCabecalho("Característica", 35);

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "[cod_receita] - [descricao]" );
$obLista->commitDado();

$obTTCERSCaracPeculiarReceita->recuperaTodos($rsCaracteristica);

$obCmbCaracteristica = new Select();
$obCmbCaracteristica->setName         ( "inCodCaracteristica_[cod_receita]_" );
$obCmbCaracteristica->setId           ( "inCodCaracteristica"         );
$obCmbCaracteristica->setCampoID      ( "cod_caracteristica"          );
$obCmbCaracteristica->setCampoDesc    ( "descricao"              );
$obCmbCaracteristica->preencheCombo   ( $rsCaracteristica  );
$obCmbCaracteristica->setValue        ( "[cod_caracteristica]");

$obLista->addDadoComponente( $obCmbCaracteristica );
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "[cod_caracteristica]" );
$obLista->commitDado();

$obLista->montaHTML();

$obSpnElementos = new Span();
$obSpnElementos->setId( 'spnElementos' );
$obSpnElementos->setValue( $obLista->getHTML() );

//DEFINICAO DOS COMPONENTES
$obFormulario = new Formulario();

$obFormulario->addForm              ( $obForm );
$obFormulario->addHidden            ( $obHdnAcao );
$obFormulario->addHidden            ( $obHdnCtrl );
$obFormulario->addSpan              ( $obSpnElementos );
$obFormulario->Cancelar($pgFilt."?".Sessao::getId());
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
