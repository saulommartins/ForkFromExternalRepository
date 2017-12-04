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
  * $Id: BalanceteDespesa.inc.php 60826 2014-11-17 20:43:05Z franver $
  * $Date: 2014-11-17 18:43:05 -0200 (Mon, 17 Nov 2014) $
  * $Author: franver $
  * $Rev: 60826 $
  *
*/
include_once CAM_GPC_TCETO_MAPEAMENTO.'FTCETOBalanceteDespesa.class.php';

$FTCETOBalanceteDespesa = new FTCETOBalanceteDespesa();

$rsRecordSet = new RecordSet();

switch ($inBimestre) {
    case 1:
        $FTCETOBalanceteDespesa->setDado('primeiro_mes' , 1 );
        $FTCETOBalanceteDespesa->setDado('segundo_mes'  , 2 );
        break;
    case 2:
        $FTCETOBalanceteDespesa->setDado('primeiro_mes' , 3 );
        $FTCETOBalanceteDespesa->setDado('segundo_mes'  , 4 );
    break;
    case 3:
        $FTCETOBalanceteDespesa->setDado('primeiro_mes' , 5 );
        $FTCETOBalanceteDespesa->setDado('segundo_mes'  , 6 );
    break;
    case 4:
        $FTCETOBalanceteDespesa->setDado('primeiro_mes' , 7 );
        $FTCETOBalanceteDespesa->setDado('segundo_mes'  , 8 );
    break;
    case 5:
        $FTCETOBalanceteDespesa->setDado('primeiro_mes' , 9 );
        $FTCETOBalanceteDespesa->setDado('segundo_mes'  , 10 );
    break;
    case 6:
        $FTCETOBalanceteDespesa->setDado('primeiro_mes' , 11 );
        $FTCETOBalanceteDespesa->setDado('segundo_mes'  , 12 );
    break;
    default:
        $FTCETOBalanceteDespesa->setDado('primeiro_mes' , 0 );
        $FTCETOBalanceteDespesa->setDado('segundo_mes'  , 0 );
        break;
}

$FTCETOBalanceteDespesa->setDado('exercicio'         , $inExercicio  );
$FTCETOBalanceteDespesa->setDado('cod_entidade'      , $inCodEntidade            );
$FTCETOBalanceteDespesa->setDado('dtInicial'         , $stDataInicial              );
$FTCETOBalanceteDespesa->setDado('dtFinal'           , $stDataFinal                );
$FTCETOBalanceteDespesa->setDado('bimestre'          , $inBimestre             );

$FTCETOBalanceteDespesa->recuperaBalanceteDespesa($rsRecordSet);


$idCount=0;
$arResult = array();

while (!$rsRecordSet->eof()) {
    $arResult[$idCount]['idUnidadeGestora']                          = $rsRecordSet->getCampo('id_unidade_gestora');
    $arResult[$idCount]['bimestre']                                  = $inBimestre;
    $arResult[$idCount]['exercicio']                                 = $inExercicio;
    $arResult[$idCount]['idOrgao']                                   = $rsRecordSet->getCampo('id_orgao');
    $arResult[$idCount]['idUnidadeOrcamentaria']                     = $rsRecordSet->getCampo('id_unidade_orcamentaria');
    $arResult[$idCount]['idFuncao']                                  = $rsRecordSet->getCampo('id_funcao');
    $arResult[$idCount]['idSubFuncao']                               = $rsRecordSet->getCampo('id_subfuncao');
    $arResult[$idCount]['idPrograma']                                = $rsRecordSet->getCampo('id_programa');
    $arResult[$idCount]['idProjetoAtividade']                        = $rsRecordSet->getCampo('id_projeto_atividade');
    $arResult[$idCount]['idRubricaDespesa']                          = $rsRecordSet->getCampo('id_rubrica_despesa');
    $arResult[$idCount]['idRecursoVinculado']                        = $rsRecordSet->getCampo('id_recurso_vinculado');
    $arResult[$idCount]['dotacaoInicial']                            = $rsRecordSet->getCampo('dotacao_inicial');
    $arResult[$idCount]['atualizacaoMonetaria']                      = $rsRecordSet->getCampo('atualizacao_monetaria');
    $arResult[$idCount]['creditoSuplementarReducaoDotacao']          = $rsRecordSet->getCampo('credito_suplementar_reducao_dotacao');
    $arResult[$idCount]['creditoSuplementarSuperavitFinanceiro']     = $rsRecordSet->getCampo('credito_suplementar_superavit_financeiro');
    $arResult[$idCount]['creditoSuplementarExcessoArrecadacao']      = $rsRecordSet->getCampo('credito_suplementar_excesso_arrecadacao');
    $arResult[$idCount]['creditoSuplementarOperacaoCredito']         = $rsRecordSet->getCampo('credito_suplementar_operacao_credito');
    $arResult[$idCount]['creditoEspecialReducaoDotacao']             = $rsRecordSet->getCampo('credito_especial_reducao_dotacao');
    $arResult[$idCount]['creditoEspecialSuperavitFinanceiro']        = $rsRecordSet->getCampo('credito_especial_superavit_financeiro');
    $arResult[$idCount]['creditoEspecialExcessoArrecadacao']         = $rsRecordSet->getCampo('credito_especial_excesso_arrecadacao');
    $arResult[$idCount]['creditoEspecialOperacaoCredito']            = $rsRecordSet->getCampo('credito_especial_operacao_credito');
    $arResult[$idCount]['creditoExtraordinario']                     = $rsRecordSet->getCampo('credito_extraordinario');
    $arResult[$idCount]['reducaoDotacaoOrcamentaria']                = $rsRecordSet->getCampo('reducao_dotacao_orcamentaria');
    $arResult[$idCount]['suplementaRecursoVinculado']                = $rsRecordSet->getCampo('suplemento_recurso_vinculado');
    $arResult[$idCount]['reducaoRecursoVinculado']                   = $rsRecordSet->getCampo('reducao_recurso_vinculado');
    $arResult[$idCount]['cronogramaDesenvolvimentoMensal1']          = $rsRecordSet->getCampo('cronograma_desenvolvimento_mensal1');
    $arResult[$idCount]['cronogramaDesenvolvimentoMensal2']          = $rsRecordSet->getCampo('cronograma_desenvolvimento_mensal2');
    $arResult[$idCount]['valorEmpenhado']                            = $rsRecordSet->getCampo('valor_empenhado');
    $arResult[$idCount]['valorLiquidado']                            = $rsRecordSet->getCampo('valor_liquidado');
    $arResult[$idCount]['valorPago']                                 = $rsRecordSet->getCampo('valor_pago');
    $arResult[$idCount]['valorLimitadoLrf']                          = $rsRecordSet->getCampo('valor_limitado_LRF');
    $arResult[$idCount]['valorRecomposicaoDotacaoLrf']               = $rsRecordSet->getCampo('valor_recomposicao_dotacao_LRF');
    $arResult[$idCount]['valorPrevistoRealizadoTerminoExercicioLrf'] = $rsRecordSet->getCampo('valor_previsto_realizado_termino_exercicio_LRF');
    $arResult[$idCount]['aumentoMovimentoOrcamentoQdd']              = $rsRecordSet->getCampo('aumento_movimento_orcamento_qdd');
    $arResult[$idCount]['reducaoMovimentoOrcamentoQdd']              = $rsRecordSet->getCampo('reducao_movimento_orcamento_qdd');

    $idCount++;
    
    $rsRecordSet->proximo();
}

?>