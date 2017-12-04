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
* Gerar imagens conforme os valores setados pelo usuário
* Data de Criação: 05/02/2003

* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00

*/

include_once ( CLA_EVENTO );
include_once ( CLA_OBJETO );

/**
    * Classe de que monta o HTML da Imagem

    * @package framework
    * @subpackage componentes
*/
class Img extends Objeto
{
/**
    * @access Private
    * @var String
*/
var $stRotulo;

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
var $stAlign;

/**
    * @access Private
    * @var Integer
*/
var $inBorder;

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
    * @var String
*/
var $stCaminho;

/**
    * @access Private
    * @var String
*/
var $stDir;

/**
    * @access Private
    * @var String
*/
var $stId;

/**
    * @access Private
    * @var String
*/
var $stLang;

/**
    * @access Private
    * @var String
*/
var $stStyle;

/**
    * @access Private
    * @var String
*/
var $stTitle;

/**
    * @access Private
    * @var Object
*/
var $obEvento;

/**
    * @access Private
    * @var String
*/
var $stHtml;

/**
    * @access Private
    * @var Integer
*/
var $inTabIndex;

//SETTERS
/**
    * @access Public
    * @param String $valor
*/
function setRotulo($valor) { $this->stRotulo     = $valor; }

/**
    * @access Public
    * @param Boolean $valor
*/
function setNull($valor) { $this->boNull	    = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setDefinicao($valor) { $this->stDefinicao  = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setAlign($valor) { $this->stAlign      = $valor; }

/**
    * @access Public
    * @param Integer $valor
*/
function setBorder($valor) { $this->inBorder     = $valor; }

/**
    * @access Public
    * @param Integer $valor
*/
function setWidth($valor) { $this->inWidth      = $valor; }

/**
    * @access Public
    * @param Integer $valor
*/
function setHeight($valor) { $this->inHeight     = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setCaminho($valor) { $this->stCaminho    = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setDir($valor) { $this->stDir        = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setId($valor) { $this->stId         = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setLang($valor) { $this->stLang       = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setStyle($valor) { $this->stStyle      = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setTitle($valor) { $this->stTitle      = $valor; }

/**
    * @access Public
    * @param Object $valor
*/
function setEvento($valor) { $this->obEvento     = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setHtml($valor) { $this->stHtml       = $valor; }

/**
    * @access Public
    * @param Integer $valor
*/
function setTabIndex($valor) { $this->inTabIndex   = $valor; }

//GETTERS
/**
    * @access Public
    * @return String
*/
function getRotulo() { return $this->stRotulo;     }

/**
    * @access Public
    * @return Boolean
*/
function getNull() { return $this->boNull;       }

/**
    * @access Public
    * @return String
*/
function getDefinicao() { return $this->stDefinicao;  }

/**
    * @access Public
    * @return String
*/
function getAlign() { return $this->stAlign;      }

/**
    * @access Public
    * @return Integer
*/
function getBorder() { return $this->inBorder;     }

/**
    * @access Public
    * @return Integer
*/
function getWidth() { return $this->inWidth;     	}

/**
    * @access Public
    * @return Integer
*/
function getHeight() { return $this->inHeight;     }

/**
    * @access Public
    * @return String
*/
function getCaminho() { return $this->stCaminho;    }

/**
    * @access Public
    * @return String
*/
function getDir() { return $this->stDir;        }

/**
    * @access Public
    * @return String
*/
function getId() { return $this->stId;         }

/**
    * @access Public
    * @return String
*/
function getLang() { return $this->stLang;       }

/**
    * @access Public
    * @return String
*/
function getStyle() { return $this->stStyle;      }

/**
    * @access Public
    * @return String
*/
function getTitle() { return $this->stTitle;      }

/**
    * @access Public
    * @return Object
*/
function getEvento() { return $this->obEvento;     }

/**
    * @access Public
    * @return String
*/
function getHtml() { return $this->stHtml;       }

/**
    * @access Public
    * @return Integer
*/
function getTabIndex() { return $this->inTabIndex;   }

/**
    * Método Construtor
    * @access Public
*/
function Img()
{
    $obEvento = new Evento;
    $this->setEvento( $obEvento );
    $this->setDefinicao( 'Img' );
    $this->setTabIndex ( 1 );
}

/**
    * Monta o HTML do Objeto Img
    * @access Protected
*/
function montaHTML()
{
    $stHtml = "<img ";
    if ( $this->getCaminho() ) {
        $stHtml .= "src =\"".$this->getCaminho()."\" ";
    }
    if ( $this->getBorder() !== false ) {
        $stHtml .= "border=\"".$this->getBorder()."\"";
    }
    if ( $this->getWidth() !== false ) {
        $stHtml .= "width=\"".$this->getWidth()."\"";
    }
    if ( $this->getHeight() !== false ) {
        $stHtml .= "height=\"".$this->getHeight()."\"";
    }
    if ( $this->getDir() ) {
        $stHtml .= "dir=\"".$this->getDir()."\" ";
    }
    if ( $this->getId() ) {
        $stHtml .= " id=\"".$this->getId()."\" ";
    }
    if ( $this->getLang() ) {
        $stHtml .= "lang=\"".$this->getLang()."\" ";
    }
    if ( $this->getStyle() ) {
        $stHtml .= " style=\"".$this->getStyle()."\" ";
    }
    if ( $this->getAlign() ) {
        $stHtml .= " align=\"".$this->getAlign()."\" ";
    }
    if ( $this->getTitle() ) {
        $stHtml .= " title=\"".$this->getTitle()."\" ";
    }
    if ( $this->getTabIndex() ) {
        $stHtml .= " tabIndex=\"".$this->getTabIndex()."\" ";
    }
    if ( $this->obEvento->getOnFocus() ) {
        $stHtml .= "onfocus=\"JavaScript:".$this->obEvento->getOnFocus()."\" ";
    }
    if ( $this->obEvento->getOnSelect() ) {
        $stHtml .= "onselect=\"JavaScript:".$this->obEvento->getOnSelect()."\" ";
    }
    if ( $this->obEvento->getOnBlur() ) {
        $stHtml .= "onblur=\"JavaScript:".$this->obEvento->getOnBlur()."\" ";
    }
    if ( $this->obEvento->getOnChange() ) {
        $stHtml .= "onchange=\"JavaScript:".$this->obEvento->getOnChange()."\" ";
    }
    if ( $this->obEvento->getOnClick() ) {
        $stHtml .= "onclick=\"JavaScript:".$this->obEvento->getOnClick()."\" ";
    }
    if ( $this->obEvento->getOnDblClick() ) {
        $stHtml .= "ondblclick=\"JavaScript:".$this->obEvento->getOnDblClick()."\" ";
    }
    if ( $this->obEvento->getOnMouseDown() ) {
        $stHtml .= "onmousedown=\"JavaScript:".$this->obEvento->getOnMouseDown()."\" ";
    }
    if ( $this->obEvento->getOnMouseUp() ) {
        $stHtml .= "onmouseup=\"JavaScript:".$this->obEvento->getOnMouseUp()."\" ";
    }
    if ( $this->obEvento->getOnMouseOver() ) {
        $stHtml .= "onmouseover=\"JavaScript:".$this->obEvento->getOnMouseOver()."\" ";
    }
    if ( $this->obEvento->getOnMouseMove() ) {
        $stHtml .= "onmousemove=\"JavaScript:".$this->obEvento->getOnMouseMove()."\" ";
    }
    if ( $this->obEvento->getOnMouseOut() ) {
        $stHtml .= "onmouseout=\"JavaScript:".$this->obEvento->getOnMouseOut()."\" ";
    }
    if ( $this->obEvento->getOnKeyPress() ) {
        $stHtml .= "onkeypress=\"JavaScript:".$this->obEvento->getOnKeyPress()."\" ";
    }
    if ( $this->obEvento->getOnKeyDown() ) {
        $stHtml .= "onkeydown=\"JavaScript:".$this->obEvento->getOnKeyDown()."\" ";
    }
    if ( $this->obEvento->getOnKeyUp() ) {
        $stHtml .= "onkeyup=\"JavaScript:".$this->obEvento->getOnKeyUp()."\" ";
    }
    $stHtml = $stHtml.">";
    $this->setHtml( $stHtml );
}

/**
    * Imprime o HTML do Objeto Img na tela (echo)
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
