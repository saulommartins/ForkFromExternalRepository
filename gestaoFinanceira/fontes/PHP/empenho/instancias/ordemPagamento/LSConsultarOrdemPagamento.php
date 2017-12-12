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
    * Página de Listagem de Ordens de Pagamento
    * Data de Criação: 31/05/2005

    * @author Analista: Dieine
    * @author Desenvolvedor: Marcelo B. Paulino

    * @ignore

    $Revision: 31627 $
    $Name$
    $Author: cako $
    $Date: 2007-12-05 15:12:56 -0200 (Qua, 05 Dez 2007) $

    * Casos de uso: uc-02.03.05
*/

/*
$Log$
Revision 1.16  2007/01/25 11:50:46  cako
Bug #8175#

Revision 1.15  2006/11/13 14:58:47  cako
Bug #7244#

Revision 1.14  2006/10/23 16:16:40  cako
Bug #7275#

Revision 1.13  2006/07/14 15:47:00  jose.eduardo
Bug #5994#

Revision 1.12  2006/07/05 20:48:56  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GF_EMP_NEGOCIO."REmpenhoOrdemPagamento.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ConsultarOrdemPagamento";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$stCaminho   = CAM_GF_EMP_INSTANCIAS."ordemPagamento/";

$obRegra = new REmpenhoOrdemPagamento;

Sessao::remove('arNota');
$arFiltro = Sessao::read('filtro');

if ( !Sessao::read('paginando') ) {
    foreach ($_POST as $stCampo => $stValor) {
        $arFiltro[$stCampo] = $stValor;
    }
    $inPg = $_GET['pg'] ? $_GET['pg'] : 0;
    $inPos = $_GET['pos']? $_GET['pos'] : 0;
    Sessao::write('paginando', true);
    Sessao::write('filtro', $arFiltro);
} else {
    $inPg = $_GET['pg'];
    $inPg = $_GET['pos'];

    $_REQUEST['inCodigoEntidade'             ] = $arFiltro['inCodEntidade'];
    $_REQUEST['inCodEntidade'                ] = $arFiltro['inCodEntidade'];
    $_REQUEST['stExercicioEmpenho'           ] = $arFiltro['stExercicioEmpenho'];
    $_REQUEST['stExercicioOrdem'             ] = $arFiltro['stExercicioOrdem'];
    $_REQUEST['inCodEmpenhoInicial'          ] = $arFiltro['inCodEmpenhoInicial' ];
    $_REQUEST['inCodEmpenhoFinal'            ] = $arFiltro['inCodEmpenhoFinal'];
    $_REQUEST['inCodLiquidacaoInicial'       ] = $arFiltro['inCodLiquidacaoInicial'];
    $_REQUEST['inCodLiquidacaoFinal'         ] = $arFiltro['inCodLiquidacaoFinal'];
    $_REQUEST['inCodigoOrdemPagamentoInicial'] = $arFiltro['inCodigoOrdemPagamentoInicial'];
    $_REQUEST['inCodigoOrdemPagamentoFinal'  ] = $arFiltro['inCodigoOrdemPagamentoFinal'];
    $_REQUEST['inCodCredor'                  ] = $arFiltro['inCodCredor'];
    $_REQUEST['inCodRecurso'                 ] = $arFiltro['inCodRecurso'];
    $_REQUEST['inCodUso'                     ] = $arFiltro['inCodUso'];
    $_REQUEST['inCodDestinacao'              ] = $arFiltro['inCodDestinacao'];
    $_REQUEST['inCodEspecificacao'           ] = $arFiltro['inCodEspecificacao'];
    $_REQUEST['inCodDetalhamento'            ] = $arFiltro['inCodDetalhamento'];
    $_REQUEST['dtDataVencimento'             ] = $arFiltro['dtDataVencimento'];
    $_REQUEST['dtDataInicial'                ] = $arFiltro['dtDataInicial'];
    $_REQUEST['dtDataFinal'                  ] = $arFiltro['dtDataFinal'];
    $_REQUEST['inCodSituacao'                ] = $arFiltro['inCodSituacao'];
    $_GET['stAcao'                           ] = $arFiltro['stAcao'];
}

Sessao::write('pg', $inPg);
Sessao::write('pos', $inPos);

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "consultar";
}

//Define arquivos PHP para cada acao
switch ($stAcao) {
    case 'consultar': $pgProx = $pgForm; break;
    DEFAULT         : $pgProx = $pgForm;
}

$stCodEntidade = implode(',', $_REQUEST['inCodEntidade']);

$stExercicio = ( $_REQUEST['stExercicio'] ) ? $_REQUEST['stExercicio'] : Sessao::getExercicio();

//$obRegra->setExercicio                               ( $stExercicio                               );
$obRegra->obROrcamentoEntidade->setCodigoEntidade    ( $stCodEntidade                             );
$obRegra->setExercicio                               ( $_REQUEST['stExercicioOrdem']              );
$obRegra->obREmpenhoEmpenho->setExercicio            ( $_REQUEST['stExercicioEmpenho']            );
$obRegra->obREmpenhoEmpenho->setCodEmpenhoInicial    ( $_REQUEST['inCodEmpenhoInicial']           );
$obRegra->obREmpenhoEmpenho->setCodEmpenhoFinal      ( $_REQUEST['inCodEmpenhoFinal']             );
$obRegra->obREmpenhoNotaLiquidacao->setCodNotaInicial( $_REQUEST['inCodLiquidacaoInicial']        );
$obRegra->obREmpenhoNotaLiquidacao->setCodNotaFinal  ( $_REQUEST['inCodLiquidacaoFinal']          );
$obRegra->setCodigoOrdemInicial                      ( $_REQUEST['inCodigoOrdemPagamentoInicial'] );
$obRegra->setCodigoOrdemFinal                        ( $_REQUEST['inCodigoOrdemPagamentoFinal']   );
$obRegra->setFornecedor                              ( $_REQUEST['inCodCredor']                   );
$obRegra->setDataVencimento                          ( $_REQUEST['dtDataVencimento']              );
$obRegra->setDataEmissaoInicial                      ( $_REQUEST['dtDataInicial']                 );
$obRegra->setDataEmissaoFinal                        ( $_REQUEST['dtDataFinal']                   );
$obRegra->obREmpenhoEmpenho->obROrcamentoDespesa->obROrcamentoRecurso->setCodRecurso( $_REQUEST['inCodRecurso'] );
if($_REQUEST['inCodUso'] && $_REQUEST['inCodDestinacao'] && $_REQUEST['inCodEspecificacao'])
    $obRegra->obREmpenhoEmpenho->obROrcamentoDespesa->obROrcamentoRecurso->setDestinacaoRecurso( $_REQUEST['inCodUso'].".".$_REQUEST['inCodDestinacao'].".".$_REQUEST['inCodEspecificacao'] );

$obRegra->obREmpenhoEmpenho->obROrcamentoDespesa->obROrcamentoRecurso->setCodDetalhamento( $_REQUEST['inCodDetalhamento'] );

if ($_REQUEST['inCodSituacao'] == 1) {
    $obRegra->setListarNaoPaga   ( true );
} elseif ($_REQUEST['inCodSituacao'] == 2) {
    $obRegra->setListarPaga( true );
} elseif ($_REQUEST['inCodSituacao'] == 3) {
    $obRegra->setListarAnulada( true );
}

$obRegra->listar( $rsLista );

$rsLista->addFormatacao( "valor_pagamento" , "NUMERIC_BR" );
$rsLista->addFormatacao( "vl_nota", "NUMERIC_BR" );
$rsLista->addFormatacao( "vl_nota_anulacoes", "NUMERIC_BR" );

$obLista = new Lista;

$obLista->setRecordSet( $rsLista );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Entidade");
$obLista->ultimoCabecalho->setWidth( 30 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Nr.OP");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Credor");
$obLista->ultimoCabecalho->setWidth( 30 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Valor");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Vl Anulado");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Situação");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "entidade" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();
//$obLista->addDado();
//$obLista->ultimoDado->setCampo( "[cod_empenho]/[exercicio_empenho]" );
//$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
//$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "[cod_ordem]/[exercicio]" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "beneficiario" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "vl_nota" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "vl_nota_anulacoes" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "situacao" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();
$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->addCampo( "&inCodOrdem"         , "cod_ordem"         );
$obLista->ultimaAcao->addCampo( "&inCodEntidade"      , "cod_entidade"      );
$obLista->ultimaAcao->addCampo( "&stExercicio" , "exercicio" );
$obLista->ultimaAcao->addCampo( "&stExercicioEmpenho" , "exercicio_empenho" );

if ($stAcao == "consultar") {
    $obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink );
}

$obLista->commitAcao();
$obLista->show();
?>
