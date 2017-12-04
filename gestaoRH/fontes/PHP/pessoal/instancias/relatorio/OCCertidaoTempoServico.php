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
    * Página oculto para Relatório Certidão Tempo Serviço
    * Data de Criação   : 09/09/2008

    * @author Analista      Dagiane Vieira
    * @author Desenvolvedor Diego Lemos de Souza

    * @package URBEM
    * @subpackage

    * @ignore

    $Id:$
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "CertidaoTempoServico";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

function validarMatricula()
{
    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php");
    $obTPessoalContrato = new TPessoalContrato();
    $stFiltro = " and registro = ".$_GET["inContratoResponsavel"];
    $obTPessoalContrato->recuperaCgmDoRegistro($rsRecordset,$stFiltro);
    if ($rsRecordset->getNumLinhas() == -1) {
        $stJs .= "alertaAviso('A Matrícula ".$_GET['inContratoResponsavel']." não está ativa ou não existe.','form','erro','".Sessao::getId()."');\n";
        $stJs .= "jQuery('#inContratoResponsavel').val('');\n";
    }

    return $stJs;
}

function processarFormulario()
{
    $stJs = gerarSpanTipoCertidao();

    return $stJs;
}

function gerarSpanTipoCertidao()
{
    $stHtml = "";
    $obFormulario = new Formulario;
    switch ($_GET["stTipoCertidao"]) {
        case "inss":
        case "descritiva":
            $obForm = Sessao::read("obForm");

            include_once(CAM_GA_PROT_COMPONENTES."IPopUpProcesso.class.php");
            $obIPopUpProcesso = new IPopUpProcesso($obForm);
            $obIPopUpProcesso->setRotulo("Número do Processo");
            $obIPopUpProcesso->setTitle("Informe o número do processo, cadastrado no protocolo.");
            $obIPopUpProcesso->setNull(false);
            $obFormulario->addComponente($obIPopUpProcesso);
            break;
        case "completa":
        default:
            $obCBxDadosIdentificacao = new CheckBox();
            $obCBxDadosIdentificacao->setRotulo("Apresentar na Certidão");
            $obCBxDadosIdentificacao->setName("boDadosIdentificacao");
            $obCBxDadosIdentificacao->setLabel("Dados Identificação");
            $obCBxDadosIdentificacao->setNull(false);
            $obCBxDadosIdentificacao->setTitle("Marque/Desmarque as opções de dados da certidão.");
            $obCBxDadosIdentificacao->setChecked(true);
            $obCBxDadosIdentificacao->setValue(true);

            $obCBxGradeEfetividade = new CheckBox();
            $obCBxGradeEfetividade->setRotulo("Apresentar na Certidão");
            $obCBxGradeEfetividade->setName("boGradeEfetividade");
            $obCBxGradeEfetividade->setLabel("Grade Efetividade");
            $obCBxGradeEfetividade->setNull(false);
            $obCBxGradeEfetividade->setTitle("Marque/Desmarque as opções de dados da certidão.");
            $obCBxGradeEfetividade->setChecked(true);
            $obCBxGradeEfetividade->setValue(true);

            $obCBxHistoricoFuncional = new CheckBox();
            $obCBxHistoricoFuncional->setRotulo("Apresentar na Certidão");
            $obCBxHistoricoFuncional->setName("boHistoricoFuncional");
            $obCBxHistoricoFuncional->setLabel("Histórico Funcional");
            $obCBxHistoricoFuncional->setNull(false);
            $obCBxHistoricoFuncional->setTitle("Marque/Desmarque as opções de dados da certidão.");
            $obCBxHistoricoFuncional->setChecked(true);
            $obCBxHistoricoFuncional->setValue(true);

            $obCBxTotaisTempoServico = new CheckBox();
            $obCBxTotaisTempoServico->setRotulo("Apresentar na Certidão");
            $obCBxTotaisTempoServico->setName("boTotaisTempoServico");
            $obCBxTotaisTempoServico->setLabel("Totais Tempo Serviço");
            $obCBxTotaisTempoServico->setNull(false);
            $obCBxTotaisTempoServico->setTitle("Marque/Desmarque as opções de dados da certidão.");
            $obCBxTotaisTempoServico->setChecked(true);
            $obCBxTotaisTempoServico->setValue(true);

            $arCbx = array($obCBxDadosIdentificacao,$obCBxGradeEfetividade,$obCBxHistoricoFuncional,$obCBxTotaisTempoServico);

            $obRdoSimNao = new SimNao();
            $obRdoSimNao->setRotulo("Agrupar Certidões");
            $obRdoSimNao->setName("boAgruparCertidoes");
            $obRdoSimNao->setTitle("Marque como sim, para que as certidões filtradas do mesmo servidor sejam agrupadas em uma única, somando os tempos de cada ocorrência de matrícula.");
            $obRdoSimNao->setChecked("N");

            $obFormulario->agrupaComponentes($arCbx);
            $obFormulario->addComponente($obRdoSimNao);
            break;
    }
    $obFormulario->montaInnerHTML();
    $obFormulario->obJavaScript->montaJavaScript();

    $stJs  = "jQuery('#spnTipoCertidao').html('".$obFormulario->getHTML()."');\n";
    $stJs .= "jQuery('#hdnTipoCertidao').val('".$obFormulario->obJavaScript->getInnerJavaScript()."');\n";

    return $stJs;
}

function gerarSpanOrdenacaoMatricula()
{
    $stHtml = "";
    $obFormulario = new Formulario;

    if ($_GET["stTipoFiltro"] == "contrato_todos" || $_GET["stTipoFiltro"] == "cgm_contrato_todos") {
        $obRadioOrdenacaoAlfabetica = new Radio();
        $obRadioOrdenacaoAlfabetica->setRotulo("Ordenação");
        $obRadioOrdenacaoAlfabetica->setName("stOrdenacaoMatricula");
        $obRadioOrdenacaoAlfabetica->setLabel("Alfabética");
        $obRadioOrdenacaoAlfabetica->setTitle("Clique para selecionar a ordenação. alfabética por nome ou numérica por matrícula");
        $obRadioOrdenacaoAlfabetica->setChecked(true);
        $obRadioOrdenacaoAlfabetica->setValue("ALFABETICA");

        $obRadioOrdenacaoNumerica = new Radio();
        $obRadioOrdenacaoNumerica->setRotulo("Ordenação");
        $obRadioOrdenacaoNumerica->setName("stOrdenacaoMatricula");
        $obRadioOrdenacaoNumerica->setLabel("Numérica");
        $obRadioOrdenacaoNumerica->setTitle("Clique para selecionar a ordenação. alfabética por nome ou numérica por matrícula");
        $obRadioOrdenacaoNumerica->setChecked(false);
        $obRadioOrdenacaoNumerica->setValue("NUMERICA");

        $arRadio = array($obRadioOrdenacaoAlfabetica,$obRadioOrdenacaoNumerica);
        $obFormulario->agrupaComponentes($arRadio);

        $obFormulario->montaInnerHTML();
        $obFormulario->obJavaScript->montaJavaScript();

        $stJs  = "jQuery('#spnOrdenacaoMatricula').html('".$obFormulario->getHTML()."');\n";
    } else {
        $stJs  = "jQuery('#spnOrdenacaoMatricula').html('');\n";
    }

    return $stJs;
}

switch ($_GET["stCtrl"]) {
    case "validarMatricula":
        $stJs = validarMatricula();
        break;
    case "gerarSpanTipoCertidao":
        $stJs = gerarSpanTipoCertidao();
        break;
    case "processarFormulario":
        $stJs = processarFormulario();
        break;
    case "gerarSpanOrdenacaoMatricula":
        $stJs = gerarSpanOrdenacaoMatricula();
        break;
}

if ($stJs) {
    echo $stJs;
}

?>
