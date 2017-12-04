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
    * Página de Formulário
    * Data de Criação   : 08/06/2006

    * @author Diego

    * Casos de uso : uc-03.03.17

    $Id: FMMovimentacaoDiversa.php 34468 2008-10-14 14:22:28Z luiz $

    */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GP_ALM_COMPONENTES."IPopUpItem.class.php";
include_once CAM_GA_PROT_COMPONENTES."IPopUpProcesso.class.php";
include_once CAM_GP_ALM_COMPONENTES."IPopUpMarca.class.php";
include_once CAM_GP_ALM_COMPONENTES."IPopUpCentroCustoUsuario.class.php";
include_once CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoLancamentoMaterialEstorno.class.php";

$stPrograma = "EstornoEntrada";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

include_once( $pgJs );

Sessao::write('sessao', array());

$arrayFiltro = Sessao::read("filtro");

foreach ($arrayFiltro as $key => $value)
    $stComplemento .= "&$key=$value";

$stAcao = $request->get('stAcao');

$stLocation = $pgList . "?". Sessao::getId() . "&stAcao=" . $stAcao . $stComplemento;

$stExercicioLancamento = $_REQUEST['stExercicioLancamento'];
$inNumLancamento       = $_REQUEST['inNumLancamento'];
$inCodNatureza         = $_REQUEST['inCodNatureza'];
$stTipoNatureza        = $_REQUEST['stTipoNatureza'];
$inCodAlmoxarifado     = $_REQUEST['inCodAlmoxarifado'];

$obTLancamentoEstorno = new TAlmoxarifadoLancamentoMaterialEstorno;
$obTLancamentoEstorno->setDado('inCodAlmoxarifado', $inCodAlmoxarifado);
$obTLancamentoEstorno->setDado('stExercicio'    , $stExercicioLancamento);
$obTLancamentoEstorno->setDado('inNumLancamento', $inNumLancamento);
$obTLancamentoEstorno->setDado('inCodNatureza'  , $inCodNatureza);
$obTLancamentoEstorno->setDado('stTipoNatureza' , $stTipoNatureza);
$obTLancamentoEstorno->listarItens($rsItens);

$arSessao['itensDisponiveis'] = $rsItens->getElementos();

for ($inCount=0; $inCount<count($arSessao['itensDisponiveis']); $inCount++) {
    $arSessao['itensDisponiveis'][$inCount]['inIdItem'] = $inCount;

    $vlQuantidade = $arSessao['itensDisponiveis'][$inCount]['quantidade'];
    $vlSaldo      = $arSessao['itensDisponiveis'][$inCount]['saldo'];

    $obTLancamentoEstorno = new TAlmoxarifadoLancamentoMaterialEstorno;
    $obTLancamentoEstorno->setDado('inNumLancamento'   , $inNumLancamento);
    $obTLancamentoEstorno->setDado('inCodAlmoxarifado' , $inCodAlmoxarifado);
    $obTLancamentoEstorno->setDado('stExercicio'       , $stExercicioLancamento);
    $obTLancamentoEstorno->setDado('inCodNatureza'     , $inCodNatureza);
    $obTLancamentoEstorno->setDado('stTipoNatureza'    , $stTipoNatureza);
    $obTLancamentoEstorno->setDado('inCodItem'         , $arSessao['itensDisponiveis'][$inCount]['cod_item'] );
    $obTLancamentoEstorno->setDado('inCodMarca'        , $arSessao['itensDisponiveis'][$inCount]['cod_marca'] );
    $obTLancamentoEstorno->setDado('inCodCentro'       , $arSessao['itensDisponiveis'][$inCount]['cod_centro'] );
    $obTLancamentoEstorno->listarMarcaCentro($rsQuantidade);

    //$obTLancamentoEstorno->setDado('inNumLancamento'   , $inNumLancamento);
    //recupera o saldo estornado do lancamento
    $obTLancamentoEstorno->listarSaldoEstornoLancamento($rsSaldoEstorno);

    $nuQuantidade = $rsQuantidade->getCampo('quantidade');
    $stQuantidade = number_format ( $nuQuantidade, 4, ",", ".");

    $nuQuantidadeEstornado = $rsSaldoEstorno->getCampo('saldo_estornado');

    $arSessao['itensDisponiveis'][$inCount]['qtde_estornada'] = ($nuQuantidadeEstornado);
    $arSessao['itensDisponiveis'][$inCount]['saldo_estornar'] = ($vlQuantidade - $nuQuantidadeEstornado);

}

$arSessao['selecionado']['stExercicioLancamento'] = $stExercicioLancamento;
$arSessao['selecionado']['inNumLancamento']       = $inNumLancamento;
$arSessao['selecionado']['inCodNatureza']         = $inCodNatureza;
$arSessao['selecionado']['stTipoNatureza']        = $stTipoNatureza;
$arSessao['selecionado']['inCodAlmoxarifado']     = $inCodAlmoxarifado;

Sessao::write('sessao', $arSessao );

$rsItensTMP = new RecordSet();
$rsItensTMP->preenche( $arSessao['itensDisponiveis'] );
$rsItensTMP->addFormatacao('quantidade', 'NUMERIC_BR_4');
$rsItensTMP->addFormatacao('valor', 'NUMERIC_BR_4');
$rsItensTMP->addFormatacao('saldo', 'NUMERIC_BR_4');
$rsItensTMP->addFormatacao('valor_unitario', 'NUMERIC_BR_4');
$rsItensTMP->addFormatacao('qtde_estornada', 'NUMERIC_BR_4');
$rsItensTMP->addFormatacao('saldo_estornar', 'NUMERIC_BR_4');

$obLista = new Lista;
$obLista->setTitulo( "Lista de Itens" );
$obLista->setMostraPaginacao( false );
$obLista->setAlternado( true );
$obLista->setRecordSet( $rsItensTMP );

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "&nbsp;" );
$obLista->ultimoCabecalho->setWidth( 3 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Item" );
$obLista->ultimoCabecalho->setWidth( 34 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Unidade Medida" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Quantidade de Entrada" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Quantidade Estornada" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Saldo a Estornar" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Valor Unitário" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Valor Total" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Ação" );
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "[cod_item]-[descricao_resumida]" );
$obLista->ultimoDado->setAlinhamento( "ESQUERDA" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "[nom_unidade]" );
$obLista->ultimoDado->setAlinhamento( "ESQUERDA" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "[quantidade]" );
$obLista->ultimoDado->setAlinhamento( "DIREITA" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "[qtde_estornada]" );
$obLista->ultimoDado->setAlinhamento( "DIREITA" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "[saldo_estornar]" );
$obLista->ultimoDado->setAlinhamento( "DIREITA" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "[valor_unitario]" );
$obLista->ultimoDado->setAlinhamento( "DIREITA" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "[valor]" );
$obLista->ultimoDado->setAlinhamento( "DIREITA" );
$obLista->commitDado();

$obRdnClassificacaoBloqueada = new Radio();
$obRdnClassificacaoBloqueada->setName( "inIdItem" );
$obRdnClassificacaoBloqueada->setId  ( "inIdItem" );
$obRdnClassificacaoBloqueada->setValue( "[inIdItem]" );
$obRdnClassificacaoBloqueada->setNull( false );
$obRdnClassificacaoBloqueada->obEvento->setOnClick("montaParametrosGET( 'detalharItem&'+this.name+'='+this.value  );");

$obLista->addDadoComponente( $obRdnClassificacaoBloqueada, false );
$obLista->ultimoDado->setAlinhamento( "CENTRO" );
$obLista->commitDadoComponente();

$obLista->montaHTML();
$stHtml = $obLista->getHTML();

$obSpnItens = new Span;
$obSpnItens->setId("spnItens");
$obSpnItens->setValue( $stHtml );

$obSpnDetalhes = new Span;
$obSpnDetalhes->setId("spnDetalhes");

$obSpnItensEstorno = new Span;
$obSpnItensEstorno->setId('spnItensEstorno');

$obForm = new Form;
$obForm->setAction      ( $pgProc  );
$obForm->setTarget      ( 'oculto' );

$obHdnAcao = new Hidden;
$obHdnAcao->setName     ( 'stAcao' );
$obHdnAcao->setValue    ( $stAcao  );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName     ( 'stCtrl' );
$obHdnCtrl->setValue    ( $stCtrl  );

$obHdnExercicioLancamento = new Hidden;
$obHdnExercicioLancamento->setName     ( 'stExercicioLancamento' );
$obHdnExercicioLancamento->setValue    ( $stExercicioLancamento  );
$obHdnExercicioLancamento->setId       ( 'stExercicioLancamento' );

$obHdnNumLancamento = new Hidden;
$obHdnNumLancamento->setName     ( 'inNumLancamento' );
$obHdnNumLancamento->setValue    ( $inNumLancamento  );
$obHdnNumLancamento->setId       ( 'inNumLancamento' );

$obHdnCodNatureza = new Hidden;
$obHdnCodNatureza->setName     ( 'inCodNatureza' );
$obHdnCodNatureza->setValue    ( $inCodNatureza  );
$obHdnCodNatureza->setId       ( 'inCodNatureza' );

$obHdnTipoNatureza = new Hidden;
$obHdnTipoNatureza->setName     ( 'stTipoNatureza' );
$obHdnTipoNatureza->setValue    ( $stTipoNatureza  );
$obHdnTipoNatureza->setId       ( 'stTipoNatureza' );

$obLblExercicio = new Label;
$obLblExercicio->setRotulo ( 'Exercício'   );
$obLblExercicio->setId     ( 'stExercicio' );
$obLblExercicio->setValue  ( $stExercicioLancamento );

$obLblAlmoxarifado = new Label;
$obLblAlmoxarifado->setRotulo ( 'Almoxarifado'   );
$obLblAlmoxarifado->setId     ( 'stAlmoxarifado' );
$obLblAlmoxarifado->setValue  ( $_REQUEST['inCodAlmoxarifado']."-".$_REQUEST['stNomAlmoxarifado'] );

$obLblAlmoxarife = new Label;
$obLblAlmoxarife->setRotulo ( 'Almoxarife'   );
$obLblAlmoxarife->setId     ( 'stAlmoxarife' );
$obLblAlmoxarife->setValue  ( $_REQUEST['inCgmAlmoxarife']."-".$_REQUEST['stNomAlmoxarife'] );

# Número do Lançamento
$obLblNroLancamento = new Label;
$obLblNroLancamento->setRotulo ( 'Código do Lançamento'       );
$obLblNroLancamento->setId     ( 'inNumLancamento'            );
$obLblNroLancamento->setValue  ( $_REQUEST['inNumLancamento'] );

$obLblDataLancamento = new Label;
$obLblDataLancamento->setRotulo ( "Data do Lançamento"   );
$obLblDataLancamento->setId     ( "stDataLancamento" );
$obLblDataLancamento->setValue  ( $_REQUEST['stDataLancamento'] );

$obFormulario = new Formulario;
$obFormulario->addForm        ( $obForm                );
$obFormulario->setAjuda       ("UC-03.03.19");
$obFormulario->addHidden      ( $obHdnAcao             );
$obFormulario->addHidden      ( $obHdnCtrl             );
$obFormulario->addHidden      ( $obHdnExercicioLancamento );
$obFormulario->addHidden      ( $obHdnNumLancamento    );
$obFormulario->addHidden      ( $obHdnCodNatureza      );
$obFormulario->addHidden      ( $obHdnTipoNatureza     );
$obFormulario->addTitulo      ( "Dados da Entrada"     );

$obFormulario->addComponente  ( $obLblExercicio        );
$obFormulario->addComponente  ( $obLblAlmoxarifado     );
$obFormulario->addComponente  ( $obLblAlmoxarife       );
$obFormulario->addComponente  ( $obLblNroLancamento    );
$obFormulario->addComponente  ( $obLblDataLancamento   );

$obFormulario->addSpan 	      ( $obSpnItens     	   );
$obFormulario->addSpan 	      ( $obSpnDetalhes  	   );
$obFormulario->addSpan 	      ( $obSpnItensEstorno     );

$obFormulario->Cancelar( $stLocation );
$obFormulario->Show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
