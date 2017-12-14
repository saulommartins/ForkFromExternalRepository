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
 * Página Oculta - Configuração Unidade Orçamentária
 * Data de Criação   : 16/01/2014

 * @author Analista: Eduardo Schitz
 * @author Desenvolvedor: Franver Sarmento de Moraes

 * @ignore

 * $Id: PRManterResponsavelLicitacao.php 59612 2014-09-02 12:00:51Z gelson $
 * $Name: $
 * $Revision: 57878 $
 * $Author: carlos.silva $
 * $Date: 2014-04-16 11:01:57 -0300 (Qua, 16 Abr 2014) $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGRespLic.class.php";


//Define o nome dos arquivos PHP
$stPrograma = "ManterResponsavelLicitacao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$_REQUEST['stAcao']= 'alterar';
$stAcao = $_REQUEST['stAcao'];
Sessao::write("arLicitacao",$_REQUEST);

    $obErro     = new Erro;
    $arCGMResponsaveis = null;
    $inCount = 1;
     foreach($_REQUEST as $stKey=>$stValue){
        if($stKey == 'inNumCGM_'.$inCount){
            $arCGMResponsaveis[$inCount] = array( $stKey=>$stValue,'stNomCGM_'.$inCount=>$_REQUEST['stNomCGM_'.$inCount] );
            
            if(empty($_REQUEST['inNumCGM_'.$inCount]) && ($_REQUEST['inCodModalidade'] != '9') && ($_REQUEST['inCodModalidade'] != '8')){
                $arCGMResponsaveis[$inCount]['stNomCGM_'.$inCount] = "";
                $obErro->setDescricao('Campo Vazio');
            }
            
            if(($_REQUEST['inNumCGM_'.$inCount] == "") && ($_REQUEST['stNomCGM_'.$inCount] != "")){
               if(($_REQUEST['inCodModalidade'] == '9') || ($_REQUEST['inCodModalidade'] == '8')){
                  $arCGMResponsaveis[$inCount]['inNumCGM_'.$inCount] = "null";
                  $_REQUEST['inNumCGM_'.$inCount] = "null";  
               }
            }
            $inCount++;
        }
     }
     
    Sessao::write("arCGMResponsaveis",$arCGMResponsaveis);
    
    $arEntidade = explode('-',$_REQUEST['stEntidade']); 
    $arModalidade = explode('-',$_REQUEST['stModalidade']); 

    $obTTCEMGRespLic = new TTCEMGRespLic;
    $obTTCEMGRespLic->setDado('exercicio', $_REQUEST['stExercicioLicitacao']);
    $obTTCEMGRespLic->setDado('cod_entidade', $arEntidade[0]);
    $obTTCEMGRespLic->setDado('cod_modalidade', $arModalidade[0]);
    $obTTCEMGRespLic->setDado('cod_licitacao', $_REQUEST['inCodLicitacao']);     

    $obTTCEMGRespLic->recuperaPorChave($rsRecordSet);
    
    if(!$obErro->ocorreu()){
        if($rsRecordSet->getNumLinhas()>0){
            $obTTCEMGRespLic->setDado('cgm_resp_abertura_licitacao'  , $_REQUEST['inNumCGM_1']);
            $obTTCEMGRespLic->setDado('cgm_resp_edital'              , $_REQUEST['inNumCGM_2']);
            $obTTCEMGRespLic->setDado('cgm_resp_recurso_orcamentario', $_REQUEST['inNumCGM_3']);
            $obTTCEMGRespLic->setDado('cgm_resp_conducao_licitacao'  , $_REQUEST['inNumCGM_4']);
            $obTTCEMGRespLic->setDado('cgm_resp_homologacao'         , $_REQUEST['inNumCGM_5']);
            $obTTCEMGRespLic->setDado('cgm_resp_adjudicacao'         , $_REQUEST['inNumCGM_6']);
            $obTTCEMGRespLic->setDado('cgm_resp_publicacao'          , $_REQUEST['inNumCGM_7']);
            $obTTCEMGRespLic->setDado('cgm_resp_avaliacao_bens'      , $_REQUEST['inNumCGM_8']);
            $obTTCEMGRespLic->setDado('cgm_resp_pesquisa'            , $_REQUEST['inNumCGM_9']);
            $obErro = $obTTCEMGRespLic->alteracao();
            
            if($obErro->ocorreu()){
                 SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=".$stAcao, urlencode($obErro->getDescricao()),"n_incluir","erro", Sessao::getId(), "../");
            }else{
                 SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=".$stAcao,"Licitaçao ".$_REQUEST['licitacao'],"alterar","aviso", Sessao::getId(), "../");
            }
        }else{
            $obTTCEMGRespLic->setDado('cgm_resp_abertura_licitacao'  , $_REQUEST['inNumCGM_1']);
            $obTTCEMGRespLic->setDado('cgm_resp_edital'              , $_REQUEST['inNumCGM_2']);
            $obTTCEMGRespLic->setDado('cgm_resp_recurso_orcamentario', $_REQUEST['inNumCGM_3']);
            $obTTCEMGRespLic->setDado('cgm_resp_conducao_licitacao'  , $_REQUEST['inNumCGM_4']);
            $obTTCEMGRespLic->setDado('cgm_resp_homologacao'         , $_REQUEST['inNumCGM_5']);
            $obTTCEMGRespLic->setDado('cgm_resp_adjudicacao'         , $_REQUEST['inNumCGM_6']);
            $obTTCEMGRespLic->setDado('cgm_resp_publicacao'          , $_REQUEST['inNumCGM_7']);
            $obTTCEMGRespLic->setDado('cgm_resp_avaliacao_bens'      , $_REQUEST['inNumCGM_8']);
            $obTTCEMGRespLic->setDado('cgm_resp_pesquisa'            , $_REQUEST['inNumCGM_9']);
            $obErro = $obTTCEMGRespLic->inclusao();
                        
            if($obErro->ocorreu()){
                 SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=".$stAcao, urlencode($obErro->getDescricao()),"n_incluir","aviso", Sessao::getId(), "../");
            }else{
                 SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=".$stAcao,"Licitacao ".$_REQUEST['licitacao'],"incluir","aviso", Sessao::getId(), "../");
            }
        }
    }else {
        SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=".$stAcao, urlencode($obErro->getDescricao()),"n_incluir","erro", Sessao::getId(), "../");
    }

?>
