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
    * Data de Criação: 29/11/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    $Id: FLManterManutencao.php 63735 2015-10-02 17:01:23Z evandro $

    * Casos de uso: uc-03.02.14
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GP_FRO_COMPONENTES."IPopUpItem.class.php" );
include_once( CAM_GF_ORC_COMPONENTES."ISelectMultiploEntidadeUsuario.class.php" );

$stPrograma = "ManterManutencao";
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

//monta a mascara para a autorizacao
$stMaxAutorizacao = SistemaLegado::pegaDado("cod_autorizacao","frota.autorizacao", " order by cod_autorizacao desc limit 1");
$stMascara =  str_pad('9',strlen($stMaxAutorizacao),'9',STR_PAD_LEFT).'/9999';

//instancia um textbox para a autorizacao
$obInCodAutorizacao = new TextBox();
$obInCodAutorizacao->setName( 'inCodAutorizacao' );
$obInCodAutorizacao->setId( 'inCodAutorizacao' );
$obInCodAutorizacao->setRotulo( 'Código da Autorização' );
$obInCodAutorizacao->setTitle( 'Informe o código da autorização de abastecimento.' );
$obInCodAutorizacao->obEvento->setOnChange( "montaParametrosGET('montaMascara','inCodAutorizacao');" );
$obInCodAutorizacao->setMascara( $stMascara );

//monta a mascara para a manutencao
$stMaxManutencao = SistemaLegado::pegaDado("cod_manutencao","frota.manutencao", " order by cod_manutencao desc limit 1");
$stMascara =  str_pad('9',strlen($stMaxManutencao),'9',STR_PAD_LEFT).'/9999';

//instancia um textbox para a manutencao
$obInCodManutencao = new TextBox();
$obInCodManutencao->setName( 'inCodManutencao' );
$obInCodManutencao->setId( 'inCodManutencao' );
$obInCodManutencao->setRotulo( 'Código da Manutenção' );
$obInCodManutencao->setTitle( 'Informe o código da manutenção do veículo.' );
$obInCodManutencao->obEvento->setOnChange( "montaParametrosGET('montaMascara','inCodManutencao');" );
$obInCodManutencao->setMascara( $stMascara );

$obInCodVeiculo = new Inteiro();
$obInCodVeiculo->setName( 'inCodVeiculo' );
$obInCodVeiculo->setRotulo( 'Código do Veículo' );
$obInCodVeiculo->setTitle( 'Informe o código do veículo.' );

//instancia um textbox para o numero da placa
$obTxtPlaca = new TextBox();
$obTxtPlaca->setRotulo( 'Placa do Veículo' );
$obTxtPlaca->setTitle ( 'Informe a placa do veículo.' );
$obTxtPlaca->setName  ( 'stNumPlaca' );
$obTxtPlaca->setId    ( 'stNumPlaca' );
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

//instancia a ipopupitem
$obIPopUpItem = new IPopUpItem( $obForm );
$obIPopUpItem->setNull( true );

//instancia o componente ISelectMultiploEntidadeUsuario
$obISelectMultiploEntidadeUsuario = new ISelectMultiploEntidadeUsuario();
$obISelectMultiploEntidadeUsuario->setNull( true );

//monta o formulário
$obFormulario = new Formulario;
$obFormulario->addForm      ( $obForm );
$obFormulario->addHidden    ( $obHdnAcao );
$obFormulario->addHidden    ( $obHdnCtrl );

$obFormulario->addTitulo    ( 'Dados do Filtro' );

$obFormulario->addComponente( $obInCodAutorizacao );
$obFormulario->addComponente( $obInCodManutencao );
$obFormulario->addComponente( $obInCodVeiculo );
$obFormulario->addComponente( $obTxtPlaca );
$obFormulario->addComponente( $obTxtPrefixo );
$obFormulario->addComponente( $obIPopUpItem );
$obFormulario->addComponente( $obISelectMultiploEntidadeUsuario );

$obFormulario->OK(true);

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
