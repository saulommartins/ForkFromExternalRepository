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
    * Data de Criação   : 25/09/2004

    * @author Desenvolvedor: Eduardo Martins

    * @ignore

    $Revision: 31801 $
    $Name$
    $Autor: $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.01.13
*/

include_once '../../../../../../config.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php"                  );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoRelatorioAnexo6.class.php"        );

$obRRelatorio = new RRelatorio;
$obPDF        = new ListaPDF("L");
$stSituacao = "";

if (Sessao::read('stSituacao')== "empenhados") {
    $stSituacao = " Valores - Empenhados";
} elseif (Sessao::read('stSituacao') == "pagos") {
    $stSituacao = " Valores - Pagos";
} elseif (Sessao::read('stSituacao') == "liquidados") {
    $stSituacao = " Valores - Liquidados";
}

$arFiltro = Sessao::read('filtroRelatorio');
// Adicionar logo nos relatorios
if ( count( $arFiltro['inCodEntidade'] ) == 1 ) {
    $obRRelatorio->setCodigoEntidade( $arFiltro['inCodEntidade'][0] );
    $obRRelatorio->setExercicioEntidade ( Sessao::getExercicio() );
}

$obRRelatorio->setExercicio  ( Sessao::getExercicio() );
$obRRelatorio->recuperaCabecalho( $arConfiguracao );
$obPDF->setModulo            ( "Orçamento Geral" );
if( $arFiltro['stAgrupamento'] == "orgao" )
    $arConfiguracao['nom_acao'] = "Anexo 6 - Programa de Trabalho por Órgão";
elseif( $arFiltro['stAgrupamento'] == "orgao_unidade" )
    $arConfiguracao['nom_acao'] = "Anexo 6 - Programa de Trabalho por Órgão e Unidade";

$obPDF->setSubTitulo         ( "Anexo 6 - Exercício: ".Sessao::getExercicio() . $stSituacao);
$obPDF->setUsuario           ( Sessao::getUsername() );
$obPDF->setEnderecoPrefeitura( $arConfiguracao );
$inContL = 0;

$arCabecalho = Sessao::read('arCabecalho');
$arAnexo6 = Sessao::read('arAnexo6');
$rsTotal = Sessao::read('rsTotal');
$arEntidade = Sessao::read('arEntidade');
for ( $inCont = 0; $inCont < count( $arCabecalho ); $inCont++ ) {
    //cabeçalho
    $obPDF->addRecordSet( $arCabecalho[$inCont] );
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho("", 5, 10);
    $obPDF->addCabecalho("", 40, 10);
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo("classificacao", 8 );
    $obPDF->addCampo("descricao", 8 );

    //Registros
    $arAnexo6[$inContL]->addFormatacao( "vl_projeto"   , "NUMERIC_BR" );
    $arAnexo6[$inContL]->addFormatacao( "vl_atividade" , "NUMERIC_BR" );
    $arAnexo6[$inContL]->addFormatacao( "vl_operacao"  , "NUMERIC_BR" );
    $arAnexo6[$inContL]->addFormatacao( "vl_total"     , "NUMERIC_BR" );
    $obPDF->addRecordSet( $arAnexo6[$inContL] );
    $obPDF->setAlturaCabecalho(8);
    $obPDF->setQuebraPaginaLista(false);
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho("CÓDIGO"         ,20, 10);
    $obPDF->addCabecalho("ESPECIFICACAO"  ,40, 10);
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCabecalho("PROJETOS"       ,10, 10);
    $obPDF->addCabecalho("ATIVIDADES"     ,10, 10);
    $obPDF->addCabecalho("OPERAC. ESPEC." ,10, 10);
    $obPDF->addCabecalho("TOTAL"          ,10, 10);

    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo("dotacao", 8 );
    $obPDF->addQuebraLinha("quebra",true,5);
    $obPDF->addIndentacao("alinhamento", "descricao", "    " );
    $obPDF->addCampo("descricao", 8 );
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCampo("vl_projeto"  , 8 );
    $obPDF->addCampo("vl_atividade", 8 );
    $obPDF->addCampo("vl_operacao" , 8 );
    $obPDF->addCampo("vl_total"    , 8 );

    $arAnexo6[$inContL + 1]->addFormatacao( "vl_projeto"   , "NUMERIC_BR" );
    $arAnexo6[$inContL + 1]->addFormatacao( "vl_atividade" , "NUMERIC_BR" );
    $arAnexo6[$inContL + 1]->addFormatacao( "vl_operacao"  , "NUMERIC_BR" );
    $arAnexo6[$inContL + 1]->addFormatacao( "vl_total"     , "NUMERIC_BR" );

    $obPDF->addRecordSet( $arAnexo6[$inContL + 1] );
    $inContL += 2;
    $obPDF->setAlturaCabecalho(0);
    $obPDF->setQuebraPaginaLista(false);
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCabecalho("" ,60, 10);
    $obPDF->addCabecalho("" ,10, 10);
    $obPDF->addCabecalho("" ,10, 10);
    $obPDF->addCabecalho("" ,10, 10);
    $obPDF->addCabecalho("" ,10, 10);
    $obPDF->addCampo("descricao", 8 );
    $obPDF->addCampo("vl_projeto"  , 8 );
    $obPDF->addCampo("vl_atividade", 8 );
    $obPDF->addCampo("vl_operacao" , 8 );
    $obPDF->addCampo("vl_total"    , 8 );
}

$rsTotal->addFormatacao( "vl_projeto"   , "NUMERIC_BR" );
$rsTotal->addFormatacao( "vl_atividade" , "NUMERIC_BR" );
$rsTotal->addFormatacao( "vl_operacao"  , "NUMERIC_BR" );
$rsTotal->addFormatacao( "vl_total"     , "NUMERIC_BR" );
$obPDF->addRecordSet( $rsTotal );

$obPDF->setAlturaCabecalho(0);
$obPDF->setQuebraPaginaLista(false);
$obPDF->setAlinhamento ( "R" );
$obPDF->addCabecalho("" ,60, 10);
$obPDF->addCabecalho("" ,10, 10);
$obPDF->addCabecalho("" ,10, 10);
$obPDF->addCabecalho("" ,10, 10);
$obPDF->addCabecalho("" ,10, 10);
$obPDF->addCampo("descricao", 8 );
$obPDF->addCampo("vl_projeto"  , 8 );
$obPDF->addCampo("vl_atividade", 8 );
$obPDF->addCampo("vl_operacao" , 8 );
$obPDF->addCampo("vl_total"    , 8 );

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
    $obPDF->addCabecalho("", 1,  10);
    $obPDF->addCabecalho("", 1, 10);

    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo("", 8 );
    $obPDF->addCampo("nota", 8 );
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
