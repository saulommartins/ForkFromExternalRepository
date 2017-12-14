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
    * Página de Geração de Relatório de Ïtens
    * Data de Criação   : 27/01/2006

    * @author Tonismar Régis Bernardo

    * @ignore

    * Casos de uso : uc-03.03.20
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_FW_PDF."RRelatorioAgata.class.php" );

$stInFiltro = "";
$stFiltro = "";

$obAgata = new RRelatorioAgata( CAM_GP_AGT_ALM."RLItens.agt" );

$filtro = Sessao::read('filtro');

if ( count($filtro['inCodAlmoxarifadoSelecionado']) > 0 ) {
    $stInFiltro = implode(',',$filtro['inCodAlmoxarifadoSelecionado']);
    $stFiltro .= ' and almoxarifado.lancamento_material.cod_almoxarifado in ('.$stInFiltro.')';
}

if ($filtro['inCodCatalogo']) {
    $stFiltro .= ' and almoxarifado.catalogo_classificacao.cod_catalogo = '.$filtro['inCodCatalogo'];
}

if ( count($filtro['inCodCentroCustoRelacionado']) > 0 ) {
    $stInFiltro = implode(',', $filtro['inCodCentroCustoRelacionado']);
    $stFiltro .= ' and almoxarifado.lancamento_material.cod_centro in ('.$stInFiltro.')';
}

if ($filtro['inHdnCodItem']) {
    $stFiltro .= " and almoxarifado.catalogo_item.descricao ilike '".$filtro['inHdnCodItem']."'";
}

if (( $filtro['stDataInicial'] ) && ( $filtro['stDataFinail'] )) {
    $stFiltro .= " and to_char(almoxarifado.natureza_lancamento.timestamp,'dd/mm/yyyy') between '".$filtro['stDataInicial']."' and '".$filtro['stDataFinail'].'"';
} elseif ($filtro['stDataInicial']) {
    $stFiltro .= " and to_char(almoxarifado.natureza_lancamento.timestamp,'dd/mm/yyyy') >= '".$filtro['stDataInicial']."'";
} elseif ($filtro['stDataFinail']) {
    $stFiltro .= " and to_char(almoxarifado.natureza_lancamento.timestamp,'dd/mm/yyyy') <= '".$filtro['stDataFinail']."'";
}

switch ($filtro['stOrdem']) {
    case "classificacao":
        $obAgata->setSQLOrderBy( ' almoxarifado.catalogo_classificacao.cod_estrutural ', 1);
        $obAgata->setSQLGroupBy( $obAgata->getSQLGroupBy(1).', almoxarifado.catalogo_classificacao.cod_estrutural ', 1);
    break;
    case "item":
        $obAgata->setSQLOrderBy( ' almoxarifado.tipo_item.cod_tipo ', 1);
        $obAgata->setSQLGroupBy( $obAgata->getSQLGroupBy(1).', almoxarifado.tipo_item.cod_tipo ', 1);
    break;
    case "descricao":
        $obAgata->setSQLOrderBy( ' almoxarifado.tipo_item.descricao ', 1 );
        $obAgata->setSQLGroupBy( $obAgata->getSQLGroupBy(1).', almoxarifado.tipo_item.descricao ', 1);
    break;
}

$stFiltro = $obAgata->getSQLWhere(1).$stFiltro;
echo "<pre>".$stFiltro."</pre>";
die;
$obAgata->setSQLWhere( $stFiltro, 1 );

if ( count($arResultado) > 0 ) {
    foreach ($arResultado as $valor => $key) {
        $stResumo .=  "#tab400: ".$valor." - ".$key."\n";
    }
}

$obAgata->setResumoFinal("
        #sety450#tab150Filtros Utilizados
        #tab180 Almoxarifados Relacionados".$stResumo);
$obAgata->Header();

$ok = $obAgata->generateDocument();

if (!$ok) {
    echo $obAgata->getError();
} else {
    $obAgata->fileDialog();
}
