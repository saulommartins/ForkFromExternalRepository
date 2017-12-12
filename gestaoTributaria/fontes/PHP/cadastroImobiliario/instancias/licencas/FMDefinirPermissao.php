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
    * Página de Definir Permissao para Conceder Licenca
    * Data de Criação   : 17/03/2008

    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Fernando Piccini Cercato

    $Id: FMDefinirPermissao.php 65128 2016-04-26 20:07:17Z evandro $

    * Casos de uso: uc-05.01.28
*/

/*
$Log$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GA_CGM_COMPONENTES."IPopUpCGMVinculado.class.php" );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMTipoLicenca.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "DefinirPermissao";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php?".Sessao::getId();
$pgJS   = "JS".$stPrograma.".js";

include_once( $pgJS );

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
$stAcao = $request->get('stAcao');

if ( empty( $stAcao ) ) {
    $stAcao = "definir";
}

Sessao::write('permissoes', array());
Sessao::remove('link');

$obTxtTipoLicenca = new TextBox;
$obTxtTipoLicenca->setRotulo  ( '*Tipo de Licença');
$obTxtTipoLicenca->setTitle   ( 'Informe o tipo de licença para definir permissão.');
$obTxtTipoLicenca->setName    ( 'inTipoLicenca');
$obTxtTipoLicenca->setInteiro ( true );
$obTxtTipoLicenca->setNull    ( true );

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
$obCmbTipoLicenca->setNULL         ( true );

//botoes do credito
$obBtnIncluirPermissao = new Button;
$obBtnIncluirPermissao->setName              ( "btnIncluirPermissao" );
$obBtnIncluirPermissao->setValue             ( "Incluir" );
$obBtnIncluirPermissao->setTipo              ( "button" );
$obBtnIncluirPermissao->obEvento->setOnClick ( "montaParametrosGET('IncluirPermissao', 'cmbTipoLicenca,inCGM,stNomCGM', true);" );
$obBtnIncluirPermissao->setDisabled          ( false );

$obBtnLimparPermissao = new Button;
$obBtnLimparPermissao->setName               ( "btnLimparPermissao" );
$obBtnLimparPermissao->setValue              ( "Limpar" );
$obBtnLimparPermissao->setTipo               ( "button" );
$obBtnLimparPermissao->obEvento->setOnClick  ( "ajaxJavaScript('".$pgOcul."', 'limpaPermissao');" );
$obBtnLimparPermissao->setDisabled           ( false );

$botoesPermissao = array ( $obBtnIncluirPermissao , $obBtnLimparPermissao );

$obSpnListaPermissao = new Span;
$obSpnListaPermissao->setID("spnListaPermissao");

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
$obHdnAcao->setValue( $_REQUEST['stAcao'] );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue ( $_REQUEST['stCtrl'] );

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );

$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addTitulo( "Dados para Permissão" );

$obPopUpCGM = new IPopUpCGMVinculado( $obForm );
$obPopUpCGM->setNull ( true );
$obPopUpCGM->setRotulo ( "*Usuário" );
$obPopUpCGM->setTitle ( "Informe o CGM do usuário que poderá conceder a licença." );
$obPopUpCGM->setTabelaVinculo ( 'administracao.usuario' );
$obPopUpCGM->setCampoVinculo  ( 'numcgm' );
$obPopUpCGM->setTipo('usuario');

$obFormulario->addComponenteComposto  ( $obTxtTipoLicenca, $obCmbTipoLicenca );
$obFormulario->addComponente ( $obPopUpCGM );

$obFormulario->defineBarra   ( $botoesPermissao, 'left', '' );
$obFormulario->addSpan       ( $obSpnListaPermissao );

$obSpnListaPermissao = new Span;
$obSpnListaPermissao->setID("spnErro");

$obFormulario->addSpan       ( $obSpnListaPermissao );

$obFormulario->ok();
$obFormulario->show();

sistemaLegado::executaFrameOculto("ajaxJavaScript('".$pgOcul."', 'ListaPermissoes');");

?>
