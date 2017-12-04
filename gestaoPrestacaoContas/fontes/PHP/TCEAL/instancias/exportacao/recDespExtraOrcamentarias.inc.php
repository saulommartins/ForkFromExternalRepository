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

 * Página de Include Oculta - Exportação Arquivos Execucao - recDespExtraOrcamentarias.xml
 *
 * Data de Criação: 09/06/2014
 *
 * @author: Diogo Zarpelon
 *
 * $Id: recDespExtraOrcamentarias.inc.php 59612 2014-09-02 12:00:51Z gelson $
 */

include_once CAM_GPC_TCEAL_MAPEAMENTO.'TTCEALRecDespExtraOrcamentarias.class.php';
include_once CAM_GF_ORC_MAPEAMENTO.'TOrcamentoEntidade.class.php';

$inCodUniGestora = explode(',', $stEntidades);

if (count($inCodUniGestora) > 1){
    
    $obTOrcamentoEntidade = new TOrcamentoEntidade;
    
    foreach ($inCodUniGestora as $cod_entidade) {
        $obTOrcamentoEntidade->setDado('exercicio'    , Sessao::getExercicio() );
        $obTOrcamentoEntidade->setDado('cod_entidade' , $cod_entidade );
        
        $stCondicao = " AND LOWER(CGM.nom_cgm) LIKE '%prefeitura%' ";
        
        $obTOrcamentoEntidade->recuperaRelacionamentoNomes( $rsEntidade, $stCondicao );
        
        if ($rsEntidade->inNumLinhas > 0) {
            $inCodUniGestora = $cod_entidade;
        }
    }

    if (!$inCodUniGestora) {
        $inCodUniGestora = $UndGestora[0];
    }
    
} else {
    $inCodUniGestora = $stEntidades;
}

$obTTCEALRecDespExtraOrcamentarias = new TTCEALRecDespExtraOrcamentarias();
$obTTCEALRecDespExtraOrcamentarias->setDado('exercicio'    , Sessao::getExercicio());
$obTTCEALRecDespExtraOrcamentarias->setDado('cod_entidade' , $stEntidades);
$obTTCEALRecDespExtraOrcamentarias->setDado('und_gestora'  , $inCodUniGestora);
$obTTCEALRecDespExtraOrcamentarias->setDado('dtInicial'    , $dtInicial);
$obTTCEALRecDespExtraOrcamentarias->setDado('dtFinal'      , $dtFinal);
$obTTCEALRecDespExtraOrcamentarias->recuperaRecDespExtraOrcamentarias($rsRecordSet);

$idCount = 0;
$stNomeArquivo = 'RecDespExtraOrcamentarias';
$arResult = array();

while (!$rsRecordSet->eof()) {
    $arResult[$idCount]['CodUndGestora']             = $rsRecordSet->getCampo('cod_und_gestora');
    $arResult[$idCount]['CodigoUA']                  = ($rsRecordSet->getCampo('codigo_ua') == "" ? '0000' : $rsRecordSet->getCampo('codigo_ua'));
    $arResult[$idCount]['Bimestre']                  = $inBimestre;
    $arResult[$idCount]['Exercicio']                 = Sessao::getExercicio();
    $arResult[$idCount]['NumerodaExtraOrcamentario'] = $rsRecordSet->getCampo('numero_da_extra_orcamentario');
    $arResult[$idCount]['CodContaBalancete']         = $rsRecordSet->getCampo('cod_conta_balancete');
    $arResult[$idCount]['IdentificadorDC']           = $rsRecordSet->getCampo('identificador_dc');
    $arResult[$idCount]['Valor']                     = $rsRecordSet->getCampo('valor');
    $arResult[$idCount]['IdentificadorDR']           = $rsRecordSet->getCampo('identificador_dr');
    $arResult[$idCount]['TipoMovimentacao']          = $rsRecordSet->getCampo('tipo_movimentacao');
    $arResult[$idCount]['Classificacao']             = $rsRecordSet->getCampo('classificacao');
    $arResult[$idCount]['CodBanco']                  = $rsRecordSet->getCampo('cod_banco_cd');
    $arResult[$idCount]['CodAgenciaBanco']           = $rsRecordSet->getCampo('cod_agencia_banco_cd');
    $arResult[$idCount]['NumContaCorrente']          = $rsRecordSet->getCampo('num_conta_corrente_cd');
    $arResult[$idCount]['TipoPagamento']             = $rsRecordSet->getCampo('tipo_pagamento');
    $arResult[$idCount]['NumDocumento']              = $rsRecordSet->getCampo('num_documento');
    
    $idCount++;
    
    $rsRecordSet->proximo();
}

unset($UndGestora, $inCodUniGestora, $obTOrcamentoEntidade);

?>