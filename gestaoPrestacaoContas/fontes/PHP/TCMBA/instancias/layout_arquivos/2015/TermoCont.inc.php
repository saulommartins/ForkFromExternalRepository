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

    include_once( CAM_GPC_TCMBA_MAPEAMENTO.Sessao::getExercicio()."/TTCMBATermoCont.class.php" );
    $obTTBATermoCont = new TTCMBATermoCont();
    $obTTBATermoCont->setDado('inMes'      , $inMes );
    $obTTBATermoCont->setDado('stEntidades', $stEntidades );
    $obTTBATermoCont->setDado('inCodUnidadeGestora', $inCodUnidadeGestora );
    $obTTBATermoCont->setDado('stDataInicial', $stDataInicial );
    $obTTBATermoCont->setDado('stDataFinal', $stDataFinal );
    $obTTBATermoCont->recuperaDadosTribunal($rsArquivo);

    $obExportador->roUltimoArquivo->addBloco($rsArquivo);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro"); 
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("unidade_gestora");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_contrato");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("ALFANUMERICO_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_termo");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("ALFANUMERICO_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(36);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("documento");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("ALFANUMERICO_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(20);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_pessoa");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_termo");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nom_responsavel");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("ALFANUMERICO_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(50);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_publicacao");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_termo");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("justificativa");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("ALFANUMERICO_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(100);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("competencia");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("objeto");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("ALFANUMERICO_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(100);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("fundamentacao_legal");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("ALFANUMERICO_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(75);
?>