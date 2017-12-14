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
    * Classe do componente Funcao
    * Data de Criação: 06/06/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package framework
    * @subpackage componentes

    Casos de uso: uc-04.04.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalCargo.class.php"                                        );

class ISelectFuncao extends Objeto
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
var $obTxtFuncao;
/**
    * @access Private
    * @var Objeto
*/
var $obCmbFuncao;
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
function setFuncao($valor) { $this->obTxtFuncao  = $valor; }
/**
    * @access Public
    * @param Objeto $Valor
*/
function setFuncaoCombo($valor) { $this->obCmbFuncao  = $valor; }
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
function getFuncao() { return $this->obTxtFuncao; }
/**
    * @access Public
    * @return Objeto
*/
function getFuncaoCombo() { return $this->obCmbFuncao; }
/**
    * @access Public
    * @return Objeto
*/
function getRPessoalCargo() { return $this->obRPessoalCargo; }

/**
    * Método construtor
    * @access Private
*/
function ISelectFuncao($boPreencheCombo=true)
{
    $this->setPreencheCombo($boPreencheCombo);

    $this->setFuncao(new TextBox);
    $this->obTxtFuncao->setRotulo                  ( "Função"                              );
    $this->obTxtFuncao->setName                    ( "inCodFuncao"                         );
    $this->obTxtFuncao->setValue                   ( $inCodFuncao                          );
    $this->obTxtFuncao->setTitle                   ( "Selecione a função."                 );
    $this->obTxtFuncao->setSize                    ( 10                                    );
    $this->obTxtFuncao->setMaxLength               ( 10                                    );
    $this->obTxtFuncao->setInteiro                 ( true                                  );

    $this->setFuncaoCombo(new Select);
    $this->obCmbFuncao->setName                    ( "stFuncao"                            );
    $this->obCmbFuncao->setValue                   ( $inCodFuncao                          );
    $this->obCmbFuncao->setRotulo                  ( "Função"                              );
    $this->obCmbFuncao->setTitle                   ( "Selecione a função."                 );
    $this->obCmbFuncao->setCampoId                 ( "[cod_cargo]"                         );
    $this->obCmbFuncao->setCampoDesc               ( "descricao"                           );
    $this->obCmbFuncao->addOption                  ( "", "Selecione"                       );
    $this->obCmbFuncao->setStyle                   ( "width: 250px"                        );
    if ( $this->getPreencheCombo() ) {
        $this->setRPessoalCargo( new RPessoalCargo );
        $this->obRPessoalCargo->listarCargo($rsCargo);
        $this->obCmbFuncao->preencheCombo          ( $rsCargo                              );
    }
}

/**
    * Monta os componentes
    * @access Public
    * @param  Object $obFormulario Objeto formulario
*/
function geraFormulario(&$obFormulario)
{
    $obFormulario->addComponenteComposto($this->obTxtFuncao,$this->obCmbFuncao);
}

}
?>
