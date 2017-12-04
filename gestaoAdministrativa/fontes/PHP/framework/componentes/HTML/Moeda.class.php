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
* Gerar o componente tipo text que formate seu valor como moeda
* Data de Criação: 05/02/2003

* @author Desenvolvedor: Marcelo Boezzio Paulino

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00

*/

/**
    *  Classe que gera o HTML do text de Moeda

    * @package framework
    * @subpackage componentes
*/
class Moeda extends TextBox
{
/**
    * @access Private
    * @var float
*/
var $flMaxValue;
/**
    * @access Private
    * @var float
*/
var $flMinValue;

/**
    * @access Public
    * @param float $valor
*/
function setMaxValue($valor) { $this->flMaxValue = $valor; }
/**
    * @access Public
    * @param float $valor
*/
function setMinValue($valor) { $this->flMinValue = $valor; }
/**
    * @access Public
    * @param float $valor
*/
function setNegativo($valor) { $this->boNegativo = $valor; }

/**
    * @access Public
    * @return float
*/
function getMaxValue() { return $this->flMaxValue; }
/**
    * @access Public
    * @return float
*/
function getMinValue() { return $this->flMinValue; }
/**
    * @access Public
    * @return float
*/
function getNegativo() { return $this->boNegativo; }

/**
    * Método construtor
    * @access Public
*/
function Moeda()
{
    parent::TextBox();
    $this->setName      ( "moeda" );
    $this->setMaxLength ( 18 );
    $this->setSize      ( 22 );
    $this->setDefinicao ( "moeda" );
    $this->setInteiro   ( false );
    $this->setNegativo  ( false );
}

/**
    * Monta o HTML do Objeto Moeda
    * @access Protected
*/
function montaHTML()
{
    $this->obEvento->setOnBlur("formataAoSair(this,',',".$this->getDecimais().",0,'');");
    if($this->getNegativo())
       $this->obEvento->setOnKeyUp("mascaraMoeda(this, ".$this->getDecimais().", event,".$this->getNegativo().");");
    else{
       $this->obEvento->setOnKeyUp("mascaraMoeda(this, ".$this->getDecimais().", event, false);");
    }
    parent::montaHTML();
}

}
?>
