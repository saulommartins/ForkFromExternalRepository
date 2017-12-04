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

 * Página de Include Oculta - Exportação Arquivos Execucao - ReceitaDespesaExtraOrcamentaria.xml
 *
 * Data de Criação: 14/11/2014
 *
 * @author: Evandro Melos
 *
 * $Id: ReceitaDespesaExtraOrcamentaria.inc.php 60780 2014-11-14 20:02:08Z evandro $
 */

include_once CAM_GPC_TCETO_MAPEAMENTO.'TTCETOReceitaDespesaExtraOrcamentaria.class.php';

$obTTCETOReceitaDespesaExtraOrcamentaria = new TTCETOReceitaDespesaExtraOrcamentaria();
$obTTCETOReceitaDespesaExtraOrcamentaria->setDado('exercicio'    , Sessao::getExercicio() );
$obTTCETOReceitaDespesaExtraOrcamentaria->setDado('cod_entidade' , $inCodEntidade         );
$obTTCETOReceitaDespesaExtraOrcamentaria->setDado('und_gestora'  , $inCodEntidade         );
$obTTCETOReceitaDespesaExtraOrcamentaria->setDado('dtInicial'    , $stDataInicial         );
$obTTCETOReceitaDespesaExtraOrcamentaria->setDado('dtFinal'      , $stDataFinal           );
$obTTCETOReceitaDespesaExtraOrcamentaria->recuperaRecDespExtraOrcamentarias($rsRecordSet);

$idCount = 0;
$stNomeArquivo = 'ReceitaDespesaExtraOrcamentaria';
$arResult = array();

while (!$rsRecordSet->eof()) {
    
    $arResult[$idCount]['idUnidadeGestora']        = $rsRecordSet->getCampo('cod_und_gestora');
    $arResult[$idCount]['bimestre']                = $inBimestre;
    $arResult[$idCount]['exercicio']               = Sessao::getExercicio();
    $arResult[$idCount]['idRecursoVinculado']      = $rsRecordSet->getCampo('cod_recurso');
    $arResult[$idCount]['numeroExtraOrcamentario'] = $rsRecordSet->getCampo('numero_da_extra_orcamentario');
    $arResult[$idCount]['contaContabil']           = $rsRecordSet->getCampo('cod_conta_balancete');
    $arResult[$idCount]['debitoCredito']           = $rsRecordSet->getCampo('identificador_dc');
    $arResult[$idCount]['despesaReceita']          = $rsRecordSet->getCampo('identificador_dr');
    $arResult[$idCount]['tipoMovimentacao']        = $rsRecordSet->getCampo('tipo_movimentacao');
    $arResult[$idCount]['data']                    = $rsRecordSet->getCampo('timestamp_transferencia');
    $arResult[$idCount]['valor']                   = $rsRecordSet->getCampo('valor');
    $arResult[$idCount]['classificacao']           = $rsRecordSet->getCampo('classificacao');
    
    $idCount++;
    
    $rsRecordSet->proximo();
}

return $arResult;

?>