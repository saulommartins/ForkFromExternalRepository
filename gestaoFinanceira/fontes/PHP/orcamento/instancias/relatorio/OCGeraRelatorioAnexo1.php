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
    * Data de Criação   : 25/09/2004

    * @author Analista: Jorge Ribarr
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Lucas Leusin Oaigen

    * @ignore

    $Revision: 31801 $
    $Name$
    $Autor: $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.01.09
*/

include_once '../../../../../../config.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php"      );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoRelatorioAnexo1.class.php" );

$obRRelatorio = new RRelatorio;
$obPDF        = new ListaPDF("L");
$rsVazio      = new RecordSet;
$obRegra      = new ROrcamentoRelatorioAnexo1;

// Adicionar logo nos relatorios
$arFiltro = Sessao::read('filtroRelatorio');
$arNomFiltro = Sessao::read('filtroNomRelatorio');

$entidades = explode(",", $arFiltro['inCodEntidade']);

if ( count($entidades) == 1 ) {
    $obRRelatorio->setCodigoEntidade( $arFiltro['inCodEntidade'][0] );
    $obRRelatorio->setExercicioEntidade ( Sessao::getExercicio() );
}

$obRRelatorio->setExercicio     ( Sessao::getExercicio() );

$obRRelatorio->recuperaCabecalho( $arConfiguracao );

$obPDF->setModulo            ( "Orcamento Geral" );
$obPDF->setComplementoAcao   ( "- Demonstr. Receita e Despesa Segundo Cat. Economicas" );
$obPDF->setSubTitulo         ( "Anexo 1 - Exercício: ".Sessao::getExercicio() );
$obPDF->setUsuario           ( Sessao::getUsername() );
$obPDF->setEnderecoPrefeitura( $arConfiguracao );

$obPDF->addRecordSet( $rsVazio );
$obPDF->setAlinhamento ( "C" );
$obPDF->addCabecalho("RECEITA", 50, 10);
$obPDF->addCabecalho("DESPESA", 50, 10);
$obPDF->setAlinhamento ( "L" );
$obPDF->addCampo("", 8 );
$obPDF->addCampo("", 8 );

//Corrente
$arRecordSet = Sessao::read('arRecordSet');
$arRecordSet[0]->addFormatacao("ValorReceita", "NUMERIC_BR_NULL");
$arRecordSet[0]->addFormatacao("ValorDespesa", "NUMERIC_BR_NULL");
$obPDF->addRecordSet( $arRecordSet[0] );
$obPDF->setQuebraPaginaLista(false);
$obPDF->addCabecalho("", 40, 10);
$obPDF->addCabecalho("", 10, 10);
$obPDF->addCabecalho("", 40, 10);
$obPDF->addCabecalho("", 10, 10);
$obPDF->setAlinhamento ( "L" );
$obPDF->addCampo("DescricaoReceita", 8 );
$obPDF->setAlinhamento ( "R" );
$obPDF->addCampo("ValorReceita", 8 );
$obPDF->setAlinhamento ( "L" );
$obPDF->addCampo("DescricaoDespesa", 8 );
$obPDF->setAlinhamento ( "R" );
$obPDF->addCampo("ValorDespesa", 8 );

$arRecordSet[1]->addFormatacao("ValorReceita", "NUMERIC_BR_NULL");
$arRecordSet[1]->addFormatacao("ValorDespesa", "NUMERIC_BR_NULL");
$obPDF->addRecordSet( $arRecordSet[1] );
$obPDF->setQuebraPaginaLista(false);
$obPDF->setAlturaCabecalho( -1 );
$obPDF->addCabecalho("", 1, 10);
$obPDF->addCabecalho("", 38, 10);
$obPDF->addCabecalho("", 10, 10);
$obPDF->addCabecalho("", 2, 10);
$obPDF->addCabecalho("", 38, 10);
$obPDF->addCabecalho("", 10, 10);
$obPDF->addCabecalho("", 1, 10);
$obPDF->setAlinhamento ( "L" );
$obPDF->addCampo("", 8 );
$obPDF->addCampo("DescricaoReceita", 8 );
$obPDF->setAlinhamento ( "R" );
$obPDF->addCampo("ValorReceita", 8 );
$obPDF->setAlinhamento ( "L" );
$obPDF->addCampo("", 8 );
$obPDF->addCampo("DescricaoDespesa", 8 );
$obPDF->setAlinhamento ( "R" );
$obPDF->addCampo("ValorDespesa", 8 );
$obPDF->addCampo("", 8 );

//Capital
for ($inCount=2; $inCount<=3; $inCount++) {
    $arRecordSet[$inCount]->addFormatacao("1", "NUMERIC_BR_NULL");
    $arRecordSet[$inCount]->addFormatacao("3", "NUMERIC_BR_NULL");
    $arRecordSet[$inCount]->addFormatacao("ValorReceita", "NUMERIC_BR_NULL");
    $arRecordSet[$inCount]->addFormatacao("ValorDespesa", "NUMERIC_BR_NULL");
    $obPDF->addRecordSet( $arRecordSet[$inCount] );
    $obPDF->setQuebraPaginaLista(false);
    $obPDF->setAlinhamento ( "L" );

    $obPDF->addCabecalho("", 1, 10);
    $obPDF->addCabecalho("", 38, 10);
    $obPDF->addCabecalho("", 10, 10);
    $obPDF->addCabecalho("", 2, 10);
    $obPDF->addCabecalho("", 38, 10);
    $obPDF->addCabecalho("", 10, 10);
    $obPDF->addCabecalho("", 1, 10);

    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo("", 8 );
    $obPDF->addCampo("DescricaoReceita", 8 );
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCampo("ValorReceita", 8 );
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo("", 8 );
    $obPDF->addCampo("DescricaoDespesa", 8 );
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCampo("ValorDespesa", 8 );
    $obPDF->addCampo("", 8 );
}

$arRecordSet[4]->addFormatacao("RECEITAS", "NUMERIC_BR_NULL");
$arRecordSet[4]->addFormatacao("DESPESAS", "NUMERIC_BR_NULL");
$obPDF->addRecordSet( $arRecordSet[4] );

$obPDF->addCabecalho("", 1,  10);
$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho("RESUMO", 50, 10);
$obPDF->addCabecalho("", 1,  10);
$obPDF->setAlinhamento ( "R" );
$obPDF->addCabecalho("RECEITAS", 22, 10);
$obPDF->addCabecalho("", 1,  10);
$obPDF->setAlinhamento ( "R" );
$obPDF->addCabecalho("DESPESAS", 22, 10);

$obPDF->setAlinhamento ( "L" );
$obPDF->addCampo("", 8 );
$obPDF->addCampo("RESUMO", 8 );
$obPDF->setAlinhamento ( "R" );
$obPDF->addCampo("", 8 );
$obPDF->setAlinhamento ( "R" );
$obPDF->addCampo("RECEITAS", 8 );
$obPDF->addCampo("", 8 );
$obPDF->setAlinhamento ( "R" );
$obPDF->addCampo("DESPESAS", 8 );

$obRegra->obROrcamentoDespesa->obROrcamentoEntidade->setExercicio( Sessao::getExercicio() );
$inEntidades = str_replace("'","",$arFiltro['inCodEntidade'] );
$arEntidades = explode(",",$inEntidades );

foreach ($arEntidades as $key => $inCodEntidade) {
    $obRegra->obROrcamentoDespesa->obROrcamentoEntidade->setCodigoEntidade( $inCodEntidade );
    $obRegra->obROrcamentoDespesa->obROrcamentoEntidade->consultarNomes($rsLista);

    if($key==0)
        $obPDF->addFiltro( "Entidades relacionadas:", $rsLista->getCampo("entidade") );
    else
        $obPDF->addFiltro( ""                 , $rsLista->getCampo("entidade") );
}

if ($arFiltro['inCodDemValores']==1) {
    $obPDF->addFiltro( "Demonstrar Valores:", "Orçamento" );
} else {
    $obPDF->addFiltro( "Demonstrar Valores:", "Balanço" );

    if($arFiltro['inCodDemDespesa']==1)
        $obPDF->addFiltro( "Demonstrar Despesa:", "Empenhada" );
    else if($arFiltro['inCodDemDespesa']==2)
        $obPDF->addFiltro( "Demonstrar Despesa:", "Liquidada" );
    else
        $obPDF->addFiltro( "Demonstrar Despesa:", "Paga" );

    if($arFiltro['inNumOrgao'])
        $obPDF->addFiltro( 'Órgão'    , $arFiltro['inNumOrgao'] . " - " . $arNomFiltro['orgao'][$arFiltro[ 'inNumOrgao' ]] );
    if($arFiltro['inNumUnidade'])
        $obPDF->addFiltro( 'Unidade'  , $arFiltro['inNumUnidade'] . " - " . $arNomFiltro['unidade'][$arFiltro[ 'inNumUnidade' ]] );
}

$obPDF->addFiltro( "Data Inicial:", $arFiltro['stDataInicial'] );
$obPDF->addFiltro( "Data Final:", $arFiltro['stDataFinal'] );

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
    $obPDF->addCabecalho("NOTAS EXPLICATIVAS", 60, 10);
    $obPDF->addCabecalho("", 1,  10);
    $obPDF->addCabecalho("", 20, 10);
    $obPDF->addCabecalho("", 1,  10);
    $obPDF->addCabecalho("", 20, 10);

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