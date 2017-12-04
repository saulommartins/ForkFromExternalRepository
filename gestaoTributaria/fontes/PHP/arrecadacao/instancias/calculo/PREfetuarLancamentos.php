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
    * Página de processamento para calculo
    * Data de criação : 01/11/2005

    * @author Analista: Fabio Bertold Rodrigues
    * @author Programador: Marcelo Boezzio Paulino

    * $Id: PREfetuarLancamentos.php 63292 2015-08-13 13:57:29Z arthur $

    Caso de uso: uc-05.03.05
**/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GT_ARR_NEGOCIO."RARRParametroCalculo.class.php"                                     );
include_once( CAM_GT_ARR_NEGOCIO."RARRLancamento.class.php"                                           );
include_once( CAM_GT_ARR_NEGOCIO."RARRCalculo.class.php"                                              );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRLancamento.class.php" );

$stAcao = $request->get('stAcao');

//Define o nome dos arquivos PHP
$stPrograma    = "EfetuarLancamentos";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgForm        = "FM".$stPrograma.".php";
$pgFormManual  = "FM".$stPrograma."Manual.php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJs          = "JS".$stPrograma.".js";
$pgFormRelatorioExecucaoLancamento = "FMRelatorioExecucaoLancamento.php";

$obErro  = new Erro;
$obRegra = new RARRParametroCalculo;

if ( $_REQUEST['boLancar'] == 1)
    $stAcao = 'incluir';
else if ( Sessao::read( "grupo_automatico" ) ) {
    $stAcao = 'lancamentoAutomaticoGeral';
}

switch ($stAcao) {
    case "lancar_calculos":
        if ($request->get('inCodGrupo')) {
            $obConexao   = new Conexao;
            //---------------------------
            list( $inCodGrupo , $inExercicio ) = explode( '/' , $request->get('inCodGrupo') );
            $inInscricaoInicial = 1000000;
            $inInscricaoFinal = 0;
            $nome_arquivo = Sessao::read( 'arquivo_calculos_lancamentos' );
            if ($nome_arquivo) {
                if ( $arquivo = fopen ( $nome_arquivo, 'r' ) ) {
                    $arCalculo = array ();
                    $arDados = array();
                    while (!feof($arquivo)) {
                        if ($stLinha = fgets($arquivo)) {
                            $arLinha = explode ('&', $stLinha);
                            $arDados[] = $arLinha[2];
                        }
                    }

                    fclose( $arquivo );

                    sort( $arDados, SORT_NUMERIC );

                    $inInscricaoFinal = $arDados[count( $arDados )-1];
                    $inInscricaoInicial = $arDados[0];
                }
            }

            $stVencimento = "";
            $stTipoDesconto = "";
            $stValorDesconto = "";
            $stVencimentoDesconto = "";
            $stNumeroParcela = "";
            if ( !Sessao::read( "UsaCalendarioFiscal" ) ) {
                $arParcelasSessao = Sessao::read( "parcelas" );
                $inQtdParc = count( $arParcelasSessao );
                for ( $inX=0; $inX<count( $arParcelasSessao ); $inX++ ) {
                    if ($inX > 0) {
                        $stVencimento .= ";";
                        $stTipoDesconto .= ";";
                        $stValorDesconto .= ";";
                        $stVencimentoDesconto .= ";";
                        $stNumeroParcela .= ";";
                    }

                    $arTMPVenc = explode( "/", $arParcelasSessao[$inX]["data_vencimento"] );
                    $stVencimento .= $arTMPVenc[2]."-".$arTMPVenc[1]."-".$arTMPVenc[0];
                    $stVencimentoDesconto .= $arTMPVenc[2]."-".$arTMPVenc[1]."-".$arTMPVenc[0];

                    $stValorDesconto .= (double) $arParcelasSessao[$inX]["valor"];

                    if ($arParcelasSessao[$inX]["stTipoDesconto"] == "Percentual") {
                        $stTipoDesconto .= "true";
                    }else
                        $stTipoDesconto .= "false";

                    if ($arParcelasSessao[$inX]["stTipoParcela"] == "Única") {
                        $stNumeroParcela .= "0";
                    }else
                        $stNumeroParcela .= $arParcelasSessao[$inX]["stTipoParcela"];
                }
            } else {
                $inQtdParc = -1;
            }

            $stSql = " SELECT
                            CASE WHEN cod_modulo = 12 THEN
                                1
                            ELSE
                                CASE WHEN cod_modulo = 14 THEN
                                    2
                                ELSE
                                    3
                                END
                            END AS tipo
                        FROM
                            arrecadacao.grupo_credito
                        WHERE cod_grupo = ".$inCodGrupo." AND ano_exercicio = ".$inExercicio;

            $obErro = $obConexao->executaSQL( $rsTipo, $stSql, $boTransacao );

            $stSql = " SELECT arrecadacao.fn_lancamento_manual_por_grupo_intervalo( ".$inCodGrupo.", ".$inExercicio.", '".$inInscricaoInicial." AND ".$inInscricaoFinal."', ".$inQtdParc.", '".$stVencimento."', '".$stTipoDesconto."', '".$stValorDesconto."', '".$stVencimentoDesconto."', '".$stNumeroParcela."', ".$rsTipo->getCampo("tipo")." )  AS resultado;";
            $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

            if ( !$rsRecordSet->Eof() )
                Sessao::write( "lancamentos_cods", $rsRecordSet->getCampo("resultado") );

            if ( ( ( $_REQUEST["emissao_carnes"] == "local" ) || ($_REQUEST["boCarne"] == "sim" ) ) && !$obErro->ocorreu() ) {
                $inCodLancamentoAtual = Sessao::read( "lancamentos_cods" );
                $inCodLancamentoAtual = substr ( $inCodLancamentoAtual, 0, strlen( $inCodLancamentoAtual - 1) );
                $obRARRLancamento->obRARRCarne->obRARRParcela->roRARRLancamento->inCodLancamento = $inCodLancamentoAtual;

                include_once 'PREmitirCarneLancManual.php';
                exit;
            }

            if (!$obErro->ocorreu() ) {
                $stPag = $pgFormRelatorioExecucaoLancamento."?stAcao=incluir&stTipoCalculo=".$_REQUEST["stTipoCalculo"]."&inCodGrupo=".$_REQUEST["inCodGrupo"]."&inCodCredito=".$_REQUEST["inCodCredito"];
                if ($_REQUEST["inCodGrupo"]) {
                    SistemaLegado::alertaAviso($stPag,"Codigo do Grupo:".$_REQUEST["inCodGrupo"],"incluir","aviso", Sessao::getId(), "../");
                } else {
                    SistemaLegado::alertaAviso($stPag,"Codigo do Crédito:".$_REQUEST["inCodCredito"],"incluir","aviso", Sessao::getId(), "../");
                }
            } else {
                SistemaLegado::exibeAviso( urlencode( $obErro->getDescricao()),"n_erro","erro",Sessao::getId(), "../" );
            }
        }
        break;

    case "lancamentoAutomaticoGeral":
    case "lancamentoAutomatico":
        SistemaLegado::BloqueiaFrames();
            $obTARRLancamento = new TARRLancamento;

        list( $inCodGrupo , $inExercicio ) = explode( '/' , $_REQUEST[ 'inCodGrupo' ] );

        $obRARRCalendarioFiscal = new RARRCalendarioFiscal ();
        $obRARRCalendarioFiscal->setCodigoGrupo($inCodGrupo);
        $obRARRCalendarioFiscal->setAnoExercicio($inExercicio);
        $obRARRCalendarioFiscal->listarCalendario($rsCalendarioFiscal);
        if ( $rsCalendarioFiscal->getNumLinhas() < 0 ) {
            SistemaLegado::LiberaFrames();
            SistemaLegado::exibeAviso( urlencode("O Grupo de Créditos selecionado não possui Calendário Fiscal configurado!"), "n_erro", "erro", Sessao::getId(), "../" );
        } else {
            if ( !Sessao::read( "grupo_automatico" ) ) {
                Sessao::write( "grupo_automatico", $_REQUEST["inCodGrupo"] );
            }

            $arDadosGrupoCredito = explode( "/", Sessao::read( "grupo_automatico" ) );
            $obErro = $obTARRLancamento->lancamentoAutomatico( $arDadosGrupoCredito[0], $arDadosGrupoCredito[1] );

            SistemaLegado::LiberaFrames();
            if ( $obErro->ocorreu() ) {
                SistemaLegado::exibeAviso( urlencode($obErro->getDescricao()), "n_erro", "erro", Sessao::getId(), "../" );
            } else {
                $pgListaSituacao = "LSManterLancamentoSituacao.php?stAcao=incluir&stTipoCalculo=".$_REQUEST["stTipoCalculo"]."&inCodGrupo=".Sessao::read( "grupo_automatico" );
                SistemaLegado::alertaAviso( $pgListaSituacao, "Código do Grupo:".Sessao::read( "grupo_automatico" ), "incluir", "aviso", Sessao::getId(), "../" );
            }
        }
        break;

    case "lanc_via_relat_exec":
        if ($_REQUEST["inCodGrupo"]) {
            $obConexao = new Conexao;
            $arTMP = explode( "/", $_REQUEST["inCodGrupo"] );
            $inCodGrupo = $arTMP[0];
            $inExercicio = $arTMP[1];

            $stInscricoes = "";
            if ( Sessao::read( 'arquivo_calculos_lancamentos' ) ) {
                $nome_arquivo = Sessao::read( 'arquivo_calculos_lancamentos' );
                if ( $arquivo = fopen ( $nome_arquivo, 'r' ) ) {
                    $arCalculo = array ();
                    $arDados = array();
                    while (!feof($arquivo)) {
                        if ($stLinha = fgets($arquivo)) {
                            $arLinha = explode ('&', $stLinha);
                            if ( !in_array( $arLinha[2], $arDados ) ) {
                                $arDados[] = $arLinha[2];
                                if ( $stInscricoes )
                                    $stInscricoes .= ",";

                                $stInscricoes .= $arLinha[2];
                            }
                        }
                    }

                    fclose( $arquivo );
                }
            }

            $stVencimento = "";
            $stTipoDesconto = "";
            $stValorDesconto = "";
            $stVencimentoDesconto = "";
            $stNumeroParcela = "";
            if ( !Sessao::read( "UsaCalendarioFiscal" ) ) {
                $arSessaoParcelas = Sessao::read( "parcelas" );
                $inQtdParc = count( $arSessaoParcelas );
                for ( $inX=0; $inX<count( $arSessaoParcelas ); $inX++ ) {
                    if ($inX > 0) {
                        $stVencimento .= ";";
                        $stTipoDesconto .= ";";
                        $stValorDesconto .= ";";
                        $stVencimentoDesconto .= ";";
                        $stNumeroParcela .= ";";
                    }

                    $arTMPVenc = explode( "/", $arSessaoParcelas[$inX]["data_vencimento"] );
                    $stVencimento .= $arTMPVenc[2]."-".$arTMPVenc[1]."-".$arTMPVenc[0];
                    $stVencimentoDesconto .= $arTMPVenc[2]."-".$arTMPVenc[1]."-".$arTMPVenc[0];

                    $stValorDesconto .= (double) $arSessaoParcelas[$inX]["valor"];

                    if ($arSessaoParcelas[$inX]["stTipoDesconto"] == "Percentual") {
                        $stTipoDesconto .= "true";
                    }else
                        $stTipoDesconto .= "false";

                    if ($arSessaoParcelas[$inX]["stTipoParcela"] == "Única") {
                        $stNumeroParcela .= "0";
                    }else
                        $stNumeroParcela .= $arSessaoParcelas[$inX]["stTipoParcela"];
                }
            } else {
                $inQtdParc = -1;
            }

            $stSql = " SELECT
                            CASE WHEN cod_modulo = 12 THEN
                                1
                            ELSE
                                CASE WHEN cod_modulo = 14 THEN
                                    2
                                ELSE
                                    3
                                END
                            END AS tipo
                        FROM
                            arrecadacao.grupo_credito
                        WHERE cod_grupo = ".$inCodGrupo." AND ano_exercicio = ".$inExercicio;

            $obErro = $obConexao->executaSQL( $rsTipo, $stSql, $boTransacao );
            if ( $obErro->ocorreu() )
                return $obErro;

            $stSql = " SELECT arrecadacao.fn_lancamento_manual_por_grupo_intervalo_especifico( ".$inCodGrupo.", ".$inExercicio.", '".$stInscricoes."', ".$inQtdParc.", '".$stVencimento."', '".$stTipoDesconto."', '".$stValorDesconto."', '".$stVencimentoDesconto."', '".$stNumeroParcela."', ".$rsTipo->getCampo("tipo")." )  AS resultado;";
            $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
            if ( !$rsRecordSet->Eof() ) {
                Sessao::write( "lancamentos_cods", $rsRecordSet->getCampo("resultado") );
                $arTMP = explode( ",", $rsRecordSet->getCampo("resultado") );
            }
        } else {//if ( $this->obRARRGrupo->getCodGrupo() ) {
            //por credito
            $obConexao   = new Conexao;
            $arCredito = explode ('.', $_REQUEST["inCodCredito"] );

            $inInscricaoInicial = 1000000;
            $inInscricaoFinal = 0;
            if ( Sessao::read( 'arquivo_calculos_lancamentos' ) ) {
                $nome_arquivo = Sessao::read( 'arquivo_calculos_lancamentos' );
                if ( $arquivo = fopen ( $nome_arquivo, 'r' ) ) {
                    $arCalculo = array ();
                    $arDados = array();
                    while (!feof($arquivo)) {
                        if ($stLinha = fgets($arquivo)) {
                            $arLinha = explode ('&', $stLinha);
                            $arDados[] = $arLinha[2];
                        }
                    }

                    fclose( $arquivo );

                    sort( $arDados, SORT_NUMERIC );

                    $inInscricaoFinal = $arDados[count( $arDados )-1];
                    $inInscricaoInicial = $arDados[0];
                }
            }

            $stVencimento = "";
            $stTipoDesconto = "";
            $stValorDesconto = "";
            $stVencimentoDesconto = "";
            $stNumeroParcela = "";
            if ( !Sessao::read( "UsaCalendarioFiscal" ) ) {
                $arSessaoParcelas = Sessao::read( "parcelas" );
                $inQtdParc = count( $arSessaoParcelas );
                for ( $inX=0; $inX<count( $arSessaoParcelas ); $inX++ ) {
                    if ($inX > 0) {
                        $stVencimento .= ";";
                        $stTipoDesconto .= ";";
                        $stValorDesconto .= ";";
                        $stVencimentoDesconto .= ";";
                        $stNumeroParcela .= ";";
                    }

                    $arTMPVenc = explode( "/", $arSessaoParcelas[$inX]["data_vencimento"] );
                    $stVencimento .= $arTMPVenc[2]."-".$arTMPVenc[1]."-".$arTMPVenc[0];
                    $stVencimentoDesconto .= $arTMPVenc[2]."-".$arTMPVenc[1]."-".$arTMPVenc[0];

                    $stValorDesconto .= (double) $arSessaoParcelas[$inX]["valor"];

                    if ($arSessaoParcelas[$inX]["stTipoDesconto"] == "Percentual") {
                        $stTipoDesconto .= "true";
                    }else
                        $stTipoDesconto .= "false";

                    if ($arSessaoParcelas[$inX]["stTipoParcela"] == "Única") {
                        $stNumeroParcela .= "0";
                    }else
                        $stNumeroParcela .= $arSessaoParcelas[$inX]["stTipoParcela"];
                }
            } else {
                $inQtdParc = -1;
            }

            $stSql = " SELECT arrecadacao.fn_lancamento_manual_por_credito_intervalo( ".$arCredito[0].", ".$arCredito[1].", ".$arCredito[2].", ".$arCredito[3].", ".Sessao::getExercicio().", '".$inInscricaoInicial." AND ".$inInscricaoFinal."', ".$inQtdParc.", '".$stVencimento."', '".$stTipoDesconto."', '".$stValorDesconto."', '".$stVencimentoDesconto."', '".$stNumeroParcela."', 1 )  AS resultado;";
            $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
            if ( !$rsRecordSet->Eof() ) {
                Sessao::write( "lancamentos_cods", $rsRecordSet->getCampo("resultado") );
                $arTMP = explode( ",", $rsRecordSet->getCampo("resultado") );
            }
        }

        // emitir carnes
        if ( ( ( $_REQUEST["emissao_carnes"] == "local" ) || ($_REQUEST["boCarne"] == "sim" ) ) && !$obErro->ocorreu() ) {

            $inCodLancamentoAtual = Sessao::read( "lancamentos_cods" );
            $inCodLancamentoAtual = substr ( $inCodLancamentoAtual, 0, strlen( $inCodLancamentoAtual - 1) );
            $obRARRLancamento->obRARRCarne->obRARRParcela->roRARRLancamento->inCodLancamento = $inCodLancamentoAtual;

            include_once 'PREmitirCarneLancManual.php';
            //exit;
        }

        if (!$obErro->ocorreu() ) {

            $stPag = $pgFormRelatorioExecucaoLancamento."?stAcao=incluir&stTipoCalculo=".$_REQUEST["stTipoCalculo"]."&inCodGrupo=".$_REQUEST["inCodGrupo"]."&inCodCredito=".$_REQUEST["inCodCredito"];

            if ($_REQUEST["inCodGrupo"]) {
                SistemaLegado::alertaAviso($stPag,"Codigo do Grupo:".$_REQUEST["inCodGrupo"],"incluir","aviso", Sessao::getId(), "../");
            } else {
                SistemaLegado::alertaAviso($stPag,"Codigo do Crédito:".$_REQUEST["inCodCredito"],"incluir","aviso", Sessao::getId(), "../");
            }

        } else {
            SistemaLegado::exibeAviso( urlencode( $obErro->getDescricao()),"n_erro","erro",Sessao::getId(), "../" );
        }
        break;

    case "incluir":

        set_time_limit(0);
        Sessao::write( "lancamentos_cods", null );
        if (!$_REQUEST['FormLancamentoManual']) { //PROCEDIMENTOS DO LANÇAMENTO AUTOMATICO
            $obRARRLancamento = new RARRLancamento( new RARRCalculo );
            $obRARRLancamento->roRARRCalculo->setTipoCalculo ( 1 );
            if ($_REQUEST["inCodGrupo"]) {
                $arDadosGrupoCredito = explode( "/", $_REQUEST["inCodGrupo"] );
                $obRARRLancamento->roRARRCalculo->obRARRGrupo->setCodGrupo( $arDadosGrupoCredito[0] );
                $obRARRLancamento->roRARRCalculo->obRARRGrupo->setExercicio( $arDadosGrupoCredito[1] );
            }
            if ( Sessao::read( 'TipoCalculo' ) == "parcial" ) {
                if ( Sessao::read( 'UsaCalendarioFiscal' ) == 'on' ) {
                    Sessao::write( 'TipoLancamento', "ParcialUsa" );
                } else {
                    Sessao::write( 'TipoLancamento', "Parcial" );
                }
            } elseif ( Sessao::read( 'TipoCalculo' ) == "geral" ) {
                Sessao::write( 'TipoLancamento', "Geral" );
            } else {
                 Sessao::write( 'TipoLancamento', "Individual" );
            }
            $obErro = $obRARRLancamento->efetuarLancamentoParcialIndividualCalculo($boTransacao);

        } else {//PROCEDIMENTOS DO LANÇAMENTO MANUAL

            if ($_REQUEST["emissao_carnes"] == "local" && !$_REQUEST["stArquivo"]) {
                $obErro->setDescricao ("Nenhum modelo de carne foi escolhido.");
            }

            if ($_REQUEST['FormLancamentoManual']) {
                include_once( CAM_GT_ARR_NEGOCIO."RARRCarne.class.php"                              );
                $arParcelasSessao = Sessao::read( "parcelas" );
                if ( count( $arParcelasSessao ) < 1 ) {
                    $obErro->setDescricao ("É necessário incluir pelo menos uma Parcela para este lançamento.");
                } else {
                    if (!$_REQUEST['stReferencia']) {
                        $obErro->setDescricao ("É necessário vincular uma referência para o lançamento.");
                    } else {

                        if ($_REQUEST['stReferencia'] == 'cgm' && !$_REQUEST['inCodContribuinte']) {
                            $obErro->setDescricao ("É necessário vincular um CGM para este lançamento.");
                        } elseif ($_REQUEST['stReferencia'] == 'ii' && !$_REQUEST['inInscricaoImobiliaria']) {
                            $obErro->setDescricao ("É necessário vincular uma entidade imobiliária para este lançamento.");
                        } elseif ($_REQUEST['stReferencia'] == 'ie' && !$_REQUEST['inInscricaoEconomica']) {

                            $obErro->setDescricao ("É necessário vincular uma entidade econômica para este lançamento.");

                        } else {
                            // objeto de lançamento
                            $obRARRLancamento = new RARRLancamento( new RARRCalculo );
                            $boDiversas = TRUE;

                            //REGISTRA A REFERENCIA
                            if ($_REQUEST['stReferencia'] == 'cgm') {
                                $obRARRLancamento->obRCgm->setNumCGM( $_REQUEST['inCodContribuinte'] );
                                $obRARRLancamento->obRARRCarne->inCodContribuinteInicial = $_REQUEST['inCodContribuinte'];
                            } elseif ($_REQUEST['stReferencia'] == 'ii') {
                                $obRARRLancamento->obRCEMInscricaoEconomica->setInscricaoEconomica( null );
                                $obRARRLancamento->obRCIMImovel->setNumeroInscricao( $_REQUEST['inInscricaoImobiliaria'] );
                                $obRARRLancamento->obRARRCarne->inInscricaoImobiliariaInicial = $_REQUEST['inInscricaoImobiliaria'];
                            } elseif ($_REQUEST['stReferencia'] == 'ie') {
                                $obRARRLancamento->obRCIMImovel->setNumeroInscricao( null );
                                $obRARRLancamento->obRCEMInscricaoEconomica->setInscricaoEconomica( $_REQUEST['inInscricaoEconomica'] );
                                $obRARRLancamento->obRARRCarne->inInscricaoEconomicaInicial = $_REQUEST['inInscricaoEconomica'];
                            }
                            $inNumeroCreditos = 1;

                            if (($_REQUEST['FormLancamentoManual'] == 'GrupoCrédito') && $_REQUEST["inCodGrupo"]) {

                                //LANCAMENTO MANUAL DE GRUPO DE CREDITO

                                $obRARRLancamento->setDataVencimento ( $arParcelasSessao[0]['data_vencimento']);

                                $arDadosGrupoCredito = explode( "/", $_REQUEST["inCodGrupo"] );
                                $descricao = $descriicao = "Codigo do Grupo: ".$arDadosGrupoCredito[0];
                                $obRARRLancamento->roRARRCalculo->obRARRGrupo->setCodGrupo( $arDadosGrupoCredito[0] );
                                $obRARRLancamento->roRARRCalculo->obRARRGrupo->setExercicio( $arDadosGrupoCredito[1] );
                                $obRARRLancamento->obRARRCarne->setGrupo ( $arDadosGrupoCredito[0] );

                                $obRARRLancamento->roRARRCalculo->obRARRGrupo->listarCreditos( $rsCreditos );
                                $inNumeroCreditos = $rsCreditos->getNumLinhas();
                                $valorTotalTMP = str_replace( '.', '', $_REQUEST['obHdnValorTotal']);
                                $valorTotalTMP = str_replace( ',', '.', $valorTotalTMP);
                                $obRARRLancamento->setValor( $valorTotalTMP  );

                                $arGruposValidos = explode(',','101,102,121,10120, 10121, 10122, 10123, 10124,10125, 10198, 10199, 10220, 10221, 10222, 10223, 10224, 10225, 10298,10299, 131,13120,13121,13122,13123,13124,13125,13197,131,13198,13199');
                                if ( in_array( $arDadosGrupoCredito[0],$arGruposValidos)) {
                                    if ( $arDadosGrupoCredito[0] == '121' )
                                        $boEspecial = TRUE;
                                    $boDiversas = FALSE;
                                }

                            } else {

                                //LANCAMENTO MANUAL DE CREDITO
                                #echo '<h3>LANCAMENTO MANUAL DE CREDITO</h3>'; exit;
                                $descricao = $descriicao = "Codigo do Crédito: ".$_REQUEST["inCodCredito"];

                                $arCredito = array();
                                $arCredito = explode ('.', $_REQUEST['inCodCredito'] );
                                $obRARRLancamento->roRARRCalculo->obRMONCredito->setCodCredito  ( $arCredito[0] );
                                $obRARRLancamento->roRARRCalculo->obRMONCredito->setCodEspecie  ( $arCredito[1] );
                                $obRARRLancamento->roRARRCalculo->obRMONCredito->setCodGenero   ( $arCredito[2] );
                                $obRARRLancamento->roRARRCalculo->obRMONCredito->setCodNatureza ( $arCredito[3] );

                                $obRARRLancamento->obRARRCarne->setExercicio ( $_REQUEST['inExercicioCalculo'] );

                                $valorTotalTMP = str_replace ( '.','',str_replace( '.', '', $_REQUEST['inValor']));
                                $valorTotalTMP = str_replace ( ',', '.', $valorTotalTMP);
                                $obRARRLancamento->setValor( $valorTotalTMP );
                            }

                            $contValores =1;
                            $arValoresCreditosSessao = array();
                            while ($contValores <= $inNumeroCreditos) {
                                if (!$_REQUEST["Valor_$contValores"]) {
                                    $arValoresCreditosSessao[$contValores] = '0,00';
                                } else {
                                    $arValoresCreditosSessao[$contValores] = $_REQUEST["Valor_$contValores"];
                                }
                                $contValores ++;
                            }

                            Sessao::write( "ValoresCreditos", $arValoresCreditosSessao );
                            $arParcelasSessao = Sessao::read( "parcelas" );
                            $obRARRLancamento->setDataVencimento ( $arParcelasSessao[0]['data_vencimento'] );
                            $obRARRLancamento->setObservacao        ( $_REQUEST['stObservacao'] );
                            $obRARRLancamento->setObservacaoSistema        ( $_REQUEST['stObservacaoInterna'] );

                            list($inProcesso,$inExercicio) = explode("/",$_POST['inProcesso']);
                            $obRARRLancamento->obRProcesso->setCodigoProcesso ( $inProcesso );
                            $obRARRLancamento->obRProcesso->setExercicio      ( $inExercicio  );

                            // Contagem de Parcelas Normais
                            $contParcelasNormais = 0;
                            $cont = 0;
                            while ( $cont < count( $arParcelasSessao ) ) {
                                if ($arParcelasSessao[$cont]['stTipoParcela'] != "Única") {
                                    $contParcelasNormais++;
                                }
                                $cont++;
                            }
                            $obRARRLancamento->setTotalParcelas ( $contParcelasNormais );

                            // Contagem de Parcelas Únicas
                            $contParcelasUnicas = 0;
                            $cont = 0;
                            while ( $cont < count( $arParcelasSessao ) ) {
                                if ($arParcelasSessao[$cont]['stTipoParcela'] == "Única") {
                                    $contParcelasUnicas++;
                                }
                                $cont++;
                            }
                            $obRARRLancamento->setTotalParcelasUnicas ( $contParcelasUnicas );

                            if ($_REQUEST['FormLancamentoManual'] == 'GrupoCrédito') {
                                $obErro = $obRARRLancamento->efetuarLancamentoManualGrupoCredito();
                            } else {
                                $obErro = $obRARRLancamento->efetuarLancamentoManualCredito();
                            }
                        }
                    }
                }
            }
            $pgForm = $pgFormManual;
        }
        // emitir carnes
        if ( ( ( $_REQUEST["emissao_carnes"] == "local" ) || ($_REQUEST["boCarne"] == "sim" ) ) && !$obErro->ocorreu() ) {

            $inCodLancamentoAtual = Sessao::read( "lancamentos_cods" );
            $inCodLancamentoAtual = substr ( $inCodLancamentoAtual, 0, strlen( $inCodLancamentoAtual - 1) );
            $obRARRLancamento->obRARRCarne->obRARRParcela->roRARRLancamento->inCodLancamento = $inCodLancamentoAtual;

            include_once 'PREmitirCarneLancManual.php';

//            if ($_REQUEST["boCarne"] == "sim" )
                exit;
        }

        if (!$obErro->ocorreu() ) {

            $stPag = $pgFormRelatorioExecucaoLancamento."?stAcao=incluir&stTipoCalculo=".$_REQUEST["stTipoCalculo"]."&inCodGrupo=".$_REQUEST["inCodGrupo"]."&inCodCredito=".$_REQUEST["inCodCredito"];

            if ($_REQUEST["inCodGrupo"]) {
                SistemaLegado::alertaAviso($stPag,"Codigo do Grupo:".$_REQUEST["inCodGrupo"],"incluir","aviso", Sessao::getId(), "../");
            } else {
                SistemaLegado::alertaAviso($stPag,"Codigo do Crédito:".$_REQUEST["inCodCredito"],"incluir","aviso", Sessao::getId(), "../");
            }

        } else {
            SistemaLegado::exibeAviso( urlencode( $obErro->getDescricao()),"n_erro","erro",Sessao::getId(), "../" );
        }
    break;
}

?>