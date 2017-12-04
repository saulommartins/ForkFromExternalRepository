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
    * Formulario para Cadastro Economico >> Conceder Outras Licenças
    * Data de Criação   : 22/04/2005
    * @author Analista      : Fábio Bertoldi Rodrigues
    * @author Desenvolvedor : Lucas Teixeira Stephanou
    * @package URBEM
    * @subpackage Regra

    * $Id: FMConcederLicencaGeral.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.02.12

*/

/*
$Log$
Revision 1.7  2006/09/15 14:33:14  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CEM_NEGOCIO."RCEMTipoLicencaDiversa.class.php" );

//Define o nome dos arquivos PHP
$stPrograma     = "ConcederLicencaGeral";
$pgFilt         = "FL".$stPrograma.".php"       ;
$pgList         = "LS".$stPrograma.".php"       ;
$pgForm         = "FM".$stPrograma.".php"       ;
$pgFormTipo     = "FM".$stPrograma."Tipo.php"   ;
$pgFormTipoSolo = "FM".$stPrograma."TipoSolo.php";
$pgProc         = "PR".$stPrograma."2.php"       ;
$pgOcul         = "OC".$stPrograma.".php"       ;
$pgJs           = "JS".$stPrograma.".js"        ;
include_once( $pgJs );

//$jsOnload = "executaFuncaoAjax('selecionaFormulario');";

// OBJETOS HIDDEN
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName                      ( "stCtrl"                      );
$obHdnCtrl->setValue                     ( $request->get("stCtrl")       );

$obHdnAcao = new Hidden;
$obHdnAcao->setName                      ( "stAcao"                      );
$obHdnAcao->setValue                     ( $request->get("stAcao")       );

//DEFINIÇÃO DOS COMPONENTES
$obTxtLicenca   = new TextBox;
$obTxtLicenca->setName  ( "inCodigoTipoLicenca"     );
$obTxtLicenca->setId    ( "inCodigoTipoLicenca"     );
$obTxtLicenca->setTitle ( "Tipo de Licença"         );
$obTxtLicenca->setRotulo( "Tipo de Licença"         );
$obTxtLicenca->setNull  ( false                 );

$obRCEMTipoLicenca =  new RCEMTipoLicencaDiversa;
$obRCEMTipoLicenca->listarTipoLicencaDiversa($rsTiposLicenca);

$Span = new span;
$Span->setId("Formularios");

$obCmbTiposLicenca  = new Select;
$obCmbTiposLicenca->setName         ( "cmbTiposLicenca"                    );
$obCmbTiposLicenca->addOption       ( "", "Selecione"                      );
$obCmbTiposLicenca->setTitle        ( "Tipo de Licença"                    );
$obCmbTiposLicenca->setCampoId      ( "cod_tipo"                           );
$obCmbTiposLicenca->setCampoDesc    ( "nom_tipo"                           );
$obCmbTiposLicenca->preencheCombo   ( $rsTiposLicenca                      );
$obCmbTiposLicenca->setValue        ( $request->get("inCodTipo")					 );
$obCmbTiposLicenca->setNull         ( false                                );
$obCmbTiposLicenca->setStyle        ( "width: 220px"                       );
$obCmbTiposLicenca->setId           ("cmbTiposLicenca");
$obCmbTiposLicenca->obEvento->setOnChange("JavaScript:document.frm.submit()" );

$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "telaPrincipal" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm   ( $obForm                        );
$obFormulario->addTitulo ( "Dados para Licença");
//$obFormulario->setAjuda      ( "UC-05.02.12");
$obFormulario->addHidden ( $obHdnCtrl                     );
$obFormulario->addHidden ( $obHdnAcao                     );
$obFormulario->addSpan   ($Span);
$obFormulario->addComponenteComposto($obTxtLicenca,$obCmbTiposLicenca);
//$obFormulario->ok();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
