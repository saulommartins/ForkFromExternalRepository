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
    * Página de Processamento do Relatorio de Comprovante de Rendimento IRRF
    * Data de Criação : 22/11/2007

    * @author Analista: Dagiane Vieira
    * @author Desenvolvedor: Alex Cardoso

    * @ignore

    $Id: PRComprovanteRendimentosIRRF.php 66258 2016-08-03 14:25:21Z evandro $

    * Casos de uso: uc-04.05.37
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
include_once (CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php"                 );

$link = Sessao::read("link");
$stAcao = $_REQUEST["stAcao"] ? $_REQUEST["stAcao"] : $_GET["stAcao"];
$stLink = "&pg=".$link["pg"]."&pos=".$link["pos"];

foreach ($_POST as $key=>$value) {
    $stLink .= $key."=".$value."&";
}

//Define o nome dos arquivos PHP
$stPrograma = "ComprovanteRendimentosIRRF";
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

//NOME DO RESPONSAVEL
include_once(CAM_GA_CGM_MAPEAMENTO."TCGM.class.php");
$obTCGM = new TCGM;
$obTCGM->setDado("numcgm", $_POST['inNumCGMResponsavel']);
$obTCGM->recuperaPorChave($rsCGM);
$stNomeResponsavel = $rsCGM->getCampo("nom_cgm");
// DATA FINAL ULTIMA COMPETENCIA ANO SELECIONADO
include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
$obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
$stFiltro = " WHERE  to_char(dt_final,'yyyy') = '".$_POST["inAnoCompetencia"]."'";
$stOrdem = " ORDER BY cod_periodo_movimentacao";
$obTFolhaPagamentoPeriodoMovimentacao->recuperaTodos($rsPeriodos,$stFiltro,$stOrdem);
$rsPeriodos->setUltimoElemento();
$dtFinalCompetencia = $rsPeriodos->getCampo("dt_final");
$arFinalCompetencia = explode("/",$rsPeriodos->getCampo("dt_final"));
$dtFinalCompetencia = $arFinalCompetencia[2]."-".$arFinalCompetencia[1]."-".$arFinalCompetencia[0];

$stValoresFiltro = "";
switch ($_REQUEST['stTipoFiltro']) {
    case "contrato":
    case "contrato_rescisao":
    case "contrato_aposentado":
    case "contrato_todos":
    case "cgm_contrato":
    case "cgm_contrato_rescisao":
    case "cgm_contrato_aposentado":
    case "cgm_contrato_todos":
        $arContratos = Sessao::read("arContratos");
        foreach ($arContratos as $arContrato) {
            $stValoresFiltro .= $arContrato["cod_contrato"].",";
        }
        $stValoresFiltro = substr($stValoresFiltro,0,strlen($stValoresFiltro)-1);
        break;
    case "contrato_pensionista":
    case "cgm_contrato_pensionista":
        $arContratos = Sessao::read("arPensionistas");
        foreach ($arContratos as $arContrato) {
            $stValoresFiltro .= $arContrato["cod_contrato"].",";
        }
        $stValoresFiltro = substr($stValoresFiltro,0,strlen($stValoresFiltro)-1);
        break;
    case "lotacao_grupo":
        $stValoresFiltro = implode(",",$_REQUEST["inCodLotacaoSelecionados"]);
        break;
    case "local_grupo":
        $stValoresFiltro = implode(",",$_REQUEST["inCodLocalSelecionados"]);
        break;
    case "reg_sub_fun_esp_grupo":
        $stValoresFiltro  = implode(",",$_REQUEST["inCodRegimeSelecionadosFunc"])."#";
        $stValoresFiltro .= implode(",",$_REQUEST["inCodSubDivisaoSelecionadosFunc"])."#";
        $stValoresFiltro .= implode(",",$_REQUEST["inCodFuncaoSelecionados"])."#";
        if (is_array($_REQUEST["inCodEspecialidadeSelecionadosFunc"])) {
            $stValoresFiltro .= implode(",",$_REQUEST["inCodEspecialidadeSelecionadosFunc"]);
        }
        break;
    case "atributo_servidor_grupo":
    case "atributo_pensionista_grupo":
        $inCodAtributo = $_REQUEST["inCodAtributo"];
        $inCodCadastro = $_REQUEST["inCodCadastro"];
        $stNomeAtributo = "Atributo_".$inCodAtributo."_".$inCodCadastro;
        if (is_array($_REQUEST[$stNomeAtributo."_Selecionados"])) {
            $inArray = 1;
            $stValores     = implode(",",$_REQUEST[$stNomeAtributo."_Selecionados"]);
        } else {
            $inArray = 0;
            $stValores     = $_REQUEST[$stNomeAtributo];
        }
        $stValoresFiltro = $inArray."#".$inCodAtributo."#".$stValores;
        break;
}

$preview = new PreviewBirt(4,27,13);
$preview->setVersaoBirt( '2.5.0' );
$preview->setFormato("pdf");
$preview->setReturnURL( CAM_GRH_FOL_INSTANCIAS."relatorio/FLComprovanteRendimentosIRRF.php");
$preview->setTitulo('Comprovante de Rendimentos IRRF');
$preview->setNomeArquivo('comprovanteRendimentosIRRF');
$preview->addParametro("entidade",Sessao::getCodEntidade($boTransacao));
$preview->addParametro("stEntidade",Sessao::getEntidade());
$preview->addParametro("stData", 	   	   date("d/m/Y"));
$preview->addParametro("stAnoCompetencia", $_POST["inAnoCompetencia"]);
$preview->addParametro("stSituacao", 	   $_POST["stSituacao"]);
$preview->addParametro("stTipoFiltro", 	   $_POST["stTipoFiltro"]);
$preview->addParametro("stValoresFiltro",  $stValoresFiltro);
$preview->addParametro("stResponsavel",	   $stNomeResponsavel);
$preview->addParametro("stComprovantes",   $_POST["stComprovantes"]);
$preview->addParametro("boAgrupar",		   ($_POST["boAgrupar"])?"true":"false");
$preview->addParametro("boSomarMatriculas",($_POST["boSomarMatriculas"])?"true":"false");
$preview->addParametro("dtFinalCompetencia",$dtFinalCompetencia);
$preview->preview();

?>
