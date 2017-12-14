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
    * Página de Filtro para popup de AUTORIDADE
    * Data de Criação   : 26/09/2006

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Diego Bueno Coelho
    * @ignore

    * $Id: FLProcurarAutoridade.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.04.08

*/

/*
$Log$
Revision 1.1  2006/09/26 11:15:13  dibueno
*** empty log message ***

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_PES_COMPONENTES."IPopUpCGMServidor.class.php" );
include_once ( CAM_GT_DAT_COMPONENTES."IPopUpAutoridade.class.php" );

/*$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}*/

//Define o nome dos arquivos PHP
$stPrograma    = "ProcurarAutoridade";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgForm        = "FM".$stPrograma.".php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJs          = "JS".$stPrograma.".js";

//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgList );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $_REQUEST['stAcao'] );

//Define o objeto HIDDEN para armazenar variavel de controle (stCtrl)
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( $_REQUEST['stCtrl'] );

$obHdnCampoNom = new Hidden;
$obHdnCampoNom->setName( "campoNom" );
$obHdnCampoNom->setValue( $_REQUEST['campoNom'] );

$obHdnCampoNum = new Hidden;
$obHdnCampoNum->setName( "campoNum" );
$obHdnCampoNum->setValue( $_REQUEST['campoNum'] );

//tipo de autoridade
$obRdbProcurador = new Radio;
$obRdbProcurador->setRotulo   ( "Tipo de Autoridade" );
$obRdbProcurador->setTitle    ( "Informe o tipo de autoridade a ser cadastrada." );
$obRdbProcurador->setName     ( "stTipoAutoridade" );
$obRdbProcurador->setLabel    ( "Procurador Municipal" );
$obRdbProcurador->setValue    ( "procurador" );
$obRdbProcurador->setNull     ( true );

//Tipo de autoridade
$obRdbAutoridade = new Radio;
$obRdbAutoridade->setRotulo   ( "Tipo de Autoridade" );
$obRdbAutoridade->setTitle    ( "Selecione o tipo de autoridade." );
$obRdbAutoridade->setName     ( "stTipoAutoridade" );
$obRdbAutoridade->setLabel    ( "Autoridade Competente" );
$obRdbAutoridade->setValue    ( "autoridade" );
$obRdbAutoridade->setNull     ( true );

$obTxtNomCGM = new TextBox;
$obTxtNomCGM->setName      ( "stNomCGMServidor"   );
$obTxtNomCGM->setTitle    ( "Informe o nome do servidor." );
$obTxtNomCGM->setSize      ( 80 );
$obTxtNomCGM->setRotulo    ( "Nome do Servidor");

$obTxtNumCGMServidor = new TextBox;
$obTxtNumCGMServidor->setName      ( "inNumCGMServidor"   );
$obTxtNumCGMServidor->setTitle    ( "Informe o CGM do servidor." );
$obTxtNumCGMServidor->setSize      ( 8 );
$obTxtNumCGMServidor->setRotulo    ( "CGM Servidor");

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm );
//$obFormulario->setAjuda      ( "UC-05.04.08" );
$obFormulario->addHidden     ( $obHdnAcao );
$obFormulario->addHidden     ( $obHdnCtrl );
$obFormulario->addHidden     ( $obHdnCampoNom      );
$obFormulario->addHidden     ( $obHdnCampoNum      );
$obFormulario->addTitulo     ( "Dados para Autoridade" );
$obFormulario->addComponenteComposto ( $obRdbProcurador, $obRdbAutoridade );
$obFormulario->AddComponente ( $obTxtNumCGMServidor );
$obFormulario->AddComponente ( $obTxtNomCGM );

$obFormulario->Ok ();
$obFormulario->show();
