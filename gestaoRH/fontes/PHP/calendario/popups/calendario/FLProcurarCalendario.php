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
/*
    * Formulário para filtro para procura de calendário
    * Data de Criação   : 13/10/2008

    * @author Analista      Dagiane Vieira
    * @author Desenvolvedor Diego Lemos de Souza

    * @package URBEM
    * @subpackage

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "ProcurarCalendario";
$pgList = "LS".$stPrograma.".php";

//destroi arrays de sessao que armazenam os dados do FILTRO
Sessao::remove( "filtroRelatorio" );
Sessao::remove( "link" );

$campoNum            = $_REQUEST['campoNum'];
$campoNom            = $_REQUEST['campoNom'];

//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgList );
//$obForm->setTarget( "telaPrincipal" );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnForm = new Hidden;
$obHdnForm->setName( "nomForm" );
$obHdnForm->setValue( $_REQUEST["nomForm"] );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $_GET['stAcao'] );

//Define HIDDEN com o o nome do campo texto
$obHdnCampoNum =  new Hidden;
$obHdnCampoNum->setName                         ( "campoNum"                                            );
$obHdnCampoNum->setValue                        ( $campoNum                                             );

$obHdnCampoNom =  new Hidden;
$obHdnCampoNom->setName                         ( "campoNom"                                            );
$obHdnCampoNom->setValue                        ( $campoNom                                             );

//Definição das Caixas de Texto
$obTxtNomeCalendario = new TextBox;
$obTxtNomeCalendario->setName( "stNomeCalendario" );
$obTxtNomeCalendario->setRotulo( "Calendário" );
$obTxtNomeCalendario->setSize( 60 );
$obTxtNomeCalendario->setMaxLength( 60 );

//Criação do formulário
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnForm );
$obFormulario->addHidden( $obHdnCampoNom );
$obFormulario->addHidden( $obHdnCampoNum );
$obFormulario->addTitulo( "Dados do Filtro para a Calendário" );
$obFormulario->addComponente( $obTxtNomeCalendario );
$obFormulario->OK();
$obFormulario->show();
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
