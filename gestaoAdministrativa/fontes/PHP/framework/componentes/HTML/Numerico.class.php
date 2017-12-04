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
* Gerar o componente tipo text que formate seu valor como data
* Data de Criação: 08/02/2004

* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
* @author Desenvolvedor: Diego Barbosa Victoria
* @author Desenvolvedor: Eduardo Martins

* @package framework
* @subpackage componentes
*
* $Id: Numerico.class.php 65311 2016-05-11 20:42:32Z michel $

Casos de uso: uc-01.01.00

*/

class Numerico extends TextBox
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
    * @access Private
    * @var Boolean
*/
var $boNegativo;

/**
    * @access Private
    * @var string
*/
var $stTipoComponente;
/**
    * @access Private
    * @var Boolean
*/
var $boFormatarNumeroBR;

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
    * @param Boolean $valor
*/
function setNegativo($valor) { $this->boNegativo = $valor; }

/**
    * @access Public
    * @param Boolean $valor
*/
function setFormatoValorBancario($valor) { $this->stTipoComponente = $valor; }

/**
    * @access Public
    * @param Boolean $valor
*/
function setFormatarNumeroBR($valor) { $this->boFormatarNumeroBR = $valor; }

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
    * @return Boolean
*/
function getNegativo() { return $this->boNegativo; }

/**
    * @access Public
    * @return string
*/
function getFormatoValorBancario() { return $this->stTipoComponente; }

/**
    * @access Public
    * @return string
*/
function getFormatarNumeroBR() { return $this->boFormatarNumeroBR; }

/**
    * Método Construtor
    * @access Public
*/
function Numerico()
{
    parent::TextBox();
    $this->flMaxValue = null;
    $this->flMinValue = null;
    $this->setName                 ( "Numerico" );
    $this->setFormatoValorBancario ( false      );
    $this->setValue                ( ""         );
    $this->setDefinicao            ( "NUMERICO" );
    $this->setFloat                ( true       );
    $this->setNegativo             ( true       );
    $this->setFormatarNumeroBR     ( false      );
}

/**
    * Monta o HTML do Objeto Label
    * @access Protected
*/
function MontaHTML()
{
    $this->setInteiro( false );
    if ($this->boNegativo) {
        $stNegativo = "true";
    } else {
        $stNegativo = "false";
    }

    // Calcula o numero de digitos necessários para o campo para comportar o valor maximo!
    $tamanhoRealMaxLength = $this->getMaxLength() + $this->getDecimais();
    $tamanhoRealSize = $this->getSize();
    $inQtdMilhar = ceil($this->getMaxLength()/3);

    $this->setMaxLength ( $tamanhoRealMaxLength );
    $this->setSize      ( $tamanhoRealSize );

    if ($this->getFormatoValorBancario() == false) {
        if($this->getFormatarNumeroBR() == true){
            $this->obEvento->setOnKeyUp  ("mascaraNumericoBR(this, ".$this->getMaxLength().", ".$this->getDecimais().", event, ".$stNegativo.");");
            $this->obEvento->setOnKeyDown("mascaraNumericoBR(this, ".$this->getMaxLength().", ".$this->getDecimais().", event, ".$stNegativo.");");

            $this->setMaxLength ( $this->getMaxLength() + $inQtdMilhar );
        }else{
            $this->obEvento->setOnKeyUp  ("mascaraNumerico(this, ".$this->getMaxLength().", ".$this->getDecimais().", event, ".$stNegativo.");");
            $this->obEvento->setOnKeyDown("mascaraNumerico(this, ".$this->getMaxLength().", ".$this->getDecimais().", event, ".$stNegativo.");");
    
            $this->setMaxLength ( ($this->getMaxLength() - 1) );
        }
    } elseif ($this->getFormatoValorBancario() == true) {
        $this->setAlign('right');
        $this->obEvento->setOnKeyUp("formataValor(this);");
        $this->obEvento->setOnBlur($this->obEvento->getOnBlur()."atualizaFormataValor(this);");
    }
    parent::montaHTML();
}

}
?>
