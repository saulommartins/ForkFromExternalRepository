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
    * Página de Listagem de Consulta de Empenho
    * Data de Criação   : 05/05/2005

    * @author Analista: Diego Victoria
    * @author Desenvolvedor: Lucas Leusin Oaigen

    * @ignore

    $Id: LSConsultarAutorizacao.php 59652 2014-09-03 19:36:55Z arthur $

    * Casos de uso: uc-02.03.02
                    uc-02.01.08

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_EMP_NEGOCIO."REmpenhoAutorizacaoEmpenho.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ConsultarAutorizacao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$stCaminho   = CAM_GF_EMP_INSTANCIAS."empenho/autorizacao/";

$obRegra        = new REmpenhoAutorizacaoEmpenho;
//$obRegra = new REmpenhoEmpenho;

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
    $inPos = $_GET['pos'];
    $arFiltro = Sessao::read('filtro');
    $_REQUEST['inCodEntidade'       ] = $arFiltro['inCodEntidade'      ];
    $_REQUEST['stExercicio'         ] = $arFiltro['stExercicio'        ];
    $_REQUEST['inCodAutorizacaoInicial'] = $arFiltro['inCodAutorizacaoInicial'];
    $_REQUEST['inCodAutorizacaoFinal'  ] = $arFiltro['inCodAutorizacaoFinal'    ];
    $_REQUEST['inNumOrgao'          ] = $arFiltro['inNumOrgao'         ];
    $_REQUEST['inNumUnidade'        ] = $arFiltro['inNumUnidade'       ];
    $_REQUEST['inCodFornecedor'     ] = $arFiltro['inCodFornecedor'    ];
    $_REQUEST['inCodDotacao'        ] = $arFiltro['inCodDotacao'       ];
    $_REQUEST['inCodDespesa'        ] = $arFiltro['inCodDespesa'       ];
    $_REQUEST['inCodRecurso'        ] = $arFiltro['inCodRecurso'       ];
    $_REQUEST['inCodUso'            ] = $arFiltro['inCodUso'];
    $_REQUEST['inCodDestinacao'     ] = $arFiltro['inCodDestinacao'];
    $_REQUEST['inCodEspecificacao'  ] = $arFiltro['inCodEspecificacao'];
    $_REQUEST['inCodDetalhamento'   ] = $arFiltro['inCodDetalhamento'];
    $_REQUEST['stDtInicial'         ] = $arFiltro['stDtInicial'        ];
    $_REQUEST['stDtFinal'           ] = $arFiltro['stDtFinal'          ];
    $_REQUEST['inCodHistorico'      ] = $arFiltro['inCodHistorico'     ];
    $_REQUEST['inSituacao'          ] = $arFiltro['inSituacao'         ];
    $_GET['stAcao'                  ] = $arFiltro['stAcao'             ];
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
    case 'alterar'  : $pgProx = $pgForm; break;
    case 'excluir'  : $pgProx = $pgProc; break;
    case 'consultar': $pgProx = $pgForm; break;
    DEFAULT         : $pgProx = $pgForm;
}

foreach ($_REQUEST['inCodEntidade'] as $value) {
    $stCodEntidade .= $value . " , ";
}
$stCodEntidade = substr($stCodEntidade,0,strlen($stCodEntidade)-2);

$stExercicio = ( $_REQUEST['stExercicio'] ) ? $_REQUEST['stExercicio'] : Sessao::getExercicio();

$obRegra->obROrcamentoEntidade->setCodigoEntidade( $stCodEntidade   );
$obRegra->setExercicio              ( $stExercicio                      );
$obRegra->setCodAutorizacaoInicial  ( $_REQUEST['inCodAutorizacaoInicial']);
$obRegra->setCodAutorizacaoFinal    ( $_REQUEST['inCodAutorizacaoFinal']);
$obRegra->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->setNumeroOrgao( $_REQUEST['inNumOrgao'] );
$obRegra->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->setNumeroUnidade( $_REQUEST['inNumUnidade'] );
$obRegra->obRCGM->setNumCGM         ( $_REQUEST['inCodFornecedor']      );
$obRegra->obROrcamentoDespesa->setCodDespesa( $_REQUEST['inCodDotacao'] );
$obRegra->obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->setCodEstrutural( $_REQUEST['inCodDespesa'] );
$obRegra->obROrcamentoDespesa->obROrcamentoRecurso->setCodRecurso( $_REQUEST['inCodRecurso']      );

if($_REQUEST['inCodUso'] && $_REQUEST['inCodDestinacao'] && $_REQUEST['inCodEspecificacao'])
    $obRegra->obROrcamentoDespesa->obROrcamentoRecurso->setDestinacaoRecurso( $_REQUEST['inCodUso'].".".$_REQUEST['inCodDestinacao'].".".$_REQUEST['inCodEspecificacao'] );

$obRegra->obROrcamentoDespesa->obROrcamentoRecurso->setCodDetalhamento( $_REQUEST['inCodDetalhamento'] );

$obRegra->setDtAutorizacaoInicial ( $_REQUEST['stDtInicial']          );
$obRegra->setDtAutorizacaoFinal   ( $_REQUEST['stDtFinal']            );
$obRegra->obREmpenhoHistorico->setCodHistorico( $_REQUEST['inCodHistorico'] );
$obRegra->setSituacao             ( $_REQUEST['inSituacao']           );

$obRegra->setBoEmpenhoCompraLicitacao( true );
$obRegra->setCodModalidadeCompra     ( $_REQUEST['inCodModalidadeCompra'] );
$obRegra->setCompraInicial           ( $_REQUEST['inCompraInicial'] );
$obRegra->setCompraFinal             ( $_REQUEST['inCompraFinal'] );
$obRegra->setCodModalidadeLicitacao  ( $_REQUEST['inCodModalidadeLicitacao'] );
$obRegra->setLicitacaoInicial        ( $_REQUEST['inLicitacaoInicial'] );
$obRegra->setLicitacaoFinal          ( $_REQUEST['inLicitacaoFinal'] );

$obRegra->listarConsulta( $rsLista );
$rsLista->addFormatacao('vl_empenhado', 'NUMERIC_BR');

$obLista = new Lista;

$obLista->setRecordSet($rsLista);
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth(5);
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Entidade");
$obLista->ultimoCabecalho->setWidth(10);
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Autorização");
$obLista->ultimoCabecalho->setWidth(10);
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Data Autorização");
$obLista->ultimoCabecalho->setWidth(15);
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Situação");
$obLista->ultimoCabecalho->setWidth(40);
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Valor");
$obLista->ultimoCabecalho->setWidth(15);
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth(5);
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_entidade" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "[cod_autorizacao]/[exercicio]" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "dt_autorizacao" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "situacao" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "vl_empenhado" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->addCampo( "&inCodAutorizacao" , "cod_autorizacao" );
$obLista->ultimaAcao->addCampo( "inCodEmpenho"     , "cod_empenho"     );
$obLista->ultimaAcao->addCampo( "inCodPreEmpenho"  , "cod_pre_empenho" );
$obLista->ultimaAcao->addCampo( "inCodEntidade"    , "cod_entidade"    );
$obLista->ultimaAcao->addCampo( "inCodReserva"     , "cod_reserva"     );
$obLista->ultimaAcao->addCampo( "stExercicio"      , "exercicio"       );
$obLista->ultimaAcao->addCampo( "stSituacao"       , "situacao"        );

if ($stAcao == "anular") {
    $obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink );
} elseif ($stAcao == "consultar") {
    $obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink );
}

$obLista->commitAcao();
$obLista->show();
?>
