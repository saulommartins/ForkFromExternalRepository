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
    * Página de Definir Caracteristicas para Tipo de Licenca
    * Data de Criação   : 04/04/2008

    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Fernando Piccini Cercato

    $Id: FMDefinirCaracteristicas.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.28
*/

/*
$Log$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMTipoLicenca.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "DefinirCaracteristicas";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php?".Sessao::getId();
$pgJS   = "JS".$stPrograma.".js";

include_once( $pgJS );

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "definir_tp";
}

Sessao::remove('link');

$obTxtTipoLicenca = new TextBox;
$obTxtTipoLicenca->setRotulo  ( 'Tipo de Licença');
$obTxtTipoLicenca->setTitle   ( 'Informe o tipo de licença para definir permissão.');
$obTxtTipoLicenca->setName    ( 'inTipoLicenca');
$obTxtTipoLicenca->setInteiro ( true );
$obTxtTipoLicenca->setNull    ( false );
$obTxtTipoLicenca->obEvento->setOnChange( "montaParametrosGET('CarregaAtributos', 'inTipoLicenca,cmbTipoLicenca', true);" );

$obTCIMTipoLicenca = new TCIMTipoLicenca;
$obTCIMTipoLicenca->recuperaTodos( $rsTipoLicenca );

$obCmbTipoLicenca = new Select;
$obCmbTipoLicenca->setRotulo       ( "*Tipo de Licença" );
$obCmbTipoLicenca->setTitle        ( "Informe o tipo de licença para definir permissão." );
$obCmbTipoLicenca->setName         ( "cmbTipoLicenca" );
$obCmbTipoLicenca->addOption       ( "", "Selecione" );
$obCmbTipoLicenca->setCampoId      ( "cod_tipo" );
$obCmbTipoLicenca->setCampoDesc    ( "nom_tipo" );
$obCmbTipoLicenca->preencheCombo   ( $rsTipoLicenca );
$obCmbTipoLicenca->setStyle        ( "width: 40%;" );
$obCmbTipoLicenca->setNULL         ( false );
$obCmbTipoLicenca->obEvento->setOnChange( "montaParametrosGET('CarregaAtributos', 'inTipoLicenca,cmbTipoLicenca', true);" );

$obCmbAtributosDinamicos = new SelectMultiplo();
$obCmbAtributosDinamicos->setName   ('inCodAtributoSelecionados');
$obCmbAtributosDinamicos->setRotulo ( "Atributos Dinâmicos" );
$obCmbAtributosDinamicos->setNull   ( true );
$obCmbAtributosDinamicos->setTitle  ( "Define quais atributos dinâmicos estarão disponíveis para cada tipo de licença." );

// lista de atributos disponiveis
$obCmbAtributosDinamicos->SetNomeLista1 ('inAtribOrdemDisponivel');
$obCmbAtributosDinamicos->setCampoId1   ('cod_atributo');
$obCmbAtributosDinamicos->setCampoDesc1 ('nom_atributo');
$obCmbAtributosDinamicos->SetRecord1    ( new RecordSet );

// lista de atributos selecionados
$obCmbAtributosDinamicos->SetNomeLista2 ('inAtribOrdemSelecionados');
$obCmbAtributosDinamicos->setCampoId2   ('cod_atributo');
$obCmbAtributosDinamicos->setCampoDesc2 ('nom_atributo');
$obCmbAtributosDinamicos->SetRecord2    ( new RecordSet );

$obCmbModeloDocumento = new SelectMultiplo();
$obCmbModeloDocumento->setName   ('inCodModDocSelecionados');
$obCmbModeloDocumento->setRotulo ( "Modelos de Documento" );
$obCmbModeloDocumento->setNull   ( false );
$obCmbModeloDocumento->setTitle  ( "Defina quais modelos de documento poderão ser emitidos para o tipo de licença selecionado." );

// lista de atributos disponiveis
$obCmbModeloDocumento->SetNomeLista1 ('inModDocDisponivel');
$obCmbModeloDocumento->setCampoId1   ('[cod_documento]-[cod_tipo_documento]');
$obCmbModeloDocumento->setCampoDesc1 ('nome_documento');
$obCmbModeloDocumento->SetRecord1    ( new RecordSet );

// lista de atributos selecionados
$obCmbModeloDocumento->SetNomeLista2 ('inModDocSelecionados');
$obCmbModeloDocumento->setCampoId2   ('[cod_documento]-[cod_tipo_documento]');
$obCmbModeloDocumento->setCampoDesc2 ('nome_documento');
$obCmbModeloDocumento->SetRecord2    ( new RecordSet );

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

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );

$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addTitulo( "Dados para Tipo de Licença" );

$obFormulario->addComponenteComposto  ( $obTxtTipoLicenca, $obCmbTipoLicenca );
$obFormulario->addComponente  ( $obCmbAtributosDinamicos );
$obFormulario->addComponente  ( $obCmbModeloDocumento );

$obSpnListaPermissao = new Span;
$obSpnListaPermissao->setID("spnErro");

$obFormulario->addSpan       ( $obSpnListaPermissao );

$obFormulario->ok();
$obFormulario->show();

//sistemaLegado::executaFrameOculto("ajaxJavaScript('".$pgOcul."', 'ListaPermissoes');");

?>
