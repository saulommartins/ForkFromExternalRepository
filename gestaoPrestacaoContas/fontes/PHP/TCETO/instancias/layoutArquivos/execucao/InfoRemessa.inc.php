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
  * Data de Criação: 10/11/2014

  * @author Analista: Silvia Martins
  * @author Desenvolvedor: Evandro Melos
  *
  * @ignore
  * $Id: InfoRemessa.inc.php 60843 2014-11-18 16:43:39Z carlos.silva $
  * $Date: 2014-11-18 14:43:39 -0200 (Tue, 18 Nov 2014) $
  * $Author: carlos.silva $
  * $Rev: 60843 $
  *
*/

include_once CAM_GPC_TCETO_MAPEAMENTO.'TTCETOInfoRemessa.class.php';

$obTTCETOInfoRemessa = new TTCETOInfoRemessa();

$obTTCETOInfoRemessa->setDado('stExercicio'  , Sessao::getExercicio());
$obTTCETOInfoRemessa->setDado('inCodEntidade', $inCodEntidade );
$obTTCETOInfoRemessa->setDado('inBimestre'   , $inBimestre );
$obTTCETOInfoRemessa->listarExportacaoInfoRemessa($rsRecordSet);

$stNomeArquivo ="InfoRemessa";
$arResult = array();
$idCount = 0;
$stDataInicial = SistemaLegado::dataToSql($stDataInicial);
$stDataFinal = SistemaLegado::dataToSql($stDataFinal);

while (!$rsRecordSet->eof()) {
    $arResult[$idCount]['idUnidadeGestora'] = $rsRecordSet->getCampo('cod_und_gestora');        
    $arResult[$idCount]['bimestre']         = $inBimestre;
    $arResult[$idCount]['exercicio']        = Sessao::getExercicio();
    $arResult[$idCount]['dataInicio']       = $stDataInicial;
    $arResult[$idCount]['dataFim']          = $stDataFinal;
    $arResult[$idCount]['dataGeracao']      = date('Y-m-d');
    $arResult[$idCount]['sistema']          = '09';
    $idCount++;
    $rsRecordSet->proximo();
}
   
return $arResult;

?>
