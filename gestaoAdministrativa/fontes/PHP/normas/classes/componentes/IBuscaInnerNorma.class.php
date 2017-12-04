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
    * Classe do componente Local
    * Data de Criação: 08/06/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package framework
    * @subpackage componentes

    Casos de uso: uc-01.04.02
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';

class IBuscaInnerNorma extends Objeto
{
/**
    * @access Private
    * @var Boolean
*/
var $inCodTipoNorma;

/**
    * @access Private
    * @var Boolean
*/
var $boNull;
/**
    * @access Public
    * @param Objeto $Valor
*/
var $obBscNorma;
/**
    * @access Public
    * @param Objeto $Valor
*/
var $boApresentaTipoNorma;
/**
    * @access Public
    * @param Objeto $Valor
*/
var $obITextBoxSelectTipoNorma;
/**
    * @access Public
    * @param Object $valor
*/
function setITextBoxSelectTipoNorma($valor) { $this->obITextBoxSelectTipoNorma     = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setTipoNorma($valor) { $this->inCodTipoNorma    = $valor; }
/**
    * @access Private
    * @var Objeto
*/

function setNorma($valor) { $this->obBscNorma  = $valor; }
/**
    * @access Private
    * @var Objeto
*/

function setApresentaTipoNorma($valor) { $this->boApresentaTipoNorma  = $valor; }
/**
    * @access Public
    * @return Objeto
*/

function setNull($valor)
{
    $this->boNull                                  = $valor;
}
/**
    * @access Public
    * @return Objeto
*/
function getTipoNorma() { return $this->inCodTipoNorma; }
/**
    * @access Public
    * @return Objeto
*/
function getNorma() { return $this->obBscNorma; }
/**
    * @access Public
    * @return Objeto
*/
function getApresentaTipoNorma() { return $this->boApresentaTipoNorma; }
/**
    * @access Public
    * @return Object
*/
function getITextBoxSelectTipoNorma() { return $this->obITextBoxSelectTipoNorma;         }
/**
    * Método construtor
    * @access Private

*/
function getNull() { return $this->boNull; }

function IBuscaInnerNorma($boApresentaTipoNorma=true,$boNull=false)
{
    $this->setNull($boNull);

    $this->setApresentaTipoNorma($boApresentaTipoNorma);
    include_once ( CAM_GA_NORMAS_COMPONENTES."ITextBoxSelectTipoNorma.class.php"                               );

    $this->setITextBoxSelectTipoNorma(new ITextBoxSelectTipoNorma($this->getNull()));

    $this->obITextBoxSelectTipoNorma->obTextBox->obEvento->setOnChange("ajaxJavaScriptSincrono( '".CAM_GA_NORMAS_PROCESSAMENTO."OCIBuscaInnerNorma.php?".Sessao::getId()."','limparNorma');");
    $this->obITextBoxSelectTipoNorma->obSelect->obEvento->setOnChange("ajaxJavaScriptSincrono( '".CAM_GA_NORMAS_PROCESSAMENTO."OCIBuscaInnerNorma.php?".Sessao::getId()."','limparNorma');");

    $this->setNorma( new BuscaInner );
    $this->obBscNorma->setRotulo                         ( "Norma"                             );
    $this->obBscNorma->setTitle                          ( "Selecione a Norma."                );
    $this->obBscNorma->setId                             ( "stNorma"                           );
    $this->obBscNorma->obCampoCod->setId                 ( "stCodNorma"                        );
    $this->obBscNorma->obCampoCod->setInteiro            ( false                               );
    $this->obBscNorma->obCampoCod->setName               ( "stCodNorma"                        );
    $this->obBscNorma->obCampoCod->setSize               ( 11                                  );
    $this->obBscNorma->obCampoCod->setMaxLength          ( 11                                  );
    $this->obBscNorma->obCampoCod->setCaracteresAceitos  ("[0-9/]");
    $this->obBscNorma->setNull                           ( $this->getNull()                    );

    if ($this->getApresentaTipoNorma()) {
        $pgOcul = "'".CAM_GA_NORMAS_PROCESSAMENTO."OCIBuscaInnerNorma.php?".Sessao::getId()."&stNorma='+this.value+'&inCodTipoNormaTxt='+document.frm.inCodTipoNormaTxt.value";
        $this->obBscNorma->obCampoCod->obEvento->setOnChange ( "ajaxJavaScript($pgOcul,'preencherNormaTipo');"   );
        include_once ( CAM_GA_NORMAS_PROCESSAMENTO."JSIBuscaInnerNorma.js");
        $this->obBscNorma->setFuncaoBusca                    ("JavaScript:abrePopUpNorma()");
    } else {
        $pgOcul = "'".CAM_GA_NORMAS_PROCESSAMENTO."OCIBuscaInnerNorma.php?".Sessao::getId()."&stNorma='+this.value";
        $this->obBscNorma->obCampoCod->obEvento->setOnChange ( "ajaxJavaScript($pgOcul,'preencherNormaTipo');"          );
        $this->obBscNorma->setFuncaoBusca                    ( "abrePopUp('".CAM_GA_NORMAS_POPUPS."normas/FLNorma.php','frm','stCodNorma','stNorma','','".Sessao::getId()."&boRetornaNumExercicio=true','800','550')" );
    }
}

/**
    * Monta os componentes
    * @access Public
    * @param  Object $obFormulario Objeto formulario
*/
function geraFormulario(&$obFormulario)
{
    if ($this->getApresentaTipoNorma()) {
       $this->obITextBoxSelectTipoNorma->setTipoNorma($this->getTipoNorma());
       $this->obITextBoxSelectTipoNorma->montaHTML();
       $obFormulario->addComponente($this->getITextBoxSelectTipoNorma());
    } else {
    // Cria um campo do tipo Hidden para receber o valor do cod_tipo_norma para ser utilizado na inclusão da norma.
    $obHdnCodTipoNorma = new Hidden;
    $obHdnCodTipoNorma->setName('hdnCodTipoNorma');
        $obHdnCodTipoNorma->setId('hdnCodTipoNorma');
        $obHdnCodTipoNorma->setValue('');
    $obHdnCodTipoNorma->montaHtml();
    $obFormulario->addHidden($obHdnCodTipoNorma);
    }
    // Cria um campo do tipo Hidden para receber o valor do cod_tipo_norma para ser utilizado na inclusão da norma.
    $obHdnCodNorma = new Hidden;
    $obHdnCodNorma->setName('hdnCodNorma');
    $obHdnCodNorma->setId('hdnCodNorma');
    $obHdnCodNorma->setValue('');
    $obHdnCodNorma->montaHtml();
    $obFormulario->addHidden($obHdnCodNorma);
    $obFormulario->addComponente($this->obBscNorma);
}

}
?>
