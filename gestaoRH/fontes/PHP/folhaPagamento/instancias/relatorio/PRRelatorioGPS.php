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
**** Página de processamento do relaório GPS
**** Data de Criação   : 27/10/2009

**** @author Analista      Dagiane
**** @author Desenvolvedor Cassiano de Vasconcellos Ferreira

**** @package URBEM
**** @subpackage

**** @ignore

     $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php";
include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPrevidencia.class.php";
include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPrevidenciaPrevidencia.class.php";
include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPrevidenciaRegimeRat.class.php";
include_once CAM_GRH_ENT_MAPEAMENTO."TEntidade.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "RelatorioContribuicaoPrevidenciaria";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$obTEntidade = new TEntidade;
$stFiltro = "  AND entidade.cod_entidade=".Sessao::getCodEntidade($boTransacao);
$obTEntidade->recuperaInformacoesCGMEntidade($rsEntidade,$stFiltro);

$obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
$obTFolhaPagamentoPeriodoMovimentacao->setDado("mes", $_REQUEST['inCodMes']);
$obTFolhaPagamentoPeriodoMovimentacao->setDado("ano", $_REQUEST['inAno']);
$obTFolhaPagamentoPeriodoMovimentacao->recuperaPeriodoMovimentacaoDaCompetencia($rsPeriodoMovimentacao);

$obTFolhaPagamentoPrevidencia = new TFolhaPagamentoPrevidencia();
$obTFolhaPagamentoPrevidenciaPrevidencia = new TFolhaPagamentoPrevidenciaPrevidencia();
$obTFolhaPagamentoPrevidenciaRegimeRat = new TFolhaPagamentoPrevidenciaRegimeRat();
$stFiltro = " AND previdencia_previdencia.cod_previdencia = ".$_POST["inCodPrevidencia"];
$obTFolhaPagamentoPrevidenciaPrevidencia->recuperaRelacionamento($rsPrevidenciaPrevidencia,$stFiltro);
$stFiltro = " AND previdencia_regime_rat.cod_previdencia = ".$_POST["inCodPrevidencia"];
$obTFolhaPagamentoPrevidenciaRegimeRat->recuperaRelacionamento($rsRat,$stFiltro);
$stFiltro = " WHERE cod_previdencia = ".$_POST["inCodPrevidencia"];
$obTFolhaPagamentoPrevidencia->recuperaTodos($rsPrevidencia,$stFiltro);
$stCodigos = "";
$stContratos = "";

if (isset($_POST['inCodComplementar'])) {
    $inCodComplementar = $_POST['inCodComplementar'];
} else {
    $inCodComplementar = 0;
}

switch ($_POST["stTipoFiltro"]) {
    case "contrato_todos":
    case "cgm_contrato_todos":
        foreach (Sessao::read('arContratos') as $array ) {
            $stCodigos .=  $array['cod_contrato'].",";
            $stContratos .= $array['inContrato'].",";
        }
        $stCodigos = substr($stCodigos,0,strlen($stCodigos)-1);
        $stContratos = substr($stContratos,0,strlen($stContratos)-1);
        break;
    case "lotacao_grupo":
        $stCodigos = implode(",",$_REQUEST['inCodLotacaoSelecionados']);
        break;
    case "local_grupo":
        $stCodigos = implode(",",$_REQUEST['inCodLocalSelecionados']);
        break;
    case "sub_divisao_grupo":
        $stCodigos = implode(",",$_REQUEST['inCodSubDivisaoSelecionados']);
        break;
}

$stTipoFiltro = $_POST['stTipoFiltro'] == 'sub_divisao_grupo' ? 'regime_subdivisao_grupo' : $_POST['stTipoFiltro'];

$preview = new PreviewBirt(4,27,25);
$preview->setVersaoBirt("2.5.0");

//$preview->setReturnURL( CAM_GRH_FOL_INSTANCIAS."relatorio/FLRelatorioContribuicaoPrevidenciaria.php");
$preview->setTitulo('Guia De Previdência Social');
$preview->setNomeArquivo('guiaPrevidenciaSocial');
$preview->addParametro("entidade",Sessao::getCodEntidade($boTransacao));
$preview->addParametro("stEntidade",Sessao::getEntidade());

$preview->setReturnURL( CAM_GRH_FOL_INSTANCIAS."relatorio/FLRelatorioGPS.php");
$preview->addParametro("cod_periodo_movimentacao", $rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao"));
$preview->addParametro("stCompetencia", str_pad($_REQUEST['inCodMes'], 2, '0', STR_PAD_LEFT) .'/'.$_REQUEST['inAno']);
$preview->addParametro("cod_previdencia",$_POST["inCodPrevidencia"]);
$preview->addParametro("cod_configuracao",$_POST["inCodConfiguracao"]);
$preview->addParametro("ordenacao",$_POST["stOrdenacao"]);
$preview->addParametro("stTipoFiltro",$stTipoFiltro);
$preview->addParametro("boAgrupar",$_POST["boAgrupar"]?$_POST["boAgrupar"]:'false');
$preview->addParametro("boQuebrar",$_POST["boQuebrar"]?$_POST["boQuebrar"]:'false');
$preview->addParametro("stCodigos",$stCodigos);
$preview->addParametro("stContratos",$stContratos);
$preview->addParametro("periodo_inicial", $rsPeriodoMovimentacao->getCampo("dt_inicial"));
$preview->addParametro("periodo_final", $rsPeriodoMovimentacao->getCampo("dt_final"));
$preview->addParametro("aliquota_rat", $rsRat->getCampo("aliquota_rat")?$rsRat->getCampo("aliquota_rat"):0);
$preview->addParametro("aliquota_patronal", $rsPrevidenciaPrevidencia->getCampo("aliquota")?$rsPrevidenciaPrevidencia->getCampo("aliquota"):0);
$preview->addParametro("stSituacaoCadastro",$_POST["stSituacao"]);
$preview->addParametro("inNumVias",$_POST['boEmitirCopia'] == 'true' ? 2 : 1);
$preview->addParametro("stAcumularSalCompl",'nao');
$preview->addParametro("inCodComplementar", $inCodComplementar);
$preview->preview();
?>
