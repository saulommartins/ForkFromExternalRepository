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
* Oculto de Configuração de Ratificador TCM-BA
* Data de Criação: 11/08/2015

* @author Analista: Ane Caroline Fiegenbaum Pereira
* @author Desenvolvedor: Jean Silva 

$Id: OCManterConfiguracaoRatificador.php 63383 2015-08-24 12:34:24Z michel $
*/


include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GA_CGM_NEGOCIO.'RCGM.class.php';
include_once CAM_GPC_TCMBA_MAPEAMENTO.'TTCMBAConfiguracaoRatificador.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoRatificador";
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
            $stJs .= montaListaRatificador();
        break;
    }

    if ($boExecuta) {
        sistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function montaListaRatificador()
{
    $rsRecordSet = new RecordSet();
    if (Sessao::read('arRatificadores') != "") {
        $rsRecordSet->preenche(Sessao::read('arRatificadores'));
    }

    $obLista = new Lista;
    $obLista->setMostraPaginacao( false );
    $obLista->setTitulo( "Ratificador da Unidade Orçamentária" );

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
    $obLista->ultimoCabecalho->addConteudo( "Nome do Ratificador" );
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
    $obLista->ultimoCabecalho->addConteudo( "Tipo de Responsável" );
    $obLista->ultimoCabecalho->setWidth( 15 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 4 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "cgm_ratificador" );
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
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "tipo_responsavel_desc" );
    $obLista->ultimoDado->setAlinhamento('ESQUERDA' );
    $obLista->commitDado();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "ALTERAR" );
    $obLista->ultimaAcao->setFuncao( true );
    $obLista->ultimaAcao->setLink( "JavaScript:modificaDado('alterarRatificador');" );
    $obLista->ultimaAcao->addCampo("1" , "inId");
    $obLista->commitAcao();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "EXCLUIR" );
    $obLista->ultimaAcao->setFuncao( true );
    $obLista->ultimaAcao->setLink( "JavaScript:modificaDado('excluirRatificador');" );
    $obLista->ultimaAcao->addCampo("1" , "inId");
    $obLista->commitAcao();

    $obLista->montaHTML();
    $stHtml = $obLista->getHTML();
    $stHtml = str_replace("\n","",$stHtml);
    $stHtml = str_replace("  ","",$stHtml);
    $stHtml = str_replace("'","\\'",$stHtml);
    $stJs .= "d.getElementById('spnCGMsRatificadores').innerHTML = '".$stHtml."';";

    return $stJs;
}

function incluirRatificador()
{    
    $responsavel = explode('-', $_REQUEST['tipo_responsavel']);
     
    for($i=1;$i<count($responsavel);$i++){
        $stResponsavel = ($stResponsavel) ? $stResponsavel.'-'.$responsavel[$i] : $responsavel[$i];
    }
   
    if ( !($_REQUEST["stHdnAcao"] == "alterar")) {        
        $obErro = new Erro();
        if ( $_REQUEST["inCgmRatificador"] == "" ) {
            return "alertaAviso('Preencher o campo Ratificador.','form','erro','".Sessao::getId()."');\n";
        }
        if ( $_REQUEST["dtDataInicio"] == "" ) {
            return "alertaAviso('Preencher o campo Início da Vigência.','form','erro','".Sessao::getId()."');\n";
        }
        if ( $_REQUEST["dtDataFim"] == "" ) {
            return "alertaAviso('Preencher o campo Fim da Vigência.','form','erro','".Sessao::getId()."');\n";
        }

        $arRatificadores = Sessao::read('arRatificadores');

        $arNovoRatificador = array();
        $arNovoRatificador["cod_entidade"] = $_REQUEST["hdnCodEntidade"];
        $arNovoRatificador["cgm_ratificador"] = $_REQUEST["inCgmRatificador"];
        $arNovoRatificador["nom_cgm"] = $_REQUEST["stNomCgmRatificador"];
        $arNovoRatificador["dt_inicio_vigencia"] = $_REQUEST["dtDataInicio"];
        $arNovoRatificador["dt_fim_vigencia"] = $_REQUEST["dtDataFim"];
        $arNovoRatificador["num_orgao"] = $_REQUEST["inMontaCodOrgaoM"];
        $arNovoRatificador["num_unidade"] = $_REQUEST["inMontaCodUnidadeM"];
        $arNovoRatificador["cod_tipo_responsavel"] = $responsavel[0];
        $arNovoRatificador["tipo_responsavel_desc"] = $stResponsavel;
        $arNovoRatificador["inId"] = count($arRatificadores);
        
        if ( $arRatificadores != "" ) {
            foreach ($arRatificadores as $arrRatificadores) {   
                if ($arrRatificadores['cgm_ratificador'] == $arNovoRatificador['cgm_ratificador'] &&
                    $arrRatificadores['cod_entidade'] == $arNovoRatificador['cod_entidade'] &&
                    $arrRatificadores['num_orgao'] == $arNovoRatificador['inMontaCodOrgaoM'] &&
                    $arrRatificadores['num_unidade'] == $arNovoRatificador['inMontaCodUnidadeM']){
                    
                    $obErro->setDescricao("Esta CGM já está cadastrado para essa Unidade Orçamentária!");
                }
                
                elseif ( SistemaLegado::comparaDatas($arrRatificadores['dt_fim_vigencia'], $arNovoRatificador['dt_inicio_vigencia'], true)
                         AND SistemaLegado::comparaDatas($arNovoRatificador['dt_fim_vigencia'],$arrRatificadores['dt_inicio_vigencia'] , true)){
                    $obErro->setDescricao("Já possui Ratificador cadastrado, para essa Unidade Orçamentária, no período informado!");
                }
            }
        }
        
        if ( SistemaLegado::comparaDatas($arNovoRatificador['dt_inicio_vigencia'], $arNovoRatificador['dt_fim_vigencia'], true) ) {
                    $obErro->setDescricao("A data final da Vigência deve ser maior que a Data Inicial!");
            } 
        if ( !$obErro->ocorreu() ) {
            $arRatificadores[] = $arNovoRatificador;
            Sessao::write('arRatificadores',$arRatificadores);
        }    
    } else {
        $obErro  = new Erro();
    
        $arRatificadores = Sessao::read('arRatificadores');

        foreach ($arRatificadores as $arrRatificadores) {
            if ( $arrRatificadores['inId'] <> $_REQUEST['hdnInId'] ) {

                if ( $arrRatificadores['dt_inicio_vigencia'] == $_REQUEST['dtDataInicio'] AND $arrRatificadores['dt_fim_vigencia'] == $_REQUEST['dtDataFim'] ){
                    $obErro->setDescricao("Já possui Ratificador cadastrado, para essa Unidade Orçamentária, no período informado!");
                }
                
                elseif ( SistemaLegado::comparaDatas($arrRatificadores['dt_fim_vigencia'], $_REQUEST['dtDataInicio'], true)
                        AND SistemaLegado::comparaDatas($_REQUEST['dtDataFim'],$arrRatificadores['dt_inicio_vigencia'] , true)) {
                    $obErro->setDescricao("Já possui Ratificador cadastrado, para essa Unidade Orçamentária, no período informado!");
                }
            }
        }
        
        if ( SistemaLegado::comparaDatas($_REQUEST['dtDataInicio'], $_REQUEST['dtDataFim'], true) ) {
            $obErro->setDescricao("A data final da Vigência deve ser maior que a Data Inicial!");
        }
            
        if ( !$obErro->ocorreu() ) {
            foreach ($arRatificadores as $key => $arRatificador) {
                if ($arRatificador['inId'] == $_REQUEST['hdnInId']) {
                    $arRatificadores[$key]['cod_entidade']            = $_REQUEST['hdnCodEntidade'];
                    $arRatificadores[$key]['cgm_ratificador']           = $_REQUEST['inCgmRatificador'];
                    $arRatificadores[$key]['nom_cgm']                 = $_REQUEST['stNomCgmRatificador'];
                    $arRatificadores[$key]['dt_inicio_vigencia']      = $_REQUEST['dtDataInicio'];
                    $arRatificadores[$key]['dt_fim_vigencia']         = $_REQUEST['dtDataFim'];
                    $arRatificadores[$key]["num_orgao"]               = $_REQUEST["inMontaCodOrgaoM"];
                    $arRatificadores[$key]["num_unidade"]             = $_REQUEST["inMontaCodUnidadeM"];
                    $arRatificadores[$key]["cod_tipo_responsavel"]    = $responsavel[0];
                    $arRatificadores[$key]["tipo_responsavel_desc"]   = $stResponsavel;
                    Sessao::write('arRatificadores',$arRatificadores);     
                    break;
                }
            }
        }
    } 
    
    if ( $obErro->ocorreu() ) {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    } else {
        $stJs .= montaListaRatificador();
    
        $stJs .= "f.inCgmRatificador.value = ''; \n";
        $stJs .= "d.getElementById('stNomCgmRatificador').innerHTML = '&nbsp;';\n";
        $stJs .= "f.dtDataInicio.value = '';\n";
        $stJs .= "f.dtDataFim.value = '';\n";
        $stJs .= "f.tipo_responsavel.value = '';\n";
        $stJs .= "f.btIncluirRatificador.value = 'Incluir';\n";
        $stJs .= "f.hdnInId.value = '';\n";
        $stJs .= "f.stHdnAcao.value = '';\n";
    }
    
    return $stJs;
}

function excluirRatificador()
{
    $arTemp = $arTempRemovido = array();

    $arRatificadores = Sessao::read('arRatificadores');

    $arRatificadoresRemovidos = Sessao::read('arRatificadoresRemovidos');

    foreach ($arRatificadores as $arRatificador) {
        if ($arRatificador['inId'] != $_REQUEST['inId']) {
            $arTemp[] = $arRatificador;
        } else {
            $arTempRemovido[] = $arRatificador;
        }
    }

    $arRatificadores = $arTemp;
    $arRatificadoresRemovidos[] = $arTempRemovido;
    
    Sessao::write('arRatificadoresRemovidos', $arRatificadoresRemovidos);
    Sessao::write('arRatificadores', $arRatificadores);

    $stJs .= montaListaRatificador();
    
    SistemaLegado::executaFrameOculto($stJs);
}

function alterarRatificador()
{    
    $arRatificadores = Sessao::read('arRatificadores');
    
    foreach($arRatificadores as $arRatificador){
        if ( $arRatificador["inId"] == $_REQUEST["inId"] ) {
            $stJs .= "f.inCgmRatificador.value = '".$arRatificador['cgm_ratificador']."';                                                   \n";
            $stJs .= "d.getElementById('stNomCgmRatificador').innerHTML = '".$arRatificador['nom_cgm']."';                                  \n";
            $stJs .= "f.dtDataInicio.value = '".$arRatificador['dt_inicio_vigencia']."';                                                    \n";
            $stJs .= "f.dtDataFim.value = '".$arRatificador['dt_fim_vigencia']."';                                                          \n";
            $stJs .= "f.hdnInId.value = '".$arRatificador["inId"]."';                                                                       \n";
            $stJs .= "f.tipo_responsavel.value = '".$arRatificador['cod_tipo_responsavel'].'-'.$arRatificador['tipo_responsavel_desc']."';  \n";
            $stJs .= "f.stNomCgmRatificador.value = '".$arRatificador['nom_cgm']."';                                                        \n";
            $stJs .= "f.btIncluirRatificador.value = 'Alterar';                                                                             \n";
            $stJs .= "f.stHdnAcao.value = 'alterar';                                                                                        \n";
        }
    }
    return $stJs;
}

// Acoes por pagina
switch ($stCtrl) {
    
    case "incluirRatificador":
        $stJs .= incluirRatificador();
    break;

    case 'excluirRatificador':
        $stJs .= excluirRatificador();
        break;
    
    case 'alterarRatificador':
        $stJs .= alterarRatificador();
        break;
}

if ($stJs) {
    SistemaLegado::executaFrameOculto($stJs);
}

?>
