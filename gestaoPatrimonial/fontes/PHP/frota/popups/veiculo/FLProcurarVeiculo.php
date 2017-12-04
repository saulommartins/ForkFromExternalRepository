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
  * Data de Criação: 19/11/2007

  * @author Analista: Gelson W. Gonçalves
  * @author Desenvolvedor: Henrique Boaventura

  * $Id: FLProcurarVeiculo.php 59612 2014-09-02 12:00:51Z gelson $

  * Casos de uso: uc-03.02.00

  **/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GP_FRO_COMPONENTES.'ISelectModeloVeiculo.class.php';
include_once CAM_GP_FRO_MAPEAMENTO.'TFrotaTipoVeiculo.class.php';
include_once CAM_GP_FRO_MAPEAMENTO.'TFrotaCombustivel.class.php';

$stPrograma = "ProcurarVeiculo";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');

# Limpa os filtros da sessão.
Sessao::write('arFiltro' , array());

//cria um novo formulario
$obForm = new Form;
$obForm->setAction ($pgList);

//Cria o hidden da acao
$obHdnAcao = new Hidden;
$obHdnAcao->setName  ("stAcao");
$obHdnAcao->setValue ($stAcao);

//cria a acao de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ("stCtrl" );
$obHdnCtrl->setValue ("");

//cria um hidden para nom do form
$obHdnNomForm = new Hidden;
$obHdnNomForm->setName  ('nomForm' );
$obHdnNomForm->setValue ($obForm->getName());

$obHdnCampoNum = new Hidden;
$obHdnCampoNum->setName  ("campoNum");
$obHdnCampoNum->setValue ($_REQUEST['campoNum']);

//Define HIDDEN com o o nome do campo texto
$obHdnCampoNom = new Hidden;
$obHdnCampoNom->setName  ("campoNom");
$obHdnCampoNom->setValue ($_REQUEST['campoNom']);

$obHdnTipoBusca = new Hidden;
$obHdnTipoBusca->setName  ("stTipoBusca");
$obHdnTipoBusca->setValue ($_REQUEST['tipoBusca']);

//cria um textbox para o codigo do veiculo
$obInCodVeiculo = new Inteiro;
$obInCodVeiculo->setRotulo ('Código do Veículo');
$obInCodVeiculo->setTitle  ('Informe o código do veículo.');
$obInCodVeiculo->setName   ('inCodVeiculo');
$obInCodVeiculo->setId     ('inCodVeiculo');

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
$obSelectOrdenacao->addOption( 'Modelo', 'Modelo' );

//monta o formulário
$obFormulario = new Formulario;
$obFormulario->addForm      ( $obForm );
$obFormulario->addHidden    ( $obHdnAcao );
$obFormulario->addHidden    ( $obHdnCtrl );
$obFormulario->addHidden    ( $obHdnNomForm );
$obFormulario->addHidden    ( $obHdnCampoNum );
$obFormulario->addHidden    ( $obHdnCampoNom );
$obFormulario->addHidden    ( $obHdnTipoBusca);

$obFormulario->addComponente( $obInCodVeiculo );
$obISelectModeloVeiculo->geraFormulario( $obFormulario );
$obFormulario->addComponente( $obSelectTipoVeiculo );
$obFormulario->addComponente( $obISelectMultiploCombustivel );
$obFormulario->addComponente( $obTxtPrefixo );
$obFormulario->addComponente( $obTxtPlaca );
$obFormulario->agrupaComponentes( array( $obRdOrigemTodos, $obRdOrigemProprio, $obRdOrigemTerceiros ) );
$obFormulario->addComponente( $obSelectOrdenacao );

$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
