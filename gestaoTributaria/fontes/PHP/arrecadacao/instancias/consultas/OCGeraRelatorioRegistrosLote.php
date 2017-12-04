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

    * $Id: OCGeraRelatorioRegistrosLote.php 60878 2014-11-20 13:35:30Z jean $

    * Casos de uso: uc-05.03.19
                    uc-05.03.10
*/

/*
$Log$
Revision 1.20  2007/08/02 21:22:53  dibueno
*** empty log message ***

Revision 1.19  2007/08/01 19:44:45  dibueno
Bug#9798#

Revision 1.18  2007/06/28 15:11:09  dibueno
Bug #9500#

Revision 1.17  2007/06/11 15:03:54  dibueno
Bug #9350#

Revision 1.16  2007/06/07 18:04:42  dibueno
Bug #9350#

Revision 1.15  2007/06/06 18:46:01  dibueno
Bug #9350#

Revision 1.14  2007/04/17 15:58:05  dibueno
Bug #9034#

Revision 1.13  2007/04/16 20:37:48  dibueno
Bug #9034#

Revision 1.12  2007/04/09 20:09:42  dibueno
Bug #9034#

Revision 1.11  2007/03/19 14:37:44  cercato
alterando numero do caso de uso, adicionando total geral e total inconsistente.

Revision 1.10  2007/03/06 17:40:21  dibueno
Exibição da descrição do Credito/Grupo no relatorio

Revision 1.9  2007/02/08 18:48:12  dibueno
Bug #8341#

Revision 1.8  2007/02/08 18:05:34  dibueno
Bug #8341#

Revision 1.7  2006/09/15 11:04:44  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php" );
include_once( CAM_FW_PDF."ListaPDF.class.php" );
include_once( CAM_GT_ARR_NEGOCIO."RARRPagamento.class.php" );

$rsDadosLote = Sessao::read( 'dadoslote' );

$obRRelatorio = new RRelatorio;
$obPDF        = new ListaPDF("L");

$obRRelatorio->setExercicio  ( Sessao::getExercicio() );
$obRRelatorio->recuperaCabecalho( $arConfiguracao );

$obPDF->setModulo            ( "Arrecadação:"   );
$obPDF->setTitulo               ( "Créditos:" );
$obPDF->setSubTitulo         ( "Exercício - ".Sessao::getExercicio() );
$obPDF->setUsuario           ( Sessao::getUsername() );
$obPDF->setEnderecoPrefeitura( $arConfiguracao );

$obRRelatorio = new RRelatorio;
$obPDF        = new ListaPDF("L");

$obRRelatorio->setExercicio  ( Sessao::getExercicio() );
$obRRelatorio->recuperaCabecalho( $arConfiguracao );

if ($_REQUEST["descricao"]) {
    $arConfiguracao["nom_acao"] = $_REQUEST["descricao"];
}

$obPDF->setModulo            ( "Arrecadação:"   );
$obPDF->setTitulo               ( "Créditos:" );
$obPDF->setSubTitulo         ( "Exercício - ".Sessao::getExercicio() );
$obPDF->setUsuario           ( Sessao::getUsername() );
$obPDF->setEnderecoPrefeitura( $arConfiguracao );

#================================================ DADOS DO LOTE
//titulo
$arTitulo1 = array("tit" => "Dados do Lote");
$rsTit1 = new Recordset;
$rsTit1->preenche($arTitulo1);
$rsTit1->setPrimeiroElemento();
$obPDF->addRecordSet( $rsTit1 );

$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho   ( "Dados do Lote"  ,20, 12, "B" );

$obPDF->addRecordSet ( $rsDadosLote );
$obPDF->setQuebraPaginaLista( false );
$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho   ( "Lote", 8, 10 );
$obPDF->addCabecalho   ( "Data", 12, 10 );
$obPDF->addCabecalho   ( "Responsável", 26, 10 );
$obPDF->addCabecalho   ( "Banco", 18, 10 );
$obPDF->addCabecalho   ( "Agência", 18, 10 );
$obPDF->addCabecalho   ( "Tipo", 8, 10 );

$obPDF->setAlinhamento ( "L" );
$obPDF->addCampo       ( "cod_lote", 8 );
$obPDF->addCampo       ( "data", 8 );
$obPDF->addCampo       ( "[responsavel_cgm] - [responsavel_cgmnome]", 8 );
$obPDF->addCampo       ( "[num_banco] - [nom_banco]", 8 );
$obPDF->addCampo       ( "[num_agencia] - [nom_agencia]", 8 );
$obPDF->addCampo       ( "tipo", 8 );
//segunda parte do relatorio
#================================================ DADOS DO LOTE FIM

$obRARRPagamento    = new RARRPagamento;
$obRARRPagamento->setOcorrenciaPagamento ( $rsDadosLote->getCampo("ocorrencia_pagamento") );
$obRARRPagamento->setCodLote ( $rsDadosLote->getCampo("cod_lote") );

if ( $rsDadosLote->getCampo("cgm_contribuinte") ) {
    $obRARRPagamento->obRMONAgencia->obRCGM->setNumCGM ( $rsDadosLote->getCampo("cgm_contribuinte") );
}

$obRARRPagamento->listaResumoLoteOrigem ( $rsListaOrigem );

$flSomaValorNormal = $flSomaValorJuros = $flSomaValorMulta = 0.00;
$flSomaValorDiferenca = $flSomaValorTotal = $flTotalValorInconsistente = 0.00;

$obRARRPagamento->listarPagamentosLoteDA ( $rsPagamentosDA );

$arTMP = $rsPagamentosDA->getElementos();

$arTMP2 = array();
$inTotalNumeracao = 0;
for ( $inX=0; $inX<count($arTMP); $inX++ ) {
    $boEncontrou = false;
    for ($inY=0; $inY<$inTotalNumeracao; $inY++) {
        if ($arTMP[$inX]["numeracao"] === $arTMP2[$inY]["numeracao"]) {
            $arTMP2[$inY]["valor_pago_calculo"] += $arTMP[$inX]["valor_pago_calculo"];
            $arTMP2[$inY]["juros"] += $arTMP[$inX]["juros"];
            $arTMP2[$inY]["multa"] += $arTMP[$inX]["multa"];
            $arTMP2[$inY]["credesc"] .= ", ".$arTMP[$inX]["cod_credito"].".".$arTMP[$inX]["cod_especie"].".".$arTMP[$inX]["cod_genero"].".".$arTMP[$inX]["cod_natureza"]." / ".$arTMP[$inX]["descricao_credito"];
            $boEncontrou = true;
            break;
        }
    }

    if (!$boEncontrou) {
        $inY = $inTotalNumeracao;
        $inTotalNumeracao++;
        $arTMP2[$inY] = $arTMP[$inX];
        $arTMP2[$inY]["credesc"] = "D.A. - ".$arTMP[$inX]["cod_credito"].".".$arTMP[$inX]["cod_especie"].".".$arTMP[$inX]["cod_genero"].".".$arTMP[$inX]["cod_natureza"]." / ".$arTMP[$inX]["descricao_credito"];
    }
}

$inTotalCredito = 0;
$arTMP = array();
for ($inX=0; $inX<$inTotalNumeracao; $inX++) {
    $boEncontrou = false;
    for ($inY=0; $inY<$inTotalCredito; $inY++) {
        if ($arTMP2[$inX]["credesc"] == $arTMP[$inY]["credesc"]) {
            $arTMP[$inY]["dados"][] = $arTMP2[$inX];
            $boEncontrou = true;
            break;
        }
    }

    if (!$boEncontrou) {
        $inY = $inTotalCredito;
        $inTotalCredito++;
        $arTMP[$inY]["credesc"] = $arTMP2[$inX]["credesc"];
        $arTMP[$inY]["dados"][] = $arTMP2[$inX];
    }
}

$arTMP_lo_2 = $rsListaOrigem->getElementos();
$arTMP_lo = array();
for ( $inX=0; $inX<count($arTMP_lo_2); $inX++ ) {
    if (preg_match( "/INCONSISTENCIA/", $arTMP_lo_2[$inX]['tipo_numeracao'] ) ) {
        $boIncluir = true;
        for ( $inY=0; $inY<count($arTMP_lo); $inY++ ) {
            if ( ( $arTMP_lo_2[$inX]["origem"] == $arTMP_lo[$inY]["origem"] ) && ( $arTMP_lo_2[$inX]["origem_exercicio"] == $arTMP_lo[$inY]["origem_exercicio"] ) ) {
                $boIncluir = false;
                break;
            }
        }

        if ( $boIncluir )
            $arTMP_lo[] = $arTMP_lo_2[$inX];
    }else
    if ( !preg_match( "/(D.A.)/", $arTMP_lo_2[$inX]['descricao'] ) ) {
        $boIncluir = true;
        for ( $inY=0; $inY<count($arTMP_lo); $inY++ ) {
            if ( ( $arTMP_lo_2[$inX]["origem"] == $arTMP_lo[$inY]["origem"] ) && ( $arTMP_lo_2[$inX]["origem_exercicio"] == $arTMP_lo[$inY]["origem_exercicio"] ) ) {
                $boIncluir = false;
                break;
            }
        }

        if ( $boIncluir )
            $arTMP_lo[] = $arTMP_lo_2[$inX];
    }
}

for ( $inX=0; $inX<count($arTMP); $inX++ ) {
    $arTMP_lo[] = $arTMP[$inX];
}

$rsListaOrigem->preenche( $arTMP_lo );
$rsListaOrigem->setPrimeiroElemento();

while ( !$rsListaOrigem->eof() ) {
    if ( $rsListaOrigem->getCampo('tipo') ) {
        $obRARRPagamento->obRARRCarne->obRMONConvenio->setCodigoConvenio( $rsListaOrigem->getCampo('cod_convenio') );
        if ( $rsListaOrigem->getCampo('tipo') == 'grupo' ) {
            $obRARRPagamento->obRARRCarne->obRARRParcela->roRARRLancamento->roRARRCalculo->obRARRGrupo->setCodGrupo ( $rsListaOrigem->getCampo('origem') );
            $obRARRPagamento->obRARRCarne->obRARRParcela->roRARRLancamento->roRARRCalculo->obRMONCredito->setCodCredito( null );
            $obRARRPagamento->obRARRCarne->obRARRParcela->roRARRLancamento->roRARRCalculo->obRMONCredito->setCodEspecie( null );
        } else {
            $obRARRPagamento->obRARRCarne->obRARRParcela->roRARRLancamento->roRARRCalculo->obRARRGrupo->setCodGrupo ( null );
            $arCredito = explode ('.', $rsListaOrigem->getCampo('origem') );
            $obRARRPagamento->obRARRCarne->obRARRParcela->roRARRLancamento->roRARRCalculo->obRMONCredito->setCodCredito( $arCredito[0] );
            $obRARRPagamento->obRARRCarne->obRARRParcela->roRARRLancamento->roRARRCalculo->obRMONCredito->setCodEspecie( $arCredito[1] );
            $obRARRPagamento->obRARRCarne->obRARRParcela->roRARRLancamento->roRARRCalculo->obRMONCredito->setCodGenero( $arCredito[2] );
            $obRARRPagamento->obRARRCarne->obRARRParcela->roRARRLancamento->roRARRCalculo->obRMONCredito->setCodNatureza( $arCredito[3] );
        }

        $obRARRPagamento->obRARRCarne->obRARRParcela->roRARRLancamento->roRARRCalculo->setExercicio ( $rsListaOrigem->getCampo('origem_exercicio') );
        $obRARRPagamento->setExercicio ( $rsListaOrigem->getCampo('exercicio') );
    }

    #================================================ DADOS DA ORIGEM
    //titulo
    $arTitulo1 = array();
    if ( $rsListaOrigem->getCampo('tipo') ) {
        $arTitulo1[] = array( "titulo" => $rsListaOrigem->getCampo('origem').' / '. $rsListaOrigem->getCampo('origem_exercicio').' - '.$rsListaOrigem->getCampo('descricao') );
    } else {
        $arTitulo1[] = array( "titulo" => $rsListaOrigem->getCampo('credesc') );
    }

    $rsTit1 = new Recordset;
    $rsTit1->preenche($arTitulo1);
    $rsTit1->setPrimeiroElemento();
    $obPDF->addRecordSet( $rsTit1 );
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho   ( "" , 80, 50 );
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo       ( "titulo" , 14, "B" );
    #================================================ DADOS DA ORIGEM FIM

    $flOrigemValorNormalOK = $flOrigemValorJurosOK = $flOrigemValorMultaOK = 0.00;
    $flOrigemValorDiferencaOK = $flOrigemValorCorrecaoOK = $flOrigemValorTotalOK = 0.00;
    if ( $rsListaOrigem->getCampo('tipo') ) {
        $obRARRPagamento->listarPagamentosLote ( $rsPagamentos );

    } else {
        $rsPagamentos = new RecordSet;
        $rsPagamentos->preenche( $rsListaOrigem->getCampo('dados') );
    }

    while ( !$rsPagamentos->eof() ) {

        $flOrigemValorNormalOK      += $rsPagamentos->getCampo( "valor_pago_calculo"    );
        $flOrigemValorJurosOK       += $rsPagamentos->getCampo( "juros"                 );
        $flOrigemValorMultaOK       += $rsPagamentos->getCampo( "multa"                 );
        $flOrigemValorDiferencaOK   += $rsPagamentos->getCampo( "diferenca"             );
        $flOrigemValorCorrecaoOK    += $rsPagamentos->getCampo( "correcao"              );
        $flOrigemValorTotalOK       += $rsPagamentos->getCampo( "valor_pago_normal"     );

        $rsPagamentos->proximo();

    }

    $flSomaValorNormal     += $flOrigemValorNormalOK;
    $flSomaValorJuros      += $flOrigemValorJurosOK;
    $flSomaValorMulta      += $flOrigemValorMultaOK;
    $flSomaValorDiferenca  += $flOrigemValorDiferencaOK;
    $flSomaValorCorrecao   += $flOrigemValorCorrecaoOK;
    $flSomaValorTotal      += $flOrigemValorTotalOK;

    $rsPagamentos->setPrimeiroElemento();

    $rsPagamentos->addFormatacao("valor_pago_normal", "NUMERIC_BR");
    $rsPagamentos->addFormatacao("valor_pago_calculo", "NUMERIC_BR");
    $rsPagamentos->addFormatacao("juros", "NUMERIC_BR");
    $rsPagamentos->addFormatacao("multa", "NUMERIC_BR");
    $rsPagamentos->addFormatacao("diferenca", "NUMERIC_BR");
    $rsPagamentos->addFormatacao("correcao", "NUMERIC_BR");

    #================================================ PAGAMENTOS OK
    //titulo
    $obPDF->addRecordSet( $rsPagamentos );

    // Numeração     Parcela     Origem      Inscrição   Contribuinte    Valor (R$)   Pagamento   Situação
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho   ( "Numeração", 11, 9 );
    $obPDF->addCabecalho   ( "Parcela", 6, 9 );
    $obPDF->addCabecalho   ( "Inscrição", 7, 9 );
    $obPDF->addCabecalho   ( "Contribuinte", 18, 9 );
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCabecalho   ( "Valor", 7.5, 9 );
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCabecalho   ( "Juros", 7.5, 9 );
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCabecalho   ( "Multa", 7.5, 9 );
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCabecalho   ( "Diferença", 7.5, 9 );
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCabecalho   ( "Correção", 7.5, 9 );
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCabecalho   ( "Total", 7.5, 9 );
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCabecalho   ( "Pagamento", 7.5, 9 );
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCabecalho   ( "Situação", 7.5, 9 );

    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo       ( "[numeracao]", 8 );
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCampo       ( "info_parcela", 8 );
    $obPDF->addCampo       ( "inscricao", 8 );
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo       ( "contribuinte", 8 );
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCampo       ( "valor_pago_calculo", 8 );
    $obPDF->addCampo       ( "[juros]", 8 );
    $obPDF->addCampo       ( "[multa]", 8 );
    $obPDF->addCampo       ( "diferenca", 8 );
    $obPDF->addCampo       ( "correcao", 8 );
    $obPDF->addCampo       ( "valor_pago_normal", 8 );
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCampo       ( "data_pagamento_br", 8 );
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo       ( "nom_tipo", 7 );

    $stTotal = "Total Normal:";
    $rsOrigemPagamento = new Recordset;
    $obPDF->addRecordSet( $rsOrigemPagamento );
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho   ( "", 11, 10 );
    $obPDF->addCabecalho   ( "", 7, 10 );
    $obPDF->addCabecalho   ( "", 6, 10 );
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCabecalho   ( $stTotal, 18, 9, "B" );
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCabecalho   ( number_format( $flOrigemValorNormalOK, 2, ',', '.'), 7.5, 9, "B" );
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCabecalho   ( number_format( $flOrigemValorJurosOK, 2, ',', '.'), 7.5, 9, "B" );
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCabecalho   ( number_format( $flOrigemValorMultaOK, 2, ',', '.'), 7.5, 9, "B" );
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCabecalho   ( number_format( $flOrigemValorDiferencaOK, 2, ',', '.'), 7.5, 9, "B" );
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCabecalho   ( number_format( $flOrigemValorCorrecaoOK, 2, ',', '.'), 7.5, 9, "B" );
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCabecalho   ( number_format( $flOrigemValorTotalOK, 2, ',', '.'), 7.5, 9, "B" );

    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo       ( "", 8 );
    $obPDF->addCampo       ( "", 8 );
    $obPDF->addCampo       ( "", 8 );
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCampo       ( "", 8 );
    $obPDF->addCampo       ( "", 9 );
    $obPDF->addCampo       ( "", 9 );
    $obPDF->addCampo       ( "", 9 );
    $obPDF->addCampo       ( "", 9 );
    $obPDF->addCampo       ( "", 9 );

    #================================================ PAGAMENTOS INCONSISTENTES VINCULO =========
    #$obRARRPagamento->listarPagamentosInconsistenciaLote ( $rsPagamentos );
    $rsPagamentosInconsistentes = new Recordset;
    if ( $rsListaOrigem->getCampo('tipo') ) {
        $obRARRPagamento->listaResumoLoteInconsistenteAgrupado ( $rsPagamentosInconsistentes );
    }

    $flOrigemValorInconsistenteOK = 0.00;
    if ( $rsPagamentosInconsistentes->getNumLinhas() > 0 ) {

        while ( !$rsPagamentosInconsistentes->eof() ) {
            $flOrigemValorInconsistenteOK += $rsPagamentosInconsistentes->getCampo( "valor" );
            $rsPagamentosInconsistentes->setCampo('valor', number_format ( $rsPagamentosInconsistentes->getCampo('valor'), 2, ',', '.' ));
            $rsPagamentosInconsistentes->proximo();
        }

        $flSomaValorInconsistentes += $flOrigemValorInconsistenteOK;
        $rsPagamentosInconsistentes->setPrimeiroElemento();

        //titulo
        $obPDF->addRecordSet( $rsPagamentosInconsistentes );

        // Numeração     Parcela     Origem      Inscrição       Contribuinte    Valor (R$)   Pagamento   Situação
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

    } // ================================================== FIM DOS INCONSISTENTES VINCULO

    $flSomaTotalOrigem = $flOrigemValorInconsistenteOK + $flOrigemValorTotalOK;

    $stTotal = "Total do Crédito / Grupo:";
    $rsOrigemPagamento = new Recordset;
    $obPDF->addRecordSet( $rsOrigemPagamento );

    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCabecalho   ( $stTotal, 75, 10, "B" );
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCabecalho   ( number_format ( $flSomaTotalOrigem , 2, ',', '.' ), 8, 10, "B" );

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
            $rsInconsistentes->setCampo('valor', str_replace ( ',', '.', $rsInconsistentes->getCampo( "valor" ) ) );
            $flOrigemValorInconsistente2OK += $rsInconsistentes->getCampo( "valor" );
            $rsInconsistentes->setCampo('valor', number_format ( $rsInconsistentes->getCampo('valor'), 2, ',', '.' ));
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
    $obPDF->addCabecalho   ( "Valor"               , 10, 11, "B" );
    $obPDF->addCabecalho   ( "Juros"               , 10, 11, "B" );
    $obPDF->addCabecalho   ( "Multa"               , 10, 11, "B" );
    $obPDF->addCabecalho   ( "Diferença"           , 13, 11, "B" );
    $obPDF->addCabecalho   ( "Total"               , 19, 11, "B" );

    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCampo       ( ""         , 11);
    $obPDF->addCampo       ( ""         , 11);
    $obPDF->addCampo       ( "normal"   , 11, "B" );
    $obPDF->addCampo       ( "juros"    , 11, "B" );
    $obPDF->addCampo       ( "multa"    , 11, "B" );
    $obPDF->addCampo       ( "diferenca", 11, "B" );
    $obPDF->addCampo       ( "total"    , 11, "B" );
    #==========================================================

    #==========================================================
    // AMOSTRA DE TOTAL INCONSISTENTE COM VINCULO
    #==========================================================
    $rsOrigemPagamento = new Recordset;
    $obPDF->addRecordSet    ( $rsOrigemPagamento );
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->addCabecalho    ( "", 10, 10 );
    $obPDF->addCabecalho    ( "", 30, 12, "B" );
    $obPDF->addCabecalho    ( "", 10, 11, "B" );
    $obPDF->setAlinhamento  ( "R" );
    $obPDF->addCabecalho    ( "Total Inconsistente:", 25, 12, "B" );
    $obPDF->addCabecalho    ( number_format($flSomaValorInconsistentes, 2, ',','.'), 17, 11, "B" );

    $obPDF->setAlinhamento  ( "R" );
    $obPDF->addCampo        ( "" , 11 );
    $obPDF->addCampo        ( "" , 11 );
    $obPDF->addCampo        ( "" , 11 );
    $obPDF->addCampo        ( "" , 11 );
    $obPDF->addCampo        ( "" , 11 );

    #==========================================================
    // AMOSTRA DE TOTAL INCONSISTENTE SEM VINCULO
    #==========================================================
    $rsOrigemPagamento = new Recordset;
    $obPDF->addRecordSet( $rsOrigemPagamento );
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho   ( "", 10, 10 );
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCabecalho   ( "", 30, 12, "B" );
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCabecalho   ( "Total Inconsistente sem Vínculo:", 35, 12, "B" );
    $obPDF->addCabecalho   ( number_format ($flSomaValorInconsistentes2, 2, ',', '.') , 17, 11, "B" );

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
    $obPDF->addCabecalho   ( "", 5, 11, "B" );
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCabecalho   ( "Total Geral:", 30, 13, "B" );
    $obPDF->addCabecalho   ( number_format(($flSomaValorInconsistentes + $flSomaValorInconsistentes2 + $flSomaValorTotal), 2,',','.'),22,12,"B");

    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCampo       ( "" , 11 );
    $obPDF->addCampo       ( "" , 11 );
    $obPDF->addCampo       ( "" , 11 );
    $obPDF->addCampo       ( "" , 11 );
    $obPDF->addCampo       ( "" , 11 );

$obPDF->show();
?>
