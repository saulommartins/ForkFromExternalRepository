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
    * Página de Formulario de Anulação de Inscrição de Restos a Pagar do Exercício
    * Data de Criação   : 07/12/2007

    * @author Desenvolvedor: Anderson cAko Konze

    $Id: FLAnularRestosAPagar.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.02.31
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_ORC_COMPONENTES."ITextBoxSelectEntidadeGeral.class.php";
//Define o nome dos arquivos PHP
$stPrograma = "AnularRestosAPagar";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

//****************************************//
// Define COMPONENTES DO FORMULARIO
//****************************************//

//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "telaPrincipal" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( $stCtrl );

$stEval = "BloqueiaFrames(true,false);";

$obHdnEval = new HiddenEval;
$obHdnEval->setName  ( "stEval"            );
$obHdnEval->setValue ( $stEval             );

$stObs = "Esta rotina desfaz os lançamentos de Incriçao de Restos a pagar do exercício de ".Sessao::getExercicio().".";

//Define objeto de select multiplo de entidade por usuários
$obISelectEntidadeUsuarioCredito = new ITextBoxSelectEntidadeGeral ();
$obISelectEntidadeUsuarioCredito->obTextBox->setId    ( "inCodEntidadeCredito" );
$obISelectEntidadeUsuarioCredito->obTextBox->setName  ( "inCodEntidadeCredito" );
$obISelectEntidadeUsuarioCredito->obSelect->setName   ( "stNomEntidadeCredito" );
$obISelectEntidadeUsuarioCredito->obSelect->setId     ( "stNomEntidadeCredito" );
$obISelectEntidadeUsuarioCredito->obTextBox->setNull  ( true                   );
$obISelectEntidadeUsuarioCredito->obSelect->setNull   ( true                   );
$obISelectEntidadeUsuarioCredito->setNull             ( true                   );
$obISelectEntidadeUsuarioCredito->setObrigatorioBarra ( true                   );

$obLblObs = new Label;
$obLblObs->setValue   ( $stObs );
$obLblObs->setRotulo  ( "Observação: " );

//****************************************//
// Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );

$obFormulario->addHidden( $obHdnAcao              );
$obFormulario->addHidden( $obHdnCtrl              );
$obFormulario->addHidden( $obHdnEval, true        );
$obFormulario->addTitulo( "Anular Inscrição de Restos a Pagar do Exercício"        );
$obFormulario->addComponente($obISelectEntidadeUsuarioCredito);
$obFormulario->addComponente($obLblObs);
$obBtnOk = new Ok;
$obFormulario->defineBarra( array($obBtnOk) );
//$obFormulario->OK();

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
