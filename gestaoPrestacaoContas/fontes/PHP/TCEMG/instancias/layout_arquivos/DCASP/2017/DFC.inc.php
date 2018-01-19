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

include_once CAM_GPC_TCEMG_MAPEAMENTO . Sessao::getExercicio() . "/TTCEMGDemonstracaoFluxoCaixa.class.php";

$rsRecordSetDFC10 = new RecordSet();
$rsRecordSetDFC20 = new RecordSet();
$rsRecordSetDFC30 = new RecordSet();
$rsRecordSetDFC40 = new RecordSet();
$rsRecordSetDFC50 = new RecordSet();
$rsRecordSetDFC60 = new RecordSet();
$rsRecordSetDFC70 = new RecordSet();
$rsRecordSetDFC80 = new RecordSet();
$rsRecordSetDFC90 = new RecordSet();
$rsRecordSetDFC100 = new RecordSet();
$rsRecordSetDFC110 = new RecordSet();

$TTCEMGDemonstracaoFluxoCaixa = new TTCEMGDemonstracaoFluxoCaixa();
$TTCEMGDemonstracaoFluxoCaixa->setDado('exercicio', Sessao::getExercicio());
$TTCEMGDemonstracaoFluxoCaixa->setDado('entidade', $stEntidades);

//Tipo Registro 10
$TTCEMGDemonstracaoFluxoCaixa->recuperaDadosDFC10($rsRecordSetDFC10);
//Tipo Registro 20
$TTCEMGDemonstracaoFluxoCaixa->recuperaDadosDFC20($rsRecordSetDFC20);
//Tipo Registro 30
$TTCEMGDemonstracaoFluxoCaixa->recuperaDadosDFC30($rsRecordSetDFC30);
//Tipo Registro 40
$TTCEMGDemonstracaoFluxoCaixa->recuperaDadosDFC40($rsRecordSetDFC40);
//Tipo Registro 50
$TTCEMGDemonstracaoFluxoCaixa->recuperaDadosDFC50($rsRecordSetDFC50);
//Tipo Registro 60
$TTCEMGDemonstracaoFluxoCaixa->recuperaDadosDFC60($rsRecordSetDFC60);
//Tipo Registro 70
$TTCEMGDemonstracaoFluxoCaixa->recuperaDadosDFC70($rsRecordSetDFC70);
//Tipo Registro 80
$TTCEMGDemonstracaoFluxoCaixa->recuperaDadosDFC80($rsRecordSetDFC80);
//Tipo Registro 90
$TTCEMGDemonstracaoFluxoCaixa->recuperaDadosDFC90($rsRecordSetDFC90);
//Tipo Registro 100
$TTCEMGDemonstracaoFluxoCaixa->recuperaDadosDFC100($rsRecordSetDFC100);
//Tipo Registro 110
$TTCEMGDemonstracaoFluxoCaixa->recuperaDadosDFC110($rsRecordSetDFC110);

if (count($rsRecordSetDFC10->getElementos()) > 0) {
  foreach ($rsRecordSetDFC10->getElementos() as $arDFC10) {
    $inCount++;

    $rsBloco10 = 'rsBloco10_' . $inCount;
    unset($rsBloco10);
    $rsBloco10 = new RecordSet();
    $rsBloco10->preenche(array($arDFC10));

    $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
    $obExportador->roUltimoArquivo->addBloco($rsBloco10);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_receita_derivada_originaria");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_transf_corrente_recebida");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_outro_ingresso_operacional");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_tot_ingresso_atividade_operacional");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
  }
}

if (count($rsRecordSetDFC20->getElementos()) > 0) {
  foreach ($rsRecordSetDFC20->getElementos() as $arDFC20) {
    $inCount++;

    $rsBloco20 = 'rsBloco20_' . $inCount;
    unset($rsBloco20);
    $rsBloco20 = new RecordSet();
    $rsBloco20->preenche(array($arDFC20));

    $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
    $obExportador->roUltimoArquivo->addBloco($rsBloco20);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_desembolso_pessoal_demais_despesa");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_desembolso_juro_encargo_divida");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_desembolso_transferencia_concedida");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_outro_desembolso_operacional");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_tot_desembolso_atividade_operacional");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
  }
}

if (count($rsRecordSetDFC30->getElementos()) > 0) {
  foreach ($rsRecordSetDFC30->getElementos() as $arDFC30) {
    $inCount++;

    $rsBloco30 = 'rsBloco30_' . $inCount;
    unset($rsBloco30);
    $rsBloco30 = new RecordSet();
    $rsBloco30->preenche(array($arDFC30));

    $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
    $obExportador->roUltimoArquivo->addBloco($rsBloco30);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_fluxo_caixa_liq_atividade_operacional");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
  }
}

if (count($rsRecordSetDFC40->getElementos()) > 0) {
  foreach ($rsRecordSetDFC40->getElementos() as $arDFC40) {
    $inCount++;

    $rsBloco40 = 'rsBloco40_' . $inCount;
    unset($rsBloco40);
    $rsBloco40 = new RecordSet();
    $rsBloco40->preenche(array($arDFC40));

    $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
    $obExportador->roUltimoArquivo->addBloco($rsBloco40);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_alienacao_bens");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_amortizacao_empres_financ_concedido");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_outro_ingresso_investimento");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_total_ingresso_atividade_investimento");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
  }
}

if (count($rsRecordSetDFC50->getElementos()) > 0) {
  foreach ($rsRecordSetDFC50->getElementos() as $arDFC50) {
    $inCount++;

    $rsBloco50 = 'rsBloco50_' . $inCount;
    unset($rsBloco50);
    $rsBloco50 = new RecordSet();
    $rsBloco50->preenche(array($arDFC50));

    $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
    $obExportador->roUltimoArquivo->addBloco($rsBloco50);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_aquisicao_ativo_circulante");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_concessao_emprestimo_financiamento");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_outro_desembolso_investimento");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_tot_desembolso_atividade_investimento");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
  }
}

if (count($rsRecordSetDFC60->getElementos()) > 0) {
  foreach ($rsRecordSetDFC60->getElementos() as $arDFC60) {
    $inCount++;

    $rsBloco60 = 'rsBloco60_' . $inCount;
    unset($rsBloco60);
    $rsBloco60 = new RecordSet();
    $rsBloco60->preenche(array($arDFC60));

    $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
    $obExportador->roUltimoArquivo->addBloco($rsBloco60);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_fluxo_caixa_liq_atividade_investimento");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
  }
}

if (count($rsRecordSetDFC70->getElementos()) > 0) {
  foreach ($rsRecordSetDFC70->getElementos() as $arDFC70) {
    $inCount++;

    $rsBloco70 = 'rsBloco70_' . $inCount;
    unset($rsBloco70);
    $rsBloco70 = new RecordSet();
    $rsBloco70->preenche(array($arDFC70));

    $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
    $obExportador->roUltimoArquivo->addBloco($rsBloco70);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_operacao_credito");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_integra_capital_social_empresa_dependente");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_transferencia_capital_recebida");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_outro_ingresso_financiamento");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_tot_ingresso_atividade_financiamento");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
  }
}

if (count($rsRecordSetDFC80->getElementos()) > 0) {
  foreach ($rsRecordSetDFC80->getElementos() as $arDFC80) {
    $inCount++;

    $rsBloco80 = 'rsBloco80_' . $inCount;
    unset($rsBloco80);
    $rsBloco80 = new RecordSet();
    $rsBloco80->preenche(array($arDFC80));

    $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
    $obExportador->roUltimoArquivo->addBloco($rsBloco80);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_amortizacao_refinanciamento_divida");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_outro_desembolso_financiamento");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_tot_desembolso_atividade_financiamento");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
  }
}

if (count($rsRecordSetDFC90->getElementos()) > 0) {
  foreach ($rsRecordSetDFC90->getElementos() as $arDFC90) {
    $inCount++;

    $rsBloco90 = 'rsBloco90_' . $inCount;
    unset($rsBloco90);
    $rsBloco90 = new RecordSet();
    $rsBloco90->preenche(array($arDFC90));

    $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
    $obExportador->roUltimoArquivo->addBloco($rsBloco90);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_fluxo_caixa_liq_atividade_financiamento");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
  }
}

if (count($rsRecordSetDFC100->getElementos()) > 0) {
  foreach ($rsRecordSetDFC100->getElementos() as $arDFC100) {
    $inCount++;

    $rsBloco100 = 'rsBloco100_' . $inCount;
    unset($rsBloco100);
    $rsBloco100 = new RecordSet();
    $rsBloco100->preenche(array($arDFC100));

    $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
    $obExportador->roUltimoArquivo->addBloco($rsBloco100);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_geracao_liq_caixa_equivalente_caixa");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
  }
}

if (count($rsRecordSetDFC110->getElementos()) > 0) {
  foreach ($rsRecordSetDFC110->getElementos() as $arDFC110) {
    $inCount++;

    $rsBloco110 = 'rsBloco110_' . $inCount;
    unset($rsBloco110);
    $rsBloco110 = new RecordSet();
    $rsBloco110->preenche(array($arDFC110));

    $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
    $obExportador->roUltimoArquivo->addBloco($rsBloco110);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_caixa_equivalente_caixa_inicial");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_caixa_equivalente_caixa_final");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
  }
}

$TTCEMGDemonstracaoFluxoCaixa = null;
$rsRecordSetDFC10 = null;
$rsRecordSetDFC20 = null;
$rsRecordSetDFC30 = null;
$rsRecordSetDFC40 = null;
$rsRecordSetDFC50 = null;
$rsRecordSetDFC60 = null;
$rsRecordSetDFC70 = null;
$rsRecordSetDFC80 = null;
$rsRecordSetDFC90 = null;
$rsRecordSetDFC100 = null;
$rsRecordSetDFC110 = null;
