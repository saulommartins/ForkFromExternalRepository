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
 * Página de Formulario de Emissao

 * Data de Criação   : 26/09/2006

 * @author Analista: Fábio Bertoldi Rodrigues
 * @author Desenvolvedor: Fernando Piccini Cercato
 * @ignore

 * $Id: FMManterEmissao.php 63615 2015-09-18 14:11:12Z evandro $

 * Casos de uso: uc-05.04.03

 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GT_DAT_MAPEAMENTO."TDATDividaAtiva.class.php";
include_once CAM_GT_DAT_MAPEAMENTO."TDATDividaParcela.class.php";
include_once CAM_GT_DAT_MAPEAMENTO."TDATEmissaoDocumento.class.php";
include_once CAM_OOPARSER."tbs_class.php";
include_once CAM_OOPARSER."tbsooo_class.php";

if (empty($_REQUEST['stAcao'])) {
    $_REQUEST['stAcao'] = "alterar";
}

//Define o nome dos arquivos PHP
$stPrograma = "ManterEmissao";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php?".Sessao::getId();
$pgJs       = "JS".$stPrograma.".js";

include_once $pgJs;

Sessao::remove('link');
Sessao::remove('stLink');

$arLista = $arDados = $arDadosArquivos = array();
$inTotal = $inTamanhoLista = $num= 0;
foreach ($_REQUEST as $valor => $key) {
    if ( preg_match("/^[a-z]boEmitir_[0-9]/",$valor) ) {
        $arKey = explode('&',$key); //numcgm,cod_inscricao                
        $boJaNaLista = false;
        for ($inX=0; $inX<$inTamanhoLista; $inX++) {
            if ( ( $arLista[$inX][3] == $arKey[3] ) && ( in_array( $arLista[$inX][0], explode( "§", $arKey[0] ) ) ) && $num !=$arKey[6]) {
                $arLista[$inX][6] .= ",".$arKey[6];
                $boJaNaLista = true;
                break;
            }
        }
        $num=$arKey[6];
        if (!$boJaNaLista) {
            $arLista[$inTamanhoLista] = $arKey;
            $inTamanhoLista++;
        }
    }
}

$obTDATEmissaoDocumento = new TDATEmissaoDocumento;

$arDadosSessao = Sessao::read( 'dados_emissao' );

foreach ($arLista as $valor => $arKey) {

    //registrando numeracao dos documentos
    Sessao::setTrataExcecao( true );
    Sessao::getTransacao()->setMapeamento( $obTDATEmissaoDocumento );
        $inExercicio = $arKey[5]?$arKey[5]:Sessao::getExercicio();
        if ($inExercicio == -1) {
            $inExercicio = Sessao::getExercicio();
        }

        $inArquivo = $arKey[6].$arKey[7].$arKey[9].$inExercicio;

        if ($arKey[10] && !$arDadosSessao[$inArquivo]["num_documento"]) {
            $arDadosSessao[$inArquivo]["num_documento"] = $arKey[10];
        }

        $arParcelamentos = explode( ",", $arKey[6] );
        for ($inX=0; $inX<count($arParcelamentos); $inX++) {
            //gerar numero do documento apenas por cod_documento, cod_tipo_documento e exercicio
            $stFiltro = " WHERE cod_documento = ".$arKey[7]." AND cod_tipo_documento = ".$arKey[9]." AND exercicio = '".$inExercicio."'";
            if ( isset($arDadosSessao[$inArquivo]["num_documento"]) ) {
                $stFiltro .= " AND num_documento = ".$arDadosSessao[$inArquivo]["num_documento"];

                $obTDATEmissaoDocumento->recuperaTodos( $rsTotalEmissao, $stFiltro, " ORDER BY num_emissao DESC LIMIT 1 " );
                if ( $rsTotalEmissao->Eof() )
                    $inNumEmissao = 1;
                else {
                    if ( $inX )
                        $inNumEmissao = $rsTotalEmissao->getCampo("num_emissao");
                    else
                        $inNumEmissao = $rsTotalEmissao->getCampo("num_emissao")+1;
                }
            } else {
                $obTDATEmissaoDocumento->recuperaTodos( $rsTotalEmissao, $stFiltro, " ORDER BY num_documento DESC LIMIT 1 " );
                $inNumEmissao = 1;
                $arDadosSessao[$inArquivo]["num_documento"] = $rsTotalEmissao->getCampo("num_documento")+1;
            }

            $obTDATEmissaoDocumento->setDado( "cod_documento", $arKey[7] );
            $obTDATEmissaoDocumento->setDado( "num_documento", $arDadosSessao[$inArquivo]["num_documento"] );
            $obTDATEmissaoDocumento->setDado( "num_emissao", $inNumEmissao );
            $obTDATEmissaoDocumento->setDado( "numcgm_usuario", Sessao::read('numCgm') );
            $obTDATEmissaoDocumento->setDado( "cod_tipo_documento", $arKey[9] );
            $obTDATEmissaoDocumento->setDado( "exercicio", $inExercicio );
            $obTDATEmissaoDocumento->setDado( "num_parcelamento", $arParcelamentos[$inX] );
            $obTDATEmissaoDocumento->inclusao();
        }

        $arDadosSessao[$inArquivo]["num_emissao"] = $inNumEmissao;
        Sessao::encerraExcecao();

        Sessao::write( 'dados_emissao', $arDadosSessao );

        if ($arKey[0] == "notificacaoCobrancaREFIS.odt") {
            $arKey[0] = "notificacaoAcordo.agt";
        }

        //verificando se inscricao tem cobranca para poder emitir relatorio que utiliza dados de cobrança!
        if ( ( $arKey[0] == "termoParcelamento.agt" ) || ( $arKey[0] == "termoAssuncao.agt" ) || ( $arKey[0] == "reqBenDev.agt" ) || ( $arKey[0] == "reqBenTerc.agt" ) || ( $arKey[0] == "termoConfissao.agt" ) ) {
            SistemaLegado::exibeAviso("Inscrição selecionada não possuí cobrança, alguns dados não serão emitidos!","n_emitir","erro");
        }

        if ( ( $arKey[0] == "termoParcelamentoDAUrbem.agt" ) || ( $arKey[0] == "termoConsolidacaoDAUrbem.agt" ) ) {
            //modelo generico de termo de parcelamento
            //modelo generico de termo de consolidacao
            $stFiltro = " WHERE ddp.num_parcelamento in ( ".$arKey[6]." ) ";
            $obTDATDividaAtiva = new TDATDividaAtiva;
            $obTDATDividaAtiva->recuperaConsultaNotificacaoAcordo( $rsDados, $stFiltro );

            if ($arKey[0] == "termoParcelamentoDAUrbem.agt") {
                $inDocumento = 5;
            } else {
                $inDocumento = 4;
            }

            $obTDATDividaAtiva->recuperaConsultaTermoInscricaoDividaGenericoConfiguracaoUsuario( $rsDadosConfiguracaoUsuario, $inDocumento );
            if ( $rsDadosConfiguracaoUsuario->getCampo("util_resp2_doc") ) {
                $resposaveis = 2;
            } else {
                $resposaveis = 1;
            }

            if ( $rsDadosConfiguracaoUsuario->getCampo("util_msg_doc") ) {
                $msg = 1;
            } else {
                $msg = 0;
            }
            if ($rsDados->getNumLinhas() > 0) {
                 $rsDados->ordena( "nr_acordo_administrativo" );
            }

        } elseif ($arKey[0] == "memorialCalculoDAUrbem.agt") {

            //modelo generico memorial calculo
            $stFiltro = " WHERE dpar.num_parcelamento in ( ".$arKey[6]." ) ";
            $obTDATDividaAtiva = new TDATDividaAtiva;
            $obTDATDividaAtiva->recuperaConsultaTermoInscricaoDividaGenericoValorOrigem( $rsDadosValorCreditos, $stFiltro );
            $obTDATDividaAtiva->recuperaConsultaTermoInscricaoDividaGenericoConfiguracaoUsuario( $rsDadosConfiguracaoUsuario, 3 );
            if ( $rsDadosConfiguracaoUsuario->getCampo("util_msg_doc") ) {
                $msg = 1;
            } else {
                $msg = 0;
            }

            $stDtOrigem = $rsDadosValorCreditos->getCampo( "dt_vencimento_origem" );
            $stDtAtual = $rsDadosValorCreditos->getCampo( "dt_notificacao" );
            $arDtOrigem = explode( "-", $stDtOrigem);
            $arDtAtual = explode( "/", $stDtAtual);
            $arValorOrigem = array();
            
            while ($arDtOrigem[0].$arDtOrigem[1].$arDtOrigem[2] < $arDtAtual[2].$arDtAtual[1].$arDtAtual[0]) {
                unset( $rsListaDA );

                if ($arDtOrigem[1] > 12) {
                    $arDtOrigem[1] = 01;
                    $arDtOrigem[0] = $arDtOrigem[0] + 1;
                }

                $inDiaInicial = $arDtOrigem[2];
                $inDiaSemana  = date("w", mktime(0, 0, 0, sprintf("%02d", $arDtOrigem[1]), sprintf("%02d", $arDtOrigem[2]), sprintf("%04d", $arDtOrigem[0])));
                $inNroDiasMes = date("t", mktime(0, 0, 0, sprintf("%02d", $arDtOrigem[1]), 01, sprintf("%04d", $arDtOrigem[0])));

                if ($inNroDiasMes <= $inDiaInicial) {
                    $inDiaSemana  = date("w", mktime(0, 0, 0, sprintf("%02d", $arDtOrigem[1]), sprintf("%02d", $inNroDiasMes), sprintf("%04d", $arDtOrigem[0])));
                    if ($inDiaSemana == 0) {
                        $dtDiaVencimento = $inNroDiasMes - 2;
                    } elseif ($inDiaSemana == 6) {
                        $dtDiaVencimento = $inNroDiasMes - 1;
                    } else {
                        $dtDiaVencimento = $inNroDiasMes;
                    }
                } elseif ( ($inNroDiasMes - 1) == $inDiaInicial ) {
                    $inDiaSemana  = date("w", mktime(0, 0, 0, sprintf("%02d", $arDtOrigem[1]), sprintf("%02d", $inNroDiasMes), sprintf("%04d", $arDtOrigem[0])));
                    if ($inDiaSemana == 0) {
                        $dtDiaVencimento = $inNroDiasMes + 1;
                    } elseif ($inDiaSemana == 6) {
                        $dtDiaVencimento = $inNroDiasMes - 1;
                    } else {
                        $dtDiaVencimento = $inNroDiasMes;
                    }

                } else {
                    $inDiaSemana  = date("w", mktime(0, 0, 0, sprintf("%02d", $arDtOrigem[1]), sprintf("%02d", $inDiaInicial), sprintf("%04d", $arDtOrigem[0])));
                    if ($inDiaSemana == 0) {
                        $dtDiaVencimento = $inDiaInicial + 1;
                    } elseif ($inDiaSemana == 6) {
                        $dtDiaVencimento = $inDiaInicial + 2;
                    } else {
                        $dtDiaVencimento = $inDiaInicial;
                    }

                }

                $boAvancaData = true;
                if ($inDiaInicial > 31) {
                    $inDiaInicial = 1;
                    $arDtOrigem[1] = $arDtOrigem[1] + 1;
                    $boAvancaData = false;
                }

                if ($dtDiaVencimento > 31) {
                    $dtDiaVencimento = 1;
                    $arDtOrigem[1] = $arDtOrigem[1] + 1;
                    $boAvancaData = false;
                }

                if ( checkdate($arDtOrigem[1],$dtDiaVencimento,$arDtOrigem[0]) != 1 ) {
                    if ($arDtOrigem[1] >= 12) {
                        $arDtOrigem[1] = 1;
                        $arDtOrigem[0]++;
                    } else {
                        $arDtOrigem[1]++;
                    }

                    $dtDiaVencimento = 1;
                }

                if ( $arDtOrigem[1] != $arDtAtual[1]) {
                    //incrementa para realizar o calculo certo dos juros
                    $stDataCalculojuros = $arDtOrigem[1]+1;
                }else{
                    $dtDiaVencimento = $arDtAtual[0];
                }

                $stNovaData = sprintf("%04d-%02d-%02d", $arDtOrigem[0], $arDtOrigem[1], $dtDiaVencimento );
                $stNovaDataBR = sprintf("%02d/%02d/%04d", $dtDiaVencimento, $arDtOrigem[1], $arDtOrigem[0] );
                                    
                $obTDATDividaAtiva->recuperaConsultaMemoriaCalculoGenerico( $rsListaDA, $rsDadosValorCreditos->getCampo( "cod_inscricao" ), $rsDadosValorCreditos->getCampo( "exercicio" ), $rsDadosValorCreditos->getCampo( "cod_modalidade" ), $rsDadosValorCreditos->getCampo( "num_parcelamento" ), $rsDadosValorCreditos->getCampo( "valor_origem" ), $rsDadosValorCreditos->getCampo( "dt_vencimento_origem" ), $stNovaData );

                if ( $boAvancaData )
                    $arDtOrigem[1]++;

                while ( !$rsListaDA->Eof() ) {
                    $arAcrescimos = explode( ";", $rsListaDA->getCampo( "acrescimos" ) );

                    $flCorrecao = 0.00;
                    $flJuros = 0.00;
                    $flMulta = 0.00;
                    for ( $inY=1; $inY<count($arAcrescimos); $inY+=3 ) {
                        //valor_total, valor_parcial1, cod_acrescimo1, cod_tipo1
                        switch ($arAcrescimos[$inY+2]) {
                            case 1: //correcao
                                $flCorrecao += $arAcrescimos[$inY];
                                break;

                            case 2: //juros
                                $flJuros += $arAcrescimos[$inY];
                                break;

                            case 3: //multa
                                $flMulta += $arAcrescimos[$inY];
                                break;
                        }
                    }

                    $flValorOrigem = $rsDadosValorCreditos->getCampo( "valor_origem" );
                    $arValorOrigem[] = array (
                        "dt_calc" => $stNovaDataBR,
                        "origem" => $rsDadosValorCreditos->getCampo("credito_origem"),
                        "valor_origem" => number_format( $flValorOrigem, 2, ',', '.' ),
                        "correcao" => number_format( $flCorrecao, 2, ',', '.' ),
                        "juros" => number_format( $flJuros, 2, ',', '.' ),
                        "multa" => number_format( $flMulta, 2, ',', '.' ),
                        "total_calc" => number_format( $flValorOrigem + $arAcrescimos[0], 2, ',', '.' )
                    );

                    $rsListaDA->proximo();
                }
            }

            $arValorOrigemDataAtual[] = array (
                "origem" => isset($arValorOrigem[count($arValorOrigem)-1]["imposto"]) ? $arValorOrigem[count($arValorOrigem)-1]["imposto"]: "" ,
                "valor_origem" => number_format( $flValorOrigem, 2, ',', '.' ),
                "correcao" => number_format( $flCorrecao, 2, ',', '.' ),
                "juros" => number_format( $flJuros, 2, ',', '.' ),
                "multa" => number_format( $flMulta, 2, ',', '.' ),
                "total_calc" => number_format( $flValorOrigem + $arAcrescimos[0], 2, ',', '.' )
            );

            $arValorCreditos = $rsDadosValorCreditos->getElementos();

            $flValorTotal = $flValorOrigem + $arAcrescimos[0];
            $arValorCreditos[0]["total_tributo"] = number_format( $flValorTotal, 2, ',', '.' );
            if ( $arValorCreditos[0]["total_reducao"] > 0.00 )
                $flValorTotal -= $arValorCreditos[0]["total_reducao"];

            $arValorCreditos[0]["total_reducao"] = number_format( $arValorCreditos[0]["total_reducao"], 2, ',', '.' );
            $arValorCreditos[0]["valor_total"] = number_format( $flValorTotal, 2, ',', '.' );
        }else
        if ( ( $arKey[0] == "termoInscricaoDAUrbem.agt" ) || ( $arKey[0] == "certidaoDAUrbem.agt" ) || ( $arKey[0] == "notificacaoDAUrbem.agt" ) ) {
            //modelo generico do termo de inscricao em divida ativa
            //modelo generico da notificacao de inscricao em divida ativa
            //modelo generico da certidao de divida ativa
            if ($arKey[0] == "notificacaoDAUrbem.agt") {
                $inDocumento = 6;
            }else
            if ($arKey[0] == "termoInscricaoDAUrbem.agt") {
                $inDocumento = 2;
            } else {
                $inDocumento = 1;
            }

            $obTDATDividaAtiva = new TDATDividaAtiva;
            $stFiltro = " AND parcelamento.num_parcelamento in ( ".$arKey[6]." ) ";
            $obTDATDividaAtiva->recuperaConsultaTermoInscricaoDividaGenericoAcrescimoFundamentacao( $rsDadosAcrescimoFundamentacao, $stFiltro );
            $stFiltro = " WHERE dpar.num_parcelamento in ( ".$arKey[6]." ) ";
            $obTDATDividaAtiva->recuperaConsultaTermoInscricaoDividaGenericoValorOrigem( $rsDadosValorCreditos, $stFiltro );
            $obTDATDividaAtiva->recuperaConsultaTermoInscricaoDividaGenericoConfiguracaoUsuario( $rsDadosConfiguracaoUsuario, $inDocumento );

            if ($arKey[0] == "termoInscricaoDAUrbem.agt") {
                if ( $rsDadosConfiguracaoUsuario->getCampo("util_incval_doc") ) {
                    $incidenciaval = 1;
                } else {
                    $incidenciaval = 0;
                }

                if ( $rsDadosConfiguracaoUsuario->getCampo("util_metcalc_doc") ) {
                    $metodologia = 1;
                } else {
                    $metodologia = 0;
                }
            } else {
                if ( $rsDadosConfiguracaoUsuario->getCampo("util_leida_doc") ) {
                    $leida = 1;
                } else {
                    $leida = 0;
                }

                if ( $rsDadosConfiguracaoUsuario->getCampo("util_incval_doc") ) {
                    $incidenciaval = 1;
                } else {
                    $incidenciaval = 0;
                }

                if ( $rsDadosConfiguracaoUsuario->getCampo("util_metcalc_doc") ) {
                    $metodologia = 1;
                } else {
                    $metodologia = 0;
                }
            }

            if ( $rsDadosConfiguracaoUsuario->getCampo("util_resp2_doc") ) {
                $resposaveis = 2;
            } else {
                $resposaveis = 1;
            }

            if ( $rsDadosConfiguracaoUsuario->getCampo("util_msg_doc") ) {
                $msg = 1;
            } else {
                $msg = 0;
            }
        }else
        if ($arKey[0] == "termCancInsc.agt") {
            $obTDATDividaAtiva = new TDATDividaAtiva;
            $obTDATDividaAtiva->RecuperaDadosCancelamentoDivida( $rsDados, $arKey[6] );
            $obTDATDividaAtiva->RecuperaMatriculaCGM( $rsDado2, Sessao::read('numCgm') );
        }else
        if ( ( $arKey[0] == "termoAssuncao.agt" ) || ( $arKey[0] == "reqBenDev.agt" ) || ( $arKey[0] == "reqBenTerc.agt" ) || ( $arKey[0] == "termoConfissao.agt" ) ) {
            $stFiltro = " WHERE ddp.num_parcelamento in ( ".$arKey[6]." ) ";
            $obTDATDividaAtiva = new TDATDividaAtiva;
            $obTDATDividaAtiva->recuperaConsultaTermoParcelamento( $rsDados, $stFiltro );
            $rsDados->ordena("imposto", "ASC", SORT_STRING );
            $obTDATDividaAtiva->RecuperaMatriculaCGM( $rsDado2, Sessao::read('numCgm') );

            $obTDATDividaAtiva->recuperaConsultaTermoConfissao( $rsDados3, $rsDados->getCampo("num_parcelamento") );

            $stPagar = SistemaLegado::extenso( $rsDados->getCampo("tpsd") );
            $rsDados->setCampo( "tpsd", "R$ ".number_format( $rsDados->getCampo("tpsd"), 2, ',', '.' )." (".$stPagar.")" );

            $stPagar = SistemaLegado::extenso( $rsDados->getCampo("total_pagar") );
            $rsDados->setCampo( "total_pagar", "R$ ".number_format( $rsDados->getCampo("total_pagar"), 2, ',', '.' )." (".$stPagar.")" );

            $stPagar = SistemaLegado::extenso( $rsDados->getCampo("valor_parcela") );
            $rsDados->setCampo( "valor_parcela", "R$ ". number_format( $rsDados->getCampo("valor_parcela"), 2, ',', '.' )." (".$stPagar.")" );

            $stPagar = SistemaLegado::extenso( $rsDados->getCampo("parcelas") );
            $arPagar = explode( " ", $stPagar );
            $rsDados->setCampo( "parcelas", $rsDados->getCampo("parcelas")." (".$arPagar[1].")" );

            $inX = 0;
            while ( !$rsDados->Eof() ) {
                if ( !$inX )
                    $stPagar = $rsDados->getCampo("imposto");
                else
                    $stPagar .= ", ".$rsDados->getCampo("imposto");

                $inX++;
                $rsDados->proximo();
            }

            $rsDados->setPrimeiroElemento();

            if ( $inX > 1 )
                $rsDados->setCampo("imposto", $stPagar);
        }else
        if ($arKey[0] == "termoParcelamento.agt") {
            $obTDATDividaParcela = new TDATDividaParcela;
            $obTDATDividaParcela->recuperaTodos( $rsDados3, " WHERE num_parcelamento in ( ".$arKey[6]." ) AND num_parcela > 1 ", " ORDER BY dt_vencimento_parcela " );

            $stFiltro = " WHERE ddp.num_parcelamento in ( ".$arKey[6]." ) ";
            $obTDATDividaAtiva = new TDATDividaAtiva;
            $obTDATDividaAtiva->recuperaConsultaTermoParcelamento( $rsDados, $stFiltro );
            $rsDados->ordena("imposto", "ASC", SORT_STRING );
            $obTDATDividaAtiva->RecuperaMatriculaCGM( $rsDado2, Sessao::read('numCgm') );

            $stPagar = SistemaLegado::extenso( $rsDados->getCampo("total_pagar") );
            $rsDados->setCampo( "total_pagar", "R$ ".number_format( $rsDados->getCampo("total_pagar"), 2, ',', '.' )." (".$stPagar.")" );

            $stPagar = SistemaLegado::extenso( $rsDados->getCampo("valor_parcela") );

            $rsDados->setCampo( "valor_parcela", "R$ ". number_format( $rsDados->getCampo("valor_parcela"), 2, ',', '.' )." (".$stPagar.")" );

            $stPagar = SistemaLegado::extenso( $rsDados->getCampo("parcelas_menos") );
            $arPagar = explode( " ", $stPagar );
            $rsDados->setCampo( "parcelas_menos", $rsDados->getCampo("parcelas_menos")." (".$arPagar[1].")" );

            $arDadosDinamicos = array();
            if ( $rsDados->getCampo("parcelas") > 1 ) {
                $arData = explode( "/", $rsDados3->getCampo("dt_vencimento_parcela") );
                $arDadosDinamicos[0] = "As ";
                $arDadosDinamicos[1] = $rsDados->getCampo("parcelas_menos");
                $arDadosDinamicos[2] = " prestações seguintes serão no valor de ";
                $arDadosDinamicos[3] = $rsDados->getCampo( "valor_parcela" );
                $arDadosDinamicos[4] = ", com vencimento todo dia ";
                $arDadosDinamicos[5] = $arData[0];
                $arDadosDinamicos[6] = ", às quais serão pagas na forma determinada por ato do Poder Executivo.";
            }

            $stPagar = SistemaLegado::extenso( $rsDados->getCampo("parcelas") );
            $arPagar = explode( " ", $stPagar );
            $rsDados->setCampo( "parcelas", $rsDados->getCampo("parcelas")." (".$arPagar[1].")" );

            $inX = 0;
            while ( !$rsDados->Eof() ) {
                if ( !$inX )
                    $stPagar = $rsDados->getCampo("imposto");
                else
                    $stPagar .= ", ".$rsDados->getCampo("imposto");

                $inX++;
                $rsDados->proximo();
            }

            $rsDados->setPrimeiroElemento();

            if ( $inX > 1 )
                $rsDados->setCampo("imposto", $stPagar);

        }else
        if ($arKey[0] == "envelope.agt") { //era o dado que vem do campo agt
            //esta consulta serve para envelope notificação
            $stFiltro = " WHERE ddp.num_parcelamento in ( ".$arKey[6]." ) ";
            $obTDATDividaAtiva = new TDATDividaAtiva;
            $obTDATDividaAtiva->recuperaConsultaEnvelopeNotificacao( $rsDados, $stFiltro );
        }else
        if ($arKey[0] == "notificacaoAcordo.agt") { //era o dado que vem do campo agt
            //esta consulta serve para notificação do acordo
            $stFiltro = " WHERE ddp.num_parcelamento in ( ".$arKey[6]." ) ";
            $obTDATDividaAtiva = new TDATDividaAtiva;
            $obTDATDividaAtiva->recuperaConsultaNotificacaoAcordo( $rsDados, $stFiltro );
            if ($rsDados->getNumLinhas() > 0) {
                $rsDados->ordena( "nr_acordo_administrativo" );
            }
        }else
        if ($arKey[0] == "notificacaoDivida.agt") { //era o dado que vem do campo agt
            //esta consulta serve para a notificacao em divida
            $stFiltro = " WHERE ddp.num_parcelamento in ( ".$arKey[6]." ) ";
            $obTDATDividaAtiva = new TDATDividaAtiva;
            //$obTDATDividaAtiva->recuperaConsultaNotificacaoDivida( $rsDados, $stFiltro." ORDER BY dda.exercicio_original DESC " ); //sem detalhar a origem
            $obTDATDividaAtiva->recuperaConsultaNotificacaoDividaMata( $rsDados, $stFiltro." ORDER BY dda.exercicio_original DESC, origem.descricao_credito ASC" ); //detalha origem por credito
            $obTDATDividaAtiva->recuperaConsultaNotificacaoDividaNormas( $rsDados2, $arKey[6] );

            $arDados = $rsDados->arElementos;
            $arDadosTotal[0]["valor_original"] = 0.00;
            $arDadosTotal[0]["juros"] = 0.00;
            $arDadosTotal[0]["multa"] = 0.00;
            $arDadosTotal[0]["multa_infracao"] = 0.00;
            $arDadosTotal[0]["correcao"] = 0.00;
            $arDadosTotal[0]["valor_total"] = 0.00;
            for ( $inX=0; $inX<count($arDados); $inX++ ) {
                $arDados[$inX]["valor_total"] = $arDados[$inX]["juros"] + $arDados[$inX]["multa"] + $arDados[$inX]["multa_infracao"] + $arDados[$inX]["correcao"] + $arDados[$inX]["valor_origem"];
                $arDadosTotal[0]["valor_original"] += $arDados[$inX]["valor_origem"];
                $arDadosTotal[0]["juros"] += $arDados[$inX]["juros"];
                $arDadosTotal[0]["multa"] += $arDados[$inX]["multa"];
                $arDadosTotal[0]["multa_infracao"] += $arDados[$inX]["multa_infracao"];
                $arDadosTotal[0]["correcao"] += $arDados[$inX]["correcao"];
                $arDadosTotal[0]["valor_total"] += $arDados[$inX]["valor_total"];
            }
        }else
        if ($arKey[0] == "termoComposicaoDAMariana.odt") {
            $stFiltro = "WHERE ddp.num_parcelamento in ( ".$arKey[6]." ) ";
            $obTDATDividaAtiva = new TDATDividaAtiva;
            $obTDATDividaAtiva->recuperaConsultaNotificacaoDividaMata( $rsDados, $stFiltro." ORDER BY dda.exercicio_original DESC, origem.descricao_credito ASC ");
            //$obTDATDividaAtiva->recuperaConsultaNotificacaoDividaNormas( $rsDados2, $arKey[6] );
            $arDados = $rsDados->arElementos;
            $arArquivo = array();
            $arFooter = array();

            $arHeader[0]['contribuinte'] = $arDados[0]['nome_notificado'];
            $arHeader[0]['endereco'] = $arDados[0]['endereco_notificado'];
            $arHeader[0]['bairro'] = $arDados[0]['bairro_notificado'];
            $arHeader[0]['cep'] = $arDados[0]['cep_notificado'];
            $arHeader[0]['cid_est'] = $arDados[0]['cidade_estado_notificado'];
            $x=-1;
            
            for ( $i = 0; $i < count($arDados); $i++ ) {
                $parcial = 0;
                if ( ( $arDados[$i]['exercicio_origem'] == $arDados[$i-1]['exercicio_origem'] ) && ( $arDados[$i]['descricao_credito'] == $arDados[$i-1]['descricao_credito'] ) && ( $i > 0 ) ) {
                    $arArquivo[$x]['contribuinte'] = $arDados[$i]['nome_notificado'];
                    $arArquivo[$x]['endereco']     = $arDados[$i]['endereco_notificado'];
                    $arArquivo[$x]['exercicio']    = $arDados[$i]['exercicio_origem'];
                    $arArquivo[$x]['tributo']      = $arDados[$i]['descricao_credito'];
                    $arArquivo[$x]['valor_atual']  += number_format($arDados[$i]['valor_origem'] + $arDados[$i]['correcao'] + $arDados[$i]['multa_infracao'],2,',','.');
                    $arArquivo[$x]['multa']        += number_format($arDados[$i]['multa'],2,',','.');
                    $arArquivo[$x]['juros']        += number_format($arDados[$i]['juros'],2,',','.');
                    $arArquivo[$x]['total']        += number_format($arDados[$i]['multa'] + $arDados[$i]['juros'],2,',','.');
                    $arArquivo[$x]['desconto']     += number_format($arDados[$i]['reducao_juros'] + $arDados[$i]['reducao_multa'],2,',','.');
                    $parcial                       = (($arDados[$i]['multa'] + $arDados[$i]['juros'])-($arDados[$i]['reducao_juros'] + $arDados[$i]['reducao_multa']));
                    $arArquivo[$x]['parcial']      += number_format($parcial,2,',','.');
                    $arArquivo[$x]['geral']        = number_format(($arArquivo[$x]['valor_atual'] + $arArquivo[$x]['parcial']),2,',','.');
                } else {
                    $x++;
                    $arArquivo[$x]['contribuinte'] = $arDados[$i]['nome_notificado'];
                    $arArquivo[$x]['endereco']     = $arDados[$i]['endereco_notificado'];
                    $arArquivo[$x]['exercicio']    = $arDados[$i]['exercicio_origem'];
                    $arArquivo[$x]['tributo']      = $arDados[$i]['descricao_credito'];
                    $arArquivo[$x]['valor_atual']  = number_format($arDados[$i]['valor_origem'] + $arDados[$i]['correcao'] + $arDados[$i]['multa_infracao'],2,',','.');
                    $nuValorAtual                  = $arDados[$i]['valor_origem'] + $arDados[$i]['correcao'] + $arDados[$i]['multa_infracao'];
                    $arArquivo[$x]['multa']        = number_format($arDados[$i]['multa'],2,',','.');
                    $arArquivo[$x]['juros']        = number_format($arDados[$i]['juros'],2,',','.');
                    $arArquivo[$x]['total']        = number_format($arDados[$i]['multa'] + $arDados[$i]['juros'],2,',','.');
                    $arArquivo[$x]['desconto']     = number_format($arDados[$i]['reducao_juros'] + $arDados[$i]['reducao_multa'],2,',','.');
                    $parcial                       = ($arDados[$i]['multa'] + $arDados[$i]['juros'])-($arDados[$i]['reducao_juros'] + $arDados[$i]['reducao_multa']);
                    $arArquivo[$x]['parcial']      = number_format($parcial,2,',','.');
                    $arArquivo[$x]['geral']        = number_format($nuValorAtual + $parcial,2,',','.');
                    $arArquivo[$x]['geral_total']  = $nuValorAtual + $parcial;
                }
            }

            for ( $i=0; $i<=count($arArquivo); $i++ ) {
                $arFooter[0]['total_geral'] += $arArquivo[$i]['geral_total'];
            }
            $arFooter[0]['total_geral'] = number_format($arFooter[0]['total_geral'],2,',','.');

            $obTDATDividaParcela = new TDATDividaParcela;
            $obTDATDividaParcela->recuperaTodos( $rsDados3, " WHERE num_parcelamento in ( ".$arKey[6]." ) ORDER BY dt_vencimento_parcela " );

            $arParcela = $rsDados3->arElementos;
            $arArquivoParcela = array();
            $arArquivoParcela[0]['parcela_um'] = number_format(0.00,2,',','.');
            $arArquivoParcela[0]['vlr_parcela'] = number_format(0.00,2,',','.');
            for ( $x = 0; $x < count($arParcela); $x++ ) {
                if (( $arParcela[$x]['num_parcela'] == 1 ) || ( count($arParcela) == 1 )) {
                    $arArquivoParcela[0]['parcela_um'] = number_format($arParcela[$x]['vlr_parcela'],2,',','.');
                }
                $arArquivoParcela[0]['vlr_parcela'] = number_format($arParcela[$x]['vlr_parcela'],2,',','.');
            }
            $arArquivoParcela[0]['nro_parcela'] = count($arParcela);

            $arFooter[0]['saldo'] = number_format(bcsub($arFooter[0]['total_geral'],$arArquivoParcela[0]['parcela_um'], 2),2,',','.');

            $arFooter[0]['dia'] = date('d');
            switch (date('m')) {
                case 01: $mes = 'Janeiro'; break;
                case 02: $mes = 'Fevereiro'; break;
                case 03: $mes = 'Março'; break;
                case 04: $mes = 'Abril'; break;
                case 05: $mes = 'Maio'; break;
                case 06: $mes = 'Junho'; break;
                case 07: $mes = 'Julho'; break;
                case 08: $mes = 'Agosto'; break;
                case 09: $mes = 'Setembro'; break;
                case 10: $mes = 'Outubro'; break;
                case 11: $mes = 'Novembro'; break;
                case 12: $mes = 'Dezembro'; break;
            }
            $arFooter[0]['mes'] = $mes;
            $arFooter[0]['ano'] = date('Y');
        }else
        if ($arKey[0] == "termoInscricao.agt" || $arKey[0] == "certidaoDivida.agt") { //era o dado que vem do campo agt
            //esta consulta serve para o termo de inscricao da divida
            //esta consulta serva para certidao de divida ativa do municipio
            $stFiltro = " WHERE ddp.num_parcelamento in ( ".$arKey[6]." ) ";
            //$stFiltro = " WHERE ddp.num_parcelamento in ( 462972, 462973 ) ";
            $obTDATDividaAtiva = new TDATDividaAtiva;
            $obTDATDividaAtiva->recuperaConsultaTermoInscricaoDivida( $rsDados, $stFiltro );
            $rsDados->ordena( "exercicio_origem" ); //esta faltando o exercicio origem!!

            $arDados = $rsDados->arElementos;
            $arDadosTotal[0]["valor_original"] = 0.00;
            $arDadosTotal[0]["juros"] = 0.00;
            $arDadosTotal[0]["multa"] = 0.00;
            $arDadosTotal[0]["multa_infracao"] = 0.00;
            $arDadosTotal[0]["correcao"] = 0.00;
            $arDadosTotal[0]["valor_total"] = 0.00;
            $arDadosTotal[0]["referencia"] = "";
            for ( $inX=0; $inX<count($arDados); $inX++ ) {
                $arDados[$inX]["valor_total"] = $arDados[$inX]["juros"] + $arDados[$inX]["multa"] + $arDados[$inX]["multa_infracao"] + $arDados[$inX]["correcao"] + $arDados[$inX]["valor_origem"];
                $arDadosTotal[0]["valor_original"] += $arDados[$inX]["valor_origem"];
                $arDadosTotal[0]["juros"] += $arDados[$inX]["juros"];
                $arDadosTotal[0]["multa"] += $arDados[$inX]["multa"];
                $arDadosTotal[0]["multa_infracao"] += $arDados[$inX]["multa_infracao"];
                $arDadosTotal[0]["correcao"] += $arDados[$inX]["correcao"];
                $arDadosTotal[0]["valor_total"] += $arDados[$inX]["valor_total"];
                if ($arDadosTotal[0]["referencia"]) {
                    $arDadosTotal[0]["referencia"] .= ", ";
                }

                $arDadosTotal[0]["referencia"] .= $arDados[$inX]["imposto"];
            }

            $arDadosTotal[0]["valor_escrito"] = SistemaLegado::extenso( $arDadosTotal[0]["valor_total"] );

            if ($arDados[0]["tipo_inscricao"] == 'ie') {
                $arDadosTotal[0]["referencia"] .= " da Inscrição Econômica ";
            }else
                if ($arDados[0]["tipo_inscricao"] == 'im') {
                    $arDadosTotal[0]["referencia"] .= " da Inscrição Municipal ";
                } else {
                    $arDadosTotal[0]["referencia"] .= " do CGM ";
                }

            $arDadosTotal[0]["referencia"] .= $arDados[0]["inscricao"];
            $arDadosTotal[0]["procurador"] = $arDados[0]["procurador"];
            $arDadosTotal[0]["oab"] = $arDados[0]["oab"];
            $arDadosTotal[0]["contribuinte"] = $arDados[0]["contribuinte"];
            $arDadosTotal[0]["domicilio_fiscal"] = $arDados[0]["domicilio_fiscal"];

            $arDataInscricao = explode( "/", date("d/m/Y") );
            $arMes = array( "01" => "Janeiro", "02" => "Fevereiro", "03" => "Março", "04" => "Abril", "05" => "Maio", "06" => "Junho", "07" => "Julho", "08" => "Agosto", "09" => "Setembro", 10 => "Outubro", 11 => "Novembro", 12 => "Dezembro" );
            $arDadosTotal[0]["dt_inscricao"] = $arDataInscricao[0]." de ".$arMes[$arDataInscricao[1]]." de ".$arDataInscricao[2];
        }

        // instantiate a TBS OOo class
        $OOParser = new clsTinyButStrongOOo;

        // setting the object
        $OOParser->SetZipBinary('zip');
        $OOParser->SetUnzipBinary('unzip');
        $OOParser->SetProcessDir('/tmp');
        $OOParser->SetDataCharset('UTF8');

        $stDocumento = '/tmp/';
        $OOParser->_process_path = $stDocumento; //nome do arquivo pra salva

        // create a new openoffice document from the template with an unique id
        $OOParser->NewDocFromTpl( CAM_GT_DAT_MODELOS.$arKey[1] ); //arquivo do openof

        $OOParser->LoadXmlFromDoc('content.xml');

        if ( ( $arKey[0] == "termoParcelamentoDAUrbem.agt" ) || ( $arKey[0] == "termoConsolidacaoDAUrbem.agt" ) ) {
            //modelo generico de termo de parcelamento
            //modelo generico de termo de consolidacao
            $arTemp = $rsDados->arElementos;
            $arTemp2 = array(0 => isset($arTemp[0]["nr_acordo_administrativo"] )? $arTemp[0]["nr_acordo_administrativo"]: "");
            $inTot = 1;
            $flValorTotal = 0.00;
            $arTMPValores = array();
            for ( $inX=0; $inX<count($arTemp); $inX++) {
                $flValorTotal += $arTemp[$inX]["vlr_parcela"];
                $arTMPValores[$inX] = $arTemp[$inX];
                $arTMPValores[$inX]["vlr_parcela"] = number_format( $arTMPValores[$inX]["vlr_parcela"], 2, ',', '.' );
                $arTMPValores[$inX]["valor_corrigido"] = number_format( $arTMPValores[$inX]["valor_corrigido"], 2, ',', '.' );
                $arTMPValores[$inX]["valor_multa"] = number_format( $arTMPValores[$inX]["valor_multa"], 2, ',', '.' );
                $arTMPValores[$inX]["valor_correcao"] = number_format( $arTMPValores[$inX]["valor_correcao"], 2, ',', '.' );
                $arTMPValores[$inX]["valor_juros"] = number_format( $arTMPValores[$inX]["valor_juros"], 2, ',', '.' );
                $arTMPValores[$inX]["valor_reducao"] = number_format( $arTMPValores[$inX]["valor_reducao"], 2, ',', '.' );
                $arTMPValores[$inX]["valor_pago"] = number_format( $arTMPValores[$inX]["valor_pago"], 2, ',', '.' );
            }

            $arTemp3[0] = array( "vlr_total" => number_format( $flValorTotal, 2, ',', '.' ) );
            for ( $inX=1; $inX<count($arTemp); $inX++) {
                $boIncluir = true;
                for ($inY=0; $inY<$inTot; $inY++) {
                    if ($arTemp2[$inY] == $arTemp[$inX]["nr_acordo_administrativo"]) {
                        $boIncluir = false;
                        break;
                    }
                }

                if ($boIncluir) {
                    $arTemp2[$inTot] = $arTemp[$inX]["nr_acordo_administrativo"];
                    $inTot++;
                    $arTemp[0]["nr_acordo_administrativo"] .= ", ".$arTemp[$inX]["nr_acordo_administrativo"];
                }
            }

            $OOParser->MergeBlock( 'Dat', $arTemp );
            $OOParser->MergeBlock( 'Dat2', $arTMPValores );
            $OOParser->MergeBlock( 'Dat3', $arTemp3 );
            $OOParser->MergeBlock( 'Dat4', $rsDadosConfiguracaoUsuario->arElementos );

        } elseif ($arKey[0] == "memorialCalculoDAUrbem.agt") {

            $OOParser->MergeBlock( 'Dat', $rsDadosConfiguracaoUsuario->arElementos );
            $OOParser->MergeBlock( 'Dat2', $arValorCreditos );
            $OOParser->MergeBlock( 'Dat3', $arValorOrigem );
            $OOParser->MergeBlock( 'Dat4', $arValorOrigemDataAtual );

        } elseif ( ( $arKey[0] == "termoInscricaoDAUrbem.agt" ) || ( $arKey[0] == "certidaoDAUrbem.agt" ) || ( $arKey[0] == "notificacaoDAUrbem.agt" ) ) {
            //modelo generico do termo de inscricao em divida ativa
            //modelo generico da certidao de divida ativa
            $arValorCreditos = $rsDadosValorCreditos->arElementos;
            $arValorCreditosNaoTributario = array();
            $arValorCreditosTributario = array();
            $flValorTotal = 0.00;
            $flValorTotalNT = 0.00; //nao tributario
            $flValorTotalT = 0.00; //tributario

            for ( $inX=0; $inX<count( $arValorCreditos ); $inX++ ) {
                $arAcrescimos = explode( ";", $arValorCreditos[$inX]["acrescimos_j"] );
                $arAcrescimos_m = explode( ";", $arValorCreditos[$inX]["acrescimos_m"] );
                $arAcrescimos_c = explode( ";", $arValorCreditos[$inX]["acrescimos_c"] );
                for ( $inJ=1; $inJ<count($arAcrescimos_m); $inJ++ )
                    $arAcrescimos[] = $arAcrescimos_m[$inJ];

                for ( $inJ=1; $inJ<count($arAcrescimos_c); $inJ++ )
                    $arAcrescimos[] = $arAcrescimos_c[$inJ];

                $flMulta = 0.00;
                $flJuros = 0.00;
                $flCorrecao = 0.00;

                $flMultaNT = 0.00;
                $flJurosNT = 0.00;
                $flCorrecaoNT = 0.00;

                $flMultaT = 0.00;
                $flJurosT = 0.00;
                $flCorrecaoT = 0.00;

                for ( $inY=1; $inY<count($arAcrescimos); $inY+=3 ) {
                    //valor_total, valor_parcial1, cod_acrescimo1, cod_tipo1
                    switch ($arAcrescimos[$inY+2]) {
                        case 1: //correcao
                            $flCorrecao += $arAcrescimos[$inY];
                            break;

                        case 2: //juros
                            $flJuros += $arAcrescimos[$inY];
                            break;

                        case 3: //multa
                            $flMulta += $arAcrescimos[$inY];
                            break;
                    }

                    if ($arValorCreditos[$inX]["cod_natureza"] == 1) { //tributario
                        switch ($arAcrescimos[$inY+2]) {
                            case 1: //correcao
                                $flCorrecaoT += $arAcrescimos[$inY];
                                break;

                            case 2: //juros
                                $flJurosT += $arAcrescimos[$inY];
                                break;

                            case 3: //multa
                                $flMultaT += $arAcrescimos[$inY];
                                break;
                        }
                    } else { //nao tributario
                        switch ($arAcrescimos[$inY+2]) {
                            case 1: //correcao
                                $flCorrecaoNT += $arAcrescimos[$inY];
                                break;

                            case 2: //juros
                                $flJurosNT += $arAcrescimos[$inY];
                                break;

                            case 3: //multa
                                $flMultaNT += $arAcrescimos[$inY];
                                break;
                        }
                    }
                }

                if ($arValorCreditos[$inX]["cod_natureza"] == 1) { //tributario
                    $arValorCreditosTributario[] = array (
                        "credito_origem" => $arValorCreditos[$inX]["credito_origem"],
                        "valor_origem" => number_format( $arValorCreditos[$inX]["valor_origem"], 2, ',', '.' ),
                        "correcao" => number_format( $flCorrecaoT, 2, ',', '.' ),
                        "juros" => number_format( $flJurosT, 2, ',', '.' ),
                        "multa" => number_format( $flMultaT, 2, ',', '.' ),
                        "total_credito" => number_format( $arValorCreditos[$inX]["valor_origem"] + $flJurosT + $flMultaT + $flCorrecaoT, 2, ',', '.' )
                    );

                    $flValorTotalT += $arValorCreditos[$inX]["valor_origem"] + $flJurosT + $flMultaT + $flCorrecaoT;
                } else {
                    $arValorCreditosNaoTributario[] = array (
                        "credito_origem" => $arValorCreditos[$inX]["credito_origem"],
                        "valor_origem" => number_format( $arValorCreditos[$inX]["valor_origem"], 2, ',', '.' ),
                        "correcao" => number_format( $flCorrecaoNT, 2, ',', '.' ),
                        "juros" => number_format( $flJurosNT, 2, ',', '.' ),
                        "multa" => number_format( $flMultaNT, 2, ',', '.' ),
                        "total_credito" => number_format( $arValorCreditos[$inX]["valor_origem"] + $flJurosNT + $flMultaNT + $flCorrecaoNT, 2, ',', '.' )
                    );

                    $flValorTotalNT += $arValorCreditos[$inX]["valor_origem"] + $flJurosNT + $flMultaNT + $flCorrecaoNT;
                }

                if ( $flValorTotalT )
                    $tributario = 1;
                else
                    $tributario = 0;

                if ( $flValorTotalNT )
                    $ntributario = 1;
                else
                    $ntributario = 0;

                if ( $tributario && $ntributario )
                    $ttributario = 1;
                else
                    if ( $ntributario )
                        $ttributario = 2;
                    else
                        $ttributario = 3;

                $flValorTotal += $arValorCreditos[$inX]["valor_origem"] + $flJurosNT + $flMultaNT + $flCorrecaoNT + $flJurosT + $flMultaT + $flCorrecaoT;
            }

            if ( $arValorCreditos[0]["total_reducao"] > 0.00 )
                $flValorTotal -= $arValorCreditos[0]["total_reducao"];

            $arValorCreditos[0]["total_reducao"] = number_format( $arValorCreditos[0]["total_reducao"], 2, ',', '.' );
            if ($flValorTotalT <= 0) {
                $arValorCreditosTributario[] = array (
                    "credito_origem" => "",
                    "valor_origem" => "",
                    "correcao" => "",
                    "juros" => "",
                    "multa" => "",
                    "total_credito" => ""
                );
            }

            if ($flValorTotalNT <= 0) {
                $arValorCreditosNaoTributario[] = array (
                    "credito_origem" => "",
                    "valor_origem" => "",
                    "correcao" => "",
                    "juros" => "",
                    "multa" => "",
                    "total_credito" => ""
                );
            }

            $arValorCreditos[0]["total_tributoT"] = number_format( $flValorTotalT, 2, ',', '.' );
            $arValorCreditos[0]["total_tributoNT"] = number_format( $flValorTotalNT, 2, ',', '.' );
            $arValorCreditos[0]["valor_total"] = number_format( $flValorTotal, 2, ',', '.' );

            $arValorCreditos[0]["num_documento"] = $arDadosSessao[$inArquivo]["num_documento"];
            $arValorCreditos[0]["exercicio"] = $inExercicio;

            $arDataInscricao = explode( "/", date("d/m/Y") );
            $arMes = array( "01" => "Janeiro", "02" => "Fevereiro", "03" => "Março", "04" => "Abril", "05" => "Maio", "06" => "Junho", "07" => "Julho", "08" => "Agosto", "09" => "Setembro", 10 => "Outubro", 11 => "Novembro", 12 => "Dezembro" );
            $arValorCreditos[0]["dt_inscricao_txt"] = $arDataInscricao[0]." de ".$arMes[$arDataInscricao[1]]." de ".$arDataInscricao[2];

            $OOParser->MergeBlock( 'Dat', $rsDadosConfiguracaoUsuario->arElementos );
            $OOParser->MergeBlock( 'Dat2', $arValorCreditos );
            $OOParser->MergeBlock( 'Dat3', $arValorCreditosNaoTributario );
            $OOParser->MergeBlock( 'Dat4', $arValorCreditosTributario );
            $OOParser->MergeBlock( 'Dat5', $rsDadosAcrescimoFundamentacao->arElementos );

        } elseif ($arKey[0] == "termCancInsc.agt") {

            $OOParser->MergeBlock( 'Dat1', $rsDados->arElementos);

            $arTemp = array();
            $arTemp[0]["cgm_testemunha"] = Sessao::read('numCgm')." - ".Sessao::read('nomCgm');
            $arTemp[0]["matricula"] = $rsDado2->getCampo("registro");
            $OOParser->MergeBlock( 'Dat2',   $arTemp );

        } elseif ( ( $arKey[0] == "termoAssuncao.agt" ) || ( $arKey[0] == "reqBenDev.agt" ) || ( $arKey[0] == "reqBenTerc.agt" ) || ( $arKey[0] == "termoConfissao.agt" ) ) {

            $OOParser->MergeBlock( 'Dat', $rsDados->arElementos );
            $OOParser->MergeBlock( 'Dat3', $rsDados3->arElementos );
            $arTemp = array();
            $arTemp[0]["cgm_testemunha"] = Sessao::read('numCgm')." - ".Sessao::read('nomCgm');

            $arDataInscricao = explode( "/", date("d/m/Y") );
            $arMes = array( "01" => "Janeiro", "02" => "Fevereiro", "03" => "Março", "04" => "Abril", "05" => "Maio", "06" => "Junho", "07" => "Julho", "08" => "Agosto", "09" => "Setembro", 10 => "Outubro", 11 => "Novembro", 12 => "Dezembro" );
            $arTemp[0]["data_atual"] = $arDataInscricao[0]." de ".$arMes[$arDataInscricao[1]]." de ".$arDataInscricao[2]." ".date("G:i");
            $arTemp[0]["matricula"] = $rsDado2->getCampo("registro");

            $OOParser->MergeBlock( 'Dat2',   $arTemp );

        } elseif ($arKey[0] == "termoParcelamento.agt") {

            //esta consulta serve para termo de parcelamento ( confissao )
            $arTemp = array();
            $arTemp[0]["cgm_testemunha"] = Sessao::read('numCgm')." - ".Sessao::read('nomCgm');

            $arDataInscricao = explode( "/", date("d/m/Y") );
            $arMes = array( "01" => "Janeiro", "02" => "Fevereiro", "03" => "Março", "04" => "Abril", "05" => "Maio", "06" => "Junho", "07" => "Julho", "08" => "Agosto", "09" => "Setembro", 10 => "Outubro", 11 => "Novembro", 12 => "Dezembro" );
            $arTemp[0]["data_atual"] = $arDataInscricao[0]." de ".$arMes[$arDataInscricao[1]]." de ".$arDataInscricao[2]." ".date("G:i");
            $arTemp[0]["matricula"] = $rsDado2->getCampo("registro");
            $arTemp[0]["din1"] = $arDadosDinamicos[0];
            $arTemp[0]["din2"] = $arDadosDinamicos[1];
            $arTemp[0]["din3"] = $arDadosDinamicos[2];
            $arTemp[0]["din4"] = $arDadosDinamicos[3];
            $arTemp[0]["din5"] = $arDadosDinamicos[4];
            $arTemp[0]["din6"] = $arDadosDinamicos[5];
            $arTemp[0]["din7"] = $arDadosDinamicos[6];
            $OOParser->MergeBlock( 'Dat',    $rsDados->arElementos );
            $OOParser->MergeBlock( 'Dat2',   $arTemp );
        } elseif ($arKey[0] == "envelope.agt") { //era o dado que vem do campo agt
            //esta consulta serve para envelope notificação
            $OOParser->MergeBlock( 'Dat',    $rsDados->arElementos );

        } elseif ($arKey[0] == "notificacaoAcordo.agt") { //era o dado que vem do campo agt
            //esta consulta serve para notificação do acordo
            $arTemp = $rsDados->arElementos;
            if (isset($arTemp[0])) {
                $arTemp2 = array(0 => $arTemp[0]["nr_acordo_administrativo"] );
            }
            $inTot = 1;
            $flValorTotal = 0.00;
            for ( $inX=0; $inX<count($arTemp); $inX++) {
                $flValorTotal += $arTemp[$inX]["vlr_parcela"];
                $arTemp[$inX]["vlr_parcela"] = number_format( $arTemp[$inX]["vlr_parcela"], 2, ',', '.' );
                if ($arTemp[$inX]["valor_pago"]) {
                    $arTemp[$inX]["valor_pago"] = number_format( $arTemp[$inX]["valor_pago"], 2, ',', '.' );
                }
            }
            $arTemp3[0] = array( "vlr_total" => number_format( $flValorTotal, 2, ',', '.' ) );

            for ( $inX=1; $inX<count($arTemp); $inX++) {
                $boIncluir = true;
                for ($inY=0; $inY<$inTot; $inY++) {
                    if ($arTemp2[$inY] == $arTemp[$inX]["nr_acordo_administrativo"]) {
                        $boIncluir = false;
                        break;
                    }
                }

                if ($boIncluir) {
                    $arTemp2[$inTot] = $arTemp[$inX]["nr_acordo_administrativo"];
                    $inTot++;
                    $arTemp[0]["nr_acordo_administrativo"] .= ", ".$arTemp[$inX]["nr_acordo_administrativo"];
                }
            }

            $OOParser->MergeBlock( 'Dat', $arTemp );
            $OOParser->MergeBlock( 'Dat2', $arTemp );
            $OOParser->MergeBlock( 'Dat3', $arTemp3 );

        } elseif ($arKey[0] == "notificacaoDivida.agt") { //era o dado que vem do campo agt

            # Aplica os valores do modelo de Documento (Notificação de Dívida)
            $arTMP1 = $rsDados->arElementos;

            # Verifica a primeira descrição.
            $stDescricaoCredito = $arDados[0]["descricao_credito"];
            $inExercicioOrigem  = $arDados[0]["exercicio_origem"];

            $arAuxDados = $arAuxDadosTotal = array();
            $i = 0;

            # Força o Agrupamento pelo tipo do Credito
            for ($inTMP=0; $inTMP < count($arDados); $inTMP++) {

                if (($arDados[$inTMP]["descricao_credito"] == $stDescricaoCredito) &&
                    ($arDados[$inTMP]["exercicio_origem"]  == $inExercicioOrigem)) {

                    $arAuxDados[$i]["descricao_credito"] = $stDescricaoCredito;
                    $arAuxDados[$i]["exercicio_origem"]  = $arDados[$inTMP]["exercicio_origem"];
                    $arAuxDados[$i]["juros"]             += $arDados[$inTMP]["juros"];
                    $arAuxDados[$i]["multa"]             += $arDados[$inTMP]["multa"];
                    $arAuxDados[$i]["multa_infracao"]    += $arDados[$inTMP]["multa_infracao"];
                    $arAuxDados[$i]["correcao"]          += $arDados[$inTMP]["correcao"];
                    $arAuxDados[$i]["valor_origem"]      += $arDados[$inTMP]["valor_origem"];
                    $arAuxDados[$i]["valor_total"]       += $arDados[$inTMP]["valor_total"];

                    $arAuxDados[$i]["dt_vencimento_origem"] = $arDados[$inTMP]['dt_vencimento_origem'];

                    # Monta o Array do Totalizador que irá ao documento.
                    $arAuxDadosTotal[0]["juros"]          += $arDados[$inTMP]["juros"];
                    $arAuxDadosTotal[0]["multa"]          += $arDados[$inTMP]["multa"];
                    $arAuxDadosTotal[0]["multa_infracao"] += $arDados[$inTMP]["multa_infracao"];
                    $arAuxDadosTotal[0]["correcao"]       += $arDados[$inTMP]["correcao"];
                    $arAuxDadosTotal[0]["valor_origem"]   += $arDados[$inTMP]["valor_origem"];
                    $arAuxDadosTotal[0]["valor_total"]    += $arDados[$inTMP]["valor_total"];
                } else {
                    $i++;
                    $stDescricaoCredito = $arDados[$inTMP]["descricao_credito"];
                    $inExercicioOrigem  = $arDados[$inTMP]["exercicio_origem"];
                    $inTMP--;
                }
            }

            # Formatação para valores monetários.
            for ( $inTMP=0; $inTMP<count($arAuxDados); $inTMP++ ) {
                $arAuxDados[$inTMP]["juros"]          = number_format($arAuxDados[$inTMP]["juros"], 2, ',', '.' );
                $arAuxDados[$inTMP]["multa"]          = number_format($arAuxDados[$inTMP]["multa"], 2, ',', '.' );
                $arAuxDados[$inTMP]["multa_infracao"] = number_format($arAuxDados[$inTMP]["multa_infracao"], 2, ',', '.' );
                $arAuxDados[$inTMP]["correcao"]       = number_format($arAuxDados[$inTMP]["correcao"], 2, ',', '.' );
                $arAuxDados[$inTMP]["valor_origem"]   = number_format($arAuxDados[$inTMP]["valor_origem"], 2, ',', '.' );
                $arAuxDados[$inTMP]["valor_total"]    = number_format($arAuxDados[$inTMP]["valor_total"], 2, ',', '.' );
            }

            $arAuxDadosTotal[0]["juros"]          = number_format($arAuxDadosTotal[0]["juros"], 2, ',', '.' );
            $arAuxDadosTotal[0]["multa"]          = number_format($arAuxDadosTotal[0]["multa"], 2, ',', '.' );
            $arAuxDadosTotal[0]["multa_infracao"] = number_format($arAuxDadosTotal[0]["multa_infracao"], 2, ',', '.' );
            $arAuxDadosTotal[0]["correcao"]       = number_format($arAuxDadosTotal[0]["correcao"], 2, ',', '.' );
            $arAuxDadosTotal[0]["valor_origem"]   = number_format($arAuxDadosTotal[0]["valor_origem"], 2, ',', '.' );
            $arAuxDadosTotal[0]["valor_total"]    = number_format($arAuxDadosTotal[0]["valor_total"], 2, ',', '.' );
            $arAuxDadosTotal[0]["valor_original"] = $arAuxDadosTotal[0]["valor_origem"];

            $arDados      = $arAuxDados;
            $arDadosTotal = $arAuxDadosTotal;

            $arTMP1[0]["emissao_exercicio"] = $arDadosSessao[$inArquivo]["num_documento"]."/".$inExercicio;

            $arDataInscricao = explode( "/", date("d/m/Y") );
            $arMes = array( "01" => "Janeiro", "02" => "Fevereiro", "03" => "Março", "04" => "Abril", "05" => "Maio", "06" => "Junho", "07" => "Julho", "08" => "Agosto", "09" => "Setembro", 10 => "Outubro", 11 => "Novembro", 12 => "Dezembro" );
            $arTMP1[0]["dt_inscricao"] = $arDataInscricao[0]." de ".$arMes[$arDataInscricao[1]]." de ".$arDataInscricao[2];

            $arTMP1[0]["cod_inscricao"] = isset($arTMP1[0]["cod_inscricao"]) ? $arTMP1[0]["cod_inscricao"] : "";
            $arTMP1[0]["exercicio"] = isset($arTMP1[0]["exercicio"]) ? $arTMP1[0]["exercicio"] : "";

            $OOParser->MergeBlock( 'Dat',    $arTMP1 );
            $OOParser->MergeBlock( 'Dat2',   $arDados );
            $OOParser->MergeBlock( 'Dat3',   $arDadosTotal );
            $OOParser->MergeBlock( 'Dat4',   $rsDados2->arElementos );

        } elseif ($arKey[0] == "termoInscricao.agt" || $arKey[0] == "certidaoDivida.agt") {            
            //esta consulta serve para o termo de inscricao da divida
            //esta consulta serva para certidao de divida ativa do municipio
            $arTMP1 = $rsDados->arElementos;

            if ($arTMP1[0]["tipo_inscricao"] != 'ie') {
                $arTMP1[0]["nomdom"] = "ENDEREÇO";
            } else {
                $arTMP1[0]["nomdom"] = "DOMICÍLIO FISCAL";
            }

            if ($arTMP1[0]["numero_quadra"] && $arTMP1[0]["numero_lote"]) {
                $arTMP1[0]["domicilio_fiscal"] = $arTMP1[0]["endereco"].", QUADRA: ".$arTMP1[0]["numero_quadra"].", LOTE: ".$arTMP1[0]["numero_lote"].", ".$arTMP1[0]["bairro"]." ".$arTMP1[0]["cep"];
            } else {
                $arTMP1[0]["domicilio_fiscal"] = $arTMP1[0]["endereco"]." ".($arTMP1[0]["bairro"])." ".$arTMP1[0]["cep"];
            }

            for ( $inTMP=0; $inTMP<count($arTMP1); $inTMP++ ) {
                $arTMP1[$inTMP]["juros"]            = number_format( $arTMP1[$inTMP]["juros"], 2, ',', '.' );
                $arTMP1[$inTMP]["multa"]            = number_format( $arTMP1[$inTMP]["multa"], 2, ',', '.' );
                $arTMP1[$inTMP]["multa_infracao"]   = number_format( $arTMP1[$inTMP]["multa_infracao"], 2, ',', '.' );
                $arTMP1[$inTMP]["correcao"]         = number_format( $arTMP1[$inTMP]["correcao"], 2, ',', '.' );
                $arTMP1[$inTMP]["valor_origem"]     = number_format( $arTMP1[$inTMP]["valor_origem"], 2, ',', '.' );
            }

            for ( $inTMP=0; $inTMP<count($arDados); $inTMP++ ) {
                $arDados[$inTMP]["juros"]           = number_format( $arDados[$inTMP]["juros"], 2, ',', '.' );
                $arDados[$inTMP]["multa"]           = number_format( $arDados[$inTMP]["multa"], 2, ',', '.' );
                $arDados[$inTMP]["multa_infracao"]  = number_format( $arDados[$inTMP]["multa_infracao"], 2, ',', '.' );
                $arDados[$inTMP]["correcao"]        = number_format( $arDados[$inTMP]["correcao"], 2, ',', '.' );
                $arDados[$inTMP]["valor_origem"]    = number_format( $arDados[$inTMP]["valor_origem"], 2, ',', '.' );
                $arDados[$inTMP]["valor_total"]     = number_format( $arDados[$inTMP]["valor_total"], 2, ',', '.' );
            }

            for ( $inTMP=0; $inTMP<count($arDadosTotal); $inTMP++ ) {
                $arDadosTotal[$inTMP]["juros"]          = number_format( $arDadosTotal[$inTMP]["juros"], 2, ',', '.' );
                $arDadosTotal[$inTMP]["multa"]          = number_format( $arDadosTotal[$inTMP]["multa"], 2, ',', '.' );
                $arDadosTotal[$inTMP]["multa_infracao"] = number_format( $arDadosTotal[$inTMP]["multa_infracao"], 2, ',', '.' );
                $arDadosTotal[$inTMP]["correcao"]       = number_format( $arDadosTotal[$inTMP]["correcao"], 2, ',', '.' );
                $arDadosTotal[$inTMP]["valor_original"] = number_format( $arDadosTotal[$inTMP]["valor_original"], 2, ',', '.' );
                $arDadosTotal[$inTMP]["valor_total"]    = number_format( $arDadosTotal[$inTMP]["valor_total"], 2, ',', '.' );
            }

            $arTmp2 = $arTMP1;

            $arTMP1[0]["num_documento"] = $arDadosSessao[$inArquivo]["num_documento"];
            $arTMP1[0]["exercicio"] = $inExercicio;

            $OOParser->MergeBlock( 'Dat',    $arTMP1 );
            $OOParser->MergeBlock( 'Dat2',   $arTmp2 );
            $OOParser->MergeBlock( 'Dat3',   $arDados );
            $OOParser->MergeBlock( 'Dat4',   $arDadosTotal );

        } elseif ($arKey[0] == "termoComposicaoDAMariana.odt") {
            $OOParser->MergeBlock( 'Head', $arHeader );
            $OOParser->MergeBlock( 'Dat', $arArquivo );
            $OOParser->MergeBlock( 'Dat2', $arArquivoParcela );
            $OOParser->MergeBlock( 'Foot', $arFooter );
        }

        $OOParser->SaveXmlToDoc();

        $OOParser->LoadXmlFromDoc('styles.xml');
        $OOParser->SaveXmlToDoc();

        $arDadosArquivos[$inTotal]["nome_arquivo_tmp"] = $OOParser->GetPathnameDoc();
        $arDadosArquivos[$inTotal]["nome_arquivo"] = $arKey[2];
        $arDadosArquivos[$inTotal]["cod_arquivo"] = $inTotal;

        $arDadosArquivos[$inTotal]["numero_parcelamento"] = $arKey[4];
        $arDadosArquivos[$inTotal]["exercicio_cobranca"] = $arKey[5];
        $arDadosArquivos[$inTotal]["num_parcelamento"] = $arKey[6];
        $arDadosArquivos[$inTotal]["cod_documento"] = $arKey[7];
        $arDadosArquivos[$inTotal]["num_emissao"] = $arKey[8];
        $arDadosArquivos[$inTotal]["cod_tipo_documento"] = $arKey[9];
        $arDadosArquivos[$inTotal]["num_documento"] = $arKey[10];

        $inTotal++;
}

Sessao::write('dados', $arDadosArquivos);

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $_REQUEST['stAcao']  );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName   ( "stCtrl" );
$obHdnCtrl->setValue  ( $_REQUEST['stCtrl']  );

$obHdnDownLoad = new Hidden;
$obHdnDownLoad->setName  ( "HdnQual" );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction ( $pgList );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm   ( $obForm );
$obFormulario->setAjuda  ( "UC-05.04.03" );
$obFormulario->addHidden ( $obHdnDownLoad );
$obFormulario->addHidden ( $obHdnAcao );
$obFormulario->addHidden ( $obHdnCtrl );
$obFormulario->addTitulo ( "Documentos para Download" );

for ($inX=0; $inX<$inTotal; $inX++) {
    $stDownLoadName = "stArq".$inX;
    $stLblDownLoadName = "stLBArq".$inX;
    $stBtnDownLoadName = "stBtnArq".$inX;

    $obLabelDownLoad = new Label;
    $obLabelDownLoad->setValue  ( $arDadosArquivos[$inX]["nome_arquivo"] );
    $obLabelDownLoad->setName   ( $stLblDownLoadName );

    $obBtnDownLoad = new Button;
    $obBtnDownLoad->setName     ( $stBtnDownLoadName );
    $obBtnDownLoad->setValue    ( "Download" );
    $obBtnDownLoad->setTipo     ( "button" );
    $obBtnDownLoad->obEvento->setOnClick  ( "buscaValor('Download','".$inX."')" );
    $obBtnDownLoad->setDisabled ( false );

    $obFormulario->defineBarra ( array( $obLabelDownLoad, $obBtnDownLoad ), 'left', '' );
}

$obFormulario->show();
