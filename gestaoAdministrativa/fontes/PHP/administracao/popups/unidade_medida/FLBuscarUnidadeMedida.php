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
 * Arquivo de popup para manutenção de unidades de medidas
 * Data de Criação: 26/08/2008

 *
 * @author Analista      : Heleno Menezes dos Santos
 * @author Desenvolvedor : Janilson Mendes P. da Silva

$Revision:
$Name$
$Author:  $
$Date: $

Casos de uso:
*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$stPrograma = "BuscarUnidadeMedida";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".php";

include_once( $pgJs );

$stAcao = $request->get('stAcao');

$stNomeUnidade = $_POST["stNomeUnidade"] ? $_POST["stNomeUnidade"] : $_GET["stNomeUnidade"];

$obTxtNomeUnidade = new TextBox;
$obTxtNomeUnidade->setRotulo( "Nome" );
$obTxtNomeUnidade->setName( "stNomeUnidade" );
$obTxtNomeUnidade->setTitle( "Nome da Unidade de Medida desejada." );
$obTxtNomeUnidade->setValue( $stNomeUnidade );
$obTxtNomeUnidade->setSize( 60 );
$obTxtNomeUnidade->setMaxLength( 60 );
$obTxtNomeUnidade->setNull( true );

$obHdnForm = new Hidden;
$obHdnForm->setName( 'nomForm' );
$obHdnForm->setValue( $_REQUEST['nomForm'] );

$obHdnCampoNum = new Hidden;
$obHdnCampoNum->setName( 'campoNum' );
$obHdnCampoNum->setValue( $_REQUEST['campoNum'] );

$obHdnCampoNom = new Hidden;
$obHdnCampoNom->setName( 'campoNom' );
$obHdnCampoNom->setValue( $_REQUEST['campoNom'] );

$obForm = new Form;
$obForm->setAction( $pgList );

$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addHidden( $obHdnForm );
$obFormulario->addHidden( $obHdnCampoNum );
$obFormulario->addHidden( $obHdnCampoNom );
$obFormulario->addTitulo( "Dados para Filtro" );
$obFormulario->addComponente( $obTxtNomeUnidade );
$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
