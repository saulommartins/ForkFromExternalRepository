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
    * Classe de Regra de Negócio para arrecadacao carne
    * Data de Criação   : 10/10/2005

    * @author Analista: Lucas Leusin Oaigen
    * @author Desenvolvedor: Lucas Leusin Oaigen

    * @package URBEM
    * @subpackage Regra

    * $Id: RARRCarneConsolidacao.class.php 59612 2014-09-02 12:00:51Z gelson $

   * Casos de uso: uc-05.03.11, uc-02.04.04
*/

/*
$Log$
Revision 1.3  2006/09/15 10:48:45  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRCarneConsolidacao.class.php" );

/**
    * Classe de Regra de Assinatura
    * @author Analista: ************
    * @author Desenvolvedor: ***********
*/

class RARRCarneConsolidacao
{
/**
    * @var Object
    * @access Private
*/
var $obTransacao;
/**
    * @access Private
    * @var Object
*/
var $obTARRCarneConsolidacao;
/**
    * @access Private
    * @var Object
*/
var $inCodConvenio;
/**
    * @access Private
    * @var String
*/
var $stNumeracao;
/**
    * @access Private
    * @var String
*/
var $stNumeracaoConsolidacao;

// SETTERS
/**
    * @access Public
    * @param Object $valor
*/
function setCodConvenio($valor) { $this->inCodConvenio = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setNumeracao($valor) { $this->stNumeracao = $valor    ; }
/**
    * @access Public
    * @param String $valor
*/
function setNumeracaoConsolidacao($valor) { $this->stNumeracaoConsolidacao  = $valor    ; }

// GETTERES
/**
    * @access Public
    * @return Object
*/
function getCodConvenio() { return $this->inCodConvenio;          }
/**
    * @access Public
    * @return String
*/
function getNumeracao() { return $this->stNumeracao;          }
/**
    * @access Public
    * @return String
*/
function getNumeracaoConsolidacao() { return $this->stNumeracaoConsolidacao;  }

/**
     * Método construtor
     * @access Private
*/
function RARRCarneConsolidacao($obParcela = "vazio")
{
    $this->obTARRCarneConsolidacao = new TARRCarneConsolidacao;
    $this->obTransacao      = new Transacao;

}

/**
* Função para inclusão de Carne Consolidação
* @access Public
* @param  Object $obTransacao Parametro Transacao
* @return Object Objeto Erro
*/
function incluirCarneConsolidacao($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTARRCarneConsolidacao->setDado( "numeracao_consolidacao", $this->getNumeracaoConsolidacao() );
        $this->obTARRCarneConsolidacao->setDado( "numeracao", $this->getNumeracao() );
        $this->obTARRCarneConsolidacao->setDado( "cod_convenio", $this->getCodConvenio() );
        $obErro = $this->obTARRCarneConsolidacao->inclusao( $boTransacao );
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTARRCarneConsolidacao );

    return $obErro;
}

/**
* Função para Alteração de Carne Consolidação
* @access Public
* @param  Object $obTransacao Parametro Transacao
* @return Object Objeto Erro
*/
function alterarCarneConsolidacao($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTARRCarneConsolidacao->setDado( "numeracao_consolidacao", $this->getNumeracaoConsolidacao() );
        $this->obTARRCarneConsolidacao->setDado( "numeracao", $this->getNumeracao() );
        $this->obTARRCarneConsolidacao->setDado( "cod_convenio", $this->getCodConvenio() );
        $obErro = $this->obTARRCarneConsolidacao->alteracao( $boTransacao );
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTARRCarneConsolidacao );

    return $obErro;
}

/**
* Função para Exclusão de Carne Consolidação
* @access Public
* @param  Object $obTransacao Parametro Transacao
* @return Object Objeto Erro
*/
function excluirCarneConsolidacao($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTARRCarneConsolidacao->setDado( "numeracao_consolidacao", $this->getNumeracaoConsolidacao() );
        $this->obTARRCarneConsolidacao->setDado( "numeracao", $this->getNumeracao() );
        $this->obTARRCarneConsolidacao->setDado( "cod_convenio", $this->getCodConvenio() );
        $obErro = $this->obTARRCarneConsolidacao->exclusao( $boTransacao );
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTARRCarneConsolidacao );

    return $obErro;
}

/**
* Função para retorno da lista de numerações da consolidação
* @access Public
* @param  Object $obTransacao Parametro Transacao
* @return Object Objeto Erro
*/
function listarNumeracaoCarne(&$rsRecordSet, $stOrdem = "", $boTransacao = "")
{
    $stFiltro = "";
    $stOrdem = $stOrdem ? $stOrdem : " ORDER BY numeracao ";

    if ($this->getNumeracaoConsolidacao()) {
        $stFiltro .= " numeracao_consolidacao = '".$this->getNumeracaoConsolidacao()."' AND ";
    }

    if ($this->getNumeracao()) {
        $stFiltro .= " numeracao = '".$this->getNumeracao()."' AND ";
    }

    if ($this->getCodConvenio()) {
        $stFiltro .= " cod_convenio = ".$this->getCodConvenio()." AND ";
    }

    if ($stFiltro) {
        $stFiltro = " WHERE ".substr($stFiltro, 0, -4);
    }

    $obErro = $this->obTARRCarneConsolidacao->recuperaListaNumeracaoCarne( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

} // fecha classe
?>
