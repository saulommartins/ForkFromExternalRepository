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
    * Página para relatorio de Divida Ativa Cancelada

    * Data de Criação   : 29/10/2009

    * @author Fernando Piccini Cercato
    * @ignore

    *Casos de uso: uc-05.04.10

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_FW_PDF."RRelatorio.class.php" );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaAtiva.class.php" );
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';

$preview = new PreviewBirt(5,33,6);
$preview->setVersaoBirt('2.5.0');
$preview->setTitulo('Relatório de Dívida Ativa Cancelada');

// INSTANCIA OBJETO
$obRRelatorio = new RRelatorio;
$obTDATDividaAtiva = new TDATDividaAtiva;
// SETA ELEMENTOS DO FILTRO
$stFiltro = "";

$arListaGrupoCreditoSessao = Sessao::read( "arListaGrupoCredito" );
$arListaCreditoSessao = Sessao::read( "arListaCredito" );
$inTotalDados = count( $arListaGrupoCreditoSessao );
$inTotalDadosCredito = count( $arListaCreditoSessao );

if ($inTotalDados > 0) {
    $stFiltroGrupo = "";
    $stFiltroTxtGrupo = "";
    for ($inX=0; $inX<$inTotalDados; $inX++) {
        if($inX==0)
            $stFiltroGrupo .= " ( ";    
        else {
            $stFiltroGrupo .= " OR ";
            $stFiltroTxtGrupo .= ", ";
        }

        $stFiltroTxtGrupo .= $arListaGrupoCreditoSessao[$inX]["stCodGrupo"];
        $arDados = explode( "/", $arListaGrupoCreditoSessao[$inX]["stCodGrupo"] );
        $stFiltroGrupo .= " ( parcela_origem.COD_GRUPO= ".$arDados[0]."  AND parcela_origem.ANO_EXERCICIO = '".$arDados[1]."' ) ";
        
        if(($inX+1)==$inTotalDados)
            $stFiltroGrupo .= " ) ";  
    }

    $preview->addParametro( 'stGrupoCredito', $stFiltroTxtGrupo );
    $stFiltro .= $stFiltroGrupo;
}

if ($inTotalDadosCredito > 0) {
    $stFiltroCredito = "";
    $stFiltroTxtCredito = "";
    for ($inX=0; $inX<$inTotalDadosCredito; $inX++) {
        if($inX==0)
            $stFiltroCredito .= " ( ";    
        else {
            $stFiltroCredito .= " OR ";
            $stFiltroTxtCredito .= ", ";
        }

        $stFiltroTxtCredito .= $arListaCreditoSessao[$inX]["stCodGrupo"];
        $arDados = explode( ".", $arListaCreditoSessao[$inX]["stCodGrupo"] );
        $stFiltroCredito .= " ( parcela_origem.cod_credito  = ".$arDados[0]."  AND parcela_origem.cod_especie  = ".$arDados[1]." AND parcela_origem.cod_genero  = ".$arDados[3]." AND  parcela_origem.cod_natureza  = ".$arDados[2]." ) ";
        
        if(($inX+1)==$inTotalDadosCredito)
            $stFiltroCredito .= " ) ";    
    }

    $preview->addParametro( 'filtro_credito', $stFiltroTxtCredito );
    $stFiltro .= $stFiltroCredito;
}

if ($_REQUEST['inCodInscricaoInicial'] && !$_REQUEST['inCodInscricaoFinal']) {
    $arCodInscricaoInicial = explode ( '/', $_REQUEST['inCodInscricaoInicial'] );
    if ($stFiltro) {
        $stFiltro = $stFiltro." AND ";
    }

    $stFiltro = $stFiltro." divida_cancelada.cod_inscricao = ".$arCodInscricaoInicial[0]." AND divida_cancelada.exercicio = ".$arCodInscricaoInicial[1];
    $preview->addParametro( 'stInscricaoDA', $_REQUEST['inCodInscricaoInicial'] );
}else
    if ($_REQUEST['inCodInscricaoFinal'] && !$_REQUEST['inCodInscricaoInicial']) {
        $arCodInscricaoFinal = explode ( '/', $_REQUEST['inCodInscricaoFinal'] );
        if ($stFiltro) {
            $stFiltro = $stFiltro." AND ";
        }

        $stFiltro = $stFiltro." divida_cancelada.cod_inscricao = ".$arCodInscricaoFinal[0]." AND divida_cancelada.exercicio = ".$arCodInscricaoFinal[1];
        $preview->addParametro( 'stInscricaoDA', $_REQUEST['inCodInscricaoFinal'] );
    }else
        if ($_REQUEST['inCodInscricaoFinal'] && $_REQUEST['inCodInscricaoInicial']) {
            $arCodInscricaoInicial = explode ( '/', $_REQUEST['inCodInscricaoInicial'] );
            $arCodInscricaoFinal = explode ( '/', $_REQUEST['inCodInscricaoFinal'] );
            if ($stFiltro) {
                $stFiltro = $stFiltro." AND ";
            }

            $stFiltro = $stFiltro." divida_cancelada.cod_inscricao BETWEEN ".$arCodInscricaoInicial[0]." AND ".$arCodInscricaoFinal[0]." AND divida_cancelada.exercicio BETWEEN ".$arCodInscricaoInicial[1]." AND ".$arCodInscricaoFinal[1];
            $preview->addParametro( 'stInscricaoDA', $_REQUEST['inCodInscricaoInicial']." até ".$_REQUEST['inCodInscricaoFinal'] );
        }

if ($_REQUEST["inCGM"]) {
    if ($stFiltro) {
        $stFiltro = $stFiltro." AND ";
    }

    $stFiltro = $stFiltro." divida_cgm.numcgm = ".$_REQUEST["inCGM"];
    $preview->addParametro( 'stCGM', $_REQUEST["inCGM"] );
}

if ($_REQUEST["inCodImovelInicial"] && $_REQUEST["inCodImovelFinal"]) {
    if ($stFiltro) {
        $stFiltro = $stFiltro." AND ";
    }

    $stFiltro = $stFiltro." divida_imovel.inscricao_municipal BETWEEN ".$_REQUEST["inCodImovelInicial"]." AND ".$_REQUEST["inCodImovelFinal"];
    $preview->addParametro( 'stInscricaoMunicipal', $_REQUEST["inCodImovelInicial"]." até ".$_REQUEST["inCodImovelFinal"] );
}else
    if ($_REQUEST["inCodImovelInicial"] && !$_REQUEST["inCodImovelFinal"]) {
        if ($stFiltro) {
            $stFiltro = $stFiltro." AND ";
        }

        $stFiltro = $stFiltro." divida_imovel.inscricao_municipal = ".$_REQUEST["inCodImovelInicial"];
        $preview->addParametro( 'stInscricaoMunicipal', $_REQUEST["inCodImovelInicial"] );
    }else
        if (!$_REQUEST["inCodImovelInicial"] && $_REQUEST["inCodImovelFinal"]) {
            if ($stFiltro) {
                $stFiltro = $stFiltro." AND ";
            }

            $stFiltro = $stFiltro." divida_imovel.inscricao_municipal = ".$_REQUEST["inCodImovelFinal"];
            $preview->addParametro( 'stInscricaoMunicipal', $_REQUEST["inCodImovelFinal"] );
        }

if ($_REQUEST["inNumInscricaoEconomicaInicial"] && $_REQUEST["inNumInscricaoEconomicaFinal"]) {
    if ($stFiltro) {
        $stFiltro = $stFiltro." AND ";
    }

    $stFiltro = $stFiltro." divida_empresa.inscricao_economica BETWEEN ".$_REQUEST["inNumInscricaoEconomicaInicial"]." AND ".$_REQUEST["inNumInscricaoEconomicaFinal"];
    $preview->addParametro( 'stInscricaoEconomica', $_REQUEST["inNumInscricaoEconomicaInicial"]." até ".$_REQUEST["inNumInscricaoEconomicaFinal"] );
}else
    if ($_REQUEST["inNumInscricaoEconomicaInicial"] && !$_REQUEST["inNumInscricaoEconomicaFinal"]) {
        if ($stFiltro) {
            $stFiltro = $stFiltro." AND ";
        }

        $stFiltro = $stFiltro." divida_empresa.inscricao_economica = ".$_REQUEST["inNumInscricaoEconomicaInicial"];
        $preview->addParametro( 'stInscricaoEconomica', $_REQUEST["inNumInscricaoEconomicaInicial"] );
    }else
        if (!$_REQUEST["inNumInscricaoEconomicaInicial"] && $_REQUEST["inNumInscricaoEconomicaFinal"]) {
            if ($stFiltro) {
                $stFiltro = $stFiltro." AND ";
            }

            $stFiltro = $stFiltro." divida_empresa.inscricao_economica = ".$_REQUEST["inNumInscricaoEconomicaFinal"];
            $preview->addParametro( 'stInscricaoEconomica', $_REQUEST["inNumInscricaoEconomicaFinal"] );
        }

if ($_REQUEST["dtInicio"] && $_REQUEST["dtFinal"]) {
    if ($stFiltro) {
        $stFiltro = $stFiltro." AND ";
    }

    $arDataIni = explode( "/", $_REQUEST["dtInicio"] );
    $arDataFim = explode( "/", $_REQUEST["dtFinal"] );
    $stFiltro = $stFiltro." divida_cancelada.timestamp::date BETWEEN '".$arDataIni[2]."-".$arDataIni[1]."-".$arDataIni[0]."' AND '".$arDataFim[2]."-".$arDataFim[1]."-".$arDataFim[0]."'";
    $preview->addParametro( 'stPeriodo', $_REQUEST["dtInicio"]." até ".$_REQUEST["dtFinal"]);
}else
    if ($_REQUEST["dtInicio"] && !$_REQUEST["dtFinal"]) {
        if ($stFiltro) {
            $stFiltro = $stFiltro." AND ";
        }

        $arData = explode( "/", $_REQUEST["dtInicio"] );
        $stFiltro = $stFiltro." divida_cancelada.timestamp::date = '".$arData[2]."-".$arData[1]."-".$arData[0]."'";
        $preview->addParametro( 'stPeriodo', $_REQUEST["dtInicio"] );
    }else
        if (!$_REQUEST["dtInicio"] && $_REQUEST["dtFinal"]) {
            if ($stFiltro) {
                $stFiltro = $stFiltro." AND ";
            }

            $arData = explode( "/", $_REQUEST["dtFinal"] );
            $stFiltro = $stFiltro." divida_cancelada.timestamp::date = '".$arData[2]."-".$arData[1]."-".$arData[0]."'";
            $preview->addParametro( 'stPeriodo', $_REQUEST["dtFinal"] );
        }

if ($stFiltro) {
    $stFiltro = " WHERE ".$stFiltro;
}

$preview->addParametro( 'stFiltro', $stFiltro );
$preview->setFormato('pdf');
$preview->preview();
?>
