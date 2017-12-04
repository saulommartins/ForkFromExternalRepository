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
    * Página de Formulário para Pagamento
    * Data de Criação   : 25/10/2005

    * @author Analista: Lucas Leusin
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    * $Id: FMManterPagamento.php 66484 2016-09-02 18:07:47Z franver $

    * Casos de uso: uc-02.04.05,uc-02.03.28

*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once CLA_IAPPLETTERMINAL;
require_once CAM_GF_TES_NEGOCIO.'RTesourariaBoletim.class.php';
require_once CAM_GA_ADM_MAPEAMENTO.'TAdministracaoConfiguracao.class.php';
include_once CAM_GF_TES_COMPONENTES.'ISaldoCaixa.class.php';
include CAM_GF_TES_NEGOCIO.'RTesourariaCheque.class.php';
include CAM_FW_COMPONENTES.'Table/Table.class.php';
include_once CAM_GPC_TCEAL_MAPEAMENTO.'TTCEALPagamentoTipoDocumento.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterPagamento";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";
$stAcao = $request->get('stAcao');

include_once( $pgJs );
list( $inCodOrdem  , $stExercicioOrdem ) = explode( '/', $_REQUEST['stOrdem'] );

$stJs = isset($stJs) ? $stJs : null;
list( $inCodNota, $stExercicioLiquidacao ) = explode( '/', $_REQUEST['stNota'] );

//pega somente o exercicio
$stExercicioLiquidacao = substr($stExercicioLiquidacao,0,4);

$obRTesourariaBoletim = new RTesourariaBoletim();
$obRTesourariaBoletim->setExercicio( Sessao::getExercicio() );
$obRTesourariaBoletim->setDataBoletim( date( 'd/m/'.Sessao::getExercicio() ) );
$obRTesourariaBoletim->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST['inCodEntidade'] );
$obRTesourariaBoletim->addArrecadacao();
$obRTesourariaBoletim->roUltimaArrecadacao->obROrcamentoEntidade->setExercicio( Sessao::getExercicio() );
$obRTesourariaBoletim->roUltimaArrecadacao->obROrcamentoEntidade->obRCGM->setNumCGM( Sessao::read('numCgm') );
$obRTesourariaBoletim->roUltimaArrecadacao->obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidade );

$boMultiploBoletim = $obRTesourariaBoletim->multiploBoletim();

$obErro = $obRTesourariaBoletim->buscarCodigoBoletim( $inCodBoletim, $stDtBoletim );
if ( $obErro->ocorreu() ) {
    $nomAcao = SistemaLegado::pegaDado("nom_acao","administracao.acao"," where cod_acao = ".Sessao::read('acao'));
    if( !$inCodBoletim )
        SistemaLegado::alertaAviso($pgFilt."?stAcao=incluir",urlencode("Erro ao executar ação: ".$nomAcao." (".$obErro->getDescricao().")"),"","erro", Sessao::getId(), "../");
    else
        SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"","erro");
}

$obRTesourariaBoletim->addPagamento();
$flValorRetencoes = isset($flValorRetencoes) ? $flValorRetencoes : 0;
if ( trim($inCodOrdem) ) {
    if ($stAcao == 'alterar') {
        $obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->setEstorno( true );
    }
    $obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->setCodigoOrdem( $inCodOrdem       );
    $obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->setExercicio  ( $stExercicioOrdem );
    $obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST['inCodEntidade'] );
    $obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->consultar();
    if ($obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->getRetencao()) {
        Sessao::write('retencao', true);
        $arRetencoes = $obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->getRetencoes();
        foreach ($arRetencoes as $item) {
            $flValorRetencoes = bcadd($flValorRetencoes,$item['vl_retencao'],2);
        }
    }

} else {
    $obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->addNotaLiquidacao();
    $obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->roUltimaNotaLiquidacao->roREmpenhoEmpenho->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST['inCodEntidade'] );
    $obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->roUltimaNotaLiquidacao->setExercicio( $stExercicioLiquidacao );
    $obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->roUltimaNotaLiquidacao->setCodNota  ( $inCodNota             );
    $obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->roUltimaNotaLiquidacao->consultar();
}

/**
 * Implementacao do vinculo com a emissao de cheques
 */
Sessao::remove('arCheque');

$obSpnCheque = new Span();
$obSpnCheque->setId    ('spnCheque');

$obRTesourariaCheque = new RTesourariaCheque();
$obRTesourariaCheque->obREmpenhoOrdemPagamento->inCodigoOrdem = $inCodOrdem;
$obRTesourariaCheque->obREmpenhoOrdemPagamento->stExercicio = $stExercicioOrdem;
$obRTesourariaCheque->obREmpenhoOrdemPagamento->obROrcamentoEntidade->inCodigoEntidade = $_REQUEST['inCodEntidade'];
if ($inCodOrdem) {
    if ($stAcao == 'incluir') {
        $obRTesourariaCheque->listChequesOPBaixa($rsCheque);
    } else {
        $obRTesourariaCheque->listChequesOPAnularBaixa($rsCheque);
    }
} else {
    $rsCheque = new RecordSet;
}
Sessao::remove('arCheque');
if ($rsCheque->getNumLinhas() > 0) {
    $pgProc = "PR".$stPrograma."Cheque.php";
    Sessao::write('arCheque',$rsCheque->arElementos);
    $rsCheque->addFormatacao('valor','NUMERIC_BR');

    $obTable = new Table();
    $obTable->setRecordset( $rsCheque );
    $obTable->setSummary('Lista de cheques vinculados a OP');

    //$obTable->setConditional( true , "#efefef" );

    $obTable->Head->addCabecalho( 'Banco',          25);
    $obTable->Head->addCabecalho( 'Agência',        25);
    $obTable->Head->addCabecalho( 'Conta Corrente', 10);
    $obTable->Head->addCabecalho( 'Nr. Cheque',     10);
    $obTable->Head->addCabecalho( 'Valor',           8);

    $obTable->Body->addCampo('[num_banco] - [nom_banco]',     'E');
    $obTable->Body->addCampo('[num_agencia] - [nom_agencia]', 'E');
    $obTable->Body->addCampo('num_conta_corrente',            'E');
    $obTable->Body->addCampo('num_cheque',                    'E');
    $obTable->Body->addCampo('valor',                         'D');

    $obTable->Foot->addSoma('valor','D');

    $obTable->montaHTML();

    $obSpnCheque->setValue ($obTable->getHTML());
    $rsCheque->setPrimeiroElemento();
    while (!$rsCheque->eof()) {
        $flValorCheque += str_replace(',','.',str_replace('.','',$rsCheque->getCampo('valor')));
        $rsCheque->proximo();
    }
}

/**
 * Fim da implementacao
 */

$boAdiantamento = isset($boAdiantamento) ? $boAdiantamento : false;
$stValorPagar = isset($stValorPagar) ? $stValorPagar : 0;
$stValorTotal = isset($stValorTotal) ? $stValorTotal : 0;
$nuValorPrestado = isset($nuValorPrestado) ? $nuValorPrestado : 0;
$inCodContrapartida = isset($inCodContrapartida) ? $inCodContrapartida : 0;
$stDataLiquidacao = isset($stDataLiquidacao)? $stDataLiquidacao : "";
$inCount = 0;
$stDtEmissaoOP            = $obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->getDataEmissao();
$arREmpenhoNotaLiquidacao = $obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->getNotaLiquidacao();

foreach ($arREmpenhoNotaLiquidacao as $obREmpenhoNotaLiquidacao) {

    $stEmpenho      = $obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->getCodEmpenho().'/'.$obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->getExercicio();
    $stDtEmpenho    = $obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->getDtEmpenho();
    $stLiquidacao   = $obREmpenhoNotaLiquidacao->getCodNota().'/'.$obREmpenhoNotaLiquidacao->getExercicio();
    $stDtLiquidacao = $obREmpenhoNotaLiquidacao->getDtNota();
    $stTimestamp    = $obREmpenhoNotaLiquidacao->getTimestamp();

    $inCodEntidade  = $obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade();
    $stNomEntidade  = $obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->obROrcamentoEntidade->obRCGM->getNomCGM();
    $stCredor       = $obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->obRCGM->getNomCGM();
    $nuValorPagar   = bcadd( $stValorPagar, $obREmpenhoNotaLiquidacao->getValorTotal(), 4 );

    $inCodCategoria = $obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->getCodCategoria();

    if ($inCodCategoria == 2 || $inCodCategoria == 3) {
        include_once CAM_GF_EMP_MAPEAMENTO.'TEmpenhoItemPrestacaoContas.class.php';
        $obTEmpenhoItemPrestacaoContas = new TEmpenhoItemPrestacaoContas();
        $obTEmpenhoItemPrestacaoContas->setDado('cod_empenho',$obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->getCodEmpenho());
        $obTEmpenhoItemPrestacaoContas->setDado('cod_entidade',$obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade());
        $obTEmpenhoItemPrestacaoContas->setDado('exercicio',$obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->getExercicio());
        $obTEmpenhoItemPrestacaoContas->recuperaValorPrestado($rsValorPrestado);
        $nuValorPrestado = $rsValorPrestado->getCampo('vl_prestado');

        include_once CAM_GF_EMP_MAPEAMENTO.'TEmpenhoContrapartidaEmpenho.class.php';
        $obTEmpenhoContrapartidaEmpenho = new TEmpenhoContrapartidaEmpenho();
        $stFiltro  = " WHERE cod_empenho  = ".$obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->getCodEmpenho()."";
        $stFiltro .= "   AND cod_entidade = ".$obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade()."";
        $stFiltro .= "   AND exercicio    = '".$obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->getExercicio()."' ";
        $obTEmpenhoContrapartidaEmpenho->recuperaTodos($rsContrapartida,$stFiltro);
        $inCodContrapartida = $rsContrapartida->getCampo('conta_contrapartida');

        $boAdiantamento = true;
    }

    if ( trim($inCodOrdem) ) {
        $stValor      = $obREmpenhoNotaLiquidacao->getValorTotal();
        $stValorTotal = bcadd( $stValorTotal, $obREmpenhoNotaLiquidacao->getValorTotal(), 4 );
    } else {
        $obREmpenhoNotaLiquidacao->listarNotasDisponiveis( $rsNotas );
        $stVlNota  = str_replace( '.', '', $rsNotas->getCampo("vl_nota") );
        $stVlNota  = str_replace( ',','.', $stVlNota );
        $stVlOrdem = bcsub( $rsNotas->getCampo("vl_ordem") ,$rsNotas->getCampo("vl_ordem_anulada")  ,4 );
        $stValor      = bcsub( $stVlNota, $stVlOrdem, 4 );
        $stValorTotal = bcsub( $stVlNota, $stVlOrdem, 4 );
    }
    if ($stAcao == "alterar") {
        $obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->obREmpenhoNotaLiquidacao->setCodNota( $obREmpenhoNotaLiquidacao->getCodNota() );
        $obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->obREmpenhoNotaLiquidacao->setExercicio( $obREmpenhoNotaLiquidacao->getExercicio() );
        $obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->obREmpenhoNotaLiquidacao->stTimestamp = $stTimestamp;
        $obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->obROrcamentoEntidade->setCodigoEntidade( $inCodEntidade );
        $obRTesourariaBoletim->roUltimoPagamento->consultar();

        $arNota[$inCount]['cod_conta']          = $obRTesourariaBoletim->roUltimoPagamento->obRContabilidadePlanoBanco->getCodConta();
        $arNota[$inCount]['cod_plano']          = $obRTesourariaBoletim->roUltimoPagamento->obRContabilidadePlanoBanco->getCodPlano();
        $arNota[$inCount]['exercicio_plano']    = $obRTesourariaBoletim->roUltimoPagamento->obRContabilidadePlanoBanco->getExercicio();
        $arNota[$inCount]['cod_plano_retencao'] = $obRTesourariaBoletim->roUltimoPagamento->inCodPlanoRetencao;
    }

    $arNota[$inCount]['cod_entidade']  = $inCodEntidade;
    $arNota[$inCount]['empenho']       = $stEmpenho;
    $arNota[$inCount]['dt_empenho']    = $stDtEmpenho;
    $arNota[$inCount]['liquidacao']    = $stLiquidacao;
    $arNota[$inCount]['timestamp']     = $stTimestamp;
    $arNota[$inCount]['dt_liquidacao'] = $stDtLiquidacao;
    $arNota[$inCount]['dt_pagamento']  = substr($stTimestamp,8,2) . '/' . substr($stTimestamp,5,2) . '/' . substr($stTimestamp,0,4);
    if (isset($flValorCheque)) {
        if ($stValor < $flValorCheque) {
            $arNota[$inCount]['vl_total']      = $stValor;
            $flValorCheque -= $stValor;
        } elseif ($flValorCheque > 0) {
            $arNota[$inCount]['vl_total']      = $flValorCheque;
            $flValorCheque = 0;
        } else {
            $arNota[$inCount]['vl_total']      = 0;
        }
    } else {
        $arNota[$inCount]['vl_total']      = $stValor;
    }

    if ($stAcao == "alterar") {
        $arNota[$inCount]['vl_estornar']   = $stValor;
        if ($boAdiantamento) {
            $arNota[$inCount]['vl_estornar'] = bcsub($stValor,$nuValorPrestado,2);
        }
    }
    $stDataLiquidacao .= $stDtLiquidacao . "#";
    $inCount++;
}

Sessao::write('arNota', $arNota);

$nomAcao = SistemaLegado::pegaDado("nom_acao","administracao.acao"," where cod_acao = ".Sessao::read('acao'));

SistemaLegado::executaFramePrincipal( "buscaDado( 'montaItem' );" );

//Define a função do arquivo7, ex: incluir, excluir, alterar, consultar, etc
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

$arFiltroAux = Sessao::read('filtroAux');
if ( !count( $arFiltroAux ) > 0 ) {
    $arFiltroAux = $_REQUEST;
} else {
    $_REQUEST = $arFiltroAux;
}
Sessao::write('filtroAux', $arFiltroAux);

include_once CAM_GF_ORC_MAPEAMENTO.'TOrcamentoRecurso.class.php';
$obTOrcamentoRecurso = new TOrcamentoRecurso;

$inCodRecurso = $obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->obROrcamentoDespesa->obROrcamentoRecurso->getCodRecurso();

$stFiltroQuery = " WHERE true ";
if ($inCodRecurso) {
    $stFiltroQuery .= " AND cod_recurso = ".$inCodRecurso;
}

if (Sessao::getExercicio()) {
    $stFiltroQuery .= " AND exercicio = '".Sessao::getExercicio()."'";
}

$obErro = $obTOrcamentoRecurso->recuperaTodos( $rsLista, $stFiltroQuery);
if ( !$obErro->ocorreu() ) {
    $stNomRecurso = $rsLista->getCampo( 'nom_recurso' );
}

$stRecurso = $inCodRecurso.' - '.$stNomRecurso;

$obForm = new Form;
$obForm->setAction ( $pgProc );
$obForm->setTarget ( "oculto" );

$obIAppletTerminal = new IAppletTerminal( $obForm );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnCodNota = new Hidden;
$obHdnCodNota->setName  ( "inCodNota" );
$obHdnCodNota->setValue ( $inCodNota );

$obHdnExercicioLiquidacao = new Hidden;
$obHdnExercicioLiquidacao->setName  ( "stExercicioLiquidacao" );
$obHdnExercicioLiquidacao->setValue ( $stExercicioLiquidacao );

$obHdnAdiantamento = new Hidden;
$obHdnAdiantamento->setName  ( "boAdiantamento"            );
$obHdnAdiantamento->setValue ( $boAdiantamento             );

$obHdnVlPrestado = new Hidden;
$obHdnVlPrestado->setName  ( "nuValorPrestado"             );
$obHdnVlPrestado->setValue ( $nuValorPrestado              );

$obHdnNumCGM = new Hidden;
$obHdnNumCGM->setName( "inNumCGM" );
$obHdnNumCGM->setValue( $_REQUEST['inNumCGM'] );

$obHdnContrapartida = new Hidden;
$obHdnContrapartida->setName( "inCodContrapartida" );
$obHdnContrapartida->setValue( $inCodContrapartida );

$obHdnCodRecurso = new Hidden;
$obHdnCodRecurso->setName( "inCodRecurso" );
$obHdnCodRecurso->setValue( $inCodRecurso );

$obHdnVlPagamento = new Hidden;
$obHdnVlPagamento->setName( "nuValorPagamento" );
$obHdnVlPagamento->setValue( $stValorTotal     );

$obHdnCodEntidade = new Hidden;
$obHdnCodEntidade->setName( "inCodEntidade" );
$obHdnCodEntidade->setValue( $_REQUEST['inCodEntidade'] );

$obHdnCodOrdem = new Hidden;
$obHdnCodOrdem->setName( "inCodOrdem" );
$obHdnCodOrdem->setValue( $inCodOrdem );

$obHdnDtEmissaoOrdem = new Hidden;
$obHdnDtEmissaoOrdem->setName( "stDtEmissaoOrdem" );
$obHdnDtEmissaoOrdem->setValue( $stDtEmissaoOP );

$obHdnExercicioOrdem = new Hidden;
$obHdnExercicioOrdem->setName( "stExercicioOrdem" );
$obHdnExercicioOrdem->setValue( $stExercicioOrdem );

$obHdnCodEmpenho = new Hidden;
$obHdnCodEmpenho->setName( "inCodEmpenho" );
$obHdnCodEmpenho->setValue( $obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->getCodEmpenho() );

$obHdnDtBoletim = new Hidden;
$obHdnDtBoletim->setName ( "stDtBoletim" );
$obHdnDtBoletim->setValue( $stDtBoletim );

$obHdnSaldoContaBanco = new Hidden;
$obHdnSaldoContaBanco->setName ( "nuSaldoContaBanco" );
$obHdnSaldoContaBanco->setValue( "0" );

$obHdnSaldoContaBancoBR = new Hidden;
$obHdnSaldoContaBancoBR->setName ( "nuSaldoContaBancoBR" );
$obHdnSaldoContaBancoBR->setValue( "0" );

$obHdnCodTerminal = new Hidden;
$obHdnCodTerminal->setName ( "inCodTerminal" );
$obHdnCodTerminal->setValue( $_REQUEST['inCodTerminal'] );

$obHdnTimestampTerminal = new Hidden;
$obHdnTimestampTerminal->setName ( "stTimestampTerminal" );
$obHdnTimestampTerminal->setValue( $_REQUEST['stTimestampTerminal']  );

$obHdnTimestampUsuario = new Hidden;
$obHdnTimestampUsuario->setName ( "stTimestampUsuario" );
$obHdnTimestampUsuario->setValue( $_REQUEST['stTimestampUsuario']  );

$obHdnNomEntidade = new Hidden;
$obHdnNomEntidade->setName ( "stEntidade" );
$obHdnNomEntidade->setValue( $stNomEntidade );

if ($stAcao == 'alterar') {
    $obHdnTimestamp = new Hidden;
    $obHdnTimestamp->setName ( "stTimestamp" );
    $obHdnTimestamp->setValue( $_REQUEST['stTimestamp']  );
}

require_once CAM_GF_TES_COMPONENTES.'ISelectBoletim.class.php';
$obISelectBoletim = new ISelectBoletim;
$obISelectBoletim->obBoletim->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST['inCodEntidade']  );
$obISelectBoletim->obBoletim->setExercicio( Sessao::getExercicio() );
$obISelectBoletim->setValue( $request->get('inCodBoletim') );

if ($stAcao == 'alterar') {
    $obISelectBoletim->obEvento->setOnChange ( "BloqueiaFrames(true,false); \r\n ;buscaDado('alteraBoletim'); \r\n
                                                if (this.value != '') { \r\n
                                                    var data = this.value.split(':'); \r\n
                                                    var data_bo = data[1]; \r\n
                                                    document.getElementById('stDtEstorno').innerHTML = ''+data[1]+''; \r\n
                                                } else document.getElementById('stDtEstorno').innerHTML = ''; \r\n
                                            ");
    $jsOnload = "if (document.getElementById('inCodBoletim').value) {
                     var data = document.getElementById('inCodBoletim').value.split(':'); \r\n
                     var data_bo = data[1]; \r\n
                     document.getElementById('stDtEstorno').innerHTML = ''+data[1]+''; \r\n
                 }
                ";
} else {

    $arNota = Sessao::read('arNota');

    $obISelectBoletim->obEvento->setOnChange ( "BloqueiaFrames(true,false); \r\n ; buscaDado('alteraBoletim'); \r\n
                                            if (this.value != '') { \r\n
                                                var data = this.value.split(':'); \r\n
                                                var data_bo = data[1]; \r\n
                                                var data_bo = data_bo.split('/'); \r\n
                                                var inCodOrdem = '".$inCodOrdem."';
                                                var dtLiquidacao = '".$arNota[0]['dt_liquidacao']."';
                                                if (inCodOrdem) {
                                                    var data_op = '".$stDtEmissaoOP."';
                                                    var data_op = data_op.split('/'); \r\n
                                                    if (data_op[2]+data_op[1]+data_op[0] > data_bo[2]+data_bo[1]+data_bo[0]) { \r\n
                                                            alertaAviso('A data do pagamento é anterior à data de emissão da OP!','','erro','".Sessao::getId()."');\r\n
                                                    } else alertaAviso('','','','".Sessao::getId()."'); \r\n
                                                } else if (dtLiquidacao != '') { \r\n
                                                    var data_li = '".$arNota[0]['dt_liquidacao']."'; \r\n
                                                    var data_li = data_li.split('/'); \r\n
                                                    if (data_li[2]+data_li[1]+data_li[0] > data_bo[2]+data_bo[1]+data_bo[0]) { \r\n
                                                        alertaAviso('A data do pagamento é anterior à data da liquidacao!','','erro','".Sessao::getId()."');\r\n
                                                    } else  alertaAviso('','','','".Sessao::getId()."'); \r\n
                                                }
                                            }
                                            montaParametrosGET('verificaFornecedor');

                                        ");
        // Verifica se tem prestacao de contas pendentes
        if ($boAdiantamento) {
            $jsOnLoad .= "if (document.getElementById('inCodBoletim').value) {
                            montaParametrosGET('verificaFornecedor');
                          }";
        }
    }

// Define Objeto Label para entidade
$obLblEntidade = new Label;
$obLblEntidade->setRotulo( "Entidade"     );
$obLblEntidade->setId    ( "stEntidade"   );
$obLblEntidade->setValue ( $stNomEntidade );

// Define Objeto Label para Ordem de pagamento
$obLblOrdemPagamento = new Label;
$obLblOrdemPagamento->setRotulo( "Ordem de Pagamento" );
$obLblOrdemPagamento->setId    ( "inCodOrdem"         );
if($inCodOrdem)
     $obLblOrdemPagamento->setValue( $inCodOrdem." / ".$stExercicioOrdem );
else $obLblOrdemPagamento->setValue( '' );

$obLblOrdemPagamentoEmissao = new Label;
$obLblOrdemPagamentoEmissao->setRotulo( "Data de emissão" );
$obLblOrdemPagamentoEmissao->setValue ( $stDtEmissaoOP );

// Define Objeto Label para Credor
$obLblCredor = new Label;
$obLblCredor->setRotulo( "Credor"   );
$obLblCredor->setId    ( "stCredor" );
$obLblCredor->setValue ( $stCredor  );

// Define Objeto Label para Valor a pagar | pago
$obLblVlPagar = new Label;
if($stAcao == 'incluir')
    $obLblVlPagar->setRotulo( "Valor a Pagar" );
else
    $obLblVlPagar->setRotulo( "Valor Pago" );
$obLblVlPagar->setId        ( "nuVlPagar" );
$obLblVlPagar->setValue     ( number_format( $stValorTotal, 2, ',', '.' ) );

// Define Objeto Label para Recurso da despesa
$obLblRecurso = new Label;
$obLblRecurso->setRotulo( "Recurso da Despesa" );
$obLblRecurso->setId    ( "stRecurso"          );
$obLblRecurso->setValue ( $stRecurso           );

if (isset($arRetencoes)) {
    $obLblValorRetencoes = new Label;
    $obLblValorRetencoes->setRotulo( "Total de Retenções" );
    $obLblValorRetencoes->setValue ( number_format( $flValorRetencoes, 2, ',','.') );

    $obLblValorLiquido = new Label;
    $obLblValorLiquido->setRotulo( "Valor Líquido da OP" );
    $obLblValorLiquido->setValue ( number_format( bcsub($stValorTotal,$flValorRetencoes,2),2,',','.') );

    $stListaExt = '';
    $stListaOrc = '';
    $inCountExt = 0;
    $inCountOrc = 0;

    foreach ($arRetencoes as $item) {
        if ($item['tipo'] == 'O') {
            $arTmpRetOrc[$inCountOrc] = $item;
            $inCountOrc++;
        }
        if ($item['tipo'] == 'E') {
            $arTmpRetExt[$inCountExt] = $item;
            $inCountExt++;
        }
    }

    if (isset($arTmpRetOrc)) {

        $rsRecordSetOrc = new RecordSet;
        $rsRecordSetOrc->preenche($arTmpRetOrc);
        $rsRecordSetOrc->addFormatacao('vl_retencao','NUMERIC_BR');

        $obLista = new Lista;
        $obLista->setTitulo ( "Retenções Orçamentárias");
        $obLista->setRecordSet ($rsRecordSetOrc );
        $obLista->setMostraPaginacao( false );
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "&nbsp;" );
        $obLista->ultimoCabecalho->setWidth( 3 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Conta de Retenção" );
        $obLista->ultimoCabecalho->setWidth( 65 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Valor da Retenção" );
        $obLista->ultimoCabecalho->setWidth( 20 );
        $obLista->commitCabecalho();
        $obLista->addDado();
        if (Sessao::getExercicio() > 2012) {
            $obLista->ultimoDado->setCampo( "[cod_receita] - [nom_conta_receita]" );
        } else {
            $obLista->ultimoDado->setCampo( "[cod_receita] - [nom_conta]" );
        }
        $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "[vl_retencao]" );
        $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
        $obLista->commitDado();
        $obLista->montaInnerHTML();
        $stListaOrc = $obLista->getHTML();
    }

   if (isset($arTmpRetExt)) {
        $rsRecordSetExt = new RecordSet;
        $rsRecordSetExt->preenche($arTmpRetExt);
        $rsRecordSetExt->addFormatacao('vl_retencao','NUMERIC_BR');

        $obLista = new Lista;
        $obLista->setTitulo ( "Retenções Extra-Orçamentárias");
        $obLista->setRecordSet ($rsRecordSetExt );
        $obLista->setMostraPaginacao( false );
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "&nbsp;" );
        $obLista->ultimoCabecalho->setWidth( 3 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Conta de Retenção" );
        $obLista->ultimoCabecalho->setWidth( 65 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Valor da Retenção" );
        $obLista->ultimoCabecalho->setWidth( 20 );
        $obLista->commitCabecalho();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "[cod_plano] - [nom_conta]" );
        $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "[vl_retencao]" );
        $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
        $obLista->commitDado();
        $obLista->montaInnerHTML();
        $stListaExt = $obLista->getHTML();
    }

    $obSpnRetencoes = new Span;
    $obSpnRetencoes->setId ('spnRet');
    $obSpnRetencoes->setValue( $stListaOrc."".$stListaExt );
}

// Define Objeto Span para Itens da ordem ou liquidacao
$obSpnItens = new Span();
$obSpnItens->setId( 'spnItem' );

$inCodConta = isset($inCodConta) ? $inCodConta : null;
$stNomConta = isset($stNomConta) ? $stNomConta : null;

if ($stAcao == 'incluir') {
    // Define Objeto BuscaInner para conta pagadora
    $obBscConta = new BuscaInner;
    $obBscConta->setRotulo ( "Conta Pagadora" );
    $obBscConta->setTitle  ( "*Informe Conta Pagadora." );
    $obBscConta->setId     ( "stNomConta"  );
    $obBscConta->setValue  ( $stNomConta   );
    $obBscConta->setNull   ( false         );
    $obBscConta->obCampoCod->setName     ( "inCodPlano" );
    $obBscConta->obCampoCod->setSize     ( 10           );
    $obBscConta->obCampoCod->setNull     ( false        );
    $obBscConta->obCampoCod->setMaxLength( 8            );
    $obBscConta->obCampoCod->setValue    ( $inCodPlano  );
    $obBscConta->obCampoCod->setAlign    ( "left"       );
    $obBscConta->setFuncaoBusca("abrePopUp('".CAM_GF_CONT_POPUPS."planoConta/FLPlanoConta.php','frm','inCodPlano','stNomConta','tes_pagamento&inCodEntidade=".$_REQUEST['inCodEntidade']."','".Sessao::getId()."','800','550');");
    $obBscConta->obCampoCod->obEvento->setOnChange("return false;");
    $obBscConta->obCampoCod->obEvento->setOnBlur( "if (this.value) { BloqueiaFrames(true,false); buscaDado('saldoConta'); }" );
} elseif ($stAcao == 'alterar') {
    // Define Objeto Hidden para cod_plano
    $obHdnConta = new Hidden();
    $obHdnConta->setName( 'inCodPlano' );
    $obHdnConta->setValue( $inCodConta );

    // Define Objeto Label para codigo do plano
    $obLblConta = new Label();
    $obLblConta->setId( 'inCodPlano' );
    $obLblConta->setRotulo ( '*Conta Pagadora' );
    $obLblConta->setValue( $inCodConta.' - '.$stNomConta );
}

//Busca cod_uf para verificar de qual estado o município pertence
$inCodUf = SistemaLegado::pegaConfiguracao("cod_uf", 2, Sessao::getExercicio(), $boTransacao);
$stSiglaUf = SistemaLegado::pegaDado("sigla_uf","sw_uf","where cod_uf = ".$inCodUf."");

//Disponibilizar na tela de Pagamento Extra na Tesouraria o campo Tipo Pagamento para atender exigências do Tribunal de Tocantins.
if ( $inCodUf == 27 ) {
    include_once CAM_GPC_TCETO_MAPEAMENTO.'TTCETOTipoPagamento.class.php';
    $obTTCETOTipoPagamento = new TTCETOTipoPagamento();
    $obTTCETOTipoPagamento->recuperaTodos($rsTipoPagamento,"","",$boTransacao);

    // Define o objeto para o tipo de pagamento
    $obTipoPagamento = new Select;
    $obTipoPagamento->setRotulo    ( "Tipo de Pagamento" );
    $obTipoPagamento->setName      ( "inCodTipoPagamento" );
    $obTipoPagamento->setCampoId   ( 'cod_tipo'      );
    $obTipoPagamento->setCampoDesc ( '[cod_tipo] - [descricao]' );
    $obTipoPagamento->addOption    ( "", "Selecione" );
    $obTipoPagamento->setNull      ( false );
    $obTipoPagamento->setStyle     ( "width: 220px" );        
    $obTipoPagamento->preencheCombo($rsTipoPagamento);
}

//TCEPB, quando o municipio for de PB, cria-se o campo Origem do Recurso
if((SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio()))==15){
    include_once CAM_GPC_TPB_NEGOCIO.'RTCEPBTipoOrigemRecurso.class.php';
    $obRTPBTipoOrigemRecurso = new RTCEPBTipoOrigemRecurso;
    $obRTPBTipoOrigemRecurso->recuperaOrigemRecurso($rsOrigemRecurso);
    $obCboOrigemRecurso = new Select;
    $obCboOrigemRecurso->setName('inCodOrigemRecurso');
    $obCboOrigemRecurso->setId('inCodOrigemRecurso');
    $obCboOrigemRecurso->setTitle('Informe a origem do recurso.');
    $obCboOrigemRecurso->setRotulo('Origem do Recurso TCE/PB');
    $obCboOrigemRecurso->setCampoDesc('[cod_tipo] - [descricao]');
    $obCboOrigemRecurso->setCampoId('[cod_tipo]-[exercicio]');
    $obCboOrigemRecurso->addOption('', 'Selecione');
    $obCboOrigemRecurso->preencheCombo( $rsOrigemRecurso );
    $obCboOrigemRecurso->setNull(false);
}

//Disponibilizar campo Tipo Pagamento para atender exigências do Tribunal da Bahia.
if ( $inCodUf == 5 ) {
    include_once CAM_GPC_TCMBA_MAPEAMENTO.'TTCMBATipoPagamento.class.php';
    $obTTCMBATipoPagamento = new TTCMBATipoPagamento();
    $obTTCMBATipoPagamento->recuperaTodos($rsTipoPagamento,"","",$boTransacao);

    // Define o objeto para o tipo de pagamento
    $obTipoPagamento = new Select;
    $obTipoPagamento->setRotulo    ( "Tipo de Pagamento" );
    $obTipoPagamento->setName      ( "inCodTipoPagamento" );
    $obTipoPagamento->setId        ( "inCodTipoPagamento" );
    $obTipoPagamento->setCampoId   ( 'cod_tipo'      );
    $obTipoPagamento->setCampoDesc ( '[cod_tipo] - [descricao]' );
    $obTipoPagamento->addOption    ( "", "Selecione" );
    $obTipoPagamento->setNull      ( false );
    $obTipoPagamento->setStyle     ( "width: 220px" );        
    $obTipoPagamento->preencheCombo($rsTipoPagamento);
    $obTipoPagamento->obEvento->setOnChange("buscaDado('montaDocumentoTCMBA');");
    
    $obTxtValorDoc = new TextBox;
    $obTxtValorDoc->setName     ( "numDocPagamento" );
    $obTxtValorDoc->setId       ( "numDocPagamento" );
    $obTxtValorDoc->setValue    ( $numDocPagamento );
    $obTxtValorDoc->setRotulo   ( "Detalhe do Tipo Pagamento" );
    $obTxtValorDoc->setTitle    ( "Informar o numero de identificação do lançamento, como número do cheque, número do TED, etc." );
    $obTxtValorDoc->setinteiro  ( true );
    $obTxtValorDoc->setNull     ( false );
    $obTxtValorDoc->setMaxLength( 8 );
}

$obAdministracaoConfiguracao = new TAdministracaoConfiguracao;
$obAdministracaoConfiguracao->recuperaTodos($rsAdministracaoConfiguracao, " WHERE configuracao.exercicio = '".Sessao::getExercicio()."' AND configuracao.parametro = 'seta_tipo_documento_tcmgo'");
$boMostrarComboTipoDocTcmgo  = $rsAdministracaoConfiguracao->getCampo('valor');

if ($boMostrarComboTipoDocTcmgo  == 'true') {
    require_once CAM_GF_EMP_MAPEAMENTO.'TEmpenhoTipoDocumentoTcepbInterna.class.php';
    $obTEmpenhoTipoDocumentoTcepbInterna = new TEmpenhoTipoDocumentoTcepbInterna;
    $obTEmpenhoTipoDocumentoTcepbInterna->recuperaTodos($rsOrigemRecurso);

    $obCboDocTipo = new Select;
    $obCboDocTipo->setName('inDocTipo');
    $obCboDocTipo->setId('inDocTipo');
    $obCboDocTipo->setRotulo('Tipo de Documento');
    $obCboDocTipo->setCampoDesc('[cod_tipo] - [descricao]');
    $obCboDocTipo->setCampoId('cod_tipo');
    $obCboDocTipo->addOption('', 'Selecione');
    $obCboDocTipo->preencheCombo( $rsOrigemRecurso );
    $obCboDocTipo->setNull(false);
    $obCboDocTipo->obEvento->setOnChange("buscaDado('montaDocumento')");
}

$obAdministracaoConfiguracao->recuperaTodos($rsAdministracaoConfiguracao, " WHERE configuracao.exercicio = '".Sessao::getExercicio()."' AND configuracao.parametro = 'seta_tipo_documento_tcemg'");
$boMostrarComboTipoDocTcemg  = $rsAdministracaoConfiguracao->getCampo('valor');

if ($boMostrarComboTipoDocTcemg  == 'true') {
    require_once CAM_GF_EMP_MAPEAMENTO.'TEmpenhoTipoDocumentoTcemgInterna.class.php';
    $obTEmpenhoTipoDocumentoTcemgInterna = new TEmpenhoTipoDocumentoTcemgInterna;
    $obTEmpenhoTipoDocumentoTcemgInterna->recuperaTodos($rsOrigemRecurso);

    $obCboDocTipo = new Select;
    $obCboDocTipo->setName('inDocTipo');
    $obCboDocTipo->setId('inDocTipo');
    $obCboDocTipo->setRotulo('Tipo de Documento');
    $obCboDocTipo->setCampoDesc('[cod_tipo] - [descricao]');
    $obCboDocTipo->setCampoId('cod_tipo');
    $obCboDocTipo->addOption('', 'Selecione');
    $obCboDocTipo->preencheCombo( $rsOrigemRecurso );
    $obCboDocTipo->setNull(false);
    $obCboDocTipo->obEvento->setOnChange("buscaDado('montaDocumentoTcemg')");
}

if ($stSiglaUf == "AL") {
    //veriica se ja contem cheques vinculados a essa OP
    list($inCodNota,$stExercicioLiquidacao) = explode('/', $_REQUEST['stNota']);
    $inCodEntidade = $_REQUEST['inCodEntidade'];
    $rsRecordset = new RecordSet;

    if (($inCodNota != '') && ($stExercicioLiquidacao != '')) {
        $obTTCEALPagamentoTipoDocumento = new TTCEALPagamentoTipoDocumento;
        $obTTCEALPagamentoTipoDocumento->setDado('cod_nota' , $inCodNota);
        $obTTCEALPagamentoTipoDocumento->setDado('exercicio', $stExercicioLiquidacao);
        $obTTCEALPagamentoTipoDocumento->setDado('cod_entidade', $inCodEntidade);
        $obTTCEALPagamentoTipoDocumento->recuperaCheque($rsRecordset, "" , "" , $boTransacao);
    }

    if ($rsRecordset->inNumLinhas < 0) {
        $inNumCheque = '';
        $boDisabled = false;
        $arTipoPagamento[] = array("cod_tipo" => "1"  ,"descricao" => "Ordem Bancária");
        $arTipoPagamento[] = array("cod_tipo" => "2"  ,"descricao" => "Cheque");
    } else {
        $inNumCheque = $rsRecordset->getCampo('num_documento');
        $boDisabled = true;
        $arTipoPagamento[] = array("cod_tipo" => "2"  ,"descricao" => "Cheque");
    }    
    
    $rsTipoPagamento = new RecordSet();
    $rsTipoPagamento->preenche($arTipoPagamento);

    $obCboDocTipoAL = new Select;
    $obCboDocTipoAL->setName              ('inDocTipo');
    $obCboDocTipoAL->setId                ('inDocTipo');
    $obCboDocTipoAL->setRotulo            ('Tipo de Documento');
    $obCboDocTipoAL->setCampoDesc         ('[cod_tipo] - [descricao]');
    $obCboDocTipoAL->setCampoId           ('cod_tipo');
    if ($boDisabled) {
        $obCboDocTipoAL->setDisabled      (true);
    }else{
        $obCboDocTipoAL->addOption        ('', 'Selecione');
    }
    $obCboDocTipoAL->preencheCombo        ( $rsTipoPagamento );
    $obCboDocTipoAL->setNull              (false);
    $obCboDocTipoAL->obEvento->setOnChange("buscaDado('montaDocumentoTCEAL');");

    $obTxtValorDoc = new TextBox;
    $obTxtValorDoc->setName     ( "nuDoc" );
    $obTxtValorDoc->setId       ( "nuDoc" );
    $obTxtValorDoc->setValue    ( $inNumCheque );
    $obTxtValorDoc->setRotulo   ( "Numero" );
    $obTxtValorDoc->setTitle    ( "Informe o Número." );
    $obTxtValorDoc->setDisabled ( $boDisabled );
    $obTxtValorDoc->setDecimais ( 0 );
    $obTxtValorDoc->setinteiro  ( true );
    $obTxtValorDoc->setNull     ( false );
    $obTxtValorDoc->setSize     ( 10 );
    $obTxtValorDoc->setMaxLength( 10 );

}

$obChkOutraConta = new Checkbox;
$obChkOutraConta->setName   ( 'boPagarOutra' );
$obChkOutraConta->setId     ( 'boPagarOutra' );
$obChkOutraConta->setLabel  ( 'Realizar pagamento com outra Conta Pagadora' );
$obChkOutraConta->setChecked( false );

$nuValor = isset($nuValor) ? $nuValor : null;

// Define Objeto Numeric para valor recebido
$obTxtValor = new Numerico;
$obTxtValor->setName     ( "nuValor" );
$obTxtValor->setId       ( "nuValor" );
$obTxtValor->setValue    ( $nuValor  );
$obTxtValor->setRotulo   ( "*Valor"  );
$obTxtValor->setTitle    ( "Informe o Valor para Pagamento." );
$obTxtValor->setDecimais ( 2     );
$obTxtValor->setNegativo ( false );
$obTxtValor->setNull     ( true  );
$obTxtValor->setSize     ( 23    );
$obTxtValor->setMaxLength( 23    );
$obTxtValor->setMinValue ( 1     );

$stCaminho = CAM_GF_TES_INSTANCIAS."pagamento/".$pgProc;

$stJs .= "alertaQuestao('".$stCaminho."','sn','".Sessao::getId()."');";
if ($stAcao == 'incluir') {
    $jsOnload .= "\r\n
           if (document.getElementById('inCodBoletim').value != '') { \r\n
               var data = document.getElementById('inCodBoletim').value.split(':'); \r\n
               var data_bo = data[1]; \r\n
               var data_bo = data_bo.split('/'); \r\n
               var inCodOrdem = '".$inCodOrdem."';
               var dtLiquidacao = '".$arNota[0]['dt_liquidacao']."';
               if (inCodOrdem) {
                   var data_op = '".$stDtEmissaoOP."';
                   var data_op = data_op.split('/'); \r\n
                   if (data_op[2]+data_op[1]+data_op[0] > data_bo[2]+data_bo[1]+data_bo[0]) { \r\n
                           alertaAviso('A data do pagamento é anterior à data de emissão da OP!','','erro','".Sessao::getId()."');\r\n
                   } else alertaAviso('','','','".Sessao::getId()."'); \r\n
               } else if (dtLiquidacao != '') { \r\n
                   var data_li = '".$arNota[0]['dt_liquidacao']."'; \r\n
                   var data_li = data_li.split('/'); \r\n
                   if (data_li[2]+data_li[1]+data_li[0] > data_bo[2]+data_bo[1]+data_bo[0]) { \r\n
                       alertaAviso('A data do pagamento é anterior à data da liquidacao!','','erro','".Sessao::getId()."');\r\n
                   } else  alertaAviso('','','','".Sessao::getId()."'); \r\n
               }
           }\r\n ";
           if ($rsCheque->getNumLinhas() <= 0) {
                $stEval .="
                var erro = false;
                if (document.frm.inCodPlano.value == '') {
                    erro = true;
                    mensagem += '@Informe uma conta pagadora!';
                }";
           }
} else {
    $stEval = " erro = false; ";
}

$obHdnEval = new HiddenEval;
$obHdnEval->setName  ( "stEval" );
$obHdnEval->setValue ( $stEval  );

// Define Objeto Button para incluir itens
$obBtnIncluir = new Button();
$obBtnIncluir->setValue( 'Incluir' );
$obBtnIncluir->obEvento->setOnClick( "incluirItem();" );

// Define Objeto Button para Limpar itens
$obBtnLimpar = new Button();
$obBtnLimpar->setValue( 'Limpar' );
$obBtnLimpar->obEvento->setOnClick( "limparItem();" );

// Define Objeto Span para dados adicionais
$obSpnDados = new Span;
$obSpnDados->setId( "spnDados" );

$obSpnDocumento = new Span;
$obSpnDocumento->setId( "spnDocumento" );

if ($stAcao == 'incluir') {
    // Define Objeto TextArea para observações
    $obTxtObs = new TextArea;
    $obTxtObs->setName   ( "stObservacoes" );
    $obTxtObs->setId     ( "stObservacoes" );
    $obTxtObs->setValue  ( $stObservacoes  );
    $obTxtObs->setRotulo ( "Observações"   );
    $obTxtObs->setTitle  ( "Digite a Observação Relativa à este Recebimento." );
    $obTxtObs->setNull   ( true );
    $obTxtObs->setRows   ( 2    );
    $obTxtObs->setCols   ( 100  );
    $obTxtObs->setMaxCaracteres( 170 );
} else {
    // Define Objeto Data para data do estorno
    $obLblDtEstorno = new Label();
    $obLblDtEstorno->setRotulo ( 'Data'       );
    $obLblDtEstorno->setId   ( 'stDtEstorno' );
if (!$boMultiploBoletim) $obLblDtEstorno->setValue( $stDtBoletim  );
    // Define Objeto TextArea para motivo da anulação
    $stMotivo = isset($stMotivo) ? $stMotivo : null;
    $obTxtMotivo = new TextArea;
    $obTxtMotivo->setName   ( "stMotivo" );
    $obTxtMotivo->setId     ( "stMotivo" );
    $obTxtMotivo->setValue  ( $stMotivo  );
    $obTxtMotivo->setRotulo ( "Motivo do Estorno" );
    $obTxtMotivo->setTitle  ( "Digite o Motivo do Estorno do Pagamento." );
    $obTxtMotivo->setNull   ( true            );
    $obTxtMotivo->setRows   ( 2               );
    $obTxtMotivo->setCols   ( 100             );
    $obTxtMotivo->setMaxCaracteres    ( 170 );
}

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm       ($obForm              );
$obFormulario->addHidden     ($obHdnAcao           );
$obFormulario->addHidden     ($obHdnCtrl           );
$obFormulario->addHidden     ($obHdnAdiantamento   );
$obFormulario->addHidden     ($obHdnExercicioLiquidacao   );
$obFormulario->addHidden     ($obHdnCodNota   );
$obFormulario->addHidden     ($obHdnVlPrestado     );
$obFormulario->addHidden     ($obHdnContrapartida  );
$obFormulario->addHidden     ($obHdnNumCGM         );
$obFormulario->addHidden     ($obHdnCodRecurso     );
$obFormulario->addHidden     ($obHdnVlPagamento    );
$obFormulario->addHidden     ($obHdnCodEntidade    );
$obFormulario->addHidden     ($obHdnCodOrdem       );
$obFormulario->addHidden     ($obHdnDtEmissaoOrdem );
$obFormulario->addHidden     ($obHdnExercicioOrdem );
$obFormulario->addHidden     ($obHdnCodEmpenho     );
$obFormulario->addHidden     ($obHdnDtBoletim      );
$obFormulario->addHidden     ($obHdnNomEntidade    );
$obFormulario->addHidden     ($obIAppletTerminal   );
$obFormulario->addHidden     ($obHdnEval, true     );
$obFormulario->addHidden     ($obHdnSaldoContaBanco);
$obFormulario->addHidden     ($obHdnSaldoContaBancoBR);
$obFormulario->addTitulo     ("Dados do Boletim"   );
$obFormulario->addComponente ($obISelectBoletim    );
$obFormulario->addTitulo     ("Dados do Pagamento" );
$obFormulario->addComponente ($obLblEntidade       );
$obFormulario->addComponente ($obLblOrdemPagamento );
$obFormulario->addComponente ($obLblOrdemPagamentoEmissao );
$obFormulario->addComponente ($obLblCredor         );
$obFormulario->addComponente ($obLblVlPagar        );
if (isset($arRetencoes)) {
    $obFormulario->addComponente ( $obLblValorRetencoes );
    $obFormulario->addComponente ( $obLblValorLiquido   );
}
$obFormulario->addComponente ( $obLblRecurso        );
if (isset($arRetencoes)) {
    $obFormulario->addSpan       ( $obSpnRetencoes      );
}
$obFormulario->addSpan       ( $obSpnItens          );

$obFormulario->addSpan       ( $obSpnCheque         );
if ($stAcao == 'incluir') {
    if ($rsCheque->getNumLinhas() <= 0) {
        $obFormulario->addComponente ( $obBscConta      );
    }
    //TCETO ou TCMBA
    if ($inCodUf == 27 || $inCodUf == 5) {
        $obFormulario->addComponente ( $obTipoPagamento );
        if($inCodUf == 5)
            $obFormulario->addComponente ( $obTxtValorDoc );
    }
    //TCEPB
    if((SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio()))==15){
        $obFormulario->addComponente ( $obCboOrigemRecurso );
    }

    if ($stSiglaUf != "BA" && $stSiglaUf != "AL") {
        if (($boMostrarComboTipoDocTcmgo  == 'true') || ($boMostrarComboTipoDocTcemg == 'true')) {
            $obFormulario->addComponente( $obCboDocTipo );
            $obFormulario->addSpan( $obSpnDocumento );
        }
    }elseif($stSiglaUf == "AL"){
        if( !Sessao::read('arCheque') ) {
            $obFormulario->addComponente( $obCboDocTipoAL );
            $obFormulario->addComponente( $obTxtValorDoc );
        }
        $obFormulario->addSpan( $obSpnDocumento );
    }

    if ($rsCheque->getNumLinhas() <= 0) {
        $obFormulario->addComponente ( $obChkOutraConta );
    }
    $obFormulario->addComponente ( $obTxtObs        );
} elseif ($stAcao == 'alterar') {
    $obFormulario->addHidden     ( $obHdnConta      );
    $obFormulario->addHidden     ( $obHdnTimestamp  );
    $obFormulario->addComponente ( $obLblDtEstorno  );
    $obFormulario->addComponente ( $obTxtMotivo     );
}
    
$obOk = new Ok();
if ($stAcao == 'alterar') {
    $obOk->obEvento->setOnClick("
        var erro = false;
        if (f.nuValorPrestado.value > 0) {
            if ( confirm('Este empenho é de adiantamentos/subvenções, caso seja estornado não poderá ser pago novamente. Deseja continuar?')) { \n
                  erro = false;      \n
            } else { erro = true; } \n
        }
        if ( Valida() && erro == false) {
            Salvar();
            BloqueiaFrames(true, false);
        }");
} else {
    $obOk->obEvento->setOnClick("
        var erro = false;
        if ( parseFloat(f.nuValorPagamento.value) > parseFloat(f.nuSaldoContaBanco.value) ) { \n
            if ( confirm( 'O saldo da conta informada não é suficiente para pagar este empenho.\\n (Saldo da conta: R$ '+f.nuSaldoContaBancoBR.value+')\\n Se efetuar este pagamento, o saldo da conta vai ficar negativo. Deseja continuar?')) {  \n
                erro = false;      \n
            } else {
                erro = true
            }; \n
        }
        if ( Valida() && erro == false ) {
            Salvar();
            BloqueiaFrames(true, false);
        }");
}

$stLocation = $pgList.'?'.Sessao::getId();

$obCancelar = new Button();
$obCancelar->setValue ("Cancelar");
$obCancelar->obEvento->setOnclick("Cancelar('".$stLocation."');");

$obFormulario->defineBarra ( Array( $obOk, $obCancelar));

$obFormulario->show();

$ISaldoCaixa = new ISaldoCaixa();
$ISaldoCaixa->inCodEntidade = $_REQUEST['inCodEntidade'];
$jsOnload .= $ISaldoCaixa->montaSaldo();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
