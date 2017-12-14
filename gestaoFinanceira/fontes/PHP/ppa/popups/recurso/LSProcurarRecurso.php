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
* Página de Listagem de Procura de Recurso
* Data de Criação   : 21/10/2008

* @author Analista: Heleno Santos
* @author Desenvolvedor: Fellipe Esteves dos Santos
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GF_ORC_MAPEAMENTO."TOrcamentoRecursoDireto.class.php");
include_once(CAM_GF_ORC_MAPEAMENTO."TOrcamentoRecursoDestinacao.class.php");

//Define o nome dos arquivos PHP
$stPrograma	= "ProcurarRecurso";
$pgFilt 	= "FL".$stPrograma.".php";
$pgList 	= "LS".$stPrograma.".php";
$pgOcul 	= "OC".$stPrograma.".php";
$pgJS   	= "JS".$stPrograma.".php";

include_once($pgJS);

$stCaminho  = CAM_GF_PPA_INSTANCIAS."recurso/";

Sessao::write('arFiltro', $arFiltro);

if ($_REQUEST['boUtilizaDestinacao'] == true) {
    $stFiltro = " WHERE recurso.exercicio = '".Sessao::getExercicio()."'";
}

if ($_REQUEST['boUtilizaDestinacao'] == false) {
    $stFiltro = " WHERE exercicio = '".Sessao::getExercicio() . "'";
}

if ($_REQUEST["campoNom"]) {
    $stLink .= '&campoNom='.$_REQUEST['campoNom'];
}
if ($_REQUEST["nomForm"]) {
    $stLink .= '&nomForm='.$_REQUEST['nomForm'];
}
if ($_REQUEST["campoNum"]) {
    $stLink .= '&campoNum='.$_REQUEST['campoNum'];
}
if ($_REQUEST['inCodRecurso']) {

    if ($_REQUEST['boUtilizaDestinacao'] == true)
        $stFiltro.= " AND recurso_destinacao.cod_recurso = '".$_REQUEST['inCodRecurso']."'";
    if ($_REQUEST['boUtilizaDestinacao'] == false)
        $stFiltro.= " AND cod_recurso = '".$_REQUEST['inCodRecurso']."'";

    $stLink  .= '&inCodRecurso='.$_REQUEST['inCodRecurso'];
}
if ($_REQUEST['stDescricaoRecurso']) {

    if ($_REQUEST['boUtilizaDestinacao'] == true)
        $stFiltro.= " AND recurso.nom_recurso = '".$_REQUEST['stDescricaoRecurso']."'";
    if ($_REQUEST['boUtilizaDestinacao'] == false)
        $stFiltro.= " AND nom_recurso = '".$_REQUEST['stDescricaoRecurso']."'";
    $stLink  .= '&stDescricaoRecurso='.$_REQUEST['stDescricaoRecurso'];
}
if ($_REQUEST['boUtilizaDestinacao']) {
    $stLink .= '&boUtilizaDestinacao='.$_REQUEST['boUtilizaDestinacao'];
}

$stLink .= "&stAcao=".$stAcao;

$boUtilizaDestinacao = $_REQUEST['boUtilizaDestinacao'];

if ($_REQUEST['boUtilizaDestinacao'] == true)
    $stOrder = "recurso.cod_recurso";
if ($_REQUEST['boUtilizaDestinacao'] == false)
    $stOrder = "cod_recurso";

if ($_REQUEST['boUtilizaDestinacao'] == true) {
    $obTOrcamentoRecursoDestinacao = new TOrcamentoRecursoDestinacao();
    $obTOrcamentoRecursoDestinacao->recuperaRelacionamento($rsLista, $stFiltro, $stOrder);
}

if ($_REQUEST['boUtilizaDestinacao'] == false) {
    $obTOrcamentoRecursoDireto = new TOrcamentoRecursoDireto();
    $obTOrcamentoRecursoDireto->recuperaTodos($rsLista, $stFiltro, $stOrder);
}

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];

// Definicao dos objetos hidden
$obHdnForm = new Hidden();
$obHdnForm->setName( "nomForm" );
$obHdnForm->setValue( $_REQUEST['nomForm'] );

$obHdnCampoNum = new Hidden();
$obHdnCampoNum->setName( "campoNum" );
$obHdnCampoNum->setValue( $_REQUEST[ 'campoNum' ] );

$obHdnCampoNom = new Hidden();
$obHdnCampoNom->setName( "campoNom" );
$obHdnCampoNom->setValue( $_REQUEST[ 'campoNom' ] );

$stLink .= "&stAcao=".$stAcao;

$obLista = new Lista;
$obLista->setRecordSet($rsLista);
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth(5);
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Código ");
$obLista->ultimoCabecalho->setWidth(15);
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Descrição ");
$obLista->ultimoCabecalho->setWidth(75);
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth(5);
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo('cod_recurso');
$obLista->ultimoDado->setAlinhamento('DIREITA');
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo('nom_recurso');
$obLista->ultimoDado->setAlinhamento('ESQUERDA');
$obLista->commitDado();

$stAcao = "SELECIONAR";
$obLista->addAcao();
$obLista->ultimaAcao->setAcao($stAcao);
$obLista->ultimaAcao->setFuncao(true);
$obLista->ultimaAcao->setLink("JavaScript:insereRecurso()");
$obLista->ultimaAcao->addCampo("1",	"cod_recurso"   );
$obLista->ultimaAcao->addCampo("2",	"nom_recurso"   );
$obLista->commitAcao();
$obLista->show();

$obFormulario = new Formulario();

$obBtnCancelar = new Button();
$obBtnCancelar->setName( 'cancelar' );
$obBtnCancelar->setValue( 'Cancelar' );
$obBtnCancelar->obEvento->setOnClick( "window.close();" );

$obBtnFiltro = new Button();
$obBtnFiltro->setName( 'filtro' );
$obBtnFiltro->setValue( 'Filtro' );
$obBtnFiltro->obEvento->setOnClick( "Javascript:history.back(-1);" );

$obFormulario->defineBarra( array( $obBtnCancelar,$obBtnFiltro ) , '', '' );

$obFormulario = new Formulario;
$obFormulario->addHidden($obHdnForm);
$obFormulario->addHidden($obHdnCampoNum);
$obFormulario->addHidden($obHdnCampoNom);

$obFormulario->show();

?>
