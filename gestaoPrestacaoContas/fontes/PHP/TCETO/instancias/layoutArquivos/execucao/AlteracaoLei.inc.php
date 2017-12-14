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
  * $Id: AlteracaoLei.inc.php 60749 2014-11-13 11:47:47Z jean $
  * $Date: 2014-11-13 09:47:47 -0200 (Thu, 13 Nov 2014) $
  * $Author: jean $
  * $Rev: 60749 $
  *
*/

include_once CAM_GPC_TCETO_MAPEAMENTO."TTCETOAlteracaoLei.class.php";

$boTransacao = new Transacao();
$obTTCETOAlteracaoLei = new TTCETOAlteracaoLei();

$obTTCETOAlteracaoLei->setDado('exercicio'  , $inExercicio  );
$obTTCETOAlteracaoLei->setDado('entidade'   , $inCodEntidade);
$obTTCETOAlteracaoLei->setDado('dt_inicial' , $stDataInicial);
$obTTCETOAlteracaoLei->setDado('dt_final'   , $stDataFinal  );
$obTTCETOAlteracaoLei->setDado('bimestre'   , $inBimestre   );

$obTTCETOAlteracaoLei->recuperaTodos($rsRecordSet, "" ,"" , $boTransacao );

$rsRecordSet->addFormatacao('unidade_gestora', "MB_SUBSTRING(0,14)");
$rsRecordSet->addFormatacao('bimestre', "MB_SUBSTRING(0,1)");
$rsRecordSet->addFormatacao('exercicio', "MB_SUBSTRING(0,4)");
$rsRecordSet->addFormatacao('num_lei', "MB_SUBSTRING(0,20)");
$rsRecordSet->addFormatacao('dt_publicacao', "MB_SUBSTRING(0,10)");
$rsRecordSet->addFormatacao('lei', "MB_SUBSTRING(0,2)");
$rsRecordSet->addFormatacao('percentual', "MB_SUBSTRING(0,3)");

$idCount = 0;
$arResult = array();

while (!$rsRecordSet->eof()) {
    $arResult[$idCount]['idUnidadeGestora']           = $rsRecordSet->getCampo('unidade_gestora');
    $arResult[$idCount]['Bimestre']                   = $rsRecordSet->getCampo('bimestre');
    $arResult[$idCount]['Exercicio']                  = $rsRecordSet->getCampo('exercicio');
    $arResult[$idCount]['NumeroLeiAlteracao']         = $rsRecordSet->getCampo('num_lei');
    $arResult[$idCount]['DataPublicacaoLeiAlteracao'] = $rsRecordSet->getCampo('dt_publicacao');
    $arResult[$idCount]['Lei']                        = $rsRecordSet->getCampo('lei');
    $arResult[$idCount]['PercentualCréditoAdicional'] = $rsRecordSet->getCampo('percentual');
    
    $idCount++;
    
    $rsRecordSet->proximo();
}

?>