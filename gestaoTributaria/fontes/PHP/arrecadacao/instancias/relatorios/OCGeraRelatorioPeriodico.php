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
    * Página de processamento oculto e geração do relatório Periódico
    * Data de Criação   : 22/05/2007

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Diego Bueno Coelho

    * @ignore

    * $Id: OCGeraRelatorioPeriodico.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.03.23
*/

/*
$Log$
Revision 1.2  2007/05/30 13:01:35  dibueno
Bug #9279#

Revision 1.1  2007/05/23 19:34:52  dibueno
Bug #9279#

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_FW_PDF."RRelatorio.class.php" );
include_once ( CAM_FW_PDF."ListaPDF.class.php" );
#include_once ( CAM_GT_ARR_NEGOCIO."RARRPagamento.class.php" );
#include_once ( CAM_GT_ARR_MAPEAMENTO."TARRPagamento.class.php" );
include_once ( CAM_GT_ARR_NEGOCIO."RARRRelatorioValoresLancados.class.php" );

;

$obRRelatorio = new RRelatorio;
$obPDF        = new ListaPDF("L");

$obRRelatorio->setExercicio  ( Sessao::getExercicio() );
$obRRelatorio->recuperaCabecalho( $arConfiguracao );

$obPDF->setModulo            ( "Arrecadação:"   );
$obPDF->setTitulo            ( "Créditos:"      );
$obPDF->setSubTitulo         ( "Exercício - ".Sessao::getExercicio() );
$obPDF->setUsuario           ( Sessao::getUsername() );
$obPDF->setEnderecoPrefeitura( $arConfiguracao );

#echo $filtro;

$arFiltro = Sessao::read( 'filtroRelatorio' );
$rsRecordSet = Sessao::read( 'sessao_transf4' );

#=====================================================================================
$flValorLancado = $flValorPago = $flValorAbertoVencido = $flValorAbertoAVencer = 0.00;

while ( !$rsRecordSet->eof() ) {

    $flValorLancado         += $rsRecordSet->getCampo('lancado');
    $flValorPago            += $rsRecordSet->getCampo('pago');
    $flValorAbertoVencido   += $rsRecordSet->getCampo('aberto_vencido');
    $flValorAbertoAVencer   += $rsRecordSet->getCampo('aberto_a_vencer');

    $rsRecordSet->proximo();

}

if ($arFiltro['dtInicio'] && $arFiltro['dtFinal']) {
    $stTituloRelatorio = "RELATÓRIO PERIÓDICO DE ".$arFiltro['dtInicio']." A ".$arFiltro['dtFinal'];
} else {
    $stTituloRelatorio = "RELATÓRIO PERIÓDICO DE ".$arFiltro['dtInicio'].$arFiltro['dtFinal']." A ".$arFiltro['dtInicio'].$arFiltro['dtFinal'];
}

//titulo
$arTitulo1 = array("tit" => $stTituloRelatorio );

$rsTit1 = new Recordset;
$rsTit1->preenche($arTitulo1);
$rsTit1->setPrimeiroElemento();

$obPDF->addRecordSet( $rsTit1 );
$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho   ( $stTituloRelatorio  ,100, 13, "B" );

$rsRecordSet->setPrimeiroElemento();
$rsRecordSet->addFormatacao ('lancado'          , 'NUMERIC_BR');
$rsRecordSet->addFormatacao ('pago'             , 'NUMERIC_BR');
$rsRecordSet->addFormatacao ('aberto_vencido'   , 'NUMERIC_BR');
$rsRecordSet->addFormatacao ('aberto_a_vencer'  , 'NUMERIC_BR');
if ($arFiltro['stTipoRelatorio'] == "analitico") {
//cass    $rsRecordSet->ordena( "numcgm" );
    $rsRecordSet->ordena( "cgm" );
}

$rsRecordSet->setPrimeiroElemento();

if ($arFiltro['stTipoRelatorio'] == "analitico") {
//cass    $inNumCGMAtual = $rsRecordSet->getCampo("numcgm");
    $inNumCGMAtual = $rsRecordSet->getCampo("cgm");
    $arTMPDados = array();
    while ( !$rsRecordSet->Eof() ) {
//cass        if ( $inNumCGMAtual != $rsRecordSet->getCampo("numcgm") ) {
//cass            $inNumCGMAtual = $rsRecordSet->getCampo("numcgm");

        if ( $inNumCGMAtual != $rsRecordSet->getCampo("cgm") ) {
            $inNumCGMAtual = $rsRecordSet->getCampo("cgm");

            $arTitulo2 = array();
            //$arTitulo2[] = array( "titulo" => "TOTAL" );

            $rsTit2 = new Recordset;
            $rsTit2->preenche($arTitulo2);
            $rsTit2->setPrimeiroElemento();

            $obPDF->addRecordSet( $rsTit2 );
            $obPDF->setQuebraPaginaLista( false );

            $obPDF->setAlinhamento ( "L" );
//CASS            $obPDF->addCabecalho   ( "CGM: ".$arTMPDados[0]["numcgm"]." - ".$arTMPDados[0]["nome_cgm"], 125, 11, "B" );
            $obPDF->addCabecalho   ( "CGM: ".$arTMPDados[0]["cgm"], 125, 11, "B" );

            $obPDF->setAlinhamento ( "R" );
            $obPDF->addCampo   ( "" ,10, "B" );

            unset( $rsDados );
            $rsDados = new RecordSet;
            for ( $inX=0; $inX<3; $inX++ )
                $arTMPDados[] = array (
                    "descricao" => " ",
//cass                    "numcgm" => " ",
                    "lancado" => " ",
//cass                    "nome_cgm" => " ",
                    "cgm" => " ",
                    "pago" => " ",
                    "aberto_vencido" => " ",
                    "aberto_a_vencer" => " "
                );

            $rsDados->preenche( $arTMPDados );

            $obPDF->addRecordSet( $rsDados );
            $obPDF->setQuebraPaginaLista( false );

            $obPDF->setAlinhamento ( "L" );
            $obPDF->addCabecalho   ( "Crédito/Grupo"    ,35, 12, "B" );
            $obPDF->setAlinhamento ( "R" );
            $obPDF->addCabecalho   ( "Valor"            ,15, 12, "B" );
            $obPDF->addCabecalho   ( "Total Recebido"   ,15, 12, "B" );
            $obPDF->addCabecalho   ( "Total Vencido"    ,15, 12, "B" );
            $obPDF->addCabecalho   ( "Total a Vencer"  ,15, 12, "B" );

            $obPDF->setAlinhamento ( "L" );
            $obPDF->addCampo       ( "descricao"        ,10 , "B" );
            $obPDF->setAlinhamento ( "R" );
            $obPDF->addCampo       ( "lancado"          , 9 );
            $obPDF->addCampo       ( "pago"             , 9 );
            $obPDF->addCampo       ( "aberto_vencido"   , 9 );
            $obPDF->addCampo       ( "aberto_a_vencer"  , 9 );

            unset( $arTMPDados );
            $arTMPDados = array();
        } else {
            $arTMPDados[] = array (
                "descricao" => $rsRecordSet->getCampo("descricao"),
//cass                "numcgm" => $rsRecordSet->getCampo("numcgm"),
                "lancado" => $rsRecordSet->getCampo("lancado"),
//cass                "nome_cgm" => $rsRecordSet->getCampo("nome_cgm"),
                "cgm" => $rsRecordSet->getCampo("cgm"),
                "pago" => $rsRecordSet->getCampo("pago"),
                "aberto_vencido" => $rsRecordSet->getCampo("aberto_vencido"),
                "aberto_a_vencer" => $rsRecordSet->getCampo("aberto_a_vencer")
            );
            $rsRecordSet->proximo();
        }
    }

    if ( count( $arTMPDados ) > 0 ) {
        $arTitulo2 = array();
//        $arTitulo2[] = array( "titulo" => "TOTAL" );

        $rsTit2 = new Recordset;
        $rsTit2->preenche($arTitulo2);
        $rsTit2->setPrimeiroElemento();

        $obPDF->addRecordSet( $rsTit2 );
        $obPDF->setQuebraPaginaLista( false );

        $obPDF->setAlinhamento ( "L" );
//cass        $obPDF->addCabecalho   ( "CGM: ".$arTMPDados[0]["numcgm"]." - ".$arTMPDados[0]["nome_cgm"], 125, 11, "B" );

        $obPDF->addCabecalho   ( "CGM: ".$arTMPDados[0]["cgm"], 125, 11, "B" );

        $obPDF->setAlinhamento ( "R" );
        $obPDF->addCampo   ( "" ,10, "B" );

        unset( $rsDados );
        $rsDados = new RecordSet;
        for ( $inX=0; $inX<3; $inX++ )
            $arTMPDados[] = array (
                "descricao" => " ",
//cass                "numcgm" => " ",
                "lancado" => " ",
//cass                "nome_cgm" => " ",
                "cgm" => " ",
                "pago" => " ",
                "aberto_vencido" => " ",
                "aberto_a_vencer" => " "
            );

        $rsDados->preenche( $arTMPDados );

        $obPDF->addRecordSet( $rsDados );
        $obPDF->setQuebraPaginaLista( false );

        $obPDF->setAlinhamento ( "L" );
        $obPDF->addCabecalho   ( "Crédito/Grupo"    ,35, 12, "B" );
        $obPDF->setAlinhamento ( "R" );
        $obPDF->addCabecalho   ( "Valor"            ,15, 12, "B" );
        $obPDF->addCabecalho   ( "Total Recebido"   ,15, 12, "B" );
        $obPDF->addCabecalho   ( "Total Vencido"    ,15, 12, "B" );
        $obPDF->addCabecalho   ( "Total a Vencer"  ,15, 12, "B" );

        $obPDF->setAlinhamento ( "L" );
        $obPDF->addCampo       ( "descricao"        ,10 , "B" );
        $obPDF->setAlinhamento ( "R" );
        $obPDF->addCampo       ( "lancado"          , 9 );
        $obPDF->addCampo       ( "pago"             , 9 );
        $obPDF->addCampo       ( "aberto_vencido"   , 9 );
        $obPDF->addCampo       ( "aberto_a_vencer"  , 9 );

        unset( $arTMPDados );
    }
} else { //sintatico
    $obPDF->addRecordSet( $rsRecordSet );
    $obPDF->setQuebraPaginaLista( false );

    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho   ( "Crédito/Grupo"    ,35, 12, "B" );
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCabecalho   ( "Valor"            ,15, 12, "B" );
    $obPDF->addCabecalho   ( "Total Recebido"   ,15, 12, "B" );
    $obPDF->addCabecalho   ( "Total Vencido"    ,15, 12, "B" );
    $obPDF->addCabecalho   ( "Total a Vencer"  ,15, 12, "B" );

    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo       ( "descricao"        ,10 , "B" );
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCampo       ( "lancado"          , 9 );
    $obPDF->addCampo       ( "pago"             , 9 );
    $obPDF->addCampo       ( "aberto_vencido"   , 9 );
    $obPDF->addCampo       ( "aberto_a_vencer"  , 9 );
}

//TOTAL GERAL
$arTitulo2 = array();
$arTitulo2[] = array( "titulo" => "TOTAL" );
$rsTit2 = new Recordset;
$rsTit2->preenche($arTitulo2);
$rsTit2->setPrimeiroElemento();
$obPDF->addRecordSet( $rsTit2 );
$obPDF->setQuebraPaginaLista( false );

$obPDF->setAlinhamento ( "R" );
$obPDF->addCabecalho   ( "Totais:" ,35, 11, "B" );
$obPDF->addCabecalho   ( number_format ( $flValorLancado        , 2, ',', '.' )  ,15, 11, "B" );
$obPDF->addCabecalho   ( number_format ( $flValorPago           , 2, ',', '.' ) ,15, 11, "B" );
$obPDF->addCabecalho   ( number_format ( $flValorAbertoVencido  , 2, ',', '.' ) ,15, 11, "B" );
$obPDF->addCabecalho   ( number_format ( $flValorAbertoAVencer  , 2, ',', '.' ) ,15, 11, "B" );

$obPDF->setAlinhamento ( "R" );
$obPDF->addCampo   ( "" ,10, "B" );
$obPDF->addCampo   ( "" ,10, "B" );
$obPDF->addCampo   ( "" ,10, "B" );
$obPDF->addCampo   ( "" ,10, "B" );
$obPDF->addCampo   ( "" ,10, "B" );

$obPDF->addFiltro( 'Grupo de Crédito Inicial', $arFiltro['inCodGrupoInicio'] );
$obPDF->addFiltro( 'Grupo de Crédito Final', $arFiltro['inCodGrupoTermino'] );
$obPDF->addFiltro( 'Crédito Inicial', $arFiltro['inCodCreditoInicio'] );
$obPDF->addFiltro( 'Crédito Final', $arFiltro['inCodCreditoTermino'] );
$obPDF->addFiltro( 'Contribuinte Inicial', $arFiltro['inCodContribuinteInicial'] );
$obPDF->addFiltro( 'Contribuinte Final', $arFiltro['inCodContribuinteFinal'] );
$obPDF->addFiltro( 'Inscrição Imobiliária Inicial', $arFiltro['inNumInscricaoImobiliariaInicial'] );
$obPDF->addFiltro( 'Inscrição Imobiliária Final', $arFiltro['inNumInscricaoImobiliariaFinal'] );
$obPDF->addFiltro( 'Inscrição Econômica Inicial', $arFiltro['inNumInscricaoEconomicaInicial'] );
$obPDF->addFiltro( 'Inscrição Econômica Final', $arFiltro['inNumInscricaoEconomicaFinal'] );
$obPDF->addFiltro( 'Atividade Inicial', $arFiltro['inCodInicio'] );
$obPDF->addFiltro( 'Atividade Final', $arFiltro['inCodTermino'] );
$obPDF->addFiltro( 'Data Inicial', $arFiltro['dtInicio'] );
$obPDF->addFiltro( 'Data Final', $arFiltro['dtFinal'] );

$obPDF->show();

?>
