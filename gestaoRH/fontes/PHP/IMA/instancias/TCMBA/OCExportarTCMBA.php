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
    * Página de Oculto do Exportar Arquivo TCM/BA
    * Data de Criação: 19/03/2007

    * @author Analista: Dagiane
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30829 $
    $Name$
    $Author: alex $
    $Date: 2008-01-11 15:59:37 -0200 (Sex, 11 Jan 2008) $

    * Casos de uso: uc-04.08.04
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GRH_PES_COMPONENTES."ISelectMultiploLotacao.class.php"                                );
include_once( CAM_GRH_PES_COMPONENTES."ISelectMultiploLocal.class.php"                                  );

//Define o nome dos arquivos PHP
$stPrograma = "ExportarTCMBA";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

function gerarSpan($stTipoFiltro="")
{
    $stTipoFiltro = ( $_GET['stTipoFiltro'] != "" ) ? $_GET['stTipoFiltro'] : $stTipoFiltro;
    switch ($stTipoFiltro) {
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
    $stJs .= "f.hdnFiltro.value = '$stEval';                           \n";

    return $stJs;
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
    $stJs .= "f.hdnFiltro.value = f.hdnFiltro.value + '$Js';                 \n";

    return $stJs;
}

function gerarSpanGeral(&$stEval)
{
    $stEval = "";
    $stHtml = "";

    return $stHtml;
}

function limparFiltro()
{
    $stJs  = gerarSpan($_GET['stTipoFiltro']);
    $stJs .= "f.inTipoEnvio.value = '1';\n";

    return $stJs;
}

function processarCompetencia()
{
    $stHtml = "";
    if ($_GET["inCodMes"] == "12" and $_GET["inAno"] != "") {
        $obCkbSomente13 = new CheckBox();
        $obCkbSomente13->setRotulo("Somente Informações da Competência 13 (13° Salário)");
        $obCkbSomente13->setName("boSomente13");
        $obCkbSomente13->setTitle("Marque esta opção para emitir o arquivo do TCM da competência 13, somente informações do décimo terceiro salário.");

        $obFormulario = new Formulario();
        $obFormulario->addComponente($obCkbSomente13);
        $obFormulario->montaInnerHTML();
        $stHtml = $obFormulario->getHTML();
    }
    $stJs .= "d.getElementById('spnCompetencia13').innerHTML = '$stHtml';\n";

    return $stJs;
}

function submeter()
{
    $obErro = new Erro();
    if ( ($_GET["stTipoFiltro"] == "contrato" or $_GET["stTipoFiltro"] == "cgm_contrato") and count(Sessao::read("arContratos2")) == 0 ) {
        $obErro->setDescricao("@A lista de contratos deve ter pelo menos uma matrícula.");
    }

    include_once(CAM_GRH_IMA_MAPEAMENTO."TIMAExportacaoTCMBA.class.php");
    $obTIMAExportacaoTCMBA = new TIMAExportacaoTCMBA();
    $obTIMAExportacaoTCMBA->recuperaTodos($rsConfiguracaoTCMBA);
    if ($rsConfiguracaoTCMBA->getNumLinhas() < 1) {
        $obErro->setDescricao("@Não é possivel gerar o arquivo. A Exportação TCM/BA não foi configurada");
    }

    if ( $obErro->ocorreu() ) {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    } else {
        $stJs .= "parent.frames[2].Salvar();\n";
        $stJs .= "BloqueiaFrames(true,false);\n";
        $stJs .= "parent.frames[2].document.body.scrollTop=0;\n";
    }

    return $stJs;
}

switch ($_GET['stCtrl']) {
    case "gerarSpan":
        $stJs .= gerarSpan();
    break;
    case "processarFiltro":
        $stJs .= processarFiltro();
    break;
    case "limparFiltro":
        $stJs .= limparFiltro();
    break;
    case "gerarSpanAtributosDinamicos":
        $stJs .= gerarSpanAtributosDinamicos();
    break;
    case "processarCompetencia":
        $stJs = processarCompetencia();
        break;
    case "submeter":
        $stJs .= submeter();
        break;
}

if ($stJs) {
   echo $stJs;
}

?>
