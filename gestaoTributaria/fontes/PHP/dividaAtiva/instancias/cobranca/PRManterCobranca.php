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
  * Página de Processamento da Cobranca
  * Data de criação : 23/01/2007

  * @author Analista: Fábio Bertoldi
  * @author Programador: Fernando Piccini Cercato

    * $Id: PRManterCobranca.php 65602 2016-06-01 17:44:39Z evandro $

    Caso de uso: uc-05.04.04
**/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_ARR_NEGOCIO."RARRCarne.class.php" );
include_once ( CAM_GT_ARR_NEGOCIO."RARRCalculo.class.php" );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaAtiva.class.php" );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaParcelamento.class.php" );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATParcelamento.class.php" );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaParcela.class.php" );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaParcelaOrigem.class.php" );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATModalidade.class.php" );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATModalidadeReducao.class.php" );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaDocumentoParcela.class.php" );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaDocumento.class.php" );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaParcelaAcrescimo.class.php" );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaParcelaReducao.class.php" );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaParcelaCalculo.class.php" );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRCalculo.class.php");
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATParcelamentoCancelamento.class.php" );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRCalculoCgm.class.php" );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRCadastroEconomicoCalculo.class.php" );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRCadastroEconomicoFaturamento.class.php" );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRImovelCalculo.class.php" );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRImovelVVenal.class.php" );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRLancamento.class.php" );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRLancamentoCalculo.class.php" );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRParcela.class.php" );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRCarne.class.php" );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRCarneDevolucao.class.php" );
include_once ( CAM_GT_MON_MAPEAMENTO."TMONConvenio.class.php" );
include_once ( CAM_GT_DAT_MAPEAMENTO."FDATBuscaSaldoDivida.class.php" );

$stAcao = $request->get('stAcao');

$stLink = Sessao::read('stLink');

//Define o nome dos arquivos PHP
$stPrograma = "ManterCobranca";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgOcul = "OC".$stPrograma.".php";

switch ($stAcao) {
    case "inscrever":
    case "cobrar":
/*
        Art. 163. Existindo simultaneamente dois ou mais débitos vencidos do mesmo sujeito passivo para com a mesma pessoa jurídica de direito público, relativos ao mesmo ou a diferentes tributos ou provenientes de penalidade pecuniária ou juros de mora, a autoridade administrativa competente para receber o pagamento determinará a respectiva imputação, obedecidas as seguintes regras, na ordem em que enumeradas:

        I - em primeiro lugar, aos débitos por obrigação própria, e em segundo lugar aos decorrentes de responsabilidade tributária;
        II - primeiramente, às contribuições de melhoria, depois às taxas e por fim aos impostos;
        III - na ordem crescente dos prazos de prescrição;
        IV - na ordem decrescente dos montantes.

        SELECT * from monetario.natureza_credito ;
        SELECT * from monetario.especie_credito ;
*/

        if ( ($_REQUEST["emissao_carnes"] == "local") && ( !$_REQUEST["cmbModelo"] ) ) {
            SistemaLegado::exibeAviso( "O campo 'Modelo' está vazio.", "n_incluir", "erro" );
            exit;
        }

        $arEmissao = array();

        $obTDATDividaAtiva = new TDATDividaAtiva;
        $obTDATDividaParcelamento = new TDATDividaParcelamento;
        $obTDATParcelamento = new TDATParcelamento;
        $obTDATDividaParcela = new TDATDividaParcela;
        $obTDATModalidade = new TDATModalidade;
        $obTDATDividaDocumentoParcela = new TDATDividaDocumentoParcela;
        $obTDATDividaDocumento = new TDATDividaDocumento;
        $obTDATDividaParcelaAcrescimo = new TDATDividaParcelaAcrescimo;
        $obTDATDividaParcelaReducao = new TDATDividaParcelaReducao;
        $obTDATDividaParcelaOrigem = new TDATDividaParcelaOrigem;
        $obTDATDividaParcelaCalculo = new TDATDividaParcelaCalculo;

        $obTARRCarneDevolucao = new TARRCarneDevolucao;
        $obTARRCalculo = new TARRCalculo;
        $obTARRCalculoCgm = new TARRCalculoCgm;
        $obTARRCadastroEconomicoCalculo = new TARRCadastroEconomicoCalculo;
        $obTARRImovelCalculo = new TARRImovelCalculo;
        $obTARRCadastroEconomicoFaturamento = new TARRCadastroEconomicoFaturamento;
        $obTARRImovelVVenal = new TARRImovelVVenal;

        $obTARRLancamento = new TARRLancamento;
        $obTARRLancamentoCalculo = new TARRLancamentoCalculo;
        $obTARRParcela = new TARRParcela;
        $obTARRCarne = new TARRCarne;

        $stNumeroParcelamento = "";

        $arDadosSessao = Sessao::read( "dados" );
        $arParcelasSessao = Sessao::read( "parcelas" );
        $arCobrancaJudicial = Sessao::read( "cobrancaJudicial" );

        Sessao::setTrataExcecao( true );
        Sessao::getTransacao()->setMapeamento( $obTDATDividaParcelamento );

            $inTotalFiltroParcelas = 0;
            $flValorTotalOriginal = 0;
            //$flValorTotalAcrescimo = 0;
            $flValorTotalFinal = 0;
            $flValorTotalReducao = 0;
            $stFiltroParcelas = "";
            $stFiltroParcelasVariasInscricoes = " WHERE ";
            $inTotalDeParcelamentos = 0;
            $inTotalDados = count( $arDadosSessao );
            $nuTotalReducaoCredito = 0;

            for ($inX=0; $inX<$inTotalDados; $inX++) {

                $flValorTotalReducao += $arDadosSessao[$inX]["vlr_reducao"];

                $flValorTotalFinal += $arDadosSessao[$inX]["vlr_final"];
                $flValorTotalOriginal += $arDadosSessao[$inX]["vlr_parcela"];

                $arDadosSessao[$inX]["creditos_separados"] = array();
                $arDadosSessao[$inX]["total_creditos_separados"] = 0;

                //CALCULA O VALOR DAS PARCELAS ORIGEM
                //busca os valores originais das parcelas
                $obTDATDividaParcelaOrigem->setDado('cod_inscricao',$arDadosSessao[$inX]["cod_inscricao"]);
                $obTDATDividaParcelaOrigem->setDado('exercicio',$arDadosSessao[$inX]["exercicio"]);
                $obTDATDividaParcelaOrigem->recuperaParcelaOrigemPorIscricao($rsParcelasOrigem,'','ORDER BY parcela_origem.cod_parcela');                

                //busca a situação atual da inscrição
                $obFDATBuscaSaldoDivida = new FDATBuscaSaldoDivida;
                $obFDATBuscaSaldoDivida->setDado('inInscDivida',$arDadosSessao[$inX]["cod_inscricao"]);
                $obFDATBuscaSaldoDivida->setDado('stExercicio',$arDadosSessao[$inX]["exercicio"]);
                $obFDATBuscaSaldoDivida->recuperaTodos($rsSaldo);
                //calcula o saldo do que já foi pago em relação ao valor origem
                $nuValorPago = $rsSaldo->getCampo('valor') - $rsSaldo->getCampo('valor_corrigido');

                //percorre as parcelas eleiminando o valor que já foi pago.
                //conforme os valores das parcelas forem menores que o valor pago elas vão sendo eleminadas.
                $arDadosSessao[$inX]["num_parcelamento"] = '';
                $nuValorParcela = 0.00;
                while (!$rsParcelasOrigem->eof()) {
                    if ($nuValorPago > 0) {
                        if ( $nuValorPago >= $rsParcelasOrigem->getCampo('valor') ) {
                            $nuValorPago = $nuValorPago - $rsParcelasOrigem->getCampo('valor');
                            $rsParcelasOrigem->proximo();
                            continue;
                        } else {
                            $nuValorParcela = $rsParcelasOrigem->getCampo('valor') - $nuValorPago;
                            $nuValorPago = 0;
                        }
                    } else {
                        $nuValorParcela  = $rsParcelasOrigem->getCampo('valor');
                    }
                    if ($arDadosSessao[$inX]["num_parcelamento"] == '') {
                        $arDadosSessao[$inX]["num_parcelamento"] = $rsParcelasOrigem->getCampo('num_parcelamento_ultimo');
                    }

                    $arDadosSessao[$inX]["creditos_separados"][ $arDadosSessao[$inX]["total_creditos_separados"] ]["cod_credito"]      = $rsParcelasOrigem->getCampo('cod_credito');
                    $arDadosSessao[$inX]["creditos_separados"][ $arDadosSessao[$inX]["total_creditos_separados"] ]["cod_especie"]      = $rsParcelasOrigem->getCampo('cod_especie');
                    $arDadosSessao[$inX]["creditos_separados"][ $arDadosSessao[$inX]["total_creditos_separados"] ]["cod_genero"]       = $rsParcelasOrigem->getCampo('cod_genero');
                    $arDadosSessao[$inX]["creditos_separados"][ $arDadosSessao[$inX]["total_creditos_separados"] ]["cod_natureza"]     = $rsParcelasOrigem->getCampo('cod_natureza');
                    $arDadosSessao[$inX]["creditos_separados"][ $arDadosSessao[$inX]["total_creditos_separados"] ]["cod_parcela"]      = $rsParcelasOrigem->getCampo('cod_parcela');
                    $arDadosSessao[$inX]["creditos_separados"][ $arDadosSessao[$inX]["total_creditos_separados"] ]["num_parcelamento"] = $rsParcelasOrigem->getCampo('num_parcelamento_ultimo');
                    $arDadosSessao[$inX]["creditos_separados"][ $arDadosSessao[$inX]["total_creditos_separados"] ]["valor"]            = $nuValorParcela;
                    $arDadosSessao[$inX]["total_creditos_separados"]++;
                    $rsParcelasOrigem->proximo();
                }

                $arDadosSessao[$inX]["creditos_agrupados"] = array();
                $arDadosSessao[$inX]["total_creditos_agrupados"] = 0;
                for ($inA=0; $inA < $arDadosSessao[$inX]["total_creditos_separados"]; $inA++) {
                    $boJaNaLista = false;
                    for ($inE=0; $inE<$arDadosSessao[$inX]["total_creditos_agrupados"]; $inE++) {
                        if ( ($arDadosSessao[$inX]["creditos_agrupados"][$inE]["cod_credito"] == $arDadosSessao[$inX]["creditos_separados"][$inA]["cod_credito"]) &&
                             ($arDadosSessao[$inX]["creditos_agrupados"][$inE]["cod_especie"] == $arDadosSessao[$inX]["creditos_separados"][$inA]["cod_especie"]) &&
                             ($arDadosSessao[$inX]["creditos_agrupados"][$inE]["cod_genero"] == $arDadosSessao[$inX]["creditos_separados"][$inA]["cod_genero"]) &&
                             ($arDadosSessao[$inX]["creditos_agrupados"][$inE]["cod_natureza"] == $arDadosSessao[$inX]["creditos_separados"][$inA]["cod_natureza"])
                           ) {

                            $boJaNaLista = true;
                            $arDadosSessao[$inX]["creditos_agrupados"][$inE]["valor"] += $arDadosSessao[$inX]["creditos_separados"][$inA]["valor"];
                            break;
                        }
                    }

                    if (!$boJaNaLista) {
                        $inProximaPosicao = $arDadosSessao[$inX]["total_creditos_agrupados"];
                        $arDadosSessao[$inX]["creditos_agrupados"][$inProximaPosicao] = $arDadosSessao[$inX]["creditos_separados"][$inA];
                        $arDadosSessao[$inX]["total_creditos_agrupados"] = $inProximaPosicao+1;
                    }
                }

                $flValorTotalAcrescimo = array();
                for ($inA=0; $inA < $arDadosSessao[$inX]["total_creditos_agrupados"]; $inA++) {
                    #$arDataAtual = explode( "/", $arParcelasSessao[0]["data_vencimento"] );
                    #$stDataTMP = $arDataAtual[2]."-".$arDataAtual[1]."-".$arDataAtual[0];
                    $stDataTMP = $_REQUEST["stDataVenc"];//$arParcelasSessao[0]["data_vencimento"];

                    $obTDATDividaAtiva->aplicaReducaoModalidadeCredito( $rsListaReducao, $_REQUEST["inCodModalidade"], $arDadosSessao[$inX]["inscricao"], $arDadosSessao[$inX]["creditos_agrupados"][$inA]["valor"], $arDadosSessao[$inX]["creditos_agrupados"][$inA]["cod_credito"], $arDadosSessao[$inX]["creditos_agrupados"][$inA]["cod_especie"], $arDadosSessao[$inX]["creditos_agrupados"][$inA]["cod_genero"], $arDadosSessao[$inX]["creditos_agrupados"][$inA]["cod_natureza"], $stDataTMP, count( $arParcelasSessao ) );
                    $arDadosSessao[$inX]["creditos_agrupados"][$inA]["reducao_credito"] = $rsListaReducao->getCampo("valor");

                    $nuTotalReducaoCredito += $rsListaReducao->getCampo("valor");

                    if ( ( $inA+1 == $arDadosSessao[$inX]["total_creditos_agrupados"] ) && ($arDadosSessao[$inX]["total_creditos_agrupados"] != 1) ) {
                        //arredondamento do acrescimo
                        $flValorTotalAcrescimo[1] = $flValorTotalAcrescimo[2] = $flValorTotalAcrescimo[3] = 0;
                        $flValorTotalReducaoCA = 0;
                        for ($inS=0; $inS<$arDadosSessao[$inX]["total_creditos_agrupados"]-1; $inS++) {
                            $flValorTotalReducaoCA += $arDadosSessao[$inX]["creditos_agrupados"][$inS]["total_reducao_creditos_agrupados"];

                            for ($inD=0; $inD<$arDadosSessao[$inX]["creditos_agrupados"][$inS]["total_acrescimos_creditos_agrupados"]; $inD++) {
                                $flValorTotalAcrescimo[ $arDadosSessao[$inX]["creditos_agrupados"][$inS]["acrescimos_creditos_agrupados"][$inD]["cod_tipo"] ] += $arDadosSessao[$inX]["creditos_agrupados"][$inS]["acrescimos_creditos_agrupados"][$inD]["valor_acrescimo"];
                                $inCodAcrescimo[ $arDadosSessao[$inX]["creditos_agrupados"][$inS]["acrescimos_creditos_agrupados"][$inD]["cod_tipo"] ] = $arDadosSessao[$inX]["creditos_agrupados"][$inS]["acrescimos_creditos_agrupados"][$inD]["cod_acrescimo"];
                            }
                        }

                        $flValorTotalAcrescimo[2] = $arDadosSessao[$inX]["juros"] - $flValorTotalAcrescimo[2];
                        $flValorTotalAcrescimo[3] = $arDadosSessao[$inX]["multa"] - $flValorTotalAcrescimo[3];
                        $flValorTotalAcrescimo[1] = $arDadosSessao[$inX]["correcao"] - $flValorTotalAcrescimo[1];

                        $inTotalAcrescimos = 0;
                        for ($inS=0; $inS<3; $inS++) {
                            if ($flValorTotalAcrescimo[$inS+1] > 0.00) {
                                $arDadosSessao[$inX]["creditos_agrupados"][$inA]["acrescimos_creditos_agrupados"][$inTotalAcrescimos]["cod_tipo"] = $inS+1;
                                $arDadosSessao[$inX]["creditos_agrupados"][$inA]["acrescimos_creditos_agrupados"][$inTotalAcrescimos]["cod_acrescimo"] = $inCodAcrescimo[$inS+1];
                                $arDadosSessao[$inX]["creditos_agrupados"][$inA]["acrescimos_creditos_agrupados"][$inTotalAcrescimos]["valor_acrescimo"] = $flValorTotalAcrescimo[$inS+1];

                                #$arDataAtual = explode( "/", $arParcelasSessao[0]["data_vencimento"] );
                                #$stDataTMP = $arDataAtual[2]."-".$arDataAtual[1]."-".$arDataAtual[0];
                                $stDataTMP = $_REQUEST["stDataVenc"];//$arParcelasSessao[0]["data_vencimento"];

                                $obTDATDividaAtiva->aplicaReducaoModalidadeAcrescimo( $rsListaReducao, $_REQUEST["inCodModalidade"], $arDadosSessao[$inX]["inscricao"], $flValorTotalAcrescimo[$inS+1], $inCodAcrescimo[$inS+1], $inS+1, $stDataTMP, count( $arParcelasSessao ) );

                                $stTipoAcrescimo = array( 1 => "reducao_correcao", 2 => "reducao_juros", 3 => "reducao_multa" );

                                $arDadosSessao[$inX]["creditos_agrupados"][$inA]["acrescimos_creditos_agrupados"][$inTotalAcrescimos][$stTipoAcrescimo[$inS+1]] = $rsListaReducao->getCampo("valor");

                                $inTotalAcrescimos++;
                            }
                        }

                        $arDadosSessao[$inX]["creditos_agrupados"][$inA]["total_acrescimos_creditos_agrupados"] = $inTotalAcrescimos;

                        //arredondamento da reducao
                        $flValorTotalReducaoCA = $arDadosSessao[$inX]["vlr_reducao"] - $flValorTotalReducaoCA;
                        $arDadosSessao[$inX]["creditos_agrupados"][$inA]["total_reducao_creditos_agrupados"] = $flValorTotalReducaoCA;

                    } else {

                        $flValorParaInserir = $arDadosSessao[$inX]["creditos_agrupados"][$inA]["valor"];

                        $stDataAtual = $_REQUEST["stDataVenc"]; //$arParcelasSessao[0]["data_vencimento"];
                        $stDataAntiga = $arDadosSessao[$inX]["dt_vencimento_original"];

                        #$arDataAtual = explode( "/", $stDataAtual );
                        #$stDataAtual = $arDataAtual[2]."-".$arDataAtual[1]."-".$arDataAtual[0];

                        #$arDataAntiga = explode( "/", $stDataAntiga );
                        #$stDataAntiga = $arDataAntiga[2]."-".$arDataAntiga[1]."-".$arDataAntiga[0];
                        $inTipoCobranca = $arCobrancaJudicial?1:0;
                        $obTDATDividaAtiva->recuperaJurosMulta( $rsListaJurosMulta, $inTipoCobranca, $arDadosSessao[$inX]["cod_inscricao"], $arDadosSessao[$inX]["exercicio"], $_REQUEST["inCodModalidade"], 0, $flValorParaInserir, $stDataAntiga, $stDataAtual, 'false' );

                        $arDadosSessao[$inX]["creditos_agrupados"][$inA]["acrescimos_creditos_agrupados"] = array();
                        $arDadosSessao[$inX]["creditos_agrupados"][$inA]["total_acrescimos_creditos_agrupados"] = 0;

                        $arTmpJuros = explode( ";", $rsListaJurosMulta->getCampo("juros") );

                        $inTotalJuros = count( $arTmpJuros );
                        $inJurosAtual = 1;
                        $inTotalAcrescimos = 0;
                        while ($inJurosAtual < $inTotalJuros) {
                            //posicao 1 = valor, 2 = acrescimo, 3 = tipo
                            if ($arTmpJuros[$inJurosAtual] > 0) {
                                $arDadosSessao[$inX]["creditos_agrupados"][$inA]["acrescimos_creditos_agrupados"][$inTotalAcrescimos]["cod_tipo"] = $arTmpJuros[2+$inJurosAtual];
                                $arDadosSessao[$inX]["creditos_agrupados"][$inA]["acrescimos_creditos_agrupados"][$inTotalAcrescimos]["cod_acrescimo"] = $arTmpJuros[1+$inJurosAtual];
                                $arDadosSessao[$inX]["creditos_agrupados"][$inA]["acrescimos_creditos_agrupados"][$inTotalAcrescimos]["valor_acrescimo"] = $arTmpJuros[$inJurosAtual];

                                #$arDataAtual = explode( "/", $arParcelasSessao[0]["data_vencimento"] );
                                #$stDataTMP = $arDataAtual[2]."-".$arDataAtual[1]."-".$arDataAtual[0];
                                $stDataTMP = $_REQUEST["stDataVenc"];//$arParcelasSessao[0]["data_vencimento"];

                                $obTDATDividaAtiva->aplicaReducaoModalidadeAcrescimo( $rsListaReducao, $_REQUEST["inCodModalidade"], $arDadosSessao[$inX]["inscricao"], $arTmpJuros[$inJurosAtual], $arTmpJuros[$inJurosAtual+1], $arTmpJuros[$inJurosAtual+2], $stDataTMP, count( $arParcelasSessao ) );

                                $arDadosSessao[$inX]["creditos_agrupados"][$inA]["acrescimos_creditos_agrupados"][$inTotalAcrescimos]["reducao_juros"] = $rsListaReducao->getCampo("valor");

                                $inTotalAcrescimos++;
                            }

                            $inJurosAtual += 3;
                        }

                        $arTmpMulta = explode( ";", $rsListaJurosMulta->getCampo("multa") );

                        $inTotalMulta = count( $arTmpMulta );
                        $inMultaAtual = 1;
                        while ($inMultaAtual < $inTotalMulta) {
                            //posicao 1 = valor, 2 = acrescimo, 3 = tipo
                            if ($arTmpMulta[$inMultaAtual] > 0) {
                                $arDadosSessao[$inX]["creditos_agrupados"][$inA]["acrescimos_creditos_agrupados"][$inTotalAcrescimos]["cod_tipo"] = $arTmpMulta[2+$inMultaAtual];
                                $arDadosSessao[$inX]["creditos_agrupados"][$inA]["acrescimos_creditos_agrupados"][$inTotalAcrescimos]["cod_acrescimo"] = $arTmpMulta[1+$inMultaAtual];
                                $arDadosSessao[$inX]["creditos_agrupados"][$inA]["acrescimos_creditos_agrupados"][$inTotalAcrescimos]["valor_acrescimo"] = $arTmpMulta[$inMultaAtual];

                                #$arDataAtual = explode( "/", $arParcelasSessao[0]["data_vencimento"] );
                                #$stDataTMP = $arDataAtual[2]."-".$arDataAtual[1]."-".$arDataAtual[0];
                                $stDataTMP = $_REQUEST["stDataVenc"];//$arParcelasSessao[0]["data_vencimento"];

                                $obTDATDividaAtiva->aplicaReducaoModalidadeAcrescimo( $rsListaReducao, $_REQUEST["inCodModalidade"], $arDadosSessao[$inX]["inscricao"], $arTmpMulta[$inMultaAtual], $arTmpMulta[$inMultaAtual+1], $arTmpMulta[$inMultaAtual+2], $stDataTMP, count( $arParcelasSessao ) );

                                $arDadosSessao[$inX]["creditos_agrupados"][$inA]["acrescimos_creditos_agrupados"][$inTotalAcrescimos]["reducao_multa"] = $rsListaReducao->getCampo("valor");

                                $inTotalAcrescimos++;
                            }

                            $inMultaAtual += 3;
                        }

                        $arTmpCorrecao = explode( ";", $rsListaJurosMulta->getCampo("correcao") );

                        $inTotalCorrecao = count( $arTmpCorrecao );
                        $inCorrecaoAtual = 1;
                        while ($inCorrecaoAtual < $inTotalCorrecao) {
                            //posicao 1 = valor, 2 = acrescimo, 3 = tipo
                            if ($arTmpCorrecao[$inCorrecaoAtual] > 0) {
                                $arDadosSessao[$inX]["creditos_agrupados"][$inA]["acrescimos_creditos_agrupados"][$inTotalAcrescimos]["cod_tipo"] = $arTmpCorrecao[2+$inCorrecaoAtual];
                                $arDadosSessao[$inX]["creditos_agrupados"][$inA]["acrescimos_creditos_agrupados"][$inTotalAcrescimos]["cod_acrescimo"] = $arTmpCorrecao[1+$inCorrecaoAtual];
                                $arDadosSessao[$inX]["creditos_agrupados"][$inA]["acrescimos_creditos_agrupados"][$inTotalAcrescimos]["valor_acrescimo"] = $arTmpCorrecao[$inCorrecaoAtual];

                                #$arDataAtual = explode( "/", $arParcelasSessao[0]["data_vencimento"] );
                                #$stDataTMP = $arDataAtual[2]."-".$arDataAtual[1]."-".$arDataAtual[0];
                                $stDataTMP = $_REQUEST["stDataVenc"];//$arParcelasSessao[0]["data_vencimento"];

                                $obTDATDividaAtiva->aplicaReducaoModalidadeAcrescimo( $rsListaReducao, $_REQUEST["inCodModalidade"], $arDadosSessao[$inX]["inscricao"], $arTmpCorrecao[$inCorrecaoAtual], $arTmpCorrecao[1+$inCorrecaoAtual], $arTmpCorrecao[2+$inCorrecaoAtual], $stDataTMP, count( $arParcelasSessao ) );

                                $arDadosSessao[$inX]["creditos_agrupados"][$inA]["acrescimos_creditos_agrupados"][$inTotalAcrescimos]["reducao_correcao"] = $rsListaReducao->getCampo("valor");
                                $inTotalAcrescimos++;
                            }

                            $inCorrecaoAtual += 3;
                        }

                        $arDadosSessao[$inX]["creditos_agrupados"][$inA]["total_acrescimos_creditos_agrupados"] = $inTotalAcrescimos;
                    } //fim do else
                } //for ( $inA=0; $inA < $arDadosSessao[$inX]["total_creditos_agrupados"]; $inA++ )

            } //for ( $inX=0; $inX < $inTotalDados; $inX++ )

            $arInscricoesAgrupadasSessao = array();
            $arInscricoesAgrupadasSessao["creditos_separados"] = array();
            $arInscricoesAgrupadasSessao["total_de_creditos_originais"] = 0;
            $arInscricoesAgrupadasSessao["total_creditos_agrupados"] = 0;
            $arInscricoesAgrupadasSessao["total_acrescimos_agrupados"] = 0;
            $arInscricoesAgrupadasSessao["primeiraInscricao"] = true;
            $arInscricoesAgrupadasSessao["multiplasInscricoes"] = (($inTotalDados>1)?true:false);

            for ($inX=0; $inX<$inTotalDados; $inX++) { //agrupando todas inscricoes numa unica
                $inUltimaPosicao = $arInscricoesAgrupadasSessao["total_de_creditos_originais"];
                for ($inZ=0; $inZ<$arDadosSessao[$inX]["total_creditos_separados"]; $inZ++) {
                    $arInscricoesAgrupadasSessao["creditos_separados"][$inUltimaPosicao+$inZ] = $arDadosSessao[$inX]["creditos_separados"][$inZ];
                }

                $arInscricoesAgrupadasSessao["total_de_creditos_originais"] += $arDadosSessao[$inX]["total_creditos_separados"];
                for ($inZ=0; $inZ<$arDadosSessao[$inX]["total_creditos_agrupados"]; $inZ++) {
                    $boJaNaLista = false;
                    for ($inD=0; $inD<$arInscricoesAgrupadasSessao["total_creditos_agrupados"]; $inD++) {
                        if (
                             ($arInscricoesAgrupadasSessao["creditos_agrupados"][$inD]["cod_credito"] == $arDadosSessao[$inX]["creditos_agrupados"][$inZ]["cod_credito"]) &&
                             ($arInscricoesAgrupadasSessao["creditos_agrupados"][$inD]["cod_especie"] == $arDadosSessao[$inX]["creditos_agrupados"][$inZ]["cod_especie"]) &&
                             ($arInscricoesAgrupadasSessao["creditos_agrupados"][$inD]["cod_natureza"] == $arDadosSessao[$inX]["creditos_agrupados"][$inZ]["cod_natureza"]) &&
                             ($arInscricoesAgrupadasSessao["creditos_agrupados"][$inD]["cod_genero"] == $arDadosSessao[$inX]["creditos_agrupados"][$inZ]["cod_genero"])
                           ) {

                            $boJaNaListaAcrescimo = false;
                            for ($inE=0; $inE<$arDadosSessao[$inX]["creditos_agrupados"][$inZ]["total_acrescimos_creditos_agrupados"]; $inE++) {
                                for ($inF=0; $inF<$arInscricoesAgrupadasSessao["creditos_agrupados"][$inD]["total_acrescimos_creditos_agrupados"]; $inF++) {
                                    if (
                                            ($arInscricoesAgrupadasSessao["creditos_agrupados"][$inD]["acrescimos_creditos_agrupados"][$inF]["cod_tipo"] == $arDadosSessao[$inX]["creditos_agrupados"][$inZ]["acrescimos_creditos_agrupados"][$inE]["cod_tipo"]) &&
                                            ($arInscricoesAgrupadasSessao["creditos_agrupados"][$inD]["acrescimos_creditos_agrupados"][$inF]["cod_acrescimo"] == $arDadosSessao[$inX]["creditos_agrupados"][$inZ]["acrescimos_creditos_agrupados"][$inE]["cod_acrescimo"])
                                       ) {
                                        $arInscricoesAgrupadasSessao["creditos_agrupados"][$inD]["acrescimos_creditos_agrupados"][$inF]["valor_acrescimo"] += $arDadosSessao[$inX]["creditos_agrupados"][$inZ]["acrescimos_creditos_agrupados"][$inE]["valor_acrescimo"];
                                        if ( $arDadosSessao[$inX]["creditos_agrupados"][$inZ]["acrescimos_creditos_agrupados"][$inE]["reducao_juros"] )
                                            $arInscricoesAgrupadasSessao["creditos_agrupados"][$inD]["acrescimos_creditos_agrupados"][$inF]["reducao_juros"] += $arDadosSessao[$inX]["creditos_agrupados"][$inZ]["acrescimos_creditos_agrupados"][$inE]["reducao_juros"];

                                        if ( $arDadosSessao[$inX]["creditos_agrupados"][$inZ]["acrescimos_creditos_agrupados"][$inE]["reducao_multa"] )
                                            $arInscricoesAgrupadasSessao["creditos_agrupados"][$inD]["acrescimos_creditos_agrupados"][$inF]["reducao_multa"] += $arDadosSessao[$inX]["creditos_agrupados"][$inZ]["acrescimos_creditos_agrupados"][$inE]["reducao_multa"];

                                        if ( $arDadosSessao[$inX]["creditos_agrupados"][$inZ]["acrescimos_creditos_agrupados"][$inE]["reducao_correcao"] )
                                            $arInscricoesAgrupadasSessao["creditos_agrupados"][$inD]["acrescimos_creditos_agrupados"][$inF]["reducao_correcao"] += $arDadosSessao[$inX]["creditos_agrupados"][$inZ]["acrescimos_creditos_agrupados"][$inE]["reducao_correcao"];

                                        $boJaNaListaAcrescimo = true;
                                        break;
                                    }
                                }

                                if (!$boJaNaListaAcrescimo) {
                                    $inF = $arInscricoesAgrupadasSessao["creditos_agrupados"]["total_acrescimos_creditos_agrupados"];
                                    $arInscricoesAgrupadasSessao["creditos_agrupados"][$inD]["acrescimos_creditos_agrupados"][$inF] = $arDadosSessao[$inX]["creditos_agrupados"][$inZ]["acrescimos_creditos_agrupados"][$inE];
                                    $arInscricoesAgrupadasSessao["creditos_agrupados"][$inD]["total_acrescimos_creditos_agrupados"] = $inF+1;
                                }
                            }

                            $arInscricoesAgrupadasSessao["creditos_agrupados"][$inD]["total_reducao_creditos_agrupados"] += $arDadosSessao[$inX]["creditos_agrupados"][$inZ]["total_reducao_creditos_agrupados"];
                            $arInscricoesAgrupadasSessao["creditos_agrupados"][$inD]["valor"] += $arDadosSessao[$inX]["creditos_agrupados"][$inZ]["valor"];

                            $boJaNaLista = true;
                            break;
                        }
                    }

                    if (!$boJaNaLista) {
                        $inD = $arInscricoesAgrupadasSessao["total_creditos_agrupados"];
                        $arInscricoesAgrupadasSessao["creditos_agrupados"][$inD] = $arDadosSessao[$inX]["creditos_agrupados"][$inZ];
                        $arInscricoesAgrupadasSessao["total_creditos_agrupados"] = $inD + 1;
                    }
                }
            }

            //esquema para agrupar acrescimos
            for ($inD=0; $inD<$arInscricoesAgrupadasSessao["total_creditos_agrupados"]; $inD++) {
                for ($inY=0; $inY<$arInscricoesAgrupadasSessao["creditos_agrupados"][$inD]["total_acrescimos_creditos_agrupados"]; $inY++) {
                    $boJaNaLista = false;
                    for ($inQ=0; $inQ<$arInscricoesAgrupadasSessao["total_acrescimos_agrupados"]; $inQ++) {
                        if ( ($arInscricoesAgrupadasSessao["acrescimos_agrupados"][$inQ]["cod_tipo"] == $arInscricoesAgrupadasSessao["creditos_agrupados"][$inD]["acrescimos_creditos_agrupados"][$inY]["cod_tipo"]) &&
                             ($arInscricoesAgrupadasSessao["acrescimos_agrupados"][$inQ]["cod_acrescimo"] == $arInscricoesAgrupadasSessao["creditos_agrupados"][$inD]["acrescimos_creditos_agrupados"][$inY]["cod_acrescimo"])
                           ) {
                            $arInscricoesAgrupadasSessao["acrescimos_agrupados"][$inQ]["valor_acrescimo"] += $arInscricoesAgrupadasSessao["creditos_agrupados"][$inD]["acrescimos_creditos_agrupados"][$inY]["valor_acrescimo"];
                            $boJaNaLista = true;
                        }
                    }

                    if (!$boJaNaLista) {
                        $inQ = $arInscricoesAgrupadasSessao["total_acrescimos_agrupados"];
                        $arInscricoesAgrupadasSessao["acrescimos_agrupados"][$inQ] = $arInscricoesAgrupadasSessao["creditos_agrupados"][$inD]["acrescimos_creditos_agrupados"][$inY];
                        $arInscricoesAgrupadasSessao["total_acrescimos_agrupados"] = $inQ+1;
                    }
                }
            }

            //esquema para ordenar creditos-------------------------------
            $arPA = $arInscricoesAgrupadasSessao["creditos_agrupados"];
            $inTempPA = 0;
            $inTempTotalPA = count( $arPA );
            $arTempPAPrimeiro = array();
            $arTempPAPrimeiro["contador"] = 0;

            $arTempPASegundo = array();
            $arTempPASegundo["contador"] = 0;

            $arTempPATerceiro = array();
            $arTempPATerceiro["contador"] = 0;

            for ($inTempPA=0; $inTempPA<$inTempTotalPA; $inTempPA++) {
                if ( ( ($arPA[$inTempPA]["cod_especie"] == 1) || ($arPA[$inTempPA]["cod_especie"] == 2) )
                    && ($arPA[$inTempPA]["cod_genero"] == 3) && ($arPA[$inTempPA]["cod_natureza"] == 1) ) {
                    $arTempPAPrimeiro[$arTempPAPrimeiro["contador"]] = $arPA[$inTempPA];
                    $arTempPAPrimeiro["contador"]++;
                }else
                    if (
                        (
                            (($arPA[$inTempPA]["cod_especie"] == 1) || ($arPA[$inTempPA]["cod_especie"] == 2))
                            && ($arPA[$inTempPA]["cod_genero"] == 2) && ($arPA[$inTempPA]["cod_natureza"] == 1)
                        )
                        ||
                        (
                            ($arPA[$inTempPA]["cod_especie"] == 100) && ($arPA[$inTempPA]["cod_genero"] == 7) && ($arPA[$inTempPA]["cod_natureza"] == 2)
                        )
                        ||
                        (
                            ($arPA[$inTempPA]["cod_especie"] == 101) && ($arPA[$inTempPA]["cod_genero"] == 4) && ($arPA[$inTempPA]["cod_natureza"] == 1)
                        )
                    ) {
                        $arTempPASegundo[$arTempPASegundo["contador"]] = $arPA[$inTempPA];
                        $arTempPASegundo["contador"]++;
                    } else {
                        $arTempPATerceiro[$arTempPATerceiro["contador"]] = $arPA[$inTempPA];
                        $arTempPATerceiro["contador"]++;
                    }
            }

            $arFinal = array();
            $inTempTotalPA = 0;
            for ($inTempPA=0; $inTempPA<$arTempPAPrimeiro["contador"]; $inTempPA++) {
                $arFinal[$inTempTotalPA] = $arTempPAPrimeiro[$inTempPA];
                $inTempTotalPA++;
            }

            for ($inTempPA=0; $inTempPA<$arTempPASegundo["contador"]; $inTempPA++) {
                $arFinal[$inTempTotalPA] = $arTempPASegundo[$inTempPA];
                $inTempTotalPA++;
            }

            for ($inTempPA=0; $inTempPA<$arTempPATerceiro["contador"]; $inTempPA++) {
                $arFinal[$inTempTotalPA] = $arTempPATerceiro[$inTempPA];
                $inTempTotalPA++;
            }

            $arFinalSomaCreditos = array();
            $inTotalCreditos = 0;
            for ( $inA=0; $inA<count($arFinal); $inA++ ) {
                $boJaNaLista = false;
                for ($inZ=0; $inZ<$inTotalCreditos; $inZ++) {
                    if ( ($arFinal[$inA]["cod_especie"] == $arFinalSomaCreditos[$inZ]["cod_especie"]) &&
                            ($arFinal[$inA]["cod_genero"] == $arFinalSomaCreditos[$inZ]["cod_genero"]) &&
                            ($arFinal[$inA]["cod_natureza"] == $arFinalSomaCreditos[$inZ]["cod_natureza"]) &&
                            ($arFinal[$inA]["cod_credito"] == $arFinalSomaCreditos[$inZ]["cod_credito"])
                        ) {
                            $arFinalSomaCreditos[$inZ]["valor"] += $arFinal[$inA]["valor"];
                            $boJaNaLista = true;
                            break;
                            }
                }

                if (!$boJaNaLista) {
                    $arFinalSomaCreditos[$inTotalCreditos] = $arFinal[$inA];
                    $inTotalCreditos++;
                }
            }

            $arInscricoesAgrupadasSessao["creditos_agrupados_classificados"] = $arFinalSomaCreditos;

            $stFiltro = " WHERE dm.cod_modalidade = ".$_REQUEST["inCodModalidade"]." AND amad.cod_acao = ".Sessao::read('acao');
            $obTDATModalidade->recuperaModalidadeDocumentos( $rsListaModalidade, $stFiltro );
            $obTDATParcelamento->recuperaNumeroParcelamentoExercicio( $rsNumeroParcelamentoExercicio, Sessao::getExercicio() );

            if ($arInscricoesAgrupadasSessao["primeiraInscricao"]) { //verificando se tem parcelas canceladas
                $stFiltro = " WHERE num_parcelamento = ".$arDadosSessao[0]["num_parcelamento"]." AND paga = false AND cancelada = true";
                $obTDATDividaParcela->recuperaTodos( $rsListaParcelasCanceladas, $stFiltro );
                if ( !$rsListaParcelasCanceladas->Eof() ) {
                    $arInscricoesAgrupadasSessao["primeiraInscricao"] = false;
                }
            }

                //criando parcelas novas
                $obTDATParcelamento->recuperaNumeroParcelamento ( $rsNumParcelamento );                
                $inNumParcelamento = $rsNumParcelamento->getCampo('valor');

                $stNumeroParcelamento = $rsNumeroParcelamentoExercicio->getCampo("valor")."/".Sessao::getExercicio();
                $obTDATParcelamento->setDado( "num_parcelamento"    , $inNumParcelamento );
                $obTDATParcelamento->setDado( 'cod_modalidade'      , $_REQUEST["inCodModalidade"] );
                $obTDATParcelamento->setDado( 'timestamp_modalidade', $rsListaModalidade->getCampo("ultimo_timestamp") );
                $obTDATParcelamento->setDado( 'numcgm_usuario'      , Sessao::read('numCgm') );
                $obTDATParcelamento->setDado( "numero_parcelamento" , $rsNumeroParcelamentoExercicio->getCampo("valor") );
                $obTDATParcelamento->setDado( 'exercicio'           , Sessao::getExercicio() );
                if ($arCobrancaJudicial) {
                    $obTDATParcelamento->setDado( 'judicial', TRUE );
                } else {
                    $obTDATParcelamento->setDado( 'judicial', FALSE );
                }
                $obTDATParcelamento->inclusao( );                

                for ($inT=0; $inT<$inTotalDados; $inT++) {
                    $obTDATDividaParcelamento->setDado( "cod_inscricao", $arDadosSessao[$inT]["cod_inscricao"] );
                    $obTDATDividaParcelamento->setDado( "exercicio", $arDadosSessao[$inT]["exercicio"] );
                    $obTDATDividaParcelamento->setDado( "num_parcelamento", $inNumParcelamento );
                    $obTDATDividaParcelamento->inclusao( );
                }

                $rsNumeroParcelasTEMP = new RecordSet;
                $rsNumeroParcelasTEMP->preenche ( $arInscricoesAgrupadasSessao["creditos_separados"] );
                $rsNumeroParcelasTEMP->ordena('cod_parcela');
                $inContTemp = $inQtdeParcelasOrigem = 0;
                $inCodParcelaAtual= null;
                $rsNumeroParcelasTEMP->setPrimeiroElemento();
                while ( !$rsNumeroParcelasTEMP->eof() ) {
                    if ( $inCodParcelaAtual != $rsNumeroParcelasTEMP->getCampo('cod_parcela') ) {
                        $inCodParcelaAtual = $rsNumeroParcelasTEMP->getCampo('cod_parcela');
                        $inQtdeParcelasOrigem ++;
                    }

                    $rsNumeroParcelasTEMP->proximo();
                }

                for ( $inT=0; $inT < count($arInscricoesAgrupadasSessao["creditos_separados"]); $inT++ ) {
                    $obTDATDividaParcelaOrigem = new TDATDividaParcelaOrigem;
                    $obTDATDividaParcelaOrigem->setDado( "cod_parcela"      , $arInscricoesAgrupadasSessao["creditos_separados"][$inT]["cod_parcela"] );
                    $obTDATDividaParcelaOrigem->setDado( "cod_credito"      , $arInscricoesAgrupadasSessao["creditos_separados"][$inT]["cod_credito"] );
                    $obTDATDividaParcelaOrigem->setDado( "cod_especie"      , $arInscricoesAgrupadasSessao["creditos_separados"][$inT]["cod_especie"] );
                    $obTDATDividaParcelaOrigem->setDado( "cod_genero"       , $arInscricoesAgrupadasSessao["creditos_separados"][$inT]["cod_genero"] );
                    $obTDATDividaParcelaOrigem->setDado( "cod_natureza"     , $arInscricoesAgrupadasSessao["creditos_separados"][$inT]["cod_natureza"] );
                    $obTDATDividaParcelaOrigem->setDado( "valor"            , $arInscricoesAgrupadasSessao["creditos_separados"][$inT]["valor"] );
                    $obTDATDividaParcelaOrigem->setDado( "num_parcelamento" , $inNumParcelamento );
                    $obTDATDividaParcelaOrigem->inclusao(  );                    
                }

            $inTotaldeParcelas = count( $arParcelasSessao );

            $flValorPrimeiraParcelaOriginal = $flValorPorParcelaOriginal = ($flValorTotalOriginal / $inTotaldeParcelas) ;
            $flDiferencaParcelas = $flValorTotalOriginal - ( $flValorPorParcelaOriginal * $inTotaldeParcelas );
            if ( $flDiferencaParcelas != 0 )
                $flValorPrimeiraParcelaOriginal += $flDiferencaParcelas;

            $stDataAtual = date ("Y-m-d");

            $obTARRLancamento->proximoCod( $inCodLancamento );
            $obTARRLancamento->setDado( "cod_lancamento"    , $inCodLancamento );
            $obTARRLancamento->setDado( "vencimento"        , $arParcelasSessao[0]["data_vencimento"] ); //eh a data de vencimento da primeira parcela
            $obTARRLancamento->setDado( "total_parcelas"    , $inTotaldeParcelas );
            $obTARRLancamento->setDado( "ativo"             , true );
            $obTARRLancamento->setDado( "observacao"        , "" );
            $obTARRLancamento->setDado( "observacao_sistema", "" );
            $obTARRLancamento->setDado( "valor"             , $flValorTotalFinal );
            $obTARRLancamento->setDado( "divida"            , true );
            $obTARRLancamento->inclusao(  );
            
            for ($inA=0; $inA<$arInscricoesAgrupadasSessao["total_creditos_agrupados"]; $inA++) { //inserindo os calculo (1 calculo por credito)
                $obTARRCalculo->proximoCod( $inCodCalculoTMP );
                $arInscricoesAgrupadasSessao["creditos_agrupados_classificados"][$inA]["cod_calculo"] = $inCodCalculoTMP;

                $flValorCalculo = $arInscricoesAgrupadasSessao["creditos_agrupados_classificados"][$inA]["valor"];
                for ($inW=0; $inW<$arInscricoesAgrupadasSessao["creditos_agrupados_classificados"][$inA]["total_acrescimos_creditos_agrupados"]; $inW++) {
                    $flValorCalculo += $arInscricoesAgrupadasSessao["creditos_agrupados_classificados"][$inA]["acrescimos_creditos_agrupados"][$inW]["valor_acrescimo"];
                    if ($arInscricoesAgrupadasSessao["creditos_agrupados_classificados"][$inA]["acrescimos_creditos_agrupados"][$inW]["reducao_juros"] > 0) {
                        $flValorCalculo -= $arInscricoesAgrupadasSessao["creditos_agrupados_classificados"][$inA]["acrescimos_creditos_agrupados"][$inW]["reducao_juros"];
                    }

                    if ($arInscricoesAgrupadasSessao["creditos_agrupados_classificados"][$inA]["acrescimos_creditos_agrupados"][$inW]["reducao_multa"] > 0) {
                        $flValorCalculo -= $arInscricoesAgrupadasSessao["creditos_agrupados_classificados"][$inA]["acrescimos_creditos_agrupados"][$inW]["reducao_multa"];
                    }

                    if ($arInscricoesAgrupadasSessao["creditos_agrupados_classificados"][$inA]["acrescimos_creditos_agrupados"][$inW]["reducao_correcao"] > 0) {
                        $flValorCalculo -= $arInscricoesAgrupadasSessao["creditos_agrupados_classificados"][$inA]["acrescimos_creditos_agrupados"][$inW]["reducao_correcao"];
                    }
                }

                $obTARRCalculo->setDado ( 'cod_calculo', $inCodCalculoTMP );
                $obTARRCalculo->setDado ( 'cod_credito', $arInscricoesAgrupadasSessao["creditos_agrupados_classificados"][$inA]["cod_credito"] );
                $obTARRCalculo->setDado ( 'cod_natureza', $arInscricoesAgrupadasSessao["creditos_agrupados_classificados"][$inA]["cod_natureza"] );
                $obTARRCalculo->setDado ( 'cod_genero', $arInscricoesAgrupadasSessao["creditos_agrupados_classificados"][$inA]["cod_genero"] );
                $obTARRCalculo->setDado ( 'cod_especie', $arInscricoesAgrupadasSessao["creditos_agrupados_classificados"][$inA]["cod_especie"] );
                $obTARRCalculo->setDado ( 'exercicio', Sessao::getExercicio() );
                $obTARRCalculo->setDado ( 'nro_parcelas', $inTotaldeParcelas );
                $obTARRCalculo->setDado ( 'valor', $flValorCalculo );
                $obTARRCalculo->setDado ( 'ativo', true );
                $obTARRCalculo->inclusao();

                $obTARRCalculoCgm->setDado( "cod_calculo", $inCodCalculoTMP );
                $obTARRCalculoCgm->setDado( "numcgm", $arDadosSessao[0]["numcgm"] );
                $obTARRCalculoCgm->inclusao();
                
                $arNumeroDasInscricoes = array();
                
                //Loop para inserir todas as inscricoes em seus devidos calculos
                foreach ($arDadosSessao as $dados) {
                    if ( $dados['inscricao'] && (!in_array($dados['inscricao'], $arNumeroDasInscricoes)) ) {
                        if ($dados['inscricao_tipo'] == "imobiliaria") {
                            $stFiltroImovel = " WHERE inscricao_municipal = ".$dados['inscricao'];
                            $obTARRImovelVVenal->recuperaTodos( $rsListaTimestampImovel, $stFiltroImovel, " timestamp DESC " );
    
                            $obTARRImovelCalculo->setDado ( 'cod_calculo', $inCodCalculoTMP );
                            $obTARRImovelCalculo->setDado ( 'inscricao_municipal', $dados['inscricao'] );
                            $obTARRImovelCalculo->setDado ( 'timestamp', $rsListaTimestampImovel->getCampo("timestamp") );
                            $obTARRImovelCalculo->inclusao();                        
                        
                        }else
                        if ($dados['inscricao_tipo'] == "economica") {
                            $stFiltroEconomico = " WHERE inscricao_economica = ".$dados['inscricao'];
                            $obTARRCadastroEconomicoFaturamento->recuperaTodos( $rsListaTimestampEconomico, $stFiltroEconomico, " timestamp DESC " );
    
                            $obTARRCadastroEconomicoCalculo->setDado ( 'cod_calculo', $inCodCalculoTMP );
                            $obTARRCadastroEconomicoCalculo->setDado ( 'inscricao_economica', $dados['inscricao'] );
                            $obTARRCadastroEconomicoCalculo->setDado ( 'timestamp', $rsListaTimestampEconomico->getCampo("timestamp" ) );
                            $obTARRCadastroEconomicoCalculo->inclusao();
                        }
                        //Atribui para nao inserir a mesma inscricao
                        $arNumeroDasInscricoes[] = $dados['inscricao'];
                    }
                }
                
                $obTARRLancamentoCalculo->setDado( "cod_calculo", $inCodCalculoTMP );
                $obTARRLancamentoCalculo->setDado( "cod_lancamento", $inCodLancamento );
                $obTARRLancamentoCalculo->setDado( "valor", $flValorCalculo );
                $obTARRLancamentoCalculo->inclusao();
            }

            $inTotalDocumentos = 0;
            while ( !$rsListaModalidade->Eof() ) {
                $inTotalDocumentos++;

                $obTDATDividaDocumento->setDado( 'num_parcelamento', $inNumParcelamento );
                $obTDATDividaDocumento->setDado( 'cod_tipo_documento', $rsListaModalidade->getCampo("cod_tipo_documento") );
                $obTDATDividaDocumento->setDado( 'cod_documento', $rsListaModalidade->getCampo("cod_documento") );
                $obTDATDividaDocumento->inclusao();

                $rsListaModalidade->proximo();
            }

            $rsListaModalidade->setPrimeiroElemento();

            //esquema para dividir o valor da reducao pelo numero de parcelas
            $flValorPrimeiraParcelaReducao = $flTotalPorParcelaReducao = ($flValorTotalReducao / $inTotaldeParcelas);
            $flDiferencaParcelas = $flValorTotalReducao - ( $flTotalPorParcelaReducao * $inTotaldeParcelas );
            if ( $flDiferencaParcelas != 0 )
                $flValorPrimeiraParcelaReducao += $flDiferencaParcelas;

            //--------------------------------------------------------

            $rsListaCreditosAgrupadosClassificados = new RecordSet;
            $rsListaCreditosAgrupadosClassificados->preenche( $arInscricoesAgrupadasSessao["creditos_agrupados_classificados"] );

            $flValorParaInserir = 0;
            $obTMONConvenio = new TMONConvenio;
            $obRARRCalculo = new RARRCalculo();
            $obRARRCalculo->obRARRCarne = new RARRCarne();

            $arValoresFinaisReducao = array();
            for ($inT=0; $inT<$inTotaldeParcelas; $inT++) {
                if ($inT == 0) {
                    $arParcelasSessao[$inT]["valor_reducao"] = $flValorPrimeiraParcelaReducao;
                } else {
                    $arParcelasSessao[$inT]["valor_reducao"] = $flTotalPorParcelaReducao;
                }

                $obTARRParcela->proximoCod( $inCodParcela );
                $obTARRParcela->setDado( "cod_parcela"      , $inCodParcela );
                $obTARRParcela->setDado( "cod_lancamento"   , $inCodLancamento );
                $obTARRParcela->setDado( "nr_parcela"       , $arParcelasSessao[$inT]["nr_parcela"] );
                $obTARRParcela->setDado( "vencimento"       , $arParcelasSessao[$inT]["data_vencimento"] );
                $obTARRParcela->setDado( "valor"            , $arParcelasSessao[$inT]["vlr_parcela"] );
                $obTARRParcela->inclusao();

                //incluir no carnes aqui
                //------------------------------------------------------
                $stFiltro = " WHERE c.cod_convenio = -1 ";
                $obTMONConvenio->recuperaTodos( $rsListaFuncao, $stFiltro );
                $obRARRCalculo->obRARRCarne->obRMONConvenio->obRFuncao->setCodFuncao( $rsListaFuncao->getCampo( "cod_funcao" ) );
                $obRARRCalculo->obRARRCarne->obRMONConvenio->obRFuncao->obRBiblioteca->setCodigoBiblioteca($rsListaFuncao->getCampo( "cod_biblioteca" ) );
                $obRARRCalculo->obRARRCarne->obRMONConvenio->obRFuncao->obRBiblioteca->roRModulo->setCodModulo($rsListaFuncao->getCampo( "cod_modulo" ));
                $obRARRCalculo->obRARRCarne->obRMONConvenio->obRFuncao->consultar();

                $stFNumeracao = "F".$obRARRCalculo->obRARRCarne->obRMONConvenio->obRFuncao->getNomeFuncao();
                if ( !$obRARRCalculo->obRARRCarne->obRMONConvenio->obRFuncao->getNomeFuncao() ) {
                    SistemaLegado::exibeAviso( "Função para gerar numeração de carnes não foi declarada!", "n_incluir", "erro" );
                    exit;
                }

                $stFNumeracao = "F".$obRARRCalculo->obRARRCarne->obRMONConvenio->obRFuncao->getNomeFuncao();
                $stFNumeracaoMap = "../../classes/funcao/".$stFNumeracao.".class.php";

                include_once ( $stFNumeracaoMap );
                $obFNumeracao = new $stFNumeracao;

                $stParametros = -1;
                //------------------------------ fim da verificação
                $obFNumeracao->executaFuncao($rsRetorno,$stParametros,$boTransacao);
                $inNumeracao = $rsRetorno->getCampo( "valor" );

                $obTARRCarne->setDado( "numeracao", $inNumeracao );
                $obTARRCarne->setDado( "exercicio", Sessao::getExercicio() );
                $obTARRCarne->setDado( "cod_parcela", $inCodParcela );
                $obTARRCarne->setDado( "cod_convenio", -1 );
                $obTARRCarne->setDado( "impresso", $_REQUEST["emissao_carnes"]!='nao_emitir'?'true':'false' );
                $obTARRCarne->inclusao();

                $arEmissao[$inCodLancamento][]= array(
                    "cod_parcela" => $inCodParcela,
                    "exercicio"   => Sessao::getExercicio(),
                    "numcgm"      => $_REQUEST['inCGM'],
                    "numeracao"     => $inNumeracao,
                    "inscricao"     => $inNumParcelamento
                );

                $obTDATDividaParcela->setDado( "num_parcelamento", $inNumParcelamento );
                $obTDATDividaParcela->setDado( "num_parcela", $arParcelasSessao[$inT]["nr_parcela"] );
                $obTDATDividaParcela->setDado( 'vlr_parcela', $arParcelasSessao[$inT]["vlr_parcela"] );
                $obTDATDividaParcela->setDado( 'dt_vencimento_parcela', $arParcelasSessao[$inT]["data_vencimento"] );
                $obTDATDividaParcela->setDado( 'paga', false );
                $obTDATDividaParcela->setDado( 'cancelada', false );
                $obTDATDividaParcela->inclusao();

                $flValorParaReducao = $arParcelasSessao[$inT]["vlr_parcela"];
                $inTotalDocumentos = 0;
                while ( !$rsListaModalidade->Eof() ) {
                    $obTDATDividaDocumentoParcela->setDado('num_parcelamento', $inNumParcelamento );
                    $obTDATDividaDocumentoParcela->setDado('cod_documento', $rsListaModalidade->getCampo("cod_documento") );
                    $obTDATDividaDocumentoParcela->setDado('cod_tipo_documento', $rsListaModalidade->getCampo("cod_tipo_documento") );
                    $obTDATDividaDocumentoParcela->setDado('num_parcela', $arParcelasSessao[$inT]["nr_parcela"] );
                    $inTotalDocumentos++;

                    $obTDATDividaDocumentoParcela->inclusao();

                    $rsListaModalidade->proximo();
                }

                $rsListaModalidade->setPrimeiroElemento();

                //inserindo acrescimos
                for ($inL=0; $inL<$arInscricoesAgrupadasSessao["total_acrescimos_agrupados"]; $inL++) {
                    $flValorAcrescimo = $arInscricoesAgrupadasSessao["acrescimos_agrupados"][$inL]["valor_acrescimo"];
                    $flValorPrimeiraParcelaAcrescimo = $flTotalPorParcelaAcrescimo = ($flValorAcrescimo / $inTotaldeParcelas);
                    $flDiferencaParcelas =  $flValorAcrescimo - ( $flTotalPorParcelaAcrescimo * $inTotaldeParcelas );
                    if ( $flDiferencaParcelas != 0 )
                        $flValorPrimeiraParcelaAcrescimo += $flDiferencaParcelas;

                    $obTDATDividaParcelaAcrescimo->setDado('num_parcelamento', $inNumParcelamento );
                    $obTDATDividaParcelaAcrescimo->setDado('num_parcela', $arParcelasSessao[$inT]["nr_parcela"] );
                    $obTDATDividaParcelaAcrescimo->setDado('cod_tipo', $arInscricoesAgrupadasSessao["acrescimos_agrupados"][$inL]["cod_tipo"] );
                    $obTDATDividaParcelaAcrescimo->setDado('cod_acrescimo', $arInscricoesAgrupadasSessao["acrescimos_agrupados"][$inL]["cod_acrescimo"] );
                    if ($inT == 0) {
                        $obTDATDividaParcelaAcrescimo->setDado('vlracrescimo', $flValorPrimeiraParcelaAcrescimo );
                        $flValorParaReducao -= $flValorPrimeiraParcelaAcrescimo;
                    } else {
                        $obTDATDividaParcelaAcrescimo->setDado('vlracrescimo', $flTotalPorParcelaAcrescimo );
                        $flValorParaReducao -= $flTotalPorParcelaAcrescimo;
                    }

                    $obTDATDividaParcelaAcrescimo->inclusao();
                }

                $flValorParcela = 0;
                if ($inT == 0) {
                    $flValorTotalParcela = $flValorPrimeiraParcelaOriginal;
                } else {
                    $flValorTotalParcela = $flValorPorParcelaOriginal;
                }

                while ( !$rsListaCreditosAgrupadosClassificados->Eof() ) {
                    $boDiferenca = false;
                    if ($flValorParaInserir != 0) {
                        if ( ($flValorParcela + $flValorParaInserir) > $flValorTotalParcela ) {
                            $flValorParaInserir = $flValorTotalParcela - $flValorParcela;
                            $boDiferenca = true;
                        }
                    } else {
                        if ( ($flValorParcela + $rsListaCreditosAgrupadosClassificados->getCampo("valor")) > $flValorTotalParcela ) {
                            $flValorParaInserir = $flValorTotalParcela - $flValorParcela;
                            $boDiferenca = true;
                        } else {
                            $flValorParaInserir = $rsListaCreditosAgrupadosClassificados->getCampo("valor");
                        }
                    }

                    $obTDATDividaParcelaCalculo->setDado('num_parcelamento', $inNumParcelamento );
                    $obTDATDividaParcelaCalculo->setDado('num_parcela', $arParcelasSessao[$inT]["nr_parcela"] );
                    $obTDATDividaParcelaCalculo->setDado('cod_calculo', $rsListaCreditosAgrupadosClassificados->getCampo("cod_calculo") );
                    $obTDATDividaParcelaCalculo->setDado('vl_credito', $flValorParaInserir );
                    $obTDATDividaParcelaCalculo->inclusao();

                    $flValorParaReducao -= $flValorParaInserir;
                    //para estas tabelas deve usar o cod_parcela antigo
                    if ($boDiferenca) {
                        $flValorParaInserir = $rsListaCreditosAgrupadosClassificados->getCampo("valor") - $flValorParaInserir;
                        $rsListaCreditosAgrupadosClassificados->setCampo( "valor", $flValorParaInserir );
                        break;
                    } else {
                        $flValorParcela += $flValorParaInserir;
                        $flValorParaInserir = 0;
                        $rsListaCreditosAgrupadosClassificados->proximo();
                        if ($flValorParcela >= $flValorTotalParcela) {
                            break;
                        }
                    }
                }

                if ($arParcelasSessao[$inT]["valor_reducao"] <= 0) {
                    $flValorParaReducao = 0.00;
                }

                $arValoresFinaisReducao[] = abs( $flValorParaReducao );
            }

            $obTDATModalidadeReducao = new TDATModalidadeReducao;
            $obTDATModalidadeReducao->recuperaListaReducaoTipo( $rsListaReducoesModalidade, $_REQUEST["inCodModalidade"] );
            $boReducaoAcrescimoCredito = true;
            $stUltimoTipo = $rsListaReducoesModalidade->getCampo( "tipo_reducao" );
            if ( $rsListaReducoesModalidade->getNumLinhas() <= 1 )
                $boReducaoAcrescimoCredito = false;
            else {
                $boReducaoAcrescimoCredito = false;
                while ( !$rsListaReducoesModalidade->Eof() ) {
                    if ( $rsListaReducoesModalidade->getCampo( "tipo_reducao" ) == "" ) {
                        $rsListaReducoesModalidade->proximo();
                        continue;
                    }else
                    if ( $stUltimoTipo != $rsListaReducoesModalidade->getCampo( "tipo_reducao" ) ) {
                        $boReducaoAcrescimoCredito = true;
                        break;
                    }

                    $rsListaReducoesModalidade->proximo();
                }

                $rsListaReducoesModalidade->setPrimeiroElemento();
            }
             //recuperar reducoes da modalidade

            if (!$boReducaoAcrescimoCredito) { //apenas um tipo de reducao
                if (!$stUltimoTipo) {
                    $stUltimoTipo = 'A';
                }

                for ($inT=0; $inT<$inTotaldeParcelas; $inT++) {
                    $obTDATDividaParcelaReducao->setDado( "num_parcelamento", $inNumParcelamento );
                    $obTDATDividaParcelaReducao->setDado( "num_parcela", $arParcelasSessao[$inT]["nr_parcela"] );
                    $obTDATDividaParcelaReducao->setDado( "valor", $arValoresFinaisReducao[$inT]  );
                    $obTDATDividaParcelaReducao->setDado( "origem_reducao", $stUltimoTipo );
                    $obTDATDividaParcelaReducao->inclusao();
                }
            } else {
                $flValorParaReducao = 0.00;
                for ($inT=0; $inT<$inTotaldeParcelas; $inT++) {
                    $flValorParaReducao += $arValoresFinaisReducao[$inT];
                }

                if ($flValorParaReducao > 0.00) {
                    $nuPrimeiraParcelaCredito = 0;
                    $nuOutrasParcelasCredito  = 0;
                    $nuParcelaAcrescimos      = 0;

                    // Divide o valor da redução entre redução de crédito e redução de acrescimo
                    $nuPrimeiraParcelaCredito = ($nuTotalReducaoCredito / $inTotaldeParcelas);
                    $nuDiferencaCredito = $flTotalReducaoCredito - ($nuPrimeiraParcelaCredito * $inTotaldeParcelas);

                    if ($nuDiferencaCredito > 0) {
                        $nuPrimeiraParcelaCredito = $nuPrimeiraParcelaCredito + $nuDiferencaCredito;
                    }

                    $nuOutrasParcelasCredito = ($nuTotalReducaoCredito - $nuPrimeiraParcelaCredito);
                    for ($inT=0; $inT<$inTotaldeParcelas; $inT++) {
                        if ($inT == 0) {
                            $nuParcelaAcrescimos = $arValoresFinaisReducao[$inT] - $nuPrimeiraParcelaCredito;
                            $nuParcelaCredito    = $nuPrimeiraParcelaCredito;
                        } else {
                            $nuParcelaAcrescimos = $arValoresFinaisReducao[$inT] - $nuOutrasParcelasCredito;
                            $nuParcelaCredito    = $nuOutrasParcelasCredito;
                        }

                        if ($nuParcelaAcrescimos > 0) {
                            $obTDATDividaParcelaReducao->setDado('valor'           , $nuParcelaAcrescimos);
                            $obTDATDividaParcelaReducao->setDado('origem_reducao'  , 'A' );
                            $obTDATDividaParcelaReducao->setDado('num_parcelamento', $inNumParcelamento );
                            $obTDATDividaParcelaReducao->setDado('num_parcela'     , $arParcelasSessao[$inT]['nr_parcela']);
                            $obTDATDividaParcelaReducao->inclusao();
                        }

                        if ($nuParcelaCredito > 0) {
                            $obTDATDividaParcelaReducao->setDado('valor'           , $nuParcelaCredito);
                            $obTDATDividaParcelaReducao->setDado('origem_reducao'  , 'C' );
                            $obTDATDividaParcelaReducao->setDado('num_parcelamento', $inNumParcelamento );
                            $obTDATDividaParcelaReducao->setDado('num_parcela'     , $arParcelasSessao[$inT]['nr_parcela']);
                            $obTDATDividaParcelaReducao->inclusao();
                        }

                        $nuParcelaAcrescimos = 0;
                        $nuParcelaCredito    = 0;
                    }
                }
            }

        Sessao::encerraExcecao(); //para testes removido!

        //$boExec = false;
        if ($_REQUEST["emissao_carnes"] == "local") {
            $stNomPdfSessao   =  ini_get("session.save_path")."/"."PdfEmissaoUrbem-".date("dmYHi").".pdf";
            $stParamPdfSessao = "F";

            Sessao::write('stNomPdf'   , $stNomPdfSessao);
            Sessao::write('stParamPdf' , $stParamPdfSessao);

            $stArquivoModelo = $_REQUEST["cmbModelo"];
            $arTmp           = explode( ".", $stArquivoModelo );
            $stObjModelo     = $arTmp[0];

            include_once( CAM_GT_ARR_CLASSES."boletos/".$stArquivoModelo );
            $obRModeloCarne = new $stObjModelo( $arEmissao );
            //$obRModeloCarne->setCobranca(true);
            $obRModeloCarne->imprimirCarne();
            
            echo "<script type=\"text/javascript\">\r\n";
            echo "    var sAux = window.open('OCImpressaoPDFEmissao.php?".Sessao::getId()."','','width=20,height=10,resizable=1,scrollbars=1,left=100,top=100');\r\n";
            echo "    eval(sAux)\r\n";
            echo "</script>\r\n";
        }

        if ($_REQUEST["boEmitirParcelamento"]) {
            $stCaminho = CAM_GT_DAT_INSTANCIAS."emissao/LSManterEmissao.php";
            $stParametros = "&stTipoModalidade=emissao";
            $stParametros .= "&stCodAcao=".Sessao::read('acao');
            $stParametros .= "&stOrigemFormulario=cobranca_divida";
            $stParametros .= "&inNumeroParcelamento=".$inNumParcelamento;

            Sessao::remove('stLink');

            sistemaLegado::alertaAviso( $stCaminho."?".Sessao::getId().$stParametros."&stAcao=incluir","Cobrança de dívida (".$stNumeroParcelamento.")", "incluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::alertaAviso($pgFilt, "Cobrança de dívida (".$stNumeroParcelamento.")", "incluir", "aviso", Sessao::getId(), "../");
        }
        break;

    case "Estornar":

        foreach ($_REQUEST as $valor => $key) {
            if ( preg_match("/boSelecionada_[0-9]/", $valor) ) {
                $stDataAtual = date ("d/m/Y");
                $obTARRCarneDevolucao = new TARRCarneDevolucao;
                $obTDATDividaParcela = new TDATDividaParcela;
                $arTMP = explode( "-", $key );
                $stFiltro = " WHERE num_parcelamento = ".$arTMP[0]." AND paga = false AND cancelada = false ";
                $obTDATDividaParcela->recuperaTodos( $rsListaDocumentos, $stFiltro );
                if ( !$rsListaDocumentos->Eof() ) {
                    Sessao::setTrataExcecao( true );
                    Sessao::getTransacao()->setMapeamento( $obTARRCarneDevolucao );

                    $obTDATDividaDocumento = new TDATDividaDocumento;
                    $obTDATDividaDocumento->recuperaListaCarnesCobrancaEstornar( $rsListaCarnes, $arTMP[0] );

                    while ( !$rsListaDocumentos->Eof() ) {
                        $obTDATDividaParcela->setDado( "num_parcelamento", $rsListaDocumentos->getCampo("num_parcelamento") );
                        $obTDATDividaParcela->setDado( "num_parcela", $rsListaDocumentos->getCampo("num_parcela") );
                        $obTDATDividaParcela->setDado( "cancelada", true );
                        $obTDATDividaParcela->alteracao();

                        $rsListaDocumentos->proximo();
                    }

                    $obTDATParcelamentoCancelamento = new TDATParcelamentoCancelamento;
                    $obTDATParcelamentoCancelamento->setDado( "num_parcelamento", $arTMP[0] );
                    $obTDATParcelamentoCancelamento->setDado( "numcgm",  Sessao::read('numCgm') );
                    $obTDATParcelamentoCancelamento->setDado( "motivo", $_REQUEST['stMotivo'] );
                    $obTDATParcelamentoCancelamento->inclusao();

                    while ( !$rsListaCarnes->Eof() ) {
                        $obTARRCarneDevolucao->setDado( "numeracao", $rsListaCarnes->getCampo("numeracao") );
                        $obTARRCarneDevolucao->setDado( "cod_convenio", $rsListaCarnes->getCampo("cod_convenio") );
                        $obTARRCarneDevolucao->setDado( "cod_motivo", 12 );
                        $obTARRCarneDevolucao->setDado( "dt_devolucao", $stDataAtual );
                        $obTARRCarneDevolucao->inclusao();

                        $rsListaCarnes->proximo();
                    }

                    Sessao::encerraExcecao();
                }
            }
        }

        SistemaLegado::alertaAviso("FLManterEstorno.php", "Estorno de cobrança de divida efetuada com sucesso!", $stAcao, "aviso", Sessao::getId(), "../");
        break;
}
