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

    include_once( CAM_GPC_TCEAM_MAPEAMENTO."TTCEAMConciliacaoBancaria.class.php" );
    include_once( CAM_GF_TES_MAPEAMENTO."TTesourariaConciliacao.class.php"   );

    $obTMapeamento            = new TTCEAMConciliacaoBancaria();
    $obTTesourariaConciliacao = new TTesourariaConciliacao();

    $rsConciliacoes = new RecordSet();

    $arRsFinal = array();

    $obTMapeamento->setDado('inMes', $inMes );
    $obTMapeamento->setDado('stEntidades', $stEntidades );
    $obTMapeamento->recuperaTodos($rsConciliacoes);

    //$obTMapeamento->debug();

    $stDataInicial = date('d/m/Y',strtotime(Sessao::getExercicio()."-$inMes-1 +1month")      );
    $stDataFinal   = date('d/m/Y',strtotime(Sessao::getExercicio()."-$inMes-1 -1day +2month"));

    // Incrementa o mês para trazer os dados da Tesouraria.
    $inMes = ($inMes == 12) ? 01 : str_pad($inMes+1, 2, "0", STR_PAD_LEFT);
    $inAno = substr(Sessao::getExercicio(), 2, 4);
    // Usado para incrementar o nro sequencial passado ao arquivo.
    $i = 1;

    while (!$rsConciliacoes->eof()) {
        // Listagem das movimentações pendentes.
        $arTmp = array();
        $inSequencial = 1;

        $stFiltro = " exercicio = \'".Sessao::getExercicio()."\' AND ";
        $stFiltroArrecadacao = " AND TB.exercicio = \'".Sessao::getExercicio()."\' ";
        $obTTesourariaConciliacao->setDado( 'exercicio', Sessao::getExercicio() );

        $stFiltro .= " dt_lancamento < TO_DATE( \'".$stDataInicial."\', \'dd/mm/yyyy\' ) AND ";
        $stFiltroArrecadacao .= " AND TB.dt_boletim <= TO_DATE( \'".$stDataInicial."\', \'dd/mm/yyyy\' ) ";

        $obTTesourariaConciliacao->setDado('stDtInicial', $stDataInicial );

        //$rsConciliacoes->setCampo('cod_plano',2975);
        $stFiltro .= " cod_plano = ".$rsConciliacoes->getCampo('cod_plano')." AND ";
        $obTTesourariaConciliacao->setDado('inCodPlano', $rsConciliacoes->getCampo('cod_plano') );

        $obTTesourariaConciliacao->setDado('inCodEntidade', $stEntidades );
        $obTTesourariaConciliacao->setDado('inMes' , $inMes);
        $stFiltro .= " (mes = \'".$inMes."\' OR conciliar != \'true\') AND ";

        $obTTesourariaConciliacao->setDado( "stFiltroArrecadacao", $stFiltroArrecadacao );
        $stFiltro = ( $stFiltro ) ? " WHERE ".substr( $stFiltro, 0, strlen( $stFiltro )-4 )." " : '';

        //recupera o saldo da tesouraria
        include_once( CAM_GF_TES_MAPEAMENTO."FTesourariaSaldoContaTesouraria.class.php" );
        $obFTesourariaSaldoContaTesouraria = new FTesourariaSaldoContaTesouraria();

        $obFTesourariaSaldoContaTesouraria->setDado( "exercicio" , Sessao::getExercicio() );
        $obFTesourariaSaldoContaTesouraria->setDado( "cod_plano" , $rsConciliacoes->getCampo('cod_plano')  );
        $obFTesourariaSaldoContaTesouraria->setDado( "dt_inicial", $stDataInicial );
        $obFTesourariaSaldoContaTesouraria->setDado( "dt_final"  , $stDataFinal   );
        $obErro = $obFTesourariaSaldoContaTesouraria->recuperaRelacionamento( $rsSaldo );
        $nuVlSaldo = $rsSaldo->getCampo( "valor" );

        $rsMovimentacaoPendente = new RecordSet();

        $obTTesourariaConciliacao->setDado('stFiltro', $stFiltro);
        $stFiltro = '';
        $obErro = $obTTesourariaConciliacao->recuperaMovimentacaoPendente( $rsMovimentacaoPendente, $stFiltro, $stOrder, $boTransacao );

        if (!$rsMovimentacaoPendente->eof()) {
            while (!$rsMovimentacaoPendente->eof()) {

                // Lançamentos Automáicos (Aba movimentação corrente)
                if ($rsMovimentacaoPendente->getCampo('tipo_movimentacao') == 'A') {
                    //correcao para trazer a forma_conciliacao certa para arr
                    if ($rsMovimentacaoPendente->getCampo('tipo') == 'A' && $rsMovimentacaoPendente->getCampo('tipo_valor') == 'D') {
                        $rsMovimentacaoPendente->setCampo('vl_original', $rsMovimentacaoPendente->getCampo('vl_original')*(-1));
                    }

                    if ($rsMovimentacaoPendente->getCampo('vl_original') > 0) {
                        // Tipo Conciliação = 2
                        $arTmp['forma_conciliacao'] = 2;
                    } elseif ($rsMovimentacaoPendente->getCampo('vl_original') < 0) {
                        // Tipo Conciliação = 5
                        $arTmp['forma_conciliacao'] = 5;
                    }
               } else {
                    // Lançamentos Manuais (Aba novas movimentações)
                    if ($rsMovimentacaoPendente->getCampo('tipo_movimentacao') == 'M') {
                        if ($rsMovimentacaoPendente->getCampo('vl_original') < 0) {
                            // Tipo Conciliação = 3
                            $arTmp['forma_conciliacao'] = 3;
                        } elseif ($rsMovimentacaoPendente->getCampo('vl_original') > 0) {
                            // Tipo Conciliação = 4
                            $arTmp['forma_conciliacao'] = 4;
                        }
                    }
                }

                // Usado para incrementar a coluna sequencial
                $rsConciliacoes->setCampo( 'sequencial' , $inSequencial);
//                $rsConciliacoes->setCampo( 'sequencial' , str_pad($i.$inMes.$inAno, 8, "0", STR_PAD_LEFT));
                $i++;

                //codigo da conciliacao eh o forma_conciliacao
                $arTmp['exercicio_conciliacao']    = $rsConciliacoes->getCampo('exercicio_conta');
                $arTmp['codigo_conta']    = str_replace('.', '', $rsConciliacoes->getCampo('codigo_conta'));
                $arTmp['sequencial']    = $rsConciliacoes->getCampo('sequencial');
                $arTmp['descricao']    = $rsMovimentacaoPendente->getCampo('descricao');;
                $arTmp['valor_conciliado']    = abs($rsMovimentacaoPendente->getCampo('vl_lancamento'));
                $arTmp['data_conciliacao']    = $rsMovimentacaoPendente->getCampo('dt_lancamento');

                $arRsFinal[] = $arTmp;

                $rsMovimentacaoPendente->proximo();
                $inSequencial++;
            }
        } else {
                // Usado para incrementar a coluna sequencial
                $rsConciliacoes->setCampo( 'sequencial' , $inSequencial);
//                $rsConciliacoes->setCampo( 'sequencial' , str_pad($i.$inMes.$inAno, 8, "0", STR_PAD_LEFT));
                $i++;

                $arTmp['forma_conciliacao'] = 1;
                $arTmp['exercicio_conciliacao']    = $rsConciliacoes->getCampo('exercicio_conta');
                $arTmp['codigo_conta']    = str_replace('.', '', $rsConciliacoes->getCampo('codigo_conta'));
                $arTmp['sequencial']    = $rsConciliacoes->getCampo('sequencial');
                $arTmp['descricao']    = "Saldo conforme extrato bancário";
//                $arTmp['valor_conciliado']    = abs($nuVlSaldo);
                $arTmp['valor_conciliado']    = abs($rsConciliacoes->getCampo('valor_conciliado'));
                $arTmp['data_conciliacao']    = $rsConciliacoes->getCampo('data_fato');

                $arRsFinal[] = $arTmp;
        }
        $rsConciliacoes->proximo();
    }

    //criando as colunas do arquivo
    $rsFinal = new RecordSet;
    $rsFinal->preenche( $arRsFinal );

    $obExportador->roUltimoArquivo->addBloco($rsFinal);
    $obExportador->roUltimoArquivo->setTipoDocumento('TCE_AM');
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("reservado_tce");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("exercicio_conciliacao");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codigo_conta");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(34);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("sequencial");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("descricao");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(30);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("valor_conciliado");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("forma_conciliacao");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("data_conciliacao");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_YYYYMMDD");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
