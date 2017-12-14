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
    * Página de Processamento para Relatório de Cargos
    * Data de Criação   : 19/01/2009

    * @author Analista: Dagiane Vieira
    * @author Desenvolvedor: Alex Cardoso

    * @ignore

    $Id: $

    * Casos de uso: uc-04.04.11
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
include_once( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                     );

//Define o nome dos arquivos PHP
$stPrograma = "RelatorioCargo";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

# Seta Filtros
$stCodigos = "";
$inCodAtributo = 0;

switch ($_POST["stTipoFiltro"]) {
    case "contrato":
        $arContratos = Sessao::read("arContratos");
        foreach ($arContratos as $chave => $arContrato) {
            $stCodigos .= $arContrato["cod_contrato"].",";
        }
        $stCodigos = substr($stCodigos, 0, -1);
        break;

    case "lotacao_grupo":
        $stCodigos = implode(',', $_POST["inCodLotacaoSelecionados"]);
        break;

    case "local_grupo":
        $stCodigos = implode(',', $_POST["inCodLocalSelecionados"]);
        break;

    case "sub_divisao_grupo":
        $stCodigos = implode(',', $_POST["inCodSubDivisaoSelecionados"]);
        break;

    case "reg_sub_car_esp_grupo":
        $stCodigos  = implode(",",$_REQUEST["inCodRegimeSelecionados"])."#";
        $stCodigos .= implode(",",$_REQUEST["inCodSubDivisaoSelecionados"])."#";
        $stCodigos .= implode(",",$_REQUEST["inCodCargoSelecionados"])."#";
        if (is_array($_REQUEST["inCodEspecialidadeSelecionados"])) {
            $stCodigos .= implode(",",$_REQUEST["inCodEspecialidadeSelecionados"]);
        }
        break;
    case "reg_sub_fun_esp_grupo":
        $stCodigos  = implode(",",$_REQUEST["inCodRegimeSelecionadosFunc"])."#";
        $stCodigos .= implode(",",$_REQUEST["inCodSubDivisaoSelecionadosFunc"])."#";
        $stCodigos .= implode(",",$_REQUEST["inCodFuncaoSelecionados"])."#";
        if (is_array($_REQUEST["inCodEspecialidadeSelecionados"])) {
            $stCodigos .= implode(",",$_REQUEST["inCodEspecialidadeSelecionados"]);
        }
        break;

    case "atributo_servidor_grupo":
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

$obRFolhaPagamentoPeriodoMovimentacao = new RFolhaPagamentoPeriodoMovimentacao();
$obRFolhaPagamentoPeriodoMovimentacao->listarUltimaMovimentacao($rsUltimaMovimentacao);

if ($rsUltimaMovimentacao->getNumLinhas() > 0) {
    $arData        = explode("/",$rsUltimaMovimentacao->getCampo('dt_final'));
    $stCompetencia = $arData[2]."-".$arData[1]."-".$arData[0];
}

//Versão BIRT
$preview = new PreviewBirt(4,22,1);
$preview->setVersaoBirt('2.5.0');
$preview->setReturnURL( CAM_GRH_PES_INSTANCIAS."relatorio/FLRelatorioCargo.php");
$preview->setTitulo('Relatório de Cargos');
$preview->setNomeArquivo('cargos');
$preview->addParametro("stEntidade", trim(Sessao::getEntidade()));
$preview->addParametro("entidade", trim(Sessao::getCodEntidade($boTransacao)));
$preview->addParametro("stCompetencia", $stCompetencia);
$preview->addParametro("stApresentaPadroes",        $_POST["stApresentaPadroes"]?'true':'false');
$preview->addParametro("stApresentaPadroesValor", $_POST["stApresentaPadroesValor"]?'true':'false');
$preview->addParametro("stApresentaProgressoes",    $_POST["stApresentaProgressoes"]?'true':'false');
$preview->addParametro("stApresentaSaldoVagas",     $_POST["stApresentaSaldoVagas"]?'true':'false');
$preview->addParametro("stApresentaReajustes",      $_POST["stApresentaReajustes"]?'true':'false');
$preview->addParametro("stApresentaServidores",     $_POST["stApresentaServidores"]?'true':'false');
$preview->addParametro("stTipoFiltro", trim($_POST["stTipoFiltro"]));
$preview->addParametro("stCodigos", $stCodigos);
$preview->addParametro("stOrdenacao", $_POST['stOrdenacao']);
$preview->addParametro("boAgrupar", isset($_POST["boAgrupar"])?$_POST["boAgrupar"]:'false');
$preview->addParametro("boQuebrar", isset($_POST["boQuebrar"])?$_POST["boQuebrar"]:'false');
$preview->addParametro("inExercicio", Sessao::getExercicio());
$preview->setFormato('pdf');
$preview->preview();
?>
