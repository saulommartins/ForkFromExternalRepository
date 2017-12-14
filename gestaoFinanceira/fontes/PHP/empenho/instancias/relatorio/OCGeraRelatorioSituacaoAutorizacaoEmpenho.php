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
    * Página de Filtro de Relatório Situação de Autorizações de Empenho
    * Data de Criação   : 12/10/2006

    * @author Tonismar Régis Bernardo

    * @ignore

    $Revision: 30805 $
    $Name$
    $Author: lbbarreiro $
    $Date: 2008-01-15 12:00:12 -0200 (Ter, 15 Jan 2008) $

    * Casos de uso : uc-02.03.34
*/

/*

$Log:

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

include_once( CAM_GF_EMP_NEGOCIO."REmpenhoRelatorioSituacaoEmpenho.class.php"  );

$obRegra      = new REmpenhoRelatorioSituacaoEmpenho;
$obPDF        = new ListaPDF( "L" );

$arFiltro = Sessao::read('filtroRelatorio');
$arFiltroNom = Sessao::read('filtroNomRelatorio');
$rsRecordSet = Sessao::read('rsRecordSet');

switch ($arFiltro['inSituacao']) {
    case 0:
        $stSituacao = 'Todas';
    break;
    case 1:
        $stSituacao = 'Empenhadas';
    break;
    case 2:
        $stSituacao = 'Não Empenhadas';
    break;
    case 3:
        $stSituacao = 'Anuladas';
    break;
}

// Adicionar logo nos relatorios
if ( count( $arFiltro['inCodEntidade'] ) == 1 ) {
    $obRegra->obRRelatorio->setCodigoEntidade( $arFiltro['inCodEntidade'][0] );
    $obRegra->obRRelatorio->setExercicioEntidade ( Sessao::getExercicio() );
}

$obRegra->obRRelatorio->recuperaCabecalho ( $arConfiguracao );
$obPDF->setModulo ( "Empenho - ".$arFiltro['stExercicio'] );
$obPDF->setTitulo ( "Situação de Autorização de Empenho " .$arFiltro['relatorio'] );
$obPDF->setSubTitulo ( $arFiltro['inSituacao'].' - '.$stSituacao );

$obRegra->obREmpenhoEmpenho->obROrcamentoEntidade->obRCGM->setNumCGM ( Sessao::read('numCgm') );
$obRegra->obREmpenhoEmpenho->obROrcamentoEntidade->obRCGM->consultar ( $rsCGM );

$obPDF->setUsuario ( Sessao::getUsername() );
$obPDF->setEnderecoPrefeitura( $arConfiguracao );

$obPDF->addRecordSet ( $rsRecordSet );

foreach ($arFiltro['inCodEntidade'] as $inCodEntidade) {
    $arNomEntidade[] = $arFiltroNom['entidade'][$inCodEntidade];
}

switch ($arFiltro[ 'inOrdenacao' ]) {
    case 1:
        $stOrdenacao = "Autorização";
        break;
    case 2:
        $stOrdenacao = "Credor";
        break;
    case 3:
        $stOrdenacao = "Data de Empenhamento";
        break;
}

$obPDF->addFiltro( 'Exercício' , $arFiltro[ 'inExercicio' ] );
$obPDF->addFiltro( 'Entidades Relacionadas' , $arNomEntidade );
$obPDF->addFiltro( 'Periodicidade Emissão' , $arFiltro[ 'stDataInicial' ]. " até " .$arFiltro[ 'stDataFinal' ] );
$obPDF->addFiltro( 'Situação Até' , $arFiltro[ 'stSituacao' ] );
$obPDF->addFiltro( 'Autorização' , $arFiltro[ 'inNumAutorizacao' ] );

if( $arFiltro['inNumOrgao'] )
    $obPDF->addFiltro( 'Órgão Orçamentário' , $arFiltro['inNumOrgao']." - ".$arFiltroNom['orgao'][$arFiltro[ 'inNumOrgao' ]] );
if( $arFiltro['inNumUnidade'] )
    $obPDF->addFiltro( 'Unidade Orçamentária' , $arFiltro['inNumUnidade']." - ".$arFiltroNom['unidade'][$arFiltro[ 'inNumUnidade' ]] );
$obPDF->addFiltro( 'Recurso' , $arFiltroNom['recurso'][$arFiltro[ 'inCodRecurso' ]] );
$obPDF->addFiltro( 'Ordenação' , $stOrdenacao );
if ($arFiltro['inCodCredor']) {
    $obRegra->obREmpenhoEmpenho->obROrcamentoEntidade->obRCGM->setNumCGM ( $arFiltro['inCodCredor'] );
    $obRegra->obREmpenhoEmpenho->obROrcamentoEntidade->obRCGM->consultar ( $rsCGMFiltro );
    $obPDF->addFiltro( 'Credor' , $arFiltro[ 'inCodCredor' ]." - ".$rsCGMFiltro->getCampo( "nom_cgm" ));
}

$obPDF->addFiltro( 'Situação' , $arFiltro['inSituacao'].' - '.$stSituacao );

$obPDF->setAlinhamento ( "C" );
$obPDF->addCabecalho("AUTORIZAÇÃO"          ,15, 8);
$obPDF->addCabecalho("EMISSÃO"              , 6, 8);
$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho("CREDOR"               ,16, 8);
$obPDF->setAlinhamento ( "R" );
$obPDF->addCabecalho("AUTORIZADO"           , 8, 8);
$obPDF->addCabecalho("ANULADO"              , 9, 8);
$obPDF->addCabecalho("SALDO AUTORIZADO"     ,10, 8);
$obPDF->setAlinhamento( "R" );
$obPDF->addCabecalho("EMPENHO"              , 9, 8);
$obPDF->setAlinhamento( "R" );
$obPDF->addCabecalho("LIQUIDADO"            , 9, 8);
//$obPDF->addCabecalho("AUTORIZADO"         , 8, 8);
//$obPDF->addCabecalho("A LIQUIDAR"         , 7, 8);
$obPDF->addCabecalho("PAGO"                 , 7, 8);
$obPDF->addCabecalho("A PAGAR"    			, 8, 8);
//$obPDF->addCabecalho("LIQUIDADO A PAGAR"  , 8, 8);
$obPDF->addQuebraLinha("nivel",2,5);

$obPDF->setAlinhamento ( "C" );
$obPDF->addCampo("autorizacao"        , 7 );
//$obPDF->addCampo("empenho"          , 7 );
$obPDF->addCampo("emissao"            , 7 );
$obPDF->setAlinhamento ( "L" );
$obPDF->addCampo("credor"             , 6 );
$obPDF->setAlinhamento ( "R" );
//$obPDF->addCampo("empenhado"        , 7 );
$obPDF->addCampo("autorizado"         , 7 );
$obPDF->addCampo("autorizado_anulado" , 7 );
$obPDF->addCampo("saldoautorizado"    , 7 );
$obPDF->addCampo("empenho"            , 7 );
$obPDF->addCampo("liquidado"          , 7 );
//$obPDF->addCampo("aliquidar"        , 7 );
$obPDF->addCampo("pago"               , 7 );
$obPDF->addCampo("empenhadoapagar"    , 7 );
//$obPDF->addCampo("liquidadoapagar"  , 7 );

$arAssinaturas = Sessao::read('assinaturas');
if ( count($arAssinaturas['selecionadas']) > 0 ) {
    include_once( CAM_FW_PDF."RAssinaturas.class.php" );
    $obRAssinaturas = new RAssinaturas;
    $obRAssinaturas->setArAssinaturas( $arAssinaturas['selecionadas'] );
    $obPDF->setAssinaturasDefinidas( $obRAssinaturas->getArAssinaturas() );
    $obRAssinaturas->montaPDF( $obPDF );
}

$obPDF->show();

?>
