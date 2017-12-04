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
    * Classe de regra de negócio para RFolhaPagamentoFGTSCategoria
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

class RFolhaPagamentoFGTSCategoria
{
/**
   * @access Private
   * @var Object
*/
var $obTransacao;
/**
   * @access Private
   * @var Numeric
*/
var $nuAliquotaDeposito;
/**
   * @access Private
   * @var Numeric
*/
var $nuAliquotaContribuicao;
/**
   * @access Private
   * @var Object
*/
var $roRFolhaPagamentoFGTS;
/**
   * @access Private
   * @var Object
*/
var $roRPessoalCategoria;

/**
    * @access Public
    * @param Object $valor
*/
function setTransacao($valor) { $this->obTransacao             = $valor; }
/**
    * @access Public
    * @param Numeric $valor
*/
function setAliquotaDeposito($valor) { $this->nuAliquotaDeposito      = $valor; }
/**
    * @access Public
    * @param Numeric $valor
*/
function setAliquotaContribuicao($valor) { $this->nuAliquotaContribuicao  = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRORFolhaPagamentoFGTS(&$valor) { $this->roRFolhaPagamentoFGTS  = &$valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRORPessoalCategoria(&$valor) { $this->roRPessoalCategoria    = &$valor; }

/**
    * @access Public
    * @return Object
*/
function getTransacao() { return $this->obTransacao;             }
/**
    * @access Public
    * @return Numeric
*/
function getAliquotaDeposito() { return $this->nuAliquotaDeposito;      }
/**
    * @access Public
    * @return Numeric
*/
function getAliquotaContribuicao() { return $this->nuAliquotaContribuicao;  }
/**
    * @access Public
    * @return Object
*/
function getRORFolhaPagamentoFGTS() { return $this->roRFolhaPagamentoFGTS;   }
/**
    * @access Public
    * @return Object
*/
function getRORPessoalCategoria() { return $this->roRPessoalCategoria;     }

/**
     * Método construtor
     * @access Private
*/
function RFolhaPagamentoFGTSCategoria(&$roRFolhaPagamentoFGTS,&$roRPessoalCategoria)
{
    $this->setTransacao                 ( new Transacao                         );
    $this->setRORFolhaPagamentoFGTS     ( $roRFolhaPagamentoFGTS                );
    $this->setRORPessoalCategoria       ( $roRPessoalCategoria                  );
}

/**
    * Inclui
    * @access Public
*/
function incluirFGTSCategoria($boTransacao="")
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoFgtsCategoria.class.php");
    $obTFolhaPagamentoFGTSCategoria = new TFolhaPagamentoFgtsCategoria;
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if (!$obErro->ocorreu()) {
        $obTFolhaPagamentoFGTSCategoria->setDado('timestamp'             ,$this->roRFolhaPagamentoFGTS->getTimestamp()   );
        $obTFolhaPagamentoFGTSCategoria->setDado('cod_categoria'         ,$this->roRPessoalCategoria->getCodCategoria()  );
        $obTFolhaPagamentoFGTSCategoria->setDado('cod_fgts'              ,$this->roRFolhaPagamentoFGTS->getCodFGTS()     );
        $obTFolhaPagamentoFGTSCategoria->setDado('aliquota_deposito'     ,$this->getAliquotaDeposito()                   );
        $obTFolhaPagamentoFGTSCategoria->setDado('aliquota_contribuicao' ,$this->getAliquotaContribuicao()               );
        $obErro = $obTFolhaPagamentoFGTSCategoria->inclusao($boTransacao);
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTFolhaPagamentoFGTSCategoria );

    return $obErro;
}

/**
    * Método listar
    * @access Private
*/
function listar(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoFgtsCategoria.class.php");
    $obTFolhaPagamentoFgtsCategoria = new TFolhaPagamentoFgtsCategoria;
    $obErro = $obTFolhaPagamentoFgtsCategoria->recuperaRelacionamento($rsRecordSet,$stFiltro,$stOrder,$boTransacao);

    return $obErro;
}

/**
    * Método listarFGTSCategoria
    * @access Public
*/
function listarFGTSCategoria(&$rsRecordSet,$boTransacao="")
{
    if ( $this->roRFolhaPagamentoFGTS->getCodFGTS() ) {
        $stFiltro .= " AND fgts_categoria.cod_fgts = ".$this->roRFolhaPagamentoFGTS->getCodFGTS();
    }
    $obErro = $this->listar($rsRecordSet,$stFiltro,$stOrdem,$boTransacao);

    return $obErro;
}

}
?>
