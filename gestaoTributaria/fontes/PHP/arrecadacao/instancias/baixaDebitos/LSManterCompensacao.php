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
  * Página de Lista de Emissão de Carnês
  * Data de criação : 06/12/2007

  * @author Analista: Fábio Bertoldi
  * @author Programador: Fernando Piccini Cercato

  * @ignore

    * $Id: LSManterCompensacao.php 63839 2015-10-22 18:08:07Z franver $

  Caso de uso: uc-05.03.10
**/

/*
$Log$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRPagamentoCompensacao.class.php" );

//Definicao dos nomes de arquivos
$stPrograma = "ManterCompensacao";
$pgFilt = "FL".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once( $pgJS );
if (!defined('CAM_GT_ARR_INSTANCIA') && is_dir(dirname(__FILE__).'documentos/')) {
        define('CAM_GT_ARR_INSTANCIA',dirname(__FILE__).'/font/');
       $stCaminho = CAM_GT_ARR_INSTANCIA."documentos/";
}

$stAcao = $request->get('stAcao');

//Define arquivos PHP para cada acao
switch ($stAcao) {
    case 'incluir'   : $pgProx = $pgForm; break;
    case 'excluir'   : $pgProx = $pgProc; break;
    case 'reemissao'   : $pgProx = $pgFormVinculo; break;
    DEFAULT          : $pgProx = $pgForm;
}

//MANTEM FILTRO E PAGINACAO
$link = Sessao::read( 'link' );
$stLink .= "&stAcao=".$stAcao;
if ( isset($_GET["pg"]) and  isset($_GET["pos"]) ) {
    $stLink.= "&pg=".$_GET["pg"]."&pos=".$_GET["pos"];
    $link["pg"]  = $_GET["pg"];
    $link["pos"] = $_GET["pos"];
}

//USADO QUANDO EXISTIR FILTRO
//NA FL O VAR LINK DEVE SER RESETADA
if ( is_array($link) ) {
    $_REQUEST = $link;
} else {
    foreach ($_REQUEST as $key => $valor) {
        $link[$key] = $valor;
    }
}

Sessao::write( 'link', $link );
if (!$_REQUEST['inCGM'] && !$_REQUEST['inInscricaoEconomica'] && !$_REQUEST['inCodImovel']) {
    sistemaLegado::alertaAviso($pgFilt."?stAcao=".$stAcao, "Um dos campos a seguir deve ser preenchido: Contribuinte, Inscrição Municipal ou Inscrição Econômica", "n_erro", "erro", Sessao::getId(), "../");
    exit;
}

$stFiltro = "";
if ($_REQUEST['inCGM']) {
    $stFiltro .= " calculo_cgm.numcgm = ".$_REQUEST['inCGM']." AND ";
}

if ($_REQUEST['inCodImovel']) {
    $stFiltro .= " imovel_calculo.inscricao_municipal = ".$_REQUEST['inCodImovel']." AND ";
}

if ($_REQUEST['inInscricaoEconomica']) {
    $stFiltro .= " cadastro_economico_calculo.inscricao_economica = ".$_REQUEST['inInscricaoEconomica']." AND ";
}

$stFiltro = substr( $stFiltro, 0, strlen($stFiltro) - 4 );
$obTARRPagamentoCompensacao = new TARRPagamentoCompensacao;
$obTARRPagamentoCompensacao->ListaSaldoDisponivel( $rsListaSaldo, $stFiltro );
Sessao::write( 'total_compensacao', $rsListaSaldo->getCampo( 'saldo_disponivel' ) );
Sessao::write( 'saldo_disponivel', $rsListaSaldo->getCampo( 'saldo_disponivel' ) );
Sessao::write( 'saldo_restante', 0.00 );
Sessao::write( 'total_pago', 0.00 );

$rsListaSaldo->addFormatacao( 'saldo_disponivel', 'NUMERIC_BR' );

$stFiltro = "";
if ($_REQUEST['inCGM']) {
    $stFiltro .= " AND calculo_cgm.numcgm = ".$_REQUEST['inCGM'];
}

if ($_REQUEST['inCodImovel']) {
    $stFiltro .= " AND imovel_calculo.inscricao_municipal = ".$_REQUEST['inCodImovel'];
}

if ($_REQUEST['inInscricaoEconomica']) {
    $stFiltro .= " AND cadastro_economico_calculo.inscricao_economica = ".$_REQUEST['inInscricaoEconomica'];
}

$stFiltroComp = $stFiltro;
if ($_REQUEST["stExercicioInicial"] && $_REQUEST["stExercicioFinal"]) {
    $stFiltroComp .= " AND calculo.exercicio BETWEEN '".$_REQUEST["stExercicioInicial"]."' AND '".$_REQUEST["stExercicioFinal"]."'";
}else
    if ($_REQUEST["stExercicioInicial"]) {
        $stFiltroComp .= " AND calculo.exercicio = '".$_REQUEST["stExercicioInicial"]."'";
    }else
        if ($_REQUEST["stExercicioFinal"]) {
            $stFiltroComp .= " AND calculo.exercicio = '".$_REQUEST["stExercicioFinal"]."'";
        }

if ($_REQUEST["stTipoPagamento"] == "duplicado") {
    $obTARRPagamentoCompensacao->ListaParcelasPagas( $rsListaPagas, $stFiltroComp, " order by carne.exercicio, carne.numeracao " );
} else {
    $obTARRPagamentoCompensacao->ListaParcelasComDiferencaPagas( $rsListaPagas, $stFiltroComp, " order by carne.exercicio, carne.numeracao " );
    $arTMP = $rsListaPagas->getElementos();
    $arTMP2 = array();
    for ( $inX=0; $inX<count( $arTMP ); $inX++ ) {
        $boEncontrou = false;
        for ( $inY=0; $inY<count( $arTMP2 ); $inY++ ) {
            if ($arTMP2[$inY]["numeracao"] === $arTMP[$inX]["numeracao"]) {
                $arTMP2[$inY]["valor_pago"] += $arTMP[$inX]["valor_pago"];
                $arTMP2[$inY]["cod_calculo"] .= "#".$arTMP[$inX]["cod_calculo"];
                $boEncontrou = true;
                break;
            }
        }

        if (!$boEncontrou) {
            $arTMP2[] = $arTMP[$inX];
        }
    }
    $rsListaPagas->Preenche( $arTMP2 );
}

$stFiltro .= " AND (((now()::date <= parcela.vencimento) AND parcela.nr_parcela = 0 ) OR parcela.nr_parcela > 0 ) ";
$obTARRPagamentoCompensacao->ListaParcelasVencer( $rsListaVencer, $stFiltro, " order by carne.exercicio, carne.numeracao " );
$stFiltro = "";
if ($_REQUEST['inCGM']) {
    $stFiltro .= " AND divida_cgm.numcgm = ".$_REQUEST['inCGM'];
}

if ($_REQUEST['inCodImovel']) {
    $stFiltro .= " AND divida_imovel.inscricao_municipal = ".$_REQUEST['inCodImovel'];
}

if ($_REQUEST['inInscricaoEconomica']) {
    $stFiltro .= " AND divida_empresa.inscricao_economica = ".$_REQUEST['inInscricaoEconomica'];
}

$obTARRPagamentoCompensacao->ListaParcelasVencerDividaAtiva( $rsListaVencerDA, $stFiltro );
$arTMP = $rsListaVencer->getElementos();
$arTMP2 = $rsListaVencerDA->getElementos();
for ( $inX=0; $inX<count($arTMP2); $inX++ ) {
    $arTMP[] = $arTMP2[$inX];
}

$rsListaVencer->preenche( $arTMP );

$rsListaPagas->addFormatacao( 'valor_parcela', 'NUMERIC_BR' );
$rsListaPagas->addFormatacao( 'valor_pago', 'NUMERIC_BR' );
$rsListaVencer->addFormatacao( 'valor_parcela', 'NUMERIC_BR' );
$rsListaVencer->addFormatacao( 'valor_corrigido', 'NUMERIC_BR' );

$stCGM = $rsListaVencer->getCampo( "numcgm" )." - ".$rsListaVencer->getCampo( "nom_cgm" );

$obLista = new Lista;
if ($_REQUEST["stTipoPagamento"] == "duplicado") {
    $obLista->setTitulo ("Parcelas Pagas em Duplicidade");
} else {
    $obLista->setTitulo ("Parcelas Pagas a Maior");
}

$obLista->setRecordSet( $rsListaPagas );
$obLista->setMostraPaginacao(false);
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Numeração");
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Parcela");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Origem");
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Vencimento");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Valor");
$obLista->ultimoCabecalho->setWidth( 5  );
$obLista->commitCabecalho();
$obLista->addCabecalho();
if ($_REQUEST["stTipoPagamento"] == "duplicado") {
    $obLista->ultimoCabecalho->addConteudo("Valor Pago");
} else {
    $obLista->ultimoCabecalho->addConteudo("Valor Excedente");
}

$obLista->ultimoCabecalho->setWidth( 6 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "[numeracao] / [exercicio]" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nr_parcela" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "origem" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "vencimento" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "valor_parcela" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA');
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "valor_pago" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();

$obChkReemitir = new Checkbox;
$obChkReemitir->setName                ( "boParPaga" );
$obChkReemitir->setValue               ( "[numeracao]§[exercicio]§[valor_parcela]§[valor_pago]§[ocorrencia_pagamento]§[cod_convenio]§[origem]§[vencimento]§[nr_parcela]§[cod_calculo]" );
$obChkReemitir->obEvento->setOnChange  ( "buscaValor( 'SomaParcelasPagas' );" );

$obLista->addDadoComponente                    ( $obChkReemitir );
$obLista->ultimoDado->setAlinhamento           ( 'CENTRO' );
$obLista->ultimoDado->setCampo                 ( "parpagas" );
$obLista->commitDadoComponente                 ();
$obLista->montaHTML();
$stHtmlN  = $obLista->getHTML();

// lista de vencidas
$obListaNVencidas = new Lista;
$obListaNVencidas->setTitulo    ("Parcelas a Vencer");
$obListaNVencidas->setRecordSet( $rsListaVencer );
$obListaNVencidas->setMostraPaginacao(false);
$obListaNVencidas->addCabecalho();
$obListaNVencidas->ultimoCabecalho->addConteudo("&nbsp;");
$obListaNVencidas->ultimoCabecalho->setWidth( 5 );
$obListaNVencidas->commitCabecalho();
$obListaNVencidas->addCabecalho();
$obListaNVencidas->ultimoCabecalho->addConteudo("Numeração");
$obListaNVencidas->ultimoCabecalho->setWidth( 20 );
$obListaNVencidas->commitCabecalho();
$obListaNVencidas->addCabecalho();
$obListaNVencidas->ultimoCabecalho->addConteudo("Parcela");
$obListaNVencidas->ultimoCabecalho->setWidth( 5 );
$obListaNVencidas->commitCabecalho();
$obListaNVencidas->addCabecalho();
$obListaNVencidas->ultimoCabecalho->addConteudo("Origem");
$obListaNVencidas->ultimoCabecalho->setWidth( 15 );
$obListaNVencidas->commitCabecalho();
$obListaNVencidas->addCabecalho();
$obListaNVencidas->ultimoCabecalho->addConteudo("Vencimento");
$obListaNVencidas->ultimoCabecalho->setWidth( 10 );
$obListaNVencidas->commitCabecalho();
$obListaNVencidas->addCabecalho();
$obListaNVencidas->ultimoCabecalho->addConteudo("Valor");
$obListaNVencidas->ultimoCabecalho->setWidth( 5  );
$obListaNVencidas->commitCabecalho();
$obListaNVencidas->addCabecalho();
$obListaNVencidas->ultimoCabecalho->addConteudo("Valor Corrigido");
$obListaNVencidas->ultimoCabecalho->setWidth( 6 );
$obListaNVencidas->commitCabecalho();
$obListaNVencidas->addCabecalho();
$obListaNVencidas->ultimoCabecalho->addConteudo("&nbsp;");
$obListaNVencidas->ultimoCabecalho->setWidth( 5 );
$obListaNVencidas->commitCabecalho();

$obListaNVencidas->addDado();
$obListaNVencidas->ultimoDado->setCampo( "[numeracao] / [exercicio]" );
$obListaNVencidas->ultimoDado->setAlinhamento( 'CENTRO' );
$obListaNVencidas->commitDado();
$obListaNVencidas->addDado();
$obListaNVencidas->ultimoDado->setCampo( "nr_parcela" );
$obListaNVencidas->ultimoDado->setAlinhamento( 'DIREITA' );
$obListaNVencidas->commitDado();
$obListaNVencidas->addDado();
$obListaNVencidas->ultimoDado->setCampo( "origem" );
$obListaNVencidas->ultimoDado->setAlinhamento( 'CENTRO' );
$obListaNVencidas->commitDado();

$obListaNVencidas->addDado();
$obListaNVencidas->ultimoDado->setCampo( "vencimento" );
$obListaNVencidas->ultimoDado->setAlinhamento( 'DIREITA' );
$obListaNVencidas->commitDado();

$obListaNVencidas->addDado();
$obListaNVencidas->ultimoDado->setCampo( "valor_parcela" );
$obListaNVencidas->ultimoDado->setAlinhamento( 'DIREITA');
$obListaNVencidas->commitDado();

$obListaNVencidas->addDado();
$obListaNVencidas->ultimoDado->setCampo( "valor_corrigido" );
$obListaNVencidas->ultimoDado->setAlinhamento( 'DIREITA');
$obListaNVencidas->commitDado();

$obChkReemitir = new Checkbox;
$obChkReemitir->setName                        ( "boParVenc" );
$obChkReemitir->setValue                       ( "[numeracao]§[exercicio]§[valor_parcela]§[valor_corrigido]§[cod_convenio]§[origem]§[vencimento]§[nr_parcela]" );
$obChkReemitir->obEvento->setOnChange          ( "buscaValor( 'SomaParcelasVencer' );" );

$obListaNVencidas->addDadoComponente                    ( $obChkReemitir );
$obListaNVencidas->ultimoDado->setAlinhamento           ( 'CENTRO' );
$obListaNVencidas->ultimoDado->setCampo                 ( "parvencer" );
$obListaNVencidas->commitDadoComponente                 ();
$obListaNVencidas->montaHTML();
$stHtmlTmp  = $obListaNVencidas->getHTML();

$obSpanNormais = new Span;
$obSpanNormais->setId       ( 'spnListaNormais' );
$obSpanNormais->setValue    ( $stHtmlN );

$obSpanVencidas = new Span;
$obSpanVencidas->setId      ( "spnLista"      );
$obSpanVencidas->setValue   ( $stHtmlTmp );

$obLabelContribuinte = new Label;
$obLabelContribuinte->setName  ( "lblContribuinte" );
$obLabelContribuinte->setValue ( $stCGM );
$obLabelContribuinte->setRotulo ( "Contribuinte" );

$obLabelSaldoDisp = new Label;
$obLabelSaldoDisp->setName  ( "lblSaldoDisp" );
$obLabelSaldoDisp->setValue ( $rsListaSaldo->getCampo("saldo_disponivel") );
$obLabelSaldoDisp->setRotulo ( "Saldo Disponível" );

$obLabelVlrParcSel = new Label;
$obLabelVlrParcSel->setName  ( "lblVlrParcSel" );
$obLabelVlrParcSel->setID  ( "lblVlrParcSel" );
$obLabelVlrParcSel->setValue ( "0,00" );
$obLabelVlrParcSel->setRotulo ( "Valor das Parcelas Selecionadas" );

$obLabelTotComp = new Label;
$obLabelTotComp->setName ( "lblTotComp" );
$obLabelTotComp->setID ( "lblTotComp" );
$obLabelTotComp->setValue ( "0,00" );
$obLabelTotComp->setRotulo ( "Total para Compensação" );

$obLabelVlrComp = new Label;
$obLabelVlrComp->setName  ( "lblVlrComp" );
$obLabelVlrComp->setID  ( "lblVlrComp" );
$obLabelVlrComp->setValue ( "0,00" );
$obLabelVlrComp->setRotulo ( "Valor a Compensar" );

$obLabelSldRest = new Label;
$obLabelSldRest->setName  ( "lblSldRest" );
$obLabelSldRest->setID  ( "lblSldRest" );
$obLabelSldRest->setValue ( "0,00" );
$obLabelSldRest->setRotulo ( "Saldo Restante" );

// OBJETOS HIDDEN
$obHdnCompensar = new Hidden;
$obHdnCompensar->setName  ( "boCompensar" );
$obHdnCompensar->setValue ( 0 );

$obHdnTipoPagamento = new Hidden;
$obHdnTipoPagamento->setName  ( "stTipoPagamento" );
$obHdnTipoPagamento->setValue ( $_REQUEST["stTipoPagamento"] );

$obHdnCGM = new Hidden;
$obHdnCGM->setName  ( "stCGM" );
$obHdnCGM->setValue ( $_REQUEST['inCGM'] );

$obHdnImovel = new Hidden;
$obHdnImovel->setName  ( "stImovel" );
$obHdnImovel->setValue ( $_REQUEST['inCodImovel'] );

$obHdnEmpresa = new Hidden;
$obHdnEmpresa->setName  ( "stEmpresa" );
$obHdnEmpresa->setValue ( $_REQUEST['inInscricaoEconomica'] );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
if (isset($stCtrl)) {
    $obHdnCtrl->setValue ( $stCtrl  );
}
$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao  );

$obChkEmitirRelatorio = new CheckBox;
$obChkEmitirRelatorio->setName    ( "boEmitirRelatorio" );
$obChkEmitirRelatorio->setValue   ( "1" );
$obChkEmitirRelatorio->setLabel   ( "Emitir Relatório" );
$obChkEmitirRelatorio->setNull    ( true );
$obChkEmitirRelatorio->setChecked ( false );

$obChkAplicarAcrescimosDevidos = new CheckBox;
$obChkAplicarAcrescimosDevidos->setName    ( "boAplicaAcrescimos" );
$obChkAplicarAcrescimosDevidos->setValue   ( "1" );
$obChkAplicarAcrescimosDevidos->setLabel   ( "Aplicar Acréscimos Devidos" );
$obChkAplicarAcrescimosDevidos->setNull    ( true );
$obChkAplicarAcrescimosDevidos->setChecked ( false );
$obChkAplicarAcrescimosDevidos->obEvento->setOnChange ( "buscaValor( 'SomaParcelasVencer' );" );

$obBtnOK = new OK;
$obBtnOK->setName              ( "btnOk" );
$obBtnOK->obEvento->setOnClick ( "validarLista();" );

$obBtnCancelar = new Cancelar;

$botoesSpanBotoes = array ( $obBtnOK, $obBtnCancelar );

$obForm = new Form;
$obForm->setAction ( $pgProc  );
$obForm->setTarget ( "oculto" );

$obFormulario = new FormularioAbas;
$obFormulario->addForm  ( $obForm );
$obFormulario->addHidden( $obHdnTipoPagamento );
$obFormulario->addHidden( $obHdnCompensar );
$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnCGM );
$obFormulario->addHidden( $obHdnImovel );
$obFormulario->addHidden( $obHdnEmpresa );

if ($_REQUEST["stTipoPagamento"] == "duplicado") {
    $obFormulario->addAba   ( "Parcelas Pagas em Duplicidade" );
} else {
    $obFormulario->addAba   ( "Parcelas Pagas a Maior" );
}

$obFormulario->addSpan  ( $obSpanNormais);
$obFormulario->addAba   ( "Parcelas a Vencer" );
$obFormulario->addSpan  ( $obSpanVencidas);

$obFormulario->addDiv( 4, "componente" );
$obFormulario->addComponente( $obLabelContribuinte );
$obFormulario->addComponente( $obLabelSaldoDisp );
$obFormulario->addComponente( $obLabelVlrParcSel );
$obFormulario->addComponente( $obLabelTotComp );
$obFormulario->addComponente( $obLabelVlrComp );
$obFormulario->addComponente( $obLabelSldRest );
$obFormulario->fechaDiv();

$obFormulario->addComponente( $obChkAplicarAcrescimosDevidos );
$obFormulario->addComponente( $obChkEmitirRelatorio );
$obFormulario->defineBarra ( $botoesSpanBotoes, 'left', '' );

$obFormulario->show();

$rsListaVencer->setPrimeiroElemento();

Sessao::write( 'dados_contribuinte', array (
                                        "numCGM" => $rsListaVencer->getCampo( "numcgm" ),
                                        "nomCGM" => $rsListaVencer->getCampo( "nom_cgm" ),
                                        "inscricaoEconomica" => $_REQUEST['inInscricaoEconomica'],
                                        "inscricaoMunicipal" => $_REQUEST['inCodImovel']
                                    ) );
