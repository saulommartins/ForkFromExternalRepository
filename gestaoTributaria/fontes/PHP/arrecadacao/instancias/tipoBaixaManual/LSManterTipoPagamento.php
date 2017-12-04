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
    * Lista para Edificação
    * Data de Criação   : 17/11/2004
    * @@author Analista: Ricardo Lopes
    * @@author Desenvolvedor: Fabio Bertoldi Rodrigues
    * @@package URBEM
    * @@subpackage Regra

    * $Id: LSManterTipoPagamento.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.03.09
*/

/*
$Log$
Revision 1.3  2006/09/15 11:19:33  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_ARR_NEGOCIO."RCIMEdificacao.class.php" );
include_once ( CAM_GT_ARR_NEGOCIO."RCIMImovel.class.php"     );

//Define o nome dos arquivos PHP
$stPrograma    = "ManterEdificacao";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgForm        = "FM".$stPrograma.".php";
$pgFormVinculo = "FM".$stPrograma."Vinculo.php";
$pgFormAlterar = "FM".$stPrograma."Alteracao.php";
$pgFormReforma = "FM".$stPrograma."Reforma.php";
$pgFormCaracteristica = "FM".$stPrograma."Caracteristica.php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJs          = "JS".$stPrograma.".js";
include_once( $pgJs );

$stCaminho = CAM_GT_ARR_INSTANCIAS."tipoBaixaManual/";

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

//DEFINE LISTA
$obRCIMEdificacao = new RCIMEdificacao;
$rsLista          = new RecordSet;

//MANTEM FILTRO E PAGINACAO
$link = Sessao::read( "link" );
$link["boVinculoEdificacao"] = $_REQUEST["boVinculoEdificacao"];
$stLink .= "&stAcao=".$stAcao;
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

Sessao::write( "link", $link );
//DEFINICAO DO FILTRO PARA CONSULTA
$stLink = "";
if ($_REQUEST["inCodigoConstrucao"]) {
    $obRCIMEdificacao->setCodigoConstrucao( $_REQUEST["inCodigoConstrucao"] );
    $stLink .= "&inCodigoConstrucao=".$_REQUEST["inCodigoConstrucao"];
}
if ($_REQUEST["inCodigoTipoEdificacao"]) {
    $obRCIMEdificacao->setCodigoTipo( $_REQUEST["inCodigoTipoEdificacao"] );
    $stLink .= "&inCodigoConstrucao=".$_REQUEST["inCodigoConstrucao"];
}
if ($_REQUEST["boVinculoEdificacao"]) {
    $obRCIMEdificacao->setTipoVinculo( $_REQUEST["boVinculoEdificacao"] );
    $stLink .= "&boVinculoEdificacao=".$_REQUEST["boVinculoEdificacao"];
}
if ($_REQUEST["inInscricaoMunicipal"]) {
    $obRCIMEdificacao->obRCIMImovel->setNumeroInscricao( $_REQUEST["inInscricaoMunicipal"] );
    $stLink .= "&inInscricaoMunicipal=".$_REQUEST["inInscricaoMunicipal"];
}
if ($_REQUEST["inCodigoCondominio"]) {
    $obRCIMEdificacao->obRCIMCondominio->setCodigoCondominio( $_REQUEST["inCodigoCondominio"] );
    $stLink .= "&inCodigoCondominio=".$_REQUEST["inCodigoCondominio"];
}

if ($_REQUEST["stNumeroLote"]) {
    $obRCIMEdificacao->obRCIMImovel->roRCIMLote->setNumeroLote( $_REQUEST["stNumeroLote"] );
    $stLink .= "&stNumeroLote=".$_REQUEST["stNumeroLote"];
}

if ( $_REQUEST[ "inCodLocalizacao_".( $_REQUEST["inNumNiveis"] - 1 ) ] ) {
    $arCodigoLocalizacao = explode( "-", $_REQUEST[ "inCodLocalizacao_".( $_REQUEST["inNumNiveis"] - 1 ) ] );
    $inCodigoLocalizacao = $arCodigoLocalizacao[1];
    $stLink .= "&inCodLocalizacao_".( $_REQUEST["inNumNiveis"] - 1 )."=".$_REQUEST[ "inCodLocalizacao_".( $_REQUEST["inNumNiveis"] - 1 ) ];
    $stLink .= "&inNumNiveis=".$_REQUEST["inNumNiveis"];
    $obRCIMEdificacao->obRCIMImovel->roRCIMLote->obRCIMLocalizacao->setCodigoLocalizacao( $inCodigoLocalizacao );
}

if ($_REQUEST["boVinculoEdificacao"] == "Condomínio") {
    $obRCIMEdificacao->listarEdificacoes( $rsLista );
} else {
    $obRCIMEdificacao->listarEdificacoesImovel( $rsLista );
}

$stLink .= "&stAcao=".$stAcao;

$rsLista->addFormatacao( "area_real", "NUMERIC_BR" );

//DEFINICAO DA LISTA
$obLista = new Lista;
$obLista->obPaginacao->setFiltro("&stLink=".$stLink );
$obLista->setRecordSet( $rsLista );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Código ");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
if ($_REQUEST["boVinculoEdificacao"]== "Imóvel") {
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Localização");
    $obLista->ultimoCabecalho->setWidth( 15 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Lote");
    $obLista->ultimoCabecalho->setWidth( 15 );
    $obLista->commitCabecalho();
}
$obLista->addCabecalho();
if ($_REQUEST["boVinculoEdificacao"] == "Condomínio") {
    $obLista->ultimoCabecalho->addConteudo("Condomínio");
} elseif ($_REQUEST["boVinculoEdificacao"] == "Imóvel") {
    $obLista->ultimoCabecalho->addConteudo("Imóvel");
}
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Tipo" );
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Área" );
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_construcao" );
$obLista->commitDado();
if ($_REQUEST["boVinculoEdificacao"]== "Imóvel") {
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "valor_composto" );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "cod_lote" );
    $obLista->commitDado();
}
$obLista->addDado();
if ($_REQUEST["boVinculoEdificacao"] == "Condomínio") {
    $obLista->ultimoDado->setCampo( "nom_condominio"    );
} elseif ($_REQUEST["boVinculoEdificacao"] == "Imóvel") {
    $obLista->ultimoDado->setCampo( "inscricao_municipal"    );
}
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_tipo"       );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "area_real"      );
$obLista->commitDado();

// Define ACOES
if ($stAcao == "alterar") {
    $obLista->addAcao();
    $stAcao = "alterar";
    $obLista->ultimaAcao->setAcao( $stAcao );
    $obLista->ultimaAcao->addCampo("&inCodigoConstrucao"      , "cod_construcao"     );
    $obLista->ultimaAcao->addCampo("&stTipoEdificacao"        , "nom_tipo"           );
    $obLista->ultimaAcao->addCampo("&inCodigoTipo"            , "cod_tipo"           );
    $obLista->ultimaAcao->addCampo("&stImovelCond"            , "imovel_cond"        );
    $obLista->ultimaAcao->addCampo("&stTipoUnidade"           , "tipo_vinculo"       );
    $obLista->ultimaAcao->addCampo("&hdnVinculoEdificacao"    , "tipo_vinculo"       );
    $obLista->ultimaAcao->addCampo("&flAreaConstruida"        , "area_real"          );
    $obLista->ultimaAcao->addCampo("&flAreaUnidade"           , "area_unidade"       );
    $obLista->ultimaAcao->addCampo("&stNumero"                , "numero"             );
    $obLista->ultimaAcao->addCampo("&stComplemento"           , "complemento"        );
    $obLista->ultimaAcao->addCampo("&inCodigoProcesso"        , "cod_processo"       );
    $obLista->ultimaAcao->addCampo("&hdnAnoExercicioProcesso" , "exercicio"          );
    $obLista->ultimaAcao->setLink( $pgFormAlterar."?".Sessao::getId().$stLink."&stAcao=".$stAcao );
    $obLista->commitAcao();
} elseif ($stAcao == "incluir") {
    $obLista->addAcao();
    $stAcao = "selecionar";
    $obLista->ultimaAcao->setAcao( $stAcao );
    $obLista->ultimaAcao->addCampo("&inCodigoConstrucao"      , "cod_construcao"     );
    $obLista->ultimaAcao->addCampo("&flAreaConstruida"        , "area_real"          );
    $obLista->ultimaAcao->setLink( $pgFormVinculo."?".Sessao::getId().$stLink."&stAcao=incluir" );
    $obLista->commitAcao();
} elseif ($stAcao == "excluir") {
    $obLista->addAcao();
    $stAcao = "excluir";
    $obLista->ultimaAcao->setAcao( $stAcao );
    $obLista->ultimaAcao->addCampo("&inCodigoConstrucao"      , "cod_construcao"     );
    $obLista->ultimaAcao->addCampo("&stTipoEdificacao"        , "nom_tipo"           );
    $obLista->ultimaAcao->addCampo("&inCodigoTipo"            , "cod_tipo"           );
    $obLista->ultimaAcao->addCampo("&stImovelCond"            , "imovel_cond"        );
    $obLista->ultimaAcao->addCampo("&stTipoUnidade"           , "tipo_vinculo"       );
    $obLista->ultimaAcao->addCampo("&hdnVinculoEdificacao"    , "tipo_vinculo"       );
    $obLista->ultimaAcao->addCampo("&flAreaConstruida"        , "area_real"          );
    $obLista->ultimaAcao->addCampo("&flAreaUnidade"           , "area_unidade"       );
    $obLista->ultimaAcao->addCampo("&stNumero"                , "numero"             );
    $obLista->ultimaAcao->addCampo("&stComplemento"           , "complemento"        );
    $obLista->ultimaAcao->addCampo("&inCodigoProcesso"        , "cod_processo"       );
    $obLista->ultimaAcao->addCampo("&hdnAnoExercicioProcesso" , "exercicio"          );
    $obLista->ultimaAcao->addCampo("&stDescQuestao"           , "[cod_construcao]"   );
    $obLista->ultimaAcao->setLink( $stCaminho.$pgProc."?".Sessao::getId().$stLink."&stAcao=".$stAcao );
    $obLista->commitAcao();
} elseif ($stAcao == "baixar") {
    $obLista->addAcao();
    $stAcao = "baixar";
    $obLista->ultimaAcao->setAcao( $stAcao );
    $obLista->ultimaAcao->addCampo("&inCodigoConstrucao" , "cod_construcao"          );
    $obLista->ultimaAcao->addCampo("&stTipoEdificacao"   , "nom_tipo"                );
    $obLista->ultimaAcao->addCampo("&stImovelCond"       , "imovel_cond"             );
    $obLista->ultimaAcao->addCampo("&stTipoUnidade"      , "tipo_vinculo"            );
    $obLista->ultimaAcao->addCampo("&inCodigoTipo"       , "cod_tipo"                );
    $obLista->ultimaAcao->addCampo("&stNumero"           , "numero"                  );
    $obLista->ultimaAcao->addCampo("&stComplemento"      , "complemento"             );
    $obLista->ultimaAcao->addCampo("&flAreaConstruida"   , "area_real"               );
    $obLista->ultimaAcao->setLink( $pgFormVinculo."?".Sessao::getId().$stLink."&stAcao=".$stAcao );
    $obLista->commitAcao();
} elseif ($stAcao == "historico") {
    $obLista->addAcao();
    $stAcao = "historico";
    $obLista->ultimaAcao->setAcao( $stAcao );
    $obLista->ultimaAcao->addCampo("&inCodigoConstrucao"      , "cod_construcao"     );
    $obLista->ultimaAcao->addCampo("&stTipoEdificacao"        , "nom_tipo"           );
    $obLista->ultimaAcao->addCampo("&inCodigoTipo"            , "cod_tipo"           );
    $obLista->ultimaAcao->addCampo("&stImovelCond"            , "imovel_cond"        );
    $obLista->ultimaAcao->addCampo("&stTipoUnidade"           , "tipo_vinculo"       );
    $obLista->ultimaAcao->addCampo("&hdnVinculoEdificacao"    , "tipo_vinculo"       );
    $obLista->ultimaAcao->addCampo("&flAreaConstruida"        , "area_real"          );
    $obLista->ultimaAcao->addCampo("&flAreaUnidade"           , "area_unidade"       );
    $obLista->ultimaAcao->addCampo("&stNumero"                , "numero"             );
    $obLista->ultimaAcao->addCampo("&stComplemento"           , "complemento"        );
    $obLista->ultimaAcao->addCampo("&inCodigoProcesso"        , "cod_processo"       );
    $obLista->ultimaAcao->addCampo("&hdnAnoExercicioProcesso" , "exercicio"          );
    $obLista->ultimaAcao->setLink( $pgFormCaracteristica."?".Sessao::getId().$stLink."&stAcao=".$stAcao );
    $obLista->commitAcao();
} elseif ($stAcao == "reforma") {
    $obLista->addAcao();
    $stAcao = "reforma";
    $obLista->ultimaAcao->setAcao( $stAcao );
    $obLista->ultimaAcao->addCampo("&inCodigoConstrucao"      , "cod_construcao"            );
    $obLista->ultimaAcao->addCampo("&inCodigoConstrucaoAut"   , "cod_construcao_autonoma"   );
    $obLista->ultimaAcao->addCampo("&stTipoEdificacao"        , "nom_tipo"                  );
    $obLista->ultimaAcao->addCampo("&inCodigoTipo"            , "cod_tipo"                  );
    $obLista->ultimaAcao->addCampo("&stImovelCond"            , "imovel_cond"               );
    $obLista->ultimaAcao->addCampo("&stTipoUnidade"           , "tipo_vinculo"              );
    $obLista->ultimaAcao->addCampo("&hdnVinculoEdificacao"    , "tipo_vinculo"              );
    $obLista->ultimaAcao->addCampo("&stNumero"                , "numero"                    );
    $obLista->ultimaAcao->addCampo("&stComplemento"           , "complemento"               );
    $obLista->ultimaAcao->addCampo("&inCodigoProcesso"        , "cod_processo"              );
    $obLista->ultimaAcao->setLink( $pgFormReforma."?".Sessao::getId().$stLink."&stAcao=".$stAcao );
    $obLista->commitAcao();
}
$obLista->show();

?>
