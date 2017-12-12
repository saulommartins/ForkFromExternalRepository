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
     * Classe de regra de negócio para proprietario
     * Data de Criação: 06/12/2004

     * @author Analista: Ricardo Lopes de Alencar
     * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

     * @package URBEM
     * @subpackage Regra

    * $Id: RCIMProprietario.class.php 59612 2014-09-02 12:00:51Z gelson $

     * Casos de uso: uc-05.01.09
*/

/*
$Log$
Revision 1.6  2006/09/18 09:12:40  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMProprietario.class.php"   );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMExProprietario.class.php" );
include_once ( CAM_GA_CGM_NEGOCIO."RCGM.class.php"                    );

class RCIMProprietario
{
var $inNumeroCGM;
var $inNumeroCGMInicial;
var $inNumeroCGMFinal;
var $inOrdem;
var $boPromitente;
var $flCota;
var $tmTimestamp;

var $roRCIMImovel;
var $obTCIMProprietario;
var $obTCIMExProprietario;
var $obTrasacao;
var $obRCGM;

function setNumeroCGM($valor) { $this->inNumeroCGM  = $valor; }
function setOrdem($valor) { $this->inOrdem      = $valor; }
function setPromitente($valor) { $this->boPromitente = $valor; }
function setCota($valor) { $this->flCota       = $valor; }
function setTimestamp($valor) { $this->tmTimestamp  = $valor; }

function getNumeroCGM() { return $this->inNumeroCGM;  }
function getOrdem() { return $this->inOrdem;      }
function getPromitente() { return $this->boPromitente; }
function getCota() { return $this->flCota;       }
function getTimestamp() { return $this->tmTimestamp;  }

function RCIMProprietario(&$obRCIMImovel)
{
    $this->obTransacao          = new Transacao;
    $this->roRCIMImovel         = &$obRCIMImovel;
    $this->obTCIMProprietario   = new TCIMProprietario;
    $this->obTCIMExProprietario = new TCIMExProprietario;
    $this->obRCGM               = new RCGM;
}

function incluirProprietario($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTCIMProprietario->setDado( "numcgm", $this->inNumeroCGM );
        $this->obTCIMProprietario->setDado( "inscricao_municipal", $this->roRCIMImovel->getNumeroInscricao() );
        $this->obTCIMProprietario->setDado( "ordem", $this->inOrdem );
        $this->obTCIMProprietario->setDado( "promitente", $this->boPromitente );
        $this->obTCIMProprietario->setDado( "cota", $this->flCota );
        $obErro = $this->obTCIMProprietario->inclusao( $boTransacao );
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMProprietario );

    return $obErro;
}

function alterarProprietario($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTCIMProprietario->setDado( "numcgm", $this->inNumeroCGM );
        $this->obTCIMProprietario->setDado( "inscricao_municipal", $this->roRCIMImovel->getNumeroInscricao() );
        $this->obTCIMProprietario->setDado( "ordem", $this->inOrdem );
        $this->obTCIMProprietario->setDado( "promitente", $this->boPromitente );
        $this->obTCIMProprietario->setDado( "cota", $this->flCota );
        $obErro = $this->obTCIMProprietario->alteracao( $boTransacao );
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMProprietario );

    return $obErro;
}

function excluirProprietario($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTCIMProprietario->setDado( "numcgm", $this->inNumeroCGM );
        $this->obTCIMProprietario->setDado( "inscricao_municipal", $this->roRCIMImovel->getNumeroInscricao() );
        $obErro = $this->obTCIMProprietario->exclusao( $boTransacao );
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMProprietario );

    return $obErro;
}

function listarProprietarios(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";
    if ($this->inNumeroCGM) {
        $stFiltro .= " numcgm = ".$this->inNumeroCGM." AND ";
    }
    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen( $stFiltro ) - 4 );
    }
    $stOrdem = " ORDER BY inscricao_municipal, ordem ";
    $obErro = $this->obTCIMProprietario->recuperaTodos( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

function listarExProprietarios(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";
    if ( $this->getTimestamp() ) {
        $stFiltro .= " timestamp = '".$this->getTimestamp()."' AND ";
    }
    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen( $stFiltro ) - 4 );
    }
    $stOrdem = " ORDER BY inscricao_municipal, ordem ";
    $obErro = $this->obTCIMExProprietario->recuperaTodos( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

function listarProprietariosPorImovel(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = " WHERE inscricao_municipal = ".$this->roRCIMImovel->getNumeroInscricao()." ";
    $stOrdem = " ORDER BY inscricao_municipal, ordem ";
    $obErro = $this->obTCIMProprietario->recuperaTodos( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

function consultarProprietario($boTransacao = "")
{
    $stFiltro = " WHERE ";
    $stFiltro .= " numcgm = ".$this->inNumeroCGM." AND ";
    $stFiltro .= " inscricao_municipal = ".$this->roRCIMImovel->getNumeroInscricao();
    $obErro = $this->obTCIMProprietario->recuperaTodos( $rsRecordSet, $stFiltro, "", $boTransacao );
    if ( !$obErro->ocorreu() and !$rsRecordSet->eof() ) {
        $this->inOrdem      = $rsRecordSet->getCampo("ordem");
        $this->boPromitente = $rsRecordSet->getCampo("promitente");
        $this->flCota       = $rsRecordSet->getCampo("cota");
        $this->obRCGM->setNumCGM( $this->inNumeroCGM );
        $obErro = $this->obRCGM->consultar( $boTransacao );
    }

    return $obErro;
}

}
