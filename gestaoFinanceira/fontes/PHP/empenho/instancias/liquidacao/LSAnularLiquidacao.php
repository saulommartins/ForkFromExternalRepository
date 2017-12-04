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
    * Página de Listagem de Anulacao de Empenho
    * Data de Criação   : 06/12/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    * $Id: LSAnularLiquidacao.php 64399 2016-02-15 19:00:33Z franver $

    * Casos de uso: uc-02.03.04
                    uc-02.03.18
*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include CAM_GF_EMP_NEGOCIO."REmpenhoEmpenhoAutorizacao.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ManterLiquidacao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgCons = "FMConsultarLiquidacao.php";
$stCaminho = CAM_GF_EMP_INSTANCIAS."liquidacao/";

$obREmpenhoEmpenhoAutorizacao = new REmpenhoEmpenhoAutorizacao;

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

//Define arquivos PHP para cada acao
switch ($stAcao) {
    case 'alterar'  : $pgProx = $pgForm; break;
    case 'excluir'  : $pgProx = $pgProc; break;
    case 'consultar': $pgProx = $pgCons; break;
    case 'anular'   : $pgProx = "FMAnularLiquidacao.php"; break;
    DEFAULT         : $pgProx = $pgForm;
}

//MANTEM FILTRO E PAGINACAO
$stLink .= "&stAcao=".$stAcao;
if ( $request->get('pg') and $request->get('pos')) {
    Sessao::write('pg', $request->get('pg'));
    Sessao::write('pos', $request->get('pos'));
}

//USADO QUANDO EXISTIR FILTRO
//NA FL O VAR LINK DEVE SER RESETADA
$arFiltro = Sessao::read('arFiltro');
if ($arFiltro['paginando']) {
    $arFiltro['pg']        = $request->get('pg');
    $arFiltro['pos']       = $request->get('pos');
    $request = new Request($arFiltro);
} else {
    $arFiltro = $_REQUEST;
    $arFiltro['paginando'] = true;
    $arFiltro['pg']        = $request->get('pg');
    $arFiltro['pos']       = $request->get('pos');
}
Sessao::write('arFiltro', $arFiltro);

if (is_array($request->get('inCodEntidade'))) {
    $stCodEntidade = implode(",", $request->get('inCodEntidade'));
} else {
    $stCodEntidade = $request->get('inCodEntidade');
}

$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setCodDespesa( $request->get('inCodDespesa') );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setCodFornecedor( $request->get('inCodFornecedor') );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setExercicio( $request->get('dtExercicioEmpenho') );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setCodEmpenhoInicial( $request->get('inCodEmpenhoInicial') );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setCodEmpenhoFinal( $request->get('inCodEmpenhoFinal') );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setDtVencimento( $request->get('stDtVencimento') );

$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->stDtLiquidacaoInicial = $request->get('stDtInicial');
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->stDtLiquidacaoFinal = $request->get('stDtFinal');

$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setCodLiquidacaoInicial( $request->get('inCodLiquidacaoInicial') );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setCodLiquidacaoFinal( $request->get('inCodLiquidacaoFinal') );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obROrcamentoEntidade->setCodigoEntidade( $stCodEntidade );

if ($request->get('inCodTipoDocumento')) {
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setCodTipoDocumento( $request->get('inCodTipoDocumento') );
}

if ($stAcao == 'anular') {
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setSomarLiquidacao( true  );
}
if ( $request->get('dtExercicioEmpenho') == Sessao::getExercicio() ) {
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->listarPorNota( $rsLista );
} elseif ( $request->get('dtExercicioEmpenho') < Sessao::getExercicio() ) {
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->listarRestosPorNota( $rsLista );
} else {
    $rsLista = new RecordSet;
}

if ($request->get('pg') and  $request->get('pos')) {
    $stLink.= '&pg='.$request->get('pg').'&pos='.$request->get('pos');
}

Sessao::write('rsListaImpressao', $rsLista);

$obLista = new Lista;
$obLista->setRecordSet( $rsLista );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Entidade");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Empenho");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Data do Empenho");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Liquidação");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Data da Liquidação");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Credor");
$obLista->ultimoCabecalho->setWidth( 65 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_entidade" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "[cod_empenho]/[exercicio]" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "dt_empenho" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "[cod_nota]/[exercicio_nota]" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "dt_liquidacao" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_fornecedor" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();
$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->addCampo( "&inCodEmpenho"     , "cod_empenho"     );
$obLista->ultimaAcao->addCampo( "&inCodPreEmpenho"  , "cod_pre_empenho" );
$obLista->ultimaAcao->addCampo( "&inCodEntidade"    , "cod_entidade"    );
$obLista->ultimaAcao->addCampo( "&inCodReserva"     , "cod_reserva"     );
$obLista->ultimaAcao->addCampo( "&inCodAutorizacao" , "cod_autorizacao" );
$obLista->ultimaAcao->addCampo( "&inCodNota"        , "cod_nota" );
$obLista->ultimaAcao->addCampo( "&stDtLiquidacao"   , "dt_liquidacao" );
$obLista->ultimaAcao->addCampo( "&stExercicioNota"  , "exercicio_nota" );
$obLista->ultimaAcao->addCampo( "&dtExercicioEmpenho"     , "exercicio"     );
$obLista->ultimaAcao->addCampo( "&boImplantado"           , "implantado"    );

if ($stAcao == "imprimir") {
    $obLista->ultimaAcao->addCampo( "&inCodNota"        , "cod_nota"        );
    $obLista->ultimaAcao->addCampo( "&stExercicioNota"  , "exercicio_nota"  );
    $pgProx = CAM_FW_POPUPS."relatorio/OCRelatorio.php";
    $stLink .= "&stCaminho=".CAM_GF_EMP_INSTANCIAS."liquidacao/OCRelatorioNotaLiquidacaoEmpenho.php";
    $obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink );

    $stLinkBotao = $pgProx."?".Sessao::getId()."&stCtrl=imprimirTodos".$stLink;
    $obBotaoImprimirTodos = new Button;
    $obBotaoImprimirTodos->setId   ("imprimirTodos");
    $obBotaoImprimirTodos->setName ("imprimirTodos");
    $obBotaoImprimirTodos->setValue("Imprimir Todos");
    $obBotaoImprimirTodos->setStyle("color: red;");
    $obBotaoImprimirTodos->setTipo ("button");
    $obBotaoImprimirTodos->setDefinicao("imprimirTodos");
    $obBotaoImprimirTodos->obEvento->setOnClick("javascript:window.open('".$stLinkBotao."', 'oculto');");
    $obBotaoImprimirTodos->montaHTML();

    $obLinkImpTodos = new Link;
    $obLinkImpTodos->setHref($pgProx."?".Sessao::getId().$stLink);
    $obLinkImpTodos->setValue('Imprimir Todos');
    $obLinkImpTodos->montaHtml();

    $obTabelaBtnImprimirTodos = new Tabela;
    $obTabelaBtnImprimirTodos->addLinha();
    $obTabelaBtnImprimirTodos->ultimaLinha->addCelula();
    $obTabelaBtnImprimirTodos->ultimaLinha->ultimaCelula->setColSpan (1);
    $obTabelaBtnImprimirTodos->ultimaLinha->ultimaCelula->setClass   ( $obLista->getClassPaginacao() );
    $obTabelaBtnImprimirTodos->ultimaLinha->ultimaCelula->addConteudo( "<div align=\"center\">".$obBotaoImprimirTodos->getHTML()."&nbsp;</div>");
    $obTabelaBtnImprimirTodos->ultimaLinha->commitCelula();
    $obTabelaBtnImprimirTodos->commitLinha();
    $obTabelaBtnImprimirTodos->montaHTML();

    $obLista->commitAcao();
    $obLista->montaHTML();
    echo $obLista->getHTML().$obTabelaBtnImprimirTodos->getHTML();
} else {
    $obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink );
    $obLista->commitAcao();
    $obLista->montaHTML();
    echo $obLista->getHTML();
}

SistemaLegado::liberaFrames();
?>
