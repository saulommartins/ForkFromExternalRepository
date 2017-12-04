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
    * Formulário
    * Data de Criação: 19/01/2009

    * @author Desenvolvedor: Rafael Garbin

    * Casos de uso: uc-04.08.14

    $Id:
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GRH_IMA_MAPEAMENTO."TIMACodigoDirf.class.php" );

$stPrograma = "ProcurarRetencaoDIRF";
$pgFilt = "FL".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

//MANTEM FILTRO E PAGINACAO
$arLink = Sessao::read("link");
$stLink .= "&stAcao=".$stAcao;
if ($_GET["pg"] and  $_GET["pos"]) {
    $stLink.= "&pg=".$_GET["pg"]."&pos=".$_GET["pos"];
    $arLink["pg"]  = $_GET["pg"];
    $arLink["pos"] = $_GET["pos"];
}
//USADO QUANDO EXISTIR FILTRO
//NA FL O VAR LINK DEVE SER RESETADA
if ( is_array($arLink) ) {
    $_REQUEST = $arLink;
} else {
    foreach ($_REQUEST as $key => $valor) {
        $arLink[$key] = $valor;
    }
}
Sessao::write("link",$arLink);

// Define javascript que retorna os dados ao formulário
$stFncJavaScript .= " function insereRetencaoDIRF(num,nom,cod) {  \n";
$stFncJavaScript .= "       var   sNum;                                \n";
$stFncJavaScript .= "       var   sNom;                                \n";
$stFncJavaScript .= "       sNum = num;                                \n";
$stFncJavaScript .= "       sNom = nom;                                \n";
$stFncJavaScript .= "       window.opener.parent.frames['telaPrincipal'].document.getElementById('".$_REQUEST["campoNom"]."').innerHTML = sNom; \n";
$stFncJavaScript .= "       window.opener.parent.frames['telaPrincipal'].document.frm.".$_REQUEST["campoNum"].".value = sNum;                   \n";
$stFncJavaScript .= "       window.opener.parent.frames['telaPrincipal'].document.frm.".$_REQUEST["campoNum"].".focus();                        \n";
$stFncJavaScript .= "       window.close();            \n";
$stFncJavaScript .= " }                                \n";

// Recupera dados da lista
if ($_REQUEST["inCodDIRF"] != "") {
    $stFiltro .= " AND cod_dirf = ".$_REQUEST["inCodDIRF"];
}
if ($_REQUEST["stDescricaoDIRF"] != "") {
    $stFiltro .= " AND descricao ilike '%".trim($_REQUEST["stDescricaoDIRF"])."%'";
}
if ($_REQUEST["inExercicio"] != "") {
    $stFiltro .= " AND exercicio = '".$_REQUEST["inExercicio"]."'";
}
if ($_REQUEST["stTipoPrestador"] != "") {
    $stFiltro .= " AND tipo = '".trim($_REQUEST["stTipoPrestador"])."'";
}

if ($stFiltro != "") {
    $stFiltro = " WHERE ".substr($stFiltro,4,strlen($stFiltro));
}
$obTIMACodigoDirf = new TIMACodigoDirf();
$obTIMACodigoDirf->recuperaCodigosDIRF($rsLista,$stFiltro, " Order By descricao ");

// Monta lista
$obLista = new Lista;
$obLista->obPaginacao->setFiltro("&stLink=".$stLink );
$obLista->setRecordSet( $rsLista );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Código");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Descrição" );
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Tipo de Prestador" );
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_dirf" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "descricao" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "tipo_formatado" );
$obLista->commitDado();

$stAcao = "SELECIONAR";
$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->setFuncao( true );
$obLista->ultimaAcao->setLink( "JavaScript:insereRetencaoDIRF();" );
$obLista->ultimaAcao->addCampo("1","cod_dirf");
$obLista->ultimaAcao->addCampo("2","descricao");
$obLista->commitAcao();

$obLista->show();

$obFormulario = new Formulario;
$obFormulario->obJavaScript->addFuncao( $stFncJavaScript );
$obFormulario->show();
?>
