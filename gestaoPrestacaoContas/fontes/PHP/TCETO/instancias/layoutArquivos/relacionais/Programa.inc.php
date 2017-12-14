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

  * Layout exportação TCE-TO arquivo : Programa.xml
  * Data de Criação

  * @author Analista:
  * @author Desenvolvedor: 
  *
  * @ignore
  * $Id: Programa.inc.php 60871 2014-11-19 19:18:36Z evandro $
  * $Date: 2014-11-19 17:18:36 -0200 (Wed, 19 Nov 2014) $
  * $Author: evandro $
  * $Rev: 60871 $
  *
*/
include_once CAM_GPC_TCETO_MAPEAMENTO.'TTCETOPrograma.class.php';

$rsRecordSet = new RecordSet();

$obTTCETOPrograma = new TTCETOPrograma();
$obTTCETOPrograma->setDado('exercicio'   ,$inExercicio   );
$obTTCETOPrograma->setDado('cod_entidade',$inCodEntidade );
$obTTCETOPrograma->recuperaPrograma($rsRecordSet, $stCondicao, $stOrdem, $boTransacao);

$idCount=0;
$arResult = array();
$rsRecordSet->addFormatacao('nome'       , 'MB_SUBSTRING(0,100)');
$rsRecordSet->addFormatacao('objetivo'   , 'MB_SUBSTRING(0,255)');
$rsRecordSet->addFormatacao('publicoAlvo', 'MB_SUBSTRING(0,255)');

while (!$rsRecordSet->eof()) {
    
    $arResult[$idCount]['idUnidadeGestora'] = $rsRecordSet->getCampo('cod_und_gestora');
    $arResult[$idCount]['bimestre']         = $inBimestre;
    $arResult[$idCount]['exercicio']        = $rsRecordSet->getCampo('exercicio');
    $arResult[$idCount]['idPrograma']       = $rsRecordSet->getCampo('cod_programa');
    $arResult[$idCount]['nome']             = $rsRecordSet->getCampo('nome');
    $arResult[$idCount]['objetivo']         = $rsRecordSet->getCampo('objetivo');
    $arResult[$idCount]['publicoAlvo']      = $rsRecordSet->getCampo('publico_alvo');
    
    $idCount++;
    
    $rsRecordSet->proximo();
}

unset($obTTCETOPrograma);
?>