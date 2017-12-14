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
    * Classe de regra de negócio para RFolhaPagamentoIRRF
    * Data de Criação: 05/01/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Regra de Negócio

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2008-03-27 08:33:32 -0300 (Qui, 27 Mar 2008) $

    * Casos de uso: uc-04.05.23
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoEvento.class.php"                                  );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFaixaIRRF.class.php"                               );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoEventoIRRF.class.php"                          );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalCID.class.php"                                            );

class RFolhaPagamentoIRRF
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
var $inCodTabela;
/**
   * @access Private
   * @var String
*/
var $stTimestamp;
/**
   * @access Private
   * @var Numeric
*/
var $nuDependente;
/**
   * @access Private
   * @var Numeric
*/
var $nuLimiteIsencao;
/**
   * @access Private
   * @var Date
*/
var $dtVigencia;
/**
   * @access Private
   * @var Array
*/
var $arRFolhaPagamentoFaxaIRRF;
/**
   * @access Private
   * @var Object
*/
var $roRFolhaPagamentoFaxaIRRF;
/**
   * @access Private
   * @var Array
*/
var $arRFolhaPagamentoEvento;
/**
   * @access Private
   * @var Object
*/
var $roRFolhaPagamentoEvento;
/**
   * @access Private
   * @var Array
*/
var $arRFolhaPagamentoEventoIRRF;
/**
   * @access Private
   * @var Object
*/
var $roRFolhaPagamentoEventoIRRF;
/**
   * @access Private
   * @var Array
*/
var $arRPessoalCID;
/**
   * @access Private
   * @var Object
*/
var $roRPessoalCID;
/**
   * @access Private
   * @var Array
*/
var $arEventoAjudaCusto;
/**
   * @access Private
   * @var Object
*/
var $roEventoAjudaCusto;

/**
    * @access Public
    * @param Object $valor
*/
function setTransacao($valor) { $this->obTransacao                      = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodTabela($valor) { $this->inCodTabela                      = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setTimestamp($valor) { $this->stTimestamp                      = $valor; }
/**
    * @access Public
    * @param Numeric $valor
*/
function setDependente($valor) { $this->nuDependente                     = $valor; }
/**
    * @access Public
    * @param Numeric $valor
*/
function setLimiteIsencao($valor) { $this->nuLimiteIsencao                  = $valor; }
/**
    * @access Public
    * @param Date $valor
*/
function setVigencia($valor) { $this->dtVigencia                       = $valor; }
/**
    * @access Public
    * @param Array $valor
*/
function setARRFolhaPagamentoFaxaIRRF($valor) { $this->arRFolhaPagamentoFaxaIRRF        = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRORFolhaPagamentoFaxaIRRF(&$valor) { $this->roRFolhaPagamentoFaxaIRRF       = &$valor; }
/**
    * @access Public
    * @param Array $valor
*/
function setARRFolhaPagamentoEvento($valor) { $this->arRFolhaPagamentoEvento          = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRORFolhaPagamentoEvento(&$valor) { $this->roRFolhaPagamentoEvento         = &$valor; }
/**
    * @access Public
    * @param Array $valor
*/
function setARRFolhaPagamentoEventoIRRF($valor) { $this->arRFolhaPagamentoEventoIRRF  = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRORFolhaPagamentoEventoIRRF(&$valor) { $this->roRFolhaPagamentoEventoIRRF = &$valor; }
/**
    * @access Public
    * @param Array $valor
*/
function setARRPessoalCID($valor) { $this->arRPessoalCID                    = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRORPessoalCID(&$valor) { $this->roRPessoalCID                   = &$valor; }

/**
    * @access Public
    * @param Array $valor
*/
function setAREventoAjudaCusto($valor) { $this->arEventoAjudaCusto          = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setROEventoAjudaCusto(&$valor) { $this->roEventoAjudaCusto              = &$valor; }

/**
    * @access Public
    * @return Object
*/
function getTransacao() { return $this->obTransacao;                      }
/**
    * @access Public
    * @return Integer
*/
function getCodTabela() { return $this->inCodTabela;                      }
/**
    * @access Public
    * @return String
*/
function getTimestamp() { return $this->stTimestamp;                      }
/**
    * @access Public
    * @return Numeric
*/
function getDependente() { return $this->nuDependente;                     }
/**
    * @access Public
    * @return Numeric
*/
function getLimiteIsencao() { return $this->nuLimiteIsencao;                  }
/**
    * @access Public
    * @return Date
*/
function getVigencia() { return $this->dtVigencia;                       }
/**
    * @access Public
    * @return Array
*/
function getARRFolhaPagamentoFaxaIRRF() { return $this->arRFolhaPagamentoFaxaIRRF;        }
/**
    * @access Public
    * @return Object
*/
function getRORFolhaPagamentoFaxaIRRF() { return $this->roRFolhaPagamentoFaxaIRRF;        }
/**
    * @access Public
    * @return Array
*/
function getARRFolhaPagamentoEvento() { return $this->arRFolhaPagamentoEvento;          }
/**
    * @access Public
    * @return Object
*/
function getRORFolhaPagamentoEvento() { return $this->roRFolhaPagamentoEvento;          }
/**
    * @access Public
    * @return Array
*/
function getARRFolhaPagamentoEventoIRRF() { return $this->arRFolhaPagamentoEventoIRRF;  }
/**
    * @access Public
    * @return Object
*/
function getRORFolhaPagamentoEventoIRRF() { return $this->roRFolhaPagamentoEventoIRRF;  }
/**
    * @access Public
    * @return Array
*/
function getARRPessoalCID() { return $this->arRPessoalCID;                    }
/**
    * @access Public
    * @return Object
*/
function getRORPessoalCID() { return $this->roRPessoalCID;                    }

/**
    * @access Public
    * @return Array
*/
function getAREventoAjudaCusto() { return $this->arEventoAjudaCusto;                    }
/**
    * @access Public
    * @return Object
*/
function getROEventoAjudaCusto() { return $this->roEventoAjudaCusto;                    }

/**
     * Método construtor
     * @access Private
*/
function RFolhaPagamentoIRRF()
{
    $this->setTransacao                         ( new Transacao                 );
    $this->setARRFolhaPagamentoFaxaIRRF         ( array()                       );
    $this->setARRFolhaPagamentoEvento           ( array()                       );
    $this->setARRFolhaPagamentoEventoIRRF       ( array()                       );
    $this->setARRPessoalCID                     ( array()                       );

}

/**
    * Inclui
    * @access Public
*/
function incluirIRRF($boTransacao="")
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoTabelaIrrf.class.php"   );
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoTabelaIrrfCid.class.php");
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoTabelaIrrfComprovanteRendimento.class.php");
    $obTFolhaPagamentoTabelaIrrf    = new TFolhaPagamentoTabelaIrrf;
    $obTFolhaPagamentoTabelaIrrfCid = new TFolhaPagamentoTabelaIrrfCid;
    $obTFolhaPagamentoTabelaIrrfComprovanteRendimento = new TFolhaPagamentoTabelaIrrfComprovanteRendimento;
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if (!$obErro->ocorreu()) {
        $stCampoCod         = $obTFolhaPagamentoTabelaIrrf->getCampoCod();
        $stComplementoChave = $obTFolhaPagamentoTabelaIrrf->getComplementoChave();
        $obTFolhaPagamentoTabelaIrrf->setCampoCod('cod_tabela');
        $obTFolhaPagamentoTabelaIrrf->setComplementoChave('');
        $obErro = $obTFolhaPagamentoTabelaIrrf->proximoCod($inCodTabela,$boTransacao);
        $this->setCodTabela($inCodTabela);
        $obTFolhaPagamentoTabelaIrrf->setCampoCod($stCampoCod);
        $obTFolhaPagamentoTabelaIrrf->setComplementoChave($stComplementoChave);
        if (!$obErro->ocorreu()) {
            $obErro = $obTFolhaPagamentoTabelaIrrf->recuperaNow3($stTimestamp,$boTransacao);
            $this->setTimestamp($stTimestamp);
        }
        if (!$obErro->ocorreu()) {
            $obTFolhaPagamentoTabelaIrrf->setDado('cod_tabela'          ,$this->getCodTabela()      );
            $obTFolhaPagamentoTabelaIrrf->setDado('vl_dependente'       ,$this->getDependente()     );
            $obTFolhaPagamentoTabelaIrrf->setDado('vl_limite_isencao'   ,$this->getLimiteIsencao()  );
            $obTFolhaPagamentoTabelaIrrf->setDado('vigencia'            ,$this->getVigencia()       );
            $obErro = $obTFolhaPagamentoTabelaIrrf->inclusao($boTransacao);
        }
        if (!$obErro->ocorreu()) {
            for ( $inIndex=0;$inIndex<count($this->arRFolhaPagamentoFaixaIRRF);$inIndex++ ) {
                $obRFolhaPagamentoFaixaIRRF = $this->arRFolhaPagamentoFaixaIRRF[$inIndex];
                $obErro = $obRFolhaPagamentoFaixaIRRF->incluirFaixaIRRF($boTransacao);
                if ( $obErro->ocorreu() ) {
                    break;
                }
            }
        }
        if (!$obErro->ocorreu()) {
            for ( $inIndex=0;$inIndex<count($this->arRPessoalCID);$inIndex++ ) {
                $obRPessoalCID = $this->arRPessoalCID[$inIndex];
                $obTFolhaPagamentoTabelaIrrfCid->setDado('cod_cid'      ,$obRPessoalCID->getCodCID()    );
                $obTFolhaPagamentoTabelaIrrfCid->setDado('cod_tabela'   ,$this->getCodTabela()          );
                $obTFolhaPagamentoTabelaIrrfCid->setDado('timestamp'    ,$this->getTimestamp()          );
                $obErro = $obTFolhaPagamentoTabelaIrrfCid->inclusao($boTransacao);
                if ( $obErro->ocorreu() ) {
                    break;
                }
            }
        }
        if (!$obErro->ocorreu()) {
            for ( $inIndex=0;$inIndex<count($this->arRFolhaPagamentoEvento);$inIndex++ ) {
                $obRFolhaPagamentoEvento = $this->arRFolhaPagamentoEvento[$inIndex];
                $obErro = $obRFolhaPagamentoEvento->roRFolhaPagamentoEventoIRRF->incluirEventoIRRF($boTransacao);
                if ( $obErro->ocorreu() ) {
                    break;
                }
            }
        }
        if (!$obErro->ocorreu()) {
            for ( $inIndex=0;$inIndex<count($this->arEventoAjudaCusto);$inIndex++ ) {
                $obTFolhaPagamentoTabelaIrrfComprovanteRendimento->setDado('cod_tabela', $this->getCodTabela());
                $obTFolhaPagamentoTabelaIrrfComprovanteRendimento->setDado('timestamp', $this->getTimestamp());
                $obTFolhaPagamentoTabelaIrrfComprovanteRendimento->setDado('cod_evento', $this->arEventoAjudaCusto[$inIndex]);
                $obErro = $obTFolhaPagamentoTabelaIrrfComprovanteRendimento->inclusao($boTransacao);
                if ( $obErro->ocorreu() ) {
                    break;
                }
            }
        }//
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTFolhaPagamentoTabelaIrrf );

    return $obErro;
}

/**
    * Alterar
    * @access Public
*/
function alterarIRRF($boTransacao="")
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoTabelaIrrf.class.php"   );
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoTabelaIrrfCid.class.php");
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoTabelaIrrfComprovanteRendimento.class.php");
    $obTFolhaPagamentoTabelaIrrf    = new TFolhaPagamentoTabelaIrrf;
    $obTFolhaPagamentoTabelaIrrfCid = new TFolhaPagamentoTabelaIrrfCid;
    $obTFolhaPagamentoTabelaIrrfComprovanteRendimento = new TFolhaPagamentoTabelaIrrfComprovanteRendimento;
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if (!$obErro->ocorreu()) {
        $obErro = $obTFolhaPagamentoTabelaIrrf->recuperaNow3($stTimestamp,$boTransacao);
        $this->setTimestamp($stTimestamp);
        if (!$obErro->ocorreu()) {
            $obTFolhaPagamentoTabelaIrrf->setDado('cod_tabela'          ,$this->getCodTabela()      );
            $obTFolhaPagamentoTabelaIrrf->setDado('vl_dependente'       ,$this->getDependente()     );
            $obTFolhaPagamentoTabelaIrrf->setDado('vl_limite_isencao'   ,$this->getLimiteIsencao()  );
            $obTFolhaPagamentoTabelaIrrf->setDado('vigencia'            ,$this->getVigencia()       );
            $obErro = $obTFolhaPagamentoTabelaIrrf->inclusao($boTransacao);
        }
        if (!$obErro->ocorreu()) {
            for ( $inIndex=0;$inIndex<count($this->arRFolhaPagamentoFaixaIRRF);$inIndex++ ) {
                $obRFolhaPagamentoFaixaIRRF = $this->arRFolhaPagamentoFaixaIRRF[$inIndex];
                $obErro = $obRFolhaPagamentoFaixaIRRF->incluirFaixaIRRF($boTransacao);
                if ( $obErro->ocorreu() ) {
                    break;
                }
            }
        }
        if (!$obErro->ocorreu()) {
            for ( $inIndex=0;$inIndex<count($this->arRPessoalCID);$inIndex++ ) {
                $obRPessoalCID = $this->arRPessoalCID[$inIndex];
                $obTFolhaPagamentoTabelaIrrfCid->setDado('cod_cid'      ,$obRPessoalCID->getCodCID()    );
                $obTFolhaPagamentoTabelaIrrfCid->setDado('cod_tabela'   ,$this->getCodTabela()          );
                $obTFolhaPagamentoTabelaIrrfCid->setDado('timestamp'    ,$this->getTimestamp()          );
                $obErro = $obTFolhaPagamentoTabelaIrrfCid->inclusao($boTransacao);
                if ( $obErro->ocorreu() ) {
                    break;
                }
            }
        }
        if (!$obErro->ocorreu()) {
            for ( $inIndex=0;$inIndex<count($this->arRFolhaPagamentoEvento);$inIndex++ ) {
                $obRFolhaPagamentoEvento = $this->arRFolhaPagamentoEvento[$inIndex];
                $obErro = $obRFolhaPagamentoEvento->roRFolhaPagamentoEventoIRRF->incluirEventoIRRF($boTransacao);
                if ( $obErro->ocorreu() ) {
                    break;
                }
            }
        }
        if (!$obErro->ocorreu()) {
            for ( $inIndex=0;$inIndex<count($this->arEventoAjudaCusto);$inIndex++ ) {
                $obTFolhaPagamentoTabelaIrrfComprovanteRendimento->setDado('cod_tabela', $this->getCodTabela());
                $obTFolhaPagamentoTabelaIrrfComprovanteRendimento->setDado('timestamp', $this->getTimestamp());
                $obTFolhaPagamentoTabelaIrrfComprovanteRendimento->setDado('cod_evento', $this->arEventoAjudaCusto[$inIndex]);
                $obErro = $obTFolhaPagamentoTabelaIrrfComprovanteRendimento->inclusao($boTransacao);
                if ( $obErro->ocorreu() ) {
                    break;
                }
            }
        }//
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTFolhaPagamentoTabelaIrrf );

    return $obErro;
}

/**
    * Excluir
    * @access Public
*/
function excluirIRRF($boTransacao="")
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoTabelaIrrf.class.php");
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoTabelaIrrfCid.class.php");
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoTabelaIrrfComprovanteRendimento.class.php");
    $obTFolhaPagamentoTabelaIrrf    = new TFolhaPagamentoTabelaIrrf;
    $obTFolhaPagamentoTabelaIrrfCid = new TFolhaPagamentoTabelaIrrfCid;
    $obTFolhaPagamentoTabelaIrrfComprovanteRendimento = new TFolhaPagamentoTabelaIrrfComprovanteRendimento;
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if (!$obErro->ocorreu()) {
        $stFiltro = " WHERE vigencia = to_date('".$this->getVigencia()."', 'dd/mm/yyyy')";
        $obTFolhaPagamentoTabelaIrrf->recuperaTodos($rsTabelaIRRF,$stFiltro,"",$boTransacao);
    }

    if (!$obErro->ocorreu()) {
        $this->addRFolhaPagamentoFaixaIRRF();
        while (!$rsTabelaIRRF->eof()) {
            $this->setCodTabela($rsTabelaIRRF->getCampo("cod_tabela"));
            $this->setTimestamp($rsTabelaIRRF->getCampo("timestamp"));
            $obErro = $this->roRFolhaPagamentoFaixaIRRF->excluirFaixaIRRF($boTransacao);
            if (!$obErro->ocorreu()) {
                $obTFolhaPagamentoTabelaIrrfCid->setDado('cod_tabela'  ,$rsTabelaIRRF->getCampo("cod_tabela")  );
                $obTFolhaPagamentoTabelaIrrfCid->setDado('timestamp'   ,$rsTabelaIRRF->getCampo("timestamp") );
                $obErro = $obTFolhaPagamentoTabelaIrrfCid->exclusao($boTransacao);
            }
            if (!$obErro->ocorreu()) {
                $this->addRFolhaPagamentoEvento();
                $obErro = $this->roRFolhaPagamentoEventoIRRF->excluirEventoIRRF($boTransacao);
            }
            if (!$obErro->ocorreu()) {
                $obTFolhaPagamentoTabelaIrrfComprovanteRendimento->setDado('cod_tabela'  ,$rsTabelaIRRF->getCampo("cod_tabela")  );
                $obTFolhaPagamentoTabelaIrrfComprovanteRendimento->setDado('timestamp'   ,$rsTabelaIRRF->getCampo("timestamp")  );
                $obErro = $obTFolhaPagamentoTabelaIrrfComprovanteRendimento->exclusao($boTransacao);
            }
            if (!$obErro->ocorreu()) {
                $obTFolhaPagamentoTabelaIrrf->setDado('cod_tabela'  ,$rsTabelaIRRF->getCampo("cod_tabela")  );
                $obTFolhaPagamentoTabelaIrrf->setDado('timestamp'   ,$rsTabelaIRRF->getCampo("timestamp")  );
                $obErro = $obTFolhaPagamentoTabelaIrrf->exclusao($boTransacao);
            }
            if ($obErro->ocorreu()) {
                break;
            }
            $rsTabelaIRRF->proximo();
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTFolhaPagamentoTabelaIrrf );

    return $obErro;
}

/**
    * Método listar
    * @access Private
*/
function listar(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoTabelaIrrf.class.php");
    $obTFolhaPagamentoTabelaIrrf = new TFolhaPagamentoTabelaIrrf;
    $obErro = $obTFolhaPagamentoTabelaIrrf->recuperaRelacionamento($rsRecordSet,$stFiltro,$stOrder,$boTransacao);

    return $obErro;
}

/**
    * Método listarIRRF
    * @access Private
*/
function listarIRRF(&$rsRecordSet,$boTransacao="")
{
    if ( $this->getVigencia() ) {
        $stFiltro .= " AND  TO_CHAR(vigencia,'dd/mm/yyyy') = '".$this->getVigencia()."'";
    }
    if ( $this->getCodTabela() ) {
        $stFiltro .= " AND tabela_irrf.cod_tabela = ". $this->getCodTabela();
    }
    if ( $this->getTimestamp() ) {
        $stFiltro .= " AND tabela_irrf.timestamp = '". $this->getTimestamp()."'";
    }
    $obErro = $this->listar($rsRecordSet,$stFiltro,$stOrdem,$boTransacao);

    return $obErro;
}

/**
    * Método listarCID
    * @access Private
*/
function listarCID(&$rsRecordSet,$boTransacao="")
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoTabelaIrrfCid.class.php");
    $obTFolhaPagamentoTabelaIrrfCid = new TFolhaPagamentoTabelaIrrfCid;
    if ( $this->getCodTabela() ) {
        $stFiltro .= " AND tabela_irrf_cid.cod_tabela = ". $this->getCodTabela();
    }
    if ( $this->getTimestamp() ) {
        $stFiltro .= " AND tabela_irrf_cid.timestamp = '". $this->getTimestamp()."'";
    }
    $obErro = $obTFolhaPagamentoTabelaIrrfCid->recuperaRelacionamento($rsRecordSet,$stFiltro,$stOrdem,$boTransacao);

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
    $this->roRFolhaPagamentoEvento->setRORFolhaPagamentoIRRF( $this );
    $this->arRFolhaPagamentoEventoIRRF[] = new RFolhaPagamentoEventoIRRF($this,$this->roRFolhaPagamentoEvento);
    $this->roRFolhaPagamentoEventoIRRF   = &$this->arRFolhaPagamentoEventoIRRF[ count($this->arRFolhaPagamentoEventoIRRF)-1 ];
    $this->roRFolhaPagamentoEvento->setRORFolhaPagamentoEventoIRRF( $this->roRFolhaPagamentoEventoIRRF );
}

/**
    * Método addRPessoalCID
    * @acess Public
*/
function addRPessoalCID()
{
    $this->arRPessoalCID[] = new RPessoalCID();
    $this->roRPessoalCID   = &$this->arRPessoalCID[ count($this->arRPessoalCID)-1 ];
    $this->roRPessoalCID->setRORFolhaPagamentoIRRF($this);
}

/**
    * Método addRFolhaPagamentoFaixaIRRF
    * @acess Public
*/
function addRFolhaPagamentoFaixaIRRF()
{
    $this->arRFolhaPagamentoFaixaIRRF[] = new RFolhaPagamentoFaixaIRRF($this);
    $this->roRFolhaPagamentoFaixaIRRF   = &$this->arRFolhaPagamentoFaixaIRRF[ count($this->arRFolhaPagamentoFaixaIRRF)-1 ];
}

/**
    * Método addEventoAjudaCusto
    * @acess Public
*/
function addEventoAjudaCusto($cod_evento)
{
    $this->arEventoAjudaCusto[] = $cod_evento;
    $this->roEventoAjudaCusto   = &$this->arEventoAjudaCusto[ count($this->arEventoAjudaCusto)-1 ];
}

}
?>
