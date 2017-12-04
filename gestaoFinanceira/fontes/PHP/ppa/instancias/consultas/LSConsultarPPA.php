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
    * Lista para consulta de PPA
    * Data de Criação   : 21/05/2009

    * @author Analista      Tonismar Régis Bernardo
    * @author Desenvolvedor Eduardo Paculski Schitz

    * @package URBEM
    * @subpackage

    * @ignore

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_PPA_MAPEAMENTO.'TPPA.class.php';

# Define o nome dos arquivos PHP
$stPrograma = 'ConsultarPPA';
$pgFilt = 'FL' . $stPrograma . '.php';
$pgList = 'LS' . $stPrograma . '.php';
$pgForm = 'FM' . $stPrograma . '.php';
$pgProc = 'PR' . $stPrograma . '.php';
$pgOcul = 'OC' . $stPrograma . '.php';
$pgProx = 'FM' . $stPrograma . '.php';

# Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');

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

# Obtem lista de ações
$arFiltro = array();

if ($_REQUEST['inCodPPA'] != '') {
    $arFiltro[] = ' ppa.cod_ppa = '.$_REQUEST['inCodPPA'].' ';
}

if ($_REQUEST['boHomologado'] != '' && $_REQUEST['boHomologado'] != 'n') {
    $arFiltro[] = " ppa.fn_verifica_homologacao(ppa.cod_ppa) = '".$_REQUEST['boHomologado']."' ";
}

if (count($arFiltro) > 0) {
    $stFiltro = ' WHERE ' . implode(' AND ',$arFiltro);
}

$obTPPA = new TPPA;
$stOrder = ' ORDER BY ppa.cod_ppa ';
$obTPPA->recuperaDadosPPA($rsListaPPA,$stFiltro,$stOrder);

$obLista = new Lista();
$obLista->setMostraPaginacao(true);
$obLista->setTitulo('Dados da Lista');
$obLista->setRecordSet($rsListaPPA);

# Colunas da lista
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('&nbsp;');
$obLista->ultimoCabecalho->setWidth(5);
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('PPA');
$obLista->ultimoCabecalho->setWidth(15);
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Status');
$obLista->ultimoCabecalho->setWidth(15);
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Data Encaminhamento');
$obLista->ultimoCabecalho->setWidth(20);
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Data Devolução');
$obLista->ultimoCabecalho->setWidth(20);
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Período Apuração Metas');
$obLista->ultimoCabecalho->setWidth(20);
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('&nbsp;');
$obLista->ultimoCabecalho->setWidth(5);
$obLista->commitCabecalho();

# Dados
$obLista->addDado();
$obLista->ultimoDado->setAlinhamento('CENTRO');
$obLista->ultimoDado->setCampo('[cod_ppa] - [ano_inicio] a [ano_final]');
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento('CENTRO');
$obLista->ultimoDado->setCampo('status');
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento('CENTRO');
$obLista->ultimoDado->setCampo('dt_encaminhamento');
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento('CENTRO');
$obLista->ultimoDado->setCampo('dt_devolucao');
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento('CENTRO');
$obLista->ultimoDado->setCampo('periodo_apuracao_metas');
$obLista->commitDado();

$stCaminho = $pgProx.'?'.Sessao::getID().'&stAcao='.$stAcao;

$obLista->addAcao();
$obLista->ultimaAcao->setAcao($stAcao);
$obLista->ultimaAcao->addCampo('&inCodPPA', 'cod_ppa');
$obLista->ultimaAcao->setLink($stCaminho);
$obLista->commitAcao();

$obLista->show();

?>
