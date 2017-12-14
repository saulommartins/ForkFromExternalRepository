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

    * Frame Oculto para relatorio de Divida
    * Data de Criação: 19/04/2007

    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Fernando Piccini Cercato

    * @ignore

    * $Id: OCMonta1RelatorioDivida.php 59913 2014-09-19 20:19:05Z lisiane $

    * Casos de uso: uc-05.04.10
*/

/*
$Log$
Revision 1.1  2007/04/19 16:06:56  cercato
*** empty log message ***

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_FW_PDF."RRelatorio.class.php" );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaAtiva.class.php" );
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';

$preview = new PreviewBirt(5,33,2);
$preview->setTitulo('Relatório de Dívida Ativa');
$preview->setVersaoBirt('2.5.0');
$preview->setExportaExcel (true );

// INSTANCIA OBJETO
$obRRelatorio = new RRelatorio;
$obTDATDividaAtiva = new TDATDividaAtiva;
// SETA ELEMENTOS DO FILTRO
$stFiltro = "";
if($_REQUEST['stRdbGrupo'] == 'credito'){
    $arListaGrupoCreditoSessao = Sessao::read( "arListaCredito" );
}else{
    $arListaGrupoCreditoSessao = Sessao::read( "arListaGrupoCredito" );
}

$inTotalDados = count( $arListaGrupoCreditoSessao );
if($_REQUEST['stRdbGrupo'] == 'credito'){
    $grupoCredito = "null";
    if ($inTotalDados > 0) {
        $stFiltroCredito = "(";
        for ($inX=0; $inX<$inTotalDados; $inX++) {
            if ($inX) {
                $stFiltroCredito .= " OR ";
            }
    
            $arDados = explode( ".", $arListaGrupoCreditoSessao[$inX]["stCodGrupo"] );
            $stFiltroCredito .= " ( parcela_origem.cod_credito  = ".$arDados[0]."  AND parcela_origem.cod_especie  = ".$arDados[1]." AND parcela_origem.cod_genero  = ".$arDados[3]." AND  parcela_origem.cod_natureza  = ".$arDados[2]." ) ";
        }
        $stFiltroCredito .= ")";
        $credito = $stFiltroCredito;
    
    } else {
        $credito = "null";
    }
}else{
    $credito = "null";
    if ($inTotalDados > 0) {
        $stFiltroGrupo = "(";
        for ($inX=0; $inX<$inTotalDados; $inX++) {
            if ($inX) {
                $stFiltroGrupo .= " OR ";
            }
    
            $arDados = explode( "/", $arListaGrupoCreditoSessao[$inX]["stCodGrupo"] );
            $stFiltroGrupo .= " ( GRUPO_CREDITO.COD_GRUPO= ".$arDados[0]."  AND GRUPO_CREDITO.ANO_EXERCICIO = ''".$arDados[1]."'' ) ";
            $grupo = $arDados[0];
            $grupoExercicio= $arDados[1];
        }
    
        $stFiltroGrupo .= ")";
        $grupoCredito = $stFiltroGrupo;
    
    } else {
        $grupoCredito = "null";
    }
    $preview->addParametro( 'grupo', $grupo);
    $preview->addParametro( 'grupo_exercicio', $grupoExercicio );
}

$preview->addParametro( 'filtro_credito', $credito );
$preview->addParametro( 'grupo_credito', $grupoCredito );

if ($_REQUEST['inCodInscricaoInicial']) {
    $arCodInscricaoInicial = explode ( '/', $_REQUEST['inCodInscricaoInicial'] );
    $inCodInscricaoInicial = (int) $arCodInscricaoInicial[0];
    $inExercicioInicial    = $arCodInscricaoInicial[1];
    //$stFiltro.=$inCodInscricaoInicial.',\''.$inExercicioInicial.'\',';
    $preview->addParametro( 'cod_insc_inicial', $inCodInscricaoInicial);
    $preview->addParametro( 'exercicio_inicial', $inExercicioInicial);
} else {
       $preview->addParametro( 'cod_insc_inicial', 'null');
}

if ($_REQUEST['inCodInscricaoFinal']) {
    $arCodInscricaoFinal = explode ( '/', $_REQUEST['inCodInscricaoFinal'] );
    $inCodInscricaoFinal = (int) $arCodInscricaoFinal[0];
    $inExercicioFinal    = $arCodInscricaoFinal[1];
    //$stFiltro.=$inCodInscricaoFinal.',\''.$inExercicioFinal.'\',';
    $preview->addParametro( 'cod_insc_final', $inCodInscricaoFinal);
    $preview->addParametro( 'exercicio_final', $inExercicioFinal);
} else {
    $preview->addParametro( 'cod_insc_final', 'null');
}

$inCgm = $_REQUEST["inCGM"] ?  $_REQUEST["inCGM"] :'null';
$preview->addParametro( 'cod_cgm', $inCgm);

$inCodImovelInicial = $_REQUEST["inCodImovelInicial"] ? $_REQUEST["inCodImovelInicial"]:'null';
$preview->addParametro( 'cod_imovel_inicial', $inCodImovelInicial);

$inCodImovelFinal = $_REQUEST["inCodImovelFinal"] ? $_REQUEST["inCodImovelFinal"]:'null';
$preview->addParametro( 'cod_imovel_final', $inCodImovelFinal);

$inNumInscricaoEconomicaInicial = $_REQUEST["inNumInscricaoEconomicaInicial"] ? $_REQUEST["inNumInscricaoEconomicaInicial"]:'null';
$preview->addParametro( 'cod_insc_eco_inicial', $inNumInscricaoEconomicaInicial);

$inNumInscricaoEconomicaFinal = $_REQUEST["inNumInscricaoEconomicaFinal"] ?  $_REQUEST["inNumInscricaoEconomicaFinal"]:'null';
$preview->addParametro( 'cod_insc_eco_final', $inNumInscricaoEconomicaFinal);

$inNumLogradouro = $_REQUEST["inNumLogradouro"] ? $_REQUEST["inNumLogradouro"]:'null';
$preview->addParametro( 'num_logradouro', $inNumLogradouro);

$flValorInicial='null';
if ($_REQUEST["flValorInicial"]) {
    $flValorInicial = str_replace('.', '', $_REQUEST["flValorInicial"]);
    $flValorInicial = str_replace(',', '.', $flValorInicial);
}
$preview->addParametro( 'valor_inicial', $flValorInicial);

$flValorFinal='null';
if ($_REQUEST["flValorFinal"]) {
    $flValorFinal = str_replace('.','',$_REQUEST["flValorFinal"]);
    $flValorFinal = str_replace(',','.',$flValorFinal);
}
$preview->addParametro( 'valor_final', $flValorFinal);

$inCodSituacao = $_REQUEST['inCodSituacao'] ? $_REQUEST['inCodSituacao']: 'null';
$preview->addParametro( 'cod_situacao', $inCodSituacao);

$stCriterio = $_REQUEST['stCriterio'] ? $_REQUEST['stCriterio']: 'null';
$preview->addParametro( 'criterio', $stCriterio);

$dtInicialCobranca = implode("-",array_reverse(explode("/",$_REQUEST['stDataInicial'])));
$dtInicialCobranca = $dtInicialCobranca  ? $dtInicialCobranca: 'null';
$preview->addParametro( 'dt_inicial_cobranca', $dtInicialCobranca);

$dtFinalCobranca = implode("-",array_reverse(explode("/",$_REQUEST['stDataFinal'])));
$dtFinalCobranca = $dtFinalCobranca ? $dtFinalCobranca : 'null';
$preview->addParametro( 'dt_final_cobranca', $dtFinalCobranca);

$preview->addParametro( 'stFiltro', $stFiltro );

//$preview->setFormato('pdf');
$preview->preview();
?>
