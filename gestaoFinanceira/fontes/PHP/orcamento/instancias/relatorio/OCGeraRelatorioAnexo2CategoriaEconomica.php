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
    $Author: rodrigosoares $
    $Date: 2008-01-07 16:42:06 -0200 (Seg, 07 Jan 2008) $

    * Casos de uso: uc-02.01.11
*/

include_once '../../../../../../config.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php"           );

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

if ($arFiltro['stDemonstrarValores'] == "orcamento") {
    $stDemonstrarValores = "Orçamento";
} else {
    $stDemonstrarValores = "Balanço";
    if ($arFiltro['stSituacao'] == "empenhados")
        $stSituacao = "Empenhado";
    if ($arFiltro['stSituacao'] == "pagos")
        $stSituacao = "Pago";
    if ($arFiltro['stSituacao'] == "liquidados")
        $stSituacao = "Liquidado";
    $stComplSubTitulo = " - Vlr. ".$stSituacao;
}

$obPDF->setComplementoAcao ( " - Natureza da Despesa por Un. segundo Cat.Econ." );

$stSubTitulo = "Anexo 2 - ". $stDemonstrarValores ." - ".$arFiltro['stDataInicial']." até ".$arFiltro['stDataFinal'].$stComplSubTitulo;

$obPDF->setSubTitulo         ( $stSubTitulo );
$obPDF->setUsuario           ( Sessao::getUsername() );
$obPDF->setEnderecoPrefeitura( $arConfiguracao );

sort($arFiltro['inCodEntidade']);
foreach ($arFiltro['inCodEntidade'] as $inCodEntidade) {
    $arNomEntidade[] = $arNomFiltro['entidade'][$inCodEntidade];
}

$obPDF->addFiltro( 'Entidades Relacionadas'   , $arNomEntidade );

if ( isset($arFiltro["stNomOrgao"]) ) {
    $obPDF->addFiltro( 'Órgão'                , $arFiltro["stNomOrgao"] );
}
if ( isset($arFiltro["stNomUnidade"]) ) {
    $obPDF->addFiltro( 'Unidade'              , $arFiltro["stNomUnidade"] );
}

$boPrimeiro = true;
$arRecordSet = array();
$arRecordSet[0] = Sessao::read('rsAnexo2');
$arRecordSet[1] = Sessao::read('rsTotal');
$arRecordSet[2] = Sessao::read('arTotalizadores');

for ($inCat=0; $inCat <=2; $inCat++) {
    $inTotalPaginas = count($arRecordSet[$inCat][1]);
    $inMaxQuebra = 5;
    $inMaxFuncoes = 3;

    // Cria RecordSets
    $rsCabecalho = $rsTotais = new RecordSet;
    $rsCabecalho->preenche ($arRecordSet[$inCat][0]);
    $rsTotais->preenche ($arRecordSet[$inCat][2]);
    // ===============

    $inUltimoIndice = 1;

    for ($inCountPaginas = 0; $inCountPaginas < $inTotalPaginas; $inCountPaginas++) {
        $rsBloco = new RecordSet;
        $rsBloco->preenche ($arRecordSet[$inCat][1][$inCountPaginas]);

        // ADICIONA FORMATACAO PARA OS CAMPOS DE VALORES
        for ($inCount = 0, $inCountArray = $inUltimoIndice; $inCount < $inMaxFuncoes; $inCount++, $inCountArray++) {
            $rsBloco->addFormatacao($arRecordSet[$inCat][0][$inCountArray], "NUMERIC_BR_NULL");
            $rsTotais->addFormatacao($arRecordSet[$inCat][0][$inCountArray], "NUMERIC_BR_NULL");
        }
        //
        $obPDF->addRecordSet( new RecordSet );
        $obPDF->setAlturaCabecalho (8);
        $obPDF->setAlinhamento ( "L" );
        $obPDF->addCabecalho("", 25, 10);
        if( $inCat == 0)
            $obPDF->addCabecalho("--------------------- DESPESAS CORRENTES ---------------------", 75, 10);
        elseif( $inCat == 1)
            $obPDF->addCabecalho("--------------------- DESPESAS DE CAPITAL ---------------------", 75, 10);
        else
            $obPDF->addCabecalho("---------------------  TOTAIS  ---------------------", 75, 10);
        $obPDF->addCampo("", 8 );

        // ADICIONA O CABECALHO VALORES
        $obPDF->addRecordSet( $rsBloco );
        $obPDF->setQuebraPaginaLista(false);
        $obPDF->setAlturaCabecalho (8);
        $obPDF->setAlinhamento ( "L" );
        $obPDF->addCabecalho("UNIDADES ORCAMENTARIAS", 25, 10);
        $obPDF->setAlinhamento ( "R" );
        for ($inCount = 0, $inCountArray = $inUltimoIndice; $inCount < $inMaxFuncoes; $inCount++, $inCountArray++) {
            $obPDF->addCabecalho($arRecordSet[$inCat][0][$inCountArray], 17, 10);
        }

        $obPDF->setAlinhamento( "L" );

        $obPDF->addCampo("descricao", 8 );
        $obPDF->setAlinhamento( "R" );

        for ($inCount = 0, $inCountArray = $inUltimoIndice; $inCount < $inMaxFuncoes; $inCount++, $inCountArray++) {
            $obPDF->addCampo($arRecordSet[$inCat][0][$inCountArray], 8);
        }

        //ADICIONA RECORDSET COM TOTAIS POR FUNCAO
        $obPDF->addRecordSet( $rsTotais );
        $obPDF->setQuebraPaginaLista (false);
        $obPDF->setAlturaCabecalho (0);
        $obPDF->addCabecalho(" ", 25, 0);
        for ($inCount = 0; $inCount < $inMaxFuncoes ; $inCount++) {
            $obPDF->addCabecalho(" ", 17, 1);
        }
        $obPDF->setAlinhamento( "L" );
        $obPDF->addCampo("descricao", 8 );
        $obPDF->setAlinhamento( "R" );

        for ($inCount = 0, $inCountArray2 = $inUltimoIndice; $inCount < $inMaxFuncoes; $inCount++, $inCountArray2++) {
            $obPDF->addCampo($arRecordSet[$inCat][0][$inCountArray2], 8);
        }

        $inUltimoIndice = $inCountArray;
    }

    //ADICIONA RECORDSET COM CONTINGENCIA
    $rsCategoriaEconomica9  = Sessao::read('rsCategoriaEconomica9');
    $obPDF->addRecordSet( $rsCategoriaEconomica9 );
    $obPDF->setQuebraPaginaLista (false);
    $obPDF->setAlturaCabecalho (-3);
    $obPDF->addCabecalho(" ", 25, 0);
    $obPDF->addCabecalho(" ", 17, 0);
    $obPDF->addCabecalho(" ", 17, 0);
    $obPDF->addCabecalho(" ", 17, 0);

    $obPDF->setAlinhamento( "L" );
    $obPDF->addCampo("descricao", 8 );
    $obPDF->addCampo("", 8);
    $obPDF->addCampo("", 8);
    $obPDF->setAlinhamento( "R" );
    $obPDF->addCampo("valor", 8);

    //ADICIONA RECORDSET COM RESERVA DO RPPS
    $rsCategoriaEconomica7 = Sessao::read('rsCategoriaEconomica7');
    $obPDF->addRecordSet( $rsCategoriaEconomica7 );
    $obPDF->setQuebraPaginaLista (false);
    $obPDF->setAlturaCabecalho (-3);
    $obPDF->addCabecalho(" ", 25, 0);
    $obPDF->addCabecalho(" ", 17, 0);
    $obPDF->addCabecalho(" ", 17, 0);
    $obPDF->addCabecalho(" ", 17, 0);

    $obPDF->setAlinhamento( "L" );
    $obPDF->addCampo("descricao", 8 );
    $obPDF->addCampo("", 8);
    $obPDF->addCampo("", 8);
    $obPDF->setAlinhamento( "R" );
    $obPDF->addCampo("valor", 8);

    //ADICIONA RECORDSET COM CONTINGENCIA
    $rsTotalGeral = Sessao::read('rsTotalGeral');
    $obPDF->addRecordSet( $rsTotalGeral );
    $obPDF->setQuebraPaginaLista (false);
    $obPDF->setAlturaCabecalho (0);
    $obPDF->addCabecalho(" ", 25, 0);
    $obPDF->addCabecalho(" ", 17, 0);
    $obPDF->addCabecalho(" ", 17, 0);
    $obPDF->addCabecalho(" ", 17, 0);

    $obPDF->setAlinhamento( "L" );
    $obPDF->addCampo("descricao", 8 );
    $obPDF->addCampo("", 8);
    $obPDF->addCampo("", 8);
    $obPDF->setAlinhamento( "R" );
    $obPDF->addCampo("valor", 8);

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
    $obPDF->addCabecalho("", 1,  10);
    $obPDF->addCabecalho("", 1, 10);

    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo("", 8 );
    $obPDF->addCampo("nota", 8 );
    $obPDF->addCampo("", 8 );
    $obPDF->addCampo("", 8 );
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
