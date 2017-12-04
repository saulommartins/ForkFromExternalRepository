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
    * Página de Filtro do Relatório de Cadastro de Pensão Judicial
    * Data de Criação : 05/03/2007

    * @author Analista: Dagiane
    * @author Desenvolvedor: André Machado

    * @ignore

    $Revision: 30860 $
    $Name$
    $Autor: $
    $Date: 2007-09-26 18:29:00 -0300 (Qua, 26 Set 2007) $

    * Casos de uso: uc-04.04.49
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "CadastroPensaoJudicial";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

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
    if ($boErro == false) {
        $stJs .= "parent.frames[2].Salvar();    \n";
    } else {
        $stJs = "alertaAviso('@ $stMensagem','form','erro','".Sessao::getId()."');";
    }

    return $stJs;
}

switch ($_GET["stCtrl"]) {
    case "OK":
        $stJs.= submeter();
    break;
    case "limpar":
        $stJs.= processarFiltro();
    break;
}

if ($stJs) {
    echo $stJs;
}

?>
