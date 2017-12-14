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
    * Classe do componente Cargo
    * Data de Criação: 19/06/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package framework
    * @subpackage componentes

    Casos de uso: uc-04.04.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalCargo.class.php"                                        );

class ISelectCargo extends Objeto
{
/**
    * @access Private
    * @var Boolean
*/
var $boPreencheCombo;
/**
    * @access Private
    * @var Objeto
*/
var $obTxtCargo;
/**
    * @access Private
    * @var Objeto
*/
var $obCmbCargo;
/**
    * @access Private
    * @var Objeto
*/
var $obRPessoalCargo;

/**
    * @access Public
    * @param Boolean $Valor
*/
function setPreencheCombo($valor) { $this->boPreencheCombo  = $valor; }
/**
    * @access Public
    * @param Objeto $Valor
*/
function setCargo($valor) { $this->obTxtCargo  = $valor; }
/**
    * @access Public
    * @param Objeto $Valor
*/
function setCargoCombo($valor) { $this->obCmbCargo  = $valor; }
/**
    * @access Public
    * @param Objeto $Valor
*/
function setRPessoalCargo($valor) { $this->obRPessoalCargo     = $valor; }

/**
    * @access Public
    * @return Boolean
*/
function getPreencheCombo() { return $this->boPreencheCombo; }
/**
    * @access Public
    * @return Objeto
*/
function getCargo() { return $this->obTxtCargo; }
/**
    * @access Public
    * @return Objeto
*/
function getCargoCombo() { return $this->obCmbCargo; }
/**
    * @access Public
    * @return Objeto
*/
function getRPessoalCargo() { return $this->obRPessoalCargo; }

/**
    * Método construtor
    * @access Private
*/
function ISelectCargo($boPreencheCombo=true)
{
    $this->setPreencheCombo($boPreencheCombo);

    $this->setCargo(new TextBox);
    $this->obTxtCargo->setRotulo                  ( "Cargo"                                 );
    $this->obTxtCargo->setName                    ( "inCodCargo"                            );
    $this->obTxtCargo->setValue                   ( $inCodCargo                             );
    $this->obTxtCargo->setTitle                   ( "Selecione o cargo."                    );
    $this->obTxtCargo->setSize                    ( 10                                      );
    $this->obTxtCargo->setMaxLength               ( 10                                      );
    $this->obTxtCargo->setInteiro                 ( true                                    );

    $this->setCargoCombo(new Select);
    $this->obCmbCargo->setName                    ( "stCargo"                               );
    $this->obCmbCargo->setValue                   ( $inCodCargo                             );
    $this->obCmbCargo->setRotulo                  ( "Cargo"                                 );
    $this->obCmbCargo->setTitle                   ( "Selecione o cargo."                    );
    $this->obCmbCargo->setCampoId                 ( "[cod_cargo]"                           );
    $this->obCmbCargo->setCampoDesc               ( "descricao"                             );
    $this->obCmbCargo->addOption                  ( "", "Selecione"                         );
    $this->obCmbCargo->setStyle                   ( "width: 250px"                          );
    if ( $this->getPreencheCombo() ) {
        $this->setRPessoalCargo( new RPessoalCargo );
        $this->obRPessoalCargo->listarCargo($rsCargo);
        $this->obCmbCargo->preencheCombo          ( $rsCargo                                );
    }
}

/**
    * Monta os componentes
    * @access Public
    * @param  Object $obFormulario Objeto formulario
*/
function geraFormulario(&$obFormulario)
{
    $obFormulario->addComponenteComposto($this->obTxtCargo,$this->obCmbCargo);
}

}
?>
