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
  * $Id: TransferenciaRecebida.inc.php 60199 2014-10-06 19:40:19Z jean $
  * $Date: 2014-10-06 16:40:19 -0300 (Mon, 06 Oct 2014) $
  * $Author: jean $
  * $Rev: 60199 $
  *
*/

include_once('../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php');
include_once('../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php');
include_once CAM_GPC_TCEPE_MAPEAMENTO."TTCEPETransferenciaRecebida.class.php";

$boTransacao = new Transacao();
$obTTCEPETransferenciaRecebida = new TTCEPETransferenciaRecebida();

$stDtInicialAnterior = date('d/m/Y',mktime(0,0,0,1,1,substr($stDataInicial,6,4)));
if ($stDataInicial == "01/01/".Sessao::getExercicio()) {
    $stDtFinalAnterior = date('d/m/Y',mktime(0,0,0,substr($stDataInicial,3,2),substr($stDataInicial,0,2),substr($stDataInicial,6,4)));
} else {
    $stDtFinalAnterior = date('d/m/Y',mktime(0,0,0,substr($stDataInicial,3,2),substr($stDataInicial,0,2)-1,substr($stDataInicial,6,4)));
}

$obTTCEPETransferenciaRecebida->setDado('exercicio'      , Sessao::getExercicio()  );
$obTTCEPETransferenciaRecebida->setDado('cod_entidade'   , $inCodEntidade          );
$obTTCEPETransferenciaRecebida->setDado('data_inicial'   , $stDataInicial          );
$obTTCEPETransferenciaRecebida->setDado('data_final'     , $stDataFinal            );
$obTTCEPETransferenciaRecebida->setDado('data_ant_final' , $stDtInicialAnterior    );
$obTTCEPETransferenciaRecebida->setDado('data_ant_final' , $stDtFinalAnterior      );
$obTTCEPETransferenciaRecebida->recuperaTransferenciaRecebida($rsRecordSet, "" ,"" , $boTransacao );

$obExportador->roUltimoArquivo->addBloco($rsRecordSet);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("reservado_tce");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("unidade_gestora_beneficiada");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("unidade_gestora_transferidora");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("transferencia");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_mensal");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("fonte_recursos");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("reservado_tce");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);

?>