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
    * Página oculto para Relatório de Cargos
    * Data de Criação   : 19/01/2009

    * @author Analista: Dagiane Vieira
    * @author Desenvolvedor: Alex Cardoso

    * @ignore

    $Id: $

    * Casos de uso: uc-04.04.11
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "RelatorioCargo";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

function habilitaSpanApresentaServidores()
{
    include_once(CAM_GRH_PES_COMPONENTES."IFiltroComponentes.class.php");
    $obIFiltroComponentes = new IFiltroComponentes();
    $obIFiltroComponentes->setMatricula();
    $obIFiltroComponentes->setCGMMatricula();
    $obIFiltroComponentes->setLotacao();
    $obIFiltroComponentes->setLocal();
    $obIFiltroComponentes->setRegimeSubDivisao();
    $obIFiltroComponentes->setRegSubCarEsp();
    $obIFiltroComponentes->setRegSubFunEsp();
    $obIFiltroComponentes->setAtributoServidor();
    $obIFiltroComponentes->setGrupoLocal();
    $obIFiltroComponentes->setGrupoLotacao();
    $obIFiltroComponentes->setGrupoRegimeSubDivisao();
    $obIFiltroComponentes->setGrupoRegSubCarEsp();
    $obIFiltroComponentes->setGrupoRegSubFunEsp();
    $obIFiltroComponentes->setGrupoAtributoServidor();
    $obIFiltroComponentes->setEnabledQuebra();

    $obFormulario = new Formulario();
    $obFormulario->addTitulo("Fitro de Servidores");

    $stHTML = $obFormulario->getHTML();

    $obIFiltroComponentes->geraFormulario($obFormulario);
    $obFormulario->montaInnerHTML();
    $obFormulario->obJavaScript->montaJavaScript();
    $stHtml = $obFormulario->getHTML();
    $stJsEval = $obFormulario->obJavaScript->getInnerJavaScript();

    $stJs .= "if ( jQuery('#stApresentaServidores').attr('checked') == true ) {\n
                    jQuery('#spnServidores').html('".$stHtml."');\n
                    jQuery('#hdnValidaServidores').val('".$stJsEval."'); \n
              } else {\n
                    jQuery('#spnServidores').html('');\n
                    jQuery('#hdnValidaServidores').val(''); \n
              } \n";

    return $stJs;
}

switch ($_GET["stCtrl"]) {
    case "habilitaSpanApresentaServidores":
        $stJs = habilitaSpanApresentaServidores();
        break;
}

if ($stJs) {
    echo $stJs;
}
?>
