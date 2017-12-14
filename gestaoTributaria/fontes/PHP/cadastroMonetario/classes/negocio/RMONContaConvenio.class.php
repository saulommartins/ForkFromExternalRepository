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
  * Classe de conta corrente convênio para cadastro monetário
  * Data de criação : 09/11/2005

  * @author Analista: Fábio Bertoldi
  * @author Programador: Diego Bueno Coelho

  * @package URBEM
  * @subpackage Regra

    * $Id: RMONContaConvenio.class.php 59612 2014-09-02 12:00:51Z gelson $

  Caso de uso: uc-05.05.04
**/

/*
$Log$
Revision 1.5  2007/03/08 15:22:32  rodrigo
Bug #8418#

Revision 1.4  2007/02/07 15:56:52  cercato
alteracoes para o convenio trabalhar com numero de variacao.

Revision 1.3  2006/09/15 14:46:22  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GT_MON_MAPEAMENTO."TMONContaCorrenteConvenio.class.php");

class RMONContaConvenio
{
/**
    * @access Private
    * @var Integer
*/
var $inCodigoConta;
/**
    * @access Private
    * @var Integer
*/
var $inCodigoBanco;
/**
    * @access Private
    * @var Integer
*/
var $inCodigoAgencia;
/**
    * @access Private
    * @var Integer
*/
var $inCodigoConvenio;

/**
    * @access Private
    * @var Integer
*/
var $inVariacao;

/**
    * @access Private
    * @var Object
*/
var $obTMONContaConvenio;

// SET
function setCodigoConvenio($valor) { $this->inCodigoConvenio = $valor; }
function setCodigoAgencia($valor) { $this->inCodigoAgencia  = $valor; }
function setCodigoBanco($valor) { $this->inCodigoBanco    = $valor; }
function setCodigoConta($valor) { $this->inCodigoConta    = $valor; }
function setVariacao($valor) { $this->inVariacao       = $valor; }

// GET
function getCodigoConvenio() { return $this->inCodigoConvenio;  }
function getCodigoAgencia() { return $this->inCodigoAgencia;   }
function getCodigoBanco() { return $this->inCodigoBanco;     }
function getCodigoConta() { return $this->inCodigoConta;     }
function getVariacao() { return $this->inVariacao;        }

//METODO CONSTRUTOR
/**
     * Método construtor
     * @access Private
*/
function RMONContaConvenio()
{
    $this->obTransacao = new Transacao;
}

/**
* Inclui os dados setados na tabela Conta_corrente_convenio
* @access Public
* @param  Object $obTransacao Parametro Transacao
* @return Object Objeto Erro
*/
function incluirContaConvenio($boTransacao = "")
{
    $this->obTMONContaConvenio = new TMONContaCorrenteConvenio;
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTMONContaConvenio->setDado( "cod_convenio", $this->getCodigoConvenio() );
        $this->obTMONContaConvenio->setDado( "cod_agencia", $this->getCodigoAgencia() );
        $this->obTMONContaConvenio->setDado( "cod_banco", $this->getCodigoBanco() );
        $this->obTMONContaConvenio->setDado( "cod_conta_corrente", $this->getCodigoConta() );
        if (!($this->getVariacao()==null | $this->getVariacao()=="")) {
            $this->obTMONContaConvenio->setDado( "variacao", $this->getVariacao() );
        }

        $obErro = $this->obTMONContaConvenio->inclusao( $boTransacao );
//      $this->obTMONContaConvenio->debug(); exit;
    }

    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTMONContaConvenio );

    return $obErro;
}

/**
* Exclui os dados setados na tabela Conta_corrente_convenio
* @access Public
* @param  Object $obTransacao Parametro Transacao
* @return Object Objeto Erro
*/
function excluirContaConvenio($boTransacao = "")
{
$this->obTMONContaConvenio = new TMONContaCorrenteConvenio;

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {

        $this->obTMONContaConvenio->setDado( "cod_convenio", $this->getCodigoConvenio() );
        $this->obTMONContaConvenio->setDado( "cod_agencia", $this->getCodigoAgencia() );
        $this->obTMONContaConvenio->setDado( "cod_banco", $this->getCodigoBanco() );
        $this->obTMONContaConvenio->setDado( "cod_conta_corrente", $this->getCodigoConta() );

        $obErro = $this->obTMONContaConvenio->exclusao( $boTransacao );
        //$this->obTMONContaConvenio->debug(); exit;
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTMONContaConvenio );

    return $obErro;
}

/**
    * Verifica se já existe um convenio com o mesmo codigo em determinado banco
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function verificaConvenioConta(&$rsRecordSet, $boTransacao = '')
{
    $this->obTMONConvenioConta = new TMONConvenioConta;

    if ($this->inNumeroConvenio) {
        $stFiltro .= " and num_convenio = ".$this->inNumeroConvenio."  ";
    }

    $obErro = $this->obTMONConvenio->recuperaTodos( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );
    $this->obTMONConvenio->debug();

    return $obErro;
}

}
