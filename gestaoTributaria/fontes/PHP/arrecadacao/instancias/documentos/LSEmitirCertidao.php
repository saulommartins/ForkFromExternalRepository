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
  * Página de Lista de Emissão de Certidão
  * Data de criação : 11/11/2005

  * @author Analista: Fábio Bertoldi
  * @author Programador: Tonismar R. Bernardo

  * @ignore

    * $Id: LSEmitirCertidao.php 59612 2014-09-02 12:00:51Z gelson $

  Caso de uso: uc-05.03.11
**/

/*
$Log$
Revision 1.5  2006/09/15 11:50:45  fabio
corrigidas tags de caso de uso

Revision 1.4  2006/09/15 11:08:04  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GT_ARR_NEGOCIO."RARRCarne.class.php" );

//Definicao dos nomes de arquivos
$stPrograma = "EmitirCertidao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php"; $pgForm = "FM".$stPrograma.".php";
$pgFormVinculo = "FM".$stPrograma."Vinculo.php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$stCaminho = CAM_GT_ARR_INSTANCIA."documentos/";

$stAcao = $request->get('stAcao');

//Define arquivos PHP para cada acao
switch ($stAcao) {
    case 'incluir'   : $pgProx = $pgForm; break;
    case 'excluir'   : $pgProx = $pgProc; break;
    case 'alterar'   : $pgProx = $pgForm; break;
    DEFAULT          : $pgProx = $pgForm;
}

//MANTEM FILTRO E PAGINACAO
$stLink .= "&stAcao=".$stAcao;
$link = Sessao::read( "link" );
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

Sessao::write( 'link', $link );
$obRARRCarne = new RARRCarne;

//MONTA FILTRO
if (( $_REQUEST['inCodContribuinteInicial'] ) && ( $_REQUEST['inCodContribuinteFinal'] )) {
} elseif ($_REQUEST['inCodContribuinteInicial']) {
} elseif ($_REQUEST['inCodContribuinteFinal']) {
}

if (( $_REQUEST['inNumInscricaoImobiliariaInicial'] ) && ( $_REQUEST['inNUmInscricaoImobiliariaFinal'] )) {
} elseif ($_REQUEST['inNumInscricaoImobiliariaInicial']) {
} elseif ($_REQUEST['inNumInscricaoImobiliariaFinal']) {
}

if (( $_REQUEST['inCodLocalizacaoInicial'] )&&( $_REQUEST['inCodLocalizacaoFinal'] )) {
} elseif ($_REQUEST['inCodLocalizacaoInicial']) {
} elseif ($_REQUEST['inCodLocalizacaoFinal']) {
}

if (( $_REQUEST['inNumInscricaoEconomicaInicial'] )&&( $_REQUEST['inNumInscricaoEconomicaFinal'] )) {
} elseif ($_REQUEST['inNumInscricaoEconomicaInicial']) {
} elseif ($_REQUEST['inNumInscricaoEconomicaFinal']) {
}

if (( $_REQUEST['inCodAtividadeInicial'] )&&( $_REQUEST['inCodAtividadeFinal'] )) {
} elseif ($_REQUEST['inCodAtividadeInicial']) {
} elseif ($_REQUEST['inCodAtividadeFinal']) {
}

$obRARRCarne->listaCarne( $rsCertidao );

$obLista = new Lista;

$obLista->setRecordSet( $rsCertidao );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Certidão");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Nome");
$obLista->ultimoCabecalho->setWidth( 25 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Inscrição Imobiliária");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Inscrição Econômica");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo      ( "nr_parcela" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO'     );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_cgm" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "inscricao_municipal" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "inscricao_economica" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();
$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );

if ($stAcao == "excluir") {
    $obLista->ultimaAcao->setLink( $stCaminho.$pgProx."?".Sessao::getId().$stLink );
} else {
    $obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink );
}

$obLista->commitAcao();
$obLista->show();
