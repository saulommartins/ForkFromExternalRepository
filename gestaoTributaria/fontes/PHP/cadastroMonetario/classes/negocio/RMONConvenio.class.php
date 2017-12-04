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
  * Classe de convênio para cadastro monetário
  * Data de criação : 09/11/2005

  * @author Analista: Fábio Bertoldi
  * @author Programador: Tonismar R. Bernardo

  * @package URBEM
  * @subpackage Regra

    * $Id: RMONConvenio.class.php 59612 2014-09-02 12:00:51Z gelson $

  Caso de uso: uc-05.05.04
**/

/*
$Log$
Revision 1.25  2007/08/06 19:00:06  cercato
Bug#9792#

Revision 1.24  2007/02/07 15:56:52  cercato
alteracoes para o convenio trabalhar com numero de variacao.

Revision 1.23  2006/09/15 14:46:22  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GT_MON_MAPEAMENTO."TMONConvenio.class.php");
include_once( CAM_GT_MON_MAPEAMENTO."TMONTipoConvenio.class.php");
include_once( CAM_GT_MON_MAPEAMENTO."TMONBanco.class.php");
include_once( CAM_GT_MON_MAPEAMENTO."TMONContaCorrenteConvenio.class.php");
include_once( CAM_GT_MON_NEGOCIO."RMONBanco.class.php");
include_once( CAM_GT_MON_NEGOCIO."RMONContaConvenio.class.php");
include_once( CAM_GA_ADM_NEGOCIO."RFuncao.class.php");

class RMONConvenio
{
/**
    * @access Private
    * @var Integer
*/
var  $inCodigoConvenio ;
/**
    * @access Private
    * @var Integer
*/
var $inNumeroConvenio;
/**
    * @access Private
    * @var Integer
*/
var $inTipoConvenio;
/**
    * @access Private
    * @var Integer
*/
var $inCodigoConta;
/**
    * @access Private
    * @var Integer
*/
var $inNumeroConta;
/**
    * @access Private
    * @var String
*/
var $stNomeTipo;
/**
    * @access Private
    * @var Float
*/
var $flTaxaBancaria;
/**
    * @access Private
    * @var Float
*/
var $flCedente;
/**
    * @access Private
    * @var Integer
*/
var $inCodBanco;
/**
    * @access Private
    * @var Integer
*/
var $inCodigoCarteira;
/**
    * @access Private
    * @var Integer
*/
var $inCodAgencia;
/**
    * @access Private
    * @var boolean
*/
var $boConvenioConsolidacao;
/**
    * @access Private
    * @var Array
*/
var $arContas;
/**
    * @access Private
    * @var Object
*/
var $obRFuncao;
/**
    * @access Private
    * @var Object
*/
var $obTMONConvenio;
/**
    * @access Private
    * @var Object
*/
var $obTMONTipoConvenio;
/**
    * @access Private
    * @var Object
*/
var $obTMONContaConvenio;

// SET
function setCodigoConvenio($valor) { $this->inCodigoConvenio = $valor; }
function setNumeroConvenio($valor) { $this->inNumeroConvenio = $valor; }
function setCodigoCarteira($valor) { $this->inCodigoCarteira = $valor; }
function setTipoConvenio($valor) { $this->inTipoConvenio   = $valor; }
function setTaxaBancaria($valor) { $this->flTaxaBancaria   = $valor; }
function setCedente($valor) { $this->flCedente        = $valor; }
function setNomeTipo($valor) { $this->stNomeTipo       = $valor; }
function setCodigoBanco($valor) { $this->inCodBanco       = $valor; }
function setCodigoAgencia($valor) { $this->inCodAgencia     = $valor; }
function setCodigoConta($valor) { $this->inCodigoConta    = $valor; }
function setNumeroConta($valor) { $this->inNumeroConta    = $valor; }
function setContas($valor) { $this->arContas         = $valor; }
function setCodigoConvenioConsolidacao($valor) { $this->boConvenioConsolidacao = $valor; }

// GET
function getCodigoConvenio() { return $this->inCodigoConvenio;  }
function getNumeroConvenio() { return $this->inNumeroConvenio;  }
function getCodigoCarteira() { return $this->inCodigoCarteira;  }
function getTipoConvenio() { return $this->inTipoConvenio;    }
function getTaxaBancaria() { return $this->flTaxaBancaria;    }
function getCedente() { return $this->flCedente;         }
function getNomeTipo() { return $this->stNomeTipo;        }
function getCodigoBanco() { return $this->inCodBanco;        }
function getCodigoAgencia() { return $this->inCodAgencia;      }
function getCodigoConta() { return $this->inCodigoConta;     }
function getNumeroConta() { return $this->inNumeroConta;     }
function getContas() { return $this->arContas;          }
function getCodigoConvenioConsolidacao() { return $this->boConvenioConsolidacao; }

//METODO CONSTRUTOR
/**
     * Método construtor
     * @access Private
*/
function RMONConvenio()
{
    $this->obTransacao = new Transacao;
    $this->obRMONBanco = new RMONBanco;
    $this->obRFuncao   = new RFuncao;
}

/**
* Inclui os dados setados na tabela Convenio e/ou Conta_corrente_convenio
* @access Public
* @param  Object $obTransacao Parametro Transacao
* @return Object Objeto Erro
*/
function incluirConvenio($boTransacao = "")
{
    $this->obTMONConvenio = new TMONConvenio;
    $this->obRMONContaConvenio = new RMONContaConvenio;

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obErro = $this->obTMONConvenio->proximoCod( $this->inCodigoConvenio, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obTMONConvenio->setDado( "cod_convenio", $this->getCodigoConvenio() );
            $this->obTMONConvenio->setDado( "num_convenio", $this->getNumeroConvenio() );
            $this->obTMONConvenio->setDado( "cod_tipo", $this->getTipoConvenio() );
            $this->obTMONConvenio->setDado( "taxa_bancaria", $this->getTaxaBancaria() );
            $this->obTMONConvenio->setDado( "cedente", $this->getCedente() );
            $obErro = $this->obTMONConvenio->inclusao( $boTransacao );

            //insercao das contas correntes
            if ( !$obErro->ocorreu() ) {
                $contas = array();
                $contas = $this->getContas();
                $nContas = count ( $contas );

                if ($nContas > 0) {
                    $this->obRMONContaConvenio->setCodigoConvenio( $this->getCodigoConvenio() );
                    $this->obRMONContaConvenio->setCodigoBanco   ( $this->getCodigoBanco() );
                    $this->obRMONContaConvenio->setCodigoAgencia ( $this->getCodigoAgencia() );
                    $cont = 0;
                    while ($cont < $nContas) {
                        $this->obRMONContaConvenio->setCodigoConta ( $contas[$cont]['cod_conta_corrente'] );
                        $this->obRMONContaConvenio->setVariacao( $contas[$cont]['num_variacao'] );
                        $this->obRMONContaConvenio->incluirContaConvenio( $boTransacao );
                        $cont++;
                    }
                }
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTMONConvenio );

    return $obErro;
}

/**
* Inclui os dados setados na tabela Convenio e/ou Conta_corrente_convenio
* @access Public
* @param  Object $obTransacao Parametro Transacao
* @return Object Objeto Erro
*/
function alterarConvenio($boTransacao = "")
{
    $this->obTMONConvenio = new TMONConvenio;
    $this->obRMONContaConvenio = new RMONContaConvenio;

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTMONConvenio->setDado( "cod_convenio", $this->getCodigoConvenio() );
        $this->obTMONConvenio->setDado( "num_convenio", $this->getNumeroConvenio() );
        $this->obTMONConvenio->setDado( "cod_tipo", $this->getTipoConvenio() );
        $this->obTMONConvenio->setDado( "taxa_bancaria", $this->getTaxaBancaria() );
        $this->obTMONConvenio->setDado( "cedente", $this->getCedente() );
        $obErro = $this->obTMONConvenio->alteracao( $boTransacao );

        //insercao das contas correntes
        if ( !$obErro->ocorreu() ) {
            $this->obRMONContaConvenio->setCodigoConvenio($this->getCodigoConvenio());
            $this->obRMONContaConvenio->excluirContaConvenio();

            $contas = array();
            $contas = $this->getContas();
            $nContas = count ( $contas );
            if ($nContas > 0) {
                $this->obRMONContaConvenio->setCodigoBanco   ( $this->getCodigoBanco() );
                $this->obRMONContaConvenio->setCodigoAgencia ( $this->getCodigoAgencia() );

                $cont = 0;
                while ($cont < $nContas) {
                    $this->obRMONContaConvenio->setCodigoConta ( $contas[$cont]['cod_conta_corrente'] );
                    $this->obRMONContaConvenio->setVariacao( $contas[$cont]['num_variacao'] );
                    $this->obRMONContaConvenio->incluirContaConvenio( $boTransacao );
                    $cont++;
                }
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTMONConvenio );

    return $obErro;
}

/**
* Exclui os dados setados na tabela Convenio e/ou Conta_corrente_convenio
* @access Public
* @param  Object $obTransacao Parametro Transacao
* @return Object Objeto Erro
*/
function excluirConvenio($boTransacao = "")
{
    $this->obTMONConvenio = new TMONConvenio;
    $this->obRMONContaConvenio = new RMONContaConvenio;

    $this->verificaConvenioReferencia( $rsReferencia );
    if ( !$rsReferencia->Eof() ) {
        $obErro = new Erro;
        $obErro->setDescricao("Convênio ".$rsReferencia->getCampo("num_convenio")." está sendo utilizado pelo sistema!");

        return $obErro;
    }

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obRMONContaConvenio->setCodigoConvenio( $this->getCodigoConvenio() );
        $this->obRMONContaConvenio->excluirContaConvenio();
        if ( !$obErro->ocorreu() ) {
            $this->obTMONConvenio->setDado( "cod_convenio", $this->getCodigoConvenio() );
            $this->obTMONConvenio->setDado( "num_convenio", $this->getNumeroConvenio() );
            $this->obTMONConvenio->setDado( "cod_tipo", $this->getTipoConvenio() );
            $this->obTMONConvenio->setDado( "taxa_bancaria", $this->getTaxaBancaria() );
            $this->obTMONConvenio->setDado( "cedente", $this->getCedente() );
            $obErro = $this->obTMONConvenio->exclusao( $boTransacao );

        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTMONConvenio );

    return $obErro;
}

/**
    * Listar todos os convenios de acordo com o filtro
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarConvenio(&$rsRecordSet, $boTransacao = '')
{
    $this->obTMONConvenio = new TMONConvenio;

    if ( $this->getCodigoConvenio() ) {
        $stFiltro .= " con.cod_convenio = ". $this->getCodigoConvenio() . " AND ";
    }
    if ( $this->getCodigoConta() ) {
        $stFiltro .= " ccc.cod_conta_corrente = ". $this->getCodigoConta(). " AND ";
    }
    if ( $this->getNumeroConvenio() ) {
        $stFiltro .= " con.num_convenio = ". $this->getNumeroConvenio() . " AND ";
    }
    if ( $this->getTipoConvenio()   ) {
        $stFiltro .= " tc.cod_tipo = ". $this->getTipoConvenio() . " AND ";
    }
    if ( $this->getCodigoBanco() ) {
        $stFiltro .= " ban.cod_banco = ".$this->getCodigoBanco(). " AND ";
    }
    if ( $this->getCodigoCarteira() ) {
        $stFiltro .= " ca.cod_carteira = ".$this->getCodigoCarteira()." AND ";
    }

    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen( $stFiltro ) - 4 );
    }

    $obErro = $this->obTMONConvenio->recuperaConvenioBanco( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

/**
    * Listar todos os tipos convenios de acordo com o filtro
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarTipoConvenio(&$rsRecordSet, $boTransacao = '')
{
    $this->obTMONTipoConvenio = new TMONTipoConvenio;

    if ( $this->getTipoConvenio() ) {
        $stFiltro .= " cod_tipo = ". $this->getTipoConvenio() ." AND ";
    }
    if ( $this->getNomeTipo()   ) {
        $stFiltro .= " to_upper(nom_tipo) like to_upper('". $this->getNomeTipo() ."%') AND ";
    }

    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen( $stFiltro ) - 4 );
    }

    $obErro = $this->obTMONTipoConvenio->recuperaTodos( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

/**
    * Listar todos os tipos convenios de acordo com o filtro
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarConvenioBanco(&$rsRecordSet, $boTransacao = '')
{
    $this->obTMONConvenio = new TMONConvenio;
    $stFiltro = "";

    if ( $this->getCodigoConvenio() ) {
        $stFiltro .= " con.cod_convenio = ".$this->getCodigoConvenio()." AND ";
    }

    if ( $this->getTipoConvenio() ) {
        $stFiltro .= " tc.cod_tipo = ". $this->getTipoConvenio() . " AND ";
    }

    if ( $this->getNumeroConvenio()) {
        $stFiltro .= " con.num_convenio = '".$this->getNumeroConvenio() ."' AND ";
    }

    if ( $this->obRMONBanco->getNumBanco() ) {
        $stFiltro .= " ban.num_banco = '".$this->obRMONBanco->getNumBanco(). "' AND ";
    }

    if ( $this->getCodigoBanco() ) {
        $stFiltro .= " ccc.cod_banco = ". $this->getCodigoBanco() ." AND ";
    }

    if ( $this->getCodigoCarteira()) {
        $stFiltro .= " ca.cod_carteira = ".$this->getCodigoCarteira() ." AND ";
    }

    if (isset($stFiltro) && !empty($stFiltro)) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen( $stFiltro ) - 4 );
    }

    if (!isset($stFiltro)) {
        $stFiltro="";
    }

    if (!isset($stOrdem)) {
        $stOrdem="";
    }

    $obErro = $this->obTMONConvenio->recuperaConvenioBanco( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

function listarConvenioBancoGF(&$rsRecordSet, $boTransacao = '')
{
    ;

    $this->obTMONConvenio = new TMONConvenio;

    if ( $this->getCodigoConvenio() ) {
        $stFiltro .= " con.cod_convenio = ".$this->getCodigoConvenio()." AND ";
    }
    if ( $this->getTipoConvenio() ) {
        $stFiltro .= " tc.cod_tipo = ". $this->getTipoConvenio() . " AND ";
    }
    if ( $this->getNumeroConvenio() ) {
        $stFiltro .= " con.num_convenio = '".$this->getNumeroConvenio() ."' AND ";
    }
    if ( $this->obRMONBanco->getNumBanco() ) {
        $stFiltro .= " ban.num_banco = '".$this->obRMONBanco->getNumBanco(). "' AND ";
    }
    if ( $this->getCodigoBanco() ) {
        $stFiltro .= " ccc.cod_banco = ". $this->getCodigoBanco() ." AND ";
    }
    if ( $this->getCodigoCarteira() ) {
        $stFiltro .= " ca.cod_carteira = ".$this->getCodigoCarteira() ." AND ";
    }

    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen( $stFiltro ) - 4 );
    }

    $obErro = $this->obTMONConvenio->recuperaConvenioBancoGF( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

/**
    * Listar todos as contas jah atreladas ao determinado convenio
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarConvenioContas(&$rsRecordSet, $boTransacao = '')
{
    $this->obTMONConvenio = new TMONConvenio;

    if ( $this->getCodigoConvenio() ) {
        $stFiltro .= " cod_convenio = ".$this->getCodigoConvenio()." AND ";
    }
    if ( $this->getCodigoBanco() ) {
        $stFiltro .= " cod_convenio = ".$this->getCodigoBanco()." AND ";
    }

    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen( $stFiltro ) - 4 );
    }
    $obErro = $this->obTMONConvenio->recuperaConvenioContas( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

/**
    * Verifica se já existe um convenio com o mesmo codigo em determinado banco
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function verificaConvenioBanco(&$rsRecordSet, $boTransacao = '')
{
    $this->obTMONConvenio = new TMONConvenio;

    if ( $this->getNumeroConvenio() ) {
        $stFiltro .= " c.num_convenio = ". $this->getNumeroConvenio() ." AND ";
    }
    if ( $this->getCodigoConvenio() ) {
        $stFiltro .= " c.cod_convenio = ".$this->getCodigoConvenio()." AND ";
    }
    if ( $this->getCodigoConvenioConsolidacao () ) {
        $stFiltro .= " c.cod_convenio = '0' AND ";
    }

    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen( $stFiltro ) - 4 );
    }

    $obErro = $this->obTMONConvenio->recuperaTodos( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

function verificaConvenioReferencia(&$rsRecordSet, $boTransacao = '')
{
    $this->obTMONConvenio = new TMONConvenio;

    if ( $this->getNumeroConvenio() ) {
        $stFiltro .= " AND mc.num_convenio = ". $this->getNumeroConvenio();
    }
    if ( $this->getCodigoConvenio() ) {
        $stFiltro .= " AND mc.cod_convenio = ".$this->getCodigoConvenio();
    }

    $obErro = $this->obTMONConvenio->verificaReferenciaConvenio( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

}
