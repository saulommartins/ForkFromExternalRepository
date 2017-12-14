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
    * Página de Oculto do Acidos Cedidos
    * Data de Criação: 27/09/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30860 $
    $Name$
    $Author: alex $
    $Date: 2007-12-14 14:57:43 -0200 (Sex, 14 Dez 2007) $

    * Casos de uso: uc-04.04.30
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GRH_PES_COMPONENTES."IContratoDigitoVerificador.class.php"                            );
include_once( CAM_GRH_PES_COMPONENTES."IFiltroCGMContrato.class.php"                                    );
include_once( CAM_GRH_PES_COMPONENTES."ISelectFuncao.class.php"                                         );
include_once( CAM_GRH_PES_COMPONENTES."IBuscaInnerLotacao.class.php"                                    );
include_once( CAM_GRH_PES_COMPONENTES."IBuscaInnerLocal.class.php"                                      );

//Define o nome dos arquivos PHP
$stPrograma = "ManterAdidoCedido";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

function preencherNorma()
{
    $stJs .= "limpaSelect(f.stNrNorma,0);                                \n";
    $stJs .= "f.stNrNormaTxt.value = '';                                 \n";
    $stJs .= "f.stNrNorma[0] = new Option('Selecione','','selected');    \n";
    if ($_GET['inCodTipoNormaTxt'] != "") {
        include_once( CAM_GA_NORMAS_MAPEAMENTO."TNorma.class.php" );
        $obTNorma = new TNorma();
        $stFiltro = " WHERE cod_tipo_norma = ".$_GET['inCodTipoNormaTxt'];
        $obTNorma->recuperaNormas($rsNorma, $stFiltro);
        $inCount = 1;
        while (!$rsNorma->eof()) {
            $stJs .= "f.stNrNorma.options[$inCount] = new Option('".$rsNorma->getCampo("nom_norma")."','".$rsNorma->getCampo("num_norma_exercicio")."',''); \n";
            $inCount++;
            $rsNorma->proximo();
        }
    }

    return $stJs;
}

function preencherPublicacao()
{
    $stHtml = "";
    if ($_REQUEST['stNrNormaTxt'] != "") {
        include_once( CAM_GA_NORMAS_MAPEAMENTO."TNorma.class.php" );
        $obTNorma = new TNorma();
        $arNorma = explode("/",$_REQUEST['stNrNormaTxt']);
        $inNumNorma = ltrim($arNorma[0], '0');
        $inExercicio= $arNorma[1];

        $stFiltro  = " WHERE cod_tipo_norma = ".$_GET['inCodTipoNormaTxt'];
        $stFiltro .= "   AND num_norma = '".$inNumNorma."'";
        $stFiltro .= "   AND exercicio = '".$inExercicio."'";
        $obTNorma->recuperaNormas($rsNorma, $stFiltro);
        $stHtml = $rsNorma->getCampo('dt_publicacao');
    }
    $stJs .= "d.getElementById('dtPublicacao').innerHTML = '".$stHtml."';\n";

    return $stJs;
}

function preencherSpanCedencia()
{
    include_once(CAM_GA_CGM_COMPONENTES."IPopUpCGM.class.php");
    $obForm = Sessao::read('obForm');
    $obIPopUpCGM = new IPopUpCGM($obForm);
    $obIPopUpCGM->setTipo("juridica");
    if ($_GET['stTipoCedencia'] == "cedido") {
        $obIPopUpCGM->setRotulo("CGM Orgão/Entidade Cessionário");
        $obIPopUpCGM->setTitle("Informe o CGM do Cessionário.");
    } else {
        $obIPopUpCGM->setRotulo("CGM Orgão/Entidade Cedente");
        $obIPopUpCGM->setTitle("Informe o CGM do cedente.");
    }
    $obFormulario = new Formulario;
    $obFormulario->addComponente($obIPopUpCGM);
    $obFormulario->montaInnerHTML();
    $stHtml = $obFormulario->getHTML();
    $stJs .= "d.getElementById('spnCedencia').innerHTML = '".$stHtml."';\n";

    return $stJs;
}

function limparForm()
{
    $stJs .= preencherSpanCedencia();

    return $stJs;
}

function limparFormAlterar()
{
    $stJs .= "f.inCodTipoNormaTxt.value = '';\n";
    $stJs .= "f.inCodTipoNorma.value = '';\n";
    $stJs .= "f.stNrNormaTxt.value = '';\n";
    $stJs .= "limpaSelect(f.stNrNorma,0);                                \n";
    $stJs .= "f.stNrNorma[0] = new Option('Selecione','','selected');    \n";
    $stJs .= "f.inCodLocal.value = '';\n";
    $stJs .= "f.dtDataInicialAto.value = '';\n";
    $stJs .= "f.dtDataFinalAto.value = '';\n";
    $stJs .= "f.inCodConvenioTxt.value = '';\n";
    $stJs .= "d.getElementById('stLocal').innerHTML = '';\n";
    $stJs .= "d.getElementById('dtPublicacao').innerHTML = '';\n";

    return $stJs;
}

function validaMatricula()
{
    if ($_GET['inContrato'] != "") {
        $obErro = new Erro();
        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidor.class.php");
        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php");
        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorCasoCausa.class.php");
        $obTPessoalContratoServidorCasoCausa = new TPessoalContratoServidorCasoCausa();
        $obTPessoalContratoServidor = new TPessoalContratoServidor();
        $obTPessoalContrato = new TPessoalContrato();
        $stFiltro = " WHERE registro = ".$_GET['inContrato'];
        $obTPessoalContrato->recuperaTodos($rsContrato,$stFiltro);
        $stFiltro = " WHERE cod_contrato = ".$rsContrato->getCampo("cod_contrato");
        $obTPessoalContratoServidor->recuperaTodos($rsContratoServidor,$stFiltro);
        $obTPessoalContratoServidorCasoCausa->recuperaTodos($rsContratoServidorRescisao,$stFiltro);

        if ( $rsContratoServidor->getCampo("ativo") == "f" or $rsContratoServidorRescisao->getNumLinhas() > 0 ) {
            $obErro->setDescricao("Matrícula inválida. Informe somente servidores em situação de ativo.");
            $stJs .= "f.inContrato.value = '';\n";
            $stJs .= "d.getElementById('inNomCGM').innerHTML = '';\n";
            $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');  \n";
        }
    }

    return $stJs;
}

function preencherSpanFiltro()
{
    include_once( CAM_GRH_PES_COMPONENTES."IFiltroContrato.class.php" );
    include_once( CAM_GRH_PES_COMPONENTES."IFiltroCGMContrato.class.php");
    $stHtml = "";
    $obFormulario = new Formulario;
    if ($_GET['stOpcao'] == 'cgm_contrato') {
        $obIFiltroCGMContrato = new IFiltroCGMContrato;
        $obIFiltroCGMContrato->obBscCGM->setNull(false);
        $obIFiltroCGMContrato->obCmbContrato->setNull(false);

        if ( $_REQUEST['stAcao'] != 'consultar' ) {
            Sessao::write('valida_ativos_cgm','true');
        }else{
            Sessao::write('valida_ativos_cgm','false');
        }

        $obIFiltroCGMContrato->geraFormulario($obFormulario);
    } else {
        $obIFiltroContrato = new IFiltroContrato();
        $obIFiltroContrato->obIContratoDigitoVerificador->setNull(false);
        if ( $_REQUEST['stAcao'] == 'consultar' ) {
            $obIFiltroContrato->setSituacao('todos');
            $obIFiltroContrato->obIContratoDigitoVerificador->setSituacao('todos');
        }else{
            $obIFiltroContrato->obIContratoDigitoVerificador->setTipo('contrato_ativos');
        }
        $obIFiltroContrato->geraFormulario($obFormulario);
    }
    $obFormulario->montaInnerHTML();
    $stJs .= "d.getElementById('spnFiltro').innerHTML = '".$obFormulario->getHTML()."';\n";
    $stJs .= "f.stEval.value = '".$obFormulario->getInnerJavaScript()."'; \n";

    return $stJs;
}

function preencherFormAlterar()
{
    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php");
    include_once(CAM_GA_ORGAN_MAPEAMENTO."TOrganogramaLocal.class.php");

    $obTPessoalContrato = new TPessoalContrato();
    $stFiltro = " AND cod_contrato = ".$_GET['inCodContrato'];
    $obTPessoalContrato->recuperaCgmDoRegistro($rsContrato,$stFiltro);

    if ($_GET['inCodLocal'] != "") {
        $obTOrganogramaLocal = new TOrganogramaLocal();
        $stFiltro = " WHERE cod_local = ".$_GET['inCodLocal'];
        $obTOrganogramaLocal->recuperaTodos($rsLocal,$stFiltro);
        $stJs .= "f.inCodLocal.value = '".$_GET['inCodLocal']."';\n";
        $stJs .= "d.getElementById('stLocal').innerHTML = '".$rsLocal->getCampo("descricao")."';\n";
    }

    $_GET['stNrNormaTxt'] = $_GET['inNumNormaTxt']."/".$_GET['inExercicio'];

    $stJs .= "d.getElementById('inNomCGM').innerHTML = '".$rsContrato->getCampo("numcgm")."-".$rsContrato->getCampo("nom_cgm")."';\n";
    $stJs .= "d.getElementById('inContrato').innerHTML = '".$rsContrato->getCampo("registro")."';\n";
    $stJs .= preencherNorma();
    $stJs .= preencherPublicacao();
    $stJs .= "f.stNrNormaTxt.value = '".$_GET['inNumNormaTxt']."/".$_GET['inExercicio']."';\n";
    $stJs .= "f.stNrNorma.value = '".$_GET['inNumNormaTxt']."/".$_GET['inExercicio']."';\n";

    return $stJs;
}

function comparaDatas()
{
    ;
    $dtDataFinalAto   = $_GET['dtDataFinalAto'];
    $dtDataInicialAto = $_GET['dtDataInicialAto'];
    $stJs = "";

    if ( $dtDataFinalAto != "" and sistemaLegado::comparaDatas($dtDataInicialAto,$dtDataFinalAto) ) {
        $stMensagem = " Data Final do Ato (".$dtDataFinalAto.") não pode ser anterior à Data Inicial do Ato  (".$dtDataInicialAto.") !";
        $stJs .= "f.dtDataFinalAto.value = '';\n";
        $stJs .= "alertaAviso('$stMensagem','form','erro','".Sessao::getId()."');       \n";
    }

    return $stJs;
}

function preencherFormConsultar()
{
    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php");
    include_once(CAM_GA_ORGAN_MAPEAMENTO."TOrganogramaLocal.class.php");

    $obTPessoalContrato = new TPessoalContrato();
    $stFiltro = " AND cod_contrato = ".$_GET['inCodContrato'];
    $obTPessoalContrato->recuperaCgmDoRegistro($rsContrato,$stFiltro);

    if ($_GET['inCodLocal'] != "") {
        $obTOrganogramaLocal = new TOrganogramaLocal();
        $stFiltro = " WHERE cod_local = ".$_GET['inCodLocal'];
        $obTOrganogramaLocal->recuperaTodos($rsLocal,$stFiltro);
        $stJs .= "d.getElementById('stLocal').innerHTML = '".$rsLocal->getCampo("cod_local")."-".$rsLocal->getCampo("descricao")."';\n";
    }

    include_once( CAM_GA_NORMAS_MAPEAMENTO."TNorma.class.php" );
    $obTNorma = new TNorma();
    $stFiltro = " WHERE cod_norma = ".$_GET['inCodNorma'];
    $obTNorma->recuperaNormas($rsNorma, $stFiltro);

    include_once( CAM_GA_NORMAS_MAPEAMENTO."TTipoNorma.class.php" );
    $obTTipoNorma = new TTipoNorma();
    $stFiltro = " WHERE cod_tipo_norma = ".$rsNorma->getCampo("cod_tipo_norma");
    $obTTipoNorma->recuperaTodos($rsTipoNorma, $stFiltro);

    $stJs .= "d.getElementById('inNomCGM').innerHTML = '".$rsContrato->getCampo("numcgm")."-".$rsContrato->getCampo("nom_cgm")."';\n";
    $stJs .= "d.getElementById('inContrato').innerHTML = '".$rsContrato->getCampo("registro")."';\n";
    $stJs .= preencherPublicacao();
    $stJs .= "d.getElementById('stNrNorma').innerHTML = '".$rsNorma->getCampo("num_norma")."/".$rsNorma->getCampo("exercicio")."-".$rsNorma->getCampo("nom_norma")."';\n";
    $stJs .= "d.getElementById('inCodTipoNorma').innerHTML = '".$rsTipoNorma->getCampo("cod_tipo_norma")."-".$rsTipoNorma->getCampo("nom_tipo_norma")."';\n";
    $stJs .= "d.getElementById('dtPublicacao').innerHTML = '".$rsNorma->getCampo("dt_publicacao")."';\n";

    return $stJs;
}

function submeter()
{
    $obErro = new Erro();

    if ($_REQUEST["inCGM"] == "" && $_REQUEST["stAcao"] == "incluir") {
        if($_REQUEST["stTipoCedencia"] == 'adido')
            $msg = "Campo CGM Orgão/Entidade Cedente inválido";
        else
            $msg = "Campo CGM Orgão/Entidade Cessionário inválido";

        $obErro->setDescricao($obErro->getDescricao()."@$msg!()");
    }

    if ( $obErro->ocorreu() ) {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','aviso','".Sessao::getId()."');";
    } else {
        $stJs .= "parent.frames[2].Salvar();\n";
    }

    return $stJs;
}

switch ($_GET['stCtrl']) {
    case "preencherNorma":
        $stJs .= preencherNorma();
    break;
    case "preencherPublicacao":
        $stJs .= preencherPublicacao();
    break;
    case "preencherSpanCedencia":
        $stJs .= preencherSpanCedencia();
    break;
    case "limparForm":
        $stJs .= limparForm();
    break;
    case "limparFiltro":
        $stJs .= preencherSpanFiltro();
    break;
    case "limparFormAlterar":
        $stJs .= limparFormAlterar();
    break;
    case "validaMatricula":
        $stJs .= validaMatricula();
    break;
    case "preencherSpanFiltro":
        $stJs .= preencherSpanFiltro();
    break;
    case "preencherFormAlterar":
        $stJs .= preencherFormAlterar();
    break;
    case "preencherFormConsultar":
        $stJs .= preencherFormConsultar();
    break;
    case "comparaDatas":
        $stJs .= comparaDatas();
    break;
    case "submeter":
        $stJs .= submeter();
    break;
}

if ($stJs) {
   echo $stJs;
}
?>
