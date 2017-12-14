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

  * Página de Processamento
  * Data de criação : 07/11/2005

  * @author Analista: Fábio Bertoldi
  * @author Programador: tonismar R. Bernardo

  * $Id: PREmitirCarne.php 66548 2016-09-21 13:05:07Z evandro $

  Caso de uso: uc-05.03.11

  **/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GT_ARR_NEGOCIO."RARRCarne.class.php";
include_once CAM_GT_ARR_NEGOCIO."RARRConfiguracao.class.php";
include_once CAM_GT_MON_NEGOCIO."RMONBanco.class.php";
include_once CAM_GT_MON_NEGOCIO."RMONCredito.class.php";
include_once CAM_GT_ARR_MAPEAMENTO."TARRCarneConsolidacao.class.php";
include_once CAM_GT_ARR_FUNCAO."FNumeracaoConsolidacao.class.php";
include_once CAM_GA_CGM_NEGOCIO."RCGM.class.php";
include_once CAM_GT_ARR_CLASSES."boletos/RRelatorioCarnePetropolis.class.php";

flush();

global $request;
$stAcao = $request->get('stAcao');

//Define o nome dos arquivos PHP
$stPrograma = "EmitirCarne";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

$obErro = new Erro;
$obRARRCarne = new RARRCarne;
$arEmissao = array();
$inNumCarnes = 0;
$inCE = 0;

switch ($stAcao) {
    case "devolver":
        $arCarnes = Sessao::read( 'Carnes' );
        if ($_REQUEST['stNumeracao']) {
            $boEstaNaLista = false;
            if ( is_array( $arCarnes ) ) {
                foreach ($arCarnes as $campo => $valor) {
                    if ($arCarnes[$campo]['stNumeracao'] == $_REQUEST['stNumeracao']) {
                        $boEstaNaLista = true;
                        break;
                    }
                }
            }

            if (!$boEstaNaLista) {
                $obRARRCarne->setNumeracao( $_REQUEST['stNumeracao'] );
                $obRARRCarne->setExercicio( $_REQUEST['stExercicio'] );
                $obRARRCarne->listarNomeDevolucao( $rsCarne, false );

                $arTmp['cod_convenio']= $rsCarne->getCampo('cod_convenio');
                $arTmp['stNumeracao'] = $_REQUEST['stNumeracao'];
                $arTmp['stNome']      = $rsCarne->getCampo('nom_cgm');
                $arTmp['stMotivo']    = $_REQUEST['stMotivo'];
                $arTmp['stExercicio'] = $_REQUEST['stExercicio'];
                $arTmp['inCodMotivo'] = $_REQUEST['inMotivo'];
                $arCarnes[] = $arTmp;
                Sessao::write( 'Carnes', $arCarnes );
            }
        }

        $obErro = new Erro;

        if (!count($arCarnes)) {
            $obErro->setDescricao('A lista de carnes a serem devolvidos está vazia!');
        } else {
            $obErro = $obRARRCarne->devolverCarne();
        }

        if ( !$obErro->ocorreu()) {
            sistemaLegado::alertaAviso("FMDevolverCarne.php?stAcao=devolver", "Devolução do carne executada com sucesso!","devolver","aviso", Sessao::getId(), "../");exit;
        } else {
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_erro","erro");
        }
        break;

    case "reemitir":
        // verificar grupos
        $stUltimaChave = "";
        $boPrimeiro = true; $ii = 0;
        foreach ($_REQUEST as $valor => $key) {
            if (preg_match("/^[a-z]boReemitir_[0-9]/",$valor) ) {
                $arKey = explode('§',$key);
                $stMd5 = $arKey[13];
                if ($boPrimeiro === true) {
                    $stUltimaChave = $stMd5;
                    $boPrimeiro = false;
                } else {
                    if ( $stMd5 !== $stUltimaChave ) $ii++;
                }
            }
        }
        if ($ii < 0) {
            $obErro->setDescricao('Dever ser selecionado somente parcelas com o mesmo vinculo!');
        } else {

            $inNumCarnes = 0;
            $idVinculo   = false;
            $arNaoImpressas = array();
            $obTARRCarneConsolidacao = new TARRCarneConsolidacao;
            $numeracaoConsolidacao = $dtVencimentoConsolidacao = null;
            $boConsolidacao =  false;
            $boConsolidaUnica = false;
            $arCarnesConsolidados = array();
            include_once( CAM_GT_ARR_MAPEAMENTO."TARRLancamento.class.php" );
            $obTARRLancamento = new TARRLancamento;

            if ( isset($_REQUEST['boConsolidarN']) == '1' || isset($_REQUEST['boConsolidarV']) == '1'  ) {
                $inNumInscricaoAntes = $inLancamentoAntes = null;
                $inIdVinculoAntes = null;
                $cont = 0;
                $arGruposConsolidados = array();

                foreach ($_POST as $valor => $key) {
                  
                    if ( preg_match("/^[a-z]boReemitir_[0-9]/",$valor) ) {
                        $arKey = explode('§',$key);
                        if ($cont > 0) {
                          
                            if ($inNumInscricaoAntes != $arKey[15]) {
                                $obErro->setDescricao("Não é possível consolidar parcelas de <b>inscrições diferentes</b>!" );
                                break;
                            } elseif ($inIdVinculoAntes != $arKey[14]) {
                                if ( count($arGruposConsolidados) == 0 ) {
                                    $stFiltro = " WHERE al.cod_lancamento = ".$inLancamentoAntes;
                                    $obTARRLancamento->recuperaListaConsulta ( $rsLancamento, $stFiltro, '', $boTransacao  );
                                    $stOrigem = $rsLancamento->getCampo('origem');
                                    $arGruposConsolidados[count($arGruposConsolidados)] = $stOrigem;
                                }
                                
                                $stFiltro = " WHERE al.cod_lancamento = ".$arKey[0];
                                $obTARRLancamento->recuperaListaConsulta ( $rsLancamento, $stFiltro, '', $boTransacao  );
                                $stOrigem = $rsLancamento->getCampo('origem');
                                $arGruposConsolidados[ count($arGruposConsolidados) ] = $stOrigem;
                                $boVinculosDistintos = true;
                            }
                        }
                      
                        $inNumInscricaoAntes = $arKey[15];
                        $inLancamentoAntes = $arKey[0];
                        $inIdVinculoAntes = $arKey[14];
                        $cont++;
                    }
                }
              
                if ($cont == 1) {
                    $obErro->setDescricao("Não é possível consolidar <b>apenas uma parcela</b>!" );
                }
                if ( count ($arGruposConsolidados) > 0 ) {
                    Sessao::write( 'grupos_consolidados', $arGruposConsolidados );
                } else {
                    $stFiltro = " WHERE al.cod_lancamento = ".$arKey[0];
                    $obTARRLancamento->recuperaListaConsulta ( $rsLancamento, $stFiltro, '', $boTransacao  );
                    $stOrigem = $rsLancamento->getCampo('origem');
                    $arGruposConsolidados[0] = $stOrigem;
                    Sessao::write( 'grupos_consolidados', $arGruposConsolidados );
                }
            }

            if ( !$obErro->ocorreu() ) {
                $arVinculo = Sessao::read( 'vinculo' );
                foreach ($_POST as $valor => $key) {
                    if (preg_match("/cmbModelo_[0-9]/", $valor) ) {
                        $arDados = explode( "_", $valor );
                        $arArqMod = explode( "§", $key );
                        $arVinculo[$arDados[1]-1]["arquivo"] = $arArqMod[0];
                        if (isset($arArqMod[1])) {
                            $arVinculo[$arDados[1]-1]["modelo"] = $arArqMod[1];
                        }
                    }
                }

                Sessao::write( 'vinculo', $arVinculo );

                foreach ($_POST as $valor => $key) {
                    if ( preg_match("/^[a-z]boReemitir_[0-9]/",$valor) ) {
                        $inNumCarnes++;
                        $arKey = explode('§',$key);

                        $inLancamento       = $arKey[0];
                        $inParcela          = $arKey[1];
                        $inCodConvenio      = $arKey[2];
                        $inCodCarteira      = $arKey[3];
                        $stExercicio        = $arKey[4];
                        $inCodConvenioAtual = $arKey[5];
                        $inCodCarteiraAtual = $arKey[6];
                        $numeracao          = $arKey[7];
                        $dtVencimento       = $arKey[8];
                        $flValorAnterior    = str_replace(',','.',str_replace('.','',$arKey[9]));
                        $stInfoParcela      = $arKey[10];
                        $inNumCgm           = $arKey[11];
                        $boImpresso         = $arKey[12];
                        $idVinculo          = $arKey[14];
                        $inInscricao        = $arKey[15];

                        $arNumCgm = explode('/', $inNumCgm);

                        foreach ($arNumCgm as $key => $inCodCgm) {
                            $obRCGM = new RCGM;
                            $obRCGM->setNumCGM     ( $inCodCgm );
                            $obRCGM->listar( $rsCGM , $boTransacao="" );

                            $obRARRConfiguracao = new RARRConfiguracao;
                            $obRARRConfiguracao->setCodModulo("");
                            $obRARRConfiguracao->setExercicio( Sessao::getExercicio() );
                            $obRARRConfiguracao->consultar();
                            $stEmitirCarne  = $obRARRConfiguracao->getEmissaoCarne();

                            if ( ($rsCGM->getCampo( "cpf") == "") AND ($rsCGM->getCampo( "cnpj") == "") AND ( $stEmitirCarne == "naoemitir") ) {
                                sistemaLegado::exibeAviso("CGM ".$inCodCgm." não possui dados preenchidos referente ao seu CPF/CNPJ! Emissão cancelada.", "n_erro", "erro");
                                exit;
                            }
                        }

                        $md5 = $arKey[13];
                        $hoje = date('Ymd');
                        $arTmp = explode('/',$dtVencimento);
                        $stData = $arTmp[2].$arTmp[1].$arTmp[0];

                        if ( isset($_REQUEST['boConsolidarN']) == '1' || isset($_REQUEST['boConsolidarV']) == '1'  ) {
                            $boConsolidacao = true;

                            if (!$numeracaoConsolidacao) {
                                $obRARRCarne->obRMONConvenio->setCodigoConvenioConsolidacao( true );
                                $obRARRCarne->obRMONCarteira->setCodigoCarteira( null );
                                $obRARRCarne->obRMONConvenio->verificaConvenioBanco ( $rsConvenioBanco );

                                 $obRARRCarne->obRMONConvenio->obRFuncao->setCodFuncao( $rsConvenioBanco->getCampo( "cod_funcao" ) );
                                 $obRARRCarne->obRMONConvenio->obRFuncao->obRBiblioteca->setCodigoBiblioteca( $rsConvenioBanco->getCampo( "cod_biblioteca" ) );
                                 $obRARRCarne->obRMONConvenio->obRFuncao->obRBiblioteca->roRModulo->setCodModulo( $rsConvenioBanco->getCampo( "cod_modulo" ) );
                                 $obRARRCarne->obRMONConvenio->obRFuncao->consultar();
                                 
                                 $stFNumeracaoConsolidacao = "F".$obRARRCarne->obRMONConvenio->obRFuncao->getNomeFuncao();
                                 $obFNumeracaoConsolidacao = new $stFNumeracaoConsolidacao;
                                 
                                 $stParametros = 0;
                                 $rsRetorno = new RecordSet;
                                 $obErro = $obFNumeracaoConsolidacao->executaFuncao($rsRetorno,$stParametros,$boTransacao);
                                 $numeracaoConsolidacao = $rsRetorno->getCampo ('valor');
                                 $dtVencimentoConsolidacao = $_REQUEST['dtNovoVencimentoV'];
                            }
                        }

                        if ($boConsolidacao) {
                            $arInfo = explode ( '/', $stInfoParcela );
                            $nrParcelaAtual = $arInfo[0];

                            if ($nrParcelaAtual == "Única") {
                                $boConsolidaUnica = true;
                                $nrParcelaAtual = 0;
                            }
                            
                            if ($inLancamentoAnterior == $inLancamento) {
                                if ( $boConsolidaUnica && ( $nrParcelaAtual != 0 ) ) {
                                    $obErro->setDescricao('Parcela única e parcela normal do mesmo lançamento não podem ser consolidadas!');
                                }
                            } elseif ( ($inLancamentoAnterior != $inLancamento) && $inLancamentoAnterior ) {
                                $boConsolidaUnica = false;
                            }

                            $inLancamentoAnterior = $inLancamento;

                            if ( !$obErro->ocorreu() ) {
                                $arEmissao[ $inLancamento ][] = array (
                                    "cod_parcela" => $inParcela,
                                    "exercicio"   => $stExercicio,
                                    "numcgm"      => $inNumCgm,
                                    "numeracao"     => $numeracaoConsolidacao,                                   
                                    "inscricao"     => $inInscricao
                                );
                                $obRARRCarne->obRARRParcela->AtualizaVencimentoParcela ( $inParcela, $nrParcelaAtual,  $boTransacao );
                            }

                        } elseif ( ( $boImpresso == 't' && $hoje <= $stData ) || ( $hoje > $stData ) ) {

                            if (!$inCodConvenioAtual || $inCodConvenioAtual == "") {
                                $obErro->setDescricao ( "Convênio Atual dos Créditos dos Carnês marcados para reemissão não informado." );
                            }

                            if ($stInfoParcela == "Única") {
                                $nrParcela = "0";
                            } else {
                                $arParcela = explode( "/" , $stInfoParcela );
                                $nrParcela = $arParcela[0];
                            }

                            //pegar novo vencimento 
                            $arTmp = explode("_",$valor);
                            $stTmp = "dtNovoVencimento_".$arTmp[1];
                            
                            //Caso usuario não passe data de vencimento 
                            //e for marcada para reemissao será executada apenas a reemissao sem calculo de juros ou multa algum
                            $boApenasReemitir = false;  
                            if ( preg_match("/vboReemitir_[0-9]/",$valor) ) {
                                if ( $_REQUEST[$stTmp] == '' ) {
                                    $boApenasReemitir = true;
                                    $dtNovoVencimento = $dtVencimento;
                                }else{
                                    $dtNovoVencimento = $_REQUEST[$stTmp];
                                }
                            } else {
                                $dtNovoVencimento = $dtVencimento;

                            }
                            
                            $arTmp = explode('/',$dtNovoVencimento);
                            $dtNovoVencimentoUs = $arTmp[2].'-'.$arTmp[1].'-'.$arTmp[0];

                            // aplica juro e multa caso necessario 
                            include_once(CAM_GT_ARR_MAPEAMENTO."Faplica_juro_reemissao.class.php");
                            include_once(CAM_GT_ARR_MAPEAMENTO."Faplica_multa_reemissao.class.php");
                            include_once(CAM_GT_ARR_MAPEAMENTO."Ftotal_parcelas.class.php");

                            $obJuro         = new Faplica_juro_reemissao   ;
                            $obMulta        = new Faplica_multa_reemissao  ;
                            $obTotalParcela = new Ftotal_parcelas;

                            $obErro          = $obTotalParcela->executaFuncao($rsTmp, $inLancamento);
                            $inTotalParcelas = $rsTmp->getCampo('valor'); // total de parcelas

                            $stParametros   = '\''.$numeracao.'\','.$stExercicio.','.$inParcela.',\''.$dtNovoVencimentoUs.'\'';
                            $obErro         = $obJuro->executaFuncao($rsTmp, $stParametros) ;                          
                            $flJuros        = $rsTmp->getCampo('valor'); // valor dos juros
                            $obErro         = $obMulta->executaFuncao($rsTmp, $stParametros) ;
                            $flMulta        = $rsTmp->getCampo('valor'); // valor da multa

                            $flValor = round($flValorAnterior + $flJuros + $flMulta,2);

                            $obRARRCarne->obRMONConvenio->setCodigoConvenio( $inCodConvenioAtual );
                            $obRARRCarne->obRMONCarteira->setCodigoCarteira( $inCodCarteiraAtual );
                            $obRARRCarne->obRARRParcela->setCodParcela( $inParcela );

                            $obRARRCarne->obRMONConvenio->listarConvenioBanco( $rsConvenioBanco );
                            $obRARRCarne->obRMONConvenio->obRFuncao->setCodFuncao( $rsConvenioBanco->getCampo( "cod_funcao" ) );
                            $obRARRCarne->obRMONConvenio->obRFuncao->obRBiblioteca->setCodigoBiblioteca( $rsConvenioBanco->getCampo( "cod_biblioteca" ) );
                            $obRARRCarne->obRMONConvenio->obRFuncao->obRBiblioteca->roRModulo->setCodModulo(25);
                            $obRARRCarne->obRMONConvenio->obRFuncao->consultar();

                            if ($inCodConvenio == -1) {
                                $inCodConvenioAtual = $inCodConvenio;
                                $stFNumeracaoMap = CAM_GT_DAT_FUNCAO."FNumeracaoDivida.class.php";
                                $stFNumeracao = "FNumeracaoDivida";
                                
                                include_once ( $stFNumeracaoMap );
                                $obFNumeracao = new $stFNumeracao;

                                if (!$inCodCarteiraAtual) {
                                    $inCodCarteiraAtual = null;
                                }

                                $stParametros = '-1';
                                $obRARRCarne->setExercicio( $stExercicio );
                                $obErro = $obFNumeracao->executaFuncao($rsRetorno,$stParametros,$boTransacao);
                            } else {
                                $stFNumeracao = "F".$obRARRCarne->obRMONConvenio->obRFuncao->getNomeFuncao();
                                $stFNumeracaoMap = "../../classes/funcao/".$stFNumeracao.".class.php";
                                
                                include_once ( $stFNumeracaoMap );
                                $obFNumeracao = new $stFNumeracao;

                                if (!$inCodCarteiraAtual) {
                                    $inCodCarteiraAtual = null;
                                }

                                $stParametros = "'".$inCodCarteiraAtual."','".$inCodConvenioAtual."'";
                                $obRARRCarne->setExercicio( $stExercicio );
                                $obErro = $obFNumeracao->executaFuncao($rsRetorno,$stParametros,$boTransacao);
                            }

                            if ( !$obErro->ocorreu() ) {
                                $inNumeracao = $rsRetorno->getCampo( "valor" );
                                $obRARRCarne->setNumeracao( $inNumeracao );
                                $obRARRCarne->setExercicio( $stExercicio );

                                $obRARRCarne->stCodContribuinteConjunto= str_replace('/', ',', $inNumCgm);
                                $obRARRCarne->obRARRParcela->setCodParcela( $inParcela );
                                $obRARRCarne->obRARRParcela->setVencimento( $dtNovoVencimento );
                                $obRARRCarne->obRARRParcela->setNrParcela( $nrParcela);
                                $obRARRCarne->obRMONConvenio->setCodigoConvenio( $inCodConvenioAtual);
                                $obRARRCarne->obRMONCarteira->setCodigoCarteira( $inCodCarteiraAtual );

                                $arReemissao = array(   "cod_convenio"     => $inCodConvenio,
                                                        "cod_carteira"     => $inCodCarteira,
                                                        "cod_lancamento"   => $inLancamento,
                                                        "info_parcela"     => $nrParcela,
                                                        "cod_parcela"      => $inParcela,
                                                        "vencimento"       => $dtVencimento,
                                                        "valor_anterior"   => $flValorAnterior,
                                                        "valor"            => $flValor,
                                                        "numeracao"        => $numeracao,
                                                        "numcgm"           => $inNumCgm,
                                                         );
                                
                                $obErro = $obRARRCarne->efetuaReemitirCarne( $arReemissao, $boTransacao );

                                if ( $obErro->ocorreu() ) {
                                    break;
                                }
                                // adicionar para impressao
                                $arEmissao[$inLancamento][]= array(
                                                                                            "cod_parcela" => $inParcela,
                                                                                            "exercicio"   => $stExercicio,
                                                                                            "numcgm"      => $inNumCgm,
                                                                                            "numeracao"   => $inNumeracao,
                                                                                            "inscricao"   => $inInscricao,
                                                                                            "boApenasReemitir" => $boApenasReemitir
                                                                                            );
                            }
                        } else {
                            // NAO IMPRESSO // fim if impresso
                            $arEmissao[$inLancamento][]= array(
                                                                                        "cod_parcela" => $inParcela,
                                                                                        "exercicio"   => $stExercicio,
                                                                                        "numcgm"      => $inNumCgm,
                                                                                        "numeracao"   => $numeracao,
                                                                                        "inscricao"   => $inInscricao,
                                                                                        "boApenasReemitir" => $boApenasReemitir
                                                                                        );

                            $arNaoImpressas[] = array( "numeracao"     => $numeracao,
                                                                        "cod_convenio"  => $inCodConvenio,
                                                                        "cod_parcela"   => $inParcela,
                                                                        "exercicio"     => $stExercicio,
                                                                        "inscricao"     => $inInscricao,
                                                                        "boApenasReemitir" => $boApenasReemitir
                                                                        );
                        }

                        if ( !$obErro->ocorreu() && $boConsolidacao  ) {
                            $obTARRCarneConsolidacao->setDado ( "numeracao_consolidacao", $numeracaoConsolidacao   );
                            $obTARRCarneConsolidacao->setDado ( "numeracao"     ,  $numeracao       );
                            $obTARRCarneConsolidacao->setDado ( "cod_convenio"  , $inCodConvenio     );
                            $obErro = $obTARRCarneConsolidacao->inclusao();
                       
                            
                        }

                        $inNumCarnes++;
                    }
                }// fim do foreach
            }//fim do verifica $obErro
        }

        // BUSCAR ADQUIRENTES
        include_once (CAM_GT_ARR_MAPEAMENTO."TARRLancamento.class.php");
        $obTARRLancamento = new TARRLancamento;

        $iLancamento = 0;

        foreach ($arEmissao as $lancamento => $index) {
            $obTARRLancamento->recuperaTodos($rsLancamento, " WHERE cod_lancamento = ".$lancamento);
        }

        include_once (CAM_GT_ARR_MAPEAMENTO."TARRLancamentoCalculo.class.php");
        $obTARRLancamentoCalculo = new TARRLancamentoCalculo;
        if ($rsLancamento->arElementos != "") {
            foreach ($rsLancamento->arElementos as $campo => $index) {
                $obTARRLancamentoCalculo->recuperaTodos($rsLancamentoCalculo, " WHERE cod_lancamento = ".$index['cod_lancamento']." AND valor = ".$index['valor']."");
            }
        }

        include_once (CAM_GT_ARR_MAPEAMENTO."TARRCalculo.class.php");
        $obCalculo = new TARRCalculo;
        if ($rsLancamentoCalculo->arElementos != "") {
            foreach ($rsLancamentoCalculo->arElementos as $campo => $index) {
                $obCalculo->recuperaRelacionamento($rsCalculo, " WHERE C.cod_calculo = ".$index['cod_calculo']);
            }
        }
        include_once (CAM_GT_CIM_MAPEAMENTO."TCIMTransferenciaImovel.class.php");
        $obImovel = new TCIMTransferenciaImovel;
        if (isset($rsCalculo->arElementos)) {
            foreach ($rsCalculo->arElementos as $campo => $index) {
                $obImovel->recuperaTodos($rsImovel, " WHERE inscricao_municipal = ".$index['inscricao_municipal']." AND cod_natureza = ".$index['cod_natureza']." AND dt_cadastro = '".$index['timestamp']."'");
            }
        }

        include_once (CAM_GT_CIM_MAPEAMENTO."TCIMTransferenciaAdquirente.class.php");
        $obAdquirente = new TCIMTransferenciaAdquirente;
        if (isset($rsImovel->arElementos)) {
            foreach ($rsImovel->arElementos as $campo => $index) {
                $obAdquirente->recuperaTodos($rsAdquirente, " WHERE cod_transferencia = ".$index['cod_transferencia']);
            }
        }
        if (isset($rsAdquirente->arElementos)) {
            $arAdquirente = $rsAdquirente->arElementos;
        }
        $iKey = 0;
        foreach ($arEmissao as $emissao => $index) {
            if (isset($arAdquirente)) {
                $arEmissao[$emissao][$iKey]['cod_transferencia'] = $arAdquirente[$iKey]['cod_transferencia'];
                $arEmissao[$emissao][$iKey]['numcgm_adquirente'] = $arAdquirente[$iKey]['numcgm'];
                $arEmissao[$emissao][$iKey]['ordem'] = $arAdquirente[$iKey]['ordem'];
                $arEmissao[$emissao][$iKey]['cota'] = $arAdquirente[$iKey]['cota'];

                $iKey++;
            }
        }

        // imprimir carnes
        if ( !$obErro->ocorreu() ) {

            $rsEmissaoCarne = new Recordset();
            $rsEmissaoCarne->preenche($arNaoImpressas);
            $boExec = TRUE;
        
            /*
             *   grava nome pdf e parametro para salvar em disco
             *   usado tambem no objeto pdf
             */
        
            $stNomPdf = ini_get("session.save_path")."/"."PdfEmissaoUrbem-".date("dmYHis").".pdf";
            Sessao::write( 'stNomPdf', $stNomPdf );
            Sessao::write( 'stParamPdf', "F" );

            $inQtdVinculo = Sessao::read( 'qtd_vinculo' );
            $arVinculo = Sessao::read( 'vinculo' );
            for ($inX=0; $inX<$inQtdVinculo; $inX++) {
                if ($arVinculo[$inX]["vinculo"] == $idVinculo) {
                    $stArquivoModelo = $arVinculo[$inX]["arquivo"];
                    if (isset($arVinculo[$inX]["modelo"])) {
                        $inCodModeloArquivo = $arVinculo[$inX]["modelo"];
                    }
                    $arTmp = explode( ".", $stArquivoModelo );
                    $stObjModelo = $arTmp[0];
                    $boEncontrouModelo = true;
                    break;
                }
            }
            if (!$stArquivoModelo) {
                sistemaLegado::exibeAviso("Nenhum modelo foi configurado para a Origem: '".$idVinculo."'.", "n_erro", "erro");
                exit;
            }

            foreach ($arEmissao as $valor => $chave) {
                $arEmissao[$valor][0]["cod_modelo"] = $inCodModeloArquivo;
            }

            include_once( CAM_GT_ARR_CLASSES."boletos/".$stArquivoModelo );
            $obRModeloCarne = new $stObjModelo( $arEmissao );
            if ($boConsolidacao) {
                $obRModeloCarne->setConsolidacao ( true );
                $obRModeloCarne->setNumeracaoConsolidacao ( $numeracaoConsolidacao );
                $obRModeloCarne->setVencimentoConsolidacao ( $dtVencimentoConsolidacao );
             }
         
            $obRModeloCarne->imprimirCarne();

            include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
            include_once (CAM_GT_ARR_MAPEAMENTO."TARRCarne.class.php");
            $rsEmissaoCarne->setPrimeiroElemento();
            while ( !$rsEmissaoCarne->eof() ) {
                $obTARRCarne = new TARRCarne;
                $obTARRCarne->setDado ( "numeracao"     , $rsEmissaoCarne->getCampo('numeracao')        );
                $obTARRCarne->setDado ( "cod_convenio"  , $rsEmissaoCarne->getCampo('cod_convenio')     );
                $obTARRCarne->setDado ( "cod_parcela"   , $rsEmissaoCarne->getCampo('cod_parcela')      );
                $obTARRCarne->setDado ( "exercicio"     , $rsEmissaoCarne->getCampo('exercicio')        );
                $obTARRCarne->setDado ( "impresso"      , TRUE                                          );
                $obErro = $obTARRCarne->alteracao();
                $rsEmissaoCarne->proximo();
            }
        }
        include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

        if (!$obErro->ocorreu() ) {
            $inNumCarnes = $inNumCarnes/2;
            SistemaLegado::alertaAviso($pgList."?stAcao=reemitir","$inNumCarnes Carnês Emitidos","reemitir","aviso", Sessao::getId(), "../");
        } else {
                SistemaLegado::alertaAviso($pgList."?stAcao=reemitir",urlencode($obErro->getDescricao()),"n_incluir","erro",Sessao::getId(), "../");
        }

        if (!$obErro->ocorreu() &&  $boExec ) {
            echo "<script type=\"text/javascript\">\r\n";
            echo "    var sAux = window.open('OCImpressaoPDFEmissao.php?".Sessao::getId()."','','width=20,height=10,resizable=1,scrollbars=1,left=100,top=100');\r\n";
            echo "    eval(sAux)\r\n";
            echo "</script>\r\n";
        }
        //SistemaLegado::mostravar('uhafuihaeuifh'); die();
    break;

    case "emitir":
        if ($_REQUEST['emissao_carnes'] == 'local') {
            $arModelo = explode( "§", $_REQUEST["cmbModelo"] );
            $stArquivoModelo = $arModelo[0];
            $arTmp = explode( ".", $stArquivoModelo );
            $stObjModelo = $arTmp[0];
            $inCodModelo = $arModelo[1];
            // carne nao impresso
            if (!$stArquivoModelo) {
                sistemaLegado::exibeAviso("Nenhum modelo de carne foi configurado!", "n_erro", "erro");
                exit;
            }
        }

        Sessao::write( 'boEmissaoImpresso', 'FALSE' );
        // validar grupo e credito
        if (!$_REQUEST['inCodGrupo'] && !$_REQUEST['inCodCredito']) {
            $obErro->setDescricao('Deve ser informado um Crédito ou Grupo de Crédito');
            SistemaLegado::alertaAviso($pgForm."?stAcao=emitir",urlencode($obErro->getDescricao()),"n_incluir","erro",Sessao::getId(), "../");
            exit;
        }

        $obRARRCarne = new RARRCarne;
        $obRARRCarne->setExercicio ( $_REQUEST['stExercicio'] );
        $boDiff = TRUE;

        $stTipo = 'grupo';
        $arDados = explode( "/", $_REQUEST['inCodGrupo'] );
        $obRARRCarne->setGrupo ( $arDados[0] );

        // 101 ou 102
        $arGruposValidos = explode(',','101,102,121,10120, 10121, 10122, 10123, 10124,10125, 10198, 10199, 10220, 10221, 10222, 10223, 10224, 10225, 10298,10299, 131,13120,13121,13122,13123,13124,13125,13197,131,13198,13199');
        if ( in_array( $arDados[0], $arGruposValidos)) {
            if ( $arDados[0] == '121' )
                $boEspecial = TRUE;

            $boDiversas = FALSE;
        }

        Sessao::write('tipo_emissao', $_REQUEST['emissao_carnes']);

        if ($_REQUEST['emissao_carnes'] == 'local') {
            // listar carnes nao impressoes para o filtro selecionado
            $obRARRCarne->listarEmissaoCarne($rsEmissaoCarne);
            if ( $rsEmissaoCarne->getNumLinhas() <= 0 ) {
                $obErro->setDescricao("Não há parcelas disponiveis para Emissão!");
                SistemaLegado::alertaAviso($pgForm."?stAcao=emitir",urlencode($obErro->getDescricao()),"n_incluir","erro",Sessao::getId(), "../");
                exit;
            }

            $boExec = TRUE;
            $arEmissao = array();
            while ( !$rsEmissaoCarne->eof() ) {
                $arEmissao[$rsEmissaoCarne->getCampo('cod_lancamento')][] = array(
                    "cod_parcela" => $rsEmissaoCarne->getCampo('cod_parcela'),
                    "exercicio"   => $rsEmissaoCarne->getCampo('exercicio'),
                    "numcgm"      => $rsEmissaoCarne->getCampo('numcgm'),
                    "cod_modelo"  => $inCodModelo,
                    "numeracao"   => $rsEmissaoCarne->getCampo('numeracao'),
                    "inscricao"   => $rsEmissaoCarne->getCampo('inscricao')
                );

                $rsEmissaoCarne->proximo();
            }

            /**
            *   grava nome pdf e parametro para salvar em disco
            *   usado tambem no objeto pdf
            */

            $stNomPdf = ini_get("session.save_path")."/"."PdfEmissaoUrbem-".date("dmYHis").".pdf";
            Sessao::write( 'stNomPdf', $stNomPdf );
            Sessao::write( 'stParamPdf', "F" );
            include_once( CAM_GT_ARR_CLASSES."boletos/".$stArquivoModelo );

            $obRModeloCarne = new $stObjModelo( $arEmissao );
            $obRModeloCarne->imprimirCarne();

            include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

            include_once (CAM_GT_ARR_MAPEAMENTO."TARRCarne.class.php");
            $rsEmissaoCarne->setPrimeiroElemento();
            while ( !$rsEmissaoCarne->eof() ) {
                $obTARRCarne = new TARRCarne;
                $obTARRCarne->setDado ("numeracao"    , $rsEmissaoCarne->getCampo('numeracao'));
                $obTARRCarne->setDado ("cod_convenio" , $rsEmissaoCarne->getCampo('cod_convenio'));
                $obTARRCarne->setDado ("cod_parcela"  , $rsEmissaoCarne->getCampo('cod_parcela'));
                $obTARRCarne->setDado ("exercicio"    , $rsEmissaoCarne->getCampo('exercicio'));
                $obTARRCarne->setDado ("impresso"     , TRUE );
                $obErro = $obTARRCarne->alteracao();

                $rsEmissaoCarne->proximo();
            }

            if (!$obErro->ocorreu() ) {
                SistemaLegado::alertaAviso($pgForm."?stAcao=emitir","Carnês emitidos","incluir","aviso", Sessao::getId(), "../");
            } else {
                SistemaLegado::alertaAviso($pgForm."?stAcao=emitir",urlencode($obErro->getDescricao()),"n_incluir","erro",Sessao::getId(), "../");
            }

            if ($boExec) {
                echo "<script type=\"text/javascript\">\r\n";
                echo "    var sAux = window.open('OCImpressaoPDFEmissao.php?".Sessao::getId()."','','width=20,height=10,resizable=1,scrollbars=1,left=100,top=100');\r\n";
                echo "    eval(sAux)\r\n";
                echo "</script>\r\n";
            }
        } else
            if ($_REQUEST['emissao_carnes'] == 'grafica') {

                $stVinculo = $_REQUEST['vinculo'];

                if (!isset($stVinculo)) {
                    $obErro->setDescricao('Escolha o vínculo desejado');
                    SistemaLegado::alertaAviso($pgForm."?stAcao=emitir", urlencode($obErro->getDescricao()), "n_incluir", "erro", Sessao::getId(), "../");
                    exit;
                } else {
                    # Busca os atributos do v
                    if ($stVinculo == 'imobiliario') {

                        $arOrdemLote = $arOrdemImovel = $arOrdemEdificacao = array();

                        if (count($_REQUEST["inCodOrdemLoteSelecionados"]) > 0) {
                            $stCodOrdemLoteSelecionados = implode(",",$_REQUEST["inCodOrdemLoteSelecionados"]);
                            $arOrdemLote = $_REQUEST["inCodOrdemLoteSelecionados"];
                        }

                        if (count($_REQUEST["inCodOrdemImovelSelecionados"]) > 0) {
                            $stCodOrdemImovelSelecionados = implode(",",$_REQUEST["inCodOrdemImovelSelecionados"]);
                            $arOrdemImovel = $_REQUEST["inCodOrdemImovelSelecionados"];
                        }

                        if (count($_REQUEST["inCodOrdemEdificacaoSelecionados"]) > 0) {
                            $stCodOrdemEdificacaoSelecionados = implode(",",$_REQUEST["inCodOrdemEdificacaoSelecionados"]);
                            $arOrdemEdificacao = $_REQUEST["inCodOrdemEdificacaoSelecionados"];
                        }

                        $inTotalAtributo = (  count($arOrdemLote)
                                            + count($arOrdemImovel)
                                            + count($arOrdemEdificacao)
                                           );

                    } elseif ($stVinculo == 'economico') {

                        $arOrdemAtrFato = $arOrdemAtrDireito = $arOrdemAutonomo = $arOrdemElemento = array();

                        if (count($_REQUEST["inAtrFatoSelecionado"]) > 0) {
                            $stAtrFatoSelecionado = implode(",", $_REQUEST["inAtrFatoSelecionado"]);
                            $arOrdemAtrFato = $_REQUEST["inAtrFatoSelecionado"];
                        }

                        if (count($_REQUEST["inAtrDireitoSelecionado"]) > 0) {
                            $stAtrDireitoSelecionado  = implode(",", $_REQUEST["inAtrDireitoSelecionado"]);
                            $arOrdemAtrDireito = $_REQUEST["inAtrDireitoSelecionado"];
                        }

                        if (count($_REQUEST["inAtrAutonomoSelecionado"]) > 0) {
                            $stAtrAutonomoSelecionado = implode(",", $_REQUEST["inAtrAutonomoSelecionado"]);
                            $arOrdemAutonomo = $_REQUEST["inAtrAutonomoSelecionado"];
                        }

                        if (count($_REQUEST["inAtrElementoSelecionado"]) > 0) {
                            $stAtrElementoSelecionado = implode(",", $_REQUEST["inAtrElementoSelecionado"]);
                            $arOrdemElemento = $_REQUEST["inAtrElementoSelecionado"];
                        }

                        $inTotalAtributo = (  count($arOrdemAtrFato)
                                            + count($arOrdemAtrDireito)
                                            + count($arOrdemAutonomo)
                                            + count($arOrdemElemento)
                                           );
                    }

                    if ($inTotalAtributo > 15) {
                        sistemaLegado::exibeAviso( "Número limite de 15 atributos ultrapassado. Selecionados: <strong>".$inTotalAtributo."</strong>", "n_incluir", "erro");
                        exit;
                    }
                }

                require_once CAM_GT_ARR_MAPEAMENTO.'FARRListaEmissaoGrafica.class.php';
                $obListaEmissao = new FARRListaEmissaoGrafica;

                
                if ($_REQUEST["inCodOrdemSelecionados"] == "") {
                        sistemaLegado::exibeAviso( "Uma ordem de Emissão deve ser selecionada!", "n_incluir", "erro");
                        SistemaLegado::LiberaFrames();
                        exit;
                } else {
                    if ($stVinculo == 'imobiliario') {
                        $arCodOrdem = $_REQUEST["inCodOrdemSelecionados"];
                        foreach ( $arCodOrdem as $value) {
                            $arTmp[] = substr($value, 5);
                        }
                        $stOrdemEmissaoFuncao = implode(",",$arTmp);
                    }
                    $stOrdemEmissao = implode(",",$_REQUEST["inCodOrdemSelecionados"]);
                }
                
                $stPadraoCodBarra = $_REQUEST['stPadraoCodBarra'];

                $obListaEmissao->stTipoInscricao = $_REQUEST['inTipoInscricao'];
                $obListaEmissao->inCodGrupo      = $arDados[0];
                $obListaEmissao->inExercicio     = $arDados[1];

                Sessao::write('inCodGrupo'       , $arDados[0] );
                Sessao::write('inExercicio'      , $arDados[1] );
                Sessao::write('stPadraoCodBarra' , $_REQUEST["stPadraoCodBarra"] );
                
                if (!$arDados[0]) {
                    $arCreditoTEMP = explode ( '.', $_REQUEST['inCodCredito'] );
                    Sessao::write('inCodCredito'  , $arCreditoTEMP[0]);
                    Sessao::write('inCodEspecie'  , $arCreditoTEMP[1]);
                    Sessao::write('inCodGenero'   , $arCreditoTEMP[2]);
                    Sessao::write('inCodNatureza' , $arCreditoTEMP[3]);
                    Sessao::write('inExercicio'   , $_REQUEST['stExercicio']);

                    $obListaEmissao->inCodCredito  = $arCredito['cod_credito']  = $arCreditoTEMP[0];
                    $obListaEmissao->inCodEspecie  = $arCredito['cod_especie']  = $arCreditoTEMP[1];
                    $obListaEmissao->inCodGenero   = $arCredito['cod_genero']   = $arCreditoTEMP[2];
                    $obListaEmissao->inCodNatureza = $arCredito['cod_natureza'] = $arCreditoTEMP[3];
                    $obListaEmissao->inExercicio   = $_REQUEST['stExercicio'];
                    $obListaEmissao->inCodGrupo    = 0;
                }

                $obListaEmissao->stOrdemEmissao = $stOrdemEmissao;

                $rsInscricoes = new RecordSet;

                if ($stVinculo == 'imobiliario') {
                    $stTipoEmissao = "II";
                    $obListaEmissao->inCodIIInicial       = $_REQUEST['inCodImovelInicial'];
                    $obListaEmissao->inCodIIFinal         = $_REQUEST['inCodImovelFinal'];
                    $obListaEmissao->stLocalizacaoInicial = $_REQUEST['inCodInicioLocalizacao'];
                    $obListaEmissao->stLocalizacaoFinal   = $_REQUEST['inCodTerminoLocalizacao'];
                    $obListaEmissao->inCodEnderecoInicial = $_REQUEST['inCodEnderecoInicial'];
                    $obListaEmissao->inCodEnderecoFinal   = $_REQUEST['inCodEnderecoFinal'];
                    $obListaEmissao->stOrdemLote          = $stCodOrdemLoteSelecionados;
                    $obListaEmissao->stOrdemImovel        = $stCodOrdemImovelSelecionados;
                    $obListaEmissao->stOrdemEdificacao    = $stCodOrdemEdificacaoSelecionados;

                    Sessao::write('stEmissaoDesonerados',   $_REQUEST['emissao_carnes_desonerados']);
                    
                    if ($_REQUEST['emissao_carnes_desonerados'] == 'nao') {
                        $obListaEmissao->listaImoveis($rsInscricoes);
                    } elseif ($_REQUEST['emissao_carnes_desonerados'] == 'sim') {
                        $obListaEmissao->listaImoveisDesonerados($rsInscricoes);
                    }

                } elseif ($stVinculo == 'economico') {
                    $stTipoEmissao = "IE";
                    $obListaEmissao->inCodIEInicial    = $_REQUEST['inNumInscricaoEconomicaInicial'];
                    $obListaEmissao->inCodIEFinal      = $_REQUEST['inNumInscricaoEconomicaFinal'];
                    $obListaEmissao->stOrdemAtrFato    = $stAtrFatoSelecionado;
                    $obListaEmissao->stOrdemAtrDireito = $stAtrDireitoSelecionado;
                    $obListaEmissao->stOrdemAutonomo   = $stAtrAutonomoSelecionado;
                    $obListaEmissao->stOrdemElemento   = $stAtrElementoSelecionado;

                    $obListaEmissao->listaEmpresas($rsInscricoes);
                }

                $obListaEmissao->stTipoEmissao = $stTipoEmissao;

                if ($rsInscricoes->getNumLinhas() < 1) {
                    $obErro->setDescricao("Nenhum registro para emissão encontrado!");
                    SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_erro","erro");
                    SistemaLegado::LiberaFrames();
                    exit;
                }

                $arTMP = $rsInscricoes->getElementos();

                Sessao::write('stVinculo', $stVinculo);
                Sessao::write('listados', 0 );
                Sessao::write('inscricoes_lista', $arTMP );
                Sessao::write('total_listar', count($arTMP) );
                
                Sessao::write("TipoEmissao"       , $stTipoEmissao );
                Sessao::write("OrdemEmissao"      , $stOrdemEmissao );
                Sessao::write("OrdemEmissaoFuncao", $stOrdemEmissaoFuncao );

                Sessao::write("OrdemLote"       , $stCodOrdemLoteSelecionados );
                Sessao::write("OrdemImovel"     , $stCodOrdemImovelSelecionados );
                Sessao::write("OrdemEdificacao" , $stCodOrdemEdificacaoSelecionados );

                Sessao::write('OrdemAtrFato'    , $stAtrFatoSelecionado);
                Sessao::write('OrdemAtrDireito' , $stAtrDireitoSelecionado);
                Sessao::write('OrdemAutonomo'   , $stAtrAutonomoSelecionado);
                Sessao::write('OrdemElemento'   , $stAtrElementoSelecionado);

                Sessao::write("ArOrdemLote"       , $arOrdemLote );
                Sessao::write("ArOrdemImovel"     , $arOrdemImovel );
                Sessao::write("ArOrdemEdificacao" , $arOrdemEdificacao );

                Sessao::write("ArOrdemAtrFato"    , $arOrdemAtrFato);
                Sessao::write("ArOrdemAtrDireito" , $arOrdemAtrDireito);
                Sessao::write("ArOrdemAutonomo"   , $arOrdemAutonomo);
                Sessao::write("ArOrdemElemento"   , $arOrdemElemento);

                $nome_arquivo = "carnes_grafica_".date("Y-m-d").'_'.date("h-i-s");
                $nome_arquivo = "/tmp/".$nome_arquivo.".txt";
                Sessao::write("NomeArquivoGrafica", $nome_arquivo);

                $pgListaSituacao = "LSEmitirCarneGraficaSituacao.php?stAcao=emitirGrafica";

                SistemaLegado::alertaAviso($pgListaSituacao, "Impressão Gráfica", "incluir", "aviso", Sessao::getId(), "../");

            } //fim do if pra qual tipo de impressao (local ou grafica)
    break;

    case "emitirGrafica":
        $stVinculo = Sessao::read('stVinculo');

        $rsEmissaoCarne = new RecordSet();

        require_once CAM_GT_ARR_MAPEAMENTO.'FARRListaEmissaoGrafica.class.php';

        $obListaEmissao = new FARRListaEmissaoGrafica;
        $obListaEmissao->inCodGrupo  = Sessao::read('inCodGrupo');
        $obListaEmissao->inExercicio = Sessao::read('inExercicio');

        if (!$obListaEmissao->inCodGrupo) {
            $obListaEmissao->inCodCredito  = Sessao::read('inCodCredito');
            $obListaEmissao->inCodEspecie  = Sessao::read('inCodEspecie');
            $obListaEmissao->inCodGenero   = Sessao::read('inCodGenero');
            $obListaEmissao->inCodNatureza = Sessao::read('inCodNatureza');
            $obListaEmissao->inCodGrupo    = 0;
        }

        $obListaEmissao->stTipoEmissao        = Sessao::read("TipoEmissao");
        $obListaEmissao->stOrdemEmissao       = Sessao::read("OrdemEmissao");
        $obListaEmissao->stOrdemEmissaoFuncao = Sessao::read("OrdemEmissaoFuncao");

        if ($stVinculo == 'imobiliario') {
            $obListaEmissao->stOrdemLote       = Sessao::read("OrdemLote");
            $obListaEmissao->stOrdemImovel     = Sessao::read("OrdemImovel");
            $obListaEmissao->stOrdemEdificacao = Sessao::read("OrdemEdificacao");

            $arOrdemLote       = Sessao::read("ArOrdemLote");
            $arOrdemImovel     = Sessao::read("ArOrdemImovel");
            $arOrdemEdificacao = Sessao::read("ArOrdemEdificacao");

        } elseif ($stVinculo == 'economico') {
            $obListaEmissao->stOrdemAtrFato    = Sessao::read('OrdemAtrFato');
            $obListaEmissao->stOrdemAtrDireito = Sessao::read('OrdemAtrDireito');
            $obListaEmissao->stOrdemAutonomo   = Sessao::read('OrdemAutonomo');
            $obListaEmissao->stOrdemElemento   = Sessao::read('OrdemElemento');

            $arOrdemAtrFato    = Sessao::read("ArOrdemAtrFato");
            $arOrdemAtrDireito = Sessao::read("ArOrdemAtrDireito");
            $arOrdemAutonomo   = Sessao::read("ArOrdemAutonomo");
            $arOrdemElemento   = Sessao::read("ArOrdemElemento");
        }
        
        $stInscricoes = "";
        $arListaCalc = Sessao::read('inscricoes_lista');

        for ($inTMP=0; $inTMP<100; $inTMP++) {
            if (Sessao::read('listados') >= Sessao::read('total_listar')) {
                break;
            }

            $stInscricoes .= $arListaCalc[Sessao::read('listados')]["inscricao"];
            if (($inTMP+1 < 100) && (Sessao::read('listados') + 1 < Sessao::read('total_listar')))
                $stInscricoes .= ", ";

            Sessao::write('listados', Sessao::read('listados') + 1);
        }

        if ($obListaEmissao->stTipoEmissao == "II") {
            $obListaEmissao->inCodIIInicial = $stInscricoes;
        } else {
            $obListaEmissao->inCodIEInicial = $stInscricoes;
        }

        $obListaEmissao->stPadraoCodBarra = Sessao::read('stPadraoCodBarra');

        $stPadraoCodBarra = Sessao::read('stPadraoCodBarra');

        $stEmissaoDesonerados = Sessao::read('stEmissaoDesonerados');

        if ($stEmissaoDesonerados == 'sim') {
            $obErro = $obListaEmissao->executaFuncaoDesonerados($rsEmissaoCarne, null);
        } else {
            $obErro = $obListaEmissao->executaFuncao($rsEmissaoCarne, null);
        }

        # Lista os creditos utilizados na emissao
        $arEmissao = array();

        if ($stVinculo == 'imobiliario') {

            include_once CAM_GT_CIM_NEGOCIO."RCIMLoteUrbano.class.php";
            include_once CAM_GT_CIM_NEGOCIO."RCIMImovel.class.php";
            include_once CAM_GT_CIM_NEGOCIO."RCIMEdificacao.class.php";

            $obRCIMLote       = new RCIMLoteUrbano;
            $obRCIMImovel     = new RCIMImovel($obRCIMLote);
            $obRCIMEdificacao = new RCIMEdificacao;
        } elseif ($stVinculo == 'economico') {

            include_once CAM_GT_CEM_NEGOCIO.'RCEMEmpresaDeFato.class.php';
            include_once CAM_GT_CEM_NEGOCIO.'RCEMEmpresaDeDireito.class.php';
            include_once CAM_GT_CEM_NEGOCIO.'RCEMAutonomo.class.php';
            include_once CAM_GT_CEM_NEGOCIO.'RCEMElemento.class.php';

            $obRCEMEmpresaDeFato    = new RCEMEmpresaDeFato;
            $obRCEMEmpresaDeDireito = new RCEMEmpresaDeDireito;
            $obRCEMAutonomo         = new RCEMAutonomo;
            $obRCEMElemento         = new RCEMElemento(new RCEMAtividade);
        }

        include_once CAM_GT_ARR_MAPEAMENTO."TARRLancamento.class.php";
        include_once CAM_GT_ARR_MAPEAMENTO."TARRParcela.class.php";
        include_once CLA_MASCARA_CNPJ;

        $obTARRLancamento = new TARRLancamento;
        $obTARRParcela    = new TARRParcela;
        $obMascaraCNPJ    = new MascaraCNPJ;

        $contArEmissao = $contInscricao = 0;

        $rsEmissaoCarne->setPrimeiroElemento();
        
        while (!$rsEmissaoCarne->eof()) {
            $inInscricaoAtual         = $rsEmissaoCarne->getCampo('inscricao');
            $inCodLancamentoAtual     = $rsEmissaoCarne->getCampo('cod_lancamento');
            $inExercicioAtual         = $rsEmissaoCarne->getCampo('exercicio');
            $inCodLoteAtual           = $rsEmissaoCarne->getCampo('cod_lote');
            $inCodConstrucaoAtual     = $rsEmissaoCarne->getCampo('cod_construcao');
            $inCodTipoConstrucaoAtual = $rsEmissaoCarne->getCampo('cod_tipo_construcao');

            $inInscricaoEconomica     = $rsEmissaoCarne->getCampo('inscricao_economica');

            $vencimento_US   = null;
            $valor_documento = null;

            $contInscricao++;

            # Atributos
            $contagemAtributos = 1;

            if ($stVinculo == 'imobiliario') {
                if ($inCodLoteAtual) {
                    $obRCIMLote->obRCadastroDinamico->obRModulo->setCodModulo( 12 );
                    $obRCIMLote->obRCadastroDinamico->setCodCadastro (2);
                    $contAtributo = 0;

                    while ($contAtributo < count($arOrdemLote) ) {
                        $arChaveAtributoLote = array(
                            "cod_lote" => $inCodLoteAtual,
                            "cod_atributo" => $arOrdemLote[$contAtributo]
                        );

                        $obRCIMLote->obRCadastroDinamico->setChavePersistenteValores($arChaveAtributoLote);
                        $obRCIMLote->obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributos,"","",$boTransacao );
                        
                        if ( $rsAtributos->getCampo('nom_tipo') == 'Lista' ) {
                            $arValores = explode ('[][][]',$rsAtributos->getCampo('valor_padrao_desc'));
                            $arValorPadrao = explode (',', $rsAtributos->getCampo('valor_padrao'));
                            $indice = ($rsAtributos->getCampo('valor'));
                            $contTMP = 0;
                            while ( $contTMP < count($arValorPadrao) ) {
                                if ($arValorPadrao[$contTMP] == $indice) {
                                    break;
                                }

                                $contTMP++;
                            }

                            $valorDoAtributo = $arValores[$contTMP];
                        } else {
                            $valorDoAtributo = $rsAtributos->getCampo('valor');
                        }

                        switch ( $rsAtributos->getCampo('cod_atributo') ) {
                            //Quadra
                            case 5:
                                $rsEmissaoCarne->setCampo ('atributo_1', $valorDoAtributo );
                                break;
                            //Lote
                            case 7:
                                $rsEmissaoCarne->setCampo ('atributo_2', $valorDoAtributo );
                                break;
                            default:
                                if ($contagemAtributos < 5) {
                                    $contagemAtributos = 5;
                                }
                                $rsEmissaoCarne->setCampo ('atributo_'.$contagemAtributos, $valorDoAtributo );
                                break;
                        }
                        
                        $contagemAtributos++;
                        $contAtributo++;
                    }
                }

                if ($inCodLoteAtual) {
                    $obRCIMImovel->obRCadastroDinamico->obRModulo->setCodModulo( 12 );
                    $obRCIMImovel->obRCadastroDinamico->setCodCadastro ( 4 );
                    $contAtributo = 0;
                    while ( $contAtributo < count ( $arOrdemImovel ) ) {
                        $arChaveAtributoLote = array(
                            "cod_lote" => $inCodLoteAtual,
                            "inscricao_municipal" => $inInscricaoAtual,
                            "cod_atributo" => $arOrdemImovel[$contAtributo]
                        );

                        $obRCIMImovel->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoLote );
                        $obRCIMImovel->obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributos,"","",$boTransacao );

                        #$arValores = $arValorPadrao = $valorDoAtributo = null;
                        if ( $rsAtributos->getCampo('nom_tipo') == 'Lista' ) {
                            $arValores = explode ('[][][]',$rsAtributos->getCampo('valor_padrao_desc'));
                            $arValorPadrao = explode (',', $rsAtributos->getCampo('valor_padrao'));
                            $indice = ($rsAtributos->getCampo('valor'));
                            $contTMP = 0;
                            while ( $contTMP < count($arValorPadrao) ) {
                                if ($arValorPadrao[$contTMP] == $indice) {
                                    break;
                                }

                                $contTMP++;
                            }

                            $valorDoAtributo = $arValores[$contTMP];
                        } else {
                            $valorDoAtributo = $rsAtributos->getCampo('valor');
                        }

                        switch ( $rsAtributos->getCampo('cod_atributo') ) {
                            //Zona
                            case 106:
                                $rsEmissaoCarne->setCampo ('atributo_3', $valorDoAtributo );
                                break;
                            //Uso do solo
                            case 8:
                                $rsEmissaoCarne->setCampo ('atributo_4', $valorDoAtributo );
                                break;
                            default:
                                if ($contagemAtributos < 5) {
                                    $contagemAtributos = 5;
                                }
                                $rsEmissaoCarne->setCampo ('atributo_'.$contagemAtributos, $valorDoAtributo );
                                break;
                        }
                        
                        $contagemAtributos++;
                        $contAtributo++;
                    }
                }

                #INICIO DOS ATRIBUTOS DINAMICOS DE EDIFICACAO
                if ($inCodConstrucaoAtual && $inCodTipoConstrucaoAtual) {
                    $obRCIMEdificacao->obRCadastroDinamico->obRModulo->setCodModulo( 12 );
                    $obRCIMEdificacao->obRCadastroDinamico->setCodCadastro ( 5 );
                    $contAtributo = 0;
                    while ( $contAtributo < count ( $arOrdemEdificacao ) ) {
                        $arChaveAtributoLote = array (
                            "cod_lote" => $inCodLoteAtual,
                            "cod_construcao" => $inCodConstrucaoAtual,
                            "cod_tipo" => $inCodTipoConstrucaoAtual,
                            "cod_atributo" => $arOrdemEdificacao[$contAtributo]
                        );

                        $obRCIMEdificacao->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoLote );
                        $obRCIMEdificacao->obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributos,"","",$boTransacao );

                        if ( $rsAtributos->getCampo('nom_tipo') == 'Lista' ) {
                            $arValores = explode ('[][][]', $rsAtributos->getCampo('valor_padrao_desc') );
                            $arValorPadrao = explode (',', $rsAtributos->getCampo('valor_padrao'));
                            $indice = ($rsAtributos->getCampo('valor'));
                            $contTMP = 0;
                            while ( $contTMP < count($arValorPadrao) ) {
                                if ($arValorPadrao[$contTMP] == $indice) {
                                    break;
                                }

                                $contTMP++;
                            }

                            $valorDoAtributo = $arValores[$contTMP];
                        } else {
                            $valorDoAtributo = $rsAtributos->getCampo('valor');
                        }

                        if ($contagemAtributos < 5) {
                            $contagemAtributos = 5;
                        }

                        $rsEmissaoCarne->setCampo ('atributo_'.$contagemAtributos, $valorDoAtributo );

                        $contagemAtributos++;
                        $contAtributo++;
                    }
                }

            } elseif ($stVinculo == 'economico') {

                # ATRIBUTOS EMPRESA DE FATO
                if (!empty($inInscricaoEconomica)) {
                    $obRCEMEmpresaDeFato->obRCadastroDinamico->obRModulo->setCodModulo(14);
                    $obRCEMEmpresaDeFato->obRCadastroDinamico->setCodCadastro(1);
                    $contAtributo = 0;

                    while ($contAtributo < count($arOrdemAtrFato)-1) {
                        $arChaveAtributoFato = array(
                            'cod_atributo'        => $arOrdemAtrFato[$contAtributo],
                            'inscricao_economica' => $inInscricaoEconomica
                        );

                        $obRCEMEmpresaDeFato->obRCadastroDinamico->setChavePersistenteValores($arChaveAtributoFato);
                        $obRCEMEmpresaDeFato->obRCadastroDinamico->recuperaAtributosSelecionadosValores($rsAtributosFato);

                        if ($rsAtributosFato->getCampo('nom_tipo') == 'Lista') {
                            $arValores     = explode ('[][][]',$rsAtributosFato->getCampo('valor_padrao_desc'));
                            $arValorPadrao = explode (',', $rsAtributosFato->getCampo('valor_padrao'));
                            $indice  = ($rsAtributosFato->getCampo('valor'));
                            $contTMP = 0;
                            while ($contTMP < count($arValorPadrao)) {
                                if ($arValorPadrao[$contTMP] == $indice) {
                                    break;
                                }

                                $contTMP++;
                            }

                            $valorDoAtributo = $arValores[$contTMP];
                        } else {
                            $valorDoAtributo = $rsAtributosFato->getCampo('valor');
                        }

                        $rsEmissaoCarne->setCampo('atributo_'.$contagemAtributos, $valorDoAtributo);
                        $contagemAtributos++;
                        $contAtributo++;
                    }
                }

                # ATRIBUTOS EMPRESA DE DIREITO
                if (!empty($inInscricaoEconomica)) {
                    $obRCEMEmpresaDeDireito->obRCadastroDinamico->obRModulo->setCodModulo(14);
                    $obRCEMEmpresaDeDireito->obRCadastroDinamico->setCodCadastro(2);
                    $contAtributo = 0;

                    while ($contAtributo < count($arOrdemAtrDireito)-1) {
                        $arChaveAtributoDireito = array(
                            'cod_atributo'        => $arOrdemAtrDireito[$contAtributo],
                            'inscricao_economica' => $inInscricaoEconomica
                        );

                        $obRCEMEmpresaDeDireito->obRCadastroDinamico->setChavePersistenteValores($arChaveAtributoDireito);
                        $obRCEMEmpresaDeFato->obRCadastroDinamico->recuperaAtributosSelecionadosValores($rsAtributosDireito);

                        if ($rsAtributosDireito->getCampo('nom_tipo') == 'Lista') {
                            $arValores     = explode ('[][][]', $rsAtributosDireito->getCampo('valor_padrao_desc') );
                            $arValorPadrao = explode (',', $rsAtributosDireito->getCampo('valor_padrao'));
                            $indice        = ($rsAtributosDireito->getCampo('valor'));
                            $contTMP = 0;
                            while ($contTMP < count($arValorPadrao)) {
                                if ($arValorPadrao[$contTMP] == $indice) {
                                    break;
                                }

                                $contTMP++;
                            }

                            $valorDoAtributo = $arValores[$contTMP];
                        } else {
                            $valorDoAtributo = $rsAtributosDireito->getCampo('valor');
                        }

                        $rsEmissaoCarne->setCampo ('atributo_'.$contagemAtributos, $valorDoAtributo );

                        $contagemAtributos++;
                        $contAtributo++;
                    }
                }

                # ATRIBUTOS DE AUTONOMO
                if (!empty($inInscricaoEconomica)) {
                    $obRCEMAutonomo->obRCadastroDinamico->obRModulo->setCodModulo(14);
                    $obRCEMAutonomo->obRCadastroDinamico->setCodCadastro(3);
                    $contAtributo = 0;

                    while ($contAtributo < count($arOrdemAutonomo)-1) {
                        $arChaveAtributoAutonomo = array(
                            'cod_atributo'        => $arOrdemAutonomo[$contAtributo],
                            'inscricao_economica' => $inInscricaoEconomica
                        );

                        $obRCEMAutonomo->obRCadastroDinamico->setChavePersistenteValores($arChaveAtributoAutonomo);
                        $obRCEMAutonomo->obRCadastroDinamico->recuperaAtributosSelecionadosValores($rsAtributosAutonomo);

                        if ($rsAtributosAutonomo->getCampo('nom_tipo') == 'Lista') {
                            $arValores     = explode ('[][][]', $rsAtributosAutonomo->getCampo('valor_padrao_desc') );
                            $arValorPadrao = explode (',', $rsAtributosAutonomo->getCampo('valor_padrao'));
                            $indice        = ($rsAtributosAutonomo->getCampo('valor'));
                            $contTMP = 0;
                            while ($contTMP < count($arValorPadrao)) {
                                if ($arValorPadrao[$contTMP] == $indice) {
                                    break;
                                }

                                $contTMP++;
                            }

                            $valorDoAtributo = $arValores[$contTMP];
                        } else {
                            $valorDoAtributo = $rsAtributosAutonomo->getCampo('valor');
                        }

                        $rsEmissaoCarne->setCampo ('atributo_'.$contagemAtributos, $valorDoAtributo );

                        $contagemAtributos++;
                        $contAtributo++;
                    }
                }

                # ATRIBUTOS DE ELEMENTO
                if (!empty($inInscricaoEconomica)) {
                    $obRCEMElemento->obRCadastroDinamico->obRModulo->setCodModulo(14);
                    $obRCEMElemento->obRCadastroDinamico->setCodCadastro(5);
                    $contAtributo = 0;

                    while ($contAtributo < count($arOrdemElemento)-1) {
                        $arChaveAtributoElemento = array(
                            'cod_atributo'        => $arOrdemElemento[$contAtributo],
                            'inscricao_economica' => $inInscricaoEconomica
                        );

                        $obRCEMElemento->obRCadastroDinamico->setChavePersistenteValores($arChaveAtributoElemento);
                        $obRCEMElemento->obRCadastroDinamico->recuperaAtributosSelecionadosValores($rsAtributosElemento);

                        if ($rsAtributosElemento->getCampo('nom_tipo') == 'Lista') {
                            $arValores     = explode ('[][][]', $rsAtributosElemento->getCampo('valor_padrao_desc') );
                            $arValorPadrao = explode (',', $rsAtributosElemento->getCampo('valor_padrao'));
                            $indice        = ($rsAtributosElemento->getCampo('valor'));
                            $contTMP = 0;
                            while ($contTMP < count($arValorPadrao)) {
                                if ($arValorPadrao[$contTMP] == $indice) {
                                    break;
                                }

                                $contTMP++;
                            }

                            $valorDoAtributo = $arValores[$contTMP];
                        } else {
                            $valorDoAtributo = $rsAtributosElemento->getCampo('valor');
                        }

                        $rsEmissaoCarne->setCampo ('atributo_'.$contagemAtributos, $valorDoAtributo);

                        $contagemAtributos++;
                        $contAtributo++;
                    }
                }
            }

            $arValores = $arValorPadrao =  NULL;
            $valorDoAtributo = $indice = $contagemAtributos = $contAtributo = NULL;

            $arChaveAtributoLote = NULL;
            $arChaveAtributoFato = $arChaveAtributoDireito = $arChaveAtributoAutonomo = $arChaveAtributoElemento = NULL;

            $rsAtributos = $rsAtributosElemento = $rsAtributosAutonomo = $rsAtributosFato = $rsAtributosDireito = NULL;

            $contArEmissao++;
            $rsEmissaoCarne->proximo();
        }

        # Dados da Prefeitura
        include_once CAM_GT_ARR_MAPEAMENTO."TARRCarne.class.php";
        $obTARRCarne = new TARRCarne;
        $stFiltro = " WHERE conf.exercicio = '".($inExercicioAtual -1)."' \n";
        $obTARRCarne->buscaCabecalhoCarneGrafica ( $rsCabecalho, $stFiltro );
        if (Sessao::read('inCodGrupo')) {
            $stFiltroComp  = " WHERE credito_grupo.cod_grupo     = '".Sessao::read('inCodGrupo')."'";
            $stFiltroComp .= "   AND credito_grupo.ano_exercicio = '".Sessao::read('inExercicio')."'";
        } else {
            $stFiltroComp  = " WHERE credito.cod_credito  = ".Sessao::read('inCodCredito');
            $stFiltroComp .= "   AND credito.cod_especie  = ".Sessao::read('inCodEspecie');
            $stFiltroComp .= "   AND credito.cod_genero   = ".Sessao::read('inCodGenero');
            $stFiltroComp .= "   AND credito.cod_natureza = ".Sessao::read('inCodNatureza');
        }

        $obTARRCarne->retornaDadosCompensacao($rsTMP, $stFiltroComp);
        $arCabecalho = array();
        $arCabecalho = $rsCabecalho->arElementos;
        $arCabecalho[0]['cod_convenio']    = $rsTMP->getCampo("cod_convenio");
        if ($stEmissaoDesonerados == 'nao') {
            $arCabecalho[0]['local_pagamento'] = $rsTMP->getCampo("local_pagamento");

        } elseif ($stEmissaoDesonerados == 'sim') {
            include_once( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php" );
            $obTAdministracaoConfiguracao = new TAdministracaoConfiguracao;
            $obTAdministracaoConfiguracao->setDado('parametro' , 'fundamentacao_legal' );
            $obTAdministracaoConfiguracao->setDado( 'exercicio ',"'".Sessao::read('inExercicio')."'" );
            $obErro = $obTAdministracaoConfiguracao->recuperaPorChave( $rsCodigoNorma );
            
            include_once CAM_GA_NORMAS_MAPEAMENTO.'TNorma.class.php';
            $obTNorma = new TNorma;
            $obTNorma->setDado('cod_norma',$rsCodigoNorma->getCampo('valor'));
            $obTNorma->recuperaPorChave($rsNorma);
 
            if ( !$rsNorma->eof() ) {
                $stNorma = $rsNorma->getCampo( "num_norma" )."/".$rsNorma->getCampo( "exercicio" );
                $rsNorma->proximo();
            }

            $arCabecalho[0]['local_pagamento'] = '[ Isento conforme Lei Municipal '.$stNorma.' ]' ;
        }

        $arCabecalho[0]['especie_doc']     = $rsTMP->getCampo("especie_doc");
        $arCabecalho[0]['aceite']          = $rsTMP->getCampo("aceite");
        $arCabecalho[0]['especie']         = $rsTMP->getCampo("especie");
        $arCabecalho[0]['quantidade']      = $rsTMP->getCampo("quantidade");
        $arCabecalho[0]['carteira']        = $rsTMP->getCampo("carteira");
        $arCabecalho[0]['agencia']         = $rsTMP->getCampo("agencia");
        $arCabecalho[0]['codigo_cedente']  = $rsTMP->getCampo("codigo_cedente");
        $obMascaraCNPJ->mascaraDado($arCabecalho[0]['cnpj']);

        $rsCabecalho = new RecordSet;
        $rsCabecalho->preenche($arCabecalho);

        include_once CAM_GT_ARR_MAPEAMENTO."FARRMontaCarneGrafica.class.php";
        $obMontaCarneGrafica = new FARRMontaCarneGrafica;
        $obMontaCarneGrafica->stTipoEmissao = $obListaEmissao->stTipoEmissao;

        $rsRetorno = $obMontaCarneGrafica->montaLinhaCarneGrafica($stPadraoCodBarra, $rsCabecalho, $rsEmissaoCarne);

        if ($rsRetorno->getNumLinhas() < 1) {
            $obErro->setDescricao ('Nenhum registro foi encontrado!');
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_erro","erro");
            exit;
        } else {
            $arX = $rsRetorno->arElementos;
        }

        $nome_arquivo = Sessao::read("NomeArquivoGrafica");
        $arquivo = fopen($nome_arquivo, "a");

        if (Sessao::read('listados') > 100) {
            $contLinhas = 7;
        } else {
            $contLinhas = 0;
        }

        # Escreve no arquivo
        while ($contLinhas < count($arX)) {
            fwrite( $arquivo, $arX[$contLinhas]."\n" );   
            $contLinhas++;
        }

        fclose ($arquivo);

        $pgListaSituacao = "LSEmitirCarneGraficaSituacao.php?stAcao=emitirGrafica";
        SistemaLegado::alertaAviso($pgListaSituacao, "Impressão Gráfica", "incluir", "aviso", Sessao::getId(), "../");

    break;
}

?>