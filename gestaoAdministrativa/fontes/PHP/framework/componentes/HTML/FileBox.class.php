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
* Gerar o componente tipo file de acordo com os valores setados pelo Usuário
* Data de Criação: 03/08/2004

* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
* @author Documentor: Diego Barbosa Victoria

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00

*/

/**
    * Gera o componente tipo file de acordo com os valores setados pelo Usuário
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
    * @author Documentor: Diego Barbosa Victoria

    * @package framework
    * @subpackage componentes
*/
class FileBox extends Componente
{
//PROPRIEDADES DA CLASSE

/**
    * @access Private
    * @var Integer
*/
var $inMaxLength;

/**
    * @access Private
    * @var Integer
*/
var $inMinLength;//DETERMINA SE O CAMPO TERA UM NUMERO MINIMO DE CARACTERES

/**
    * @access Private
    * @var Integer
*/
var $inSize;

/**
    * @access Private
    * @var Boolean
*/
var $boInteiro;

/**
    * @access Private
    * @var Boolean
*/
var $boFloat;

/**
    * @access Private
    * @var Integer
*/
var $inDecimais;

/**
    * @access Private
    * @var Char
*/
var $chDecimal;//CARACTER DO DECIMAL

/**
    * @access Private
    * @var Char
*/
var $chMilhar;//CARACTER DO MILHAR

/**
    * @access Private
    * @var String
*/
var $stAlign;

/**
    * @access Private
    * @var Integer
*/
function setMaxLength($valor) { $this->inMaxLength  = $valor; }
/**
    * @access Private
    * @var Integer
*/
function setMinLength($valor) { $this->inMinLength  = $valor; }
/**
    * @access Private
    * @var Integer
*/
function setSize($valor) { $this->inSize       = $valor; }
/**
    * @access Private
    * @var Integer
*/
function setDecimais($valor) { $this->inDecimais   = $valor; }
/**
    * @access Private
    * @var Boolean
*/
function setInteiro($valor) { $this->boInteiro    = $valor; }
/**
    * @access Private
    * @var Boolean
*/
function setFloat($valor) { $this->boFloat      = $valor; }
/**
    * @access Private
    * @var String
*/
function setDecimal($valor) { $this->chDecimal    = $valor; }
/**
    * @access Private
    * @var String
*/
function setMilhar($valor) { $this->chMilhar     = $valor; }
/**
    * @access Private
    * @var String
*/
function setAlign($valor) { $this->stAlign      = $valor; }
/**
    * @access Private
    * @var Boolean
*/
function setToLowerCase($valor) { $this->boToLowerCase= $valor; }
/**
    * @access Private
    * @var Boolean
*/
function setToUpperCase($valor) { $this->boToUpperCase= $valor; }

/**
    * @access Private
    * @var Integer
*/
function getMaxLength() { return $this->inMaxLength;  }
/**
    * @access Private
    * @var Integer
*/
function getMinLength() { return $this->inMinLength;  }
/**
    * @access Private
    * @var Integer
*/
function getSize() { return $this->inSize;       }
/**
    * @access Private
    * @var Boolean
*/
function getInteiro() { return $this->boInteiro;    }
/**
    * @access Private
    * @var Integer
*/
function getDecimais() { return $this->inDecimais;   }
/**
    * @access Private
    * @var Boolean
*/
function getFloat() { return $this->boFloat;      }
/**
    * @access Private
    * @var String
*/
function getDecimal() { return $this->chDecimal;    }
/**
    * @access Private
    * @var String
*/
function getMilhar() { return $this->chMilhar;     }
/**
    * @access Private
    * @var String
*/
function getAlign() { return $this->stAlign;      }
/**
    * @access Private
    * @var Boolean
*/
function getToLowerCase() { return $this->boToLowerCase;}
/**
    * @access Private
    * @var Boolean
*/
function getToUpperCase() { return $this->boToUpperCase;}

/**
    * Método Construtor
    * @access Public
*/
function FileBox()
{
    parent::Componente();//CHAMA O METODO CONSTRUTOR DA CLASSE BASE
    $this->setMaxLength  ( 10 );
    $this->setSize       ( 10 );
    $this->setInteiro    ( false );
    $this->setFloat      ( false );
    $this->setDecimais   ( 2 );
    $this->setDecimal    ( "," );
    $this->setMilhar     ( "." );
    $this->setAlign      ( "left" );
    $this->setTipo       ( "file" );
    $this->setName       ( "stText" );
    $this->setDefinicao  ( "file" );
    $this->setToUpperCase( false );
    $this->setToLowerCase( false );
}

/**
    * Monta o HTML do Objeto FileBox
    * @access Protected
*/
function montaHtml()
{
    if ( $this->getInteiro() ) {
        $this->obEvento->setOnKeyPress( $this->obEvento->getOnKeyPress()."return inteiro( event );" );
    } elseif ( $this->getFloat() ) {
        $this->obEvento->setOnKeyPress( $this->obEvento->getOnKeyPress()."return tfloat( this, event );" );
        if ( $this->getDecimais() ) {
            $this->obEvento->setOnBlur( "floatDecimal(this, '".$this->getDecimais()."', event );" );
        }
    }
    if ( $this->getToUpperCase() ) {
        $this->obEvento->setOnBlur( $this->obEvento->getOnBlur()."toUpperCase(this);" );
    }
    if ( $this->getToLowerCase() ) {
        $this->obEvento->setOnBlur( $this->obEvento->getOnBlur()."toLowerCase(this);" );
    }
    if ( $this->getMinLength() ) {
        $this->obEvento->setOnBlur( $this->obEvento->getOnBlur()."validaMinLength(this,".$this->getMinLength().");" );
    }
    parent::montaHtml();
    $stHtml = $this->getHtml();
    $stHtml = substr( $stHtml, 0, strlen($stHtml) - 1 );
    if ( $this->getMaxLength() ) {
        $stHtml .= "maxlength=\"".$this->getMaxLength()."\" ";
    }
    if ( $this->getSize() ) {
        $stHtml .= "size=\"".$this->getSize()."\" ";
    }
    $stHtml .= "align=\"".$this->getAlign()."\" ";
    $stHtml .= ">";
    $this->setHtml($stHtml);
}

}
?>
