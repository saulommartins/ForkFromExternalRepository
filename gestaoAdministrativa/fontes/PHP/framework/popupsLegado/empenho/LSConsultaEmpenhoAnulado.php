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
*
* Data de Criação: 27/10/2005

* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
* @author Documentor: Cassiano de Vasconcellos Ferreira

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00, uc-02.03.03
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_REGRA."REmpenhoEmpenho.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ConsultaEmpenhoAnulado";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";
$pgCons = $pgFilt;

$obRegra = new REmpenhoEmpenho ( $obRempenho );

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

if ($_REQUEST['stExercicio']) {
    $obRegra->setExercicio( $_REQUEST['stExercicio'] );
    $stLink .= '&stExercicio='.$_REQUEST['stExercicio'];
}
if ($_REQUEST['inCodEntidade']) {
    $obRegra->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST['inCodEntidade'] );
    $stLink .= '&inCodEntidade='.$_REQUEST['inCodEntidade'];
}
if ($_REQUEST['inCodEmpenho']) {
    $obRegra->setCodEmpenho( $_REQUEST['inCodEmpenho'] );
    $stLink .= '&inCodEmpenho='.$_REQUEST['inCodEmpenho'];
}

$stLink .= "&stAcao=".$stAcao;
$stLink .= "&nomForm=".$_REQUEST['nomForm'];
$stLink .= "&campoNum=".$_REQUEST['campoNum'];
$stLink .= "&campoNom=".$_REQUEST['campoNom'];
$stLink .= "&tipoBusca=".$_REQUEST['tipoBusca'];

$obRegra->listarAnulados( $rsLista );

$obLista = new Lista;
$obLista->obPaginacao->setFiltro("&stLink=".$stLink );

$obLista->setRecordSet( $rsLista );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Data Anulação");
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Valor Anulado");
$obLista->ultimoCabecalho->setWidth( 30 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "dt_anulado" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "vl_anulado" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();

$obLista->show();

?>
