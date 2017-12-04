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

    * @author Henrique Boaventura

    * @ignore

    * Casos de uso : uc-03.03.21

    $Id: OCRelatorioItensCatalogo.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';

$preview = new PreviewBirt(3,29,3);
$preview->setVersaoBirt('2.5.0');
$preview->setTitulo('Relatório de Itens do Catálogo');
$preview->setNomeArquivo('itensCatalogo');

if (!empty($_REQUEST['inCodCatalogo'])) {
    $preview->addParametro( "codCatalogo",$_REQUEST['inCodCatalogo'] );
} else {
    $preview->addParametro( "codCatalogo", "" );
}

if (!empty($_REQUEST['stChaveClassificacao'])) {
    $preview->addParametro( "codClassificacao",$_REQUEST['stChaveClassificacao'] );
}

if (!empty($_REQUEST['inCodItem'])) {
    $preview->addParametro( "codigoItem",$_REQUEST['inCodItem'] );
} else {
    $preview->addParametro( "codigoItem",""                     );
}

if (!empty($_REQUEST['inDescItem'])) {
    $preview->addParametro( "descricaoItem", $_REQUEST['inDescItem'] );
} else {
    $preview->addParametro( "descricaoItem", "" );
}

if (!empty($_REQUEST['stTipoBuscaDescItem'])) {
    $preview->addParametro( "tipo_busca",$_REQUEST['stTipoBuscaDescItem'] );
}

if (!($_REQUEST['inCodTipo']=="0")) {
    $preview->addParametro( "tipoItem",$_REQUEST['inCodTipo'] );
} else {
    $preview->addParametro( "tipoItem",""                     );
}

if (!empty($_REQUEST['stTipoBuscaCodItem'])) {
    $preview->addParametro( "filtro",$_REQUEST['stTipoBuscaCodItem'] );
}

if (!empty($_REQUEST['stMovimentacao'])) {
    $preview->addParametro( "stMovimentacao",$_REQUEST['stMovimentacao'] );
}

if (!empty($_REQUEST['stDataInicial']) && !empty($_REQUEST['stDataFinal'])) {
    $dtIni = $_REQUEST['stDataInicial'];
    $dtFim = $_REQUEST['stDataFinal'];
    $preview->addParametro( "periodicidade"," natureza_lancamento.timestamp BETWEEN '".$dtIni."' AND '".$dtFim."' " );
}

if (!empty($_REQUEST['stOrdem'])) {
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
  $preview->addParametro( "ordenacao"," ORDER BY ".$ordem." ASC;" );
}

/* solução para o bug #9605
 * consistência de filtro. Ou um nível do catalogo de ser selecionado, ou a descrição do item
*/
if (!$_REQUEST['stChaveClassificacao'] && !$_REQUEST['inHdnDescItem']) {
    SistemaLegado::alertaAviso("FLItensCatalogo.php?".Sessao::getId()."&stAcao=$stAcao", "Ao menos um nível do catálogo deve ser selecionado ou a descrição do item.","","aviso", Sessao::getId(), "../");
} else {
    $preview->preview();
}
