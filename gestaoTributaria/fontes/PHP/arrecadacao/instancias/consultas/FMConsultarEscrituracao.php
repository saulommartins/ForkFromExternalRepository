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
  * Página de Consulta de Escrituração
  * Data de criação : 29/12/2006

  * @author Analista: Fábio Bertoldi
  * @author Programador: Cassiano de Vasconcellos Ferreira

    * $Id: FMConsultarEscrituracao.php 59612 2014-09-02 12:00:51Z gelson $

  Caso de uso: uc-05.03.19
**/

/*
$Log$
Revision 1.2  2007/05/09 20:14:04  cercato
Bug #9238#

Revision 1.1  2007/02/22 12:21:43  cassiano
Consulta escrituração

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_ARR_MAPEAMENTO.'TARRNotaServico.class.php' );

//Define o nome dos arquivos PHP
$stPrograma = "ConsultarEscrituracao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$rsListaNota = new RecordSet();

$obTARRNotaServico = new TARRNotaServico();
$stFiltro  = " WHERE \n";
$stFiltro .= "      cod_atividade = $_REQUEST[inCodAtividade]\n";
$stFiltro .= "      AND inscricao_economica = $_REQUEST[inInscricaoEconomica]\n";
$stFiltro .= "      AND timestamp = '$_REQUEST[timestamp]'\n";
$obTARRNotaServico->recuperaTodos($rsLista , $stFiltro);

if ( $rsLista->eof() ) {
    //buscar direto na listade servico(faturamento servico)
    include_once ( CAM_GT_ARR_MAPEAMENTO.'TARRFaturamentoServico.class.php' );
    $obTARRFaturamentoServico = new TARRFaturamentoServico();
    $stFiltro  = " WHERE \n";
    $stFiltro .= "     faturamento_servico.cod_atividade           = $_REQUEST[inCodAtividade]\n";
    $stFiltro .= "     AND faturamento_servico.inscricao_economica = $_REQUEST[inInscricaoEconomica]\n";
    $stFiltro .= "     AND faturamento_servico.timestamp           = '$_REQUEST[timestamp]'\n";
    $obTARRFaturamentoServico->recuperaRelacionamento($rsListaServico, $stFiltro);

    $rsListaServico->addFormatacao('valor_declarado','NUMERIC_BR_NULL');
    $rsListaServico->addFormatacao('valor_deducao','NUMERIC_BR_NULL');
    $rsListaServico->addFormatacao('valor_retido','NUMERIC_BR_NULL');
    $rsListaServico->addFormatacao('valor_lancado','NUMERIC_BR_NULL');

    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/Table.class.php';
    $table = new Table();
    $table->setRecordset( $rsListaServico );

    $table->setSummary('Lista de Serviços');

    //$table->setParametros( array( 'cod_modalidade', 'nom_modalidade') );
    $table->Head->addCabecalho( 'Seriço' ,          16 );
    $table->Head->addCabecalho( 'Aliquota(%)',      16 );
    $table->Head->addCabecalho( 'Valor Declarado' , 16 );
    $table->Head->addCabecalho( 'Dedução' ,         16 );
    $table->Head->addCabecalho( 'Valor Retido' ,    16 );
    $table->Head->addCabecalho( 'Valor Lançado' ,   16 );

    $table->Body->addCampo( '[cod_servico] - [nom_servico]' , 'C' );
    $table->Body->addCampo( 'aliquota', 'C' );
    $table->Body->addCampo( 'valor_declarado', 'C' );
    $table->Body->addCampo( 'valor_deducao', 'C' );
    $table->Body->addCampo( 'valor_retido', 'C' );
    $table->Body->addCampo( 'valor_lancado', 'C' );

    $table->Foot->addSoma ( 'valor_lancado', "D" );

    $table->montaHTML();

    $stListaServico =  $table->getHtml();
} else {
    include_once ( CAM_GT_ARR_MAPEAMENTO.'TARRNotaServico.class.php' );
    $obTARRNotaServico = new TARRNotaServico();
    $stFiltro = "     AND faturamento_servico.cod_atividade       = $_REQUEST[inCodAtividade]\n";
    $stFiltro .= "     AND faturamento_servico.inscricao_economica = $_REQUEST[inInscricaoEconomica]\n";
    $stFiltro .= "     AND faturamento_servico.timestamp           = '$_REQUEST[timestamp]'\n";
    $obTARRNotaServico->recuperaNotasServico($rsListaNota, $stFiltro);
    $rsListaNota->addFormatacao('valor_nota','NUMERIC_BR');
    $rsListaNota->addFormatacao('valor_mercadoria','NUMERIC_BR');
}

include_once(CAM_GA_CGM_MAPEAMENTO.'TCGMCGM.class.php');
$obTCGMCGM = new TCGMCGM();
$stFiltroCGM = ' WHERE CGM.numcgm = '.$_REQUEST['inNumCGM'];
$obTCGMCGM->recuperaRelacionamento($rsCGM, $stFiltroCGM);
$stNomeFantasia = $rsCGM->getCampo('nom_fantasia') ? $rsCGM->getCampo('nom_fantasia') : $rsCGM->getCampo('nom_cgm');

include_once(CAM_GT_CEM_MAPEAMENTO.'TCEMModalidadeLancamento.class.php');
$obTCEMModalidadeLancamento = new TCEMModalidadeLancamento();
$obTCEMModalidadeLancamento->setDado('cod_modalidade',$_REQUEST['inCodModalidade']);
$obTCEMModalidadeLancamento->consultar();

include_once(CAM_GT_ARR_MAPEAMENTO.'TARRCadastroEconomicoCalculo.class.php');
$obTARRCadastroEconomicoCalculo = new TARRCadastroEconomicoCalculo();
$stFiltroCalculo  = ' WHERE ';
$stFiltroCalculo .= '     timestamp = \''.$_REQUEST[timestamp].'\' AND ';
$stFiltroCalculo .= '     inscricao_economica = '.$_REQUEST[inInscricaoEconomica];
$obTARRCadastroEconomicoCalculo->recuperaTodos($rsCalculo, $stFiltroCalculo);

$inCodCalculo = $rsCalculo->getCampo('cod_calculo');
$stFiltroDetalhe = " ac.cod_calculo = ".$inCodCalculo;
$dtDataBase = date('Y-m-d');

include_once( CAM_GT_ARR_MAPEAMENTO.'TARRCarne.class.php');
$obTARRCarne = new TARRCarne();
$obTARRCarne->recuperaConsulta($rsDetalhamentoValores,$stFiltroDetalhe,'','',$dtDataBase,'','');

$nuValorPagar = $rsDetalhamentoValores->getCampo('parcela_valor') - $rsDetalhamentoValores->getCampo('parcela_valor_desconto');
$nuValorPagar = number_format ( $nuValorPagar, 2, ",", ".");

$rsDetalhamentoValores->addFormatacao('pagamento_valor','NUMERIC_BR_NULL');
$rsDetalhamentoValores->addFormatacao('parcela_valor_desconto','NUMERIC_BR_NULL');
$rsDetalhamentoValores->addFormatacao('parcela_juros_pago','NUMERIC_BR_NULL');
$rsDetalhamentoValores->addFormatacao('parcela_multa_pago','NUMERIC_BR_NULL');
$rsDetalhamentoValores->addFormatacao('tmp_pagamento_diferenca','NUMERIC_BR_NULL');
$rsDetalhamentoValores->addFormatacao('correcao_valor','NUMERIC_BR_NULL');
$rsDetalhamentoValores->addFormatacao('valor_total','NUMERIC_BR_NULL');

$rsDetalhamentoValores->addFormatacao('parcela_multa_pagar','NUMERIC_BR_NULL');
$rsDetalhamentoValores->addFormatacao('parcela_juros_pagar','NUMERIC_BR_NULL');
$rsDetalhamentoValores->addFormatacao('parcela_correcao_pagar','NUMERIC_BR_NULL');

$nuConsolidacao = $rsDetalhamentoValores->getCampo('consolidacao_numeracao');
$nuConsolidacao = $nuConsolidacao ? $nuConsolidacao : "&nbsp;";

$stProcesso = $rsDetalhamentoValores->getCampo('processo_pagamento');
$stProcesso = $stProcesso ? $stProcesso : "&nbsp;";

$stObservacao = $rsDetalhamentoValores->getCampo('observacao');
$stObservacao = $stObservacao ? $stObservacao : "&nbsp;";

$nuCorrecao = $rsDetalhamentoValores->getCampo('correcao_valor');
$nuCorrecao = $nuCorrecao ? $nuCorrecao : "&nbsp;";

//DADOS DA CONSULTA
$obLblContribuinte = new Label();
$obLblContribuinte->setRotulo('Contribuinte');
$obLblContribuinte->setValue($_REQUEST['inNumCGM'].' - '.$rsCGM->getCampo('nom_cgm'));

$obLblInscricaoEconomica = new Label();
$obLblInscricaoEconomica->setRotulo('Inscrição Econômica');
$obLblInscricaoEconomica->setValue($_REQUEST[inInscricaoEconomica].' - '.$stNomeFantasia );

$obLblModalidade = new Label();
$obLblModalidade->setRotulo('Modalidade');
$obLblModalidade->setValue($obTCEMModalidadeLancamento->getDado('nom_modalidade'));

$obLblCompetencia = new Label();
$obLblCompetencia->setRotulo('Competência');
$obLblCompetencia->setValue($_REQUEST['competencia']);

//LISTAS DE RECEITAS ESCRITURADAS
$obListaReceitasEscrituras = new Lista();
$obListaReceitasEscrituras->setMostraPaginacao(false);
$obListaReceitasEscrituras->setRecordSet($rsListaNota);
$obListaReceitasEscrituras->setTitulo('Lista de Receitas Escrituradas');
$obListaReceitasEscrituras->addCabecalho("", 5 );
$obListaReceitasEscrituras->addCabecalho("Código", 5 );
$obListaReceitasEscrituras->addCabecalho("Série", 5 );
$obListaReceitasEscrituras->addCabecalho("Nro Nota", 10 );
$obListaReceitasEscrituras->addCabecalho("Data de Emissão", 15 );
$obListaReceitasEscrituras->addCabecalho("Valor da Nota", 20 );
$obListaReceitasEscrituras->addCabecalho("Valor em Mercadorias", 20 );
$obListaReceitasEscrituras->addCabecalho("", 5 );

$obListaReceitasEscrituras->addDado();
$obListaReceitasEscrituras->ultimoDado->setCampo('cod_nota');
$obListaReceitasEscrituras->ultimoDado->setAlinhamento('CENTER');
$obListaReceitasEscrituras->commitDado();
$obListaReceitasEscrituras->addDado();
$obListaReceitasEscrituras->ultimoDado->setCampo('nro_serie');
$obListaReceitasEscrituras->ultimoDado->setAlinhamento('CENTER');
$obListaReceitasEscrituras->commitDado();
$obListaReceitasEscrituras->addDado();
$obListaReceitasEscrituras->ultimoDado->setCampo('nro_nota');
$obListaReceitasEscrituras->ultimoDado->setAlinhamento('CENTER');
$obListaReceitasEscrituras->commitDado();
$obListaReceitasEscrituras->addDado();
$obListaReceitasEscrituras->ultimoDado->setCampo('dt_emissao');
$obListaReceitasEscrituras->ultimoDado->setAlinhamento('CENTER');
$obListaReceitasEscrituras->commitDado();
$obListaReceitasEscrituras->addDado();
$obListaReceitasEscrituras->ultimoDado->setCampo('valor_nota');
$obListaReceitasEscrituras->ultimoDado->setAlinhamento('CENTER');
$obListaReceitasEscrituras->commitDado();
$obListaReceitasEscrituras->addDado();
$obListaReceitasEscrituras->ultimoDado->setCampo('valor_mercadoria');
$obListaReceitasEscrituras->ultimoDado->setAlinhamento('CENTER');
$obListaReceitasEscrituras->commitDado();

$obListaReceitasEscrituras->addAcao();
$obListaReceitasEscrituras->ultimaAcao->setFuncao(true);
$obListaReceitasEscrituras->ultimaAcao->setAcao('visualizar');
//$obListaReceitasEscrituras->ultimaAcao->setLink( "javascript: montaParametrosGET('listaServico');" );

$obListaReceitasEscrituras->ultimaAcao->setLink( "JavaScript:ajaxJavaScript('$pgOcul?Sessao::getId()','listaServico');");
$stLink  = $pgOcul.'?'.Sessao::getId().'&inCodAtividade='.$_REQUEST[inCodAtividade];
$stLink .= '&inInscricaoEconomica='.$_REQUEST[inInscricaoEconomica];
$stLink .= '&timestamp='.$_REQUEST[timestamp];

$obListaReceitasEscrituras->ultimaAcao->setLink( "JavaScript:listarServicos('$stLink')");
$obListaReceitasEscrituras->ultimaAcao->addCampo('1','cod_nota');

$obListaReceitasEscrituras->commitAcao();

$obSpnListaServicos = new Span();
$obSpnListaServicos->setId('stListaServicos');
$obSpnListaServicos->setValue($stListaServico);

//DETALHAMENTO DE VALORES
$obLblNumeracao = new Label();
$obLblNumeracao->setRotulo('Numeração');
$obLblNumeracao->setId('inNumeracao');
$obLblNumeracao->setValue($rsDetalhamentoValores->getCampo('numeracao'));

$obLblConsolidacaoCredito = new Label();
$obLblConsolidacaoCredito->setRotulo('Consolidação de Crédito');
$obLblConsolidacaoCredito->setId('nuConsolidacaoCredito');
$obLblConsolidacaoCredito->setValue($nuConsolidacao);

$obLblVencimento = new Label();
$obLblVencimento->setRotulo('Vencimento');
$obLblVencimento->setId('dtVenciamento');
$obLblVencimento->setValue($rsDetalhamentoValores->getCampo('parcela_vencimento_original'));

$obLblSituacao = new Label();
$obLblSituacao->setRotulo('Situação');
$obLblSituacao->setId('stSituacao');
$obLblSituacao->setValue($rsDetalhamentoValores->getCampo('situacao'));

$obLblDataPagamento = new Label();
$obLblDataPagamento->setRotulo('Data de Pagamento');
$obLblDataPagamento->setId('dtPagamento');
$obLblDataPagamento->setValue($rsDetalhamentoValores->getCampo('pagamento_data'));

$obLblLote = new Label();
$obLblLote->setRotulo('Lote');
$obLblLote->setId('stLote');
$obLblLote->setValue($rsDetalhamentoValores->getCampo('pagamento_cod_lote').'/'.$rsDetalhamentoValores->getCampo('pagamento_tipo'));

$obLblDataProcessamento = new Label();
$obLblDataProcessamento->setRotulo('Data de Processamento');
$obLblDataProcessamento->setId('dtProcessamento');
$obLblDataProcessamento->setValue($rsDetalhamentoValores->getCampo('pagamento_data_baixa'));

$obLblProcesso = new Label();
$obLblProcesso->setRotulo('Processo');
$obLblProcesso->setId('stProcesso');
$obLblProcesso->setValue($stProcesso);

$obLblObservacao = new Label();
$obLblObservacao->setRotulo('Observações');
$obLblObservacao->setId('stObservacoes');
$obLblObservacao->setValue($stObservacao);

$obLblBanco = new Label();
$obLblBanco->setRotulo('Banco');
$obLblBanco->setId('stBanco');
$stBanco = $rsDetalhamentoValores->getCampo('pagamento_num_banco').' - '.$rsDetalhamentoValores->getCampo('pagamento_nom_banco');
$obLblBanco->setValue($stBanco);

$stUsuario = $rsDetalhamentoValores->getCampo('pagamento_numcgm').' - '.$rsDetalhamentoValores->getCampo('pagamento_nomcgm');
$obLblUsuario = new Label();
$obLblUsuario->setRotulo('Usuário');
$obLblUsuario->setId('stUsuario');
$obLblUsuario->setValue($stUsuario);

$obLblValorPago = new Label();
$obLblValorPago->setRotulo('Valor Pago');
$obLblValorPago->setId('nuValorPago');
$obLblValorPago->setValue($rsDetalhamentoValores->getCampo('pagamento_valor'));

$obLblDescontos = new Label();
$obLblDescontos->setRotulo('Desconto');
$obLblDescontos->setId('nuDesconto');
$obLblDescontos->setValue($rsDetalhamentoValores->getCampo('parcela_valor_desconto'));

$obLblJuros = new Label();
$obLblJuros->setRotulo('Juros');
$obLblJuros->setId('nuJuros');
$obLblJuros->setValue($rsDetalhamentoValores->getCampo('parcela_juros_pago'));

$obLblMulta = new Label();
$obLblMulta->setRotulo('Multa');
$obLblMulta->setId('nuMulta');
$obLblMulta->setValue($rsDetalhamentoValores->getCampo('parcela_multa_pago'));

$obLblDiferencaPagamento = new Label();
$obLblDiferencaPagamento->setRotulo('Diferença de Pagamento');
$obLblDiferencaPagamento->setId('nuDiferencaPagamento');
$obLblDiferencaPagamento->setValue($rsDetalhamentoValores->getCampo('tmp_pagamento_diferenca'));

$obLblCorrecao = new Label();
$obLblCorrecao->setRotulo('Correção');
$obLblCorrecao->setId('nuCorrecao');
$obLblCorrecao->setValue($nuCorrecao);

$obLblTotalPago = new Label();
$obLblTotalPago->setRotulo('Total Pago');
$obLblTotalPago->setId('nuTotalPago');
$obLblTotalPago->setValue($rsDetalhamentoValores->getCampo('valor_total'));

$obTxtDataBase = new Data;
$obTxtDataBase->setName     ( "dtDataBase"  );
$obTxtDataBase->setId       ( "dtDataBase"  );
$obTxtDataBase->setValue    ( date('d/m/Y') );
$obTxtDataBase->setRotulo   ( "Data Base"   );
$obTxtDataBase->setStyle    ( "vertical-align:top;");
$obTxtDataBase->setTitle    ( "Data Base para os valores informados, altere para atualização dos valores!");

$obButtonAtualizarData = new Img;
$obButtonAtualizarData->setId    ( "imgAtualizar" );
$obButtonAtualizarData->setTitle ( "Atualizar" );
$obButtonAtualizarData->setNull  ( true );
$obButtonAtualizarData->setCaminho (CAM_FW_TEMAS."/imagens/btnRefresh.png");
$obButtonAtualizarData->obEvento->setOnClick('buscarDetalhamentoValoresDataBase();');

$obLblValorPagar = new Label();
$obLblValorPagar->setRotulo('Valor a Pagar');
$obLblValorPagar->setId('nuValorPagar');
$obLblValorPagar->setValue($nuValorPagar);

$obLblJurosPagar = new Label();
$obLblJurosPagar->setRotulo('Juros');
$obLblJurosPagar->setId('nuParcelaJurosPagar');
$obLblJurosPagar->setValue($rsDetalhamentoValores->getCampo('parcela_juros_pagar'));

$obLblMultaPagar = new Label();
$obLblMultaPagar->setRotulo('Multa');
$obLblMultaPagar->setId('nuParcelaMultaPagar');
$obLblMultaPagar->setValue($rsDetalhamentoValores->getCampo('parcela_multa_pagar'));

$obLblCorrecaoPagar = new Label();
$obLblCorrecaoPagar->setRotulo('Correção');
$obLblCorrecaoPagar->setId('nuParcelaCorrecaPagar');
$obLblCorrecaoPagar->setValue($rsDetalhamentoValores->getCampo('parcela_correcao_pagar'));

$obLblTotalPagar = new Label();
$obLblTotalPagar->setRotulo('Total a Pagar');
$obLblTotalPagar->setId('nuTotalPagar');
$obLblTotalPagar->setValue($rsDetalhamentoValores->getCampo('valor_total'));

$obBotaoVoltar = new Voltar();
$obBotaoVoltar->obEvento->setOnClick('VoltarLista();');

//$obBotaoRelatorio = new Button();

include_once ( $pgJS );

$obForm = new Form();

$obFormulario = new FormularioAbas();
$obFormulario->addForm($obForm);
$obFormulario->addAba('Dados da Consulta');
$obFormulario->addTitulo('Dados da Consulta');
$obFormulario->addComponente($obLblContribuinte);
$obFormulario->addComponente($obLblInscricaoEconomica);
$obFormulario->addComponente($obLblModalidade);
$obFormulario->addComponente($obLblCompetencia);
$obFormulario->addLista($obListaReceitasEscrituras);
$obFormulario->addSpan($obSpnListaServicos);

$obFormulario->addAba('Detalhamento de Valores');
$obFormulario->addTitulo('Detalhamento de Valores');
if ( $rsDetalhamentoValores->getCampo('tp_pagamento') == 't') {
    $obFormulario->addComponente($obLblNumeracao);
    $obFormulario->addComponente($obLblConsolidacaoCredito);
    $obFormulario->addComponente($obLblVencimento);
    $obFormulario->addComponente($obLblSituacao);
    $obFormulario->addComponente($obLblDataPagamento);
    $obFormulario->addComponente($obLblLote);
    $obFormulario->addComponente($obLblDataProcessamento);
    $obFormulario->addComponente($obLblProcesso);
    $obFormulario->addComponente($obLblObservacao);
    $obFormulario->addComponente($obLblBanco);
    $obFormulario->addComponente($obLblUsuario);
    $obFormulario->addComponente($obLblValorPago);
    $obFormulario->addComponente($obLblDescontos);
    $obFormulario->addComponente($obLblJuros);
    $obFormulario->addComponente($obLblMulta);
    $obFormulario->addComponente($obLblDiferencaPagamento);
    $obFormulario->addComponente($obLblCorrecao);
    $obFormulario->addComponente($obLblTotalPago);
} else {
    $obFormulario->agrupaComponentes( array($obTxtDataBase,$obButtonAtualizarData) );
    $obFormulario->addComponente($obLblValorPagar);
    $obFormulario->addComponente($obLblJurosPagar);
    $obFormulario->addComponente($obLblMultaPagar);
    $obFormulario->addComponente($obLblCorrecaoPagar);
    $obFormulario->addComponente($obLblTotalPagar);
}
$obFormulario->defineBarra(array($obBotaoVoltar));
$obFormulario->show();
