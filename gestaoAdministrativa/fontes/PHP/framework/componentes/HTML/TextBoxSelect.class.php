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
* Gerar o componente composto com um TextBox e um Select
* Data de Criação: 08/02/2003

* @author Desenvolvedor: Leandro André Zis

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00

*/

/**
    * Classe que monta o HTML da TexBoxSelect
    * @author Desenvolvedor: Leandro André Zis

    * @package framework
    * @subpackage componentes
*/
class TextBoxSelect extends ComponenteBase
{
var $obTextBox;

var $obSelect;

var $stHTML;

var $stMensagem;

var $stName;

var $stValue;

var $stId;

var $boLabel;

var $inCodEntidade;

var $inExercicio;

//SETTERS
function setHTML($valor) { $this->stHTML           = $valor; }

function setName($valor) { $this->stName           = $valor; }

function setMensagem($valor) { $this->stMensagem       = $valor; }

function setId($valor) { $this->stId             = $valor; }

function setValue($valor) { $this->stValue          = $valor; }

function setLabel($valor) { $this->boLabel          = $valor; }

//GETTERS
function getHTML() { return $this->stHTML;           }

function getName() { return $this->stName;           }

function getMensagem() { return $this->stMensagem;       }

function getValue() { return $this->stValue;          }

function getId() { return $this->stId;             }

function getLabel() { return $this->boLabel;          }

function TextBoxSelect()
{
    parent::ComponenteBase();
    //DEFINICAO DO CAMPO COD
    $this->obTextBox  = new TextBox;

    $this->obSelect = new Select;
    $this->setDefinicao('TEXTBOXSELECT');
}

/**
    * Monta o HTML do Objeto TextBoxSelect
    * @access Private
*/
function montaHTML()
{
    if ($this->getLabel()) {
        $this->obTextBox->setLabel(true);
        $this->obSelect->setLabel(true);
    }
    $stPreenchido = "document.getElementById('".$this->obSelect->getId()."')";
    if ( $this->getMensagem() == '' ) {
        $stOnChangeCod  = "preencheCampo( this, ".$stPreenchido.", '".Sessao::getId()."');";
    } else {
        $stOnChangeCod  = "preencheCampoMensagem( this, ".$stPreenchido.", '".Sessao::getId()."','".$this->getMensagem()."');";
    }
    $stOnChangeCod .= $this->obTextBox->obEvento->getOnChange();
    $this->obTextBox->obEvento->setOnChange( $stOnChangeCod );
    $this->obTextBox->montaHTML    ();

    $stPreenchido = "document.getElementById('".$this->obTextBox->getId()."')";
    if ( $this->getMensagem() == '' ) {
        $stOnChangeSel = "preencheCampo( this, ".$stPreenchido.", '".Sessao::getId()."');";
    } else {
        $stOnChangeSel = "preencheCampoMensagem( this, ".$stPreenchido.", '".Sessao::getId()."','".$this->getMensagem()."');";
    }
    $stOnChangeSel .= $this->obSelect->obEvento->getOnChange();
    $this->obSelect->obEvento->setOnChange( $stOnChangeSel );
    $this->obSelect->montaHTML      ();

    if ($this->getLabel()) {
        $this->setHTML( $this->obTextBox->getHTML()." - ".$this->obSelect->getHTML() );
    } else {
        $this->setHTML( $this->obTextBox->getHTML()."&nbsp;".$this->obSelect->getHTML() );
    }
}

/**
    * Imprime o HTML do Objeto TextBoxSelect na tela (echo)
    * @access Private
*/
function show()
{
    $this->montaHTML();
    $stHTML = $this->getHTML();
    $stHTML =  trim( $stHTML )."\n";
}
}
?>
