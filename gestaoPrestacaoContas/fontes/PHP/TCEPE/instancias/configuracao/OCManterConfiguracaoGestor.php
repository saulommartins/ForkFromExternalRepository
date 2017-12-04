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
 * * Pacote de configuração do TCEPE
 * Página Oculta - Configuração Gestor 

 * Data de Criação   : 26/09/2014

 * @author Analista: Silvia Martins
 * @author Desenvolvedor: Lisiane Morais
 * 
 * $Id: OCManterConfiguracaoGestor.php 60205 2014-10-06 21:06:16Z lisiane $
 * $Name: $
 * $Revision:$
 * $Author:$
 * $Date:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GA_CGM_NEGOCIO."RCGM.class.php" );
include_once(CAM_GPC_TCEPE_MAPEAMENTO."TTCEPEConfiguracaoGestor.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoGestor";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once ($pgJS);

$stCtrl = $request->get('stCtrl');

function processarForm($boExecuta = false, $stArquivo = "Form", $stAcao = "manter")
{
    switch ($stAcao) {
        case "manter":
            $stJs .= montaListaGestor();
        break;
    }

    if ($boExecuta) {
        sistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function montaListaGestor()
{
    $rsRecordSet = new RecordSet();
    if (Sessao::read('arGestores') != "") {
        $rsRecordSet->preenche(Sessao::read('arGestores'));
    }

    $obLista = new Lista;
    $obLista->setMostraPaginacao( false );
    $obLista->setTitulo( "Gestor da Unidade Orçamentária" );

    $obLista->setRecordSet( $rsRecordSet );
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 3 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "CGM" );
    $obLista->ultimoCabecalho->setWidth( 8 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Nome do Gestor" );
    $obLista->ultimoCabecalho->setWidth( 37 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Data de Início" );
    $obLista->ultimoCabecalho->setWidth( 12 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Data de Término" );
    $obLista->ultimoCabecalho->setWidth( 12 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 4 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "cgm_gestor" );
    $obLista->ultimoDado->setAlinhamento('ESQUERDA' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "nom_cgm" );
    $obLista->ultimoDado->setAlinhamento('ESQUERDA' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "dt_inicio_vigencia" );
    $obLista->ultimoDado->setAlinhamento('ESQUERDA' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "dt_fim_vigencia" );
    $obLista->ultimoDado->setAlinhamento('ESQUERDA' );
    $obLista->commitDado();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "ALTERAR" );
    $obLista->ultimaAcao->setFuncao( true );
    $obLista->ultimaAcao->setLink( "JavaScript:modificaDado('alterarGestor');" );
    $obLista->ultimaAcao->addCampo("1" , "inId");
    $obLista->commitAcao();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "EXCLUIR" );
    $obLista->ultimaAcao->setFuncao( true );
    $obLista->ultimaAcao->setLink( "JavaScript:modificaDado('excluirGestor');" );
    $obLista->ultimaAcao->addCampo("1" , "inId");
    $obLista->commitAcao();

    $obLista->montaHTML();
    $stHtml = $obLista->getHTML();
    $stHtml = str_replace("\n","",$stHtml);
    $stHtml = str_replace("  ","",$stHtml);
    $stHtml = str_replace("'","\\'",$stHtml);
    $stJs .= "d.getElementById('spnCGMsGestores').innerHTML = '".$stHtml."';";

    return $stJs;
}

function incluirGestor()
{
    if ( !($_REQUEST["stHdnAcao"] == "alterar")) {
        
        $obTTCPEGConfiguracaoGestor = new TTCEPEConfiguracaoGestor();
        
        $obErro = new Erro();
        if ( $_REQUEST["inCgmGestor"] == "" ) {
            return "alertaAviso('Preencher o campo Gestor.','form','erro','".Sessao::getId()."');\n";
        }
        if ( $_REQUEST["dtDataInicio"] == "" ) {
            return "alertaAviso('Preencher o campo Início da Vigência.','form','erro','".Sessao::getId()."');\n";
        }
        if ( $_REQUEST["dtDataFim"] == "" ) {
            return "alertaAviso('Preencher o campo Fim da Vigência.','form','erro','".Sessao::getId()."');\n";
        }

        $arGestores = Sessao::read('arGestores');

        $arNovoGestor = array();
        $arNovoGestor["cod_entidade"] = $_REQUEST["hdnCodEntidade"];
        $arNovoGestor["cgm_gestor"] = $_REQUEST["inCgmGestor"];
        $arNovoGestor["nom_cgm"] = $_REQUEST["stNomCgmGestor"];
        $arNovoGestor["dt_inicio_vigencia"] = $_REQUEST["dtDataInicio"];
        $arNovoGestor["dt_fim_vigencia"] = $_REQUEST["dtDataFim"];
        $arNovoGestor["num_orgao"] = $_REQUEST["inMontaCodOrgaoM"];
        $arNovoGestor["num_unidade"] = $_REQUEST["inMontaCodUnidadeM"];
        $arNovoGestor["inId"] = count($arGestores);
        
        if ( $arGestores != "" ) {
            foreach ($arGestores as $arrGestores) {   
                if ($arrGestores['cgm_gestor'] == $arNovoGestor['cgm_gestor'] &&
                    $arrGestores['cod_entidade'] == $arNovoGestor['cod_entidade'] &&
                    $arrGestores['num_orgao'] == $arNovoGestor['inMontaCodOrgaoM'] &&
                    $arrGestores['num_unidade'] == $arNovoGestor['inMontaCodUnidadeM']){
                    
                    $obErro->setDescricao("Esta CGM já está cadastrado para essa Unidade Orçamentária!");
                }
                
                elseif ( SistemaLegado::comparaDatas($arrGestores['dt_fim_vigencia'], $arNovoGestor['dt_inicio_vigencia'], true)
                         AND SistemaLegado::comparaDatas($arNovoGestor['dt_fim_vigencia'],$arrGestores['dt_inicio_vigencia'] , true)){
                    $obErro->setDescricao("Já possui Gestor cadastrado, para essa Unidade Orçamentária, no período informado!");
                }
            }
        }
        
        if ( SistemaLegado::comparaDatas($arNovoGestor['dt_inicio_vigencia'], $arNovoGestor['dt_fim_vigencia'], true) ) {
                    $obErro->setDescricao("A data final da Vigência deve ser maior que a Data Inicial!");
            } 
        if ( !$obErro->ocorreu() ) {
            $arGestores[] = $arNovoGestor;
            Sessao::write('arGestores',$arGestores);
        }    
    } else {

        $obErro  = new Erro();
    
        $arGestores = Sessao::read('arGestores');
        
        foreach ($arGestores as $arrGestores) {
            if ( $arrGestores['inId'] <> $_REQUEST['hdnInId'] ) {

                if ( $arrGestores['dt_inicio_vigencia'] == $_REQUEST['dtDataInicio'] AND $arrGestores['dt_fim_vigencia'] == $_REQUEST['dtDataFim'] ){
                    $obErro->setDescricao("Já possui Gestor cadastrado, para essa Unidade Orçamentária, no período informado!");
                }
                
                elseif ( SistemaLegado::comparaDatas($arrGestores['dt_fim_vigencia'], $_REQUEST['dtDataInicio'], true)
                        AND SistemaLegado::comparaDatas($_REQUEST['dtDataFim'],$arrGestores['dt_inicio_vigencia'] , true)){
                    $obErro->setDescricao("Já possui Gestor cadastrado, para essa Unidade Orçamentária, no período informado!");
                }
            }
        }
        
        if ( SistemaLegado::comparaDatas($_REQUEST['dtDataInicio'], $_REQUEST['dtDataFim'], true) ) {
            $obErro->setDescricao("A data final da Vigência deve ser maior que a Data Inicial!");
        }
            
        if ( !$obErro->ocorreu() ) {
            foreach ($arGestores as $key => $arGestor) {
                if ($arGestor['inId'] == $_REQUEST['hdnInId']) {
                    $arGestores[$key]['cod_entidade']            = $_REQUEST['hdnCodEntidade'];
                    $arGestores[$key]['cgm_gestor']           = $_REQUEST['inCgmGestor'];
                    $arGestores[$key]['nom_cgm']                 = $_REQUEST['stNomCgmGestor'];
                    $arGestores[$key]['dt_inicio_vigencia']      = $_REQUEST['dtDataInicio'];
                    $arGestores[$key]['dt_fim_vigencia']         = $_REQUEST['dtDataFim'];
                    $arGestores[$key]["num_orgao"]               = $_REQUEST["inMontaCodOrgaoM"];
                    $arGestores[$key]["num_unidade"]             = $_REQUEST["inMontaCodUnidadeM"];
                    
                    Sessao::write('arGestores',$arGestores);     
                    break;
                }
            }
        }
    } 
    
    if ( $obErro->ocorreu() ) {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    } else {
        $stJs .= montaListaGestor();
    
        $stJs .= "f.inCgmGestor.value = ''; \n";
        $stJs .= "d.getElementById('stNomCgmGestor').innerHTML = '&nbsp;';\n";
        
        $stJs .= "f.dtDataInicio.value = '';\n";
        $stJs .= "f.dtDataFim.value = '';\n";
        $stJs .= "f.btIncluirGestor.value = 'Incluir';\n";
        $stJs .= "f.hdnInId.value = '';\n";
        $stJs .= "f.stHdnAcao.value = '';\n";
    }
    
    return $stJs;
}

function excluirGestor()
{
    $arTemp = $arTempRemovido = array();

    $arGestores = Sessao::read('arGestores');

    $arGestoresRemovidos = Sessao::read('arGestoresRemovidos');

    foreach ($arGestores as $arGestor) {
        if ($arGestor['inId'] != $_GET['inId']) {
            $arTemp[] = $arGestor;
        } else {
            $arTempRemovido[] = $arGestor;
        }
    }

    $arGestores = $arTemp;
    $arGestoresRemovidos[] = $arTempRemovido;
    
    Sessao::write('arGestoresRemovidos', $arGestoresRemovidos);
    Sessao::write('arGestores', $arGestores);

    $stJs .= montaListaGestor();
    
    SistemaLegado::executaFrameOculto($stJs);
}

function alterarGestor()
{    
    $arGestores = Sessao::read('arGestores');
    foreach($arGestores as $arGestor){
        if ( $arGestor["inId"] == $_GET["inId"] ) {
            $stJs .= "f.inCgmGestor.value = '".$arGestor['cgm_gestor']."';\n";
            $stJs .= "d.getElementById('stNomCgmGestor').innerHTML = '".$arGestor['nom_cgm']."';\n";
            $stJs .= "f.dtDataInicio.value = '".$arGestor['dt_inicio_vigencia']."';\n";
            $stJs .= "f.dtDataFim.value = '".$arGestor['dt_fim_vigencia']."';\n";
            $stJs .= "f.hdnInId.value = '".$arGestor["inId"]."';\n";
            $stJs .= "f.stNomCgmGestor.value = '".$arGestor['nom_cgm']."';\n";
            $stJs .= "f.btIncluirGestor.value = 'Alterar';\n";
            $stJs .= "f.stHdnAcao.value = 'alterar';\n";
        }
    }
    return $stJs;
}

// Acoes por pagina
switch ($stCtrl) {
    
    case "incluirGestor":
        $stJs .= incluirGestor();
    break;

    case 'excluirGestor':
        $stJs .= excluirGestor();
        break;
    
    case 'alterarGestor':
        $stJs .= alterarGestor();
        break;
}

if ($stJs) {
    SistemaLegado::executaFrameOculto($stJs);
}

?>
