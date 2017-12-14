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
   * Exportação Banco HSBC
   * Data de Criação   : 14/12/2009

   * @author Analista      Dagiane Vieira
   * @author Desenvolvedor Diego Mancilha

   * @package URBEM
   * @subpackage Instancias

     $Id:$
   */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CLA_EXPORTADOR );

$stAcao = $_REQUEST["stAcao"] ? $_REQUEST["stAcao"] : $_GET["stAcao"];
$arSessaoLink = Sessao::read('link');
$stLink = "&pg=".$arSessaoLink["pg"]."&pos=".$arSessaoLink["pos"];

foreach ($_POST as $key=>$value) {
    $stLink .= $key."=".$value."&";
}

define('NUM_BCO', '399');

//Define o nome dos arquivos PHP
$stPrograma = "ExportacaoBancoHSBC";
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

Sessao::setTrataExcecao(true);

include_once(CAM_GRH_IMA_MAPEAMENTO."TIMAConfiguracaoConvenioHSBC.class.php");
$obTIMAConfiguracaoConvenioHSBC = new TIMAConfiguracaoConvenioHSBC();
$obTIMAConfiguracaoConvenioHSBC->recuperaTodos($rsConfiguracaoConvenio);

$rsContasConvenio = Sessao::read("rsContas");

include_once(CAM_GRH_IMA_MAPEAMENTO."TIMAConfiguracaoHSBCOrgao.class.php");
$obTIMAConfiguracaoHSBCOrgao = new TIMAConfiguracaoHSBCOrgao;

include_once(CAM_GRH_IMA_MAPEAMENTO."TIMAConfiguracaoHSBCLocal.class.php");
$obTIMAConfiguracaoHSBCLocal = new TIMAConfiguracaoHSBCLocal;

include_once(CAM_GRH_IMA_MAPEAMENTO."TIMAConfiguracaoHSBCBanco.class.php");
$obTIMAConfiguracaoHSBCBanco = new TIMAConfiguracaoHSBCBanco;

$obExportador = new Exportador();
$obExportador->setRetorno($pgForm);
$arArquivoDadosExtras = array();
$inNumeroSequencial = $_POST["inNumeroSequencial"];

$stSituacaoContrato = recuperarSituacaoContrato();

while (!$rsContasConvenio->eof()) {
        //Busca os orgões cadastrados na configuração de cada conta individualmente
        //esses códigos devem ser incluídos no filtro fixo junto com o filtro de banco
        $obTIMAConfiguracaoHSBCOrgao->setDado("cod_convenio"      , $rsContasConvenio->getCampo("cod_convenio"));
        $obTIMAConfiguracaoHSBCOrgao->setDado("cod_agencia"       , $rsContasConvenio->getCampo("cod_agencia"));
        $obTIMAConfiguracaoHSBCOrgao->setDado("cod_banco"         , $rsContasConvenio->getCampo("cod_banco"));
        $obTIMAConfiguracaoHSBCOrgao->setDado("cod_conta_corrente", $rsContasConvenio->getCampo("cod_conta_corrente"));
        $obTIMAConfiguracaoHSBCOrgao->setDado("timestamp"         , $rsContasConvenio->getCampo("timestamp"));
        $obTIMAConfiguracaoHSBCOrgao->recuperaPorChave($rsOrgaos);
        $stCodOrgaos = "";
        while (!$rsOrgaos->eof()) {
            $stCodOrgaos .= $rsOrgaos->getCampo("cod_orgao").",";
            $rsOrgaos->proximo();
        }
        $stCodOrgaos = substr($stCodOrgaos,0,strlen($stCodOrgaos)-1);

        $obTIMAConfiguracaoHSBCLocal->setDado("cod_convenio"      , $rsContasConvenio->getCampo("cod_convenio"));
        $obTIMAConfiguracaoHSBCLocal->setDado("cod_agencia"       , $rsContasConvenio->getCampo("cod_agencia"));
        $obTIMAConfiguracaoHSBCLocal->setDado("cod_banco"         , $rsContasConvenio->getCampo("cod_banco"));
        $obTIMAConfiguracaoHSBCLocal->setDado("cod_conta_corrente", $rsContasConvenio->getCampo("cod_conta_corrente"));
        $obTIMAConfiguracaoHSBCLocal->setDado("timestamp"         , $rsContasConvenio->getCampo("timestamp"));
        $obTIMAConfiguracaoHSBCLocal->recuperaPorChave($rsLocais);
        $stCodLocais = "";
        while (!$rsLocais->eof()) {
            $stCodLocais .= $rsLocais->getCampo("cod_local").",";
            $rsLocais->proximo();
        }
        if (strlen($stCodLocais) > 0) {
            $stCodLocais = substr($stCodLocais,0,strlen($stCodLocais)-1);
        }

        ### BUSCA OUTROS BANCOS
        $obTIMAConfiguracaoHSBCBanco->setDado("cod_convenio"      , $rsContasConvenio->getCampo("cod_convenio"));
        $obTIMAConfiguracaoHSBCBanco->setDado("cod_agencia"       , $rsContasConvenio->getCampo("cod_agencia"));
        $obTIMAConfiguracaoHSBCBanco->setDado("cod_banco"         , $rsContasConvenio->getCampo("cod_banco"));
        $obTIMAConfiguracaoHSBCBanco->setDado("cod_conta_corrente", $rsContasConvenio->getCampo("cod_conta_corrente"));
        $obTIMAConfiguracaoHSBCBanco->setDado("timestamp"         , $rsContasConvenio->getCampo("timestamp"));
        $obTIMAConfiguracaoHSBCBanco->recuperaPorChave($rsBancos);
        $stCodBancos = "";
        while (!$rsBancos->eof()) {
            $stCodBancos .= $rsBancos->getCampo("cod_banco_outros").",";
            $rsBancos->proximo();
        }
        if (strlen($stCodBancos) > 0) {
            $stCodBancos = substr($stCodBancos,0,strlen($stCodBancos)-1);
            $stCodBancos = $stCodBancos.','.$rsConfiguracaoConvenio->getCampo('cod_banco');
        } else {
            $stCodBancos = $rsConfiguracaoConvenio->getCampo('cod_banco');
        }

        $arAgenciaConvenio          = separarDigito($rsContasConvenio->getCampo("num_agencia"));
        $inDigitoVerificadorAgencia = $arAgenciaConvenio[1];
        $inAgenciaConvenio          = $arAgenciaConvenio[0];
        $arContaConvenio            = separarDigito($rsContasConvenio->getCampo("num_conta_corrente"));
        $inDigitoVerificadorConta   = $arContaConvenio[1];
        $inContaConvenio            = $arContaConvenio[0];

        ################################## ENTIDADE #################################

        include_once(CAM_GRH_ENT_MAPEAMENTO."TEntidade.class.php");
        $obTEntidade = new TEntidade();
        $stFiltro  = " AND entidade.cod_entidade = ".Sessao::getCodEntidade($boTransacao);
        $stFiltro .= " AND entidade.exercicio = '".Sessao::getExercicio()."'";
        $obTEntidade->recuperaInformacoesCGMEntidade($rsEntidade,$stFiltro);

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

        ################################## EXPORTADOR ###################################
        // Criando objeto que monta arquivo de remessa
        $stNomeArquivo = "Cpg".date('dm').'a'.$inContaConvenio.".seq";
        $arArquivoDadosExtras[$stNomeArquivo]['descricao'] = $rsContasConvenio->getCampo("descricao");
        $arArquivoDadosExtras[$stNomeArquivo]['seq']       = $inNumeroSequencial;

        $obExportador->addArquivo($stNomeArquivo);
        $obExportador->roUltimoArquivo->setTipoDocumento("RemessaHSBC");
        $inNumLote = 0;
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

        ################################## ATIVOS/APOSENTADOS/PENSIONISTA ###################################
        $stFiltroContrato  = "\n AND cod_orgao IN (".$stCodOrgaos.")";
        if (trim($stCodLocais) != "") {
            $stFiltroContrato .= "\n AND cod_local IN (".$stCodLocais.")";
        }

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
        $stFiltroEstagiario  = " AND cod_orgao IN (".$stCodOrgaos.")";
        if (trim($stCodLocais) != "") {
            $stFiltroEstagiario .= " AND cod_local IN (".$stCodLocais.")";
        }

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
        $stFiltroPensaoJudicial  = " AND cod_orgao IN (".$stCodOrgaos.")";
        if (trim($stCodLocais) != "") {
            $stFiltroPensaoJudicial .= " AND cod_local IN (".$stCodLocais.")";
        }

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
        }//
        #############################################################################

        $rsContrato = new RecordSet();
        if ($_POST["stSituacao"] == 'ativos' ||
            $_POST["stSituacao"] == 'aposentados' ||
            $_POST["stSituacao"] == 'rescindidos' ||
            $_POST["stSituacao"] == 'pensionistas' ||
            $_POST["stSituacao"] == 'todos') {

            include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoCalculado.class.php" );
            $obRecuperaEventoCalculado = new TFolhaPagamentoEventoCalculado();

            $obRecuperaEventoCalculado->setDado("inCodPeriodoMovimentacao", $rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao"));
            $obRecuperaEventoCalculado->setDado("stSituacao"              , $_POST["stSituacao"]);
            $obRecuperaEventoCalculado->setDado("inCodConfiguracao"       , $_POST["inCodConfiguracao"]);
            $obRecuperaEventoCalculado->setDado("inCodComplementar"       , ($_POST["inCodComplementar"]==""?0:$_POST["inCodComplementar"]));
            $obRecuperaEventoCalculado->setDado("stTipoFiltro"            , $_POST["stTipoFiltro"]);
            $obRecuperaEventoCalculado->setDado("stValoresFiltro"         , $stValoresFiltro);
            $obRecuperaEventoCalculado->setDado("stDesdobramento"         , $_POST["stDesdobramento"]);
            $obRecuperaEventoCalculado->setDado("inCodBanco"              , $stCodBancos /*$rsConfiguracaoConvenio->getCampo("cod_banco")*/);
            $obRecuperaEventoCalculado->setDado("nuLiquidoMinimo"         , $nuValorLiquidoInicial);
            $obRecuperaEventoCalculado->setDado("nuLiquidoMaximo"         , $nuValorLiquidoFinal);
            $obRecuperaEventoCalculado->setDado("nuPercentualPagar"       , $nuPercentualPagar);
            $obRecuperaEventoCalculado->recuperaContratosCalculadosRemessaBancos($rsContrato,$stFiltroContrato);
        }

        $rsEstagio = new RecordSet();
        if ($_POST["stSituacao"] == 'estagiarios' ||
            $_POST["stSituacao"] == 'todos') {
            include_once(CAM_GRH_EST_MAPEAMENTO."TEstagioEstagiarioEstagio.class.php");
            $obTEstagioEstagiorioEstagio = new TEstagioEstagiarioEstagio();
            $obTEstagioEstagiorioEstagio->setDado("inCodPeriodoMovimentacao", $rsUltimaMovimentacao->getCampo("cod_periodo_movimentacao"));
            $obTEstagioEstagiorioEstagio->setDado("inCodBanco"              , $stCodBancos /*$rsConfiguracaoConvenio->getCampo("cod_banco")*/);
            $obTEstagioEstagiorioEstagio->setDado("stTipoFiltro"            , $_POST["stTipoFiltro"]);
            $obTEstagioEstagiorioEstagio->setDado("stValoresFiltro"         , $stValoresFiltro);
            $obTEstagioEstagiorioEstagio->setDado("nuLiquidoMinimo"         , $nuValorLiquidoInicial);
            $obTEstagioEstagiorioEstagio->setDado("nuLiquidoMaximo"         , $nuValorLiquidoFinal);
            $obTEstagioEstagiorioEstagio->setDado("nuPercentualPagar"       , $nuPercentualPagar);
            $obTEstagioEstagiorioEstagio->recuperaRemessaBancos($rsEstagio,$stFiltroEstagiario);
        }

        $rsPensaoJudicial = new RecordSet();
        if ($_POST["stSituacao"] == 'todos' ||
            $_POST["stSituacao"] == 'pensao_judicial') {

            include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoCalculadoDependente.class.php" );
            $obTFolhaPagamentoEventoCalculadoDependente = new TFolhaPagamentoEventoCalculadoDependente();
            $obTFolhaPagamentoEventoCalculadoDependente->setDado("inCodPeriodoMovimentacao", $rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao"));
            $obTFolhaPagamentoEventoCalculadoDependente->setDado("inCodConfiguracao"       , $_POST["inCodConfiguracao"]);
            $obTFolhaPagamentoEventoCalculadoDependente->setDado("inCodComplementar"       , ($_POST["inCodComplementar"]==""?0:$_POST["inCodComplementar"]));
            $obTFolhaPagamentoEventoCalculadoDependente->setDado("stDesdobramento"         , $_POST["stDesdobramento"]);
            $obTFolhaPagamentoEventoCalculadoDependente->setDado("inCodBanco"              , $stCodBancos /*$rsConfiguracaoConvenio->getCampo("cod_banco")*/);
            $obTFolhaPagamentoEventoCalculadoDependente->setDado("nuLiquidoMinimo"         , $nuValorLiquidoInicial);
            $obTFolhaPagamentoEventoCalculadoDependente->setDado("nuLiquidoMaximo"         , $nuValorLiquidoFinal);
            $obTFolhaPagamentoEventoCalculadoDependente->setDado("nuPercentualPagar"       , $nuPercentualPagar);
            $obTFolhaPagamentoEventoCalculadoDependente->recuperaContratosCalculadosRemessaBancos($rsPensaoJudicial,$stFiltroPensaoJudicial);
        }

        #############################################################################
        $arExportador = array();
        $inIndex = 0;
        $nuVlrLancamentoTotal = 0;
        $inQuantRegistros = 4;

        ## Definindo array do cabeçalho do arquivo e do lote de serviço ##############
        $arCabecalhoArquivo = array();
        $arCabecalhoArquivo[0]['branco']                                       = " ";
        $arCabecalhoArquivo[0]['banco']                                        = NUM_BCO;
        $arCabecalhoArquivo[0]['lote']                                         = $inNumLote;
        $arCabecalhoArquivo[0]['registro']                                     = 0;
        $arCabecalhoArquivo[0]['tipo_inscricao']                               = 2;
        $arCabecalhoArquivo[0]['numero_inscricao']                             = $rsEntidade->getCampo("cnpj");
        $arCabecalhoArquivo[0]['convenio']                                     = $rsConfiguracaoConvenio->getCampo("cod_convenio_banco");
        $arCabecalhoArquivo[0]['codigo_agencia']                               = $inAgenciaConvenio;
        $arCabecalhoArquivo[0]['numero_conta_corrente']                        = $inContaConvenio;
        $arCabecalhoArquivo[0]['digito_verificador_conta']                     = $inDigitoVerificadorConta;
        $arCabecalhoArquivo[0]['digito_verificador_agencia']                   = $inDigitoVerificadorAgencia;
        $arCabecalhoArquivo[0]['nome_empresa']                                 = $rsEntidade->getCampo("nom_cgm");
        $arCabecalhoArquivo[0]['nome_banco']                                   = "HSBC BANK BRASIL S.A";
        $arCabecalhoArquivo[0]['codigo_remessa']                               = 1;
        $arCabecalhoArquivo[0]['data_geracao']                                 = str_replace("/","",$_POST["dtGeracaoArquivo"]);
        $arCabecalhoArquivo[0]['hora_geracao']                                 = date("His");
        $arCabecalhoArquivo[0]['numero_sequencial']                            = $inNumeroSequencial;
        $arCabecalhoArquivo[0]['numero_versao_layout']                         = 20;
        $arCabecalhoArquivo[0]['densidade_gravacao_arquivo']                   = 1600;
        $arCabecalhoArquivo[0]['sigla_aplicativo']                             = "CPG";
        $arCabecalhoArquivo[0]['identifica_ano_2000']                          = "Y2K";

        include_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoMunicipio.class.php");
        $obTAdministracaoMunicipio = new TMunicipio();
        $obTAdministracaoMunicipio->setDado("cod_municipio",$rsEntidade->getCampo("cod_municipio"));
        $obTAdministracaoMunicipio->setDado("cod_uf",$rsEntidade->getCampo("cod_uf"));
        $obTAdministracaoMunicipio->recuperaPorChave($rsMunicipio);

        include_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoUF.class.php");
        $obTAdministracaoUF = new TUF();
        $obTAdministracaoUF->setDado("cod_uf",$rsEntidade->getCampo("cod_uf"));
        $obTAdministracaoUF->recuperaPorChave($rsUF);

        $stCEP  = $rsEntidade->getCampo("cep");
        $inCEP1 = substr($stCEP,0,5);
        $inCEP2 = substr($stCEP,4,3);

        $inNumLote = $inNumLote + 1;
        $arCabecalhoLote[0]['branco']                                       = " ";
        $arCabecalhoLote[0]['banco']                                        = NUM_BCO;
        $arCabecalhoLote[0]['lote']                                         = $inNumLote;
        $arCabecalhoLote[0]['registro']                                     = 1;
        $arCabecalhoLote[0]['operacao']                                     = "C";
        $arCabecalhoLote[0]['servico']                                      = 30;
        $arCabecalhoLote[0]['forma_lancamento']                             = 1;
        $arCabecalhoLote[0]['versao_layout']                                = 20;
        $arCabecalhoLote[0]['tipo_inscricao']                               = 2;
        $arCabecalhoLote[0]['numero_inscricao']                             = $rsEntidade->getCampo("cnpj");
        $arCabecalhoLote[0]['convenio']                                     = $rsConfiguracaoConvenio->getCampo("cod_convenio_banco");
        $arCabecalhoLote[0]['codigo_agencia']                               = $inAgenciaConvenio;
        $arCabecalhoLote[0]['numero_conta_corrente']                        = $inContaConvenio;
        $arCabecalhoLote[0]['digito_verificador_conta']                     = $inDigitoVerificadorConta;
        $arCabecalhoLote[0]['digito_verificador_agencia']                   = $inDigitoVerificadorAgencia;
        $arCabecalhoLote[0]['nome_empresa']                                 = formata_alfanumerico($rsEntidade->getCampo("nom_cgm"));
        $arCabecalhoLote[0]['mensagem']                                     = "";
        $arCabecalhoLote[0]['logradouro']                                   = formata_alfanumerico($rsEntidade->getCampo("logradouro"));
        $arCabecalhoLote[0]['numero_local']                                 = formata_alfanumerico($rsEntidade->getCampo("numero"));
        $arCabecalhoLote[0]['complemento']                                  = formata_alfanumerico($rsEntidade->getCampo("complemento"));
        $arCabecalhoLote[0]['cidade']                                       = formata_alfanumerico($rsMunicipio->getCampo('nom_municipio'));
        $arCabecalhoLote[0]['cep']                                          = $inCEP1;
        $arCabecalhoLote[0]['complemento_cep']                              = $inCEP2;
        $arCabecalhoLote[0]['estado']                                       = $rsUF->getCampo("sigla_uf");
        $arCabecalhoLote[0]['emissao_em_lote']                              = "N";
        #############################################################################

        while (!$rsContrato->eof()) {

            $inNumLote = $inNumLote + 1;

            $arExportador[$inIndex]["banco"]                         = NUM_BCO;
            $arExportador[$inIndex]["lote"]                          = $inNumLote;
            $arExportador[$inIndex]["registro_detalhe_lote"]         = 3;
            $arExportador[$inIndex]["numero_sequencial"]             = $inIndex+1;
            $arExportador[$inIndex]["segmento"]                      = "A";
            $arExportador[$inIndex]["tipo_movimento"]                = 0;
            $arExportador[$inIndex]["codigo_instrucao"]              = 0;
            $arExportador[$inIndex]["compensacao"]                   = 18;
            $arExportador[$inIndex]["numero_banco_favorecido"]       = $rsContrato->getCampo("num_banco");

            $arAgencia = separarDigito($rsContrato->getCampo("num_agencia"));
            $arExportador[$inIndex]["numero_agencia"]                = $arAgencia[0];
            $arExportador[$inIndex]["digito_agencia"]                = $arAgencia[1];

            $arConta = separarDigito($rsContrato->getCampo("nr_conta"));
            $arExportador[$inIndex]["nr_conta"]                      = $arConta[0];
            $arExportador[$inIndex]["digito_conta"]                  = $arConta[1];

            $arExportador[$inIndex]["nome_favorecido"]               = formata_alfanumerico($rsContrato->getCampo("nom_cgm"));
            $arExportador[$inIndex]["registro"]                      = $rsContrato->getCampo("registro");
            $arExportador[$inIndex]["competencia"]                   = $_REQUEST['inAno'].str_pad($_REQUEST['inCodMes'], 2, "0", STR_PAD_LEFT);
            $arExportador[$inIndex]["cpf"]                           = $rsContrato->getCampo("cpf");
            $arExportador[$inIndex]["finalidade"]                    = 4;
            $arExportador[$inIndex]["dt_lancamento"]                 = str_replace("/","",$_POST['dtPagamento']);
            $arExportador[$inIndex]["tipo_moeda"]                    = "R$";

            $nuVlrLancamento = number_format($rsContrato->getCampo("liquido"), 2, ".", "");
            $nuVlrLancamentoTotal += $nuVlrLancamento;
            $nuVlrLancamento = number_format($nuVlrLancamento, 2, "", "");

            $arExportador[$inIndex]["vlr_lancamento"]                = $nuVlrLancamento;
            $arExportador[$inIndex]["dt_real_efetivacao"]            = 0;
            $arExportador[$inIndex]["vl_real_efetivacao"]            = 0;

            $arExportador[$inIndex]["tipo_inscricao"]                = 1;
            $arExportador[$inIndex]["nr_documento"]                  = $rsContrato->getCampo("cpf");
            $arExportador[$inIndex]["branco"]                        = " ";
            $arExportador[$inIndex]["aviso_favorecido"]              = 0;
            $arExportador[$inIndex]["emissao_individual"]            = "N";

            $inIndex++;
            $inQuantRegistros++;

            $rsContrato->proximo();
        }

        while (!$rsEstagio->eof()) {
            $inNumLote = $inNumLote + 1;
            $arExportador[$inIndex]["banco"]                         = NUM_BCO;
            $arExportador[$inIndex]["lote"]                          = $inNumLote;
            $arExportador[$inIndex]["registro_detalhe_lote"]         = 3;
            $arExportador[$inIndex]["numero_sequencial"]             = $inIndex+1;
            $arExportador[$inIndex]["segmento"]                      = "A";
            $arExportador[$inIndex]["tipo_movimento"]                = 0;
            $arExportador[$inIndex]["codigo_instrucao"]              = 0;
            $arExportador[$inIndex]["compensacao"]                   = 18;
            $arExportador[$inIndex]["numero_banco_favorecido"]       = $rsEstagio->getCampo("num_banco");

            $arAgencia = separarDigito($rsEstagio->getCampo("num_agencia"));
            $arExportador[$inIndex]["numero_agencia"]                = $arAgencia[0];
            $arExportador[$inIndex]["digito_agencia"]                = $arAgencia[1];

            $arConta = separarDigito($rsEstagio->getCampo("nr_conta"));
            $arExportador[$inIndex]["nr_conta"]                      = $arConta[0];
            $arExportador[$inIndex]["digito_conta"]                  = $arConta[1];

            $arExportador[$inIndex]["nome_favorecido"]               = formata_alfanumerico($rsEstagio->getCampo("nom_cgm"));
            $arExportador[$inIndex]["registro"]                      = $rsEstagio->getCampo("registro");
            $arExportador[$inIndex]["competencia"]                   = $_REQUEST['inAno'].str_pad($_REQUEST['inCodMes'], 2, "0", STR_PAD_LEFT);
            $arExportador[$inIndex]["cpf"]                           = $rsEstagio->getCampo("cpf");
            $arExportador[$inIndex]["finalidade"]                    = 4;
            $arExportador[$inIndex]["dt_lancamento"]                 = str_replace("/","",$_POST['dtPagamento']);
            $arExportador[$inIndex]["tipo_moeda"]                    = "R$";

            $nuVlrLancamento = number_format($rsEstagio->getCampo("liquido"), 2, ".", "");
            $nuVlrLancamentoTotal += $nuVlrLancamento;
            $nuVlrLancamento = number_format($nuVlrLancamento, 2, "", "");

            $arExportador[$inIndex]["vlr_lancamento"]                = $nuVlrLancamento;
            $arExportador[$inIndex]["dt_real_efetivacao"]            = 0;
            $arExportador[$inIndex]["vl_real_efetivacao"]            = 0;

            $arExportador[$inIndex]["tipo_inscricao"]                = 1;
            $arExportador[$inIndex]["nr_documento"]                  = $rsEstagio->getCampo("cpf");
            $arExportador[$inIndex]["branco"]                        = " ";
            $arExportador[$inIndex]["aviso_favorecido"]              = 0;
            $arExportador[$inIndex]["emissao_individual"]            = "N";

            $inIndex++;
            $inQuantRegistros++;

            $rsEstagio->proximo();
        }

        while (!$rsPensaoJudicial->eof()) {
            $inNumLote = $inNumLote + 1;
            $arExportador[$inIndex]["banco"]                         = NUM_BCO;
            $arExportador[$inIndex]["lote"]                          = $inNumLote;
            $arExportador[$inIndex]["registro_detalhe_lote"]         = 3;
            $arExportador[$inIndex]["numero_sequencial"]             = $inIndex+1;
            $arExportador[$inIndex]["segmento"]                      = "A";
            $arExportador[$inIndex]["tipo_movimento"]                = 0;
            $arExportador[$inIndex]["codigo_instrucao"]              = 0;
            $arExportador[$inIndex]["compensacao"]                   = 18;
            $arExportador[$inIndex]["numero_banco_favorecido"]       = $rsPensaoJudicial->getCampo("num_banco");

            $arAgencia = separarDigito($rsPensaoJudicial->getCampo("num_agencia"));
            $arExportador[$inIndex]["numero_agencia"]                = $arAgencia[0];
            $arExportador[$inIndex]["digito_agencia"]                = $arAgencia[1];

            $arConta = separarDigito($rsPensaoJudicial->getCampo("nr_conta"));
            $arExportador[$inIndex]["nr_conta"]                      = $arConta[0];
            $arExportador[$inIndex]["digito_conta"]                  = $arConta[1];

            $arExportador[$inIndex]["nome_favorecido"]               = formata_alfanumerico($rsPensaoJudicial->getCampo("nom_cgm"));
            $arExportador[$inIndex]["registro"]                      = $rsPensaoJudicial->getCampo("registro");
            $arExportador[$inIndex]["competencia"]                   = $_REQUEST['inAno'].str_pad($_REQUEST['inCodMes'], 2, "0", STR_PAD_LEFT);
            $arExportador[$inIndex]["cpf"]                           = $rsPensaoJudicial->getCampo("cpf");
            $arExportador[$inIndex]["finalidade"]                    = 101;
            $arExportador[$inIndex]["dt_lancamento"]                 = str_replace("/","",$_POST['dtPagamento']);
            $arExportador[$inIndex]["tipo_moeda"]                    = "R$";

            $nuVlrLancamento = number_format($rsPensaoJudicial->getCampo("liquido"), 2, ".", "");
            $nuVlrLancamentoTotal += $nuVlrLancamento;
            $nuVlrLancamento = number_format($nuVlrLancamento, 2, "", "");

            $arExportador[$inIndex]["vlr_lancamento"]                = $nuVlrLancamento;
            $arExportador[$inIndex]["dt_real_efetivacao"]            = 0;
            $arExportador[$inIndex]["vl_real_efetivacao"]            = 0;

            $arExportador[$inIndex]["tipo_inscricao"]                = 1;
            $arExportador[$inIndex]["nr_documento"]                  = $rsPensaoJudicial->getCampo("cpf");
            $arExportador[$inIndex]["branco"]                        = " ";
            $arExportador[$inIndex]["aviso_favorecido"]              = 0;
            $arExportador[$inIndex]["emissao_individual"]            = "N";

            $inIndex++;
            $inQuantRegistros++;

            $rsPensaoJudicial->proximo();
        }

        #####################################CABEÇALHO ARQUIVO####################################
        $rsCabecalhoArquivo = new RecordSet();
        $rsCabecalhoArquivo->preenche($arCabecalhoArquivo);
        $obExportador->roUltimoArquivo->addBloco($rsCabecalhoArquivo);
        $inNumeroSequencial++;

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("banco");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("lote");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("registro");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("branco");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(9);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_inscricao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_inscricao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("convenio");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("branco");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codigo_agencia");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(5);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("branco");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_conta_corrente");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(12);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("digito_verificador_conta");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("branco");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nome_empresa");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(30);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nome_banco");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(30);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("branco");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(10);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codigo_remessa");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("data_geracao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("hora_geracao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_sequencial");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_versao_layout");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("densidade_gravacao_arquivo");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(5);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("sigla_aplicativo");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("identifica_ano_2000");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("branco");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("branco");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(49);

        #######################################    ORDENA LOTES E REGISTROS    ######################################

        //Variavel que controla os bancos que fazem parte do lote
        //Variavel que armazema os dados do lote
        $lote[] = $dadosLote  = $arCabecalhoLote[0];
        //Contador de lotes
        $countLote = 0;

        foreach ($arExportador as $registro) {
          //Verifica se banco já está incluido no lote
          if (!in_array($registro['numero_banco_favorecido'], $lote)) {
            //Inclui banco na variavel de controle de lotes
            $lote[]                      = $registro['numero_banco_favorecido'];
            //Inclui banco com os dados do lote, utilizado para fazer a comparação dos registros no detalhe do lote
            $dadosLote['banco_lote']     = $registro['numero_banco_favorecido'];
            //Incrementa número do lote
            $dadosLote['lote']           = $countLote + 1;

            //Verifica se deve fazer doc ou transferencia normal
            //Se o banco do favorecido for o HSBC então faz transferência
            //Senão efetua DOC para conta do favorecido
            if ($dadosLote['banco_lote'] == NUM_BCO) {
              $dadosLote['forma_lancamento'] = 1;
            } else {
              $dadosLote['forma_lancamento'] = 3;
            }

            //Inclui o lote no array de lotes
            $arCabecalhoLote[$countLote] = $dadosLote;

            $countLote++;
          }
        }

        // Obtem uma lista de colunas a serem ordenadas
        foreach ($arExportador as $key => $row) {
            $data[$key]  = $row['numero_banco_favorecido'];
        }

        if (count($arExportador) > 0) {
            // Ordena os registros de acordo com o campo 'numero_banco_favorecido'
            array_multisort($data, SORT_ASC, $arExportador);

        foreach ($arCabecalhoLote as $cabecalho) {
          $cabecalhoLote[0] = $cabecalho;

          #######################################    CABEÇALHO DO LOTE    ######################################
          $rsCabecalhoLote = new RecordSet;
          $rsCabecalhoLote->preenche($cabecalhoLote);

          $obExportador->roUltimoArquivo->addBloco($rsCabecalhoLote);
          $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("banco");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);

          $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("lote");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);

          $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("registro");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

          $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("operacao");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

          $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("servico");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

          $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("forma_lancamento");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

          $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("versao_layout");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);

          $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("branco");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

          $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_inscricao");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

          $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_inscricao");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);

          $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("convenio");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);

          $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("branco");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);

          $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codigo_agencia");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(5);

          $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("branco");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

          $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_conta_corrente");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(12);

          $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("digito_verificador_conta");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

          $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("branco");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

          $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nome_empresa");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(30);

          $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("branco");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(40);

          $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("logradouro");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(30);

          $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_local");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(5);

          $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("complemento");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(15);

          $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cidade");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(20);

          $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cep");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(5);

          $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("complemento_cep");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);

          $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("estado");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

          $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("emissao_em_lote");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

          $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("branco");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(17);

          $numeroSequencial = 1;
          //Efetua a separação dos registros em lotes de acordo com o banco
          foreach ($arExportador as $registro) {
            //Verifica se banco do favorecido pertence ao lote que está sendo montado
            if ($cabecalhoLote[0]['banco_lote'] == $registro['numero_banco_favorecido']) {
                //Insere no registro o número do lote
                $registro['lote'] = $cabecalhoLote[0]['lote'];
                //Insere número sequencial do lote
                $registro['numero_sequencial'] = $numeroSequencial;
                $numeroSequencial++;
                //Acumula registros em que banco pertence ao lote que está sendo montado
                $registros[] = $registro;
            }
          }
          //Transfere do acumulador para variável que irá gerar o Recorset
          $arExportadorLote = $registros;
          //Esvazia acumulador
          unset($registros);

          ##################################### DETALHE DO LOTE ##############################################
          $rsExportador = new RecordSet();
          $rsExportador->preenche($arExportadorLote);

          $inQuantRegistrosLote = ($rsExportador->getNumLinhas() == -1) ? 0 : count($arExportadorLote);
          $arArquivoDadosExtras[$stNomeArquivo]['qtd'] = $inQuantRegistrosLote;
          $nuLiquidoTotal = number_format($nuVlrLancamentoTotal,2,",",".");
          $arArquivoDadosExtras[$stNomeArquivo]['total'] = $nuLiquidoTotal;

          Sessao::write('inQuantRegistrosLote', ($rsExportador->getNumLinhas() == -1) ? 0 : $rsExportador->getNumLinhas());
          Sessao::write('nuLiquidoTotal', number_format($nuVlrLancamentoTotal,2,",","."));
          $arQuantMoedas = $rsExportador->getSomaCampo("quant_moeda");
          $nuQuantMoedas = $arQuantMoedas["quant_moeda"];

          $obExportador->roUltimoArquivo->addBloco($rsExportador);

          $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("banco");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);

          $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("lote");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);

          $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("registro_detalhe_lote");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

          $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_sequencial");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(5);

          $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("segmento");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

          $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_movimento");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

          $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codigo_instrucao");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

          $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("branco");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);

          $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_banco_favorecido");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);

          $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_agencia");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(5);

          $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("branco");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

          $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nr_conta");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(12);

          $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("digito_conta");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

          $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("branco");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

          $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nome_favorecido");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(30);

          $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("registro");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(10);

          $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("competencia");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);

          $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("branco");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);

          $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_lancamento");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

          $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_moeda");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);

          $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("branco");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(17);

          $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vlr_lancamento");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

          $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("emissao_individual"); // Emissão Individual
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

          $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("branco"); // Pagador Efetivo
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(30);

          $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("branco"); // Brancos
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(12);

          $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("branco"); // Outras informações
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(40);

          $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("branco"); // Brancos
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(12);

          $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("aviso_favorecido");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

          $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("branco");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(10);

        }//FIM DO IF COUNT

          ##################################### RODAPÉ DO LOTE ################################################
          $arRodapeLote[0]['branco']                  = " ";
          $arRodapeLote[0]['banco']                   = $cabecalhoLote[0]['banco'];
          $arRodapeLote[0]['lote']                    = $cabecalhoLote[0]['lote'];
          $arRodapeLote[0]['registro']                = 5;
          $arRodapeLote[0]['branco']                  = " ";
          $arRodapeLote[0]["quant_registros"]         = $inQuantRegistrosLote + 2;

          $arRodapeLote[0]["valor_debito_credito"]    = str_replace(".","",number_format($nuVlrLancamentoTotal,2,".",""));
          $arRodapeLote[0]["quant_moedas"]            = $nuQuantMoedas;

          $rsRodapeLote = new RecordSet;
          $rsRodapeLote->preenche($arRodapeLote);

          $obExportador->roUltimoArquivo->addBloco($rsRodapeLote);
          $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("banco");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);

          $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("lote");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);

          $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("registro");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

          $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("branco");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(9);

          $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("quant_registros");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);

          $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("branco");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);

          $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("valor_debito_credito");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(15);

          $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("branco");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
          $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(199);

        }

        ##########################################RODAPÉ ARQUIVO#########################################
        $arRodape[0]['branco']          = " ";
        $arRodape[0]['banco']           = NUM_BCO;
        $arRodape[0]['lote']            = 9999;
        $arRodape[0]['registro']        = 9;
        $arRodape[0]['branco']          = " ";
        $arRodape[0]['quant_registros'] = $inQuantRegistros + 2;
        $arRodape[0]['quant_lotes']     = $countLote;
        $arRodape[0]['quant_contas']    = 0;

        $rsRodape = new RecordSet;
        $rsRodape->preenche($arRodape);

        $obExportador->roUltimoArquivo->addBloco($rsRodape);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("banco");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("lote");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("registro");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("branco");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(9);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("quant_lotes");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("quant_registros");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("branco");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(211);

        ########################UPDATE CONFIGURACAO########################################################
        include_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php");
        $obTAdministracaoConfiguracao = new TAdministracaoConfiguracao();
        $obTAdministracaoConfiguracao->setDado("exercicio",Sessao::getExercicio());
        $obTAdministracaoConfiguracao->setDado("cod_modulo",40);

        $arPeriodoMovimentacaoAtual = explode("/",$rsUltimaMovimentacao->getCampo('dt_final'));
        $dtPeriodoMovimentacaoAtual = $arPeriodoMovimentacaoAtual[2]."-".$arPeriodoMovimentacaoAtual[1]."-".$arPeriodoMovimentacaoAtual[0];

        $obTAdministracaoConfiguracao->setDado("parametro","dt_num_sequencial_arquivo_hsbc".Sessao::getEntidade());
        $obTAdministracaoConfiguracao->setDado("valor",$dtPeriodoMovimentacaoAtual);
        $obTAdministracaoConfiguracao->alteracao();

        $obTAdministracaoConfiguracao->setDado("parametro","num_sequencial_arquivo_hsbc".Sessao::getEntidade());
        $obTAdministracaoConfiguracao->setDado("valor",$inNumeroSequencial);
        $obTAdministracaoConfiguracao->alteracao();

        $rsContasConvenio->proximo();
}
$obExportador->Show();

Sessao::write("arArquivoDadosExtras",$arArquivoDadosExtras);
Sessao::encerraExcecao();

function separarDigito($stString)
{
    $inNumero = preg_replace( "/[^0-9a-zA-Z]/i","",$stString);
    $inDigito = $inNumero[strlen($inNumero)-1];
    $inNumero = substr($inNumero,0,strlen($inNumero)-1);

    return array($inNumero,$inDigito);
}

function suprimirAlpha($stString)
{
    $inNumero = preg_replace( "/[^0-9]/i","",$stString);

    return $inNumero;
}

function formata_alfanumerico($str)
{
  $from = 'ÀÁÃÂÉÊÍÓÕÔÚÜÇàáãâéêíóõôúüç';
  $to   = 'AAAAEEIOOOUUCaaaaeeiooouuc';

  return mb_strtoupper(strtr($str, $from, $to));
}

function recuperarSituacaoContrato()
{
    $stSituacaoContrato = "";
    if ($_POST["stSituacao"] == 'ativos') {
        $stSituacaoContrato = "'A'";
    }

    if ($_POST["stSituacao"] == 'aposentados') {
        $stSituacaoContrato = "'P'";
    }

    if ($_POST["stSituacao"] == 'rescindidos') {
        $stSituacaoContrato = "'R'";
    }

    if ($_POST["stSituacao"] == 'pensionistas') {
        $stSituacaoContrato = "'E'";
    }

    if ($_POST["stSituacao"] == 'todos') {
        $stSituacaoContrato = "'R','A','P','E'";
    }

    return $stSituacaoContrato;
}

?>
