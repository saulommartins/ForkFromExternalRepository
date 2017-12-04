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
    * Arquivo de popup para manutenção de usuários
    * Data de Criação: 25/07/2005

    * @author Analista: Cassiano
    * @author Desenvolvedor: Cassiano

    Casos de uso: uc-03.03.02

    $Id: FLProcurarAlmoxarife.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include(CAM_GP_ALM_NEGOCIO."RAlmoxarifadoAlmoxarife.class.php");

$stPrograma = "ProcurarAlmoxarife";
$pgFilt = "FL".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$obRegra = new RAlmoxarifadoAlmoxarife;

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];

Sessao::remove('linkPopUp');

$obForm = new Form;
$obForm->setAction( $pgList );
$obForm->setTarget( "" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnForm = new Hidden;
$obHdnForm->setName( "nomForm" );
$obHdnForm->setValue( $nomForm );

$obHdnCampoNum = new Hidden;
$obHdnCampoNum->setName( "campoNum" );
$obHdnCampoNum->setValue( $_REQUEST['campoNum'] );

$obHdnCampoNom = new Hidden;
$obHdnCampoNom->setName( "campoNom" );
$obHdnCampoNom->setValue( $_REQUEST['campoNom'] );

$obHdnTipoBusca = new Hidden;
$obHdnTipoBusca->setName( "tipoBusca" );
$obHdnTipoBusca->setValue( $_REQUEST['tipoBusca'] );

$obTxtNumCGM = new TextBox;
$obTxtNumCGM->setName      ( "inNumCGM" );
$obTxtNumCGM->setValue     ( $inNumCGM  );
$obTxtNumCGM->setRotulo    ( "CGM Usuário" );
$obTxtNumCGM->setTitle     ( "Informe o CGM do usuário" );
$obTxtNumCGM->setSize      ( 11 );
$obTxtNumCGM->setMaxLength ( 9  );
$obTxtNumCGM->setNull      ( true );
$obTxtNumCGM->setInteiro   ( true );

$obTxtNomCGM = new TextBox;
$obTxtNomCGM->setName      ( "stNomCGM" );
$obTxtNomCGM->setValue     ( $stNomCGM  );
$obTxtNomCGM->setRotulo    ( "Nome"     );
$obTxtNomCGM->setTitle     ( "Informe o nome" );
$obTxtNomCGM->setSize      ( 80 );
$obTxtNomCGM->setMaxLength ( 100 );
$obTxtNomCGM->setNull      ( true );

$obTxtUsername = new TextBox;
$obTxtUsername->setName      ( "stUsername" );
$obTxtUsername->setValue     ( $stUsername );
$obTxtUsername->setRotulo    ( "Usuário" );
$obTxtUsername->setTitle     ( "Informe o usuário" );
$obTxtUsername->setSize      ( 17 );
$obTxtUsername->setMaxLength ( 15 );
$obTxtUsername->setNull      ( true );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addTitulo            ( "Dados para filtro" );
$obFormulario->addForm              ( $obForm );
$obFormulario->addHidden            ( $obHdnAcao );
$obFormulario->addHidden            ( $obHdnCtrl );
$obFormulario->addHidden            ( $obHdnForm );
$obFormulario->addHidden            ( $obHdnCampoNum  );
$obFormulario->addHidden            ( $obHdnCampoNom  );
$obFormulario->addHidden            ( $obHdnTipoBusca );
$obFormulario->addComponente        ( $obTxtNumCGM    );
$obFormulario->addComponente        ( $obTxtNomCGM    );
$obFormulario->addComponente        ( $obTxtUsername  );
$obFormulario->OK                   ();
$obFormulario->show                 ();

?>
