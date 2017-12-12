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
    * Data de Criação: 26/11/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    $Id: FLManterAutorizacao.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-03.02.13
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GP_FRO_COMPONENTES."IPopUpVeiculo.class.php" );
include_once( CAM_GF_ORC_COMPONENTES."ISelectMultiploEntidadeUsuario.class.php" );

$stPrograma = "ManterAutorizacao";
$pgFilt   = "FL".$stPrograma.".php";
$pgList   = "LS".$stPrograma.".php";
$pgForm   = "FM".$stPrograma.".php";
$pgProc   = "PR".$stPrograma.".php";
$pgOcul   = "OC".$stPrograma.".php";
$pgJs     = "JS".$stPrograma.".js";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

//cria um novo formulario
$obForm = new Form;
$obForm->setAction ($pgList);

//Cria o hidden da acao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ("stAcao");
$obHdnAcao->setValue($stAcao);

//cria a acao de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ("stCtrl" );
$obHdnCtrl->setValue("");

//instancia um textbox para o codigo da autorizacao
$obInCodAutorizacao = new TextBox();
$obInCodAutorizacao->setName( 'inCodAutorizacao' );
$obInCodAutorizacao->setId( 'inCodAutorizacao' );
$obInCodAutorizacao->setRotulo( 'Autorização' );
$obInCodAutorizacao->setTitle( 'Informe o código da autorização.' );

//instancia o componente IPopUpVeiculo
$obIPopUpVeiculo = new IPopUpVeiculo($obForm);
$obIPopUpVeiculo->setNull( true );

//instancia um textbox para o numero da placa
$obTxtPlaca = new TextBox();
$obTxtPlaca->setRotulo( 'Placa do Veículo' );
$obTxtPlaca->setTitle ( 'Informe a placa do veículo.' );
$obTxtPlaca->setName  ( 'stNumPlaca' );
$obTxtPlaca->setId    ( 'stNumPlaca' );
$obTxtPlaca->setMaxLength (8);
$obTxtPlaca->obEvento->setOnKeyUp( "mascaraPlacaVeiculo(this);" );
$obTxtPlaca->obEvento->setOnBlur( "mascaraPlacaVeiculo(this);" );

//instancia textbox para o prefixo
$obTxtPrefixo = new TextBox();
$obTxtPrefixo->setRotulo( 'Prefixo' );
$obTxtPrefixo->setTitle ( 'Informe prefixo do veículo.' );
$obTxtPrefixo->setName  ( 'stPrefixo' );
$obTxtPrefixo->setId    ( 'stPrefixo' );
$obTxtPrefixo->setSize  ( 15 );
$obTxtPrefixo->setMaxLength( 15 );

//instancia periodicidade
$obPeriodicidade = new Periodicidade( $obForm );
$obPeriodicidade->setExercicio( Sessao::getExercicio() );

//instancia o componente ISelectMultiploEntidadeUsuario
$obISelectMultiploEntidadeUsuario = new ISelectMultiploEntidadeUsuario();
$obISelectMultiploEntidadeUsuario->setNull( true );

$obRdoUmaVia = new Radio();
$obRdoUmaVia->setName( "boVias" );
$obRdoUmaVia->setId( "boVias" );
$obRdoUmaVia->setRotulo( "Vias por página" );
$obRdoUmaVia->setValue ( "false" );
$obRdoUmaVia->setLabel( "Uma" );

$obRdoDuasVias = new Radio();
$obRdoDuasVias->setName( "boVias" );
$obRdoDuasVias->setId( "boVias" );
$obRdoDuasVias->setValue( "true" );
$obRdoDuasVias->setLabel( "Duas" );
$obRdoDuasVias->setChecked( true );

//monta o formulário
$obFormulario = new Formulario;
$obFormulario->addForm      ( $obForm );
$obFormulario->addHidden    ( $obHdnAcao );
$obFormulario->addHidden    ( $obHdnCtrl );

$obFormulario->addTitulo    ( 'Dados do Filtro' );

$obFormulario->addComponente( $obInCodAutorizacao );
//$obFormulario->addComponente( $obExercicio );
$obFormulario->addComponente( $obIPopUpVeiculo );
$obFormulario->addComponente( $obTxtPlaca );
$obFormulario->addComponente( $obTxtPrefixo );
$obFormulario->addComponente( $obPeriodicidade );
$obFormulario->addComponente( $obISelectMultiploEntidadeUsuario );
if ($_REQUEST['acao'] == 1429) {
    $obFormulario->agrupaComponentes( array( $obRdoUmaVia, $obRdoDuasVias ) );
}
$obFormulario->OK();

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
