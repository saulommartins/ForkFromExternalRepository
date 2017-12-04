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
  * Classe do componente de data com calendário
  * Data de Criação: 09/10/2008

  * @author Analista      Dagiane Vieira
  * @author Desenvolvedor Alex Cardoso

  * @package URBEM
  * @subpackage

  $Id: $

  * Casos de uso: uc-04.10.02

*/

/**
    * Classe de que monta o HTML do IBuscarInner por Escala

    * @package framework
    * @subpackage componentes
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once ( CAM_GRH_PON_MAPEAMENTO."TPontoEscala.class.php"                                      );

class IBuscaInnerEscala extends Objeto
{
/**
    * @access Private
    * @var Objeto
*/
var $obBscEscala;
/**
    * @access Private
    * @var Objeto
*/
var $obHdnCodEscala;
/**
    * @access Private
    * @var Objeto
*/
var $obTPontoEscala;

/**
    * @access Public
    * @param Objeto $Valor
*/
function setEscala($valor) { $this->obBscEscala = $valor; }
/**
    * @access Public
    * @param Objeto $Valor
*/
function setTPontoEscala($valor) { $this->obTPontoEscala = $valor; }

/**
    * @access Public
    * @return Objeto
*/
function getEscala() { return $this->obBscEscala; }
/**
    * @access Public
    * @return Objeto
*/
function getTPontoEscala() { return $this->obTPontoEscala; }

/**
    * Método construtor
    * @access Private
*/
function IBuscaInnerEscala($stExtensao="")
{
    $this->setTPontoEscala(new TPontoEscala());
    $this->obTPontoEscala->recuperaTodos($rsEscala, "", " ORDER BY cod_escala DESC LIMIT 1");
    $stMascEscala   = "99999";
    $inMaxLenEscala = "5";

    $pgOcul = "'".CAM_GRH_PON_PROCESSAMENTO."OCIBuscaInnerEscala.php?".Sessao::getId()."&'+this.name+'='+this.value+'&stExtensao=$stExtensao'";

    $this->setEscala( new BuscaInner );
    $this->obBscEscala->setRotulo                         ( "Código da Escala"                    );
    $this->obBscEscala->setTitle                          ( "Informe o código da escala de horário previamente cadastrada." );
    $this->obBscEscala->setId                             ( "stEscala".$stExtensao                );
    $this->obBscEscala->obCampoCod->setName               ( "inCodEscala".$stExtensao             );
    $this->obBscEscala->obCampoCod->setValue              ( $inCodEscala                          );
    $this->obBscEscala->obCampoCod->setPreencheComZeros   ( 'E'                                   );
    $this->obBscEscala->obCampoCod->setMaxLength          ( $inMaxLenEscala                       );
    $this->obBscEscala->obCampoCod->setMascara            ( $stMascEscala                         );
    $this->obBscEscala->obCampoCod->setSize               ( 15                                    );
    $this->obBscEscala->obCampoCod->obEvento->setOnChange ( "ajaxJavaScript($pgOcul,'preencherEscala');" );
    $this->obBscEscala->obCampoCod->obEvento->setOnBlur   ( "ajaxJavaScript($pgOcul,'preencherEscala');" );
    $this->obBscEscala->setFuncaoBusca                    ( "abrePopUp('".CAM_GRH_PON_POPUPS."escalaHorario/FLProcurarEscala.php','frm','inCodEscala$stExtensao','stEscala$stExtensao','','".Sessao::getId()."','800','550')" );

}

/**
    * Monta os componentes
    * @access Public
    * @param  Object $obFormulario Objeto formulario
*/
function geraFormulario(&$obFormulario)
{
    $obFormulario->addComponente($this->obBscEscala);
}

}
?>
