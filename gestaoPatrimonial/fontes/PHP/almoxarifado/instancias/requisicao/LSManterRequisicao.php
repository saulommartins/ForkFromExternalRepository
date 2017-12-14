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
 * Página de Filtro de Requisição
 * Data de Criação   : 03/03/2006

 * @author Analista: Diego Barbosa Victoria
 * @author Desenvolvedor: Tonismar Régis Bernardo

 * @ignore

 * Casos de uso: uc-03.03.10

 $Id: LSManterRequisicao.php 59612 2014-09-02 12:00:51Z gelson $

 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once CAM_GP_ALM_NEGOCIO."RAlmoxarifadoRequisicao.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ManterRequisicao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$stCaminho = CAM_GP_ALM_INSTANCIAS."requisicao/";

$stAcao = $request->get('stAcao');

if (empty($stAcao)) {
    $stAcao = "alterar";
}

switch ($stAcao) {
    case 'alterar':
        $pgProx = $pgForm;
    break;

    case 'excluir':
        $pgProx = $pgProc;
    break;

    case 'anular':
        $pgProx = $pgForm;
    break;

    case 'homologar':
        $pgProx = $pgForm;
    break;

    case 'anular_homolog':
        $pgProx = $pgForm;
    break;

    case 'consultar':
        $pgProx = $pgForm;
    break;
}

//MANTEM FILTRO E PAGINACAO
$link = Sessao::read("link");
$stLink .= "&stAcao=".$_REQUEST['stAcao'];
if ($_GET["pg"] and  $_GET["pos"]) {
    $link["pg"]  = $_GET["pg"];
    $link["pos"] = $_GET["pos"];
}

//USADO QUANDO EXISTIR FILTRO
//NA FL O VAR LINK DEVE SER RESETADA
if ( is_array($link) ) {
    $_REQUEST["pg"] = $link["pg"];
    $_REQUEST["pos"] = $link["pos"];
    $_GET["pg"] = $link["pg"];
    $_GET["pos"] = $link["pos"];

    foreach ($link as $key => $valor) {
        if (!isset ($_REQUEST[$key])) {
            $_REQUEST[$key] = $valor;
        }
    }
} else {
    foreach ($_REQUEST as $key => $valor) {
        $link[$key] = $valor;
    }
}

Sessao::write('link'  , $link);
Sessao::write('stLink', $stLink);

$obRAlmoxarifadoRequisicao = new RAlmoxarifadoRequisicao;
$obRAlmoxarifadoRequisicao->addRequisicaoItem();

# Montando filtro
if ($_REQUEST['stExercicio']) {
    $obRAlmoxarifadoRequisicao->setExercicio( $_REQUEST['stExercicio'] );
}

if (count($_REQUEST['inCodAlmoxarifado']) > 0) {
    $obRAlmoxarifadoRequisicao->obRAlmoxarifadoAlmoxarifado->setCodigo(implode(',',$_REQUEST['inCodAlmoxarifado']));
}

if ($_REQUEST['inCodRequisicao']) {
    $obRAlmoxarifadoRequisicao->setCodigo( $_REQUEST['inCodRequisicao'] );
}

if ($_REQUEST['stHdnObservacao']) {
    $obRAlmoxarifadoRequisicao->setObservacao( $_REQUEST['stHdnObservacao'] );
}

if ($_REQUEST['inCodItem']) {
    $obRAlmoxarifadoRequisicao->roUltimoRequisicaoItem->obRAlmoxarifadoEstoqueItem->obRCatalogoItem->setCodigo( $_REQUEST['inCodItem'] );
}

if ($_REQUEST['inCodMarca']) {
    $obRAlmoxarifadoRequisicao->roUltimoRequisicaoItem->obRAlmoxarifadoEstoqueItem->obRMarca->setCodigo( $_REQUEST['inCodMarca'] );
}

if ($_REQUEST['inCodCentroCusto']) {
    $obRAlmoxarifadoRequisicao->roUltimoRequisicaoItem->obRAlmoxarifadoEstoqueItem->obRCentroDeCustos->setCodigo( $_REQUEST['inCodCentroCusto'] );
}

if ($_REQUEST['inCGMSolicitante']) {
    $obRAlmoxarifadoRequisicao->setCGMSolicitante( $_REQUEST['inCGMSolicitante'] );
}

if ($_REQUEST['inCGMRequisitante']) {
    $obRAlmoxarifadoRequisicao->setCGMRequisitante( $_REQUEST['inCGMRequisitante'] );
}

$obRAlmoxarifadoRequisicao->setAcao($stAcao);

$stOrder = "cod_requisicao DESC, req.dt_requisicao DESC";

if ($stAcao == "consultar") {
    $obRAlmoxarifadoRequisicao->listarRequisicaoItemConsultar($rsLista, "", $stOrder);
} elseif ($stAcao == "anular") {
    $obRAlmoxarifadoRequisicao->listarRequisicaoAlteracaoAnulacao($rsLista, "", $stOrder);
} else {
    $obRAlmoxarifadoRequisicao->listarRequisicaoAlteracao($rsLista, "", $stOrder);
}

$obLista = new Lista;

$obLista->setRecordSet( $rsLista );

$obLista->obPaginacao->setFiltro("&stLink=".$stLink );

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Exercício");
$obLista->ultimoCabecalho->setWidth( 8 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Código" );
$obLista->ultimoCabecalho->setWidth( 8 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Almoxarifado" );
$obLista->ultimoCabecalho->setWidth( 55 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Data" );
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Ação");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "exercicio" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "cod_requisicao" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "[cod_almoxarifado] - [nom_cgm]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "dt_requisicao" );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao($stAcao);
$obLista->ultimaAcao->addCampo("&inCodAlmoxarifado"   , "cod_almoxarifado");
$obLista->ultimaAcao->addCampo("&inCodRequisicao"     , "cod_requisicao");
$obLista->ultimaAcao->addCampo("&stExercicio"         , "exercicio");

$obLista->ultimaAcao->setLink( $stCaminho.$pgProx."?".Sessao::getId().$stLink );

$obLista->setAjuda("UC-03.03.10");
$obLista->commitAcao();
$obLista->Show();
?>
