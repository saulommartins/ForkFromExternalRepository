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
    * Página de Geração do Relatório Razão do Credor
    * Data de Criação   : 08/04/2009

    * @author Analista: Tonismar Regis Bernardo
    * @author Desenvolvedor: Eduardo Pacuslki Schitz

    * @package URBEM
    * @subpackage

    * $Id: OCGeraRelatorioRazaoCredor.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
include '../../../../../../gestaoAdministrativa/fontes/PHP/administracao/classes/mapeamento/TAdministracaoConfiguracao.class.php';
include '../../../../../../gestaoRH/fontes/PHP/entidade/classes/mapeamento/TEntidade.class.php';
include '../../../../../../gestaoFinanceira/fontes/PHP/contabilidade/classes/mapeamento/TContabilidadePlanoConta.class.php';

$preview = new PreviewBirt( 2, 9, 6 );
$preview->setVersaoBirt( '2.5.0' );
$preview->setExportaExcel(true);

$arEntidadesAux = array();
$arEntidades = Sessao::read('filtroNomRelatorio');
$arEntidades = $arEntidades['entidade'];
foreach ($arEntidades as $inChave => $stEntidade) {
    if (array_search($inChave, $_REQUEST['inCodEntidade']) !== false) {
        $arEntidadesAux[] = utf8_decode($stEntidade);
    }
}

$stNomeEntidade = implode("<br/>", $arEntidadesAux);
$preview->addParametro('nome_entidade', $stNomeEntidade);

$preview->addParametro('exercicio',  Sessao::read('exercicio'));
$preview->addParametro('dt_inicial'  , $_REQUEST['stDataInicial']);
$preview->addParametro('dt_final'    , $_REQUEST['stDataFinal']);
$stEntidades = implode(',', $_REQUEST['inCodEntidade']);
$preview->addParametro('cod_entidade'  , $stEntidades);
$preview->addParametro('cgm_credor'  , $_REQUEST['inCGM'] );
$preview->addParametro('nom_credor'  , utf8_decode($_REQUEST['stNomFornecedor']));

if ($_REQUEST['inCodEmpenho'] != '') {
    $preview->addParametro('cod_empenho', $_REQUEST['inCodEmpenho']);
}

if ($_REQUEST['inNumOrgao'] != '') {
    $preview->addParametro('cod_orgao', $_REQUEST['inNumOrgao']);
}

if ($_REQUEST['inNumUnidade'] != '') {
    $preview->addParametro('cod_unidade', $_REQUEST['inNumUnidade']);
}

if ($_REQUEST['inCodRecurso'] != '') {
    $preview->addParametro('cod_recurso', $_REQUEST['inCodRecurso']);
}

if ($_REQUEST['inCodUso'] && $_REQUEST['inCodDestinacao'] && $_REQUEST['inCodEspecificacao']) {
    $stDestinacao = $_REQUEST['inCodUso'].".".$_REQUEST['inCodDestinacao'].".".$_REQUEST['inCodEspecificacao'];
    $preview->addParametro('destinacao_recurso', $stDestinacao);
}

if ($_REQUEST['inCodDespesa'] != '') {
    $preview->addParametro('cod_despesa', $_REQUEST['inCodDespesa']);
}

$preview->addParametro('demonstrar_restos', $_REQUEST['boDemoRestos']);
$preview->addParametro('demonstrar_liquidacao', $_REQUEST['boDemoLiquidacao']);

// Exibição do Relatorio Birt
$preview->preview();

?>
