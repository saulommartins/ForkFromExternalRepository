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
    * Página de Include Oculta - Exportação Arquivos Relacionais - UniOrcamentaria.xml
    *
    * Data de Criação: 27/05/2014
    *
    * @author: Franver Sarmento de Moraes
    *
    $Id: UndOrcamentaria.inc.php 59612 2014-09-02 12:00:51Z gelson $
    *
    * @ignore
    *
*/
include_once CAM_GPC_TCEAL_MAPEAMENTO.'TTCEALUndOrcamentaria.class.php';

$obTTCEALUndOrcamentaria = new TTCEALUndOrcamentaria();
$obTTCEALUndOrcamentaria->setDado('exercicio', Sessao::getExercicio());
$obTTCEALUndOrcamentaria->setDado('cod_entidade',$stEntidades);

$obTTCEALUndOrcamentaria->recuperaUndOrcamentaria($rsRecordSet, $stCondicao, $stOrdem, $boTransacao);

$idCount=0;
$stNomeArquivo = 'UndOrcamentaria';
$arResult = array();

while (!$rsRecordSet->eof()) {
    $arResult[$idCount]['CodUndGestora'] = $rsRecordSet->getCampo('cod_und_gestora');
    $arResult[$idCount]['CodigoUA'] = $rsRecordSet->getCampo('codigo_ua');
    $arResult[$idCount]['Bimestre'] = $inBimestre;
    $arResult[$idCount]['Exercicio'] = $rsRecordSet->getCampo('exercicio');
    $arResult[$idCount]['CodOrgao'] = $rsRecordSet->getCampo('cod_orgao');
    $arResult[$idCount]['CodUndOrcamentaria'] = $rsRecordSet->getCampo('cod_und_orcamentaria');
    $arResult[$idCount]['CNPJ'] = $rsRecordSet->getCampo('cnpj');
    $arResult[$idCount]['Nome'] = $rsRecordSet->getCampo('nome');
    $arResult[$idCount]['Identificador'] = $rsRecordSet->getCampo('identificador');
    $arResult[$idCount]['Descricao'] = $rsRecordSet->getCampo('descricao');
    
    $idCount++;

    $rsRecordSet->proximo();
}

unset($obTTCEALUndOrcamentaria);
?>