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

/*
* Script de função PLPGSQL
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Id: OCGeraRelacaoEmpenho.php 64670 2016-03-18 20:25:16Z jean $
*
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php" );

if (Sessao::getExercicio() > '2015') {
    $preview = new PreviewBirt(2, 10, 12);
} else {
    $preview = new PreviewBirt(2, 10, 1);
}

$preview->setTitulo('Relação de Empenhos');
$preview->setVersaoBirt( '2.5.0' );
$preview->setExportaExcel (true );

$obTOrcamentoEntidade = new TOrcamentoEntidade();
$obTOrcamentoEntidade->setDado( 'exercicio'   , Sessao::getExercicio() );
$obTOrcamentoEntidade->recuperaEntidades( $rsEntidade, "and e.cod_entidade in (".implode(',',$_REQUEST['inCodEntidade']).")" );

$preview->addParametro( 'entidade', implode(',', $_REQUEST['inCodEntidade'] ) );
if ( count($_REQUEST['inCodEntidade']) == 1 ) {
    $preview->addParametro( 'nom_entidade', $rsEntidade->getCampo('nom_cgm') );
} else {
    while ( !$rsEntidade->eof() ) {
        if ( preg_match( "/prefeitura.*/i", $rsEntidade->getCampo('nom_cgm')) ) {
            $preview->addParametro( 'nom_entidade', $rsEntidade->getCampo('nom_cgm') );
            break;
        }
        $rsEntidade->proximo();
    }
}

$preview->addParametro( 'data_inicial', $_REQUEST['stDataInicial'] );
$preview->addParametro( 'data_final', $_REQUEST['stDataFinal'] );
$preview->addParametro( 'cod_entidade', implode(',',$_REQUEST['inCodEntidade']) );

if ($_REQUEST['inCodOrgao'] != '') {
    $preview->addParametro( 'cod_orgao', $_REQUEST['inCodOrgao'] );
} else {
    $preview->addParametro( 'cod_orgao', '' );
}

if ($_REQUEST['inCodUnidade'] != '') {
    $preview->addParametro( 'cod_unidade', $_REQUEST['inCodUnidade'] );
} else {
    $preview->addParametro( 'cod_unidade', '' );
}

if ($_REQUEST['inCodPao'] != '') {
    $preview->addParametro( 'cod_pao', $_REQUEST['inCodPao'] );
} else {
    $preview->addParametro( 'cod_pao', '' );
}

if ($_REQUEST['inCodRecurso'] != '') {
    $preview->addParametro( 'cod_recurso', $_REQUEST['inCodRecurso'] );
} else {
    $preview->addParametro( 'cod_recurso', '' );
}

if ($_REQUEST['inCodDespesa'] != '') {
    $preview->addParametro( 'cod_elemento_despesa', $_REQUEST['inCodDespesa'] );
} else {
    $preview->addParametro( 'cod_elemento_despesa', '' );
}

if ($_REQUEST['inCodDestinacaoRecurso'] != '') {
    $preview->addParametro( 'destinacao_recurso', $_REQUEST['inCodDestinacao'] );
} else {
    $preview->addParametro( 'destinacao_recurso', '' );
}

if ($_REQUEST['inCodDetalhamento'] != '') {
    $preview->addParametro( 'cod_detalhamento', $_REQUEST['inCodDetalhamento'] );
} else {
    $preview->addParametro( 'cod_detalhamento', '' );
}

if ($_REQUEST['inCodDespesa'] != '') {
    $preview->addParametro( 'cod_elemento_despesa_masc', $_REQUEST['stMascClassificacao'] );
} else {
    $preview->addParametro( 'cod_elemento_despesa_masc', '' );
}

if ($_REQUEST['inCodHistorico'] != '') {
    $preview->addParametro( 'cod_historico', $_REQUEST['inCodHistorico'] );
} else {
    $preview->addParametro( 'cod_historico', '' );
}

if ($_REQUEST['stOrdenacao'] != '') {
    $preview->addParametro( 'ordenacao', $_REQUEST['stOrdenacao'] );
} else {
    $preview->addParametro( 'ordenacao', '' );
}

if ($_REQUEST['inCodFuncao'] != '') {
    $preview->addParametro( 'cod_funcao', $_REQUEST['inCodFuncao'] );
} else {
    $preview->addParametro( 'cod_funcao', '' );
}

if ($_REQUEST['inCodSubFuncao'] != '') {
    $preview->addParametro( 'cod_subfuncao', $_REQUEST['inCodSubFuncao'] );
} else {
    $preview->addParametro( 'cod_subfuncao', '' );
}

if ($_REQUEST['inCodPrograma'] != '') {
    $preview->addParametro( 'cod_programa', $_REQUEST['inCodPrograma'] );
} else {
    $preview->addParametro( 'cod_programa', '' );
}

if ($_REQUEST['inCodDotacao'] != '') {
    $preview->addParametro( 'cod_dotacao', $_REQUEST['inCodDotacao'] );
} else {
    $preview->addParametro( 'cod_dotacao', '' );
}

if ($_REQUEST['inCodTipo'] != '') {
    $preview->addParametro( 'cod_tipo', $_REQUEST['inCodTipo'] );
} else {
    $preview->addParametro( 'cod_tipo', '' );
}

if ($_REQUEST['inCodCategoria'] != '') {
    $preview->addParametro( 'cod_categoria', $_REQUEST['inCodCategoria'] );
} else {
    $preview->addParametro( 'cod_categoria', '' );
}

if ($_REQUEST['inCodFornecedor'] != '') {
    $preview->addParametro( 'cod_fornecedor', $_REQUEST['inCodFornecedor'] );
} else {
    $preview->addParametro( 'cod_fornecedor', '' );
}

if ($_REQUEST['inCentroCusto'] != '') {
    $preview->addParametro( 'centro_custo', $_REQUEST['inCentroCusto'] );
} else {
    $preview->addParametro( 'centro_custo', '' );
}

$preview->preview();
