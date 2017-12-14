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
    * Classe do componente Padrao
    * Data de Criação: 19/06/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package framework
    * @subpackage componentes

    Casos de uso: uc-04.04.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPadrao.class.php"                                        );

class ISelectPadrao extends Objeto
{
/**
    * @access Private
    * @var Boolean
*/
var $boNull;
/**
    * @access Private
    * @var Objeto
*/
var $obTxtPadrao;
/**
    * @access Private
    * @var Objeto
*/
var $obCmbPadrao;
/**
    * @access Private
    * @var Objeto
*/
var $obRFolhaPagamentoPadrao;

/**
    * @access Public
    * @param Objeto $Valor
*/
function setPadrao($valor) { $this->obTxtPadrao  = $valor; }
/**
    * @access Public
    * @param Boolean $Valor
*/
function setNull($valor)
{
    $this->obTxtPadrao->setNull                    ( $valor  );
    $this->obCmbPadrao->setNull                    ( $valor  );
    $this->boNull                                  = $valor;
}
/**
    * @access Public
    * @param Objeto $Valor
*/
function setPadraoCombo($valor) { $this->obCmbPadrao  = $valor; }
/**
    * @access Public
    * @param Objeto $Valor
*/
function setRFolhaPagamentoPadrao($valor) { $this->obRFolhaPagamentoPadrao     = $valor; }

/**
    * @access Public
    * @return Objeto
*/
function getPadrao() { return $this->obTxtPadrao; }
/**
    * @access Public
    * @return Boolean
*/
function getNull() { return $this->boNull; }
/**
    * @access Public
    * @return Objeto
*/
function getPadraoCombo() { return $this->obCmbPadrao; }
/**
    * @access Public
    * @return Objeto
*/
function getRFolhaPagamentoPadrao() { return $this->obRFolhaPagamentoPadrao; }

/**
    * Método construtor
    * @access Private
*/
function ISelectPadrao($boNull=true)
{
    $this->setRFolhaPagamentoPadrao( new RFolhaPagamentoPadrao );
    $this->obRFolhaPagamentoPadrao->listarPadrao($rsPadrao);

    $this->setPadrao(new TextBox);
    $this->obTxtPadrao->setRotulo                  ( "Padrão"                                );
    $this->obTxtPadrao->setName                    ( "inCodPadrao"                           );
    $this->obTxtPadrao->setValue                   ( $inCodPadrao                            );
    $this->obTxtPadrao->setTitle                   ( "Selecione o padrão."                   );
    $this->obTxtPadrao->setSize                    ( 10                                      );
    $this->obTxtPadrao->setMaxLength               ( 10                                      );
    $this->obTxtPadrao->setInteiro                 ( true                                    );

    $this->setPadraoCombo(new Select);
    $this->obCmbPadrao->setName                    ( "stPadrao"                              );
    $this->obCmbPadrao->setValue                   ( $inCodPadrao                            );
    $this->obCmbPadrao->setRotulo                  ( "Padrão"                                );
    $this->obCmbPadrao->setTitle                   ( "Selecione o padrão."                   );
    $this->obCmbPadrao->setCampoId                 ( "[cod_padrao]"                          );
    $this->obCmbPadrao->setCampoDesc               ( "descricao"                             );
    $this->obCmbPadrao->addOption                  ( "", "Selecione"                         );
    $this->obCmbPadrao->setStyle                   ( "width: 250px"                          );
    $this->obCmbPadrao->preencheCombo              ( $rsPadrao                               );
    $this->obCmbPadrao->setNull                    ( $this->getNull()                        );
}

/**
    * Monta os componentes
    * @access Public
    * @param  Object $obFormulario Objeto formulario
*/
function geraFormulario(&$obFormulario)
{
    $obFormulario->addComponenteComposto($this->obTxtPadrao,$this->obCmbPadrao);
}

}
?>
