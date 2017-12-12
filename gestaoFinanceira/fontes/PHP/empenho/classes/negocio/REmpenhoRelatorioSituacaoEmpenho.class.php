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
    * Classe de Regra do Relatório de Situação de Empenho
    * Data de Criação   : 13/05/2005

    * @author Analista: Dieine da Silva
    * @author Desenvolvedor: Lucas Leusin Oaigen

    * @package URBEM
    * @subpackage Regra

    $Revision: 30805 $
    $Name$
    $Autor: $
    $Date: 2007-12-04 12:08:26 -0200 (Ter, 04 Dez 2007) $

    * Casos de uso: uc-02.03.13
*/

/*
$Log$
Revision 1.8  2006/07/05 20:47:06  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE_RELATORIO   );
include_once( CAM_GF_EMP_NEGOCIO."REmpenhoEmpenho.class.php"             );
include_once( CAM_FW_PDF."RRelatorio.class.php"                  );

/**
    * Classe de Regra de Negócios Situacão Empenhos
    * @author Desenvolvedor: Lucas Leusin Oaigen
*/
class REmpenhoRelatorioSituacaoEmpenho extends PersistenteRelatorio
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
function setTipoEmpenho($valor)
{
    $this->stTipoEmpenho = $valor;
}
/**
     * @access Public
     * @param Object $valor
*/
function setCentroCusto($valor)
{
    $this->inCentroCusto = $valor;
}

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
function getTipoEmpenho()
{
    return $this->stTipoEmpenho;
}
/**
     * @access Public
     * @param Object $valor
*/
function getCentroCusto()
{
    return $this->inCentroCusto;
}

/**
    * Método Construtor
    * @access Private
*/
function REmpenhoRelatorioSituacaoEmpenho()
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
    include_once( CAM_GF_EMP_MAPEAMENTO."FEmpenhoSituacaoEmpenho.class.php");
    $obFEmpenhoSituacaoEmpenho    = new FEmpenhoSituacaoEmpenho;

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

    $obFEmpenhoSituacaoEmpenho->setDado("stFiltro", $this->getFiltro());
    $obFEmpenhoSituacaoEmpenho->setDado("stEntidade", $this->getCodEntidade());
    $obFEmpenhoSituacaoEmpenho->setDado("exercicio", $this->obREmpenhoEmpenho->getExercicio());
    $obFEmpenhoSituacaoEmpenho->setDado("stDataInicialEmissao", $this->obREmpenhoEmpenho->getDtEmpenhoInicial());
    $obFEmpenhoSituacaoEmpenho->setDado("stDataFinalEmissao", $this->obREmpenhoEmpenho->getDtEmpenhoFinal());
    $obFEmpenhoSituacaoEmpenho->setDado("stDataInicialAnulacao", $this->getDataInicialAnulacao());
    $obFEmpenhoSituacaoEmpenho->setDado("stDataFinalAnulacao", $this->getDataFinalAnulacao());
    $obFEmpenhoSituacaoEmpenho->setDado("stDataInicialLiquidacao", $this->getDataInicialLiquidacao());
    $obFEmpenhoSituacaoEmpenho->setDado("stDataFinalLiquidacao", $this->getDataFinalLiquidacao());
    $obFEmpenhoSituacaoEmpenho->setDado("stDataInicialEstornoLiquidacao", $this->getDataInicialEstornoLiquidacao());
    $obFEmpenhoSituacaoEmpenho->setDado("stDataFinalEstornoLiquidacao", $this->getDataFinalEstornoLiquidacao());
    $obFEmpenhoSituacaoEmpenho->setDado("stDataInicialPagamento", $this->getDataInicialPagamento());
    $obFEmpenhoSituacaoEmpenho->setDado("stDataFinalPagamento", $this->getDataFinalPagamento());
    $obFEmpenhoSituacaoEmpenho->setDado("stDataInicialEstornoPagamento", $this->getDataInicialEstornoPagamento());
    $obFEmpenhoSituacaoEmpenho->setDado("stDataFinalEstornoPagamento", $this->getDataFinalEstornoPagamento());
    $obFEmpenhoSituacaoEmpenho->setDado("inCodEmpenhoInicial", $this->obREmpenhoEmpenho->getCodEmpenhoInicial());
    $obFEmpenhoSituacaoEmpenho->setDado("inCodEmpenhoFinal", $this->obREmpenhoEmpenho->getCodEmpenhoFinal());
    $obFEmpenhoSituacaoEmpenho->setDado("inCodDotacao", $this->obREmpenhoEmpenho->getCodDespesa());
    $obFEmpenhoSituacaoEmpenho->setDado("inCodDespesa", $this->obREmpenhoEmpenho->obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->getCodEstrutural() );
    $obFEmpenhoSituacaoEmpenho->setDado("inCodRecurso", $this->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoRecurso->getCodRecurso() );
    $obFEmpenhoSituacaoEmpenho->setDado("stDestinacaoRecurso", $this->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoRecurso->getDestinacaoRecurso() );
    $obFEmpenhoSituacaoEmpenho->setDado("inCodDetalhamento"  , $this->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoRecurso->getCodDetalhamento() );
    $obFEmpenhoSituacaoEmpenho->setDado("inNumOrgao", $this->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->getNumeroOrgao());
    $obFEmpenhoSituacaoEmpenho->setDado("inNumUnidade", $this->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->getNumeroUnidade() );
    $obFEmpenhoSituacaoEmpenho->setDado("inOrdenacao", $this->getOrdenacao());
    $obFEmpenhoSituacaoEmpenho->setDado("inCodFornecedor", $this->obREmpenhoEmpenho->obRCGM->getNumCGM());
    $obFEmpenhoSituacaoEmpenho->setDado("inSituacao", $this->getSituacao());
    $obFEmpenhoSituacaoEmpenho->setDado("stTipoEmpenho", $this->getTipoEmpenho());

    if (Sessao::getExercicio() > '2015') {
        $obFEmpenhoSituacaoEmpenho->setDado("inCentroCusto",$this->getCentroCusto());
    }

    $obErro = $obFEmpenhoSituacaoEmpenho->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder );

    $inCount            = 0;
    $inTotalEmpenhado   = 0;
    $inTotalAnulado     = 0;
    $inTotalLiquidado   = 0;
    $inTotalPago        = 0;
    $arRecord           = array();

    while ( !$rsRecordSet->eof() ) {

        //alterações para quebra de linha
        $stNomContaTemp = str_replace( chr(10), "", trim($rsRecordSet->getCampo('credor')) );
        $stNomContaTemp = wordwrap( $stNomContaTemp,30,chr(13) );
        $arNomContaOLD = explode( chr(13), $stNomContaTemp );
        //fim de alterações para quebra de linha

        $arRecord[$inCount]['nivel']            = 1;
        $arRecord[$inCount]['empenho']          = $rsRecordSet->getCampo('entidade') . " - " . $rsRecordSet->getCampo('empenho') . "/" . $rsRecordSet->getCampo('exercicio');
        $arRecord[$inCount]['emissao']          = $rsRecordSet->getCampo('emissao');

        $inCount2 = $inCount;
        //alterações para quebra de linha
        foreach ($arNomContaOLD as $stNomContaTemp) {
            $arRecord[$inCount2]['credor']    = $stNomContaTemp;
            $inCount2++;
        }

        $arRecord[$inCount]['empenhado']        = number_format( $rsRecordSet->getCampo('empenhado'), 2, ',', '.' );
        $arRecord[$inCount]['saldoempenhado']   = number_format( $rsRecordSet->getCampo('saldoempenhado'), 2, ',', '.' );
        $arRecord[$inCount]['anulado']          = number_format( $rsRecordSet->getCampo('anulado'), 2, ',', '.' );
        $arRecord[$inCount]['liquidado']        = number_format( $rsRecordSet->getCampo('liquidado'), 2, ',', '.' );
        $arRecord[$inCount]['aliquidar']        = number_format( $rsRecordSet->getCampo('aliquidar'), 2, ',', '.' );
        $arRecord[$inCount]['pago']             = number_format( $rsRecordSet->getCampo('pago'), 2, ',', '.' );
        $arRecord[$inCount]['empenhadoapagar']  = number_format( $rsRecordSet->getCampo('empenhadoapagar'), 2, ',', '.' );
        $arRecord[$inCount]['liquidadoapagar']  = number_format( $rsRecordSet->getCampo('liquidadoapagar'), 2, ',', '.' );
        $arRecord[$inCount]['cod_recurso']      = $rsRecordSet->getCampo('cod_recurso');
        $arRecord[$inCount]['entidade']         = $rsRecordSet->getCampo('entidade');

        $inCount = $inCount2;

        $inTotalEmpenhado           = $inTotalEmpenhado + $rsRecordSet->getCampo('empenhado');
        $inTotalAnulado             = $inTotalAnulado + $rsRecordSet->getCampo('anulado');
        $inTotalSaldoEmpenhado      = $inTotalSaldoEmpenhado + $rsRecordSet->getCampo('saldoempenhado');
        $inTotalLiquidado           = $inTotalLiquidado + $rsRecordSet->getCampo('liquidado');
        $inTotalALiquidar           = $inTotalALiquidar + $rsRecordSet->getCampo('aliquidar');
        $inTotalPago                = $inTotalPago + $rsRecordSet->getCampo('pago');
        $inTotalEmpenhadoAPagar     = $inTotalEmpenhadoAPagar + $rsRecordSet->getCampo('empenhadoapagar');
        $inTotalLiquidadoAPagar     = $inTotalLiquidadoAPagar + $rsRecordSet->getCampo('liquidadoapagar');

        $rsRecordSet->proximo();

    }

    if ($inCount>0) {
    //MONTA TOTALIZADOR GERAL
    $arRecord[$inCount]['nivel']            = 1;
    $arRecord[$inCount]['empenho']          = "";
    $arRecord[$inCount]['emissao ']         = "";
    $arRecord[$inCount]['credor']           = "";
    $arRecord[$inCount]['empenhado']        = "";
    $arRecord[$inCount]['anulado']          = "";
    $arRecord[$inCount]['saldoempenhado']   = "";
    $arRecord[$inCount]['liquidado']        = "";
    $arRecord[$inCount]['aliquidar']        = "";
    $arRecord[$inCount]['pago']             = "";
    $arRecord[$inCount]['empenhadoapagar']  = "";
    $arRecord[$inCount]['liquidadoapagar']  = "";
    $arRecord[$inCount]['cod_recurso']      = "";
    $arRecord[$inCount]['entidade']         = "";

    $inCount++;

    //MONTA TOTALIZADOR GERAL
    $arRecord[$inCount]['nivel']            = 2;
    $arRecord[$inCount]['empenho']          = "";
    $arRecord[$inCount]['emissao ']         = "";
    $arRecord[$inCount]['credor']           = "TOTAL";
    $arRecord[$inCount]['empenhado']        = number_format( $inTotalEmpenhado, 2, ',', '.' );
    $arRecord[$inCount]['anulado']          = number_format( $inTotalAnulado, 2, ',', '.' );
    $arRecord[$inCount]['saldoempenhado']   = number_format( $inTotalSaldoEmpenhado, 2, ',', '.' );
    $arRecord[$inCount]['liquidado']        = number_format( $inTotalLiquidado, 2, ',', '.' );
    $arRecord[$inCount]['aliquidar']        = number_format( $inTotalALiquidar, 2, ',', '.' );
    $arRecord[$inCount]['pago']             = number_format( $inTotalPago, 2, ',', '.' );
    $arRecord[$inCount]['empenhadoapagar']  = number_format( $inTotalEmpenhadoAPagar, 2, ',', '.' );
    $arRecord[$inCount]['liquidadoapagar']  = number_format( $inTotalLiquidadoAPagar,2, ',', '.' );

    $inCount++;
    }

    $rsRecordSet = new RecordSet;
    $rsRecordSet->preenche( $arRecord );

    return $obErro;
}

}
