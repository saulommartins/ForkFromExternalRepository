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
* Gerar o componente composto por dois Radio Buttons (Sim e Nao)
* Data de Criação: 23/07/2003

* @author Desenvolvedor: Diego Barbosa Victoria
* @author Desenvolvedor: Gustavo Passos Tourinho
* @author Desenvolvedor: Eduardo Martins

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00

*/

/**
    * Cria o composto por dois Radio Buttons (Sim e Nao)
    * @author Desenvolvedor: Diego Barbosa Victoria
    * @author Desenvolvedor: Gustavo Passos Tourinho
    * @author Desenvolvedor: Eduardo Martins

    * @package framework
    * @subpackage componentes
*/
class SimNao extends Componente
{
/**
    * @access Private
    * @var Object
*/
var $obRadioSim;
/**
    * @access Private
    * @var Object
*/
var $obRadioNao;
/**
    * @access Private
    * @var String
*/
var $stChecked;

/**
    * @access Public
    * @var Object
*/
function setRadioSim($valor) { $this->obRadioSim   = $valor; }
/**
    * @access Public
    * @var Object
*/
function setRadioNao($valor) { $this->obRadioNao   = $valor; }
/**
    * @access Public
    * @var String
*/
function setChecked($valor) { $this->stChecked    = $valor; }

/**
    * @access Public
    * @return Object
*/
function getRadioSim() { return $this->obRadioSim;   }
/**
    * @access Public
    * @return Object
*/
function getRadioNao() { return $this->obRadioNao;   }
/**
    * @access Public
    * @return String
*/
function getChecked() { return $this->stChecked;    }

/**
    * Método Construtor
    * @access Public
*/
function SimNao()
{
    parent::Componente();
    $this->setChecked            ( "Sim" );
    $this->setName               ( "SimNao" );
    $this->setDefinicao          ( "SimNao" );
    $this->setRadioSim           ( new Radio );
    $this->obRadioSim->setName   ("rdbRadio");
    $this->obRadioSim->setLabel  ("Sim");
    $this->obRadioSim->setValue  ("S");
    $this->obRadioSim->setChecked( true );
    $this->setRadioNao           ( new Radio );
    $this->obRadioNao->setName   ("rdbRadio");
    $this->obRadioNao->setLabel  ("Não");
    $this->obRadioNao->setValue  ("N");
    $this->obRadioNao->setChecked( false );
}

/**
    * Monta o HTML do Objeto TextBox
    * @access Protected
*/
function montaHtml()
{
    if ( strToUpper($this->getChecked()) == "SIM" || strToUpper($this->getChecked()) == "S" ) {
        $this->obRadioSim->setChecked( true );
        $this->obRadioNao->setChecked( false );
    } else {
        $this->obRadioSim->setChecked( false );
        $this->obRadioNao->setChecked( true );
    }
    $this->obRadioSim->setName( $this->getName() );
    $this->obRadioNao->setName( $this->getName() );

    $this->obRadioSim->montaHTML();
    $stHtml = $this->obRadioSim->getHTML();
    $this->obRadioNao->montaHTML();
    $stHtml .= $this->obRadioNao->getHTML();
    $this->setHtml($stHtml);
}
}

?>
