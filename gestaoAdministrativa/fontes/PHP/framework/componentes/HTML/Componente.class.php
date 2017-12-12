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
* Servir de base para a criação de componetes de formulario HTML
* Data de Criação: 05/02/2003

* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00

*/

/**
    * Classe que serve de base para os componentes HTML

    * @package framework
    * @subpackage componentes
*/
class Componente extends ComponenteBase
{
//PROPRIEDADES DA CLASSE
/**
    * @access Private
    * @var String
*/
var $stForm;

/**
    * @access Private
    * @var String
*/
var $stTipo;

/**
    * @access Private
    * @var String
*/
var $stName;

/**
    * @access Private
    * @var String
*/
var $stId;

/**
    * @access Private
    * @var Integer
*/
var $inTabIndex;

/**
    * @access Private
    * @var String
*/
var $stClass;

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
var $boUpperCase;

/**
    * @access Private
    * @var Boolean
*/
var $boLowerCase;

/**
    * @access Private
    * @var String
*/
var $stStyle;

/**
    * @access Private
    * @var String
*/
var $stAlt;

/**
    * @access Private
    * @var String
*/
var $stValue;

/**
    * @access Private
    * @var String
*/
var $stMiscelania;

/**
    * @access Private
    * @var String
*/
var $stHtml;

/**
    * @access Private
    * @var Object
*/
var $obEvento;

/**
    * @access Private
    * @var Boolean
*/
var $boNaoZero;

//METODOS DA CLASSE
//SETTERS
/**
    * @access Public
    * @param String $valor
*/
function setForm($valor) { $this->stForm       = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setTipo($valor) { $this->stTipo       = $valor; }

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
    * @param Integer $valor
*/
function setTabIndex($valor) { $this->inTabIndex   = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setClass($valor) { $this->stClass      = $valor; }

/**
    * @access Public
    * @param Boolean $valor
*/
function setDisabled($valor) { $this->boDisabled   = $valor; }

/**
    * @access Public
    * @param Boolean $valor
*/
function setReadOnly($valor) { $this->boReadOnly   = $valor; }

/**
    * @access Public
    * @param Boolean $valor
*/
function setUpperCase($valor) { $this->boUpperCase  = $valor; }

/**
    * @access Public
    * @param Boolean $valor
*/
function setLowerCase($valor) { $this->boLowerCase  = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setStyle($valor) { $this->stStyle      = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setAlt($valor) { $this->stAlt        = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setValue($valor) { $this->stValue      = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setMiscelania($valor) { $this->stMiscelania = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setHtml($valor) { $this->stHtml       = $valor; }

/**
    * @access Public
    * @param Boolean $valor
*/
function setNaoZero($valor) { $this->boNaoZero    = $valor; }

//GETTERS
/**
    * @access Public
    * @return String
*/
function getForm() { return $this->stForm;       }

/**
    * @access Public
    * @return String
*/
function getTipo() { return $this->stTipo;       }

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
    * @return Integer
*/
function getTabIndex() { return $this->inTabIndex;   }

/**
    * @access Public
    * @return String
*/
function getClass() { return $this->stClass;      }

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
function getUpperCase() { return $this->boUpperCase;  }

/**
    * @access Public
    * @return Boolean
*/
function getLowerCase() { return $this->boLowerCase;  }

/**
    * @access Public
    * @return String
*/
function getStyle() { return $this->stStyle;      }

/**
    * @access Public
    * @return String
*/
function getAlt() { return $this->stAlt;        }

/**
    * @access Public
    * @return String
*/
function getValue() { return $this->stValue;      }

/**
    * @access Public
    * @return String
*/
function getMiscelania() { return $this->stMiscelania; }

/**
    * @access Public
    * @return String
*/
function getHtml() { return $this->stHtml;       }

/**
    * @access Public
    * @return Boolean
*/
function getNaoZero() { return $this->boNaoZero;    }

/**
    * Método Construtor
    * @access Public
*/
function Componente()
{
    parent::ComponenteBase();
    $this->obEvento     = new Evento;
    $this->setForm      ( "frm" );
    $this->setTabIndex  ( 1 );
    $this->setDisabled  ( false );
    $this->setReadOnly  ( false );
    $this->setUpperCase ( false );
    $this->setLowerCase ( false );
    $this->setNaoZero   ( false );

    return true;
}

/**
    * Monta o HTML do Objeto
    * @access Protected
*/
function montaHtml()
{
    $stHtml  = "";
    if ( $this->getTipo() ) {
        $stHtml .= "<input type=\"".$this->getTipo()."\" ";
    }
    if ( $this->getName() ) {
        $stHtml .= "name=\"".$this->getName()."\" ";
    }
    if ( $this->getId() ) {
        $stHtml .= "id=\"".$this->getId()."\" ";
    }
    if ( $this->getTabIndex() ) {
        $stHtml .= "tabindex=\"".$this->getTabIndex()."\" ";
    }
    if ( $this->getClass() ) {
        $stHtml .= "class=\"".$this->getClass()."\" ";
    }
    if ( $this->getDisabled() ) {
        $stHtml .= "disabled=\"true\" ";
    }
    if ( $this->getReadOnly() ) {
        $stHtml .= "readonly ";
    }
    if ( $this->getUpperCase() ) {
        $this->obEvento->setOnBlur( "this.value=this.value.toUpperCase();".$this->obEvento->getOnBlur() );//CRIAR AS FUNÇÕES EM JAVASCRIPT PARA FAZER ISTO
    } elseif ( $this->getLowerCase() ) {
        $this->obEvento->setOnBlur( "this.value=this.value.toLowerCase();".$this->obEvento->getOnBlur() );//CRIAR AS FUNÇÕES EM JAVASCRIPT PARA FAZER ISTO
    }
    if ( $this->getStyle() ) {
        $stHtml .= "style=\"".$this->getStyle()."\" ";
    }
    if ( $this->getAlt() ) {
        $stHtml .= "alt=\"".$this->getAlt()."\" ";
    }
    if ( !is_null( $this->getValue() ) ) {
        $stHtml .= "value=\"".$this->getValue()."\" ";
    }

    if ( $this->obEvento->getOnSelect() ) {
        $stHtml .= "onselect=\"JavaScript:".$this->obEvento->getOnSelect()."\" ";
    }
    if ( $this->obEvento->getOnSubmit() ) {
        $stHtml .= "onselect=\"JavaScript:".$this->obEvento->getOnSubmit()."\" ";
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

    if ( $this->getMiscelania() ) {
        $stHtml .= $this->getMiscelania();
    }

    if ( $this->obEvento->getOnChange ()  and ( ( $this->obEvento->getOnBlur()     )
                                             or ( $this->obEvento->getOnKeyPress() )
                                             or ( $this->obEvento->getOnKeyUp()    ) ) )
    {
       $this->obEvento->setOnFocus ( " oldValue = this.value;" .  $this->obEvento->getOnFocus() );
       $this->obEvento->setOnBlur ( $this->obEvento->getOnBlur() . "if (oldValue != this.value) {" . $this->obEvento->getOnChange() . " }" );
       $this->obEvento->setOnChange ( '' );
    }

    if ( $this->obEvento->getOnBlur() ) {
        $stHtml .= "onblur=\"JavaScript:".$this->obEvento->getOnBlur()."\" ";
    }

    if ( $this->obEvento->getOnFocus() ) {
        $stHtml .= "onfocus=\"JavaScript:".$this->obEvento->getOnFocus()."\" ";
    }

    if ( $this->obEvento->getOnChange() ) {
        $stHtml .= "onchange=\"JavaScript:".$this->obEvento->getOnChange()."\" ";
    }

    $stHtml .= ">";
    $this->setHtml($stHtml);

    return true;
}

/**
    * Imprime o HTML do Objeto na tela (echo)
    * @access Public
*/
function show()
{
    $this->montaHtml();
    $stHtml = $this->getHtml();
    $stHtml .= "\n";
    echo $stHtml;
}
}
?>
