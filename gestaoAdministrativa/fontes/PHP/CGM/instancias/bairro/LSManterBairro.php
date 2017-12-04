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

    * Página de lista para o cadastro de bairro
    * Data de Criação   : 24/09/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Fábio Bertoldi Rodrigues

    * @ignore

    * $Id: LSProcurarBairro.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.05
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLogradouro.class.php" );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMBairro.class.php"     );

//Define o nome dos arquivos PHP
$stPrograma = "ManterBairro";
$pgFilt     = "FL".$stPrograma.".php";
$pgFMMLogr  = "../logradouro/FMManterLogradouro.php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

$stCaminho = CAM_GA_CGM_INSTANCIAS."/bairro/";

include_once( $pgJs );

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');

if ($request->get("stErro")) {
    $stErro = $request->get("stErro");
    SistemaLegado::exibeAviso($request->get("stErro"),"n_alterar","erro");
}

//DEFINE LISTA
$obRCIMBairro = new RCIMBairro;

//DEFINICAO DO FILTRO PARA CONSULTA
$stLink = "";
if ($request->get("inCodigoBairro")) {
    $obRCIMBairro->setCodigoBairro( $request->get("inCodigoBairro") );
    $stLink .= "&inCodigoBairro=".$request->get("inCodigoBairro");
}
if ($request->get("stNomBairro")) {
    $obRCIMBairro->setNomeBairro( $request->get("stNomBairro") );
    $stLink .= "&stNomBairro=".$request->get("stNomBairro");
}
if ($request->get("inCodUF")) {
    $obRCIMBairro->setCodigoUF( $request->get("inCodUF") );
    $stLink .= "&inCodUF=".$request->get("inCodUF");
}
if ($request->get("inCodMunicipio")) {
    $obRCIMBairro->setCodigoMunicipio( $request->get("inCodMunicipio") );
    $stLink .= "&inCodMunicipio=".$request->get("inCodMunicipio");
}
if ($request->get("campoNom")) {
    $stLink .= "&campoNom=".$request->get("campoNom");
}
if ($request->get("campoNum")) {
    $stLink .= "&campoNum=".$request->get("campoNum");
}

$stLink .= "&stAcao=".$stAcao;

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
switch ($stAcao) {
    case "alterar":
        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( $stAcao );
        $obLista->ultimaAcao->addCampo("&inCodBairro"     , "cod_bairro"     );
        $obLista->ultimaAcao->addCampo("&stNomeBairro"    , "nom_bairro"     );
        $obLista->ultimaAcao->addCampo("&inCodUF"         , "cod_uf"         );
        $obLista->ultimaAcao->addCampo("&inCodMunicipio"  , "cod_municipio"  );
        $obLista->ultimaAcao->addCampo("&stNomeUF"        , "nom_uf"         );
        $obLista->ultimaAcao->addCampo("&stNomeMunicipio" , "nom_municipio"  );
        $obLista->ultimaAcao->setLink( $pgForm."?".Sessao::getId().$stLink   );
        $obLista->commitAcao();
    break;

    case "excluir":
        Sessao::write('acao_generica', 'Excluir Bairro');
        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "REMOVER" );
        $obLista->ultimaAcao->addCampo("&inCodBairro"    , "cod_bairro"    );
        $obLista->ultimaAcao->addCampo("&stNomBairro"    , "nom_bairro"    );
        $obLista->ultimaAcao->addCampo("&inCodUF"        , "cod_uf"        );
        $obLista->ultimaAcao->addCampo("&inCodMunicipio" , "cod_municipio" );
        $obLista->ultimaAcao->addCampo("&stDescQuestao"  , "[cod_bairro] - [nom_bairro]"    );
        $obLista->ultimaAcao->setLink( $pgProc."?".Sessao::getId().$stLink );
        $obLista->commitAcao();        
    break;

    case "consultar":
        $obLista->addAcao();
        //$stAcao = "consultar";
        $obLista->ultimaAcao->setAcao( "CONSULTAR" );
        $obLista->ultimaAcao->addCampo("&inCodBairro"     , "cod_bairro"     );
        $obLista->ultimaAcao->addCampo("&stNomeBairro"    , "nom_bairro"     );
        $obLista->ultimaAcao->addCampo("&inCodUF"         , "cod_uf"         );
        $obLista->ultimaAcao->addCampo("&inCodMunicipio"  , "cod_municipio"  );
        $obLista->ultimaAcao->addCampo("&stNomeUF"        , "nom_uf"         );
        $obLista->ultimaAcao->addCampo("&stNomeMunicipio" , "nom_municipio"  );
        $obLista->ultimaAcao->setLink( $pgForm."?".Sessao::getId().$stLink   );
        $obLista->commitAcao();
    break;
}

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

//DEFINE FORMULARIO
$obFormulario = new Formulario;

$obHdnCodUF = new Hidden;
$obHdnCodUF->setName         ( "inCodUF"           );
$obHdnCodUF->setValue        ( $request->get("inCodUF") );

$obHdnCodMunicipio = new Hidden;
$obHdnCodMunicipio->setName  ( "inCodMunicipio"    );
$obHdnCodMunicipio->setValue ( $request->get("inCodMunicipio") );

$obFormulario->addHidden     ( $obHdnCodUF         );
$obFormulario->addHidden     ( $obHdnCodMunicipio  );

$obFormulario->defineBarra($arBotoes,'','');
$obFormulario->obJavaScript->addFuncao( $stFncJavaScript );

$obFormulario->show();

if ($stErro) {
    SistemaLegado::exibeAviso(str_replace( "\n", "", $stErro ),"n_alterar","erro");
}
?>
