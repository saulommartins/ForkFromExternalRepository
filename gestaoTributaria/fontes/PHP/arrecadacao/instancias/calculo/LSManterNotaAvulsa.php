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
  * Página de Lista Nota Avulsa
  * Data de criação : 19/06/2008

  * @author Analista: Fábio Bertoldi
  * @author Programador: Fernando Piccini Cercato

  * $Id: $

  Caso de uso: uc-05.03.22
**/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMCadastroEconomico.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterNotaAvulsa";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
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

Sessao::write( "link", $link );
//MONTAGEM DO FILTRO
$stFiltro = '';
//    [stEscrituracao] => servico
if ($_REQUEST['inInscricaoEconomica']) {
    $stFiltro .= " \n ece.inscricao_economica = '".$_REQUEST['inInscricaoEconomica']."' AND ";
}

if ($_REQUEST['inCGM']) {
    $stFiltro .= " ( eca.numcgm = ".$_REQUEST['inCGM']." OR ecf.numcgm = ".$_REQUEST['inCGM']." OR ecd.numcgm = ".$_REQUEST['inCGM'].") AND ";
}

$stFiltro .= " ( COALESCE(eceml.cod_modalidade, eam.cod_modalidade) IS NOT NULL) AND ";

if ( $stFiltro )
    $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );

$obTCEMCadastroEconomico = new TCEMCadastroEconomico;
$obTCEMCadastroEconomico->recuperaModalidadeAtividadeInscricao( $rsLista, $stFiltro );

$obLista = new Lista;
$obLista->setRecordSet( $rsLista );

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Inscrição Econômica" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Nome" );
$obLista->ultimoCabecalho->setWidth( 25 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Atividade" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Modalidade" );
$obLista->ultimoCabecalho->setWidth( 25 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "inscricao_economica" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "[numcgm] - [nom_cgm] " );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_atividade" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_modalidade" );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );

$obLista->ultimaAcao->addCampo( "&inInscricaoEconomica", "inscricao_economica" );
$obLista->ultimaAcao->addCampo( "&inNumCGM", "numcgm" );
$obLista->ultimaAcao->addCampo( "&stNomCGM", "nom_cgm" );
$obLista->ultimaAcao->addCampo( "&inCodAtividade", "cod_atividade" );
$obLista->ultimaAcao->addCampo( "&stNomAtividade", "nom_atividade" );
$obLista->ultimaAcao->addCampo( "&inCodModalidade", "cod_modalidade" );
$obLista->ultimaAcao->addCampo( "&stNomModalidade", "nom_modalidade" );

$obLista->ultimaAcao->setLink( $pgForm."?".Sessao::getId().$stLink );

$obLista->commitAcao();
$obLista->show();
