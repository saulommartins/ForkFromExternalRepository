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
    * Página oculta do Relatório de FGTS
    * Data de Criação: 05/05/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Bruce Cruz de Sena

    * @ignore
    * Casos de uso: uc-04.05.25
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroCompetencia.class.php"                                   );

//Define o nome dos arquivos PHP
$stPrograma = "RelatorioFGTS";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

function submeter()
{
    if ( ($_GET["stTipoFiltro"] == "contrato" or $_GET["stTipoFiltro"] == "cgm_contrato") and count(Sessao::read("arContratos"))==0 ) {
        $stMensagem = "Deve haver pelo menos um contrato na lista.";
        $stJs = "alertaAviso('@ $stMensagem','form','erro','".Sessao::getId()."');";
    } else {
        $stJs  = "f.target = 'telaPrincipal'; \n";
        $stJs .= "parent.frames[2].Salvar();\n";
    }

    return $stJs;
}

function limparForm()
{
    Sessao::remove("arContratos");
    $stJs  = "d.frm.inCodConfiguracao.value = 1;																	\n";
    $stJs .= "d.frm.stConfiguracao.value = 1; 																		\n";
    $stJs .= "d.frm.stOrdenacao.options[0].selected = true; 														\n";
    $stJs .= "d.frm.stTipoFiltro.value = 'contrato';																\n";
    $stJs .= "ajaxJavaScriptSincrono('".CAM_GRH_PES_PROCESSAMENTO."OCIFiltroComponentes.php?".Sessao::getId()."&stTipoFiltro=contrato','gerarSpan' ); \n";

    return $stJs;
}

switch ($_GET["stCtrl"]) {
    case "OK":
        $stJs = submeter();
    break;
    case "limparForm":
        $stJs = limparForm();
        break;
}

if ($stJs) {
    echo $stJs;
}

?>
