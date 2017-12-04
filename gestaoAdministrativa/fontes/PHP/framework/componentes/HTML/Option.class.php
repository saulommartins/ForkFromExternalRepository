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
* Gerar o componente tipo option de acordo com os valores setados pelo usuário.
* Este componete sera usado na classe Select.
* Data de Criação: 05/02/2003

* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00

*/

/**
    * Classe que gera o HTML para o Option

    * @package framework
    * @subpackage componentes
*/
class Option extends Objeto
{
/**
    * @access Private
    * @var String
*/
var $stValor;

/**
    * @access Private
    * @var String
*/
var $stCampo;

/**
    * @access Private
    * @var String
*/
var $stSelected;

/**
    * @access Private
    * @var String
*/
var $stLabel;

/**
    * @access Private
    * @var String
*/
var $stId;

/**
    * @access Private
    * @var Boolean
*/
var $boDisabled;

/**
    * @access Private
    * @var Boolean
*/
var $boReadOnly;
/**
    * @access Private
    * @var Boolean
*/
var $stTiTle;
//SETTERS
/**
    * @access Public
    * @param String $Valor
*/
function setValor($valor) { $this->stValor      = $valor; }

/**
    * @access Public
    * @param String $Valor
*/
function setCampo($valor) { $this->stCampo      = $valor; }

/**
    * @access Public
    * @param String $Valor
*/
function setSelected($valor) { $this->stSelected   = $valor; }

/**
    * @access Public
    * @param String $Valor
*/
function setLabel($valor) { $this->stLabel      = $valor; }

/**
    * @access Public
    * @param String $Valor
*/
function setId($valor) { $this->stId         = $valor; }

/**
    * @access Public
    * @param Boolean $Valor
*/
function setDisabled($valor) { $this->boDisabled   = $valor; }

/**
    * @access Public
    * @param Boolean $Valor
*/
function setReadOnly($valor) { $this->boReadOnly   = $valor; }
/**
    * @access Public
    * @param Boolean $Valor
*/
function setTitle($valor) { $this->stTitle   = $valor; }
//GETTERS
/**
    * @access Public
    * @return Boolean
*/
function getValor() { return $this->stValor;      }

/**
    * @access Public
    * @return Boolean
*/
function getCampo() { return $this->stCampo;      }

/**
    * @access Public
    * @return Boolean
*/
function getSelected() { return $this->stSelected;   }

/**
    * @access Public
    * @return Boolean
*/
function getLabel() { return $this->stLabel;      }

/**
    * @access Public
    * @return Boolean
*/
function getId() { return $this->stId;         }

/**
    * @access Public
    * @return Boolean
*/
function getDisabled() { return $this->boDisabled;   }

/**
    * @access Public
    * @return Boolean
*/
function getReadOnly() { return $this->boReadOnly;   }

/**
    * @access Public
    * @return Boolean
*/
function getTitle() { return $this->stTitle;   }
/**
    * Método construtor
    * @access Public
*/
function Option()
{
    $this->setDisabled  ( false );
    $this->setReadOnly  ( false );
}

}
