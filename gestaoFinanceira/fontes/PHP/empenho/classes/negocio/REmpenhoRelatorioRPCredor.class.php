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
    * Classe de Regra do Relatório de Balancete de Receita
    * Data de Criação   : 23/02/2005

    * @author Desenvolvedor: Rafael Almeida

    * @package URBEM
    * @subpackage Regra

    $Revision: 30805 $
    $Name$
    $Author: cako $
    $Date: 2007-12-05 15:12:56 -0200 (Qua, 05 Dez 2007) $

    * Casos de uso : uc-02.03.10
*/

/*
$Log$
Revision 1.9  2006/07/05 20:47:06  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE_RELATORIO        );
include_once( CAM_GF_EMP_NEGOCIO."REmpenhoEmpenho.class.php");
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoUnidadeOrcamentaria.class.php"             );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoOrgaoOrcamentario.class.php"             );
include_once( CAM_FW_PDF."RRelatorio.class.php"           );

/**
    * Classe de Regra de Negócios Empenho Empenhado, Pago ou Liquidado
    * @author Desenvolvedor: Lucas Leusin Oaigen
*/
class REmpenhoRelatorioRPCredor extends PersistenteRelatorio
{
/**
    * @var Object
    * @access Private
*/
var $obREntidade;
/**
    * @var Integer
    * @access Private
*/
var $inCodEntidade;
/**
    * @var Integer
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
var $inOrgao;
/**
    * @var Integer
    * @access Private
*/
var $inUnidade;
/**
    * @var Integer
    * @access Private
*/
var $stCodElementoDespesa;
/**
    * @var String
    * @access Private
*/
var $inRecurso;
/**
    * @var Integer
    * @access Private
*/
var $inSituacao;
/**
    * @var Integer
    * @access Private
*/
var $inExercicio;
/**
    * @var Integer
    * @access Private
*/
var $stFiltro;
/**
    * @var Integer
    * @access Private
*/
var $inFuncao;
/**
    * @var Integer
    * @access Private
*/
var $inSubFuncao;
/**
    * @var Integer
    * @access Private
*/
var $inOrdem;
/**
    * @var Integer
    * @access Private
*/
var $inCodModulo;
/**
     * @access Public
     * @param Object $valor
*/
function setCodEntidade($valor) { $this->inCodEntidade      = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setDataInicial($valor) { $this->stDataInicial     = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setDataFinal($valor) { $this->stDataFinal      = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setOrgao($valor) { $this->inOrgao        = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setUnidade($valor) { $this->inUnidade        = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setCodElementoDespesa($valor) { $this->stCodElementoDespesa      = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setRecurso($valor) { $this->inRecurso        = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setSituacao($valor) { $this->inSituacao        = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setExercicio($valor) { $this->inExercicio        = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setFiltro($valor) { $this->stFiltro           = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setFuncao($valor) { $this->inFuncao        = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setSubFuncao($valor) { $this->inSubFuncao        = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setOrdem($valor) { $this->inOrdem            = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setModulo($valor) { $this->inCodModulo            = $valor; }
/**
     * @access Public
     * @return Object
*/
function getCodEntidade() { return $this->inCodEntidade;                }
/**
     * @access Public
     * @return Object
*/
function getDataInicial() { return $this->stDataInicial;                }
/**
     * @access Public
     * @return Object
*/
function getDataFinal() { return $this->stDataFinal;                }
/**
     * @access Public
     * @return Object
*/
function getOrgao() { return $this->inOrgao;                  }
/**
     * @access Public
     * @return Object
*/
function getUnidade() { return $this->inUnidade;                  }
/**
     * @access Public
     * @return Object
*/
function getCodElementoDespesa() { return $this->stCodElementoDespesa;                }
/**
     * @access Public
     * @return Object
*/
function getRecurso() { return $this->inRecurso;                  }
/**
     * @access Public
     * @return Object
*/
function getSituacao() { return $this->inSituacao;                  }
/**
     * @access Public
     * @return Object
*/
function getExercicio() { return $this->inExercicio;                  }
/**
     * @access Public
     * @return Object
*/
function getFiltro() { return $this->stFiltro;                     }
/**
     * @access Public
     * @return Object
*/
function getFuncao() { return $this->inFuncao;                     }
/**
     * @access Public
     * @return Object
*/
function getSubFuncao() { return $this->inSubFuncao;               }
/**
     * @access Public
     * @return Object
*/
function getOrdem() { return $this->inOrdem;                   }
/**
     * @access Public
     * @return Object
*/
function getModulo() { return $this->inCodModulo;                   }
/**
    * Método Construtor
    * @access Private
*/
function REmpenhoRelatorioRPCredor()
{
    $this->obROrcamentoUnidadeOrcamentaria       = new ROrcamentoUnidadeOrcamentaria;
    $this->obROrcamentoOrgao            = new ROrcamentoOrgaoOrcamentario;

    $this->obRRelatorio                  = new RRelatorio;
    $this->obREmpenhoEmpenho            = new REmpenhoEmpenho;
    $this->obREmpenhoEmpenho->obROrcamentoEntidade->obRCGM->setNumCGM     ( Sessao::read('numCgm') );

}

/**
    * Método abstrato
    * @access Public
*/
function geraRecordSet(&$rsRecordSet , $stOrder = "")
{
    include_once( CAM_GF_EMP_MAPEAMENTO."FEmpenhoRPCredor.class.php" );
    $obFEmpenhoRPCredor              = new FEmpenhoRPCredor;

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
    $obFEmpenhoRPCredor->setDado("exercicio",         $this->getExercicio());
    $obFEmpenhoRPCredor->setDado("stFiltro",          $this->getFiltro());
    $obFEmpenhoRPCredor->setDado("stEntidade",        $this->getCodEntidade());
    $obFEmpenhoRPCredor->setDado("stDataInicial",       $this->getDataInicial());
    $obFEmpenhoRPCredor->setDado("stDataFinal",       $this->getDataFinal());
    $obFEmpenhoRPCredor->setDado("inCodEmpenhoInicial",$this->obREmpenhoEmpenho->getCodEmpenhoInicial());
    $obFEmpenhoRPCredor->setDado("inCodEmpenhoFinal", $this->obREmpenhoEmpenho->getCodEmpenhoFinal());
    $obFEmpenhoRPCredor->setDado("inOrgao",           $this->obROrcamentoOrgao->getNumeroOrgao());
    $obFEmpenhoRPCredor->setDado("inUnidade",         $this->obROrcamentoUnidadeOrcamentaria->getNumeroUnidade());
    $obFEmpenhoRPCredor->setDado("stElementoDespesa", $this->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->getCodEstrutural() );
    $obFEmpenhoRPCredor->setDado("inRecurso",         $this->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoRecurso->getCodRecurso());
    $obFEmpenhoRPCredor->setDado("stDestinacaoRecurso", $this->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoRecurso->getDestinacaoRecurso());
    $obFEmpenhoRPCredor->setDado("inCodDetalhamento", $this->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoRecurso->getCodDetalhamento());
    $obFEmpenhoRPCredor->setDado("inCGM",             $this->obREmpenhoEmpenho->obROrcamentoEntidade->obRCGM->getNumCGM());
    $obFEmpenhoRPCredor->setDado("inFuncao",          $this->getFuncao());
    $obFEmpenhoRPCredor->setDado("inSubFuncao",       $this->getSubFuncao());
    $obFEmpenhoRPCredor->setDado("inOrdem",           $this->getOrdem());
    $obFEmpenhoRPCredor->setDado("inCodModulo",       '8');
    $obFEmpenhoRPCredor->recuperaMascara( $rsMascara );
    $obFEmpenhoRPCredor->setDado("stMascara",         $rsMascara->getCampo('masc_despesa'));

    $stOrder = "";
    $obErro = $obFEmpenhoRPCredor->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder );

    $inCount            = 0;
    $inTotal            = 0;
    $arRecord           = array();
    $dtAtual            = "";
    $mostra             = true;
    $inNumCgm = 0;
    while ( !$rsRecordSet->eof() ) {

        $stData = $rsRecordSet->getCampo('data_empenho');
        $inCgm  = $rsRecordSet->getCampo('cgm');
        $arRecord[$inCount]['ordem']          = $rsRecordSet->getCampo('ordem');
        $arRecord[$inCount]['dotacao']        = $rsRecordSet->getCampo('dotacao');
        $arRecord[$inCount]['empenho']        = $rsRecordSet->getCampo('empenho');
//        $arRecord[$inCount]['empenho']        = $rsRecordSet->getCampo('empenho');
        $arRecord[$inCount]['razao_social']   = $rsRecordSet->getCampo('cgm') ." ". $rsRecordSet->getCampo('razao_social');
        $arRecord[$inCount]['data_empenho']   = $rsRecordSet->getCampo('data_empenho');
        $arRecord[$inCount]['data_vencimento']= $rsRecordSet->getCampo('data_vencimento');
        $arRecord[$inCount]['empenhado']      = number_format( $rsRecordSet->getCampo('vl_empenhado'),2,',','.');
        $arRecord[$inCount]['liquidado']      = number_format( $rsRecordSet->getCampo('vl_liquidado'),2,',','.');
        $arRecord[$inCount]['anulado']        = number_format( $rsRecordSet->getCampo('vl_anulado'),2,',','.');
        $arRecord[$inCount]['pago']           = number_format( $rsRecordSet->getCampo('vl_empenhado_pago'),2,',','.');
        $arRecord[$inCount]['apagar']         = number_format( $rsRecordSet->getCampo('vl_apagar'),2,',','.');

        $stTotalLiquidosApagarCredor = $stTotalLiquidosApagarCredor + ( $rsRecordSet->getCampo('vl_liquidado') - $rsRecordSet->getCampo('vl_empenhado_pago') );
        $stTotalRestosApagarCredor   = $stTotalRestosApagarCredor + $rsRecordSet->getCampo('vl_apagar');

        $rsRecordSet->proximo();
        $inCgmProximo = $rsRecordSet->getCampo('cgm');
        $stDataProximo = $rsRecordSet->getCampo('data_empenho');
        $rsRecordSet->anterior();
        if ( ($inCgm != $inCgmProximo) || ($stData != $stDataProximo) ) {
            $inCount++;
            $arRecord[$inCount]['data']           = "";
            $arRecord[$inCount]['ordem']          = "";
            $arRecord[$inCount]['dotacao']        = "";
            $arRecord[$inCount]['razao_social']   = "Total de liquidados a pagar: ";
            $arRecord[$inCount]['data_empenho']   = "";
            $arRecord[$inCount]['data_vencimento']= "";
            $arRecord[$inCount]['empenhado']      = "";
            $arRecord[$inCount]['liquidado']      = "";
            $arRecord[$inCount]['anulado']        = "";
            $arRecord[$inCount]['pago']           = "";
            $arRecord[$inCount]['apagar']         = number_format($stTotalLiquidosApagarCredor,2,',','.');
            $inCount++;
            $arRecord[$inCount]['data']           = "";
            $arRecord[$inCount]['ordem']          = "";
            $arRecord[$inCount]['dotacao']        = "";
            $arRecord[$inCount]['razao_social']   = "Total de restos a pagar: ";
            $arRecord[$inCount]['data_empenho']   = "";
            $arRecord[$inCount]['data_vencimento']= "";
            $arRecord[$inCount]['empenhado']      = "";
            $arRecord[$inCount]['liquidado']      = "";
            $arRecord[$inCount]['anulado']        = "";
            $arRecord[$inCount]['pago']           = "";
            $arRecord[$inCount]['apagar']         = number_format($stTotalRestosApagarCredor,2,',','.');
            $inCount++;
            $arRecord[$inCount]['data']           = "";
            $arRecord[$inCount]['ordem']          = "";
            $arRecord[$inCount]['empenho']        = "";
            $arRecord[$inCount]['dotacao']        = "";
            $arRecord[$inCount]['razao_social']   = "";
            $arRecord[$inCount]['data_empenho']   = "";
            $arRecord[$inCount]['data_vencimento']= "";
            $arRecord[$inCount]['empenhado']      = "";
            $arRecord[$inCount]['liquidado']      = "";
            $arRecord[$inCount]['anulado']        = "";
            $arRecord[$inCount]['pago']           = "";
            $arRecord[$inCount]['apagar']         = "";

            $stTotalLiquidosApagarCredor = 0;
            $stTotalRestosApagarCredor   = 0;

        }

        $stTotalGeralLiquidosApagar = $stTotalGeralLiquidosApagar + ( $rsRecordSet->getCampo('vl_liquidado') - $rsRecordSet->getCampo('vl_empenhado_pago') );
        $stTotalGeralRestosApagar   = $stTotalGeralRestosApagar + $rsRecordSet->getCampo('vl_apagar');
        $inCount++;
        $rsRecordSet->proximo();

    }

    $arRecord[$inCount]['data']           = "";
    $arRecord[$inCount]['ordem']          = "";
    $arRecord[$inCount]['dotacao']        = "";
    $arRecord[$inCount]['razao_social']   = "Total Geral de liquidados a pagar: ";
    $arRecord[$inCount]['data_empenho']   = "";
    $arRecord[$inCount]['data_vencimento']= "";
    $arRecord[$inCount]['empenhado']      = "";
    $arRecord[$inCount]['liquidado']      = "";
    $arRecord[$inCount]['anulado']        = "";
    $arRecord[$inCount]['pago']           = "";
    $arRecord[$inCount]['apagar']         = number_format($stTotalGeralLiquidosApagar,2,',','.');
    $inCount++;

    $arRecord[$inCount]['data']           = "";
    $arRecord[$inCount]['ordem']          = "";
    $arRecord[$inCount]['dotacao']        = "";
    $arRecord[$inCount]['razao_social']   = "Total Geral de restos a pagar: ";
    $arRecord[$inCount]['data_empenho']   = "";
    $arRecord[$inCount]['data_vencimento']= "";
    $arRecord[$inCount]['empenhado']      = "";
    $arRecord[$inCount]['liquidado']      = "";
    $arRecord[$inCount]['anulado']        = "";
    $arRecord[$inCount]['pago']           = "";
    $arRecord[$inCount]['apagar']         = number_format($stTotalGeralRestosApagar,2,',','.');

/*    //MONTA TOTALIZADOR GERAL
    $arRecord[$inCount]['nivel']             = 2;
    $arRecord[$inCount]['empenho']           = "";
    $arRecord[$inCount]['cgm']               = "";
    $arRecord[$inCount]['razao_social']      = "";
    $arRecord[$inCount]['debito']            = "";
    $arRecord[$inCount]['credito']           = "";
    $arRecord[$inCount]['valor_previsto']    = number_format( $inTotalPrevisto, 2, ',', '.' );
    $arRecord[$inCount]['data']           = "";*/

    $rsRecordSet = new RecordSet;
    $rsRecordSet->preenche( $arRecord );

    return $obErro;

}

}
