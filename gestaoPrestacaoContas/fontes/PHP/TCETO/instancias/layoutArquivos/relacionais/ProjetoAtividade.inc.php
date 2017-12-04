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
  * $Id: ProjetoAtividade.inc.php 60766 2014-11-13 19:12:27Z diogo.zarpelon $
  * $Date: 2014-11-13 17:12:27 -0200 (Thu, 13 Nov 2014) $
  * $Author: diogo.zarpelon $
  * $Rev: 60766 $
  *
*/
include_once CAM_GPC_TCETO_MAPEAMENTO.'TTCETOProjetoAtividade.class.php';
include_once CAM_GF_ORC_MAPEAMENTO.'TOrcamentoEntidade.class.php';

$rsRecordSet = new RecordSet();

$obTTCETOProjetoAtividade = new TTCETOProjetoAtividade();
$obTTCETOProjetoAtividade->setDado('exercicio'    , Sessao::getExercicio() );
$obTTCETOProjetoAtividade->setDado('cod_entidade' , $inCodEntidade );
$obTTCETOProjetoAtividade->setDado('bimestre'     , $inBimestre    );
$obTTCETOProjetoAtividade->recuperaProjetoAtividade($rsRecordSet);

$rsRecordSet->addFormatacao('nome', 'MB_SUBSTRING(0,100)');

$idCount = 0;
$arResult = array();

while (!$rsRecordSet->eof()) {
    $arResult[$idCount]['idUnidadeGestora']   = $rsRecordSet->getCampo('cod_und_gestora');
    $arResult[$idCount]['bimestre']           = $rsRecordSet->getCampo('bimestre');
    $arResult[$idCount]['exercicio']          = $rsRecordSet->getCampo('exercicio');
    $arResult[$idCount]['idProjetoAtividade'] = $rsRecordSet->getCampo('cod_proj_atividade');
    $arResult[$idCount]['identificador']      = $rsRecordSet->getCampo('identificador');
    $arResult[$idCount]['nome']               = $rsRecordSet->getCampo('nome');
    
    $idCount++;
    
    $rsRecordSet->proximo();
}

unset($UndGestora, $obTOrcamentoEntidade, $obTTCETOProjetoAtividade); 

?>