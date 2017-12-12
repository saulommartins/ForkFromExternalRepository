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
    * Página de Listagem de Itens
    * Data de Criação   : 05/12/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    * $Id: LSManterEmpenho.php 66418 2016-08-25 21:02:27Z michel $

    * Casos de uso: uc-02.03.03
                    uc-02.01.08

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GF_EMP_NEGOCIO."REmpenhoEmpenhoAutorizacao.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterEmpenho";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$stCaminho   = CAM_GF_EMP_INSTANCIAS."empenho/";

$obREmpenhoEmpenhoAutorizacao = new REmpenhoEmpenhoAutorizacao;
$stCodEntidade = isset($stCodEntidade) ? $stCodEntidade : "";
$stLink        = isset($stLink)        ? $stLink        : "";
//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

//Define arquivos PHP para cada acao
switch ($stAcao) {
    case 'alterar'  : $pgProx = $pgForm; break;
    case 'excluir'  : $pgProx = $pgProc; break;
    default         : $pgProx = $pgForm;
}

if ( !Sessao::read('paginando') ) {
    $arFiltro = array();
    foreach ($_POST as $stCampo => $stValor) {
        $arFiltro[$stCampo] = $stValor;
    }
    Sessao::write('filtro', $arFiltro);
    Sessao::write('pg' , $request->get('pg') ? $request->get('pg') : 0);
    Sessao::write('pos', $request->get('pos')? $request->get('pos') : 0);
    Sessao::write('paginando', true);
} else {
    Sessao::write('pg', $request->get('pg'));
    Sessao::write('pos', $request->get('pos'));
}

$arFiltro = Sessao::read('filtro');
$_REQUEST['inCodAutorizacaoInicial'] = $arFiltro['inCodAutorizacaoInicial'  ];
$_REQUEST['inCodAutorizacaoFinal'  ] = $arFiltro['inCodAutorizacaoFinal'    ];
$_REQUEST['stCodClassificacao'] = isset($arFiltro['stCodClassificacao']) ? $arFiltro['stCodClassificacao'] : "";
$_REQUEST['stDtInicial'       ] = $arFiltro['stDtInicial'       ];
$_REQUEST['stDtFinal'         ] = $arFiltro['stDtFinal'         ];
$_REQUEST['inCodEntidade'     ] = $arFiltro['inCodEntidade'     ];

$stCodEntidade = implode(',', $_REQUEST['inCodEntidade']);

$stExercicio = Sessao::getExercicio();
if ($request->get('stDtExercicioEmpenho')) {
  $stExercicio = $_REQUEST['stDtExercicioEmpenho'];
}

$obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setCodDespesa( $request->get('inCodDespesa') );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->setExercicio( $stExercicio );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->setCodAutorizacaoInicial( $_REQUEST['inCodAutorizacaoInicial'] );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->setCodAutorizacaoFinal( $_REQUEST['inCodAutorizacaoFinal'] );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->setDtAutorizacaoInicial( $_REQUEST['stDtInicial'] );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->setDtAutorizacaoFinal( $_REQUEST['stDtFinal'] );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->obRCGM->setNumCGM( $request->get('inCodFornecedor') );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->obROrcamentoEntidade->setCodigoEntidade( $stCodEntidade );

$obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->setBoEmpenhoCompraLicitacao( true );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->setCodModalidadeCompra( $_REQUEST['inCodModalidadeCompra'] );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->setCompraInicial( $_REQUEST['inCompraInicial'] );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->setCompraFinal( $_REQUEST['inCompraFinal'] );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->setCodModalidadeLicitacao( $_REQUEST['inCodModalidadeLicitacao'] );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->setLicitacaoInicial( $_REQUEST['inLicitacaoInicial'] );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->setLicitacaoFinal( $_REQUEST['inLicitacaoFinal'] );
if (Sessao::getExercicio() > '2015') {
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->setCentroCusto( $_REQUEST['inCentroCusto'] );
}
$obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->listar( $rsLista );

$stLink .= "&stAcao=".$stAcao;
if ( $request->get('pg') and  $request->get('pos') ) {
    $stLink.= "&pg=".$_GET["pg"]."&pos=".$_GET["pos"];
}

$obLista = new Lista;

$obLista->setRecordSet( $rsLista );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Entidade");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Autorização");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Data da Autorização");
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Fornecedor");
$obLista->ultimoCabecalho->setWidth( 50 );
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
$obLista->ultimoDado->setCampo( "[cod_autorizacao]/[exercicio]" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "dt_autorizacao" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_fornecedor" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();
$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->addCampo( "&inCodAutorizacao" , "cod_autorizacao" );
$obLista->ultimaAcao->addCampo( "&inCodPreEmpenho"  , "cod_pre_empenho" );
$obLista->ultimaAcao->addCampo( "&inCodEntidade"    , "cod_entidade"    );
$obLista->ultimaAcao->addCampo( "&stCodEntidade"    , "cod_entidade"    );
$obLista->ultimaAcao->addCampo( "&inCodReserva"     , "cod_reserva"     );

if ($stAcao == "excluir") {
    $obLista->ultimaAcao->addCampo("&stDescQuestao", "descricao");
    $obLista->ultimaAcao->setLink( $stCaminho.$pgProx."?".Sessao::getId().$stLink );
} else {
    if ($stAcao == "imprimir") {
        $pgProx = CAM_FW_POPUPS."relatorio/OCRelatorio.php";
        $stLink .= "&stCaminho=".CAM_GF_EMP_INSTANCIAS."autorizacao/OCRelatorioAutorizacao.php";
    }
    $obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink );
}
$obLista->commitAcao();
$obLista->show();
?>
