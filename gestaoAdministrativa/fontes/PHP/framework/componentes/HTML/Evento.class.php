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
* Manipular os eventos HTML
* Data de Criação: 05/02/2003

* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00

*/

/**
    * Classe que manipula eventos HTML

    * @package framework
    * @subpackage componentes
*/
class Evento extends Objeto
{
/**
    * @access Private
    * @var String
*/
var $onFocus;

/**
    * @access Private
    * @var String
*/
var $onSelect;

/**
    * @access Private
    * @var String
*/
var $onSubmit;

/**
    * @access Private
    * @var String
*/
var $onBlur;

/**
    * @access Private
    * @var String
*/
var $onChange;

/**
    * @access Private
    * @var String
*/
var $onClick;

/**
    * @access Private
    * @var String
*/
var $onDblClick;

/**
    * @access Private
    * @var String
*/
var $onMouseDown;

/**
    * @access Private
    * @var String
*/
var $onMouseUp;

/**
    * @access Private
    * @var String
*/
var $onMouseOver;

/**
    * @access Private
    * @var String
*/
var $onMouseMove;

/**
    * @access Private
    * @var String
*/
var $onMouseOut;

/**
    * @access Private
    * @var String
*/
var $onKeyPress;

/**
    * @access Private
    * @var String
*/
var $onKeyDown;

/**
    * @access Private
    * @var String
*/
var $onKeyUp;

//SETTERS
/**
    * @access Public
    * @param String $valor
*/
function setOnFocus($valor) { $this->onFocus      = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setOnSelect($valor) { $this->onSelect     = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setOnSubmit($valor) { $this->onSubmit     = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setOnBlur($valor) { $this->onBlur       = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setOnChange($valor) { $this->onChange     = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setOnClick($valor) { $this->onClick      = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setOnDblClick($valor) { $this->onDblClick   = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setOnMouseDown($valor) { $this->onMouseDown  = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setOnMouseUp($valor) { $this->onMouseUp    = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setOnMouseOver($valor) { $this->onMouseOver  = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setOnMouseMove($valor) { $this->onMouseMove  = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setOnMouseOut($valor) { $this->onMouseOut   = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setOnKeyPress($valor) { $this->onKeyPress   = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setOnKeyDown($valor) { $this->onKeyDown    = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setOnKeyUp($valor) { $this->onKeyUp      = $valor; }

//GETTERS
/**
    * @access Public
    * @return String
*/
function getOnFocus() { return $this->onFocus;      }

/**
    * @access Public
    * @return String
*/
function getOnSelect() { return $this->onSelect;     }

/**
    * @access Public
    * @return String
*/
function getOnSubmit() { return $this->onSubmit;     }

/**
    * @access Public
    * @return String
*/
function getOnBlur() { return $this->onBlur;       }

/**
    * @access Public
    * @return String
*/
function getOnChange() { return $this->onChange;     }

/**
    * @access Public
    * @return String
*/
function getOnClick() { return $this->onClick;      }

/**
    * @access Public
    * @return String
*/
function getOnDblClick() { return $this->onDblClick;   }

/**
    * @access Public
    * @return String
*/
function getOnMouseDown() { return $this->onMouseDown;  }

/**
    * @access Public
    * @return String
*/
function getOnMouseUp() { return $this->onMouseUp;    }

/**
    * @access Public
    * @return String
*/
function getOnMouseOver() { return $this->onMouseOver;  }

/**
    * @access Public
    * @return String
*/
function getOnMouseMove() { return $this->onMouseMove;  }

/**
    * @access Public
    * @return String
*/
function getOnMouseOut() { return $this->onMouseOut;   }

/**
    * @access Public
    * @return String
*/
function getOnKeyPress() { return $this->onKeyPress;   }

/**
    * @access Public
    * @return String
*/
function getOnKeyDown() { return $this->onKeyDown;    }

/**
    * @access Public
    * @return String
*/
function getOnKeyUp() { return $this->onKeyUp;      }

/**
    * Método Construtor
    * @access Public
*/
function Evento()
{
}

}
?>
