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
    * Página de Processamento Configuração de Orgão
    * Data de Criação   : 14/01/2014

    * @author Analista: Eduardo Paculski Schitz
    * @author Desenvolvedor: Franver Sarmento de Moraes

    * @ignore

    * $Id: $
    *
    * $Revision: $
    * $Name: $
    * $Author: $
    * $Date: $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GA_ADM_MAPEAMENTO.'TAdministracaoConfiguracaoEntidade.class.php');
include_once(CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGConfiguracaoOrgao.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoOrgao";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$stDataAtual = date('d-m-Y');
$stAcao = $request->get("stAcao");
$stModulo = str_replace("?","",$request->get("stModulo"));

switch ($stAcao) {
    default:
        $obErro = new Erro();
        $obTransacao = new Transacao;
        $obTransacao->begin();
        $boTransacao = $obTransacao->getTransacao();
        $obErro = $obTransacao->abreTransacao($boFlagTransacao, $boTransacao);
        $obTAdministracaoConfiguracaoEntidade = new TAdministracaoConfiguracaoEntidade();
        $obTTCEMGConfiguracaoOrgao = new TTCEMGConfiguracaoOrgao();
        
        if(!$obErro->ocorreu()){
            // Adiciona a Unidade Gestora na tabela : administracao.configuracao_entidade para tcemg_codigo_orgao_entidade_sicom
            $obTAdministracaoConfiguracaoEntidade->setDado("exercicio",Sessao::getExercicio());
            $obTAdministracaoConfiguracaoEntidade->setDado('cod_entidade',$request->get('hdnCodEntidade'));
            $obTAdministracaoConfiguracaoEntidade->setDado('parametro','tcemg_codigo_orgao_entidade_sicom');
            $obTAdministracaoConfiguracaoEntidade->setDado('valor',$request->get('inCodigo'));
            $obTAdministracaoConfiguracaoEntidade->setDado('cod_modulo',$request->get('stModulo'));
            $obTAdministracaoConfiguracaoEntidade->recuperaPorChave($rsRecordSet);
            
            if ($rsRecordSet->eof()) {
                $obErro = $obTAdministracaoConfiguracaoEntidade->inclusao($boTransacao);
            } else {
                $obErro = $obTAdministracaoConfiguracaoEntidade->alteracao($boTransacao);
            }
            
            // Adiciona a Unidade Gestora na tabela : administracao.configuracao_entidade para tcemg_tipo_orgao_entidade_sicom
            $obTAdministracaoConfiguracaoEntidade->setDado("exercicio",Sessao::getExercicio());
            $obTAdministracaoConfiguracaoEntidade->setDado('cod_entidade',$request->get('hdnCodEntidade'));
            $obTAdministracaoConfiguracaoEntidade->setDado('parametro','tcemg_tipo_orgao_entidade_sicom');
            $obTAdministracaoConfiguracaoEntidade->setDado('valor',$request->get('inNumUnidade'));
            $obTAdministracaoConfiguracaoEntidade->setDado('cod_modulo',$request->get('stModulo'));
            $obTAdministracaoConfiguracaoEntidade->recuperaPorChave($rsRecordSet);
            
            if ($rsRecordSet->eof()) {
                $obErro = $obTAdministracaoConfiguracaoEntidade->inclusao($boTransacao);
            } else {
                $obErro = $obTAdministracaoConfiguracaoEntidade->alteracao($boTransacao);
            }
            
            // Adiciona a Unidade Gestora na tabela : administracao.configuracao_entidade para tcemg_cgm_responsavel
            //$obTAdministracaoConfiguracaoEntidade->setDado("exercicio",Sessao::getExercicio());
            //$obTAdministracaoConfiguracaoEntidade->setDado('cod_entidade',$request->get('hdnCodEntidade'));
            //$obTAdministracaoConfiguracaoEntidade->setDado('parametro','tcemg_cgm_responsavel');
            //$obTAdministracaoConfiguracaoEntidade->setDado('valor',$request->get('inNumCGM'));
            //$obTAdministracaoConfiguracaoEntidade->setDado('cod_modulo',$request->get('stModulo'));
            //$obTAdministracaoConfiguracaoEntidade->recuperaPorChave($rsRecordSet);
            
            //if ($rsRecordSet->eof()) {
            //    $obErro = $obTAdministracaoConfiguracaoEntidade->inclusao($boTransacao);
            //} else {
            //    $obErro = $obTAdministracaoConfiguracaoEntidade->alteracao($boTransacao);
            //}
            $arResponsaveis = Sessao::read('arResponsaveis');
            
            if (is_array($arResponsaveis) && count($arResponsaveis) > 0) {
                $obTTCEMGConfiguracaoOrgao->setDado("exercicio",Sessao::getExercicio());
                $obTTCEMGConfiguracaoOrgao->setDado("cod_entidade",$request->get('hdnCodEntidade'));
                $obTTCEMGConfiguracaoOrgao->exclusao();
                foreach( $arResponsaveis AS $arResponsavel ){
                    $obTTCEMGConfiguracaoOrgao->setDado("exercicio"              ,Sessao::getExercicio());
                    $obTTCEMGConfiguracaoOrgao->setDado("cod_entidade"           ,$arResponsavel['cod_entidade']);
                    $obTTCEMGConfiguracaoOrgao->setDado("tipo_responsavel"       ,$arResponsavel['tipo_responsavel']);
                    $obTTCEMGConfiguracaoOrgao->setDado("num_cgm"                ,$arResponsavel['num_cgm']);
                    $obTTCEMGConfiguracaoOrgao->setDado("crc_contador"           ,$arResponsavel['crc_contador']);
                    $obTTCEMGConfiguracaoOrgao->setDado("uf_crcContador"         ,$arResponsavel['uf_crccontador']);
                    $obTTCEMGConfiguracaoOrgao->setDado("cargo_ordenador_despesa",$arResponsavel['cargo_ordenador_despesa']);
                    $obTTCEMGConfiguracaoOrgao->setDado("dt_inicio"              ,$arResponsavel['dt_inicio']);
                    $obTTCEMGConfiguracaoOrgao->setDado("dt_fim"                 ,$arResponsavel['dt_fim']);
                    $obTTCEMGConfiguracaoOrgao->setDado("email"                  ,$arResponsavel['email']);
                    $obTTCEMGConfiguracaoOrgao->recuperaPorChave($rsRecordSet);
                    
                    if ($rsRecordSet->eof()) {
                        $obErro = $obTTCEMGConfiguracaoOrgao->inclusao($boTransacao);
                    } else {
                        $obErro = $obTTCEMGConfiguracaoOrgao->alteracao($boTransacao);
                    }
                }

            } else {
                $obErro->setDescricao("Deverá que inserir pelo menos um tipo de responsável!");
            }
            if(!$obErro->ocorreu()){
                $obTransacao->fechaTransacao($boFlagTransacao,$boTransacao,$obErro,$obTAdministracaoConfiguracaoEntidade);
                $obTransacao->fechaTransacao($boFlagTransacao,$boTransacao,$obErro,$obTTCEMGConfiguracaoOrgao);
                SistemaLegado::alertaAviso($pgFilt."?".Sessao::getId()."&stAcao=".$stAcao."&modulo=".$stModulo,"Configuração ","incluir","incluir_n", Sessao::getId(), "../");
            }else{
                SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
            }
        }
        
        break;
}
?>
