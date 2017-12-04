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
    * Data de Criação   : 09/05/2005

    * @author Analista: Diego Victoria
    * @author Desenvolvedor: Lucas Leusin Oaigen

    * @ignore

    $Revision: 30805 $
    $Name$
    $Autor: $
    $Date: 2007-09-06 17:29:28 -0300 (Qui, 06 Set 2007) $

    * Casos de uso: uc-02.03.02
                    uc-02.01.08
*/

/*
$Log$
Revision 1.9  2007/09/06 20:28:28  luciano
Ticket#9094#

Revision 1.8  2007/02/23 15:15:05  gelson
Sempre que for autorização tem que ir a reserva. Adicionado em todos arquivos o caso de uso da reserva.

Revision 1.7  2006/10/19 19:25:44  larocca
Bug #7245#

Revision 1.6  2006/09/21 10:47:27  jose.eduardo
Bug #6976#

Revision 1.5  2006/07/05 20:47:28  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_EMP_NEGOCIO."REmpenhoEmpenhoAutorizacao.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "DuplicarAutorizacao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$stCaminho   = CAM_GF_EMP_INSTANCIAS."empenho/autorizacao/";

$obRegra = new REmpenhoEmpenhoAutorizacao;

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
    $_REQUEST['stDtInicio'          ] = $arFiltro['stDtInicio'         ];
    $_REQUEST['stDtTermino'         ] = $arFiltro['stDtTermino'        ];
    $_REQUEST['inCodEmpenhoInicial' ] = $arFiltro['inCodEmpenhoInicial'];
    $_REQUEST['inCodEmpenhoFinal'   ] = $arFiltro['inCodEmpenhoFinal'  ];
    $_REQUEST['inCodAutorizacaoInicial'] = $arFiltro['inCodAutorizacaoInicial'];
    $_REQUEST['inCodAutorizacaoFinal'  ] = $arFiltro['inCodAutorizacaoFinal'    ];
    $_REQUEST['stExercicio'         ] = $arFiltro['stExercicio'        ];
    $_REQUEST['inCodFornecedor'     ] = $arFiltro['inCodFornecedor'    ];
    $_GET['stAcao'                  ] = $arFiltro['stAcao'             ];
}

Sessao::write('inPg', $inPg);
Sessao::write('inPos', $inPos);

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "duplicar";
}

//Define arquivos PHP para cada acao
switch ($stAcao) {
    case 'alterar'  : $pgProx = $pgForm; break;
    case 'excluir'  : $pgProx = $pgProc; break;
    case 'consultar': $pgProx = $pgForm; break;
    case 'duplicar' : $pgProx = $pgProc; break;
    DEFAULT         : $pgProx = $pgForm;
}

foreach ($_REQUEST['inCodEntidade'] as $value) {
    $stCodEntidade .= $value . " , ";
}
$stCodEntidade = substr($stCodEntidade,0,strlen($stCodEntidade)-2);

//$stExercicio = ( $_REQUEST['stExercicio'] ) ? $_REQUEST['stExercicio'] : Sessao::getExercicio();

$obRegra->obREmpenhoAutorizacaoEmpenho->obROrcamentoEntidade->setCodigoEntidade( $stCodEntidade       );
$obRegra->obREmpenhoAutorizacaoEmpenho->setDtAutorizacaoInicial   ( $_REQUEST['stDtInicio']           );
$obRegra->obREmpenhoAutorizacaoEmpenho->setDtAutorizacaoFinal     ( $_REQUEST['stDtTermino']          );
$obRegra->obREmpenhoAutorizacaoEmpenho->setCodAutorizacaoInicial  ( $_REQUEST['inCodAutorizacaoInicial']);
$obRegra->obREmpenhoAutorizacaoEmpenho->setCodAutorizacaoFinal    ( $_REQUEST['inCodAutorizacaoFinal']);
$obRegra->obREmpenhoEmpenho->setCodEmpenhoInicial                 ( $_REQUEST['inCodEmpenhoInicial']  );
$obRegra->obREmpenhoEmpenho->setCodEmpenhoFinal                   ( $_REQUEST['inCodEmpenhoFinal']    );
$obRegra->obREmpenhoAutorizacaoEmpenho->setExercicio              ( $_REQUEST['stExercicio']          );
$obRegra->obREmpenhoAutorizacaoEmpenho->obRCGM->setNumCGM         ( $_REQUEST['inCodFornecedor']      );
$obRegra->obREmpenhoAutorizacaoEmpenho->setAnuladaTotal           ( true                              );
$obRegra->obREmpenhoAutorizacaoEmpenho->setSituacao               ( 3                                 );

$obRegra->listarAutorizacao( $rsLista );

$rsLista->addFormatacao("vl_empenhado", "NUMERIC_BR");

$stLink .= "&stAcao=".$stAcao;
if ($_GET["pg"] and  $_GET["pos"]) {
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
$obLista->ultimoCabecalho->addConteudo("Data Autorização");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Credor");
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Valor");
$obLista->ultimoCabecalho->setWidth( 10 );
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
$obLista->ultimaAcao->addCampo( "inCodCategoria"   , "cod_categoria"   );

if ($stAcao == "anular") {
    $obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink );
} elseif ($stAcao == "consultar") {
    $obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink );
} elseif ($stAcao == "duplicar") {
    $obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink );
}

$obLista->commitAcao();
$obLista->show();
?>
