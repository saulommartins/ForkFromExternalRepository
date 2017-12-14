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
    * Página de Oculto do Relatório de Cadastro de Estagiários
    * Data de Criação : 19/04/2007

    * @author Analista: Dagiane
    * @author Desenvolvedor: Alexandre Melo

    * @ignore

    * Casos de uso: uc-04.05.25
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
include_once (CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php"                 );

//Define o nome dos arquivos PHP
$stPrograma = "RelatorioFGTS";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$preview = new PreviewBirt(4,27,1);
$preview->setVersaoBirt("2.5.0");
$preview->setReturnURL( CAM_GRH_FOL_INSTANCIAS."relatorio/FLRelatorioFGTS.php");
$preview->setTitulo('Relatório de FGTS');
$preview->setNomeArquivo('relatorioFGTS');
$preview->addParametro("stEntidade", Sessao::getEntidade());
$preview->addParametro("stcodEntidade", Sessao::getCodEntidade($boTransacao));

//periodo de movimentação
$inMesFinal =( $_POST["inCodMes"]<10 ) ? "0".$_POST["inCodMes"]:$_POST["inCodMes"];
$dtCompetenciaFinal = $inMesFinal."/".$_POST["inAno"];
$stFiltro = " AND to_char(dt_final,'mm/yyyy') = '".$dtCompetenciaFinal."'";
$obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
$obTFolhaPagamentoPeriodoMovimentacao->recuperaPeriodoMovimentacao($rsPeriodoMovimentaco,$stFiltro);
$periodoMovimentacao = $rsPeriodoMovimentaco->getCampo("cod_periodo_movimentacao");

if (!$rsPeriodoMovimentaco->eof()) {
    $preview->addParametro("periodoMovimentacao", $periodoMovimentacao);
    $preview->addParametro("stCompetencia", $dtCompetenciaFinal);
    $preview->addParametro("stPeriodoInicial", $rsPeriodoMovimentaco->getCampo("dt_inicial"));
    $preview->addParametro("stPeriodoFinal", $rsPeriodoMovimentaco->getCampo("dt_final"));
}

//ordenação
if ($_POST["stOrdenacao"] == "A") {
    $preview->addParametro("ordenacao", "nom_cgm");
} else {
    $preview->addParametro("ordenacao", "registro");
}

//tipo complementar
if ($_POST["inCodComplementar"] != "") {
    $inCodComplementar = $_POST["inCodComplementar"];
    $preview->addParametro("inCodComplementar", "$inCodComplementar");
} else {
    $preview->addParametro("inCodComplementar", "");
}

//geral - listado pela query no birt

//tipo de cálculo
$inConfiguracao = $_POST["inCodConfiguracao"];
$preview->addParametro("inCodConfiguracao", "$inConfiguracao");

//matrícula - CGM/Matrícula
$arrContrato = "";
if ($_POST['stTipoFiltro'] == 'contrato' || $_POST['stTipoFiltro'] == 'cgm_contrato') {
    if (count(Sessao::read('arContratos')) > 0) {
        foreach (Sessao::read('arContratos') as $array ) {
            $arrContrato .=  $array['cod_contrato'].",";
        }
        $arrContrato = substr($arrContrato, 0, strlen($arrContrato)-1);

    }
}
//lotação - local
$arrLotacaoSelecionados = "";
if (trim($_POST['stTipoFiltro']) == "lotacao_grupo") {
    if (count($_POST['inCodLotacaoSelecionados']) > 0) {
        foreach ($_POST['inCodLotacaoSelecionados'] as $array) {
            $arrLotacaoSelecionados.= $array.",";
        }
        $arrLotacaoSelecionados = substr($arrLotacaoSelecionados, 0, strlen($arrLotacaoSelecionados)-1);
    }
}

$arrLocalSelecionados = "";
if (trim($_POST['stTipoFiltro']) == "local_grupo") {
    if (count($_POST['inCodLocalSelecionados']) > 0) {
        foreach ($_POST['inCodLocalSelecionados'] as $array) {
            $arrLocalSelecionados.= $array.",";
        }
        $arrLocalSelecionados = substr($arrLocalSelecionados, 0, strlen($arrLocalSelecionados)-1);
    }
}

if (trim($_POST["stTipoFiltro"]) == "local_grupo") {
    $preview->addParametro( "boAgruparLocal", ($_POST["boAgrupar"]) ? "true" : "false");
    $preview->addParametro( "boQuebrarLocal", ($_POST["boQuebrar"]) ? "true" : "false");
} else {
    $preview->addParametro( "boAgruparLocal", "false");
    $preview->addParametro( "boQuebrarLocal", "false");
}

if (trim($_POST["stTipoFiltro"]) == "lotacao_grupo") {
    $preview->addParametro( "boAgruparLotacao", ($_POST["boAgrupar"]) ? "true" : "false");
    $preview->addParametro( "boQuebrarLotacao", ($_POST["boQuebrar"]) ? "true" : "false");
} else {
    $preview->addParametro( "boAgruparLotacao", "false");
    $preview->addParametro( "boQuebrarLotacao", "false");
}

$preview->addParametro( "codContrato",$arrContrato);
$preview->addParametro( "codLocal", $arrLocalSelecionados);
$preview->addParametro( "codOrgao", $arrLotacaoSelecionados);
$preview->preview();
?>
