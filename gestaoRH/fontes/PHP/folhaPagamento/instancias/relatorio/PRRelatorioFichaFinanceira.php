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
    * Filtro
    * Data de Criação: 29/01/2007

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 32511 $
    $Name$
    $Author: souzadl $
    $Date: 2008-03-19 15:41:28 -0300 (Qua, 19 Mar 2008) $

    * Casos de uso: uc-04.05.35
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");

switch ($_POST["stTipoFiltro"]) {
    case "contrato_todos":
    case "cgm_contrato_todos":
        $stValoresFiltro = "";
        foreach (Sessao::read("arContratos") as $arContrato) {
            $stValoresFiltro .= $arContrato["cod_contrato"].",";
        }
        $stValoresFiltro      = substr($stValoresFiltro,0,-1);
        $stOrdenacaoContratos = "nom_cgm";
        break;
    case "lotacao":
        $inCodLotacaoSelecionados = $_POST["inCodLotacaoSelecionados"];
        $stValoresFiltro          = implode(",",$inCodLotacaoSelecionados);
        $stOrdenacaoContratos     = "desc_orgao";
        break;
    case "local":
        $inCodLocalSelecionados = $_POST["inCodLocalSelecionados"];
        $stValoresFiltro        = implode(",",$inCodLocalSelecionados);
        $stOrdenacaoContratos   = "desc_local";
        break;
    case "evento":
        foreach (Sessao::read("arEventos") as $arEvento) {
            $stEventosFiltro  .= $arEvento["inCodEvento"].",";
        }
        $stEventosFiltro = substr($stEventosFiltro,0,-1);
        $stValoresFiltro = $stEventosFiltro;
        $stOrdenacaoContratos = "nom_cgm";
        break;
}

$inCodMes      = ($_POST['inCodMes'] > 9)?$_POST['inCodMes']:"0".$_POST['inCodMes'];
$inCodMesFinal = ($_POST['inCodMesFinal'] > 9)?$_POST['inCodMesFinal']:"0".$_POST['inCodMesFinal'];

$dtCompetenciaInicial = $inCodMes."/".$_POST['inAno'];
$dtCompetenciaFinal   = $inCodMesFinal."/".$_POST['inAnoFinal'];

$obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();

$stFiltroPeriodo = " AND to_char(dt_final,'mm/yyyy') = '".$dtCompetenciaInicial."'";
$obTFolhaPagamentoPeriodoMovimentacao->recuperaPeriodoMovimentacao($rsPeriodoMovimentacao,$stFiltroPeriodo);
$inCodPeriodoMovimentacaoInicial = $rsPeriodoMovimentacao->getCampo('cod_periodo_movimentacao');

$stFiltroPeriodo = " AND to_char(dt_final,'mm/yyyy') = '".$dtCompetenciaFinal."'";
$obTFolhaPagamentoPeriodoMovimentacao->recuperaPeriodoMovimentacao($rsPeriodoMovimentacao,$stFiltroPeriodo);
$inCodPeriodoMovimentacaoFinal = $rsPeriodoMovimentacao->getCampo('cod_periodo_movimentacao');

$preview = new PreviewBirt(4,27,24);
$preview->setVersaoBirt("2.5.0");
$preview->setFormato("pdf");
$preview->addParametro("stEntidade", Sessao::getEntidade());
$preview->addParametro("entidade", Sessao::getCodEntidade($boTransacao));
$preview->addParametro("stTipoFiltro", $_POST["stTipoFiltro"]);
$preview->addParametro("stValoresFiltro", ($stValoresFiltro)?$stValoresFiltro:"");
$preview->addParametro("inCodPeriodoMovimentacaoInicial", $inCodPeriodoMovimentacaoInicial);
$preview->addParametro("inCodPeriodoMovimentacaoFinal", $inCodPeriodoMovimentacaoFinal);
$preview->addParametro("inCodConfiguracao", ($_POST["inCodConfiguracao"]) ? $_POST["inCodConfiguracao"] : 'NULL' );
$preview->addParametro("inCodComplementar", ($_POST["inCodComplementar"]) ? $_POST["inCodComplementar"] : '0');
$preview->addParametro("stOrdenacaoEventos", $_POST["stOrdenacaoEventos"]);
$preview->addParametro("stOrdenacaoContratos",($stOrdenacaoContratos)?$stOrdenacaoContratos:"nom_cgm");
$preview->preview();

?>
