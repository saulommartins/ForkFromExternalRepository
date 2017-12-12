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

    * @author Desenvovledor: Eduardo Martins
    * @author Desenvolvedor: Gustavo Tourinho

    * @ignore

    $Revision: 31801 $
    $Name$
    $Author: cleisson $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.01.11
*/

include_once '../../../../../../config.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php"                  );

$obRRelatorio = new RRelatorio;
$obPDF        = new ListaPDF("L");

if (Sessao::read('stSituacao') == "empenhados") {
    $stSituacao = "Empenhado";
} elseif (Sessao::read('stSituacao') == "pagos") {
    $stSituacao = "Pago";
} elseif (Sessao::read('stSituacao') == "liquidados") {
    $stSituacao = "Liquidado";
}
$stSituacao = "Vlr. " . $stSituacao;

// Adicionar logo nos relatorios
$arFiltro = Sessao::read('filtroRelatorio');
if ( count( $arFiltro['inCodEntidade'] ) == 1 ) {
    $obRRelatorio->setCodigoEntidade( $arFiltro['inCodEntidade'][0] );
    $obRRelatorio->setExercicioEntidade ( Sessao::getExercicio() );
}

$obRRelatorio->setExercicio  ( Sessao::getExercicio() );
$obRRelatorio->recuperaCabecalho( $arConfiguracao );
$obPDF->setModulo            ( "Orçamento Geral" );
$obPDF->setComplementoAcao   ( "- Natureza da Despesa por Orgão" );

if ($arFiltro['stDemonstrarValores'] == "balanco") {
    $obPDF->setSubTitulo         ( "Anexo 2 Balanco - " . Sessao::read('stDataInicial') . " até " . Sessao::read('stDataFinal') . " - " . $stSituacao);
} else {
    $obPDF->setSubTitulo         ( "Anexo 2 Orcamento - ". Sessao::getExercicio() );
}

$obPDF->setUsuario           ( Sessao::getUsername() );
$obPDF->setEnderecoPrefeitura( $arConfiguracao );

$arTodos = array();
$arTodos = Sessao::read('rsAnexo2');

foreach ($arTodos as $indice) {

    $rsAnexo2 = new RecordSet;
    $rsAnexo2 = $indice;
    $rsAnexo2->setPrimeiroElemento();
    // RecordSet para Relatorio
    $obPDF->addRecordSet( $rsAnexo2 );
    $obPDF->setAlturaCabecalho(8);

    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho("", 2, 0);
    $obPDF->addCabecalho("", 13,0);
    $obPDF->addCabecalho("", 40,0);
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCabecalho("",15, 0);
    $obPDF->addCabecalho("",15, 0);
    $obPDF->addCabecalho("", 15, 0);

    $obPDF->addIndentacao("alinhamento","descricao","   ");
    $obPDF->addQuebraLinha( "nivel", 1, 2 );
    $obPDF->addQuebraLinha( "nivel", 2, 2 );
    $obPDF->addQuebraProximaLinha( "nivel", 1, 2 );

    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCampo("num_orgao",8);
    $obPDF->addCampo("classificacao", 8 );
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo("descricao", 8 );
    $obPDF->setAlinhamento ( "R" );

    $obPDF->addCampo("valor_d", 8 );
    $obPDF->addCampo("valor_e", 8 );
    $obPDF->addCampo("valor_c", 8 );
}

//total
$rsTotal = Sessao::read('rsTotal');
$obPDF->addRecordSet( $rsTotal );
$obPDF->setQuebraPaginaLista( false );
$obPDF->setAlturaCabecalho(0);

$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho("", 55, 0);
$obPDF->addCabecalho("", 15, 0);
$obPDF->addCabecalho("", 30, 0);

$obPDF->setAlinhamento ( "R" );
$obPDF->addCampo("descricao",8);
$obPDF->setAlinhamento ( "R" );
$obPDF->addCampo("valor", 8 );

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

$arAssinaturas = Sessao::read('assinaturas');
if ( count($arAssinaturas['selecionadas']) > 0 ) {
    include_once( CAM_FW_PDF."RAssinaturas.class.php" );
    $obRAssinaturas = new RAssinaturas;
    $obRAssinaturas->setArAssinaturas( $arAssinaturas['selecionadas'] );
    $obPDF->setAssinaturasDefinidas( $obRAssinaturas->getArAssinaturas() );
}

$obPDF->show();
?>
