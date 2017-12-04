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
 * Titulo do arquivo : Classe componente IRadioCadastroSituacao
 * Data de Criação   : 15/09/2008

 * @author Analista      Dagiane Vieira
 * @author Desenvolvedor Diego Lemos de Souza

 * @package URBEM
 * @subpackage

 $Id:$
 */

class IRadioCadastroSituacao extends Componente
{
/**
    * @access Private
    * @var Objeto
*/
var $obRdoAtivo;
/**
    * @access Private
    * @var Objeto
*/
var $obRdoInativo;
/**
    * @access Private
    * @var Objeto
*/
var $obRdoRescindido;
/**
    * @access Private
    * @var Objeto
*/
var $obRdoPensionista;
/**
    * @access Private
    * @var Objeto
*/
var $obRdoTodos;

/**
    * @access Public
    * @param Objeto $Valor
*/
function setRdoAtivo($valor) { $this->obRdoAtivo  = $valor; }
/**
    * @access Public
    * @param Objeto $Valor
*/
function setRdoInativo($valor) { $this->obRdoInativo  = $valor; }
/**
    * @access Public
    * @param Objeto $Valor
*/
function setRdoRescindido($valor) { $this->obRdoRescindido  = $valor; }
/**
    * @access Public
    * @param Objeto $Valor
*/
function setRdoPensionista($valor) { $this->obRdoPensionista  = $valor; }
/**
    * @access Public
    * @param Objeto $Valor
*/
function setRdoTodos($valor) { $this->obRdoTodos  = $valor; }

/**
    * @access Public
    * @return Objeto
*/
function getRdoAtivo() { return $this->obRdoAtivo; }
/**
    * @access Public
    * @return Objeto
*/
function getRdoInativo() { return $this->obRdoInativo; }
/**
    * @access Public
    * @return Objeto
*/
function getRdoRescindido() { return $this->obRdoRescindido; }
/**
    * @access Public
    * @return Objeto
*/
function getRdoPensionista() { return $this->obRdoPensionista; }
/**
    * @access Public
    * @return Objeto
*/
function getRdoTodos() { return $this->obRdoTodos; }

/**
    * Método construtor
    * @access Private
*/
function IRadioCadastroSituacao()
{
    parent::Componente();
    $this->setRotulo("Situação Servidor");
    $this->setTitle("Selecione a situação.");
    $this->setName("stSituacao");
    $this->setId("stSituacao");
    $this->setNull(false);

    $this->setRdoAtivo(new Radio);
    $this->obRdoAtivo->setRotulo($this->getRotulo());
    $this->obRdoAtivo->setName($this->getName());
    $this->obRdoAtivo->setId($this->getId());
    $this->obRdoAtivo->setTitle($this->getTitle());
    $this->obRdoAtivo->setNull($this->getNull());
    $this->obRdoAtivo->setLabel("Ativo");
    $this->obRdoAtivo->setValue("ativo");
    $this->obRdoAtivo->setChecked(true);

    $this->setRdoInativo(new Radio);
    $this->obRdoInativo->setRotulo($this->getRotulo());
    $this->obRdoInativo->setName($this->getName());
    $this->obRdoInativo->setId($this->getId());
    $this->obRdoInativo->setTitle($this->getTitle());
    $this->obRdoInativo->setNull($this->getNull());
    $this->obRdoInativo->setLabel("Inativo");
    $this->obRdoInativo->setValue("inativo");

    $this->setRdoRescindido(new Radio);
    $this->obRdoRescindido->setRotulo($this->getRotulo());
    $this->obRdoRescindido->setName($this->getName());
    $this->obRdoRescindido->setId($this->getId());
    $this->obRdoRescindido->setTitle($this->getTitle());
    $this->obRdoRescindido->setNull($this->getNull());
    $this->obRdoRescindido->setLabel("Rescindido");
    $this->obRdoRescindido->setValue("rescindido");

    $this->setRdoPensionista(new Radio);
    $this->obRdoPensionista->setRotulo($this->getRotulo());
    $this->obRdoPensionista->setName($this->getName());
    $this->obRdoPensionista->setId($this->getId());
    $this->obRdoPensionista->setTitle($this->getTitle());
    $this->obRdoPensionista->setNull($this->getNull());
    $this->obRdoPensionista->setLabel("Pensionista");
    $this->obRdoPensionista->setValue("pensionista");

    $this->setRdoTodos(new Radio);
    $this->obRdoTodos->setRotulo($this->getRotulo());
    $this->obRdoTodos->setName($this->getName());
    $this->obRdoTodos->setId($this->getId());
    $this->obRdoTodos->setTitle($this->getTitle());
    $this->obRdoTodos->setNull($this->getNull());
    $this->obRdoTodos->setLabel("Todos");
    $this->obRdoTodos->setValue("todos");

}

/**
    * Monta os componentes
    * @access Public
    * @param  Object $obFormulario Objeto formulario
*/
function geraFormulario(&$obFormulario)
{
    $arRdo = array();
    if (is_object($this->getRdoAtivo())) {
        $arRdo[] = $this->getRdoAtivo();
    }
    if (is_object($this->getRdoInativo())) {
        $arRdo[] = $this->getRdoInativo();
    }
    if (is_object($this->getRdoRescindido())) {
        $arRdo[] = $this->getRdoRescindido();
    }
    if (is_object($this->getRdoPensionista())) {
        $arRdo[] = $this->getRdoPensionista();
    }
    if (is_object($this->getRdoTodos())) {
        $arRdo[] = $this->getRdoTodos();
    }
    if (count($arRdo) >= 2) {
        $obFormulario->agrupaComponentes($arRdo);
    }
    if (count($arRdo) == 1) {
        $obFormulario->addComponente($arRdo[0]);
    }
}

}
?>
