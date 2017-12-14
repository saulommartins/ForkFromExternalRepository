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
    * Página de processamento oculto e geração do relatório para CONSULTA DE ARRECADACAO
    * Data de Criação   : 23/05/2006

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Diego Bueno Coelho

    * @ignore

    * $Id: OCGeraRelatorioConsultaArrecadacao.php 59612 2014-09-02 12:00:51Z gelson $
*/

/*
$Log$
Revision 1.31  2007/10/01 19:09:52  cercato
Ticket#10290#

Revision 1.30  2007/07/27 13:37:55  cercato
Bug#9772#

Revision 1.29  2007/03/21 21:37:20  dibueno
Bug #8851#

Revision 1.28  2007/02/09 09:24:00  dibueno
Melhorias da consulta da arrecadacao

Revision 1.27  2007/02/05 17:18:25  dibueno
Melhorias da consulta da arrecadacao

Revision 1.26  2007/01/25 16:33:53  dibueno
Alterações para buscar calculos creditos

Revision 1.25  2006/09/15 11:04:44  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php" );
include_once( CAM_FW_PDF."ListaPDF.class.php" );
include_once( CAM_GT_ARR_NEGOCIO."RARRParcela.class.php"                                                );
include_once( CAM_GT_ARR_NEGOCIO."RARRCarne.class.php"                                                );

$obRARRCarne = new RARRCarne;
$obRRelatorio = new RRelatorio;
$obPDF        = new ListaPDF("L");

$obRRelatorio->setExercicio  ( Sessao::getExercicio() );
$obRRelatorio->recuperaCabecalho( $arConfiguracao );
$obPDF->setModulo            ( "Relatório:" );
$obPDF->setTitulo               ( "Créditos:"  );
$obPDF->setSubTitulo         ( "Exercício - ".Sessao::getExercicio() );
$obPDF->setUsuario           ( Sessao::getUsername() );
$obPDF->setEnderecoPrefeitura( $arConfiguracao );

$arrInformacoes = array();
$arrInformacoes2 = array();

    $arrInformacoes['inCodLancamento']  = $_REQUEST['inCodLancamento'];
    $arrInformacoes['stProprietarios']  =   str_replace('<br>', ' ', $_REQUEST['stProprietarios']);
    $arProprietarios                    =   explode ('<br>', $_REQUEST['stProprietarios'] );
    $arrInformacoes['inCodGrupo']       = $_REQUEST['inCodGrupo'];
    $arrInformacoes['stOrigem']         = $_REQUEST['stOrigem'];
    $arrInformacoes['inInscricao']      = $_REQUEST['inInscricao'];
    $arrInformacoes['stDados']          = $_REQUEST['stDados'];
    $arrInformacoes['stSituacao']       = $_REQUEST['stSituacao'];
    $arrInformacoes['flValorVenal']     = $_REQUEST['flValorVenal'];
    $arrInformacoes['inCodModulo']      = $_REQUEST['inCodModulo'];
    $arrInformacoes['inExercicio']      = $_REQUEST['inExercicio'];

    // observação
    include_once(CAM_GT_ARR_MAPEAMENTO."TARRLancamento.class.php");
    $obTARRLancamento = new TARRLancamento;
    $stFiltro = " \n\t where cod_lancamento=".$arrInformacoes['inCodLancamento'];
    $obTARRLancamento->recuperaObservacaoLancamento( $rsObs, $stFiltro );
    $arrInformacoes['stObservacao'] = $rsObs->getCampo('observacao');
   //processo
    include_once(CAM_GT_ARR_MAPEAMENTO."TARRLancamento.class.php");
    $obTARRLancamento = new TARRLancamento;
    $stFiltro = " \n\t where cod_lancamento=".$arrInformacoes['inCodLancamento'];
    $obTARRLancamento->recuperaProcessoLancamento($rsPro, $stFiltro);
    if ($rsPro->getCampo('cod_processo') ) {
        $stProcessoA = $rsPro->getCampo('cod_processo')."/".$rsPro->getCampo('ano_exercicio');
        $stProcessoB = $rsPro->getCampo('resumo_assunto');
        $stProcessoC = $rsPro->getCampo('observacoes');
    }

    // DADOS
    $arLinhaDados = array();
    $inCountProprietarios = 0;
    while ( $inCountProprietarios < count($arProprietarios)) {
        if ($inCountProprietarios == 0) {
            $arLinhaTmp = array (
                "Label1" => "Contribuinte: ", "Dado1" => $arProprietarios[$inCountProprietarios]
            );
        } else {
            $arLinhaTmp = array (
                "Label1" => " ", "Dado1" => $arProprietarios[$inCountProprietarios]
            );
        }
        $inCountProprietarios++;
        $arLinhaDados[] = $arLinhaTmp;
    }

    if ($arrInformacoes['inInscricao']) {
        if ($arrInformacoes['inCodModulo'] == 12) {
            $arLinhaTmp = array (
                "Label1" => "Inscrição Imobiliária: ", "Dado1" => $arrInformacoes['inInscricao'].' '.$arrInformacoes['stDados']
            );
        } elseif ($arrInformacoes['inCodModulo'] == 14) {
            $arLinhaTmp = array (
                "Label1" => "Inscrição Econômica: ", "Dado1" => $arrInformacoes['inInscricao'].' '.$arrInformacoes['stDados']
            );
        } else {
            $arLinhaTmp = array (
                "Label1" => "Outros: ", "Dado1" => $arrInformacoes['inInscricao'].' '.$arrInformacoes['stDados']
            );
        }
        //$arLinhaDados[] = $arLinhaTmp;
    }
    if ($arrInformacoes['flValorVenal']) {
        $arLinhaTmp2 = array (
                "Label2" => "Valor Venal Total: ", "Dado2" => $arrInformacoes['flValorVenal'],
                "Label3" => "Situação do Imóvel: ", "Dado3" => $arrInformacoes['stSituacao']
        );
    } else {
            $arLinhaTmp2 = array ();
    }
    $arLinhaTmp = $arLinhaTmp + $arLinhaTmp2;
    $arLinhaDados[] = $arLinhaTmp;

    $rsDados = new RecordSet;
    $rsDados->preenche ( $arLinhaDados );
    $obPDF->addRecordSet($rsDados);

    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCabecalho   ( "DADOS DA EMISSÃO" ,15, 8, "B" );
    $obPDF->addCabecalho   ( "" ,65, 8, "B" );
    $obPDF->addCabecalho   ( "" ,10, 8, "B" );
    $obPDF->addCabecalho   ( "" ,15, 8, "B" );
    $obPDF->addCabecalho   ( "" ,10, 8, "B" );
    $obPDF->addCabecalho   ( "" ,5, 8, "B" );

    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCampo       ( "Label1" , 8 , "B" );
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo       ( "Dado1" , 8 );
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCampo       ( "Label2" , 8 , "B" );
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo       ( "Dado2" , 8 );
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCampo       ( "Label3" , 8 , "B" );
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo       ( "Dado3" , 8 );

    //NOVA LISTAGEM
    $arLinhaDados = array();

    $obRARRCarne->obRARRParcela = new RARRParcela( new RARRLancamento ( new RARRCalculo));
    $obRARRCarne->obRARRParcela->roRARRLancamento->setCodLancamento ( $arrInformacoes['inCodLancamento'] );

    if ($arrInformacoes['inCodGrupo']) {         // caso seja grupo de credito
        $obRARRCarne->obRARRParcela->roRARRLancamento->roRARRCalculo->obRARRGrupo->setCodGrupo($arrInformacoes['inCodGrupo']);
        $obRARRCarne->obRARRParcela->roRARRLancamento->roRARRCalculo->obRARRGrupo->setExercicio($arrInformacoes['inExercicio']);
    }

    $obRARRCarne->obRARRParcela->roRARRLancamento->listarCalculosCredito( $rsCreditos );

    $rsCreditos->setPrimeiroElemento();
    $somaLancamento = 0.00;

    $arCreditos = array();
    $contCreditos = 0;

    while ( !$rsCreditos->eof() ) {

        $arCreditos[$contCreditos]['cod_credito']       = $rsCreditos->getCampo('cod_credito');
        $arCreditos[$contCreditos]['cod_especie']       = $rsCreditos->getCampo('cod_especie');
        $arCreditos[$contCreditos]['cod_genero']        = $rsCreditos->getCampo('cod_genero');
        $arCreditos[$contCreditos]['cod_natureza']      = $rsCreditos->getCampo('cod_natureza');
        $arCreditos[$contCreditos]['descricao_credito'] = $rsCreditos->getCampo('descricao_credito');
        $arCreditos[$contCreditos]['valor']             = $rsCreditos->getCampo('valor');

        $somaLancamento += $rsCreditos->getCampo('valor');

        $contCreditos++;
        $rsCreditos->proximo();
    }

        $somaLancamento = number_format( $somaLancamento, 2 ,",",".");
        $somaLancamento = 'R$ '.$somaLancamento ;
        $rsCreditos->addFormatacao("valor","NUMERIC_BR");

    if ($arrInformacoes['inCodGrupo']) {
        $arLinhaTmp = array (
                "Label1" => "Grupo de Créditos: ",
                "Dado1" => $arrInformacoes['inCodGrupo']. ' - '. $arrInformacoes['stOrigem'],
                "Label2" => "Créditos", "Dado2" => "Valor",
                "Label3" => "Total", "Dado3" => $somaLancamento
        );

        $arLinhaDados[] = $arLinhaTmp;
    } else {
        $arTMP = explode( "<br>", $arrInformacoes['stOrigem'] );
        for ( $inX=0; $inX<count($arTMP); $inX++ ) {
            if ($arTMP[$inX]) {
                unset( $arLinhaTmp );
                if (!$inX) {
                    $arLinhaTmp = array (
                        "Label1" => "Crédito: ",
                        "Dado1" =>  $arTMP[$inX],
                        "Label2" => "Créditos", "Dado2" => "Valor",
                        "Label3" => "Total", "Dado3" => $somaLancamento
                    );
                } else {
                    $arLinhaTmp = array (
                        "Dado1" =>  $arTMP[$inX]
                    );
                }

                $arLinhaDados[] = $arLinhaTmp;
            }
        }
        //aqui colocar o explode com os dados da divida ativa
    }

    $contCreditos = 0;
    while ($contCreditos < 2) { //2 linhas no minimo, para Processo e Observacao

        if ($contCreditos == 0) {

            if ($stProcessoA) {
                $arLinhaTmp = array (
                    "Label1" => "Processo: ", "Dado1" => $stProcessoA
                );
            } else {
                $arLinhaTmp = array();
            }

        } elseif ($contCreditos == 1) {

            if ($arrInformacoes['stObservacao']) {
                //$arObs =
                $stObservacao = $arrInformacoes['stObservacao'];
                $caracterInicio = 0; $contArrays = 0;
                $tam = strlen ($stObservacao);
                $tamQuebra = 50;
                if ($tam > $tamQuebra) {
                    $arObs = array();
                    $contCaracter = 0;
                    $quebrou = false;
                    $finaliza = false;

                    while ($contCaracter < $tam && !$finaliza) {
                        $contTotal = 0;
                        $contCaracter = 0;
                        $ondeQuebra = $contCaracter + $tamQuebra;
                        while ($contCaracter < $ondeQuebra && !$finaliza) {
                            $quebrou = false;

                            if ( ($contCaracter >  $cont + $ondeQuebra - 15 ) && $stObservacao[$contCaracter] == ' ' && !$quebrou) {
                                $arObs[$contArrays] = substr ( $stObservacao, 0, $contCaracter );
                                $stObservacao = substr ( $stObservacao, $contCaracter, strlen ( $stObservacao ) -1 );
                                $quebrou = true;
                                $contArrays++;
                            } elseif ( $contCaracter == ($tamQuebra -1 ) && !$quebrou ) {
                                $arObs[$contArrays] = substr ( $stObservacao, 0, $contCaracter );
                                $stObservacao = substr ( $stObservacao, $contCaracter, strlen ( $stObservacao ) -1 );
                                $quebrou = true;
                                $finaliza = true;
                                $contArrays++;
                            }

                            if (!$quebrou) {
                                $contCaracter++;
                            } else {
                                break;
                            }

                        }

                        $caracterInicio = $contCaracter;
                        $contTotal++;
                    }

                } else {
                    $arObs[0] = $stObservacao;
                }

                $arLinhaTmp = array (
                    "Label1" => "Observação: ", "Dado1" => $arObs[0]
                );
            } else {
                $arLinhaTmp = array();
            }

        }
        if ($arCreditos[$contCreditos]['cod_credito']) {
            $stLinha  = $arCreditos[$contCreditos]['cod_credito'].'.'.$arCreditos[$contCreditos]['cod_especie'].'.';
            $stLinha .= $arCreditos[$contCreditos]['cod_genero'].'.'.$arCreditos[$contCreditos]['cod_natureza'].'-';
            $stLinha .= $arCreditos[$contCreditos]['descricao_credito'];
            $arLinhaTmp2 = array (
                "Label2" => $stLinha,
                "Dado2" => "R$ ".number_format( $arCreditos[$contCreditos]['valor'], 2 ,"," ,"." )
            );
        } else {
            $arLinhaTmp2 = array ();
        }

        $contCreditos++;
        $arLinhaTmp = $arLinhaTmp + $arLinhaTmp2;
        if ($arLinhaDados[$contCreditos]) {
            $arLinhaDados[$contCreditos] = $arLinhaDados[$contCreditos] + $arLinhaTmp;
        } else {
            $arLinhaDados[$contCreditos] = $arLinhaTmp;
        }
    }

    $contCreditos= 2;
    $contObs = 1;
    $numObs = count ( $arObs ) ;
    $numCreditos = $rsCreditos->getNumLinhas()+2;

    while ( ( $numObs > 0 && $contObs <= $numObs) || ($contCreditos <= $numCreditos ) ) {

        if ($arObs[$contObs] != '') {
            $arLinhaTmp = array (
                "Label1" => "",
                "Dado1" => $arObs[$contObs]
            );
        } else {
            $arLinhaTmp = array ();
        }

        if ($arCreditos[$contCreditos]['descricao_credito'] != '') {

            $stLinha  = $arCreditos[$contCreditos]['cod_credito'].'.'.$arCreditos[$contCreditos]['cod_especie'].'.';
            $stLinha .= $arCreditos[$contCreditos]['cod_genero'].'.'.$arCreditos[$contCreditos]['cod_natureza'].'-';
            $stLinha .= $arCreditos[$contCreditos]['descricao_credito'];
            $arLinhaTmp2 = array (
                "Label2" => $stLinha,
                "Dado2" => "R$ ".number_format($arCreditos[$contCreditos]['valor'] ,2 ,",",".")
            );
        } else {
            $arLinhaTmp2 = array ();
        }
        if ($arLinhaTmp || $arLinhaTmp2) {
            $arLinhaDados[] = $arLinhaTmp + $arLinhaTmp2;
        }
        $contCreditos++;
        $contObs++;
    }

//    $arLinhaDados = $arLinhaTmp + $arLinhaTmp2 + $arLinhaTmp3;
    $rsDados = new RecordSet;
    $rsDados->preenche ( $arLinhaDados );
    $obPDF->addRecordSet($rsDados);
    $obPDF->setQuebraPaginaLista( false );

    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCabecalho   ( "" ,15, 9, "B" );
    $obPDF->addCabecalho   ( "" ,30, 9, "B" );
    $obPDF->addCabecalho   ( "" ,25, 9, "B" );
    $obPDF->addCabecalho   ( "" ,10, 9, "B" );
    $obPDF->addCabecalho   ( "" ,10, 9, "B" );
    $obPDF->addCabecalho   ( "" ,10, 9, "B" );

    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCampo       ( "Label1" , 9 , "B" );
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo       ( "Dado1" , 9 );
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo       ( "Label2" , 9 );
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCampo       ( "Dado2" , 9 );
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCampo       ( "Label3" , 9 , "B" );
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo       ( "Dado3" , 9 );

    $obRARRCarne->obRARRParcela->listarConsulta( $rsListaParcelas );

    $rsListaParcelas->ordena ("nr_parcela");
    $rsListaParcelas->addFormatacao("valor","NUMERIC_BR");

    $arListaParcelasTudo = array();
    $contListaParcelas = 0;

    while ( !$rsListaParcelas->eof() ) {

        $obRARRCarne->setNumeracao                                      ( $rsListaParcelas->getCampo('numeracao'));
        $obRARRCarne->obRARRParcela->roRARRLancamento->setCodLancamento ( $rsListaParcelas->getCampo('cod_lancamento')  );
        $obRARRCarne->obRARRParcela->setCodParcela           ( $rsListaParcelas->getCampo('cod_parcela') );
        if ( $rsListaParcelas->getCampo('pagamento') ) {
            $dataUS = $rsListaParcelas->getCampo('pagamento');
        } else {
            $dataUS = date ('Y-m-d');
        }
        $obRARRCarne->listarConsulta ( $rsDetalheParcela, '', $dataUS, $rsListaParcelas->getCampo('vencimento') );

        // --------------- FILTROS
        $valorJuros =  $rsDetalheParcela->getCampo ( "parcela_juros" );
        $valorMulta = $rsDetalheParcela->getCampo ( "parcela_multa" );
        $valorDescontos = null;
        $valorDiferenca = null;
        $valorTotal = $rsDetalheParcela->getCampo ( "valor_total" );

        if ( $rsListaParcelas->getCampo ( "cod_motivo" ) ) {
            ;
        } elseif ( $rsListaParcelas->getCampo ( "situacao_resumida" ) == "Vencida") {
            $valorTotal = $rsDetalheParcela->getCampo ( "valor_total" );
        } elseif ( !$rsListaParcelas->getCampo ( "pagamento" ) ) { //N TA PAGA
            $valorDescontos = $rsDetalheParcela->getCampo ( "parcela_valor_desconto" );
            $valorJuros = null;
            $valorMulta = null;
            #$valorTotal = 0.00;
        } else {
            $valorDescontos = $rsDetalheParcela->getCampo ( "parcela_valor_desconto" );
            $valorDiferenca  = $rsDetalheParcela->getCampo ( "valor_total_pago" ) - $rsDetalheParcela->getCampo ( "parcela_juros" ) - $rsDetalheParcela->getCampo ( "parcela_multa" ) - $rsDetalheParcela->getCampo ( "valor" ) + $valorDescontos;
            $valorTotal         = $rsDetalheParcela->getCampo ( "valor_total" );
        }

        // --------------- FILTROS
        $arTmp  = array (
            "situacao_resumida"     => $rsListaParcelas->getCampo ( "situacao_resumida" ),
            "vencimento"            => $rsListaParcelas->getCampo ( "vencimento" ),
            "exercicio"             => $rsListaParcelas->getCampo ( "exercicio" ),
            "valor"                 => $rsListaParcelas->getCampo ( "valor" ),
            "numeracao"             => $rsListaParcelas->getCampo ( "numeracao" ),
            "info_parcela"          => $rsListaParcelas->getCampo ( "info_parcela" ),
            "nr_parcela"            => $rsListaParcelas->getCampo ( "nr_parcela" ),
            "cod_motivo"            => $rsListaParcelas->getCampo ( "cod_motivo" ),
            "valor_juros"           => number_format($valorJuros,     2 , "," , "."),
            "valor_multa"           => number_format($valorMulta,     2 , "," , "."),
            "valor_descontos"       => number_format($valorDescontos, 2 , "," , "."),
            "valor_total"           => number_format($valorTotal,     2 , "," , "."),
            "valor_diferenca"       => number_format($valorDiferenca, 2 , "," , "."),
            "data_pagamento"        => $rsDetalheParcela->getCampo ( "pagamento_data" ),
            "pagamento_num_banco"   => $rsDetalheParcela->getCampo("pagamento_num_banco"),
            "pagamento_nom_banco"   => $rsDetalheParcela->getCampo("pagamento_nom_banco"),
            "pagamento_num_agencia" => $rsDetalheParcela->getCampo("pagamento_num_agencia"),
            "pagamento_nom_agencia" => $rsDetalheParcela->getCampo("pagamento_nom_agencia")

        );
        $arListaParcelasTudo[] = $arTmp;
        $rsListaParcelas->proximo();
    }

     // e adiciona o $rsListaParcelas ao PDF

    $rsListaParcelasTudo = new RecordSet;
    $rsListaParcelasTudo->preenche ( $arListaParcelasTudo );
    $rsTemp = new RecordSet;
    $rsTemp->preenche ( $arListaParcelasTudo );
    $rsListaParcelasTudo->addFormatacao("valor_juros","NUMERIC_BR");
    $rsListaParcelasTudo->addFormatacao("valor_multa","NUMERIC_BR");
    $rsListaParcelasTudo->addFormatacao("valor_juros_pagar","NUMERIC_BR");
    $rsListaParcelasTudo->addFormatacao("valor_multa_pagar","NUMERIC_BR");
    $rsListaParcelasTudo->addFormatacao("valor_descontos","NUMERIC_BR");
    $rsListaParcelasTudo->addFormatacao("valor_total","NUMERIC_BR");
    $rsListaParcelasTudo->addFormatacao("valor_diferenca","NUMERIC_BR");

    $rsTemp->setPrimeiroElemento();
    $obPDF->addRecordSet( $rsTemp );

    $obPDF->setQuebraPaginaLista( false );

    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCabecalho   ( "Parcela" ,5, 9, "B" );
    $obPDF->addCabecalho   ( "Numeração" ,15, 9, "B" );
    $obPDF->addCabecalho   ( "Vencimento" , 8, 9, "B" );
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCabecalho   ( "Valor" ,8, 9, "B" );
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCabecalho   ( "Pagamento" ,8, 9, "B" );
    $obPDF->addCabecalho   ( "Banco" ,8, 9, "B" );
    $obPDF->addCabecalho   ( "Agência" ,8, 9, "B" );
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCabecalho   ( "Juros" ,8, 9, "B" );
    $obPDF->addCabecalho   ( "Multa" ,8, 9, "B" );
    $obPDF->addCabecalho   ( "Descontos" ,8, 9, "B" );
    $obPDF->addCabecalho   ( "Dif. Pagto" ,8, 9, "B" );
    $obPDF->addCabecalho   ( "Total" ,10, 9, "B" );

    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo       ( "info_parcela" ,9 );
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCampo       ( "numeracao" , 9 );
    $obPDF->addCampo       ( "vencimento" , 9 );
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCampo       ( "valor" , 9 );
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCampo       ( "data_pagamento" , 9 );
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCampo       ( "pagamento_num_banco" , 9 );
    $obPDF->addCampo       ( "pagamento_num_agencia" , 9 );
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCampo       ( "valor_juros" , 9 );
    $obPDF->addCampo       ( "valor_multa" , 9 );
    $obPDF->addCampo       ( "valor_descontos" , 9 );
    $obPDF->addCampo       ( "valor_diferenca" , 9 );
    $obPDF->addCampo       ( "valor_total" , 9 );

    //------------------------------------------------------------------ TOTAIS
    //GERACAO DOS DADOS
    $inContUnicas   = 0;
    $inContNormais = 0;
    $boFlagPrimeiraUnica = false;
    $boFlagTemUnicaPaga  = false;

    $somaValorUnicas   = 0.00; $somaValorJurosUnicas = 0.00; $somaValorMultaUnicas = 0.00;
    $somaValorDescontosUnicas = 0.00; $somaValorDiferencaUnicas = 0.00; $somaValorTotalUnicas = 0.00;

    $somaValorNormais   = 0.00; $somaValorJurosNormais = 0.00; $somaValorMultaNormais = 0.00;
    $somaValorDescontosNormais = 0.00; $somaValorDiferencaNormais = 0.00; $somaValorTotalNormais = 0.00;

    $rsListaParcelasTudo->setPrimeiroElemento();

    while ( !$rsListaParcelasTudo->eof() ) {

        if ( $rsListaParcelasTudo->getCampo('info_parcela') == 'Única' && !$boFlagPrimeiraUnica ) {

            $somaValorUnicas                    = str_replace ( ',', '.', str_replace ( '.', '', $rsListaParcelasTudo->getCampo('valor') ) );
            $somaValorJurosUnicas            = str_replace ( ',', '.', str_replace ( '.', '', $rsListaParcelasTudo->getCampo('valor_juros') ) );
            $somaValorMultaUnicas           = str_replace ( ',', '.', str_replace ( '.', '', $rsListaParcelasTudo->getCampo('valor_multa') ) );
            $somaValorDescontosUnicas   = str_replace ( ',', '.', str_replace ( '.', '', $rsListaParcelasTudo->getCampo('valor_descontos') ) );
            $somaValorDiferencaUnicas     = str_replace ( ',', '.', str_replace ( '.', '', $rsListaParcelasTudo->getCampo('valor_diferenca') ) );
            $somaValorTotalUnicas            = str_replace ( ',', '.', str_replace ( '.', '', $rsListaParcelasTudo->getCampo('valor_total') ) );
            $boFlagPrimeiraUnica = true;
            if ( $rsListaParcelasTudo->getCampo('data_pagamento') ) {
                $boFlagTemUnicaPaga = true;
            }
            $inContUnicas++;

        } else {
            if ( ($rsListaParcelasTudo->getCampo('info_parcela') != 'Única' && $boFlagPrimeiraUnica ) || !$boFlagPrimeiraUnica ) {
                $somaValorNormais                 +=  str_replace ( ',', '.', str_replace ( '.', '',  $rsListaParcelasTudo->getCampo('valor') ) );
                $somaValorJurosNormais         += str_replace ( ',', '.', str_replace ( '.', '', $rsListaParcelasTudo->getCampo('valor_juros') ) );
                $somaValorMultaNormais         += str_replace ( ',', '.', str_replace ( '.', '', $rsListaParcelasTudo->getCampo('valor_multa')) ) ;
                $somaValorDescontosNormais += str_replace ( ',', '.', str_replace ( '.', '', $rsListaParcelasTudo->getCampo('valor_descontos'))) ;
                $somaValorDiferencaNormais   += str_replace ( ',', '.', str_replace ( '.', '', $rsListaParcelasTudo->getCampo('valor_diferenca'))) ;
                $somaValorTotalNormais          += str_replace ( ',', '.', str_replace ( '.', '', $rsListaParcelasTudo->getCampo('valor_total')));
                $inContNormais++;
            }
        }

        $rsListaParcelasTudo->proximo();
    }
    $arTmp = array();
    //if ( $boFlagTemUnicaPaga && ( $inContUnicas > 0 && $inContNormais < 1 ) ) {
    if ($boFlagTemUnicaPaga) {

            $arTmp  = array (
            "situacao_resumida"     => "",
            "vencimento"            => "",
            "exercicio"             => "",
            "valor"                 => $somaValorUnicas,
            "numeracao"             => "",
            "info_parcela"          => "TOTAIS",
            "valor_juros_pagar"     => $somaValorJurosUnicas,
            "valor_multa_pagar"     => $somaValorMultaUnicas,
            "valor_descontos"       => $somaValorDescontosUnicas,
            "valor_diferenca"       => $somaValorDiferencaUnicas,
            "valor_total"           => $somaValorTotalUnicas,
            "data_pagamento"        => "",
            "num_banco"             => "",
            "nom_banco"             => "",
            "num_agencia"           => "",
            "nom_agencia"           => ""

            );

    } else {

        $arTmp  = array (
            "situacao_resumida"     => "",
            "vencimento"            => "",
            "exercicio"             => "",
            "valor"                 => $somaValorNormais>0?$somaValorNormais:$somaValorUnicas,
            "numeracao"             => "",
            "info_parcela"          => "TOTAL",
            "valor_juros_pagar"     => $somaValorJurosNormais>0?$somaValorJurosNormais:$somaValorJurosUnicas,
            "valor_multa_pagar"     => $somaValorMultaNormais>0?$somaValorMultaNormais:$somaValorMultaUnicas,
            "valor_descontos"       => $somaValorDescontosNormais>0?$somaValorDescontosNormais:$somaValorDescontosUnicas,
            "valor_diferenca"       => $somaValorDiferencaNormais>0?$somaValorDiferencaNormais:$somaValorDiferencaUnicas,
            "valor_total"           => $somaValorTotalNormais>0?$somaValorTotalNormais:$somaValorTotalUnicas,
            "data_pagamento"        => "",
            "num_banco"             => "",
            "nom_banco"             => "",
            "num_agencia"           => "",
            "nom_agencia"           => ""
        );
    }

    $arListaParcelasTotais = array();
    $arListaParcelasTotais[] = $arTmp;

    //EXIBICAO DO RECORDSET
    $rsTotais = new RecordSet;
    $rsTotais->preenche ( $arListaParcelasTotais );
    $rsTotais->addFormatacao("valor_diferenca"  , "NUMERIC_BR");
    $rsTotais->addFormatacao("valor_juros_pagar", "NUMERIC_BR");
    $rsTotais->addFormatacao("valor_multa_pagar", "NUMERIC_BR");
    $rsTotais->addFormatacao("valor_descontos"  , "NUMERIC_BR");
    $rsTotais->addFormatacao("valor_total"      , "NUMERIC_BR");
    $rsTotais->addFormatacao("valor"            , "NUMERIC_BR");

    $rsTotais->setPrimeiroElemento();
    $obPDF->addRecordSet($rsTotais);
    $obPDF->setQuebraPaginaLista( false );

    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCabecalho   ( "" , 5, 8, "B" );
    $obPDF->addCabecalho   ( "" ,15, 8, "B" );
    $obPDF->addCabecalho   ( "" , 8, 8, "B" );
    $obPDF->addCabecalho   ( "" , 8, 8, "B" );
    $obPDF->addCabecalho   ( "" , 8, 8, "B" );
    $obPDF->addCabecalho   ( "" , 8, 8, "B" );
    $obPDF->addCabecalho   ( "" , 8, 8, "B" );
    $obPDF->addCabecalho   ( "" , 8, 8, "B" );
    $obPDF->addCabecalho   ( "" , 8, 8, "B" );
    $obPDF->addCabecalho   ( "" , 8, 8, "B" );
    $obPDF->addCabecalho   ( "" , 8, 8, "B" );
    $obPDF->addCabecalho   ( "" ,10, 8, "B" );

    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo       ( "info_parcela" ,9, "B"  );
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCampo       ( "numeracao" , 9  );
    $obPDF->addCampo       ( "vencimento" , 9  );
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCampo       ( "valor" , 9, "B"  );
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCampo       ( "data_pagamento" , 9, "B"  );
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCampo       ( "num_banco" , 9 );
    $obPDF->addCampo       ( "num_agencia" , 9 );
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCampo       ( "valor_juros_pagar" , 9, "B"  );
    $obPDF->addCampo       ( "valor_multa_pagar" , 9, "B"  );
    $obPDF->addCampo       ( "valor_descontos" , 9, "B"  );
    $obPDF->addCampo       ( "valor_diferenca" , 9, "B"  );
    $obPDF->addCampo       ( "valor_total" , 9, "B"  );

$obPDF->show();
?>
