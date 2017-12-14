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
  * Layout exportação TCE-PE arquivo : Pagamentos Restos
  * Data de Criação

  * @author Analista:
  * @author Desenvolvedor: Evandro Melos
  *
  * @ignore
  * $Id: PagamentosRestos.inc.php 60426 2014-10-21 11:54:26Z gelson $
  * $Date: 2014-10-21 09:54:26 -0200 (Tue, 21 Oct 2014) $
  * $Author: gelson $
  * $Rev: 60426 $
  *
*/
include_once CAM_GPC_TCEPE_MAPEAMENTO."TTCEPEPagamentosRestos.class.php";

$boTransacao = new Transacao();
$obTTCEPEPagamentosRestos = new TTCEPEPagamentosRestos();

$obTTCEPEPagamentosRestos->setDado('exercicio'            , Sessao::getExercicio() );
$obTTCEPEPagamentosRestos->setDado('cod_entidade'         , $inCodEntidade         );
$obTTCEPEPagamentosRestos->setDado('dt_inicial'           , $stDataInicial         );
$obTTCEPEPagamentosRestos->setDado('dt_final'             , $stDataFinal           );

if ( $inCodCompetencia < 10 ) {
    $inCodCompetencia = '0'.$inCodCompetencia;
}
$obTTCEPEPagamentosRestos->setDado('mes'                  , $inCodCompetencia       );

$obTTCEPEPagamentosRestos->recuperaTodos($rsRecordSet, "" ,"" , $boTransacao );

$arCount = array();
for($i=0;$i<$rsRecordSet->getNumLinhas();$i++){
    if(isset($arCount[$rsRecordSet->arElementos[$i]['cod_empenho']]))
        $arCount[$rsRecordSet->arElementos[$i]['cod_empenho']]++;
    else
        $arCount[$rsRecordSet->arElementos[$i]['cod_empenho']] = 1;
    
    $rsRecordSet->arElementos[$i]['num_parcela'] = $arCount[$rsRecordSet->arElementos[$i]['cod_empenho']];
}

$obExportador->roUltimoArquivo->addBloco($rsRecordSet);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("reservado_tce");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("exercicio");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("unidade_orcamentaria");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(10);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_empenho");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(10);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_parcela");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(7);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_pagamento");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("reservado_tce");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);

?>
