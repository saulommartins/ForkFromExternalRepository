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
    * Página de Include Oculta - Exportação Arquivos Relacionais - DepositoPagamento.xml
    *
    * Data de Criação: 17/11/2014
    *
    * @author: Evandro Melos
    *
    * $Id: DepositoPagamento.inc.php 60908 2014-11-24 16:04:20Z arthur $
    *
    * @ignore
    *
*/

include_once CAM_GPC_TCETO_MAPEAMENTO.'TTCETODepositoPagamento.class.php';

$obTTCETODepositoPagamento = new TTCETODepositoPagamento();

$obTTCETODepositoPagamento->setDado('exercicio'     , Sessao::getExercicio() );
$obTTCETODepositoPagamento->setDado('cod_entidade'  , $inCodEntidade         );
$obTTCETODepositoPagamento->setDado('dtInicial'     , $stDataInicial         );
$obTTCETODepositoPagamento->setDado('dtFinal'       , $stDataFinal           );
$obTTCETODepositoPagamento->setDado('bimestre'      , $inBimestre            );

$obTTCETODepositoPagamento->recuperaDepositoPagamento($rsRecordSet);

$idCount = 0;
$stNomeArquivo = 'DepositoPagamento';
$arResult = array();

while (!$rsRecordSet->eof()) {
    
    $arResult[$idCount]['idUnidadeGestora']   = $rsRecordSet->getCampo('cod_und_gestora');    
    $arResult[$idCount]['bimestre']           = $inBimestre;
    $arResult[$idCount]['exercicio']          = Sessao::getExercicio();
    $arResult[$idCount]['idRecursoVinculado'] = $rsRecordSet->getCampo('recurso_vinculado');
    $arResult[$idCount]['contaContabil']      = $rsRecordSet->getCampo('cod_conta_contabil');
    $arResult[$idCount]['numeroEmpenho']      = $rsRecordSet->getCampo('num_empenho');
    $arResult[$idCount]['numeroPagamento']    = $rsRecordSet->getCampo('num_pagamento');
    $arResult[$idCount]['numeroRegistro']     = $rsRecordSet->getCampo('num_registro');
    $arResult[$idCount]['data']               = $rsRecordSet->getCampo('dt_liquidacao');
    $arResult[$idCount]['valor']              = $rsRecordSet->getCampo('valor');
    $arResult[$idCount]['sinal']              = $rsRecordSet->getCampo('sinal');
    $arResult[$idCount]['sinalLancamento']    = $rsRecordSet->getCampo('sinal_lancamento');
    
    $idCount++;
    
    $rsRecordSet->proximo();
}

?>