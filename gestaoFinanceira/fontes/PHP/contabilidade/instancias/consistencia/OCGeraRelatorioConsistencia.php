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
    * Página de Geração de Relatório de Consistências
    * Data de Criação   : 24/05/2006

    * @author Cleisson Barboza

    * @ignore

    * $Id: OCGeraRelatorioConsistencia.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso : uc-02.02.32
*/

//include_once("../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php" );
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once( CAM_GF_CONT_MAPEAMENTO."FContabilidadeConsistencia.class.php"    );
include_once( CAM_FW_PDF."RRelatorio.class.php" );

$obRRelatorio = new RRelatorio;
$obPDF        = new ListaFormPDF();
$rsVazio      = new RecordSet;

// Inicializa as variaveis de sessao
$arFiltro = Sessao::read('filtroRelatorio');
$arRecordSet = Sessao::read('arRecordSet');

// Adicionar logo nos relatorios
if ( count( $arFiltro['inCodEntidade'] ) == 1 ) {
    $obRRelatorio->setCodigoEntidade( $arFiltro['inCodEntidade'][0] );
    $obRRelatorio->setExercicioEntidade ( Sessao::getExercicio() );
}

$obRRelatorio->setExercicio  ( Sessao::getExercicio() );
$obRRelatorio->recuperaCabecalho( $arConfiguracao );
$obPDF->setModulo            ( "Relatorio"   );
$obPDF->setAcao              ( "Consistência Financeira" );
$obPDF->setSubTitulo         ( "Período: ".$arFiltro['stDataInicial']." até ".$arFiltro['stDataFinal'] );
$obPDF->setUsuario           ( Sessao::getUsername() );
$obPDF->setEnderecoPrefeitura( $arConfiguracao );

/*******************************************************************
/*
/* TABELA CONSISTENCIA_1
/*
/*******************************************************************/
/*
$obPDF->addRecordSet            ( $rsVazio );
$obPDF->setAlturaCabecalho      ( 6 );
$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho   ( "Inconsistências na ordem cronológica dos empenhos:", 100, 10, 'B' );

if (sessao->transf5[1]) {
    $obPDF->addRecordSet( sessao->transf5[1] );
    $obPDF->setQuebraPaginaLista    ( false );
    $obPDF->setAlturaCabecalho      ( 5 );
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCabecalho   ( "Entidade"         , 14, 10, '','','LTRB','205,206,205' );
    $obPDF->addCabecalho   ( "Empenho"          , 14, 10, '','','LTRB','205,206,205' );
    $obPDF->addCabecalho   ( "Dt Empenho"       , 12, 10, '','','LTRB','205,206,205' );
    $obPDF->addCabecalho   ( "Dt Anul. Emp."    , 12, 10, '','','LTRB','205,206,205' );
    $obPDF->addCabecalho   ( "Dt Liquidação"    , 12, 10, '','','LTRB','205,206,205' );
    $obPDF->addCabecalho   ( "Dt Anul. Liq."    , 12, 10, '','','LTRB','205,206,205' );
    $obPDF->addCabecalho   ( "Dt Pagamento"     , 12, 10, '','','LTRB','205,206,205' );
    $obPDF->addCabecalho   ( "Dt Estorno Pag."  , 12, 10, '','','LTRB','205,206,205' );

    $obPDF->addCampo       ( "cod_entidade"         , 8 , '','','LRB');
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCampo       ( "empenho"              , 8 , '','','LRB');
    $obPDF->setAlinhamento ( "C");
    $obPDF->addCampo       ( "dt_empenho"           , 8 , '','','LRB');
    $obPDF->addCampo       ( "dt_anul_emp"          , 8 , '','','LRB');
    $obPDF->addCampo       ( "dt_liquidacao"        , 8 , '','','LRB');
    $obPDF->addCampo       ( "dt_anul_liq"          , 8 , '','','LRB');
    $obPDF->addCampo       ( "dt_pagamento"         , 8 , '','','LRB');
    $obPDF->addCampo       ( "dt_estorno_pag"       , 8 , '','','LRB');

    $obPDF->addRecordSet            ( $rsVazio      );
    $obPDF->setQuebraPaginaLista    ( false         );
    $obPDF->setAlturaCabecalho      ( 10            );
    $obPDF->setAlinhamento          ( "L"           );
    $obPDF->addCabecalho            ( "", 100, 1    );
} else {
    $obPDF->addRecordSet            ( $rsVazio      );
    $obPDF->setQuebraPaginaLista    ( false         );
    $obPDF->setAlturaCabecalho      ( 20            );
    $obPDF->setAlinhamento          ( "C"           );
    $obPDF->addCabecalho            ( "Nenhum Problema Encontrado", 100, 10  );
}
*/
/*******************************************************************
/*
/* TABELA CONSISTENCIA_2
/*
/*******************************************************************/

$obPDF->addRecordSet            ( $rsVazio );
//$obPDF->setQuebraPaginaLista    ( false         );
$obPDF->setAlturaCabecalho      ( 6 );
$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho   ( "Inconsistências entre exercício e datas de Empenhos ou Liquidações:", 100, 10, 'B' );

if ($arRecordSet[2]) {
    $obPDF->addRecordSet( $arRecordSet[2] );
    $obPDF->setQuebraPaginaLista    ( false );
    $obPDF->setAlturaCabecalho      ( 5 );
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCabecalho   ( "Entidade"         , 20, 10, '','','LTRB','205,206,205' );
    $obPDF->addCabecalho   ( "Empenho"          , 15, 10, '','','LTRB','205,206,205' );
    $obPDF->addCabecalho   ( "Data Empenho"     , 15, 10, '','','LTRB','205,206,205' );
    $obPDF->addCabecalho   ( "Exerc. Empenho"   , 15, 10, '','','LTRB','205,206,205' );
    $obPDF->addCabecalho   ( "Data Liquidação"  , 15, 10, '','','LTRB','205,206,205' );
    $obPDF->addCabecalho   ( "Exerc. Liquidação", 20, 10, '','','LTRB','205,206,205' );

    $obPDF->addCampo       ( "cod_entidade"         , 8 , '','','LTRB');
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCampo       ( "cod_empenho"          , 8 , '','','LTRB');
    $obPDF->setAlinhamento ( "C");
    $obPDF->addCampo       ( "dt_empenho"           , 8 , '','','LTRB');
    $obPDF->addCampo       ( "exercicio"            , 8 , '','','LTRB');
    $obPDF->addCampo       ( "dt_liquidacao"        , 8 , '','','LTRB');
    $obPDF->addCampo       ( "exercicio_liquidacao" , 8 , '','','LTRB');

    $obPDF->addRecordSet            ( $rsVazio      );
    $obPDF->setQuebraPaginaLista    ( false         );
    $obPDF->setAlturaCabecalho      ( 10            );
    $obPDF->setAlinhamento          ( "L"           );
    $obPDF->addCabecalho            ( "", 100, 1    );
} else {
    $obPDF->addRecordSet            ( $rsVazio      );
    $obPDF->setQuebraPaginaLista    ( false         );
    $obPDF->setAlturaCabecalho      ( 20            );
    $obPDF->setAlinhamento          ( "C"           );
    $obPDF->addCabecalho            ( "Nenhum Problema Encontrado", 100, 10  );
}

/*******************************************************************
/*
/* TABELA CONSISTENCIA_3
/*
/*******************************************************************/

$obPDF->addRecordSet            ( $rsVazio );
$obPDF->setQuebraPaginaLista    ( false );
$obPDF->setAlturaCabecalho      ( 6 );
$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho   ( "Inconsistências entre exercício do Lote e data de Lote:", 100, 10, 'B' );

if ($arRecordSet[3]) {
    $obPDF->addRecordSet( $arRecordSet[3] );
    $obPDF->setQuebraPaginaLista    ( false );
    $obPDF->setAlturaCabecalho      ( 5 );
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCabecalho   ( "Entidade"         , 20, 10, '','','LTRB','205,206,205' );
    $obPDF->addCabecalho   ( "Cod Lote"         , 20, 10, '','','LTRB','205,206,205' );
    $obPDF->addCabecalho   ( "Exercicio Lote"   , 20, 10, '','','LTRB','205,206,205' );
    $obPDF->addCabecalho   ( "Data Lote"        , 20, 10, '','','LTRB','205,206,205' );
    $obPDF->addCabecalho   ( "Tipo"             , 20, 10, '','','LTRB','205,206,205' );

    $obPDF->addCampo       ( "cod_entidade"     , 8 , '','','LTRB');
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCampo       ( "cod_lote"         , 8 , '','','LTRB');
    $obPDF->setAlinhamento ( "C");
    $obPDF->addCampo       ( "exercicio"        , 8 , '','','LTRB');
    $obPDF->addCampo       ( "dt_lote"          , 8 , '','','LTRB');
    $obPDF->addCampo       ( "tipo"             , 8 , '','','LTRB');

    $obPDF->addRecordSet            ( $rsVazio      );
    $obPDF->setQuebraPaginaLista    ( false         );
    $obPDF->setAlturaCabecalho      ( 10            );
    $obPDF->setAlinhamento          ( "L"           );
    $obPDF->addCabecalho            ( "", 100, 1    );
} else {
    $obPDF->addRecordSet            ( $rsVazio      );
    $obPDF->setQuebraPaginaLista    ( false         );
    $obPDF->setAlturaCabecalho      ( 20            );
    $obPDF->setAlinhamento          ( "C"           );
    $obPDF->addCabecalho            ( "Nenhum Problema Encontrado", 100, 10  );
}

/*******************************************************************
/*
/* TABELA CONSISTENCIA_4
/*
/*******************************************************************/

$obPDF->addRecordSet            ( $rsVazio );
$obPDF->setQuebraPaginaLista    ( false );
$obPDF->setAlturaCabecalho      ( 6 );
$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho   ( "Inconsistências na data de Lote:", 100, 10, 'B' );

if ($arRecordSet[4]) {
    $obPDF->addRecordSet( $arRecordSet[4] );
    $obPDF->setQuebraPaginaLista    ( false );
    $obPDF->setAlturaCabecalho      ( 5 );
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCabecalho   ( "Entidade"         , 12, 10, '','','LTRB','205,206,205' );
    $obPDF->addCabecalho   ( "Cod Lote"         , 12, 10, '','','LTRB','205,206,205' );
    $obPDF->addCabecalho   ( "Dt Empenho"       , 12, 10, '','','LTRB','205,206,205' );
    $obPDF->addCabecalho   ( "Dt Lote"          , 12, 10, '','','LTRB','205,206,205' );
    $obPDF->addCabecalho   ( "Complemento"      , 52, 10, '','','LTRB','205,206,205' );

    $obPDF->addCampo       ( "cod_entidade"     , 8 , '','','LTRB');
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCampo       ( "cod_lote"         , 8 , '','','LTRB');
    $obPDF->setAlinhamento ( "C");
    $obPDF->addCampo       ( "dt_empenho"       , 8 , '','','LTRB');
    $obPDF->addCampo       ( "dt_lote"          , 8 , '','','LTRB');
    $obPDF->addCampo       ( "complemento"      , 8 , '','','LTRB');

    $obPDF->addRecordSet            ( $rsVazio      );
    $obPDF->setQuebraPaginaLista    ( false         );
    $obPDF->setAlturaCabecalho      ( 10            );
    $obPDF->setAlinhamento          ( "L"           );
    $obPDF->addCabecalho            ( "", 100, 1    );
} else {
    $obPDF->addRecordSet            ( $rsVazio      );
    $obPDF->setQuebraPaginaLista    ( false         );
    $obPDF->setAlturaCabecalho      ( 20            );
    $obPDF->setAlinhamento          ( "C"           );
    $obPDF->addCabecalho            ( "Nenhum Problema Encontrado", 100, 10  );
}

/*******************************************************************
/*
/* TABELA CONSISTENCIA_5
/*
/*******************************************************************/

$obPDF->addRecordSet            ( $rsVazio );
$obPDF->setQuebraPaginaLista    ( false );
$obPDF->setAlturaCabecalho      ( 6 );
$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho   ( "Inconsistências na regra de valores (empenhado >= anulado) >= (liquidado >= anulado) >= (pago >= estornado):", 100, 10, 'B' );

if ($arRecordSet[5]) {
    $obPDF->addRecordSet( $arRecordSet[5] );
    $obPDF->setQuebraPaginaLista    ( false );
    $obPDF->setAlturaCabecalho      ( 5 );
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCabecalho   ( "Entidade"         , 10, 10, '','','LTRB','205,206,205' );
    $obPDF->addCabecalho   ( "Empenho"          , 10, 10, '','','LTRB','205,206,205' );
    $obPDF->addCabecalho   ( "Vlr. Empenhado"   , 16, 10, '','','LTRB','205,206,205' );
    $obPDF->addCabecalho   ( "Vlr. Anulado"     , 12, 10, '','','LTRB','205,206,205' );
    $obPDF->addCabecalho   ( "Vlr. Liquidado"   , 13, 10, '','','LTRB','205,206,205' );
    $obPDF->addCabecalho   ( "Vlr. Anulado"     , 13, 10, '','','LTRB','205,206,205' );
    $obPDF->addCabecalho   ( "Vlr. Pago"        , 13, 10, '','','LTRB','205,206,205' );
    $obPDF->addCabecalho   ( "Vlr. Estornado"   , 13, 10, '','','LTRB','205,206,205' );

    $obPDF->addCampo       ( "cod_entidade"     , 8 , '','','LTRB');
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCampo       ( "empenho"          , 8 , '','','LTRB');
    $obPDF->addCampo       ( "vlremp"           , 8 , '','','LTRB');
    $obPDF->addCampo       ( "vlranu"           , 8 , '','','LTRB');
    $obPDF->addCampo       ( "vlrliq"           , 8 , '','','LTRB');
    $obPDF->addCampo       ( "vlrliqanu"        , 8 , '','','LTRB');
    $obPDF->addCampo       ( "vlrpag"           , 8 , '','','LTRB');
    $obPDF->addCampo       ( "vlrpagest"        , 8 , '','','LTRB');

    $obPDF->addRecordSet            ( $rsVazio      );
    $obPDF->setQuebraPaginaLista    ( false         );
    $obPDF->setAlturaCabecalho      ( 10            );
    $obPDF->setAlinhamento          ( "L"           );
    $obPDF->addCabecalho            ( "", 100, 1    );
} else {
    $obPDF->addRecordSet            ( $rsVazio      );
    $obPDF->setQuebraPaginaLista    ( false         );
    $obPDF->setAlturaCabecalho      ( 20            );
    $obPDF->setAlinhamento          ( "C"           );
    $obPDF->addCabecalho            ( "Nenhum Problema Encontrado", 100, 10  );

}

/*******************************************************************
/*
/* TABELA CONSISTENCIA_6
/*
/*******************************************************************/

$obPDF->addRecordSet            ( $rsVazio );
$obPDF->setQuebraPaginaLista    ( false );
$obPDF->setAlturaCabecalho      ( 6 );
$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho   ( "Inconsistências entre valores do empenho (empenho, liquidação, pagamento) e valores lançados na contabilidade:", 100, 10, 'B' );

if ($arRecordSet[6]) {
    $obPDF->addRecordSet( $arRecordSet[6] );
    $obPDF->setQuebraPaginaLista    ( false );
    $obPDF->setAlturaCabecalho      ( 5 );
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCabecalho   ( "Entidade"         , 15, 10, '','','LTRB','205,206,205' );
    $obPDF->addCabecalho   ( "Empenho"          , 15, 10, '','','LTRB','205,206,205' );
    $obPDF->addCabecalho   ( "Vlr. no Empenho"  , 15, 10, '','','LTRB','205,206,205' );
    $obPDF->addCabecalho   ( "Vlr. na Contab."  , 15, 10, '','','LTRB','205,206,205' );
    $obPDF->addCabecalho   ( "Descrição"        , 40, 10, '','','LTRB','205,206,205' );

    $obPDF->addCampo       ( "cod_entidade"     , 8 , '','','LTBR');
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCampo       ( "empenho"          , 8 , '','','LTBR');
    $obPDF->addCampo       ( "vl_empenho"       , 8 , '','','LTBR');
    $obPDF->addCampo       ( "vl_contabilidade" , 8 , '','','LTBR');
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCampo       ( "descricao"        , 8 , '','','LTBR');

    $obPDF->addRecordSet            ( $rsVazio );
    $obPDF->setQuebraPaginaLista    ( false );
    $obPDF->setAlturaCabecalho      ( 10 );
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho   ( "", 100, 1 );

} else {
    $obPDF->addRecordSet            ( $rsVazio      );
    $obPDF->setQuebraPaginaLista    ( false         );
    $obPDF->setAlturaCabecalho      ( 20            );
    $obPDF->setAlinhamento          ( "C"           );
    $obPDF->addCabecalho            ( "Nenhum Problema Encontrado", 100, 10  );

}

/*******************************************************************
/*
/* TABELA CONSISTENCIA_7
/*
/*******************************************************************/

$obPDF->addRecordSet            ( $rsVazio );
$obPDF->setQuebraPaginaLista    ( false );
$obPDF->setAlturaCabecalho      ( 6 );
$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho   ( "Inconsistências no sinal do valor em lançamentos de crédito e débito:", 100, 10, 'B' );

if ($arRecordSet[7]) {
    $obPDF->addRecordSet( $arRecordSet[7] );
    $obPDF->setQuebraPaginaLista    ( false );
    $obPDF->setAlturaCabecalho      ( 5 );
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCabecalho   ( "Entidade"         , 16, 10, '','','LTBR','205,206,205' );
    $obPDF->addCabecalho   ( "Lote"             , 16, 10, '','','LTBR','205,206,205' );
    $obPDF->addCabecalho   ( "Sequencia"        , 16, 10, '','','LTBR','205,206,205' );
    $obPDF->addCabecalho   ( "Tipo"             , 16, 10, '','','LTBR','205,206,205' );
    $obPDF->addCabecalho   ( "Valor Débito"     , 18, 10, '','','LTBR','205,206,205' );
    $obPDF->addCabecalho   ( "Valor Crédito"    , 18, 10, '','','LTBR','205,206,205' );

    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCampo       ( "cod_entidade"         , 8 , '','','LTBR');
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCampo       ( "[cod_lote]/[exercicio]",8 , '','','LTBR');
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCampo       ( "sequencia"            , 8 , '','','LTBR');
    $obPDF->addCampo       ( "tipo"                 , 8 , '','','LTBR');
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCampo       ( "vl_debito"            , 8 , '','','LTBR');
    $obPDF->addCampo       ( "vl_credito"           , 8 , '','','LTBR');

    $obPDF->addRecordSet            ( $rsVazio );
    $obPDF->setQuebraPaginaLista    ( false );
    $obPDF->setAlturaCabecalho      ( 10 );
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho   ( "", 100, 1 );

} else {
    $obPDF->addRecordSet            ( $rsVazio      );
    $obPDF->setQuebraPaginaLista    ( false         );
    $obPDF->setAlturaCabecalho      ( 20            );
    $obPDF->setAlinhamento          ( "C"           );
    $obPDF->addCabecalho            ( "Nenhum Problema Encontrado", 100, 10  );

}
/*******************************************************************
/*
/* TABELA CONSISTENCIA_8
/*
/*******************************************************************/

$obPDF->addRecordSet            ( $rsVazio );
$obPDF->setQuebraPaginaLista    ( false );
$obPDF->setAlturaCabecalho      ( 6 );
$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho   ( "Inconsistências na quantidade de lançamentos contábeis:", 100, 10, 'B' );

if ($arRecordSet[8]) {
    $obPDF->addRecordSet( $arRecordSet[8] );
    $obPDF->setQuebraPaginaLista    ( false );
    $obPDF->setAlturaCabecalho      ( 5 );
    $obPDF->setAlinhamento ( "C" );

    $obPDF->addCabecalho   ( "Entidade"         , 10, 10, '','','LTRB','205,206,205' );
    $obPDF->addCabecalho   ( "Cod lote"         , 10, 10, '','','LTRB','205,206,205' );
    $obPDF->addCabecalho   ( "Tipo"             , 10, 10, '','','LTRB','205,206,205' );
    $obPDF->addCabecalho   ( "Lançamentos"      , 13, 10, '','','LTRB','205,206,205' );
    $obPDF->addCabecalho   ( "Lanc. Corretos"   , 20, 10, '','','LTRB','205,206,205' );
    $obPDF->addCabecalho   ( "Complemento"      , 37, 10, '','','LTRB','205,206,205' );

    $obPDF->addCampo       ( "cod_entidade"     , 8 , '','','LTBR');
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCampo       ( "cod_lote"         , 8 , '','','LTBR');
    $obPDF->setAlinhamento ( "C");
    $obPDF->addCampo       ( "tipo"             , 8 , '','','LTBR');
    $obPDF->addCampo       ( "lancamentos"      , 8 , '','','LTBR');
    $obPDF->addCampo       ( "lancamentos_corretos" , 8 , '','','LTBR');
    $obPDF->addCampo       ( "complemento"      , 8 , '','','LTBR');

    $obPDF->addRecordSet            ( $rsVazio );
    $obPDF->setQuebraPaginaLista    ( false );
    $obPDF->setAlturaCabecalho      ( 10 );
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho   ( "", 100, 1 );

} else {
    $obPDF->addRecordSet            ( $rsVazio      );
    $obPDF->setQuebraPaginaLista    ( false         );
    $obPDF->setAlturaCabecalho      ( 20            );
    $obPDF->setAlinhamento          ( "C"           );
    $obPDF->addCabecalho            ( "Nenhum Problema Encontrado", 100, 10  );

}

/*******************************************************************
/*
/* tabela consistencia_9
/*
/*******************************************************************/

$obPDF->addRecordSet            ( $rsVazio );
$obPDF->setQuebraPaginaLista    ( false         );
$obPDF->setAlturaCabecalho      ( 6 );
$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho   ( "Lançamentos existentes na contabilidade sem correspondência no empenho:", 100, 10, 'B' );

if ($arRecordSet[9]) {
    $obPDF->addRecordSet( $arRecordSet[9] );
    $obPDF->setQuebraPaginaLista    ( false );
    $obPDF->setAlturaCabecalho      ( 5 );
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCabecalho   ( "Entidade"         , 20, 10, '','','LTRB','205,206,205' );
    $obPDF->addCabecalho   ( "Cod lote"         , 20, 10, '','','LTRB','205,206,205' );
    $obPDF->addCabecalho   ( "Tipo"             , 20, 10, '','','LTRB','205,206,205' );
    $obPDF->addCabecalho   ( "Complemento"      , 40, 10, '','','LTRB','205,206,205' );

    $obPDF->addCampo       ( "cod_entidade"     , 8 , '','','LTRB');
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCampo       ( "cod_lote"         , 8 , '','','LTRB');
    $obPDF->setAlinhamento ( "C");
    $obPDF->addCampo       ( "tipo"             , 8 , '','','LTRB');
    $obPDF->addCampo       ( "complemento"      , 8 , '','','LTRB');
} else {
    $obPDF->addRecordSet            ( $rsVazio      );
    $obPDF->setQuebraPaginaLista    ( false         );
    $obPDF->setAlturaCabecalho      ( 20            );
    $obPDF->setAlinhamento          ( "C"           );
    $obPDF->addCabecalho            ( "Nenhum Problema Encontrado", 100, 10  );
}

/*******************************************************************
/*
/* tabela consistencia_10
/*
/*******************************************************************/

$obPDF->addRecordSet            ( $rsVazio );
$obPDF->setQuebraPaginaLista    ( false         );
$obPDF->setAlturaCabecalho      ( 6 );
$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho   ( "Inconsistências na Natureza do Saldo das Contas Contábeis:", 100, 10, 'B' );

if ($arRecordSet[10]) {
    $obPDF->addRecordSet( $arRecordSet[10] );
    $obPDF->setQuebraPaginaLista    ( false );
    $obPDF->setAlturaCabecalho      ( 5 );
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCabecalho   ( "Entidade"         , 9, 10, '','','LTRB','205,206,205');
    $obPDF->addCabecalho   ( "Reduzido"         , 9, 10, '','','LTRB','205,206,205');
    $obPDF->addCabecalho   ( "Tipo"             , 5, 10, '','','LTRB','205,206,205');
    $obPDF->addCabecalho   ( "Cod Estrutural"   , 18, 10, '','','LTRB','205,206,205');
    $obPDF->addCabecalho   ( "Nome"             , 60, 10, '','','LTRB','205,206,205');

    $obPDF->addCampo       ( "cod_entidade"          , 8 , '','','LTRB');
    $obPDF->addCampo       ( "cod_conta"             , 8 , '','','LTRB');
    $obPDF->addCampo       ( "natureza_saldo"        , 8 , '','','LTRB');
    $obPDF->addCampo       ( "cod_estrutural"        , 8 , '','','LTRB');
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo       ( "nome"                  , 8 , '','','LTRB');

} else {
    $obPDF->addRecordSet            ( $rsVazio      );
    $obPDF->setQuebraPaginaLista    ( false         );
    $obPDF->setAlturaCabecalho      ( 20            );
    $obPDF->setAlinhamento          ( "C"           );
    $obPDF->addCabecalho            ( "Nenhum Problema Encontrado", 100, 10  );
}

/*******************************************************************
/*
/* tabela consistencia_11
/*
/*******************************************************************/

$obPDF->addRecordSet            ( $rsVazio );
$obPDF->setQuebraPaginaLista    ( false         );
$obPDF->setAlturaCabecalho      ( 6 );
$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho   ( "Inconsistências nos Valores do Saldo das Contas Contábeis:", 100, 10, 'B' );

if ($arRecordSet[11]) {
    $obPDF->addRecordSet( $arRecordSet[11] );
    $obPDF->setQuebraPaginaLista    ( false );
    $obPDF->setAlturaCabecalho      ( 5 );
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCabecalho   ( "Entidade"          , 9, 10,  '', '','LTRB','205,206,205');
    $obPDF->addCabecalho   ( "Reduzido/Conta"              , 60, 10, '','','LTRB','205,206,205');
    $obPDF->addCabecalho   ( "Natureza do Saldo" , 18, 10, '', '','LTRB','205,206,205');
    $obPDF->addCabecalho   ( "Saldo Atual" , 15, 10, '', '','LTRB','205,206,205');

    $obPDF->addCampo       ( "cod_entidade"          , 8 , '','','LTRB');
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo       ( "nom_conta"        , 8 , '','','LTRB');
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCampo       ( "natureza_saldo"        , 8 , '','','LTRB');
    $obPDF->addCampo       ( "natureza_atual"       , 8 , '','','LTRB');

} else {
    $obPDF->addRecordSet            ( $rsVazio      );
    $obPDF->setQuebraPaginaLista    ( false         );
    $obPDF->setAlturaCabecalho      ( 20            );
    $obPDF->setAlinhamento          ( "C"           );
    $obPDF->addCabecalho            ( "Nenhum Problema Encontrado", 100, 10  );
}

$obPDF->show();
