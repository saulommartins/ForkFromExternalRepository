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

/**
  * Pacote de configuração do TCEPE
  * Data de Criação: 08/10/2014
  * @author Desenvolvedor: Lisiane Morais
  *
  $Id: $
  *
  */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
require_once CAM_GPC_TCEPE_MAPEAMENTO.'TTCEPETipoCredor.class.php';

$stPrograma = "ManterTipoCredor";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stCtrl = $request->get('stCtrl');
$stAcao = $request->get('stAcao');

include_once ($pgJs);

function processarForm($boExecuta = false, $stArquivo = "Form", $stAcao = "manter")
{
    switch ($stAcao) {
        case "manter":
            $stJs .= montarLista();
        break;
    }

    if ($boExecuta) {
        sistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function montarLista()
{
    //$obTTCEPETipoCredor = new TTCEPETipoCredor();

    $rsRecordSet = new RecordSet();
    if (Sessao::read('arCGMTipoCredor') != "") {
        $rsRecordSet->preenche(Sessao::read('arCGMTipoCredor'));
    }

    $obLista = new Lista;
    $obLista->setMostraPaginacao( false );
    $obLista->setTitulo( "Lista de CGM" );

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
    $obLista->ultimoCabecalho->addConteudo( "Nome" );
    $obLista->ultimoCabecalho->setWidth( 37 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Tipo de Credor" );
    $obLista->ultimoCabecalho->setWidth( 12 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 4 );
    $obLista->commitCabecalho();
        
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "cgm_credor" );
    $obLista->ultimoDado->setAlinhamento('ESQUERDA' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "nom_cgm" );
    $obLista->ultimoDado->setAlinhamento('ESQUERDA' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "nom_tipo_credor" );
    $obLista->ultimoDado->setAlinhamento('ESQUERDA' );
    $obLista->commitDado();
    
    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "ALTERAR" );
    $obLista->ultimaAcao->setFuncao( true );
    $obLista->ultimaAcao->setLink( "JavaScript:modificaDado('alterarTipoCredor');" );
    $obLista->ultimaAcao->addCampo("1" , "inId");
    $obLista->commitAcao();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "EXCLUIR" );
    $obLista->ultimaAcao->setFuncao( true );
    $obLista->ultimaAcao->setLink( "JavaScript:modificaDado('excluirTipoCredor');" );
    $obLista->ultimaAcao->addCampo("1" , "inId");
    $obLista->commitAcao();

    $obLista->montaHTML();
    $stHtml = $obLista->getHTML();
    $stHtml = str_replace("\n","",$stHtml);
    $stHtml = str_replace("  ","",$stHtml);
    $stHtml = str_replace("'","\\'",$stHtml);
    $stJs .= "d.getElementById('spnListaCredor').innerHTML = '".$stHtml."';";

    return $stJs;
}

function incluirLista() {
    if ( !($_REQUEST["stAcao"] == "alterar")) {
        
        $obErro = new Erro();
        if ( $_REQUEST["inCodCGM"] == "" ) {
            return "alertaAviso('Preencher o campo CGM.','form','erro','".Sessao::getId()."');\n";
        }
        if ( $_REQUEST["inCodTipoCredor"] == "" ) {
            return "alertaAviso('Preencher o tipo de credor.','form','erro','".Sessao::getId()."');\n";
        }
        
        $arCGMTipoCredor = Sessao::read('arCGMTipoCredor');

        $arNovoCredor = array();
        $arNovoCredor["cgm_credor"]      = $_REQUEST["inCodCGM"];
        $arNovoCredor["nom_cgm"]         = $_REQUEST["stNomCGM"];
        $arNovoCredor["cod_tipo_credor"] = $_REQUEST["inCodTipoCredor"];
        $arNovoCredor["nom_tipo_credor"] = $_REQUEST["stNomTipoCredor"];
        $arNovoCredor["inId"] = count($arCGMTipoCredor);
        
        if ( $arCGMTipoCredor != "" ) {
            foreach ($arCGMTipoCredor as $arrCredores) {   
                if ($arrCredores['cgm_credor'] == $arNovoCredor['cgm_credor']){
                    
                    $obErro->setDescricao("Esta CGM já está cadastrado!");
                }
            }
        }
       
        if ( !$obErro->ocorreu() ) {
            $arCGMTipoCredor[] = $arNovoCredor;
            Sessao::write('arCGMTipoCredor',$arCGMTipoCredor);
        }   
    }else {

        $obErro  = new Erro();
    
        $arCGMTipoCredor = Sessao::read('arCGMTipoCredor');
        
        foreach ($arCGMTipoCredor as $arrCredores) {   
            if ( $arrCredores['inId'] <> $_REQUEST['hdnInId'] ) {
                
                if ( $_REQUEST["inCodCGM"] == "" ) {
                            return "alertaAviso('Preencher o campo CGM.','form','erro','".Sessao::getId()."');\n";
                    }
                if ( $_REQUEST["inCodTipoCredor"] == "" ) {
                            return "alertaAviso('Preencher o tipo de credor.','form','erro','".Sessao::getId()."');\n";
                }
            }
        }
        
        if ( !$obErro->ocorreu() ) {
            foreach ($arCGMTipoCredor as $key => $arCredor) {
                if ($arCredor['inId'] == $_REQUEST['hdnInId']) {
                    $arCGMTipoCredor[$key]['cgm_credor']              = $_REQUEST['inCodCGM'];
                    $arCGMTipoCredor[$key]['nom_cgm']                 = $_REQUEST['stNomCGMCredor'];
                    $arCGMTipoCredor[$key]['cod_tipo_credor']         = $_REQUEST['inCodTipoCredor'];
                    $arCGMTipoCredor[$key]['nom_tipo_credor']         = $_REQUEST['stNomTipoCredor'];
                   
                    Sessao::write('arCGMTipoCredor',$arCGMTipoCredor);     
                    break;
                }
            }
        }
    } 
    
    if ( $obErro->ocorreu() ) {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    } else {
        $stJs .= montarLista();
    
        $stJs .= "f.inCGM.value = ''; \n";
        $stJs .= "d.getElementById('stNomCGM').innerHTML = '&nbsp;';\n";
        $stJs .= "f.inCodTipoCredor.value = '';\n";
        $stJs .= "f.btIncluirCGM.value = 'Incluir';\n";
        $stJs .= "f.hdnInId.value = '';\n";
        $stJs .= "f.stAcao.value = '';\n";
    }
    return $stJs;
}

function excluirCredor()
{
    $arTemp = $arTempRemovido = array();

    $arCGMTipoCredor = Sessao::read('arCGMTipoCredor');

    $arCredoresRemovidos = Sessao::read('arCredoresRemovidos');

    foreach ($arCGMTipoCredor as $arCredor) {
        if ($arCredor['inId'] != $_GET['inId']) {
            $arTemp[] = $arCredor;
        } else {
            $arTempRemovido[] = $arCredor;
        }
    }

    $arCGMTipoCredor = $arTemp;
    $arCredoresRemovidos[] = $arTempRemovido;
    
    Sessao::write('arCredoresRemovidos', $arCredoresRemovidos);
    Sessao::write('arCGMTipoCredor', $arCGMTipoCredor);

    $stJs .= montarLista();
    
    SistemaLegado::executaFrameOculto($stJs);
}

function alterarCredor()
{    
    $arCGMTipoCredor = Sessao::read('arCGMTipoCredor');
    foreach($arCGMTipoCredor as $arCredor){

        if ( $arCredor["inId"] == $_GET["inId"] ) {
            $stJs .= "f.inCGM.value = '".$arCredor['cgm_credor']."';\n";
            $stJs .= "d.getElementById('stNomCGM').innerHTML = '".$arCredor['nom_cgm']."';\n";
            $stJs .= "f.inCodTipoCredor.value = '".$arCredor['cod_tipo_credor']."';\n";
            $stJs .= "f.hdnInId.value = '".$arCredor["inId"]."';\n";
            $stJs .= "f.stNomTipoCredor.value = '".$arCredor['nom_tipo_credor']."';\n";
            $stJs .= "f.btIncluirCGM.value = 'Alterar';\n";
            $stJs .= "f.stAcao.value = 'alterar';\n";
            $stJs .= "f.stNomCGMCredor.value = '".$arCredor['nom_cgm']."';\n";
        }
    }
    return $stJs;
}
switch ($stCtrl) {

    case 'incluirLista':
       $stJs .= incluirLista();
    break;

    case 'montarLista':
         $stJs .= montarLista();
    break;

    case "alterarTipoCredor":
        $stJs .= alterarCredor();
    break;

    case 'excluirTipoCredor':
        $stJs .= excluirCredor();
    break;

}

if ($stJs) {
    SistemaLegado::executaFrameOculto($stJs);
}