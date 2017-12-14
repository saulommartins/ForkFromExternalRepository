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

  * Layout exportação TCE-TO arquivo : 
  * Data de Criação

  * @author Analista:
  * @author Desenvolvedor: 
  *
  * @ignore
  * $Id: MovimentoContabil.inc.php 60931 2014-11-25 16:35:56Z carlos.silva $
  * $Date: 2014-11-25 14:35:56 -0200 (Tue, 25 Nov 2014) $
  * $Author: carlos.silva $
  * $Rev: 60931 $
  *
*/
include_once CAM_GPC_TCETO_MAPEAMENTO."TExportacaoMovimentoContabil.class.php";

$obTExportacaoMovimentoContabil = new TExportacaoMovimentoContabil();

$obTExportacaoMovimentoContabil->setDado('stExercicio', $inExercicio);
$obTExportacaoMovimentoContabil->setDado('inCodEntidade', $inCodEntidade);
$obTExportacaoMovimentoContabil->setDado('dtInicial', $stDataInicial);
$obTExportacaoMovimentoContabil->setDado('dtFinal', $stDataFinal);
$obTExportacaoMovimentoContabil->setDado('bimestre'      , $inBimestre           );
$obTExportacaoMovimentoContabil->recuperaDadosExportacao( $rsRecordSet );

$rsRecordSet->addFormatacao('cod_und_gestora', "MB_SUBSTRING(0,14)");
$rsRecordSet->addFormatacao('exercicio', "MB_SUBSTRING(0,4)");
$rsRecordSet->addFormatacao('sequencia', "MB_SUBSTRING(0,13)");
$rsRecordSet->addFormatacao('cod_lote', "MB_SUBSTRING(0,13)");
$rsRecordSet->addStrPad('cod_estrutural',17,'0','R');
$rsRecordSet->addFormatacao('tipo_valor', "MB_SUBSTRING(0,1)");
$rsRecordSet->addFormatacao('dt_lote', "MB_SUBSTRING(0,10)");


$idCount = 1;
$arResult = array();

while (!$rsRecordSet->eof()) {
    $arResult[$idCount]['idUnidadeGestora'] = $rsRecordSet->getCampo('cod_und_gestora');
    $arResult[$idCount]['Bimestre']= $rsRecordSet->getCampo('bimestre');
    $arResult[$idCount]['Exercicio']= $rsRecordSet->getCampo('exercicio');
    $arResult[$idCount]['NumeroRegistro'] = $idCount; 
    $arResult[$idCount]['NumeroLancamento']= $rsRecordSet->getCampo('cod_lote');
    $arResult[$idCount]['ContaContabil'] = $rsRecordSet->getCampo('cod_estrutural');
    $arResult[$idCount]['Identificador'] = $rsRecordSet->getCampo('tipo_valor');
    $arResult[$idCount]['DataLancamento']= $rsRecordSet->getCampo('dt_lote');
    $arResult[$idCount]['Valor']= $rsRecordSet->getCampo('vl_lancamento');
    $arResult[$idCount]['Historico']= $rsRecordSet->getCampo('historico');
    
    $idCount++;
    
    $rsRecordSet->proximo();
}
?>