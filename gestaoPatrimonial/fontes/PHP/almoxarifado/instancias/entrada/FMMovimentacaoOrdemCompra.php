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
    * Arquivo de Formulário da Entrada por Ordem de Compra
    * Data de Criação: 12/07/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Girardi dos Santos

    $Id: FMMovimentacaoOrdemCompra.php 65631 2016-06-03 21:06:49Z michel $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GP_COM_MAPEAMENTO.'TComprasFornecedor.class.php';

//Define a função do arquivo, ex: incluir ou alterar
$stAcao = $request->get('stAcao');

//Define o nome dos arquivos PHP
$stPrograma = "MovimentacaoOrdemCompra";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

include_once( $pgJs );

// array de itens da ordem de compra
Sessao::write('arItens', array());

// array com os dados pereciveis do item
Sessao::write('arItensPerecivel', array());

// array com os atributos do item
Sessao::write('arItensAtributo', array());

// array dos itens de entrada, a partir dos item da ordem de compra
Sessao::write('arItensEntrada', array());

// array de relacionamento entre o item selecionado e a linha no array de entrada
Sessao::write('arItemLinha', array());

//#22967, Verifica se o Fornecedor do Empenho é cadastrado ou ativo em Compras->Fornecedor.
//Obrigatório ser Fornecedor Cadastrado e Ativo.
$stFornecedor = explode(" - ", $request->get('stFornecedor'));
$inCodFornecedor = $stFornecedor[0];

$obHdnFornecedor = new Hidden;
$obHdnFornecedor->setName ( "inCodFornecedor" );
$obHdnFornecedor->setId ( "inCodFornecedor" );
$obHdnFornecedor->setValue( $inCodFornecedor );

$obTComprasFornecedor = new TComprasFornecedor();
$obTComprasFornecedor->setDado("cgm_fornecedor", $inCodFornecedor);
$obTComprasFornecedor->recuperaListaFornecedor( $rsFornecedor );

if ( $rsFornecedor->getNumLinhas() < 1 || $rsFornecedor->getCampo('status')=='Inativo'){
    $stMsg = "Cadastro do Fornecedor (".$request->get('stFornecedor').") não localizado em Compras. Necessário cadastrar o fornecedor!";
    $stMsg = ($rsFornecedor->getNumLinhas() < 1) ? $stMsg : "Cadastro do Fornecedor (".$request->get('stFornecedor').") está Inativo em Compras. Necessário ativar o fornecedor!";
    sistemaLegado::alertaAviso($pgList.'?'.Sessao::getId().'&stAcao='.$stAcao , $stMsg ,"unica","aviso", Sessao::getId(), "../");
}

$arEntidade = explode('-',$request->get('stEntidade'));

//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( "" );

/*
*    Labels so seguimento inicial do formulário
*    Dados da Ordem de Compra
*/

$obLblExercicio = new Label();
$obLblExercicio->setRotulo( 'Exercício' );
$obLblExercicio->setValue ( $request->get('stExercicio') );

$obHdnExercicio = new Hidden;
$obHdnExercicio->setName ( "stExercicio" );
$obHdnExercicio->setId ( "stExercicio" );
$obHdnExercicio->setValue( $request->get('stExercicio') );

$obLblOrdemCompra = new Label();
$obLblOrdemCompra->setRotulo( 'Ordem de Compra' );
$obLblOrdemCompra->setValue( $request->get('inOrdemCompra') );

$obHdnOrdemCompra = new Hidden;
$obHdnOrdemCompra->setName ( "inOrdemCompra" );
$obHdnOrdemCompra->setId ( "inOrdemCompra" );
$obHdnOrdemCompra->setValue( $request->get('inOrdemCompra') );

$obLblEntidade = new Label();
$obLblEntidade->setRotulo( 'Entidade' );
$obLblEntidade->setValue( $request->get('stEntidade') );

$stEntidade = explode(" - ", $request->get('stEntidade'));

$obHdnEntidade = new Hidden;
$obHdnEntidade->setName ( "inCodEntidade" );
$obHdnEntidade->setId ( "inCodEntidade" );
$obHdnEntidade->setValue( $stEntidade[0] );

$obLblDtOrdem = new Label();
$obLblDtOrdem->setRotulo( 'Data da Ordem de Compra' );
$obLblDtOrdem->setValue( $request->get('stDtOrdem') );

$obHdnDtOrdem = new Hidden;
$obHdnDtOrdem->setName ( "stDtOrdem" );
$obHdnDtOrdem->setId ( "stDtOrdem" );
$obHdnDtOrdem->setValue( $request->get('stDtOrdem') );

$obLblFornecedor = new Label();
$obLblFornecedor->setRotulo( 'Fornecedor' );
$obLblFornecedor->setValue( $request->get('stFornecedor') );

// label - Valor Total
$obLblVlTotal = new Label();
$obLblVlTotal->setRotulo( 'Valor Total' );
$obLblVlTotal->setValue( number_format($request->get('nuVlTotal'), 2, ",", "."));

// Hidden - Valor Total
$obHdnVlTotal = new Hidden;
$obHdnVlTotal->setName ( "nuVlTotal" );
$obHdnVlTotal->setId ( "nuVlTotal" );
$obHdnVlTotal->setValue( $request->get('nuVlTotal') );

// Novo método criado para retonar o valor atendido no momento da Ordem de Compra.
include_once CAM_GP_COM_MAPEAMENTO."TComprasOrdem.class.php";
$obTComprasOrdemCompra = new TComprasOrdem();

$obTComprasOrdemCompra->setDado('tipo'         ,'C'                            );
$obTComprasOrdemCompra->setDado('cod_ordem'    , $request->get('inOrdemCompra'));
$obTComprasOrdemCompra->setDado('cod_entidade' , $request->get('inCodEntidade'));
$obTComprasOrdemCompra->setDado('exercicio'    , $request->get('stExercicio')  );
$obTComprasOrdemCompra->recuperaVlAtendidoOrdemCompra( $rsVlAtendido, $stFiltro, $stOrdem );

Sessao::write('inOrdemCompra',$request->get('inOrdemCompra'));

// label - Valor Atendido
$obLblVlAtendido = new Label();
$obLblVlAtendido->setRotulo( 'Valor Atendido' );

$obLblVlAtendido->setValue( number_format((double) $rsVlAtendido->getCampo('vl_total_atendido'), 2, ",", "."));

// Hidden - Valor Atendido
$obHdnVlAtendido = new Hidden;
$obHdnVlAtendido->setName ( "nuVlAtendido" );
$obHdnVlAtendido->setId ( "nuVlAtendido" );
$obHdnVlAtendido->setValue( $rsVlAtendido->getCampo('vl_total_atendido') );

/*
*    Labels so seguimento inicial do formulário
*    Dados da Ordem de Compra
*/

$obDtNotaFiscal = new Data();
$obDtNotaFiscal->setRotulo    ( 'Data da Nota Fiscal' );
$obDtNotaFiscal->setName      ( "dtNotaFiscal" );
$obDtNotaFiscal->setId        ( "dtNotaFiscal" );
$obDtNotaFiscal->setTitle     ( "Informe a data da nota fiscal.");
$obDtNotaFiscal->setNull      ( false );

$obTxtNumNotaFiscal = new Inteiro();
$obTxtNumNotaFiscal->setRotulo    ( 'Número da Nota Fiscal' );
$obTxtNumNotaFiscal->setName      ( "inNotaFiscal" );
$obTxtNumNotaFiscal->setId        ( "inNotaFiscal" );
$obTxtNumNotaFiscal->setTitle     ( "Informe o número da nota fiscal.");
$obTxtNumNotaFiscal->setNull      ( false );
$obTxtNumNotaFiscal->setSize      ( 10 );
$obTxtNumNotaFiscal->setMaxLength (  9 );

$obTxtNumSerie = new Inteiro();
$obTxtNumSerie->setRotulo    ( 'Número de Série' );
$obTxtNumSerie->setName      ( "inNumSerie" );
$obTxtNumSerie->setId        ( "inNumSerie" );
$obTxtNumSerie->setTitle     ( "Informe a série da nota fiscal.");
$obTxtNumSerie->setNull      ( false );
$obTxtNumSerie->setSize      ( 10 );
$obTxtNumSerie->setMaxLength (  9 );

$obTxtObservacao = new TextBox;
$obTxtObservacao->setName      ( "stObservacao" );
$obTxtObservacao->setId        ( "stObservacao" );
$obTxtObservacao->setRotulo    ( "Observação" );
$obTxtObservacao->setTitle     ( "Informe a observação sobre a nota fiscal.");
$obTxtObservacao->setNull      ( true );
$obTxtObservacao->setMaxLength ( 200 );
$obTxtObservacao->setSize      ( 100 );

/*
*    Spans dos itens Atendidos
*    Itens da Ordem de Compra
*/
$obSpnItensAtendidos = new Span();
$obSpnItensAtendidos->setId( 'spnItensAtendidos' );

/*
*    Spans dos itens
*    Itens da Ordem de Compra
*/
$obSpnItens = new Span();
$obSpnItens->setId( 'spnItens' );

$obSpnDetalheItem = new Span();
$obSpnDetalheItem->setId( 'spnDetalheItem' );

//cria span para o número da placa do bem
$obSpnNumeroPlaca = new Span();
$obSpnNumeroPlaca->setId( 'spnNumeroPlaca' );

$obSpnItemEntrada = new Span();
$obSpnItemEntrada->setId( 'spnItensEntrada' );

//Monta FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm );
$obFormulario->setAjuda      ("UC-03.03.18");
$obFormulario->addHidden     ( $obHdnCtrl );
$obFormulario->addHidden     ( $obHdnAcao );
$obFormulario->addHidden     ( $obHdnExercicio );
$obFormulario->addHidden     ( $obHdnOrdemCompra );
$obFormulario->addHidden     ( $obHdnEntidade );
$obFormulario->addHidden     ( $obHdnDtOrdem );
$obFormulario->addHidden     ( $obHdnFornecedor );
$obFormulario->addHidden     ( $obHdnVlTotal );
$obFormulario->addHidden     ( $obHdnVlAtendido );

$obFormulario->addTitulo     ( 'Dados da Ordem de Compra' );
$obFormulario->addComponente ( $obLblExercicio );
$obFormulario->addComponente ( $obLblOrdemCompra );
$obFormulario->addComponente ( $obLblEntidade );
$obFormulario->addComponente ( $obLblDtOrdem );
$obFormulario->addComponente ( $obLblFornecedor	);
$obFormulario->addComponente ( $obLblVlTotal );
$obFormulario->addComponente ( $obLblVlAtendido );

$obFormulario->addTitulo     ( 'Dados da Nota Fiscal' );
$obFormulario->addComponente ( $obDtNotaFiscal );
$obFormulario->addComponente ( $obTxtNumNotaFiscal );
$obFormulario->addComponente ( $obTxtNumSerie );
$obFormulario->addComponente ( $obTxtObservacao );

// Span de Itens e dos detalhes dos itens
$obFormulario->addSpan       ( $obSpnItensAtendidos );
$obFormulario->addSpan       ( $obSpnItens );
$obFormulario->addSpan       ( $obSpnDetalheItem );
$obFormulario->addSpan       ( $obSpnItemEntrada );

$obFormulario->Cancelar( $pgList.'?'.Sessao::getId().'&stAcao='.$stAcao );
$obFormulario->show();

$stJs="	<script>
            ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&cod_ordem=".$request->get('inOrdemCompra')."&cod_entidade=".trim($arEntidade[0])."&exercicio=".$request->get('stExercicio')."','montaItensAtendidos');
            ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&cod_ordem=".$request->get('inOrdemCompra')."&cod_entidade=".trim($arEntidade[0])."&exercicio=".$request->get('stExercicio')."','montaItens');
        </script>";
echo $stJs;

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
