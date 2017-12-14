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
* Página de Listagem de Procura de PPA
* Data de Criação   : 21/10/2008

* @author Analista: Heleno Santos
* @author Desenvolvedor: Fellipe Esteves dos Santos
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GF_PPA_MAPEAMENTO."TPPA.class.php");

//Define o nome dos arquivos PHP
$stPrograma 	= "ProcurarPPA";
$pgFilt 				= "FL".$stPrograma.".php";
$pgList 			= "LS".$stPrograma.".php";
$pgJs   			= "JS".$stPrograma.".php";

include_once($pgJs);

$stCaminho   	= CAM_GF_PPA_INSTANCIAS."ppa/";

$obTPPA = new TPPA;

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

if ($_REQUEST['inCodPPA']) {
    $obRegra->setCodigo( $_REQUEST['inCodPPA'] );
    $stLink .= '&inCodPPA='.$_REQUEST['inCodPPA'];
}

if ($_REQUEST['stAnoInicio']) {
    $stFiltro.= " orgao.ano_inicio = '".$_REQUEST['stAnoInicio']."' and ";
    $stLink .= '&stAnoInicio='.$_REQUEST['stAnoInicio'];
}

if ($stFiltro) {
    $stFiltro = ' where '.substr( $stFiltro, 0, strlen( $stFiltro ) - 4 );
}

$stLink .= "&stAcao=".$stAcao;

$stOrder = " order by ppa.ano_inicio";

$obTPPA->recuperaOrgao($rsLista, $stFiltro, $stOrder);

$obLista = new Lista;
$obLista->setRecordSet($rsLista);
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth(5);
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Inicio ");
$obLista->ultimoCabecalho->setWidth(15);
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Fim ");
$obLista->ultimoCabecalho->setWidth(75);
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth(5);
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo('ano_inicio');
$obLista->ultimoDado->setAlinhamento('DIREITA');
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo('ano_fim');
$obLista->ultimoDado->setAlinhamento('ESQUERDA');
$obLista->commitDado();

$stAcao = "SELECIONAR";
$obLista->addAcao();
$obLista->ultimaAcao->setAcao($stAcao);
$obLista->ultimaAcao->setFuncao(true);
$obLista->ultimaAcao->setLink("JavaScript:inserePPA()");
$obLista->ultimaAcao->addCampo("1",	"ano_inicio"   );
$obLista->ultimaAcao->addCampo("2",	"ano_fim"   );
$obLista->commitAcao();
$obLista->show();

$obFormulario = new Formulario;
$obFormulario->addHidden($obHdnForm);
$obFormulario->addHidden($obHdnCampoNum);
$obFormulario->addHidden($obHdnCampoNom);
$obFormulario->show();

?>
