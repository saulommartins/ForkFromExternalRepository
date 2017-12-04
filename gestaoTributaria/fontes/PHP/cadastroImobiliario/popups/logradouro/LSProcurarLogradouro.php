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
    * Página de lista para o cadastro de logradouro
    * Data de Criação   : 08/09/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Fábio Bertoldi Rodrigues
                             Gustavo Passos Tourinho
                             Cassiano de Vasconcelos Ferreira

    * @ignore

    * $Id: LSProcurarLogradouro.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.04
*/

//include_once("../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php");
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once(CAM_GT_CIM_NEGOCIO."RCIMTrecho.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ProcurarLogradouro";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FMManterLogradouro.php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$acao   = Sessao::read('acao');
$modulo = Sessao::read('modulo');

Sessao::write('acao', "783");
Sessao::write('modulo', "0");

$stCaminho = CAM_GT_CIM_POPUPS."logradouro/";

$obRCIMTrecho = new RCIMTrecho;

//MANTEM FILTRO E PAGINACAO
$link = Sessao::read('link');
if (isset($_REQUEST['campoNom'])) { Sessao::write('campoNom',$_REQUEST['campoNom']);} else { $_REQUEST['campoNom'] = $link['campoNom'];}
if (isset($_REQUEST['campoNum'])) { Sessao::write('campoNum',$_REQUEST['campoNum']);} else { $_REQUEST['campoNum'] = $link['campoNum'];}

$stLink .= "&stAcao=".$_REQUEST['stAcao']."&acao=$acao";

if ($_GET["pg"] and  $_GET["pos"]) {
    $link["pg"]  = $_GET["pg"];
    $link["pos"] = $_GET["pos"];
}

$arFiltro = $_REQUEST;

$stErro = $_REQUEST['stErro'];
//USADO QUANDO EXISTIR FILTRO
//NA FL O VAR LINK DEVE SER RESETADA
if ( is_array($link) ) {
    $arFiltro = $link;
} elseif (Sessao::read('stLink') != '') {
    $arLink = explode('&', Sessao::read('stLink'));
    $arFiltro = array();
    foreach ($arLink AS $inChave => $stValor) {
        $arParametros = explode("=",$stValor);
        $arFiltro[$arParametros[0]] = $arParametros[1];
    }
} else {
    foreach ($_REQUEST as $key => $valor) {
        $link[$key] = $valor;
    }
}

include_once( $pgJs );
Sessao::write('link', $link);
Sessao::write('stLink', $stLink);

if ($arFiltro['campoNom']) {
    $stLink .= "&campoNom=".$arFiltro["campoNom"];}
if ($arFiltro['campoNum']) {
    $stLink .= "&campoNum=".$arFiltro["campoNum"];}
if ($arFiltro['nomForm']) {
    $stLink .= "&nomForm=".$arFiltro["nomForm"];}

//MONTA OS FILTROS
if ($arFiltro["inCodigoBairro"]) {
    $obRCIMTrecho->setBairro( $arFiltro["inCodigoBairro"] );
    $stLink .= "&inCodigoBairro=".$arFiltro["inCodigoBairro"];
}
if ($arFiltro["stCEP"]) {
    $obRCIMTrecho->setCEP( $arFiltro["stCEP"] );
    $stLink .= "&stCEP=".$arFiltro["stCEP"];
}

if ($arFiltro["inCodigoLogradouro"]) {
    $obRCIMTrecho->setCodigoLogradouro( $arFiltro["inCodigoLogradouro"] );
    $stLink .= "&inCodigoLogradouro=".$arFiltro["inCodigoLogradouro"];
}
if ($arFiltro["stNomeLogradouro"]) {
    $obRCIMTrecho->setNomeLogradouro( $arFiltro["stNomeLogradouro"] );
    $stLink .= "&stNomeLogradouro=".$arFiltro["stNomeLogradouro"];
}
if ($arFiltro["inCodigoUF"]) {
    $obRCIMTrecho->setCodigoUF( $arFiltro["inCodigoUF"] );
    $stLink .= "&inCodigoUF=".$arFiltro["inCodigoUF"];
}
if ($arFiltro["inCodigoMunicipio"]) {
    $obRCIMTrecho->setCodigoMunicipio( $arFiltro["inCodigoMunicipio"] );
    $stLink .= "&inCodigoMunicipio=".$arFiltro["inCodigoMunicipio"];
}
if ($arFiltro["stCadastro"]) {
    $stLink .= "&stCadastro=".$arFiltro["stCadastro"];
}

$obRCIMTrecho->listarLogradourosTrecho( $rsLista, "", $arFiltro["inCodPais"] );
Sessao::write('stLink', $stLink);

//DEFINICAO DA LISTA
$obLista = new Lista;
$obLista->obPaginacao->setFiltro( "&stLink=".$stLink );

$obLista->setRecordSet( $rsLista );
$obLista->setTitulo ( "Registros de Logradouro" );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Código ");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Nome do Logradouro" );
$obLista->ultimoCabecalho->setWidth( 30 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Nome do Bairro" );
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Município" );
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "CEP" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "cod_logradouro" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "tipo_nome" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_bairro" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "[sigla_uf] - [nom_municipio]" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "cep" );
$obLista->commitDado();

// Define ACOES
$obLista->addAcao();
$_REQUEST['stAcao'] = "alterar";
$obLista->ultimaAcao->setAcao  ( $_REQUEST['stAcao'] );
$obLista->ultimaAcao->addCampo ( "&inCodigoLogradouro", "cod_logradouro" );
$obLista->ultimaAcao->addCampo ( "&inCodigoTipo"      , "cod_tipo"       );
$obLista->ultimaAcao->addCampo ( "&stNomeLogradouro"  , "nom_logradouro" );
$obLista->ultimaAcao->addCampo ( "&inCodigoUF"        , "cod_uf"         );
$obLista->ultimaAcao->addCampo ( "&inCodigoMunicipio" , "cod_municipio"  );
$obLista->ultimaAcao->addCampo ( "&stNomeUF"          , "nom_uf"         );
$obLista->ultimaAcao->addCampo ( "&stNomeMunicipio"   , "nom_municipio"  );

$obLista->ultimaAcao->setLink  ( $pgForm."?".Sessao::getId().$stLink."&stAcao=".$_REQUEST['stAcao'] );
$obLista->commitAcao();

$obLista->addAcao();
$_REQUEST['stAcao'] = "remover";

Sessao::write('acao_generica', 'Excluir Logradouro');

$obLista->ultimaAcao->setAcao  ( $_REQUEST['stAcao'] );
$obLista->ultimaAcao->addCampo ( "&inCodigoLogradouro", "cod_logradouro"                 );
$obLista->ultimaAcao->addCampo ( "&inCodigoUF"        , "cod_uf"                         );
$obLista->ultimaAcao->addCampo ( "&inCodigoMunicipio" , "cod_municipio"                  );
$obLista->ultimaAcao->addCampo ( "&stNomeLogradouro"  , "tipo_nome"." "."nom_logradouro" );
$obLista->ultimaAcao->addCampo ( "&stDescQuestao"     , "[cod_logradouro] - [tipo_nome]" );
$obLista->ultimaAcao->setLink  ( $stCaminho.$pgProc."?".Sessao::getId().$stLink."&stAcao=".$_REQUEST['stAcao'] );
$obLista->commitAcao();

/*
$obLista->addAcao();
$stAcao = "renomear";
$obLista->ultimaAcao->setAcao  ( $stAcao );
$obLista->ultimaAcao->addCampo ( "&inCodigoLogradouro", "cod_logradouro" );
$obLista->ultimaAcao->addCampo ( "&inCodigoTipo"      , "cod_tipo"       );
$obLista->ultimaAcao->addCampo ( "&stNomeLogradouro"  , "nom_logradouro" );
$obLista->ultimaAcao->addCampo ( "&inCodigoUF"        , "cod_uf"         );
$obLista->ultimaAcao->addCampo ( "&inCodigoMunicipio" , "cod_municipio"  );
$obLista->ultimaAcao->addCampo ( "&stNomeUF"          , "nom_uf"         );
$obLista->ultimaAcao->addCampo ( "&stNomeMunicipio"   , "nom_municipio"  );
$obLista->ultimaAcao->addCampo ( "&tipoNome"          , "tipo_nome"      );
$obLista->ultimaAcao->setLink  ( $pgForm."?".Sessao::getId().$stLink."&stAcao=$stAcao" );
$obLista->commitAcao();
*/

$obLista->addAcao();
$_REQUEST['stAcao'] = "selecionar";
$obLista->ultimaAcao->setAcao   ( $_REQUEST['stAcao'] );
$obLista->ultimaAcao->setFuncao ( true );
$obLista->ultimaAcao->addCampo  ( "&inCodigoLogradouro", "cod_logradouro" );
$obLista->ultimaAcao->addCampo  ( "&stNomeLogradouro"  , "tipo_nome"      );

if ($_REQUEST["stCadastro"]<>"") {
    $stCadastro=$_REQUEST["stCadastro"];
} elseif ($link["stCadastro"]<>"") {
    $stCadastro=$link["stCadastro"];
} elseif ($arFiltro["stCadastro"] <>"") {
    $stCadastro=$arFiltro["stCadastro"];
} else {
    $stCadastro="";
}

if ($stCadastro == "trecho") {
    $obLista->ultimaAcao->addCampo ( "&inProxSequencia" , "prox_sequencia" );
    $obLista->ultimaAcao->setLink  ( "Javascript:preencheCamposTrecho()");
} elseif ($stCadastro == "imovel") {
    $obLista->ultimaAcao->addCampo ( "&inCodigoUF" , "cod_uf" );
    $obLista->ultimaAcao->addCampo ( "&inCodigoMunicipio" , "cod_municipio" );
    $obLista->ultimaAcao->setLink  ( "Javascript:preencheCamposImovel()");
} elseif ($stCadastro == "Cgm") {
    $obLista->ultimaAcao->addCampo ( "&stNomeMunicipio" , "municipio_nome" );
    $obLista->ultimaAcao->addCampo ( "&stNomeUF" , "uf_nome" );
    $obLista->ultimaAcao->addCampo ( "&inCodigoUF" , "cod_uf" );
    $obLista->ultimaAcao->addCampo ( "&inCodigoMunicipio" , "cod_municipio" );
    $obLista->ultimaAcao->addCampo ( "&inCodigoBairro" , "cod_bairro" );
    $obLista->ultimaAcao->addCampo ( "&stCEP" , "cep" );
    $obLista->ultimaAcao->setLink  ( "Javascript:preencheCamposCgm()");
} elseif ($stCadastro == "CgmCorresp") {
    $obLista->ultimaAcao->addCampo ( "&stNomeMunicipio" , "municipio_nome" );
    $obLista->ultimaAcao->addCampo ( "&stNomeUF" , "uf_nome" );
    $obLista->ultimaAcao->addCampo ( "&inCodigoUF" , "cod_uf" );
    $obLista->ultimaAcao->addCampo ( "&inCodigoMunicipio" , "cod_municipio" );
    $obLista->ultimaAcao->addCampo ( "&inCodigoBairro" , "cod_bairro" );
    $obLista->ultimaAcao->addCampo ( "&stCEP" , "cep" );
    $obLista->ultimaAcao->setLink  ( "Javascript:preencheCamposCgmCorresp()");
} else {
    $obLista->ultimaAcao->setLink  ("Javascript:preencheCampos()");
}

$obLista->commitAcao();
$obLista->show();

$inCodigoUF = $_REQUEST["inCodigoUF"];
$inCodigoMunicipio = $_REQUEST["inCodigoMunicipio"];

//DEFINICAO DOS COMPONETES
$obHdnCampoNom = new Hidden;
$obHdnCampoNom->setName( "campoNom" );
$obHdnCampoNom->setValue( $_REQUEST['campoNom'] );

$obHdnCampoNum = new Hidden;
$obHdnCampoNum->setName( "campoNum" );
$obHdnCampoNum->setValue( $_REQUEST['campoNum'] );

$obHdnCodUF = new Hidden;
$obHdnCodUF->setName     ( "inCodigoUF"       );
$obHdnCodUF->setValue    ( $inCodigoUF        );

$obHdnCadastro = new Hidden;
$obHdnCadastro->setName  ( "stCadastro" );
$obHdnCadastro->setValue ( $_REQUEST["stCadastro"]  );

$obHdnPais = new Hidden;
$obHdnPais->setName  ( "inCodPais" );
$obHdnPais->setValue ( $_REQUEST["inCodPais"] );

$obHdnCodMunicipio = new Hidden;
$obHdnCodMunicipio->setName  ( "inCodigoMunicipio" );
$obHdnCodMunicipio->setValue ( $inCodigoMunicipio  );

// DEFINE BOTOES
$obBtnIncluir = new Button;
$obBtnIncluir->setName              ( "btnIncluir"   );
$obBtnIncluir->setValue             ( "Incluir Novo" );
$obBtnIncluir->setTipo              ( "button"       );
$obBtnIncluir->obEvento->setOnClick ( "incluir();"   );
$obBtnIncluir->setDisabled          ( false          );

$obBtnFiltro = new Button;
$obBtnFiltro->setName              ( "btnFiltrar" );
$obBtnFiltro->setValue             ( "Filtrar"    );
$obBtnFiltro->setTipo              ( "button"     );
$obBtnFiltro->obEvento->setOnClick ( "filtrar();" );
$obBtnFiltro->setDisabled          ( false        );

$obBtnFechar = new Button;
$obBtnFechar->setName              ( "btnFechar" );
$obBtnFechar->setValue             ( "Fechar"    );
$obBtnFechar->setTipo              ( "button"    );
$obBtnFechar->obEvento->setOnClick ( "fechar();" );
$obBtnFechar->setDisabled          ( false       );

$botoes = array ($obBtnFiltro);
//$botoes = array ($obBtnIncluir, $obBtnFiltro);
//$botoes = array ($obBtnIncluir, $obBtnFiltro, $obBtnFechar);

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addHidden   ( $obHdnCampoNom );
$obFormulario->addHidden   ( $obHdnCampoNum );
$obFormulario->addHidden   ( $obHdnCodUF        );
$obFormulario->addHidden   ( $obHdnCodMunicipio );
$obFormulario->addHidden   ( $obHdnCadastro     );
$obFormulario->addHidden   ( $obHdnPais );
$obFormulario->defineBarra ($botoes,'left',''   );
//$obFormulario->obJavaScript->addFuncao( $stFncJavaScript );
$obFormulario->show();

//DEFINICAO DO IFRAME MENSAGEM
$obIFrame = new IFrame;
$obIFrame->setName("telaMensagem");
$obIFrame->setWidth("100%");
$obIFrame->setHeight("200");
$obIFrame->show();

if ($stErro) {
    sistemaLegado::exibeAviso(str_replace( "\n", "", $stErro ),"n_excluir","erro");
}

Sessao::write('acao',   $acao  );
Sessao::write('modulo', $modulo);

?>
