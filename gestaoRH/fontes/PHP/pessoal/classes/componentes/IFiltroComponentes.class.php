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
    * Classe interface para Filtro de Componentes
    * Data de Criação: 20/08/2007

    * @author Analista: Diego Lemos de Souza
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package framework
    * @subpackage componentes

    $Id: IFiltroComponentes.class.php 65809 2016-06-17 18:31:52Z michel $

    Casos de uso: uc-04.00.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php";

class IFiltroComponentes extends Objeto
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
var $obHdnLotacaoSubNivel;

var $boGeral;
var $boMatricula;
var $boCGMMatricula;
var $boAtributoServidor;

var $boCGMMatriculaPensionista;
var $boMatriculaPensionista;
var $boAtributoPensionista;

var $boCGMCodigoEstagio;
var $boAtributoEstagiario;
var $boInstituicaoEnsino;
var $boInstituicaoIntermediadora;

var $boLotacao;
var $boLocal;
var $boRegimeSubDivisao;
var $boRegimeSubDivisaoFuncao;
var $boRegSubCarEsp;
var $boRegSubFunEsp;
var $boPadrao;
var $boCargo;
var $boFuncao;
var $boPeriodo;
var $stRotuloPeriodo;

var $boGrupoLotacao;
var $boGrupoLocal;
var $boGrupoAtributoServidor;
var $boGrupoAtributoPensionista;
var $boGrupoAtributoEstagiario;
var $boGrupoRegimeSubDivisao;
var $boGrupoRegimeSubDivisaoFuncao;
var $boGrupoCargo;
var $boGrupoRegSubFunEsp;
var $boGrupoRegSubCarEsp;
var $boGrupoPadrao;

var $boDisabledQuebra;

var $boEvento;
var $stFiltroPadrao;
var $boRescisao;
var $stTipoServidor;

var $boProcessarCompetencia;
var $boEventoMultiplo;

var $boLotacaoSubNivel;

function setGeral($stValor=true) {$this->boGeral=$stValor;}
function setMatricula() {$this->boMatricula=true;}
function setCGMMatricula() {$this->boCGMMatricula=true;}
function setAtributoServidor() {$this->boAtributoServidor=true;}

function setMatriculaPensionista() {$this->boMatriculaPensionista=true;}
function setCGMMatriculaPensionista() {$this->boCGMMatriculaPensionista=true;}
function setAtributoPensionista() {$this->boAtributoPensionista=true;}

function setCGMCodigoEstagio() {$this->boCGMCodigoEstagio=true;}
function setAtributoEstagiario() {$this->boAtributoEstagiario=true;}
function setInstituicaoEnsino() {$this->boInstituicaoEnsino=true;}
function setInstituicaoIntermediadora() {$this->boInstituicaoIntermediadora=true;}

function setLotacao() {$this->boLotacao=true;}
function setLocal() {$this->boLocal=true;}
function setRegimeSubDivisao() {$this->boRegimeSubDivisao=true;}
function setRegimeSubDivisaoFuncao() {$this->boRegimeSubDivisaoFuncao=true;}
function setRegSubCarEsp() {$this->boRegSubCarEsp=true;}
function setRegSubFunEsp() {$this->boRegSubFunEsp=true;}
function setPadrao() {$this->boPadrao=true;}
function setCargo() {$this->boCargo=true;}
function setFuncao() {$this->boFuncao=true;}
function setEvento() {$this->boEvento=true;}
function setEventoMultiplo() {$this->boEventoMultiplo=true;}
function setFiltroPadrao($stValor) {$this->stFiltroPadrao=$stValor;}
function setRescisao()
{
    $this->boRescisao=true;
    $this->stTipoServidor="rescindido";
}
function setAposentados() {$this->stTipoServidor="aposentado";}
function setAtivos() {$this->stTipoServidor="ativo";}
function setTodos() {$this->stTipoServidor="todos";}
function setGrupoRegSubFunEsp() {$this->boGrupoRegSubFunEsp=true;}
function setGrupoRegSubCarEsp() {$this->boGrupoRegSubCarEsp=true;}

function setGrupoLotacao()
{
    $this->boGrupoLotacao=true;
}
function setGrupoLocal()
{
    $this->boGrupoLocal=true;
}
function setGrupoAtributoServidor()
{
    $this->boGrupoAtributoServidor=true;
}
function setGrupoAtributoPensionista()
{
    $this->boGrupoAtributoPensionista=true;
}
function setGrupoAtributoEstagiario()
{
    $this->boGrupoAtributoEstagiario=true;
}
function setGrupoRegimeSubDivisao()
{
    $this->boGrupoRegimeSubDivisao=true;
}
function setGrupoRegimeSubDivisaoFuncao()
{
    $this->boGrupoRegimeSubDivisaoFuncao=true;
}
function setGrupoCargo()
{
    $this->boGrupoCargo=true;
}
function setGrupoFuncao()
{
    $this->boGrupoFuncao=true;
}
function setGrupoPadrao()
{
    $this->boGrupoPadrao=true;
}

function setDisabledQuebra()
{
    $this->boDisabledQuebra = true;
}

function setEnabledQuebra()
{
    $this->boDisabledQuebra = false;
}

function setPeriodo()
{
    $this->boPeriodo = true;
    $this->setRotuloPeriodo("Período");
}
function setRotuloPeriodo($stRotulo)
{
    $this->stRotuloPeriodo = $stRotulo;
}

function setProcessarCompetencia()
{
    $this->boProcessarCompetencia = true;
}

function setLotacaoSubNivel($valor = false)
{
    $this->boLotacaoSubNivel = $valor;
}

function getGeral() {return $this->boGeral;}
function getMatricula() {return $this->boMatricula;}
function getCGMMatricula() {return $this->boCGMMatricula;}
function getAtributoServidor() {return $this->boAtributoServidor;}

function getMatriculaPensionista() {return $this->boMatriculaPensionista;}
function getCGMMatriculaPensionista() {return $this->boCGMMatriculaPensionista;}
function getAtributoPensionista() {return $this->boAtributoPensionista;}

function getCGMCodigoEstagio() {return $this->boCGMCodigoEstagio;}
function getAtributoEstagiario() {return $this->boAtributoEstagiario;}
function getInstituicaoEnsino() {return $this->boInstituicaoEnsino;}
function getInstituicaoIntermediadora() {return $this->boInstituicaoIntermediadora;}

function getLotacao() {return $this->boLotacao;}
function getLocal() {return $this->boLocal;}
function getRegimeSubDivisao() {return $this->boRegimeSubDivisao;}
function getRegimeSubDivisaoFuncao() {return $this->boRegimeSubDivisaoFuncao;}
function getRegSubCarEsp() {return $this->boRegSubCarEsp;}
function getRegSubFunEsp() {return $this->boRegSubFunEsp;}
function getPadrao() {return $this->boPadrao;}
function getCargo() {return $this->boCargo;}
function getFuncao() {return $this->boFuncao;}
function getEvento() {return $this->boEvento;}
function getEventoMultiplo() {return $this->boEventoMultiplo;}
function getFiltroPadrao() {return $this->stFiltroPadrao;}
function getRescisao() {return $this->boRescisao;}
function getPeriodo() {return $this->boPeriodo;}
function getRotuloPeriodo() {return $this->stRotuloPeriodo;}

function getGrupoLotacao() {return $this->boGrupoLotacao;}
function getGrupoLocal() {return $this->boGrupoLocal;}
function getGrupoAtributoServidor() {return $this->boGrupoAtributoServidor;}
function getGrupoAtributoPensionista() {return $this->boGrupoAtributoPensionista;}
function getGrupoAtributoEstagiario() {return $this->boGrupoAtributoEstagiario;}
function getGrupoRegimeSubDivisao() {return $this->boGrupoRegimeSubDivisao;}
function getGrupoRegimeSubDivisaoFuncao() {return $this->boGrupoRegimeSubDivisaoFuncao;}
function getGrupoCargo() {return $this->boGrupoCargo;}
function getGrupoFuncao() {return $this->boGrupoFuncao;}
function getDisabledQuebra() {return $this->boDisabledQuebra;}
function getGrupoRegSubFunEsp() {return $this->boGrupoRegSubFunEsp;}
function getGrupoRegSubCarEsp() {return $this->boGrupoRegSubCarEsp;}
function getGrupoPadrao() {return $this->boGrupoPadrao;}

function getTipoServidor() {return $this->stTipoServidor;}

function getProcessarCompetencia() {return $this->boProcessarCompetencia;}
function getLotacaoSubNivel() {return $this->boLotacaoSubNivel;}

/**
     * Método construtor
     * @access Private
*/
function IFiltroComponentes()
{
    $this->setGeral();

    $this->obCmbTipoFiltro = new Select;
    $this->obCmbTipoFiltro->setRotulo ( "Tipo de Filtro"                          );
    $this->obCmbTipoFiltro->setTitle  ( "Selecione o tipo de filtro."             );
    $this->obCmbTipoFiltro->setName   ( "stTipoFiltro"                            );
    $this->obCmbTipoFiltro->setId     ( "stTipoFiltro"                            );
    $this->obCmbTipoFiltro->setValue  ( isset($stTipoFiltro) ? $stTipoFiltro : "" );
    $this->obCmbTipoFiltro->setStyle  ( "width: 200px"                            );
    $this->obCmbTipoFiltro->addOption ( "", "Selecione"                           );
    $this->obCmbTipoFiltro->setNull   ( false                                     );

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

    $stOnChangeSubNivel = "";
    if ( $this->getLotacaoSubNivel() )
        $stOnChangeSubNivel = "+'&boHdnLotacaoSubNivel='+document.frm.boHdnLotacaoSubNivel.value";

    if ($this->getProcessarCompetencia())
        $this->obCmbTipoFiltro->obEvento->setOnChange("ajaxJavaScript('".CAM_GRH_PES_PROCESSAMENTO."OCIFiltroComponentes.php?".Sessao::getId()."&stTipoFiltro='+this.value+'&boQuebrarDisabled='+document.frm.boQuebrarDisabled.value+'&inCodMes='+document.frm.inCodMes.value+'&inAno='+document.frm.inAno.value,'gerarSpan' ); ".$stOnChange);
    else
        $this->obCmbTipoFiltro->obEvento->setOnChange("ajaxJavaScript('".CAM_GRH_PES_PROCESSAMENTO."OCIFiltroComponentes.php?".Sessao::getId()."&stTipoFiltro='+this.value+'&boQuebrarDisabled='+document.frm.boQuebrarDisabled.value".$stOnChangeSubNivel.",'gerarSpan' ); ".$stOnChange);

    if ($this->getMatricula()) {
        switch ($this->getTipoServidor()) {
            case "todos":
                $this->obCmbTipoFiltro->addOption( "contrato_todos", "Matrícula" );
            break;
            case "rescindido":
                $this->obCmbTipoFiltro->addOption( "contrato_rescisao", "Matrícula" );
            break;
            case "aposentado":
                $this->obCmbTipoFiltro->addOption( "contrato_aposentado", "Matrícula" );
            break;
            default:
                $this->obCmbTipoFiltro->addOption( "contrato", "Matrícula" );
            break;
        }
    }
    if ($this->getMatriculaPensionista())
        $this->obCmbTipoFiltro->addOption( "contrato_pensionista", "Matrícula" );

    if ($this->getCGMMatricula()) {
        switch ($this->getTipoServidor()) {
            case "todos":
                $this->obCmbTipoFiltro->addOption( "cgm_contrato_todos", "CGM/Matrícula" );
            break;
            case "rescindido":
                $this->obCmbTipoFiltro->addOption( "cgm_contrato_rescisao", "CGM/Matrícula" );
            break;
            case "aposentado":
                $this->obCmbTipoFiltro->addOption( "cgm_contrato_aposentado", "CGM/Matrícula" );
            break;
            default:
                $this->obCmbTipoFiltro->addOption( "cgm_contrato", "CGM/Matrícula" );
            break;
        }
    }
    if ($this->getCGMMatriculaPensionista())
        $this->obCmbTipoFiltro->addOption( "cgm_contrato_pensionista", "CGM/Matrícula" );

    //#################
    //INÍCIO ESTAGIÁRIO
    //#################
    if ($this->getCGMCodigoEstagio()) 
        $this->obCmbTipoFiltro->addOption("cgm_codigo_estagio","Código do Estágio");

    if ($this->getInstituicaoEnsino())
        $this->obCmbTipoFiltro->addOption("instituicao_ensino","Instituição de Ensino");

    if ($this->getInstituicaoIntermediadora())
        $this->obCmbTipoFiltro->addOption("entidade_intermediadora","Entidade Intermediadora");

    if ($this->getAtributoEstagiario()) {
        if ($this->getGrupoAtributoServidor())
            $this->obCmbTipoFiltro->addOption("atributo_estagiario_grupo","Atributo Dinâmico Estagiário");
        else
            $this->obCmbTipoFiltro->addOption("atributo_estagiario","Atributo Dinâmico Estagiário");
    }
    //#################
    //FIM ESTAGIÁRIO
    //#################

    if ($this->getLotacao()) {
        if ($this->getGrupoLotacao())
            $this->obCmbTipoFiltro->addOption( "lotacao_grupo", "Lotação" );
        else
            $this->obCmbTipoFiltro->addOption( "lotacao", "Lotação" );
    }
    if ($this->getLocal()) {
        if ($this->getGrupoLocal())
            $this->obCmbTipoFiltro->addOption( "local_grupo", "Local" );
        else
            $this->obCmbTipoFiltro->addOption( "local", "Local" );
    }
    if ($this->getRegimeSubDivisao()) {
        if ($this->getGrupoRegimeSubDivisao())
            $this->obCmbTipoFiltro->addOption( "sub_divisao_grupo", "Regime/SubDivisão" );
        else
            $this->obCmbTipoFiltro->addOption( "sub_divisao", "Regime/SubDivisão" );
    }
    if ($this->getRegimeSubDivisaoFuncao()) {
        if ($this->getGrupoRegimeSubDivisaoFuncao())
            $this->obCmbTipoFiltro->addOption( "sub_divisao_funcao_grupo", "Regime/SubDivisão Função" );
        else
            $this->obCmbTipoFiltro->addOption( "sub_divisao_funcao", "Regime/SubDivisão Função" );
    }
    if ($this->getAtributoServidor()) {
        if ($this->getGrupoAtributoServidor())
            $this->obCmbTipoFiltro->addOption( "atributo_servidor_grupo", "Atributo Dinâmico Servidor" );
        else
            $this->obCmbTipoFiltro->addOption( "atributo_servidor", "Atributo Dinâmico Servidor" );
    }
    if ($this->getAtributoPensionista()) {
        if ($this->getGrupoAtributoPensionista())
            $this->obCmbTipoFiltro->addOption( "atributo_pensionista_grupo", "Atributo Dinâmico Pensionista" );
        else
            $this->obCmbTipoFiltro->addOption( "atributo_pensionista", "Atributo Dinâmico Pensionista" );
    }
    if ($this->getRegSubCarEsp()) {
        if ($this->getGrupoRegSubCarEsp())
            $this->obCmbTipoFiltro->addOption( "reg_sub_car_esp_grupo", "Regime/Subdivisão/Cargo/Especialidade" );
        else
            $this->obCmbTipoFiltro->addOption( "reg_sub_car_esp", "Regime/Subdivisão/Cargo/Especialidade" );
    }
    if ($this->getRegSubFunEsp()) {
        if ($this->getGrupoRegSubFunEsp())
            $this->obCmbTipoFiltro->addOption( "reg_sub_fun_esp_grupo", "Regime/Subdivisão/Função/Especialidade" );
        else
            $this->obCmbTipoFiltro->addOption( "reg_sub_fun_esp", "Regime/Subdivisão/Função/Especialidade" );
    }
    if ($this->getPadrao()) {
        if ($this->getGrupoPadrao())
            $this->obCmbTipoFiltro->addOption( "padrao_grupo", "Padrão" );
        else
            $this->obCmbTipoFiltro->addOption( "padrao", "Padrão" );
    }
    if ($this->getEvento())
        $this->obCmbTipoFiltro->addOption( "evento", "Evento" );

    if ($this->getEventoMultiplo())
        $this->obCmbTipoFiltro->addOption( "evento_multiplo", "Evento" );

    if ($this->getCargo()) {
        if ($this->getGrupoCargo())
            $this->obCmbTipoFiltro->addOption( "cargo_grupo", "Cargo" );
        else
            $this->obCmbTipoFiltro->addOption( "cargo", "Cargo" );
    }
    if ($this->getFuncao()) {
        if ($this->getGrupoFuncao())
            $this->obCmbTipoFiltro->addOption( "funcao_grupo", "Função" );
        else
            $this->obCmbTipoFiltro->addOption( "funcao", "Função" );
    }
    if ($this->getPeriodo()) {
        $this->obCmbTipoFiltro->addOption( "periodo", $this->getRotuloPeriodo() );
        Sessao::write("stRotuloPeriodoComponente",$this->getRotuloPeriodo());
    }

    if ($this->getGeral() == true)
        $this->obCmbTipoFiltro->addOption( "geral", "Geral" );

    if ($this->getFiltroPadrao())
        $this->obCmbTipoFiltro->setValue($this->getFiltroPadrao());

    if ($this->getDisabledQuebra())
        $this->obHdnQuebrarDisabled->setValue("true");
    else
        $this->obHdnQuebrarDisabled->setValue("false");

    if ( $this->getLotacaoSubNivel() ) {
        $this->obHdnLotacaoSubNivel = new Hidden();         
        $this->obHdnLotacaoSubNivel->setName("boHdnLotacaoSubNivel");
        $this->obHdnLotacaoSubNivel->setId("boHdnLotacaoSubNivel");
        $this->obHdnLotacaoSubNivel->setValue(true);
    }

    $obFormulario->addComponente( $this->obCmbTipoFiltro );
    if ($this->getLotacaoSubNivel())
        $obFormulario->addHidden($this->obHdnLotacaoSubNivel);

    $obFormulario->addSpan($this->obSpnTipoFiltro);
    $obFormulario->addHidden($this->obHdnTipoFiltro,true);
    $obFormulario->addHidden($this->obHdnQuebrarDisabled);
    $obFormulario->addHidden($this->obHdnValidaMatriculas,true);
    $obFormulario->addHidden($this->obIntValidaMatriculas);
}

function getOnload(&$jsOnload)
{
    $jsOnload .= "ajaxJavaScript('".CAM_GRH_PES_PROCESSAMENTO."OCIFiltroComponentes.php?".Sessao::getId()."&stTipoFiltro=".$this->getFiltroPadrao()."&boQuebrarDisabled='+document.frm.boQuebrarDisabled.value,'gerarSpan' );";

    return $jsOnload;
}

}
?>
