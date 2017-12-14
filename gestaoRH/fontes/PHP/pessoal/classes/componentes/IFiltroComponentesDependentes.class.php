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
    * Classe interface para Filtro de Componentes para Dependentes de Pensão Judicial
    * Data de Criação: 08/03/2008

    * @author Analista: Dagiane Vieira
    * @author Desenvolvedor: Alex Cardoso

    * @package framework
    * @subpackage componentes

    $Id: IFiltroComponentesDependentes.class.php 59612 2014-09-02 12:00:51Z gelson $

    Casos de uso: uc-04.00.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';

class IFiltroComponentesDependentes extends Objeto
{
/**
   * @access Private
   * @var Object
*/
var $obTipoFiltro;
var $obSpnTipoFiltro;
var $obHdnTipoFiltro;
var $obHdnValidaMatriculas;
var $obIntValidaMatriculas;
var $boCGMDependente;
var $boCGMMatriculaServidorDependente;
var $boMatriculaDependenteDeServidor;
var $boLotacao;
var $boLocal;
var $boFiltrarPensaoJudicial;
var $stFiltroPadrao;

var $boGrupoLotacao;
var $boGrupoLocal;

function setCGMDependente() {$this->boCGMDependente=true; $this->boFiltrarPensaoJudicial=true;}
function setCGMMatriculaServidorDependente() {$this->boCGMMatriculaServidorDependente=true;}
function setMatriculaDependenteDeServidor() {$this->boMatriculaDependenteDeServidor=true;}
function setFiltrarPensaoJudicial() {$this->boFiltrarPensaoJudicial=true;}
function setFiltroPadrao($stValor) {$this->stFiltroPadrao=$stValor;}
function setLotacao() {$this->boLotacao=true;}
function setLocal() {$this->boLocal=true;}
function setGrupoLotacao()
{
    $this->boGrupoLotacao=true;
}
function setGrupoLocal()
{
    $this->boGrupoLocal=true;
}

function getCGMDependente() {return $this->boCGMDependente;}
function getCGMMatriculaServidorDependente() {return $this->boCGMMatriculaServidorDependente;}
function getMatriculaDependenteDeServidor() {return $this->boMatriculaDependenteDeServidor;}
function getFiltrarPensaoJudicial() {return $this->boFiltrarPensaoJudicial;}
function getFiltroPadrao() {return $this->stFiltroPadrao;}
function getLotacao() {return $this->boLotacao;}
function getLocal() {return $this->boLocal;}
function getGrupoLotacao() {return $this->boGrupoLotacao;}
function getGrupoLocal() {return $this->boGrupoLocal;}

/**
     * Método construtor
     * @access Private
*/
function IFiltroComponentesDependentes()
{
    $this->obCmbTipoFiltro = new Select;
    $this->obCmbTipoFiltro->setRotulo                         ( "Tipo de Filtro"                                      );
    $this->obCmbTipoFiltro->setTitle                          ( "Selecione o tipo de filtro."                         );
    $this->obCmbTipoFiltro->setName                           ( "stTipoFiltro"                                        );
    $this->obCmbTipoFiltro->setValue                          ( $stTipoFiltro                                         );
    $this->obCmbTipoFiltro->setStyle                          ( "width: 300px"                                        );
    $this->obCmbTipoFiltro->addOption                         ( "", "Selecione"                                       );
    $this->obCmbTipoFiltro->setNull                           ( false                                                 );
    //$this->obCmbTipoFiltro->obEvento->setOnChange("ajaxJavaScriptSincrono('".CAM_GRH_PES_PROCESSAMENTO."OCIFiltroComponentesDependentes.php?".Sessao::getId()."&boFiltrarPensaoJudicial=".$this->getFiltrarPensaoJudicial()."&stTipoFiltro='+this.value,'gerarSpan' );");

    $this->obSpnTipoFiltro = new Span();
    $this->obSpnTipoFiltro->setId("spnTipoFiltro");

    $this->obHdnTipoFiltro = new hiddenEval();
    $this->obHdnTipoFiltro->setName("hdnTipoFiltro");
    $this->obHdnTipoFiltro->setId("hdnTipoFiltro");

    $this->obHdnQuebrarDisabled = new hidden();
    $this->obHdnQuebrarDisabled->setName("boQuebrarDisabled");

    $this->obIntValidaMatriculas = new hidden();
    $this->obIntValidaMatriculas->setName("inValidaMatriculas");
    $this->obIntValidaMatriculas->setId("inValidaMatriculas");
    $this->obIntValidaMatriculas->setValue("0");

    $this->obHdnValidaMatriculas = new hiddenEval();
    $this->obHdnValidaMatriculas->setName("hdnValidaMatriculas");
    $this->obHdnValidaMatriculas->setId("hdnValidaMatriculas");
}

/**
    * Monta os combos de competencia
    * @access Public
    * @param  Object $obFormulario Objeto formulario
*/
function geraFormulario(&$obFormulario)
{
    $stOnChange = $this->obCmbTipoFiltro->obEvento->getOnChange();
    $this->obCmbTipoFiltro->obEvento->setOnChange("ajaxJavaScriptSincrono('".CAM_GRH_PES_PROCESSAMENTO."OCIFiltroComponentesDependentes.php?".Sessao::getId()."&boFiltrarPensaoJudicial=".$this->getFiltrarPensaoJudicial()."&stTipoFiltro='+this.value,'gerarSpan' ); ".$stOnChange);

    if ($this->getCGMDependente()) {
        $this->obCmbTipoFiltro->addOption("cgm_dependente", "CGM do Dependente da Pensão");
    }
    if ($this->getCGMMatriculaServidorDependente()) {
        $this->obCmbTipoFiltro->addOption("cgm_servidor_dependente", "CGM/Matrícula do Servidor");
    }
    if ($this->getMatriculaDependenteDeServidor()) {
        $this->obCmbTipoFiltro->addOption("matricula_dependente_servidor", "Matrícula do Dependente de Servidor");
    }
    if ($this->getLotacao()) {
        if ($this->getGrupoLotacao()) {
            $this->obCmbTipoFiltro->addOption                         ( "lotacao_grupo", "Lotação"                                  );
        } else {
            $this->obCmbTipoFiltro->addOption                         ( "lotacao", "Lotação"                                  );
        }
    }
    if ($this->getLocal()) {
        if ($this->getGrupoLocal()) {
            $this->obCmbTipoFiltro->addOption                         ( "local_grupo", "Local"                                      );
        } else {
            $this->obCmbTipoFiltro->addOption                         ( "local", "Local"                                      );
        }
    }

    $this->obCmbTipoFiltro->addOption( "geral", "Geral" );

    if ($this->getFiltroPadrao()) {
        $this->obCmbTipoFiltro->setValue($this->getFiltroPadrao());
    }

    $obFormulario->addComponente( $this->obCmbTipoFiltro );
    $obFormulario->addSpan($this->obSpnTipoFiltro);
    $obFormulario->addHidden($this->obHdnTipoFiltro,true);
    $obFormulario->addHidden($this->obHdnQuebrarDisabled);
    $obFormulario->addHidden($this->obHdnValidaMatriculas,true);
    $obFormulario->addHidden($this->obIntValidaMatriculas);
}

function getOnload(&$jsOnload)
{
    $jsOnload .= "ajaxJavaScriptSincrono('".CAM_GRH_PES_PROCESSAMENTO."OCIFiltroComponentesDependentes.php?".Sessao::getId()."&stTipoFiltro=".$this->getFiltroPadrao()."&boFiltrarPensaoJudicial=".$this->getFiltrarPensaoJudicial()."','gerarSpan' );";

    return $jsOnload;
}

}
?>
