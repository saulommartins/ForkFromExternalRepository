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
    * Nova lista para inclusão de despesa, agora utilizando ação
    * Data de Criação   : 12/08/2009

    * @author Analista      Tonismar Régis Bernardo
    * @author Desenvolvedor Eduardo Paculski Schitz

    * @package URBEM
    * @subpackage

    * @ignore # só use se for paginas que o cliente visualiza, se for mapeamento ou classe de negocio não se usa

    $Id: $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_LDO_MAPEAMENTO.'TLDO.class.php';
include_once CAM_GF_LDO_NEGOCIO.'RLDOValidarAcao.class.php';
include_once CAM_GF_LDO_VISAO.'VLDOValidarAcao.class.php';

//Define o nome dos arquivos PHP
$stPrograma = 'DespesaAcao';
$pgFilt = 'FL'.$stPrograma.'.php';
$pgList = 'LS'.$stPrograma.'.php';
$pgForm = 'FM'.$stPrograma.'.php';
$pgProc = 'PR'.$stPrograma.'.php';
$pgOcul = 'OC'.$stPrograma.'.php';
$pgJs   = 'JS'.$stPrograma.'.js';

$stCaminho   = CAM_GF_ORC_INSTANCIAS.'elaboracaoOrcamento/';

$stLink = isset($stLink) ? $stLink : null;

$arFiltro = Sessao::read('filtro');
if (!Sessao::read('paginando')) {
    foreach ($_POST as $stCampo => $stValor) {
        $arFiltro['filtro'][$stCampo] = $stValor;
    }
    $inPg = $request->get('pg');
    $inPos =  $request->get('pos');
    $boPaginando = true;

    Sessao::write('filtro',$arFiltro);
    Sessao::write('pg',$inPg);
    Sessao::write('pos',$inPos);
    Sessao::write('paginando',$boPaginando);
} else {
    $inPg = $request->get('pg');
    $inPos = $request->get('pos');
    foreach ($arFiltro['filtro'] AS $stKey=>$stValue) {
        $_REQUEST[$stKey] = $stValue;
    }
}

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_REQUEST['stAcao'];

if (empty($stAcao)) {
    $stAcao = 'alterar';
}

$obTLDO = new TLDO();
$obTLDO->setDado('exercicio', Sessao::getExercicio());
$obTLDO->setDado('homologado', true);
$obTLDO->recuperaExerciciosLDO($rsExerciciosLDO);

$rsLista = new RecordSet;

if ($rsExerciciosLDO->getNumLinhas() > -1) {
    $arParametros['inCodPPA']        = $rsExerciciosLDO->getCampo('cod_ppa');
    $arParametros['slExercicioLDO']  = $rsExerciciosLDO->getCampo('ano');
    $arParametros['stTitulo']        = $_REQUEST['stNomAcao'];
    $arParametros['inCodRecurso']    = $_REQUEST['inCodRecurso'];
    $arParametros['inNumAcaoInicio'] = $_REQUEST['inNumAcaoInicio'];
    $arParametros['inNumAcaoFim']    = $_REQUEST['inNumAcaoFim'];
    $arParametros['stAcao']          = 'incluirAcao';

    $obModel = new RLDOValidarAcao();
    $obView  = new VLDOValidarAcao($obModel);
    $stOrder = ' ORDER BY acao.num_acao';
    $obView->listAcaoDespesa($rsLista, $arParametros, $stOrder);
}

$obLista = new Lista;
$obLista->obPaginacao->setFiltro('&stLink='.$stLink );

$obLista->setRecordSet($rsLista);
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
$obLista->ultimoCabecalho->setWidth(80);
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('&nbsp;');
$obLista->ultimoCabecalho->setWidth(5);
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo('num_acao');
$obLista->ultimoDado->setAlinhamento('CENTRO');
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo('titulo');
$obLista->ultimoDado->setAlinhamento('ESQUERDA');
$obLista->commitDado();
$obLista->addAcao();

$obLista->ultimaAcao->setAcao($stAcao);
$obLista->ultimaAcao->addCampo('&inCodAcao', 'cod_acao');
$obLista->ultimaAcao->addCampo('&inNumAcao', 'num_acao');
$obLista->ultimaAcao->addCampo('&inAno'    , 'ano');
$obLista->ultimaAcao->addCampo('&inCodPPA' , 'cod_ppa');
$obLista->ultimaAcao->setLink($pgForm.'?stAcao='.$stAcao.'&inCodRecurso='.$_REQUEST['inCodRecurso'].'&'.Sessao::getId().$stLink);
$obLista->commitAcao();
$obLista->show();
?>
