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

    * Página de Include Oculta - Exportação Arquivos Relacionais - LeisOrcamentarias.xml
    *
    * Data de Criação: 25/06/2014
    *
    * @author: Evandro Melos
    *
    * $Id: leisOrcamentarias.inc.php 64807 2016-04-04 21:11:31Z carlos.silva $
    *
    * @ignore
    *
*/

include_once CAM_GRH_ENT_MAPEAMENTO.'TEntidade.class.php';
include_once CAM_GPC_TCEAL_MAPEAMENTO.'TTCEALLeisOrcamentarias.class.php';
include_once CAM_GPC_TCEAL_MAPEAMENTO.'TExportacaoRelacionais.class.php';

$undGestora = explode(',', $stEntidades);
$obTTCEALLeisOrcamentarias = new TTCEALLeisOrcamentarias();
$obTExportacaoRelacionais = new TExportacaoRelacionais();

foreach ($undGestora as $inCodEntidade) {
    $obTTCEALLeisOrcamentarias->setDado('exercicio', Sessao::getExercicio());
    $obTTCEALLeisOrcamentarias->setDado('cod_entidade', $inCodEntidade );
    
    $obTTCEALLeisOrcamentarias->listarExportacaoLeisOrcamentarias($rsRecordSet);
    
    $stNomeArquivo ="LeisOrcamentarias";
    $arResult = array();
    $idCount = 0;

    while (!$rsRecordSet->eof()) {
        $arResult[$idCount]['CodUndGestora']            = $rsRecordSet->getCampo('cod_und_gestora'); 
        $arResult[$idCount]['CodigoUA']                 = ($rsRecordSet->getCampo('codigo_ua') == "" ? '0000' : $rsRecordSet->getCampo('codigo_ua'));
        $arResult[$idCount]['Exercicio']                = $rsRecordSet->getCampo('exercicio');
        $arResult[$idCount]['Complementacao']           = $rsRecordSet->getCampo('complementacao');
        $arResult[$idCount]['NumLeiPPA']                = $rsRecordSet->getCampo('num_lei_ppa');
        $arResult[$idCount]['DataLeiPPA']               = $rsRecordSet->getCampo('data_lei_ppa');
        $arResult[$idCount]['DataPubLeiPPA']            = $rsRecordSet->getCampo('data_pub_lei_ppa');
        $arResult[$idCount]['NumLDO']                   = $rsRecordSet->getCampo('num_ldo');
        $arResult[$idCount]['DataLDO']                  = $rsRecordSet->getCampo('data_ldo');
        $arResult[$idCount]['DataPubLDO']               = $rsRecordSet->getCampo('data_pub_ldo');
        $arResult[$idCount]['NumLOA']                   = $rsRecordSet->getCampo('num_loa');
        $arResult[$idCount]['DataLOA']                  = $rsRecordSet->getCampo('data_loa');
        $arResult[$idCount]['DataPubLOA']               = $rsRecordSet->getCampo('data_pub_loa');        
        $arResult[$idCount]['PercCreditoAdicional']     = $rsRecordSet->getCampo('perc_credito_adicional');
        $arResult[$idCount]['PercOpCreditoAntecipacao'] = $rsRecordSet->getCampo('perc_op_credito_antecipacao');
        $arResult[$idCount]['PercOpCreditoInterno']     = $rsRecordSet->getCampo('perc_op_credito_interno');
        $arResult[$idCount]['PercOpCreditoExterno']     = $rsRecordSet->getCampo('perc_op_credito_externo');
        
        $idCount++;

        $rsRecordSet->proximo();
    }
    return $arResult;
}
?>