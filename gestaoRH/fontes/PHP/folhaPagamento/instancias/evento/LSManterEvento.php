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
    * Página de Listagem Evento
    * Data de Criação   : 10/02/2005

    * @author Analista: Leandro Oliveira
    * @author Desenvolvedor: Lucas Leusin Oaigen

    * @ignore

    $Revision: 30727 $
    $Name$
    $Author: souzadl $
    $Date: 2008-03-26 10:16:48 -0300 (Qua, 26 Mar 2008) $

    * Casos de uso: uc-04.05.06

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoEvento.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterEvento";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$stCaminho = CAM_GRH_FOL_INSTANCIAS."evento/";

$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];

$obRFolhaPagamentoEvento = new RFolhaPagamentoEvento;

if ( empty($stAcao) ) {
    $stAcao = "alterar";
}

switch ($stAcao) {
    case 'alterar': $pgProx = $pgForm; break;
    case 'excluir': $pgProx = $pgProc; break;
    default       : $pgProx = $pgForm;
}

//Mantem filtro e paginacao
$stLink .= "&stAcao=".$stAcao;
$link = Sessao::read("link");
if ($_GET["pg"] and  $_GET["pos"]) {
    $stLink.= "&pg=".$_GET["pg"]."&pos=".$_GET["pos"];
    $link["pg"]  = $_GET["pg"];
    $link["pos"] = $_GET["pos"];
}
Sessao::write("link",$link);
//USADO QUANDO EXISTIR FILTRO
//NA FL O VAR LINK DEVE SER RESETADA
if ( is_array(Sessao::read("link")) ) {
    $_REQUEST = Sessao::read("link");
} else {
    foreach ($_REQUEST as $key => $valor) {
        $link[$key] = $valor;
    }
    Sessao::write("link",$link);
}
//Monta o filtro
$stCodigo    = $_REQUEST['stCodigo'];
$stDescricao =  $_REQUEST['stDescricao'];
if ( strlen($stCodigo) > 0 ) {
    $obRFolhaPagamentoEvento->setCodigo( $_REQUEST["stCodigo"] );
}
if ( strlen($stDescricao) > 0 ) {
    $obRFolhaPagamentoEvento->setDescricao( $_REQUEST["stDescricao"] );
}

$obRFolhaPagamentoEvento->listarEvento( $rsListaEvento );

$obLista = new Lista;

$obLista->setRecordSet( $rsListaEvento );

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Código ");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Descrição" );
$obLista->ultimoCabecalho->setWidth( 55 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "codigo" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "descricao" );
$obLista->commitDado();
$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->addCampo( "&inCodEvento"   , "cod_evento");
$obLista->ultimaAcao->addCampo( "stTimestamp"    , "timestamp");
$obLista->ultimaAcao->addCampo( "stCodigo"       , "codigo");

if ($stAcao == "excluir") {
    $obLista->ultimaAcao->addCampo( "&stDescQuestao" , "descricao" );
    $obLista->ultimaAcao->setLink( $stCaminho.$pgProx."?".Sessao::getId().$stLink );
} else {
    $obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink );
}
$obLista->commitAcao();
$obLista->show();

?>
