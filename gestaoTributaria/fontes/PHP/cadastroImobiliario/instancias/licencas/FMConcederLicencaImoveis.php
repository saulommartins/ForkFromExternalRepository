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

    * Página de Conceder Licenca
    * Data de Criação   : 17/03/2008

    * @author Analista: Fábio Bertoldi
    * @author Programador: Fernando Piccini Cercato

    $Id: FMConcederLicencaImoveis.php 59845 2014-09-15 19:32:00Z carolina $

    * Casos de uso: uc-05.01.28
*/


include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GA_CGM_COMPONENTES."IPopUpCGM.class.php" );
include_once ( CAM_GA_ADM_NEGOCIO."RCadastroDinamico.class.php" );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMTipoLicenca.class.php" );
include_once ( CAM_GT_CIM_COMPONENTES."IPopUpImovel.class.php" );
include_once ( CAM_GT_CEM_COMPONENTES."IPopUpResponsavelTecnico.class.php" );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMAtributoTipoLicenca.class.php" );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMAtributoTipoLicencaImovelValor.class.php" );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMTipoLicencaDocumento.class.php" );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMLicencaDocumento.class.php" );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMConfiguracao.class.php"     );
require_once ( CAM_GA_PROT_COMPONENTES."IPopUpProcesso.class.php" );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMLicencaBaixa.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ConcederLicenca";
$pgList = "LS".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php?".Sessao::getId();
$pgJS   = "JS".$stPrograma.".js";

include_once( $pgJS );

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "definir";
}else
if ($stAcao == "incluir") {
    $stAcao = "incluirImoveis";
}else
if ($stAcao == "alterar") {
    $stAcao = "alterarImoveis";
}
Sessao::write('stDescricao', $_REQUEST["stDescricao"]);
Sessao::write('inCodConstrucaoOutros', $_REQUEST["inCodConstrucaoOutros"]);
Sessao::write('arResponsaveis', array());
//incidencia do acrescimo
$obRdbNovaUnidadeConstrucao = new Radio;
$obRdbNovaUnidadeConstrucao->setRotulo   ( "*Nova Unidade" );
$obRdbNovaUnidadeConstrucao->setTitle    ( "Informe o tipo da nova unidade da inscriçao imobiliaria." );
$obRdbNovaUnidadeConstrucao->setName     ( "stNovaUnidade" );
$obRdbNovaUnidadeConstrucao->setLabel    ( "Construção" );
$obRdbNovaUnidadeConstrucao->setValue    ( "construcao" );
if (!$_REQUEST["inTipoNovaEdificacao"]) {
    if ( ( $_REQUEST["stAcao"] != "baixar" ) && ($_REQUEST["stAcao"] != "suspender") && ($_REQUEST["stAcao"] != "cancelar") && ($_REQUEST["stAcao"] != "cassar") ) {
        $obRdbNovaUnidadeConstrucao->setChecked( true );
        $jsOnLoad =     "ajaxJavaScript( '".$pgOcul."?".Sessao::getId()."','NovaUnidadeConstrucao' );";
        SistemaLegado::executaFrameOculto($jsOnLoad);
    }
}

$obRdbNovaUnidadeConstrucao->obEvento->setOnChange ( "ajaxJavaScript('".$pgOcul."', 'NovaUnidadeConstrucao');" );

$obRdbNovaUnidadeEdificacao = new Radio;
$obRdbNovaUnidadeEdificacao->setRotulo   ( "Nova Unidade" );
$obRdbNovaUnidadeEdificacao->setTitle    ( "Informe o tipo da nova unidade da inscriçao imobiliaria." );
$obRdbNovaUnidadeEdificacao->setName     ( "stNovaUnidade" );
$obRdbNovaUnidadeEdificacao->setLabel    ( "Edificação" );
$obRdbNovaUnidadeEdificacao->setValue    ( "edificacao" );
$obRdbNovaUnidadeEdificacao->obEvento->setOnChange ( "ajaxJavaScript('".$pgOcul."', 'NovaUnidadeEdificacao');" );
if ( $_REQUEST["inTipoNovaEdificacao"] ) {
    $obRdbNovaUnidadeEdificacao->setChecked( true );
}
$obTxtAreaLicenca = new Numerico;
$obTxtAreaLicenca->setName      ( "flAreaLicenca" );
$obTxtAreaLicenca->setRotulo    ( "Área" );
$obTxtAreaLicenca->setMaxValue  ( 999999999999.99 );
$obTxtAreaLicenca->setSize      ( 18 );
$obTxtAreaLicenca->setMaxLength ( 18 );
$obTxtAreaLicenca->setNull      ( false );
$obTxtAreaLicenca->setNegativo  ( false );
$obTxtAreaLicenca->setNaoZero   ( true );
$obTxtAreaLicenca->setTitle     ( "Informe a área relativa a licença." );
$obTxtAreaLicenca->setValue     ( str_replace( ".", ",", $_REQUEST["inAreaImovel"] ) );

//Busca dados da Configuracao
$obRCIMConfiguracao = new RCIMConfiguracao;
$obRCIMConfiguracao->setCodigoModulo( 5 );
$obRCIMConfiguracao->setAnoExercicio( Sessao::getExercicio() );
$obRCIMConfiguracao->consultarConfiguracao();
$obRCIMConfiguracao->consultarMascaraProcesso( $stMascaraProcesso );

$obTxtObservacao = new TextArea;
$obTxtObservacao->setName ( "stObservacao" );
$obTxtObservacao->setNull ( true );
$obTxtObservacao->setTitle ( "Observações sobre a licença." );
$obTxtObservacao->setRotulo ("Observações");
$obTxtObservacao->setValue ( $_REQUEST["stObservacao"] );

//Data de vigencia
$obDtValidadeInicio = new Data;
$obDtValidadeInicio->setName ( "dtValidadeInicio" );
$obDtValidadeInicio->setValue ( $_REQUEST["dtInicio"] );
$obDtValidadeInicio->setRotulo ( "Data de Início" );
$obDtValidadeInicio->setTitle ( "Data de início da validade da licença." );
$obDtValidadeInicio->setMaxLength ( 20 );
$obDtValidadeInicio->setSize ( 10 );
$obDtValidadeInicio->setNull ( false );
$obDtValidadeInicio->obEvento->setOnChange ( "validaData1500( this );" );

$obDtValidadeFim = new Data;
$obDtValidadeFim->setName ( "dtValidadeFim" );
$obDtValidadeFim->setValue ( $_REQUEST["dtTermino"] );
$obDtValidadeFim->setRotulo ( "Data de Término" );
$obDtValidadeFim->setTitle ( "Data de término da validade da licença." );
$obDtValidadeFim->setMaxLength ( 20 );
$obDtValidadeFim->setSize ( 10 );
$obDtValidadeFim->setNull ( true );
$obDtValidadeFim->obEvento->setOnChange ( "validaData1500( this );" );

$obBtnIncluirRespTec = new Button;
$obBtnIncluirRespTec->setName              ( "btnIncluirPermissao" );
$obBtnIncluirRespTec->setValue             ( "Incluir" );
$obBtnIncluirRespTec->setTipo              ( "button" );
$obBtnIncluirRespTec->obEvento->setOnClick ( "montaParametrosGET('IncluirResponsavel', 'inRespTecnico', true);" );
$obBtnIncluirRespTec->setDisabled          ( false );

$obBtnLimparRespTec = new Button;
$obBtnLimparRespTec->setName               ( "btnLimparPermissao" );
$obBtnLimparRespTec->setValue              ( "Limpar" );
$obBtnLimparRespTec->setTipo               ( "button" );
$obBtnLimparRespTec->obEvento->setOnClick  ( "ajaxJavaScript('".$pgOcul."', 'limpaResponsavel');" );
$obBtnLimparRespTec->setDisabled           ( false );

$botoesResponsavelTec = array ( $obBtnIncluirRespTec, $obBtnLimparRespTec );

$obSpnListaResponsavelTec = new Span;
$obSpnListaResponsavelTec->setID("spnListaRespTec");

$obHdnEval = new HiddenEval;
$obHdnEval->setName( "stEval" );
$obHdnEval->setValue( "" );

$obSpnAtributos = new Span;
$obSpnAtributos->setId( "lsAtributos" );

/*$obSpnDadosEdificacao = new Span;
$obSpnDadosEdificacao->setId( "spnDadosEdificacao" );*/

$obChkEmissao = new Checkbox;
$obChkEmissao->setName   ( "boEmitir"  );
$obChkEmissao->setRotulo ( "Emissão de Documentos" );
$obChkEmissao->setValue  ( "Impressão Local" );

$rsModelos = new RecordSet;
// tipo_licenca_documento fazer aqui consulta para recuperar os modelos de documentos para emissao
$obTCIMTipoLicencaDocumento = new TCIMTipoLicencaDocumento;
$stFiltro = " WHERE tipo_licenca_documento.cod_tipo = ".$_REQUEST["inTipoLicenca"];
$obTCIMTipoLicencaDocumento->retornaListadeDocumentosLicenca( $rsModelos, $stFiltro );

$obCmbModelo = new Select;
$obCmbModelo->setRotulo       ( "Modelo" );
$obCmbModelo->setTitle        ( "Modelo do Documento" );
$obCmbModelo->setName         ( "cmbModelo" );
$obCmbModelo->addOption       ( "", "Selecione" );
$obCmbModelo->setCampoId      ( "[cod_documento]-[cod_tipo_documento]" );
$obCmbModelo->setCampoDesc    ( "nome_documento" );
$obCmbModelo->preencheCombo   ( $rsModelos );
$obCmbModelo->setStyle        ( "width: 100%;" );
$obCmbModelo->setNULL         ( false );
if ($_REQUEST["stAcao"] == "alterar") {
    $stFiltro = " WHERE cod_licenca = ".$_REQUEST["inCodLicenca"]." AND exercicio = '".$_REQUEST["inExercicio"]."' ORDER BY timestamp DESC limit 1 ";
    $obTCIMLicencaDocumento = new TCIMLicencaDocumento;
    $obTCIMLicencaDocumento->recuperaTodos( $rsListaDocs, $stFiltro );
    $obCmbModelo->setValue ( $rsListaDocs->getCampo( "cod_documento" )."-".$rsListaDocs->getCampo( "cod_tipo_documento" ) );
}

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction ( $pgProc );
$obForm->settarget ( "oculto" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue ( $stCtrl );

$obHdnTipoLicenca = new Hidden;
$obHdnTipoLicenca->setName ( "inTipoLicenca" );
$obHdnTipoLicenca->setValue( $_REQUEST["inTipoLicenca"] );

$obSpnTipoEdificacao = new Span;
$obSpnTipoEdificacao->setID("spnTipoEdificacao");

//esquema para atributos dinamicos
$obRCadastroDinamico = new RCadastroDinamico;
$obRCadastroDinamico->setCodCadastro( 10 );
if ($_REQUEST["stAcao"] == "alterar") {
    $obRCadastroDinamico->setPersistenteValores( new TCIMAtributoTipoLicencaImovelValor );
    $obRCadastroDinamico->setChavePersistenteValores( array( "cod_licenca" => $_REQUEST["inCodLicenca"], "exercicio" => $_REQUEST["inExercicio"], "inscricao_municipal" => $_REQUEST["inCodInscriao"], "cod_tipo" => $_REQUEST["inTipoLicenca"] ) );
    $obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributos );
} else {
    $obRCadastroDinamico->setPersistenteAtributos( new TCIMAtributoTipoLicenca );
    $obRCadastroDinamico->setChavePersistenteValores( array( "cod_tipo" => $_REQUEST["inTipoLicenca"] ) );
    $obRCadastroDinamico->recuperaAtributosSelecionados( $rsAtributos );
}

$obMontaAtributos = new MontaAtributos;
$obMontaAtributos->setTitulo     ( "Atributos"  );
$obMontaAtributos->setName       ( "Atributo_"  );
$obMontaAtributos->setRecordSet  ( $rsAtributos );

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );

$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnTipoLicenca );
$obFormulario->addTitulo( "Dados para Licença" );

if ($_REQUEST["stAcao"] != "incluir") {
    $obLblLicenca = new Label;
    $obLblLicenca->setRotulo    ( "Licença/Exercício" );
    $obLblLicenca->setName      ( "labelLicenca");
    $obLblLicenca->setValue     ( $_REQUEST["inCodLicenca"]."/".$_REQUEST["inExercicio"] );
    $obLblLicenca->setTitle     ( "Licença/Exercício" );

    $obLblTipoLicenca = new Label;
    $obLblTipoLicenca->setRotulo    ( "Tipo de Licença" );
    $obLblTipoLicenca->setName      ( "labelTipoLicenca");
    $obLblTipoLicenca->setValue     ( $_REQUEST["inTipoLicenca"]." - ".$_REQUEST["stNomeTipo"] );
    $obLblTipoLicenca->setTitle     ( "Tipo de Licença" );

    $obLblImovel = new Label;
    $obLblImovel->setRotulo    ( "Inscrição Imobiliária" );
    $obLblImovel->setName      ( "labelInscricaoImobiliaria");
    $obLblImovel->setValue     ( $_REQUEST["inCodInscriao"] );
    $obLblImovel->setTitle     ( "Inscrição Imobiliária" );
    if (!$_REQUEST["inTipoNovaEdificacao"]) {
        $obLblDescricao = new Label;
        $obLblDescricao->setRotulo    ( "Descrição" );
        $obLblDescricao->setName      ( "labelDescricao");
        $obLblDescricao->setValue     ( $_REQUEST["stDescricao"] );
        $obLblDescricao->setTitle     ( "Descrição" );
    }

    $obHdnTipoNovaEdificacao = new Hidden;
    $obHdnTipoNovaEdificacao->setName ( "inTipoNovaEdificacao" );
    $obHdnTipoNovaEdificacao->setValue ( $_REQUEST["inTipoNovaEdificacao"] );

    $obHdnImovel = new Hidden;
    $obHdnImovel->setName ( "inCodInscriao" );
    $obHdnImovel->setValue ( $_REQUEST["inCodInscriao"] );

    $obHdnLicenca = new Hidden;
    $obHdnLicenca->setName ( "inCodLicenca" );
    $obHdnLicenca->setValue ( $_REQUEST["inCodLicenca"] );

    $obHdnLicencaExercicio = new Hidden;
    $obHdnLicencaExercicio->setName ( "inExercicio" );
    $obHdnLicencaExercicio->setValue ( $_REQUEST["inExercicio"] );

    $obFormulario->addHidden( $obHdnTipoNovaEdificacao );
    $obFormulario->addHidden( $obHdnImovel );
    $obFormulario->addHidden( $obHdnLicenca );
    $obFormulario->addHidden( $obHdnLicencaExercicio );
    $obFormulario->addComponente ( $obLblLicenca );
    $obFormulario->addComponente ( $obLblTipoLicenca );
    $obFormulario->addComponente ( $obLblImovel );
     if (!$_REQUEST["inTipoNovaEdificacao"]) {
        $obFormulario->addComponente ( $obLblDescricao );
     }
} else {
    $obIPopUpImovel = new IPopUpImovel;
    $obIPopUpImovel->obInnerImovel->setTitle ( "Inscrição imobiliária para a qual será concedida a licença." );
    $obIPopUpImovel->geraFormulario ( $obFormulario );

}

if ($_REQUEST["stAcao"] == "alterar" || $_REQUEST["stAcao"] == "incluir" ) {

        $obPopUpProcesso = new IPopUpProcesso($obForm);
        $obPopUpProcesso->setRotulo("Processo");
        $obPopUpProcesso->obCampoCod->setName ("inProcesso");
        $obPopUpProcesso->obCampoCod->setId   ("inProcesso");
        $obPopUpProcesso->obCampoCod->setValue($_REQUEST['stProcesso'] );
        $obPopUpProcesso->setValidar(true);
        $stProcesso = explode ("/",$_REQUEST['stProcesso']);
        $obPopUpProcesso->setValue($_REQUEST['stProcesso']);
 }   
    $obFormulario->addHidden( $obHdnEval, true );

if ( ( $_REQUEST["stAcao"] != "baixar" ) && ($_REQUEST["stAcao"] != "suspender") && ($_REQUEST["stAcao"] != "cancelar") && ($_REQUEST["stAcao"] != "cassar") ) {
    $obFormulario->addComponenteComposto ( $obRdbNovaUnidadeConstrucao, $obRdbNovaUnidadeEdificacao );
    $obFormulario->addSpan       ( $obSpnTipoEdificacao );
    //$obFormulario->addSpan       ( $obSpnDadosEdificacao );
    $obFormulario->addComponente ( $obTxtAreaLicenca );
    $obFormulario->addComponente ( $obPopUpProcesso );
    $obFormulario->addComponente ( $obTxtObservacao );
    $obFormulario->addSpan( $obSpnAtributos );

    $obFormulario->addTitulo( "Validade da Licença" );
    $obFormulario->addComponente ( $obDtValidadeInicio );
    $obFormulario->addComponente ( $obDtValidadeFim );

    $obFormulario->addTitulo( "Responsáveis Técnicos" );

    $obIPopUpResponsavelTecnico = new IPopUpResponsavelTecnico;
    $obIPopUpResponsavelTecnico->setProfissoes ( "1,2" );
    $obIPopUpResponsavelTecnico->geraFormulario ( $obFormulario );

    $obFormulario->defineBarra   ( $botoesResponsavelTec, 'left', '' );
    $obFormulario->addSpan       ( $obSpnListaResponsavelTec );

    $obMontaAtributos->geraFormulario  ( $obFormulario );

    $obFormulario->addTitulo( "Dados para Documento" );
    $obFormulario->addComponente ( $obChkEmissao );
    $obFormulario->addComponente ( $obCmbModelo );
} else {
    $obDtBaixa = new Data;
    $obDtBaixa->setName ( "dtBaixa" );
    
    $obLicencaBaixa = new TCIMLicencaBaixa();
    $obLicencaBaixa->setDado('cod_licenca', $_REQUEST["inCodLicenca"]);
    $obLicencaBaixa->setDado('exercicio', $_REQUEST["inExercicio"]);
    $obLicencaBaixa->recuperaPorChave($rsLicencaBaixa);

    if ($_REQUEST["stAcao"] == "baixar") {
        $obDtBaixa->setRotulo ( "Data da Baixa" );
        $obDtBaixa->setTitle ( "Data da baixa." );
        $obDtBaixa->setMaxLength ( 20 );
        $obDtBaixa->setSize ( 10 );
        $obDtBaixa->setNull ( false );
        $obDtBaixa->obEvento->setOnChange ( "validaData1500( this );" );
    }else
    if ($_REQUEST["stAcao"] == "suspender") {
        $obDtBaixa->setRotulo ( "Data de Suspensão" );
        $obDtBaixa->setTitle ( "Data de suspensão." );
        $obDtBaixa->setMaxLength ( 20 );
        $obDtBaixa->setSize ( 10 );
        $obDtBaixa->setNull ( false );
        $obDtBaixa->obEvento->setOnChange ( "validaData1500( this );" );
    }else
    if ($_REQUEST["stAcao"] == "cassar") {
        $obDtBaixa->setRotulo ( "Data de Cassação" );
        $obDtBaixa->setTitle ( "Data de Cassação." );
        $obDtBaixa->setMaxLength ( 20 );
        $obDtBaixa->setSize ( 10 );
        $obDtBaixa->setNull ( false );
        $obDtBaixa->obEvento->setOnChange ( "validaData1500( this );" );
    } else 
    if($_REQUEST["stAcao"] == "cancelar"){          
        $obDtBaixa->setRotulo ( "Data de Suspenção" );
        $obDtBaixa->setTitle ( "Data de Suspenção." );
        $obDtBaixa->setMaxLength ( 20 );
        $obDtBaixa->setSize ( 10 );
        $obDtBaixa->setNull ( false );
        $obDtBaixa->setValue($rsLicencaBaixa->getCampo("dt_inicio"));
        $obDtBaixa->setDisabled(true);     
     
    }

    $obTxtMotivo = new TextArea;
    $obTxtMotivo->setName ( "stMotivo" );
    $obTxtMotivo->setNull ( false );
    $obTxtMotivo->setTitle ( "Motivo da baixa." );
    $obTxtMotivo->setRotulo ("Motivo");
    $obTxtMotivo->setValue($rsLicencaBaixa->getCampo("motivo"));

    $obFormulario->addComponente ( $obDtBaixa );

    if ( ( $_REQUEST["stAcao"] == "suspender" ) || ( $_REQUEST["stAcao"] == "cancelar" ) ) {
        $obDtTermino = new Data;
        $obDtTermino->setName ( "dtTermino" );
        $obDtTermino->setRotulo ( "Data de Término" );
        $obDtTermino->setTitle ( "Data de término." );
        $obDtTermino->setMaxLength ( 20 );
        $obDtTermino->setSize ( 10 );
        $obDtTermino->setValue($rsLicencaBaixa->getCampo("dt_termino"));
        if ( $_REQUEST["stAcao"] == "cancelar" )
            $obDtTermino->setNull ( false );
        else
            $obDtTermino->setNull ( true );

        $obDtTermino->obEvento->setOnChange ( "validaData1500( this );" );

        $obFormulario->addComponente ( $obDtTermino );
    }

    $obFormulario->addComponente ( $obTxtMotivo );
}

if ($_REQUEST["stAcao"] == "alterar") {
    sistemaLegado::executaFrameOculto("montaParametrosGET('carregaResponsavel', 'inCodLicenca,inExercicio,inTipoNovaEdificacao', false);");
}
$obSpnListaPermissao = new Span;
$obSpnListaPermissao->setID("spnErro");
$obFormulario->addSpan       ( $obSpnListaPermissao );

if ($_REQUEST["stAcao"] == "baixar") {
    $obFormulario->cancelar();
} else {
    $obFormulario->ok();
}

$obFormulario->show();


?>
