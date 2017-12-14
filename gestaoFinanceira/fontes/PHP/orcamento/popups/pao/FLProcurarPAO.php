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
    * Popup de busca do PAO
    * Data de Criação: 11/07/2007

    * @author Analista: Dagiane Vieira
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 31000 $
    $Name$
    $Author: souzadl $
    $Date: 2007-07-17 11:49:55 -0300 (Ter, 17 Jul 2007) $

    * Casos de uso: uc-02.01.03
*/

/*
$Log$
Revision 1.1  2007/07/17 14:49:34  souzadl
construção

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "ProcurarPAO";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

//include_once( $pgJS );

//destroi arrays de sessao que armazenam os dados do FILTRO
Sessao::remove('filtroPopUp');
Sessao::remove('pg');
Sessao::remove('pos');
Sessao::remove('paginando');
Sessao::remove('link');
//sessao->transf4 = array( 'filtro' => array(), 'pg' => '' , 'pos' => '', 'paginando' => false );

//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgList );

//Define HIDDEN com código do logradouro
$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $_GET['stAcao'] );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnForm = new Hidden;
$obHdnForm->setName( "nomForm" );
$obHdnForm->setValue( $_REQUEST["nomForm"] );

$obHdnCampoNum = new Hidden;
$obHdnCampoNum->setName( "campoNum" );
$obHdnCampoNum->setValue( $_REQUEST["campoNum"] );

//Define HIDDEN com o o nome do campo texto
$obHdnCampoNom = new Hidden;
$obHdnCampoNom->setName( "campoNom" );
$obHdnCampoNom->setValue( $_REQUEST["campoNom"] );

$obHdnExtensao = new Hidden;
$obHdnExtensao->setName                     ( "stExtensao" );
$obHdnExtensao->setValue                    ( $_REQUEST["stExtensao"] );

//Definição das Caixas de Texto
$obTxtNumeroPAO = new Inteiro();
$obTxtNumeroPAO->setName( "stNumPAO" );
$obTxtNumeroPAO->setRotulo( "Número PAO" );
$obTxtNumeroPAO->setSize( 10 );

$obTxtNomePAO = new TextBox;
$obTxtNomePAO->setName( "stNomPAO" );
$obTxtNomePAO->setRotulo( "Nome PAO" );
$obTxtNomePAO->setSize( 60 );
$obTxtNomePAO->setMaxLength( 80 );

$obHdnExercicio = new Hidden;
$obHdnExercicio->setName( "inExercicio" );
$obHdnExercicio->setValue( $_REQUEST["inExercicio"] );

//Criação do formulário
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnForm );
$obFormulario->addHidden( $obHdnCampoNum );
$obFormulario->addHidden( $obHdnCampoNom );
$obFormulario->addHidden( $obHdnExtensao );
$obFormulario->addHidden( $obHdnExercicio );
$obFormulario->addTitulo( "Busca de PAO" );
$obFormulario->addComponente( $obTxtNumeroPAO );
$obFormulario->addComponente( $obTxtNomePAO );
$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
