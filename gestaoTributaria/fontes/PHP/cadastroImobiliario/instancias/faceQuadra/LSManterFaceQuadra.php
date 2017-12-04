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
    * Página de lista para o cadastro de face de quadra
    * Data de Criação   : 26/10/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @ignore

    * $Id: LSManterFaceQuadra.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.07
*/

/*
$Log$
Revision 1.9  2006/09/18 10:30:35  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GT_CIM_NEGOCIO."RCIMFaceQuadra.class.php" );

 //Define o nome dos arquivos PHP
 $stPrograma = "ManterFaceQuadra";
 $pgFilt      = "FL".$stPrograma.".php";
 $pgList      = "LS".$stPrograma.".php";
 $pgForm      = "FM".$stPrograma.".php";
 $pgProc      = "PR".$stPrograma.".php";
 $pgOcul      = "OC".$stPrograma.".php";
 $pgJS        = "JS".$stPrograma.".js";
 $pgFormBaixa = "FM".$stPrograma."Baixa.php";
 $pgFormCaracteristica = "FM".$stPrograma."Caracteristica.php";

 $stCaminho = CAM_GT_CIM_INSTANCIAS."faceQuadra/";
 //$stCaminho = "../modulos/cadastroImobiliario/faceQuadra/";

 $obRCIMFaceQuadra  = new RCIMFaceQuadra;

//Define arquivos PHP para cada acao

 switch ($_REQUEST['stAcao']) {
     case 'alterar'   : $pgProx = $pgForm; break;
     case 'reativar':
     case 'baixar'    : $pgProx = $pgFormBaixa; break;
     case 'excluir'   : $pgProx = $pgProc; break;
     case 'historico' : $pgProx = $pgFormCaracteristica; break;
     DEFAULT          : $pgProx = $pgForm;
 }

 //MANTEM FILTRO E PAGINACAO
$link = Sessao::read('link');
$stLink = Sessao::read('stLink');

 $stLink .= "&stAcao=".$_REQUEST['stAcao'];
 if ($_GET["pg"] and  $_GET["pos"]) {
     $stLink.= "&pg=".$_GET["pg"]."&pos=".$_GET["pos"];
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

 //MONTA O FILTRO
 if ($_REQUEST["inCodigoFace"]) {
     $obRCIMFaceQuadra->setCodigoFace( $_REQUEST["inCodigoFace"] );
 }

 if ($_REQUEST["stChaveLocalizacao"]) {
     $obRCIMFaceQuadra->obRCIMLocalizacao->setValorComposto( $_REQUEST["stChaveLocalizacao"] );
 }

 if ($_REQUEST["stNomeLogradouro"]) {
     $obRCIMFaceQuadra->obRCIMTrecho->setNomeLogradouro( $_REQUEST["stNomeLogradouro"] );
 }

if ($_REQUEST['stAcao'] == "reativar") {
    $obRCIMFaceQuadra->verificaBaixaFace( $rsListaFaceQuadra );
} else {
    $obRCIMFaceQuadra->listarFaceQuadra( $rsListaFaceQuadra );
    if ( $rsListaFaceQuadra->eof() && $_REQUEST["inCodigoFace"] ) { //nao encontrou nada, verificar se esta baixado
        $obRCIMFaceQuadra->verificaBaixaFace( $rsListaFaceQuadraBaixa );
        if ( !$rsListaFaceQuadraBaixa->eof()) {
            $stJs = "alertaAviso('@Face baixada. (".$_REQUEST["inCodigoFace"].")','form','erro','".Sessao::getId()."');";

            SistemaLegado::executaFrameOculto($stJs);
        }
    }
}

 $obLista = new Lista;
 $obLista->setRecordSet( $rsListaFaceQuadra );
 $obLista->addCabecalho();
 $obLista->ultimoCabecalho->addConteudo("&nbsp;");
 $obLista->ultimoCabecalho->setWidth( 5 );
 $obLista->commitCabecalho();
 $obLista->addCabecalho();
 $obLista->ultimoCabecalho->addConteudo("Localização");
 $obLista->ultimoCabecalho->setWidth( 15 );
 $obLista->commitCabecalho();
 $obLista->addCabecalho();
 $obLista->ultimoCabecalho->addConteudo( "Código da Face de Quadra" );
 $obLista->ultimoCabecalho->setWidth( 20 );
 $obLista->commitCabecalho();
 $obLista->addCabecalho();
 $obLista->ultimoCabecalho->addConteudo( "Nome da Localização" );
 $obLista->ultimoCabecalho->setWidth( 55 );
 $obLista->commitCabecalho();
 $obLista->addCabecalho();
 $obLista->ultimoCabecalho->addConteudo("&nbsp;");
 $obLista->ultimoCabecalho->setWidth( 5 );
 $obLista->commitCabecalho();

 $obLista->addDado();
 $obLista->ultimoDado->setCampo( "valor_composto" );
 $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
 $obLista->commitDado();
 $obLista->addDado();
 $obLista->ultimoDado->setCampo( "cod_face" );
 $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
 $obLista->commitDado();
 $obLista->addDado();
 $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
 $obLista->ultimoDado->setCampo( "nom_localizacao" );
 $obLista->commitDado();
 //$obLista->addAcao();

 $obLista->addAcao();
 $obLista->ultimaAcao->setAcao( $_REQUEST['stAcao'] );

 $obLista->ultimaAcao->addCampo("&inValorComposto"    , "valor_composto"      );
 $obLista->ultimaAcao->addCampo("&inCodigoFace"       , "cod_face"            );
 $obLista->ultimaAcao->addCampo("&stNomeLocalizacao"  , "nom_localizacao"     );
 $obLista->ultimaAcao->addCampo("&stDescQuestao"      , "[cod_face]-[nom_localizacao]" );
 $obLista->ultimaAcao->addCampo("&inCodigoLocalizacao", "cod_localizacao"     );
if ($_REQUEST['stAcao'] == "reativar") { //colocar aqui os dados necessarios para reativar
    $obLista->ultimaAcao->addCampo("&stDtInicio", "dt_inicio" );
    $obLista->ultimaAcao->addCampo("&stTimestamp", "timestamp" );
    $obLista->ultimaAcao->addCampo("&stJustificativa", "justificativa" );
}

 if ($_REQUEST['stAcao'] == "excluir") {
    $obLista->ultimaAcao->setLink( $stCaminho.$pgProx."?".Sessao::getId().$stLink );
 } else {
    $obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink );
 }

$obLista->commitAcao();
$obLista->show();

$obFormulario = new Formulario;
$obFormulario->setAjuda  ( "UC-05.01.07" );
$obFormulario->show();
