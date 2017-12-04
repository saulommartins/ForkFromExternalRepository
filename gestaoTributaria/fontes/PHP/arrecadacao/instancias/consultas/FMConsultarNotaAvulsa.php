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
  * Página de Consulta Nota Avulsa
  * Data de criação : 02/09/2008

  * @author Analista: Fábio Bertoldi
  * @author Programador: Fernando Piccini Cercato

  * @ignore

    * $Id: FMConsultarNotaAvulsa.php 59612 2014-09-02 12:00:51Z gelson $

  Caso de uso: uc-05.03.19
**/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

//Definicao dos nomes de arquivos
$stPrograma = "ConsultarNotaAvulsa";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once( $pgJS );

$stAcao = $request->get('stAcao');

$rsListaDadosNotas = Sessao::read( 'ListaNotasAvulsas' );

$arDadosNotas = $rsListaDadosNotas->getElementos();
$arDadosSelecionados = array();
for ( $inX=0; $inX<count( $arDadosNotas ); $inX++ ) {
    if (
        ( $arDadosNotas[$inX]["inscricao_economica"] == $_REQUEST["inInscricaoEconomica"] ) &&
        ( $arDadosNotas[$inX]["cod_modalidade"] == $_REQUEST["inCodModalidade"] ) &&
        ( $arDadosNotas[$inX]["competencia"] == $_REQUEST["stCompetencia"] ) &&
        ( $arDadosNotas[$inX]["nro_serie"] == $_REQUEST["inNroSerie"] ) &&
        ( $arDadosNotas[$inX]["nro_nota"] == $_REQUEST["inNroNota"] )
       ) {
        $arDadosSelecionados[] = $arDadosNotas[$inX];
    }
}

$rsListaDadosServicos = new RecordSet;
$rsListaDadosServicos->preenche( $arDadosSelecionados );
$rsListaDadosServicos->addFormatacao( "valor_declarado", "NUMERIC_BR" );
$rsListaDadosServicos->addFormatacao( "valor_deducao", "NUMERIC_BR" );
$rsListaDadosServicos->addFormatacao( "valor_retido", "NUMERIC_BR" );
$rsListaDadosServicos->addFormatacao( "valor_lancado", "NUMERIC_BR" );
$rsListaDadosServicos->addFormatacao( "aliquota", "NUMERIC_BR" );

$stPrestadorServicos = $rsListaDadosServicos->getCampo( "numcgm_prestador" )." - ".$rsListaDadosServicos->getCampo( "nomcgm_prestador" );
$stInscricaoEconomica = $rsListaDadosServicos->getCampo( "inscricao_economica" );
$stModalidade = $rsListaDadosServicos->getCampo( "cod_modalidade" )." - ".$rsListaDadosServicos->getCampo("descricao_modalidade");
$stCompetencia = $rsListaDadosServicos->getCampo( "competencia" );
$stTomadorServicos = $rsListaDadosServicos->getCampo( "numcgm_tomador" )."".$rsListaDadosServicos->getCampo( "nomcgm_tomador" );
$stSerie = $rsListaDadosServicos->getCampo( "nro_serie" );
$stNroNota = $rsListaDadosServicos->getCampo( "nro_nota" );
$stDtEmissao = $rsListaDadosServicos->getCampo( "dt_emissao" );
$stSituacao = $rsListaDadosServicos->getCampo( "situacao_nota" );
//------------------------------------
$stNumeracaoCarne = $rsListaDadosServicos->getCampo( "numeracao" );
$stVencimentoCarne = $rsListaDadosServicos->getCampo( "dt_vencimento" );
$stSituacaoCarne = $rsListaDadosServicos->getCampo( "situacao_parcela" );
$stDataPagamentoCarne = $rsListaDadosServicos->getCampo( "dt_pagamento" );
$stLote = $rsListaDadosServicos->getCampo( "cod_lote" );
$stDataProcessamentoCarne = $rsListaDadosServicos->getCampo( "dt_baixa" );
$stProcessoCarne = $rsListaDadosServicos->getCampo( "cod_processo" )."/".$rsListaDadosServicos->getCampo( "ano_exercicio" );
$stObservacoesCarne = $rsListaDadosServicos->getCampo( "observacao_pagamento" );
$stBanco = $rsListaDadosServicos->getCampo( "num_banco" )." - ".$rsListaDadosServicos->getCampo( "nom_banco" );
$stAgencia = $rsListaDadosServicos->getCampo( "num_agencia" )." - ".$rsListaDadosServicos->getCampo( "nom_agencia" );
$stValorPago = number_format( $rsListaDadosServicos->getCampo( "valor_pago" ), 2, ',', '.' );
$stJurosPago = number_format( $rsListaDadosServicos->getCampo( "valor_pago_juros" ), 2, ',', '.' );
$stMultaPago = number_format( $rsListaDadosServicos->getCampo( "valor_pago_multa" ), 2, ',', '.' );
$stCorrecaoPago = number_format( $rsListaDadosServicos->getCampo( "valor_pago_correcao" ), 2, ',', '.' );
$stTotalPago = number_format( $rsListaDadosServicos->getCampo( "valor_pago_total" ), 2, ',', '.' );
$stValorPagar = number_format( $rsListaDadosServicos->getCampo( "valor_a_pagar" ), 2, ',', '.' );
$stJurosPagar = number_format( $rsListaDadosServicos->getCampo( "valor_juros_a_pagar" ), 2, ',', '.' );
$stMultaPagar = number_format( $rsListaDadosServicos->getCampo( "valor_multa_a_pagar" ), 2, ',', '.' );
$stCorreçãoPagar = number_format( $rsListaDadosServicos->getCampo( "valor_correcao_a_pagar" ), 2, ',', '.' );
$stTotalPagar = number_format( $rsListaDadosServicos->getCampo( "valor_total_a_pagar" ), 2, ',', '.' );

$obListaServicos = new Lista;
$obListaServicos->setRecordSet( $rsListaDadosServicos );
$obListaServicos->setMostraPaginacao( false );
$obListaServicos->setTitulo("Lista de Serviços");

$obListaServicos->addCabecalho();
    $obListaServicos->ultimoCabecalho->addConteudo("&nbsp;");
    $obListaServicos->ultimoCabecalho->setWidth( 5 );
$obListaServicos->commitCabecalho();

$obListaServicos->addCabecalho();
    $obListaServicos->ultimoCabecalho->addConteudo("Serviço");
    $obListaServicos->ultimoCabecalho->setWidth( 20 );
$obListaServicos->commitCabecalho();

$obListaServicos->addCabecalho();
    $obListaServicos->ultimoCabecalho->addConteudo("Aliquota (%)");
    $obListaServicos->ultimoCabecalho->setWidth( 10 );
$obListaServicos->commitCabecalho();

$obListaServicos->addCabecalho();
    $obListaServicos->ultimoCabecalho->addConteudo("Valor Declarado");
    $obListaServicos->ultimoCabecalho->setWidth( 10 );
$obListaServicos->commitCabecalho();

$obListaServicos->addCabecalho();
    $obListaServicos->ultimoCabecalho->addConteudo("Dedução");
    $obListaServicos->ultimoCabecalho->setWidth( 10 );
$obListaServicos->commitCabecalho();

$obListaServicos->addCabecalho();
    $obListaServicos->ultimoCabecalho->addConteudo("Valor Retido");
    $obListaServicos->ultimoCabecalho->setWidth( 10 );
$obListaServicos->commitCabecalho();

$obListaServicos->addCabecalho();
    $obListaServicos->ultimoCabecalho->addConteudo("Valor Lançado");
    $obListaServicos->ultimoCabecalho->setWidth( 10 );
$obListaServicos->commitCabecalho();

$obListaServicos->addDado();
    $obListaServicos->ultimoDado->setCampo( "[cod_servico] - [descricao_servico]" );
$obListaServicos->commitDado();

$obListaServicos->addDado();
    $obListaServicos->ultimoDado->setCampo( "aliquota" );
$obListaServicos->commitDado();

$obListaServicos->addDado();
    $obListaServicos->ultimoDado->setCampo( "valor_declarado" );
$obListaServicos->commitDado();

$obListaServicos->addDado();
    $obListaServicos->ultimoDado->setCampo( "valor_deducao" );
$obListaServicos->commitDado();

$obListaServicos->addDado();
    $obListaServicos->ultimoDado->setCampo( "valor_retido" );
$obListaServicos->commitDado();

$obListaServicos->addDado();
    $obListaServicos->ultimoDado->setCampo( "valor_lancado" );
$obListaServicos->commitDado();

$obListaServicos->montaHTML();
$stHtmlTmpServicos  = $obListaServicos->getHTML();

$obSpanServicos = new Span;
$obSpanServicos->setId      ( "spnListaServicos" );
$obSpanServicos->setValue   ( $stHtmlTmpServicos );

$obLabelPrestadorServicos = new Label;
$obLabelPrestadorServicos->setRotulo ( "Prestador de Serviços" );
$obLabelPrestadorServicos->setValue ( $stPrestadorServicos );

$obLabelInscricaoEconomica = new Label;
$obLabelInscricaoEconomica->setRotulo ( "Inscrição Econômica" );
$obLabelInscricaoEconomica->setValue ( $stInscricaoEconomica );

$obLabelModalidade = new Label;
$obLabelModalidade->setRotulo ( "Modalidade" );
$obLabelModalidade->setValue ( $stModalidade );

$obLabelCompetencia = new Label;
$obLabelCompetencia->setRotulo ( "Competência" );
$obLabelCompetencia->setValue ( $stCompetencia );

$obLabelTomadorServicos = new Label;
$obLabelTomadorServicos->setRotulo ( "Tomador de Serviços" );
$obLabelTomadorServicos->setValue ( $stTomadorServicos );

$obLabelSerie = new Label;
$obLabelSerie->setRotulo ( "Série" );
$obLabelSerie->setValue ( $stSerie );

$obLabelNroNota = new Label;
$obLabelNroNota->setRotulo ( "Nº da Nota" );
$obLabelNroNota->setValue ( $stNroNota );

$obLabelDtEmissao = new Label;
$obLabelDtEmissao->setRotulo ( "Data de Emissão" );
$obLabelDtEmissao->setValue ( $stDtEmissao );

$obLabelSituacao = new Label;
$obLabelSituacao->setRotulo ( "Situação" );
$obLabelSituacao->setValue ( $stSituacao );

//---------------------

$obLabelNumeracao = new Label;
$obLabelNumeracao->setRotulo ( "Numeração" );
$obLabelNumeracao->setValue ( $stNumeracaoCarne );

$obLabelVencimento = new Label;
$obLabelVencimento->setRotulo ( "Vencimento" );
$obLabelVencimento->setValue ( $stVencimentoCarne );

$obLabelSituacaoCarne = new Label;
$obLabelSituacaoCarne->setRotulo ( "Situação" );
$obLabelSituacaoCarne->setValue ( $stSituacaoCarne );

$obLabelDataPagamento = new Label;
$obLabelDataPagamento->setRotulo ( "Data de Pagamento" );
$obLabelDataPagamento->setValue ( $stDataPagamentoCarne );

$obLabelLote = new Label;
$obLabelLote->setRotulo ( "Lote" );
$obLabelLote->setValue ( $stLote );

$obLabelDataProcessamento = new Label;
$obLabelDataProcessamento->setRotulo ( "Data de Processamento" );
$obLabelDataProcessamento->setValue ( $stDataProcessamentoCarne );

$obLabelProcesso = new Label;
$obLabelProcesso->setRotulo ( "Processo" );
$obLabelProcesso->setValue ( $stProcessoCarne );

$obLabelObservacoes = new Label;
$obLabelObservacoes->setRotulo ( "Observações" );
$obLabelObservacoes->setValue ( $stObservacoesCarne );

$obLabelBanco = new Label;
$obLabelBanco->setRotulo ( "Banco" );
$obLabelBanco->setValue ( $stBanco );

$obLabelAgencia = new Label;
$obLabelAgencia->setRotulo ( "Agência" );
$obLabelAgencia->setValue ( $stAgencia );

$obLabelValorPago = new Label;
$obLabelValorPago->setRotulo ( "Valor Pago" );
$obLabelValorPago->setValue ( $stValorPago );

$obLabelJuros = new Label;
$obLabelJuros->setRotulo ( "Juros" );
$obLabelJuros->setValue ( $stJurosPago );

$obLabelMulta = new Label;
$obLabelMulta->setRotulo ( "Multa" );
$obLabelMulta->setValue ( $stMultaPago );

$obLabelCorrecao = new Label;
$obLabelCorrecao->setRotulo ( "Correção" );
$obLabelCorrecao->setValue ( $stCorrecaoPago );

$obLabelTotalPago = new Label;
$obLabelTotalPago->setRotulo ( "Total Pago" );
$obLabelTotalPago->setValue ( $stTotalPago );

$obLabelValorPagar = new Label;
$obLabelValorPagar->setRotulo ( "Valor a Pagar" );
$obLabelValorPagar->setValue ( $stValorPagar );

$obLabelJurosPagar = new Label;
$obLabelJurosPagar->setRotulo ( "Juros" );
$obLabelJurosPagar->setValue ( $stJurosPagar );

$obLabelMultaPagar = new Label;
$obLabelMultaPagar->setRotulo ( "Multa" );
$obLabelMultaPagar->setValue ( $stMultaPagar );

$obLabelCorreçãoPagar = new Label;
$obLabelCorreçãoPagar->setRotulo ( "Correção" );
$obLabelCorreçãoPagar->setValue ( $stCorreçãoPagar );

$obLabelTotalPagar = new Label;
$obLabelTotalPagar->setRotulo ( "Total a Pagar" );
$obLabelTotalPagar->setValue ( $stTotalPagar );

// OBJETOS HIDDEN
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( $stCtrl  );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao  );

$obBotaoVoltar = new Voltar();
$obBotaoVoltar->obEvento->setOnClick('VoltarLista();');

$obFormulario = new FormularioAbas;
$obFormulario->addHidden ( $obHdnCtrl );
$obFormulario->addHidden ( $obHdnAcao );

$obFormulario->addAba ( "Dados de Nota Avulsa" );
$obFormulario->addComponente( $obLabelPrestadorServicos );
$obFormulario->addComponente( $obLabelInscricaoEconomica );
$obFormulario->addComponente( $obLabelModalidade );
$obFormulario->addComponente( $obLabelCompetencia );
$obFormulario->addComponente( $obLabelTomadorServicos );
$obFormulario->addComponente( $obLabelSerie );
$obFormulario->addComponente( $obLabelNroNota );
$obFormulario->addComponente( $obLabelDtEmissao );
$obFormulario->addComponente( $obLabelSituacao );
$obFormulario->addSpan ( $obSpanServicos );

$obFormulario->addAba ( "Detalhamento de Valores" );
$obFormulario->addComponente( $obLabelNumeracao );
$obFormulario->addComponente( $obLabelVencimento );
$obFormulario->addComponente( $obLabelSituacaoCarne );
if ($stDataPagamentoCarne) {
    $obFormulario->addComponente( $obLabelDataPagamento );
    $obFormulario->addComponente( $obLabelLote );
    $obFormulario->addComponente( $obLabelDataProcessamento );
    $obFormulario->addComponente( $obLabelProcesso );
    $obFormulario->addComponente( $obLabelObservacoes );
    $obFormulario->addComponente( $obLabelBanco );
    $obFormulario->addComponente( $obLabelAgencia );
    $obFormulario->addComponente( $obLabelValorPago );
    $obFormulario->addComponente( $obLabelJuros );
    $obFormulario->addComponente( $obLabelMulta );
    $obFormulario->addComponente( $obLabelCorrecao );
    $obFormulario->addComponente( $obLabelTotalPago );
} else {
    $obFormulario->addComponente( $obLabelValorPagar );
    $obFormulario->addComponente( $obLabelJurosPagar );
    $obFormulario->addComponente( $obLabelMultaPagar );
    $obFormulario->addComponente( $obLabelCorreçãoPagar );
    $obFormulario->addComponente( $obLabelTotalPagar );
}

$obFormulario->defineBarra( array( $obBotaoVoltar ) );
$obFormulario->show();
