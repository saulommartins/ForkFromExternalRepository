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
     * Classe de regra de negócio para confrontações
     * Data de Criação: 20/11/2004

     * @author Analista: Ricardo Lopes de Alencar
     * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

     * @package URBEM
     * @subpackage Regra

    * $Id: RCIMConfrontacao.class.php 59612 2014-09-02 12:00:51Z gelson $

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
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMConfrontacao.class.php"              );
include_once ( CAM_GT_CIM_MAPEAMENTO."VCIMConfrontacaoExtensaoAtual.class.php" );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMConfrontacaoExtensao.class.php"      );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMPontoCardeal.class.php"              );

class RCIMConfrontacao
{
/**
    * @access Private
    * @var Integer
*/
var $inCodigoConfrontacao;
/**
    * @access Private
    * @var Integer
*/
var $inCodigoPontoCardeal;
/**
    * @access Private
    * @var String
*/
var $stNomePontoCardeal;
/**
    * @access Private
    * @var Float
*/
var $flExtensao;
/**
    * @access Private
    * @var Object
*/
var $obTCIMConfrontacao;
/**
    * @access Private
    * @var Object
*/
var $obTCIMConfrontacaoExtensao;
/**
    * @access Private
    * @var Object
*/
var $obTCIMPontoCardeal;
/**
    * @access Private
    * @var Object*/

var $obVCIMConfrontacaoExtensaoAtual;
/**
    * @access Private
    * @var Object
*/
var $obTransacao;
/**
    * @access Private
    * @var Object
*/
var $roRCIMLote;
/**
    * @access Public
    * @param Integer $valor
*/
function setCodigoConfrontacao($valor) { $this->inCodigoConfrontacao = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodigoPontoCardeal($valor) { $this->inCodigoPontoCardeal = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setNomePontoCardeal($valor) { $this->stNomePontoCardeal   = $valor; }
/**
    * @access Public
    * @param Float $valor
*/
function setExtensao($valor) { $this->flExtensao           = $valor; }

/**
    * @access Public
    * @return Integer
*/
function getCodigoConfrontacao() { return $this->inCodigoConfrontacao; }
/**
    * @access Public
    * @return Integer
*/
function getCodigoPontoCardeal() { return $this->inCodigoPontoCardeal; }
/**
    * @access Public
    * @return String
*/
function getNomePontoCardel() { return $this->stNomePontoCardeal;   }
/**
    * @access Public
    * @return Float
*/
function getExtensao() { return $this->flExtensao;           }

/**
     * Método construtor
     * @access Private
     * @param Object &$obRCIMLote Referência a classe Lote
*/
function RCIMConfrontacao(&$obRCIMLote)
{
    $this->obTransacao                     = new Transacao;
    $this->obTCIMConfrontacao              = new TCIMConfrontacao;
    $this->obTCIMConfrontacaoExtensao      = new TCIMConfrontacaoExtensao;
    $this->obVCIMConfrontacaoExtensaoAtual = new VCIMConfrontacaoExtensaoAtual;
    $this->obTCIMPontoCardeal              = new TCIMPontoCardeal;
    $this->roRCIMLote = &$obRCIMLote;
}

/**
    * Inclui os dados setados na tabela de Confrontacao
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluirConfrontacao($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTCIMConfrontacao->setDado( "cod_lote", $this->roRCIMLote->getCodigoLote() );
        $obErro = $this->obTCIMConfrontacao->proximoCod( $this->inCodigoConfrontacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obTCIMConfrontacao->setDado( "cod_confrontacao", $this->inCodigoConfrontacao );
            $this->obTCIMConfrontacao->setDado( "cod_ponto",        $this->inCodigoPontoCardeal );
            $obErro = $this->obTCIMConfrontacao->inclusao( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $this->obTCIMConfrontacaoExtensao->setDado( "cod_confrontacao", $this->inCodigoConfrontacao        );
                $this->obTCIMConfrontacaoExtensao->setDado( "cod_lote",         $this->roRCIMLote->getCodigoLote() );
                $this->obTCIMConfrontacaoExtensao->setDado( "valor",            $this->flExtensao                  );
                $obErro = $this->obTCIMConfrontacaoExtensao->inclusao( $boTransacao );
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMConfrontacao );

    return $obErro;
}

/**
    * Altera os dados setados na tabela de Confrontacao Extensao
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function alterarConfrontacao($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTCIMConfrontacao->setDado( "cod_confrontacao", $this->inCodigoConfrontacao );
        $this->obTCIMConfrontacao->setDado( "cod_ponto"       , $this->inCodigoPontoCardeal );
        $this->obTCIMConfrontacao->setDado( "cod_lote"        , $this->roRCIMLote->getCodigoLote() );
        $obErro = $this->obTCIMConfrontacao->alteracao( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obTCIMConfrontacaoExtensao->setDado( "cod_confrontacao", $this->inCodigoConfrontacao        );
            $this->obTCIMConfrontacaoExtensao->setDado( "cod_lote",         $this->roRCIMLote->getCodigoLote() );
            $this->obTCIMConfrontacaoExtensao->setDado( "valor",            $this->flExtensao                  );
            $obErro = $this->obTCIMConfrontacaoExtensao->inclusao( $boTransacao );
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMConfrontacaoExtensao );

    return $obErro;
}

/**
    * Exclui a Confrontacao setada
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluirConfrontacao($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTCIMConfrontacaoExtensao->setDado( "cod_confrontacao", $this->inCodigoConfrontacao        );
        $this->obTCIMConfrontacaoExtensao->setDado( "cod_lote",         $this->roRCIMLote->getCodigoLote() );
        $obErro = $this->obTCIMConfrontacaoExtensao->exclusao( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obTCIMConfrontacao->setDado( "cod_confrontacao", $this->inCodigoConfrontacao        );
            $this->obTCIMConfrontacao->setDado( "cod_lote",        $this->roRCIMLote->getCodigoLote() );
            $obErro = $this->obTCIMConfrontacao->exclusao( $boTransacao );
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMConfrontacao );

    return $obErro;
}

/**
    * Recupera do banco de dados os dados da Confrontacao selecionada
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultarConfrontacao($boTransacao = "")
{
    $stFiltro  = " WHERE ";
    $stFiltro .= " C.COD_CONFRONTACAO = ".$this->inCodigoConfrontacao." AND ";
    $stFiltro .= " C.COD_LOTE = ".$this->roRCIMLote->getCodigoLote();
    $obErro = $this->obTCIMConfrontacao->recuperaRelacionamento( $rsRecordSet, $stFiltro, "", $boTransacao );
    if ( !$obErro->ocorreu() and !$rsRecordSet->eof() ) {
        $this->flExtensao          = $rsRecordSet->getCampo( "valor"     );
        $this->stNomePontoCardeal  = $rsRecordSet->getCampo( "nom_ponto" );
        $this->inCodigoPontoCardeal= $rsRecordSet->getCampo( "cod_ponto" );
    }

    return $obErro;
}

function listarConfrontacoesPorLote(&$rsRecordSet, $boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $stFiltro = " WHERE C.COD_LOTE = ".$this->roRCIMLote->inCodigoLote;
        $obErro = $this->obTCIMConfrontacao->recuperaRelacionamento( $rsRecordSet, $stFiltro, "", $boTransacao );
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro );

    return $obErro;
}

function listarPontosCardeais(&$rsRecordSet, $boTransacao = "")
{
     $stFiltro =
     $stOrdem = " ORDER BY cod_ponto ";
     $obErro = $this->obTCIMPontoCardeal->recuperaTodos( $rsRecordSet, "", $stOrdem, $boTransacao );

     return $obErro;
}

}
?>
