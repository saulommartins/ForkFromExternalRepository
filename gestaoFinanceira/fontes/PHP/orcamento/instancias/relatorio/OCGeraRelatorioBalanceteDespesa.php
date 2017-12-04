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

    * $Id: OCGeraRelatorioBalanceteDespesa.php 65648 2016-06-07 17:19:01Z franver $

    * Casos de uso: uc-02.01.22
*/

include_once '../../../../../../config.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_FW_PDF."RRelatorio.class.php";

$obRRelatorio = new RRelatorio;
$obPDF        = new ListaPDF( "L" );

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

if ($arFiltro['inAno']) {
   $obPDF->setModulo            ('Orçamento Geral'.'  -  '. $arFiltro['inAno']);
} else {
   $obPDF->setModulo            ( 'Orçamento Geral'.' - '.  Sessao::getExercicio() );
}
$obPDF->setTitulo            ( "Balancete de Despesa" );
$obPDF->setSubTitulo         ( $arFiltro['stDataInicial'] .' à '. $arFiltro['stDataFinal']);
$obPDF->setUsuario           ( Sessao::getUsername() );
$obPDF->setEnderecoPrefeitura( $arConfiguracao );

foreach ($arFiltro['inCodEntidade'] as $inCodEntidade) {
    $arNomEntidade[] = $arNomFiltro['entidade'][$inCodEntidade];
}

$obPDF->addFiltro( 'Entidades Relacionadas'             , $arNomEntidade                                               );
if($arFiltro['inNumOrgao'])
    $obPDF->addFiltro( 'Órgão Orçamentário'    , $arFiltro['inNumOrgao'] . " - " . $arNomFiltro['orgao'][$arFiltro[ 'inNumOrgao' ]] );
if($arFiltro['inNumUnidade'])
    $obPDF->addFiltro( 'Unidade Orçamentária'  , $arFiltro['inNumUnidade'] . " - " . $arNomFiltro['unidade'][$arFiltro[ 'inNumUnidade' ]] );

if ($arFiltro['stDataInicial']) {
    $obPDF->addFiltro( 'Periodicidade ', $arFiltro['stDataInicial']." até ".$arFiltro['stDataFinal'] );
}

if ($arFiltro['inCodFuncao']) {
    $obPDF->addFiltro( 'Função ', $arFiltro['inCodFuncao']." - ".$arNomFiltro['funcao'][$arFiltro['inCodFuncao']]);
}

if ($arFiltro['inCodSubFuncao']) {
    $obPDF->addFiltro( 'Subfunção ', $arFiltro['inCodSubFuncao']." - ".$arNomFiltro['subfuncao'][$arFiltro['inCodSubFuncao']]);
}

if ($arFiltro['inCodPrograma']) {
    $obPDF->addFiltro( 'Programa ', $arFiltro['inCodPrograma']." - ".$arNomFiltro['programa'][$arFiltro['inCodPrograma']] );
}

if ($arFiltro['inCodPao']) {
    $obPDF->addFiltro( 'PAO '    ,  $arFiltro['inCodPao']." - ".$arNomFiltro['pao'][$arFiltro['inCodPao']] );
}

if ($arFiltro['inCodDotacaoInicial']) {
    $obPDF->addFiltro( 'Código Reduzido Inicial ', $arFiltro['inCodDotacaoInicial'] );
}
if ($arFiltro['inCodDotacaoFinal']) {
    $obPDF->addFiltro( 'Código Reduzido Final ', $arFiltro['inCodDotacaoFinal'] );
}
if ($arFiltro['stCodEstruturalInicial']) {
    $obPDF->addFiltro( 'Código Estrutural Inicial ', $arFiltro['stCodEstruturalInicial'] );
}
if ($arFiltro['stCodEstruturalFinal']) {
    $obPDF->addFiltro( 'Código Estrutural Final ', $arFiltro['stCodEstruturalFinal'] );
}

if ($arFiltro['stDescricaoRecurso']) {
    $obPDF->addFiltro( 'Recurso ', $arFiltro['inCodRecurso']. " - " . $arFiltro['stDescricaoRecurso'] );
}

$obPDF->addFiltro( 'Observação ', "Os valores de 'SALDO INICIAL' é referente ao valor da Dotação. E o 'SALDO DISPONÍVEL', é o desconto dos lançamentos das contas de desdobramento filtradas." );

$rsCabecalho = Sessao::read('rsCabecalho');
$rsBalanceteDespesa = Sessao::read('rsBalanceteDespesa');

for ( $inCont = 0; $inCont < count( $rsCabecalho); $inCont++ ) {
    //cabeçalho
    $obPDF->addRecordSet( $rsCabecalho[$inCont] );
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho("", 5, 10);
    $obPDF->addCabecalho("", 25,10);
    $obPDF->addCampo("classificacao", 8 );
    $obPDF->addCampo("descricao", 8 );

    //Registros
    $obPDF->addRecordSet( $rsBalanceteDespesa[$inCont]);
    $obPDF->setQuebraPaginaLista(false);
    $obPDF->setAlturaCabecalho(3);
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho("DOTAÇÃO", 12,8);
    $obPDF->addCabecalho("",23, 8);
    $obPDF->setAlinhamento ( "R" );
    if ($arFiltro['inCodTipoEmissao'] == '2') {
       $obPDF->addCabecalho("SALDO INICIAL  EMPENHADO NO MÊS  EMPENHADO NO ANO",14,8);
       $obPDF->addCabecalho("SUPLEMENTAÇOES  ANULADO NO MÊS  ANULADO NO ANO ",12,8 );
       $obPDF->addCabecalho("REDUÇÕES  LIQUIDADO NO MÊS  LIQUIDADO NO ANO ",12, 8);
       $obPDF->addCabecalho("TOTAL CRÉDITO  PAGO NO MÊS  PAGO NO ANO ",11, 8);
    } else {
       $obPDF->addCabecalho("SALDO INICIAL  EMPENHADO NO PER  EMPENHADO ATÉ PER",14,8);
       $obPDF->addCabecalho("SUPLEMENTAÇOES  ANULADO NO PER  ANULADO ATÉ PER",12,8 );
       $obPDF->addCabecalho("REDUÇÕES  LIQUIDADO NO PER  LIQUIDADO ATÉ PER",12, 8);
       $obPDF->addCabecalho("TOTAL CRÉDITO  PAGO NO PER  PAGO ATÉ PER",11, 8);
    }
    $obPDF->addCabecalho("     SALDO DISPONÍVEL                       A LIQUIDAR    A PAGAR LÍQUIDADO ",13, 8);
    $obPDF->addIndentacao("nivel","[classificacao]  [descricao_despesa]","    ");
    $obPDF->addQuebraLinha("nivel",0,4);
    $obPDF->addQuebraPagina("pagina",1);

    $obPDF->setAlturaLinha(3);
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo("classificacao", 7 );
    $obPDF->addCampo("descricao_despesa", 7 );
    $obPDF->setAlinhamento ( "R" );

    $obPDF->setAlturaCabecalho(4);
    $obPDF->addCampo("coluna3", 7 );
    $obPDF->addCampo("coluna4", 7 );
    $obPDF->addCampo("coluna5", 7 );
    $obPDF->addCampo("coluna6", 7 );
    $obPDF->addCampo("coluna7", 7 );
}

if ($arFiltro['radResumoRecurso'] == 'N') {
    $rsTotal = Sessao::read('rsTotalFinal');
} else {
    $rsTotalFinal = Sessao::read('rsTotalFinal');
    $rsResumoRecurso = Sessao::read('rsResumoRecurso');
    $arTotal = array_merge($rsTotalFinal->getElementos(), $rsResumoRecurso->getElementos());

    $rsTotal = new RecordSet;
    $rsTotal->preenche($arTotal);
}

//monta linha do totalizador
$obPDF->addRecordSet($rsTotal);
$obPDF->setAlturaLinha(3);
$obPDF->setQuebraPaginaLista(true);
$obPDF->setAlturaCabecalho(5);
$obPDF->setAlinhamento("L");
$obPDF->addCabecalho("", 12, 8);
$obPDF->addCabecalho("",25, 8);
$obPDF->setAlinhamento("R");
if ($arFiltro['inCodTipoEmissao'] == '2') {
    $obPDF->addCabecalho("SALDO INICIAL  EMPENHADO NO MÊS  EMPENHADO NO ANO",12,8);
    $obPDF->addCabecalho("SUPLEMENTAÇOES  ANULADO NO MÊS  ANULADO NO ANO ",12,8 );
    $obPDF->addCabecalho("REDUÇÕES  LIQUIDADO NO MÊS  LIQUIDADO NO ANO ",12, 8);
    $obPDF->addCabecalho("TOTAL CRÉDITO  PAGO NO MÊS  PAGO NO ANO ",11, 8);
} else {
    $obPDF->addCabecalho("SALDO INICIAL  EMPENHADO NO PER  EMPENHADO ATÉ PER",12,8);
    $obPDF->addCabecalho("SUPLEMENTAÇOES  ANULADO NO PER  ANULADO ATÉ PER",12,8 );
    $obPDF->addCabecalho("REDUÇÕES  LIQUIDADO NO PER  LIQUIDADO ATÉ PER",12, 8);
    $obPDF->addCabecalho("TOTAL CRÉDITO  PAGO NO PER  PAGO ATÉ PER",11, 8);
}
$obPDF->addCabecalho("SALDO DISPONÍVEL A LIQUIDAR A PAGAR LÍQUIDADO ",13, 8);
$obPDF->addIndentacao("nivel","[classificacao]  [descricao_despesa]","    ");
$obPDF->addQuebraLinha("nivel",0,5);
$obPDF->addQuebraPagina("pagina",1);
$obPDF->setAlinhamento("L");
$obPDF->addCampo("classificacao", 7);
$obPDF->addCampo("descricao_despesa", 7);
$obPDF->setAlinhamento("R");
$obPDF->addCampo("coluna3", 7);
$obPDF->addCampo("coluna4", 7);
$obPDF->addCampo("coluna5", 7);
$obPDF->addCampo("coluna6", 7);
$obPDF->addCampo("coluna7", 7);

$arAssinaturas = Sessao::read('assinaturas');
if ( count($arAssinaturas['selecionadas']) > 0 ) {
    include_once( CAM_FW_PDF."RAssinaturas.class.php" );
    $obRAssinaturas = new RAssinaturas;
    $obRAssinaturas->setArAssinaturas( $arAssinaturas['selecionadas'] );
    $obPDF->setAssinaturasDefinidas( $obRAssinaturas->getArAssinaturas() );
    //$obRAssinaturas->montaPDF( $obPDF );
}

$obPDF->show();
?>
