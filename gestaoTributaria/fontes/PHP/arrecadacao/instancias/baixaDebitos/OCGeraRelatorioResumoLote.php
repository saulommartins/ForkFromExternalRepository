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

    * $Id: OCGeraRelatorioResumoLote.php 65763 2016-06-16 17:31:43Z evandro $

    * Casos de uso: uc-05.01.03
*/

/*
$Log$
Revision 1.31  2007/08/01 19:44:55  dibueno
Bug#9798#

Revision 1.30  2007/07/10 19:03:10  dibueno
Melhoria referente à soma de pagamentos duplicados no relatorio

Revision 1.29  2007/06/28 15:10:37  dibueno
Bug #9500#

Revision 1.28  2007/06/11 15:04:02  dibueno
Bug #9350#

Revision 1.27  2007/05/18 19:04:18  dibueno
Inclusão dos valores inconsistentes para cada origem do lancamento

Revision 1.26  2007/04/17 15:57:50  dibueno
Bug #9034#

Revision 1.25  2007/04/16 20:38:14  dibueno
Bug #9034#

Revision 1.24  2007/04/09 20:17:12  dibueno
Bug #9034#

Revision 1.23  2007/04/09 18:56:14  dibueno
*** empty log message ***

Revision 1.22  2007/03/16 20:17:12  cercato
Bug #8772#

Revision 1.21  2007/03/06 17:40:30  dibueno
Exibição da descrição do Credito/Grupo no relatorio

Revision 1.20  2007/02/08 18:01:56  dibueno
Bug #8341#

Revision 1.19  2007/01/23 17:28:34  dibueno
Bug #7926#

Revision 1.18  2007/01/23 17:16:38  dibueno
Bug #7926#

Revision 1.17  2006/09/15 10:55:12  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php" );
include_once( CAM_FW_PDF."ListaPDF.class.php" );
include_once( CAM_GT_ARR_NEGOCIO."RARRPagamento.class.php"                                             );

$obRRelatorio = new RRelatorio;
$obPDF        = new ListaPDF("L");

$stTipo       = $_REQUEST["tipo"];

$obRRelatorio->setExercicio  ( Sessao::getExercicio() );
$obRRelatorio->recuperaCabecalho( $arConfiguracao );
$obPDF->setModulo            ( "Arrecadação:"   );
$obPDF->setTitulo               ( "Créditos:" );
$obPDF->setSubTitulo         ( "Exercício - ".Sessao::getExercicio() );
$obPDF->setUsuario           ( Sessao::getUsername() );
$obPDF->setEnderecoPrefeitura( $arConfiguracao );

$obRARRPagamento = new RARRPagamento;
$obRARRPagamento->inCodLote = $_REQUEST["cod_lote"];
$obRARRPagamento->stExercicio = $_REQUEST["exercicio"];

$obRARRPagamento->consultaResumoLoteBaixaManual($rsResumoLote );

$arDados = $rsResumoLote->getElementos();
$arDados[0]["tipo"] = $stTipo;
$rsResumoLote->preenche( $arDados );
$rsResumoLote->setPrimeiroElemento();

//titulo
$arTitulo1 = array("tit" => "DADOS DO LOTE");
$rsTit1 = new Recordset;
$rsTit1->preenche($arTitulo1);
$rsTit1->setPrimeiroElemento();
$obPDF->addRecordSet( $rsTit1 );
//$obPDF->setQuebraPaginaLista( false );
$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho   ( "DADOS DO LOTE"  ,20, 12, "B" );

$obPDF->addRecordSet( $rsResumoLote);
$obPDF->setQuebraPaginaLista( false );
$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho   ( "LOTE"             ,8, 10 );
$obPDF->addCabecalho   ( "DATA DO LOTE"     ,12, 10 );
$obPDF->addCabecalho   ( "DATA DA BAIXA"    ,12, 10 );
$obPDF->addCabecalho   ( "TIPO"             ,8, 10 );
$obPDF->addCabecalho   ( "REGISTROS"        ,9, 10 );
$obPDF->addCabecalho   ( "BANCO"            ,15, 10 );
$obPDF->addCabecalho   ( "AGÊNCIA"          ,15, 10 );

$obPDF->setAlinhamento ( "L" );
$obPDF->addCampo       ( "cod_lote"    , 8 );
$obPDF->addCampo       ( "data_lote"   , 8 );
$obPDF->addCampo       ( "data_baixa"  , 8 );
$obPDF->addCampo       ( "tipo" , 8 );
$obPDF->addCampo       ( "registros"   , 8 );
$obPDF->addCampo       ( "[num_banco] - [nom_banco]"     , 8 );
$obPDF->addCampo       ( "[num_agencia] - [nom_agencia]" , 8 );

if ($stTipo == "Pagamento") {

    $obRARRPagamento->listaResumoLoteOrigem ( $rsListaOrigem );

    $flSomaValorNormal = $flSomaValorJuros = $flSomaValorMulta = $flSomaValorDiff = 0.00;
    $flSomaValorTotal = $flSomaValorInconsistentes = 0.00;

    while ( !$rsListaOrigem->eof() ) {

        if ( $rsListaOrigem->getCampo('tipo') == 'grupo' ) {

            $obRARRPagamento->obRARRCarne->obRARRParcela->roRARRLancamento->roRARRCalculo->obRARRGrupo->setCodGrupo ( $rsListaOrigem->getCampo('origem') );
            $obRARRPagamento->obRARRCarne->obRARRParcela->roRARRLancamento->roRARRCalculo->obRARRGrupo->setExercicio ( $rsListaOrigem->getCampo('origem_exercicio') );

            $obRARRPagamento->obRARRCarne->obRARRParcela->roRARRLancamento->roRARRCalculo->obRMONCredito->setCodCredito( null );

            $obRARRPagamento->obRARRCarne->obRARRParcela->roRARRLancamento->roRARRCalculo->obRMONCredito->setCodEspecie( null );

        } else {

            $obRARRPagamento->obRARRCarne->obRARRParcela->roRARRLancamento->roRARRCalculo->obRARRGrupo->setCodGrupo ( null );
            $arCredito = explode ('.', $rsListaOrigem->getCampo('origem') );
            $obRARRPagamento->obRARRCarne->obRARRParcela->roRARRLancamento->roRARRCalculo->setExercicio( $rsListaOrigem->getCampo('origem_exercicio') );
            $obRARRPagamento->obRARRCarne->obRARRParcela->roRARRLancamento->roRARRCalculo->obRMONCredito->setCodCredito( $arCredito[0] );
            $obRARRPagamento->obRARRCarne->obRARRParcela->roRARRLancamento->roRARRCalculo->obRMONCredito->setCodEspecie( $arCredito[1] );
            $obRARRPagamento->obRARRCarne->obRARRParcela->roRARRLancamento->roRARRCalculo->obRMONCredito->setCodGenero( $arCredito[2] );
            $obRARRPagamento->obRARRCarne->obRARRParcela->roRARRLancamento->roRARRCalculo->obRMONCredito->setCodNatureza( $arCredito[3] );

        }

        $obRARRPagamento->setExercicio ( $rsListaOrigem->getCampo('exercicio') );

        if ( preg_match( "/Dívida Ativa/", $rsListaOrigem->getCampo("descricao") ) || preg_match( "/Divida Ativa/", $rsListaOrigem->getCampo("descricao") ) ) {
            $boDivida = 2;
        }else
        if ( preg_match( "/(D.A.)/", $rsListaOrigem->getCampo("descricao") )) {
            $boDivida = 1;
        }else
            $boDivida = 0;

        $obRARRPagamento->listaResumoLote( $rsListaCreditos, NULL, $boDivida );

        #SOMATORIO DAS PARCELAS NORMAIS
        $arSomatorios = null;
        $flOrigemValorNormalOK = $flOrigemValorJurosOK = $flOrigemValorMultaOK = $flOrigemValorDiffOK = $flOrigemValorTotalOK = 0.00;

        while ( !$rsListaCreditos->eof() ) {

            $flOrigemValorNormalOK += $rsListaCreditos->getCampo( 'somatorio' );
            $flOrigemValorJurosOK  += $rsListaCreditos->getCampo( 'juros' );
            $flOrigemValorMultaOK  += $rsListaCreditos->getCampo( 'multa' );
            $flOrigemValorDiffOK   += $rsListaCreditos->getCampo( 'diferenca' );
            $flOrigemValorTotalOK  += $rsListaCreditos->getCampo( 'total' );

            $rsListaCreditos->proximo();

        }

        $flSomaValorNormal += $flOrigemValorNormalOK;
        $flSomaValorJuros  += $flOrigemValorJurosOK;
        $flSomaValorMulta  += $flOrigemValorMultaOK;
        $flSomaValorDiff   += $flOrigemValorDiffOK;
        $flSomaValorTotal  += $flOrigemValorTotalOK;

        $arSomatorios[] = array (
                "somaNormal"    => $flOrigemValorNormalOK,
                "somaJuros"     => $flOrigemValorJurosOK,
                "somaMulta"     => $flOrigemValorMultaOK,
                "somaDiff"      => $flOrigemValorDiffOK,
                "somaTotal"     => $flOrigemValorTotalOK
        );
        #SOMATORIO DAS PARCELAS NORMAIS FIM

        #SOMATORIO DAS PARCELAS INCONSISTENTES
        $arSomatoriosI = null;
        $flOrigemValorInconsistenteOK = 0.00;
        $obRARRPagamento->listaResumoLoteInconsistenteAgrupado( $rsListaCreditosInconsistentes );
        while ( !$rsListaCreditosInconsistentes->eof() ) {

            $flValorAtual = str_replace ( ',', '.', $rsListaCreditosInconsistentes->getCampo( "valor" ) );
            $flOrigemValorInconsistenteOK += $flValorAtual;
            $rsListaCreditosInconsistentes->proximo();

        }

        $flSomaValorInconsistentes += $flOrigemValorInconsistenteOK;

        //titulo
        $arTitulo2 = array();
        $arTitulo2[] = array( "titulo" => $rsListaOrigem->getCampo('origem').' / '. $rsListaOrigem->getCampo('origem_exercicio') .' - '.$rsListaOrigem->getCampo('descricao') );
        $rsTit2 = new Recordset;
        $rsTit2->preenche($arTitulo2);
        $rsTit2->setPrimeiroElemento();
        $obPDF->addRecordSet( $rsTit2 );
        $obPDF->setQuebraPaginaLista( false );
        $obPDF->setAlinhamento ( "L" );
        $obPDF->addCabecalho   ( "" ,80, 50 );
        $obPDF->setAlinhamento ( "L" );
        $obPDF->addCampo       ( "titulo" , 14, "B" );

        $rsListaCreditos->addFormatacao ('somatorio', 'NUMERIC_BR');
        $rsListaCreditos->addFormatacao ('juros' , 'NUMERIC_BR');
        $rsListaCreditos->addFormatacao ('multa' , 'NUMERIC_BR');
        $rsListaCreditos->addFormatacao ('diferenca'  , 'NUMERIC_BR');
        $rsListaCreditos->addFormatacao ('total' , 'NUMERIC_BR');

        $rsListaCreditos->setPrimeiroElemento();

        $obPDF->addRecordSet( $rsListaCreditos );
        $obPDF->setQuebraPaginaLista( false );

        $obPDF->setAlinhamento ( "L" );
        $obPDF->addCabecalho   ( "CÓDIGO"  ,10, 10 );
        $obPDF->addCabecalho   ( "CRÉDITO" ,20, 10 );
        $obPDF->setAlinhamento ( "R" );
        $obPDF->addCabecalho   ( "VALOR"   ,20, 10 );
        $obPDF->addCabecalho   ( "JUROS"   ,10, 10 );
        $obPDF->addCabecalho   ( "MULTA"   ,10, 10 );
        $obPDF->addCabecalho   ( "DIFF"    ,10, 10 );
        $obPDF->addCabecalho   ( "TOTAL"   ,10, 10 );

        $obPDF->setAlinhamento ( "L" );
        $obPDF->addCampo       ( "origem" , 8 );
        $obPDF->addCampo       ( "descricao" , 8, "B" );
        $obPDF->setAlinhamento ( "R" );
        $obPDF->addCampo       ( "somatorio"    , 8 );
        $obPDF->addCampo       ( "juros"    , 8 );
        $obPDF->addCampo       ( "multa"    , 8 );
        $obPDF->addCampo       ( "diferenca", 8 );
        $obPDF->addCampo       ( "total"    , 8 );

        $rsSomatorio = new Recordset;
        $rsSomatorio->preenche ( $arSomatorios );
        $rsSomatorio->addFormatacao ('somaNormal', 'NUMERIC_BR');
        $rsSomatorio->addFormatacao ('somaJuros' , 'NUMERIC_BR');
        $rsSomatorio->addFormatacao ('somaMulta' , 'NUMERIC_BR');
        $rsSomatorio->addFormatacao ('somaDiff'  , 'NUMERIC_BR');
        $rsSomatorio->addFormatacao ('somaTotal' , 'NUMERIC_BR');
        $obPDF->addRecordSet( $rsSomatorio );
        $obPDF->setQuebraPaginaLista( false );

        $obPDF->setAlinhamento ( "R" );
        $obPDF->addCabecalho   ( "Totais" ,30, 9, "B" );
        $obPDF->addCabecalho   ( number_format( $flOrigemValorNormalOK, 2, ',', '.')  ,20, 9, "B" );
        $obPDF->addCabecalho   ( number_format( $flOrigemValorJurosOK, 2, ',', '.')   ,10, 9, "B" );
        $obPDF->addCabecalho   ( number_format( $flOrigemValorMultaOK, 2, ',', '.')   ,10, 9, "B" );
        $obPDF->addCabecalho   ( number_format( $flOrigemValorDiffOK, 2, ',', '.')    ,10, 9, "B" );
        $obPDF->addCabecalho   ( number_format( $flOrigemValorTotalOK, 2, ',', '.')  ,10, 9, "B" );

        $obPDF->addCampo       ( "" , 8 );
        $obPDF->setAlinhamento ( "R" );
        $obPDF->addCampo       ( ""   , 8, "B" );
        $obPDF->addCampo       ( ""    , 8, "B" );
        $obPDF->addCampo       ( ""    , 8, "B" );
        $obPDF->addCampo       ( ""     , 8, "B" );
        $obPDF->addCampo       ( ""    , 8, "B" );

        if ( $rsListaCreditosInconsistentes->getNumLinhas() > 0 ) {

            //titulo
            $arTitulo2 = array();
            $arTitulo2[] = array( "titulo" => "INCONSISTENTES" );
            $rsTit2 = new Recordset;
            $rsTit2->preenche($arTitulo2);
            $rsTit2->setPrimeiroElemento();
            $obPDF->addRecordSet( $rsTit2 );
            $obPDF->setQuebraPaginaLista( false );
            $obPDF->setAlinhamento ( "L" );
            $obPDF->addCabecalho   ( "" ,10, 10 );
            $obPDF->addCabecalho   ( "" ,50, 50 );
            $obPDF->setAlinhamento ( "L" );
            $obPDF->addCampo       ( "" , 10, "B" );
            $obPDF->addCampo       ( "titulo" , 9, "B" );

            $rsListaCreditosInconsistentes->addFormatacao ('valor', 'NUMERIC_BR');
            $rsListaCreditosInconsistentes->setPrimeiroElemento();

            $obPDF->addRecordSet( $rsListaCreditosInconsistentes );
            $obPDF->setQuebraPaginaLista( false );

            $obPDF->setQuebraPaginaLista( false );
            $obPDF->setAlinhamento ( "L" );
            $obPDF->addCabecalho   ( "Inconsistentes", 17, 10 );

            $obPDF->addCabecalho   ( "", 6, 10 );
            $obPDF->addCabecalho   ( "", 18, 10 );
            $obPDF->setAlinhamento ( "R" );
            $obPDF->addCabecalho   ( "", 8, 10 );
            $obPDF->setAlinhamento ( "R" );
            $obPDF->addCabecalho   ( "", 8, 10 );
            $obPDF->setAlinhamento ( "R" );
            $obPDF->addCabecalho   ( "", 8, 10 );
            $obPDF->setAlinhamento ( "R" );
            $obPDF->addCabecalho   ( "", 10, 10 );
            $obPDF->setAlinhamento ( "R" );
            $obPDF->addCabecalho   ( "", 8, 10 );
            $obPDF->addCabecalho   ( "", 8, 10 );

            $obPDF->setAlinhamento ( "L" );
            $obPDF->addCampo       ( "[numeracao]", 9 );
            $obPDF->setAlinhamento ( "C" );
            $obPDF->addCampo       ( "inscricao", 8 );
            $obPDF->setAlinhamento ( "L" );
            $obPDF->addCampo       ( "contribuinte", 8 );
            $obPDF->setAlinhamento ( "R" );
            $obPDF->addCampo       ( "valor", 8 );
            $obPDF->addCampo       ( "", 8 );
            $obPDF->addCampo       ( "", 8 );
            $obPDF->addCampo       ( "", 8 );
            $obPDF->addCampo       ( "valor", 9 );
            $obPDF->setAlinhamento ( "C" );
            $obPDF->addCampo       ( "data_pagamento", 9 );

            $stTotal = "Total Inconsistente:";
            $rsOrigemPagamento = new Recordset;
            $obPDF->addRecordSet( $rsOrigemPagamento );

            $obPDF->setQuebraPaginaLista( false );
            $obPDF->setAlinhamento ( "R" );
            $obPDF->addCabecalho   ( $stTotal, 75, 9, "B" );
            $obPDF->setAlinhamento ( "R" );
            $obPDF->addCabecalho   ( number_format ( $flOrigemValorInconsistenteOK, 2, ',', '.' ), 8, 9, "B" );

            $obPDF->setAlinhamento ( "L" );
            $obPDF->addCampo       ( "", 8 );
            $obPDF->addCampo       ( "", 9 );

        }

        $flSomaTotalOrigem = $flOrigemValorInconsistenteOK + $flOrigemValorTotalOK;
        #echo '<br>'.$flOrigemValorInconsistenteOK.' - '.$flOrigemValorTotalOK.' = '.$flSomaTotalOrigem;

        $stTotal = "Total do Crédito / Grupo:";
        $rsOrigemPagamento = new Recordset;
        $obPDF->addRecordSet( $rsOrigemPagamento );

        $obPDF->setQuebraPaginaLista( false );
        $obPDF->setAlinhamento ( "R" );
        $obPDF->addCabecalho   ( $stTotal, 75, 10, "B" );
        $obPDF->setAlinhamento ( "R" );
        $obPDF->addCabecalho   ( number_format ( $flSomaTotalOrigem , 2, ',', '.' ), 15, 10, "B" );

        $obPDF->setAlinhamento ( "L" );
        $obPDF->addCampo       ( "", 8 );
        $obPDF->addCampo       ( "", 9 );

        $rsListaOrigem->proximo();
    }

    #==========================================================
    // AMOSTRA DE TOTAL INCONSISTENTE SEM VINCULO
    #==========================================================
    $rsInconsistentes = new Recordset;
    $obRARRPagamento->listaResumoLoteInconsistenteSemVinculo ( $rsInconsistentes );
    $flOrigemValorInconsistente2OK = 0.00;
    if ( $rsInconsistentes->getNumLinhas() > 0 ) {

        while ( !$rsInconsistentes->eof() ) {
            $flValorAtual = str_replace ( ',', '.', $rsInconsistentes->getCampo( "valor" ) );
            $flOrigemValorInconsistente2OK += $flValorAtual;
            $rsInconsistentes->proximo();
        }

        $flSomaValorInconsistentes2 += $flOrigemValorInconsistente2OK;
        $rsInconsistentes->setPrimeiroElemento();

        //titulo
        $obPDF->addRecordSet( $rsInconsistentes );

        // Numeração     Parcela     Origem      Inscrição       Contribuinte    Valor (R$)   Pagamento   Situação
        $obPDF->setQuebraPaginaLista( false );
        $obPDF->setAlinhamento ( "L" );
        $obPDF->addCabecalho   ( "INCONSISTENTES SEM VÍNCULO", 30, 12, "B" );

        $obPDF->addCabecalho   ( "", 11, 10 );
        $obPDF->addCabecalho   ( "", 8, 10 );
        $obPDF->addCabecalho   ( "", 8, 10 );
        $obPDF->addCabecalho   ( "", 8, 10 );
        $obPDF->addCabecalho   ( "", 5, 10 );
        $obPDF->addCabecalho   ( "", 8, 10 );
        $obPDF->addCabecalho   ( "", 14, 10 );

        $obPDF->setAlinhamento ( "L" );
        $obPDF->addCampo       ( "[numeracao]", 9, "B" );
        $obPDF->setAlinhamento ( "C" );
        $obPDF->addCampo       ( "inscricao", 8 );
        $obPDF->setAlinhamento ( "L" );
        $obPDF->addCampo       ( "contribuinte", 8 );
        $obPDF->setAlinhamento ( "R" );
        $obPDF->addCampo       ( "", 8 );
        $obPDF->addCampo       ( "", 8 );
        $obPDF->addCampo       ( "", 8 );
        $obPDF->setAlinhamento ( "C" );
        $obPDF->addCampo       ( "data_pagamento", 9 );
        $obPDF->setAlinhamento ( "R" );
        $obPDF->addCampo       ( "valor", 9 );

        $stTotal = "Total Inconsistente Sem Vínculo:";
        $rsOrigemPagamento = new Recordset;
        $obPDF->addRecordSet( $rsOrigemPagamento );

        $obPDF->setQuebraPaginaLista( false );
        $obPDF->setAlinhamento ( "R" );
        $obPDF->addCabecalho   ( $stTotal, 75, 10, "B" );
        $obPDF->addCabecalho   ( number_format ( $flSomaValorInconsistentes2, 2, ',', '.' ), 17, 10, "B" );

        $obPDF->setAlinhamento ( "L" );
        $obPDF->addCampo       ( "", 8 );
        $obPDF->addCampo       ( "", 9 );

    }
    // ================================================== FIM DOS INCONSISTENTES SEM VINCULO

    //ESPAÇAMENTO
    $obPDF->addRecordSet( $rsOrigemPagamento );
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->addCabecalho   ( "", 15, 11, "B" );
    $obPDF->addCampo       ( "", 11 );

    #==========================================================
    // AMOSTRA DE TOTAL DE GRUPOS/CREDITO
    #==========================================================
    $arTotalLote = array();
    $arTotalLote[] = array (
        "normal"    => $flSomaValorNormal,
        "juros"     => $flSomaValorJuros,
        "multa"     => $flSomaValorMulta,
        "diferenca" => $flSomaValorDiferenca,
        "total"     => $flSomaValorTotal
    );

    $rsOrigemPagamento = new Recordset;
    $rsOrigemPagamento->preenche ( $arTotalLote );
    $rsOrigemPagamento->addFormatacao ( "normal"    , "NUMERIC_BR" );
    $rsOrigemPagamento->addFormatacao ( "juros"     , "NUMERIC_BR" );
    $rsOrigemPagamento->addFormatacao ( "multa"     , "NUMERIC_BR" );
    $rsOrigemPagamento->addFormatacao ( "diferenca" , "NUMERIC_BR" );
    $rsOrigemPagamento->addFormatacao ( "total"     , "NUMERIC_BR" );
    $obPDF->addRecordSet( $rsOrigemPagamento );
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho   ( "", 7, 10 );
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCabecalho   ( "Totais do Relatório:", 30, 12, "B" );
    $obPDF->addCabecalho   ( "Valor", 10, 11, "B" );
    $obPDF->addCabecalho   ( "Juros", 10, 11, "B" );
    $obPDF->addCabecalho   ( "Multa", 10, 11, "B" );
    $obPDF->addCabecalho   ( "Diferença", 10, 11, "B" );
    $obPDF->addCabecalho   ( "Total", 15, 11, "B" );

    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCampo       ( ""         , 11 );
    $obPDF->addCampo       ( ""         , 11 );
    $obPDF->addCampo       ( "normal"   , 11, "B" );
    $obPDF->addCampo       ( "juros"    , 11, "B" );
    $obPDF->addCampo       ( "multa"    , 11, "B" );
    $obPDF->addCampo       ( "diferenca", 11, "B" );
    $obPDF->addCampo       ( "total"    , 11, "B" );
    #==========================================================

    #==========================================================
    // AMOSTRA DE TOTAL INCONSISTENTE COM VINCULO
    #==========================================================
    $obPDF->addRecordSet( $rsOrigemPagamento );
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho   ( "", 10, 10 );
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCabecalho   ( "", 30, 12, "B" );
    $obPDF->addCabecalho   ( "", 10, 11, "B" );
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCabecalho   ( "Total Inconsistente:", 25, 12, "B" );
    $obPDF->addCabecalho   ( number_format($flSomaValorInconsistentes,2,',','.') , 17, 11, "B" );

    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCampo       ( "" , 11 );
    $obPDF->addCampo       ( "" , 11 );
    $obPDF->addCampo       ( "" , 11 );
    $obPDF->addCampo       ( "" , 11 );
    $obPDF->addCampo       ( "" , 11 );

    #==========================================================
    // AMOSTRA DE TOTAL INCONSISTENTE SEM VINCULO
    #==========================================================
    $obPDF->addRecordSet( $rsOrigemPagamento );
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho   ( "", 10, 10 );
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCabecalho   ( "", 30, 12, "B" );
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCabecalho   ( "Total Inconsistente sem Vínculo:", 35, 12, "B" );
    $obPDF->addCabecalho   ( number_format ($flSomaValorInconsistentes2, 2, ',', '.'), 17, 11, "B" );

    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCampo       ( "" , 11 );
    $obPDF->addCampo       ( "" , 11 );
    $obPDF->addCampo       ( "" , 11 );
    $obPDF->addCampo       ( "" , 11 );

    #==========================================================
    // TOTAL GERAL
    #==========================================================
    $obPDF->addRecordSet( $rsOrigemPagamento );
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho   ( "", 5, 10 );
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCabecalho   ( "", 30, 12, "B" );
    $obPDF->addCabecalho   ( "", 10, 11, "B" );
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCabecalho   ( "Total Geral:", 25, 13, "B" );
    $obPDF->addCabecalho   ( number_format(( $flSomaValorInconsistentes + $flSomaValorTotal + $flSomaValorInconsistentes2 ), 2,',','.'),22,12,"B");

    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCampo       ( "" , 11 );
    $obPDF->addCampo       ( "" , 11 );
    $obPDF->addCampo       ( "" , 11 );
    $obPDF->addCampo       ( "" , 11 );
    $obPDF->addCampo       ( "" , 11 );

} else {

    $obRARRCarne = new RARRCarne;

    $obRARRPagamento->consultaListaFechaBaixaManual( $rsListaDados );
    $rsListaCreditos = new Recordset;

    if ( !$rsListaDados->Eof() ) {
        $arDados = array();
        $inDados = 0;
        while ( !$rsListaDados->Eof() ) {
            $obRARRPagamento->obRARRCarne->obRARRParcela->roRARRLancamento->setCodLancamento( $rsListaDados->getCampo("cod_lancamento") );
            $obRARRPagamento->obRARRCarne->setNumeracao( $rsListaDados->getCampo("numeracao") );
            $obRARRPagamento->obRARRCarne->obRARRParcela->setCodParcela( $rsListaDados->getCampo("cod_parcela") );

            $obRARRPagamento->listaResumoBaixaCanceladas( $rsListaCreditos, "",  $rsListaDados->getCampo("data_pagamento") );

            $rsListaDados->proximo();

            $arTmp = $rsListaCreditos->getElementos();
            $inTotal = count( $arTmp );
            for ($inX = 0; $inX < $inTotal; $inX++) {
                $boAchou = false;
                for ($inY = 0; $inY < $inDados; $inY++) {
                    if ($arDados[$inY]["cod"] == $arTmp[$inX]["cod"]) {
                        $arDados[$inY]["valor"] += $arTmp[$inX]["valor"];
                        $arDados[$inY]["somatorio"] += $arTmp[$inX]["somatorio"];
                        $arDados[$inY]["valor_credito_jurosp"] += $arTmp[$inX]["valor_credito_jurosp"];
                        $arDados[$inY]["valor_credito_multap"] += $arTmp[$inX]["valor_credito_multap"];
                        $arDados[$inY]["descontop"] += $arTmp[$inX]["descontop"];
                        $arDados[$inY]["juros"] += $arTmp[$inX]["juros"];
                        $arDados[$inY]["multa"] += $arTmp[$inX]["multa"];
                        $arDados[$inY]["desconto"] += $arTmp[$inX]["desconto"];
                        $boAchou = true;
                        break;
                    }
                }

                if (!$boAchou) {
                    $arDados[$inDados] = $arTmp[$inX];
                    $inDados++;
                }
            }
        }

        $rsListaCreditos->preenche( $arDados );
        $rsListaCreditos->setPrimeiroElemento();
    }

    $arTmp = array();
    $stControla = 1;
    $stCodAnt = 'naoexiste';
    //$arTmp3 = $rsListaCreditos->arElementos;
    //$arTmp3[] = array( 'cod' =>  '' ,'cod_credito' => '', 'cod_especie' => '' , 'cod_genero' => '', 'cod_natureza'=> '', 'descricao_credito' => '', 'somatorio' => '','juros' => '', 'multa' => '');
    //$rsListaCreditos->preenche($arTmp3);
    //$rsListaCreditos->setPrimeiroElemento();
    foreach ($rsListaCreditos->arElementos as $value) {
        if ($stControla == 1) {
            $stControla = 2;
            $arTmp[] = $value;
        }
        if ($stControla == 2) {
        $arN = array( 'cod' =>  $value['cod'] ,'cod_credito' => '', 'cod_especie' => '' , 'cod_genero' => '', 'cod_natureza' => '', 'descricao_credito' => '     Juros', 'somatorio' => $value['juros'], 'juros' => $value['juros'] , 'multa' => $value['multa']);
        $stControla = 3;
        if ( $value['juros'] > 0)  $arTmp[] = $arN;
        }
        if ($stControla == 3) {
        $stControla = 4;
        $arN = array( 'cod' =>  $value['cod'] ,'cod_credito' => '', 'cod_especie' => '' , 'cod_genero' => '', 'cod_natureza' => '', 'descricao_credito' => '     Multa', 'somatorio' => $value['multa'], 'juros' => $value['juros'], 'multa' => $value['multa']);
        if ( $value['multa'] > 0)  $arTmp[] = $arN;
        }
        if ($stControla == 4) {
        $stControla = 1;
        $arN = array( 'cod' =>  $value['cod'] ,'cod_credito' => '', 'cod_especie' => '' , 'cod_genero' => '', 'cod_natureza' => '', 'descricao_credito' => '     Diferença de Pagamento', 'somatorio' => $value['diferenca'], 'juros' => $value['juros'], 'multa' => $value['multa']);
        if ( $value['diferenca'] > 0)  $arTmp[] = $arN;
        }
    }
    $rsListaCreditos->preenche($arTmp);
    $rsListaCreditos->setPrimeiroElemento();

    $totalLote = 0.00;
    foreach ($rsListaCreditos->arElementos as $value) {
        $stValor = str_replace(".","",$value['somatorio']);
        $stValor = str_replace(",",".",$value['somatorio']);
        $arNovo[] = array("cod" => $value['cod'], "cod_credito" => $value['cod_credito'], "cod_especie" => $value['cod_especie'] , "cod_genero" => $value['cod_genero'], "cod_natureza" => $value['cod_natureza'] ,"descricao_credito" => $value['descricao_credito'],  "somatorio" => "$stValor" );
        $totalLote += $stValor;
    }

    $arNovo[] = array("cod" => "", "cod_credito" => " ", "cod_especie" => " " , "cod_genero" => " ", "cod_natureza" => " " ,"descricao_credito" => "", "somatorio" => "" );
    $arNovo[] = array("cod" => " ", "cod_credito" => " ", "cod_especie" => " " , "cod_genero" => " ", "cod_natureza" => " " ,"descricao_credito" => "TOTAL BAIXADO",  "somatorio" => number_format( $totalLote, 2, ',', '.' ) );

    $rsListaCreditos->preenche($arNovo);
    $rsListaCreditos->setPrimeiroElemento();

    //titulo
    $arTitulo2 = array("tit" => "CRÉDITOS");
    $rsTit2 = new Recordset;
    $rsTit2->preenche($arTitulo2);
    $rsTit2->setPrimeiroElemento();
    $obPDF->addRecordSet( $rsTit2 );
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho   ( "CRÉDITOS"  ,20, 10, "B" );

    $obPDF->addRecordSet( $rsListaCreditos );

    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho   ( "CÓDIGO"  ,20, 10 );
    $obPDF->addCabecalho   ( "CRÉDITO" ,20, 10 );
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCabecalho   ( "VALOR"   ,20, 10 );

    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo       ( "cod" , 8 );
    $obPDF->addCampo       ( "descricao_credito" , 8 );
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCampo       ( "somatorio"    , 8 );

}

$obPDF->show();
?>
