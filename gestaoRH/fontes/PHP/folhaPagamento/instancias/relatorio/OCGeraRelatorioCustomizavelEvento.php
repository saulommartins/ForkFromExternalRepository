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
    * Oculto de Relatório Customizável de Eventos
    * Data de Criação   : 17/04/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 23545 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-26 17:12:04 -0300 (Ter, 26 Jun 2007) $

    * Casos de uso: uc-04.05.51
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/PDF/ListaFormPDFRH.class.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/PDF/ListaPDFRH.class.php';
include_once( CAM_FW_PDF."RRelatorio.class.php"                                                         );
include_once( CAM_GA_CGM_NEGOCIO."RCGMPessoaFisica.class.php"                                           );

$obRRelatorio           = new RRelatorio;
$obCGM                  = new RCGMPessoaFisica;
$obPDF                  = new ListaFormPDFRH('L');

$obRRelatorio->setExercicio  ( $sessao->exercicio );
$obRRelatorio->setCodigoEntidade( $sessao->getCodEntidade() );
$obRRelatorio->setExercicioEntidade( $sessao->exercicio );
$obRRelatorio->recuperaCabecalho( $arConfiguracao );
$obPDF->setModulo            ( "Folha de Pagamento" );
//if ($sessao->filtro['stFolha'] == 'analítica') {
//    $arConfiguracao['nom_acao'] = 'Relatório da Folha Analítica';
//    $obPDF->setTitulo            ( "Relatório da Folha Analítica" );
//} else {
//    $arConfiguracao['nom_acao'] = 'Relatório da Folha Sintética';
//    $obPDF->setTitulo            ( "Relatório da Folha Sintética" );
//}
$obPDF->setSubTitulo         ( $sessao->exercicio );
$obPDF->setUsuario           ( $sessao->username );
$obPDF->setEnderecoPrefeitura( $arConfiguracao );
$rsRecordSet = (is_object($sessao->transf5)) ? $sessao->transf5 : new recordset ;
$rsVazio = new RecordSet;

$arRecordset = $rsRecordSet->getElementos();
$arRecordset = (is_array($arRecordset)) ? $arRecordset : array();

if ( count($arRecordset) ) {
    foreach ($arRecordset as $inIndex=>$arPagina) {
        $rsTemp = new RecordSet;
        $rsTemp->preenche( $arPagina['linha1'] );
        $obPDF->addRecordSet($rsTemp);
        $obPDF->setAlinhamento  ( "R"           );
        $obPDF->addCabecalho    ( "",     10, 10);
        $obPDF->addCabecalho    ( "",     20, 10);
        $obPDF->addCabecalho    ( "",     10, 10);
        $obPDF->addCabecalho    ( "",     30, 10);
        $obPDF->addCabecalho    ( "",     10, 10);
        $obPDF->addCabecalho    ( "",     20, 10);
        $obPDF->setAlinhamento  ( "R"           );
        $obPDF->addCampo        ( "campo1", 8   );
        $obPDF->setAlinhamento  ( "L"           );
        $obPDF->addCampo        ( "campo2", 8   );
        $obPDF->setAlinhamento  ( "R"           );
        $obPDF->addCampo        ( "campo3", 8   );
        $obPDF->setAlinhamento  ( "L"           );
        $obPDF->addCampo        ( "campo4", 8   );
        $obPDF->setAlinhamento  ( "R"           );
        $obPDF->addCampo        ( "campo5", 8   );
        $obPDF->setAlinhamento  ( "L"           );
        $obPDF->addCampo        ( "campo6", 8   );

        $rsTemp = new RecordSet;
        $rsTemp->preenche( $arPagina['eventos'] );
        $obPDF->addRecordSet($rsTemp);
        $obPDF->setQuebraPaginaLista(false);
        $obPDF->addCabecalho    ( "",     10, 10);
        $obPDF->addCabecalho    ( "",     25, 10);
        $obPDF->addCabecalho    ( "",     25, 10);
        $obPDF->addCabecalho    ( "",     25, 10);
        $obPDF->addCabecalho    ( "",     25, 10);
        $obPDF->addCabecalho    ( "",     25, 10);
        $obPDF->setAlinhamento  ( "R"           );
        $obPDF->addCampo        ( "campo1", 8   );
        $obPDF->setAlinhamento  ( "L"           );
        $obPDF->addCampo        ( "campo2", 8   );
        $obPDF->setAlinhamento  ( "L"           );
        $obPDF->addCampo        ( "campo3", 8   );
        $obPDF->setAlinhamento  ( "L"           );
        $obPDF->addCampo        ( "campo4", 8   );
        $obPDF->setAlinhamento  ( "L"           );
        $obPDF->addCampo        ( "campo5", 8   );

        $arCabecalho = array();
        $arCabecalho[0]["campo1"] = "";
        $arCabecalho[0]["campo2"] = "";
        $arCabecalho[0]["campo3"] = "";
        $arCabecalho[0]["campo4"] = "";
        $arCabecalho[0]["campo5"] = "";
        $arCabecalho[0]["campo6"] = "";
        $inIndex = 7;
        foreach ($arPagina['contratos1'][0] as $stCampo=>$inContrato) {
            $arCabecalho[0]["campo".$inIndex] = "Evento";
            $inIndex++;
        }
        $rsCabecalho = new RecordSet();
        $rsCabecalho->preenche($arCabecalho);
        $obPDF->addRecordSet($rsCabecalho);
        $obPDF->setQuebraPaginaLista(false);
        $obPDF->setAlinhamento  ( "C" );
        $obPDF->addCabecalho    ( ""    ,     6 , 10);
        $obPDF->addCabecalho    ( ""    ,     25, 10);
        if ($sessao->filtro['boApresentarPorMatricula'] == "lotacao") { $obPDF->addCabecalho    ( "" ,     20, 10); }
        if ($sessao->filtro['boApresentarPorMatricula'] == "local") { $obPDF->addCabecalho    ( "" ,     20, 10); }
        if ($sessao->filtro['boApresentarPorMatricula'] == "cargo") { $obPDF->addCabecalho    ( "" ,     20, 10); }
        if ($sessao->filtro['boApresentarPorMatricula'] == "funcao") { $obPDF->addCabecalho    ( "" ,     20, 10); }
        foreach ($arPagina['contratos1'][0] as $stCampo=>$inContrato) {
            $obPDF->addCabecalho    ( ""    ,     10 , 8);
        }
        $obPDF->addCampo        ( "campo1", 8, '', '', 'T',''   );
        $obPDF->addCampo        ( "campo2", 8, '', '', 'T',''   );
        if ($sessao->filtro['boApresentarPorMatricula'] == "lotacao") { $obPDF->addCampo        ( "campo3", 8, '', '', 'T',''   );  }
        if ($sessao->filtro['boApresentarPorMatricula'] == "local") { $obPDF->addCampo        ( "campo4", 8, '', '', 'T',''   );  }
        if ($sessao->filtro['boApresentarPorMatricula'] == "cargo") { $obPDF->addCampo        ( "campo5", 8, '', '', 'T',''   );  }
        if ($sessao->filtro['boApresentarPorMatricula'] == "funcao") { $obPDF->addCampo        ( "campo6", 8, '', '', 'T',''   );  }
        $inIndex = 7;
        foreach ($arPagina['contratos1'][0] as $stCampo=>$inContrato) {
            $obPDF->addCampo        ( "campo".$inIndex, 8 , '', '', 'T',''  );
            $inIndex++;
        }

        $rsTemp = new RecordSet;
        $rsTemp->preenche( $arPagina['contratos1'] );
        $obPDF->addRecordSet($rsTemp);
        $obPDF->setQuebraPaginaLista(false);
        $obPDF->setAlinhamento  ( "R"                       );
        $obPDF->addCabecalho    ( ""    ,     6 , 10);
        $obPDF->setAlinhamento  ( "L"                       );
        $obPDF->addCabecalho    ( ""    ,     25, 10);
        if ($sessao->filtro['boApresentarPorMatricula'] == "lotacao") { $obPDF->addCabecalho    ( "" ,     20, 10); }
        if ($sessao->filtro['boApresentarPorMatricula'] == "local") { $obPDF->addCabecalho    ( "" ,     20, 10); }
        if ($sessao->filtro['boApresentarPorMatricula'] == "cargo") { $obPDF->addCabecalho    ( "" ,     20, 10); }
        if ($sessao->filtro['boApresentarPorMatricula'] == "funcao") { $obPDF->addCabecalho    ( "" ,     20, 10); }
        $obPDF->setAlinhamento  ( "C"                       );
        foreach ($arPagina['contratos1'][0] as $stCampo=>$inContrato) {
            $obPDF->addCabecalho    ( ""      ,     10, 8);
        }
        $obPDF->setAlinhamento  ( "R"           );
        $obPDF->addCampo        ( "campo1", 8   );
        $obPDF->setAlinhamento  ( "L"           );
        $obPDF->addCampo        ( "campo2", 8   );
        if ($sessao->filtro['boApresentarPorMatricula'] == "lotacao") {
            $obPDF->setAlinhamento  ( "L"           );
            $obPDF->addCampo        ( "campo3", 8   );
        }
        if ($sessao->filtro['boApresentarPorMatricula'] == "local") {
            $obPDF->setAlinhamento  ( "L"           );
            $obPDF->addCampo        ( "campo4", 8   );
        }
        if ($sessao->filtro['boApresentarPorMatricula'] == "cargo") {
            $obPDF->setAlinhamento  ( "L"           );
            $obPDF->addCampo        ( "campo5", 8   );
        }
        if ($sessao->filtro['boApresentarPorMatricula'] == "funcao") {
            $obPDF->setAlinhamento  ( "L"           );
            $obPDF->addCampo        ( "campo6", 8   );
        }
        $obPDF->setAlinhamento  ( "C"           );
        foreach ($arPagina['contratos1'][0] as $stCampo=>$inContrato) {
            $obPDF->addCampo        ( $stCampo, 8   );
        }

        $arCabecalho = array();
        $arCabecalho[0]["campo1"] = "Matrícula";
        $arCabecalho[0]["campo2"] = "Servidor";
        $arCabecalho[0]["campo3"] = "Lotação";
        $arCabecalho[0]["campo4"] = "Local";
        $arCabecalho[0]["campo5"] = "Cargo/Esp.";
        $arCabecalho[0]["campo6"] = "Função/Esp.";
        $inIndex = 7;
        foreach ($arPagina['contratos1'][0] as $stCampo=>$inContrato) {
            $arCabecalho[0]["campo".$inIndex] = "Qtd.";
            $inIndex++;
            $arCabecalho[0]["campo".$inIndex] = "Valor";
            $inIndex++;
        }
        $rsCabecalho = new RecordSet();
        $rsCabecalho->preenche($arCabecalho);
        $obPDF->addRecordSet($rsCabecalho);
        $obPDF->setQuebraPaginaLista(false);
        $obPDF->setAlinhamento  ( "R"                      );
        $obPDF->addCabecalho    ( ""            ,     6 , 8);
        $obPDF->setAlinhamento  ( "L"                      );
        $obPDF->addCabecalho    ( ""            ,     25, 8);
        if ($sessao->filtro['boApresentarPorMatricula'] == "lotacao") { $obPDF->addCabecalho    ( ""   ,     20, 8); }
        if ($sessao->filtro['boApresentarPorMatricula'] == "local") { $obPDF->addCabecalho    ( ""   ,     20, 8); }
        if ($sessao->filtro['boApresentarPorMatricula'] == "cargo") { $obPDF->addCabecalho    ( ""   ,     20, 8); }
        if ($sessao->filtro['boApresentarPorMatricula'] == "funcao") { $obPDF->addCabecalho    ( ""   ,     20, 8); }
        $obPDF->setAlinhamento  ( "R"                      );
        if ($sessao->filtro['boQuantidade'] and $sessao->filtro['boValor']) {
            $inLarguraQtd = 5;
            $inLarguraVlr = 5;
        }
        if (!$sessao->filtro['boQuantidade'] and $sessao->filtro['boValor']) {
            $inLarguraVlr = 10;
        }
        if ($sessao->filtro['boQuantidade'] and !$sessao->filtro['boValor']) {
            $inLarguraQtd = 10;
        }
        foreach ($arPagina['contratos1'][0] as $stCampo=>$inContrato) {
            if ($sessao->filtro['boQuantidade']) { $obPDF->addCabecalho    ( ""       ,     $inLarguraQtd , 8); }
            if ($sessao->filtro['boValor']) { $obPDF->addCabecalho    ( ""       ,     $inLarguraVlr , 8); }
        }
        $obPDF->setAlinhamento  ( "R"           );
        $obPDF->addCampo        ( "campo1", 8, '', '','B',''    );
        $obPDF->setAlinhamento  ( "L"           );
        $obPDF->addCampo        ( "campo2", 8, '', '','B',''   );
        if ($sessao->filtro['boApresentarPorMatricula'] == "lotacao") {
            $obPDF->setAlinhamento  ( "L"           );
            $obPDF->addCampo        ( "campo3", 8, '', '','B',''   );
        }
        if ($sessao->filtro['boApresentarPorMatricula'] == "local") {
            $obPDF->setAlinhamento  ( "L"           );
            $obPDF->addCampo        ( "campo4", 8, '', '','B',''   );
        }
        if ($sessao->filtro['boApresentarPorMatricula'] == "cargo") {
            $obPDF->setAlinhamento  ( "L"           );
            $obPDF->addCampo        ( "campo5", 8, '', '','B',''   );
        }
        if ($sessao->filtro['boApresentarPorMatricula'] == "funcao") {
            $obPDF->setAlinhamento  ( "L"           );
            $obPDF->addCampo        ( "campo6",8, '', '','B',''    );
        }
        $obPDF->setAlinhamento  ( "R"                      );
        $inIndex = 7;
        foreach ($arPagina['contratos1'][0] as $stCampo=>$inContrato) {
            if ($sessao->filtro['boQuantidade']) { $obPDF->addCampo    ( "campo".$inIndex       , 8, '', '','B',''); }
            $inIndex++;
            if ($sessao->filtro['boValor']) { $obPDF->addCampo    ( "campo".$inIndex       , 8, '', '','B',''); }
            $inIndex++;
        }

        $rsTemp = new RecordSet;
        $rsTemp->preenche( $arPagina['contratos2'] );
        $obPDF->addRecordSet($rsTemp);
        $obPDF->setQuebraPaginaLista(false);
        $obPDF->setAlinhamento  ( "R"                      );
        $obPDF->addCabecalho    ( ""            ,     6 , 8    );
        $obPDF->setAlinhamento  ( "L"                      );
        $obPDF->addCabecalho    ( ""            ,     25, 8);
        if ($sessao->filtro['boApresentarPorMatricula'] == "lotacao") { $obPDF->addCabecalho    ( ""       ,     20, 8); }
        if ($sessao->filtro['boApresentarPorMatricula'] == "local") { $obPDF->addCabecalho    ( ""         ,     20, 8); }
        if ($sessao->filtro['boApresentarPorMatricula'] == "cargo") { $obPDF->addCabecalho    ( ""    ,     20, 8); }
        if ($sessao->filtro['boApresentarPorMatricula'] == "funcao") { $obPDF->addCabecalho    ( ""   ,     20, 8); }
        $obPDF->setAlinhamento  ( "R"                      );
        if ($sessao->filtro['boQuantidade'] and $sessao->filtro['boValor']) {
            $inLarguraQtd = 5;
            $inLarguraVlr = 5;
        }
        if (!$sessao->filtro['boQuantidade'] and $sessao->filtro['boValor']) {
            $inLarguraVlr = 10;
        }
        if ($sessao->filtro['boQuantidade'] and !$sessao->filtro['boValor']) {
            $inLarguraQtd = 10;
        }
        foreach ($arPagina['contratos1'][0] as $stCampo=>$inContrato) {
            if ($sessao->filtro['boQuantidade']) { $obPDF->addCabecalho    ( ""        ,     $inLarguraQtd , 8); }
            if ($sessao->filtro['boValor']) { $obPDF->addCabecalho    ( ""       ,     $inLarguraVlr , 8); }
        }
        $obPDF->setAlinhamento  ( "R"           );
        $obPDF->addCampo        ( "campo1", 8, '', '', 'BT',''   );
        $obPDF->setAlinhamento  ( "L"           );
        $obPDF->addCampo        ( "campo2", 8, '', '', 'BT',''   );
        if ($sessao->filtro['boApresentarPorMatricula'] == "lotacao") {
            $obPDF->setAlinhamento  ( "L"           );
            $obPDF->addCampo        ( "campo3", 8, '', '', 'BT',''   );
        }
        if ($sessao->filtro['boApresentarPorMatricula'] == "local") {
            $obPDF->setAlinhamento  ( "L"           );
            $obPDF->addCampo        ( "campo4", 8, '', '', 'BT',''   );
        }
        if ($sessao->filtro['boApresentarPorMatricula'] == "cargo") {
            $obPDF->setAlinhamento  ( "L"           );
            $obPDF->addCampo        ( "campo5", 8, '', '', 'BT',''   );
        }
        if ($sessao->filtro['boApresentarPorMatricula'] == "funcao") {
            $obPDF->setAlinhamento  ( "L"           );
            $obPDF->addCampo        ( "campo6",8, '', '', 'BT',''    );
        }
        $obPDF->setAlinhamento  ( "R"           );
        foreach ($arPagina['contratos1'][0] as $stCampo=>$inContrato) {
            switch ($stCampo) {
                case 'campo7':
                    $stCampoQtd = "campo7";
                    $stCampoVlr = "campo8";
                break;
                case 'campo8':
                    $stCampoQtd = "campo9";
                    $stCampoVlr = "campo10";
                break;
                case 'campo9':
                    $stCampoQtd = "campo11";
                    $stCampoVlr = "campo12";
                break;
                case 'campo10':
                    $stCampoQtd = "campo13";
                    $stCampoVlr = "campo14";
                break;
                case 'campo11':
                    $stCampoQtd = "campo15";
                    $stCampoVlr = "campo16";
                break;
                case 'campo12':
                    $stCampoQtd = "campo17";
                    $stCampoVlr = "campo18";
                break;
                case 'campo13':
                    $stCampoQtd = "campo19";
                    $stCampoVlr = "campo20";
                break;
            }
            if ($sessao->filtro['boQuantidade']) { $obPDF->addCampo        ( $stCampoQtd, 8, '', '', 'BT',''   );  }
            if ($sessao->filtro['boValor']) { $obPDF->addCampo        ( $stCampoVlr, 8, '', '', 'BT',''   );  }
        }

        $rsTemp = new RecordSet;
        $rsTemp->preenche( $arPagina['linha1'] );
        $obPDF->addRecordSet($rsTemp);
        $obPDF->setAlinhamento  ( "R"           );
        $obPDF->addCabecalho    ( "",     10, 10);
        $obPDF->addCabecalho    ( "",     20, 10);
        $obPDF->addCabecalho    ( "",     10, 10);
        $obPDF->addCabecalho    ( "",     30, 10);
        $obPDF->addCabecalho    ( "",     10, 10);
        $obPDF->addCabecalho    ( "",     20, 10);
        $obPDF->setAlinhamento  ( "R"           );
        $obPDF->addCampo        ( "campo1", 8   );
        $obPDF->setAlinhamento  ( "L"           );
        $obPDF->addCampo        ( "campo2", 8   );
        $obPDF->setAlinhamento  ( "R"           );
        $obPDF->addCampo        ( "campo3", 8   );
        $obPDF->setAlinhamento  ( "L"           );
        $obPDF->addCampo        ( "campo4", 8   );
        $obPDF->setAlinhamento  ( "R"           );
        $obPDF->addCampo        ( "campo5", 8   );
        $obPDF->setAlinhamento  ( "L"           );
        $obPDF->addCampo        ( "campo6", 8   );

        $rsTemp = new RecordSet;
        $rsTemp->preenche( $arPagina['eventos'] );
        $obPDF->addRecordSet($rsTemp);
        $obPDF->setQuebraPaginaLista(false);
        $obPDF->addCabecalho    ( "",     10, 10);
        $obPDF->addCabecalho    ( "",     25, 10);
        $obPDF->addCabecalho    ( "",     25, 10);
        $obPDF->addCabecalho    ( "",     25, 10);
        $obPDF->addCabecalho    ( "",     25, 10);
        $obPDF->addCabecalho    ( "",     25, 10);
        $obPDF->setAlinhamento  ( "R"           );
        $obPDF->addCampo        ( "campo1", 8   );
        $obPDF->setAlinhamento  ( "L"           );
        $obPDF->addCampo        ( "campo2", 8   );
        $obPDF->setAlinhamento  ( "L"           );
        $obPDF->addCampo        ( "campo3", 8   );
        $obPDF->setAlinhamento  ( "L"           );
        $obPDF->addCampo        ( "campo4", 8   );
        $obPDF->setAlinhamento  ( "L"           );
        $obPDF->addCampo        ( "campo5", 8   );

        $rsTemp = new RecordSet;
        $rsTemp->preenche( $arPagina['titulo_total'] );
        $obPDF->addRecordSet($rsTemp);
        $obPDF->setQuebraPaginaLista(false);
        $obPDF->setAlinhamento  ( "C"                 );
        $obPDF->addCabecalho    ( ""      ,     100, 8);
        $obPDF->setAlinhamento  ( "C"                 );
        $obPDF->addCampo        ( "titulo", 8         );

        $rsTemp = new RecordSet;
        $rsTemp->preenche( $arPagina['contratos1'] );
        $obPDF->addRecordSet($rsTemp);
        $obPDF->setQuebraPaginaLista(false);
        $obPDF->addCabecalho    ( ""      ,     20, 8);
        $obPDF->setAlinhamento  ( "C"                       );
        foreach ($arPagina['contratos1'][0] as $stCampo=>$inContrato) {
            $obPDF->addCabecalho    ( "Evento"      ,     10, 8);
        }
        $obPDF->addCampo        ( "campo0", 8   );
        $obPDF->setAlinhamento  ( "C"           );
        foreach ($arPagina['contratos1'][0] as $stCampo=>$inContrato) {
            $obPDF->addCampo        ( $stCampo, 8   );
        }

        $rsTemp = new RecordSet;
        $rsTemp->preenche( $arPagina['total_geral2'] );
        $obPDF->addRecordSet($rsTemp);
        $obPDF->setQuebraPaginaLista(false);
        $obPDF->addCabecalho    ( "Numero de Funcionários....: ".count($arPagina['contratos2']),     20, 8);
        $obPDF->setAlinhamento  ( "R"           );
        foreach ($arPagina['contratos1'][0] as $stCampo=>$inContrato) {
            if ($sessao->filtro['boQuantidade']) { $obPDF->addCabecalho    ( "Qtd."        ,     $inLarguraQtd , 8); }
            if ($sessao->filtro['boValor']) { $obPDF->addCabecalho    ( "Valor"       ,     $inLarguraVlr , 8); }
        }
        $obPDF->addCampo        ( "campo0", 8   );
        foreach ($arPagina['contratos1'][0] as $stCampo=>$inContrato) {
            switch ($stCampo) {
                case 'campo7':
                    $stCampoQtd = "campo7";
                    $stCampoVlr = "campo8";
                break;
                case 'campo8':
                    $stCampoQtd = "campo9";
                    $stCampoVlr = "campo10";
                break;
                case 'campo9':
                    $stCampoQtd = "campo11";
                    $stCampoVlr = "campo12";
                break;
                case 'campo10':
                    $stCampoQtd = "campo13";
                    $stCampoVlr = "campo14";
                break;
                case 'campo11':
                    $stCampoQtd = "campo15";
                    $stCampoVlr = "campo16";
                break;
                case 'campo12':
                    $stCampoQtd = "campo17";
                    $stCampoVlr = "campo18";
                break;
                case 'campo13':
                    $stCampoQtd = "campo19";
                    $stCampoVlr = "campo20";
                break;
            }
            if ($sessao->filtro['boQuantidade']) { $obPDF->addCampo        ( $stCampoQtd, 8   );  }
            if ($sessao->filtro['boValor']) { $obPDF->addCampo        ( $stCampoVlr, 8   );  }
        }

    }
}
$obPDF->show();

?>
