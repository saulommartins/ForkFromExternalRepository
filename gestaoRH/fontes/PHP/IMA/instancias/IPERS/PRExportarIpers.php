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
    * Página de Processamento do Arquivo Ipers
    * Data de Criação: 25/06/2008

    * @author Analista: Dagiane	Vieira
    * @author Desenvolvedor: <Alex Cardoso>

    * @ignore

    $Id: PRExportarIpers.php 62147 2015-03-31 19:14:32Z jean $

    * Casos de uso: uc-04.08.28
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

//Define o nome dos arquivos PHP
$stPrograma = "ExportarIpers";
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

switch ($stAcao) {
    case "exportar":
        Sessao::setTrataExcecao(true);

        ################################## COMPETENCIA ###################################

        ///////// COMPETENCIA SELECIONADA
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
        $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
        $stCompetencia  = (  $_POST["inCodMes"] < 10 ) ? "0".$_POST["inCodMes"] : $_POST["inCodMes"];
        $stCompetencia .= $_POST["inAno"];
        $stFiltroCompetencia = " WHERE to_char(dt_final,'mmyyyy') = '".$stCompetencia."'";
        $obTFolhaPagamentoPeriodoMovimentacao->recuperaTodos($rsPeriodoMovimentacao,$stFiltroCompetencia);

        ################################## CONFIGURACAO #################################

        include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConfiguracaoIpe.class.php" );

        $obTFolhaPagamentoConfiguracaoIpe = new TFolhaPagamentoConfiguracaoIpe();
        $obTFolhaPagamentoConfiguracaoIpe->setDado('cod_periodo_movimentacao', $rsPeriodoMovimentacao->getCampo('cod_periodo_movimentacao'));
        $obTFolhaPagamentoConfiguracaoIpe->recuperaTodosVigencia($rsConfiguracaoIpe, '', ' ORDER BY configuracao_ipe.vigencia DESC, configuracao_ipe.cod_configuracao DESC LIMIT 1 ');

        ################################## EXPORTADOR ###################################

        $obExportador = new Exportador();
        $obExportador->setRetorno($pgForm);
        $obExportador->addArquivo("ipe.txt");
        $obExportador->roUltimoArquivo->setTipoDocumento("IPERS");

        $stValoresFiltro = "";
        switch ($_REQUEST['stTipoFiltro']) {
            case "contrato":
            case "contrato_rescisao":
            case "contrato_todos":
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
            case "funcao":
                $stValoresFiltro = implode(",",$_REQUEST["inCodFuncaoSelecionados"]);
            break;
            case "lotacao":
                $stValoresFiltro = implode(",",$_REQUEST["inCodLotacaoSelecionados"]);
            break;
            case "local":
                $stValoresFiltro = implode(",",$_REQUEST["inCodLocalSelecionados"]);
            break;
            case "reg_sub_fun_esp":
                $stValoresFiltro  = implode(",",$_REQUEST["inCodRegimeSelecionadosFunc"])."#";
                $stValoresFiltro .= implode(",",$_REQUEST["inCodSubDivisaoSelecionadosFunc"])."#";
                $stValoresFiltro .= implode(",",$_REQUEST["inCodFuncaoSelecionados"])."#";
                if (is_array($_REQUEST["inCodEspecialidadeSelecionadosFunc"])) {
                    $stValoresFiltro .= implode(",",$_REQUEST["inCodEspecialidadeSelecionadosFunc"]);
                }
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
        }

        include_once ( CAM_GRH_IMA_MAPEAMENTO."FIMAExportarArquivoIpers.class.php" );
        $obFIMAExportarArquivoIpers = new FIMAExportarArquivoIpers();

        $obFIMAExportarArquivoIpers->setDado('inCodPeriodoMovimentacao', $rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao"));
        $obFIMAExportarArquivoIpers->setDado('inCodFolha'              , $_POST["inCodConfiguracao"] ? $_POST["inCodConfiguracao"] : -1);
        $obFIMAExportarArquivoIpers->setDado('inCodComplementar'       , ($_POST["inCodComplementar"] != "")?$_POST["inCodComplementar"]:0);
        $obFIMAExportarArquivoIpers->setDado('stDesdobramento'         , $_POST["stDesdobramento"]);
        $obFIMAExportarArquivoIpers->setDado('stSituacaoCadastro'      , $_POST['stSituacao']);
        $obFIMAExportarArquivoIpers->setDado('inCodTipoEmissao'        , $_POST['inCodTipoEmissao']);
        $obFIMAExportarArquivoIpers->setDado('stTipoFiltro'            , $_REQUEST['stTipoFiltro']);
        $obFIMAExportarArquivoIpers->setDado('stValoresFiltro'         , $stValoresFiltro);
        $obFIMAExportarArquivoIpers->setDado('boAgruparFolhas'         , $_REQUEST['stJuntarCalculo']=='sim' ? 'true' : 'false');

        $rsContrato = new RecordSet();
        $obFIMAExportarArquivoIpers->exportarArquivoIpers($rsContrato);

        #############################################################################

        Sessao::write("inCodPeriodoMovimentacao", $obFIMAExportarArquivoIpers->getDado("inCodPeriodoMovimentacao") );
        Sessao::write("inCodFolha"              , $obFIMAExportarArquivoIpers->getDado("inCodFolha") );
        Sessao::write("inCodComplementar"       , $obFIMAExportarArquivoIpers->getDado("inCodComplementar") );
        Sessao::write("stDesdobramento"         , $obFIMAExportarArquivoIpers->getDado("stDesdobramento") );
        Sessao::write("stSituacaoCadastro"      , $obFIMAExportarArquivoIpers->getDado("stSituacaoCadastro") );
        Sessao::write("stTipoFiltro"            , $obFIMAExportarArquivoIpers->getDado("stTipoFiltro") );
        Sessao::write("stValoresFiltro"         , $stValoresFiltro);
        Sessao::write("inCodTipoEmissao"        , $_POST['inCodTipoEmissao']);
        Sessao::write("stCompetenciaTitulo"     , substr($stCompetencia,0,2)."/".substr($stCompetencia,2) );
        Sessao::write("stCodigoOrgao"           , str_pad($rsConfiguracaoIpe->getCampo('codigo_orgao'),3,"0",STR_PAD_LEFT) );
        Sessao::write("inValorPerContPatronal"  , $rsConfiguracaoIpe->getCampo('contribuicao_pat') );
        Sessao::write("boAgruparFolhas"         , $obFIMAExportarArquivoIpers->getDado('boAgruparFolhas') );

        #############################################################################

        $arExportador = array();
        $inIndex = 0;
        $nuVlrLancamentoTotal = 0;

        while (!$rsContrato->eof()) {

                switch ($rsContrato->getCampo("sexo")) {
                    case "m"://Solteiro
                          $inCodSexo = 1;
                          break;
                    case "f"://Casado
                          $inCodSexo = 2;
                          break;
                }

            switch ($rsContrato->getCampo("cod_estado_civil")) {
                case 1://Solteiro
                      $inCodEstadoCivil = 1;
                      break;
                case 2://Casado
                      $inCodEstadoCivil = 2;
                      break;
                case 3://Divorciado
                      $inCodEstadoCivil = 5;
                      break;
                case 4://Separado
                      $inCodEstadoCivil = 4;
                      break;
                case 5://Viuvo
                      $inCodEstadoCivil = 3;
                      break;
                default://Outros
                      $inCodEstadoCivil = 6;
                      break;
            }

            $arExportador[$inIndex]["orgao"]                         = $rsConfiguracaoIpe->getCampo('codigo_orgao');
            $arExportador[$inIndex]["registro"]                      = $rsContrato->getCampo("registro");

            $arExportador[$inIndex]["matricula_ipe"]                 = $rsContrato->getCampo("matricula_ipe");
            $arExportador[$inIndex]["situacao"]                      = $rsContrato->getCampo("situacao");
            $arExportador[$inIndex]["nome"]                          = preg_replace('[°]','',removeAcentuacao($rsContrato->getCampo("nom_cgm")));
            $arExportador[$inIndex]["endereco"]                      = preg_replace('[°]','',removeAcentuacao($rsContrato->getCampo("logradouro").(($rsContrato->getCampo("numero")!="")?", ".$rsContrato->getCampo("numero"):"")));
            $arExportador[$inIndex]["cep"]                           = preg_replace( "/[^0-9A-Za-z]/i","",$rsContrato->getCampo("cep"));

            $arExportador[$inIndex]["data_ingresso"]                 = preg_replace( "/[^0-9]/i","",$rsContrato->getCampo("dt_ingresso"));
            $arExportador[$inIndex]["data_situacao"]                 = preg_replace( "/[^0-9]/i","",$rsContrato->getCampo("dt_situacao"));
            $arExportador[$inIndex]["data_nascimento"]               = preg_replace( "/[^0-9]/i","",$rsContrato->getCampo("dt_nascimento"));
            $arExportador[$inIndex]["sexo"]                          = $inCodSexo;
            $arExportador[$inIndex]["estado_civil"]                  = $inCodEstadoCivil;
            $arExportador[$inIndex]["rg"]                            = preg_replace( "/[^0-9]/i","",$rsContrato->getCampo("rg"));
            $arExportador[$inIndex]["cpf"]                           = preg_replace( "/[^0-9]/i","",$rsContrato->getCampo("cpf"));

            $arExportador[$inIndex]["salario"]                       = number_format($rsContrato->getCampo("valor"), 2, "", "");
            $arExportador[$inIndex]["vazio"]                         = "";

            $nuVlrLancamentoTotal += $rsContrato->getCampo("valor");
            $inIndex++;

            $rsContrato->proximo();
        }

        Sessao::write('inQuantRegistros', $inIndex);

        #####################################CABEÇALHO ARQUIVO####################################

        $arCabecalhoArquivo = array();
        $arCabecalhoArquivo[0]['orgao']          = $rsConfiguracaoIpe->getCampo("codigo_orgao");
        $arCabecalhoArquivo[0]['data_movimento'] = substr($stCompetencia, 2).substr($stCompetencia, 0, 2);
        $arCabecalhoArquivo[0]['identificador']  = $_POST['inCodTipoEmissao'];
        $arCabecalhoArquivo[0]['vazio']          = "";

        $rsCabecalhoArquivo = new RecordSet();
        $rsCabecalhoArquivo->preenche($arCabecalhoArquivo);
        $obExportador->roUltimoArquivo->addBloco($rsCabecalhoArquivo);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("orgao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vazio");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("data_movimento");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("identificador");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vazio");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(232);

        ##################################### DETALHE DO LOTE ##############################################

        $rsExportador = new RecordSet();
        $rsExportador->preenche($arExportador);

        $obExportador->roUltimoArquivo->addBloco($rsExportador);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("orgao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("registro");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("matricula_ipe");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("situacao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nome");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(32);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("endereco");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(40);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cep");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("data_ingresso");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("data_situacao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("data_nascimento");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("sexo");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("estado_civil");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("rg");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(10);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cpf");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(11);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("salario");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(11);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vazio");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(86);

        ##########################################RODAPÉ ARQUIVO#########################################

        $arRodape[0]['orgao']           = $rsConfiguracaoIpe->getCampo('codigo_orgao');
        $arRodape[0]['noves']           = 99999999;
        $arRodape[0]["quant_registros"] = $inIndex;
        $arRodape[0]["total_salario"]   = number_format($nuVlrLancamentoTotal, 2, "", "");;
        $arRodape[0]["vazio"]           = "";

        $rsRodape = new RecordSet;
        $rsRodape->preenche($arRodape);

        $obExportador->roUltimoArquivo->addBloco($rsRodape);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("orgao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("noves");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("quant_registros");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(5);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("total_salario");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(17);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vazio");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(217);

        Sessao::encerraExcecao();
        $obExportador->Show();
    break;
}

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

function removeAcentuacao($str)
{
  $from = 'ÀÁÃÂÉÊÍÓÕÔÚÜÇàáãâéêíóõôúüç';
  $to   = 'AAAAEEIOOOUUCaaaaeeiooouuc';

  return strtr($str, $from, $to);
}

?>
