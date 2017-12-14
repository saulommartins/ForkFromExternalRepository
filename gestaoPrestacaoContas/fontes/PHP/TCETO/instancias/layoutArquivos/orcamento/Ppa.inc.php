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
  * $Id: Ppa.inc.php 60952 2014-11-26 11:59:26Z michel $
  * $Date: 2014-11-26 09:59:26 -0200 (Wed, 26 Nov 2014) $
  * $Author: michel $
  * $Rev: 60952 $
  *
*/

include_once CAM_GPC_TCETO_MAPEAMENTO."TTCETOPPA.class.php";

$boTransacao = new Transacao();
$obTTCETOPPA = new TTCETOPPA();

$obTTCETOPPA->setDado('exercicio'            , Sessao::getExercicio() );
$obTTCETOPPA->setDado('cod_entidade'         , $inCodEntidade         );
$obTTCETOPPA->setDado('dt_inicial'           , $stDataInicial         );
$obTTCETOPPA->setDado('dt_final'             , $stDataFinal           );
$obTTCETOPPA->setDado('unidade_gestora'      , $stUnidadeGestora      );

$obTTCETOPPA->recuperaTodos($rsRecordSet, "" ,"" , $boTransacao );

$idCount = 0;
$arResult = array();

while (!$rsRecordSet->eof()) {
    $arResult[$idCount]['idUnidadeGestora']         = $rsRecordSet->getCampo('cod_und_gestora');
    $arResult[$idCount]['exercicio']                = $rsRecordSet->getCampo('exercicio');
    $arResult[$idCount]['idOrgao']                  = $rsRecordSet->getCampo('cod_orgao');
    $arResult[$idCount]['idUnidadeOrcamentaria']    = $rsRecordSet->getCampo('cod_und_orcamentaria');
    $arResult[$idCount]['idPrograma']               = $rsRecordSet->getCampo('cod_programa');
    $arResult[$idCount]['idProjetoAtividade']       = intval($rsRecordSet->getCampo('cod_proj_atividade'));
    $arResult[$idCount]['metaFisica1Ano']           = $rsRecordSet->getCampo('meta_fisica_1ano');
    $arResult[$idCount]['metaFisica2Ano']           = $rsRecordSet->getCampo('meta_fisica_2ano');
    $arResult[$idCount]['metaFisica3Ano']           = $rsRecordSet->getCampo('meta_fisica_3ano');
    $arResult[$idCount]['metaFisica4Ano']           = $rsRecordSet->getCampo('meta_fisica_4ano');
    $arResult[$idCount]['metaFisicaTotal']          = $rsRecordSet->getCampo('meta_fisica_total');
    $arResult[$idCount]['metaFinanceira1Ano']       = $rsRecordSet->getCampo('meta_financeira_1ano');
    $arResult[$idCount]['metaFinanceira2Ano']       = $rsRecordSet->getCampo('meta_financeira_2ano');
    $arResult[$idCount]['metaFinanceira3Ano']       = $rsRecordSet->getCampo('meta_financeira_3ano');
    $arResult[$idCount]['metaFinanceira4Ano']       = $rsRecordSet->getCampo('meta_financeira_4ano');
    $arResult[$idCount]['metaFinanceiraTotal']      = $rsRecordSet->getCampo('meta_financeira_total');
    $arResult[$idCount]['unidadeMedida']            = $rsRecordSet->getCampo('cod_unidade_medida');

    $idCount++;
    
    $rsRecordSet->proximo();
}

?>