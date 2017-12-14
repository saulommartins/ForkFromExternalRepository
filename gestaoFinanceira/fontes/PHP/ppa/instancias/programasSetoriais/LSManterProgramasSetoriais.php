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
 * Listagem de Programa Setorial
 *
 * @category    Urbem
 * @package     PPA
 * @author      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
 * @author      Henrique Boaventura <henrique.boaventura@cnm.org.br>
 * $Id: $
 */

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_PPA_MAPEAMENTO . 'TPPAProgramaSetorial.class.php';

$stPrograma = 'ManterProgramasSetoriais';

$stAcao = $request->get('stAcao');

//seta os filtros
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

$obTPPAProgramaSetorial = new TPPAProgramaSetorial;

$stFiltro = ' WHERE ';
if ($_REQUEST['inCodPPA'] != '') {
    $stFiltro .= " ppa.cod_ppa = " . $_REQUEST['inCodPPA'] . "  AND ";
}
if ($_REQUEST['inCodMacroObjetivo'] != '') {
    $stFiltro .= " macro_objetivo.cod_macro = " . $_REQUEST['inCodMacroObjetivo'] . "  AND ";
}
if ($_REQUEST['inCodSetorial'] != '') {
    $stFiltro .= " programa_setorial.cod_setorial = " . $_REQUEST['inCodSetorial'] . "  AND ";
}
if ($_REQUEST['stHdnDescricao'] != '') {
    $stFiltro .= " programa_setorial.descricao ILIKE '" . $_REQUEST['stHdnDescricao'] . "'  AND ";
}
if ($_REQUEST['stAcao'] == 'excluir') {
    $stFiltro .=  "NOT EXISTS (SELECT 1
                                 FROM  ppa.programa
                                WHERE programa.cod_setorial = programa_setorial.cod_setorial)  AND ";
}

$stFiltro = substr($stFiltro,0,-6);
$stOrder  = ' ORDER by cod_setorial';

$obTPPAProgramaSetorial->listProgramaSetorial($rsProgramaSetorial, $stFiltro, $stOrder);

//instancia uma nova lista
$obLista = new Lista;

$stLink .= '&stAcao=' . $stAcao;

$obLista->obPaginacao->setFiltro('&stLink=' . $stLink );

$obLista->setRecordSet($rsProgramaSetorial);

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('&nbsp;');
$obLista->ultimoCabecalho->setWidth(5);
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('PPA');
$obLista->ultimoCabecalho->setWidth(15);
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Macro Objetivo');
$obLista->ultimoCabecalho->setWidth(30);
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Código');
$obLista->ultimoCabecalho->setWidth(10);
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Descrição');
$obLista->ultimoCabecalho->setWidth(20);
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Ação');
$obLista->ultimoCabecalho->setWidth(5);
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento('ESQUERDA');
$obLista->ultimoDado->setCampo('[ano_inicio] - [ano_final]');
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento('ESQUERDA');
$obLista->ultimoDado->setCampo('[cod_macro] - [nom_macro]');
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento('CENTRO');
$obLista->ultimoDado->setCampo('[cod_setorial]');
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento('ESQUERDA');
$obLista->ultimoDado->setCampo('[nom_setorial]');
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );

$obLista->ultimaAcao->addCampo('&inCodSetorial', 'cod_setorial');
$obLista->ultimaAcao->addCampo('&stDescQuestao', '[cod_setorial]');
$teste = new RecordSet;
if ($stAcao == 'alterar') {
    $obLista->ultimaAcao->setLink(CAM_GF_PPA_INSTANCIAS . 'programasSetoriais/FMManterProgramasSetoriais.php' . "?".Sessao::getId().$stLink."&pg=".$_REQUEST['pg']."&pos=".Sessao::read('pos') );
} else {
    $obLista->ultimaAcao->setLink(CAM_GF_PPA_INSTANCIAS . 'programasSetoriais/PRManterProgramasSetoriais.php' . '?' . Sessao::getId().$stLink );
}

$obLista->commitAcao();
$obLista->show();

?>
