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

Casos de uso: uc-01.01.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GF_EMP_NEGOCIO."REmpenhoOrdemPagamento.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "Empenho";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";
$pgCons = $pgFilt;

include_once( $pgJS );

//$stCaminho   = "../modulos/calendario/relatorio/";

$obRegra = new REmpenhoOrdemPagamento( $obRempenho );

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

//Monta s essao com os valores do filtro
if ( is_array(Sessao::read('linkPopUp')) ) {
    $_REQUEST = Sessao::read('linkPopUp');
} else {
    $arLinkPopUp = array();
    foreach ($_REQUEST as $key => $valor) {
        $arLinkPopUp[$key] = $valor;
    }
    Sessao::write('linkPopUp',$arLinkPopUp);
}

if ($_REQUEST['stExercicioEmpenho']) {
    $obRegra->obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->setExercicio( $_REQUEST['stExercicioEmpenho'] );
    $stLink .= '&stExercicioEmpenho='.$_REQUEST['stExercicioEmpenho'];
}
if ($_REQUEST['inCodigoEntidade']) {
    $obRegra->obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST['inCodigoEntidade'] );
    $stLink .= '&inCodigoEntidade='.$_REQUEST['inCodigoEntidade'];
}
if ($_REQUEST['inCodEmpenhoInicial']) {
    $obRegra->obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->setCodEmpenhoInicial( $_REQUEST['inCodEmpenhoInicial'] );
    $stLink .= '&inCodEmpenhoInicial='.$_REQUEST['inCodEmpenhoInicial'];
}
if ($_REQUEST['inCodEmpenhoFinal']) {
    $obRegra->obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->setCodEmpenhoFinal( $_REQUEST['inCodEmpenhoFinal'] );
    $stLink .= '&inCodEmpenhoFinal='.$_REQUEST['inCodEmpenhoFinal'];
}
if ($_REQUEST['stDtInicial']) {
    $obRegra->obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->setDtEmpenhoInicial( $_REQUEST['stDtInicial'] );
    $stLink .= '&stDtInicial='.$_REQUEST['stDtInicial'];
}
if ($_REQUEST['stDtEmpenhoFinal']) {
    $obRegra->obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->setDtEmpenhoFinal( $_REQUEST['stDtFinal'] );
    $stLink .= '&stDtFinal='.$_REQUEST['stDtFinal'];
}

$stLink .= "&stAcao=".$stAcao;
$stLink .= "&nomForm=".$_REQUEST['nomForm'];
$stLink .= "&campoNum=".$_REQUEST['campoNum'];
$stLink .= "&campoNom=".$_REQUEST['campoNom'];
$stLink .= "&tipoBusca=".$_REQUEST['tipoBusca'];

$obRegra->obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->checarImplantado( $boImplantado );
if ($boImplantado) {
    $obRegra->obREmpenhoNotaLiquidacao->listarNotasDisponiveisImplantadas( $rsLista );
} else {
    $obRegra->obREmpenhoNotaLiquidacao->listarNotasDisponiveis( $rsLista );
}

$obLista = new Lista;
$obLista->obPaginacao->setFiltro("&stLink=".$stLink );

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
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Data do Empenho");
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Fornecedor");
$obLista->ultimoCabecalho->setWidth( 45 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Recurso");
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
$obLista->ultimoDado->setCampo( "[cod_empenho]/[exercicio_empenho]" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "dt_empenho" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "beneficiario" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_recurso" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();

$stAcao = "SELECIONAR";
$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->setFuncao( true );
$obLista->ultimaAcao->setLink( "JavaScript:insere();" );
$obLista->ultimaAcao->addCampo("1","cod_empenho");
$obLista->ultimaAcao->addCampo("2","beneficiario");
$obLista->commitAcao();

$obLista->show();

?>
