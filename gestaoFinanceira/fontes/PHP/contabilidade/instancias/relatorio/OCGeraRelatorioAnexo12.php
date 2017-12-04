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
    * Página de Relatório Despesas SIOPS
    * Data de Criação  : 12/06/2008

    * $Id: OCGeraRelatorioAnexo12.php 59612 2014-09-02 12:00:51Z gelson $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php"                                    );
require_once CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php";

$preview = new PreviewBirt(2,9,1);
$preview->setTitulo('Anexo 12 - Balanço Orçamentário');
$preview->setVersaoBirt( '2.5.0' );
$preview->setExportaExcel(true);

$obTOrcamentoEntidade = new TOrcamentoEntidade();
$obTOrcamentoEntidade->setDado( 'exercicio'   , Sessao::getExercicio() );

if (count($_REQUEST['inCodEntidade']) == 1) {
    $inCodEntidade = $_REQUEST['inCodEntidade'][0];
    $obTOrcamentoEntidade->recuperaEntidades( $rsEntidade, "and e.cod_entidade in (".$inCodEntidade.")" );

    if ( preg_match( "/câmara.*/i", $rsEntidade->getCampo( 'nom_cgm' ) ) || preg_match( "/camara.*/i", $rsEntidade->getCampo( 'nom_cgm' ) ) ) {
        $preview->addParametro( 'poder' , 'Poder Legislativo' );
    } else {
        $preview->addParametro( 'poder' , 'Poder Executivo' );
    }

    $inValor = SistemaLegado::pegaDado('valor', 'administracao.configuracao', "where exercicio='".Sessao::getExercicio()."' and parametro='cod_entidade_rpps' and cod_modulo = 8");
    if ($inCodEntidade == $inValor) {
        $preview->addParametro ( 'rpps', 'T' );
    } else {
        $preview->addParametro ( 'rpps', 'F' );
    }
} else {

    $inCodEntidade = SistemaLegado::pegaDado('valor', 'administracao.configuracao', "where exercicio='".Sessao::getExercicio()."' and parametro='cod_entidade_prefeitura' and cod_modulo = 8");
    $obTOrcamentoEntidade->recuperaEntidades( $rsEntidade, "and e.cod_entidade in (".$inCodEntidade.")" );
    $preview->addParametro( 'poder' , 'Poder Executivo' );
    $preview->addParametro ( 'rpps', 'F' );
}

$stDataInicial = implode('-',array_reverse(explode('/', $_REQUEST['stDataInicial'])));
$stDataFinal = implode('-',array_reverse(explode('/', $_REQUEST['stDataFinal'])));

$preview->addParametro ( 'data_inicial_nota', $stDataInicial );
$preview->addParametro ( 'data_final_nota', $stDataFinal );

$preview->addParametro ( 'cod_acao', Sessao::read('acao') );
$preview->addParametro ( 'exercicio', Sessao::getExercicio() );
$preview->addParametro ( 'cod_entidade', implode(',', $_REQUEST['inCodEntidade']) );
$preview->addParametro ( 'data_inicial', $_REQUEST['stDataInicial'] );
$preview->addParametro ( 'data_final', $_REQUEST['stDataFinal'] );
$preview->addParametro ( 'tipo_relatorio', $_REQUEST['stTipoRelatorio'] );
$preview->addParametro ( 'nome_entidade', $rsEntidade->getCampo('nom_cgm') );
$preview->preview();
