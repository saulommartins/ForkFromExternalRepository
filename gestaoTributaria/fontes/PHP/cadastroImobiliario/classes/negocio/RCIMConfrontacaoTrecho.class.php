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
     * Classe de regra de negócio para confrontação de trecho
     * Data de Criação: 22/11/2004

     * @author Analista: Ricardo Lopes de Alencar
     * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

     * @package URBEM
     * @subpackage Regra

    * $Id: RCIMConfrontacaoTrecho.class.php 59612 2014-09-02 12:00:51Z gelson $

     * Casos de uso: uc-05.01.08
                     uc-05.01.09
*/

/*
$Log$
Revision 1.4  2006/09/18 09:12:40  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMConfrontacao.class.php"            );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMTrecho.class.php"                  );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMConfrontacaoTrecho.class.php" );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMImovelConfrontacao.class.php" );

class RCIMConfrontacaoTrecho extends RCIMConfrontacao
{
/**
    * @access Private
    * @var Boolean
*/
var $boPrincipal;
/**
    * @access Private
    * @var Object
*/
var $obRCIMTrecho;
/**
    * @access Private
    * @var Object
*/
var $obTCIMConfrontacaoTrecho;
/**
    * @access Private
    * @var Object
*/
var $obTCIMImovelConfrontacao;

/**
    * @access Public
    * @param Boolean $valor
*/
function setPrincipal($valor) { $this->boPrincipal = $valor; }

/**
    * @access Public
    * @return Boolean
*/
function getPrincipal() { return $this->boPrincipal; }

/**
     * MÃ©todo construtor
     * @access Private
     * @param Object &$obRCIMLote ReferÃªncia a classe Lote
*/
function RCIMConfrontacaoTrecho(&$obRCIMLote)
{
    parent::RCIMConfrontacao( $obRCIMLote );
    $this->obTCIMConfrontacaoTrecho = new TCIMConfrontacaoTrecho;
    $this->obTCIMImovelConfrontacao = new TCIMImovelConfrontacao;
    $this->obRCIMTrecho = new RCIMTrecho;
}

/**
    * Inclui os dados setados na tabela de Confrontacao Trecho
    * @access Public
    * @param  Object $obTransacao ParÃ¢metro TransaÃ§Ã£o
    * @return Object Objeto Erro
*/
function incluirConfrontacao($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obErro = parent::incluirConfrontacao( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obTCIMConfrontacaoTrecho->setDado( "cod_confrontacao", $this->inCodigoConfrontacao                );
            $this->obTCIMConfrontacaoTrecho->setDado( "cod_lote",         $this->roRCIMLote->getCodigoLote()         );
            $this->obTCIMConfrontacaoTrecho->setDado( "principal",        $this->boPrincipal                         );
            $this->obTCIMConfrontacaoTrecho->setDado( "cod_trecho",       $this->obRCIMTrecho->getCodigoTrecho()     );
            $this->obTCIMConfrontacaoTrecho->setDado( "cod_logradouro",   $this->obRCIMTrecho->getCodigoLogradouro() );
            $obErro = $this->obTCIMConfrontacaoTrecho->inclusao( $boTransacao );
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMConfrontacaoTrecho );

    return $obErro;
}

/**
    * Altera os dados setados na tabela de Confrontacao Trecho
    * @access Public
    * @param  Object $obTransacao ParÃ¢metro TransaÃ§Ã£o
    * @return Object Objeto Erro
*/
function alterarConfrontacao($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obErro = parent::alterarConfrontacao( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obTCIMConfrontacaoTrecho->setDado( "cod_confrontacao", $this->inCodigoConfrontacao        );
            $this->obTCIMConfrontacaoTrecho->setDado( "cod_lote",         $this->roRCIMLote->getCodigoLote() );
            $this->obTCIMConfrontacaoTrecho->setDado( "principal",        $this->boPrincipal                 );
            $this->obTCIMConfrontacaoTrecho->setDado( "cod_trecho",       $this->obRCIMTrecho->getCodigoTrecho()     );
            $this->obTCIMConfrontacaoTrecho->setDado( "cod_logradouro",   $this->obRCIMTrecho->getCodigoLogradouro() );
            $obErro = $this->obTCIMConfrontacaoTrecho->alteracao( $boTransacao );
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMConfrontacaoTrecho );

    return $obErro;
}

/**
    * Exclui a Confrontacao de Trecho setada
    * @access Public
    * @param  Object $obTransacao ParÃ¢metro TransaÃ§Ã£o
    * @return Object Objeto Erro
*/
function excluirConfrontacao($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obErro = $this->verificaImovelConfrontacao( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obTCIMConfrontacaoTrecho->setDado( "cod_confrontacao", $this->inCodigoConfrontacao        );
            $this->obTCIMConfrontacaoTrecho->setDado( "cod_lote",         $this->roRCIMLote->getCodigoLote() );
            $obErro = $this->obTCIMConfrontacaoTrecho->exclusao( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $obErro = parent::excluirConfrontacao( $boTransacao );
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMConfrontacaoTrecho );

    return $obErro;
}

/**
    * Recupera do banco de dados os dados da Confrontacao de Trecho selecionada
    * @access Public
    * @param  Object $obTransacao ParÃ¢metro TransaÃ§Ã£o
    * @return Object Objeto Erro
*/
function consultarConfrontacao($boTransacao = "")
{
    $obErro = parent::consultarConfrontacao( $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTCIMConfrontacaoTrecho->setDado( "cod_confrontacao", $this->getCodigoConfrontacao()    );
        $this->obTCIMConfrontacaoTrecho->setDado( "cod_lote",         $this->roRCIMLote->getCodigoLote() );
        $obErro = $this->obTCIMConfrontacaoTrecho->recuperaPorChave( $rsRecordSet, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->boPrincipal = $rsRecordSet->getCampo( "principal" );
            $this->obRCIMTrecho->setCodigoTrecho     ( $rsRecordSet->getCampo( "cod_trecho" )     );
            $this->obRCIMTrecho->setCodigoLogradouro ( $rsRecordSet->getCampo( "cod_logradouro" ) );
            $obErro = $this->obRCIMTrecho->consultarTrecho( $boTransacao );
        }
    }

    return $obErro;
}

function listarConfrontacoes(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";
    if ( $this->roRCIMLote->getCodigoLote() ) {
        $stFiltro .= " AND L.cod_lote = ".$this->roRCIMLote->getCodigoLote();
    }
    if ($this->boPrincipal) {
        $stFiltro .= " AND CT.principal = '".$this->boPrincipal."' ";
    }
    $stOrdem = " ORDER BY NL.nom_tipo, NL.nom_logradouro ";
    $obErro = $this->obTCIMConfrontacaoTrecho->recuperaRelacionamento( $rsRecordSet, $stFiltro, "", $boTransacao );

    return $obErro;
}

function verificaImovelConfrontacao($boTransacao = "")
{
    $stFiltro  = " WHERE COD_CONFRONTACAO = ".$this->inCodigoConfrontacao;
    $stFiltro .= " AND COD_LOTE = ".$this->roRCIMLote->getCodigoLote();
    $obErro = $this->obTCIMImovelConfrontacao->recuperaTodos( $rsRecordSet, $stFiltro, "", $boTransacao );
    if ( !$obErro->ocorreu() and !$rsRecordSet->eof() ) {
        $stChaveTrecho  = $this->obRCIMTrecho->getCodigoLogradouro().".";
        $stChaveTrecho .= $this->obRCIMTrecho->getSequencia();
        $obErro->setDescricao( "A confrontação ".$stChaveTrecho." é o endereço do imóvel de inscrição imobiliária ".$rsRecordSet->getCampo("inscricao_municipal")."; portanto, não pode ser removida!");
    }

    return $obErro;
}

}
?>
