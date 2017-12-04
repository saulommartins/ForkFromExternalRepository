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
  * Página de Formulario para prorrogar desoneração
  * Data de criação : 07/06/2005

  * @author Analista: Fábio Bertoldi
  * @author Programador: tonismar R. Bernardo

  * @ignore

    * $Id: FMProrrogarDesoneracao.php 59612 2014-09-02 12:00:51Z gelson $

  Caso de uso: uc-05.03.04
**/

/*
$Log$
Revision 1.11  2006/11/21 15:49:28  cercato
bug #6853#

Revision 1.10  2006/09/15 11:50:40  fabio
corrigidas tags de caso de uso

Revision 1.9  2006/09/15 11:04:52  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterDesoneracao";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once ( $pgJS );

$stAcao = $_REQUEST[ "stAcao" ];
$stCtrl = $_REQUEST[ "stCtrl" ];
//DEFINIÇÃO DOS COMPONENTES
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( 'stAcao' );
$obHdnAcao->setValue( $stAcao  );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( 'stCtrl' );
$obHdnCtrl->setValue( $stCtrl  );

$obHdnDtExp = new Hidden;
$obHdnDtExp->setName ( 'dtExpiracao' );
$obHdnDtExp->setValue( $_REQUEST["dtProrrogacao"]?$_REQUEST["dtProrrogacao"]:$_REQUEST["dtExpiracao"] );

$obHdnCodDes = new Hidden;
$obHdnCodDes->setName ( 'inCodigoDesoneracao' );
$obHdnCodDes->setValue( $_REQUEST["inCodigoDesoneracao"] );

$obHdnCGM = new Hidden;
$obHdnCGM->setName ( 'inNumCGM' );
$obHdnCGM->setValue( $_REQUEST["inNumCGM"] );

$obHdnOcorrencia = new Hidden;
$obHdnOcorrencia->setName ( 'inOcorrencia' );
$obHdnOcorrencia->setValue( $_REQUEST["inOcorrencia"] );

$obHdnConcessao = new Hidden;
$obHdnConcessao->setName ( 'dtConcessao' );
$obHdnConcessao->setValue( $_REQUEST["dtConcessao"] );

$obHdnProrrogacao = new Hidden;
$obHdnProrrogacao->setName ( 'dtProrrogacao' );
$obHdnProrrogacao->setValue( $_REQUEST["dtProrrogacao"] );

$obHdnRevogacao = new Hidden;
$obHdnRevogacao->setName ( 'dtRevogacao' );
$obHdnRevogacao->setValue( $_REQUEST["dtRevogacao"] );

$obLblDesoneracao = new Label;
$obLblDesoneracao->setName   ( "stDesoneracao" );
$obLblDesoneracao->setRotulo ( "Desoneração" );
$obLblDesoneracao->setValue  ( $_REQUEST["stDesoneracao"] );
$obLblDesoneracao->setId     ( "stDesoneracao" );

$obLblCGM = new Label;
$obLblCGM->setName   ( "inNumCGM" );
$obLblCGM->setRotulo ( "CGM"      );
$obLblCGM->setValue  ( $_REQUEST["stCGM"] );
$obLblCGM->setId     ( "inNumCGM" );

$obDtProDes = new Data;
$obDtProDes->setName      ( "dtProDes" );
$obDtProDes->setValue     ( $_REQUEST["dtProDes"]  );
if ($_REQUEST['stAcao'] == 'prorrogar') {
    $obDtProDes->setRotulo    ( "Data da Prorrogação"   );
    $obDtProDes->setTitle     ( "Nova data para o término da desoneração." );
    $obDtProDes->obEvento->setOnChange( "buscaValor('verificaProrrogacao');" );
} elseif ($_REQUEST['stAcao'] == 'revogar') {
    $obDtProDes->setRotulo    ( "Data da Revogação"   );
    $obDtProDes->setTitle     ( "Data para a revogação da desoneração." );
}
$obDtProDes->setMaxLength ( 20         );
$obDtProDes->setSize      ( 10         );
$obDtProDes->setNull      ( false      );

//DEFINICAO DO FORMULARIO
$obForm = new Form;
$obForm->setAction            ( $pgProc  );
$obForm->setTarget            ( 'oculto' );

$obFormulario = new Formulario;
$obFormulario->addForm        ( $obForm      );
$obFormulario->addHidden      ( $obHdnAcao   );
$obFormulario->addHidden      ( $obHdnCtrl   );
$obFormulario->addHidden      ( $obHdnCodDes );
$obFormulario->addHidden      ( $obHdnCGM    );
$obFormulario->addHidden      ( $obHdnOcorrencia );
$obFormulario->addHidden      ( $obHdnConcessao );
$obFormulario->addHidden      ( $obHdnProrrogacao );
$obFormulario->addHidden      ( $obHdnRevogacao );
$obFormulario->addHidden      ( $obHdnDtExp );
if ($_REQUEST['stAcao'] == 'prorrogar') {
    $obFormulario->addTitulo      ( "Dados para Prorrogação" );
} else {
    $obFormulario->addTitulo      ( "Dados para Revogação" );
}

$obFormulario->addComponente  ( $obLblDesoneracao );
$obFormulario->addComponente  ( $obLblCGM );
$obFormulario->addComponente  ( $obDtProDes );

$obFormulario->Ok();
$obFormulario->Show();
