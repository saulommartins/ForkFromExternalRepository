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
    * Classe de regra de negócio para RFolhaPagamentoEventoIRRF
    * Data de Criação: 05/01/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Regra de Negócio

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-05 17:06:51 -0300 (Ter, 05 Jun 2007) $

    * Casos de uso: uc-04.05.23
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class RFolhaPagamentoEventoIRRF
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
var $inCodTipo;
/**
   * @access Private
   * @var Object
*/
var $roRFolhaPagamentoIRRF;
/**
   * @access Private
   * @var Object
*/
var $roRFolhaPagamentoEvento;

/**
    * @access Public
    * @param Object $valor
*/
function setTransacao($valor) { $this->obTransacao              = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodTipo($valor) { $this->inCodTipo                = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRORFolhaPagamentoIRRF(&$valor) { $this->roRFolhaPagamentoIRRF   = &$valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRORFolhaPagamentoEvento(&$valor) { $this->roRFolhaPagamentoEvento = &$valor; }

/**
    * @access Public
    * @return Object
*/
function getTransacao() { return $this->obTransacao;              }
/**
    * @access Public
    * @return Integer
*/
function getCodTipo() { return $this->inCodTipo;                }
/**
    * @access Public
    * @return Object
*/
function getRORFolhaPagamentoIRRF() { return $this->roRFolhaPagamentoIRRF;    }
/**
    * @access Public
    * @return Object
*/
function getRORFolhaPagamentoEvento() { return $this->roRFolhaPagamentoEvento;  }

/**
     * Método construtor
     * @access Private
*/
function RFolhaPagamentoEventoIRRF(&$roRFolhaPagamentoIRRF,&$roRFolhaPagamentoEvento)
{
    $this->setTransacao                 ( new Transacao                         );
    $this->setRORFolhaPagamentoIRRF     ( $roRFolhaPagamentoIRRF                );
    $this->setRORFolhaPagamentoEvento   ( $roRFolhaPagamentoEvento              );
}

/**
    * Inclui
    * @access Public
*/
function incluirEventoIRRF($boTransacao)
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoTabelaIrrfEvento.class.php");
    $obTFolhaPagamentoTabelaIrrfEvento = new TFolhaPagamentoTabelaIrrfEvento;
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if (!$obErro->ocorreu()) {
        $this->roRFolhaPagamentoEvento->listarEvento($rsEvento,$boTransacao);
        $obTFolhaPagamentoTabelaIrrfEvento->setDado('cod_tabela'    ,$this->roRFolhaPagamentoIRRF->getCodTabela()   );
        $obTFolhaPagamentoTabelaIrrfEvento->setDado('cod_tipo'      ,$this->getCodTipo()                            );
        $obTFolhaPagamentoTabelaIrrfEvento->setDado('cod_evento'    ,$rsEvento->getCampo('cod_evento')              );
        $obTFolhaPagamentoTabelaIrrfEvento->setDado('timestamp'     ,$this->roRFolhaPagamentoIRRF->getTimestamp()   );
        $obErro = $obTFolhaPagamentoTabelaIrrfEvento->inclusao($boTransacao);
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTFolhaPagamentoTabelaIrrfEvento );

    return $obErro;
}

/**
    * Excluir
    * @access Public
*/
function excluirEventoIRRF($boTransacao)
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoTabelaIrrfEvento.class.php");
    $obTFolhaPagamentoTabelaIrrfEvento = new TFolhaPagamentoTabelaIrrfEvento;
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if (!$obErro->ocorreu()) {
        $obTFolhaPagamentoTabelaIrrfEvento->setDado('cod_tabela'    ,$this->roRFolhaPagamentoIRRF->getCodTabela()   );
        $obTFolhaPagamentoTabelaIrrfEvento->setDado('timestamp'     ,$this->roRFolhaPagamentoIRRF->getTimestamp()   );
        $obErro = $obTFolhaPagamentoTabelaIrrfEvento->exclusao($boTransacao);
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTFolhaPagamentoTabelaIrrfEvento );

    return $obErro;
}

/**
    * Método listar
    * @access Private
*/
function listar(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoTipoEventoIrrf.class.php");
    $obTFolhaPagamentoTipoEventoIRRF = new TFolhaPagamentoTipoEventoIRRF;
    $obErro = $obTFolhaPagamentoTipoEventoIRRF->recuperaTodos($rsRecordSet,$stFiltro,$stOrder,$boTransacao);

    return $obErro;
}

/**
    * Método listarEventoIRRF
    * @access Public
*/
function listarEventoIRRF(&$rsRecordSet,$boTransacao="")
{
    $stOrdem = "tipo_evento_irrf.cod_tipo";
    $obErro  = $this->listar($rsRecordSet,$stFiltro,$stOrdem,$boTransacao);

    return $obErro;
}

/**
    * Método listarTabelaIRRFEvento
    * @access Public
*/
function listarTabelaIRRFEvento(&$rsRecordSet,$boTransacao="")
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoTabelaIrrfEvento.class.php");
    $obTFolhaPagamentoTabelaIrrfEvento = new TFolhaPagamentoTabelaIrrfEvento;
    if ( $this->roRFolhaPagamentoIRRF->getCodTabela() ) {
        $stFiltro .= " AND tabela_irrf_evento.cod_tabela = ".$this->roRFolhaPagamentoIRRF->getCodTabela();
    }
    $obErro = $obTFolhaPagamentoTabelaIrrfEvento->recuperaRelacionamento($rsRecordSet,$stFiltro,$stOrder,$boTransacao);

    return $obErro;
}

}
?>
