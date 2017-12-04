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

    include_once( CAM_GPC_TPB_MAPEAMENTO."TTPBConciliacaoBancaria.class.php" );
    include_once( CAM_GF_TES_NEGOCIO."RTesourariaConciliacao.class.php"                                 );

    $obTMapeamento            = new TTPBConciliacaoBancaria();
    $rsConciliacoes           = new RecordSet();

    $arRsFinal = array();

    $obTMapeamento->setDado('inMes', $inMes );
    $obTMapeamento->setDado('stEntidades', $stEntidades );
    $obTMapeamento->recuperaTodos($rsConciliacoes);

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
        $arTmp = array();
        $nuSaldoContabilConciliado = '';
        

        $obRTesourariaConciliacao = new RTesourariaConciliacao();
        $obRTesourariaConciliacao->setDataInicial   ($dt_inicial);
        $obRTesourariaConciliacao->setDataFinal     ($dt_final );
        $obRTesourariaConciliacao->obRContabilidadePlanoBanco->setExercicio(Sessao::getExercicio());
        $obRTesourariaConciliacao->obRContabilidadePlanoBanco->setCodPlano($rsConciliacoes->getCampo('cod_plano'));
        $obRTesourariaConciliacao->setMes           (intval($inMesConciliacaoCorrente));
        $obRTesourariaConciliacao->obRTesourariaAssinatura->obROrcamentoEntidade->setCodigoEntidade($stEntidades);
        $obRTesourariaConciliacao->obRTesourariaAssinatura->setExercicio(Sessao::getExercicio());
        $obRTesourariaConciliacao->obRTesourariaAssinatura->obROrcamentoEntidade->setCodigoEntidade($stEntidades);
       
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
            if (!$arMovimentacaoPendenciaAuxSessao[$x]['conciliar']) {
                $nuSaldoContabilConciliado = bcadd( $nuSaldoContabilConciliado, ($arMovimentacaoPendenciaAuxSessao[$x]['vl_lancamento']*(-1)), 4 );
            } else {
                if (substr($arMovimentacaoPendenciaAuxSessao[$x]['dt_conciliacao'],3,2) != $inMesConciliacaoCorrente) {
                    $nuSaldoContabilConciliado = bcadd( $nuSaldoContabilConciliado, ($arMovimentacaoPendenciaAuxSessao[$x]['vl_lancamento']*(-1)), 4 );
                }
            }
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
        $arTmp['forma_conciliacao'] = 1;
        $arTmp['descricao']         = "Saldo conforme extrato bancário";
        $arTmp['conta_corrente']    = $rsConciliacoes->getCampo('conta_corrente');
        $arTmp['data_fato']         = $rsConciliacoes->getCampo('data_fato');
        $arTmp['sequencial']        = $rsConciliacoes->getCampo('sequencial');
        $arTmp['nro_cheque']        = $rsConciliacoes->getCampo('nro_cheque');
        $arTmp['valor_conciliado']  = bcsub($nuSaldoTesouraria,$nuSaldoContabilConciliado,4) ;
        $arTmp['reservado_tce']     = $rsConciliacoes->getCampo('reservado_tce');
        $arTmp['cod_plano']         = $rsConciliacoes->getCampo('cod_plano');
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
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(12);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("sequencial");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("forma_conciliacao");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("descricao");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(150);
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("data_fato");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nro_cheque");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("debito_automatico");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(11);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("valor_conciliado");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("reservado_tce");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);
