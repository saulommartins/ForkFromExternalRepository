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
    * Oculto
    * Data de Criação: 21/11/2005

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30936 $
    $Name$
    $Author: rgarbin $
    $Date: 2008-04-03 10:54:36 -0300 (Qui, 03 Abr 2008) $

    * Casos de uso: uc-04.05.34
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroContrato.class.php"                                          );
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroCGMContrato.class.php"                                       );

//Define o nome dos arquivos PHP
$stPrograma = "RelatorioEventoPorContrato";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

function processarForm($boExecuta=false)
{
    $stJs .= montaSpanContrato();
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function montaSpanContrato($boExecuta=false)
{
    $obIFiltroContrato = new IFiltroContrato;
    $obIFiltroContrato->setInformacoesFuncao  ( true  );
    $obIFiltroContrato->obIContratoDigitoVerificador->setNull(false);

    $obFormulario = new Formulario;
    $obIFiltroContrato->geraFormulario          ( $obFormulario                                             );
    $obFormulario->obJavaScript->montaJavaScript();
    $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
    $stEval = str_replace("\n","",$stEval);
    $obFormulario->montaInnerHtml();
    $stJs .= "d.getElementById('spnFiltro').innerHTML = '".$obFormulario->getHTML()."';     \n";
    $stJs .= "f.hdnFiltro.value                       = '".$stEval."';                      \n";
    $stJs .= "d.getElementById('inContrato').focus();";
    $stJs .= "d.getElementById('spnSpanLista').innerHTML = '';  \n";
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function montaSpanCGMContrato($boExecuta=false)
{
    $obIFiltroCGMContrato = new IFiltroCGMContrato;
    $obIFiltroCGMContrato->setInformacoesFuncao  ( true  );
    $obIFiltroCGMContrato->obCmbContrato->setNull( false );

    $obFormulario = new Formulario;
    $obIFiltroCGMContrato->geraFormulario       ( $obFormulario                                             );
    $obFormulario->obJavaScript->montaJavaScript();
    $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
    $stEval = str_replace("\n","",$stEval);
    $obFormulario->montaInnerHtml();
    $stJs .= "d.getElementById('spnFiltro').innerHTML = '".$obFormulario->getHTML()."';    \n";
    $stJs .= "f.hdnFiltro.value                       = '".$stEval."';                     \n";
    $stJs .= "d.getElementById('spnSpanLista').innerHTML = '';  \n";
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function montaSpanEvento()
{
     include_once(CAM_GRH_FOL_COMPONENTES.'IBscEvento.class.php');

    $obIBscEvento = new IBscEvento();
    $obIBscEvento->setTodosEventos(true);
    $obIBscEvento->obBscInnerEvento->setNull(false);

    $stOnChange = $obIBscEvento->obBscInnerEvento->obCampoCod->obEvento->getOnBlur();
    $stOnBlur .= " document.getElementById('spnSpanLista').innerHTML = ''; ";
    $obIBscEvento->obBscInnerEvento->obCampoCod->obEvento->setOnBlur($stOnBlur);

    $obFormulario = new Formulario;
    $obFormulario->addTitulo("Filtro por Evento");
    $obIBscEvento->geraFormulario($obFormulario);
    $obFormulario->obJavaScript->montaJavaScript();
    $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
    $stEval = str_replace("\n","",$stEval);
    $obFormulario->montaInnerHtml();

    $stJs .= "d.getElementById('spnFiltro').innerHTML = '".$obFormulario->getHTML()."';";
    $stJs .= "f.hdnFiltro.value                       = '".$stEval."';                     \n";
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
        $obBtnOK->obEvento->setOnClick     ( "if ( Valida() ) { montaParametrosGET('processarPopUp', 'inContrato,inCodMes,inAno'); }" );
        $obBtnOK->setStyle                 ( "width: 60px" );

        $obBtnLimpar = new Button;
        $obBtnLimpar->setName              ( "btnLimparForm" );
        $obBtnLimpar->setValue             ( "Limpar"        );
        $obBtnLimpar->setTipo              ( "button"        );
        $obBtnLimpar->obEvento->setOnClick ( "limpaForm();"  );
        $obFormulario->defineBarra ( array($obBtnOK , $obBtnLimpar) );
    } else {
        $obBtnVisualizar = new Button;
        $obBtnVisualizar->setName                       ( "btnVisualizar"             );
        $obBtnVisualizar->setValue                      ( "Visualizar"                );
        $obBtnVisualizar->setTipo                       ( "button"                    );
        $obBtnVisualizar->obEvento->setOnClick          ( " if ( Valida() ) { buscaValor('visualizar'); }" );
        $obFormulario->defineBarra ( array($obBtnVisualizar) );
    }
    $obFormulario->montaInnerHtml();
    $stJs .= "d.getElementById('spnSpanBotao').innerHTML = '".$obFormulario->getHTML()."';";

    return $stJs;
}

function montaListaContratos()
{
    $obErro = new erro;

    if ( (trim($_REQUEST['inCodMes']) == "") or (trim($_REQUEST['inAno']) == "") ) {

        $obErro->setDescricao("Campo Competência inválido!()");

    } else {

        include_once ( CAM_GRH_FOL_NEGOCIO . "RFolhaPagamentoPeriodoMovimentacao.class.php"                            );
        include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
        include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
        include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/Table.class.php';
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoCalculado.class.php"                                 );
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoRegistroEventoPeriodo.class.php"                           );

        //Busca a competencia
        $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
        $obTFolhaPagamentoPeriodoMovimentacao->setDado("mes", $_REQUEST['inCodMes']);
        $obTFolhaPagamentoPeriodoMovimentacao->setDado("ano", $_REQUEST['inAno']);
        $obTFolhaPagamentoPeriodoMovimentacao->recuperaPeriodoMovimentacaoDaCompetencia($rsPeriodoMovimentacao);

        if ($rsPeriodoMovimentacao->getNumLinhas() > 0) {

            //Busca o código correto do evento
            $obTFolhaPagamentoEvento = new TFolhaPagamentoEventoCalculado();
            $stFiltro = "where codigo::integer = '".$_REQUEST['inCodigoEvento']."':: integer ";
            $obTFolhaPagamentoEvento->recuperaCodigoEventoFichaFinanceira( $rsEvento, $stFiltro);

            /************* Montando busca da listagem de contratos **************/
            $rsLista = new Recordset;
            $obTFolhaPagamentoRegistroEventoPeriodo = new TFolhaPagamentoRegistroEventoPeriodo();
            $stFiltro = " AND registro_evento.cod_evento::integer = ".$rsEvento->getCampo("cod_evento");
            $stFiltro .= " AND registro_evento_periodo.cod_periodo_movimentacao = ".$rsPeriodoMovimentacao->getCampo('cod_periodo_movimentacao');
            $stOrdem = "  ORDER BY nom_cgm, matricula, proporcional";
            $obTFolhaPagamentoRegistroEventoPeriodo->recuperarRegistroContratoSalarioComPensionista( $rsLista, $stFiltro, $stOrdem);

            $rsListaCorrigida = new recordset();
            $rsListaCorrigida = corrigeArrayContratos($rsLista);
            $rsListaCorrigida->setPrimeiroElemento();

            /***********Monta a Lista de contratos****************/
            $obLista = new Table();
            $obLista->setRecordset($rsListaCorrigida);
            $obLista->setSummary("Lista de Contratos do Evento");

            $obLista->Head->addCabecalho("Matrícula",5);
            $obLista->Head->addCabecalho("Nome",70);
            $obLista->Head->addCabecalho("Quantidade",10);
            $obLista->Head->addCabecalho("Valor",15);

            $obLista->Body->addCampo( 'matricula', 'D' );
            $obLista->Body->addCampo( 'nom_cgm', 'E' );
            $obLista->Body->addCampo( 'quantidade', 'D' );
            $obLista->Body->addCampo( 'valor', 'D' );

            $obLista->Foot->addSoma ( 'quantidade', 'D' );
            $obLista->Foot->addSoma ( 'valor', 'D' );

            $obLista->Body->addAcao("consultar","executaFuncaoAjax('%s','&inContrato=%s&inCodMes=".$_REQUEST["inCodMes"]."&inAno=".$_REQUEST["inAno"]."&boOpcao=evento')",array('processarPopUp','matricula', 'inCodMes', 'inAno', 'boOpcao'));
            $rsListaCorrigida->setPrimeiroElemento();
            $obLista->montaHTML(true);
            $stJs = "d.getElementById('spnSpanLista').innerHTML = '".$obLista->getHtml()."';  \n";
        } else {
            $obErro->setDescricao("Campo Competência inválido, não foi aberto o período de movimentação!");
        }
    }

    if ($obErro->ocorreu()) {
        $stJs .= "alertaAviso('@".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    }

    return $stJs;
}

function corrigeArrayContratos($rsLista)
{
    $rsTemp = clone $rsLista;
    $arLista = $rsTemp->getElementos();

    $inCodContratoAnterior = "";
    $arContratoAnterior = array();
    $arTemp = array();

    foreach ($arLista as $inIndex => $arContrato) {
        if( !($arContrato["cod_contrato"] == $arLista[$inIndex+1]["cod_contrato"]
        and $arContrato["cod_evento"]   == $arLista[$inIndex+1]["cod_evento"]
        and $arLista[$inIndex+1]["proporcional"] == "t")){
            $arTemp[] = $arContrato;
        }
    }
    reset($arTemp);
    $rsListaOk = new Recordset;
    $rsListaOk->preenche($arTemp);
    $rsListaOk->setPrimeiroElemento();

    return $rsListaOk;
}

function processarPopUp()
{
    $obErro = new erro;

    if ( (trim($_REQUEST['inCodMes']) == "") or (trim($_REQUEST['inAno']) == "") ) {
        $obErro->setDescricao("Campo Competência inválido!()");
    } else {
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
        //Busca a competencia
        $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
        $obTFolhaPagamentoPeriodoMovimentacao->setDado("mes", $_REQUEST['inCodMes']);
        $obTFolhaPagamentoPeriodoMovimentacao->setDado("ano", $_REQUEST['inAno']);
        $obTFolhaPagamentoPeriodoMovimentacao->recuperaPeriodoMovimentacaoDaCompetencia($rsPeriodoMovimentacao);

        if (trim($rsPeriodoMovimentacao->getCampo('cod_periodo_movimentacao'))=="") {
            $obErro->setDescricao("Campo Competência inválido, não foi aberto o período de movimentação!");
        }
    }

    if (!$obErro->ocorreu()) {
        $stFiltros = "&inContrato=".$_GET['inContrato']."&inCodMes=".$_GET["inCodMes"]."&inAno=".$_GET["inAno"];
        $stUrlFrame = CAM_GRH_FOL_POPUPS."movimentacaoFinanceira/FRConsultarRegistroEvento.php?sUrlConsulta=LSConsultarRegistroEvento.php?".Sessao::getId().$stFiltros;
        $stJs .=  "window.open('".$stUrlFrame."', 'popUpRegistrosEventos', 'width=800,height=550,resizable=1,scrollbars=1,left=0,top=0');";
    } else {
        $stJs .= "alertaAviso('@".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    }

    return $stJs;
}

$stDarEcho = false;
$stJs.= montaSpanBotao();
switch ($_REQUEST["stCtrl"]) {
    case "montaSpanContrato":
        $stJs.= montaSpanContrato();
        break;

    case "montaSpanCGMContrato":
        $stJs.= montaSpanCGMContrato();
        break;

    case "montaSpanEvento":
        $stJs.= montaSpanEvento();
        break;

    case "visualizar":
        $stJs.= montaListaContratos();
        break;

    case "processarPopUp":
        $stDarEcho = true;
        $stJs.= processarPopUp();
        break;
}

if ($stJs) {
    if ($stDarEcho) {
        echo $stJs;
    } else {
        sistemaLegado::executaFrameOculto($stJs);
    }
}

?>
