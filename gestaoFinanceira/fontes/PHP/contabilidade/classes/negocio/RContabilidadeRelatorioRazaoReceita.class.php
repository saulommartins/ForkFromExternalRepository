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
    * Classe de Regra para relatorio da razão da receita
    * Data de Criação   : 27/06/2005

    * @author Analista: Dieine da Silva
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @package URBEM
    * @subpackage Regra

    $Revision: 30668 $
    $Name$
    $Author: cleisson $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.02.30
*/

/*
$Log$
Revision 1.5  2006/07/05 20:50:26  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../bibliotecas/mascaras.lib.php';
include_once ( CLA_PERSISTENTE_RELATORIO."PersistenteRelatorio.class.php"        );
include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeRazaoReceita.class.php" );

/**
    * Classe de Regra para emissão de relatorio de razão
    * @author Analista: Dieine da Silva
    * @author Desenvolvedor: Anderson R. M. Buzo
*/
class RContabilidadeRelatorioRazaoReceita extends PersistenteRelatorio
{
/**
    * @var Object
    * @access Private
*/
var $obTContabilidadeRazaoReceita;
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
var $inCodReceita;
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
    * @var Integer
    * @access Private
*/
var $inMes;
/**
    * @var String
    * @access Private
*/
var $stData;
/**
    * @var String
    * @access Private
*/
var $stDemonstrar;

/**
     * @access Public
     * @param String $valor
*/
function setExercicio($valor) { $this->stExercicio     = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setCodEntidade($valor) { $this->stCodEntidade   = $valor; }
/**
     * @access Public
     * @param Integer $valor
*/
function setCodReceita($valor) { $this->inCodReceita    = $valor; }
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
     * @param String $valor
*/
function setData($valor) { $this->stData          = $valor; }
/**
     * @access Public
     * @param Integer $valor
*/
function setMes($valor) { $this->inMes           = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setDemonstrar($valor) { $this->stDemonstrar    = $valor; }

/**
     * @access Public
     * @return String
*/
function getExercicio() { return $this->stExercicio;               }
/**
     * @access Public
     * @return String
*/
function getCodEntidade() { return $this->stCodEntidade;             }
/**
     * @access Public
     * @return Integer
*/
function getCodReceita() { return $this->inCodReceita;               }
/**
     * @access Public
     * @return String
*/
function getDtInicial() { return $this->stDtInicial;               }
/**
     * @access Public
     * @return String
*/
function getDtFinal() { return $this->stDtFinal;                 }
/**
     * @access Public
     * @return String
*/
function getData() { return $this->stData;                    }
/**
     * @access Public
     * @return Integer
*/
function getMes() { return $this->inMes;                     }
/**
     * @access Public
     * @return String
*/
function getDemonstrar() { return $this->stDemonstrar;              }

/**
    * Método Construtor
    * @access Private
*/
function RContabilidadeRelatorioRazaoReceita()
{
    $this->obTContabilidadeRazaoReceita = new TContabilidadeRazaoReceita();
}

/**
    * Método abstrato
    * @access Public
*/
function geraRecordSet(&$rsRecordSet , $stOrder = "")
{
    $stFiltro = "";
    if( $this->stExercicio )
        $stFiltro .= " AND ORE.exercicio = '".$this->stExercicio."' ";
    if( $this->stCodEntidade )
        $stFiltro .= " AND ORE.cod_entidade IN ( ".$this->stCodEntidade." ) ";
    if( $this->inCodReceita )
        $stFiltro .= " AND ORE.cod_receita = ".$this->inCodReceita." ";
    if( $this->stDtInicial and !$this->stDtFinal )
        $stFiltro .= " AND CLO.dt_lote >= TO_DATE( '".$this->stDtInicial."', 'dd/mm/yyyy' ) ";
    if( $this->stDtFinal and !$this->stDtInicial )
        $stFiltro .= " AND CLO.dt_lote <= TO_DATE( '".$this->stDtFinal."', 'dd/mm/yyyy' ) ";
    if( $this->stDtInicial and $this->stDtFinal )
        $stFiltro .= " AND CLO.dt_lote BETWEEN TO_DATE( '".$this->stDtInicial."', 'dd/mm/yyyy' ) AND TO_DATE( '".$this->stDtFinal."', 'dd/mm/yyyy' ) ";
    if( $this->stData )
        $stFiltro .= " AND CLO.dt_lote = TO_DATE( '".$this->stData."', 'dd/mm/yyyy' ) ";
    if( $this->inMes AND $this->stExercicio )
        $stFiltro .= " AND TO_CHAR( CLO.dt_lote, 'mm/yyyy' ) = '".str_pad($this->inMes,2,'0',STR_PAD_LEFT)."/".$this->stExercicio."' ";

    if ($this->stDemonstrar == 'arrecadado') {
        $stFiltro .= " AND CLR.estorno = false ";
    } elseif ($this->stDemonstrar == 'estornado') {
        $stFiltro .= " AND CLR.estorno = true ";
    }

    $stOrder = " ORDER BY ORE.cod_receita, CLO.dt_lote ";
    $obErro = $this->obTContabilidadeRazaoReceita->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder );

//    $this->obTContabilidadeRazaoReceita->debug(); die;

    $arRecordSet = array();
    $inCount = 0;

    while ( !$rsRecordSet->eof() ) {

        if ( $rsRecordSet->getCampo( "cod_receita" ) != $inCodReceitaOld ) {

            if ($inCount > 0) {
                $nuVlReceitaPeriodo = bcadd( $nuVlReceitaAnterior, ( $nuVlReceitaPeriodo * -1 ), 4 );
                $arRecordSet[$inCount]['data'] = '';
                $inCount++;
                $arRecordSet[$inCount]['contra_partida'] = "Saldo atual da Receita:  ".number_format( $nuVlReceitaPeriodo, 2, ',', '.');
                $inCount++;
            }

            $nuVlReceitaPeriodo = 0;

            $arRecordSet[$inCount]['data']  = '';
            $inCount++;

            $arRecordSet[$inCount]['entidade']       = 'Código';
            $arRecordSet[$inCount]['data']           = 'Reduzido: '.$rsRecordSet->getCampo('cod_receita');
            $arRecordSet[$inCount]['historico']      = 'Código Estrutural: '.$rsRecordSet->getCampo('cod_estrutural');

            $stContraPartida = 'Descrição: '.$rsRecordSet->getCampo('nom_conta');
            $stContraPartida = str_replace( chr(10), "", $stContraPartida );
            $stContraPartida = wordwrap( $stContraPartida, 72, chr(13) );
            $arContraPartida = explode( chr(13), $stContraPartida );
            foreach ($arContraPartida as $stContraPartida) {
                $arRecordSet[$inCount]['contra_partida'] = $stContraPartida;
                $inCount++;
            }
            $arRecordSet[$inCount]['contra_partida'] = 'Saldo Anterior da Receita: ';

            if ($this->stDtInicial) {
                $stDtFinal   = $this->stDtInicial;
                $arData      = explode( '/', $this->stDtInicial );
                $stExercicio = $arData[2];
            } elseif ($this->stData) {
                $stDtFinal   = $this->stData;
                $arData      = explode( '/', $this->stData );
                $stExercicio = $arData[2];
            } elseif ($this->inMes AND $this->stExercicio) {
                $stExercicio = $this->stExercicio;
                $stDtFinal   = date("d/m/Y", mktime( 0,0,0,$this->inMes,1-1,$stExercicio ) );
            }

            if ( substr( $stDtFinal, 0, 5 ) != '01/01' ) {
                 // Recupera Valor do Saldo Anterior da Receita
                $this->obTContabilidadeRazaoReceita->setDado( "exercicio"   , $stExercicio                             );
                $this->obTContabilidadeRazaoReceita->setDado( "cod_entidade", $rsRecordSet->getCampo( "cod_entidade" ) );
                $this->obTContabilidadeRazaoReceita->setDado( "cod_receita" , $rsRecordSet->getCampo( "cod_receita"  ) );
                $this->obTContabilidadeRazaoReceita->setDado( "dt_inicial"  , '01/01/'.$stExercicio                    );
                $this->obTContabilidadeRazaoReceita->setDado( "dt_final"    , $stDtFinal                               );
                $obErro = $this->obTContabilidadeRazaoReceita->recuperaValorReceita( $rsReceita );
                if ( !$obErro->ocorreu() ) {
                    $arRecordSet[$inCount]['contra_partida'] .= number_format($rsReceita->getCampo( "vl_receita" ),2,',','.' );
                    $nuVlReceitaAnterior = $rsReceita->getCampo( "vl_receita" );
                }
            } else {
                $arRecordSet[$inCount]['contra_partida'] .= number_format(0,2,',','.' );
                $nuVlReceitaAnterior = 0;
            }

            $inCount++;
            $arRecordSet[$inCount]['data']           = '';
            $inCount++;
        }

        if ( ( $rsRecordSet->getCampo('tipo_valor') == 'C' and  $rsRecordSet->getCampo( "estorno" ) == 'f' ) or $rsRecordSet->getCampo('tipo_valor') == 'D' and  $rsRecordSet->getCampo( "estorno" ) == 't' ) {

            $arRecordSet[$inCount]['entidade']       = $rsRecordSet->getCampo('cod_entidade');
            $arRecordSet[$inCount]['data']           = $rsRecordSet->getCampo('dt_lote');
            $arRecordSet[$inCount]['historico']      = $rsRecordSet->getCampo('nom_historico');
            $arRecordSet[$inCount]['complemento']    = $rsRecordSet->getCampo('complemento');

            $stContraPartida = str_replace( chr(10), "", $rsRecordSet->getCampo('contra_partida') );
            $stContraPartida = wordwrap( $stContraPartida, 72, chr(13) );
            $arContraPartida = explode( chr(13), $stContraPartida );
            foreach ($arContraPartida as $stContraPartida) {
                $arRecordSet[$inCount]['contra_partida'] = $stContraPartida;
                $inCount++;
            }
            $inCount--;

            if ( $rsRecordSet->getCampo('tipo_valor') == 'C' ) {
                $nuVlLancamento = $rsRecordSet->getCampo('vl_lancamento')*(-1);
                if ( $rsRecordSet->getCampo( "estorno" ) == 'f' ) {
                    $nuVlReceitaPeriodo = bcadd( $nuVlReceitaPeriodo, $rsRecordSet->getCampo('vl_lancamento'), 4 );
                }
                $arRecordSet[$inCount]['valor']          = number_format($nuVlLancamento,2,',','.').'C';
            } else {
                $arRecordSet[$inCount]['valor']          = number_format($rsRecordSet->getCampo('vl_lancamento'),2,',','.').'  ';
                if ( $rsRecordSet->getCampo( "estorno" ) == 't' ) {
                    $nuVlReceitaPeriodo = bcadd( $nuVlReceitaPeriodo, $rsRecordSet->getCampo('vl_lancamento'), 4 );
                }
            }
            $inCount++;
        }
        $inCodReceitaOld = $rsRecordSet->getCampo('cod_receita');
        $rsRecordSet->proximo();
    }

    $rsRecordSet = new RecordSet;
    $rsRecordSet->preenche( $arRecordSet );

    return $obErro;
}

}
