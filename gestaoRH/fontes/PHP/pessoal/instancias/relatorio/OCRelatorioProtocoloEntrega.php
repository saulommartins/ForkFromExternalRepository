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
    * Página de Oculto do Relatorio de Protocolo de Entrega
    * Data de Criação : 04/04/2007

    * @author Analista: Dagiane
    * @author Desenvolvedor: André Machado

    * @ignore

    $Revision: 30863 $
    $Name$
    $Autor: $
    $Date: 2007-09-26 18:29:00 -0300 (Qua, 26 Set 2007) $

    * Casos de uso: uc-04.04.47
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroContrato.class.php"                                          );
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroCGMContrato.class.php"                                       );
include_once ( CAM_GRH_PES_COMPONENTES."ISelectMultiploLocal.class.php"                                     );
include_once ( CAM_GRH_PES_COMPONENTES."ISelectMultiploLotacao.class.php"                                   );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoContratoServidor.class.php"                       );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                           );

//Define o nome dos arquivos PHP
$stPrograma = "RelatorioProtocoloEntrega";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

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

    $obChkAtributoTotalizar = new CheckBox();
    $obChkAtributoTotalizar->setRotulo("Atributo");
    $obChkAtributoTotalizar->setName("boTotalizarAtributo");
    $obChkAtributoTotalizar->setValue(true);
    $obChkAtributoTotalizar->setTitle("Selecione a opção para totalização e quebra por atributo.");
    $obChkAtributoTotalizar->setLabel("Totalizar");

    $obChkAtributoQuebrar = new CheckBox();
    $obChkAtributoQuebrar->setRotulo("Atributo");
    $obChkAtributoQuebrar->setName("boQuebrarAtributo");
    $obChkAtributoQuebrar->setValue(true);
    $obChkAtributoQuebrar->setTitle("Selecione a opção para totalização e quebra por atributo.");
    $obChkAtributoQuebrar->setLabel("Quebrar Página");

    $obFormulario = new Formulario;
    $obFormulario->addTitulo("Atributo Dinâmico");
    $obFormulario->addComponente($obCmbAtributo);
    $obFormulario->addSpan($obSpnAtributo);

    $obFormulario->addTitulo("Filtros a Agrupar ");
    $obFormulario->agrupaComponentes(array($obChkAtributoTotalizar,$obChkAtributoQuebrar));

    $obFormulario->montaInnerHTML();
    $obFormulario->obJavaScript->montaJavaScript();

    $stEval = $obFormulario->obJavaScript->getInnerJavascriptBarra();
    $stEval = str_replace("\n", "", $stEval);

    return $obFormulario->getHTML();
}

function gerarSpanAtributosDinamicos(&$stEval2)
{
    if ($_REQUEST['inCodAtributo'] != "") {
        include_once(CAM_GRH_PES_NEGOCIO."RPessoalServidor.class.php");
        $obRPessoalServidor = new RPessoalServidor();
        $obRPessoalServidor->addContratoServidor();
        $obRPessoalServidor->roUltimoContratoServidor->obRCadastroDinamico->setChavePersistenteValores( array('cod_atributo'=>$_REQUEST['inCodAtributo']) );
        $obRPessoalServidor->roUltimoContratoServidor->obRCadastroDinamico->recuperaAtributosSelecionados( $rsAtributos );

        $obMontaAtributos = new MontaAtributos;
        $obMontaAtributos->setTitulo     ( "Atributos"  );
        $obMontaAtributos->setName       ( "Atributo_"  );
        $obMontaAtributos->setRecordSet  ( $rsAtributos );

        $obHdnCodCadastro = new hidden();
        $obHdnCodCadastro->setName("inCodCadastro");
        $obHdnCodCadastro->setValue($rsAtributos->getCampo("cod_cadastro"));

        $obHdnDescCadastro = new hidden();
        $obHdnDescCadastro->setName("stDescCadastro");
        $obHdnDescCadastro->setValue($rsAtributos->getCampo("nom_atributo"));

        $obFormulario = new Formulario();
        $obFormulario->addHidden($obHdnCodCadastro);
        $obFormulario->addHidden($obHdnDescCadastro);
        $obMontaAtributos->geraFormulario( $obFormulario );

        $obFormulario->montaInnerHTML();
        $obFormulario->obJavaScript->montaJavaScript();
        $stEval2 .= $obFormulario->obJavaScript->getInnerJavaScript();
        $stHtml =  $obFormulario->getHTML();
    } else {
        $stHtml = "";
    }

    return $stHtml;
}

function gerarSpanComplementoTitulo()
{
    ;

    if ($_GET['boComplementoTitulo'] == 'on') {
        $obTxtComplementoTitulo = new TextBox;
        $obTxtComplementoTitulo->setRotulo          ( "Complemento do título"								);
        $obTxtComplementoTitulo->setName			( "txtComplementoTitulo"								);
        $obTxtComplementoTitulo->setId				( "txtComplementoTitulo"								);
        $obTxtComplementoTitulo->setSize			( 80													);
        $obTxtComplementoTitulo->setMaxLength		( 60													);
        $obTxtComplementoTitulo->setTitle           ( "Digite o complemento do título do relatório"			);

        $obFormulario = new Formulario;
        $obFormulario->addComponente            	( $obTxtComplementoTitulo                   			);
        $obFormulario->montaInnerHtml();

        $stJs = "document.getElementById('spnComplementoTitulo').innerHTML = '".$obFormulario->getHtml()."';    \n";
    } else {
        $stJs = "document.getElementById('spnComplementoTitulo').innerHTML = '';    							 \n";
    }

    return $stJs;
}

function imprimirSequencia()
{
    $obTxtIniciar = new TextBox;
    $obTxtIniciar->setRotulo                    ( "Iniciar em "                                     );
    $obTxtIniciar->setTitle                     ( "Digite o inicio da sequência."                        );
    $obTxtIniciar->setName                      ( "inSequencia"                                   );
    $obTxtIniciar->setValue                     ( 0001                                    );
    $obTxtIniciar->setSize                      ( 6                                                     );
    $obTxtIniciar->setMaxLength                 ( 6                                                     );
    $obTxtIniciar->setNull                      ( false                                                 );
    $obTxtIniciar->setInteiro                   ( true                                                  );

    $obFormulario = new Formulario;
    $obFormulario->addComponente            ( $obTxtIniciar                   );
    $obFormulario->montaInnerHtml();

    $stJs .= "if (f.boImprimirSequencia.checked) {d.getElementById('spnSequencia').innerHTML = '".$obFormulario->getHtml()."';}\n";
    $stJs .= "else{d.getElementById('spnSequencia').innerHTML = '';}";

    return $stJs;
}

function submeter()
{
    $boErro = false;
    $inMes = ( $_GET["inCodMes"] < 10 ) ? "0".$_GET["inCodMes"] : $_GET["inCodMes"];
    $dtCompetenciaInicial = $_GET["inAno"]."-".$inMes;

    $inMesFinal = ( $_GET["inCodMesFinal"] < 10 ) ? "0".$_GET["inCodMesFinal"] : $_GET["inCodMesFinal"];
    $dtCompetenciaFinal = $_GET["inAnoFinal"]."-".$inMesFinal;
    if ($dtCompetenciaFinal < $dtCompetenciaInicial) {
        $stMensagem = "A competência final deve ser superior à competência inicial.";
        $boErro = true;
    }
    if ( ($_GET["stOpcao"] == "contrato" or $_GET["stOpcao"] == "cgm_contrato") and count(Sessao::read("arContratos")) == 0 ) {
        $stMensagem = "Deve haver pelo menos um contrato na lista.";
        $boErro = true;
    }
    if ( $_GET["stOpcao"] == "evento" and count(Sessao::read("arEventos")) == 0 ) {
        $stMensagem = "Deve haver pelo menos um contrato na lista.";
        $boErro = true;
    }
    if ($_GET["stOpcao"] == "atributo" and $_REQUEST["Atributo_".$_REQUEST["inCodAtributo"]."_5"] == "") {
        $stMensagem = "Campo Atributo deve ser preenchido.";
        $boErro = true;
    }
    if ($boErro == false) {
        $stJs .= "parent.frames[2].Salvar();    \n";
    } else {
        $stJs = "alertaAviso('@ $stMensagem','form','erro','".Sessao::getId()."');";
    }

    return $stJs;
}

switch ($_GET["stCtrl"]) {
    case "gerarSpanAtributosDinamicos":
       $stHtml = gerarSpanAtributosDinamicos($stEval2);
       $stJs .= "d.getElementById('spnAtributo').innerHTML = '$stHtml';\n";
       $stJs .= "f.hdnFiltroDinamico.value                       = '$stEval2';\n";
    break;
    case "OK":
        $stJs.= submeter();
    break;
    case "limpar":
        $stJs.= processarFiltro();
    break;
    case "imprimirSequencia":
        $stJs .= imprimirSequencia();
    break;
    case "gerarSpanComplementoTitulo":
        $stJs .= gerarSpanComplementoTitulo();
    break;
}

if ($stJs) {
    echo $stJs;
}

?>
