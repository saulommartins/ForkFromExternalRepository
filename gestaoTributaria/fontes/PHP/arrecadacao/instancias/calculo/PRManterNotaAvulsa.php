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
    * Página de processamento para nota fiscal avulsa
    * Data de Criação   : 23/06/2008

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato

    * @ignore

    * $Id: $

    * Casos de uso: uc-05.03.22
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRCadastroEconomicoFaturamento.class.php" );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRNotaServico.class.php" );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRNota.class.php" );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRNotaAvulsa.class.php" );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRTomadorEmpresa.class.php" );
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
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRLancamentoUsaDesoneracao.class.php" );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRDesonerado.class.php" );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRDesoneradoCadEconomico.class.php" );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRParcela.class.php" );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRParcelaReemissao.class.php" );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRVencimentoParcela.class.php" );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRCarne.class.php" );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMServico.class.php" );
include_once ( CAM_GT_MON_MAPEAMENTO."TMONCredito.class.php" );
include_once ( CAM_GT_ARR_NEGOCIO."RARRConfiguracao.class.php" );
include_once ( CAM_GT_ARR_NEGOCIO."RARRCalculo.class.php" );

$stAcao = $request->get('stAcao');

//Define o nome dos arquivos PHP
$stPrograma = "EscriturarReceita";
$pgFilt = "FL".$stPrograma.".php?stAcao=$stAcao";
$pgList = "LS".$stPrograma.".php?stAcao=$stAcao";
$pgForm = "FM".$stPrograma.".php?stAcao=$stAcao";

switch ($stAcao) {
    case "incluir":
        if ( ( $_REQUEST['boEmissaoCarne'] == 1 ) && ( !$_REQUEST['stArquivo'] ) ) {
            SistemaLegado::exibeAviso( "Campo Modelo de Carnê inválido.", "n_incluir", "erro" );
            exit;
        }

        if ( Sessao::read( "setar_data" ) && !$_REQUEST["dtVencimento"] ) {
            SistemaLegado::exibeAviso( "Campo Vencimento inválido.", "n_incluir", "erro" );
            exit;
        }

        if ( count( Sessao::read( "servicos_retencao_semrt" ) ) <= 0 ) {
            SistemaLegado::exibeAviso( "Lista de serviços vazia.", "n_incluir", "erro" );
            exit;
        }

        if (!$_REQUEST["dtEmissao"]) {
            SistemaLegado::exibeAviso( "Campo Data de Emissão inválido.", "n_incluir", "erro" );
            exit;
        }

        $obRARRConfiguracao = new RARRConfiguracao;
        $obRARRConfiguracao->consultar();

        $stCodGrupoNotaAvulsa = $obRARRConfiguracao->getCodigoGrupoNotaAvulsa();
        $arGrupoNotaAvulsa = preg_split( '/\//', $stCodGrupoNotaAvulsa );

        $obTARRCreditoGrupo = new TARRCreditoGrupo;
        $stFiltro = " WHERE acg.cod_grupo = ".$arGrupoNotaAvulsa[0]." AND acg.ano_exercicio = '".$arGrupoNotaAvulsa[1]."' ";
        $obTARRCreditoGrupo->recuperaRelacionamento( $rsListaCreditos, $stFiltro, " ORDER BY acg.ordem ASC " );

        if ( $rsListaCreditos->Eof() ) {
            SistemaLegado::exibeAviso( "Não existem créditos para o grupo de credito da escrituração.", "n_incluir", "erro");
            exit;
        }

        $arTMPData = explode( "/", $_REQUEST["dtEmissao"] );

        $obTARRVencimentoParcela = new TARRVencimentoParcela;
        $stFiltro = " WHERE cod_grupo = ".$arGrupoNotaAvulsa[0]." AND ano_exercicio = '".$arGrupoNotaAvulsa[1]."' AND cod_parcela = ".$arTMPData[1];
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
        $obTARRNotaAvulsa = new TARRNotaAvulsa;
        $obTARRTomadorEmpresa = new TARRTomadorEmpresa;
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

        $flTotalLancado = 0;
        $flTotalServico = 0;
        $flTotalRetido = 0;
        $stAliquota = "";
        $arServicoRetencaoSemRTSessao = Sessao::read( "servicos_retencao_semrt" );
        $nregistros = count ( $arServicoRetencaoSemRTSessao );
        for ($inX=0; $inX<$nregistros; $inX++) {
            $flTotalLancado += str_replace ( ',', '.', str_replace ( '.', '', $arServicoRetencaoSemRTSessao[$inX]["flValorLancado"] ) );
            $flTotalServico += str_replace ( ',', '.', str_replace ( '.', '', $arServicoRetencaoSemRTSessao[$inX]["flValorDeclarado"] ) );
            $flTotalRetido += str_replace ( ',', '.', str_replace ( '.', '', $arServicoRetencaoSemRTSessao[$inX]["flValorRetido"] ));
            if ($arServicoRetencaoSemRTSessao[$inX]["flAliquota"]) {
                $stAliquota .= $arServicoRetencaoSemRTSessao[$inX]["flAliquota"];
                if ( $nregistros )
                    $stAliquota .= ";";
            }
        }

        Sessao::setTrataExcecao( true );
        Sessao::getTransacao()->setMapeamento( $obTARRCadastroEconomicoFaturamento );

            $obTARRCadastroEconomicoFaturamento->setDado( "inscricao_economica", $_REQUEST["inInscricaoEconomica"]);
            $dtCompetencia = $_REQUEST["stCompetencia"]."/".$_REQUEST["stExercicio"];

            $obTARRCadastroEconomicoFaturamento->setDado( "competencia", $dtCompetencia );
            $obTARRCadastroEconomicoFaturamento->inclusao();

            $obTARRCadastroEconomicoFaturamento->recuperaTodos( $rsLista, " WHERE inscricao_economica = ".$_REQUEST["inInscricaoEconomica"]." AND competencia = '".$dtCompetencia."' ", "timestamp DESC" );
            $stTimeStamp = $rsLista->getCampo( "timestamp" );

            $inValorCalculo = 0;

            $arOcorrencias = array();
            $inTotalDeOcorrencias = 0;

            $obTARRNota->proximoCod( $inCodNota );

            $obTARRNota->setDado( "cod_nota", $inCodNota );
            $obTARRNota->setDado( "valor_nota", $flTotalServico );
            $obTARRNota->inclusao();

            $obTARRNotaAvulsa->recuperaProximoCodNotaSerie( $inCodNroNota, $inCodSerie );
            $obTARRNotaAvulsa->setDado( "cod_nota", $inCodNota );
            $obTARRNotaAvulsa->setDado( "nro_serie", $inCodSerie );
            $obTARRNotaAvulsa->setDado( "nro_nota", $inCodNroNota );
            $obTARRNotaAvulsa->setDado( "numcgm_tomador", $_REQUEST["inCGM"] );
            $obTARRNotaAvulsa->setDado( "numcgm_usuario", Sessao::read('numCgm') );
            $obTARRNotaAvulsa->setDado( "exercicio", Sessao::getExercicio() );
            $obTARRNotaAvulsa->setDado( "observacao", $_REQUEST["stObservacaoNF"] );
            $obTARRNotaAvulsa->inclusao();

            if ($_REQUEST["cmbEmpresaTomador"]) {
                $obTARRTomadorEmpresa->setDado( "cod_nota", $inCodNota );
                $obTARRTomadorEmpresa->setDado( "inscricao_economica", $_REQUEST["cmbEmpresaTomador"] );
                $obTARRTomadorEmpresa->inclusao();
            }

            $inValorCalculo = 0;
            $inOcorrencia = 0;
            $arOcorrencias = array();
            $inTotalDeOcorrencias = 0;
            foreach ($arServicoRetencaoSemRTSessao as $inChave2 => $arServicoRetencao) {
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

                $obTARRNotaServico->setDado( "cod_nota", $inCodNota );
                $obTARRNotaServico->setDado( "cod_atividade", $_REQUEST["inCodAtividade"] );
                $obTARRNotaServico->setDado( "cod_servico", $rsListaServico->getCampo("cod_servico") );
                $obTARRNotaServico->setDado( "inscricao_economica", $_REQUEST["inInscricaoEconomica"] );
                $obTARRNotaServico->setDado( "ocorrencia",$inOcorrencia);
                $obTARRNotaServico->setDado( "timestamp", $stTimeStamp );
                $obTARRNotaServico->inclusao();

                $obTARRServicoSemRetencao->setDado( "cod_atividade", $_REQUEST["inCodAtividade"] );
                $obTARRServicoSemRetencao->setDado( "cod_servico", $rsListaServico->getCampo( "cod_servico" ) );
                $obTARRServicoSemRetencao->setDado( "inscricao_economica", $_REQUEST["inInscricaoEconomica"] );
                $obTARRServicoSemRetencao->setDado( "valor_declarado", str_replace ( ',', '.', str_replace ( '.', '', $arServicoRetencao["flValorDeclarado"] ) ) );
                $obTARRServicoSemRetencao->setDado( "valor_deducao_legal", str_replace ( ',', '.', str_replace ( '.', '', $arServicoRetencao["flDeducaoLegal"] ) ) );
                if ($arServicoRetencao["flDeducao"])
                    $obTARRServicoSemRetencao->setDado( "valor_deducao", str_replace ( ',', '.', str_replace ( '.', '', $arServicoRetencao["flDeducao"] ) ) );
                else
                    $obTARRServicoSemRetencao->setDado( "valor_deducao", 0 );

                $obTARRServicoSemRetencao->setDado( "valor_lancado", str_replace ( ',', '.', str_replace ( '.', '', $arServicoRetencao["flValorLancado"] ) ) );
                $obTARRServicoSemRetencao->setDado( "aliquota", $arServicoRetencao["flAliquota"] );
                $obTARRServicoSemRetencao->setDado( "ocorrencia",$inOcorrencia);
                $obTARRServicoSemRetencao->setDado( "timestamp", $stTimeStamp );
                $obTARRServicoSemRetencao->inclusao();

                $inValorCalculo += str_replace ( ',', '.', str_replace ( '.', '', $arServicoRetencao["flValorLancado"] ) );
                $inOcorrencia++;
            }

            $obTARRCalculo->proximoCod( $inCodCalculo );
            $obTARRCalculo->setDado( "cod_calculo", $inCodCalculo );
            $obTARRCalculo->setDado( "cod_credito", $rsListaCreditos->getCampo("cod_credito") );
            $obTARRCalculo->setDado( "cod_natureza", $rsListaCreditos->getCampo("cod_natureza") );
            $obTARRCalculo->setDado( "cod_genero", $rsListaCreditos->getCampo("cod_genero") );
            $obTARRCalculo->setDado( "cod_especie", $rsListaCreditos->getCampo("cod_especie") );
            $obTARRCalculo->setDado( "exercicio", $arGrupoNotaAvulsa[1] );
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

            $rsListaCreditos->proximo();

            while ( !$rsListaCreditos->Eof() ) {
                $obCalculoTributario->setDado( 'inRegistro', $_REQUEST["inInscricaoEconomica"] );
                $obCalculoTributario->setDado( 'inExercicio', $_REQUEST["stExercicio"] );
                $obCalculoTributario->setDado( 'stGrupo', '' );
                $obCalculoTributario->setDado( 'stCredito', $rsListaCreditos->getCampo("cod_credito").".".$rsListaCreditos->getCampo("cod_especie").".".$rsListaCreditos->getCampo("cod_genero").".".$rsListaCreditos->getCampo("cod_natureza") );
                $obCalculoTributario->setDado( 'stModulo', 14 );
                $obCalculoTributario->calculoTributario( $rsCalculo );
                
                if ( $rsCalculo->getCampo('retorno') == 't' ) {
                    $obRARRCalculo->buscarCalculos( $rsListaCalculos );
                    while ( !$rsListaCalculos->Eof() ) {
                        $inValorCalculo += $rsListaCalculos->getCampo( "valor" );
                        $arCalculosParaLancar[] = array( "cod_calculo" => $rsListaCalculos->getCampo( "cod_calculo" ),
                                                         "valor" => $rsListaCalculos->getCampo( "valor" ), "credito" => $rsListaCreditos->getCampo("cod_credito"),
                                            "natureza" => $rsListaCreditos->getCampo("cod_natureza"), "genero" => $rsListaCreditos->getCampo("cod_genero"),
                                            "especie" => $rsListaCreditos->getCampo("cod_especie") );
                        $rsListaCalculos->proximo();
                    }
                } else {
                    $obRARRCalculo->buscarCalculosMensagem( $rsListaFalhas );
                    Sessao::getExcecao()->setDescricao( "Erro no calculo para escrituração ( ".$rsListaFalhas->getCampo("cod_calculo")." ".$rsListaFalhas->getCampo("mensagem")." )" );
                }

                $rsListaCreditos->proximo();
            }

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

            $stSql = "select cod_funcao, cod_modulo, cod_biblioteca from arrecadacao.regra_desoneracao_grupo where cod_grupo = ".$arGrupoNotaAvulsa[0]."
                        AND ano_exercicio = '".$arGrupoNotaAvulsa[1]."';";

            $obConexao->executaSQL( $rsGrupoCreditoDesoneracao, $stSql, $obTransacao );

            if ( !$rsGrupoCreditoDesoneracao->eof() ) {

                $stSql = "select nom_funcao from administracao.funcao where cod_funcao = ".$rsGrupoCreditoDesoneracao->getCampo('cod_funcao').
                                                                            " AND cod_modulo = ".$rsGrupoCreditoDesoneracao->getCampo('cod_modulo').
                                                                            " AND cod_biblioteca = ".$rsGrupoCreditoDesoneracao->getCampo('cod_biblioteca').";";

                $obConexao->executaSQL( $rsNomeFuncaoRegraDesoneracao, $stSql, $obTransacao );

                $stSql = "select ".$rsNomeFuncaoRegraDesoneracao->getCampo('nom_funcao')."(".$_REQUEST["inInscricaoEconomica"].") as retorno;";
                $obConexao->executaSQL( $rsSaidaFuncao, $stSql, $obTransacao );
            } else  {
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
                $obTARRCalculoGrupoCredito->setDado( "cod_grupo", $arGrupoNotaAvulsa[0] );
                $obTARRCalculoGrupoCredito->setDado( "ano_exercicio", $arGrupoNotaAvulsa[1] );
                $obTARRCalculoGrupoCredito->inclusao();
            }

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
                $stExercicioCarne = $arGrupoNotaAvulsa[1];

                $obTARRCarne->setDado( "numeracao", $inNumeracao );
                $obTARRCarne->setDado( "exercicio", $stExercicioCarne );
                $obTARRCarne->setDado( "cod_parcela", $inCodParcela );
                $obTARRCarne->setDado( "cod_convenio", $rsListaCreditos->getCampo("cod_convenio") );
                $obTARRCarne->setDado( "impresso", $_REQUEST["boEmissaoCarne"]?'true':'false' );
                $obTARRCarne->inclusao();

            }

            if ( $obRARRConfiguracao->getNotaAvulsa() == "sim" ) {
                //lancamento do valor da nota_avulsa
                $obTMONCredito = new TMONCredito;
                $stFiltro = " WHERE cod_credito = 99 AND cod_especie = 1 AND cod_genero = 2 and cod_natureza = 1 ";
                $obTMONCredito->recuperaTodos( $rsCredito, $stFiltro );

                $obCalculoTributario->setDado( 'inRegistro', $_REQUEST["inInscricaoEconomica"] );
                $obCalculoTributario->setDado( 'inExercicio', $_REQUEST["stExercicio"] );
                $obCalculoTributario->setDado( 'stGrupo', '' );
                $obCalculoTributario->setDado( 'stCredito', "99.1.2.1" );
                $obCalculoTributario->setDado( 'stModulo', 14 );
                $obCalculoTributario->calculoTributario( $rsCalculo );

                if ( $rsCalculo->getCampo('retorno') == 't' ) {
                    $obRARRCalculo->buscarCalculos( $rsListaCalculos );
                    if ( !$rsListaCalculos->Eof() ) {
                        $inValorCalculo = $rsListaCalculos->getCampo( "valor" );
                        $inCodCalculo = $rsListaCalculos->getCampo( "cod_calculo" );
                    }
                } else {
                    $obRARRCalculo->buscarCalculosMensagem( $rsListaFalhas );
                    Sessao::getExcecao()->setDescricao( "Erro no calculo para escrituração ( ".$rsListaFalhas->getCampo("cod_calculo")." ".$rsListaFalhas->getCampo("mensagem")." )" );
                }

                $arDataVencimento = explode("/", $rsListaParcela->getCampo('data_vencimento'));
                list( $dia, $mes, $ano) = $arDataVencimento;
                $dtVencimento = $ano."-".$mes."-".$dia;
                $obConexao = new Conexao;
                $stSql = " SELECT arrecadacao.fn_lancamento_manual_por_credito_intervalo( 99, 1, 2, 1, ".Sessao::read( "exercicio" ).", '".$_REQUEST['inInscricaoEconomica']." AND ".$_REQUEST['inInscricaoEconomica']."', 1, '".$dtVencimento."', '".$rsListaParcela->getCampo('percentual')."', '0', '".$rsListaParcela->getCampo('data_vencimento_descontos')."', '0', 2)  AS resultado;";

                $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql );
                $inCodLancamentoNotaAvul = str_replace(',', '', $rsRecordSet->getCampo('resultado'));

                $stSql = "SELECT cod_parcela from arrecadacao.parcela where cod_lancamento = ".$inCodLancamentoNotaAvul."; ";
                $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao);
                $inCodParcelaNotaAvul = $rsRecordSet->getCampo('cod_parcela');

                if ( $rsRecordSet->getNumLinhas() > 0 ) {
                    $stSql = "SELECT numeracao from arrecadacao.carne where cod_parcela = ".$inCodParcelaNotaAvul.";";
                    $obErro = $obConexao->executaSQL( $rsNumeracao, $stSql, $boTransacao);
                    $inNumeracaoNotaAvul = $rsNumeracao->getCampo('numeracao');
                }

           }//fim do lancamneto do valor da nota_avulsa

        if ( $_REQUEST['boEmissaoCarne'] == 0 )
            SistemaLegado::alertaAviso($pgList, "Inscrição Econômica: ".$_REQUEST["inInscricaoEconomica"], "incluir", "aviso", Sessao::getId(), "../");

        Sessao::encerraExcecao();
        // imprimir carne

        //if ( ( $_REQUEST['boEmissaoCarne'] == 1 ) && ( ( $inCodParcelaNotaAvul > 0 ) || ( $inCodParcela > 0 ) ) ) {
        if ( ($_REQUEST['boEmissaoCarne'] == 1) && ($inCodParcelaNotaAvul != 0) || ($inCodParcela != 0)) {

            $arArqMod = explode( "§", $_REQUEST["stArquivo"] );
            $stArquivoModelo = $arArqMod[0];
            $inCodModelo = $arArqMod[1];
            $arEmissao = array();

            if ($inCodParcela != 0) {
                $arEmissao[$inCodLancamento][] = array(
                    "cod_parcela" => $inCodParcela,
                    "exercicio"   => $stExercicioCarne,
                    "inscricao"   => $_REQUEST["inInscricaoEconomica"],
                    "numeracao"   => $inNumeracao,
                    "numcgm"      => $_REQUEST["inNumCGM"],
                    "cod_modelo"  => $inCodModelo
                );
            }

            if (( $obRARRConfiguracao->getNotaAvulsa() == "sim" ) && ( $inCodParcelaNotaAvul != 0 )) {

                $arEmissao[$inCodLancamentoNotaAvul][] = array(
                    "cod_parcela" => $inCodParcelaNotaAvul,
                    "exercicio"   => $stExercicioCarne,
                    "inscricao"   => $_REQUEST["inInscricaoEconomica"],
                    "numeracao"   => $inNumeracaoNotaAvul,
                    "numcgm"      => $_REQUEST["inNumCGM"],
                    "cod_modelo"  => $inCodModelo
                );
            }

            $arTmp = explode( ".", $stArquivoModelo );
            $stObjModelo = $arTmp[0];

            Sessao::write( 'stNomPdf', ini_get("session.save_path")."/"."PdfEmissaoUrbem-".date("dmYHis").".pdf" );
            Sessao::write( 'stParamPdf', "F" );
            include_once( CAM_GT_ARR_CLASSES."boletos/".$stArquivoModelo );

            if ( count($arEmissao) > 0) {
                $obRModeloCarne = new $stObjModelo( $arEmissao );
                $obRModeloCarne->imprimirCarne();
            }

            SistemaLegado::alertaAviso( "FMEmitirNotaAvulsa.php?stAcao=".$stAcao."&inCodLancamentoNotaAvul=".$inCodLancamentoNotaAvul."&inCodParcelaNotaAvul=".$inCodParcelaNotaAvul."&inscricao_economica=".$_REQUEST["inInscricaoEconomica"]."&inCodLancamento=".$inCodLancamento, "Inscrição Econômica: ".$_REQUEST["inInscricaoEconomica"], "incluir", "aviso", Sessao::getId(), "../");

            if (is_null($rsDesoneracao)) {
                echo "<script type=\"text/javascript\">\r\n";
                echo "    var sAux = window.open('".CAM_GT_ARR_INSTANCIAS."documentos/OCImpressaoPDFEmissao.php?".Sessao::getId()."','','width=20,height=10,resizable=1,scrollbars=1,left=100,top=100');\r\n";
                echo "    eval(sAux)\r\n";
                echo "</script>\r\n";
            }

        } else {
            SistemaLegado::alertaAviso( "FMEmitirNotaAvulsa.php?stAcao=".$stAcao."&inCodLancamentoNotaAvul=".$inCodLancamentoNotaAvul."&inscricao_economica=".$_REQUEST["inInscricaoEconomica"]."&inCodLancamento=".$inCodLancamento, "Inscrição Econômica: ".$_REQUEST["inInscricaoEconomica"], "incluir", "aviso", Sessao::getId(), "../");
        }

        break;
}
?>
