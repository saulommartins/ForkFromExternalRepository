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
  * Página de Listao para popup de credito
  * Data de criação : 01/06/2005

    * @author Analista: Fabio Bertold Rodrigues
    * @author Programador: Lucas Teixeira Stephanou

    * $Id: LSProcurarFormulaCalculo.php 59612 2014-09-02 12:00:51Z gelson $

    Caso de uso: uc-05.03.05
**/

/*
$Log$
Revision 1.7  2006/09/15 11:51:01  fabio
corrigidas tags de caso de uso

Revision 1.6  2006/09/15 10:50:51  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GT_MON_NEGOCIO."RMONCredito.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "FormulaCalculo";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once( $pgJS );

$obRegra   = new RMONCredito;

$stFiltro = "";

$stLink .= "&stAcao=".$stAcao;
$stLink .= "&campoNom=".$_REQUEST["campoNom"];
$stLink .= "&campoNum=".$_REQUEST["campoNum"];
$stLink .= "&nomForm=".$_REQUEST["nomForm"];

//MANTEM FILTRO E PAGINACAO
if ($_GET["pg"] and  $_GET["pos"]) {
    $stLink .= "&pg=".$_GET["pg"];
    $stLink .= "&pos=".$_GET["pos"];
}
// FILTRAGEM

if ($_REQUEST["stCodCredito"]) {
    $obRegra->setCodCredito( $_REQUEST["stCodCredito"] );
    $stLink .= "&stCodCredito=".$_REQUEST["stCodCredito"];
}
if ($_REQUEST["stDescCredito"]) {
    $obRegra->setDescricao( $_REQUEST["stDescCredito"] );
    $stLink .= "&stDescCredito=".$_REQUEST["stDescCredito"];
}
if ($_REQUEST["stCodEspecie"]) {
    $obRegra->setCodEspecie( $_REQUEST["stCodEspecie"] );
    $stLink .= "&stCodEspecie=".$_REQUEST["stCodEspecie"];
}
if ($_REQUEST["stCodGenero"]) {
    $obRegra->setCodGenero( $_REQUEST["stCodGenero"] );
    $stLink .= "&stCodGenero=".$_REQUEST["stCodGenero"];
}
if ($_REQUEST["stCodNatureza"]) {
    $obRegra->setCodNatureza( $_REQUEST["stCodNatureza"] );
    $stLink .= "&stCodNatureza=".$_REQUEST["stCodNatureza"];
}

$obRegra->listarCreditos($rsLista, $boTransacao );

$obLista = new Lista;
$obLista->obPaginacao->setFiltro( $stLink );

$obLista->setRecordSet( $rsLista );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Crédito");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Descrição do Crédito" );
$obLista->ultimoCabecalho->setWidth( 60 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Espécie" );
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Genêro" );
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Natureza" );
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_credito" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "descricao_credito" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_especie" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_genero" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_natureza" );
$obLista->commitDado();

$obLista->addAcao();

$stAcao = "SELECIONAR";
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->setFuncao( true );
$obLista->ultimaAcao->setLink( "JavaScript:Insere();" );
$obLista->ultimaAcao->addCampo("5","cod_credito");
$obLista->ultimaAcao->addCampo("4","cod_especie");
$obLista->ultimaAcao->addCampo("3","cod_genero");
$obLista->ultimaAcao->addCampo("2","cod_natureza");
$obLista->ultimaAcao->addCampo("1","descricao_credito");
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
$obFormulario->defineBarra ($botoes, 'left', '' );
$obFormulario->show();
