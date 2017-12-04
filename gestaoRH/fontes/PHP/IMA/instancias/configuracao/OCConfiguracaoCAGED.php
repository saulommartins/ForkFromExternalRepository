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
    * Arquivo de Oculto para configuração da exportação do CAGED
    * Data de Criação: 18/04/2008

    * @author Diego Lemos de Souza

    * Casos de uso: uc-04.08.20

    $Id: OCConfiguracaoCAGED.php 66444 2016-08-29 19:13:17Z evandro $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "ConfiguracaoCAGED";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

function gerarSpanInformarResponsavel()
{
    $stHtml = '';
    if ($_REQUEST["boInformarResponsavel"]) {
        include_once(CAM_GA_CGM_COMPONENTES."IPopUpCGM.class.php");
        $obForm = Sessao::read("obForm");
        $obIPopUpCGM = new IPopUpCGM($obForm);
        $obIPopUpCGM->setTipo('juridica');
        $obIPopUpCGM->obCampoCod->setValue($stCgm);
        $obIPopUpCGM->setRotulo("CGM do Autorizado");
        $obIPopUpCGM->setTitle("Informe o CGM do responsável autorizado pelas informações da entidade.");

        $obChkInformarCEI = new Checkbox();
        $obChkInformarCEI->setRotulo("Informar CEI");
        $obChkInformarCEI->setName("boInformarCEIAutorizado");
        $obChkInformarCEI->setId("boInformarCEIAutorizado");
        $obChkInformarCEI->setTitle("Marque para utilizar o número do CEI ao invés de CNPJ do Autorizado.");
        $obChkInformarCEI->obEvento->setOnChange("montaParametrosGET('gerarSpanInformarCEIAutorizado','boInformarCEIAutorizado');");
        $obChkInformarCEI->setValue("true");

        $obSpnInformarCEI = new Span();
        $obSpnInformarCEI->setId("spnInformarCEIAutorizado");

        $obHdnInformarCEI = new HiddenEval();
        $obHdnInformarCEI->setName("hdnInformarCEIAutorizado");

        $obTxtNumeroAutorizacao = new TextBox();
        $obTxtNumeroAutorizacao->setRotulo("Número da Autorização");
        $obTxtNumeroAutorizacao->setName("inNumeroAutorizacao");
        $obTxtNumeroAutorizacao->setInteiro(true);
        $obTxtNumeroAutorizacao->setTitle("Informe o número da autorização fornecido pelo MTE.");
        $obTxtNumeroAutorizacao->setSize(10);
        $obTxtNumeroAutorizacao->setMaxlength(7);

        $obFormulario = new Formulario();
        $obFormulario->addComponente($obIPopUpCGM);
        $obFormulario->addComponente($obChkInformarCEI);
        $obFormulario->addSpan($obSpnInformarCEI);
        $obFormulario->addHidden($obHdnInformarCEI,true);
        $obFormulario->addComponente($obTxtNumeroAutorizacao);
        $obFormulario->montaInnerHTML();
        $obFormulario->obJavaScript->montaJavaScript();
        $stHtml = $obFormulario->getHTML();
        $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
    }
    $stJs  = "d.getElementById('spnInformarResponsavel').innerHTML = '".$stHtml."';";
    $stJs .= "f.hdnInformarResponsavel.value = '".$stEval."';";

    return $stJs;
}

function gerarSpanInformarCEI()
{
    $stHtml = '';
    if ($_REQUEST["boInformarCEI"]) {
        $obTxtNumeroCEI = new TextBox();
        $obTxtNumeroCEI->setRotulo("Número do CEI");
        $obTxtNumeroCEI->setName("inNumeroCEI");
        $obTxtNumeroCEI->setInteiro(true);
        $obTxtNumeroCEI->setTitle("Informe o número do CEI.");
        $obTxtNumeroCEI->setNull(false);

        $obFormulario = new Formulario();
        $obFormulario->addComponente($obTxtNumeroCEI);
        $obFormulario->montaInnerHTML();
        $obFormulario->obJavaScript->montaJavaScript();
        $stHtml = $obFormulario->getHTML();
        $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
    }
    $stJs = "d.getElementById('spnInformarCEI').innerHTML = '".$stHtml."';";
    $stJs .= "f.hdnInformarCEI.value = '".$stEval."';";

    return $stJs;
}

function gerarSpanInformarCEIAutorizado()
{
    $stHtml = '';
    if ($_REQUEST["boInformarCEIAutorizado"]) {
        $obTxtNumeroCEI = new TextBox();
        $obTxtNumeroCEI->setRotulo("Número do CEI");
        $obTxtNumeroCEI->setName("inNumeroCEIAutorizacao");
        $obTxtNumeroCEI->setInteiro(true);
        $obTxtNumeroCEI->setTitle("Informe o número do CEI.");
        $obTxtNumeroCEI->setSize(10);
        $obTxtNumeroCEI->setNull(false);

        $obFormulario = new Formulario();
        $obFormulario->addComponente($obTxtNumeroCEI);
        $obFormulario->montaInnerHTML();
        $obFormulario->obJavaScript->montaJavaScript();
        $stHtml = $obFormulario->getHTML();
        $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
    }
    $stJs = "d.getElementById('spnInformarCEIAutorizado').innerHTML = '".$stHtml."';";
    $stJs .= "f.hdnInformarCEIAutorizado.value = '".$stEval."';";

    return $stJs;
}

function buscaCnae()
{
    $rsCnae = new RecordSet();
    if ($_REQUEST["inCodCnae"]) {
        include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMCnaeFiscal.class.php"         );
        $obTCEMCnaeFiscal = new TCEMCnaeFiscal();
        $stFiltro = " WHERE cod_estrutural = '".$_REQUEST["inCodCnae"]."'";
        $obTCEMCnaeFiscal->recuperaTodos($rsCnae,$stFiltro);
    }
    if ( $rsCnae->getNumLinhas() == -1 ) {
        $stJs .= 'f.inCodCnae.value = "";';
        $stJs .= 'f.inCodCnae.focus();';
        $stJs .= "f.HdninCodCnae.value = '';";
        $stJs .= 'd.getElementById("stCnae").innerHTML = "&nbsp;";';
        $stJs .= "alertaAviso('campo Código CNAE Fiscal inválido!(".$_REQUEST['inCodCnae'].").','form','erro','".Sessao::getId()."');\n";
    } else {
        $stCnae        = $rsCnae->getCampo ("nom_atividade");
        $inCodigoCnae  = $rsCnae->getCampo ("cod_cnae");
        $stJs .= "f.HdninCodCnae.value = '$inCodigoCnae';";
        $stJs .= "d.getElementById('stCnae').innerHTML = '$stCnae'";
    }

    return $stJs;
}

function preencherDadosCaged()
{
    $stJs  = montaJavaScriptComboEventos();
    $stJs .= montaJavaScriptComboSubDivisao();
    $stJs .= montaJavaScriptSpanInformarResponsavel();
    $stJs .= montaJavaScriptSpanInformarEstabelecimento();

    return $stJs;
}

function montaJavaScriptSpanInformarEstabelecimento()
{
    include_once(CAM_GRH_IMA_MAPEAMENTO."TIMACagedEstabelecimento.class.php");
    $obTIMACagedEstabelecimento = new TIMACagedEstabelecimento();
    $obTIMACagedEstabelecimento->recuperaTodos($rsEstabelecimento);
    if ($rsEstabelecimento->getNumLinhas() == 1) {
        $stJs .= "d.getElementById('boInformarCEI').checked = true;\n";
        $_REQUEST["boInformarCEI"] = true;
        $stJs .= gerarSpanInformarCEI();
        $stJs .= "f.inNumeroCEI.value = ".$rsEstabelecimento->getCampo("num_cei").";\n";
    }

    return $stJs;
}

function montaJavaScriptSpanInformarResponsavel()
{
    include_once(CAM_GRH_IMA_MAPEAMENTO."TIMACagedAutorizadoCgm.class.php");
    $obTIMACagedAutorizadoCgm = new TIMACagedAutorizadoCgm();
    $obTIMACagedAutorizadoCgm->recuperaRelacionamento($rsAutorizadoCgm);
    if ($rsAutorizadoCgm->getNumLinhas() == 1) {
        $stJs  = "d.getElementById('boInformarResponsavel').checked = true;\n";
        $_REQUEST["boInformarResponsavel"] = true;
        $stJs .= gerarSpanInformarResponsavel();
        $stJs .= "f.inCGM.value = ".$rsAutorizadoCgm->getCampo("numcgm").";\n";
        $stJs .= "d.getElementById('stNomCGM').innerHTML = '".$rsAutorizadoCgm->getCampo("nom_cgm")."';\n";
        $stJs .= "f.inNumeroAutorizacao.value = '".trim($rsAutorizadoCgm->getCampo("num_autorizacao"))."';\n";
        include_once(CAM_GRH_IMA_MAPEAMENTO."TIMACagedAutorizadoCei.class.php");
        $obTIMACagedAutorizadoCei = new TIMACagedAutorizadoCei();
        $obTIMACagedAutorizadoCei->recuperaTodos($rsAutorizadoCei);
        if ($rsAutorizadoCei->getNumLinhas() == 1) {
            $stJs .= "d.getElementById('boInformarCEIAutorizado').checked = true;\n";
            $_REQUEST["boInformarCEIAutorizado"] = true;
            $stJs .= gerarSpanInformarCEIAutorizado();
            $stJs .= "f.inNumeroCEIAutorizacao.value = ".$rsAutorizadoCei->getCampo("num_cei").";\n";
        }
    }

    return $stJs;
}

function montaJavaScriptComboSubDivisao()
{
    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalRegime.class.php");
    $obTPessoalRegime = new TPessoalRegime();
    $stFiltro = " WHERE cod_regime = 1";
    $obTPessoalRegime->recuperaTodos($rsRegimes,$stFiltro);
    $stJs .= "limpaSelect(f.inCodRegimeDisponiveis,0);\n";
    $stJs .= "limpaSelect(f.inCodRegimeSelecionados,0);\n";
    $stJs .= "limpaSelect(f.inCodSubDivisaoDisponiveis,0);\n";
    $stJs .= "limpaSelect(f.inCodSubDivisaoSelecionados,0);\n";
    $stJs .= "f.inCodRegimeDisponiveis[0] = new Option('".trim($rsRegimes->getCampo("descricao"))."','".$rsRegimes->getCampo("cod_regime")."','');\n";

    include_once(CAM_GRH_IMA_MAPEAMENTO."TIMACagedSubDivisao.class.php");
    $obTIMACagedSubDivisao = new TIMACagedSubDivisao();
    $obTIMACagedSubDivisao->recuperaRelacionamento($rsSubDivisaoGravadas);
    if ($rsSubDivisaoGravadas->getNumLinhas() > 0) {
        $stJs .= "limpaSelect(f.inCodRegimeSelecionados,0);\n";
        $stJs .= "limpaSelect(f.inCodRegimeDisponiveis,0);\n";

        $arRegimes = array();
        $arSubDivisao = array();
        while (!$rsSubDivisaoGravadas->eof()) {
            $arRegimes[] = $rsSubDivisaoGravadas->getCampo("cod_regime");
            $arSubDivisao[] = $rsSubDivisaoGravadas->getCampo("cod_sub_divisao");
            $rsSubDivisaoGravadas->proximo();
        }
        $stCodRegimes = implode(",",array_unique($arRegimes));
        $stCodSubDivisoes = implode(",",array_unique($arSubDivisao));

        if (count($arRegimes)) {
            $stJs .= "f.inCodRegimeSelecionados[0] = new Option('".trim($rsRegimes->getCampo("descricao"))."','".$rsRegimes->getCampo("cod_regime")."','');\n";
        } else {
            $stJs .= "f.inCodRegimeDisponiveis[0] = new Option('".trim($rsRegimes->getCampo("descricao"))."','".$rsRegimes->getCampo("cod_regime")."','');\n";
        }

        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalSubDivisao.class.php");
        $obTPessoalSubDivisao = new TPessoalSubDivisao();
        $stFiltro  = " WHERE cod_sub_divisao IN (".$stCodSubDivisoes.")";
        $stFiltro .= "   AND cod_regime IN (".$stCodRegimes.")";
        $obTPessoalSubDivisao->recuperaTodos($rsSubDivisao,$stFiltro);        
        $inIndex = 0;
        $stJs .= "limpaSelect(f.inCodSubDivisaoSelecionados,0);\n";
        while (!$rsSubDivisao->eof()) {
            $stJs .= "f.inCodSubDivisaoSelecionados[".$inIndex."] = new Option('".trim($rsSubDivisao->getCampo("descricao"))."','".$rsSubDivisao->getCampo("cod_sub_divisao")."','');\n";
            $inIndex++;
            $rsSubDivisao->proximo();
        }
        $stFiltro = " WHERE cod_sub_divisao NOT IN (".$stCodSubDivisoes.")";
        $stFiltro .= "   AND cod_regime IN (".$stCodRegimes.")";
        $obTPessoalSubDivisao->recuperaTodos($rsSubDivisao,$stFiltro);
        $inIndex = 0;
        while (!$rsSubDivisao->eof()) {
            $stJs .= "f.inCodSubDivisaoDisponiveis[".$inIndex."] = new Option('".trim($rsSubDivisao->getCampo("descricao"))."','".$rsSubDivisao->getCampo("cod_sub_divisao")."','');\n";
            $inIndex++;
            $rsSubDivisao->proximo();
        }
    }

    return $stJs;
}

function montaJavaScriptComboEventos()
{
    include_once(CAM_GRH_IMA_MAPEAMENTO."TIMACagedEvento.class.php");
    $obTIMACagedEvento = new TIMACagedEvento();
    $obTIMACagedEvento->recuperaRelacionamento($rsEventosGravados);
    $stJs .= "limpaSelect(f.inCodEventoSelecionados,0);\n";
    $stJs .= "limpaSelect(f.inCodEventoDisponiveis,0);\n";

    $inIndex = 0;
    $stCodEventos = "";
    while (!$rsEventosGravados->eof()) {
        $stJs .= "f.inCodEventoSelecionados[".$inIndex."] = new Option('".$rsEventosGravados->getCampo("codigo")."-".trim($rsEventosGravados->getCampo("descricao"))."','".$rsEventosGravados->getCampo("cod_evento")."','');\n";
        $stCodEventos .= $rsEventosGravados->getCampo("cod_evento").",";
        $inIndex++;
        $rsEventosGravados->proximo();
    }
    $stCodEventos = substr($stCodEventos,0,strlen($stCodEventos)-1);

    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEvento.class.php");
    $obTFolhaPagamentoEvento = new TFolhaPagamentoEvento();
    $stFiltro  = " WHERE (natureza = 'P' OR natureza = 'D' OR natureza = 'B')";
    if ($stCodEventos!="") {
        $stFiltro .= "   AND cod_evento NOT IN (".$stCodEventos.")";
    }
    $obTFolhaPagamentoEvento->recuperaTodos($rsEventos,$stFiltro," descricao");
    $inIndex = 0;
    while (!$rsEventos->eof()) {
        $stJs .= "f.inCodEventoDisponiveis[".$inIndex."] = new Option('".$rsEventos->getCampo("codigo")."-".trim($rsEventos->getCampo("descricao"))."','".$rsEventos->getCampo("cod_evento")."','');\n";
        $stCodEventos .= $rsEventos->getCampo("cod_evento").",";
        $inIndex++;
        $rsEventos->proximo();
    }

    return $stJs;
}

function submeter()
{
    $obErro = new Erro();

    if(is_array($_REQUEST['inCodEventoSelecionados']) &&
       sizeof($_REQUEST['inCodEventoSelecionados']) ){

        $stFiltro = implode(",",$_REQUEST['inCodEventoSelecionados']);
        $stFiltro = " WHERE cod_evento IN ($stFiltro) AND (natureza = 'P' OR natureza = 'B') ORDER BY codigo, natureza ";

        $rsEventos = new RecordSet();

        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEvento.class.php");
        $obTFolhaPagamentoEvento = new TFolhaPagamentoEvento();
        $obTFolhaPagamentoEvento->recuperaTodos($rsEventos,$stFiltro);

        if ($rsEventos->getNumLinhas() > 0) {
            $inIndice = 0;
            while (!$rsEventos->eof()) {
                if ($inIndice == 0) {
                    $stNaturezaEventoReferencia = $rsEventos->getCampo('natureza');
                    $inIndice = 1;
                } elseif ($stNaturezaEventoReferencia != $rsEventos->getCampo('natureza')) {
                    $obErro->setDescricao($obErro->getDescricao()."@A natureza dos eventos selecionados deve ser uniforme de Proventos ou de Bases. (Codigo Evento - ".$rsEventos->getCampo('codigo')."; Descrição - ".$rsEventos->getCampo('descricao')."; Natureza - ".($rsEventos->getCampo('natureza') == 'P'?"Provento":"Base").")!");
                    break;
                }
                $rsEventos->proximo();
            }
        }
    }

    if ( $obErro->ocorreu() ) {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','aviso','".Sessao::getId()."');";
    } else {        
        $stJs .= " BloqueiaFrames(true,false); parent.frames[2].Salvar(); \n";
    }

    return $stJs;
}

switch ($_REQUEST['stCtrl']) {
    case "gerarSpanInformarResponsavel":
        $stJs = gerarSpanInformarResponsavel();
        break;
    case "gerarSpanInformarCEI";
        $stJs = gerarSpanInformarCEI();
        break;
    case "gerarSpanInformarCEIAutorizado":
        $stJs = gerarSpanInformarCEIAutorizado();
        break;
    case "buscaCnae":
        $stJs = buscaCnae();
        break;
    case "preencherDadosCaged":
        $stJs = preencherDadosCaged();
        break;
    case "submeter":
        $stJs = submeter();
        break;
}
if ($stJs) {
    echo $stJs;
}

?>
