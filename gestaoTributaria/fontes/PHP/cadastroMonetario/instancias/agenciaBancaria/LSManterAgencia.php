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
    * Página de Formulario de Inclusao/Alteracao de Agencia

    * Data de Criação   : 28/10/2005

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Lizandro Kirst da Silva
    * @ignore

    * $Id: LSManterAgencia.php 63839 2015-10-22 18:08:07Z franver $

    *Casos de uso: uc-05.05.02

*/

/*
$Log$
Revision 1.7  2006/09/15 14:57:28  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_MON_NEGOCIO."RMONAgencia.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterAgencia";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$stCaminho     = CAM_GT_MON_INSTANCIAS."agenciaBancaria/";
$obRMONAgencia = new RMONAgencia;
$obRMONBanco   = new RMONBanco;
$obRCGM        = new RCGM;
//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc

if ( empty( $_REQUEST['stAcao'] ) ) {
    $_REQUEST['stAcao'] = "alterar";
}
//Define arquivos PHP para cada acao
switch ($_REQUEST['stAcao']) {
    case 'alterar'  : $pgProx = $pgForm; break;
    case 'excluir'  : $pgProx = $pgProc; break;
    case 'baixar'   : $pgProx = $pgFormBaixar; break;
    DEFAULT         : $pgProx = $pgForm;
}

//MANTEM FILTRO E PAGINACAO
$link = Sessao::read( "link" );
$stLink .= "&stAcao=".$_REQUEST['stAcao'];
if ($_GET["pg"] and  $_GET["pos"]) {
    $stLink.= "&pg=".$_GET["pg"]."&pos=".$_GET["pos"];
    $link["pg"]  = $_GET["pg"];
    $link["pos"] = $_GET["pos"];
}

//USADO QUANDO EXISTIR FILTRO
//NA FL O VAR LINK DEVE SER RESETADA
if ( is_array($link) ) {
    $_REQUEST = $link;
} else {
    foreach ($_REQUEST as $key => $valor) {
        $link[$key] = $valor;
    }
}

Sessao::write('link'  , $link);
Sessao::write('stLink', $stLink);

//MONTA O FILTRO
if ($_REQUEST["inCodBancoTxt"]) {
    $obRMONAgencia->obRMONBanco->setNumBanco( $_REQUEST['inCodBancoTxt'] );
}

if ($_REQUEST["stNumAgencia"]) {
    $obRMONAgencia->setNumAgencia( $_REQUEST['stNumAgencia'] );
}

if ($_REQUEST["stNomAgencia"]) {
    $obRMONAgencia->setNomAgencia( $_REQUEST["stNomAgencia"] );
}

if ($_REQUEST["inNumCGMAgencia"]) {
    $obRMONAgencia->obRCGM->setNumCGM( $_REQUEST["inNumCGMAgencia"] );
}

$stLink .= "&stAcao=".$_REQUEST['stAcao'];
$obRMONAgencia->listarAgencia($rsLista);

$obLista = new Lista;
$obLista->setRecordSet( $rsLista );
$obLista->setTitulo("Registros de Agência");
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Banco ");
$obLista->ultimoCabecalho->setWidth( 30 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Número ");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Agência ");
$obLista->ultimoCabecalho->setWidth( 80 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "[num_banco] - [nom_banco]" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "num_agencia" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_agencia" );
$obLista->commitDado();
$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $_REQUEST['stAcao'] );
$obLista->ultimaAcao->addCampo("&stNumAgencia",       "num_agencia"        );
$obLista->ultimaAcao->addCampo("&stNomBanco",         "nom_banco"          );
$obLista->ultimaAcao->addCampo("&stNomAgencia",       "nom_agencia"        );
$obLista->ultimaAcao->addCampo("&inNumBanco",         "num_banco"          );
$obLista->ultimaAcao->addCampo("&inCodBanco",         "cod_banco"          );
$obLista->ultimaAcao->addCampo("&inCodAgencia",       "cod_agencia"        );
$obLista->ultimaAcao->addCampo("&inNumCGMAgencia",    "numcgm_agencia"     );
$obLista->ultimaAcao->addCampo("&stContato",          "nom_pessoa_contato" );
$obLista->ultimaAcao->addCampo("&stDescQuestao","[num_agencia] - [nom_agencia]");
if ($_REQUEST['stAcao'] == "excluir") {
    $obLista->ultimaAcao->setLink( $stCaminho.$pgProx."?".Sessao::getId().$stLink );
} else {
    $obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink );
}

$obLista->commitAcao();
$obLista->show();

$obFormulario = new Formulario;
$obFormulario->setAjuda  ( "UC-05.05.02" );
$obFormulario->show();
