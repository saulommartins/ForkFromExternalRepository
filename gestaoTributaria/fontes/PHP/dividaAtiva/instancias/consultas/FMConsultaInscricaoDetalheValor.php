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
  * Página de Formulario de Detalhes da Parcela Selecionada
  * Usada sempre junto do OC
  * Data de criação : 29/12/2005

    * @author Analista: Fabio Bertoldi
    * @author Programador: Lucas Teixeira Stephanou

    * $Id: FMConsultaInscricaoDetalheValor.php 66056 2016-07-13 13:38:50Z evandro $

    Caso de uso: uc-05.04.09
**/

/*
$Log$
Revision 1.7  2007/09/05 15:57:45  cercato
adicionando acrescimos dinamicos.

Revision 1.6  2007/07/31 13:27:03  cercato
correcao na operacao de consulta.

Revision 1.5  2007/07/19 19:37:33  fabio
corrigido caso de uso p/ 05.04.09

Revision 1.4  2007/07/18 20:08:07  cercato
correcao na reducao da cobranca.

Revision 1.3  2007/07/13 14:13:20  cercato
correcao da consulta da divida para apresentar acrescimos.

Revision 1.2  2007/02/27 12:32:28  cercato
sql da consulta da divida

Revision 1.1  2007/02/26 12:59:16  cercato
*** empty log message ***

*/

include_once '../../../arrecadacao/classes/negocio/RARRCarne.class.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaAtiva.class.php" );

$inNumeracao = $request->get('numeracao');
if (empty($inNumeracao)) {    
    $stDescricao = $request->get('stDescricao');    
    $stDescricao = explode('§', $stDescricao);
    $inNumeracao = $stDescricao[0];
}

$dtVencimentoPR = $request->get('vencimento');
$inOcorrencia = $request->get('ocorrencia_pagamento');
$inCodLancamento = $request->get('cod_lancamento', $request->get('inCodLancamento') );
$inCodParcela = $request->get('cod_parcela');
$inExercicio = $request->get('inExercicio');
$dtDataBaseBR = $request->get('database_br');
$stInfoParcela = $request->get("info_parcela");
$inNumParcelamento = $request->get("num_parcelamento");

if ( !$stIdCarregamento )
    $stIdCarregamento = $request->get('linha_table_tree')."_sub_cell_2";

if ($dtDataBaseBR) {
    $arData = explode("/",$dtDataBaseBR);
    $dtDataUS = $arData[2]."-".$arData[1]."-".$arData[0];
    $dtDataBase = $dtDataBaseBR;
}

include_once ( CAM_GT_ARR_MAPEAMENTO."Ffn_situacao_carne.class.php"               );
$obSituacao = new Ffn_situacao_carne;
$stParam =  "'$inNumeracao','f'";
$obSituacao->executaFuncao($rsTmp,$stParam);
$stSituacao = $rsTmp->getCampo('valor');

$obRARRCarne = new RARRCarne;
$obRARRCarne->setNumeracao                                      ( $inNumeracao      );
$obRARRCarne->setOcorrenciaPagamento                            ( $inOcorrencia     );
$obRARRCarne->obRARRParcela->roRARRLancamento->setCodLancamento ( $inCodLancamento  );
$obRARRCarne->obRARRParcela->setCodParcela                      ( $inCodParcela     );
$obRARRCarne->setDataPagamento                                  ( $dtPagamento      );

$obTDATDividaAtiva = new TDATDividaAtiva;
$stFiltro = "";
// se a data estiver vazia
if ( !$dtDataUS)
    $dtDataUS = date("Y-m-d");

if ($inCodLancamento) {
    $stFiltro .= " al.cod_lancamento= ".$inCodLancamento. " AND ";
}

if ($inNumeracao) {
    $stFiltro .= " carne.numeracao= '".$inNumeracao."' AND ";
}

if ($inOcorrencia) {
    $stFiltro .= " apag.ocorrencia_pagamento = ".$inOcorrencia." AND ";
}

if ($inCodParcela) {
    $stFiltro .= " ap.cod_parcela= ".$inCodParcela." AND ";
}

if ($stFiltro) {
    $stFiltro = " ". substr ( $stFiltro, 0, strlen ( $stFiltro ) - 4 );
}

$obTDATDividaAtiva->recuperaConsulta( $rsDetalheParcela, $stFiltro, $dtDataUS );

$stFiltroDetalhes = " ap.cod_lancamento = ".$inCodLancamento." AND ap.cod_parcela = ".$inCodParcela." AND dpc.num_parcelamento = ".$inNumParcelamento;
$obTDATDividaAtiva->recuperaListaDetalhesAcrescimosConsultaDivida( $rsListaDetalheCreditos, $stFiltroDetalhes, $dtDataUS, $inNumeracao );

$flTotalCreditos = 0;
$inQuantidadeCreditos = 0;
$arCreditos = array();
while ( !$rsListaDetalheCreditos->Eof() ) {
    $boIncluir = true;
    for ($inX=0; $inX<$inQuantidadeCreditos; $inX++) {
        if ( $arCreditos[$inX] == $rsListaDetalheCreditos->getCampo( "credito_codigo_composto" ) ) {
            $boIncluir = false;
            break;
        }
    }

    if ($boIncluir) {
        $flTotalCreditos += $rsListaDetalheCreditos->getCampo("valor_credito");
        $arCreditos[$inQuantidadeCreditos] = $rsListaDetalheCreditos->getCampo( "credito_codigo_composto" );
        $inQuantidadeCreditos++;
    }

    $rsListaDetalheCreditos->proximo();
}

$rsListaDetalheCreditos->setPrimeiroElemento();
$rsDetalheParcela->setCampo( "parcela_desconto_pagar", $rsListaDetalheCreditos->getCampo("credito_descontos"));
$rsDetalheParcela->setCampo( "valor_total", $rsDetalheParcela->getCampo("pagamento_valor")?$rsDetalheParcela->getCampo("pagamento_valor"):($flTotalCreditos+$rsListaDetalheCreditos->getCampo("credito_juros_pagar")+$rsListaDetalheCreditos->getCampo("credito_multa_pagar")+$rsListaDetalheCreditos->getCampo("credito_correcao_pagar")) - $rsListaDetalheCreditos->getCampo("credito_descontos"));
$rsDetalheParcela->setCampo( "parcela_valor", $flTotalCreditos );
$rsDetalheParcela->setCampo( "parcela_juros_pagar", $rsListaDetalheCreditos->getCampo("credito_juros_pagar") );
$rsDetalheParcela->setCampo( "parcela_multa_pagar", $rsListaDetalheCreditos->getCampo("credito_multa_pagar") );
$rsDetalheParcela->setCampo( "parcela_correcao_pagar", $rsListaDetalheCreditos->getCampo("credito_correcao_pagar") );

$arDadosTMP2 = $rsDetalheParcela->getElementos();
$arDadosTMP = $rsListaDetalheCreditos->getElementos();
$inQuantidadeCreditos = 0;
$arCreditos = array();
$arDadosTMP2[0]["credito_utilizado"] = "";
for ( $inY=0; $inY<count( $arDadosTMP ); $inY++ ) {
    $flValorAcrescimo = $arDadosTMP[$inY]["valor_credito"];
    $flValorAcrescimoPercent = ($flValorAcrescimo * 100) / $flTotalCreditos;
    $boIncluir = true;
    for ($inX=0; $inX<$inQuantidadeCreditos; $inX++) {
        if ($arCreditos[$inX]["credito_codigo_composto"] == $arDadosTMP[$inY]["credito_codigo_composto"]) {
            $inValorExtra = 0.00;
            $arMultas = explode( ";", $arDadosTMP[$inY]["multa_sob_multa_pagar"] );
            $arJuros = explode( ";", $arDadosTMP[$inY]["juros_sob_juros_pagar"] );
            $arCorrecao = explode( ";", $arDadosTMP[$inY]["correcao_sob_correcao_pagar"] );

            $boIncluir = false;
            for ( $inA=2; $inA<count( $arMultas ); $inA+=3 ) {
                if ( ( $arMultas[$inA] == $arDadosTMP[$inY]["cod_acrescimo_individual"] ) && ( $arMultas[$inA+1] == $arDadosTMP[$inY]["tipo_acrescimo_individual"]) ) {
                    $boIncluir = true;
                    $inValorExtra = $arMultas[$inA-1];
                    break;
                }
            }

            if (!$boIncluir) {
                for ( $inA=2; $inA<count( $arJuros ); $inA+=3 ) {
                    if ( ( $arJuros[$inA] == $arDadosTMP[$inY]["cod_acrescimo_individual"] ) && ( $arJuros[$inA+1] == $arDadosTMP[$inY]["tipo_acrescimo_individual"]) ) {
                        $boIncluir = true;
                        $inValorExtra = $arJuros[$inA-1];
                        break;
                    }
                }
            }

            if (!$boIncluir) {
                for ( $inA=2; $inA<count( $arCorrecao ); $inA+=3 ) {
                    if ( ( $arCorrecao[$inA] == $arDadosTMP[$inY]["cod_acrescimo_individual"] ) && ( $arCorrecao[$inA+1] == $arDadosTMP[$inY]["tipo_acrescimo_individual"]) ) {
                        $boIncluir = true;
                        $inValorExtra = $arCorrecao[$inA-1];
                        break;
                    }
                }
            }

            $arCreditos[$inX]["lista_acrescimos"][ $arCreditos[$inX]["total_de_acrescimos"] ] = $arDadosTMP[$inY]["descricao_acrescimo_individual"];
            $arCreditos[$inX]["total_de_acrescimos"]++;

            if ($arDadosTMP2[0]["credito_utilizado"] == $arDadosTMP[$inY]["credito_codigo_composto"]) {
                $arDadosTMP2[0]["total_de_acrescimos"]++;
                $arDadosTMP2[0]["acrescimo_nome_".$arDadosTMP2[0]["total_de_acrescimos"] ] = $arDadosTMP[$inY]["descricao_acrescimo_individual"];
                $arDadosTMP2[0]["acrescimo_valor_".$arDadosTMP2[0]["total_de_acrescimos"] ] = $arDadosTMP[$inY]["valor_acrescimo_individual"]+$inValorExtra;
            }

            $arCreditos[$inX]["acrescimo_nome_".$arCreditos[$inX]["total_de_acrescimos"] ] = $arDadosTMP[$inY]["descricao_acrescimo_individual"];
            $arCreditos[$inX]["acrescimo_valor_".$arCreditos[$inX]["total_de_acrescimos"] ] = (($arDadosTMP[$inY]["valor_acrescimo_individual"]+$inValorExtra) * $flValorAcrescimoPercent) / 100;

            $arCreditos[$inX]["valor_total"] += $arCreditos[$inX][ "acrescimo_valor_".$arCreditos[$inX]["total_de_acrescimos"] ];
            if ( $arDadosTMP[$inY]["valor_total_pago"] )
                $arCreditos[$inX]["diferenca"] = round( $arCreditos[$inX]["valor_total_pago"] - $arCreditos[$inX]["valor_total"], 2);

            $boIncluir = false;
            break;
        }
    }

    if ($boIncluir) {
        //falta preenxer estes dados que serao dinamicos de acordo com o acrescimo q existir
        $arCreditos[$inQuantidadeCreditos]["lista_acrescimos"] = array(); //lista com nome e posicao dos acrescimos
        $arCreditos[$inQuantidadeCreditos]["total_de_acrescimos"] = 1;
        $arCreditos[$inQuantidadeCreditos]["lista_acrescimos"][0] = $arDadosTMP[$inY]["descricao_acrescimo_individual"];

        $inValorExtra = 0.00;
        $arMultas = explode( ";", $arDadosTMP[$inY]["multa_sob_multa_pagar"] );
        $arJuros = explode( ";", $arDadosTMP[$inY]["juros_sob_juros_pagar"] );
        $arCorrecao = explode( ";", $arDadosTMP[$inY]["correcao_sob_correcao_pagar"] );

        $boIncluir = false;
        for ( $inA=2; $inA<count( $arMultas ); $inA+=3 ) {
            if ( ( $arMultas[$inA] == $arDadosTMP[$inY]["cod_acrescimo_individual"] ) && ( $arMultas[$inA+1] == $arDadosTMP[$inY]["tipo_acrescimo_individual"]) ) {
                $boIncluir = true;
                $inValorExtra = $arMultas[$inA-1];
                break;
            }
        }

        if (!$boIncluir) {
            for ( $inA=2; $inA<count( $arJuros ); $inA+=3 ) {
                if ( ( $arJuros[$inA] == $arDadosTMP[$inY]["cod_acrescimo_individual"] ) && ( $arJuros[$inA+1] == $arDadosTMP[$inY]["tipo_acrescimo_individual"]) ) {
                    $boIncluir = true;
                    $inValorExtra = $arJuros[$inA-1];
                    break;
                }
            }
        }

        if (!$boIncluir) {
            for ( $inA=2; $inA<count( $arCorrecao ); $inA+=3 ) {
                if ( ( $arCorrecao[$inA] == $arDadosTMP[$inY]["cod_acrescimo_individual"] ) && ( $arCorrecao[$inA+1] == $arDadosTMP[$inY]["tipo_acrescimo_individual"]) ) {
                    $boIncluir = true;
                    $inValorExtra = $arCorrecao[$inA-1];
                    break;
                }
            }
        }

        if (!$arDadosTMP2[0]["credito_utilizado"]) {
            $arDadosTMP2[0]["credito_utilizado"] = $arDadosTMP[$inY]["credito_codigo_composto"];
            $arDadosTMP2[0]["acrescimo_nome_1"] = $arDadosTMP[$inY]["descricao_acrescimo_individual"];
            $arDadosTMP2[0]["acrescimo_valor_1"] = $arDadosTMP[$inY]["valor_acrescimo_individual"]+$inValorExtra;
            $arDadosTMP2[0]["total_de_acrescimos"] = 1;
        }

        $arCreditos[$inQuantidadeCreditos]["acrescimo_nome_1"] = $arDadosTMP[$inY]["descricao_acrescimo_individual"];
        $arCreditos[$inQuantidadeCreditos]["acrescimo_valor_1"] = (($arDadosTMP[$inY]["valor_acrescimo_individual"]+$inValorExtra) * $flValorAcrescimoPercent) / 100;

        $arCreditos[$inQuantidadeCreditos]["credito_descontos"] = ($arDadosTMP[$inY]["credito_descontos"] * $flValorAcrescimoPercent) / 100;
        $arCreditos[$inQuantidadeCreditos]["valor_total"] = ($arDadosTMP[$inY]["valor_credito"] + $arCreditos[$inQuantidadeCreditos]["acrescimo_valor_1"]) - $arCreditos[$inQuantidadeCreditos]["credito_descontos"];
        $arCreditos[$inQuantidadeCreditos]["valor_credito"] = $arDadosTMP[$inY]["valor_credito"];

        $arCreditos[$inQuantidadeCreditos]["credito_nome"] = $arDadosTMP[$inY]["credito_nome"];
        $arCreditos[$inQuantidadeCreditos]["credito_codigo_composto"] = $arDadosTMP[$inY]["credito_codigo_composto"];
        $arCreditos[$inQuantidadeCreditos]["valor_total_pago"] = $arDadosTMP[$inY]["valor_total_pago"];
        if ( $arDadosTMP[$inY]["valor_total_pago"] )
            $arCreditos[$inQuantidadeCreditos]["diferenca"] = round( $arCreditos[$inQuantidadeCreditos]["valor_total_pago"] - $arCreditos[$inQuantidadeCreditos]["valor_total"], 2);
        else
            $arCreditos[$inQuantidadeCreditos]["diferenca"] = 0.00;

        $inQuantidadeCreditos++;
    }

    $rsListaDetalheCreditos->proximo();
}

$rsDetalheParcela->preenche( $arDadosTMP2 );
$rsListaDetalheCreditos->preenche( $arCreditos );

$obSpnQuebra = new Span;
$obSpnQuebra->setId ("spnQuebra");

$obTxtDataBase = new Data;
$obTxtDataBase->setName     ( "dtDataBase"  );
$obTxtDataBase->setId       ( "dtDataBase"  );
$obTxtDataBase->setValue    ( $dtDataBase   );
$obTxtDataBase->setRotulo   ( "Data Base"   );
$obTxtDataBase->setStyle    ( "vertical-align:top;");
$obTxtDataBase->setTitle    ( "Data Base para os valores informados, altere para atualização dos valores!");

$obButtonAtualizarData = new Img;
$obButtonAtualizarData->setId    ( "imgAtualizar" );
$obButtonAtualizarData->setTitle ( "Atualizar" );
$obButtonAtualizarData->setNull  ( true );
$obButtonAtualizarData->setCaminho (CAM_FW_TEMAS."/imagens/btnRefresh.png");

$obButtonAtualizarData->obEvento->setOnClick("visualizarDetalhesAtualizaReemitida( '$inCodLancamento', '$inNumeracao', '$inExercicio', '$inCodParcela',document.getElementById('dtDataBase').value, document.getElementById('dtDataBase').value, '$dtVencimentoPR', '$inOcorrencia','$stIdCarregamento', '$stInfoParcela','$inNumParcelamento');");
$obButtonAtualizarData->obEvento->setOnDblClick("visualizarDetalhesAtualizaReemitida('$inCodLancamento', '$inNumeracao', '$inExercicio', '$inCodParcela',document.getElementById('dtDataBase').value, document.getElementById('dtDataBase').value, '$dtVencimentoPR', '$inOcorrencia','$stIdCarregamento', '$stInfoParcela','$inNumParcelamento');");

$obLblNumeracao = new Label;
$obLblNumeracao->setName        ( "stNumeracao" );
$obLblNumeracao->setValue       ( "<span id='ult_num'>".$rsDetalheParcela->getCampo("numeracao")."</span>/".$rsDetalheParcela->getCampo("exercicio"));
$obLblNumeracao->setRotulo      ( "Ultima Numeração"   );

$obRARRParcela = new RARRParcela ( new RARRLancamento (new RARRCalculo) );
$obRARRParcela->setCodParcela ( $inCodParcela );
$obRARRParcela->listarReemissaoConsulta( $rsNumeracoes );

$obCmbNumeracao = new Select;
$obCmbNumeracao->setName         ( "cmbNumeracao"               );
$obCmbNumeracao->addOption       ( "", "Vencimentos"            );
$obCmbNumeracao->setTitle        ( "Selecione Numerações Anteriores");
$obCmbNumeracao->setCampoId      ( "[numeracao]§[vencimento]§[data_pagamento]§[ocorrencia_pagamento]" );
$obCmbNumeracao->setCampoDesc    ( "[numeracao]" );
$obCmbNumeracao->preencheCombo   ( $rsNumeracoes                );
$obCmbNumeracao->setNull         ( true                         );
$obCmbNumeracao->setStyle        ( "width: 220px"               );
$obCmbNumeracao->obEvento->setOnChange ("visualizarDetalhesAtualizaReemitidaCombo( 'parcela', '$inCodLancamento', this.value, '$inExercicio', '$inCodParcela','', document.getElementById('dtDataBase').value, '$stIdCarregamento', '$stInfoParcela','$inNumParcelamento' );");

$obLblNumeracaoMigrada = new Label;
$obLblNumeracaoMigrada->setName  ( "stNumeracaoMigrada" );
$obLblNumeracaoMigrada->setValue ( $rsDetalheParcela->getCampo("migracao_numeracao")."/".$rsDetalheParcela->getCampo("migracao_prefixo"));
$obLblNumeracaoMigrada->setRotulo( "Numeração Migrada"   );

$obLblParcela = new Label;
$obLblParcela->setName      ( "stParcela" );
$obLblParcela->setValue     ( $rsDetalheParcela->getCampo("info_parcela") );
$obLblParcela->setRotulo    ( "Parcela"   );

$obLblValor = new Label;
$obLblValor->setName        ( "stValor" );
$obLblValor->setValue       ( "R$ ". $rsDetalheParcela->getCampo("parcela_valor") );
$obLblValor->setRotulo      ( "Valor"   );

$dtVencimento = $rsDetalheParcela->getCampo("parcela_vencimento_original");

$obLblVencimento = new Label;
$obLblVencimento->setName   ( "stVencimento" );
$obLblVencimento->setValue  ( $dtVencimento );
$obLblVencimento->setRotulo ( "Vencimento"   );

if ( $rsDetalheParcela->getCampo("numeracao_consolidacao") )
    $dtVencimento = $rsDetalheParcela->getCampo("vencimento");

$obLblVencimentoConsolidacao = new Label;
$obLblVencimentoConsolidacao->setName        ( "stVencimentoConsolidacao" );
$obLblVencimentoConsolidacao->setValue       ( $dtVencimento );
$obLblVencimentoConsolidacao->setRotulo      ( "Vencimento Consolidação"   );

if ( $rsDetalheParcela->getCampo("valor_consolidacao") )
    $valor_consolidacao = number_format ($rsDetalheParcela->getCampo('valor_consolidacao'), 2, ',', '.');

$obLblValorConsolidacao = new Label;
$obLblValorConsolidacao->setName        ( "stValorConsolidacao" 	);
$obLblValorConsolidacao->setValue       ( "R$ ".$valor_consolidacao );
$obLblValorConsolidacao->setRotulo      ( "Valor Consolidação"   	);

$obLblSituacao = new Label;
$obLblSituacao->setName     ( "stSituacao" );
$obLblSituacao->setValue    ( $rsDetalheParcela->getCampo("situacao") );
$obLblSituacao->setRotulo   ( "Situação"   );
// exclusivos para situacao = devolucao
$obLblDataDevolucao = new Label;
$obLblDataDevolucao->setName        ( "stDataDevolucao" );
$obLblDataDevolucao->setValue       ( $rsDetalheParcela->getCampo("devolucao_data") );
$obLblDataDevolucao->setRotulo      ( "Data de Devolução"   );

$obLblMotivo = new Label;
$obLblMotivo->setName        ( "stMotivo" );
$obLblMotivo->setValue       ( $rsDetalheParcela->getCampo("devolucao_descricao") );
$obLblMotivo->setRotulo      ( "Motivo"   );

$obLblDataPagamento = new Label;
$obLblDataPagamento->setName        ( "stDataPagamento" );
$obLblDataPagamento->setValue       ( $rsDetalheParcela->getCampo("pagamento_data") );
$obLblDataPagamento->setRotulo      ( "Data de Pagamento"   );

$obLblValorPagar = new Label;
$obLblValorPagar->setValue       ( "R$ ". $rsDetalheParcela->getCampo("parcela_valor") );
$obLblValorPagar->setRotulo      ( "Valor da Parcela"   );

$stPercentual = '';
if ( $rsDetalheParcela->getCampo("parcela_desconto_percentual") > 0 ) {
    $stPercentual = " (".$rsDetalheParcela->getCampo("parcela_desconto_percentual"). "%)";
}

$obLblDescontosPagar = new Label;
$obLblDescontosPagar->setName  ( "stDescontos" );
$obLblDescontosPagar->setValue  ( "R$ ".str_replace(".",",",$rsDetalheParcela->getCampo("parcela_valor_desconto")). $stPercentual );
$obLblDescontosPagar->setRotulo ( "Descontos"   );

$obLblNumeracaoConsolidacao = new Label;
$obLblNumeracaoConsolidacao->setName ('stNumeracaoConsolidacao');
$obLblNumeracaoConsolidacao->setValue ( $rsDetalheParcela->getCampo ('numeracao_consolidacao') );
$obLblNumeracaoConsolidacao->setRotulo ('Numeração Consolidação');

// labels exclusivas de pagamento
$obLblLote = new Label;
$obLblLote->setName     ( "stLote" );
$obLblLote->setValue    ( $rsDetalheParcela->getCampo("pagamento_cod_lote"));
$obLblLote->setRotulo   ( "Lote" );

$obLblDtLote = new Label;
$obLblDtLote->setName     ( "stDtLote" );
$obLblDtLote->setValue    ( $rsDetalheParcela->getCampo("pagamento_data_baixa"));
$obLblDtLote->setRotulo   ( "Data Processamento" );
$obLblDtLote->setTitle    ( "Data de Processamento do Lote" ) ;

$obLblProcesso = new Label;
$obLblProcesso->setName     ( "stProcesso" );
$obLblProcesso->setValue    ( $rsDetalheParcela->getCampo("processo") );
$obLblProcesso->setRotulo   ( "Processo" );

$obLblObservacao = new Label;
$obLblObservacao->setName     ( "stObservacao" );
$obLblObservacao->setValue    ( $rsDetalheParcela->getCampo("observacao") );
$obLblObservacao->setRotulo   ( "Observacao" );

$obLblBanco = new Label;
$obLblBanco->setName     ( "stBanco" );
$obLblBanco->setValue    ( $rsDetalheParcela->getCampo("pagamento_num_banco")." - ".$rsDetalheParcela->getCampo("pagamento_nom_banco") );

$obLblBanco->setRotulo   ( "Banco" );

$obLblAgencia = new Label;
$obLblAgencia->setName     ( "stAgencia" );
$obLblAgencia->setValue    ( $rsDetalheParcela->getCampo("pagamento_num_agencia")." - ".$rsDetalheParcela->getCampo("pagamento_nom_agencia") );
$obLblAgencia->setRotulo   ( "Agência" );

$obLblUsuario = new Label;
$obLblUsuario->setName     ( "stUsuario" );
$obLblUsuario->setValue    ( $rsDetalheParcela->getCampo("pagamento_numcgm")." - ".$rsDetalheParcela->getCampo("pagamento_nomcgm") );
$obLblUsuario->setRotulo   ( "Usuário" );

//guarda tipo baixa
$stTipoBaixa = $rsDetalheParcela->getCampo("cod_lote");

/** ********************* PAGAMENTOS DUPLICADOS *****************/

// antes de form , apresentar lista de pagamentos duplicados
$obRARRCarne->listarPagamentosConsulta( $rsPagDuplicados );
$rsPagDuplicados->addFormatacao("valor","NUMERIC_BR");

$inContPagamentos =  $rsPagDuplicados->getNumLinhas();
$arParcelasDuplicadas = array();
if ($inContPagamentos > 1) {
    //retira da lista de parcelas duplicadas, a parcela que está sendo exibida, indexada pelo "ocorrencia_pagamento"
    $arrParcelasDuplicadasTMP = $rsPagDuplicados->arElementos;

    $cont = $contParcelasOK = 0;
    while ($cont < $inContPagamentos) {
        if ( $arrParcelasDuplicadasTMP[$cont]['ocorrencia_pagamento'] != $rsDetalheParcela->getCampo('ocorrencia_pagamento') ) {
            $arParcelasDuplicadas[$contParcelasOK] = $arrParcelasDuplicadasTMP[$cont];
            $arParcelasDuplicadas[$contParcelasOK]['container'] = $stIdCarregamento;
            $contParcelasOK++;
        }
        $cont++;
    }

    $rsPagDuplicados = new RecordSet;
    $rsPagDuplicados->preenche ( $arParcelasDuplicadas );
} else {
    $rsPagDuplicados = new RecordSet;
}

if ( $rsPagDuplicados->getNumLinhas() > 0 ) {

    ########################### TABELA DOM
    $table = new Table();
    $table->setRecordset( $rsPagDuplicados );
    $table->setSummary('Pagamentos Duplicados');

    // lista zebrada
    //$table->setConditional( true , "#efefef" );

    $table->Head->addCabecalho( 'Numeração' , 20  );
    $table->Head->addCabecalho( 'Numeração Migrada' , 10  );
    $table->Head->addCabecalho( 'Ocorrência' , 10  );
    $table->Head->addCabecalho( 'Data Pagamento' , 10  );
    $table->Head->addCabecalho( 'Valor (R$)' , 10  );

    $table->Body->addCampo( '[numeracao]/[exercicio]' );
    $table->Body->addCampo( 'num_migrada'         , "D" );
    $table->Body->addCampo( 'ocorrencia_pagamento'      , "D" );
    $table->Body->addCampo( 'data_pagamento'   , "D" );
    $table->Body->addCampo( 'valor'   , "D" );

    #$table->Foot->addSoma ( 'valor_total', "D" );

    $table->Body->addAcao( 'consultar' ,  "visualizarDetalhesAtualizaReemitida ( %04d, %s, %04d, %04d, %s ,%s ,%s, %04d, %s, %s)" , array( 'cod_lancamento', 'numeracao', 'exercicio', 'cod_parcela', 'data_pagamento_us', "dtdatabase_br", 'vencimento', 'ocorrencia_pagamento', "container", 'info_parcela' ) );

    $table->montaHTML();

    echo $table->getHtml();
    #=======================================================================

}

$rsDetalheParcela->addFormatacao ("parcela_juros_pagar","NUMERIC_BR");
$rsDetalheParcela->addFormatacao ("parcela_multa_pagar","NUMERIC_BR");
$rsDetalheParcela->addFormatacao ("parcela_juros","NUMERIC_BR");
$rsDetalheParcela->addFormatacao ("parcela_multa","NUMERIC_BR");
$rsDetalheParcela->addFormatacao ("parcela_valor","NUMERIC_BR");
$rsDetalheParcela->addFormatacao ("parcela_valor_desconto","NUMERIC_BR");
$rsDetalheParcela->addFormatacao ("valor_total","NUMERIC_BR");
$rsDetalheParcela->addFormatacao ("parcela_correcao_pago","NUMERIC_BR");
$rsDetalheParcela->addFormatacao ("parcela_correcao_pagar","NUMERIC_BR");
$rsDetalheParcela->addFormatacao ("pagamento_diferenca","NUMERIC_BR");

$obLblValorDiferenca = new Label;
$obLblValorDiferenca->setName        ( "stDiff" );
$obLblValorDiferenca->setValue       ( "R$ ". $rsDetalheParcela->getCampo('pagamento_diferenca') );
$obLblValorDiferenca->setRotulo      ( "Diferença de Pagamento"   );

$obLblDescontoPagar = new Label;
$obLblDescontoPagar->setValue       ("R$ ". $rsDetalheParcela->getCampo("parcela_desconto_pagar") );
$obLblDescontoPagar->setRotulo      ( "Descontos");

$obLblTotalPago = new Label;
$obLblTotalPago->setName        ( "stValorTotalPago" );
$obLblTotalPago->setValue       ( "R$ ". $rsDetalheParcela->getCampo("valor_total") );
$obLblTotalPago->setRotulo      ( "Total Pago"   );

$obLblTotalPagar = new Label;
$obLblTotalPagar->setName        ( "stValorTotal" );
$obLblTotalPagar->setValue       ( "R$ ". $rsDetalheParcela->getCampo("valor_total") );
$obLblTotalPagar->setRotulo      ( "Total a Pagar"   );
/****************************************************************************/

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setName    ("frm_detalhes");
$obForm->setAction( $pgProc  );
$obForm->setTarget( "oculto" );

$obFormulario = new Formulario;
$obFormulario->addTitulo        ( "Detalhamento de Valores da parcela ".$request->get('info_parcela') );
$obFormulario->agrupaComponentes( array($obTxtDataBase,$obButtonAtualizarData) );
$obFormulario->addSpan          ( $obSpnQuebra              );
$obFormulario->agrupaComponentes (array( $obLblNumeracao,$obCmbNumeracao)           );

#if ( $rsDetalheParcela->getCampo("numeracao_migracao") ) {
if ( $rsDetalheParcela->getCampo("migracao_numeracao") ) {
    $obFormulario->addComponente( $obLblNumeracaoMigrada );
}

$obFormulario->addComponente    ( $obLblParcela             );

$obFormulario->addComponente    ( $obLblVencimento          );

#if ( $rsDetalheParcela->getCampo ('numeracao_consolidacao') ) {
if ( $rsDetalheParcela->getCampo ('consolidacao_numeracao') ) {
    $obFormulario->addComponente ( $obLblNumeracaoConsolidacao );
    $obFormulario->addComponente ( $obLblVencimentoConsolidacao );
    $obFormulario->addComponente ( $obLblValorConsolidacao );
}

$obFormulario->addComponente    ( $obLblSituacao            );
if ( $rsDetalheParcela->getCampo("situacao") == "Devolvido" ) {
    $obFormulario->addComponente    ( $obLblDataDevolucao   );
    $obFormulario->addComponente    ( $obLblMotivo          );
#} elseif ( $rsDetalheParcela->getCampo("data_pagamento") ) {
} elseif ( $rsDetalheParcela->getCampo("pagamento_data") ) {
    $obFormulario->addComponente    ( $obLblDataPagamento   );
    $obFormulario->addComponente    ( $obLblLote            );
    $obFormulario->addComponente    ( $obLblDtLote          );
    if ($stTipoBaixa != 'Baixa Manual') {
        $obFormulario->addComponente    ( $obLblBanco           );
        $obFormulario->addComponente    ( $obLblAgencia           );
    }
    if ( $rsDetalheParcela->getCampo("processo") ) {
        $obFormulario->addComponente    ( $obLblProcesso       );
    }
    if ( $rsDetalheParcela->getCampo("observacao") ) {
        $obFormulario->addComponente    ( $obLblObservacao    );
    }
    $obFormulario->addComponente    ( $obLblUsuario         );
    $obFormulario->addComponente    ( $obLblValor               );
    $obFormulario->addComponente    ( $obLblDescontoPagar      );

    for ($inA=1; $inA<=$rsDetalheParcela->getCampo("total_de_acrescimos"); $inA++ ) {
        $rsDetalheParcela->addFormatacao ("acrescimo_valor_".$inA, "NUMERIC_BR");
        $obLblAcrescimo = new Label;
        $obLblAcrescimo->setValue   ( "R$ ".$rsDetalheParcela->getCampo("acrescimo_valor_".$inA ) );
        $obLblAcrescimo->setRotulo  ( $rsDetalheParcela->getCampo( "acrescimo_nome_".$inA ) );

        $obFormulario->addComponente    ( $obLblAcrescimo );
    }

    if ( $rsDetalheParcela->getCampo('tp_pagamento') == 't' ) {
        $obFormulario->addComponente    ( $obLblValorDiferenca );
        $obFormulario->addComponente    ( $obLblTotalPago        );
    } else {
        $obFormulario->addComponente    ( $obLblTotalPagar       );
    }
} else { // parcela em aberto
    $obFormulario->addComponente    ( $obLblValorPagar       );
    $obFormulario->addComponente    ( $obLblDescontoPagar      );

    for ($inA=1; $inA<=$rsDetalheParcela->getCampo("total_de_acrescimos"); $inA++ ) {
        $rsDetalheParcela->addFormatacao ("acrescimo_valor_".$inA, "NUMERIC_BR");
        $obLblAcrescimo = new Label;
        $obLblAcrescimo->setValue   ( "R$ ".$rsDetalheParcela->getCampo("acrescimo_valor_".$inA ) );
        $obLblAcrescimo->setRotulo  ( $rsDetalheParcela->getCampo( "acrescimo_nome_".$inA ) );

        $obFormulario->addComponente    ( $obLblAcrescimo );
    }

    $obFormulario->addComponente    ( $obLblTotalPagar       );
}
$obFormulario->show();
//**********************************************************************************************

$rsListaDetalheCreditos->setPrimeiroElemento();
$rsListaDetalheCreditos->addFormatacao( "valor_credito"   , "NUMERIC_BR" );
$rsListaDetalheCreditos->addFormatacao( "credito_descontos"   , "NUMERIC_BR" );
$rsListaDetalheCreditos->addFormatacao( "valor_credito_juros_pago"   , "NUMERIC_BR" );
$rsListaDetalheCreditos->addFormatacao( "valor_credito_multa_pago"   , "NUMERIC_BR" );
$rsListaDetalheCreditos->addFormatacao( "credito_juros_pagar"   , "NUMERIC_BR" );
$rsListaDetalheCreditos->addFormatacao( "credito_multa_pagar"   , "NUMERIC_BR" );
$rsListaDetalheCreditos->addFormatacao( "credito_correcao_pagar", "NUMERIC_BR" );
$rsListaDetalheCreditos->addFormatacao( "credito_correcao_pago" , "NUMERIC_BR" );
$rsListaDetalheCreditos->addFormatacao( "diferenca"       , "NUMERIC_BR" );
$rsListaDetalheCreditos->addFormatacao( "valor_total"   , "NUMERIC_BR" );

for ( $inX=1; $inX<=$arCreditos[0]["total_de_acrescimos"]; $inX++ )
    $rsListaDetalheCreditos->addFormatacao( 'acrescimo_valor_'.$inX, "NUMERIC_BR" );

########################### TABELA DOM

$table = new Table();
$table->setRecordset( $rsListaDetalheCreditos );
$table->setSummary('Detalhamento por Crédito');

// lista zebrada
//$table->setConditional( true , "#efefef" );

$table->Head->addCabecalho( 'Crédito' , 20  );
$table->Head->addCabecalho( 'Valor' , 10  );
$table->Head->addCabecalho( 'Descontos' , 10  );

for ($inX=0; $inX<$arCreditos[0]["total_de_acrescimos"]; $inX++) {
    $table->Head->addCabecalho( $arCreditos[0]["lista_acrescimos"][$inX], 10  );
}

$table->Head->addCabecalho( 'Valor Diferença' , 10  );
$table->Head->addCabecalho( 'Valor Total (R$)' , 10  );

$table->Body->addCampo( '[credito_codigo_composto] - [credito_nome]' );
$table->Body->addCampo( 'valor_credito'         , "D" );
$table->Body->addCampo( 'credito_descontos'      , "D" );

for ($inX=1; $inX<=$arCreditos[0]["total_de_acrescimos"]; $inX++) {
    $table->Body->addCampo( 'acrescimo_valor_'.$inX, "D" );
}

$table->Body->addCampo( 'diferenca' , "D" );
$table->Body->addCampo( 'valor_total'   , "D" );

$table->Foot->addSoma ( 'valor_total', "D" );

#$table->Body->addAcao( null ,  null , array( 'nome' ) );

$table->montaHTML();

echo $table->getHtml();
#########################################
