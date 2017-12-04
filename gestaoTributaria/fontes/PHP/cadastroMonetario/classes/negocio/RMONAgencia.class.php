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
    * Classe de regra de negocio para MONETARIO.AGENCIA
    * Data de Criacao: 21/12/2004

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Vandre Miguel Ramos

    * @package URBEM
    * @subpackage Regra

    * $Id: RMONAgencia.class.php 66262 2016-08-03 20:58:03Z carlos.silva $

* Casos de uso: uc-05.05.02
*/

/*
$Log$
Revision 1.22  2007/02/06 10:43:06  cercato
Bug #8141#

Revision 1.21  2006/09/15 14:46:22  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_MON_NEGOCIO."RMONBanco.class.php"          );
include_once ( CAM_GA_CGM_NEGOCIO."RCGM.class.php"    );
include_once ( CAM_GT_MON_MAPEAMENTO."TMONAgencia.class.php"   );

class RMONAgencia
{
/**
    * @access Private
    * @var Integer
*/
var $inCodAgencia;
/**
    * @access Private
    * @var String
*/
var $stNumAgencia;
/**
    * @access Private
    * @var String
*/
var $stNomAgencia;
/**
    * @access Private
    * @var String
*/
var $stContato;
/**
    * @access Private
    * @var Object
*/
var $obTMonetarioAgencia;
/**
    * @access Private
    * @var Object
*/
var $obRMonetarioBanco;
/**
    * @access Private
    * @var Object
*/
var $obRCGM;

//SETTERS
function setCodAgencia($valor) { $this->inCodAgencia = $valor; }
function setNumAgencia($valor) { $this->stNumAgencia = $valor; }
function setNomAgencia($valor) { $this->stNomAgencia = $valor; }
function setContato($valor) { $this->stContato    = $valor; }
function setTMONAgencia($valor) { $this->obTMONAgencia= $valor; }
function setRMONBanco($valor) { $this->obRMONBanco  = $valor; }
function setRCGM($valor) { $this->obRCGM       = $valor; }

//GETTERS
function getCodAgencia() { return $this->inCodAgencia; }
function getNumAgencia() { return $this->stNumAgencia; }
function getNomAgencia() { return $this->stNomAgencia; }
function getContato() { return $this->stContato;    }
function getTMONAgencia() { return $this->obTMONAgencia;}
function getRMONBanco() { return $this->obRMONBanco;  }
function getRCGM() { return $this->obRCGM;       }

//METODO CONSTRUTOR
/**
     * Método construtor
     * @access Private
*/
function RMONAgencia()
{
    $this->setTMONAgencia           (  new TMONAgencia   );
    $this->setRMONBanco             (  new RMONBanco     );
    $this->setRCGM                  (  new RCGM          );
}
/**
* Inclui os dados setados na tabela Monetario.Agencia
* @access Public
* @param  Object $obTransacao Parametro Transacao
* @return Object Objeto Erro
*/
function incluirAgencia($boTransacao = "")
{
    $boFlagTransacao = false;
    $obTransacao = new Transacao;
    $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obRMONBanco = new RMONBanco;
        $obRMONBanco->setNumBanco( $this->obRMONBanco->getNumBanco() );
        $obErro = $obRMONBanco->listarBanco( $rsBanco, $boTransacao );
        
        if ( !$obErro->ocorreu() ) {
            if ( !$rsBanco->eof() ) {
                $obErro = $this->verificaAgencia();
                
                if ( !$obErro->ocorreu() ) {
                    $this->obTMONAgencia->setDado( "cod_banco", $rsBanco->getCampo('cod_banco') );
                    $obErro = $this->obTMONAgencia->proximoCod( $this->inCodAgencia, $boTransacao );
                    if ( !$obErro->ocorreu() ) {
                        $this->obTMONAgencia->setDado( "cod_agencia",        $this->inCodAgencia );
                        $this->obTMONAgencia->setDado( "num_agencia",        $this->getNumAgencia() );
                        $this->obTMONAgencia->setDado( "nom_agencia",        $this->getNomAgencia() );
                        $this->obTMONAgencia->setDado( "numcgm_agencia",     $this->getRCGM()       );
                        $this->obTMONAgencia->setDado( "nom_pessoa_contato", $this->getContato()    );
                        $obErro = $this->obTMONAgencia->inclusao( $boTransacao );
                    }
                }
            } else {
                $obErro->setDescricao( "A Agência número ".$this->obRMONAgencia->getNumAgencia()." não existe." );
            }
        }
    }
    $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTMONAgencia );

    return $obErro;
}
/**
* Altera os dados setados na tabela Monetario.Agencia
* @access Public
* @param  Object $obTransacao Parametro Transacao
* @return Object Objeto Erro
*/
function alterarAgencia($boTransacao = "")
{
    $boFlagTransacao = false;
    $obTransacao = new Transacao;
    $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

    if ( !$obErro->ocorreu() ) {
        $obErro = $this->verificaAgencia();

        if ( !$obErro->ocorreu() ) {
            $this->obTMONAgencia->setDado( "cod_banco",          $this->obRMONBanco->getCodBanco() );
            $this->obTMONAgencia->setDado( "cod_agencia",        $this->getCodAgencia() );
            $this->obTMONAgencia->setDado( "num_agencia",        $this->getNumAgencia() );
            $this->obTMONAgencia->setDado( "nom_agencia",        $this->getNomAgencia() );
            $this->obTMONAgencia->setDado( "numcgm_agencia",     $this->getRCGM()       );
            $this->obTMONAgencia->setDado( "nom_pessoa_contato", $this->getContato   () );
            $obErro = $this->obTMONAgencia->alteracao( $boTransacao );
        }
    }

    $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTMONAgencia );

    return $obErro;
}

/**
* Exclui os dados setados na tabela Monetario.Agencia
* @access Public
* @param  Object $obTransacao Parametro Transacao
* @return Object Objeto Erro
*/
function excluirAgencia($boTransacao = "")
{
    include_once ( CAM_GT_MON_NEGOCIO."RMONContaCorrente.class.php" );
    $obRMONConta = new RMONContaCorrente;

    $boFlagTransacao = false;
    $obTransacao = new Transacao;
    $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {

        $obRMONConta->obRMONAgencia->setCodAgencia( $this->getCodAgencia() );
        $obRMONConta->obRMONAgencia->obRMONBanco->setCodBanco( $this->obRMONBanco->getCodBanco() );

        $obRMONConta->listarContaCorrente ( $rsLista );

        if ( $rsLista->getNumLinhas() < 1 ) {
            $this->obTMONAgencia->setDado( "cod_agencia", $this->getCodAgencia() );
            $this->obTMONAgencia->setDado( "cod_banco"  , $this->obRMONBanco->getCodBanco() );
            $obErro = $this->obTMONAgencia->exclusao( $boTransacao );
        } else {
            $obErro->setDescricao ( "A Agência selecionada tem uma ou mais contas correntes vinculadas (Ag. ". $_REQUEST['stNomAgencia'] . " )" );
        }
    }
    $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTMONAgencia );

    return $obErro;
}

/**
* Lista as Agencias conforme o filtro setado
* @access Public
* @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
* @param  Object $obTransacao Parametro Transacao
* @return Object Objeto Erro
*/
function listarAgencia(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";

    if ( $this->obRMONBanco->getCodBanco() ) {
        $stFiltro .= " ag.cod_banco = ".$this->obRMONBanco->getCodBanco(). " AND ";
    }
    if ( $this->obRMONBanco->getNumBanco() ) {
        $stFiltro .= " num_banco = '".$this->obRMONBanco->getNumBanco()."' AND ";
    }
    if ( $this->getCodAgencia() ) {
        $stFiltro .= " cod_agencia = ".$this->getCodAgencia()." AND ";
    }
    if ( $this->getNumAgencia() ) {
        $stFiltro .= " num_agencia = '".$this->getNumAgencia()."' AND ";
    }
    if ( $this->getNomAgencia() ) {
        $stFiltro .= " nom_agencia like '%".$this->getNomAgencia()."%' AND ";
    }
    if ( $this->obRCGM->getNumCGM() ) {
        $stFiltro .= " numcgm_agencia = '".$this->obRCGM->getNumCGM()."' AND ";
    }

    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    }

    $stOrder = " ORDER BY ban.cod_banco, ag.cod_agencia ";
    $obErro = $this->obTMONAgencia->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
    //$this->obTMONAgencia->debug();
    return $obErro;
}
/**
* Recupera do BD os dados da Agencia selecionada
* @access Public
* @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
* @param  Object $obTransacao Parametro Transacao
* @return Object Objeto Erro
*/
//function consultarAgencia($boTransacao = "") {
function consultarAgencia(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";
    if ( $this->getCodAgencia() ) {
        $stFiltro .= " ag.cod_agencia = ".$this->getCodAgencia()." AND ";
    }
    if ( $this->getNumAgencia() ) {
        $stFiltro .= " ag.num_agencia = '".$this->getNumAgencia()."' AND ";
    }
    if ( $this->getNomAgencia() ) {
        $stFiltro .= " ag.nom_agencia like '%".$this->getNomAgencia()."%' AND ";
    }
    if ( $this->obRMONBanco->getNumBanco() ) {
        $stFiltro .= " ban.num_banco = '".$this->obRMONBanco->getNumBanco()."' AND ";
    }
    if ( $this->obRMONBanco->getCodBanco() ) {
        $stFiltro .= " ban.cod_banco = ".$this->obRMONBanco->getCodBanco()." AND ";
    }
    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    }
    $stOrder = " ORDER BY ag.cod_agencia ";
    $obErro = $this->obTMONAgencia->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
    //$this->obTMONAgencia->debug(); //exit;
    if ( !$obErro->ocorreu() and !$rsRecordSet->eof() ) {
        $this->setCodAgencia( $rsRecordSet->getCampo( "cod_agencia"        ));
        $this->setNumAgencia( $rsRecordSet->getCampo( "num_agencia"        ));
        $this->setNomAgencia( $rsRecordSet->getCampo( "nom_agencia"        ));
        $this->setContato   ( $rsRecordSet->getCampo( "nom_pessoa_contato" ));
        $this->obRMONBanco->consultarBanco($boTransacao);
    }

    return $obErro;
}
/**
    * Verifica se a Agencia a ser incluida, ja nao existe
    * @access Public
    * @param  Object $rsBanco Objeto RecordSet preenchido com os dados selecionados
    * @param  Object $obTransacao Parametro Transacao
    * @return Object Objeto Erro
*/
function verificaAgencia($boTransacao = "")
{
    $obRMONBanco = new RMONBanco;
    $obRMONBanco->setNumBanco( $this->obRMONBanco->getNumBanco() );
    $obErro = $obRMONBanco->listarBanco( $rsBanco, $boTransacao );
    
    $stFiltro  = " WHERE cod_banco = ".$rsBanco->getCampo('cod_banco');
    $stFiltro .= "   AND num_agencia = '".$this->getNumAgencia()."' ";
    $obErro = $this->obTMONAgencia->recuperaTodos( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );
    
    if ( $rsRecordSet->getNumLinhas() > 0 ) {
        $obErro->setDescricao("Agência já cadastrada no Sistema! (Banco: ".$rsBanco->getCampo('nom_banco')." e Agência: ".$this->getNumAgencia().")");
    }

    return $obErro;
}

}
