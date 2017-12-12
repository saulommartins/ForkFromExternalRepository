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
* Gerar o componente tipo textarea de acordo com os valores setados pelo usuário.
* Data de Criação: 05/02/2003

* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00

*/

/**
    * Gera o componente tipo textarea

    * @package framework
    * @subpackage componentes
*/
class TextArea extends Componente
{
/**
    * @access Private
    * @var Integer
*/
var $inCols;

/**
    * @access Private
    * @var Integer
*/
var $inRows;

/**
    * @access Private
    * @var Integer
*/
var $inMaxCaracteres;

/**
    * @access Private
    * @var Integer
*/
var $boLabel;

//SETTERS
/**
    * @access Public
    * @param Integer $valor
*/
function setCols($valor) { $this->inCols           = $valor; }

/**
    * @access Public
    * @param Integer $valor
*/
function setRows($valor) { $this->inRows           = $valor; }

/**
    * @access Public
    * @param Integer $valor
*/
function setMaxCaracteres($valor) { $this->inMaxCaracteres  = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setLabel($valor) { $this->boLabel  = $valor; }

//GETTERS
/**
    * @access Public
    * @return Integer
*/
function getCols() { return $this->inCols;           }

/**
    * @access Public
    * @return Integer
*/
function getRows() { return $this->inRows;           }

/**
    * @access Public
    * @return Integer
*/
function getMaxCaracteres() { return $this->inMaxCaracteres;  }

/**
    * @access Public
    * @return Integer
*/
function getLabel() { return $this->boLabel;  }

/**
    * Método Construtor
    * @access Public
*/
function TextArea()
{
    parent::Componente();//CHAMA O METODO CONSTRUTOR DA CLASSE BASE
    $this->setCols      ( 30 );
    $this->setRows      ( 5 );
    $this->setName      ( "textArea" );
    $this->setDefinicao ( "textarea" );
    $this->setLabel     ( false );
}

/**
    * Monta o HTML do Objeto TextArea
    * @access Protected
*/
function montaHtml()
{
    $stValue = $this->getValue();
    $this->setValue( "" );
    if ( $this->getMaxCaracteres() ) {
        $this->obEvento->setOnKeyPress( $this->obEvento->getOnKeyPress()."return validaMaxCaracter(this, ".$this->getMaxCaracteres().", event, false);" );
        $this->obEvento->setOnBlur    ( $this->obEvento->getOnBlur()."return validaMaxCaracter(this, ".$this->getMaxCaracteres().", event, true);"      );
    }
    if ( $this->getLabel() ) {
        $this->setStyle ( 'display:none;' );
    }
    parent::montaHtml();
    $stHtml = $this->getHtml();
    $stHtml = substr( $stHtml, 0, strlen($stHtml) - 1 );
    $stHtml = "<textarea ".$stHtml;
    if ( $this->getCols() ) {
        $stHtml .= "cols=\"".$this->getCols()."\" ";
    }
    if ( $this->getRows() ) {
        $stHtml .= "rows=\"".$this->getRows()."\" ";
    }
    $stHtml = trim($stHtml);
    $stHtml .= ">".$stValue."</textarea>";
    if ( $this->getLabel() ) {
        $stHtml .= "<span id=\"".$this->getId()."_label\" >";
        $stHtml .= $stValue;
        $stHtml .= "</span>";
    }
    $this->setHtml( $stHtml );
}
}
?>
