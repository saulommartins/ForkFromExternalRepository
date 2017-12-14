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
  * Layout exportação TCE-PE arquivo : 
  * Data de Criação

  * @author Analista:
  * @author Desenvolvedor:
  *
  * @ignore
  * $Id: SaldosContasCorrentes.inc.php 60658 2014-11-06 13:57:11Z evandro $
  * $Date: 2014-11-06 11:57:11 -0200 (Thu, 06 Nov 2014) $
  * $Author: evandro $
  * $Rev: 60658 $
  *
*/
include_once CAM_GPC_TCEPE_MAPEAMENTO."TTCEPESaldosContasCorrente.class.php";

$rsRecordSet = new RecordSet();

$obTTCEPESaldosContasCorrente = new TTCEPESaldosContasCorrente();

if($inCodCompetencia == 13){
   $obTTCEPESaldosContasCorrente->setDado('exercicio'   , (Sessao::getExercicio()-1)         );
   $obTTCEPESaldosContasCorrente->setDado('dt_inicial'  , '01/01/'.(Sessao::getExercicio()-1));
   $obTTCEPESaldosContasCorrente->setDado('dt_final'    , '01/12/'.(Sessao::getExercicio()-1)); 
}else{
   $obTTCEPESaldosContasCorrente->setDado('exercicio'   , Sessao::getExercicio());
   $obTTCEPESaldosContasCorrente->setDado('dt_inicial'  , $stDataInicial        );
   $obTTCEPESaldosContasCorrente->setDado('dt_final'    , $stDataFinal          );
}

$obTTCEPESaldosContasCorrente->setDado('cod_entidade', $inCodEntidade         );
$obTTCEPESaldosContasCorrente->setDado('competencia' , $inCodCompetencia      );
$obTTCEPESaldosContasCorrente->recuperaTodos($rsRecordSet, '', '', '');

$obExportador->roUltimoArquivo->addBloco($rsRecordSet);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("reservado_tce");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_conta_contabil");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(20);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_cc");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(30);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("saldo_inicial");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("natureza_saldo_inicial");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("movimento_debito");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("movimento_credito");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("saldo_final");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("natureza_saldo_final");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

//--Conforme arquivos exemplo enviados pelo Tribunal, ao gerar os arquivos de janeiro a novembro
//--os campos Movimento a débito após encerramento e Movimento a crédito após encerramento devem ficar em branco
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("movimento_debito_encerramento");
if( ($inCodCompetencia == 13) || ($inCodCompetencia == 12) )
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
else
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("movimento_credito_encerramento");
if( ($inCodCompetencia == 13) || ($inCodCompetencia == 12) )
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
else
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("reservado_tce");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);
?>