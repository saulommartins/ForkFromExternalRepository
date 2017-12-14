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
    * Classe do componente SubDivisao
    * Data de Criação: 19/06/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package framework
    * @subpackage componentes

    Casos de uso: uc-04.04.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalSubDivisao.class.php"                                        );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalRegime.class.php"                                            );

class ISelectSubDivisao extends Objeto
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
var $obTxtSubDivisao;
/**
    * @access Private
    * @var Objeto
*/
var $obCmbSubDivisao;
/**
    * @access Private
    * @var Objeto
*/
var $obRPessoalSubDivisao;

/**
    * @access Public
    * @param Boolean $Valor
*/
function setPreencheCombo($valor) { $this->boPreencheCombo  = $valor; }
/**
    * @access Public
    * @param Objeto $Valor
*/
function setSubDivisao($valor) { $this->obTxtSubDivisao  = $valor; }
/**
    * @access Public
    * @param Objeto $Valor
*/
function setSubDivisaoCombo($valor) { $this->obCmbSubDivisao  = $valor; }
/**
    * @access Public
    * @param Objeto $Valor
*/
function setRPessoalSubDivisao($valor) { $this->obRPessoalSubDivisao     = $valor; }

/**
    * @access Public
    * @return Boolean
*/
function getPreencheCombo() { return $this->boPreencheCombo; }
/**
    * @access Public
    * @return Objeto
*/
function getSubDivisao() { return $this->obTxtSubDivisao; }
/**
    * @access Public
    * @return Objeto
*/
function getSubDivisaoCombo() { return $this->obCmbSubDivisao; }
/**
    * @access Public
    * @return Objeto
*/
function getRPessoalSubDivisao() { return $this->obRPessoalSubDivisao; }

/**
    * Método construtor
    * @access Private
*/
function ISelectSubDivisao($boPreencheCombo=true)
{
    $this->setPreencheCombo($boPreencheCombo);

    $this->setSubDivisao(new TextBox);
    $this->obTxtSubDivisao->setRotulo                  ( "SubDivisao"                            );
    $this->obTxtSubDivisao->setName                    ( "inCodSubDivisao"                       );
    $this->obTxtSubDivisao->setValue                   ( $inCodSubDivisao                        );
    $this->obTxtSubDivisao->setTitle                   ( "Selecione a subdivisão."               );
    $this->obTxtSubDivisao->setSize                    ( 10                                      );
    $this->obTxtSubDivisao->setMaxLength               ( 10                                      );
    $this->obTxtSubDivisao->setInteiro                 ( true                                    );

    $this->setSubDivisaoCombo(new Select);
    $this->obCmbSubDivisao->setName                    ( "stSubDivisao"                          );
    $this->obCmbSubDivisao->setValue                   ( $inCodSubDivisao                        );
    $this->obCmbSubDivisao->setRotulo                  ( "SubDivisao"                            );
    $this->obCmbSubDivisao->setTitle                   ( "Selecione a subdivisão."               );
    $this->obCmbSubDivisao->setCampoId                 ( "[cod_sub_divisao]"                     );
    $this->obCmbSubDivisao->setCampoDesc               ( "nom_sub_divisao"                       );
    $this->obCmbSubDivisao->addOption                  ( "", "Selecione"                         );
    $this->obCmbSubDivisao->setStyle                   ( "width: 250px"                          );
    if ( $this->getPreencheCombo() ) {
        $this->setRPessoalSubDivisao( new RPessoalSubDivisao(new RPessoalRegime) );
        $this->obRPessoalSubDivisao->listarSubDivisao($rsSubDivisao);
        $this->obCmbSubDivisao->preencheCombo          ( $rsSubDivisao                           );
    }
}

/**
    * Monta os componentes
    * @access Public
    * @param  Object $obFormulario Objeto formulario
*/
function geraFormulario(&$obFormulario)
{
    $obFormulario->addComponenteComposto($this->obTxtSubDivisao,$this->obCmbSubDivisao);
}

}
?>
