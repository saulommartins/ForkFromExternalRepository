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
    * Formulário
    * Data de Criação: 13/10/2008

    * @author Analista      Dagiane Vieira
    * @author Desenvolvedor Rafael Garbin

    * @package URBEM
    * @subpackage

    $Id:
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                                  );
include_once ( CAM_GRH_PON_MAPEAMENTO."TPontoDadosRelogioPonto.class.php"                                          );
include_once ( CAM_GRH_PON_MAPEAMENTO."TPontoConfiguracaoLotacao.class.php"                                        );

$stPrograma = "ManterPonto";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";
$arLink     = Sessao::read('link');

Sessao::remove("arJustificativasExcluidos");

(trim($_REQUEST["boTipoManutencao"])!="" ? $boTipoManutencao=$_REQUEST["boTipoManutencao"] : $boTipoManutencao=$arLink["boTipoManutencao"]);

function carregaDiaLote()
{
    list($parDia, $parMes, $parAno) = explode("/", $_REQUEST["dtLote"]);
    $data = $parAno."-".$parMes."-".$parDia;

    $ano =  substr($data, 0, 4);
    $mes =  substr($data, 5, -3);
    $dia =  substr($data, 8, 9);

    $diasemana = date("w", mktime(0,0,0,$mes,$dia,$ano) );

    switch ($diasemana) {
        case"0": $diasemana = "Domingo";       break;
        case"1": $diasemana = "Segunda-Feira"; break;
        case"2": $diasemana = "Terça-Feira";   break;
        case"3": $diasemana = "Quarta-Feira";  break;
        case"4": $diasemana = "Quinta-Feira";  break;
        case"5": $diasemana = "Sexta-Feira";   break;
        case"6": $diasemana = "Sábado";        break;
    }
    $retorno = $_REQUEST["dtLote"]." - ".$diasemana;

    return $retorno;
}

$jsOnload = "montaParametrosGET('FMProcessaOnLoad','inCodConfiguracao,stPeriodoInicial,stPeriodoFinal,inCodContrato,boTipoManutencao');";

$obRFolhaPagamentoPeriodoMovimentacao = new RFolhaPagamentoPeriodoMovimentacao;
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao($obRFolhaPagamentoPeriodoMovimentacao);

$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $_REQUEST["stAcao"] );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnTipoManutencao = new Hidden;
$obHdnTipoManutencao->setName ( "boTipoManutencao" );
$obHdnTipoManutencao->setValue( $boTipoManutencao );

$obSpnJustificativa = new Span;
$obSpnJustificativa->setId ( "spnJustificativa" );

$obSpnDadosPontoPeriodo = new Span;
$obSpnDadosPontoPeriodo->setId    ( "spnDadosPontoPeriodo" );
$obSpnDadosPontoPeriodo->setValue ( ""                     );

$obBtnOkFiltro = new Ok;
$obBtnOkFiltro->setName("okFiltro");
$obBtnOkFiltro->setValue("OK/Filtro");
$obBtnOkFiltro->setTabIndex(997);
$obBtnOkFiltro->obEvento->setOnClick("jQuery('#okVoltar').val('okFiltro'); Salvar();");

$obBtnOkLista = new Ok;
$obBtnOkLista->setName("okLista");
$obBtnOkLista->setValue("OK/Lista");
$obBtnOkLista->setTabIndex(998);
$obBtnOkLista->obEvento->setOnClick("jQuery('#okVoltar').val('okLista'); Salvar();");

$obBtnCancelar = new Button;
$obBtnCancelar->setName              ( "cancelar"                 );
$obBtnCancelar->setValue             ( "Cancelar"                 );
$obBtnCancelar->setTabIndex(999);
if (trim($boTipoManutencao) == "LOTE_DIARIO") {
    $obBtnCancelar->obEvento->setOnClick ( "Cancelar('".$pgFilt."');" );
} else {
    $obBtnCancelar->obEvento->setOnClick ( "Cancelar('".$pgList."');" );
}

$obHdnOkVoltar = new Hidden;
$obHdnOkVoltar->setName ( "okVoltar" );
$obHdnOkVoltar->setid   ( "okVoltar" );
$obHdnOkVoltar->setValue( "" );

if (trim($boTipoManutencao) == "LOTE_DIARIO") {
    $obLblDtLote = new Label;
    $obLblDtLote->setRotulo   ( "Dia do Lote" );
    $obLblDtLote->setName     ( "dtLote" );
    $obLblDtLote->setValue    ( carregaDiaLote() );

    // Carrega Dados para montar lista do lote
    switch ($_REQUEST["stTipoFiltro"]) {
        case "cgm_contrato":
        case "contrato":
            $arContratos = Sessao::read("arContratos");
            foreach ($arContratos as $chave => $arContrato) {
                $stCodigos .= $arContrato["cod_contrato"].",";
            }
            break;
        case "local":
            $stCodigos = implode(",", $_REQUEST["inCodLocalSelecionados"]);
            break;
        case "lotacao":
            $stCodigos = implode(",", $_REQUEST["inCodLotacaoSelecionados"]);
            break;
        case "sub_divisao_funcao":
            $stCodigos = implode(",", $_REQUEST["inCodSubDivisaoSelecionadosFunc"]);
            break;
    }
    $stCodigos = rtrim($stCodigos, ",");
    $stCodigos = rtrim($stCodigos, ",");

    $stFiltro  = "";
    if (trim($_REQUEST["boOrdenacao"]) == "NOME") {
        $stOrdem = " ORDER BY nom_cgm";
    } else {
        $stOrdem = " ORDER BY registro";
    }

    $obTPontoDadosRelogioPonto = new TPontoDadosRelogioPonto();
    $obTPontoDadosRelogioPonto->setDado($_REQUEST["stTipoFiltro"], $stCodigos);
    $obTPontoDadosRelogioPonto->setDado("existe_configuracao", true);
    $obTPontoDadosRelogioPonto->recuperaDadosContratoServidor($rsDadosRelogioPonto, $stFiltro, $stOrdem);

    Sessao::remove("arContratosLoteDiario");
    $arElementos = array();
    while (!$rsDadosRelogioPonto->eof()) {
        $arTMP = array();

        $inId                         = count($arElementos) + 1;
        $arTMP["inId"]                = $inId;
        $arTMP["cod_contrato" ]       = $rsDadosRelogioPonto->getCampo("cod_contrato");
        $arTMP["registro" ]           = $rsDadosRelogioPonto->getCampo("registro");
        $arTMP["nom_cgm" ]            = $rsDadosRelogioPonto->getCampo("nom_cgm");
        $arTMP["dtLote" ]             = $_REQUEST["dtLote"];

        $obTPontoDadosRelogioPonto->setDado("stDataInicial", $_REQUEST["dtLote"]);
        $obTPontoDadosRelogioPonto->setDado("stDataFinal"  , $_REQUEST["dtLote"]);
        $obTPontoDadosRelogioPonto->setDado("inCodContrato", $rsDadosRelogioPonto->getCampo("cod_contrato"));
        $obTPontoDadosRelogioPonto->recuperaDadosRelogioPontoPeriodo($rsHorario);

        while (!$rsHorario->eof()) {
            $arTMP["horas_trabalho" ]           = $rsHorario->getCampo("horas_trabalho");
            $arTMP["horas_faltas" ]             = $rsHorario->getCampo("horas_faltas");
            $arTMP["tipo" ]                     = $rsHorario->getCampo("tipo");
            $arTMP["carga_horaria_padrao"]      = $rsHorario->getCampo("carga_horaria_padrao");
            $arTMP["justificativa_afastamento"] = $rsHorario->getCampo("justificativa_afastamento");
            $arTMP["horario"]                   = $rsHorario->getCampo("horario");
            $arTMP["horario_padrao" ]           = $rsHorario->getCampo("horario_padrao");

            $rsHorario->proximo();
        }

        $arElementos[] = $arTMP;

        $rsDadosRelogioPonto->proximo();
    }
    Sessao::write("arContratosLoteDiario", $arElementos);
} else {

    //Busca Dados do Contrato do Servidor para setar Labels
    $stFiltro = " WHERE contrato.cod_contrato = ".$_REQUEST["inCodContrato"];
    $obTPontoDadosRelogioPonto = new TPontoDadosRelogioPonto();
    $obTPontoDadosRelogioPonto->recuperaDadosContratoServidor($rsDadosRelogioPonto, $stFiltro);

    //Busca a configuração do contrato
    $stFiltro = " AND contrato.cod_contrato = ".$_REQUEST["inCodContrato"];
    $obTPontoConfiguracaoLotacao = new TPontoConfiguracaoLotacao();
    $obTPontoConfiguracaoLotacao->recuperaConfiguracaoContrato($rsConfiguracaoLotacao, $stFiltro);

    $obHdnStDataInicial = new Hidden;
    $obHdnStDataInicial->setName ( "stPeriodoInicial" );
    $obHdnStDataInicial->setValue( $arLink["stDataInicial"] );

    $obHdnStDataFinal = new Hidden;
    $obHdnStDataFinal->setName ( "stPeriodoFinal" );
    $obHdnStDataFinal->setValue( $arLink["stDataFinal"] );

    $obHdnCodConfiguracao = new Hidden;
    $obHdnCodConfiguracao->setName ( "inCodConfiguracao" );
    $obHdnCodConfiguracao->setValue( $rsConfiguracaoLotacao->getCampo("cod_configuracao") );

    $obHdnCodContrato = new Hidden;
    $obHdnCodContrato->setName ( "inCodContrato" );
    $obHdnCodContrato->setValue( $_REQUEST["inCodContrato"] );

    $obLblMatricula = new Label;
    $obLblMatricula->setRotulo   ( "Matrícula" );
    $obLblMatricula->setName     ( "stMatricula" );
    $obLblMatricula->setValue    ( $rsDadosRelogioPonto->getCampo("registro")." - ".$rsDadosRelogioPonto->getCampo("nom_cgm") );

    $obLblDatas = new Label;
    $obLblDatas->setRotulo   ( "Admissão/Posse/Nomeação" );
    $obLblDatas->setName     ( "stDataAdmissaoPosseNomeacao" );
    $obLblDatas->setValue    ( $rsDadosRelogioPonto->getCampo("dt_admissao")." - ".$rsDadosRelogioPonto->getCampo("dt_posse")." - ".$rsDadosRelogioPonto->getCampo("dt_nomeacao") );

    $obImagem    = new Img;
    $obImagem->setCaminho   ( CAM_FW_IMAGENS."procuracgm.gif");
    $obImagem->setAlign     ( "absmiddle" );
    $obImagem->montaHtml();

    $obLink = new Link();
    $obLink->setValue($obImagem->getHtml());
    $obLink->setHref("JavaScript: abrePopUp('".CAM_GRH_PES_POPUPS."servidor/LSGradeHorarios.php','frm','','','','".Sessao::getId()."&inCodGrade=".$rsDadosRelogioPonto->getCampo("cod_grade")."', '','800','550')");

    $obLblGradeHorario = new Label;
    $obLblGradeHorario->setRotulo   ( "Grade de Horários" );
    $obLblGradeHorario->setName     ( "stGradeHorario" );
    $obLblGradeHorario->setValue    ( $rsDadosRelogioPonto->getCampo("cod_grade_formatado")." - ".$rsDadosRelogioPonto->getCampo("grade_horario") );

    $obLblPeriodo = new Label;
    $obLblPeriodo->setRotulo        ( "Período" );
    $obLblPeriodo->setName          ( "stPeriodo" );
    $obLblPeriodo->setValue         ( $arLink["stDataInicial"]." a ".$arLink["stDataFinal"] );

    $obLblInformativo = new Label;
    $obLblInformativo->setRotulo    ( "" );
    $obLblInformativo->setValue     ( "<font color='red'>Não existe configuração para a lotação do servidor</font>" );

    $obSpnHorasExtras = new Span;
    $obSpnHorasExtras->setId ( "spnHorasExtras" );

    $stLink  = Sessao::getId()."&inContrato=".$rsDadosRelogioPonto->getCampo("registro");
    $stLink .= "&inCodContrato=".$rsDadosRelogioPonto->getCampo("cod_contrato");
    $stLink .= "&stDataInicial=".$arLink["stDataInicial"];
    $stLink .= "&stDataFinal=".$arLink["stDataFinal"];
    $stLink .= "&inCodTipoClassificacao=2";

    $obBtnAfastamento = new Ok();
    $obBtnAfastamento->setValue ("Afastamentos");
    $obBtnAfastamento->setStyle ("width:150px;");
    $obBtnAfastamento->setId    ("boImprimirAfastamentos");
    $obBtnAfastamento->obEvento->setOnClick("abrePopUp('".CAM_GRH_PES_POPUPS."assentamento/FMConsultarAssentamentoGerado.php','frm','','','','".$stLink."', '','800','550');");

    $stLink .= "&stOrigem=manutencao";
    $obBtnJustificativas = new Ok();
    $obBtnJustificativas->setValue ("Justificativas");
    $obBtnJustificativas->setStyle ("width:150px;");
    $obBtnJustificativas->setId    ("boImprimirJustificativas");
    $obBtnJustificativas->obEvento->setOnClick("montaParametrosGET('montaSpanJustificativa','inCodContrato');");

    $obBtnEscalas = new Ok();
    $obBtnEscalas->setValue ("Escalas");
    $obBtnEscalas->setStyle ("width:150px;");
    $obBtnEscalas->setId    ("boImprimirEscalas");
    $obBtnEscalas->obEvento->setOnClick("abrePopUp('".CAM_GRH_PON_POPUPS."manutencao/FMConsultarEscala.php','frm','','','','".$stLink."', '','800','550');");

    $obBtnAtualizar = new Ok();
    $obBtnAtualizar->setValue ("Atualizar");
    $obBtnAtualizar->setStyle ("width:150px;");
    $obBtnAtualizar->setId    ("boAtualizar");
    $obBtnAtualizar->obEvento->setOnClick("jQuery('#okVoltar').val('okManutencaoIndividual'); Salvar();");
}

$obFormulario = new Formulario;
$obFormulario->addForm           ( $obForm                                                                   );
$obFormulario->addTitulo         ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right"          );
$obFormulario->addHidden         ( $obHdnCtrl                                                                );
$obFormulario->addHidden         ( $obHdnAcao                                                                );
$obFormulario->addHidden         ( $obHdnTipoManutencao                                                      );
$obFormulario->addHidden         ( $obHdnOkVoltar                                                            );

if (trim($boTipoManutencao) == "LOTE_DIARIO") {
    $obFormulario->addComponente     ( $obLblDtLote                                                              );
    $obFormulario->addSpan           ( $obSpnDadosPontoPeriodo                                                   );
    $obFormulario->defineBarra       ( array($obBtnOkFiltro,$obBtnCancelar)                                      );
} else {
    $obFormulario->addHidden         ( $obHdnCodContrato                                                         );
    $obFormulario->addHidden         ( $obHdnCodConfiguracao                                                     );
    $obFormulario->addHidden         ( $obHdnStDataInicial                                                       );
    $obFormulario->addHidden         ( $obHdnStDataFinal                                                         );
    $obFormulario->addComponente     ( $obLblMatricula                                                           );
    $obFormulario->addComponente     ( $obLblDatas                                                               );
    $obFormulario->agrupaComponentes ( array($obLblGradeHorario,$obLink)                                         );
    $obFormulario->addComponente     ( $obLblPeriodo                                                             );
    if ($rsConfiguracaoLotacao->getNumLinhas() > 0) {
        $obFormulario->addSpan           ( $obSpnHorasExtras                                                         );
        $obFormulario->defineBarra       ( array($obBtnAfastamento,$obBtnJustificativas,$obBtnEscalas,$obBtnAtualizar), "center", "" );
        $obFormulario->addSpan           ( $obSpnJustificativa                                                       );
        $obFormulario->addSpan           ( $obSpnDadosPontoPeriodo                                                   );
        $obFormulario->defineBarra       ( array($obBtnOkFiltro,$obBtnOkLista,$obBtnCancelar)                        );
    } else {
        $obFormulario->addComponente     ( $obLblInformativo                                                         );
        $obFormulario->defineBarra       ( array($obBtnCancelar)                                                     );
    }
}
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
