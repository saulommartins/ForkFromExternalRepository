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
* Classe responsável por conter atributos e métodos necessários à classe Lista
* Data de Criação: 02/04/2004

* @author Desenvolvedor: Diego Barbosa Victoria

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00

*/

/**
    * Classe responsável por conter atributos e métodos necessários à classe Lista
    * Data de Criação   : 02/04/2004
    * @author Diego Barbosa Victoria
*/
class DadoTextBox extends Dado
{
/**
    * @var Object TextBox
*/
var $obTextBox;

/**
    * @access Public
    * @param Object $valor Seta o objeto TextBox
*/
function setTextBox($valor) { $this->obTextBox = $valor; }
/**
    * @access Public
    * @param Object $valor Seta o objeto Label
*/
function setLabel($valor) { $this->obLabel = $valor;   }

/**
    * @access Public
    * @return String Retorna o objeto TextBox
*/
function getTextBox() { return $this->obTextBox; }
/**
    * @access Public
    * @return String Retorna o objeto Label
*/
function getLabel() { return $this->obLabel;   }

/**
    * Método Construtor
    * @access Public
    * @param Object $obComponente Componente passado por parâmetro. ex: new CPF, new CGC...
*/
function DadoTextBox($obComponente = '')
{
    parent::Dado();
    $this->setLabel  ( new Label() );
    $this->obLabel->setValue("");
    $this->setTextBox( ($obComponente) ? $obComponente : new TextBox() );
}

/**
    * Imprime o HTML do Objeto DadoTextBox na tela (echo)
    * @access Public
*/
function montaHTML()
{
    $obTextBoxClass = new TextBox();

    $this->obTextBox->setValue( $this->getConteudo() );

    if (strtolower(get_class($arObDado))=='textbox') {
        if($this->obTextBox->getSize() == $obTextBoxClass->getSize())
            $this->obTextBox->setSize ( strlen($this->getConteudo()) );

        if($this->obTextBox->getMaxLength() == $obTextBoxClass->getMaxLength())
            $this->obTextBox->setMaxLength( strlen($this->getConteudo()) );
    }
    $this->addComponente( $this->obLabel   );
    $this->addComponente( $this->obTextBox );
    parent::montaHTML();
}

}
?>
