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
* Gerar o componente checkbox radio de acordo com os valores setados pelo usuário.
* Data de Criação: 05/02/2003

* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00

*/

/**
    * Classe de que monta o HTML do CheckBox

    * @package framework
    * @subpackage componentes
*/
class CheckBox extends Componente
{
/**
    * @access Private
    * @var Boolean
*/
var $boChecked;

/**
    * @access Private
    * @var String
*/
var $stLabel;

//SETTERS
/**
    * @access Public
    * @param Boolean $valor
*/
function setChecked($valor) { $this->boChecked    = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setLabel($valor) { $this->stLabel      = $valor; }

//GETTERS
/**
    * @access Public
    * @return Boolean
*/
function getChecked() { return $this->boChecked;    }

/**
    * @access Public
    * @return String
*/
function getLabel() { return $this->stLabel;      }

/**
    * Método Construtor
    * @access Public
*/
function checkbox()
{
    parent::Componente();//CHAMA O METODO CONSTRUTOR DA CLASSE BASE
    $this->setChecked   ( false );
    $this->setTipo      ( "checkbox" );
    $this->setName      ( "checkbox" );
    $this->setId        ( "checkbox" );
    $this->setDefinicao ( "checkbox" );
}

/**
    * Monta o HTML do Objeto CheckBox
    * @access Protected
*/
function montaHtml()
{
    parent::montaHtml();
    $stHtml = $this->getHtml();
    $stHtml = substr( $stHtml, 0, strlen($stHtml) - 1 );
    if ( $this->getLabel() ) {
      $stHtml = "<label style='cursor:pointer'>". $stHtml;
    }
    if ( $this->getChecked() ) {
        $stHtml .= "checked ";
    }
    $stHtml = $stHtml.">";
    if ( $this->getLabel() ) {
        $stHtml = $stHtml."&nbsp;".$this->getLabel();
        $stHtml .= "</label>";
    }
    $this->setHtml($stHtml);
}
}
?>
