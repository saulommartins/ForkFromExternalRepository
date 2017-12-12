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
    * Página de Popup de atividade
    * Data de Criação   : 27/12/2004

    * @author Tonismar Régis Bernardo

    * @ignore

    * $Id: FLProcurarAtividade.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso : uc-05.02.07, uc-03.04.03
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GT_CEM_NEGOCIO."RCEMAtividade.class.php";

//Define valores para sessao
if ($stCadastro == 'modalidade') {
    session_regenerate_id();
    Sessao::setId("PHPSESSID=".session_id());
    $sessao->geraURLRandomica();
    Sessao::write('acao', "721" );
    Sessao::write('modulo', "12");
}

//Define o nome dos arquivos PHP
$stPrograma = "ProcurarAtividade";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";
include_once ($pgJS);

$obRCEMAtividade = new RCEMAtividade;
$obRCEMAtividade->geraMascara( $stMascara );

$rsNivel = new RecordSet;
if ( Sessao::read( "CodigoVigencia" ) ) {
    $obRCEMAtividade->setCodigoVigencia( Sessao::read( "CodigoVigencia" ) );
}

$obRCEMAtividade->listarNiveis( $rsNivel );

$stAcao = $request->get('stAcao');

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $stAcao  );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnForm = new Hidden;
$obHdnForm->setName( "nomForm" );
$obHdnForm->setValue( $_REQUEST['nomForm'] );

$obHdnCampoNum = new Hidden;
$obHdnCampoNum->setName( "campoNum" );
$obHdnCampoNum->setValue( $_REQUEST['campoNum'] );

//Define HIDDEN com o o nome do campo texto
$obHdnCampoNom = new Hidden;
$obHdnCampoNom->setName( "campoNom" );
$obHdnCampoNom->setValue( $_REQUEST['campoNom'] );

$obHdnCampoFoco = new Hidden;
$obHdnCampoFoco->setName( "campoFoco" );
$obHdnCampoFoco->setValue( $_REQUEST['campoFoco'] );

$obCmbNivel = new Select;
$obCmbNivel->setRotulo    ( "Nível"         );
$obCmbNivel->addOption    ( "", "Todos"     );
$obCmbNivel->setCampoId   ( "cod_nivel"     );
$obCmbNivel->setCampoDesc ( "nom_nivel"     );
$obCmbNivel->setStyle     ( "width:250px"   );
$obCmbNivel->setName      ( "inCodigoNivel" );
$obCmbNivel->preencheCombo( $rsNivel     );

$obTxtCodigo = new TextBox;
$obTxtCodigo->setName      ( "stValorComposto" );
$obTxtCodigo->setRotulo    ( "Código" );
$obTxtCodigo->setMaxLength ( strlen( $stMascara ) );
$obTxtCodigo->setSize      ( strlen( $stMascara ) );
$obTxtCodigo->obEvento->setOnKeyUp("mascaraDinamico('".$stMascara."', this, event);");

$obTxtNomeAtividade = new TextBox;
$obTxtNomeAtividade->setName      ( "stNomeAtividade" );
$obTxtNomeAtividade->setRotulo    ( "Nome" );
$obTxtNomeAtividade->setMaxLength ( 80 );
$obTxtNomeAtividade->setSize      ( 40 );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgList );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm               );
$obFormulario->addTitulo     ( "Dados para filtro"   );
$obFormulario->addHidden     ( $obHdnAcao            );
$obFormulario->addHidden     ( $obHdnCtrl            );
$obFormulario->addHidden     ( $obHdnForm            );
$obFormulario->addHidden     ( $obHdnCampoNum        );
$obFormulario->addHidden     ( $obHdnCampoNom        );
$obFormulario->addHidden     ( $obHdnCampoFoco       );
$obFormulario->addComponente ( $obCmbNivel           );
$obFormulario->addComponente ( $obTxtCodigo          );
$obFormulario->addComponente ( $obTxtNomeAtividade   );
$obFormulario->OK();
$obFormulario->show();
?>
