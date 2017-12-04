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
    * Página de Frame Oculto para Consulta de Divida Ativa
    * Data de Criação   : 23/02/2007

    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Fernando Piccini Cercato

    * @ignore

    * $Id: OCConsultaInscricao.php 65864 2016-06-22 20:56:43Z evandro $

    * Casos de uso: uc-05.04.09

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaAtiva.class.php" );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATEmissaoDocumento.class.php" );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaDocumento.class.php" );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaParcela.class.php" );

switch ($_REQUEST['stCtrl']) {
    case "impressaoDocumento":
        include_once ( CAM_OOPARSER."tbs_class.php" );
        include_once ( CAM_OOPARSER."tbsooo_class.php" );

        $stTipoDocumento = $_REQUEST["stNomeArquivoAGT"];

        $inNumParcelamento = $_REQUEST["inNumParcelamento"];

        $obTDATEmissaoDocumento = new TDATEmissaoDocumento;

        Sessao::setTrataExcecao( true );
        Sessao::getTransacao()->setMapeamento( $obTDATEmissaoDocumento );

            $stFiltro = " WHERE cod_documento = ".$_REQUEST["inCodDocumento"]." AND num_documento = ".$_REQUEST["inNumDocumento"]." AND cod_tipo_documento = ".$_REQUEST["inCodTipoDocumento"]." AND exercicio = ".$_REQUEST["inExercicio"];
            $obTDATEmissaoDocumento->recuperaTodos( $rsTotalEmissao, $stFiltro, " ORDER BY num_emissao DESC LIMIT 1 " );
            if ( $rsTotalEmissao->Eof() )
                $inNumEmissao = 1;
            else
                $inNumEmissao = $rsTotalEmissao->getCampo("num_emissao")+1;

            $obTDATEmissaoDocumento->setDado( "cod_documento", $_REQUEST["inCodDocumento"] );
            $obTDATEmissaoDocumento->setDado( "num_documento", $_REQUEST["inNumDocumento"] );
            $obTDATEmissaoDocumento->setDado( "num_emissao", $inNumEmissao );
            $obTDATEmissaoDocumento->setDado( "numcgm_usuario", Sessao::read('numCgm') );
            $obTDATEmissaoDocumento->setDado( "cod_tipo_documento", $_REQUEST["inCodTipoDocumento"] );
            $obTDATEmissaoDocumento->setDado( "exercicio", $_REQUEST["inExercicio"] );
            $obTDATEmissaoDocumento->setDado( "num_parcelamento", $_REQUEST["inNumParcelamento"] );
            $obTDATEmissaoDocumento->inclusao();

        Sessao::encerraExcecao();

        if ( ( $stTipoDocumento == "termoAssuncao.agt" ) || ( $stTipoDocumento == "reqBenDev.agt" ) || ( $stTipoDocumento == "reqBenTerc.agt" ) || ( $stTipoDocumento == "termoConfissao.agt" ) ) {
            $stFiltro = " WHERE ddp.num_parcelamento in ( ".$inNumParcelamento." ) ";
            $obTDATDividaAtiva = new TDATDividaAtiva;
            $obTDATDividaAtiva->recuperaConsultaTermoParcelamento( $rsDados, $stFiltro );
            $rsDados->ordena("imposto", "ASC", SORT_STRING );
            $obTDATDividaAtiva->RecuperaMatriculaCGM( $rsDado2, Sessao::read('numCgm') );

            $obTDATDividaAtiva->recuperaConsultaTermoConfissao( $rsDados3, $rsDados->getCampo("num_parcelamento") );

            $stPagar = SistemaLegado::extenso( $rsDados->getCampo("tpsd") );
            $rsDados->setCampo( "tpsd", "R$ ".number_format( $rsDados->getCampo("tpsd"), 2, ',', '.' )." (".$stPagar.")" );

            $stPagar = SistemaLegado::extenso( $rsDados->getCampo("total_pagar") );
            $rsDados->setCampo( "total_pagar", "R$ ".number_format( $rsDados->getCampo("total_pagar"), 2, ',', '.' )." (".$stPagar.")" );

            $stPagar = SistemaLegado::extenso( $rsDados->getCampo("valor_parcela") );
            $rsDados->setCampo( "valor_parcela", "R$ ". number_format( $rsDados->getCampo("valor_parcela"), 2, ',', '.' )." (".$stPagar.")" );

            $stPagar = SistemaLegado::extenso( $rsDados->getCampo("parcelas") );
            $arPagar = explode( " ", $stPagar );
            $rsDados->setCampo( "parcelas", $rsDados->getCampo("parcelas")." (".$arPagar[1].")" );

            $inX = 0;
            while ( !$rsDados->Eof() ) {
                if ( !$inX )
                    $stPagar = $rsDados->getCampo("imposto");
                else
                    $stPagar .= ", ".$rsDados->getCampo("imposto");

                $inX++;
                $rsDados->proximo();
            }

            $rsDados->setPrimeiroElemento();

            if ( $inX > 1 )
                $rsDados->setCampo("imposto", $stPagar);
        }else
        if ($stTipoDocumento == "termoParcelamento.agt") {
            $obTDATDividaParcela = new TDATDividaParcela;
            $obTDATDividaParcela->recuperaTodos( $rsDados3, " WHERE num_parcelamento in ( ".$inNumParcelamento." ) AND num_parcela > 1 ", " ORDER BY dt_vencimento_parcela " );

            $stFiltro = " WHERE ddp.num_parcelamento in ( ".$inNumParcelamento." ) ";
            $obTDATDividaAtiva = new TDATDividaAtiva;
            $obTDATDividaAtiva->recuperaConsultaTermoParcelamento( $rsDados, $stFiltro );
            $rsDados->ordena("imposto", "ASC", SORT_STRING );
            $obTDATDividaAtiva->RecuperaMatriculaCGM( $rsDado2, Sessao::read('numCgm') );

            $stPagar = SistemaLegado::extenso( $rsDados->getCampo("total_pagar") );
            $rsDados->setCampo( "total_pagar", "R$ ".number_format( $rsDados->getCampo("total_pagar"), 2, ',', '.' )." (".$stPagar.")" );

            $stPagar = SistemaLegado::extenso( $rsDados->getCampo("valor_parcela") );

            $rsDados->setCampo( "valor_parcela", "R$ ". number_format( $rsDados->getCampo("valor_parcela"), 2, ',', '.' )." (".$stPagar.")" );

            $stPagar = SistemaLegado::extenso( $rsDados->getCampo("parcelas_menos") );
            $arPagar = explode( " ", $stPagar );
            $rsDados->setCampo( "parcelas_menos", $rsDados->getCampo("parcelas_menos")." (".$arPagar[1].")" );

            $arDadosDinamicos = array();
            if ( $rsDados->getCampo("parcelas") > 1 ) {
                $arData = explode( "/", $rsDados3->getCampo("dt_vencimento_parcela") );
                $arDadosDinamicos[0] = "As ";
                $arDadosDinamicos[1] = $rsDados->getCampo("parcelas_menos");
                $arDadosDinamicos[2] = " prestações seguintes serão no valor de ";
                $arDadosDinamicos[3] = $rsDados->getCampo( "valor_parcela" );
                $arDadosDinamicos[4] = ", com vencimento todo dia ";
                $arDadosDinamicos[5] = $arData[0];
                $arDadosDinamicos[6] = ", às quais serão pagas na forma determinada por ato do Poder Executivo.";
            }

            $stPagar = SistemaLegado::extenso( $rsDados->getCampo("parcelas") );
            $arPagar = explode( " ", $stPagar );
            $rsDados->setCampo( "parcelas", $rsDados->getCampo("parcelas")." (".$arPagar[1].")" );

            $inX = 0;
            while ( !$rsDados->Eof() ) {
                if ( !$inX )
                    $stPagar = $rsDados->getCampo("imposto");
                else
                    $stPagar .= ", ".$rsDados->getCampo("imposto");

                $inX++;
                $rsDados->proximo();
            }

            $rsDados->setPrimeiroElemento();

            if ( $inX > 1 )
                $rsDados->setCampo("imposto", $stPagar);

        }else
        if ($stTipoDocumento == "envelope.agt") { //era o dado que vem do campo agt
            //esta consulta serve para envelope notificação
            $stFiltro = " WHERE ddp.num_parcelamento in ( ".$inNumParcelamento." ) ";
            $obTDATDividaAtiva = new TDATDividaAtiva;
            $obTDATDividaAtiva->recuperaConsultaEnvelopeNotificacao( $rsDados, $stFiltro );

        }else
        if ($stTipoDocumento == "notificacaoAcordo.agt") { //era o dado que vem do campo agt
            //esta consulta serve para notificação do acordo
            $stFiltro = " WHERE ddp.num_parcelamento in ( ".$inNumParcelamento." ) ";
            $obTDATDividaAtiva = new TDATDividaAtiva;
            $obTDATDividaAtiva->recuperaConsultaNotificacaoAcordo( $rsDados, $stFiltro );
            $rsDados->ordena( "nr_acordo_administrativo" );
        }else
        if ($stTipoDocumento == "notificacaoDivida.agt") { //era o dado que vem do campo agt
            //esta consulta serve para a notificacao em divida
            $stFiltro = " WHERE ddp.num_parcelamento in ( ".$inNumParcelamento." ) ";
            $obTDATDividaAtiva = new TDATDividaAtiva;
            $obTDATDividaAtiva->recuperaConsultaNotificacaoDivida( $rsDados, $stFiltro );
            $obTDATDividaAtiva->recuperaConsultaNotificacaoDividaNormas( $rsDados2, $stFiltro );

            $arDados = $rsDados->arElementos;
            $arDadosTotal[0]["valor_original"] = 0.00;
            $arDadosTotal[0]["juros"] = 0.00;
            $arDadosTotal[0]["multa"] = 0.00;
            $arDadosTotal[0]["correcao"] = 0.00;
            $arDadosTotal[0]["valor_total"] = 0.00;
            for ( $inX=0; $inX<count($arDados); $inX++ ) {
                $arDados[$inX]["valor_total"] = $arDados[$inX]["juros"] + $arDados[$inX]["multa"] + $arDados[$inX]["correcao"] + $arDados[$inX]["valor_origem"];
                $arDadosTotal[0]["valor_original"] += $arDados[$inX]["valor_origem"];
                $arDadosTotal[0]["juros"] += $arDados[$inX]["juros"];
                $arDadosTotal[0]["multa"] += $arDados[$inX]["multa"];
                $arDadosTotal[0]["correcao"] += $arDados[$inX]["correcao"];
                $arDadosTotal[0]["valor_total"] += $arDados[$inX]["valor_total"];
            }

        }else
        if ($stTipoDocumento == "termoInscricao.agt" || $stTipoDocumento == "certidaoDivida.agt") { //era o dado que vem do campo agt
            //esta consulta serve para o termo de inscricao da divida
            //esta consulta serva para certidao de divida ativa do municipio
            $stFiltro = " WHERE ddp.num_parcelamento in ( ".$inNumParcelamento." ) ";
            //$stFiltro = " WHERE ddp.num_parcelamento in ( 462972, 462973 ) ";
            $obTDATDividaAtiva = new TDATDividaAtiva;
            $obTDATDividaAtiva->recuperaConsultaTermoInscricaoDivida( $rsDados, $stFiltro );
            $rsDados->ordena( "exercicio_origem" );

            $arDados = $rsDados->arElementos;
            $arDadosTotal[0]["valor_original"] = 0.00;
            $arDadosTotal[0]["juros"] = 0.00;
            $arDadosTotal[0]["multa"] = 0.00;
            $arDadosTotal[0]["correcao"] = 0.00;
            $arDadosTotal[0]["valor_total"] = 0.00;
            for ( $inX=0; $inX<count($arDados); $inX++ ) {
                $arDados[$inX]["valor_total"] = $arDados[$inX]["juros"] + $arDados[$inX]["multa"] + $arDados[$inX]["correcao"] + $arDados[$inX]["valor_origem"];
                $arDadosTotal[0]["valor_original"] += $arDados[$inX]["valor_origem"];
                $arDadosTotal[0]["juros"] += $arDados[$inX]["juros"];
                $arDadosTotal[0]["multa"] += $arDados[$inX]["multa"];
                $arDadosTotal[0]["correcao"] += $arDados[$inX]["correcao"];
                $arDadosTotal[0]["valor_total"] += $arDados[$inX]["valor_total"];
            }

            $arDadosTotal[0]["valor_escrito"] = SistemaLegado::extenso( $arDadosTotal[0]["valor_total"] );

            $arDadosTotal[0]["referencia"] = $arDados[0]["imposto"];
            if ($arDados[0]["tipo_inscricao"] == 'ie') {
                $arDadosTotal[0]["referencia"] .= " da Inscrição Econômica ";
            }else
                if ($arDados[0]["tipo_inscricao"] == 'im') {
                    $arDadosTotal[0]["referencia"] .= " da Inscrição Municipal ";
                } else {
                    $arDadosTotal[0]["referencia"] .= " do CGM ";
                }

            $arDadosTotal[0]["referencia"] .= $arDados[0]["inscricao"];
            $arDadosTotal[0]["procurador"] = $arDados[0]["procurador"];
            $arDadosTotal[0]["oab"] = $arDados[0]["oab"];
            $arDadosTotal[0]["contribuinte"] = $arDados[0]["contribuinte"];
            $arDadosTotal[0]["domicilio_fiscal"] = $arDados[0]["domicilio_fiscal"];

            $arDataInscricao = explode( "/", $arDados[0]["dt_inscricao_divida"] );
            $arMes = array( "01" => "Janeiro", "02" => "Fevereiro", "03" => "Março", "04" => "Abril", "05" => "Maio", "06" => "Junho", "07" => "Julho", "08" => "Agosto", "09" => "Setembro", 10 => "Outubro", 11 => "Novembro", 12 => "Dezembro" );
            $arDadosTotal[0]["dt_inscricao"] = $arDataInscricao[0]." de ".$arMes[$arDataInscricao[1]]." de ".$arDataInscricao[2];
        }

        // instantiate a TBS OOo class
        $OOParser = new clsTinyButStrongOOo;

        // setting the object
        $OOParser->SetZipBinary('zip');
        $OOParser->SetUnzipBinary('unzip');
        $OOParser->SetProcessDir('/tmp');

        $stDocumento = '/tmp/';
        $OOParser->_process_path = $stDocumento; //nome do arquivo pra salva

        // create a new openoffice document from the template with an unique id
        $OOParser->NewDocFromTpl( CAM_GT_DAT_MODELOS.$_REQUEST["stNomeArquivo"] ); //arquivo do openof

        $OOParser->LoadXmlFromDoc('content.xml');

        if ( ( $stTipoDocumento == "termoAssuncao.agt" ) || ( $stTipoDocumento == "reqBenDev.agt" ) || ( $stTipoDocumento == "reqBenTerc.agt" ) || ( $stTipoDocumento == "termoConfissao.agt" ) ) {
            $OOParser->MergeBlock( 'Dat', $rsDados->arElementos );
            $OOParser->MergeBlock( 'Dat3', $rsDados3->arElementos );
            $arTemp = array();
            $arTemp[0]["cgm_testemunha"] = Sessao::read('numCgm')." - ".Sessao::read('nomCgm');

            $arDataInscricao = explode( "/", date("d/m/Y") );
            $arMes = array( "01" => "Janeiro", "02" => "Fevereiro", "03" => "Março", "04" => "Abril", "05" => "Maio", "06" => "Junho", "07" => "Julho", "08" => "Agosto", "09" => "Setembro", 10 => "Outubro", 11 => "Novembro", 12 => "Dezembro" );
            $arTemp[0]["data_atual"] = $arDataInscricao[0]." de ".$arMes[$arDataInscricao[1]]." de ".$arDataInscricao[2]." ".date("G:i");
            $arTemp[0]["matricula"] = $rsDado2->getCampo("registro");
            $OOParser->MergeBlock( 'Dat2',   $arTemp );
        }else
        if ($stTipoDocumento == "termoParcelamento.agt") {
            //esta consulta serve para termo de parcelamento ( confissao )
            $arTemp = array();
            $arTemp[0]["cgm_testemunha"] = Sessao::read('numCgm')." - ".Sessao::read('nomCgm');

            $arDataInscricao = explode( "/", date("d/m/Y") );
            $arMes = array( "01" => "Janeiro", "02" => "Fevereiro", "03" => "Março", "04" => "Abril", "05" => "Maio", "06" => "Junho", "07" => "Julho", "08" => "Agosto", "09" => "Setembro", 10 => "Outubro", 11 => "Novembro", 12 => "Dezembro" );
            $arTemp[0]["data_atual"] = $arDataInscricao[0]." de ".$arMes[$arDataInscricao[1]]." de ".$arDataInscricao[2]." ".date("G:i");
            $arTemp[0]["matricula"] = $rsDado2->getCampo("registro");
            $arTemp[0]["din1"] = $arDadosDinamicos[0];
            $arTemp[0]["din2"] = $arDadosDinamicos[1];
            $arTemp[0]["din3"] = $arDadosDinamicos[2];
            $arTemp[0]["din4"] = $arDadosDinamicos[3];
            $arTemp[0]["din5"] = $arDadosDinamicos[4];
            $arTemp[0]["din6"] = $arDadosDinamicos[5];
            $arTemp[0]["din7"] = $arDadosDinamicos[6];
            $OOParser->MergeBlock( 'Dat',    $rsDados->arElementos );
            $OOParser->MergeBlock( 'Dat2',   $arTemp );
        }else
        if ($stTipoDocumento == "envelope.agt") { //era o dado que vem do campo agt
            //esta consulta serve para envelope notificação
            $OOParser->MergeBlock( 'Dat',    $rsDados->arElementos );
        }else
        if ($stTipoDocumento == "notificacaoAcordo.agt") { //era o dado que vem do campo agt
            //esta consulta serve para notificação do acordo
            $arTemp = $rsDados->arElementos;
            $arTemp2 = array(0 => $arTemp[0]["nr_acordo_administrativo"] );
            $inTot = 1;
            for ( $inX=1; $inX<count($arTemp); $inX++) {
                $boIncluir = true;
                for ($inY=0; $inY<$inTot; $inY++) {
                    if ($arTemp2[$inY] == $arTemp[$inX]["nr_acordo_administrativo"]) {
                        $boIncluir = false;
                        break;
                    }
                }

                if ($boIncluir) {
                    $arTemp2[$inTot] = $arTemp[$inX]["nr_acordo_administrativo"];
                    $inTot++;
                    $arTemp[0]["nr_acordo_administrativo"] .= ", ".$arTemp[$inX]["nr_acordo_administrativo"];
                }
            }

            $OOParser->MergeBlock( 'Dat',    $arTemp );
            $OOParser->MergeBlock( 'Dat2',   $rsDados->arElementos );
        }else
        if ($stTipoDocumento == "notificacaoDivida.agt") { //era o dado que vem do campo agt
            //esta consulta serve para a notificacao em divida
            $arTMP1 = $rsDados->arElementos;
            for ( $inTMP=0; $inTMP<count($arTMP1); $inTMP++ ) {
                $arTMP1[$inTMP]["juros"] = number_format( $arTMP1[$inTMP]["juros"], 2, ',', '.' );
                $arTMP1[$inTMP]["multa"] = number_format( $arTMP1[$inTMP]["multa"], 2, ',', '.' );
                $arTMP1[$inTMP]["correcao"] = number_format( $arTMP1[$inTMP]["correcao"], 2, ',', '.' );
                $arTMP1[$inTMP]["valor_origem"] = number_format( $arTMP1[$inTMP]["valor_origem"], 2, ',', '.' );
            }

            for ( $inTMP=0; $inTMP<count($arDados); $inTMP++ ) {
                $arDados[$inTMP]["juros"] = number_format( $arDados[$inTMP]["juros"], 2, ',', '.' );
                $arDados[$inTMP]["multa"] = number_format( $arDados[$inTMP]["multa"], 2, ',', '.' );
                $arDados[$inTMP]["correcao"] = number_format( $arDados[$inTMP]["correcao"], 2, ',', '.' );
                $arDados[$inTMP]["valor_origem"] = number_format( $arDados[$inTMP]["valor_origem"], 2, ',', '.' );
                $arDados[$inTMP]["valor_total"] = number_format( $arDados[$inTMP]["valor_total"], 2, ',', '.' );
            }

            for ( $inTMP=0; $inTMP<count($arDadosTotal); $inTMP++ ) {
                $arDadosTotal[$inTMP]["juros"] = number_format( $arDadosTotal[$inTMP]["juros"], 2, ',', '.' );
                $arDadosTotal[$inTMP]["multa"] = number_format( $arDadosTotal[$inTMP]["multa"], 2, ',', '.' );
                $arDadosTotal[$inTMP]["correcao"] = number_format( $arDadosTotal[$inTMP]["correcao"], 2, ',', '.' );
                $arDadosTotal[$inTMP]["valor_original"] = number_format( $arDadosTotal[$inTMP]["valor_original"], 2, ',', '.' );
                $arDadosTotal[$inTMP]["valor_total"] = number_format( $arDadosTotal[$inTMP]["valor_total"], 2, ',', '.' );
            }

            $OOParser->MergeBlock( 'Dat',    $arTMP1 );
            $OOParser->MergeBlock( 'Dat2',   $arDados );
            $OOParser->MergeBlock( 'Dat3',   $arDadosTotal );
            $OOParser->MergeBlock( 'Dat4',   $rsDados2->arElementos );
        }else
        if ($stTipoDocumento == "termoInscricao.agt" || $stTipoDocumento == "certidaoDivida.agt") {
            //esta consulta serve para o termo de inscricao da divida
            //esta consulta serva para certidao de divida ativa do municipio
            $arTMP1 = $rsDados->arElementos;
            for ( $inTMP=0; $inTMP<count($arTMP1); $inTMP++ ) {
                $arTMP1[$inTMP]["juros"] = number_format( $arTMP1[$inTMP]["juros"], 2, ',', '.' );
                $arTMP1[$inTMP]["multa"] = number_format( $arTMP1[$inTMP]["multa"], 2, ',', '.' );
                $arTMP1[$inTMP]["correcao"] = number_format( $arTMP1[$inTMP]["correcao"], 2, ',', '.' );
                $arTMP1[$inTMP]["valor_origem"] = number_format( $arTMP1[$inTMP]["valor_origem"], 2, ',', '.' );
            }

            for ( $inTMP=0; $inTMP<count($arDados); $inTMP++ ) {
                $arDados[$inTMP]["juros"] = number_format( $arDados[$inTMP]["juros"], 2, ',', '.' );
                $arDados[$inTMP]["multa"] = number_format( $arDados[$inTMP]["multa"], 2, ',', '.' );
                $arDados[$inTMP]["correcao"] = number_format( $arDados[$inTMP]["correcao"], 2, ',', '.' );
                $arDados[$inTMP]["valor_origem"] = number_format( $arDados[$inTMP]["valor_origem"], 2, ',', '.' );
                $arDados[$inTMP]["valor_total"] = number_format( $arDados[$inTMP]["valor_total"], 2, ',', '.' );
            }

            for ( $inTMP=0; $inTMP<count($arDadosTotal); $inTMP++ ) {
                $arDadosTotal[$inTMP]["juros"] = number_format( $arDadosTotal[$inTMP]["juros"], 2, ',', '.' );
                $arDadosTotal[$inTMP]["multa"] = number_format( $arDadosTotal[$inTMP]["multa"], 2, ',', '.' );
                $arDadosTotal[$inTMP]["correcao"] = number_format( $arDadosTotal[$inTMP]["correcao"], 2, ',', '.' );
                $arDadosTotal[$inTMP]["valor_original"] = number_format( $arDadosTotal[$inTMP]["valor_original"], 2, ',', '.' );
                $arDadosTotal[$inTMP]["valor_total"] = number_format( $arDadosTotal[$inTMP]["valor_total"], 2, ',', '.' );
            }

            $OOParser->MergeBlock( 'Dat',    $arTMP1 );
            $OOParser->MergeBlock( 'Dat2',   $arTMP1 );
            $OOParser->MergeBlock( 'Dat3',   $arDados );
            $OOParser->MergeBlock( 'Dat4',   $arDadosTotal );
        }

        $OOParser->SaveXmlToDoc();
        $OOParser->LoadXmlFromDoc('styles.xml');
        $OOParser->SaveXmlToDoc();

        $download = $_REQUEST["stNomeArquivo"];
        $stDocumento = $OOParser->GetPathnameDoc();
        $content_type = 'application/sxw';

        header ("Content-Length: " . filesize( $stDocumento ));
        header("Content-type: $content_type");
        header("Content-Disposition: attachment; filename=\"$download\"");
        readfile( $stDocumento );
        break;

    case "detalheParcela":
        include_once 'FMConsultaInscricaoDetalheValor.php';
        break;

    case "atualizaLancamentos":
        include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';
        include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaAtiva.class.php" );
        $obTDATDividaAtiva = new TDATDividaAtiva;
        $obTDATDividaAtiva->setDado('data_base',$_REQUEST['dtDataBase']);
        $obTDATDividaAtiva->listaConsultaValoresOrigemDivida( $rsListaLancamentos ,"WHERE dda.cod_inscricao = ".$_REQUEST["inCodInscricao"]." AND dda.exercicio = '".$_REQUEST["inExercicio"]."'" );
        while ( !$rsListaLancamentos->Eof() ) {
            $rsListaLancamentos->setCampo( "nom_origem", str_replace( ";", "<BR>", $rsListaLancamentos->getCampo("nom_origem") ) );
            $rsListaLancamentos->proximo();
        }

        $rsListaLancamentos->setPrimeiroElemento();
        $rsListaLancamentos->addFormatacao ('valor_lancado', 'NUMERIC_BR');
        $rsListaLancamentos->addFormatacao ('valor_atualizado', 'NUMERIC_BR');

        $tableLancamentos = new Table();
        $tableLancamentos->setRecordset( $rsListaLancamentos );
        $tableLancamentos->setSummary('Lista de Lançamentos');

        #$tableCobrancas->setParametros( array( "num_parcelamento" ) );
        //$tableLancamentos->setConditional( true , "#efefef" ); // lista zebrada
        $tableLancamentos->Head->addCabecalho( 'Exercício', 10  );
        $tableLancamentos->Head->addCabecalho( 'Crédito/Grupo de Crédito', 40  );
        $tableLancamentos->Head->addCabecalho( 'Parcelas', 10  );
        $tableLancamentos->Head->addCabecalho( 'Valor Lançado', 20  );
        $tableLancamentos->Head->addCabecalho( 'Valor Atualizado', 20  );

        $tableLancamentos->Body->addCampo( 'exercicio_original', 'C' );
        $tableLancamentos->Body->addCampo( 'nom_origem' );
        $tableLancamentos->Body->addCampo( 'total_parcelas', 'C' );
        $tableLancamentos->Body->addCampo( 'valor_lancado', 'D' );
        $tableLancamentos->Body->addCampo( 'valor_atualizado', 'D' );

        $tableLancamentos->Foot->addSoma ( 'valor_lancado', "D" );
        $tableLancamentos->Foot->addSoma ( 'valor_atualizado', "D" );

        $tableLancamentos->montaHTML();
        echo $tableLancamentos->getHtml();
        break;
}

?>
