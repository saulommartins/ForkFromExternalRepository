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
    * Arquivo de instância para manutenção dos processos
    * Data de Criação: 27/09/2006

    * @author Analista: Cassiano de Vasconcellos Ferreira
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

    $Id: LSManterProcesso.php 59612 2014-09-02 12:00:51Z gelson $

    Casos de uso: uc-01.06.98
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GA_PROT_MAPEAMENTO."TProtocoloProcesso.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ManterProcesso";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$stAcao = $request->get('stAcao');

$stAcao = $stAcao ? $stAcao : 'alterar';
$stLink = "";
$stFiltro = "";

$stLink .= "&stAcao=".$stAcao;
$link = Sessao::read("link");
if ($_GET["pg"] and  $_GET["pos"]) {
    $stLink.= "&pg=".$_GET["pg"]."&pos=".$_GET["pos"];
    $link["pg"]  = $_GET["pg"];
    $link["pos"] = $_GET["pos"];
}
Sessao::write("link",$link);

if ( is_array(Sessao::read("link")) ) {
    $_REQUEST = Sessao::read("link");
} else {
    foreach ($_REQUEST as $key => $valor) {
        $link[$key] = $valor;
    }
    Sessao::write("link",$link);
}

$stFiltro  = "";

if ($_REQUEST['inCodProcesso']) {
    $inCodigoProcesso    = substr( $_REQUEST['inCodProcesso'], 0, strlen($_REQUEST['inCodProcesso']) - 5 );
    $inExercicioProcesso = substr( $_REQUEST['inCodProcesso'], strlen($_REQUEST['inCodProcesso']) - 4 );
    $stFiltro .= " AND cod_processo  = ".(int) $inCodigoProcesso;
    $stFiltro .= " AND ano_exercicio = '".$inExercicioProcesso."'";
}

$stFiltro .= ($_REQUEST['codClassificacao']) ? " AND cod_classificacao = ".$_REQUEST['codClassificacao'] : "";

$stFiltro .= ($_REQUEST['codAssunto']) ? " AND cod_assunto = ".$_REQUEST['codAssunto'] : "";

if ($_REQUEST['stHdnAssuntoReduzido']) {
    $stFiltro .= " AND lower(resumo_assunto) ";
    $stFiltro .= " like lower('".$_REQUEST['stHdnAssuntoReduzido']."') ";
}
if ($_REQUEST['numCgm']) {
    $stFiltro .= " AND numcgm = ".$_REQUEST['numCgm'];
}
if ($_REQUEST['dataInicio'] AND $_REQUEST['dataTermino']) {
    $stFiltro .= " AND to_date(inclusao, 'dd/mm/yyyy') between TO_DATE('".$_REQUEST['dataInicio']."','dd/mm/yyyy') AND ";
    $stFiltro .= " TO_DATE('".$_REQUEST['dataTermino']."','dd/mm/yyyy') ";
}
if ($_REQUEST['valorAtributoTxt']) {
    foreach ($_REQUEST['valorAtributoTxt'] as $key => $value) {
        if ($_REQUEST['valorAtributoTxt'][$key]) {
            $stFiltro .= " AND SW_ASSUNTO_ATRIBUTO_VALOR.valor ILIKE ( '%".$_REQUEST['valorAtributoTxt'][$key]."%' ) \n";
            $stFiltro .= " AND SW_ASSUNTO_ATRIBUTO_VALOR.cod_atributo = '".$key."' \n";
        }
    }
}
if ($_REQUEST['valorAtributoNum']) {
    foreach ($_REQUEST['valorAtributoNum'] as $key => $value) {
        if ($_REQUEST['valorAtributoNum'][$key]) {
            $stFiltro .= " AND SW_ASSUNTO_ATRIBUTO_VALOR.valor = '".$_REQUEST['valorAtributoNum'][$key]."' \n";
            $stFiltro .= " AND SW_ASSUNTO_ATRIBUTO_VALOR.cod_atributo = '".$key."' \n";
        }
    }
}
if ($_REQUEST['valorAtributoCmb']) {
    foreach ($_REQUEST['valorAtributoCmb'] as $key => $value) {
        if ($_REQUEST['valorAtributoCmb'][$key]) {
            $stFiltro .= " AND SW_ASSUNTO_ATRIBUTO_VALOR.valor = '".$_REQUEST['valorAtributoCmb'][$key]."' \n";
            $stFiltro .= " AND SW_ASSUNTO_ATRIBUTO_VALOR.cod_atributo = '".$key."' \n";
        }
    }
}

$rsProcesso = new RecordSet();
$obTProtocoloProcesso = new TProtocoloProcesso();
$obTProtocoloProcesso->listarProcessoAlteracao($rsProcesso,$stFiltro);

$stMascaraProcesso = SistemaLegado::pegaConfiguracao('mascara_processo',5,$sessao->exercicio);
$arMascaraProcesso = preg_split('/[^a-zA-Z0-9]/',$stMascaraProcesso);
$rsProcesso->addStrPad('cod_processo', strlen(strval($arMascaraProcesso)),"0");

$obLista = new Lista;

$obLista->setRecordSet($rsProcesso);

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Código" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Interessado" );
$obLista->ultimoCabecalho->setWidth( 25 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Classificação" );
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Assunto" );
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Inclusão");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "[cod_processo]"."/"."[ano_exercicio]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_cgm" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_classificacao" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_assunto" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "inclusao" );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->addCampo("&inCodigoProcesso" ,"cod_processo");
$obLista->ultimaAcao->addCampo("&inAnoExercicio","ano_exercicio");
$obLista->ultimaAcao->addCampo("&inCodigoClassificacao" ,"cod_classificacao");
$obLista->ultimaAcao->addCampo("&inCodigoAssunto","cod_assunto");
$obLista->ultimaAcao->setLink( $pgForm."?".Sessao::getId().$stLink );
$obLista->commitAcao();

$obLista->show();
?>
