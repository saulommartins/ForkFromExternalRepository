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
    * Data de Criação: 09/10/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Girardi dos Santos

    * @package URBEM
    * @subpackage

    $Id: LSManterAditivoContrato.php 65046 2016-04-20 14:10:18Z jean $

    * Casos de uso : uc-03.05.24
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(TLIC."TLicitacaoContrato.class.php");
include_once(TLIC."TLicitacaoContratoAditivos.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ManterAditivoContrato";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgProx = $pgForm;
$stCaminho = CAM_GP_LIC_INSTANCIAS."contrato/";

//Define a função do arquivo, ex: incluir, alterar, anular, etc
$stAcao = $request->get('stAcao');
$stLink = "&stAcao=".$stAcao;

if ($_REQUEST['inNumContrato']) {
   $stFiltro .= " contrato.numero_contrato = ". $_REQUEST['inNumContrato']." and ";
}
if ($_REQUEST['stExercicioContrato']) {
   $stFiltro .= " contrato.exercicio = '". $_REQUEST['stExercicioContrato']."' and ";
}
if ($_REQUEST['dtContrato']) {
   $stFiltro .= " contrato.dt_assinatura = to_date('". $_REQUEST['dtContrato']."','dd/mm/yyyy') and ";
}
if ($_REQUEST['inCodContratado']) {
   $stFiltro .= " contrato.cgm_contratado = ".$_REQUEST['inCodContratado']." and ";
}
if ($_REQUEST["inNumCGM"]) {
   $stFiltro .= " cgm_entidade.numcgm in (".implode(",", $_REQUEST["inNumCGM"]).") and ";
}

if ($stAcao == "alterar") {
    if ($_REQUEST["inNumeroAditivo"]) {
        $stFiltro .= " contrato_aditivos.num_aditivo = ".$_REQUEST["inNumeroAditivo"]." \n and ";
    }
    if ($_REQUEST["stExercioAditivo"]) {
        $stFiltro .= " contrato_aditivos.exercicio = '".$_REQUEST["stExercioAditivo"]."' \n and ";
    }
}

$stFiltro .=  " NOT EXISTS (SELECT 1 "
            ."\n          FROM licitacao.rescisao_contrato "
            ."\n          WHERE rescisao_contrato.exercicio_contrato = contrato.exercicio"
            ."\n          AND rescisao_contrato.cod_entidade = contrato.cod_entidade"
            ."\n          AND rescisao_contrato.num_contrato = contrato.num_contrato"
            ."\n         ) \n and ";

if ($stAcao != "incluir") {
    $stFiltro .=  " NOT EXISTS (SELECT 1"
    ."\n                  FROM licitacao.contrato_aditivos_anulacao"
    ."\n                  WHERE contrato_aditivos.exercicio_contrato = contrato_aditivos_anulacao.exercicio_contrato"
    ."\n                  AND contrato_aditivos.cod_entidade = contrato_aditivos_anulacao.cod_entidade"
    ."\n                  AND contrato_aditivos.num_contrato = contrato_aditivos_anulacao.num_contrato"
    ."\n                  AND contrato_aditivos.exercicio = contrato_aditivos_anulacao.exercicio"
    ."\n                  AND contrato_aditivos.num_aditivo = contrato_aditivos_anulacao.num_aditivo"
    ."\n                  ) and ";
}

$stFiltro = ($stFiltro)?' and '.substr($stFiltro,0,strlen($stFiltro)-4):'';

$rsLista = new RecordSet;
$obLista = new Lista;

if ($stAcao == 'incluir') {
    $obTLicitacaoContrato = new TLicitacaoContrato;
    $obTLicitacaoContrato->recuperaNaoAnuladosContratado($rsLista, $stFiltro );
} else {
    $obTLicitacaoContrato = new TLicitacaoContratoAditivos($rsLista, $stFiltro );
    $obTLicitacaoContrato->recuperaContratosAditivosLicitacao($rsLista, $stFiltro );
}

$obLista->obPaginacao->setFiltro("&stLink=".$stLink );
$rsLista->addFormatacao('valor_contratado', 'NUMERIC_BR');
$obLista->setRecordSet( $rsLista );
$obLista->setTitulo("Contrato cadastrados");
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Contrato" );
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();

if ($stAcao == "incluir") {
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Data do Contrato" );
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();
} else {
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Aditivo" );
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Data Aditivo" );
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();
}

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Contratado" );
$obLista->ultimoCabecalho->setWidth( 40 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();

//ADICIONAR DADOS

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "[numero_contrato]/[exercicio_contrato]" );
$obLista->commitDado();

if ($stAcao == "incluir") {
    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "dt_assinatura" );
    $obLista->commitDado();
} else {
    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "[num_aditivo]/[exercicio_aditivo]" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "dt_assinatura" );
    $obLista->commitDado();
}

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "[cgm_contratado] - [nom_cgm]" );
$obLista->commitDado();

$obLista->addAcao();
if ($stAcao == 'incluir') {
   $obLista->ultimaAcao->setAcao ( 'SELECIONAR' );
} elseif ($stAcao == 'alterar') {
   $obLista->ultimaAcao->setAcao ( 'ALTERAR' );
} else {
   $obLista->ultimaAcao->setAcao ( 'ANULAR' );
}
$obLista->ultimaAcao->addCampo( "&inNumContrato", "num_contrato" );
$obLista->ultimaAcao->addCampo( "&inCodEntidade", "cod_entidade" );
$obLista->ultimaAcao->addCampo( "&stExercicioContrato", "exercicio_contrato" );

if ($stAcao != 'incluir') {
    $obLista->ultimaAcao->addCampo( "&inNumeroAditivo", "num_aditivo" );
    $obLista->ultimaAcao->addCampo( "&stExercicioAditivo", "exercicio_aditivo" );
}
$obLista->ultimaAcao->setLink( $stCaminho.$pgProx."?".Sessao::getId().$stLink );
$obLista->commitAcao();

$obLista->show();
?>
