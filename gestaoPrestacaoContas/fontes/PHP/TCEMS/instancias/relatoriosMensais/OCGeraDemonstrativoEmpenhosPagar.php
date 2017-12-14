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
 * Página de Oculto para gerar relatório birt de Empenhos à Ppagar
 *
 * @category   Urbem
 * @package    Framework
 * @author     Analista Tonismar R. Bernardo <tonismar.bernardo@cnm.org.br>
 * @author     Desenvolvedor Henrique Girardi dos Santos <henrique.santos@cnm.org.br>
 * $Id:$
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';

include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php" );
include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoUnidade.class.php" );

$obTUnidade = new TOrcamentoUnidade;
$obTUnidade->setDado('exercicio', Sessao::read('exercicio'));
$obTUnidade->setDado('num_orgao', $_REQUEST['inCodOrgao']);
$obTUnidade->setDado('num_unidade', $_REQUEST['inCodUnidade']);
$obTUnidade->recuperaOrgaoUnidadeOrcamentaria( $rsUnidade );

$preview = new PreviewBirt(6,57,7);
$preview->setTitulo('Demonstrativo Empenhos a Pagar');
$preview->setVersaoBirt('2.5.0');

$preview->addParametro('entidade'           , implode(',', $_REQUEST['inCodEntidade']));
$preview->addParametro('exercicio_empenho'  , $_REQUEST['inExercicio']);
$preview->addParametro('dt_inicial'         , $_REQUEST['stDataInicial']);
$preview->addParametro('dt_final'           , $_REQUEST['stDataFinal']);
$preview->addParametro('dt_situacao'        , $_REQUEST['stDataSituacao']);
$preview->addParametro('cod_empenho_inicial', $_REQUEST['inCodEmpenhoInicial']);
$preview->addParametro('cod_empenho_final'  , $_REQUEST['inCodEmpenhoFinal']);
$preview->addParametro('cgm'                , $_REQUEST['inCodFornecedor']);
$preview->addParametro( 'num_orgao'         , str_pad($_REQUEST['inCodOrgao'], 2, '0', STR_PAD_LEFT));
$preview->addParametro( 'num_unidade'       , str_pad($_REQUEST['inCodUnidade'], 3, '0', STR_PAD_LEFT));
$preview->addParametro( 'nom_orgao'         , $rsUnidade->getCampo('nom_orgao'));
$preview->addParametro( 'nom_unidade'       , $rsUnidade->getCampo('nom_unidade'));
$preview->addParametro('ordenacao'          , $_REQUEST['ordenacao']);

$stIncluirAssinaturas = $_REQUEST['stIncluirAssinaturas'];
if ($stIncluirAssinaturas == 'nao') {
    $stIncluirAssinaturas = 'não';
} else {
    $stIncluirAssinaturas = 'sim';
}

$preview->addParametro('incluir_assinaturas', $stIncluirAssinaturas);
$preview->addAssinaturas(Sessao::read('assinaturas'));

$preview->preview();

?>
