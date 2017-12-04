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
    * Arquivo paga geração de relatorio de Divida
    * Data de Criação: 19/04/2007

    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Fernando Piccini Cercato

    * @ignore

    * $Id: OCMonta2RelatorioDivida.php 63839 2015-10-22 18:08:07Z franver $

    * Casos de uso: uc-05.04.10
*/

/*
$Log$
Revision 1.4  2007/07/12 14:36:57  dibueno
Melhoria para o Relatorio da Divida

Revision 1.3  2007/07/12 13:02:05  dibueno
Melhoria para o Relatorio da Divida

Revision 1.2  2007/07/11 15:04:22  dibueno
Exibição de Parcelas Originais, caso não haja ainda parcelas da cobrança da divida

Revision 1.1  2007/04/19 16:06:56  cercato
*** empty log message ***

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaAtiva.class.php" );
include_once( CAM_FW_PDF."RRelatorio.class.php" );
include_once( CAM_FW_PDF."ListaPDF.class.php" );

set_time_limit(300000);

$obRRelatorio = new RRelatorio;
$obPDF        = new ListaPDF("L");

$obRRelatorio->setExercicio  ( Sessao::getExercicio() );
$obRRelatorio->recuperaCabecalho( $arConfiguracao );

$obPDF->setModulo            ( "Relatorio:"   );
$obPDF->setTitulo            ( "Divida Ativa:" );
$obPDF->setSubTitulo         ( "Exercício - ".Sessao::getExercicio() );
$obPDF->setUsuario           ( Sessao::getUsername() );
$obPDF->setEnderecoPrefeitura( $arConfiguracao );

$rsDadosRelatorio = Sessao::read('rsDadosRelatorio');
$rsDados1 = new RecordSet;
$rsDados3 = new RecordSet;
$rsDados4 = new RecordSet;
$arTMP = $rsDadosRelatorio->getElementos();
$obTDATDividaAtiva = new TDATDividaAtiva;

$rsDadosRelatorio->setUltimoElemento();
$obPDF->addRecordSet( $rsDadosRelatorio );

$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho   ( "Contribuinte"         , 26, 9, "B" );
$obPDF->setAlinhamento ( "C" );
$obPDF->addCabecalho   ( "Inscrição Imobiliária", 18, 9, "B" );
$obPDF->addCabecalho   ( "Inscrição Econômica"  , 18, 9, "B" );
$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho   ( "Endereço"             , 28, 9, "B" );

$obPDF->setAlinhamento( "L" );
$obPDF->addCampo      ( "[numcgm] - [nom_cgm]"  , 8 );
$obPDF->setAlinhamento ( "C" );
$obPDF->addCampo      ( "inscricao_municipal"   , 8 );
$obPDF->addCampo      ( "inscricao_economica"   , 8 );
$obPDF->setAlinhamento ( "L" );
$obPDF->addCampo      ( "endereco"              , 8 );

$rsDadosRelatorio->setPrimeiroElemento();

$arNumeracao = array();
$inContNum = 0;
while ( !$rsDadosRelatorio->eof() ) {
    $rsDadosCobranca = new RecordSet;
    $boInserir = true;
    $arInscricoes = array();
    for ($inX=0; $inX<$inContNum; $inX++) {
        if ( $arNumeracao[$inX] == $rsDadosRelatorio->getCampo('num_parcelamento') ) {
            $boInserir = false;
            break;
        }
    }
    if ($boInserir) {
        for ( $inX=0; $inX<count($arTMP); $inX++ ) {
            if ( $arTMP[$inX]["num_parcelamento"] == $rsDadosRelatorio->getCampo('num_parcelamento') ) {
                $boInserir = true;
                for ( $inY=0; $inY<count($arInscricoes); $inY++ ) {
                    if ( ($arInscricoes[$inY]["cod_inscricao"] == $arTMP[$inX]["cod_inscricao"])
                        && ($arInscricoes[$inY]["exercicio"] == $arTMP[$inX]["exercicio"]) ) {
                        $boInserir = false;
                        break;
                    }
                }

                if ( $boInserir )
                    $arInscricoes[] = $arTMP[$inX];
            }
        }

        $arNumeracao[$inContNum] = $rsDadosRelatorio->getCampo('num_parcelamento');
        $inContNum++;
        $stFiltro = " WHERE dp.num_parcelamento = ". $rsDadosRelatorio->getCampo('num_parcelamento');
        $obTDATDividaAtiva->recuperaListaDadosRelatorioDivida( $rsDadosCobranca, $stFiltro );

        $arCobranca = array();
        $nuTotalAberto = $nuTotalPago = $nuTotalCancelado = 0.00;
        while ( !$rsDadosCobranca->Eof() ) {

            #********* TOTAIS *******
            if ( $rsDadosCobranca->getCampo("situacao") == "Aberta" ) {
                $nuTotalAberto  += $rsDadosCobranca->getCampo("valor_total");
            } elseif ( $rsDadosCobranca->getCampo("situacao") == "Paga" ) {
                $nuTotalPago    += $rsDadosCobranca->getCampo("valor_total");
            } else {
                $nuTotalCancelado += $rsDadosCobranca->getCampo("valor_total");
            }

            $arCobranca[] = array (
                "num_parcela"           => $rsDadosCobranca->getCampo("num_parcela"),
                "total_de_parcelas"     => $rsDadosCobranca->getCampo("total_de_parcelas"),
                "valor_original"        => $rsDadosCobranca->getCampo("valor_original"),
                "valor_reducao"         => $rsDadosCobranca->getCampo("valor_reducao"),
                "valor_juros"           => $rsDadosCobranca->getCampo("valor_juros"),
                "valor_multa"           => $rsDadosCobranca->getCampo("valor_multa"),
                "valor_correcao"        => $rsDadosCobranca->getCampo("valor_correcao"),
                "valor_multa_infracao"  => $rsDadosCobranca->getCampo("valor_multa_infracao"),
                "valor_total"           => $rsDadosCobranca->getCampo("valor_total"),
                "situacao"              => $rsDadosCobranca->getCampo("situacao")
            );

            $rsDadosCobranca->proximo();
        }

        $obPDF->addRecordSet( $rsDados4 );
        $obPDF->setQuebraPaginaLista ( false );
        $obPDF->setAlinhamento ( "L" );
        $obPDF->addCabecalho   ( "", 8, 9 );
        $obPDF->setAlinhamento( "L" );
        $obPDF->addCampo      ( "espaco", 7 );

        $boInserir = true;
        for ( $inY=0; $inY<count($arInscricoes); $inY++ ) {
            if ( preg_match( "/-1\//", $arInscricoes[$inY]["numero_parcelamento"] ) ) {
                $boInserir = false;
                break;
            }
        }

        $rsDados5 = new RecordSet;
        $rsDados5->preenche( $arInscricoes );
        $rsDados5->ordena( "exercicio" );

        $obPDF->addRecordSet( $rsDados5 );
        $obPDF->setQuebraPaginaLista ( false );

        $obPDF->setAlinhamento ( "C" );
        $obPDF->addCabecalho   ( "Exercício"                , 6 , 9, "B" );
        $obPDF->setAlinhamento ( "C" );
        $obPDF->addCabecalho   ( "Processo/Ano"             , 15, 9, "B" );
        $obPDF->addCabecalho   ( "Inscrição/Ano"            , 15, 9, "B" );
        $obPDF->addCabecalho   ( "Grupo de Crédito/Crédito" , 25, 9, "B" );
        if ( $boInserir )
            $obPDF->addCabecalho   ( "Cobrança"                 , 25, 9, "B" );

        $obPDF->setAlinhamento( "C" );
        $obPDF->addCampo      ( "exercicio"                     , 8 );
        $obPDF->addCampo      ( "processo"                      , 8 );
        $obPDF->addCampo      ( "[cod_inscricao]/[exercicio]"   , 8 );
        $obPDF->addCampo      ( "origem"                        , 8 );
        if ( $boInserir )
            $obPDF->addCampo      ( "numero_parcelamento"           , 8 );

        if ( count($arCobranca) > 0 ) {
            $rsCobranca = new RecordSet;
            $arTmp = array();
            $arTmp[] = array        ("nome" => "Parcelas de Cobrança" );
            $rsDados4->preenche     ( $arTmp );
            $obPDF->addRecordSet    ( $rsDados4 );
            $obPDF->setQuebraPaginaLista ( false );
            $obPDF->setAlinhamento  ( "L" );
            $obPDF->addCabecalho    ( ""    , 50, 9     );
            $obPDF->setAlinhamento  ( "L" );
            $obPDF->addCampo        ( "nome", 12, "B"   );

            $rsCobranca->preenche   ( $arCobranca );
            $rsCobranca->addFormatacao( "valor_original"        , "NUMERIC_BR" );
            $rsCobranca->addFormatacao( "valor_reducao"         , "NUMERIC_BR" );
            $rsCobranca->addFormatacao( "valor_juros"           , "NUMERIC_BR" );
            $rsCobranca->addFormatacao( "valor_multa"           , "NUMERIC_BR" );
            $rsCobranca->addFormatacao( "valor_correcao"        , "NUMERIC_BR" );
            $rsCobranca->addFormatacao( "valor_multa_infracao"  , "NUMERIC_BR" );
            $rsCobranca->addFormatacao( "valor_total"           , "NUMERIC_BR" );

            $obPDF->addRecordSet    ( $rsCobranca );
            $obPDF->setQuebraPaginaLista ( false );

            $obPDF->setAlinhamento  ( "C" );
            $obPDF->addCabecalho    ( "Parcela"          , 10 , 9, "B" );
            $obPDF->addCabecalho    ( "V. Original"      , 10 , 9, "B" );
            $obPDF->addCabecalho    ( "Desconto"         , 10 , 9, "B" );
            $obPDF->addCabecalho    ( "Juros"            , 10 , 9, "B" );
            $obPDF->addCabecalho    ( "Multa"            , 10 , 9, "B" );
            $obPDF->addCabecalho    ( "Correção"         , 10 , 9, "B" );
            $obPDF->addCabecalho    ( "Multa de Infração", 12 , 9, "B" );
            $obPDF->addCabecalho    ( "Total"            , 12 , 9, "B" );
            $obPDF->setAlinhamento  ( "C" );
            $obPDF->addCabecalho    ( "Situação"         , 16 , 9, "B" );

            $obPDF->setAlinhamento  ( "C" );
            $obPDF->addCampo        ( "[num_parcela]/[total_de_parcelas]", 7 );
            $obPDF->setAlinhamento  ( "R" );
            $obPDF->addCampo        ( "valor_original"      , 8 );
            $obPDF->addCampo        ( "valor_reducao"       , 8 );
            $obPDF->addCampo        ( "valor_juros"         , 8 );
            $obPDF->addCampo        ( "valor_multa"         , 8 );
            $obPDF->addCampo        ( "valor_correcao"      , 8 );
            $obPDF->addCampo        ( "valor_multa_infracao", 8 );
            $obPDF->addCampo        ( "valor_total"         , 8 );
            $obPDF->setAlinhamento  ( "C" );
            $obPDF->addCampo        ( "situacao"            , 8 );

            $arTmp = array();
            $arTmp[] = array        (
                "nome"      => "Totais:",
                "aberto"    => $nuTotalAberto,
                "pago"      => $nuTotalPago,
                "cancelado" => $nuTotalCancelado
            );
            $rsDados4->preenche     ( $arTmp );

            $rsDados4->addFormatacao ( "aberto"     , "NUMERIC_BR" );
            $rsDados4->addFormatacao ( "pago"       , "NUMERIC_BR" );
            $rsDados4->addFormatacao ( "cancelado"  , "NUMERIC_BR" );

            $obPDF->addRecordSet    ( $rsDados4 );
            $obPDF->setQuebraPaginaLista ( false );
            $obPDF->setAlinhamento  ( "C" );
            $obPDF->addCabecalho    ( ""                , 30, 10, "B"  );
            $obPDF->addCabecalho    ( "Abertos"         , 12, 10, "B"  );
            $obPDF->addCabecalho    ( "Pagos"           , 12, 10, "B"  );
            $obPDF->addCabecalho    ( "Cancelados"      , 12, 10, "B"  );

            $obPDF->setAlinhamento  ( "R" );
            $obPDF->addCampo        ( "nome"        , 9, "B" );
            $obPDF->addCampo        ( "aberto"      , 9, "B" );
            $obPDF->addCampo        ( "pago"        , 9, "B" );
            $obPDF->addCampo        ( "cancelado"   , 9, "B" );
        }
    }
    $rsDadosRelatorio->proximo();
}

$obPDF->addFiltro( 'Inscrição/Ano Inicial', $sessao->filtro['inCodInscricaoInicial'] );
$obPDF->addFiltro( 'Inscrição/Ano Final', $sessao->filtro['inCodInscricaoFinal'] );
$obPDF->addFiltro( 'Inscrição Imobiliária Final', $sessao->filtro['inCodImovelFinal'] );
$obPDF->addFiltro( 'Inscrição Imobiliária Inicial', $sessao->filtro['inCodImovelInicial'] );
$obPDF->addFiltro( 'Inscrição Imobiliária Final', $sessao->filtro['inCodImovelFinal'] );
$obPDF->addFiltro( 'Inscrição Econômica Inicial', $sessao->filtro['inNumInscricaoEconomicaInicial'] );
$obPDF->addFiltro( 'Inscrição Econômica Final', $sessao->filtro['inNumInscricaoEconomicaFinal'] );
$obPDF->addFiltro( 'CGM', $sessao->filtro['inCGM'] );
$obPDF->addFiltro( 'Código Logradouro', $sessao->filtro['inNumLogradouro'] );

$obPDF->show();

?>
