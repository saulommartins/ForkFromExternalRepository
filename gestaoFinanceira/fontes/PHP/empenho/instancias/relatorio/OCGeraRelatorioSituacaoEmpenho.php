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
    * Página de Formulario de Seleção de Impressora para Relatorio
    * Data de Criação   : 13/05/2005

    * @author Analista: Dieine da Silva
    * @author Desenvolvedor: Lucas Leusin Oaigen

    * @ignore

    $Id: OCGeraRelatorioSituacaoEmpenho.php 65133 2016-04-27 14:20:11Z michel $

    * Casos de uso: uc-02.03.13
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GF_EMP_NEGOCIO."REmpenhoRelatorioSituacaoEmpenho.class.php";

$obRegra      = new REmpenhoRelatorioSituacaoEmpenho;
$obPDF        = new ListaPDF( "L" );

$arFiltro    = Sessao::read('filtroRelatorio');
$arFiltroNom = Sessao::read('filtroNomRelatorio');
$rsRecordSet = Sessao::read('rsRecordSet');
// Adicionar logo nos relatorios
if ( count( $arFiltro['inCodEntidade'] ) == 1 ) {
    $obRegra->obRRelatorio->setCodigoEntidade( $arFiltro['inCodEntidade'][0] );
    $obRegra->obRRelatorio->setExercicioEntidade ( Sessao::getExercicio() );
}

$obRegra->obRRelatorio->recuperaCabecalho ( $arConfiguracao          );
$obPDF->setModulo                ( "Empenho - ".$arFiltro['stExercicio']   );
$obPDF->setTitulo                ( "Situação de Empenhos " . $arFiltro['relatorio'] );

$obRegra->obREmpenhoEmpenho->obROrcamentoEntidade->obRCGM->setNumCGM( Sessao::read('numCgm') );
$obRegra->obREmpenhoEmpenho->obROrcamentoEntidade->obRCGM->consultar($rsCGM);

switch ($arFiltro[ 'inSituacao' ]) {
    case 1:
        $stSituacao = "Empenhados";
    break;
    case 2:
        $stSituacao = "Anulados";
    break;
    case 3:
        $stSituacao = "Liquidados";
    break;
    case 4:
        $stSituacao = "A Liquidar";
    break;
    case 5:
        $stSituacao = "Pagos";
    break;
    case 6:
        $stSituacao = "A Pagar";
    break;
    default:
        $stSituacao = "Todos";
}
$obPDF->setSubTitulo         ( $stSituacao  );

$obPDF->setUsuario           ( Sessao::getUsername() );
$obPDF->setEnderecoPrefeitura( $arConfiguracao );

$obPDF->addRecordSet( $rsRecordSet );

foreach ($arFiltro['inCodEntidade'] as $inCodEntidade) {
    $arNomEntidade[] = $arFiltroNom['entidade'][$inCodEntidade];
}

switch ($arFiltro['inOrdenacao']) {
    case 1:
        $stOrdenacao = "Empenho";
    break;
    case 2:
        $stOrdenacao = "Credor";
    break;
    case 3:
        $stOrdenacao = "Data Pagamento";
    break;
}

$obPDF->addFiltro('Exercício'             , $arFiltro['inExercicio']);
$obPDF->addFiltro('Entidades Relacionadas', $arNomEntidade);
$obPDF->addFiltro('Periodicidade Emissão' , $arFiltro['stDataInicialEmissao']." até ".$arFiltro['stDataFinalEmissao']);
$obPDF->addFiltro('Situação Até'          , $arFiltro['stDataSituacao']);

if ($arFiltro['inCodTipoEmpenho'] != "") {
    $obPDF->addFiltro('Tipo de Empenho'   , SistemaLegado::pegaDado('nom_tipo', 'empenho.tipo_empenho', ' WHERE tipo_empenho.cod_tipo='.$arFiltro['inCodTipoEmpenho']));
}
if ($arFiltro['inCentroCusto'] && $arFiltro['inCentroCusto'] != ""){
    $stCentroCusto = $arFiltro['inCentroCusto']." - ".SistemaLegado::pegaDado('descricao', 'almoxarifado.centro_custo', ' WHERE centro_custo.cod_centro='.$arFiltro['inCentroCusto']);
    $obPDF->addFiltro( 'Centro de Custo' , $stCentroCusto );
}

if ($arFiltro['inCodDotacao']) {
    $obRegra->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setCodDespesa($arFiltro['inCodDotacao']);
    $obRegra->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setExercicio( $arFiltro['inExercicio']);
    $obRegra->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->listarDespesaUsuario( $rsDespesa );

    $obPDF->addFiltro( 'Dotação Orçamentária'               , $arFiltro[ 'inCodDotacao' ] . " - " . $rsDespesa->getCampo( "descricao" ));
}
if ($arFiltro['inCodDespesa']) {
    $arMascClassificacao = Mascara::validaMascaraDinamica( $arFiltro['stMascClassificacao'] , $arFiltro['inCodDespesa'] );

    //busca DESCRICAO DA RUBRICA DE DESPESA
    $obRegra->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obROrcamentoClassificacaoDespesa->setMascara  ( $arFiltro['stMascClassificacao'] );
    $obRegra->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obROrcamentoClassificacaoDespesa->setMascClassificacao( $arMascClassificacao[1]       );
    $obRegra->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obROrcamentoClassificacaoDespesa->recuperaDescricaoDespesa( $stDescricao );

    $obPDF->addFiltro( 'Elemento da Despesa'                , $arFiltro[ 'inCodDespesa' ] . " - " . $stDescricao);
}
if($arFiltro['inNumOrgao'])
    $obPDF->addFiltro( 'Órgão Orçamentário'                 , $arFiltro['inNumOrgao'] . " - " . $arFiltroNom['orgao'][$arFiltro[ 'inNumOrgao' ]] );
if($arFiltro['inNumUnidade'])
    $obPDF->addFiltro( 'Unidade Orçamentária'               , $arFiltro['inNumUnidade'] . " - " . $arFiltroNom['unidade'][$arFiltro[ 'inNumUnidade' ]] );
$obPDF->addFiltro( 'Recurso'                                , $arFiltroNom['recurso'][$arFiltro[ 'inCodRecurso' ]] );
$obPDF->addFiltro( 'Ordenação'                              , $stOrdenacao);
if ($arFiltro['inCodFornecedor']) {
    $obRegra->obREmpenhoEmpenho->obROrcamentoEntidade->obRCGM->setNumCGM($arFiltro['inCodFornecedor']);
    $obRegra->obREmpenhoEmpenho->obROrcamentoEntidade->obRCGM->consultar($rsCGMFiltro);
    $obPDF->addFiltro( 'Credor' , $arFiltro[ 'inCodFornecedor' ] . " - " . $rsCGMFiltro->getCampo( "nom_cgm" ));
}
$obPDF->addFiltro( 'Situação'                           , $stSituacao );

$obPDF->setAlinhamento ( "C" );
$obPDF->addCabecalho("EMPENHO"           ,  7, 8);
$obPDF->addCabecalho("EMISSÃO"           ,  6, 8);
$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho("CREDOR"            , 16, 8);
$obPDF->setAlinhamento ( "R" );
$obPDF->addCabecalho("EMPENHADO"         ,  8, 8);
$obPDF->addCabecalho("ANULADO"           ,  8, 8);
$obPDF->addCabecalho("SALDO EMPENHADO"   , 10, 8);
$obPDF->addCabecalho("LIQUIDADO"         , 10, 8);
$obPDF->addCabecalho("A LIQUIDAR"        ,  8, 8);
$obPDF->addCabecalho("PAGO"              ,  6, 8);
$obPDF->addCabecalho("EMPENHADO A PAGAR" , 10, 8);
$obPDF->addCabecalho("LIQUIDADO A PAGAR" , 10, 8);
$obPDF->addQuebraLinha("nivel",2,5);

$obPDF->setAlinhamento ( "C" );
$obPDF->addCampo("empenho"          , 7 );
$obPDF->addCampo("emissao"          , 7 );
$obPDF->setAlinhamento ( "L" );
$obPDF->addCampo("credor"           , 6 );
$obPDF->setAlinhamento ( "R" );
$obPDF->addCampo("empenhado"        , 7 );
$obPDF->addCampo("anulado"          , 7 );
$obPDF->addCampo("saldoempenhado"   , 7 );
$obPDF->addCampo("liquidado"        , 7 );
$obPDF->addCampo("aliquidar"        , 7 );
$obPDF->addCampo("pago"             , 7 );
$obPDF->addCampo("empenhadoapagar"  , 7 );
$obPDF->addCampo("liquidadoapagar"  , 7 );

$arAssinaturas = Sessao::read('assinaturas');
if ( count($arAssinaturas['selecionadas']) > 0 ) {
    include_once CAM_FW_PDF."RAssinaturas.class.php";
    $obRAssinaturas = new RAssinaturas;
    $obRAssinaturas->setArAssinaturas( $arAssinaturas['selecionadas'] );
    $obPDF->setAssinaturasDefinidas( $obRAssinaturas->getArAssinaturas() );
    $obRAssinaturas->montaPDF( $obPDF );
}

$obPDF->show();
?>
