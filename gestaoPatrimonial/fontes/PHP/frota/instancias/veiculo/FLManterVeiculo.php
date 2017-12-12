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
  * Data de Criação: 10/09/2007

  * @author Analista: Gelson W. Gonçalves
  * @author Desenvolvedor: Henrique Boaventura

  * $Id: FLManterVeiculo.php 59612 2014-09-02 12:00:51Z gelson $

  * Casos de uso: uc-03.02.06
  */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GP_FRO_COMPONENTES.'ISelectModeloVeiculo.class.php';
include_once CAM_GP_FRO_MAPEAMENTO.'TFrotaTipoVeiculo.class.php';
include_once CAM_GP_FRO_MAPEAMENTO.'TFrotaCombustivel.class.php';
include_once CAM_GA_CGM_COMPONENTES."IPopUpCGMVinculado.class.php";
include_once CAM_GF_ORC_COMPONENTES."ISelectMultiploEntidadeUsuario.class.php";

$stPrograma = "ManterVeiculo";
$pgFilt   = "FL".$stPrograma.".php";
$pgList   = "LS".$stPrograma.".php";
$pgListRetirar = "LSManterRetirarVeiculo.php";
$pgForm   = "FM".$stPrograma.".php";
$pgProc   = "PR".$stPrograma.".php";
$pgOcul   = "OC".$stPrograma.".php";
$pgJs     = "JS".$stPrograma.".js";

Sessao::remove('filtro');

$stAcao = $request->get("stAcao");

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

//cria um textbox para o codigo do veiculo
$obInCodVeiculo = new Inteiro();
$obInCodVeiculo->setRotulo( 'Código do Veículo' );
$obInCodVeiculo->setTitle( 'Informe o código do veículo.' );
$obInCodVeiculo->setName( 'inCodVeiculo' );
$obInCodVeiculo->setId( 'inCodVeiculo' );

//instancia o componenten ISelectModeloVeiculo
$obISelectModeloVeiculo = new ISelectModeloVeiculo( $obForm );
$obISelectModeloVeiculo->obISelectMarcaVeiculo->setNull( true );
$obISelectModeloVeiculo->obSelectModeloVeiculo->setNull( true );

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

//instancia um textbox para o prefixo
$obTxtPrefixo = new TextBox();
$obTxtPrefixo->setRotulo( 'Prefixo' );
$obTxtPrefixo->setTitle( 'Informe o prefixo do veículo.' );
$obTxtPrefixo->setName( 'stPrefixo' );
$obTxtPrefixo->setId( 'stPrefixo' );
$obTxtPrefixo->setNull( true );

//instancia um textbox para o numero da placa
$obTxtPlaca = new TextBox();
$obTxtPlaca->setRotulo( 'Placa do Veículo' );
$obTxtPlaca->setTitle ( 'Informe a placa do veículo.' );
$obTxtPlaca->setName  ( 'stNumPlaca' );
$obTxtPlaca->setNull  ( true );
$obTxtPlaca->obEvento->setOnKeyUp( "mascaraPlacaVeiculo(this);" );
$obTxtPlaca->obEvento->setOnBlur( "mascaraPlacaVeiculo(this);" );

//instancia o componente IPopUpCGMVinculado para o responsavel
$obIPopUpResponsavel = new IPopUpCGMVinculado( $obForm );
$obIPopUpResponsavel->setTabelaVinculo    ( 'sw_cgm_pessoa_fisica' );
$obIPopUpResponsavel->setCampoVinculo     ( 'numcgm'               );
$obIPopUpResponsavel->setNomeVinculo      ( 'Responsável'          );
$obIPopUpResponsavel->setRotulo           ( 'Responsável'          );
$obIPopUpResponsavel->setTitle            ( 'Informe o reponsável pelo veículo.' );
$obIPopUpResponsavel->setName             ( 'stNomResponsavel'       );
$obIPopUpResponsavel->setId               ( 'stNomResponsavel'       );
$obIPopUpResponsavel->obCampoCod->setName ( 'inCodResponsavel'       );
$obIPopUpResponsavel->obCampoCod->setId   ( 'inCodResponsavel'       );
$obIPopUpResponsavel->setNull             ( true                     );

//instancia o componente ISelectMultiploEntidadeUsuario
$obISelectMultiploEntidadeUsuario = new ISelectMultiploEntidadeUsuario();
$obISelectMultiploEntidadeUsuario->setNull( true );

/****
*radio para origem do veículo
****/
$obRdOrigemTodos = new Radio();
$obRdOrigemTodos->setName( 'stOrigem' );
$obRdOrigemTodos->setId( 'stOrigem' );
$obRdOrigemTodos->setRotulo( 'Origem do Veículo' );
$obRdOrigemTodos->setTitle( 'Selecione a origem do veículo.' );
$obRdOrigemTodos->setLabel( 'Todos' );
$obRdOrigemTodos->setValue( 'todos' );

$obRdOrigemProprio = new Radio();
$obRdOrigemProprio->setName( 'stOrigem' );
$obRdOrigemProprio->setId( 'stOrigem' );
$obRdOrigemProprio->setRotulo( 'Origem do Veículo' );
$obRdOrigemProprio->setTitle( 'Selecione a origem do veículo.' );
$obRdOrigemProprio->setLabel( 'Próprio' );
$obRdOrigemProprio->setValue( 'proprio' );

$obRdOrigemTerceiros = new Radio();
$obRdOrigemTerceiros->setName( 'stOrigem' );
$obRdOrigemTerceiros->setId( 'stOrigem' );
$obRdOrigemTerceiros->setRotulo( 'Origem do Veículo' );
$obRdOrigemTerceiros->setTitle( 'Selecione a origem do veículo.' );
$obRdOrigemTerceiros->setLabel( 'Terceiros' );
$obRdOrigemTerceiros->setValue( 'terceiros' );

//instancia select para a ordenacao
$obSelectOrdenacao = new Select();
$obSelectOrdenacao->setName( 'stOrdenacao' );
$obSelectOrdenacao->setId  ( 'stOrdenacao' );
$obSelectOrdenacao->setRotulo( 'Ordenação' );
$obSelectOrdenacao->setTitle( 'Selecione a ordenação dos bens.' );
$obSelectOrdenacao->addOption( 'codigo', 'Código do Bem' );
$obSelectOrdenacao->addOption( 'placa' , 'Placa' );
$obSelectOrdenacao->addOption( 'marca' , 'Marca' );
$obSelectOrdenacao->addOption( 'modelo', 'Modelo' );

//instancia um componente periodicidade
$obPeriodicidade = new Periodicidade();
$obPeriodicidade->setExercicio(date('Y'));

//monta o formulário
$obFormulario = new Formulario;
$obFormulario->addForm      ( $obForm );
$obFormulario->addHidden    ( $obHdnAcao );
$obFormulario->addHidden    ( $obHdnCtrl );

$obFormulario->addComponente( $obInCodVeiculo );
$obISelectModeloVeiculo->geraFormulario( $obFormulario );
$obFormulario->addComponente( $obSelectTipoVeiculo );
$obFormulario->addComponente( $obISelectMultiploCombustivel );
$obFormulario->addComponente( $obTxtPrefixo );
$obFormulario->addComponente( $obTxtPlaca );
$obFormulario->addComponente( $obIPopUpResponsavel );
$obFormulario->addComponente( $obISelectMultiploEntidadeUsuario );
$obFormulario->agrupaComponentes( array( $obRdOrigemTodos, $obRdOrigemProprio, $obRdOrigemTerceiros ) );

if ($stAcao == 'retornar') {
    $obFormulario->addComponente( $obPeriodicidade );
}

$obFormulario->addComponente( $obSelectOrdenacao );

$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
