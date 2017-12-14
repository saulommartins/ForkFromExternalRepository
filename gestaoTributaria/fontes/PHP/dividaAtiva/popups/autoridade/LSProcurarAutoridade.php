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
  * Página de Lista de Autoridade da POP UP
  * Data de criação : 26/09/2006

  * @author Analista: Fábio Bertoldi
  * @author Programador: Diego Bueno Coelho

    * $Id: LSProcurarAutoridade.php 63839 2015-10-22 18:08:07Z franver $

  Caso de uso: uc-05.04.08
**/

/*
$Log$
Revision 1.1  2006/09/26 11:15:13  dibueno
*** empty log message ***

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATAutoridade.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ProcurarAutoridade";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

//$stCaminho = "../../../../../../gestaoTributaria/fontes/PHP/dividaAtiva/instancias/autoridade/";

include_once ( $pgJS );

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
if ( empty( $_REQUEST['stAcao'] ) ) {
    $_REQUEST['stAcao'] = "incluir";
}

$stNomCGMServidor = $_REQUEST['stNomCGMServidor'];
$inNumCGMServidor = $_REQUEST['inNumCGMServidor'];
$stTipoAutoridade = $_REQUEST['stTipoAutoridade'];

//Define arquivos PHP para cada acao
switch ($_REQUEST['stAcao']) {
    case 'alterar'   : $pgProx = $pgForm; break;
    case 'excluir'   : $pgProx = $pgProc; break;
    DEFAULT          : $pgProx = $pgForm;
}

//MANTEM FILTRO E PAGINACAO
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

Sessao::write('link', $link);
Sessao::write('stLink', $stLink);

//MONTAGEM DO FILTRO
$stFiltro = '';
if ($inNumCGMServidor) {
    $stFiltro .= " \n ps.numcgm = '".$inNumCGMServidor."' AND ";
}

if ($stTipoAutoridade) {
    if ( $stTipoAutoridade == "procurador" )
        $stFiltro .= " \n dp.cod_autoridade IS NOT NULL AND ";
    else
        $stFiltro .= " \n dp.cod_autoridade IS NULL AND ";
}
if ($stNomCGMServidor) {
    $stFiltro .= "\n upper(sc.nom_cgm) like upper('%". $stNomCGMServidor."%') AND ";
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

$_REQUEST['stAcao'] = "SELECIONAR";
$obLista->ultimaAcao->setAcao( $_REQUEST['stAcao'] );
$obLista->ultimaAcao->setFuncao( true );
$obLista->ultimaAcao->setLink( "JavaScript:Insere();" );
$obLista->ultimaAcao->addCampo("1","cod_autoridade");
$obLista->ultimaAcao->addCampo("2","autoridade");
//$obLista->ultimaAcao->addCampo("3","numcgm");

$obLista->commitAcao();
$obLista->show();

$obHdnCampoNom = new Hidden;
$obHdnCampoNom->setName( "campoNom" );
$obHdnCampoNom->setValue( $_REQUEST['campoNom'] );

$obHdnCampoNum = new Hidden;
$obHdnCampoNum->setName( "campoNum" );
$obHdnCampoNum->setValue( $_REQUEST['campoNum'] );

$obBtnFiltro = new Button;
$obBtnFiltro->setName              ( "btnFiltrar" );
$obBtnFiltro->setValue             ( "Filtrar"    );
$obBtnFiltro->setTipo              ( "button"     );
$obBtnFiltro->obEvento->setOnClick ( "filtrar();" );
$obBtnFiltro->setDisabled          ( false        );

$botoes = array ( $obBtnFiltro );

$obFormulario = new Formulario;
$obFormulario->addHidden($obHdnCampoNom);
$obFormulario->addHidden($obHdnCampoNum);
$obFormulario->defineBarra ( $botoes, 'left', '' );
$obFormulario->show();
