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
    * Classe de regra de negócio para RFolhaPagamentoFGTS
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
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoEvento.class.php"                                  );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFGTSEvento.class.php"                              );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFGTSCategoria.class.php"                           );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalCategoria.class.php"                                      );

class RFolhaPagamentoFGTS
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
var $inCodFGTS;
/**
   * @access Private
   * @var String
*/
var $stTimestamp;
/**
   * @access Private
   * @var Date
*/
var $dtVigencia;
/**
   * @access Private
   * @var Object
*/
var $roRFolhaPagamentoEvento;
/**
   * @access Private
   * @var Array
*/
var $arRFolhaPagamentoEvento;
/**
   * @access Private
   * @var Object
*/
var $roRFolhaPagamentoFGTSEvento;
/**
   * @access Private
   * @var Array
*/
var $arRFolhaPagamentoFGTSEvento;
/**
   * @access Private
   * @var Object
*/
var $roRFolhaPagamentoFGTSCategoria;
/**
   * @access Private
   * @var Array
*/
var $arRFolhaPagamentoFGTSCategoria;
/**
   * @access Private
   * @var Object
*/
var $roRPessoalCategoria;
/**
   * @access Private
   * @var Array
*/
var $arRPessoalCategoria;

/**
    * @access Public
    * @param Object $valor
*/
function setTransacao($valor) { $this->obTransacao                     = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodFGTS($valor) { $this->inCodFGTS                       = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setTimestamp($valor) { $this->stTimestamp                     = $valor; }
/**
    * @access Public
    * @param Date $valor
*/
function setVigencia($valor) { $this->dtVigencia                      = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRORFolhaPagamentoEvento(&$valor) { $this->roRFolhaPagamentoEvento        = &$valor; }
/**
    * @access Public
    * @param Array $valor
*/
function setARRFolhaPagamentoEvento($valor) { $this->arRFolhaPagamentoEvento         = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRORFolhaPagamentoFGTSEvento(&$valor) { $this->roRFolhaPagamentoFGTSEvento    = &$valor; }
/**
    * @access Public
    * @param Array $valor
*/
function setARRFolhaPagamentoFGTSEvento($valor) { $this->arRFolhaPagamentoFGTSEvento     = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRORFolhaPagamentoFGTSCategoria(&$valor) { $this->roRFolhaPagamentoFGTSCategoria = &$valor; }
/**
    * @access Public
    * @param Array $valor
*/
function setARRFolhaPagamentoFGTSCategoria($valor) { $this->arRFolhaPagamentoFGTSCategoria  = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRORPessoalCategoria(&$valor) { $this->roRPessoalCategoria            = &$valor; }
/**
    * @access Public
    * @param Array $valor
*/
function setARRPessoalCategoria($valor) { $this->arRPessoalCategoria             = $valor; }

/**
    * @access Public
    * @return Object
*/
function getTransacao() { return $this->obTransacao;                     }
/**
    * @access Public
    * @return Integer
*/
function getCodFGTS() { return $this->inCodFGTS;                       }
/**
    * @access Public
    * @return String
*/
function getTimestamp() { return $this->stTimestamp;                     }
/**
    * @access Public
    * @return Date
*/
function getVigencia() { return $this->dtVigencia;                      }
/**
    * @access Public
    * @return Object
*/
function getRORFolhaPagamentoEvento() { return $this->roRFolhaPagamentoEvento;         }
/**
    * @access Public
    * @return Array
*/
function getARRFolhaPagamentoEvento() { return $this->arRFolhaPagamentoEvento;         }
/**
    * @access Public
    * @return Object
*/
function getRORFolhaPagamentoFGTSEvento() { return $this->roRFolhaPagamentoFGTSEvento;     }
/**
    * @access Public
    * @return Array
*/
function getARRFolhaPagamentoFGTSEvento() { return $this->arRFolhaPagamentoFGTSEvento;     }
/**
    * @access Public
    * @return Object
*/
function getRORFolhaPagamentoFGTSCategoria() { return $this->roRFolhaPagamentoFGTSCategoria;  }
/**
    * @access Public
    * @return Array
*/
function getARRFolhaPagamentoFGTSCategoria() { return $this->arRFolhaPagamentoFGTSCategoria;  }
/**
    * @access Public
    * @return Object
*/
function getRORPessoalCategoria() { return $this->roRPessoalCategoria;             }
/**
    * @access Public
    * @return Array
*/
function getARRPessoalCategoria() { return $this->arRPessoalCategoria;             }

/**
     * Método construtor
     * @access Private
*/
function RFolhaPagamentoFGTS()
{
    $this->setTransacao                                 ( new Transacao                 );
    $this->setARRFolhaPagamentoEvento                   ( array()                       );
    $this->setARRFolhaPagamentoFGTSEvento               ( array()                       );
    $this->setARRFolhaPagamentoFGTSCategoria            ( array()                       );
    $this->setARRPessoalCategoria                       ( array()                       );

}

/**
    * Inclui
    * @access Public
*/
function incluirFGTS($boTransacao="")
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoFgts.class.php");
    $obTFolhaPagamentoFGTS = new TFolhaPagamentoFgts;
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if (!$obErro->ocorreu()) {
        $stCampoCod         = $obTFolhaPagamentoFGTS->getCampoCod();
        $stComplementoChave = $obTFolhaPagamentoFGTS->getComplementoChave();
        $obTFolhaPagamentoFGTS->setCampoCod('cod_fgts');
        $obTFolhaPagamentoFGTS->setComplementoChave('');
        $obErro = $obTFolhaPagamentoFGTS->proximoCod($inCodFGTS,$boTransacao);
        $this->setCodFGTS($inCodFGTS);
        $obTFolhaPagamentoFGTS->setCampoCod($stCampoCod);
        $obTFolhaPagamentoFGTS->setComplementoChave($stComplementoChave);
        if (!$obErro->ocorreu()) {
            $obErro = $obTFolhaPagamentoFGTS->recuperaNow3($stTimestamp,$boTransacao);
            $this->setTimestamp($stTimestamp);
        }
        if (!$obErro->ocorreu()) {
            $obTFolhaPagamentoFGTS->setDado('cod_fgts'  ,$this->getCodFGTS()    );
            $obTFolhaPagamentoFGTS->setDado('vigencia'  ,$this->getVigencia()   );
            $obErro = $obTFolhaPagamentoFGTS->inclusao($boTransacao);
        }
        if (!$obErro->ocorreu()) {
            for ( $inIndex=0;$inIndex<count($this->arRPessoalCategoria);$inIndex++ ) {
                $obRPessoalCategoria = $this->arRPessoalCategoria[$inIndex];
                $obErro = $obRPessoalCategoria->roRFolhaPagamentoFGTSCategoria->incluirFGTSCategoria($boTransacao);
                if ( $obErro->ocorreu() ) {
                    break;
                }
            }
        }
        if (!$obErro->ocorreu()) {
            for ( $inIndex=0;$inIndex<count($this->arRFolhaPagamentoEvento);$inIndex++ ) {
                $obRFolhaPagamentoEvento = $this->arRFolhaPagamentoEvento[$inIndex];
                $obErro = $obRFolhaPagamentoEvento->roRFolhaPagamentoFGTSEvento->incluirFGTSEvento($boTransacao);
                if ( $obErro->ocorreu() ) {
                    break;
                }
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTFolhaPagamentoFGTS );

    return $obErro;
}

/**
    * Alterar
    * @access Public
*/
function alterarFGTS($boTransacao="")
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoFgts.class.php");
    $obTFolhaPagamentoFGTS = new TFolhaPagamentoFgts;
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if (!$obErro->ocorreu()) {
        if (!$obErro->ocorreu()) {
            $obErro = $obTFolhaPagamentoFGTS->recuperaNow3($stTimestamp,$boTransacao);
            $this->setTimestamp($stTimestamp);
        }
        if (!$obErro->ocorreu()) {
            $obTFolhaPagamentoFGTS->setDado('cod_fgts'  ,$this->getCodFGTS()    );
            $obTFolhaPagamentoFGTS->setDado('vigencia'  ,$this->getVigencia()   );
            $obErro = $obTFolhaPagamentoFGTS->inclusao($boTransacao);
        }
        if (!$obErro->ocorreu()) {
            for ( $inIndex=0;$inIndex<count($this->arRPessoalCategoria);$inIndex++ ) {
                $obRPessoalCategoria = $this->arRPessoalCategoria[$inIndex];
                $obErro = $obRPessoalCategoria->roRFolhaPagamentoFGTSCategoria->incluirFGTSCategoria($boTransacao);
                if ( $obErro->ocorreu() ) {
                    break;
                }
            }
        }
        if (!$obErro->ocorreu()) {
            for ( $inIndex=0;$inIndex<count($this->arRFolhaPagamentoEvento);$inIndex++ ) {
                $obRFolhaPagamentoEvento = $this->arRFolhaPagamentoEvento[$inIndex];
                $obErro = $obRFolhaPagamentoEvento->roRFolhaPagamentoFGTSEvento->incluirFGTSEvento($boTransacao);
                if ( $obErro->ocorreu() ) {
                    break;
                }
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTFolhaPagamentoFGTS );

    return $obErro;
}

/**
    * Método listar
    * @access Private
*/
function listar(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoFgts.class.php");
    $obTFolhaPagamentoFGTS = new TFolhaPagamentoFgts;
    $obErro = $obTFolhaPagamentoFGTS->recuperaRelacionamento($rsRecordSet,$stFiltro,$stOrder,$boTransacao);

    return $obErro;
}

/**
    * Método listarFGTS
    * @access Public
*/
function listarFGTS(&$rsRecordSet,$boTransacao="")
{
    if ( $this->getVigencia() ) {
        $stFiltro .= " AND  TO_CHAR(vigencia,'dd/mm/yyyy') = '".$this->getVigencia()."'";
    }
    if ( $this->getCodFGTS() ) {
        $stFiltro .= " AND fgts.cod_fgts = ".$this->getCodFGTS();
    }
    $obErro = $this->listar($rsRecordSet,$stFiltro,$stOrdem,$boTransacao);

    return $obErro;
}

/**
    * Método addRFolhaPagamentoEvento
    * @acess Public
*/
function addRFolhaPagamentoEvento()
{
    $this->arRFolhaPagamentoEvento[] = new RFolhaPagamentoEvento();
    $this->roRFolhaPagamentoEvento   = &$this->arRFolhaPagamentoEvento[ count($this->arRFolhaPagamentoEvento)-1 ];
    $this->roRFolhaPagamentoEvento->setRORFolhaPagamentoFGTS( $this );
    $this->arRFolhaPagamentoFGTSEvento[] = new RFolhaPagamentoFGTSEvento($this,$this->roRFolhaPagamentoEvento);
    $this->roRFolhaPagamentoFGTSEvento   = &$this->arRFolhaPagamentoFGTSEvento[ count($this->arRFolhaPagamentoFGTSEvento)-1 ];
    $this->roRFolhaPagamentoEvento->setRORFolhaPagamentoFGTSEvento( $this->roRFolhaPagamentoFGTSEvento );
}

/**
    * Método addRPessoalCategoria
    * @acess Public
*/
function addRPessoalCategoria()
{
    $this->arRPessoalCategoria[] = new RPessoalCategoria();
    $this->roRPessoalCategoria   = &$this->arRPessoalCategoria[ count($this->arRPessoalCategoria)-1 ];
    $this->roRPessoalCategoria->setRORFolhaPagamentoFGTS( $this );
    $this->arRFolhaPagamentoFGTSCategoria[] = new RFolhaPagamentoFGTSCategoria($this,$this->roRPessoalCategoria);
    $this->roRFolhaPagamentoFGTSCategoria   = &$this->arRFolhaPagamentoFGTSCategoria[ count($this->arRFolhaPagamentoFGTSCategoria)-1 ];
    $this->roRPessoalCategoria->setRORFolhaPagamentoFGTSCategoria( $this->roRFolhaPagamentoFGTSCategoria );
}

}
?>
