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
    * Página de Oculto do Conceder de 13º Salário
    * Data de Criação: 14/09/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30711 $
    $Name$
    $Author: souzadl $
    $Date: 2008-03-26 17:16:54 -0300 (Qua, 26 Mar 2008) $

    * Casos de uso: uc-04.05.24
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GRH_PES_COMPONENTES."IFiltroContrato.class.php"                                       );
include_once( CAM_GRH_PES_COMPONENTES."IFiltroCGMContrato.class.php"                                    );
include_once( CAM_GRH_PES_COMPONENTES."ISelectMultiploLotacao.class.php"                                );
include_once( CAM_GRH_PES_COMPONENTES."ISelectMultiploLocal.class.php"                                  );
include_once( CAM_GRH_PES_COMPONENTES."ISelectMultiploRegSubCarEsp.class.php"                           );

//Define o nome dos arquivos PHP
$stPrograma = "ConcederDecimo";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

function mudaAction($stPagina1,$stPagina2)
{
    $stJs .= "f.action = f.action.replace('".$stPagina1."','".$stPagina2."');    \n";

    return $stJs;
}

function gerarSpanPercentualAdiantamento()
{
    $obFormulario = new Formulario;

    $obPorcentagem = new Porcentagem();
    $obPorcentagem->setRotulo("Percentual para Pagamento");
    $obPorcentagem->setName("nuPercentualPagamento");
    $obPorcentagem->setValue("50");
    $obPorcentagem->setTitle("Informe o percentual do adiantamento do 13º salário.");

    $obCkbGerarSomenteVantagem = new CheckBox();
    $obCkbGerarSomenteVantagem->setRotulo("Gerar Somente Vantagens Fixas");
    $obCkbGerarSomenteVantagem->setName("boGerarSomenteVantagem");
    $obCkbGerarSomenteVantagem->setValue("true");
    $obCkbGerarSomenteVantagem->setTitle("Gerar somente vantagens (eventos) fixas.");

    $obFormulario->addComponente($obPorcentagem);
    $obFormulario->addComponente($obCkbGerarSomenteVantagem);
    addLancamentoFolha($obFormulario);
    $obFormulario->montaInnerHTML();
    $stHtml       = $obFormulario->getHTML();
    $stJs .= "d.getElementById('spnPercentualAdiantamento').innerHTML = '$stHtml';   \n";

    return $stJs;
}

function addLancamentoFolha(&$obFormulario)
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoFolhaSituacao.class.php");
    $obTFolhaPagamentoFolhaSituacao = new TFolhaPagamentoFolhaSituacao();
    $obTFolhaPagamentoFolhaSituacao->recuperaUltimaFolhaSituacao($rsSituacaoFolha);

    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConcessaoDecimo.class.php");
    include_once(CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoConfiguracao.class.php");
    $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
    $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsUltimaMovimentacao);
    $arMesCompetencia = explode("/",$rsUltimaMovimentacao->getCampo("dt_final"));
    $inMesCompetencia = (float) $arMesCompetencia[1];
    $obRFolhaPagamentoConfiguracao = new RFolhaPagamentoConfiguracao();
    $obRFolhaPagamentoConfiguracao->consultar();
    $inMesCalculoDecimo = $obRFolhaPagamentoConfiguracao->getMesCalculoDecimo();

    if ($_GET['stAcao'] == "inserir") {
        $stRotulo = "Pagamento na Folha";
        $stTitle = "Lançar o adiantamento de décimo terceiro na folha de décimo terceiro ou na folha salário.";
    }
    if ($_GET['stAcao'] == "cancelar") {
        $stRotulo = "Cancelar Lançamento na Folha";
        $stTitle = "Cancelar lançamento do adiantamento de décimo terceiro na folha de décimo terceiro ou na folha salário.";
    }

    $obRdoPagEmFolhaDecimo = new Radio();
    $obRdoPagEmFolhaDecimo->setRotulo($stRotulo);
    $obRdoPagEmFolhaDecimo->setName("boPagEmFolhaSalario");
    $obRdoPagEmFolhaDecimo->setLabel("13º Salário");
    $obRdoPagEmFolhaDecimo->setValue("false");
    $obRdoPagEmFolhaDecimo->setTitle($stTitle);
    $obRdoPagEmFolhaDecimo->setChecked(true);

    $obRdoPagEmFolhaSalario = new Radio();
    $obRdoPagEmFolhaSalario->setRotulo($stRotulo);
    $obRdoPagEmFolhaSalario->setName("boPagEmFolhaSalario");
    $obRdoPagEmFolhaSalario->setLabel("Salário");
    $obRdoPagEmFolhaSalario->setValue("true");
    $obRdoPagEmFolhaSalario->setTitle($stTitle);
    if ($rsSituacaoFolha->getCampo("situacao") == "f" || $inMesCompetencia >= $inMesCalculoDecimo) {
        $obRdoPagEmFolhaSalario->setDisabled(true);
    }

    $obFormulario->agrupaComponentes(array($obRdoPagEmFolhaDecimo,$obRdoPagEmFolhaSalario));
}

function processarFiltro()
{
    if ($_REQUEST['stAcao'] == "inserir") {
        $stJs = preencheFiltrar();
    }
    if ($_REQUEST['stAcao'] == "cancelar") {
        $stJs = preencheFiltrarCancelar();
    }

    return $stJs;
}

function preencheFiltrarCancelar()
{
    $obFormulario = new Formulario;

    addLancamentoFolha($obFormulario);
    $obFormulario->montaInnerHTML();
    $stHtml       = $obFormulario->getHTML();
    $stJs .= "d.getElementById('spnPercentualAdiantamento').innerHTML = '$stHtml';   \n";

    return $stJs;
}

function preencheFiltrar()
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConcessaoDecimo.class.php");
    include_once(CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoConfiguracao.class.php");
    $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
    $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsUltimaMovimentacao);
    $arMesCompetencia = explode("/",$rsUltimaMovimentacao->getCampo("dt_final"));
    $inMesCompetencia = (float) $arMesCompetencia[1];
    $obTFolhaPagamentoConcessaoDecimo = new TFolhaPagamentoConcessaoDecimo();
    $stFiltro .= " WHERE desdobramento = 'D'";
    $obTFolhaPagamentoConcessaoDecimo->recuperaTodos($rsConcessaoDecimo,$stFiltro);
    $obRFolhaPagamentoConfiguracao = new RFolhaPagamentoConfiguracao();
    $obRFolhaPagamentoConfiguracao->consultar();
    $inMesCalculoDecimo = $obRFolhaPagamentoConfiguracao->getMesCalculoDecimo();

    if ($inMesCompetencia == $inMesCalculoDecimo) {
        $stJs .= "f.stDesdobramento[1].disabled = false;\n";
        $stJs .= "f.stDesdobramento[1].checked = true;\n";
    }
    if ($inMesCompetencia >= 1 and $inMesCompetencia < $inMesCalculoDecimo) {
        $stJs .= "f.stDesdobramento[2].disabled = false;\n";
        $stJs .= "f.stDesdobramento[2].checked = true;\n";
        $stJs .= gerarSpanPercentualAdiantamento();
    }
    if ( $inMesCompetencia == 12 and $rsConcessaoDecimo->getNumLinhas() > 0 ) {
        $stJs .= "f.stDesdobramento[0].disabled = false;\n";
        $stJs .= "f.stDesdobramento[0].checked = true;\n";
    }

    return $stJs;
}

function limparFiltro()
{
    $stJs .= processarFiltro();

    return $stJs;
}

function preencherSpanLista()
{
    if ($_GET['stOpcao'] == "T4") {
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoUltimoRegistroEventoDecimo.class.php");
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
        $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao;
        $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsUltimaMovimentacao);
        $obTFolhaPagamentoUltimoRegistroEventoDecimo = new TFolhaPagamentoUltimoRegistroEventoDecimo();
        $obTFolhaPagamentoUltimoRegistroEventoDecimo->setDado("desdobramento","D");
        $obTFolhaPagamentoUltimoRegistroEventoDecimo->setDado("cod_periodo_movimentacao",$rsUltimaMovimentacao->getCampo("cod_periodo_movimentacao"));
        $obTFolhaPagamentoUltimoRegistroEventoDecimo->recuperaContratosDeRegistrosDeEventoSemCalculo($rsContratos);

        $obLista = new Lista;
        $obLista->setTitulo("Matrículas não Calculadas");
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

        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento("DIREITA");
        $obLista->ultimoDado->setCampo( "[registro]" );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento("ESQUERDA");
        $obLista->ultimoDado->setCampo( "[numcgm]-[nom_cgm]" );
        $obLista->commitDado();

        $obLista->montaHTML();
        $stHtml = $obLista->getHTML();
        $stHtml = str_replace("\n","",$stHtml);
        $stHtml = str_replace("  ","",$stHtml);
        $stHtml = str_replace("'","\\'",$stHtml);

        $stJs .= "d.getElementById('spnSpan1').innerHTML = '".$stHtml."';   \n";
    }
    if ($_GET['stOpcao'] == "T3") {
        $stJs .= montaListaConcessao();
    }

    return $stJs;
}

function montaListaConcessao()
{
    if ( $_GET['stRegistrados'] == "sim" or !isset($_GET['stRegistrados']) ) {
        $rsContratos = new RecordSet();
        $rsContratos->preenche(Sessao::read('arContratos'));
        $rsContratos->ordena("registro");
        $obLista = new Lista;
        $obLista->setTitulo("Concessões Realizadas com Sucesso");
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

        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento("DIREITA");
        $obLista->ultimoDado->setCampo( "[registro]" );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento("ESQUERDA");
        $obLista->ultimoDado->setCampo( "[numcgm]-[nom_cgm]" );
        $obLista->commitDado();

        $obLista->montaHTML();
        $stHtml = $obLista->getHTML();
        $stHtml = str_replace("\n","",$stHtml);
        $stHtml = str_replace("  ","",$stHtml);
        $stHtml = str_replace("'","\\'",$stHtml);
    } else {
        $rsContratosErro = new RecordSet();
        $rsContratosErro->preenche(Sessao::read('arContratosErro'));
        $rsContratosErro->ordena("registro");
        $obLista = new Lista;
        $obLista->setTitulo("Concessões não Realizadas");
        $obLista->setRecordSet( $rsContratosErro );
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
        $obLista->ultimoCabecalho->addConteudo("Motivo");
        $obLista->ultimoCabecalho->setWidth( 20 );
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
        $obLista->ultimoDado->setCampo( "motivo" );
        $obLista->commitDado();

        $obLista->montaHTML();
        $stHtml = $obLista->getHTML();
        $stHtml = str_replace("\n","",$stHtml);
        $stHtml = str_replace("  ","",$stHtml);
        $stHtml = str_replace("'","\\'",$stHtml);

        $obBtnOk = new ok();
        $obBtnOk->setValue                      ( "Imprimir"                );

        $obFormulario = new Formulario;
        $obFormulario->defineBarra              ( array($obBtnOk),"",""     );
        $obFormulario->obJavaScript->montaJavaScript();
        $obFormulario->montaInnerHtml();

        $stHtml .= $obFormulario->getHtml();

        $stJs .= "f.stCaminho.value = '".CAM_GRH_FOL_INSTANCIAS."decimo/PRRelatorioConcessoesNaoRealizadas.php'; \n";

    }
    $stJs .= "d.getElementById('spnSpan1').innerHTML = '".$stHtml."';   \n";

    return $stJs;
}

function excluir()
{
    global $pgProc;
    Sessao::write("boPagEmFolhaSalario",$_GET["boPagEmFolhaSalario"]);
    switch ($_GET['stTipoFiltro']) {
        case "lotacao":
            Sessao::write("arCodLotacao",$_GET["inCodLotacaoSelecionados"]);
        case "local":
            Sessao::write("arCodLocal",$_GET["inCodLocalSelecionados"]);
        case "reg_sub_fun_esp":
            Sessao::write("arCodRegime",$_GET["inCodRegimeSelecionadosFunc"]);
            Sessao::write("arCodSubDivisao",$_GET["inCodSubDivisaoSelecionadosFunc"]);
            Sessao::write("arCodFuncao",$_GET["inCodFuncaoSelecionados"]);
            Sessao::write("arCodEspecialidade",$_GET["inCodEspecialidadeSelecionadosFunc"]);
        case "geral":
            $stID = str_replace("&","*_*", Sessao::getId());
            $stJs .= "if ( Valida() ) {       \n";
            $stJs .= "     alertaQuestao('".CAM_GRH_FOL_INSTANCIAS."decimo/".$pgProc."?".$stID."*_*stAcao=cancelar*_*stTipoFiltro=".$_GET['stTipoFiltro']."*_*stDescQuestao=Confirma a exclusão da concessão do 13º Salário','sn_excluir','');  \n";
            $stJs .= "}                     \n";
            break;
        default:
            $stJs .= "parent.frames[2].Salvar();    \n";
            break;
    }

    return $stJs;
}

function submeter()
{
    $stJs .= "BloqueiaFrames(true,false);";
    $stJs .= "jQuery('#showLoading',parent.frames[2].document).css('width','500px');";
    $stJs .= "jQuery('#showLoading',parent.frames[2].document).css('margin','-25px 0px 0px -250px;');";
    $stJs .= "parent.frames[2].Salvar();\n";

    return $stJs;
}

switch ($_GET['stCtrl']) {
    case "submeter":
        $stJs = submeter();
        break;
    case "habilitaSpanFiltro":
        $stJs .= habilitaSpanFiltro();
        break;
    case "processarFiltro":
        $stJs .= processarFiltro();
        break;
    case "limparFiltro":
        $stJs .= limparFiltro();
        break;
    case "excluir":
        $stJs .= excluir();
        break;
    case "preencheFiltrar":
        $stJs .= preencheFiltrar();
        break;
    case "preencherSpanLista":
        $stJs .= preencherSpanLista();
        break;
    case "montaListaConcessao":
        $stJs .= montaListaConcessao();
        break;
}

if ($stJs) {
    echo $stJs;
}

?>
