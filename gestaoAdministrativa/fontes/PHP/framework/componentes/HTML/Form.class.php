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
* Data de Criação: 09/02/2003

* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00

*/

/**
    * Classe de que monta o HTML do form

    * @package Interface
    * @subpackage Componente
*/
class Form extends Objeto
{
//PROPRIEDADES DA CLASSE
/**
    * @access Private
    * @var String
*/
var $stAction;

/**
    * @access Private
    * @var String
*/
var $stEncType;// default: application/x-www-form-urlencoded , multipart/form-data

/**
    * @access Private
    * @var String
*/
var $obEvento;

/**
    * @access Private
    * @var String
*/
var $stId;

/**
    * @access Private
    * @var String
*/
var $stMethod;

/**
    * @access Private
    * @var String
*/
var $stName;

/**
    * @access Private
    * @var String
*/
var $stTarget;

/**
    * @access Private
    * @var String
*/
var $stFoco;

//SETTERS
/**
    * @access Private
    * @var String
*/
function setAction($valor) { $this->stAction     = $valor; }

/**
    * @access Private
    * @var String
*/
function setEncType($valor) { $this->stEncType    = $valor; }

/**
    * @access Private
    * @var String
*/
function setEvento($valor) { $this->obEvento     = $valor; }

/**
    * @access Private
    * @var String
*/
function setId($valor) { $this->stId         = $valor; }

/**
    * @access Private
    * @var String
*/
function setMethod($valor) { $this->stMethod     = $valor; }

/**
    * @access Private
    * @var String
*/
function setName($valor) { $this->stName       = $valor; }

/**
    * @access Private
    * @var String
*/
function setTarget($valor) { $this->stTarget     = $valor; }

/**
    * @access Private
    * @var String
*/
function setFoco($valor) { $this->stFoco       = $valor; }

//GETTERS
/**
    * @access Private
    * @var String
*/
function getAction() { return $this->stAction;     }

/**
    * @access Private
    * @var String
*/
function getEncType() { return $this->stEncType;    }

/**
    * @access Private
    * @var String
*/
function getEvento() { return $this->obEvento;     }

/**
    * @access Private
    * @var String
*/
function getId() { return $this->stId;         }

/**
    * @access Private
    * @var String
*/
function getMethod() { return $this->stMethod;     }

/**
    * @access Private
    * @var String
*/
function getName() { return $this->stName;       }

/**
    * @access Private
    * @var String
*/
function getTarget() { return $this->stTarget;     }

/**
    * @access Private
    * @var String
*/
function getFoco() { return $this->stFoco;       }

/**
    * Método Construtor
    * @access Public
*/
function Form()
{
    $this->setEncType("application/x-www-form-urlencoded");
    $this->obEvento = new Evento;
    $this->setMethod("post");
    $this->setName("frm");
}

/**
    * FALTA DESCRICAO
    * @access Public
    * @return String
*/
function abreForm()
{
    $stForm = "<form onSubmit = 'return false' onReset='return limpaFormulario();' ";
    if ( $this->getAction() ) {
        $stForm .= "action=\"".$this->getAction()."?".Sessao::getId()."\" ";
    }
    if ( $this->getEncType() ) {
        $stForm .= " enctype=\"".$this->getEncType()."\" ";
    }
    if ( $this->obEvento->getOnFocus() ) {
        $stForm .= "onfocus=\"JavaScript:".$this->obEvento->getOnFocus()."\" ";
    }
    if ( $this->obEvento->getOnSelect() ) {
        $stForm .= "onselect=\"JavaScript:".$this->obEvento->getOnSelect()."\" ";
    }
    if ( $this->obEvento->getOnBlur() ) {
        $stForm .= "onblur=\"JavaScript:".$this->obEvento->getOnBlur()."\" ";
    }
    if ( $this->obEvento->getOnChange() ) {
        $stForm .= "onchange=\"JavaScript:".$this->obEvento->getOnChange()."\" ";
    }
    if ( $this->obEvento->getOnClick() ) {
        $stForm .= "onclick=\"JavaScript:".$this->obEvento->getOnClick()."\" ";
    }
    if ( $this->obEvento->getOnDblClick() ) {
        $stForm .= "ondblclick=\"JavaScript:".$this->obEvento->getOnDblClick()."\" ";
    }
    if ( $this->obEvento->getOnMouseDown() ) {
        $stForm .= "onmousedown=\"JavaScript:".$this->obEvento->getOnMouseDown()."\" ";
    }
    if ( $this->obEvento->getOnMouseUp() ) {
        $stForm .= "onmouseup=\"JavaScript:".$this->obEvento->getOnMouseUp()."\" ";
    }
    if ( $this->obEvento->getOnMouseOver() ) {
        $stForm .= "onmouseover=\"JavaScript:".$this->obEvento->getOnMouseOver()."\" ";
    }
    if ( $this->obEvento->getOnMouseMove() ) {
        $stForm .= "onmousemove=\"JavaScript:".$this->obEvento->getOnMouseMove()."\" ";
    }
    if ( $this->obEvento->getOnMouseOut() ) {
        $stForm .= "onmouseout=\"JavaScript:".$this->obEvento->getOnMouseOut()."\" ";
    }
    if ( $this->obEvento->getOnKeyPress() ) {
        $stForm .= "onkeypress=\"JavaScript:".$this->obEvento->getOnKeyPress()."\" ";
    }
    if ( $this->obEvento->getOnKeyDown() ) {
        $stForm .= "onkeydown=\"JavaScript:".$this->obEvento->getOnKeyDown()."\" ";
    }
    if ( $this->obEvento->getOnKeyUp() ) {
        $stForm .= "onkeyup=\"JavaScript:".$this->obEvento->getOnKeyUp()."\" ";
    }
    if ( $this->getId() ) {
        $stForm .= "id=\"".$this->getId()."\" ";
    } else {
        $stForm .= "id=\"".$this->getName()."\" ";
    }
    if ( $this->getMethod() ) {
        $stForm .= "method=\"".$this->getMethod()."\" ";
    }
    if ( $this->getName() ) {
        $stForm .= "name=\"".$this->getName()."\" ";
    }
    if ( $this->getTarget() ) {
        $stForm .= "target=\"".$this->getTarget()."\" ";
    }
    $stForm = trim($stForm).">\n";

    return $stForm;
}
/*
* Modificado por Lucas Stephanou(domluc) en 16/02/2005
*/
/**
    * FALTA DESCRICAO
    * @access Public
    * @return String
*/
function fechaForm()
{
    $stSaida  = "</form>\n\r";
    $stSaida .= $this->defineFoco();

    return $stSaida;
}

/*
* Adicionado por Lucas Stephanou
* 16/02/2005
*/
/**
    * FALTA DESCRICAO
    * @access Public
    * @return String
*/
function defineFoco()
{
    $stSaida = '';
    if ($stId = $this->getFoco()) {
            $stSaida    = "<script type=\"text/javascript\">\r\n"                ;
            $stSaida   .= "document.getElementById('".$stId."').focus()\r\n"    ;
            $stSaida   .= "</script>\r\n"                                       ;
        }

    return $stSaida;
}

}
?>
