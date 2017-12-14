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
    * Página de Formulario de Consulta Nota Avulsa

    * Data de Criação: 02/09/2008

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato
    * @ignore

    * $Id: LSConsultarNotaAvulsa.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.03.19

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRNotaAvulsa.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ConsultarNotaAvulsa";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
if ( empty( $_REQUEST['stAcao'] ) ) {
    $_REQUEST['stAcao'] = "alterar";
}

$link = Sessao::read( "link" );
//MANTEM FILTRO E PAGINACAO
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

Sessao::write('link', $link);
Sessao::write('stLink', $stLink);

//MONTA O FILTRO
$stFiltro = "";
if ($_REQUEST["stExercicio"]) {
    $stFiltro .= " carne.exercicio = '".$_REQUEST["stExercicio"]."' AND ";
}

if ($_REQUEST["inInscricaoEconomica"]) {
    $stFiltro .= " cadastro_economico_calculo.inscricao_economica = ".$_REQUEST["inInscricaoEconomica"]." AND ";
}

if ($_REQUEST["inCGM"]) {
    $stFiltro .= " coalesce ( cadastro_economico_empresa_fato.numcgm, cadastro_economico_autonomo.numcgm, cadastro_economico_empresa_direito.numcgm ) = ".$_REQUEST["inCGM"]." AND ";
}

if ($stFiltro) {
    $stFiltro = " WHERE ".substr( $stFiltro, 0, -4 );
}

$obTARRNotaAvulsa = new TARRNotaAvulsa;
$obTARRNotaAvulsa->recuperaConsultaNotaAvulsa( $rsLista, $stFiltro );
$stLink .= "&stAcao=".$_REQUEST['stAcao'];

Sessao::write( 'ListaNotasAvulsas', $rsLista );

$arDados = $rsLista->getElementos();
$arDadosTEMP = array();
for ( $inX=0; $inX<count( $arDados ); $inX++ ) {
    $boIncluir = true;
    for ( $inY=0; $inY<count( $arDadosTEMP ); $inY++ ) {
        if (
            ( $arDadosTEMP[$inY]["inscricao_economica"] == $arDados[$inX]["inscricao_economica"] ) &&
            ( $arDadosTEMP[$inY]["cod_modalidade"] == $arDados[$inX]["cod_modalidade"] ) &&
            ( $arDadosTEMP[$inY]["competencia"] == $arDados[$inX]["competencia"] ) &&
            ( $arDadosTEMP[$inY]["nro_serie"] == $arDados[$inX]["nro_serie"] ) &&
            ( $arDadosTEMP[$inY]["nro_nota"] == $arDados[$inX]["nro_nota"] )
        ) {
            $boIncluir = false;
            break;
        }
    }

    if ($boIncluir) {
        $arDadosTEMP[] = array(
            "inscricao_economica" => $arDados[$inX]["inscricao_economica"],
            "cod_modalidade" => $arDados[$inX]["cod_modalidade"],
            "descricao_modalidade" => $arDados[$inX]["descricao_modalidade"],
            "competencia" => $arDados[$inX]["competencia"],
            "numcgm_prestador" => $arDados[$inX]["numcgm_prestador"],
            "nomcgm_prestador" => $arDados[$inX]["nomcgm_prestador"],
            "nro_nota" => $arDados[$inX]["nro_nota"],
            "nro_serie" => $arDados[$inX]["nro_serie"]
        );
    }
}

$rsListaDados = new RecordSet;
$rsListaDados->preenche($arDadosTEMP);

$obLista = new Lista;
$obLista->setRecordSet( $rsListaDados );
$obLista->setTitulo("Registros da Nota Avulsa");

$obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Inscrição Econômica");
    $obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Contribuinte");
    $obLista->ultimoCabecalho->setWidth( 40 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Modalidade");
    $obLista->ultimoCabecalho->setWidth( 30 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Competência");
    $obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Série/Nota");
    $obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
    $obLista->ultimoDado->setCampo( "inscricao_economica" );
$obLista->commitDado();

$obLista->addDado();
    $obLista->ultimoDado->setCampo( "[numcgm_prestador] - [nomcgm_prestador]" );
$obLista->commitDado();

$obLista->addDado();
    $obLista->ultimoDado->setCampo( "[cod_modalidade] - [descricao_modalidade]" );
$obLista->commitDado();

$obLista->addDado();
    $obLista->ultimoDado->setCampo( "competencia" );
$obLista->commitDado();

$obLista->addDado();
    $obLista->ultimoDado->setCampo( "[nro_serie]/[nro_nota]" );
$obLista->commitDado();

$obLista->addAcao();
    $obLista->ultimaAcao->setAcao( $_REQUEST['stAcao'] );
    $obLista->ultimaAcao->addCampo( "&inInscricaoEconomica", "inscricao_economica" );
    $obLista->ultimaAcao->addCampo( "&inCodModalidade", "cod_modalidade" );
    $obLista->ultimaAcao->addCampo( "&stCompetencia", "competencia" );
    $obLista->ultimaAcao->addCampo( "&inNroSerie", "nro_serie" );
    $obLista->ultimaAcao->addCampo( "&inNroNota", "nro_nota" );
    $obLista->ultimaAcao->setLink( $pgForm."?".Sessao::getId().$stLink );
$obLista->commitAcao();

$obLista->show();

$obFormulario = new Formulario;
$obFormulario->setAjuda  ( "UC-05.03.19" );
$obFormulario->show();
