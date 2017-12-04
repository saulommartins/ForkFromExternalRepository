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
    * Página de processamento da Retencao de Fonte
    * Data de Criação   : 27/10/2006

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato

    * @ignore

    * $Id: PRReterFonte.php 63839 2015-10-22 18:08:07Z franver $

    * Casos de uso: uc-05.03.22
*/

/*
$Log$
Revision 1.3  2007/08/07 20:26:52  cercato
Bug#9837#

Revision 1.2  2007/07/05 13:25:41  cercato
Bug #9571#

Revision 1.1  2006/10/30 13:00:16  cercato
*** empty log message ***

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRCadastroEconomicoFaturamento.class.php" );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRCalculo.class.php" );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRCalculoCgm.class.php" );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRCalculoGrupoCredito.class.php" );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRCadastroEconomicoCalculo.class.php" );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRCreditoGrupo.class.php" );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRLancamento.class.php" );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRLancamentoCalculo.class.php" );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRParcela.class.php" );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRParcelaReemissao.class.php" );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRVencimentoParcela.class.php" );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRCarne.class.php" );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRRetencaoFonte.class.php" );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRRetencaoNota.class.php" );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRRetencaoServico.class.php" );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMServico.class.php" );
include_once ( CAM_GT_ARR_NEGOCIO."RARRConfiguracao.class.php" );
include_once ( CAM_GT_ARR_NEGOCIO."RARRCarne.class.php" );
include_once ( CAM_GT_ARR_NEGOCIO."RARRCalculo.class.php" );

$stAcao = $request->get('stAcao');

//Define o nome dos arquivos PHP
$stPrograma = "ReterFonte";
$pgFilt = "FL".$stPrograma.".php?stAcao=$stAcao";
$pgList = "LS".$stPrograma.".php?stAcao=$stAcao";
$pgForm = "FM".$stPrograma.".php?stAcao=$stAcao";

switch ($stAcao) {
    case "incluir":
        if ($_REQUEST["boEmissaoCarne"] && !$_REQUEST["cmbModelo"]) {
            sistemaLegado::exibeAviso("Nenhum modelo foi configurado para a emissão.", "n_erro", "erro");
            exit;
        }

        $arArqMod = explode( "§", $_REQUEST["cmbModelo"] );
        $stArquivoModelo = $arArqMod[0];
        $inCodModelo = $arArqMod[1];

        $arNotasRetencaoSessao = Sessao::read( 'notas_retencao' );
        if ( count ( $arNotasRetencaoSessao ) <= 0 ) {
            SistemaLegado::exibeAviso( "Lista de notas vazia.", "n_incluir", "erro");
            exit;
        }

        $arEmissao = array();
        $obRARRConfiguracao = new RARRConfiguracao;
        $obRARRConfiguracao->consultar();
        $stCodGrupoCreditoEscrituracao = $obRARRConfiguracao->getCodigoGrupoCreditoEscrituracao();
        $arGrupoCreditoEscrituracao = explode( "/", $stCodGrupoCreditoEscrituracao );

        $obTARRCreditoGrupo = new TARRCreditoGrupo;
        $stFiltro = " WHERE acg.cod_grupo = ".$arGrupoCreditoEscrituracao[0]." AND acg.ano_exercicio = '".$arGrupoCreditoEscrituracao[1]."'";
        $obTARRCreditoGrupo->recuperaRelacionamento( $rsListaCreditos, $stFiltro );

        if ( $rsListaCreditos->Eof() ) {
            SistemaLegado::exibeAviso( "Não existem créditos para o grupo de credito da escrituração.", "n_incluir", "erro");
            exit;
        }

        $obTARRVencimentoParcela = new TARRVencimentoParcela;
        $stFiltro = " WHERE cod_grupo = ".$arGrupoCreditoEscrituracao[0]." AND ano_exercicio = '".$arGrupoCreditoEscrituracao[1]."' AND cod_parcela = ".$_REQUEST["stCompetencia"];
        $obTARRVencimentoParcela->recuperaTodos( $rsListaParcela, $stFiltro );

        if ( $rsListaParcela->Eof() ) {
            SistemaLegado::exibeAviso( "Nenhum calendário fiscal foi definido para o grupo de credito da escrituração.", "n_incluir", "erro");
            exit;
        }

        if ( Sessao::read( 'setar_data' ) ) {
            $dataReemissao = $rsListaParcela->getCampo("data_vencimento" );
            $rsListaParcela->setCampo("data_vencimento", $_REQUEST["dtVencimento"] );
        }

        $obTARRRetencaoFonte = new TARRRetencaoFonte;
        $obTARRRetencaoNota = new TARRRetencaoNota;
        $obTARRRetencaoServico = new TARRRetencaoServico;
        $obTARRLancamento = new TARRLancamento;
        $obTARRCadastroEconomicoFaturamento = new TARRCadastroEconomicoFaturamento;
        $obTARRCalculo = new TARRCalculo;
        $obTARRCalculoCgm = new TARRCalculoCgm;
        $obTARRCalculoGrupoCredito = new TARRCalculoGrupoCredito;
        $obTARRCadastroEconomicoCalculo = new TARRCadastroEconomicoCalculo;
        $obTARRLancamentoCalculo = new TARRLancamentoCalculo;
        $obTARRParcela = new TARRParcela;
        $obTARRParcelaReemissao = new TARRParcelaReemissao;
        $obTARRCarne = new TARRCarne;
        $obTCEMServico = new TCEMServico;

        $obRARRConfiguracao = new RARRConfiguracao;
        $obRARRConfiguracao->consultar();
        $stCodGrupoCreditoEscrituracao = $obRARRConfiguracao->getCodigoGrupoCreditoEscrituracao();
        $arGrupoCreditoEscrituracao = preg_split( "/\//", $stCodGrupoCreditoEscrituracao );

        $flValorTotalRetido = 0;
        $flValorTotalRetidoSemAliquota = 0;
        foreach ($arNotasRetencaoSessao as $inChave => $arNotasRetencao) {
            $flValorTotalRetido += $arNotasRetencao["flValorRetidoEUA"];
            $flValorTotalRetidoSemAliquota += $arNotasRetencao["flValorDeclaradoEUA"]-$arNotasRetencao["flValorDeducaoEUA"];
        }

        Sessao::setTrataExcecao( true );
        Sessao::getTransacao()->setMapeamento( $obTARRCadastroEconomicoFaturamento );

            $obTARRCadastroEconomicoFaturamento->setDado( "inscricao_economica", $_REQUEST["inInscricaoEconomica"]);
            $dtCompetencia = $_REQUEST["stCompetencia"]."/".$_REQUEST["stExercicio"];
            $obTARRCadastroEconomicoFaturamento->setDado( "competencia", $dtCompetencia );
            $obTARRCadastroEconomicoFaturamento->inclusao();

            $obTARRCadastroEconomicoFaturamento->recuperaTodos( $rsLista, " WHERE inscricao_economica = ".$_REQUEST["inInscricaoEconomica"]." AND competencia = '".$dtCompetencia."' ", "timestamp DESC" );
            $stTimeStamp = $rsLista->getCampo( "timestamp" );

            $obTARRRetencaoFonte->proximoCod( $inCodRetencao );
            $obTARRRetencaoFonte->setDado( "cod_retencao", $inCodRetencao );
            $obTARRRetencaoFonte->setDado( "inscricao_economica", $_REQUEST["inInscricaoEconomica"] );
            $obTARRRetencaoFonte->setDado( "timestamp", $stTimeStamp );
            $obTARRRetencaoFonte->setDado( "valor_retencao", $flValorTotalRetidoSemAliquota );
            $obTARRRetencaoFonte->inclusao();

            foreach ($arNotasRetencaoSessao as $inChave => $arNotasRetencao) {
                $obTARRRetencaoNota->proximoCod( $inCodNota );
                $obTARRRetencaoNota->setDado( "cod_nota", $inCodNota );
                $obTARRRetencaoNota->setDado( "inscricao_economica", $_REQUEST["inInscricaoEconomica"] );
                $obTARRRetencaoNota->setDado( "timestamp", $stTimeStamp );
                $obTARRRetencaoNota->setDado( "cod_retencao", $inCodRetencao );
                $obTARRRetencaoNota->setDado( "cod_municipio", $arNotasRetencao["stMunicipio"] );
                $obTARRRetencaoNota->setDado( "cod_uf", $arNotasRetencao["stEstado"] );
                $obTARRRetencaoNota->setDado( "numcgm_retentor", $arNotasRetencao["inCGM"] );
                $obTARRRetencaoNota->setDado( "num_serie", $arNotasRetencao["inSerie"] );
                $obTARRRetencaoNota->setDado( "num_nota", $arNotasRetencao["inNumeroNota"] );
                $obTARRRetencaoNota->setDado( "dt_emissao", $arNotasRetencao["dtEmissao"] );
                $obTARRRetencaoNota->setDado( "valor_nota", $arNotasRetencao["flValorDeclaradoEUA"]-$arNotasRetencao["flValorDeducaoEUA"] );
                $obTARRRetencaoNota->inclusao();

                $inSequencia = 1;
                foreach ($arNotasRetencao["arServicos"] as $inChave2 => $arServicoRetencao) {
                    $stFiltro = " WHERE cod_estrutural = '".$arServicoRetencao["stServico"]."'";
                    $obTCEMServico->recuperaTodos( $rsListaServico, $stFiltro );

                    $obTARRRetencaoServico->setDado( "cod_nota", $inCodNota );
                    $obTARRRetencaoServico->setDado( "num_servico", $inSequencia );
                    $obTARRRetencaoServico->setDado( "inscricao_economica", $_REQUEST["inInscricaoEconomica"] );
                    $obTARRRetencaoServico->setDado( "timestamp", $stTimeStamp );
                    $obTARRRetencaoServico->setDado( "cod_retencao", $inCodRetencao );
                    $obTARRRetencaoServico->setDado( "cod_servico", $rsListaServico->getCampo("cod_servico") );
                    $obTARRRetencaoServico->setDado( "valor_declarado", $arServicoRetencao["flValorDeclarado"] );
                    if ($arServicoRetencao["flDeducao"])
                        $obTARRRetencaoServico->setDado( "valor_deducao", $arServicoRetencao["flDeducao"] );
                    else
                        $obTARRRetencaoServico->setDado( "valor_deducao", 0 );

                    $obTARRRetencaoServico->setDado( "valor_lancado", $arServicoRetencao["flValorLancadoSemAliquota"] );
                    $obTARRRetencaoServico->setDado( "aliquota", $arServicoRetencao["flAliquota"] );
                    $obTARRRetencaoServico->inclusao();

                    $inSequencia++;
                }
            }

            $inValorCalculo = number_format( $flValorTotalRetido, 2, '.', '' );

            $obTARRCalculo->proximoCod( $inCodCalculo );
            $obTARRCalculo->setDado( "cod_calculo", $inCodCalculo );
            $obTARRCalculo->setDado( "cod_credito", $rsListaCreditos->getCampo("cod_credito") );
            $obTARRCalculo->setDado( "cod_natureza", $rsListaCreditos->getCampo("cod_natureza") );
            $obTARRCalculo->setDado( "cod_genero", $rsListaCreditos->getCampo("cod_genero") );
            $obTARRCalculo->setDado( "cod_especie", $rsListaCreditos->getCampo("cod_especie") );
            $obTARRCalculo->setDado( "exercicio", Sessao::getExercicio() );
            $obTARRCalculo->setDado( "valor", $inValorCalculo );
            $obTARRCalculo->setDado( "nro_parcelas", 0 );
            $obTARRCalculo->setDado( "ativo", true );
            $obTARRCalculo->setDado( "calculado", true );
            $obTARRCalculo->inclusao();

            $obTARRCalculoCgm->setDado( "cod_calculo", $inCodCalculo );
            $obTARRCalculoCgm->setDado( "numcgm", $_REQUEST["inNumCGM"] );
            $obTARRCalculoCgm->inclusao();

            $obTARRCalculoGrupoCredito->setDado( "cod_calculo", $inCodCalculo );
            $obTARRCalculoGrupoCredito->setDado( "cod_grupo", $arGrupoCreditoEscrituracao[0] );
            $obTARRCalculoGrupoCredito->setDado( "ano_exercicio", $arGrupoCreditoEscrituracao[1] );
            $obTARRCalculoGrupoCredito->inclusao();

            $rsListaCreditos->proximo();
//----------------------
            require_once (CAM_GT_ARR_MAPEAMENTO . "FARRCalculoTributario.class.php");
            $obCalculoTributario = new FARRCalculoTributario;

            $obRARRCalculo = new RARRCalculo;
            $arCalculosParaLancar = array();
            $arCalculosParaLancar[] = array( "cod_calculo" => $inCodCalculo, "valor" => $inValorCalculo );

            while ( !$rsListaCreditos->Eof() ) {
                $obCalculoTributario->setDado( 'inRegistro', $_REQUEST["inInscricaoEconomica"] );
                $obCalculoTributario->setDado( 'inExercicio', Sessao::getExercicio() );
                $obCalculoTributario->setDado( 'stGrupo', '' );
                $obCalculoTributario->setDado( 'stCredito', $rsListaCreditos->getCampo("cod_credito").".".$rsListaCreditos->getCampo("cod_especie").".".$rsListaCreditos->getCampo("cod_genero").".".$rsListaCreditos->getCampo("cod_natureza") );
                $obCalculoTributario->setDado( 'stModulo', 14 );
                $obCalculoTributario->calculoTributario( $rsCalculo );

                if ( $rsCalculo->getCampo('retorno') == 't' ) {
                    $obRARRCalculo->buscarCalculos( $rsListaCalculos );

                    while ( !$rsListaCalculos->Eof() ) {
                        $inValorCalculo += $rsListaCalculos->getCampo( "valor" );
                        $arCalculosParaLancar[] = array( "cod_calculo" => $rsListaCalculos->getCampo( "cod_calculo" ), "valor" => $rsListaCalculos->getCampo( "valor" ) );

                        $obTARRCalculoGrupoCredito->setDado( "cod_calculo", $rsListaCalculos->getCampo("cod_calculo"));
                        $obTARRCalculoGrupoCredito->setDado( "cod_grupo", $arGrupoCreditoEscrituracao[0] );
                        $obTARRCalculoGrupoCredito->setDado( "ano_exercicio", $arGrupoCreditoEscrituracao[1] );
                        $obTARRCalculoGrupoCredito->inclusao();

                        $rsListaCalculos->proximo();
                    }
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

            for ( $inD=0; $inD<count($arCalculosParaLancar); $inD++ ) {
                $obTARRLancamentoCalculo->setDado( "cod_calculo", $arCalculosParaLancar[$inD]["cod_calculo"] );
                $obTARRLancamentoCalculo->setDado( "cod_lancamento", $inCodLancamento );
                $obTARRLancamentoCalculo->setDado( "valor", $arCalculosParaLancar[$inD]["valor"] );
                $obTARRLancamentoCalculo->inclusao();
            }

            $obTARRCadastroEconomicoCalculo->setDado( "cod_calculo", $inCodCalculo );
            $obTARRCadastroEconomicoCalculo->setDado( "inscricao_economica", $_REQUEST["inInscricaoEconomica"] );
            $obTARRCadastroEconomicoCalculo->setDado( "timestamp", $stTimeStamp );
            $obTARRCadastroEconomicoCalculo->inclusao();

            $obTARRParcela->proximoCod( $inCodParcela );
            $obTARRParcela->setDado( "cod_parcela", $inCodParcela );
            $obTARRParcela->setDado( "cod_lancamento", $inCodLancamento );
            $obTARRParcela->setDado( "nr_parcela", 0 );
            $obTARRParcela->setDado( "vencimento", $rsListaParcela->getCampo("data_vencimento") );
            $obTARRParcela->setDado( "valor", $inValorCalculo );
            $obTARRParcela->inclusao();

            if ( Sessao::read( 'setar_data' ) ) {
                $obTARRParcelaReemissao->setDado( "cod_parcela", $inCodParcela );
                $obTARRParcelaReemissao->setDado( "vencimento", $dataReemissao );
                $obTARRParcelaReemissao->setDado( "valor", $inValorCalculo );
                $obTARRParcelaReemissao->inclusao();
            }
            /*********************************************************************************/
            // verificar convenio do grupo
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

            $obTARRCarne->setDado( "numeracao", $inNumeracao );
            $obTARRCarne->setDado( "exercicio", Sessao::getExercicio() );
            $obTARRCarne->setDado( "cod_parcela", $inCodParcela );
            $obTARRCarne->setDado( "cod_convenio", $rsListaCreditos->getCampo("cod_convenio") );
            $obTARRCarne->setDado( "impresso", $_REQUEST["boEmissaoCarne"]?'true':'false' );
            $obTARRCarne->inclusao();

            $arEmissao[$inCodLancamento][]= array(
                "cod_parcela" => $inCodParcela,
                "exercicio"   => Sessao::getExercicio(),
                "numcgm"      => $_REQUEST["inNumCGM"],
                "numeracao"   => $inNumeracao,
                "inscricao"   => $_REQUEST["inInscricaoEconomica"],
                "cod_modelo"  => $inCodModelo
            );

        Sessao::encerraExcecao();

        if ($_REQUEST["boEmissaoCarne"]) {
            Sessao::write( 'stNomPdf', ini_get("session.save_path")."/"."PdfEmissaoUrbem-".date("dmYHis").".pdf" );
            Sessao::write( 'stParamPdf', "F" );

            $arTmp = explode( ".", $stArquivoModelo );
            $stObjModelo = $arTmp[0];

            include_once( CAM_GT_ARR_CLASSES."boletos/".$stArquivoModelo );

            $obRModeloCarne = new $stObjModelo( $arEmissao );
            $obRModeloCarne->imprimirCarne();
        }

        SistemaLegado::alertaAviso($pgList, "Inscrição Econômica: ".$_REQUEST["inInscricaoEconomica"], "incluir", "aviso", Sessao::getId(), "../");

        if ($_REQUEST["boEmissaoCarne"]) {
            echo "<script type=\"text/javascript\">\r\n";
            echo "    var sAux = window.open('OCImpressaoPDFEmissao.php?".Sessao::getId()."','','width=20,height=10,resizable=1,scrollbars=1,left=100,top=100');\r\n";
            echo "    eval(sAux)\r\n";
            echo "</script>\r\n";
        }

        break;
}
?>
