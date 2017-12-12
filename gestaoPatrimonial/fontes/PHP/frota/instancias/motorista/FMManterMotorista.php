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
    * Data de Criação: 08/11/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    $Id: FMManterMotorista.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-03.02.11
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GA_CGM_COMPONENTES."IPopUpCGMVinculado.class.php" );
include_once( CAM_GP_FRO_MAPEAMENTO.'TFrotaCategoriaHabilitacao.class.php' );
include_once( CAM_GP_FRO_MAPEAMENTO.'TFrotaMotorista.class.php' );
include_once( CAM_GP_FRO_COMPONENTES.'IPopUpVeiculo.class.php' );

$stPrograma = "ManterMotorista";
$pgFilt   = "FL".$stPrograma.".php";
$pgList   = "LS".$stPrograma.".php";
$pgForm   = "FM".$stPrograma.".php";
$pgProc   = "PR".$stPrograma.".php";
$pgOcul   = "OC".$stPrograma.".php";
$pgJs     = "JS".$stPrograma.".js";

include_once( $pgJs );

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

Sessao::write('veiculosMotorista' , array());

if ($stAcao == 'alterar') {
    $obTFrotaMotorista = new TFrotaMotorista();
    $obTFrotaMotorista->setDado( 'cgm_motorista', $_REQUEST['inCodMotorista'] );
    $obTFrotaMotorista->recuperaMotoristaAnalitico( $rsMotorista );
} else {
    $rsMotorista = new RecordSet();
}

//cria um novo formulario
$obForm = new Form;
$obForm->setAction ($pgProc);
$obForm->setTarget ("oculto");

//Cria o hidden da acao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ("stAcao");
$obHdnAcao->setValue($stAcao);

//cria a acao de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ("stCtrl" );
$obHdnCtrl->setValue("");

//instancia o componente IPopUpCGMVinculado para o motorista
$obIPopUpMotorista = new IPopUpCGMVinculado( $obForm );
$obIPopUpMotorista->setTabelaVinculo    ( 'sw_cgm_pessoa_fisica' );
$obIPopUpMotorista->setCampoVinculo     ( 'numcgm'               );
$obIPopUpMotorista->setNomeVinculo      ( 'Motorista'            );
$obIPopUpMotorista->setRotulo           ( 'CGM do Motorista'     );
$obIPopUpMotorista->setTitle            ( 'Informe o número do CGM do motorista.' );
$obIPopUpMotorista->setName             ( 'stNomMotorista'       );
$obIPopUpMotorista->setId               ( 'stNomMotorista'       );
$obIPopUpMotorista->obCampoCod->setName ( 'inCodMotorista'       );
$obIPopUpMotorista->obCampoCod->setId   ( 'inCodMotorista'       );
$obIPopUpMotorista->setNull             ( false                  );
$stFiltro = " AND NOT EXISTS ( SELECT 1
                                 FROM frota.motorista
                                WHERE motorista.cgm_motorista = cgm.numcgm
                             ) ";
$obIPopUpMotorista->setFiltro           ( $stFiltro              );
$obIPopUpMotorista->obCampoCod->obEvento->setOnBlur( "montaParametrosGET('preencheDadosMotorista', 'inCodMotorista' );" );
$obIPopUpMotorista->setValue( $rsMotorista->getCampo('nom_motorista') );
$obIPopUpMotorista->obCampoCod->setValue( $rsMotorista->getCampo( 'cgm_motorista' ) );
if ($stAcao == 'alterar') {
    $obIPopUpMotorista->setLabel( true );
}

//instancia um textbox para o numero da carteira de motorista
$obTxtNumCNH = new TextBox();
$obTxtNumCNH->setName( 'stNumCNH' );
$obTxtNumCNH->setId( 'stNumCNH' );
$obTxtNumCNH->setRotulo( 'Número CNH' );
$obTxtNumCNH->setTitle( 'Informe o número da habilitação.' );
$obTxtNumCNH->setNull( false );
$obTxtNumCNH->setValue( $rsMotorista->getCampo( 'num_cnh' ) );

//instancia um componente data para a validade da carteira
$obDtValidade = new Data();
$obDtValidade->setName( 'dtValidade' );
$obDtValidade->setId( 'dtValidade' );
$obDtValidade->setRotulo( 'Data de Validade CNH' );
$obDtValidade->setTitle( 'Informe a data de validade da habilitação.' );
$obDtValidade->setNull( false );
$obDtValidade->setValue( $rsMotorista->getCampo( 'dt_validade_cnh' ) );

//recupera os as categorias de habilitacao
$obTFrotaCategoriaHabilitacao = new TFrotaCategoriaHabilitacao();
$obTFrotaCategoriaHabilitacao->recuperaTodos( $rsCategoriaHabilitacao, ' ORDER BY nom_categoria ' );

//instancia um hidden para guardar o valor do select da habilitacao
$obHdnHabilitacao = new Hidden();
$obHdnHabilitacao->setName  ( 'hdnHabilitacao' );
$obHdnHabilitacao->setId    ( 'hdnHabilitacao' );
$obHdnHabilitacao->setValue ( $rsMotorista->getCampo( 'cod_categoria_cnh' ) );

//instancia um select para a habilitacao exigida
$obSelectHabilitacao = new Select();
$obSelectHabilitacao->setRotulo( 'Categoria' );
$obSelectHabilitacao->setTitle ( 'Selecione a categoria de habilitação.' );
$obSelectHabilitacao->setName  ( 'slHabilitacao' );
$obSelectHabilitacao->setId    ( 'slHabilitacao' );
$obSelectHabilitacao->addOption( '','Selecione' );
$obSelectHabilitacao->setCampoId( 'cod_categoria' );
$obSelectHabilitacao->setCampoDesc( 'nom_categoria' );
$obSelectHabilitacao->preencheCombo( $rsCategoriaHabilitacao );
$obSelectHabilitacao->obEvento->setOnChange( 'preencheHabilitacao(this.value);' );
$obSelectHabilitacao->setNull  ( false );
$obSelectHabilitacao->setValue( $rsMotorista->getCampo( 'cod_categoria_cnh' ) );

//instancia um radio para ativo
$obRdStatusAtivo = new Radio();
$obRdStatusAtivo->setName( 'boStatus' );
$obRdStatusAtivo->setId( 'boStatusAtivo' );
$obRdStatusAtivo->setRotulo( 'Status' );
$obRdStatusAtivo->setTitle( 'Selecione o status do motorista.' );
$obRdStatusAtivo->setLabel( 'Ativo' );
$obRdStatusAtivo->setValue( true );
$obRdStatusAtivo->setNull( false );
if ( $stAcao != 'alterar' OR $rsMotorista->getCampo( 'ativo' ) == 't' ) {
    $obRdStatusAtivo->setChecked( true );
}

//instancia um radio para ativo
$obRdStatusInativo = new Radio();
$obRdStatusInativo->setName( 'boStatus' );
$obRdStatusInativo->setId( 'boStatusInativo' );
$obRdStatusInativo->setRotulo( 'Status' );
$obRdStatusInativo->setTitle( 'Selecione o status do motorista.' );
$obRdStatusInativo->setLabel( 'Inativo' );
$obRdStatusInativo->setNull( false );
$obRdStatusInativo->setValue( false );
if ( $rsMotorista->getCampo( 'ativo' ) == 'f' ) {
    $obRdStatusInativo->setChecked( true );
}

//instancia o componente IPopUpVeiculo
$obIPopUpVeiculo = new IPopUpVeiculo($obForm);
$obIPopUpVeiculo->setMostrarDescricao( false );
$obIPopUpVeiculo->obCampoCod->obEvento->setOnBlur("montaParametrosGET('montaVeiculo','inCodVeiculo');");
$obIPopUpVeiculo->obCampoCod->setObrigatorioBarra( true );
$obIPopUpVeiculo->setObrigatorioBarra( true );

//instancia um textbox para o numero da placa
$obTxtPlaca = new TextBox();
$obTxtPlaca->setRotulo( 'Placa do Veículo' );
$obTxtPlaca->setTitle ( 'Informe a placa do veículo.' );
$obTxtPlaca->setName  ( 'stNumPlaca' );
$obTxtPlaca->setId    ( 'stNumPlaca' );
$obTxtPlaca->obEvento->setOnKeyUp( "mascaraPlacaVeiculo(this);" );
$obTxtPlaca->obEvento->setOnBlur( "mascaraPlacaVeiculo(this);" );
$obTxtPlaca->setValue( $_REQUEST['stNumPlaca'] );
$obTxtPlaca->obEvento->setOnChange("montaParametrosGET('montaVeiculo','stNumPlaca');");

//instancia textbox para o prefixo
$obTxtPrefixo = new TextBox();
$obTxtPrefixo->setRotulo( 'Prefixo' );
$obTxtPrefixo->setTitle ( 'Informe prefixo do veículo.' );
$obTxtPrefixo->setName  ( 'stPrefixo' );
$obTxtPrefixo->setId    ( 'stPrefixo' );
$obTxtPrefixo->setSize  ( 15 );
$obTxtPrefixo->setMaxLength( 15 );
$obTxtPrefixo->setValue( $_REQUEST['stPrefixo'] );
$obTxtPrefixo->obEvento->setOnChange("montaParametrosGET('montaVeiculo','stPrefixo');");

//radio para padrao sim
$obRdPadraoSim = new Radio();
$obRdPadraoSim->setName('boPadrao');
$obRdPadraoSim->setId  ('boPadraoSim');
$obRdPadraoSim->setRotulo( 'Padrão' );
$obRdPadraoSim->setLabel( 'Sim' );
$obRdPadraoSim->setValue( true );
$obRdPadraoSim->setObrigatorioBarra( true );

//radio para padrao nao
$obRdPadraoNao = new Radio();
$obRdPadraoNao->setName('boPadrao');
$obRdPadraoNao->setId  ('boPadraoNao');
$obRdPadraoNao->setRotulo( 'Padrão' );
$obRdPadraoNao->setLabel( 'Não' );
$obRdPadraoNao->setValue( false );
$obRdPadraoNao->setChecked( true );
$obRdPadraoNao->setObrigatorioBarra( true );

//Define Objeto Button para Incluir veiculo
$obBtnIncluirVeiculo = new Button;
$obBtnIncluirVeiculo->setValue             ( "Incluir"                                      );
$obBtnIncluirVeiculo->setId                ( "incluiVeiculo"                                );
$obBtnIncluirVeiculo->obEvento->setOnClick ( "montaParametrosGET('incluirListaVeiculos');" );

//Define Objeto Button para Limpar Veiculo
$obBtnLimparVeiculo = new Button;
$obBtnLimparVeiculo->setValue             ( "Limpar"          );
$obBtnLimparVeiculo->obEvento->setOnClick ( "montaParametrosGET('limparVeiculo');" );

//cria um span para os dados do veiculo
$obSpnDetalhe = new Span();
$obSpnDetalhe->setId('spnDetalhe');

//cria um span para a lista de veiculos
$obSpnVeiculos = new Span();
$obSpnVeiculos->setId('spnVeiculos');

//cria um span para a lista de veiculos
$obSpnInfracao = new Span();
$obSpnInfracao->setId('spnInfracao');

//monta o formulário
$obFormulario = new Formulario;
$obFormulario->setAjuda('uc-03.02.11');
$obFormulario->addForm      ( $obForm );
$obFormulario->addHidden    ( $obHdnAcao );
$obFormulario->addHidden    ( $obHdnCtrl );
$obFormulario->addHidden    ( $obHdnHabilitacao );

$obFormulario->addTitulo    ( 'Dados do Motorista' );

$obFormulario->addComponente( $obIPopUpMotorista );
$obFormulario->addComponente( $obTxtNumCNH );
$obFormulario->addComponente( $obDtValidade );
$obFormulario->addComponente( $obSelectHabilitacao );
$obFormulario->agrupaComponentes( array( $obRdStatusAtivo, $obRdStatusInativo ) );

$obFormulario->addTitulo    ( 'Autorizar Veículo' );

$obFormulario->addComponente( $obIPopUpVeiculo );
$obFormulario->addComponente( $obTxtPlaca );
$obFormulario->addComponente( $obTxtPrefixo );
$obFormulario->agrupaComponentes( array( $obRdPadraoSim, $obRdPadraoNao ) );
$obFormulario->addSpan      ( $obSpnDetalhe );
$obFormulario->defineBarra  ( array( $obBtnIncluirVeiculo, $obBtnLimparVeiculo ) );
$obFormulario->addSpan      ( $obSpnVeiculos );
$obFormulario->addSpan      ( $obSpnInfracao );

if ($stAcao == 'alterar') {
    $obFormulario->Cancelar ($pgList.'?'.Sessao::getId().'&stAcao='.$stAcao."&pg=".$_REQUEST['pg']."&pos=".$_REQUEST['pos']);
} else {
    $obFormulario->OK();
}

$obFormulario->show();

if ($stAcao == 'alterar') {
    $jsOnLoad = "ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&inCodMotorista=".$_REQUEST['inCodMotorista']."','preencheListaVeiculos');";
    $jsOnLoad.= "ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&inCodMotorista=".$_REQUEST['inCodMotorista']."','carregarListaInfracao');";
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
