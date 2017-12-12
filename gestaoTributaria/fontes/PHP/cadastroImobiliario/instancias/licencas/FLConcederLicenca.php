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
    * Página de Filtro do Conceder Licenca
    * Data de Criação   : 08/04/2008

    * @author Analista: Fábio Bertoldi
    * @author Programador: Fernando Piccini Cercato

    $Id: FLConcederLicenca.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.28
*/

/*
$Log$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMTipoLicenca.class.php" );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMLicenca.class.php" );
include_once ( CAM_GT_CIM_COMPONENTES."MontaLocalizacao.class.php" );
include_once ( CAM_GT_CIM_COMPONENTES."IPopUpImovel.class.php" );

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
}

Sessao::remove('link');

$rsLotes = new RecordSet;

$obCmbLotes = new Select;
$obCmbLotes->setRotulo       ( "Lote" );
$obCmbLotes->setTitle        ( "Informe o número do lote." );
$obCmbLotes->setName         ( "cmbLotes" );
$obCmbLotes->addOption       ( "", "Selecione" );
$obCmbLotes->setCampoId      ( "cod_lote" );
$obCmbLotes->setCampoDesc    ( "valor" );
$obCmbLotes->preencheCombo   ( $rsLotes );
$obCmbLotes->setStyle        ( "width: 40%;" );
$obCmbLotes->setNULL         ( true );
$obCmbLotes->obEvento->setOnChange ( "montaParametrosGET('LoteSelecionado', 'cmbLotes,inTipoLicenca', true);" );

$obTxtTipoLicenca = new TextBox;
$obTxtTipoLicenca->setRotulo  ( 'Tipo de Licença');
$obTxtTipoLicenca->setTitle   ( 'Informe o tipo de licença.');
$obTxtTipoLicenca->setName    ( 'inTipoLicenca');
$obTxtTipoLicenca->setInteiro ( true );
$obTxtTipoLicenca->setNull    ( false );

$obTCIMTipoLicenca = new TCIMTipoLicenca;
$obTCIMTipoLicenca->recuperaLicencaPorCGM( $rsTipoLicenca );

$obCmbTipoLicenca = new Select;
$obCmbTipoLicenca->setRotulo       ( "Tipo de Licença" );
$obCmbTipoLicenca->setTitle        ( "Informe o tipo de licença." );
$obCmbTipoLicenca->setName         ( "cmbTipoLicenca" );
$obCmbTipoLicenca->addOption       ( "", "Selecione" );
$obCmbTipoLicenca->setCampoId      ( "cod_tipo" );
$obCmbTipoLicenca->setCampoDesc    ( "nom_tipo" );
$obCmbTipoLicenca->preencheCombo   ( $rsTipoLicenca );
$obCmbTipoLicenca->setStyle        ( "width: 40%;" );
$obCmbTipoLicenca->setNULL         ( false );

$obTCIMLicenca = new TCIMLicenca;
$obTCIMLicenca->recuperaMaxLicenca( $rsMaxLicenca );
if ( $rsMaxLicenca->eof() ) {
    $stMascaraLicenca = "9/9999";
} else {
    $stMascaraLicenca = "";
    for ( $inX=0; $inX<strlen( $rsMaxLicenca->getCampo("cod_licenca") ); $inX++ ) {
        $stMascaraLicenca .= "9";
    }

    $stMascaraLicenca .= "/9999";
}

$obTxtLicenca = new TextBox;
$obTxtLicenca->setRotulo  ( 'Licença/Exercício' );
$obTxtLicenca->setTitle   ( 'Informe o código da licença e o exercício.' );
$obTxtLicenca->setName    ( 'stLicenca' );
$obTxtLicenca->setSize    ( strlen( $stMascaraLicenca ) );
$obTxtLicenca->setMaxLength ( strlen( $stMascaraLicenca ) );
$obTxtLicenca->setMascara ( $stMascaraLicenca );
$obTxtLicenca->setInteiro ( false );
$obTxtLicenca->setNull    ( true );
$obTxtLicenca->obEvento->setOnChange   ( "montaParametrosGET('montaLicenca', 'stLicenca', true);" );

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction ( $pgList );

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
$obFormulario->addTitulo( "Dados para Filtro" );

$obFormulario->addComponente ( $obTxtLicenca );

$obIPopUpImovel = new IPopUpImovel;
$obIPopUpImovel->obInnerImovel->setNULL( true );
$obIPopUpImovel->geraFormulario ( $obFormulario );

$obMontaLocalizacao = new MontaLocalizacao;
$obMontaLocalizacao->setObrigatorio( false );
$obMontaLocalizacao->geraFormulario( $obFormulario );
$stOnChange = "ajaxJavaScriptSincrono('".$pgOcul."&stChaveLocalizacao='+this.value,'BuscaLocalizacao');";
$obMontaLocalizacao->obBscChaveLocalizacao->obCampoCod->obEvento->setOnChange( $stOnChange );
$obMontaLocalizacao->obBscChaveLocalizacao->obCampoCod->obEvento->setOnBlur( $stOnChange );

$obFormulario->addComponente ( $obCmbLotes );
$obFormulario->addComponenteComposto ( $obTxtTipoLicenca, $obCmbTipoLicenca );

$obSpnListaPermissao = new Span;
$obSpnListaPermissao->setID("spnErro");
$obFormulario->addSpan ( $obSpnListaPermissao );

$obFormulario->ok();
$obFormulario->show();

?>
