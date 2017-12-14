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
    * Processamento
    * Data de Criação : 14/08/2008

    * @author Analista: Dagiane Vieira
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    * Casos de uso: uc-04.05.34
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
include_once (CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php"                 );

//Define o nome dos arquivos PHP
$stPrograma = "RelatorioEventoPorContrato";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

//periodo de movimentação
$inMesFinal =( $_POST["inCodMes"]<10 ) ? "0".$_POST["inCodMes"]:$_POST["inCodMes"];
$dtCompetenciaFinal = $inMesFinal."/".$_POST["inAno"];
$stFiltro = " AND to_char(dt_final,'mm/yyyy') = '".$dtCompetenciaFinal."'";
$obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
$obTFolhaPagamentoPeriodoMovimentacao->recuperaPeriodoMovimentacao($rsPeriodoMovimentaco,$stFiltro);

$stCodigos = "";
switch ($_POST["stTipoFiltro"]) {
    case "lotacao":
        $stCodigos = trim(implode(",",$_POST["inCodLotacaoSelecionados"]));
        break;
    case "local":
        $stCodigos = trim(implode(",",$_POST["inCodLocalSelecionados"]));
        break;
    case "contrato_todos":
    case "contrato":
        foreach (Sessao::read("arContratos") as $arContrato) {
            $stCodigos .= $arContrato["cod_contrato"].",";
        }
        $stCodigos = substr($stCodigos,0,strlen($stCodigos)-1);
        break;
    case "evento":
        foreach (Sessao::read("arEventos") as $arEvento) {
            $stCodigos .= $arEvento["inCodEvento"].",";
        }
        $stCodigos = substr($stCodigos,0,strlen($stCodigos)-1);
        break;
}
$stCodigos = ($stCodigos == "")?"null":$stCodigos;

$preview = new PreviewBirt(4,27,18);
$preview->setVersaoBirt( '2.5.0' );
$preview->setReturnURL( CAM_GRH_FOL_INSTANCIAS."relatorio/FLRelatorioEventoPorContrato.php");
$preview->setTitulo('Eventos Por Contrato');
$preview->setNomeArquivo('eventosPorContrato');
$preview->addParametro("entidade", Sessao::getCodEntidade($boTransacao));
$preview->addParametro("stEntidade", Sessao::getEntidade());
$preview->addParametro("inCodPeriodoMovimentacao", $rsPeriodoMovimentaco->getCampo("cod_periodo_movimentacao"));
$preview->addParametro("stTipoFiltro", $_POST["stTipoFiltro"]);
$preview->addParametro("stCodigos", $stCodigos);
$preview->addParametro("inCodConfiguracao", $_POST["inCodConfiguracao"]);
$preview->addParametro("inCodComplementar", $_POST["inCodComplementar"]);
$preview->preview();

?>
