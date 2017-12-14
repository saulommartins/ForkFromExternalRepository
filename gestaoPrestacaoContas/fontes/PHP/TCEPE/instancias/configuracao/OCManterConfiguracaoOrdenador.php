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

 * $Id: OCManterConfiguracaoOrdenador.php 60205 2014-10-06 21:06:16Z lisiane $
 * $Name: $
 * $Revision: 59612 $
 * $Author: gelson $
 * $Date: 2014-09-02 09:00:51 -0300 (Ter, 02 Set 2014) $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GA_CGM_NEGOCIO."RCGM.class.php" );
include_once(CAM_GPC_TCEPE_MAPEAMENTO."TTCEPEConfiguracaoOrdenador.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoOrdenador";
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
            $stJs .= montaListaOrdenador();
        break;
    }

    if ($boExecuta) {
        sistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function montaListaOrdenador()
{
    $rsRecordSet = new RecordSet();
    if (Sessao::read('arOrdenadores') != "") {
        $rsRecordSet->preenche(Sessao::read('arOrdenadores'));
    }

    $obLista = new Lista;
    $obLista->setMostraPaginacao( false );
    $obLista->setTitulo( "Ordenador da Unidade Orçamentária" );

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
    $obLista->ultimoCabecalho->addConteudo( "Nome do Ordenador" );
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
    $obLista->ultimoDado->setCampo( "cgm_ordenador" );
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
    $obLista->ultimaAcao->setLink( "JavaScript:modificaDado('alterarOrdenador');" );
    $obLista->ultimaAcao->addCampo("1" , "inId");
    $obLista->commitAcao();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "EXCLUIR" );
    $obLista->ultimaAcao->setFuncao( true );
    $obLista->ultimaAcao->setLink( "JavaScript:modificaDado('excluirOrdenador');" );
    $obLista->ultimaAcao->addCampo("1" , "inId");
    $obLista->commitAcao();

    $obLista->montaHTML();
    $stHtml = $obLista->getHTML();
    $stHtml = str_replace("\n","",$stHtml);
    $stHtml = str_replace("  ","",$stHtml);
    $stHtml = str_replace("'","\\'",$stHtml);
    $stJs .= "d.getElementById('spnCGMsOrdenadores').innerHTML = '".$stHtml."';";

    return $stJs;
}

function incluirOrdenador()
{
    if ( !($_REQUEST["stHdnAcao"] == "alterar")) {
        
        $obTTCPEGConfiguracaoOrdenador = new TTCEPEConfiguracaoOrdenador();
        
        $obErro = new Erro();
        if ( $_REQUEST["inCgmOrdenador"] == "" ) {
            return "alertaAviso('Preencher o campo Ordenador.','form','erro','".Sessao::getId()."');\n";
        }
        if ( $_REQUEST["dtDataInicio"] == "" ) {
            return "alertaAviso('Preencher o campo Início da Vigência.','form','erro','".Sessao::getId()."');\n";
        }
        if ( $_REQUEST["dtDataFim"] == "" ) {
            return "alertaAviso('Preencher o campo Fim da Vigência.','form','erro','".Sessao::getId()."');\n";
        }

        $arOrdenadores = Sessao::read('arOrdenadores');

        $arNovoOrdenador = array();
        $arNovoOrdenador["cod_entidade"] = $_REQUEST["hdnCodEntidade"];
        $arNovoOrdenador["cgm_ordenador"] = $_REQUEST["inCgmOrdenador"];
        $arNovoOrdenador["nom_cgm"] = $_REQUEST["stNomCgmOrdenador"];
        $arNovoOrdenador["dt_inicio_vigencia"] = $_REQUEST["dtDataInicio"];
        $arNovoOrdenador["dt_fim_vigencia"] = $_REQUEST["dtDataFim"];
        $arNovoOrdenador["num_orgao"] = $_REQUEST["inMontaCodOrgaoM"];
        $arNovoOrdenador["num_unidade"] = $_REQUEST["inMontaCodUnidadeM"];
        $arNovoOrdenador["inId"] = count($arOrdenadores);
        
        if ( $arOrdenadores != "" ) {
            foreach ($arOrdenadores as $arrOrdenadores) {   
                if ($arrOrdenadores['cgm_ordenador'] == $arNovoOrdenador['cgm_ordenador'] &&
                    $arrOrdenadores['cod_entidade'] == $arNovoOrdenador['cod_entidade'] &&
                    $arrOrdenadores['num_orgao'] == $arNovoOrdenador['inMontaCodOrgaoM'] &&
                    $arrOrdenadores['num_unidade'] == $arNovoOrdenador['inMontaCodUnidadeM']){
                    
                    $obErro->setDescricao("Esta CGM já está cadastrado para essa Unidade Orçamentária!");
                }
                
                elseif ( SistemaLegado::comparaDatas($arrOrdenadores['dt_fim_vigencia'], $arNovoOrdenador['dt_inicio_vigencia'], true)
                         AND SistemaLegado::comparaDatas($arNovoOrdenador['dt_fim_vigencia'],$arrOrdenadores['dt_inicio_vigencia'] , true)){
                    $obErro->setDescricao("Já possui Ordenador cadastrado, para essa Unidade Orçamentária, no período informado!");
                }
            }
        }
        
        if ( SistemaLegado::comparaDatas($arNovoOrdenador['dt_inicio_vigencia'], $arNovoOrdenador['dt_fim_vigencia'], true) ) {
                    $obErro->setDescricao("A data final da Vigência deve ser maior que a Data Inicial!");
            } 
        if ( !$obErro->ocorreu() ) {
            $arOrdenadores[] = $arNovoOrdenador;
            Sessao::write('arOrdenadores',$arOrdenadores);
        }    
    } else {

        $obErro  = new Erro();
    
        $arOrdenadores = Sessao::read('arOrdenadores');

        foreach ($arOrdenadores as $arrOrdenadores) {
            if ( $arrOrdenadores['inId'] <> $_REQUEST['hdnInId'] ) {

                if ( $arrOrdenadores['dt_inicio_vigencia'] == $_REQUEST['dtDataInicio'] AND $arrOrdenadores['dt_fim_vigencia'] == $_REQUEST['dtDataFim'] ){
                    $obErro->setDescricao("Já possui Ordenador cadastrado, para essa Unidade Orçamentária, no período informado!");
                }
                
                elseif ( SistemaLegado::comparaDatas($arrOrdenadores['dt_fim_vigencia'], $_REQUEST['dtDataInicio'], true)
                        AND SistemaLegado::comparaDatas($_REQUEST['dtDataFim'],$arrOrdenadores['dt_inicio_vigencia'] , true)) {
                    $obErro->setDescricao("Já possui Ordenador cadastrado, para essa Unidade Orçamentária, no período informado!");
                }
            }
        }
        
        if ( SistemaLegado::comparaDatas($_REQUEST['dtDataInicio'], $_REQUEST['dtDataFim'], true) ) {
            $obErro->setDescricao("A data final da Vigência deve ser maior que a Data Inicial!");
        }
            
        if ( !$obErro->ocorreu() ) {
            foreach ($arOrdenadores as $key => $arOrdenador) {
                if ($arOrdenador['inId'] == $_REQUEST['hdnInId']) {
                    $arOrdenadores[$key]['cod_entidade']            = $_REQUEST['hdnCodEntidade'];
                    $arOrdenadores[$key]['cgm_ordenador']           = $_REQUEST['inCgmOrdenador'];
                    $arOrdenadores[$key]['nom_cgm']                 = $_REQUEST['stNomCgmOrdenador'];
                    $arOrdenadores[$key]['dt_inicio_vigencia']      = $_REQUEST['dtDataInicio'];
                    $arOrdenadores[$key]['dt_fim_vigencia']         = $_REQUEST['dtDataFim'];
                    $arOrdenadores[$key]["num_orgao"]               = $_REQUEST["inMontaCodOrgaoM"];
                    $arOrdenadores[$key]["num_unidade"]             = $_REQUEST["inMontaCodUnidadeM"];
                    
                    Sessao::write('arOrdenadores',$arOrdenadores);     
                    break;
                }
            }
        }
    } 
    
    if ( $obErro->ocorreu() ) {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    } else {
        $stJs .= montaListaOrdenador();
    
        $stJs .= "f.inCgmOrdenador.value = ''; \n";
        $stJs .= "d.getElementById('stNomCgmOrdenador').innerHTML = '&nbsp;';\n";
        
        $stJs .= "f.dtDataInicio.value = '';\n";
        $stJs .= "f.dtDataFim.value = '';\n";
        $stJs .= "f.btIncluirOrdenador.value = 'Incluir';\n";
        $stJs .= "f.hdnInId.value = '';\n";
        $stJs .= "f.stHdnAcao.value = '';\n";
    }
    
    return $stJs;
}

function excluirOrdenador()
{
    $arTemp = $arTempRemovido = array();

    $arOrdenadores = Sessao::read('arOrdenadores');

    $arOrdenadoresRemovidos = Sessao::read('arOrdenadoresRemovidos');

    foreach ($arOrdenadores as $arOrdenador) {
        if ($arOrdenador['inId'] != $_GET['inId']) {
            $arTemp[] = $arOrdenador;
        } else {
            $arTempRemovido[] = $arOrdenador;
        }
    }

    $arOrdenadores = $arTemp;
    $arOrdenadoresRemovidos[] = $arTempRemovido;
    
    Sessao::write('arOrdenadoresRemovidos', $arOrdenadoresRemovidos);
    Sessao::write('arOrdenadores', $arOrdenadores);

    $stJs .= montaListaOrdenador();
    
    SistemaLegado::executaFrameOculto($stJs);
}

function alterarOrdenador()
{    
    $arOrdenadores = Sessao::read('arOrdenadores');
    foreach($arOrdenadores as $arOrdenador){
        if ( $arOrdenador["inId"] == $_GET["inId"] ) {
            $stJs .= "f.inCgmOrdenador.value = '".$arOrdenador['cgm_ordenador']."';\n";
            $stJs .= "d.getElementById('stNomCgmOrdenador').innerHTML = '".$arOrdenador['nom_cgm']."';\n";
            $stJs .= "f.dtDataInicio.value = '".$arOrdenador['dt_inicio_vigencia']."';\n";
            $stJs .= "f.dtDataFim.value = '".$arOrdenador['dt_fim_vigencia']."';\n";
            $stJs .= "f.hdnInId.value = '".$arOrdenador["inId"]."';\n";
            $stJs .= "f.stNomCgmOrdenador.value = '".$arOrdenador['nom_cgm']."';\n";
            $stJs .= "f.btIncluirOrdenador.value = 'Alterar';\n";
            $stJs .= "f.stHdnAcao.value = 'alterar';\n";
        }
    }
    return $stJs;
}

// Acoes por pagina
switch ($stCtrl) {
    
    case "incluirOrdenador":
        $stJs .= incluirOrdenador();
    break;

    case 'excluirOrdenador':
        $stJs .= excluirOrdenador();
        break;
    
    case 'alterarOrdenador':
        $stJs .= alterarOrdenador();
        break;
}

if ($stJs) {
    SistemaLegado::executaFrameOculto($stJs);
}

?>
