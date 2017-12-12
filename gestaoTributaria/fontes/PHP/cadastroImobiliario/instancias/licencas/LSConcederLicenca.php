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

  * Página de Lista de Licença
  * Data de criação : 09/04/2008

    * @author Analista: Fábio Bertoldi
    * @author Programador: Fernando Piccini Cercato

    * $Id: LSConcederLicenca.php 59845 2014-09-15 19:32:00Z carolina $

  Caso de uso: uc-05.01.28
**/


include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMLicenca.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ConcederLicenca";
$pgForm1 = "FM".$stPrograma."Imoveis.php";
$pgForm2 = "FM".$stPrograma."Lotes.php";
$pgForm3 = "FM".$stPrograma."Edificacao.php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php?".Sessao::getId();
$pgJS   = "JS".$stPrograma.".js";

include_once ( $pgJS );

$stCaminho   = CAM_GT_CIM_INSTANCIAS."licencas/";
//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');

if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

//MANTEM FILTRO E PAGINACAO
$stLink .= "&stAcao=".$stAcao;
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

Sessao::write('link', $stLink);

//MONTAGEM DO FILTRO
$stFiltro = '';
if ($_REQUEST['stLicenca']) {
    $arLicenca = explode( "/", $_REQUEST['stLicenca'] );
    $stFiltro .= " \n licenca.cod_licenca = ".$arLicenca[0]." AND licenca.exercicio = ".$arLicenca[1]." AND ";
}

if ($_REQUEST["inCodImovel"]) {
    $stFiltro .= " \n licenca_imovel.inscricao_municipal = ".$_REQUEST["inCodImovel"]." AND ";
}

if ($_REQUEST["stChaveLocalizacao"]) {
    $stFiltro .= " \n localizacao.codigo_composto = '".$_REQUEST["stChaveLocalizacao"]."' AND ";
}

if ($_REQUEST["cmbLotes"]) {
    $stFiltro .= " \n licenca_lote.cod_lote = ".$_REQUEST["cmbLotes"]." AND ";
}

if ($_REQUEST["inTipoLicenca"]) {
    $stFiltro .= " \n licenca.cod_tipo = ".$_REQUEST["inTipoLicenca"]." AND ";
    $stLink .= "&inTipoLicenca=".$_REQUEST["inTipoLicenca"];
}

Sessao::write('stLink', $stLink);

if ($_REQUEST['stAcao'] != "cancelar") { //quando nao for cancelar deve eliminar da lista as licencas na tabela da baixa
    $stFiltro .= " \n ((licenca_baixa.cod_licenca IS NULL) OR ( licenca_baixa.dt_termino IS NOT NULL  AND licenca_baixa.dt_termino <= now() )) AND ";
} else {
    $stFiltro .= " \n licenca_baixa.cod_licenca IS NOT NULL AND licenca_baixa.cod_tipo = 2 AND licenca_baixa.dt_termino IS NULL  AND ";
}

if ( $stFiltro )
    $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );

$obTCIMLicenca = new TCIMLicenca;
$obTCIMLicenca->filtroListaLicencas( $rsListaLicenca, $stFiltro );

$obLista = new Lista;
$obLista->setRecordSet( $rsListaLicenca );

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Licença");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Tipo de Licença" );
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Origem" );
$obLista->ultimoCabecalho->setWidth( 30 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "[cod_licenca]/[exercicio]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "[cod_tipo]-[nom_tipo]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "inscricao" );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );

$obLista->ultimaAcao->addCampo( "&inCodLicenca", "cod_licenca" );
$obLista->ultimaAcao->addCampo( "&inExercicio", "exercicio" );
$obLista->ultimaAcao->addCampo( "&inCodInscriao", "inscricao" );
$obLista->ultimaAcao->addCampo( "&inAreaImovel", "area_imovel" );
$obLista->ultimaAcao->addCampo( "&stProcesso", "processo" );
$obLista->ultimaAcao->addCampo( "&stNomeTipo", "nom_tipo" );
$obLista->ultimaAcao->addCampo( "&inTipoNovaEdificacao", "tipo_nova_edificacao" );
$obLista->ultimaAcao->addCampo( "&stObservacao", "observacao" );
$obLista->ultimaAcao->addCampo( "&dtInicio", "dt_inicio" );
$obLista->ultimaAcao->addCampo( "&dtTermino", "dt_termino" );
$obLista->ultimaAcao->addCampo( "&stNomeTipoEdificacao", "nome_tipo_edificacao" );
$obLista->ultimaAcao->addCampo( "&inAreaEdificacao", "area_edificacao" );
$obLista->ultimaAcao->addCampo( "&inCodConstrucao", "cod_construcao" );
$obLista->ultimaAcao->addCampo( "&stCodComposto", "codigo_composto" );
$obLista->ultimaAcao->addCampo( "&stNomLocalizacao", "nom_localizacao" );
$obLista->ultimaAcao->addCampo( "&inNroLote", "nro_lote" );
$obLista->ultimaAcao->addCampo( "&inCodLote", "cod_lote" );
$obLista->ultimaAcao->addCampo( "&stAreaLote", "area_lote" );
$obLista->ultimaAcao->addCampo( "&stDescQuestao", "[cod_licenca]/[exercicio]" );
$obLista->ultimaAcao->addCampo( "&stDescricao", "[descricao]" );
$obLista->ultimaAcao->addCampo( "&inCodConstrucaoOutros", "[cod_construcao_outros]" );

if ($stAcao == "excluir") {
    $obLista->ultimaAcao->setLink( $stCaminho.$pgProc."?".Sessao::getId().$stLink );
} else {
    if ( $_REQUEST["inTipoLicenca"] == 1 )
        $obLista->ultimaAcao->setLink( $pgForm1."?".Sessao::getId().$stLink );
    else
        if ( ( $_REQUEST["inTipoLicenca"] >= 2 ) && ( $_REQUEST["inTipoLicenca"] <= 6 ) )
            $obLista->ultimaAcao->setLink( $pgForm3."?".Sessao::getId().$stLink );
        else
            $obLista->ultimaAcao->setLink( $pgForm2."?".Sessao::getId().$stLink );
}

$obLista->commitAcao();

$obLista->show();
