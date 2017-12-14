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
  * Página de Processamento de Configuração dos Tipos de Cargos Remuneracao
  * Data de Criação: 16/03/2016

  * @author Analista: Dagiane Vieira
  * @author Desenvolvedor: Evandro Melos
  * @ignore
  *
  * $Id: $
  * $Revision: $
  * $Author: $
  * $Date: $
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GPC_TCEMG_MAPEAMENTO.Sessao::getExercicio().'/TFolhaPagamentoTCEMGEntidadeRequisitosCargo.class.php';
include_once CAM_GPC_TCEMG_MAPEAMENTO.Sessao::getExercicio().'/TFolhaPagamentoTCEMGEntidadeRemuneracao.class.php';
include_once CAM_GPC_TCEMG_MAPEAMENTO.Sessao::getExercicio().'/TFolhaPagamentoTCEMGEntidadeCargoServidor.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoTipoCargoRemuneracao";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

$obErro = new Erro();

$arRegimeSubDivisao = Sessao::read('arRegimeSubDivisao');
$arRequisitosCargos = Sessao::read('arRequisitosCargos');
$arEventos = Sessao::read('arEventos');

$obTransacao = new Transacao();
$obErro = $obTransacao->abreTransacao($boFlagTransacao,$boTransacao);

//-------------------------------------------------------
//EXCLUSOES DAS CONFIGURACOES 
//-------------------------------------------------------
if ( !$obErro->ocorreu() ) {
    //folhapagamento.tcemg_entidade_cargo_servidor
    $obTFolhaPagamentoTCEMGEntidadeCargoServidor = new TFolhaPagamentoTCEMGEntidadeCargoServidor();
    $obTFolhaPagamentoTCEMGEntidadeCargoServidor->setDado('exercicio',Sessao::getExercicio() );
    if ( count($arRegimeSubDivisao) > 0 ){
        foreach ($arRegimeSubDivisao as $key => $value) {
            if ( !$obErro->ocorreu() ) {
                $obTFolhaPagamentoTCEMGEntidadeCargoServidor->setDado('cod_tipo',$key);
                $obErro = $obTFolhaPagamentoTCEMGEntidadeCargoServidor->exclusao($boTransacao);
            }
        }
    }else{ //caso o usuario tenha limpado todos os dados cadastrados
        $obErro = $obTFolhaPagamentoTCEMGEntidadeCargoServidor->exclusao($boTransacao);
    }
}

if ( !$obErro->ocorreu() ) {
    //folhapagamento.tcemg_entidade_requisitos_cargo
    $obTFolhaPagamentoTCEMGEntidadeRequisitosCargo = new TFolhaPagamentoTCEMGEntidadeRequisitosCargo();
    $obTFolhaPagamentoTCEMGEntidadeRequisitosCargo->setDado('exercicio',Sessao::getExercicio() );
    if ( count($arRequisitosCargos) > 0 ){    
        foreach ($arRequisitosCargos as $key => $value) {
            if ( !$obErro->ocorreu() ) {
                $obTFolhaPagamentoTCEMGEntidadeRequisitosCargo->setDado('cod_tipo',$key);
                $obErro = $obTFolhaPagamentoTCEMGEntidadeRequisitosCargo->exclusao($boTransacao);
            }
        }
    }else{//caso o usuario tenha limpado todos os dados cadastrados
        $obErro = $obTFolhaPagamentoTCEMGEntidadeRequisitosCargo->exclusao($boTransacao);
    }
}
if ( !$obErro->ocorreu() ) {
    //folhapagamento.tcemg_entidade_remuneracao
    $obTFolhaPagamentoTCEMGEntidadeRemuneracao = new TFolhaPagamentoTCEMGEntidadeRemuneracao();
    $obTFolhaPagamentoTCEMGEntidadeRemuneracao->setDado('exercicio',Sessao::getExercicio() );    
    if ( count($arEventos) > 0 ){    
        foreach ($arEventos as $key => $value) {
            if ( !$obErro->ocorreu() ) {
                $obTFolhaPagamentoTCEMGEntidadeRemuneracao->setDado('cod_tipo',$key);
                $obErro = $obTFolhaPagamentoTCEMGEntidadeRemuneracao->exclusao($boTransacao);
            }
        }
    }else{//caso o usuario tenha limpado todos os dados cadastrados
        $obErro = $obTFolhaPagamentoTCEMGEntidadeRemuneracao->exclusao($boTransacao);
    }
}
//-------------------------------------------------------
//INSERT DAS CONFIGURACOES
//-------------------------------------------------------

if ( !$obErro->ocorreu() ) {
    if ( !$obErro->ocorreu() ) {
        //folhapagamento.tcemg_entidade_cargo_servidor
        if ( count($arRegimeSubDivisao) > 0 ){
            foreach ($arRegimeSubDivisao as $codTipoRegime => $subDivisao ) {    
                foreach ($subDivisao as $codSubDivisao => $cargo) {
                    foreach ($cargo as $codCargo => $value) {            
                        if (!$obErro->ocorreu()) {
                            $obTFolhaPagamentoTCEMGEntidadeCargoServidor->setDado('exercicio'      , Sessao::getExercicio() );
                            $obTFolhaPagamentoTCEMGEntidadeCargoServidor->setDado('cod_tipo'       , $codTipoRegime );
                            $obTFolhaPagamentoTCEMGEntidadeCargoServidor->setDado('cod_sub_divisao', $codSubDivisao );
                            $obTFolhaPagamentoTCEMGEntidadeCargoServidor->setDado('cod_cargo'      , $codCargo );
                            $obErro = $obTFolhaPagamentoTCEMGEntidadeCargoServidor->inclusao($boTransacao);
                        }
                    }
                }
            }
        }
    }

    if ( !$obErro->ocorreu() ) {
        //folhapagamento.tcemg_entidade_requisitos_cargo
        if ( count($arRequisitosCargos) > 0 ){
            foreach ($arRequisitosCargos as $codTipoRequisito => $cargo ) {    
                foreach ($cargo as $codCargo => $value) {
                    if (!$obErro->ocorreu()) {
                        $obTFolhaPagamentoTCEMGEntidadeRequisitosCargo->setDado('exercicio'      , Sessao::getExercicio() );
                        $obTFolhaPagamentoTCEMGEntidadeRequisitosCargo->setDado('cod_tipo'       , $codTipoRequisito );                    
                        $obTFolhaPagamentoTCEMGEntidadeRequisitosCargo->setDado('cod_cargo'      , $codCargo );
                        $obErro = $obTFolhaPagamentoTCEMGEntidadeRequisitosCargo->inclusao($boTransacao);
                    }
                }
            }
        }
    }

    if ( !$obErro->ocorreu() ) {
        //folhapagamento.tcemg_entidade_remuneracao
        if ( count($arRequisitosCargos) > 0 ){
            foreach ($arEventos as $codRemuneracao => $evento ) {    
                foreach ($evento as $codEvento => $value) {
                    if (!$obErro->ocorreu()) {
                        $obTFolhaPagamentoTCEMGEntidadeRemuneracao->setDado('exercicio'      , Sessao::getExercicio() );
                        $obTFolhaPagamentoTCEMGEntidadeRemuneracao->setDado('cod_tipo'       , $codRemuneracao );                    
                        $obTFolhaPagamentoTCEMGEntidadeRemuneracao->setDado('cod_evento'     , $codEvento );
                        $obErro = $obTFolhaPagamentoTCEMGEntidadeRemuneracao->inclusao($boTransacao);
                    }
                }
            }
        }
    }

}

$obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro, $obTFolhaPagamentoTCEMGEntidadeCargoServidor);

if ( !$obErro->ocorreu() ) {
    SistemaLegado::alertaAviso($pgFilt."?stAcao=".$request->get('stAcao'),"Configuração realizada com sucesso!","incluir","aviso",Sessao::getId(),"../");
}else{
    SistemaLegado::LiberaFrames(true, true);
    SistemaLegado::exibeAviso("Houve erro no processamento da configuração!", 'n_incluir', 'erro');
}

?>