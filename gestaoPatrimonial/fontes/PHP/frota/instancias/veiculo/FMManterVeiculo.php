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

    $Id: FMManterVeiculo.php 62489 2015-05-14 13:40:01Z jean $

    * Casos de uso: uc-03.02.06
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GP_FRO_COMPONENTES.'ISelectModeloVeiculo.class.php' );
include_once( CAM_GP_FRO_MAPEAMENTO.'TFrotaVeiculo.class.php' );
include_once( CAM_GP_FRO_MAPEAMENTO.'TFrotaTipoVeiculo.class.php' );
include_once( CAM_GP_FRO_MAPEAMENTO.'TFrotaCombustivel.class.php' );
include_once( CAM_GP_FRO_MAPEAMENTO.'TFrotaCategoriaHabilitacao.class.php' );
include_once( CAM_GP_FRO_MAPEAMENTO.'TFrotaDocumento.class.php' );
include_once( CAM_GP_FRO_MAPEAMENTO."TFrotaControleInterno.class.php" );
include_once( CAM_GA_CGM_COMPONENTES."IPopUpCGMVinculado.class.php" );
include_once( CAM_GPC_TCERN_MAPEAMENTO."TTCERNCategoriaVeiculoTCE.class.php" );
include_once( CAM_GPC_TCERN_MAPEAMENTO."TTCERNVeiculoCategoriaVinculo.class.php" );
include_once(CAM_GA_PROT_COMPONENTES.'IPopUpProcesso.class.php');

$stPrograma = "ManterVeiculo";
$pgFilt   = "FL".$stPrograma.".php";
$pgList   = "LS".$stPrograma.".php";
$pgForm   = "FM".$stPrograma.".php";
$pgProc   = "PR".$stPrograma.".php";
$pgOcul   = "OC".$stPrograma.".php";
$pgJs     = "JS".$stPrograma.".js";

include $pgJs;

$stAcao = $request->get("stAcao");

Sessao::write('arDocumentos' , array());
Sessao::write('arDocumentosExcluidos' , array());

if ($stAcao == 'alterar') {
    $obTFrotaVeiculo = new TFrotaVeiculo();
    $obTFrotaVeiculo->setDado( 'cod_veiculo', $_REQUEST['inCodVeiculo'] );
    $obTFrotaVeiculo->recuperaVeiculoAnalitico( $rsVeiculo );

    //cria um textbox  para o codigo do veiculo
    $obTxtCodVeiculo = new TextBox();
    $obTxtCodVeiculo->setRotulo( 'Código do Veículo' );
    $obTxtCodVeiculo->setValue( $rsVeiculo->getCampo('cod_veiculo') );
    $obTxtCodVeiculo->setName( 'inCodVeiculo' );
    $obTxtCodVeiculo->setLabel( true );
    
    $obTFrotaControleInterno = new TFrotaControleInterno();
    $obTFrotaControleInterno->setDado( 'cod_veiculo', $_REQUEST['inCodVeiculo'] );
    $obTFrotaControleInterno->setDado( 'exercicio', Sessao::getExercicio());
    $obTFrotaControleInterno->recuperaPorChave($rsFrotaControleInterno);
           
    if($rsFrotaControleInterno->inNumLinhas > 0){
        $boControleInterno = $rsFrotaControleInterno->getCampo('verificado');
    }else{
        $boControleInterno = "";
    }
        
} else {
    $rsVeiculo = new RecordSet();
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

//instancia o componente ISelectModeloVeiculo
$obISelectModeloVeiculo = new ISelectModeloVeiculo( $obForm );
$obISelectModeloVeiculo->obISelectMarcaVeiculo->setValue( $rsVeiculo->getCampo('cod_marca') );
$obISelectModeloVeiculo->obSelectModeloVeiculo->setValue( $rsVeiculo->getCampo('cod_modelo') );
$obISelectModeloVeiculo->setNull( false );

//recupera os tipos de veiculo
$obTFrotaTipoVeiculo = new TFrotaTipoVeiculo();
$obTFrotaTipoVeiculo->recuperaTodos( $rsTipoVeiculo, ' ORDER BY nom_tipo ' );

//preenche o select com os tipos de veiculo
$obSelectTipoVeiculo = new Select();
$obSelectTipoVeiculo->setRotulo ( 'Tipo de Veículo' );
$obSelectTipoVeiculo->setTitle  ( 'Selecione o tipo do veículo.' );
$obSelectTipoVeiculo->setName   ( 'slTipoVeiculo' );
$obSelectTipoVeiculo->setId     ( 'slTipoVeiculo' );
$obSelectTipoVeiculo->addOption ( '', 'Selecione' );
$obSelectTipoVeiculo->setCampoId( 'cod_tipo' );
$obSelectTipoVeiculo->setCampoDesc( 'nom_tipo' );
$obSelectTipoVeiculo->preencheCombo( $rsTipoVeiculo );
$obSelectTipoVeiculo->setValue( $rsVeiculo->getCampo('cod_tipo_veiculo') );
$obSelectTipoVeiculo->obEvento->setOnChange( "montaParametrosGET('montaPrefixoPlaca','slTipoVeiculo');" );
$obSelectTipoVeiculo->setNull   ( false );

//recupera os tipos de combustivel
$obTFrotaCombustivel = new TFrotaCombustivel();
if ($stAcao == 'alterar') {
    $obTFrotaCombustivel->setDado('cod_veiculo', $rsVeiculo->getCampo('cod_veiculo') );
    $obTFrotaCombustivel->recuperaCombustivelDisponivelVeiculo( $rsCombustivel );
} else {
    $obTFrotaCombustivel->recuperaTodos( $rsCombustivel, ' ORDER BY nom_combustivel ' );
}

//instancia um select multiplo para os tipo do combustível
$obISelectMultiploCombustivel = new SelectMultiplo();
$obISelectMultiploCombustivel->setName   ('inCodCombustivel');
$obISelectMultiploCombustivel->setRotulo ( "Tipo de Combustível" );
$obISelectMultiploCombustivel->setNull   ( false );
$obISelectMultiploCombustivel->setTitle  ( "Selecione o tipo de combustível do veículo." );

//seta os combustiveis disponiveis
$obISelectMultiploCombustivel->SetNomeLista1 ('inCodCombustivelDisponivel');
$obISelectMultiploCombustivel->setCampoId1   ('cod_combustivel');
$obISelectMultiploCombustivel->setCampoDesc1 ('nom_combustivel');
$obISelectMultiploCombustivel->SetRecord1    ( $rsCombustivel );

//recupera os combustiveis do veiculo
if ($stAcao == 'alterar') {
    $obTFrotaCombustivel->setDado('cod_veiculo', $rsVeiculo->getCampo('cod_veiculo') );
    $obTFrotaCombustivel->recuperaCombustivelVeiculo($rsCombustivelVeiculo );
} else {
    $rsCombustivelVeiculo = new RecordSet();
}
//seta os combustiveis selecionados
$obISelectMultiploCombustivel->SetNomeLista2 ('inCodCombustivelSelecionados');
$obISelectMultiploCombustivel->setCampoId2   ('cod_combustivel');
$obISelectMultiploCombustivel->setCampoDesc2 ('nom_combustivel');
$obISelectMultiploCombustivel->SetRecord2    ( $rsCombustivelVeiculo );

//instancia um radio para a origem do veiculo
$obRdOrigemProprio = new Radio();
$obRdOrigemProprio->setRotulo( 'Origem do Bem' );
$obRdOrigemProprio->setTitle ( 'Selecione a origem do bem.' );
$obRdOrigemProprio->setName  ( 'stOrigemBem' );
$obRdOrigemProprio->setId    ( 'stOrigemBemProprio' );
$obRdOrigemProprio->setLabel ( 'Veículo Próprio' );
$obRdOrigemProprio->setValue ( 'proprio' );
$obRdOrigemProprio->setNull  ( false );
$obRdOrigemProprio->obEvento->setOnClick( "ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&stOrigem='+this.value+'&inCodVeiculo=".$rsVeiculo->getCampo('cod_veiculo')."','montaOrigem' ); ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&stOrigem='+this.value,'montaResponsavel');" );

$obRdOrigemTerceiro = new Radio();
$obRdOrigemTerceiro->setRotulo( 'Origem do Bem' );
$obRdOrigemTerceiro->setTitle ( 'Selecione a origem do bem.' );
$obRdOrigemTerceiro->setName  ( 'stOrigemBem' );
$obRdOrigemTerceiro->setId    ( 'stOrigemBemTerceiros' );
$obRdOrigemTerceiro->setLabel ( 'Veículo de Terceiros' );
$obRdOrigemTerceiro->setValue ( 'terceiro' );
$obRdOrigemTerceiro->setNull  ( false );
$obRdOrigemTerceiro->obEvento->setOnClick( "ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&stOrigem='+this.value+'&inCodVeiculo=".$rsVeiculo->getCampo('cod_veiculo')."','montaOrigem' ); ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&stOrigem='+this.value,'montaResponsavel');" );

//span para o tipo de origem: proprio/terceiros
$obSpnOrigem = new Span();
$obSpnOrigem->setId( 'spnOrigem' );

$obHdnOrigem = new hiddenEval;
$obHdnOrigem->setName('hdnOrigem');
$obHdnOrigem->setId('hdnOrigem');

//span para a placa/prefixo
$obSpnPrefixoPlaca = new Span();
$obSpnPrefixoPlaca->setId( 'spnPrefixoPlaca' );

//instancia textbox para o chassi
$obTxtChassi = new TextBox();
$obTxtChassi->setRotulo( 'Chassi' );
$obTxtChassi->setTitle ( 'Informe o chassi do veículo.' );
$obTxtChassi->setName  ( 'stChassi' );
$obTxtChassi->setNull  ( true );
$obTxtChassi->setSize  ( 30 );
$obTxtChassi->setMaxLength( 30 );
$obTxtChassi->setValue( $rsVeiculo->getCampo('chassi') );

//instancia um numerico para a quilometragem
$obNumKmInicial = new TextBox();
$obNumKmInicial->setRotulo( 'Km Inicial' );
$obNumKmInicial->setTitle ( 'Informe a quilometragem inicial do veículo.' );
$obNumKmInicial->setName  ( 'inKmInicial' );
$obNumKmInicial->setId    ( 'inKmInicial' );
$obNumKmInicial->setNull  ( true );
$obNumKmInicial->setSize  ( 6 );
$obNumKmInicial->setValue (($rsVeiculo->getCampo('km_inicial') != '') ? $rsVeiculo->getCampo('km_inicial') : '' );

//instancia um inteiro para o numero do certificado
$obNumCertificado = new Inteiro();
$obNumCertificado->setRotulo( 'Renavam do Veículo' );
$obNumCertificado->setTitle ( 'Informe o número do Renavam do veículo.' );
$obNumCertificado->setName  ( 'inNumCertificado' );
$obNumCertificado->setNull  ( true );
$obNumCertificado->inMaxLength = 14;
$obNumCertificado->setValue( $rsVeiculo->getCampo('num_certificado') );

//instancia um componente ano para a fabricacao
$obStAnoFabricacao = new Exercicio();
$obStAnoFabricacao->setName( 'stAnoFabricacao' );
$obStAnoFabricacao->setRotulo( 'Ano da Fabricação' );
$obStAnoFabricacao->setTitle ( 'Informe o ano de fabricação do veículo.' );
$obStAnoFabricacao->setValue( $rsVeiculo->getCampo('ano_fabricacao') );
$obStAnoFabricacao->setMascara('9999');
$obStAnoFabricacao->setMinLength(4);

//instancia um componente ano para o modelo
$obStAnoModelo = new Exercicio();
$obStAnoModelo->setName( 'stAnoModelo' );
$obStAnoModelo->setRotulo( 'Ano do Modelo' );
$obStAnoModelo->setTitle ( 'Informe o ano do modelo do veículo.' );
$obStAnoModelo->setValue( $rsVeiculo->getCampo('ano_modelo') );
$obStAnoModelo->setMascara('9999');
$obStAnoModelo->setMinLength(4);

//instancia um textbox para a categoria do veiculo
$obTxtCategoriaVeiculo = new TextBox();
$obTxtCategoriaVeiculo->setRotulo( 'Categoria do Veículo (CRLV)' );
$obTxtCategoriaVeiculo->setTitle ( 'Informe a categoria do veículo.' );
$obTxtCategoriaVeiculo->setName  ( 'stCategoriaVeiculo' );
$obTxtCategoriaVeiculo->setNull  ( true );
$obTxtCategoriaVeiculo->setMaxLength( 20 );
$obTxtCategoriaVeiculo->setSize  ( 20 );
$obTxtCategoriaVeiculo->setValue( $rsVeiculo->getCampo('categoria') );

//instancia textbox para a cor do veiculo
$obTxtCor = new TextBox();
$obTxtCor->setRotulo( 'Cor' );
$obTxtCor->setTitle ( 'Informe a cor do veículo' );
$obTxtCor->setName  ( 'stCor' );
$obTxtCor->setNull  ( true );
$obTxtCor->setValue( $rsVeiculo->getCampo('cor') );

//instancia um textbox para a capacidade
$obTxtCapacidade = new TextBox();
$obTxtCapacidade->setRotulo( 'Capacidade' );
$obTxtCapacidade->setTitle ( 'Informe a capacidade do veículo.' );
$obTxtCapacidade->setName  ( 'stCapacidade' );
$obTxtCapacidade->setNull  ( true );
$obTxtCapacidade->setMaxLength( 20 );
$obTxtCapacidade->setSize  ( 20 );
$obTxtCapacidade->setValue( $rsVeiculo->getCampo('capacidade') );

//instancia um textbox para a potencia
$obTxtPotencia = new TextBox();
$obTxtPotencia->setRotulo( 'Potência' );
$obTxtPotencia->setTitle ( 'Informe a potência do veículo.' );
$obTxtPotencia->setName  ( 'stPotencia' );
$obTxtPotencia->setNull  ( true );
$obTxtPotencia->setMaxLength( 20 );
$obTxtPotencia->setSize  ( 20 );
$obTxtPotencia->setValue( $rsVeiculo->getCampo('potencia') );

//instnacia um textbox para a cilindrada
$obTxtCilindrada = new TextBox();
$obTxtCilindrada->setRotulo( 'Cilindrada' );
$obTxtCilindrada->setTitle ( 'Informe a cilindrada do veículo.' );
$obTxtCilindrada->setName  ( 'stCilindrada' );
$obTxtCilindrada->setNull  ( true );
$obTxtCilindrada->setMaxLength( 20 );
$obTxtCilindrada->setSize  ( 20 );
$obTxtCilindrada->setValue( $rsVeiculo->getCampo('cilindrada') );

//instancia um componente data para a aquisicao
$obDtAquisicao = new Data();
$obDtAquisicao->setRotulo( 'Data da Aquisição' );
$obDtAquisicao->setTitle ( 'Informe a data de aquisição.' );
$obDtAquisicao->setName  ( 'dtAquisicao' );
$obDtAquisicao->setNull  ( false );
$obDtAquisicao->setValue ( $rsVeiculo->getCampo('dt_aquisicao') );

//recupera os as categorias de habilitacao
$obTFrotaCategoriaHabilitacao = new TFrotaCategoriaHabilitacao();
$obTFrotaCategoriaHabilitacao->recuperaTodos( $rsCategoriaHabilitacao, ' ORDER BY nom_categoria ' );

//instancia um select para a habilitacao exigida
$obSelectHabilitacao = new Select();
$obSelectHabilitacao->setRotulo    ( 'Habilitação Exigida' );
$obSelectHabilitacao->setTitle     ( 'Selecione a habilitação exigida pelo veículo.' );
$obSelectHabilitacao->setName      ( 'slHabilitacao' );
$obSelectHabilitacao->setId        ( 'slHabilitacao' );
$obSelectHabilitacao->addOption    ( '','Selecione'  );
$obSelectHabilitacao->setCampoId   ( 'cod_categoria' );
$obSelectHabilitacao->setCampoDesc ( 'nom_categoria' );
$obSelectHabilitacao->preencheCombo( $rsCategoriaHabilitacao );
$obSelectHabilitacao->setValue     ( $rsVeiculo->getCampo('cod_categoria') );
$obSelectHabilitacao->setNull      ( false );

$obRdControleInternoSim = new Radio();
$obRdControleInternoSim->setRotulo   ( "Atestado pelo Controle Interno" );
$obRdControleInternoSim->setName     ( "boControleInterno" );
$obRdControleInternoSim->setId       ( "boControleInterno" ); 
$obRdControleInternoSim->setLabel    ( "Sim"  );
$obRdControleInternoSim->setValue    ( "true" );
$obRdControleInternoSim->setChecked  (($boControleInterno == 't'));
$obRdControleInternoSim->setNull     ( false  );

$obRdControleInternoNao = new Radio();
$obRdControleInternoNao->setRotulo   ( "Atestado pelo Controle Interno" );
$obRdControleInternoNao->setName     ( "boControleInterno" );
$obRdControleInternoNao->setId       ( "boControleInterno" ); 
$obRdControleInternoNao->setLabel    ( "Não"   );
$obRdControleInternoNao->setValue    ( "false" );
$obRdControleInternoNao->setChecked  (($boControleInterno == 'f'));
$obRdControleInternoNao->setNull     ( false   );

// TCERN - select para as categorias necessárias

if (SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio()) == '20') {

    $obTTCERNCategoriaVeiculoTCE = new TTCERNCategoriaVeiculoTCE();
    $obTTCERNCategoriaVeiculoTCE->recuperaTodos($rsCategoriaVeiculo);

    if ($stAcao == 'alterar') {
        $obTTCERNVeiculoCategoriaVinculo = new TTCERNVeiculoCategoriaVinculo();
        $obTTCERNVeiculoCategoriaVinculo->recuperaTodos($rsCategoriaVinculo, " WHERE cod_veiculo = ".$rsVeiculo->getCampo('cod_veiculo'));
    }

    $obCmbCategoriaVeiculo = new Select();
    $obCmbCategoriaVeiculo->setRotulo    ( 'Categoria do Veículo' );
    $obCmbCategoriaVeiculo->setTitle     ( 'Selecione a categoria do veículo.' );
    $obCmbCategoriaVeiculo->setName      ( 'inCategoriaVeiculo' );
    $obCmbCategoriaVeiculo->setId        ( 'inCategoriaVeiculo' );
    $obCmbCategoriaVeiculo->addOption    ( '','Selecione'  );
    $obCmbCategoriaVeiculo->setCampoId   ( 'cod_categoria' );
    $obCmbCategoriaVeiculo->setCampoDesc ( 'nom_categoria' );
    $obCmbCategoriaVeiculo->preencheCombo( $rsCategoriaVeiculo );
    
    if ($rsCategoriaVinculo) {
        $obCmbCategoriaVeiculo->setValue     ( $rsCategoriaVinculo->getCampo('cod_categoria') );
    }

    $obCmbCategoriaVeiculo->setNull      ( false );
}

$obSelectHabilitacao = new Select();
$obSelectHabilitacao->setRotulo    ( 'Habilitação Exigida' );
$obSelectHabilitacao->setTitle     ( 'Selecione a habilitação exigida pelo veículo.' );
$obSelectHabilitacao->setName      ( 'slHabilitacao' );
$obSelectHabilitacao->setId        ( 'slHabilitacao' );
$obSelectHabilitacao->addOption    ( '','Selecione'  );
$obSelectHabilitacao->setCampoId   ( 'cod_categoria' );
$obSelectHabilitacao->setCampoDesc ( 'nom_categoria' );
$obSelectHabilitacao->preencheCombo( $rsCategoriaHabilitacao );
$obSelectHabilitacao->setValue     ( $rsVeiculo->getCampo('cod_categoria') );
$obSelectHabilitacao->setNull      ( false );

//instancia um textbox para a potencia
$obTxtNumPassageiro = new Inteiro();
$obTxtNumPassageiro->setRotulo( 'Número de Passageiros' );
$obTxtNumPassageiro->setTitle ( 'Informe a número de passageiros.' );
$obTxtNumPassageiro->setName  ( 'inNumPassageiro' );
$obTxtNumPassageiro->setNull  ( true );
$obTxtNumPassageiro->setMaxLength( 20 );
$obTxtNumPassageiro->setSize  ( 20 );
$obTxtNumPassageiro->setValue( $rsVeiculo->getCampo('num_passageiro') );

//instancia um textbox para a potencia
$obTxtCapacidadeTanque = new Inteiro();
$obTxtCapacidadeTanque->setRotulo( 'Capacidade do tanque' );
$obTxtCapacidadeTanque->setTitle ( 'Informe a capacidade do tanque de combustí­vel em litros.' );
$obTxtCapacidadeTanque->setName  ( 'inCapacidadeTanque' );
$obTxtCapacidadeTanque->setNull  ( false );
$obTxtCapacidadeTanque->setMaxLength( 20 );
$obTxtCapacidadeTanque->setSize  ( 20 );
$obTxtCapacidadeTanque->setValue( $rsVeiculo->getCampo('capacidade_tanque') );

$obSpnResponsavel = new Span();
$obSpnResponsavel->setId( 'spnResponsavel' );

//span para a Locação de Veículos
$obSpnLocacao = new Span();
$obSpnLocacao->setId ( 'spnLocacao' );
//$obSpnLocacao->setName ( 'spnLocacao' );

/****
* Cessão
****/

//cria um hidden para o id
$obHdnIdCessao = new Hidden();
$obHdnIdCessao->setName( 'hdnIdCessao' );
$obHdnIdCessao->setId( 'hdnIdCessao' );

//cria um form
$obFormCessao = new Form();
$obFormCessao->setAction ($pgProc);
$obFormCessao->setTarget ("oculto");

//processo
$obPopUpProcesso = new IPopUpProcesso($obFormCessao);
$obPopUpProcesso->setRotulo("Processo");
$obPopUpProcesso->obCampoCod->setId('stProcessoCessao');
$obPopUpProcesso->obCampoCod->setName('stProcessoCessao');
$obPopUpProcesso->setValidar(true);
$obPopUpProcesso->setObrigatorioBarra( true );

//instancia o CGM cedente
$obCGMCedente = new IPopUpCGMVinculado( $obForm );
$obCGMCedente->setTabelaVinculo    ( 'sw_cgm_pessoa_juridica' );
$obCGMCedente->setCampoVinculo     ( 'numcgm'                 );
$obCGMCedente->setNomeVinculo      ( 'CGM Cedente'            );
$obCGMCedente->setRotulo           ( 'CGM Cedente'            );
$obCGMCedente->setTitle            ( 'Informe o CGM cedente.' );
$obCGMCedente->setName             ( 'stNomCedente'           );
$obCGMCedente->setId               ( 'stNomCedente'           );
$obCGMCedente->obCampoCod->setName ( 'inCodCedente'           );
$obCGMCedente->obCampoCod->setId   ( 'inCodCedente'           );
$obCGMCedente->setObrigatorioBarra( true );
$obCGMCedente->setNull ( true );

//ĩnstancia a data de início
$obDtInicioCessao = new Data();
$obDtInicioCessao->setRotulo( 'Início' );
$obDtInicioCessao->setTitle ( 'Informe a data de início da cessão.' );
$obDtInicioCessao->setName  ( 'dtInicioCessao' );
$obDtInicioCessao->setId    ( 'dtInicioCessao' );
$obDtInicioCessao->setObrigatorioBarra( true );

//ĩnstancia a data de término
$obDtTerminoCessao = new Data();
$obDtTerminoCessao->setRotulo( 'Término' );
$obDtTerminoCessao->setTitle ( 'Informe a data de término da cessão.' );
$obDtTerminoCessao->setName  ( 'dtTerminoCessao' );
$obDtTerminoCessao->setId    ( 'dtTerminoCessao' );
$obDtTerminoCessao->setObrigatorioBarra( true );

$obSpnListaCessao = new Span();
$obSpnListaCessao->setId ( 'spnListaCessao' );

//define objeto buttion para incluir cessao
$obBtnIncluirCessao = new Button;
$obBtnIncluirCessao->setValue             ( "Incluir" );
$obBtnIncluirCessao->setId                ( "incluiDadosCessao" );
$obBtnIncluirCessao->obEvento->setOnClick ( "montaParametrosGET('incluirDadosCessao','stProcessoCessao,inCodCedente,stNomCedente,dtInicioCessao,dtTerminoCessao');" );

//Define Objeto Button para Limpar cessao
$obBtnLimparCessao = new Button;
$obBtnLimparCessao->setValue             ( "Limpar" );
$obBtnLimparCessao->obEvento->setOnClick ( "montaParametrosGET('limparDadosCessao');" );

/****
 * Controle de documentos
****/

//cria um hidden para o id
$obHdnId = new Hidden();
$obHdnId->setName( 'hdnId' );
$obHdnId->setId( 'hdnId' );

$obHdnCodEntidade = new Hidden;
$obHdnCodEntidade->setName ("hdnInCodEntidade" );
$obHdnCodEntidade->setValue($rsVeiculo->getCampo('cod_entidade'));

$obHdnCodUnidade = new Hidden;
$obHdnCodUnidade->setName ("hdnInCodUnidade" );
$obHdnCodUnidade->setValue($rsVeiculo->getCampo('num_unidade'));  

//recupera todos os documentos da base
$obTFrotaDocumento = new TFrotaDocumento();
$obTFrotaDocumento->recuperaTodos( $rsDocumentos );

//instancia um select para os documentos
$obSlDocumento = new Select();
$obSlDocumento->setRotulo( 'Documento' );
$obSlDocumento->setTitle ( 'Selecione o documento do veículo.' );
$obSlDocumento->setName( 'stDocumento' );
$obSlDocumento->setId( 'stDocumento' );
$obSlDocumento->addOption( '', 'Selecione' );
$obSlDocumento->setCampoId( 'cod_documento' );
$obSlDocumento->setCampoDesc( 'nom_documento' );
$obSlDocumento->preencheCombo( $rsDocumentos );
$obSlDocumento->setObrigatorioBarra( true );

//instancia o componente exercicio para o documento
$obExercicio = new Exercicio();
$obExercicio->setId( 'stExercicio' );
$obExercicio->setValue( Sessao::getExercicio() );
$obExercicio->setRotulo( 'Ano' );
$obExercicio->setTitle( 'Informe o ano do vencimento do documento.' );
$obExercicio->setObrigatorioBarra( true );
$obExercicio->setNull( true );

//instancia o componente mes
$obMes = new Mes();
$obMes->setName( 'inMes' );
$obMes->obMes->setId( 'inMes' );
$obMes->setRotulo( 'Mês' );
$obMes->setTitle ( 'Selecione o mês do vencimento do documento.' );
$obMes->setObrigatorioBarra( true );

//instancia radio para a situacao do documento
$obRdSituacaoPago = new Radio();
$obRdSituacaoPago->setRotulo( 'Situação' );
$obRdSituacaoPago->setTitle ( 'Informe a situação do documento do veículo.' );
$obRdSituacaoPago->setLabel ( 'Pago' );
$obRdSituacaoPago->setValue ( 'pago' );
$obRdSituacaoPago->setName  ( 'stSituacao' );
$obRdSituacaoPago->setId    ( 'stSituacao1' );
$obRdSituacaoPago->setObrigatorioBarra( true );
$obRdSituacaoPago->obEvento->setOnClick( "ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&stSituacao='+this.value,'montaEmpenho' );" );

$obRdSituacaoNaoPago = new Radio();
$obRdSituacaoNaoPago->setRotulo( 'Situação' );
$obRdSituacaoNaoPago->setTitle ( 'Informe a situação do documento do veículo.' );
$obRdSituacaoNaoPago->setLabel ( 'Não Pago' );
$obRdSituacaoNaoPago->setValue ( 'naopago' );
$obRdSituacaoNaoPago->setName  ( 'stSituacao' );
$obRdSituacaoNaoPago->setId    ( 'stSituacao2' );
$obRdSituacaoNaoPago->setObrigatorioBarra( true );
$obRdSituacaoNaoPago->obEvento->setOnClick( "ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&stSituacao='+this.value,'montaEmpenho' );" );

//cria um span para os dados do empenho
$obSpnEmpenho = new Span();
$obSpnEmpenho->setId( 'spnEmpenho' );

//define objeto buttion para incluir documento
$obBtnIncluirDocumento = new Button;
$obBtnIncluirDocumento->setValue             ( "Incluir"                                      );
$obBtnIncluirDocumento->setId                ( "incluiDocumento"                                );
$obBtnIncluirDocumento->obEvento->setOnClick ( "montaParametrosGET('incluirDocumento','stDocumento,stExercicio,inMes,stSituacao,stExercicioEmpenho,inCodEntidadeOculto,inCodigoEmpenho,stNomFornecedor');"      );

//Define Objeto Button para Limpar documentos
$obBtnLimparDocumento = new Button;
$obBtnLimparDocumento->setValue             ( "Limpar"          );
$obBtnLimparDocumento->obEvento->setOnClick ( "montaParametrosGET('limparDocumentos');" );

//cria um span para os documentos
$obSpnDocumentos = new Span();
$obSpnDocumentos->setId( 'spnDocumentos' );

//cria um span para os documentos
$obSpnInfracao = new Span();
$obSpnInfracao->setId( 'spnInfracao' );

//monta o formulário
$obFormulario = new Formulario;
$obFormulario->addForm      ( $obForm );
$obFormulario->addHidden    ( $obHdnAcao );
$obFormulario->addHidden    ( $obHdnCtrl );
$obFormulario->addTitulo    ( 'Dados do Veículo' );

if ($stAcao == 'alterar') {
    $obFormulario->addHidden    ($obHdnCodEntidade);
    $obFormulario->addHidden    ($obHdnCodUnidade);
    $obFormulario->addComponente( $obTxtCodVeiculo );
}
$obISelectModeloVeiculo->geraFormulario( $obFormulario );
$obFormulario->addComponente( $obSelectTipoVeiculo );
$obFormulario->addComponente( $obISelectMultiploCombustivel );
$obFormulario->agrupaComponentes( array( $obRdOrigemProprio, $obRdOrigemTerceiro ) );
$obFormulario->addSpan      ( $obSpnOrigem );
$obFormulario->addHidden    ($obHdnOrigem,true);

$obFormulario->addSpan      ( $obSpnPrefixoPlaca );
$obFormulario->addComponente( $obTxtChassi );
$obFormulario->addComponente( $obNumKmInicial );
$obFormulario->addComponente( $obNumCertificado );
$obFormulario->addComponente( $obStAnoFabricacao );
$obFormulario->addComponente( $obStAnoModelo );
$obFormulario->addComponente( $obTxtCategoriaVeiculo );
$obFormulario->addComponente( $obTxtCapacidade );
$obFormulario->addComponente( $obTxtPotencia );
$obFormulario->addComponente( $obTxtCilindrada );
$obFormulario->addComponente( $obTxtNumPassageiro );
$obFormulario->addComponente( $obTxtCapacidadeTanque );
$obFormulario->addComponente( $obTxtCor );
$obFormulario->addComponente( $obDtAquisicao );
$obFormulario->addComponente( $obSelectHabilitacao );
$obFormulario->agrupaComponentes( array( $obRdControleInternoSim, $obRdControleInternoNao) );
$obFormulario->addSpan ( $obSpnLocacao );

if (SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio()) == '20') {

    $obFormulario->addTitulo    ( 'Dados TCE-RN' );
    $obFormulario->addComponente( $obCmbCategoriaVeiculo );

}

$obFormulario->addSpan      ( $obSpnResponsavel );

$obFormulario->addTitulo    ( 'Cessão' );
$obFormulario->addHidden    ( $obHdnIdCessao );
$obFormulario->addComponente( $obPopUpProcesso );
$obFormulario->addComponente( $obCGMCedente );
$obFormulario->addComponente( $obDtInicioCessao );
$obFormulario->addComponente( $obDtTerminoCessao );
$obFormulario->defineBarra  ( array( $obBtnIncluirCessao, $obBtnLimparCessao ) );
$obFormulario->addSpan      ( $obSpnListaCessao );

/*
$obFormulario->addSpan      ( $obSpnEmpenho );
$obFormulario->defineBarra  ( array( $obBtnIncluirCessao, $obBtnLimparCessao ) );
$obFormulario->addSpan      ( $obSpnDocumentos );
$obFormulario->addSpan      ( $obSpnInfracao );
*/

$obFormulario->addTitulo    ( 'Controle de Documentos' );
$obFormulario->addHidden    ( $obHdnId );
$obFormulario->addComponente( $obSlDocumento );
$obFormulario->addComponente( $obExercicio );
$obFormulario->addComponente( $obMes );
$obFormulario->agrupaComponentes( array( $obRdSituacaoPago, $obRdSituacaoNaoPago ) );
$obFormulario->addSpan      ( $obSpnEmpenho );
$obFormulario->defineBarra  ( array( $obBtnIncluirDocumento, $obBtnLimparDocumento ) );
$obFormulario->addSpan      ( $obSpnDocumentos );
$obFormulario->addSpan      ( $obSpnInfracao );

if ($stAcao == 'alterar') {
    $obFormulario->Cancelar( $pgList.'?'.Sessao::getId().'&stAcao='.$stAcao.'&pg='.$request->get('pg').'&pos='.$request->get('pos') );
} else {
    $obFormulario->OK(true);
}
$obFormulario->show();

if ($stAcao == 'alterar') {
    if ( $rsVeiculo->getCampo('proprio') == 't' ) {
        $stOrigem = 'proprio';
    } elseif ( $rsVeiculo->getCampo('proprio') == 'f' ) {
        $stOrigem = 'terceiro';
    }
    $jsOnLoad  = "ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&inCodVeiculo=".$_REQUEST['inCodVeiculo']."&inCodMarca=".$rsVeiculo->getCampo('cod_marca')."&inCodModelo=".$rsVeiculo->getCampo('cod_modelo')."&stOrigem=".$rsVeiculo->getCampo('proprio')."&inCodEntidade=".$rsVeiculo->getCampo('cod_entidade')."&inCodUnidade=".$rsVeiculo->getCampo('num_unidade')."','montaAlterar');";
    $jsOnLoad .= "ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&stOrigem=".$stOrigem."&inCodPropriedade=".$rsVeiculo->getCampo('cod_propriedade')."&stNomPropriedade=".$rsVeiculo->getCampo('nom_propriedade')."&stLocalizacao=".$rsVeiculo->getCampo('localizacao')."&inCodVeiculo=".$rsVeiculo->getCampo('cod_veiculo')."','montaOrigem' );";
    $jsOnLoad .= "ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&stOrigem=".$stOrigem."&inCodResponsavel=".$rsVeiculo->getCampo('cod_responsavel')."&stNomResponsavel=".$rsVeiculo->getCampo('nom_responsavel')."&dtInicio=".$rsVeiculo->getCampo('dt_inicio')."','montaResponsavel');";
    if ( $rsVeiculo->getCampo('proprio') == 't' ) {
        $jsOnLoad .= "ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&inCodBem=".$rsVeiculo->getCampo('cod_propriedade')."','preencheDetalheBem');";
    } else { 
        $jsOnLoad .= "ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&stOrigem=".$stOrigem."&inCodPropriedade=".$rsVeiculo->getCampo('cod_propriedade')."&stNomPropriedade=".$rsVeiculo->getCampo('nom_propriedade')."&stLocalizacao=".$rsVeiculo->getCampo('localizacao')."&stExercicioEntidade=".$rsVeiculo->getCampo('exercicio_entidade')."&inCodEntidade=".$rsVeiculo->getCampo('cod_entidade')."&inCodEntidade=".$rsVeiculo->getCampo('cod_entidade')."&inCodOrgao=".$rsVeiculo->getCampo('num_orgao')."&inCodUnidade=".$rsVeiculo->getCampo('num_unidade')."&inCodVeiculo=".$rsVeiculo->getCampo('cod_veiculo')."','montaOrigem' );";
    }
    
    $jsOnLoad .= "ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&slTipoVeiculo=".$rsVeiculo->getCampo('cod_tipo_veiculo')."&stNumPlaca=".$rsVeiculo->getCampo('placa_masc')."&stPrefixo=".$rsVeiculo->getCampo('prefixo')."','montaPrefixoPlaca');";
    $jsOnLoad .= "ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&inCodVeiculo=".$_REQUEST['inCodVeiculo']."','carregarListaInfracao');";
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
