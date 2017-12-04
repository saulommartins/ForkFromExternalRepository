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
    * Página de Formulario de Procura de Conta Corrente

    * Data de Criação   : 08/11/2005

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Lizandro Kirst da Silva
    * @ignore

    * $Id: FLProcurarConta.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.05.03

*/

/*
$Log$
Revision 1.11  2006/09/18 08:47:09  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_MON_NEGOCIO."RMONContaCorrente.class.php" );
include_once ( CAM_GT_MON_COMPONENTES."IMontaAgencia.class.php" );

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc

if ( empty( $_REQUEST['stAcao'] ) ) {
    $_REQUEST['stAcao'] = "incluir";
}

$arBancoSessao = Sessao::read( "banco" );
if ( $_REQUEST["NumAgencia"] )
    $arBancoSessao[1] = $_REQUEST["NumAgencia"];

if ( $_REQUEST["NumBanco"] )
    $arBancoSessao[0] = $_REQUEST["NumBanco"];

Sessao::write( "banco", $arBancoSessao );
$inNumBanco = $arBancoSessao[0];
$inNumAgencia = $arBancoSessao[1];

//Define o nome dos arquivos PHP
$stPrograma    = "ProcurarConta";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgForm        = "FM".$stPrograma.".php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJs          = "JS".$stPrograma.".js";
include_once( $pgJs );

$obRMONConta = new RMONContaCorrente;

$rsAgencia = new recordSet();

$obRMONConta->obRMONAgencia->obRMONBanco->setNumBanco ( $inNumBanco );
$obRMONConta->obRMONAgencia->listarAgencia($rsAgencia);

$obIMontaAgencia = new IMontaAgencia;
$obIMontaAgencia->obITextBoxSelectBanco->setNull( true );
$obIMontaAgencia->obITextBoxSelectBanco->obTextBox->setValue($inNumBanco);
$obIMontaAgencia->obITextBoxSelectBanco->obSelect->setValue($inNumBanco);
$obIMontaAgencia->obTextBoxSelectAgencia->setNull( true );
$obIMontaAgencia->obTextBoxSelectAgencia->obTextBox->setValue($inNumAgencia);
$obIMontaAgencia->obTextBoxSelectAgencia->obSelect->preencheCombo($rsAgencia);
$obIMontaAgencia->obTextBoxSelectAgencia->obSelect->setValue($inNumAgencia);

$obIFrame = new IFrame;
$obIFrame->setName  ("oculto");
$obIFrame->setWidth ("100%");
$obIFrame->setHeight("0");

$obIFrame2 = new IFrame;
$obIFrame2->setName   ( "telaMensagem");
$obIFrame2->setWidth  ( "100%"        );
$obIFrame2->setHeight ( "50"          );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction  ( $pgList );
//$obForm->setTarget  ("oculto" );

Sessao::remove('link');
Sessao::remove('stLink');

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $_REQUEST['stAcao']  );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName   ( "stCtrl" );
$obHdnCtrl->setValue  ( $_REQUEST['stCtrl'] );

$obHdnCampoNom = new Hidden;
$obHdnCampoNom->setName( "campoNom" );
$obHdnCampoNom->setValue( $_REQUEST['campoNom'] );

$obHdnCampoNum = new Hidden;
$obHdnCampoNum->setName( "campoNum" );
$obHdnCampoNum->setValue( $_REQUEST['campoNum']);

$obTxtConta = new TextBox ;
$obTxtConta->setRotulo    ( "Conta Corrente"                         );
$obTxtConta->setName      ( "stNumeroConta"                          );
$obTxtConta->setValue     ( $stNumeroConta                           );
$obTxtConta->setTitle     ( "Número da conta corrente"               );
$obTxtConta->setSize      ( 20                                       );
$obTxtConta->setMaxLength ( 20                                       );
$obTxtConta->setNull      ( true                                     );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm );
$obFormulario->addTitulo     ( "Dados para Filtro" );

$obFormulario->addHidden     ( $obHdnAcao );
$obFormulario->addHidden     ( $obHdnCtrl );
$obFormulario->addHidden     ( $obHdnCampoNom      );
$obFormulario->addHidden     ( $obHdnCampoNum      );

$obIMontaAgencia->geraFormulario( $obFormulario );
$obFormulario->addComponente ( $obTxtConta );

$obFormulario->OK();
$obFormulario->show();

$obIFrame->show();
$obIFrame2->show();
