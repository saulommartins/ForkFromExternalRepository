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
    * Página de Processameto de Assentamentos
    * Data de Criação: 21/02/2008

    * @author Diego Lemos de Souza

    * Casos de uso: uc-04.04.51

    $Id: PRAssentamentos.php 66258 2016-08-03 14:25:21Z evandro $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "Assentamentos";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$stCodigos = "";
$stCodigos2 = "";
$stArray = "false";

switch ($_POST["stTipoFiltro"]) {
    case "contrato_todos":
    case "contrato_rescisao":
    case "cgm_contrato_rescisao":
    case "contrato":
    case "cgm_contrato":
    case "cgm_contrato_todos":
        foreach (Sessao::read("arContratos") as $arContrato) {
            $stCodigos .= $arContrato["cod_contrato"].",";
        }
        $stCodigos = substr($stCodigos,0,strlen($stCodigos)-1);
        break;
    case "lotacao":
        $stCodigos = implode(",",$_POST["inCodLotacaoSelecionados"]);
        break;
    case "local":
        $stCodigos = implode(",",$_POST["inCodLocalSelecionados"]);
        break;
    case "reg_sub_fun_esp":
        $stCodigos  = implode(",",$_REQUEST["inCodRegimeSelecionadosFunc"])."#";
        $stCodigos .= implode(",",$_REQUEST["inCodSubDivisaoSelecionadosFunc"])."#";
        $stCodigos .= implode(",",$_REQUEST["inCodFuncaoSelecionados"])."#";
        if (is_array($_REQUEST["inCodEspecialidadeSelecionadosFunc"])) {
            $stCodigos .= implode(",",$_REQUEST["inCodEspecialidadeSelecionadosFunc"]);
        }
        break;
    case "atributo_servidor":
        $inCodAtributo = $_POST["inCodAtributo"];
        $inCodCadastro = $_POST["inCodCadastro"];
        $stNomeAtributo = "Atributo_".$inCodAtributo."_".$inCodCadastro;
        if (is_array($_POST($stNomeAtributo."_Selecionados"))) {
             $inArray = 1;
             $stValores = implode(",",$_POST[$stNomeAtributo."_Selecionados"]);
        } else {
             $inArray = 0;
             $stValores = pg_escape_string($_POST[$stNomeAtributo]);
        }
        $stCodigos = $inArray."#".$inCodAtributo."#".$stValores;
        break;
}

$stPeriodoInicial = "";
$stPeriodoFinal = "";
switch ($_POST["inPeriodicidade"]) {
    case 1:
        if ($_POST["stDia"] != "") {
            $stPeriodoInicial = $_POST["stDia"];
            $stPeriodoFinal = $_POST["stDia"];
        }
        break;
    case 2:
        if ($_POST["stMes"] != "" and $_POST["stAnoMes"] != "") {
            $stPeriodoInicial = "01/".$_POST["stMes"]."/".$_POST["stAnoMes"];
            $stPeriodoFinal = "31/".$_POST["stMes"]."/".$_POST["stAnoMes"];
        }
        break;
    case 3:
        if ($_POST["stAno"] != "") {
            $stPeriodoInicial = "01/01/".$_POST["stAno"];
            $stPeriodoFinal = "31/12/".$_POST["stAno"];
        }
        break;
    case 4:
        if ($_POST["stPeriodoInicial"] != "" and $_POST["stPeriodoFinal"] != "") {
            $stPeriodoInicial = $_POST["stPeriodoInicial"];
            $stPeriodoFinal = $_POST["stPeriodoFinal"];
        }
        break;
}

if ($_REQUEST['boAgrupamento'] == 'C') {
    $preview = new PreviewBirt(4,22,8);
    $preview->setTitulo('Relatório de Assentamentos por Classificação');
    $preview->setNomeArquivo('relatorioClassificacao');

} elseif ($_REQUEST['boAgrupamento'] == 'A') {
    $preview = new PreviewBirt(4,22,13);
    $preview->setTitulo('Relatório de Assentamentos por Contrato');
    $preview->setNomeArquivo('assentamentoContrato');
}

$preview->setVersaoBirt("2.5.0");
$preview->setFormato("pdf");

$preview->addParametro("stEntidade", Sessao::getEntidade());
$preview->addParametro("entidade", Sessao::getCodEntidade($boTransacao));
$preview->addParametro("stCodAssentamentos", implode(",",$_POST["inCodAssentamentoSelecionados"]));
$preview->addParametro("stTipoFiltro", $_POST["stTipoFiltro"]);
$preview->addParametro("stCodigos", $stCodigos);
$preview->addParametro("stPeriodoInicial", $stPeriodoInicial);
$preview->addParametro("stPeriodoFinal", $stPeriodoFinal);
$preview->preview();
?>
