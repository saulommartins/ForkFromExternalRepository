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
    * Data de Criação: 12/06/2014
    *
    * @author: Evandro Melos
    *
    $Id: depositoPagamento.inc.php 65567 2016-05-31 21:12:25Z arthur $
    *
    * @ignore
    *
*/
include_once CAM_GF_ORC_MAPEAMENTO.'TOrcamentoEntidade.class.php';
include_once CAM_GPC_TCEAL_MAPEAMENTO.'TTCEALLiquidacao.class.php';

$obTTCEALLiquidacao = new TTCEALLiquidacao();

$obTTCEALLiquidacao->setDado('exercicio'     , Sessao::getExercicio());
$obTTCEALLiquidacao->setDado('cod_entidade'  , $stEntidades          );
$obTTCEALLiquidacao->setDado('und_gestora'   , $CodUndGestora        );
$obTTCEALLiquidacao->setDado('dtInicial'     , $dtInicial            );
$obTTCEALLiquidacao->setDado('dtFinal'       , $dtFinal              );
$obTTCEALLiquidacao->setDado('bimestre'      , $inBimestre           );

$obTTCEALLiquidacao->recuperaDepositoPagamento($rsRecordSet);

$idCount = 0;
$stNomeArquivo = 'DepositoPagamento';
$arResult = array();

// Conforme combinado, será gerado em branco, referente a solicitação do ticket #23811
$rsRecordSet = new Recordset();

while (!$rsRecordSet->eof()) {
    $arResult[$idCount]['CodUndGestora']        = $rsRecordSet->getCampo('cod_und_gestora');
    $arResult[$idCount]['CodigoUA']             = $rsRecordSet->getCampo('codigo_ua');
    $arResult[$idCount]['Bimestre']             = $inBimestre;
    $arResult[$idCount]['Exercicio']            = Sessao::getExercicio();
    $arResult[$idCount]['NumEmpenho']           = $rsRecordSet->getCampo('num_empenho');
    $arResult[$idCount]['NumLiquidacao']        = $rsRecordSet->getCampo('num_liquidacao');
    $arResult[$idCount]['NumPagamento']         = $rsRecordSet->getCampo('num_pagamento');
    $arResult[$idCount]['Valor']                = $rsRecordSet->getCampo('valor');
    $arResult[$idCount]['Sinal']                = $rsRecordSet->getCampo('sinal');
    $arResult[$idCount]['Tipo']                 = $rsRecordSet->getCampo('tipo');
    $arResult[$idCount]['CodContaBalancete']    = $rsRecordSet->getCampo('cod_estrutural');
    $arResult[$idCount]['CodContaContabil']     = $rsRecordSet->getCampo('cod_conta_contabil');
    
    $idCount++;
    
    $rsRecordSet->proximo();
}

unset($UndGestora, $CodUndGestora, $obTOrcamentoEntidade);

?>