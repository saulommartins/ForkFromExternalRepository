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
  * Arquivo de instância para busca de Escala
  * Data de Criação: 09/10/2008

  * @author Analista      Dagiane Vieira
  * @author Desenvolvedor Alex Cardoso

  * @package URBEM
  * @subpackage

  $Id: $

  * Casos de uso: uc-04.10.02

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

//DEFINE O NOME DOS ARQUIVOS PHP
$stPrograma  = "ProcurarEscala";
$pgFilt      = "FL".$stPrograma.".php";
$pgList      = "LS".$stPrograma.".php";
$pgForm      = "FM".$stPrograma.".php";
$pgProc      = "PR".$stPrograma.".php";
$pgOcul      = "OC".$stPrograma.".php";
$pgJs        = "JS".$stPrograma.".js";

$pgProx = $pgList;
Sessao::remove( "link" );

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

//DEFINICAO DOS COMPONENTES DO FORMULARIO
$obHdnAcao = new Hidden;
$obHdnAcao->setName                     ( "stAcao" );
$obHdnAcao->setValue                    ( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName                     ( "stCtrl" );
$obHdnCtrl->setValue                    ( "" );

$obHdnExtensao = new Hidden;
$obHdnExtensao->setName                 ( "stExtensao" );
$obHdnExtensao->setValue                ( $_REQUEST["stExtensao"] );

$obHdnCampoNum =  new Hidden;
$obHdnCampoNum->setName                 ( "campoNum"            );
$obHdnCampoNum->setValue                ( $_REQUEST["campoNum"] );

$obHdnCampoNom =  new Hidden;
$obHdnCampoNom->setName                 ( "campoNom"            );
$obHdnCampoNom->setValue                ( $_REQUEST["campoNom"] );

$obTxtCodigo = new TextBox();
$obTxtCodigo->setRotulo     			("Código da Escala"			     );
$obTxtCodigo->setTitle      			("Informe o código da escala." );
$obTxtCodigo->setName       			("inCodEscala"					 );
$obTxtCodigo->setMaxLength  			( 20							 );
$obTxtCodigo->setSize       			( 10							 );
$obTxtCodigo->setMaxLength  			( "5"                            );
$obTxtCodigo->setMascara				( "99999"                        );

$obTxtFiltro = new TextBox;
$obTxtFiltro->setRotulo                 ( "Descrição"        );
$obTxtFiltro->setTitle                  ( "Informe a descrição da escala." );
$obTxtFiltro->setName                   ( "stDescricao"      );
$obTxtFiltro->setValue                  ( $stDescricao       );
$obTxtFiltro->setSize                   ( 80 );
$obTxtFiltro->setMaxLength              ( 80 );
$obTxtFiltro->setInteiro                ( false );

//INSTANCIA DO FORMULARIO
$obForm = new Form;
$obForm->setAction( $pgList );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm                  ( $obForm             );
$obFormulario->addHidden                ( $obHdnAcao          );
$obFormulario->addHidden                ( $obHdnCtrl          );
$obFormulario->addHidden                ( $obHdnExtensao      );
$obFormulario->addHidden                ( $obHdnCampoNum      );
$obFormulario->addHidden                ( $obHdnCampoNom      );
$obFormulario->addTitulo                ( "Dados para Filtro" );
$obFormulario->addComponente            ( $obTxtCodigo        );
$obFormulario->addComponente            ( $obTxtFiltro        );
$obFormulario->OK                       ();
$obFormulario->show                     ();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
