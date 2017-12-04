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
    * Página de Formulario de Inclusao/Alteracao de Conta Corrente

    * Data de Criação   : 03/11/2005

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Lizandro Kirst da Silva
    * @ignore

    * $Id: FMManterConta.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.05.03

*/

/*
$Log$
Revision 1.12  2006/09/15 14:57:40  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GT_MON_COMPONENTES."IMontaAgencia.class.php" );
include_once( CAM_GT_MON_NEGOCIO."RMONContaCorrente.class.php");

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc

if ( empty( $_REQUEST['stAcao'] ) ) {
    $_REQUEST['stAcao'] = "incluir";
}

//Define o nome dos arquivos PHP
$stPrograma    = "ManterConta";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php?".Sessao::getId();
$pgForm        = "FM".$stPrograma.".php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJs          = "JS".$stPrograma.".js";

include_once( $pgJs );

$obIMontaAgencia = new IMontaAgencia;
$obRMONContaCorrente=new RMONContaCorrente();

//DEFINICAO DOS COMPONENTES
//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgProc );

$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $_REQUEST['stAcao'] );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName   ( "stCtrl" );
$obHdnCtrl->setValue  ( $_REQUEST['stCtrl'] );

$obHdnCodBanco =  new Hidden;
$obHdnCodBanco->setName   ( "inCodBanco" );
$obHdnCodBanco->setValue  ( $_REQUEST["inCodBanco"]  );

$obHdnNumAgencia = new Hidden;
$obHdnNumAgencia->setName    ( "stNumHdnAgencia"           );
$obHdnNumAgencia->setValue   ( $_REQUEST["stNumAgencia"]   );

$obHdnCodConta = new Hidden;
$obHdnCodConta->setName    ( "inCodigoConta"           );
$obHdnCodConta->setValue   ( $_REQUEST["inCodigoConta"]);

$obHdnCodAgencia = new Hidden;
$obHdnCodAgencia->setName    ( "inCodAgencia"           );
$obHdnCodAgencia->setValue   ( $_REQUEST["inCodAgencia"]);

$obHdnNumeroConta = new Hidden;
$obHdnNumeroConta->setName    ( "stNumeroConta"           );
$obHdnNumeroConta->setValue   ( $_REQUEST["stNumeroConta"]);

$obLblBanco = new Label ;
$obLblBanco->setRotulo    ( "Banco"                             );
$obLblBanco->setName      ( "labelBanco"                        );
$obLblBanco->setValue     ( $_REQUEST["inNumBanco"]." - ".$_REQUEST["stNomBanco"] );

$obLblAgencia = new Label ;
$obLblAgencia->setRotulo    ( "Agencia"                               );
$obLblAgencia->setName      ( "labelAgencia"                          );
$obLblAgencia->setValue     ( $_REQUEST["stNumAgencia"]." - ".$_REQUEST["stNomAgencia"] );

$obLblCCorrente = new Label ;
$obLblCCorrente->setRotulo    ( "Conta Corrente"     );
$obLblCCorrente->setName      ( "labelCCorrente"     );
$obLblCCorrente->setValue     ( $_REQUEST["stNumeroConta"] );

$obTxtConta = new TextBox ;
$obTxtConta->setRotulo    ( "Conta-Corrente"                         );
$obTxtConta->setName      ( "stNumeroConta"                          );
$obTxtConta->setValue     ( $_REQUEST["stNumeroConta"]               );
$obTxtConta->setTitle     ( "Número da conta-corrente            "   );
$obTxtConta->setSize      ( 20                                       );
$obTxtConta->setMaxLength ( 20                                       );
$obTxtConta->obEvento->setOnKeyPress( "return validar(event)" );
$obTxtConta->setNull      ( false                                    );

$obErro=$obRMONContaCorrente->listarTipoConta($rsTipoConta);

$obCmbTipoConta=new Select();
$obCmbTipoConta->setName        ("inCodTipoConta"           );
$obCmbTipoConta->setTitle       ("Tipo da conta-corrente"   );
$obCmbTipoConta->setRotulo      ("Tipo de Conta"            );
$obCmbTipoConta->setValue       ($_REQUEST["inCodTipoConta"]);
$obCmbTipoConta->setNull        (false                      );
$obCmbTipoConta->addOption      ("","Selecione"             );
$obCmbTipoConta->setCampoId     ("cod_tipo"                 );
$obCmbTipoConta->setCampoDesc   ("descricao"                );
$obCmbTipoConta->preencheCombo  ($rsTipoConta               );
$obCmbTipoConta->setStyle       ("width: 200px;"            );

$obTxtData = new Data ;
$obTxtData->setRotulo    ( "Data de Abertura"                       );
$obTxtData->setName      ( "dtDataCriacao"                          );
$obTxtData->setValue     ( $_REQUEST["dtDataCriacao"]               );
$obTxtData->setTitle     ( "Data de abertura da conta           "   );
$obTxtData->setNull      ( false                                    );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm );
$obFormulario->setAjuda      ( "UC-05.05.03" );
$obFormulario->addTitulo     ( "Dados para Conta-Corrente" );

$obFormulario->addHidden     ( $obHdnAcao );
$obFormulario->addHidden     ( $obHdnCtrl );

if ($_REQUEST['stAcao'] == "alterar") {
    $obFormulario->addHidden     ( $obHdnCodBanco       );
    $obFormulario->addHidden     ( $obHdnNumAgencia     );
    $obFormulario->addHidden     ( $obHdnCodConta       );
    $obFormulario->addHidden     ( $obHdnCodAgencia     );
    $obFormulario->addComponente ( $obLblBanco          );
    $obFormulario->addComponente ( $obLblAgencia        );
    $obFormulario->addComponente ( $obTxtConta          );
    $obFormulario->addComponente ( $obCmbTipoConta      );

    $obFormulario->addComponente ( $obTxtData           );
}

if ($_REQUEST['stAcao'] == "incluir") {
    $obIMontaAgencia->geraFormulario( $obFormulario );
    $obFormulario->addComponente ( $obTxtConta                   );
    $obFormulario->addComponente ( $obCmbTipoConta               );
    $obFormulario->addComponente ( $obTxtData                    );
}

if ($_REQUEST['stAcao'] == "incluir") {
    $obFormulario->Ok       ();
} else {
    $link = Sessao::read('link');
    $obFormulario->Cancelar ($pgList.'&pg='.$link["pg"].'&pos='.$link["pos"]);
}

$obFormulario->show();

if ($_REQUEST['stAcao'] == 'incluir') {
    $stJs .= 'f.inNumbanco.focus();';
} else {
    $stJs .= 'f.dtDataCriacao.focus();';
}
sistemaLegado::executaFrameOculto ( $stJs );
