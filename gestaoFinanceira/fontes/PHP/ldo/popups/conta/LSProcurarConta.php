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
 * Página de Lista do componente IPopUpConta
 * Data de Criação: 07/09/2009
 * Copyright CNM - Confederação Nacional de Municípios
 *
 * @author Janilson Mendes Pereira da Silva <janilson.mendes>
 * @package GF
 * @subpackage LDO
 * @uc uc-02.10.03
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_ORC_NEGOCIO . "ROrcamentoClassificacaoDespesa.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ProcurarConta";
$pgFilt = "FL".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".php";

include_once $pgJs;

$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];

//Define arquivos PHP para cada acao
switch ($stAcao) {
    case 'alterar'  : $pgProx = $pgForm; break;
    case 'excluir'  : $pgProx = $pgProc; break;
    DEFAULT         : $pgProx = $pgForm;
}

if ($_REQUEST["campoNom"]) {
    $stLink .= '&campoNom=' . $_REQUEST['campoNom'];
}

if ($_REQUEST["nomForm"]) {
    $stLink .= '&nomForm=' . $_REQUEST['nomForm'];
}

if ($_REQUEST["campoNum"]) {
    $stLink .= '&campoNum=' . $_REQUEST['campoNum'];
}

if ($_REQUEST['stDescricao']) {
    $stLink .= '&stDescricao=' . $_REQUEST['stDescricao'];
}

if ($_REQUEST['tipoBusca']) {
    $stLink .= '&tipoBusca=' . $_REQUEST['tipoBusca'];
}

if ($_REQUEST['stExercicio']) {
    $stLink .= '&stExercicio=' . $_REQUEST['stExercicio'];
} else {
    $_REQUEST['stExercicio']   = $_REQUEST['tipoBusca'];
    $stLink .= '&stExercicio=' . $_REQUEST['stExercicio'];
}

$stLink .= "&stAcao=".$stAcao;

$obROrcamentoClassificacaoDespesa = new ROrcamentoClassificacaoDespesa;

if ($_REQUEST['stDescricao']) {
    $obROrcamentoClassificacaoDespesa->setDescricao($_REQUEST['stDescricao']);
}

if ($_REQUEST['stExercicio']) {
    $obROrcamentoClassificacaoDespesa->setExercicio($_REQUEST['stExercicio']);
}

$obROrcamentoClassificacaoDespesa->listar($rsLista, " cod_conta ");

$obLista = new Lista;
$obLista->obPaginacao->setFiltro("&stLink=".$stLink);

$obLista->setRecordSet($rsLista);
$obLista->addCabecalho();

$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth(5);
$obLista->commitCabecalho();
$obLista->addCabecalho();

$obLista->ultimoCabecalho->addConteudo("Código da Conta");
$obLista->ultimoCabecalho->setWidth(20);
$obLista->commitCabecalho();
$obLista->addCabecalho();

$obLista->ultimoCabecalho->addConteudo("Descrição");
$obLista->ultimoCabecalho->setWidth(70);
$obLista->commitCabecalho();
$obLista->addCabecalho();

$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth(5);
$obLista->commitCabecalho();
$obLista->addCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo("cod_conta");
$obLista->ultimoDado->setAlinhamento('DIREITA');
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo("descricao");
$obLista->ultimoDado->setAlinhamento('ESQUERDA');
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao("SELECIONAR");
$obLista->ultimaAcao->setFuncao(true);
$obLista->ultimaAcao->setLink("JavaScript:insereCodConta();");
$obLista->ultimaAcao->addCampo("1", "descricao");
$obLista->ultimaAcao->addCampo("2", "cod_conta");
$obLista->commitAcao();
$obLista->show();
