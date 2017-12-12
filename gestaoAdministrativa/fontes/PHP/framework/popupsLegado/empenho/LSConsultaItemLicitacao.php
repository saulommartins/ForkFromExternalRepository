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
include_once( CAM_REGRA."REmpenhoAutorizacaoEmpenho.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ConsultaItemLicitacao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";
$pgCons = $pgFilt;

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

if ($_REQUEST['inCodLicitacao']) {
    $stLink .= '&inCodLicitacao='.$_REQUEST['inCodLicitacao'];
}
if ($_REQUEST['stTipoModalidade']) {
    $stLink .= '&stTipoModalidade='.$_REQUEST['stTipoModalidade'];
}
if ($_REQUEST['stNumCgm']) {
    $stLink .= '&stNumCgm='.$_REQUEST['stNumCgm'];
}
if ($_REQUEST['stDotacao']) {
    $stLink .= '&stDotacao='.$_REQUEST['stDotacao'];
}

$stLink .= "&stAcao=".$stAcao;
$stLink .= "&nomForm=".$_REQUEST['nomForm'];
$stLink .= "&campoNum=".$_REQUEST['campoNum'];
$stLink .= "&campoNom=".$_REQUEST['campoNom'];
$stLink .= "&tipoBusca=".$_REQUEST['tipoBusca'];

# alguem de jeito nisso, da aonde vem esse arItens???
#$rsItens = unserialize( sessao->transf3['arItens'] );
$inCount = 0;

while ( !$rsItens->eof() ) {
    if ( $rsItens->getCampo( 'dotacao' ) == $_REQUEST['stDotacao'] and $rsItens->getCampo( 'numcgm' ) == $_REQUEST['stNumCgm'] ) {
        $arLista[$inCount]['item']       = $rsItens->getCampo( 'num_item'   ).' - '.$rsItens->getCampo( 'nom_item' );
        $arLista[$inCount]['quantidade'] = $rsItens->getCampo( 'quantidade' );
        $arLista[$inCount]['vl_total']   = $rsItens->getCampo( 'vl_total'   );
        $inCount++;
    }
    $rsItens->proximo();
}

$rsLista = new RecordSet;
$rsLista->preenche( $arLista );

$obLista = new Lista;
$obLista->obPaginacao->setFiltro("&stLink=".$stLink );

$rsLista->addFormatacao( 'vl_total', 'NUMERIC_BR' );

$obLista->setRecordSet( $rsLista );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Item");
$obLista->ultimoCabecalho->setWidth( 71 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Quantidade");
$obLista->ultimoCabecalho->setWidth( 12 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Valor");
$obLista->ultimoCabecalho->setWidth( 12 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "item" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "quantidade" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "vl_total" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();

$obLista->show();

?>
