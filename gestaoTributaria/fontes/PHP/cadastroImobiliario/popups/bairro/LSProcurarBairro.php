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
    * Página de lista para o cadastro de bairro
    * Data de Criação   : 24/09/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Fábio Bertoldi Rodrigues

    * @ignore

    * $Id: LSProcurarBairro.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.05
*/

/*
$Log$
Revision 1.5  2006/10/27 18:39:00  dibueno
Correção Bug #7274

Revision 1.4  2006/09/15 15:03:47  fabio
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
include_once( $pgJs );

$stCaminho = "../popups/popups/bairro/";

//Define a funÃ§Ã£o do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');

if ($_REQUEST["stCtrl"] == "incluirNovoBairro") {
    $stAcaoLogradouro = $_REQUEST["stAcaoLogr"]."&inCodigoLogradouro=".$_REQUEST["inCodigoLogradouro"]."&tipoNome=".$_REQUEST["stNomeAntigo"];
}

if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

if ($_REQUEST["stErro"]) {
    $stErro = $_REQUEST["stErro"];
    exibeAviso($_REQUEST["stErro"],"n_alterar","erro");
}

//DEFINE LISTA
$obRCIMBairro = new RCIMBairro;

//MANTEM FILTRO E PAGINACAO
if ($_GET["pg"] and  $_GET["pos"]) {
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

//DEFINICAO DO FILTRO PARA CONSULTA
$stLink = "";
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
if ($_REQUEST["campoNom"]) {
    $stLink .= "&campoNom=".$_REQUEST["campoNom"];
}
if ($_REQUEST["campoNum"]) {
    $stLink .= "&campoNum=".$_REQUEST["campoNum"];
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
$stAcao = "alterar";
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->addCampo("&inCodBairro"     , "cod_bairro"     );
$obLista->ultimaAcao->addCampo("&stNomeBairro"    , "nom_bairro"     );
$obLista->ultimaAcao->addCampo("&inCodUF"         , "cod_uf"         );
$obLista->ultimaAcao->addCampo("&inCodMunicipio"  , "cod_municipio"  );
$obLista->ultimaAcao->addCampo("&stNomeUF"        , "nom_uf"         );
$obLista->ultimaAcao->addCampo("&stNomeMunicipio" , "nom_municipio"  );
$obLista->ultimaAcao->setLink( $pgForm."?".Sessao::getId().$stLink."&stAcao=".$stAcao );
$obLista->commitAcao();
$obLista->addAcao();
Sessao::write('acao_generica', 'Excluir Bairro');
$stAcao = "remover";
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->addCampo("&inCodBairro"    , "cod_bairro"    );
$obLista->ultimaAcao->addCampo("&stNomBairro"    , "nom_bairro"    );
$obLista->ultimaAcao->addCampo("&inCodUF"        , "cod_uf"        );
$obLista->ultimaAcao->addCampo("&inCodMunicipio" , "cod_municipio" );
$obLista->ultimaAcao->addCampo("&stDescQuestao"  , "[cod_bairro] - [nom_bairro]"    );
//$obLista->ultimaAcao->setLink("Javascript: alertaQuestaoPopUp('".$pgProc."','cod_bairro','".$inCodBairro."','pp_excluir','".Sessao::getId()."');" );
//$obLista->ultimaAcao->setLink( $pgProc."?".Sessao::getId().$stLink."&stAcao=".$stAcao );
$obLista->ultimaAcao->setLink( $stCaminho.$pgProc."?".Sessao::getId().$stLink."&stAcao=".$stAcao );
$obLista->commitAcao();

$obLista->addAcao();
$stAcao = "selecionar";
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->addCampo("&inCodigoBairro"    , "cod_bairro"    );
$obLista->ultimaAcao->addCampo("&inCodigoUF"        , "cod_uf"        );
$obLista->ultimaAcao->addCampo("&inCodigoMunicipio" , "cod_municipio" );
$obLista->ultimaAcao->setLink( $pgFMMLogr."?".Sessao::getId().$stLink."&stAcao=".$stAcaoLogradouro );
$obLista->commitAcao();

$obLista->show();

// DEFINE BOTOES
$obBtnIncluir = new Button;
$obBtnIncluir->setName              ( "botaoIncluir" );
$obBtnIncluir->setValue             ( "Incluir Novo" );
$obBtnIncluir->setTipo              ( "button"       );
$obBtnIncluir->obEvento->setOnClick ( "incluir();"   );
$obBtnIncluir->setDisabled          ( false          );

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

$arBotoes = array ($obBtnIncluir, $obBtnFiltro);
//$arBotoes = array ($obBtnIncluir, $obBtnFiltro, $obBtnFechar);

//DEFINE FORMULARIO
$obFormulario = new Formulario;

$obHdnCodUF = new Hidden;
$obHdnCodUF->setName         ( "inCodUF"           );
$obHdnCodUF->setValue        ( $_REQUEST["inCodUF"] );

$obHdnCodMunicipio = new Hidden;
$obHdnCodMunicipio->setName  ( "inCodMunicipio"    );
$obHdnCodMunicipio->setValue ( $_REQUEST["inCodMunicipio"] );

$obFormulario->addHidden     ( $obHdnCodUF         );
$obFormulario->addHidden     ( $obHdnCodMunicipio  );

$obFormulario->defineBarra($arBotoes,'','');
$obFormulario->obJavaScript->addFuncao( $stFncJavaScript );

$obFormulario->show();

//DEFINICAO DO IFRAME MENSAGEM
$obIFrame = new IFrame;
$obIFrame->setName("telaMensagem");
$obIFrame->setWidth("100%");
$obIFrame->setHeight("60");
$obIFrame->show();

if ($stErro) {
    exibeAviso(str_replace( "\n", "", $stErro ),"n_alterar","erro");
}
?>
