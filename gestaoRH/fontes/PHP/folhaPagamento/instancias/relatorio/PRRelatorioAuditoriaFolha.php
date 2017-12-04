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
    * Data de Cria??o: 29/01/2007

    * @author Analista: Vandr? Miguel Ramos
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

switch ($_POST['stTipoFiltro']) {
    case "contrato_todos":
    case "cgm_contrato_todos":
        $stTipoFiltro    = "contrato_todos";
        $stValoresFiltro = "";
        foreach (Sessao::read("arContratos") as $arContrato) {
            $stValoresFiltro .= $arContrato['cod_contrato'].",";
        }
        $stValoresFiltro      = substr($stValoresFiltro,0,-1);
        $stOrdenacaoContratos = "nom_cgm";
        break;

    case "lotacao_grupo":
        $stTipoFiltro             = "lotacao";
        $inCodLotacaoSelecionados = $_REQUEST['inCodLotacaoSelecionados'];
        $stValoresFiltro          = implode(",",$inCodLotacaoSelecionados);
        $stOrdenacaoContratos     = "desc_orgao";
        break;

    case "local_grupo":
        $stTipoFiltro             = "local";
        $inCodLocalSelecionados = $_REQUEST['inCodLocalSelecionados'];
        $stValoresFiltro        = implode(",",$inCodLocalSelecionados);
        $stOrdenacaoContratos   = "desc_local";
        break;

    case "evento_multiplo":
        $stTipoFiltro            = "evento"; //tradução para reutilizar PL
        $stOrdenacaoContratos    = "nom_cgm";

        foreach ($_REQUEST['inCodEventoSelecionados'] as $evento) {
            $evento = str_pad($evento, 5, '0', STR_PAD_LEFT);
            $stValoresFiltro .= "'".$evento."',";
        }

        $stValoresFiltro = substr($stValoresFiltro, 0, -1);

        break;
    case "geral":
        $stTipoFiltro = "geral";
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

switch ($stTipoFiltro) {
    case "lotacao":
        if ($_POST['boAgrupar'] = true)
            $preview = new PreviewBirt(4,27,27);
    break;

    case "local":
        if($_POST['boAgrupar'] = true)
            $preview = new PreviewBirt(4,27,28);
    break;

    default:
        $preview = new PreviewBirt(4,27,26);
    break;
}

$naturezaFiltro = array();
if ($_POST['boProvento']) {
    if ($preview->inCodRelatorio == 26)
        $naturezaFiltro[] = "P";
    else
        $naturezaFiltro[] = "'P'";
}

if ($_POST['boDesconto']) {
    if ($preview->inCodRelatorio == 26)
        $naturezaFiltro[] = "D";
    else
        $naturezaFiltro[] = "'D'";
}

if ($_POST['boInformativo']) {
    if ($preview->inCodRelatorio == 26)
        $naturezaFiltro[] = "I";
    else
        $naturezaFiltro[] = "'I'";
}

if ($_POST['boBase']) {
    if ($preview->inCodRelatorio == 26)
        $naturezaFiltro[] = "B";
    else
        $naturezaFiltro[] = "'B'";
}

$naturezaFiltro = implode($naturezaFiltro, ',') ? implode($naturezaFiltro, ','):"";

//Pegar sempre o Maior Periodo de Movimentação
$inPeriodoSituacao = $inCodPeriodoMovimentacaoInicial > $inCodPeriodoMovimentacaoFinal ? $inCodPeriodoMovimentacaoInicial : $inCodPeriodoMovimentacaoFinal;

$preview->setVersaoBirt("2.5.0");
$preview->setTitulo('Auditoria na folha');
$preview->addParametro("st_entidade"                          , Sessao::getEntidade());
$preview->addParametro("st_data"                              , Sessao::getExercicio().'-01-01');
$preview->addParametro("tipo_filtro"                          , $stTipoFiltro);
$preview->addParametro("valores_filtro"                       , ($stValoresFiltro)?$stValoresFiltro:"");
$preview->addParametro("natureza_filtro"                      , $naturezaFiltro);
$preview->addParametro("agrupar"                              , (isset($_POST['boAgrupar']))?1:0);
$preview->addParametro("page_break"                           , (isset($_POST['boQuebrar']))?1:0);
$preview->addParametro("ordem_filtro"                         , $_POST['stOrdenacao']);
$preview->addParametro("cod_periodo_movimentacao_comparacao"  , $inCodPeriodoMovimentacaoInicial);
$preview->addParametro("cod_periodo_movimentacao_analise"     , $inCodPeriodoMovimentacaoFinal);
$preview->addParametro("competencia_comparacao"               , $dtCompetenciaInicial);
$preview->addParametro("competencia_analise"                  , $dtCompetenciaFinal);
$preview->addParametro("periodo_situacao"                     , $inPeriodoSituacao);
$preview->preview();

?>
