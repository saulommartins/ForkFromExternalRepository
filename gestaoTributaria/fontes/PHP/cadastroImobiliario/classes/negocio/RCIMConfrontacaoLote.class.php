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
     * Classe de regra de negócio para confrontação de lote
     * Data de Criação: 22/11/2004

     * @author Analista: Ricardo Lopes de Alencar
     * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

     * @package URBEM
     * @subpackage Regra

    * $Id: RCIMConfrontacaoLote.class.php 59612 2014-09-02 12:00:51Z gelson $

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
include_once ( CAM_GT_CIM_NEGOCIO."RCIMConfrontacao.class.php"          );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLote.class.php"                  );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMConfrontacaoLote.class.php" );

class RCIMConfrontacaoLote extends RCIMConfrontacao
{
/**
    * @access Private
    * @var Object
*/
var $obRCIMLote;
/**
    * @access Private
    * @var Object
*/
var $obTCIMConfrontacaoLote;

/**
     * MÃ©todo construtor
     * @access Private
     * @param Object &$obRCIMLote ReferÃªncia a classe Lote
*/
function RCIMConfrontacaoLote(&$obRCIMLote)
{
    parent::RCIMConfrontacao( $obRCIMLote );
    $this->obTCIMConfrontacaoLote = new TCIMConfrontacaoLote;
    $this->obRCIMLote = new RCIMLote;
}

/**
    * Inclui os dados setados na tabela de Confrontacao Lote
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
            $this->obTCIMConfrontacaoLote->setDado( "cod_confrontacao",      $this->inCodigoConfrontacao        );
            $this->obTCIMConfrontacaoLote->setDado( "cod_lote",              $this->roRCIMLote->getCodigoLote() );
            $this->obTCIMConfrontacaoLote->setDado( "cod_lote_confrontacao", $this->obRCIMLote->getCodigoLote() );
            $obErro = $this->obTCIMConfrontacaoLote->inclusao( $boTransacao );
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMConfrontacaoLote );

    return $obErro;
}

/**
    * Altera os dados setados na tabela de Confrontacao Lote
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
            $this->obTCIMConfrontacaoLote->setDado( "cod_confrontacao",      $this->inCodigoConfrontacao        );
            $this->obTCIMConfrontacaoLote->setDado( "cod_lote",              $this->roRCIMLote->getCodigoLote() );
            $this->obTCIMConfrontacaoLote->setDado( "cod_lote_confrontacao", $this->obRCIMLote->getCodigoLote() );
            $obErro = $this->obTCIMConfrontacaoLote->alteracao( $boTransacao );
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMConfrontacaoLote );

    return $obErro;
}

/**
    * Exclui a Confrontacao de Lote setada
    * @access Public
    * @param  Object $obTransacao ParÃ¢metro TransaÃ§Ã£o
    * @return Object Objeto Erro
*/
function excluirConfrontacao($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTCIMConfrontacaoLote->setDado( "cod_confrontacao", $this->inCodigoConfrontacao        );
        $this->obTCIMConfrontacaoLote->setDado( "cod_lote",         $this->roRCIMLote->getCodigoLote() );
        $obErro = $this->obTCIMConfrontacaoLote->exclusao( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $obErro = parent::excluirConfrontacao( $boTransacao );
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMConfrontacaoLote );

    return $obErro;
}

/**
    * Recupera do banco de dados os dados da Confrontacao de Lote selecionada
    * @access Public
    * @param  Object $obTransacao ParÃ¢metro TransaÃ§Ã£o
    * @return Object Objeto Erro
*/
function consultarConfrontacao($boTransacao = "")
{
    $obErro = parent::consultarConfrontacao( $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTCIMConfrontacaoLote->setDado( "cod_confrontacao", $this->inCodigoConfrontacao );
        $this->obTCIMConfrontacaoLote->setDado( "cod_lote",         $this->roRCIMLote->getCodigoLote()         );
        $obErro = $this->obTCIMConfrontacaoLote->recuperaPorChave( $rsRecordSet, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obRCIMLote->setCodigoLote( $rsRecordSet->getCampo( "cod_lote_confrontacao" ) );
            $obErro = $this->obRCIMLote->consultarLote( $boTransacao );
        }
    }

    return $obErro;
}

}
?>
