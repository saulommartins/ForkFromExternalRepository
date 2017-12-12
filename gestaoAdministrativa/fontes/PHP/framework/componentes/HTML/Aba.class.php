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
* Montar uma linha de uma tabela de acordo com os valores setados pelo usuário
* Data de Criação: 05/02/2003

* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00

*/

/**
    * Classe que monta uma linha de uma tabela

    * @package framework
    * @subpackage componentes
*/
class Aba extends Objeto
{
/**
    * @access Private
    * @var String
*/
var $stLabel;

/**
    * @access Private
    * @var Boolean
*/
var $boQuebra;

/**
    * @access Private
    * @var String
*/
var $stReferencia;

/**
    * @access Private
    * @var String
*/
var $stIndice;

/**
    * @access Private
    * @var String
*/
var $stFuncao;

/**
    * @access Private
    * @var String
*/
var $stTipTitle;

/**
    * @access Public
    * @param String $Valor
*/
function setLabel($valor) { $this->stLabel     = $valor; }

/**
    * @access Public
    * @param Boolean $Valor
*/
function setQuebra($valor) { $this->boQuebra    = $valor; }

/**
    * @access Public
    * @param String $Valor
*/
function setReferencia($valor) { $this->stReferencia= $valor; }

/**
    * @access Public
    * @param String $Valor
*/
function setIndice($valor) { $this->stIndice    = $valor; }

/**
    * @access Public
    * @param String $Valor
*/
function setFuncao($valor) { $this->stFuncao    = $valor; }

/**
    * @access Public
    * @param String $Valor
*/
public function setTipTitle($valor) { $this->stTipTitle    = $valor; }

/**
    * @access Public
    * @return String
*/
function getLabel() { return $this->stLabel;           }

/**
    * @access Public
    * @return Boolean
*/
function getQuebra() { return $this->boQuebra;          }

/**
    * @access Public
    * @return String
*/
function getReferencia() { return $this->stReferencia;      }

/**
    * @access Public
    * @return String
*/
function getIndice() { return $this->stIndice;          }

/**
    * @access Public
    * @return String
*/
function getFuncao() { return $this->stFuncao;          }

/**
    * Método construtor
    * @access Public
*/

public function getTipTitle() { return $this->stTipTitle;          }

/**
    * @access Public
    * @return String
*/
function Aba()
{
    $this->setLabel("");
    $this->setReferencia("layer_");
    $this->setIndice("0");
    $this->setQuebra(false);
}

}
?>
