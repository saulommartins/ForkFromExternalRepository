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
* Classe de regra de negócio para RFolhaPagamentoPeriodoContratoServidor
* Data de Criação: 09/11/2005

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Diego Lemos de Souza

* @package URBEM
* @subpackage Regra de Negócio

$Revision: 30566 $
$Name$
$Author: souzadl $
$Date: 2007-06-05 17:06:51 -0300 (Ter, 05 Jun 2007) $

* Casos de uso: uc-04.05.07
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalContratoServidor.class.php"                                  );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalServidor.class.php"                                          );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoRegistroEvento.class.php"                             );
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoContratoServidorPeriodo.class.php"                 );

class RFolhaPagamentoPeriodoContratoServidor extends RPessoalContratoServidor
{
/**
   * @access Private
   * @var Object
*/
var $obTransacao;
/**
   * @access Private
   * @var Integer
*/
var $inCodPeriodoMovimentacao;
/**
   * @access Private
   * @var Object
*/
var $roRFolhaPagamentoPeriodoMovimentacao;
/**
   * @access Private
   * @var Array
*/
var $arRFolhaPagamentoRegistroEvento;
/**
   * @access Private
   * @var Object
*/
var $roRFolhaPagamentoRegistroEvento;

/**
    * @access Public
    * @param Object $valor
*/
function setTransacao($valor) { $this->obTransacao                           = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodPeriodoMovimentacao($valor) { $this->inCodPeriodoMovimentacao              = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRORFolhaPagamentoPeriodoMovimentacao(&$valor) { $this->roRFolhaPagamentoPeriodoMovimentacao  = &$valor; }
/**
    * @access Public
    * @param Array $valor
*/
function setARRFolhaPagamentoRegistroEvento($valor) { $this->arRFolhaPagamentoRegistroEvento       = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRORFolhaPagamentoRegistroEvento($valor) { $this->roRFolhaPagamentoRegistroEvento       = $valor; }

/**
    * @access Public
    * @return Object
*/
function getTransacao() { return $this->obTransacao;                           }
/**
    * @access Public
    * @return Integer
*/
function getCodPeriodoMovimentacao() { return $this->inCodPeriodoMovimentacao;              }
/**
    * @access Public
    * @return Object
*/
function getRORFolhaPagamentoPeriodoMovimentacao() { return $this->roRFolhaPagamentoPeriodoMovimentacao;  }
/**
    * @access Public
    * @return Array
*/
function getARRFolhaPagamentoRegistroEvento() { return $this->arRFolhaPagamentoRegistroEvento;       }
/**
    * @access Public
    * @return Object
*/
function getRORFolhaPagamentoRegistroEvento() { return $this->roRFolhaPagamentoRegistroEvento;       }

/**
     * Método construtor
     * @access Private
*/
function RFolhaPagamentoPeriodoContratoServidor(&$obRFolhaPagamentoPeriodoMovimentacao)
{
    parent::RPessoalContratoServidor                ( new RPessoalServidor                      );
    $this->setARRFolhaPagamentoRegistroEvento       ( array()                                   );
    $this->setRORFolhaPagamentoPeriodoMovimentacao  ( $obRFolhaPagamentoPeriodoMovimentacao     );
}

/**
    * Método addRFolhaPagamentoRegistroEvento
    * @access Public
*/
function addRFolhaPagamentoRegistroEvento()
{
    $this->arRFolhaPagamentoRegistroEvento[] = new RFolhaPagamentoRegistroEvento();
    $this->roRFolhaPagamentoRegistroEvento = &$this->arRFolhaPagamentoRegistroEvento[ count($this->arRFolhaPagamentoRegistroEvento)-1 ];
    $this->roRFolhaPagamentoRegistroEvento->setRORFolhaPagamentoPeriodoContratoServidor( $this );
}

/**
    * Método incluirPeriodoContratoServidor
    * @access Public
*/
function incluirPeriodoContratoServidor($boTransacao = "")
{
    $obTFolhaPagamentoContratoServidorPeriodo = new TFolhaPagamentoContratoServidorPeriodo;
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obErro = $this->listarPeriodoContratoServidor($rsPeriodoContratoServidor,$boTransacao);
    }
    if ( !$obErro->ocorreu() and $rsPeriodoContratoServidor->getNumLinhas() < 0 ) {
        $obTFolhaPagamentoContratoServidorPeriodo->setDado('cod_periodo_movimentacao',  $this->roRFolhaPagamentoPeriodoMovimentacao->getCodPeriodoMovimentacao());
        $obTFolhaPagamentoContratoServidorPeriodo->setDado('cod_contrato',              $this->getCodContrato()                                                 );
        $obErro = $obTFolhaPagamentoContratoServidorPeriodo->inclusao($boTransacao);
    }
    if ( !$obErro->ocorreu() ) {
        for ($inIndex=0;$inIndex<count($this->arRFolhaPagamentoRegistroEvento);$inIndex++) {
            $obRFolhaPagamentoRegistroEvento = $this->arRFolhaPagamentoRegistroEvento[$inIndex];
            $inCodEvento   = $obRFolhaPagamentoRegistroEvento->obRFolhaPagamentoEvento->getCodEvento();
            $inCodRegistro = $obRFolhaPagamentoRegistroEvento->getCodRegistroEvento();
            $stTimestamp   = $obRFolhaPagamentoRegistroEvento->getTimestamp();
            if ($inCodEvento != "" and $inCodRegistro != "") {
                $obErro = $this->roRFolhaPagamentoRegistroEvento->excluirUltimoRegistroEvento($inCodEvento,$inCodRegistro,$stTimestamp,$boTransacao);
            }
            if ( !$obErro->ocorreu() ) {
                $obErro = $obRFolhaPagamentoRegistroEvento->incluirRegistroEvento( $boTransacao );
            }
            if ( $obErro->ocorreu() ) {
                break;
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTFolhaPagamentoContratoServidorPeriodo );

    return $obErro;
}

/**
    * Método excluirPeriodoContratoServidor
    * @access Public
*/
function excluirPeriodoContratoServidor($boTransacao = "")
{
    $obTFolhaPagamentoContratoServidorPeriodo = new TFolhaPagamentoContratoServidorPeriodo;
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        for ($inIndex=0;$inIndex<count($this->arRFolhaPagamentoRegistroEvento);$inIndex++) {
            $obRFolhaPagamentoRegistroEvento = $this->arRFolhaPagamentoRegistroEvento[$inIndex];
            $obErro = $obRFolhaPagamentoRegistroEvento->excluirRegistroEvento( $boTransacao );
            if ( $obErro->ocorreu() ) {
                break;
            }
        }
    }
    if ( !$obErro->ocorreu() ) {
        $obTFolhaPagamentoContratoServidorPeriodo->setDado('cod_periodo_movimentacao',$this->roRFolhaPagamentoPeriodoMovimentacao->getCodPeriodoMovimentacao());
        $obTFolhaPagamentoContratoServidorPeriodo->setDado('cod_contrato',              $this->getCodContrato()                                                 );
        $obErro = $obTFolhaPagamentoContratoServidorPeriodo->exclusao($boTransacao);
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTFolhaPagamentoContratoServidorPeriodo );

    return $obErro;
}

/**
    * Método listar
    * @access Private
*/
function listar(&$rsRecordSet,$stFiltro,$stOrder,$boTransacao)
{
    $obTFolhaPagamentoContratoServidorPeriodo = new TFolhaPagamentoContratoServidorPeriodo;
    $obErro = $obTFolhaPagamentoContratoServidorPeriodo->recuperaTodos($rsRecordSet,$stFiltro,$stOrder,$boTransacao);

    return $obErro;
}

/**
    * Método listarPeriodoContratoServidor
    * @access Public
*/
function listarPeriodoContratoServidor(&$rsRecordSet,$boTransacao="")
{
    $stFiltro = "";
    $stOrder  = "";
    if ( $this->getCodContrato() ) {
        $stFiltro .= " AND cod_contrato = ".$this->getCodContrato();
    }
    if ( $this->roRFolhaPagamentoPeriodoMovimentacao->getCodPeriodoMovimentacao() ) {
        $stFiltro .= " AND cod_periodo_movimentacao = ".$this->roRFolhaPagamentoPeriodoMovimentacao->getCodPeriodoMovimentacao();
    }
    if ($stFiltro) {
        $stFiltro = " WHERE ".substr($stFiltro,4,strlen($stFiltro));
    }
    $obErro = $this->listar($rsRecordSet,$stFiltro,$stOrder,$boTransacao);

    return $obErro;
}

}
?>
