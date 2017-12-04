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
    * Página de Include Oculta - Exportação Arquivos Execucao - BalanceteDespesa.xml
    *
    * Data de Criação: 04/07/2014
    *
    * @author: Evandro Melos
    *
    $Id: balanceteDespesa.inc.php 65453 2016-05-24 13:41:56Z jean $
    *
    * @ignore
    *
*/
include_once CAM_GPC_TCEAL_MAPEAMENTO.'TTCEALBalanceteDespesa.class.php';
include_once CAM_GF_ORC_MAPEAMENTO.'TOrcamentoEntidade.class.php';

$TTCEALBalanceteDespesa = new TTCEALBalanceteDespesa();

$UndGestora = explode(',', $stEntidades);
if (count($UndGestora) > 1) {
    $obTOrcamentoEntidade = new TOrcamentoEntidade;
    foreach ($UndGestora as $cod_entidade) {
        $obTOrcamentoEntidade->setDado( 'exercicio', Sessao::getExercicio() );
        $obTOrcamentoEntidade->setDado( 'cod_entidade', $cod_entidade );
        $stCondicao = " AND CGM.nom_cgm ILIKE 'prefeitura%' ";
        $obTOrcamentoEntidade->recuperaRelacionamentoNomes( $rsEntidade, $stCondicao );
        
        if ($rsEntidade->inNumLinhas > 0){
            $CodUndGestora = $cod_entidade;
        }
    }
    if (!$CodUndGestora)
        $CodUndGestora = $UndGestora[0];
} else {
    $CodUndGestora = $stEntidades;
}
foreach($UndGestora   as $stEntidade){
    $rsRecordSet = "rsRecordSet";
    $rsRecordSet .= $stEntidade;        
    $$rsRecordSet = new RecordSet();

    switch ($inBimestre) {
        case 1:
            $TTCEALBalanceteDespesa->setDado('primeiro_mes' , 1 );
            $TTCEALBalanceteDespesa->setDado('segundo_mes'  , 2 );
            break;
        case 2:
            $TTCEALBalanceteDespesa->setDado('primeiro_mes' , 3 );
            $TTCEALBalanceteDespesa->setDado('segundo_mes'  , 4 );
        break;
        case 3:
            $TTCEALBalanceteDespesa->setDado('primeiro_mes' , 5 );
            $TTCEALBalanceteDespesa->setDado('segundo_mes'  , 6 );
        break;
        case 4:
            $TTCEALBalanceteDespesa->setDado('primeiro_mes' , 7 );
            $TTCEALBalanceteDespesa->setDado('segundo_mes'  , 8 );
        break;
        case 5:
            $TTCEALBalanceteDespesa->setDado('primeiro_mes' , 9 );
            $TTCEALBalanceteDespesa->setDado('segundo_mes'  , 10 );
        break;
        case 6:
            $TTCEALBalanceteDespesa->setDado('primeiro_mes' , 11 );
            $TTCEALBalanceteDespesa->setDado('segundo_mes'  , 12 );
        break;
        default:
            $TTCEALBalanceteDespesa->setDado('primeiro_mes' , 0 );
            $TTCEALBalanceteDespesa->setDado('segundo_mes'  , 0 );
            break;
    }

    $TTCEALBalanceteDespesa->setDado('exercicio'         , Sessao::getExercicio()  );
    $TTCEALBalanceteDespesa->setDado('cod_entidade'      , $stEntidade            );
    $TTCEALBalanceteDespesa->setDado('und_gestora'       , $CodUndGestora          );
    $TTCEALBalanceteDespesa->setDado('dtInicial'         , $dtInicial              );
    $TTCEALBalanceteDespesa->setDado('dtFinal'           , $dtFinal                );
    $TTCEALBalanceteDespesa->setDado('bimestre'          , $inBimestre             );

    $TTCEALBalanceteDespesa->recuperaBalanceteDespesa($$rsRecordSet);
}
$idCount=0;
$stNomeArquivo = 'BalanceteDespesa';
$arResult = array();

while (!$$rsRecordSet->eof()) {
    $arResult[$idCount]['CodUndGestora']                = $$rsRecordSet->getCampo('cod_und_gestora');
    $arResult[$idCount]['CodigoUA']                     = $$rsRecordSet->getCampo('codigo_ua');
    $arResult[$idCount]['Bimestre']                     = $inBimestre;
    $arResult[$idCount]['Exercicio']                    = Sessao::getExercicio();
    $arResult[$idCount]['CodOrgao']                     = $$rsRecordSet->getCampo('cod_orgao');
    $arResult[$idCount]['CodUndOrcamentaria']           = $$rsRecordSet->getCampo('cod_unid_orcamentaria');
    $arResult[$idCount]['CodFuncao']                    = $$rsRecordSet->getCampo('cod_funcao');
    $arResult[$idCount]['CodSubFuncao']                 = $$rsRecordSet->getCampo('cod_subfuncao');
    $arResult[$idCount]['CodPrograma']                  = $$rsRecordSet->getCampo('cod_programa');
    $arResult[$idCount]['CodProjAtividade']             = $$rsRecordSet->getCampo('cod_proj_atividade');
    $arResult[$idCount]['CodContaDespesa']              = $$rsRecordSet->getCampo('cod_conta_despesa');
    $arResult[$idCount]['CodContaContabil']             = $$rsRecordSet->getCampo('cod_conta_contabil');
    $arResult[$idCount]['CodRecVinculado']              = $$rsRecordSet->getCampo('cod_rec_vinculado');
    $arResult[$idCount]['DotacaoInicial']               = $$rsRecordSet->getCampo('dotacao_inicial');
    $arResult[$idCount]['AtualizacaoMonetaria']         = $$rsRecordSet->getCampo('atualizacao_monetaria');
    $arResult[$idCount]['CreditoSupSuperavit']          = $$rsRecordSet->getCampo('credito_sup_superavit');
    $arResult[$idCount]['CreditoSupExcessoArrecadacao'] = $$rsRecordSet->getCampo('credito_sup_excesso_arrecadacao');
    $arResult[$idCount]['CreditoSupOpCredito']          = $$rsRecordSet->getCampo('credito_sup_op_credito');
    $arResult[$idCount]['CreditoSupReducao']            = $$rsRecordSet->getCampo('credito_sup_reducao');
    $arResult[$idCount]['CredEspSuperavit']             = $$rsRecordSet->getCampo('cred_esp_superavit');
    $arResult[$idCount]['CredEspExcessoArrecadacao']    = $$rsRecordSet->getCampo('cred_esp_excesso_arrecadacao');
    $arResult[$idCount]['CredEspOpCredito']             = $$rsRecordSet->getCampo('cred_esp_op_credito');
    $arResult[$idCount]['CredEspReducao']               = $$rsRecordSet->getCampo('cred_esp_reducao');
    $arResult[$idCount]['CreditoExtraordinario']        = $$rsRecordSet->getCampo('credito_extraordinario');
    $arResult[$idCount]['ReducaoDotacoes']              = $$rsRecordSet->getCampo('reducao_dotacoes');
    $arResult[$idCount]['DotacaoAtualizada']            = $$rsRecordSet->getCampo('dotacao_atualizada');
    $arResult[$idCount]['SupRecVinculado']              = $$rsRecordSet->getCampo('sup_rec_vinculado');
    $arResult[$idCount]['RedRecVinculado']              = $$rsRecordSet->getCampo('red_rec_vinculado');
    $arResult[$idCount]['ValorEmpenhado']               = $$rsRecordSet->getCampo('valor_empenhado');
    $arResult[$idCount]['ValorLiquidado']               = $$rsRecordSet->getCampo('valor_liquidado');
    $arResult[$idCount]['ValorPago']                    = $$rsRecordSet->getCampo('valor_pago');
    $arResult[$idCount]['ValorLimitadoLRF']             = $$rsRecordSet->getCampo('valor_limitado_LRF');
    $arResult[$idCount]['ValorRecLRF']                  = $$rsRecordSet->getCampo('valor_rec_LRF');
    $arResult[$idCount]['ValorPrevLRF']                 = $$rsRecordSet->getCampo('valor_prev_LRF');
    $arResult[$idCount]['SaldoDotacao']                 = $$rsRecordSet->getCampo('saldo_dotacao');
    $arResult[$idCount]['CronDesenvMensal1']            = $$rsRecordSet->getCampo('cron_desenv_mensal1');
    $arResult[$idCount]['CronDesenvMensal2']            = $$rsRecordSet->getCampo('cron_desenv_mensal2');

    $idCount++;
    
    $$rsRecordSet->proximo();
}

unset($UndGestora, $CodUndGestora, $obTOrcamentoEntidade);
?>