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
  * Popup de busca de desoneração
  * Data de criação : 08/06/2005

  * @author Analista: Fábio Bertoldi
  * @author Programador: tonismar R. Bernardo

  * @ignore

    * $Id: FLProcurarDesoneracao.php 59612 2014-09-02 12:00:51Z gelson $

  Caso de uso: uc-05.03.04
**/

/*
$Log$
Revision 1.5  2006/09/15 11:51:30  fabio
corrigidas tags de caso de uso

Revision 1.4  2006/09/15 10:49:17  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_ARR_NEGOCIO."RARRDesoneracao.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ProcurarDesoneracao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$obRARRDesoneracao = new RARRDesoneracao;
$obRARRDesoneracao->listarTipoDesoneracao( $rsTipo );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $_REQUEST["stAcao"] );

//Define o objeto HIDDEN para armazenar variavel de controle (stCtrl)
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnCampoNom = new Hidden;
$obHdnCampoNom->setName( "campoNom" );
$obHdnCampoNom->setValue( $_REQUEST["campoNom"] );

$obHdnCampoNum = new Hidden;
$obHdnCampoNum->setName( "campoNum" );
$obHdnCampoNum->setValue( $_REQUEST["campoNum"] );

$obTxtCodigoDesoneracao = new TextBox;
$obTxtCodigoDesoneracao->setName        ( "inCodigoDesoneracao" );
$obTxtCodigoDesoneracao->setTitle       ( "Informe o código da desoneração." );
$obTxtCodigoDesoneracao->setRotulo      ( "Código"              );
$obTxtCodigoDesoneracao->setMaxLength   ( 7                     );
$obTxtCodigoDesoneracao->setSize        ( 7                     );
$obTxtCodigoDesoneracao->setValue       ( $_REQUEST["inCodigoDesoneracao"]  );
$obTxtCodigoDesoneracao->setInteiro     ( true                  );

$obTxtTipo = new TextBox;
$obTxtTipo->setName               ( "inCodigoTipo" );
$obTxtTipo->setRotulo             ( "Tipo de Desoneração" );
$obTxtTipo->setTitle              ( "Selecione o tipo da desoneração." );
$obTxtTipo->setMaxLength          ( 7                     );
$obTxtTipo->setSize               ( 7                     );
$obTxtTipo->setValue              ( $_REQUEST["inCodigoTipo"]         );
$obTxtTipo->setInteiro            ( true                  );

$obCmbTipo = new Select;
$obCmbTipo->setName               ( "stTipo"               );
$obCmbTipo->setTitle              ( "Selecione o tipo da desoneração." );
$obCmbTipo->setRotulo             ( "Tipo de desoneração"  );
$obCmbTipo->setCampoId            ( "cod_tipo_desoneracao" );
$obCmbTipo->setCampoDesc          ( "descricao"            );
$obCmbTipo->addOption             ( "", "Selecione"        );
$obCmbTipo->preencheCombo         ( $rsTipo                );

$obTxtCredito = new TextBox;
$obTxtCredito->setName               ( "stDescricao"        );
$obTxtCredito->setRotulo             ( "Crédito"            );
$obTxtCredito->setTitle              ( "Informe o crédito da desoneração." );
$obTxtCredito->setValue              ( $_REQUEST["stDescricao"]         );
$obTxtCredito->setSize               ( 40                   );
$obTxtCredito->setMaxLength          ( 40                   );

$obBtnOK = new OK;
$obBtnLimpar = new Limpar;

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgList  );

$obFormulario = new Formulario;
$obFormulario->addForm ( $obForm );
$obFormulario->addHidden ( $obHdnCtrl );
$obFormulario->addHidden ( $obHdnAcao );
$obFormulario->addHidden ( $obHdnCampoNom );
$obFormulario->addHidden ( $obHdnCampoNum );
$obFormulario->addTitulo ( "Dados para Filtro" );
$obFormulario->addComponente( $obTxtCodigoDesoneracao );
$obFormulario->addComponenteComposto( $obTxtTipo, $obCmbTipo );
$obFormulario->addComponente( $obTxtCredito );
$obFormulario->defineBarra( array ( $obBtnOK, $obBtnLimpar ) );
$obFormulario->Show();
