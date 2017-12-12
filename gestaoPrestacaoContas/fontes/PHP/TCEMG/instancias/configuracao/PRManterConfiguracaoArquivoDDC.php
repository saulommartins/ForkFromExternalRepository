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
include_once(CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGConfiguracaoOrgao.class.php");
include_once( CAM_GPC_TCEMG_MAPEAMENTO.'TTCEMGConfiguracaoDDC.class.php' );

$link = Sessao::read("link");

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoArquivoDDC";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$obErro = new Erro;
$stAcao = $request->get('stAcao');

switch ($stAcao) {
    case 'configurar' :
        
        $boFlagTransacao = false;
        $obTransacao = new Transacao;
        $obTTCEMGConfiguracaoOrgao   = new TTCEMGConfiguracaoOrgao();
        $obTTCEMGConfiguracaoDDC     = new TTCEMGConfiguracaoDDC();
        
        $obErro = $obTransacao->abreTransacao($boFlagTransacao, $boTransacao);
        
        if(!$obErro->ocorreu()){
            
            $obTTCEMGConfiguracaoOrgao->setDado("cod_modulo", 55);
            $obTTCEMGConfiguracaoOrgao->setDado("parametro","tcemg_codigo_orgao_entidade_sicom");
            $obTTCEMGConfiguracaoOrgao->setDado("exercicio",Sessao::getExercicio());
            $obTTCEMGConfiguracaoOrgao->recuperaCodigos($rsOrgao," AND ent.cod_entidade  = ".$request->get('inCodEntidade')," ORDER BY ent.cod_entidade");
            
            $arDividas  = Sessao::read('arDividas');
            if(is_array($arDividas)){
                foreach ($arDividas as $key => $value) {
                    $obTTCEMGConfiguracaoDDC->setDado('exercicio', $arDividas[$key]['inExercicio']);
                    $obTTCEMGConfiguracaoDDC->setDado('mes_referencia',$arDividas[$key]['inMes']);
                    $obTTCEMGConfiguracaoDDC->setDado('cod_entidade', $arDividas[$key]['inCodEntidade']);
                    $obTTCEMGConfiguracaoDDC->setDado('nro_contrato_divida', $arDividas[$key]['inNumContratoDivida'] );
                    $obErro = $obTTCEMGConfiguracaoDDC->recuperaPorChave( $rsRecordSet, $boTransacao );
                       
                    if ( $rsRecordSet->eof() ) {
                        $obTTCEMGConfiguracaoDDC->setDado("exercicio",$arDividas[$key]['inExercicio']);
                        $obTTCEMGConfiguracaoDDC->setDado("mes_referencia",$arDividas[$key]['inMes']);
                        $obTTCEMGConfiguracaoDDC->setDado("cod_entidade",$arDividas[$key]['inCodEntidade']);
                        $obTTCEMGConfiguracaoDDC->setDado("cod_orgao",$rsOrgao->getCampo('valor'));
                        $obTTCEMGConfiguracaoDDC->setDado("cod_norma",$arDividas[$key]['inCodLeiAutorizacao']);
                        $obTTCEMGConfiguracaoDDC->setDado("nro_contrato_divida",$arDividas[$key]['inNumContratoDivida']);
                        $obTTCEMGConfiguracaoDDC->setDado("dt_assinatura",$arDividas[$key]['dtAssinaturaDivida']);
                        $obTTCEMGConfiguracaoDDC->setDado("contrato_dec_lei",$arDividas[$key]['stContratoDecLei']);
                        $obTTCEMGConfiguracaoDDC->setDado("objeto_contrato_divida",$arDividas[$key]['stObjetoContrato']);
                        $obTTCEMGConfiguracaoDDC->setDado("especificacao_contrato_divida",$arDividas[$key]['stDescDivida']);      
                        $obTTCEMGConfiguracaoDDC->setDado("tipo_lancamento",$arDividas[$key]['inTipoLancamento']);
                        $obTTCEMGConfiguracaoDDC->setDado("numcgm",$arDividas[$key]['inCGMCredor']);
                        $obTTCEMGConfiguracaoDDC->setDado("justificativa_cancelamento",$arDividas[$key]['stJustificativaCancelamento']);
                        $obTTCEMGConfiguracaoDDC->setDado("valor_saldo_anterior",$arDividas[$key]['flValorSaldoAnterior']);
                        $obTTCEMGConfiguracaoDDC->setDado("valor_contratacao",$arDividas[$key]['flValorContratacaoMes']);
                        $obTTCEMGConfiguracaoDDC->setDado("valor_amortizacao",$arDividas[$key]['flValorAmortizacaoMes']);
                        $obTTCEMGConfiguracaoDDC->setDado("valor_cancelamento",$arDividas[$key]['flValorCancelamentoMes']);
                        $obTTCEMGConfiguracaoDDC->setDado("valor_encampacao",$arDividas[$key]['flValorEncampacaoMes']);
                        $obTTCEMGConfiguracaoDDC->setDado("valor_atualizacao",$arDividas[$key]['flValorAtualizacaoMes']);
                        $obTTCEMGConfiguracaoDDC->setDado("valor_saldo_atual",$arDividas[$key]['flValorSaldoAtual']);
                        $obErro = $obTTCEMGConfiguracaoDDC->inclusao($boTransacao);
                    }  else{ 
                            SistemaLegado::exibeAviso("Arquivo DDC já foi incluído para o nro de contrato ".$arDividas[$key]['inNumContratoDivida']. " no mês ".$request->get('inMes') ." do exercício de ".$request->get('inExercicio'),"n_incluir","erro");
                      }
                }
              } else{
                    $obTTCEMGConfiguracaoDDC->setDado("exercicio",$request->get('inExercicio'));
                    $obTTCEMGConfiguracaoDDC->setDado("mes_referencia",$request->get('inMes'));
                    $obTTCEMGConfiguracaoDDC->setDado("cod_entidade",$request->get('inCodEntidade'));
                    $obTTCEMGConfiguracaoDDC->setDado("cod_orgao",$rsOrgao->getCampo('valor'));
                    $obTTCEMGConfiguracaoDDC->setDado("cod_norma",$request->get('inCodLeiAutorizacao'));
                    $obTTCEMGConfiguracaoDDC->setDado("nro_contrato_divida",$request->get('inNumContratoDivida'));
                    $obTTCEMGConfiguracaoDDC->setDado("dt_assinatura",$request->get('dtAssinaturaDivida'));
                    $obTTCEMGConfiguracaoDDC->setDado("contrato_dec_lei",$request->get('stContratoDecLei'));
                    $obTTCEMGConfiguracaoDDC->setDado("objeto_contrato_divida",$request->get('stObjetoContrato'));
                    $obTTCEMGConfiguracaoDDC->setDado("especificacao_contrato_divida",$request->get('stDescDivida'));      
                    $obTTCEMGConfiguracaoDDC->setDado("tipo_lancamento",$request->get('inTipoLancamento'));
                    $obTTCEMGConfiguracaoDDC->setDado("numcgm",$request->get('inCGMCredor'));
                    $obTTCEMGConfiguracaoDDC->setDado("justificativa_cancelamento",$request->get('stJustificativaCancelamento'));
                    $obTTCEMGConfiguracaoDDC->setDado("valor_saldo_anterior",$request->get('flValorSaldoAnterior'));
                    $obTTCEMGConfiguracaoDDC->setDado("valor_contratacao",$request->get('flValorContratacaoMes'));
                    $obTTCEMGConfiguracaoDDC->setDado("valor_amortizacao",$request->get('flValorAmortizacaoMes'));
                    $obTTCEMGConfiguracaoDDC->setDado("valor_cancelamento",$request->get('flValorCancelamentoMes'));
                    $obTTCEMGConfiguracaoDDC->setDado("valor_encampacao",$request->get('flValorEncampacaoMes'));
                    $obTTCEMGConfiguracaoDDC->setDado("valor_atualizacao",$request->get('flValorAtualizacaoMes'));
                    $obTTCEMGConfiguracaoDDC->setDado("valor_saldo_atual",$request->get('flValorSaldoAtual'));
                    $obErro = $obTTCEMGConfiguracaoDDC->inclusao($boTransacao);    
                }
           
           
            if(!$obErro->ocorreu() && $rsRecordSet->eof() ){
                Sessao::remove('arDividas');
                $obTransacao->fechaTransacao($boFlagTransacao,$boTransacao,$obErro,$obTTCEMGConfiguracaoDDC);
                $stLink = "&pg=".$link["pg"]."&pos=".$link["pos"].'&inExercicio='.$request->get('inExercicio').'&inMes='.$request->get('inMes')."&inCodEntidade=".$request->get('inCodEntidade');
                SistemaLegado::alertaAviso($pgList."?".Sessao::getId().$stLink,"Arquivo DDC incluido","incluir","aviso", Sessao::getId(), "../");
            }
        }
    break;
    
    case 'alterar' :
                
        $boFlagTransacao = false;
        
        $obTransacao = new Transacao;
        $obTransacao->begin();
        $boTransacao = $obTransacao->getTransacao();
        
        $obTTCEMGConfiguracaoOrgao   = new TTCEMGConfiguracaoOrgao();
        $obTTCEMGConfiguracaoDDC     = new TTCEMGConfiguracaoDDC();
        
        $obErro = $obTransacao->abreTransacao($boFlagTransacao, $boTransacao);
        
        if(!$obErro->ocorreu()){
            $obTTCEMGConfiguracaoOrgao->setDado("cod_modulo", 55);
            $obTTCEMGConfiguracaoOrgao->setDado("parametro","tcemg_codigo_orgao_entidade_sicom");
            $obTTCEMGConfiguracaoOrgao->recuperaCodigos($rsOrgao," AND ent.cod_entidade  = ".$request->get('hdnEntidade')," ORDER BY ent.cod_entidade");
           
            $obTTCEMGConfiguracaoDDC->setDado("exercicio",$request->get('inExercicio'));
            $obTTCEMGConfiguracaoDDC->setDado("mes_referencia",$request->get('inMes'));
            $obTTCEMGConfiguracaoDDC->setDado("cod_entidade",$request->get('hdnEntidade'));
            $obTTCEMGConfiguracaoDDC->setDado("cod_orgao",$rsOrgao->getCampo('valor'));
            $obTTCEMGConfiguracaoDDC->setDado("cod_norma",$request->get('inCodLeiAutorizacao'));
            $obTTCEMGConfiguracaoDDC->setDado("nro_contrato_divida",$request->get('hdnNroContrato'));
            $obTTCEMGConfiguracaoDDC->setDado("dt_assinatura",$request->get('dtAssinaturaDivida'));
            $obTTCEMGConfiguracaoDDC->setDado("contrato_dec_lei",$request->get('stContratoDecLei'));
            $obTTCEMGConfiguracaoDDC->setDado("objeto_contrato_divida",$request->get('stObjetoContrato'));
            $obTTCEMGConfiguracaoDDC->setDado("especificacao_contrato_divida",$request->get('stDescDivida'));      
            $obTTCEMGConfiguracaoDDC->setDado("tipo_lancamento",$request->get('inTipoLancamento'));
            $obTTCEMGConfiguracaoDDC->setDado("numcgm",$request->get('inCGMCredor'));
            $obTTCEMGConfiguracaoDDC->setDado("justificativa_cancelamento",$request->get('stJustificativaCancelamento'));
            $obTTCEMGConfiguracaoDDC->setDado("valor_saldo_anterior",$request->get('flValorSaldoAnterior'));
            $obTTCEMGConfiguracaoDDC->setDado("valor_contratacao",$request->get('flValorContratacaoMes'));
            $obTTCEMGConfiguracaoDDC->setDado("valor_amortizacao",$request->get('flValorAmortizacaoMes'));
            $obTTCEMGConfiguracaoDDC->setDado("valor_cancelamento",$request->get('flValorCancelamentoMes'));
            $obTTCEMGConfiguracaoDDC->setDado("valor_encampacao",$request->get('flValorEncampacaoMes'));
            $obTTCEMGConfiguracaoDDC->setDado("valor_atualizacao",$request->get('flValorAtualizacaoMes'));
            $obTTCEMGConfiguracaoDDC->setDado("valor_saldo_atual",$request->get('flValorSaldoAtual'));
            $obErro = $obTTCEMGConfiguracaoDDC->alteracao($boTransacao);
                                                
            if(!$obErro->ocorreu()){
                $obTransacao->fechaTransacao($boFlagTransacao,$boTransacao,$obErro,$obTTCEMGConfiguracaoDDC);
                $stLink = "&pg=".$link["pg"]."&pos=".$link["pos"].'&inExercicio='.$request->get('inExercicio').'&inMes='.$request->get('inMes')."&inCodEntidade=".$request->get('hdnEntidade');
                SistemaLegado::alertaAviso($pgList."?".Sessao::getId().$stLink,"Arquivo DDC alterado","alterar","aviso", Sessao::getId(), "../");
            }else{
                SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
            }
        }
    break;

    case 'excluir':
        $boFlagTransacao = false;
        
        $obTransacao = new Transacao;
        $obTransacao->begin();
        $boTransacao = $obTransacao->getTransacao();
        
        $obTTCEMGConfiguracaoOrgao   = new TTCEMGConfiguracaoOrgao();
        $obTTCEMGConfiguracaoDDC     = new TTCEMGConfiguracaoDDC();
        
        $obErro = $obTransacao->abreTransacao($boFlagTransacao, $boTransacao);
        
        if(!$obErro->ocorreu()){
            
            $obTTCEMGConfiguracaoDDC->setDado("exercicio",$request->get('inExercicio'));
            $obTTCEMGConfiguracaoDDC->setDado("mes_referencia",$request->get('inMes'));
            $obTTCEMGConfiguracaoDDC->setDado("cod_entidade",$request->get('inCodEntidade'));
            $obTTCEMGConfiguracaoDDC->setDado("nro_contrato_divida",$request->get('inNroContrato'));
            $obErro = $obTTCEMGConfiguracaoDDC->exclusao($boTransacao);
                        
            if( !$obErro->ocorreu() ){
                $obTransacao->fechaTransacao($boFlagTransacao,$boTransacao,$obErro,$obTTCEMGConfiguracaoDDC);
                $stLink = "&pg=".$link["pg"]."&pos=".$link["pos"].'&inExercicio='.$request->get('inExercicio').'&inMes='.$request->get('inMes')."&inCodEntidade=".$request->get('inCodEntidade');
                SistemaLegado::alertaAviso($pgList."?".Sessao::getId().$stLink,"Arquivo DDC incluido","incluir","aviso", Sessao::getId(), "../");
	    }else{
		SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
	    }
        }
        
    break;
    
}

?>