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
    * Lista para Bairro
    * Data de CriaÃ§Ã£o   : 24/09/2004
    * @author Analista: Ricardo Lopes
    * @author Desenvolvedor: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
    * @package URBEM
    * @subpackage Regra

    * @ignore

    * $Id: LSProcurarBairro.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.05

*/

/*
$Log$
Revision 1.6  2006/09/15 15:03:51  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLogradouro.class.php" );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMBairro.class.php"     );

//Define o nome dos arquivos PHP
$stPrograma = "ProcurarBairro";
$pgFilt     = "FL".$stPrograma.".php";
$pgFMMLogr  = "../logradouro/FMManterLogradouro.php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FMManterBairro.php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

$stCaminho = "../popups/popups/bairroSistema/";

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

//DEFINE LISTA
$obRCIMBairro = new RCIMBairro;

//MANTEM FILTRO E PAGINACAO
if ($_GET["pg"] and  $_GET["pos"]) {
    $link["pg"]  = $_GET["pg"];
    $link["pos"] = $_GET["pos"];
}

include_once( $pgJs );

//DEFINICAO DO FILTRO PARA CONSULTA
$stLink = "";

$stLink .= "&stAcao=".$_REQUEST["stAcao"];
if ($_REQUEST["inCodigoBairro"]) {
    $obRCIMBairro->setCodigoBairro( $_REQUEST["inCodigoBairro"] );
    $stLink .= "&inCodigoBairro=".$_REQUEST["inCodigoBairro"];
}
if ($_REQUEST["stNomBairro"]) {
    $obRCIMBairro->setNomeBairro( $_REQUEST["stNomBairro"] );
    $stLink .= "&stNomBairro=".$_REQUEST["stNomBairro"];
}
if ($_REQUEST["inCodUF"]) {
    $obRCIMBairro->setCodigoUF( $_REQUEST["inCodUF"] );
    $stLink .= "&inCodUF=".$_REQUEST["inCodUF"];
}
if ($_REQUEST["inCodMunicipio"]) {
    $obRCIMBairro->setCodigoMunicipio( $_REQUEST["inCodMunicipio"] );
    $stLink .= "&inCodMunicipio=".$_REQUEST["inCodMunicipio"];
}
if ($_REQUEST["campoNum"]) {
    $stLink .= "&campoNum=".$_REQUEST["campoNum"];
}
if ($_REQUEST["campoNom"]) {
    $stLink .= "&campoNom=".$_REQUEST["campoNom"];
}

Sessao::write('stLink', $stLink);
$obRCIMBairro->listarBairros( $rsLista );

//DEFINICAO DA LISTA
$obLista = new Lista;
$obLista->obPaginacao->setFiltro("&stLink=".$stLink );
$obLista->setRecordSet( $rsLista );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("C&oacute;digo ");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Nome" );
$obLista->ultimoCabecalho->setWidth( 28 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Munic&iacute;pio" );
$obLista->ultimoCabecalho->setWidth( 30 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("UF");
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 12 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_bairro" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_bairro" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_municipio" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_uf" );
$obLista->commitDado();

// Define ACOES
$obLista->addAcao();
$stAcao = "selecionar";
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->setFuncao ( true );
$obLista->ultimaAcao->addCampo("1", "cod_uf"        );
$obLista->ultimaAcao->addCampo("2", "cod_municipio" );
$obLista->ultimaAcao->addCampo("3", "cod_bairro"    );
$obLista->ultimaAcao->addCampo("4", "nom_bairro"    );
$obLista->ultimaAcao->setLink( "javascript: preencheBairro()" );
$obLista->commitAcao();

$obLista->show();

// DEFINE BOTOES
$obBtnFiltro = new Button;
$obBtnFiltro->setName               ( "botaoFiltrar" );
$obBtnFiltro->setValue              ( "Filtro"       );
$obBtnFiltro->setTipo               ( "button"       );
$obBtnFiltro->obEvento->setOnClick  ( "filtrar();"   );
$obBtnFiltro->setDisabled           ( false          );

$obBtnFechar = new Button;
$obBtnFechar->setName               ( "botaoFechar"  );
$obBtnFechar->setValue              ( "Fechar"       );
$obBtnFechar->setTipo               ( "button"       );
$obBtnFechar->obEvento->setOnClick  ( "fechar();"    );
$obBtnFechar->setDisabled           ( false          );

$arBotoes = array ( $obBtnFiltro );
//$arBotoes = array ( $obBtnFiltro, $obBtnFechar);

//DEFINE FORMULARIO
$obFormulario = new Formulario;

$obHdnCodUF = new Hidden;
$obHdnCodUF->setName         ( "inCodUF"           );
$obHdnCodUF->setValue        ( $_REQUEST["inCodUF"] );

$obHdnCodMunicipio = new Hidden;
$obHdnCodMunicipio->setName  ( "inCodMunicipio"    );
$obHdnCodMunicipio->setValue ( $_REQUEST["inCodMunicipio"] );

$obHdnCampoCodigo = new Hidden;
$obHdnCampoCodigo->setName  ( "campoNum" );
$obHdnCampoCodigo->setValue ( $_REQUEST["campoNum"] );

$obHdbCampoDescricao = new Hidden;
$obHdbCampoDescricao->setName  ( "campoNom" );
$obHdbCampoDescricao->setValue ( $_REQUEST["campoNom"] );

$obFormulario->addHidden     ( $obHdnCodUF          );
$obFormulario->addHidden     ( $obHdnCodMunicipio   );
$obFormulario->addHidden     ( $obHdnCampoCodigo    );
$obFormulario->addHidden     ( $obHdbCampoDescricao );

$obFormulario->defineBarra($arBotoes,'','');
$obFormulario->obJavaScript->addFuncao( $stFncJavaScript );

$obFormulario->show();
?>
