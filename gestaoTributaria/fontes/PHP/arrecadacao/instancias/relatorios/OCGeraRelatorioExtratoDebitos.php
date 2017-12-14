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
    * Página de processamento oculto e geração do relatório de Extrato de Débitos
    * Data de Criação   : 16/07/2007

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Diego Bueno Coelho

    * @ignore

    * $Id: OCGeraRelatorioExtratoDebitos.php 63839 2015-10-22 18:08:07Z franver $

    * Casos de uso: uc-05.03.23
*/

/*
$Log$
Revision 1.10  2007/08/17 16:00:50  dibueno
Bug#9927#

Revision 1.9  2007/08/17 15:47:51  dibueno
Bug#9927#

Revision 1.8  2007/08/16 18:12:49  dibueno
Bug#9927#

Revision 1.7  2007/08/15 18:14:08  dibueno
Bug#9927#

Revision 1.6  2007/08/09 21:33:43  dibueno
Bug#9873#

Revision 1.5  2007/08/01 21:06:28  dibueno
Bug#9781#

Revision 1.4  2007/08/01 13:56:54  dibueno
Bug#9793#

Revision 1.3  2007/07/16 21:00:24  dibueno
Bug #9659#

Revision 1.2  2007/07/16 18:22:55  dibueno
Bug #9659#

Revision 1.1  2007/07/16 16:03:57  dibueno
Bug #9659#

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_FW_PDF."RRelatorio.class.php"                                                    );
include_once ( CAM_FW_PDF."ListaPDF.class.php"                                                      );
include_once ( CAM_GT_ARR_NEGOCIO."RARRPagamento.class.php"                                         );
include_once ( CAM_GT_ARR_MAPEAMENTO."FARRRelatorioExtratoDebitos.class.php"                        );

$obRRelatorio = new RRelatorio;
$obPDF        = new ListaPDF("L");

$obRRelatorio->setExercicio  ( Sessao::getExercicio() );
$obRRelatorio->recuperaCabecalho( $arConfiguracao );

$obPDF->setModulo            ( "Arrecadação:"       );
$obPDF->setTitulo            ( "Créditos:"          );
$obPDF->setSubTitulo         ( "Exercício - ".Sessao::getExercicio() );
$obPDF->setUsuario           ( Sessao::getUsername());
$obPDF->setEnderecoPrefeitura( $arConfiguracao      );

$rsDados4 = new RecordSet;
$obPDF->addRecordSet    ( $rsDados4 );
$obPDF->setAlinhamento  ( "L" );
$obPDF->addCabecalho    ( "RELAÇÃO DE PARCELAS EM ABERTO", 40, 13, "B" );
$obPDF->setAlinhamento  ( "L" );
$obPDF->addCampo        ( "", 7 );

$obFARRRelatorioExtratoDebitos = new FARRRelatorioExtratoDebitos;
$arFiltro       = Sessao::read( 'filtroRelatorio' );
$stFiltroINNER  = $arFiltro['stINNER'];
$stFiltroSQL    = $arFiltro['stSQL'];

$stVinculo = Sessao::read( 'vinculo' );
$stValorVinculo = Sessao::read( 'valor_vinculo' );
$arCabecalho = array ();
$arCabecalho[] = array (
    "LabelA" => $stVinculo.":",
    "LabelB" => $stValorVinculo
);

$stExercicio = Sessao::read( 'exercicio' );
if ($stExercicio) {
    $arCabecalho[] = array (
        "LabelA" => "Exercício:",
        "LabelB" => $stExercicio
    );
}

$stTipoInscricao = Sessao::read( 'TipoInscricao' );
if ($stTipoInscricao == "II") {
    $contP = 0;
    $arProprietarios = Sessao::read( 'Proprietarios' );
    while ( $contP < count ($arProprietarios) ) {

        if ($contP == 0) {
            $arCabecalho[] = array (
                "LabelA" => "Contribuinte:",
                "LabelB" => $arProprietarios[$contP]["cgm"].' - '.$arProprietarios[$contP]["nome"]
            );
        } else {
            $arCabecalho[] = array (
                "LabelA" => "",
                "LabelB" => $arProprietarios[$contP]["cgm"].' - '.$arProprietarios[$contP]["nome"]
            );
        }
        $contP++;
    }
} elseif ($stTipoInscricao == "IE") {
    $arCabecalho[] = array (
                "LabelA" => "Endereço:",
                "LabelB" => Sessao::read( 'DadosComplementares' )
            );
}

$arCabecalho[] = array (
    "LabelA" => " ",
    "LabelB" => " "
);

$rsDados4 = new RecordSet;
$rsDados4->preenche ( $arCabecalho );
$obPDF->addRecordSet    ( $rsDados4 );
$obPDF->setQuebraPaginaLista ( false );
$obPDF->setAlinhamento  ( "L" );
$obPDF->addCabecalho    ( "", 18, 11, "B" );
$obPDF->addCabecalho    ( "", 60, 11, "B" );
$obPDF->setAlinhamento  ( "R" );
$obPDF->addCampo        ( "LabelA", 11, "B" );
$obPDF->setAlinhamento  ( "L" );
$obPDF->addCampo        ( "LabelB", 11 );

if ($arFiltro['boTipoRelatorio'] == 'Detalhado') {

    $obFARRRelatorioExtratoDebitos->recuperaRelatorioOrigem ($rsListaRelatorioOrigem, $stFiltroINNER, $stFiltroSQL);
    
    #===========  TOTAL ==================
    $flTotalGeral = 0.00;

    #======= MONTA CABECALHOS
    $arTopicos = array();
    $rsListaRelatorioOrigem->setPrimeiroElemento();
    $stLancamentoAtual = '';
    while ( !$rsListaRelatorioOrigem->eof() ) {

        if ( $stLancamentoAtual != $rsListaRelatorioOrigem->getCampo('cod_lancamento') ) {
            $arTopicos[] = array (
                "cod_lancamento"    =>  $rsListaRelatorioOrigem->getCampo('cod_lancamento'),
                "origem"            =>  $rsListaRelatorioOrigem->getCampo('origem')
            );
        }
        $stLancamentoAtual = $rsListaRelatorioOrigem->getCampo('cod_lancamento');
        $flTotalGeral += $rsListaRelatorioOrigem->getCampo( "valor" );

        $rsListaRelatorioOrigem->proximo();
    }

    $rsTopicos = new RecordSet;
    $rsTopicos->preenche($arTopicos);
    while ( !$rsTopicos->eof() ) {

        $stFiltroRel = " AND lancamento_nominal = '". $rsTopicos->getCampo('cod_lancamento') ."' \n";
        $arTopicoTMP = array();

        $arLancamentos = explode ('<br>', $rsTopicos->getCampo('origem') );
        if ( count($arLancamentos) > 1 ) {

            $contOrigem = 0;
            while ( $contOrigem < count($arLancamentos) ) {

                if ($contOrigem == 0) {
                    $arTopicoTMP[] = array(
                        "LabelA" => "Cobrança:",
                        "LabelB" => $rsTopicos->getCampo('cod_lancamento'),
                        "LabelC" => "Origens:",
                        "LabelD" => $arLancamentos[$contOrigem]
                    );
                } else {
                    $arTopicoTMP[] = array(
                        "LabelA" => "",
                        "LabelB" => "",
                        "LabelC" => "",
                        "LabelD" => $arLancamentos[$contOrigem]
                    );
                }

                $contOrigem++;
            }

        } else {
            $arTopicoTMP[] = array(
                "LabelA" => "Lançamento:",
                "LabelB" => $rsTopicos->getCampo('cod_lancamento'),
                "LabelC" => "Origem:",
                "LabelD" => $rsTopicos->getCampo('origem')
            );
        }

        # ESPAÇAMENTO
        $arTopicoTMP[] = array(
            "LabelA" => "",
            "LabelB" => "",
            "LabelC" => "",
            "LabelD" => ""
        );

        $rsDados4 = new RecordSet;
        $rsDados4->preenche ( $arTopicoTMP );
        $obPDF->addRecordSet    ( $rsDados4 );
        $obPDF->setQuebraPaginaLista ( false );
        $obPDF->setAlinhamento  ( "L" );
        $obPDF->addCabecalho    ( "", 10, 11, "B" );
        $obPDF->addCabecalho    ( "", 10, 11, "B" );
        $obPDF->addCabecalho    ( "", 10, 11, "B" );
        $obPDF->addCabecalho    ( "", 40, 11, "B" );
        $obPDF->setAlinhamento  ( "R" );
        $obPDF->addCampo        ( "LabelA", 11, "B" );
        $obPDF->setAlinhamento  ( "L" );
        $obPDF->addCampo        ( "LabelB", 11 );
        $obPDF->setAlinhamento  ( "R" );
        $obPDF->addCampo        ( "LabelC", 11, "B" );
        $obPDF->setAlinhamento  ( "L" );
        $obPDF->addCampo        ( "LabelD", 11 );

        $obFARRRelatorioExtratoDebitos->recuperaRelatorio ( $rsListaRelatorio, $stFiltroINNER, $stFiltroSQL, $stFiltroRel );

        $rsListaRelatorio->addFormatacao ( "valor", "NUMERIC_BR" );
        $rsListaRelatorio->addFormatacao ( "juros", "NUMERIC_BR" );
        $rsListaRelatorio->addFormatacao ( "multa", "NUMERIC_BR" );
        $rsListaRelatorio->addFormatacao ( "correcao", "NUMERIC_BR" );
        $rsListaRelatorio->addFormatacao ( "total", "NUMERIC_BR" );
        $rsListaRelatorio->setPrimeiroElemento();
        $obPDF->addRecordSet( $rsListaRelatorio );
        $obPDF->setQuebraPaginaLista( false );
        $obPDF->setAlinhamento ( "C" );
        $obPDF->addCabecalho   ( "Exercício"    ,10 ,11 , "B" );
        $obPDF->addCabecalho   ( "Situação"     ,10 ,11 , "B" );
        $obPDF->addCabecalho   ( "Numeração"    ,12 ,11 , "B" );
        $obPDF->addCabecalho   ( "Parcela"      ,10  ,11, "B"  );
        $obPDF->addCabecalho   ( "Venc"         ,7 ,11, "B" );
        $obPDF->addCabecalho   ( "Valor"        ,11 ,11, "B" );
        $obPDF->addCabecalho   ( "Juros"        ,10 ,11, "B" );
        $obPDF->addCabecalho   ( "Multa"        ,10 ,11, "B" );
        $obPDF->addCabecalho   ( "Correção"     ,10  ,11, "B" );
        $obPDF->addCabecalho   ( "Total"        ,13 ,11, "B" );

        $obPDF->setAlinhamento ( "C" );
        $obPDF->addCampo       ( "exercicio"    , 9 );
        $obPDF->setAlinhamento ( "C" );
        $obPDF->addCampo       ( "situacao_parcela", 9 );
        $obPDF->addCampo       ( "numeracao"    , 9 );
        $obPDF->addCampo       ( "[nr_parcela]/[total_parcelas]", 9 );
        $obPDF->addCampo       ( "vencimento_br", 9 );
        $obPDF->addCampo       ( "valor", 9 );
        $obPDF->addCampo       ( "juros", 9 );
        $obPDF->addCampo       ( "multa", 9 );
        $obPDF->addCampo       ( "correcao", 9 );
        $obPDF->addCampo       ( "total", 9, "B" );

        $rsTopicos->proximo();

    }

    $obFARRRelatorioExtratoDebitos->recuperaRelatorio ( $rsListaRelatorio, $stFiltroINNER, $stFiltroSQL, $stFiltroRel );

    $rsDados4 = new RecordSet;
    $arDadosTemp = array();
    $arDadosTemp[] = array(
        "total" => "Total: ",
        "valor" => number_format ( $flTotalGeral, 2, ',','.')
    );
    $rsDados4->preenche ( $arDadosTemp );
    $obPDF->addRecordSet    ( $rsDados4 );
    $obPDF->setQuebraPaginaLista ( false );
    $obPDF->setAlinhamento  ( "R" );
    $obPDF->addCabecalho    ( ""   , 80    , 12, "B" );
    $obPDF->addCabecalho    ( "" ,20 ,12 , "B" );
    $obPDF->setAlinhamento  ( "R" );
    $obPDF->addCampo        ( "total", 12, "B" );
    $obPDF->addCampo        ( "valor", 12, "B" );

} else { #=============== RELATORIO SIMPLES =================================

    $obFARRRelatorioExtratoDebitos->recuperaRelatorioOrigem ( $rsListaRelatorio, $stFiltroINNER, $stFiltroSQL );

    #******** TOTAL
    $flTotal = 0.00;
    $rsListaRelatorio->setPrimeiroElemento();
    while ( !$rsListaRelatorio->eof() ) {
        $flTotal += $rsListaRelatorio->getCampo( "valor" );
        $rsListaRelatorio->proximo();
    }
    #******** TOTAL FIM

    #******** FORMATANDO COLUNAS POR CAUSA DA DIVIDA
    $arRelatorio = array();
    $rsListaRelatorio->setPrimeiroElemento();
    while ( !$rsListaRelatorio->eof() ) {

        $arOrigem = explode ( '<br>' , $rsListaRelatorio->getCampo( "origem" ) );
        if ( count($arOrigem) > 1 ) {
            #insere um registro vazio, para espaçamento
            $arRelatorio[] = array (
                "origem"            => "",
                "cod_lancamento"    => "",
                "exercicio"         => "",
                "qtde"              => "",
                "valor"             => "",
            );
            $contOrigem = 1;
            while ( $contOrigem <= count($arOrigem) ) {

                if ( $contOrigem == count($arOrigem) ) {
                    $arRelatorio[] = array (
                        "origem"            => "D.A. - ".$arOrigem[($contOrigem -1)],
                        "cod_lancamento"    => $rsListaRelatorio->getCampo( "cod_lancamento" ),
                        "exercicio"         => $rsListaRelatorio->getCampo( "exercicio" ),
                        "qtde"              => $rsListaRelatorio->getCampo( "qtde" ),
                        "valor"             => $rsListaRelatorio->getCampo( "valor" ),
                    );
                } else {
                    $arRelatorio[] = array (
                        "origem"            => "D.A. - ".$arOrigem[($contOrigem -1)],
                        "cod_lancamento"    => "",
                        "exercicio"         => "",
                        "qtde"              => "",
                        "valor"             => "",
                    );
                }

                $contOrigem++;
            }

            #insere um registro vazio, para espaçamento
            $arRelatorio[] = array (
                "origem"            => "",
                "cod_lancamento"    => "",
                "exercicio"         => "",
                "qtde"              => "",
                "valor"             => "",
            );

        } else {
            $arRelatorio[] = array (
                "origem"            => $rsListaRelatorio->getCampo( "origem" ),
                "cod_lancamento"    => $rsListaRelatorio->getCampo( "cod_lancamento" ),
                "exercicio"         => $rsListaRelatorio->getCampo( "exercicio" ),
                "qtde"              => $rsListaRelatorio->getCampo( "qtde" ),
                "valor"             => $rsListaRelatorio->getCampo( "valor" ),
            );
        }

        $rsListaRelatorio->proximo();
    }
    #******** FORMATANDO COLUNAS POR CAUSA DA DIVIDA

    $rsLista = new RecordSet;
    $rsLista->preenche ( $arRelatorio );
    $rsLista->addFormatacao ( "valor", "NUMERIC_BR" );
    $rsLista->setPrimeiroElemento();
    $obPDF->addRecordSet( $rsLista );
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCabecalho   ( "Exercício"    ,10 ,11 , "B" );
    $obPDF->addCabecalho   ( "Lanc/Cobrança"   ,15 ,11 , "B" );
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho   ( "Origem"       ,30 ,11 , "B" );
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCabecalho   ( "Parcelas"     ,8  ,11, "B"  );
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCabecalho   ( "Total"        ,15 ,11, "B" );

    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCampo       ( "exercicio"    , 9 );
    $obPDF->addCampo       ( "cod_lancamento"       , 9 );
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo       ( "origem", 9 );
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCampo       ( "[qtde]", 9 );
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCampo       ( "valor" , 10 );

    $rsDados4 = new RecordSet;
    $arDadosTemp = array();
    $arDadosTemp[] = array(
        "total" => "Total: ",
        "valor" => number_format ( $flTotal, 2, ',','.')
    );
    $rsDados4->preenche ( $arDadosTemp );
    $obPDF->addRecordSet    ( $rsDados4 );
    $obPDF->setQuebraPaginaLista ( false );
    $obPDF->setAlinhamento  ( "R" );
    $obPDF->addCabecalho    ( ""        , 61 ,12 , "B" );
    $obPDF->addCabecalho    ( ""        , 15 ,12 , "B" );
    $obPDF->setAlinhamento  ( "R" );
    $obPDF->addCampo        ( "total"   , 12 , "B" );
    $obPDF->addCampo        ( "valor"   , 12 , "B" );

}

$obPDF->addFiltro( Sessao::read( 'vinculo' ), Sessao::read( 'valor_vinculo' ) );
if(Sessao::read( 'exercicio' )){
    $obPDF->addFiltro( 'Exercício', Sessao::read( 'exercicio' ) );
}

$obPDF->show();

?>
