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
* Tag de iFrame
* Data de Criação: 08/02/2003

* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00

*/

/**
    * Classe de que monta o HTML do IFrame

    * @package framework
    * @subpackage componentes
*/
class IFrame extends ComponenteBase
{
/**
    * @access Private
    * @var String
*/
var $stName;

/**
    * @access Private
    * @var String
*/
var $stSrc;

/**
    * @access Private
    * @var Integer
*/
var $inWidth;

/**
    * @access Private
    * @var Integer
*/
var $inHeight;

/**
    * @access Private
    * @var Integer
*/
var $inFrameBorder;

/**
    * @access Private
    * @var String
*/
var $stHtml;

/**
    * Método Construtor
    * @access Public
*/
function IFrame()
{
    parent::ComponenteBase();
    $this->setName          ( "IFrame" );
    $this->setFrameBorder   ( "0" );
    $this->setWidth         ( "0" );
    $this->setHeight        ( "0" );
    $this->setDefinicao     ( "IFRAME" );
}

/**
    * @access Public
    * @param String $valor
*/
function setName($valor) { $this->stName       = $valor;   }

/**
    * @access Public
    * @param String $valor
*/
function setRotulo($valor) { $this->stRotulo     = $valor;   }

/**
    * @access Public
    * @param String $valor
*/
function setNull($valor) { $this->boNull       = $valor;   }

/**
    * @access Public
    * @param String $valor
*/
function setTitle($valor) { $this->stTitle      = $valor;   }

/**
    * @access Public
    * @param String $valor
*/
function setSrc($valor) { $this->stSrc        = $valor;   }

/**
    * @access Public
    * @param String $valor
*/
function setWidth($valor) { $this->inWidth      = $valor;   }

/**
    * @access Public
    * @param String $valor
*/
function setHeight($valor) { $this->inHeight     = $valor;   }

/**
    * @access Public
    * @param String $valor
*/
function setFrameBorder($valor) { $this->inFrameBorder= $valor;   }

/**
    * @access Public
    * @param String $valor
*/
function setHtml($valor) { $this->stHtml       = $valor;   }

/**
    * @access Public
    * @return String
*/
function getName() { return $this->stName;       }

/**
    * @access Public
    * @return String
*/
function getRotulo() { return $this->stRotulo;     }

/**
    * @access Public
    * @return String
*/
function getNull() { return $this->boNull;       }

/**
    * @access Public
    * @return String
*/
function getTitle() { return $this->stTitle;      }

/**
    * @access Public
    * @return String
*/
function getSrc() { return $this->stSrc;        }

/**
    * @access Public
    * @return String
*/
function getWidth() { return $this->inWidth;      }

/**
    * @access Public
    * @return String
*/
function getHeight() { return $this->inHeight;     }

/**
    * @access Public
    * @return String
*/
function getFrameBorder() { return $this->inFrameBorder;}

/**
    * @access Public
    * @return String
*/
function getHtml() { return $this->stHtml;       }

/**
    * Monta o HTML do Objeto IFrame
    * @access Protected
*/
function montaHtml()
{
    $stHtml = '<IFrame ';
    if ( $this->getSrc() ) {
        $stHtml .= ' src="'.$this->getSrc().'" ';
    }
    $stHtml .= ' name="'.       $this->getName()        .'"';
    $stHtml .= ' id="'.         $this->getName()        .'"';
    $stHtml .= ' width="'.      $this->getWidth()       .'" ';
    $stHtml .= ' height="'.     $this->getHeight()      .'" ';
    $stHtml .= ' frameborder="'.$this->getFrameBorder() .'" ';

    $stHtml .= " ></IFrame>\n";
    $this->setHtml( $stHtml );
}

/**
    * Imprime o HTML do Objeto IFrame na tela (echo)
    * @access Public
*/
function show()
{
    $this->montaHtml();
    echo  $this->getHtml();
}

}
?>
