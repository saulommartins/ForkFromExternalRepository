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
    * Página de Oculto do Conferência SEFIP
    * Data de Criação: 02/04/2007

    * @author Analista: Dagiane
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30829 $
    $Name$
    $Author: andre $
    $Date: 2007-07-09 10:56:29 -0300 (Seg, 09 Jul 2007) $

    * Casos de uso: uc-04.08.09
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GRH_PES_COMPONENTES."IFiltroContrato.class.php"                                       );
include_once( CAM_GRH_PES_COMPONENTES."IFiltroCGMContrato.class.php"                                    );
include_once( CAM_GRH_PES_COMPONENTES."ISelectMultiploRegSubCarEsp.class.php"                           );
include_once( CAM_GRH_PES_COMPONENTES."ISelectMultiploLotacao.class.php"                                );
include_once( CAM_GRH_PES_COMPONENTES."ISelectMultiploLocal.class.php"                                  );

//Define o nome dos arquivos PHP
$stPrograma = "ConferenciaSEFIP";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

function gerarSpan($stTipoFiltro="")
{
    $stTipoFiltro = ( $_GET['stTipoFiltro'] != "" ) ? $_GET['stTipoFiltro'] : $stTipoFiltro;
    switch ($stTipoFiltro) {
        case "contrato":
            $stHtml .= gerarSpanContrato($stJs);
        break;
        case "cgm_contrato":
            $stHtml .= gerarSpanCgmContrato($stJs);
        break;
        case "lotacao":
            $stHtml  .= gerarSpanLotacao($stEval);
        break;
        case "local":
            $stHtml .= gerarSpanLocal($stEval);
        break;
        case "atributos":
            $stHtml .= gerarSpanAtributos($stEval);
        break;
        case "geral":
            $stHtml .= gerarSpanGeral($stEval);
        break;
    }
    $stJs .= "d.getElementById('spnFiltro').innerHTML = '$stHtml';  \n";
    $stJs .= "f.stEval.value = '$stEval';                           \n";

    return $stJs;
}

function gerarSpanContrato(&$stJs)
{
    $obSpnContratos = new Span;
    $obSpnContratos->setid                      ( "spnContratos"                                                           );

    $obIFiltroContrato = new IFiltroContrato();
    $obIFiltroContrato->obIContratoDigitoVerificador->obTxtRegistroContrato->setNullBarra(false);
    $obFormulario = new Formulario;
    $obIFiltroContrato->geraFormulario($obFormulario);
    $obFormulario->Incluir("Contrato",array($obIFiltroContrato->obIContratoDigitoVerificador->obTxtRegistroContrato,
                                            $obIFiltroContrato->obLblCGM,
                                            $obIFiltroContrato->obHdnCGM),true);
    $obFormulario->addSpan($obSpnContratos);
    $obFormulario->montaInnerHTML();
    $obFormulario->obJavaScript->montaJavaScript();

    $stJs .= $obFormulario->getInnerJavascriptBarra();

    return $obFormulario->getHTML();
}

function gerarSpanCgmContrato(&$stJs)
{
    $obSpnContratos = new Span;
    $obSpnContratos->setid                      ( "spnContratos"                                                           );

    $obIFiltroCGMContrato = new IFiltroCGMContrato;
    $obIFiltroCGMContrato->obCmbContrato->setNullBarra(false);
    $obIFiltroCGMContrato->obBscCGM->setNullBarra(false);
    $obFormulario = new Formulario;
    $obIFiltroCGMContrato->geraFormulario($obFormulario);
    $obFormulario->Incluir("Contrato",array($obIFiltroCGMContrato->obCmbContrato,
                                            $obIFiltroCGMContrato->obBscCGM),true);

    $obFormulario->addSpan($obSpnContratos);
    $obFormulario->montaInnerHTML();
    $obFormulario->obJavaScript->montaJavaScript();
    $stJs .= $obFormulario->getInnerJavascriptBarra();

    return $obFormulario->getHTML();
}

function gerarSpanLotacao(&$stEval)
{
    $obISelectMultiploLotacao = new ISelectMultiploLotacao();
    $obISelectMultiploLotacao->setNull(false);
    $obFormulario = new Formulario;
    $obFormulario->addComponente($obISelectMultiploLotacao);
    $obFormulario->montaInnerHTML();
    $obFormulario->obJavaScript->montaJavaScript();
    $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
    $stEval = str_replace("\n","",$stEval);

    return $obFormulario->getHTML();
}

function gerarSpanLocal(&$stEval)
{
    $obISelectMultiploLocal = new ISelectMultiploLocal;
    $obISelectMultiploLocal->setNull(false);
    $obFormulario = new Formulario;
    $obFormulario->addComponente($obISelectMultiploLocal);
    $obFormulario->montaInnerHTML();
    $obFormulario->obJavaScript->montaJavaScript();
    $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
    $stEval = str_replace("\n","",$stEval);

    return $obFormulario->getHTML();
}

function gerarSpanAtributos(&$stEval)
{
    include_once(CAM_GRH_PES_NEGOCIO."RPessoalServidor.class.php");
    $obRPessoalServidor = new RPessoalServidor();
    $obRPessoalServidor->addContratoServidor();
    $obRPessoalServidor->roUltimoContratoServidor->obRCadastroDinamico->recuperaAtributosSelecionados( $rsAtributos );

    $obCmbAtributo = new Select();
    $obCmbAtributo->setRotulo("Atributo Dinâmico");
    $obCmbAtributo->setName("inCodAtributo");
    $obCmbAtributo->setTitle("Selecione o atributo dinâmico para filtro.");
    $obCmbAtributo->setNull(false);
    $obCmbAtributo->setCampoDesc("nom_atributo");
    $obCmbAtributo->setCampoId("cod_atributo");
    $obCmbAtributo->addOption("","Selecione");
    $obCmbAtributo->preencheCombo($rsAtributos);
    $obCmbAtributo->obEvento->setOnChange("montaParametrosGET('gerarSpanAtributosDinamicos','inCodAtributo');");

    $obSpnAtributo = new Span();
    $obSpnAtributo->setId("spnAtributo");

    $obFormulario = new Formulario();
    $obFormulario->addComponente($obCmbAtributo);
    $obFormulario->addSpan($obSpnAtributo);
    $obFormulario->montaInnerHTML();
    $obFormulario->obJavaScript->montaJavaScript();
    $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
    $stEval = str_replace("\n","",$stEval);

    return $obFormulario->getHTML();
}

function gerarSpanAtributosDinamicos()
{
    if ($_GET['inCodAtributo'] != "") {
        include_once(CAM_GRH_PES_NEGOCIO."RPessoalServidor.class.php");
        $obRPessoalServidor = new RPessoalServidor();
        $obRPessoalServidor->addContratoServidor();
        $obRPessoalServidor->roUltimoContratoServidor->obRCadastroDinamico->setChavePersistenteValores( array('cod_atributo'=>$_GET['inCodAtributo']) );
        $obRPessoalServidor->roUltimoContratoServidor->obRCadastroDinamico->recuperaAtributosSelecionados( $rsAtributos );

        $obMontaAtributos = new MontaAtributos;
        $obMontaAtributos->setTitulo     ( "Atributos"  );
        $obMontaAtributos->setName       ( "Atributo_"  );
        $obMontaAtributos->setRecordSet  ( $rsAtributos );

        $obHdnCodCadastro = new hidden();
        $obHdnCodCadastro->setName("inCodCadastro");
        $obHdnCodCadastro->setValue($rsAtributos->getCampo("cod_cadastro"));

        $obFormulario = new Formulario();
        $obFormulario->addHidden($obHdnCodCadastro);
        $obMontaAtributos->geraFormulario( $obFormulario );
        $obFormulario->montaInnerHTML();
        $stHtml = $obFormulario->getHTML();
        $Js = $obFormulario->getInnerJavaScript();
    }
    $stJs .= "d.getElementById('spnAtributo').innerHTML = '$stHtml';   \n";
    $stJs .= "f.stEval.value = f.stEval.value + '$Js';                 \n";

    return $stJs;
}

function gerarSpanGeral(&$stEval)
{
    $stEval = "";
    $stHtml = "";

    return $stHtml;
}

function processarFiltro()
{
    //$stJs .= gerarSpan("contrato");
    $stJs .= gerarSpanArrecadouFGTS();
    $stJs .= preencheConfiguracoesSEFIP();
    $stJs .= "document.frm.stTipoFiltro.value = 'geral'; \n";

    return $stJs;
}

function limparFiltro()
{
    $stJs .= gerarSpan("geral");
    $stJs .= "document.frm.stTipoFiltro.value = 'geral';                     \n";
    $stJs .= "f.inCodModalidadeRecolhimentoTxt.value = '';  \n";
    $stJs .= "f.inCodModalidadeRecolhimento.value = '';  \n";
    $stJs .= "f.inCodRecolhimentoTxt.value = '';  \n";
    $stJs .= "f.inCodRecolhimento.value = '';  \n";

    return $stJs;
}

function incluirContrato()
{
    $obErro    = new erro;
    if ( !$obErro->ocorreu() ) {
        $arContratos = ( is_array(Sessao::read('arContratos')) ) ? Sessao::read('arContratos') : array();
        foreach ($arContratos as $arContrato) {
            if ($arContrato['inContrato'] == $_GET['inContrato']) {
                $obErro->setDescricao("Matrícula já inserida na lista.");
                break;
            }
        }
    }
    if ( !$obErro->ocorreu() ) {
        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php");
        $obTPessoalContrato = new TPessoalContrato;
        $stFiltro = " AND registro = ".$_GET['inContrato'];
        $obTPessoalContrato->recuperaCgmDoRegistro($rsCGM,$stFiltro);
    }
    if ( !$obErro->ocorreu() ) {
        $arContratos = Sessao::read('arContratos');

        $arContrato                             = array();
        $arContrato['inId']                     = count($arContratos);
        $arContrato['inContrato']               = $_GET['inContrato'];
        $arContrato['cod_contrato']             = $rsCGM->getCampo("cod_contrato");
        $arContrato['numcgm']                   = $rsCGM->getCampo("numcgm");
        $arContrato['nom_cgm']                  = $rsCGM->getCampo("nom_cgm");

        $arContratos[] = $arContrato;
        Sessao::write('arContratos', $arContratos);
        $stJs .= montaListaContratos($arContratos);
    } else {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    }

    return $stJs;
}

function excluirContrato()
{
    $arContratos = ( is_array(Sessao::read('arContratos')) ? Sessao::read('arContratos') : array());
    $arSessaoContratos = array();
    foreach ($arContratos as $arContrato) {
        if ($arContrato['inId'] != $_GET['inId']) {
            $inId = sizeof($arSessaoContratos);
            $arEvento['inId'] = $inId;
            $arSessaoContratos[] = $arContrato;
        }
    }
    Sessao::write('arContratos', $arSessaoContratos);
    $stJs .= montaListaContratos($arSessaoContratos);

    return $stJs;
}

function montaListaContratos($arContratos)
{
    $rsContratos = new Recordset;
    $rsContratos->preenche($arContratos);

    $obLista = new Lista;
    $obLista->setTitulo("Lista de Matrículas");
    $obLista->setRecordSet( $rsContratos );
    $obLista->setMostraPaginacao( false );

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Matrícula");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("CGM");
    $obLista->ultimoCabecalho->setWidth( 40 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 2 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("DIREITA");
    $obLista->ultimoDado->setCampo( "[inContrato]" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("ESQUERDA");
    $obLista->ultimoDado->setCampo( "[numcgm]-[nom_cgm]" );
    $obLista->commitDado();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "EXCLUIR" );
    $obLista->ultimaAcao->setFuncaoAjax( true );
    $obLista->ultimaAcao->setLink( "JavaScript:executaFuncaoAjax('excluirContrato');");
    $obLista->ultimaAcao->addCampo("1","inId");
    $obLista->commitAcao();

    $obLista->montaHTML();
    $stHtml = $obLista->getHTML();
    $stHtml = str_replace("\n","",$stHtml);
    $stHtml = str_replace("  ","",$stHtml);
    $stHtml = str_replace("'","\\'",$stHtml);

    $stJs .= "d.getElementById('spnContratos').innerHTML = '".$stHtml."';   \n";

    return $stJs;
}

function gerarSpanCompetencia13()
{
    if ($_GET["inCodMes"] == 12) {
        $obCkbCompetencia13 = new CheckBox();
        $obCkbCompetencia13->setRotulo("Somente Informações da Competência 13");
        $obCkbCompetencia13->setName("boCompetencia13");
        $obCkbCompetencia13->setTitle("Marque para emitir o relatório de conferência da sefip para competência 13.");
        $obCkbCompetencia13->setNull(false);

        $obFormulario = new Formulario();
        $obFormulario->addComponente($obCkbCompetencia13);
        $obFormulario->montaInnerHTML();
        $stHtml = $obFormulario->getHTML();
    }
    $stJs .= "d.getElementById('spnCompetencia13').innerHTML = '".$stHtml."';   \n";

    return $stJs;
}

function submeter()
{
    $obErro = new Erro();
    if ( ($_GET["stTipoFiltro"] == "contrato" or $_GET["stTipoFiltro"] == "cgm_contrato") and count(Sessao::read("arContratos")) == 0 ) {
        $obErro->setDescricao("@A lista de contratos deve ter pelo menos uma matrícula.");
    }
    if ( $obErro->ocorreu() ) {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    } else {
        $stJs .= "parent.frames[2].Salvar();\n";
    }

    return $stJs;
}

switch ($_GET['stCtrl']) {
    case "gerarSpan":
        $stJs .= gerarSpan();
    break;
    case "processarFiltro":
        $stJs .= processarFiltro();
    break;
    case "incluirContrato":
        $stJs .= incluirContrato();
    break;
    case "excluirContrato":
        $stJs .= excluirContrato();
    break;
    case "limparFiltro":
        $stJs .= limparFiltro();
    break;
    case "gerarSpanAtributosDinamicos":
        $stJs .= gerarSpanAtributosDinamicos();
    break;
    case "gerarSpanCompetencia13":
        $stJs .= gerarSpanCompetencia13();
    break;
    case "submeter":
        $stJs .= submeter();
    break;
}

if ($stJs) {
   echo $stJs;
}

?>
