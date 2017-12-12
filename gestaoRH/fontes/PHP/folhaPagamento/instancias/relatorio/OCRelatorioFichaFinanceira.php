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

    $Revision: 30840 $
    $Name$
    $Author: souzadl $
    $Date: 2007-10-10 15:09:58 -0300 (Qua, 10 Out 2007) $

    * Casos de uso: uc-04.05.35
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroContrato.class.php"                                          );
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroCGMContrato.class.php"                                       );
include_once ( CAM_GRH_PES_COMPONENTES."ISelectMultiploLocal.class.php"                                     );
include_once ( CAM_GRH_PES_COMPONENTES."ISelectMultiploLotacao.class.php"                                   );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoContratoServidor.class.php"                       );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                           );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidor.class.php"                                  );

//Define o nome dos arquivos PHP
$stPrograma = "RelatorioFichaFinanceira";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

function gerarSpanFiltroFolha()
{
    if ($_GET["stEmitir"] == "tipo_calculo" OR !$_GET["stEmitir"]) {
        include_once(CAM_GRH_PES_COMPONENTES."IFiltroTipoFolha.class.php");

        $obIFiltroTipoFolha = new IFiltroTipoFolha();
        $obIFiltroTipoFolha->setValorPadrao("1");

        $obFormulario = new Formulario;
           $obIFiltroTipoFolha->geraFormulario($obFormulario);
        $obFormulario->montaInnerHtml();
        $stJs .= "d.getElementById('spnFiltroFolha').innerHTML = '".$obFormulario->getHTML()."';     \n";
    } else {
        $stJs .= "d.getElementById('spnFiltroFolha').innerHTML = '';     \n";
    }

    return $stJs;
}

function submeter()
{
    include_once( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php" );

    $boErro = false;
    $inMes = ( $_GET["inCodMes"] < 10 ) ? "0".$_GET["inCodMes"] : $_GET["inCodMes"];
    $dtCompetenciaInicial = $_GET["inAno"]."-".$inMes;

    $inMesFinal = ( $_GET["inCodMesFinal"] < 10 ) ? "0".$_GET["inCodMesFinal"] : $_GET["inCodMesFinal"];
    $dtCompetenciaFinal = $_GET["inAnoFinal"]."-".$inMesFinal;

    if ($dtCompetenciaFinal < $dtCompetenciaInicial) {
        $stMensagem = "A competência final deve ser superior à competência inicial.";
        $boErro = true;
    }

    if ($boErro == false) {
        $stJs .= "parent.frames[2].Salvar(); BloqueiaFrames(true, false);\n";
    } else {
        $stJs = "alertaAviso('@ $stMensagem','form','erro','".Sessao::getId()."');\n";
    }

    return $stJs;
}

function limparFormulario()
{
    include_once(CAM_GRH_PES_COMPONENTES."IFiltroTipoFolha.class.php");

    Sessao::remove("arContratos");
    Sessao::remove("arEventos");
    $obIFiltroTipoFolha = new IFiltroTipoFolha();
    $obIFiltroTipoFolha->setValorPadrao("1");

    $obFormulario = new Formulario;
    $obIFiltroTipoFolha->geraFormulario($obFormulario);
    $obFormulario->montaInnerHtml();
    $stJs  = "d.getElementById('spnFiltroFolha').innerHTML = '".$obFormulario->getHTML()."';     \n";
    $stJs .= "d.frm.stEmitir[0].checked = true;											 		 \n";
    $stJs .= "d.frm.stOrdenacaoEventos[0].checked = true; 										 \n";
    $stJs .= "d.frm.stTipoFiltro.value = 'contrato';											 \n";
    $stJs .= "ajaxJavaScriptSincrono('".CAM_GRH_PES_PROCESSAMENTO."OCIFiltroComponentes.php?".Sessao::getId()."&stTipoFiltro=contrato','gerarSpan' ); \n";

    return $stJs;

}

switch ($_GET["stCtrl"]) {
    case "OK":
        $stJs = submeter();
    break;
    case "limparFormulario":
        $stJs = limparFormulario();
    break;
    case "gerarSpanFiltroFolha":
        $stJs = gerarSpanFiltroFolha();
        break;
}

if ($stJs) {
    echo $stJs;
}

?>
