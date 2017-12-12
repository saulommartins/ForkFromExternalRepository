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

    $Id: FMManterManutencao.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-03.02.14
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GP_FRO_MAPEAMENTO.'TFrotaManutencao.class.php' );
include_once( CAM_GP_FRO_MAPEAMENTO.'TFrotaManutencaoItem.class.php' );
include_once( CAM_GP_FRO_COMPONENTES.'IPopUpVeiculo.class.php' );
include_once( CAM_GP_FRO_COMPONENTES.'IPopUpItem.class.php' );
include_once ( CAM_GF_ORC_COMPONENTES.'ITextBoxSelectEntidadeGeral.class.php' );

$stPrograma = "ManterManutencao";
$pgFilt   = "FL".$stPrograma.".php";
$pgList   = "LS".$stPrograma.".php";
$pgForm   = "FM".$stPrograma.".php";
$pgProc   = "PR".$stPrograma.".php";
$pgOcul   = "OC".$stPrograma.".php";
$pgJs     = "JS".$stPrograma.".js";

//include_once( $pgJs );

$stAcao = $request->get('stAcao');

Sessao::write('arItensAutorizacao' , array());

if ($stAcao == 'anular' || $stAcao == 'alterar') {
    //recupera os dados da manutencao
    $obTFrotaManutencao = new TFrotaManutencao();
    $obTFrotaManutencao->setDado('cod_manutencao', $_REQUEST['inCodManutencao'] );
    $obTFrotaManutencao->setDado('exercicio', $_REQUEST['stExercicio'] );
    $obTFrotaManutencao->recuperaManutencaoAnalitica( $rsManutencao );

    //cria um textbox para o codigo da manutencao
    $obInCodManutencao = new TextBox();
    $obInCodManutencao->setRotulo( 'Código da Manutenção' );
    $obInCodManutencao->setName( 'inCodManutencao' );
    $obInCodManutencao->setValue( $rsManutencao->getCampo('cod_manutencao').'/'.$rsManutencao->getCampo('exercicio') );
    $obInCodManutencao->setLabel( true );

    //recupera os itens da manutencao
    $obTFrotaManutencaoItem = new TFrotaManutencaoItem();
    $obTFrotaManutencaoItem->setDado('cod_manutencao', $_REQUEST['inCodManutencao'] );
    $obTFrotaManutencaoItem->setDado('exercicio', $_REQUEST['stExercicio'] );
    $obTFrotaManutencaoItem->recuperaManutencaoItens( $rsManutencaoItens );

    //monta na sessao todos os itens
    $inCount = 0;
    while ( !$rsManutencaoItens->eof() ) {
        //coloca na sessao os dados do item
        $arItensAutorizacao[$inCount]['id'         ] = $inCount;
        $arItensAutorizacao[$inCount]['cod_item'   ] = $rsManutencaoItens->getCampo('cod_item');
        $arItensAutorizacao[$inCount]['descricao'  ] = $rsManutencaoItens->getCampo('descricao');
        $arItensAutorizacao[$inCount]['quantidade' ] = $rsManutencaoItens->getCampo('quantidade');
        $arItensAutorizacao[$inCount]['valor'      ] = $rsManutencaoItens->getCampo('valor');
        $arItensAutorizacao[$inCount]['tipo'       ] = $rsManutencaoItens->getCampo('nom_tipo');
        $arItensAutorizacao[$inCount]['combustivel'] = ( $rsManutencaoItens->getCampo('cod_tipo') == 1 ) ? true : false;
        $arItensAutorizacao[$inCount]['alteravel'  ] = ($rsManutencaoItens->getCampo('alteravel') == 't' ) ? true : false;
        $inCount++;
        $rsManutencaoItens->proximo();
    }

    Sessao::write('arItensAutorizacao' , $arItensAutorizacao);
    //monta a lista de itens
    $jsOnLoad .= "ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&boReadOnly=".(($stAcao == 'alterar') ? 'false' : 'true')."','montaListaItens');";
    if ($stAcao == 'alterar') {
        $jsOnLoad .= "ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&inCodVeiculo=".$_REQUEST['inCodVeiculo']."&inCodManutencao=".$_REQUEST['inCodManutencao']."','montaQuilometragem');";
    }
} else {
    $rsManutencao = new RecordSet();
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

//cria um hidden para o id do item
$obHdnId = new Hidden();
$obHdnId->setId('hdnId');

//monta a mascara para a autorizacao
$stMaxAutorizacao = SistemaLegado::pegaDado("cod_autorizacao","frota.autorizacao", " order by cod_autorizacao desc limit 1");
$stMascara =  str_pad('9',strlen($stMaxAutorizacao),'9',STR_PAD_LEFT).'/9999';

//instancia um textbox para a autorizacao
$obInCodAutorizacao = new TextBox();
$obInCodAutorizacao->setName( 'inCodAutorizacao' );
$obInCodAutorizacao->setId( 'inCodAutorizacao' );
$obInCodAutorizacao->setRotulo( 'Código da Autorização' );
$obInCodAutorizacao->setValue( ( $rsManutencao->getCampo('cod_autorizacao') != '' ) ? $rsManutencao->getCampo('cod_autorizacao').'/'.$rsManutencao->getCampo('exercicio_autorizacao') : '' );
if ($stAcao == 'anular') {
    $obInCodAutorizacao->setLabel( true );
} else {
    $obInCodAutorizacao->obEvento->setOnChange( "montaParametrosGET('preencheAutorizacao','inCodAutorizacao,stAcao');" );
    $obInCodAutorizacao->setMascara( $stMascara );
    $obInCodAutorizacao->setTitle( 'Informe o código da autorização de abastecimento.' );
}

//instancia o componente IPopUpVeiculo
$obIPopUpVeiculo = new IPopUpVeiculo($obForm);
$obIPopUpVeiculo->obCampoCod->obEvento->setOnBlur("montaParametrosGET('montaVeiculo','inCodVeiculo,inCodAutorizacao');");
$obIPopUpVeiculo->obCampoCod->setValue( $rsManutencao->getCampo('cod_veiculo') );
$obIPopUpVeiculo->setValue( $rsManutencao->getCampo('nom_modelo') );
if ($stAcao == 'anular') {
    $obIPopUpVeiculo->setLabel( true );
} else {
    $obIPopUpVeiculo->setNull( false );
}

//instancia um textbox para o numero da placa
$obTxtPlaca = new TextBox();
$obTxtPlaca->setRotulo( 'Placa do Veículo' );
$obTxtPlaca->setName  ( 'stNumPlaca' );
$obTxtPlaca->setId    ( 'stNumPlaca' );
$obTxtPlaca->setValue( ($rsManutencao->getCampo('placa_masc') != '-') ? $rsManutencao->getCampo('placa_masc') : '' );
if ($stAcao == 'anular') {
    $obTxtPlaca->setLabel( true );
} else {
    $obTxtPlaca->setTitle ( 'Informe a placa do veículo.' );
    $obTxtPlaca->obEvento->setOnKeyUp( "mascaraPlacaVeiculo(this);" );
    $obTxtPlaca->obEvento->setOnChange( "mascaraPlacaVeiculo(this);" );
    $obTxtPlaca->obEvento->setOnChange("montaParametrosGET('montaVeiculo','stNumPlaca,inCodVeiculo,inCodAutorizacao');");
}

//instancia textbox para o prefixo
$obTxtPrefixo = new TextBox();
$obTxtPrefixo->setRotulo( 'Prefixo' );
$obTxtPrefixo->setName  ( 'stPrefixo' );
$obTxtPrefixo->setId    ( 'stPrefixo' );
$obTxtPrefixo->setValue( $rsManutencao->getCampo( 'prefixo' ) );
if ($stAcao == 'anular') {
    $obTxtPrefixo->setLabel( true );
} else {
    $obTxtPrefixo->setTitle ( 'Informe prefixo do veículo.' );
    $obTxtPrefixo->setSize  ( 15 );
    $obTxtPrefixo->setMaxLength( 15 );
    $obTxtPrefixo->obEvento->setOnChange("montaParametrosGET('montaVeiculo','stPrefixo,inCodAutorizacao');");
}

//instancia um data
$obDtManutencao = new Data();
$obDtManutencao->setName( 'dtManutencao' );
$obDtManutencao->setId( 'dtManutencao' );
$obDtManutencao->setRotulo( 'Data' );
$obDtManutencao->setValue( $rsManutencao->getCampo( 'dt_manutencao' ) );
if ($stAcao == 'anular') {
    $obDtManutencao->setLabel( true );
} else {
    $obDtManutencao->setTitle( 'Informe a data da manutenção.' );
    $obDtManutencao->setNull( false );
    $obDtManutencao->setValue( date('d/m/Y') );
}

if ($stAcao == 'anular') {
    $obTxtQuilometragem = new TextBox();
    $obTxtQuilometragem->setName( 'inQuilometragem' );
    $obTxtQuilometragem->setRotulo('Quilometragem');
    $obTxtQuilometragem->setValue( $rsManutencao->getCampo('km') );
    $obTxtQuilometragem->setLabel( true );
} else {
    //instancia um span para a quilometragem
    $obSpnQuilometragem = new Span();
    $obSpnQuilometragem->setId( 'spnQuilometragem' );
}

//instancia um textarea para a observacao
$obTxtObservacao = new TextArea();
$obTxtObservacao->setName( 'stObservacao' );
$obTxtObservacao->setNull( true );
$obTxtObservacao->setRotulo( 'Observação' );
$obTxtObservacao->setValue( $rsManutencao->getCampo('observacao') );
if ($stAcao == 'anular') {
    $obTxtObservacao->setLabel( true );
} else {
    $obTxtObservacao->setTitle( 'Informe a observação da manutenção.' );
}

/****
* Dados do empenho
****/

//instancia um componente exercicio
if ($stAcao == 'anular') {
    $stExercicioEmpenho = new TextBox();
    $stExercicioEmpenho->setTitle( '' );
    $stExercicioEmpenho->setLabel( true );
} else {
    $stExercicioEmpenho = new Exercicio;
}
$stExercicioEmpenho->setNull( true );
$stExercicioEmpenho->setId('stExercicioEmpenho');
$stExercicioEmpenho->setName('stExercicioEmpenho');
$stExercicioEmpenho->setValue( ($rsManutencao->getCampo('exercicio_empenho') != '' ) ? $rsManutencao->getCampo('exercicio_empenho') : Sessao::getExercicio() );
// Define Objeto TextBox para Codigo da Entidade
if ($stAcao == 'anular') {
    $obLblEntidade = new Label();
    $obLblEntidade->setRotulo('Entidade');
    $obLblEntidade->setValue( $rsManutencao->getCampo('cod_entidade').' - '.$rsManutencao->getCampo('nom_entidade') );
} else {
    $obEntidadeGeral = new ITextBoxSelectEntidadeGeral;
    $obEntidadeGeral->setCodEntidade( $rsManutencao->getCampo('cod_entidade') );
}

//define um textbox para o empenho
$obTxtEmpenho = new Inteiro();
$obTxtEmpenho->setRotulo ( "Empenho" );
$obTxtEmpenho->setId     ( "stNomFornecedor" );
$obTxtEmpenho->setName   ( 'inCodigoEmpenho' );
$obTxtEmpenho->setValue( $rsManutencao->getCampo('cod_empenho') );
if ($stAcao == 'anular') {
    $obTxtEmpenho->setLabel( true );
} else {
    $obTxtEmpenho->setTitle  ( "Informe o número do empenho."             );
}

/****
* Dados do item
****/

//instancia o componente ipopupitem
$obIPopUpItem = new IPopUpItem( $obForm );
$obIPopUpItem->setObrigatorioBarra( true );
$obIPopUpItem->setTipoConsulta( 'sem_combustivel' );

$obInQuantidade = new Quantidade();
$obInQuantidade->setName( 'inQuantidade' );
$obInQuantidade->setId( 'inQuantidade' );
$obInQuantidade->setRotulo( 'Quantidade' );
$obInQuantidade->setValue( number_format($request->get('inQuantidade'),4,',','.') );
$obInQuantidade->setObrigatorioBarra( false );
$obInQuantidade->setNull( true );
$obInQuantidade->setMinValue(null);

$obInValor = new Numerico();
$obInValor->setName( 'inValor' );
$obInValor->setId( 'inValor' );
$obInValor->setRotulo( 'Valor Total' );
$obInValor->setTitle( 'Informe o valor do abastecimento.' );
$obInValor->setObrigatorioBarra( false );
$obInValor->setNull( true );
$obInValor->setValue( number_format($request->get('inValor'),2,',','.') );

//Define Objeto Button para Incluir veiculo
$obBtnIncluirItem = new Button;
$obBtnIncluirItem->setValue             ( "Incluir"                                      );
$obBtnIncluirItem->setId                ( "incluiItem"                                );
$obBtnIncluirItem->obEvento->setOnClick ( "montaParametrosGET('incluirListaItem','inCodItem,inQuantidade,inValor');" );

//Define Objeto Button para Limpar Veiculo
$obBtnLimparItem = new Button;
$obBtnLimparItem->setValue             ( "Limpar"          );
$obBtnLimparItem->obEvento->setOnClick ( "montaParametrosGET('limparItem');" );

//instancia um span para os itens
$obSpnItens = new Span();
$obSpnItens->setId( 'spnItens' );

/******
* Dados da anulacao
******/
$obTxtObservacaoAnulacao = new TextArea();
$obTxtObservacaoAnulacao->setName( 'stObservacaoAnulacao' );
$obTxtObservacaoAnulacao->setRotulo( 'Observação' );
$obTxtObservacaoAnulacao->setTitle( 'Informe a observação da anulação.' );
$obTxtObservacaoAnulacao->setNull( true );

//monta o formulário
$obFormulario = new Formulario;
$obFormulario->addForm      ( $obForm );
$obFormulario->addHidden    ( $obHdnAcao );
$obFormulario->addHidden    ( $obHdnCtrl );
$obFormulario->addHidden    ( $obHdnId );

if ($stAcao == 'anular' || $stAcao == 'alterar') {
    $obFormulario->addTitulo    ( 'Dados da Manutenção' );
    $obFormulario->addComponente( $obInCodManutencao );
} else {
    $obFormulario->addTitulo    ( 'Incluir Manutenção de Veículos' );
}
if ( ( $stAcao == 'anular' && $rsManutencao->getCampo('cod_autorizacao') != '' ) || $stAcao != 'anular' ) {
    $obFormulario->addComponente( $obInCodAutorizacao );
}
$obFormulario->addComponente( $obIPopUpVeiculo );
$obFormulario->addComponente( $obTxtPlaca );
$obFormulario->addComponente( $obTxtPrefixo );
if ($stAcao == 'anular') {
    $obFormulario->addComponente( $obTxtQuilometragem );
} else {
    $obFormulario->addSpan      ( $obSpnQuilometragem );
}
$obFormulario->addComponente( $obDtManutencao );
$obFormulario->addComponente( $obTxtObservacao );

$obFormulario->addTitulo    ( 'Dados do Pagamento' );
$obFormulario->addComponente( $stExercicioEmpenho );
if ($stAcao == 'anular') {
    $obFormulario->addComponente( $obLblEntidade );
} else {
    $obFormulario->addComponente( $obEntidadeGeral );
}
$obFormulario->addComponente( $obTxtEmpenho );

//campo da observacao quando for anular
if ($stAcao == 'anular') {
    $obFormulario->addTitulo    ( 'Dados da Anulação' );
    $obFormulario->addComponente( $obTxtObservacaoAnulacao );
}

if ($stAcao != 'anular') {
    $obFormulario->addTitulo    ( 'Incluir Item' );
    $obFormulario->addComponente( $obIPopUpItem );
    $obFormulario->addComponente( $obInQuantidade );
    $obFormulario->addComponente( $obInValor );
    $obFormulario->defineBarra( array( $obBtnIncluirItem, $obBtnLimparItem ) );
}
$obFormulario->addSpan      ( $obSpnItens );

if ($stAcao == 'incluir') {
    $obFormulario->OK(true);
} else {
    $obFormulario->Cancelar(  $pgList.'?'.Sessao::getId().'&stAcao='.$stAcao );
}

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
