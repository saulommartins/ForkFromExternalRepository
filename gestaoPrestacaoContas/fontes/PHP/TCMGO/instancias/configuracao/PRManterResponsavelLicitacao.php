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
 * Página de Filtro de Responsavel Licitacao
 * Data de Criação   : 21/01/2015
 * @author Analista: Ane Caroline Fiegenbaum Pereira
 * @author Desenvolvedor: Evandro Melos
 * $Id: $
 * $Name: $
 * $Revision: $
 * $Author: $
 * $Date: $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GPC_TGO_MAPEAMENTO."TTCMGOResponsavelLicitacao.class.php";

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
$obTTCMGOResponsavelLicitacao = new TTCMGOResponsavelLicitacao();
$obTTCMGOResponsavelLicitacao->setDado('exercicio'      , $_REQUEST['stExercicioLicitacao']);
$obTTCMGOResponsavelLicitacao->setDado('cod_entidade'   , $arEntidade[0]);
$obTTCMGOResponsavelLicitacao->setDado('cod_modalidade' , $arModalidade[0]);
$obTTCMGOResponsavelLicitacao->setDado('cod_licitacao'  , $_REQUEST['inCodLicitacao']);     

$obTTCMGOResponsavelLicitacao->recuperaPorChave($rsRecordSet);

if(!$obErro->ocorreu()){
    if($rsRecordSet->getNumLinhas()>0){
        $obTTCMGOResponsavelLicitacao->setDado('cgm_resp_abertura_licitacao'  , $_REQUEST['inNumCGM_1']);
        $obTTCMGOResponsavelLicitacao->setDado('cgm_resp_edital'              , $_REQUEST['inNumCGM_2']);
        $obTTCMGOResponsavelLicitacao->setDado('cgm_resp_pesquisa'            , $_REQUEST['inNumCGM_3']);
        $obTTCMGOResponsavelLicitacao->setDado('cgm_resp_recurso_orcamentario', $_REQUEST['inNumCGM_4']);
        $obTTCMGOResponsavelLicitacao->setDado('cgm_resp_conducao_licitacao'  , $_REQUEST['inNumCGM_5']);
        $obTTCMGOResponsavelLicitacao->setDado('cgm_resp_homologacao'         , $_REQUEST['inNumCGM_6']);
        $obTTCMGOResponsavelLicitacao->setDado('cgm_resp_adjudicacao'         , $_REQUEST['inNumCGM_7']);
        
        $obErro = $obTTCMGOResponsavelLicitacao->alteracao();
        
        if($obErro->ocorreu()){
             SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=".$stAcao, urlencode($obErro->getDescricao()),"n_incluir","erro", Sessao::getId(), "../");
        }else{
             SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=".$stAcao,"Licitaçao ".$_REQUEST['licitacao'],"alterar","aviso", Sessao::getId(), "../");
        }
    }else{
        $obTTCMGOResponsavelLicitacao->setDado('cgm_resp_abertura_licitacao'  , $_REQUEST['inNumCGM_1']);
        $obTTCMGOResponsavelLicitacao->setDado('cgm_resp_edital'              , $_REQUEST['inNumCGM_2']);
        $obTTCMGOResponsavelLicitacao->setDado('cgm_resp_pesquisa'            , $_REQUEST['inNumCGM_3']);
        $obTTCMGOResponsavelLicitacao->setDado('cgm_resp_recurso_orcamentario', $_REQUEST['inNumCGM_4']);
        $obTTCMGOResponsavelLicitacao->setDado('cgm_resp_conducao_licitacao'  , $_REQUEST['inNumCGM_5']);
        $obTTCMGOResponsavelLicitacao->setDado('cgm_resp_homologacao'         , $_REQUEST['inNumCGM_6']);
        $obTTCMGOResponsavelLicitacao->setDado('cgm_resp_adjudicacao'         , $_REQUEST['inNumCGM_7']);
        $obErro = $obTTCMGOResponsavelLicitacao->inclusao();
                    
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
