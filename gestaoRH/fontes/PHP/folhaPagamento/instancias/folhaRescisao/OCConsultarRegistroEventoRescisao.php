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
    * Página de Oculto do Consultar Registro de Evento de Rescisão
    * Data de Criação: 17/10/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30566 $
    $Name$
    $Author: rgarbin $
    $Date: 2008-04-03 10:54:36 -0300 (Qui, 03 Abr 2008) $

    * Casos de uso: uc-04.05.54
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroContrato.class.php"                                      );
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroCGMContrato.class.php"                                   );

//Define o nome dos arquivos PHP
$stPrograma = "ConsultarRegistroEventoRescisao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

function montaSpanContrato()
{
    $obIFiltroContrato = new IFiltroContrato(true);
    $obIFiltroContrato->obIContratoDigitoVerificador->obTxtRegistroContrato->setNull(false);
    $obIFiltroContrato->setInformacoesFuncao  ( true  );

    $obFormulario = new Formulario;
    $obIFiltroContrato->geraFormulario          ( $obFormulario                                             );
    $obFormulario->obJavaScript->montaJavaScript();
    $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
    $stEval = str_replace("\n","",$stEval);
    $obFormulario->montaInnerHtml();
    $stJs .= "d.getElementById('spnFiltro').innerHTML = '".$obFormulario->getHTML()."';     \n";
    $stJs .= "d.getElementById('spnSpanLista').innerHTML = '';  \n";
    $stJs .= "f.stEval.value                          = '".$stEval."';                      \n";
    $stJs .= "d.getElementById('inContrato').focus();";
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function montaSpanCGMContrato()
{
    $obIFiltroCGMContrato = new IFiltroCGMContrato(true);
    $obIFiltroCGMContrato->setInformacoesFuncao  ( true  );
    $obIFiltroCGMContrato->obCmbContrato->setNull( false );

    $obFormulario = new Formulario;
    $obIFiltroCGMContrato->geraFormulario       ( $obFormulario                                             );
    $obFormulario->obJavaScript->montaJavaScript();
    $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
    $stEval = str_replace("\n","",$stEval);
    $obFormulario->montaInnerHtml();
    $stJs .= "d.getElementById('spnFiltro').innerHTML = '".$obFormulario->getHTML()."';    \n";
    $stJs .= "d.getElementById('spnSpanLista').innerHTML = '';  \n";
    $stJs .= "f.stEval.value                          = '".$stEval."';                     \n";
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function limparFiltro()
{
    $stJs .= "f.boOpcao[0].checked = true;  \n";
    $stJs .= montaSpanContrato();

    return $stJs;
}

function abrePopUpRegistrosEventos()
{
    $stErros = false;

    //Valida formulario
    if (trim($_REQUEST["boOpcao"]) == "evento") {
        if (trim($_REQUEST['inCodigoEvento']) == "") {
            $stErros = true;
            $stJs .= "alertaAviso('@Campo Evento obrigatório . ','form','erro','".Sessao::getId()."');";
        }
    }

    if (trim($_REQUEST["boOpcao"]) == "contrato") {
        if (trim($_REQUEST['inContrato']) == "") {
            $stErros = true;
            $stJs .= "alertaAviso('@Campo Matricula obrigatório . ','form','erro','".Sessao::getId()."');";
        }
    }

    if (trim($_REQUEST["boOpcao"]) == "cgm_contrato") {
        if (trim($_REQUEST['inNumCGM']) == "") {
            $stErros = true;
            $stJs .= "alertaAviso('@Campo CGM obrigatório . ','form','erro','".Sessao::getId()."');";
        }

        if (trim($_REQUEST['inContrato']) == "") {
            $stErros = true;
            $stJs .= "alertaAviso('@Campo Matricula obrigatório . ','form','erro','".Sessao::getId()."');";
        }
    }

    if ($stErros === false) {
        $stJs .= "var width  = 800;\n";
        $stJs .= "var height = 550;\n";
        $stJs .= "var sFiltros     = '&inContrato=".$_GET['inContrato']."';";
        $stJs .= "    sFiltros     +='&inCodMes=".$_GET['inCodMes']."';";
        $stJs .= "    sFiltros     +='&inAno=".$_GET['inAno']."';";

        if ($_GET['boOpcao'] == 'contrato') {
            $stJs .= "sFiltros    +='&hdnCGM=".$_GET['hdnCGM']."';";
        } else {
            $stJs .= "sFiltros    +='&inNumCGM=".$_GET['inNumCGM']."';";
            $stJs .= "sFiltros    +='&inCampoInner=".$_GET['inCampoInner']."';";
        }

        $obErro = new Erro;
        if ( empty($_GET['inContrato']) ) {
            $obErro->setDescricao('O campo matrícula é inválido.');
            $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
        } else {
            $stJs .= "var sSessao      = '".Sessao::getId()."';\n";
            $stJs .= "var sUrlFrames   = '".CAM_GRH_FOL_POPUPS."rescisao/FRConsultarRegistroEventoRescisao.php?sUrlConsulta=FMConsultarRegistroEventoRescisao.php?'+sSessao+sFiltros;\n";
            $stJs .= "window.open( sUrlFrames, 'popUpRegistrosEventos', 'width='+width+',height='+height+',resizable=1,scrollbars=1,left=0,top=0' );\n";
        }
    }

    return $stJs;

}

function montaSpanEvento()
{
     include_once(CAM_GRH_FOL_COMPONENTES.'IBscEvento.class.php');

    $obIBscEvento = new IBscEvento();
    $obIBscEvento->setTodosEventos(true);

    $stOnChange = $obIBscEvento->obBscInnerEvento->obCampoCod->obEvento->getOnBlur();
    $stOnBlur .= " document.getElementById('spnSpanLista').innerHTML = ''; ";
    $obIBscEvento->obBscInnerEvento->obCampoCod->obEvento->setOnBlur($stOnBlur);

    $obFormulario = new Formulario;
    $obFormulario->addTitulo("Filtro por Evento");
    $obIBscEvento->geraFormulario($obFormulario);
    $obFormulario->montaInnerHtml();

    $stJs .= "d.getElementById('spnFiltro').innerHTML = '".$obFormulario->getHTML()."';";
    $stJs .= "d.getElementById('inCodigoEvento').focus();";
    $stJs .= "d.getElementById('spnSpanLista').innerHTML = '';  \n";

    return $stJs;
}

function montaSpanBotao()
{
    $obFormulario = new Formulario;

    if (trim($_REQUEST["boOpcao"]) != "evento") {
        $obBtnOK = new Button;
        $obBtnOK->setName                  ( "btnOk"  );
        $obBtnOK->setId                    ( "btnOk"  );
        $obBtnOK->setValue                 ( "Ok"     );
        $obBtnOK->setTipo                  ( "button" );
        $obBtnOK->obEvento->setOnClick     ( "montaParametrosGET('abrePopUpRegistrosEventos','',true);" );
        $obBtnOK->setStyle                 ( "width: 60px" );

        $obBtnLimpar = new Button;
        $obBtnLimpar->setName              ( "btnLimparForm" );
        $obBtnLimpar->setValue             ( "Limpar"        );
        $obBtnLimpar->setTipo              ( "button"        );
        $obBtnLimpar->obEvento->setOnClick ( "executaFuncaoAjax('limparFiltro');"  );
        $obFormulario->defineBarra ( array($obBtnOK , $obBtnLimpar) );
    } else {
        $obBtnVisualizar = new Button;
        $obBtnVisualizar->setName                       ( "btnVisualizar"             );
        $obBtnVisualizar->setValue                      ( "Visualizar"                );
        $obBtnVisualizar->setTipo                       ( "button"                    );
        $obBtnVisualizar->obEvento->setOnClick          ( "montaParametrosGET('visualizar', '', true);" );
        $obFormulario->defineBarra ( array($obBtnVisualizar) );
    }
    $obFormulario->montaInnerHtml();
    $stJs .= "d.getElementById('spnSpanBotao').innerHTML = '".$obFormulario->getHTML()."';";

    return $stJs;
}

function montaListaContratos()
{
    $stErros = false;

    include_once ( CAM_GRH_FOL_NEGOCIO . "RFolhaPagamentoPeriodoMovimentacao.class.php"                            );
    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/Table.class.php';
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoCalculado.class.php"                                 );
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoRegistroEventoRescisao.class.php"                            );

    //Valida formulario
    if (trim($_REQUEST["boOpcao"]) == "evento") {
        if (trim($_REQUEST['inCodigoEvento']) == "") {
            $stErros = true;
            $stJs .= "alertaAviso('@Campo Evento obrigatório . ','form','erro','".Sessao::getId()."');";
        }
    }

    if (trim($_REQUEST["boOpcao"]) == "contrato") {
        if (trim($_REQUEST['inContrato']) == "") {
            $stErros = true;
            $stJs .= "alertaAviso('@Campo Matricula obrigatório . ','form','erro','".Sessao::getId()."');";
        }
    }

    if (trim($_REQUEST["boOpcao"]) == "cgm_contrato") {
        if (trim($_REQUEST['inNumCGM']) == "") {
            $stErros = true;
            $stJs .= "alertaAviso('@Campo CGM obrigatório . ','form','erro','".Sessao::getId()."');";
        }

        if (trim($_REQUEST['inContrato']) == "") {
            $stErros = true;
            $stJs .= "alertaAviso('@Campo Matricula obrigatório . ','form','erro','".Sessao::getId()."');";
        }
    }

    if ($stErros === false) {
        //Busca a competencia
        $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
        $obTFolhaPagamentoPeriodoMovimentacao->setDado("mes", $_REQUEST['inCodMes']);
        $obTFolhaPagamentoPeriodoMovimentacao->setDado("ano", $_REQUEST['inAno']);
        $obTFolhaPagamentoPeriodoMovimentacao->recuperaPeriodoMovimentacaoDaCompetencia($rsPeriodoMovimentacao);

        //Busca o código correto do evento
        $obTFolhaPagamentoEvento = new TFolhaPagamentoEventoCalculado();
        $stFiltro = "where codigo = '".$_REQUEST['inCodigoEvento']."'";
        $obTFolhaPagamentoEvento->recuperaCodigoEventoFichaFinanceira( $rsEvento, $stFiltro);

        /************* Montando busca da listagem de contratos **************/
        $rsLista = new Recordset;
        $obTFolhaPagamentoEventoRescisao = new TFolhaPagamentoRegistroEventoRescisao();
        $stFiltro =  " AND registro_evento_rescisao.cod_evento = ".$rsEvento->getCampo("cod_evento");
        $stFiltro .= " AND registro_evento_rescisao.cod_periodo_movimentacao = ".$rsPeriodoMovimentacao->getCampo('cod_periodo_movimentacao');
        $stOrdem = "  ORDER BY nom_cgm";
        $obTFolhaPagamentoEventoRescisao->recuperaRegistroContratoRescisao( $rsLista, $stFiltro, $stOrdem);
        /***********Monta a Lista de contratos****************/
        $obLista = new Table();
        $obLista->setRecordset($rsLista);
        $obLista->setSummary("Lista de Contratos do Evento");

        $obLista->Head->addCabecalho("Matrícula",5);
        $obLista->Head->addCabecalho("Nome",50);
        $obLista->Head->addCabecalho("Desdobramento",20);
        $obLista->Head->addCabecalho("Quantidade",10);
        $obLista->Head->addCabecalho("Valor",15);

        $obLista->Body->addCampo( 'matricula', 'D' );
        $obLista->Body->addCampo( 'nom_cgm', 'E' );
        $obLista->Body->addCampo( 'descricao', 'E' );
        $obLista->Body->addCampo( 'quantidade', 'D' );
        $obLista->Body->addCampo( 'valor', 'D' );

        $obLista->Foot->addSoma ( 'quantidade', 'D' );
        $obLista->Foot->addSoma ( 'valor', 'D' );

        $obLista->Body->addAcao("consultar","executaFuncaoAjax('%s','&inContrato=%s&inCodMes=".$_REQUEST["inCodMes"]."&inAno=".$_REQUEST["inAno"]."&inCodigoEvento=".$_REQUEST["inCodigoEvento"]."&inNumCGM=".$_REQUEST["inNumCGM"]."&boOpcao=evento')",array('abrePopUpRegistrosEventos','matricula', 'inCodMes', 'inAno', 'boOpcao', 'inCodigoEvento', 'inNumCGM'));

        $obLista->montaHTML(true);
        $stJs = "d.getElementById('spnSpanLista').innerHTML = '".$obLista->getHtml()."';  \n";
    }

    return $stJs;
}

$stJs.= montaSpanBotao();
switch ($_REQUEST['stCtrl']) {
    case "montaSpanContrato":
        $stJs .= montaSpanContrato();
        break;

    case "montaSpanCGMContrato":
        $stJs .= montaSpanCGMContrato();
        break;

    case "limparFiltro":
        $stJs .= limparFiltro();
        break;

    case "abrePopUpRegistrosEventos":
        $stJs .= abrePopUpRegistrosEventos();
        break;

    case "montaSpanEvento":
        $stJs.= montaSpanEvento();
        break;

    case "visualizar":
        $stJs.= montaListaContratos();
        break;
}

if ($stJs) {
   echo $stJs;
}

?>
