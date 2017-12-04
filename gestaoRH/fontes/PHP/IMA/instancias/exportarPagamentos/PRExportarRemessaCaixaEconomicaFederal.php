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
    * Página de Processamento do Exportação Remessa CaixaEconomicaFederal
    * Data de Criação: 09/10/2007

    * @author Desenvolvedor: Alex Cardoso

    * Casos de uso: uc-04.08.11

    $Id: PRExportarRemessaCaixaEconomicaFederal.php 64152 2015-12-09 17:32:21Z evandro $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CLA_EXPORTADOR                  );

$stAcao = $request->get('stAcao');
$arSessaoLink = Sessao::read('link');
$stLink = "&pg=".$arSessaoLink["pg"]."&pos=".$arSessaoLink["pos"];

foreach ($request->getAll() as $key=>$value) {
    $stLink .= $key."=".$value."&";
}

//Define o nome dos arquivos PHP
$stPrograma = "ExportarRemessaCaixaEconomicaFederal";
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

switch ($stAcao) {
    case "exportar":
        Sessao::setTrataExcecao(true);
        
        //Busca codigo do tipo de arquivo que deve ser exportado de acordo com a configuracao
        //Gestão Recursos Humanos :: Informações Mensais e Anuais :: Configuração :: Exportação Banco Caixa
        $inCodTIpoArquivo = SistemaLegado::pegaDado('cod_tipo','ima.configuracao_convenio_caixa_economica_federal','');        

        $obExportador = new Exportador();
        if ( $inCodTIpoArquivo == 1) {
            //SIACC 150    
            $obExportador = montaArquivoSIACC150($request);
        }else{
            //SICOV 150 - PADRAO 150 FEBRABAN
            //cod_tipo = 2    
            $obExportador = montaArquivoSICOV150($request);
        }
        $obExportador->setRetorno($pgForm);
        ########################UPDATE CONFIGURACAO########################################################
        include_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php");
        $obTAdministracaoConfiguracao = new TAdministracaoConfiguracao();
        $obTAdministracaoConfiguracao->setDado("exercicio",Sessao::getExercicio());
        $obTAdministracaoConfiguracao->setDado("cod_modulo",40);
        $obTAdministracaoConfiguracao->setDado("parametro","dt_num_sequencial_arquivo_caixa".Sessao::getEntidade());
        $obTAdministracaoConfiguracao->recuperaPorChave($rsConfiguracao);
        
        $arCompetencia = explode("-",$rsConfiguracao->getCampo('valor'));
        $dtCompetencia = $arCompetencia[0]."-".$arCompetencia[1];
        
        if ( $dtCompetencia == date("Y-m") ) {
            $obTAdministracaoConfiguracao->setDado("parametro","num_sequencial_arquivo_caixa".Sessao::getEntidade());
            $obTAdministracaoConfiguracao->recuperaPorChave($rsConfiguracao);
            $inSequencial = $rsConfiguracao->getCampo("valor") + 1;
        } else {
            $inSequencial = 1;
            $dtSequencial = date("Y-m-d");
            $obTAdministracaoConfiguracao->setDado("parametro","dt_num_sequencial_arquivo_caixa".Sessao::getEntidade());
            $obTAdministracaoConfiguracao->setDado("valor",$dtSequencial);
            $obTAdministracaoConfiguracao->alteracao();
        }
        $obTAdministracaoConfiguracao->setDado("parametro","num_sequencial_arquivo_caixa".Sessao::getEntidade());
        $obTAdministracaoConfiguracao->setDado("valor",$inSequencial);
        $obTAdministracaoConfiguracao->alteracao();

        $obExportador->show();
        Sessao::encerraExcecao();
    break;
}

function separarDigito($stString)
{
    $inNumero = preg_replace( "/[^0-9a-zA-Z]/i","",$stString);
    $inDigito = $inNumero[strlen($inNumero)-1];
    if(strlen($inNumero) > 4)
        $inNumero = substr($inNumero,0,strlen($inNumero)-1);

    return array($inNumero,$inDigito);
}

function montaArquivoSIACC150(Request $request)
{
        ################################## BANCO CONVENIO #################################

        include_once(CAM_GRH_IMA_MAPEAMENTO."TIMAConfiguracaoConvenioCaixaEconomicaFederal.class.php");
        $obTIMAConfiguracaoConvenioCaixaEconomicaFederal = new TIMAConfiguracaoConvenioCaixaEconomicaFederal();
        $obTIMAConfiguracaoConvenioCaixaEconomicaFederal->recuperaRelacionamento($rsConfiguracaoConvenio);

        $inCodBanco = $rsConfiguracaoConvenio->getCampo("cod_banco");

        $arAgenciaConvenio = separarDigito($rsConfiguracaoConvenio->getCampo("num_agencia"));
        $inDigitoVerificadorAgencia       = $arAgenciaConvenio[1];
        $inAgenciaConvenio                = $arAgenciaConvenio[0];
        $arContaConvenio                  = separarDigito($rsConfiguracaoConvenio->getCampo("num_conta_corrente"));
        $inDigitoVerificadorContaConvenio = $arContaConvenio[1];
        $inCodigoOperacaoContaConvenio    = substr($arContaConvenio[0],0,3);
        $inNumeroContaConvenio            = substr($arContaConvenio[0],3);

        ################################## BANCO CONVENIO #################################

        include_once(CAM_GT_MON_MAPEAMENTO."TMONBanco.class.php");
        $obTMONBanco = new TMONBanco();
        $obTMONBanco->setDado("cod_banco", $inCodBanco);
        $obTMONBanco->recuperaPorChave($rsBanco);

        $stNomBanco = $rsBanco->getCampo('nom_banco');

        ################################## COMPETENCIA ###################################

        ///////// COMPETENCIA SELECIONADA
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
        $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
        $stCompetencia  = (  $request->get('inCodMes') < 10 ) ? "0".$request->get('inCodMes') : $request->get('inCodMes');
        $stCompetencia .= $request->get('inAno');
        $stFiltroCompetencia = " WHERE to_char(dt_final,'mmyyyy') = '".$stCompetencia."'";
        $obTFolhaPagamentoPeriodoMovimentacao->recuperaTodos($rsPeriodoMovimentacao,$stFiltroCompetencia);

        $arInicialCompetenciaSelecionada = explode('/',$rsPeriodoMovimentacao->getCampo('dt_inicial'));
        $arFinalCompetenciaSelecionada = explode('/',$rsPeriodoMovimentacao->getCampo('dt_final'));

        $dtInicialCompetenciaSelecionada = $arInicialCompetenciaSelecionada[2].'-'.$arInicialCompetenciaSelecionada[1].'-'.$arInicialCompetenciaSelecionada[0];
        $dtFinalCompetenciaSelecionada = $arFinalCompetenciaSelecionada[2].'-'.$arFinalCompetenciaSelecionada[1].'-'.$arFinalCompetenciaSelecionada[0];

        ///////// COMPETENCIA ANTERIOR SELECIONADA
        $inCodMovimentacaoAnteriorSelecionada = $rsPeriodoMovimentacao->getCampo('cod_periodo_movimentacao') - 1;
        $obTFolhaPagamentoPeriodoMovimentacao->recuperaPeriodoMovimentacao($rsPenultimaMovimentacao, " AND FPM.cod_periodo_movimentacao = ".$inCodMovimentacaoAnteriorSelecionada);

        if ($rsPenultimaMovimentacao->getCampo('dt_inicial') != '') {
            $arInicialCompetenciaAnteriorSelecionada = explode('/',$rsPenultimaMovimentacao->getCampo('dt_inicial'));
            $arFinalCompetenciaAnteriorSelecionada   = explode('/',$rsPenultimaMovimentacao->getCampo('dt_final'));
        } else {
            $arInicialCompetenciaAnteriorSelecionada = $arInicialCompetenciaSelecionada;
            $arFinalCompetenciaAnteriorSelecionada   = $arFinalCompetenciaSelecionada;
        }

        $dtInicialCompetenciaAnteriorSelecionada = $arInicialCompetenciaAnteriorSelecionada[2].'-'.$arInicialCompetenciaAnteriorSelecionada[1]."-".$arInicialCompetenciaAnteriorSelecionada[0];
        $dtFinalCompetenciaAnteriorSelecionada   = $arFinalCompetenciaAnteriorSelecionada[2].'-'.$arFinalCompetenciaAnteriorSelecionada[1]."-".$arFinalCompetenciaAnteriorSelecionada[0];

        ///////// COMPETENCIA ATUAL
        $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsUltimaMovimentacao);

        $arInicialCompetenciaAtual = explode('/',$rsUltimaMovimentacao->getCampo('dt_inicial'));
        $arFinalCompetenciaAtual   = explode('/',$rsUltimaMovimentacao->getCampo('dt_final'));

        $dtInicialCompetenciaAtual = $arInicialCompetenciaAtual[2].'-'.$arInicialCompetenciaAtual[1].'-'.$arInicialCompetenciaAtual[0];
        $dtFinalCompetenciaAtual   = $arFinalCompetenciaAtual[2].'-'.$arFinalCompetenciaAtual[1].'-'.$arFinalCompetenciaAtual[0];
        
        ################################## EXPORTADOR ###################################

        $obExportador = new Exportador();

        if( ($request->get('stSituacao') == 'aposentados') || ($request->get('stSituacao') == 'pensionistas') ){
            $obExportador->addArquivo("ACC".$arFinalCompetenciaSelecionada[1]."01.txt");
        }else{
            $obExportador->addArquivo("ACC".$arFinalCompetenciaAtual[1]."01.txt");
        }

        $obExportador->roUltimoArquivo->setTipoDocumento('RemessaCaixaEconomicaFederal');

        #############################################################################

        if ( ($request->get('nuValorLiquidoInicial') != '') && ($request->get('nuValorLiquidoFinal') != '') ) {
            $nuValorLiquidoFinal = str_replace(".","",$request->get('nuValorLiquidoFinal'));
            $nuValorLiquidoFinal = str_replace(",",".",$nuValorLiquidoFinal);

            $nuValorLiquidoInicial = str_replace(".","",$request->get('nuValorLiquidoInicial'));
            $nuValorLiquidoInicial = str_replace(",",".",$nuValorLiquidoInicial);
        }

        if ($request->get('nuPercentualPagar') != "") {
            $nuPercentualPagar = str_replace(".", "",  $request->get('nuPercentualPagar'));
            $nuPercentualPagar = str_replace(",", ".", $nuPercentualPagar);
        } else {
            $nuPercentualPagar = 0;
        }

        ################################## ATIVOS/APOSENTADOS/PENSIONISTA ###################################
        $stFiltroContrato = "";

        if ($request->get('stSituacao') == 'ativos' ||
            $request->get('stSituacao') == 'aposentados' ||
            $request->get('stSituacao') == 'rescindidos' ||
            $request->get('stSituacao') == 'pensionistas' ||
            $request->get('stSituacao') == 'todos') {

            $stValoresFiltro = "";
            switch ($request->get('stTipoFiltro')) {
                case 'contrato':
                case 'contrato_rescisao':
                case 'contrato_aposentado':
                case 'contrato_todos':
                case 'cgm_contrato':
                case 'cgm_contrato_rescisao':
                case 'cgm_contrato_aposentado':
                case 'cgm_contrato_todos':
                    $arContratos = Sessao::read('arContratos');
                    foreach ($arContratos as $arContrato) {
                        $stValoresFiltro .= $arContrato['cod_contrato'].",";
                    }
                    $stValoresFiltro = substr($stValoresFiltro,0,strlen($stValoresFiltro)-1);
                    break;
                case 'contrato_pensionista':
                case 'cgm_contrato_pensionista':
                    $arPensionistas = Sessao::read('arPensionistas');
                    foreach ($arPensionistas as $arPensionista) {
                        $stValoresFiltro .= $arPensionista['cod_contrato'].",";
                    }
                    $stValoresFiltro = substr($stValoresFiltro,0,strlen($stValoresFiltro)-1);
                    break;
                case 'lotacao':
                    $stValoresFiltro = implode(",",$request->get('inCodLotacaoSelecionados'));
                    break;
                case 'local':
                    $stValoresFiltro = implode(",",$request->get('inCodLocalSelecionados'));
                    break;
                case 'atributo_servidor':
                    $inCodAtributo = $request->get('inCodAtributo');
                    $inCodCadastro = $request->get('inCodCadastro');
                    $stNomeAtributo = "Atributo_".$inCodAtributo."_".$inCodCadastro;
                    if (is_array($request->get($stNomeAtributo."_Selecionados"))) {
                        $inArray = 1;
                        $stValores     = implode(",",$request->get($stNomeAtributo."_Selecionados"));
                    } else {
                        $inArray = 0;
                        $stValores     = $request->get($stNomeAtributo);
                    }
                    $stValoresFiltro = $inArray."#".$inCodAtributo."#".$stValores;
                    break;
                case 'atributo_pensionista':
                    $inCodAtributo = $request->get('inCodAtributo');
                    $inCodCadastro = $request->get('inCodCadastro');
                    $stNomeAtributo = "Atributo_".$inCodAtributo."_".$inCodCadastro;
                    if (is_array($request->get($stNomeAtributo."_Selecionados"))) {
                        $inArray = 1;
                        $stValores = implode(",",$request->get($stNomeAtributo."_Selecionados"));
                    } else {
                        $inArray = 0;
                        $stValores = $request->get($stNomeAtributo);
                    }
                    $stValoresFiltro = $inArray."#".$inCodAtributo."#".$stValores;
                    break;
            }
        }

        ################################## ESTAGIARIOS ###################################
        $stFiltroEstagiario  = "";

        if ($request->get('stSituacao') == 'estagiarios' ||
            $request->get('stSituacao') == 'todos') {
            switch ($request->get('stTipoFiltro')) {
                case 'cgm_codigo_estagio':
                    foreach (Sessao::read('arEstagios') as $arEstagio) {
                        $stCodEstagio .= $arEstagio['inCodigoEstagio'].",";
                    }
                    $stCodEstagio = substr($stCodEstagio,0,strlen($stCodEstagio)-1);
                    $stFiltroEstagiario  .= " AND numero_estagio IN (".$stCodEstagio.")";
                    break;
                case 'lotacao':
                    $stCodOrgao = implode(",",$request->get('inCodLotacaoSelecionados'));
                    $stFiltroEstagiario  .= " AND cod_orgao in (".$stCodOrgao.")";
                    break;
                case 'local':
                    $stCodLocal = implode(",",$_POST['inCodLocalSelecionados']);
                    $stFiltroEstagiario .= " AND cod_local in (".$stCodLocal.")";
                    break;
                case 'atributo_estagiario':
                    $inCodAtributo = $request->get('inCodAtributo');
                    $inCodCadastro = $request->get('inCodCadastro');
                    $stNomeAtributo = "Atributo_".$inCodAtributo."_".$inCodCadastro;
                    if (is_array($request->get($stNomeAtributo."_Selecionados"))) {
                        $inArray = 1;
                        $stValores = implode(",",$request->get($stNomeAtributo."_Selecionados"));
                    } else {
                        $inArray = 0;
                        $stValores = $request->get($stNomeAtributo);
                    }
                    $stValoresFiltro = $inArray."#".$inCodAtributo."#".$stValores;
                    break;
            }
        }

        ################################## PENSAO JUDICIAL ###################################
        $stFiltroPensaoJudicial  = "";

        //Tipo de Cadastro
        if ($request->get('stSituacao') == 'todos' ||
            $request->get('stSituacao') == 'pensao_judicial') {

            switch ($request->get('stTipoFiltro')) {
                case 'cgm_dependente': //IFiltroComponentesDependentes
                        foreach (Sessao::read('arCGMDependentes') as $arCGMDependente) {
                            $stCGMDependente .= "'".addslashes($arCGMDependente['numcgm'])."',";
                        }
                        $stCGMDependente = substr($stCGMDependente,0,strlen($stCGMDependente)-1);

                        $stFiltroPensaoJudicial  .= " AND contrato.numcgm_dependente IN (".$stCGMDependente.")";
                        break;
                case 'cgm_servidor_dependente': //IFiltroComponentesDependentes
                        foreach (Sessao::read('arContratos') as $arContrato) {
                            $stCodContrato .= $arContrato['cod_contrato'].",";
                        }
                        $stCodContrato = substr($stCodContrato,0,strlen($stCodContrato)-1);
                        $stFiltroPensaoJudicial  .= " AND cod_contrato IN (".$stCodContrato.")";
                        break;
                case 'lotacao':
                        $stCodOrgao = implode(",",$request->get('inCodLotacaoSelecionados'));
                        $stFiltroPensaoJudicial  .= " AND cod_orgao in (".$stCodOrgao.")";
                        break;
            }
        }//
        #############################################################################

        $rsContrato = new RecordSet();
        if ($request->get('stSituacao') == 'ativos' ||
            $request->get('stSituacao') == 'aposentados' ||
            $request->get('stSituacao') == 'rescindidos' ||
            $request->get('stSituacao') == 'pensionistas' ||
            $request->get('stSituacao') == 'todos') {

            include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoCalculado.class.php" );
            $obRecuperaEventoCalculado = new TFolhaPagamentoEventoCalculado();

            $inCodConfiguracao = ($request->get('inCodComplementar') == '') ? 0 : $request->get('inCodComplementar');
            $obRecuperaEventoCalculado->setDado('inCodPeriodoMovimentacao', $rsPeriodoMovimentacao->getCampo('cod_periodo_movimentacao'));
            $obRecuperaEventoCalculado->setDado('stSituacao'              , $request->get('stSituacao') );
            $obRecuperaEventoCalculado->setDado('inCodConfiguracao'       , $request->get('inCodConfiguracao') );
            $obRecuperaEventoCalculado->setDado('inCodComplementar'       , $inCodConfiguracao );
            $obRecuperaEventoCalculado->setDado('stTipoFiltro'            , $request->get('stTipoFiltro'));
            $obRecuperaEventoCalculado->setDado('stValoresFiltro'         , $stValoresFiltro);
            $obRecuperaEventoCalculado->setDado('stDesdobramento'         , $request->get('stDesdobramento'));
            $obRecuperaEventoCalculado->setDado('inCodBanco'              , $rsConfiguracaoConvenio->getCampo('cod_banco'));
            $obRecuperaEventoCalculado->setDado('nuLiquidoMinimo'         , $nuValorLiquidoInicial);
            $obRecuperaEventoCalculado->setDado('nuLiquidoMaximo'         , $nuValorLiquidoFinal);
            $obRecuperaEventoCalculado->setDado('nuPercentualPagar'       , $nuPercentualPagar);
            $obRecuperaEventoCalculado->recuperaContratosCalculadosRemessaBancos($rsContrato,$stFiltroContrato);
        }


        $rsEstagio = new RecordSet();
        if ($request->get('stSituacao') == 'estagiarios' ||
            $request->get('stSituacao') == 'todos') {
            include_once(CAM_GRH_EST_MAPEAMENTO."TEstagioEstagiarioEstagio.class.php");
            $obTEstagioEstagiorioEstagio = new TEstagioEstagiarioEstagio();
            $obTEstagioEstagiorioEstagio->setDado('inCodPeriodoMovimentacao', $rsUltimaMovimentacao->getCampo('cod_periodo_movimentacao'));
            $obTEstagioEstagiorioEstagio->setDado('inCodBanco'              , $rsConfiguracaoConvenio->getCampo('cod_banco'));
            $obTEstagioEstagiorioEstagio->setDado('stTipoFiltro'            , $request->get('stTipoFiltro'));
            $obTEstagioEstagiorioEstagio->setDado('stValoresFiltro'         , $stValoresFiltro);
            $obTEstagioEstagiorioEstagio->setDado('nuLiquidoMinimo'         , $nuValorLiquidoInicial);
            $obTEstagioEstagiorioEstagio->setDado('nuLiquidoMaximo'         , $nuValorLiquidoFinal);
            $obTEstagioEstagiorioEstagio->setDado('nuPercentualPagar'       , $nuPercentualPagar);
            $obTEstagioEstagiorioEstagio->recuperaRemessaBancos($rsEstagio,$stFiltroEstagiario);
        }

        $rsPensaoJudicial = new RecordSet();
        if ($request->get('stSituacao') == 'todos' ||
            $request->get('stSituacao') == 'pensao_judicial') {

            include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoCalculadoDependente.class.php" );
            $obTFolhaPagamentoEventoCalculadoDependente = new TFolhaPagamentoEventoCalculadoDependente();
            $obTFolhaPagamentoEventoCalculadoDependente->setDado('inCodPeriodoMovimentacao', $rsPeriodoMovimentacao->getCampo('cod_periodo_movimentacao'));
            $obTFolhaPagamentoEventoCalculadoDependente->setDado('inCodConfiguracao'       , $request->get('inCodConfiguracao'));
            $obTFolhaPagamentoEventoCalculadoDependente->setDado('inCodComplementar'       , ($request->get('inCodComplementar') == '')?0:$request->get('inCodComplementar'));
            $obTFolhaPagamentoEventoCalculadoDependente->setDado('stDesdobramento'         , $_POST["stDesdobramento"]);
            $obTFolhaPagamentoEventoCalculadoDependente->setDado('inCodBanco'              , $rsConfiguracaoConvenio->getCampo("cod_banco"));
            $obTFolhaPagamentoEventoCalculadoDependente->setDado('nuLiquidoMinimo'         , $nuValorLiquidoInicial);
            $obTFolhaPagamentoEventoCalculadoDependente->setDado('nuLiquidoMaximo'         , $nuValorLiquidoFinal);
            $obTFolhaPagamentoEventoCalculadoDependente->setDado('nuPercentualPagar'       , $nuPercentualPagar);
            $obTFolhaPagamentoEventoCalculadoDependente->recuperaContratosCalculadosRemessaBancos($rsPensaoJudicial,$stFiltroPensaoJudicial);
        }

        ##################################Começa a montar o arquivo###########################################
        $arExportador = array();
        $inIndex = 0;
        $nuVlrLancamentoTotal = 0;

        while (!$rsContrato->eof()) {
            $arExportador[$inIndex]['codigo_registro']                         = "E";
            $arExportador[$inIndex]['identificacao_cliente_empresa']           = $rsContrato->getCampo("registro");//matricula servidor

            $arAgencia = separarDigito($rsContrato->getCampo("num_agencia"));
            $arExportador[$inIndex]['agencia_debito_credito']                  = $arAgencia[0];

            $arConta = separarDigito($rsContrato->getCampo("nr_conta"));
            $inCodOperacao = substr($arConta[0],0,3);
            $inNumeroConta = substr($arConta[0],3);
            $arExportador[$inIndex]['identificacao_cliente_codigo_operacao']   = $inCodOperacao;
            $arExportador[$inIndex]['identificacao_cliente_numero_conta']      = $inNumeroConta;
            $arExportador[$inIndex]['identificacao_cliente_dv_conta']          = $arConta[1];

            $arDataVencimento = explode("/",$request->get('dtPagamento'));
            $arExportador[$inIndex]['data_vencimento']                         = $arDataVencimento[2].$arDataVencimento[1].$arDataVencimento[0];

            $nuVlrLancamento = number_format($rsContrato->getCampo("liquido"), 2, ".", "");
            $nuVlrLancamentoTotal += $nuVlrLancamento;
            $nuVlrLancamento = number_format($nuVlrLancamento, 2, "", "");

            $arExportador[$inIndex]['valor_debito_credito']             = $nuVlrLancamento;
            $arExportador[$inIndex]['codigo_moeda']                     = "03";
            $arExportador[$inIndex]['uso_empresa']                      = $rsContrato->getCampo("nom_cgm");//nome servidor
            $arExportador[$inIndex]['numero_agendamento_cliente']       = $inIndex+1;
            $arExportador[$inIndex]['reservado_futuro']                 = "";
            $arExportador[$inIndex]['numero_sequencial_registro']       = $inIndex+1;
            $arExportador[$inIndex]['codigo_movimento']                 = $request->get('inTipoMovimento');

            $inIndex++;
            $rsContrato->proximo();
        }

        while (!$rsEstagio->eof()) {
            $arExportador[$inIndex]['codigo_registro']                         = "E";
            $arExportador[$inIndex]['identificacao_cliente_empresa']           = $rsEstagio->getCampo("numero_estagio");//codigo do estagio

            $arAgencia = separarDigito($rsEstagio->getCampo("num_agencia"));
            $arExportador[$inIndex]['agencia_debito_credito']                  = $arAgencia[0];

            $arConta = separarDigito($rsEstagio->getCampo("num_conta"));
            $inCodOperacao = substr($arConta[0],0,3);
            $inNumeroConta = substr($arConta[0],3);
            $arExportador[$inIndex]['identificacao_cliente_codigo_operacao']   = $inCodOperacao;
            $arExportador[$inIndex]['identificacao_cliente_numero_conta']      = $inNumeroConta;
            $arExportador[$inIndex]['identificacao_cliente_dv_conta']          = $arConta[1];

            $nuVlrLancamento = number_format($rsEstagio->getCampo("liquido"), 2, ".", "");
            $nuVlrLancamentoTotal += $nuVlrLancamento;
            $nuVlrLancamento = number_format($nuVlrLancamento, 2, "", "");

            $arExportador[$inIndex]['valor_debito_credito']                    = $nuVlrLancamento;
            $arExportador[$inIndex]['codigo_moeda']                            = "03";
            $arExportador[$inIndex]['uso_empresa']                             = $rsEstagio->getCampo("nom_cgm");//nome servidor
            $arExportador[$inIndex]['numero_agendamento_cliente']              = $inIndex+1;
            $arExportador[$inIndex]['reservado_futuro']                        = "";
            $arExportador[$inIndex]['numero_sequencial_registro']              = $inIndex+1;
            $arExportador[$inIndex]['codigo_movimento']                        = $request->get('inTipoMovimento');

            $inIndex++;
            $rsEstagio->proximo();
        }

        while (!$rsPensaoJudicial->eof()) {
            $arExportador[$inIndex]['codigo_registro']                         = "E";
            $arExportador[$inIndex]['identificacao_cliente_empresa']           = $rsPensaoJudicial->getCampo("registro");//matricula servidor

            $arAgencia = separarDigito($rsPensaoJudicial->getCampo("num_agencia"));
            $arExportador[$inIndex]['agencia_debito_credito']                  = $arAgencia[0];

            $arConta = separarDigito($rsPensaoJudicial->getCampo("nr_conta"));
            $inCodOperacao = substr($arConta[0],0,3);
            $inNumeroConta = substr($arConta[0],3);
            $arExportador[$inIndex]['identificacao_cliente_codigo_operacao']   = $inCodOperacao;
            $arExportador[$inIndex]['identificacao_cliente_numero_conta']      = $inNumeroConta;
            $arExportador[$inIndex]['identificacao_cliente_dv_conta']          = $arConta[1];

            $arDataVencimento = explode("/",$_POST['dtPagamento']);
            $arExportador[$inIndex]['data_vencimento']                         = $arDataVencimento[2].$arDataVencimento[1].$arDataVencimento[0];

            $nuVlrLancamento = number_format($rsPensaoJudicial->getCampo("liquido"), 2, ".", "");
            $nuVlrLancamentoTotal += $nuVlrLancamento;
            $nuVlrLancamento = number_format($nuVlrLancamento, 2, "", "");

            $arExportador[$inIndex]['valor_debito_credito']                    = $nuVlrLancamento;
            $arExportador[$inIndex]['codigo_moeda']                            = "03";
            $arExportador[$inIndex]['uso_empresa']                             = $rsPensaoJudicial->getCampo("nom_cgm");//nome servidor
            $arExportador[$inIndex]['numero_agendamento_cliente']              = $inIndex+1;
            $arExportador[$inIndex]['reservado_futuro']                        = "";
            $arExportador[$inIndex]['numero_sequencial_registro']              = $inIndex+1;
            $arExportador[$inIndex]['codigo_movimento']                        = $request->get('inTipoMovimento');

            $inIndex++;
            $rsPensaoJudicial->proximo();
        }

        ##################################### REGISTRO A ####################################

        $arCabecalhoArquivo = array();
        $arCabecalhoArquivo[0]['codigo_registro']       = "A";
        $arCabecalhoArquivo[0]['codigo_remessa']        = 1;
        $arCabecalhoArquivo[0]['codigo_convenio']       = $rsConfiguracaoConvenio->getCampo('cod_convenio_banco');
        $arCabecalhoArquivo[0]['nome_empresa']          = SistemaLegado::pegaConfiguracao('nom_prefeitura',2,Sessao::getExercicio());
        $arCabecalhoArquivo[0]['codigo_banco']          = 104;
        $arCabecalhoArquivo[0]['nome_banco']            = $stNomBanco;

        $arDataMovimento = explode("/",$request->get('dtGeracaoArquivo'));
        $arCabecalhoArquivo[0]['data_movimento']        = $arDataMovimento[2].$arDataMovimento[1].$arDataMovimento[0];

        $arCabecalhoArquivo[0]['numero_sequencial']     = $request->get('inNumeroSequencial');
        $arCabecalhoArquivo[0]['numero_versao_layout']  = 4;
        $arCabecalhoArquivo[0]['servico']               = "FOLHA PAGAMENTO";
        $arCabecalhoArquivo[0]['conta_compromisso_agencia']         = $inAgenciaConvenio;
        $arCabecalhoArquivo[0]['conta_compromisso_codigo_operacao'] = $inCodigoOperacaoContaConvenio;
        $arCabecalhoArquivo[0]['conta_compromisso_numero_conta']    = $inNumeroContaConvenio;
        $arCabecalhoArquivo[0]['conta_compromisso_dv_conta']        = $inDigitoVerificadorContaConvenio;
        $arCabecalhoArquivo[0]['identificacao_ambiente_cliente']    = "P";
        $arCabecalhoArquivo[0]['identificacao_ambiente_caixa']      = "P";
        $arCabecalhoArquivo[0]['reservado_futuro']                  = "";
        $arCabecalhoArquivo[0]['numero_sequencial_registro']        = 0;

        $rsCabecalhoArquivo = new RecordSet();
        $rsCabecalhoArquivo->preenche($arCabecalhoArquivo);
        $obExportador->roUltimoArquivo->addBloco($rsCabecalhoArquivo);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codigo_registro");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codigo_remessa");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codigo_convenio");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(20);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nome_empresa");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(20);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codigo_banco");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nome_banco");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(20);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("data_movimento");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_sequencial");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_versao_layout");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("servico");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(17);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("conta_compromisso_agencia");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("conta_compromisso_codigo_operacao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("conta_compromisso_numero_conta");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("conta_compromisso_dv_conta");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("identificacao_ambiente_cliente");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("identificacao_ambiente_caixa");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("reservado_futuro");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(27);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_sequencial_registro");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("reservado_futuro");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

        #######################################    REGISTRO E   ######################################

        $rsExportador = new RecordSet();
        $rsExportador->preenche($arExportador);
        Sessao::write('inQuantRegistros', ($rsExportador->getNumLinhas() == -1) ? 0 : $rsExportador->getNumLinhas());
        Sessao::write('nuLiquidoTotal', number_format($nuVlrLancamentoTotal,2,",","."));

        $obExportador->roUltimoArquivo->addBloco($rsExportador);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codigo_registro");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("identificacao_cliente_empresa");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(25);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("agencia_debito_credito");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("identificacao_cliente_codigo_operacao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("identificacao_cliente_numero_conta");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("identificacao_cliente_dv_conta");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("reservado_futuro");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("data_vencimento");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("valor_debito_credito");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(15);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codigo_moeda");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("uso_empresa");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(60);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_agendamento_cliente");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("reservado_futuro");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_sequencial_registro");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codigo_movimento");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

        ##########################################RODAPÉ ARQUIVO#########################################

        $arRodapeArquivo[0]['codigo_registro']      = "Z";
        $arRodapeArquivo[0]["quant_registros"]      = $inIndex+2;
        $arRodapeArquivo[0]["soma_registros"]       = number_format($nuVlrLancamentoTotal,2,'','');//garantido a formatacao dos zeros
        $arRodapeArquivo[0]["numero_sequencial_registro"] = $inIndex+1;
        $arRodapeArquivo[0]["reservado_futuro"]     = "";
        $arRodapeArquivo[0]["reservado_futuro_num"] = 0;

        $rsRodapeArquivo = new RecordSet();
        $rsRodapeArquivo->preenche($arRodapeArquivo);
        $obExportador->roUltimoArquivo->addBloco($rsRodapeArquivo);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codigo_registro");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("quant_registros");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("soma_registros");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(17);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("reservado_futuro");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(119);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_sequencial_registro");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("reservado_futuro");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

    return $obExportador;
}

function montaArquivoSICOV150(Request $request)
{
        ################################## BANCO CONVENIO #################################

        include_once(CAM_GRH_IMA_MAPEAMENTO."TIMAConfiguracaoConvenioCaixaEconomicaFederal.class.php");
        $obTIMAConfiguracaoConvenioCaixaEconomicaFederal = new TIMAConfiguracaoConvenioCaixaEconomicaFederal();
        $obTIMAConfiguracaoConvenioCaixaEconomicaFederal->recuperaRelacionamento($rsConfiguracaoConvenio);

        $inCodBanco = $rsConfiguracaoConvenio->getCampo("cod_banco");

        $arAgenciaConvenio = separarDigito($rsConfiguracaoConvenio->getCampo("num_agencia"));
        $inDigitoVerificadorAgencia       = $arAgenciaConvenio[1];
        $inAgenciaConvenio                = $arAgenciaConvenio[0];
        $arContaConvenio                  = separarDigito($rsConfiguracaoConvenio->getCampo("num_conta_corrente"));
        $inDigitoVerificadorContaConvenio = $arContaConvenio[1];
        $inCodigoOperacaoContaConvenio    = substr($arContaConvenio[0],0,3);
        $inNumeroContaConvenio            = substr($arContaConvenio[0],3);

        ################################## BANCO CONVENIO #################################

        include_once(CAM_GT_MON_MAPEAMENTO."TMONBanco.class.php");
        $obTMONBanco = new TMONBanco();
        $obTMONBanco->setDado("cod_banco", $inCodBanco);
        $obTMONBanco->recuperaPorChave($rsBanco);

        $stNomBanco = $rsBanco->getCampo('nom_banco');

        ################################## COMPETENCIA ###################################

        ///////// COMPETENCIA SELECIONADA
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
        $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
        $stCompetencia  = (  $request->get('inCodMes') < 10 ) ? "0".$request->get('inCodMes') : $request->get('inCodMes');
        $stCompetencia .= $request->get('inAno');
        $stFiltroCompetencia = " WHERE to_char(dt_final,'mmyyyy') = '".$stCompetencia."'";
        $obTFolhaPagamentoPeriodoMovimentacao->recuperaTodos($rsPeriodoMovimentacao,$stFiltroCompetencia);

        $arInicialCompetenciaSelecionada = explode('/',$rsPeriodoMovimentacao->getCampo('dt_inicial'));
        $arFinalCompetenciaSelecionada = explode('/',$rsPeriodoMovimentacao->getCampo('dt_final'));

        $dtInicialCompetenciaSelecionada = $arInicialCompetenciaSelecionada[2].'-'.$arInicialCompetenciaSelecionada[1].'-'.$arInicialCompetenciaSelecionada[0];
        $dtFinalCompetenciaSelecionada = $arFinalCompetenciaSelecionada[2].'-'.$arFinalCompetenciaSelecionada[1].'-'.$arFinalCompetenciaSelecionada[0];

        ///////// COMPETENCIA ANTERIOR SELECIONADA
        $inCodMovimentacaoAnteriorSelecionada = $rsPeriodoMovimentacao->getCampo('cod_periodo_movimentacao') - 1;
        $obTFolhaPagamentoPeriodoMovimentacao->recuperaPeriodoMovimentacao($rsPenultimaMovimentacao, " AND FPM.cod_periodo_movimentacao = ".$inCodMovimentacaoAnteriorSelecionada);

        if ($rsPenultimaMovimentacao->getCampo('dt_inicial') != '') {
            $arInicialCompetenciaAnteriorSelecionada = explode('/',$rsPenultimaMovimentacao->getCampo('dt_inicial'));
            $arFinalCompetenciaAnteriorSelecionada   = explode('/',$rsPenultimaMovimentacao->getCampo('dt_final'));
        } else {
            $arInicialCompetenciaAnteriorSelecionada = $arInicialCompetenciaSelecionada;
            $arFinalCompetenciaAnteriorSelecionada   = $arFinalCompetenciaSelecionada;
        }

        $dtInicialCompetenciaAnteriorSelecionada = $arInicialCompetenciaAnteriorSelecionada[2].'-'.$arInicialCompetenciaAnteriorSelecionada[1]."-".$arInicialCompetenciaAnteriorSelecionada[0];
        $dtFinalCompetenciaAnteriorSelecionada   = $arFinalCompetenciaAnteriorSelecionada[2].'-'.$arFinalCompetenciaAnteriorSelecionada[1]."-".$arFinalCompetenciaAnteriorSelecionada[0];

        ///////// COMPETENCIA ATUAL
        $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsUltimaMovimentacao);

        $arInicialCompetenciaAtual = explode('/',$rsUltimaMovimentacao->getCampo('dt_inicial'));
        $arFinalCompetenciaAtual   = explode('/',$rsUltimaMovimentacao->getCampo('dt_final'));

        $dtInicialCompetenciaAtual = $arInicialCompetenciaAtual[2].'-'.$arInicialCompetenciaAtual[1].'-'.$arInicialCompetenciaAtual[0];
        $dtFinalCompetenciaAtual   = $arFinalCompetenciaAtual[2].'-'.$arFinalCompetenciaAtual[1].'-'.$arFinalCompetenciaAtual[0];
        
        ################################## EXPORTADOR ###################################

        $obExportador = new Exportador();
        
        if( ($request->get('stSituacao') == 'aposentados') || ($request->get('stSituacao') == 'pensionistas') ){
            $obExportador->addArquivo("ACC".$arFinalCompetenciaSelecionada[1]."01.txt");
        }else{
            $obExportador->addArquivo("ACC".$arFinalCompetenciaAtual[1]."01.txt");
        }

        $obExportador->roUltimoArquivo->setTipoDocumento('RemessaCaixaEconomicaFederal');

        #############################################################################

        if ( ($request->get('nuValorLiquidoInicial') != '') && ($request->get('nuValorLiquidoFinal') != '') ) {
            $nuValorLiquidoFinal = str_replace(".","",$request->get('nuValorLiquidoFinal'));
            $nuValorLiquidoFinal = str_replace(",",".",$nuValorLiquidoFinal);

            $nuValorLiquidoInicial = str_replace(".","",$request->get('nuValorLiquidoInicial'));
            $nuValorLiquidoInicial = str_replace(",",".",$nuValorLiquidoInicial);
        }

        if ($request->get('nuPercentualPagar') != "") {
            $nuPercentualPagar = str_replace(".", "",  $request->get('nuPercentualPagar'));
            $nuPercentualPagar = str_replace(",", ".", $nuPercentualPagar);
        } else {
            $nuPercentualPagar = 0;
        }

        ################################## ATIVOS/APOSENTADOS/PENSIONISTA ###################################
        $stFiltroContrato = "";

        if ($request->get('stSituacao') == 'ativos' ||
            $request->get('stSituacao') == 'aposentados' ||
            $request->get('stSituacao') == 'rescindidos' ||
            $request->get('stSituacao') == 'pensionistas' ||
            $request->get('stSituacao') == 'todos') {

            $stValoresFiltro = "";
            switch ($request->get('stTipoFiltro')) {
                case 'contrato':
                case 'contrato_rescisao':
                case 'contrato_aposentado':
                case 'contrato_todos':
                case 'cgm_contrato':
                case 'cgm_contrato_rescisao':
                case 'cgm_contrato_aposentado':
                case 'cgm_contrato_todos':
                    $arContratos = Sessao::read('arContratos');
                    foreach ($arContratos as $arContrato) {
                        $stValoresFiltro .= $arContrato['cod_contrato'].",";
                    }
                    $stValoresFiltro = substr($stValoresFiltro,0,strlen($stValoresFiltro)-1);
                    break;
                case 'contrato_pensionista':
                case 'cgm_contrato_pensionista':
                    $arPensionistas = Sessao::read('arPensionistas');
                    foreach ($arPensionistas as $arPensionista) {
                        $stValoresFiltro .= $arPensionista['cod_contrato'].",";
                    }
                    $stValoresFiltro = substr($stValoresFiltro,0,strlen($stValoresFiltro)-1);
                    break;
                case 'lotacao':
                    $stValoresFiltro = implode(",",$request->get('inCodLotacaoSelecionados'));
                    break;
                case 'local':
                    $stValoresFiltro = implode(",",$request->get('inCodLocalSelecionados'));
                    break;
                case 'atributo_servidor':
                    $inCodAtributo = $request->get('inCodAtributo');
                    $inCodCadastro = $request->get('inCodCadastro');
                    $stNomeAtributo = "Atributo_".$inCodAtributo."_".$inCodCadastro;
                    if (is_array($request->get($stNomeAtributo."_Selecionados"))) {
                        $inArray = 1;
                        $stValores     = implode(",",$request->get($stNomeAtributo."_Selecionados"));
                    } else {
                        $inArray = 0;
                        $stValores     = $request->get($stNomeAtributo);
                    }
                    $stValoresFiltro = $inArray."#".$inCodAtributo."#".$stValores;
                    break;
                case 'atributo_pensionista':
                    $inCodAtributo = $request->get('inCodAtributo');
                    $inCodCadastro = $request->get('inCodCadastro');
                    $stNomeAtributo = "Atributo_".$inCodAtributo."_".$inCodCadastro;
                    if (is_array($request->get($stNomeAtributo."_Selecionados"))) {
                        $inArray = 1;
                        $stValores = implode(",",$request->get($stNomeAtributo."_Selecionados"));
                    } else {
                        $inArray = 0;
                        $stValores = $request->get($stNomeAtributo);
                    }
                    $stValoresFiltro = $inArray."#".$inCodAtributo."#".$stValores;
                    break;
            }
        }

        ################################## ESTAGIARIOS ###################################
        $stFiltroEstagiario  = "";

        if ($request->get('stSituacao') == 'estagiarios' ||
            $request->get('stSituacao') == 'todos') {
            switch ($request->get('stTipoFiltro')) {
                case 'cgm_codigo_estagio':
                    foreach (Sessao::read('arEstagios') as $arEstagio) {
                        $stCodEstagio .= $arEstagio['inCodigoEstagio'].",";
                    }
                    $stCodEstagio = substr($stCodEstagio,0,strlen($stCodEstagio)-1);
                    $stFiltroEstagiario  .= " AND numero_estagio IN (".$stCodEstagio.")";
                    break;
                case 'lotacao':
                    $stCodOrgao = implode(",",$request->get('inCodLotacaoSelecionados'));
                    $stFiltroEstagiario  .= " AND cod_orgao in (".$stCodOrgao.")";
                    break;
                case 'local':
                    $stCodLocal = implode(",",$_POST['inCodLocalSelecionados']);
                    $stFiltroEstagiario .= " AND cod_local in (".$stCodLocal.")";
                    break;
                case 'atributo_estagiario':
                    $inCodAtributo = $request->get('inCodAtributo');
                    $inCodCadastro = $request->get('inCodCadastro');
                    $stNomeAtributo = "Atributo_".$inCodAtributo."_".$inCodCadastro;
                    if (is_array($request->get($stNomeAtributo."_Selecionados"))) {
                        $inArray = 1;
                        $stValores = implode(",",$request->get($stNomeAtributo."_Selecionados"));
                    } else {
                        $inArray = 0;
                        $stValores = $request->get($stNomeAtributo);
                    }
                    $stValoresFiltro = $inArray."#".$inCodAtributo."#".$stValores;
                    break;
            }
        }

        ################################## PENSAO JUDICIAL ###################################
        $stFiltroPensaoJudicial  = "";

        //Tipo de Cadastro
        if ($request->get('stSituacao') == 'todos' ||
            $request->get('stSituacao') == 'pensao_judicial') {

            switch ($request->get('stTipoFiltro')) {
                case 'cgm_dependente': //IFiltroComponentesDependentes
                        foreach (Sessao::read('arCGMDependentes') as $arCGMDependente) {
                            $stCGMDependente .= "'".addslashes($arCGMDependente['numcgm'])."',";
                        }
                        $stCGMDependente = substr($stCGMDependente,0,strlen($stCGMDependente)-1);

                        $stFiltroPensaoJudicial  .= " AND contrato.numcgm_dependente IN (".$stCGMDependente.")";
                        break;
                case 'cgm_servidor_dependente': //IFiltroComponentesDependentes
                        foreach (Sessao::read('arContratos') as $arContrato) {
                            $stCodContrato .= $arContrato['cod_contrato'].",";
                        }
                        $stCodContrato = substr($stCodContrato,0,strlen($stCodContrato)-1);
                        $stFiltroPensaoJudicial  .= " AND cod_contrato IN (".$stCodContrato.")";
                        break;
                case 'lotacao':
                        $stCodOrgao = implode(",",$request->get('inCodLotacaoSelecionados'));
                        $stFiltroPensaoJudicial  .= " AND cod_orgao in (".$stCodOrgao.")";
                        break;
            }
        }//
        #############################################################################

        $rsContrato = new RecordSet();
        if ($request->get('stSituacao') == 'ativos' ||
            $request->get('stSituacao') == 'aposentados' ||
            $request->get('stSituacao') == 'rescindidos' ||
            $request->get('stSituacao') == 'pensionistas' ||
            $request->get('stSituacao') == 'todos') {

            include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoCalculado.class.php" );
            $obRecuperaEventoCalculado = new TFolhaPagamentoEventoCalculado();

            $inCodConfiguracao = ($request->get('inCodComplementar') == '') ? 0 : $request->get('inCodComplementar');
            $obRecuperaEventoCalculado->setDado('inCodPeriodoMovimentacao', $rsPeriodoMovimentacao->getCampo('cod_periodo_movimentacao'));
            $obRecuperaEventoCalculado->setDado('stSituacao'              , $request->get('stSituacao') );
            $obRecuperaEventoCalculado->setDado('inCodConfiguracao'       , $request->get('inCodConfiguracao') );
            $obRecuperaEventoCalculado->setDado('inCodComplementar'       , $inCodConfiguracao );
            $obRecuperaEventoCalculado->setDado('stTipoFiltro'            , $request->get('stTipoFiltro'));
            $obRecuperaEventoCalculado->setDado('stValoresFiltro'         , $stValoresFiltro);
            $obRecuperaEventoCalculado->setDado('stDesdobramento'         , $request->get('stDesdobramento'));
            $obRecuperaEventoCalculado->setDado('inCodBanco'              , $rsConfiguracaoConvenio->getCampo('cod_banco'));
            $obRecuperaEventoCalculado->setDado('nuLiquidoMinimo'         , $nuValorLiquidoInicial);
            $obRecuperaEventoCalculado->setDado('nuLiquidoMaximo'         , $nuValorLiquidoFinal);
            $obRecuperaEventoCalculado->setDado('nuPercentualPagar'       , $nuPercentualPagar);
            $obRecuperaEventoCalculado->recuperaContratosCalculadosRemessaBancos($rsContrato,$stFiltroContrato);
        }


        $rsEstagio = new RecordSet();
        if ($request->get('stSituacao') == 'estagiarios' ||
            $request->get('stSituacao') == 'todos') {
            include_once(CAM_GRH_EST_MAPEAMENTO."TEstagioEstagiarioEstagio.class.php");
            $obTEstagioEstagiorioEstagio = new TEstagioEstagiarioEstagio();
            $obTEstagioEstagiorioEstagio->setDado('inCodPeriodoMovimentacao', $rsUltimaMovimentacao->getCampo('cod_periodo_movimentacao'));
            $obTEstagioEstagiorioEstagio->setDado('inCodBanco'              , $rsConfiguracaoConvenio->getCampo('cod_banco'));
            $obTEstagioEstagiorioEstagio->setDado('stTipoFiltro'            , $request->get('stTipoFiltro'));
            $obTEstagioEstagiorioEstagio->setDado('stValoresFiltro'         , $stValoresFiltro);
            $obTEstagioEstagiorioEstagio->setDado('nuLiquidoMinimo'         , $nuValorLiquidoInicial);
            $obTEstagioEstagiorioEstagio->setDado('nuLiquidoMaximo'         , $nuValorLiquidoFinal);
            $obTEstagioEstagiorioEstagio->setDado('nuPercentualPagar'       , $nuPercentualPagar);
            $obTEstagioEstagiorioEstagio->recuperaRemessaBancos($rsEstagio,$stFiltroEstagiario);
        }

        $rsPensaoJudicial = new RecordSet();
        if ($request->get('stSituacao') == 'todos' ||
            $request->get('stSituacao') == 'pensao_judicial') {

            include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoCalculadoDependente.class.php" );
            $obTFolhaPagamentoEventoCalculadoDependente = new TFolhaPagamentoEventoCalculadoDependente();
            $obTFolhaPagamentoEventoCalculadoDependente->setDado('inCodPeriodoMovimentacao', $rsPeriodoMovimentacao->getCampo('cod_periodo_movimentacao'));
            $obTFolhaPagamentoEventoCalculadoDependente->setDado('inCodConfiguracao'       , $request->get('inCodConfiguracao'));
            $obTFolhaPagamentoEventoCalculadoDependente->setDado('inCodComplementar'       , ($request->get('inCodComplementar') == '')?0:$request->get('inCodComplementar'));
            $obTFolhaPagamentoEventoCalculadoDependente->setDado('stDesdobramento'         , $_POST["stDesdobramento"]);
            $obTFolhaPagamentoEventoCalculadoDependente->setDado('inCodBanco'              , $rsConfiguracaoConvenio->getCampo("cod_banco"));
            $obTFolhaPagamentoEventoCalculadoDependente->setDado('nuLiquidoMinimo'         , $nuValorLiquidoInicial);
            $obTFolhaPagamentoEventoCalculadoDependente->setDado('nuLiquidoMaximo'         , $nuValorLiquidoFinal);
            $obTFolhaPagamentoEventoCalculadoDependente->setDado('nuPercentualPagar'       , $nuPercentualPagar);
            $obTFolhaPagamentoEventoCalculadoDependente->recuperaContratosCalculadosRemessaBancos($rsPensaoJudicial,$stFiltroPensaoJudicial);
        }

        ##################################Começa a montar o arquivo###########################################
        $arExportador = array();
        $inIndex = 0;
        $nuVlrLancamentoTotal = 0;

        while (!$rsContrato->eof()) {
            #################### REGISTRO E ####################
            $arAgencia = separarDigito($rsContrato->getCampo("num_agencia"));
            $arConta = separarDigito($rsContrato->getCampo("nr_conta"));
            $inCodOperacao = substr($arConta[0],0,3);
            $inNumeroConta = substr($arConta[0],3);
            $arDataVencimento = explode("/",$request->get('dtPagamento'));
            $nuVlrLancamento = number_format($rsContrato->getCampo("liquido"), 2, ".", "");
            $nuVlrLancamentoTotal += $nuVlrLancamento;
            $nuVlrLancamento = number_format($nuVlrLancamento, 2, "", "");
            
            $arExportador[$inIndex]['codigo_registro']                       = "E";
            $arExportador[$inIndex]['identificacao_cliente_empresa']         = $rsContrato->getCampo("registro");//matricula servidor
            $arExportador[$inIndex]['agencia_debito_credito']                = $arAgencia[0];
            $arExportador[$inIndex]['identificacao_cliente_codigo_operacao'] = $inCodOperacao;
            $arExportador[$inIndex]['identificacao_cliente_numero_conta']    = $inNumeroConta;
            $arExportador[$inIndex]['identificacao_cliente_dv_conta']        = $arConta[1];
            $arExportador[$inIndex]['data_vencimento']                       = $arDataVencimento[2].$arDataVencimento[1].$arDataVencimento[0];
            $arExportador[$inIndex]['valor_debito_credito']                  = $nuVlrLancamento;
            $arExportador[$inIndex]['codigo_moeda']                          = "03";
            $arExportador[$inIndex]['uso_empresa']                           = $rsContrato->getCampo("nom_cgm");//nome servidor
            $arExportador[$inIndex]['reservado_futuro']                      = "";
            $arExportador[$inIndex]['codigo_movimento']                      = 2;

            $inIndex++;
            $rsContrato->proximo();
        }

        while (!$rsEstagio->eof()) {
            $arAgencia = separarDigito($rsEstagio->getCampo("num_agencia"));
            $arConta = separarDigito($rsEstagio->getCampo("num_conta"));
            $inCodOperacao = substr($arConta[0],0,3);
            $inNumeroConta = substr($arConta[0],3);
            $nuVlrLancamento = number_format($rsEstagio->getCampo("liquido"), 2, ".", "");
            $nuVlrLancamentoTotal += $nuVlrLancamento;
            $nuVlrLancamento = number_format($nuVlrLancamento, 2, "", "");
            
            $arExportador[$inIndex]['codigo_registro']                         = "E";
            $arExportador[$inIndex]['identificacao_cliente_empresa']           = $rsEstagio->getCampo("numero_estagio");//codigo do estagio
            $arExportador[$inIndex]['agencia_debito_credito']                  = $arAgencia[0];
            $arExportador[$inIndex]['identificacao_cliente_codigo_operacao']   = $inCodOperacao;
            $arExportador[$inIndex]['identificacao_cliente_numero_conta']      = $inNumeroConta;
            $arExportador[$inIndex]['identificacao_cliente_dv_conta']          = $arConta[1];
            $arExportador[$inIndex]['valor_debito_credito']                    = $nuVlrLancamento;
            $arExportador[$inIndex]['codigo_moeda']                            = "03";
            $arExportador[$inIndex]['uso_empresa']                             = $rsEstagio->getCampo("nom_cgm");//nome servidor
            $arExportador[$inIndex]['reservado_futuro']                        = "";
            $arExportador[$inIndex]['codigo_movimento']                        = 2;

            $inIndex++;
            $rsEstagio->proximo();
        }

        while (!$rsPensaoJudicial->eof()) {
            $arAgencia = separarDigito($rsPensaoJudicial->getCampo("num_agencia"));
            $arConta = separarDigito($rsPensaoJudicial->getCampo("nr_conta"));
            $inCodOperacao = substr($arConta[0],0,3);
            $inNumeroConta = substr($arConta[0],3);
            $arDataVencimento = explode("/",$_POST['dtPagamento']);
            $nuVlrLancamento = number_format($rsPensaoJudicial->getCampo("liquido"), 2, ".", "");
            $nuVlrLancamentoTotal += $nuVlrLancamento;
            $nuVlrLancamento = number_format($nuVlrLancamento, 2, "", "");
            
            $arExportador[$inIndex]['codigo_registro']                         = "E";
            $arExportador[$inIndex]['identificacao_cliente_empresa']           = $rsPensaoJudicial->getCampo("registro");//matricula servidor
            $arExportador[$inIndex]['agencia_debito_credito']                  = $arAgencia[0];
            $arExportador[$inIndex]['identificacao_cliente_codigo_operacao']   = $inCodOperacao;
            $arExportador[$inIndex]['identificacao_cliente_numero_conta']      = $inNumeroConta;
            $arExportador[$inIndex]['identificacao_cliente_dv_conta']          = $arConta[1];
            $arExportador[$inIndex]['data_vencimento']                         = $arDataVencimento[2].$arDataVencimento[1].$arDataVencimento[0];
            $arExportador[$inIndex]['valor_debito_credito']                    = $nuVlrLancamento;
            $arExportador[$inIndex]['codigo_moeda']                            = "03";
            $arExportador[$inIndex]['uso_empresa']                             = $rsPensaoJudicial->getCampo("nom_cgm");//nome servidor
            $arExportador[$inIndex]['reservado_futuro']                        = "";
            $arExportador[$inIndex]['codigo_movimento']                        = 2;

            $inIndex++;
            $rsPensaoJudicial->proximo();
        }

        ##################################### REGISTRO A ####################################

        $arCabecalhoArquivo = array();
        $arDataMovimento = explode("/",$request->get('dtGeracaoArquivo'));
        
        $arCabecalhoArquivo[0]['codigo_registro']       = "A";
        $arCabecalhoArquivo[0]['codigo_remessa']        = 1;
        $arCabecalhoArquivo[0]['codigo_convenio']       = $rsConfiguracaoConvenio->getCampo('cod_convenio_banco');
        $arCabecalhoArquivo[0]['nome_empresa']          = SistemaLegado::pegaConfiguracao('nom_prefeitura',2,Sessao::getExercicio());
        $arCabecalhoArquivo[0]['codigo_banco']          = 104;
        $arCabecalhoArquivo[0]['nome_banco']            = $stNomBanco;
        $arCabecalhoArquivo[0]['data_movimento']        = $arDataMovimento[2].$arDataMovimento[1].$arDataMovimento[0];
        $arCabecalhoArquivo[0]['numero_sequencial']     = $request->get('inNumeroSequencial');
        $arCabecalhoArquivo[0]['numero_versao_layout']  = '04';
        $arCabecalhoArquivo[0]['servico']               = "FOLHA PAGAMENTO";
        $arCabecalhoArquivo[0]['reservado_futuro']      = "";

        $rsCabecalhoArquivo = new RecordSet();
        $rsCabecalhoArquivo->preenche($arCabecalhoArquivo);
        $obExportador->roUltimoArquivo->addBloco($rsCabecalhoArquivo);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codigo_registro");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codigo_remessa");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codigo_convenio");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(20);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nome_empresa");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(20);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codigo_banco");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nome_banco");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(20);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("data_movimento");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_sequencial");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_versao_layout");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("servico");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(17);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("reservado_futuro");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(52);

        #######################################    REGISTRO E   ######################################

        $rsExportador = new RecordSet();
        $rsExportador->preenche($arExportador);
        Sessao::write('inQuantRegistros', ($rsExportador->getNumLinhas() == -1) ? 0 : $rsExportador->getNumLinhas());
        Sessao::write('nuLiquidoTotal', number_format($nuVlrLancamentoTotal,2,",","."));

        $obExportador->roUltimoArquivo->addBloco($rsExportador);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codigo_registro");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("identificacao_cliente_empresa");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(25);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("agencia_debito_credito");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("identificacao_cliente_codigo_operacao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("identificacao_cliente_numero_conta");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("identificacao_cliente_dv_conta");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("reservado_futuro");//espaco em branco
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("data_vencimento");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("valor_debito_credito");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(15);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codigo_moeda");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("uso_empresa");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(60);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("reservado_futuro");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(20);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codigo_movimento");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

        ##########################################RODAPÉ ARQUIVO#########################################

        $arRodapeArquivo[0]['codigo_registro']      = "Z";
        $arRodapeArquivo[0]["quant_registros"]      = $inIndex+2;
        $arRodapeArquivo[0]["soma_registros"]       = number_format($nuVlrLancamentoTotal,2,'','');//garantido a formatacao dos zeros
        $arRodapeArquivo[0]["reservado_futuro"]     = "";

        $rsRodapeArquivo = new RecordSet();
        $rsRodapeArquivo->preenche($arRodapeArquivo);
        $obExportador->roUltimoArquivo->addBloco($rsRodapeArquivo);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codigo_registro");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("quant_registros");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("reservado_futuro_num");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(17);
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("soma_registros");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(17);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("reservado_futuro");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(109);

    return $obExportador;
}

?>
