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
    * Página de Conceder Licenca
    * Data de Criação   : 17/03/2008

    * @author Analista: Fábio Bertoldi
    * @author Programador: Fernando Piccini Cercato

    $Id: FMConcederLicenca.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.28
*/

/*
$Log$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMTipoLicenca.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ConcederLicenca";
$pgForm1    = "FM".$stPrograma."Imoveis.php";
$pgForm2    = "FM".$stPrograma."Lotes.php";
$pgForm3    = "FM".$stPrograma."Edificacao.php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php?".Sessao::getId();
$pgJS       = "JS".$stPrograma.".js";

include_once( $pgJS );

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "definir";
}

Sessao::write('link', array() );

$obTxtTipoLicenca = new TextBox;
$obTxtTipoLicenca->setRotulo  ( 'Tipo de Licença'           );
$obTxtTipoLicenca->setTitle   ( 'Informe o tipo de licença.');
$obTxtTipoLicenca->setName    ( 'inTipoLicenca'             );
$obTxtTipoLicenca->setInteiro ( true                        );
$obTxtTipoLicenca->setNull    ( false                       );

$obTCIMTipoLicenca = new TCIMTipoLicenca;
$obTCIMTipoLicenca->recuperaLicencaPorCGM( $rsTipoLicenca   );

$obCmbTipoLicenca = new Select;
$obCmbTipoLicenca->setRotulo       ( "Tipo de Licença"            );
$obCmbTipoLicenca->setTitle        ( "Informe o tipo de licença." );
$obCmbTipoLicenca->setName         ( "cmbTipoLicenca"             );
$obCmbTipoLicenca->addOption       ( "", "Selecione"              );
$obCmbTipoLicenca->setCampoId      ( "cod_tipo"                   );
$obCmbTipoLicenca->setCampoDesc    ( "nom_tipo"                   );
$obCmbTipoLicenca->preencheCombo   ( $rsTipoLicenca               );
$obCmbTipoLicenca->setStyle        ( "width: 40%;"                );
$obCmbTipoLicenca->setNULL         ( false                        );

$obBtnOK = new OK;
$obBtnOK->setName              ( "btnOk"                 );
$obBtnOK->obEvento->setOnClick ( "carregarFormulario();" );

$obBtnLimpar = new Limpar;

$botoesSpanBotoes = array ( $obBtnOK, $obBtnLimpar );
//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction ( $pgProc  );
$obForm->settarget ( "oculto" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao  );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( $stCtrl  );

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
//$obFormulario->addForm( $obForm );

$obFormulario->addHidden( $obHdnAcao           );
$obFormulario->addHidden( $obHdnCtrl           );
$obFormulario->addTitulo( "Dados para Licença" );

$obFormulario->addComponenteComposto  ( $obTxtTipoLicenca, $obCmbTipoLicenca );
$obFormulario->defineBarra   ( $botoesSpanBotoes, 'left', '' );

$obFormulario->show();

?>
