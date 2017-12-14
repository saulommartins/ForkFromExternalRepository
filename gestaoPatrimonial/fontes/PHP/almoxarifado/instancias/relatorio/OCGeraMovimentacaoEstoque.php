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

    * Página de Filtro para Relatório de Ïtens
    * Data de Criação   : 24/01/2006

    * @author Gelson W. Gonçalves

    * @ignore

    * $Id: OCGeraMovimentacaoEstoque.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso : uc-03.03.24
*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';

$preview = new PreviewBirt(3,29,5);

// Nova versão
$preview->setVersaoBirt( '2.5.0' );

$preview->setTitulo('Relatório do Birt');

$preview->setNomeArquivo('movimentacaoEstoque');

if (count($_REQUEST['inCodAlmoxarifadoSelecionado'])>0) {
    foreach ($_REQUEST['inCodAlmoxarifadoSelecionado'] as $array) {
        $arrAlmoxarifado.=  $array.",";
    }
    $arrAlmoxarifado=substr($arrAlmoxarifado,0,strlen($arrAlmoxarifado)-1);
    $preview->addParametro( "codAlmoxarifado",$arrAlmoxarifado );
} else {
    $preview->addParametro( "codAlmoxarifado","" );
}

if ($_REQUEST['inCodCatalogo'] != '') {
    $preview->addParametro( "codCatalogo",$_REQUEST['inCodCatalogo'] );
} else {
    $preview->addParametro( "codCatalogo","");
}

if ($_REQUEST['stChaveClassificacao']!= "") {
    $preview->addParametro( "codClassificacao",$_REQUEST['stChaveClassificacao'] );
} else {
    $preview->addParametro( 'codClassificacao', '');
}

if (count($_REQUEST['inCodCentroCustoRelacionado'])>0) {
    foreach ($_REQUEST['inCodCentroCustoRelacionado'] as $array) {
        $arrCentroCusto.=  $array.",";
    }
    $arrCentroCusto=substr($arrCentroCusto,0,strlen($arrCentroCusto)-1);
    $preview->addParametro( "codCentroCusto",$arrCentroCusto );
} else {
    $preview->addParametro( "codCentroCusto","" );
}

//fornecedor
if ($_REQUEST['inCGM']!= "") {
    $preview->addParametro( "inCGM",$_REQUEST['inCGM'] );
} else {
    $preview->addParametro( 'inCGM', '');
}

if ($_REQUEST['stNomCGM']!= "") {
    $preview->addParametro( "stNomCGM",$_REQUEST['stNomCGM'] );
} else {
    $preview->addParametro( 'stNomCGM', '');
}

//nota fiscal
if ($_REQUEST['inNF'] != "") {
    $preview->addParametro( "notaFiscal",(int) $_REQUEST['inNF']);
}

if ($_REQUEST['stSerieNF'] != "") {
    $preview->addParametro( "serieNF",$_REQUEST['stSerieNF']);
}
$preview->addParametro( "inCodItemInicial",$_REQUEST['inCodItemInicial']);
$preview->addParametro( "inCodItemFinal",$_REQUEST['inCodItemFinal']);



if (!($_REQUEST['stDescItem']=="")) {
    if ($_REQUEST['stTipoBuscaDescItem']=='contem') {
        $preview->addParametro( "descricaoItem"," ilike '%".$_REQUEST['stDescItem']."%' " );

    } elseif ($_REQUEST['stTipoBuscaDescItem']=='inicio') {
        $preview->addParametro( "descricaoItem"," ilike '".$_REQUEST['stDescItem']."%' " );

    } elseif ($_REQUEST['stTipoBuscaDescItem']=='final') {
        $preview->addParametro( "descricaoItem"," ilike '%".$_REQUEST['stDescItem']."' " );

    } else {
        $preview->addParametro( "descricaoItem"," = '".$_REQUEST['stDescItem']."' " );
    }

} else {
    $preview->addParametro( "descricaoItem",""                     );
}

if ($_REQUEST['inCodTipo'] != "0") {
    $preview->addParametro( "tipoItem",$_REQUEST['inCodTipo'] );
} else {
    $preview->addParametro( "tipoItem",""                     );
}

if (!($_REQUEST['stTipoBuscaCodItem']=="")) {
    $preview->addParametro( "filtro",$_REQUEST['stTipoBuscaCodItem'] );
}

if (($_REQUEST['stDataInicial']!="") && ($_REQUEST['stDataFinal']!="")) {
    $preview->addParametro( "dtIni",$_REQUEST['stDataInicial']);
    $preview->addParametro( "dtFim",$_REQUEST['stDataFinal'] );
    //$preview->addParametro( "periodicidade"," AND natureza_lancamento.timestamp::date BETWEEN to_date('".$dtIni."','dd/mm/yyyy') AND to_date('".$dtFim."','dd/mm/yyyy') " );
}

//if ($_REQUEST['stDataSaldo'] != '') {
    $preview->addParametro( 'data_saldo', $_REQUEST['stDataFinal'] );
//} else {
   // $preview->addParametro( 'data_saldo', '' );
//}

if (!empty($_REQUEST['stOrdem'])) {
    switch ($_REQUEST['stOrdem']) {
        case "classificacao" : $preview->addParametro( "tipo_ordenacao", "catalogo_classificacao.cod_estrutural" ); break;
        case "item"          : $preview->addParametro( "tipo_ordenacao", "catalogo_item.cod_item" );                break;
        case "descricao"     : $preview->addParametro( "tipo_ordenacao", "catalogo_item.descricao_resumida" ); break;
    }
}

if ($_REQUEST['stGrupoAlmoxarifado'] != '') {
    $arGrupo[] = $_REQUEST['stGrupoAlmoxarifado'];
}

if ($_REQUEST['stGrupoMarca'] != '') {
    $arGrupo[] = $_REQUEST['stGrupoMarca'];
}

if ($_REQUEST['stGrupoCentroCusto'] != '') {
    $arGrupo[] = $_REQUEST['stGrupoCentroCusto'];
}

if ( count( $arGrupo ) > 0 ) {
    $preview->addParametro( 'tipo_agrupamento', implode(',',$arGrupo) );
} else {
    $preview->addParametro( 'tipo_agrupamento','' );
}

if ($_REQUEST['stTipoRelatorio'] != '') {
    $preview->addParametro( 'tipo_relatorio', $_REQUEST['stTipoRelatorio'] );
}

$preview->addParametro( 'unidade_medida', $_REQUEST['inUnidadeAbrev'] );

$preview->addParametro( 'natureza', $_REQUEST['inNaturezaAbrev'] );

$stFiltroNatureza = "";
if (isset($_REQUEST['inCodNaturezaRelacionados'])) {
    foreach ($_REQUEST['inCodNaturezaRelacionados'] AS $inCodNatureza) {
         $arNatureza = explode("-",$inCodNatureza);
         $stFiltroNatureza .= " OR (natureza_lancamento.cod_natureza=".$arNatureza[0]." AND natureza_lancamento.tipo_natureza='".$arNatureza[1]."') ";
    }
    $stFiltroNatureza = substr($stFiltroNatureza,3);
}

//if ($_REQUEST['inCodNatureza'] != '') {
//	$preview->addParametro( 'inCodNatureza', $_REQUEST['inCodNatureza'] );
//} else {
//	$preview->addParametro( 'inCodNatureza', '' );
//}
$preview->addParametro( 'inCodNatureza', $stFiltroNatureza );

$preview->addParametro( 'cgm_usuario',Sessao::read('numCgm') );

$preview->preview();
