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
  * Página de Processamento da Configuração Metas Fiscais
  * Data de Criação: 14/01/2014

  * @author Analista: Eduardo Schitz
  * @author Desenvolvedor: Franver Sarmento de Moraes

  * @ignore
  *
  * $Id: PRManterConfiguracaoMetasFiscais.php 64403 2016-02-17 12:33:38Z jean $

  * $Revision: 64403 $
  * $Name: $
  * $Author: jean $
  * $Date: 2016-02-17 10:33:38 -0200 (Wed, 17 Feb 2016) $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGMetasFiscais.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoMetasFiscais";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');
$obErro = new Erro;

switch ($stAcao) {
    default:
        $boFlagTransacao = false;
        $obTransacao = new Transacao;
        $rsTTCEMGMetasFiscais = new RecordSet();
        $obTTCEMGMetasFiscais = new TTCEMGMetasFiscais();
        $obErro = $obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

        if (!$obErro->ocorreu()) {
            $obTTCEMGMetasFiscais->setDado('exercicio'                                 , $request->get('stExercicio'));
            $obTTCEMGMetasFiscais->setDado('valor_corrente_receita_total'              , $request->get('flValorCorrenteReceitaTotal'));
            $obTTCEMGMetasFiscais->setDado('valor_corrente_receita_primaria'           , $request->get('flValorCorrenteReceitaPrimaria'));
            $obTTCEMGMetasFiscais->setDado('valor_corrente_despesa_total'              , $request->get('flValorCorrenteDespesaTotal'));
            $obTTCEMGMetasFiscais->setDado('valor_corrente_despesa_primaria'           , $request->get('flValorCorrenteDespesaPrimaria'));
            $obTTCEMGMetasFiscais->setDado('valor_corrente_resultado_primario'         , $request->get('flValorCorrenteResultadoPrimario'));
            $obTTCEMGMetasFiscais->setDado('valor_corrente_resultado_nominal'          , $request->get('flValorCorrenteResultadoNominal'));
            $obTTCEMGMetasFiscais->setDado('valor_corrente_divida_publica_consolidada' , $request->get('flValorCorrenteDividaPublicaConsolidada'));
            $obTTCEMGMetasFiscais->setDado('valor_corrente_divida_consolidada_liquida' , $request->get('flValorCorrenteDividaConsolidadaLiquida'));
            //
            $obTTCEMGMetasFiscais->setDado('valor_corrente_receita_primaria_adv'       , $request->get('flValorCorrenteReceitaPrimariaAdv'));
            $obTTCEMGMetasFiscais->setDado('valor_corrente_despesa_primaria_gerada'    , $request->get('flValorCorrenteDespesaPrimariaGerada'));
            //

            $obTTCEMGMetasFiscais->setDado('valor_constante_receita_total'             , $request->get('flValorConstanteReceitaTotal'));
            $obTTCEMGMetasFiscais->setDado('valor_constante_receita_primaria'          , $request->get('flValorConstanteReceitaPrimaria'));
            $obTTCEMGMetasFiscais->setDado('valor_constante_despesa_total'             , $request->get('flValorConstanteDespesaTotal'));
            $obTTCEMGMetasFiscais->setDado('valor_constante_despesa_primaria'          , $request->get('flValorConstanteDespesaPrimaria'));
            $obTTCEMGMetasFiscais->setDado('valor_constante_resultado_primario'        , $request->get('flValorConstanteDespesaResultadoPrimario'));
            $obTTCEMGMetasFiscais->setDado('valor_constante_resultado_nominal'         , $request->get('flValorConstanteResultadoNominal'));
            $obTTCEMGMetasFiscais->setDado('valor_constante_divida_publica_consolidada', $request->get('flValorConstanteDividaPublicaConsolidada'));
            $obTTCEMGMetasFiscais->setDado('valor_constante_divida_consolidada_liquida', $request->get('flValorConstanteDividaConsolidadaLiquida'));
            //
            $obTTCEMGMetasFiscais->setDado('valor_constante_receita_primaria_adv'      , $request->get('flValorConstanteReceitaDividaAdv'));
            $obTTCEMGMetasFiscais->setDado('valor_constante_despesa_primaria_gerada'   , $request->get('flValorConstanteDespesaPrimariaGerada'));
            //

            $obTTCEMGMetasFiscais->setDado('percentual_pib_receita_total'              , $request->get('flPercentualPIBReceitaTotal'));
            $obTTCEMGMetasFiscais->setDado('percentual_pib_receita_primaria'           , $request->get('flPercentualPIBReceitaPrimaria'));
            $obTTCEMGMetasFiscais->setDado('percentual_pib_despesa_total'              , $request->get('flPercentualPIBDespesaTotal'));
            $obTTCEMGMetasFiscais->setDado('percentual_pib_despesa_primaria'           , $request->get('flPercentualPIBDespesaPrimaria'));
            $obTTCEMGMetasFiscais->setDado('percentual_pib_resultado_primario'         , $request->get('flPercentualPIBResultadoPrimario'));
            $obTTCEMGMetasFiscais->setDado('percentual_pib_resultado_nominal'          , $request->get('flPercentualPIBResultadoNominal'));
            $obTTCEMGMetasFiscais->setDado('percentual_pib_divida_publica_consolidada' , $request->get('flPercentualPIBDividaPublicaConsolidada'));
            $obTTCEMGMetasFiscais->setDado('percentual_pib_divida_consolidada_liquida' , $request->get('flPercentualPIBDividaConsolidadaLiquida'));
            //
            $obTTCEMGMetasFiscais->setDado('percentual_pib_receita_primaria_adv'       , $request->get('flPercentualPIBReceitaPrimariaAdv'));
            $obTTCEMGMetasFiscais->setDado('percentual_pib_despesa_primaria_adv'       , $request->get('flPercentualPIBDespesaPrimariaAdv'));
            //

            $obTTCEMGMetasFiscais->recuperaPorChave($rsTTCEMGMetasFiscais,$boTransacao);

            if ($rsTTCEMGMetasFiscais->getNumLinhas() < 0) {
                $obErro = $obTTCEMGMetasFiscais->inclusao($boTransacao);
            } else {
                $obErro = $obTTCEMGMetasFiscais->alteracao($boTransacao);
            }

            if (!$obErro->ocorreu()) {
                SistemaLegado::alertaAviso($pgFilt."?".Sessao::getId(),"Configuração de Metas Fiscais","manter","aviso", Sessao::getId(), "../");
            } else {
                SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
            }

            $obTransacao->fechaTransacao($boFlagTransacao,$boTransacao,$obErro,$obTTCEMGMetasFiscais);
        }

        break;
}

?>
