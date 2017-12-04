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
* Data de Criação: 20/06/2006

* @author Analista: Cleisson Barboza
* @author Desenvolvedor: José Eduardo Porto

* @package URBEM
* @subpackage

$Revision: 30824 $
$Name$
$Author: cako $
$Date: 2007-12-04 12:08:26 -0200 (Ter, 04 Dez 2007) $

 Casos de uso: uc-02.01.05
*/

/*
$Log$
Revision 1.3  2006/08/29 14:50:18  fernando
Alteração do hint do componente para ficar de acordo com o padrão do framework

Revision 1.2  2006/07/05 20:41:48  cleisson
Adicionada tag Log aos arquivos

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once( CAM_GF_ORC_MAPEAMENTO."TOrcamentoConfiguracao.class.php"        );
include_once ( CLA_BUSCAINNER );

class  IPopUpRecurso extends BuscaInner
{
/**
    * @access Private
    * @var Integer
*/
var $inCodRecurso;
/**
    * @access Private
    * @var String
*/
var $stDescricaoRecurso;

/**
    * @access Public
    * @param Integer $Valor
*/
function setCodRecurso($valor) { $this->inCodRecurso  = $valor; }
/**
    * @access Public
    * @param String $Valor
*/
function setDescricaoRecurso($valor) { $this->stDescricaoRecurso  = $valor; }

/**
    * @access Public
    * @return Integer
*/
function getCodRecurso() { return $this->inCodRecurso; }
/**
    * @access Public
    * @return String
*/
function getDescricaoRecurso() { return $this->stDescricaoRecurso; }

function IPopUpRecurso()
{
    $obTOrcamentoConfiguracao = new TOrcamentoConfiguracao;
    $obTOrcamentoConfiguracao->setDado("parametro","masc_recurso");
    $obTOrcamentoConfiguracao->consultar();

    parent::BuscaInner();

    $this->setRotulo               ( "Recurso" );
    $this->setTitle                ( "Informe o recurso." );
    $this->setId                   ( "stDescricaoRecurso" );
    $this->setValue                ( $this->getDescricaoRecurso() );
    $this->obCampoCod->setName     ( "inCodRecurso" );
    $this->obCampoCod->setSize     ( 10 );
    $this->obCampoCod->setMaxLength( strlen($obTOrcamentoConfiguracao->getDado("valor")) );
    $this->obCampoCod->setValue    ( $this->getCodRecurso() );
    $this->obCampoCod->setAlign    ("left");
}

function montaHTML()
{
    if ($this->getCodRecurso() != NULL) {
        $this->obCampoCod->setValue($this->getCodRecurso());
    }

    if($this->getDescricaoRecurso())
        $this->setValue ( $this->getDescricaoRecurso());

    $pgOcul = "'".CAM_GF_ORC_PROCESSAMENTO."OCRecurso.php?".Sessao::getId()."&inCodRecurso='+this.value";
    $this->obCampoCod->obEvento->setOnChange ( "ajaxJavaScript($pgOcul,'buscaPopup');" );
    $this->setFuncaoBusca("abrePopUp('" . CAM_GF_ORC_POPUPS . "recurso/FLRecurso.php','frm', 'inCodRecurso','stDescricaoRecurso','','".Sessao::getId()."','800','550');");
    parent::montaHTML();
}
}
?>
