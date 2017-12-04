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
 * Arquivo que passa os filtros para o relatório do BIRT
 *
 * @category   Urbem
 * @package    Framework
 * @author     Analista Tonismar Bernardo <tonismar.bernardo@cnm.org.br>
 * @author     Desenvolvedor Eduardo Schitz <eduardo.schitz@cnm.org.br>
 * $Id:$
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
include_once CAM_GF_ORC_MAPEAMENTO.'TOrcamentoEntidade.class.php';

$preview = new PreviewBirt(2, 9, 2);
$preview->setTitulo('Balanço Financeiro');
$preview->setVersaoBirt('2.5.0');
$preview->setExportaExcel(true);

$stExercicio = Sessao::getExercicio();

$obTOrcamentoEntidade = new TOrcamentoEntidade();
$obTOrcamentoEntidade->setDado('exercicio', $stExercicio);
$obTOrcamentoEntidade->recuperaEntidades($rsEntidade, 'and e.cod_entidade in ('.implode(',',$_REQUEST['inCodEntidade']).')');

$obErro = new Erro();

$preview->addParametro('cod_entidade', implode(',', $_REQUEST['inCodEntidade']));

if (COUNT($_REQUEST['inCodEntidade']) == 1) {
    $preview->addParametro('nom_entidade', $rsEntidade->getCampo('nom_cgm'));
} else {
    $rsEntidade->setPrimeiroElemento();
    $preview->addParametro('nom_entidade', $rsEntidade->getCampo('nom_cgm'));
    while (!$rsEntidade->eof()) {
        if (preg_match('/prefeitura.*/', strtolower($rsEntidade->getCampo( 'nom_cgm' )))) {
            $preview->addParametro('nom_entidade', $rsEntidade->getCampo('nom_cgm'));
            break;
        }
        $rsEntidade->proximo();
    }
}

$stDataInicial = implode('-',array_reverse(explode('/', $_REQUEST['stDataInicial'])));
$stDataFinal = implode('-',array_reverse(explode('/', $_REQUEST['stDataFinal'])));

$preview->addParametro ( 'data_inicial_nota', $stDataInicial );
$preview->addParametro ( 'data_final_nota', $stDataFinal );

$preview->addParametro('data_ini', $_REQUEST['stDataInicial']);
$preview->addParametro('data_fim', $_REQUEST['stDataFinal']);
$preview->addParametro('demonstrar_despesa', $_REQUEST['stDemonstrarDespesa']);
if ($_REQUEST['stDemonstrarDespesa'] == 'E') {
    $preview->addParametro('nom_demonstrar_despesa', 'Empenhados');
} elseif ($_REQUEST['stDemonstrarDespesa'] == 'L') {
    $preview->addParametro('nom_demonstrar_despesa', 'Liquidados');
} elseif ($_REQUEST['stDemonstrarDespesa'] == 'P') {
    $preview->addParametro('nom_demonstrar_despesa', 'Pagos');
}

$preview->addParametro('tipo_relatorio', $_REQUEST['inCodTipoRelatorio']);
if ($_REQUEST['inCodTipoRelatorio'] == 1) {
    $preview->addParametro('nom_tipo_relatorio', 'Função');
} elseif ($_REQUEST['inCodTipoRelatorio'] == 2) {
    $preview->addParametro('nom_tipo_relatorio', 'Categoria Econômica');
}

$preview->addParametro('exercicio', $stExercicio);

$preview->preview();
