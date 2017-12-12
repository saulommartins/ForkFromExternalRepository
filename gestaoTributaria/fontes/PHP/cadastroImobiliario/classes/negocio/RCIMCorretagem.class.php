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
     * Classe de regra de negócio para corretagem
     * Data de Criação: 24/01/2005

     * @author Analista: Ricardo Lopes de Alencar
     * @author Desenvolvedor: Fábio Bertoldi Rodrigues

     * @package URBEM
     * @subpackage Regra

    * $Id: RCIMCorretagem.class.php 59612 2014-09-02 12:00:51Z gelson $

     * Casos de uso: uc-05.01.13
*/

/*
$Log$
Revision 1.4  2006/09/18 09:12:40  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMCorretagem.class.php" );
include_once ( CAM_GA_CGM_NEGOCIO."RCGM.class.php"                );

class RCIMCorretagem
{
/**
    * @access Private
    * @var String
*/
var $stRegistroCreci;
/**
    * @access Private
    * @var String
*/
var $stNomCgmCreci;
var $obRCGM;
/**
    * @access Private
    * @var Object
*/
var $obTCIMCorretagem;

//SETTERS
/**
    * @access Public
    * @param String $valor
*/
function setRegistroCreci($valor) { $this->stRegistroCreci = $valor; }

//GETTERS
/**
    * @access Public
    * @return String
*/
function getRegistroCreci() { return $this->stRegistroCreci; }

function setNomCgmCreci($valor) { $this->stNomCgmCreci = $valor; }
function getNomCgmCreci() { return $this->stNomCgmCreci; }
/**
     * Método construtor
     * @access Private
*/
function RCIMCorretagem()
{
    $this->obTransacao       = new Transacao;
    $this->obRCGM            = new RCGM;
    $this->obTCIMCorretagem  = new TCIMCorretagem;
}

// METODOS FUNCIONAIS (inclusao,alteracao,exclusao...)
/**
* Inclui os dados setados na tabela de Corretagem
* @access Public
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function incluirCorretagem($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->listarCorretagem ( $rsCorretagem );
        if ( $rsCorretagem->getNumLinhas() < 1 ) {
            $this->obTCIMCorretagem->setDado( "creci", $this->stRegistroCreci );
            $obErro = $this->obTCIMCorretagem->inclusao( $boTransacao );
        } else {
            $obErro->setDescricao("Registro Creci já cadastrado no sistema! (".$rsCorretagem->getCampo("creci").")");
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMCorretagem );

    return $obErro;
}

/**
* Exclui os dados setados na tabela de Corretagem
* @access Public
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function excluirCorretagem($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTCIMCorretagem->setDado( "creci", $this->stRegistroCreci );
        $obErro = $this->obTCIMCorretagem->exclusao( $boTransacao );
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMCorretagem );

    return $obErro;
}

/**
* Lista as Corretagens conforme o filtro setado
* @access Public
* @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function listarCorretagem(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";
    if ($this->stRegistroCreci) {
        $stFiltro .= " cr.creci = '".$this->stRegistroCreci."' AND ";
    }
    if ( $this->obRCGM->getNumCGM() ) {
        $stFiltro .= " cgm.numcgm = ".$this->obRCGM->getNumCGM()." AND ";
    }
    if ( $this->obRCGM->getNomCGM() ) {
        $stFiltro .= " UPPER (cgm.nom_cgm) like UPPER ('%".$this->obRCGM->getNomCGM()."%') AND ";
    }
    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    }
    $stOrder = " ORDER BY cr.creci ";
    $obErro = $this->obTCIMCorretagem->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
* Recupera do banco de dados os dados da Corretagem selecionada
* @access Public
* @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function consultarCorretagem($boTransacao = "")
{
    $stFiltro = "";
    if ($this->stRegistroCreci) {
        $stFiltro .= " cr.creci = '".$this->stRegistroCreci."' AND ";
    }
    if ( $this->obRCGM->getNumCGM() ) {
        $stFiltro .= " numcgm = ".$this->obRCGM->getNumCGM()." AND ";
    }
    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    }
    $stOrder = " ORDER BY cr.creci ";
    $obErro = $this->obTCIMCorretagem->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
    if ( !$obErro->ocorreu() and !$rsRecordSet->eof() ) {
        $this->stRegistroCreci = $rsRecordSet->getCampo( "creci"    );
    }

    return $obErro;
}

/**
* Recupera do banco de dados os dados do CRECI selecionado
* @access Public
* @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function buscaCorretagem(&$rsCorretagem , $boTransacao = "")
{
    $stFiltro = " CR.creci = '".$this->stRegistroCreci."' AND ";
    $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    $obErro = $this->obTCIMCorretagem->recuperaRelacionamento( $rsCorretagem, $stFiltro, "",  $boTransacao );

    return $obErro;
}
}
