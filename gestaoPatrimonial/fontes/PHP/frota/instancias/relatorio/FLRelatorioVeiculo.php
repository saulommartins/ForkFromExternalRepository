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
    * Data de Criação: 06/12/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * $Id: FLRelatorioVeiculo.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-03.02.10
*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GP_FRO_COMPONENTES."IPopUpVeiculo.class.php");
include_once( CAM_GF_ORC_COMPONENTES."ISelectMultiploEntidadeUsuario.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "RelatorioVeiculo";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgGera = "OCGera".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

//define os componentes do formulário

$obForm = new Form;
$obForm->setAction( $pgGera );

$obHdnCtrl   = new Hidden;
$obHdnCtrl->setName("stCtrl");
$obHdnCtrl->setValue( " " );

$obIPopUpVeiculo = new IPopUpVeiculo( $obForm );

//instancia um textbox para o numero da placa
$obTxtPlaca = new TextBox();
$obTxtPlaca->setRotulo( 'Placa do Veículo' );
$obTxtPlaca->setName  ( 'stNumPlaca' );
$obTxtPlaca->setId    ( 'stNumPlaca' );
$obTxtPlaca->setTitle ( 'Informe a placa do veículo.' );
$obTxtPlaca->obEvento->setOnKeyUp( "mascaraPlacaVeiculo(this);" );
$obTxtPlaca->obEvento->setOnBlur( "mascaraPlacaVeiculo(this);" );

//instancia textbox para o prefixo
$obTxtPrefixo = new TextBox();
$obTxtPrefixo->setRotulo( 'Prefixo' );
$obTxtPrefixo->setName  ( 'stPrefixo' );
$obTxtPrefixo->setId    ( 'stPrefixo' );
$obTxtPrefixo->setTitle ( 'Informe prefixo do veículo.' );
$obTxtPrefixo->setSize  ( 15 );
$obTxtPrefixo->setMaxLength( 15 );

//Define o objeto SelectMultiplo para armazenar a ordenação
$obCmbOrdenacao = new Select();
$obCmbOrdenacao->setName   ('inCodOrdenacao');
$obCmbOrdenacao->setValue  ($inCodOrdenacao );
$obCmbOrdenacao->setStyle  ( "width: 270px" );
$obCmbOrdenacao->setRotulo ( "Ordenação"    );
$obCmbOrdenacao->setTitle  ( "Selecione a Ordenação." );
$obCmbOrdenacao->setNull   ( false          );
$obCmbOrdenacao->addOption ("1", "Placa");
$obCmbOrdenacao->addOption ("2", "Código do Veículo" );

//Radios de veículos baixados
$obRdbTodos = new Radio;
$obRdbTodos->setRotulo ( "Veículos Baixados" );
$obRdbTodos->setName   ( "inCodVeiculoBaixado" );
$obRdbTodos->setValue  ( "1" );
$obRdbTodos->setTitle  ( "Selecione se o veículo der baixa." );
$obRdbTodos->setLabel  ( "Todos" );
$obRdbTodos->setNull   ( false      );

$obRdbSim = new Radio;
$obRdbSim->setName   ( "inCodVeiculoBaixado" );
$obRdbSim->setValue  ( "2" );
$obRdbSim->setLabel  ( "Sim" );

$obRdbNao = new Radio;
$obRdbNao->setName   ( "inCodVeiculoBaixado" );
$obRdbNao->setValue  ( "3" );
$obRdbNao->setLabel  ( "Não" );
$obRdbNao->setChecked (true);

//instancia o componente ISelectMultiploEntidadeUsuario
$obISelectMultiploEntidadeUsuario = new ISelectMultiploEntidadeUsuario();
$obISelectMultiploEntidadeUsuario->setNull( true );

//define o formulário

$obFormulario = new Formulario;
$obFormulario->addForm          ( $obForm             );
$obFormulario->setAjuda         ("UC-03.02.10");
$obFormulario->addHidden        ( $obHdnCtrl          );
$obFormulario->addTitulo        ( "Dados de Filtro"   );

$obFormulario->addComponente    ( $obIPopUpVeiculo );
$obFormulario->addComponente    ( $obTxtPlaca );
$obFormulario->addComponente    ( $obTxtPrefixo );
$obFormulario->addComponente    ( $obCmbOrdenacao      );
$obFormulario->addComponente    ( $obISelectMultiploEntidadeUsuario      );
$obFormulario->agrupaComponentes( array($obRdbTodos,$obRdbSim,$obRdbNao));

$obFormulario->OK();
$obFormulario->show();
