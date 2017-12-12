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
* Arquivo de instância para manutenção de normas
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 28800 $
$Name$
$Author: diogo.zarpelon $
$Date: 2008-03-26 17:35:00 -0300 (Qua, 26 Mar 2008) $

Casos de uso: uc-01.04.02
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GA_NORMAS_NEGOCIO."RNorma.class.php";
include_once CAM_GA_NORMAS_MAPEAMENTO."TNormaDetalheAl.class.php";
include_once CAM_GA_NORMAS_MAPEAMENTO."TNorma.class.php";

$stAcao = $request->get("stAcao");

//Define o nome dos arquivos PHP
$stPrograma = "ManterNorma";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao&inCodNorma=".$_POST['inCodNorma']."&inCodTipoNorma=".$_POST['inCodTipoNorma']."&pg=".Sessao::read('link_pg')."&pos=".Sessao::read('link_pos');
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao&inCodNorma=".$_POST['inCodNorma']."&inCodTipoNorma=".$_POST['inCodTipoNorma'];
$pgProc = "PR".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao&inCodNorma=".$_POST['inCodNorma']."&inCodTipoNorma=".$_POST['inCodTipoNorma'];
$pgOcul = "OC".$stPrograma.".php";

$obRNorma = new RNorma;
$obErro  = new Erro;

$obAtributos = new MontaAtributos;
$obAtributos->setName('Atributo_');
$obAtributos->recuperaVetor( $arChave );

switch ($stAcao) {
    case "incluir":
        
        foreach ($arChave as $key=>$value) {
            $arChaves = preg_split( "/[^a-zA-Z0-9]/", $key );
            $inCodAtributo = $arChaves[0];
            if (is_array($value)) {
                $value = implode(",",$value);
            }

            $obRNorma->obRTipoNorma->obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo , $value );
        }
    
        $obRNorma->setNumNorma       ( $_POST['inNumNorma']       );
        $obRNorma->setExercicio      ( $_POST['stExercicio']      );
        $obRNorma->setDataPublicacao ( $_POST['stDataPublicacao'] );
        $obRNorma->setDataAssinatura ( $_POST['stDataAssinatura'] );
        $obRNorma->setDataTermino    ( $_POST['stDataTermino']    );
        $obRNorma->setNomeNorma      ( $_POST['stNomeNorma']      );
        $obRNorma->setDescricaoNorma ( $_POST['stDescricao']      );                
        $obRNorma->setUrl            ( $_FILES['btnIncluirLink']['tmp_name']  );
        $obRNorma->setNomeArquivo    ( $_FILES['btnIncluirLink']['name']      );
        $obRNorma->obRTipoNorma->setCodTipoNorma( $_POST['inCodTipoNorma'] );
                
        if (($_POST['numPercentualCreditoAdicional']) && $_POST['numPercentualCreditoAdicional'] > 100) {
            $obErro->setDescricao('Não pode ser informado mais do que 100 no Percentual de Crédito');
        }
               
        if (!$obErro->ocorreu()) {
           
            switch (SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio(), $boTransacao)) {
                case 02: //TCEAL
                    
                    if( !empty($_POST['stTipoLeiAlteracao']) ){
                        
                        if (empty($_REQUEST['stCodNorma'])) {
                            $obErro->setDescricao('Necessário informar a Lei Alterada!');
                        }
                        
                        if ( !$obErro->ocorreu() ) {
                            $obNorma = new TNorma;
                            $obNorma->setDado('cod_norma' ,  $_POST['hdnCodNorma']);
                            $obErro = $obNorma->recuperaPorChave($rsNormaAlterada, $boTransacao);
                            $stDescricao = $rsNormaAlterada->getCampo('descricao');
                            
                            if ( !$obErro->ocorreu() ) {
                                $obRNorma->setCodLeiAlteracao( $_POST['stTipoLeiAlteracao'] );
                                $obRNorma->setCodNormaAlteracao( $_POST['hdnCodNorma'] );
                                $obRNorma->setDescricaoNormaAlteracao( empty($stDescricao) ? $_REQUEST['stDescricao'] : $rsNormaAlterada->getCampo('descricao') );
                            }
                        }
                    }
                break;
            
                case 27: //TCETO
                    
                    if ( !empty($_POST['stTipoLeiAlteracao']) ){

                        if(empty($_REQUEST['numPercentualCreditoAdicional'])){
                            $obErro->setDescricao('Necessário informar Percentual de Crédito Adicional!');
                        } elseif (empty($_REQUEST['stCodNorma'])) {
                            $obErro->setDescricao('Necessário informar a Lei Alterada!');
                        }
                                                   
                        if ( !$obErro->ocorreu() ){
                            $obRNorma->setCodNormaAlteracao( $_POST['hdnCodNorma'] );
                            $obRNorma->setCodLeiAlteracao( $_POST['stTipoLeiAlteracao'] );
                            $obRNorma->setPercentualCreditoAdicional( $_POST['numPercentualCreditoAdicional'] );
                        }
                        
                    }
                break;
                
                case 11: //TCEMG
                    if ( !empty($_REQUEST['stTipoLeiOrigemDecreto']) ){
                        $obRNorma->setTipoLeiOrigemDecreto ($_REQUEST['stTipoLeiOrigemDecreto']);
                        if($_REQUEST['stTipoLeiOrigemDecreto']==3 and !(empty($_REQUEST['stTipoLeiAlteracaoOrcamentaria']))){
                            $obRNorma->setTipoLeiAlteracaoOrcamentaria ($_REQUEST['stTipoLeiAlteracaoOrcamentaria']);
                        }
                    }
                break;
            }
        }
        
        if ( !$obErro->ocorreu() ){
            $obTransacao = new Transacao;
            $obErro = $obRNorma->salvar($boTransacao);
        }
        
        if ( !$obErro->ocorreu() )
            sistemaLegado::alertaAviso($pgForm,"Norma: ".$_POST['inNumNorma'],"incluir","aviso", Sessao::getId(), "../");
        else
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");

    break;
    case "alterar":
        $inCodNorma = Sessao::read('inCodNorma');
        $anexo = $_FILES['btnIncluirLink']['tmp_name'];
        foreach ($arChave as $key=>$value) {
            $arChaves = preg_split( "/[^a-zA-Z0-9]/", $key );
            $inCodAtributo = $arChaves[0];
            if( is_array($value) )
                $value = implode(",",$value);
            $obRNorma->obRTipoNorma->obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo , $value );
        }
        if ($anexo) {
            $obRNorma->setCodNorma( $_REQUEST['inCodNorma'] );
            $obRNorma->consultar( $rsNorma );
            if ( $rsNorma->getCampo("link") ) {
                $stNomeArquivo  = $rsNorma->getCampo("link");
                $stDestinoAnexo = CAM_NORMAS.'anexos/';
                unlink( $stDestinoAnexo.$stNomeArquivo );
            }
            $obRNorma->setUrl($anexo);
            $obRNorma->setNomeArquivo($_FILES['btnIncluirLink']['name']);
        }
        
        $obRNorma->setCodNorma                  ( $inCodNorma                );
        $obRNorma->setNumNorma                  ( $_POST['inNumNorma']       );
        $obRNorma->setExercicio                 ( $_POST['stExercicio']      );
        $obRNorma->setDataPublicacao            ( $_POST['stDataPublicacao'] );
        $obRNorma->setDataAssinatura            ( $_POST['stDataAssinatura'] );
        $obRNorma->setDataTermino               ( $_POST['stDataTermino']    );
        $obRNorma->setNomeNorma                 ( $_POST['stNomeNorma']      );
        $obRNorma->setDescricaoNorma            ( $_POST['stDescricao']      );
        $obRNorma->obRTipoNorma->setCodTipoNorma( $_POST['inCodTipoNorma']   );
                
        if (($_POST['numPercentualCreditoAdicional']) && $_POST['numPercentualCreditoAdicional'] > 100) {
            $obErro->setDescricao('Não pode ser informado mais do que 100 no Percentual de Crédito');
        }

        if (!$obErro->ocorreu()) {
            switch (SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio())) {
                case 02: //TCEAL
                
                    if( $_POST['stTipoLeiAlteracao'] ){
                        if( empty( $_POST['stCodNorma'] ) ){
                            $obErro->setDescricao('Necessário informar a Lei Alterada!');
                        }
                        
                        if ( !$obErro->ocorreu() ){
                            $inCodNormaAlteracao = Sessao::read('inCodNormaAlteracao');
                            $stDescNormaAlteracao = Sessao::read('stDescNormaAlteracao');
                            
                            if($_REQUEST['hdnCodNorma']){
                                $obNorma = new TNorma;
                                $obNorma->setDado('cod_norma' , $_REQUEST['hdnCodNorma']);
                                $obErro = $obNorma->recuperaPorChave($rsNormaAlterada);
                                
                                if ( $rsNormaAlterada->getNumLinhas() > 0 ){
                                    $obRNorma->setCodLeiAlteracao( $_POST['stTipoLeiAlteracao'] );
                                    $obRNorma->setCodNormaAlteracao(  $_REQUEST['hdnCodNorma'] );
                                    $obRNorma->setDescricaoNormaAlteracao( $rsNormaAlterada->getCampo('descricao') );
                                }else{
                                    $obErro->setDescricao("Número de Lei Alterada inválida!");
                                }
                                
                            }else{
                                $obRNorma->setCodLeiAlteracao( $_POST['stTipoLeiAlteracao'] );
                                $obRNorma->setCodNormaAlteracao(  $inCodNormaAlteracao );
                                $obRNorma->setDescricaoNormaAlteracao( $stDescNormaAlteracao );
                            }
                        }
                    } else {
                        $obNormaDetalheAl = new TNormaDetalheAl;
                        $obNormaDetalheAl->setDado( 'cod_norma', $inCodNorma );
                        $obNormaDetalheAl->exclusao();
                    }
                break;
            
                case 27: //TCETO
                    if(!empty($_POST['stTipoLeiAlteracao'])){
                        
                        if(empty($_REQUEST['numPercentualCreditoAdicional'])){
                            $obErro->setDescricao('Necessário informar Percentual de Crédito Adicional!');
                        } elseif (empty($_REQUEST['stCodNorma'])) {
                            $obErro->setDescricao('Necessário informar a Lei Alterada!');
                        }
                        
                        if ( !$obErro->ocorreu() ){
                            $inCodNormaAlteracao           = Sessao::read('inCodNormaAlteracao');
                            $stDescNormaAlteracao          = Sessao::read('stDescNormaAlteracao');
                            $numPercentualCreditoAdicional = Sessao::read('numPercentualCreditoAdicional');
                            
                            if($_REQUEST['hdnCodNorma']){
                                $obNorma = new TNorma;
                                $obNorma->setDado('cod_norma' , $_REQUEST['hdnCodNorma']);
                                $obErro = $obNorma->recuperaPorChave($rsNormaAlterada);
                                
                                if ( $rsNormaAlterada->getNumLinhas() > 0 ){
                                    $obRNorma->setCodLeiAlteracao( $_POST['stTipoLeiAlteracao'] );
                                    $obRNorma->setCodNormaAlteracao(  $_REQUEST['hdnCodNorma'] );
                                    $obRNorma->setPercentualCreditoAdicional( $_REQUEST['numPercentualCreditoAdicional'] );
                                }else{
                                    $obErro->setDescricao("Número de Lei Alterada inválida!");
                                }
                                
                            }else{
                                $obRNorma->setCodLeiAlteracao( $_POST['stTipoLeiAlteracao'] );
                                $obRNorma->setCodNormaAlteracao(  $inCodNormaAlteracao );
                                $obRNorma->setPercentualCreditoAdicional( $_REQUEST['numPercentualCreditoAdicional'] );
                            }
                        }
                    } else {
                        include_once ( CAM_GPC_TCETO_MAPEAMENTO."TTCETONormaDetalhe.class.php");
                        $obTTCETONormaDetalhe = new TTCETONormaDetalhe;
                        $obTTCETONormaDetalhe->setDado( 'cod_norma', Sessao::read('inCodNorma') );
                        $obTTCETONormaDetalhe->exclusao();
                    }
                break;
            
              case 11: //TCEMG
                    if ( !empty($_REQUEST['stTipoLeiOrigemDecreto']) ){
                        $obRNorma->setTipoLeiOrigemDecreto ($_REQUEST['stTipoLeiOrigemDecreto'] );
                        if( $_REQUEST['stTipoLeiOrigemDecreto']==3 and !(empty($_REQUEST['stTipoLeiAlteracaoOrcamentaria']))){
                            $obRNorma->setTipoLeiAlteracaoOrcamentaria ($_REQUEST['stTipoLeiAlteracaoOrcamentaria']);
                        }
                    }
                break;
            }
        }
        
        if (!$obErro->ocorreu()){
            $obTransacao = new Transacao;
            $obErro = $obRNorma->salvar($boTransacao);
        }
        
        if (!$obErro->ocorreu()) {
            SistemaLegado::alertaAviso($pgList,"Norma: ".$_POST['inNumNorma'],"alterar","aviso", Sessao::getId(), "../");
        }else{
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
        }

    break;

    case "excluir";
        $obRNorma->setCodNorma ( $request->get('inCodNorma') );
        $obRNorma->consultar	  ( $rsNorma );
        $obRNorma->setNomeArquivo ( $rsNorma->getCampo("link") );
        
        $boTransacao = new Transacao;
        $obErro = $obRNorma->excluir($boTransacao);

        if (!$obErro->ocorreu()) {
            sistemaLegado::alertaAviso($pgList,"Norma: ".$obRNorma->getNomeNorma(),"excluir","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::alertaAviso($pgList,"Norma: ".urlencode( $_REQUEST['inCodNorma'] )." está sendo utilizada.","n_excluir","erro", Sessao::getId(), "../");
        }
    break;
}

?>
