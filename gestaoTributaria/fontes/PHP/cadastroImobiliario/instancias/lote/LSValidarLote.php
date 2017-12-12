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
    * Página de lista para a validação de lotes desmembrados
    * Data de Criação   : 05/04/2005

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    * $Id: LSValidarLote.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.08
*/

/*
$Log$
Revision 1.8  2006/09/18 10:30:54  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLoteRural.class.php"        );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLoteUrbano.class.php"       );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMConfiguracao.class.php"     );
include_once ( CAM_GT_CIM_COMPONENTES."MontaLocalizacao.class.php" );

//Define o nome dos arquivos PHP
$stPrograma           = "ManterLote";
$pgFilt               = "FL".$stPrograma.".php";
$pgList               = "LS".$stPrograma.".php";
$pgForm               = "FM".$stPrograma.".php";
$pgFormAlt            = "FM".$stPrograma."Alteracao.php";
$pgFormBaixa          = "FM".$stPrograma."Baixa.php";
$pgFormCaracteristica = "FM".$stPrograma."Caracteristica.php";
$pgFormAglutinar      = "FM".$stPrograma."Aglutinar.php";
$pgProc               = "PR".$stPrograma.".php";
$pgOcul               = "OC".$stPrograma.".php";
$pgFormDesmembrar     = "FMDesmembrarLote.php";
$pgJs                 = "JS".$stPrograma.".js";
include_once( $pgJs );

$stCaminho = "../modulos/cadastroImobiliario/lote/";

/**
    *Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
*/

$stAcao = "validar";

//MASCARA PROCESSO
$obRCIMConfiguracao = new RCIMConfiguracao;
$obRCIMConfiguracao->setCodigoModulo( 12 );
$obRCIMConfiguracao->setAnoExercicio( Sessao::getExercicio() );
$obRCIMConfiguracao->consultarConfiguracao();
$obRCIMConfiguracao->consultarMascaraLote( $stMascaraLote );
$arConfrontacoesSessao = array();
Sessao::write('confrontacoes', $arConfrontacoesSessao);

//[funcionalidade] => 178 ->Lote Urbano  193 ->Lote Rural
if ($_REQUEST["funcionalidade"] == 178) {
    $obRCIMLote = new RCIMLoteUrbano;
    Sessao::write('acao',  898);
} elseif ($_REQUEST["funcionalidade"] == 193) {
    $obRCIMLote = new RCIMLoteRural;
    Sessao::write('acao', 899);
}
$stLink = "&funcionalidade=".$_REQUEST["funcionalidade"];

//MANTEM FILTRO E PAGINACAO
$stLink .= "&stAcao=".$stAcao;
if ($_GET["pg"] and  $_GET["pos"]) {
    $link["pg"]  = $_GET["pg"];
    $link["pos"] = $_GET["pos"];
}

Sessao::write('link', $link);
Sessao::write('stLink', $stLink);
/*
//USADO QUANDO EXISTIR FILTRO
//NA FL O VAR LINK DEVE SER RESETADA
if ( is_array($sessao->link) ) {
    $_REQUEST = $sessao->link;
} else {
    foreach ($_REQUEST as $key => $valor) {
        $sessao->link[$key] = $valor;
    }
}

$arCodigoLocalizacao = explode( "-", $_REQUEST[ "inCodLocalizacao_".( $_REQUEST["inNumNiveis"] - 1 ) ] );
$inCodigoLocalizacao = $arCodigoLocalizacao[1];
if ($inCodigoLocalizacao) {
    $obRCIMLote->obRCIMLocalizacao->setCodigoLocalizacao( $inCodigoLocalizacao );
}

if ($_REQUEST["stNumeroLote"]) {
    $obRCIMLote->setNumeroLote( $_REQUEST["stNumeroLote"] );
}
*/

$obRCIMLote->listarLotesDesmembramento( $rsListaLote );
$rsListaLote->addStrPad( "valor", strlen( $stMascaraLote ), "0" );

/**
    * InstÃ¢ncia o OBJETO Lista
*/

$obLista = new Lista;
$obLista->obPaginacao->setFiltro("&stLink=".$stLink );
$obLista->setRecordSet( $rsListaLote );
$obLista->setTitulo ("Registros de lote desmembrado");
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Localização" );
$obLista->ultimoCabecalho->setWidth( 40 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Lote" );
$obLista->ultimoCabecalho->setWidth( 50 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

// campos codigo e logradouro sao montados no SQL
$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "valor_composto" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "valor" );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );

$obLista->ultimaAcao->addCampo("&inCodigoLote"          , "cod_lote"        );
$obLista->ultimaAcao->addCampo("&inCodigoLocalizacao"   , "cod_localizacao" );
$obLista->ultimaAcao->addCampo("&inCodigoBairro"        , "cod_bairro"      );
$obLista->ultimaAcao->addCampo("&inCidigoUF"            , "cod_uf"          );
$obLista->ultimaAcao->addCampo("&inCodigoMunicipio"     , "cod_municipio"   );
$obLista->ultimaAcao->addCampo("&inNumProcesso"         , "cod_processo"    );
$obLista->ultimaAcao->addCampo("&stExercicio"           , "ano_exercicio"   );
$obLista->ultimaAcao->addCampo("&inCodigoGrandeza"      , "cod_grandeza"    );
$obLista->ultimaAcao->addCampo("&inCodigoUnidade"       , "cod_unidade"     );
$obLista->ultimaAcao->addCampo("&inCodigoParcelamento"  , "cod_parcelamento");
$obLista->ultimaAcao->addCampo("&stDescQuestao"         , "valor"           );

$obLista->ultimaAcao->setLink( $pgFormAlt."?".Sessao::getId().$stLink."&stOrigem=validar" );

$obLista->commitAcao();
$obLista->show();

$obFormulario = new Formulario;
$obFormulario->setAjuda  ( "UC-05.01.08" );
$obFormulario->show();

?>
