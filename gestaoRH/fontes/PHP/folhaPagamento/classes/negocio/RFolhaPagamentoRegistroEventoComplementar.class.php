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
    * Classe de regra de negócio para RFolhaPagamentoRegistroEventoComplementar
    * Data de Criação: 20/01/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Regra de Negócio

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-05 17:06:51 -0300 (Ter, 05 Jun 2007) $

    * Casos de uso: uc-04.05.08
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoEvento.class.php"                                    );

class RFolhaPagamentoRegistroEventoComplementar
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
var $inCodRegistro;
/**
   * @access Private
   * @var String
*/
var $stTimestamp;
/**
   * @access Private
   * @var Numeric
*/
var $nuValor;
/**
   * @access Private
   * @var Numeric
*/
var $nuQuantidade;
/**
   * @access Private
   * @var Integer
*/
var $inParcela;
/**
   * @access Private
   * @var Object
*/
var $roRFolhaPagamentoFolhaComplementar;
/**
   * @access Private
   * @var Object
*/
var $obRFolhaPagamentoEvento;

/**
    * @access Public
    * @param Object $valor
*/
function setTransacao($valor) { $this->obTransacao                         = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodRegistro($valor) { $this->inCodRegistro                       = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setTimestamp($valor) { $this->stTimestamp                         = $valor; }
/**
    * @access Public
    * @param Numeric $valor
*/
function setValor($valor) { $this->nuValor                             = $valor; }
/**
    * @access Public
    * @param Numeric $valor
*/
function setQuantidade($valor) { $this->nuQuantidade                        = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setParcela($valor) { $this->inParcela                           = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRORFolhaPagamentoFolhaComplementar(&$valor) { $this->roRFolhaPagamentoFolhaComplementar = &$valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRFolhaPagamentoEvento($valor) { $this->obRFolhaPagamentoEvento              = $valor; }

/**
    * @access Public
    * @return Object
*/
function getTransacao() { return $this->obTransacao;                         }
/**
    * @access Public
    * @return Integer
*/
function getCodRegistro() { return $this->inCodRegistro;                       }
/**
    * @access Public
    * @return String
*/
function getTimestamp() { return $this->stTimestamp;                         }
/**
    * @access Public
    * @return Numeric
*/
function getValor() { return $this->nuValor;                             }
/**
    * @access Public
    * @return Numeric
*/
function getQuantidade() { return $this->nuQuantidade;                        }
/**
    * @access Public
    * @return Integer
*/
function getParcela() { return $this->inParcela;                           }
/**
    * @access Public
    * @return Object
*/
function getRORFolhaPagamentoFolhaComplementar() { return $this->roRFolhaPagamentoFolhaComplementar;  }
/**
    * @access Public
    * @return Object
*/
function getRFolhaPagamentoEvento() { return $this->obRFolhaPagamentoEvento;              }

/**
     * Método construtor
     * @access Private
*/
function RFolhaPagamentoRegistroEventoComplementar(&$obRFolhaPagamentoFolhaComplementar)
{
    $this->setTransacao                         ( new Transacao                             );
    $this->setRFolhaPagamentoEvento             ( new RFolhaPagamentoEvento                 );
    $this->setRORFolhaPagamentoFolhaComplementar( $obRFolhaPagamentoFolhaComplementar       );
}

/**
    * incluiRegistroEventoComplementar
    * @access Public
*/
function incluirRegistroEventoComplementar($boTransacao="")
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoRegistroEventoComplementar.class.php"       );
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoRegistroEventoComplementarParcela.class.php");
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoUltimoRegistroEventoComplementar.class.php" );
    $obTFolhaPagamentoRegistroEventoComplementar        = new TFolhaPagamentoRegistroEventoComplementar;
    $obTFolhaPagamentoUltimoRegistroEventoComplementar  = new TFolhaPagamentoUltimoRegistroEventoComplementar;
    $obTFolhaPagamentoRegistroEventoComplementarParcela = new TFolhaPagamentoRegistroEventoComplementarParcela;
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if (!$obErro->ocorreu()) {
        if ( $this->getCodRegistro() == "" ) {
            $obErro = $obTFolhaPagamentoRegistroEventoComplementar->proximoCod($inCodRegistro,$boTransacao);
            $this->setCodRegistro($inCodRegistro);
        }
        if ( !$obErro->ocorreu() ) {
            $obErro = $this->excluirUltimoRegistroEventoComplementar($boTransacao);
        }
        if ( !$obErro->ocorreu() ) {
            $obTFolhaPagamentoRegistroEventoComplementar->setDado("cod_registro"            ,$this->getCodRegistro()   );
            $obTFolhaPagamentoRegistroEventoComplementar->setDado("cod_contrato"            ,$this->roRFolhaPagamentoFolhaComplementar->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoPeriodoContratoServidor->getCodContrato()   );
            $obTFolhaPagamentoRegistroEventoComplementar->setDado("cod_complementar"        ,$this->roRFolhaPagamentoFolhaComplementar->getCodComplementar());
            $obTFolhaPagamentoRegistroEventoComplementar->setDado("cod_periodo_movimentacao",$this->roRFolhaPagamentoFolhaComplementar->roRFolhaPagamentoPeriodoMovimentacao->getCodPeriodoMovimentacao());
            $obTFolhaPagamentoRegistroEventoComplementar->setDado("cod_evento"              ,$this->obRFolhaPagamentoEvento->getCodEvento());
            $obTFolhaPagamentoRegistroEventoComplementar->setDado("valor"                   ,$this->getValor());
            $obTFolhaPagamentoRegistroEventoComplementar->setDado("quantidade"              ,$this->getQuantidade());
            $obTFolhaPagamentoRegistroEventoComplementar->setDado("cod_configuracao"        ,$this->obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->getCodConfiguracao());
            $obTFolhaPagamentoRegistroEventoComplementar->setDado("timestamp"               ,$this->getTimestamp());
            $obErro = $obTFolhaPagamentoRegistroEventoComplementar->inclusao($boTransacao);
        }
        if ( !$obErro->ocorreu() and $this->getTimestamp() == "" ) {
            $obErro = $obTFolhaPagamentoRegistroEventoComplementar->recuperaNow3($stTimestamp,$boTransacao);
            $this->setTimestamp( $stTimestamp );
        }
        if ( !$obErro->ocorreu() ) {
            $obTFolhaPagamentoUltimoRegistroEventoComplementar->setDado("cod_registro"    ,$this->getCodRegistro());
            $obTFolhaPagamentoUltimoRegistroEventoComplementar->setDado("timestamp"         ,$this->getTimestamp());
            $obTFolhaPagamentoUltimoRegistroEventoComplementar->setDado("cod_evento"        ,$this->obRFolhaPagamentoEvento->getCodEvento());
            $obTFolhaPagamentoUltimoRegistroEventoComplementar->setDado("cod_configuracao"  ,$this->obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->getCodConfiguracao());
            $obErro = $obTFolhaPagamentoUltimoRegistroEventoComplementar->inclusao($boTransacao);
        }
        if ( !$obErro->ocorreu() and $this->getParcela() ) {
            $obTFolhaPagamentoRegistroEventoComplementarParcela->setDado('cod_registro'     ,$this->getCodRegistro()      );
            $obTFolhaPagamentoRegistroEventoComplementarParcela->setDado('timestamp'        ,$this->getTimestamp()        );
            $obTFolhaPagamentoRegistroEventoComplementarParcela->setDado('parcela'          ,$this->getParcela()          );
            $obTFolhaPagamentoRegistroEventoComplementarParcela->setDado('cod_evento'       ,$this->obRFolhaPagamentoEvento->getCodEvento());
            $obTFolhaPagamentoRegistroEventoComplementarParcela->setDado('cod_configuracao' ,$this->obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->getCodConfiguracao());
            $obErro = $obTFolhaPagamentoRegistroEventoComplementarParcela->inclusao($boTransacao);
        }

    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTFolhaPagamentoRegistroEventoComplementar );

    return $obErro;
}

/**
    * excluirRegistroEventoComplementar
    * @access Public
*/
function excluirRegistroEventoComplementar($boTransacao="")
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoRegistroEventoComplementar.class.php"       );
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoRegistroEventoComplementarParcela.class.php");
    $obTFolhaPagamentoRegistroEventoComplementar        = new TFolhaPagamentoRegistroEventoComplementar;
    $obTFolhaPagamentoRegistroEventoComplementarParcela = new TFolhaPagamentoRegistroEventoComplementarParcela;
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if (!$obErro->ocorreu()) {
        $obErro = $this->excluirUltimoRegistroEventoComplementar($boTransacao);
        if (!$obErro->ocorreu()) {
            $obTFolhaPagamentoRegistroEventoComplementarParcela->setDado("cod_registro"  ,$this->getCodRegistro());
            $obTFolhaPagamentoRegistroEventoComplementarParcela->setDado("timestamp",   $this->getTimestamp());
            $obTFolhaPagamentoRegistroEventoComplementarParcela->setDado("cod_evento",  $this->obRFolhaPagamentoEvento->getCodEvento());
            if ( is_object($this->obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento) ) {
                $obTFolhaPagamentoRegistroEventoComplementarParcela->setDado("cod_configuracao",$this->obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->getCodConfiguracao());
            }
            $obErro = $obTFolhaPagamentoRegistroEventoComplementarParcela->exclusao($boTransacao);
        }
        if (!$obErro->ocorreu()) {
            $obTFolhaPagamentoRegistroEventoComplementar->setDado("cod_registro",$this->getCodRegistro());
            $obTFolhaPagamentoRegistroEventoComplementar->setDado("timestamp",   $this->getTimestamp());
            $obTFolhaPagamentoRegistroEventoComplementar->setDado("cod_evento",  $this->obRFolhaPagamentoEvento->getCodEvento());
            if ( is_object($this->obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento) ) {
                $obTFolhaPagamentoRegistroEventoComplementar->setDado("cod_configuracao",$this->obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->getCodConfiguracao());
            }
            $obErro = $obTFolhaPagamentoRegistroEventoComplementar->exclusao($boTransacao);
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTFolhaPagamentoRegistroEventoComplementar );

    return $obErro;
}

/**
    * excluirUltimoRegistroEventoComplementar
    * @access Public
*/
function excluirUltimoRegistroEventoComplementar($boTransacao="")
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoUltimoRegistroEventoComplementar.class.php" );
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoRegistroEventoComplementarParcela.class.php" );
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoLogErroCalculoComplementar.class.php" );
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoComplementarCalculado.class.php");
    $obTFolhaPagamentoUltimoRegistroEventoComplementar = new TFolhaPagamentoUltimoRegistroEventoComplementar;
    $obTFolhaPagamentoEventoComplementarCalculado      = new TFolhaPagamentoEventoComplementarCalculado;
    $obTFolhaPagamentoRegistroEventoComplementarParcela= new TFolhaPagamentoRegistroEventoComplementarParcela;
    $obTFolhaPagamentoLogErroCalculoComplementar       = new TFolhaPagamentoLogErroCalculoComplementar;
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if (!$obErro->ocorreu()) {
        $obTFolhaPagamentoEventoComplementarCalculado->setDado("cod_registro"  ,$this->getCodRegistro());
        $obTFolhaPagamentoEventoComplementarCalculado->setDado("timestamp",   $this->getTimestamp());
        $obTFolhaPagamentoEventoComplementarCalculado->setDado("cod_evento",  $this->obRFolhaPagamentoEvento->getCodEvento());
        if ( is_object($this->obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento) ) {
            $obTFolhaPagamentoEventoComplementarCalculado->setDado("cod_configuracao",$this->obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->getCodConfiguracao());
        }
        $obErro = $obTFolhaPagamentoEventoComplementarCalculado->exclusao($boTransacao);

    }
    if (!$obErro->ocorreu()) {
        $obTFolhaPagamentoRegistroEventoComplementarParcela->setDado("cod_registro"  ,$this->getCodRegistro());
        $obTFolhaPagamentoRegistroEventoComplementarParcela->setDado("timestamp",   $this->getTimestamp());
        $obTFolhaPagamentoRegistroEventoComplementarParcela->setDado("cod_evento",  $this->obRFolhaPagamentoEvento->getCodEvento());
        if ( is_object($this->obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento) ) {
            $obTFolhaPagamentoRegistroEventoComplementarParcela->setDado("cod_configuracao",$this->obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->getCodConfiguracao());
        }
        $obErro = $obTFolhaPagamentoRegistroEventoComplementarParcela->exclusao($boTransacao);

    }
    if (!$obErro->ocorreu()) {
        $obTFolhaPagamentoLogErroCalculoComplementar->setDado("cod_registro"  ,$this->getCodRegistro());
        $obTFolhaPagamentoLogErroCalculoComplementar->setDado("timestamp",   $this->getTimestamp());
        $obTFolhaPagamentoLogErroCalculoComplementar->setDado("cod_evento",  $this->obRFolhaPagamentoEvento->getCodEvento());
        if ( is_object($this->obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento) ) {
            $obTFolhaPagamentoLogErroCalculoComplementar->setDado("cod_configuracao",$this->obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->getCodConfiguracao());
        }
        $obErro = $obTFolhaPagamentoLogErroCalculoComplementar->exclusao($boTransacao);

    }

    if (!$obErro->ocorreu()) {
        $obTFolhaPagamentoUltimoRegistroEventoComplementar->setDado("cod_registro"  ,$this->getCodRegistro());
        $obTFolhaPagamentoUltimoRegistroEventoComplementar->setDado("timestamp",   $this->getTimestamp());
        $obTFolhaPagamentoUltimoRegistroEventoComplementar->setDado("cod_evento",  $this->obRFolhaPagamentoEvento->getCodEvento());
        if ( is_object($this->obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento) ) {
            $obTFolhaPagamentoUltimoRegistroEventoComplementar->setDado("cod_configuracao",$this->obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->getCodConfiguracao());
        }
        $obErro = $obTFolhaPagamentoUltimoRegistroEventoComplementar->exclusao($boTransacao);
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTFolhaPagamentoRegistroEventoComplementar );

    return $obErro;
}

/**
    * Método listar
    * @access Private
*/
function listar(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoRegistroEventoComplementar.class.php");
    $obTFolhaPagamentoRegistroEventoComplementar = new TFolhaPagamentoRegistroEventoComplementar;
    $obErro = $obTFolhaPagamentoRegistroEventoComplementar->recuperaRelacionamento($rsRecordSet,$stFiltro,$stOrder,$boTransacao);

    return $obErro;
}

/**
    * Método listarRegistroEventoComplementar
    * @access Public
*/
function listarRegistroEventoComplementar(&$rsRecordSet,$boTransacao="")
{
    if ( $this->roRFolhaPagamentoFolhaComplementar->roRFolhaPagamentoPeriodoMovimentacao->getCodPeriodoMovimentacao() ) {
        $stFiltro .= " AND registro_evento_complementar.cod_periodo_movimentacao = ".$this->roRFolhaPagamentoFolhaComplementar->roRFolhaPagamentoPeriodoMovimentacao->getCodPeriodoMovimentacao();
    }
    if ( $this->roRFolhaPagamentoFolhaComplementar->getCodComplementar() ) {
        $stFiltro .= " AND registro_evento_complementar.cod_complementar = ".$this->roRFolhaPagamentoFolhaComplementar->getCodComplementar();
    }
    if ( is_object($this->roRFolhaPagamentoFolhaComplementar->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoPeriodoContratoServidor) ) {
        if ( $this->roRFolhaPagamentoFolhaComplementar->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoPeriodoContratoServidor->getCodContrato() ) {
            $stFiltro .= " AND registro_evento_complementar.cod_contrato = ".$this->roRFolhaPagamentoFolhaComplementar->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoPeriodoContratoServidor->getCodContrato();
        }
    }
    if ( $this->obRFolhaPagamentoEvento->getCodEvento() ) {
        $stFiltro .= " AND registro_evento_complementar.cod_evento = ".$this->obRFolhaPagamentoEvento->getCodEvento();
    }
    if ( is_object($this->obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento) ) {
        if ( $this->obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->getCodConfiguracao() ) {
            $stFiltro .= " AND registro_evento_complementar.cod_configuracao = ".$this->obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->getCodConfiguracao();
        }
    }
    $obErro = $this->listar($rsRecordSet,$stFiltro,$stOrdem,$boTransacao);

    return $obErro;
}

/**
    * Método listarRegistroEventoComplementarExclusao
    * @access Public
*/
function listarRegistroEventoComplementarExclusao(&$rsRecordSet,$boTransacao="")
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoRegistroEventoComplementar.class.php");
    $obTFolhaPagamentoRegistroEventoComplementar = new TFolhaPagamentoRegistroEventoComplementar;
    if ( $this->roRFolhaPagamentoFolhaComplementar->roRFolhaPagamentoPeriodoMovimentacao->getCodPeriodoMovimentacao() ) {
        $stFiltro .= " AND registro_evento_complementar.cod_periodo_movimentacao = ".$this->roRFolhaPagamentoFolhaComplementar->roRFolhaPagamentoPeriodoMovimentacao->getCodPeriodoMovimentacao();
    }
    if ( $this->roRFolhaPagamentoFolhaComplementar->getCodComplementar() ) {
        $stFiltro .= " AND registro_evento_complementar.cod_complementar = ".$this->roRFolhaPagamentoFolhaComplementar->getCodComplementar();
    }
    if ( is_object($this->roRFolhaPagamentoFolhaComplementar->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoPeriodoContratoServidor) ) {
        if ( $this->roRFolhaPagamentoFolhaComplementar->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoPeriodoContratoServidor->getCodContrato() ) {
            $stFiltro .= " AND registro_evento_complementar.cod_contrato = ".$this->roRFolhaPagamentoFolhaComplementar->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoPeriodoContratoServidor->getCodContrato();
        }
    }
    $obErro = $obTFolhaPagamentoRegistroEventoComplementar->recuperaRegistroEventoComplementarExclusao($rsRecordSet,$stFiltro,$stOrdem,$boTransacao);

    return $obErro;
}

/**
    * Método listarTodosRegistrosEventoComplemtar
    * @access Public
*/
function listarTodosRegistrosEventoComplementar(&$rsRecordSet,$boTransacao="")
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoRegistroEventoComplementar.class.php");
    $obTFolhaPagamentoRegistroEventoComplementar = new TFolhaPagamentoRegistroEventoComplementar;
    if ( $this->roRFolhaPagamentoFolhaComplementar->roRFolhaPagamentoPeriodoMovimentacao->getCodPeriodoMovimentacao() ) {
        $stFiltro .= " AND registro_evento_complementar.cod_periodo_movimentacao = ".$this->roRFolhaPagamentoFolhaComplementar->roRFolhaPagamentoPeriodoMovimentacao->getCodPeriodoMovimentacao();
    }
    if ( $this->roRFolhaPagamentoFolhaComplementar->getCodComplementar() ) {
        $stFiltro .= " AND registro_evento_complementar.cod_complementar = ".$this->roRFolhaPagamentoFolhaComplementar->getCodComplementar();
    }
    if ( is_object($this->roRFolhaPagamentoFolhaComplementar->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoPeriodoContratoServidor) ) {
        if ( $this->roRFolhaPagamentoFolhaComplementar->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoPeriodoContratoServidor->getCodContrato() ) {
            $stFiltro .= " AND registro_evento_complementar.cod_contrato = ".$this->roRFolhaPagamentoFolhaComplementar->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoPeriodoContratoServidor->getCodContrato();
        }
    }
    if ($stFiltro != "") {
        $stFiltro = " WHERE ".substr($stFiltro,4,strlen($stFiltro));
    }
    $obErro = $obTFolhaPagamentoRegistroEventoComplementar->recuperaTodos($rsRecordSet,$stFiltro,$stOrdem,$boTransacao);

    return $obErro;
}

/**
    * Método listarTodosRegistrosEventoComplemtar
    * @access Public
*/
function listarContratosComRegistroDeEventoPorCgm(&$rsRecordSet,$inCGM,$inRegistro,$inCodComplementar,$boTransacao="")
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoRegistroEventoComplementar.class.php");
    $obTFolhaPagamentoRegistroEventoComplementar = new TFolhaPagamentoRegistroEventoComplementar;
    $stFiltro  = " WHERE registro != ".$inRegistro;
    $stFiltro .= " AND numcgm = ".$inCGM;
    $stFiltro .= " AND cod_complementar = ".$inCodComplementar;
    $obErro = $obTFolhaPagamentoRegistroEventoComplementar->recuperaContratosComRegistroDeEventoPorCgm($rsRecordSet,$stFiltro,$stOrdem,$boTransacao);

    return $obErro;
}

/**
    * Método listarContratosComRegistroDeEvento
    * @access Public
*/
function listarContratosComRegistroDeEvento(&$rsRecordSet,$inCodComplementar,$inCodPeriodoMovimentacao,$boTransacao="")
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoRegistroEventoComplementar.class.php");
    $obTFolhaPagamentoRegistroEventoComplementar = new TFolhaPagamentoRegistroEventoComplementar;
    $stFiltro .= " WHERE cod_complementar = ".$inCodComplementar;
    $stFiltro .= "   AND cod_periodo_movimentacao = ". $inCodPeriodoMovimentacao;
    $stOrdem   = " numcgm,registro";
    $obErro = $obTFolhaPagamentoRegistroEventoComplementar->recuperaContratosComRegistroDeEvento($rsRecordSet,$stFiltro,$stOrdem,$boTransacao);

    return $obErro;
}

}
?>
