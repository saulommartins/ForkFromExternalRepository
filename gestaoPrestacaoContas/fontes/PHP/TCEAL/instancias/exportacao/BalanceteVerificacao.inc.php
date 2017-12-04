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
    * Página de Include Oculta - Exportação Arquivos Execucao - BalanceteVerificacao.xml
    *
    * Data de Criação: 30/05/2014
    *
    * @author: Franver Sarmento de Moraes
    *
    $Id: BalanceteVerificacao.inc.php 59693 2014-09-05 12:39:50Z carlos.silva $
    *
    * @ignore
    *
*/
include_once CAM_GPC_TCEAL_MAPEAMENTO.'TTCEALBalanceteVerificacao.class.php';
include_once CAM_GF_ORC_MAPEAMENTO.'TOrcamentoEntidade.class.php';

$UndGestora = explode(',', $stEntidades);
foreach($UndGestora   as $stEntidade){
    $rsRecordSet = "rsRecordSet";
    $rsRecordSet .= $stEntidade;        
    $$rsRecordSet = new RecordSet();

    $obTTCEALBalanceteVerificacao = new TTCEALBalanceteVerificacao();
    $obTTCEALBalanceteVerificacao->setDado('exercicio', Sessao::getExercicio());
    $obTTCEALBalanceteVerificacao->setDado('cod_entidade',$stEntidade);
    $obTTCEALBalanceteVerificacao->setDado('dtInicial', $dtInicial );
    $obTTCEALBalanceteVerificacao->setDado('dtFinal'  , $dtFinal   );
    $obTTCEALBalanceteVerificacao->recuperaBalanceteVerificacao($$rsRecordSet, $stCondicao="", $stOrdem="", $boTransacao);
}

$idCount=0;
$stNomeArquivo = 'BalanceteVerificacao';
$arResult = array();

while (!$$rsRecordSet->eof()) {
    $arResult[$idCount]['CodUndGestora'] = $$rsRecordSet->getCampo('cod_und_gestora');
    $arResult[$idCount]['CodigoUA'] = $$rsRecordSet->getCampo('codigo_ua');
    $arResult[$idCount]['Bimestre'] = $inBimestre;
    $arResult[$idCount]['Exercicio'] = Sessao::getExercicio();
    $arResult[$idCount]['CodOrgao'] = $$rsRecordSet->getCampo('cod_orgao');
    $arResult[$idCount]['CodUndOrcamentaria'] = $$rsRecordSet->getCampo('cod_und_orcamentaria');
    $arResult[$idCount]['CodContaBalancete'] = $$rsRecordSet->getCampo('cod_conta_balancete');
    $arResult[$idCount]['Descricao'] = trim($$rsRecordSet->getCampo('descricao'));
    $arResult[$idCount]['SaldoAnterior'] = $$rsRecordSet->getCampo('saldo_anterior');
    $arResult[$idCount]['MovDebitoNoMes'] = $$rsRecordSet->getCampo('mov_debito_no_mes');
    $arResult[$idCount]['MovDebitoAteMes'] = $$rsRecordSet->getCampo('mov_debito_ate_mes');
    $arResult[$idCount]['MovCreditoNoMes'] = $$rsRecordSet->getCampo('mov_credito_no_mes');
    $arResult[$idCount]['MovCreditoAteMes'] = $$rsRecordSet->getCampo('mov_credito_ate_mes');
    $arResult[$idCount]['SaldoAtual'] = $$rsRecordSet->getCampo('saldo_atual');
    $arResult[$idCount]['TipoNivelConta'] = $$rsRecordSet->getCampo('tipo_nivel_conta');
    $arResult[$idCount]['TipoBalancete'] = $$rsRecordSet->getCampo('tipo_balancete');
    
    $idCount++;
    
    $$rsRecordSet->proximo();
}

unset($obTTCEALBalanceteVerificacao);
?>