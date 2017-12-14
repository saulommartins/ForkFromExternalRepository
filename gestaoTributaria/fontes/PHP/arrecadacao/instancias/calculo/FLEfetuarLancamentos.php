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
    * Página de Formulario para EFETUAR LANCAMENTOS  - MODULO ARRECADACAO
    * Data de criação : 01/06/2005

    * @author Analista: Fabio Bertold Rodrigues
    * @author Programador: Marcelo Boezzio Paulino

    * $Id: FLEfetuarLancamentos.php 59612 2014-09-02 12:00:51Z gelson $

    Caso de uso: uc-05.03.05
**/

/*
$Log$
Revision 1.6  2006/09/15 11:50:26  fabio
corrigidas tags de caso de uso

Revision 1.5  2006/09/15 10:57:57  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_MON_NEGOCIO."RMONCredito.class.php" );

//Define o nome dos arquivos PHP
$stPrograma      = "EfetuarLancamentos";
$pgFilt          = "FL".$stPrograma.".php";
$pgList          = "LS".$stPrograma.".php";
$pgForm          = "FM".$stPrograma.".php";
$pgProc          = "PR".$stPrograma.".php";
$pgFormAutomatico = $pgForm;
$pgFormManual = "FM".$stPrograma."Manual.php";
$pgOcul          = "OCManterCalculo.php";
$pgJs            = "JSManterCalculo.js";
include_once( $pgJs );

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

Sessao::write( "link", "" );

// instancia objeto
$obRMONCredito = new RMONCredito;
// pegar mascara de credito
$obRMONCredito->consultarMascaraCredito();
$stMascaraCredito = $obRMONCredito->getMascaraCredito();

// OBJETOS HIDDEN
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( $_REQUEST["stCtrl"]  );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao  );

$obHdnCodModulo = new Hidden;
$obHdnCodModulo->setName  ( "inCodModulo" );
$obHdnCodModulo->setValue ( $_REQUEST["inCodModulo"] );

// DEFINE OBJETOS DO FORMULARIO
$obRadioLancamentoAutomatico = new Radio;
$obRadioLancamentoAutomatico->setName         ( "boTipoLancamento"               );
$obRadioLancamentoAutomatico->setTitle            ( "Efetuar Lançamento"             );
$obRadioLancamentoAutomatico->setRotulo       ( "Efetuar Lançamento"                           );
$obRadioLancamentoAutomatico->setValue        ( "Automático"                            );
$obRadioLancamentoAutomatico->setLabel        ( "Automático"                            );
$obRadioLancamentoAutomatico->setNull         ( false                               );
$obRadioLancamentoAutomatico->setChecked      ( false                         );
$obRadioLancamentoAutomatico->obEvento->setOnChange (  "habilitaTipoLancamento( 'false' );"   );

$obRadioLancamentoManual = new Radio;
$obRadioLancamentoManual->setName     ( "boTipoLancamento"               );
$obRadioLancamentoManual->setValue    ( "Manual"                        );
$obRadioLancamentoManual->setLabel    ( "Manual"                        );
$obRadioLancamentoManual->setNull     ( true                               );
$obRadioLancamentoManual->setChecked      ( true                         );
$obRadioLancamentoManual->obEvento->setOnChange (  "habilitaTipoLancamento( 'true' );");

$obRadioTipoCredito = new Radio;
$obRadioTipoCredito->setName         ( "boTipoLancamentoManual"        );
$obRadioTipoCredito->setTitle            ( "Efetuar Lançamento"                 );
$obRadioTipoCredito->setRotulo       ( "Lançamento Manual de "             );
$obRadioTipoCredito->setValue        ( "Crédito"                                        );
$obRadioTipoCredito->setLabel        ( "Crédito"                                        );
$obRadioTipoCredito->setNull           ( true                               );
//$obRadioTipoCredito->setDisabled           ( true                               );
$obRadioTipoCredito->setChecked      ( true                         );

$obRadioTipoGrupoCredito = new Radio;
$obRadioTipoGrupoCredito->setName     ( "boTipoLancamentoManual"               );
$obRadioTipoGrupoCredito->setValue    ( "GrupoCrédito"                        );
$obRadioTipoGrupoCredito->setLabel    ( "Grupo de Crédito"                        );
//$obRadioTipoGrupoCredito->setDisabled ( true );
$obRadioTipoGrupoCredito->setNull     ( true                               );

$obBtnOK = new OK;
$obBtnOK->obEvento->setOnClick( "verificaAction()" );
$obBtnLimpar = new Limpar;
$obBtnLimpar->obEvento->setOnClick( "LimparFL();" );

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
//$obForm->setAction           ( $pgProc  );
//$obForm->setTarget           ( "oculto" );
//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm              );
$obFormulario->addHidden     ( $obHdnCtrl           );
$obFormulario->addHidden     ( $obHdnAcao           );
$obFormulario->addHidden     ( $obHdnCodModulo      );
$obFormulario->addTitulo     ( "Dados para Cálculo" );
$obFormulario->addComponenteComposto ( $obRadioLancamentoAutomatico, $obRadioLancamentoManual  );
$obFormulario->addComponenteComposto ( $obRadioTipoCredito, $obRadioTipoGrupoCredito  );
//$obFormulario->addComponente ( $obBscGrupoCredito   );

$obFormulario->defineBarra           ( array( $obBtnOK, $obBtnLimpar)                             );
$obFormulario->show();

?>
