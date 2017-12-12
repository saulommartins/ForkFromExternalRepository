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
    * Pagina oculta para gerar relatorio
    * Data de Criação   : 27/09/2004

    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

    * @ignore

    $Revision: 31801 $
    $Name$
    $Autor: $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.01.15
*/

include_once '../../../../../../config.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php"                );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoRelatorioAnexo8.class.php"   );

$obRRelatorio = new RRelatorio;
$obPDF        = new ListaPDF("L");
$stSituacao = "";

$arFiltro = Sessao::read('filtroRelatorio');

if ($arFiltro['stSituacao'] == "empenhados") {
    $stSituacao = "         Valores - Empenhados";
} elseif ($arFiltro['stSituacao'] == "pagos") {
    $stSituacao = "         Valores - Pagos";
} elseif ($arFiltro['stSituacao'] == "liquidados") {
    $stSituacao = "         Valores - Liquidados";
}
$periodicidade = $arFiltro['stDataInicial']." até ".$arFiltro['stDataFinal'];

// Adicionar logo nos relatorios
if ( count( $arFiltro['inCodEntidade'] ) == 1 ) {
    $obRRelatorio->setCodigoEntidade( $arFiltro['inCodEntidade'][0] );
    $obRRelatorio->setExercicioEntidade ( Sessao::getExercicio() );
}

$obRRelatorio->setExercicio  ( Sessao::getExercicio() );
$obRRelatorio->recuperaCabecalho( $arConfiguracao );
$obPDF->setModulo            ( "Orçamento Geral" );
$obPDF->setComplementoAcao   ( "- Demonstr. Desp. por Função, Subfunção e Progr." );
$obPDF->setSubTitulo         ( "Anexo 8 - " . $periodicidade . $stSituacao);
$obPDF->setUsuario           ( Sessao::getUsername() );
$obPDF->setEnderecoPrefeitura( $arConfiguracao );

$rsAnexo8 = Sessao::read('rsAnexo8');
$rsTotal = Sessao::read('rsTotal');
$arEntidade = Sessao::read('arEntidade');

$obPDF->addRecordSet( $rsAnexo8 );

$obPDF->addIndentacao  ( "nivel", "descricao", "  ");
$obPDF->addQuebraLinha ( "nivel", 1 );

$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho   ("CÓDIGO", 15, 10);
$obPDF->addCabecalho   ("ESPECIFICAÇÃO", 40, 10);
$obPDF->setAlinhamento ( "R" );
$obPDF->addCabecalho   ("ORDINARIA",15, 10);
$obPDF->addCabecalho   ("VINCULADA",15, 10);
$obPDF->addCabecalho   ("TOTAL", 15, 10);

$obPDF->setAlinhamento ( "L" );
$obPDF->addCampo       ("dotacao", 8 );
$obPDF->addCampo       ("descricao", 8 );
$obPDF->setAlinhamento ( "R" );

$obPDF->addCampo("vl_ordinario", 8 );
$obPDF->addCampo("vl_vinculado", 8 );
$obPDF->addCampo("vl_total", 8 );

$obPDF->addRecordSet( $rsTotal );
$obPDF->setQuebraPaginaLista ( false );

$obPDF->setAlinhamento ( "C" );
$obPDF->addCabecalho("", 55, 0);
$obPDF->addCabecalho("", 15, 0);
$obPDF->addCabecalho("", 15, 0);
$obPDF->addCabecalho("", 15, 0);

$obPDF->setAlinhamento ( "R" );
$obPDF->addCampo("titulo", 8 );
$obPDF->addCampo("vl_ordinario", 8 );
$obPDF->addCampo("vl_vinculado", 8 );
$obPDF->addCampo("vl_total", 8 );

for ( $inCont = 0; $inCont < count( $arEntidade ); $inCont++ ) {
    $obPDF->addRecordSet( $arEntidade[$inCont] );
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho("", 40, 10);
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo("descricao", 8 );
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
