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
    * Página de Filtro Historico Padrão
    * Data de Criação   : 16/11/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    * $Id: FLManterHistorico.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.02.03
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GF_CONT_NEGOCIO."RContabilidadeHistoricoPadrao.class.php");

$stPrograma = "ManterHistorico";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

//***********************************************/
// Limpa a variavel de sessão para o filtro
//***********************************************/

Sessao::remove('link');
Sessao::write('filtro', array());
Sessao::write('pg', '');
Sessao::write('pos', '');
Sessao::write('paginando', false);

//sessao->transf4 = array( 'filtro' => array(), 'pg' => '' , 'pos' => '', 'paginando' => false );

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obTxtCodHistorico = new TextBox;
$obTxtCodHistorico->setRotulo        ( "Código" );
$obTxtCodHistorico->setName          ( "inCodHistorico" );
$obTxtCodHistorico->setTitle         ( "Informe um código" );
$obTxtCodHistorico->setValue         ( $inCodHistorico );
$obTxtCodHistorico->setSize          ( 10 );
$obTxtCodHistorico->setMaxLength     ( 10 );
$obTxtCodHistorico->setInteiro       ( true  );

$obTxtNomHistorico = new TextBox;
$obTxtNomHistorico->setRotulo        ( "Descrição" );
$obTxtNomHistorico->setName          ( "stNomHistorico" );
$obTxtCodHistorico->setTitle         ( "Informe uma descrição" );
$obTxtNomHistorico->setValue         ( $stNomHistorico );
$obTxtNomHistorico->setSize          ( 80 );
$obTxtNomHistorico->setMaxLength     ( 80 );

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction                  ( $pgList );
$obForm->setTarget                  ( "telaPrincipal" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->setAjuda('UC-02.02.03');
$obFormulario->addForm              ( $obForm );
$obFormulario->addHidden            ( $obHdnAcao );
$obFormulario->addHidden            ( $obHdnCtrl );

$obFormulario->addTitulo            ( "Dados para Filtro" );
$obFormulario->addComponente        ( $obTxtCodHistorico  );
$obFormulario->addComponente        ( $obTxtNomHistorico  );

$obFormulario->OK                   ();
$obFormulario->show                 ();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
