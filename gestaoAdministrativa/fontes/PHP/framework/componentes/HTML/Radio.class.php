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
* Gerar o componente tipo radio de acordo com os valores setados pelo usuário.
* Data de Criação: 05/02/2003

* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00

*/

/**
    * Classe que monta uma linha de uma tabela

    * @package framework
    * @subpackage componentes
*/
class Radio extends Componente
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
    * @param Boolean $Valor
*/
function setChecked($valor) { $this->boChecked    = $valor; }

/**
    * @access Public
    * @param String $Valor
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
    * Método construtor
    * @access Public
*/
function Radio()
{
    parent::Componente();//CHAMA O METODO CONSTRUTOR DA CLASSE BASE
    $this->setChecked   ( false );
    $this->setTipo      ( "radio" );
    $this->setName      ( "radio" );
    $this->setDefinicao ( "radio" );
}

/**
    * Monta o HTML do Objeto Label
    * @access Protected
*/
function montaHtml()
{
    parent::montaHtml();
    $stHtml = $this->getHtml();
    if ( $this->getLabel() ) {
      $stHtml = "<label style='cursor:pointer'>". $stHtml;
    }
    $stHtml = substr( $stHtml, 0, strlen($stHtml) - 1 );
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
