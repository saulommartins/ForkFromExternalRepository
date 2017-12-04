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
    * Data de Criação: 16/10/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * @package URBEM
    * @subpackage

    $Id: FMManterBemSegurado.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-03.01.08

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GP_PAT_MAPEAMENTO.'TPatrimonioApolice.class.php' );
include_once( CAM_GP_PAT_COMPONENTES.'IIntervaloPopUpBem.class.php' );

$stPrograma = "ManterBemSegurado";
$pgFilt   = "FL".$stPrograma.".php";
$pgList   = "LS".$stPrograma.".php";
$pgForm   = "FM".$stPrograma.".php";
$pgProc   = "PR".$stPrograma.".php";
$pgOcul   = "OC".$stPrograma.".php";
$pgJs     = "JS".$stPrograma.".js";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

Sessao::remove('bens');
Sessao::remove('bensExcluidos');

//cria um novo formulario
$obForm = new Form;
$obForm->setAction ($pgProc);
$obForm->setTarget('oculto');

//Cria o hidden da acao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ("stAcao");
$obHdnAcao->setValue($stAcao);

//cria a acao de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ("stCtrl" );
$obHdnCtrl->setValue("");

//recupera as seguradoras
$obTPatrimonioApolice = new TPatrimonioApolice();
$obTPatrimonioApolice->recuperaSeguradoras( $rsSeguradoras, 'ORDER BY nom_seguradora' );

//instancia um select para as seguradoras
$obSelectSeguradora = new Select();
$obSelectSeguradora->setName( 'inCodSeguradora' );
$obSelectSeguradora->setRotulo( 'Seguradora' );
$obSelectSeguradora->setTitle( 'Seleciona a seguradora.' );
$obSelectSeguradora->addOption( '','Selecione' );
$obSelectSeguradora->setCampoId( 'num_seguradora' );
$obSelectSeguradora->setCampoDesc( 'nom_seguradora' );
$obSelectSeguradora->preencheCombo( $rsSeguradoras );
$obSelectSeguradora->obEvento->setOnChange( "montaParametrosGET( 'preencheApolice', 'inCodSeguradora' );" );
$obSelectSeguradora->setValue( 2/*$_REQUEST['inCodSeguradora']*/ );
$obSelectSeguradora->setNull( false );

//instancia um select para as apolices
$obSelectApolice = new Select();
$obSelectApolice->setName( 'inCodApolice' );
$obSelectApolice->setId( 'inCodApolice' );
$obSelectApolice->setRotulo( 'Apólice' );
$obSelectApolice->setTitle( 'Selecione a apólice.' );
$obSelectApolice->addOption( '','Selecione' );
$obSelectApolice->obEvento->setOnChange( "montaParametrosGET( 'preencheLista','inCodApolice' );" );
$obSelectApolice->setNull( false );

//instancia o componente iintervalorpopupbem
$obIIntervaloPopUpBem = new IIntervaloPopUpBem( $obForm );
$obIIntervaloPopUpBem->setObrigatorioBarra( true );

//instancia um componente textbox para o numero da placa
$obTxtNumPlaca = new TextBox();
$obTxtNumPlaca->setName( 'stNumPlaca' );
$obTxtNumPlaca->setId( 'stNumPlaca' );
$obTxtNumPlaca->setRotulo( 'Número da Placa' );
$obTxtNumPlaca->setTitle( 'Informe o número da placa.' );
$obTxtNumPlaca->obEvento->setOnChange( "montaParametrosGET( 'preencheBemPlaca', 'stNumPlaca' );" );
$obTxtNumPlaca->setSize( '20' );

//cria os botões de acoes para os bens
$obBtnOk = new Ok;
$obBtnOk->setName ( "btnOk" );
$obBtnOk->setValue( "Incluir" );
$obBtnOk->setTipo ( "button" );
$obBtnOk->obEvento->setOnClick( "montaParametrosGET( 'incluirBem' );" );

$obBtnLimpar = new Button;
$obBtnLimpar->setName ( "btnOk" );
$obBtnLimpar->setValue(  "Limpar" );
//$obBtnLimpar->obEvento->setOnClick( "LimparCodigos();" );

//instancia um span para os bens da apolice
$obSpnBem = new Span();
$obSpnBem->setId( 'spnBem' );

//monta o formulário
$obFormulario = new Formulario;
$obFormulario->setAjuda     ('UC-03.01.08');
$obFormulario->addForm      ( $obForm );
$obFormulario->addHidden    ( $obHdnAcao );
$obFormulario->addHidden    ( $obHdnCtrl );

$obFormulario->addTitulo    ( 'Dados da Apólice' );
$obFormulario->addComponente( $obSelectSeguradora );
$obFormulario->addComponente( $obSelectApolice );
$obFormulario->addTitulo    ( 'Bem' );
$obFormulario->addComponente( $obIIntervaloPopUpBem );
$obFormulario->addComponente( $obTxtNumPlaca );

$obFormulario->defineBarra( array( $obBtnOk, $obBtnLimpar ) );

$obFormulario->addSpan      ( $obSpnBem );

$obFormulario->OK();

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
