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

    Casos de uso: uc-04.04.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';

class IBuscaInnerLocal extends Objeto
{
/**
    * @access Private
    * @var Objeto
*/
var $obBscLocal;
/**
    * @access Private
    * @var Objeto
*/
var $obCmbLocal;

/**
    * @access Public
    * @param Objeto $Valor
*/
function setLocal($valor) { $this->obBscLocal  = $valor; }

/**
    * @access Public
    * @return Objeto
*/
function getLocal() { return $this->obBscLocal; }

/**
    * Método construtor
    * @access Private
*/
function IBuscaInnerLocal($stExtensao="")
{
    $pgOcul = "'".CAM_GRH_PES_PROCESSAMENTO."OCIBuscaInnerLocal.php?".Sessao::getId()."&'+this.name+'='+this.value+'&stExtensao=$stExtensao'";

    $this->setLocal( new BuscaInner );
    $this->obBscLocal->setRotulo                         ( "Local"                             );
    $this->obBscLocal->setTitle                          ( "Selecione o local."                );
    $this->obBscLocal->setId                             ( "stLocal$stExtensao"                );
    $this->obBscLocal->obCampoCod->setName               ( "inCodLocal$stExtensao"             );
    $this->obBscLocal->obCampoCod->setValue              ( $inCodLocal                         );
    $this->obBscLocal->obCampoCod->setPreencheComZeros   ( 'D'                                 );
    $this->obBscLocal->obCampoCod->setSize               ( 10                                  );
    $this->obBscLocal->obCampoCod->obEvento->setOnChange ( "ajaxJavaScript($pgOcul,'preencherLocal');");
    $this->obBscLocal->obCampoCod->obEvento->setOnBlur   ( "ajaxJavaScript($pgOcul,'preencherLocal');");
    $this->obBscLocal->setFuncaoBusca                    ( "abrePopUp('".CAM_GRH_PES_POPUPS."servidor/FLProcurarLocal.php','frm','inCodLocal$stExtensao','stLocal$stExtensao','','".Sessao::getId()."','800','550')" );
}

/**
    * Monta os componentes
    * @access Public
    * @param  Object $obFormulario Objeto formulario
*/
function geraFormulario(&$obFormulario)
{
    $obFormulario->addComponente($this->obBscLocal);
}

}
?>
