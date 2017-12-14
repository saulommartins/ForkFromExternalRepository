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
  * $Id: LeiOrcamentaria.inc.php 60685 2014-11-07 20:04:16Z evandro $
  * $Date: 2014-11-07 18:04:16 -0200 (Fri, 07 Nov 2014) $
  * $Author: evandro $
  * $Rev: 60685 $
  *
*/
include_once CAM_GRH_ENT_MAPEAMENTO.'TEntidade.class.php';
include_once CAM_GPC_TCETO_MAPEAMENTO.'TTCETOLeiOrcamentaria.class.php';

$obTTCETOLeiOrcamentaria = new TTCETOLeiOrcamentaria();
$obTTCETOLeiOrcamentaria->setDado('exercicio', Sessao::getExercicio());
$obTTCETOLeiOrcamentaria->setDado('cod_entidade', $inCodEntidade );
    
$obTTCETOLeiOrcamentaria->listarExportacaoLeiOrcamentaria($rsRecordSet);

$stNomeArquivo ="LeiOrcamentaria";
$arResult = array();
$idCount = 0;

$rsRecordSet->addFormatacao("complementacao","MB_SUBSTRING(0,255)");

while (!$rsRecordSet->eof()) {
    $arResult[$idCount]['idUnidadeGestora']              = $rsRecordSet->getCampo('cod_und_gestora'); 
    $arResult[$idCount]['exercicio']                     = $rsRecordSet->getCampo('exercicio');
    $arResult[$idCount]['complementacao']                = $rsRecordSet->getCampo('complementacao'); 
    $arResult[$idCount]['numeroLeiPpa']                  = $rsRecordSet->getCampo('num_lei_ppa');
    $arResult[$idCount]['dataLeiPpa']                    = $rsRecordSet->getCampo('data_lei_ppa');
    $arResult[$idCount]['numeroLdo']                     = $rsRecordSet->getCampo('num_ldo');
    $arResult[$idCount]['dataLdo']                       = $rsRecordSet->getCampo('data_ldo');
    $arResult[$idCount]['dataPublicacaoLdo']             = $rsRecordSet->getCampo('data_pub_ldo');
    $arResult[$idCount]['numeroLoa']                     = $rsRecordSet->getCampo('num_loa');
    $arResult[$idCount]['dataLoa']                       = $rsRecordSet->getCampo('data_loa');
    $arResult[$idCount]['dataPublicacaoLoa']             = $rsRecordSet->getCampo('data_pub_loa');        
    $arResult[$idCount]['percentualCreditoAdicional']    = $rsRecordSet->getCampo('perc_credito_adicional');
    $arResult[$idCount]['percentualCreditoAntecipacao']  = $rsRecordSet->getCampo('perc_op_credito_antecipacao');
    $arResult[$idCount]['percentualCreditoInterno']      = $rsRecordSet->getCampo('perc_op_credito_interno');
    $arResult[$idCount]['percentualCreditoExterno']      = $rsRecordSet->getCampo('perc_op_credito_externo');
    $arResult[$idCount]['numeroLeiAlteracaoPpa']         = $rsRecordSet->getCampo('num_lei_alteracao_ppa');         
    $arResult[$idCount]['dataPublicacaoLeiAlteracaoPpa'] = $rsRecordSet->getCampo('data_pub_lei_alteracao_ppa');         
    $arResult[$idCount]['dataPublicacaoLeiPpa']          = $rsRecordSet->getCampo('data_pub_lei_ppa');
        
    $idCount++;

    $rsRecordSet->proximo();
}
return $arResult;

?>

