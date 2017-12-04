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
    * Página de Formulario de Inclusao/Alteracao de Bancos

    * Data de Criação   : 13/10/2005

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Lizandro Kirst da Silva
    * @ignore

    * $Id: FMManterAgencia.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.05.02

*/

/*
$Log$
Revision 1.11  2007/02/06 10:43:23  cercato
Bug #8141#

Revision 1.10  2006/09/15 14:57:28  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_MON_COMPONENTES."ITextBoxSelectBanco.class.php" );
include_once ( CAM_GT_MON_NEGOCIO."RMONAgencia.class.php" );
include_once ( CAM_GT_MON_NEGOCIO."RMONBanco.class.php" );
include_once ( CAM_GA_CGM_NEGOCIO."RCGM.class.php"    );

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc

if ( empty( $_REQUEST['stAcao'] ) ) {
    $_REQUEST['stAcao'] = "incluir";
}

//Define o nome dos arquivos PHP
$stPrograma    = "ManterAgencia";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgForm        = "FM".$stPrograma.".php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJs          = "JS".$stPrograma.".js";
//$stCaminho   = CAM_GT_MON_INSTANCIAS."banco/";
include_once( $pgJs );

$obITextBoxSelectBanco = new ITextBoxSelectBanco;
$obRCGM = new RCGM;

if ($_REQUEST['stAcao'] == "alterar") {
    $obRCGM->setNumCGM($_REQUEST["inNumCGMAgencia"]);
    $obRCGM->consultar($rsCGM);
}
//DEFINICAO DOS COMPONENTES
//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgProc );
//$obForm->setTarget( $pgOcul );

$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $_REQUEST['stAcao']  );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName   ( "stCtrl" );
$obHdnCtrl->setValue  ( $_REQUEST['stCtrl']  );

$obHdnCodAgencia =  new Hidden;
$obHdnCodAgencia->setName   ( "inCodAgencia" );
$obHdnCodAgencia->setValue  ( $_REQUEST["inCodAgencia"]  );

$obHdnCodBanco =  new Hidden;
$obHdnCodBanco->setName   ( "inCodBanco" );
$obHdnCodBanco->setValue  ( $_REQUEST["inCodBanco"]  );

$obHdnNumCGMAgencia = new Hidden;
$obHdnNumCGMAgencia->setName    ( "inNumCGMAgencia"                          );
$obHdnNumCGMAgencia->setValue   ( $_REQUEST["inNumCGMAgencia"]               );

$stNumAgencia = "";
Sessao::write( "stNumAgencia", $stNumAgencia );

$obTxtNumAgencia = new TextBox ;
$obTxtNumAgencia->setRotulo    ( "Número da Agência" );
$obTxtNumAgencia->setName      ( "stNumAgencia"      );
$obTxtNumAgencia->setValue     ( $_REQUEST["stNumAgencia"] );
$obTxtNumAgencia->setTitle     ( "Número da Agencia" );
$obTxtNumAgencia->setInteiro   ( false                );
$obTxtNumAgencia->setSize      ( 10                  );
$obTxtNumAgencia->setMaxLength ( 10                  );
$obTxtNumAgencia->setNull      ( false               );
$obTxtNumAgencia->obEvento->setOnKeyPress( "return validar(event)" );

$obLblBanco = new Label ;
$obLblBanco->setRotulo    ( "Banco" );
$obLblBanco->setName      ( "labelBanco");
$obLblBanco->setValue     ( $_REQUEST["inNumBanco"]." - ".$_REQUEST["stNomBanco"] );

$obLblCGMAgencia = new Label ;
$obLblCGMAgencia->setRotulo    ( "CGM da Agência" );
$obLblCGMAgencia->setName      ( "labelCGMAgencia");
$obLblCGMAgencia->setValue     ( $_REQUEST["inNumCGMAgencia"]." - ".$obRCGM->getNomCGM() );
$obLblCGMAgencia->setTitle     ( "CGM da Agência" );

$obTxtNomAgencia = new TextBox ;
$obTxtNomAgencia->setRotulo    ( "Nome da Agência" );
$obTxtNomAgencia->setName      ( "stNomAgencia"    );
$obTxtNomAgencia->setValue     ( $_REQUEST["stNomAgencia"] );
$obTxtNomAgencia->setTitle     ( "Nome da Agência" );
$obTxtNomAgencia->setSize      ( 80                );
$obTxtNomAgencia->setMaxLength ( 80                );
$obTxtNomAgencia->setNull      ( false             );

$obTxtNomPessContatoAgencia = new TextBox ;
$obTxtNomPessContatoAgencia->setRotulo    ( "Pessoa de Contato"                      );
$obTxtNomPessContatoAgencia->setName      ( "stContato"                              );
$obTxtNomPessContatoAgencia->setValue     ( $_REQUEST["stContato"]                   );
$obTxtNomPessContatoAgencia->setTitle     ( "Nome da Pessoa de Contato na Agência"   );
$obTxtNomPessContatoAgencia->setSize      ( 80                                       );
$obTxtNomPessContatoAgencia->setMaxLength ( 80                                       );
$obTxtNomPessContatoAgencia->setNull      ( true                                     );

$obBscCGM = new BuscaInner;
$obBscCGM->setRotulo    ( "CGM da Agência"                            );
$obBscCGM->setTitle     ( "CGM da agência"                            );
$obBscCGM->setId        ( "inNomCGMAgencia"                           );
$obBscCGM->setNull      ( false                                       );
$obBscCGM->obCampoCod->setName  ( "inNumCGMAgencia"                   );
$obBscCGM->obCampoCod->setValue ( $_REQUEST["inNumCGMAgencia"]        );
//$obBscCGM->obCampoCod->obEvento->setOnChange("buscaValor('buscaCGM');");
$obBscCGM->setFuncaoBusca( "abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inNumCGMAgencia','inNomCGMAgencia','juridica','".Sessao::getId()."','800','550');" );
$obBscCGM->setValoresBusca(CAM_GA_CGM_POPUPS."cgm/OCProcurarCgm.php?".Sessao::getId()."&stTipoPessoa=J",$obForm->getName(),'juridica');

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm );
$obFormulario->setAjuda      ( "UC-05.05.02" );
$obFormulario->addTitulo     ( "Dados para Agência" );

$obFormulario->addHidden     ( $obHdnAcao );
$obFormulario->addHidden     ( $obHdnCtrl );

if ($_REQUEST['stAcao'] == "alterar") {
    $obFormulario->addHidden ($obHdnCodAgencia);
    $obFormulario->addHidden ($obHdnCodBanco);
    $obFormulario->addHidden ($obHdnNumCGMAgencia);
    $obFormulario->addComponente ( $obLblBanco      );
    $obFormulario->addComponente ( $obLblCGMAgencia );
    $obFormulario->addComponente ( $obTxtNumAgencia );
    $obFormulario->addComponente ( $obTxtNomAgencia                   );
    $obFormulario->addComponente ( $obTxtNomPessContatoAgencia        );
}

if ($_REQUEST['stAcao'] == "incluir") {
    $obFormulario->addComponente ( $obITextBoxSelectBanco );
    $obFormulario->addComponente ( $obTxtNumAgencia                   );
    $obFormulario->addComponente ( $obTxtNomAgencia                   );
    $obFormulario->addComponente ( $obBscCGM                          );
    $obFormulario->addComponente ( $obTxtNomPessContatoAgencia        );
}

if ($_REQUEST['stAcao'] == "incluir") {
    $obFormulario->Ok       ();
} else {
    $obFormulario->Cancelar ();
}

$obFormulario->show();

if ($_REQUEST['stAcao'] == 'incluir') {
    $stJs .= 'f.inNumbanco.focus();';
} else {
    $stJs .= 'f.stNumAgencia.focus();';
}
sistemaLegado::executaFrameOculto ( $stJs );
