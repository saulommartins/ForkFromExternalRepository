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

    $Id: FMManterBaixarVeiculo.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-03.02.07
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GP_FRO_COMPONENTES.'ISelectModeloVeiculo.class.php' );
include_once( CAM_GP_FRO_MAPEAMENTO.'TFrotaVeiculo.class.php' );
include_once( CAM_GP_FRO_MAPEAMENTO."TFrotaVeiculoBaixado.class.php" );


$stPrograma = "ManterBaixarVeiculo";
$pgFilt   = "FL".$stPrograma.".php";
$pgList   = "LS".$stPrograma.".php";
$pgForm   = "FM".$stPrograma.".php";
$pgProc   = "PR".$stPrograma.".php";
$pgOcul   = "OC".$stPrograma.".php";
$pgJs     = "JS".$stPrograma.".js";

//include_once( $pgJs );

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$obTFrotaVeiculo = new TFrotaVeiculo();
$obTFrotaVeiculo->setDado( 'cod_veiculo', $_REQUEST['inCodVeiculo'] );
$obTFrotaVeiculo->recuperaVeiculoAnalitico( $rsVeiculo );

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

//cria um textbox  para o codigo do veiculo
$obTxtCodVeiculo = new TextBox();
$obTxtCodVeiculo->setRotulo( 'Código do Veículo' );
$obTxtCodVeiculo->setValue( $rsVeiculo->getCampo('cod_veiculo') );
$obTxtCodVeiculo->setName( 'inCodVeiculo' );
$obTxtCodVeiculo->setLabel( true );

//cria um label para a marca do veiculo
$obLblMarca = new Label();
$obLblMarca->setRotulo( 'Marca' );
$obLblMarca->setValue( $rsVeiculo->getCampo('cod_marca').' - '.$rsVeiculo->getCampo('nom_marca') );

//cria um label para a modelo do veiculo
$obLblModelo = new Label();
$obLblModelo->setRotulo( 'Modelo' );
$obLblModelo->setValue( $rsVeiculo->getCampo('cod_modelo').' - '.$rsVeiculo->getCampo('nom_modelo') );

//cria um label para a placa do veículo
$obLblPlaca = new Label();
$obLblPlaca->setRotulo( 'Placa' );
$obLblPlaca->setValue( $rsVeiculo->getCampo('placa_masc') );

//cria um label para o tipo do veiculo
$obLblTipoVeiculo = new Label();
$obLblTipoVeiculo->setRotulo( 'Tipo de Veículo' );
$obLblTipoVeiculo->setValue( $rsVeiculo->getCampo('nom_tipo') );

//instancia um componente data para a baixa
$obDtBaixa = new Data();
$obDtBaixa->setName( 'dtBaixa' );
$obDtBaixa->setId  ( 'dtBaixa' );
$obDtBaixa->setValue( date('d/m/Y') );
$obDtBaixa->setRotulo( 'Data da Baixa' );
$obDtBaixa->setTitle ( 'Informe a data da baixa do veículo.' );
$obDtBaixa->setNull ( false );

//instancia um select para tipo de baixa
$obTFrotaVeiculoBaixado = new TFrotaVeiculoBaixado();
$obTFrotaVeiculoBaixado->recuperaTipoBaixa($rsTpBaixa);

$obCmbTpBaixa = new Select();
$obCmbTpBaixa->setRotulo( "Tipo de Baixa");
$obCmbTpBaixa->setName( "inCodTpBaixa");
$obCmbTpBaixa->setTitle( "Informe o tipo de baixa.");
$obCmbTpBaixa->setStyle( "width: 250px");
$obCmbTpBaixa->setNull( false);
$obCmbTpBaixa->addOption( "","Selecione" );
$obCmbTpBaixa->setCampoId( "cod_tipo");
$obCmbTpBaixa->setCampoDesc( "[cod_tipo] - [descricao]");
$obCmbTpBaixa->preencheCombo( $rsTpBaixa  );

//instancia um textarea para o motivo
$obTxtMotivo = new TextArea();
$obTxtMotivo->setName( 'stMotivo' );
$obTxtMotivo->setId( 'stMotivo' );
$obTxtMotivo->setRotulo( 'Motivo' );
$obTxtMotivo->setTitle( 'Informe o motivo da baixa do veículo.' );
$obTxtMotivo->setNull( false );

//monta o formulário
$obFormulario = new Formulario;
$obFormulario->addForm      ( $obForm );
$obFormulario->addHidden    ( $obHdnAcao );
$obFormulario->addHidden    ( $obHdnCtrl );

$obFormulario->addTitulo    ( 'Dados do Veículo' );

$obFormulario->addComponente( $obTxtCodVeiculo );
$obFormulario->addComponente( $obLblMarca );
$obFormulario->addComponente( $obLblModelo );
$obFormulario->addComponente( $obLblPlaca );
$obFormulario->addComponente( $obLblTipoVeiculo );

$obFormulario->addTitulo    ( 'Dados da Baixa' );
$obFormulario->addComponente( $obDtBaixa    );
$obFormulario->addComponente( $obCmbTpBaixa );
$obFormulario->addComponente( $obTxtMotivo  );

$obFormulario->OK(true);

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
