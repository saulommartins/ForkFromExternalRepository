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
    * Página de Processamento do Recibo de Pensão Judicial
    * Data de Criação: 25/06/2008

    * @author Analista: Dagiane Vieira
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    * Casos de uso: uc-04.05.65

    $Id:$
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "ReciboPensaoJudicial";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$stCodigosFiltro = "";
switch ($_POST["stTipoFiltro"]) {
    case "cgm_dependente":
        foreach (Sessao::read("arCGMDependentes") as $arDependente) {
            $stCodigosFiltro .= $arDependente["numcgm"].",";
        }
        $stCodigosFiltro = substr($stCodigosFiltro,0,strlen($stCodigosFiltro)-1);
        break;
    case "matricula_dependente_servidor":
        foreach (Sessao::read("arDependentes") as $arDependente) {
            $stCodigosFiltro .= $arDependente["cod_dependente"].",";
        }
        $stCodigosFiltro = substr($stCodigosFiltro,0,strlen($stCodigosFiltro)-1);
        break;
    case "lotacao_grupo":
        $stCodigosFiltro = implode($_POST["inCodLotacaoSelecionados"],",");
        break;
    case "local_grupo":
        $stCodigosFiltro = implode($_POST["inCodLocalSelecionados"],",");
        break;
}

include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
$obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
$obTFolhaPagamentoPeriodoMovimentacao->setDado("mes",$_POST["inCodMes"]);
$obTFolhaPagamentoPeriodoMovimentacao->setDado("ano",$_POST["inAno"]);
$obTFolhaPagamentoPeriodoMovimentacao->recuperaPeriodoMovimentacaoDaCompetencia($rsPeriodoMovimentacao);
$stCompetencia = substr($rsPeriodoMovimentacao->getCampo("dt_final"),3,7);

include_once(CAM_GRH_ENT_MAPEAMENTO."TEntidade.class.php");
$obTEntidade = new TEntidade();
$stFiltro  = " AND entidade.cod_entidade = ".Sessao::getCodEntidade($boTransacao);
$stFiltro .= " AND entidade.exercicio = '".Sessao::getExercicio()."'";
$obTEntidade->recuperaInformacoesCGMEntidade($rsEntidade,$stFiltro);

$stFiltro  = " WHERE cod_entidade = ".Sessao::getCodEntidade($boTransacao);
$stFiltro .= "   AND exercicio = '".Sessao::getExercicio()."'";
$obTEntidade->recuperaLogotipoEntidade($rsLogotipo,$stFiltro);
if ($rsLogotipo->getNumLinhas() < 0) {
    $stLogotipo = '../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/'.sistemalegado::pegaConfiguracao('logotipo',2,Sessao::getExercicio());
} else {
    $stLogotipo = '../../../../../../gestaoFinanceira/fontes/PHP/orcamento/anexos/'.$rsLogotipo->getCampo("logotipo");
}

$stOrdem = "";
if ($_POST["boAgrupar"]) {
    if ($_POST["stTipoFiltro"] == "lotacao_grupo") {
        $stOrdem = "o";
    }
    if ($_POST["stTipoFiltro"] == "local_grupo") {
        $stOrdem = "l";
    }
}
$stOrdem .= $_POST["stOrdenacao"];

$preview = new PreviewBirt(4,27,17);
$preview->setVersaoBirt( '2.5.0' );
$preview->setReturnURL( CAM_GRH_FOL_INSTANCIAS."relatorio/".$pgFilt);
$preview->addParametro("stEntidade", Sessao::getEntidade());
$preview->addParametro("stNomeEntidade", $rsEntidade->getCampo("nom_cgm"));
$preview->addParametro("stCNPJEntidade", $rsEntidade->getCampo("cnpj"));
$preview->addParametro("stLogotipoEntidade", $stLogotipo);
$preview->addParametro("entidade", Sessao::getCodEntidade($boTransacao));
$preview->addParametro("stTipoFiltro", $_POST["stTipoFiltro"]);
$preview->addParametro("stCodigosFiltro", $stCodigosFiltro);
$preview->addParametro("inCodPeriodoMovimentacao", $rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao"));
$preview->addParametro("stCompetencia", $stCompetencia);
$preview->addParametro("inFolha", $_POST["inCodConfiguracao"]);
$preview->addParametro("inCodComplementar", ($_POST["inCodComplementar"]) ? $_POST["inCodComplementar"] : 0);
$preview->addParametro("stOrdem", $stOrdem);
$preview->addParametro("boDuplicar", ($_POST["boCopia"]) ? "true" : "false");

$preview->preview();

?>
