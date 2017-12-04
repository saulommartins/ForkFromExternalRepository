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
    * Página oculta para gerar relatório
    * Data de Criação   : 29/09/2004

    * @author Desenvolvedor: Gustavo Tourinho

    * @ignore

    $Revision: 31801 $
    $Name$
    $Autor: $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.01.16
*/

include_once '../../../../../../config.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php"           );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoRelatorioAnexo9.class.php" );

$obRRelatorio = new RRelatorio;
$obPDF        = new ListaPDF("L");

// Adicionar logo nos relatorios
$arFiltro = Sessao::read('filtroRelatorio');
$arNomFiltro = Sessao::read('filtroNomRelatorio');
if ( count( $arFiltro['inCodEntidade'] ) == 1 ) {
    $obRRelatorio->setCodigoEntidade( $arFiltro['inCodEntidade'][0] );
    $obRRelatorio->setExercicioEntidade ( Sessao::getExercicio() );
}

$obRRelatorio->setExercicio  ( Sessao::getExercicio() );
$obRRelatorio->recuperaCabecalho( $arConfiguracao );
$obPDF->setModulo            ( "Orçamento Geral" );
$obPDF->setComplementoAcao   ( "- Demonstrativo da Despesa por Orgão e Função" );

if ($arFiltro['stTipoRelatorio'] == "orcamento") {
    $obPDF->setSubTitulo( $arFiltro['stDataInicial']." até ".$arFiltro['stDataFinal'] );
} else {
    switch ($arFiltro['stSituacao']) {
        case "empenhados":
            $obPDF->setSubTitulo( "Empenhado - " . $arFiltro['stDataInicial']." até ".$arFiltro['stDataFinal'] );
        break;
        case "liquidados":
            $obPDF->setSubTitulo( "Liquidado - " . $arFiltro['stDataInicial']." até ".$arFiltro['stDataFinal'] );
        break;
        case "pagos":
            $obPDF->setSubTitulo( "Pagos - "     . $arFiltro['stDataInicial']." até ".$arFiltro['stDataFinal'] );
        break;
    }
}
$obPDF->setUsuario           ( Sessao::getUsername() );
$obPDF->setEnderecoPrefeitura( $arConfiguracao );

foreach ($arFiltro['inCodEntidade'] as $inCodEntidade) {
    $arNomEntidade[] = $arNomFiltro['entidade'][$inCodEntidade];
}
$obPDF->addFiltro( 'Entidades Relacionadas'             , $arNomEntidade );

if ($arFiltro['stDataInicial']) {
    $obPDF->addFiltro( 'Periodicidade: '    ,  $arFiltro['stDataInicial']." até ".$arFiltro['stDataFinal'] );
}

if ($arFiltro['stTipoRelatorio'] == "orcamento") {
    $obPDF->addFiltro( 'Tipo de Relatório: ' , 'Orçamento' );
} else {
    $obPDF->addFiltro( 'Tipo de Relatório: ' , 'Balanço'   );
    switch ($arFiltro['stSituacao']) {
        case "empenhados":
            $obPDF->addFiltro( 'Demostrar Valores: '  , 'Empenhados' );
        break;
        case "liquidados":
            $obPDF->addFiltro( 'Demonstrar Valores: ' , 'Liquidados' );
        break;
        case "pagos":
            $obPDF->addFiltro( 'Demonstrar Valores: ' , 'Pagos'      );
        break;
    }
}

$arAnexo9 = Sessao::read('arAnexo9');

$inTotalPaginas = count($arAnexo9[1]);
$inNumeroFuncoes = count($arAnexo9[0]);
$inMaxQuebra = 5;
$inMaxFuncoes = 4;

// Cria RecordSets
$rsCabecalho = $rsTotais = new RecordSet;
$rsCabecalho->preenche ($arAnexo9[0]);
$rsTotais->preenche ($arAnexo9[2]);
// ===============

$inUltimoIndice = 2;

for ($inCountPaginas = 0; $inCountPaginas < $inTotalPaginas; $inCountPaginas++) {
    $rsBloco = new RecordSet;
    $rsBloco->preenche ($arAnexo9[1][$inCountPaginas]);

    // ADICIONA FORMATACAO PARA OS CAMPOS DE VALORES
    for ($inCount = 0, $inCountArray = $inUltimoIndice; $inCount < $inMaxFuncoes; $inCount++, $inCountArray++) {
        $rsBloco->addFormatacao($arAnexo9[0][$inCountArray], "NUMERIC_BR_NULL");
        $rsTotais->addFormatacao($arAnexo9[0][$inCountArray], "NUMERIC_BR_NULL");
    }

    $obPDF->addRecordSet( new RecordSet );
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCabecalho("", 32, 10);
    $obPDF->addCabecalho("FUNÇÔES", 68, 10);

    // ADICIONA O CABECALHO
    $obPDF->addRecordSet( $rsBloco );
    $obPDF->setAlturaCabecalho (8);
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho("CÓDIGO", 7, 10);
    $obPDF->addCabecalho("NOME", 25, 10);
    $obPDF->setAlinhamento ( "R" );
    for ($inCount = 0, $inCountArray = $inUltimoIndice; $inCount < $inMaxFuncoes; $inCount++, $inCountArray++) {
        $obPDF->addCabecalho($arAnexo9[0][$inCountArray], 17, 10);
    }
    $obPDF->setAlinhamento( "L" );
    $obPDF->addCampo("codigo", 8 );
    $obPDF->addCampo("descricao", 8 );
    $obPDF->setAlinhamento( "R" );

    for ($inCount = 0, $inCountArray = $inUltimoIndice; $inCount < $inMaxFuncoes; $inCount++, $inCountArray++) {
        $obPDF->addCampo($arAnexo9[0][$inCountArray], 8);
    }

    //ADICIONA RECORDSET COM TOTAIS POR FUNCAO
    $obPDF->addRecordSet( $rsTotais );
    $obPDF->setQuebraPaginaLista (false);
    $obPDF->setAlturaCabecalho (0);
    $obPDF->addCabecalho(" ", 7, 0);
    $obPDF->addCabecalho(" ", 25, 0);
    for ($inCount = 0; $inCount < $inMaxFuncoes ; $inCount++) {
        $obPDF->addCabecalho(" ", 17, 1);
    }
    $obPDF->setAlinhamento( "L" );
    $obPDF->addCampo(" ", 8 );
    $obPDF->addCampo("descricao", 8 );
    $obPDF->setAlinhamento( "R" );

    for ($inCount = 0, $inCountArray2 = $inUltimoIndice; $inCount < $inMaxFuncoes; $inCount++, $inCountArray2++) {
        $obPDF->addCampo($arAnexo9[0][$inCountArray2], 8);
    }

    $inUltimoIndice = $inCountArray;
}

$stDataInicial = implode('-',array_reverse(explode('/',$arFiltro['stDataInicial'])));
$stDataFinal = implode('-',array_reverse(explode('/',$arFiltro['stDataFinal'])));

include_once CAM_GF_CONT_MAPEAMENTO.'TContabilidadeNotasExplicativas.class.php';
$obTContabilidadeNotaExplicativa = new TContabilidadeNotasExplicativas;
$obTContabilidadeNotaExplicativa->setDado('cod_acao', Sessao::read('acao'));
$obTContabilidadeNotaExplicativa->setDado('dt_inicial', $stDataInicial);
$obTContabilidadeNotaExplicativa->setDado('dt_final', $stDataFinal);
$obTContabilidadeNotaExplicativa->recuperaNotaExplicativaRelatorio($rsAnexo);

$arNota = explode("\n", $rsAnexo->getCampo('nota_explicativa'));
$inCount = 0;
foreach ($arNota as $arNotaTMP) {
    $arRecordSetNota[$inCount]['nota'] = $arNotaTMP;
    $inCount++;
}

if ($rsAnexo->getCampo('nota_explicativa')) {
    $rsNota = new RecordSet;
    $rsNota->preenche($arRecordSetNota);
    $obPDF->addRecordSet($rsNota);
    $obPDF->setQuebraPaginaLista(false);

    $obPDF->addCabecalho("", 1,  10);
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho("NOTAS EXPLICATIVAS", 90, 10);

    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo("", 8 );
    $obPDF->addCampo("nota", 8 );
}

$arAssinaturas = Sessao::read('assinaturas');
if ( count($arAssinaturas['selecionadas']) > 0 ) {
    include_once( CAM_FW_PDF."RAssinaturas.class.php" );
    $obRAssinaturas = new RAssinaturas;
    $obRAssinaturas->setArAssinaturas( $arAssinaturas['selecionadas'] );
    $obPDF->setAssinaturasDefinidas( $obRAssinaturas->getArAssinaturas() );
}

$obPDF->show();
?>
