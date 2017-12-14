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
    * Lista de Demonstrativo de Riscos Fiscais
    * Data de Criação   : 02/06/2009

    * @author Analista      Tonismar Régis Bernardo <tonismar.bernardo@cnm.org.br>
    * @author Desenvolvedor Eduardo Paculski Schitz <eduardo.scritz@cnm.org.br>

    * @package URBEM
    * @subpackage

    * @ignore

    $Id:$
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GPC_STN_MAPEAMENTO.'TSTNRiscosFiscais.class.php';

// Define o nome dos arquivos PHP
$stPrograma = 'ManterRiscosFiscais';
$pgFilt = 'FL' . $stPrograma . '.php';
$pgList = 'LS' . $stPrograma . '.php';
$pgForm = 'FM' . $stPrograma . '.php';
$pgProc = 'PR' . $stPrograma . '.php';
$pgOcul = 'OC' . $stPrograma . '.php';

// Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');

if (empty($stAcao)) {
    $stAcao = 'alterar';
}

$arFiltro = Sessao::read('filtro');
if (!$arFiltro['paginando']) {
    foreach ($_REQUEST as $stCampo => $stValor) {
        $arFiltro[$stCampo] = $stValor;
    }
    $arFiltro['paginando'] = true;
    Sessao::write('filtro', $arFiltro);
} else {
    foreach ($arFiltro as $stKey => $stValue) {
        $_REQUEST[$stKey] = $stValue;
    }
}

$arFiltro = array();
if ($_REQUEST['stExercicio'] != '') {
    $arFiltro[] = " riscos_fiscais.exercicio = '".$_REQUEST['stExercicio']."' ";
}
if ($_REQUEST['inCodEntidade'] != '') {
    $arFiltro[] = ' riscos_fiscais.cod_entidade IN ('.implode(', ', $_REQUEST['inCodEntidade']).') ';
}

if (!empty($arFiltro)) {
    $stFiltro = ' WHERE '.implode(' AND ', $arFiltro);
}

$obTSTNRiscosFiscais = new TSTNRiscosFiscais;
$stOrder = ' ORDER BY riscos_fiscais.cod_entidade, riscos_fiscais.cod_risco, riscos_fiscais.exercicio ';
$obTSTNRiscosFiscais->listRiscosFiscais($rsRiscosFiscais,$stFiltro,$stOrder);

$rsRiscosFiscais->addFormatacao('valor', 'NUMERIC_BR');
$obLista = new Lista();
$obLista->setMostraPaginacao(true);
$obLista->setTitulo('Dados da Lista');
$obLista->setRecordSet($rsRiscosFiscais);

// Colunas da lista
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('&nbsp;');
$obLista->ultimoCabecalho->setWidth(5);
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Entidade');
$obLista->ultimoCabecalho->setWidth(25);
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Descrição do Risco');
$obLista->ultimoCabecalho->setWidth(55);
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Valor');
$obLista->ultimoCabecalho->setWidth(15);
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('&nbsp;');
$obLista->ultimoCabecalho->setWidth(5);
$obLista->commitCabecalho();

// Dados
$obLista->addDado();
$obLista->ultimoDado->setAlinhamento('ESQUERDA');
$obLista->ultimoDado->setCampo('[cod_entidade] - [nom_cgm]');
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento('ESQUERDA');
$obLista->ultimoDado->setCampo('[cod_risco] - [descricao]');
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento('CENTRO');
$obLista->ultimoDado->setCampo('valor');
$obLista->commitDado();

// Define ação e caminho.
if ($stAcao == 'excluir') {
    $stCaminho = CAM_GPC_STN_INSTANCIAS.'configuracao/'.$pgProc.'?stAcao=excluirDemonstrativo';
} else {
    $stCaminho = $pgForm.'?stAcao='.$stAcao;
}
$stCaminho .= '&pg='.$_REQUEST['pg'].'&pos='.$_REQUEST['pos'].'&paginando='.$_REQUEST['paginando'].'&';

$obLista->addAcao();
$obLista->ultimaAcao->setAcao ($stAcao);
$obLista->ultimaAcao->addCampo('inCodRisco'        , 'cod_risco');
$obLista->ultimaAcao->addCampo('inCodEntidade'     , 'cod_entidade');
$obLista->ultimaAcao->addCampo('stExercicio'       , 'exercicio');
$obLista->ultimaAcao->addCampo('inCodIdentificador', 'cod_identificador');
$obLista->ultimaAcao->setLink ($stCaminho);
$obLista->commitAcao();

$obLista->show();
