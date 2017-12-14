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
  * Layout exportação TCE-PE arquivo : ItemPagamento
  * Data de Criação

  * @author Analista:
  * @author Desenvolvedor: Jean Silva
  *
  * @ignore
  * $Id: ItemPagamento.inc.php 60525 2014-10-27 13:14:49Z michel $
  * $Date: 2014-10-27 11:14:49 -0200 (Mon, 27 Oct 2014) $
  * $Author: michel $
  * $Rev: 60525 $
  *
*/

include_once('../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php');
include_once('../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php');
include_once CAM_GPC_TCEPE_MAPEAMENTO."TTCEPEItemPagamento.class.php";

$boTransacao = new Transacao();
$rsRecordSet = new RecordSet();
$obTTCEPEItemPagamento = new TTCEPEItemPagamento();

$obTTCEPEItemPagamento->setDado('exercicio'     , Sessao::getExercicio()  );
$obTTCEPEItemPagamento->setDado('stDataInicial' , $stDataInicial  );
$obTTCEPEItemPagamento->setDado('stDataFinal'   , $stDataFinal  );
$obTTCEPEItemPagamento->setDado('stEntidade'    , $inCodEntidade  );
if ($inCodCompetencia < 10) {
  $inCodCompetencia = '0'.$inCodCompetencia;
}
$obTTCEPEItemPagamento->setDado('mes'           , $inCodCompetencia       );
$obTTCEPEItemPagamento->recuperaItemPagamento($rsRecordSet, "" ,"" , $boTransacao );

$obExportador->roUltimoArquivo->addBloco($rsRecordSet);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("reservado_tce");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("ano_empenho");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("unidade_orcamentaria");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(10);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_empenho");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(7);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_parcela");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(7);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_pagamento");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_banco_debito");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(12);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_cheque");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CHARACTER_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("doc_debito");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CHARACTER_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(11);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_febraban");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CHARACTER_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_agencia");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CHARACTER_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_banco_credito");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CHARACTER_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(12);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("fonte_recursos");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_conta");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_sequencial");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(7);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("reservado_tce");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);

?>