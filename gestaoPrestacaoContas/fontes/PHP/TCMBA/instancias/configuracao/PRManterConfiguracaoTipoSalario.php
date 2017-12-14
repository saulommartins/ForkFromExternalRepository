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
  * Página de Formulário de Configuração de Tipos de Salários
  * Data de Criação: 28/10/2015

  * @author Analista: Dagiane Vieira
  * @author Desenvolvedor: Arthur Cruz
  * @ignore
  *
  * $Id: $
  * $Revision: $
  * $Author: $
  * $Date: $
*/
require_once "../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php";
require_once "../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php";
include_once CAM_GPC_TCMBA_MAPEAMENTO.'TTCMBACargoServidor.class.php';
include_once CAM_GPC_TCMBA_MAPEAMENTO.'TTCMBACargoServidorTemporario.class.php';
include_once CAM_GPC_TCMBA_MAPEAMENTO.'TTCMBAEmprestimoConsignado.class.php';
include_once CAM_GPC_TCMBA_MAPEAMENTO.'TTCMBASalarioBase.class.php';
include_once CAM_GPC_TCMBA_MAPEAMENTO.'TTCMBAVantagensSalariais.class.php';
include_once CAM_GPC_TCMBA_MAPEAMENTO.'TTCMBAGratificacaoFuncao.class.php';
include_once CAM_GPC_TCMBA_MAPEAMENTO.'TTCMBASalarioFamilia.class.php';
include_once CAM_GPC_TCMBA_MAPEAMENTO.'TTCMBASalarioHorasExtras.class.php';
include_once CAM_GPC_TCMBA_MAPEAMENTO.'TTCMBASalarioDescontos.class.php';
include_once CAM_GPC_TCMBA_MAPEAMENTO.'TTCMBAPlanoSaude.class.php';
include_once CAM_GPC_TCMBA_MAPEAMENTO.'TTCMBAFonteRecursoLotacao.class.php';
include_once CAM_GPC_TCMBA_MAPEAMENTO.'TTCMBAFonteRecursoLocal.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoTipoSalario";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

$obErro = new Erro();
$boFlagTransacao = false;
$obTransacao = new Transacao();
$stAcao      = $request->get('stAcao');

switch ($stAcao) {
    case 'configurar' :

        $obErro = $obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

        if (!$obErro->ocorreu()) {
            
            /*** Tipo Função Servidor ***/            
            $arFuncaoCargoServidorSessao = Sessao::read('arFuncaoCargoServidor');
            
            $obTTCMBACargoServidor = new TTCMBACargoServidor();
            $obTTCMBACargoServidor->setDado('exercicio'   , Sessao::getExercicio());
            $obTTCMBACargoServidor->setDado('cod_entidade', $request->get('inCodEntidade'));
            $obErro = $obTTCMBACargoServidor->exclusao($boTransacao);

            if (!$obErro->ocorreu()) {
                if( is_array($arFuncaoCargoServidorSessao) ){
                    foreach ($arFuncaoCargoServidorSessao as $arFuncaoCargoServidorSessaoTmp) {
                        foreach ($arFuncaoCargoServidorSessaoTmp["cargos"] as $arCargoSelecionado) {
                            $obTTCMBACargoServidor->setDado('cod_tipo_funcao' , $arFuncaoCargoServidorSessaoTmp["cod_tipo_funcao"]);
                            $obTTCMBACargoServidor->setDado('cod_cargo'       , $arCargoSelecionado["cod_cargo"]);
                            $obErro = $obTTCMBACargoServidor->inclusao($boTransacao);
                            if ( $obErro->ocorreu() ) {
                               SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
                               break;
                            }
                        }
                    }
                }
            } else {
                SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
            }

            /*** Tipo Função Servidor Temporario ***/
            $arFuncaoCargoServidorTemporarioSessao = Sessao::read('arFuncaoCargoServidorTemporario');
            
            $obTTCMBACargoServidorTemporario = new TTCMBACargoServidorTemporario();
            $obTTCMBACargoServidorTemporario->setDado('exercicio'   , Sessao::getExercicio());
            $obTTCMBACargoServidorTemporario->setDado('cod_entidade', $request->get('inCodEntidade'));
            $obErro = $obTTCMBACargoServidorTemporario->exclusao($boTransacao);
            
            if (!$obErro->ocorreu()) {
                if( is_array($arFuncaoCargoServidorTemporarioSessao) ) {
                    foreach ($arFuncaoCargoServidorTemporarioSessao as $arFuncaoCargoServidorSessaoTemporarioTmp) {
                        foreach ($arFuncaoCargoServidorSessaoTemporarioTmp["cargos"] as $arCargoSelecionado) {
                            $obTTCMBACargoServidorTemporario->setDado('cod_tipo_funcao' , $arFuncaoCargoServidorSessaoTemporarioTmp["cod_tipo_funcao"]);
                            $obTTCMBACargoServidorTemporario->setDado('cod_cargo'       , $arCargoSelecionado["cod_cargo"]);
                            $obErro = $obTTCMBACargoServidorTemporario->inclusao($boTransacao);
                            if ( $obErro->ocorreu() ) {
                               SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
                               break;
                            }
                        }
                    }
                }
            } else {
               SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro"); 
            }
            
            /*** Bancos Emprestimos ***/
            $arBancoEventosEmprestimoSessao = Sessao::read('arBancoEventosEmprestimo');
            
            $obTTCMBAEmprestimoConsignado = new TTCMBAEmprestimoConsignado();
            $obTTCMBAEmprestimoConsignado->setDado('exercicio'   , Sessao::getExercicio());
            $obTTCMBAEmprestimoConsignado->setDado('cod_entidade', $request->get('inCodEntidade'));
            $obErro = $obTTCMBAEmprestimoConsignado->exclusao($boTransacao);
            
            if (!$obErro->ocorreu()) {
                if ( is_array($arBancoEventosEmprestimoSessao) ) {
                    foreach ($arBancoEventosEmprestimoSessao as $arBancoEventosEmprestimoSessaoTmp) {
                        foreach ($arBancoEventosEmprestimoSessaoTmp["eventos"] as $arEventoSelecionado) {
                            $obTTCMBAEmprestimoConsignado->setDado('cod_banco'  , $arBancoEventosEmprestimoSessaoTmp["cod_banco"]);
                            $obTTCMBAEmprestimoConsignado->setDado('cod_evento' , $arEventoSelecionado["cod_evento"]);
                            $obErro = $obTTCMBAEmprestimoConsignado->inclusao($boTransacao);
                            if ( $obErro->ocorreu() ) {
                               SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
                               break;
                            }
                        }
                    }
                }
            } else {
                SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro"); 
            }
            
            /*** Salário Base ***/
            $arSalarioBaseSelecionados = $request->get('arSalarioBaseSelecionados');
            
            $obTTCMBASalarioBase = new TTCMBASalarioBase();
            $obTTCMBASalarioBase->setDado('exercicio'   , Sessao::getExercicio());
            $obTTCMBASalarioBase->setDado('cod_entidade', $request->get('inCodEntidade'));
            $obErro = $obTTCMBASalarioBase->exclusao($boTransacao);
            
            if (!$obErro->ocorreu()) {
                if ( isset($arSalarioBaseSelecionados) ) {
                    foreach ($arSalarioBaseSelecionados as $arSalarioBaseValor) {
                        $obTTCMBASalarioBase->setDado('cod_evento', $arSalarioBaseValor);
                        $obErro = $obTTCMBASalarioBase->inclusao($boTransacao);
                        if ( $obErro->ocorreu() ) {
                            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
                            break;
                        }
                    }
                }
            } else {
               SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");  
            }
            
            /*** Demais Vantagens Salariais ***/
            $arVantagensSalariaisSelecionados = $request->get('arVantagensSalariaisSelecionados');
            
            $obTTCMBAVantagensSalariais = new TTCMBAVantagensSalariais();
            $obTTCMBAVantagensSalariais->setDado('exercicio'   , Sessao::getExercicio());
            $obTTCMBAVantagensSalariais->setDado('cod_entidade', $request->get('inCodEntidade'));
            $obErro = $obTTCMBAVantagensSalariais->exclusao($boTransacao);
            
            if (!$obErro->ocorreu()) {
                if ( isset($arVantagensSalariaisSelecionados) ) {
                    foreach ($arVantagensSalariaisSelecionados as $arVantagensSalariais) {
                        $obTTCMBAVantagensSalariais->setDado('cod_evento', $arVantagensSalariais);
                        $obErro = $obTTCMBAVantagensSalariais->inclusao($boTransacao);
                        if ( $obErro->ocorreu() ) {
                            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
                            break;
                        }
                    }
                }
            } else {
               SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro"); 
            }
            
            /*** Gratificação de função ***/
            $arGratificacaoFuncaoSelecionados = $request->get('arGratificacaoFuncaoSelecionados');
            
            $obTTCMBAGratificacaoFuncao = new TTCMBAGratificacaoFuncao();
            $obTTCMBAGratificacaoFuncao->setDado('exercicio'   , Sessao::getExercicio());
            $obTTCMBAGratificacaoFuncao->setDado('cod_entidade', $request->get('inCodEntidade'));
            $obErro = $obTTCMBAGratificacaoFuncao->exclusao($boTransacao);
            
            if (!$obErro->ocorreu()) {
                if ( isset($arGratificacaoFuncaoSelecionados) ) {
                    foreach ($arGratificacaoFuncaoSelecionados as $arGratificacaoFuncao) {
                        $obTTCMBAGratificacaoFuncao->setDado('cod_evento', $arGratificacaoFuncao);
                        $obErro = $obTTCMBAGratificacaoFuncao->inclusao($boTransacao);
                        if ( $obErro->ocorreu() ) {
                            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
                            break;
                        }
                    }
                }
            } else {
                SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro"); 
            }
            
            /*** Salário Família ***/
            $arSalarioFamiliaSelecionados = $request->get('arSalarioFamiliaSelecionados');
            
            $obTTCMBASalarioFamilia = new TTCMBASalarioFamilia();
            $obTTCMBASalarioFamilia->setDado('exercicio'   , Sessao::getExercicio());
            $obTTCMBASalarioFamilia->setDado('cod_entidade', $request->get('inCodEntidade'));
            $obErro = $obTTCMBASalarioFamilia->exclusao($boTransacao);
            
            if (!$obErro->ocorreu()) {
                if ( isset($arSalarioFamiliaSelecionados) ) {
                    foreach ($arSalarioFamiliaSelecionados as $arSalarioFamilia) {
                        $obTTCMBASalarioFamilia->setDado('cod_evento', $arSalarioFamilia);
                        $obErro = $obTTCMBASalarioFamilia->inclusao($boTransacao);
                        if ( $obErro->ocorreu() ) {
                            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
                            break;
                        }
                    }
                }
            } else {
                SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro"); 
            }
            
            /*** Horas Extras trabalhadas ***/
            $arHorasExtrasSelecionados = $request->get('arHorasExtrasSelecionados');
            
            $obTTCMBASalarioHorasExtras = new TTCMBASalarioHorasExtras();
            $obTTCMBASalarioHorasExtras->setDado('exercicio'   , Sessao::getExercicio());
            $obTTCMBASalarioHorasExtras->setDado('cod_entidade', $request->get('inCodEntidade'));
            $obErro = $obTTCMBASalarioHorasExtras->exclusao($boTransacao);
            
            if (!$obErro->ocorreu()) {
                if ( isset($arHorasExtrasSelecionados) ) {
                    foreach ($arHorasExtrasSelecionados as $arHorasExtras) {
                        $obTTCMBASalarioHorasExtras->setDado('cod_evento', $arHorasExtras);
                        $obErro = $obTTCMBASalarioHorasExtras->inclusao($boTransacao);
                        if ( $obErro->ocorreu() ) {
                            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
                            break;
                        }
                    }
                }
            } else {
                SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro"); 
            }
            
            /*** Demais Descontos ***/
            $arDemaisDescontosSelecionados = $request->get('arDemaisDescontosSelecionados');
            
            $obTTCMBASalarioDescontos = new TTCMBASalarioDescontos();
            $obTTCMBASalarioDescontos->setDado('exercicio'   , Sessao::getExercicio());
            $obTTCMBASalarioDescontos->setDado('cod_entidade', $request->get('inCodEntidade'));
            $obErro = $obTTCMBASalarioDescontos->exclusao($boTransacao);
            
            if (!$obErro->ocorreu()) {
                if ( isset($arDemaisDescontosSelecionados) ) {
                    foreach ($arDemaisDescontosSelecionados as $arDemaisDescontos) {
                        $obTTCMBASalarioDescontos->setDado('cod_evento', $arDemaisDescontos);
                        $obErro = $obTTCMBASalarioDescontos->inclusao($boTransacao);
                        if ( $obErro->ocorreu() ) {
                            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
                            break;
                        }
                    }
                }
            } else {
                SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
            }

            /*** Plano de Saúde/Odontológico ***/
            $arPlanoSaudeSelecionados = $request->get('arPlanoSaudeSelecionados');
            
            $obTTCMBAPlanoSaude = new TTCMBAPlanoSaude();
            $obTTCMBAPlanoSaude->setDado('exercicio'   , Sessao::getExercicio());
            $obTTCMBAPlanoSaude->setDado('cod_entidade', $request->get('inCodEntidade'));
            $obErro = $obTTCMBAPlanoSaude->exclusao($boTransacao);
            
            if (!$obErro->ocorreu()) {
                if ( isset($arPlanoSaudeSelecionados) ) {
                    foreach ($arPlanoSaudeSelecionados as $arPlanoSaude) {
                        $obTTCMBAPlanoSaude->setDado('cod_evento', $arPlanoSaude);
                        $obErro = $obTTCMBAPlanoSaude->inclusao($boTransacao);
                        if ( $obErro->ocorreu() ) {
                            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
                            break;
                        }
                    }
                }
            } else {
                SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
            }

            /*** Classe/Aplicação do Salário do Servidor ***/
            $arFonteRecursoLotacaoLocal = Sessao::read('arFonteRecursoLotacaoLocal');
            
            $obTTCMBAFonteRecursoLotacao = new TTCMBAFonteRecursoLotacao();
            $obTTCMBAFonteRecursoLotacao->setDado('exercicio'   , Sessao::getExercicio());
            $obTTCMBAFonteRecursoLotacao->setDado('cod_entidade', $request->get('inCodEntidade'));
            $obErro = $obTTCMBAFonteRecursoLotacao->exclusao($boTransacao);
            
            if (!$obErro->ocorreu()) {
                $oTTCMBAFonteRecursoLocal = new TTCMBAFonteRecursoLocal();
                $oTTCMBAFonteRecursoLocal->setDado('exercicio'   , Sessao::getExercicio());
                $oTTCMBAFonteRecursoLocal->setDado('cod_entidade', $request->get('inCodEntidade'));
                $obErro = $oTTCMBAFonteRecursoLocal->exclusao($boTransacao);

                if( is_array($arFonteRecursoLotacaoLocal) ){
                    foreach ($arFonteRecursoLotacaoLocal as $arFonteRecursoLotacaoLocalTmp) {
                        foreach ($arFonteRecursoLotacaoLocalTmp["lotacao"] as $arLotacaoSelecionado) {
                            $obTTCMBAFonteRecursoLotacao->setDado('cod_tipo_fonte' , $arFonteRecursoLotacaoLocalTmp["cod_tipo_fonte"]);
                            $obTTCMBAFonteRecursoLotacao->setDado('cod_orgao'      , $arLotacaoSelecionado["cod_orgao"]);
                            $obErro = $obTTCMBAFonteRecursoLotacao->inclusao($boTransacao);
                            if ( $obErro->ocorreu() ) {
                               SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
                               break;
                            }
                        }
    
                        if ( !empty($arFonteRecursoLotacaoLocalTmp["local"]) )
                            foreach ($arFonteRecursoLotacaoLocalTmp["local"] as $arLocalSelecionado) {
                                $oTTCMBAFonteRecursoLocal->setDado('cod_tipo_fonte' , $arFonteRecursoLotacaoLocalTmp["cod_tipo_fonte"]);
                                $oTTCMBAFonteRecursoLocal->setDado('cod_local'      , $arLocalSelecionado["cod_local"]);
                                $obErro = $oTTCMBAFonteRecursoLocal->inclusao($boTransacao);
                                if ( $obErro->ocorreu() ) {
                                    SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
                                    break;
                                }
                            }
                    }
                }
            } else {
                SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
            }

            if ( !$obErro->ocorreu() ) {
                $obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro, $obTTCMBACargoServidor);
                SistemaLegado::alertaAviso($pgFilt."?".Sessao::getId()."&stAcao=".$stAcao,"Configuração ","incluir","incluir_n", Sessao::getId(), "../");
            }

        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
    
    break;       
}

?>