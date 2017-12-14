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
    * Classe de regra de negócio para RFolhaPagamentoFaixaIRRF
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

class RFolhaPagamentoFaixaIRRF
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
var $inCodFaixa;
/**
   * @access Private
   * @var Numeric
*/
var $nuInicial;
/**
   * @access Private
   * @var Numeric
*/
var $nuFinal;
/**
   * @access Private
   * @var Numeric
*/
var $nuAliquota;
/**
   * @access Private
   * @var Numeric
*/
var $nuParcelaDeduzir;
/**
   * @access Private
   * @var Object
*/
var $roRFolhaPagamentoIRRF;

/**
    * @access Public
    * @param Object $valor
*/
function setTransacao($valor) { $this->obTransacao            = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodFaixa($valor) { $this->inCodFaixa             = $valor; }
/**
    * @access Public
    * @param Numeric $valor
*/
function setInicial($valor) { $this->nuInicial              = $valor; }
/**
    * @access Public
    * @param Numeric $valor
*/
function setFinal($valor) { $this->nuFinal                = $valor; }
/**
    * @access Public
    * @param Numeric $valor
*/
function setAliquota($valor) { $this->nuAliquota             = $valor; }
/**
    * @access Public
    * @param Numeric $valor
*/
function setParcelaDeduzir($valor) { $this->nuParcelaDeduzir       = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRORFolhaPagamentoIRRF(&$valor) { $this->roRFolhaPagamentoIRRF = &$valor; }

/**
    * @access Public
    * @return Object
*/
function getTransacao() { return $this->obTransacao;            }
/**
    * @access Public
    * @return Integer
*/
function getCodFaixa() { return $this->inCodFaixa;             }
/**
    * @access Public
    * @return Numeric
*/
function getInicial() { return $this->nuInicial;              }
/**
    * @access Public
    * @return Numeric
*/
function getFinal() { return $this->nuFinal;                }
/**
    * @access Public
    * @return Numeric
*/
function getAliquota() { return $this->nuAliquota;             }
/**
    * @access Public
    * @return Numeric
*/
function getParcelaDeduzir() { return $this->nuParcelaDeduzir;       }
/**
    * @access Public
    * @return Object
*/
function getRORFolhaPagamentoIRRF() { return $this->roRFolhaPagamentoIRRF;  }

/**
     * Método construtor
     * @access Private
*/
function RFolhaPagamentoFaixaIRRF(&$roRFolhaPagamentoIRRF)
{
    $this->setTransacao             ( new Transacao             );
    $this->setRORFolhaPagamentoIRRF ( $roRFolhaPagamentoIRRF    );
}

/**
    * Inclui
    * @access Public
*/
function incluirFaixaIRRF($boTransacao)
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoFaixaDescontoIrrf.class.php");
    $obTFolhaPagamentoFaixaDescontoIrrf = new TFolhaPagamentoFaixaDescontoIrrf;
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if (!$obErro->ocorreu()) {
        $stCampoCod         = $obTFolhaPagamentoFaixaDescontoIrrf->getCampoCod();
        $stComplementoChave = $obTFolhaPagamentoFaixaDescontoIrrf->getComplementoChave();
        $obTFolhaPagamentoFaixaDescontoIrrf->setCampoCod('cod_faixa');
        $obTFolhaPagamentoFaixaDescontoIrrf->setComplementoChave('');
        $obErro = $obTFolhaPagamentoFaixaDescontoIrrf->proximoCod($inCodFaixa,$boTransacao);
        $this->setCodFaixa($inCodFaixa);
        $obTFolhaPagamentoFaixaDescontoIrrf->setCampoCod($stCampoCod);
        $obTFolhaPagamentoFaixaDescontoIrrf->setComplementoChave($stComplementoChave);
        if (!$obErro->ocorreu()) {
            $obTFolhaPagamentoFaixaDescontoIrrf->setDado('cod_faixa'        ,$this->getCodFaixa()                           );
            $obTFolhaPagamentoFaixaDescontoIrrf->setDado('cod_tabela'       ,$this->roRFolhaPagamentoIRRF->getCodTabela()   );
            $obTFolhaPagamentoFaixaDescontoIrrf->setDado('timestamp'        ,$this->roRFolhaPagamentoIRRF->getTimestamp()   );
            $obTFolhaPagamentoFaixaDescontoIrrf->setDado('vl_inicial'       ,$this->getInicial()                            );
            $obTFolhaPagamentoFaixaDescontoIrrf->setDado('vl_final'         ,$this->getFinal()                              );
            $obTFolhaPagamentoFaixaDescontoIrrf->setDado('aliquota'         ,$this->getAliquota()                           );
            $obTFolhaPagamentoFaixaDescontoIrrf->setDado('parcela_deduzir'  ,$this->getParcelaDeduzir()                     );
            $obErro = $obTFolhaPagamentoFaixaDescontoIrrf->inclusao($boTransacao);
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTFolhaPagamentoFaixaDescontoIrrf );

    return $obErro;
}

/**
    * Excluir
    * @access Public
*/
function excluirFaixaIRRF($boTransacao="")
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoFaixaDescontoIrrf.class.php");
    $obTFolhaPagamentoFaixaDescontoIrrf = new TFolhaPagamentoFaixaDescontoIrrf;
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if (!$obErro->ocorreu()) {
        $obTFolhaPagamentoFaixaDescontoIrrf->setDado('cod_tabela', $this->roRFolhaPagamentoIRRF->getCodTabela()    );
        $obTFolhaPagamentoFaixaDescontoIrrf->setDado('timestamp' , $this->roRFolhaPagamentoIRRF->getTimestamp()    );
        $obErro = $obTFolhaPagamentoFaixaDescontoIrrf->exclusao($boTransacao);
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTFolhaPagamentoFaixaDescontoIrrf );

    return $obErro;
}

/**
    * Método listar
    * @access Private
*/
function listar(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoFaixaDescontoIrrf.class.php");
    $obTFolhaPagamentoFaixaDescontoIrrf = new TFolhaPagamentoFaixaDescontoIrrf;
    $obErro = $obTFolhaPagamentoFaixaDescontoIrrf->recuperaRelacionamento($rsRecordSet,$stFiltro,$stOrder,$boTransacao);

    return $obErro;
}

/**
    * Método listarFaixaIRRF
    * @access Public
*/
function listarFaixaIRRF(&$rsRecordSet,$boTransacao="")
{
    if ( $this->roRFolhaPagamentoIRRF->getCodTabela() ) {
        $stFiltro .= " AND faixa_desconto_irrf.cod_tabela = ".$this->roRFolhaPagamentoIRRF->getCodTabela();
    }
    $obErro = $this->listar($rsRecordSet,$stFiltro,$stOrdem,$boTransacao);

    return $obErro;
}
}
?>
