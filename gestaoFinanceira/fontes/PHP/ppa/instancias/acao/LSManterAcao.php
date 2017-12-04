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
 * Página de Lista do 02.10.03 - Manter Ação
 * Data de Criação: 16/02/2009
 * Copyright CNM - Confederação Nacional de Municípios
 *
 * @author Fellipe Esteves dos Santos <fellipe.santos>
 * @package GF
 * @subpackage LDO
 * @uc 02.10.03 - Manter Ação
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

include_once CAM_GF_PPA_MAPEAMENTO . 'TPPAAcao.class.php';

# Define o nome dos arquivos PHP
$stPrograma = 'ManterAcao';
$pgFilt = 'FL' . $stPrograma . '.php';
$pgList = 'LS' . $stPrograma . '.php';
$pgForm = 'FM' . $stPrograma . '.php';
$pgProc = 'PR' . $stPrograma . '.php';
$pgOcul = 'OC' . $stPrograma . '.php';
$pgExcl = $pgProc;
$pgCons = 'FMConsultarAcao.php';

# Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ? $_GET['stAcao'] : $_POST['stAcao'];

if (empty($stAcao)) {
    $stAcao = 'alterar';
}

# Define arquivos de instância para cada ação.
switch ($stAcao) {
case 'alterar':
    $pgProx = $pgForm;
    break;

case 'excluir':
    $pgProx = $pgExcl;
    break;

case 'consultar':
    $pgProx = $pgCons;
    break;

default:
    $pgProx = $pgForm;
    break;
}

$arFiltro = Sessao::read('filtro');
if ($_POST OR $_GET['pg']) {
    foreach ($_REQUEST as $stCampo => $stValor) {
        $arFiltro['filtro'][$stCampo] = $stValor;
    }
    $boPaginando = true;

    Sessao::write('filtro',$arFiltro);
    Sessao::write('paginando',$boPaginando);
} else {
    foreach ($arFiltro['filtro'] AS $stKey=>$stValue) {
        $_REQUEST[$stKey] = $stValue;
    }
    $_GET['pg']  = $_REQUEST['pg' ];
    $_GET['pos'] = $_REQUEST['pos'];
}

$arFiltro = array();

if ($_REQUEST['inCodPPA'] != '') {
    $arFiltro[] = ' cod_ppa::INTEGER = ' . $_REQUEST['inCodPPA'];
}
if ($_REQUEST['inCodPrograma'] != '') {
    $arFiltro[] = ' num_programa::INTEGER = ' . $_REQUEST['inCodPrograma'];
}
if ($_REQUEST['inCodAcaoInicio'] != '') {
    $arFiltro[] = ' num_acao::INTEGER >= ' . $_REQUEST['inCodAcaoInicio'];
}
if ($_REQUEST['inCodAcaoFim'] != '') {
    $arFiltro[] = ' num_acao::INTEGER <= ' . $_REQUEST['inCodAcaoFim'];
}
if ($_REQUEST['stTitulo'] != '') {
    $arFiltro[] = " titulo ILIKE '%" . $_REQUEST['stTitulo'] . "%' ";
}
if ($_REQUEST['inCodTipoAcao'] == '1') {
    $arFiltro[] = " cod_tipo IN (1,2,3) ";
}
if ($_REQUEST['inCodTipoAcao'] == '2') {
    $arFiltro[] = " cod_tipo IN (4,5,6,7,8) ";
}
if ($_REQUEST['inCodTipo'] != '') {
    $arFiltro[] = " cod_tipo = ".$_REQUEST['inCodTipo']." ";
}
if ( isset($_REQUEST['inCodIdentificadorAcao'])) {
    if ( $_REQUEST['inCodIdentificadorAcao'] == '' )
        $arFiltro[] = " num_programa IS NOT NULL ";       
    else
        $arFiltro[] = " acao_identificador_acao.cod_identificador = ".$_REQUEST['inCodIdentificadorAcao']." ";   
}

if (count($arFiltro) > 0) {
    $stFiltro = ' WHERE ' . implode(' AND ',$arFiltro);
}

$obTPPAAcao = new TPPAAcao;
$stOrder = ' ORDER BY num_acao, cod_ppa ';
if ($_REQUEST['stAcao'] == 'alterar') {
    $inCodUf = SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio(),$boTransacao);
    if ( ($inCodUf == 2) || ($inCodUf = 27)) {
        $obTPPAAcao->setDado('cod_uf',$inCodUf);
        $obTPPAAcao->recuperaListaAcoesProgramasTCE($rsListaAcao,$stFiltro,$stOrder);              
    }else{
        $obTPPAAcao->recuperaListaAcoesProgramas($rsListaAcao,$stFiltro,$stOrder);
    }
} else {
    $obTPPAAcao->recuperaListaAcoesProgramasExclusao($rsListaAcao,$stFiltro,$stOrder);
}

$obLista = new Lista();
$obLista->setMostraPaginacao(true);
$obLista->setTitulo('Dados da Lista');
$obLista->setRecordSet($rsListaAcao);

# Colunas da lista
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('&nbsp;');
$obLista->ultimoCabecalho->setWidth(5);
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Código Ação');
$obLista->ultimoCabecalho->setWidth(8);
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('PPA');
$obLista->ultimoCabecalho->setWidth(5);
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Tipo');
$obLista->ultimoCabecalho->setWidth(17);
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Descrição da Ação');
$obLista->ultimoCabecalho->setWidth(50);
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Valor da Ação');
$obLista->ultimoCabecalho->setWidth(15);
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('&nbsp;');
$obLista->ultimoCabecalho->setWidth(5);
$obLista->commitCabecalho();

# Dados
$obLista->addDado();
$obLista->ultimoDado->setAlinhamento('CENTRO');
$obLista->ultimoDado->setCampo('num_acao');
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento('CENTRO');
$obLista->ultimoDado->setCampo('cod_ppa');
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento('CENTRO');
$obLista->ultimoDado->setCampo('desc_tipo');
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento('ESQUERDA');
$obLista->ultimoDado->setCampo('descricao');
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento('CENTRO');
$obLista->ultimoDado->setCampo('valor');
$obLista->commitDado();

# Define ação e caminho.
if ($stAcao == 'excluir') {
    $stCaminho =  CAM_GF_PPA_INSTANCIAS . 'acao/' . $pgProx . '?' . Sessao::getID() . '&stAcao=' . $stAcao;
} else {
    $stCaminho = $pgProx . '?' . Sessao::getID() . '&stAcao=' . $stAcao;
}

$obLista->addAcao();
$obLista->ultimaAcao->setAcao($stAcao);
$obLista->ultimaAcao->addCampo('&inCodPPA', 'cod_ppa');
$obLista->ultimaAcao->addCampo('inCodAcao', 'cod_acao');
$obLista->ultimaAcao->addCampo('inNumAcao', 'num_acao');
$obLista->ultimaAcao->addCampo('stExercicio', 'exercicio');
$obLista->ultimaAcao->addCampo('inCodPrograma', 'cod_programa');
$obLista->ultimaAcao->addCampo('stDescricao', 'descricao');
$obLista->ultimaAcao->addCampo('tsAcaoDados', 'ultimo_timestamp_acao_dados');
$obLista->ultimaAcao->addCampo( "&stDescQuestao","[num_acao]");
$obLista->ultimaAcao->setLink($stCaminho);
$obLista->commitAcao();

$obLista->show();
