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
    * Página de Processamento
    * Data de Criação   : 16/04/2007

    * @author Henrique Boaventura

    * @ignore

    $Id: PRManterDividaFundada.php 61697 2015-02-26 12:46:40Z evandro $

    * Casos de uso : uc-06.04.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GPC_TGO_MAPEAMENTO."TTCMGODividaFundada.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterDividaFundada";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$stAcao = $request->get('stAcao');

$obErro = new Erro;

$boFlagTransacao = false;
$obTransacao = new Transacao;
$obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

switch ($stAcao) {
    case 'incluir' :

        $obTTCMGODividaFundada = new TTCMGODividaFundada;
        $obTTCMGODividaFundada->setDado('exercicio'             , $request->get('stExercicio'));
        $obTTCMGODividaFundada->setDado('cod_entidade'          , $request->get('inCodEntidade'));
        $obTTCMGODividaFundada->setDado('num_unidade'           , $request->get('inNumUnidade'));
        $obTTCMGODividaFundada->setDado('num_orgao'             , $request->get('inNumOrgao'));
        $obTTCMGODividaFundada->setDado('cod_norma'             , $request->get('inCodLeiAutorizacao'));
        $obTTCMGODividaFundada->setDado('numcgm'                , $request->get('inCGM'));
        $obTTCMGODividaFundada->setDado('cod_tipo_lancamento'   , $request->get('inTipoLancamento'));
        $obTTCMGODividaFundada->setDado('valor_saldo_anterior'  , $request->get('flValorSaldoAnterior'));
        $obTTCMGODividaFundada->setDado('valor_contratacao'     , $request->get('flValorContratacao'));
        $obTTCMGODividaFundada->setDado('valor_amortizacao'     , $request->get('flValorAmortizacao'));
        $obTTCMGODividaFundada->setDado('valor_cancelamento'    , $request->get('flValorCancelamento'));
        $obTTCMGODividaFundada->setDado('valor_encampacao'      , $request->get('flValorEncampacao'));
        $obTTCMGODividaFundada->setDado('valor_correcao'        , $request->get('flValorCorrecao'));
        $obTTCMGODividaFundada->setDado('valor_saldo_atual'     , $request->get('flValorSaldoAtual'));

        $obErro = $obTTCMGODividaFundada->inclusao($boTransacao);

        $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTTCMGODividaFundada );

        if (!$obErro->ocorreu()) {
            SistemaLegado::alertaAviso($pgForm,"Dívida Fundada".$request->get('cod_norma'),"alterar","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
        }

    break;

    case 'alterar':
        $obTTCMGODividaFundada = new TTCMGODividaFundada;
        $obTTCMGODividaFundada->setDado('exercicio'             , $request->get('stExercicio'));
        $obTTCMGODividaFundada->setDado('cod_entidade'          , $request->get('inCodEntidade'));
        $obTTCMGODividaFundada->setDado('num_unidade'           , $request->get('inNumUnidade'));
        $obTTCMGODividaFundada->setDado('num_orgao'             , $request->get('inNumOrgao'));
        $obTTCMGODividaFundada->setDado('cod_norma'             , $request->get('inCodLeiAutorizacao'));
        $obTTCMGODividaFundada->setDado('numcgm'                , $request->get('inCGM'));
        $obTTCMGODividaFundada->setDado('cod_tipo_lancamento'   , $request->get('inTipoLancamento'));
        $obTTCMGODividaFundada->setDado('valor_saldo_anterior'  , $request->get('flValorSaldoAnterior'));
        $obTTCMGODividaFundada->setDado('valor_contratacao'     , $request->get('flValorContratacao'));
        $obTTCMGODividaFundada->setDado('valor_amortizacao'     , $request->get('flValorAmortizacao'));
        $obTTCMGODividaFundada->setDado('valor_cancelamento'    , $request->get('flValorCancelamento'));
        $obTTCMGODividaFundada->setDado('valor_encampacao'      , $request->get('flValorEncampacao'));
        $obTTCMGODividaFundada->setDado('valor_correcao'        , $request->get('flValorCorrecao'));
        $obTTCMGODividaFundada->setDado('valor_saldo_atual'     , $request->get('flValorSaldoAtual'));

        $obErro = $obTTCMGODividaFundada->alteracao($boTransacao);

        $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTTCMGODividaFundada );

        if (!$obErro->ocorreu()) {
            SistemaLegado::alertaAviso($pgFilt,"Dívida Fundada".$request->get('cod_norma'),"alterar","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
        }

    break;

    case 'excluir':
        $obTTCMGODividaFundada = new TTCMGODividaFundada;
        $obTTCMGODividaFundada->setDado('exercicio'             ,$request->get('inExercicio'));
        $obTTCMGODividaFundada->setDado('cod_entidade'          ,$request->get('inCodEntidade'));
        $obTTCMGODividaFundada->setDado('num_unidade'           ,$request->get('inNumUnidade'));
        $obTTCMGODividaFundada->setDado('num_orgao'             ,$request->get('inNumOrgao'));
        $obTTCMGODividaFundada->setDado('cod_norma'             ,$request->get('inCodNorma'));
        $obTTCMGODividaFundada->setDado('numcgm'                ,$request->get('inNumCgm'));
        $obTTCMGODividaFundada->setDado('cod_tipo_lancamento'   ,$request->get('inCodTipoLancamento'));
        $obTTCMGODividaFundada->setDado('valor_saldo_anterior'  ,$request->get('vlSaldoAnterior'));
        $obTTCMGODividaFundada->setDado('valor_contratacao'     ,$request->get('vlContratacao'));
        $obTTCMGODividaFundada->setDado('valor_amortizacao'     ,$request->get('vlAmortizacao'));
        $obTTCMGODividaFundada->setDado('valor_cancelamento'    ,$request->get('vlCancelamento'));
        $obTTCMGODividaFundada->setDado('valor_encampacao'      ,$request->get('vlEncampacao'));
        $obTTCMGODividaFundada->setDado('valor_correcao'        ,$request->get('vlCorrecao'));
        $obTTCMGODividaFundada->setDado('valor_saldo_atual'     ,$request->get('vlSaldoAtual'));

        $obErro = $obTTCMGODividaFundada->exclusao($boTransacao);

        $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTTCMGODividaFundada );

        if (!$obErro->ocorreu()) {
            SistemaLegado::alertaAviso($pgFilt,"Dívida Fundada".$request->get('cod_norma'),"alterar","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
        }

    break;
}

//Sessao::encerraExcecao();

?>