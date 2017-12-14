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
/*
 * Oculto para Relatório Grade de Horários
 * Data de Criação   : 17/10/2008

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
$stPrograma = "GradeHorarios";
$pgFilt      = "FL".$stPrograma.".php";
$pgList      = "LS".$stPrograma.".php";
$pgForm      = "FM".$stPrograma.".php";
$pgProc      = "PR".$stPrograma.".php";
$pgOcul      = "OC".$stPrograma.".php";
$pgJS        = "JS".$stPrograma.".js";

function onLoad()
{
    $stJs = gerarSpanGradeHorarios();

    return $stJs;
}

function gerarSpanEmitir()
{
    if ($_GET["stEmitir"] == "G") {
        $stJs = gerarSpanGradeHorarios();
    } else {
        $stJs = gerarSpanServidoresGrade();
    }

    return $stJs;
}

function gerarSpanGradeHorarios()
{
    include_once(CAM_GRH_PES_COMPONENTES."IPopUpGradeHorario.class.php");
    $obIPopUpGradeHorario = new IPopUpGradeHorario();

    $obFormulario = new Formulario();
    $obFormulario->addComponente($obIPopUpGradeHorario);
    $obFormulario->montaInnerHTML();
    $obFormulario->obJavaScript->montaJavaScript();
    $stHTML = $obFormulario->getHTML();
    $stEval  = $obFormulario->obJavaScript->getInnerJavaScript();

    $stJs  = "jQuery('#spnEmitir').html('".$stHTML."');\n";
    $stJs .= "jQuery('#hdnEmitir').val('".$stEval."');\n";

    return $stJs;
}

function gerarSpanServidoresGrade()
{
    include_once(CAM_GRH_PES_COMPONENTES."IFiltroComponentes.class.php")    ;
    $obIFiltroComponentes = new IFiltroComponentes();
    $obIFiltroComponentes->setGeral(false);
    $obIFiltroComponentes->setMatricula();
    $obIFiltroComponentes->setCGMMatricula();
    $obIFiltroComponentes->setLotacao();
    $obIFiltroComponentes->setLocal();
    $obIFiltroComponentes->setRegSubFunEsp();
    $obIFiltroComponentes->setGrupoLotacao();
    $obIFiltroComponentes->setGrupoLocal();
    $obIFiltroComponentes->setGrupoRegSubFunEsp();
    $obIFiltroComponentes->setFiltroPadrao("lotacao_grupo");

    include_once(CAM_GRH_PES_COMPONENTES."IFiltroCompetencia.class.php");
    $obIFiltroCompetencia = new IFiltroCompetencia();

    $obRdoOrdAlfabetica = new Radio();
    $obRdoOrdAlfabetica->setRotulo("Ordenação dos Servidores");
    $obRdoOrdAlfabetica->setName("stOrdenacao");
    $obRdoOrdAlfabetica->setId("stOrdenacao");
    $obRdoOrdAlfabetica->setLabel("Alfabética");
    $obRdoOrdAlfabetica->setValue("A");
    $obRdoOrdAlfabetica->setChecked(true);

    $obRdoOrdNumerica = new Radio();
    $obRdoOrdNumerica->setRotulo("Ordenação dos Servidores");
    $obRdoOrdNumerica->setName("stOrdenacao");
    $obRdoOrdNumerica->setId("stOrdenacao");
    $obRdoOrdNumerica->setLabel("Numérica");
    $obRdoOrdNumerica->setValue("N");

    $obFormulario = new Formulario();
    $obIFiltroComponentes->geraFormulario($obFormulario);
    $obIFiltroCompetencia->geraFormulario($obFormulario);
    $obFormulario->agrupaComponentes(array($obRdoOrdAlfabetica,$obRdoOrdNumerica));
    $obFormulario->montaInnerHTML();
    $obFormulario->obJavaScript->montaJavaScript();
    $stHTML = $obFormulario->getHTML();
    $stEval  = $obFormulario->obJavaScript->getInnerJavaScript();

    $stJs  = "jQuery('#spnEmitir').html('".$stHTML."');\n";
    $stJs .= "jQuery('#hdnEmitir').val('".$stEval."');\n";
    $obIFiltroComponentes->getOnLoad($stJs);

    return $stJs;
}

switch ($_GET["stCtrl"]) {
    case "onLoad":
        $stJs = onLoad();
        break;
    case "gerarSpanEmitir":
        $stJs = gerarSpanEmitir();
        break;
}

if ($stJs) {
    echo $stJs;
}

?>
