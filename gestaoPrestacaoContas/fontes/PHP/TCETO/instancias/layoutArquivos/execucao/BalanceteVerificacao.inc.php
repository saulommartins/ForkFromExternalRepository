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
    * Data de Criação: 12/11/2014
    *
    * @author: Evandro Melos
    *
    * $Id: BalanceteVerificacao.inc.php 60732 2014-11-12 16:00:40Z evandro $
    *
    * @ignore
    *
*/
include_once CAM_GPC_TCETO_MAPEAMENTO.'TTCETOBalanceteVerificacao.class.php';

$obTTCETOBalanceteVerificacao = new TTCETOBalanceteVerificacao();
$obTTCETOBalanceteVerificacao->setDado('exercicio'   , Sessao::getExercicio() );
$obTTCETOBalanceteVerificacao->setDado('cod_entidade', $inCodEntidade         );
$obTTCETOBalanceteVerificacao->setDado('dtInicial'   , $stDataInicial         );
$obTTCETOBalanceteVerificacao->setDado('dtFinal'     , $stDataFinal           );

$obTTCETOBalanceteVerificacao->recuperaBalanceteVerificacao($rsRecordSet, $stCondicao, "", $boTransacao);

$idCount=0;
$stNomeArquivo = 'BalanceteVerificacao';
$arResult = array();

$rsRecordSet->addFormatacao("descricao", "MB_SUBSTRING(0,255)");

while (!$rsRecordSet->eof()) {
    
    $arResult[$idCount]['idUnidadeGestora']             = $rsRecordSet->getCampo('cod_und_gestora');    
    $arResult[$idCount]['bimestre']                     = $inBimestre;
    $arResult[$idCount]['exercicio']                    = Sessao::getExercicio();
    $arResult[$idCount]['idOrgao']                      = $rsRecordSet->getCampo('cod_orgao');
    $arResult[$idCount]['idUnidadeOrcamentaria']        = $rsRecordSet->getCampo('cod_und_orcamentaria');
    $arResult[$idCount]['contaContabil']                = $rsRecordSet->getCampo('cod_conta_balancete');
    $arResult[$idCount]['saldoAnteriorContaDevedora']   = $rsRecordSet->getCampo('saldo_anterior_conta_devedora');
    $arResult[$idCount]['saldoAnteriorContaCredora']    = $rsRecordSet->getCampo('saldo_anterior_conta_credora');
    $arResult[$idCount]['movimentoContaDevedora']       = $rsRecordSet->getCampo('mov_conta_devedora');
    $arResult[$idCount]['movimentoContaCredora']        = $rsRecordSet->getCampo('mov_conta_credora');
    $arResult[$idCount]['saldoAtualContaDevedora']      = $rsRecordSet->getCampo('saldo_atual_conta_devedora');
    $arResult[$idCount]['saldoAtualContaCredora']       = $rsRecordSet->getCampo('saldo_atual_conta_credora');
    $arResult[$idCount]['descricao']                    = $rsRecordSet->getCampo('descricao');
    $arResult[$idCount]['escrituracao']                 = $rsRecordSet->getCampo('escrituracao');
    $arResult[$idCount]['indicadorSuperavitFinanceiro'] = $rsRecordSet->getCampo('indicador_superavit');
    $arResult[$idCount]['numeroNivelConta']             = $rsRecordSet->getCampo('nivel');
    $arResult[$idCount]['naturezaInformacao']           = $rsRecordSet->getCampo('natureza_informacao');
    $arResult[$idCount]['tipoBalancete']                = $rsRecordSet->getCampo('tipo_balancete');
    
    $idCount++;
    
    $rsRecordSet->proximo();
}

return $arResult;
?>