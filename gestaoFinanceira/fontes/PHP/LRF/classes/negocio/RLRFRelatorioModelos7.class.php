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
    * Classe de Regra do Relatório de Modelos 7
    * Data de Criação   : 03/08/2005

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @package URBEM
    * @subpackage Regra

    $Revision: 30668 $
    $Name$
    $Author: cleisson $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso :uc-02.05.09
    * Casos de uso :uc-02.05.13
*/

/*
$Log$
Revision 1.3  2006/07/05 20:44:40  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE_RELATORIO          );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php"          );
include_once( CAM_GF_LRF_MAPEAMENTO."FLRFTCERSModelo7.class.php"                );

/**
    * Classe de Regra de Negócios Modelos Executivo
    * @author Desenvolvedor: Lucas Leusin Oaigen
*/
class RLRFRelatorioModelos7 extends PersistenteRelatorio
{
/**
    * @var Object
    * @access Private
*/
var $obFLRFLRFTCERSModelo7;
/*
    * @var String
    * @access Private
*/
var $stExercicio;
/**
    * @var String
    * @access Private
*/
var $stCodEntidade;
/**
    * @var String
    * @access Private
*/
var $stMes;

/**
    * @var Integer
    * @access Private
*/
function setFTECERSModelo7($valor) { $this->obFLRFTCERSModelo7  = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setCodEntidade($valor) { $this->stCodEntidade  = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setExercicio($valor) { $this->stExercicio    = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setMes($valor) { $this->stMes          = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setFiltro($valor) { $this->stFiltro          = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setCodModelo($valor) { $this->inCodModelo      = $valor; }

/**
     * @access Public
     * @param Object $valor
*/
function getFLRFLRFTCERSModelo7() { return $this->obFLRFLRFTCERSModelo7;   }
/**
     * @access Public
     * @param Object $valor
*/
function getCodEntidade() { return $this->stCodEntidade;   }
/**
     * @access Public
     * @return Object
*/
function getExercicio() { return $this->stExercicio;     }
/**
     * @access Public
     * @param String $valor
*/
function getMes() { return $this->stMes;         }
/**
     * @access Public
     * @param String $valor
*/
function getFiltro() { return $this->stFiltro;         }
/**
     * @access Public
     * @param Object $valor
*/
function getCodModelo() { return $this->inCodModelo;                     }

/**
     * @access Public
     * @return Object
*/
function RLRFRelatorioModelos7()
{
    $this->obFLRFTCERSModelo7 = new FLRFTCERSModelo7;
    $this->stMes           = $this->stMes + 1;
    $this->obROrcamentoEntidade          = new ROrcamentoEntidade;
    $this->obROrcamentoEntidade->obRCGM->setNumCGM     ( Sessao::read('numCgm') );
}

/**
    * Método abstrato
    * @access Public
*/
function geraRecordSet(&$rsRecordSet, $boTransacao = "")
{
    $this->obFLRFTCERSModelo7->setDado( 'exercicio'   , $this->stExercicio );
    $this->obFLRFTCERSModelo7->setDado( 'cod_entidade', $this->stCodEntidade );
    $this->obFLRFTCERSModelo7->setDado( 'dt_final'    , date( 'd/m/Y', mktime( 0, 0, 0, $this->stMes, -1, $this->stExercicio )  ) );

    $obErro = $this->obFLRFTCERSModelo7->recuperaTodos( $rsRecordSet, '', '', $boTransacao );

    if ( !$obErro->ocorreu() ) {

        $inCount = 0;
        while ( !$rsRecordSet->eof() ) {

            $arRecordSet[$inCount]['recurso']   = str_pad( $rsRecordSet->getCampo('cod_recurso'), 4, '0', STR_PAD_LEFT );

            $nuVlSaldoEmpenhado = 0;
            $nuVlSaldoLiquidado = 0;
            $nuVlSaldoPago      = 0;

            // totaliza valores de resto para o recurso
            $boRestos = false;
            $inCodRecursoOld = $rsRecordSet->getCampo( 'cod_recurso' );
            while (!$rsRecordSet->eof() and $rsRecordSet->getCampo('cod_recurso') == $inCodRecursoOld and $rsRecordSet->getCampo('exercicio') < $this->stExercicio) {

               $nuVlSaldoEmpenhado = bcadd($nuVlSaldoEmpenhado,bcsub($rsRecordSet->getCampo('vl_empenhado '),$rsRecordSet->getCampo('vl_anulado'           ),4),4);
               $nuVlSaldoLiquidado = bcadd($nuVlSaldoLiquidado,bcsub($rsRecordSet->getCampo('vl_liquidacao'),$rsRecordSet->getCampo('vl_liquidacao_anulado'),4),4);
               $nuVlSaldoPago      = bcadd($nuVlSaldoPago     ,bcsub($rsRecordSet->getCampo('vl_pago      '),$rsRecordSet->getCampo('vl_estornado'         ),4),4);

               $inCodRecursoOld = $rsRecordSet->getCampo( 'cod_recurso' );
               $boRestos        = true;
               $rsRecordSet->proximo();
            }

            // Gera Valores de resto
            $arRecordSet[$inCount]['liquidado_rp']   = bcsub( $nuVlSaldoLiquidado, $nuVlSaldoPago     , 4 );
            $arRecordSet[$inCount]['a_liquidar_rp']  = bcsub( $nuVlSaldoEmpenhado, $nuVlSaldoLiquidado, 4 );

            // Gera Valores do exercicio atual
            if ( ( $rsRecordSet->getCampo( 'cod_recurso' ) == $inCodRecursoOld and $boRestos ) or !$boRestos ) {
                $nuVlSaldoEmpenhado = bcsub( $rsRecordSet->getCampo( 'vl_empenhado'  ), $rsRecordSet->getCampo( 'vl_anulado'            ), 4 );
                $nuVlSaldoLiquidado = bcsub( $rsRecordSet->getCampo( 'vl_liquidacao' ), $rsRecordSet->getCampo( 'vl_liquidacao_anulado' ), 4 );
                $nuVlSaldoPago      = bcsub( $rsRecordSet->getCampo( 'vl_pago'       ), $rsRecordSet->getCampo( 'vl_estornado'          ), 4 );

                $arRecordSet[$inCount]['liquidado']             = bcsub( $nuVlSaldoLiquidado, $nuVlSaldoPago     , 4 );
                $arRecordSet[$inCount]['a_liquidar']            = bcsub( $nuVlSaldoEmpenhado, $nuVlSaldoLiquidado, 4 );
                $arRecordSet[$inCount]['saldo'     ]            = $rsRecordSet->getCampo( 'vl_saldo'          );
                $arRecordSet[$inCount]['lq_adicao_exclusao']    = $rsRecordSet->getCampo( 'vl_lq_ajustado'    );
                $arRecordSet[$inCount]['n_lq_adicao_exclusao']  = $rsRecordSet->getCampo( 'vl_n_lq_ajustado'  );
                $arRecordSet[$inCount]['saldo_adicao_exclusao'] = $rsRecordSet->getCampo( 'vl_saldo_ajustado' );

                $rsRecordSet->proximo();
            }

            // Monta Totais
            $arRecordSet[$inCount]['lq_ajustado']       = bcadd( $arRecordSet[$inCount]['liquidado']    , $arRecordSet[$inCount]['lq_adicao_exclusao']   , 4 );
            $arRecordSet[$inCount]['n_lq_ajustado']     = bcadd( $arRecordSet[$inCount]['a_liquidar']   , $arRecordSet[$inCount]['n_lq_adicao_exclusao'] , 4 );
            $arRecordSet[$inCount]['total_liq_ajust']   = bcadd( $arRecordSet[$inCount]['lq_ajustado']  , $arRecordSet[$inCount]['liquidado_rp']         , 4 );
            $arRecordSet[$inCount]['total_n_liq_ajust'] = bcadd( $arRecordSet[$inCount]['n_lq_ajustado'], $arRecordSet[$inCount]['a_liquidar_rp'] , 4 );
            $arRecordSet[$inCount]['total_saldo_ajust'] = bcadd( $arRecordSet[$inCount]['saldo'      ]  , $arRecordSet[$inCount]['saldo_adicao_exclusao'], 4 );

            $inCount++;
        }

        $rsRecordSet = new RecordSet;
        $rsRecordSet->preenche( $arRecordSet );

    }

    return $obErro;
}

}
