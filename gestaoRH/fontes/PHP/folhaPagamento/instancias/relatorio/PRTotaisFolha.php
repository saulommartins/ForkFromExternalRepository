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
* Processamento para totais da folha
* Data de Criação   : 05/03/2009

* @author Analista      Dagiane Vieira
* @author Desenvolvedor Diego Lemos de Souza

* @package URBEM
* @subpackage

* @ignore # só use se for paginas que o cliente visualiza, se for mapeamento ou classe de negocio não se usa
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
include_once (CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php"                 );

//Define o nome dos arquivos PHP
$stPrograma = "TotaisFolha";
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
$stCompetencia = substr($rsPeriodoMovimentacao->getCampo("dt_final"),3,7);

$stCodigos = "";
switch ($_POST["stTipoFiltro"]) {
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
    case "atributo_servidor_grupo":
    case "atributo_pensionista_grupo":
         $inCodAtributo = $_POST["inCodAtributo"];
         $inCodCadastro = $_POST["inCodCadastro"];
         $stNomeAtributo = "Atributo_".$inCodAtributo."_".$inCodCadastro;
         if (is_array($_POST[$stNomeAtributo."_Selecionados"])) {
             $inArray = 1;
             $stValores = implode(",",$_POST[$stNomeAtributo."_Selecionados"]);
         } else {
             $inArray = 0;
             $stValores = $_POST[$stNomeAtributo];
         }
         $stCodigos = $inArray."#".$inCodAtributo."#".$stValores;
         break;
}

$inCodBancos = "";
if (is_array($_POST["inCodBancoSelecionados"])) {
    $inCodBancos = implode(",",$_POST["inCodBancoSelecionados"]);
}

$stSituacao = "";
if ($_POST["stSituacaoAtivos"]) {
    $stSituacao .= $_POST["stSituacaoAtivos"].",";
}
if ($_POST["stSituacaoRescindidos"]) {
    $stSituacao .= $_POST["stSituacaoRescindidos"].",";
}
if ($_POST["stSituacaoAposentados"]) {
    $stSituacao .= $_POST["stSituacaoAposentados"].",";
}
if ($_POST["stSituacaoPensionistas"]) {
    $stSituacao .= $_POST["stSituacaoPensionistas"].",";
}
if (trim($stSituacao) != "") {
    $stSituacao = substr($stSituacao,0,strlen($stSituacao)-1);
}

$preview = new PreviewBirt(4,27,23);
$preview->setVersaoBirt( '2.5.0' );
$preview->setFormato("pdf");
$preview->setTitulo('Relatório de Totais da Folha');
$preview->setNomeArquivo('totaisDaFolha');
$preview->addParametro("entidade",Sessao::getCodEntidade($boTransacao));
$preview->addParametro("stEntidade",Sessao::getEntidade());
$preview->addParametro("inCodPeriodoMovimentacao", $rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao"));
$preview->addParametro("stCompetencia", $stCompetencia);
$preview->addParametro("inCodConfiguracao",$_POST["inCodConfiguracao"]);
$preview->addParametro("inCodComplementar",($_POST["inCodComplementar"])?$_POST["inCodComplementar"]:0);
$preview->addParametro("stDesdobramento",$_POST["stDesdobramento"]);
$preview->addParametro("stTipoFiltro",$_POST["stTipoFiltro"]);
$preview->addParametro("stCodigos",$stCodigos);
$preview->addParametro("stOrdenacao",$_POST["stOrdenacao"]);
$preview->addParametro("boAgrupar",($_POST["boAgrupar"]) ? "true" : "false");
$preview->addParametro("boQuebrar",$_POST["boQuebrar"]);
$preview->addParametro("boAgruparEventos",($_POST["boAgruparEventos"]) ? "true" : "false");
$preview->addParametro("boAgruparBanco",($_POST["boAgruparBanco"]) ? "true" : "false");
$preview->addParametro("boQuebrarBanco",$_POST["boQuebrarBanco"]);
$preview->addParametro("inCodConfiguracaoTotais",($_POST["inCodConfiguracaoTotais"])?$_POST["inCodConfiguracaoTotais"]:0);
$preview->addParametro("inCodBancos",$inCodBancos);
$preview->addParametro("stSituacao",$stSituacao);
$preview->preview();

?>
