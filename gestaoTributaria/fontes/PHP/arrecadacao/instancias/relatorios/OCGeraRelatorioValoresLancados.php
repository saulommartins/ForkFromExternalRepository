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
    * Página de processamento oculto e geração do relatório para Valores Lançados
    * Data de Criação   : 23/03/2005

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Marcelo Boezio Paulino

    * @ignore

    * $Id: OCGeraRelatorioValoresLancados.php 61362 2015-01-12 11:22:07Z carolina $

    * Casos de uso: uc-05.01.13
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_ARR_NEGOCIO."RARRRelatorioValoresLancados.class.php" );
include_once ( CAM_FW_PDF."RRelatorio.class.php" );
include_once ( CAM_FW_PDF."ListaPDF.class.php" );

$obRRelatorio = new RRelatorio;
$obPDF        = new ListaPDF("L");

$obRRelatorio->setExercicio  ( Sessao::getExercicio() );
$obRRelatorio->recuperaCabecalho( $arConfiguracao );
$obPDF->setModulo            ( "Relatório:"   );
$obPDF->setTitulo            ( "Créditos:" );
$obPDF->setSubTitulo         ( "Exercício - ".Sessao::getExercicio() );
$obPDF->setUsuario           ( Sessao::getUsername() );
$obPDF->setEnderecoPrefeitura( $arConfiguracao );

$arFiltroRelatorio = Sessao::read( 'filtroRelatorio' );
$arSessaoTransf6 = Sessao::read( 'sessao_transf6' );
if (!$arSessaoTransf6[0]['erro']) {
    if ($arFiltroRelatorio['stTipoRelatorio'] == 'sintetico') {
        $arSessaoTransf5 = Sessao::read( 'sessao_transf5' );

        $arSessaoTransf5[0]->addFormatacao( "pago_vista"        , "NUMERIC_BR" );
        $arSessaoTransf5[0]->addFormatacao( "pago_juros"        , "NUMERIC_BR" );
        $arSessaoTransf5[0]->addFormatacao( "pago_multa"        , "NUMERIC_BR" );
        $arSessaoTransf5[0]->addFormatacao( "pago_correcao"     , "NUMERIC_BR" );
        $arSessaoTransf5[0]->addFormatacao( "total_pago"        , "NUMERIC_BR" );
        $arSessaoTransf5[0]->addFormatacao( "total_aberto"      , "NUMERIC_BR" );
        $arSessaoTransf5[0]->addFormatacao( "somatotal"         , "NUMERIC_BR" );

        #echo 'SINTETICO'; exit;
        $obPDF->addRecordSet( $arSessaoTransf5[0] );

        $obPDF->setAlinhamento ( "L" );
        $obPDF->addCabecalho   ( "ORIGEM"      ,20, 10, "B" );
        $obPDF->setAlinhamento ( "C" );
        $obPDF->addCabecalho   ( "ABERTO"       ,12, 10, "B" );
        $obPDF->addCabecalho   ( "PAGO"         ,12, 10, "B" );
        $obPDF->addCabecalho   ( "JUROS"        ,10, 10, "B" );
        $obPDF->addCabecalho   ( "MULTA"        ,10, 10, "B" );
        $obPDF->addCabecalho   ( "CORREÇÃO"     ,10, 10, "B" );
        $obPDF->addCabecalho   ( "DIFERENÇA"    ,10, 10, "B" );
        $obPDF->addCabecalho   ( "TOTAL PAGO"   ,12, 10, "B" );

        #$obPDF->addCabecalho   ( "TOTAL"   ,12, 10, "B" );

        $obPDF->setAlinhamento ( "L" );
        $obPDF->addCampo       ( "origem" , 8);
        $obPDF->setAlinhamento ( "R" );
        $obPDF->addCampo       ( "soma_aberto"    , 8 );
        $obPDF->addCampo       ( "pagamento_valor" , 8 );
        $obPDF->addCampo       ( "juros_pago"    , 8 );
        $obPDF->addCampo       ( "multa_pago"    , 8 );
        $obPDF->addCampo       ( "correcao_pago" , 8 );
        $obPDF->addCampo       ( "diferenca_real" , 8 );
        $obPDF->addCampo       ( "soma_pago"    , 8 );

        #$obPDF->addCampo       ( "somatotal"    , 8 );

        #echo '<b>TRANSF 1</b>';
        $obPDF->addRecordSet( $arSessaoTransf5[1] );
        $obPDF->setQuebraPaginaLista( false );

        $obPDF->setAlinhamento ( "L" );
        $obPDF->addCabecalho   ( "" ,20, 10 );
        $obPDF->addCabecalho   ( "" ,12, 10 );
        $obPDF->addCabecalho   ( "" ,12, 10 );
        $obPDF->addCabecalho   ( "" ,10, 10 );
        $obPDF->addCabecalho   ( "" ,10, 10 );
        $obPDF->addCabecalho   ( "" ,10, 10 );
        $obPDF->addCabecalho   ( "" ,10, 10 );
        $obPDF->addCabecalho   ( "" ,12, 10 );

        $obPDF->setAlinhamento ( "R" );
        $obPDF->addCampo       ( "st_total" , 9, "B" );
        $obPDF->setAlinhamento ( "R" );
        $obPDF->addCampo       ( "soma_aberto"      , 9, "B" );
        $obPDF->addCampo       ( "pagamento_valor"  , 9, "B" );
        $obPDF->addCampo       ( "juros_pago"       , 9, "B" );
        $obPDF->addCampo       ( "multa_pago"       , 9, "B" );
        $obPDF->addCampo       ( "correcao_pago"    , 9, "B" );
        $obPDF->addCampo       ( "diferenca_real"   , 9, "B" );
        $obPDF->addCampo       ( "soma_pago"        , 9, "B" );

        if ($arFiltroRelatorio['inCodCreditoInicio'] AND $arFiltroRelatorio['inCodCreditoTermino']) {
            $obPDF->addFiltro( 'Créditos' , $arFiltroRelatorio['inCodCreditoInicio'].' - '.$arFiltroRelatorio['inCodCreditoTermino'] );
        } elseif ($arFiltroRelatorio['inCodCreditoInicio'] AND !$arFiltroRelatorio['inCodCreditoTermino']) {
            $obPDF->addFiltro( 'Créditos' , 'À partir do '.$arFiltroRelatorio['inCodCreditoInicio'] );
        } elseif (!$arFiltroRelatorio['inCodCreditoInicio'] AND $arFiltroRelatorio['inCodCreditoTermino']) {
            $obPDF->addFiltro( 'Créditos' , 'Até o ' . $arFiltroRelatorio['inCodCreditoTermino'] );
        }
        $obPDF->addFiltro( 'Exercicio', $arFiltroRelatorio['inExercicio'] );

        if ($arFiltroRelatorio['inCodCondominioInicial'] || $arFiltroRelatorio['inCodCondominioFinal']) {
            if ($arFiltroRelatorio['inCodCondominioInicial'] && $arFiltroRelatorio['inCodCondominioFinal']) {
                $obPDF->addFiltro( 'Condomínios' , $arFiltroRelatorio['inCodCondominioInicial'].' ao '.$arFiltroRelatorio['inCodCondominioFinal'] );
            } else {
                $obPDF->addFiltro( 'Condomínio' , $arFiltroRelatorio['inCodCondominioInicial'].$arFiltroRelatorio['inCodCondominioFinal'] );
            }
        }

        if ($sessao->filtro['inNumLogradouro']) {
            $obPDF->addFiltro( 'Logradouro' , $arFiltroRelatorio['inNumLogradouro'].' - '. $arFiltroRelatorio['stNomLogradouro'] );
        }

        $obPDF->show();
    } //if ($arFiltroRelatorio['stTipoRelatorio'] == 'sintetico') {
} else {

    //titulo
    $arTitulo2 = array();
    $arTitulo2[] = array(
        "aviso" => "[ AVISO ]:",
        "titulo" => $arSessaoTransf6[0]['erro']
    );

    $rsTit2 = new Recordset;
    $rsTit2->preenche($arTitulo2);
    $rsTit2->setPrimeiroElemento();
    $obPDF->addRecordSet( $rsTit2 );
    #$obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho   ( "" ,10, 20 );
    $obPDF->addCabecalho   ( "" ,60, 50 );
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCampo       ( "aviso" , 11, "B" );
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo       ( "titulo" , 11  );

    $obPDF->show();
}

?>
