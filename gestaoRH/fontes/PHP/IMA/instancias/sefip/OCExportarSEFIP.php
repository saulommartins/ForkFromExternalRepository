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
    * Página de Oculto do Exportar Arquivo Sefip
    * Data de Criação: 12/01/2007

    * @author Desenvolvedor: Diego Lemos de Souza

    * Casos de uso: uc-04.08.03

    * $Id: OCExportarSEFIP.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GRH_PES_COMPONENTES."IFiltroContrato.class.php"                                       );
include_once( CAM_GRH_PES_COMPONENTES."IFiltroCGMContrato.class.php"                                    );
include_once( CAM_GRH_PES_COMPONENTES."ISelectMultiploRegSubCarEsp.class.php"                           );
include_once( CAM_GRH_PES_COMPONENTES."ISelectMultiploLotacao.class.php"                                );
include_once( CAM_GRH_PES_COMPONENTES."ISelectMultiploLocal.class.php"                                  );

//Define o nome dos arquivos PHP
$stPrograma = "ExportarSEFIP";
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
    $obIFiltroContrato->obIContratoDigitoVerificador->setTipo("geral");
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

    $obIFiltroCGMContrato = new IFiltroCGMContrato();
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
    $stJs .= processarCompetencia();
    $stJs .= gerarSpanArrecadouFGTS();
    $stJs .= preencheConfiguracoesSEFIP();
    $stJs .= "document.frm.stTipoFiltro.value = 'geral'; \n";

    return $stJs;
}

function limparFiltro()
{
    $stJs .= "document.frm.reset();\n";
    $stJs .= processarFiltro();

    return $stJs;
}

function incluirContrato()
{
    $obErro    = new erro;
    if ( !$obErro->ocorreu() ) {
        $arContratos = ( is_array(Sessao::read('arContratos2')) ) ? Sessao::read('arContratos2') : array();
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
        $arContratos                            = Sessao::read('arContratos2');

        $arContrato                             = array();
        $arContrato['inId']                     = count(Sessao::read('arContratos2'));
        $arContrato['inContrato']               = $_GET['inContrato'];
        $arContrato['cod_contrato']             = $rsCGM->getCampo("cod_contrato");
        $arContrato['numcgm']                   = $rsCGM->getCampo("numcgm");
        $arContrato['nom_cgm']                  = $rsCGM->getCampo("nom_cgm");

        $arContratos[]                          = $arContrato;
        Sessao::write('arContratos2', $arContratos);

        $stJs .= montaListaContratos($arContratos);
    } else {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    }

    return $stJs;
}

function excluirContrato()
{
    $arContratos = ( is_array(Sessao::read('arContratos2')) ? Sessao::read('arContratos2') : array());
    $arSessaoContratos = array();
    foreach ($arContratos as $arContrato) {
        if ($arContrato['inId'] != $_GET['inId']) {
            $inId = sizeof($arSessaoContratos);
            $arEvento['inId'] = $inId;
            $arSessaoContratos[] = $arContrato;
        }
    }
    Sessao::write('arContratos2', $arSessaoContratos);

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

function submeter()
{
    $obErro = new Erro();
    if ( ($_GET["stTipoFiltro"] == "contrato_todos" or $_GET["stTipoFiltro"] == "cgm_contrato_todos") and count(Sessao::read("arContratos")) == 0 ) {
        $obErro->setDescricao("@A lista de contratos deve ter pelo menos uma matrícula.");
    }
    if ($_GET["boCompetencia13"]) {
        if ($_GET["inCodIndicadorRecolhimento"] != "") {
            $obErro->setDescricao($obErro->getDescricao()."@Campo Indicador de Recolhimento inválido, não pode ser informado na competência 13!()");
        }
    } else {
        if ( in_array($_GET["inCodRecolhimento"],array(115,130,135,145,150,155,307,317,327,337,345,608,640,650,660)) and $_GET["inCodIndicadorRecolhimento"] == "" ) {
            $obErro->setDescricao($obErro->getDescricao()."@Campo Indicador de Recolhimento inválido!()");
        }
        if ( in_array($_GET["inCodRecolhimento"],array(145,307,317,327,337,345,640)) and $_GET["inCodIndicadorRecolhimento"] != "2" ) {
            $obErro->setDescricao($obErro->getDescricao()."@Campo Indicador de Recolhimento inválido, código de recolhimento 145,307,317,327,337,345 e 640 só aceita indicador igual a 2!()");
        }
        if ( in_array($_GET["inCodRecolhimento"],array(211)) and $_GET["inCodIndicadorRecolhimento"] != "" ) {
            $obErro->setDescricao($obErro->getDescricao()."@Campo Indicador de Recolhimento inválido, não pode ser informado para o código de recolhimento 211!(".$_GET["inCodIndicadorRecolhimento"].")");
        }
    }
    if ($_GET["inAno"] < 1967) {
        $obErro->setDescricao($obErro->getDescricao()."@Campo Ano da Competência inválido, o ano informado deve ser maior ou igual a 1967!()");
    }
    #Competência
    $inMes = (  $_GET["inCodMes"] < 10 ) ? "0".$_GET["inCodMes"] : $_GET["inCodMes"];
    $dtCompetencia = $_GET["inAno"].$inMes;
    if ( in_array($_GET["inCodRecolhimento"],array(211)) and $dtCompetencia < '200003' ) {
        $obErro->setDescricao($obErro->getDescricao()."@Campo Competência inválido, a competência deve ser maior ou igual a 03/2000 para código de recolhimento 211!()");
    }
    if ( in_array($_GET["inCodRecolhimento"],array(640)) and $dtCompetencia >= '198810' ) {
        $obErro->setDescricao($obErro->getDescricao()."@Campo Competência inválido, a competência deve ser menor que 10/1988 para código de recolhimento 640!()");
    }
    #Competência
    #Modalidade
    if ( $dtCompetencia < 199810 and !in_array($_GET["inCodModalidadeRecolhimento"],array(0,1,7,8)) ) {
        $obErro->setDescricao($obErro->getDescricao()."@Campo Modalidade do Recolhimento inválido, para competência  anterior a 10/1998 a modalidade deve ser 0,1,7 ou 8!()");
    }
    if ( in_array($_GET["inCodRecolhimento"],array(145,307,317,327,337,345,640,660)) and ($_GET["inCodModalidadeRecolhimento"] != 7 and $_GET["inCodModalidadeRecolhimento"] != 0) ) {
        $obErro->setDescricao($obErro->getDescricao()."@Campo Modalidade do Recolhimento inválido, para os códigos 145,307,317,327,337,345,640 e 660 a modalidade de recolhimento deve ser 0 ou 7!()");
    }
    if ( $_GET["inCodRecolhimento"] == 211 and !in_array($_GET["inCodModalidadeRecolhimento"],array(1,8,9)) ) {
        $obErro->setDescricao($obErro->getDescricao()."@Campo Modalidade do Recolhimento inválido, para o código 211 a modalidade de recolhimento deve ser 1,8 ou 9!()");
    }
    if ( $_GET["fpas"] == 868 and !in_array($_GET["inCodModalidadeRecolhimento"],array(7,9)) ) {
        $obErro->setDescricao($obErro->getDescricao()."@Campo Modalidade do Recolhimento inválido, para o FPAS 868 a modalidade de recolhimento deve ser 7 ou 9!()");
    }

    if ($_REQUEST["boSefipRetificadora"]) {
        if (is_array(Sessao::read("arContratosRetificadora2")) and count(Sessao::read("arContratosRetificadora2"))<=0) {
            $obErro->setDescricao($obErro->getDescricao()."@Campo Matrícula para o Sefip Retificadora é inválido!()");
        }
    }

    #Modalidade
    if ( $obErro->ocorreu() ) {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    } else {
        $stJs .= "parent.frames[2].Salvar();\n";
        $stJs .= "BloqueiaFrames(true,false);\n";
        $stJs .= "parent.frames[2].document.body.scrollTop=0;\n";
    }

    return $stJs;
}

function gerarSpanCompetencia13()
{
    include_once(CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoConfiguracao.class.php");
    $obRFolhaPagamentoConfiguracao = new RFolhaPagamentoConfiguracao;
    $obRFolhaPagamentoConfiguracao->consultar();
    $inMesCalculoDecimo = $obRFolhaPagamentoConfiguracao->getMesCalculoDecimo();

    if ($_GET["inCodMes"] == $inMesCalculoDecimo) {
        $obCkbCompetencia13 = new CheckBox();
        $obCkbCompetencia13->setRotulo("Somente Informações da Competência 13");
        $obCkbCompetencia13->setName("boCompetencia13");
        $obCkbCompetencia13->setTitle("Marque para emitir o arquivo da sefip para competência 13.");
        $obCkbCompetencia13->setNull(false);
        $obCkbCompetencia13->obEvento->setOnClick("montaParametrosGET('processarInfoComp13','boCompetencia13')");

        $obFormulario = new Formulario();
        $obFormulario->addComponente($obCkbCompetencia13);
        $obFormulario->montaInnerHTML();
        $stHtml = $obFormulario->getHTML();
    }
    $stJs .= "d.getElementById('spnCompetencia13').innerHTML = '".$stHtml."';   \n";

    return $stJs;
}

function geraSpanDataRecolhimentoFGTS()
{
    if ($_GET["inCodIndicadorRecolhimento"] == 2) {
        $obDtRecolhimentoFGTS = new Data();
        $obDtRecolhimentoFGTS->setRotulo("Data de Recolhimento FGTS");
        $obDtRecolhimentoFGTS->setName("dtRecolhimentoFGTS");
        $obDtRecolhimentoFGTS->setTitle("Informe a data do recolhimento do FGTS. A mesma deve ser posterior ao dia 07 do mês seguinte da competência e deve ser dia útil.");
        $obDtRecolhimentoFGTS->obEvento->setOnChange("montaParametrosGET('validaDataRecolhimentoFGTS','dtRecolhimentoFGTS,inCodMes,inAno');");
        $obDtRecolhimentoFGTS->setNull(false);

        $obFormulario = new Formulario();
        $obFormulario->addComponente($obDtRecolhimentoFGTS);
        $obFormulario->montaInnerHTML();
        $stHtml = $obFormulario->getHTML();
    }
    $stJs .= "d.getElementById('spnDataRecolhimento').innerHTML = '".$stHtml."';   \n";

    return $stJs;
}

function validaDataRecolhimentoFGTS()
{
    $arData = explode("/",$_GET["dtRecolhimentoFGTS"]);
    $dtRecolhimentoFGTS = $arData[2].$arData[1].$arData[0];
    $inCodMes = $_GET["inCodMes"] + 1;
    $inMes = (  $inCodMes < 10 ) ? "0".$inCodMes : $inCodMes;
    $dtCompetencia = $_GET["inAno"].$inMes.'07';
    $obErro = new Erro();
    if ( !($dtRecolhimentoFGTS > $dtCompetencia) ) {
        $obErro->setDescricao("Data de recolhimento em atraso do FGTS deve ser posterior ao dia 7 do mês seguinte a competência informada.");
    }
    if ( !$obErro->ocorreu() and (date('D',mktime(0,0,0,$arData[1],$arData[0],$arData[2])) == "Sun" or date('D',mktime(0,0,0,$arData[1],$arData[0],$arData[2])) == "Sat" )) {
        $obErro->setDescricao("Data de recolhimento em atraso do FGTS deve ser um dia útil.");
    }
    if ( $obErro->ocorreu() ) {
        $stJs .= "f.dtRecolhimentoFGTS.value = '';\n";
        $stJs .= "alertaAviso('@".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    }

    return $stJs;
}

function geraSpanDataRecolhimentoPrevidencia()
{
    if ($_GET["inCodIndicadorRecolhimentoPrevidencia"] == 2) {
        $obDtRecolhimentoPrevidencia = new Data();
        $obDtRecolhimentoPrevidencia->setRotulo("Data de Recolhimento Previdência");
        $obDtRecolhimentoPrevidencia->setName("dtRecolhimentoPrevidencia");
        $obDtRecolhimentoPrevidencia->setTitle("Informe a data do recolhimento da Previdência. A mesma deve ser posterior ao dia 02 do mês seguinte da competência.");
        $obDtRecolhimentoPrevidencia->obEvento->setOnChange("montaParametrosGET('validaDataRecolhimentoPrevidencia','dtRecolhimentoPrevidencia,inCodMes');");
        $obDtRecolhimentoPrevidencia->setNull(false);

        $obFormulario = new Formulario();
        $obFormulario->addComponente($obDtRecolhimentoPrevidencia);
        $obFormulario->montaInnerHTML();
        $stHtml = $obFormulario->getHTML();
    }
    $stJs .= "d.getElementById('spnDataRecolhimentoPrevidencia').innerHTML = '".$stHtml."';   \n";

    return $stJs;
}

function validaDataRecolhimentoPrevidencia()
{
    $arData = explode("/",$_GET["dtRecolhimentoPrevidencia"]);
    $dtRecolhimentoPrevidencia = $arData[2].$arData[1].$arData[0];
    $inCodMes = $_GET["inCodMes"] + 1;
    $inMes = (  $inCodMes < 10 ) ? "0".$inCodMes : $inCodMes;
    $dtCompetencia = $_GET["inAno"].$inMes.'07';
    $obErro = new Erro();
    if ( !($dtRecolhimentoPrevidencia > $dtCompetencia) ) {
        $obErro->setDescricao("Data de recolhimento em atraso da previdência deve ser posterior ao dia 2 do mês seguinte a competência informada.");
    }
    if ( !$obErro->ocorreu() and (date('D',mktime(0,0,0,$arData[1],$arData[0],$arData[2])) == "Sun" or date('D',mktime(0,0,0,$arData[1],$arData[0],$arData[2])) == "Sat" )) {
        $obErro->setDescricao("Data de recolhimento em atraso do FGTS deve ser um dia útil.");
    }
    if ( $obErro->ocorreu() ) {
        $stJs .= "f.dtRecolhimentoPrevidencia.value = '';\n";
        $stJs .= "alertaAviso('@".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    }

    return $stJs;
}

function gerarSpanArrecadouFGTS()
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
    $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
    $inMes = (  $_GET["inCodMes"] < 10 ) ? "0".$_GET["inCodMes"] : $_GET["inCodMes"];
    $dtCompetencia = $inMes."-".$_GET["inAno"];
    $stFiltroMovimentacao = " AND to_char(dt_final,'mm-yyyy') = '".$dtCompetencia."'";
    $obTFolhaPagamentoPeriodoMovimentacao->recuperaPeriodoMovimentacao($rsPeriodoMovimentacao,$stFiltroMovimentacao);
    if ( $rsPeriodoMovimentacao->getNumLinhas() == 1 ) {
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoCalculado.class.php");
        $obTFolhaPagamentoEventoCalculado = new TFolhaPagamentoEventoCalculado();
        $stFiltro  = " AND cod_periodo_movimentacao = ".$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao");
        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorCasoCausa.class.php");
        $obTPessoalContratoServidorCasoCausa = new TPessoalContratoServidorCasoCausa();
        $obTPessoalContratoServidorCasoCausa->recuperaRescisaoComContratoCalculado($rsContratosRescindidos,$stFiltro,"cod_contrato");
        if ($rsContratosRescindidos->getNumLinhas() > 0) {
            $obCkbArrecadouFGTS = new CheckBox();
            $obCkbArrecadouFGTS->setRotulo("Arrecadou FGTS na Guia de Recolhimento Rescisório?");
            $obCkbArrecadouFGTS->setName("boArrecadouFGTS");
            $obCkbArrecadouFGTS->setTitle("Desmarcar caso não tenha realizado todos os recolhimentos d GRR (antiga GRFC).");
            $obCkbArrecadouFGTS->setLabel("Sim");
            $obCkbArrecadouFGTS->setChecked(true);
            $obCkbArrecadouFGTS->obEvento->setOnChange("montaParametrosGET('gerarSpanMatriculasRescindidas','boArrecadouFGTS,inCodMes,inAno')");

            $obFormulario = new Formulario();
            $obFormulario->addComponente($obCkbArrecadouFGTS);
            $obFormulario->montaInnerHTML();
            $stHtml = $obFormulario->getHTML();
        }
    }
    $stJs .= "d.getElementById('spnArrecadouFGTS').innerHTML = '".$stHtml."';   \n";

    return $stJs;
}

function processarCompetencia()
{
    if ($_GET["inCodMes"] != "" and $_GET["inAno"] != "") {
        $stJs .= gerarSpanArrecadouFGTS();
        $stJs .= gerarSpanCompetencia13();
    }

    return $stJs;
}

function preencheConfiguracoesSEFIP()
{
    include_once( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php");
    $obTAdministracaoConfiguracao = new TAdministracaoConfiguracao();
    $obTAdministracaoConfiguracao->setDado("cod_modulo",40);
    $obTAdministracaoConfiguracao->setDado("exercicio",Sessao::getExercicio());
    $obTAdministracaoConfiguracao->pegaConfiguracao($stValor,"cnae_fiscal".Sessao::getEntidade());

    if ($stValor != "") {
        include_once( CAM_GT_CEM_MAPEAMENTO."TCEMCnaeFiscal.class.php" );
        $obTCEMCnaeFiscal = new TCEMCnaeFiscal;
        $stFiltro = " WHERE cod_cnae = ".$stValor;
        $obTCEMCnaeFiscal->recuperaCnaeAtivo( $rsCnaeFiscal,$stFiltro );

        $stJs .= "d.getElementById('cnae_fiscal').innerHTML = '".$rsCnaeFiscal->getCampo("valor_composto")."-".$rsCnaeFiscal->getCampo("nom_atividade")."';\n";
        $stJs .= "f.cnae_fiscal.value = '".$rsCnaeFiscal->getCampo("valor_composto")."';\n";
    }

    $obTAdministracaoConfiguracao->pegaConfiguracao($stValor,"centralizacao".Sessao::getEntidade());
    $stJs .= "d.getElementById('centralizacao').innerHTML = '".$stValor."';\n";
    $stJs .= "f.centralizacao.value = '".$stValor."';\n";
    $obTAdministracaoConfiguracao->pegaConfiguracao($stValor,"fpas".Sessao::getEntidade());
    $stJs .= "d.getElementById('fpas').innerHTML = '".$stValor."';\n";
    $stJs .= "f.fpas.value = '".$stValor."';\n";
    Sessao::write("fpas", $stValor);
    $obTAdministracaoConfiguracao->pegaConfiguracao($stValor,"gps".Sessao::getEntidade());
    $stJs .= "d.getElementById('gps').innerHTML = '".$stValor."';\n";
    $stJs .= "f.gps.value = '".$stValor."';\n";

    return $stJs;
}

function gerarSpanMatriculasRescindidas()
{
    if (!$_GET["boArrecadouFGTS"]) {
        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorCasoCausa.class.php");
        $obTPessoalContratoServidorCasoCausa = new TPessoalContratoServidorCasoCausa();
        $inMes = (  $_GET["inCodMes"] < 10 ) ? "0".$_GET["inCodMes"] : $_GET["inCodMes"];
        $dtCompetencia = $inMes."-".$_GET["inAno"];
        $stFiltro = " AND to_char(dt_final,'mm-yyyy') = '".$dtCompetencia."'";
        $obTPessoalContratoServidorCasoCausa->recuperaConstratosRescindidosComRecolhimentoFGTS($rsContratos,$stFiltro);

        $obLista = new Lista;
        $obLista->setTitulo("Lista de Matrículas Rescindidas com Recolhimento de FGTS");
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
        $obLista->ultimoCabecalho->addConteudo("Rescisão");
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Recolhido");
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();

        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento("DIREITA");
        $obLista->ultimoDado->setCampo( "[registro]" );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento("ESQUERDA");
        $obLista->ultimoDado->setCampo( "[numcgm]-[nom_cgm]" );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento("CENTRO");
        $obLista->ultimoDado->setCampo( "dt_rescisao" );
        $obLista->commitDado();

        $obChkRecolhido = new CheckBox();
        $obChkRecolhido->setLabel("Sim");
        $obChkRecolhido->setName("boRecolhido");

        $obLista->addDadoComponente($obChkRecolhido);
        $obLista->ultimoDado->setCampo( "boRecolhido" );
        $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
        $obLista->commitDadoComponente();

        $obLista->montaHTML();
        $stHtml = $obLista->getHTML();
        $stHtml = str_replace("\n","",$stHtml);
        $stHtml = str_replace("  ","",$stHtml);
        $stHtml = str_replace("'","\\'",$stHtml);
    }
    $stJs .= "d.getElementById('spnMatriculasRescindidas').innerHTML = '".$stHtml."';   \n";

    return $stJs;
}

function gerarSpanSefipRetificadora()
{
    Sessao::write("arContratosRetificadora", array());
    if ($_GET["boSefipRetificadora"] == "sim") {
        include_once ( CAM_GRH_PES_NEGOCIO."RConfiguracaoPessoal.class.php"                                 );
        $obRConfiguracaoPessoal = new RConfiguracaoPessoal();
        $obRConfiguracaoPessoal->Consultar();
        $stMascaraRegistro = $obRConfiguracaoPessoal->getMascaraRegistro();
        $arMascaraRegistro = explode("-",$stMascaraRegistro);
        $boMascaraRegistro = ( count($arMascaraRegistro) >= 2 ) ? true : false;
        $inSize            = strlen($arMascaraRegistro[0]);

        $obLblCGM = new Label;
        $obLblCGM->setRotulo ( "CGM"      );
        $obLblCGM->setName   ( "inNomCGMRetificadora" );
        $obLblCGM->setId     ( "inNomCGMRetificadora" );

        $obHdnCGM = new Hidden;
        $obHdnCGM->setName("hdnCGMRetificadora");

        $obHdnRegistro = new Hidden;
        $obHdnRegistro->setName("hdnContratoRetificadora");

        $obTxtRegistroContrato = new TextBox;
        $obTxtRegistroContrato->setRotulo                   ( "Matrícula"                                       );
        $obTxtRegistroContrato->setTitle                    ( "Informe a matrícula do servidor."                );
        $obTxtRegistroContrato->setName                     ( "inContratoRetificadora"                                      );
        $obTxtRegistroContrato->setId                       ( "inContratoRetificadora"                                      );
        $obTxtRegistroContrato->setInteiro                  ( true                                              );
        $obTxtRegistroContrato->setMaxLength                ( $inSize                                           );
        $obTxtRegistroContrato->setMinLength                ( 1                                                 );
        $obTxtRegistroContrato->setSize                     ( $inSize                                           );
        $obTxtRegistroContrato->obEvento->setOnChange("montaParametrosGET('preencherMatriculaRetificadora','inContratoRetificadora',true);");
        $obTxtRegistroContrato->obEvento->setOnBlur("montaParametrosGET('preencherMatriculaRetificadora','inContratoRetificadora',true);");

        $obImagem    = new Img;
        $obImagem->setCaminho   ( CAM_FW_IMAGENS."botao_popup.png");
        $obImagem->setAlign     ( "absmiddle" );
        $obImagem->montaHtml();

        $obLink = new Link();
        $obLink->setValue($obImagem->getHtml());
        $obLink->setHref("JavaScript: abrePopUp('".CAM_GRH_IMA_POPUPS."SEFIP/FLProcurarMatricula.php','frm','".$obTxtRegistroContrato->getName()."','".$obLblCGM->getName()."','','".Sessao::getId()."','800','550')");

        $obSpnContratos = new Span;
        $obSpnContratos->setid( "spnContratosRetificadora");

        $stName = "Contrato";

        $obBtnIncluir = new Button;
        $obBtnIncluir->setName              ( "btIncluir$stName"    );
        $obBtnIncluir->setValue             ( "Incluir"             );
        $obBtnIncluir->obEvento->setOnClick ( "montaParametrosGET('incluirContratoRetificadora','');" );
        $arBarra[] = $obBtnIncluir;

        $obBtnLimpar = new Button;
        $obBtnLimpar->setName              ( "btLimpar$stName"          );
        $obBtnLimpar->setValue             ( "Limpar"                   );
        $obBtnLimpar->obEvento->setOnClick ( "montaParametrosGET('limparContratoRetificadora','');");
        $arBarra[] = $obBtnLimpar;

        $obFormulario = new Formulario;
        $obFormulario->addTitulo("Matrícula");
        $obFormulario->addComponente($obLblCGM);
        $obFormulario->addHidden($obHdnRegistro);
        $obFormulario->addHidden($obHdnCGM);
        $obFormulario->agrupaComponentes(array($obTxtRegistroContrato,$obLink));
        $obFormulario->defineBarra($arBarra);
        $obFormulario->addSpan($obSpnContratos);
        $obFormulario->montaInnerHTML();

        $stHtml = $obFormulario->getHTML();
    }
    $stJs .= "d.getElementById('spnSefipRetificadora').innerHTML = '".$stHtml."';\n";

    return $stJs;
}

function preencherMatriculaRetificadora()
{
    if (trim($_GET["inContratoRetificadora"])) {
        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php");
        $obTPessoalContrato = new TPessoalContrato();
        $stFiltro = " AND registro = ".$_GET["inContratoRetificadora"];
        $obTPessoalContrato->recuperaCgmDoRegistro($rsContrato,$stFiltro);
        if ($rsContrato->getNumLinhas() == 1) {
            $stJs = "d.getElementById('inNomCGMRetificadora').innerHTML = '".$rsContrato->getCampo("numcgm")."-".$rsContrato->getCampo("nom_cgm")."';\n";
            $stJs .= "f.hdnContratoRetificadora.value = '".$rsContrato->getCampo("cod_contrato")."';\n";
            $stJs .= "f.hdnCGMRetificadora.value = '".$rsContrato->getCampo("numcgm")."-".$rsContrato->getCampo("nom_cgm")."';\n";
        } else {
            $stJs  = limparMatriculaRetificadora();
        }
    }

    return $stJs;
}

function limparMatriculaRetificadora()
{
    $stJs  = "d.getElementById('inNomCGMRetificadora').innerHTML = '';\n";
    $stJs .= "f.hdnContratoRetificadora.value = '';\n";
    $stJs .= "f.inContratoRetificadora.value = '';\n";
    $stJs .= "f.hdnCGMRetificadora.value = '';\n";

    return $stJs;
}

function incluirContratoRetificadora()
{
    $obErro = new erro;
    if ($_GET["inContratoRetificadora"] == "") {
        $obErro->setDescricao("Campo Matrícula inválido!()");
    }
    if (!$obErro->ocorreu()) {
        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidor.class.php");
        $obTPessoalContratoServidor = new TPessoalContratoServidor();
        $obTPessoalContratoServidor->setDado("cod_contrato",$_GET["hdnContratoRetificadora"]);
        $obTPessoalContratoServidor->recuperaPorChave($rsContratoServidor);

        include_once(CAM_GRH_IMA_MAPEAMENTO."TIMACategoriaSefip.class.php");
        $obTIMACategoriaSefip = new TIMACategoriaSefip();
        $stFiltro = " AND cod_categoria = ".$rsContratoServidor->getCampo("cod_categoria");
        $obTIMACategoriaSefip->recuperaRelacionamento($rsCategoriaSefip,$stFiltro);

        $inSefip = "";
        if ($rsCategoriaSefip->getCampo("sefip") == "0") {
            $inSefip = 7;
        }
        if ($rsCategoriaSefip->getCampo("sefip") == "1") {
            $inSefip = 8;
        }

        $arMatricula["registro"] = $_GET["inContratoRetificadora"];
        $arMatricula["cod_contrato"] = $_GET["hdnContratoRetificadora"];
        $arMatricula["cgm"] = $_GET["hdnCGMRetificadora"];
        $arMatricula["categoria"] = $rsContratoServidor->getCampo("cod_categoria");
        $arMatricula["modalidade"] = $inSefip;
        if (array_search($arMatricula,Sessao::read("arContratosRetificadora"))) {
            $obErro->setDescricao("Essa Matrícula já encontra-ser na lista.");
        }
    }
    if (!$obErro->ocorreu() and $inSefip == "") {
        $obErro->setDescricao("A categoria da matrícula não esta configuração nas modalidades de recolhimento da SEFIP.");
    }
    if (!$obErro->ocorreu()) {
        $arSessaoContratosRetificadora = Sessao::read('arContratosRetificadora');
        $arSessaoContratosRetificadora[] = $arMatricula;
        Sessao::write('arContratosRetificadora', $arSessaoContratosRetificadora);

        $stJs .= gerarSpanContratoRetificadora();
        $stJs .= limparMatriculaRetificadora();

    } else {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    }

    return $stJs;
}

function gerarSpanContratoRetificadora()
{
    $rsContratos = new recordset();
    $rsContratos->preenche(Sessao::read("arContratosRetificadora"));

    $obLista = new Lista;
    $obLista->setTitulo("Lista de Matrículas à Retificar");
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
    $obLista->ultimoCabecalho->addConteudo("Categoria");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Modalidade");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 2 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("DIREITA");
    $obLista->ultimoDado->setCampo( "[registro]" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("ESQUERDA");
    $obLista->ultimoDado->setCampo( "cgm" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "categoria" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "modalidade" );
    $obLista->commitDado();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "EXCLUIR" );
    $obLista->ultimaAcao->setFuncaoAjax( true );
    $obLista->ultimaAcao->setLink( "JavaScript:executaFuncaoAjax('excluirContratoRetificadora');");
    $obLista->ultimaAcao->addCampo("1","cod_contrato");
    $obLista->commitAcao();

    $obLista->montaHTML();
    $stHtml = $obLista->getHTML();
    $stHtml = str_replace("\n","",$stHtml);
    $stHtml = str_replace("  ","",$stHtml);
    $stHtml = str_replace("'","\\'",$stHtml);

    $stJs .= "d.getElementById('spnContratosRetificadora').innerHTML = '".$stHtml."';   \n";

    return $stJs;
}

function excluirContratoRetificadora()
{
    $arTemp = array();
    foreach (Sessao::read("arContratosRetificadora") as $arContrato) {
        if ($arContrato["cod_contrato"] != $_GET["cod_contrato"]) {
            $arTemp[] = $arContrato;
        }
    }
    Sessao::write("arContratosRetificadora", $arTemp);
    $stJs = gerarSpanContratoRetificadora();

    return $stJs;
}

function processarInfoComp13()
{
    $stJs  = "f.inCodIndicadorRecolhimentoTxt.value = '';\n";
    $stJs .= "f.inCodIndicadorRecolhimento.value = '';\n";

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
    case "submeter":
        $stJs .= submeter();
    break;
    case "gerarSpanCompetencia13":
        $stJs .= gerarSpanCompetencia13();
        break;
    case "geraSpanDataRecolhimentoFGTS":
        $stJs .= geraSpanDataRecolhimentoFGTS();
        break;
    case "geraSpanDataRecolhimentoPrevidencia":
        $stJs .= geraSpanDataRecolhimentoPrevidencia();
        break;
    case "processarCompetencia":
        $stJs .= processarCompetencia();
        break;
    case "gerarSpanMatriculasRescindidas":
        $stJs .= gerarSpanMatriculasRescindidas();
        break;
    case "validaDataRecolhimentoFGTS":
        $stJs .= validaDataRecolhimentoFGTS();
        break;
    case "validaDataRecolhimentoPrevidencia":
        $stJs .= validaDataRecolhimentoPrevidencia();
        break;
    case "gerarSpanSefipRetificadora":
        $stJs = gerarSpanSefipRetificadora();
        break;
    case "preencherMatriculaRetificadora":
        $stJs = preencherMatriculaRetificadora();
        break;
    case "incluirContratoRetificadora":
        $stJs = incluirContratoRetificadora();
        break;
    case "excluirContratoRetificadora":
        $stJs = excluirContratoRetificadora();
        break;
    case "limparContratoRetificadora":
        $stJs = limparMatriculaRetificadora();
        break;
    case "processarInfoComp13":
        $stJs = processarInfoComp13();
        break;
}

if ($stJs) {
   echo $stJs;
}

?>
