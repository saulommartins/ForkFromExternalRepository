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
    * Página de Include Oculta - Exportação Arquivos Relacionais - Unidade Orcamentaria.xml
    *
    * Data de Criação: 11/11/2014
    *
    * @author: Evandro Melos
    *
    * $Id: UnidadeOrcamentaria.inc.php 60722 2014-11-11 18:20:25Z evandro $
    *
    * @ignore
    *
*/
include_once CAM_GPC_TCETO_MAPEAMENTO.'TTCETOUnidadeOrcamentaria.class.php';

$obTTCETOUnidadeOrcamentaria = new TTCETOUnidadeOrcamentaria();
$obTTCETOUnidadeOrcamentaria->setDado('exercicio'   , Sessao::getExercicio());
$obTTCETOUnidadeOrcamentaria->setDado('cod_entidade', $inCodEntidade        );

$obTTCETOUnidadeOrcamentaria->recuperaUndOrcamentaria($rsRecordSet, $stCondicao, $stOrdem, $boTransacao);

$idCount=0;
$stNomeArquivo = 'UndOrcamentaria';
$arResult = array();

$rsRecordSet->addFormatacao("nome","MB_SUBSTRING(0,100)");

while (!$rsRecordSet->eof()) {
    
    $arResult[$idCount]['idUnidadeGestora']      = $rsRecordSet->getCampo('cod_und_gestora');
    $arResult[$idCount]['bimestre']              = $inBimestre;
    $arResult[$idCount]['exercicio']             = $rsRecordSet->getCampo('exercicio');
    $arResult[$idCount]['idUnidadeOrcamentaria'] = $rsRecordSet->getCampo('cod_und_orcamentaria');
    $arResult[$idCount]['idOrgao']               = $rsRecordSet->getCampo('cod_orgao');
    $arResult[$idCount]['nome']                  = $rsRecordSet->getCampo('nome');
    $arResult[$idCount]['identificador']         = $rsRecordSet->getCampo('identificador');    
    $arResult[$idCount]['cnpj']                  = $rsRecordSet->getCampo('cnpj');
    
    $idCount++;

    $rsRecordSet->proximo();
}

return $arResult;
?>