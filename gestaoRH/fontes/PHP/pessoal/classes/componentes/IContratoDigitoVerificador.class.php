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
    * Classe do componente do contrato com o dígito verificador
    * Data de Criação: 06/06/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    $Id: IContratoDigitoVerificador.class.php 66479 2016-09-01 18:39:28Z michel $

    * @package framework
    * @subpackage componentes

    Casos de uso: uc-04.04.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once CAM_GRH_PES_NEGOCIO."RConfiguracaoPessoal.class.php";
include_once CAM_GRH_PES_NEGOCIO."RPessoalContrato.class.php";
include_once CAM_GRH_PES_COMPONENTES."ILinkConsultaServidor.class.php";

class IContratoDigitoVerificador extends Componente
{
/**
    * @access Private
    * @var Boolean
*/
var $boAutomatico;
/**
    * @access Private
    * @var Boolean
*/

var $obHdnContratoPensionista;

/**
    * @access Private
    * @var Boolean
*/
var $boPagFiltro;
/**
    * @access Private
    * @var Boolean
*/
var $boDigitoVerificador;
/**
    * @access Private
    * @var Boolean
*/
var $boHabilitaDigitoVerificador;
/**
    * @access Private
    * @var Boolean
*/
var $boMascaraRegistro;
/**
    * @access Private
    * @var Objeto
*/
var $obTxtRegistroContrato;
/**
    * @access Private
    * @var Objeto
*/
var $obLblRegistroContrato;
/**
    * @access Private
    * @var Objeto
*/
var $obHdnRegistroContrato;
/**
    * @access Private
    * @var Objeto
*/
var $obTxtDigitoVerificador;
/**
    * @access Private
    * @var Objeto
*/
var $obImagem;
/**
    * @access Private
    * @var Objeto
*/
var $obLblSeparador;
/**
    * @access Private
    * @var Objeto
*/
var $obHiddenEval;
/**
    * @access Private
    * @var Objeto
*/
var $obRConfiguracaoPessoal;
/**
    * @access Private
    * @var Objeto
*/
var $obRPessoalContrato;
/**
    * @access Private
    * @var String
*/
var $stExtender;
/**
    * @access Private
    * @var String
*/
var $stFuncaoBusca;
/**
    * @access Private
    * @var Boolean
*/
var $boLinkConsultaServidor;
/**
    * @access Private
    * @var Object
*/
var $obILinkConsultaServidor;
/**
    * @access Private
    * @var String
*/
var $stTipo;
/**
    * @access Private
    * @var String
*/
var $stSituacao;
/**
    * @access Private
    * @var Boolean
*/
var $boRescindido;
/**
    * @access Private
    * @var String
*/
var $stFuncaoBuscaFiltro;
/**
    * @access Private
    * @var Object
*/
var $obHiddenEvalContrato;

/**
    * @access Public
    * @param Boolean $valor
*/
function setAutomatico($valor) { $this->boAutomatico    = $valor; }
/**
    * @access Public
    * @param Boolean $valor
*/
function setPagFiltro($valor) { $this->boPagFiltro    = $valor; }
/**
    * @access Public
    * @param Boolean $valor
*/
function setMostraDigitoVerificador($valor) { $this->boDigitoVerificador    = $valor; }
/**
    * @access Public
    * @param Boolean $valor
*/
function setHabilitaDigitoVerificador($valor) { $this->boHabilitaDigitoVerificador    = $valor; }
/**
    * @access Public
    * @param Boolean $valor
*/
function setMascaraRegistro($valor) { $this->boMascaraRegistro    = $valor; }
/**
    * @access Public
    * @param Objeto $Valor
*/
function setRegistroContrato($valor) { $this->obTxtRegistroContrato  = $valor; }
/**
    * @access Public
    * @param Objeto $Valor
*/
function setRegistroContratoLabel($valor) { $this->obLblRegistroContrato  = $valor; }
/**
    * @access Public
    * @param Objeto $Valor
*/
function setRegistroContratoHidden($valor) { $this->obHdnRegistroContrato  = $valor; }
/**
    * @access Public
    * @param Objeto $Valor
*/
function setDigitoVerificador($valor) { $this->obTxtDigitoVerificador = $valor; }
/**
    * @access Public
    * @param Objeto $Valor
*/
function setSeparador($valor) { $this->obLblSeparador         = $valor; }
/**
    * @access Public
    * @param Objeto $Valor
*/
function setHiddenEval($valor) { $this->obHiddenEval         = $valor; }
/**
    * @access Public
    * @param Objeto $Valor
*/
function setHiddenEvalContrato($valor) { $this->obHiddenEvalContrato = $valor; }
/**
    * @access Public
    * @param Objeto $Valor
*/
function setRConfiguracaoPessoal($valor) { $this->obRConfiguracaoPessoal     = $valor; }
/**
    * @access Public
    * @param Objeto $Valor
*/
function setRPessoalContrato($valor) { $this->obRPessoalContrato     = $valor; }
/**
    * @access Public
    * @param String $Valor
*/
function setExtender($valor) { $this->stExtender     = $valor; }
/**
    * @access Public
    * @param String $Valor
*/
function setFuncaoBusca($valor) { $this->stFuncaoBusca     = $valor; }
/**
    * @access Public
    * @param Boolean $Valor
*/
function setLinkConsultaServidor($valor) { $this->boLinkConsultaServidor     = $valor; }
/**
    * @access Public
    * @param Object $Valor
*/
function setILinkConsultaServidor($valor) { $this->obILinkConsultaServidor     = $valor; }
/**
    * @access Public
    * @param String $Valor
*/
function setTipo($valor) { $this->stTipo     = $valor; }
/**
    * @access Public
    * @param String $Valor
*/
function setSituacao($valor) { $this->stSituacao     = $valor; }
/**
    * @access Public
    * @param Boolean $Valor
*/
function setRescindido($valor) { $this->boRescindido     = $valor; }
/**
    * @access Public
    * @param String $Valor
*/
function setFuncaoBuscaFiltro($valor) { $this->stFuncaoBuscaFiltro = $valor; }

/**
    * @access Public
    * @param String $Valor
*/
function setHdnContratoPensionista($valor) { $this->obHdnContratoPensionista = $valor; }

/**
    * @access Public
    * @return Boolean
*/
function getAutomatico() { return $this->boAutomatico; }
/**
    * @access Public
    * @return Boolean
*/
function getPagFiltro() { return $this->boPagFiltro; }
/**
    * @access Public
    * @return Boolean
*/
function getMostraDigitoVerificador() { return $this->boDigitoVerificador; }
/**
    * @access Public
    * @return Boolean
*/
function getHabilitaDigitoVerificador() { return $this->boHabilitaDigitoVerificador; }
/**
    * @access Public
    * @return Boolean
*/
function getMascaraRegistro() { return $this->boMascaraRegistro; }
/**
    * @access Public
    * @return Objeto
*/
function getRegistroContrato() { return $this->obTxtRegistroContrato; }
/**
    * @access Public
    * @return Objeto
*/
function getRegistroContratoLabel() { return $this->obLblRegistroContrato; }
/**
    * @access Public
    * @return Objeto
*/
function getRegistroContratoHidden() { return $this->obHdnRegistroContrato; }
/**
    * @access Public
    * @return Objeto
*/
function getDigitoVerificador() { return $this->obTxtDigitoVerificador; }
/**
    * @access Public
    * @return Objeto
*/
function getSeparador() { return $this->obLblSeparador; }
/**
    * @access Public
    * @return Objeto
*/
function getHiddenEval() { return $this->obHiddenEval; }
/**
    * @access Public
    * @return Objeto
*/
function getHiddenEvalContrato() { return $this->obHiddenEvalContrato; }
/**
    * @access Public
    * @return Objeto
*/
function getRConfiguracaoPessoal() { return $this->obRConfiguracaoPessoal; }
/**
    * @access Public
    * @return Objeto
*/
function getRPessoalContrato() { return $this->obRPessoalContrato; }
/**
    * @access Public
    * @return String
*/
function getExtender() { return $this->stExtender; }
/**
    * @access Public
    * @return String
*/
function getFuncaoBusca() { return $this->stFuncaoBusca; }
/**
    * @access Public
    * @return Boolean
*/
function getLinkConsultaServidor() { return $this->boLinkConsultaServidor; }
/**
    * @access Public
    * @return Object
*/
function getILinkConsultaServidor() { return $this->obILinkConsultaServidor; }
/**
    * @access Public
    * @return String
*/
function getTipo() { return $this->stTipo; }
/**
    * @access Public
    * @return String
*/
function getSituacao() { return $this->stSituacao; }
/**
    * @access Public
    * @return Boolean
*/
function getRescindido() { return $this->boRescindido; }
/**
    * @access Public
    * @return String
*/
function getFuncaoBuscaFiltro() { return $this->stFuncaoBuscaFiltro; }

/**
    * Método construtor
    * @access Private
*/
function getHdnContratoPensionista() { return $this->obHdnContratoPensionista; }

function IContratoDigitoVerificador($inContrato="",$stSituacao=false, $boRegistroObrigatorio=false, $boTransacao = "")
{
    parent::Componente();

    if ($stSituacao !== "todos") {
        $this->setRescindido($stSituacao);
        if ($this->getRescindido()) {
            $stSituacao = "rescindidos";
        } else {
            $stSituacao = "ativos";
        }
    }

    $this->setSituacao($stSituacao);

    $this->setRConfiguracaoPessoal( new RConfiguracaoPessoal );
    $this->obRConfiguracaoPessoal->Consultar($boTransacao);

    $stMascaraRegistro = $this->obRConfiguracaoPessoal->getMascaraRegistro();
    $arMascaraRegistro = explode("-",$stMascaraRegistro);
    $boMascaraRegistro = ( count($arMascaraRegistro) >= 2 ) ? true : false;
    $inSize            = strlen($arMascaraRegistro[0]);
    $this->setMostraDigitoVerificador   ( $boMascaraRegistro                                                            );
    $this->setHabilitaDigitoVerificador ( false                                                                         );
    $this->setAutomatico                ( ($this->obRConfiguracaoPessoal->getGeracaoRegistro() == "A") ? true : false   );

    if ( $inContrato != "" or $this->getAutomatico() ) {
        $this->setRPessoalContrato(new RPessoalContrato);
        if ($inContrato == "") {
            $this->obRPessoalContrato->proximoRegistro($boTransacao);
        } else {
            $this->obRPessoalContrato->setRegistro($inContrato);
        }
        $this->obRPessoalContrato->calculaDigito($boTransacao);

        $inContrato          = $this->obRPessoalContrato->getRegistro();
        $inDigitoVerificador = $this->obRPessoalContrato->getDigito();
    }

    $this->setRegistroContratoLabel(new Label);
    $this->obLblRegistroContrato->setRotulo                   ( "Matrícula"                                       );
    $this->obLblRegistroContrato->setName                     ( "inContrato"                                      );
    if ( $this->getMostraDigitoVerificador() ) {
        $this->obLblRegistroContrato->setValue                ( $inContrato."-".$inDigitoVerificador              );
    } else {
        $this->obLblRegistroContrato->setValue                ( $inContrato                                       );
    }
    $this->obLblRegistroContrato->setId                       ( "inContrato"                                      );

    $this->setRegistroContratoHidden(new Hidden);
    $this->obHdnRegistroContrato->setName                     ( "inContrato"                                      );
    $this->obHdnRegistroContrato->setValue                    ( $inContrato                                       );

    $this->setHdnContratoPensionista(new Hidden);
    $this->obHdnContratoPensionista->setName                  ( "inContratoPensionista"                                      );
    $this->obHdnContratoPensionista->setId                    ( "inContratoPensionista"     );                    
    
    if ($inContrato != "") {
        $this->setRPessoalContrato(new RPessoalContrato);
        $this->obRPessoalContrato->setRegistro($inContrato);
        $this->obRPessoalContrato->calculaDigito($boTransacao);
        $inDigitoVerificador = $this->obRPessoalContrato->getDigito();
    }
    $this->setName("inContrato");
    $this->setRegistroContrato( new TextBox );
    $this->obTxtRegistroContrato->setRotulo                   ( "Matrícula"                                       );
    $this->obTxtRegistroContrato->setTitle                    ( "Informe a matrícula do servidor."                );
    $this->obTxtRegistroContrato->setName                     ( "inContrato"                                      );
    $this->obTxtRegistroContrato->setId                       ( "inContrato"                                      );
    $this->obTxtRegistroContrato->setValue                    ( $inContrato                                       );
    $this->obTxtRegistroContrato->setInteiro                  ( true                                              );
    $this->obTxtRegistroContrato->setMaxLength                ( $inSize                                           );
    $this->obTxtRegistroContrato->setMinLength                ( 1                                                 );
    $this->obTxtRegistroContrato->setSize                     ( $inSize                                           );

    $this->setHiddenEvalContrato(new HiddenEval);
    if ($boRegistroObrigatorio) {
        $stHiddenEval  = "";
        $stHiddenEval .= "f = document.frm;                                                         ";
        $stHiddenEval .= "if (f.inContrato.value == '') {                                           ";
        $stHiddenEval .= "      erro = true; mensagem += '@Campo Matrícula inválido!()';            ";
        $stHiddenEval .= "};                                                                        ";
        $this->obHiddenEvalContrato->setName( "stHiddenEvalContrato" );
        $this->obHiddenEvalContrato->setValue( $stHiddenEval );
    }

    //DEFINICAO DA IMAGEM
    $this->obImagem    = new Img;
    $this->obImagem->setCaminho   ( CAM_FW_IMAGENS."botao_popup.png");
    $this->obImagem->setAlign     ( "absmiddle" );

    $this->setRotulo                                            ( "Matrícula"                                       );
    if ($boRegistroObrigatorio) {
        $this->setRotulo                                        ( "*Matrícula"                                       );
    }
    $this->setTitle                                             ( "Informe a matrícula do servidor."                );

    $this->setDigitoVerificador( new TextBox );
    $this->obTxtDigitoVerificador->setRotulo                  ( "Matrícula"                                       );
    $this->obTxtDigitoVerificador->setTitle                   ( "Informe a matrícula do servidor."                );
    $this->obTxtDigitoVerificador->setName                    ( "inDigitoVerificador"                             );
    $this->obTxtDigitoVerificador->setId                      ( "inDigitoVerificador"                             );
    $this->obTxtDigitoVerificador->setValue                   ( $inDigitoVerificador                              );
    $this->obTxtDigitoVerificador->setInteiro                 ( true                                              );
    $this->obTxtDigitoVerificador->setMaxLength               ( 1                                                 );
    $this->obTxtDigitoVerificador->setSize                    ( 1                                                 );
    //Situacao igual a false significa somente os contratos ativos
    if ($stSituacao == false) {
        $this->obTxtRegistroContrato->obEvento->setOnChange       ( "ajaxJavaScriptSincrono( '".CAM_GRH_PES_PROCESSAMENTO."OCContratoDigitoVerificador.php?".Sessao::getId()."&".$this->obTxtRegistroContrato->getName()."='+this.value+'&stAcao='+document.frm.stAcao.value, 'validaRegistroContrato2' );");
    }
   
    $stHiddenEval  = "";
    $stHiddenEval .= "f = document.frm;                                                         ";
    $stHiddenEval .= "if (f.inDigitoVerificador.value == '' && f.inContrato.value != '') {      ";
    $stHiddenEval .= "      erro = true; mensagem += '@Campo Dígito Verificador inválido!()';   ";
    $stHiddenEval .= "};                                                                        ";

    $this->setHiddenEval(new HiddenEval);
    $this->obHiddenEval->setName                              ( "stHiddenEval"                                    );
    $this->obHiddenEval->setValue                             ( $stHiddenEval                                     );
    
    $this->setILinkConsultaServidor(new ILinkConsultaServidor(""));
    $this->obILinkConsultaServidor->setRotulo("Link");
    $this->obILinkConsultaServidor->setValue("Consultar Cadastro");

    $boValidaAtivos = Sessao::read('valida_ativos_cgm');
    if ($boValidaAtivos == 'true') {
        $this->setTipo('contrato_ativos');
    }
    $this->geraFuncaoBuscaFiltro();

}

/**
    * Monta os combos de localização conforme o nível setado
    * @access Public
    * @param  Object $obFormulario Objeto formulario
*/
function geraFormulario(&$obFormulario)
{
    if ( $this->getExtender() != "" ) {
        $this->setName($this->getName().$this->getExtender());
        $this->obTxtRegistroContrato->setName( $this->obTxtRegistroContrato->getName().$this->getExtender() );
        $this->obTxtRegistroContrato->setId  ( $this->obTxtRegistroContrato->getId()  .$this->getExtender() );
        $this->obTxtDigitoVerificador->setName( $this->obTxtDigitoVerificador->getName().$this->getExtender() );
        $this->obTxtDigitoVerificador->setId  ( $this->obTxtDigitoVerificador->getId()  .$this->getExtender() );
    }

    if ( $this->getPagFiltro() ) {
        $this->obTxtRegistroContrato->setValue("");
        $this->obTxtDigitoVerificador->setValue("");
        if ( $this->getMostraDigitoVerificador() ) {
            $this->obTxtDigitoVerificador->obEvento->setOnChange    ( "ajaxJavaScriptSincrono( '".CAM_GRH_PES_PROCESSAMENTO."OCContratoDigitoVerificador.php?".Sessao::getId()."&".$this->obTxtDigitoVerificador->getName()."='+this.value+'&".$this->obTxtRegistroContrato->getName()."='+document.frm.".$this->obTxtRegistroContrato->getName().".value, 'verificaDigitoVerificador' );");
            $this->obTxtRegistroContrato->obEvento->setOnChange     ( $this->obTxtRegistroContrato->obEvento->getOnChange()."ajaxJavaScriptSincrono( '".CAM_GRH_PES_PROCESSAMENTO."OCContratoDigitoVerificador.php?".Sessao::getId()."', 'limpaDigitoVerificador' );");
            $obFormulario->addHidden          ( $this->obHiddenEval,true );
        }
        $obFormulario->addHidden( $this->obHiddenEvalContrato,true );
        $obFormulario->addHidden    ( $this->obHdnContratoPensionista, true);

        $obFormulario->addComponente($this);
    } else {
        if ( $this->getAutomatico() ) {
            $obFormulario->addComponente( $this->obLblRegistroContrato );
            $obFormulario->addHidden    ( $this->obHdnRegistroContrato );
            $obFormulario->addHidden    ( $this->obHdnContratoPensionista);

        } else {
            $this->obTxtRegistroContrato->obEvento->setOnChange       ( "ajaxJavaScriptSincrono( '".CAM_GRH_PES_PROCESSAMENTO."OCContratoDigitoVerificador.php?".Sessao::getId()."&".$this->obTxtRegistroContrato->getName()."='+this.value+'&stAcao='+document.frm.stAcao.value, 'validaRegistroContrato' );");
            if ( $this->getMostraDigitoVerificador() ) {
                $this->obTxtDigitoVerificador->setReadOnly(true);
            }
            $obFormulario->addComponente($this);
        }
    }
    if ( $this->getLinkConsultaServidor() ) {
        $obFormulario->addComponente($this->obILinkConsultaServidor);
    }

}

function geraFuncaoBuscaFiltro()
{
    $this->setFuncaoBuscaFiltro ( "abrePopUp('".CAM_GRH_PES_POPUPS."servidor/FLProcurarMatricula.php','frm','".$this->obTxtRegistroContrato->getName()."','".$this->obTxtRegistroContrato->getId()."','','".Sessao::getId()."&stSituacao=".$this->getSituacao()."&boValidaDigito=true&stTipo=".$this->getTipo()."','800','550')" );
}

/**
    * Monta o HTML do Objeto Label
    * @access Private
*/
function montaHTML()
{
    $this->geraFuncaoBuscaFiltro();

    $this->obTxtRegistroContrato->montaHTML();
    $this->obImagem->montaHTML();
    $this->obTxtDigitoVerificador->montaHTML();

    if ( $this->getPagFiltro() ) {
        $this->setFuncaoBusca($this->getFuncaoBuscaFiltro());

        $stTitleImagem = strtolower(preg_replace("/\*/","",$this->stRotulo));
        $stLink  = "&nbsp;<a href=\"JavaScript: ".$this->getFuncaoBusca().";\" title='Buscar ".$stTitleImagem."'>";
        $stLink .= $this->obImagem->getHTML();
        $stLink .= "</a>";
    }

    $obTabela = new Tabela;
    $obTabela->setCellPadding( 0 );
    $obTabela->setCellSpacing( 0 );
    $obTabela->setWidth( 100 );
    $obTabela->addLinha();
    if ( $this->obTxtRegistroContrato->getMinLength() > 0 ) {
        $obTabela->ultimaLinha->addCelula();
        $obTabela->ultimaLinha->ultimaCelula->setClass( "field" );
        $obTabela->ultimaLinha->ultimaCelula->setWidth( $this->obTxtRegistroContrato->getSize() );
        $obTabela->ultimaLinha->ultimaCelula->addConteudo( $this->obTxtRegistroContrato->getHTML() );
        $obTabela->ultimaLinha->commitCelula();
        if ( $this->getMostraDigitoVerificador() ) {
            $obTabela->ultimaLinha->addCelula();
            $obTabela->ultimaLinha->ultimaCelula->setClass( "field" );
            $obTabela->ultimaLinha->ultimaCelula->setWidth( "4" );
            $obTabela->ultimaLinha->ultimaCelula->addConteudo( $this->obTxtDigitoVerificador->getHTML() );
            $obTabela->ultimaLinha->commitCelula();
        }
    }
    $obTabela->ultimaLinha->addCelula();
    $obTabela->ultimaLinha->ultimaCelula->setClass( "fieldleft" );
    $obTabela->ultimaLinha->ultimaCelula->setValign( "top" );
    $obTabela->ultimaLinha->ultimaCelula->addConteudo( $stLink );
    $obTabela->ultimaLinha->commitCelula();

    $obTabela->commitLinha();
    $obTabela->montaHTML();
    $this->setHTML( $obTabela->getHTML() );
}

function setAposentado()
{
    $this->setTipo("aposentado");
}

function setPensionista()
{
    $this->setTipo("pensionista");
}

}
?>
