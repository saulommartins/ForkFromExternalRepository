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

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GPC_TGO_MAPEAMENTO."TTCMGODividaConsolidada.class.php" );

$stPrograma = "ManterConfiguracaoDividaConsolidada";
$pgJs   = "JS".$stPrograma.".js";
include( $pgJs );

$arDivida = Sessao::read('arDivida');

if (empty($arDivida) || (count($arDivida) == 0)) {
    SistemaLegado::exibeAviso("Configuração salva","incluir","incluir_n");
    SistemaLegado::LiberaFrames(true,False);
    die;
}

//Variável de controle para efetuar limpeza das chaves antes de inserir os dados no banco
$limpaDivida = true;
$obErro = new Erro;
foreach ($arDivida as $arValue) {
    if ($limpaDivida) {
        $dtInicio = Sessao::getExercicio().'-'.$arValue['inMes'].'-01';
        $dtFim    = SistemaLegado::dataToSql(SistemaLegado::retornaUltimoDiaMes($arValue['inMes'], Sessao::getExercicio()));

        $obTTCMGODividaConsolidada = new TTCMGODividaConsolidada();
        $obTTCMGODividaConsolidada->setDado('exercicio' , Sessao::getExercicio());
        $obTTCMGODividaConsolidada->setDado('dt_inicio' , $dtInicio);
        $obTTCMGODividaConsolidada->setDado('dt_fim'    , $dtFim);
        $obTTCMGODividaConsolidada->limpaDividas();

        $limpaDivida = false;
    }

    $dtInicio = '01/'.$arValue['inMes'].'/'.Sessao::getExercicio();
    $dtFim    = SistemaLegado::retornaUltimoDiaMes($arValue['inMes'], Sessao::getExercicio());

    $obTTCMGODividaConsolidada = new TTCMGODividaConsolidada();
    $obTTCMGODividaConsolidada->setDado('exercicio'            , Sessao::getExercicio());
    $obTTCMGODividaConsolidada->setDado('dt_inicio'            , $dtInicio);
    $obTTCMGODividaConsolidada->setDado('dt_fim'               , $dtFim);
    $obTTCMGODividaConsolidada->setDado('num_unidade'          , $arValue['inUnidade']);
    $obTTCMGODividaConsolidada->setDado('num_orgao'            , $arValue['inOrgao']);
    $obTTCMGODividaConsolidada->setDado('numcgm'               , $arValue['inCGM'] ? $arValue['inCGM'] : 'null');
    $obTTCMGODividaConsolidada->setDado('tipo_lancamento'      , $arValue['inTipoLancamento']);
    $obTTCMGODividaConsolidada->setDado('nro_lei_autorizacao'  , $arValue['stLeiAutorizacao']);
    $obTTCMGODividaConsolidada->setDado('dt_lei_autorizacao'   , $arValue['dtLeiAutorizacao']);
    $obTTCMGODividaConsolidada->setDado('vl_saldo_anterior'    , (float) str_replace(',', '.', str_replace('.', '', $arValue['vlSaldoAnterior'])));
    $obTTCMGODividaConsolidada->setDado('vl_contratacao'       , (float) str_replace(',', '.', str_replace('.', '', $arValue['vlContratacao'])));
    $obTTCMGODividaConsolidada->setDado('vl_amortizacao'       , (float) str_replace(',', '.', str_replace('.', '', $arValue['vlAmortizacao'])));
    $obTTCMGODividaConsolidada->setDado('vl_cancelamento'      , (float) str_replace(',', '.', str_replace('.', '', $arValue['vlCancelamento'])));
    $obTTCMGODividaConsolidada->setDado('vl_encampacao'        , (float) str_replace(',', '.', str_replace('.', '', $arValue['vlEncampacao'])));
    $obTTCMGODividaConsolidada->setDado('vl_atualizacao'       , (float) str_replace(',', '.', str_replace('.', '', $arValue['vlAtualizacao'])));
    $obTTCMGODividaConsolidada->setDado('vl_saldo_atual'       , (float) str_replace(',', '.', str_replace('.', '', $arValue['vlSaldoAtual'])));
    $obErro = $obTTCMGODividaConsolidada->inclusao();
}

//Limpa tela principal e esvazia sessão
Sessao::remove('arDivida');
$js = "<script>window.parent.frames['telaPrincipal'].limparDivida();</script>";
$js.= "<script>window.parent.frames['telaPrincipal'].document.getElementById('inMes').value = '';</script>";
$js.= "<script>window.parent.frames['telaPrincipal'].document.getElementById('spnDivida').innerHTML = '';</script>";
echo $js;

if ($obErro->ocorreu()) {
    SistemaLegado::exibeAviso("Erro ao salvar configuração","n_incluir","erro");
} else {
    SistemaLegado::exibeAviso("Configuração salva","incluir","incluir_n");
}
