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
    * Página de Formulario de Seleção de Impressora para Relatorio
    * Data de Criação   : 18/02/2005

    * @author Lucas Leusin Oaigen

    * @ignore

    $Id: OCGeraRelatorioEmpenhoEmpenhadoPagoLiquidado.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso : uc-02.03.06
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php" );

$obRRelatorio = new RRelatorio;
$obPDF        = new ListaFormPDF( "L" );

$arFiltro = Sessao::read('filtroRelatorio');
$rsRecordSet = Sessao::read('rsRecordSet');

// Adicionar logo nos relatorios
if ( count( $arFiltro['inCodEntidade'] ) == 1 ) {
    $obRRelatorio->setCodigoEntidade( $arFiltro['inCodEntidade'][0] );
    $obRRelatorio->setExercicioEntidade ( Sessao::getExercicio() );
}

$obRRelatorio->recuperaCabecalho ( $arConfiguracao                                              );
$obPDF->setModulo                ( "Empenho - ".Sessao::getExercicio()                      );
switch ($arFiltro['inSituacao']) {
case 1:
    $stSituacao = "Empenhados";
    break;
case 2:
    $stSituacao = "Pagos";
    break;
case 3:
    $stSituacao = "Liquidados";
    break;
case 4:
    $stSituacao = "Anulados";
    break;
case 5:
    $stSituacao = "Estornados";
    break;
case 6:
    $stSituacao = "Estornados na Liquidação";
    break;
}
$dtPeriodo = $arFiltro['stDataInicial']." a ".$arFiltro['stDataFinal'] ." - ".$stSituacao;
$obPDF->setSubTitulo             ( $dtPeriodo  );

$obPDF->setUsuario           ( Sessao::getUsername() );
$obPDF->setEnderecoPrefeitura( $arConfiguracao );

$obPDF->addRecordSet( $rsRecordSet );

$boDemonstraRecurso = (isset($arFiltro["stDemonstracaoRecursoEmpenho"]) && $arFiltro["stDemonstracaoRecursoEmpenho"] == "S") ? true : false;
$boDemonstraElementoDespesa = (isset($arFiltro["stDemonstracaoElementoDespesa"]) && $arFiltro["stDemonstracaoElementoDespesa"] == "S") ? true : false;

switch ($arFiltro['inSituacao']) {
case 1:
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho("DATA", 5, 6, 'b');
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCabecalho("CATEGORIA", 7, 7, 'b');
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCabecalho("TIPO", 5, 7, 'b');
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho("EMPENHO", 6, 7, 'b');
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCabecalho("LIQ.", 3, 7, 'b');
    $obPDF->addCabecalho("CGM", 3, 7, 'b');
    $obPDF->setAlinhamento ( "L" );

    if ($boDemonstraRecurso && $boDemonstraElementoDespesa) {
        $obPDF->addCabecalho("RAZÃO SOCIAL",28, 7, 'b');
    } elseif ($boDemonstraRecurso && !$boDemonstraElementoDespesa) {
        $obPDF->addCabecalho("RAZÃO SOCIAL",35, 7, 'b');
    } elseif (!$boDemonstraRecurso && $boDemonstraElementoDespesa) {
        $obPDF->addCabecalho("RAZÃO SOCIAL",45, 7, 'b');
    } else {
        $obPDF->addCabecalho("RAZÃO SOCIAL", 50, 7, 'b');
    }

    if ($boDemonstraRecurso) {
        $obPDF->setAlinhamento("L");
        $obPDF->addCabecalho("RECURSO", 16, 7, 'b');
    }

    if ($boDemonstraElementoDespesa) {
        $obPDF->setAlinhamento("L");
        $obPDF->addCabecalho("DESPESA", 7, 7, 'b');
    }

    break;
    case 5:

        $obPDF->setAlinhamento ( "L" );
        $obPDF->addCabecalho("DATA", 5, 7, 'b');
        $obPDF->setAlinhamento ( "C" );
        $obPDF->addCabecalho("CATEGORIA", 7, 7, 'b');
        $obPDF->setAlinhamento ( "C" );
        $obPDF->addCabecalho("TIPO", 5, 7, 'b');
        $obPDF->setAlinhamento ( "C" );
        $obPDF->addCabecalho("EMPENHO", 6, 7, 'b');
        $obPDF->setAlinhamento ( "R" );
        $obPDF->addCabecalho("LIQ.", 4, 7, 'b');
        $obPDF->addCabecalho("O.P.", 4, 7, 'b');
        $obPDF->addCabecalho("CGM", 5, 7, 'b');

        $obPDF->setAlinhamento ( "L" );

        if ($boDemonstraRecurso && $boDemonstraElementoDespesa) {
            $obPDF->addCabecalho("RAZÃO SOCIAL", 10, 7, 'b');
            $obPDF->setAlinhamento ( "R" );
            $obPDF->addCabecalho("CONTA", 6, 7, 'b');
            $obPDF->setAlinhamento ( "L" );
            $obPDF->addCabecalho("NOME CONTA", 12, 7, 'b');
        } elseif ($boDemonstraRecurso && !$boDemonstraElementoDespesa) {
            $obPDF->addCabecalho("RAZÃO SOCIAL",15, 7, 'b');
            $obPDF->setAlinhamento ( "R" );
            $obPDF->addCabecalho("CONTA", 7, 7, 'b');
            $obPDF->setAlinhamento ( "L" );
            $obPDF->addCabecalho("NOME CONTA", 14, 7, 'b');
        } elseif (!$boDemonstraRecurso && $boDemonstraElementoDespesa) {
            $obPDF->addCabecalho("RAZÃO SOCIAL", 19, 7, 'b');
            $obPDF->setAlinhamento ( "R" );
            $obPDF->addCabecalho("CONTA", 7, 7, 'b');
            $obPDF->setAlinhamento ( "L" );
            $obPDF->addCabecalho("NOME CONTA", 14, 7, 'b');
        } else {
            $obPDF->addCabecalho("RAZÃO SOCIAL",21, 7, 'b');
            $obPDF->setAlinhamento ( "R" );
            $obPDF->addCabecalho("CONTA", 7, 7, 'b');
            $obPDF->setAlinhamento ( "L" );
            $obPDF->addCabecalho("NOME CONTA", 20, 7, 'b');
        }

        if ($boDemonstraRecurso) {
            $obPDF->setAlinhamento("L");
            $obPDF->addCabecalho("RECURSO", 12, 7, 'b');
        }
        if ($boDemonstraElementoDespesa) {
            $obPDF->setAlinhamento("L");
            $obPDF->addCabecalho("DESPESA", 8, 7, 'b');
        }
        break;
    default:
        $obPDF->setAlinhamento ( "L" );
        $obPDF->addCabecalho("DATA", 5, 7, 'b');
        $obPDF->setAlinhamento ( "C" );
        $obPDF->addCabecalho("CATEGORIA", 7, 7, 'b');
        $obPDF->setAlinhamento ( "C" );
        $obPDF->addCabecalho("TIPO", 5, 7, 'b');
        $obPDF->setAlinhamento ( "L" );
        $obPDF->addCabecalho("EMPENHO", 6, 7, 'b');
        $obPDF->setAlinhamento ( "L" );
        $obPDF->addCabecalho("LIQ.", 4, 7, 'b');
        $obPDF->addCabecalho("CGM", 4, 7, 'b');
        $obPDF->setAlinhamento ( "L" );

        if ($boDemonstraRecurso && $boDemonstraElementoDespesa) {
            $obPDF->addCabecalho("RAZÃO SOCIAL",20, 7, 'b');
        } elseif ($boDemonstraRecurso && !$boDemonstraElementoDespesa) {
            $obPDF->addCabecalho("RAZÃO SOCIAL",28, 7, 'b');
        } elseif (!$boDemonstraRecurso && $boDemonstraElementoDespesa) {
            $obPDF->addCabecalho("RAZÃO SOCIAL",40, 7, 'b');
        } else {
            $obPDF->addCabecalho("RAZÃO SOCIAL", 50, 7, 'b');
        }

        if ($boDemonstraRecurso) {
            $obPDF->setAlinhamento("L");
            $obPDF->addCabecalho("RECURSO", 24, 7, 'b');
        }

        if ($boDemonstraElementoDespesa) {
            $obPDF->setAlinhamento("L");
            $obPDF->addCabecalho("DESPESA", 8, 7, 'b');
        }

        break;
}

switch ($arFiltro['inSituacao']) {
    //Empenhados
    case 1:
            $obPDF->setAlinhamento ( "R" );
            $obPDF->addCabecalho("EMPENHADO", 7, 7, 'b');
            $obPDF->addQuebraLinha("nivel",2,5);
            $obPDF->setAlinhamento ( "R" );
            $obPDF->addCabecalho("ANULADO", 6, 7, 'b');
            $obPDF->addQuebraLinha("nivel",2,5);
            $obPDF->setAlinhamento ( "R" );
            $obPDF->addCabecalho("SALDO", 6, 7, 'b');
            $obPDF->addQuebraLinha("nivel",2,5);
    break;
    //Liquidados
    case 3:
            $obPDF->setAlinhamento ( "R" );
            $obPDF->addCabecalho("VALOR", 7, 7, 'b');
            $obPDF->addQuebraLinha("nivel",2,5);
            $obPDF->setAlinhamento ( "R" );
            $obPDF->addCabecalho("ANULADO", 6, 7, 'b');
            $obPDF->addQuebraLinha("nivel",2,5);
            $obPDF->setAlinhamento ( "R" );
            $obPDF->addCabecalho("SALDO", 6, 7, 'b');
            $obPDF->addQuebraLinha("nivel",2,5);
    break;

    default:
            $obPDF->setAlinhamento ( "R" );
            $obPDF->addCabecalho("VALOR", 8, 7, 'b');
            $obPDF->addQuebraLinha("nivel",2,5);
}

// Campos

$obPDF->setAlinhamento ( "L" );
$obPDF->addCampo("data", 6,'','','','',0 );
$obPDF->setAlinhamento("C");
$obPDF->addCampo("descricao_categoria", 6, '', '', '', 0);
$obPDF->setAlinhamento("C");
$obPDF->addCampo("nom_tipo", 6, '', '', '', 0);
$obPDF->setAlinhamento ( "L" );
$obPDF->addCampo("empenho", 6,'','','','',0 );
$obPDF->setAlinhamento ( "L" );
$obPDF->addCampo("cod_nota", 6,'','','','',0 );

switch ($arFiltro['inSituacao']) {
    case 5:
        $obPDF->addCampo("ordem", 6,'','','','',0 );
        break;
    default:
        break;
}

$obPDF->setAlinhamento ( "L" );
$obPDF->addCampo("cgm", 6,'','','','',0 );
$obPDF->setAlinhamento ( "L" );

if ($arFiltro['inSituacao'] == 5) {
    $obPDF->addCampo("razao_social", 5,'','','','',0 );
} else {
    $obPDF->addCampo("razao_social", 6,'','','','',0 );
}
switch ($arFiltro['inSituacao']) {
    case 5:
        $obPDF->setAlinhamento ( "R" );
        $obPDF->addCampo("conta", 6,'','','','',0 );
        $obPDF->setAlinhamento ( "L" );
        $obPDF->addCampo("nome_conta", 5,'','','','',0);
        break;
    default:
        break;
}

if (isset($arFiltro["stDemonstracaoRecursoEmpenho"]) && $arFiltro["stDemonstracaoRecursoEmpenho"] == "S") {
    $obPDF->setAlinhamento("L");
    $obPDF->addCampo("recurso", 5, '', '', '', 0);
}

if (isset($arFiltro["stDemonstracaoElementoDespesa"]) && $arFiltro["stDemonstracaoElementoDespesa"] == "S") {
    $obPDF->setAlinhamento("L");
    $obPDF->addCampo("despesa", 6, '', '', '', 0);
}

switch ($arFiltro['inSituacao']) {
    //Empenhados
    case 1:
            $obPDF->setAlinhamento ( "R" );
            $obPDF->addCampo("valor", 6,'','','','',1 );
            $obPDF->setAlinhamento ( "R" );
            $obPDF->addCampo("valor_anulado", 6,'','','','',1 );
            $obPDF->setAlinhamento ( "R" );
            $obPDF->addCampo("saldo", 6,'','','','',1 );
    break;
    //Liquididados
    case 3:
            $obPDF->setAlinhamento ( "R" );
            $obPDF->addCampo("valor", 6,'','','','',1 );
            $obPDF->setAlinhamento ( "R" );
            $obPDF->addCampo("valor_anulado", 6,'','','','',1 );
            $obPDF->setAlinhamento ( "R" );
            $obPDF->addCampo("saldo", 6,'','','','',1 );
    break;

    default:
            $obPDF->setAlinhamento ( "R" );
            $obPDF->addCampo("valor", 6,'','','','',1 );
    break;
}

$arAssinaturas = Sessao::read('assinaturas');
if ( count($arAssinaturas['selecionadas']) > 0 ) {
    include_once( CAM_FW_PDF."RAssinaturas.class.php" );
    $obRAssinaturas = new RAssinaturas;
    $obRAssinaturas->setArAssinaturas( $arAssinaturas['selecionadas'] );
    $obPDF->setAssinaturasDefinidas( $obRAssinaturas->getArAssinaturas() );
    $obRAssinaturas->montaPDF( $obPDF );
}

$obPDF->show();
?>
