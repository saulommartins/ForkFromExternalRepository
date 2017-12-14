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
    * Pacote de configuração do TCETO - Oculto Configurar Orçamento
    * Data de Criação   : 05/11/2014

    * @author Analista: Silvia Martins Silva
    * @author Desenvolvedor: Michel Teixeira
    * $Id: OCManterOrcamento.php 60778 2014-11-14 17:55:19Z evandro $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GA_NORMAS_MAPEAMENTO.'TNorma.class.php';

$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];

switch ($stCtrl) {
    case "PreencheNorma":
        $obTNorma = new TNorma;

        if (is_numeric($_REQUEST['inCodNorma']) && isset($_REQUEST['inCodNorma'])) {

            $stFiltro = ' WHERE N.cod_norma='.$_REQUEST['inCodNorma'].' ';
            $obTNorma->recuperaNormasDecreto($rsNorma, $stFiltro);
            
            if ( $rsNorma->getNumLinhas()>0 ) {
                $stJs = "f.inCodNorma.value = '". $rsNorma->getCampo("cod_norma")."';\n";
                $stJs .= "f.stComplementacaoLoa.focus();\n";
                $stJs .= "d.getElementById('stNomTipoNorma').innerHTML ='".$rsNorma->getCampo('nom_tipo_norma').' '.$rsNorma->getCampo('num_norma_exercicio').' - '.$rsNorma->getCampo('nom_norma')."';\n";
            } else {
                $stJs = "f.inCodNorma.value = '';\n";
                $stJs .= "d.getElementById('stNomTipoNorma').innerHTML ='&nbsp;';\n";
                $stJs .= "alertaAviso('@Código da Lei/Decreto informado não existe. (".$_REQUEST['inCodNorma'].")','form','erro','".Sessao::getId()."');\n";
            }
        } else {
             $stJs = "f.inCodNorma.value = '';\n";
             $stJs .= "d.getElementById('stNomTipoNorma').innerHTML ='&nbsp;';\n";
        }
       sistemaLegado::executaFrameOculto($stJs); 
    break;
    case "PreencheAlteracaoNorma":
        $obTNorma = new TNorma;

        if (is_numeric($_REQUEST['stCodNorma']) && isset($_REQUEST['stCodNorma'])) {

            $stFiltro = ' WHERE N.cod_norma='.$_REQUEST['stCodNorma'].' ';
            $obTNorma->recuperaNormasDecreto($rsNorma, $stFiltro);
            
            if ( $rsNorma->getNumLinhas()>0 ) {
                $stJs = "f.stCodNorma.value = '". $rsNorma->getCampo("cod_norma")."';\n";                
                $stJs .= " d.getElementById('stNorma').innerHTML = '".$rsNorma->getCampo('nom_tipo_norma').' '.$rsNorma->getCampo('num_norma_exercicio').' - '.$rsNorma->getCampo('nom_norma')."';\n";                
                $stJs .= " f.stNorma.value = '".$rsNorma->getCampo('nom_tipo_norma').' '.$rsNorma->getCampo('num_norma_exercicio').' - '.$rsNorma->getCampo('nom_norma')."';\n";                
            } else {
                $stJs = "f.stCodNorma.value = '';\n";
                $stJs .= "d.getElementById('stNorma').innerHTML ='&nbsp;';\n";
                $stJs .= "alertaAviso('@Código da Lei/Decreto informado não existe. (".$_REQUEST['stCodNorma'].")','form','erro','".Sessao::getId()."');\n";
            }
        } else {
             $stJs = "f.stCodNorma.value = '';\n";
             $stJs .= "d.getElementById('stNorma').innerHTML ='&nbsp;';\n";
        }
       sistemaLegado::executaFrameOculto($stJs); 
    break;

    case 'incluirLista':
       $stJs .= incluirLista();
       sistemaLegado::executaFrameOculto($stJs);
    break;

    case 'montarLista':
         $stJs .= montarLista();
         sistemaLegado::executaFrameOculto($stJs);
    break;

    case "alterarItemAlteracaoLei":
        $stJs .= alterarItemAlteracaoLei();
        sistemaLegado::executaFrameOculto($stJs);
    break;

    case 'excluirItemAlteracaoLei':
        $stJs .= excluirItemAlteracaoLei();
        sistemaLegado::executaFrameOculto($stJs);
    break;
}

function montarLista(){    
    $rsRecordSet = new RecordSet();
    if (Sessao::read('arAlteracaoLei') != "") {
        $rsRecordSet->preenche(Sessao::read('arAlteracaoLei'));
    }

    $obLista = new Lista;
    $obLista->setMostraPaginacao( false );
    $obLista->setTitulo( "Lista de Alterações de Lei" );

    $obLista->setRecordSet( $rsRecordSet );
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 2 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Lei Alteração PPA" );
    $obLista->ultimoCabecalho->setWidth( 6 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Data Publicação" );
    $obLista->ultimoCabecalho->setWidth( 6 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Descrição" );
    $obLista->ultimoCabecalho->setWidth( 40 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 4 );
    $obLista->commitCabecalho();
        
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "cod_norma" );
    $obLista->ultimoDado->setAlinhamento('ESQUERDA' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "data_alteracao_lei" );
    $obLista->ultimoDado->setAlinhamento('ESQUERDA' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "descricao" );
    $obLista->ultimoDado->setAlinhamento('ESQUERDA' );
    $obLista->commitDado();
    
    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "ALTERAR" );
    $obLista->ultimaAcao->setFuncao( true );
    $obLista->ultimaAcao->setLink( "JavaScript:modificaDado('alterarItemAlteracaoLei');" );
    $obLista->ultimaAcao->addCampo("1" , "inId");
    $obLista->commitAcao();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "EXCLUIR" );
    $obLista->ultimaAcao->setFuncao( true );
    $obLista->ultimaAcao->setLink( "JavaScript:modificaDado('excluirItemAlteracaoLei');" );
    $obLista->ultimaAcao->addCampo("1" , "inId");
    $obLista->commitAcao();

    $obLista->montaHTML();
    $stHtml = $obLista->getHTML();
    $stHtml = str_replace("\n","",$stHtml);
    $stHtml = str_replace("  ","",$stHtml);
    $stHtml = str_replace("'","\\'",$stHtml);
    $stJs .= "d.getElementById('spnListaNormaAlteracao').innerHTML = '".$stHtml."';";

    sistemaLegado::executaFrameOculto($stJs);
}

function incluirLista(){
    if ( $_REQUEST["stAcao"] != "alterar" ) {
        //incluir item na lista        
        $obErro = new Erro();
        if ( $_REQUEST["stCodNorma"] == "" ) {
                        return "alertaAviso('Preencher o campo Lei Alteração PPA.','form','erro','".Sessao::getId()."');\n";
        }
        if ( $_REQUEST["stDataAlteracaoPPA"] == "" ) {
                    return "alertaAviso('Preencher o campo Data Publicação da Lei Alteração.','form','erro','".Sessao::getId()."');\n";
        }
        
        $arAlteracaoLei = Sessao::read('arAlteracaoLei');

        $arNovaAlteracaoLei = array();
        $arNovaAlteracaoLei["cod_norma"]           = $_REQUEST["stCodNorma"];
        $arNovaAlteracaoLei["data_alteracao_lei"]  = $_REQUEST["stDataAlteracaoPPA"];
        $arNovaAlteracaoLei["descricao"]           = $_REQUEST["stNorma"];
        $arNovaAlteracaoLei["inId"]                = count($arAlteracaoLei);
        
        if ( $arAlteracaoLei != "" ) {
            foreach ($arAlteracaoLei as $arLei) {   
                if ($arLei['cod_norma'] == $arNovaAlteracaoLei['cod_norma']){
                    $obErro->setDescricao("Esta Lei já está cadastrado!");
                }
            }
        }
       
        if ( !$obErro->ocorreu() ) {
            $arAlteracaoLei[] = $arNovaAlteracaoLei;
            Sessao::write('arAlteracaoLei',$arAlteracaoLei);
        }   

    }else{

        //alterar item da lista
        $obErro  = new Erro();
    
        $arAlteracaoLei = Sessao::read('arAlteracaoLei');
        
        foreach ($arAlteracaoLei as $arLei) {   
            if ( $arLei['inId'] <> $_REQUEST['hdnInId'] ) {
                if ( $_REQUEST["stCodNorma"] == "" ) {
                        return "alertaAviso('Preencher o campo Lei Alteração PPA.','form','erro','".Sessao::getId()."');\n";
                }
                if ( $_REQUEST["stDataAlteracaoPPA"] == "" ) {
                    return "alertaAviso('Preencher o campo Data Publicação da Lei Alteração.','form','erro','".Sessao::getId()."');\n";
                }
            }
        }
        
        if ( !$obErro->ocorreu() ) {
            foreach ($arAlteracaoLei as $key => $arLei) {
                if ($arLei['inId'] == $_REQUEST['hdnInId']) {
                    $arAlteracaoLei[$key]['cod_norma']          = $_REQUEST['stCodNorma'];
                    $arAlteracaoLei[$key]['data_alteracao_lei'] = $_REQUEST['stDataAlteracaoPPA'];
                    $arAlteracaoLei[$key]['descricao']          = $_REQUEST['stNorma'];
                   
                    Sessao::write('arAlteracaoLei',$arAlteracaoLei);     
                    break;
                }
            }
        }
    } 
    
    if ( $obErro->ocorreu() ) {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    } else {
        $stJs .= montarLista();
        $stJs .= "f.stCodNorma.value = ''; \n";
        $stJs .= "d.getElementById('stNorma').innerHTML = '&nbsp;';\n";
        $stJs .= "f.stDataAlteracaoPPA.value = '';\n";            
        $stJs .= "f.hdnInId.value = '';\n";
        $stJs .= "f.stAcao.value = '';\n";
        $stJs .= "f.btIncluirLei.value = 'incluir';\n";
        $stJs .= "f.stAcao.value = 'configurar';\n";
    }

    SistemaLegado::executaFrameOculto($stJs);
}

function alterarItemAlteracaoLei(){
    $arAlteracaoLei = Sessao::read('arAlteracaoLei');
    foreach($arAlteracaoLei as $arLei){
        if ( $arLei["inId"] == $_REQUEST["inId"] ) {
            $stJs .= "f.stCodNorma.value = '".$arLei['cod_norma']."';\n";
            $stJs .= "d.getElementById('stNorma').innerHTML = '".$arLei['descricao']."';\n";
            $stJs .= "f.stDataAlteracaoPPA.value = '".$arLei['data_alteracao_lei']."';\n";
            $stJs .= "f.hdnInId.value = '".$arLei["inId"]."';\n";            
            $stJs .= "f.btIncluirLei.value = 'Alterar';\n";
            $stJs .= "f.stAcao.value = 'alterar';\n";
        }
    }

    sistemaLegado::executaFrameOculto($stJs);   
}

function excluirItemAlteracaoLei()
{
    $arTemp = $arTempRemovido = array();
    $arAlteracaoLei  = Sessao::read('arAlteracaoLei');
    $arLeisRemovidos = Sessao::read('arLeisRemovidos');

    foreach ($arAlteracaoLei as $arLei) {
        if ($arLei['inId'] != $_GET['inId']) {
            $arTemp[] = $arLei;
        } else {
            $arTempRemovido[] = $arLei;
        }
    }

    $arAlteracaoLei = $arTemp;
    $arLeisRemovidos[] = $arTempRemovido;
    
    Sessao::write('arLeisRemovidos', $arLeisRemovidos);
    Sessao::write('arAlteracaoLei', $arAlteracaoLei);

    $stJs .= montarLista();
    
    SistemaLegado::executaFrameOculto($stJs);
}

?>
