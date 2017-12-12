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
  * Página de Listagem de Lançamento de Receita
  * Data de criação : 20/06/2005

  * @author Analista: Fábio Bertoldi
  * @author Programador: tonismar R. Bernardo

  * @ignore

    * $Id: LSLancarReceita.php 59612 2014-09-02 12:00:51Z gelson $

  Caso de uso: uc-05.03.06
**/

/*
$Log$
Revision 1.5  2006/09/15 11:14:47  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_ARR_NEGOCIO."RARRAvaliacaoEconomica.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "LancarReceita";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$stCaminho = "../modulos/arrecadacao/movimentacoes/";

$obRARRAvaliacaoEconomica = new RARRAvaliacaoEconomica;
$stAcao = $request->get('stAcao');
if ($stAcao == "") {
    $stAcao = "incluir";
}

//Define arquivos PHP para cada acao
switch ($stAcao) {
    case 'incluir'   : $pgProx = $pgForm; break;
    case 'excluir'   : $pgProx = $pgProc; break;
    DEFAULT          : $pgProx = $pgForm;
}

//MANTEM FILTRO E PAGINACAO
$link = Sessao::read( "link" );
$stLink .= "&stAcao=".$stAcao;
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

if ($_REQUEST[ 'inNumCGM' ]) {
    $obRARRAvaliacaoEconomica->obRCEMInscricaoEconomica->obRCGM->setNumCGM( $_REQUEST[ 'inNumCGM' ] );
}

if ($_REQUEST[ 'inInscricaoEconomica' ]) {
    $obRARRAvaliacaoEconomica->obRCEMInscricaoEconomica->setInscricaoEconomica( $_REQUEST[ 'inInscricaoEconomica' ] );
}

$obRARRAvaliacaoEconomica->obRCEMInscricaoEconomica->addInscricaoAtividade();
$obRARRAvaliacaoEconomica->obRCEMInscricaoEconomica->roUltimaInscricaoAtividade->addAtividade();
if ($_REQUEST[ "inCodigoAtividade"]) {
    $obRARRAvaliacaoEconomica->obRCEMInscricaoEconomica->roUltimaInscricaoAtividade->roUltimaAtividade->setCodigoAtividade( $_REQUEST[ "inCodigoAtividade"] );
}

if ( $_REQUEST[ "inCodLocalizacao_".( $_REQUEST["inNumNiveis"] - 1 ) ] ) {
    $arCodigoLocalizacao = explode( "-", $_REQUEST[ "inCodLocalizacao_".( $_REQUEST["inNumNiveis"] - 1 ) ] );
    $inCodigoLocalizacao = $arCodigoLocalizacao[1];
    $obRARRAvaliacaoEconomica->obRCEMInscricaoEconomica->setDomicilioFiscal( $inCodigoLocalizacao );
}

$rsInscricoes = new RecordSet;
$obRARRAvaliacaoEconomica->listarInscricaoAvaliarReceita( $rsInscricoes );

$obLista = new Lista;

$obLista->setRecordSet( $rsInscricoes );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Inscrição Econômica");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Nome" );
$obLista->ultimoCabecalho->setWidth( 25 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "inscricao_economica" );
$obLista->ultimoDado->setAlinhamento( "DIREITA" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_cgm" );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );

$obLista->ultimaAcao->addCampo("&inInscricaoEconomica"   , "inscricao_economica" );
$obLista->ultimaAcao->addCampo("&stNomCGM"               , "nom_cgm" );
$obLista->ultimaAcao->addCampo("&inNumCGM"               , "numcgm"  );

$obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink );

$obLista->commitAcao();
$obLista->show();
