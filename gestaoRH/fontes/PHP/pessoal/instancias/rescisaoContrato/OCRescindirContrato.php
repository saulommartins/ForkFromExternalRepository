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
* Página de Oculto - Pessoal - Rescindir Contrato
* Data de Criação   : 12/10/2005

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Eduardo Antunez

* @ignore

$Id: OCRescindirContrato.php 66538 2016-09-13 20:01:05Z carlos.silva $

* Casos de uso: uc-04.04.44
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GRH_PES_NEGOCIO."RPessoalServidor.class.php";
include_once CAM_GA_CGM_NEGOCIO. "RCGMPessoaFisica.class.php";
include_once CAM_GRH_PES_NEGOCIO."RPessoalRescisaoContrato.class.php";
include_once CAM_GRH_PES_NEGOCIO."RConfiguracaoPessoal.class.php";
include_once CAM_GRH_PES_COMPONENTES."IContratoDigitoVerificador.class.php";
include_once CAM_GRH_PES_COMPONENTES."IFiltroCGMContrato.class.php";
include_once CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPadrao.class.php";

$stAcao = $request->get("stAcao");

$arLink = Sessao::read('link');
$stPrograma = "RescindirContrato";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php"."?".Sessao::getId()."&stAcao=".$stAcao."&pg=".$arLink["pg"]."&pos=".$arLink["pos"];
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stCtrl = $request->get('stCtrl');

$obRFolhaPagamentoPadrao = new RFolhaPagamentoPadrao;

function atualizaSpanFiltro()
{
    $obFormulario = new Formulario;

    if ($_GET['stAcao'] == 'incluir') {
        $boRescindido = false;
    } elseif ($_GET['stAcao'] == 'excluir') {
        $boRescindido = true;
    }

    if ($_GET["rdoOpcao"] == 1) {
        $obIFiltroCGMContrato = new IFiltroCGMContrato($boRescindido);

        if ($boRescindido == false) {
            $obIFiltroCGMContrato->setTipoContrato('rescindido');
        } else {
            $obIFiltroCGMContrato->setTipoContrato('vigente');
        }

        $obIFiltroCGMContrato->obBscCGM->obCampoCod->setId('inNumCGM');
        $obIFiltroCGMContrato->geraFormulario($obFormulario);

    } elseif ($_GET["rdoOpcao"] == 2) {

        $obIContratoDigitoVerificador = new IContratoDigitoVerificador("",$boRescindido);
        $obIContratoDigitoVerificador->setPagFiltro(true);
        $obIContratoDigitoVerificador->obTxtRegistroContrato->setNull(false);
        $obIContratoDigitoVerificador->obTxtRegistroContrato->obEvento->setOnChange("");
        $obIContratoDigitoVerificador->setTipo('rescindir_contrato');
        $obIContratoDigitoVerificador->geraFormulario($obFormulario);
    }
    
    $obFormulario->montaInnerHTML();
    $obFormulario->obJavaScript->montaJavascript();
    $stTmp = $obFormulario->obJavaScript->getInnerJavascript();
    $stTmp = str_replace("\n","",$stTmp);
    $stJs  .= "f.stEval.value = '".trim($stTmp)."'; \n";
    $stJs  .= "d.getElementById('spnOpcao').innerHTML = '".$obFormulario->getHTML()."';\n";

    if ($_GET["rdoOpcao"] == 1) {
        $stJs  .= "d.getElementById('inNumCGM').focus(); \n";
    } else {
        $stJs  .= "d.getElementById('inContrato').focus(); \n";
    }

    return $stJs;
}

function buscaCasoCausa()
{
    if ($_GET['inCausaRescisao'] != "" and $_GET['dtRescisao'] != "") {
        $obRConfiguracaoPessoal = new RConfiguracaoPessoal;
        $obRConfiguracaoPessoal->consultar();
        $inGrupoPeriodo = $obRConfiguracaoPessoal->getGrupoPeriodo();
        $dtInicial = $_GET[$obRConfiguracaoPessoal->getContagemInicial()];
        $arInicial = explode("/",$dtInicial);
        $dtInicial = $arInicial[2]."/".$arInicial[1]."/".$arInicial[0];
        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalCausaRescisao.class.php");
        $obTPessoalCausaRescisao = new TPessoalCausaRescisao();
        $stFiltro = " WHERE num_causa = ".$_GET['inCausaRescisao'];

        $obTPessoalCausaRescisao->recuperaTodos($rsCausaRescisao,$stFiltro);
        $obRPessoalRescisaoContrato = new RPessoalRescisaoContrato;

        $obRPessoalRescisaoContrato->setDtInicial($dtInicial);
        $obRPessoalRescisaoContrato->setDtRescisao($_GET['dtRescisao']);
        $obRPessoalRescisaoContrato->obRPessoalCausaRescisao->setCodCausaRescisao($rsCausaRescisao->getCampo("cod_causa_rescisao"));
        $obRPessoalRescisaoContrato->obRPessoalCausaRescisao->addPessoalCasoCausa();
        $obRPessoalRescisaoContrato->obRPessoalCausaRescisao->roUltimoPessoalCasoCausa->setCodPeriodo($inGrupoPeriodo);
        $obRPessoalRescisaoContrato->obRPessoalCausaRescisao->roUltimoPessoalCasoCausa->addPessoalSubDivisao();
        $obRPessoalRescisaoContrato->obRPessoalCausaRescisao->roUltimoPessoalCasoCausa->roUltimoPessoalSubDivisao->setCodSubDivisao($_GET['inCodSubDivisao']);
        $obRPessoalRescisaoContrato->consultarCasoCausa($rsCasoCausa);

        if ( $rsCasoCausa->getNumLinhas() > 0 ) {
            $stJs .= "d.getElementById('stCasoCausa').innerHTML = '".$rsCasoCausa->getCampo("descricao")."';\n";
            $stJs .= "f.inCasoCausa.value = '".$rsCasoCausa->getCampo("cod_caso_causa")."'; \n";
            $stJs .= gerarSpanAviso($rsCasoCausa->getCampo("paga_aviso_previo"));
        } else {
            $stJs .= "d.getElementById('stCasoCausa').innerHTML = '';\n";
            $stJs .= "f.inCasoCausa.value = ''; \n";
            $stJs .= "alertaAviso('@Não existe caso de causa que se enquadre ao contrato selecionado.','form','erro','".Sessao::getId()."');";
        }
    }

    return $stJs;
}

function gerarDataAviso()
{
    if ($_GET['stAvisoPrevio'] == "t") {
        $obDtAviso = new Data;
        $obDtAviso->setName  ( "dtAviso" );
        $obDtAviso->setValue ( $dtAviso );
        $obDtAviso->setRotulo( "Data Aviso" );
        $obDtAviso->setNull  ( false );
        $obDtAviso->setTitle ( 'Informe a data do início do aviso prévio trabalhado ou indenizado.' );
        $obDtAviso->obEvento->setOnChange("montaParametrosGET('validarDataAviso','dtAviso,dtPosse,dtNomeacao,dtAdmissao');");

        $obFormulario = new Formulario;
        $obFormulario->addComponente($obDtAviso);
        $obFormulario->montaInnerHTML();
        $obFormulario->obJavaScript->montaJavaScript();
        $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
        $stEval = str_replace("\n","",$stEval);
        $stHtml = $obFormulario->getHTML();
    }
    $stJs .= "d.getElementById('spnDataAviso').innerHTML = '".$stHtml."';\n";
    $stJs .= "f.stEvalAviso.value = '$stEval';                       \n";

    return $stJs;
}

function gerarSpanAviso($boPagaAvisoPrevio)
{
    if ($boPagaAvisoPrevio == "t") {
        $_GET['stAvisoPrevio'] = "t";

        $obRdoTrabalhado = new Radio();
        $obRdoTrabalhado->setRotulo("Aviso Prévio");
        $obRdoTrabalhado->setTitle("Informe se o aviso prévio será trabalhado ou indenizado.");
        $obRdoTrabalhado->setName("stAvisoPrevio");
        $obRdoTrabalhado->setValue("t");
        $obRdoTrabalhado->setLabel("Trabalhado");
        $obRdoTrabalhado->setChecked(true);
        $obRdoTrabalhado->obEvento->setOnChange("montaParametrosGET('gerarDataAviso','stAvisoPrevio');");

        $obRdoIndenizado = new Radio();
        $obRdoIndenizado->setRotulo("Aviso Prévio");
        $obRdoIndenizado->setTitle("Informe se o aviso prévio será trabalhado ou indenizado.");
        $obRdoIndenizado->setName("stAvisoPrevio");
        $obRdoIndenizado->setLabel("Indenizado");
        $obRdoIndenizado->setValue("i");
        $obRdoIndenizado->obEvento->setOnChange("montaParametrosGET('gerarDataAviso','stAvisoPrevio');");

        $obSpnDataAviso = new Span();
        $obSpnDataAviso->setId("spnDataAviso");

        $obFormulario = new Formulario;
        $obFormulario->agrupaComponentes(array($obRdoTrabalhado,$obRdoIndenizado));
        $obFormulario->addSpan($obSpnDataAviso);
        $obFormulario->montaInnerHTML();
        $stHtml = $obFormulario->getHTML();
    }
    $stJs .= "d.getElementById('spnAviso').innerHTML = '".$stHtml."';\n";
    if ($stHtml != "") {
        $stJs .= gerarDataAviso();
    }

    return $stJs;
}

function gerarSpanObito()
{
    $stHtml = "";
    $stEval = "";

    if ($_GET['inCausaRescisao'] != "") {
        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalCausaRescisao.class.php");
        $obTPessoalCausaRescicao = new TPessoalCausaRescisao();
        $stFiltro = " AND num_causa = ".$_GET['inCausaRescisao'];
        $obTPessoalCausaRescicao->recuperaRelacionamento($rsCausaRescisao,$stFiltro);
        if ( trim($rsCausaRescisao->getCampo("num_sefip")) == "S2" or trim($rsCausaRescisao->getCampo("num_sefip")) == "S3" ) {
            $obTxtNroCertidaoObto = new TextBox;
            $obTxtNroCertidaoObto->setName     ( "stNroCertidaoObito"   );
            $obTxtNroCertidaoObto->setValue    ( $stNroCertidaoObito    );
            $obTxtNroCertidaoObto->setRotulo   ( "Número da Certidão de Óbito" );
            $obTxtNroCertidaoObto->setSize     ( 10 );
            $obTxtNroCertidaoObto->setMaxLength( 10 );
            $obTxtNroCertidaoObto->setNull     ( false );
            $obTxtNroCertidaoObto->setTitle    ( "Informe o númerop da certidão de óbito." );

            $obTxtDescCausaMortis = new TextArea();
            $obTxtDescCausaMortis->setName     ( "stDescCausaMortis"   );
            $obTxtDescCausaMortis->setValue    ( $stDescCausaMortis    );
            $obTxtDescCausaMortis->setRotulo   ( "Descrição da Causa Mortis" );
            $obTxtDescCausaMortis->setMaxCaracteres(200);
            $obTxtDescCausaMortis->setNull     ( false );
            $obTxtDescCausaMortis->setTitle    ( "Digite a descrição da causa mortis." );

            $obFormulario = new Formulario;
            $obFormulario->addComponente($obTxtNroCertidaoObto);
            $obFormulario->addComponente($obTxtDescCausaMortis);
            $obFormulario->montaInnerHTML();
            $obFormulario->obJavaScript->montaJavaScript();
            $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
            $stEval = str_replace("\n","",$stEval);
            $stHtml = $obFormulario->getHTML();
        }
    }
    $stJs .= "d.getElementById('spnObto').innerHTML = '".$stHtml."';\n";
    //$stJs .= "f.stEval.value = '$stEval';                           \n";
    $stJs .= buscaCasoCausa();

    return $stJs;
}

function validarDataRescisao()
{
    $obErro = new Erro();

    if (!$obErro->ocorreu()) {
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
        $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
        $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsCompetenciaAtual);
        $stFiltro = " AND FPM.cod_periodo_movimentacao = ".($rsCompetenciaAtual->getCampo("cod_periodo_movimentacao")-1);
        $obTFolhaPagamentoPeriodoMovimentacao->recuperaPeriodoMovimentacao($rsCompetenciaAnterior,$stFiltro);
        $arCompetenciaAtual    = explode("/",$rsCompetenciaAtual->getCampo("dt_final"));
        $arCompetenciaAnterior = explode("/",$rsCompetenciaAnterior->getCampo("dt_final"));
        $arRescisao = explode("/",$_GET['dtRescisao']);
        $dtCompetenciaAtual = $arCompetenciaAtual[2]."-".$arCompetenciaAtual[1];
        $dtCompetenciaAnterior = $arCompetenciaAnterior[2]."-".$arCompetenciaAnterior[1];
        $dtRescisao = $arRescisao[2]."-".$arRescisao[1];
        if ( !($dtRescisao >= $dtCompetenciaAnterior and $dtRescisao <= $dtCompetenciaAtual) ) {
            $obErro->setDescricao("Data da rescisão inválida. A mesma deverá estar compreendida entre a competência atual ou a competência anterior.");
        }
    }
    if (!$obErro->ocorreu()) {
        $obRConfiguracaoPessoal = new RConfiguracaoPessoal;
        $obRConfiguracaoPessoal->consultar();
        $dtInicial = $_GET[$obRConfiguracaoPessoal->getContagemInicial()];
        $arRescisao = explode("/",$_GET['dtRescisao']);
        $dtRescisao = $arRescisao[2]."/".$arRescisao[1]."/".$arRescisao[0];
        if ($dtRescisao < $dtInicial) {
            $obErro->setDescricao("Data da rescisão inválida. A mesma deve ser posterior a data da nomeação ou posse.");
        }
    }
    if (!$obErro->ocorreu()) {
        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalFerias.class.php");
        $obTPessoalFerias = new TPessoalFerias();
        $stFiltro  = " AND ferias.cod_contrato = ".$_GET['inCodContrato'];
        $stFiltro .= " AND mes_competencia     = '".$arCompetenciaAtual[1]."'";
        $stFiltro .= " AND ano_competencia     = '".$arCompetenciaAtual[2]."'";

        $obTPessoalFerias->recuperaRelacionamento($rsFerias,$stFiltro);

        if ( $rsFerias->getNumLinhas() == 1 ) {
            $arInicio  = explode("/",$rsFerias->getCampo("dt_inicio"));
            $arFim     = explode("/",$rsFerias->getCampo("dt_fim"));
            $dtInicio  = $arInicio[2]."-".$arInicio[1]."-".$arInicio[0];
            $dtFim     = $arFim[2]."-".$arFim[1]."-".$arFim[0];
            $arRescisao = explode("/",$_GET['dtRescisao']);
            $dtRescisao = $arRescisao[2]."-".$arRescisao[1]."-".$arRescisao[0];
            if ($dtRescisao >= $dtInicio and $dtRescisao <= $dtFim) {
                $obErro->setDescricao("Data de rescisão inválida. Está em colisão com o gozo de férias.");
            }
        }
    }
    if ( $obErro->ocorreu() ) {
        $stJs .= "f.dtRescisao.value = '';  \n";
        $stJs .= "alertaAviso('@".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');";
    } else {
        $stJs .= buscaCasoCausa();
    }

    return $stJs;
}

function submeter()
{
    $obErro = new Erro();
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoComplementarSituacao.class.php");
    $obTFolhaPagamentoComplementarSituacao = new TFolhaPagamentoComplementarSituacao();
    $obTFolhaPagamentoComplementarSituacao->recuperaUltimaFolhaComplementarSituacao($rsFolhaComplementar);
    if ( $rsFolhaComplementar->getCampo("situacao") == "a" ) {
        $obErro->setDescricao('@Existe folha complementar aberta. Esta folha deverá ser fechada ou cancelada!()');
    }
    if (isset($_GET["stDescCausaMortis"]) and trim($_GET["stDescCausaMortis"]) == "") {
        $obErro->setDescricao('@Campo Descrição da Causa Mortis inválido!()');
    }
    if ($obErro->ocorreu()) {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');";
    } else {
        $stJs .= "BloqueiaFrames(true,false); parent.frames[2].Salvar();\n";
    }

    return $stJs;
}

function validarIncorporarFolhaSalario()
{
    $obErro = new Erro();
    if ($_GET["boFolhaSalario"]) {
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
        $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
        $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsPeriodoMovimentacao);
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoCalculado.class.php");
        $obTFolhaPagamentoEventoCalculado = new TFolhaPagamentoEventoCalculado();
        $stFiltro  = " WHERE cod_periodo_movimentacao = ".$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao");
        $stFiltro .= "   AND cod_contrato = ".$_GET["inCodContrato"];
        $obTFolhaPagamentoEventoCalculado->recuperaContratosCalculados($rsContratosCalculados,$stFiltro);
        if ($rsContratosCalculados->getNumLinhas() == -1) {
            $obErro->setDescricao($obErro->getDescricao()."@Folha Salário deste contrato não calculada! Para que esses valores façam parte das médias da rescisão, calcule o salário antes de conceder a rescisão!()");
        }
    }
    if ($obErro->ocorreu()) {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');";
    }

    return $stJs;
}

function validarDataAviso()
{
    $obErro = new Erro();
    if (!$obErro->ocorreu()) {
        $obRConfiguracaoPessoal = new RConfiguracaoPessoal;
        $obRConfiguracaoPessoal->consultar();
        $dtInicial = $_GET[$obRConfiguracaoPessoal->getContagemInicial()];
        $arAviso = explode("/",$_GET['dtAviso']);
        $dtAviso = $arAviso[2]."/".$arAviso[1]."/".$arAviso[0];
        if ($dtAviso < $dtInicial) {
            $obErro->setDescricao("Data Aviso inválida. A mesma deve ser posterior a data da nomeação ou posse.");
        }
    }
    if (!$obErro->ocorreu()) {
        $inDia = date('w',mktime(0,0,0,$arAviso[1],$arAviso[0],$arAviso[2]));
        if ($inDia == 0 or $inDia == 6) {
            $obErro->setDescricao("Data Aviso inválida. A mesma deve ser diferente de sábado ou domingo.");
        }
    }
    if ( $obErro->ocorreu() ) {
        $stJs .= "f.dtAviso.value = '';  \n";
        $stJs .= "alertaAviso('@".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');";
    }

    return $stJs;
}

function MontaNorma($stSelecionado = "")
{
    global $obRFolhaPagamentoPadrao;

    $stCombo  = "inCodNorma";
    $stFiltro = "inCodTipoNorma";
    $stJs .= "limpaSelect(f.$stCombo,0); \n";
    $stJs .= "f.$stCombo.options[0] = new Option('Selecione','', 'selected');\n";
    $stJs .= "f.".$stCombo."Txt.value='$stSelecionado';\n";

    if ($_GET[ $stFiltro ] != "") {
        $inCodTipoNorma = $_GET[ $stFiltro ];
        $obRFolhaPagamentoPadrao->obRNorma->obRTipoNorma->setCodTipoNorma( $inCodTipoNorma );
        $obRFolhaPagamentoPadrao->obRNorma->listar( $rsCombo );
        $inCount = 0;
        while (!$rsCombo->eof()) {
            $inCount++;
            $inId   = $rsCombo->getCampo("cod_norma");
            $stDesc = $rsCombo->getCampo("nom_norma");
            if( $stSelecionado == $inId )
                $stSelected = 'selected';
            else
                $stSelected = '';
            $stJs .= "f.$stCombo.options[$inCount] = new Option('".$stDesc."','".$inId."','".$stSelected."'); \n";
            $rsCombo->proximo();
        }
    }

    return $stJs;

}

switch ($stCtrl) {
    case 'atualizaSpanFiltro':
        $stJs .= atualizaSpanFiltro();
        break;
    case 'buscaCasoCausa':
        $stJs .= buscaCasoCausa();
        break;
    case "MontaNorma":
        $stJs .= MontaNorma();
        break;
    case 'gerarSpanObito':
        $stJs .= gerarSpanObito();
        break;
    case 'validarDataRescisao':
        $stJs .= validarDataRescisao();
        break;
    case 'submeter':
        $stJs .= submeter();
        break;
    case 'validarDataAviso':
        $stJs .= validarDataAviso();
        break;
    case 'gerarDataAviso':
        $stJs .= gerarDataAviso();
        break;
    case 'validarIncorporarFolhaSalario':
        $stJs .= validarIncorporarFolhaSalario();
        break;
    case 'montaAtivarUsuario':
        $arAtivarUsuario = Sessao::read('arAtivarUsuario');
        $arAtivarUsuario = (is_array($arAtivarUsuario)) ? $arAtivarUsuario : array();

        $arId = explode('_', $request->get('stId'));
        $inRegistro = $arId[1];
        $inCgm = $arId[2];

        $arAtivarUsuario[$inRegistro.'.'.$inCgm] = $request->get('boAtivarUsuario');

        Sessao::write('arAtivarUsuario', $arAtivarUsuario);
    break;
}

if ($stJs) {
    echo $stJs;
}
?>
