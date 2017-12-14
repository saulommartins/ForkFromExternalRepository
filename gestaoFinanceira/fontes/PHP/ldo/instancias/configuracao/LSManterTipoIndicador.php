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
 * Página de listagem do Tipo de Indicador
 * Data de Criação: 09/01/2009
 * Copyright CNM - Confederação Nacional de Municípios
 *
 * @author Analista     : Tonismar Regis Bernardo     <tonismar.bernardo@cnm.org.br>
 * @author Desenvolvedor: Henrique Girardi dos Santos <henrique.santos@cnm.org.br>
 * @package gestaoFinanceira
 * @subpackage ldo
 * @uc uc-02.10.02
 */

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once CAM_GF_LDO_VISAO.'VLDOManterTipoIndicador.class.php';

//Define o nome dos arquivos PHP
$stModulo = 'ManterTipoIndicador';
$pgForm   = 'FM'.$stModulo.'.php';
$pgProc   = 'PR'.$stModulo.'.php';
$pgJS     = 'JS'.$stModulo.'.php';
$pgOcul   = 'OC'.$stModulo.'.php';
$pgList   = 'LS'.$stModulo.'.php';

$arFiltro = Sessao::read('arFiltro');
if ($arFiltro['paginando']) {
    $_REQUEST = $arFiltro;
} else {
    $arFiltro = $_REQUEST;
    $arFiltro['paginando'] = true;
    $arFiltro['pg']        = $_GET['pg'];
    $arFiltro['pos']       = $_GET['pos'];
}
Sessao::write('arFiltro', $arFiltro);

$stAcao = $request->get('stAcao');
$pgAcao = $pgForm;
if ($stAcao == 'excluir') {
    $pgAcao = CAM_GF_LDO_INSTANCIAS.'configuracao/'.$pgProc;
}

list($inCodUnidade, $inCodGrandeza) = explode('-', $_REQUEST['inCodUnidadeMedida']);
$arFiltro = array(
        'stDescricao'   => $_REQUEST['stHdnDescricao']
    ,   'inCodGrandeza' => $inCodGrandeza
    ,   'inCodUnidade'  => $inCodUnidade
);

$rsTipoIndicador = new RecordSet;
$rsTipoIndicador = VLDOManterTipoIndicador::recuperarInstancia()->recuperarRegra()->retornaDadosTipoIndicador($arFiltro);

$obLista = new Lista;
$obLista->setMostraPaginacao(true);
$obLista->setTitulo('Lista');

$obLista->setRecordSet($rsTipoIndicador);

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth(5);
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Código');
$obLista->ultimoCabecalho->setWidth(7);
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Descrição');
$obLista->ultimoCabecalho->setWidth(40);
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Unidade Medida');
$obLista->ultimoCabecalho->setWidth(40);
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth(7);
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento('CENTRO');
$obLista->ultimoDado->setCampo      ('cod_tipo_indicador');
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento('ESQUERDA');
$obLista->ultimoDado->setCampo      ('descricao');
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento('ESQUERDA');
$obLista->ultimoDado->setCampo      ('nom_unidade');
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao ($stAcao);
$obLista->ultimaAcao->addCampo('inCodTipoIndicador', 'cod_tipo_indicador');
$obLista->ultimaAcao->addCampo('inCodGrandeza'     , 'cod_grandeza');
$obLista->ultimaAcao->addCampo('inCodUnidade'      , 'cod_unidade');
if ($stAcao == 'excluir') {
    $obLista->ultimaAcao->addCampo('stDescQuestao', 'cod_tipo_indicador');
}
$obLista->ultimaAcao->setLink ($pgAcao.'?stAcao='.$stAcao.'&');
$obLista->commitAcao();

$obLista->show();
