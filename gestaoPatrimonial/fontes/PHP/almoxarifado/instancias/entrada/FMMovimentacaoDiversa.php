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
    * Página de Formulário para Processar Implantação
    * Data de Criação   : 08/06/2006

    * @author Rodrigo

    $Id: FMMovimentacaoDiversa.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso : uc-03.03.17
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GP_ALM_COMPONENTES."IPopUpItem.class.php");
include_once(CAM_GA_PROT_COMPONENTES."IPopUpProcesso.class.php");
include_once(CAM_GP_ALM_COMPONENTES."IPopUpMarca.class.php");
include_once(CAM_GP_ALM_COMPONENTES."IPopUpCentroCustoUsuario.class.php");
include_once(CAM_GA_CGM_COMPONENTES."IPopUpCGMVinculado.class.php" );
include_once(CAM_GF_ORC_COMPONENTES. "ITextBoxSelectEntidadeGeral.class.php");

$stPrograma = "MovimentacaoDiversa";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

include_once( $pgJs );

// Inicializa o array de itens.
Sessao::write('itens', array());

$stAcao = $request->get('stAcao');

$obForm = new Form;
$obForm->setAction      ( $pgProc  );
$obForm->setTarget      ( 'oculto' );

$obHdnAcao = new Hidden;
$obHdnAcao->setName     ( "stAcao" );
$obHdnAcao->setValue    ( $stAcao  );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName     ( "stCtrl" );
$obHdnCtrl->setValue    ( $stCtrl  );

$obHdnInId = new Hidden;
$obHdnInId->setName     ( "inId" );
$obHdnInId->setValue    ( $inId  );
$obHdnInId->setId       ( 'inId' );

$obLblExercicio = new Label;
$obLblExercicio->setRotulo ( "Exercício"   );
$obLblExercicio->setId     ( "stExercicio" );
$obLblExercicio->setValue  ( Sessao::getExercicio() );

$obSpnAlmoxarifado = new Span;
$obSpnAlmoxarifado->setId ( 'spnAlmoxarifado' );

$obItem = new IPopUpItem($obForm);
$obItem->setAtivo(true);
$obItem->setUnidadeNaoInformado(true);
$obItem->setTipoNaoInformado(true);
$obItem->setServico(false);
$obItem->setNull(true);
$obItem->setObrigatorioBarra( true );
$obItem->obCampoCod->setId('inCodItem');
$obItem->obCampoCod->obEvento->setOnBlur   ( "if (this.value != '') { montaParametrosGET('montaFormLotes'); montaParametrosGET('montaAtributos');montaParametrosGET('montaFormBemPatrimonio'); }" );
$obItem->obCampoCod->obEvento->setOnChange ( "if (this.value != '') { montaParametrosGET('buscaCodigoBarras'); }" );

$obSpnAtributos = new Span;
$obSpnAtributos->setId( 'spnAtributos' );

//cria span para o número da placa do bem
$obSpnNumeroPlaca = new Span;
$obSpnNumeroPlaca->setId( 'spnNumeroPlaca' );

$obSpnFormPatrimonio = new Span;
$obSpnFormPatrimonio->setId( 'spnFormPatrimonio' );

$obMarca = new IPopUpMarca($obForm);
$obMarca->setTitle("Informe a marca do item.");
$obMarca->obCampoCod->setId('inCodMarca');
$obMarca->setNull (true);
$obMarca->setObrigatorioBarra(true);
$obMarca->obCampoCod->obEvento->setOnChange ( "montaParametrosGET('buscaCodigoBarras');" );

$obIPopUpFornecedor = new IPopUpCGMVinculado( $obForm                 );
$obIPopUpFornecedor->setTabelaVinculo       ( 'compras.fornecedor'    );
$obIPopUpFornecedor->setCampoVinculo        ( 'cgm_fornecedor'        );
$obIPopUpFornecedor->setNomeVinculo         ( 'Fornecedor'            );
$obIPopUpFornecedor->setRotulo              ( 'Fornecedor'            );
$obIPopUpFornecedor->setTitle               ( 'Informe o fornecedor.' );
$obIPopUpFornecedor->setName                ( 'stNomCGM'              );
$obIPopUpFornecedor->setId                  ( 'stNomCGM'              );
$obIPopUpFornecedor->obCampoCod->setName    ( 'inCGM'                 );
$obIPopUpFornecedor->obCampoCod->setId      ( 'inCGM'                 );
$obIPopUpFornecedor->obCampoCod->setNull    ( false                   );
$obIPopUpFornecedor->setNull                ( false                   );

$obtxtDataNotaFiscal = new Data;
$obtxtDataNotaFiscal->setName('dtNotaFiscal');
$obtxtDataNotaFiscal->setId  ('dtNotaFiscal');
$obtxtDataNotaFiscal->setRotulo('Data da Nota Fiscal');
$obtxtDataNotaFiscal->setTitle('Informe a data da Nota Fiscal');
$obtxtDataNotaFiscal->setNull(false);

$obTxtNumeroNotaFiscal = new Inteiro;
$obTxtNumeroNotaFiscal->setName   ( 'inNumNota'  );
$obTxtNumeroNotaFiscal->setId     ( 'inNumNota'    );
$obTxtNumeroNotaFiscal->setRotulo ( 'Número da Nota Fiscal' );
$obTxtNumeroNotaFiscal->setTitle  ( 'Digite o número da Nota Fiscal.' );
$obTxtNumeroNotaFiscal->setMaxLength (9);
$obTxtNumeroNotaFiscal->setNull(false);

$obTxtNumeroSerieNotaFiscal = new Inteiro;
$obTxtNumeroSerieNotaFiscal->setName   ( 'inNumSerieNota'  );
$obTxtNumeroSerieNotaFiscal->setId     ( 'inNumSerieNota'    );
$obTxtNumeroSerieNotaFiscal->setRotulo ( 'Número de Série' );
$obTxtNumeroSerieNotaFiscal->setTitle  ( 'Digite o número de Série da Nota Fiscal.' );
$obTxtNumeroSerieNotaFiscal->setMaxLength (9);
$obTxtNumeroSerieNotaFiscal->setNull(false);

$obTxtAreaObservacao = new TextArea;
$obTxtAreaObservacao->setName('stObservacao');
$obTxtAreaObservacao->setId('stObservacao');
$obTxtAreaObservacao->setRotulo('Observação');
$obTxtAreaObservacao->setNull  ( true );

$obTxtAreaObservacao->setTitle('Digite obervações da Nota Fiscal');

$obTxtCodigoBarras = new Inteiro;
$obTxtCodigoBarras->setName   ( 'inCodigoBarras'    );
$obTxtCodigoBarras->setId     ( 'inCodigoBarras'    );
$obTxtCodigoBarras->setRotulo ( 'Código  de Barras' );
$obTxtCodigoBarras->setTitle  ( 'Digite o código de barras do item.' );

$obCentroCusto = new IPopUpCentroCustoUsuario($obForm);
$obCentroCusto->setNull(true);
$obCentroCusto->obCampoCod->setId('inCodCentroCusto');
$obCentroCusto->setObrigatorioBarra(true);

$obSpnFormLotes = new Span;
$obSpnFormLotes->setId("spnFormLotes");

$obSpnDadosItens = new Span;
$obSpnDadosItens->setId('spnDadosItem');

$obQuantidade = new Quantidade;
$obQuantidade->setRotulo ( 'Quantidade' );
$obQuantidade->setSize( 15 );
$obQuantidade->setDefinicao ( "NUMERICO" );
$obQuantidade->setObrigatorioBarra( true );

$obValorTotal = new ValorTotal;
$obValorTotal->setRotulo      ( 'Valor Total de Mercado' );
$obValorTotal->setSize        ( 15);
$obValorTotal->setNaoZero     ( true);
$obValorTotal->setObrigatorio ( true);

$obSpnItens = new Span;
$obSpnItens->setId( "spnItens" );

$obBtnIncluir = new Button();
$obBtnIncluir->setValue('Incluir');
$obBtnIncluir->setId('botaoIncluir');
$obBtnIncluir->obEvento->setOnClick( "montaParametrosGET('incluirmontaListaItens');" );

$obBtnLimpar = new Button();
$obBtnLimpar->setValue('Limpar');
$obBtnLimpar->obEvento->setOnClick( "limparFormulario();" );

$obIPopupProcesso = new IPopUpProcesso( $obForm );
// Novo método criado para validar o conteúdo do campo processo.
$obIPopupProcesso->setValidar( true );
$obIPopupProcesso->setNull( false );

$obFormulario = new Formulario;
$obFormulario->addForm        ( $obForm                );
$obFormulario->setAjuda       ("UC-03.03.17");
$obFormulario->addHidden      ( $obHdnAcao             );
$obFormulario->addHidden      ( $obHdnCtrl             );
$obFormulario->addHidden      ( $obHdnInId             );
$obFormulario->addTitulo      ( "Dados da Implantação" );

$obFormulario->addComponente  ( $obLblExercicio        );
$obFormulario->addSpan        ( $obSpnAlmoxarifado     );

if ($stAcao == 'doacao') {
    $obFormulario->addTitulo 	  ( 'Dados do Processo' );
    $obFormulario->addComponente  ( $obIPopupProcesso );
}

if ($stAcao != 'doacao') {
    $obFormulario->addTitulo      ( "Dados da Nota Fiscal" );
    $obFormulario->addComponente  ( $obIPopUpFornecedor    );
    $obFormulario->addComponente  ( $obtxtDataNotaFiscal );
    $obFormulario->addComponente  ( $obTxtNumeroNotaFiscal );
    $obFormulario->addComponente  ( $obTxtNumeroSerieNotaFiscal  );
    $obFormulario->addComponente  ( $obTxtAreaObservacao );
} else {
    $obFormulario->addComponente  ( $obIPopUpFornecedor    );
}

$obFormulario->addTitulo      ( "Dados do Item"        );
$obFormulario->addComponente  ( $obItem 			   );
$obFormulario->addSpan	      ( $obSpnDadosItens 	   );
$obFormulario->addComponente  ( $obMarca               );
$obFormulario->addComponente  ( $obTxtCodigoBarras     );
$obFormulario->addComponente  ( $obCentroCusto         );
$obFormulario->addComponente  ( $obQuantidade          );
$obFormulario->addComponente  ( $obValorTotal          );
$obFormulario->addSpan        ( $obSpnFormLotes        );
$obFormulario->addSpan        ( $obSpnFormPatrimonio   );
$obFormulario->addSpan        ( $obSpnNumeroPlaca   );
$obFormulario->addSpan 	      ( $obSpnAtributos 	   );
$obFormulario->defineBarra( array( $obBtnIncluir, $obBtnLimpar ) );

$obFormulario->addSpan        ( $obSpnItens            );
$obFormulario->Ok(true);
$obFormulario->Show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

SistemaLegado::executaFrameOculto("executaFuncaoAjax('montaCampoAlmoxarifado'); \n");
