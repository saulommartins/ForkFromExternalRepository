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
  * Página de Formulário para prorrogação de vencimentos
  * Data de criação : 16/02/2007

  * @author Analista: Fábio Bertoldi
  * @author Programador: Fernando Piccini Cercato

    * $Id: FMProrrogarVencimentos.php 59612 2014-09-02 12:00:51Z gelson $

  Caso de uso: uc-05.03.11
**/

/*
$Log$
Revision 1.1  2007/02/16 12:38:31  cercato
*** empty log message ***

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_ARR_COMPONENTES."MontaGrupoCredito.class.php" );
include_once ( CAM_GT_MON_COMPONENTES."IPopUpCredito.class.php" );

//Define o nome dos arquivos PHP
$stPrograma      = "ProrrogarVencimentos";
$pgForm          = "FM".$stPrograma.".php";
$pgProc          = "PR".$stPrograma.".php";

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao  );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( 'stCtrl' );
$obHdnCtrl->setValue( '' );

$obMontaGrupoCredito = new MontaGrupoCredito;
$obTxtExercicio = new Exercicio;
$obIPopUpCredito = new IPopUpCredito;
$obIPopUpCredito->setNull( true );

$obDataVencimento = new Data;
$obDataVencimento->setName ( "inDataVencimento" );
$obDataVencimento->setTitle ( "Novo Vencimento" );
$obDataVencimento->setRotulo ( "Novo Vencimento" );
$obDataVencimento->setNull ( false );

$obChkEmitirCarnes = new Checkbox;
$obChkEmitirCarnes->setName   ( "boEmitirCarnes"  );
$obChkEmitirCarnes->setLabel  ( "Seguir para a emissão de carnes" );
$obChkEmitirCarnes->setValue  ( true );

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction           ( $pgProc    );
$obForm->setTarget           ( "oculto"   );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm      ( $obForm               );
$obFormulario->addHidden    ( $obHdnAcao            );
$obFormulario->addHidden    ( $obHdnCtrl            );
$obFormulario->addTitulo    ( "Dados para Prorrogação" );
$obFormulario->addComponente( $obTxtExercicio       );
$obMontaGrupoCredito->geraFormulario( $obFormulario, true, true );
$obIPopUpCredito->geraFormulario( $obFormulario );
$obFormulario->addComponente( $obDataVencimento );
$obFormulario->addComponente( $obChkEmitirCarnes );

$obFormulario->Ok();
$obFormulario->show();
