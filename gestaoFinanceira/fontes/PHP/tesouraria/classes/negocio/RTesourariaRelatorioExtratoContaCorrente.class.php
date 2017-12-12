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

    * Classe de Regra do Relatório de Extrato Conta Corrente
    * Data de Criação   : 16/11/2005
    *
    * @author Analista: Eduardo Schitz
    * @author Desenvolvedor: Carolina Schwaab Marçal
    *
    * @package URBEM
    * @subpackage Regra
    *
    * $id: $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE_RELATORIO   );
include_once( CAM_GF_TES_NEGOCIO."RTesourariaBoletim.class.php"             );
include_once( CAM_FW_PDF."RRelatorio.class.php"                  );

/**
    * Classe de Regra de Negócios Extrato Bancario
    * @author Desenvolvedor: Jose Eduardo Porto
*/
class RTesourariaRelatorioExtratoContaCorrente extends PersistenteRelatorio
{
/**
    * @var Integer
    * @access Private
*/
var $inCodPlano;
/**
    * @var String
    * @access Private
*/
var $stExercicio;
/**
    * @var String
    * @access Private
*/
var $stEntidade;
/**
    * @var String
    * @access Private
*/
var $stDataInicial;
/**
    * @var String
    * @access Private
*/
var $stDataFinal;
/**
    * @var String
    * @access Private
*/
var $stFiltro;
/**
    * @var Integer
    * @access Private
*/
var $inCodBanco;
/**
    * @var Integer
    * @access Private
*/
var $inCodAgencia;
/**
    * @var String
    * @access Private
*/
var $stCotaCorrente;

/**
    * @var Integer
    * @access Private
*/
var $inCodRecurso;

var $boImprimeSemContasSemMov = true;

var $boDemonstrarCredor = true;

/**
     * @access Public
     * @param Integer $valor
*/
function setCodPlano($valor) { $this->inCodPlano= $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setExercicio($valor) { $this->stExercicio        = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setEntidade($valor) { $this->stEntidade           = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setDataInicial($valor) { $this->stDataInicial      = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setDataFinal($valor) { $this->stDataFinal      = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setFiltro($valor) { $this->stFiltro      = $valor; }
/*
    * @access Public
    * @return Integer
*/
function setCodBanco($valor) { $this->inCodBanco =$valor;                   }
/*
    * @access Public
    * @return Integer
*/
function setCodAgencia($valor) { $this->inCodAgencia=$valor;                      }
/*
    * @access Public
    * @return Integer
*/
function setContaCorrente($valor) { $this->stContaCorrente=$valor; }
/*
    * @access Public
    * @return Integer
*/
function setCodRecurso($valor) { $this->inCodRecurso=$valor;  }
/*
    * @access Public
    * @return Integer
*/
function getCodPlano() { return $this->inCodPlano;                      }
/*
    * @access Public
    * @return String
*/
function getExercicio() { return $this->stExercicio;                      }
/*
    * @access Public
    * @return String
*/
function getEntidade() { return $this->stEntidade;                      }
/*
    * @access Public
    * @return String
*/
function getDataInicial() { return $this->stDataInicial;                      }
/*
    * @access Public
    * @return String
*/
function getDataFinal() { return $this->stDataFinal;                      }
/*
    * @access Public
    * @return String
*/
function getFiltro() { return $this->stFiltro;                      }
/*
    * @access Public
    * @return Integer
*/
function getCodBanco() { return $this->inCodBanco;                      }
/*
    * @access Public
    * @return Integer
*/
function getCodAgencia() { return $this->inCodAgencia;                      }
/*
    * @access Public
    * @return Integer
*/
function getContaCorrente() { return $this->stContaCorrente; }
/*
    * @access Public
    * @return Integer
*/
function getCodRecurso() { return $this->inCodRecurso; }


/**
    * Método Construtor
    * @access Private
*/
function RTesourariaRelatorioExtratoContaCorrente()
{
    $this->obRTesourariaBoletim            = new RTesourariaBoletim;
    $this->obRRelatorio                    = new RRelatorio;
    $this->obRTesourariaBoletim->addArrecadacao();
    $this->obRTesourariaBoletim->roUltimaArrecadacao->obROrcamentoEntidade->obRCGM->setNumCGM     ( Sessao::read('numCgm') );

}

/**
    * Método abstrato
    * @access Public
*/
function geraRecordSet(&$rsRecordSet , $stOrder = "")
{
    include_once( CAM_GF_TES_MAPEAMENTO."FTesourariaExtratoContaCorrente.class.php");
    $obFTesourariaExtratoContaCorrente = new FTesourariaExtratoContaCorrente;

    $codPlano = $this->getCodPlano()  ;

    $obFTesourariaExtratoContaCorrente->setDado("inCodPlanoInicial" , $codPlano[0] );
    $obFTesourariaExtratoContaCorrente->setDado("inCodPlanoFinal"   , $codPlano[1] );

    $obFTesourariaExtratoContaCorrente->setDado("stExercicio"     , $this->getExercicio());
    $obFTesourariaExtratoContaCorrente->setDado("stEntidade"      , $this->getEntidade());
    $obFTesourariaExtratoContaCorrente->setDado("stDataInicial"   , $this->getDataInicial());
    $obFTesourariaExtratoContaCorrente->setDado("stDataFinal"     , $this->getDataFinal());
    $obFTesourariaExtratoContaCorrente->setDado("inCodBanco"      , $this->getCodBanco());
    $obFTesourariaExtratoContaCorrente->setDado("inCodAgencia"    , $this->getCodAgencia());
    $obFTesourariaExtratoContaCorrente->setDado("stContaCorrente" , $this->getContaCorrente());
    $obFTesourariaExtratoContaCorrente->setDado("inCodRecurso"    , $this->getCodRecurso());
    $obErro = $obFTesourariaExtratoContaCorrente->recuperaDadosContaCorrente( $rsDadosBancarios, $stFiltroExtratoBancario, $stOrder );

    $rsMovimentacoes = new RecordSet();
    $rsTodasMovimentacoes = new RecordSet();
    $saldoAnterior = 0;
    $arTodasMovimentacaoes = array();
    //// buscando o extrato da conta
    if( $rsDadosBancarios->getNumLinhas() > 0 ) {
        while ( !$rsDadosBancarios->eof() ) {
            $obFTesourariaExtratoContaCorrente->setDado( "stDtInicial","01/01/".$this->getExercicio());
            $obFTesourariaExtratoContaCorrente->setDado( "inCodPlano", $rsDadosBancarios->getCampo( 'cod_plano' ) );
            
            if (substr($this->getDataInicial(),0,5) == "01/01") {
                $dtAnterior = $this->getDataInicial();
                $obFTesourariaExtratoContaCorrente->setDado( "boMovimentacao", "false" );
            } else {
                $dtInicial = explode("/",$this->getDataInicial());
                $dtAnterior = date("d/m/Y",mktime(0,0,0,$dtInicial[1],$dtInicial[0]-1,$dtInicial[2]));
                $obFTesourariaExtratoContaCorrente->setDado( "boMovimentacao", "false" );
            }
            $obFTesourariaExtratoContaCorrente->setDado( "stDtFinal", $dtAnterior );
            $obErro = $obFTesourariaExtratoContaCorrente->recuperaSaldoAnteriorAtual( $rsSaldoAnteriorAtual, $stFiltro, $stOrder );
            
            $obFTesourariaExtratoContaCorrente->setDado( "botcems", "false" );
            
            if (Sessao::getExercicio() > '2012') {
                $obFTesourariaExtratoContaCorrente->setDado( "botcems", "true" );
            }
            if($this->boDemonstrarCredor){
                $obFTesourariaExtratoContaCorrente->setDado( "credor", "true" );
            } else {
                $obFTesourariaExtratoContaCorrente->setDado( "credor", "false" );
            }
            $obFTesourariaExtratoContaCorrente->setDado( "inCodPlano", $rsDadosBancarios->getCampo( 'cod_plano' ) );
            
            $obErro = $obFTesourariaExtratoContaCorrente->recuperaTodos( $rsMovimentacoes, $stFiltro, $stOrder );

            $arTodasMovimentacaoes = array_merge($arTodasMovimentacaoes,$rsMovimentacoes->getElementos());
            
            $saldoAnterior = bcadd($saldoAnterior, $rsSaldoAnteriorAtual->getCampo("fn_saldo_conta_tesouraria"),4);
        
            $rsDadosBancarios->proximo();
        }
    }
    
    $arTodasMovimentacaoes = SistemaLegado::ordenaArray($arTodasMovimentacaoes,array( "data" => SORT_ASC ,"cod_arrecadacao" => SORT_ASC));
    
    $rsTodasMovimentacoes->preenche( $arTodasMovimentacaoes );

    // cria variavel tipo array para as movimentações das contas
    $arMovimentacoes = array();
    // monta a primeira linha do relatório com o Saldo anterior de todas as contas referentes ao filtro.
    $arMovimentacoes[0]["valor"]     = "";
    $arMovimentacoes[0]["data"]      = "";
    $arMovimentacoes[0]["descricao"] = "               SALDO ANTERIOR";
    $arMovimentacoes[0]["saldo"]     = number_format($saldoAnterior,2,',','.');
    
    $arDadosBancarios = array();

    $inCount = 0;
    $in = 0;

    $nuVlTotalArrecadacao             = 0;
    $nuVlTotalEstornoArrecadacao      = 0;
    $nuVlTotalPagamento               = 0;
    $nuVlTotalEstornoPagamento        = 0;
    $nuVlTotalArrecadacaoExtra        = 0;
    $nuVlTotalEstornoArrecadacaoExtra = 0;
    $nuVlTotalPagamentoExtra          = 0;
    $nuVlTotalEstornoPagamentoExtra   = 0;
    $nuVlTotalAplicacoes              = 0;
    $nuVlTotalResgates                = 0;
    $nuVlTotalDepositosRetiradas      = 0;
    
    $inCount = 1;
    
    $saldoAtual = $saldoAnterior;
    
    while ( !$rsTodasMovimentacoes->eof() ) {
        $arMovimentacoes[$inCount]["valor"]     = number_format($rsTodasMovimentacoes->getCampo("valor"),2,',','.');
        $arMovimentacoes[$inCount]["data"]      = $rsTodasMovimentacoes->getCampo("data");
        $saldoAtual = bcadd( $saldoAtual, $rsTodasMovimentacoes->getCampo("valor"), 2 );
    
        $stDescricao = str_replace(chr(10), '', $rsTodasMovimentacoes->getCampo('descricao'));
        $stDescricao = wordwrap($stDescricao, 70, chr(13));
        $arDescricao = explode(chr(13), $stDescricao);
    
        $inCountAux = $inCount;
        foreach ($arDescricao as $stDescricao) {
            $arMovimentacoes[$inCount]['descricao'] = $stDescricao;
            $inCount++;
        }
    
        if ( $stDtOld != $rsTodasMovimentacoes->getCampo("data") ) {
            $arMovimentacoes[$inCountAux-1]["saldo"] = number_format(bcsub($saldoAtual,$rsTodasMovimentacoes->getCampo("valor"), 2 ),2,',','.');
        }
        $stDtOld = $rsTodasMovimentacoes->getCampo("data");
    
        // Gera Totalizadores
        if ( $rsTodasMovimentacoes->getCampo('situacao') == '2' ) {
            $nuVlTotalEstornoArrecadacao = bcadd( $nuVlTotalEstornoArrecadacao, $rsTodasMovimentacoes->getCampo("valor"), 4 );
        } elseif ( $rsTodasMovimentacoes->getCampo('situacao') == '4' ) {
            $nuVlTotalEstornoPagamento = bcadd( $nuVlTotalEstornoPagamento, $rsTodasMovimentacoes->getCampo("valor"), 4 );
        } elseif ( $rsTodasMovimentacoes->getCampo('situacao') == 'X' ) {
            switch ( $rsTodasMovimentacoes->getCampo('cod_situacao') ) {
                    case '2' : $nuVlTotalEstornoPagamentoExtra   = bcadd( $nuVlTotalEstornoPagamentoExtra  , $rsTodasMovimentacoes->getCampo("valor"), 4 ); break;
                    case '1' : $nuVlTotalEstornoArrecadacaoExtra = bcadd( $nuVlTotalEstornoArrecadacaoExtra, $rsTodasMovimentacoes->getCampo("valor"), 4 ); break;
                }
        } else {
            if ( $rsTodasMovimentacoes->getCampo('situacao') == '1' ) {
                $nuVlTotalArrecadacao = bcadd( $nuVlTotalArrecadacao, $rsTodasMovimentacoes->getCampo("valor"), 4 );
            } if ( $rsTodasMovimentacoes->getCampo('situacao') == '3' ) {
                $nuVlTotalPagamento = bcadd( $nuVlTotalPagamento, $rsTodasMovimentacoes->getCampo("valor"), 4 );
            } if ( strpos($rsTodasMovimentacoes->getCampo('descricao'), 'Pagamento Extra') !== false ) {
                $nuVlTotalPagamentoExtra     = bcadd( $nuVlTotalPagamentoExtra, $rsTodasMovimentacoes->getCampo("valor"), 4 );
            } if ( strpos($rsTodasMovimentacoes->getCampo('descricao'), 'Arrecadação Extra') !== false ) {
                $nuVlTotalArrecadacaoExtra   = bcadd( $nuVlTotalArrecadacaoExtra, $rsTodasMovimentacoes->getCampo("valor"), 4 );
            } if ( strpos($rsTodasMovimentacoes->getCampo('descricao'), 'Aplicação') !== false ) {
                $nuVlTotalAplicacoes         = bcadd( $nuVlTotalAplicacoes, $rsTodasMovimentacoes->getCampo("valor"), 4 );
            } if ( strpos($rsTodasMovimentacoes->getCampo('descricao'), 'Resgate') !== false ) {
                $nuVlTotalResgates           = bcadd( $nuVlTotalResgates, $rsTodasMovimentacoes->getCampo("valor"), 4 );
            } if ( strpos($rsTodasMovimentacoes->getCampo('descricao'), 'Depósito/Retirada') !== false ) {
                $nuVlTotalDepositosRetiradas = bcadd( $nuVlTotalDepositosRetiradas, $rsTodasMovimentacoes->getCampo("valor"), 4 );
            }
        }
    
        $rsTodasMovimentacoes->proximo();
    }
    
    $arMovimentacoes[$inCount]["valor"]     = "";
    $arMovimentacoes[$inCount]["data"]      = "";
    $arMovimentacoes[$inCount]["descricao"] = "               SALDO ATUAL";
    $arMovimentacoes[$inCount]["saldo"]     = number_format($saldoAtual,2,',','.');
    
    $boContaTemMovimentacao = ( $rsTodasMovimentacoes->getNumLinhas() > 0 ) ? true : false;
    // Para forçar caso for informada apenas uma conta e não gerar um relatório em branco.
    if ($rsDadosBancarios->getNumLinhas() == 1) {
        $boContaTemMovimentacao = true;
        $this->boImprimeContasSemMov = true;
    }
    if ( ($this->boImprimeContasSemMov || $boContaTemMovimentacao) ) {
       $arDadosBancarios[$in]["dados_banco"] = $stDadosBanco;
       $arDadosBancarios[$in]['movimentacao'] = $arMovimentacoes;
       $in++;
    }

    $nuVlTotalLiquidoArrecadacao      = bcadd( $nuVlTotalArrecadacao, $nuVlTotalEstornoArrecadacao, 4);
    $nuVlTotalLiquidoPagamento        = bcadd( $nuVlTotalPagamento, $nuVlTotalEstornoPagamento, 4);
    $nuVlTotalLiquidoArrecadacaoExtra = bcadd( $nuVlTotalArrecadacaoExtra, $nuVlTotalEstornoArrecadacaoExtra, 4);
    $nuVlTotalLiquidoPagamentoExtra   = bcadd( $nuVlTotalPagamentoExtra, $nuVlTotalEstornoPagamentoExtra, 4);

    $inCount = 0;
    $arTotalDemonstrativo[$inCount]["descricao"] = "Total de Arrecadações Orçamentárias";
    $arTotalDemonstrativo[$inCount]["valor"] = number_format( $nuVlTotalArrecadacao, 2, ',', '.');
    $arTotalDemonstrativo[$inCount]["descricao_liquido"]  = "Arrecadação Orçamentária Líquida";
    $arTotalDemonstrativo[$inCount]["valor_liquido"]  = number_format( $nuVlTotalLiquidoArrecadacao, 2, ',', '.');

    $inCount++;
    $arTotalDemonstrativo[$inCount]["descricao"] = "Total de Estorno de Arrecadações Orçamentárias";
    $arTotalDemonstrativo[$inCount]["valor"] = number_format( $nuVlTotalEstornoArrecadacao, 2, ',', '.');
    $arTotalDemonstrativo[$inCount]["descricao_liquido"]  = "";
    $arTotalDemonstrativo[$inCount]["valor_liquido"]  = "";

    $inCount++;
    $arTotalDemonstrativo[$inCount]["descricao"] = "Total de Arrecadações Extra-Orçamentárias";
    $arTotalDemonstrativo[$inCount]["valor"] = number_format( $nuVlTotalArrecadacaoExtra, 2, ',', '.');
    $arTotalDemonstrativo[$inCount]["descricao_liquido"]  = "Arrecadação Extra-Orçamentária Líquida";
    $arTotalDemonstrativo[$inCount]["valor_liquido"]  = number_format( $nuVlTotalLiquidoArrecadacaoExtra, 2, ',', '.');

    $inCount++;
    $arTotalDemonstrativo[$inCount]["descricao"] = "Total de Estorno de Arrecadações Extra-Orçamentárias";
    $arTotalDemonstrativo[$inCount]["valor"] = number_format( $nuVlTotalEstornoArrecadacaoExtra, 2, ',', '.');
    $arTotalDemonstrativo[$inCount]["descricao_liquido"]  = "";
    $arTotalDemonstrativo[$inCount]["valor_liquido"]  = "";

    $inCount++;
    $arTotalDemonstrativo[$inCount]["descricao"] = "Total de Pagamentos Orçamentários";
    $arTotalDemonstrativo[$inCount]["valor"] = number_format( $nuVlTotalPagamento, 2, ',', '.' );
    $arTotalDemonstrativo[$inCount]["descricao_liquido"]  = "Pagamento Orçamentário Líquido";
    $arTotalDemonstrativo[$inCount]["valor_liquido"]  = number_format( $nuVlTotalLiquidoPagamento, 2, ',', '.');

    $inCount++;
    $arTotalDemonstrativo[$inCount]["descricao"] = "Total de Estorno de Pagamentos Orçamentários";
    $arTotalDemonstrativo[$inCount]["valor"] = number_format( $nuVlTotalEstornoPagamento, 2, ',', '.' );
    $arTotalDemonstrativo[$inCount]["descricao_liquido"]  = "";
    $arTotalDemonstrativo[$inCount]["valor_liquido"]  = "";

    $inCount++;
    $arTotalDemonstrativo[$inCount]["descricao"] = "Total de Pagamentos Extra-Orçamentários";
    $arTotalDemonstrativo[$inCount]["valor"] = number_format( $nuVlTotalPagamentoExtra, 2, ',', '.' );
    $arTotalDemonstrativo[$inCount]["descricao_liquido"]  = "Pagamento Extra-Orçamentário Líquido";
    $arTotalDemonstrativo[$inCount]["valor_liquido"]  = number_format( $nuVlTotalLiquidoPagamentoExtra, 2, ',', '.');

    $inCount++;
    $arTotalDemonstrativo[$inCount]["descricao"] = "Total de Estorno de Pagamentos Extra-Orçamentários";
    $arTotalDemonstrativo[$inCount]["valor"] = number_format( $nuVlTotalEstornoPagamentoExtra, 2, ',', '.' );
    $arTotalDemonstrativo[$inCount]["descricao_liquido"]  = "";
    $arTotalDemonstrativo[$inCount]["valor_liquido"]  = "";

    $inCount++;
    $arTotalDemonstrativo[$inCount]["descricao"] = "Total de Aplicações";
    $arTotalDemonstrativo[$inCount]["valor"] = number_format( $nuVlTotalAplicacoes, 2, ',', '.' );
    $arTotalDemonstrativo[$inCount]["descricao_liquido"]  = "";
    $arTotalDemonstrativo[$inCount]["valor_liquido"]  = "";

    $inCount++;
    $arTotalDemonstrativo[$inCount]["descricao"] = "Total de Resgates";
    $arTotalDemonstrativo[$inCount]["valor"] = number_format( $nuVlTotalResgates, 2, ',', '.' );
    $arTotalDemonstrativo[$inCount]["descricao_liquido"]  = "";
    $arTotalDemonstrativo[$inCount]["valor_liquido"]  = "";

    $inCount++;
    $arTotalDemonstrativo[$inCount]["descricao"] = "Total de Depósitos/Retiradas";
    $arTotalDemonstrativo[$inCount]["valor"] = number_format( $nuVlTotalDepositosRetiradas, 2, ',', '.' );
    $arTotalDemonstrativo[$inCount]["descricao_liquido"]  = "";
    $arTotalDemonstrativo[$inCount]["valor_liquido"]  = "";

    $rsTotalDemonstrativo = new RecordSet;
    $rsTotalDemonstrativo->preenche( $arTotalDemonstrativo );

    $rsRecordSet[0] = $arDadosBancarios;
    $rsRecordSet[1] = $rsTotalDemonstrativo;

    return $obErro;
}

}
