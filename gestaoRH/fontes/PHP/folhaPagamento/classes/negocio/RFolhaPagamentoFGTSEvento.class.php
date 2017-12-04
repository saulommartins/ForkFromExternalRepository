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
    * Classe de regra de negócio para RFolhaPagamentoFGTSEvento
    * Data de Criação: 10/01/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Regra de Negócio

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-05 17:06:51 -0300 (Ter, 05 Jun 2007) $

    * Casos de uso: uc-04.05.42
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class RFolhaPagamentoFGTSEvento
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
var $roRFolhaPagamentoEvento;
/**
   * @access Private
   * @var Object
*/
var $roRFolhaPagamentoFGTS;

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
function setRORFolhaPagamentoEvento(&$valor) { $this->roRFolhaPagamentoEvento = &$valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRORFolhaPagamentoFGTS(&$valor) { $this->roRFolhaPagamentoFGTS   = &$valor; }

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
function getRORFolhaPagamentoEvento() { return $this->roRFolhaPagamentoEvento;  }
/**
    * @access Public
    * @return Object
*/
function getRORFolhaPagamentoFGTS() { return $this->roRFolhaPagamentoFGTS;    }

/**
     * Método construtor
     * @access Private
*/
function RFolhaPagamentoFGTSEvento(&$roRFolhaPagamentoFGTS,&$roRFolhaPagamentoEvento)
{
    $this->setTransacao                 ( new Transacao                         );
    $this->setRORFolhaPagamentoFGTS     ( $roRFolhaPagamentoFGTS                );
    $this->setRORFolhaPagamentoEvento   ( $roRFolhaPagamentoEvento              );
}

/**
    * Inclui
    * @access Public
*/
function incluirFGTSEvento($boTransacao)
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoFgtsEvento.class.php");
    $obTFolhaPagamentoFgtsEvento = new TFolhaPagamentoFgtsEvento;
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if (!$obErro->ocorreu()) {
        $obErro = $this->roRFolhaPagamentoEvento->listarEvento($rsEvento,$boTransacao);
        if (!$obErro->ocorreu()) {
            $obTFolhaPagamentoFgtsEvento->setDado('timestamp'       ,$this->roRFolhaPagamentoFGTS->getTimestamp()   );
            $obTFolhaPagamentoFgtsEvento->setDado('cod_tipo'        ,$this->getCodTipo()                            );
            $obTFolhaPagamentoFgtsEvento->setDado('cod_fgts'        ,$this->roRFolhaPagamentoFGTS->getCodFGTS()     );
            $obTFolhaPagamentoFgtsEvento->setDado('cod_evento'      ,$rsEvento->getCampo('cod_evento')              );
            $obErro = $obTFolhaPagamentoFgtsEvento->inclusao($boTransacao);
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTFolhaPagamentoFgtsEvento );

    return $obErro;
}

/**
    * Método listar
    * @access Private
*/
function listar(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoFgtsEvento.class.php");
    $obTFolhaPagamentoFgtsEvento = new TFolhaPagamentoFgtsEvento;
    $obErro = $obTFolhaPagamentoFgtsEvento->recuperaRelacionamento($rsRecordSet,$stFiltro,$stOrder,$boTransacao);

    return $obErro;
}

/**
    * Método listarFGTS
    * @access Private
*/
function listarFGTSEvento(&$rsRecordSet,$boTransacao="")
{
    if ( $this->roRFolhaPagamentoFGTS->getCodFGTS() ) {
        $stFiltro .= " AND  fgts_evento.cod_fgts = ".$this->roRFolhaPagamentoFGTS->getCodFGTS();
    }
    $obErro = $this->listar($rsRecordSet,$stFiltro,$stOrdem,$boTransacao);

    return $obErro;
}

/**
    * Método listarTipoEventoFGTS
    * @access Private
*/
function listarTipoEventoFGTS(&$rsRecordSet,$boTransacao="")
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoTipoEventoFgts.class.php");
    $obTFolhaPagamentoTipoEventoFgts = new TFolhaPagamentoTipoEventoFgts;
    $obErro = $obTFolhaPagamentoTipoEventoFgts->recuperaTodos($rsRecordSet,$stFiltro,$stOrder,$boTransacao);

    return $obErro;
}

}
?>
