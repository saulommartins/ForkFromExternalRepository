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
    * Página de Processamento do Exportação Remessa Banrisul
    * Data de Criação: 10/06/2008

    * @author Analista: Dagiane	Vieira
    * @author Desenvolvedor: <Alex Cardoso>

    * @ignore

    $Id: $

    * Casos de uso: uc-04.08.26
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalContratoServidor.class.php"                                 );
include_once ( CLA_EXPORTADOR );

$stAcao = $_REQUEST["stAcao"] ? $_REQUEST["stAcao"] : $_GET["stAcao"];
$arSessaoLink = Sessao::read('link');
$stLink = "&pg=".$arSessaoLink["pg"]."&pos=".$arSessaoLink["pos"];

foreach ($_POST as $key=>$value) {
    $stLink .= $key."=".$value."&";
}

define('NUM_BCO', '041');

//Define o nome dos arquivos PHP
$stPrograma = "CreditoBanrisul";
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

switch ($stAcao) {
    case "exportar":
        Sessao::setTrataExcecao(true);

        ################################## CONFIGURACAO #################################

        $rsConfiguracaoRemuneracao = new RecordSet();
        $rsConfiguracaoLiquido     = new RecordSet();
        $rsMonetarioBanco          = new RecordSet();

        include_once(CAM_GT_MON_MAPEAMENTO."TMONBanco.class.php");
        $obTMONBanco = new TMONBanco();
        $obTMONBanco->recuperaTodos($rsMonetarioBanco, " WHERE num_banco = '".NUM_BCO."'");

        include_once(CAM_GRH_IMA_MAPEAMENTO."TIMAConsignacaoBanrisulRemuneracao.class.php");
        $obTIMAConsignacaoBanrisulRemuneracao = new TIMAConsignacaoBanrisulRemuneracao();
        $obTIMAConsignacaoBanrisulRemuneracao->recuperaRelacionamento($rsConfiguracaoRemuneracao);

        include_once(CAM_GRH_IMA_MAPEAMENTO."TIMAConsignacaoBanrisulLiquido.class.php");
        $obTIMAConsignacaoBanrisulLiquido = new TIMAConsignacaoBanrisulLiquido();
        $obTIMAConsignacaoBanrisulLiquido->recuperaRelacionamento($rsConfiguracaoLiquido);

        $arCodEventosRemuneracao = array();
        while (!$rsConfiguracaoRemuneracao->eof()) {
                $arCodEventosRemuneracao[] = $rsConfiguracaoRemuneracao->getCampo('cod_evento');
                $rsConfiguracaoRemuneracao->proximo();
        }

        $arCodEventosDescontos = array();

        while (!$rsConfiguracaoLiquido->eof()) {
                $arCodEventosDescontos[] = $rsConfiguracaoLiquido->getCampo('cod_evento');
                $rsConfiguracaoLiquido->proximo();
        }

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

        ################################## EXPORTADOR ###################################

        $obExportador = new Exportador();
        $obExportador->setRetorno($pgForm);
        $obExportador->addArquivo("consignacao.txt");
        $obExportador->roUltimoArquivo->setTipoDocumento("ConsignacaoBanrisul");

        ################################## ATIVOS/APOSENTADOS/PENSIONISTA ###############
        $stFiltroContrato  = "";

        if ($_POST['stSituacao'] == 'ativos' ||
           $_POST['stSituacao'] == 'aposentados' ||
           $_POST['stSituacao'] == 'rescindidos' ||
           $_POST['stSituacao'] == 'pensionistas' ||
           $_POST['stSituacao'] == 'todos') {

            $stValoresFiltro = "";
            switch ($_POST['stTipoFiltro']) {
                case "contrato":
                case "contrato_todos":
                case "contrato_aposentado":
                case "cgm_contrato":
                case "cgm_contrato_todos":
                case "cgm_contrato_aposentado":
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
            $obRecuperaEventoCalculado->setDado("inCodConfiguracao"       , 1);
            $obRecuperaEventoCalculado->setDado("inCodComplementar"       , 0);
            $obRecuperaEventoCalculado->setDado("stTipoFiltro"            , $_POST["stTipoFiltro"]);
            $obRecuperaEventoCalculado->setDado("stValoresFiltro"         , $stValoresFiltro);
            $obRecuperaEventoCalculado->setDado("inCodBanco"              , $rsMonetarioBanco->getCampo("cod_banco"));
            $obRecuperaEventoCalculado->setDado("arEventosProventos"      , $arCodEventosRemuneracao);
            $obRecuperaEventoCalculado->setDado("arEventosDescontos"      , $arCodEventosDescontos);
            $obRecuperaEventoCalculado->recuperaContratosCalculadosRemessaBancos($rsContrato,$stFiltroContrato);
        }

        #############################################################################

        $arExportador = array();
        $inIndex = 0;

        while (!$rsContrato->eof()) {
            $arExportador[$inIndex]["cpf"]                           = suprimirAlpha($rsContrato->getCampo('cpf'));
            $arExportador[$inIndex]["nome"]                          = strtoupper(removeAcentuacao($rsContrato->getCampo("nom_cgm")));

            $nuVlrProventos = number_format($rsContrato->getCampo("proventos"), 2, ".", "");
            $nuVlrProventos = number_format($nuVlrProventos, 2, "", "");

            $nuVlrProventosLiquido = number_format($rsContrato->getCampo("liquido"), 2, ".", "");
            $nuVlrProventosLiquido = number_format($nuVlrProventosLiquido, 2, "", "");

            $arExportador[$inIndex]["salario"]                       = $nuVlrProventos;
            $arExportador[$inIndex]["salario_liquido"]               = $nuVlrProventosLiquido;
            $arExportador[$inIndex]["matricula"]                     = $rsContrato->getCampo("registro");
            $arExportador[$inIndex]["banco"]                         = NUM_BCO;

            $arAgencia = separarDigito($rsContrato->getCampo("num_agencia"));
            $arExportador[$inIndex]["numero_agencia"]                = $arAgencia[0];

            $arExportador[$inIndex]["nr_conta"]                      = suprimirAlpha($rsContrato->getCampo("nr_conta"));
            $arExportador[$inIndex]["funcao"]                        = strtoupper(removeAcentuacao($rsContrato->getCampo("descricao_funcao")));

            $inIndex++;
            $rsContrato->proximo();
        }

        ##################################### DETALHE ##############################################

        $rsExportador = new RecordSet();
        $rsExportador->preenche($arExportador);
        Sessao::write('inQuantRegistros', ($rsExportador->getNumLinhas() == -1) ? 0 : $rsExportador->getNumLinhas());

        $obExportador->roUltimoArquivo->addBloco($rsExportador);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cpf");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(11);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nome");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(46);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("salario");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(17);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("matricula");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(12);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("banco");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_agencia");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nr_conta");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(10);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("salario_liquido");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(17);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("funcao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(60);

        ############################################################################################

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
