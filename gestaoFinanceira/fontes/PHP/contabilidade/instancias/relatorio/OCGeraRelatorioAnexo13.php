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
    * Pagina oculta para gerar relatorio
    * Data de Criação   : 28/04/2005

    * @author Desenvolvedor: Fernando Zank Correa Evangelista

    * @ignore

    $Id: OCGeraRelatorioAnexo13.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.02.10
*/

include_once '../../../../../../config.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php"            );

$obRRelatorio = new RRelatorio;
$obPDF        = new ListaPDF();

// Adicionar logo nos relatorios
$arFiltro = Sessao::read('filtroRelatorio');
if ( count( $arFiltro['inCodEntidade'] ) == 1 ) {
    $obRRelatorio->setCodigoEntidade( $arFiltro['inCodEntidade'][0] );
    $obRRelatorio->setExercicioEntidade ( Sessao::getExercicio() );
}

$obRRelatorio->setExercicio  ( Sessao::getExercicio() );
$obRRelatorio->recuperaCabecalho( $arConfiguracao );
$obPDF->setModulo            ( "Contabilidade " );

if ($arFiltro['stTipoRelatorio'] == "E")
    $tipo = "Empenhados";
if ($arFiltro['stTipoRelatorio'] == "P")
    $tipo = "Pagos";
if ($arFiltro['stTipoRelatorio'] == "L")
    $tipo = "Liquidados";

if ($arFiltro['inCodDemonstracaoDespesa'] == 1)
    $demonstracao = "por Função";
else
    $demonstracao = "por Categoria Econômica";

$obPDF->setComplementoAcao ( " - Balanço Financeiro ".$demonstracao ) ;

$dtPeriodo = "Período: " . $arFiltro['stDataInicial']." a ".$arFiltro['stDataFinal'] ."  ".$arFiltro['relatorio'];
$obPDF->setSubTitulo   ( $dtPeriodo . " - Valores ".$tipo);

$obPDF->setUsuario           ( Sessao::getUsername() );
$obPDF->setEnderecoPrefeitura( $arConfiguracao );

$rsAnexo13 = Sessao::read('rsAnexo13');
$rsAnexo13->addFormatacao( 'valor_receita', 'NUMERIC_BR_NULL' );
$rsAnexo13->addFormatacao( 'total_receita', 'NUMERIC_BR_NULL' );
$rsAnexo13->addFormatacao( 'valor_despesa', 'NUMERIC_BR_NULL' );
$rsAnexo13->addFormatacao( 'total_despesa', 'NUMERIC_BR_NULL' );

$obPDF->addRecordSet( $rsAnexo13 );

$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho( "   Receita", 29, 08 );
$obPDF->addCabecalho( ""          , 10, 08 );
$obPDF->addCabecalho( ""          , 11, 08 );
$obPDF->addCabecalho( ""          ,  2, 08 );
$obPDF->addCabecalho( "   Despesa", 29, 08 );
$obPDF->addCabecalho( ""          , 10, 08 );
$obPDF->addCabecalho( ""          , 11, 08 );

//Gera os elementos da receita
$obPDF->addIndentacao("nivel_receita","nom_conta_receita","");
$obPDF->setAlinhamento ( "L" );
$obPDF->addCampo       ("nom_conta_receita", 5 );
$obPDF->setAlinhamento ( "R" );
$obPDF->addCampo       ("valor_receita", 6 );
$obPDF->addCampo       ("total_receita", 6 );
$obPDF->addCampo       ("espaço-vazio" , 5 );
$obPDF->setAlinhamento ( "L" );

//Gera os elementos da Despesa
$obPDF->addIndentacao  ("nivel_despesa","nom_conta_despesa","");
$obPDF->addCampo       ("nom_conta_despesa", 5 );
$obPDF->setAlinhamento ( "R" );
$obPDF->addCampo       ("valor_despesa", 6 );
$obPDF->addCampo       ("total_despesa", 6 );

$obPDF->show();
?>
