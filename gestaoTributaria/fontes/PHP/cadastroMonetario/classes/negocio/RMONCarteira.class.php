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
  * Classe de Carteira
  * Data de criação : 10/11/2005

  * @author Analista: Fábio Bertoldi
  * @author Programador: Tonismar R. Bernardo

    * $Id: RMONCarteira.class.php 59612 2014-09-02 12:00:51Z gelson $

  Caso de uso: uc-05.05.05
**/

/*
$Log$
Revision 1.9  2006/09/15 14:46:22  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_MON_MAPEAMENTO."TMONCarteira.class.php");
include_once ( CAM_GT_MON_NEGOCIO."RMONConvenio.class.php");

class RMONCarteira
{
/**
    * @access Private
    * @var Integer
*/
var $inCodigoCarteira;
/**
    * @access Private
    * @var Integer
*/
var $inNumeroCarteira;
/**
    * @access Private
    * @var Float
*/
var $flVariacao;
/**
    * @access Private
    * @var Object
*/
var $obTransacao;
/**
    * @access Private
    * @var Object
*/
var $obRMONConvenio;
/**
    * @access Private
    * @var Object
*/
var $obTMONCarteira;

function setCodigoCarteira($valor) { $this->inCodigoCarteira = $valor; }
function setNumeroCarteira($valor) { $this->inNumeroCarteira = $valor; }
function setVariacao($valor) { $this->flVariacao       = $valor; }

function getCodigoCarteira() { return $this->inCodigoCarteira;   }
function getNumeroCarteira() { return $this->inNumeroCarteira;   }
function getVariacao() { return $this->flVariacao;         }

function RMONCarteira()
{
    $this->obTransacao    = new Transacao;
    $this->obRMONConvenio = new RMONConvenio;
}

/**
* Inclui os dados setados na tabela Carteira
* @access Public
* @param  Object $obTransacao Parametro Transacao
* @return Object Objeto Erro
*/
function incluirCarteira($boTransacao = "")
{
    $this->obTransacao = new Transacao;
    $this->obTMONCarteira = new TMONCarteira;
    $boFlagTransacao = false;

    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {

        $obErro = $this->verificaCarteira();
        if ( !$obErro->ocorreu() ) {

            $obErro = $this->obTMONCarteira->proximoCod( $this->inCodigoCarteira,$boTransacao );
            if ( !$obErro->ocorreu() ) {

                $this->obTMONCarteira->setDado( "cod_carteira", $this->getCodigoCarteira() );
                $this->obTMONCarteira->setDado( "num_carteira", $this->getNumeroCarteira() );
                $this->obTMONCarteira->setDado( "cod_convenio", $this->obRMONConvenio->getCodigoConvenio() );
                $this->obTMONCarteira->setDado( "variacao", $this->getVariacao() );
                $obErro = $this->obTMONCarteira->inclusao( $boTransacao );

            }
        }
    }

    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTMONCarteira );

    return $obErro;

}

/**
* Exclui os dados setados na tabela Carteira
* @access Public
* @param  Object $obTransacao Parametro Transacao
* @return Object Objeto Erro
*/
function excluirCarteira($boTransacao = "")
{
    $this->obTMONCarteira = new TMONCarteira;
    $boFlagTransacao = false;

    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
            $this->obTMONCarteira->setDado( "cod_carteira", $this->getCodigoCarteira() );
            $this->obTMONCarteira->setDado( "num_carteira", $this->getNumeroCarteira() );
            $this->obTMONCarteira->setDado( "cod_convenio", $this->obRMONConvenio->getCodigoConvenio() );
            $this->obTMONCarteira->setDado( "variacao", $this->getVariacao() );
            $obErro = $this->obTMONCarteira->exclusao( $boTransacao );
            //$this->obTMONCarteira->debug(); exit;
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTMONCarteira );

    return $obErro;

}

/**
* Altera os dados setados na tabela Carteira
* @access Public
* @param  Object $obTransacao Parametro Transacao
* @return Object Objeto Erro
*/
function alterarCarteira($boTransacao = "")
{
    $this->obTMONCarteira = new TMONCarteira;
    $boFlagTransacao = false;

    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {

        $this->obTMONCarteira->setDado( "cod_carteira", $this->getCodigoCarteira() );
        $this->obTMONCarteira->setDado( "num_carteira", $this->getNumeroCarteira() );
        $this->obTMONCarteira->setDado( "cod_convenio", $this->obRMONConvenio->getCodigoConvenio() );
        $this->obTMONCarteira->setDado( "variacao", $this->getVariacao() );
        $obErro = $this->obTMONCarteira->alteracao( $boTransacao );
       // $this->obTMONCarteira->debug();// exit;

    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTMONCarteira );

    return $obErro;

}

/**
* Lista as carteiras
* @access Public
* @param  Object $obTransacao Parâmetro Transação
* @param  Object $rsRecordSet Recordset de retorno
* @return Object Objeto Erro
*/
function listarCarteira(&$rsRecordSet, $boTransacao = "")
{
    //$this->obTMONCarteira = new TMONCarteira;
    $this->obTMONCarteira = new TMONCarteira;

    if ($this->inCodigoCarteira) {
        $stFiltro .= " cod_carteira = ". $this->inCodigoCarteira ." and ";
    }

    if ($this->inNumeroCarteira) {
        $stFiltro .= " num_carteira = ". $this->inNumeroCarteira ." and ";
    }

    if ( $this->obRMONConvenio->getTipoConvenio() ) {
        $stFiltro .= " co.cod_tipo = ". $this->obRMONConvenio->getTipoConvenio() ." and ";
    }

    if ( $this->obRMONConvenio->getCodigoConvenio() ) {
        $stFiltro .= " cod_convenio = ". $this->obRMONConvenio->getCodigoConvenio() ." and ";
    }

    if ( $this->obRMONConvenio->getNumeroConvenio() ) {
        $stFiltro .= " co.num_convenio = ". $this->obRMONConvenio->getNumeroConvenio() ." and ";
    }

    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    }

    $stOrder = " ORDER BY cod_carteira ";
    $obErro = $this->obTMONCarteira->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
    //$this->obTMONCarteira->debug();
    return $obErro;
}

/**
    * Verifica se o Numero da Carteira a ser incluida ja nao existe
    * @access Public
    * @param  Object $rsBanco Objeto RecordSet preenchido com os dados selecionados
    * @param  Object $obTransacao Parametro Transacao
    * @return Object Objeto Erro
*/
function verificaCarteira($boTransacao = "")
{
    $this->obTMONCarteira = new TMONCarteira;

    $obErro = $this->obTMONCarteira->recuperaTodos( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    $cont =0;
    $achou = false;
    $valores = Array ();
    while ($cont < $rsRecordSet->getNumLinhas()) {
        $valores[$cont] = strtoupper ($rsRecordSet->getCampo("num_carteira"));

        if ( $valores[$cont] == strtoupper ( $this->inNumeroCarteira )  ) {
            $achou = true;
            break;
        }
        $cont++;
        $rsRecordSet->proximo();
    }

    if ( $rsRecordSet->getNumLinhas() > 0 && $achou ) {
        $obErro->setDescricao("Carteira já cadastrada no Sistema! ($this->inNumeroCarteira)");
    }

    return $obErro;
}

}
