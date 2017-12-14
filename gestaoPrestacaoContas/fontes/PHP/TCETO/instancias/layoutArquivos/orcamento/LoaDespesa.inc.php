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

  * Layout exportação TCE-TO arquivo : LOA DESPESA
  * Data de Criação

  * @author Analista:
  * @author Desenvolvedor: 
  *
  * @ignore
  * $Id: LoaDespesa.inc.php 60895 2014-11-21 13:34:45Z arthur $
  * $Date: 2014-11-21 11:34:45 -0200 (Fri, 21 Nov 2014) $
  * $Author: arthur $
  * $Rev: 60895 $
  *
*/
include_once CAM_GPC_TCETO_MAPEAMENTO.'TTCETOLoaDespesa.class.php';
include_once CAM_GF_ORC_MAPEAMENTO.'TOrcamentoEntidade.class.php';

$rsRecordSet = new RecordSet();
$obTTCETOLoaDespesa = new TTCETOLoaDespesa();

$obTTCETOLoaDespesa->setDado('exercicio'    , Sessao::getExercicio());
$obTTCETOLoaDespesa->setDado('cod_entidade' , $inCodEntidade);
$obTTCETOLoaDespesa->recuperaDespesa($rsRecordSet);

$idCount  = 0;
$arResult = array();

while (!$rsRecordSet->eof()) {
    $arResult[$idCount]['idUnidadeGestora']      = $rsRecordSet->getCampo('cod_und_gestora');
    $arResult[$idCount]['exercicio']             = Sessao::getExercicio();
    $arResult[$idCount]['idOrgao']               = $rsRecordSet->getCampo('cod_orgao');
    $arResult[$idCount]['idUnidadeOrcamentaria'] = $rsRecordSet->getCampo('cod_unid_orcamentaria');
    $arResult[$idCount]['idFuncao']              = $rsRecordSet->getCampo('cod_funcao');
    $arResult[$idCount]['idSubFuncao']           = $rsRecordSet->getCampo('cod_subfuncao');
    $arResult[$idCount]['idPrograma']            = $rsRecordSet->getCampo('cod_programa');
    $arResult[$idCount]['idProjetoAtividade']    = $rsRecordSet->getCampo('cod_proj_atividade');
    $arResult[$idCount]['idRecursoVinculado']    = intval($rsRecordSet->getCampo('cod_rec_vinculado'));
    $arResult[$idCount]['idRubricaDespesa']      = $rsRecordSet->getCampo('rubrica_despesa');
    $arResult[$idCount]['contaContabil']         = 51110000000000000;
    $arResult[$idCount]['dotacaoInicial']        = $rsRecordSet->getCampo('dotacao_inicial');
    $arResult[$idCount]['janeiro']               = $rsRecordSet->getCampo('janeiro');
    $arResult[$idCount]['fevereiro']             = $rsRecordSet->getCampo('fevereiro');
    $arResult[$idCount]['marco']                 = $rsRecordSet->getCampo('marco');
    $arResult[$idCount]['abril']                 = $rsRecordSet->getCampo('abril');
    $arResult[$idCount]['maio']                  = $rsRecordSet->getCampo('maio');
    $arResult[$idCount]['junho']                 = $rsRecordSet->getCampo('junho');
    $arResult[$idCount]['julho']                 = $rsRecordSet->getCampo('julho');
    $arResult[$idCount]['agosto']                = $rsRecordSet->getCampo('agosto');
    $arResult[$idCount]['setembro']              = $rsRecordSet->getCampo('setembro');
    $arResult[$idCount]['outubro']               = $rsRecordSet->getCampo('outubro');
    $arResult[$idCount]['novembro']              = $rsRecordSet->getCampo('novembro');
    $arResult[$idCount]['dezembro']              = $rsRecordSet->getCampo('dezembro');
    
    $idCount++;
    
    $rsRecordSet->proximo();
}

?>