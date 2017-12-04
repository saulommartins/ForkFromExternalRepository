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
  * Página de Lista de Autoridade
  * Data de criação : 15/09/2006

  * @author Analista: Fábio Bertoldi
  * @author Programador: Fernando Piccini Cercato

    * $Id: LSManterAutoridade.php 59612 2014-09-02 12:00:51Z gelson $

  Caso de uso: uc-05.04.08
**/

/*
$Log$
Revision 1.3  2007/03/01 13:30:45  cercato
Bug #8521#

Revision 1.2  2006/09/26 11:12:33  dibueno
Utilização do cod_autoridade no filtro

Revision 1.1  2006/09/18 17:18:29  cercato
formularios da autoridade de acordo com interface abstrata.

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATAutoridade.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterAutoridade";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$stCaminho = "../../../../../../gestaoTributaria/fontes/PHP/dividaAtiva/instancias/autoridade/";

include_once ( $pgJS );

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
if ( empty( $_REQUEST['stAcao'] ) ) {
    $_REQUEST['stAcao'] = "incluir";
}

//Define arquivos PHP para cada acao
switch ($_REQUEST['stAcao']) {
    case 'alterar'   : $pgProx = $pgForm; break;
    case 'excluir'   : $pgProx = $pgProc; break;
    DEFAULT          : $pgProx = $pgForm;
}

//MANTEM FILTRO E PAGINACAO
$link = Sessao::read( 'link' );
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

//MONTAGEM DO FILTRO
$stFiltro = '';
if ($_REQUEST['inCGM']) {
    $stFiltro .= " \n ps.numcgm = '".$_REQUEST['inCGM']."' AND ";
}
if ($_REQUEST['inCodAutoridade']) {
    $stFiltro .= " \n da.cod_autoridade = '".$_REQUEST['inCodAutoridade']."' AND ";
}

if ($_REQUEST['stTipoAutoridade']) {
    if ( $_REQUEST['stTipoAutoridade'] == "procurador" )
        $stFiltro .= " \n dp.cod_autoridade IS NOT NULL AND ";
    else
        $stFiltro .= " \n dp.cod_autoridade IS NULL AND ";
}

if ( $stFiltro )
    $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );

$obTDATAutoridade = new TDATAutoridade;
$obTDATAutoridade->recuperaListaAutoridade( $rsAutoridade, $stFiltro, " ORDER BY ps.numcgm " );

$obLista = new Lista;
$obLista->setRecordSet( $rsAutoridade );

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("CGM");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Nome" );
$obLista->ultimoCabecalho->setWidth( 25 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Cargo/Função" );
$obLista->ultimoCabecalho->setWidth( 25 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "numcgm" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_cgm" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "descricao" );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $_REQUEST['stAcao'] );

$obLista->ultimaAcao->addCampo( "&inCodAutoridade", "cod_autoridade" );
$obLista->ultimaAcao->addCampo( "&stTipo", "tipo" );
$obLista->ultimaAcao->addCampo( "&inCodUF", "cod_uf" );
$obLista->ultimaAcao->addCampo( "&stNomUF", "nom_uf" );
$obLista->ultimaAcao->addCampo( "&inCodNorma", "cod_norma" );
$obLista->ultimaAcao->addCampo( "&stNomNorma", "nom_norma" );
$obLista->ultimaAcao->addCampo( "&stNomUF", "nom_uf" );
$obLista->ultimaAcao->addCampo( "&stNumCGM", "numcgm" );
$obLista->ultimaAcao->addCampo( "&stNomCGM", "nom_cgm" );
$obLista->ultimaAcao->addCampo( "&stOAB", "oab" );
$obLista->ultimaAcao->addCampo( "&inMatricula", "registro" );
$obLista->ultimaAcao->addCampo( "&stDescricao", "descricao" );
$obLista->ultimaAcao->addCampo( "&stVigencia", "vigencia" );
$obLista->ultimaAcao->addCampo( "&stDescQuestao", "cod_autoridade" );

if ($_REQUEST['stAcao'] == "excluir") {
    $obLista->ultimaAcao->setLink( $stCaminho.$pgProx."?".Sessao::getId().$stLink );
} else {
    $obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink );
}

$obLista->commitAcao();
$obLista->show();
