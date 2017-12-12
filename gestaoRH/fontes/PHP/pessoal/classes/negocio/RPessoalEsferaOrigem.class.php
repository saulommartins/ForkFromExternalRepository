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
    * Classe de regra de negócio PessoalEsfera
    * Data de Criação: 06/06/2005

    * @author Analista: Leandro Oliveira
    * @author Desenvolvedor: Vandré Miguel Ramos

    * @package URBEM
    * @subpackage Regra

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

    Caso de uso: uc-04.04.08

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalEsferaOrigem.class.php"   );

class RPessoalEsferaOrigem
{
/**
    * @access Private
    * @var Integer
*/
var $inCodEsfera;
/**
    * @access Private
    * @var String
*/
var $stDescricao;
/**
    * @access Private
    * @var Array de Objetos
*/
var $obTEsferaOrigem;
/*
    * @access Private
    * @var Object
*/
var $obTransacao;

/**
    * @access Public
    * @param Integer $Valor
*/
function setCodEsfera($valor) { $this->inCodEsfera      = $valor; }
/**
    * @access Public
    * @param String $Valor
*/
function setDescricao($valor) { $this->stDescricao  = $valor ; }
/**
    * @access Public
    * @param Object $Valor
*/
function setTEsferaOrigem($valor) { $this->obTEsferaOrigem      = $valor  ; }

/**
    * @access Public
    * @return Integer
*/
function getCodEsfera() { return $this->inCodEsfera                ; }
/**
    * @access Public
    * @return String
*/
function getDescricao() { return $this->stDescricao                     ; }
/**
    * @access Public
    * @return Object
*/
function getTEsferaOrigem() { return $this->obTEsferaOrigem     ; }

/**
     * Método construtor
     * @access Private
*/
function RPessoalEsferaOrigem()
{
    $this->setTEsferaOrigem    ( new TPessoalEsferaOrigem     );
    $this->obTransacao         = new Transacao;
}

/**
    * Executa um recuperaLista na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listar(&$rsRecordSet, $stOrder = "", $boTransacao = "")
{
    if ($this->getCodEsfera()) {
       $this->obTEsferaOrigem->setDado('cod_esfera',$this->getCodEsfera());
    } else {
       $this->obTEsferaOrigem->setDado('cod_esfera','null');
      }
    $obErro = $this->obTEsferaOrigem->recuperaTodos( $rsRecordSet, $stFiltro, " cod_esfera", $boTransacao );

    return $obErro;
}
}
