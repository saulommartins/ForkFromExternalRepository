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
 * Página de Lista do componente IPopUpRubrica
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
include_once CAM_GF_ORC_NEGOCIO . "ROrcamentoReceita.class.php";
include_once CAM_GF_ORC_NEGOCIO . "ROrcamentoDespesa.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ProcurarRubrica";
$pgFilt = "FL".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".php";

include_once $pgJs;

$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];

switch ($stAcao) {
    case 'alterar'  : $pgProx = $pgForm; break;
    case 'excluir'  : $pgProx = $pgProc; break;
    default         : $pgProx = $pgForm;
}

if ($_REQUEST["campoNom"]) {
    $stLink .= '&campoNom='.$_REQUEST['campoNom'];
}

if ($_REQUEST["nomForm"]) {
    $stLink .= '&nomForm='.$_REQUEST['nomForm'];
}

if ($_REQUEST["campoNum"]) {
    $stLink .= '&campoNum='.$_REQUEST['campoNum'];
}

if ($_REQUEST['stDescricao']) {
    $stLink .= '&stDescricao='.$_REQUEST['stDescricao'];
}

if ($_REQUEST['inCodEntidade']) {
    $stLink .= '&inCodEntidade='.$_REQUEST['inCodEntidade'];
}

if ($_REQUEST['stCodEstrutural']) {
    $stLink .= '&stCodEstrutural='.$_REQUEST['stCodEstrutural'];
}

if ($_REQUEST['tipoBusca']) {
    $stLink .= '&tipoBusca='.$_REQUEST['tipoBusca'];
}

$stLink .= '&boDedutora='.$_REQUEST['boDedutora'];

$stLink .= '&stIdCodConta='.$_REQUEST['stIdCodConta'];

$stLink .= "&stAcao=".$stAcao;

switch ($_REQUEST['tipoBusca']) {
    case 'receita':
        $obROrcamentoReceita = new ROrcamentoReceita;
        $obROrcamentoReceita->obROrcamentoClassificacaoReceita->setDedutora((bool) $_REQUEST['boDedutora']);
        $obROrcamentoReceita->obROrcamentoClassificacaoReceita->setListarAnaliticas(true);
        $obROrcamentoReceita->obROrcamentoClassificacaoReceita->setMascClassificacao($_REQUEST['stCodEstrutural']);
        $obROrcamentoReceita->obROrcamentoClassificacaoReceita->setExercicio(Sessao::getExercicio());
        $obROrcamentoReceita->obROrcamentoEntidade->setCodigoEntidade($_REQUEST['inCodEntidade']);
        $obROrcamentoReceita->obROrcamentoClassificacaoReceita->setDescricao($_REQUEST['stDescricao']);
        $obROrcamentoReceita->obROrcamentoClassificacaoReceita->listar($rsLista, "ORDER BY cod_conta");
    break;

    case 'despesa':
        $obROrcamentoDespesa = new ROrcamentoDespesa;
        $obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->setMascClassificacao($_REQUEST['stCodEstrutural']);
        $obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->setExercicio(Sessao::getExercicio());
        $obROrcamentoDespesa->obROrcamentoEntidade->setCodigoEntidade($_REQUEST['inCodEntidade']);
        $obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->setDescricao($_REQUEST['stDescricao']);
        $obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->listar($rsLista, "ORDER BY cod_conta");
    break;

    default:
        $obROrcamentoReceita = new ROrcamentoReceita;
        $obROrcamentoReceita->obROrcamentoClassificacaoReceita->setDedutora((bool) $_REQUEST['boDedutora']);
        $obROrcamentoReceita->obROrcamentoClassificacaoReceita->setListarAnaliticas(true);
        $obROrcamentoReceita->obROrcamentoClassificacaoReceita->setMascClassificacao($_REQUEST['stCodEstrutural']);
        $obROrcamentoReceita->obROrcamentoClassificacaoReceita->setExercicio(Sessao::getExercicio());
        $obROrcamentoReceita->obROrcamentoEntidade->setCodigoEntidade($_REQUEST['inCodEntidade']);
        $obROrcamentoReceita->obROrcamentoClassificacaoReceita->setDescricao($_REQUEST['stDescricao']);
        $obROrcamentoReceita->obROrcamentoClassificacaoReceita->listar($rsLista, "ORDER BY cod_conta");
    break;
}

foreach ($rsLista->arElementos as $stChave => $stValor) {
    $rsLista->arElementos[$stChave]['cod_conta_hidden'] = $_REQUEST['stIdCodConta'];
}

$obLista = new Lista;
$obLista->obPaginacao->setFiltro("&stLink=".$stLink);

$obLista->setRecordSet($rsLista);
$obLista->addCabecalho();

$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth(5);
$obLista->commitCabecalho();
$obLista->addCabecalho();

$obLista->ultimoCabecalho->addConteudo("Código Classificação");
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

$obLista->addDado();
$obLista->ultimoDado->setCampo("mascara_classificacao");
$obLista->ultimoDado->setAlinhamento('DIREITA');
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo("descricao");
$obLista->ultimoDado->setAlinhamento('ESQUERDA');
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao("SELECIONAR");
$obLista->ultimaAcao->setFuncao(true);
$obLista->ultimaAcao->setLink("JavaScript:insereRubrica();");
$obLista->ultimaAcao->addCampo("1", "mascara_classificacao");
$obLista->ultimaAcao->addCampo("2", "descricao");
$obLista->ultimaAcao->addCampo("3", "cod_conta");
$obLista->ultimaAcao->addCampo("4", "cod_conta_hidden");
$obLista->commitAcao();
$obLista->show();
