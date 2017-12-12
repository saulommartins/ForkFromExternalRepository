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
    * Data de Criação: 17/07/2007

    * @author Analista: Dagiane Vieira
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 31094 $
    $Name$
    $Author: souzadl $
    $Date: 2007-09-18 10:38:42 -0300 (Ter, 18 Set 2007) $

    * Casos de uso: uc-04.05.62
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkDB.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';
include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoEmpenho.class.php" );

function gerarSpanOpcoes()
{
    Sessao::write('arContratos',"");
    switch ($_GET["stOpcoes"]) {
        case "matricula":
            $stHtml  = gerarSpanMatricula();
            break;
        case "cgm_matricula":
            $stHtml = gerarSpanCGMMatrituca();
            break;
        case "lotacao":
            $stHtml = gerarSpanLotacao($stEval);
            break;
        case "local":
            $stHtml = gerarSpanLocal($stEval);
            break;
        case "atributo":
            $stHtml = gerarSpanAtributo($stEval);
            break;
        case "geral":
            $stHtml = "";
            $stEval = "";
            break;
        default:
            $stJs = "f.stOpcoes.value = '';";
            $stHtml = "";
            $stEval = "";
            break;
    }
    $stJs  = "d.getElementById('spnOpcoes').innerHTML = '$stHtml';\n";
    $stJs .= "f.hdnOpcoes.value = '$stEval';\n";

    return $stJs;
}

function gerarSpanMatricula()
{
    include_once(CAM_GRH_PES_COMPONENTES."IFiltroContrato.class.php");
    $obIFiltroContrato = new IFiltroContrato();
    $obIFiltroContrato->setTituloFormulario("Matrícula");

    $obFormulario = new Formulario;
    $obIFiltroContrato->geraFormulario($obFormulario);
    $obFormulario = addDemaisComponenteMatricula($obFormulario,"_incluirMatricula");
    $obFormulario->montaInnerHTML();

    return $obFormulario->getHTML();
}

function gerarSpanCGMMatrituca()
{
    include_once(CAM_GRH_PES_COMPONENTES."IFiltroCGMContrato.class.php");
    $obIFiltroCGMContrato = new IFiltroCGMContrato();
    $obIFiltroCGMContrato->setTituloFormulario("CGM/Matrícula");

    $obFormulario = new Formulario;
    $obIFiltroCGMContrato->geraFormulario($obFormulario);
    $obFormulario = addDemaisComponenteMatricula($obFormulario,"__incluirMatricula");
    $obFormulario->montaInnerHTML();

    return $obFormulario->getHTML();
}

function addDemaisComponenteMatricula($obFormulario,$stFuncao)
{
    $onBtnIncluir = new Button();
    $onBtnIncluir->setName("obBtnIncluir");
    $onBtnIncluir->setValue("Incluir");
    $onBtnIncluir->setTipo("button");
    $onBtnIncluir->setDisabled(false);
    $onBtnIncluir->obEvento->setOnClick("montaParametrosGET('$stFuncao','inContrato',true);");

    $obSpnMatriculas = new Span();
    $obSpnMatriculas->setId("spnMatriculas");

    $obFormulario->defineBarra(array($onBtnIncluir),'','');
    $obFormulario->addSpan($obSpnMatriculas);

    return $obFormulario;
}

function gerarSpanLotacao(&$stEval)
{
    include_once ( CAM_GRH_PES_COMPONENTES.'ISelectMultiploLotacao.class.php' );
    $obISelectMultiploLotacao = new ISelectMultiploLotacao;
    $obISelectMultiploLotacao->setNull(false);

    $obFormulario = new Formulario;
    $obFormulario->addTitulo("Lotação");
    $obFormulario->addComponente($obISelectMultiploLotacao);
    $obFormulario->montaInnerHTML();
    $obFormulario->obJavaScript->montaJavaScript();
    $stEval = $obFormulario->obJavaScript->getInnerJavaScript();

    return $obFormulario->getHTML();
}

function gerarSpanLocal(&$stEval)
{
    include_once ( CAM_GRH_PES_COMPONENTES.'ISelectMultiploLocal.class.php' );
    $obISelectMultiploLocal   = new ISelectMultiploLocal;
    $obISelectMultiploLocal->setNull(false);

    $obFormulario = new Formulario;
    $obFormulario->addTitulo("Local");
    $obFormulario->addComponente($obISelectMultiploLocal);
    $obFormulario->montaInnerHTML();
    $obFormulario->obJavaScript->montaJavaScript();
    $stEval = $obFormulario->obJavaScript->getInnerJavaScript();

    return $obFormulario->getHTML();
}

function gerarSpanAtributo(&$stEval)
{
    include_once ( CAM_GA_ADM_NEGOCIO."RCadastroDinamico.class.php" );
    $obRCadastroDinamico = new RCadastroDinamico;
    $obRCadastroDinamico->obRModulo->setCodModulo(22);
    if ($_GET["stSituacao"] == "e") {
        $obRCadastroDinamico->setCodCadastro(7);
    } else {
        $obRCadastroDinamico->setCodCadastro(5);
    }
    $obRCadastroDinamico->recuperaAtributosSelecionados( $rsAtributos );

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

    $arComponentesAtributos = array($obCmbAtributo);

    $obFormulario = new Formulario;
    $obFormulario->addTitulo("Atributo Dinâmico");
    $obFormulario->addComponente($obCmbAtributo);
    $obFormulario->addSpan($obSpnAtributo);
    $obFormulario->montaInnerHTML();
    $obFormulario->obJavaScript->montaJavaScript();
    $stEval = $obFormulario->obJavaScript->getInnerJavaScript();

    return $obFormulario->getHTML();
}

function gerarSpanAtributosDinamicos($inCodAtributo="",$stValor="")
{
    $inCodAtributo = ($_REQUEST['inCodAtributo'] != "") ? $_REQUEST['inCodAtributo'] : $inCodAtributo;
    if ($inCodAtributo != "") {
        $rsAtributos = new RecordSet();
        include_once(CAM_GRH_PES_NEGOCIO."RPessoalServidor.class.php");
        $obRPessoalServidor = new RPessoalServidor();
        $obRPessoalServidor->addContratoServidor();
        $obRPessoalServidor->roUltimoContratoServidor->obRCadastroDinamico->setChavePersistenteValores( array('cod_atributo'=>$inCodAtributo) );
        $obRPessoalServidor->roUltimoContratoServidor->obRCadastroDinamico->recuperaAtributosSelecionados( $rsAtributos );

        if ($stValor!="") {
            $rsAtributos->setCampo("valor",$stValor,true);
            if (is_array($stValor)) {
                $stJs2 .= "var array = new Array();                         \n";
                $stJs2 .= "var campo= f.Atributo_".$inCodAtributo."_5_Disponiveis;\n";
                $stJs2 .= "var tam = campo.length;                          \n";
                foreach ($stValor as $inIndex=>$stTemp) {
                    $stJs2 .= "array[".$inIndex."] = '".$stTemp."';         \n";
                }
                $stJs2 .= "for (var i=0 ;i<tam;i++) {                         \n";
                $stJs2 .= "    for (var j=0 ;j<tam;j++) {                     \n";
                $stJs2 .= "        if (campo.options[i].value == array[j]) {  \n";
                $stJs2 .= "            campo.options[i].selected = true;    \n";
                $stJs2 .= "        }                                        \n";
                $stJs2 .= "    }                                            \n";
                $stJs2 .= "}                                                \n";
                $stJs2 .= "passaItem('document.frm.Atributo_".$inCodAtributo."_5_Disponiveis','document.frm.Atributo_".$inCodAtributo."_5_Selecionados','selecao');";
            }
        }

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
        $stJs = "f.hdnOpcoes.value='".$obFormulario->getInnerJavaScript()."';";
    }
    $stJs .= "d.getElementById('spnAtributo').innerHTML = '$stHtml';   \n";
    $stJs .= $stJs2;

    return $stJs;
}

function gerarSpanFolhaComplementar()
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoComplementar.class.php");
    $obTFolhaPagamentoComplementar = new TFolhaPagamentoComplementar();
    $obTFolhaPagamentoComplementar->recuperaTodos($rsFolhaComplementar);

    $obCmbFolhaComplementar = new Select;
    $obCmbFolhaComplementar->setRotulo      ( "Folha Complementar"              );
    $obCmbFolhaComplementar->setTitle       ( "Selecione a folha complementar." );
    $obCmbFolhaComplementar->setName        ( "inCodComplementar"               );
    $obCmbFolhaComplementar->setValue       ( $inCodComplementar                );
    $obCmbFolhaComplementar->setStyle       ( "width: 200px"                    );
    $obCmbFolhaComplementar->addOption      ( "", "Selecione"                   );
    $obCmbFolhaComplementar->setCampoID     ( "[cod_complementar]"              );
    $obCmbFolhaComplementar->setCampoDesc   ( "[cod_complementar]"              );
    $obCmbFolhaComplementar->setNull        ( false                             );
    $obCmbFolhaComplementar->preencheCombo  ( $rsFolhaComplementar              );

    $obFormulario = new Formulario;
    $obFormulario->addComponente            ( $obCmbFolhaComplementar           );
    $obFormulario->obJavaScript->montaJavaScript();
    $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
    $obFormulario->montaInnerHtml();
    $stJs  = "d.getElementById('spnFiltrarFolhas').innerHTML = '".$obFormulario->getHTML()."';    \n";
    $stJs .= "f.hdnFiltrarFolhas.value = '$stEval';\n";

    return $stJs;
}

function gerarSpanFolhas()
{
    include_once(CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoEvento.class.php");
    $obRFolhaPagamentoEvento = new RFolhaPagamentoEvento;
    $obRFolhaPagamentoEvento->addConfiguracaoEvento();
    $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->listarConfiguracaoEvento($rsConfiguracaoEvento);
    $obCmbTipoCalculo = new Select;
    $obCmbTipoCalculo->setRotulo     ( "Tipo de Cálculo"              );
    $obCmbTipoCalculo->setTitle      ( "Selecione o tipo de cálculo." );
    $obCmbTipoCalculo->setName       ( "inCodConfiguracao"            );
    $obCmbTipoCalculo->setValue      ( $inCodConfiguracao             );
    $obCmbTipoCalculo->setStyle      ( "width: 200px"                 );
    $obCmbTipoCalculo->addOption     ( "", "Selecione"                );
    $obCmbTipoCalculo->setCampoID    ( "[cod_configuracao]"           );
    $obCmbTipoCalculo->setCampoDesc  ( "[descricao]"                  );
    $obCmbTipoCalculo->preencheCombo ( $rsConfiguracaoEvento          );
    $obCmbTipoCalculo->setNull       ( false                          );

    $obFormulario = new Formulario;
    $obFormulario->addComponente     ( $obCmbTipoCalculo );
    $obFormulario->obJavaScript->montaJavaScript();
    $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
    $obFormulario->montaInnerHtml();

    $stJs  = "d.getElementById('spnFiltrarFolhas').innerHTML = '".$obFormulario->getHTML()."';    \n";
    $stJs .= "f.hdnFiltrarFolhas.value = '$stEval';\n";

    return $stJs;
}

function gerarSpanComplementar()
{
    if ($_GET["boFiltrarComplementar"]) {
        $stJs = gerarSpanFolhaComplementar();
    } else {
        $stJs = gerarSpanFolhas();
    }

    return $stJs;
}

function gerarSpans()
{
    $stJs = gerarSpanFolhas();

    return $stJs;
}

function gerarSpanResumoEmissaoAutorizacaoEmpenho()
{
    $rsLista = new RecordSet();
    $arLista = (is_array(Sessao::read("arEmissaoEmpenho"))) ? Sessao::read("arEmissaoEmpenho") : array();
    $rsLista->preenche($arLista);
    $rsLista->addFormatacao("valor","NUMERIC_BR");

    $obLista = new TableTree();
    $obLista->setRecordset($rsLista);
    $obLista->setArquivo(CAM_GRH_FOL_INSTANCIAS."rotinaMensal/DTEmitirAutorizacaoEmpenho.php");
    $obLista->setParametros(array("num_pao","desc_pao","lla","evento"));
    $obLista->setSummary("Resumo para Emissão das Autorizações de Empenho");

    $obLista->Head->addCabecalho( 'Órgão Orçamentário'      , 20  );
    $obLista->Head->addCabecalho( 'Unidade Orçamentária'    , 20  );
    $obLista->Head->addCabecalho( 'Red Dotação'             , 8  );
    $obLista->Head->addCabecalho( 'Saldo Dotação'           , 10  );
    $obLista->Head->addCabecalho( 'Rública Despesa'         , 15  );
    $obLista->Head->addCabecalho( 'Valor'                   , 10  );

    $obLista->Body->addCampo( 'orgao', 'E' );
    $obLista->Body->addCampo( 'unidade', 'E' );
    $obLista->Body->addCampo( 'red_dotacao', 'C' );
    $obLista->Body->addCampo( 'saldo_dotacao', 'D' );
    $obLista->Body->addCampo( 'rubrica_despesa', 'E' );
    $obLista->Body->addCampo( 'valor', 'D' );

    $obLista->Body->addAcao("excluir","executaFuncaoAjax('%s','&inId=%s')",array('excluirAutorizacao','inId'));

    $obLista->Foot->addSoma("valor","D");

    $obLista->montaHTML(true);
    $stHtml = $obLista->getHtml();

    $stJs  = "d.getElementById('spnResumoEmissaoAutorizacoesEmpenho').innerHTML = '$stHtml';    \n";

    return $stJs;
}

function gerarSpanResumoEmissaoAutorizacaoEmpenhoDiarias()
{
    $rsLista = new RecordSet();
    $arLista = (is_array(Sessao::read("arEmissaoEmpenho"))) ? Sessao::read("arEmissaoEmpenho") : array();
    $rsLista->preenche($arLista);
    $rsLista->addFormatacao("valor","NUMERIC_BR");

    $obLista = new TableTree();
    $obLista->setRecordset($rsLista);
    $obLista->setArquivo(CAM_GRH_FOL_INSTANCIAS."rotinaMensal/DTEmitirAutorizacaoEmpenhoDiarias.php");
    $obLista->setParametros(array("rubrica_despesa","red_dotacao","cargo","descricao_despesa"));
    $obLista->setSummary("Resumo para Emissão das Autorizações de Empenho");

    $obLista->Head->addCabecalho( 'Fornecedor' , 40  );
    $obLista->Head->addCabecalho( 'Ato' , 10  );
    $obLista->Head->addCabecalho( 'Período da Viagem' , 30  );
    $obLista->Head->addCabecalho( 'Valor' , 15  );

    $obLista->Body->addCampo( 'fornecedor', 'E' );
    $obLista->Body->addCampo( 'ato', 'E' );
    $obLista->Body->addCampo( 'periodo', 'C' );
    $obLista->Body->addCampo( 'valor', 'D' );

    $obLista->Body->addAcao("excluir","executaFuncaoAjax('%s','&inId=%s')",array('excluirAutorizacaoDiarias','inId'));

    $obLista->Foot->addSoma("valor","D");

    $obLista->montaHTML(true);
    $stHtml = $obLista->getHtml();

    $stJs  = "d.getElementById('spnResumoEmissaoAutorizacoesEmpenho').innerHTML = '$stHtml';    \n";

    return $stJs;
}

function excluir()
{
    $arEmissoesEmpenho = array();
    foreach (Sessao::read("arEmissaoEmpenho") as $arEmissaoEmpenho) {
        if ($arEmissaoEmpenho["inId"] != $_GET["inId"]) {
            $arEmissaoEmpenho["inId"] = count($arEmissoesEmpenho)+1;
            $arEmissoesEmpenho[] = $arEmissaoEmpenho;
        }
    }
    Sessao::write("arEmissaoEmpenho",$arEmissoesEmpenho);
}

function excluirAutorizacao()
{
    excluir();
    $stJs .= gerarSpanResumoEmissaoAutorizacaoEmpenho();

    return $stJs;
}

function excluirAutorizacaoDiarias()
{
    excluir();
    $stJs .= gerarSpanResumoEmissaoAutorizacaoEmpenhoDiarias();

    return $stJs;
}

function submeter()
{
    $stId = str_replace("&","*_*",Sessao::getId());
    $stJs = "alertaQuestao('".CAM_GRH_FOL_INSTANCIAS."rotinaMensal/PREmitirAutorizacaoEmpenho.php?$stId*_*stAcao=".$_GET["stAcao"]."*_*stDescQuestao=Confirma a emissão das autorizações de empenho? Caso seja necessário excluí-las, este procedimento deverá ser feito manualmente através do módulo de empenho.','sn_excluir','".Sessao::getId()."');\n";

    return $stJs;
}

function _incluirMatricula()
{
    $stJs  = incluirMatricula();
    $stJs .= "f.inContrato.value = '';                      \n";
    $stJs .= "d.getElementById('inNomCGM').innerHTML = '&nbsp;';  \n";

    return $stJs;
}

function __incluirMatricula()
{
    $stJs  = incluirMatricula();
    $stJs .= "f.inNumCGM.value = '';                            \n";
    $stJs .= "d.getElementById('inCampoInner').innerHTML = '&nbsp;';  \n";
    $stJs .= "limpaSelect(f.inContrato,0);                      \n";

    return $stJs;
}

function incluirMatricula()
{
    $obErro    = new erro;
    if ( !$obErro->ocorreu() and $_GET['inContrato'] == "") {
        $obErro->setDescricao("Campo Matrícula inválido!()");
    }
    if ( !$obErro->ocorreu() ) {
        $arContratos = ( is_array(Sessao::read('arContratos')) ) ? Sessao::read('arContratos') : array();
        foreach ($arContratos as $arContrato) {
            if ($arContrato['inContrato'] == $_GET['inContrato']) {
                $obErro->setDescricao("Matrícula ".$_GET['inContrato']." já inserida na lista.");
                break;
            }
        }
    }
    if ( !$obErro->ocorreu() ) {
        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php");
        $obTPessoalContrato = new TPessoalContrato;
        $stFiltro = " AND registro = ".$_GET['inContrato'];
        $obTPessoalContrato->recuperaCgmDoRegistro($rsCGM,$stFiltro);
        $arContrato                             = array();
        $arContrato['inId']                     = count($arContratos);
        $arContrato['inContrato']               = $_GET['inContrato'];
        $arContrato['cod_contrato']             = $rsCGM->getCampo("cod_contrato");
        $arContrato['numcgm']                   = $rsCGM->getCampo("numcgm");
        $arContrato['nom_cgm']                  = $rsCGM->getCampo("nom_cgm");
        $arContratos[]        = $arContrato;
        Sessao::write("arContratos",$arContratos);
        $stJs .= montaListaContratos();
    } else {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    }

    return $stJs;
}

function excluirMatricula()
{
    $arContratos = ( is_array(Sessao::read('arContratos')) ? Sessao::read('arContratos') : array());
    $arTemp = array();
    foreach ($arContratos as $arContrato) {
        if ($arContrato['inId'] != $_GET['inId']) {
            $inId = sizeof($arTemp);
            $arEvento['inId'] = $inId;
            $arTemp[] = $arContrato;
        }
    }
    Sessao::write("arContratos",$arTemp);
    $stJs .= montaListaContratos();

    return $stJs;
}

function montaListaContratos()
{
    $rsLista = new RecordSet();
    $arLista = ( is_array(Sessao::read('arContratos')) ? Sessao::read('arContratos') : array());;
    $rsLista->preenche($arLista);

    $obLista = new Table();
    $obLista->setRecordset($rsLista);
    $obLista->setSummary("Matrículas");

    $obLista->Head->addCabecalho( 'Matrícula' , 20  );
    $obLista->Head->addCabecalho( 'CGM' , 20  );

    $obLista->Body->addCampo( 'inContrato', 'E' );
    $obLista->Body->addCampo( '[numcgm]-[nom_cgm]', 'E' );

    $obLista->Body->addAcao("excluir","executaFuncaoAjax('%s','&inId=%s')",array('excluirMatricula','inId'));

    $obLista->montaHTML(true);
    $stHtml = $obLista->getHtml();

    $stJs .= "d.getElementById('spnMatriculas').innerHTML = '".$stHtml."';   \n";

    return $stJs;
}

function processarSpanCadastroAtributo()
{
    if ($_GET["stOpcoes"] == "atributo") {
        $stJs = gerarSpanOpcoes();
    }

    return $stJs;
}

function gerarSpanOrigemValores()
{
    $obFormulario = new Formulario;
    global $request;

    $stHtml  = "";
    $stJs    = "";
    $stJsAux = "";

    include_once(CAM_GRH_PES_COMPONENTES."IFiltroComponentes.class.php");
    $obIFiltroComponentes = new IFiltroComponentes();
    $obIFiltroComponentes->setMatricula();
    $obIFiltroComponentes->setCGMMatricula();
    $obIFiltroComponentes->setLotacao();

    if ($request->get("stOrigem") != "d") {
        $obIFiltroComponentes->setLocal();
        $obIFiltroComponentes->setAtributoServidor();
        $obIFiltroComponentes->setAtributoPensionista();

        include_once(CAM_GRH_PES_COMPONENTES."IFiltroCompetencia.class.php");
        $obIFiltroCompetencia =  new IFiltroCompetencia(true,"",true);
        $obIFiltroCompetencia->obCmbMes->obEvento->setOnChange("montaParametrosGET('gerarSpanAutorizacao','inAno,inCodMes',true);");
        $obIFiltroCompetencia->obSeletorAno->setDisabled(true);

        include_once(CAM_GRH_PES_COMPONENTES."IFiltroTipoFolha.class.php");
        $obIFiltroTipoFolha = new IFiltroTipoFolha();
        $obIFiltroTipoFolha->setValorPadrao(1);

        if ($request->get("stOrigem") == "p") {
            include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPrevidencia.class.php");
            $obTFolhaPagamentoPrevidencia = new TFolhaPagamentoPrevidencia();
            $obTFolhaPagamentoPrevidencia->recuperaLista($rsPrevidencia);

            $obCmbTabelaPrevidencia = new Select;
            $obCmbTabelaPrevidencia->setRotulo                    ( "Tabela Previdência"                                        );
            $obCmbTabelaPrevidencia->setTitle                     ( "Selecione a tabela de previdência que deverá ser emitida." );
            $obCmbTabelaPrevidencia->setName                      ( "inCodPrevidencia"                                          );
            $obCmbTabelaPrevidencia->setStyle                     ( "width: 200px"                                              );
            $obCmbTabelaPrevidencia->addOption                    ( "", "Selecione"                                             );
            $obCmbTabelaPrevidencia->setCampoID                   ( "[cod_previdencia]"                                         );
            $obCmbTabelaPrevidencia->setCampoDesc                 ( "[descricao]"                                               );
            $obCmbTabelaPrevidencia->preencheCombo                ( $rsPrevidencia                                              );
            $obCmbTabelaPrevidencia->setNull                      ( false                                                       );
        }

        $obCkbSituacao1 = new Radio();
        $obCkbSituacao1->setRotulo("Dados do Cadastro");
        $obCkbSituacao1->setName("stSituacao");
        $obCkbSituacao1->setTitle("Marque o cadastro para filtro: Ativos, Aposentados, Pensionistas, Rescindidos.");
        $obCkbSituacao1->setValue("a");
        $obCkbSituacao1->setLabel("Ativos");
        $obCkbSituacao1->setChecked(true);
        $obCkbSituacao1->setNull(false);
        $obCkbSituacao1->obEvento->setOnClick("montaParametrosGET('processarSpanCadastroAtributo','stSituacao,stOpcoes',true);");

        $obCkbSituacao2 = new Radio();
        $obCkbSituacao2->setRotulo("Dados do Cadastro");
        $obCkbSituacao2->setName("stSituacao");
        $obCkbSituacao2->setTitle("Marque o cadastro para filtro: Ativos, Aposentados, Pensionistas, Rescindidos.");
        $obCkbSituacao2->setValue("p");
        $obCkbSituacao2->setLabel("Aposentados");
        $obCkbSituacao2->setNull(false);
        $obCkbSituacao2->obEvento->setOnClick("montaParametrosGET('processarSpanCadastroAtributo','stSituacao,stOpcoes',true);");

        $obCkbSituacao3 = new Radio();
        $obCkbSituacao3->setRotulo("Dados do Cadastro");
        $obCkbSituacao3->setName("stSituacao");
        $obCkbSituacao3->setTitle("Marque o cadastro para filtro: Ativos, Aposentados, Pensionistas, Rescindidos.");
        $obCkbSituacao3->setValue("e");
        $obCkbSituacao3->setLabel("Pensionistas");
        $obCkbSituacao3->setNull(false);
        $obCkbSituacao3->obEvento->setOnClick("montaParametrosGET('processarSpanCadastroAtributo','stSituacao,stOpcoes',true);");

        $obCkbSituacao4 = new Radio();
        $obCkbSituacao4->setRotulo("Dados do Cadastro");
        $obCkbSituacao4->setName("stSituacao");
        $obCkbSituacao4->setTitle("Marque o cadastro para filtro: Ativos, Aposentados, Pensionistas, Rescindidos.");
        $obCkbSituacao4->setValue("r");
        $obCkbSituacao4->setLabel("Rescindidos");
        $obCkbSituacao4->setNull(false);
        $obCkbSituacao4->obEvento->setOnClick("montaParametrosGET('processarSpanCadastroAtributo','stSituacao,stOpcoes',true);");

        $obSpnAutorizacao = new Span();
        $obSpnAutorizacao->setId('spnAutorizacao');

        $obIFiltroCompetencia->geraFormulario($obFormulario);
        $obIFiltroTipoFolha->geraFormulario($obFormulario);
        $obIFiltroComponentes->geraFormulario($obFormulario);
        $obFormulario->agrupaComponentes(array($obCkbSituacao1,$obCkbSituacao2,$obCkbSituacao3,$obCkbSituacao4));
        if ($request->get("stOrigem") == "p") {
            $obFormulario->addComponente($obCmbTabelaPrevidencia);
        }
        $obFormulario->addSpan($obSpnAutorizacao);

        $stJsAux = gerarSpanAutorizacao($obIFiltroCompetencia->obSeletorAno->getValue(), $obIFiltroCompetencia->obCmbMes->getValue());
        validaExercicioContabil($stJsAux);
    } else {
        $obIFiltroComponentes->setPeriodo();
        $obIFiltroComponentes->setRotuloPeriodo("Período da Viagem");

        $obIFiltroComponentes->geraFormulario($obFormulario);

        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
        $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
        $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsPeriodoMovimentacao);

        if (validaExercicioContabil($stJsAux)) {
           $stJsAux = validaConfiguracaoDiarias();
        }
    }

    $obFormulario->obJavaScript->montaJavaScript();
    $stEval = $obFormulario->obJavaScript->getInnerJavaScript();

    $obFormulario->montaInnerHtml();
    $stHtml .= $obFormulario->getHTML();
    $stJs   .= "jQuery('#spnOrigem').html('".$stHtml."');\n";
    $stJs   .= "jQuery('#hdnOrigem').val('".$stEval."');\n";
    $stJs .= $stJsAux;

    return $stJs;
}

function gerarSpanAutorizacao($inAnoCompetencia, $inMesCompetencia)
{
    $obFormulario = new Formulario;
    $rsConfiguracaoAutorizacaoEmpenho = new RecordSet();

    $stHtml   = "";
    $stJs     = "";
    $stFiltro = "";

    if ($inAnoCompetencia != "" && $inMesCompetencia != "") {
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
        $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
        $obTFolhaPagamentoPeriodoMovimentacao->setDado("mes",$inMesCompetencia*1);
        $obTFolhaPagamentoPeriodoMovimentacao->setDado("ano",$inAnoCompetencia*1);
        $obTFolhaPagamentoPeriodoMovimentacao->recuperaPeriodoMovimentacaoDaCompetencia($rsPeriodoMovimentacao);

        if ($rsPeriodoMovimentacao->getNumLinhas() > 0) {

            include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConfiguracaoEmpenho.class.php");
            include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConfiguracaoAutorizacaoEmpenho.class.php");
            $obTFolhaPagamentoConfiguracaoEmpenho            = new TFolhaPagamentoConfiguracaoEmpenho();
            $obTFolhaPagamentoConfiguracaoAutorizacaoEmpenho = new TFolhaPagamentoConfiguracaoAutorizacaoEmpenho();
            $stFiltro = " WHERE ultima_vigencia_competencia.vigencia <= to_date('".$rsPeriodoMovimentacao->getCampo("dt_final")."','dd/mm/yyyy')
                            AND to_char(ultima_vigencia_competencia.vigencia,'yyyy') = '".$inAnoCompetencia."'";
            $stOrdem  = " ORDER BY dt_vigencia DESC LIMIT 1";
            $obTFolhaPagamentoConfiguracaoEmpenho->recuperaVigencias($rsVigencia, $stFiltro, $stOrdem);

            if ($rsVigencia->getNumLinhas() > 0) {
                $stFiltro = " WHERE configuracao_autorizacao_empenho.vigencia = '".$rsVigencia->getCampo("dt_vigencia")."'
                                AND configuracao_autorizacao_empenho.exercicio = '".$inAnoCompetencia."'";
                $obTFolhaPagamentoConfiguracaoAutorizacaoEmpenho->recuperaRelacionamento($rsConfiguracaoAutorizacaoEmpenho, $stFiltro);
            }
        }
    }

    $obCmbDadosAutorizacao = new Select;
    $obCmbDadosAutorizacao->setRotulo                    ( "Configuração Dados da Autorização"                                     );
    $obCmbDadosAutorizacao->setTitle                     ( "Selecione a configuração que será usada para preenchimento da autorização de empenho, esta configuração é realizada na funcionalidade Configuração, ação Configurar Autorização de Empenho."                        );
    $obCmbDadosAutorizacao->setName                      ( "inCodConfiguracaoAutorizacao"                                      );
    $obCmbDadosAutorizacao->setStyle                     ( "width: 200px"                                        );
    $obCmbDadosAutorizacao->addOption                    ( "", "Selecione"                                       );
    $obCmbDadosAutorizacao->setCampoID                   ( "[cod_configuracao_autorizacao]"                      );
    $obCmbDadosAutorizacao->setCampoDesc                 ( "[descricao_item]"                                    );
    $obCmbDadosAutorizacao->preencheCombo                ( $rsConfiguracaoAutorizacaoEmpenho                     );
    $obCmbDadosAutorizacao->setNull                      ( false                                                 );

    $obFormulario->addComponente($obCmbDadosAutorizacao);

    $obFormulario->obJavaScript->montaJavaScript();
    $stEval = $obFormulario->obJavaScript->getInnerJavaScript();

    $obFormulario->montaInnerHtml();
    $stHtml .= $obFormulario->getHTML();
    $stJs   .= "jQuery('#spnAutorizacao').html('".$stHtml."');\n";
    $stJs   .= "jQuery('#hdnOrigem').val( jQuery('#hdnOrigem').val() + '".$stEval."');\n";
    $stJs   .= "jQuery('#Ok').attr('disabled', false);\n";

    return $stJs;
}

function validaConfiguracaoDiarias()
{
    $obErro = new Erro();
    $rsConfiguracaoAutorizacaoEmpenho = new RecordSet();

    $stJs = "";

    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
    $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
    $obTFolhaPagamentoPeriodoMovimentacao->setDado("mes",1);
    $obTFolhaPagamentoPeriodoMovimentacao->setDado("ano",Sessao::getExercicio());
    $obTFolhaPagamentoPeriodoMovimentacao->recuperaPeriodoMovimentacaoDaCompetencia($rsPeriodoMovimentacao);

    if ($rsPeriodoMovimentacao->getNumLinhas() < 1) {
       $obErro->setDescricao("Não existe período movimentação criado para o Exercício especificado - ".Sessao::getExercicio());
    } else {
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConfiguracaoEmpenho.class.php");
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConfiguracaoAutorizacaoEmpenho.class.php");
        $obTFolhaPagamentoConfiguracaoEmpenho            = new TFolhaPagamentoConfiguracaoEmpenho();
        $obTFolhaPagamentoConfiguracaoAutorizacaoEmpenho = new TFolhaPagamentoConfiguracaoAutorizacaoEmpenho();
        $stFiltro = " WHERE to_char(ultima_vigencia_competencia.vigencia,'yyyy') = '".Sessao::getExercicio()."'";
        $stOrdem  = " ORDER BY dt_vigencia DESC LIMIT 1 ";
        $obTFolhaPagamentoConfiguracaoEmpenho->recuperaVigencias($rsVigencia, $stFiltro, $stOrdem);

        if ($rsVigencia->getNumLinhas() > 0) {
            $stFiltro = " WHERE configuracao_autorizacao_empenho.exercicio = '".Sessao::getExercicio()."'";
            $stOrdem  = " ORDER BY configuracao_autorizacao_empenho.cod_configuracao_autorizacao LIMIT 1 ";
            $obTFolhaPagamentoConfiguracaoAutorizacaoEmpenho->recuperaRelacionamento($rsConfiguracaoAutorizacaoEmpenho, $stFiltro, $stOrdem);
        }

        if ($rsConfiguracaoAutorizacaoEmpenho->getNumLinhas() < 1) {
           $obErro->setDescricao('Não existem Configurações de Autorização de Empenho para o exercício especificado - '.Sessao::getExercicio());
        }
    }

    if ($obErro->ocorreu()) {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
        $stJs .= "jQuery('#Ok').attr('disabled', true);\n";
    } else {
        $stJs .= "jQuery('#Ok').attr('disabled', false);\n";
    }

    return $stJs;
}

function validaExercicioContabil(&$stJs)
{
    $stFiltro = " WHERE cod_entidade = ".Sessao::getCodEntidade($boTransacao)."
                    AND to_char(dt_empenho, 'yyyy')::integer > ".Sessao::getExercicio();
    $stOrdem  = " ORDER BY dt_empenho DESC LIMIT 1 ";

    $obTEmpenhoEmpenho = new TEmpenhoEmpenho();
    $obTEmpenhoEmpenho->recuperaTodos($rsExerciciosEmpenho, $stFiltro, $stOrdem);

    if ($rsExerciciosEmpenho->getNumLinhas() > 0) {
        $stJs .= "alertaAviso('O exercício de ".$rsExerciciosEmpenho->getCampo('exercicio')." já foi iniciado pela Gestão Financeira para a entidade de trabalho.','form','erro','".Sessao::getId()."');\n";
        $stJs .= "jQuery('#Ok').attr('disabled', true);\n";

        return false;
    }

    $stFiltro = " WHERE cod_entidade = ".Sessao::getCodEntidade($boTransacao)."
                    AND to_char(dt_empenho, 'yyyy')::integer = ".Sessao::getExercicio();
    $obTEmpenhoEmpenho->recuperaTodos($rsExerciciosEmpenho, $stFiltro, $stOrdem);
    if ($rsExerciciosEmpenho->getNumLinhas() < 1) {
        $stJs .= "alertaAviso('O exercício de ".Sessao::getExercicio()." ainda não foi iniciado pela Gestão Financeira para a entidade de trabalho.','form','erro','".Sessao::getId()."');\n";
        $stJs .= "jQuery('#Ok').attr('disabled', true);\n";

        return false;
    }

    $stJs .= "jQuery('#Ok').attr('disabled', false);\n";

    return true;
}

switch ($_GET["stCtrl"]) {
    case "gerarSpanOpcoes":
       $stJs = gerarSpanOpcoes();
       break;
    case "gerarSpanAtributosDinamicos":
        $stJs = gerarSpanAtributosDinamicos();
        break;
    case "gerarSpanFolhaComplementar":
        $stJs = gerarSpanFolhaComplementar();
        break;
    case "gerarSpanFolhas":
        $stJs = gerarSpanFolhas();
        break;
    case "gerarSpans":
        $stJs = gerarSpans();
        break;
    case "gerarSpanComplementar":
        $stJs = gerarSpanComplementar();
        break;
    case "gerarSpanResumoEmissaoAutorizacaoEmpenho":
        $stJs = gerarSpanResumoEmissaoAutorizacaoEmpenho();
        break;
    case "gerarSpanResumoEmissaoAutorizacaoEmpenhoDiarias":
        $stJs = gerarSpanResumoEmissaoAutorizacaoEmpenhoDiarias();
        break;
    case "excluirAutorizacao":
        $stJs = excluirAutorizacao();
        break;
    case "excluirAutorizacaoDiarias":
        $stJs = excluirAutorizacaoDiarias();
        break;
    case "submeter":
        $stJs = submeter();
        break;
    case "_incluirMatricula":
        $stJs = _incluirMatricula();
        break;
    case "__incluirMatricula":
        $stJs = __incluirMatricula();
        break;
    case "excluirMatricula":
        $stJs = excluirMatricula();
        break;
    case "processarSpanCadastroAtributo":
        $stJs = processarSpanCadastroAtributo();
        break;
    case "gerarSpanOrigemValores":
        $stJs = gerarSpanOrigemValores();
        break;
    case "gerarSpanAutorizacao":
        $stJs = gerarSpanAutorizacao($_REQUEST['inAno'], $_REQUEST['inCodMes']);
        break;
}

if ($stJs) {
    echo $stJs;
}
?>
