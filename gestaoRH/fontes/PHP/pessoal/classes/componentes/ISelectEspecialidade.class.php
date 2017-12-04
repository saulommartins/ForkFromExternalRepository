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
    * Classe do componente Especialidade
    * Data de Criação: 19/06/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package framework
    * @subpackage componentes

    Casos de uso: uc-04.04.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalEspecialidade.class.php"                                );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalCargo.class.php"                                        );

class ISelectEspecialidade extends Objeto
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
var $obTxtEspecialidade;
/**
    * @access Private
    * @var Objeto
*/
var $obCmbEspecialidade;
/**
    * @access Private
    * @var Objeto
*/
var $obRPessoalEspecialidade;

/**
    * @access Public
    * @param Boolean $Valor
*/
function setPreencheCombo($valor) { $this->boPreencheCombo  = $valor; }
/**
    * @access Public
    * @param Objeto $Valor
*/
function setEspecialidade($valor) { $this->obTxtEspecialidade  = $valor; }
/**
    * @access Public
    * @param Objeto $Valor
*/
function setEspecialidadeCombo($valor) { $this->obCmbEspecialidade  = $valor; }
/**
    * @access Public
    * @param Objeto $Valor
*/
function setRPessoalEspecialidade($valor) { $this->obRPessoalEspecialidade     = $valor; }

/**
    * @access Public
    * @return Boolean
*/
function getPreencheCombo() { return $this->boPreencheCombo; }
/**
    * @access Public
    * @return Objeto
*/
function getEspecialidade() { return $this->obTxtEspecialidade; }
/**
    * @access Public
    * @return Objeto
*/
function getEspecialidadeCombo() { return $this->obCmbEspecialidade; }
/**
    * @access Public
    * @return Objeto
*/
function getRPessoalEspecialidade() { return $this->obRPessoalEspecialidade; }

/**
    * Método construtor
    * @access Private
*/
function ISelectEspecialidade($boPreencheCombo=true)
{
    $this->setPreencheCombo($boPreencheCombo);

    $this->setEspecialidade(new TextBox);
    $this->obTxtEspecialidade->setRotulo                  ( "Especialidade"                         );
    $this->obTxtEspecialidade->setName                    ( "inCodEspecialidade"                    );
    $this->obTxtEspecialidade->setValue                   ( $inCodEspecialidade                     );
    $this->obTxtEspecialidade->setTitle                   ( "Selecione a especialidade."            );
    $this->obTxtEspecialidade->setSize                    ( 10                                      );
    $this->obTxtEspecialidade->setMaxLength               ( 10                                      );
    $this->obTxtEspecialidade->setInteiro                 ( true                                    );

    $this->setEspecialidadeCombo(new Select);
    $this->obCmbEspecialidade->setName                    ( "stEspecialidade"                       );
    $this->obCmbEspecialidade->setValue                   ( $inCodEspecialidade                     );
    $this->obCmbEspecialidade->setRotulo                  ( "Especialidade"                         );
    $this->obCmbEspecialidade->setTitle                   ( "Selecione a especialidade."            );
    $this->obCmbEspecialidade->setCampoId                 ( "[cod_especialidade]"                   );
    $this->obCmbEspecialidade->setCampoDesc               ( "descricao"                             );
    $this->obCmbEspecialidade->addOption                  ( "", "Selecione"                         );
    $this->obCmbEspecialidade->setStyle                   ( "width: 250px"                          );
    if ( $this->getPreencheCombo() ) {
        $this->setRPessoalEspecialidade( new RPessoalEspecialidade(new RPessoalCargo) );
        $this->obRPessoalEspecialidade->listarEspecialidadesPorCargo($rsEspecialidade);
        $this->obCmbEspecialidade->preencheCombo          ( $rsEspecialidade                        );
    }
}

/**
    * Monta os componentes
    * @access Public
    * @param  Object $obFormulario Objeto formulario
*/
function geraFormulario(&$obFormulario)
{
    $obFormulario->addComponenteComposto($this->obTxtEspecialidade,$this->obCmbEspecialidade);
}

}
?>
