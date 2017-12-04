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
    * Gerar o componente tipo text que formate seu valor tipo INTEIRO
    * Data de Criação   : 01/08/2005

    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
    * @author Desenvolvedor: Diego Barbosa Victoria
    * @author Desenvolvedor: Lucas Leusin Oaigen

    * @package Interface
    * @subpackage Tipo

Casos de uso: uc-01.01.00

*/

/**
    * Gerar o componente tipo text que formate seu valor como data
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
    * @author Desenvolvedor: Diego Barbosa Victoria
    * @author Desenvolvedor: Lucas Leusin Oaigen

    * @package Interface
    * @subpackage Tipo
*/
class Inteiro extends TextBox
{
/**
    * @access Private
    * @var float
*/
var $inMaxValue;
/**
    * @access Private
    * @var float
*/
var $inMinValue;
/**
    * @access Private
    * @var Boolean
*/
var $boNegativo;

/**
    * @access Public
    * @param float $valor
*/
function setMaxValue($valor) { $this->inMaxValue = $valor; }
/**
    * @access Public
    * @param float $valor
*/
function setMinValue($valor) { $this->inMinValue = $valor; }
/**
    * @access Public
    * @param Boolean $valor
*/
function setNegativo($valor) { $this->boNegativo = $valor; }

/**
    * @access Public
    * @return float
*/
function getMaxValue() { return $this->inMaxValue; }
/**
    * @access Public
    * @return float
*/
function getMinValue() { return $this->inMinValue; }
/**
    * @access Public
    * @return Boolean
*/
function getNegativo() { return $this->boNegativo; }

/**
    * Método Construtor
    * @access Private
*/
function Inteiro()
{
    parent::TextBox();
    $this->inMinValue = -2147483648;
    $this->inMaxValue = 2147483647;
    $this->setName      ( "Inteiro" );
    $this->setValue     ( "" );
    $this->setDefinicao ( "INTEIRO" );
    $this->setInteiro   ( true );
    $this->setMaxLength ( $this->inMaxValue );
    $this->setMinLength ( $this->inMinValue );
}

function MontaHTML()
{
    parent::montaHTML();
}

}
?>
