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

    * Página de processamento oculto e geração do relatório de resumo do lote
    * Data de Criação   : 06/03/2007

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato

    * @ignore

    * $Id: OCGeraRelatorioResumoLote.php 61658 2015-02-23 14:28:04Z evandro $

    * Casos de uso: uc-05.03.23
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_FW_PDF."RRelatorio.class.php" );
include_once ( CAM_FW_PDF."ListaPDF.class.php" );
include_once ( CAM_GT_ARR_NEGOCIO."RARRPagamento.class.php" );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRPagamento.class.php" );

$obRRelatorio = new RRelatorio;
$obPDF        = new ListaPDF("L");

$obRRelatorio->setExercicio  ( Sessao::getExercicio() );
$obRRelatorio->recuperaCabecalho( $arConfiguracao );

if ( isset($_REQUEST["descricao"]) ) {
    $arConfiguracao["nom_acao"] = $_REQUEST["descricao"];
} else {
    if ( $_REQUEST["stTipoRelatorio"] == "analitico" )
        $arConfiguracao["nom_acao"] .= " (analítico)";
    else
        $arConfiguracao["nom_acao"] .= " (sintético)";
}

$obPDF->setModulo            ( "Arrecadação:"   );
$obPDF->setTitulo               ( "Créditos:" );
$obPDF->setSubTitulo         ( "Exercício - ".Sessao::getExercicio() );
$obPDF->setUsuario           ( Sessao::getUsername() );
$obPDF->setEnderecoPrefeitura( $arConfiguracao );

$obTARRPagamento = new TARRPagamento;
$stFiltro = "";

if ($_REQUEST["inNumBanco"]!='') {
    $stFiltro .= " banco.num_banco = '".$_REQUEST["inNumBanco"]."' AND ";
}

if ($_REQUEST["inNumAgencia"]!='') {
    $stFiltro .= " agencia.num_agencia = ".$_REQUEST["inNumAgencia"]." AND ";
}

if ($_REQUEST["stExercicio"]!='') {
    $stFiltro .= " lote.exercicio = '".$_REQUEST["stExercicio"]."' AND ";
}

if ($_REQUEST["inCodContribuinte"]!='') {
    $stFiltro .= " pagamento.numcgm = ".$_REQUEST["inCodContribuinte"]." AND ";
}

if ($_REQUEST["stTipoLote"] != '') {
    if ($_REQUEST["stTipoLote"] == "automatico") {
        $stFiltro .= " lote.automatico = TRUE AND ";
    } elseif ($_REQUEST["stTipoLote"] == "manual") {
        $stFiltro .= " lote.automatico = FALSE AND ";
    }
}

if ($_REQUEST["inCodLoteInicio"]!='' && $_REQUEST["inCodLoteFinal"]!='') {
    $stFiltro .= " lote.cod_lote BETWEEN ".$_REQUEST["inCodLoteInicio"]." and ".$_REQUEST["inCodLoteFinal"]." and ";
} elseif ($_REQUEST["inCodLoteInicio"] != '') {
    $stFiltro .= " lote.cod_lote = ".$_REQUEST["inCodLoteInicio"]." and ";
}

if ($_REQUEST["dtInicio"]!='' &&  $_REQUEST["dtFinal"]!='') {
    $arDataInicial = explode( "/", $_REQUEST["dtInicio"] );
    $stDataInicial = $arDataInicial[2].'-'.$arDataInicial[1].'-'.$arDataInicial[0];

    $arDataFinal = explode( "/", $_REQUEST["dtFinal"] );
    $stDataFinal = $arDataFinal[2].'-'.$arDataFinal[1].'-'.$arDataFinal[0];

    $stFiltro .= " lote.data_lote BETWEEN '".$stDataInicial."' AND '".$stDataFinal."' AND ";

} elseif ($_REQUEST["dtInicio"]!='') {
    $arDataInicial = explode( "/", $_REQUEST["dtInicio"] );
    $stDataInicial = $arDataInicial[2].'-'.$arDataInicial[1].'-'.$arDataInicial[0];

    $stFiltro .= " lote.data_lote = '".$stDataInicial."' AND ";
}

if ($stFiltro!="") {
    $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 )." ORDER BY lote.cod_lote ASC ";
}

$obTARRPagamento->recuperaListaLotes( $rsListaLotes, $stFiltro );
if ( $rsListaLotes->Eof() ) {
    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
    SistemaLegado::exibeAviso( "Nenhum lote encontrado!", "n_erro", "erro", Sessao::getId(), "../" );
    exit;
}

//aqui vai montar filtro com dados da lista do rsListaLotes
$stFiltro = " WHERE lote.exercicio = '".$rsListaLotes->getCampo( "exercicio" )."' AND lote.cod_lote in ( ";

$arDadosInicial[] = array(
        "titulo1" => "LOTE INICIAL:",
        "titulo2" => "DATA DO LOTE:",
        "titulo3" => "DATA DA BAIXA:",
        "cod_lote" => $rsListaLotes->getCampo( "cod_lote" ),
        "data_lote" => $rsListaLotes->getCampo( "data_lote" ),
        "data_baixa" => $rsListaLotes->getCampo( "data_baixa" )
);

$inTotal = 0;
$rsListaLotes->setPrimeiroElemento();
$stFiltro2 = "";
while ( !$rsListaLotes->eof() ) {

    if ($inTotal > 0) {
        $stFiltro .= ", ";
        $stFiltro2 .= ", ";
    }

    $stFiltro .= $rsListaLotes->getCampo( "cod_lote" );
    $stFiltro2 .= $rsListaLotes->getCampo( "cod_lote" );

    if ( $inTotal+1 >= $rsListaLotes->getNumLinhas() ) {
        $arDadosFinal[] = array(
            "titulo1" => "LOTE FINAL:",
            "titulo2" => "DATA DO LOTE:",
            "titulo3" => "DATA DA BAIXA:",
            "cod_lote" => $rsListaLotes->getCampo( "cod_lote" ),
            "data_lote" => $rsListaLotes->getCampo( "data_lote" ),
            "data_baixa" => $rsListaLotes->getCampo( "data_baixa" )
        );
    }

    $rsListaLotes->proximo();
    $inTotal++;
}

$stFiltro .= " ) ";

$rsResumoLoteInicial = new RecordSet;
$rsResumoLoteInicial->preenche( $arDadosInicial );

$rsResumoLoteInicial->setPrimeiroElemento();

$rsResumoLoteFinal = new RecordSet;
$rsResumoLoteFinal->preenche( $arDadosFinal );

$rsResumoLoteFinal->setPrimeiroElemento();

//titulo
$arTitulo1 = array("tit" => "DADOS DO LOTE");

$rsTit1 = new Recordset;
$rsTit1->preenche($arTitulo1);
$rsTit1->setPrimeiroElemento();

$obPDF->addRecordSet( $rsTit1 );
$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho   ( "DADOS DO LOTE"  ,20, 12, "B" );

if ( ( !$_REQUEST["inCodLoteFinal"] && $_REQUEST["inCodLoteInicio"]) || (($_REQUEST["inCodLoteInicio"] == $_REQUEST["inCodLoteFinal"]) && ($_REQUEST["inCodLoteInicio"]) ) ) {
    $obRARRPagamento = new RARRPagamento;
    $obRARRPagamento->inCodLote = $_REQUEST["inCodLoteInicio"];
    if ( $_REQUEST["stExercicio"] )
        $obRARRPagamento->stExercicio = $_REQUEST["stExercicio"];

    $obRARRPagamento->consultaResumoLoteBaixaManual($rsResumoLote );

    $obPDF->addRecordSet( $rsResumoLote );
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho   ( "LOTE"             ,8, 10 );
    $obPDF->addCabecalho   ( "DATA DO LOTE"     ,12, 10 );
    $obPDF->addCabecalho   ( "DATA DA BAIXA"    ,12, 10 );
    $obPDF->addCabecalho   ( "REGISTROS"        ,9, 10 );
    $obPDF->addCabecalho   ( "BANCO"            ,15, 10 );
    $obPDF->addCabecalho   ( "AGÊNCIA"          ,15, 10 );

    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo       ( "cod_lote"    , 8 );
    $obPDF->addCampo       ( "data_lote"   , 8 );
    $obPDF->addCampo       ( "data_baixa"  , 8 );
    $obPDF->addCampo       ( "registros"   , 8 );
    $obPDF->addCampo       ( "[num_banco] - [nom_banco]"     , 8 );
    $obPDF->addCampo       ( "[num_agencia] - [nom_agencia]" , 8 );
} else {
    $obPDF->addRecordSet( $rsResumoLoteInicial );
    $obPDF->setQuebraPaginaLista( false );

    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho   ( ""     ,8 , 0 );
    $obPDF->addCabecalho   ( ""     ,12, 0 );
    $obPDF->addCabecalho   ( ""     ,10, 0 );
    $obPDF->addCabecalho   ( ""     ,12, 0 );
    $obPDF->addCabecalho   ( ""     ,10, 0 );
    $obPDF->addCabecalho   ( ""     ,12, 0 );

    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo       ( "titulo1", 8, "B" );
    $obPDF->addCampo       ( "cod_lote", 8 );
    $obPDF->addCampo       ( "titulo2", 8, "B" );
    $obPDF->addCampo       ( "data_lote", 8 );
    $obPDF->addCampo       ( "titulo3", 8, "B" );
    $obPDF->addCampo       ( "data_baixa", 8 );

    $obPDF->addRecordSet( $rsResumoLoteFinal );
    $obPDF->setQuebraPaginaLista( false );

    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho   ( ""     ,8 , 0 );
    $obPDF->addCabecalho   ( ""     ,12, 0 );
    $obPDF->addCabecalho   ( ""     ,10, 0 );
    $obPDF->addCabecalho   ( ""     ,12, 0 );
    $obPDF->addCabecalho   ( ""     ,10, 0 );
    $obPDF->addCabecalho   ( ""     ,12, 0 );

    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo       ( "titulo1", 8, "B" );
    $obPDF->addCampo       ( "cod_lote", 8 );
    $obPDF->addCampo       ( "titulo2", 8, "B" );
    $obPDF->addCampo       ( "data_lote", 8 );
    $obPDF->addCampo       ( "titulo3", 8, "B" );
    $obPDF->addCampo       ( "data_baixa", 8 );
}

$obTARRPagamento->recuperaResumoLoteListaOrigem( $rsListaOrigem, $stFiltro );
$arTMP_lo_2 = $rsListaOrigem->getElementos();
$arTMP_lo = array();
for ( $inX=0; $inX<count($arTMP_lo_2); $inX++ ) {
    if ( preg_match( "/INCONSISTENCIA/", $arTMP_lo_2[$inX]['tipo_numeracao'] ) ) {
        if ( !preg_match( "/(D.A.)/", $arTMP_lo_2[$inX]['descricao'] )  ) {
            $boIncluir = true;
            for ( $inY=0; $inY<count($arTMP_lo); $inY++ ) {
                if ( ( $arTMP_lo_2[$inX]["origem"] == $arTMP_lo[$inY]["origem"] ) && ( $arTMP_lo_2[$inX]["origem_exercicio"] == $arTMP_lo[$inY]["origem_exercicio"] ) ) {
                    $arTMP_lo[$inY] = $arTMP_lo_2[$inX];
                    $boIncluir = false;
                    break;
                }
            }

            if ( $boIncluir )
                $arTMP_lo[] = $arTMP_lo_2[$inX];
        }
    }else
    if ( !preg_match( "/(D.A.)/", $arTMP_lo_2[$inX]['descricao'] )  ) {
        $boIncluir = true;
        for ( $inY=0; $inY<count($arTMP_lo); $inY++ ) {
            if ( ( $arTMP_lo_2[$inX]["origem"] == $arTMP_lo[$inY]["origem"] ) && ( $arTMP_lo_2[$inX]["origem_exercicio"] == $arTMP_lo[$inY]["origem_exercicio"] ) ) {
                $boIncluir = false;
                break;
            }
        }

        if ( $boIncluir )
            $arTMP_lo[] = $arTMP_lo_2[$inX];
    }
}

$obTARRPagamento->recuperaListaPagamentosLoteDARelatorio ( $rsPagamentosDA, $stFiltro2 );

$arCredOrig = array();

$arTMP1 = $rsPagamentosDA->getElementos();
$inTot = 0;
for ( $inX=0; $inX<count($arTMP1); $inX++ ) {
    $boInserir = true;
    for ($inY=0; $inY<$inTot; $inY++) {
        if (
            ($arTMP1[$inX]["cod_credito"] === $arCredOrig[$inY]["cod_credito"]) &&
            ($arTMP1[$inX]["cod_especie"] === $arCredOrig[$inY]["cod_especie"]) &&
            ($arTMP1[$inX]["cod_genero"] === $arCredOrig[$inY]["cod_genero"]) &&
            ($arTMP1[$inX]["cod_natureza"] === $arCredOrig[$inY]["cod_natureza"])
           ) {
            $arCredOrig[$inY]["valor_pago_calculo"] += $arTMP1[$inX]["valor_pago_calculo"];
            $arCredOrig[$inY]["juros"] += $arTMP1[$inX]["juros"];
            $arCredOrig[$inY]["multa"] += $arTMP1[$inX]["multa"];
            $arCredOrig[$inY]["diferenca"] += $arTMP1[$inX]["diferenca"];
            $arCredOrig[$inY]["correcao"] += $arTMP1[$inX]["correcao"];
            $arCredOrig[$inY]["valor_pago_normal"] += $arTMP1[$inX]["valor_pago_normal"];

            $boInserir = false;
            break;
        }
    }

    if ($boInserir) {
        $inY = $inTot;
        $arCredOrig[$inY]["origem"] = $arTMP1[$inX]["cod_credito"].".".$arTMP1[$inX]["cod_especie"].".".$arTMP1[$inX]["cod_genero"].".".$arTMP1[$inX]["cod_natureza"];
        $arCredOrig[$inY]["descricao"] = "D.A. - ".$arTMP1[$inX]["descricao_credito"];
        $arCredOrig[$inY]["cod_credito"] = $arTMP1[$inX]["cod_credito"];
        $arCredOrig[$inY]["cod_especie"] = $arTMP1[$inX]["cod_especie"];
        $arCredOrig[$inY]["cod_genero"] = $arTMP1[$inX]["cod_genero"];
        $arCredOrig[$inY]["cod_natureza"] = $arTMP1[$inX]["cod_natureza"];

        $arCredOrig[$inY]["valor_pago_calculo"] = $arTMP1[$inX]["valor_pago_calculo"];
        $arCredOrig[$inY]["juros"] = $arTMP1[$inX]["juros"];
        $arCredOrig[$inY]["multa"] = $arTMP1[$inX]["multa"];
        $arCredOrig[$inY]["diferenca"] = $arTMP1[$inX]["diferenca"];
        $arCredOrig[$inY]["correcao"] += $arTMP1[$inX]["correcao"];
        $arCredOrig[$inY]["valor_pago_normal"] = $arTMP1[$inX]["valor_pago_normal"];
        $arCredOrig[$inY]["tipo"] = "divida";
        $inTot++;
    }
}

for ($inX=0; $inX<$inTot; $inX++) {
    $arTMP_lo[] = $arCredOrig[$inX];
}

$rsListaOrigem->preenche( $arTMP_lo );
$rsListaOrigem->setPrimeiroElemento();
$flSomaValorNormal = $flSomaValorJuros = $flSomaValorMulta = 0.00;
$flSomaValorDiff = $flSomaValorCorrecao = $flSomaValorTotal = $flSomaValorInconsistente = 0.00;
$contOrigem = 0;

while ( !$rsListaOrigem->eof() ) {
    $contOrigem ++;
    $arSomatorios = null;
    $flOrigemValorNormalOK = $flOrigemValorJurosOK = $flOrigemValorMultaOK = 0.00;
    $flOrigemValorDiffOK = $flOrigemValorCorrecaoOK = $flOrigemValorTotalOK = $flOrigemValorInconsistenteOK = 0.00;

    if ( $rsListaOrigem->getCampo('tipo') != "divida" ) {
        $stFiltro3 = " WHERE pagamento_lote.cod_lote IN ( ".$stFiltro2." ) \n";
        if ( $rsListaOrigem->getCampo('tipo') == 'grupo' ) {
            $stFiltro3 .= "\n AND acgc.cod_grupo = ".$rsListaOrigem->getCampo('origem');
            $stFiltro3 .= "\n and acgc.cod_grupo is not null ";
            $stFiltro3 .= "\n AND c.exercicio = '".$rsListaOrigem->getCampo ('origem_exercicio')."'";
        } else {
            $stFiltro3 .= "\n AND c.exercicio = '".$rsListaOrigem->getCampo('origem_exercicio')."'";
            $arCredito = explode ('.', $rsListaOrigem->getCampo('origem') );
            $stFiltro3 .= "\n AND c.cod_credito = ".$arCredito[0];
            $stFiltro3 .= "\n AND c.cod_especie = ".$arCredito[1];
            $stFiltro3 .= "\n AND c.cod_genero = ".$arCredito[2];
            $stFiltro3 .= "\n AND c.cod_natureza = ".$arCredito[3];
            $stFiltro3 .= "\n AND acgc.cod_grupo is null ";
        }

        $stFiltro3 .= " AND pagamento.cod_convenio != -1 ";
        if ($_REQUEST["stTipoRelatorio"] == "analitico") {
            $obTARRPagamento->recuperaListaPagamentosLoteAnalitico ( $rsListaCreditos, $stFiltro3, "", "", false );     
            $arTMP = $rsListaCreditos->getElementos();

            $inTot = 0;
            $arTMP1 = array();
            for ( $inX=0; $inX<count($arTMP); $inX++ ) {
                $boInserir = true;
                for ($inY=0; $inY<$inTot; $inY++) {
                    if ($arTMP[$inX]["origem"] == $arTMP1[$inY]["origem"]) {
                        $arTMP1[$inY]['valor_pago_calculo'] += $arTMP[$inX]['valor_pago_calculo'];
                        $arTMP1[$inY]['juros'] += $arTMP[$inX]['juros'];
                        $arTMP1[$inY]['multa'] += $arTMP[$inX]['multa'];
                        $arTMP1[$inY]['valor_pago_normal'] += $arTMP[$inX]['valor_pago_normal'];
                        $arTMP1[$inY]['diferenca'] += $arTMP[$inX]['diferenca'];
                        $arTMP1[$inY]['correcao'] += $arTMP[$inX]['correcao'];  
                        $boInserir = false;
                        break;
                    }
                }
                $arTMP1[$inY]['valor_pago_calculo'] = $arTMP1[$inY]['valor_pago_calculo']== 0 ? number_format($arTMP1[$inY]['valor_pago_calculo'], 2, ',', '.' ) :$arTMP1[$inY]['valor_pago_calculo'] ;             
                $arTMP1[$inY]['juros'] = $arTMP1[$inY]['juros'] == 0 ? number_format($arTMP[$inX]['juros'], 2, ',', '.' ) : $arTMP1[$inY]['juros']; 
                $arTMP1[$inY]['multa'] = $arTMP1[$inY]['multa'] == 0 ? number_format($arTMP[$inX]['multa'], 2, ',', '.' ) : $arTMP1[$inY]['multa'];
                $arTMP1[$inY]['valor_pago_normal'] = $arTMP1[$inY]['valor_pago_normal'] == 0 ? number_format($arTMP[$inX]['valor_pago_normal'], 2, ',', '.' ) : $arTMP1[$inY]['valor_pago_normal'];
                $arTMP1[$inY]['diferenca'] = $arTMP1[$inY]['diferenca'] == 0 ? number_format($arTMP[$inX]['diferenca'], 2, ',', '.' ) : $arTMP1[$inY]['diferenca'];
                $arTMP1[$inY]['correcao'] = $arTMP1[$inY]['correcao'] == 0 ? number_format($arTMP[$inX]['correcao'], 2, ',', '.' ) : $arTMP1[$inY]['correcao'];
                if ($boInserir) {
                    $arTMP1[$inTot] = $arTMP[$inX];
                    $inTot++;
                }
            }

            for ( $inX=0; $inX<count($arTMP1); $inX++ ) {
                $arTMP1[$inX]["descricao"] = $arTMP1[$inX]["descricao_credito"];
            }

            $rsListaCreditos->preenche( $arTMP1 );       
            $rsListaCreditos->setPrimeiroElemento();

        } else {
            $obTARRPagamento->recuperaListaPagamentosLote ( $rsListaCreditos, $stFiltro3, "", "", false );
        }

        while ( !$rsListaCreditos->eof() ) {
            $flOrigemValorNormalOK    += $rsListaCreditos->getCampo( 'valor_pago_calculo' );
            $flOrigemValorJurosOK     += $rsListaCreditos->getCampo( 'juros' );
            $flOrigemValorMultaOK     += $rsListaCreditos->getCampo( 'multa' );
            $flOrigemValorDiffOK      += $rsListaCreditos->getCampo( 'diferenca' );
            $flOrigemValorTotalOK     += $rsListaCreditos->getCampo( 'valor_pago_normal' );
            $flOrigemValorCorrecaoOK  += $rsListaCreditos->getCampo( 'correcao' );

            $rsListaCreditos->proximo();
        }
    } else {
        $flOrigemValorNormalOK   += $rsListaOrigem->getCampo( 'valor_pago_calculo' );
        $flOrigemValorJurosOK    += $rsListaOrigem->getCampo( 'juros' );
        $flOrigemValorMultaOK    += $rsListaOrigem->getCampo( 'multa' );
        $flOrigemValorDiffOK     += $rsListaOrigem->getCampo( 'diferenca' );
        $flOrigemValorCorrecaoOK += $rsListaOrigem->getCampo( 'correcao' );
        $flOrigemValorTotalOK    += $rsListaOrigem->getCampo( 'valor_pago_normal' );
    }

    $flSomaValorNormal   += $flOrigemValorNormalOK;
    $flSomaValorJuros    += $flOrigemValorJurosOK;
    $flSomaValorMulta    += $flOrigemValorMultaOK;
    $flSomaValorDiff     += $flOrigemValorDiffOK;
    $flSomaValorCorrecao += $flOrigemValorCorrecaoOK;
    $flSomaValorTotal    += $flOrigemValorTotalOK;

    $arSomatorios[] = array (
        "stNormal"      => "Normais",
        "somaNormal"    => $flOrigemValorNormalOK,
        "somaJuros"     => $flOrigemValorJurosOK,
        "somaMulta"     => $flOrigemValorMultaOK,
        "somaDiff"      => $flOrigemValorDiffOK,
        "somaCorrecao"  => $flOrigemValorCorrecaoOK,
        "somaTotal"     => $flOrigemValorTotalOK
    );

    //titulo
    $arTitulo2 = array();
    $arTitulo2[] = array(
        "titulo" => $rsListaOrigem->getCampo('origem').' - '.$rsListaOrigem->getCampo('descricao')
    );

    $rsTit2 = new Recordset;
    $rsTit2->preenche($arTitulo2);
    $rsTit2->setPrimeiroElemento();
    $obPDF->addRecordSet( $rsTit2 );
    $obPDF->setQuebraPaginaLista( false );

    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho   ( "" ,50, 0 );
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo       ( "titulo" , 14, "B" );
    
    $rsListaCreditos->addFormatacao ('valor_pago_calculo', 'NUMERIC_BR');
    $rsListaCreditos->addFormatacao ('juros' , 'NUMERIC_BR');
    $rsListaCreditos->addFormatacao ('multa' , 'NUMERIC_BR');
    $rsListaCreditos->addFormatacao ('diferenca'  , 'NUMERIC_BR');
    $rsListaCreditos->addFormatacao ('correcao'  , 'NUMERIC_BR');
    $rsListaCreditos->addFormatacao ('valor_pago_normal' , 'NUMERIC_BR');

    $rsListaCreditos->setPrimeiroElemento();

    if ($_REQUEST["stTipoRelatorio"] == "analitico") {

        if ( $rsListaOrigem->getCampo('tipo') == "divida" ) {
            $arTMP1 = array();
            $arTMP1[0]["origem"] = $rsListaOrigem->getCampo('origem');
            $arTMP1[0]["descricao"] = $rsListaOrigem->getCampo('descricao');
            $arTMP1[0]["valor_pago_calculo"] = $rsListaOrigem->getCampo('valor_pago_calculo') == 0 ? number_format($rsListaOrigem->getCampo('valor_pago_calculo'), 2, ',', '.' ) : $rsListaOrigem->getCampo('valor_pago_calculo');
            $arTMP1[0]["juros"] = $rsListaOrigem->getCampo('juros') == 0 ? number_format($rsListaOrigem->getCampo('juros'), 2, ',', '.' ): $rsListaOrigem->getCampo('juros');
            $arTMP1[0]["multa"] = $rsListaOrigem->getCampo('multa')==0 ? number_format($rsListaOrigem->getCampo('multa'), 2, ',', '.' ): $rsListaOrigem->getCampo('multa');
            $arTMP1[0]["diferenca"] = $rsListaOrigem->getCampo('diferenca') == 0 ? number_format($rsListaOrigem->getCampo('diferenca'), 2, ',', '.' ) : $rsListaOrigem->getCampo('diferenca');
            $arTMP1[0]["correcao"]  =$rsListaOrigem->getCampo('correcao') == 0 ? number_format($rsListaOrigem->getCampo('correcao'), 2, ',', '.' ) : $rsListaOrigem->getCampo('correcao');
            $arTMP1[0]["valor_pago_normal"] = $rsListaOrigem->getCampo('valor_pago_normal') == 0 ? number_format($rsListaOrigem->getCampo('valor_pago_normal'), 2, ',', '.' ) : $rsListaOrigem->getCampo('valor_pago_normal');
            
            $rsListaCreditos = new RecordSet;
            $rsListaCreditos->preenche($arTMP1);
        }
        $rsListaCreditos->addFormatacao ('valor_pago_calculo', 'NUMERIC_BR');
        $rsListaCreditos->addFormatacao ('juros' , 'NUMERIC_BR');
        $rsListaCreditos->addFormatacao ('multa' , 'NUMERIC_BR');
        $rsListaCreditos->addFormatacao ('diferenca'  , 'NUMERIC_BR');
        $rsListaCreditos->addFormatacao ('correcao'  , 'NUMERIC_BR');
        $rsListaCreditos->addFormatacao ('valor_pago_normal' , 'NUMERIC_BR');
        $obPDF->addRecordSet( $rsListaCreditos );
        $obPDF->setQuebraPaginaLista( false );

        $obPDF->setAlinhamento ( "L" );
        $obPDF->addCabecalho   ( "CÓDIGO"   ,10, 9 );
        $obPDF->addCabecalho   ( "CRÉDITO"  ,20, 9 );
        $obPDF->setAlinhamento ( "R" );
        $obPDF->addCabecalho   ( "VALOR"    ,20, 9 );
        $obPDF->addCabecalho   ( "JUROS"    ,10, 9 );
        $obPDF->addCabecalho   ( "MULTA"    ,10, 9 );
        $obPDF->addCabecalho   ( "DIFF"     ,10, 9 );
        $obPDF->addCabecalho   ( "CORREÇÃO" ,10, 9 );
        $obPDF->addCabecalho   ( "TOTAL"    ,10, 9 );

        $obPDF->setAlinhamento ( "L" );
        $obPDF->addCampo       ( "origem"   , 8 );
        $obPDF->addCampo       ( "descricao", 8, "B" );
        $obPDF->setAlinhamento ( "R" );
        $obPDF->addCampo       ( "valor_pago_calculo", 8 );
        $obPDF->addCampo       ( "juros"    , 8 );
        $obPDF->addCampo       ( "multa"    , 8 );
        $obPDF->addCampo       ( "diferenca", 8 );
        $obPDF->addCampo       ( "correcao" , 8 );
        $obPDF->addCampo       ( "valor_pago_normal"    , 8 );
    } else {
        $obPDF->addRecordSet( $rsTit2 );
        $obPDF->setQuebraPaginaLista( false );

        $obPDF->setAlinhamento ( "L" );
        $obPDF->addCabecalho   ( ""  ,10, 9 );
        $obPDF->addCabecalho   ( "" ,20, 9 );
        $obPDF->setAlinhamento ( "R" );
        $obPDF->addCabecalho   ( "VALOR"    ,20, 9 );
        $obPDF->addCabecalho   ( "JUROS"    ,10, 9 );
        $obPDF->addCabecalho   ( "MULTA"    ,10, 9 );
        $obPDF->addCabecalho   ( "DIFF"     ,10, 9 );
        $obPDF->addCabecalho   ( "CORREÇÃO" ,10, 9 );
        $obPDF->addCabecalho   ( "TOTAL"    ,10, 9 );
    }

    $rsSomatorio = new Recordset;
    $rsSomatorio->preenche ( $arSomatorios );
    $rsSomatorio->addFormatacao ('somaNormal'   , 'NUMERIC_BR');
    $rsSomatorio->addFormatacao ('somaJuros'    , 'NUMERIC_BR');
    $rsSomatorio->addFormatacao ('somaMulta'    , 'NUMERIC_BR');
    $rsSomatorio->addFormatacao ('somaDiff'     , 'NUMERIC_BR');
    $rsSomatorio->addFormatacao ('somaCorrecao' , 'NUMERIC_BR');
    $rsSomatorio->addFormatacao ('somaTotal'    , 'NUMERIC_BR');
    $obPDF->addRecordSet( $rsSomatorio );
    $obPDF->setQuebraPaginaLista( false );

    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCabecalho   ( "Total Normal"         ,30 ,9, "B"  );
    $obPDF->addCabecalho   ( number_format ( $flOrigemValorNormalOK   , 2, ',', '.')  ,20 ,9, "B" );
    $obPDF->addCabecalho   ( number_format ( $flOrigemValorJurosOK    , 2, ',', '.')  ,10 ,9, "B" );
    $obPDF->addCabecalho   ( number_format ( $flOrigemValorMultaOK    , 2, ',', '.')  ,10 ,9, "B" );
    $obPDF->addCabecalho   ( number_format ( $flOrigemValorDiffOK     , 2, ',', '.')  ,10 ,9, "B" );
    $obPDF->addCabecalho   ( number_format ( $flOrigemValorCorrecaoOK , 2, ',', '.')  ,10 ,9, "B" );
    $obPDF->addCabecalho   ( number_format ( $flOrigemValorTotalOK    , 2, ',', '.')  ,10 ,9, "B" );

    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCampo       ( "" , 9, "B" );
    $obPDF->addCampo       ( "" , 9, "B" );
    $obPDF->addCampo       ( "" , 9, "B" );
    $obPDF->addCampo       ( "" , 9, "B" );
    $obPDF->addCampo       ( "" , 9, "B" );
    $obPDF->addCampo       ( "" , 9, "B" );


    $stFiltroI = str_replace ( ' lote.', ' plote.', $stFiltro );

    if ( $rsListaOrigem->getCampo('tipo') == 'grupo' ) {
        $stFiltroI .= "\n AND acgc.cod_grupo = ".$rsListaOrigem->getCampo('origem')." and acgc.cod_grupo is not null ";
        $stFiltroI .= "\n AND c.exercicio = '".$rsListaOrigem->getCampo ('origem_exercicio')."'";
        $stFiltroSomaPL = "grupo";
    } else {
        if ( $rsListaOrigem->getCampo('origem_exercicio') )
            $stFiltroI .= "\n AND c.exercicio = '".$rsListaOrigem->getCampo('origem_exercicio')."'";

        $arCredito = explode ('.', $rsListaOrigem->getCampo('origem') );
        $stFiltroI .= "\n AND c.cod_credito = ".$arCredito[0];
        $stFiltroI .= "\n AND c.cod_especie = ".$arCredito[1];
        $stFiltroI .= "\n AND c.cod_genero = ".$arCredito[2];
        $stFiltroI .= "\n AND c.cod_natureza = ".$arCredito[3];
        $stFiltroI .= "\n AND acgc.cod_grupo is null ";

        $stFiltroSomaPL = "credito";
    }

    $stFiltroI = str_replace( "plote", "ali", $stFiltroI );
    if ( $rsListaOrigem->getCampo('cod_convenio')?$rsListaOrigem->getCampo('cod_convenio'):-1 != -1 ) {
        $obTARRPagamento->recuperaResumoLoteListaInconsistenteAgrupado( $rsPagamentosInconsistentes, $stFiltroI." AND carne.cod_convenio = ".($rsListaOrigem->getCampo('cod_convenio')?$rsListaOrigem->getCampo('cod_convenio'):-1) );
    }else
        $rsPagamentosInconsistentes = new RecordSet;

    $flOrigemValorInconsistenteOK = 0.00;
    if ( $rsPagamentosInconsistentes->getNumLinhas() > 0 ) {
        $rsPagamentosInconsistentes->setPrimeiroElemento();
        while ( !$rsPagamentosInconsistentes->eof() ) {
            $flOrigemValorInconsistenteOK += $rsPagamentosInconsistentes->getCampo('valor');
            
            $rsPagamentosInconsistentes->setCampo('valor', number_format ( $rsPagamentosInconsistentes->getCampo('valor'), 2, ',', '.' ));
            $rsPagamentosInconsistentes->proximo();
        }

        //titulo
        $flSomaValorInconsistentes += $flOrigemValorInconsistenteOK;
        $rsPagamentosInconsistentes->setPrimeiroElemento();

        $obPDF->addRecordSet( $rsPagamentosInconsistentes );

        // Numeração     Parcela     Origem   Inscrição       Contribuinte    Valor (R$)   Pagamento   Situação
        $obPDF->setQuebraPaginaLista( false );
        $obPDF->setAlinhamento ( "L" );
        $obPDF->addCabecalho   ( "Inconsistentes", 17, 10 );

        $obPDF->addCabecalho   ( "", 6, 10 );
        $obPDF->addCabecalho   ( "", 18, 10 );
        $obPDF->setAlinhamento ( "R" );
        $obPDF->addCabecalho   ( "", 8, 10 );
        $obPDF->setAlinhamento ( "R" );
        $obPDF->addCabecalho   ( "", 8, 10 );
        $obPDF->setAlinhamento ( "R" );
        $obPDF->addCabecalho   ( "", 8, 10 );
        $obPDF->setAlinhamento ( "R" );
        $obPDF->addCabecalho   ( "", 10, 10 );
        $obPDF->setAlinhamento ( "R" );
        $obPDF->addCabecalho   ( "", 8, 10 );
        $obPDF->addCabecalho   ( "", 8, 10 );

        $obPDF->setAlinhamento ( "L" );
        $obPDF->addCampo       ( "[numeracao]", 9 );
        $obPDF->setAlinhamento ( "C" );
        $obPDF->addCampo       ( "inscricao", 8 );
        $obPDF->setAlinhamento ( "L" );
        $obPDF->addCampo       ( "contribuinte", 8 );
        $obPDF->setAlinhamento ( "R" );
        $obPDF->addCampo       ( "valor", 8 );
        $obPDF->addCampo       ( "", 8 );
        $obPDF->addCampo       ( "", 8 );
        $obPDF->addCampo       ( "", 8 );
        $obPDF->addCampo       ( "valor", 9 );
        $obPDF->setAlinhamento ( "C" );
        $obPDF->addCampo       ( "data_pagamento", 9 );

        $stTotal = "Total Inconsistente:";
        $rsOrigemPagamento = new Recordset;
        $obPDF->addRecordSet( $rsOrigemPagamento );

        $obPDF->setQuebraPaginaLista( false );
        $obPDF->setAlinhamento ( "R" );
        $obPDF->addCabecalho   ( $stTotal, 75, 9, "B" );
        $obPDF->setAlinhamento ( "R" );
        $obPDF->addCabecalho   ( number_format ( $flOrigemValorInconsistenteOK, 2, ',', '.' ), 8, 9, "B" );

        $obPDF->setAlinhamento ( "L" );
        $obPDF->addCampo       ( "", 8 );
        $obPDF->addCampo       ( "", 9 );

    }

    $flSomaTotalOrigem = $flOrigemValorInconsistenteOK + $flOrigemValorTotalOK;

    $stTotal = "Total do Crédito / Grupo:";
    $rsOrigemPagamento = new Recordset;
    $obPDF->addRecordSet( $rsOrigemPagamento );

    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCabecalho   ( $stTotal, 75, 10, "B" );
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCabecalho   ( number_format ( $flSomaTotalOrigem , 2, ',', '.' ), 15, 10, "B" );

    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo       ( "", 8 );
    $obPDF->addCampo       ( "", 9 );

    $rsListaOrigem->proximo();
$i++;
}

    $stFiltroI = str_replace( "lote.", "li.", $stFiltro ). "\n AND EXISTS ( select numeracao FROM arrecadacao.carne WHERE numeracao = li.numeracao AND cod_convenio = -1) ";
    $obTARRPagamento->recuperaResumoLoteListaInconsistente( $rsInconsistentes, $stFiltroI );

    $flOrigemValorInconsistente2OK = 0.00;
    
    if ( $rsInconsistentes->getNumLinhas() > 0 ) {
        while ( !$rsInconsistentes->eof() ) {
            $rsInconsistentes->setCampo( "valor", str_replace ( ',', '.', $rsInconsistentes->getCampo( "valor" ) ) );
            $flOrigemValorInconsistente2OK += $rsInconsistentes->getCampo( "valor" );
            
            $rsInconsistentes->setCampo( "valor",  number_format ( $rsInconsistentes->getCampo( "valor" ) , 2, ',', '.' ) );
            
            $rsInconsistentes->proximo();
        }
        
        $flSomaValorInconsistentes2 += $flOrigemValorInconsistente2OK;
        $rsInconsistentes->setPrimeiroElemento();

        //titulo
        $obPDF->addRecordSet( $rsInconsistentes );

        // Numeração     Parcela     Origem      Inscrição       Contribuinte    Valor (R$)   Pagamento   Situação
        $obPDF->setQuebraPaginaLista( false );
        $obPDF->setAlinhamento ( "L" );
        $obPDF->addCabecalho   ( "INCONSISTENTES DIVIDA ATIVA", 30, 12, "B" );

        $obPDF->addCabecalho   ( "", 11, 10 );
        $obPDF->addCabecalho   ( "", 8, 10 );
        $obPDF->addCabecalho   ( "", 8, 10 );
        $obPDF->addCabecalho   ( "", 8, 10 );
        $obPDF->addCabecalho   ( "", 5, 10 );
        $obPDF->addCabecalho   ( "", 8, 10 );
        $obPDF->addCabecalho   ( "", 14, 10 );

        $obPDF->setAlinhamento ( "L" );
        $obPDF->addCampo       ( "[numeracao]", 9 );
        $obPDF->setAlinhamento ( "C" );
        $obPDF->addCampo       ( "inscricao", 8 );
        $obPDF->setAlinhamento ( "L" );
        $obPDF->addCampo       ( "contribuinte", 8 );
        $obPDF->setAlinhamento ( "R" );
        $obPDF->addCampo       ( "", 8 );
        $obPDF->addCampo       ( "", 8 );
        $obPDF->addCampo       ( "", 8 );
        $obPDF->setAlinhamento ( "C" );
        $obPDF->addCampo       ( "data_pagamento", 9 );
        $obPDF->setAlinhamento ( "R" );
        $obPDF->addCampo       ( "valor", 9 );

        $stTotal = "Total Inconsistente Divida Ativa:";
        $rsOrigemPagamento = new Recordset;
        $obPDF->addRecordSet( $rsOrigemPagamento );

        $obPDF->setQuebraPaginaLista( false );
        $obPDF->setAlinhamento ( "R" );
        $obPDF->addCabecalho   ( $stTotal, 75, 10, "B" );
        $obPDF->addCabecalho   ( number_format ( $flSomaValorInconsistentes2, 2, ',', '.' ), 17, 10, "B" );

        $obPDF->setAlinhamento ( "L" );
        $obPDF->addCampo       ( "", 8 );
        $obPDF->addCampo       ( "", 9 );
    }
    #==========================================================
    // AMOSTRA DE TOTAL INCONSISTENTE SEM VINCULO
    #==========================================================
    $rsInconsistentes = new Recordset;
    $stFiltroI = str_replace( "lote.", "li.", $stFiltro ). "\n AND NOT EXISTS ( select numeracao FROM arrecadacao.carne WHERE numeracao = li.numeracao ) ";
    $obTARRPagamento->recuperaResumoLoteListaInconsistente( $rsInconsistentes, $stFiltroI );
    $flOrigemValorInconsistente2OK = 0.00;
    if ( $rsInconsistentes->getNumLinhas() > 0 ) {

        while ( !$rsInconsistentes->eof() ) {
            $rsInconsistentes->setCampo( "valor", str_replace ( ',', '.', $rsInconsistentes->getCampo( "valor" ) ) );
            //$flValorAtual = str_replace ( ',', '.', $rsInconsistentes->getCampo( "valor" ) );
            //$flOrigemValorInconsistente2OK += $flValorAtual;
            $flOrigemValorInconsistente2OK += $rsInconsistentes->getCampo( "valor" );
            $rsInconsistentes->setCampo( "valor",  number_format ( $rsInconsistentes->getCampo( "valor" ) , 2, ',', '.' ) );
            $rsInconsistentes->proximo();
        }

        $flSomaValorInconsistentes2 += $flOrigemValorInconsistente2OK;
        $rsInconsistentes->setPrimeiroElemento();

        //titulo
        $obPDF->addRecordSet( $rsInconsistentes );

        // Numeração     Parcela     Origem      Inscrição       Contribuinte    Valor (R$)   Pagamento   Situação
        $obPDF->setQuebraPaginaLista( false );
        $obPDF->setAlinhamento ( "L" );
        $obPDF->addCabecalho   ( "INCONSISTENTES SEM VÍNCULO", 30, 12, "B" );

        $obPDF->addCabecalho   ( "", 11, 10 );
        $obPDF->addCabecalho   ( "", 8, 10 );
        $obPDF->addCabecalho   ( "", 8, 10 );
        $obPDF->addCabecalho   ( "", 8, 10 );
        $obPDF->addCabecalho   ( "", 5, 10 );
        $obPDF->addCabecalho   ( "", 8, 10 );
        $obPDF->addCabecalho   ( "", 14, 10 );

        $obPDF->setAlinhamento ( "L" );
        $obPDF->addCampo       ( "[numeracao]", 9, "B" );
        $obPDF->setAlinhamento ( "C" );
        $obPDF->addCampo       ( "inscricao", 8 );
        $obPDF->setAlinhamento ( "L" );
        $obPDF->addCampo       ( "contribuinte", 8 );
        $obPDF->setAlinhamento ( "R" );
        $obPDF->addCampo       ( "", 8 );
        $obPDF->addCampo       ( "", 8 );
        $obPDF->addCampo       ( "", 8 );
        $obPDF->setAlinhamento ( "C" );
        $obPDF->addCampo       ( "data_pagamento", 9 );
        $obPDF->setAlinhamento ( "R" );
        $obPDF->addCampo       ( "valor", 9 );

        $stTotal = "Total Inconsistente Sem Vínculo:";
        $rsOrigemPagamento = new Recordset;
        $obPDF->addRecordSet( $rsOrigemPagamento );

        $obPDF->setQuebraPaginaLista( false );
        $obPDF->setAlinhamento ( "R" );
        $obPDF->addCabecalho   ( $stTotal, 75, 10, "B" );
        $obPDF->addCabecalho   ( number_format ( $flSomaValorInconsistentes2, 2, ',', '.' ), 17, 10, "B" );

        $obPDF->setAlinhamento ( "L" );
        $obPDF->addCampo       ( "", 8 );
        $obPDF->addCampo       ( "", 9 );

    }
    // ================================================== FIM DOS INCONSISTENTES SEM VINCULO

    //ESPAÇAMENTO
    $obPDF->addRecordSet( $rsOrigemPagamento );
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->addCabecalho   ( "", 15, 11, "B" );
    $obPDF->addCampo       ( "", 11 );

    #==========================================================
    // AMOSTRA DE TOTAL DE GRUPOS/CREDITO
    #==========================================================
    $arTotalLote = array();
    $arTotalLote[] = array (
        "normal"    => $flSomaValorNormal,
        "juros"     => $flSomaValorJuros,
        "multa"     => $flSomaValorMulta,
        "diferenca" => $flSomaValorDiff,
        "correcao"  => $flSomaValorCorrecao,
        "total"     => $flSomaValorTotal
    );

    $rsOrigemPagamento = new Recordset;
    $rsOrigemPagamento->preenche ( $arTotalLote );

    $rsOrigemPagamento->addFormatacao ( "normal"    , "NUMERIC_BR" );
    $rsOrigemPagamento->addFormatacao ( "juros"     , "NUMERIC_BR" );
    $rsOrigemPagamento->addFormatacao ( "multa"     , "NUMERIC_BR" );
    $rsOrigemPagamento->addFormatacao ( "diferenca" , "NUMERIC_BR" );
    $rsOrigemPagamento->addFormatacao ( "correcao"  , "NUMERIC_BR" );
    $rsOrigemPagamento->addFormatacao ( "total"     , "NUMERIC_BR" );
    $obPDF->addRecordSet( $rsOrigemPagamento );
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho   ( "", 7, 10 );
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCabecalho   ( "Totais do Relatório:", 30, 12, "B" );
    $obPDF->addCabecalho   ( "Valor", 10, 11, "B" );
    $obPDF->addCabecalho   ( "Juros", 10, 11, "B" );
    $obPDF->addCabecalho   ( "Multa", 10, 11, "B" );
    $obPDF->addCabecalho   ( "Diferença", 10, 11, "B" );
    $obPDF->addCabecalho   ( "Correção", 10, 11, "B" );
    $obPDF->addCabecalho   ( "Total", 15, 11, "B" );

    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCampo       ( ""         , 11 );
    $obPDF->addCampo       ( ""         , 11 );
    $obPDF->addCampo       ( "normal"   , 11, "B" );
    $obPDF->addCampo       ( "juros"    , 11, "B" );
    $obPDF->addCampo       ( "multa"    , 11, "B" );
    $obPDF->addCampo       ( "diferenca", 11, "B" );
    $obPDF->addCampo       ( "correcao" , 11, "B" );
    $obPDF->addCampo       ( "total"    , 11, "B" );

    #==========================================================

    $obPDF->addRecordSet( $rsOrigemPagamento );
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho   ( "", 10, 10 );
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCabecalho   ( "", 30, 12, "B" );
    $obPDF->addCabecalho   ( "", 10, 11, "B" );
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCabecalho   ( "Total Inconsistente:", 25, 12, "B" );
    $obPDF->addCabecalho   ( number_format ($flSomaValorInconsistentes, 2, ',', '.') , 17, 11, "B" );

    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCampo       ( "" , 11 );
    $obPDF->addCampo       ( "" , 11 );
    $obPDF->addCampo       ( "" , 11 );
    $obPDF->addCampo       ( "" , 11 );
    $obPDF->addCampo       ( "" , 11 );

    #==========================================================
    // AMOSTRA DE TOTAL INCONSISTENTE SEM VINCULO
    #==========================================================
    $obPDF->addRecordSet( $rsOrigemPagamento );
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho   ( "", 10, 10 );
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCabecalho   ( "", 30, 12, "B" );
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCabecalho   ( "Total Inconsistente sem Vínculo:", 35, 12, "B" );
    $obPDF->addCabecalho   ( number_format ($flSomaValorInconsistentes2, 2, ',', '.'), 17, 11, "B" );

    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCampo       ( "" , 11 );
    $obPDF->addCampo       ( "" , 11 );
    $obPDF->addCampo       ( "" , 11 );
    $obPDF->addCampo       ( "" , 11 );

    #==========================================================
    // TOTAL GERAL
    #==========================================================
    $obPDF->addRecordSet( $rsOrigemPagamento );
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho   ( "", 5, 10 );
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCabecalho   ( "", 30, 12, "B" );
    $obPDF->addCabecalho   ( "", 10, 11, "B" );
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCabecalho   ( "Total Geral:", 25, 13, "B" );
    $obPDF->addCabecalho   ( number_format(( $flSomaValorInconsistentes + $flSomaValorTotal + $flSomaValorInconsistentes2 ), 2,',','.'),22,12,"B");

    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCampo       ( "" , 11 );
    $obPDF->addCampo       ( "" , 11 );
    $obPDF->addCampo       ( "" , 11 );
    $obPDF->addCampo       ( "" , 11 );
    $obPDF->addCampo       ( "" , 11 );

$obPDF->show();
?>
