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
    * Página de Filtro do Relatório de IRRF
    * Data de Criação : 07/08/2007

    * @author Desenvolvedor: André Machado

    * Casos de uso: uc-04.05.28

    $Id: PRRelatorioIRRF.php 66258 2016-08-03 14:25:21Z evandro $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
include_once (CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php"                 );

//Define o nome dos arquivos PHP
$stPrograma = "RelatorioIRRF";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
$obTFolhaPagamentoPeriodoMovimentacao->setDado("mes", $_REQUEST['inCodMes']);
$obTFolhaPagamentoPeriodoMovimentacao->setDado("ano", $_REQUEST['inAno']);
$obTFolhaPagamentoPeriodoMovimentacao->recuperaPeriodoMovimentacaoDaCompetencia($rsPeriodoMovimentacao);

$stCodigos = "";
switch ($_POST["stTipoFiltro"]) {
    case "contrato_todos":
    case "cgm_contrato_todos":
    case "contrato_rescisao":
    case "cgm_contrato_rescisao":
        foreach (Sessao::read("arContratos") as $arContrato) {
            $stCodigos .= $arContrato["cod_contrato"].",";
        }
        $stCodigos = substr($stCodigos,0,strlen($stCodigos)-1);
        break;
    case "contrato_pensionista":
    case "cgm_contrato_pensionista":
        foreach (Sessao::read("arPensionistas") as $arContrato) {
            $stCodigos .= $arContrato["cod_contrato"].",";
        }
        $stCodigos = substr($stCodigos,0,strlen($stCodigos)-1);
        break;
    case "lotacao_grupo":
        $stCodigos = implode(",",$_POST["inCodLotacaoSelecionados"]);
        break;
    case "local_grupo":
        $stCodigos = implode(",",$_POST["inCodLocalSelecionados"]);
        break;
}

$stOrdenacao = ($_POST["stOrdenacao"] == "A") ? " nom_cgm" : " registro";

$preview = new PreviewBirt(4,27,4);
$preview->setVersaoBirt( '2.5.0' );
$preview->setReturnURL( CAM_GRH_FOL_INSTANCIAS."relatorio/FLRelatorioIRRF.php");
$preview->setTitulo('Relatório de IRRF');
$preview->setNomeArquivo('relatorioIRRF');
$preview->addParametro("entidade",Sessao::getCodEntidade($boTransacao));
$preview->addParametro("stEntidade",Sessao::getEntidade());
$preview->addParametro("inCodPeriodoMovimentacao", $rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao"));
$preview->addParametro("dtPeriodoInicial", $rsPeriodoMovimentacao->getCampo("dt_inicial"));
$preview->addParametro("dtPeriodoFinal", $rsPeriodoMovimentacao->getCampo("dt_final"));
$preview->addParametro("stSituacao",$_POST["stSituacao"]);
$preview->addParametro("inCodConfiguracao",$_POST["inCodConfiguracao"]);
$preview->addParametro("inCodComplementar",$_POST["inCodComplementar"]);
$preview->addParametro("stDesdobramento",$_POST["stDesdobramento"]);
$preview->addParametro("stTipoFiltro",$_POST["stTipoFiltro"]);
$preview->addParametro("stCodigos",$stCodigos);
$preview->addParametro("stOrdenacao",$stOrdenacao);
$preview->addParametro("boAgrupar",$_POST["boAgrupar"]);
$preview->addParametro("boQuebrar",$_POST["boQuebrar"]);
$preview->preview();
?>
