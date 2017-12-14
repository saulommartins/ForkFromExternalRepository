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

    * Página de Include Oculta - Exportação Arquivos Relacionais - DeclaracaoPublicacaoRREO.xml
    *
    * Data de Criação: 12/05/2016
    *
    * @author: Michel Teixeira
    *
    * $Id: declaracaoPublicacaoRREO.inc.php 65338 2016-05-12 21:26:51Z michel $
    *
    * @ignore
    *
*/

include_once CAM_GPC_TCEAL_MAPEAMENTO.'TTCEALPublicacaoRREO.class.php';

$stNomeArquivo = 'DeclaracaoPublicacaoRREO';

$rsRecordSet = new RecordSet();

$obTTCEALPublicacaoRREO = new TTCEALPublicacaoRREO();
$obTTCEALPublicacaoRREO->setDado('dtInicial'   , $dtInicial);
$obTTCEALPublicacaoRREO->setDado('dtFinal'     , $dtFinal);
$obTTCEALPublicacaoRREO->setDado('stExercicio' , Sessao::getExercicio());
$obTTCEALPublicacaoRREO->setDado('inEntidade'  , $stEntidades);

$obTTCEALPublicacaoRREO->recuperaExportacaoRREO($rsRecordSet);

$arResult = array();
$idCount = 0;

while (!$rsRecordSet->eof()) {
    $arResult[$idCount]['Texto']                         = $rsRecordSet->getCampo('observacao');
    $arResult[$idCount]['LocalDeclaracaoPublicacaoRREO'] = $rsRecordSet->getCampo('nom_veiculo');
    $arResult[$idCount]['DataDeclaracaoPublicacaoRREO']  = $rsRecordSet->getCampo('dt_publicacao');
    $arResult[$idCount]['CodUndGestora']                 = $rsRecordSet->getCampo('cod_und_gestora');
    $arResult[$idCount]['CodigoUA']                      = $rsRecordSet->getCampo('codigo_ua');
    $arResult[$idCount]['Bimestre']                      = $inBimestre;
    $arResult[$idCount]['Exercicio']                     = $rsRecordSet->getCampo('exercicio');

    $idCount++;

    $rsRecordSet->proximo();
}

return $arResult;
   
?>