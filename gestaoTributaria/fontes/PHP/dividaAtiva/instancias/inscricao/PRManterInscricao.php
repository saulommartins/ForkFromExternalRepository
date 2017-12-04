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
  * Página de Formulario para Inscrição de Dívida
  * Data de criação : 27/09/2006

  * @author Analista: Fábio Bertoldi
  * @author Programador: Diego Bueno Coelho

    * $Id: PRManterInscricao.php 61417 2015-01-15 13:29:03Z evandro $

  Caso de uso: uc-05.04.02
**/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATAutoridade.class.php"             );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATModalidade.class.php"             );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATModalidadeParcela.class.php"      );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATModalidadeAcrescimo.class.php"    );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaAtiva.class.php"            );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaAtivaAuditoria.class.php"   );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaCGM.class.php"              );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaParcelaOrigem.class.php"    );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaImovel.class.php"           );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaEmpresa.class.php"          );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaParcelamento.class.php"     );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaParcelaOrigem.class.php"    );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaProcesso.class.php"         );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATParcelamento.class.php"           );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaParcela.class.php"          );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATPosicaoLivro.class.php"           );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaDocumento.class.php"        );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaDocumentoParcela.class.php" );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATEmissaoDocumento.class.php"       );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaAcrescimo.class.php"        );

include_once ( CAM_GT_ARR_MAPEAMENTO."TARRCreditoGrupo.class.php"           );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRCalculo.class.php"                );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRCalculoCgm.class.php"             );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRCadastroEconomicoCalculo.class.php");
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRImovelCalculo.class.php"          );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRLancamentoCalculo.class.php"      );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRLancamento.class.php"             );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRParcela.class.php"                );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRCarne.class.php"                  );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRCarneDevolucao.class.php"         );

include_once ( CAM_GT_MON_MAPEAMENTO."TMONCredito.class.php"                );
include_once ( CAM_GT_MON_MAPEAMENTO."TMONAcrescimo.class.php"              );
include_once ( CAM_GT_ARR_MAPEAMENTO."FCalculo.class.php"                   );
include_once ( CAM_GT_DAT_FUNCAO."FNumeracaoDivida.class.php"               );
include_once ( CAM_GT_DAT_COMPONENTES."IPopUpAutoridade.class.php"          );

//Define o nome dos arquivos PHP
$stPrograma = "ManterInscricao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once ( $pgJS );
$arModalidadeSessao = Sessao::read('modalidade');

sistemaLegado::LiberaFrames();

if ( Sessao::read('inscricaoDA') == -1 ) {
    $obErro = new Erro;
    Sessao::setTrataExcecao(true);    

    $obTDATPosicaoLivro = new TDATPosicaoLivro;
    $obTDATPosicaoLivro->recuperaPosicaoLivro( $rsListaLivro );
    if ( $rsListaLivro->Eof() ) {
        SistemaLegado::alertaAviso($pgFilt, "Configuração do livro vazia!", "n_incluir", "erro" );
        exit;
    }else
        if ( !$rsListaLivro->getCampo("valor") ) {
            SistemaLegado::alertaAviso($pgFilt, "Configuração do livro vazia!", "n_incluir", "erro" );
            exit;
        }

    Sessao::write( "inCodAutoridade"        , $_REQUEST["inCodAutoridade"]          );
    Sessao::write( "dtInscricao"            , $_REQUEST["dtInscricao"]              );
    Sessao::write( "inProcesso"             , $_REQUEST['inProcesso']               );
    Sessao::write( "boEmissaoDocumento"     , $_REQUEST["boEmissaoDocumento"]       );
    Sessao::write( "boRelatorioLancamentos" , $_REQUEST["boRelatorioLancamentos"]   );

    $arTemp = Sessao::read('lista_dividas_parcelas');
    $arListagemDividas = array();
    if ($_REQUEST["inTotalRegistros"] <= 5000) {
        foreach ($_REQUEST as $valor => $key) {
            if ( preg_match("/boIncluir_/", $valor ) ) {
                $stIncluir = $_REQUEST[$valor];
                for ( $inX=0; $inX<count( $arTemp ); $inX++ ) {
                    if ($arTemp[$inX]['cod_lancamento'] == $stIncluir) {
                        $arListagemDividas[] = array (
                            "cod_lancamento"    => $arTemp[$inX]['cod_lancamento'],
                            "origem"            => $arTemp[$inX]['vinculo'],
                            "parcelas"          => $arTemp[$inX]['parcelas'],
                            "valor_original"    => $arTemp[$inX]['valor_aberto'],
                            "valor_aberto"      => $arTemp[$inX]['valor_aberto'],
                            "valor_lancamento"  => $arTemp[$inX]['valor_lancamento'],
                            "cod_lancamento"    => $arTemp[$inX]['cod_lancamento'],
                            "numcgm"            => $arTemp[$inX]['numcgm'],
                            "nom_cgm"           => $arTemp[$inX]['nom_cgm'],
                            "vinculo"           => $arTemp[$inX]['vinculo'],
                            "id_vinculo"        => $arTemp[$inX]['id_vinculo'],
                            "inscricao"         => $arTemp[$inX]['inscricao'],
                            "tipo_inscricao"    => $arTemp[$inX]['tipo_inscricao'],
                            "vencimento_base"   => $arTemp[$inX]['vencimento_base'],
                            "vencimento_base_br"=> $arTemp[$inX]['vencimento_base_br'],
                            "timestamp_venal"   => $arTemp[$inX]['timestamp_venal'],
                            "incluir"           => 'true'
                        );
                    }
                }
            }
        }
    } else {
        for ( $inX=0; $inX<count( $arTemp ); $inX++ ) {
            $arListagemDividas[] = array (
                "cod_lancamento"    => $arTemp[$inX]['cod_lancamento'],
                "origem"            => $arTemp[$inX]['vinculo'],
                "parcelas"          => $arTemp[$inX]['parcelas'],
                "valor_original"    => $arTemp[$inX]['valor_aberto'],
                "valor_aberto"      => $arTemp[$inX]['valor_aberto'],
                "valor_lancamento"  => $arTemp[$inX]['valor_lancamento'],
                "cod_lancamento"    => $arTemp[$inX]['cod_lancamento'],
                "numcgm"            => $arTemp[$inX]['numcgm'],
                "nom_cgm"           => $arTemp[$inX]['nom_cgm'],
                "vinculo"           => $arTemp[$inX]['vinculo'],
                "id_vinculo"        => $arTemp[$inX]['id_vinculo'],
                "inscricao"         => $arTemp[$inX]['inscricao'],
                "tipo_inscricao"    => $arTemp[$inX]['tipo_inscricao'],
                "vencimento_base"   => $arTemp[$inX]['vencimento_base'],
                "vencimento_base_br"=> $arTemp[$inX]['vencimento_base_br'],
                "timestamp_venal"   => $arTemp[$inX]['timestamp_venal'],
                "incluir"           => 'true'
            );
        }
    }

    foreach ($arListagemDividas as $key => $value) {
        $stCodLancamento .= $value["cod_lancamento"].",";
    }
    $stCodLancamento = substr($stCodLancamento, 0,strlen($stCodLancamento)-1);
    //CRIA TABELA COM TODAS AS PARCELAS DE TODOS OS REGISTROS
    $obTDATDividaAtiva = new TDATDividaAtiva;
    $obTDATDividaAtiva->setDado("stCodLancamentos", $stCodLancamento);
    $obErro = $obTDATDividaAtiva->criaTabelaTodasParcelas($boTransacao);

    Sessao::write('lista_dividas_parcelas'  , $arListagemDividas            );
    Sessao::write('inscricaoDA'             , 0                             );
    Sessao::write('total_inscricaoDA'       , count( $arListagemDividas )   );
    
    Sessao::encerraExcecao();
    SistemaLegado::mudaFramePrincipal( "LSManterInscricaoSituacao.php?inCodGrupo=".$_REQUEST['inCodGrupo'].Sessao::getId()."&stAcao=incluir" );
    
} else {
    $obErro = new Erro;
    Sessao::setTrataExcecao(true);    
    
    $time_start = microtime(true); 
    
    $obTDATDividaAtiva = new TDATDividaAtiva;
    $arListagemDividas = Sessao::read( 'lista_dividas_parcelas' );

    include_once (CAM_GT_DAT_MAPEAMENTO."FDATInscricaoDivida.class.php");

    $arProcesso = explode ( '/', Sessao::read( "inProcesso" ) );
    $inCodProcesso = $arProcesso[0];
    $inExercicioProcesso = $arProcesso[1];
    //calcular de 20 em 20 registro depois atualizar a pagina
    $i = 1;        
    while( (Sessao::read('inscricaoDA') < Sessao::read('total_inscricaoDA')) ){
        $inX = Sessao::read('inscricaoDA');
        Sessao::write( 'inscricaoDA', $inX+1 );
        
        //Salva o timestamp para mostrar os dados inseridos no momento, no relatorio de inscricao
        if ( Sessao::read('inscricaoDA') == 1) {
            $obTDATDividaAtiva->recuperaTimestampInsert($rsTimestamp , $boTransacao);            
            Sessao::write('primeiro_timestamp', $rsTimestamp->getCampo('timestamp_insert'));
        }

        $obTARRLancamentoCalculo = new TARRLancamentoCalculo;
        $stFiltro = " WHERE cod_lancamento = ".$arListagemDividas[$inX]["cod_lancamento"];
        $obErro   = $obTARRLancamentoCalculo->recuperaTodos( $rsListaCalculos, $stFiltro, "", $boTransacao);

        $obTARRCalculo = new TARRCalculo;
        $stFiltro = " WHERE cod_calculo = ".$rsListaCalculos->getCampo("cod_calculo");
        $obErro   = $obTARRCalculo->recuperaTodos( $rsListaCalculo, $stFiltro ,"", $boTransacao);

        $stParametros = $arListagemDividas[$inX]["cod_lancamento"];
        $obErro       = $obTDATDividaAtiva->recuperaListaParcelasDivida( $rsParcelasDivida, $stParametros ,$boTransacao);

        /***** INÍCIO DO BLOCO QUE EXECUTA O ARREDONDAMENTO DAS PARCELAS DA DÍVIDA *****/
        $inCountCalculos = count($rsListaCalculos->arElementos);
        //VERIFICA SE EXISTEM CÁLCULOS
        if ($inCountCalculos > 0) {
            //PERCORRE ELEMENTOS DO CÁLCULO
            for ($xx = 0; $xx < $inCountCalculos; $xx++) {
                //VERIFICA SE VALOR DO CÁLCULO É MAIOR QUE 0
                if ($rsListaCalculos->arElementos[$xx]['valor'] > 0) {
                    //TRANSFERE COD_CALCULO PARA VARIÁVEL INCODCALCULO
                    $inCodCalculo = $rsListaCalculos->arElementos[$xx]['cod_calculo'];
                    //INICIALIZA VARIÁVEL QUE IRÁ SOMAR AS PARCELAS
                    $somaParcelas      = 0;
                    $somaParcelasExato = 0;
                    //PERCORRE PARCELAS DA DIVIDA
                    for ($yy = 0; $yy < count($rsParcelasDivida->arElementos); $yy++) {
                        //VERIFICA SE COD_CALCULO DA PARCELA É IGUAL COD_CALCULO DO CALCULO
                        if ($rsParcelasDivida->arElementos[$yy]['cod_calculo'] == $inCodCalculo) {
                            //EFETUA A SOMA DAS PARCELAS
                            $somaParcelas      += $rsParcelasDivida->arElementos[$yy]['valor'];
                            $somaParcelasExato += $rsParcelasDivida->arElementos[$yy]['valor_exato'];
                        }
                    }

                    $diferenca = 0;
                    $diferenca = $somaParcelas - $somaParcelasExato;

                    if ($diferenca != 0) {
                        for ($zz = 0; $zz < count($rsParcelasDivida->arElementos); $zz++) {
                            if ($rsParcelasDivida->arElementos[$zz]['cod_calculo'] == $inCodCalculo) {
                                $rsParcelasDivida->arElementos[$zz]['valor'] = $rsParcelasDivida->arElementos[$zz]['valor'] - round($diferenca,2);
                                $zz = 99999999999;
                            }
                        }
                    }

                    unset($somaParcelas);
                    unset($somaParcelasExato);
                }
            }
        }
        /***** FIM DO BLOCO QUE EXECUTA O ARREDONDAMENTO DAS PARCELAS DA DÍVIDA *****/

        $obTARRCarneDevolucao = new TARRCarneDevolucao;
        $stDataAtual = date ("d/m/Y");
        $arNumeracoes = array();
        $inTotalNumeracoes = 0;
        $obTDATDividaAtiva->recuperaListaCarnesParaCancelar( $rsListaCarnesCancelar, $rsParcelasDivida->getCampo("cod_parcela") ,$boTransacao);

        while ( !$rsListaCarnesCancelar->Eof() && !$obErro->ocorreu() ) {
            $obTARRCarneDevolucao->setDado( "cod_motivo"    , 11                                                );
            $obTARRCarneDevolucao->setDado( "numeracao"     , $rsListaCarnesCancelar->getCampo("numeracao")     );
            $obTARRCarneDevolucao->setDado( "cod_convenio"  , $rsListaCarnesCancelar->getCampo("cod_convenio")  );
            $obTARRCarneDevolucao->setDado( "dt_devolucao"  , $stDataAtual                                      );
            $obErro = $obTARRCarneDevolucao->inclusao($boTransacao);
            $rsListaCarnesCancelar->proximo();
        }

        if ($arModalidadeSessao[0]["cod_forma_inscricao"] == 4 && !$obErro->ocorreu()) { //Parcelas Individuais por Crédito
            $arParcelasDivida = $rsParcelasDivida->getElementos();
            for ( $inW=0; $inW<count($arParcelasDivida); $inW++ ) {
                $obTDATDividaAtiva->recuperaCodigoInscricaoComponente( $rsListaPosicao, $boTransacao );

                $inCodInscricao = $rsListaPosicao->getCampo( "max_inscricao" )>0?$rsListaPosicao->getCampo( "max_inscricao" )+1:1;
                unset( $obTDATPosicaoLivro );
                $obTDATPosicaoLivro = new TDATPosicaoLivro;
                $obTDATPosicaoLivro->recuperaPosicaoLivro( $rsListaLivro , $boTransacao);

                $arLivros = explode( "-", $rsListaLivro->getCampo("valor") );

                if (!$obErro->ocorreu()) {
                    $obTDATDividaAtiva->setDado( "num_livro"            , $arLivros[1]                                  );
                    $obTDATDividaAtiva->setDado( "num_folha"            , $arLivros[2]                                  );
                    $obTDATDividaAtiva->setDado( "exercicio"            , Sessao::getExercicio()                        );
                    $obTDATDividaAtiva->setDado( "cod_inscricao"        , $inCodInscricao                               );
                    $obTDATDividaAtiva->setDado( "cod_autoridade"       , Sessao::read( "inCodAutoridade" )             );
                    $obTDATDividaAtiva->setDado( "numcgm_usuario"       , Sessao::read('numCgm')                        );
                    $obTDATDividaAtiva->setDado( "dt_inscricao"         , Sessao::read( "dtInscricao" )                 );
                    $obTDATDividaAtiva->setDado( "dt_vencimento_origem" , $arListagemDividas[$inX]["vencimento_base_br"]);
                    $obTDATDividaAtiva->setDado( "exercicio_original"   , $rsListaCalculo->getCampo("exercicio")        );
                    $obTDATDividaAtiva->setDado( "exercicio_livro"      , $arLivros[3]                                  );
                    $obErro = $obTDATDividaAtiva->inclusao($boTransacao);
                }

                if (!$obErro->ocorreu()) {
                    unset( $obTDATDividaCGM );
                    $obTDATDividaCGM = new TDATDividaCGM;
                    $obTDATDividaCGM->setDado( "exercicio"     , Sessao::getExercicio()             );
                    $obTDATDividaCGM->setDado( "cod_inscricao" , $inCodInscricao                    );
                    $obTDATDividaCGM->setDado( "numcgm"        , $arListagemDividas[$inX]["numcgm"] );
                    $obErro = $obTDATDividaCGM->inclusao($boTransacao);
                }

                if (!$obErro->ocorreu() && $arListagemDividas[$inX]["tipo_inscricao"] == "IM") {
                    $obTDATDividaImovel = new TDATDividaImovel;
                    $obTDATDividaImovel->setDado( "exercicio"           , Sessao::getExercicio()                );
                    $obTDATDividaImovel->setDado( "cod_inscricao"       , $inCodInscricao                       );
                    $obTDATDividaImovel->setDado( "inscricao_municipal" , $arListagemDividas[$inX]["inscricao"] );
                    $obErro = $obTDATDividaImovel->inclusao($boTransacao);
                }else if (!$obErro->ocorreu() && $arListagemDividas[$inX]["tipo_inscricao"] == "IE") {
                    $obTDATDividaEmpresa = new TDATDividaEmpresa;
                    $obTDATDividaEmpresa->setDado( "exercicio"          , Sessao::getExercicio()                );
                    $obTDATDividaEmpresa->setDado( "cod_inscricao"      , $inCodInscricao                       );
                    $obTDATDividaEmpresa->setDado( "inscricao_economica", $arListagemDividas[$inX]["inscricao"] );
                    $obErro = $obTDATDividaEmpresa->inclusao($boTransacao);
                }

                $obTDATParcelamento = new TDATParcelamento;
                $obTDATParcelamento->recuperaNumeroParcelamento( $rsNumeroParcelamento , $boTransacao);
                $inNumeroParcelamento = $rsNumeroParcelamento->getCampo("valor");

                if (!$obErro->ocorreu()) {
                    $obTDATParcelamento->setDado( "num_parcelamento"    , $inNumeroParcelamento                     );
                    $obTDATParcelamento->setDado( "numcgm_usuario"      , Sessao::read('numCgm')                    );
                    $obTDATParcelamento->setDado( "cod_modalidade"      , $arModalidadeSessao[0]["cod_modalidade"]  );
                    $obTDATParcelamento->setDado( "timestamp_modalidade", $arModalidadeSessao[0]["timestamp"]       );
                    $obTDATParcelamento->setDado( "numero_parcelamento" , -1                                        );
                    $obTDATParcelamento->setDado( "exercicio"           , -1                                        );                    
                    $obErro = $obTDATParcelamento->inclusao($boTransacao);
                }

                if (!$obErro->ocorreu()) {
                    $obTDATDividaParcelamento = new TDATDividaParcelamento;
                    $obTDATDividaParcelamento->setDado( "num_parcelamento"  , $inNumeroParcelamento );
                    $obTDATDividaParcelamento->setDado( "exercicio"         , Sessao::getExercicio());
                    $obTDATDividaParcelamento->setDado( "cod_inscricao"     , $inCodInscricao       );
                    $obErro = $obTDATDividaParcelamento->inclusao($boTransacao);
                }

                if (!$obErro->ocorreu()) {
                    $obTDATDividaDocumento = new TDATDividaDocumento;
                    for ($inY=0; $inY<count( $arModalidadeSessao ); $inY++) {
                        if (!$obErro->ocorreu()) {
                            $obTDATDividaDocumento->setDado( "num_parcelamento"  , $inNumeroParcelamento                            );
                            $obTDATDividaDocumento->setDado( "cod_documento"     , $arModalidadeSessao[$inY]["cod_documento"]       );
                            $obTDATDividaDocumento->setDado( "cod_tipo_documento", $arModalidadeSessao[$inY]["cod_tipo_documento"]  );
                            $obErro = $obTDATDividaDocumento->inclusao($boTransacao);
                        }
                    }
                }

                if ( !$obErro->ocorreu() && Sessao::read( "inProcesso" ) ) {
                    $obTDATDividaProcesso = new TDATDividaProcesso;
                    $obTDATDividaProcesso->setDado  ( "cod_inscricao"   , $inCodInscricao        );
                    $obTDATDividaProcesso->setDado  ( "exercicio"       , Sessao::getExercicio() );
                    $obTDATDividaProcesso->setDado  ( "cod_processo"    , $inCodProcesso         );
                    $obTDATDividaProcesso->setDado  ( "ano_exercicio"   , $inExercicioProcesso   );
                    $obErro = $obTDATDividaProcesso->inclusao($boTransacao);
                }

                if (!$obErro->ocorreu()) {
                    $obTDATDividaParcelaOrigem = new TDATDividaParcelaOrigem;
                    $obTDATDividaParcelaOrigem->setDado( "cod_parcela"      , $arParcelasDivida[$inW]["cod_parcela"]  );
                    $obTDATDividaParcelaOrigem->setDado( "cod_especie"      , $arParcelasDivida[$inW]["cod_especie"]  );
                    $obTDATDividaParcelaOrigem->setDado( "cod_genero"       , $arParcelasDivida[$inW]["cod_genero"]   );
                    $obTDATDividaParcelaOrigem->setDado( "cod_natureza"     , $arParcelasDivida[$inW]["cod_natureza"] );
                    $obTDATDividaParcelaOrigem->setDado( "cod_credito"      , $arParcelasDivida[$inW]["cod_credito"]  );
                    $obTDATDividaParcelaOrigem->setDado( "num_parcelamento" , $inNumeroParcelamento                   );
                    $obTDATDividaParcelaOrigem->setDado( "valor"            , $arParcelasDivida[$inW]["valor"]        );
                    $obErro = $obTDATDividaParcelaOrigem->inclusao($boTransacao);
                }

                if (!$obErro->ocorreu()) {
                    $obTDATDividaAcrescimo = new TDATDividaAcrescimo;
                    $obErro = $obTDATDividaAcrescimo->lancarAcrescimos( Sessao::getExercicio(), $inCodInscricao, $boTransacao );
                }
            }
        }else if (!$obErro->ocorreu() && $arModalidadeSessao[0]["cod_forma_inscricao"] == 3) { //Parcelas Individuais
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
                $obTDATDividaAtiva->recuperaCodigoInscricaoComponente( $rsListaPosicao , $boTransacao);

                $inCodInscricao = $rsListaPosicao->getCampo( "max_inscricao" )>0?$rsListaPosicao->getCampo( "max_inscricao" )+1:1;

                $obTDATPosicaoLivro = new TDATPosicaoLivro;
                $obTDATPosicaoLivro->recuperaPosicaoLivro( $rsListaLivro ,$boTransacao);

                $arLivros = explode( "-", $rsListaLivro->getCampo("valor") );

                if (!$obErro->ocorreu()) {
                    $obTDATDividaAtiva->setDado( "num_livro"            , $arLivros[1]                                  );
                    $obTDATDividaAtiva->setDado( "num_folha"            , $arLivros[2]                                  );
                    $obTDATDividaAtiva->setDado( "exercicio"            , Sessao::getExercicio()                        );
                    $obTDATDividaAtiva->setDado( "cod_inscricao"        , $inCodInscricao                               );
                    $obTDATDividaAtiva->setDado( "cod_autoridade"       , Sessao::read( "inCodAutoridade" )             );
                    $obTDATDividaAtiva->setDado( "numcgm_usuario"       , Sessao::read('numCgm')                        );
                    $obTDATDividaAtiva->setDado( "dt_inscricao"         , Sessao::read( "dtInscricao" )                 );
                    $obTDATDividaAtiva->setDado( "dt_vencimento_origem" , $arListagemDividas[$inX]["vencimento_base_br"]);
                    $obTDATDividaAtiva->setDado( "exercicio_original"   , $rsListaCalculo->getCampo("exercicio")        );
                    $obTDATDividaAtiva->setDado( "exercicio_livro"      , $arLivros[3]                                  );
                    $obErro = $obTDATDividaAtiva->inclusao($boTransacao);
                }

                if (!$obErro->ocorreu()) {
                    $obTDATDividaCGM = new TDATDividaCGM;
                    $obTDATDividaCGM->setDado( "exercicio"      , Sessao::getExercicio()            );
                    $obTDATDividaCGM->setDado( "cod_inscricao"  , $inCodInscricao                   );
                    $obTDATDividaCGM->setDado( "numcgm"         , $arListagemDividas[$inX]["numcgm"]);
                    $obErro = $obTDATDividaCGM->inclusao($boTransacao);
                }

                if (!$obErro->ocorreu()&&$arListagemDividas[$inX]["tipo_inscricao"] == "IM") {
                    $obTDATDividaImovel = new TDATDividaImovel;
                    $obTDATDividaImovel->setDado( "exercicio"           , Sessao::getExercicio()                );
                    $obTDATDividaImovel->setDado( "cod_inscricao"       , $inCodInscricao                       );
                    $obTDATDividaImovel->setDado( "inscricao_municipal" , $arListagemDividas[$inX]["inscricao"] );
                    $obErro = $obTDATDividaImovel->inclusao($boTransacao);
                }else if (!$obErro->ocorreu() && $arListagemDividas[$inX]["tipo_inscricao"] == "IE") {
                    $obTDATDividaEmpresa = new TDATDividaEmpresa;
                    $obTDATDividaEmpresa->setDado( "exercicio"          , Sessao::getExercicio()                );
                    $obTDATDividaEmpresa->setDado( "cod_inscricao"      , $inCodInscricao                       );
                    $obTDATDividaEmpresa->setDado( "inscricao_economica", $arListagemDividas[$inX]["inscricao"] );
                    $obErro = $obTDATDividaEmpresa->inclusao($boTransacao);
                }

                $obTDATParcelamento = new TDATParcelamento;
                $obTDATParcelamento->recuperaNumeroParcelamento( $rsNumeroParcelamento , $boTransacao);
                $inNumeroParcelamento = $rsNumeroParcelamento->getCampo("valor");

                if (!$obErro->ocorreu()){
                    $obTDATParcelamento->setDado( "num_parcelamento"    , $inNumeroParcelamento                     );
                    $obTDATParcelamento->setDado( "numcgm_usuario"      , Sessao::read('numCgm')                    );
                    $obTDATParcelamento->setDado( "cod_modalidade"      , $arModalidadeSessao[0]["cod_modalidade"]  );
                    $obTDATParcelamento->setDado( "timestamp_modalidade", $arModalidadeSessao[0]["timestamp"]       );
                    $obTDATParcelamento->setDado( "numero_parcelamento" , -1                                        );
                    $obTDATParcelamento->setDado( "exercicio"           , -1                                        );                    
                    $obErro = $obTDATParcelamento->inclusao($boTransacao);
                }

                if (!$obErro->ocorreu()){
                    $obTDATDividaParcelamento = new TDATDividaParcelamento;
                    $obTDATDividaParcelamento->setDado( "num_parcelamento", $inNumeroParcelamento   );
                    $obTDATDividaParcelamento->setDado( "exercicio"       , Sessao::getExercicio()  );
                    $obTDATDividaParcelamento->setDado( "cod_inscricao"   , $inCodInscricao         );
                    $obErro = $obTDATDividaParcelamento->inclusao($boTransacao);
                }

                if (!$obErro->ocorreu()){
                    $obTDATDividaDocumento = new TDATDividaDocumento;
                    for ($inY=0; $inY<count($arModalidadeSessao); $inY++) {
                        if (!$obErro->ocorreu()){
                            $obTDATDividaDocumento->setDado( "num_parcelamento"  , $inNumeroParcelamento                            );
                            $obTDATDividaDocumento->setDado( "cod_documento"     , $arModalidadeSessao[$inY]["cod_documento"]       );
                            $obTDATDividaDocumento->setDado( "cod_tipo_documento", $arModalidadeSessao[$inY]["cod_tipo_documento"]  );
                            $obErro = $obTDATDividaDocumento->inclusao($boTransacao);
                        }
                    }
                }

                if ( !$obErro->ocorreu() && Sessao::read( "inProcesso" ) ) {
                    $obTDATDividaProcesso = new TDATDividaProcesso;
                    $obTDATDividaProcesso->setDado  ( "cod_inscricao" , $inCodInscricao        );
                    $obTDATDividaProcesso->setDado  ( "exercicio"     , Sessao::getExercicio() );
                    $obTDATDividaProcesso->setDado  ( "cod_processo"  , $inCodProcesso         );
                    $obTDATDividaProcesso->setDado  ( "ano_exercicio" , $inExercicioProcesso   );
                    $obErro = $obTDATDividaProcesso->inclusao ($boTransacao);
                }

                if (!$obErro->ocorreu()){
                    $obTDATDividaParcelaOrigem = new TDATDividaParcelaOrigem;
                    for ($inY=0; $inY<$arListaCreditoPorParcela[$inW]["total_de_creditos"]; $inY++) {
                        if (!$obErro->ocorreu()){
                            $obTDATDividaParcelaOrigem->setDado( "cod_parcela"      , $arListaCreditoPorParcela[$inW]["credito"][$inY]["cod_parcela" ]);
                            $obTDATDividaParcelaOrigem->setDado( "cod_especie"      , $arListaCreditoPorParcela[$inW]["credito"][$inY]["cod_especie" ]);
                            $obTDATDividaParcelaOrigem->setDado( "cod_genero"       , $arListaCreditoPorParcela[$inW]["credito"][$inY]["cod_genero"  ]);
                            $obTDATDividaParcelaOrigem->setDado( "cod_natureza"     , $arListaCreditoPorParcela[$inW]["credito"][$inY]["cod_natureza"]);
                            $obTDATDividaParcelaOrigem->setDado( "cod_credito"      , $arListaCreditoPorParcela[$inW]["credito"][$inY]["cod_credito" ]);
                            $obTDATDividaParcelaOrigem->setDado( "num_parcelamento" , $inNumeroParcelamento                                           );
                            $obTDATDividaParcelaOrigem->setDado( "valor"            , $arListaCreditoPorParcela[$inW]["credito"][$inY]["valor"       ]);
                            $obErro = $obTDATDividaParcelaOrigem->inclusao($boTransacao);
                        }
                    }
                }

                if (!$obErro->ocorreu()){
                    $obTDATDividaAcrescimo = new TDATDividaAcrescimo;
                    $obErro = $obTDATDividaAcrescimo->lancarAcrescimos( Sessao::getExercicio(), $inCodInscricao , $boTransacao);
                }
            }
        }else if (!$obErro->ocorreu() && $arModalidadeSessao[0]["cod_forma_inscricao"] == 2) { //Valor Total Por Crédito
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
                $obTDATDividaAtiva->recuperaCodigoInscricaoComponente( $rsListaPosicao , $boTransacao);

                $inCodInscricao = $rsListaPosicao->getCampo( "max_inscricao" )>0?$rsListaPosicao->getCampo( "max_inscricao" )+1:1;

                $obTDATPosicaoLivro = new TDATPosicaoLivro;
                $obTDATPosicaoLivro->recuperaPosicaoLivro( $rsListaLivro, $boTransacao);

                $arLivros = explode( "-", $rsListaLivro->getCampo("valor") );

                if (!$obErro->ocorreu()){
                    $obTDATDividaAtiva->setDado( "num_livro"            , $arLivros[1]                                  );
                    $obTDATDividaAtiva->setDado( "num_folha"            , $arLivros[2]                                  );
                    $obTDATDividaAtiva->setDado( "exercicio"            , Sessao::getExercicio()                        );
                    $obTDATDividaAtiva->setDado( "cod_inscricao"        , $inCodInscricao                               );
                    $obTDATDividaAtiva->setDado( "cod_autoridade"       , Sessao::read( "inCodAutoridade" )             );
                    $obTDATDividaAtiva->setDado( "numcgm_usuario"       , Sessao::read('numCgm')                        );
                    $obTDATDividaAtiva->setDado( "dt_inscricao"         , Sessao::read( "dtInscricao" )                 );
                    $obTDATDividaAtiva->setDado( "dt_vencimento_origem" , $arListagemDividas[$inX]["vencimento_base_br"]);
                    $obTDATDividaAtiva->setDado( "exercicio_original"   , $rsListaCalculo->getCampo("exercicio")        );
                    $obTDATDividaAtiva->setDado( "exercicio_livro"      , $arLivros[3]                                  );
                    $obErro = $obTDATDividaAtiva->inclusao($boTransacao);
                }

                if (!$obErro->ocorreu()){
                    $obTDATDividaCGM = new TDATDividaCGM;
                    $obTDATDividaCGM->setDado( "exercicio"      , Sessao::getExercicio()            );
                    $obTDATDividaCGM->setDado( "cod_inscricao"  , $inCodInscricao                   );
                    $obTDATDividaCGM->setDado( "numcgm"         , $arListagemDividas[$inX]["numcgm"]);
                    $obErro = $obTDATDividaCGM->inclusao($boTransacao);
                }

                if (!$obErro->ocorreu() && $arListagemDividas[$inX]["tipo_inscricao"] == "IM") {
                    $obTDATDividaImovel = new TDATDividaImovel;
                    $obTDATDividaImovel->setDado( "exercicio"           , Sessao::getExercicio()                );
                    $obTDATDividaImovel->setDado( "cod_inscricao"       , $inCodInscricao                       );
                    $obTDATDividaImovel->setDado( "inscricao_municipal" , $arListagemDividas[$inX]["inscricao"] );
                    $obErro = $obTDATDividaImovel->inclusao($boTransacao);
                }else if (!$obErro->ocorreu() && $arListagemDividas[$inX]["tipo_inscricao"] == "IE") {
                    $obTDATDividaEmpresa = new TDATDividaEmpresa;
                    $obTDATDividaEmpresa->setDado( "exercicio"          , Sessao::getExercicio()                );
                    $obTDATDividaEmpresa->setDado( "cod_inscricao"      , $inCodInscricao                       );
                    $obTDATDividaEmpresa->setDado( "inscricao_economica", $arListagemDividas[$inX]["inscricao"] );
                    $obErro = $obTDATDividaEmpresa->inclusao($boTransacao);
                }

                $obTDATParcelamento = new TDATParcelamento;
                $obTDATParcelamento->recuperaNumeroParcelamento( $rsNumeroParcelamento ,$boTransacao);
                $inNumeroParcelamento = $rsNumeroParcelamento->getCampo("valor");

                if (!$obErro->ocorreu()){
                    $obTDATParcelamento->setDado( "num_parcelamento"    , $inNumeroParcelamento                     );
                    $obTDATParcelamento->setDado( "numcgm_usuario"      , Sessao::read('numCgm')                    );
                    $obTDATParcelamento->setDado( "cod_modalidade"      , $arModalidadeSessao[0]["cod_modalidade"]  );
                    $obTDATParcelamento->setDado( "timestamp_modalidade", $arModalidadeSessao[0]["timestamp"]       );
                    $obTDATParcelamento->setDado( "numero_parcelamento" , -1                                        );
                    $obTDATParcelamento->setDado( "exercicio"           , -1                                        );                    
                    $obErro = $obTDATParcelamento->inclusao($boTransacao);
                }

                if (!$obErro->ocorreu()){
                    $obTDATDividaParcelamento = new TDATDividaParcelamento;
                    $obTDATDividaParcelamento->setDado( "num_parcelamento"  , $inNumeroParcelamento );
                    $obTDATDividaParcelamento->setDado( "exercicio"         , Sessao::getExercicio());
                    $obTDATDividaParcelamento->setDado( "cod_inscricao"     , $inCodInscricao       );
                    $obErro = $obTDATDividaParcelamento->inclusao($boTransacao);
                }

                if (!$obErro->ocorreu()){
                    $obTDATDividaDocumento = new TDATDividaDocumento;
                    for ($inY=0; $inY<count($arModalidadeSessao); $inY++) {
                        if (!$obErro->ocorreu()){
                            $obTDATDividaDocumento->setDado( "num_parcelamento"     , $inNumeroParcelamento                             );
                            $obTDATDividaDocumento->setDado( "cod_documento"        , $arModalidadeSessao[$inY]["cod_documento"]        );
                            $obTDATDividaDocumento->setDado( "cod_tipo_documento"   , $arModalidadeSessao[$inY]["cod_tipo_documento"]   );
                            $obErro = $obTDATDividaDocumento->inclusao($boTransacao);
                        }
                    }
                }

                if ( !$obErro->ocorreu() && Sessao::read( "inProcesso" ) ) {
                    $obTDATDividaProcesso = new TDATDividaProcesso;
                    $obTDATDividaProcesso->setDado  ( "cod_inscricao"   , $inCodInscricao       );
                    $obTDATDividaProcesso->setDado  ( "exercicio"       , Sessao::getExercicio());
                    $obTDATDividaProcesso->setDado  ( "cod_processo"    , $inCodProcesso        );
                    $obTDATDividaProcesso->setDado  ( "ano_exercicio"   , $inExercicioProcesso  );
                    $obErro = $obTDATDividaProcesso->inclusao($boTransacao);
                }

                if (!$obErro->ocorreu()){
                    $obTDATDividaParcelaOrigem = new TDATDividaParcelaOrigem;
                    $stCodParcela = "";
                    $stCodParcelaTmp = "";
                    for ($inY=0; $inY<$arListaParcelasPorCredito[$inW]["total_de_creditos"]; $inY++) {
                        $stCodParcela  = $arListaParcelasPorCredito[$inW]["credito"][$inY]["cod_parcela"]."_";
                        $stCodParcela .= $arListaParcelasPorCredito[$inW]["cod_especie"]."_";
                        $stCodParcela .= $arListaParcelasPorCredito[$inW]["cod_genero"]."_";
                        $stCodParcela .= $arListaParcelasPorCredito[$inW]["cod_natureza"]."_";
                        $stCodParcela .= $arListaParcelasPorCredito[$inW]["cod_credito"]."_";
                        $stCodParcela .= $inNumeroParcelamento;
                        if (!$obErro->ocorreu() && $stCodParcela != $stCodParcelaTmp) {
                            $obTDATDividaParcelaOrigem->setDado( "cod_parcela"      , $arListaParcelasPorCredito[$inW]["credito"][$inY]["cod_parcela"]  );
                            $obTDATDividaParcelaOrigem->setDado( "cod_especie"      , $arListaParcelasPorCredito[$inW]["cod_especie"]                   );
                            $obTDATDividaParcelaOrigem->setDado( "cod_genero"       , $arListaParcelasPorCredito[$inW]["cod_genero"]                    );
                            $obTDATDividaParcelaOrigem->setDado( "cod_natureza"     , $arListaParcelasPorCredito[$inW]["cod_natureza"]                  );
                            $obTDATDividaParcelaOrigem->setDado( "cod_credito"      , $arListaParcelasPorCredito[$inW]["cod_credito"]                   );
                            $obTDATDividaParcelaOrigem->setDado( "num_parcelamento" , $inNumeroParcelamento                                             );
                            $obTDATDividaParcelaOrigem->setDado( "valor"            , $arListaParcelasPorCredito[$inW]["credito"][$inY]["valor"]        );
                            $obErro = $obTDATDividaParcelaOrigem->inclusao($boTransacao);
                            $stCodParcelaTmp = $stCodParcela;
                        }
                    }
                    
                    if (!$obErro->ocorreu()){
                        $obTDATDividaAcrescimo = new TDATDividaAcrescimo;
                        $obErro = $obTDATDividaAcrescimo->lancarAcrescimos( Sessao::getExercicio(), $inCodInscricao ,$boTransacao);
                    }
                }

            }
        }else if (!$obErro->ocorreu() && $arModalidadeSessao[0]["cod_forma_inscricao"] == 1) { //Valor Total
            $obTDATDividaAtiva->recuperaCodigoInscricaoComponente( $rsListaPosicao, $boTransacao);
            $inCodInscricao = $rsListaPosicao->getCampo( "max_inscricao" )>0?$rsListaPosicao->getCampo( "max_inscricao" )+1:1;

            $obTDATPosicaoLivro = new TDATPosicaoLivro;
            $obTDATPosicaoLivro->recuperaPosicaoLivro( $rsListaLivro, $boTransacao);

            $arLivros = explode( "-", $rsListaLivro->getCampo("valor") );

            if (!$obErro->ocorreu()){
                $obTDATDividaAtiva->setDado( "num_livro"            , $arLivros[1]                                  );
                $obTDATDividaAtiva->setDado( "num_folha"            , $arLivros[2]                                  );
                $obTDATDividaAtiva->setDado( "exercicio"            , Sessao::getExercicio()                        );
                $obTDATDividaAtiva->setDado( "cod_inscricao"        , $inCodInscricao                               );
                $obTDATDividaAtiva->setDado( "cod_autoridade"       , Sessao::read( "inCodAutoridade" )             );
                $obTDATDividaAtiva->setDado( "numcgm_usuario"       , Sessao::read('numCgm')                        );
                $obTDATDividaAtiva->setDado( "dt_inscricao"         , Sessao::read( "dtInscricao" )                 );
                $obTDATDividaAtiva->setDado( "dt_vencimento_origem" , $arListagemDividas[$inX]["vencimento_base_br"]);
                $obTDATDividaAtiva->setDado( "exercicio_original"   , $rsListaCalculo->getCampo("exercicio")        );
                $obTDATDividaAtiva->setDado( "exercicio_livro"      , $arLivros[3]                                  );
                $obErro = $obTDATDividaAtiva->inclusao($boTransacao);
            }

            if (!$obErro->ocorreu()){
                $obTDATDividaCGM = new TDATDividaCGM;
                $obTDATDividaCGM->setDado( "exercicio"      , Sessao::getExercicio()            );
                $obTDATDividaCGM->setDado( "cod_inscricao"  , $inCodInscricao                   );
                $obTDATDividaCGM->setDado( "numcgm"         , $arListagemDividas[$inX]["numcgm"]);
                $obErro = $obTDATDividaCGM->inclusao($boTransacao);
            }

            if (!$obErro->ocorreu()&&$arListagemDividas[$inX]["tipo_inscricao"] == "IM") {
                $obTDATDividaImovel = new TDATDividaImovel;
                $obTDATDividaImovel->setDado( "exercicio"           , Sessao::getExercicio()                );
                $obTDATDividaImovel->setDado( "cod_inscricao"       , $inCodInscricao                       );
                $obTDATDividaImovel->setDado( "inscricao_municipal" , $arListagemDividas[$inX]["inscricao"] );
                $obErro = $obTDATDividaImovel->inclusao($boTransacao);
            }else if (!$obErro->ocorreu()&&$arListagemDividas[$inX]["tipo_inscricao"] == "IE") {
                $obTDATDividaEmpresa = new TDATDividaEmpresa;
                $obTDATDividaEmpresa->setDado( "exercicio"          , Sessao::getExercicio()                );
                $obTDATDividaEmpresa->setDado( "cod_inscricao"      , $inCodInscricao                       );
                $obTDATDividaEmpresa->setDado( "inscricao_economica", $arListagemDividas[$inX]["inscricao"] );
                $obErro = $obTDATDividaEmpresa->inclusao($boTransacao);
            }

            $obTDATParcelamento = new TDATParcelamento;
            $obTDATParcelamento->recuperaNumeroParcelamento( $rsNumeroParcelamento, $boTransacao );
            $inNumeroParcelamento = $rsNumeroParcelamento->getCampo("valor");

            if (!$obErro->ocorreu()){
                $obTDATParcelamento->setDado( "num_parcelamento"        , $inNumeroParcelamento                     );
                $obTDATParcelamento->setDado( "numcgm_usuario"          , Sessao::read('numCgm')                    );
                $obTDATParcelamento->setDado( "cod_modalidade"          , $arModalidadeSessao[0]["cod_modalidade"]  );
                $obTDATParcelamento->setDado( "timestamp_modalidade"    , $arModalidadeSessao[0]["timestamp"]       );
                $obTDATParcelamento->setDado( "numero_parcelamento"     , -1                                        );
                $obTDATParcelamento->setDado( "exercicio"               , -1                                        );                
                $obErro = $obTDATParcelamento->inclusao($boTransacao);
            }

            if (!$obErro->ocorreu()){
                $obTDATDividaParcelamento = new TDATDividaParcelamento;
                $obTDATDividaParcelamento->setDado( "num_parcelamento", $inNumeroParcelamento   );
                $obTDATDividaParcelamento->setDado( "exercicio"       , Sessao::getExercicio()  );
                $obTDATDividaParcelamento->setDado( "cod_inscricao"   , $inCodInscricao         );
                $obErro = $obTDATDividaParcelamento->inclusao($boTransacao);
            }

            if (!$obErro->ocorreu()){
                $obTDATDividaDocumento = new TDATDividaDocumento;
                for ($inY=0; $inY<count($arModalidadeSessao); $inY++) {
                    if (!$obErro->ocorreu()){
                        $obTDATDividaDocumento->setDado( "num_parcelamento"  , $inNumeroParcelamento                            );
                        $obTDATDividaDocumento->setDado( "cod_documento"     , $arModalidadeSessao[$inY]["cod_documento"]       );
                        $obTDATDividaDocumento->setDado( "cod_tipo_documento", $arModalidadeSessao[$inY]["cod_tipo_documento"]  );
                        $obErro = $obTDATDividaDocumento->inclusao($boTransacao);
                    }
                }
            }

            if ( !$obErro->ocorreu() && Sessao::read( "inProcesso" ) ) {
                $obTDATDividaProcesso = new TDATDividaProcesso;
                $obTDATDividaProcesso->setDado  ( "cod_inscricao"   , $inCodInscricao       );
                $obTDATDividaProcesso->setDado  ( "exercicio"       , Sessao::getExercicio());
                $obTDATDividaProcesso->setDado  ( "cod_processo"    , $inCodProcesso        );
                $obTDATDividaProcesso->setDado  ( "ano_exercicio"   , $inExercicioProcesso  );
                $obErro = $obTDATDividaProcesso->inclusao($boTransacao);
            }

            if (!$obErro->ocorreu()){
                $obTDATDividaParcelaOrigem = new TDATDividaParcelaOrigem;
                while ( !$obErro->ocorreu() && !$rsParcelasDivida->Eof() ) {
                    if ( $rsParcelasDivida->getCampo("valor") > 0.00 ) {
                        $obTDATDividaParcelaOrigem->setDado( "cod_parcela"      , $rsParcelasDivida->getCampo("cod_parcela" )   );
                        $obTDATDividaParcelaOrigem->setDado( "cod_especie"      , $rsParcelasDivida->getCampo("cod_especie" )   );
                        $obTDATDividaParcelaOrigem->setDado( "cod_genero"       , $rsParcelasDivida->getCampo("cod_genero"  )   );
                        $obTDATDividaParcelaOrigem->setDado( "cod_natureza"     , $rsParcelasDivida->getCampo("cod_natureza")   );
                        $obTDATDividaParcelaOrigem->setDado( "cod_credito"      , $rsParcelasDivida->getCampo("cod_credito" )   );
                        $obTDATDividaParcelaOrigem->setDado( "num_parcelamento" , $inNumeroParcelamento                         );
                        $obTDATDividaParcelaOrigem->setDado( "valor"            , $rsParcelasDivida->getCampo("valor")          );
                        $obErro = $obTDATDividaParcelaOrigem->inclusao($boTransacao);
                    }
                    $rsParcelasDivida->proximo();
                }
            }

            if (!$obErro->ocorreu()){
                $obTDATDividaAcrescimo = new TDATDividaAcrescimo;
                $obErro = $obTDATDividaAcrescimo->lancarAcrescimos( Sessao::getExercicio(), $inCodInscricao , $boTransacao);
            }
        } 
        
        
        if( $i == 100 ){
            Sessao::encerraExcecao();
            SistemaLegado::mudaFramePrincipal( "LSManterInscricaoSituacao.php?inCodGrupo=".$_REQUEST['inCodGrupo'].Sessao::getId()."&stAcao=incluir");            
            exit;
        }elseif( Sessao::read('total_inscricaoDA') < 100 ){
            Sessao::encerraExcecao();
            SistemaLegado::mudaFramePrincipal( "LSManterInscricaoSituacao.php?inCodGrupo=".$_REQUEST['inCodGrupo'].Sessao::getId()."&stAcao=incluir"); 
            exit;
        }    
        
        $i++;    
    }

    if (Sessao::read('inscricaoDA') == Sessao::read('total_inscricaoDA')) {
        //Abre uma transacao para salvar na auditoria os dados da inscricao
        $boFlagTransacao = false;
        $obErro = new Erro();
        $obTransacao = new Transacao();
        Sessao::setTrataExcecao(true);
        $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );    
        
        //DELETA a tabela temporaria com todas as parcelas        
        $obTDATDividaAtiva = new TDATDividaAtiva;
        $obErro = $obTDATDividaAtiva->deletaTabelaParcelas($boTransacao);

        if (!$obErro->ocorreu()) {
            $arCodModalidade = Sessao::read("modalidade");
            $arCodModalidade = $arCodModalidade[0]["cod_modalidade"];
            $obTDATDividaAtivaAuditoria = new TDATDividaAtivaAuditoria();
            $obTDATDividaAtivaAuditoria->setDado("cod_grupo", $_REQUEST["inCodGrupo"]);
            $obTDATDividaAtivaAuditoria->setDado("cod_modalidade", $arCodModalidade);
            $obTDATDividaAtivaAuditoria->setDado("exercicio", Sessao::getExercicio());
            $obTDATDividaAtivaAuditoria->setDado("total_inscritos", Sessao::read("total_inscricaoDA"));
            //Salva na auditoria
            $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTDATDividaAtivaAuditoria );
            Sessao::encerraExcecao(); 
        }else{
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_erro","erro",Sessao::getId(), "../");   
        }
    }   

    if (Sessao::read('inscricaoDA') == Sessao::read('total_inscricaoDA')) {
        $obErro = new Erro;
        $obTransacao = new Transacao();
        Sessao::setTrataExcecao(true);
        //Varre todos os dados que foram inscritos e incluidos no banco        
        $arDadosAux = Sessao::read('lista_dividas_parcelas');

        foreach ($arDadosAux as $dados) {            
            $arNumCgm[] = $dados['numcgm'];
        }
        sort($arNumCgm);
        $inNumCgm = implode(",", $arNumCgm);
        
        foreach ($arModalidadeSessao as $modalidades) {
            $inNumModalidade = $modalidades['cod_modalidade'];
        }
        
        //Se a emissao for só para uma inscricao
        if ( ( Sessao::read( "boEmissaoDocumento" ) == "on" )  && ( !Sessao::read( "boRelatorioLancamentos" ) ) ) { //boEmissaoDocumento
            Sessao::remove('stLink');
    
            $stCaminho = CAM_GT_DAT_INSTANCIAS."emissao/LSManterEmissao.php";
    
            $stParametros  = "&inExercicio=".Sessao::getExercicio();
            $stParametros .= "&stTipoModalidade=emissao";
            $stParametros .= "&inNumeroParcelamento=".$inNumeroParcelamento;
            $stParametros .= "&stCodAcao=1639";
            $stParametros .= "&stOrigemFormulario=inscricao_divida";
            $stParametros .= "&inCGM=".$inNumCgm;
            $stParametros .= "&inNumModalidade=".$inNumModalidade;
            $stParametros .= "&stDataInscricao=".Sessao::read( "dtInscricao" );
            
            Sessao::encerraExcecao();
            SistemaLegado::mudaFramePrincipal( $stCaminho."?".Sessao::getId().$stParametros."&stAcao=incluir","Inscrição de Dívida Ativa", "incluir","aviso", Sessao::getId(), "../");
            //Emitir Relatório de Inscrição em Dívida Ativa quando for mais de um inscricao
        } else if( Sessao::read( "boRelatorioLancamentos" ) ) {        
            $stParametros  = "&stExercicio=".Sessao::getExercicio();        
            $stParametros .= "&stDataInscricao=".Sessao::read( "dtInscricao" );
            $stParametros .= "&inNumModalidade=".$inNumModalidade;
            $stParametros .= "&inCodGrupo=".$_REQUEST['inCodGrupo'];
            $stParametros .= "&inCGM=muitos";
            Sessao::write('inCGM',$inNumCgm);
            
            //Pega o ultimo e o primeiro timestamp para passar para o relatorio
            //Recuperando o ultimo timestamp
            $obTDATDividaAtiva = new TDATDividaAtiva();
            $obTDATDividaAtiva->recuperaTimestampInsert($rsTimestamp , $boTransacao);
            $stParametros .= "&stPrimeiroTimestamp=".Sessao::read('primeiro_timestamp');
            $stParametros .= "&stUltimoTimestamp=".$rsTimestamp->getCampo('timestamp_insert');

            Sessao::encerraExcecao();
            $stCaminho = CAM_GT_DAT_INSTANCIAS."relatorios/PRRelatorioInscricaoDividaAtiva.php";
            SistemaLegado::alertaAviso( $stCaminho."?".Sessao::getId().$stParametros."&stAcao=incluir","Inscrição de Dívida Ativa", "incluir","aviso", Sessao::getId(), "../");
        } else {
            Sessao::encerraExcecao();
            sistemaLegado::alertaAviso( $pgFilt."?".Sessao::getId()."&stAcao=incluir","Inscrição de Dívida Ativa", "incluir","aviso", Sessao::getId(), "../");
        }// fim boEmissaoDocumento
    }
}
