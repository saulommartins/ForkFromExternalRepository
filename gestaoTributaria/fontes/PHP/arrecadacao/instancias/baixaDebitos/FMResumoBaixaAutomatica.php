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

    * Formulario de Consulta de Arrecadação
    * Data de Criação   : 22/12/2005

    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Lucas Teixeira Stephanou

    * @ignore

    * $Id: FMResumoBaixaAutomatica.php 64995 2016-04-18 18:40:22Z evandro $
    * Casos de uso: uc-05.03.19

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GT_ARR_NEGOCIO."RARRPagamento.class.php"                                             );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRPagamento.class.php" );

// passar do request pra variaveis
$inCodLote    = $_REQUEST["cod_lote"   ];
$inExercicio  = $_REQUEST["exercicio"  ];

$obRARRPagamento = new RARRPagamento;
$obRARRPagamento->inCodLote = $inCodLote;
$obRARRPagamento->stExercicio = $inExercicio;

$obRARRPagamento->consultaResumoLote($rsResumoLote);

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgProc  );
$obForm->setTarget( "oculto" );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnLote = new Hidden;
$obHdnLote->setName    ( "cod_lote"     );
$obHdnLote->setValue   ( $rsResumoLote->getCampo("cod_lote")   );

$obHdnExercicio = new Hidden;
$obHdnExercicio->setName    ( "exercicio" );
$obHdnExercicio->setValue   ( $rsResumoLote->getCampo("exercicio")  );

// COMPONENTES
$obLblNumero   = new Label;
$obLblNumero->setRotulo    ( "Número" );
$obLblNumero->setValue     ( $rsResumoLote->getCampo("cod_lote") );

$obLblNomeArquivo   = new Label;
$obLblNomeArquivo->setRotulo    ( "Nome do Arquivo" );
$obLblNomeArquivo->setValue     ( $rsResumoLote->getCampo("nom_arquivo") );

$obLblData   = new Label;
$obLblData->setRotulo    ( "Data" );
$obLblData->setValue     ( $rsResumoLote->getCampo("data_lote") );

$obLblDataBaixa   = new Label;
$obLblDataBaixa->setRotulo    ( "Data da Baixa" );
$obLblDataBaixa->setValue     ( $rsResumoLote->getCampo("data_baixa") );

$obLblBanco   = new Label;
$obLblBanco->setRotulo    ( "Banco" );
$obLblBanco->setValue     ( $rsResumoLote->getCampo("num_banco")." - ".$rsResumoLote->getCampo("nom_banco") );

$obLblAgencia   = new Label;
$obLblAgencia->setRotulo    ( "Agência"  );
$obLblAgencia->setValue     ( $rsResumoLote->getCampo("num_agencia")." - ".$rsResumoLote->getCampo("nom_agencia") );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm          ( $obForm                   );
$obFormulario->addHidden        ( $obHdnCtrl                );
$obFormulario->addHidden        ( $obHdnAcao                );

$obFormulario->addTitulo        ( "Resumo de Baixa Automática"      );
$obFormulario->addHidden        ( $obHdnLote                );
$obFormulario->addHidden        ( $obHdnExercicio           );
$obFormulario->addComponente    ( $obLblNumero              );
$obFormulario->addComponente    ( $obLblNomeArquivo         );
$obFormulario->addComponente    ( $obLblData                );
$obFormulario->addComponente    ( $obLblDataBaixa           );
$obFormulario->addComponente    ( $obLblBanco               );
$obFormulario->addComponente    ( $obLblAgencia             );

$obFormulario->show();

//$obRARRPagamento->listaResumoLoteBaixaAutomatica($rsListaCreditos);

$stFiltro = " WHERE lote.exercicio = '".$inExercicio."' AND lote.cod_lote = ".$inCodLote;
$stFiltro2 = $inCodLote;
$rsListaCreditos = new RecordSet;
$obTARRPagamento = new TARRPagamento;
$obTARRPagamento->recuperaResumoLoteListaOrigem( $rsListaOrigem, $stFiltro );

$arTMP_lo_2 = $rsListaOrigem->getElementos();
$arTMP_lo = array();
for ( $inX=0; $inX<count($arTMP_lo_2); $inX++ ) {

    if (preg_match( "/INCONSISTENCIA/", $arTMP_lo_2[$inX]['tipo_numeracao'] ) ) {

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

while ( !$rsPagamentosDA->Eof() ) {
    $rsPagamentosDA->setCampo( "diferenca", ( $rsPagamentosDA->getCampo( "valor_pago_normal" ) - ($rsPagamentosDA->getCampo( "valor_pago_calculo" ) + $rsPagamentosDA->getCampo( "juros" ) + $rsPagamentosDA->getCampo( "multa" ) ) ) );
    $rsPagamentosDA->proximo();
}

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
            $arCredOrig[$inY]["valor_pago_normal"] += $arTMP1[$inX]["valor_pago_normal"];
            $arCredOrig[$inY]["correcao"] += $arTMP1[$inX]["correcao"];
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

$arTmp = array();
while ( !$rsListaOrigem->eof() ) {
    $arSomatorios = null;
    $flOrigemValorNormalOK = $flOrigemValorJurosOK = $flOrigemValorMultaOK = 0.00;
    $flOrigemValorDiffOK = $flOrigemValorTotalOK = $flOrigemValorInconsistenteOK = 0.00;
    $flOrigemValorCorrecaoOK =  0.00;

    if ( $rsListaOrigem->getCampo('tipo') != "divida" ) {
        $stFiltro3 = " WHERE pagamento_lote.cod_lote IN ( ".$stFiltro2." ) \n";
        if ( $rsListaOrigem->getCampo('tipo') == 'grupo' ) {
            $stFiltro3 .= "\n AND acgc.cod_grupo = ".$rsListaOrigem->getCampo('origem');
            $stFiltro3 .= "\n and acgc.cod_grupo is not null ";

        } else {

            $arCredito = explode ('.', $rsListaOrigem->getCampo('origem') );
            $stFiltro3 .= "\n AND calculo.cod_credito = ".$arCredito[0];
            $stFiltro3 .= "\n AND calculo.cod_especie = ".$arCredito[1];
            $stFiltro3 .= "\n AND calculo.cod_genero = ".$arCredito[2];
            $stFiltro3 .= "\n AND calculo.cod_natureza = ".$arCredito[3];
            $stFiltro3 .= "\n AND acgc.cod_grupo is null ";
        }

        $stFiltro3 .= " AND pagamento.cod_convenio != -1 ";
        $stFiltro3 .= " AND pagamento_calculo.valor > 0 ";

        $obTARRPagamento->recuperaListaResumoBaixaAutomatica ( $rsListaCreditos, $stFiltro3, "", "", false );

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
                    $arTMP1[$inY]['correcao'] += $arTMP[$inX]['correcao'];
                    $arTMP1[$inY]['valor_pago_normal'] += $arTMP[$inX]['valor_pago_normal'];
                    $arTMP1[$inY]['diferenca'] += $arTMP[$inX]['diferenca'];

                    $boInserir = false;
                    break;
                }
            }

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

        while ( !$rsListaCreditos->eof() ) {
            $flOrigemValorNormalOK   = $rsListaCreditos->getCampo( 'valor_pago_calculo' );
            $flOrigemValorJurosOK    = $rsListaCreditos->getCampo( 'juros' );
            $flOrigemValorMultaOK    = $rsListaCreditos->getCampo( 'multa' );
            $flOrigemValorCorrecaoOK = $rsListaCreditos->getCampo( 'correcao' );
            $flOrigemValorDiffOK     = $rsListaCreditos->getCampo( 'diferenca' );
            $flOrigemValorTotalOK    = $rsListaCreditos->getCampo( 'valor_pago_normal' );

            if ($flOrigemValorNormalOK > 0) {
                $arTmp[] = array(
                    "cod" => $rsListaCreditos->getCampo('origem'),
                    "descricao_credito" => $rsListaCreditos->getCampo('descricao'),
                    "somatorio" => $flOrigemValorNormalOK
                );

                if ($flOrigemValorJurosOK > 0) {
                    $arTmp[] = array(
                        "cod" => $rsListaCreditos->getCampo('origem'),
                        "descricao_credito" => "Juros",
                        "somatorio" => $flOrigemValorJurosOK
                    );
                }

                if ($flOrigemValorMultaOK > 0) {
                    $arTmp[] = array(
                        "cod" => $rsListaCreditos->getCampo('origem'),
                        "descricao_credito" => "Multa",
                        "somatorio" => $flOrigemValorMultaOK
                    );
                }

                if ($flOrigemValorCorrecaoOK > 0) {
                    $arTmp[] = array(
                        "cod" => $rsListaCreditos->getCampo('origem'),
                        "descricao_credito" => "Correção",
                        "somatorio" => $flOrigemValorCorrecaoOK
                    );
                }

                if ( ($flOrigemValorDiffOK > 0) && ($flOrigemValorNormalOK != $flOrigemValorTotalOK)) {
                    $arTmp[] = array(
                        "cod" => $rsListaCreditos->getCampo('origem'),
                        "descricao_credito" => "Diferença",
                        "somatorio" => $flOrigemValorDiffOK
                    );
                }
            }

            $rsListaCreditos->proximo();
        }
    } else {
        $flOrigemValorNormalOK   = $rsListaOrigem->getCampo( 'valor_pago_calculo' );
        $flOrigemValorJurosOK    = $rsListaOrigem->getCampo( 'juros' );
        $flOrigemValorMultaOK    = $rsListaOrigem->getCampo( 'multa' );
        $flOrigemValorCorrecaoOK = $rsListaOrigem->getCampo( 'correcao' );
        $flOrigemValorDiffOK     = $rsListaOrigem->getCampo( 'diferenca' );
        $flOrigemValorTotalOK    = $rsListaOrigem->getCampo( 'valor_pago_normal' );

        if ($flOrigemValorNormalOK > 0) {
            $arTmp[] = array(
                "cod" => $rsListaOrigem->getCampo('origem'),
                "descricao_credito" => $rsListaOrigem->getCampo('descricao'),
                "somatorio" => $flOrigemValorNormalOK
            );

            if ($flOrigemValorJurosOK > 0) {
                $arTmp[] = array(
                    "cod" => $rsListaOrigem->getCampo('origem'),
                    "descricao_credito" => "Juros",
                    "somatorio" => $flOrigemValorJurosOK
                );
            }

            if ($flOrigemValorMultaOK > 0) {
                $arTmp[] = array(
                    "cod" => $rsListaOrigem->getCampo('origem'),
                    "descricao_credito" => "Multa",
                    "somatorio" => $flOrigemValorMultaOK
                );
            }

            if ($flOrigemValorCorrecaoOK > 0) {
                $arTmp[] = array(
                    "cod" => $rsListaOrigem->getCampo('origem'),
                    "descricao_credito" => "Correção",
                    "somatorio" => $flOrigemValorCorrecaoOK
                );
            }

            if ( ($flOrigemValorDiffOK > 0) && ($flOrigemValorNormalOK != $flOrigemValorTotalOK)) {
                $arTmp[] = array(
                    "cod" => $rsListaOrigem->getCampo('origem'),
                    "descricao_credito" => "Diferença",
                    "somatorio" => $flOrigemValorDiffOK
                );
            }
        }
    }

    $rsListaOrigem->proximo();
}

    $rsListaCreditos->preenche($arTmp);

$rsListaCreditos->addFormatacao( "somatorio", "NUMERIC_BR" );

$rsListaCreditos->setPrimeiroElemento();

$obListaCreditos = new Lista;
$obListaCreditos->setRecordSet          ( $rsListaCreditos              );
$obListaCreditos->setTitulo             ( "Valores Totais por Crédito"  );
$obListaCreditos->setMostraPaginacao    ( false                 );
$obListaCreditos->setTotaliza           ( "somatorio,Valor Total,right,4"  );
$obListaCreditos->addCabecalho();
$obListaCreditos->ultimoCabecalho->addConteudo("&nbsp;");
$obListaCreditos->ultimoCabecalho->setWidth( 5 );
$obListaCreditos->commitCabecalho();
$obListaCreditos->addCabecalho();
$obListaCreditos->ultimoCabecalho->addConteudo("Código");
$obListaCreditos->ultimoCabecalho->setWidth( 15 );
$obListaCreditos->commitCabecalho();
$obListaCreditos->addCabecalho();
$obListaCreditos->ultimoCabecalho->addConteudo("Crédito");
$obListaCreditos->ultimoCabecalho->setWidth( 55 );
$obListaCreditos->commitCabecalho();
$obListaCreditos->addCabecalho();
$obListaCreditos->ultimoCabecalho->addConteudo("Valor");
$obListaCreditos->ultimoCabecalho->setWidth( 15 );
$obListaCreditos->commitCabecalho();

$obListaCreditos->addDado();
$obListaCreditos->ultimoDado->setCampo          ( "cod" );
$obListaCreditos->ultimoDado->setAlinhamento    ( "CENTRO" );
$obListaCreditos->commitDado();
$obListaCreditos->addDado();
$obListaCreditos->ultimoDado->setCampo          ( "descricao_credito" );
$obListaCreditos->ultimoDado->setAlinhamento    ( "ESQUERDA" );
$obListaCreditos->commitDado();
$obListaCreditos->addDado();
$obListaCreditos->ultimoDado->setCampo          ( "somatorio" );
$obListaCreditos->ultimoDado->setAlinhamento    ( "DIREITA" );
$obListaCreditos->commitDado();

$obListaCreditos->show();

$obRARRPagamento->listaResumoLoteInconsistente($rsListaInconsistente);

$arTmp = array();
if ( $rsListaInconsistente->getNumLinhas() > 0 ) {
    foreach ($rsListaInconsistente->arElementos as $value) {
        $stValor = str_replace(",",".",$value['valor']);
        $value['valor'] = $stValor;
        $arTmp[] = $value;
    }
}
$rsListaInconsistente->preenche($arTmp);
$rsListaInconsistente->addFormatacao( "valor", "NUMERIC_BR" );

$rsListaInconsistente->setPrimeiroElemento();

$obListaInconsistente = new Lista;
$obListaInconsistente->setRecordSet          ( $rsListaInconsistente        );
$obListaInconsistente->setTitulo             ( "Dados Inconsistentes"       );
$obListaInconsistente->setMostraPaginacao    ( false                        );
$obListaInconsistente->setTotaliza           ( "valor,Valor Total,right,4"  );
$obListaInconsistente->addCabecalho();
$obListaInconsistente->ultimoCabecalho->addConteudo("&nbsp;");
$obListaInconsistente->ultimoCabecalho->setWidth( 5 );
$obListaInconsistente->commitCabecalho();
$obListaInconsistente->addCabecalho();
$obListaInconsistente->ultimoCabecalho->addConteudo("Númeração");
$obListaInconsistente->ultimoCabecalho->setWidth( 20 );
$obListaInconsistente->commitCabecalho();
$obListaInconsistente->addCabecalho();
$obListaInconsistente->ultimoCabecalho->addConteudo("Data de Pagamento");
$obListaInconsistente->ultimoCabecalho->setWidth( 50 );
$obListaInconsistente->commitCabecalho();
$obListaInconsistente->addCabecalho();
$obListaInconsistente->ultimoCabecalho->addConteudo("Valor");
$obListaInconsistente->ultimoCabecalho->setWidth( 15 );
$obListaInconsistente->commitCabecalho();

$obListaInconsistente->addDado();
$obListaInconsistente->ultimoDado->setCampo          ( "numeracao" );
$obListaInconsistente->ultimoDado->setAlinhamento    ( "CENTRO" );
$obListaInconsistente->commitDado();
$obListaInconsistente->addDado();
$obListaInconsistente->ultimoDado->setCampo          ( "data_pagamento" );
$obListaInconsistente->ultimoDado->setAlinhamento    ( "ESQUERDA" );
$obListaInconsistente->commitDado();
$obListaInconsistente->addDado();
$obListaInconsistente->ultimoDado->setCampo          ( "valor" );
$obListaInconsistente->ultimoDado->setAlinhamento    ( "DIREITA" );
$obListaInconsistente->commitDado();

$obListaInconsistente->show();

// total baixados

$rsListaCreditos->setPrimeiroElemento();
$nuTotalBaixados = 0.00;
foreach ($rsListaCreditos->getElementos() as $value) {
    $nuTotalBaixados += $value['somatorio'];
}
// total inconsistente
$rsListaInconsistente->setPrimeiroElemento();
$nuTotalInconsistente = 0.00;
if ( $rsListaInconsistente->getNumLinhas() > 0 ) {
    foreach ($rsListaInconsistente->getElementos() as $value) {
        $nuTotalInconsistente += $value['valor'];
    }
}
// total do lote
$nuTotalLote = $nuTotalInconsistente + $nuTotalBaixados;

$arLote[] = array( "tit" => "Valores Totais Baixados", "valor" => $nuTotalBaixados);
$arLote[] = array( "tit" => "Valores Totais Inconsistentes", "valor" => $nuTotalInconsistente);

$rsLote = new Recordset;
$rsLote->preenche($arLote);
$rsLote->addFormatacao( "valor", "NUMERIC_BR" );

$obListaLote = new Lista;
$obListaLote->setRecordSet          ( $rsLote                      );
$obListaLote->setTitulo             ( "Resumo do Lote"             );
$obListaLote->setMostraPaginacao    ( false                        );
$obListaLote->setTotaliza           ( "valor,Valor Total,right,3"  );
$obListaLote->addCabecalho();
$obListaLote->ultimoCabecalho->addConteudo("&nbsp;");
$obListaLote->ultimoCabecalho->setWidth( 5 );
$obListaLote->commitCabecalho();
$obListaLote->addCabecalho();
$obListaLote->ultimoCabecalho->addConteudo("Descrição");
$obListaLote->ultimoCabecalho->setWidth( 70 );
$obListaLote->commitCabecalho();
$obListaLote->addCabecalho();
$obListaLote->ultimoCabecalho->addConteudo("Valor");
$obListaLote->ultimoCabecalho->setWidth( 15 );
$obListaLote->commitCabecalho();

$obListaLote->addDado();
$obListaLote->ultimoDado->setCampo          ( "tit" );
$obListaLote->ultimoDado->setAlinhamento    ( "ESQUERDA" );
$obListaLote->commitDado();

$obListaLote->addDado();
$obListaLote->ultimoDado->setCampo          ( "valor" );
$obListaLote->ultimoDado->setAlinhamento    ( "DIREITA" );
$obListaLote->commitDado();

$obListaLote->show();

$rsITBI = new RecordSet;
$rsITBI->preenche ( Sessao::read( 'logItbiPago' ) );

$obListaItbi = new Lista;
$obListaItbi->setRecordSet          ( $rsITBI                      );
$obListaItbi->setTitulo             ( "Registros de Efetivação de Transferência de Propriedade");
$obListaItbi->setMostraPaginacao    ( false                        );
$obListaItbi->addCabecalho();
$obListaItbi->ultimoCabecalho->addConteudo("&nbsp;");
$obListaItbi->ultimoCabecalho->setWidth( 5 );
$obListaItbi->commitCabecalho();
$obListaItbi->addCabecalho();
$obListaItbi->ultimoCabecalho->addConteudo("Imóvel");
$obListaItbi->ultimoCabecalho->setWidth( 30 );
$obListaItbi->commitCabecalho();
$obListaItbi->addCabecalho();
$obListaItbi->ultimoCabecalho->addConteudo("Numeração");
$obListaItbi->ultimoCabecalho->setWidth( 30 );
$obListaItbi->commitCabecalho();
$obListaItbi->addCabecalho();
$obListaItbi->ultimoCabecalho->addConteudo("Estado");
$obListaItbi->ultimoCabecalho->setWidth( 15 );
$obListaItbi->commitCabecalho();

$obListaItbi->addDado();
$obListaItbi->ultimoDado->setCampo          ( "imovel" );
$obListaItbi->ultimoDado->setAlinhamento    ( "CENTRO" );
$obListaItbi->commitDado();
$obListaItbi->addDado();
$obListaItbi->ultimoDado->setCampo          ( "numeracao" );
$obListaItbi->ultimoDado->setAlinhamento    ( "CENTRO" );
$obListaItbi->commitDado();
$obListaItbi->addDado();
$obListaItbi->ultimoDado->setCampo          ( "status" );
$obListaItbi->ultimoDado->setAlinhamento    ( "CENTRO" );
$obListaItbi->commitDado();
$obListaItbi->show();

$stLocation2  = '../relatorios/OCGeraRelatorioResumoLote.php?'.Sessao::getId().'&stAcao='.$stAcao;
$stLocation2 .= '&inCodLoteInicio='.$rsResumoLote->getCampo("cod_lote").'&stExercicio='.$rsResumoLote->getCampo("exercicio").'&tipo=Pagamento&stTipoRelatorio=analitico&descricao=Relatório de Baixa do Lote';

$obButtonRelatorio = new Button;
$obButtonRelatorio->setName  ( "Relatorio" );
$obButtonRelatorio->setValue ( "Imprimir" );
$obButtonRelatorio->obEvento->setOnClick( "window.parent.frames['oculto'].location='".$stLocation2."';");

$obFormulario = new Formulario;
$obFormulario->addForm($obForm);
$obFormulario->defineBarra( array( $obButtonRelatorio), "left", "" );
$obFormulario->show();

SistemaLegado::LiberaFrames();
