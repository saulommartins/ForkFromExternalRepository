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
  * Página de Lista para popup de MOEDA
  * Data de criação : 19/12/2005

    * @author Analista: Fabio Bertold Rodrigues
    * @author Programador: Diego Bueno Coelho

    * $Id: LSProcurarMoeda.php 59612 2014-09-02 12:00:51Z gelson $

    Caso de uso: uc-05.05.06
**/

/*
$Log$
Revision 1.6  2006/09/18 08:47:27  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GT_MON_NEGOCIO."RMONMoeda.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ProcurarMoeda";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once( $pgJS );

$obRMONMoeda   = new RMONMoeda;

$stFiltro = "";

$stLink .= "&stAcao=".$stAcao;
$stLink .= "&inCodMoeda=".      $_REQUEST["inCodMoeda"];
$stLink .= "&stDescSingular=".  $_REQUEST["stDescSingular"];
$stLink .= "&stFracaoSingular=".$_REQUEST["stFracaoSingular"];
$stLink .= "&stSimbolo=".       $_REQUEST["stSimbolo"];

//MANTEM FILTRO E PAGINACAO
if ($_GET["pg"] and  $_GET["pos"]) {
    $stLink .= "&pg=".$_GET["pg"];
    $stLink .= "&pos=".$_GET["pos"];
}
// FILTRAGEM

if ($_REQUEST["inCodMoeda"]) {
    $obRMONMoeda->setCodMoeda( $_REQUEST["inCodMoeda"] );
    $stLink .= "&inCodMoeda=".$_REQUEST["inCodMoeda"];
}
if ($_REQUEST["stDescSingular"]) {
    $obRMONMoeda->setDescSingular ( $_REQUEST["stDescSingular"] );
    $stLink .= "&stDescSingular=".$_REQUEST["stDescSingular"];
}
if ($_REQUEST["stFracaoSingular"]) {
    $obRMONMoeda->setFracaoSingular ( $_REQUEST["stFracaoSingular"] );
    $stLink .= "&stFracaoSingular=".$_REQUEST["stFracaoSingular"];
}
if ($_REQUEST["stSimbolo"]) {
    $obRMONMoeda->setSimbolo ( $_REQUEST["stSimbolo"] );
    $stLink .= "&stSimbolo=".$_REQUEST["stSimbolo"];
}

$obRMONMoeda->listarMoeda($rsLista, $boTransacao );

$obLista = new Lista;
$obLista->obPaginacao->setFiltro( $stLink );

$obLista->setRecordSet( $rsLista );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Código");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Descrição");
$obLista->ultimoCabecalho->setWidth( 40 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Fração");
$obLista->ultimoCabecalho->setWidth( 40 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Símbolo");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_moeda" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "descricao_singular" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "fracao_singular" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "simbolo" );
$obLista->commitDado();

$obLista->addAcao();

$stAcao = "SELECIONAR";
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->setFuncao( true );
$obLista->ultimaAcao->setLink( "JavaScript:Insere();" );
$obLista->ultimaAcao->addCampo( "2", "cod_moeda" );
$obLista->ultimaAcao->addCampo( "1", "descricao_singular" );
$obLista->ultimaAcao->addCampo( "3", "simbolo" );
$obLista->commitAcao();
$obLista->show();

$obHdnCampoNom = new Hidden;
$obHdnCampoNom->setName( "campoNom" );
$obHdnCampoNom->setValue( $campoNom );

$obHdnCampoNum = new Hidden;
$obHdnCampoNum->setName( "campoNum" );
$obHdnCampoNum->setValue( $campoNum );

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
