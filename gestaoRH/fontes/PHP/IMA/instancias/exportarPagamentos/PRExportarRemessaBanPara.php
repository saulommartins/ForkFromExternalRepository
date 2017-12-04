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
    * Página de Processamento do Exportação Remessa BanPara
    * Data de Criação: 10/04/2008

    * @author Analista: Dagiane	Vieira
    * @author Desenvolvedor: <Alex Cardoso>

    * @ignore

    $Id: PRExportarRemessaBanPara.php 31326 2008-07-22 18:37:44Z alex $

    * Casos de uso: uc-04.08.19
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalServidor.class.php" 										);
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalPensionista.class.php" 									);
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalPensao.class.php" 										);
include_once ( CAM_GRH_EST_MAPEAMENTO."TEstagioEstagiario.class.php" 									);
include_once ( CAM_GT_MON_MAPEAMENTO."TMONBanco.class.php" 												);
include_once ( CLA_EXPORTADOR 																			);

$stAcao = $request->get('stAcao');
$rsConfiguracaoBanpara = Sessao::read("rsContas");

$arSessaoLink = Sessao::read('link');
$stLink = "&pg=".$arSessaoLink["pg"]."&pos=".$arSessaoLink["pos"];

foreach ($_POST as $key=>$value) {
    $stLink .= $key."=".$value."&";
}

//Define o nome dos arquivos PHP
$stPrograma = "ExportarRemessaBanPara";
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

switch ($stAcao) {
    case "exportar":
        Sessao::setTrataExcecao(true);

        Sessao::write('arArquivosDownload', array());
        Sessao::write('dadosArquivosRemessaBanPara', array());
        Sessao::write('inCodOrgaosSelecionados', $_REQUEST['inCodOrgaosSelecionados']);

        ################################## COMPETENCIA ###################################

        ///////// COMPETENCIA SELECIONADA
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
        $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
        $stCompetencia  = (  $_POST["inCodMes"] < 10 ) ? "0".$_POST["inCodMes"] : $_POST["inCodMes"];
        $stCompetencia .= $_POST["inAno"];
        $stFiltroCompetencia = " WHERE to_char(dt_final,'mmyyyy') = '".$stCompetencia."'";
        $obTFolhaPagamentoPeriodoMovimentacao->recuperaTodos($rsPeriodoMovimentacao,$stFiltroCompetencia);

        $arInicialCompetenciaSelecionada = explode("/",$rsPeriodoMovimentacao->getCampo("dt_inicial"));
        $arFinalCompetenciaSelecionada = explode("/",$rsPeriodoMovimentacao->getCampo("dt_final"));

        $dtInicialCompetenciaSelecionada = $arInicialCompetenciaSelecionada[2]."-".$arInicialCompetenciaSelecionada[1]."-".$arInicialCompetenciaSelecionada[0];
        $dtFinalCompetenciaSelecionada = $arFinalCompetenciaSelecionada[2]."-".$arFinalCompetenciaSelecionada[1]."-".$arFinalCompetenciaSelecionada[0];

        ///////// COMPETENCIA ANTERIOR SELECIONADA
        $inCodMovimentacaoAnteriorSelecionada = $rsPeriodoMovimentacao->getCampo('cod_periodo_movimentacao') - 1;
        $obTFolhaPagamentoPeriodoMovimentacao->recuperaPeriodoMovimentacao($rsPenultimaMovimentacao, " AND FPM.cod_periodo_movimentacao = ".$inCodMovimentacaoAnteriorSelecionada);

        if ($rsPenultimaMovimentacao->getCampo('dt_inicial') != "") {
            $arInicialCompetenciaAnteriorSelecionada = explode("/",$rsPenultimaMovimentacao->getCampo('dt_inicial'));
            $arFinalCompetenciaAnteriorSelecionada   = explode("/",$rsPenultimaMovimentacao->getCampo('dt_final'));
        } else {
            $arInicialCompetenciaAnteriorSelecionada = $arInicialCompetenciaSelecionada;
            $arFinalCompetenciaAnteriorSelecionada   = $arFinalCompetenciaSelecionada;
        }

        $dtInicialCompetenciaAnteriorSelecionada = $arInicialCompetenciaAnteriorSelecionada[2]."-".$arInicialCompetenciaAnteriorSelecionada[1]."-".$arInicialCompetenciaAnteriorSelecionada[0];
        $dtFinalCompetenciaAnteriorSelecionada   = $arFinalCompetenciaAnteriorSelecionada[2]."-".$arFinalCompetenciaAnteriorSelecionada[1]."-".$arFinalCompetenciaAnteriorSelecionada[0];

        ///////// COMPETENCIA ATUAL
        $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsUltimaMovimentacao);

        $arInicialCompetenciaAtual = explode("/",$rsUltimaMovimentacao->getCampo('dt_inicial'));
        $arFinalCompetenciaAtual   = explode("/",$rsUltimaMovimentacao->getCampo('dt_final'));

        $dtInicialCompetenciaAtual = $arInicialCompetenciaAtual[2]."-".$arInicialCompetenciaAtual[1]."-".$arInicialCompetenciaAtual[0];
        $dtFinalCompetenciaAtual   = $arFinalCompetenciaAtual[2]."-".$arFinalCompetenciaAtual[1]."-".$arFinalCompetenciaAtual[0];

        ################################## CONFIGURACAO BANCO #################################
        define('NUM_BCO', '037');

        include_once(CAM_GRH_IMA_MAPEAMENTO."TIMAConfiguracaoBanparaEmpresa.class.php"		);
        include_once(CAM_GRH_IMA_MAPEAMENTO."TIMAConfiguracaoBanpara.class.php"				);
        include_once(CAM_GRH_IMA_MAPEAMENTO."TIMAConfiguracaoBanparaOrgao.class.php"		);
        include_once(CAM_GRH_IMA_MAPEAMENTO."TIMAConfiguracaoBanparaLocal.class.php"		);

        $rsConfiguracaoBanparaEmpresa = new RecordSet();
        $rsConfiguracaoBanparaOrgao   = new RecordSet();
        $rsConfiguracaoBanparaLocal   = new RecordSet();

        $rsMonetarioBanco = new RecordSet();
        $obTMONBanco = new TMONBanco();
        $obTMONBanco->recuperaTodos($rsMonetarioBanco, ' WHERE num_banco = \''.NUM_BCO.'\'');
        #############################################################################

        if ($_POST['nuValorLiquidoInicial'] != "" && $_POST['nuValorLiquidoFinal'] != "") {
            $nuValorLiquidoFinal = str_replace(".","",$_POST["nuValorLiquidoFinal"]);
            $nuValorLiquidoFinal = str_replace(",",".",$nuValorLiquidoFinal);

            $nuValorLiquidoInicial = str_replace(".","",$_POST["nuValorLiquidoInicial"]);
            $nuValorLiquidoInicial = str_replace(",",".",$nuValorLiquidoInicial);
        }

        if ($_POST["nuPercentualPagar"] != "") {
            $nuPercentualPagar = str_replace(".", "",  $_POST["nuPercentualPagar"]);
            $nuPercentualPagar = str_replace(",", ".", $nuPercentualPagar);
        } else {
            $nuPercentualPagar = 0;
        }

        ################################## VARIAVEIS ARQUIVO ##################################
        $dtGeracaoArquivo      = explode('/',$_POST['dtGeracaoArquivo']);
        $dtGeracaoArquivo      = $dtGeracaoArquivo[2].$dtGeracaoArquivo[1].$dtGeracaoArquivo[0];

        $dtPagamento           = explode('/',$_POST['dtPagamento']);
        $dtPagamento           = $dtPagamento[2].$dtPagamento[1].$dtPagamento[0];
        $dtReferenciaPagamento = $arFinalCompetenciaSelecionada[2].$arFinalCompetenciaSelecionada[1];

        ################################## ATIVOS/APOSENTADOS/PENSIONISTA #######################
        $stFiltroContrato  = "";

        if ($_POST['stSituacao'] == 'ativos' ||
           $_POST['stSituacao'] == 'aposentados' ||
           $_POST['stSituacao'] == 'rescindidos' ||
           $_POST['stSituacao'] == 'pensionistas' ||
           $_POST['stSituacao'] == 'todos') {

            $stValoresFiltro = "";
            switch ($_POST['stTipoFiltro']) {
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
                    $arPensionistas = Sessao::read("arPensionistas");
                    foreach ($arPensionistas as $arPensionista) {
                        $stValoresFiltro .= $arPensionista["cod_contrato"].",";
                    }
                    $stValoresFiltro = substr($stValoresFiltro,0,strlen($stValoresFiltro)-1);
                    break;
                case "lotacao":
                    $stValoresFiltro = implode(",",$_REQUEST["inCodLotacaoSelecionados"]);
                    break;
                case "local":
                    $stValoresFiltro = implode(",",$_REQUEST["inCodLocalSelecionados"]);
                    break;
                case "atributo_servidor":
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
                case "atributo_pensionista":
                    $inCodAtributo = $_REQUEST["inCodAtributo"];
                    $inCodCadastro = $_REQUEST["inCodCadastro"];
                    $stNomeAtributo = "Atributo_".$inCodAtributo."_".$inCodCadastro;
                    if (is_array($_REQUEST[$stNomeAtributo."_Selecionados"])) {
                        $inArray = 1;
                        $stValores = implode(",",$_REQUEST[$stNomeAtributo."_Selecionados"]);
                    } else {
                        $inArray = 0;
                        $stValores = $_REQUEST[$stNomeAtributo];
                    }
                    $stValoresFiltro = $inArray."#".$inCodAtributo."#".$stValores;
                    break;
            }
        }

        ################################## ESTAGIARIOS ###################################
        $stFiltroEstagiario  = "";

        if ($_POST['stSituacao'] == 'estagiarios' ||
           $_POST['stSituacao'] == 'todos') {
            switch ($_POST['stTipoFiltro']) {
                case "cgm_codigo_estagio":
                    foreach (Sessao::read('arEstagios') as $arEstagio) {
                        $stCodEstagio .= $arEstagio["inCodigoEstagio"].",";
                    }
                    $stCodEstagio = substr($stCodEstagio,0,strlen($stCodEstagio)-1);
                    $stFiltroEstagiario  .= " AND numero_estagio IN (".$stCodEstagio.")";
                    break;
                case "lotacao":
                    $stCodOrgao = implode(",",$_REQUEST["inCodLotacaoSelecionados"]);
                    $stFiltroEstagiario  .= " AND cod_orgao in (".$stCodOrgao.")";
                    break;
                case "local":
                    $stCodLocal = implode(",",$_POST['inCodLocalSelecionados']);
                    $stFiltroEstagiario .= " AND cod_local in (".$stCodLocal.")";
                    break;
                case "atributo_estagiario":
                    $inCodAtributo = $_REQUEST["inCodAtributo"];
                    $inCodCadastro = $_REQUEST["inCodCadastro"];
                    $stNomeAtributo = "Atributo_".$inCodAtributo."_".$inCodCadastro;
                    if (is_array($_REQUEST[$stNomeAtributo."_Selecionados"])) {
                        $inArray = 1;
                        $stValores = implode(",",$_REQUEST[$stNomeAtributo."_Selecionados"]);
                    } else {
                        $inArray = 0;
                        $stValores = $_REQUEST[$stNomeAtributo];
                    }
                    $stValoresFiltro = $inArray."#".$inCodAtributo."#".$stValores;
                    break;
            }
        }

        ################################## PENSAO JUDICIAL ###################################
        $stFiltroPensaoJudicial  = "";

        //Tipo de Cadastro
        if ($_POST['stSituacao'] == 'todos' ||
           $_POST['stSituacao'] == 'pensao_judicial') {

            switch ($_POST['stTipoFiltro']) {
                case "cgm_dependente": //IFiltroComponentesDependentes
                        foreach (Sessao::read('arCGMDependentes') as $arCGMDependente) {
                            $stCGMDependente .= "'".addslashes($arCGMDependente["numcgm"])."',";
                        }
                        $stCGMDependente = substr($stCGMDependente,0,strlen($stCGMDependente)-1);

                        $stFiltroPensaoJudicial  .= " AND contrato.numcgm_dependente IN (".$stCGMDependente.")";
                        break;
                case "cgm_servidor_dependente": //IFiltroComponentesDependentes
                        foreach (Sessao::read('arContratos') as $arContrato) {
                            $stCodContrato .= $arContrato["cod_contrato"].",";
                        }
                        $stCodContrato = substr($stCodContrato,0,strlen($stCodContrato)-1);
                        $stFiltroPensaoJudicial  .= " AND cod_contrato IN (".$stCodContrato.")";
                        break;
                case "lotacao":
                        $stCodOrgao = implode(",",$_REQUEST["inCodLotacaoSelecionados"]);
                        $stFiltroPensaoJudicial  .= " AND cod_orgao in (".$stCodOrgao.")";
                        break;
            }
        }

        ################################## EXPORTADOR ###################################
        $obExportador = new Exportador();
        $obExportador->setRetorno($pgForm);

        if ($rsConfiguracaoBanpara->getNumLinhas() > 0) {
            while (!$rsConfiguracaoBanpara->eof()) {

                    #############################################################################
                    ##                                                                         ##
                    ##                  F I L T R O S  L O T A C A O / L O C A L               ##
                    ##      P O R   C O N F I G U R A C A O _ B A N P A R A - > O R G A O      ##
                    ##                                                                         ##
                    #############################################################################
                    $obTIMAConfiguracaoBanparaOrgao = new TIMAConfiguracaoBanparaOrgao();
                    $stFiltro  = " WHERE cod_empresa = ".$rsConfiguracaoBanpara->getCampo('cod_empresa');
                    $stFiltro .= "   AND num_orgao_banpara = ".$rsConfiguracaoBanpara->getCampo('num_orgao_banpara');
                    $stFiltro .= "   AND timestamp = '".$rsConfiguracaoBanpara->getCampo('timestamp')."'";
                    $obTIMAConfiguracaoBanparaOrgao->recuperaTodos($rsConfiguracaoBanparaOrgao, $stFiltro);

                    $arCodLotacao = array();
                    $stCodLotacao = "";

                    if ($rsConfiguracaoBanparaOrgao->getNumLinhas() > 0) {
                        while (!$rsConfiguracaoBanparaOrgao->eof()) {
                            $arCodLotacao[] = $rsConfiguracaoBanparaOrgao->getCampo('cod_orgao');
                            $rsConfiguracaoBanparaOrgao->proximo();
                        }
                        $stCodLotacao = implode(',', $arCodLotacao);
                    }

                    $obTIMAConfiguracaoBanparaLocal = new TIMAConfiguracaoBanparaLocal();
                    $stFiltro  = " WHERE cod_empresa = ".$rsConfiguracaoBanpara->getCampo('cod_empresa');
                    $stFiltro .= "   AND num_orgao_banpara = ".$rsConfiguracaoBanpara->getCampo('num_orgao_banpara');
                    $stFiltro .= "   AND timestamp = '".$rsConfiguracaoBanpara->getCampo('timestamp')."'";
                    $obTIMAConfiguracaoBanparaLocal->recuperaTodos($rsConfiguracaoBanparaLocal, $stFiltro);

                    $arCodLocal = array();
                    $stCodLocal = "";

                    if ($rsConfiguracaoBanparaLocal->getNumLinhas() > 0) {
                        while (!$rsConfiguracaoBanparaLocal->eof()) {
                            $arCodLocal[] = $rsConfiguracaoBanparaLocal->getCampo('cod_local');
                            $rsConfiguracaoBanparaLocal->proximo();
                        }
                        $stCodLocal = implode(',', $arCodLocal);
                    }

                    #############################################################################
                    ##                                                                         ##
                    ##                 A R Q U I V O    C R E D I T O                          ##
                    ##                                                                         ##
                    #############################################################################
                    $stNomeArquivo = "BanPara_Cred".$rsConfiguracaoBanpara->getCampo('num_orgao_banpara').".rem";
                    $obExportador->addArquivo($stNomeArquivo);
                    $obExportador->roUltimoArquivo->setTipoDocumento("RemessaBanParaCredito");

                    ################ F I L T R O  A R Q U I V O  C R E D I T O ################
                    $rsCreditoServidor = new RecordSet();
                    if ($_POST["stSituacao"] == 'ativos' ||
                        $_POST["stSituacao"] == 'aposentados' ||
                        $_POST["stSituacao"] == 'rescindidos' ||
                        $_POST["stSituacao"] == 'pensionistas') {

                        $stFiltroCredito  = $stFiltroServidorCredito;
                        $stFiltroCredito .= " AND cod_orgao IN ($stCodLotacao) \n";
                        $stFiltroCredito .= ($stCodLocal != "")?" AND cod_local IN ($stCodLocal)\n":"";

                        include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoCalculado.class.php" );
                        $obRecuperaEventoCalculado = new TFolhaPagamentoEventoCalculado();

                        $obRecuperaEventoCalculado->setDado("inCodPeriodoMovimentacao", $rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao"));
                        $obRecuperaEventoCalculado->setDado("stSituacao"              , $_POST["stSituacao"]);
                        $obRecuperaEventoCalculado->setDado("inCodConfiguracao"       , $_POST["inCodConfiguracao"]);
                        $obRecuperaEventoCalculado->setDado("inCodComplementar"       , ($_POST["inCodComplementar"]==""?0:$_POST["inCodComplementar"]));
                        $obRecuperaEventoCalculado->setDado("stTipoFiltro"            , $_POST["stTipoFiltro"]);
                        $obRecuperaEventoCalculado->setDado("stValoresFiltro"         , $stValoresFiltro);
                        $obRecuperaEventoCalculado->setDado("stDesdobramento"         , $_POST["stDesdobramento"]);
                        $obRecuperaEventoCalculado->setDado("inCodBanco"              , $rsMonetarioBanco->getCampo("cod_banco"));
                        $obRecuperaEventoCalculado->setDado("nuLiquidoMinimo"         , $nuValorLiquidoInicial);
                        $obRecuperaEventoCalculado->setDado("nuLiquidoMaximo"         , $nuValorLiquidoFinal);
                        $obRecuperaEventoCalculado->setDado("nuPercentualPagar"       , $nuPercentualPagar);
                        $obRecuperaEventoCalculado->recuperaContratosCalculadosRemessaBancos($rsCreditoServidor,$stFiltroCredito);
                    }

                    $rsCreditoEstagio = new RecordSet();
                    if ($_POST["stSituacao"] == 'estagiarios') {
                        $stFiltroCredito  = $stFiltroEstagiarioCredito;
                        $stFiltroCredito .= " AND cod_orgao IN ($stCodLotacao) \n";
                        $stFiltroCredito .= ($stCodLocal != "")?" AND cod_local IN ($stCodLocal)\n":"";

                        include_once(CAM_GRH_EST_MAPEAMENTO."TEstagioEstagiarioEstagio.class.php");
                        $obTEstagioEstagiorioEstagio = new TEstagioEstagiarioEstagio();
                        $obTEstagioEstagiorioEstagio->setDado("inCodPeriodoMovimentacao", $rsUltimaMovimentacao->getCampo("cod_periodo_movimentacao"));
                        $obTEstagioEstagiorioEstagio->setDado("inCodBanco"              , $rsMonetarioBanco->getCampo("cod_banco"));
                        $obTEstagioEstagiorioEstagio->setDado("stTipoFiltro"            , $_POST["stTipoFiltro"]);
                        $obTEstagioEstagiorioEstagio->setDado("stValoresFiltro"         , $stValoresFiltro);
                        $obTEstagioEstagiorioEstagio->setDado("nuLiquidoMinimo"         , $nuValorLiquidoInicial);
                        $obTEstagioEstagiorioEstagio->setDado("nuLiquidoMaximo"         , $nuValorLiquidoFinal);
                        $obTEstagioEstagiorioEstagio->setDado("nuPercentualPagar"       , $nuPercentualPagar);
                        $obTEstagioEstagiorioEstagio->recuperaRemessaBancos($rsCreditoEstagio,$stFiltroCredito);
                    }

                    $rsCreditoPensaoJudicial = new RecordSet();
                    if ($_POST["stSituacao"] == 'pensao_judicial') {
                        $stFiltroCredito  = $stFiltroPensaoJudicialCredito;
                        $stFiltroCredito .= " AND cod_orgao IN ($stCodLotacao) \n";
                        $stFiltroCredito .= ($stCodLocal != "")?" AND cod_local IN ($stCodLocal)\n":"";

                        include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoCalculadoDependente.class.php" );
                        $obTFolhaPagamentoEventoCalculadoDependente = new TFolhaPagamentoEventoCalculadoDependente();
                        $obTFolhaPagamentoEventoCalculadoDependente->setDado("inCodPeriodoMovimentacao", $rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao"));
                        $obTFolhaPagamentoEventoCalculadoDependente->setDado("inCodConfiguracao"       , $_POST["inCodConfiguracao"]);
                        $obTFolhaPagamentoEventoCalculadoDependente->setDado("inCodComplementar"       , ($_POST["inCodComplementar"]==""?0:$_POST["inCodComplementar"]));
                        $obTFolhaPagamentoEventoCalculadoDependente->setDado("stDesdobramento"         , $_POST["stDesdobramento"]);
                        $obTFolhaPagamentoEventoCalculadoDependente->setDado("inCodBanco"              , $rsMonetarioBanco->getCampo("cod_banco"));
                        $obTFolhaPagamentoEventoCalculadoDependente->setDado("nuLiquidoMinimo"         , $nuValorLiquidoInicial);
                        $obTFolhaPagamentoEventoCalculadoDependente->setDado("nuLiquidoMaximo"         , $nuValorLiquidoFinal);
                        $obTFolhaPagamentoEventoCalculadoDependente->setDado("nuPercentualPagar"       , $nuPercentualPagar);
                        $obTFolhaPagamentoEventoCalculadoDependente->recuperaContratosCalculadosRemessaBancos($rsCreditoPensaoJudicial,$stFiltroCredito);
                    }

                    ##################  PREENCHIMENTO ARRAY EXPORTACAO CREDITO  ################
                    $arExportadorCredito = array();
                    $inIndexCredito = 0;
                    $inQuantRegistrosCredito = 2;
                    $nuVlrLancamentoTotal = 0;

                    /////////// CADASTRO EXPORTADOR - SERVIDOR
                    while (!$rsCreditoServidor->eof()) {
                        $arExportadorCredito[$inIndexCredito]["matricula"]                    = $rsCreditoServidor->getCampo("registro");
                        $arExportadorCredito[$inIndexCredito]["num_banco"]                    = $rsCreditoServidor->getCampo("num_banco");

                        $arAgencia = separarDigito($rsCreditoServidor->getCampo("num_agencia"));
                        $arExportadorCredito[$inIndexCredito]["num_agencia"]                  = $arAgencia[0];
                        $arExportadorCredito[$inIndexCredito]["num_conta"]                    = implode('',separarDigito($rsCreditoServidor->getCampo("nr_conta")));

                        $nuVlrLancamento = number_format($rsCreditoServidor->getCampo("liquido"), 2, ".", "");
                        $nuVlrLancamentoTotal += $nuVlrLancamento;
                        $nuVlrLancamento = number_format($nuVlrLancamento, 2, "", "");

                        $arExportadorCredito[$inIndexCredito]["valor"]                        = $nuVlrLancamento;
                        $arExportadorCredito[$inIndexCredito]["vazio"]                        = "";

                        $inIndexCredito++;
                        $inQuantRegistrosCredito++;

                        $rsCreditoServidor->proximo();
                    }//end while rsCreditoServidor->oef

                    /////////// CREDITO EXPORTADOR - ESTAGIARIO
                    while (!$rsCreditoEstagio->eof()) {
                        $arExportadorCredito[$inIndexCredito]["matricula"]                    = $rsCreditoEstagio->getCampo("numero_estagio");
                        $arExportadorCredito[$inIndexCredito]["num_banco"]                    = $rsCreditoEstagio->getCampo("num_banco");

                        $arAgencia = separarDigito($rsCreditoEstagio->getCampo("num_agencia"));
                        $arExportadorCredito[$inIndexCredito]["num_agencia"]                  = $arAgencia[0];
                        $arExportadorCredito[$inIndexCredito]["num_conta"]                    = implode('',separarDigito($rsCreditoEstagio->getCampo("num_conta")));

                        $nuVlrLancamento = number_format($rsCreditoEstagio->getCampo("liquido"), 2, ".", "");
                        $nuVlrLancamentoTotal += $nuVlrLancamento;
                        $nuVlrLancamento = number_format($nuVlrLancamento, 2, "", "");

                        $arExportadorCredito[$inIndexCredito]["valor"]                        = $nuVlrLancamento;
                        $arExportadorCredito[$inIndexCredito]["vazio"]                        = "";

                        $inIndexCredito++;
                        $inQuantRegistrosCredito++;

                        $rsCreditoEstagio->proximo();
                    }//end while rsCreditoEstagio->oef

                    # CREDITO EXPORTADOR - PENSAO JUDICIAL
                    while (!$rsCreditoPensaoJudicial->eof()) {
                        $arExportadorCredito[$inIndexCredito]["matricula"]                    = $rsCreditoPensaoJudicial->getCampo("numcgm_dependente");
                        $arExportadorCredito[$inIndexCredito]["num_banco"]                    = $rsCreditoPensaoJudicial->getCampo("num_banco");

                        $arAgencia = separarDigito($rsCreditoPensaoJudicial->getCampo("num_agencia"));
                        $arExportadorCredito[$inIndexCredito]["num_agencia"]                  = $arAgencia[0];
                        $arExportadorCredito[$inIndexCredito]["num_conta"]                    = implode('',separarDigito($rsCreditoPensaoJudicial->getCampo("nr_conta")));

                        $nuVlrLancamento = number_format($rsCreditoPensaoJudicial->getCampo("liquido"), 2, ".", "");
                        $nuVlrLancamentoTotal += $nuVlrLancamento;
                        $nuVlrLancamento = number_format($nuVlrLancamento, 2, "", "");

                        $arExportadorCredito[$inIndexCredito]["valor"]                        = $nuVlrLancamento;
                        $arExportadorCredito[$inIndexCredito]["vazio"]                        = "";

                        $inIndexCredito++;
                        $inQuantRegistrosCredito++;

                        $rsCreditoPensaoJudicial->proximo();

                    }//end while rsCreditoPensaoJudicial->oef

                    $arSessaoDadosArquivosRemessaBanPara = Sessao::read("dadosArquivosRemessaBanPara");

                    $arSessaoDadosArquivosRemessaBanPara[$stNomeArquivo] = array();
                    $arSessaoDadosArquivosRemessaBanPara[$stNomeArquivo]['nuRegistros']   = $inQuantRegistrosCredito-2;
                    $arSessaoDadosArquivosRemessaBanPara[$stNomeArquivo]['nuVlrLancamentoTotal'] = $nuVlrLancamentoTotal;

                    Sessao::write("dadosArquivosRemessaBanPara", $arSessaoDadosArquivosRemessaBanPara);

                    ##################################### CREDITO - CABEÇALHO ARQUIVO ####################################
                    $arCabecalhoArquivoCredito = array();
                    $arCabecalhoArquivoCredito[0]['vazio']                     = "";
                    $arCabecalhoArquivoCredito[0]['tipo_registro']             = "0";
                    $arCabecalhoArquivoCredito[0]['nome_arquivo']              = "CREDITO";
                    $arCabecalhoArquivoCredito[0]['codigo_empresa']            = $rsConfiguracaoBanparaEmpresa->getCampo('codigo');
                    $arCabecalhoArquivoCredito[0]['codigo_orgao']              = $rsConfiguracaoBanpara->getCampo('codigo');
                    $arCabecalhoArquivoCredito[0]['dt_geracao']                = $dtGeracaoArquivo;
                    $arCabecalhoArquivoCredito[0]['hr_geracao']                = date("His");
                    $arCabecalhoArquivoCredito[0]['tipo_pagamento']            = $_POST['stTipoPagamento'];
                    $arCabecalhoArquivoCredito[0]['referencia_pagamento']      = $dtReferenciaPagamento;
                    $arCabecalhoArquivoCredito[0]['dt_pagamento']              = $dtPagamento;

                    $rsCabecalhoArquivoCredito = new RecordSet();
                    $rsCabecalhoArquivoCredito->preenche($arCabecalhoArquivoCredito);
                    $obExportador->roUltimoArquivo->addBloco($rsCabecalhoArquivoCredito);
                    unset($rsCabecalhoArquivoCredito);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vazio");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(15);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nome_arquivo");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codigo_empresa");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codigo_orgao");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_geracao");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("hr_geracao");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_pagamento");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("referencia_pagamento");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_pagamento");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vazio");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(51);

                    ##################################### CREDITO - DETALHE ARQUIVO ###################################
                    $rsExportadorCredito = new RecordSet();
                    $rsExportadorCredito->preenche($arExportadorCredito);
                    $obExportador->roUltimoArquivo->addBloco($rsExportadorCredito);
                    unset($rsExportadorCredito);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vazio");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("matricula");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(7);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_banco");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_agencia");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_conta");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(10);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("valor");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(17);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vazio");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(17);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vazio");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(54);

                    ##################################### CREDITO - RODAPÉ ARQUIVO ####################################
                    $arRodapeArquivoCredito = array();
                    $arRodapeArquivoCredito[0]['tipo_registro']             = "99999";
                    $arRodapeArquivoCredito[0]['quantidade_registros']      = $inQuantRegistrosCredito;
                    $arRodapeArquivoCredito[0]['soma']                      = number_format($nuVlrLancamentoTotal, 2, "", "");
                    $arRodapeArquivoCredito[0]['vazio']                     = "";

                    $rsRodapeArquivoCredito = new RecordSet();
                    $rsRodapeArquivoCredito->preenche($arRodapeArquivoCredito);
                    $obExportador->roUltimoArquivo->addBloco($rsRodapeArquivoCredito);
                    unset($rsRodapeArquivoCredito);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(5);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(5);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(5);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("quantidade_registros");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("soma");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(17);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vazio");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(82);

                    #############################################################################
                    ##                                                                         ##
                    ##                 A R Q U I V O   C A D A S T R O                         ##
                    ##                                                                         ##
                    #############################################################################
                    $stNomeArquivo = "BanPara_Cad".$rsConfiguracaoBanpara->getCampo('num_orgao_banpara').".rem";
                    $obExportador->addArquivo($stNomeArquivo);
                    $obExportador->roUltimoArquivo->setTipoDocumento("RemessaBanParaCadadastro");

                    ################ F I L T R O  A R Q U I V O  C A D A S T R O ################
                    $rsCadastroServidor = new RecordSet();
                    if ($_POST["stSituacao"] == 'ativos' or
                        $_POST["stSituacao"] == 'aposentados' or
                        $_POST["stSituacao"] == 'rescindidos') {

                        if ($rsCreditoServidor->getNumLinhas() > 0) {
                            $stCodContratoServidor = "";
                            $rsCreditoServidor->setPrimeiroElemento();
                            while (!$rsCreditoServidor->eof()) {
                                $boIncluiCodServidor = ($rsCreditoServidor->getCampo('tipo_cadastro') == 'S')?true:false;
                                if($boIncluiCodServidor)
                                    $stCodContratoServidor .= $rsCreditoServidor->getCampo('cod_contrato');
                                $rsCreditoServidor->proximo();
                                if(!$rsCreditoServidor->eof() && $boIncluiCodServidor)
                                    $stCodContratoServidor .= ",";
                            }

                            $stFiltroCadastro = " WHERE cod_contrato IN ($stCodContratoServidor) ";

                            $obTPessoalServidor = new TPessoalServidor();
                            $obTPessoalServidor->setDado("inCodPeriodoMovimentacao", $rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao"));
                            $obTPessoalServidor->recuperaServidorRemessaBanPara($rsCadastroServidor, $stFiltroCadastro);
                        }
                    }

                    $rsCadastroPensionista = new RecordSet();
                    if ($_POST["stSituacao"] == 'pensionistas') {

                        if ($rsCreditoServidor->getNumLinhas() > 0) {
                            $stCodContratoPensionista = "";
                            $rsCreditoServidor->setPrimeiroElemento();
                            while ( !$rsCreditoServidor->eof() ) {
                                $boIncluiCodPensionista = ($rsCreditoServidor->getCampo('tipo_cadastro') == 'P')?true:false;
                                if($boIncluiCodPensionista)
                                    $stCodContratoPensionista .= $rsCreditoServidor->getCampo('cod_contrato');
                                $rsCreditoServidor->proximo();
                                if(!$rsCreditoServidor->eof() && $boIncluiCodPensionista)
                                    $stCodContratoPensionista .= ",";
                            }

                            $stFiltroCadastro = " WHERE cod_contrato IN ($stCodContratoPensionista) ";

                            $obTPessoalPensionista = new TPessoalPensionista();
                            $obTPessoalPensionista->setDado("inCodPeriodoMovimentacao", $rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao"));
                            $obTPessoalPensionista->recuperaPensionistaRemessaBanPara($rsCadastroPensionista, $stFiltroCadastro);
                        }
                    }

                    $rsCadastroEstagiario = new RecordSet();
                    if ($_POST["stSituacao"] == 'estagiarios') {

                        if ( $rsCreditoEstagio->getNumLinhas() > 0 ) {
                            $stCodEstagioEstagiario = "";
                            $rsCreditoEstagio->setPrimeiroElemento();
                            while ( !$rsCreditoEstagio->eof() ) {
                                $stCodEstagioEstagiario .= "'".$rsCreditoEstagio->getCampo('cod_estagio')."#".$rsCreditoEstagio->getCampo('cgm_estagiario')."#".$rsCreditoEstagio->getCampo('cod_curso')."#".$rsCreditoEstagio->getCampo('cgm_instituicao_ensino')."'";
                                $rsCreditoEstagio->proximo();
                                if ( !$rsCreditoEstagio->eof() ) {
                                    $stCodEstagioEstagiario .= ",";
                                }
                            }

                            $stFiltroCadastro = " WHERE cod_estagio||'#'||cgm_estagiario||'#'||cod_curso||'#'||cgm_instituicao_ensino IN ($stCodEstagioEstagiario) ";

                            $obTEstagioEstagiario = new TEstagioEstagiario();
                            $obTEstagioEstagiario->setDado("inCodPeriodoMovimentacao", $rsUltimaMovimentacao->getCampo("cod_periodo_movimentacao"));
                            $obTEstagioEstagiario->recuperaEstagiarioRemessaBanPara($rsCadastroEstagiario, $stFiltroCadastro);
                        }
                    }

                    $rsCadastroPensaoJudicial = new RecordSet();
                    if ($_POST["stSituacao"] == 'pensao_judicial') {

                        if ($rsCreditoPensaoJudicial->getNumLinhas() > 0) {
                            $stCodPensaoJudicial = "";
                            $rsCreditoPensaoJudicial->setPrimeiroElemento();
                            while (!$rsCreditoPensaoJudicial->eof()) {
                                $stCodPensaoJudicial .= $rsCreditoPensaoJudicial->getCampo('cod_contrato');
                                $rsCreditoPensaoJudicial->proximo();
                                if(!$rsCreditoPensaoJudicial->eof())
                                    $stCodPensaoJudicial .= ",";
                            }

                            $stFiltroCadastro = " WHERE cod_contrato IN ($stCodPensaoJudicial) ";

                            $obTPessoalPensao = new TPessoalPensao();
                            $obTPessoalPensao->setDado("inCodPeriodoMovimentacao", $rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao"));
                            $obTPessoalPensao->recuperaDependentePensaoRemessaBanPara($rsCadastroPensaoJudicial, $stFiltroCadastro);
                        }
                    }

                    ##################  PREENCHIMENTO ARRAY EXPORTACAO CADASTRO  ################
                    $arExportadorCadastro = array();
                    $inIndexCadastro = 0;
                    $inQuantRegistrosCadastro = 2;

                    /////////// CADASTRO EXPORTADOR - SERVIDOR
                    while (!$rsCadastroServidor->eof()) {

                        # GRAU INSTRUCAO
                        #    0	N?o informado / 1	Analfabeto / 2	Alfabetizado / 4	1o grau incompleto
                        #    5	1o grau / 6	2o grau incompleto / 7	2o grau / 8	Superior incompleto
                        #    9	Superior/Especializa / 10	Mestrado / 11	Doutorado
                        #

                        switch ($rsCadastroServidor->getCampo("cod_escolaridade")) {
                            case 1://Analfabeto
                                  $inCodGrauInstrucao= 1;
                                  break;
                            case 2://1o Grau Incompleto
                            case 4:
                                  $inCodGrauInstrucao= 2;
                                  break;
                            case 5://1o Grau Completo
                                  $inCodGrauInstrucao= 3;
                                  break;
                            case 6://2o Grau Incompleto
                                  $inCodGrauInstrucao= 4;
                                  break;
                            case 7://2o Grau Completo
                                  $inCodGrauInstrucao= 5;
                                  break;
                            case 8://Superior Incompleto
                                  $inCodGrauInstrucao= 6;
                                  break;
                            case 9://Superior Completo
                            case 10:
                            case 11:
                                  $inCodGrauInstrucao= 7;
                                  break;
                            default://Nao Informado
                                   $inCodGrauInstrucao= 0;
                                  break;
                        }

                        # ESTADO CIVIL
                        #0	Não Informado  / 1	Solteiro(a) / 2	Casado(a) / 3	Divorciado(a)
                        #4	Separado(a) / 5	Vi?vo(a) / 6	Desquitado(a) / 7	Companheiro(a)
                        #

                        switch ($rsCadastroServidor->getCampo("cod_estado_civil")) {
                            case 1://Solteiro
                                  $inCodEstadoCivil= 1;
                                  break;
                            case 2://Casado
                                   $inCodEstadoCivil= 2;
                                  break;
                            case 3://Divorciado
                            case 4:
                            case 6:
                                   $inCodEstadoCivil= 3;
                                  break;
                            default://Outros
                                   $inCodEstadoCivil= 4;
                                  break;
                        }

                        $arExportadorCadastro[$inIndexCadastro]["matricula"]                    = $rsCadastroServidor->getCampo("registro");
                        $arExportadorCadastro[$inIndexCadastro]["cpf"]                          = preg_replace( "/[^0-9a-zA-Z]/i","",$rsCadastroServidor->getCampo("cpf"));
                        $arExportadorCadastro[$inIndexCadastro]["rg"]                           = preg_replace( "/[^0-9a-zA-Z]/i","",$rsCadastroServidor->getCampo("rg"));
                        $arExportadorCadastro[$inIndexCadastro]["orgao_emissor"]                = $rsCadastroServidor->getCampo("orgao_emissor");
                        $arExportadorCadastro[$inIndexCadastro]["uf_orgao_emissor"]             = ($rsCadastroServidor->getCampo("cod_uf_orgao_emissor") == '0')?"":$rsCadastroServidor->getCampo("uf_orgao_emissor");
                        $arExportadorCadastro[$inIndexCadastro]["nome"]                         = $rsCadastroServidor->getCampo("nom_cgm");

                        $dtAdmissao = explode('-',$rsCadastroServidor->getCampo("dt_admissao"));
                        $arExportadorCadastro[$inIndexCadastro]["dt_admissao"]                  = $dtAdmissao[2].$dtAdmissao[1].$dtAdmissao[0];
                        $arExportadorCadastro[$inIndexCadastro]["logradouro"]                   = $rsCadastroServidor->getCampo("logradouro");
                        $arExportadorCadastro[$inIndexCadastro]["complemento"]                  = $rsCadastroServidor->getCampo("complemento");
                        $arExportadorCadastro[$inIndexCadastro]["bairro"]                       = $rsCadastroServidor->getCampo("bairro");
                        $arExportadorCadastro[$inIndexCadastro]["numero"]                       = $rsCadastroServidor->getCampo("numero");
                        $arExportadorCadastro[$inIndexCadastro]["cidade"]                       = ($rsCadastroServidor->getCampo("cod_municipio") == '0')?"":$rsCadastroServidor->getCampo("cidade");
                        $arExportadorCadastro[$inIndexCadastro]["uf"]                           = ($rsCadastroServidor->getCampo("cod_uf") == '0')?"":$rsCadastroServidor->getCampo("uf");
                        $arExportadorCadastro[$inIndexCadastro]["cep"]                          = preg_replace( "/[^0-9a-zA-Z]/i","",$rsCadastroServidor->getCampo("cep"));
                        $arExportadorCadastro[$inIndexCadastro]["ddd"]                          = preg_replace( "/[^0-9]/i","",substr($rsCadastroServidor->getCampo("fone_residencial"),0,2));
                        $arExportadorCadastro[$inIndexCadastro]["fone_residencial"]             = preg_replace( "/[^0-9]/i","",substr($rsCadastroServidor->getCampo("fone_residencial"),2));

                        $dtNascimento = explode('-',$rsCadastroServidor->getCampo("dt_nascimento"));
                        $arExportadorCadastro[$inIndexCadastro]["dt_nascimento"]                = $dtNascimento[2].$dtNascimento[1].$dtNascimento[0];

                        $arExportadorCadastro[$inIndexCadastro]["nome_pai"]                     = (trim($rsCadastroServidor->getCampo("nome_pai")) == 'NAO INFORMADO')?"":$rsCadastroServidor->getCampo("nome_pai");
                        $arExportadorCadastro[$inIndexCadastro]["nome_mae"]                     = (trim($rsCadastroServidor->getCampo("nome_mae")) == 'NAO INFORMADO')?"":$rsCadastroServidor->getCampo("nome_mae");
                        $arExportadorCadastro[$inIndexCadastro]["nome_conjuge"]                 = ($rsCadastroServidor->getCampo("numcgm_conjuge") == '')?"":$rsCadastroServidor->getCampo("nom_cgm_conjuge");
                        $arExportadorCadastro[$inIndexCadastro]["sexo"]                         = $rsCadastroServidor->getCampo("sexo");
                        $arExportadorCadastro[$inIndexCadastro]["estado_civil"]                 = $inCodEstadoCivil;
                        $arExportadorCadastro[$inIndexCadastro]["grau_instrucao"]               = $inCodGrauInstrucao;
                        $arExportadorCadastro[$inIndexCadastro]["num_banco"]                    = $rsCadastroServidor->getCampo("num_banco");

                        $arAgencia = separarDigito($rsCadastroServidor->getCampo("num_agencia"));
                        $arExportadorCadastro[$inIndexCadastro]["num_agencia"]                  = $arAgencia[0];

                        $arExportadorCadastro[$inIndexCadastro]["num_conta"]                    = implode('',separarDigito($rsCadastroServidor->getCampo("nr_conta")));
                        $arExportadorCadastro[$inIndexCadastro]["destino_credito"]              = "01";//Conta Corrente
                        $arExportadorCadastro[$inIndexCadastro]["vazio"]                        = "";

                        $inIndexCadastro++;
                        $inQuantRegistrosCadastro++;
                        $rsCadastroServidor->proximo();
                    }

                    /////////// CADASTRO EXPORTADOR - PENSIONISTA
                    while (!$rsCadastroPensionista->eof()) {

                        # GRAU INSTRUCAO
                        #    0	Não informado / 1	Analfabeto / 2	Alfabetizado / 4	1o grau incompleto
                        #    5	1o grau / 6	2o grau incompleto / 7	2o grau / 8	Superior incompleto
                        #    9	Superior/Especializa / 10	Mestrado / 11	Doutorado

                        switch ($rsCadastroPensionista->getCampo("cod_escolaridade")) {
                            case 1://Analfabeto
                                  $inCodGrauInstrucao= 1;
                                  break;
                            case 2://1o Grau Incompleto
                            case 4:
                                  $inCodGrauInstrucao= 2;
                                  break;
                            case 5://1o Grau Completo
                                  $inCodGrauInstrucao= 3;
                                  break;
                            case 6://2o Grau Incompleto
                                  $inCodGrauInstrucao= 4;
                                  break;
                            case 7://2o Grau Completo
                                  $inCodGrauInstrucao= 5;
                                  break;
                            case 8://Superior Incompleto
                                  $inCodGrauInstrucao= 6;
                                  break;
                            case 9://Superior Completo
                            case 10:
                            case 11:
                                  $inCodGrauInstrucao= 7;
                                  break;
                            default://Nao Informado
                                   $inCodGrauInstrucao= 0;
                                  break;
                        }

                        $arExportadorCadastro[$inIndexCadastro]["matricula"]                    = $rsCadastroPensionista->getCampo("registro");
                        $arExportadorCadastro[$inIndexCadastro]["cpf"]                          = preg_replace( "/[^0-9a-zA-Z]/i","",$rsCadastroPensionista->getCampo("cpf"));
                        $arExportadorCadastro[$inIndexCadastro]["rg"]                           = preg_replace( "/[^0-9a-zA-Z]/i","",$rsCadastroPensionista->getCampo("rg"));
                        $arExportadorCadastro[$inIndexCadastro]["orgao_emissor"]                = $rsCadastroPensionista->getCampo("orgao_emissor");
                        $arExportadorCadastro[$inIndexCadastro]["uf_orgao_emissor"]             = ($rsCadastroPensionista->getCampo("cod_uf_orgao_emissor") == '0')?"":$rsCadastroPensionista->getCampo("uf_orgao_emissor");
                        $arExportadorCadastro[$inIndexCadastro]["nome"]                         = $rsCadastroPensionista->getCampo("nom_cgm");

                        $dtAdmissao = explode('-',$rsCadastroPensionista->getCampo("dt_inicio_beneficio"));
                        $arExportadorCadastro[$inIndexCadastro]["dt_admissao"]                  = $dtAdmissao[2].$dtAdmissao[1].$dtAdmissao[0];
                        $arExportadorCadastro[$inIndexCadastro]["logradouro"]                   = $rsCadastroPensionista->getCampo("logradouro");
                        $arExportadorCadastro[$inIndexCadastro]["complemento"]                  = $rsCadastroPensionista->getCampo("complemento");
                        $arExportadorCadastro[$inIndexCadastro]["bairro"]                       = $rsCadastroPensionista->getCampo("bairro");
                        $arExportadorCadastro[$inIndexCadastro]["numero"]                       = $rsCadastroPensionista->getCampo("numero");
                        $arExportadorCadastro[$inIndexCadastro]["cidade"]                       = ($rsCadastroPensionista->getCampo("cod_municipio") == '0')?"":$rsCadastroPensionista->getCampo("cidade");
                        $arExportadorCadastro[$inIndexCadastro]["uf"]                           = ($rsCadastroPensionista->getCampo("cod_uf") == '0')?"":$rsCadastroPensionista->getCampo("uf");
                        $arExportadorCadastro[$inIndexCadastro]["cep"]                          = preg_replace( "/[^0-9a-zA-Z]/i","",$rsCadastroPensionista->getCampo("cep"));
                        $arExportadorCadastro[$inIndexCadastro]["ddd"]                          = preg_replace( "/[^0-9]/i","",substr($rsCadastroPensionista->getCampo("fone_residencial"),0,2));
                        $arExportadorCadastro[$inIndexCadastro]["fone_residencial"]             = preg_replace( "/[^0-9]/i","",substr($rsCadastroPensionista->getCampo("fone_residencial"),2));

                        $dtNascimento = explode('-',$rsCadastroPensionista->getCampo("dt_nascimento"));
                        $arExportadorCadastro[$inIndexCadastro]["dt_nascimento"]                = $dtNascimento[2].$dtNascimento[1].$dtNascimento[0];

                        $arExportadorCadastro[$inIndexCadastro]["nome_pai"]                     = "";
                        $arExportadorCadastro[$inIndexCadastro]["nome_mae"]                     = "";
                        $arExportadorCadastro[$inIndexCadastro]["nome_conjuge"]                 = "";
                        $arExportadorCadastro[$inIndexCadastro]["sexo"]                         = $rsCadastroPensionista->getCampo("sexo");
                        $arExportadorCadastro[$inIndexCadastro]["estado_civil"]                 = "";
                        $arExportadorCadastro[$inIndexCadastro]["grau_instrucao"]               = $inCodGrauInstrucao;
                        $arExportadorCadastro[$inIndexCadastro]["num_banco"]                    = $rsCadastroPensionista->getCampo("num_banco");

                        $arAgencia = separarDigito($rsCadastroPensionista->getCampo("num_agencia"));
                        $arExportadorCadastro[$inIndexCadastro]["num_agencia"]                  = $arAgencia[0];

                        $arExportadorCadastro[$inIndexCadastro]["num_conta"]                    = implode('',separarDigito($rsCadastroPensionista->getCampo("nr_conta")));
                        $arExportadorCadastro[$inIndexCadastro]["destino_credito"]              = "01";//Conta Corrente
                        $arExportadorCadastro[$inIndexCadastro]["vazio"]                        = "";

                        $inIndexCadastro++;
                        $inQuantRegistrosCadastro++;
                        $rsCadastroPensionista->proximo();
                    }

                    /////////// CADASTRO EXPORTADOR - ESTAGIARIO
                    while (!$rsCadastroEstagiario->eof()) {

                        # GRAU INSTRUCAO
                        #    0	Não informado / 1	Analfabeto / 2	Alfabetizado / 4	1o grau incompleto
                        #    5	1o grau / 6	2o grau incompleto / 7	2o grau / 8	Superior incompleto
                        #    9	Superior/Especializa / 10	Mestrado / 11	Doutorado
                        #

                        switch ($rsCadastroEstagiario->getCampo("cod_escolaridade")) {
                            case 1://Analfabeto
                                  $inCodGrauInstrucao= 1;
                                  break;
                            case 2://1o Grau Incompleto
                            case 4:
                                  $inCodGrauInstrucao= 2;
                                  break;
                            case 5://1o Grau Completo
                                  $inCodGrauInstrucao= 3;
                                  break;
                            case 6://2o Grau Incompleto
                                  $inCodGrauInstrucao= 4;
                                  break;
                            case 7://2o Grau Completo
                                  $inCodGrauInstrucao= 5;
                                  break;
                            case 8://Superior Incompleto
                                  $inCodGrauInstrucao= 6;
                                  break;
                            case 9://Superior Completo
                            case 10:
                            case 11:
                                  $inCodGrauInstrucao= 7;
                                  break;
                            default://Nao Informado
                                   $inCodGrauInstrucao= 0;
                                  break;
                        }

                        $arExportadorCadastro[$inIndexCadastro]["matricula"]                    = $rsCadastroEstagiario->getCampo("numero_estagio");
                        $arExportadorCadastro[$inIndexCadastro]["cpf"]                          = preg_replace( "/[^0-9a-zA-Z]/i","",$rsCadastroEstagiario->getCampo("cpf"));
                        $arExportadorCadastro[$inIndexCadastro]["rg"]                           = preg_replace( "/[^0-9a-zA-Z]/i","",$rsCadastroEstagiario->getCampo("rg"));
                        $arExportadorCadastro[$inIndexCadastro]["orgao_emissor"]                = $rsCadastroEstagiario->getCampo("orgao_emissor");
                        $arExportadorCadastro[$inIndexCadastro]["uf_orgao_emissor"]             = ($rsCadastroEstagiario->getCampo("cod_uf_orgao_emissor") == '0')?"":$rsCadastroEstagiario->getCampo("uf_orgao_emissor");
                        $arExportadorCadastro[$inIndexCadastro]["nome"]                         = $rsCadastroEstagiario->getCampo("nom_cgm");

                        $dtAdmissao = explode('-',$rsCadastroEstagiario->getCampo("dt_inicio"));
                        $arExportadorCadastro[$inIndexCadastro]["dt_admissao"]                  = $dtAdmissao[2].$dtAdmissao[1].$dtAdmissao[0];
                        $arExportadorCadastro[$inIndexCadastro]["logradouro"]                   = $rsCadastroEstagiario->getCampo("logradouro");
                        $arExportadorCadastro[$inIndexCadastro]["complemento"]                  = $rsCadastroEstagiario->getCampo("complemento");
                        $arExportadorCadastro[$inIndexCadastro]["bairro"]                       = $rsCadastroEstagiario->getCampo("bairro");
                        $arExportadorCadastro[$inIndexCadastro]["numero"]                       = $rsCadastroEstagiario->getCampo("numero");
                        $arExportadorCadastro[$inIndexCadastro]["cidade"]                       = ($rsCadastroEstagiario->getCampo("cod_municipio") == '0')?"":$rsCadastroEstagiario->getCampo("cidade");
                        $arExportadorCadastro[$inIndexCadastro]["uf"]                           = ($rsCadastroEstagiario->getCampo("cod_uf") == '0')?"":$rsCadastroEstagiario->getCampo("uf");
                        $arExportadorCadastro[$inIndexCadastro]["cep"]                          = preg_replace( "/[^0-9a-zA-Z]/i","",$rsCadastroEstagiario->getCampo("cep"));
                        $arExportadorCadastro[$inIndexCadastro]["ddd"]                          = preg_replace( "/[^0-9]/i","",substr($rsCadastroEstagiario->getCampo("fone_residencial"),0,2));
                        $arExportadorCadastro[$inIndexCadastro]["fone_residencial"]             = preg_replace( "/[^0-9]/i","",substr($rsCadastroEstagiario->getCampo("fone_residencial"),2));

                        $dtNascimento = explode('-',$rsCadastroEstagiario->getCampo("dt_nascimento"));
                        $arExportadorCadastro[$inIndexCadastro]["dt_nascimento"]                = $dtNascimento[2].$dtNascimento[1].$dtNascimento[0];

                        $arExportadorCadastro[$inIndexCadastro]["nome_pai"]                     = (trim($rsCadastroEstagiario->getCampo("nom_pai")) == 'NAO INFORMADO')?"":$rsCadastroEstagiario->getCampo("nom_pai");
                        $arExportadorCadastro[$inIndexCadastro]["nome_mae"]                     = (trim($rsCadastroEstagiario->getCampo("nom_mae")) == 'NAO INFORMADO')?"":$rsCadastroEstagiario->getCampo("nom_mae");
                        $arExportadorCadastro[$inIndexCadastro]["nome_conjuge"]                 = "";
                        $arExportadorCadastro[$inIndexCadastro]["sexo"]                         = $rsCadastroEstagiario->getCampo("sexo");
                        $arExportadorCadastro[$inIndexCadastro]["estado_civil"]                 = "";
                        $arExportadorCadastro[$inIndexCadastro]["grau_instrucao"]               = $inCodGrauInstrucao;
                        $arExportadorCadastro[$inIndexCadastro]["num_banco"]                    = $rsCadastroEstagiario->getCampo("num_banco");

                        $arAgencia = separarDigito($rsCadastroEstagiario->getCampo("num_agencia"));
                        $arExportadorCadastro[$inIndexCadastro]["num_agencia"]                  = $arAgencia[0];

                        $arExportadorCadastro[$inIndexCadastro]["num_conta"]                    = implode('',separarDigito($rsCadastroEstagiario->getCampo("num_conta")));
                        $arExportadorCadastro[$inIndexCadastro]["destino_credito"]              = "01";//Conta Corrente
                        $arExportadorCadastro[$inIndexCadastro]["vazio"]                        = "";

                        $inIndexCadastro++;
                        $inQuantRegistrosCadastro++;
                        $rsCadastroEstagiario->proximo();
                    }

                    /////////// CADASTRO EXPORTADOR - PENSAO JUDICIAL
                    while (!$rsCadastroPensaoJudicial->eof()) {

                        # GRAU INSTRUCAO
                        #    0	Não informado / 1	Analfabeto / 2	Alfabetizado / 4	1o grau incompleto
                        #    5	1o grau / 6	2o grau incompleto / 7	2o grau / 8	Superior incompleto
                        #    9	Superior/Especializa / 10	Mestrado / 11	Doutorado
                        #

                        switch ($rsCadastroPensaoJudicial->getCampo("cod_escolaridade")) {
                            case 1://Analfabeto
                                  $inCodGrauInstrucao= 1;
                                  break;
                            case 2://1o Grau Incompleto
                            case 4:
                                  $inCodGrauInstrucao= 2;
                                  break;
                            case 5://1o Grau Completo
                                  $inCodGrauInstrucao= 3;
                                  break;
                            case 6://2o Grau Incompleto
                                  $inCodGrauInstrucao= 4;
                                  break;
                            case 7://2o Grau Completo
                                  $inCodGrauInstrucao= 5;
                                  break;
                            case 8://Superior Incompleto
                                  $inCodGrauInstrucao= 6;
                                  break;
                            case 9://Superior Completo
                            case 10:
                            case 11:
                                  $inCodGrauInstrucao= 7;
                                  break;
                            default://Nao Informado
                                   $inCodGrauInstrucao= 0;
                                  break;
                        }

                        $arExportadorCadastro[$inIndexCadastro]["matricula"]                    = $rsCadastroPensaoJudicial->getCampo("numcgm_dependente");
                        $arExportadorCadastro[$inIndexCadastro]["cpf"]                          = preg_replace( "/[^0-9a-zA-Z]/i","",$rsCadastroPensaoJudicial->getCampo("cpf"));
                        $arExportadorCadastro[$inIndexCadastro]["rg"]                           = preg_replace( "/[^0-9a-zA-Z]/i","",$rsCadastroPensaoJudicial->getCampo("rg"));
                        $arExportadorCadastro[$inIndexCadastro]["orgao_emissor"]                = $rsCadastroPensaoJudicial->getCampo("orgao_emissor");
                        $arExportadorCadastro[$inIndexCadastro]["uf_orgao_emissor"]             = ($rsCadastroPensaoJudicial->getCampo("cod_uf_orgao_emissor") == '0')?"":$rsCadastroPensaoJudicial->getCampo("uf_orgao_emissor");
                        $arExportadorCadastro[$inIndexCadastro]["nome"]                         = $rsCadastroPensaoJudicial->getCampo("nom_cgm");

                        $dtAdmissao = explode('-',$rsCadastroPensaoJudicial->getCampo("dt_inclusao"));
                        $arExportadorCadastro[$inIndexCadastro]["dt_admissao"]                  = $dtAdmissao[2].$dtAdmissao[1].$dtAdmissao[0];
                        $arExportadorCadastro[$inIndexCadastro]["logradouro"]                   = $rsCadastroPensaoJudicial->getCampo("logradouro");
                        $arExportadorCadastro[$inIndexCadastro]["complemento"]                  = $rsCadastroPensaoJudicial->getCampo("complemento");
                        $arExportadorCadastro[$inIndexCadastro]["bairro"]                       = $rsCadastroPensaoJudicial->getCampo("bairro");
                        $arExportadorCadastro[$inIndexCadastro]["numero"]                       = $rsCadastroPensaoJudicial->getCampo("numero");
                        $arExportadorCadastro[$inIndexCadastro]["cidade"]                       = ($rsCadastroPensaoJudicial->getCampo("cod_municipio") == '0')?"":$rsCadastroPensaoJudicial->getCampo("cidade");
                        $arExportadorCadastro[$inIndexCadastro]["uf"]                           = ($rsCadastroPensaoJudicial->getCampo("cod_uf") == '0')?"":$rsCadastroPensaoJudicial->getCampo("uf");
                        $arExportadorCadastro[$inIndexCadastro]["cep"]                          = preg_replace( "/[^0-9a-zA-Z]/i","",$rsCadastroPensaoJudicial->getCampo("cep"));
                        $arExportadorCadastro[$inIndexCadastro]["ddd"]                          = preg_replace( "/[^0-9]/i","",substr($rsCadastroPensaoJudicial->getCampo("fone_residencial"),0,2));
                        $arExportadorCadastro[$inIndexCadastro]["fone_residencial"]             = preg_replace( "/[^0-9]/i","",substr($rsCadastroPensaoJudicial->getCampo("fone_residencial"),2));

                        $dtNascimento = explode('-',$rsCadastroPensaoJudicial->getCampo("dt_nascimento"));
                        $arExportadorCadastro[$inIndexCadastro]["dt_nascimento"]                = $dtNascimento[2].$dtNascimento[1].$dtNascimento[0];

                        $arExportadorCadastro[$inIndexCadastro]["nome_pai"]                     = "";
                        $arExportadorCadastro[$inIndexCadastro]["nome_mae"]                     = "";
                        $arExportadorCadastro[$inIndexCadastro]["nome_conjuge"]                 = "";
                        $arExportadorCadastro[$inIndexCadastro]["sexo"]                         = $rsCadastroPensaoJudicial->getCampo("sexo");
                        $arExportadorCadastro[$inIndexCadastro]["estado_civil"]                 = "";
                        $arExportadorCadastro[$inIndexCadastro]["grau_instrucao"]               = $inCodGrauInstrucao;
                        $arExportadorCadastro[$inIndexCadastro]["num_banco"]                    = $rsCadastroPensaoJudicial->getCampo("num_banco");

                        $arAgencia = separarDigito($rsCadastroPensaoJudicial->getCampo("num_agencia"));
                        $arExportadorCadastro[$inIndexCadastro]["num_agencia"]                  = $arAgencia[0];

                        $arExportadorCadastro[$inIndexCadastro]["num_conta"]                    = implode('',separarDigito($rsCadastroPensaoJudicial->getCampo("conta_corrente")));
                        $arExportadorCadastro[$inIndexCadastro]["destino_credito"]              = "01";//Conta Corrente
                        $arExportadorCadastro[$inIndexCadastro]["vazio"]                        = "";

                        $inIndexCadastro++;
                        $inQuantRegistrosCadastro++;
                        $rsCadastroPensaoJudicial->proximo();
                    }

                    $arSessaoDadosArquivosRemessaBanPara = Sessao::read("dadosArquivosRemessaBanPara");

                    $arSessaoDadosArquivosRemessaBanPara[$stNomeArquivo] = array();
                    $arSessaoDadosArquivosRemessaBanPara[$stNomeArquivo]['nuRegistros'] = $inQuantRegistrosCadastro-2;

                    Sessao::write("dadosArquivosRemessaBanPara", $arSessaoDadosArquivosRemessaBanPara);

                    ##################################### CADASTRO - CABEÇALHO ARQUIVO ####################################
                    $arCabecalhoArquivoCadastro = array();
                    $arCabecalhoArquivoCadastro[0]['vazio']                     = "";
                    $arCabecalhoArquivoCadastro[0]['nome_arquivo']              = "CADASTRO";
                    $arCabecalhoArquivoCadastro[0]['codigo_empresa']            = $rsConfiguracaoBanparaEmpresa->getCampo('codigo');
                    $arCabecalhoArquivoCadastro[0]['codigo_orgao']              = $rsConfiguracaoBanpara->getCampo('codigo');
                    $arCabecalhoArquivoCadastro[0]['dt_geracao']                = $dtGeracaoArquivo;
                    $arCabecalhoArquivoCadastro[0]['hr_geracao']                = date("His");

                    $rsCabecalhoArquivoCadastro = new RecordSet();
                    $rsCabecalhoArquivoCadastro->preenche($arCabecalhoArquivoCadastro);
                    $obExportador->roUltimoArquivo->addBloco($rsCabecalhoArquivoCadastro);
                    unset($rsCabecalhoArquivoCadastro);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vazio");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(15);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nome_arquivo");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codigo_empresa");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codigo_orgao");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_geracao");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("hr_geracao");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vazio");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(429);

                    ##################################### CADASTRO - DETALHE ARQUIVO ####################################
                    $rsExportadorCadastro = new RecordSet();
                    $rsExportadorCadastro->preenche($arExportadorCadastro);
                    $obExportador->roUltimoArquivo->addBloco($rsExportadorCadastro);
                    unset($rsExportadorCadastro);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vazio");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("matricula");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(7);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cpf");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(11);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("rg");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(15);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("orgao_emissor");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(15);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("uf_orgao_emissor");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nome");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(40);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_admissao");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("logradouro");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(30);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("complemento");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(30);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("bairro");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(20);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cidade");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(25);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("uf");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cep");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(9);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vazio");//categoria funcional
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("ddd");//ddd
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("fone_residencial");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(9);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_nascimento");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nome_pai");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(40);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nome_mae");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(40);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("sexo");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("estado_civil");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nome_conjuge");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(40);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("grau_instrucao");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vazio");//motivo afastamento
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_banco");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_agencia");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_conta");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(10);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("destino_credito");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vazio");//nome representante
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(40);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vazio");//cpf representante
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(11);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vazio");//rg representante
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(15);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vazio");//orgao_emissor rg representante
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(10);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vazio");//uf_orgao_emissor rg representante
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vazio");//dt_validade
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

                    ##################################### CADASTRO - RODAPÉ ARQUIVO ####################################
                    $arRodapeArquivoCadastro = array();
                    $arRodapeArquivoCadastro[0]['tipo_registro']             = "99999";
                    $arRodapeArquivoCadastro[0]['quantidade_registros']      = $inQuantRegistrosCadastro;
                    $arRodapeArquivoCadastro[0]['vazio']                     = "";

                    $rsRodapeArquivoCadastro = new RecordSet();
                    $rsRodapeArquivoCadastro->preenche($arRodapeArquivoCadastro);
                    $obExportador->roUltimoArquivo->addBloco($rsRodapeArquivoCadastro);
                    unset($rsRodapeArquivoCadastro);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(5);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(5);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(5);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("quantidade_registros");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vazio");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(461);
                    ###################################################################
                    $rsConfiguracaoBanpara->proximo();
            }//end while ! rsOrgao->eof
        }//end if rsOrgao->linhas > 0
        $obExportador->Show();
        Sessao::encerraExcecao();
    break;
}

function separarDigito($stString)
{
    $inNumero = preg_replace( "/[^0-9a-zA-Z]/i","",$stString);
    $inDigito = $inNumero[strlen($inNumero)-1];
    $inNumero = substr($inNumero,0,strlen($inNumero)-1);

    return array($inNumero,$inDigito);
}

function removeAcentuacao($str)
{
  $from = 'ÀÁÃÂÉÊÍÓÕÔÚÜÇàáãâéêíóõôúüç';
  $to   = 'AAAAEEIOOOUUCaaaaeeiooouuc';

  return strtr($str, $from, $to);
}

?>
