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
 * Lista de acoes para validacao
 *
 * @category    Urbem
 * @package     LDO
 * @author      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
 * @author      Henrique Boaventura <henrique.boaventura@cnm.org.br>
 * $Id:$
 */

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once CAM_GF_LDO_NEGOCIO.'RLDOValidarAcao.class.php';
require_once CAM_GF_LDO_VISAO.'VLDOValidarAcao.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "VincularAcao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

require_once 'JSValidarAcao.js';

$arFiltro = Sessao::read('filtro');
if ($_POST || $_GET['pg']) {
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

$obModel = new RLDOValidarAcao();
$obView  = new VLDOValidarAcao($obModel);
$obView->listAcao($rsAcao, $_REQUEST);

$rsAcao->addFormatacao('quantidade', 'NUMERIC_BR');
$rsAcao->addFormatacao('valor'     , 'NUMERIC_BR');

$stCaminho = CAM_GF_LDO_INSTANCIAS.'configuracao/FMValidarAcao.php?stAcao='.$_REQUEST['stAcao'];
$stAcao = $request->get('stAcao');
if ($_REQUEST['stAcao'] == 'excluir') {
    $stAcao = 'alterar';
}

$obLista = new Lista;
$obLista->setTitulo('Lista de Ações');
$obLista->setId('tblListaAcoes');
$obLista->setRecordSet($rsAcao);

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('&nbsp;');
$obLista->ultimoCabecalho->setWidth(5);
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Ação');
$obLista->ultimoCabecalho->setWidth(5);
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Nome');
$obLista->ultimoCabecalho->setWidth(55);
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Meta');
$obLista->ultimoCabecalho->setWidth(10);
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Valor');
$obLista->ultimoCabecalho->setWidth(10);
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Ação");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

//Dados
$obLista->addDado();
$obLista->ultimoDado->setAlinhamento('CENTRO');
$obLista->ultimoDado->setCampo('num_acao');
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento('ESQUERDA');
$obLista->ultimoDado->setCampo('titulo');
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento('DIREITA');
$obLista->ultimoDado->setCampo('quantidade');
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento('DIREITA');
$obLista->ultimoDado->setCampo('valor');
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao ($stAcao);
$obLista->ultimaAcao->setLink ($stCaminho);
$obLista->ultimaAcao->addCampo('&inCodAcao'    , 'cod_acao');
$obLista->ultimaAcao->addCampo('&inNumAcao'    , 'num_acao');
$obLista->ultimaAcao->addCampo('&inCodPPA'     , 'cod_ppa');
$obLista->ultimaAcao->addCampo('&inAno'        , 'ano');
$obLista->ultimaAcao->addCampo('&stDescQuestao', 'Ação - [cod_acao]');
$obLista->ultimaAcao->addCampo('&stExercicio'  , 'exercicio');
$obLista->commitAcao();

$obLista->show();

?>
