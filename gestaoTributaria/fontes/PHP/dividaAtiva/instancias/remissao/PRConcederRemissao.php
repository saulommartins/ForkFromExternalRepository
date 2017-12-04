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
  * Página de Formulario para Remissao
  * Data de criação : 22/08/2008

  * @author Analista: Fábio Bertoldi
  * @author Programador: Fernando Piccini Cercato

    * $Id: PRConcederRemissao.php 64290 2016-01-08 18:28:54Z evandro $

  Caso de uso: uc-05.04.11
**/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATAutoridade.class.php"             );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATModalidade.class.php"             );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATModalidadeParcela.class.php"      );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATModalidadeAcrescimo.class.php"    );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaAtiva.class.php"            );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaCGM.class.php"              );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaParcelaOrigem.class.php"    );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaImovel.class.php"           );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaEmpresa.class.php"          );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaParcelamento.class.php"     );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaParcelaOrigem.class.php"    );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaProcesso.class.php"         );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATRemissaoProcesso.class.php"       );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATParcelamento.class.php"           );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaParcela.class.php"          );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATPosicaoLivro.class.php"           );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaDocumento.class.php"        );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaDocumentoParcela.class.php" );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATEmissaoDocumento.class.php"       );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaAcrescimo.class.php"        );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaRemissao.class.php"         );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRCreditoGrupo.class.php");
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRCalculo.class.php");
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRCalculoCgm.class.php");
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRCadastroEconomicoCalculo.class.php");
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRImovelCalculo.class.php");
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRLancamentoCalculo.class.php" );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRLancamento.class.php" );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRParcela.class.php" );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRCarne.class.php" );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRCarneDevolucao.class.php" );

include_once ( CAM_GT_MON_MAPEAMENTO."TMONCredito.class.php");
include_once ( CAM_GT_MON_MAPEAMENTO."TMONAcrescimo.class.php");
include_once ( CAM_GT_ARR_MAPEAMENTO."FCalculo.class.php");
include_once ( CAM_GT_DAT_FUNCAO."FNumeracaoDivida.class.php");
include_once ( CAM_GT_DAT_COMPONENTES."IPopUpAutoridade.class.php" );
include_once ( CAM_GT_DAT_NEGOCIO."RDATConfiguracao.class.php" );
include_once ( CAM_GT_DAT_MAPEAMENTO . "FDATInscricaoDivida.class.php" );
include_once CAM_GT_DAT_MAPEAMENTO.'TDATDividaRelatorioRemissaoCredito.class.php';

include_once( CAM_GA_ADM_MAPEAMENTO."TAdministracaoModeloDocumento.class.php");
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoFuncao.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ConcederRemissao";
$pgForm = "FM".$stPrograma.".php";
$pgFilt = "FL".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";

SistemaLegado::BloqueiaFrames(true, false);

function percentageBar($nuPercentual,$stMensagem="")
{
    $stBarra  = "<div id=\"box\" style=\"width:500px;border:2px solid #fff;height:17px;text-align:center;\">";
    $stBarra .= $nuPercentual."%";
    $stBarra .= "<div id=\"bar\" style=\"width:".str_replace(',','.',$nuPercentual)."%;background:#FF8C00;height:14px;color:#fff;text-align:right;padding:3px 0px 0px 0px;margin-top:-19px\">";
    $stBarra .= "</div>";
    $stBarra .= "</div>";
    $stJs = "<script>";
    $stJs .= "jQuery('#loadingModal',parent.frames[2].document).attr('style','visibility:hidden;');";
    $stJs .= "jQuery('#showLoading h5',parent.frames[2].document).html('".$stBarra.$stMensagem."');";
    $stJs .= "</script>";
    echo $stJs;
    flush();
}

//a autoridade pra inscricao automatica ficou definida pra ser sempre a 1 fixo
$obRDATConfiguracao = new RDATConfiguracao;
$obRDATConfiguracao->consultar();
$stInscricaoAutomatica = $obRDATConfiguracao->getInscricaoAutomatica();
$inCodModalidade = $obRDATConfiguracao->getCodModalidade();

$inCodModeloDocumentoRemissao = $_REQUEST['inCodModeloDocumentoRemissao'];
$inCodTipoDocumentoRemissao = $obRDATConfiguracao->getCodTipoDocumentoRemissao();

$obTDATDividaRemissao = new TDATDividaRemissao;
$obTDATDividaAtiva = new TDATDividaAtiva;
$obTDATModalidade = new TDATModalidade;
$filtro = " WHERE dm.cod_modalidade = ".$inCodModalidade;
$obTDATModalidade->recuperaInfoModalidade( $rsModalidade, $filtro );

$obRDATConfiguracao = new RDATConfiguracao;
$obRDATConfiguracao->consultar();
$stLancamentosAtivos = $obRDATConfiguracao->getLancamentoAtivo();
$stValidacaoRemissao = $obRDATConfiguracao->getValidacao();

$arListaGrupoCreditoSessao = Sessao::read( "arListaGrupoCredito" );
$inTotalNaListaGrupoCreditoSessao = count( $arListaGrupoCreditoSessao );

if ($inTotalNaListaGrupoCreditoSessao <= 0) {
    SistemaLegado::LiberaFrames();
    SistemaLegado::exibeAviso("Lista de grupo de créditos está vazia!", "n_incluir", "erro" );
    exit;
}

$stFuncaoValidacao = "";

if ($_REQUEST["inCodFuncao"]) {
    $arCodFuncao = explode('.', $_REQUEST["inCodFuncao"] );

    $obTFuncao = new TAdministracaoFuncao;
    $obTFuncao->setDado( "cod_biblioteca", $arCodFuncao[1] );
    $obTFuncao->setDado( "cod_modulo", $arCodFuncao[0] );
    $obTFuncao->setDado( "cod_funcao", $arCodFuncao[2] );
    $obTFuncao->recuperaPorChave( $rsFuncao );

    $stFuncaoValidacao = $rsFuncao->getCampo( "nom_funcao" );
}

$rsListaDados = new RecordSet;

$stFiltro = "";
if ($_REQUEST["inCodContribuinteInicial"] && $_REQUEST["inCodContribuinteFinal"]) {
    $stFiltro .= " AND calculo_cgm.numcgm BETWEEN ".$_REQUEST["inCodContribuinteInicial"]." AND ".$_REQUEST["inCodContribuinteFinal"];
}else
    if ($_REQUEST["inCodContribuinteInicial"]) {
        $stFiltro .= " AND calculo_cgm.numcgm = ".$_REQUEST["inCodContribuinteInicial"];
    }else
        if ($_REQUEST["inCodContribuinteFinal"]) {
            $stFiltro .= " AND calculo_cgm.numcgm = ".$_REQUEST["inCodContribuinteFinal"];
        }

if ($_REQUEST["inCodImovelInicial"] && $_REQUEST["inCodImovelFinal"]) {
    $stFiltro .= " AND imovel_calculo.inscricao_municipal BETWEEN ".$_REQUEST["inCodImovelInicial"]." AND ".$_REQUEST["inCodImovelFinal"];
}else
    if ($_REQUEST["inCodImovelInicial"]) {
        $stFiltro .= " AND imovel_calculo.inscricao_municipal = ".$_REQUEST["inCodImovelInicial"];
    }else
        if ($_REQUEST["inCodImovelFinal"]) {
            $stFiltro .= " AND imovel_calculo.inscricao_municipal = ".$_REQUEST["inCodImovelFinal"];
        }

if ($_REQUEST["inNumInscricaoEconomicaInicial"] && $_REQUEST["inNumInscricaoEconomicaFinal"]) {
    $stFiltro .= " AND cadastro_economico_calculo.inscricao_economica BETWEEN ".$_REQUEST["inNumInscricaoEconomicaInicial"]." AND ".$_REQUEST["inNumInscricaoEconomicaFinal"];
}else
    if ($_REQUEST["inNumInscricaoEconomicaInicial"]) {
        $stFiltro .= " AND cadastro_economico_calculo.inscricao_economica = ".$_REQUEST["inNumInscricaoEconomicaInicial"];
    }else
        if ($_REQUEST["inNumInscricaoEconomicaFinal"]) {
            $stFiltro .= " AND cadastro_economico_calculo.inscricao_economica = ".$_REQUEST["inNumInscricaoEconomicaFinal"];
        }

$stFiltro .= " AND calculo_grupo_credito.cod_grupo IN ( ";
$stFiltro2 = " AND calculo_grupo_credito.ano_exercicio IN ( ";

for ($inX=0; $inX<$inTotalNaListaGrupoCreditoSessao; $inX++) {
    $arDados = explode( "/", $arListaGrupoCreditoSessao[$inX]["stCodGrupo"] );
    $stFiltro .= $arDados[0].", ";
    $stFiltro2 .= "'".$arDados[1]."', ";
}

// $stFiltro .= $arDados[0]." ) ";
// $stFiltro2 .= $arDados[1]." ) ";
$stFiltro = substr($stFiltro, 0, -2)." ) ";
$stFiltro2 = substr($stFiltro2, 0, -2)." ) ";
$stFiltro .= $stFiltro2;

//monta filtro pelos créditos selecionados
$stFiltroCredito = '';
foreach ($arListaGrupoCreditoSessao as $arGrupoCredito) {
    $arCreditosGrupo = Sessao::read($arGrupoCredito['stCodGrupo']);
    foreach ($arCreditosGrupo as $arCreditoGrupo) {
        if ($arCreditoGrupo['selecionado']) {
            $stFiltroCredito .= " (   parcela_origem.cod_credito  = ".$arCreditoGrupo['cod_credito'];
            $stFiltroCredito .= " AND parcela_origem.cod_especie  = ".$arCreditoGrupo['cod_especie'];
            $stFiltroCredito .= " AND parcela_origem.cod_genero   = ".$arCreditoGrupo['cod_genero'];
            $stFiltroCredito .= " AND parcela_origem.cod_natureza = ".$arCreditoGrupo['cod_natureza'];
            $stFiltroCredito .= " ) OR ";
        }
    }
}
$stFiltroCredito = substr($stFiltroCredito, 0, -4);

if ( $stLancamentosAtivos == "desconsiderar" )
    $stFiltroLancamentoAtivo = " AND lancamento.situacao = 'D' ";
else
    $stFiltroLancamentoAtivo = "";

percentageBar( 0.00, "Processando..." );
$obTDATDividaRemissao = new TDATDividaRemissao;
$obTDATDividaRemissao->ListaLancamentosPraRemissao( $rsListaDados, $stFiltro, $stFuncaoValidacao, str_replace ( ',', '.', str_replace ( '.', '', $_REQUEST["stLimiteCredito"] ) ), $stFiltroLancamentoAtivo );

$arDadosRemissaoTMP = $rsListaDados->getElementos();
$arDadosTMP = array();
$arDadosTMP2 = array();
$arDadosTMPORDEM = array();
$arDadosExcluidos = array();

$arDataTMP2 = explode ( "/", $_REQUEST["dtLimiteInscricaoDA"] );
for ( $inX=0; $inX<count( $arDadosRemissaoTMP ); $inX++ ) {
    $stFiltro = $arDadosRemissaoTMP[$inX]["inscricao"];
    $boIncluir = true;

    if ($stValidacaoRemissao == "todos") {
        if ($arDadosExcluidos[$stFiltro] == 1) {
            $boIncluir = false;
        }
    }

    if ($arDadosRemissaoTMP[$inX]["dt_inscricao_da"] && $boIncluir) {
        unset( $arDataTMP1 );
        $arDataTMP1 = explode( "-", $arDadosRemissaoTMP[$inX]["dt_inscricao_da"] );
        if ($arDataTMP1[0].$arDataTMP1[1].$arDataTMP1[2] > $arDataTMP2[2].$arDataTMP2[1].$arDataTMP2[0]) {
            if ($stValidacaoRemissao == "todos") {
                $arDadosExcluidos[$stFiltro] = 1;
                unset( $arDadosTMP[$stFiltro] ); //podia ter alguma coisa anterior deste cara
            }

            $boIncluir = false;
        }
    }

    if ($_REQUEST["stLimiteCredito"] && $boIncluir) {
        if ( ( $arDadosRemissaoTMP[$inX]["credito_remir"] == 'f' ) && $boIncluir ) {
            if ($stValidacaoRemissao == "todos") {
                $arDadosExcluidos[$stFiltro] = 1;
                unset( $arDadosTMP[$stFiltro] ); //podia ter alguma coisa anterior deste cara
            }

            $boIncluir = false;
        }
    }

    if ($_REQUEST["stLimiteExercicio"] && $boIncluir) {
        if (( $arDadosRemissaoTMP[$inX]["valor"] > str_replace ( ',', '.', str_replace ( '.', '', $_REQUEST["stLimiteExercicio"] ) ) ) && $boIncluir ) {
            if ($stValidacaoRemissao == "todos") {
                $arDadosExcluidos[$stFiltro] = 1;
                unset( $arDadosTMP[$stFiltro] ); //podia ter alguma coisa anterior deste cara
            }

            $boIncluir = false;
        }
    }

    if ($boIncluir) {
        if ($arDadosTMP[$stFiltro]) {
            $arDadosTMP[$stFiltro]["cod_lancamento"] .= ",".$arDadosRemissaoTMP[$inX]["cod_lancamento"];
            $arDadosTMP[$stFiltro]["dt_inscricao_da"] .= ",".$arDadosRemissaoTMP[$inX]["dt_inscricao_da"];
            $arDadosTMP[$stFiltro]["valores"] .= ",".$arDadosRemissaoTMP[$inX]["valor"];
            $arDadosTMP[$stFiltro]["exercicio"] = $arDadosRemissaoTMP[$inX]["exercicio"];
            $arDadosTMP[$stFiltro]["exercicios"] .= ",".$arDadosRemissaoTMP[$inX]["exercicio"];
            $arDadosTMP[$stFiltro]["tipo_inscricao"] .= ",".$arDadosRemissaoTMP[$inX]["tipo_inscricao"];
            $arDadosTMP[$stFiltro]["valor"] += $arDadosRemissaoTMP[$inX]["valor"];
            $arDadosTMP[$stFiltro][$arDadosTMP[$stFiltro]["exercicio"]] += $arDadosRemissaoTMP[$inX]["valor"];
        } else {
            $arDadosTMPORDEM[] = $stFiltro;
            $arDadosTMP[$stFiltro] = $arDadosRemissaoTMP[$inX];
            $arDadosTMP[$stFiltro]["valores"] = $arDadosRemissaoTMP[$inX]["valor"];
            $arDadosTMP[$stFiltro]["exercicios"] = $arDadosRemissaoTMP[$inX]["exercicio"];
            $arDadosTMP[$stFiltro]["valores"] = $arDadosRemissaoTMP[$inX]["valor"];
            $arDadosTMP[$stFiltro][$arDadosTMP[$stFiltro]["exercicio"]] = $arDadosRemissaoTMP[$inX]["valor"];
        }
    }
}

unset( $arDadosRemissaoTMP );
for ( $inX=0; $inX<count( $arDadosTMPORDEM ); $inX++ ) {
    if ( !$arDadosTMP[$arDadosTMPORDEM[$inX]] )
        continue;

    $boIncluir = true;
    if ($_REQUEST["stLimiteTotal"] && $boIncluir) {
        if ( $arDadosTMP[$arDadosTMPORDEM[$inX]]["valor"] > str_replace ( ',', '.', str_replace ( '.', '', $_REQUEST["stLimiteTotal"] ) ) ) {
            $boIncluir = false;
        }
    }

    if ( $boIncluir )
        $arDadosTMP2[] = $arDadosTMP[$arDadosTMPORDEM[$inX]];
}

unset( $arDadosTMP );
unset( $arDadosTMPORDEM );
unset( $arDadosExcluidos );
if ( count( $arDadosTMP2 )<= 0 ) {
    SistemaLegado::LiberaFrames();
    SistemaLegado::exibeAviso("Nenhum registro retornado para os grupos de crédito selecionados!", "n_incluir", "erro" );
    exit;
}

$obTDATDividaRelatorioRemissaoCredito = new TDATDividaRelatorioRemissaoCredito;
$obTDATDividaRelatorioRemissaoCredito->recuperaTodos($rsLancamentosRelatorio);

while (!$rsLancamentosRelatorio->EOF()) {
    $obTDATDividaRelatorioRemissaoCredito->setDado('cod_lancamento', $rsLancamentosRelatorio->getCampo('cod_lancamento'));
    $obTDATDividaRelatorioRemissaoCredito->exclusao();

    $rsLancamentosRelatorio->proximo();
}

$stFiltro = "where a.cod_acao = '".Sessao::read('acao')."'";
$obTModeloDocumento = new TAdministracaoModeloDocumento;
$obTModeloDocumento->recuperaRelacionamento( $rsDocumentos, $stFiltro );
unset( $obTModeloDocumento );

$obConexao = new Conexao;

$inProcesso = $_REQUEST['inProcesso'];
if ( $inProcesso )
    $arProcesso = explode( "/", $inProcesso );

$inCodNorma = $_REQUEST["inCodNorma"];
$stObservacao = $_REQUEST["stObservacao"];
$arDadosSessao = array();

$inInscricoesRemir = 0;
$inTotalRemir = count( $arDadosTMP2 );
$obTransacao = new Transacao;
    while ($inInscricoesRemir < $inTotalRemir) {
        $boFlagTransacao = false;
        $boTransacao = "";
        $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

        $stLancamentos = $arDadosTMP2[$inInscricoesRemir]["cod_lancamento"];
        $inInscricao = $arDadosTMP2[$inInscricoesRemir]["inscricao"];
        $inNumCGM = $arDadosTMP2[$inInscricoesRemir]["numcgm"];
        $stExercicios = $arDadosTMP2[$inInscricoesRemir]["exercicios"];
        $stTipoInscricoes = $arDadosTMP2[$inInscricoesRemir]["tipo_inscricao"];

        $inInscricoesRemir++;

        $flTotalRemir = round( ( $inInscricoesRemir * 100 ) / $inTotalRemir, 2 );
        $stResultado = "CGM: ".$inNumCGM." - lançamento(s): ".$stLancamentos;
        percentageBar( $flTotalRemir, $stResultado );

        $arDadosSessao[] = array( "Lancamentos" => $stLancamentos );

        $arLancamentos = explode( ",", $stLancamentos );
        $arExercicios = explode( ",", $stExercicios );
        $arTipoInscricoes = explode( ",", $stTipoInscricoes );
        $stDataAtual = date ("d/m/Y");
        for ( $inLancamentos=0; $inLancamentos<count( $arLancamentos ); $inLancamentos++ ) {
            $inCodLancamento = $arLancamentos[$inLancamentos];
            $inExercicio = $arExercicios[$inLancamentos];
            $stTipoInscricao = $arTipoInscricoes[$inLancamentos];

            $obTDATDividaRelatorioRemissaoCredito->setDado('cod_lancamento', $inCodLancamento);
            $obErro2 = $obTDATDividaRelatorioRemissaoCredito->consultar();
            if ($obErro2->ocorreu()) {
                $obTDATDividaRelatorioRemissaoCredito->inclusao($boTransacao);
            }

            if ($stInscricaoAutomatica == "nao") {
                if ($stTipoInscricao != "d") { //carnes devem ser cancelados com o motivo 14
                    $stParametros = $inCodLancamento;
                    unset( $obTDATDividaAtiva );
                    $obTDATDividaAtiva = new TDATDividaAtiva;
                    $obTDATDividaAtiva->recuperaListaParcelasDivida( $rsParcelasDivida, $stParametros, $boTransacao );

                    $arNumeracoes = array();
                    $inTotalNumeracoes = 0;
                    $obTDATDividaAtiva->recuperaListaCarnesParaCancelar( $rsListaCarnesCancelar, $rsParcelasDivida->getCampo("cod_parcela"), $boTransacao );

                    unset( $obTARRCarneDevolucao );
                    $obTARRCarneDevolucao = new TARRCarneDevolucao;
                    while ( !$rsListaCarnesCancelar->Eof() ) {
                        $obTARRCarneDevolucao->setDado( "cod_motivo", 14 );
                        $obTARRCarneDevolucao->setDado( "numeracao", $rsListaCarnesCancelar->getCampo("numeracao") );
                        $obTARRCarneDevolucao->setDado( "cod_convenio", $rsListaCarnesCancelar->getCampo("cod_convenio") );
                        $obTARRCarneDevolucao->setDado( "dt_devolucao", $stDataAtual );
                        $obTARRCarneDevolucao->inclusao( $boTransacao );
                        $rsListaCarnesCancelar->proximo();
                    }
                } else {
                    $stSql = "
                           SELECT DISTINCT divida_parcelamento.cod_inscricao
                                , divida_parcelamento.exercicio
                                , divida_parcelamento.num_parcelamento
                             FROM divida.divida_parcelamento
                        LEFT JOIN divida.divida_remissao
                               ON divida_remissao.cod_inscricao = divida_parcelamento.cod_inscricao
                              AND divida_remissao.exercicio = divida_parcelamento.exercicio
                       INNER JOIN ( SELECT min(parcela_origem.num_parcelamento) AS num_parcelamento
                                         , parcela.cod_lancamento
                                      FROM divida.parcela_origem
                                INNER JOIN divida.divida_parcelamento
                                        ON divida_parcelamento.num_parcelamento = parcela_origem.num_parcelamento
                                INNER JOIN arrecadacao.parcela
                                        ON parcela.cod_parcela = parcela_origem.cod_parcela
                                  GROUP BY parcela.cod_lancamento
                                  )AS parcela_origem
                               ON parcela_origem.num_parcelamento = divida_parcelamento.num_parcelamento
                       INNER JOIN ( SELECT divida_parcelamento.cod_inscricao
                                         , divida_parcelamento.exercicio
                                      FROM divida.parcela_origem
                                INNER JOIN divida.parcelamento
                                        ON parcelamento.num_parcelamento = parcela_origem.num_parcelamento
                                INNER JOIN divida.divida_parcelamento
                                        ON divida_parcelamento.num_parcelamento = parcelamento.num_parcelamento
                                INNER JOIN divida.modalidade_vigencia
                                        ON parcelamento.cod_modalidade = modalidade_vigencia.cod_modalidade
                                       AND parcelamento.timestamp_modalidade = modalidade_vigencia.timestamp
                                INNER JOIN arrecadacao.parcela
                                        ON parcela_origem.cod_parcela = parcela.cod_parcela
                                     WHERE modalidade_vigencia.cod_forma_inscricao IN (2,4) --deve funcionar somente para as formas 2 e 4
                                       AND ( ".$stFiltroCredito." )
                                  GROUP BY divida_parcelamento.cod_inscricao
                                         , divida_parcelamento.exercicio
                                   ) as valida
                               ON valida.cod_inscricao = divida_parcelamento.cod_inscricao
                              AND valida.exercicio = divida_parcelamento.exercicio
                            WHERE divida_remissao.cod_inscricao IS NULL
                              AND parcela_origem.cod_lancamento = ".$inCodLancamento;

                    unset( $rsRecordSet );
                    $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

                    while ( !$rsRecordSet->Eof() ) {
                            $obTDATDividaRemissao->setDado( 'cod_inscricao', $rsRecordSet->getCampo("cod_inscricao") );
                            $obTDATDividaRemissao->setDado( 'exercicio', $rsRecordSet->getCampo("exercicio") );
                            $obTDATDividaRemissao->setDado( 'cod_norma', $inCodNorma );
                            $obTDATDividaRemissao->setDado( 'numcgm', Sessao::read('numCgm') );
                            $obTDATDividaRemissao->setDado( 'dt_remissao', $stDataAtual );
                            $obTDATDividaRemissao->setDado( 'observacao', $stObservacao );
                            $obTDATDividaRemissao->inclusao( $boTransacao );
                            $obTDATDividaDocumento = new TDATDividaDocumento;
                            if ($inCodModeloDocumentoRemissao != '') {
                                $obTDATDividaDocumento->setDado( "num_parcelamento"  , $rsRecordSet->getCampo("num_parcelamento") );
                                $obTDATDividaDocumento->setDado( "cod_documento"     , $inCodModeloDocumentoRemissao );
                                $obTDATDividaDocumento->setDado( "cod_tipo_documento", $inCodTipoDocumentoRemissao );
                                $obTDATDividaDocumento->inclusao($boTransacao);
                            }
                            if ($inProcesso) {
                                $obTDATRemissaoProcesso->setDado( 'cod_inscricao', $arInscricoesDA[$inInscricaoDA] );
                                $obTDATRemissaoProcesso->setDado( 'exercicio', Sessao::getExercicio() );
                                $obTDATRemissaoProcesso->setDado( 'cod_processo', $arProcesso[0] );
                                $obTDATRemissaoProcesso->setDado( 'ano_exercicio', $arProcesso[1] );
                                $obTDATRemissaoProcesso->inclusao( $boTransacao );
                            }

                        $rsRecordSet->proximo();
                    }

                    //adicionar aqui documentos da remissao
                    $stSql = "
                        SELECT DISTINCT divida_parcelamento.num_parcelamento
                          FROM divida.divida_parcelamento
                     LEFT JOIN divida.divida_remissao
                            ON divida_remissao.cod_inscricao = divida_parcelamento.cod_inscricao
                           AND divida_remissao.exercicio = divida_parcelamento.exercicio
                    INNER JOIN ( SELECT min(parcela_origem.num_parcelamento) AS num_parcelamento
                                      , parcela.cod_lancamento
                                   FROM divida.parcela_origem
                             INNER JOIN divida.divida_parcelamento
                                     ON divida_parcelamento.num_parcelamento = parcela_origem.num_parcelamento
                             INNER JOIN arrecadacao.parcela
                                     ON parcela.cod_parcela = parcela_origem.cod_parcela
                               GROUP BY parcela.cod_lancamento
                               )AS parcela_origem
                            ON parcela_origem.num_parcelamento = divida_parcelamento.num_parcelamento
                         WHERE divida_remissao.cod_inscricao IS NULL
                           AND parcela_origem.cod_lancamento = ".$inCodLancamento;

                    unset( $rsRecordSet );
                    $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

                    $obTDATDividaDocumento = new TDATDividaDocumento;
                    while ( !$rsDocumentos->Eof() ) {
                        $rsRecordSet->setPrimeiroElemento();
                        while ( !$rsRecordSet->Eof() ) {
                            $obTDATDividaDocumento->setDado( "num_parcelamento"  , $rsRecordSet->getCampo("num_parcelamento") );
                            $obTDATDividaDocumento->setDado( "cod_documento"     , $rsDocumentos->getCampo( "cod_documento" ) );
                            $obTDATDividaDocumento->setDado( "cod_tipo_documento", $rsDocumentos->getCampo( "cod_tipo_documento" ) );
                            $obTDATDividaDocumento->inclusao( $boTransacao );

                            $rsRecordSet->proximo();
                        }

                        $rsDocumentos->proximo();
                    }

                }
            } else { //if ($stInscricaoAutomatica == "nao") {
                if ($stTipoInscricao != "d") { //inscreve primeiro na divida ativa
                    unset( $arInscricoesDA );
                    $arInscricoesDA = array();

                    $stParametros = $inCodLancamento;
                    $obTDATDividaAtiva->recuperaListaParcelasDivida( $rsParcelasDivida, $stParametros, $boTransacao );

                    unset( $obTARRCarneDevolucao );
                    $obTARRCarneDevolucao = new TARRCarneDevolucao;

                    $stSql = " SELECT to_char( arrecadacao.fn_busca_vencimento_base_lancamento( ".$inCodLancamento.", '".$inExercicio."' )::date, 'dd/mm/yyyy' ) as vencimento_base_br; ";
                    unset( $rsRecordSet );
                    $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
                    $stDtVencimentoBaseBR = $rsRecordSet->getCampo( "vencimento_base_br" );

                    $arNumeracoes = array();
                    $inTotalNumeracoes = 0;
                    $obTDATDividaAtiva->recuperaListaCarnesParaCancelar( $rsListaCarnesCancelar, $rsParcelasDivida->getCampo("cod_parcela"), $boTransacao );
                    while ( !$rsListaCarnesCancelar->Eof() ) {
                        $obTARRCarneDevolucao->setDado( "cod_motivo", 11 );
                        $obTARRCarneDevolucao->setDado( "numeracao", $rsListaCarnesCancelar->getCampo("numeracao") );
                        $obTARRCarneDevolucao->setDado( "cod_convenio", $rsListaCarnesCancelar->getCampo("cod_convenio") );
                        $obTARRCarneDevolucao->setDado( "dt_devolucao", $stDataAtual );
                        $obTARRCarneDevolucao->inclusao( $boTransacao );
                        $rsListaCarnesCancelar->proximo();
                    }

                    $arNumParcelamento = array();
                    if ( $rsModalidade->getCampo( "cod_forma_inscricao" ) == 4 ) { //Parcelas Individuais por Crédito
                        $arParcelasDivida = $rsParcelasDivida->getElementos();
                        for ( $inW=0; $inW<count($arParcelasDivida); $inW++ ) {
                            $obTDATDividaAtiva->recuperaCodigoInscricaoComponente( $rsListaPosicao, $boTransacao );

                            $inCodInscricao = $rsListaPosicao->getCampo( "max_inscricao" )>0?$rsListaPosicao->getCampo( "max_inscricao" )+1:1;
                            $arInscricoesDA[] = $inCodInscricao;

                            unset( $obTDATPosicaoLivro );
                            $obTDATPosicaoLivro = new TDATPosicaoLivro;
                            $obTDATPosicaoLivro->recuperaPosicaoLivro( $rsListaLivro, $boTransacao );

                            $arLivros = explode( "-", $rsListaLivro->getCampo("valor") );

                            $obTDATDividaAtiva->setDado( "num_livro"            , $arLivros[1] );
                            $obTDATDividaAtiva->setDado( "num_folha"            , $arLivros[2] );
                            $obTDATDividaAtiva->setDado( "exercicio"            , Sessao::getExercicio() );
                            $obTDATDividaAtiva->setDado( "cod_inscricao"        , $inCodInscricao );
                            $obTDATDividaAtiva->setDado( "cod_autoridade"       , 1 );
                            $obTDATDividaAtiva->setDado( "numcgm_usuario"       , Sessao::read('numCgm') );
                            $obTDATDividaAtiva->setDado( "dt_inscricao"         , $stDataAtual );
                            $obTDATDividaAtiva->setDado( "dt_vencimento_origem" , $stDtVencimentoBaseBR );
                            $obTDATDividaAtiva->setDado( "exercicio_original"   , $inExercicio );
                            $obTDATDividaAtiva->setDado( "exercicio_livro"      , $arLivros[3] );
                            $obTDATDividaAtiva->inclusao( $boTransacao );

                            unset( $obTDATDividaCGM );
                            $obTDATDividaCGM = new TDATDividaCGM;
                            $obTDATDividaCGM->setDado( "exercicio"     , Sessao::getExercicio() );
                            $obTDATDividaCGM->setDado( "cod_inscricao" , $inCodInscricao );
                            $obTDATDividaCGM->setDado( "numcgm"        , $inNumCGM );
                            $obTDATDividaCGM->inclusao( $boTransacao );

                            if ($stTipoInscricao == "i") {
                                unset( $obTDATDividaImovel );
                                $obTDATDividaImovel = new TDATDividaImovel;
                                $obTDATDividaImovel->setDado( "exercicio"           , Sessao::getExercicio() );
                                $obTDATDividaImovel->setDado( "cod_inscricao"       , $inCodInscricao );
                                $obTDATDividaImovel->setDado( "inscricao_municipal" , $inInscricao );
                                $obTDATDividaImovel->inclusao( $boTransacao );
                            }else
                                if ($stTipoInscricao == "e") {
                                    unset( $obTDATDividaEmpresa );
                                    $obTDATDividaEmpresa = new TDATDividaEmpresa;
                                    $obTDATDividaEmpresa->setDado( "exercicio"          , Sessao::getExercicio() );
                                    $obTDATDividaEmpresa->setDado( "cod_inscricao"      , $inCodInscricao );
                                    $obTDATDividaEmpresa->setDado( "inscricao_economica", $inInscricao );
                                    $obTDATDividaEmpresa->inclusao( $boTransacao );
                                }

                            unset( $obTDATParcelamento );
                            $obTDATParcelamento = new TDATParcelamento;
                            $obTDATParcelamento->recuperaNumeroParcelamento( $rsNumeroParcelamento, $boTransacao );
                            $inNumeroParcelamento = $rsNumeroParcelamento->getCampo("valor");

                            $arNumParcelamento[] = $inNumeroParcelamento;
                            $obTDATParcelamento->setDado( "num_parcelamento"    , $inNumeroParcelamento );
                            $obTDATParcelamento->setDado( "numcgm_usuario"      , Sessao::read('numCgm') );
                            $obTDATParcelamento->setDado( "cod_modalidade"      , $rsModalidade->getCampo("cod_modalidade"));
                            $obTDATParcelamento->setDado( "timestamp_modalidade", $rsModalidade->getCampo("timestamp") );
                            $obTDATParcelamento->setDado( "numero_parcelamento" , -1 );
                            $obTDATParcelamento->setDado( "exercicio"           , '-1' );
                            $obTDATParcelamento->inclusao( $boTransacao );

                            unset( $obTDATDividaParcelamento );
                            $obTDATDividaParcelamento = new TDATDividaParcelamento;
                            $obTDATDividaParcelamento->setDado( "num_parcelamento"  , $inNumeroParcelamento );
                            $obTDATDividaParcelamento->setDado( "exercicio"         , Sessao::getExercicio() );
                            $obTDATDividaParcelamento->setDado( "cod_inscricao"     , $inCodInscricao );
                            $obTDATDividaParcelamento->inclusao( $boTransacao );

                            unset( $obTDATDividaDocumento );
                            $obTDATDividaDocumento = new TDATDividaDocumento;
                            while ( !$rsModalidade->Eof() ) {
                                $obTDATDividaDocumento->setDado( "num_parcelamento"  , $inNumeroParcelamento );
                                $obTDATDividaDocumento->setDado( "cod_documento"     , $rsModalidade->getCampo( "cod_documento" ) );
                                $obTDATDividaDocumento->setDado( "cod_tipo_documento", $rsModalidade->getCampo( "cod_tipo_documento" ) );
                                $obTDATDividaDocumento->inclusao( $boTransacao );
                                $inTotalDocumentos++;
                                $rsModalidade->proximo();
                            }

                            $rsModalidade->setPrimeiroElemento();
                            unset( $obTDATDividaParcelaOrigem );
                            $obTDATDividaParcelaOrigem = new TDATDividaParcelaOrigem;
                            $obTDATDividaParcelaOrigem->setDado( "cod_parcela"      , $arParcelasDivida[$inW]["cod_parcela"]  );
                            $obTDATDividaParcelaOrigem->setDado( "cod_especie"      , $arParcelasDivida[$inW]["cod_especie"]  );
                            $obTDATDividaParcelaOrigem->setDado( "cod_genero"       , $arParcelasDivida[$inW]["cod_genero"]   );
                            $obTDATDividaParcelaOrigem->setDado( "cod_natureza"     , $arParcelasDivida[$inW]["cod_natureza"] );
                            $obTDATDividaParcelaOrigem->setDado( "cod_credito"      , $arParcelasDivida[$inW]["cod_credito"]  );
                            $obTDATDividaParcelaOrigem->setDado( "num_parcelamento" , $inNumeroParcelamento                   );
                            $obTDATDividaParcelaOrigem->setDado( "valor"            , $arParcelasDivida[$inW]["valor"]        );
                            $obTDATDividaParcelaOrigem->inclusao( $boTransacao );

                            unset( $obTDATDividaAcrescimo );
                            $obTDATDividaAcrescimo = new TDATDividaAcrescimo;
                            $obTDATDividaAcrescimo->lancarAcrescimos( Sessao::getExercicio(), $inCodInscricao, $boTransacao );
                        }
                    }else
                    if ( $rsModalidade->getCampo( "cod_forma_inscricao" ) == 3 ) { //Parcelas Individuais
                        $arParcelasDivida = $rsParcelasDivida->getElementos();
                        $arTMP = array();
                        $inTotalDeParcelas = 0;
                        for ( $inW=0; $inW<count($arParcelasDivida); $inW++ ) {
                            if ($arParcelasDivida[$inW]["valor"] <= 0.00) {
                                continue;
                            }

                            $boJaNaLista = false;
                            for ($inY=0; $inY<$inTotalDeParcelas; $inY++) {
                                if ($arTMP[$inY]["cod_parcela"] == $arParcelasDivida[$inW]["cod_parcela"]) {
                                    $boJaNaLista = true;
                                    $arTMP[$inY]["credito"][ $arTMP[$inY]["total_de_creditos"] ] = $arParcelasDivida[$inW];
                                    $arTMP[$inY]["total_de_creditos"]++;
                                    break;
                                }
                            }

                            if (!$boJaNaLista) {
                                $arTMP[$inTotalDeParcelas]["cod_parcela"] = $arParcelasDivida[$inW]["cod_parcela"];
                                $arTMP[$inTotalDeParcelas]["credito"][0] = $arParcelasDivida[$inW];
                                $arTMP[$inTotalDeParcelas]["total_de_creditos"] = 1;
                                $inTotalDeParcelas++;
                            }
                        }

                        $arListaCreditoPorParcela = $arTMP;
                        for ($inW=0; $inW<$inTotalDeParcelas; $inW++) { //uma inscricao por parcelas
                            $obTDATDividaAtiva->recuperaCodigoInscricaoComponente( $rsListaPosicao, $boTransacao );

                            $inCodInscricao = $rsListaPosicao->getCampo( "max_inscricao" )>0?$rsListaPosicao->getCampo( "max_inscricao" )+1:1;
                            $arInscricoesDA[] = $inCodInscricao;

                            unset( $obTDATPosicaoLivro );
                            $obTDATPosicaoLivro = new TDATPosicaoLivro;
                            $obTDATPosicaoLivro->recuperaPosicaoLivro( $rsListaLivro, $boTransacao );

                            $arLivros = explode( "-", $rsListaLivro->getCampo("valor") );

                            $obTDATDividaAtiva->setDado( "num_livro"            , $arLivros[1] );
                            $obTDATDividaAtiva->setDado( "num_folha"            , $arLivros[2] );
                            $obTDATDividaAtiva->setDado( "exercicio"            , Sessao::getExercicio() );
                            $obTDATDividaAtiva->setDado( "cod_inscricao"        , $inCodInscricao );
                            $obTDATDividaAtiva->setDado( "cod_autoridade"       , 1 );
                            $obTDATDividaAtiva->setDado( "numcgm_usuario"       , Sessao::read('numCgm') );
                            $obTDATDividaAtiva->setDado( "dt_inscricao"         , $stDataAtual );
                            $obTDATDividaAtiva->setDado( "dt_vencimento_origem" , $stDtVencimentoBaseBR );
                            $obTDATDividaAtiva->setDado( "exercicio_original"   , $inExercicio );
                            $obTDATDividaAtiva->setDado( "exercicio_livro"      , $arLivros[3] );
                            $obTDATDividaAtiva->inclusao( $boTransacao );

                            unset( $obTDATDividaCGM );
                            $obTDATDividaCGM = new TDATDividaCGM;
                            $obTDATDividaCGM->setDado( "exercicio"      , Sessao::getExercicio() );
                            $obTDATDividaCGM->setDado( "cod_inscricao"  , $inCodInscricao );
                            $obTDATDividaCGM->setDado( "numcgm"         , $inNumCGM );
                            $obTDATDividaCGM->inclusao( $boTransacao );

                            if ($stTipoInscricao == "i") {
                                unset( $obTDATDividaImovel );
                                $obTDATDividaImovel = new TDATDividaImovel;
                                $obTDATDividaImovel->setDado( "exercicio"           , Sessao::getExercicio() );
                                $obTDATDividaImovel->setDado( "cod_inscricao"       , $inCodInscricao );
                                $obTDATDividaImovel->setDado( "inscricao_municipal" , $inInscricao );
                                $obTDATDividaImovel->inclusao( $boTransacao );
                            }else
                                if ($stTipoInscricao == "e") {
                                    unset( $obTDATDividaEmpresa );
                                    $obTDATDividaEmpresa = new TDATDividaEmpresa;
                                    $obTDATDividaEmpresa->setDado( "exercicio"          , Sessao::getExercicio() );
                                    $obTDATDividaEmpresa->setDado( "cod_inscricao"      , $inCodInscricao );
                                    $obTDATDividaEmpresa->setDado( "inscricao_economica", $inInscricao );
                                    $obTDATDividaEmpresa->inclusao( $boTransacao );
                                }

                            unset( $obTDATParcelamento );
                            $obTDATParcelamento = new TDATParcelamento;
                            $obTDATParcelamento->recuperaNumeroParcelamento( $rsNumeroParcelamento, $boTransacao );
                            $inNumeroParcelamento = $rsNumeroParcelamento->getCampo("valor");

                            $arNumParcelamento[] = $inNumeroParcelamento;
                            $obTDATParcelamento->setDado( "num_parcelamento"    , $inNumeroParcelamento );
                            $obTDATParcelamento->setDado( "numcgm_usuario"      , Sessao::read('numCgm') );
                            $obTDATParcelamento->setDado( "cod_modalidade"      , $rsModalidade->getCampo("cod_modalidade") );
                            $obTDATParcelamento->setDado( "timestamp_modalidade", $rsModalidade->getCampo("timestamp") );
                            $obTDATParcelamento->setDado( "numero_parcelamento" , -1 );
                            $obTDATParcelamento->setDado( "exercicio"           , '-1' );
                            $obTDATParcelamento->inclusao( $boTransacao );

                            unset( $obTDATDividaParcelamento );
                            $obTDATDividaParcelamento = new TDATDividaParcelamento;
                            $obTDATDividaParcelamento->setDado( "num_parcelamento", $inNumeroParcelamento );
                            $obTDATDividaParcelamento->setDado( "exercicio"       , Sessao::getExercicio() );
                            $obTDATDividaParcelamento->setDado( "cod_inscricao"   , $inCodInscricao );
                            $obTDATDividaParcelamento->inclusao( $boTransacao );

                            unset( $obTDATDividaDocumento );
                            $obTDATDividaDocumento = new TDATDividaDocumento;
                            while ( !$rsModalidade->Eof() ) {
                                $obTDATDividaDocumento->setDado( "num_parcelamento"  , $inNumeroParcelamento );
                                $obTDATDividaDocumento->setDado( "cod_documento"     , $rsModalidade->getCampo( "cod_documento" ) );
                                $obTDATDividaDocumento->setDado( "cod_tipo_documento", $rsModalidade->getCampo( "cod_tipo_documento" ) );
                                $obTDATDividaDocumento->inclusao( $boTransacao );

                                $inTotalDocumentos++;
                                $rsModalidade->proximo();
                            }

                            $rsModalidade->setPrimeiroElemento();
                            unset( $obTDATDividaParcelaOrigem );
                            $obTDATDividaParcelaOrigem = new TDATDividaParcelaOrigem;
                            for ($inY=0; $inY<$arListaCreditoPorParcela[$inW]["total_de_creditos"]; $inY++) {
                                $obTDATDividaParcelaOrigem->setDado( "cod_parcela"      , $arListaCreditoPorParcela[$inW]["credito"][$inY]["cod_parcela" ]);
                                $obTDATDividaParcelaOrigem->setDado( "cod_especie"      , $arListaCreditoPorParcela[$inW]["credito"][$inY]["cod_especie" ]);
                                $obTDATDividaParcelaOrigem->setDado( "cod_genero"       , $arListaCreditoPorParcela[$inW]["credito"][$inY]["cod_genero"  ]);
                                $obTDATDividaParcelaOrigem->setDado( "cod_natureza"     , $arListaCreditoPorParcela[$inW]["credito"][$inY]["cod_natureza"]);
                                $obTDATDividaParcelaOrigem->setDado( "cod_credito"      , $arListaCreditoPorParcela[$inW]["credito"][$inY]["cod_credito" ]);
                                $obTDATDividaParcelaOrigem->setDado( "num_parcelamento" , $inNumeroParcelamento                                           );
                                $obTDATDividaParcelaOrigem->setDado( "valor"            , $arListaCreditoPorParcela[$inW]["credito"][$inY]["valor"       ]);
                                $obTDATDividaParcelaOrigem->inclusao( $boTransacao );
                            }

                            unset( $obTDATDividaAcrescimo );
                            $obTDATDividaAcrescimo = new TDATDividaAcrescimo;
                            $obTDATDividaAcrescimo->lancarAcrescimos( Sessao::getExercicio(), $inCodInscricao, $boTransacao );
                        }//for ($inW=0; $inW<$inTotalDeParcelas; $inW++) { //uma inscricao por parcelas
                    }else
                    if ( $rsModalidade->getCampo( "cod_forma_inscricao" ) == 2 ) { //Valor Total Por Crédito
                        $arParcelasDivida = $rsParcelasDivida->getElementos();
                        $arTMP = array();
                        $inTotalDeCreditos = 0;
                        for ( $inW=0; $inW<count($arParcelasDivida); $inW++ ) {
                            if ($arParcelasDivida[$inW]["valor"] <= 0.00) {
                                continue;
                            }

                            $boJaNaLista = false;
                            for ($inY=0; $inY<$inTotalDeCreditos; $inY++) {
                                if ($arTMP[$inY]["cod_credito" ] == $arParcelasDivida[$inW]["cod_credito" ] &&
                                    $arTMP[$inY]["cod_natureza"] == $arParcelasDivida[$inW]["cod_natureza"] &&
                                    $arTMP[$inY]["cod_genero"  ] == $arParcelasDivida[$inW]["cod_genero"  ] &&
                                    $arTMP[$inY]["cod_especie" ] == $arParcelasDivida[$inW]["cod_especie" ]
                                ) {
                                    $boJaNaLista = true;
                                    $arTMP[$inY]["credito"][ $arTMP[$inY]["total_de_creditos"] ] = $arParcelasDivida[$inW];
                                    $arTMP[$inY]["total_de_creditos"]++;
                                    break;
                                }
                            }

                            if (!$boJaNaLista) {
                                $arTMP[$inTotalDeCreditos]["cod_credito"] = $arParcelasDivida[$inW]["cod_credito"];
                                $arTMP[$inTotalDeCreditos]["cod_natureza"] = $arParcelasDivida[$inW]["cod_natureza"];
                                $arTMP[$inTotalDeCreditos]["cod_genero"] = $arParcelasDivida[$inW]["cod_genero"];
                                $arTMP[$inTotalDeCreditos]["cod_especie"] = $arParcelasDivida[$inW]["cod_especie"];
                                $arTMP[$inTotalDeCreditos]["credito"][0] = $arParcelasDivida[$inW];
                                $arTMP[$inTotalDeCreditos]["total_de_creditos"] = 1;
                                $inTotalDeCreditos++;
                            }
                        }

                        $arListaParcelasPorCredito = $arTMP;
                        for ($inW=0; $inW<$inTotalDeCreditos; $inW++) { //uma inscricao por credito
                            $obTDATDividaAtiva->recuperaCodigoInscricaoComponente( $rsListaPosicao, $boTransacao );

                            $inCodInscricao = $rsListaPosicao->getCampo( "max_inscricao" )>0?$rsListaPosicao->getCampo( "max_inscricao" )+1:1;
                            $arInscricoesDA[] = $inCodInscricao;

                            unset( $obTDATPosicaoLivro );
                            $obTDATPosicaoLivro = new TDATPosicaoLivro;
                            $obTDATPosicaoLivro->recuperaPosicaoLivro( $rsListaLivro, $boTransacao );

                            $arLivros = explode( "-", $rsListaLivro->getCampo("valor") );

                            $obTDATDividaAtiva->setDado( "num_livro"            , $arLivros[1] );
                            $obTDATDividaAtiva->setDado( "num_folha"            , $arLivros[2] );
                            $obTDATDividaAtiva->setDado( "exercicio"            , Sessao::getExercicio() );
                            $obTDATDividaAtiva->setDado( "cod_inscricao"        , $inCodInscricao );
                            $obTDATDividaAtiva->setDado( "cod_autoridade"       , 1 );
                            $obTDATDividaAtiva->setDado( "numcgm_usuario"       , Sessao::read('numCgm') );
                            $obTDATDividaAtiva->setDado( "dt_inscricao"         , $stDataAtual );
                            $obTDATDividaAtiva->setDado( "dt_vencimento_origem" , $stDtVencimentoBaseBR );
                            $obTDATDividaAtiva->setDado( "exercicio_original"   , $inExercicio );
                            $obTDATDividaAtiva->setDado( "exercicio_livro"      , $arLivros[3] );
                            $obTDATDividaAtiva->inclusao( $boTransacao );

                            unset( $obTDATDividaCGM );
                            $obTDATDividaCGM = new TDATDividaCGM;
                            $obTDATDividaCGM->setDado( "exercicio"      , Sessao::getExercicio() );
                            $obTDATDividaCGM->setDado( "cod_inscricao"  , $inCodInscricao );
                            $obTDATDividaCGM->setDado( "numcgm"         , $inNumCGM );
                            $obTDATDividaCGM->inclusao( $boTransacao );

                            if ($stTipoInscricao == "i") {
                                unset( $obTDATDividaImovel );
                                $obTDATDividaImovel = new TDATDividaImovel;
                                $obTDATDividaImovel->setDado( "exercicio"           , Sessao::getExercicio() );
                                $obTDATDividaImovel->setDado( "cod_inscricao"       , $inCodInscricao );
                                $obTDATDividaImovel->setDado( "inscricao_municipal" , $inInscricao );
                                $obTDATDividaImovel->inclusao( $boTransacao );
                            }else
                                if ($stTipoInscricao == "e") {
                                    unset( $obTDATDividaEmpresa );
                                    $obTDATDividaEmpresa = new TDATDividaEmpresa;
                                    $obTDATDividaEmpresa->setDado( "exercicio"          , Sessao::getExercicio() );
                                    $obTDATDividaEmpresa->setDado( "cod_inscricao"      , $inCodInscricao );
                                    $obTDATDividaEmpresa->setDado( "inscricao_economica", $inInscricao );
                                    $obTDATDividaEmpresa->inclusao( $boTransacao );
                                }

                            unset( $obTDATParcelamento );
                            $obTDATParcelamento = new TDATParcelamento;
                            $obTDATParcelamento->recuperaNumeroParcelamento( $rsNumeroParcelamento, $boTransacao );
                            $inNumeroParcelamento = $rsNumeroParcelamento->getCampo("valor");
                            $arNumParcelamento[] = $inNumeroParcelamento;
                            $obTDATParcelamento->setDado( "num_parcelamento"    , $inNumeroParcelamento );
                            $obTDATParcelamento->setDado( "numcgm_usuario"      , Sessao::read('numCgm'));
                            $obTDATParcelamento->setDado( "cod_modalidade"      , $rsModalidade->getCampo( "cod_modalidade" ) );
                            $obTDATParcelamento->setDado( "timestamp_modalidade", $rsModalidade->getCampo( "timestamp" ) );
                            $obTDATParcelamento->setDado( "numero_parcelamento" , -1 );
                            $obTDATParcelamento->setDado( "exercicio"           ,'-1' );
                            $obTDATParcelamento->inclusao( $boTransacao );

                            unset( $obTDATDividaParcelamento );
                            $obTDATDividaParcelamento = new TDATDividaParcelamento;
                            $obTDATDividaParcelamento->setDado( "num_parcelamento"  , $inNumeroParcelamento );
                            $obTDATDividaParcelamento->setDado( "exercicio"         , Sessao::getExercicio() );
                            $obTDATDividaParcelamento->setDado( "cod_inscricao"     , $inCodInscricao );
                            $obTDATDividaParcelamento->inclusao( $boTransacao );

                            unset( $obTDATDividaDocumento );
                            $obTDATDividaDocumento = new TDATDividaDocumento;
                            while ( !$rsModalidade->Eof() ) {
                                $obTDATDividaDocumento->setDado( "num_parcelamento"     , $inNumeroParcelamento );
                                $obTDATDividaDocumento->setDado( "cod_documento"        , $rsModalidade->getCampo( "cod_documento" ) );
                                $obTDATDividaDocumento->setDado( "cod_tipo_documento"   , $rsModalidade->getCampo( "cod_tipo_documento" ) );
                                $obTDATDividaDocumento->inclusao( $boTransacao );

                                $inTotalDocumentos++;
                                $rsModalidade->proximo();
                            }

                            $rsModalidade->setPrimeiroElemento();
                            unset( $obTDATDividaParcelaOrigem );
                            $obTDATDividaParcelaOrigem = new TDATDividaParcelaOrigem;
                            for ($inY=0; $inY<$arListaParcelasPorCredito[$inW]["total_de_creditos"]; $inY++) {
                                $obTDATDividaParcelaOrigem->setDado( "cod_parcela"      , $arListaParcelasPorCredito[$inW]["credito"][$inY]["cod_parcela"] );
                                $obTDATDividaParcelaOrigem->setDado( "cod_especie"      , $arListaParcelasPorCredito[$inW]["cod_especie"] );
                                $obTDATDividaParcelaOrigem->setDado( "cod_genero"       , $arListaParcelasPorCredito[$inW]["cod_genero"] );
                                $obTDATDividaParcelaOrigem->setDado( "cod_natureza"     , $arListaParcelasPorCredito[$inW]["cod_natureza"] );
                                $obTDATDividaParcelaOrigem->setDado( "cod_credito"      , $arListaParcelasPorCredito[$inW]["cod_credito"] );
                                $obTDATDividaParcelaOrigem->setDado( "num_parcelamento" , $inNumeroParcelamento );
                                $obTDATDividaParcelaOrigem->setDado( "valor"            , $arListaParcelasPorCredito[$inW]["credito"][$inY]["valor"] );
                                $obTDATDividaParcelaOrigem->inclusao( $boTransacao );
                            }

                            unset( $obTDATDividaAcrescimo );
                            $obTDATDividaAcrescimo = new TDATDividaAcrescimo;
                            $obTDATDividaAcrescimo->lancarAcrescimos( Sessao::getExercicio(), $inCodInscricao, $boTransacao );
                        } //for ($inW=0; $inW<$inTotalDeCreditos; $inW++) { //uma inscricao por credito
                    }else
                    if ( $rsModalidade->getCampo( "cod_forma_inscricao" ) == 1 ) { //Valor Total
                        $obTDATDividaAtiva->recuperaCodigoInscricaoComponente( $rsListaPosicao, $boTransacao );
                        $inCodInscricao = $rsListaPosicao->getCampo( "max_inscricao" )>0?$rsListaPosicao->getCampo( "max_inscricao" )+1:1;
                        $arInscricoesDA[] = $inCodInscricao;

                        unset( $obTDATPosicaoLivro );
                        $obTDATPosicaoLivro = new TDATPosicaoLivro;
                        $obTDATPosicaoLivro->recuperaPosicaoLivro( $rsListaLivro, $boTransacao );

                        $arLivros = explode( "-", $rsListaLivro->getCampo("valor") );

                        $obTDATDividaAtiva->setDado( "num_livro"            , $arLivros[1] );
                        $obTDATDividaAtiva->setDado( "num_folha"            , $arLivros[2] );
                        $obTDATDividaAtiva->setDado( "exercicio"            , Sessao::getExercicio() );
                        $obTDATDividaAtiva->setDado( "cod_inscricao"        , $inCodInscricao );
                        $obTDATDividaAtiva->setDado( "cod_autoridade"       , 1 );
                        $obTDATDividaAtiva->setDado( "numcgm_usuario"       , Sessao::read('numCgm') );
                        $obTDATDividaAtiva->setDado( "dt_inscricao"         , $stDataAtual );
                        $obTDATDividaAtiva->setDado( "dt_vencimento_origem" , $stDtVencimentoBaseBR );
                        $obTDATDividaAtiva->setDado( "exercicio_original"   , $inExercicio );
                        $obTDATDividaAtiva->setDado( "exercicio_livro"      , $arLivros[3] );
                        $obTDATDividaAtiva->inclusao( $boTransacao );

                        unset( $obTDATDividaCGM );
                        $obTDATDividaCGM = new TDATDividaCGM;
                        $obTDATDividaCGM->setDado( "exercicio"      , Sessao::getExercicio() );
                        $obTDATDividaCGM->setDado( "cod_inscricao"  , $inCodInscricao );
                        $obTDATDividaCGM->setDado( "numcgm"         , $inNumCGM );
                        $obTDATDividaCGM->inclusao( $boTransacao );

                        if ($stTipoInscricao == "i") {
                            unset( $obTDATDividaImovel );
                            $obTDATDividaImovel = new TDATDividaImovel;
                            $obTDATDividaImovel->setDado( "exercicio"           , Sessao::getExercicio() );
                            $obTDATDividaImovel->setDado( "cod_inscricao"       , $inCodInscricao );
                            $obTDATDividaImovel->setDado( "inscricao_municipal" , $inInscricao );
                            $obTDATDividaImovel->inclusao( $boTransacao );
                        }else
                            if ($stTipoInscricao == "e") {
                                unset( $obTDATDividaEmpresa );
                                $obTDATDividaEmpresa = new TDATDividaEmpresa;
                                $obTDATDividaEmpresa->setDado( "exercicio"          , Sessao::getExercicio() );
                                $obTDATDividaEmpresa->setDado( "cod_inscricao"      , $inCodInscricao );
                                $obTDATDividaEmpresa->setDado( "inscricao_economica", $inInscricao );
                                $obTDATDividaEmpresa->inclusao( $boTransacao );
                            }

                        unset( $obTDATParcelamento );
                        $obTDATParcelamento = new TDATParcelamento;
                        $obTDATParcelamento->recuperaNumeroParcelamento( $rsNumeroParcelamento, $boTransacao );
                        $inNumeroParcelamento = $rsNumeroParcelamento->getCampo("valor");
                        $arNumParcelamento[] = $inNumeroParcelamento;
                        $obTDATParcelamento->setDado( "num_parcelamento"        , $inNumeroParcelamento );
                        $obTDATParcelamento->setDado( "numcgm_usuario"          , Sessao::read('numCgm') );
                        $obTDATParcelamento->setDado( "cod_modalidade"          , $rsModalidade->getCampo( "cod_modalidade" ) );
                        $obTDATParcelamento->setDado( "timestamp_modalidade"    , $rsModalidade->getCampo( "timestamp" ) );
                        $obTDATParcelamento->setDado( "numero_parcelamento"     , -1 );
                        $obTDATParcelamento->setDado( "exercicio"               , '-1' );
                        $obTDATParcelamento->inclusao( $boTransacao );

                        unset( $obTDATDividaParcelamento );
                        $obTDATDividaParcelamento = new TDATDividaParcelamento;
                        $obTDATDividaParcelamento->setDado( "num_parcelamento", $inNumeroParcelamento );
                        $obTDATDividaParcelamento->setDado( "exercicio"       , Sessao::getExercicio() );
                        $obTDATDividaParcelamento->setDado( "cod_inscricao"   , $inCodInscricao );
                        $obTDATDividaParcelamento->inclusao( $boTransacao );

                        unset( $obTDATDividaDocumento );
                        $obTDATDividaDocumento = new TDATDividaDocumento;
                        while ( !$rsModalidade->Eof() ) {
                            $obTDATDividaDocumento->setDado( "num_parcelamento"  , $inNumeroParcelamento );
                            $obTDATDividaDocumento->setDado( "cod_documento"     , $rsModalidade->getCampo( "cod_documento" ) );
                            $obTDATDividaDocumento->setDado( "cod_tipo_documento", $rsModalidade->getCampo( "cod_tipo_documento" ) );
                            $obTDATDividaDocumento->inclusao( $boTransacao );

                            $inTotalDocumentos++;
                            $rsModalidade->proximo();
                        }

                        $rsModalidade->setPrimeiroElemento();
                        unset( $obTDATDividaParcelaOrigem );
                        $obTDATDividaParcelaOrigem = new TDATDividaParcelaOrigem;
                        while ( !$rsParcelasDivida->Eof() ) {
                            if ( $rsParcelasDivida->getCampo("valor") > 0.00 ) {
                                $obTDATDividaParcelaOrigem->setDado( "cod_parcela"      , $rsParcelasDivida->getCampo("cod_parcela" ));
                                $obTDATDividaParcelaOrigem->setDado( "cod_especie"      , $rsParcelasDivida->getCampo("cod_especie" ));
                                $obTDATDividaParcelaOrigem->setDado( "cod_genero"       , $rsParcelasDivida->getCampo("cod_genero"  ));
                                $obTDATDividaParcelaOrigem->setDado( "cod_natureza"     , $rsParcelasDivida->getCampo("cod_natureza"));
                                $obTDATDividaParcelaOrigem->setDado( "cod_credito"      , $rsParcelasDivida->getCampo("cod_credito" ));
                                $obTDATDividaParcelaOrigem->setDado( "num_parcelamento" , $inNumeroParcelamento );
                                $obTDATDividaParcelaOrigem->setDado( "valor"            , $rsParcelasDivida->getCampo("valor"));
                                $obTDATDividaParcelaOrigem->inclusao( $boTransacao );
                            }

                            $rsParcelasDivida->proximo();
                        }

                        unset( $obTDATDividaAcrescimo );
                        $obTDATDividaAcrescimo = new TDATDividaAcrescimo;
                        $obTDATDividaAcrescimo->lancarAcrescimos( Sessao::getExercicio(), $inCodInscricao, $boTransacao );
                    } //if ( $sessao->transf4["modalidade"][0]["cod_forma_inscricao"] == 1 )  //Valor Total

                    $obTDATRemissaoProcesso = new TDATRemissaoProcesso;
                    for ( $inInscricaoDA=0; $inInscricaoDA<count($arInscricoesDA); $inInscricaoDA++ ) {
                        $obTDATDividaRemissao->setDado( 'cod_inscricao', $arInscricoesDA[$inInscricaoDA] );
                        $obTDATDividaRemissao->setDado( 'exercicio', Sessao::getExercicio() );
                        $obTDATDividaRemissao->setDado( 'cod_norma', $inCodNorma );
                        $obTDATDividaRemissao->setDado( 'numcgm', Sessao::read('numCgm') );
                        $obTDATDividaRemissao->setDado( 'dt_remissao', $stDataAtual );
                        $obTDATDividaRemissao->setDado( 'observacao', $stObservacao );
                        $obTDATDividaRemissao->inclusao( $boTransacao );

                        if ($inProcesso) {
                            $obTDATRemissaoProcesso->setDado( 'cod_inscricao', $arInscricoesDA[$inInscricaoDA] );
                            $obTDATRemissaoProcesso->setDado( 'exercicio', Sessao::getExercicio() );
                            $obTDATRemissaoProcesso->setDado( 'cod_processo', $arProcesso[0] );
                            $obTDATRemissaoProcesso->setDado( 'ano_exercicio', $arProcesso[1] );
                            $obTDATRemissaoProcesso->inclusao( $boTransacao );
                        }
                    }

                    //adicionar aqui documentos da remissao (falta apenas descobrir os num_parcelamentos!!
                    $obTDATDividaDocumento = new TDATDividaDocumento;
                    while ( !$rsDocumentos->Eof() ) {
                        for ( $inX=0; $inX<count($arNumParcelamento); $inX++ ) {
                            $obTDATDividaDocumento->setDado( "num_parcelamento"  , $arNumParcelamento[$inX]["num_parcelamento"] );
                            $obTDATDividaDocumento->setDado( "cod_documento"     , $rsDocumentos->getCampo( "cod_documento" ) );
                            $obTDATDividaDocumento->setDado( "cod_tipo_documento", $rsDocumentos->getCampo( "cod_tipo_documento" ) );
                            $obTDATDividaDocumento->inclusao( $boTransacao );
                        }

                        $rsDocumentos->proximo();
                    }
                } else { //if ($stTipoInscricao != "d") { //inscreve primeiro na divida ativa
                    $stSql = "
                      SELECT DISTINCT divida_parcelamento.cod_inscricao
                           , divida_parcelamento.exercicio
                        FROM divida.divida_parcelamento
                   LEFT JOIN divida.divida_remissao
                          ON divida_remissao.cod_inscricao = divida_parcelamento.cod_inscricao
                         AND divida_remissao.exercicio = divida_parcelamento.exercicio
                  INNER JOIN ( SELECT min(parcela_origem.num_parcelamento) AS num_parcelamento
                                    , parcela.cod_lancamento
                                 FROM divida.parcela_origem
                           INNER JOIN divida.divida_parcelamento
                                   ON divida_parcelamento.num_parcelamento = parcela_origem.num_parcelamento
                           INNER JOIN arrecadacao.parcela
                                   ON parcela.cod_parcela = parcela_origem.cod_parcela
                             GROUP BY parcela.cod_lancamento
                             )AS parcela_origem
                          ON parcela_origem.num_parcelamento = divida_parcelamento.num_parcelamento
                  INNER JOIN ( SELECT divida_parcelamento.cod_inscricao
                                    , divida_parcelamento.exercicio
                                 FROM divida.parcela_origem
                           INNER JOIN divida.parcelamento
                                   ON parcelamento.num_parcelamento = parcela_origem.num_parcelamento
                           INNER JOIN divida.divida_parcelamento
                                   ON divida_parcelamento.num_parcelamento = parcelamento.num_parcelamento
                           INNER JOIN divida.modalidade_vigencia
                                   ON parcelamento.cod_modalidade = modalidade_vigencia.cod_modalidade
                                  AND parcelamento.timestamp_modalidade = modalidade_vigencia.timestamp
                           INNER JOIN arrecadacao.parcela
                                   ON parcela_origem.cod_parcela = parcela.cod_parcela
                                WHERE modalidade_vigencia.cod_forma_inscricao IN (2,4) --deve funcionar somente para as formas 2 e 4
                                  AND ( ".$stFiltroCredito." )
                             GROUP BY divida_parcelamento.cod_inscricao
                                    , divida_parcelamento.exercicio
                              ) as valida
                          ON valida.cod_inscricao = divida_parcelamento.cod_inscricao
                         AND valida.exercicio = divida_parcelamento.exercicio
                       WHERE divida_remissao.cod_inscricao IS NULL
                         AND parcela_origem.cod_lancamento =  ".$inCodLancamento;

                    unset( $rsRecordSet );
                    $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

                    while ( !$rsRecordSet->Eof() ) {
                        $obTDATDividaRemissao->setDado( 'cod_inscricao', $rsRecordSet->getCampo("cod_inscricao") );
                        $obTDATDividaRemissao->setDado( 'exercicio', $rsRecordSet->getCampo("exercicio") );
                        $obTDATDividaRemissao->setDado( 'cod_norma', $inCodNorma );
                        $obTDATDividaRemissao->setDado( 'numcgm', Sessao::read('numCgm') );
                        $obTDATDividaRemissao->setDado( 'dt_remissao', $stDataAtual );
                        $obTDATDividaRemissao->setDado( 'observacao', $stObservacao );
                        $obTDATDividaRemissao->inclusao( $boTransacao );

                        if ($inProcesso) {
                            $obTDATRemissaoProcesso->setDado( 'cod_inscricao', $arInscricoesDA[$inInscricaoDA] );
                            $obTDATRemissaoProcesso->setDado( 'exercicio', Sessao::getExercicio() );
                            $obTDATRemissaoProcesso->setDado( 'cod_processo', $arProcesso[0] );
                            $obTDATRemissaoProcesso->setDado( 'ano_exercicio', $arProcesso[1] );
                            $obTDATRemissaoProcesso->inclusao( $boTransacao );
                        }

                        $rsRecordSet->proximo();
                    }

                    //adicionar aki documentos da divida
                    $stSql = "
                        SELECT DISTINCT divida_parcelamento.num_parcelamento
                          FROM divida.divida_parcelamento
                     LEFT JOIN divida.divida_remissao
                            ON divida_remissao.cod_inscricao = divida_parcelamento.cod_inscricao
                           AND divida_remissao.exercicio = divida_parcelamento.exercicio
                    INNER JOIN ( SELECT min(parcela_origem.num_parcelamento) AS num_parcelamento
                                      , parcela.cod_lancamento
                                   FROM divida.parcela_origem
                             INNER JOIN divida.divida_parcelamento
                                     ON divida_parcelamento.num_parcelamento = parcela_origem.num_parcelamento
                             INNER JOIN arrecadacao.parcela
                                     ON parcela.cod_parcela = parcela_origem.cod_parcela
                               GROUP BY parcela.cod_lancamento
                               )AS parcela_origem
                            ON parcela_origem.num_parcelamento = divida_parcelamento.num_parcelamento
                         WHERE divida_remissao.cod_inscricao IS NULL
                           AND parcela_origem.cod_lancamento = ".$inCodLancamento;

                    unset( $rsRecordSet );
                    $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

                    $obTDATDividaDocumento = new TDATDividaDocumento;
                    while ( !$rsDocumentos->Eof() ) {
                        $rsRecordSet->setPrimeiroElemento();
                        while ( !$rsRecordSet->Eof() ) {
                            $obTDATDividaDocumento->setDado( "num_parcelamento"  , $rsRecordSet->getCampo("num_parcelamento") );
                            $obTDATDividaDocumento->setDado( "cod_documento"     , $rsDocumentos->getCampo( "cod_documento" ) );
                            $obTDATDividaDocumento->setDado( "cod_tipo_documento", $rsDocumentos->getCampo( "cod_tipo_documento" ) );
                            $obTDATDividaDocumento->inclusao( $boTransacao );

                            $rsRecordSet->proximo();
                        }

                        $rsDocumentos->proximo();
                    }
                }
            }
        }

        $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro );
    }

    Sessao::write( "dadosRemissao", $arDadosSessao );
    Sessao::write('arListaGrupoCredito', $arListaGrupoCreditoSessao);
    Sessao::write( "InscricoesRemir", $inInscricoesRemir );

SistemaLegado::LiberaFrames();

$pgFormRelatorioExecucao = "FMConcederRemissaoRelatorio.php";
echo "<script type=\"text/javascript\">\r\n";
echo "    var sAux = window.open('".$pgFormRelatorioExecucao."?".Sessao::getId()."&stAcao=".$_REQUEST["stAcao"]."&inCodNorma=".$_REQUEST['inCodNorma']."','','width=20,height=10,resizable=1,scrollbars=1,left=100,top=100');\r\n";
echo "    eval(sAux)\r\n";
echo "</script>\r\n";

SistemaLegado::alertaAviso( $pgFilt, "Remissão de Dívida Ativa", "incluir", "aviso", Sessao::getId(), "../" );
