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
    * Página de Filtro para Relatório de Ïtens Perecíveis
    * Data de Criação   : 22/08/2007

    * @author Henrique Boaventura

    * @ignore

    * $Id: OCGeraMovimentacaoEstoquePerecivel.php 60887 2014-11-20 18:39:49Z franver $

    * Casos de uso : uc-03.03.25
*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';

$preview = new PreviewBirt(3,29,6);
$preview->setVersaoBirt('2.5.0');
$preview->setTitulo('Relatório do Birt');

$preview->setNomeArquivo('relatorioItem');

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

if ($_REQUEST['inCodItemInicial'] != "" && $_REQUEST['inCodItemFinal'] != "") {
    $preview->addParametro( "codItem", " and lancamento_material.cod_item between ".$_REQUEST['inCodItemInicial']." and ".$_REQUEST['inCodItemFinal'] );

} elseif ($_REQUEST['inCodItemInicial'] != "") {
    $preview->addParametro( "codItem"," and lancamento_material.cod_item = ".$_REQUEST['inCodItemInicial']);

} else {
    $preview->addParametro( "codItem", "" );
}

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

if (!($_REQUEST['stTipoBuscaCodItem']=="")) {
    $preview->addParametro( "filtro",$_REQUEST['stTipoBuscaCodItem'] );
}

if (($_REQUEST['stDataInicial']!="") && ($_REQUEST['stDataFinal']!="")) {
    $dtIni = $_REQUEST['stDataInicial'];
    $dtFim = $_REQUEST['stDataFinal'];
    $preview->addParametro( "periodicidade"," AND TO_DATE(natureza_lancamento.timestamp::VARCHAR, 'yyyy-mm-dd') BETWEEN to_date('".$dtIni."','dd/mm/yyyy') AND to_date('".$dtFim."','dd/mm/yyyy') " );
} else {
    $preview->addParametro( "periodicidade","" );
}
if (($_REQUEST['stDataInicialFabricacao']!="") && ($_REQUEST['stDataFinalFabricacao']!="")) {
    $dtIni = $_REQUEST['stDataInicialFabricacao'];
    $dtFim = $_REQUEST['stDataFinalFabricacao'];
    $preview->addParametro( "periodicidadeFabricacao"," AND perecivel.dt_fabricacao BETWEEN to_date('".$dtIni."','dd/mm/yyyy') AND to_date('".$dtFim."','dd/mm/yyyy') " );
} else {
    $preview->addParametro( "periodicidadeFabricacao","" );
}
if (($_REQUEST['stDataInicialValidade']!="") && ($_REQUEST['stDataFinalValidade']!="")) {
    $dtIni = $_REQUEST['stDataInicialValidade'];
    $dtFim = $_REQUEST['stDataFinalValidade'];
    $preview->addParametro( "periodicidadeValidade"," AND perecivel.dt_validade BETWEEN to_date('".$dtIni."','dd/mm/yyyy') AND to_date('".$dtFim."','dd/mm/yyyy') " );
} else {
    $preview->addParametro( "periodicidadeValidade","" );
}
if ($_REQUEST['stDataSaldo'] != '') {
    $preview->addParametro( 'data_saldo', $_REQUEST['stDataSaldo'] );
} else {
    $preview->addParametro( 'data_saldo', '' );
}

if (!($_REQUEST['stOrdem']=="")) {
  $preview->addParametro( "tipo_ordenacao",$_REQUEST['stOrdem'] );
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

$preview->addParametro( 'cgm_usuario',Sessao::read('numCgm') );

$preview->preview();
