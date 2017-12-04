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
    * Página de Include Oculta - Exportação Arquivos

    * Data de Criação   : 22/03/2011

    * @author Desenvolvedor: Matheus Figueredo
    * @ignore
*/
    include_once( CAM_GPC_TCEAM_MAPEAMENTO."TTCEAMPagamento.class.php" );
    $obTMapeamento = new TTCEAMPagamento();
    $obTMapeamento->setDado('exercicio'  , Sessao::getExercicio() );
    $obTMapeamento->setDado('inMes'      , $inMes );
    $obTMapeamento->setDado('stEntidades', $stEntidades );
    if ($boIncorporarEmpenhos) {
        $obTMapeamento->setDado('boIncorporarEmpenhos', $boIncorporarEmpenhos );
        $obTMapeamento->setDado('stCodEntidadesIncorporadas', $stCodEntidadesIncorporadas );
        $obTMapeamento->setDado('stEntidades', $inCodEntidadePrefeitura );
    }
    $obTMapeamento->recuperaPagamentos($arRecordSet[$stArquivo]);

    $boAdd = false;
    $stChave = '';
    $arTmp = array();
    foreach ($arRecordSet[$stArquivo]->arElementos as $registros) {
        if ($registros['cod_empenho_incorporado'] > 0) {
            if ($stChave == $registros['cod_empenho_incorporado'].$registros['ano'].$registros['cod_unidade_orcamentaria'].$registros['cod_banco_pagamento'].$registros['cod_agencia_pagamento'].$registros['conta_pagamento'].$registros['entidade_c1']) {
                $boAdd = false;
                if ($registros['cod_banco_pagamento2'] != $arTmp[count($arTmp)-1]['cod_banco_pagamento2'] && !empty($registros['cod_banco_pagamento2']) && !empty($arTmp[count($arTmp)-1]['cod_banco_pagamento2'])) {
                    $boAdd = true;
                    $arTmp[] = $registros;
                }

                if (!$boAdd) {
                    if ($registros['cod_banco_pagamento3'] != $arTmp[count($arTmp)-1]['cod_banco_pagamento3'] && !empty($registros['cod_banco_pagamento3']) && !empty($arTmp[count($arTmp)-1]['cod_banco_pagamento3'])) {
                        $boAdd = true;
                        $arTmp[] = $registros;
                    }

                    if (!$boAdd) {
                        if ( ($registros['cod_banco_pagamento2'] == $arTmp[count($arTmp)-1]['cod_banco_pagamento2']) || (!empty($registros['cod_banco_pagamento2']) && empty($arTmp[count($arTmp)-1]['cod_banco_pagamento2']))) {
                            $arTmp[count($arTmp)-1]['cod_banco_pagamento2']   = $registros['cod_banco_pagamento2'];
                            $arTmp[count($arTmp)-1]['cod_agencia_pagamento2'] = $registros['cod_agencia_pagamento2'];
                            $arTmp[count($arTmp)-1]['conta_pagamento2']       = $registros['conta_pagamento2'];
                            $arTmp[count($arTmp)-1]['entidade_c2']            = $registros['entidade_c2'];
                        }
                        if ( ($registros['cod_banco_pagamento3'] == $arTmp[count($arTmp)-1]['cod_banco_pagamento3']) || (!empty($registros['cod_banco_pagamento3']) && empty($arTmp[count($arTmp)-1]['cod_banco_pagamento3']))) {
                            $arTmp[count($arTmp)-1]['cod_banco_pagamento3']   = $registros['cod_banco_pagamento3'];
                            $arTmp[count($arTmp)-1]['cod_agencia_pagamento3'] = $registros['cod_agencia_pagamento3'];
                            $arTmp[count($arTmp)-1]['conta_pagamento3']       = $registros['conta_pagamento3'];
                            $arTmp[count($arTmp)-1]['entidade_c3']            = $registros['entidade_c3'];
                        }
                        $arTmp[count($arTmp)-1]['valor_pagamento'] = number_format(((float) $arTmp[count($arTmp)-1]['valor_pagamento'] + (float) $registros['valor_pagamento']), 2, '.', '');
                    }
                }
            } else {
                $arTmp[] = $registros;
            }
            $stChave = $registros['cod_empenho_incorporado'].$registros['ano'].$registros['cod_unidade_orcamentaria'].$registros['cod_banco_pagamento'].$registros['cod_agencia_pagamento'].$registros['conta_pagamento'].$registros['entidade_c1'];

        } else {
            $arTmp[] = $registros;
        }
    }
    $arRecordSet[$stArquivo] = new RecordSet;
    $arRecordSet[$stArquivo]->preenche($arTmp);

    $obExportador->roUltimoArquivo->addBloco($arRecordSet[$stArquivo]);
    $obExportador->roUltimoArquivo->setTipoDocumento('TCE_AM');
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("reservado_tce");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(5);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("ano");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_unidade_orcamentaria");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_empenho");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(10);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("valor_pagamento");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("data_pagamento_vencimento");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_banco_pagamento");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_agencia_pagamento");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("conta_pagamento");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(10);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_banco_pagamento2");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_agencia_pagamento2");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("conta_pagamento2");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(10);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_banco_pagamento3");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_agencia_pagamento3");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("conta_pagamento3");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(10);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_banco_recebimento");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_agencia_recebimento");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("conta_recebimento");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(10);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("data_exigibilidade");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("data_justificativa");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
