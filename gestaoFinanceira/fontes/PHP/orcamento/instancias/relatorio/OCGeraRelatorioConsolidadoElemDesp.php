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
set_time_limit(0);
/**
    * Página de Formulario de Seleção de Impressora para Relatorio
    * Data de Criação   : 13/08/2004

    * @author Desenvolvedor: Vandre Miguel Ramos

    * @ignore

    $Revison:$
    $Name$
    $Autor:$
    $Date: 2007-12-05 15:12:56 -0200 (Qua, 05 Dez 2007) $

    * Casos de uso: uc-02.01.23
*/
require_once '../../../../../../config.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
require_once CAM_FW_PDF."RRelatorio.class.php";

$obRRelatorio = new RRelatorio;
$obPDF        = new ListaPDF( "L" );

// Adicionar logo nos relatorios
$arFiltro = Sessao::read('filtroRelatorio');
$arNomFiltro = Sessao::read('filtroNomRelatorio');
if ( count( $arFiltro['inCodEntidade'] ) == 1 ) {
    $obRRelatorio->setCodigoEntidade( $arFiltro['inCodEntidade'][0] );
    $obRRelatorio->setExercicioEntidade ( Sessao::getExercicio() );
}

if ($arFiltro['inAno']) {
   $obRRelatorio->setExercicio ( $arFiltro['inAno'] );
} else {
   $obRRelatorio->setExercicio  ( Sessao::getExercicio() );
}
$obRRelatorio->recuperaCabecalho( $arConfiguracao );

if ($arf['inAno']) {
   $obPDF->setModulo            ('Orçamento Geral'.'  -  '. $arFiltro['inAno']);
} else {
   $obPDF->setModulo            ( 'Orçamento Geral'.' - '.  Sessao::getExercicio() );
}
$obPDF->setTitulo            ( "Consolidado por elemento de despesa" );
$obPDF->setSubTitulo         ( $arFiltro['stDataInicial'] .' à '. $arFiltro['stDataFinal']);
$obPDF->setUsuario           ( Sessao::getUsername() );
$obPDF->setEnderecoPrefeitura( $arConfiguracao );

foreach ($arFiltro['inCodEntidade'] as $inCodEntidade) {
    $arNomEntidade[] = $arNomFiltro['entidade'][$inCodEntidade];
}

$obPDF->addFiltro( 'Entidades Relacionadas'             , $arNomEntidade );

if ($arFiltro['stDataInicial']) {
    $obPDF->addFiltro( 'Periodicidade: '    ,  $arFiltro['stDataInicial']." até ".$arFiltro['stDataFinal'] );
}

if($arFiltro['inCodOrgao'])
    $obPDF->addFiltro( 'Órgão Orçamentário Inicial'    , $arFiltro['inCodOrgao'] . " - " . $arNomFiltro['orgao'][$arFiltro[ 'inCodOrgao' ]] );

if($arFiltro['inCodOrgaoFinal'])
    $obPDF->addFiltro( 'Órgão Orçamentário Final'    , $arFiltro['inCodOrgaoFinal'] . " - " . $arNomFiltro['orgao'][$arFiltro[ 'inCodOrgaoFinal' ]] );

if($arFiltro['inCodUnidade'])
    $obPDF->addFiltro( 'Unidade Orçamentária Inicial'  , $arFiltro['inCodUnidade'] . " - " . $arNomFiltro['unidade'][$arFiltro[ 'inCodUnidade' ]] );

if($arFiltro['inCodUnidadeFinal'])
    $obPDF->addFiltro( 'Unidade Orçamentária Final'  , $arFiltro['inCodUnidadeFinal'] . " - " . $arNomFiltro['unidadeFinal'][$arFiltro[ 'inCodUnidadeFinal' ]] );

if ($arFiltro['stDescricaoRecurso'] != "") {
    $obPDF->addFiltro( 'Recurso: '    ,  $arFiltro['inCodRecurso']." - ".$arFiltro['stDescricaoRecurso'] );
}

if($arFiltro['inCodFuncao']) {
    $obPDF->addFiltro( 'Função'  , $arFiltro['inCodFuncao'] . " - " . $arNomFiltro['funcao'][$arFiltro[ 'inCodFuncao' ]] );
}

if($arFiltro['inCodSubFuncao']) {
    $obPDF->addFiltro( 'Subfunção'  , $arFiltro['inCodSubFuncao'] . " - " . $arNomFiltro['subfuncao'][$arFiltro[ 'inCodSubFuncao' ]] );
}

if($arFiltro['inCodUso'] && $arFiltro['inCodDestinacao'] && $arFiltro['inCodEspecificacao'])
    $obPDF->addFiltro( 'Destinação de Recursos', $arFiltro['inCodUso'].".".$arFiltro['inCodDestinacao'].".".$arFiltro['inCodEspecificacao'] );

if ($arFiltro['inTipo'] == "1") {
    $obPDF->addFiltro( 'Demonstrar Sintéticas: '    ,  "Não" );
} else {
    $obPDF->addFiltro( 'Demonstrar Sintéticas: '    ,  "Sim" );
}


//Registros
$rsConsolidado = Sessao::read('rsConsolidado');
$obPDF->addRecordSet( $rsConsolidado );

$obPDF->setAlturaCabecalho(5);
$obPDF->setAlinhamento ( "C" );
$obPDF->addCabecalho("Elemento da Despesa", 15,8);
$obPDF->addCabecalho("",22, 8);
$obPDF->setAlinhamento ( "R" );
$obPDF->addCabecalho("SALDO INICIAL EMPENHADO NO MÊS EMPENHADO ATÉ PER",12,8);
$obPDF->addCabecalho("SUPLEMENTAÇOES ANULADO NO MÊS ANULADO ATÉ PER",12,8 );
$obPDF->addCabecalho("REDUÇÕES LIQUIDADO NO MÊS LIQUIDADO ATÉ PER",12, 8);
$obPDF->addCabecalho("TOTAL CRÉDITO PAGO NO MÊS PAGO ATÉ PER",11, 8);
$obPDF->addCabecalho("SALDO DISPONÍVEL A LIQUIDAR A PAGAR LÍQUIDADO ",13, 8);
$obPDF->addIndentacao("nivel","[classificacao]  [descricao_despesa]","    ");
$obPDF->addQuebraLinha("nivel",2,5);

$obPDF->setAlturaLinha ( 4 );
$obPDF->setAlinhamento ( "C" );
$obPDF->addCampo("classificacao", 8 );
$obPDF->setAlinhamento ( "L" );
$obPDF->addCampo("descricao_despesa", 8 );
$obPDF->setAlinhamento ( "R" );
$obPDF->addCampo("coluna3", 8 );
$obPDF->addCampo("coluna4", 8 );
$obPDF->addCampo("coluna5", 8 );
$obPDF->addCampo("coluna6", 8 );
$obPDF->addCampo("coluna7", 8 );

$arAssinaturas = Sessao::read('assinaturas');
if ( count($arAssinaturas['selecionadas']) > 0 ) {
    include_once( CAM_FW_PDF."RAssinaturas.class.php" );
    $obRAssinaturas = new RAssinaturas;
    $obRAssinaturas->setArAssinaturas( $arAssinaturas['selecionadas'] );
    $obPDF->setAssinaturasDefinidas( $obRAssinaturas->getArAssinaturas() );
}

$obPDF->show();
?>
