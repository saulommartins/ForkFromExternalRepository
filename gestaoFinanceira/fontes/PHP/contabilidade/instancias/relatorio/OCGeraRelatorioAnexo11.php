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
    * Pagina oculta para gerar relatorio de Demonstrativo de Despesa - Anexo 11
    * Data de Criação: 12/05/2005

    * @author Analista: Gelson Wolowski
    * @author Desenvolvedor: Marcelo B. Paulino

    * @ignore

    $Id: OCGeraRelatorioAnexo11.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.02.08
*/

include_once '../../../../../../config.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php" );

$obRRelatorio = new RRelatorio;
$obPDF        = new ListaPDF("L");
$rsVazio      = new RecordSet;

// Adicionar logo nos relatorios
$arFiltro = Sessao::read('filtroRelatorio');
if ( count( $arFiltro['inCodEntidade'] ) == 1 ) {
    $obRRelatorio->setCodigoEntidade( $arFiltro['inCodEntidade'][0] );
    $obRRelatorio->setExercicioEntidade ( Sessao::getExercicio() );
}

$obRRelatorio->setExercicio  ( Sessao::getExercicio() );
$obRRelatorio->recuperaCabecalho( $arConfiguracao );
$obPDF->setModulo            ( "Relatorio"   );
$obPDF->setAcao              ( "Anexo 11 - Comparativo da Despesa Autorizada com a Realizada" );
$obPDF->setSubTitulo         ( "Período: ".$arFiltro['stDataInicial']." até ".$arFiltro['stDataFinal'] );
$obPDF->setUsuario           ( Sessao::getUsername() );
$obPDF->setEnderecoPrefeitura( $arConfiguracao );

$obPDF->addRecordSet( Sessao::read('rsRecordSet') );
$obPDF->addIndentacao  ( "nivel", "descricao", "   " );

$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho   ( "CATEGORIA ECONÔMICA"                     , 45, 10 );
$obPDF->setAlinhamento ( "R" );
$obPDF->addCabecalho   ( "CRÉDITOS ORÇAMENTÁRIOS E SUPLEMENTAÇÕES" , 14, 10 );
$obPDF->addCabecalho   ( "CRÉDITOS ESPECIAIS E EXTRAORDINÁRIOS"    , 14, 10 );
$obPDF->addCabecalho   ( "TOTAL"                                   , 9, 10 );
$obPDF->addCabecalho   ( "REALIZADO"                               , 9, 10 );
$obPDF->addCabecalho   ( "DIFERENÇA"                               , 9, 10 );

$obPDF->setAlinhamento ( "L" );
$obPDF->addCampo       ( "descricao"               , 6 );
$obPDF->setAlinhamento ( "R" );
$obPDF->addCampo       ( "vl_credito_orcamentario" , 7 );
$obPDF->addCampo       ( "vl_credito_especial"     , 7 );
$obPDF->addCampo       ( "vl_total"                , 7 );
$obPDF->addCampo       ( "vl_realizado"            , 7 );
$obPDF->addCampo       ( "vl_diferenca"            , 7 );

$stDataInicial = implode('-',array_reverse(explode('/',$arFiltro['stDataInicial'])));
$stDataFinal = implode('-',array_reverse(explode('/',$arFiltro['stDataFinal'])));

include_once CAM_GF_CONT_MAPEAMENTO.'TContabilidadeNotasExplicativas.class.php';
$obTContabilidadeNotaExplicativa = new TContabilidadeNotasExplicativas;
$obTContabilidadeNotaExplicativa->setDado('cod_acao', Sessao::read('acao'));
$obTContabilidadeNotaExplicativa->setDado('dt_inicial', $stDataInicial);
$obTContabilidadeNotaExplicativa->setDado('dt_final', $stDataFinal);
$obTContabilidadeNotaExplicativa->recuperaNotaExplicativaRelatorio($rsAnexo);

//$arRecordSetNota[0]['nota'] = $rsAnexo->getCampo('nota_explicativa');
//$arRecordSetNota[1]['nota'] = '';
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
    $obPDF->addCabecalho("", 1,  10);
    $obPDF->addCabecalho("", 1, 10);

    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo("", 8 );
    $obPDF->addCampo("nota", 8 );
    $obPDF->addCampo("", 8 );
    $obPDF->addCampo("", 8 );
    $obPDF->addCampo("", 8 );
    $obPDF->addCampo("", 8 );
}

$obPDF->show();
?>
