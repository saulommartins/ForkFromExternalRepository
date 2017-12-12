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
* Gerar o componente Label da interface
* Data de Criação: 03/03/2004

* @author Desenvolvedor: Diego Barbosa Victoria

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00

*/

include_once ( CLA_OBJETO );

/**
    * Classe de que monta o HTML do Label
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package framework
    * @subpackage componentes
*/
class Label extends Objeto
{
/**
    * @access Private
    * @var String
*/
var $stValue;
/**
    * @access Private
    * @var String
*/
var $stId;
/**
    * @access Private
    * @var String
*/
var $stRotulo;
/**
    * @access Private
    * @var String
*/
var $stTitle;
/**
    * @access Private
    * @var Boolean
*/
var $boNull;
/**
    * @access Private
    * @var String
*/
var $stHtml;

/**
    * @access Private
    * @var String
*/
var $stStyle;

/**
    * @access Private
    * @var Int
*/
var $inCodObjeto;

/**
    * @access Public
    * @param String $valor
*/
function setName($valor) { $this->stName       = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setId($valor) { $this->stId         = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setValue($valor) { $this->stValue      = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setRotulo($valor) { $this->stRotulo     = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setTitle($valor) { $this->stTitle = mb_check_encoding($valor, 'UTF-8') ? utf8_decode($valor) : $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setDefinicao($valor) { $this->stDefinicao  = $valor; }
/**
    * @access Public
    * @param Boolean $valor
*/
function setNull($valor) { $this->boNull       = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setHtml($valor) { $this->stHtml       = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setStyle($valor) { $this->stStyle      = $valor; }

/**
    * @access Public
    * @return String
*/
function getName() { return $this->stName;       }
/**
    * @access Public
    * @return String
*/
function getId() { return $this->stId;         }
/**
    * @access Public
    * @return String
*/
function getValue() { return $this->stValue;      }
/**
    * @access Public
    * @return String
*/
function getRotulo() { return $this->stRotulo;     }
/**
    * @access Public
    * @return String
*/
function getTitle() { return $this->stTitle;      }
/**
    * @access Public
    * @return String
*/
function getDefinicao() { return $this->stDefinicao;  }
/**
    * @access Public
    * @return Boolean
*/
function getNull() { return $this->boNull;       }
/**
    * @access Public
    * @return String
*/
function getHtml() { return $this->stHtml;       }
/**
    * @access Public
    * @return String
*/
function getStyle() { return $this->stStyle;      }

/**
    * Método Construtor
    * @access Public
*/
function Label()
{
    $this->setNull      ( true );
    $this->setName      ( 'Label' );
    $this->setDefinicao ( 'Label' );
}

/**
    * Monta o HTML do Objeto Label
    * @access Protected
*/
function montaHTML()
{
    $this->setNull      ( true );

    $obSpn = new Span;
    $obSpn->setId    ($this->getId());
    $obSpn->setValue (($this->getValue()!== false) ? $this->getValue() : '&nbsp;');
    $obSpn->setStyle ($this->getStyle());
    $obSpn->montaHTML();
    $this->setHtml   ($obSpn->getHtml());
}

/**
    * Imprime o HTML do Objeto Label na tela (echo)
    * @access Public
*/
function show()
{
    $this->montaHtml();
    $stHtml = $this->getHtml();
    $stHtml =  trim( $stHtml )."\n";
    echo $stHtml;
}

}

?>
