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
    * Página Oculta da Causa Rescisao
    * Data de Criação   : 04/05/2005

    * @author Analista: Leandro Oliveira
    * @author Desenvolvedor: Vandré Miguel Ramos

    * @ignore

    $Revision: 31465 $
    $Name$
    $Author: andre $
    $Date: 2007-03-29 16:14:50 -0300 (Qui, 29 Mar 2007) $

    * Casos de uso :uc-04.04.10

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GRH_PES_MAPEAMENTO."TPessoalCausaRescisao.class.php";

$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];

function addElementoArray($inId,$inCodCasoCausa="")
{
    $arTemp = array();
    $arTemp['inId'] = $inId;
    $arTemp['inCodCasoCausa']           = $inCodCasoCausa;
    $arTemp['stDescricaoCaso']          = $_POST['stDescricaoCaso'];
    $arTemp['inCodPeriodo']             = $_POST['inCodPeriodo'];
    $arTemp['inCodRegimeSelecionados']  = $_POST['inCodRegimeSelecionados'];
    $arTemp['boPagaAvisoPrevio']        = $_POST['boPagaAvisoPrevio'];
    $arTemp['boFeriasVencidas']         = $_POST['boFeriasVencidas'];
    $arTemp['boFeriasProporcionais']    = $_POST['boFeriasProporcionais'];
    $arTemp['inCodSaqueFGTS']           = $_POST['inCodSaqueFGTS'];
    $arTemp['flMultaFGTS']              = $_POST['flMultaFGTS'];
    $arTemp['flContribuicao']           = $_POST['flContribuicao'];
    $arTemp['boFeriasFGTS']             = $_POST['boFeriasFGTS'];
    $arTemp['bo13FGTS']                 = $_POST['bo13FGTS'];
    $arTemp['boAvisoPrevioFGTS']        = $_POST['boAvisoPrevioFGTS'];
    $arTemp['boFeriasIRRF']             = $_POST['boFeriasIRRF'];
    $arTemp['bo13IRRF']                 = $_POST['bo13IRRF'];
    $arTemp['boAvisoPrevioIRRF']        = $_POST['boAvisoPrevioIRRF'];
    $arTemp['boFeriasPrevidencia']      = $_POST['boFeriasPrevidencia'];
    $arTemp['bo13Previdencia']          = $_POST['bo13Previdencia'];
    $arTemp['boAvisoPrevioPrevidencia'] = $_POST['boAvisoPrevioPrevidencia'];
    $arTemp['boArtigo479']              = $_POST['boArtigo479'];

    return $arTemp;
}

function incluirCaso()
{
    $obTPessoalCausaRescisao = new TPessoalCausaRescisao();
    $obErro = new Erro();
    $arCasosCausa = Sessao::read('arCasosCausa');

    $stFiltro = " WHERE num_causa = '".$_POST['inNumCausa']."'";
    $obTPessoalCausaRescisao->recuperaTodos($rsNumeroCausaRescisao, $stFiltro);
        
    if ( $rsNumeroCausaRescisao->getNumLinhas() >= 1 ) {
        $obErro->setDescricao( "Código ".$_POST['inNumCausa']." já cadastrado. Informe um código diferente." );
    }

    foreach ($arCasosCausa as $arCasoCausa) {
        if ($arCasoCausa['stDescricaoCaso'] == $_POST['stDescricaoCaso']) {
            $obErro->setDescricao("A descrição informada já existe na lista de casos de causa de rescisão.");
            break;
        }
    }
    
    if ( !$obErro->ocorreu() ) {
        $arCasosCausa[] = addElementoArray(count($arCasosCausa));
        Sessao::write('arCasosCausa', $arCasosCausa);
    } else {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    }
    
    $stJs .= montaListaCasosCausa();
    $stJs .= limpaCamposLista();

    return $stJs;
}

function alterarCaso()
{
    $obErro = new Erro();
    $arCasosCausa = Sessao::read('arCasosCausa');
    $inIdAlterar  = Sessao::read('inIdAlterar');
    foreach ($arCasosCausa as $arCasoCausa) {
        if ($arCasoCausa['stDescricaoCaso'] == $_POST['stDescricaoCaso'] and $arCasoCausa['inId'] != $inIdAlterar) {
            $obErro->setDescricao("A descrição informada já existe na lista de casos de causa de rescisão.");
            break;
        }
    }
    if ( !$obErro->ocorreu() ) {
        $inCodCasoCausa = $arCasosCausa[$inIdAlterar]['inCodCasoCausa'];
        $arCasosCausa[$inIdAlterar] = addElementoArray($inIdAlterar,$inCodCasoCausa);
        Sessao::write('arCasosCausa', $arCasosCausa);
    } else {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    }
    $stJs .= montaListaCasosCausa();
    $stJs .= limpaCamposLista();

    return $stJs;
}

function excluirCaso()
{
    $arTemp       = array();
    $arCasosCausa = Sessao::read('arCasosCausa');

    foreach ($arCasosCausa as $arCasoCausa) {
        if ($arCasoCausa['inId'] != $_GET['inId']) {
            $arTemp[] = $arCasoCausa;
        }
    }
    $arCasosCausa = $arTemp;
    Sessao::write('arCasosCausa', $arCasosCausa);
    $stJs .= montaListaCasosCausa();

    return $stJs;
}

function limpaCamposLista()
{
    Sessao::remove('inIdAlterar');
    $stJs .= "f.stDescricaoCaso.value = '';\n";
    $stJs .= "f.inCodPeriodo.value = '';\n";
    $stJs .= "f.inCodTxtPeriodo.value = '';\n";
    $stJs .= "f.boPagaAvisoPrevio.checked = false;\n";
    $stJs .= "f.boFeriasVencidas.checked = false;\n";
    $stJs .= "f.boFeriasProporcionais.checked = false;\n";
    $stJs .= "f.inCodSaqueFGTS.value = '';\n";
    $stJs .= "f.flMultaFGTS.value = '';\n";
    $stJs .= "f.flContribuicao.value = '';\n";
    $stJs .= "f.boFeriasFGTS.checked = false;\n";
    $stJs .= "f.bo13FGTS.checked = false;\n";
    $stJs .= "f.boAvisoPrevioFGTS.checked = false;\n";
    $stJs .= "f.boFeriasIRRF.checked = false;\n";
    $stJs .= "f.bo13IRRF.checked = false;\n";
    $stJs .= "f.boAvisoPrevioIRRF.checked = false;\n";
    $stJs .= "f.boFeriasPrevidencia.checked = false;\n";
    $stJs .= "f.bo13Previdencia.checked = false;\n";
    $stJs .= "f.boAvisoPrevioPrevidencia.checked = false;\n";
    $stJs .= "f.boArtigo479.checked = false;\n";
    $stJs .= "parent.frames[2].passaItem('document.frm.inCodRegimeSelecionados','document.frm.inCodRegimeDisponiveis','tudo');\n";
    $stJs .= "f.btnIncluir.disabled = false;\n";
    $stJs .= "f.btnAlterar.disabled = true;\n";

    return $stJs;
}

function montaListaCasosCausa()
{
    $rsRecordSet = new RecordSet();
    $rsRecordSet->preenche(Sessao::read('arCasosCausa'));

    $obLista = new Lista;
    $obLista->setMostraPaginacao( false );
    $obLista->setTitulo( "Casos cadastrados" );

    $obLista->setRecordSet( $rsRecordSet );
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 3 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Descrição" );
    $obLista->ultimoCabecalho->setWidth( 85 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 15 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "stDescricaoCaso" );
    $obLista->ultimoDado->setAlinhamento('ESQUERDA' );
    $obLista->commitDado();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "ALTERAR" );
    $obLista->ultimaAcao->setFuncao( true );
    $obLista->ultimaAcao->setLink( "JavaScript:alteraDado('montaAlterar');" );
    $obLista->ultimaAcao->addCampo("1","inId");
    $obLista->commitAcao();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "EXCLUIR" );
    $obLista->ultimaAcao->setFuncao( true );
    $obLista->ultimaAcao->setLink( "JavaScript:alteraDado('excluirCaso');" );
    $obLista->ultimaAcao->addCampo("1","inId");
    $obLista->commitAcao();

    $obLista->montaHTML();
    $stHtml = $obLista->getHTML();
    $stHtml = str_replace("\n","",$stHtml);
    $stHtml = str_replace("  ","",$stHtml);
    $stHtml = str_replace("'","\\'",$stHtml);
    $stJs .= "d.getElementById('spnSubDivisao').innerHTML = '".$stHtml."';";

    return $stJs;
}

function montaAlterar()
{
    Sessao::write('inIdAlterar', $_GET['inId']);
    $arCasosCausa = Sessao::read('arCasosCausa');
    $arCasoCausa  = $arCasosCausa[$_GET['inId']];
    $stJs .= "f.stDescricaoCaso.value = '".$arCasoCausa['stDescricaoCaso']."';\n";
    $stJs .= "f.inCodPeriodo.value = '".$arCasoCausa['inCodPeriodo']."';\n";
    $stJs .= "f.inCodTxtPeriodo.value = '".$arCasoCausa['inCodPeriodo']."';\n";
    $stJs .= ($arCasoCausa['boPagaAvisoPrevio']        == 't') ? "f.boPagaAvisoPrevio.checked = true;\n" :"f.boPagaAvisoPrevio.checked = false;\n";
    $stJs .= ($arCasoCausa['boFeriasVencidas']         == 't') ? "f.boFeriasVencidas.checked = true;\n" :"f.boFeriasVencidas.checked = false;\n";
    $stJs .= ($arCasoCausa['boFeriasProporcionais']    == 't') ? "f.boFeriasProporcionais.checked = true;\n" :"f.boFeriasProporcionais.checked = false;\n";
    $stJs .= "f.inCodSaqueFGTS.value = '".$arCasoCausa['inCodSaqueFGTS']."';\n";
    $stJs .= "f.flMultaFGTS.value = '".$arCasoCausa['flMultaFGTS']."';\n";
    $stJs .= "f.flContribuicao.value = '".$arCasoCausa['flContribuicao']."';\n";
    $stJs .= ($arCasoCausa['boFeriasFGTS']             == 't') ? "f.boFeriasFGTS.checked = true;\n" : "f.boFeriasFGTS.checked = false;\n";
    $stJs .= ($arCasoCausa['bo13FGTS']                 == 't') ? "f.bo13FGTS.checked = true;\n" : "f.bo13FGTS.checked = false;\n";
    $stJs .= ($arCasoCausa['boAvisoPrevioFGTS']        == 't') ? "f.boAvisoPrevioFGTS.checked = true;\n" : "f.boAvisoPrevioFGTS.checked = false;\n";
    $stJs .= ($arCasoCausa['boFeriasIRRF']             == 't') ? "f.boFeriasIRRF.checked = true;\n" : "f.boFeriasIRRF.checked = false;\n";
    $stJs .= ($arCasoCausa['bo13IRRF']                 == 't') ? "f.bo13IRRF.checked = true;\n" : "f.bo13IRRF.checked = false;\n";
    $stJs .= ($arCasoCausa['boAvisoPrevioIRRF']        == 't') ? "f.boAvisoPrevioIRRF.checked = true;\n" : "f.boAvisoPrevioIRRF.checked = false;\n";
    $stJs .= ($arCasoCausa['boFeriasPrevidencia']      == 't') ? "f.boFeriasPrevidencia.checked = true;\n" : "f.boFeriasPrevidencia.checked = false;\n";
    $stJs .= ($arCasoCausa['bo13Previdencia']          == 't') ? "f.bo13Previdencia.checked = true;\n" : "f.bo13Previdencia.checked = false;\n";
    $stJs .= ($arCasoCausa['boAvisoPrevioPrevidencia'] == 't') ? "f.boAvisoPrevioPrevidencia.checked = true ;\n": "f.boAvisoPrevioPrevidencia.checked = false ;\n";
    $stJs .= ($arCasoCausa['boArtigo479']              == 't') ? "f.boArtigo479.checked = true ;\n" : "f.boArtigo479.checked = false ;\n";
    $stJs .= "limpaSelect(f.inCodRegimeSelecionados,0); \n";
    $inCount = 0;
    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalSubDivisao.class.php");
    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalRegime.class.php");
    $obTPessoalSubDivisao = new TPessoalSubDivisao();
    $obTPessoalRegime = new TPessoalRegime();
    foreach ($arCasoCausa['inCodRegimeSelecionados'] as $inCodSubDivisaoSel) {
        $obTPessoalSubDivisao->setDado("cod_sub_divisao",$inCodSubDivisaoSel);
        $obTPessoalSubDivisao->recuperaPorChave($rsSubDivisao);
        $obTPessoalRegime->setDado("cod_regime",$rsSubDivisao->getCampo("cod_regime"));
        $obTPessoalRegime->recuperaPorChave($rsRegime);
        $stDesc = $rsRegime->getCampo("descricao")."/".$rsSubDivisao->getCampo("descricao");
        $stJs .= "f.inCodRegimeSelecionados.options[$inCount] = new Option('".$stDesc."','".$inCodSubDivisaoSel."',''); \n";
        $inCount++;
    }
    $stJs .= "limpaSelect(f.inCodRegimeDisponiveis,0); \n";
    $obTPessoalSubDivisao->recuperaTodos($rsSubDivisao);
    $inCount = 0;
    while (!$rsSubDivisao->eof()) {
        if ( !in_array($rsSubDivisao->getCampo("cod_sub_divisao"),$arCasoCausa['inCodRegimeSelecionados']) ) {
            $obTPessoalRegime->setDado("cod_regime",$rsSubDivisao->getCampo("cod_regime"));
            $obTPessoalRegime->recuperaPorChave($rsRegime);
            $stDesc = $rsRegime->getCampo("descricao")."/".$rsSubDivisao->getCampo("descricao");
            $stJs .= "f.inCodRegimeDisponiveis.options[$inCount] = new Option('".$stDesc."','".$rsSubDivisao->getCampo("cod_sub_divisao")."',''); \n";
            $inCount++;
        }
        $rsSubDivisao->proximo();
    }
    $stJs .= "f.btnIncluir.disabled = true;\n";
    $stJs .= "f.btnAlterar.disabled = false;\n";

    return $stJs;
}

switch ($stCtrl) {
    case "incluirCaso":
        $stJs .= incluirCaso();
        break;
    case "alterarCaso":
        $stJs .= alterarCaso();
        break;
    case "excluirCaso":
        $stJs .= excluirCaso();
        break;
    case "limparCaso":
        $stJs .= limpaCamposLista();
        break;
    case "montaListaCasosCausa":
        $stJs .= montaListaCasosCausa();
        break;
    case "montaAlterar":
        $stJs .= montaAlterar();
        break;

}
if($stJs)
sistemaLegado::executaFrameOculto($stJs);

?>
