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
    * Data de Criação: 14/02/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: André Almeida

    * @ignore

    $Revision: 30566 $
    $Name$
    $Author: rgarbin $
    $Date: 2008-04-03 10:54:36 -0300 (Qui, 03 Abr 2008) $

    * Casos de uso: uc-04.05.34
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroContrato.class.php"                                     );
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroCGMContrato.class.php"                                  );

//Define o nome dos arquivos PHP
$stPrograma = "ConsultarRegistroEventoComplementar";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
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
    $stJs .= "f.hdnFiltro.value                       = '".$stEval."';                      \n";
    $stJs .= "d.getElementById('inContrato').focus();";
    $stJs .= "d.getElementById('spnSpanLista').innerHTML = '';  \n";
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
    $stJs .= "f.hdnFiltro.value                       = '".$stEval."';                     \n";
    $stJs .= "d.getElementById('spnSpanLista').innerHTML = '';  \n";
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function preencheComplementarValorInicial()
{
    include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php" );
    include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaComplementar.class.php"   );

    $obPeriodoMovimentacao = new RFolhaPagamentoPeriodoMovimentacao;
    $obPeriodoMovimentacao->listarUltimaMovimentacao( $rsUltimaMovimentacao );
    $obPeriodoMovimentacao->setCodPeriodoMovimentacao( $rsUltimaMovimentacao->getCampo("cod_periodo_movimentacao") );

    $obFolhaComplementar = new RFolhaPagamentoFolhaComplementar( $obPeriodoMovimentacao );
    $obFolhaComplementar->listarFolhaComplementar( $rsFolhaComplementar );

    $stJs .= "limpaSelect(f.inCodComplementar,0); \n";
    $stJs .= "f.inCodComplementar[0] = new Option('Selecione','','selected');    \n";

    for ( $i=1 ; $i<=$rsFolhaComplementar->getNumLinhas() ; $i++  ) {
        $stJs .= "f.inCodComplementar[".$i."] = new Option('".$rsFolhaComplementar->getCampo("cod_complementar")."','".$rsFolhaComplementar->getCampo("cod_complementar")."','');    \n";
        $rsFolhaComplementar->proximo();
    }

    return $stJs;
}

function preencheComplementar()
{
    include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php" );
    include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaComplementar.class.php"   );

    $inAno = $_POST["inAno"];
    $stMes = $_POST["inCodMes"];
    if ( strlen($stMes) == 1 ) {
        $stMes = "0".$stMes;
    }

    $stJs .= "limpaSelect(f.inCodComplementar,0); \n";
    $stJs .= "f.inCodComplementar[0] = new Option('Selecione','','selected');    \n";

    if ($stMes) {
        $obPeriodoMovimentacao = new RFolhaPagamentoPeriodoMovimentacao;
        $obPeriodoMovimentacao->setDtFinal( $inAno."-".$stMes );
        $obPeriodoMovimentacao->listarPeriodoMovimentacao( $rsUltimaMovimentacao );

        if ( $rsUltimaMovimentacao->getNumLinhas() >= 1 ) {
            $obPeriodoMovimentacao->setCodPeriodoMovimentacao( $rsUltimaMovimentacao->getCampo("cod_periodo_movimentacao") );

            $obFolhaComplementar = new RFolhaPagamentoFolhaComplementar( $obPeriodoMovimentacao );
            $obFolhaComplementar->listarFolhaComplementar( $rsFolhaComplementar );
            $rsFolhaComplementar->setPrimeiroElemento();

            for ( $i=1 ; $i<=$rsFolhaComplementar->getNumLinhas() ; $i++  ) {
                $stJs .= "f.inCodComplementar[".$i."] = new Option('".$rsFolhaComplementar->getCampo("cod_complementar")."','".$rsFolhaComplementar->getCampo("cod_complementar")."','');    \n";
                $rsFolhaComplementar->proximo();
            }
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
        $obBtnOK->obEvento->setOnClick     ( "abrePopUpRegistrosEventos();" );
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
        $obBtnVisualizar->obEvento->setOnClick          ( "buscaValor('visualizar');" );
        $obFormulario->defineBarra ( array($obBtnVisualizar) );
    }
    $obFormulario->montaInnerHtml();
    $stJs .= "d.getElementById('spnSpanBotao').innerHTML = '".$obFormulario->getHTML()."';";

    return $stJs;
}

function montaListaContratos()
{
    $obErro = new erro;

    include_once ( CAM_GRH_FOL_NEGOCIO . "RFolhaPagamentoPeriodoMovimentacao.class.php"                            );
    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/Table.class.php';
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoCalculado.class.php"                                 );
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoRegistroEventoComplementar.class.php"                      );

    if (trim($_REQUEST['inCodComplementar'])=="") {
        $obErro->setDescricao("Campo *Complementar inválido!()");

        if ($obErro->ocorreu()) {
            $stJs .= "alertaAviso('@".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
        }
    } else {
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
        $obTFolhaPagamentoEventoComplementar = new TFolhaPagamentoRegistroEventoComplementar();
        if($rsEvento->getCampo("cod_evento") != ''){
             $stFiltro = " AND registro_evento_complementar.cod_evento = ".$rsEvento->getCampo("cod_evento");
        } else {
            $stFiltro = "";
        }
       
        $stFiltro .= " AND registro_evento_complementar.cod_periodo_movimentacao = ".$rsPeriodoMovimentacao->getCampo('cod_periodo_movimentacao');
        $stFiltro .= " AND registro_evento_complementar.cod_complementar = ".$_REQUEST['inCodComplementar'];

        $stOrdem = "  ORDER BY nom_cgm";
        $obTFolhaPagamentoEventoComplementar->recuperarRegistroContratoComplementarComPensionista( $rsLista, $stFiltro, $stOrdem);

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

        $obLista->Body->addAcao("consultar","executaFuncaoAjax('%s','&inContrato=%s&inCodMes=".$_REQUEST["inCodMes"]."&inAno=".$_REQUEST["inAno"]."&inCodComplementar=".$_REQUEST["inCodComplementar"]."&boOpcao=evento')",array('processarPopUp','matricula', 'inCodMes', 'inAno', 'inCodComplementar', 'boOpcao'));

        $obLista->montaHTML(true);
        $stJs = "d.getElementById('spnSpanLista').innerHTML = '".$obLista->getHtml()."';  \n";
    }

    return $stJs;
}

function processarPopUp()
{
    $stFiltros = "&inContrato=".$_GET['inContrato']."&inCodMes=".$_GET["inCodMes"]."&inAno=".$_GET["inAno"]."&inCodComplementar=".$_GET["inCodComplementar"];
    $stUrlFrame = CAM_GRH_FOL_POPUPS."folhaComplementar/FRConsultarRegistroEventoComplementar.php?sUrlConsulta=LSConsultarRegistroEventoComplementar.php?".Sessao::getId().$stFiltros;
    $stJs .=  "window.open('".$stUrlFrame."', 'popUpRegistrosEventos', 'width=800,height=550,resizable=1,scrollbars=1,left=0,top=0');";

    return $stJs;
}

$stDarEcho = false;
$stJs.= montaSpanBotao();
switch ($_REQUEST["stCtrl"]) {
    case "carregaValoresIniciais":
        $stJs .= preencheComplementarValorInicial();

    case "montaSpanContrato":
        $stJs .= montaSpanContrato();
        break;

    case "montaSpanCGMContrato":
        $stJs .= montaSpanCGMContrato();
        break;

    case "preencheComplementar":
        $stJs .= preencheComplementar();
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
