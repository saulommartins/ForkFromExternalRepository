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

  * Classe de Regra de Parcela
  * Data de criação : 16/11/2005

  * @author Analista: Fábio Bertoldi
  * @author Programador: Tonismar R. Bernardo

  * $Id: RARRParcela.class.php 59752 2014-09-09 18:17:43Z evandro $
**/


include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once(CAM_GT_ARR_MAPEAMENTO."TARRParcela.class.php");
include_once(CAM_GT_ARR_MAPEAMENTO."TARRParcelaReemissao.class.php");
include_once(CAM_GT_ARR_NEGOCIO."RARRLancamento.class.php");
include_once(CAM_GT_ARR_NEGOCIO."RARRCarne.class.php");

/**
    * Classe Regra de Parcela
    * @author Desenvolvedor: Tonismar Régis Bernardo
    * @author Analista: Fábio Bertoldi
*/

class RARRParcela
{
/**
    * @var Date
    * @access Private
*/
var $dtVencimento;
/**
    * @var Integer
    * @access Private
*/
var $inValor;
/**
    * @var Array
    * @access Private
*/
var $arParcelas;
/**
    * @var Integer
    * @access Private
*/
var $inCodParcela;
/**
    * @var Integer
    * @access Private
*/
var $inNrParcela;

//SETTERS
function setVencimento($valor) { $this->dtVencimento = $valor; }
function setValor($valor) { $this->inValor      = $valor; }
function setParcelas($valor) { $this->arParcelas   = $valor; }
function setCodParcela($valor) { $this->inCodParcela = $valor; }
function setNrParcela($valor) { $this->inNrParcela  = $valor; }

//GETTERS
function getVencimento() { return $this->dtVencimento;   }
function getValor() { return $this->inValor;        }
function getParcelas() { return $this->arParcelas;     }
function getCodParcela() { return $this->inCodParcela;   }
function getNrParcela() { return $this->inNrParcela;    }

/**
     * Método construtor
     * @access Private
*/
function RARRParcela(&$obRARRLancamento)
{
    $this->obTARRParcela    = new TARRParcela;
    $this->roRARRLancamento = &$obRARRLancamento;
    $this->obTransacao = new Transacao;
    $this->obTARRCarne = new TARRCarne;

}

/**
     * Método reemissao de parcela....
     * @access Private
*/
function reemitirParcela($arParcelaAnterior,$boTransacao)
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        // inserir parcela anterior em parcela_reemissao
        include_once(CAM_GT_ARR_MAPEAMENTO."TARRParcelaReemissao.class.php");
        $obTARRParcelaReemissao = new TARRParcelaReemissao;
        $obTARRParcelaReemissao->setDado ( "cod_parcela", $arParcelaAnterior["cod_parcela"]     );
        $obTARRParcelaReemissao->setDado ( "vencimento" , $arParcelaAnterior["vencimento"]      );
        $obTARRParcelaReemissao->setDado ( "valor"      , $arParcelaAnterior["valor_anterior"]  );
        $obErro = $obTARRParcelaReemissao->inclusao($boTransacao);
        
        if ( !$obErro->ocorreu() ) {
            // atualiza parcela com novos valores
            $obTARRParcela = new TARRParcela;
            if ( !$obErro->ocorreu() ) {
                // recuperar valor original
                $stFiltroP = " where cod_parcela = ".$arParcelaAnterior["cod_parcela"];
                $obErro = $this->obTARRParcela->recuperaParcelaValor($rsValorP,$stFiltroP,"",$boTransacao);
                $nuValor = $rsValorP->getCampo("valor");
                if ( !$obErro->ocorreu() ) {
                    $obTARRParcela->setDado ( "cod_parcela"     , $arParcelaAnterior["cod_parcela"]             );
                    $obTARRParcela->setDado ( "cod_lancamento"  , $arParcelaAnterior["cod_lancamento"]          );
                    $obTARRParcela->setDado ( "nr_parcela"      ,  $this->getNrParcela()                         );
                    $obTARRParcela->setDado ( "vencimento"      , $this->getVencimento()                        );
                    $obTARRParcela->setDado ( "valor"           , $arParcelaAnterior["valor_anterior"] );
                    $obErro = $obTARRParcela->alteracao($boTransacao);
                }
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTARRCarne );

    return $obErro;
}

/**
     * Método atualização de vencimento de parcela...
     * @access Private
*/
function AtualizaVencimentoParcela($inCodParcela, $inNrParcela,  $boTransacao = "")
{
  
    $stFiltroP = " WHERE ap.cod_parcela = ". $inCodParcela;
    $obErro = $this->obTARRParcela->recuperaParcelaCarne( $rsParcela, $stFiltroP, "", $boTransacao );

    if ( !$obErro->ocorreu() ) {
        $this->obTARRParcelaReemissao = new TARRParcelaReemissao;
        $this->obTARRParcelaReemissao->setDado ('cod_parcela', $rsParcela->getCampo('cod_parcela') );
        $this->obTARRParcelaReemissao->setDado ('vencimento', $rsParcela->getCampo('vencimento') );
        $this->obTARRParcelaReemissao->setDado ('valor', $rsParcela->getCampo('valor') );
        $obErro = $this->obTARRParcelaReemissao->inclusao( $boTransacao );
    }

    return $obErro;
}

function listarConsulta(&$rsRecordSet , $boTransacao = "")
{
    $stFiltro = "";
    if ( $this->roRARRLancamento->getCodLancamento() ) {
        $stFiltro .= $this->roRARRLancamento->getCodLancamento();
    }
    $stOrdem = " ORDER BY ap.nr_parcela";

   $obErro = $this->obTARRParcela->recuperaListaConsulta ( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );
   return $obErro;
}

function listarConsultaRelatorio(&$rsRecordSet , $boTransacao = "")
{
    $stFiltro = "";
    if ( $this->roRARRLancamento->getCodLancamento() ) {
        $stFiltro .= $this->roRARRLancamento->getCodLancamento();
    }
    $stOrdem = " ORDER BY ap.nr_parcela";

   $obErro = $this->obTARRParcela->recuperaListaConsultaRelatorio ( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );
   return $obErro;
}

function listarParcela(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";
    if ($this->inCodParcela) {
        $stFiltro .= " ap.cod_parcela = ".$this->inCodParcela." and ";
    }

    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    }

    $stOrdem = " ap.cod_parcela ";

    $obErro = $this->obTARRParcela->recuperaTodos( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

function listarParcelaCarne(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";
    if ($this->inCodParcela) {
        $stFiltro .= " WHERE ap.cod_parcela = ".$this->inCodParcela;
    }

    $stOrdem = "";

    $obErro = $this->obTARRParcela->recuperaParcelaCarne( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

function listarReemissaoConsulta(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";
    if ($this->inCodParcela) {
        $stFiltro = $this->inCodParcela;
    }

    $obErro = $this->obTARRParcela->recuperaListaReemissaoConsulta( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

}//end of class
