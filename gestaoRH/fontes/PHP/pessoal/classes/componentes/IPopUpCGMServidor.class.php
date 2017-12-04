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
* Gerar o componente BuscaInner para CGM do Servidor
* Data de Criação: 09/11/2005

* @author Analista: Vandre Miguel Ramos
* @author Desenvolvedor: Andre Almeida

* @package beneficios
* @subpackage componentes

Casos de uso: uc-00.00.00

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
//include_once ( "../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/GA.inc.php" );
include_once ( CAM_GA_ORGAN_NEGOCIO."ROrganogramaLocal.class.php" );

/**
    * Cria o componente BuscaInner para CGM do Servidor
    * @author Desenvolvedor: Andre Almeida

    * @package beneficios
    * @subpackage componentes
*/
class IPopUpCGMServidor extends BuscaInner
{
/**
    * @access Private
    * @var String
    * Tipos: todos, vigente, rescindido
*/
var $stTipoContrato;

var $boPreencheCombo;

var $boRescindido;

/**
    * @access Public
    * @param String $Valor
    * Tipos: 1 - todos
    *        2 - vigente
    *        3 - rescindido
*/
function setTipoContrato($valor)
{
    $this->stTipoContrato = $valor;
    $inTipoContrato = 0;
    switch ($valor) {
        case "todos":
            $inTipoContrato = 1;
            $this->setFuncaoBusca ( "abrePopUp('".CAM_GRH_PES_POPUPS."servidor/FLProcurarCgm.php','frm','".$this->obCampoCod->getName()."','".$this->getId()."','','".Sessao::getId()."&inFiltro=".$inTipoContrato."','800','550')" );
        break;
        case "vigente":
            $inTipoContrato = 2;
            $this->setFuncaoBusca ( "abrePopUp('".CAM_GRH_PES_POPUPS."servidor/FLProcurarCgm.php','frm','".$this->obCampoCod->getName()."','".$this->getId()."','','".Sessao::getId()."&inFiltro=".$inTipoContrato."','800','550')" );
        break;
        case "rescindido":
            $inTipoContrato = 3;
            $this->setFuncaoBusca ( "abrePopUp('".CAM_GRH_PES_POPUPS."servidor/FLProcurarCgm.php','frm','".$this->obCampoCod->getName()."','".$this->getId()."','','".Sessao::getId()."&inFiltro=".$inTipoContrato."','800','550')" );
        break;
        case "pensionista":
            $inTipoContrato = 4;
            $this->setId("inCampoInnerPensionista");
            $this->obCampoCod->setName("inNumCGMPensionista");
            $this->setFuncaoBusca ( "abrePopUp('".CAM_GRH_PES_POPUPS."servidor/FLProcurarCgm.php','frm','".$this->obCampoCod->getName()."','".$this->getId()."','','".Sessao::getId()."&inFiltro=".$inTipoContrato."','800','550')" );
        break;
    }
}

function setPreencheCombo($valor)
{
    $this->boPreencheCombo = $valor;
    $this->obCampoCod->obEvento->setOnBlur( "ajaxJavaScript( '".CAM_GRH_PES_PROCESSAMENTO."OCFiltroCGM.php?".Sessao::getId()."&inNumCGM='+this.value+'&boRescindido=".$this->boRescindido."&boPreencheCombo=".$this->boPreencheCombo."&campoNum=".$this->obCampoCod->getName()."&campoNom=".$this->getId()."', 'buscaCGMContrato' );");
}

/*
  * Eu criei este set aih de baixo porque alguem criou aquele boRescindido como parametro do construtor.
  * Não dava pra ter usado o setTipoContrato? que tem uma opção rescindido?
*/
function setRescindido($valor)
{
    $this->boRescindido = $valor;
}

/**
    * @access Public
    * @return String
    * Tipos: todos, não rescindidos, rescindidos
*/
function getTipoContrato() { return $this->stTipoContrato; }

function getPreencheCombo() { return $this->boPreencheCombo; }

function getRescindido() { return $this->boRescindido; }

/**
    * Método Construtor
    * @access Public
*/
function IPopUpCGMServidor($boRescindido=false)
{
    parent::BuscaInner();

    $this->setPreencheCombo( false );
    /*
      * Eu criei este set aih de baixo porque alguem criou aquele boRescindido como paramentro do construtor.
      * Não dava pra ter usado o setTipoContrato? que tem uma opção rescindido?
    */
    $this->setRescindido( $boRescindido );

    $this->setId                ( "inCampoInner"                            );
    $this->setRotulo            ( "CGM"                                     );
    $this->setTitle             ( "Informe o CGM do servidor para o filtro" );
    $this->setNull              ( false                                     );
    $this->obCampoCod->setName  ( "inNumCGM"                                );
    $this->obCampoCod->setValue ( isset($inNumCGM) ? $inNumCGM : ""         );
    $this->setFuncaoBusca       ( "abrePopUp('".CAM_GRH_PES_POPUPS."servidor/FLProcurarCgm.php','frm','inNumCGM','inCampoInner','','".Sessao::getId()."&inFiltro=1','800','550')" );
    $this->obCampoCod->obEvento->setOnBlur( "ajaxJavaScript( '".CAM_GRH_PES_PROCESSAMENTO."OCFiltroCGM.php?".Sessao::getId()."&inNumCGM='+this.value+'&boRescindido=".$this->boRescindido."&boPreencheCombo=".$this->boPreencheCombo."', 'buscaCGMContrato' );");
}

}

?>
