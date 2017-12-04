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
    * Página de Listagem de pontos facultativo
    * Data de Criação   : 28/04/2005

    * @author Vandré Miguel Ramos

    * @ignore

    $Revision: 30859 $
    $Name$
    $Author: vandre $
    $Date: 2006-08-08 14:53:12 -0300 (Ter, 08 Ago 2006) $

    * Casos de uso :uc-04.02.05

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GRH_CAL_NEGOCIO."RCalendario.class.php" );
include_once( CAM_GRH_CAL_NEGOCIO."RCalendarioDiaCompensado.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterDiaCompensado";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgCons = "FMConsultarDiaCompensado.php";

$stCaminho   = "../../../../../../gestaoRH/fontes/PHP/calendario/instancias/diaCompensado/";

$obRDiaCompensado = new RCalendarioDiaCompensado;

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

//Define arquivos PHP para cada acao
switch ($stAcao) {
    case 'alterar'  : $pgProx = $pgForm; break;
    case 'baixar'   : $pgProx = $pgBaix; break;
    case 'excluir'  : $pgProx = $pgProc; break;
    case 'prorrogar': $pgProx = $pgCons; break;
    case 'consultar': $pgProx = $pgCons; break;
    DEFAULT         : $pgProx = $pgForm;
}

$arSessaoLink = Sessao::read('link');
if ( is_array($arSessaoLink) ) {
    $_REQUEST = $arSessaoLink;
} else {
    foreach ($_REQUEST as $key => $valor) {
        $arSessaoLink[$key] = $valor;
    }
}
if ($_GET["pg"] and $_GET["pos"]) {
    $arSessaoLink["pg"] = $_GET["pg"];
    $arSessaoLink["pos"] = $_GET["pos"];
}
Sessao::write('link', $arSessaoLink);

if ($_REQUEST['dtData']) {
    $obRDiaCompensado->setDtFeriado( $_REQUEST['dtData'] );
}

$stLink .= "&stAcao=".$stAcao;

$obRDiaCompensado->listarDiaCompensado($rsLista, $stOrdem,$boTransacao);

$obLista = new Lista;
$obLista->setRecordSet( $rsLista );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Data do feriado ");
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Tipo Feriado");
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Descrição ");
$obLista->ultimoCabecalho->setWidth( 65 );
$obLista->commitCabecalho();

if ($stAcao != 'consultar') {
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();
}

$obLista->addDado();
$obLista->ultimoDado->setCampo( "dt_feriado" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "tipoferiado" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "descricao" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();

if ($stAcao != 'consultar') {
    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( $stAcao );
    $obLista->ultimaAcao->addCampo("&inCodFeriado","cod_feriado");
    $obLista->ultimaAcao->addCampo("stDescQuestao","descricao");
    $obLista->ultimaAcao->addCampo("stTipoFeriado","tipoferiado");
    $obLista->ultimaAcao->addCampo("dt_feriado","dt_feriado");

    if ($stAcao == "excluir") {
        $obLista->ultimaAcao->setLink( $stCaminho.$pgProx."?" . Sessao::getId().$stLink . '&dtData=25/12/2004' . '&stAcao=excluir');
    } else {
        $obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink );
    }

    $obLista->commitAcao();
}

$obLista->show();
?>
