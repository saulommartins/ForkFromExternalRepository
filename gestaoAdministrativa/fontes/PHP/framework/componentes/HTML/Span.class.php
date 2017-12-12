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
* Gerar o componente tipo SPAN de acordo com os valores setados pelo usuário.
* Data de Criação: 03/03/2004

* @author Analista: Cassiano de Vasconcellos Ferreira
* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00

*/

/**
    * Classe que gera o HTML do Span
*/
class Span
{
/**
    * @access Private
    * @var String
*/
var $Id;

/**
    * @access Private
    * @var String
*/
var $stValue;

/**
    * @access Private
    * @var Boolean
*/
var $boNull;

/**
    * @access Private
    * @var String
*/
var $stDefinicao;

/**
    * @access Private
    * @var String
*/
var $stHTML;

/**
    * @access Private
    * @var String
*/
var $stId;

/**
    * @access Private
    * @var String
*/
var $stName;

/**
  * @access Private
  * @var String
  */
var $stStyle;

/**
 * @access Private
 * @var Boolean
 */
var $boDestaque;

//SETTERS
/**
    * @access Public
    * @param String $Valor
*/
function setId($Valor) {$this->Id             = $Valor;  }

/**
    * @access Public
    * @param String $Valor
*/
function setValue($Valor) {$this->stValue        = $Valor;  }

/**
    * @access Public
    * @param Boolean $Valor
*/
function setNull($valor) {$this->boNull         = $valor;  }

/**
    * @access Public
    * @param String $Valor
*/
function setDefinicao($valor) {$this->stDefinicao    = $valor;  }

/**
    * @access Public
    * @param String $Valor
*/
function setHTML($Valor) {$this->stHTML         = $Valor;  }

/**
    * @access Public
    * @param String $Valor
*/
function setDestaque($Valor) {$this->boDestaque     = $Valor;  }
/**
  * @access Public
  * @param String $valor
*/
function setStyle($valor) { $this->stStyle      = $valor; }

/**
  * @access Public
  * @param String $valor
*/
function setClass($valor) { $this->stClass      = $valor; }

//GETTERS
/**
    * @access Public
    * @return String
*/
function getId() {return $this->Id;            }

/**
    * @access Public
    * @return String
*/
function getValue() {return $this->stValue;       }

/**
    * @access Public
    * @return Boolean
*/
function getNull() {return $this->boNull;        }

/**
    * @access Public
    * @return String
*/
function getDefinicao() {return $this->stDefinicao;   }

/**
    * @access Public
    * @return String
*/
function getHTML() {return $this->stHTML;        }

/**
    * @access Public
    * @return String
*/
function getDestaque() {return $this->boDestaque;   }

/**
  * @access Public
  * @return String
  */
function getStyle() { return $this->stStyle;      }

/**
  * @access Public
  * @return String
  */
function getClass() { return $this->stClass;      }

/**
    * Método construtor
    * @access Public
*/
function Span()
{
    $this->setDefinicao     ( "SPAN" );
    $this->setNull          ( true );
}

/**
    * Monta o HTML do Objeto Span
    * @access Protected
*/
function montaHTML()
{
    $stHTML = "<span ";

    if ($this->getId()) {
        $stHTML .= "id=\"".$this->getId()."\"";
    }

    if ($this->getDestaque()) {
        $stHTML .= ' class="spanDestaque" ';
    }
    
    if ($this->getClass()) {
        $stHTML .= " class=\"".$this->getClass()."\" ";
    }

    if ($this->getStyle()) {
        $stHTML .= " style=\"".$this->getStyle()."\" ";
    }

    $stHTML .= ">";

    if ($this->getValue() !== false) {
        $stHTML .= " ".$this->getValue()." ";
    }

    $stHTML .= " </span>";
    $this->setHTML( $stHTML );
}

/**
    * Imprime o HTML do Objeto Span na tela (echo)
    * @access Public
*/
function show()
{
    $this->montaHTML();
    echo $this->getHTML();
}

}
?>
