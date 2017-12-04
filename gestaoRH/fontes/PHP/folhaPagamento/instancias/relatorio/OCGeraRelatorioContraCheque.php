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
    * Página de Relatório Contra Cheque
    * Data de Criação  : 01/07/2013
    * Desenvolvedor: Evandro Melos
    * Analise: Dagiane
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php"                                                     );
include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoCalculado.class.php");
include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoComplementarCalculado.class.php");
include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoDecimoCalculado.class.php");
include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoFeriasCalculado.class.php");
include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoRescisaoCalculado.class.php");
include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorSalario.class.php");
include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPrevidenciaEvento.class.php");
include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoFgtsEvento.class.php");
include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoTabelaIrrfEvento.class.php");
include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoFaixaDescontoIrrf.class.php");
include_once(CAM_GRH_FOL_MAPEAMENTO."FFolhaPagamentoGetDesdobramentoFolha.class.php");
include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalServidorDependente.class.php");
define       ('FPDF_FONTPATH','font/');

//Define o nome dos arquivos PHP
$stPrograma = "ContraCheque";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgGera     = "OCGeraRelatorio".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js";

//Define Birt
$preview = new PreviewBirt(4,27,29);
$preview->setTitulo('Relatorio Contra Cheque');
$preview->setVersaoBirt( '2.5.0' );
$preview->setExportaExcel(false);
$preview->setReturnURL(CAM_GRH_FOL_INSTANCIAS."relatorio/".$pgFilt);

// Essa variavel serve para passar a quantidade de regitros de eventos por contra cheque.
$inQuantEvento = 17;

$arFiltro = $_REQUEST;

include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
$obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
$inCodMes = $request->get('inCodMes');
$inAno    = $request->get('inAno');
$inMes    = ( $inCodMes < 10 ) ? "0".$inCodMes : $inCodMes;

$stCompetencia = $inMes."/".$inAno;
$stFiltro = " AND to_char(FPM.dt_final,'mm/yyyy') = '".$stCompetencia."'";
$obTFolhaPagamentoPeriodoMovimentacao->recuperaPeriodoMovimentacao($rsPeriodoMovimentacao,$stFiltro);

$stTipoFiltro = $request->get('stTipoFiltro');
$stOrdenacao  = $request->get('stOrdenacao');
$stSituacao   = $request->get('stSituacao');

$inCodLotacaoSelecionados = ($arFiltro["inCodLotacaoSelecionados"])?$arFiltro["inCodLotacaoSelecionados"]:$_POST["inCodLotacaoSelecionados"];
$inCodLocalSelecionados = ($arFiltro["inCodLocalSelecionados"])?$arFiltro["inCodLocalSelecionados"]:$_POST["inCodLocalSelecionados"];

$boAgrupar = $request->get('boAgrupar');

switch ($stTipoFiltro) {
    case "contrato_todos":
    case "contrato":
    case "cgm_contrato_todos":
    case "contrato_rescisao":
    case "cgm_contrato_rescisao":
    case "contrato_aposentado":
    case "cgm_contrato_aposentado":
    case "contrato_pensionista":
    case "cgm_contrato_pensionista":
        if ($stOrdenacao == "alfabetica") {
            $stOrdem = "nom_cgm";
        } else {
            $stOrdem = "registro";
        }
        $stFiltroContratos = " AND contrato.cod_contrato IN (";
        foreach (Sessao::read("arContratos") as $arContrato) {
            $stFiltroContratos .= $arContrato["cod_contrato"].",";
        }
        $stFiltroContratos = substr($stFiltroContratos,0,strlen($stFiltroContratos)-1).")";
        break;
    case "geral":
        if ($stOrdenacao == "alfabetica") {
            $stOrdem = "nom_cgm";
        } else {
            $stOrdem = "registro";
        }
        break;
    case "lotacao_grupo":
        $stOrdem = "";
        $virgula = "";

        if ($boAgrupar) {
            $stOrdem .= "orgao";
            $virgula = ", ";
        }
        if ($stOrdenacao == "alfabetica") {
            $stOrdem .= $virgula."descricao_lotacao,nom_cgm";
        } else {
            $stOrdem .= $virgula."orgao,registro";
        }
        $stFiltroContratos = " AND cadastros.cod_orgao IN (";
        foreach ($inCodLotacaoSelecionados as $inCodOrgao) {
            $stFiltroContratos .= $inCodOrgao.",";
        }
        $stFiltroContratos = substr($stFiltroContratos,0,strlen($stFiltroContratos)-1).")";
        break;
    case "local_grupo":
        $stOrdem = "";
        $virgula = "";

        if ($boAgrupar) {
            $stOrdem .= "local";
            $virgula = ", ";
        }

        if ($stOrdenacao == "alfabetica") {
            $stOrdem .= $virgula."descricao_local,nom_cgm";
        } else {
            $stOrdem .= $virgula."cod_local,registro";
        }
        $stFiltroContratos = " AND cadastros.cod_local IN (";
        foreach ($inCodLocalSelecionados as $inCodLocal) {
            $stFiltroContratos .= $inCodLocal.",";
        }
        $stFiltroContratos = substr($stFiltroContratos,0,strlen($stFiltroContratos)-1).")";
        break;
}

if ($stOrdem == '') {
    $stOrdem = 'nom_cgm';
}

$boComplementar    = $request->get('boComplementar');
$inCodConfiguracao = $request->get('inCodConfiguracao');
$inFolha           = ($inCodConfiguracao != "") ? $inCodConfiguracao : 0;

$inCodComplementar   = $request->get('inCodComplementar');
$stDesdobramento     = $request->get('stDesdobramento');
$inContratoReemissao = $request->get('inContratoReemissao');
$inCodComplementar   = ( $inCodComplementar   ) ? $inCodComplementar   : 0;
$inContratoReemissao = ( $inContratoReemissao ) ? $inContratoReemissao : 0;

$boMensagemAniversariante = $request->get('boMensagemAniversariante');
if ($boMensagemAniversariante) 
    $stMensagemAniversario    = $request->get('stMensagemAniversario');

$stMensagem               = $request->get('stMensagem');

$inMes = ( $inCodMes < 10 ) ? "0".$inCodMes : $inCodMes;
$dtCompetencia = $inMes."/".$inAno;

list($dia,$mes,$ano) = explode('/', $rsPeriodoMovimentacao->getCampo('timestamp_situacao') );
$timestamp_situacao = $ano.'-'.$mes.'-'.$dia;

list($dia,$mes,$ano) = explode('/', $rsPeriodoMovimentacao->getCampo('dt_final') );
$dt_final = $ano.'-'.$mes.'-'.$dia;

if (!is_null($request->get('boDuplicar'))) {
    $boDuplicar = 'true';
} else {
    $boDuplicar = 'false';
}

switch ($inFolha) {
        case 0:
            $stFolha = "Folha Complementar";
            break;
        case 1:
            $stFolha = "Folha Salário";
            break;
        case 2:
            $stFolha = "Folha Férias";
            break;
        case 3:
            $stFolha = "Folha Décimo";
            break;
        case 4:
            $stFolha = "Folha Rescisão";
            break;
}

$preview->addParametro("exercicio"           , Sessao::getExercicio() );
$preview->addParametro("entidade"            , Sessao::getEntidade() );
$preview->addParametro("qtdEventos"          , $inQuantEvento);
$preview->addParametro("periodoMovimentacao" , $rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao") );
$preview->addParametro("timestamp_situacao"  , $timestamp_situacao);
$preview->addParametro("dt_inicial"          , $rsPeriodoMovimentacao->getCampo("dt_inicial") );
$preview->addParametro("dt_final"            , $dt_final );
$preview->addParametro("ordenacao"           , $stOrdem);
$preview->addParametro("codConfiguracao"     , (trim($inFolha)==""?0:$inFolha) );
$preview->addParametro("dt_competencia"      , $dtCompetencia );
$preview->addParametro("codComplementar"     , $inCodComplementar );
$preview->addParametro("filtro"              , $stFiltroContratos );
$preview->addParametro("registroReemissao"   , $inContratoReemissao );
$preview->addParametro("duplicar"            , $boDuplicar );
$preview->addParametro("situacao"            , $stSituacao );
$preview->addParametro("st_Folha"            , $stFolha );
$preview->addParametro("mensagem_aniversario", $stMensagemAniversario );
$preview->addParametro("mensagem"            , $stMensagem );
$preview->addParametro("inMes"               , $inMes );
$preview->addParametro("desdobramento"       , ($stDesdobramento) ? $stDesdobramento : "");

$preview->preview();
