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
include_once CAM_GF_LDO_VISAO . 'VLDOManterAcao.class.php';
include_once CAM_GF_LDO_VISAO . 'VLDOManterLDO.class.php';

$stPrograma = 'ManterAcao';
$pgFilt     = 'FL' . $stPrograma . '.php';
$pgList     = 'LS' . $stPrograma . '.php';
$pgForm     = 'FM' . $stPrograma . '.php';
$pgExcl     = 'FM' .'ExcluirAcao'. '.php';
$pgProc     = 'PR' . $stPrograma . '.php';
$pgOcul     = 'OC' . $stPrograma . '.php';

$stAcao = $_GET['stAcao'] ? $_GET['stAcao'] : $_POST['stAcao'];

if (empty($stAcao)) {
    $stAcao = 'alterar';
}

switch ($stAcao) {
case 'alterar':
    $pgProx = $pgForm;
    break;

case 'excluir':
    if (VLDOManterLDO::recuperarInstancia()->recuperarLDOHomologado()) {
        $stAcao = strtoupper($stAcao);
        $pgProx = $pgExcl;
    } else {
        $pgProx = $pgProc;
    }
    break;

case 'consultar':
    $pgProx = $pgCons;
    break;

default:
    $pgProx = $pgForm;
    break;
}

if ($_GET['pg'] and $_GET['pos']) {
    $sessao->link['pg']  = $_GET['pg'];
    $sessao->link['pos'] = $_GET['pos'];
} elseif (is_array($sessao->link)) {
    $_GET = $sessao->link;
    $_REQUEST = $sessao->link;
} else {
    foreach ($_REQUEST as $key => $valor) {
        $sessao->link[$key] = $valor;
    }
}

$rsAcoes = VLDOManterAcao::recuperarInstancia()->listar($_REQUEST['inNumAcaoInicio'], $_REQUEST['inNumAcaoFim']);

$obLista = new Lista();

$obLista->setMostraPaginacao(true);
$obLista->setTitulo('Lista de Ações');
$obLista->setRecordSet($rsAcoes);

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('&nbsp;');
$obLista->ultimoCabecalho->setWidth(5);
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Ação');
$obLista->ultimoCabecalho->setWidth(10);
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Descrição');
$obLista->ultimoCabecalho->setWidth(70);
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Valor');
$obLista->ultimoCabecalho->setWidth(15);
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('&nbsp;');
$obLista->ultimoCabecalho->setWidth(5);
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento('CENTRO');
$obLista->ultimoDado->setCampo('cod_acao');
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento('ESQUERDA');
$obLista->ultimoDado->setCampo('descricao');
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento('CENTRO');
$obLista->ultimoDado->setCampo('valor');
$obLista->commitDado();

$stCaminho = CAM_GF_LDO_INSTANCIAS . 'acao/' . $pgProx . '?' . Sessao::getID() . '&stAcao=' . $stAcao;

$obLista->addAcao();
$obLista->ultimaAcao->setAcao($stAcao);
$obLista->ultimaAcao->addCampo('&stAno',         'ano');
$obLista->ultimaAcao->addCampo('inCodPPA',       'cod_ppa');
$obLista->ultimaAcao->addCampo('inCodAcao',      'cod_acao');
$obLista->ultimaAcao->addCampo('inCodAcaoDados', 'cod_acao_dados');
$obLista->ultimaAcao->addCampo('inCodAcaoPPA',   'cod_acao_ppa');
$obLista->ultimaAcao->addCampo('&stDescQuestao', '[cod_acao] - [descricao]');
$obLista->ultimaAcao->setLink($stCaminho);
$obLista->commitAcao();

$obLista->show();
