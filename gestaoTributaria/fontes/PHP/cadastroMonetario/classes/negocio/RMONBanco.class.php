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
    * Classe de regra de negocio para MONETARIO.BANCO
    * Data de Criacao: 21/12/2004

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Vandre Miguel Ramos

    * @package URBEM
    * @subpackage Regra

    * $Id: RMONBanco.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.05.10
*/

/*
$Log$
Revision 1.15  2006/09/15 14:46:22  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_MON_MAPEAMENTO."TMONBanco.class.php"   );

class RMONBanco
{
/**
    * @access Private
    * @var Integer
*/
var $inCodBanco;
/**
    * @access Private
    * @var Integer
*/
var $stNumBanco;
/**
    * @access Private
    * @var String
*/
var $stNomBanco;
/**
    * @access Private
    * @var Object
*/
var $obTMONBanco;

//SETTERS
function setCodBanco($valor) { $this->inCodBanco         = $valor;              }
function setNomBanco($valor) { $this->stNomBanco         = $valor;              }
function setNumBanco($valor) { $this->stNumBanco         = $valor;          }

//GETTERS
function getCodBanco() { return $this->inCodBanco;                              }
function getNomBanco() { return $this->stNomBanco;                              }
function getNumBanco() { return $this->stNumBanco;                              }

//METODO CONSTRUTOR
/**
     * Metodo construtor
     * @access Private
*/
function RMONBanco()
{
    $this->obTMONBanco  = new TMONBanco;
    $this->obTransacao  = new Transacao;
}

/**
* Inclui os dados setados na tabela Monetaria
* @access Public
* @param  Object $obTransacao Parametro Transacao
* @return Object Objeto Erro
*/
function incluirBanco($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obErro = $this->verificaBanco($boTransacao);
    if ( !$obErro->ocorreu() ) {
        $obErro = $this->obTMONBanco->proximoCod( $this->inCodBanco, $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $this->obTMONBanco->setDado( "cod_banco", $this->getCodBanco() );
                $this->obTMONBanco->setDado( "nom_banco", $this->getNomBanco() );
                $this->obTMONBanco->setDado( "num_banco", $this->getNumBanco() );
                $obErro = $this->obTMONBanco->inclusao( $boTransacao );
            }
    }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTMONBanco );

    return $obErro;
}
/**
* Altera os dados setados na tabela Monetaria
* @access Public
* @param  Object $obTransacao Parametro Transacao
* @return Object Objeto Erro
*/
function alterarBanco($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTMONBanco->setDado( "cod_banco", $this->getCodBanco() );
        $this->obTMONBanco->setDado( "nom_banco", $this->getNomBanco() );
        $this->obTMONBanco->setDado( "num_banco", $this->getNumBanco() );
        $obErro = $this->obTMONBanco->alteracao( $boTransacao );
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTMONBanco );

    return $obErro;
}

/**
* Exclui os dados setados na tabela Monetaria
* @access Public
* @param  Object $obTransacao Parametro Transacao
* @return Object Objeto Erro
*/
function excluirBanco($boTransacao = "")
{
    $obErro = new Erro;
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {

        $obErro = $this->verificaBancoTemAgencia ();
        if ( !$obErro->ocorreu() ) {

            $this->obTMONBanco->setDado( "cod_banco", $this->getCodBanco() );
            $this->obTMONBanco->setDado( "nom_banco", $this->getNomBanco() );
            $this->obTMONBanco->setDado( "num_banco", $this->getNumBanco() );
            $obErro = $this->obTMONBanco->exclusao( $boTransacao );
        }

    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTMONBanco );

    return $obErro;
}
/**
* Lista os Bancos conforme o filtro setado
* @access Public
* @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
* @param  Object $obTransacao Parametro Transacao
* @return Object Objeto Erro
*/
function listarBanco(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";
    if ( $this->getCodBanco() ) {
    //echo $this->getCodBanco()." - cod<br>";
        $stFiltro .= " cod_banco = ".$this->getCodBanco()." AND ";
    }
    if ( $this->getNumBanco() ) {
    //echo $this->getNumBanco()." - num<br>";
        $stFiltro .= " num_banco = '".$this->getNumBanco()."' AND ";
    }
    if ( $this->getNomBanco() ) {
    //echo $this->getNomBanco()." - nom<br>";
        $stFiltro .= " nom_banco like '%".$this->getNomBanco()."%' AND ";
    }
    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    }

    $stOrder = " ORDER BY cod_banco ";
    $obErro = $this->obTMONBanco->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
* Recupera do BD os dados do Banco selecionado
* @access Public
* @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
* @param  Object $obTransacao Parametro Transacao
* @return Object Objeto Erro
*/
function consultarBanco($boTransacao = "")
{
    $stFiltro = "";
    if ( $this->getCodBanco() ) {
        $stFiltro .= " cod_banco = ".$this->getCodBanco()." AND ";
    }
    if ( $this->getNumBanco() ) {
        $stFiltro .= " num_banco = '".$this->getNumBanco()."' AND ";
    }
    if ($this->stNomBanco) {
        $stFiltro .= " nom_banco like '%".$this->stNomBanco."%' AND ";
    }
    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    }
    $stOrder = " ORDER BY cod_banco ";
    $obErro = $this->obTMONBanco->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
    //$this->obTMONBanco->debug();
    if ( !$obErro->ocorreu() and !$rsRecordSet->eof() ) {
        $this->inCodBanco  = $rsRecordSet->getCampo( "cod_banco"        );
        $this->stNumBanco = $rsRecordSet->getCampo( "num_banco"        );
        $this->stNomBanco = $rsRecordSet->getCampo( "nom_banco"        );
    }

    return $obErro;
}

/**
    * Verifica se o Banco a ser incluido ja nao existe
    * @access Public
    * @param  Object $rsBanco Objeto RecordSet preenchido com os dados selecionados
    * @param  Object $obTransacao Parametro Transacao
    * @return Object Objeto Erro
*/
function verificaBanco($boTransacao = "")
{
   $obErro = $this->obTMONBanco->recuperaTodos( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    $cont =0;
    $achou = false;
    $achouN = false;
    $valores = Array ();
    while ($cont < $rsRecordSet->getNumLinhas()) {
        $valores[$cont] = strtoupper ($rsRecordSet->getCampo("nom_banco"));

        if ( $valores[$cont] == strtoupper ($this->stNomBanco)  ) {
            $achou = true;
            break;
        } elseif (  $this->stNumBanco == $rsRecordSet->getCampo("num_banco") ) {
            $achouN = true;
            break;
        }

        $cont++;
        $rsRecordSet->proximo();
    }

    if ( $rsRecordSet->getNumLinhas() > 0 && $achou ) {
        $obErro->setDescricao("Banco já cadastrado no Sistema! ($this->stNomBanco)");
    }else
        if ( $rsRecordSet->getNumLinhas() > 0 && $achouN ) {
            $obErro->setDescricao("Banco já cadastrado no Sistema! ($this->stNumBanco)");
        }

    return $obErro;
}

/**
    * Verifica se o Banco tem alguma agência vinculada antes de EXCLUIR. Se tiver, deve mostrar mensagem de erro e não excluir.
    * @access Public
    * @param  Object $rsBanco Objeto RecordSet preenchido com os dados selecionados
    * @param  Object $obTransacao Parametro Transacao
    * @return Object Objeto Erro
*/
function verificaBancoTemAgencia($boTransacao = "")
{
    include_once ( CAM_GT_MON_NEGOCIO."RMONAgencia.class.php"   );
    $obRMONAgencia = new RMONAgencia;

    $obRMONAgencia->obRMONBanco->setCodBanco ( $this->getCodBanco() );
    $obErro = $obRMONAgencia->listarAgencia( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );
    $obRMONAgencia->obRMONBanco->setCodBanco ( '' );

    $cont =0;
    $achou = false;
    $valores = Array ();

    if ( $rsRecordSet->getNumLinhas() > 0 ) {
        $obErro->setDescricao("[Este Banco possui uma ou mais Agências vinculadas ($this->stNomBanco)]");
    }

    return $obErro;
}

}
