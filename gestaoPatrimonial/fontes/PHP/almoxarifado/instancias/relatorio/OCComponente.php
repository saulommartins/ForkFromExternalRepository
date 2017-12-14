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
    * Página de Filtro para Relatório de Ïtens
    * Data de Criação   : 24/01/2006

    * @author Tonismar Régis Bernardo

    * @ignore

    * $Id: OCComponente.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso : uc-03.03.20
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';

$preview = new PreviewBirt(3,29,1);
$preview->setVersaoBirt('2.5.0');
$preview->setTitulo('Relatório do Birt');

$preview->setNomeArquivo('itensEstoque');

if (count($_REQUEST['inCodAlmoxarifadoSelecionado']) > 0) {
    foreach ($_REQUEST['inCodAlmoxarifadoSelecionado'] as $array) {
        $arrAlmoxarifado .=  $array.",";
    }

    $arrAlmoxarifado = substr($arrAlmoxarifado, 0, strlen($arrAlmoxarifado)-1);
    $preview->addParametro( "codAlmoxarifado",$arrAlmoxarifado );
} else {
    $preview->addParametro( "codAlmoxarifado","" );
}

if (count($_REQUEST['inCodCentroCustoRelacionado']) > 0) {
    foreach ($_REQUEST['inCodCentroCustoRelacionado'] as $array) {
        $arrCentroCusto .=  $array.",";
    }

    $arrCentroCusto = substr($arrCentroCusto, 0, strlen($arrCentroCusto)-1);
    $preview->addParametro( "codCentroCusto",$arrCentroCusto );
} else {
    $preview->addParametro( "codCentroCusto","" );
}

$preview->addParametro( "codCatalogo",$_REQUEST['inCodCatalogo'] );

if (!empty($_REQUEST['stChaveClassificacao']))
    $preview->addParametro( "codClassificacao",$_REQUEST['stChaveClassificacao'] );
else
    $preview->addParametro( "codClassificacao", "" );

if ($_REQUEST['inCodItemInicial'] != "")
    if ($_REQUEST['inCodItemFinal'] != "") {
        $preview->addParametro( "codItemInicial", $_REQUEST['inCodItemInicial'] );
        $preview->addParametro( "codItemFinal"  , $_REQUEST['inCodItemFinal']   );
    } else
        $preview->addParametro( "codItemInicial", $_REQUEST['inCodItemInicial'] );
else{
    $preview->addParametro( "codItemInicial" , "" );
    $preview->addParametro( "codItemFinal"   , "" );
}

// Seta o parâmetro para filtrar por descrição do item.
if (!empty($_REQUEST['inDescItem'])) {
    $preview->addParametro( "descricaoItem", $_REQUEST['inDescItem'] );
} else {
    $preview->addParametro( "descricaoItem","" );
}

// Seta o parâmetro para exibir itens com saldo, sem saldo ou todos.
if ($_REQUEST['stItensSaldo'] == "todos") {
    $preview->addParametro( "exibirItensComSaldo", "todos" );
} elseif ($_REQUEST['stItensSaldo'] == "sim") {
    $preview->addParametro( "exibirItensComSaldo", "sim" );
} else
    $preview->addParametro( "exibirItensComSaldo", "nao" );

if (!($_REQUEST['inCodTipo']=="0")) {
    $preview->addParametro( "tipoItem",$_REQUEST['inCodTipo'] );
} else {
    $preview->addParametro( "tipoItem","" );
}

if (!empty($_REQUEST['stTipoBuscaDescItem']))
    $preview->addParametro( "stTipoBuscaDescItem", $_REQUEST['stTipoBuscaDescItem'] );
else
    $preview->addParametro( "stTipoBuscaDescItem", "" );

if ($_REQUEST['inCodNatureza'] != "") {
    $preview->addParametro( "inCodNatureza", $_REQUEST['inCodNatureza'] );
} else {
    $preview->addParametro( 'inCodNatureza', '' );
}

if (!empty($_REQUEST['stDataSituacao'])) {
    $dtFim = $_REQUEST['stDataSituacao'];
    $preview->addParametro("periodicidade", $dtFim);
}

if (!($_REQUEST['stOrdem']=="")) {
    switch ($_REQUEST['stOrdem']) {
        case 'classificacao':
            $ordem = 'catalogo_classificacao.cod_classificacao';
        break;

        case 'item':
            $ordem = 'catalogo_item.cod_item';
        break;

        case 'descricao':
            $ordem = 'catalogo_item.descricao';
        break;
    }
  $preview->addParametro("ordenacao", $ordem);
}

if (!empty($_REQUEST['stTipoQuebra'])) {
    $preview->addParametro("stTipoQuebra", $_REQUEST['stTipoQuebra']);
} else {
    $preview->addParametro("stTipoQuebra", 'centro_custo');
}

$preview->preview();
