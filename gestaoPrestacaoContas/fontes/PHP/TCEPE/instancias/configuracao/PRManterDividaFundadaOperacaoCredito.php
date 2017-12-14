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
    * Data de Criação   : 06/02/2014

    * @author Arthur Cruz
*/
 
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GPC_TCEPE_MAPEAMENTO.'TTCEPEDividaFundadaOperacaoCredito.class.php' );

$link = Sessao::read("link");

//Define o nome dos arquivos PHP
$stPrograma = "ManterDividaFundadaOperacaoCredito";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$obErro = new Erro;
$stAcao = $request->get('stAcao');

$boFlagTransacao = false;
$obTransacao = new Transacao;

$obErro = $obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

if(!$obErro->ocorreu()){
    $arDividas  = Sessao::read('arDividas');
    
    $obTTCEPEDividaFundadaOperacaoCredito = new TTCEPEDividaFundadaOperacaoCredito();
    $obTTCEPEDividaFundadaOperacaoCredito->setDado('exercicio',$arDividas[0]['stExercicio']);
    $obTTCEPEDividaFundadaOperacaoCredito->setDado('cod_entidade',$arDividas[0]['inCodEntidade']);
    $obTTCEPEDividaFundadaOperacaoCredito->excluirDivida($boTransacao);
    
    foreach ($arDividas as $key => $value) {

        $obTTCEPEDividaFundadaOperacaoCredito = new TTCEPEDividaFundadaOperacaoCredito();
        $obTTCEPEDividaFundadaOperacaoCredito->setDado('exercicio'                      , $value['stExercicio']);
        $obTTCEPEDividaFundadaOperacaoCredito->setDado('cod_entidade'                   , $value['inCodEntidade']);
        $obTTCEPEDividaFundadaOperacaoCredito->setDado('tipo_operacao_credito'          , $value['inTipoOperacaoCredito']);
        $obTTCEPEDividaFundadaOperacaoCredito->setDado('cod_norma'                      , $value['inCodLeiAutorizacao']);
        $obTTCEPEDividaFundadaOperacaoCredito->setDado('dt_assinatura'                  , $value['stDataNorma']);
        $obTTCEPEDividaFundadaOperacaoCredito->setDado('num_contrato'                   , $value['inNumeroContrato']);
        $obTTCEPEDividaFundadaOperacaoCredito->setDado('vl_saldo_anterior_titulo'       , $value['vlSaldoAnteriorTitulo']);
        $obTTCEPEDividaFundadaOperacaoCredito->setDado('vl_inscricao_exercicio_titulo'  , $value['vlInscricaoExercicioTitulo']);
        $obTTCEPEDividaFundadaOperacaoCredito->setDado('vl_baixa_exercicio_titulo'      , $value['vlBaixaExercicioTitulo']);
        $obTTCEPEDividaFundadaOperacaoCredito->setDado('vl_saldo_anterior_contrato'     , $value['vlSaldoAnteriorContrato']);
        $obTTCEPEDividaFundadaOperacaoCredito->setDado('vl_inscricao_exercicio_contrato', $value['vlInscricaoExercicioContrato']);
        $obTTCEPEDividaFundadaOperacaoCredito->setDado('vl_baixa_exercicio_contrato'    , $value['vlBaixaExercicioContrato']);
        $obErro = $obTTCEPEDividaFundadaOperacaoCredito->inclusao($boTransacao);
    }
   
    if(!$obErro->ocorreu() ){
        Sessao::remove('arDividas');
        
        $obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro, $obTTCEPEDividaFundadaOperacaoCredito);
        $stLink = '&stExercicio='.$request->get('stExercicio')."&inCodEntidade=".$request->get('inCodEntidade');
        SistemaLegado::alertaAviso($pgFilt."?".Sessao::getId().$stLink,"Configuração de Dívida Fundada/Operacão de Crédito salva com sucesso","incluir","aviso", Sessao::getId(), "../");
    } else {
        SistemaLegado::exibeAviso(urlencode('Não foi possível salvar os dados inseridos.'),"n_incluir","erro");
    }
}
