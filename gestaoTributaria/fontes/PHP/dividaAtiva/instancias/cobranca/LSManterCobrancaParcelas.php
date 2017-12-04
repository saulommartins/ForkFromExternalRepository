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
  * Página de Lista de Cobranca
  * Data de criação : 03/01/2007

  * @author Analista: Fábio Bertoldi
  * @author Programador: Fernando Piccini Cercato

    * $Id: LSManterCobrancaParcelas.php 61643 2015-02-20 10:45:39Z evandro $

  Caso de uso: uc-05.04.04
**/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaAtiva.class.php" );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATParcelamento.class.php" );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaParcelamento.class.php" );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATModalidade.class.php" );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATModalidadeParcela.class.php" );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRCreditoGrupo.class.php" );
include_once ( CAM_GA_ADM_COMPONENTES."ITextBoxSelectDocumento.class.php" );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMUnidadeAutonoma.class.php"       );

include_once ( CAM_GT_DAT_MAPEAMENTO."FDATBuscaSaldoDivida.class.php" );

$limite = 50; // esta variável limita até o máximo de itens (inscrições) podem ser trazidas na tela.
              // Caso ultrapasse o valor configurado, não será exibida as tabelas por exceder a memória e
              // fica inviável analisar algo.

//Define o nome dos arquivos PHP
$stPrograma = "ManterCobranca";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once ( $pgJS );

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
if ( empty( $_REQUEST['stAcao'] ) ) {
    $_REQUEST['stAcao'] = "incluir";
}

$obTDATModalidade = new TDATModalidade;
$stFiltro = " WHERE dm.cod_modalidade = ".$_REQUEST["inCodModalidade"];
$obTDATModalidade->recuperaModalidadeSelecionada( $rsListaModalidade, $stFiltro );
$obTDATModalidade->ListaAcrescimosDaModalidade( $rsListaAcrescimosDaModalidade, " WHERE modalidade.cod_modalidade = ".$_REQUEST["inCodModalidade"]." AND modalidade_acrescimo.pagamento = false " );

$stFiltro = " WHERE cod_modalidade = ".$_REQUEST["inCodModalidade"]." AND timestamp = '".$rsListaModalidade->getCampo("timestamp")."'";
$obTDATModalidadeParcela = new TDATModalidadeParcela;
$obTDATModalidadeParcela->recuperaTodos( $rsDadosModalidade, $stFiltro );

$obTDATDividaAtiva = new TDATDividaAtiva;

$request = $_REQUEST;
$arDados = array();
$inPosicao = 0;
$flValorTotalGeral = 0;
$flValorTotalAcrescimo = 0;
$flValorTotalReducao = 0;
$flValorTotalOriginal = 0;
$inTotalInscricoes = 0;
$inTotalInscricoesInscrever = 0;

foreach ($request as $valor => $key) {
    if ( preg_match("/boSelecionada_[0-9]/",$valor) ) {
        $arKey = explode( '§',$key );
        // $stFiltroCobranca = " WHERE cod_inscricao = ".$arKey[0]." AND exercicio = ".$arKey[1];
        $stFiltroDividaCancelada = " AND divida_ativa.cod_inscricao = ".$arKey[0]." AND divida_ativa.exercicio = '".$arKey[1]."'";
        // unset( $rsDadosCobranca, $rsDadosDividaCancelada );
        unset( $rsDadosDividaCancelada );
        //verifica se está cancelada e se está em cobrança judicial
        $obTDATDividaAtiva->recuperaConsultaVerificaInscricao( $rsDadosDivida, $stFiltroDividaCancelada );

        if ( !$rsDadosDivida->Eof() ) {
            $inTotalInscricoesInscrever++;
        }

        $inTotalInscricoes++;
    }
}

if ( ( $inTotalInscricoesInscrever > 0 ) && ( $inTotalInscricoes != $inTotalInscricoesInscrever ) ) {
    SistemaLegado::alertaAviso($pgList, "Não é possível efetuar a cobrança com inscrições em dívida que não estejam canceladas ou não estejam em cobrança.", "n_incluir", "erro" );
    exit;
}

$obFDATBuscaSaldoDivida = new FDATBuscaSaldoDivida;

$obTDATDividaParcelamento = new TDATDividaParcelamento;
foreach ($request as $valor => $key) {
    if ( preg_match("/boSelecionada_[0-9]/",$valor) ) {
        $arKey = explode( '§',$key );

        $obFDATBuscaSaldoDivida->setDado('inInscDivida',$arKey[0]);
        $obFDATBuscaSaldoDivida->setDado('stExercicio',$arKey[1]);
        $obFDATBuscaSaldoDivida->recuperaTodos($rsSaldo);

        $stCreditos = '';
        $stOrigemTMP = '';
        while (!$rsSaldo->eof()) {
            $arCreditoFormatado = explode('§',$rsSaldo->getCampo('credito_formatado'));
            $stCreditos .= $arCreditoFormatado[0].';'.$rsSaldo->getCampo('valor_corrigido').';';
            $stOrigem = $rsSaldo->getCampo('origem');
            $stOrigemTMP = $arCreditoFormatado[0].' - '.$arCreditoFormatado[5].'<br>';
            $rsSaldo->proximo();
        }
        $arKey[14] = $stCreditos;
        $arKey[15] = substr($stOrigemTMP, 0,-4);

        $inEncontrouCredito = 0;
        $inTotalCreditos = 0;
        unset( $arCredito );
        $arCredito = array();

        unset( $arCreditoTMP );
        $arCreditoTMP = explode (';', $stCreditos );

        $inTotalCred = count ( $arCreditoTMP );
        for ($inX = 0 ; $inX < $inTotalCred; $inX+=2) {
            if ($arCreditoTMP[$inX] != "") {
                $arCredito[$inTotalCreditos] = $arCreditoTMP[$inX];
                $inTotalCreditos++;
            }
        }

        $rsListaModalidade->setPrimeiroElemento();

        foreach ($arCredito AS $inIndice => $stCredito) {
            $arCreditoTmp = explode( ".", $stCredito );
            $arCretidoValida[sprintf( "%d.%d.%d.%d",$arCreditoTmp[0],$arCreditoTmp[1],$arCreditoTmp[2],$arCreditoTmp[3])] = $inIndice;
        }
        while ( !$rsListaModalidade->Eof() ) {
            $arCreditoTmp = explode( ".", $rsListaModalidade->getCampo( "credito" ) );
            if ( isset( $arCretidoValida[sprintf( "%d.%d.%d.%d",$arCreditoTmp[0],$arCreditoTmp[1],$arCreditoTmp[2],$arCreditoTmp[3])]) ) {
                $inEncontrouCredito++;
                if ($inEncontrouCredito == $inTotalCreditos) {
                    break; //ja encontrou todos creditos necessarios
                }
            }
            $rsListaModalidade->proximo();
        }

        if ($inEncontrouCredito >= $inTotalCreditos) {
            $arDataTMP = explode( "/", $_REQUEST['inDataVencimento'] );
            $dataOrdenacao = $arDataTMP;

            if ($dataOrdenacao[1] > 12) {
                $dataOrdenacao[1] = 01;
                $dataOrdenacao[2] = $dataOrdenacao[2] + 1;
            }

            $inDiaInicial = $dataOrdenacao[0];
            $inDiaSemana  = date("w", mktime(0, 0, 0, sprintf("%02d", $dataOrdenacao[1]), sprintf("%02d", $dataOrdenacao[0]), sprintf("%04d", $dataOrdenacao[2])));
            $inNroDiasMes = date("t", mktime(0, 0, 0, sprintf("%02d", $dataOrdenacao[1]), 01, sprintf("%04d", $dataOrdenacao[2])));

            if ($inNroDiasMes <= $inDiaInicial) {
                $inDiaSemana  = date("w", mktime(0, 0, 0, sprintf("%02d", $dataOrdenacao[1]), sprintf("%02d", $inNroDiasMes), sprintf("%04d", $dataOrdenacao[2])));
                if ($inDiaSemana == 0) {
                    $dtDiaVencimento = $inNroDiasMes - 2;
                } elseif ($inDiaSemana == 6) {
                    $dtDiaVencimento = $inNroDiasMes - 1;
                } else {
                    $dtDiaVencimento = $inNroDiasMes;
                }

            } elseif ( ($inNroDiasMes - 1) == $inDiaInicial ) {
                $inDiaSemana  = date("w", mktime(0, 0, 0, sprintf("%02d", $dataOrdenacao[1]), sprintf("%02d", $inNroDiasMes), sprintf("%04d", $dataOrdenacao[2])));
                if ($inDiaSemana == 0) {
                    $dtDiaVencimento = $inNroDiasMes + 1;
                } elseif ($inDiaSemana == 6) {
                    $dtDiaVencimento = $inNroDiasMes - 1;
                } else {
                    $dtDiaVencimento = $inNroDiasMes;
                }

            } else {
                $inDiaSemana  = date("w", mktime(0, 0, 0, sprintf("%02d", $dataOrdenacao[1]), sprintf("%02d", $inDiaInicial), sprintf("%04d", $dataOrdenacao[2])));
                if ($inDiaSemana == 0) {
                    $dtDiaVencimento = $inDiaInicial + 1;
                } elseif ($inDiaSemana == 6) {
                    $dtDiaVencimento = $inDiaInicial + 2;
                } else {
                    $dtDiaVencimento = $inDiaInicial;
                }

            }

            if ($dtDiaVencimento > 31) {
                $dtDiaVencimento = 1;
                $dataOrdenacao[1] = $dataOrdenacao[1] + 1;
            }

            $stDataAtual = $dataOrdenacao[2].'-'.$dataOrdenacao[1].'-'.$dtDiaVencimento;

            if ($arKey[7]) {
                $inRegistro = $arKey[7];
            }else
                $inRegistro = $_REQUEST['inCGM'];

            unset( $rsUtilizarModalidade );
            $obTDATDividaAtiva->verificaUtilizacaoModalidade( $rsUtilizarModalidade, $_REQUEST["inCodModalidade"], $inRegistro, date("Y-m-d") );

            if ( $rsUtilizarModalidade->getCampo("utilizar") == 't' ) {  //verificar aqui se pode usar modalidade

                $arDados[$inPosicao]["cod_inscricao"]               = $arKey[0];
                $arDados[$inPosicao]["exercicio"]                   = $arKey[1];
                $arDados[$inPosicao]["credito"]                     = $arKey[2];
                $arDados[$inPosicao]["credito_formatado"]           = $arKey[11];
                $arDados[$inPosicao]["num_parcela"]                 = $arKey[3];
                $flValorTotalOriginal += $arDados[$inPosicao]["vlr_parcela"] = $arKey[9]?$arKey[9]:$arKey[4];
                $arDados[$inPosicao]["dt_vencimento_parcela"]       = $arKey[5]?$arKey[5]:$arKey[8];
                $arDados[$inPosicao]["dt_vencimento_parcela_br"]    = $arKey[13];
                $arDados[$inPosicao]["total_de_parcelas_divida"]    = $arKey[6];
                $arDados[$inPosicao]["inscricao"]                   = $inRegistro; //$arKey[7];
                $arDados[$inPosicao]["inscricao_tipo"]              = $arKey[10];
                $arDados[$inPosicao]["dt_vencimento_original"]      = $arKey[8];
                $arDados[$inPosicao]["origem"]                      = $arKey[15].'<br>';
                $arFilters = explode('.', preg_replace('/([\d\.]+) (.*)/', '$1', $arKey[15]));
                $descricao_monetario = SistemaLegado::pegaDado('descricao_credito', 'monetario.credito', 'WHERE cod_credito = '.$arFilters[0].' AND cod_especie = '.$arFilters[1].' AND cod_genero = '.$arFilters[2].' AND cod_natureza = '.$arFilters[3].'');
                $arDados[$inPosicao]["descricao_credito"]			= $descricao_monetario;
                $arDados[$inPosicao]["grupo_original"]              = $arKey[16];
                $arDados[$inPosicao]["numcgm"]                      = $arKey[17];
                unset( $rsRecuperaExercicio );
                $obTDATDividaAtiva->recuperaTodos( $rsRecuperaExercicio, " where cod_inscricao = ".$arDados[$inPosicao]["cod_inscricao"]." and exercicio = '".$arDados[$inPosicao]["exercicio"]."'" );
                if ( !$rsRecuperaExercicio->Eof() ) {
                    $arDados[$inPosicao]["exercicio_original"] = $rsRecuperaExercicio->getCampo("exercicio_original");
                }

                $arDados[$inPosicao]["reducao"] = 0.00;
                $arDados[$inPosicao]["vlr_reducao"] = 0.00;

                $inTipoCobranca = Sessao::read("cobrancaJudicial")?1:0;
                unset( $rsListaJurosMulta );
                $stFiltroInscricao = " WHERE cod_inscricao = ".$arKey[0]." AND exercicio = '".$arKey[1]."'";
                $obTDATDividaParcelamento->recuperaTodos( $rsListaNumParcelamento, $stFiltroInscricao );
                $obTDATDividaAtiva->recuperaJurosMulta ( $rsListaJurosMulta, $inTipoCobranca,
                    $arDados[$inPosicao]["cod_inscricao"], $arDados[$inPosicao]["exercicio"],
                    $_REQUEST["inCodModalidade"], 0,
                    $arDados[$inPosicao]["vlr_parcela"],
                    $arDados[$inPosicao]["dt_vencimento_original"],
                    $stDataAtual,
                    'false'
                );

                $arDados[$inPosicao]["total_de_acrescimos"] = 0;
                $arTmpJuros = explode( ";", $rsListaJurosMulta->getCampo("juros") );
                $flValorTotalAcrescimo += $arDados[$inPosicao]["juros"] = $arTmpJuros[0];
                for ( $inJ=1; $inJ<count( $arTmpJuros ); $inJ+=3 ) {
                    $rsListaAcrescimosDaModalidade->setPrimeiroElemento();
                    while ( !$rsListaAcrescimosDaModalidade->Eof() ) {
                        if ( $arTmpJuros[$inJ+1] == $rsListaAcrescimosDaModalidade->getCampo("cod_acrescimo") && ($arTmpJuros[$inJ+2] == $rsListaAcrescimosDaModalidade->getCampo("cod_tipo")) ) {
                            $arDados[$inPosicao][ "valor_acrescimo_".$arDados[$inPosicao]["total_de_acrescimos"] ] = $arTmpJuros[$inJ];
                            $arDados[$inPosicao][ "nome_acrescimo_".$arDados[$inPosicao]["total_de_acrescimos"] ] = $rsListaAcrescimosDaModalidade->getCampo("descricao_acrescimo");
                            $arDados[$inPosicao][ "total_de_acrescimos" ]++;
                            break;
                        }
                        $rsListaAcrescimosDaModalidade->proximo();
                    }

                    unset( $rsListaReducao );
                    $obTDATDividaAtiva->aplicaReducaoModalidadeAcrescimo(
                        $rsListaReducao, $_REQUEST["inCodModalidade"], $inRegistro, $arTmpJuros[$inJ],
                        $arTmpJuros[$inJ+1], $arTmpJuros[$inJ+2],
                        $arDados[$inPosicao]["dt_vencimento_original"], $_REQUEST['cmbParcelas']
                    ); //verificando se deve aplicar reducao no acrescimo (juros)
                    #$obTDATDividaAtiva->debug();
                    if ( $rsListaReducao->getCampo("valor") > 0 ) {
                        $arDados[$inPosicao]["reducao"] += $rsListaReducao->getCampo("valor");
                        $arDados[$inPosicao]["vlr_reducao"] += $rsListaReducao->getCampo("valor");
                        $flValorTotalReducao += $rsListaReducao->getCampo("valor");
                    }
                }

                unset( $arTmpMulta );
                $arTmpMulta = explode( ";", $rsListaJurosMulta->getCampo("multa") );
                $flValorTotalAcrescimo += $arDados[$inPosicao]["multa"] = $arTmpMulta[0];
                for ( $inJ=1; $inJ<count( $arTmpMulta ); $inJ+=3 ) {
                    $rsListaAcrescimosDaModalidade->setPrimeiroElemento();
                    while ( !$rsListaAcrescimosDaModalidade->Eof() ) {
                        if ( $arTmpMulta[$inJ+1] == $rsListaAcrescimosDaModalidade->getCampo("cod_acrescimo") && ($arTmpMulta[$inJ+2] == $rsListaAcrescimosDaModalidade->getCampo("cod_tipo")) ) {
                            $arDados[$inPosicao][ "valor_acrescimo_".$arDados[$inPosicao]["total_de_acrescimos"] ] = $arTmpMulta[$inJ];
                            $arDados[$inPosicao][ "nome_acrescimo_".$arDados[$inPosicao]["total_de_acrescimos"] ] = $rsListaAcrescimosDaModalidade->getCampo("descricao_acrescimo");
                            $arDados[$inPosicao][ "total_de_acrescimos" ]++;
                            break;
                        }

                        $rsListaAcrescimosDaModalidade->proximo();
                    }

                    unset( $rsListaReducao );
                    $obTDATDividaAtiva->aplicaReducaoModalidadeAcrescimo(
                        $rsListaReducao, $_REQUEST["inCodModalidade"], $inRegistro, $arTmpMulta[$inJ],
                        $arTmpMulta[$inJ+1], $arTmpMulta[$inJ+2],
                        $arDados[$inPosicao]["dt_vencimento_original"],
                        $_REQUEST['cmbParcelas']
                    ); //verificando se deve aplicar reducao no acrescimo (multa)

                    if ( $rsListaReducao->getCampo("valor") > 0 ) {
                        $arDados[$inPosicao]["reducao"] += $rsListaReducao->getCampo("valor");
                        $arDados[$inPosicao]["vlr_reducao"] += $rsListaReducao->getCampo("valor");
                        $flValorTotalReducao += $rsListaReducao->getCampo("valor");
                    }
                }

                unset( $arTmpCorrecao );
                $arTmpCorrecao = explode( ";", $rsListaJurosMulta->getCampo("correcao") );
                $flValorTotalAcrescimo += $arDados[$inPosicao]["correcao"] = $arTmpCorrecao[0];
                for ( $inJ=1; $inJ<count( $arTmpCorrecao ); $inJ+=3 ) {
                    $rsListaAcrescimosDaModalidade->setPrimeiroElemento();
                    while ( !$rsListaAcrescimosDaModalidade->Eof() ) {
                        if ( $arTmpCorrecao[$inJ+1] == $rsListaAcrescimosDaModalidade->getCampo("cod_acrescimo") && ($arTmpCorrecao[$inJ+2] == $rsListaAcrescimosDaModalidade->getCampo("cod_tipo")) ) {
                            $arDados[$inPosicao][ "valor_acrescimo_".$arDados[$inPosicao]["total_de_acrescimos"] ] = $arTmpCorrecao[$inJ];
                            $arDados[$inPosicao][ "nome_acrescimo_".$arDados[$inPosicao]["total_de_acrescimos"] ] = $rsListaAcrescimosDaModalidade->getCampo("descricao_acrescimo");
                            $arDados[$inPosicao][ "total_de_acrescimos" ]++;
                            break;
                        }

                        $rsListaAcrescimosDaModalidade->proximo();
                    }

                    $obTDATDividaAtiva->aplicaReducaoModalidadeAcrescimo(
                        $rsListaReducao, $_REQUEST["inCodModalidade"], $inRegistro,
                        $arTmpCorrecao[$inJ], $arTmpCorrecao[$inJ+1], $arTmpCorrecao[$inJ+2],
                        $arDados[$inPosicao]["dt_vencimento_original"], $_REQUEST['cmbParcelas']
                    ); //verificando se deve aplicar reducao no acrescimo (correcao)
                    #$obTDATDividaAtiva->debug();
                    if ( $rsListaReducao->getCampo("valor") > 0 ) {
                        $arDados[$inPosicao]["reducao"] += $rsListaReducao->getCampo("valor");
                        $arDados[$inPosicao]["vlr_reducao"] += $rsListaReducao->getCampo("valor");
                        $flValorTotalReducao += $rsListaReducao->getCampo("valor");
                    }
                }

                $rsListaAcrescimosDaModalidade->setPrimeiroElemento();
                while ( !$rsListaAcrescimosDaModalidade->Eof() ) {
                    $boIncluir = true;
                    for ($inJ=0; $inJ<$arDados[$inPosicao]["total_de_acrescimos"]; $inJ++) {
                        if ( $arDados[$inPosicao][ "nome_acrescimo_".$inJ ] == $rsListaAcrescimosDaModalidade->getCampo("descricao_acrescimo") ) {
                            $boIncluir = false;
                            break;
                        }
                    }

                    if ($boIncluir) {
                        $arDados[$inPosicao][ "nome_acrescimo_".$arDados[$inPosicao]["total_de_acrescimos"] ] = $rsListaAcrescimosDaModalidade->getCampo("descricao_acrescimo");
                        $arDados[$inPosicao][ "valor_acrescimo_".$arDados[$inPosicao]["total_de_acrescimos"] ] = 0.00;
                        $arDados[$inPosicao]["total_de_acrescimos"]++;
                    }

                    $rsListaAcrescimosDaModalidade->proximo();
                }

                $contCreditos = 0;
                $arSTCredito = explode ( ';', $stCreditos );
                while ( $contCreditos < ( count( $arSTCredito ) -1 ) ) {
                    unset( $arTMPCreditos );
                    $arTMPCreditos = explode( ".", $arSTCredito[$contCreditos] );

                    $obTDATDividaAtiva->aplicaReducaoModalidadeCredito(
                        $rsListaReducao
                        , $_REQUEST["inCodModalidade"]
                        , $inRegistro
                        , $arSTCredito[($contCreditos + 1)]
                        , $arTMPCreditos[0], $arTMPCreditos[1], $arTMPCreditos[2], $arTMPCreditos[3]
                        , $arDados[$inPosicao]["dt_vencimento_original"]
                        , $_REQUEST['cmbParcelas']
                    );
                    if ( $rsListaReducao->getCampo("valor") > 0 ) {
                        $arDados[$inPosicao]["reducao"]     += $rsListaReducao->getCampo("valor");
                        $arDados[$inPosicao]["vlr_reducao"] += $rsListaReducao->getCampo("valor");
                        $flValorTotalReducao += $rsListaReducao->getCampo("valor");
                    }

                    $contCreditos += 2;
                }

                if ($arDados[$inPosicao]["vlr_reducao"] > 0) {
                    $arDados[$inPosicao]["vlr_final"] = (
                                            $arDados[$inPosicao]["vlr_parcela"]
                                            + $arDados[$inPosicao]["juros"]
                                            + $arDados[$inPosicao]["multa"]
                                            + $arDados[$inPosicao]["correcao"]
                        ) - $arDados[$inPosicao]["vlr_reducao"] ;
                } else {
                    $arDados[$inPosicao]["vlr_final"] = $arDados[$inPosicao]["vlr_parcela"]+$arDados[$inPosicao]["juros"]+$arDados[$inPosicao]["multa"]+$arDados[$inPosicao]["correcao"];
                }

                $flValorTotalGeral += $arDados[$inPosicao]["vlr_final"];
                $inPosicao++;
            }
        } else {
            SistemaLegado::alertaAviso($pgList, "Os créditos referentes às inscrições selecionadas para cobrança não estão disponíveis na modalidade selecionada!", "n_incluir", "erro", Sessao::getId(), "../");
            exit;
        }
    }
}

$newDtVencimento = $_REQUEST['inDataVencimento'];
$inTotalParcelas = $_REQUEST['cmbParcelas'];

$boAprovado = false;
$inQtdParcelasMax = 0;
while ( !$rsDadosModalidade->Eof() ) {
    if ( ( $flValorTotalGeral >= $rsDadosModalidade->getCampo("vlr_limite_inicial") ) && ($flValorTotalGeral <= $rsDadosModalidade->getCampo("vlr_limite_final")) ) {
        if ( $inTotalParcelas <= $rsDadosModalidade->getCampo("qtd_parcela") ) {
            $boAprovado = true;
            break;
        } else {
            $inQtdParcelasMax = $rsDadosModalidade->getCampo("qtd_parcela");
        }
    }

    $rsDadosModalidade->proximo();
}

if (!$boAprovado) {
    SistemaLegado::alertaAviso($pgList, "A quantidade de parcelas informada excede o total de parcelas permitido! (Máximo: ".$inQtdParcelasMax." parcelas)", "n_incluir", "erro", Sessao::getId(), "../");
    exit;
}

$contDados = 0;
unset( $arInscricoes );
$arInscricoes = array();
$inInscricaoAtual = null;
$inExercicioAtual = null;
$obRegra = new RCIMUnidadeAutonoma( new RCIMImovel( new RCIMLote) );
while ( $contDados < count ($arDados) ) {
    if ( ( $inInscricaoAtual != $arDados[$contDados]["cod_inscricao"] )
        || ( $inExercicioAtual != $arDados[$contDados]["exercicio"] )
    ){
        $inCodInscricaoDividaAtual  = $arDados[$contDados]["cod_inscricao"];
        $inInscricaoAtual           = $arDados[$contDados]["cod_inscricao"];
        $inExercicioAtual           = $arDados[$contDados]["exercicio"];
        $stTipoInscricao            = $arDados[$contDados]["inscricao_tipo"];
        $arInscricoes[$contDados]['exercicio_original'] = $arDados[$contDados]["exercicio_original"];
        $arInscricoes[$contDados]['inscricao'] = $arDados[$contDados]["inscricao"];
        $arInscricoes[$contDados]['cod_inscricao'] = $inCodInscricaoDividaAtual;
        $arInscricoes[$contDados]['inscricao_tipo'] = $stTipoInscricao;
        $arInscricoes[$contDados]['exercicio'] = $arDados[$contDados]["exercicio"];
        $arInscricoes[$contDados]['inscricao_endereco'] = null;

        if ($stTipoInscricao == 'imobiliaria') {

            $obRegra->roRCIMImovel->setNumeroInscricao( $arDados[$contDados]["inscricao"] );
            $obRegra->roRCIMImovel->listarImoveisConsulta( $rsImoveis );
            if ( $rsImoveis->getNumLinhas() > 0 ) {
                $arInscricoes[$contDados]['inscricao_endereco'] = $rsImoveis->getCampo('endereco');
            }

        }

        $stLinhaInscricoes =    $arDados[$contDados]["inscricao"] . ' - ' .$arInscricoes[$contDados]['inscricao_endereco'];
        $stLinhaInscricoes .=   ' / '.$arInscricoes[$contDados]['exercicio'].'<br>';
    }

    $contDados ++;
}
$stLinhaInscricoes = substr ( $stLinhaInscricoes, 0, strlen ( $stLinhaInscricoes ) - 4 );
Sessao::write('inscricoes', $arInscricoes);

$rsInscricoes = new RecordSet;
$rsInscricoes->preenche ( $arInscricoes );

if ( count($arInscricoes) <= $limite ) {

    $table = new Table();
    $table->setRecordset( $rsInscricoes );
    $table->setSummary('Inscrições Vinculadas à cobrança');

    //$table->setConditional( true , "#efefef" );

    $table->Head->addCabecalho( 'Insc. Dívida' , 15 );
    $table->Head->addCabecalho( 'Exercício Devedor' , 10 );
    $table->Head->addCabecalho( 'Inscrição' , 15 );
    $table->Head->addCabecalho( 'Dados Complementares' , 55 );

    $stTitleLanc = "";
    $table->Body->addCampo( '[cod_inscricao]/[exercicio] ', "C", $stTitleLanc );
    $table->Body->addCampo( 'exercicio_original', "C", $stTitleLanc );
    $table->Body->addCampo( 'inscricao', "C", $stTitleLanc );
    $table->Body->addCampo( 'inscricao_endereco', "E", $stTitleLanc );

    $table->montaHTML();
}

do {
    $flValorPrimeiraParcelaAC = $flTotalPorParcelaAC = round($flValorTotalAcrescimo / $inTotalParcelas, 2);
    $flDiferencaParcelas = $flValorTotalAcrescimo - ( $flTotalPorParcelaAC * $inTotalParcelas );
    if ( $flDiferencaParcelas != 0 )
        $flValorPrimeiraParcelaAC += $flDiferencaParcelas;

    $flValorPrimeiraParcelaRD = $flTotalPorParcelaRD = round($flValorTotalReducao / $inTotalParcelas, 2);
    $flDiferencaParcelas = $flValorTotalReducao - ( $flTotalPorParcelaRD * $inTotalParcelas );
    if ( $flDiferencaParcelas != 0 )
        $flValorPrimeiraParcelaRD += $flDiferencaParcelas;

    $flValorPrimeiraParcelaOR = $flTotalPorParcelaOR = round($flValorTotalOriginal / $inTotalParcelas, 2);
    $flDiferencaParcelas = $flValorTotalOriginal - ( $flTotalPorParcelaOR * $inTotalParcelas );
    if ( $flDiferencaParcelas != 0 )
        $flValorPrimeiraParcelaOR += $flDiferencaParcelas;

    $flValorPrimeiraParcela = ($flValorPrimeiraParcelaOR + $flValorPrimeiraParcelaAC) - $flValorPrimeiraParcelaRD;
    $flTotalPorParcela = ($flTotalPorParcelaOR + $flTotalPorParcelaAC) - $flTotalPorParcelaRD;
    if ( $flTotalPorParcela < $rsDadosModalidade->getCampo( "vlr_minimo" ) ) {
        $inTotalParcelas--;
        if ($inTotalParcelas <= 0) {
            SistemaLegado::alertaAviso($pgList, "O valor das parcelas é inferior ao valor minimo! (Minimo: ".$rsDadosModalidade->getCampo( "vlr_minimo" )." )", "n_incluir", "erro", Sessao::getId(), "../");
            exit;
        }
    }
} while ( $flTotalPorParcela < $rsDadosModalidade->getCampo( "vlr_minimo" ) );

$dataOrdenacao = explode ('/', $newDtVencimento);
$count = 0;
for ($inQtdParcelas=0; $inQtdParcelas < $inTotalParcelas; $inQtdParcelas++) {

    if ($dataOrdenacao[1] > 12) {
        $dataOrdenacao[1] = 01;
        $dataOrdenacao[2] = $dataOrdenacao[2] + 1;
    }

    $inDiaInicial = $dataOrdenacao[0];
    $inDiaSemana  = date("w", mktime(0, 0, 0, sprintf("%02d", $dataOrdenacao[1]), sprintf("%02d", $dataOrdenacao[0]), sprintf("%04d", $dataOrdenacao[2])));
    $inNroDiasMes = date("t", mktime(0, 0, 0, sprintf("%02d", $dataOrdenacao[1]), 01, sprintf("%04d", $dataOrdenacao[2])));

    if ($inNroDiasMes <= $inDiaInicial) {
        $inDiaSemana  = date("w", mktime(0, 0, 0, sprintf("%02d", $dataOrdenacao[1]), sprintf("%02d", $inNroDiasMes), sprintf("%04d", $dataOrdenacao[2])));
        if ($inDiaSemana == 0) {
            $dtDiaVencimento = $inNroDiasMes - 2;
        } elseif ($inDiaSemana == 6) {
            $dtDiaVencimento = $inNroDiasMes - 1;
        } else {
            $dtDiaVencimento = $inNroDiasMes;
        }

    } elseif ( ($inNroDiasMes - 1) == $inDiaInicial ) {
        $inDiaSemana  = date("w", mktime(0, 0, 0, sprintf("%02d", $dataOrdenacao[1]), sprintf("%02d", $inNroDiasMes), sprintf("%04d", $dataOrdenacao[2])));
        if ($inDiaSemana == 0) {
            $dtDiaVencimento = $inNroDiasMes + 1;
        } elseif ($inDiaSemana == 6) {
            $dtDiaVencimento = $inNroDiasMes - 1;
        } else {
            $dtDiaVencimento = $inNroDiasMes;
        }

    } else {
        $inDiaSemana  = date("w", mktime(0, 0, 0, sprintf("%02d", $dataOrdenacao[1]), sprintf("%02d", $inDiaInicial), sprintf("%04d", $dataOrdenacao[2])));
        if ($inDiaSemana == 0) {
            $dtDiaVencimento = $inDiaInicial + 1;
        } elseif ($inDiaSemana == 6) {
            $dtDiaVencimento = $inDiaInicial + 2;
        } else {
            $dtDiaVencimento = $inDiaInicial;
        }

    }

    if ( $inQtdParcelas == 0 )
        $arrayParcelasTMP[$inQtdParcelas]['vlr_parcela'] = $flValorPrimeiraParcela;
    else
        $arrayParcelasTMP[$inQtdParcelas]['vlr_parcela'] = $flTotalPorParcela;

    $arrayParcelasTMP[$inQtdParcelas]['nr_parcela'] = $inQtdParcelas + 1;

    $boAvancaData = true;
    if ($inDiaInicial > 31) {
        $inDiaInicial = 1;
        $dataOrdenacao[1] = $dataOrdenacao[1] + 1;
        $boAvancaData = false;
    }

    if ($dtDiaVencimento > 31) {
        $dtDiaVencimento = 1;
        $dataOrdenacao[1] = $dataOrdenacao[1] + 1;
        $boAvancaData = false;
    }

    $arrayParcelasTMP[$inQtdParcelas]['data_vencimento'] = sprintf("%02d/%02d/%04d", $dtDiaVencimento, $dataOrdenacao[1], $dataOrdenacao[2]);
    #$arrayParcelasTMP[$inQtdParcelas]['data_vencimento_br'] =

    $newDtVencimento = sprintf("%02d/%02d/%04d", $inDiaInicial, $dataOrdenacao[1], $dataOrdenacao[2] );
    if ( $boAvancaData )
        $dataOrdenacao[1]++;
}

#============================================= DADOS

for ($i=0; $i <count($arDados) ; $i++) { 
    $arDados[$i]["vlr_reducao"] = number_format($arDados[$i]["vlr_reducao"],2,',','.');
}

$rsDados = new RecordSet;
$rsDados->preenche( $arDados );
$rsDados->addFormatacao ('vlr_final', 'NUMERIC_BR');
$rsDados->addFormatacao ('juros', 'NUMERIC_BR');
$rsDados->addFormatacao ('multa', 'NUMERIC_BR');
$rsDados->addFormatacao ('correcao', 'NUMERIC_BR');
$rsDados->addFormatacao ('vlr_reducao', 'NUMERIC_BR');
$rsDados->addFormatacao ('vlr_parcela', 'NUMERIC_BR');

Sessao::write('dados', $arDados);

for ( $inJ=0; $inJ<$rsDados->getCampo("total_de_acrescimos"); $inJ++ ) {
    $rsDados->addFormatacao ( "valor_acrescimo_".$inJ, 'NUMERIC_BR' );
}

if ( count($arDados) <=  $limite ) {

    $tableDados = new Table();
    $tableDados->setRecordset( $rsDados );
    $tableDados->setSummary('Relatório de Cobrança Detalhado');

    $tableDados->Head->addCabecalho( 'Crédito/Descrição/Grupo de Crédito' , 30 );
    $tableDados->Head->addCabecalho( 'Vencimento' , 10 );
    $tableDados->Head->addCabecalho( 'Valor Origem' , 10 );

    for ( $inJ=0; $inJ<$rsDados->getCampo("total_de_acrescimos"); $inJ++ ) {
        $tableDados->Head->addCabecalho( $rsDados->getCampo( "nome_acrescimo_".$inJ ), 9 );
    }

    $tableDados->Head->addCabecalho( 'Redução' , 9 );

    $tableDados->Head->addCabecalho( 'Sub-Total' , 15 );

    $stTitleLanc = "";
    $tableDados->Body->addCampo( '[origem] [descricao_credito] - [grupo_original];', "E", $stTitleLanc );
    $tableDados->Body->addCampo( 'dt_vencimento_parcela_br', "C", $stTitleLanc );
    $tableDados->Body->addCampo( 'vlr_parcela', "D", $stTitleLanc );

    for ( $inJ=0; $inJ<$rsDados->getCampo("total_de_acrescimos"); $inJ++ ) {
        $tableDados->Body->addCampo( "valor_acrescimo_".$inJ, "D", $stTitleLanc );
    }

    $tableDados->Body->addCampo( 'vlr_reducao', "D", $stTitleLanc );
    $tableDados->Body->addCampo( 'vlr_final', "D", $stTitleLanc );

    $tableDados->Foot->addSoma ( 'vlr_final', "D" );

    $tableDados->montaHTML();
}

#============================================= PARCELAS
Sessao::write('parcelas', $arrayParcelasTMP);

$rsDadosParcelas = new RecordSet;
$rsDadosParcelas->preenche( $arrayParcelasTMP );
$rsDadosParcelas->addFormatacao ('vlr_parcela', 'NUMERIC_BR');

$obTxtV = new Data;
$obTxtV->setName ('Vencimento');
$obTxtV->setID ('Vencimento');
$obTxtV->setValue ( '[data_vencimento]' );
$obTxtV->obEvento->setOnChange ( "buscaValor('alteraData')" );

$tableParcelas = new Table();
$tableParcelas->setRecordset( $rsDadosParcelas );
$tableParcelas->setSummary('Relatório de Cobrança');

$tableParcelas->Head->addCabecalho( 'Parcela' , 10 );
$tableParcelas->Head->addCabecalho( 'Vencimento' , 10 );
$tableParcelas->Head->addCabecalho( 'Valor' , 20 );
$tableParcelas->Head->addCabecalho( '' , 70 );

$stTitleLanc = "";
$tableParcelas->Body->addCampo( 'nr_parcela', "C", $stTitleLanc );
$tableParcelas->Body->addCampo( $obTxtV, "C", $stTitleLanc );
$tableParcelas->Body->addCampo( 'vlr_parcela', "D", $stTitleLanc );
$tableParcelas->Body->addCampo( '', "E", $stTitleLanc );

$tableParcelas->montaHTML();

#--------------------------------------------- Montagem Formulario

if ( count($arInscricoes) <= $limite ) {
    $stHtml = $table->getHtml();
}
if ( count($arDados) <= $limite ) {
    $stHtml .= $tableDados->getHTML();
}
$stHtml .= $tableParcelas->getHTML();

$obSpanDados = new Span;
$obSpanDados->setId      ( "spnLista" );
$obSpanDados->setValue   ( $stHtml );

$obLblContribuinte = new Label;
$obLblContribuinte->setRotulo    ( "CGM" );
$obLblContribuinte->setName      ( "stContribuinte" );
$obLblContribuinte->setValue     ( isset($_REQUEST['stProprietario']) ? $_REQUEST['stProprietario'] : $_REQUEST['inCGM']." - ".$_REQUEST["stNomCGM"] );
$obLblContribuinte->setTitle     ( "CGM" );

$rsListaModalidade->setPrimeiroElemento();

$obLblModalidade = new Label;
$obLblModalidade->setRotulo    ( "Modalidade" );
$obLblModalidade->setName      ( "stModalidade" );
$obLblModalidade->setValue     ( $rsListaModalidade->getCampo("cod_modalidade")." - ".$rsListaModalidade->getCampo("descricao") );
$obLblModalidade->setTitle     ( "Modalidade" );

$obRdbEmissaoNaoEmitir = new Radio;
$obRdbEmissaoNaoEmitir->setTitle    ( "Informe se deverá ou não ser emitido carnê."  );
$obRdbEmissaoNaoEmitir->setRotulo   ( "Emissão de Carnês"                            );
$obRdbEmissaoNaoEmitir->setName     ( "emissao_carnes"                               );
$obRdbEmissaoNaoEmitir->setId       ( "emissao_carnes"                               );
$obRdbEmissaoNaoEmitir->setLabel    ( "Não Emitir"                                   );
$obRdbEmissaoNaoEmitir->setValue    ( "nao_emitir"                                   );
$obRdbEmissaoNaoEmitir->setNull     ( false                                          );
$obRdbEmissaoNaoEmitir->setChecked  ( true                                           );

$obRdbEmissaoLocal = new Radio;
$obRdbEmissaoLocal->setRotulo   ( "Emissão de Carnês"                            );
$obRdbEmissaoLocal->setName     ( "emissao_carnes"                               );
$obRdbEmissaoLocal->setId       ( "emissao_carnes"                               );
$obRdbEmissaoLocal->setLabel    ( "Impressão Local"                              );
$obRdbEmissaoLocal->setValue    ( "local"                                        );
$obRdbEmissaoLocal->setNull     ( false                                          );
$obRdbEmissaoLocal->setChecked  ( false                                          );

$obChkTermoParcelamento = new Checkbox;
$obChkTermoParcelamento->setName   ( "boEmitirParcelamento"  );
$obChkTermoParcelamento->setRotulo ( "Emitir Documentos da Cobrança" );
$obChkTermoParcelamento->setValue  ( "emitir" );

include_once( CAM_GT_ARR_NEGOCIO."RARRCarne.class.php" );

$obRARRCarne = new RARRCarne;
$obRARRCarne->listarModeloDeCarne( $rsModelos, Sessao::read('acao') );

$obCmbModelo = new Select;
$obCmbModelo->setRotulo       ( "Modelo" );
$obCmbModelo->setTitle        ( "Modelo de carne" );
$obCmbModelo->setName         ( "cmbModelo" );
$obCmbModelo->addOption       ( "", "Selecione" );
$obCmbModelo->setCampoId      ( "nom_arquivo" );
$obCmbModelo->setCampoDesc    ( "nom_modelo" );
$obCmbModelo->preencheCombo   ( $rsModelos );
$obCmbModelo->setStyle        ( "width: 100%;" );
$obCmbModelo->setNULL         ( true );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( $_REQUEST['stCtrl']  );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $_REQUEST['stAcao']  );

$obHdnCodModalidade = new Hidden;
$obHdnCodModalidade->setName  ( "inCodModalidade" );
$obHdnCodModalidade->setValue ( $_REQUEST["inCodModalidade"] );

$obHdnCGM = new Hidden;
$obHdnCGM->setName  ( "inCGM" );
$obHdnCGM->setValue ( $_REQUEST['inCGM'] );

$arVenc = explode( "/", $arrayParcelasTMP[0]["data_vencimento"] );

$obHdnVenc = new Hidden;
$obHdnVenc->setName  ( "stDataVenc" );
$obHdnVenc->setValue ( $arVenc[2]."-".$arVenc[1]."-".$arVenc[0] );

$stLocation =  'OCGeraRelatorioSimulacaoCobranca.php?'.Sessao::getId().'&stAcao='.$_REQUEST['stAcao'];
$stLocation .= '&inCGM='.$_REQUEST['inCGM'].'&stNomCGM='.$_REQUEST['stNomCGM'];
$stLocation .= '&inCGM='.$_REQUEST['inCGM'].'&stNomCGM='.$_REQUEST['stNomCGM'];
$stLocation .= '&stDescModalidade='.$rsListaModalidade->getCampo("descricao");
$stLocation .= '&inCodModalidade='.$rsListaModalidade->getCampo("cod_modalidade");

$obButtonRelatorio = new Button;
$obButtonRelatorio->setName  ( "Relatorio" );
$obButtonRelatorio->setValue ( "Simulação de Cobrança" );
$obButtonRelatorio->obEvento->setOnClick( "window.parent.frames['oculto'].location='".$stLocation."';");

$obBtnOK = new OK;
$obBtnCancelar = new CANCELAR;

$obBtnVoltar = new Button;
$obBtnVoltar->setName               ( "btnVoltar" );
$obBtnVoltar->setValue              ( "Voltar" );
$obBtnVoltar->setTipo               ( "button" );
$obBtnVoltar->obEvento->setOnClick  ( "VoltarTela();" );
$obBtnVoltar->setDisabled           ( false );

$arBotoes = array( $obBtnOK, $obBtnCancelar, $obBtnVoltar );

$obForm = new Form;
$obForm->setAction ( $pgProc );
$obForm->settarget ( "oculto" );

$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm );
$obFormulario->addHidden     ( $obHdnCtrl );
$obFormulario->addHidden     ( $obHdnAcao );
$obFormulario->addHidden     ( $obHdnVenc );
$obFormulario->addHidden     ( $obHdnCodModalidade );
$obFormulario->addHidden     ( $obHdnCGM );
$obFormulario->addTitulo     ( "Dados do Contribuinte" );
$obFormulario->addComponente ( $obLblContribuinte );
$obFormulario->addComponente ( $obLblModalidade );
$obFormulario->addSpan( $obSpanDados );
$obFormulario->agrupaComponentes( array( $obRdbEmissaoNaoEmitir, $obRdbEmissaoLocal ) );
$obFormulario->addComponente ( $obCmbModelo );
$obFormulario->addComponente ( $obChkTermoParcelamento );

$obFormulario->defineBarra( array( $obButtonRelatorio ), "left", "" );
$obFormulario->defineBarra( $arBotoes );

$obFormulario->show();
