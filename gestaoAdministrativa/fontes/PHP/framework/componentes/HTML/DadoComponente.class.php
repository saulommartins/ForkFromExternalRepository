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
* Data de Criação: 26/07/2004

* @author Desenvolvedor: Diego Barbosa Victoria

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00

*/

/**
    * Classe que oferece mecanismos a classe Lista para se criar uma EntryList, ou, Lista com entrada de dados
    * Data de Criação   : 26/07/2004

    * @author Diego Barbosa Victoria

    * @package framework
    * @subpackage componentes
*/
class DadoComponente extends Dado
{
/**
    * @var Object Componente
*/
var $obComponente;
/**
    * @var Object Componente
*/
var $boOcultaComponente;

var $stDesabilitaComponente;

function setDesabilitaComponente($boValor) { $this->stDesabilitaComponente = $boValor ; }

/**
    * @access Public
    * @param Object $valor Seta o objeto Componente
*/
function setComponenteLista($valor) { $this->obComponenteLista= $valor; }
/**
    * @access Public
    * @param Object $valor Seta o objeto Label
*/
function setLabel($valor) { $this->obLabel = $valor;   }
/**
    * @access Public
    * @param String $valor
*/
function setNameSequencial($valor) { $this->stNameSequencial = $valor;   }
/**
    * @access Public
    * @param Boolean $valor
*/
function setOcultaComponente($valor) { $this->boOcultaComponente = $valor; }

function getDesabilitaComponente() { return $this->stDesabilitaComponente; }

/**
    * @access Public
    * @return String Retorna o objeto Componente
*/
function getComponenteLista() { return $this->obComponenteLista; }
/**
    * @access Public
    * @return Object Retorna o objeto Label
*/
function getLabel() { return $this->obLabel;   }
/**
    * @access Public
    * @return String Retorna o objeto Label
*/
function getNameSequencial() { return $this->stNameSequencial;   }
/**
    *@access Public
    *@return Boolean Retorno boOcultaComponente
*/
function getOcultaComponente() { return $this->boOcultaComponente; }

/**
    * Método Construtor
    * @access Public
    * @param Object $obComponente Componente passado por parâmetro. ex: new CPF, new CGC...
*/
function DadoComponente(&$obComponente)
{
    parent::Dado();
    $this->setLabel  ( new Label() );
    $this->obLabel->setValue("");
    //$this->setComponenteLista( ($obComponente) ? $obComponente : new TextBox() );
    $this->setComponenteLista( $obComponente );
    $this->setNameSequencial  ( true );
}

/**
    * Imprime o HTML do Objeto DadoComponente na tela (echo)
    * @access Public
*/
function montaHTML()
{
    $obTextBoxClass = new TextBox();

    $this->obComponenteLista->setValue( $this->getConteudo() );

    if (strtolower(get_class($this->obComponenteLista))=='textbox') {
        if($this->obComponenteLista->getSize() == $obTextBoxClass->getSize())
            $this->obComponenteLista->setSize ( strlen($this->getConteudo()) );

        if($this->obComponenteLista->getMaxLength() == $obTextBoxClass->getMaxLength())
            $this->obComponenteLista->setMaxLength( strlen($this->getConteudo()) );
    }
    $this->addComponente( $this->obLabel   );
    $this->addComponente( $this->obComponenteLista );
    parent::montaHTML();
}

}
?>
