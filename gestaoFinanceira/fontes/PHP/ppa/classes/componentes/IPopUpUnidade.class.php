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
* Arquivo de popup de busca de Recurso
* Data de Criação: 06/10/2008

* @author Analista: Heleno Santos
* @author Desenvolvedor: Aldo Jean

* @package URBEM
* @subpackage
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once ( CLA_BUSCAINNER );

class IPopUpUnidade extends BuscaInner
{

var $obForm;
var $boNull = false;
var $inExercicio;
var $inNumUnidade;
var $InNumOrgao;
var $InAnoExercicio;
var $InCodOrgao;
var $InCodUnidade;

function setNull($valor) { $this->boNull 	  = $valor;   }
function getNull() { return $this->boNull; 	      }

/**
    * @access Public
    * @return Character
*/
function getExercicio() { return $this->inExercicio; }

/**
    * @access Public
    * @param Character $Valor
*/
function setExercicio($valor) { $this->inExercicio  = $valor; }
/**
    * @access Public
    * @param Inteter $Valor
*/
function setnumUnidade($valor) { $this->inNumUnidade  = $valor; }
/**
    * @access Public
    * @return Integer
*/
function getnumUnidade() { return $this->inNumUnidade; }

/**
    * @access Public
    * @param Inteter $Valor
*/
function setnumOrgao($valor) { $this->InNumOrgao  = $valor; }
/**
    * @access Public
    * @return Integer
*/
/**
    * @access Public
    * @return Integer
*/
function getnumOrgao() { return $this->inNumUnidade; }

/**
    * @access Public
    * @param Inteter $Valor
*/
function setanoExercicio($valor) { $this->InAnoExercicio  = $valor; }
/**
    * @access Public
    * @return Integer
*/

function getanoExercicio() { return $this->InAnoExercicio; }

/**
    * @access Public
    * @return Integer
*/

function getcodOrgao() { return $this->InCodOrgao; }
/**
    * @access Public
    * @param Inteter $Valor
*/
function setcodOrgao($valor) { $this->InCodOrgao  = $valor; }
/**
    * @access Public
    * @return Integer
*/

function getcodUnidade() { return $this->InCodUnidade; }

function IPopUpUnidade()
{
   parent::BuscaInner();

        $this->obForm = $obForm;
        $this->setObrigatorio ( true );
        $this->setRotulo('Unidade Orçamentária:');
        $this->setTitle('Informe a Unidade.');
        $this->setId('stCodUnidade');
        $this->obCampoCod->setName('inCodUnidade');
        $this->obCampoCod->setSize(10);
        $this->obCampoCod->setMaxLength(9);
        $this->obCampoCod->setAlign('left');
        $this->stTipo = 'geral';

        $this->obHdnAnoExercicio = new Hidden();
        $this->obHdnAnoExercicio->setName('inExercicioUnidadeOrc');
        $this->obHdnAnoExercicio->setId('inExercicioUnidadeOrc');

        $this->obHdnCodOrgao = new Hidden();
        $this->obHdnCodOrgao->setName('inCodOrgao');
        $this->obHdnCodOrgao->setId('inCodOrgao');
}

function setName($stName)
{
    $this->obCampoCod->setName($stName);
}

function geraFormulario(&$obFormulario)
{
        $pgOcul = "'../../../../../../gestaoFinanceira/fontes/PHP/ppa/popups/unidade/OCUnidade.php?".Sessao::getId();
        $pgOcul.= "&".$this->obCampoCod->getName()."='+this.value+'&stNomCampoCod=".$this->obCampoCod->getName();
        $pgOcul.= "&stIdCampoDesc=".$this->getId()."'";

        $this->obCampoCod->obEvento->setOnChange ( "ajaxJavaScript(".$pgOcul.",'buscaUnidade' );" );
        $this->setFuncaoBusca("abrePopUp('../../../../../../gestaoFinanceira/fontes/PHP/ppa/popups/unidade/FLUnidade.php','frm',
                              '".$this->obCampoCod->stName ."','". $this->stId . "','". $this->stTipo . "','"
                                . Sessao::getId() ."','800','550');");

        $obFormulario->addHidden($this->obHdnAnoExercicio);
        $obFormulario->addHidden($this->obHdnCodOrgao);
        $obFormulario->addComponente($this);
    }

}
?>
