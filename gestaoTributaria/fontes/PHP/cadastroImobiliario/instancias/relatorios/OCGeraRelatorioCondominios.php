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
    * Arquivo paga geração de relatorio de Condominios
    * Data de Criação: 11/02/2008

    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Fernando Piccini Cercato

    * $Id: OCGeraRelatorioCondominios.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.27
*/

/*
$Log$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php" );
include_once( CAM_FW_PDF."ListaPDF.class.php" );

set_time_limit(300000);

$obRRelatorio = new RRelatorio;
$obPDF        = new ListaPDF("L");

$obRRelatorio->setExercicio  ( Sessao::getExercicio() );
$obRRelatorio->recuperaCabecalho( $arConfiguracao );
$obPDF->setModulo            ( "Relatorio:"   );
$obPDF->setTitulo            ( "Condomínios:" );
$obPDF->setSubTitulo         ( "Exercício - ".Sessao::getExercicio() );
$obPDF->setUsuario           ( Sessao::getUsername() );
$obPDF->setEnderecoPrefeitura( $arConfiguracao );

//colocar aqui o laco principal para criar uma lista para cada condominio
$arDadosSessao = Sessao::read('arDados');
for ( $inL=0; $inL<count($arDadosSessao); $inL++ ) {
    unset( $rsDados );
    $rsDados = new RecordSet;
    unset( $arDados );
    $arDados[] = array( "condominio" => "CONDOMÍNIO ".$arDadosSessao[$inL]["nom_condominio"] );

    $rsDados->preenche( $arDados );
    $obPDF->addRecordSet( $rsDados );

    if ( $inL )
         $obPDF->setQuebraPaginaLista ( false );

    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho   ( "", 50, 9 );
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo       ( "condominio", 12, 'B' );

    unset($rsDados2);
    $rsDados2 = new RecordSet;

    $rsDados2->preenche( $arDadosSessao[$inL]["imoveis"] );

    $obPDF->addRecordSet( $rsDados2 );
    $obPDF->setQuebraPaginaLista ( false );

    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho   ( "INSCRIÇÃO IMOBILIÁRIA", 10, 9 );
    $obPDF->addCabecalho   ( "CONTRIBUINTE", 25, 9 );
    $obPDF->addCabecalho   ( "LOGRADOURO", 39, 9 );

    for ($inX=1; $inX<=$arDadosSessao[0]["imoveis"][0]["qtd_atributo"]; $inX++) {
        $obPDF->addCabecalho   ( $arDadosSessao[0]["imoveis"][0]["atribn_".$inX], 9, 9 );
    }

    $obPDF->setAlinhamento( "C" );
    $obPDF->addCampo      ( "inscricao_municipal", 7 );
    $obPDF->setAlinhamento( "L" );
    $obPDF->addCampo      ( "nomcgm_proprietario", 7 );
    $obPDF->addCampo      ( "logradouro", 7 );
    for ($inX=1; $inX<=$arDadosSessao[0]["imoveis"][0]["qtd_atributo"]; $inX++) {
        $obPDF->addCampo   ( "atribv_".$inX, 7 );
    }

    unset( $rsDados );
    $rsDados = new RecordSet;
    unset( $arDados );
    $arDados[] = array( "condominio" => "Total de Imóveis: ".count( $arDadosSessao[$inL]["imoveis"] ) );

    $rsDados->preenche( $arDados );

    $obPDF->addRecordSet( $rsDados );
    $obPDF->setQuebraPaginaLista ( false );

    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho   ( "", 50, 9 );
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo       ( "condominio", 10 );
}

//$obPDF->addFiltro( 'Código Bairro Inicial'         , $sessao->filtro['inCodInicioBairro']        );
//$obPDF->addFiltro( 'Código Bairro Final'           , $sessao->filtro['inCodTerminoBairro']       );

$obPDF->show();
?>
