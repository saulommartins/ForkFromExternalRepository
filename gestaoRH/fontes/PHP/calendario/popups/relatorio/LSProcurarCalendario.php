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
    * Página de Listagem de Calendários
    * Data de Criação   : 19/08/2004

    * @author Analista: Leandro Oliveira
    * @author Desenvolvedor Eduardo Martins

    * @ignore
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GRH_CAL_NEGOCIO."RCalendario.class.php" );
include_once( CAM_GRH_CAL_NEGOCIO."RCalendarioFeriado.class.php" );
include_once( CAM_GRH_CAL_NEGOCIO."RCalendarioFeriadoVariavel.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ProcurarCalendario";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";
$pgCons = $pgFilt;

include_once( $pgJS );

$stCaminho   = "../modulos/calendario/relatorio/";

$obRCalendario = new RCalendario;

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

//Define arquivos PHP para cada acao
switch ($stAcao) {
    case 'alterar'  : $pgProx = $pgForm; break;
    case 'baixar'   : $pgProx = $pgBaix; break;
    case 'excluir'  : $pgProx = $pgProc; break;
    case 'prorrogar': $pgProx = $pgCons; break;
    case 'consultar': $pgProx = $pgCons; break;
    DEFAULT         : $pgProx = $pgForm;
}

// //Monta sessao com os valores do filtro
// if ( is_array(#sessao->link) ) {
//     $_REQUEST = #sessao->link;
// } else {
//     foreach ($_REQUEST as $key => $valor) {
//         #sessao->link[$key] = $valor;
//     }
// }

$stDescricao = $_REQUEST[ 'stDescricao' ];

$arSessaoLink = Sessao::read('link');
if (!$arSessaoLink['paginando']) {

    foreach ($_POST as $stCampo => $stValor) {
        $arSessaoLink['filtro'][$stCampo] = $stValor;
    }

    $arSessaoLink['pg']  = $_GET['pg'] ? $_GET['pg'] : 0;
    $arSessaoLink['pos'] = $_GET['pos']? $_GET['pos'] : 0;
    $arSessaoLink['paginando'] = true;
} else {
    $arSessaoLink['pg']  = $_GET['pg'];
    $arSessaoLink['pos'] = $_GET['pos'];
}
Sessao::write('link', $arSessaoLink);

$stLink .= "&stAcao=".$stAcao;

$stFiltro = " WHERE cod_calendar != 0";
if ($stDescricao) {
    $stFiltro .= " AND LOWER(descricao) LIKE LOWER('%".$stDescricao."%') ";
}

include_once(CAM_GRH_CAL_MAPEAMENTO."TCalendarioCalendarioCadastro.class.php");
$obTCalendarioCalendarioCadastro = new TCalendarioCalendarioCadastro;
$obTCalendarioCalendarioCadastro->recuperaTodos( $rsLista, $stFiltro );

$obLista = new Lista;
//$obLista->obPaginacao->setFiltro("&stLink=".$stLink );

$obLista->setRecordSet( $rsLista );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Codigo");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Descrição ");
$obLista->ultimoCabecalho->setWidth( 70 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_calendar" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "descricao" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();

$stAcao = "SELECIONAR";
$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->setFuncao( true );
$obLista->ultimaAcao->setLink( "JavaScript:insere();" );
$obLista->ultimaAcao->addCampo("1","cod_calendar");
$obLista->ultimaAcao->addCampo("2","descricao");
$obLista->commitAcao();

$obLista->show();

?>
