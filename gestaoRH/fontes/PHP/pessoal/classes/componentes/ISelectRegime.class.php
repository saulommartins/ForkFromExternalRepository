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
    * Classe do componente Regime
    * Data de Criação: 19/06/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package framework
    * @subpackage componentes

    Casos de uso: uc-04.04.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalRegime.class.php"                                        );

class ISelectRegime extends Objeto
{
/**
    * @access Private
    * @var Objeto
*/
var $obTxtRegime;
/**
    * @access Private
    * @var Objeto
*/
var $obCmbRegime;
/**
    * @access Private
    * @var Objeto
*/
var $obRPessoalRegime;

/**
    * @access Public
    * @param Objeto $Valor
*/
function setRegime($valor) { $this->obTxtRegime  = $valor; }
/**
    * @access Public
    * @param Objeto $Valor
*/
function setRegimeCombo($valor) { $this->obCmbRegime  = $valor; }
/**
    * @access Public
    * @param Objeto $Valor
*/
function setRPessoalRegime($valor) { $this->obRPessoalRegime     = $valor; }

/**
    * @access Public
    * @return Objeto
*/
function getRegime() { return $this->obTxtRegime; }
/**
    * @access Public
    * @return Objeto
*/
function getRegimeCombo() { return $this->obCmbRegime; }
/**
    * @access Public
    * @return Objeto
*/
function getRPessoalRegime() { return $this->obRPessoalRegime; }

/**
    * Método construtor
    * @access Private
*/
function ISelectRegime()
{
    $this->setRPessoalRegime( new RPessoalRegime );
    $this->obRPessoalRegime->listarRegime($rsRegime);

    $this->setRegime(new TextBox);
    $this->obTxtRegime->setRotulo                  ( "Regime"                                );
    $this->obTxtRegime->setName                    ( "inCodRegime"                           );
    $this->obTxtRegime->setValue                   ( $inCodRegime                            );
    $this->obTxtRegime->setTitle                   ( "Selecione o regime."                   );
    $this->obTxtRegime->setSize                    ( 10                                      );
    $this->obTxtRegime->setMaxLength               ( 10                                      );
    $this->obTxtRegime->setInteiro                 ( true                                    );

    $this->setRegimeCombo(new Select);
    $this->obCmbRegime->setName                    ( "stRegime"                              );
    $this->obCmbRegime->setValue                   ( $inCodRegime                            );
    $this->obCmbRegime->setRotulo                  ( "Regime"                                );
    $this->obCmbRegime->setTitle                   ( "Selecione o regime."                   );
    $this->obCmbRegime->setCampoId                 ( "[cod_regime]"                          );
    $this->obCmbRegime->setCampoDesc               ( "descricao"                             );
    $this->obCmbRegime->addOption                  ( "", "Selecione"                         );
    $this->obCmbRegime->setStyle                   ( "width: 250px"                          );
    $this->obCmbRegime->preencheCombo              ( $rsRegime                               );
}

/**
    * Monta os componentes
    * @access Public
    * @param  Object $obFormulario Objeto formulario
*/
function geraFormulario(&$obFormulario)
{
    $obFormulario->addComponenteComposto($this->obTxtRegime,$this->obCmbRegime);
}

}
?>
