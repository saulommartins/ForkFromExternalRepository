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
  * Data de Criação: 21/02/2014
  
  * @author Analista: Eduardo Schitz
  * @author Desenvolvedor: Franver Sarmento de Moraes
  
  * @ignore
  *
  * $Id: PRManterConfiguracaoArquivoDCLRF.php 64864 2016-04-08 17:03:33Z evandro $
  
  * $Revision: 64864 $
  * $Author: evandro $
  * $Date: 2016-04-08 14:03:33 -0300 (Fri, 08 Apr 2016) $
  
*/

include_once("../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php");
include_once("../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php");
include_once(CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGConfiguracaoArquivoDCLRF.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoArquivoDCLRF";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";


$stAcao = $request->get('stAcao');
$obErro = new Erro;

switch ($stAcao){
    default:
        $boFlagTransacao = false;
        $obTransacao = new Transacao;
        $rsTTCEMGConfiguracaoArquivoDCLRF = new RecordSet();
        $obTTCEMGConfiguracaoArquivoDCLRF = new TTCEMGConfiguracaoArquivoDCLRF();
        $obErro = $obTransacao->abreTransacao($boFlagTransacao, $boTransacao);
        
        if(!$obErro->ocorreu()) {
            if( Sessao::getExercicio() >= '2016' ){
                $nuValorAtualConcessoesGarantia = $request->get('flValorSaldoAtualConcessoesGarantiaExterna');
            }else{
                $nuValorAtualConcessoesGarantia = $request->get('flValorSaldoAtualConcessoesGarantia');
            }
            $obTTCEMGConfiguracaoArquivoDCLRF->setDado('exercicio',$request->get('stExercicio'));
            $obTTCEMGConfiguracaoArquivoDCLRF->setDado('mes_referencia',$request->get('stMes'));
            $obTTCEMGConfiguracaoArquivoDCLRF->setDado('valor_saldo_atual_concessoes_garantia',$nuValorAtualConcessoesGarantia );
            $obTTCEMGConfiguracaoArquivoDCLRF->setDado('receita_privatizacao',$request->get('flValorReceitaPrivatizacao'));
            $obTTCEMGConfiguracaoArquivoDCLRF->setDado('valor_liquidado_incentivo_contribuinte',$request->get('flValorLiquidadoIncentivoContribuinte'));
            $obTTCEMGConfiguracaoArquivoDCLRF->setDado('valor_liquidado_incentivo_instituicao_financeira',$request->get('flValorLiquidadoIncentivoInstituicaoFinanceiro'));
            $obTTCEMGConfiguracaoArquivoDCLRF->setDado('valor_inscrito_rpnp_incentivo_contribuinte',$request->get('flValorInscritoRPNPIncentivoContribuinte'));
            $obTTCEMGConfiguracaoArquivoDCLRF->setDado('valor_inscrito_rpnp_incentivo_instituicao_financeira',$request->get('flValorInscritoRPNPIncentivoInstituicaoFinanceiro'));
            $obTTCEMGConfiguracaoArquivoDCLRF->setDado('valor_compromissado',$request->get('flValorCompromissado'));
            $obTTCEMGConfiguracaoArquivoDCLRF->setDado('valor_recursos_nao_aplicados',$request->get('flValorRecursosNaoAplicados'));
            $obTTCEMGConfiguracaoArquivoDCLRF->setDado('publicacao_relatorio_lrf', $request->get('inPublicacaoRelatorioLRF'));
            $obTTCEMGConfiguracaoArquivoDCLRF->setDado('dt_publicacao_relatorio_lrf', $request->get('dtPublicacaoRelatorioLRF'));
            $obTTCEMGConfiguracaoArquivoDCLRF->setDado('bimestre', $request->get('inBimestre'));
            $obTTCEMGConfiguracaoArquivoDCLRF->setDado('meta_bimestral', $request->get('inMetaBimestral'));
            $obTTCEMGConfiguracaoArquivoDCLRF->setDado('medida_adotada', $request->get('stMedidasAdotadas'));            
            
            if( Sessao::getExercicio() >= '2016' ){
                $obTTCEMGConfiguracaoArquivoDCLRF->setDado('valor_saldo_atual_concessoes_garantia_interna',$request->get('flValorSaldoAtualConcessoesGarantiaInterna'));
                $obTTCEMGConfiguracaoArquivoDCLRF->setDado('valor_saldo_atual_contra_concessoes_garantia_interna',$request->get('flValorSaldoAtualContraConcessoesGarantiaInterna'));
                $obTTCEMGConfiguracaoArquivoDCLRF->setDado('valor_saldo_atual_contra_concessoes_garantia_externa',$request->get('flValorSaldoAtualContraConcessoesGarantiaExterna'));
                $obTTCEMGConfiguracaoArquivoDCLRF->setDado('medidas_corretivas',$request->get('stMedidasCorretivas'));
            }
            
            //Seta o valor atual para os campos abaixo caso não seja o mês de dezembro
            $obTTCEMGConfiguracaoArquivoDCLRF->recuperaPorChave($rsTTCEMGConfiguracaoArquivoDCLRF);
            
            if($request->get('stMes') == 12) {
                $obTTCEMGConfiguracaoArquivoDCLRF->setDado('cont_op_credito', $request->get('inContOpCredito'));
                $obTTCEMGConfiguracaoArquivoDCLRF->setDado('desc_cont_op_credito', $request->get('stDescContOpCredito'));
                $obTTCEMGConfiguracaoArquivoDCLRF->setDado('realiz_op_credito', $request->get('inRealizOpCredito'));
                $obTTCEMGConfiguracaoArquivoDCLRF->setDado('tipo_realiz_op_credito_capta', $request->get('inTipoRealizOpCreditoCapta'));
                $obTTCEMGConfiguracaoArquivoDCLRF->setDado('tipo_realiz_op_credito_receb', $request->get('inTipoRealizOpCreditoReceb'));
                $obTTCEMGConfiguracaoArquivoDCLRF->setDado('tipo_realiz_op_credito_assun_dir', $request->get('inTipoRealizOpCreditoAssunDir'));
                $obTTCEMGConfiguracaoArquivoDCLRF->setDado('tipo_realiz_op_credito_assun_obg', $request->get('inTipoRealizOpCreditoAssunObg'));
            } else {
                $obTTCEMGConfiguracaoArquivoDCLRF->setDado('cont_op_credito', $rsTTCEMGConfiguracaoArquivoDCLRF->getCampo('cont_op_credito'));
                $obTTCEMGConfiguracaoArquivoDCLRF->setDado('desc_cont_op_credito', $rsTTCEMGConfiguracaoArquivoDCLRF->getCampo('desc_cont_op_credito'));
                $obTTCEMGConfiguracaoArquivoDCLRF->setDado('realiz_op_credito', $rsTTCEMGConfiguracaoArquivoDCLRF->getCampo('realiz_op_credito'));
                $obTTCEMGConfiguracaoArquivoDCLRF->setDado('tipo_realiz_op_credito_capta', $rsTTCEMGConfiguracaoArquivoDCLRF->getCampo('tipo_realiz_op_credito_capta'));
                $obTTCEMGConfiguracaoArquivoDCLRF->setDado('tipo_realiz_op_credito_receb', $rsTTCEMGConfiguracaoArquivoDCLRF->getCampo('tipo_realiz_op_credito_receb'));
                $obTTCEMGConfiguracaoArquivoDCLRF->setDado('tipo_realiz_op_credito_assun_dir', $rsTTCEMGConfiguracaoArquivoDCLRF->getCampo('tipo_realiz_op_credito_assun_dir'));
                $obTTCEMGConfiguracaoArquivoDCLRF->setDado('tipo_realiz_op_credito_assun_obg', $rsTTCEMGConfiguracaoArquivoDCLRF->getCampo('tipo_realiz_op_credito_assun_obg'));
            }
            
            if($rsTTCEMGConfiguracaoArquivoDCLRF->getNumLinhas() < 0) {
                $obErro = $obTTCEMGConfiguracaoArquivoDCLRF->inclusao($boTransacao);
            } else {
                $obErro = $obTTCEMGConfiguracaoArquivoDCLRF->alteracao($boTransacao);
            }

            if(!$obErro->ocorreu()) {
                SistemaLegado::alertaAviso($pgFilt."?".Sessao::getId(),"Configuração de Dados Complementares à LRF","manter","aviso", Sessao::getId(), "../");
            } else {
                SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
            }
            
            $obTransacao->fechaTransacao($boFlagTransacao,$boTransacao,$obErro,$obTTCEMGConfiguracaoArquivoDCLRF);
        }
        
        break;
}

?>
