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

    *$Id: LSAnularEmpenho.php 59620 2014-09-02 17:25:32Z arthur $

    * Casos de uso: uc-02.03.03, uc-02.03.17, uc-02.03.18
*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include CAM_GF_EMP_NEGOCIO."REmpenhoEmpenhoAutorizacao.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "AnularEmpenho";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgCons = "FMConsultarEmpenho.php";

$stCaminho = CAM_GF_EMP_INSTANCIAS."empenho/";
$obREmpenhoEmpenhoAutorizacao = new REmpenhoEmpenhoAutorizacao;

if ( !Sessao::read('paginando') ) {
    $arFiltro = array();
    foreach ($_POST as $stCampo => $stValor) {
        $arFiltro[$stCampo] = $stValor;
    }
    Sessao::write('filtro', $arFiltro);
    Sessao::write('pg', $_GET['pg'] ? $_GET['pg'] : 0);
    Sessao::write('pos', $_GET['pos']? $_GET['pos'] : 0);
    Sessao::write('paginando', true);
} else {
    Sessao::write('pg', $_GET['pg']);
    Sessao::write('pos', $_GET['pos']);
    $arFiltro = Sessao::read('filtro');
    $_GET['stAcao'            ] = $arFiltro['stAcao'            ];
    $_REQUEST['inCodAutorizacaoInicial'  ] = $arFiltro['inCodAutorizacaoInicial'  ];
    $_REQUEST['inCodAutorizacaoFinal'  ] = $arFiltro['inCodAutorizacaoFinal'  ];
    $_REQUEST['stCodClassificacao'] = $arFiltro['stCodClassificacao'];
    $_REQUEST['stDtInicial'       ] = $arFiltro['stDtInicial'       ];
    $_REQUEST['stDtFinal'         ] = $arFiltro['stDtFinal'         ];
    $_REQUEST['inCodEntidade'     ] = $arFiltro['inCodEntidade'     ];
    $_REQUEST['stExercicio'       ] = $arFiltro['stExercicio'       ];
}

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}
if ($stAcao == "imprimirAN") {
    $stAcao = "reemitir";
}

//Define arquivos PHP para cada acao
switch ($stAcao) {
    case 'alterar'  : $pgProx = $pgForm; break;
    case 'excluir'  : $pgProx = $pgProc; break;
    case 'consultar': $pgProx = $pgCons; break;
    DEFAULT         : $pgProx = $pgForm;
}

foreach ($_REQUEST['inCodEntidade'] as $value) {
    $stCodEntidade .= $value . " , ";
}
$stCodEntidade = substr($stCodEntidade,0,strlen($stCodEntidade)-2);

$stExercicio = ( $_REQUEST['stExercicio'] ) ? $_REQUEST['stExercicio'] : Sessao::getExercicio();

$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setCodDespesa( $_REQUEST['inCodDespesa'] );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setExercicio( $stExercicio );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setCodEmpenhoInicial( $_REQUEST['inCodEmpenhoInicial'] );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setCodEmpenhoFinal( $_REQUEST['inCodEmpenhoFinal'] );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setDtEmpenhoInicial( $_REQUEST['stDtInicial'] );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setDtEmpenhoFinal( $_REQUEST['stDtFinal'] );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obRCGM->setNumCGM( $_REQUEST['inCodFornecedor'] );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obROrcamentoEntidade->setCodigoEntidade( $stCodEntidade );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->setCodAutorizacaoInicial( $_REQUEST['inCodAutorizacaoInicial'] );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->setCodAutorizacaoFinal( $_REQUEST['inCodAutorizacaoFinal'] );

$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setBoEmpenhoCompraLicitacao( true );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setCodModalidadeCompra( $_REQUEST['inCodModalidadeCompra'] );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setCompraInicial( $_REQUEST['inCompraInicial'] );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setCompraFinal( $_REQUEST['inCompraFinal'] );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setCodModalidadeLicitacao( $_REQUEST['inCodModalidadeLicitacao'] );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setLicitacaoInicial( $_REQUEST['inLicitacaoInicial'] );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setLicitacaoFinal( $_REQUEST['inLicitacaoFinal'] );

if ($stAcao == 'anular') {
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setSomar( true );
}
if ($stAcao == 'reemitir') {
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->listarReemitirAnulados( $rsLista );
    $rsLista->addFormatacao("vl_anulado","NUMERIC_BR");
    Sessao::write('reemitir', 't');

} else {
    if ( $stExercicio == Sessao::getExercicio() ) {
        $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->listar( $rsLista );
    } else {
        $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->listarRestosAPagarAjustes( $rsLista );
    }
}

$stLink .= "&stAcao=".$stAcao;
if ($_GET["pg"] and  $_GET["pos"]) {
    $stLink.= "&pg=".$_GET["pg"]."&pos=".$_GET["pos"];
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
if ($stAcao == "reemitir") {
    $obLista->ultimoCabecalho->addConteudo("Data Anulação");
} else {
    $obLista->ultimoCabecalho->addConteudo("Data do Empenho");
}
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
if ($stAcao == "reemitir") {
    $obLista->ultimoCabecalho->addConteudo("Credor");
} else {
    $obLista->ultimoCabecalho->addConteudo("Fornecedor");
}
$obLista->ultimoCabecalho->setWidth( 70 );
$obLista->commitCabecalho();
if ($stAcao == "reemitir") {
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Valor");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();
}
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
if ($stAcao == "reemitir") {
    $obLista->ultimoDado->setCampo( "dt_anulado" );
} else {
    $obLista->ultimoDado->setCampo( "dt_empenho" );
}
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_fornecedor" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();
if ($stAcao == "reemitir") {
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "vl_anulado" );
    $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
    $obLista->commitDado();
}
$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->addCampo( "&inCodEmpenho"     , "cod_empenho"     );
$obLista->ultimaAcao->addCampo( "&inCodPreEmpenho"  , "cod_pre_empenho" );
$obLista->ultimaAcao->addCampo( "&inCodAutorizacao" , "cod_autorizacao" );
$obLista->ultimaAcao->addCampo( "&inCodEntidade"    , "cod_entidade"    );
$obLista->ultimaAcao->addCampo( "&inCodReserva"     , "cod_reserva"     );
$obLista->ultimaAcao->addCampo( "&boImplantado"     , "implantado"      );
$obLista->ultimaAcao->addCampo( "&stExercicioEmpenho" ,"exercicio"      );
$obLista->ultimaAcao->addCampo( "&stDtExercicioEmpenho" ,"exercicio"      );
$obLista->ultimaAcao->addCampo( "&timestamp" ,"timestamp"      );

$htmlExtra = "";
if ($stAcao == "anular") {
    $obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink );
} elseif ($stAcao == "imprimir") {
    $pgProx = CAM_FW_POPUPS."relatorio/OCRelatorio.php";
    $stLink .= "&stCaminho=".CAM_GF_EMP_INSTANCIAS."empenho/OCRelatorioEmpenhoOrcamentario.php";
    $obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink );
    Sessao::write('reemitir', 't');

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

    $obTabelaBtnImprimirTodos = new Tabela;
    $obTabelaBtnImprimirTodos->addLinha();
    $obTabelaBtnImprimirTodos->ultimaLinha->addCelula();
    $obTabelaBtnImprimirTodos->ultimaLinha->ultimaCelula->setColSpan (1);
    $obTabelaBtnImprimirTodos->ultimaLinha->ultimaCelula->setClass   ( $obLista->getClassPaginacao() );
    $obTabelaBtnImprimirTodos->ultimaLinha->ultimaCelula->addConteudo( "<div align=\"center\">".$obBotaoImprimirTodos->getHTML()."&nbsp;</div>");
    $obTabelaBtnImprimirTodos->ultimaLinha->commitCelula();
    $obTabelaBtnImprimirTodos->commitLinha();
    $obTabelaBtnImprimirTodos->montaHTML();

    $htmlExtra = $obTabelaBtnImprimirTodos->getHTML();
} elseif ($stAcao == "reemitir") {

    $pgProx = CAM_FW_POPUPS."relatorio/OCRelatorio.php";
    $stLink .= "&stCaminho=".CAM_GF_EMP_INSTANCIAS."empenho/OCRelatorioEmpenhoOrcamentarioAnulado.php";

    $obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink );
} elseif ($stAcao == "consultar") {
    $obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink );
}

$obLista->commitAcao();
$obLista->montaHTML();
echo $obLista->getHTML().$htmlExtra;
?>
