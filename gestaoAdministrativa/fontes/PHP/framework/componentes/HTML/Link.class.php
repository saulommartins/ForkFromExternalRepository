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
* Gerar o componente estilo link (href) na interface
* Data de Criação: 28/05/2004

* @author Desenvolvedor: Diego Barbosa Victoria

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00

*/

include_once( CLA_LABEL  );
include_once( CLA_EVENTO );

/**
    * Classe que monta o HTML do Link é uma extensão de Label
    * @author Desenvolvedor: Diego Barbosa Victoria
*/
class Link extends Label
{
/**
    * @access Private
    * @var String
*/
var $stLinkTitle;
/**
    * @access Private
    * @var String
*/
var $stHref;
/**
    * @access Private
    * @var String
*/
var $stTarget;
/**
    * @access Private
    * @var Object
*/
var $obEvento;

/**
    * @access Public
    * @param String $valor
*/
function setLinkTitle($valor) { $this->stLinkTitle  = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setHref($valor) { $this->stHref       = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setTarget($valor) { $this->stTarget     = $valor; }

/**
    * @access Public
    * @return String
*/
function getLinkTitle() { return $this->stLinkTitle;  }
/**
    * @access Public
    * @return String
*/
function getHref() { return $this->stHref;       }
/**
    * @access Public
    * @return String
*/
function getTarget() { return $this->stTarget;     }

/**
    * Método Construtor
    * @access Public
*/
function Link()
{
    parent::Label();
    $this->setName      ( '' );
    $this->setDefinicao ( 'Link' );
    //$this->setTarget    ( '_blank' );
    $this->obEvento     = new Evento;
}

/**
    * Monta o HTML do Objeto Link
    * @access Protected
*/
function montaHTML()
{
    parent::montaHTML();
    $stHtml = '<a  ';

    if ( ($this->getLinkTitle()) ) {
        $stHtml .= ' title="'.$this->getLinkTitle().'" ';
    }
    if ( ($this->getTarget()) ) {
        $stHtml .= ' target="'.$this->getTarget().'" ';
    }
    if ( ($this->getHref()) || ($this->getValue()) ) {
        $stHtml .= ' href="';
        $stHtml .= ( $this->getHref() ) ? $this->getHref() : $this->getValue();
        $stHtml .= '" ';
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
    if ( $this->getId() ) {
        $stHtml .= "id=\"".$this->getId()."\" ";
    }
    $stHtml .= ">";
    if ( $this->getValue() ) {
        $stHtml .= $this->getValue();
    }
    $stHtml .= '</a>';
    $this->setHtml( $stHtml );
}

}

?>
