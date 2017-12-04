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
 * Processamento para Relatório Grade de Horários
 * Data de Criação   : 17/10/2008

 * @author Analista      Dagiane Vieira
 * @author Desenvolvedor Diego Lemos de Souza

 * @package URBEM
 * @subpackage

 * @ignore

 $Id:$
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';

$stAcao = $_REQUEST["stAcao"] ? $_REQUEST["stAcao"] : $_GET["stAcao"];
$arLink = Sessao::read("link");
$stLink = "&pg=".$arLink["pg"]."&pos=".$arLink["pos"];

//Define o nome dos arquivos PHP
$stPrograma = "GradeHorarios";
$pgFilt      = "FL".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgList      = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgForm      = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgProc      = "PR".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgOcul      = "OC".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgJS        = "JS".$stPrograma.".js";

if ($_POST["stEmitir"] == "G") {
    $preview = new PreviewBirt(4,51,5);
    $preview->setVersaoBirt( '2.5.0' );
    $preview->setReturnURL( CAM_GRH_PON_INSTANCIAS."relatorios/FLGradeHorarios.php");
    $preview->setTitulo('Grade de Horários');
    $preview->setNomeArquivo('gradeHorario');
    $preview->addParametro("entidade",Sessao::getCodEntidade($boTransacao));
    $preview->addParametro("stEntidade",Sessao::getEntidade());
    $preview->addParametro("inCodGrade",$_POST["inCodGrade"]);
    $preview->preview();
} else {
    switch ($_REQUEST["stTipoFiltro"]) {
        case "contrato":
        case "cgm_contrato":
            include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php");
            foreach (Sessao::read("arContratos") as $arContrato) {
                $stCodContratos .= $arContrato["cod_contrato"].",";
            }
            $stCodigos = substr($stCodContratos,0,strlen($stCodContratos)-1);
            break;
        case "lotacao_grupo":
            $stCodigos = implode(",",$_POST["inCodLotacaoSelecionados"]);
            break;
        case "local_grupo":
            $stCodigos = implode(",",$_POST["inCodLocalSelecionados"]);
            break;
        case "reg_sub_fun_esp_grupo":
             $stCodigos  = implode(",",$_REQUEST["inCodRegimeSelecionadosFunc"])."#";
             $stCodigos .= implode(",",$_REQUEST["inCodSubDivisaoSelecionadosFunc"])."#";
             $stCodigos .= implode(",",$_REQUEST["inCodFuncaoSelecionados"])."#";
             if (is_array($_REQUEST["inCodEspecialidadeSelecionadosFunc"])) {
                 $stCodigos .= implode(",",$_REQUEST["inCodEspecialidadeSelecionadosFunc"]);
             }
            break;
    }
    include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
    $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
    $obTFolhaPagamentoPeriodoMovimentacao->setDado('mes', $_POST["inCodMes"]);
    $obTFolhaPagamentoPeriodoMovimentacao->setDado('ano', $_POST["inAno"]);
    $obTFolhaPagamentoPeriodoMovimentacao->recuperaPeriodoMovimentacaoDaCompetencia($rsPeriodoMovimentacao);

    if (isset($_POST["boAgrupar"])) {
        $preview = new PreviewBirt(4,51,8);
        $preview->setVersaoBirt( '2.5.0' );
        $preview->setReturnURL( CAM_GRH_PON_INSTANCIAS."relatorios/FLGradeHorarios.php");
        $preview->setTitulo('Servidores / Grade');
        $preview->setNomeArquivo('servidoresGradeAgrupamento');
        $preview->addParametro("entidade",Sessao::getCodEntidade($boTransacao));
        $preview->addParametro("stEntidade",Sessao::getEntidade());
        $preview->addParametro("stTipoFiltro", $_POST["stTipoFiltro"]);
        $preview->addParametro("stCodigos", $stCodigos);
        $preview->addParametro("stOrdem", $_POST["stOrdenacao"]);
        $preview->addParametro("inCodPeriodoMovimentacao", $rsPeriodoMovimentacao->getCampo('cod_periodo_movimentacao'));
        $preview->addParametro("boAgrupar", (isset($_POST["boAgrupar"])) ? "true" : "false");
        $preview->addParametro("boQuebrar", (isset($_POST["boQuebrar"])) ? "true" : "false");
        $preview->preview();
    } else {
        $preview = new PreviewBirt(4,51,6);
        $preview->setVersaoBirt( '2.5.0' );
        $preview->setReturnURL( CAM_GRH_PON_INSTANCIAS."relatorios/FLGradeHorarios.php");
        $preview->setTitulo('Servidores / Grade');
        $preview->setNomeArquivo('servidoresGrade');
        $preview->addParametro("entidade",Sessao::getCodEntidade($boTransacao));
        $preview->addParametro("stEntidade",Sessao::getEntidade());
        $preview->addParametro("stTipoFiltro", $_POST["stTipoFiltro"]);
        $preview->addParametro("stCodigos", $stCodigos);
        $preview->addParametro("inCodPeriodoMovimentacao", $rsPeriodoMovimentacao->getCampo('cod_periodo_movimentacao'));
        $preview->addParametro("stOrdem", $_POST["stOrdenacao"]);
        $preview->preview();
    }
}
?>
