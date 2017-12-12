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
    * Classe de regra de relatório para Extrato de Recurso
    * Data de Criação   : 04/07/2005

    * @author Analista: Dieine da Silva
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @package URBEM
    * @subpackage Regra

    $Revision: 30668 $
    $Name$
    $Autor:$
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.01.29
*/

/*
$Log$
Revision 1.6  2006/07/05 20:42:11  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE_RELATORIO                                     );
include_once( CAM_GF_ORC_MAPEAMENTO."TOrcamentoExtratoRecurso.class.php"    );
include_once( CAM_FW_PDF."RRelatorio.class.php"                             );

/**
    * Classe de regra de negócio

    * @author Analista: Dieine da Silva
    * @author Desenvolvedor: Anderson R. M. Buzo
*/
class ROrcamentoRelatorioExtratoRecurso extends PersistenteRelatorio
{
/**
    * @var Object
    * @access Private
*/
var $obTOrcamentoExtratoRecurso;
/**
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
    * @var Integer
    * @access Private
*/
var $inCodRecurso;
/**
    * @var String
    * @access Private
*/
var $stDtInicial;
/**
    * @var String
    * @access Private
*/
var $stDtFinal;

/**
     * @access Public
     * @param String $valor
*/
function setExercicio($valor) { $this->stExercicio    = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setCodEntidade($valor) { $this->stCodEntidade  = $valor; }
/**
     * @access Public
     * @param Integer $valor
*/
function setCodRecurso($valor) { $this->inCodRecurso    = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setDtInicial($valor) { $this->stDtInicial     = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setDtFinal($valor) { $this->stDtFinal       = $valor; }

/**
     * @access Public
     * @return String
*/
function getExercicio() { return $this->stExercicio;            }
/**
     * @access Public
     * @return String
*/
function getCodEntidade() { return $this->stCodEntidade;          }
/**
     * @access Public
     * @return Integer
*/
function getCodRecurso() { return $this->inCodRecurso;           }
/**
     * @access Public
     * @return String
*/
function getDtInicial() { return $this->stDtInicial;            }
/**
     * @access Public
     * @return String
*/
function getDtFinal() { return $this->stDtFinal;              }

/**
    * Método Construtor
    * @access Private
*/
function ROrcamentoRelatorioExtratoRecurso()
{
    $this->obRRelatorio               = new RRelatorio;
    $this->obTOrcamentoExtratoRecurso = new TOrcamentoExtratoRecurso;
}

/**
    * Método abstrato
    * @access Public
*/
function geraRecordSet(&$rsRecordSet , $stOrder = "")
{
    list( $stDia, $stMes, $stExercicio) = explode( '/', $this->stDtInicial );
    $this->obTOrcamentoExtratoRecurso->setDado( 'exercicio'   , $this->stExercicio    );
    $this->obTOrcamentoExtratoRecurso->setDado( 'cod_entidade', $this->stCodEntidade  );
    $this->obTOrcamentoExtratoRecurso->setDado( 'dt_inicial'  , '01/01/'.$stExercicio );
    if ($stMes.$stDia == '0101') {
        $this->obTOrcamentoExtratoRecurso->setDado( 'dt_final'    , date('d/m/Y', mktime(0,0,0,$stMes,$stDia,$stExercicio ) ) );
    } else {
        $this->obTOrcamentoExtratoRecurso->setDado( 'dt_final'    , date('d/m/Y', mktime(0,0,0,$stMes,$stDia-1,$stExercicio ) ) );
    }

    $stFiltro = "";
    if( $this->inCodRecurso )
        $stFiltro .= " ORE.cod_recurso = ".$this->inCodRecurso." AND ";
    if( $stExercicio )
        $stFiltro .= " CVL.exercicio = '".$this->stExercicio."' AND ";

    $stFiltro = ( $stFiltro ) ? " AND ".substr( $stFiltro, 0, strlen($stFiltro)-4 ) : '';
    $stOrder = " GROUP BY CVL.exercicio, CPR.cod_recurso ORDER BY CVL.exercicio, CPR.cod_recurso ";
    $obErro = $this->obTOrcamentoExtratoRecurso->recuperaSaldoAnteriorRecurso( $rsSaldoAnterior, $stFiltro, $stOrder );
    if ( !$obErro->ocorreu() ) {
        while ( !$rsSaldoAnterior->eof() ) {
            $arSaldoAnterior[$rsSaldoAnterior->getCampo( 'cod_recurso' )] = $rsSaldoAnterior->getCampo( 'saldo' );
            $rsSaldoAnterior->proximo();
        }
    }

    if ( !$obErro->ocorreu() ) {

        $this->obTOrcamentoExtratoRecurso->setDado( 'dt_inicial', $this->stDtInicial );
        $this->obTOrcamentoExtratoRecurso->setDado( 'dt_final'  , $this->stDtFinal   );

        $stFiltro = "";
        if( $this->inCodRecurso )
            $stFiltro .= " ORE.cod_recurso = ".$this->inCodRecurso." AND ";
        if( $stExercicio )
            $stFiltro .= " ORE.exercicio = '".$this->stExercicio."' AND ";

        $stFiltro = ( $stFiltro ) ? " WHERE ".substr( $stFiltro, 0, strlen($stFiltro)-4 ) : '';
        $stOrder = " ORDER BY ORE.exercicio, ORE.cod_recurso ";

        $obErro = $this->obTOrcamentoExtratoRecurso->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder );

        if ( !$obErro->ocorreu() ) {

            $inCount = 0;
            while ( !$rsRecordSet->eof() ) {
                $arRecordSet[$inCount]['cod_recurso']    = $rsRecordSet->getCampo( 'cod_recurso'    );
                $arRecordSet[$inCount]['nom_recurso']    = $rsRecordSet->getCampo( 'nom_recurso'    );

                $nuSaldoAnterior = $arSaldoAnterior[$rsRecordSet->getCampo( 'cod_recurso' )];
                $nuSaldoAnterior = ( $nuSaldoAnterior > 0 ) ? $nuSaldoAnterior : '0.00';
                $arRecordSet[$inCount]['saldo_anterior'] = $nuSaldoAnterior;

                $arRecordSet[$inCount]['arrecadado']     = $rsRecordSet->getCampo( 'saldo' );

                $arRecordSet[$inCount]['pago']           = bcsub( $rsRecordSet->getCampo( 'vl_pago')   , $rsRecordSet->getCampo('vl_anulado')   , 4 );
                $arRecordSet[$inCount]['pago_rp']        = bcsub( $rsRecordSet->getCampo( 'vl_pago_rp'), $rsRecordSet->getCampo('vl_anulado_rp'), 4 );
                $arRecordSet[$inCount]['sub_total']      = bcadd( $arRecordSet[$inCount]['saldo_anterior'], bcsub( $arRecordSet[$inCount]['arrecadado'], bcadd( $arRecordSet[$inCount]['pago'], $arRecordSet[$inCount]['pago_rp'], 4 ), 4 ), 4 );
                $arRecordSet[$inCount]['saldo_atual']    = bcadd( $arRecordSet[$inCount]['saldo_anterior'], $arRecordSet['arrecadado'], 4 );
                $arRecordSet[$inCount]['diferenca']      = "";

                $inCount++;
                $rsRecordSet->proximo();
            }
        $rsRecordSet = new RecordSet;
        $rsRecordSet->preenche( $arRecordSet );
        $rsRecordSet->addFormatacao( 'saldo_anterior', 'NUMERIC_BR' );
        $rsRecordSet->addFormatacao( 'arrecadado'    , 'NUMERIC_BR' );
        $rsRecordSet->addFormatacao( 'pago'          , 'NUMERIC_BR' );
        $rsRecordSet->addFormatacao( 'pago_rp'       , 'NUMERIC_BR' );
        $rsRecordSet->addFormatacao( 'sub_total'     , 'NUMERIC_BR' );
        $rsRecordSet->addFormatacao( 'saldo_atual'   , 'NUMERIC_BR' );
        }
    }

    return $obErro;
}

}
