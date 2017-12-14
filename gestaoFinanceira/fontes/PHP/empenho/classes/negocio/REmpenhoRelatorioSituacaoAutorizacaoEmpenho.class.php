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
    * Classe de Regra do Relatório de Situação Autorização de Empenho
    * Data de Criação   : 16/10/2006

    * @author Desenvolvedor: Tonismar Régis Bernardo

    $Revision: 30805 $
    $Name$
    $Autor: $
    $Date: 2007-12-04 12:08:26 -0200 (Ter, 04 Dez 2007) $

    * Casos de uso: uc-02.03.34
*/

/*

$Log:

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE_RELATORIO   );
include_once( CAM_GF_EMP_NEGOCIO."REmpenhoEmpenho.class.php"             );
include_once( CAM_FW_PDF."RRelatorio.class.php"                  );

/**
    * Classe de Regra de Negócios Situacão Empenhos
    * @author Desenvolvedor: Lucas Leusin Oaigen
*/
class REmpenhoRelatorioSituacaoAutorizacaoEmpenho extends PersistenteRelatorio
{
/**
    * @var Integer
    * @access Private
*/
var $inOrdenacao;
/**
    * @var Integer
    * @access Private
*/
var $inSituacao;
/**
    * @var String
    * @access Private
*/
var $stFiltro;
/**
    * @var Object
    * @access Private
*/
var $inCodEntidade;
/**
    * @var String
    * @access Private
*/
var $stDataInicialAnulacao;
/**
    * @var String
    * @access Private
*/
var $stDataFinalAnulacao;
/**
    * @var String
    * @access Private
*/
var $stDataInicialLiquidacao;
/**
    * @var String
    * @access Private
*/
var $stDataFinalLiquidacao;
/**
    * @var String
    * @access Private
*/
var $stDataInicialEstornoLiquidacao;
/**
    * @var String
    * @access Private
*/
var $stDataFinalEstornoLiquidacao;
/**
    * @var String
    * @access Private
*/
var $stDataInicialPagamento;
/**
    * @var String
    * @access Private
*/
var $stDataFinalPagamento;
/**
    * @var String
    * @access Private
*/
var $stDataInicialEstornoPagamento;
/**
    * @var String
    * @access Private
*/
var $stDataFinalEstornoPagamento;
/**
    * @var String
    * @access Private
*/
var $inCodAutorizacao;
/**
    * @var Integer
    * @access Private
*/
var $inCentroCusto;

/**
     * @access Public
     * @param Integer $valor
*/
function setOrdenacao($valor) { $this->inOrdenacao        = $valor; }
/**
     * @access Public
     * @param Integer $valor
*/
function setSituacao($valor) { $this->inSituacao        = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setFiltro($valor) { $this->stFiltro           = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setCodEntidade($valor) { $this->inCodEntidade      = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setDataInicialAnulacao($valor) { $this->stDataInicialAnulacao      = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setDataFinalAnulacao($valor) { $this->stDataFinalAnulacao      = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setDataInicialLiquidacao($valor) { $this->stDataInicialLiquidacao      = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setDataFinalLiquidacao($valor) { $this->stDataFinalLiquidacao      = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setDataInicialEstornoLiquidacao($valor) { $this->stDataInicialEstornoLiquidacao      = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setDataFinalEstornoLiquidacao($valor) { $this->stDataFinalEstornoLiquidacao      = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setDataInicialPagamento($valor) { $this->stDataInicialPagamento      = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setDataFinalPagamento($valor) { $this->stDataFinalPagamento      = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setDataInicialEstornoPagamento($valor) { $this->stDataInicialEstornoPagamento      = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setDataFinalEstornoPagamento($valor) { $this->stDataFinalEstornoPagamento      = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setCodAutorizacao($valor) { $this->inCodAutorizacao      = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setCentroCusto($valor) { $this->inCentroCusto = $valor; }

/**
     * @access Public
     * @return Integer
*/
function getOrdenacao() { return $this->inOrdenacao;                  }
/**
     * @access Public
     * @return Integer
*/
function getSituacao() { return $this->inSituacao;                  }
/**
     * @access Public
     * @return String
*/
function getFiltro() { return $this->stFiltro;                     }
/**
     * @access Public
     * @param Object $valor
*/
function getCodEntidade() { return $this->inCodEntidade;                }
/**
     * @access Public
     * @param Object $valor
*/
function getDataInicialAnulacao() { return $this->stDataInicialAnulacao;                }
/**
     * @access Public
     * @param Object $valor
*/
function getDataFinalAnulacao() { return $this->stDataFinalAnulacao;                }
/**
     * @access Public
     * @param Object $valor
*/
function getDataInicialLiquidacao() { return $this->stDataInicialLiquidacao;                }
/**
     * @access Public
     * @param Object $valor
*/
function getDataFinalLiquidacao() { return $this->stDataFinalLiquidacao;                }
/**
     * @access Public
     * @param Object $valor
*/
function getDataInicialEstornoLiquidacao() { return $this->stDataInicialEstornoLiquidacao;                }
/**
     * @access Public
     * @param Object $valor
*/
function getDataFinalEstornoLiquidacao() { return $this->stDataFinalEstornoLiquidacao;                }
/**
     * @access Public
     * @param Object $valor
*/
function getDataInicialPagamento() { return $this->stDataInicialPagamento;                }
/**
     * @access Public
     * @param Object $valor
*/
function getDataFinalPagamento() { return $this->stDataFinalPagamento;                }
/**
     * @access Public
     * @param Object $valor
*/
function getDataInicialEstornoPagamento() { return $this->stDataInicialEstornoPagamento;                }
/**
     * @access Public
     * @param Object $valor
*/
function getDataFinalEstornoPagamento() { return $this->stDataFinalEstornoPagamento;                }
/**
     * @access Public
     * @param Object $valor
*/
function getCodAutorizacao() { return $this->inCodAutorizacao;                }
/**
     * @access Public
     * @param Object $valor
*/
function getCentroCusto() { return $this->inCentroCusto;                }

/**
    * Método Construtor
    * @access Private
*/
function REmpenhoRelatorioSituacaoAutorizacaoEmpenho()
{
    $this->obREmpenhoEmpenho            = new REmpenhoEmpenho;
    $this->obRRelatorio                 = new RRelatorio;
    $this->obREmpenhoEmpenho->obROrcamentoEntidade->obRCGM->setNumCGM     ( Sessao::read('numCgm') );

}

/**
    * Método abstrato
    * @access Public
*/
function geraRecordSet(&$rsRecordSet , $stOrder = "")
{
    include_once( CAM_GF_EMP_MAPEAMENTO."FEmpenhoSituacaoAutorizacaoEmpenho.class.php");
    $obFEmpenhoSituacaoEmpenho    = new FEmpenhoSituacaoAutorizacaoEmpenho;

    $stFiltro = "";
    if ( $this->getCodEntidade() ) {
        $stEntidade .= $this->getCodEntidade();
    } else {
        $this->obREmpenhoEmpenho->obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidades );
        while ( !$rsEntidades->eof() ) {
            $stEntidade .= $rsEntidades->getCampo( 'cod_entidade' ).",";
            $rsEntidades->proximo();
        }
        $stEntidade = substr( $stEntidade, 0, strlen($stEntidade) - 1 );
        $stEntidade = $stEntidade;
    }

    $obFEmpenhoSituacaoEmpenho->setDado("stFiltro"                ,$this->getFiltro());
    $obFEmpenhoSituacaoEmpenho->setDado("stEntidade"              ,$this->getCodEntidade());
    $obFEmpenhoSituacaoEmpenho->setDado("exercicio"               ,$this->obREmpenhoEmpenho->getExercicio());
    $obFEmpenhoSituacaoEmpenho->setDado("stDataInicialEmissao"    ,$this->obREmpenhoEmpenho->getDtEmpenhoInicial());
    $obFEmpenhoSituacaoEmpenho->setDado("stDataFinalEmissao"      ,$this->obREmpenhoEmpenho->getDtEmpenhoFinal());
    $obFEmpenhoSituacaoEmpenho->setDado("stDataInicialAnulacao"   ,$this->getDataInicialAnulacao());
    $obFEmpenhoSituacaoEmpenho->setDado("stDataFinalAnulacao"     ,$this->getDataFinalAnulacao());
    $obFEmpenhoSituacaoEmpenho->setDado("stDataInicialLiquidacao" ,$this->getDataInicialLiquidacao());
    $obFEmpenhoSituacaoEmpenho->setDado("stDataFinalLiquidacao"   ,$this->getDataFinalLiquidacao());
    $obFEmpenhoSituacaoEmpenho->setDado("stDataInicialEstornoLiquidacao" ,$this->getDataInicialEstornoLiquidacao());
    $obFEmpenhoSituacaoEmpenho->setDado("stDataFinalEstornoLiquidacao"   ,$this->getDataFinalEstornoLiquidacao());
    $obFEmpenhoSituacaoEmpenho->setDado("stDataInicialPagamento"  ,$this->getDataInicialPagamento());
    $obFEmpenhoSituacaoEmpenho->setDado("stDataFinalPagamento"    ,$this->getDataFinalPagamento());
    $obFEmpenhoSituacaoEmpenho->setDado("stDataInicialEstornoPagamento"  ,$this->getDataInicialEstornoPagamento());
    $obFEmpenhoSituacaoEmpenho->setDado("stDataFinalEstornoPagamento"    ,$this->getDataFinalEstornoPagamento());
    $obFEmpenhoSituacaoEmpenho->setDado("inCodEmpenhoInicial"     ,$this->obREmpenhoEmpenho->getCodEmpenhoInicial());
    $obFEmpenhoSituacaoEmpenho->setDado("inCodEmpenhoFinal"       ,$this->obREmpenhoEmpenho->getCodEmpenhoFinal());
    $obFEmpenhoSituacaoEmpenho->setDado("inCodDotacao"            ,$this->obREmpenhoEmpenho->getCodDespesa());
    $obFEmpenhoSituacaoEmpenho->setDado("inCodDespesa"            ,$this->obREmpenhoEmpenho->obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->getCodEstrutural() );
    $obFEmpenhoSituacaoEmpenho->setDado("inCodRecurso"            ,$this->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoRecurso->getCodRecurso() );
    $obFEmpenhoSituacaoEmpenho->setDado("inCodDetalhamento"       ,$this->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoRecurso->getCodDetalhamento() );
    $obFEmpenhoSituacaoEmpenho->setDado("stDestinacaoRecurso"     ,$this->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoRecurso->getDestinacaoRecurso() );
    $obFEmpenhoSituacaoEmpenho->setDado("inNumOrgao"              ,$this->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->getNumeroOrgao());
    $obFEmpenhoSituacaoEmpenho->setDado("inNumUnidade"            ,$this->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->getNumeroUnidade() );
    $obFEmpenhoSituacaoEmpenho->setDado("inOrdenacao"             ,$this->getOrdenacao());
    $obFEmpenhoSituacaoEmpenho->setDado("inCodFornecedor"         ,$this->obREmpenhoEmpenho->obRCGM->getNumCGM());
    $obFEmpenhoSituacaoEmpenho->setDado("inSituacao"              ,$this->getSituacao());
    $obFEmpenhoSituacaoEmpenho->setDado("inCodAutorizacao"        ,$this->getCodAutorizacao());

    if (Sessao::getExercicio() > '2015') {
        $obFEmpenhoSituacaoEmpenho->setDado("inCentroCusto"       ,$this->getCentroCusto());
    }

    $obErro = $obFEmpenhoSituacaoEmpenho->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder );

    $inCount                  = 0;
    $inTotalAutorizado  	   = 0;
    $inTotalAutorizadoAnulado = 0;
    $inTotalSaldoAutorizado   = 0;
    $inTotalLiquidado         = 0;
    $inTotalPago              = 0;
    $arRecord           = array();

    while ( !$rsRecordSet->eof() ) {

        //alterações para quebra de linha
        $stNomContaTemp = str_replace( chr(10), "", trim($rsRecordSet->getCampo('credor')) );
        $stNomContaTemp = wordwrap( $stNomContaTemp,30,chr(13) );
        $arNomContaOLD = explode( chr(13), $stNomContaTemp );
        //fim de alterações para quebra de linha

        $arRecord[$inCount]['nivel']            = 1;
        if ( $rsRecordSet->getCampo('empenho') ) {
            $arRecord[$inCount]['empenho']          = $rsRecordSet->getCampo('entidade') . " - " . $rsRecordSet->getCampo('empenho') . "/" . $rsRecordSet->getCampo('exercicio');
            $arRecord[$inCount]['liquidado']        = number_format( $rsRecordSet->getCampo('liquidado'), 2, ',', '.' );
            $arRecord[$inCount]['pago']             = number_format( $rsRecordSet->getCampo('pago'), 2, ',', '.' );
            $arRecord[$inCount]['empenhadoapagar']  = number_format( $rsRecordSet->getCampo('empenhadoapagar'), 2, ',', '.' );
        } else {
            $arRecor[$inCount]['empenho'] = '';
            $arRecor[$inCount]['liquidado'] = '';
            $arRecor[$inCount]['pago'] = '';
            $arRecor[$inCount]['empenhadoapagar'] = '';
        }
        $arRecord[$inCount]['autorizacao']      = $rsRecordSet->getCampo('entidade') . " - " . $rsRecordSet->getCampo('autorizacao') . "/" . $rsRecordSet->getCampo('exercicio');
        $arRecord[$inCount]['emissao']          = $rsRecordSet->getCampo('emissao');

        $inCount2 = $inCount;
        //alterações para quebra de linha
        foreach ($arNomContaOLD as $stNomContaTemp) {
            $arRecord[$inCount2]['credor']    = $stNomContaTemp;
            $inCount2++;
        }

        $arRecord[$inCount]['autorizado']             = number_format( $rsRecordSet->getCampo('autorizado'), 2, ',', '.' );
        $arRecord[$inCount]['autorizado_anulado']     = number_format( $rsRecordSet->getCampo('autorizado_anulado'), 2, ',', '.' );
        $arRecord[$inCount]['saldoautorizado']        = number_format( $rsRecordSet->getCampo('saldoautorizado'), 2, ',', '.' );

        $inCount = $inCount2;

        $inTotalAutorizado            = $inTotalAutorizado + $rsRecordSet->getCampo('autorizado');
        $inTotalAutorizadoAnulado     = $inTotalAutorizadoAnulado + $rsRecordSet->getCampo('autorizado_anulado');
        $inTotalSaldoAutorizado       = $inTotalSaldoAutorizado + $rsRecordSet->getCampo('saldoautorizado');
        $inTotalLiquidado           = $inTotalLiquidado + $rsRecordSet->getCampo('liquidado');
        $inTotalPago                = $inTotalPago + $rsRecordSet->getCampo('pago');
        $inTotalEmpenhadoAPagar     = $inTotalEmpenhadoAPagar + $rsRecordSet->getCampo('empenhadoapagar');

        $rsRecordSet->proximo();

    }

    if ($inCount>0) {
    //MONTA TOTALIZADOR GERAL
    $arRecord[$inCount]['nivel']            = 1;
    $arRecord[$inCount]['autorizacao']      = "";
    $arRecord[$inCount]['empenho']          = "";
    $arRecord[$inCount]['emissao ']         = "";
    $arRecord[$inCount]['credor']           = "";
    $arRecord[$inCount]['autorizado']       = "";
    $arRecord[$inCount]['autorizado_anulado'] = "";
    $arRecord[$inCount]['saldoautorizado']  = "";
    $arRecord[$inCount]['pago']             = "";
    $arRecord[$inCount]['empenhadoapagar']  = "";

    $inCount++;

    //MONTA TOTALIZADOR GERAL
    $arRecord[$inCount]['nivel']            = 2;
    $arRecord[$inCount]['empenho']          = "";
    $arRecord[$inCount]['autorizacao']      = "";
    $arRecord[$inCount]['emissao ']         = "";
    $arRecord[$inCount]['credor']           = "TOTAL";
    $arRecord[$inCount]['liquidado']        = number_format( $inTotalLiquidado, 2, ',', '.' );
    $arRecord[$inCount]['pago']             = number_format( $inTotalPago, 2, ',', '.' );
    $arRecord[$inCount]['empenhadoapagar']  = number_format( $inTotalEmpenhadoAPagar, 2, ',', '.' );
    $arRecord[$inCount]['autorizado']       = number_format( $inTotalAutorizado,2, ',', '.' );
    $arRecord[$inCount]['autorizado_anulado'] = number_format( $inTotalAutorizadoAnulado,2, ',', '.' );
    $arRecord[$inCount]['saldoautorizado']  = number_format( $inTotalSaldoAutorizado,2, ',', '.' );

    $inCount++;
    }

    $rsRecordSet = new RecordSet;
    $rsRecordSet->preenche( $arRecord );

    return $obErro;
}

}