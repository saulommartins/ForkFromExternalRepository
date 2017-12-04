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
    * Formulario de Resumo do Fechamento da Baixa Manual
    * Data de Criação   : 11/05/2006

    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Fernando Piccini Cercato

    * @ignore

    * $Id: FMResumoFechamentoBaixaManual.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.03.10
*/

/*
$Log$
Revision 1.7  2006/09/15 10:55:12  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GT_ARR_NEGOCIO."RARRPagamento.class.php"                                              );

;

// passar do request pra variaveis
$stTipo       = $_REQUEST["pagamento"  ];
$inCodLote    = $_REQUEST["cod_lote"   ];
$inExercicio  = $_REQUEST["exercicio"  ];

$obRARRPagamento = new RARRPagamento;
$obRARRPagamento->inCodLote = $inCodLote;
$obRARRPagamento->stExercicio = $inExercicio;

$obRARRPagamento->consultaResumoLoteBaixaManual($rsResumoLote);
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

$obLblData   = new Label;
$obLblData->setRotulo    ( "Data Fechamento" );
$obLblData->setValue     ( $rsResumoLote->getCampo("data_lote") );

$obLblDataBaixa   = new Label;
$obLblDataBaixa->setRotulo    ( "Data da Baixa" );
$obLblDataBaixa->setValue     ( $rsResumoLote->getCampo("data_baixa") );

$obLblTipo   = new Label;
$obLblTipo->setRotulo    ( "Tipo" );
$obLblTipo->setValue     ( $stTipo );

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

$obFormulario->addTitulo        ( "Resumo de Fechamento da Baixa Manual" );
$obFormulario->addHidden        ( $obHdnLote                );
$obFormulario->addHidden        ( $obHdnExercicio           );
$obFormulario->addComponente    ( $obLblNumero              );
$obFormulario->addComponente    ( $obLblData                );
$obFormulario->addComponente    ( $obLblDataBaixa           );
$obFormulario->addComponente    ( $obLblTipo                );
$obFormulario->addComponente    ( $obLblBanco               );
$obFormulario->addComponente    ( $obLblAgencia             );

$obFormulario->show();

if ($stTipo == "Pagamento") {
    $obRARRPagamento->listaResumoLote($rsListaCreditos);
} else {
    $obRARRCarne = new RARRCarne;

    $obRARRPagamento->consultaListaFechaBaixaManual( $rsListaDados );
    $rsListaCreditos = new Recordset;
    if ( !$rsListaDados->Eof() ) {
        $arDados = array();
        $inDados = 0;
        while ( !$rsListaDados->Eof() ) {
            $obRARRPagamento->obRARRCarne->obRARRParcela->roRARRLancamento->setCodLancamento( $rsListaDados->getCampo("cod_lancamento") );
            $obRARRPagamento->obRARRCarne->setNumeracao( $rsListaDados->getCampo("numeracao") );
            $obRARRPagamento->obRARRCarne->obRARRParcela->setCodParcela( $rsListaDados->getCampo("cod_parcela") );

            $obRARRPagamento->listaResumoBaixaCanceladas( $rsListaCreditos, "",  $rsListaDados->getCampo("data_pagamento") );

            $rsListaDados->proximo();

            $arTmp = $rsListaCreditos->getElementos();
            $inTotal = count( $arTmp );
            for ($inX = 0; $inX < $inTotal; $inX++) {
                $boAchou = false;
                for ($inY = 0; $inY < $inDados; $inY++) {
                    if ($arDados[$inY]["cod"] == $arTmp[$inX]["cod"]) {
                        $arDados[$inY]["valor"] += $arTmp[$inX]["valor"];
                        $arDados[$inY]["somatorio"] += $arTmp[$inX]["somatorio"];
                        $arDados[$inY]["valor_credito_jurosp"] += $arTmp[$inX]["valor_credito_jurosp"];
                        $arDados[$inY]["valor_credito_multap"] += $arTmp[$inX]["valor_credito_multap"];
                        $arDados[$inY]["descontop"] += $arTmp[$inX]["descontop"];
                        $arDados[$inY]["juros"] += $arTmp[$inX]["juros"];
                        $arDados[$inY]["multa"] += $arTmp[$inX]["multa"];
                        $arDados[$inY]["desconto"] += $arTmp[$inX]["desconto"];
                        $boAchou = true;
                        break;
                    }
                }

                if (!$boAchou) {
                    $arDados[$inDados] = $arTmp[$inX];
                    $inDados++;
                }
            }
        }

        $rsListaCreditos->preenche( $arDados );
        $rsListaCreditos->setPrimeiroElemento();
    }
}

$arTmp = array();
$stControla = 1;
$stCodAnt = 'naoexiste';
$rsListaCreditos->setPrimeiroElemento();
foreach ($rsListaCreditos->arElementos as $value) {
    if ($stControla == 1) {
        $stControla = 2;
        $arTmp[] = $value;
    }
    if ($stControla == 2) {
       $arN = array( 'cod' =>  $value['cod'] ,'cod_credito' => '', 'cod_especie' => '' , 'cod_genero' => '', 'cod_natureza' => '', 'descricao_credito' => '&nbsp;&nbsp;&nbsp;&nbsp; Juros', 'somatorio' => $value['juros'], 'juros' => $value['juros'] , 'multa' => $value['multa']);
       $stControla = 3;
       if ( $value['juros'] > 0 ) $arTmp[] = $arN;
    }
    if ($stControla == 3) {
       $stControla = 4;
       $arN = array( 'cod' =>  $value['cod'] ,'cod_credito' => '', 'cod_especie' => '' , 'cod_genero' => '', 'cod_natureza' => '', 'descricao_credito' => '&nbsp;&nbsp;&nbsp;&nbsp; Multa', 'somatorio' => $value['multa'], 'juros' => $value['juros'], 'multa' => $value['multa']);
       if ( $value['multa'] > 0 ) $arTmp[] = $arN;
    }
    if ($stControla == 4) {
       $stControla = 1;
       $arN = array( 'cod' =>  $value['cod'] ,'cod_credito' => '', 'cod_especie' => '' , 'cod_genero' => '', 'cod_natureza' => '', 'descricao_credito' => '&nbsp;&nbsp;&nbsp;&nbsp; Diferença de Pagamento', 'somatorio' => $value['diferenca'], 'juros' => 0, 'multa' => 0);
       if ( $value['diferenca'] > 0 ) $arTmp[] = $arN;
    }
}
$rsListaCreditos->preenche($arTmp);

// diferença de pagamento
//$obRARRPagamento->listaResumoLoteDiff($rsListaDiff);
//$arTmp2 =  $rsListaCreditos->arElementos;
//$arTmp2[] = array( 'cod' =>  '' ,'cod_credito' => '', 'cod_especie' => '' , 'cod_genero' => '', 'cod_natureza' => '', 'descricao_credito' => 'Diferença de Pagamento', 'somatorio' => $rsListaDiff->getCampo('somatorio'), 'juros' => '', 'multa' => '');
//$rsListaCreditos->preenche($arTmp2);

$rsListaCreditos->setPrimeiroElemento();
foreach ($rsListaCreditos->arElementos as $value) {
    $stValor = str_replace(",",".",$value['somatorio']);
    $value['somatorio'] = $stValor;
//  $arTmp[] = $value;
}
//$rsListaCreditos->preenche($arTmp);
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
foreach ($rsListaCreditos->arElementos as $value) {
    $stValor = str_replace(".","",$value['somatorio']);
    $stValor = str_replace(",",".",$value['somatorio']);
    $nuTotalBaixados += $stValor;
}
// total inconsistente
$rsListaInconsistente->setPrimeiroElemento();
$nuTotalInconsistente = 0.00;
if ( $rsListaInconsistente->getNumLinhas() > 0 ) {
    foreach ($rsListaInconsistente->arElementos as $value) {
        $stValor = str_replace(".","",$value['valor']);
        $stValor = str_replace(",",".",$value['valor']);
        $nuTotalInconsistente += $stValor;
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

$stLocation2 = 'OCGeraRelatorioResumoLote.php?'.Sessao::getId().'&stAcao='.$stAcao.'&cod_lote='.$rsResumoLote->getCampo("cod_lote")."&exercicio=".$rsResumoLote->getCampo("exercicio")."&tipo=".$stTipo;
$obButtonRelatorio = new Button;
$obButtonRelatorio->setName  ( "Relatorio" );
$obButtonRelatorio->setValue ( "Imprimir" );
$obButtonRelatorio->obEvento->setOnClick( "window.parent.frames['oculto'].location='".$stLocation2."';");

$stLocation3 = 'LSResumoFechamentoBaixaManual.php?'.Sessao::getId().'&stAcao='.$stAcao;

$obBtnVoltar = new Button;
$obBtnVoltar->setName              ( "btnVoltar" );
$obBtnVoltar->setValue             ( "Voltar" );
$obBtnVoltar->setTipo              ( "button" );
$obBtnVoltar->obEvento->setOnClick ( "window.parent.frames['telaPrincipal'].location='".$stLocation3."';" );
$obBtnVoltar->setDisabled          ( false );

$obFormulario = new Formulario;
$obFormulario->addForm($obForm);

if ( $_REQUEST["Voltar"] == "" )
    $obFormulario->defineBarra( array( $obButtonRelatorio ), "left", "" );
else
    $obFormulario->defineBarra( array( $obButtonRelatorio, $obBtnVoltar ), "left", "" );

$obFormulario->show();

SistemaLegado::LiberaFrames();
?>
