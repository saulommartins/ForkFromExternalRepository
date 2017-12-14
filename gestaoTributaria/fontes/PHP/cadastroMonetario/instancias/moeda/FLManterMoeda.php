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
    * Página de Formulario de Inclusao/Alteracao de MOEDA

    * Data de Criação   : 16/12/2005

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Diego Bueno Coelho
    * @ignore

    * $Id: FLManterMoeda.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.05.06

*/

/*
$Log$
Revision 1.7  2006/09/15 14:58:03  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_MON_NEGOCIO."RMONMoeda.class.php" );

if ( empty( $_REQUEST['stAcao'] ) ) {
    $_REQUEST['stAcao'] = "incluir";
}

//Define o nome dos arquivos PHP
$stPrograma    = "ManterMoeda";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgForm        = "FM".$stPrograma.".php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJs          = "JS".$stPrograma.".js";

include_once( $pgJs );
$obRMONMoeda = new RMONMoeda;

Sessao::remove('link');
Sessao::remove('stLink');

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $_REQUEST['stAcao'] );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName   ( "stCtrl" );
$obHdnCtrl->setValue  ( $_REQUEST['stCtrl'] );

$obHdnCodMoeda =  new Hidden;
$obHdnCodMoeda->setName   ( "inCodMoeda" );
$obHdnCodMoeda->setValue  ( $_REQUEST["inCodMoeda"]  );

$obTxtCodMoeda = new TextBox ;
$obTxtCodMoeda->setRotulo    ( "Código da Moeda" );
$obTxtCodMoeda->setName      ( "inCodMoeda");
$obTxtCodMoeda->setValue     ( $inCodMoeda );
$obTxtCodMoeda->setTitle     ( "Código da Moeda" );
$obTxtCodMoeda->setInteiro ( true );
$obTxtCodMoeda->setSize    ( 10 );
$obTxtCodMoeda->setMaxLength ( 10 );
$obTxtCodMoeda->setNull    ( true );

$obTxtDescSingular = new TextBox ;
$obTxtDescSingular->setRotulo    ( "Descrição no Singular" );
$obTxtDescSingular->setName      ( "stDescSingular");
$obTxtDescSingular->setValue     ( $stDescSingular );
$obTxtDescSingular->setTitle     ( "Descrição da moeda no singular" );
$obTxtDescSingular->setSize      ( 40 );
$obTxtDescSingular->setMaxLength ( 40 );
$obTxtDescSingular->setNull      ( true );

$obTxtDescPlural = new TextBox ;
$obTxtDescPlural->setRotulo    ( "Descrição no Plural" );
$obTxtDescPlural->setName      ( "stDescPlural");
$obTxtDescPlural->setValue     ( $stDescPlural );
$obTxtDescPlural->setTitle     ( "Descrição da moeda no plural" );
$obTxtDescPlural->setSize      ( 40 );
$obTxtDescPlural->setMaxLength ( 40 );
$obTxtDescPlural->setNull      ( true );

$obTxtFracaoSingular = new TextBox ;
$obTxtFracaoSingular->setRotulo    ( "Fração no Singular" );
$obTxtFracaoSingular->setName      ( "stFracaoSingular");
$obTxtFracaoSingular->setValue     ( $stFracaoSingular );
$obTxtFracaoSingular->setTitle     ( "Fração da moeda no singular" );
$obTxtFracaoSingular->setSize      ( 40 );
$obTxtFracaoSingular->setMaxLength ( 40 );
$obTxtFracaoSingular->setNull      ( true );

$obTxtFracaoPlural = new TextBox ;
$obTxtFracaoPlural->setRotulo    ( "Fração no Plural" );
$obTxtFracaoPlural->setName      ( "stFracaoPlural");
$obTxtFracaoPlural->setValue     ( $stFracaoPlural );
$obTxtFracaoPlural->setTitle     ( "Fração da moeda no plural" );
$obTxtFracaoPlural->setSize      ( 40 );
$obTxtFracaoPlural->setMaxLength ( 40 );
$obTxtFracaoPlural->setNull      ( true );

$obTxtSimbolo = new TextBox ;
$obTxtSimbolo->setRotulo    ( "Símbolo da Moeda" );
$obTxtSimbolo->setName      ( "stSimbolo");
$obTxtSimbolo->setValue     ( $stSimbolo );
$obTxtSimbolo->setTitle     ( "Símbolo da Moeda" );
$obTxtSimbolo->setSize      ( 4 );
$obTxtSimbolo->setMaxLength ( 4 );
$obTxtSimbolo->setNull      ( true );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgList );
//$obForm->setTarget( $pgOcul );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm );
$obFormulario->setAjuda ( "UC-05.05.06" );
$obFormulario->addTitulo     ( "Dados para Filtro" );

$obFormulario->addHidden ( $obHdnAcao );
$obFormulario->addHidden ( $obHdnCtrl );

$obFormulario->addComponente ( $obTxtCodMoeda );
$obFormulario->addComponente ( $obTxtDescSingular );
//$obFormulario->addComponente ( $obTxtDescPlural );
$obFormulario->addComponente ( $obTxtFracaoSingular );
//$obFormulario->addComponente ( $obTxtFracaoPlural );
$obFormulario->addComponente ( $obTxtSimbolo );

$obFormulario->ok();
$obFormulario->show ();

$stJs .= 'f.inCodMoeda.focus();';
sistemaLegado::executaFrameOculto ( $stJs );
