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
    * Página Oculta de Geração do Relatório Resumo Execução de Restos a Pagar
    * Data de Criação   : 24/02/2016

    * @author Analista: Valtair Santos
    * @author Desenvolvedor: Michel Teixeira

    * @ignore

    $Id: OCGeraRelatorioResumoExecucaoRP.php 65308 2016-05-11 20:00:27Z jean $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../config.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once CLA_LISTA_MPDF;

$arDados        = Sessao::read('arDados');

$inCodEntidades = $arDados['inCodEntidade'];
$stDataInicial  = $arDados['stDataInicial'];
$stDataFinal    = $arDados['stDataFinal'];

$obListaMPDF = new ListaMPDF();
$obListaMPDF->setNomeRelatorio("Resumo Execucao Restos a Pagar");
$obListaMPDF->setFormatoFolha("A4-L");
$obListaMPDF->setCodGestao( 2 );
$obListaMPDF->setCodModulo( 10 );
$obListaMPDF->setCodEntidades($inCodEntidades);
$obListaMPDF->setDataInicio($stDataInicial);
$obListaMPDF->setDataFinal($stDataFinal);
$obListaMPDF->setTipoSaida('D');

if($arDados['rsAssinaturas'])
    $obListaMPDF->addAssinatura($arDados['rsAssinaturas']);

if(isset($arDados['filtro']) && is_array($arDados['filtro'])){
    foreach ($arDados['filtro'] as $chave => $value) {
        $obListaMPDF->addFiltro( $value['titulo'], $value['valor'] );
    }
}

$arTotalExercicio = $arDados['total_exercicio'];
if(is_array($arTotalExercicio) && count($arTotalExercicio) > 0){
    foreach ($arTotalExercicio as $stNomEntidade => $execucaoRP) {
        $rsRecordSetLista = new RecordSet();
        $rsRecordSetLista->preenche($execucaoRP);

        $obListaMPDF->addLista( $rsRecordSetLista );

        $obListaMPDF->addCabecalho($stNomEntidade             , "L",    "", 10, 1, "", "FALSE", "", "border font_weight_bold"                );

        $obListaMPDF->addCabecalho(""                         , "C",    "",  1, 1, "", "TRUE" , "", "border_top border_right border_left"    );
        $obListaMPDF->addCabecalho("SALDO PERÍODO ANTERIOR"   , "C",    "",  3, 1, "", "FALSE", "", "border_right border_left"               );
        $obListaMPDF->addCabecalho("MOVIMENTAÇÕES NO PERÍODO" , "C",    "",  3, 1, "", "FALSE", "", "border_right border_left"               );
        $obListaMPDF->addCabecalho("SALDO PERÍODO ATUAL"      , "C",    "",  3, 1, "", "FALSE", "", "border_right border_left"               );

        $obListaMPDF->addCabecalho("EXERCÍCIO"                , "C", "10%",  1, 1, "", "TRUE" , "", "border_bottom border_right border_left" );
        $obListaMPDF->addCabecalho("EMPENHADO"                , "C", "10%",  1, 1, "", "FALSE", "", "border_left"                            );
        $obListaMPDF->addCabecalho("A LIQUIDAR"               , "C", "10%",  1, 1, "", "FALSE", "", ""                                       );
        $obListaMPDF->addCabecalho("A PAGAR LIQUIDADO"        , "C", "10%",  1, 1, "", "FALSE", "", "border_right"                           );
        $obListaMPDF->addCabecalho("ANULADO"                  , "C", "10%",  1, 1, "", "FALSE", "", "border_left"                            );
        $obListaMPDF->addCabecalho("LIQUIDADO"                , "C", "10%",  1, 1, "", "FALSE", "", ""                                       );
        $obListaMPDF->addCabecalho("PAGO"                     , "C", "10%",  1, 1, "", "FALSE", "", "border_right"                           );
        $obListaMPDF->addCabecalho("EMPENHADO"                , "C", "10%",  1, 1, "", "FALSE", "", "border_left"                            );
        $obListaMPDF->addCabecalho("A LIQUIDAR"               , "C", "10%",  1, 1, "", "FALSE", "", ""                                       );
        $obListaMPDF->addCabecalho("A PAGAR LIQUIDADO"        , "C", "10%",  1, 1, "", "FALSE", "", "border_right"                           );

        $obListaMPDF->addCampo("exercicio"                    , "C" );
        $obListaMPDF->addCampo("empenhado"                    , "R", "", 1, 1, "", "NUMERIC_BR");
        $obListaMPDF->addCampo("aliquidar"                    , "R", "", 1, 1, "", "NUMERIC_BR");
        $obListaMPDF->addCampo("liquidadoapagar"              , "R", "", 1, 1, "", "NUMERIC_BR");
        $obListaMPDF->addCampo("anulado"                      , "R", "", 1, 1, "", "NUMERIC_BR");
        $obListaMPDF->addCampo("liquidado"                    , "R", "", 1, 1, "", "NUMERIC_BR");
        $obListaMPDF->addCampo("pagamento"                    , "R", "", 1, 1, "", "NUMERIC_BR");
        $obListaMPDF->addCampo("empenhado_saldo"              , "R", "", 1, 1, "", "NUMERIC_BR");
        $obListaMPDF->addCampo("aliquidar_saldo"              , "R", "", 1, 1, "", "NUMERIC_BR");
        $obListaMPDF->addCampo("liquidadoapagar_saldo"        , "R", "", 1, 1, "", "NUMERIC_BR");

        $obListaMPDF->addRodape("SUB-TOTAL");
        $obListaMPDF->addRodape($arDados['total_exercicio_entidade'][$stNomEntidade]['empenhado']             , "R", "", 1, 1, "", "NUMERIC_BR");
        $obListaMPDF->addRodape($arDados['total_exercicio_entidade'][$stNomEntidade]['aliquidar']             , "R", "", 1, 1, "", "NUMERIC_BR");
        $obListaMPDF->addRodape($arDados['total_exercicio_entidade'][$stNomEntidade]['liquidadoapagar']       , "R", "", 1, 1, "", "NUMERIC_BR");
        $obListaMPDF->addRodape($arDados['total_exercicio_entidade'][$stNomEntidade]['anulado']               , "R", "", 1, 1, "", "NUMERIC_BR");
        $obListaMPDF->addRodape($arDados['total_exercicio_entidade'][$stNomEntidade]['liquidado']             , "R", "", 1, 1, "", "NUMERIC_BR");
        $obListaMPDF->addRodape($arDados['total_exercicio_entidade'][$stNomEntidade]['pagamento']             , "R", "", 1, 1, "", "NUMERIC_BR");
        $obListaMPDF->addRodape($arDados['total_exercicio_entidade'][$stNomEntidade]['empenhado_saldo']       , "R", "", 1, 1, "", "NUMERIC_BR");
        $obListaMPDF->addRodape($arDados['total_exercicio_entidade'][$stNomEntidade]['aliquidar_saldo']       , "R", "", 1, 1, "", "NUMERIC_BR");
        $obListaMPDF->addRodape($arDados['total_exercicio_entidade'][$stNomEntidade]['liquidadoapagar_saldo'] , "R", "", 1, 1, "", "NUMERIC_BR");
    }
}

$arTotal = $arDados['total'];
if(is_array($arTotal) && count($arTotal) > 0){
    $rsRecordSetLista = new RecordSet();
    $rsRecordSetLista->preenche($arTotal);

    $obListaMPDF->addLista( $rsRecordSetLista );

    $obListaMPDF->addCabecalho("TOTAL GERAL"              , "C", "10%", 1, 3);
    $obListaMPDF->addCabecalho("SALDO PERÍODO ANTERIOR"   , "C",    "", 3, 1, "", "FALSE", "", "border_right border_left");
    $obListaMPDF->addCabecalho("MOVIMENTAÇÕES NO PERÍODO" , "C",    "", 3, 1, "", "FALSE", "", "border_right border_left");
    $obListaMPDF->addCabecalho("SALDO PERÍODO ATUAL"      , "C",    "", 3, 1, "", "FALSE", "", "border_right border_left");

    $obListaMPDF->addCabecalho("EMPENHADO"                , "C", "10%", 1, 1, "", "TRUE" , "", "border_left"             );
    $obListaMPDF->addCabecalho("A LIQUIDAR"               , "C", "10%", 1, 1, "", "FALSE", "", ""                        );
    $obListaMPDF->addCabecalho("A PAGAR LIQUIDADO"        , "C", "10%", 1, 1, "", "FALSE", "", "border_right"            );
    $obListaMPDF->addCabecalho("ANULADO"                  , "C", "10%", 1, 1, "", "FALSE", "", "border_left"             );
    $obListaMPDF->addCabecalho("LIQUIDADO"                , "C", "10%", 1, 1, "", "FALSE", "", ""                        );
    $obListaMPDF->addCabecalho("PAGO"                     , "C", "10%", 1, 1, "", "FALSE", "", "border_right"            );
    $obListaMPDF->addCabecalho("EMPENHADO"                , "C", "10%", 1, 1, "", "FALSE", "", "border_left"             );
    $obListaMPDF->addCabecalho("A LIQUIDAR"               , "C", "10%", 1, 1, "", "FALSE", "", ""                        );
    $obListaMPDF->addCabecalho("A PAGAR LIQUIDADO"        , "C", "10%", 1, 1, "", "FALSE", "", "border_right"            );

    $obListaMPDF->addCampo("empenhado"             , "R", "", 1, 1, "", "NUMERIC_BR");
    $obListaMPDF->addCampo("aliquidar"             , "R", "", 1, 1, "", "NUMERIC_BR");
    $obListaMPDF->addCampo("liquidadoapagar"       , "R", "", 1, 1, "", "NUMERIC_BR");
    $obListaMPDF->addCampo("anulado"               , "R", "", 1, 1, "", "NUMERIC_BR");
    $obListaMPDF->addCampo("liquidado"             , "R", "", 1, 1, "", "NUMERIC_BR");
    $obListaMPDF->addCampo("pagamento"             , "R", "", 1, 1, "", "NUMERIC_BR");
    $obListaMPDF->addCampo("empenhado_saldo"       , "R", "", 1, 1, "", "NUMERIC_BR");
    $obListaMPDF->addCampo("aliquidar_saldo"       , "R", "", 1, 1, "", "NUMERIC_BR");
    $obListaMPDF->addCampo("liquidadoapagar_saldo" , "R", "", 1, 1, "", "NUMERIC_BR");
}

$obListaMPDF->geraRelatorioMPDF();
?>
