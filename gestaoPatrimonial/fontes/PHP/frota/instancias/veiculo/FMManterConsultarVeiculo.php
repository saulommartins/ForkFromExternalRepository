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

    $Id: FMManterConsultarVeiculo.php 63833 2015-10-22 13:05:17Z franver $

    * Casos de uso: uc-03.02.06
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GP_FRO_MAPEAMENTO.'TFrotaVeiculo.class.php' );
include_once( CAM_GP_FRO_MAPEAMENTO.'TFrotaCombustivel.class.php' );
include_once( CAM_GP_FRO_MAPEAMENTO.'TFrotaDocumento.class.php' );

$stPrograma = "ManterConsultarVeiculo";
$pgFilt   = "FL".$stPrograma.".php";
$pgList   = "LSManterVeiculo.php";
$pgForm   = "FM".$stPrograma.".php";
$pgProc   = "PR".$stPrograma.".php";
$pgOcul   = "OC".$stPrograma.".php";
$pgJs     = "JS".$stPrograma.".js";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$obTFrotaVeiculo = new TFrotaVeiculo();
$obTFrotaVeiculo->setDado( 'cod_veiculo', $_REQUEST['inCodVeiculo'] );
$obTFrotaVeiculo->recuperaVeiculoConsulta( $rsVeiculo );

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

//label para o codigo do veiculo
$obLblCodVeiculo = new Label();
$obLblCodVeiculo->setRotulo( 'Código' );
$obLblCodVeiculo->setValue( $rsVeiculo->getCampo( 'cod_veiculo' ) );

//label para a marca
$obLblMarca = new Label();
$obLblMarca->setRotulo( 'Marca' );
$obLblMarca->setValue( $rsVeiculo->getCampo( 'cod_marca' ).' - '.$rsVeiculo->getCampo( 'nom_marca' ) );

//label para a modelo
$obLblModelo = new Label();
$obLblModelo->setRotulo( 'Modelo' );
$obLblModelo->setValue( $rsVeiculo->getCampo( 'cod_modelo' ).' - '.$rsVeiculo->getCampo( 'nom_modelo' ) );

//label para o tipo do veiculo
$obLblTipoVeiculo = new Label();
$obLblTipoVeiculo->setRotulo( 'Tipo de Veículo' );
$obLblTipoVeiculo->setValue( $rsVeiculo->getCampo( 'nom_tipo' ) );

//recupera os combustiveis do veiculo
$obTFrotaCombustivel = new TFrotaCombustivel();
$obTFrotaCombustivel->setDado( 'cod_veiculo', $_REQUEST['inCodVeiculo'] );
$obTFrotaCombustivel->recuperaCombustivelVeiculo( $rsCombustivel );
while ( !$rsCombustivel->eof() ) {
    $stCombustivel .= $rsCombustivel->getCampo('nom_combustivel').', ';
    $rsCombustivel->proximo();
}

//label para o combustivel
$obLblCombustivel = new Label();
$obLblCombustivel->setRotulo( 'Combustível' );
$obLblCombustivel->setValue( substr($stCombustivel,0,-2) );

//label para o prefixo
$obLblPrefixo = new Label();
$obLblPrefixo->setRotulo( 'Prefixo' );
$obLblPrefixo->setValue( $rsVeiculo->getCampo( 'prefixo' ) );

//label para o chassi
$obLblChassi = new Label();
$obLblChassi->setRotulo( 'Chassi' );
$obLblChassi->setValue( $rsVeiculo->getCampo( 'chassi' ) );

//label para a km_inicial
$obLblKmInicial = new Label();
$obLblKmInicial->setRotulo( 'Km Inicial' );
$obLblKmInicial->setValue( $rsVeiculo->getCampo( 'km_inicial' ) );

//label para o numero do certificado
$obLblNumCertificado = new Label();
$obLblNumCertificado->setRotulo( 'Número Certificado' );
$obLblNumCertificado->setValue( $rsVeiculo->getCampo( 'num_certificado' ) );

//label para placa do veiculo
$obLblPlacaVeiculo = new Label();
$obLblPlacaVeiculo->setRotulo( 'Placa do Veículo' );
$obLblPlacaVeiculo->setValue( $rsVeiculo->getCampo( 'placa' ) );

//label para ano fabricacao
$obLblAnoFabricacao = new Label();
$obLblAnoFabricacao->setRotulo( 'Ano da Fabricação' );
$obLblAnoFabricacao->setValue( $rsVeiculo->getCampo( 'ano_fabricacao' ) );

//label para ano modelo
$obLblAnoModelo = new Label();
$obLblAnoModelo->setRotulo( 'Ano do Modelo' );
$obLblAnoModelo->setValue( $rsVeiculo->getCampo( 'ano_modelo' ) );

//label para categoria
$obLblCategoria = new Label();
$obLblCategoria->setRotulo( 'Categoria do Veículo(CRLV)' );
$obLblCategoria->setValue( $rsVeiculo->getCampo( 'categoria' ) );

//label para cor
$obLblCor = new Label();
$obLblCor->setRotulo( 'Cor' );
$obLblCor->setValue( $rsVeiculo->getCampo( 'cor' ) );

//label para capacidade
$obLblCapacidade = new Label();
$obLblCapacidade->setRotulo( 'Capacidade' );
$obLblCapacidade->setValue( $rsVeiculo->getCampo( 'capacidade' ) );

//label para potencia
$obLblPotencia = new Label();
$obLblPotencia->setRotulo( 'Potência' );
$obLblPotencia->setValue( $rsVeiculo->getCampo( 'potencia' ) );

//label para cilindradas
$obLblCilindradas = new Label();
$obLblCilindradas->setRotulo( 'Cilindradas' );
$obLblCilindradas->setValue( $rsVeiculo->getCampo('cilindrada') );

//label para a data de aquisicao
$obLblDtAquisicao = new Label();
$obLblDtAquisicao->setRotulo( 'Data de Aquisição' );
$obLblDtAquisicao->setValue( $rsVeiculo->getCampo('dt_aquisicao') );

//label para o tipo habilitacao
$obLblHabilitacao = new Label();
$obLblHabilitacao->setRotulo( 'Tipo de Habilitação' );
$obLblHabilitacao->setValue( $rsVeiculo->getCampo('nom_categoria') );

/***
* Dados do responsavel
***/

//label para o responsavel
$obLblResponsavel = new Label();
$obLblResponsavel->setRotulo( 'Responsável' );
$obLblResponsavel->setValue( $rsVeiculo->getCampo('num_responsavel').' - '.$rsVeiculo->getCampo('nom_responsavel') );

/***
* Dados da localizacao
***/

//label para exercicio setor
$obLblAnoExercicio = new Label();
$obLblAnoExercicio->setRotulo( 'Exercício' );
$obLblAnoExercicio->setValue( Sessao::getExercicio() );

//label para o orgao
$obLblOrgao = new Label();
$obLblOrgao->setRotulo( 'Orgão' );
$obLblOrgao->setValue( $rsVeiculo->getCampo('cod_orgao').' - '.$rsVeiculo->getCampo('descricao') );

//label para o local
$obLblLocal = new Label();
$obLblLocal->setRotulo( 'Local' );
$obLblLocal->setValue( $rsVeiculo->getCampo('cod_local').' - '.$rsVeiculo->getCampo('local_descricao') );

/***
* Dados da Baixa
***/
if ( $rsVeiculo->getCampo( 'dt_baixa' ) != '' ) {
    //label para a data da baixa
    $obLblDtBaixa = new Label();
    $obLblDtBaixa->setRotulo( 'Data' );
    $obLblDtBaixa->setValue( $rsVeiculo->getCampo('dt_baixa') );

    //label para o motivo da baixa
    $obLblMotivo = new Label();
    $obLblMotivo->setRotulo( 'Motivo' );
    $obLblMotivo->setValue( $rsVeiculo->getCampo('motivo') );
}

/***
* Dados do Proprietário
***/
if ( $rsVeiculo->getCampo( 'num_proprietario' ) != '' ) {
    //label para o proprietario
    $obLblProprietario = new Label();
    $obLblProprietario->setRotulo( 'Proprietário' );
    $obLblProprietario->setValue( $rsVeiculo->getCampo( 'num_proprietario' ).' - '.$rsVeiculo->getCampo( 'nom_proprietario' ) );
} else {
    //label para o bem
    $obLblBem = new Label();
    $obLblBem->setRotulo( 'Bem' );
    $obLblBem->setValue( $rsVeiculo->getCampo( 'cod_bem' ).' - '.$rsVeiculo->getCampo( 'nom_bem' ) );
}

/***
* Dados financeiros do veiculo
***/
if ( $rsVeiculo->getCampo( 'cod_entidade' ) != '' ) {

    //label para a entidade
    $obLblEntidade = new Label();
    $obLblEntidade->setRotulo( 'Entidade' );
    $obLblEntidade->setValue( $rsVeiculo->getCampo( 'cod_entidade' ).' - '.$rsVeiculo->getCampo( 'nom_entidade' ) );

    //label para o exercicio
    $obLblExercicioEmpenho = new Label();
    $obLblExercicioEmpenho->setRotulo( 'Exercício' );
    $obLblExercicioEmpenho->setValue( $rsVeiculo->getCampo( 'exercicio' ) );

    //label para o empenho
    $obLblEmpenho = new Label();
    $obLblEmpenho->setRotulo( 'Empenho' );
    $obLblEmpenho->setValue( $rsVeiculo->getCampo( 'cod_empenho' ).' - '.$rsVeiculo->getCampo( 'nom_empenho' ) );

    //label para o nota_fiscal
    $obLblNotaFiscal = new Label();
    $obLblNotaFiscal->setRotulo( 'Nota Fiscal' );
    $obLblNotaFiscal->setValue( $rsVeiculo->getCampo( 'nota_fiscal' ) );
}

/***
* Lista de documentos
***/
$obSpnDocumentos = new Label();
$obSpnDocumentos->setId('spnDocumentos');

//botao para voltar
$stLocation = $pgList.'?'.Sessao::getId().'&stAcao='.$stAcao.$stFiltro.'&pg='.$_REQUEST['pg'].'&pos='.$_REQUEST['pos']."&inCodVeiculo=".Sessao::read('codVeiculoFiltro');
$obBtnVoltar = new Button;
$obBtnVoltar->setName ( "btnVoltar" );
$obBtnVoltar->setValue(  $stAcao == "consultar" ? "Voltar" : "Cancelar" );
$obBtnVoltar->obEvento->setOnClick( "Cancelar('".$stLocation."');" );

//monta o formulário
$obFormulario = new Formulario;
$obFormulario->addForm      ( $obForm );
$obFormulario->addHidden    ( $obHdnAcao );
$obFormulario->addHidden    ( $obHdnCtrl );

$obFormulario->addTitulo    ( 'Dados do Veículo' );
$obFormulario->addComponente( $obLblCodVeiculo );
$obFormulario->addComponente( $obLblMarca );
$obFormulario->addComponente( $obLblModelo );
$obFormulario->addComponente( $obLblTipoVeiculo );
$obFormulario->addComponente( $obLblCombustivel );
$obFormulario->addComponente( $obLblPrefixo );
$obFormulario->addComponente( $obLblChassi );
$obFormulario->addComponente( $obLblKmInicial );
$obFormulario->addComponente( $obLblNumCertificado );
$obFormulario->addComponente( $obLblPlacaVeiculo );
$obFormulario->addComponente( $obLblAnoFabricacao );
$obFormulario->addComponente( $obLblAnoModelo );
$obFormulario->addComponente( $obLblCategoria );
$obFormulario->addComponente( $obLblCor );
$obFormulario->addComponente( $obLblCapacidade );
$obFormulario->addComponente( $obLblPotencia );
$obFormulario->addComponente( $obLblCilindradas );
$obFormulario->addComponente( $obLblDtAquisicao );
$obFormulario->addComponente( $obLblHabilitacao );

$obFormulario->addTitulo    ( 'Dados do Responsável' );
$obFormulario->addComponente( $obLblResponsavel );

if ( $rsVeiculo->getCampo( 'cod_entidade' ) != '' ) {

    $obFormulario->addTitulo    ( 'Dados Financeiros' );
    $obFormulario->addComponente( $obLblExercicioEmpenho );
    $obFormulario->addComponente( $obLblEntidade );
    $obFormulario->addComponente( $obLblEmpenho );
    $obFormulario->addComponente( $obLblNotaFiscal );

}

$obFormulario->addTitulo    ( 'Dados da Localização' );
$obFormulario->addComponente( $obLblAnoExercicio );
$obFormulario->addComponente( $obLblOrgao );
$obFormulario->addComponente( $obLblLocal );

if ( $rsVeiculo->getCampo('dt_baixa') != '' ) {
    $obFormulario->addTitulo    ( 'Dados da Baixa' );
    $obFormulario->addComponente( $obLblDtBaixa );
    $obFormulario->addComponente( $obLblMotivo );
}

if ( $rsVeiculo->getCampo('num_proprietario') != '' ) {
    $obFormulario->addTitulo    ( 'Dados do Proprietário' );
    $obFormulario->addComponente( $obLblProprietario );
} else {
    $obFormulario->addTitulo    ( 'Dados do Bem' );
    $obFormulario->addComponente( $obLblBem );
}

$obFormulario->addSpan      ( $obSpnDocumentos );

$obFormulario->defineBarra( array( $obBtnVoltar ), 'left', '' );

$obFormulario->show();

$jsOnLoad = "ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&inCodVeiculo=".$_REQUEST['inCodVeiculo']."','montaListaDocumentos');";

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
