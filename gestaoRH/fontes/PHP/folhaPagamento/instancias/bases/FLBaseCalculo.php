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
    * Formulário
    * Data de Criação: 06/08/2008

    * @author Desenvolvedor: Rafael Garbin

    * Casos de uso: uc-04.05.67

    $Id:
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                     );

$stPrograma = 'BaseCalculo';
$pgFilt   = "FL".$stPrograma.".php";
$pgList   = "LS".$stPrograma.".php";
$pgForm   = "FM".$stPrograma.".php";
$pgProc   = "PR".$stPrograma.".php";
$pgOcul   = "OC".$stPrograma.".php";
$pgJs     = "JS".$stPrograma.".js";

$stAcao   = $_REQUEST["stAcao"];
$stCtrl   = $request->get("stCtrl");
Sessao::write("link","");

$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

$obHdnAcao = new Hidden;
$obHdnAcao->setName     ( "stAcao" );
$obHdnAcao->setValue    ( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName     ( "stCtrl" );
$obHdnCtrl->setValue    ( $stCtrl );

$obTxtNomBase = new TextBox;
$obTxtNomBase->setRotulo      ( "Nome da Base" );
$obTxtNomBase->setTitle       ( "Informe a nome da Base" );
$obTxtNomBase->setName        ( "stNomBase" );
$obTxtNomBase->setId          ( "stNomBase" );
$obTxtNomBase->setValue       ( $request->get("stNomBase") );
$obTxtNomBase->setSize        ( 50 );
$obTxtNomBase->setMaxLength   ( 50 );
$obTxtNomBase->setNull        ( true );
$obTxtNomBase->setInteiro     ( false );

$obForm = new Form;
$obForm->setAction( $pgList );

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm            ( $obForm );
$obFormulario->addTitulo          ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" );
$obFormulario->addHidden          ( $obHdnAcao );
$obFormulario->addHidden          ( $obHdnCtrl );
$obFormulario->addTitulo          ( "Bases de Cálculo" );
$obFormulario->addComponente      ( $obTxtNomBase );
$obFormulario->Cancelar();
$obFormulario->show();
?>
