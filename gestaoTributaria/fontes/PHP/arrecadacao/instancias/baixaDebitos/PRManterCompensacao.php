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
  * Página de Formulario
  * Data de criação : 12/12/2007

  * @author Analista: Fábio Bertoldi
  * @author Programador: Fernando Piccini Cercato

    * $Id: PRManterCompensacao.php 62838 2015-06-26 13:02:49Z diogo.zarpelon $

  Caso de uso: uc-05.03.10
**/

/*
$Log$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRPagamento.class.php");
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRPagamentoAcrescimo.class.php");
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRPagamentoCalculo.class.php");
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRPagamentoCompensacao.class.php");
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRPagamentoCompensacaoPagas.class.php");
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRCompensacaoUtilizaResto.class.php");
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRCompensacaoResto.class.php");
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRCompensacao.class.php");
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRPagamentoDiferencaCompensacao.class.php");
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRCarne.class.php");
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRCarneDevolucao.class.php");
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRCalculo.class.php");
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRCalculoCgm.class.php");
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRCalculoGrupoCredito.class.php");
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRImovelCalculo.class.php");
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRCadastroEconomicoCalculo.class.php");
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRParcela.class.php");
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaParcela.class.php");
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaParcelaCalculo.class.php");
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRLancamentoCalculo.class.php");
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRLancamento.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ManterCompensacao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";

$obTARRPagamento = new TARRPagamento;
$obTARRPagamentoAcrescimo = new TARRPagamentoAcrescimo;
$obTARRPagamentoCalculo = new TARRPagamentoCalculo;
$obTARRPagamentoCompensacao = new TARRPagamentoCompensacao;
$obTARRPagamentoCompensacaoPagas = new TARRPagamentoCompensacaoPagas;
$obTARRCompensacaoUtilizaResto = new TARRCompensacaoUtilizaResto;
$obTARRCompensacaoResto = new TARRCompensacaoResto;
$obTARRCompensacao = new TARRCompensacao;
$obTARRPagamentoDiferencaCompensacao = new TARRPagamentoDiferencaCompensacao;
$obTARRCarne = new TARRCarne;
$obTARRCarneDevolucao = new TARRCarneDevolucao;
$obTARRParcela = new TARRParcela;
$obTDATDividaParcela = new TDATDividaParcela;
$obTARRLancamentoCalculo = new TARRLancamentoCalculo;
$obTDATParcelaCalculo = new TDATDividaParcelaCalculo;

Sessao::setTrataExcecao( true );
Sessao::getTransacao()->setMapeamento( $obTARRCompensacao );
    $obTARRCompensacao->recuperaProximoCodigoCompensacao( $rsProxCod );

    if ( !$rsProxCod->getCampo("max_cod") )
        $inCodCompensacao = 1;
    else
        $inCodCompensacao = $rsProxCod->getCampo("max_cod") + 1;

    $obTARRCompensacao->setDado ( "cod_compensacao", $inCodCompensacao );
    $obTARRCompensacao->setDado ( "numcgm", Sessao::read('numCgm') );
    $obTARRCompensacao->setDado ( "valor", Sessao::read("total_compensacao") );
    $obTARRCompensacao->setDado ( "aplicar_acrescimos", $_REQUEST["boAplicaAcrescimos"]?true:false );
    if ($_REQUEST["stTipoPagamento"] == "duplicado") {
        $obTARRCompensacao->setDado ( "cod_tipo", 1 );
    } else {
        $obTARRCompensacao->setDado ( "cod_tipo", 2 );
    }

    $obTARRCompensacao->inclusao ();

    if ( Sessao::read("saldo_restante") > 0.00 ) {
        $obTARRCompensacaoResto->setDado( "cod_compensacao", $inCodCompensacao );
        $obTARRCompensacaoResto->setDado( "valor", Sessao::read("saldo_restante") );
        $obTARRCompensacaoResto->inclusao();
    }

    $stFiltro = "";
    if ($_REQUEST['stCGM']) {
        $stFiltro .= " calculo_cgm.numcgm = ".$_REQUEST['stCGM']." AND ";
    }

    if ($_REQUEST['stImovel']) {
        $stFiltro .= " imovel_calculo.inscricao_municipal = ".$_REQUEST['stImovel']." AND ";
    }

    if ($_REQUEST['stEmpresa']) {
        $stFiltro .= " cadastro_economico_calculo.inscricao_economica = ".$_REQUEST['stEmpresa']." AND ";
    }

    $stFiltro = substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    $obTARRPagamentoCompensacao->ListaCarnesPagamentoComResto( $rsListaParcelasResto, $stFiltro );
    while ( !$rsListaParcelasResto->Eof() ) {
        $obTARRCompensacaoUtilizaResto->setDado ( "cod_compensacao", $inCodCompensacao );
        $obTARRCompensacaoUtilizaResto->setDado ( "cod_compensacao_resto", $rsListaParcelasResto->getCampo("cod_compensacao") );
        $obTARRCompensacaoUtilizaResto->inclusao();

        $rsListaParcelasResto->proximo();
    }

    Sessao::remove('parcelas_pagas');
    $arParcelasPagas = array();
    foreach ($_REQUEST as $valor => $key) {
        if ( preg_match( "/boParPaga_[0-9]/", $valor ) ) {
            $arKey = explode( '§', $key );

            if ($_REQUEST["stTipoPagamento"] == "duplicado") {
                $obTARRPagamentoCompensacao->setDado ( "cod_compensacao", $inCodCompensacao );
                $obTARRPagamentoCompensacao->setDado ( "numeracao", $arKey[0] );
                $obTARRPagamentoCompensacao->setDado ( "ocorrencia_pagamento", $arKey[4] );
                $obTARRPagamentoCompensacao->setDado ( "cod_convenio", $arKey[5] );
                $obTARRPagamentoCompensacao->inclusao ();
            } else {
                $arTMPCalculos = explode( "#", $arKey[9] );
                for ( $inX=0; $inX<count( $arTMPCalculos ); $inX++ ) {
                    $obTARRPagamentoDiferencaCompensacao->setDado ( "cod_compensacao", $inCodCompensacao );
                    $obTARRPagamentoDiferencaCompensacao->setDado ( "numeracao", $arKey[0] );
                    $obTARRPagamentoDiferencaCompensacao->setDado ( "ocorrencia_pagamento", $arKey[4] );
                    $obTARRPagamentoDiferencaCompensacao->setDado ( "cod_convenio", $arKey[5] );
                    $obTARRPagamentoDiferencaCompensacao->setDado ( "cod_calculo", $arTMPCalculos[$inX] );
                    $obTARRPagamentoDiferencaCompensacao->inclusao();
                }
            }

            $arParcelasPagas[] = array (
                "numeracao" => $arKey[0]." / ".$arKey[1],
                "parcela" => $arKey[8],
                "origem" => $arKey[6],
                "vencimento" => $arKey[7],
                "valor" => $arKey[2],
                "valor_pago" => $arKey[3]
            );
        }
    }

    Sessao::write( 'parcelas_pagas', $arParcelasPagas );
    Sessao::remove( 'parcelas_vencer' );
    $arParcelasVencer = array();
    $arParcelasNovas = array();
    $flTotalCompensado = Sessao::read("total_compensacao");
    foreach ($_REQUEST as $valor => $key) {
        if ( preg_match( "/boParVenc_[0-9]/", $valor ) ) {

            $arKey = explode( '§', $key );

            if ( $_REQUEST["boAplicaAcrescimos"] )
                $flValorPago = $arKey[3];
            else
                $flValorPago = $arKey[2];

            if ($flTotalCompensado - $flValorPago < 0.00) {
                $flFaltou = $flValorPago - $flTotalCompensado;
                $flValorPago = $flTotalCompensado;
            }else
                $flFaltou = 0.00;

            $arParcelasVencer[] = array (
                "numeracao" => $arKey[0]." / ".$arKey[1],
                "parcela" => $arKey[7],
                "origem" => $arKey[5],
                "vencimento" => $arKey[6],
                "valor" => $arKey[2],
                "valor_pago" => $flValorPago
            );

            $stFiltro = " carne.numeracao = ".$arKey[0];
            if ( $arKey[5] != -1 )
                $obTARRPagamentoCompensacao->ListaCalculosParcela( $rsCalculos, $stFiltro );
            else
                $obTARRPagamentoCompensacao->ListaCalculosParcelaDA( $rsCalculos, $stFiltro );

            if ($flFaltou) {
                $flTotalJuros = 0.00;
                $flTotalMulta = 0.00;
                $flTotalCorrecao = 0.00;
                $flTotalCalculo = 0.00;
                $flTotalCalculo = $rsCalculos->getCampo("valor_parcela");
                while ( !$rsCalculos->Eof() ) {
                    if ($_REQUEST["boAplicaAcrescimos"]) {
                        $flTotalCorrecao += $rsCalculos->getCampo("correcao");
                        $flTotalJuros += $rsCalculos->getCampo("juro");
                        $flTotalMulta += $rsCalculos->getCampo("multa");
                    }

                    $rsCalculos->proximo();
                }

                $flTotalAcrescimos = ($flTotalCorrecao + $flTotalJuros + $flTotalMulta);
                $flSobraPraAcrescimo = $flValorPago;
                $flSobraPraCalculo = $flValorPago - $flTotalAcrescimos;
                $rsCalculos->setPrimeiroElemento();
            }

            if ($arKey[5] != -1) { //parcela de arrecadacao
                //cancelando parcelas por motivo de pagamento de unica/parcela normal
                $stFiltroCarnes = " WHERE p.cod_lancamento = (
                                        SELECT DISTINCT
                                            parcela.cod_lancamento
                                        FROM
                                            arrecadacao.parcela
                                        INNER JOIN
                                            arrecadacao.carne
                                        ON
                                            carne.cod_parcela = parcela.cod_parcela
                                        WHERE
                                            carne.numeracao = '".$arKey[0]."' ) ";

                if ($arKey[7] == 0) { //eh unica
                    $stFiltroCarnes .= " AND p.nr_parcela > 0 ";
                    $inMotivo = 100;
                } else {
                    $stFiltroCarnes .= " AND p.nr_parcela = 0 ";
                    $inMotivo = 101;
                }

                $obTARRCarne->listaParcelasLancamento( $rsListaCarnesCancelar, $stFiltroCarnes );
                while ( !$rsListaCarnesCancelar->Eof() ) {
                    $obTARRCarneDevolucao->setDado( "numeracao", $rsListaCarnesCancelar->getCampo( "numeracao" ) );
                    $obTARRCarneDevolucao->setDado( "cod_motivo", $inMotivo );
                    $obTARRCarneDevolucao->setDado( "dt_devolucao", date( "d/m/Y" ) );
                    $obTARRCarneDevolucao->setDado( "cod_convenio", $rsListaCarnesCancelar->getCampo( "cod_convenio" ) );
                    $obTARRCarneDevolucao->inclusao();
                    $rsListaCarnesCancelar->proximo();
                }
            }

            $obTARRPagamento->setDado( "numeracao", $arKey[0] );
            $obTARRPagamento->setDado( "ocorrencia_pagamento", 1 );
            $obTARRPagamento->setDado( "cod_convenio", $arKey[4] );
            $obTARRPagamento->setDado( "numcgm", Sessao::read('numCgm') );
            $obTARRPagamento->setDado( "data_baixa", date("d/m/Y") );
            $obTARRPagamento->setDado( "data_pagamento", date("d/m/Y") );
            $obTARRPagamento->setDado( "inconsistente", false );
            $obTARRPagamento->setDado( "valor", $flValorPago );
            $obTARRPagamento->setDado( "cod_tipo", 12 );
            $obTARRPagamento->inclusao();

            $obTARRPagamentoCompensacaoPagas->setDado( "cod_compensacao", $inCodCompensacao );
            $obTARRPagamentoCompensacaoPagas->setDado( "numeracao", $arKey[0] );
            $obTARRPagamentoCompensacaoPagas->setDado( "ocorrencia_pagamento", 1 );
            $obTARRPagamentoCompensacaoPagas->setDado( "cod_convenio", $arKey[4] );
            $obTARRPagamentoCompensacaoPagas->inclusao();

            if ($flFaltou) {
                while ( !$rsCalculos->Eof() ) {
                    if ($flSobraPraCalculo > 0.00) {
                        if ( $flSobraPraCalculo - $rsCalculos->getCampo("valor_calculo") >= 0.00 ) {
                            $flValorCalc = $rsCalculos->getCampo("valor_calculo");
                            $flSobraPraCalculo -= $rsCalculos->getCampo("valor_calculo");
                        } else {
                            $flValorCalc = $flSobraPraCalculo;
                            $flSobraPraCalculo = 0.00;
                        }

                        $obTARRPagamentoCalculo->setDado( "numeracao", $arKey[0] );
                        $obTARRPagamentoCalculo->setDado( "cod_calculo", $rsCalculos->getCampo("cod_calculo") );
                        $obTARRPagamentoCalculo->setDado( "ocorrencia_pagamento", 1 );
                        $obTARRPagamentoCalculo->setDado( "cod_convenio", $arKey[4] );
                        $obTARRPagamentoCalculo->setDado( "valor", $flValorCalc );
                        $obTARRPagamentoCalculo->inclusao();
                    }

                    if ($_REQUEST["boAplicaAcrescimos"]) {
                        if ($flSobraPraAcrescimo > 0.00) {
                            if ( $flSobraPraAcrescimo - $rsCalculos->getCampo("correcao") >= 0.00 ) {
                                $flValorAcr = $rsCalculos->getCampo("correcao");
                                $flSobraPraAcrescimo -= $rsCalculos->getCampo("correcao");
                            } else {
                                $flValorAcr = $flSobraPraAcrescimo;
                                $flSobraPraAcrescimo = 0.00;
                            }

                            $obTARRPagamentoAcrescimo->setDado( "numeracao", $arKey[0] );
                            $obTARRPagamentoAcrescimo->setDado( "ocorrencia_pagamento", 1 );
                            $obTARRPagamentoAcrescimo->setDado( "cod_convenio", $arKey[4] );
                            $obTARRPagamentoAcrescimo->setDado( "cod_calculo", $rsCalculos->getCampo("cod_calculo") );
                            $obTARRPagamentoAcrescimo->setDado( "cod_acrescimo", 3 );
                            $obTARRPagamentoAcrescimo->setDado( "cod_tipo", 1 );
                            $obTARRPagamentoAcrescimo->setDado( "valor", $flValorAcr );
                            $obTARRPagamentoAcrescimo->inclusao();
                        }

                        if ($flSobraPraAcrescimo > 0.00) {
                            if ( $flSobraPraAcrescimo - $rsCalculos->getCampo("juro") >= 0.00 ) {
                                $flValorAcr = $rsCalculos->getCampo("juro");
                                $flSobraPraAcrescimo -= $rsCalculos->getCampo("juro");
                            } else {
                                $flValorAcr = $flSobraPraAcrescimo;
                                $flSobraPraAcrescimo = 0.00;
                            }

                            $obTARRPagamentoAcrescimo->setDado( "numeracao", $arKey[0] );
                            $obTARRPagamentoAcrescimo->setDado( "ocorrencia_pagamento", 1 );
                            $obTARRPagamentoAcrescimo->setDado( "cod_convenio", $arKey[4] );
                            $obTARRPagamentoAcrescimo->setDado( "cod_calculo", $rsCalculos->getCampo("cod_calculo") );
                            $obTARRPagamentoAcrescimo->setDado( "cod_acrescimo", 1 );
                            $obTARRPagamentoAcrescimo->setDado( "cod_tipo", 2 );
                            $obTARRPagamentoAcrescimo->setDado( "valor", $flValorAcr );
                            $obTARRPagamentoAcrescimo->inclusao();
                        }

                        if ($flSobraPraAcrescimo > 0.00) {
                            if ( $flSobraPraAcrescimo - $rsCalculos->getCampo("multa") >= 0.00 ) {
                                $flValorAcr = $rsCalculos->getCampo("multa");
                                $flSobraPraAcrescimo -= $rsCalculos->getCampo("multa");
                            } else {
                                $flValorAcr = $flSobraPraAcrescimo;
                                $flSobraPraAcrescimo = 0.00;
                            }

                            $obTARRPagamentoAcrescimo->setDado( "numeracao", $arKey[0] );
                            $obTARRPagamentoAcrescimo->setDado( "ocorrencia_pagamento", 1 );
                            $obTARRPagamentoAcrescimo->setDado( "cod_convenio", $arKey[4] );
                            $obTARRPagamentoAcrescimo->setDado( "cod_calculo", $rsCalculos->getCampo("cod_calculo") );
                            $obTARRPagamentoAcrescimo->setDado( "cod_acrescimo", 2 );
                            $obTARRPagamentoAcrescimo->setDado( "cod_tipo", 3 );
                            $obTARRPagamentoAcrescimo->setDado( "valor", $flValorAcr );
                            $obTARRPagamentoAcrescimo->inclusao();
                        }
                    }

                    $rsCalculos->proximo();
                }

                //criando novo calculo, lancamento e carne para sobras do carne
                $rsCalculos->setPrimeiroElemento();

                $stObservacaoSistema = "Novo lancamento referente a compensação da parcela '".$arKey[7]."', carne '".$arKey[0]."' e origem '".$arKey[5]."'.";
                $obTARRLancamento = new TARRLancamento;
                $obTARRLancamento->proximoCod( $inCodLancamento );
                $obTARRLancamento->setDado( "cod_lancamento", $inCodLancamento );
                $obTARRLancamento->setDado( "vencimento", date("d/m/Y") );
                $obTARRLancamento->setDado( "total_parcelas", 1 );
                $obTARRLancamento->setDado( "ativo", true );
                $obTARRLancamento->setDado( "valor", ($flTotalCalculo + $flTotalAcrescimos) - $flValorPago ); //total a lancar eh o valor total do calculo mais acrescimos menos o total pago
                $obTARRLancamento->setDado( "observacao_sistema", $stObservacaoSistema );
                $obTARRLancamento->setDado( "observacao", $stObservacaoSistema );
                $obTARRLancamento->inclusao();

                unset( $obTARRParcela );
                $obTARRParcela = new TARRParcela;
                $obTARRParcela->proximoCod( $inCodParcela );
                $obTARRParcela->setDado( "cod_parcela", $inCodParcela );
                $obTARRParcela->setDado( "cod_lancamento", $inCodLancamento );
                $obTARRParcela->setDado( "valor", ($flTotalCalculo + $flTotalAcrescimos) - $flValorPago );
                $obTARRParcela->setDado( "vencimento", date("d/m/Y") );
                $obTARRParcela->setDado( "nr_parcela", 1 );
                $obTARRParcela->inclusao();

                if ($flValorPago - $flTotalAcrescimos < 0.00) { //nao conseguiu pagar nem os acrescimos, adiciona valor dos acrescimos ao total por credito
                    while ( !$rsCalculos->Eof() ) {
                        unset ( $obTARRCalculo );
                        $obTARRCalculo = new TARRCalculo;

                        $stFiltroCalculo = " WHERE cod_calculo = ".$rsCalculos->getCampo( "cod_calculo" );
                        unset( $rsCalculo );
                        $obTARRCalculo->recuperaTodos( $rsCalculo, $stFiltroCalculo );

                        $flPorcentoAcrescimo = ( $rsCalculos->getCampo( "valor_calculo" ) * 100 ) / $flTotalCalculo;
                        $flPorcentoAcrescimo = ( ($flTotalAcrescimos - $flValorPago) * $flPorcentoAcrescimo ) / 100;

                        $obTARRCalculo->proximoCod( $inCodCalculo );
                        $obTARRCalculo->setDado( "cod_calculo", $inCodCalculo );
                        $obTARRCalculo->setDado( "cod_credito", $rsCalculo->getCampo( "cod_credito" ) );
                        $obTARRCalculo->setDado( "cod_especie", $rsCalculo->getCampo( "cod_especie" ) );
                        $obTARRCalculo->setDado( "cod_genero", $rsCalculo->getCampo( "cod_genero" ) );
                        $obTARRCalculo->setDado( "cod_natureza", $rsCalculo->getCampo( "cod_natureza" ) );
                        $obTARRCalculo->setDado( "exercicio", $rsCalculo->getCampo( "exercicio" ) );
                        $obTARRCalculo->setDado( "valor", $rsCalculos->getCampo( "valor_calculo" ) + $flPorcentoAcrescimo );
                        $obTARRCalculo->setDado( "nro_parcelas", 1 );
                        $obTARRCalculo->setDado( "calculado", true );
                        $obTARRCalculo->inclusao();

                        unset( $obTARRLancamentoCalculo );
                        $obTARRLancamentoCalculo = new TARRLancamentoCalculo;
                        $obTARRLancamentoCalculo->setDado( "cod_calculo", $inCodCalculo );
                        $obTARRLancamentoCalculo->setDado( "cod_lancamento", $inCodLancamento );
                        $obTARRLancamentoCalculo->setDado( "valor", $rsCalculos->getCampo( "valor_calculo" ) + $flPorcentoAcrescimo );
                        $obTARRLancamentoCalculo->inclusao();

                        unset ( $obTARRCalculoCgm );
                        $obTARRCalculoCgm = new TARRCalculoCgm;
                        unset ( $rsCalculoCGM );
                        $obTARRCalculoCgm->recuperaTodos( $rsCalculoCGM, $stFiltroCalculo );
                        $obTARRCalculoCgm->setDado( "cod_calculo", $inCodCalculo );
                        $obTARRCalculoCgm->setDado( "numcgm", $rsCalculoCGM->getCampo( "numcgm" ) );
                        $obTARRCalculoCgm->inclusao();

                        unset ( $obTARRImovelCalculo );
                        $obTARRImovelCalculo = new TARRImovelCalculo;
                        unset ( $rsImovelCalculo );
                        $obTARRImovelCalculo->recuperaTodos( $rsImovelCalculo, $stFiltroCalculo );
                        if ( !$rsImovelCalculo->Eof() ) {
                            $obTARRImovelCalculo->setDado( "cod_calculo", $inCodCalculo );
                            $obTARRImovelCalculo->setDado( "inscricao_municipal", $rsImovelCalculo->getCampo("inscricao_municipal") );
                            $obTARRImovelCalculo->setDado( "timestamp", $rsImovelCalculo->getCampo("timestamp") );
                            $obTARRImovelCalculo->inclusao();
                        }

                        unset ( $obTARRCadastroEconomicoCalculo );
                        $obTARRCadastroEconomicoCalculo = new TARRCadastroEconomicoCalculo;
                        unset ( $rsCadastroEconomicoCalculo );
                        $obTARRCadastroEconomicoCalculo->recuperaTodos( $rsCadastroEconomicoCalculo, $stFiltroCalculo );
                        if ( !$rsCadastroEconomicoCalculo->Eof() ) {
                            $obTARRCadastroEconomicoCalculo->setDado( "cod_calculo", $inCodCalculo );
                            $obTARRCadastroEconomicoCalculo->setDado( "inscricao_economica", $rsCadastroEconomicoCalculo->getCampo("inscricao_economica") );
                            $obTARRCadastroEconomicoCalculo->setDado( "timestamp", $rsCadastroEconomicoCalculo->getCampo("timestamp") );
                            $obTARRCadastroEconomicoCalculo->inclusao();
                        }

                        unset( $obTARRCalculoGrupoCredito );
                        $obTARRCalculoGrupoCredito = new TARRCalculoGrupoCredito;
                        unset( $rsCalculoGrupoCredito );
                        $obTARRCalculoGrupoCredito->recuperaTodos( $rsCalculoGrupoCredito, $stFiltroCalculo );
                        $obTARRCalculoGrupoCredito->setDado( "cod_calculo", $inCodCalculo );
                        $obTARRCalculoGrupoCredito->setDado( "cod_grupo", $rsCalculoGrupoCredito->getCampo("cod_grupo") );
                        $obTARRCalculoGrupoCredito->setDado( "ano_exercicio", $rsCalculoGrupoCredito->getCampo("ano_exercicio") );
                        $obTARRCalculoGrupoCredito->inclusao();

                        //----------------------------
                        $rsCalculos->proximo();
                    }
                } else { //pagou todos acrescimos, faltou apenas creditos
                    $flSobraPraCalculo = $flValorPago - $flTotalAcrescimos;
                    while ( !$rsCalculos->Eof() ) {
                        unset ( $obTARRCalculo );
                        $obTARRCalculo = new TARRCalculo;

                        $stFiltroCalculo = " WHERE cod_calculo = ".$rsCalculos->getCampo( "cod_calculo" );
                        unset( $rsCalculo );
                        $obTARRCalculo->recuperaTodos( $rsCalculo, $stFiltroCalculo );

                        if ($flSobraPraCalculo > 0.00) {
                            if ( $flSobraPraCalculo - $rsCalculos->getCampo("valor_calculo") >= 0.00 ) {
                                $flValorCalc = 0.00;
                                $flSobraPraCalculo -= $rsCalculos->getCampo("valor_calculo");
                                $rsCalculos->proximo();
                                continue;
                            } else {
                                $flValorCalc = $rsCalculos->getCampo("valor_calculo") - $flSobraPraCalculo;
                                $flSobraPraCalculo = 0.00;
                            }
                        } else {
                            $flValorCalc = $rsCalculos->getCampo("valor_calculo");
                        }

                        $obTARRCalculo->proximoCod( $inCodCalculo );
                        $obTARRCalculo->setDado( "cod_calculo", $inCodCalculo );
                        $obTARRCalculo->setDado( "cod_credito", $rsCalculo->getCampo( "cod_credito" ) );
                        $obTARRCalculo->setDado( "cod_especie", $rsCalculo->getCampo( "cod_especie" ) );
                        $obTARRCalculo->setDado( "cod_genero", $rsCalculo->getCampo( "cod_genero" ) );
                        $obTARRCalculo->setDado( "cod_natureza", $rsCalculo->getCampo( "cod_natureza" ) );
                        $obTARRCalculo->setDado( "exercicio", $rsCalculo->getCampo( "exercicio" ) );
                        $obTARRCalculo->setDado( "valor", $flValorCalc );
                        $obTARRCalculo->setDado( "nro_parcelas", 1 );
                        $obTARRCalculo->setDado( "calculado", true );
                        $obTARRCalculo->inclusao();

                        unset( $obTARRLancamentoCalculo );
                        $obTARRLancamentoCalculo = new TARRLancamentoCalculo;
                        $obTARRLancamentoCalculo->setDado( "cod_calculo", $inCodCalculo );
                        $obTARRLancamentoCalculo->setDado( "cod_lancamento", $inCodLancamento );
                        $obTARRLancamentoCalculo->setDado( "valor", $flValorCalc );
                        $obTARRLancamentoCalculo->inclusao();

                        unset ( $obTARRCalculoCgm );
                        $obTARRCalculoCgm = new TARRCalculoCgm;
                        unset ( $rsCalculoCGM );
                        $obTARRCalculoCgm->recuperaTodos( $rsCalculoCGM, $stFiltroCalculo );
                        $inCGMCalc = $rsCalculoCGM->getCampo( "numcgm" );

                        $obTARRCalculoCgm->setDado( "cod_calculo", $inCodCalculo );
                        $obTARRCalculoCgm->setDado( "numcgm", $inCGMCalc );
                        $obTARRCalculoCgm->inclusao();

                        unset ( $obTARRImovelCalculo );
                        $obTARRImovelCalculo = new TARRImovelCalculo;
                        unset ( $rsImovelCalculo );
                        $obTARRImovelCalculo->recuperaTodos( $rsImovelCalculo, $stFiltroCalculo );
                        if ( !$rsImovelCalculo->Eof() ) {
                            $obTARRImovelCalculo->setDado( "cod_calculo", $inCodCalculo );
                            $obTARRImovelCalculo->setDado( "inscricao_municipal", $rsImovelCalculo->getCampo("inscricao_municipal") );
                            $obTARRImovelCalculo->setDado( "timestamp", $rsImovelCalculo->getCampo("timestamp") );
                            $obTARRImovelCalculo->inclusao();
                        }

                        unset ( $obTARRCadastroEconomicoCalculo );
                        $obTARRCadastroEconomicoCalculo = new TARRCadastroEconomicoCalculo;
                        unset ( $rsCadastroEconomicoCalculo );
                        $obTARRCadastroEconomicoCalculo->recuperaTodos( $rsCadastroEconomicoCalculo, $stFiltroCalculo );
                        if ( !$rsCadastroEconomicoCalculo->Eof() ) {
                            $obTARRCadastroEconomicoCalculo->setDado( "cod_calculo", $inCodCalculo );
                            $obTARRCadastroEconomicoCalculo->setDado( "inscricao_economica", $rsCadastroEconomicoCalculo->getCampo("inscricao_economica") );
                            $obTARRCadastroEconomicoCalculo->setDado( "timestamp", $rsCadastroEconomicoCalculo->getCampo("timestamp") );
                            $obTARRCadastroEconomicoCalculo->inclusao();
                        }

                        unset( $obTARRCalculoGrupoCredito );
                        $obTARRCalculoGrupoCredito = new TARRCalculoGrupoCredito;
                        unset( $rsCalculoGrupoCredito );
                        $obTARRCalculoGrupoCredito->recuperaTodos( $rsCalculoGrupoCredito, $stFiltroCalculo );
                        $obTARRCalculoGrupoCredito->setDado( "cod_calculo", $inCodCalculo );
                        $obTARRCalculoGrupoCredito->setDado( "cod_grupo", $rsCalculoGrupoCredito->getCampo("cod_grupo") );
                        $obTARRCalculoGrupoCredito->setDado( "ano_exercicio", $rsCalculoGrupoCredito->getCampo("ano_exercicio") );
                        $obTARRCalculoGrupoCredito->inclusao();

                        //----------------------------
                        $rsCalculos->proximo();
                    }
                }

                unset( $obTARRCarne );
                $obTARRCarne = new TARRCarne;
                $obTARRCarne->recuperaNumeracaoParaCompensacao ( $rsFuncaoNumeracao, $inCodCalculo );
                if ( $rsFuncaoNumeracao->getCampo( "cod_carteira" ) ) {
                    $stSql = " SELECT ".$rsFuncaoNumeracao->getCampo( "nom_funcao" )."('".$rsFuncaoNumeracao->getCampo( "cod_carteira" )."', '".$rsFuncaoNumeracao->getCampo( "cod_convenio" )."' ) AS numeracao";
                } else {
                    $stSql = " SELECT ".$rsFuncaoNumeracao->getCampo( "nom_funcao" )."('', '".$rsFuncaoNumeracao->getCampo( "cod_convenio" )."' ) AS numeracao";
                }

                $obConexao   = new Conexao;
                $rsNumeracao = new RecordSet;
                //---------------------------
                $obConexao->executaSQL( $rsNumeracao, $stSql );
                $obTARRCarne->setDado( "cod_parcela", $inCodParcela );
                $obTARRCarne->setDado( "numeracao", $rsNumeracao->getCampo("numeracao") );
                $obTARRCarne->setDado( "cod_convenio", $rsFuncaoNumeracao->getCampo( "cod_convenio" ) );
                $obTARRCarne->setDado( "cod_carteira", $rsFuncaoNumeracao->getCampo( "cod_carteira" ) );
                $obTARRCarne->setDado( "exercicio", date("Y") );
                $obTARRCarne->setDado( "impresso", false );
                $obTARRCarne->inclusao();

                $arParcelasNovas[] = array(
                    "numcgm" => $inCGMCalc,
                    "cod_lancamento" => $inCodLancamento,
                    "cod_parcela" => $inCodParcela,
                    "numeracao_sem_exercicio" => $rsNumeracao->getCampo("numeracao"),
                    "exercicio" => date("Y"),
                    "numeracao" => $rsNumeracao->getCampo("numeracao")." / ".date("Y"),
                    "parcela" => "1",
                    "origem" => $arKey[5],
                    "vencimento" => date("d/m/Y"),
                    "valor" => (($flTotalCalculo + $flTotalAcrescimos) - $flValorPago )
                );
            } else {
                while ( !$rsCalculos->Eof() ) {
                    $obTARRPagamentoCalculo->setDado( "numeracao", $arKey[0] );
                    $obTARRPagamentoCalculo->setDado( "cod_calculo", $rsCalculos->getCampo("cod_calculo") );
                    $obTARRPagamentoCalculo->setDado( "ocorrencia_pagamento", 1 );
                    $obTARRPagamentoCalculo->setDado( "cod_convenio", $arKey[4] );
                    $obTARRPagamentoCalculo->setDado( "valor", $rsCalculos->getCampo("valor_calculo") );
                    $obTARRPagamentoCalculo->inclusao();

                    if ($_REQUEST["boAplicaAcrescimos"]) {
                        $obTARRPagamentoAcrescimo->setDado( "numeracao", $arKey[0] );
                        $obTARRPagamentoAcrescimo->setDado( "ocorrencia_pagamento", 1 );
                        $obTARRPagamentoAcrescimo->setDado( "cod_convenio", $arKey[4] );
                        $obTARRPagamentoAcrescimo->setDado( "cod_calculo", $rsCalculos->getCampo("cod_calculo") );
                        $obTARRPagamentoAcrescimo->setDado( "cod_acrescimo", 3 );
                        $obTARRPagamentoAcrescimo->setDado( "cod_tipo", 1 );
                        $obTARRPagamentoAcrescimo->setDado( "valor", $rsCalculos->getCampo("correcao") );
                        $obTARRPagamentoAcrescimo->inclusao();

                        $obTARRPagamentoAcrescimo->setDado( "numeracao", $arKey[0] );
                        $obTARRPagamentoAcrescimo->setDado( "ocorrencia_pagamento", 1 );
                        $obTARRPagamentoAcrescimo->setDado( "cod_convenio", $arKey[4] );
                        $obTARRPagamentoAcrescimo->setDado( "cod_calculo", $rsCalculos->getCampo("cod_calculo") );
                        $obTARRPagamentoAcrescimo->setDado( "cod_acrescimo", 1 );
                        $obTARRPagamentoAcrescimo->setDado( "cod_tipo", 2 );
                        $obTARRPagamentoAcrescimo->setDado( "valor", $rsCalculos->getCampo("juro") );
                        $obTARRPagamentoAcrescimo->inclusao();

                        $obTARRPagamentoAcrescimo->setDado( "numeracao", $arKey[0] );
                        $obTARRPagamentoAcrescimo->setDado( "ocorrencia_pagamento", 1 );
                        $obTARRPagamentoAcrescimo->setDado( "cod_convenio", $arKey[4] );
                        $obTARRPagamentoAcrescimo->setDado( "cod_calculo", $rsCalculos->getCampo("cod_calculo") );
                        $obTARRPagamentoAcrescimo->setDado( "cod_acrescimo", 2 );
                        $obTARRPagamentoAcrescimo->setDado( "cod_tipo", 3 );
                        $obTARRPagamentoAcrescimo->setDado( "valor", $rsCalculos->getCampo("multa") );
                        $obTARRPagamentoAcrescimo->inclusao();
                    }

                    $rsCalculos->proximo();
                }
            }

            //CONTROLA SE A PARCELA NA DIVIDA ESTA PAGA OU NAO (caso for parcela de dívida)
            $stFiltro  = " WHERE numeracao    = ".$arKey[0];
            $stFiltro .= "   AND cod_convenio = ".$arKey[4];
            $obTARRCarne->recuperaTodos($rsCarne, $stFiltro);

            if ($rsCarne->getNumLinhas() > 0) {
                $stFiltro  = " WHERE cod_parcela = ".$rsCarne->getCampo('cod_parcela');
                $obTARRParcela->recuperaTodos($rsParcela, $stFiltro);
                if ($rsParcela->getNumLinhas() > 0) {
                    $stFiltro  = " WHERE cod_lancamento = ".$rsParcela->getCampo('cod_lancamento');
                    $obTARRLancamentoCalculo->recuperaTodos($rsLancamento, $stFiltro);
                    if ($rsLancamento->getNumLinhas() > 0) {
                        $stFiltro  = " WHERE num_parcela = ".$rsParcela->getCampo('nr_parcela');
                        $stFiltro .= "   AND cod_calculo = ".$rsLancamento->getCampo('cod_calculo');
                        $obTDATParcelaCalculo->recuperaTodos($rsParcelaCalculo, $stFiltro);
                        if ($rsParcelaCalculo->getNumLinhas() > 0) {
                            $obTDATDividaParcela->setDado('num_parcelamento' , $rsParcelaCalculo->getCampo('num_parcelamento') );
                            $obTDATDividaParcela->setDado('num_parcela'      , $rsParcela->getCampo('nr_parcela')              );
                            $obTDATDividaParcela->setDado('paga'             , 't'                                             );
                            $obTDATDividaParcela->alteracao();
                        }
                    }
                }
            }
        }
    }

    Sessao::write( 'parcelas_vencer', $arParcelasVencer );
    Sessao::write( 'parcelas_novas', $arParcelasNovas );
//echo "finito<br>";exit;
Sessao::encerraExcecao();

if ($_REQUEST["boEmitirRelatorio"]) {
    echo "<script type=\"text/javascript\">\r\n";
    echo "    var sAux = window.open('OCGeraRelatorioCompensacao.php?".Sessao::getId()."','','width=20,height=10,resizable=1,scrollbars=1,left=100,top=100');\r\n";
    echo "    eval(sAux)\r\n";
    echo "</script>\r\n";
}

sistemaLegado::alertaAviso( $pgFilt."?".Sessao::getId()."&stAcao=incluir","Compensar Pagamentos", "incluir","aviso", Sessao::getId(), "../");
