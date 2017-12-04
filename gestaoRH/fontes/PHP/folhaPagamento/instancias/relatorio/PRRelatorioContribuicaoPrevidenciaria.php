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

    * @author Analista: Dagiane
    * @author Desenvolvedor: André Machado

    * @ignore

    $Revision: 30840 $
    $Name$
    $Autor:$
    $Date: 2008-01-24 07:52:11 -0200 (Qui, 24 Jan 2008) $

    * Casos de uso: uc-04.05.43
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
include_once (CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php"                 );

//Define o nome dos arquivos PHP
$stPrograma = "RelatorioContribuicaoPrevidenciaria";
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

include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPrevidencia.class.php");
include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPrevidenciaPrevidencia.class.php");
include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPrevidenciaRegimeRat.class.php");
$obTFolhaPagamentoPrevidencia = new TFolhaPagamentoPrevidencia();
$obTFolhaPagamentoPrevidenciaPrevidencia = new TFolhaPagamentoPrevidenciaPrevidencia();
$obTFolhaPagamentoPrevidenciaRegimeRat = new TFolhaPagamentoPrevidenciaRegimeRat();
$stFiltro = " WHERE cod_previdencia = ".$_POST["inCodPrevidencia"];
$stFiltro.= " AND vigencia <= TO_DATE('".$rsPeriodoMovimentacao->getCampo('dt_final')."','DD/MM/YYYY')";
$stFiltro.= " ORDER BY vigencia_ordenacao DESC, timestamp DESC LIMIT 1";

$obTFolhaPagamentoPrevidenciaPrevidencia->recuperaTodos($rsPrevidenciaPrevidencia,$stFiltro);

$stFiltro = " AND previdencia_regime_rat.cod_previdencia = ".$_POST["inCodPrevidencia"];
$obTFolhaPagamentoPrevidenciaRegimeRat->recuperaRelacionamento($rsRat,$stFiltro);

$stFiltro = " WHERE cod_previdencia = ".$_POST["inCodPrevidencia"];
$obTFolhaPagamentoPrevidencia->recuperaTodos($rsPrevidencia,$stFiltro);

if (isset($_POST['inCodComplementar'])) {
    $inCodComplementar = $_POST['inCodComplementar'];
} else {
    $inCodComplementar = 0;
}

switch ($_POST["stTipoFiltro"]) {
    case "contrato_todos":
    case "cgm_contrato_todos":
        $stCodigos = "";
        foreach (Sessao::read('arContratos') as $array ) {
            $stCodigos .=  $array['cod_contrato'].",";
        }
        $stCodigos = substr($stCodigos,0,strlen($stCodigos)-1);
        break;
    case "lotacao_grupo":
        $stCodigos = implode(",",$_REQUEST['inCodLotacaoSelecionados']);
        break;
    case "local_grupo":
        $stCodigos = implode(",",$_REQUEST['inCodLocalSelecionados']);
        break;
    case "sub_divisao_grupo":
        $stCodigos = implode(",",$_REQUEST['inCodSubDivisaoSelecionados']);
        foreach ($_REQUEST['inCodRegimeSelecionados'] as $chave => $inCodRegime) {
            $stRegime.= $inCodRegime.' ,';
        }
        $stRegime = substr($stRegime, 0, -1);
        break;
}

if ($_POST["boAgrupar"]) {
    $preview = new PreviewBirt(4,27,20);
    $preview->setVersaoBirt("2.5.0");
    $preview->addParametro("boAgrupar",$_POST["boAgrupar"]);
    $preview->addParametro("boQuebrar",$_POST["boQuebrar"]);
} else {
    $preview = new PreviewBirt(4,27,7);
    $preview->setVersaoBirt("2.5.0");
}

if ($_REQUEST["boAcumularSalCompl"]) {
    $stAcumularSalCompl = 'sim';
} else {
    $stAcumularSalCompl = 'nao';
}

$preview->setReturnURL( CAM_GRH_FOL_INSTANCIAS."relatorio/FLRelatorioContribuicaoPrevidenciaria.php");
$preview->setTitulo('Contribuição Previdenciária');
$preview->setNomeArquivo('contribuicaoPrevidenciaria');
$preview->addParametro("entidade",Sessao::getCodEntidade($boTransacao));
$preview->addParametro("stEntidade",Sessao::getEntidade());
$preview->addParametro("cod_periodo_movimentacao", $rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao"));
$preview->addParametro("cod_previdencia",$_POST["inCodPrevidencia"]);
$preview->addParametro("cod_configuracao",$_POST["inCodConfiguracao"]);
$preview->addParametro("ordenacao",$_POST["stOrdenacao"]);
$preview->addParametro("stTipoFiltro",$_POST["stTipoFiltro"]);
$preview->addParametro("stRegime",$stRegime);
$preview->addParametro("stCodigos",$stCodigos);
$preview->addParametro("periodo_inicial", $rsPeriodoMovimentacao->getCampo("dt_inicial"));
$preview->addParametro("periodo_final", $rsPeriodoMovimentacao->getCampo("dt_final"));
$preview->addParametro("aliquota_rat", $rsRat->getCampo("aliquota_rat") != '' ? number_format($rsRat->getCampo("aliquota_rat"), 2, '.', '') : '');
$preview->addParametro("aliquota_fap", $rsRat->getCampo("aliquota_fap") != '' ? number_format($rsRat->getCampo("aliquota_fap"), 4, '.', '') : '');
$preview->addParametro("aliquota_patronal", $rsPrevidenciaPrevidencia->getCampo("aliquota"));
$preview->addParametro("stSituacaoCadastro",$_POST["stSituacao"]);
$preview->addParametro("stAcumularSalCompl", $stAcumularSalCompl);
$preview->addParametro("inCodComplementar", $inCodComplementar);
$preview->preview();
?>
