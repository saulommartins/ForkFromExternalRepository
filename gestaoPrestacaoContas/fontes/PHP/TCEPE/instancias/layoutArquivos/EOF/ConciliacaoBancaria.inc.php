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
  * Layout exportação TCE-PE arquivo : Conciliacao Bancaria
  * Data de Criação

  * @author Analista:
  * @author Desenvolvedor: Evandro Melos
  *
  * @ignore
  * $Id: ConciliacaoBancaria.inc.php 60807 2014-11-17 16:20:40Z diogo.zarpelon $
  * $Date: 2014-11-17 14:20:40 -0200 (Mon, 17 Nov 2014) $
  * $Author: diogo.zarpelon $
  * $Rev: 60807 $
  *
*/
include_once CAM_GPC_TCEPE_MAPEAMENTO."TTCEPEConciliacaoBancaria.class.php";
include_once CAM_GF_TES_NEGOCIO."RTesourariaConciliacao.class.php";

$boTransacao = new Transacao();
$obTTCEPEConciliacaoBancaria = new TTCEPEConciliacaoBancaria();

$obTTCEPEConciliacaoBancaria->setDado('exercicio'    , Sessao::getExercicio() );
$obTTCEPEConciliacaoBancaria->setDado('cod_entidade' , $inCodEntidade         );

if ($inCodCompetencia < 10) {
  $inCodCompetencia = '0'.$inCodCompetencia;
}

$obTTCEPEConciliacaoBancaria->setDado('mes' , $inCodCompetencia       );
$obTTCEPEConciliacaoBancaria->recuperaTodos($rsConciliacoes, "" ,"" , $boTransacao );

$inMes = $inCodCompetencia;

$dt_inicial = "01/".$inMes."/".Sessao::getExercicio();
$dt_final = SistemaLegado::retornaUltimoDiaMes($inMes,Sessao::getExercicio());
$inMesConciliacaoCorrente =  $inMes;
    
// Incrementa o mês para trazer os dados da Tesouraria.
$inMes = ($inMes == 12) ? 01 : str_pad($inMes+1, 2, "0", STR_PAD_LEFT);
$inAno = substr(Sessao::getExercicio(), 2, 4);
// Usado para incrementar o nro sequencial passado ao arquivo.
$i = 1;

while (!$rsConciliacoes->eof()) {   
    // Listagem das movimentações pendentes.
    $forma_conciliacao = '';
    $arTmp = array();
    $nuSaldoContabilConciliado = '';    

    $obRTesourariaConciliacao = new RTesourariaConciliacao();
    $obRTesourariaConciliacao->setDataInicial   ($dt_inicial);
    $obRTesourariaConciliacao->setDataFinal     ($dt_final );
    $obRTesourariaConciliacao->obRContabilidadePlanoBanco->setExercicio(Sessao::getExercicio());
    $obRTesourariaConciliacao->obRContabilidadePlanoBanco->setCodPlano($rsConciliacoes->getCampo('cod_plano'));
    $obRTesourariaConciliacao->setMes           (intval($inMesConciliacaoCorrente));
    $obRTesourariaConciliacao->obRTesourariaAssinatura->obROrcamentoEntidade->setCodigoEntidade($inCodEntidade);
    $obRTesourariaConciliacao->obRTesourariaAssinatura->setExercicio(Sessao::getExercicio());
    $obRTesourariaConciliacao->obRTesourariaAssinatura->obROrcamentoEntidade->setCodigoEntidade($inCodEntidade);
   
    $obRTesourariaConciliacao->listarMovimentacao($rsLista);
    $obRTesourariaConciliacao->listarMovimentacaoPendente($rsListaPendencia);
    
    $obRTesourariaConciliacao->addLancamentoManual();
    $obRTesourariaConciliacao->roUltimoLancamentoManual->listar( $rsListaManual );
    $obRTesourariaConciliacao->obRTesourariaSaldoTesouraria->obRContabilidadePlanoBanco->setExercicio( Sessao::getExercicio() );
    $obRTesourariaConciliacao->obRTesourariaSaldoTesouraria->obRContabilidadePlanoBanco->setCodPlano ( $rsConciliacoes->getCampo('cod_plano')  );
    $obRTesourariaConciliacao->obRTesourariaSaldoTesouraria->consultarSaldoTesouraria( $nuSaldoTesouraria, '01/01/'.Sessao::getExercicio(), $dt_final );
    
    ///Conciliacao Movimentacoes Corrente
    $arMovimentacaoAuxSessao = array();
    $arMovimentacaoAuxSessao = ( !$rsLista->eof() ) ? $rsLista->getElementos() : array();
    sort($arMovimentacaoAuxSessao);
    unset( $rsLista );
    
    $inCount = 0;
    for ( $x = 0; $x<count($arMovimentacaoAuxSessao); $x++ ) {
        if (!$arMovimentacaoAuxSessao[$x]['conciliar']) {
            $nuSaldoContabilConciliado = bcadd( $nuSaldoContabilConciliado, $arMovimentacaoAuxSessao[$x]['vl_lancamento'], 4);
        } else {
            if (substr($arMovimentacaoAuxSessao[$x]['dt_conciliacao'],3,2) != $inMesConciliacaoCorrente) {
                $nuSaldoContabilConciliado = bcadd( $nuSaldoContabilConciliado, ($arMovimentacaoAuxSessao[$x]['vl_lancamento']), 4 );
            }
        }
    }
    
    $arMovimentacaoSessao = array();
    
    //lista pendencia
    $rsListaPendencia->addFormatacao('vl_lancamento','NUMERIC_BR');
    $rsListaPendencia->setPrimeiroElemento();
    $arMovimentacaoPendenciaAuxSessao = array();
    $arMovimentacaoPendenciaAuxSessao = ( !$rsListaPendencia->eof() ) ? $rsListaPendencia->getElementos() : array();
    
    unset( $arMovimentacaoAux );
    unset( $rsListaPendencia );
    
    $inCount = 0;
    $arPendenciaListagemAux = array();
    for ( $x = 0; $x<count( $arMovimentacaoPendenciaAuxSessao ); $x++ ) {

        # Busca da forma_conciliacao com base no que a rotina da GF faz.
        # Tipo Descrição [Tabela interna do TCEPE]
        # 1 Saldo conforme extrato bancário
        # 2 Entrada não considerada pelo banco
        # 3 Saída não considerada pela contabilidade
        # 4 Entrada não considerada pela contabilidade
        # 5 Saída não considerada pelo banco

        if (trim($arMovimentacaoPendenciaAuxSessao[$x]['ordem']) == "") {
            // tipo == C (entrada) | tipo == D (saida)
            if ($arMovimentacaoPendenciaAuxSessao[$x]['tipo_valor'] == 'C') {
                # Entrada Tesouraria = 4 Entrada não considerada pela contabilidade 
                $forma_conciliacao = '4';
            } else {
                # Saida Tesouraria = 3 Saida nao considerada pela contabilidade
                $forma_conciliacao = '3';
            }
        // se não é uma movimentacao corrente do mes passado
        } else {
            if ($arMovimentacaoPendenciaAuxSessao[$x]['vl_lancamento'] < 0) {
                # Entrada Banco = 2 Entrada não considerada pelo banco
                $forma_conciliacao = '2';
            } else {
                # Saida Banco = 5 Saída não considerada pelo banco
                $forma_conciliacao = '5'; 
            }
        }

        if (!$arMovimentacaoPendenciaAuxSessao[$x]['conciliar']) {
            $nuSaldoContabilConciliado = bcadd( $nuSaldoContabilConciliado, ($arMovimentacaoPendenciaAuxSessao[$x]['vl_lancamento']*(-1)), 4 );
        } else {
            if (substr($arMovimentacaoPendenciaAuxSessao[$x]['dt_conciliacao'],3,2) != $inMesConciliacaoCorrente) {
                $nuSaldoContabilConciliado = bcadd( $nuSaldoContabilConciliado, ($arMovimentacaoPendenciaAuxSessao[$x]['vl_lancamento']*(-1)), 4 );
            }
        }
    }

    if (empty($forma_conciliacao)) {
        $forma_conciliacao = 1;
    }

    $arMovimentacaoPendenciaSessao = ( is_array($arPendenciaAux) ) ? $arPendenciaAux : array();
    
    //Conciliacao Movimentacao manual
    $rsListaManual->addFormatacao('vl_lancamento', 'NUMERIC_BR');
    $arMovimentacaoManualSessao = array();
    $arMovimentacaoManualSessao = ( !$rsListaManual->eof() ) ? $rsListaManual->getElementos() : array();
    
    for ( $x = 0; $x<count( $arMovimentacaoManualSessao ); $x++ ) {
        $inSequencia = $arMovimentacaoManualSessao[$x]['sequencia'] - 1;
        $arMovimentacaoManualSessao[$x]['id'] = 'M'.$inSequencia;
        if ($arMovimentacaoManualSessao[$x]['conciliado'] == 't') {
            $arMovimentacaoManualSessao[$x]['conciliar'] = true;
            if (substr($arMovimentacaoManualSessao[$x]['dt_conciliacao'],3,2) != $inMesConciliacaoCorrente) {
                $nuSaldoContabilConciliado = bcsub( $nuSaldoContabilConciliado, $arMovimentacaoManualSessao[$x]['vl_lancamento'], 4 );
            }
        } else {
            $arMovimentacaoManualSessao[$x]['conciliar'] = false;
            $nuSaldoContabilConciliado = bcsub( $nuSaldoContabilConciliado, $arMovimentacaoManualSessao[$x]['vl_lancamento'], 4 );
        }
    }
            
    // Usado para incrementar a coluna sequencial
    $rsConciliacoes->setCampo( 'sequencial' , str_pad($i.$inMes.$inAno, 8, "0", STR_PAD_LEFT));
    $i++;
    
    //Soma o vl_extrato
    $arTmp['forma_conciliacao']       = $forma_conciliacao;
    $arTmp['descricao']               = $rsConciliacoes->getCampo('descricao');
    $arTmp['conta_corrente']          = $rsConciliacoes->getCampo('conta_corrente');
    $arTmp['data_fato']               = $rsConciliacoes->getCampo('data_fato');
    $arTmp['sequencial']              = $rsConciliacoes->getCampo('sequencial');
    $arTmp['nro_documento']           = $rsConciliacoes->getCampo('nro_documento');
    $arTmp['cod_tipo_conta_banco']    = $rsConciliacoes->getCampo('cod_tipo_conta_banco');
    $arTmp['tipo_documento_bancario'] = $rsConciliacoes->getCampo('tipo_documento_bancario');
    $arTmp['valor_conciliado']        = bcsub($nuSaldoTesouraria,$nuSaldoContabilConciliado,4) ;

    $arRsFinal[] = $arTmp;

    $rsConciliacoes->proximo();
}

$rsFinal = new RecordSet;
$rsFinal->preenche( $arRsFinal );

$obExportador->roUltimoArquivo->addBloco($rsFinal);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("reservado_tce");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("conta_corrente");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(12);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("sequencial");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("forma_conciliacao");//verificar valor com relacao a TABELA INTERNA 15
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("descricao");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(150);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("data_fato");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nro_documento");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(11);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("valor_conciliado");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_tipo_conta_banco");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_documento_bancario");//ver valor de acordo com a forma e o tipo de documento TABELA INTERNA 32
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("reservado_tce");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);

?>
