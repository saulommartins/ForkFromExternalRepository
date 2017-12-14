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

    * Classe de Regra de Negócio para Baixa de Pagamento Manual
    * Data de Criação   : 05/12/2005

    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Marcelo B. Paulino

    * @package URBEM
    * @subpackage Regra

    * $Id: RARRPagamento.class.php 66042 2016-07-12 17:32:39Z evandro $

   * Casos de uso: uc-05.03.10
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRPagamento.class.php"           );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRPagamentoLote.class.php"       );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRLote.class.php"                );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRLoteInconsistencia.class.php"  );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRLoteArquivo.class.php"  );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRCarneDevolucao.class.php"      );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRPagamentoCalculo.class.php"    );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRPagamentoAcrescimo.class.php"  );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRPagamentoDiferenca.class.php"  );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRPagamentoLoteManual.class.php" );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRProcessoPagamento.class.php"   );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMTransferenciaEfetivacao.class.php"   );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaParcela.class.php" );
include_once ( CAM_GA_PROT_NEGOCIO."RProcesso.class.php"                 );
include_once ( CAM_GT_ARR_NEGOCIO."RARRCarne.class.php"                  );
include_once ( CAM_GT_ARR_NEGOCIO."RARRLancamento.class.php"             );
include_once ( CAM_GT_ARR_NEGOCIO."RARRTipoPagamento.class.php"          );
include_once ( CAM_GT_ARR_NEGOCIO."RARRConfiguracao.class.php"           );
include_once ( CAM_GT_MON_NEGOCIO."RMONBanco.class.php"                  );
include_once ( CAM_GT_MON_NEGOCIO."RMONAgencia.class.php"                );

/**
    * Classe de Regra de Negócio para Baixa de Pagamento Manual
    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Marcelo B. Paulino
*/

class RARRPagamento
{
/**
    * @var Object
    * @access Private
*/
var $obTransacao;
/**
    * @access Private
    * @var Object
*/
var $obTARRPagamento;
/**
    * @access Private
    * @var Object
*/
var $obTARRPagamentoLote;
/**
    * @access Private
    * @var Object
*/
var $obTARRLote;
/**
    * @access Private
    * @var Object
*/
var $obTARRLoteInconsistencia;
/**
    * @access Private
    * @var Object
*/
var $obTARRCarneDevolucao;
/**
    * @access Private
    * @var Object
*/
var $obTARRPagamentoCalculo;
/**
    * @access Private
    * @var Object
*/
var $obTARRPagamentoAcrescimo;
/**
    * @access Private
    * @var Object
*/
var $obTARRPagamentoDiferenca;
/**
    * @access Private
    * @var Object
*/
var $obTARRProcessoPagamento;
/**
    * @access Private
    * @var Object
*/
var $obRARRCarne;
/**
    * @access Private
    * @var Object
*/
var $obRARRTipoPagamento;
/**
    * @access Private
    * @var Object
*/
var $obRARRConfiguracao;
/**
    * @access Private
    * @var Object
*/
var $obRMONBanco;
/**
    * @access Private
    * @var Object
*/
var $obRMONAgencia;
/**
    * @access Private
    * @var Object
*/
var $obRProcesso;
/**
    * @access Private
    * @var Date
*/
var $dtDataPagamento;
/**
    * @access Private
    * @var Date
*/
var $dtDataBaixa;
/**
    * @access Private
    * @var Numeric
*/
var $nuValorPagamento;
/**
    * @access Private
    * @var Numeric
*/
var $nuValorPagoConsolidacao;
/**
    * @access Private
    * @var String
*/
var $stObservacao;
/**
    * @access Private
    * @var File
*/
var $flArquivo;
/**
    * @access Private
    * @var Integer
*/
var $inCodLote;
/**
    * @access Private
    * @var Integer
*/
var $inCodLoteFinal;
/**
    * @access Private
    * @var Integer
*/
var $inOcorrenciaPagamento;
/**
    * @access Private
    * @var Date
*/
var $dtDataLote;
/**
    * @access Private
    * @var Date
*/
var $dtDataLoteFinal;
/**
    * @access Private
    * @var String
*/
var $stExercicio;
var $stMd5Sum;
var $stHeader;
var $stFooter;
var $stNomArquivo;
/**
    * @access Private
    * @var Array
*/
var $arITBI;

// SETTERS
/**
    * @access Public
    * @param Date $valor
*/
function setDataPagamento($valor) { $this->dtDataPagamento   = $valor; }
/**
    * @access Public
    * @param Date $valor
*/
function setDataBaixa($valor) { $this->dtDataBaixa      = $valor; }
/**
    * @access Public
    * @param Numeric $valor
*/
function setValorPagamento($valor) { $this->nuValorPagamento  = $valor; }
/**
    * @access Public
    * @param Numeric $valor
*/
function setValorPagoConsolidacao($valor) { $this->nuValorPagoConsolidacao  = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setObservacao($valor) { $this->stObservacao      = $valor; }
/**
    * @access Public
    * @param File $valor
*/
function setArquivo($valor) { $this->flArquivo         = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodLote($valor) { $this->inCodLote         = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodLoteFinal($valor) { $this->inCodLoteFinal      = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setOcorrenciaPagamento($valor) { $this->inOcorrenciaPagamento = $valor; }
/**
    * @access Public
    * @param Date $valor
*/
function setDataLote($valor) { $this->dtDataLote         = $valor; }
/**
    * @access Public
    * @param Date $valor
*/
function setDataLoteFinal($valor) { $this->dtDataLoteFinal         = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setExercicio($valor) { $this->stExercicio       = $valor; }

// GETTERES
/**
    * @access Public
    * @param Date $valor
*/
function getDataPagamento() { return $this->dtDataPagamento;  }
/**
    * @access Public
    * @param Date $valor
*/
function getDataBaixa() { return $this->dtDataBaixa;  }
/**
    * @access Public
    * @param Numeric $valor
*/
function getValorPagamento() { return $this->nuValorPagamento; }
/**
    * @access Public
    * @param Numeric $valor
*/
function getValorPagoConsolidacao() { return $this->nuValorPagoConsolidacao; }
/**
    * @access Public
    * @param Numeric $valor
*/
function getObservacao() { return $this->stObservacao; }
/**
    * @access Public
    * @param File $valor
*/
function getArquivo() { return $this->flArquivo; }
/**
    * @access Public
    * @param Integer $valor
*/
function getCodLote() { return $this->inCodLote; }
/**
    * @access Public
    * @param Integer $valor
*/
function getCodLoteFinal() { return $this->inCodLoteFinal; }
/**
    * @access Public
    * @param Integer $valor
*/
function getOcorrenciaPagamento() { return $this->inOcorrenciaPagamento; }
/**
    * @access Public
    * @param Date $valor
*/
function getDataLote() { return $this->dtDataLote; }
/**
    * @access Public
    * @param Date $valor
*/
function getDataLoteFinal() { return $this->dtDataLoteFinal; }
/**
    * @access Public
    * @param String $valor
*/
function getExercicio() { return $this->stExercicio; }

/**
     * Método construtor
     * @access Private
*/
function RARRPagamento()
{
    $this->obTARRPagamento          = new TARRPagamento;
    $this->obTARRPagamentoLote      = new TARRPagamentoLote;
    $this->obTARRLote               = new TARRLote;
    $this->obTARRLoteInconsistencia = new TARRLoteInconsistencia;
    $this->obTARRLoteArquivo        = new TARRLoteArquivo;
    $this->obTARRCarneDevolucao     = new TARRCarneDevolucao;
    $this->obTARRPagamentoCalculo   = new TARRPagamentoCalculo;
    $this->obTARRPagamentoAcrescimo = new TARRPagamentoAcrescimo;
    $this->obTARRPagamentoDiferenca = new TARRPagamentoDiferenca;
    $this->obTARRPagamentoLoteManual= new TARRPagamentoLoteManual;
    $this->obTARRProcessoPagamento  = new TARRProcessoPagamento;
    $this->obRARRCarne              = new RARRCarne;
    $this->obRARRTipoPagamento      = new RARRTipoPagamento;
    $this->obTransacao              = new Transacao;
    $this->obRProcesso              = new RProcesso;
    $this->obRMONBanco              = new RMONBanco;
    $this->obRMONAgencia            = new RMONAgencia;
    $this->obRARRCarne->obRARRParcela = new RARRParcela( new RARRLancamento( new RARRCalculo) );
}

/*
funcao que recebe um array com a data como parametro de entrada e retorna a data ajustada caso caia num final de semana
*/
function verifica_dia_util($arData)
{
    $dataOrdenacao = $arData;
    $inDiaSemana = date("w", mktime(0, 0, 0, sprintf("%02d", $dataOrdenacao[1]), sprintf("%02d", $dataOrdenacao[0]), sprintf("%04d", $dataOrdenacao[2])));
    if ($inDiaSemana == 0 || $inDiaSemana == 6) { //o dia caiu num domingo ou sabado
        $arDiaMes = array(31,28,31,30,31,30,31,31,30,31,30,31);

        if ( $inDiaSemana == 0 )
            $inDiasMais = 1;
        else
            $inDiasMais = 2;

        for ($inY=0; $inY < $inDiasMais; $inY++) {
            if ($dataOrdenacao[0]+1 > $arDiaMes[$dataOrdenacao[1]-1]) {
                $dataOrdenacao[0] = 1;
                if ($dataOrdenacao[1] <  12)
                    $dataOrdenacao[1]++;
                else {
                    $dataOrdenacao[1] = 1;
                    $dataOrdenacao[2]++;
                }
            } else {
                $dataOrdenacao[0]++;
            }
            if ($dataOrdenacao[0] < 10) {
                $dataOrdenacao[0] = '0'.$dataOrdenacao[0];
            }
        }
    }

    return $dataOrdenacao;
}

/**
    * Efetua baixa manual de Pagamento
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function efetuarPagamentoManual($boTransacao = "", $boPagamentoAutomatico = FALSE , $boFechaBaixaManual = FALSE, &$nuTotal, $boConsolidacao = FALSE)
{
    $boFlagTransacao = false;

    include_once ( CAM_GT_MON_NEGOCIO."RMONCreditoAcrescimo.class.php" );

    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

    $nuTotal = 0;

    if ( Sessao::read( "consultadivida" ) || ( $this->obRARRCarne->obRMONConvenio->getCodigoConvenio() == -1 ) ) { //divida ativa
        $this->obRARRConfiguracao = new RARRConfiguracao;
        $this->obRARRConfiguracao->setAnoExercicio( Sessao::getExercicio() );
        $this->obRARRConfiguracao->consultarParamentro( $vlMinimoLancamentoAutomatico, $boTransacao, "minimo_lancamento_automatico" );
        $this->obRARRConfiguracao->consultarParamentro( $vlTipoAvaliacao, $boTransacao, "tipo_avaliacao" );
        $this->obRARRConfiguracao->consultarParamentro( $vlValorMaximo, $boTransacao, "valor_maximo" );
        $this->obRARRConfiguracao->consultarParamentro( $vlBaixaManual, $boTransacao, "baixa_manual" );

        $nuValorLancamentoAutomatico = str_replace( ".", "" , $vlMinimoLancamentoAutomatico);
        $nuValorLancamentoAutomatico = str_replace( ",", ".", $nuValorLancamentoAutomatico );
        $this->obRARRConfiguracao->setMinimoLancamentoAutomatico( $nuValorLancamentoAutomatico );

        //Verifica se a PARCELA relativa ao NUMERO DE CARNE informado JA FOI PAGA
        $obErro = $this->obRARRCarne->verificaPagamento( $rsVerificaPagamento, $boTransacao );
        $this->inOcorrenciaPagamento = 0;
        if ( $rsVerificaPagamento->getNumLinhas() < 0 OR $boPagamentoAutomatico == TRUE ) {
            if ( $rsVerificaPagamento->getCampo('ocorrencia_pagamento') > 0 ) {
                if ( $rsVerificaPagamento->getCampo('pagamento') == 'f' ) {
                    $obErro = $this->salvarInconsistencia( $boTransacao );

                    return $obErro;
                }

                $this->inOcorrenciaPagamento = $rsVerificaPagamento->getCampo('ocorrencia_pagamento') + 1;
                $this->obRARRTipoPagamento->setCodigoTipo(5);
            } else {
                $this->inOcorrenciaPagamento = 1;
            }
        }

        $arDataBase = explode( "/" , $this->getDataPagamento() );
        $dtDataBase = $arDataBase[2]."-".$arDataBase[1]."-".$arDataBase[0];
        $obErro = $this->obRARRCarne->listarDetalheCreditosBaixaDivida( $rsConsultaCredito, $boTransacao, $dtDataBase );

        include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaParcela.class.php" );
        $obTDATDividaParcela = new TDATDividaParcela;
        $stFiltro = " WHERE num_parcelamento = ".$rsConsultaCredito->getCampo( "num_parcelamento" )." AND num_parcela = ".$rsConsultaCredito->getCampo( "num_parcela" );
        $obTDATDividaParcela->recuperaTodos( $rsListaValorParcela, $stFiltro, "", $boTransacao );

        include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaParcelaCalculo.class.php" );
        $obTDATDividaParcelaCalculo = new TDATDividaParcelaCalculo;
        $stFiltro = " WHERE num_parcelamento = ".$rsConsultaCredito->getCampo( "num_parcelamento" )." AND num_parcela = ".$rsConsultaCredito->getCampo( "num_parcela" );
        $obTDATDividaParcelaCalculo->recuperaTodos( $rsListaCalculos, $stFiltro, "", $boTransacao );

        //verifica se o pagamento esta sendo feito depois do VENCIMENTO
        $boParcelaVencida = FALSE;
        $arDataPagamentoComp  = explode( "/" , $this->getDataPagamento() );
        $arDataVencimentoComp = explode( "-" , $rsConsultaCredito->getCampo('vencimento') );

        //verificacao de sabado
        $inTempVal = $arDataVencimentoComp[0];
        $arDataVencimentoComp[0] = $arDataVencimentoComp[2];
        $arDataVencimentoComp[2] = $inTempVal;

        $arDataVencimentoComp = $this->verifica_dia_util( $arDataVencimentoComp );
        if ($arDataPagamentoComp[2].$arDataPagamentoComp[1].$arDataPagamentoComp[0] > $arDataVencimentoComp[2].$arDataVencimentoComp[1].$arDataVencimentoComp[0]) {
            $boParcelaVencida = TRUE;
        }

        $nuTotalJuroMulta = 0.00;
        while ( !$rsConsultaCredito->eof() ) {
            $nuTotalJuroMulta += $rsConsultaCredito->getCampo('juros') + $rsConsultaCredito->getCampo('multa') + $rsConsultaCredito->getCampo('correcao');
            $rsConsultaCredito->proximo();
        }

        $rsConsultaCredito->setPrimeiroElemento();

        if ($boParcelaVencida == TRUE) {            
            $nuValorTotalParcela = $nuTotalJuroMulta + $rsConsultaCredito->getCampo('vlr_parcela');
        } else {
            $nuValorTotalParcela = $rsConsultaCredito->getCampo('vlr_parcela');
        }

        if ( $this->obRARRTipoPagamento->getPagamento() == "t" ) {
            // Verifica se a diferença do valor da parcela com o valor pago esta entre o limite
            // de valor minimo/maximo definido na configuracao                                    
            $nuDifPagamento = $nuValorTotalParcela - $this->getValorPagamento();            
            if (!empty($vlValorMaximo) ) {
                $vlValorMaximo = SistemaLegado::formataValorDecimal($vlValorMaximo, false);                
                $nuDifPagamentoMais = $nuDifPagamento * -1;
                if ( ($nuDifPagamentoMais > 0) && ($nuDifPagamentoMais > $vlValorMaximo) ) {
                    if ($boPagamentoAutomatico == TRUE) {
                        $obErro = $this->salvarInconsistencia( $boTransacao );
                        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTARRPagamento );
                        return $obErro;
                    }                
                }
            }

            if ($vlTipoAvaliacao == 'percentual') {                
                $nuValorDifMaximo = number_format( (($nuValorTotalParcela*$vlValorMaximo)/100) ,2);
            } else {
                $nuValorDifMaximo = $vlValorMaximo;
            }

            //SE PAGOU MENOS
            $boExecutaLancamento = FALSE;
            $boPagamentoMenor    = FALSE;                        

            if ( $this->getValorPagamento() < $nuValorTotalParcela ) {
                SistemaLegado::formataValorDecimal($nuValorDifMaximo, false);                
                if ($boPagamentoAutomatico) {
                    $this->obRARRTipoPagamento->setCodigoTipo(6);                    
                    if( $this->obRARRConfiguracao->getMinimoLancamentoAutomatico() ){
                        $boExecutaLancamento = TRUE;
                        //Validando valores para não serem inseridos a mais                        
                        $nuValorValidacaoAcrescimos = 0.00;                        
                        while ( !$rsConsultaCredito->eof() ) {                            
                            $nuValorValidacaoAcrescimos = $nuValorValidacaoAcrescimos - ($nuDifPagamento-$rsConsultaCredito->getCampo('multa'));                            
                            if ( $nuValorValidacaoAcrescimos <= 0 ){
                                $rsConsultaCredito->setCampo('multa_completo','');
                            }
                            $rsConsultaCredito->proximo();                            
                        }
                        $rsConsultaCredito->setPrimeiroElemento();
                    }                
                } else {
                    if ($vlBaixaManual == 'bloqueia') {
                        $obErro->setDescricao("O valor pago está abaixo da diferença de pagamento ( $nuDifPagamento ).");
                        return $obErro;
                    }
                }

                if ( $this->getValorPagamento() < $rsConsultaCredito->getCampo("vlr_parcela") ) {
                    $boPagamentoMenor = TRUE;
                }
            //SE PAGOU MAIS
            } else 
                if ( $this->getValorPagamento() > $nuValorTotalParcela ) {
                    $nuDifPagamento = $nuDifPagamento * -1;                    
                    if ($boPagamentoAutomatico == TRUE) {
                        $this->obRARRTipoPagamento->setCodigoTipo(7);
                    } else {
                        //se for pagamento manual..
                        if ($vlBaixaManual == 'bloqueia') {
                            $obErro->setDescricao("O valor pago está acima da diferença de pagamento ( $nuDifPagamento ).");
                        }
                    }

                    if ($boParcelaVencida) {
                        $boPagamentoAcrescimo = TRUE;
                    } else {
                        $boPagamentoDiferenca = TRUE;
                    }
                }else
                    if ( (double) $this->getValorPagamento() > (double) $rsConsultaCredito->getCampo("vlr_parcela") ) {
                        $boPagamentoAcrescimo = TRUE;
                    }
        }

        $this->obRARRCarne->obRARRParcela->roRARRLancamento->setCodLancamento( $rsConsultaCredito->getCampo('cod_lancamento') );

        //insere Pagamento
        $this->obTARRPagamento->setDado( "numeracao"            , $this->obRARRCarne->getNumeracao()          );
        $this->obTARRPagamento->setDado( "ocorrencia_pagamento" , $this->inOcorrenciaPagamento                );
        $this->obTARRPagamento->setDado( "cod_convenio"         , $this->obRARRCarne->obRMONConvenio->getCodigoConvenio() );
        $this->obTARRPagamento->setDado( "data_pagamento"       , $this->getDataPagamento()                   );
        $this->obTARRPagamento->setDado( "valor"                , $this->getValorPagamento()                  );
        $this->obTARRPagamento->setDado( "inconsistente"        , FALSE                                       );
        $this->obTARRPagamento->setDado( "observacao"           , $this->getObservacao()                      );
        $this->obTARRPagamento->setDado( "cod_tipo"             , $this->obRARRTipoPagamento->getCodigoTipo() );
        $this->obTARRPagamento->setDado( "numcgm"               , Sessao::read( "numCgm" )                    );
        $obErro = $this->obTARRPagamento->inclusao( $boTransacao );        
        if ( $obErro->ocorreu() )
            return $obErro;

        include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaParcela.class.php" );

        $obTDATDividaParcela = new TDATDividaParcela;
        $obTDATDividaParcela->setDado( "num_parcelamento", $rsConsultaCredito->getCampo("num_parcelamento") );
        $obTDATDividaParcela->setDado( "num_parcela", $rsConsultaCredito->getCampo("num_parcela") );
        $this->obRARRTipoPagamento->listarTipoPagamento( $rsListaTipo, $boTransacao );
        if ( $this->obRARRTipoPagamento->getPagamento() == 't' ){
            $obTDATDividaParcela->setDado( "paga", true );        
        }else{
            $obTDATDividaParcela->setDado( "cancelada", true );
        }
        
        $obErro = $obTDATDividaParcela->alteracao( $boTransacao );
        if ( $obErro->ocorreu() )
            return $obErro;

        //insere informaçoes do lote de pagamento manual
        if ($boPagamentoAutomatico == FALSE) {
            $this->obTARRPagamentoLoteManual->setDado( "numeracao"            , $this->obRARRCarne->getNumeracao()          );
            $this->obTARRPagamentoLoteManual->setDado( "ocorrencia_pagamento" , $this->inOcorrenciaPagamento                );
            $this->obTARRPagamentoLoteManual->setDado( "cod_convenio"         , $this->obRARRCarne->obRMONConvenio->getCodigoConvenio() );
            $this->obTARRPagamentoLoteManual->setDado( "cod_agencia"          , $this->obRMONAgencia->getCodAgencia() );
            $this->obTARRPagamentoLoteManual->setDado( "cod_banco"            , $this->obRMONBanco->getCodBanco() );
            $obErro = $this->obTARRPagamentoLoteManual->inclusao( $boTransacao );
            if ( $obErro->ocorreu() )
                return $obErro;
        }

        //insere o processo se esse for setado
        if ( $this->obRProcesso->getCodigoProcesso() && $boPagamentoAutomatico == FALSE ) {
            $this->obTARRProcessoPagamento->setDado( "numeracao"            , $this->obRARRCarne->getNumeracao()          );
            $this->obTARRProcessoPagamento->setDado( "ocorrencia_pagamento" , $this->inOcorrenciaPagamento                );
            $this->obTARRProcessoPagamento->setDado( "cod_convenio"         , $this->obRARRCarne->obRMONConvenio->getCodigoConvenio() );
            $this->obTARRProcessoPagamento->setDado( "cod_processo"         , $this->obRProcesso->getCodigoProcesso()     );
            $this->obTARRProcessoPagamento->setDado( "ano_exercicio"        , $this->obRProcesso->getExercicio()          );
            $obErro = $this->obTARRProcessoPagamento->inclusao( $boTransacao );
            if ( $obErro->ocorreu() )
                return $obErro;
        }

        //se o metodo estiver sendo usado pela baixa automatica,
        //insere as informações do lote
        if ($boPagamentoAutomatico == TRUE) {
            $obErro = $this->salvarPagamentoLote( $boTransacao );
            if ( $obErro->ocorreu() )
                return $obErro;
        }

        //insere pagamento_calculo
        $flSomaValores = 0;
        $inQtdCalculos = 0;

        $flSomaCreditos = 0;
        while ( !$rsListaCalculos->Eof() ) {
            $flSomaCreditos += $rsListaCalculos->getCampo( "vl_credito" );
            $rsListaCalculos->proximo();
        }

        $nuValorPagamentoCalculo = round( $rsListaValorParcela->getCampo("vlr_parcela") / $rsListaCalculos->getNumLinhas(), 8 );        
        $flValorReducao = $rsListaValorParcela->getCampo("vlr_parcela") - ( $nuValorPagamentoCalculo * $rsListaCalculos->getNumLinhas() );

        $rsListaCalculos->setPrimeiroElemento();
        while ( !$rsListaCalculos->Eof() ) {
            $flValorPorCredito = $rsListaCalculos->getCampo( "vl_credito" );
            if ( $flValorPorCredito > 0.00 ){
                $flValorAcrescimoPercent = ($flValorPorCredito * 100) / $flSomaCreditos;
            }else{
                $flValorAcrescimoPercent = 0.00;
            }

            if ( $rsListaCalculos->getNumLinhas() < $rsListaCalculos->getCorrente()+1 ) {
                $flValorFinal = $rsListaValorParcela->getCampo("vlr_parcela") - $flSomaValores;
            }else{
                //se pagou menos deve usar o valor que foi pago e não o que deveria ser pago
                if ( $this->getValorPagamento() < $nuValorTotalParcela ) {                    
                    $flValorFinal = round( ($this->getValorPagamento() * $flValorAcrescimoPercent) / 100, 8 );
                }else{
                    $flValorFinal = round( ($rsListaValorParcela->getCampo("vlr_parcela") * $flValorAcrescimoPercent) / 100, 8 );                
                }
            }            
            
            $flSomaValores += $flValorFinal;
            $inQtdCalculos++;            
            $flValorFinal = round($flValorFinal,2);

            $this->obTARRPagamentoCalculo->setDado( "numeracao"            , $this->obRARRCarne->getNumeracao()     );
            $this->obTARRPagamentoCalculo->setDado( "cod_calculo"          , $rsListaCalculos->getCampo('cod_calculo') );
            $this->obTARRPagamentoCalculo->setDado( "ocorrencia_pagamento" , $this->inOcorrenciaPagamento           );
            $this->obTARRPagamentoCalculo->setDado( "cod_convenio"         , $this->obRARRCarne->obRMONConvenio->getCodigoConvenio() );
            $this->obTARRPagamentoCalculo->setDado( "valor"                , $flValorFinal );
            $obErro = $this->obTARRPagamentoCalculo->inclusao( $boTransacao );            
 
            if ( $obErro->ocorreu() )
                return $obErro;

            $nuTotal += $this->obTARRPagamentoCalculo->getDado("valor");

            $inX = 0;

            while ( !$rsConsultaCredito->Eof() ) {
                $stJuros = $rsConsultaCredito->getCampo("juros_completo");
                $arJuros = explode( ";", $stJuros );                
                $inZ = 1;
                if ($arJuros[0] > 0.00) {
                    while ( $inZ < count( $arJuros ) ) { //loop para mais de um juros
                        $flValorJuros = $arJuros[$inZ];
                       
                        $nuValorPagamentoJuros = round( ($flValorJuros * $flValorAcrescimoPercent) / 100, 8 );
                        
                        //validacao para diferenca juros
                        $nuDiferencaJuros = $nuTotal + $nuValorPagamentoJuros;                                                                        
                        $nuDiferencaJuros = $this->getValorPagamento() - $nuDiferencaJuros;
                        $nuDiferencaJuros = round($nuDiferencaJuros,8);
                        //caso o valor for negativo retirar essa diferença                        
                        if ($nuDiferencaJuros < 0.00) {
                            $nuDiferencaJuros = ($nuDiferencaJuros*-1);
                            $nuValorPagamentoJuros = $nuValorPagamentoJuros - $nuDiferencaJuros;
                        }
                        
                        $nuValorPagamentoJuros = substr($nuValorPagamentoJuros,0,strpos($nuValorPagamentoJuros,'.')+3 );                        

                        $this->obTARRPagamentoAcrescimo->setDado( "numeracao"            , $this->obRARRCarne->getNumeracao()     );
                        $this->obTARRPagamentoAcrescimo->setDado( "ocorrencia_pagamento" , $this->inOcorrenciaPagamento           );
                        $this->obTARRPagamentoAcrescimo->setDado( "cod_calculo"          , $rsListaCalculos->getCampo('cod_calculo') );
                        $this->obTARRPagamentoAcrescimo->setDado( "cod_convenio"         , '-1' );
                        $this->obTARRPagamentoAcrescimo->setDado( "cod_tipo"             , $arJuros[$inZ+2] );
                        $this->obTARRPagamentoAcrescimo->setDado( "cod_acrescimo"        , $arJuros[$inZ+1] );
                        $this->obTARRPagamentoAcrescimo->setDado( "valor"                , $nuValorPagamentoJuros                   );
                        $obErro = $this->obTARRPagamentoAcrescimo->inclusao( $boTransacao );
                        if ( $obErro->ocorreu() )
                            return $obErro;
                        
                        $nuTotal += $this->obTARRPagamentoAcrescimo->getDado("valor");

                        $inZ += 3;
                    }
                }

                $stMulta = $rsConsultaCredito->getCampo("multa_completo");                
                $arMulta = explode( ";", $stMulta );
                $inZ = 1;
                if ($arMulta[0] > 0.00) {
                    while ( $inZ < count( $arMulta ) ) { //loop para mais de uma multa
                        $flValorMulta = $arMulta[$inZ];
                        $arValPagamentoMulta = explode( ".", (($flValorMulta * $flValorAcrescimoPercent) / 100));
                        (float) $nuValorPagamentoMulta = (string) $arValPagamentoMulta[0].".".substr((string) $arValPagamentoMulta[1],0,2);

                        //validacao para diferenca juros
                        $nuDiferencaMulta = $nuTotal + $nuValorPagamentoMulta;                                                                        
                        $nuDiferencaMulta = $this->getValorPagamento() - $nuDiferencaMulta;
                        $nuDiferencaMulta = round($nuDiferencaMulta,8);
                        //caso o valor for negativo retirar essa diferença                        
                        if ($nuDiferencaMulta < 0.00) {
                            $nuDiferencaMulta = ($nuDiferencaMulta*-1);
                            $nuValorPagamentoMulta = $nuValorPagamentoMulta - $nuDiferencaMulta;
                        }

                        $this->obTARRPagamentoAcrescimo->setDado( "numeracao"            , $this->obRARRCarne->getNumeracao()     );
                        $this->obTARRPagamentoAcrescimo->setDado( "ocorrencia_pagamento" , $this->inOcorrenciaPagamento           );
                        $this->obTARRPagamentoAcrescimo->setDado( "cod_calculo"          , $rsListaCalculos->getCampo('cod_calculo') );
                        $this->obTARRPagamentoAcrescimo->setDado( "cod_convenio"         , '-1' );
                        $this->obTARRPagamentoAcrescimo->setDado( "cod_tipo"             , $arMulta[$inZ+2] );
                        $this->obTARRPagamentoAcrescimo->setDado( "cod_acrescimo"        , $arMulta[$inZ+1] );
                        $this->obTARRPagamentoAcrescimo->setDado( "valor"                , $nuValorPagamentoMulta );
                        $obErro = $this->obTARRPagamentoAcrescimo->inclusao( $boTransacao );                        
                        if ( $obErro->ocorreu() )
                            return $obErro;

                        $nuTotal += $this->obTARRPagamentoAcrescimo->getDado("valor");

                        $inZ += 3;
                    }
                }

                $stCorrecao = $rsConsultaCredito->getCampo("correcao_completo");
                $arCorrecao = explode( ";", $stCorrecao );
                $inZ = 1;

                if ($arCorrecao[0] > 0.00) {
                    while ( $inZ < count( $arCorrecao ) ) { //loop para mais de uma correcao
                        $flValorCorrecao = $arCorrecao[$inZ];
                        $nuValorPagamentoCorrecao = round( ($flValorCorrecao * $flValorAcrescimoPercent) / 100, 2 );                        

                        $this->obTARRPagamentoAcrescimo->setDado( "numeracao"            , $this->obRARRCarne->getNumeracao()     );
                        $this->obTARRPagamentoAcrescimo->setDado( "ocorrencia_pagamento" , $this->inOcorrenciaPagamento           );
                        $this->obTARRPagamentoAcrescimo->setDado( "cod_calculo"          , $rsListaCalculos->getCampo('cod_calculo') );
                        $this->obTARRPagamentoAcrescimo->setDado( "cod_convenio"         , '-1' );
                        $this->obTARRPagamentoAcrescimo->setDado( "cod_tipo"             , $arCorrecao[$inZ+2] );
                        $this->obTARRPagamentoAcrescimo->setDado( "cod_acrescimo"        , $arCorrecao[$inZ+1] );
                        $this->obTARRPagamentoAcrescimo->setDado( "valor"                , $nuValorPagamentoCorrecao                   );
                        $obErro = $this->obTARRPagamentoAcrescimo->inclusao( $boTransacao );
                        if ( $obErro->ocorreu() )
                            return $obErro;

                        
                        $nuTotal += $this->obTARRPagamentoAcrescimo->getDado("valor");

                        $inZ += 3;
                    }
                }

                $rsConsultaCredito->proximo();
                $inX++;
            } //while ( !$rsConsultaCredito->Eof() ) {

            $rsConsultaCredito->setPrimeiroElemento();
            $rsListaCalculos->proximo();
        }
        
        if ( $this->getValorPagamento() > $nuTotal ) {
            $rsListaCalculos->setPrimeiroElemento();

            $flDiferenca = round( $this->getValorPagamento() - $nuTotal, 2 );
            $flDiferencaProp = round( $flDiferenca / $rsListaCalculos->getNumLinhas(), 2 );
            $flDiferencaResto = $flDiferenca - ($flDiferencaProp * $rsListaCalculos->getNumLinhas());
            while ( !$rsListaCalculos->Eof() ) {
                $this->obTARRPagamentoDiferenca->setDado( "numeracao"            , $this->obRARRCarne->getNumeracao() );
                $this->obTARRPagamentoDiferenca->setDado( "ocorrencia_pagamento" , $this->inOcorrenciaPagamento       );
                $this->obTARRPagamentoDiferenca->setDado( "cod_convenio"         , '-1' );
                $this->obTARRPagamentoDiferenca->setDado( "cod_calculo"          , $rsListaCalculos->getCampo('cod_calculo') );
                $this->obTARRPagamentoDiferenca->setDado( "valor"                , $flDiferencaProp+$flDiferencaResto );
                $obErro = $this->obTARRPagamentoDiferenca->inclusao( $boTransacao );
                $nuTotal += $this->obTARRPagamentoDiferenca->getDado("valor");
                $flDiferencaResto = 0;
                $rsListaCalculos->proximo();
            }
        //Validando ROUND automatico de acordo com o formato da tabela, o banco faz e insere de centavos a mais
        }elseif( $this->getValorPagamento() < $nuTotal ){
            $obTARRPagamentoCalculoRound = new TARRPagamentoCalculo();
            $obTARRPagamentoCalculoRound->setDado( "numeracao"            , $this->obRARRCarne->getNumeracao()     );
            $obTARRPagamentoCalculoRound->setDado( "cod_convenio"         , $this->obRARRCarne->obRMONConvenio->getCodigoConvenio() );
            $obErro = $obTARRPagamentoCalculoRound->recuperaPorChave($rsPagamentoCalculoDiferencaRound, $boTransacao );
            if( !$obErro->ocorreu() ){
                foreach ($rsPagamentoCalculoDiferencaRound->getElementos() as $value) {
                    
                    $nuValorTotalInseridoBanco += $value['valor'];
                    
                    if ( $nuValorTotalInseridoBanco > $this->getValorPagamento() ) {
                        
                        $inDiferencaRound = $nuValorTotalInseridoBanco - $this->getValorPagamento();
                        $flValorAtualizado = $value['valor'] - $inDiferencaRound;
                        
                        $obTARRPagamentoCalculoRound->setDado( "numeracao"            , $value['numeracao']     );
                        $obTARRPagamentoCalculoRound->setDado( "cod_calculo"          , $value['cod_calculo'] );
                        $obTARRPagamentoCalculoRound->setDado( "ocorrencia_pagamento" , $value['ocorrencia_pagamento'] );
                        $obTARRPagamentoCalculoRound->setDado( "cod_convenio"         , $value['cod_convenio'] );
                        $obTARRPagamentoCalculoRound->setDado( "valor"                , $flValorAtualizado );
                        $obErro = $obTARRPagamentoCalculoRound->alteracao( $boTransacao );
                        
                        if ( $obErro->ocorreu() )
                            return $obErro;
                    }
                }                
            }
        }//FIM ELSE

        $rsListaCalculos->setPrimeiroElemento();

        if ($boPagamentoAutomatico == FALSE) {
            // se for fechar a baixa manual
            if ($boFechaBaixaManual) {
                $this->efetuarFechamentoManual( $boTransacao );
            }

            $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTARRPagamento );
        }
    //FIM DO REGISTRO DA DIVIDA
    } else {
    if ( !$obErro->ocorreu() ) {
        $this->obRARRConfiguracao = new RARRConfiguracao;
        $this->obRARRConfiguracao->setAnoExercicio( Sessao::getExercicio() );
        $this->obRARRConfiguracao->consultarParamentro( $vlMinimoLancamentoAutomatico, $boTransacao, "minimo_lancamento_automatico" );
        $this->obRARRConfiguracao->consultarParamentro( $vlTipoAvaliacao, $boTransacao, "tipo_avaliacao" );
        $this->obRARRConfiguracao->consultarParamentro( $vlValorMaximo, $boTransacao, "valor_maximo" );
        $this->obRARRConfiguracao->consultarParamentro( $vlBaixaManual, $boTransacao, "baixa_manual" );

        $nuValorLancamentoAutomatico = str_replace( ".", "" , $vlMinimoLancamentoAutomatico);
        $nuValorLancamentoAutomatico = str_replace( ",", ".", $nuValorLancamentoAutomatico );
        $this->obRARRConfiguracao->setMinimoLancamentoAutomatico( $nuValorLancamentoAutomatico );

        //Verifica se a PARCELA relativa ao NUMERO DE CARNE informado JA FOI PAGA
        $obErro = $this->obRARRCarne->verificaPagamento( $rsVerificaPagamento, $boTransacao );
        $this->inOcorrenciaPagamento = 0;

        if ( !$obErro->ocorreu() ) {

            /*
                - Verifica se o pagamento é de uma parcela de ISSQN com valor zerado
                - Se for aplica funcao para buscar valor original da parcela, juro e multa
                - Senao aplica regra de negocio normal para calculo de acrescimo/desconto
            */
            $this->obRARRCarne->verificaCarneEconomico( $rsVerificaCarne, $boTransacao );
            $boCalculoEconomico = FALSE;
            if ( !$rsVerificaCarne->eof() ) {
                $boCalculoEconomico = TRUE;
            }

            //Se nao foi paga, efetua a rotina de baixa de pagamento
            //Se JA FOI PAGA, nao efetua a baixa e insere as informaçoes de devolução na tabela (CARNE_DEVOLUCAO)
            if ( $rsVerificaPagamento->getNumLinhas() < 0 OR $boPagamentoAutomatico == TRUE ) {
                if ( $rsVerificaPagamento->getCampo('ocorrencia_pagamento') > 0 ) {
                    if ( $rsVerificaPagamento->getCampo('pagamento') == 'f' ) {
                        $obErro = $this->salvarInconsistencia( $boTransacao );

                        return $obErro;
                    }

                    $this->inOcorrenciaPagamento = $rsVerificaPagamento->getCampo('ocorrencia_pagamento') + 1;
                    $this->obRARRTipoPagamento->setCodigoTipo(5);
                } else {
                    $this->inOcorrenciaPagamento = 1;
                }

                $obErro = $this->obRARRCarne->consultarLancamento( $rsLancamento , $boTransacao );
                $this->obRARRCarne->obRARRParcela->roRARRLancamento->setCodLancamento( $rsLancamento->getCampo('cod_lancamento') );

                $arDataBase = explode( "/" , $this->getDataPagamento() );
                $dtDataBase = $arDataBase[2]."-".$arDataBase[1]."-".$arDataBase[0];
                $obErro = $this->obRARRCarne->listarDetalheCreditosBaixa( $rsConsultaCredito, $boTransacao, $dtDataBase );

                //verifica se o pagamento esta sendo feito depois do VENCIMENTO
                $boParcelaVencida = FALSE;
                $arDataPagamentoComp  = explode( "/" , $this->getDataPagamento() );
                $arDataVencimentoComp = explode( "-" , $rsLancamento->getCampo('vencimento') );

                //verificacao de sabado
                $inTempVal = $arDataVencimentoComp[0];
                $arDataVencimentoComp[0] = $arDataVencimentoComp[2];
                $arDataVencimentoComp[2] = $inTempVal;

                $arDataVencimentoComp = $this->verifica_dia_util( $arDataVencimentoComp );
                if ($arDataPagamentoComp[2].$arDataPagamentoComp[1].$arDataPagamentoComp[0] > $arDataVencimentoComp[2].$arDataVencimentoComp[1].$arDataVencimentoComp[0]) {
                    $boParcelaVencida = TRUE;
                }

                //faz somatorio de juro e multa se a parcela estiver vencida
                if ( !$obErro->ocorreu() ) {

                    $nuTotalJuroMulta  = 0;
                    $nuTotalCalculo    = 0;
                    $arDescontoCalculo = array();

                    while ( !$rsConsultaCredito->eof() ) {                        
                        $nuTotalJuroMulta += $rsConsultaCredito->getCampo('valor_credito_juros') + $rsConsultaCredito->getCampo('valor_credito_multa') + $rsConsultaCredito->getCampo('valor_credito_correcao');
                        $nuTotalCalculo   += $rsConsultaCredito->getCampo('valor');
                        if ( $rsConsultaCredito->getCampo('descontop') > 0 ) {
                            $arDescontoCalculo[$rsConsultaCredito->getCampo('cod_calculo')] = $rsConsultaCredito->getCampo('descontop');
                        }
                        $rsConsultaCredito->proximo();
                    }

                    $rsConsultaCredito->setPrimeiroElemento();
                    if ($boParcelaVencida == TRUE) {                        
                        $nuValorTotalParcela = round( ($nuTotalJuroMulta + $rsLancamento->getCampo('valor_parcela')),8);                        
                    } else {
                        $nuValorTotalParcela = $rsLancamento->getCampo("valor_parcela");
                    }
                    $inGrupoCreditoOriginal = $rsConsultaCredito->getCampo('cod_grupo');
                }

                if ($boConsolidacao == TRUE) {
                    if ( $nuValorTotalParcela <= $this->getValorPagoConsolidacao() ) {
                        $this->setValorPagamento( $nuValorTotalParcela );
                    } else {
                        $this->setValorPagamento( $this->getValorPagoConsolidacao() );
                    }
                }

                if ( $rsLancamento->getCampo("total_parcelas") > 0 && $rsLancamento->getCampo("nr_parcela") > 0 ) {
                    $nuTotalCalculo = $rsLancamento->getCampo("valor_parcela");
                }

                if ( !$obErro->ocorreu() AND $boCalculoEconomico == FALSE OR ( $boCalculoEconomico == TRUE AND $rsLancamento->getCampo("valor_parcela") > 0 ) ) {

                    if ( $this->obRARRTipoPagamento->getPagamento() == "t" ) {

                        // Verifica se a diferença do valor da parcela com o valor pago esta entre o limite
                        // de valor minimo/maximo definido na configuracao
                        $nuDifPagamento = $nuValorTotalParcela - $this->getValorPagamento();

                        if ($vlTipoAvaliacao == 'percentual') {                            
                            $vlValorMaximo = str_replace('.','',$vlValorMaximo);
                            $vlValorMaximo = number_format((double)$vlValorMaximo,2,'.','');                            
                            $nuValorDifMaximo = ( $nuValorTotalParcela * $vlValorMaximo) / 100;
                        } else {//absoluto
                            $nuValorDifMaximo = $vlValorMaximo;
                        }

                        //se pagou menos
                        $boExecutaLancamento = FALSE;
                        $boPagamentoMenor    = FALSE;
                        //SE pagou o valor MENOR da parcela com os acrescimos para esta parcela
                        if ( $this->getValorPagamento() < $nuValorTotalParcela ) {
                            $nuValorDifMaximo = SistemaLegado::formataValorDecimal($nuValorDifMaximo, false);
                            if ($nuDifPagamento < $nuValorDifMaximo) {                                
                                if ($boPagamentoAutomatico) {
                                    $this->obRARRTipoPagamento->setCodigoTipo(6);
                                    if( $this->obRARRConfiguracao->getMinimoLancamentoAutomatico() ){
                                        $boExecutaLancamento = TRUE;
                                    }
                                } else {
                                    if ($vlBaixaManual == 'bloqueia') {
                                        $obErro->setDescricao("O valor pago está abaixo da diferença de pagamento ( $nuDifPagamento ).");
                                    }
                                }
                            }
                            //Se pagou menos do valor da parcela sem acrescimos
                            if ( $this->getValorPagamento() < $rsLancamento->getCampo("valor_parcela") ) {
                                $boPagamentoMenor = TRUE;
                            }                            
                            
                        } elseif ( $this->getValorPagamento() > $nuValorTotalParcela ) {//SE pagou o valor MAIOR da parcela com os acrescimos para esta parcela
                            
                            $nuDifPagamento = $nuDifPagamento * -1;
                            if ($nuDifPagamento > $nuValorDifMaximo) {
                                if ($boPagamentoAutomatico == TRUE) {
                                    $this->obRARRTipoPagamento->setCodigoTipo(7);
                                } else {
                                    //se for pagamento manual..
                                    if ($vlBaixaManual == 'bloqueia') {
                                        $obErro->setDescricao("O valor pago está acima da diferença de pagamento ( $nuDifPagamento ).");
                                    }
                                }
                            }

                            if ($boParcelaVencida) {
                                $boPagamentoAcrescimo = TRUE;
                            } else {
                                $boPagamentoDiferenca = TRUE;
                            }
                        } elseif ( $this->getValorPagamento() < $rsLancamento->getCampo("valor_parcela") ) {
                        } elseif ( $this->getValorPagamento() > $rsLancamento->getCampo("valor_parcela") ) {
                            $boPagamentoAcrescimo = TRUE;
                        }
                    }
                }

                //se for parcela unica e ja estiver vencida, insere como inconsistencia

                $inParcela = $rsLancamento->getCampo('nr_parcela');
                if ($boParcelaVencida == TRUE AND $inParcela == 0 AND $boPagamentoAutomatico == TRUE) {
                    $obErro = $this->salvarInconsistencia( $boTransacao );

                    return $obErro;

                } else {
                    //Recupera informacoes do lancamento com juros,multas e descontos
                    if ( !$obErro->ocorreu() ) {
                        $this->obRARRCarne->obRARRParcela->roRARRLancamento->setCodLancamento($rsLancamento->getCampo('cod_lancamento') );

                        if ( !$obErro->ocorreu() ) {
                            //insere Pagamento
                            $this->obTARRPagamento->setDado( "numeracao"            , $this->obRARRCarne->getNumeracao()          );
                            $this->obTARRPagamento->setDado( "ocorrencia_pagamento" , $this->inOcorrenciaPagamento                );
                            $this->obTARRPagamento->setDado( "cod_convenio"         , $this->obRARRCarne->obRMONConvenio->getCodigoConvenio() );
                            $this->obTARRPagamento->setDado( "data_pagamento"       , $this->getDataPagamento()                   );
                            $this->obTARRPagamento->setDado( "valor"                , $this->getValorPagamento()                  );
                            $this->obTARRPagamento->setDado( "inconsistente"        , FALSE                                       );
                            $this->obTARRPagamento->setDado( "observacao"           , $this->getObservacao()                      );
                            $this->obTARRPagamento->setDado( "cod_tipo"             , $this->obRARRTipoPagamento->getCodigoTipo() );
                            $this->obTARRPagamento->setDado( "numcgm"               , Sessao::read( "numCgm" )                             );
                            $obErro = $this->obTARRPagamento->inclusao( $boTransacao );                            

                            //insere informaçoes do lote de pagamento manual
                            if ( $boPagamentoAutomatico == FALSE AND !$obErro->ocorreu() ) {
                                $this->obTARRPagamentoLoteManual->setDado( "numeracao"            , $this->obRARRCarne->getNumeracao()          );
                                $this->obTARRPagamentoLoteManual->setDado( "ocorrencia_pagamento" , $this->inOcorrenciaPagamento                );
                                $this->obTARRPagamentoLoteManual->setDado( "cod_convenio"         , $this->obRARRCarne->obRMONConvenio->getCodigoConvenio() );
                                $this->obTARRPagamentoLoteManual->setDado( "cod_agencia"          , $this->obRMONAgencia->getCodAgencia() );
                                $this->obTARRPagamentoLoteManual->setDado( "cod_banco"            , $this->obRMONBanco->getCodBanco() );
                                $obErro = $this->obTARRPagamentoLoteManual->inclusao( $boTransacao );
                            }

                            //insere o processo se esse for setado
                            if ( $this->obRProcesso->getCodigoProcesso() AND $boPagamentoAutomatico == FALSE AND !$obErro->ocorreu() ) {
                                $this->obTARRProcessoPagamento->setDado( "numeracao"            , $this->obRARRCarne->getNumeracao()          );
                                $this->obTARRProcessoPagamento->setDado( "ocorrencia_pagamento" , $this->inOcorrenciaPagamento                );
                                $this->obTARRProcessoPagamento->setDado( "cod_convenio"         , $this->obRARRCarne->obRMONConvenio->getCodigoConvenio() );
                                $this->obTARRProcessoPagamento->setDado( "cod_processo"         , $this->obRProcesso->getCodigoProcesso()     );
                                $this->obTARRProcessoPagamento->setDado( "ano_exercicio"        , $this->obRProcesso->getExercicio()          );
                                $obErro = $this->obTARRProcessoPagamento->inclusao( $boTransacao );
                            }

                            //se o metodo estiver sendo usado pela baixa automatica,
                            //insere as informações do lote
                            if ( !$obErro->ocorreu() AND $boPagamentoAutomatico == TRUE ) {
                                $obErro = $this->salvarPagamentoLote( $boTransacao );
                            }
                        }
                    }

                    if ( !$obErro->ocorreu() ) {

                        //insere o valor PAGO para cada CALCULO na tabela PAGAMENTO_CALCULO
                        $nuValorPagoTmp = $this->getValorPagamento();
                        $arPagamentoCalculo = array();

                        $boLancamentoEconomico = FALSE;
                        $inCodLancamento       = '';

                        while ( !$rsLancamento->eof() ) {

                            $stTipoCarne      = $rsLancamento->getCampo('tipo_numeracao');
                            $dtDataVencimento = $rsLancamento->getCampo('vencimento');
                            $inCodCalculo     = $rsLancamento->getCampo('cod_calculo');

                            if ( !$rsVerificaCarne->eof() AND  $rsVerificaCarne->getCampo('valor') == 0 && $rsLancamento->getCampo('valor_parcela') == 0 ) {

                                $boLancamentoEconomico = TRUE;
                                $inCodLancamento       = $rsLancamento->getCampo('cod_lancamento');

                                if ( $this->getValorPagamento() ) {
                                    $arDataPagamento = explode( "/" , $this->getDataPagamento() );
                                    $dtPagamento =  $arDataPagamento[2]."-".$arDataPagamento[1]."-".$arDataPagamento[0];
                                    $this->obRARRCarne->recuperaValorParcelaJuroMulta( $rsParcelaJuroMulta, $rsLancamento->getCampo('vencimento'), $dtPagamento, $this->getValorPagamento(), $boTransacao );
                                    $arPagamentoEconomico = explode( "-" , $rsParcelaJuroMulta->getCampo('parcela_juro_multa') );
                                    $nuValorPagamentoCalculo = $arPagamentoEconomico[0];
                                    $nuValorPagamentoMulta   = $arPagamentoEconomico[1];
                                    $nuValorPagamentoJuro    = $arPagamentoEconomico[2];
                                } else {
                                    $nuValorPagamentoCalculo = 0;
                                    $nuValorPagamentoMulta   = 0;
                                    $nuValorPagamentoJuro    = 0;
                                }
 
                                //insere pagamento_calculo
                                $this->obTARRPagamentoCalculo->setDado( "numeracao"            , $this->obRARRCarne->getNumeracao()     );
                                $this->obTARRPagamentoCalculo->setDado( "cod_calculo"          , $rsLancamento->getCampo('cod_calculo') );
                                $this->obTARRPagamentoCalculo->setDado( "ocorrencia_pagamento" , $this->inOcorrenciaPagamento           );
                                $this->obTARRPagamentoCalculo->setDado( "cod_convenio"         , $this->obRARRCarne->obRMONConvenio->getCodigoConvenio() );
                                $this->obTARRPagamentoCalculo->setDado( "valor"                , $nuValorPagamentoCalculo );
                                $obErro = $this->obTARRPagamentoCalculo->inclusao( $boTransacao );
                                $nuTotal += $this->obTARRPagamentoCalculo->getDado("valor");

                                //insere pagamento_acrescimo (juro e multa)
                                //juro
                                if ($nuValorPagamentoJuro > 0) {

                                    //novo CC
                                    $obRMONCreditoAcrescimo = new RMONCreditoAcrescimo;
                                    $rsConsultaCredito->setPrimeiroElemento();
                                    while ( !$rsConsultaCredito->Eof() ) {
                                        if ( $rsConsultaCredito->getCampo("cod_calculo") == $rsLancamento->getCampo('cod_calculo') && $rsConsultaCredito->getCampo("numeracao") ==  $rsLancamento->getCampo('numeracao') ) {
                                            $obRMONCreditoAcrescimo->setCodCredito( $rsConsultaCredito->getCampo("cod_credito") );
                                            $obRMONCreditoAcrescimo->setCodEspecie( $rsConsultaCredito->getCampo("cod_especie") );
                                            $obRMONCreditoAcrescimo->setCodGenero( $rsConsultaCredito->getCampo("cod_genero") );
                                            $obRMONCreditoAcrescimo->setCodNatureza( $rsConsultaCredito->getCampo("cod_natureza") );
                                            $obRMONCreditoAcrescimo->setCodTipo(2);

                                            $obRMONCreditoAcrescimo->ListarAcrescimosDoCredito( $rsListaCreditoAcrescimo, $boTransacao );

                                            break;
                                        }

                                        $rsConsultaCredito->proximo();
                                    }

                                    $rsConsultaCredito->setPrimeiroElemento();
                                    //monetario _credito_acrescimo
                                    if ( !$rsListaCreditoAcrescimo->Eof() ) {
                                        $this->obTARRPagamentoAcrescimo->setDado( "numeracao"            , $this->obRARRCarne->getNumeracao()     );
                                        $this->obTARRPagamentoAcrescimo->setDado( "ocorrencia_pagamento" , $this->inOcorrenciaPagamento           );
                                        $this->obTARRPagamentoAcrescimo->setDado( "cod_calculo"          , $rsVerificaCarne->getCampo('cod_calculo') );
                                        $this->obTARRPagamentoAcrescimo->setDado( "cod_convenio"         , $this->obRARRCarne->obRMONConvenio->getCodigoConvenio() );
                                        $this->obTARRPagamentoAcrescimo->setDado( "cod_tipo"             , '2' );
                                        $this->obTARRPagamentoAcrescimo->setDado( "cod_acrescimo"        , $rsListaCreditoAcrescimo->getCampo("cod_acrescimo") );

                                        $this->obTARRPagamentoAcrescimo->setDado( "valor"                , $nuValorPagamentoJuro                   );
                                        $obErro = $this->obTARRPagamentoAcrescimo->inclusao( $boTransacao );
                                        $nuTotal += $this->obTARRPagamentoAcrescimo->getDado("valor");
                                    }
                                }

                                //multa
                                if ($nuValorPagamentoMulta > 0) {

                                    //novo CC
                                    $obRMONCreditoAcrescimo = new RMONCreditoAcrescimo;
                                    $rsConsultaCredito->setPrimeiroElemento();
                                    while ( !$rsConsultaCredito->Eof() ) {
                                        if ( $rsConsultaCredito->getCampo("cod_calculo") == $rsLancamento->getCampo('cod_calculo') && $rsConsultaCredito->getCampo("numeracao") ==  $rsLancamento->getCampo('numeracao') ) {
                                            $obRMONCreditoAcrescimo->setCodCredito( $rsConsultaCredito->getCampo("cod_credito") );
                                            $obRMONCreditoAcrescimo->setCodEspecie( $rsConsultaCredito->getCampo("cod_especie") );
                                            $obRMONCreditoAcrescimo->setCodGenero( $rsConsultaCredito->getCampo("cod_genero") );
                                            $obRMONCreditoAcrescimo->setCodNatureza( $rsConsultaCredito->getCampo("cod_natureza") );
                                            $obRMONCreditoAcrescimo->setCodTipo(3);

                                            $obRMONCreditoAcrescimo->ListarAcrescimosDoCredito( $rsListaCreditoAcrescimo, $boTransacao );

                                            break;
                                        }

                                        $rsConsultaCredito->proximo();
                                    }

                                    $rsConsultaCredito->setPrimeiroElemento();

                                    if ( !$rsListaCreditoAcrescimo->Eof() ) {
                                        $this->obTARRPagamentoAcrescimo->setDado( "numeracao"            , $this->obRARRCarne->getNumeracao()     );
                                        $this->obTARRPagamentoAcrescimo->setDado( "ocorrencia_pagamento" , $this->inOcorrenciaPagamento           );
                                        $this->obTARRPagamentoAcrescimo->setDado( "cod_calculo"          , $rsVerificaCarne->getCampo('cod_calculo') );
                                        $this->obTARRPagamentoAcrescimo->setDado( "cod_convenio"         , $this->obRARRCarne->obRMONConvenio->getCodigoConvenio() );
                                        $this->obTARRPagamentoAcrescimo->setDado( "cod_tipo"             , '3'                                     );
                                        $this->obTARRPagamentoAcrescimo->setDado( "cod_acrescimo"        , $rsListaCreditoAcrescimo->getCampo("cod_acrescimo") );
                                        $this->obTARRPagamentoAcrescimo->setDado( "valor"                , $nuValorPagamentoMulta                  );
                                        $obErro = $this->obTARRPagamentoAcrescimo->inclusao( $boTransacao );
                                        $nuTotal += $this->obTARRPagamentoAcrescimo->getDado("valor");
                                    }
                               }

                            } else {

                                $boParamentoIgual     = FALSE;
                                $nuValorCalculo       = $rsLancamento->getCampo('valor');                                

                                $this->obTARRPagamentoCalculo->setDado( "numeracao"            , $this->obRARRCarne->getNumeracao()     );
                                $this->obTARRPagamentoCalculo->setDado( "cod_calculo"          , $rsLancamento->getCampo('cod_calculo') );
                                $this->obTARRPagamentoCalculo->setDado( "ocorrencia_pagamento" , $this->inOcorrenciaPagamento           );
                                $this->obTARRPagamentoCalculo->setDado( "cod_convenio"         , $this->obRARRCarne->obRMONConvenio->getCodigoConvenio() );
                                if ( $rsLancamento->getCampo('valor_parcela') > 0 ) {
                                    /*
                                        - DESCONTO
                                        se o valor pago é MENOR que o valor da PARCELA
                                    */
                                    if ( $this->getValorPagamento() < $rsLancamento->getCampo('valor_parcela') ) {

                                        if ( $rsLancamento->getCampo('nr_parcela') == 0 ) {
                                            $nuValorCalculo = $rsLancamento->getCampo('valor');
                                        } else {
                                            $nuValorCalculo = round( $rsLancamento->getCampo('valor')/$rsLancamento->getCampo('total_parcelas') , 8);
                                        }
                                        if ( $rsLancamento->getCampo('valor') == 0 ) {
                                            $nuValorCalculo = 0;
                                        }
                                        if ($nuValorPagoTmp >= $nuValorCalculo AND $nuValorPagoTmp >= 0) {
                                            $this->obTARRPagamentoCalculo->setDado( "valor" , (floor($nuValorCalculo*100)/100) );
                                        } elseif ($nuValorPagoTmp > 0) {
                                            $this->obTARRPagamentoCalculo->setDado( "valor" , (floor($nuValorPagoTmp*100)/100) );
                                        } else {
                                            $this->obTARRPagamentoCalculo->setDado( "valor" , 0 );
                                        }
                                        $nuValorPagoTmp = $nuValorPagoTmp - $nuValorCalculo;
                                    }

                                    /*
                                        - ACRESCIMO
                                        se o valor pago é MAIOR que o valor da PARCELA
                                    */
                                    if ( $this->getValorPagamento() > $rsLancamento->getCampo('valor_parcela') ) {

                                        if ( $rsLancamento->getCampo('nr_parcela') == 1 && ( $rsLancamento->getCampo('valor') == $rsLancamento->getCampo('valor_lancamento') ) ) {
                                            $nuValorCalculo = $rsLancamento->getCampo('valor_parcela');
                                        } elseif ( $rsLancamento->getCampo('nr_parcela') == 1 AND $nuTotalCalculo > 0 ) {                                            

                                            $nuDiffTotalCalculo = $nuTotalCalculo - $rsLancamento->getCampo('valor_parcela');
                                            $nuValorCalculo = round( $rsLancamento->getCampo('valor') / $rsLancamento->getCampo('total_parcelas') , 8 );

                                            if ($nuDiffTotalCalculo < 0) {
                                                $nuDiffTotalCalculo = $nuDiffTotalCalculo * -1;
                                                $nuValorCalculo += $nuDiffTotalCalculo;
                                            } elseif ($nuDiffTotalCalculo > 0) {
                                                $nuValorCalculo -= $nuDiffTotalCalculo;
                                            }
                                            $nuTotalCalculo = 0;
                                        } else {                                            
                                            if ( $rsLancamento->getCampo('total_parcelas') > 0  && $rsLancamento->getCampo('nr_parcela') > 0 ) {                                                
                                                $nuValorCalculo = round( ($rsLancamento->getCampo('valor') / $rsLancamento->getCampo('total_parcelas')) ,8);                                            
                                            } else {
                                                //deve verificar se sofre desconto                                                
                                                $nuValorCalculo = $rsLancamento->getCampo('valor');
                                                $nuDescontoCalculo = round( $arDescontoCalculo[$rsLancamento->getCampo('cod_calculo')], 8 );
                                                if ($nuDescontoCalculo > 0) {
                                                    $nuValorCalculo -= $nuDescontoCalculo;
                                                }
                                            }
                                        }

                                        if ( $rsLancamento->getCampo('valor') == 0 ) {
                                            $nuValorCalculo = 0;
                                        }
                                        
                                        $this->obTARRPagamentoCalculo->setDado( "valor" , round($nuValorCalculo,2) );
                                        
                                        $nuValorPagoTmp = round (($nuValorPagoTmp - $nuValorCalculo),8);

                                        if ( $nuValorPagoTmp > 0 )  $boPagamentoAcrescimo = TRUE;
                                    }
                                } else {
                                    $this->obTARRPagamentoCalculo->setDado( "valor" , $this->getValorPagamento() );
                                    $nuValorPagoTmp = round (($nuValorPagoTmp - $nuValorCalculo),8);                                    

                                    if ( $nuValorPagoTmp > 0 ) $boPagamentoAcrescimo = true;

                                }

                                /*
                                    - PAGAMENTO DO VALOR EXATO DA PARCELA
                                */
                                if ( $this->getValorPagamento() == $rsLancamento->getCampo('valor_parcela') ) {

                                    $boParamentoIgual = TRUE;
                                    $boPagamentoAcrescimo = FALSE;
                                    $boPagamentoDiferenca = FALSE;
                                    if ( $rsLancamento->getCampo('nr_parcela') == 0 ) {
                                            $nuValorPercentCredito = round(($rsLancamento->getCampo('valor') / $rsLancamento->getCampo('valor_lancamento')),8);
                                            $nuValorCalculo = round( ($rsLancamento->getCampo('valor_parcela') * $nuValorPercentCredito),8);
                                    } else {
                                        if ( $rsLancamento->getCampo('nr_parcela') == 1 && ( $rsLancamento->getCampo('valor') == $rsLancamento->getCampo('valor_lancamento') ) ) {
                                            $nuValorCalculo = $rsLancamento->getCampo('valor_parcela');
                                        } elseif ( $rsLancamento->getCampo('nr_parcela') == 1 AND $nuTotalCalculo > 0 ) {

                                            $nuDiffTotalCalculo = $nuTotalCalculo - $rsLancamento->getCampo('valor_parcela');                                            
                                            $nuValorCalculo = round( $rsLancamento->getCampo('valor') / $rsLancamento->getCampo('total_parcelas'), 8 );                                            
                                            if ($nuDiffTotalCalculo < 0) {
                                                $nuDiffTotalCalculo = $nuDiffTotalCalculo * -1;
                                                $nuValorCalculo += $nuDiffTotalCalculo;
                                            } elseif ($nuDiffTotalCalculo > 0) {
                                                $nuValorCalculo -= $nuDiffTotalCalculo;
                                            }
                                            $nuTotalCalculo = 0;
                                        } else {
                                            $nuValorCalculo = round( $rsLancamento->getCampo('valor') / $rsLancamento->getCampo('total_parcelas') , 8 );
                                            //$nuValorCalculo = $rsLancamento->getCampo('valor');
                                            $nuDescontoCalculo = round( $arDescontoCalculo[$rsLancamento->getCampo('cod_calculo')], 8 );
                                            if ($nuDescontoCalculo > 0) {
                                                $nuValorCalculo -= $nuDescontoCalculo;
                                            }
                                        }
                                    }
                                    if ( $rsLancamento->getCampo('valor') == 0 ) {
                                        $nuValorCalculo = 0;
                                    }

                                    //tecnica para cortar as casas decimais sem realizar arrendodamento
                                    $this->obTARRPagamentoCalculo->setDado( "valor" , (floor($nuValorCalculo*100)/100) );
                                    $nuValorPagoTmp = 0.00;
                                }

                                $nuTotal += $this->obTARRPagamentoCalculo->getDado("valor");
                                $obErro = $this->obTARRPagamentoCalculo->inclusao( $boTransacao );                                
                                if ( $obErro->ocorreu() ){
                                    return $obErro;
                                }
                            }
                            $rsLancamento->proximo();
                        }
                        $rsLancamento->setPrimeiroElemento();

                        //armazena ultimo informacao de pagamento_calculo para caso haja diferenca de pagamento
                        //essa diferenca seja lancçada nesse calculo
                        $arPagamentoCalculo['numeracao']            = $this->obTARRPagamentoCalculo->getDado('numeracao');
                        $arPagamentoCalculo['cod_calculo']          = $this->obTARRPagamentoCalculo->getDado('cod_calculo');
                        $arPagamentoCalculo['ocorrencia_pagamento'] = $this->obTARRPagamentoCalculo->getDado('ocorrencia_pagamento');
                        $arPagamentoCalculo['cod_convenio']         = $this->obTARRPagamentoCalculo->getDado('cod_convenio');
                        $arPagamentoCalculo['valor']                = $this->obTARRPagamentoCalculo->getDado('valor');                        
                    }

                    //faz verificacoes de ACRESCIMO
                    //Necessita vefificar se foi pago mais do que a o valor original da parcela para separar os acrescimos
                    //Valor da diferenca precisa ser maior que zero, para separar nos acrescimos correspondentes
                    //utilizando as formulas de calculo de acrescimo (juro/multa)
                    if ($boPagamentoDiferenca == FALSE && $boPagamentoAcrescimo == TRUE  &&  !$obErro->ocorreu() ) {

                        //insere juro
                        while ( !$rsConsultaCredito->eof() ) {
                            if ( ($nuValorPagoTmp > 0 ) && ($rsConsultaCredito->getCampo('valor_credito_juros') > 0) ) {                                
                                //verifica se o valor que foi pago a mais é maior que o valor de juros ou menor e insere o valor que tem                                
                                if ($nuValorPagoTmp >= $rsConsultaCredito->getCampo('valor_credito_juros')) {
                                    $this->obTARRPagamentoAcrescimo->setDado( "valor" , $rsConsultaCredito->getCampo('valor_credito_juros') );
                                }else{                                    
                                    $this->obTARRPagamentoAcrescimo->setDado( "valor" , (floor($nuValorPagoTmp*100)/100) );
                                }
                                $nuValorPagoTmp -= $rsConsultaCredito->getCampo('valor_credito_juros');                                    
                            } else {
                                $boPagamentoDiferenca = TRUE;
                                $this->obTARRPagamentoAcrescimo->setDado( "valor" , 0.00 );
                            }

                            $nuTotal += $this->obTARRPagamentoAcrescimo->getDado('valor');

                            $obRMONCreditoAcrescimo = new RMONCreditoAcrescimo;
                            $obRMONCreditoAcrescimo->setCodCredito( $rsConsultaCredito->getCampo("cod_credito") );
                            $obRMONCreditoAcrescimo->setCodEspecie( $rsConsultaCredito->getCampo("cod_especie") );
                            $obRMONCreditoAcrescimo->setCodGenero( $rsConsultaCredito->getCampo("cod_genero") );
                            $obRMONCreditoAcrescimo->setCodNatureza( $rsConsultaCredito->getCampo("cod_natureza") );
                            $obRMONCreditoAcrescimo->setCodTipo(2);

                            $obRMONCreditoAcrescimo->ListarAcrescimosDoCredito( $rsListaCreditoAcrescimo, $boTransacao );

                            if ( !$rsListaCreditoAcrescimo->Eof() ) {
                                $this->obTARRPagamentoAcrescimo->setDado( "numeracao"            , $this->obRARRCarne->getNumeracao()     );
                                $this->obTARRPagamentoAcrescimo->setDado( "ocorrencia_pagamento" , $this->inOcorrenciaPagamento           );
                                $this->obTARRPagamentoAcrescimo->setDado( "cod_calculo"          , $rsConsultaCredito->getCampo('cod_calculo') );
                                $this->obTARRPagamentoAcrescimo->setDado( "cod_convenio"         , $this->obRARRCarne->obRMONConvenio->getCodigoConvenio() );
                                $this->obTARRPagamentoAcrescimo->setDado( "cod_tipo"             , '2'                                     );
                                $this->obTARRPagamentoAcrescimo->setDado( "cod_acrescimo"        , $rsListaCreditoAcrescimo->getCampo("cod_acrescimo") );
                                $obErro = $this->obTARRPagamentoAcrescimo->inclusao( $boTransacao );
                            }
                            
                            $rsConsultaCredito->proximo();
                        }
                        $rsConsultaCredito->setPrimeiroElemento();
                        
                        //insere multa
                        while ( !$rsConsultaCredito->eof() ) {
                            if ( ($nuValorPagoTmp > 0 ) && $rsConsultaCredito->getCampo('valor_credito_multa') > 0 ) {
                                //verifica se o valor que foi pago a mais é maior que o valor de multa ou menor e insere o valor que tem
                                if ( $nuValorPagoTmp >= $rsConsultaCredito->getCampo('valor_credito_multa') ) {
                                    $this->obTARRPagamentoAcrescimo->setDado( "valor" , $rsConsultaCredito->getCampo('valor_credito_multa') );
                                }else{
                                    $this->obTARRPagamentoAcrescimo->setDado( "valor" , (floor($nuValorPagoTmp*100)/100) );
                                }
                                $nuValorPagoTmp -= $rsConsultaCredito->getCampo('valor_credito_multa');
                            } else {
                                $boPagamentoDiferenca = TRUE;
                                $this->obTARRPagamentoAcrescimo->setDado( "valor" , 0.00 );
                            }

                            $nuTotal += $this->obTARRPagamentoAcrescimo->getDado('valor');

                            $obRMONCreditoAcrescimo = new RMONCreditoAcrescimo;
                            $obRMONCreditoAcrescimo->setCodCredito( $rsConsultaCredito->getCampo("cod_credito") );
                            $obRMONCreditoAcrescimo->setCodEspecie( $rsConsultaCredito->getCampo("cod_especie") );
                            $obRMONCreditoAcrescimo->setCodGenero( $rsConsultaCredito->getCampo("cod_genero") );
                            $obRMONCreditoAcrescimo->setCodNatureza( $rsConsultaCredito->getCampo("cod_natureza") );
                            $obRMONCreditoAcrescimo->setCodTipo(3);

                            $obRMONCreditoAcrescimo->ListarAcrescimosDoCredito( $rsListaCreditoAcrescimo, $boTransacao );

                            if ( !$rsListaCreditoAcrescimo->Eof() ) {
                                $this->obTARRPagamentoAcrescimo->setDado( "numeracao"            , $this->obRARRCarne->getNumeracao()     );
                                $this->obTARRPagamentoAcrescimo->setDado( "ocorrencia_pagamento" , $this->inOcorrenciaPagamento           );
                                $this->obTARRPagamentoAcrescimo->setDado( "cod_calculo"          , $rsConsultaCredito->getCampo('cod_calculo') );
                                $this->obTARRPagamentoAcrescimo->setDado( "cod_convenio"         , $this->obRARRCarne->obRMONConvenio->getCodigoConvenio() );
                                $this->obTARRPagamentoAcrescimo->setDado( "cod_tipo"             , '3'                                     );
                                $this->obTARRPagamentoAcrescimo->setDado( "cod_acrescimo"        , $rsListaCreditoAcrescimo->getCampo("cod_acrescimo") );
                                $obErro = $this->obTARRPagamentoAcrescimo->inclusao( $boTransacao );
                            }
                            
                            $rsConsultaCredito->proximo();
                        }
                        $rsConsultaCredito->setPrimeiroElemento();

                        //insere correcao
                        while ( !$rsConsultaCredito->eof() ) {
                            if ( $nuValorPagoTmp > 0 && $rsConsultaCredito->getCampo('valor_credito_correcao') > 0 ) {
                                //verifica se o valor que foi pago a mais é maior que o valor de correcao ou menor e insere o valor que tem
                                if ( $nuValorPagoTmp >= $rsConsultaCredito->getCampo('valor_credito_correcao') ) {                                    
                                    $this->obTARRPagamentoAcrescimo->setDado( "valor" , $rsConsultaCredito->getCampo('valor_credito_correcao') );
                                    $nuTotalCaluloPagamentoDiff = $rsConsultaCredito->getCampo('valor_credito_correcao');
                                }else{
                                    $this->obTARRPagamentoAcrescimo->setDado( "valor" , (round(($nuValorPagoTmp*100),8)/100) );
                                    $nuTotalCaluloPagamentoDiff = $nuValorPagoTmp;
                                }
                                $nuValorPagoTmp -= $rsConsultaCredito->getCampo('valor_credito_correcao');
                            } else {
                                $boPagamentoDiferenca = TRUE;
                                $this->obTARRPagamentoAcrescimo->setDado( "valor" , 0.00 );
                                $nuTotalCaluloPagamentoDiff = 0.00;
                            }

                            $nuTotal += $nuTotalCaluloPagamentoDiff;

                            $obRMONCreditoAcrescimo = new RMONCreditoAcrescimo;
                            $obRMONCreditoAcrescimo->setCodCredito( $rsConsultaCredito->getCampo("cod_credito") );
                            $obRMONCreditoAcrescimo->setCodEspecie( $rsConsultaCredito->getCampo("cod_especie") );
                            $obRMONCreditoAcrescimo->setCodGenero( $rsConsultaCredito->getCampo("cod_genero") );
                            $obRMONCreditoAcrescimo->setCodNatureza( $rsConsultaCredito->getCampo("cod_natureza") );
                            $obRMONCreditoAcrescimo->setCodTipo(1);

                            $obRMONCreditoAcrescimo->ListarAcrescimosDoCredito( $rsListaCreditoAcrescimo, $boTransacao );

                            if ( !$rsListaCreditoAcrescimo->Eof() ) {
                                $this->obTARRPagamentoAcrescimo->setDado( "numeracao"            , $this->obRARRCarne->getNumeracao()     );
                                $this->obTARRPagamentoAcrescimo->setDado( "ocorrencia_pagamento" , $this->inOcorrenciaPagamento           );
                                $this->obTARRPagamentoAcrescimo->setDado( "cod_calculo"          , $rsConsultaCredito->getCampo('cod_calculo') );
                                $this->obTARRPagamentoAcrescimo->setDado( "cod_convenio"         , $this->obRARRCarne->obRMONConvenio->getCodigoConvenio() );
                                $this->obTARRPagamentoAcrescimo->setDado( "cod_tipo"             , '1'                                     );
                                $this->obTARRPagamentoAcrescimo->setDado( "cod_acrescimo"        , $rsListaCreditoAcrescimo->getCampo("cod_acrescimo") );
                                $obErro = $this->obTARRPagamentoAcrescimo->inclusao( $boTransacao );                                
                            }

                            $rsConsultaCredito->proximo();
                        }

                    }
                    unset( $rsConsultaCredito );

                    //Se depois de inserir todos os acrescimos ainda sobrar valor possitivo significa que foi pago um valor maior do que o esperado
                    //então insere sobra deste valor na tabela pagamento_diferenca                    

                    if ( $nuValorPagoTmp > 0 AND !$obErro->ocorreu() ) {

                        //SEPARAR POR CALCULO
                        $inNumCalculo = $rsLancamento->getNumLinhas();
                        $nuValorDifCalculo = $nuValorPagoTmp / $inNumCalculo;
                        $nuValorDifCalculo = round( $nuValorDifCalculo, 2 );

                        $cont = 0;
                        $rsLancamento->setPrimeiroElemento();
                        while ( !$rsLancamento->eof() ) {
                            if ( $rsLancamento->getCampo('valor_lancamento') > 0 ) {
                                $ProporcaoDoCalculo =  round( $rsLancamento->getCampo('valor') * 100 / $rsLancamento->getCampo('valor_lancamento'), 2);
                                //$ProporcaoDoCalculo = number_format ( $ProporcaoDoCalculo, 2 );
                                $valorCalculoDiferenca = round( $nuValorPagoTmp * $ProporcaoDoCalculo/100, 2);
                                //$valorCalculoDiferenca = number_format ( $valorCalculoDiferenca, 2 );
                            } else {
                                $valorCalculoDiferenca = 0.00;
                            }

                            if ($nuValorDifCalculo > 0) {

                                $this->obTARRPagamentoDiferenca->setDado( "numeracao"            , $this->obRARRCarne->getNumeracao() );
                                $this->obTARRPagamentoDiferenca->setDado( "ocorrencia_pagamento" , $this->inOcorrenciaPagamento       );
                                $this->obTARRPagamentoDiferenca->setDado( "cod_convenio"         , $this->obRARRCarne->obRMONConvenio->getCodigoConvenio() );
                                $this->obTARRPagamentoDiferenca->setDado( "cod_calculo"          , $rsLancamento->getCampo('cod_calculo') );
                                $this->obTARRPagamentoDiferenca->setDado( "valor"                , $valorCalculoDiferenca );
                                $obErro = $this->obTARRPagamentoDiferenca->inclusao( $boTransacao );                                
                                $nuTotal += $this->obTARRPagamentoDiferenca->getDado("valor");

                            }
                            $rsLancamento->proximo();
                        }
                    }
 
                    if ($nuValorPagoTmp < 0) {
                        $nuValorPagoTmp = $nuValorPagoTmp * -1;
                    }

                    //verifica se a parcela é de um calculo do economico ou imobiliario
                    if ( !$obErro->ocorreu() ) {

                        if ($boCalculoEconomico == TRUE) {
                            $stTipoCarne = "E";
                            $stParametro = "grupo_diferenca_econ";
                            $stParametroJuroMulta = "grupo_diferenca_acrescimo_econ";
                            $inInscricao = $rsVerificaCarne->getCampo('inscricao_economica');
                        } else {
                            $this->obRARRCarne->obRARRParcela->roRARRLancamento->roRARRCalculo->setCodCalculo( $inCodCalculo );
                            $this->obRARRCarne->verificaCarneImobiliario( $rsVerificaCarneImobiliario, $boTransacao );
                            if ( !$rsVerificaCarneImobiliario->eof() ) {
                                $stTipoCarne = "I";
                                $inInscricao = $rsVerificaCarneImobiliario->getCampo('inscricao_municipal');
                                $stParametro = "grupo_diferenca_imob";
                                $stParametroJuroMulta = "grupo_diferenca_acrescimo_imob";
                            } else {
                                $stParametro = "grupo_diferenca_geral";
                                $stParametroJuroMulta = "grupo_diferenca_acrescimo_geral";
                            }
                        }

                        //Só executa Lancamento quando for BAIXA AUTOMATICA
                        if ($boPagamentoAutomatico == TRUE) {

                            //se foi pago a MENOS, cria um novo CALCULO, LANCAMENTO E PARCELA para esse valor
                            if ($boPagamentoMenor == TRUE AND $boLancamentoEconomico == FALSE) {
                                if ($boParcelaVencida == TRUE) {
                                    if ( $nuValorPagoTmp >= $this->obRARRConfiguracao->getMinimoLancamentoAutomatico() ) {
                                        $obErro = $this->executaLancamento( $stParametro, $stTipoCarne, $inInscricao, $nuValorPagoTmp, $inParcela, $inGrupoCreditoOriginal, $dtDataVencimento, $boTransacao );
                                    }
                                    if ( $nuTotalJuroMulta > 0 AND $nuTotalJuroMulta >= $this->obRARRConfiguracao->getMinimoLancamentoAutomatico() AND !$obErro->ocorreu() ) {
                                        $obErro = $this->executaLancamento( $stParametroJuroMulta, $stTipoCarne, $inInscricao, $nuTotalJuroMulta, $inParcela, $inGrupoCreditoOriginal, $dtDataVencimento, $boTransacao );
                                    }
                                } else {
                                    if ( $nuValorPagoTmp >= $this->obRARRConfiguracao->getMinimoLancamentoAutomatico() ) {                                        
                                        $obErro = $this->executaLancamento( $stParametro, $stTipoCarne, $inInscricao, $nuValorPagoTmp, $inParcela, $inGrupoCreditoOriginal, $dtDataVencimento, $boTransacao );
                                    }
                                }
                            }
                            //se foi pago a mais, verifica se o valor pago a mais cobre o total de jura e multa
                            //senao, cria lancamento/calculp/parcela com o valor que faltou para cobrir juro e multa
                            elseif ( $boParcelaVencida == TRUE AND ( $nuValorTotalParcela > $nuTotal ) ) {
                                $nuValorDifJuroMulta = $nuValorTotalParcela - $nuTotal;
                                if ( $nuValorDifJuroMulta >= $this->obRARRConfiguracao->getMinimoLancamentoAutomatico() ) {
                                    $obErro = $this->executaLancamento( $stParametroJuroMulta, $stTipoCarne, $inInscricao, $nuValorDifJuroMulta, $inParcela, $inGrupoCreditoOriginal, $dtDataVencimento, $boTransacao );
                                }
                            }

                            //se foi pago o valor exato da parcela e a parcela esta vencida
                            //cria lancamento/calculp/parcela com o valor que faltou para cobrir juro e multa
                            elseif ($boParcelaVencida == TRUE AND $boParamentoIgual == TRUE AND $nuTotalJuroMulta > 0) {
                                if ( $nuTotalJuroMulta >= $this->obRARRConfiguracao->getMinimoLancamentoAutomatico() ) {
                                    $obErro = $this->executaLancamento( $stParametro, $stTipoCarne, $inInscricao, $nuTotalJuroMulta, $inParcela, $inGrupoCreditoOriginal, $dtDataVencimento, $boTransacao );
                                }
                            }
                        }
                    }

                    /*
                        CANCELAMENTOS
                        - se for pagamento de PARCELA UNICA, CANCELA AS DEMAIS PARCELAS
                        - se nao CANCELA AS PARCELAS UNICAS
                    */

                    if ( $this->obRARRTipoPagamento->getPagamento() == "t" AND !$obErro->ocorreu() ) {
                        $rsLancamento->setPrimeiroElemento();
                        $inNumeracaoOriginal = $this->obRARRCarne->getNumeracao();
                        $this->obRARRCarne->obRARRParcela->roRARRLancamento->setCodLancamento( $rsLancamento->getCampo('cod_lancamento') );
                        $this->obRARRCarne->obRARRParcela->setNrParcela                      ( $rsLancamento->getCampo('nr_parcela') );
                        $this->obRARRCarne->obRARRParcela->setCodParcela                    ( $rsLancamento->getCampo('cod_parcela') );
                        $this->obRARRCarne->listaParcelasLancamento( $rsParcelasLancamento, $boTransacao );

                        if ( $rsLancamento->getCampo('nr_parcela') == 0 ) {
                            $this->obRARRCarne->setCodMotivo(100);
                        } else {
                            $this->obRARRCarne->setCodMotivo(101);
                        }

                        $inX = 0;
                        $arCarnesSessao = array();
                        while ( !$rsParcelasLancamento->eof() ) {
                            if ( $rsParcelasLancamento->getCampo('devolucao') == 'f' ) {
                                $arCarnesSessao[$inX]['stNumeracao'] = $rsParcelasLancamento->getCampo('numeracao');
                                $arCarnesSessao[$inX]['cod_convenio'] = $rsParcelasLancamento->getCampo('cod_convenio');
                                $arCarnesSessao[$inX]['inCodMotivo'] = $this->obRARRCarne->getCodMotivo();

                                $inX++;
                            }

                            $rsParcelasLancamento->proximo();
                        }

                        Sessao::write( "Carnes", $arCarnesSessao );
                        $obErro = $this->obRARRCarne->devolverCarne( $boTransacao ); //cc

                        unset( $rsParcelasLancamento );
                        unset( $rsVerificaPagamento  );

                        $this->obRARRCarne->setNumeracao( $inNumeracaoOriginal );

                    } else {
                        $rsLancamento->setPrimeiroElemento();
                        if ( !$obErro->ocorreu() ) {
                            $this->obRARRCarne->obRARRParcela->roRARRLancamento->setCodLancamento( $rsLancamento->getCampo('cod_lancamento') );
                            $this->obRARRCarne->obRARRParcela->setNrParcela                      ( $rsLancamento->getCampo('nr_parcela') );
                            $this->obRARRCarne->obRARRParcela->setCodParcela                     ( $rsLancamento->getCampo('cod_parcela') );
                            $obErro = $this->obRARRCarne->listaParcelasLancamento( $rsParcelasLancamento, $boTransacao );

                            if ( !$obErro->ocorreu() ) {
                                while ( !$rsParcelasLancamento->eof() ) {
                                    if ( $rsParcelasLancamento->getCampo('devolucao') == 'f' AND $rsParcelasLancamento->getCampo('pago') == 'f' ) {
                                        $this->obTARRPagamento->setDado( "numeracao"            , $rsParcelasLancamento->getCampo('numeracao')    );
                                        $this->obTARRPagamento->setDado( "ocorrencia_pagamento" , 1                                               );
                                        $this->obTARRPagamento->setDado( "cod_convenio"         , $rsParcelasLancamento->getCampo('cod_convenio') );
                                        $this->obTARRPagamento->setDado( "data_pagamento"       , $this->getDataPagamento()                       );
                                        $this->obTARRPagamento->setDado( "valor"                , 0                                               );
                                        $this->obTARRPagamento->setDado( "inconsistente"        , FALSE                                           );
                                        $this->obTARRPagamento->setDado( "observacao"           , $this->getObservacao()                          );
                                        $this->obTARRPagamento->setDado( "cod_tipo"             , $this->obRARRTipoPagamento->getCodigoTipo()     );
                                        $this->obTARRPagamento->setDado( "numcgm"               , Sessao::read( "numCgm" )                        );
                                        $obErro = $this->obTARRPagamento->inclusao( $boTransacao );

                                        if ( $this->obRProcesso->getCodigoProcesso() AND $boPagamentoAutomatico == FALSE AND !$obErro->ocorreu() ) {
                                            $this->obTARRProcessoPagamento->setDado( "numeracao"            , $rsParcelasLancamento->getCampo('numeracao')    );
                                            $this->obTARRProcessoPagamento->setDado( "ocorrencia_pagamento" , 1                                               );
                                            $this->obTARRProcessoPagamento->setDado( "cod_convenio"         , $rsParcelasLancamento->getCampo('cod_convenio') );
                                            $this->obTARRProcessoPagamento->setDado( "cod_processo"         , $this->obRProcesso->getCodigoProcesso()         );
                                            $this->obTARRProcessoPagamento->setDado( "ano_exercicio"        , $this->obRProcesso->getExercicio()              );
                                            $obErro = $this->obTARRProcessoPagamento->inclusao( $boTransacao );
                                        }
                                    }
                                    $rsParcelasLancamento->proximo();

                                }
                            }
                        }
                    }

                    if ($boPagamentoAutomatico == TRUE) {
                        $this->obRARRCarne->listaCarnesParaCancelar( $rsCarnesParaCancelar, $boTransacao );
                        while ( !$rsCarnesParaCancelar->Eof() ) {
                            $this->obTARRCarneDevolucao->setDado( "numeracao", $rsCarnesParaCancelar->getCampo('numeracao')    );
                            $this->obTARRCarneDevolucao->setDado( "cod_convenio", $rsCarnesParaCancelar->getCampo('cod_convenio') );
                            $this->obTARRCarneDevolucao->setDado( "cod_motivo", 10 );
                            $this->obTARRCarneDevolucao->setDado( "dt_devolucao", date("d/m/Y") );
                            $obErro = $this->obTARRCarneDevolucao->inclusao( $boTransacao );

                            $rsCarnesParaCancelar->proximo();
                        }

                        $this->obTARRCarneDevolucao->setDado( "numeracao"    , $this->obRARRCarne->getNumeracao() );
                        $this->obTARRCarneDevolucao->setDado( "cod_convenio" , $this->obRARRCarne->obRMONConvenio->getCodigoConvenio() );
                        $this->obTARRCarneDevolucao->exclusao( $boTransacao );
                    }

                    /*
                        FIM CANCELAMENTOS
                    */
                }

            /*
                - DEVOLUCAO
                insere as informacoes de devolucao na tabela CARNE_DEVOLUCAO
            */
            } else {
                $this->obTARRCarneDevolucao->setDado( "numeracao"    , $this->obRARRCarne->getNumeracao() );
                $this->obTARRCarneDevolucao->setDado( "dt_devolucao" , $this->getDataPagamento()          );
                $this->obTARRCarneDevolucao->setDado( "cod_motivo"   , 7                                  );
                $this->obTARRCarneDevolucao->setDado( "cod_convenio" , $this->obRARRCarne->obRMONConvenio->getCodigoConvenio() );
                $obErro = $this->obTARRCarneDevolucao->inclusao( $boTransacao );
                $boParcelaPaga = TRUE;
            }
        }
    }

    //se existe diferença entre o valor pago e a soma de pagamento_calculo + pagamento_acrescimo + pagamento_diferenca
    //adiciona ou subtrai a diferença no ultimo pagamento_calculo inserido
    if ( !$obErro->ocorreu() ) {
        $nuTotal = round($nuTotal,8);
        $nuTotalPag = round($this->getValorPagamento(),8);
        $x = bcsub($nuTotal,$nuTotalPag,2);

        if ($x != 0.00) {            
            $stFiltroCalculo = "WHERE numeracao = '".$arPagamentoCalculo['numeracao']."'
                                AND cod_convenio = ".$arPagamentoCalculo['cod_convenio']."
                                AND ocorrencia_pagamento = ".$arPagamentoCalculo['ocorrencia_pagamento']." 
                                AND valor > 0";
            $stOrdemCalculo = "ORDER BY cod_calculo DESC LIMIT 1";
            $obErro = $this->obTARRPagamentoCalculo->recuperaTotalPagamentoCalculo($rsTotalPagamentoCalculo, $stFiltroCalculo, $stOrdemCalculo, $boTransacao );            
            
            if ( !$obErro->ocorreu() ) {                
                if ($x > 0) {
                    $nuValorDif = $rsTotalPagamentoCalculo->getCampo('valor') - abs($x);    
                }else{
                    $nuValorDif = $rsTotalPagamentoCalculo->getCampo('valor') + abs($x);
                }
                
                $nuValorDif = (round($nuValorDif*100)/100);
                
                $this->obTARRPagamentoCalculo->setDado( "cod_calculo"          , $rsTotalPagamentoCalculo->getCampo('cod_calculo') );
                $this->obTARRPagamentoCalculo->setDado( "numeracao"            , $rsTotalPagamentoCalculo->getCampo('numeracao') );
                $this->obTARRPagamentoCalculo->setDado( "ocorrencia_pagamento" , $rsTotalPagamentoCalculo->getCampo('ocorrencia_pagamento') );
                $this->obTARRPagamentoCalculo->setDado( "cod_convenio"         , $rsTotalPagamentoCalculo->getCampo('cod_convenio') );
                $this->obTARRPagamentoCalculo->setDado( "valor"                , $nuValorDif );
                
                $obErro = $this->obTARRPagamentoCalculo->alteracao( $boTransacao );
            }
        }
    }
    if ($obErro->ocorreu()) {
        return $obErro;
    }

        //se for uma baixa de ISSQN do economico executa a rotina para alterar valor LANCAMENTO, CALCULO E PARCELA zerados.
        if ($boLancamentoEconomico && $nuValorPagamentoCalculo) {
            $this->obRARRCarne->obRARRParcela->roRARRLancamento->atualizaLancamento( $inCodLancamento, $this->obRARRCarne->getNumeracao(), $nuValorPagamentoCalculo, $boTransacao );
        }

        if ($boPagamentoAutomatico == FALSE) {
            // se for fechar a baixa manual
            if ($boFechaBaixaManual) {
                $this->efetuarFechamentoManual( $boTransacao );
            }
            $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTARRPagamento );
        }
        //Se a Parcela ja foi paga, seta nova mensagem de confirmação
        if ( $boParcelaPaga AND !$obErro->ocorreu() AND $boPagamentoAutomatico == FALSE ) {
            $obErro->setDescricao( "A parcela informada já foi paga! (Numeração: ".$this->obRARRCarne->getNumeracao().")" );
        }

    }

    return $obErro;
}

/**
    * Efetua baixa automatica de Pagamento
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function efetuarPagamentoAutomatico($boTransacao = "")
{
    $obErro = $this->obTARRLote->proximoCod( $this->inCodLote , $boTransacao );
    if (!$obErro->ocorreu() ) {
        $this->obTARRLote->setDado( "cod_lote"     , $this->inCodLote );
        $this->obTARRLote->setDado( "exercicio"    , $this->stExercicio );
        $this->obTARRLote->setDado( "numcgm"       , Sessao::read( "numCgm" ) );
        $this->obTARRLote->setDado( "data_lote"    , $this->dtDataLote );
        $this->obTARRLote->setDado( "cod_banco"    , $this->obRMONBanco->getCodBanco() );
        $this->obTARRLote->setDado( "cod_agencia"  , $this->obRMONAgencia->getCodAgencia() );
        $this->obTARRLote->setDado( "automatico"   , true );
        $obErro = $this->obTARRLote->inclusao($boTransacao);
        if (!$obErro->ocorreu() ) {
            $this->obTARRLoteArquivo->setDado ( 'cod_lote'    , $this->inCodLote  );
            $this->obTARRLoteArquivo->setDado ( 'exercicio'   , $this->stExercicio);
            $this->obTARRLoteArquivo->setDado ( 'md5sum'      , $this->stMd5Sum   );
            $this->obTARRLoteArquivo->setDado ( 'header'      , $this->stHeader   );
            $this->obTARRLoteArquivo->setDado ( 'footer'      , $this->stFooter   );
            $this->obTARRLoteArquivo->setDado ( 'nom_arquivo' , $this->stNomArquivo );
            $obErro = $this->obTARRLoteArquivo->inclusao($boTransacao);
        }
    }

    return $obErro;
}

/**
    * Executa rotina para criacao de lancamento/calculo/parcelas da Diferença de Pagamento
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function executaLancamento($stParametro, $stTipoCarne, $inInscricao, $nuValor, $inParcela, $inGrupoCreditoOriginal, $dtDataVencimento, $boTransacao = "")
{
    $arDataVencimento = explode( "-" , $dtDataVencimento );

    //se for pagamento de acrescimo, a data de vencimento deve ser o ultimo dia do mes em que a parcela esta sendo paga
    if ($stParametro == 'grupo_diferenca_acrescimo_imob' OR $stParametro == 'grupo_diferenca_acrescimo_econ' OR $stParametro == 'grupo_diferenca_acrescimo_geral') {

        $arDataPagamento = explode( "/" , $this->getDataPagamento() );
        $arDataVencimento[0] = $arDataPagamento[2];
        $arDataVencimento[1] = $arDataPagamento[1];
        $arDataVencimento[2] = $arDataPagamento[0];

        switch ($arDataVencimento[1]) {
            case '01':
            case '03':
            case '05':
            case '07':
            case '08':
            case '10':
            case '12':
                $arDataVencimento[2] = '31';
            break;
            case '04':
            case '06':
            case '09':
            case '11':
                $arDataVencimento[2] = '30';
            break;
            case '02':
                $arDataVencimento[2] = '28';
            break;
        }

    }

    $dtDataVencimento = $arDataVencimento[2]."/".$arDataVencimento[1]."/".$arDataVencimento[0];
    $dtDataVencimentoOrdem = $arDataVencimento[0].$arDataVencimento[1].$arDataVencimento[2];

    //seta variavel de sessao com informacao das parcelas
    $arParcelasSessao = array();
    $arParcelasSessao[0]['inIndice']              = '0';
    $arParcelasSessao[0]['stTipoParcela']         = 'Baixa';
    $arParcelasSessao[0]['data_vencimento']       = $dtDataVencimento;
    $arParcelasSessao[0]['flDesconto']            = '0,00';
    $arParcelasSessao[0]['stTipoDesconto']        = 'Percentual';
    $arParcelasSessao[0]['dtVencimentoOrdenacao'] = $dtDataVencimentoOrdem;
    Sessao::write( "parcelas", $arParcelasSessao );
    $arValoresCreditosSessao = array();
    $arValoresCreditosSessao[1] = $nuValor;
    Sessao::write( "ValoresCreditos", $arValoresCreditosSessao );
    $this->obRARRConfiguracao->setParametro( $stParametro );

    $this->obRARRConfiguracao->consultarGrupoCredito( $inGrupoCredito, $boTransacao );
    $arTMPdados = explode ("/", $inGrupoCredito);
    //$inGrupoCredito = $arTMPdados[0];

    $this->obRARRCarne->obRARRParcela->roRARRLancamento->roRARRCalculo->obRARRGrupo->setExercicio( $arTMPdados[1] );
    $this->obRARRCarne->obRARRParcela->roRARRLancamento->roRARRCalculo->obRARRGrupo->setCodGrupo( $arTMPdados[0] );

    if ($stTipoCarne == "I") {
        //seta INSCRICAO MUNICIPAL
        $this->obRARRCarne->obRARRParcela->roRARRLancamento->obRCIMImovel->setNumeroInscricao( $inInscricao );
        $this->obRARRCarne->obRARRParcela->roRARRLancamento->obRCEMInscricaoEconomica->setInscricaoEconomica('');
    } elseif ($stTipoCarne == "E") {
        //seta INSCRICAO ECONOMICA
        $this->obRARRCarne->obRARRParcela->roRARRLancamento->obRCEMInscricaoEconomica->setInscricaoEconomica( $inInscricao );
        $this->obRARRCarne->obRARRParcela->roRARRLancamento->obRCIMImovel->setNumeroInscricao('');
    } else {
    }

    $obErro = $this->obRARRCarne->obRARRParcela->roRARRLancamento->roRARRCalculo->obRARRGrupo->listarCreditos( $rsCreditosGrupo, $boTransacao );

    if ( !$obErro->ocorreu() ) {
        //seta valores do lancamento        
        $this->obRARRCarne->obRARRParcela->roRARRLancamento->setDataVencimento( $dtDataVencimento );
        $this->obRARRCarne->obRARRParcela->roRARRLancamento->setTotalParcelas ( 1                 );
        $this->obRARRCarne->obRARRParcela->roRARRLancamento->setValor         ( $nuValor          );
        $this->obRARRCarne->obRARRParcela->roRARRLancamento->setObservacao    (
                                                                                "Carne Original: ".$this->obRARRCarne->getNumeracao()." \n
                                                                                 Parcela: ".$inParcela." \n
                                                                                 Grupo de Crédito: ".$inGrupoCreditoOriginal
                                                                              );
        //seta valroes para o calculo
        $this->obRARRCarne->obRARRParcela->roRARRLancamento->roRARRCalculo->obRMONCredito->setCodCredito ( $rsCreditosGrupo->getCampo('cod_credito')  );
        $this->obRARRCarne->obRARRParcela->roRARRLancamento->roRARRCalculo->obRMONCredito->setCodEspecie ( $rsCreditosGrupo->getCampo('cod_especie')  );
        $this->obRARRCarne->obRARRParcela->roRARRLancamento->roRARRCalculo->obRMONCredito->setCodGenero  ( $rsCreditosGrupo->getCampo('cod_genero')   );
        $this->obRARRCarne->obRARRParcela->roRARRLancamento->roRARRCalculo->obRMONCredito->setCodNatureza( $rsCreditosGrupo->getCampo('cod_natureza') );

//echo "antes veja2: ".$this->obRARRCarne->obRARRParcela->roRARRLancamento->roRARRCalculo->obRARRGrupo->getExercicio()."<br>";
        $obErro = $this->obRARRCarne->obRARRParcela->roRARRLancamento->efetuarLancamentoManualGrupoCredito( $boTransacao );
    }

    return $obErro;
}

//efetua fechamento manual para todos os bancos e agencias
function efetuarFechamentoManualTodosBancos($boTransacao = "")
{
    $boFlagTransacao = false;
    //------------------------
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( $obErro->ocorreu() ) {
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTARRPagamento );

        return $obErro;
    }

    $obErro = $this->obTARRPagamentoLoteManual->recuperaRelacionamento( $rsListaPagamentos, "", " ORDER BY aplm.cod_banco, atp.pagamento", $boTransacao );
    if ( $obErro->ocorreu() ) {
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTARRPagamento );

        return $obErro;
    }

    if ( $rsListaPagamentos->eof() ) {
        $obErro->setDescricao("Não existem pagamentos baixados!");
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTARRPagamento );

        return $obErro;
    }

    $inCodBanco = "";
    $inCodAgencia = "";
    $stTipoParcela = "";
    $arDadosFechamento = array();
    $inTotalDados = 0;

    while ( !$rsListaPagamentos->eof() ) {
        if ( $inCodBanco != $rsListaPagamentos->getCampo("cod_banco") || $inCodAgencia != $rsListaPagamentos->getCampo("cod_agencia") || $stTipoParcela != $rsListaPagamentos->getCampo("pagamento") ) {
            $stTipoParcela = $rsListaPagamentos->getCampo("pagamento");
            $inCodAgencia = $rsListaPagamentos->getCampo("cod_agencia");
            $inCodBanco = $rsListaPagamentos->getCampo("cod_banco");
            $obErro = $this->obTARRLote->proximoCod( $this->inCodLote , $boTransacao );
            if ( $obErro->ocorreu() ) {
                $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTARRPagamento );

                return $obErro;
            }

            $this->obTARRLote->setDado( "cod_lote"     , $this->inCodLote );
            $this->obTARRLote->setDado( "exercicio"    , $this->stExercicio );
            $this->obTARRLote->setDado( "numcgm"       , Sessao::read( "numCgm" ) );
            $this->obTARRLote->setDado( "data_lote"    , $this->dtDataLote );
            $this->obTARRLote->setDado( "cod_banco"    , $inCodBanco );
            $this->obTARRLote->setDado( "cod_agencia"  , $inCodAgencia );
            $this->obTARRLote->setDado( "automatico"   , false );
            $obErro = $this->obTARRLote->inclusao($boTransacao);
            if ( $obErro->ocorreu() ) {
                $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTARRPagamento );

                return $obErro;
            }

            $arDadosFechamento[$inTotalDados]["exercicio"] = $this->stExercicio;
            $arDadosFechamento[$inTotalDados]["cod_lote"] = $this->inCodLote;

            $obRMONAgencia = new RMONAgencia;
            $obRMONAgencia->obRMONBanco->setCodBanco( $inCodBanco );
            $obRMONAgencia->setCodAgencia( $inCodAgencia );
            $obErro = $obRMONAgencia->consultarAgencia( $rsDadosAgencia, $boTransacao );
            if ( $obErro->ocorreu() ) {
                $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTARRPagamento );

                return $obErro;
            }

            $arDadosFechamento[$inTotalDados]["pagamento"] = $rsListaPagamentos->getCampo("pagamento");
            $arDadosFechamento[$inTotalDados]["cod_banco"] = $obRMONAgencia->obRMONBanco->getNumBanco();
            $arDadosFechamento[$inTotalDados]["nom_banco"] = $obRMONAgencia->obRMONBanco->getNomBanco();
            $arDadosFechamento[$inTotalDados]["cod_agencia"] = $obRMONAgencia->getNumAgencia();
            $arDadosFechamento[$inTotalDados]["nom_agencia"] = $obRMONAgencia->getNomAgencia();

            $inTotalDados++;
        }

        // incluir pagamentos_lotes
        $this->obTARRPagamentoLote->setDado( "cod_lote", $this->inCodLote );
        $this->obTARRPagamentoLote->setDado( "exercicio", $this->stExercicio );
        $this->obTARRPagamentoLote->setDado( "cod_convenio", $rsListaPagamentos->getCampo('cod_convenio') );
        $this->obTARRPagamentoLote->setDado( "numeracao", $rsListaPagamentos->getCampo('numeracao') );
        $this->obTARRPagamentoLote->setDado( "ocorrencia_pagamento", $rsListaPagamentos->getCampo('ocorrencia_pagamento') );
        $obErro = $this->obTARRPagamentoLote->inclusao($boTransacao);
        if ( $obErro->ocorreu() ) {
           $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTARRPagamento );

           return $obErro;
        }

        // excluir pagamentos lote manual
        $this->obTARRPagamentoLoteManual->setDado( "cod_agencia", $rsListaPagamentos->getCampo('cod_agencia') );
        $this->obTARRPagamentoLoteManual->setDado( "cod_banco", $rsListaPagamentos->getCampo('cod_banco') );
        $this->obTARRPagamentoLoteManual->setDado( "cod_convenio", $rsListaPagamentos->getCampo('cod_convenio') );
        $this->obTARRPagamentoLoteManual->setDado( "numeracao", $rsListaPagamentos->getCampo('numeracao') );
        $this->obTARRPagamentoLoteManual->setDado( "ocorrencia_pagamento", $rsListaPagamentos->getCampo('ocorrencia_pagamento') );
        $obErro = $this->obTARRPagamentoLoteManual->exclusao($boTransacao);
        if ( $obErro->ocorreu() ) {
           $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTARRPagamento );

           return $obErro;
        }

        $rsListaPagamentos->proximo();
    }

    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTARRPagamento );

    Sessao::write( "fechamento", $arDadosFechamento );

    return $obErro;
}

function efetuarFechamentoManual($boTransacao = "")
{
    $boFlagTransacao = false;
    $arDadosFechamento = array();
    $inTotalDados = 0;

    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( $obErro->ocorreu() ) {
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTARRPagamento );

        return $obErro;
    }

    $obErro = $this->listarPagamentosManuaisAFechar( $rsPagFechar, $boTransacao );
    $stTipoParcela = "";

    /* Buscar Numerações e chama funcao de itbi inicial*/
    $rsFechaItbi = $rsPagFechar;

    while ( !$rsFechaItbi->eof() ) {
        $stFiltroI .= "".$rsFechaItbi->getCampo('numeracao').",";
        $rsFechaItbi->proximo();
    }

    $this->buscaPorITBI($stFiltroI,$boTransacao,false);

    $obRMONAgencia = new RMONAgencia;
    $obRMONAgencia->obRMONBanco->setCodBanco( $this->obRMONBanco->getCodBanco() );
    $obRMONAgencia->setCodAgencia( $this->obRMONAgencia->getCodAgencia() );
    $obErro = $obRMONAgencia->consultarAgencia( $rsDadosAgencia, $boTransacao );
    if ( $obErro->ocorreu() ) {
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTARRPagamento );

        return $obErro;
    }

    while ( !$rsPagFechar->eof() ) {
        if ( $stTipoParcela != $rsPagFechar->getCampo("pagamento") ) {
            $stTipoParcela = $rsPagFechar->getCampo("pagamento");

            $obErro = $this->obTARRLote->proximoCod( $this->inCodLote , $boTransacao );
            if ( $obErro->ocorreu() ) {
                $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTARRPagamento );

                return $obErro;
            }

            $this->obTARRLote->setDado( "cod_lote"     , $this->inCodLote );
            $this->obTARRLote->setDado( "exercicio"    , $this->stExercicio );
            $this->obTARRLote->setDado( "numcgm"       , Sessao::read( "numCgm" ) );
            $this->obTARRLote->setDado( "data_lote"    , $this->dtDataLote );
            $this->obTARRLote->setDado( "cod_banco"    , $this->obRMONBanco->getCodBanco() );
            $this->obTARRLote->setDado( "cod_agencia"  , $this->obRMONAgencia->getCodAgencia() );
            $this->obTARRLote->setDado( "automatico"   , false );
            $obErro = $this->obTARRLote->inclusao($boTransacao);
            if ( $obErro->ocorreu() ) {
                $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTARRPagamento );

                return $obErro;
            }

            $arDadosFechamento[$inTotalDados]["exercicio"] = $this->stExercicio;
            $arDadosFechamento[$inTotalDados]["cod_lote"] = $this->inCodLote;
            $arDadosFechamento[$inTotalDados]["pagamento"] = $stTipoParcela;
            $arDadosFechamento[$inTotalDados]["cod_banco"] = $obRMONAgencia->obRMONBanco->getNumBanco();
            $arDadosFechamento[$inTotalDados]["nom_banco"] = $obRMONAgencia->obRMONBanco->getNomBanco();
            $arDadosFechamento[$inTotalDados]["cod_agencia"] = $obRMONAgencia->getNumAgencia();
            $arDadosFechamento[$inTotalDados]["nom_agencia"] = $obRMONAgencia->getNomAgencia();

            $inTotalDados++;
        }

        $this->obTARRPagamentoLote->setDado( "cod_lote", $this->inCodLote   );
        $this->obTARRPagamentoLote->setDado( "exercicio", $this->stExercicio );
        $this->obTARRPagamentoLote->setDado( "cod_convenio", $rsPagFechar->getCampo('cod_convenio')        );
        $this->obTARRPagamentoLote->setDado( "numeracao", $rsPagFechar->getCampo('numeracao')           );
        $this->obTARRPagamentoLote->setDado( "ocorrencia_pagamento", $rsPagFechar->getCampo('ocorrencia_pagamento'));

        $obErro = $this->obTARRPagamentoLote->inclusao($boTransacao);
        if ( $obErro->ocorreu() ) {
            $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTARRPagamento );

            return $obErro;
        }

        $rsPagFechar->proximo();
    }

    // excluir pagamentos lote manual
    $rsPagFechar->setPrimeiroElemento();
    while ( !$rsPagFechar->eof() ) {
        $this->obTARRPagamentoLoteManual->setDado( "cod_agencia", $rsPagFechar->getCampo('cod_agencia')   );
        $this->obTARRPagamentoLoteManual->setDado( "cod_banco", $rsPagFechar->getCampo('cod_banco')      );
        $this->obTARRPagamentoLoteManual->setDado( "cod_convenio", $rsPagFechar->getCampo('cod_convenio')        );
        $this->obTARRPagamentoLoteManual->setDado( "numeracao", $rsPagFechar->getCampo('numeracao')           );
        $this->obTARRPagamentoLoteManual->setDado( "ocorrencia_pagamento", $rsPagFechar->getCampo('ocorrencia_pagamento') );

        $obErro = $this->obTARRPagamentoLoteManual->exclusao($boTransacao);
        if ($obErro->ocorreu() )
            break;

        $rsPagFechar->proximo();
    }

    $obErro = $this->posBaixa($boTransacao);

    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTARRPagamento );
    Sessao::write( "fechamento", $arDadosFechamento );

    return $obErro;
}

/**
    * Salva a relacao de pagamento com lote
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function salvarPagamentoLote($boTransacao = "")
{
    $this->obTARRPagamentoLote->setDado( "cod_lote"             , $this->inCodLote );
    $this->obTARRPagamentoLote->setDado( "exercicio"            , $this->stExercicio );
    $this->obTARRPagamentoLote->setDado( "cod_convenio"         , $this->obRARRCarne->obRMONConvenio->getCodigoConvenio() );
    $this->obTARRPagamentoLote->setDado( "numeracao"            , $this->obRARRCarne->getNumeracao() );
    $this->obTARRPagamentoLote->setDado( "ocorrencia_pagamento" , $this->inOcorrenciaPagamento );
    $obErro = $this->obTARRPagamentoLote->inclusao($boTransacao);

    return $obErro;
}

/**
    * Salva as numerações inconsistentes do Lote
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function salvarInconsistencia($boTransacao = "")
{
    $obErro = $this->listarOcorrenciaInconsistencia( $rsInconsistencia, $boTransacao );
    if ( $rsInconsistencia->eof() ) {
        $inOcorrencia = 1;
    } else {
        $inOcorrencia = $rsInconsistencia->getCampo('ocorrencia') + 1;
    }
    if ( !$obErro->ocorreu() ) {
        $this->obTARRLoteInconsistencia->setDado( "cod_lote"       , $this->inCodLote );
        $this->obTARRLoteInconsistencia->setDado( "exercicio"      , $this->stExercicio );
        $this->obTARRLoteInconsistencia->setDado( "data_pagamento" , $this->getDataPagamento() );
        $this->obTARRLoteInconsistencia->setDado( "numeracao"      , $this->obRARRCarne->getNumeracao() );
        $this->obTARRLoteInconsistencia->setDado( "ocorrencia"     , $inOcorrencia );
        $this->obTARRLoteInconsistencia->setDado( "valor"          , $this->getValorPagamento() );
        $obErro = $this->obTARRLoteInconsistencia->inclusao($boTransacao);
    }

    return $obErro;
}

/**
    * Verifica o layout do arquivo de baixa
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function verificaLayout(&$stLayout , &$arDadosBaixa, $boTransacao = "")
{
    $obErro = new Erro;

    //verifica a primeira string da primeira linha para ver se o layout é FEBRABAN
    $arFileTmp = $this->getArquivo();
    $flTmp     = fopen( $arFileTmp['tmp_name'], "rb" );
    $stBuffer  = fread( $flTmp, filesize($arFileTmp['tmp_name']) );
    fclose( $flTmp );

    $arDadosBaixa = $arLinhas = preg_split( "/\r?\n|\r/" , $stBuffer );

    //se o primeiro elemento foi igual a A o arquivo esta no layout FEBRABAN
    $stPrimeiroElemento = substr( $arLinhas[0], 0, 1 );
    if ( strtoupper( $stPrimeiroElemento ) == "A" ) {

        $stLayout = "LayoutFebraban";
    } else {
        $stPrimeiroElemento = substr( $arLinhas[1], 0, 9 );
        if ( strtoupper( $stPrimeiroElemento ) == "00100011T" ) { //CNAB
            $stLayout = "LayoutCNAB";
        } else {
            $stPrimeiroElemento = substr( $arLinhas[0], 9, 10 );
            if (strtoupper( $stPrimeiroElemento ) == "DAF607 SIMPLESNACION" ) {
                $stLayout = "LayoutSIMPLESNACION";
            } else {
                $stPrimeiroElemento = substr( $arLinhas[1], 0, 9 );
                if ( strtoupper( $stPrimeiroElemento ) == "10400011T" ) { //CNAB Caixa
                    $stLayout = "LayoutCaixaCNAB";
                } else {
                    //implementar layout dos bancos -- NAO FEBRABAN
                }

            }
        }
    }

    return $obErro;
}
/**
    * Verifica no arquivo baixado se contem pagamento de ITBI
    * caso exista grava o imovel que foi pago para efetivar transferencia no fim da baixa
    * @return array{    imovel => 1
                        numeracao => 4321011001135
                        exercicio => 2006
                        numcgm => 26431
                        cod_transferencia => 2008
                        cod_natureza   }
*/
function buscaPorITBI($arDados , $boTransacao , $boAuto = true)
{
    if ($boAuto) {
        /* retirar primeira linha e as duas ultimas, que são lixo*/
        array_shift($arDados);array_pop($arDados); array_pop($arDados);

        foreach ($arDados AS $key => $stValorLinha) {
            //busca numeracao na string
            $stNumeracao = substr( $stValorLinha, 64, 17 );
            $stNumeracao = ltrim(trim( $stNumeracao ), "0");

            if ( $stNumeracao )
                $stFiltro .= "'".$stNumeracao."',";
        }
    } else {
        $stFiltro = "'".$arDados."',";
    }
    $this->obRARRConfiguracao = new RARRConfiguracao;
    $this->obRARRConfiguracao->setAnoExercicio( Sessao::getExercicio() );
    $this->obRARRConfiguracao->consultar( $boTransacao );
    $inCodGrupoITBI = $this->obRARRConfiguracao->getCodigoGrupoCreditoITBI();
    $arTMPdados = explode( "/", $inCodGrupoITBI );
    $inCodGrupoITBI = $arTMPdados[0];

    $stFiltro  = substr ( $stFiltro , 0 , strlen( $stFiltro )  - 1 );

    $stFiltro  = " and carne.numeracao in (".$stFiltro.") ";
    $stFiltro .= " and calculo_grupo_credito.cod_grupo = ".$inCodGrupoITBI;

    if ($inCodGrupoITBI == "") {
        $obErro = new Erro;
        $obErro->setDescricao("Grupo ITBI não encontrado.");

        return $obErro;
    }

    require_once ( CAM_GT_CIM_NEGOCIO . "RCIMTransferencia.class.php" );
    $obRCIMTransferencia = new RCIMTransferencia;
    $this->obTARRPagamento->recuperaListaITBIArquivo( $rsImoveisArquivo, $stFiltro, '', $boTransacao );
    $arItbi  = array();
    while ( !$rsImoveisArquivo->eof() ) {
        $obRCIMTransferencia->setEfetivacao ('t');
        $obRCIMTransferencia->setInscricaoMunicipal ( $rsImoveisArquivo->getCampo('inscricao_municipal') );
        $obRCIMTransferencia->setCodigoTransferencia ( $rsImoveisArquivo->getCampo('cod_transferencia') );
        $obRCIMTransferencia->setCodigoNatureza ( $rsImoveisArquivo->getCampo('cod_natureza') );
        $obErro = $obRCIMTransferencia->listarTransferencia( $rsTransferencia, $boTransacao ) ;
        $arItbi[] = array(  "imovel"            => $rsImoveisArquivo->getCampo('inscricao_municipal'),
                            "numeracao"         => $rsImoveisArquivo->getCampo('numeracao'),
                            "exercicio"         => $rsImoveisArquivo->getCampo('exercicio'),
                            "numcgm"            => $rsImoveisArquivo->getCampo('numcgm'),
                            "cod_transferencia" => $rsImoveisArquivo->getCampo('cod_transferencia'),
                            "cod_natureza"      => $rsImoveisArquivo->getCampo('cod_natureza'),
                            "fazer"             => !$rsTransferencia->Eof()
                         );

        $rsImoveisArquivo->proximo();
    }

    $this->arITBI = $arItbi;
    unset($arItbi,$rsImoveisArquivo,$arDados);

}
/* Executa funções de pós baixa */
function posBaixa($boTransacao, $boBaixaAutomatica = false)
{
    $obErro = new Erro;

    /* ITBI PAGO >>> */
    Sessao::write( 'logItbiPago', array() );
    $arItbi = $this->arITBI ;

    $inCount = count($arItbi);
    if ($inCount > 0) {
        /* verificar se algum itbi foi baixado nesta transação */
        $arNums = array();
        $arTEMP = array();

        foreach ( $arItbi as $valor ) :
            if ($valor['fazer']) {

                $stFiltro .= "'".$valor['numeracao']."',";
                $arNums[] = $valor['numeracao'];
                $arTEMP[] = $valor;
            }
        endforeach;

        if ( count( $arNums ) > 0 ) {
            $arItbi = $arTEMP;
            /* busca por numerações na tabela de pagamento */
            $stFiltro  = substr ( $stFiltro , 0 , strlen( $stFiltro )  - 1 );
            $stFiltro  = " and pagamento.numeracao in (".$stFiltro.") ";

            $this->obTARRPagamento->recuperaPagosITBI($rsPagosItbi,$stFiltro,'',$boTransacao);
        } else {
            $rsPagosItbi = new RecordSet;
        }

        /* efetivar transferencia */
        require_once ( CAM_GT_CIM_NEGOCIO . "RCIMTransferencia.class.php" );
        $obRCIMTransferencia = new RCIMTransferencia;
        while ( !$rsPagosItbi->eof() ) :
            /* verifica numeracao no array de pagos */
            if ( in_array( $rsPagosItbi->getCampo('numeracao'), $arNums ) ) {
                $inPos = array_search($rsPagosItbi->getCampo('numeracao'),$arNums);
                if ( $rsPagosItbi->getCampo('pagamento') == 't') { //'f' ) {
                    /* busca posição no array */
                    $boOk = false;
                    /* validar documentos */
                    $obRCIMTransferencia->setCodigoTransferencia( $arItbi[ $inPos ] ['cod_transferencia']    );
                    $obRCIMTransferencia->setCodigoNatureza     ( $arItbi[ $inPos ] ['cod_natureza']         );
                    $obRCIMTransferencia->validarDocumentosEfetivacao( $boOk, $boTransacao );
                    $obRCIMTransferencia->consultarDocumentos ( $boTransacao );
                    if ($boOk) {
                        $status = 'Transf. Efetivada';
                        $obRCIMTransferencia->inInscricaoMunicipal  = $arItbi[ $inPos ][ 'imovel' ] ;
                        $obRCIMTransferencia->setEfetivacao( 'f' );
                        $obRCIMTransferencia->listarTransferencia( $rsListaTransferencia, $boTransacao );
                        $obRCIMTransferencia->setEfetivacao( 't' );
                        if ( $rsListaTransferencia->Eof() ) {
                            /* Efetivar transferencia */
                            $obRCIMTransferencia->setMatriculaRegImov    ( $arItbi[ $inPos ] [ 'mat_registro_imovel' ] );
                            $obRCIMTransferencia->setDataEfetivacao      ( date('d/m/Y')                            );
                            $obRCIMTransferencia->setObservacao          ( 'Efetivação Automatica pela baixa do carne: '.$arItbi[$inPos]['numeracao']);
                            $obErro = $obRCIMTransferencia->efetivarTransferencia  ( $boTransacao, true );
                            if ( $obErro->getDescricao() == 'Nenhuma transferência foi encontrada!' ) {
                                $status = 'Transf. Não Efetivada';
                                $obErro->setDescricao('');
                            }
                        }

                        $arLogItbiPagoSessao = Sessao::read( "logItbiPago" );
                        $arLogItbiPagoSessao[] = array(  'numeracao'  => $arItbi[ $inPos ] [ 'numeracao']
                                                                    , 'imovel'     => $arItbi[ $inPos ] [ 'imovel' ]
                                                                    , 'status'     => $status  );
                        Sessao::write( "logItbiPago", $arLogItbiPagoSessao );
                    } else {
                        $arLogItbiPagoSessao = Sessao::read( "logItbiPago" );
                        $arLogItbiPagoSessao[] = array(  'numeracao'  => $arItbi[ $inPos ] [ 'numeracao']
                                                                , 'imovel'     => $arItbi[ $inPos ] [ 'imovel' ]
                                                                , 'status'     => 'Erro'  );

                        Sessao::write( "logItbiPago", $arLogItbiPagoSessao );
                    }
                }else
                if (!$boBaixaAutomatica) {
                    /*** cancelar transferencias de carnes cancelados! */
                    $obRCIMTransferencia->setInscricaoMunicipal  ( $arItbi[ $inPos ] [ 'imovel' ] );
                    $obRCIMTransferencia->setEfetivacao( 't' );
                    $obRCIMTransferencia->listarTransferencia( $rsListaTransferencia, $boTransacao );
                    if ( !$rsListaTransferencia->Eof() ) {
                        // busca posição no array
                        $inPos = array_search($rsPagosItbi->getCampo('numeracao'),$arNums);
                        // seta var's
                        $obRCIMTransferencia->setCodigoTransferencia( $arItbi[ $inPos ] ['cod_transferencia']    );
                        $obRCIMTransferencia->setDataCancelamento   ( date('d/m/Y') );
                        $obRCIMTransferencia->setMotivo             ( 'Cancelamento Automatico na baixa do carne: '.$arItbi[ $inPos ][ 'numeracao' ] );
                        $obErro = $obRCIMTransferencia->cancelarTransferencia ( $boTransacao, false );
                    }

                    $arLogItbiPagoSessao = Sessao::read( "logItbiPago" );
                    $arLogItbiPagoSessao[] = array(  'numeracao'  => $arItbi[ $inPos ] [ 'numeracao']
                                                            , 'imovel'     => $arItbi[ $inPos ] [ 'imovel' ]
                                                            , 'status'     => 'Transf. Cancelada' );

                    Sessao::write( "logItbiPago", $arLogItbiPagoSessao );
                }
            }
            $rsPagosItbi->proximo();
        endwhile;

        /* limpar memoria */
        unset($obRCIMTransferencia,$arItbi);
    }

    /* ITBI PAGO <<< */

    return $obErro;
}

function listarCarne(&$rsRecordSet, $boTransacao = '')
{
    $stFiltro = '';
    if ($this->stNumeracao) {
    }
    $obErro = $this->obTARRCarne->recuperaTodos( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

function listarOcorrenciaInconsistencia(&$rsRecordSet, $boTransacao = '')
{
    $stFiltro = '';
    if ($this->inCodLote) {
        $stFiltro .= " cod_lote = ".$this->inCodLote." and";
    }
    if ($this->stExercicio) {
        $stFiltro .= " exercicio = '".$this->stExercicio."' and";
    }
    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    }
    $stOrdem = " ocorrencia desc limit 1";
    $obErro = $this->obTARRLoteInconsistencia->recuperaTodos( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

function listarPagamentoCreditosConsulta(&$rsRecordSet , $boTransacao = "")
{
    $stFiltro = "";
    if ( $this->obRARRCarne->getNumeracao() ) {
        $stFiltro .= " and apag.numeracao= '".$this->obRARRCarne->getNumeracao()."'";
    }
    if ($this->obRARRCarne->getExercicio) {
        $stFiltro .= " and apag.exercicio= '".$this->obRARRCarne->getExercicio()."'";
    }
    $stOrdem = " ORDER BY ac.cod_credito";
    $obErro = $this->obTARRPagamento->recuperaPagamentoPorCreditoConsulta( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

function consultaResumoLote(&$rsRecordSet , $boTransacao = "")
{
    $stFiltro = "";
    if ($this->inCodLote) {
        $stFiltro .= " and l.cod_lote= '".$this->inCodLote."'";
    }
    if ($this->stExercicio) {
        $stFiltro .= " and l.exercicio= '".$this->stExercicio."'";
    }

    $stOrdem = "";

    $obErro = $this->obTARRPagamento->recuperaResumoLote( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

   return $obErro;
}

function consultaResumoLoteBaixaManual(&$rsRecordSet , $boTransacao = "")
{
    $stFiltro = "";
    if ($this->inCodLote) {
        $stFiltro .= " l.cod_lote= '".$this->inCodLote."' and";
    }
    if ($this->stExercicio) {
        $stFiltro .= " l.exercicio= '".$this->stExercicio."' and";
    }
    $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    $stOrdem = "";

    $obErro = $this->obTARRPagamento->recuperaResumoLoteBaixaManual( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

function listaResumoLote(&$rsRecordSet , $boTransacao = "", $divida)
{
    $stFiltroJOIN = "";

    if ($divida == 2) {
    }else
    if ($divida == 1) {
        $stFiltroJOIN = "
            INNER JOIN arrecadacao.carne
                ON carne.numeracao = plote.numeracao

            INNER JOIN arrecadacao.parcela
                ON arrecadacao.parcela.cod_parcela = carne.cod_parcela

            INNER JOIN divida.parcela_calculo
                ON parcela_calculo.cod_calculo = pagc.cod_calculo

            INNER JOIN divida.parcela
                ON divida.parcela.num_parcelamento = parcela_calculo.num_parcelamento
                AND divida.parcela.num_parcela = parcela_calculo.num_parcela
                AND divida.parcela.num_parcela = arrecadacao.parcela.nr_parcela
        ";
    } else {
        $stFiltroJOIN = "
            INNER JOIN arrecadacao.carne
                ON carne.numeracao = plote.numeracao

            INNER JOIN arrecadacao.parcela
                ON arrecadacao.parcela.cod_parcela = carne.cod_parcela

            LEFT JOIN divida.parcela_calculo
                ON parcela_calculo.cod_calculo = pagc.cod_calculo

            LEFT JOIN divida.parcela
                ON divida.parcela.num_parcelamento = parcela_calculo.num_parcelamento
                AND divida.parcela.num_parcela = parcela_calculo.num_parcela
                AND divida.parcela.num_parcela = arrecadacao.parcela.nr_parcela
        ";
    }

    $stFiltro = "";
    if ($divida == 0) {
        $stFiltro .= " (divida.parcela_calculo.cod_calculo IS NULL) AND ";
    }

    if ($this->inCodLote) {
        $stFiltro .= " plote.cod_lote= ".$this->inCodLote." and";
    }
    if ($this->stExercicio) {
        $stFiltro .= " plote.exercicio = '".$this->stExercicio."' and";
    }
    if ( $this->obRARRCarne->obRARRParcela->roRARRLancamento->roRARRCalculo->obRARRGrupo->getExercicio() ) {
        $stFiltro .= " c.exercicio = ".$this->obRARRCarne->obRARRParcela->roRARRLancamento->roRARRCalculo->obRARRGrupo->getExercicio()." and";
    }

    if ( $this->obRARRCarne->obRARRParcela->roRARRLancamento->roRARRCalculo->obRARRGrupo->getCodGrupo() ) {
        $stFiltro .= " acgc.cod_grupo = ";
        $stFiltro .= $this->obRARRCarne->obRARRParcela->roRARRLancamento->roRARRCalculo->obRARRGrupo->getCodGrupo();
        $stFiltro .= " and acgc.cod_grupo is not null and";

        $stFiltroPL = "grupo";

    } elseif ($this->obRARRCarne->obRARRParcela->roRARRLancamento->roRARRCalculo->obRMONCredito->getCodCredito()) {
        $stFiltro .= " c.cod_credito = ";
        $stFiltro .= $this->obRARRCarne->obRARRParcela->roRARRLancamento->roRARRCalculo->obRMONCredito->getCodCredito();
        $stFiltro .= " and c.cod_especie = ";
        $stFiltro .= $this->obRARRCarne->obRARRParcela->roRARRLancamento->roRARRCalculo->obRMONCredito->getCodEspecie();
        $stFiltro .= " and c.cod_genero = ";
        $stFiltro .= $this->obRARRCarne->obRARRParcela->roRARRLancamento->roRARRCalculo->obRMONCredito->getCodGenero();
        $stFiltro .= " and c.cod_natureza = ";
        $stFiltro .= $this->obRARRCarne->obRARRParcela->roRARRLancamento->roRARRCalculo->obRMONCredito->getCodNatureza();
        $stFiltro .= " and c.exercicio = ";
        $stFiltro .= $this->obRARRCarne->obRARRParcela->roRARRLancamento->roRARRCalculo->getExercicio();
        $stFiltro .= " and acgc.cod_grupo is null and";

        $stFiltroPL = "credito";

    }

    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    }

    $obErro = $this->obTARRPagamento->recuperaResumoLoteLista($rsRecordSet,$stFiltro, $stFiltroPL, $stOrdem, $boTransacao, $stFiltroJOIN );

    return $obErro;

}

function listaResumoLoteInconsistenteAgrupado(&$rsRecordSet , $boTransacao = "")
{
    $stFiltro = "";
    if ($this->inCodLote) {
        $stFiltro .= " ali.cod_lote= ".$this->inCodLote." and";
    }
    if ($this->stExercicio) {
        $stFiltro .= " ali.exercicio = '".$this->stExercicio."' and";
    }

    if ( $this->obRARRCarne->obRMONConvenio->getCodigoConvenio() ) {
        $stFiltro .= " carne.cod_convenio = ".$this->obRARRCarne->obRMONConvenio->getCodigoConvenio()." and";
    }

    if ( $this->obRARRCarne->obRARRParcela->roRARRLancamento->roRARRCalculo->obRARRGrupo->getExercicio() ) {
        $stFiltro .= " c.exercicio = ".$this->obRARRCarne->obRARRParcela->roRARRLancamento->roRARRCalculo->obRARRGrupo->getExercicio()." and";
    }

    if ( $this->obRARRCarne->obRARRParcela->roRARRLancamento->roRARRCalculo->obRARRGrupo->getCodGrupo() ) {
        $stFiltro .= " acgc.cod_grupo = ";
        $stFiltro .= $this->obRARRCarne->obRARRParcela->roRARRLancamento->roRARRCalculo->obRARRGrupo->getCodGrupo();
        $stFiltro .= " and acgc.cod_grupo is not null and";

        $stFiltroPL = "grupo";

    } elseif ($this->obRARRCarne->obRARRParcela->roRARRLancamento->roRARRCalculo->obRMONCredito->getCodCredito()) {
        $stFiltro .= " c.cod_credito = ";
        $stFiltro .= $this->obRARRCarne->obRARRParcela->roRARRLancamento->roRARRCalculo->obRMONCredito->getCodCredito();
        $stFiltro .= " and c.cod_especie = ";
        $stFiltro .= $this->obRARRCarne->obRARRParcela->roRARRLancamento->roRARRCalculo->obRMONCredito->getCodEspecie();
        $stFiltro .= " and c.cod_genero = ";
        $stFiltro .= $this->obRARRCarne->obRARRParcela->roRARRLancamento->roRARRCalculo->obRMONCredito->getCodGenero();
        $stFiltro .= " and c.cod_natureza = ";
        $stFiltro .= $this->obRARRCarne->obRARRParcela->roRARRLancamento->roRARRCalculo->obRMONCredito->getCodNatureza();
        $stFiltro .= " and c.exercicio = '";
        $stFiltro .= $this->obRARRCarne->obRARRParcela->roRARRLancamento->roRARRCalculo->getExercicio()."'";
        $stFiltro .= " and acgc.cod_grupo is null and";

        $stFiltroPL = "credito";

    }

    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    }

    $obErro = $this->obTARRPagamento->recuperaResumoLoteListaInconsistenteAgrupado ($rsRecordSet,$stFiltro, $stFiltroPL, $stOrdem, $boTransacao);

    return $obErro;

}


function listaResumoLoteOrigem(&$rsRecordSet , $boTransacao = "")
{
    $stFiltro = "";
    if ($this->inCodLote) {
        $stFiltro .= " lote.cod_lote= ".$this->inCodLote." and";
    }
    if ($this->stExercicio) {
        $stFiltro .= " lote.exercicio= '".$this->stExercicio."' and";
    }

    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    }

    $obErro = $this->obTARRPagamento->recuperaResumoLoteListaOrigem( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

function listaResumoLoteInconsistente(&$rsRecordSet , $boTransacao = "")
{
    $stFiltro = "";
    if ($this->inCodLote) {
        $stFiltro .= " li.cod_lote= '".$this->inCodLote."' and ";
    }
    if ($this->stExercicio) {
        $stFiltro .= " li.exercicio= '".$this->stExercicio."' and";
    }

    $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );

    $stOrdem = "";

    $obErro = $this->obTARRPagamento->recuperaResumoLoteListaInconsistente( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

function listaResumoLoteInconsistenteSemVinculo(&$rsRecordSet , $boTransacao = "")
{
    $stFiltro = "";
    if ($this->inCodLote) {
        $stFiltro .= " li.cod_lote= '".$this->inCodLote."' and ";
    }
    if ($this->stExercicio) {
        $stFiltro .= " li.exercicio= '".$this->stExercicio."' and";
    }

    $stFiltro .=" NOT EXISTS ( select numeracao FROM arrecadacao.carne WHERE numeracao = li.numeracao ) AND";

    $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );

    $stOrdem = "";

    $obErro = $this->obTARRPagamento->recuperaResumoLoteListaInconsistente( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

function listaResumoLoteDiff(&$rsRecordSet , $boTransacao = "")
{
    $stFiltro = "";
    if ($this->inCodLote) {
        $stFiltro .= "".$this->inCodLote;
    }
    if ($this->stExercicio) {
        $stFiltro .= ",".$this->stExercicio."";
    }

    $stOrdem = "";

    $obErro = $this->obTARRPagamento->recuperaResumoLoteListaDiff( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

   return $obErro;
}

function listaVerificaMd5(&$rsRecordSet , $boTransacao = "")
{
    $stFiltro = "";

    if ($this->stMd5Sum) {
        $stFiltro .= " upper( md5sum ) = upper('".$this->stMd5Sum."') and ";
    }

    $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );

    $stOrdem = "";

    $obErro = $this->obTARRLoteArquivo->recuperaVerificaArquivoMd5($rsRecordSet, $stFiltro, $stOrdem, $boTransacao);

    return $obErro;

}

function listaVerificaConteudo(&$rsRecordSet , $boTransacao = "")
{
    $stFiltro = "";
    if ($this->stHeader) {
        $stFiltro .= " trim(header) = '".$this->stHeader."' and ";
    }
    if ($this->stFooter) {
        $stFiltro .= " trim(footer) = '".$this->stFooter."' and ";
    }
    if ($this->stNomArquivo) {
        $stFiltro .= " trim(nom_arquivo) = '".$this->stNomArquivo."' and ";
    }
    $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );

    $stOrdem = "";

    $obErro = $this->obTARRLoteArquivo->recuperaVerificaArquivoConteudo( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

   return $obErro;
}

function listarPagamentosManuaisAFechar(&$rsRecordSet, $boTransacao = '')
{
    $stFiltro = '';
    if ( $this->obRMONAgencia->getCodAgencia() ) {
        $stFiltro .= " aplm.cod_agencia = ".$this->obRMONAgencia->getCodAgencia()." and";
    }
    if ( $this->obRMONBanco->getCodBanco() ) {
        $stFiltro .= " aplm.cod_banco = ".$this->obRMONBanco->getCodBanco()." and";
    }
    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    }
    $stOrdem = " ORDER BY atp.pagamento ";
    $obErro = $this->obTARRPagamentoLoteManual->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

function listarEstornoBaixaManual(&$rsRecordSet, $boTransacao = '')
{
    $stFiltro = '';
    if ( $this->getCodLote() ) {
        $stFiltro .= " aplm.cod_lote = ".$this->getCodLote()." AND";
    }

    if ( $this->getExercicio() ) {
        $stFiltro .= " alot.exercicio = '".$this->getExercicio()."' AND";
    }

    if ( $this->obRARRCarne->getNumeracao() ) {
        $stFiltro .= " aplm.numeracao = '".$this->obRARRCarne->getNumeracao()."' AND";
    }

    if ( $this->obRMONAgencia->getNumAgencia() ) {
        $stFiltro .= " ma.num_agencia = ".$this->obRMONAgencia->getNumAgencia()." AND";
    }

    if ( $this->obRMONBanco->getNumBanco() ) {
        $stFiltro .= " mb.num_banco = ".$this->obRMONBanco->getNumBanco()." AND";
    }

    if ( $this->obRMONAgencia->obRCGM->getNumCGM() ) {
        $stFiltro .= " apa.numcgm = ".$this->obRMONAgencia->obRCGM->getNumCGM()." AND";
    }

    if ( $this->obRARRCarne->obRARRParcela->roRARRLancamento->obRCIMImovel->getNumeroInscricao() ) {
        $stFiltro .= " aic.inscricao_municipal = ".$this->obRARRCarne->obRARRParcela->roRARRLancamento->obRCIMImovel->getNumeroInscricao()." AND";
    }

    if ( $this->obRARRCarne->obRARRParcela->roRARRLancamento->obRCEMInscricaoEconomica->getInscricaoEconomica() ) {
        $stFiltro .= " aec.inscricao_economica = ".$this->obRARRCarne->obRARRParcela->roRARRLancamento->obRCEMInscricaoEconomica->getInscricaoEconomica()." AND";
    }

    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    }
    $obErro =$this->obTARRPagamentoLoteManual->recuperaListaEstornoBaixaManual( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

function efetuarEstornoBaixaManual($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ($obErro->ocorreu() ) {
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTARRPagamento );

        return $obErro;
    }

    //verifica se contém outras parcelas para estornar
    $obRARRPagamento2 = new RARRPagamento();
    $obRARRPagamento2->obRARRCarne->obRARRParcela->roRARRLancamento->obRCIMImovel->setNumeroInscricao( $_REQUEST['inInscricao'] );
    $obRARRPagamento2->setExercicio( Sessao::read('exercicio') );
    $obRARRPagamento2->listarEstornoBaixaManual ($rsListaEstornoCotaUnica);

    //excluindo no pagamento acrescimo
    $this->obTARRPagamentoAcrescimo->setDado( "numeracao", $this->obRARRCarne->getNumeracao() );
    $this->obTARRPagamentoAcrescimo->setDado( "ocorrencia_pagamento", $this->getOcorrenciaPagamento() );
    $this->obTARRPagamentoAcrescimo->setDado( "cod_convenio", $this->obRARRCarne->obRMONConvenio->getCodigoConvenio() );

    $obErro = $this->obTARRPagamentoAcrescimo->exclusao( $boTransacao );

    if ($obErro->ocorreu() ) {
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTARRPagamento );

        return $obErro;
    }

    //excluindo no pagamento calculo
    $this->obTARRPagamentoCalculo->setDado( "cod_convenio", $this->obRARRCarne->obRMONConvenio->getCodigoConvenio() );
    $this->obTARRPagamentoCalculo->setDado( "ocorrencia_pagamento", $this->getOcorrenciaPagamento());
    $this->obTARRPagamentoCalculo->setDado( "numeracao", $this->obRARRCarne->getNumeracao() );

    $obErro = $this->obTARRPagamentoCalculo->exclusao( $boTransacao );
    if ($obErro->ocorreu() ) {
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTARRPagamento );

        return $obErro;
    }

    //excluindo no processo pagamento
    $this->obTARRProcessoPagamento->setDado( "numeracao", $this->obRARRCarne->getNumeracao() );
    $this->obTARRProcessoPagamento->setDado( "ocorrencia_pagamento", $this->getOcorrenciaPagamento() );
    $this->obTARRProcessoPagamento->setDado( "cod_convenio", $this->obRARRCarne->obRMONConvenio->getCodigoConvenio() );

    $obErro = $this->obTARRProcessoPagamento->exclusao( $boTransacao );
    
    if ($obErro->ocorreu() ) {
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTARRPagamento );

        return $obErro;
    }

    //excluindo no pagamento lote
    $this->obTARRPagamentoLote->setDado( "cod_convenio", $this->obRARRCarne->obRMONConvenio->getCodigoConvenio());
    $this->obTARRPagamentoLote->setDado( "ocorrencia_pagamento", $this->getOcorrenciaPagamento());
    $this->obTARRPagamentoLote->setDado( "numeracao", $this->obRARRCarne->getNumeracao() );
    $this->obTARRPagamentoLote->setDado( "cod_lote", $this->getCodLote() );
    $this->obTARRPagamentoLote->setDado( "exercicio", $this->getExercicio() );
    $obErro = $this->obTARRPagamentoLote->exclusao( $boTransacao );
    if ($obErro->ocorreu() ) {
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTARRPagamento );

        return $obErro;
    }

    //excluindo no pagamento diferenca
    $this->obTARRPagamentoDiferenca->setDado( "numeracao", $this->obRARRCarne->getNumeracao());
    $this->obTARRPagamentoDiferenca->setDado( "cod_convenio", $this->obRARRCarne->obRMONConvenio->getCodigoConvenio());
    $this->obTARRPagamentoDiferenca->setDado( "ocorrencia_pagamento", $this->getOcorrenciaPagamento());

    $obErro = $this->obTARRPagamentoDiferenca->exclusao( $boTransacao );
    if ($obErro->ocorreu() ) {
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTARRPagamento );

        return $obErro;
    }

    //excluindo no observacao pagamento
    include_once ( CAM_GT_ARR_MAPEAMENTO."TARRObservacaoPagamento.class.php" );

    $obTARRObservacaoPagamento = new TARRObservacaoPagamento;
    $obTARRObservacaoPagamento->setDado( "numeracao", $this->obRARRCarne->getNumeracao() );
    $obTARRObservacaoPagamento->setDado( "ocorrencia_pagamento", $this->getOcorrenciaPagamento() );
    $obTARRObservacaoPagamento->setDado( "cod_convenio", $this->obRARRCarne->obRMONConvenio->getCodigoConvenio() );

    $obErro = $obTARRObservacaoPagamento->exclusao( $boTransacao );
    if ($obErro->ocorreu() ) {
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTARRPagamento );

        return $obErro;
    }

    if ( $this->obRARRCarne->obRMONConvenio->getCodigoConvenio() == -1 ) {
        $obTDATDividaParcela = new TDATDividaParcela;
        $obTDATDividaParcela->recuperaListaParcelasDoEstornoLote( $rsDadosDivida, $this->obRARRCarne->getNumeracao(), $boTransacao );
        while ( !$rsDadosDivida->Eof() ) {
            $obTDATDividaParcela->setDado( "num_parcelamento", $rsDadosDivida->getCampo( "num_parcelamento" ) );
            $obTDATDividaParcela->setDado( "num_parcela", $rsDadosDivida->getCampo( "num_parcela" ) );
            $obTDATDividaParcela->setDado( "paga", false );
            $obErro = $obTDATDividaParcela->alteracao( $boTransacao );
            $rsDadosDivida->proximo();

            if ($obErro->ocorreu() ) {
                $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTARRPagamento );

                return $obErro;
            }
        }
    }

    //excluindo no pagamento
    $this->obTARRPagamento->setDado( "numeracao", $this->obRARRCarne->getNumeracao());
    $this->obTARRPagamento->setDado( "ocorrencia_pagamento", $this->getOcorrenciaPagamento());
    $this->obTARRPagamento->setDado( "cod_convenio", $this->obRARRCarne->obRMONConvenio->getCodigoConvenio());

    $obErro = $this->obTARRPagamento->exclusao( $boTransacao );
    if ($obErro->ocorreu() ) {
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTARRPagamento );

        return $obErro;
    }

    if ($rsListaEstornoCotaUnica->getNumLinhas() < 2) {
        $this->obRARRCarne->listarCarneDevolucao( $rsListaCarnes, $boTransacao );
        while ( !$rsListaCarnes->Eof() ) { // estornar o cancelamento de uma cota única,
            $this->obTARRCarneDevolucao->setDado( "numeracao", $rsListaCarnes->getCampo("numeracao") );
            $this->obTARRCarneDevolucao->setDado( "cod_convenio", $rsListaCarnes->getCampo("cod_convenio") );
            $obErro = $this->obTARRCarneDevolucao->exclusao( $boTransacao );
            if ( $obErro->ocorreu() ) {
                $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTARRPagamento );

                return $obErro;
            }
            $rsListaCarnes->proximo();
        }
    }

    $this->obRARRConfiguracao = new RARRConfiguracao;
    $this->obRARRConfiguracao->setAnoExercicio( Sessao::getExercicio() );
    $this->obRARRConfiguracao->consultar( $boTransacao );
    $inCodGrupoITBI = $this->obRARRConfiguracao->getCodigoGrupoCreditoITBI();
    $arTMPdados = explode( "/", $inCodGrupoITBI );
    $inCodGrupoITBI = $arTMPdados[0];

    $stFiltro  = " and carne.numeracao = '".$this->obRARRCarne->getNumeracao()."'";
    $stFiltro .= " and calculo_grupo_credito.cod_grupo = ".$inCodGrupoITBI;

    if ($inCodGrupoITBI) {
        $this->obTARRPagamento->recuperaListaITBIArquivo( $rsImoveisArquivo, $stFiltro, '', $boTransacao );
        if ( !$rsImoveisArquivo->Eof() ) {
            $obTCIMTransferenciaEfetivacao = new TCIMTransferenciaEfetivacao;
            $obTCIMTransferenciaEfetivacao->setDado( "cod_transferencia", $rsImoveisArquivo->getCampo( "cod_transferencia" ) );
            $obTCIMTransferenciaEfetivacao->exclusao( $boTransacao );
        }
    }

    $this->obRARRCarne->listarPagamentosCancelados ( $rsListaCarnesCancelados, $boTransacao );
    while ( !$rsListaCarnesCancelados->eof() ) {
        $this->obTARRPagamento->setDado( "numeracao", $rsListaCarnesCancelados->getCampo("numeracao") );
        $this->obTARRPagamento->setDado( "cod_convenio", $rsListaCarnesCancelados->getCampo("cod_convenio") );
        $this->obTARRPagamento->setDado( "ocorrencia_pagamento", $rsListaCarnesCancelados->getCampo("ocorrencia_pagamento") );
        if ( $rsListaCarnesCancelados->getCampo("cod_processo") ) {
            $this->obTARRProcessoPagamento->setDado( "cod_convenio", $rsListaCarnesCancelados->getCampo("cod_convenio") );
            $this->obTARRProcessoPagamento->setDado( "numeracao", $rsListaCarnesCancelados->getCampo("numeracao") );
            $this->obTARRProcessoPagamento->setDado( "ocorrencia_pagamento", $rsListaCarnesCancelados->getCampo("ocorrencia_pagamento") );
            $this->obTARRProcessoPagamento->exclusao( $boTransacao );
        }
        $obErro = $this->obTARRPagamento->exclusao( $boTransacao );
        $rsListaCarnesCancelados->proximo();
    }

    $this->obTARRLote->setDado( "cod_lote", $this->getCodLote() );
    $this->obTARRLote->setDado( "exercicio", $this->getExercicio() );
    $obErro = $this->obTARRLote->exclusao( $boTransacao );

    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTARRPagamento );

    return $obErro;
}

function listarPagamentosManuais(&$rsRecordSet, $boTransacao = '')
{
    $stFiltro = '';
    if ( $this->obRMONAgencia->getCodAgencia() ) {
        $stFiltro .= " aplm.cod_agencia = ".$this->obRMONAgencia->getCodAgencia()." and";
    }
    if ( $this->obRMONBanco->getCodBanco() ) {
        $stFiltro .= " aplm.cod_banco = ".$this->obRMONBanco->getCodBanco()." and";
    }
    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    }
    $stOrdem = "";
    $obErro = $this->obTARRPagamentoLoteManual->recuperaListaPagamentosManuaisAFechar( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

function listarConsultaLote(&$rsRecordSet, $boTransacao = '')
{
    $stFiltro = '';
    if ( $this->obRMONAgencia->getCodAgencia() ) {
        $stFiltro .= " mag.cod_agencia = ".$this->obRMONAgencia->getCodAgencia()." and";
    }
    if ( $this->obRMONAgencia->getNumAgencia() ) {
        $stFiltro .= " mag.num_agencia = '".$this->obRMONAgencia->getNumAgencia()."' and";
    }
    if ( $this->obRMONAgencia->obRMONBanco->getCodBanco() ) {
        $stFiltro .= " mb.cod_banco = ".$this->obRMONAgencia->obRMONBanco->getCodBanco()." and";
    }
    if ( $this->obRMONAgencia->obRMONBanco->getNumBanco() ) {
        $stFiltro .= " mb.num_banco = '".$this->obRMONAgencia->obRMONBanco->getNumBanco()."' and";
    }
    if ( $this->obRMONAgencia->obRCGM->getNumCGM() ) {
        $stFiltroCGM = " AND accgm.numcgm = ".$this->obRMONAgencia->obRCGM->getNumCGM()." ";
    }
    if ( $this->getExercicio() ) {
        $stFiltro .= " lote.exercicio = '".$this->getExercicio()."' and";
    }
    if ( $this->getDataLote() && $this->getDataLoteFinal() ) {
        $stFiltro .= " lote.data_lote between '".$this->getDataLote()."' and '".$this->getDataLoteFinal()."' AND";
    } elseif ( $this->getDataLote() && !$this->getDataLoteFinal() ) {
        $stFiltro .= " lote.data_lote = '".$this->getDataLote()."' and ";
    } elseif ( !$this->getDataLote() && $this->getDataLoteFinal() ) {
        $stFiltro .= " lote.data_lote = '".$this->getDataLoteFinal()."' and ";
    }
    if ( $this->getCodLote() && !$this->getCodLoteFinal() ) {
        $stFiltro .= " lote.cod_lote = ".$this->getCodLote()." and ";
    } elseif ( !$this->getCodLote() && $this->getCodLoteFinal() ) {
        $stFiltro .= " lote.cod_lote = ".$this->getCodLoteFinal()." and ";
    } elseif ( $this->getCodLote() && $this->getCodLoteFinal() ) {
        $stFiltro .= " lote.cod_lote between ".$this->getCodLote()." and ".$this->getCodLoteFinal(). " AND";
    }
    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    }

    $stOrdem = " ";

    $obErro = $this->obTARRPagamento->recuperaConsultaLotesPagamento ( $rsRecordSet, $stFiltro, $stFiltroCGM, $stOrdem, $boTransacao );

    return $obErro;
}

function listarPagamentosLote(&$rsRecordSet, $boTransacao = '')
{
    $stFiltro = '';
    $inLote = false;
    if ( $this->getCodLote() ) {
        $stFiltro .= " pagamento_lote.cod_lote = ".$this->getCodLote()." AND\n";
        $inLote = $this->getCodLote();
    }
    if ( $this->obRMONAgencia->obRCGM->getNumCGM() ) {
        $stFiltro .= " accgm.numcgm = ".$this->obRMONAgencia->obRCGM->getNumCGM()." AND\n";
    }
    if ( $this->obRARRTipoPagamento->getNomeTipo() ) {
        $stFiltro .= " atp.nom_tipo = '".$this->obRARRTipoPagamento->getNomeTipo()."' AND\n";
    }
    if ( $this->getOcorrenciaPagamento() ) {
        $stFiltro .= " pagamentos_lote.ocorrencia_pagamento = '".$this->getOcorrenciaPagamento()."' AND\n";
    }

    if ( $this->obRARRCarne->obRARRParcela->roRARRLancamento->roRARRCalculo->obRARRGrupo->getCodGrupo() ) {

        $stFiltro .= " acgc.cod_grupo = ". $this->obRARRCarne->obRARRParcela->roRARRLancamento->roRARRCalculo->obRARRGrupo->getCodGrupo() ." AND\n";

    } elseif ($this->obRARRCarne->obRARRParcela->roRARRLancamento->roRARRCalculo->obRMONCredito->getCodCredito()) {

        $stFiltro .= " c.cod_credito = ";
        $stFiltro .= $this->obRARRCarne->obRARRParcela->roRARRLancamento->roRARRCalculo->obRMONCredito->getCodCredito();
        $stFiltro .= " and c.cod_especie = ";
        $stFiltro .= $this->obRARRCarne->obRARRParcela->roRARRLancamento->roRARRCalculo->obRMONCredito->getCodEspecie();
        $stFiltro .= " and c.cod_genero = ";
        $stFiltro .= $this->obRARRCarne->obRARRParcela->roRARRLancamento->roRARRCalculo->obRMONCredito->getCodGenero();
        $stFiltro .= " and c.cod_natureza = ";
        $stFiltro .= $this->obRARRCarne->obRARRParcela->roRARRLancamento->roRARRCalculo->obRMONCredito->getCodNatureza();
        $stFiltro .= " and acgc.cod_grupo is null  AND\n";

    }

    if ( $this->obRARRCarne->obRARRParcela->roRARRLancamento->roRARRCalculo->getExercicio() ) {
        $stFiltro .= " c.exercicio = '". $this->obRARRCarne->obRARRParcela->roRARRLancamento->roRARRCalculo->getExercicio() ."' AND\n";
    }

    $stFiltro .= " pagamento.cod_convenio != -1 AND";
    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    }

    $obErro = $this->obTARRPagamento->recuperaListaPagamentosLote ( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao, $inLote );

    return $obErro;
}


function listarPagamentosLoteDA(&$rsRecordSet, $boTransacao = '')
{
    $stFiltro = '';
    $inLote = $this->getCodLote();
    $obErro = $this->obTARRPagamento->recuperaListaPagamentosLoteDA ( $rsRecordSet, $inLote, $boTransacao );

    return $obErro;
}

function consultaListaFechaBaixaManual(&$rsRecordSet , $boTransacao = "")
{
    $stFiltro = "";
    if ($this->inCodLote) {
        $stFiltro .= " apl.cod_lote= '".$this->inCodLote."' and";
    }
    $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    $stOrdem = "";

    $obErro = $this->obTARRPagamentoLoteManual->recuperaListaFechaBaixaManual( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

function listaResumoBaixaCanceladas(&$rsRecordSet , $boTransacao = "", $dtDataBase = "")
{
    $stFiltro = "";
    // se a data estiver vazia
    if ( !$dtDataBase )
        $dtDataBase = date("Y-m-d");

    if ( $this->obRARRCarne->obRARRParcela->roRARRLancamento->getCodLancamento() ) {
        $stFiltro .= " cod_lancamento= ".$this->obRARRCarne->obRARRParcela->roRARRLancamento->getCodLancamento()." and ";
    }
    if ( $this->obRARRCarne->getNumeracao() ) {
        $stFiltro .= " numeracao= '".$this->obRARRCarne->getNumeracao()."' and ";
    }
    if ( $this->obRARRCarne->obRARRParcela->getCodParcela() ) {
        $stFiltro .= " cod_parcela= ".$this->obRARRCarne->obRARRParcela->getCodParcela()." and ";
    }
    if ($stFiltro) {
        $stFiltro = " WHERE ". substr ( $stFiltro, 0, strlen ( $stFiltro ) - 4 );
    }
    $stOrdem = " ORDER BY cod_parcela, nr_parcela";
    $obErro = $this->obTARRPagamento->recuperaListaPagamentosBaixaCanceladas($rsRecordSet, $stFiltro, $stOrdem, $boTransacao , $dtDataBase, $valorPorcentagem );

    return $obErro;
}

function listaResumoLoteBaixaAutomatica(&$rsRecordSet , $boTransacao = "")
{
    $stFiltro = "";
    if ($this->inCodLote) {
        $stFiltro .= " and plote.cod_lote= ".$this->inCodLote."";
    }
    if ($this->stExercicio) {
        $stFiltro .= " and plote.exercicio= '".$this->stExercicio."'";
    }

    $stOrdem = "";

    $obErro = $this->obTARRPagamento->recuperaResumoLoteListaBaixaAutomatica( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

   return $obErro;
}

} // fecha classe
