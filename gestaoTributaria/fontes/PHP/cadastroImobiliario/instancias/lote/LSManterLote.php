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
    * Página de lista para o cadastro de lote
    * Data de Criação   : 30/12/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

    * @ignore

    * $Id: LSManterLote.php 65099 2016-04-25 17:37:56Z jean $

    * Casos de uso: uc-05.01.08
*/

/*
$Log$
Revision 1.12  2006/09/18 10:30:54  fabio
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
$pgProc               = "PR".$stPrograma.".php";
$pgOcul               = "OC".$stPrograma.".php";
$pgFormDesmembrar     = "FMDesmembrarLote.php";
$pgFormAglutinar      = "FM".$stPrograma."Aglutinar.php";
$pgJs                 = "JS".$stPrograma.".js";
include_once( $pgJs );

$stCaminho = CAM_GT_CIM_INSTANCIAS."lote/";
$arLoteSessao = array();
Sessao::write('lotes', $arLoteSessao);

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

/**
    *Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
*/

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

/**
    * Define arquivos PHP para cada ação
*/

switch ($stAcao) {
    case 'alterar'   : $pgProx = $pgFormAlt; break;
    case 'reativar':
    case 'baixar'    : $pgProx = $pgFormBaixa; break;
    case 'excluir'   : $pgProx = $pgProc; break;
    case 'historico' : $pgProx = $pgFormCaracteristica; break;
    case 'desmembrar': $pgProx = $pgFormDesmembrar; break;
    case 'aglutinar' : $pgProx = $pgFormAglutinar; break;
    DEFAULT          : $pgProx = $pgForm;
}

$arConfrontacoesSessao = array();
Sessao::write('confrontacoes', $arConfrontacoesSessao );

//[funcionalidade] => 178 ->Lote Urbano  193 ->Lote Rural
if ($_REQUEST["funcionalidade"] == 178) {
    $obRCIMLote = new RCIMLoteUrbano;
} elseif ($_REQUEST["funcionalidade"] == 193) {
    $obRCIMLote = new RCIMLoteRural;
}
$stLink = "&funcionalidade=".$request->get("funcionalidade")."&stChaveLocalizacao=".$request->get("stChaveLocalizacao")."&stNumeroLote=".$request->get("stNumeroLote");

//MANTEM FILTRO E PAGINACAO
$stLink .= "&stAcao=".$stAcao;
if ( $request->get("pg") and  $request->get("pos") ) {
    $link["pg"]  = $request->get("pg");
    $link["pos"] = $request->get("pos");
}

//USADO QUANDO EXISTIR FILTRO
$link = Sessao::read( 'link' );
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

include_once ( CAM_GT_CIM_NEGOCIO."RCIMLocalizacao.class.php"       );

$obRCIMLocalizacao = new RCIMLocalizacao;
$obRCIMLocalizacao->setValorComposto($request->get("stChaveLocalizacao"));
$obRCIMLocalizacao->consultaCodigoLocalizacao($inCodigoLocalizacao);

if ($inCodigoLocalizacao) {
    $obRCIMLote->obRCIMLocalizacao->setCodigoLocalizacao( $inCodigoLocalizacao );
}

if ( $request->get("stNumeroLote") ) {
    $obRCIMLote->setNumeroLote( $request->get("stNumeroLote") );
}

if ($stAcao == "reativar") {
    $obRCIMLote->verificaBaixaLote( $rsListaLote );
} else {
    $obRCIMLote->listarLotes( $rsListaLote );
    if ( $rsListaLote->eof() && $request->get("stNumeroLote") && $request->get("stChaveLocalizacao") ) {
        $obRCIMLote->verificaBaixaLote( $rsListaLoteBaixado );
        if ( !$rsListaLoteBaixado->eof()) {
            $stJs = "alertaAviso('@Lote baixado. (Localização: ".$request->get("stChaveLocalizacao")." Lote: ".$request->get("stNumeroLote").")','form','erro','".Sessao::getId()."');";

            SistemaLegado::executaFrameOculto($stJs);
        }
    }
}

//MASCARA PROCESSO
$obRCIMConfiguracao = new RCIMConfiguracao;
$obRCIMConfiguracao->setCodigoModulo( 12 );
$obRCIMConfiguracao->setAnoExercicio( Sessao::getExercicio() );
$obRCIMConfiguracao->consultarConfiguracao();
$obRCIMConfiguracao->consultarMascaraLote( $stMascaraLote );

//$stMascaraLote = "9999";

$rsListaLote->addStrPad( "valor", strlen( $stMascaraLote ), "0" );

/**
    * InstÃ¢ncia o OBJETO Lista
*/

$obLista = new Lista;
$obLista->obPaginacao->setFiltro("&stLink=".$stLink );
$obLista->setRecordSet( $rsListaLote );
$obLista->setTitulo ("Registros de lote");
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

$obLista->ultimaAcao->addCampo("&inCodigoLote"          , "cod_lote"            );
$obLista->ultimaAcao->addCampo("&inCodigoLocalizacao"   , "cod_localizacao"     );
$obLista->ultimaAcao->addCampo("&inCodigoGrandeza"      , "cod_grandeza"        );
$obLista->ultimaAcao->addCampo("&inCodigoUnidade"       , "cod_unidade"         );
$obLista->ultimaAcao->addCampo("&inCodigoBairro"        , "cod_bairro"          );
$obLista->ultimaAcao->addCampo("&inCodigoUF"            , "cod_uf"              );
$obLista->ultimaAcao->addCampo("&inCodigoMunicipio"     , "cod_municipio"       );
$obLista->ultimaAcao->addCampo("&inNumProcesso"         , "cod_processo"        );
$obLista->ultimaAcao->addCampo("&stExercicio"           , "ano_exercicio"       );
$obLista->ultimaAcao->addCampo("&stDescQuestao"         , "valor"               );
$obLista->ultimaAcao->addCampo("&dtDataInscricaoLote"   , "dt_inscricao"        );
$obLista->ultimaAcao->addCampo("&flAreaRealOrigem"      , "area_real"           );
$obLista->ultimaAcao->addCampo("&inCodigoUnidadeOrigem" , "cod_unidade"         );
$obLista->ultimaAcao->addCampo("&stValor"               , "valor"               );
$obLista->ultimaAcao->addCampo("&stValorComposto"       , "valor_composto"      );
$obLista->ultimaAcao->addCampo("&stNomLocalizacao"      , "nom_localizacao"     );

if ($stAcao == "reativar") { //dados necessarios para reativar
    $obLista->ultimaAcao->addCampo("&stTimestamp"       , "timestamp_baixa"     );
    $obLista->ultimaAcao->addCampo("&stDTInicio"        , "dt_inicio"           );
    $obLista->ultimaAcao->addCampo("&stJustificativa"   , "justificativa"       );
}

if ($stAcao == "excluir") {
    $obLista->ultimaAcao->setLink( $stCaminho.$pgProx."?".Sessao::getId().$stLink );
} else {
    $obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink );
}

$obLista->commitAcao();
$obLista->show();

$obFormulario = new Formulario;
$obFormulario->setAjuda  ( "UC-05.01.08" );
$obFormulario->show();

?>
