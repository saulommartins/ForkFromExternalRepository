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
    * Página de filtro para o cadastro de condomínio
    * Data de Criação   : 21/03/2005

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Marcelo Boezio Paulino

    * @ignore

    * $Id: FLProcurarCondominio.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.14
*/

/*
$Log$
Revision 1.6  2006/09/15 15:03:55  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMCondominio.class.php"       );

//Define o nome dos arquivos PHP
$stPrograma    = "ProcurarCondominio";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgForm        = "FM".$stPrograma.".php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJs          = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

Sessao::remove('link');

// DEFINE OBJETOS DAS CLASSES
$obRCIMCondominio = new RCIMCondominio;
$rsTipoCondominio = new RecordSet;

// OBJETOS HIDDEN
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( $_REQUEST['stCtrl'] );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $_REQUEST['stAcao'] );

$obHdnForm = new Hidden;
$obHdnForm->setName( "nomForm" );
$obHdnForm->setValue( $_REQUEST['nomForm'] );

$obHdnCampoNum = new Hidden;
$obHdnCampoNum->setName( "campoNum" );
$obHdnCampoNum->setValue( $_REQUEST['campoNum'] );

$obHdnCampoNom = new Hidden;
$obHdnCampoNom->setName( "campoNom" );
$obHdnCampoNom->setValue( $_REQUEST['campoNom'] );

if ($_REQUEST['tipoBusca'] != '') {
    $obHdnCodLote = new Hidden;
    $obHdnCodLote->setName ( "inCodLote" );
    $obHdnCodLote->setValue( $_REQUEST['tipoBusca'] );
}

// DEFINE OBJETOS DO FORMULARIO
$obTxtCodigoCondominio = new TextBox;
$obTxtCodigoCondominio->setRotulo      ( "Código"                            );
$obTxtCodigoCondominio->setName        ( "inCodigoCondominio"                );
$obTxtCodigoCondominio->setValue       ( $_REQUEST["inCodigoCondominio"]     );
$obTxtCodigoCondominio->setSize        ( 8                                   );
$obTxtCodigoCondominio->setMaxLength   ( 8                                   );
$obTxtCodigoCondominio->setNull        ( true                                );
$obTxtCodigoCondominio->setInteiro     ( true                                );

$obTxtNomCondominio = new TextBox;
$obTxtNomCondominio->setRotulo       ( "Nome"               );
$obTxtNomCondominio->setName         ( "stNomCondominio"    );
$obTxtNomCondominio->setValue        ( $stNomCondominio     );
$obTxtNomCondominio->setSize         ( 80 );
$obTxtNomCondominio->setMaxLength    ( 80 );
$obTxtNomCondominio->setNull         ( true );

$obTxtNumCGM = new TextBox;
$obTxtNumCGM->setRotulo      ( "CGM"                  );
$obTxtNumCGM->setName        ( "inNumCGM"             );
$obTxtNumCGM->setValue       ( $_REQUEST["inNumCGM"]  );
$obTxtNumCGM->setSize        ( 8                      );
$obTxtNumCGM->setMaxLength   ( 8                      );
$obTxtNumCGM->setNull        ( true                   );
$obTxtNumCGM->setInteiro     ( true                   );

$obTxtNomCGM = new TextBox;
$obTxtNomCGM->setRotulo       ( "Nome do CGM"     );
$obTxtNomCGM->setName         ( "stNomCGM"        );
$obTxtNomCGM->setValue        ( $stNomCGM         );
$obTxtNomCGM->setSize         ( 80 );
$obTxtNomCGM->setMaxLength    ( 80 );
$obTxtNomCGM->setNull         ( true );

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction  ( $pgList );
$obForm->setTarget  ( ""      );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm   ( $obForm        );
$obFormulario->addHidden ( $obHdnCtrl     );
$obFormulario->addHidden ( $obHdnAcao     );
$obFormulario->addHidden ( $obHdnForm     );
$obFormulario->addHidden ( $obHdnCampoNum );
$obFormulario->addHidden ( $obHdnCampoNom );
if ($_REQUEST['tipoBusca'] != '') {
    $obFormulario->addHidden( $obHdnCodLote );
}

$obFormulario->addTitulo ( "Dados para filtro" );

$obFormulario->addComponente         ( $obTxtCodigoCondominio );
$obFormulario->addComponente         ( $obTxtNomCondominio    );
$obFormulario->addComponente         ( $obTxtNumCGM           );
$obFormulario->addComponente         ( $obTxtNomCGM           );

$obFormulario->Ok();
$obFormulario->show  ();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
