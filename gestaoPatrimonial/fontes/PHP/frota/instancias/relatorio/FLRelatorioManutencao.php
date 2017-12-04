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
    * Data de Criação: 10/12/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * $Id: FLRelatorioManutencao.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-03.02.17
*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once (CAM_GP_FRO_COMPONENTES."ISelectModeloVeiculo.class.php" );
include_once( CAM_GP_FRO_MAPEAMENTO.'TFrotaTipoVeiculo.class.php' );
include_once( CAM_GP_FRO_MAPEAMENTO.'TFrotaCombustivel.class.php' );
include_once( CAM_GF_ORC_COMPONENTES."ISelectMultiploEntidadeUsuario.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "RelatorioManutencao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgGera = "OCGera".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

//define os componentes do formulário

$obForm = new Form;
$obForm->setAction( $pgGera );
//$obForm->setTarget( "oculto" );

$obHdnCtrl   = new Hidden;
$obHdnCtrl->setName("stCtrl");
$obHdnCtrl->setValue( " " );

// Define Objeto TextBox para o código do veículo
$obTxtCodVeiculo = new TextBox;
$obTxtCodVeiculo->setRotulo    ( "Código do Veículo"           );
$obTxtCodVeiculo->setTitle     ( "Informe o código do veículo." );
$obTxtCodVeiculo->setName      ( "inCodVeiculo"                );
$obTxtCodVeiculo->setValue     ( $stCodVeiculo                 );
$obTxtCodVeiculo->setMaxLength ( 8                             );
$obTxtCodVeiculo->setSize      ( 8                             );
$obTxtCodVeiculo->setInteiro   ( true                          );

// Define Objeto TextBox para o Prefixo
$obTxtPrefixo = new TextBox;
$obTxtPrefixo->setRotulo    ( "Prefixo"                      );
$obTxtPrefixo->setTitle     ( "Informe o prefixo do veículo." );
$obTxtPrefixo->setName      ( "stPrefixo"                    );
$obTxtPrefixo->setValue     ( $stPrefixo                     );
$obTxtPrefixo->setMaxLength ( 8                              );
$obTxtPrefixo->setSize      ( 8                              );

// Define Objeto TextBox para a placa do Veículo
$obTxtPlaca = new TextBox;
$obTxtPlaca->setRotulo    ( "Placa do Veículo"             );
$obTxtPlaca->setTitle     ( "Informe a placa do veículo."   );
$obTxtPlaca->setName      ( "stPlaca"               );
$obTxtPlaca->setValue     ( $stPlaca                );
$obTxtPlaca->setMaxLength ( 8                              );
$obTxtPlaca->setSize      ( 8                              );
$obTxtPlaca->obEvento->setOnKeyUp ("mascaraPlacaVeiculo(this);");
$obTxtPlaca->obEvento->setOnBlur ("verificaPlacaVeiculo(this);");

$obISelectModeloVeiculo = new ISelectModeloVeiculo($obForm);
$obISelectModeloVeiculo->setNull( true );

//recupera os tipos de veículo
$obTFrotaTipoVeiculo = new TFrotaTipoVeiculo();
$obTFrotaTipoVeiculo->recuperaTodos( $rsTipoVeiculo, ' ORDER BY nom_tipo' );

//cria um select para os tipos de veiculo
$obSelectTipoVeiculo = new Select();
$obSelectTipoVeiculo->setName( 'slTipoVeiculo' );
$obSelectTipoVeiculo->setId( 'slTipoVeiculo' );
$obSelectTipoVeiculo->setRotulo( 'Tipo de Veículo' );
$obSelectTipoVeiculo->setTitle( 'Selecione o tipo de veículo.' );
$obSelectTipoVeiculo->setCampoId( 'cod_tipo' );
$obSelectTipoVeiculo->setCampoDesc( 'nom_tipo' );
$obSelectTipoVeiculo->addOption( '', 'Selecione' );
$obSelectTipoVeiculo->preencheCombo( $rsTipoVeiculo );

//recupera os combustiveis
$obTFrotaCombustivel = new TFrotaCombustivel();
$obTFrotaCombustivel->recuperaTodos( $rsCombustivel, ' ORDER BY nom_combustivel' );

//instancia um select multiplo para os tipo do combustível
$obISelectMultiploCombustivel = new SelectMultiplo();
$obISelectMultiploCombustivel->setName   ('inCodCombustivel');
$obISelectMultiploCombustivel->setRotulo ( "Tipo de Combustível" );
$obISelectMultiploCombustivel->setNull   ( true );
$obISelectMultiploCombustivel->setTitle  ( "Selecione o tipo de combustível do veículo." );

//seta os combustiveis disponiveis
$obISelectMultiploCombustivel->SetNomeLista1 ('inCodCombustivelDisponivel');
$obISelectMultiploCombustivel->setCampoId1   ('cod_combustivel');
$obISelectMultiploCombustivel->setCampoDesc1 ('nom_combustivel');
$obISelectMultiploCombustivel->SetRecord1    ( $rsCombustivel );

//seta os combustiveis selecionados
$obISelectMultiploCombustivel->SetNomeLista2 ('inCodCombustivelSelecionados');
$obISelectMultiploCombustivel->setCampoId2   ('cod_combustivel');
$obISelectMultiploCombustivel->setCampoDesc2 ('nom_combustivel');
$obISelectMultiploCombustivel->SetRecord2    ( new RecordSet() );

//Define o objeto SelectMultiplo para armazenar a ordenação
$obCmbOrdenacao = new Select();
$obCmbOrdenacao->setName   ('inCodOrdenacao');
$obCmbOrdenacao->setValue  ($inCodOrdenacao );
$obCmbOrdenacao->setStyle  ( "width: 270px" );
$obCmbOrdenacao->setRotulo ( "Ordenação"    );
$obCmbOrdenacao->setTitle  ( "Selecione a Ordenação." );
$obCmbOrdenacao->setNull   ( false          );
$obCmbOrdenacao->addOption ("1", "Placa");
$obCmbOrdenacao->addOption ("2", "Marca");

//instancia um componente periodicidade
$obPeriodicidade = new Periodicidade();
$obPeriodicidade->setExercicio(date('Y'));
$obPeriodicidade->setExibeDia( false );
$obPeriodicidade->setNull( false );

//Radios de origem do veículo
$obRdbOrigemTodos = new Radio;
$obRdbOrigemTodos->setRotulo ( "Origem do Veículo" );
$obRdbOrigemTodos->setName   ( "inCodOrigem" );
$obRdbOrigemTodos->setChecked( true );
$obRdbOrigemTodos->setValue  ( "1" );
$obRdbOrigemTodos->setLabel  ( "Todos" );
$obRdbOrigemTodos->setNull   ( false      );
$obRdbOrigemTodos->setTitle  ( "Selecione a origem." );

$obRdbOrigemSim = new Radio;
$obRdbOrigemSim->setName   ( "inCodOrigem" );
$obRdbOrigemSim->setValue  ( "2" );
$obRdbOrigemSim->setLabel  ( "Veículo Própio" );

$obRdbOrigemNao = new Radio;
$obRdbOrigemNao->setName   ( "inCodOrigem" );
$obRdbOrigemNao->setValue  ( "3" );
$obRdbOrigemNao->setLabel  ( "Veículo de Terceiros" );

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
//define o formulário

$obBscCGMResponsavel = new IPopUpCGM($obForm);
$obBscCGMResponsavel->setId                    ('stNomeCGMResponsavel');
$obBscCGMResponsavel->setRotulo                ( 'Responsável'       );
$obBscCGMResponsavel->setTipo                  ('fisica'           );
$obBscCGMResponsavel->setTitle                ( 'Informe o CGM relacionado ao responsável pelo almoxarifado');
$obBscCGMResponsavel->setValue                 ( $stNomeCGMResponsavel);
$obBscCGMResponsavel->obCampoCod->setName      ( 'inCGMResponsavel' );
$obBscCGMResponsavel->obCampoCod->setSize      (8);
$obBscCGMResponsavel->obCampoCod->setValue     ( $inCGMResponsavel   );
$obBscCGMResponsavel->setNull                  ( true                );

//instancia o componente ISelectMultiploEntidadeUsuario
$obISelectMultiploEntidadeUsuario = new ISelectMultiploEntidadeUsuario();
$obISelectMultiploEntidadeUsuario->setNull( true );

$obFormulario = new Formulario;
$obFormulario->addForm          ( $obForm              );
$obFormulario->setAjuda         ("UC-03.02.15");
$obFormulario->addHidden        ( $obHdnCtrl           );
$obFormulario->addTitulo        ( "Dados de Filtro"    );
$obFormulario->addComponente    ( $obTxtCodVeiculo     );
$obISelectModeloVeiculo->geraFormulario( $obFormulario );
$obFormulario->addComponente    ( $obSelectTipoVeiculo );
$obFormulario->addComponente    ( $obISelectMultiploCombustivel );
$obFormulario->addComponente    ( $obISelectMultiploEntidadeUsuario );
$obFormulario->addComponente    ( $obTxtPrefixo        );
$obFormulario->addComponente    ( $obTxtPlaca          );
$obFormulario->addComponente    ( $obBscCGMResponsavel );
$obFormulario->addComponente    ( $obCmbOrdenacao      );
$obFormulario->agrupaComponentes( array($obRdbOrigemTodos,$obRdbOrigemSim,$obRdbOrigemNao));
$obFormulario->agrupaComponentes( array($obRdbTodos,$obRdbSim,$obRdbNao));
$obFormulario->addComponente    ( $obPeriodicidade     );
$obFormulario->OK();
$obFormulario->show();
