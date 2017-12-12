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
    * Página de lista para o cadastro de trecho
    * Data de Criação   : 02/09/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Gustavo Passos Tourinho

    * @ignore

    * $Id: LSManterTrecho.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.06
*/

/*
$Log$
Revision 1.9  2006/09/18 10:31:51  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include(CAM_GT_CIM_NEGOCIO."RCIMTrecho.class.php");

/**
    * Define o nome dos arquivos PHP
*/

$stPrograma = "ManterTrecho";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgFormCaracteristica = "FM".$stPrograma."Caracteristica.php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$stFormBaixa = "FM".$stPrograma."Baixa.php";

//$stCaminho = "../modulos/cadastroImobiliario/trecho/";
$stCaminho = CAM_GT_CIM_INSTANCIAS."trecho/";

$obRCIMTrecho  = new RCIMTrecho;
$rsListaTrecho = new RecordSet;

/**
    *Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
*/
$link = Sessao::read('link');

if ( empty( $_REQUEST['stAcao'] ) ) {
    $_REQUEST['stAcao'] = "alterar";
}

/**
    * Define arquivos PHP para cada ação
*/

switch ($_REQUEST['stAcao']) {
    case 'alterar'   : $pgProx = $pgForm; break;
    case 'reativar'  :
    case 'baixar'    : $pgProx = $stFormBaixa; break;
    case 'excluir'   : $pgProx = $pgProc; break;
    case 'historico' : $pgProx = $pgFormCaracteristica; break;
    DEFAULT          : $pgProx = $pgForm;
}

//MANTEM FILTRO E PAGINACAO
$stLink .= "&stAcao=".$_REQUEST['stAcao'];
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

Sessao::write('link'  , $link);
Sessao::write('stLink', $stLink);

if ($_REQUEST['inNumLogradouro']) {
    $obRCIMTrecho->setCodigoLogradouro ( $_REQUEST['inNumLogradouro'] );
    $stLink .= '&inNumLogradouro='.$_REQUEST['setCodigoLogradouro'];
}
if ($_REQUEST['inCodSequencia']) {
    $obRCIMTrecho->setSequencia( $_REQUEST['inCodSequencia'] );
    $stLink .= '&inCodSequencia='.$_REQUEST['inCodSequencia'];
}

if ($_REQUEST['stAcao'] == "reativar") {
    $obRCIMTrecho->verificaBaixaTrecho( $rsListaTrecho );
} else {
    $obRCIMTrecho->listarTrechos( $rsListaTrecho );
    if ( $rsListaTrecho->eof() && $_REQUEST['inCodSequencia'] && $_REQUEST['inNumLogradouro'] ) {
        $obRCIMTrecho->verificaBaixaTrecho( $rsListaTrechoBaixado );
        if ( !$rsListaTrechoBaixado->eof() ) {
                $stJs = "alertaAviso('@Trecho baixado. (".$_REQUEST["inNumLogradouro"].".".$_REQUEST["inCodSequencia"].")','form','erro','".Sessao::getId()."');";

            SistemaLegado::executaFrameOculto($stJs);
        }
    }
}

$rsListaTrecho->addFormatacao( "extensao", "NUMERIC_BR" );

$stLink .= "&stAcao=".$_REQUEST['stAcao'];

/**
    * InstÃ¢ncia o OBJETO Lista
*/
Sessao::write('stLink', $stLink);

$obLista = new Lista;
$obLista->obPaginacao->setFiltro("&stLink=".$stLink );
$obLista->setRecordSet( $rsListaTrecho );
$obLista->setTitulo ("Registros de Trecho");
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Código ");
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Logradouro" );
$obLista->ultimoCabecalho->setWidth( 60 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Extensão" );
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

// campos codigo e logradouro sao montados no SQL
$obLista->addDado();
$obLista->ultimoDado->setCampo( "[cod_logradouro].[sequencia]" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "[nom_tipo] [nom_logradouro]" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->ultimoDado->setCampo( "extensao" );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $_REQUEST['stAcao'] );

$obLista->ultimaAcao->addCampo("&inSequencia"       , "sequencia"        );
$obLista->ultimaAcao->addCampo("&stNomTipo"         , "nom_tipo"         );
$obLista->ultimaAcao->addCampo("&stNomeLogradouro"   , "[nom_tipo] [nom_logradouro]" );
$obLista->ultimaAcao->addCampo("&flExtensao"        , "extensao"         );
$obLista->ultimaAcao->addCampo("&inCodTrecho"       , "cod_trecho"       );
$obLista->ultimaAcao->addCampo("&inCodLogradouro"   , "cod_logradouro"   );
$obLista->ultimaAcao->addCampo("&stDescQuestao"     , "[cod_logradouro].[sequencia]-[nom_tipo] [nom_logradouro]" );
if ($_REQUEST['stAcao'] == "reativar") {
    $obLista->ultimaAcao->addCampo("&stDTInicio", "dt_inicio" );
    $obLista->ultimaAcao->addCampo("&stJustificativa", "justificativa" );
    $obLista->ultimaAcao->addCampo("&stTimeStamp", "timestamp" );
}

if ($_REQUEST['stAcao'] == "excluir") {
    $obLista->ultimaAcao->setLink( $stCaminho.$pgProx."?".Sessao::getId().$stLink );
} else {
    $obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink );
}
$obLista->commitAcao();
$obLista->show();

$obFormulario = new Formulario;
$obFormulario->setAjuda  ( "UC-05.01.06" );
$obFormulario->show();

?>
