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
    * Data de Criação   : 20/10/2006

    * @author Analista: Gelson
    * @author Desenvolvedor: Rodrigo

    $Id: LSManterAdiantamentosSubvencoes.php 59612 2014-09-02 12:00:51Z gelson $

    * @ignore

    * Casos de uso: uc-02.03.31
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(  TEMP."TEmpenhoEmpenho.class.php"                                                       );

//Define o nome dos arquivos PHP
$stPrograma = "ManterAdiantamentosSubvencoes";

$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgCons = "FMConsultarAdiantamentosSubvencoes.php";

$stCaminho = CAM_GF_EMP_INSTANCIAS."empenho/";
$stCodEntidade = null;
$stLink        = null;

$obTAutorizacao = new TEmpenhoEmpenho();
//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');

//Define arquivos PHP para cada acao
switch ($stAcao) {
    case 'alterar'  : $pgProx = $pgForm; break;
    case 'excluir'  : $pgProx = $pgProc; break;
    case 'consultar': $pgProx = $pgCons; break;
    case 'anular'   : $pgProx = 'FMAnularAdiantamentosSubvencoes.php'; break;
    default         : $pgProx = $pgForm;
}

$stExercicio   = $request->get('stExercicio', Sessao::getExercicio());

$obTAutorizacao->setDado('exercicio',$stExercicio );

$rsLista = new RecordSet();
$obLista = new Lista();
$arFiltro = Sessao::read('filtro');

$boPaginando = false;
if ( !Sessao::read('paginando') ) {
    foreach ($_POST as $stCampo => $stValor) {
        $arFiltro[$stCampo] = $stValor;
    }
    $inPg = $_GET['pg'] ? $_GET['pg'] : 0;
    $inPos = $_GET['pos']? $_GET['pos'] : 0;
    $boPaginando = true;
} else {
    $inPg = $_GET['pg'];
    $inPos = $_GET['pos'];
}

$_REQUEST['inCodEntidade'       ] = $arFiltro['inCodEntidade'      ];
$_REQUEST['inCodEmpenhoInicial' ] = $arFiltro['inCodEmpenhoInicial'];
$_REQUEST['inCodEmpenhoFinal'   ] = $arFiltro['inCodEmpenhoFinal'  ];
$_REQUEST['stDtInicial'         ] = (array_key_exists('stDtInicial',$arFiltro)) ? $arFiltro['stDtInicial'] : '';
$_REQUEST['stDtFinal'           ] = (array_key_exists('stDtFinal',$arFiltro)) ? $arFiltro['stDtFinal'] : '';
$_REQUEST['inCodFornecedor'     ] = $arFiltro['inCodFornecedor'    ];
$_REQUEST['stAcao'              ] = $arFiltro['stAcao'             ];

Sessao::write('filtro',$arFiltro);
Sessao::write('pg',$inPg);
Sessao::write('pos',$inPos);
Sessao::write('paginando',$boPaginando);

//Inicio da listagem de dados para prestação de contas

include_once( CAM_GF_EMP_NEGOCIO."REmpenhoEmpenho.class.php" );

foreach ($_REQUEST['inCodEntidade'] as $value) { $stCodEntidade .= $value . " , "; }
$stCodEntidade = substr($stCodEntidade,0,strlen($stCodEntidade)-2);
$stExercicio   = $request->get('stExercicio', Sessao::getExercicio());
$obRegra       = new REmpenhoEmpenho;

$obRegra->obROrcamentoEntidade->setCodigoEntidade( $stCodEntidade                   );
$obRegra->setExercicio                           ( $stExercicio                     );
$obRegra->setCodEmpenhoInicial                   ( $_REQUEST['inCodEmpenhoInicial'] );
$obRegra->setCodEmpenhoFinal                     ( $_REQUEST['inCodEmpenhoFinal']   );
$obRegra->setDtEmpenhoInicial                    ( $request->get('stDataInicial')   );
$obRegra->setDtEmpenhoFinal                      ( $request->get('stDataFinal')     );
$obRegra->obRCGM->setNumCGM                      ( $_REQUEST['inCodFornecedor']     );

if ($_REQUEST['stAcao'] == 'incluir') {
    $obRegra->setSituacao(2);
}

if ($_REQUEST['stAcao'] == 'anular') {
      $obRegra->setSituacao(2); // Alterado para listar prestações não completas
}

if ($_REQUEST['stAcao'] == 'consultar') {
      $obRegra->setSituacao(3);
}

$obRegra->listarAdiantamentoSubvencao( $rsLista );

$rsLista->addFormatacao("vl_empenhado","NUMERIC_BR");

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

$obLista->ultimoCabecalho->addConteudo("Credor");
$obLista->ultimoCabecalho->setWidth( 70 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Valor");
$obLista->ultimoCabecalho->setWidth( 5 );
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

$obLista->ultimoDado->setCampo( "nom_fornecedor" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "vl_empenhado" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao ( $stAcao );

$obLista->ultimaAcao->addCampo( "&inCodEmpenho"      , "cod_empenho"         );
$obLista->ultimaAcao->addCampo( "inCodPreEmpenho"    , "cod_pre_empenho"     );
$obLista->ultimaAcao->addCampo( "inCodAutorizacao"   , "cod_autorizacao"     );
$obLista->ultimaAcao->addCampo( "inCodEntidade"      , "cod_entidade"        );
$obLista->ultimaAcao->addCampo( "inCodReserva"       , "cod_reserva"         );
$obLista->ultimaAcao->addCampo( "inCodContrapartida" , "conta_contrapartida" );
$obLista->ultimaAcao->addCampo( "boImplantado"       , "implantado"          );
$obLista->ultimaAcao->addCampo( "stExercicioEmpenho" , "exercicio"           );

$obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink );

$obLista->commitAcao();
$obLista->show();
?>
