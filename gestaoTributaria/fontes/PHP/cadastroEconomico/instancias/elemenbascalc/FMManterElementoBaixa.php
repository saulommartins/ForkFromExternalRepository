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
    * Formulario para Edificação
    * Data de Criação   : 15/04/2005
    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Marcelo B. Paulino
    * @package URBEM
    * @subpackage Regra

    * $Id: FMManterElementoBaixa.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.02.05

*/

/*
$Log$
Revision 1.7  2006/09/15 14:32:46  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CEM_NEGOCIO."RCEMElemento.class.php"  );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMAtividade.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterElemento";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs 	= "JS".$stPrograma.".js";

include_once( $pgJs );
$stAcao = $request->get('stAcao');
$link = Sessao::read( "link" );
$stLink = "&pg=".$link["pg"]."&pos=".$link["pos"]."&stAcao=".$stAcao;
if ( empty( $stAcao ) ) {
    $stAcao = "baixar";
}

//Instancia objetos
$obRCEMElemento = new RCEMElemento( new RCEMAtividade );

$inCodigoElemento = $_REQUEST["inCodigoElemento"];
$obRCEMElemento->setCodigoElemento ( $inCodigoElemento );
$obRCEMElemento->consultarElemento();
$stNomeElemento = $obRCEMElemento->getNomeElemento();

// OBJETOS HIDDEN
$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnCodigoElemento = new Hidden;
$obHdnCodigoElemento->setName( "inCodigoElemento" );
$obHdnCodigoElemento->setValue( $inCodigoElemento );

$obHdnNomeElemento = new Hidden;
$obHdnNomeElemento->setName( "stNomeElemento" );
$obHdnNomeElemento->setValue( $stNomeElemento );

// DEFINICAO DOS COMPONENTES DO FORMULARIO
$obLblCodigoElemento = new Label;
$obLblCodigoElemento->setRotulo ( "Código" );
$obLblCodigoElemento->setValue  ( $inCodigoElemento  );

$obLblNomeElemento = new Label;
$obLblNomeElemento->setRotulo ( "Nome" );
$obLblNomeElemento->setValue  ( $stNomeElemento  );

$obTxtJustificativa = new TextArea;
$obTxtJustificativa->setName   ( "stJustificativa" );
$obTxtJustificativa->setRotulo ( "Motivo" );
$obTxtJustificativa->setId     ( "motivo");
$obTxtJustificativa->setRows   ( 5 );
$obTxtJustificativa->setCols   ( 30 );
$obTxtJustificativa->setNull   ( false );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

$obFormulario = new Formulario;
$obFormulario->setAjuda           ("UC-05.02.05"   );
$obFormulario->addForm            ( $obForm              );
$obFormulario->addHidden          ( $obHdnCtrl           );
$obFormulario->addHidden          ( $obHdnAcao           );
$obFormulario->addHidden          ( $obHdnCodigoElemento );
$obFormulario->addHidden          ( $obHdnNomeElemento   );

$obFormulario->addTitulo          ( "Dados para elemento" );
$obFormulario->addComponente      ( $obLblCodigoElemento  );
$obFormulario->addComponente      ( $obLblNomeElemento    );
$obFormulario->addComponente      ( $obTxtJustificativa   );

$obFormulario->setFormFocus( $obTxtJustificativa->getid() );

$obFormulario->Cancelar( $pgList );
$obFormulario->show();
?>
