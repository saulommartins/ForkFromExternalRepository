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
    * Página de processamento para o Calculo ISS
    * Data de Criação   : 23/10/2006

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato

    * @ignore

    * $Id: PREscriturarReceita.php 62838 2015-06-26 13:02:49Z diogo.zarpelon $

    * Casos de uso: uc-05.03.22
                    uc-05.03.05
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRCadastroEconomicoFaturamento.class.php" );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRNotaServico.class.php" );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRNota.class.php" );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRNotaFiscal.class.php" );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRFaturamentoServico.class.php" );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRFaturamentoSemMovimento.class.php" );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRServicoComRetencao.class.php" );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRServicoSemRetencao.class.php" );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRCalculo.class.php" );

include_once ( CAM_GT_ARR_MAPEAMENTO."TARRCalculoGrupoCredito.class.php" );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRCalculoCgm.class.php" );

include_once ( CAM_GT_ARR_MAPEAMENTO."TARRCadastroEconomicoCalculo.class.php" );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRCreditoGrupo.class.php" );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRLancamento.class.php" );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRLancamentoCalculo.class.php" );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRParcela.class.php" );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRParcelaReemissao.class.php" );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRVencimentoParcela.class.php" );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRCarne.class.php" );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMServico.class.php" );
include_once ( CAM_GT_ARR_NEGOCIO."RARRConfiguracao.class.php" );
include_once ( CAM_GT_ARR_NEGOCIO."RARRCalculo.class.php" );

include_once ( CAM_GT_ARR_MAPEAMENTO."TARRLancamentoUsaDesoneracao.class.php" );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRDesonerado.class.php" );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRDesoneradoCadEconomico.class.php" );
$stAcao = $request->get('stAcao');

//Define o nome dos arquivos PHP
$stPrograma = "EscriturarReceita";
$pgFilt = "FL".$stPrograma.".php?stAcao=$stAcao";
$pgList = "LS".$stPrograma.".php?stAcao=$stAcao";
$pgForm = "FM".$stPrograma.".php?stAcao=$stAcao";

switch ($stAcao) {
    case "incluir":
        if($_REQUEST["inCodModalidade"] == ''){
            SistemaLegado::exibeAviso( "Não foi definido uma modalidade para a atividade.", "n_incluir", "erro");
            exit;
        }
        if ($_REQUEST["stEscrituracao"] == "smov") { //sem movimentacao
            $obTARRLancamento = new TARRLancamento;
            $obTARRCadastroEconomicoFaturamento = new TARRCadastroEconomicoFaturamento;
            $obTARRFaturamentoSemMovimento = new TARRFaturamentoSemMovimento;
            $obTARRCalculo = new TARRCalculo;
            $obTARRCalculoGrupoCredito = new TARRCalculoGrupoCredito;
            $obTARRCalculoCgm = new TARRCalculoCgm;
            $obTARRCadastroEconomicoCalculo = new TARRCadastroEconomicoCalculo;
            $obTARRLancamentoCalculo = new TARRLancamentoCalculo;

            $obRARRConfiguracao = new RARRConfiguracao;
            $obRARRConfiguracao->consultar();
            $stCodGrupoCreditoEscrituracao = $obRARRConfiguracao->getCodigoGrupoCreditoEscrituracao();
            $arGrupoCreditoEscrituracao = preg_split( "/\//", $stCodGrupoCreditoEscrituracao );

            $obTARRCreditoGrupo = new TARRCreditoGrupo;
            $stFiltro = " WHERE acg.cod_grupo = ".$arGrupoCreditoEscrituracao[0]." AND acg.ano_exercicio = '".$arGrupoCreditoEscrituracao[1]."'";
            $obTARRCreditoGrupo->recuperaRelacionamento( $rsListaCreditos, $stFiltro );

            if ( $rsListaCreditos->Eof() ) {
                SistemaLegado::exibeAviso( "Não existem créditos para o grupo de credito da escrituração.", "n_incluir", "erro");
                exit;
            }

            $inValorCalculo = 0;

            Sessao::setTrataExcecao( true );
            Sessao::getTransacao()->setMapeamento( $obTARRCadastroEconomicoFaturamento );

                $obTARRCadastroEconomicoFaturamento->setDado( "inscricao_economica", $_REQUEST["inInscricaoEconomica"]);
                $dtCompetencia = $_REQUEST["stCompetencia"]."/".$_REQUEST["stExercicio"];
                $obTARRCadastroEconomicoFaturamento->setDado( "competencia", $dtCompetencia );
                $obTARRCadastroEconomicoFaturamento->inclusao();

                $obTARRCadastroEconomicoFaturamento->recuperaTodos( $rsLista, " WHERE inscricao_economica = ".$_REQUEST["inInscricaoEconomica"]." AND competencia = '".$dtCompetencia."' ", "timestamp DESC" );
                $stTimeStamp = $rsLista->getCampo( "timestamp" );

                $obTARRFaturamentoSemMovimento->setDado( "inscricao_economica", $_REQUEST["inInscricaoEconomica"]);
                $obTARRFaturamentoSemMovimento->setDado( "timestamp", $stTimeStamp );
                $obTARRFaturamentoSemMovimento->inclusao();

                $obTARRCalculo->proximoCod( $inCodCalculo );
                $obTARRCalculo->setDado( "cod_calculo", $inCodCalculo );
                $obTARRCalculo->setDado( "cod_credito", $rsListaCreditos->getCampo("cod_credito") );
                $obTARRCalculo->setDado( "cod_natureza", $rsListaCreditos->getCampo("cod_natureza") );
                $obTARRCalculo->setDado( "cod_genero", $rsListaCreditos->getCampo("cod_genero") );
                $obTARRCalculo->setDado( "cod_especie", $rsListaCreditos->getCampo("cod_especie") );
                $obTARRCalculo->setDado( "exercicio", $arGrupoCreditoEscrituracao[1] );
                $obTARRCalculo->setDado( "valor", $inValorCalculo );
                $obTARRCalculo->setDado( "nro_parcelas", 0 );
                $obTARRCalculo->setDado( "ativo", true );
                $obTARRCalculo->setDado( "calculado", true );
                $obTARRCalculo->inclusao();

                $obTARRCalculoGrupoCredito->setDado( "cod_calculo", $inCodCalculo );
                $obTARRCalculoGrupoCredito->setDado( "cod_grupo", $arGrupoCreditoEscrituracao[0] );
                $obTARRCalculoGrupoCredito->setDado( "ano_exercicio", $arGrupoCreditoEscrituracao[1] );
                $obTARRCalculoGrupoCredito->inclusao();

                $obTARRCalculoCgm->setDado( "cod_calculo", $inCodCalculo );
                $obTARRCalculoCgm->setDado( "numcgm", $_REQUEST["inNumCGM"] );
                $obTARRCalculoCgm->inclusao();

                //buscar a data de vencimento no calendario fiscal
                $obTARRLancamento->proximoCod( $inCodLancamento );
                $obTARRLancamento->setDado( "cod_lancamento", $inCodLancamento );
                $obTARRLancamento->setDado( "vencimento", date("d/m/Y") );
                $obTARRLancamento->setDado( "total_parcelas", 0 );
                $obTARRLancamento->setDado( "ativo", true );
                $obTARRLancamento->setDado( "observacao", $_REQUEST["stObservacao"] );
                $obTARRLancamento->setDado( "observacao_sistema", "" );
                $obTARRLancamento->setDado( "valor", $inValorCalculo );
                $obTARRLancamento->inclusao();

                $obTARRLancamentoCalculo->setDado( "cod_calculo", $inCodCalculo );
                $obTARRLancamentoCalculo->setDado( "cod_lancamento", $inCodLancamento );
                $obTARRLancamentoCalculo->setDado( "valor", $inValorCalculo );
                $obTARRLancamentoCalculo->inclusao();

                $obTARRCadastroEconomicoCalculo->setDado( "cod_calculo", $inCodCalculo );
                $obTARRCadastroEconomicoCalculo->setDado( "inscricao_economica", $_REQUEST["inInscricaoEconomica"] );
                $obTARRCadastroEconomicoCalculo->setDado( "timestamp", $stTimeStamp );
                $obTARRCadastroEconomicoCalculo->inclusao();

            Sessao::encerraExcecao();

            SistemaLegado::alertaAviso($pgList, "Inscrição Econômica: ".$_REQUEST["inInscricaoEconomica"], "incluir", "aviso", Sessao::getId(), "../");
        } else {
            if ( ( $_REQUEST['boEmissaoCarne'] == 1 ) && (!$_REQUEST['stArquivo']) ) {
                SistemaLegado::exibeAviso( "Campo Modelo de Carnê inválido.", "n_incluir", "erro");
                exit;
            }

            if ( Sessao::read("setar_data") && !$_REQUEST["dtVencimento"] ) {
                SistemaLegado::exibeAviso( "Campo Vencimento inválido.", "n_incluir", "erro");
                exit;
            }

            if ($_REQUEST["stEscrituracao"] == "servico") { //por servico
                if ( count( Sessao::read("servicos_retencao") ) <= 0 ) {
                    SistemaLegado::exibeAviso( "Lista de serviços vazia.", "n_incluir", "erro");
                    exit;
                }

                if (!$_REQUEST["dtEmissao"]) {
                    SistemaLegado::exibeAviso( "Campo Data de Emissão inválido.", "n_incluir", "erro");
                    exit;
                }
            } else { //por nota
                if ($_REQUEST["boReterFonte"]) { //com retencao
                    if ( count ( Sessao::read("notas_retencao_comrt") ) <= 0 ) {
                        SistemaLegado::exibeAviso( "Lista de notas vazia.", "n_incluir", "erro");
                        exit;
                    }
                } else { //sem retencao
                    if ( count ( Sessao::read("notas_retencao_semrt") ) <= 0 ) {
                        SistemaLegado::exibeAviso( "Lista de notas vazia.", "n_incluir", "erro");
                        exit;
                    }
                }
            }

            $obRARRConfiguracao = new RARRConfiguracao;
            $obRARRConfiguracao->consultar();
            $stCodGrupoCreditoEscrituracao = $obRARRConfiguracao->getCodigoGrupoCreditoEscrituracao();
            $arGrupoCreditoEscrituracao = preg_split( "[/]", $stCodGrupoCreditoEscrituracao );
            $obTARRCreditoGrupo = new TARRCreditoGrupo;
            $stFiltro = " WHERE acg.cod_grupo = ".$arGrupoCreditoEscrituracao[0]." AND acg.ano_exercicio = '".$arGrupoCreditoEscrituracao[1]."'";
            $obTARRCreditoGrupo->recuperaRelacionamento( $rsListaCreditos, $stFiltro, " ORDER BY acg.ordem ASC " );

            if ( $rsListaCreditos->Eof() ) {
                SistemaLegado::exibeAviso( "Não existem créditos para o grupo de credito da escrituração.", "n_incluir", "erro");
                exit;
            }

            if ($_REQUEST["stEscrituracao"] == "servico") { //por servico
                $arTMPData = explode( "/", $_REQUEST["dtEmissao"] );
            } else {
                if ($_REQUEST["boReterFonte"]) { //com retencao
                    $stTipoNota = "notas_retencao_comrt";
                } else { //sem retencao
                    $stTipoNota = "notas_retencao_semrt";
                }

                $arTipoNota = Sessao::read( $stTipoNota );
                $arTMPData = explode( "/", $arTipoNota[0]["dtEmissao"] );
            }

            $obTARRVencimentoParcela = new TARRVencimentoParcela;
            $stFiltro = " WHERE cod_grupo = ".$arGrupoCreditoEscrituracao[0]." AND ano_exercicio = '".$arGrupoCreditoEscrituracao[1]."' AND cod_parcela = ".$arTMPData[1];
            $obTARRVencimentoParcela->recuperaTodos( $rsListaParcela, $stFiltro );
            if ( $rsListaParcela->Eof() ) {
                SistemaLegado::exibeAviso( "Nenhum calendário fiscal foi definido para o grupo de credito da escrituração.", "n_incluir", "erro");
                exit;
            }

            if ( Sessao::read("setar_data") ) {
                $dataReemissao = $rsListaParcela->getCampo("data_vencimento" );
                $rsListaParcela->setCampo("data_vencimento", $_REQUEST["dtVencimento"] );
            }

            $obTARRLancamento = new TARRLancamento;
            $obTARRCadastroEconomicoFaturamento = new TARRCadastroEconomicoFaturamento;
            $obTARRNotaServico = new TARRNotaServico;
            $obTARRNota = new TARRNota;
            $obTARRNotaFiscal = new TARRNotaFiscal;
            $obTARRFaturamentoServico = new TARRFaturamentoServico;
            $obTARRServicoComRetencao = new TARRServicoComRetencao;
            $obTARRServicoSemRetencao = new TARRServicoSemRetencao;
            $obTARRCalculo = new TARRCalculo;

            $obTARRCalculoGrupoCredito = new TARRCalculoGrupoCredito;
            $obTARRCalculoCgm = new TARRCalculoCgm;

            $obTARRCadastroEconomicoCalculo = new TARRCadastroEconomicoCalculo;
            $obTARRLancamentoCalculo = new TARRLancamentoCalculo;
            $obTARRParcela = new TARRParcela;
            $obTARRParcelaReemissao = new TARRParcelaReemissao;
            $obTARRCarne = new TARRCarne;
            $obTCEMServico = new TCEMServico;

            $arServicosInseridos = array();
            $inTotalServicosInseridos = 0;

            Sessao::setTrataExcecao( true );
            Sessao::getTransacao()->setMapeamento( $obTARRCadastroEconomicoFaturamento );

                $obTARRCadastroEconomicoFaturamento->setDado( "inscricao_economica", $_REQUEST["inInscricaoEconomica"]);
                $dtCompetencia = $_REQUEST["stCompetencia"]."/".$_REQUEST["stExercicio"];
                $obTARRCadastroEconomicoFaturamento->setDado( "competencia", $dtCompetencia );
                $obTARRCadastroEconomicoFaturamento->inclusao();

                $obTARRCadastroEconomicoFaturamento->recuperaTodos( $rsLista, " WHERE inscricao_economica = ".$_REQUEST["inInscricaoEconomica"]." AND competencia = '".$dtCompetencia."' ", "timestamp DESC" );
                $stTimeStamp = $rsLista->getCampo( "timestamp" );

                if ($_REQUEST["stEscrituracao"] == "nota") { //usando nota
                    $inValorCalculo = 0;
                    if ($_REQUEST["boReterFonte"]) { //com retencao
                        $stTipoNota = "notas_retencao_comrt";
                    } else { //sem retencao
                        $stTipoNota = "notas_retencao_semrt";
                    }

                    $arOcorrencias = array();
                    $inTotalDeOcorrencias = 0;
                    $arTipoNota = Sessao::read( $stTipoNota );
                    foreach ($arTipoNota as $inChave => $arNotasRetencao) {
                        $obTARRNota->proximoCod( $inCodNota );

                        $obTARRNota->setDado( "cod_nota", $inCodNota );
                        $obTARRNota->setDado( "valor_nota", str_replace ( ',', '.', str_replace ( '.', '', $arNotasRetencao["flValorLancadoSemAliquota"] ) ) );
                        $obTARRNota->inclusao();

                        $obTARRNotaFiscal->setDado( "cod_nota", $inCodNota );
                        $obTARRNotaFiscal->setDado( "nro_serie", $arNotasRetencao["inSerie"] );
                        $obTARRNotaFiscal->setDado( "nro_nota", $arNotasRetencao["inNumeroNota"] );
                        $obTARRNotaFiscal->inclusao();

                        $arNotasServicosInseridos = array();
                        $inTotalNotasServicosInseridos = 0;
                        $inOcorrencia = 0;
                        foreach ($arNotasRetencao["arServicos"] as $inChave2 => $arServicoRetencao) {
                            $stFiltro = " WHERE cod_estrutural = '".$arServicoRetencao["stServico"]."'";
                            $obTCEMServico->recuperaTodos( $rsListaServico, $stFiltro );

                            $boAchou = false;
                            for ( $inTY=0; $inTY<count($arOcorrencias); $inTY++ ) {
                                if ( $arOcorrencias[$inTY]["cod_servico"] == $rsListaServico->getCampo("cod_servico") ) {
                                    $arOcorrencias[$inTY]["ocorrencia"]++;
                                    $inOcorrencia = $arOcorrencias[$inTY]["ocorrencia"];
                                    $boAchou = true;
                                    break;
                                }
                            }

                            if (!$boAchou) {
                                $inTY = count($arOcorrencias);
                                $arOcorrencias[$inTY]["ocorrencia"] = $inOcorrencia;
                                $arOcorrencias[$inTY]["cod_servico"] = $rsListaServico->getCampo("cod_servico");
                            }

                            $inTotalServicosInseridos = count($arServicosInseridos);
                                $obTARRFaturamentoServico->setDado( "cod_atividade", $_REQUEST["inCodAtividade"] );
                                $obTARRFaturamentoServico->setDado( "cod_servico", $rsListaServico->getCampo("cod_servico") );
                                $obTARRFaturamentoServico->setDado( "inscricao_economica", $_REQUEST["inInscricaoEconomica"] );
                                $obTARRFaturamentoServico->setDado( "cod_modalidade", $_REQUEST["inCodModalidade"] );
                                $obTARRFaturamentoServico->setDado( "dt_emissao", $arNotasRetencao["dtEmissao"] );
                                $obTARRFaturamentoServico->setDado( "ocorrencia",$inOcorrencia);
                                $obTARRFaturamentoServico->setDado( "timestamp", $stTimeStamp );
                                $obTARRFaturamentoServico->inclusao();

                                $arServicosInseridos[$inTotalServicosInseridos] = $rsListaServico->getCampo("cod_servico");

                            $inTotalNotasServicosInseridos = count( $arNotasServicosInseridos );
                                $obTARRNotaServico->setDado( "cod_nota", $inCodNota );
                                $obTARRNotaServico->setDado( "cod_atividade", $_REQUEST["inCodAtividade"] );
                                $obTARRNotaServico->setDado( "cod_servico", $rsListaServico->getCampo("cod_servico") );
                                $obTARRNotaServico->setDado( "inscricao_economica", $_REQUEST["inInscricaoEconomica"] );
                                $obTARRNotaServico->setDado( "ocorrencia",$inOcorrencia);
                                $obTARRNotaServico->setDado( "timestamp", $stTimeStamp );
                                $obTARRNotaServico->inclusao();

                                $arNotasServicosInseridos[$inTotalNotasServicosInseridos] = $rsListaServico->getCampo("cod_servico");

                            if ($arServicoRetencao["flValorDeclarado"]) { //sem retencao
                                    $obTARRServicoSemRetencao->setDado( "cod_atividade", $_REQUEST["inCodAtividade"] );
                                    $obTARRServicoSemRetencao->setDado( "cod_servico", $rsListaServico->getCampo( "cod_servico" ) );
                                    $obTARRServicoSemRetencao->setDado( "inscricao_economica", $_REQUEST["inInscricaoEconomica"] );
                                    $obTARRServicoSemRetencao->setDado( "valor_declarado", str_replace ( ',', '.', str_replace ( '.', '', $arServicoRetencao["flValorDeclarado"] ) ) );
                                    $obTARRServicoSemRetencao->setDado( "valor_deducao_legal", str_replace ( ',', '.', str_replace ( '.', '', $arServicoRetencao["flDeducaoLegal"] ) ) );
                                    if ($arServicoRetencao["flDeducao"])
                                        $obTARRServicoSemRetencao->setDado( "valor_deducao", str_replace ( ',', '.', str_replace ( '.', '', $arServicoRetencao["flDeducao"] ) ) );
                                    else
                                        $obTARRServicoSemRetencao->setDado( "valor_deducao", 0 );

                                    $obTARRServicoSemRetencao->setDado( "valor_lancado", str_replace ( ',', '.', str_replace ( '.', '', $arServicoRetencao["flValorLancadoSemAliquota"] ) ) );

                                    $obTARRServicoSemRetencao->setDado( "aliquota", $arServicoRetencao["flAliquota"] );
                                    $obTARRServicoSemRetencao->setDado( "ocorrencia",$inOcorrencia);
                                    $obTARRServicoSemRetencao->setDado( "timestamp", $stTimeStamp );
                                    $obTARRServicoSemRetencao->inclusao();

                                $inValorCalculo += str_replace ( ',', '.', str_replace ( '.', '', $arServicoRetencao["flValorLancado"] ) );
                            } else { //com retencao
                                    $obTARRServicoComRetencao->setDado( "cod_atividade", $_REQUEST["inCodAtividade"] );
                                    $obTARRServicoComRetencao->setDado( "cod_servico", $rsListaServico->getCampo("cod_servico") );
                                    $obTARRServicoComRetencao->setDado( "cod_municipio", $arServicoRetencao["stMunicipio"]);
                                    $obTARRServicoComRetencao->setDado( "cod_uf", $arServicoRetencao["stEstado"] );
                                    $obTARRServicoComRetencao->setDado( "numcgm", $arServicoRetencao["inCGM"] );
                                    $obTARRServicoComRetencao->setDado( "inscricao_economica", $_REQUEST["inInscricaoEconomica"] );
                                    $obTARRServicoComRetencao->setDado( "valor_retido", str_replace ( ',', '.', str_replace ( '.', '', $arServicoRetencao["flValorRetido"] ) ) );
                                    $obTARRServicoComRetencao->setDado( "ocorrencia",$inOcorrencia);
                                    $obTARRServicoComRetencao->setDado( "timestamp", $stTimeStamp );
                                    $obTARRServicoComRetencao->inclusao();
                            }
                            $inOcorrencia++;
                        }

                    }
                } else { //usando servico
                    $inValorCalculo = 0;
                    $inOcorrencia = 0;
                    $arOcorrencias = array();
                    $inTotalDeOcorrencias = 0;
                    foreach ( Sessao::read("servicos_retencao") as $inChave2 => $arServicoRetencao ) {
                        $stFiltro = " WHERE cod_estrutural = '".$arServicoRetencao["stServico"]."'";
                        $obTCEMServico->recuperaTodos( $rsListaServico, $stFiltro );

                        $boAchou = false;
                        for ( $inTY=0; $inTY<count($arOcorrencias); $inTY++ ) {
                            if ( $arOcorrencias[$inTY]["cod_servico"] == $rsListaServico->getCampo("cod_servico") ) {
                                $arOcorrencias[$inTY]["ocorrencia"]++;
                                $inOcorrencia = $arOcorrencias[$inTY]["ocorrencia"];
                                $boAchou = true;
                                break;
                            }
                        }

                        if (!$boAchou) {
                            $inTY = count($arOcorrencias);
                            $arOcorrencias[$inTY]["ocorrencia"] = $inOcorrencia;
                            $arOcorrencias[$inTY]["cod_servico"] = $rsListaServico->getCampo("cod_servico");
                        }

                        $obTARRFaturamentoServico->setDado( "cod_atividade", $_REQUEST["inCodAtividade"] );
                        $obTARRFaturamentoServico->setDado( "cod_servico", $rsListaServico->getCampo("cod_servico") );
                        $obTARRFaturamentoServico->setDado( "inscricao_economica", $_REQUEST["inInscricaoEconomica"] );
                        $obTARRFaturamentoServico->setDado( "cod_modalidade", $_REQUEST["inCodModalidade"] );
                        $obTARRFaturamentoServico->setDado( "dt_emissao", $_REQUEST["dtEmissao"] );
                        $obTARRFaturamentoServico->setDado( "ocorrencia", $inOcorrencia );
                        $obTARRFaturamentoServico->setDado( "timestamp", $stTimeStamp );
                        $obTARRFaturamentoServico->inclusao();

                        if ($arServicoRetencao["flValorDeclarado"]) { //sem retencao
                            $obTARRServicoSemRetencao->setDado( "cod_atividade", $_REQUEST["inCodAtividade"] );
                            $obTARRServicoSemRetencao->setDado( "cod_servico", $rsListaServico->getCampo("cod_servico") );
                            $obTARRServicoSemRetencao->setDado( "inscricao_economica", $_REQUEST["inInscricaoEconomica"] );
                            $obTARRServicoSemRetencao->setDado( "valor_declarado", str_replace ( ',', '.', str_replace ( '.', '', $arServicoRetencao["flValorDeclarado"] ) ) );
                            $obTARRServicoSemRetencao->setDado( "valor_deducao_legal", str_replace ( ',', '.', str_replace ( '.', '', $arServicoRetencao["flDeducaoLegal"] ) ) );

                            if ($arServicoRetencao["flDeducao"])
                                $obTARRServicoSemRetencao->setDado( "valor_deducao", str_replace ( ',', '.', str_replace ( '.', '', $arServicoRetencao["flDeducao"] ) ) );
                            else
                                $obTARRServicoSemRetencao->setDado( "valor_deducao", 0 );

                            $obTARRServicoSemRetencao->setDado( "valor_lancado", str_replace ( ',', '.', str_replace ( '.', '', $arServicoRetencao["flValorLancadoSemAliquota"] ) ) );
                            $obTARRServicoSemRetencao->setDado( "aliquota", $arServicoRetencao["flAliquota"] );
                            $obTARRServicoSemRetencao->setDado( "ocorrencia", $inOcorrencia );
                            $obTARRServicoSemRetencao->setDado( "timestamp", $stTimeStamp );
                            $obTARRServicoSemRetencao->inclusao();

                            $inValorCalculo += str_replace ( ',', '.', str_replace ( '.', '', $arServicoRetencao["flValorLancado"] ) );

                        } else { //com retencao
                            $obTARRServicoComRetencao->setDado( "cod_atividade", $_REQUEST["inCodAtividade"] );
                            $obTARRServicoComRetencao->setDado( "cod_servico", $rsListaServico->getCampo("cod_servico") );
                            $obTARRServicoComRetencao->setDado( "cod_municipio", $arServicoRetencao["stMunicipio"]);
                            $obTARRServicoComRetencao->setDado( "cod_uf", $arServicoRetencao["stEstado"] );
                            $obTARRServicoComRetencao->setDado( "numcgm", $arServicoRetencao["inCGM"] );
                            $obTARRServicoComRetencao->setDado( "inscricao_economica", $_REQUEST["inInscricaoEconomica"] );
                            $obTARRServicoComRetencao->setDado( "valor_retido", str_replace ( ',', '.', str_replace ( '.', '', $arServicoRetencao["flValorRetido"] ) ) );
                            $obTARRServicoComRetencao->setDado( "ocorrencia", $inOcorrencia );
                            $obTARRServicoComRetencao->setDado( "timestamp", $stTimeStamp );
                            $obTARRServicoComRetencao->inclusao();
                        }
                        $inOcorrencia++;
                    }
                }

                $obTARRCalculo->proximoCod( $inCodCalculo );
                $obTARRCalculo->setDado( "cod_calculo", $inCodCalculo );
                $obTARRCalculo->setDado( "cod_credito", $rsListaCreditos->getCampo("cod_credito") );
                $obTARRCalculo->setDado( "cod_natureza", $rsListaCreditos->getCampo("cod_natureza") );
                $obTARRCalculo->setDado( "cod_genero", $rsListaCreditos->getCampo("cod_genero") );
                $obTARRCalculo->setDado( "cod_especie", $rsListaCreditos->getCampo("cod_especie") );
                $obTARRCalculo->setDado( "exercicio", $arGrupoCreditoEscrituracao[1] );
                $obTARRCalculo->setDado( "valor", $inValorCalculo );
                $obTARRCalculo->setDado( "nro_parcelas", 0 );
                $obTARRCalculo->setDado( "ativo", true );
                $obTARRCalculo->setDado( "calculado", true );
                $obTARRCalculo->inclusao();

                $obTARRCalculoCgm->setDado( "cod_calculo", $inCodCalculo );
                $obTARRCalculoCgm->setDado( "numcgm", $_REQUEST["inNumCGM"] );
                $obTARRCalculoCgm->inclusao();

//----------------------
                require_once (CAM_GT_ARR_MAPEAMENTO . "FARRCalculoTributario.class.php");
                $obCalculoTributario = new FARRCalculoTributario;

                $obRARRCalculo = new RARRCalculo;

                $arCalculosParaLancar = array();
                $arCalculosParaLancar[] = array( "cod_calculo" => $inCodCalculo, "valor" => $inValorCalculo, "credito" => $rsListaCreditos->getCampo("cod_credito"),
                                            "natureza" => $rsListaCreditos->getCampo("cod_natureza"), "genero" => $rsListaCreditos->getCampo("cod_genero"),
                                            "especie" => $rsListaCreditos->getCampo("cod_especie") );
//----------------------
                $rsListaCreditos->setPrimeiroElemento();

                //buscar a data de vencimento no calendario fiscal
                $obTARRLancamento->proximoCod( $inCodLancamento );
                $obTARRLancamento->setDado( "cod_lancamento", $inCodLancamento );
                $obTARRLancamento->setDado( "vencimento", $rsListaParcela->getCampo("data_vencimento") );
                $obTARRLancamento->setDado( "total_parcelas", 0 );
                $obTARRLancamento->setDado( "ativo", true );
                $obTARRLancamento->setDado( "observacao", $_REQUEST["stObservacao"] );
                $obTARRLancamento->setDado( "observacao_sistema", "" );
                $obTARRLancamento->setDado( "valor", $inValorCalculo );
                $obTARRLancamento->inclusao();

                $obConexao = new Conexao;

                $stSql = "select cod_funcao, cod_modulo, cod_biblioteca from arrecadacao.regra_desoneracao_grupo where cod_grupo = ".$arGrupoCreditoEscrituracao[0]."
                            AND ano_exercicio = '".$arGrupoCreditoEscrituracao[1]."';";

                $obConexao->executaSQL( $rsGrupoCreditoDesoneracao, $stSql, $obTransacao );

                //if desoneracao
                if ( !$rsGrupoCreditoDesoneracao->eof() ) {

                    $stSql = "select nom_funcao from administracao.funcao where cod_funcao = ".$rsGrupoCreditoDesoneracao->getCampo('cod_funcao').
                                                                                " AND cod_modulo = ".$rsGrupoCreditoDesoneracao->getCampo('cod_modulo').
                                                                                " AND cod_biblioteca = ".$rsGrupoCreditoDesoneracao->getCampo('cod_biblioteca').";";

                    $obConexao->executaSQL( $rsNomeFuncaoRegraDesoneracao, $stSql, $obTransacao );

                    $stSql = "select ".$rsNomeFuncaoRegraDesoneracao->getCampo('nom_funcao')."(".$_REQUEST["inInscricaoEconomica"].") as retorno;";
                    $obConexao->executaSQL( $rsSaidaFuncao, $stSql, $obTransacao );
                }else{
                    $rsSaidaFuncao = new RecordSet;
                }

                    $totalValor = 0;

                    for ( $inD=0; $inD<count($arCalculosParaLancar); $inD++ ) {
                        if ( $rsSaidaFuncao->getCampo('retorno') == 't' ) {

                            $stSql = "select cod_desoneracao, cod_funcao, cod_modulo, cod_biblioteca from arrecadacao.desoneracao where cod_credito = ".$arCalculosParaLancar[$inD]["credito"]." AND
                                                                            cod_natureza = ".$arCalculosParaLancar[$inD]["natureza"]." AND
                                                                            cod_genero = ".$arCalculosParaLancar[$inD]["genero"]." AND
                                                                            cod_especie = ".$arCalculosParaLancar[$inD]["especie"]." AND
                                                                            cod_tipo_desoneracao = 1 AND
                                                                            now()::date between inicio AND termino;";
                            $obConexao->executaSQL( $rsDesoneracao, $stSql, $obTransacao );

                            if ( !$rsDesoneracao->eof() ) {
                                $stSql = "select nom_funcao from administracao.funcao where cod_funcao = ".$rsDesoneracao->getCampo('cod_funcao').
                                                                                        " AND cod_modulo = ".$rsDesoneracao->getCampo('cod_modulo').
                                                                                        " AND cod_biblioteca = ".$rsDesoneracao->getCampo('cod_biblioteca').";";

                                $obConexao->executaSQL( $rsNomeFuncaoDesoneracao, $stSql, $obTransacao );

                                $stSql = "select ".$rsNomeFuncaoDesoneracao->getCampo('nom_funcao')."(".$_REQUEST["inInscricaoEconomica"].", ".$arCalculosParaLancar[$inD]["valor"].") as retorno;";

                                $obConexao->executaSQL( $rsValorFuncao, $stSql, $obTransacao );
                            }

                            $totalValor = $totalValor + $rsValorFuncao->getCampo('retorno');
                        } else {
                            $totalValor = $totalValor + $arCalculosParaLancar[$inD]["valor"];
                        }

                        $stSql = "select numcgm from arrecadacao.calculo_cgm where cod_calculo = ".$arCalculosParaLancar[$inD]["cod_calculo"].";";

                        $obConexao->executaSQL( $rsCGMCalculo, $stSql, $obTransacao );

                        $obTARRLancamentoCalculo->setDado( "cod_calculo", $arCalculosParaLancar[$inD]["cod_calculo"] );
                        $obTARRLancamentoCalculo->setDado( "cod_lancamento", $inCodLancamento );
                        //$obTARRLancamentoCalculo->setDado( "valor", $arCalculosParaLancar[$inD]["valor"] );
                        $obTARRLancamentoCalculo->setDado( "valor", $rsSaidaFuncao->getCampo('retorno') == 't' ? $rsValorFuncao->getCampo('retorno') : $arCalculosParaLancar[$inD]["valor"] );
                        $obTARRLancamentoCalculo->inclusao();

                        if ( $rsSaidaFuncao->getCampo('retorno') == 't') {
                            while ( !$rsCGMCalculo->eof() ) {

                                $obTARRDesonerado = new TARRDesonerado;

                                $obTARRDesonerado->setDado('cod_desoneracao', $rsDesoneracao->getCampo('cod_desoneracao'));
                                $obTARRDesonerado->setDado('numcgm', $rsCGMCalculo->getCampo('numcgm'));

                                $obTARRDesonerado->recuperaTodos( $rsDesonerado, $obTransacao );

                                $obTARRDesonerado->setDado('ocorrencia', count($rsDesonerado->arElementos)+1 );

                                $obTARRDesonerado->setDado('data_concessao', date("Y-m-d") );

                                $obTARRDesonerado->inclusao();

                                $obTARRDesoneradoCadEconomico = new TARRDesoneradoCadEconomico;
                                $obTARRDesoneradoCadEconomico->setDado('numcgm', $rsCGMCalculo->getCampo('numcgm'));
                                $obTARRDesoneradoCadEconomico->setDado('cod_desoneracao' , $rsDesoneracao->getCampo('cod_desoneracao'));
                                $obTARRDesoneradoCadEconomico->setDado('ocorrencia', count($rsDesonerado->arElementos)+1);
                                $obTARRDesoneradoCadEconomico->setDado('inscricao_economica', $_REQUEST['inInscricaoEconomica']);
                                $obTARRDesoneradoCadEconomico->inclusao();

                                $obTARRLancamentoUsaDesoneracao = new TARRLancamentoUsaDesoneracao;
                                $obTARRLancamentoUsaDesoneracao->setDado('cod_lancamento', $inCodLancamento);
                                $obTARRLancamentoUsaDesoneracao->setDado('cod_calculo', $arCalculosParaLancar[$inD]["cod_calculo"]);
                                $obTARRLancamentoUsaDesoneracao->setDado('cod_desoneracao', $rsDesoneracao->getCampo('cod_desoneracao'));
                                $obTARRLancamentoUsaDesoneracao->setDado('numcgm', $rsCGMCalculo->getCampo('numcgm'));
                                $obTARRLancamentoUsaDesoneracao->setDado('ocorrencia', count($rsDesonerado->arElementos)+1);
                                $obTARRLancamentoUsaDesoneracao->inclusao();

                                $rsCGMCalculo->proximo();
                            }
                        }
                        $obTARRCalculoGrupoCredito->setDado( "cod_calculo", $arCalculosParaLancar[$inD]["cod_calculo"] );
                        $obTARRCalculoGrupoCredito->setDado( "cod_grupo", $arGrupoCreditoEscrituracao[0] );
                        $obTARRCalculoGrupoCredito->setDado( "ano_exercicio", $arGrupoCreditoEscrituracao[1] );
                        $obTARRCalculoGrupoCredito->inclusao();
                    }//fim for
                    if ( $rsSaidaFuncao->getCampo('retorno') == 't' ) {
                        $obTARRLancamento->setDado( "cod_lancamento", $inCodLancamento );
                        $obTARRLancamento->setDado( "ativo", $totalValor == 0 ? false : true );
                        $obTARRLancamento->setDado( "valor", $totalValor );
                        $obTARRLancamento->alteracao();
                    }
                $obTARRCadastroEconomicoCalculo->setDado( "cod_calculo", $arCalculosParaLancar[0]["cod_calculo"] );
                $obTARRCadastroEconomicoCalculo->setDado( "inscricao_economica", $_REQUEST["inInscricaoEconomica"] );
                $obTARRCadastroEconomicoCalculo->setDado( "timestamp", $stTimeStamp );
                $obTARRCadastroEconomicoCalculo->inclusao();

//                $obTARRParcela->proximoCod( $inCodParcela );
//                $obTARRParcela->setDado( "cod_parcela", $inCodParcela );
//                $obTARRParcela->setDado( "cod_lancamento", $inCodLancamento );
//                $obTARRParcela->setDado( "nr_parcela", 1 );
//                $obTARRParcela->setDado( "vencimento", $rsListaParcela->getCampo("data_vencimento") );
//                $obTARRParcela->setDado( "valor", $inValorCalculo );
//                $obTARRParcela->inclusao();
//                if ( Sessao::read("setar_data") ) {
//                    $obTARRParcelaReemissao->setDado( "cod_parcela", $inCodParcela );
//                    $obTARRParcelaReemissao->setDado( "vencimento", $dataReemissao );
//                    $obTARRParcelaReemissao->setDado( "valor", $inValorCalculo );
//                    $obTARRParcelaReemissao->inclusao();
//                }
                if ($totalValor != 0) {
                    $obTARRParcela->proximoCod( $inCodParcela );
                    $obTARRParcela->setDado( "cod_parcela", $inCodParcela );
                    $obTARRParcela->setDado( "cod_lancamento", $inCodLancamento );
                    $obTARRParcela->setDado( "nr_parcela", 1 );
                    $obTARRParcela->setDado( "vencimento", $rsListaParcela->getCampo("data_vencimento") );
                    $obTARRParcela->setDado( "valor", $totalValor );
                    $obTARRParcela->inclusao();

                    if ( Sessao::read( "setar_data" ) ) {
                        $obTARRParcelaReemissao->setDado( "cod_parcela", $inCodParcela );
                        $obTARRParcelaReemissao->setDado( "vencimento", $dataReemissao );
                        $obTARRParcelaReemissao->setDado( "valor", $totalValor );
                        $obTARRParcelaReemissao->inclusao();
                    }

                    /*********************************************************************************/
                    // verificar convenio do grupo
                    include_once ( CAM_GT_ARR_NEGOCIO."RARRCarne.class.php"                             );
                    include_once ( CAM_GT_ARR_NEGOCIO."RARRCalculo.class.php"                             );
                    $obRARRCalculo = new RARRCalculo();
                    $obRARRCalculo->obRARRCarne = new RARRCarne();

                    $obRARRCalculo->obRARRCarne->obRMONConvenio->setCodigoConvenio( $rsListaCreditos->getCampo("cod_convenio") );
                    $obRARRCalculo->obRARRCarne->obRMONCarteira->setCodigoCarteira( $rsListaCreditos->getCampo("cod_carteira") );

                    $obRARRCalculo->obRARRCarne->obRMONConvenio->listarConvenioBanco( $rsConvenioBanco );

                    $obRARRCalculo->obRARRCarne->obRMONConvenio->obRFuncao->setCodFuncao( $rsConvenioBanco->getCampo( "cod_funcao" ) );
                    $obRARRCalculo->obRARRCarne->obRMONConvenio->obRFuncao->obRBiblioteca->setCodigoBiblioteca($rsConvenioBanco->getCampo( "cod_biblioteca" ) );
                    $obRARRCalculo->obRARRCarne->obRMONConvenio->obRFuncao->obRBiblioteca->roRModulo->setCodModulo(25);
                    $obRARRCalculo->obRARRCarne->obRMONConvenio->obRFuncao->consultar();

                    $stFNumeracao = "F".$obRARRCalculo->obRARRCarne->obRMONConvenio->obRFuncao->getNomeFuncao();
                    $stFNumeracaoMap = "../../classes/funcao/".$stFNumeracao.".class.php";
                    include_once ( $stFNumeracaoMap );
                    $obFNumeracao = new $stFNumeracao;

                    $stParametros = "'".$rsListaCreditos->getCampo("cod_carteira")."','".$rsListaCreditos->getCampo("cod_convenio")."'";
                    /********************************** fim da verificação *******************************/

                    $obFNumeracao->executaFuncao($rsRetorno,$stParametros,$boTransacao);

                    $inNumeracao = $rsRetorno->getCampo( "valor" );
                    $stExercicioCarne = $arGrupoCreditoEscrituracao[1];

                    $obTARRCarne->setDado( "numeracao", $inNumeracao );
                    $obTARRCarne->setDado( "exercicio", $stExercicioCarne );
                    $obTARRCarne->setDado( "cod_parcela", $inCodParcela );
                    $obTARRCarne->setDado( "cod_convenio", $rsListaCreditos->getCampo("cod_convenio") );
                    $obTARRCarne->setDado( "impresso", $_REQUEST["boEmissaoCarne"]?'true':'false' );
                    $obTARRCarne->inclusao();
                }
                if ( $_REQUEST['boEmissaoCarne'] == 0 )
                    SistemaLegado::alertaAviso($pgList, "Inscrição Econômica: ".$_REQUEST["inInscricaoEconomica"], "incluir", "aviso", Sessao::getId(), "../");

                Sessao::encerraExcecao();

                // imprimir carne
                if ($_REQUEST['boEmissaoCarne'] == 1 && $inCodParcela != 0) {
                    $arArqMod = explode( "§", $_REQUEST["stArquivo"] );
                    $stArquivoModelo = $arArqMod[0];
                    $inCodModelo = $arArqMod[1];

                    $arEmissao[$inCodLancamento][] = array (
                        "cod_parcela" => $inCodParcela,
                        "exercicio"   => $stExercicioCarne,
                        "inscricao"   => $_REQUEST["inInscricaoEconomica"],
                        "numeracao"   => $inNumeracao,
                        "numcgm"      => $_REQUEST["inNumCGM"],
                        "cod_modelo"  => $inCodModelo
                    );

                    $arTmp = explode( ".", $stArquivoModelo );
                    $stObjModelo = $arTmp[0];

                    Sessao::write( 'stNomPdf', ini_get("session.save_path")."/"."PdfEmissaoUrbem-".date("dmYHis").".pdf" );
                    Sessao::write( 'stParamPdf', "F" );
                    include_once( CAM_GT_ARR_CLASSES."boletos/".$stArquivoModelo );

                    $obRModeloCarne = new $stObjModelo( $arEmissao );
                    $obRModeloCarne->imprimirCarne();

                    SistemaLegado::alertaAviso($pgList, "Inscrição Econômica: ".$_REQUEST["inInscricaoEconomica"], "incluir", "aviso", Sessao::getId(), "../");

                    echo "<script type=\"text/javascript\">\r\n";
                    echo "    var sAux = window.open('".CAM_GT_ARR_INSTANCIAS."documentos/OCImpressaoPDFEmissao.php?".Sessao::getId()."','','width=20,height=10,resizable=1,scrollbars=1,left=100,top=100');\r\n";
                    echo "    eval(sAux)\r\n";
                    echo "</script>\r\n";
                } else {

                SistemaLegado::alertaAviso($pgList, "Inscrição Econômica: ".$_REQUEST["inInscricaoEconomica"], "incluir", "aviso", Sessao::getId(), "../");
            }
        }////fim do else -> if ( $_REQUEST["stEscrituracao"] == "smov" )
    break;
}
?>
