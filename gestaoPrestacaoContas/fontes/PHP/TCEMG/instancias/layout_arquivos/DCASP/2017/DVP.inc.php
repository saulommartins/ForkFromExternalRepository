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

include_once CAM_GPC_TCEMG_MAPEAMENTO . Sessao::getExercicio() . "/TTCEMGDemonstracaoVariacaoPatrimonial.class.php";

$rsRecordSetBF10 = new RecordSet();
$rsRecordSetBF20 = new RecordSet();

$TTCEMGDemonstracaoVariacaoPatrimonial = new TTCEMGDemonstracaoVariacaoPatrimonial();
$TTCEMGDemonstracaoVariacaoPatrimonial->setDado('exercicio', Sessao::getExercicio());
$TTCEMGDemonstracaoVariacaoPatrimonial->setDado('entidade', $stEntidades);

//Tipo Registro 10
$TTCEMGDemonstracaoVariacaoPatrimonial->recuperaDadosDVP10($rsRecordSetDVP10);
//Tipo Registro 20
$TTCEMGDemonstracaoVariacaoPatrimonial->recuperaDadosDVP20($rsRecordSetDVP20);
//Tipo Registro 30
$TTCEMGDemonstracaoVariacaoPatrimonial->recuperaDadosDVP30($rsRecordSetDVP30);

if (count($rsRecordSetDVP10->getElementos()) > 0) {
  foreach ($rsRecordSetDVP10->getElementos() as $arDVP10) {
    $inCount++;

    $rsBloco10 = 'rsBloco10_' . $inCount;
    unset($rsBloco10);
    $rsBloco10 = new RecordSet();
    $rsBloco10->preenche(array($arDVP10));

    $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
    $obExportador->roUltimoArquivo->addBloco($rsBloco10);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_impostos");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_contribuicoes");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_exploracao_vendas_bens_serv_direitos");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_variacoes_aumentativas_financeiras");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_transf_delegacoes_recebidas");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_valoriz_ativo_desincorporacao_passivos");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_outras_variacoes_patrim_aumentativas");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_total_variacoes_patrimon_aumentativas");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
  }
}

if (count($rsRecordSetDVP20->getElementos()) > 0) {
  foreach ($rsRecordSetDVP20->getElementos() as $arDVP20) {
    $inCount++;

    $rsBloco20 = 'rsBloco20_' . $inCount;
    unset($rsBloco20);
    $rsBloco20 = new RecordSet();
    $rsBloco20->preenche(array($arDVP20));

    $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
    $obExportador->roUltimoArquivo->addBloco($rsBloco20);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_variacao_patrim_diminu_pessoal_encargos");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_beneficio_previdenciario_assistencial");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_uso_bens_servicos_consumo_capital_fixo");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_varia_patri_diminutiva_financeira");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_transferencia_delegacoes_concedidas");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_desvalo_perdas_ativ_incorporacao_passivos");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_varia_patrimonial_diminutiva_tributaria");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_custo_mercado_produ_vendido_servi_prestado");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_outras_varia_patrim_diminutiva");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_total_variacoes_patrimoniais");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
  }
}

if (count($rsRecordSetDVP30->getElementos()) > 0) {
  foreach ($rsRecordSetDVP30->getElementos() as $arDVP30) {
    $inCount++;

    $rsBloco30 = 'rsBloco30_' . $inCount;
    unset($rsBloco30);
    $rsBloco30 = new RecordSet();
    $rsBloco30->preenche(array($arDVP30));

    $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
    $obExportador->roUltimoArquivo->addBloco($rsBloco30);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_resultado_patrimonial_periodo");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
  }
}

$TTCEMGDemonstracaoVariacaoPatrimonial = null;
$rsRecordSetDVP10 = null;
$rsRecordSetDVP20 = null;
$rsRecordSetDVP30 = null;
