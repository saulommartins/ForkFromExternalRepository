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
  * Página de
  * Data de criação : 09/06/2005

  * @author Analista:
  * @author Programador: Vandré Miguel Ramos

    $Id: PRManterAssentamento.php 66365 2016-08-18 14:39:09Z evandro $

   Caso de uso: uc-04.04.08
**/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalVantagem.class.php"                                       );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalAssentamentoVinculado.class.php"                          );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalAssentamento.class.php"                                   );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalCondicaoAssentamento.class.php"                           );

$arLink = Sessao::read('link');
$stAcao = $request->get('stAcao');
$stLink = "&pg=".$arLink["pg"]."&pos=".$arLink["pos"];

//Define o nome dos arquivos PHP
if ( !isset($_REQUEST['inCodClassificacao']) ) {
    $inCodClassificacao = $_REQUEST['hdnCodClassificacao'];
} else {
    $inCodClassificacao = $_REQUEST['inCodClassificacao'];
}

$stPrograma = "ManterAssentamento";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php?stAcao=$stAcao".$stLink."&inCodClassificacao=".$inCodClassificacao;
$pgForm = "FM".$stPrograma.".php?";
$pgProc = "PR".$stPrograma.".php?";
$pgOcul = "OC".$stPrograma.".php";

$obRPessoalVantagem                 = new RPessoalVantagem;
$obRPessoalAssentamento             = new RPessoalAssentamento( $obRPessoalVantagem );
$obRPessoalAssentamento2            = new RPessoalAssentamento( $obRPessoalVantagem );
$obRPessoalCondicaoAssentamento     = new RPessoalCondicaoAssentamento();
$obRPessoalAssentamentoVinculado    = new RPessoalAssentamentoVinculado($obRPessoalAssentamento,$obRPessoalAssentamento2,$obRPessoalCondicaoAssentamento);

switch ($stAcao) {
    case "incluir":
        foreach ($_POST as $Campo => $value) {
            if (substr($Campo,0,11)=='inCodAbaRes') {
                $validaRescisao = true;
            }
        }
        $obErro = new erro;
        $stNumNorma = ltrim($_POST['inCodNorma'],0);
        if ($stNumNorma == "") {
            $stNumNorma = "0";
        }

        $obRPessoalAssentamento->obRNorma->setNumNorma($stNumNorma);
        $obErro = $obRPessoalAssentamento->obRNorma->listar( $rsNorma );
        $inCodNorma = $rsNorma->getCampo('cod_norma');
        if ( !$obErro->ocorreu() and $inCodNorma != 0 and sistemaLegado::comparaDatas( $_POST['hdndtPublicacao'],$_POST['dtDataInicioAssentamento'] ) ) {
            $obErro->setDescricao("A data inicial deve ser posterior à data de publicação!");
        }
        if ( !$obErro->ocorreu() and $_POST['dtDataFinalAssentamento'] != "" and sistemaLegado::comparaDatas( $_POST['dtDataInicioAssentamento'],$_POST['dtDataFinalAssentamento'] ) ) {
            $obErro->setDescricao("A data final deve ser posterior a data inicial!");
        }
        if ( !$obErro->ocorreu() and $_POST['hdnCodTipo'] == 2 ) {
            if ((($_POST['inCodSefip']) || (count(Sessao::read('Faixas')) > 0)) && ($validaRescisao == true)) {
                $obErro->setDescricao("Você dever preencher apenas campos de uma das abas, ou de afastamento temporário ou afastamento permanente!");
            }
            if ( count(Sessao::read('Faixas')) > 0 and $_POST['inCodSefip'] == "" ) {
                $obErro->setDescricao("Código Sefip não informado!");
            }
        }
        if ( !$obErro->ocorreu() and $_POST['hdnCodTipo'] == 4 ) {
            if ($_POST['dtDataInicio'] == "") {
                $obErro->setDescricao("A data inicial da aba vantagem deve ser inserida!");
            }
            if ( $_POST['dtDataInicio'] != "" and sistemaLegado::comparaDatas( $_POST['hdndtPublicacao'],$_POST['dtDataInicio'] ) ) {
                $obErro->setDescricao("A data inicial deve ser posterior à data de publicação!");
            }
        }
        if ( !$obErro->ocorreu() ) {
            $obRPessoalAssentamento->setSigla                              ( $_POST['stSigla']);
            $obErro = $obRPessoalAssentamento->listarAssentamento( $rsAssentamento );
        }
        if ( !$obErro->ocorreu() and $rsAssentamento->getNumLinhas() > 0 ) {
            $obErro->setDescricao("A sigla informada já está cadastrada, informe outra sigla!");
        }
        if ( $obErro->ocorreu() ) {
            sistemaLegado::exibeAviso($obErro->getDescricao()," "," ");
        } else {
            if ( is_array( $_REQUEST['inCodEventoSelecionados'] ) ) {
                foreach ($_REQUEST['inCodEventoSelecionados'] as $codEvento) {
                    $obRPessoalAssentamento->addEvento();
                    $obRPessoalAssentamento->roUltimoFolhaPagamentoEvento->setCodEvento($codEvento);
                }
            }
            if ( is_array($_REQUEST['inCodEventoProporcionalizacaoSelecionados']) ) {
                foreach ($_REQUEST['inCodEventoProporcionalizacaoSelecionados'] as $inCodEvento) {
                    $obRPessoalAssentamento->addEventoProporcional();
                    $obRPessoalAssentamento->roRFolhaPagamentoEventoProporcional->setCodEvento($inCodEvento);
                }
            }
            if ( is_array($_REQUEST['inCodRegimeSelecionados']) ) {
                foreach ($_REQUEST['inCodRegimeSelecionados'] as $stSubDivisao) {
                    $arSubDivisao = explode("/",$stSubDivisao);
                    $obRPessoalAssentamento->addPessoalSubDivisao();
                    $obRPessoalAssentamento->roUltimoPessoalSubDivisao->setCodSubDivisao($arSubDivisao[0]);
                }
            }
            $obRPessoalAssentamento->obRPessoalClassificacaoAssentamento->setCodTipo                       ( $_POST['hdnCodTipo']);
            $obRPessoalAssentamento->obRPessoalClassificacaoAssentamento->setCodClassificacaoAssentamento  ( $_POST['inCodClassificacao']);
            $obRPessoalAssentamento->obRNorma->setCodNorma                                                 ( $inCodNorma );
            $obRPessoalAssentamento->setDescricao                          ( $_POST['stDescricao']);
            $obRPessoalAssentamento->setSigla                              ( $_POST['stSigla']);
            $obRPessoalAssentamento->setAbreviacao                         ( $_POST['stAbreviacao']);
            $obRPessoalAssentamento->setAssentamentoInicio                 ( $_POST['boAssentamentoInicio']);
            $obRPessoalAssentamento->setGradeEfetividade                   ( $_POST['boGradeEfetividade']);
            $obRPessoalAssentamento->setRelFuncaoGratificada               ( $_POST['boRelFuncaoGratificada']);
            $obRPessoalAssentamento->setDataInicial                        ( $_POST['dtDataInicioAssentamento'] );
            $obRPessoalAssentamento->setDataFinal                          ( $_POST['dtDataFinalAssentamento'] );
            $obRPessoalAssentamento->setEventoAutomatico                   ( $_POST['boEventoAutomatico'] );
            $obRPessoalAssentamento->setAssentamentoAutomatico             ( $_POST['boAssentamentoAutomatico'] );
            $obRPessoalAssentamento->obRPessoalEsferaOrigem->setCodEsfera  ( $_POST['inCodEsfera']);
            $obRPessoalAssentamento->setCodOperador                        ( $_POST['inCodOperadorTxt']);
            $obRPessoalAssentamento->setDiasAfastamento                    ( $_POST['inQuantidadeDias']);
            $obRPessoalAssentamento->obRPessoalVantagem->setDataInicial    ( $_POST['dtDataInicio'] );
            $obRPessoalAssentamento->obRPessoalVantagem->setDataFinal      ( $_POST['dtDataEncerramento'] );            
            $obRPessoalAssentamento->setCancelarDireito                    ( $_POST['boCancelarDireito'] );
            $obRPessoalAssentamento->obRPessoalAssentamentoMotivo->setCodMotivo( $_POST['inCodMotivoTxt'] );
            $obRPessoalAssentamento->obRPessoalAssentamentoMotivo->setQuantDiasOnusEmpregador($_POST["inQuantDiasOnusEmpregador"]);
            $obRPessoalAssentamento->obRPessoalAssentamentoMotivo->setQuantDiasLicencaPremio($_POST["inQuantDiasLicencaPremio"]);
            //dados para afastamento
            $rsSefip = new RecordSet();
            if ($_POST['inCodSefip'] != "") {
                $obRPessoalAssentamento->obRPessoalSefip->setNumSefip          ( $_POST['inCodSefip'] );
                $obRPessoalAssentamento->obRPessoalSefip->listar               ( $rsSefip             );
            }
            $obRPessoalAssentamento->obRPessoalSefip->setCodSefip          ( $rsSefip->getCampo('cod_sefip') );
            $obRPessoalAssentamento->setCodRais($_POST["inCodRais"]);
            $arFaixas = Sessao::read('Faixas');
            if ( count($arFaixas) > 0 ) {
                $arFaixa = array ();
                for ($inCount=0; $inCount<count($arFaixas); $inCount++) {
                    $arFaixa[$inCount]["inId"]                  = $arFaixas[$inCount]['inId']    ;
                    $arFaixa[$inCount]["inInicioIntervalo"]     = $arFaixas[$inCount]['inInicioIntervalo'];
                    $arFaixa[$inCount]["inFimIntervalo"]        = $arFaixas[$inCount]['inFimIntervalo']  ;
                    $arFaixa[$inCount]["flPercentualDesc"]      = $arFaixas[$inCount]['flPercentualDesc'];
                }
                $obErro = $obRPessoalAssentamento->addFaixa ($arFaixa);
            }
            Sessao::write('Faixas', $arFaixas);

            $arCorrecoes = Sessao::read('Correcoes');
            if ( count($arCorrecoes) > 0 ) {
                for ($inCount=0; $inCount<count($arCorrecoes); $inCount++) {
                    $obRPessoalAssentamento->obRPessoalVantagem->addPessoalFaixaCorrecao();
                    $obRPessoalAssentamento->obRPessoalVantagem->roUltimoPessoalFaixaCorrecao->setQuantMeses           ( $arCorrecoes[$inCount]['inQuantidadeMeses'] );
                    $obRPessoalAssentamento->obRPessoalVantagem->roUltimoPessoalFaixaCorrecao->setPercentualCorrecao   ( $arCorrecoes[$inCount]['nuPercentualCorrecao'] );
                }
            }
            Sessao::write('Correcoes', $arCorrecoes);

            foreach ($_POST as $Campo => $value) {
                if (substr($Campo,0,11)=='inCodAbaRes') {
                    $codCausa = explode('_',$Campo);
                    $obRPessoalAssentamento->addPessoalCausaRescisao();
                    $obRPessoalAssentamento->roUltimoPessoalCausaRescisao->setCodCausaRescisao($codCausa[1]);
                }
            }
            $obErro = $obRPessoalAssentamento->incluirAssentamento();
            if ( !$obErro->ocorreu() ) {
                sistemaLegado::alertaAviso($pgForm,"Assentamento: ".$_POST['stDescricao'],"incluir","aviso", Sessao::getId(), "../");
            } else {
                sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
            }
        }
    break;

    case "alterar":
        foreach ($_POST as $Campo => $value) {
            if (substr($Campo,0,11)=='inCodAbaRes') {
                $validaRescisao = true;
            }
        }
        $obErro = new Erro;
        $obRPessoalAssentamento->obRNorma->setNumNorma( $_POST['hdnCodNorma'] );
        $obErro = $obRPessoalAssentamento->obRNorma->listar( $rsNorma );
        $inCodNorma = $rsNorma->getCampo('cod_norma');
        if ( !$obErro->ocorreu() and sistemaLegado::comparaDatas( $_POST['hdndtPublicacao'],$_POST['dtDataInicioAssentamento'] ) and $inCodNorma ) {
            $obErro->setDescricao("A data inicial deve ser posterior à data de publicação!");
        }
        if ( !$obErro->ocorreu() and $_POST['dtDataFinalAssentamento'] != "" and sistemaLegado::comparaDatas( $_POST['dtDataInicioAssentamento'],$_POST['dtDataFinalAssentamento'] ) ) {
            $obErro->setDescricao("A data final deve ser posterior a data inicial!");
        }
        if ( !$obErro->ocorreu() and $_POST['hdnCodTipo'] == 2 ) {
            if ((($_POST['inCodSefip']) || (count(Sessao::read('Faixas')) > 0)) && ($validaRescisao == true)) {
                $obErro->setDescricao("Você dever preencher apenas campos de uma das abas, ou de afastamento temporário ou afastamento permanente!");
            }
            if ( count(Sessao::read('Faixas')) > 0 and $_POST['inCodSefip'] == "" ) {
                $obErro->setDescricao("Código Sefip não informado!");
            }
        }
        if ( !$obErro->ocorreu() and $_POST['hdnCodTipo'] == 4 ) {
            if ($_POST['dtDataInicio'] == "") {
                $obErro->setDescricao("A data inicial da aba vantagem deve ser inserida!");
            }
            if ( $_POST['dtDataInicio'] != "" and sistemaLegado::comparaDatas( $_POST['hdndtPublicacao'],$_POST['dtDataInicio'] ) and $inCodNorma ) {
                $obErro->setDescricao("A data inicial deve ser posterior à data de publicação!");
            }
        }
        if ( $obErro->ocorreu() ) {
            sistemaLegado::exibeAviso($obErro->getDescricao()," "," ");
        } else {
            $obRPessoalAssentamento->setCodAssentamento            ( $_POST['inCodAssentamento'] );
            //Assetamento

            if ( is_array($_REQUEST['inCodEventoSelecionados']) ) {
                foreach ($_REQUEST['inCodEventoSelecionados'] as $codEvento) {
                    $obRPessoalAssentamento->addEvento();
                    $obRPessoalAssentamento->roUltimoFolhaPagamentoEvento->setCodEvento($codEvento);
                }
            }
            if ( is_array($_REQUEST['inCodEventoProporcionalizacaoSelecionados']) ) {
                foreach ($_REQUEST['inCodEventoProporcionalizacaoSelecionados'] as $inCodEvento) {
                    $obRPessoalAssentamento->addEventoProporcional();
                    $obRPessoalAssentamento->roRFolhaPagamentoEventoProporcional->setCodEvento($inCodEvento);
                }
            }
            if ( is_array($_REQUEST['inCodRegimeSelecionados']) ) {
                foreach ($_REQUEST['inCodRegimeSelecionados'] as $stSubDivisao) {
                    $arSubDivisao = explode("/",$stSubDivisao);
                    $obRPessoalAssentamento->addPessoalSubDivisao();
                    $obRPessoalAssentamento->roUltimoPessoalSubDivisao->setCodSubDivisao($arSubDivisao[0]);
                }
            }
            $obRPessoalAssentamento->obRPessoalClassificacaoAssentamento->setCodTipo                       ( $_POST['hdnCodTipo']);
            $obRPessoalAssentamento->obRPessoalClassificacaoAssentamento->setCodClassificacaoAssentamento  ( $_POST['hdnCodClassificacao']);
            $obRPessoalAssentamento->obRNorma->setCodNorma                                                 ( $inCodNorma );
            $obRPessoalAssentamento->setDescricao                          ( $_POST['hdnDescricao']);
            $obRPessoalAssentamento->setSigla                              ( $_POST['hdnSigla']);
            $obRPessoalAssentamento->setAbreviacao                         ( $_POST['stAbreviacao']);
            $obRPessoalAssentamento->setGradeEfetividade                   ( $_POST['boGradeEfetividade']);
            $obRPessoalAssentamento->setAssentamentoInicio                 ( $_POST['boAssentamentoInicio']);
            $obRPessoalAssentamento->setRelFuncaoGratificada               ( $_POST['boRelFuncaoGratificada']);
            $obRPessoalAssentamento->setDataInicial                        ( $_POST['dtDataInicioAssentamento'] );
            $obRPessoalAssentamento->setDataFinal                          ( $_POST['dtDataFinalAssentamento'] );
            $obRPessoalAssentamento->setEventoAutomatico                   ( $_POST['boEventoAutomatico'] );
            $obRPessoalAssentamento->setAssentamentoAutomatico             ( $_POST['boAssentamentoAutomatico'] );
            $obRPessoalAssentamento->obRPessoalEsferaOrigem->setCodEsfera  ( $_POST['inCodEsfera']);
            $obRPessoalAssentamento->setCodOperador                        ( ($_POST["inCodOperador"] != "") ? $_POST["inCodOperador"] : $_POST['hdnCodOperador']);
            $obRPessoalAssentamento->setDiasAfastamento                    ( $_POST['inQuantidadeDias']);
            $obRPessoalAssentamento->obRPessoalVantagem->setDataInicial    ( $_POST['dtDataInicio'] );
            $obRPessoalAssentamento->obRPessoalVantagem->setDataFinal      ( $_POST['dtDataEncerramento'] );
            $obRPessoalAssentamento->setCancelarDireito                    ( $_POST['boCancelarDireito'] );
            $obRPessoalAssentamento->obRPessoalAssentamentoMotivo->setQuantDiasOnusEmpregador($_POST["inQuantDiasOnusEmpregador"]);
            $obRPessoalAssentamento->obRPessoalAssentamentoMotivo->setQuantDiasLicencaPremio($_POST["inQuantDiasLicencaPremio"]);

            //dados para afastamento
            if ($_POST['inCodSefip']) {
                $obRPessoalAssentamento->obRPessoalSefip->setNumSefip          ( $_POST['inCodSefip'] );
                $obRPessoalAssentamento->obRPessoalSefip->listar               ( $rsSefip             );
                $obRPessoalAssentamento->obRPessoalSefip->setCodSefip          ( $rsSefip->getCampo('cod_sefip') );
            }
            $obRPessoalAssentamento->setCodRais($_POST["inCodRais"]);
            $arFaixas = Sessao::read('Faixas');
            if (count($arFaixas)>0) {
                $arFaixa = array ();
                for ($inCount=0; $inCount<count($arFaixas); $inCount++) {
                    $arFaixa[$inCount]["inId"]                  = $arFaixas[$inCount]['inId']    ;
                    $arFaixa[$inCount]["inInicioIntervalo"]     = $arFaixas[$inCount]['inInicioIntervalo'];
                    $arFaixa[$inCount]["inFimIntervalo"]        = $arFaixas[$inCount]['inFimIntervalo']  ;
                    $arFaixa[$inCount]["flPercentualDesc"]      = $arFaixas[$inCount]['flPercentualDesc'];
                }
                $obErro = $obRPessoalAssentamento->addFaixa ($arFaixa);
            }
            Sessao::write('Faixas', $arFaixas);

            foreach ($_POST as $Campo => $value) {
                if (substr($Campo,0,11)=='inCodAbaRes') {
                    $codCausa = explode('_',$Campo);
                    $obRPessoalAssentamento->addPessoalCausaRescisao();
                    $obRPessoalAssentamento->roUltimoPessoalCausaRescisao->setCodCausaRescisao($codCausa[1]);
                }
            }

            $arCorrecoes = Sessao::read('Correcoes');
            if ( count($arCorrecoes) > 0 ) {
                for ($inCount=0; $inCount<count($arCorrecoes); $inCount++) {
                    $obRPessoalAssentamento->obRPessoalVantagem->addPessoalFaixaCorrecao();
                    $obRPessoalAssentamento->obRPessoalVantagem->roUltimoPessoalFaixaCorrecao->setQuantMeses           ( $arCorrecoes[$inCount]['inQuantidadeMeses'] );
                    $obRPessoalAssentamento->obRPessoalVantagem->roUltimoPessoalFaixaCorrecao->setPercentualCorrecao   ( $arCorrecoes[$inCount]['nuPercentualCorrecao'] );
                }
            }
            Sessao::write('Correcoes', $arCorrecoes);

            $obErro = $obRPessoalAssentamento->alterarAssentamento();
            if ( !$obErro->ocorreu() ) {
                sistemaLegado::alertaAviso($pgList,"Assentamento: ".$_POST['hdnDescricao'],"incluir","aviso", Sessao::getId(), "../");
            } else {
                sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
            }
        }
    break;

    case "excluir":
        $obRPessoalAssentamento->setCodAssentamento            ( $_GET['inCodAssentamento']);
        $obRPessoalAssentamento->setDescricao($_GET["stDescricao"]);
        $obErro = $obRPessoalAssentamento->excluirAssentamento();
        if ( !$obErro->ocorreu() )
            sistemaLegado::alertaAviso($pgList,"Assentamento: ".$obRPessoalAssentamento->getDescricao(),"excluir","aviso", Sessao::getId(), "../");
        else
            sistemaLegado::alertaAviso($pgList,$obErro->getDescricao(),"n_excluir","erro", Sessao::getId(), "../");
    break;

}
?>
