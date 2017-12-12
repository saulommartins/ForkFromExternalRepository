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
    * Arquivo que gera o relatorio de Razão do Empenho
    * Data de Criação: 02/06/2005

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Cleisson Barboza

    * @ignore

    $Revision: 30805 $
    $Name$
    $Author: luciano $
    $Date: 2007-02-13 10:27:40 -0200 (Ter, 13 Fev 2007) $

    * Casos de uso: uc-02.03.14
*/

/*
$Log$
Revision 1.8  2007/02/13 12:26:52  luciano
#8381#

Revision 1.7  2006/07/05 20:48:34  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php" );

$obRRelatorio = new RRelatorio;
$obPDF        = new ListaFormPDF();
$rsVazio      = new RecordSet;

$arRecordSet0 = Sessao::read('arRecordSet0');
$arRecordSet1 = Sessao::read('arRecordSet1');
$arRecordSet2 = Sessao::read('arRecordSet2');
$arRecordSet3 = Sessao::read('arRecordSet3');
$arRecordSet4 = Sessao::read('arRecordSet4');
$arRecordSet5 = Sessao::read('arRecordSet5');
$arRecordSet6 = Sessao::read('arRecordSet6');
$arRecordSet7 = Sessao::read('arRecordSet7');
$arFiltroRelatorio = Sessao::read('filtroRelatorio');
// Adicionar logo da entidade no relatorio
$inCodEntidade = $arRecordSet1->getCampo("coluna2");
$obRRelatorio->setCodigoEntidade( $inCodEntidade );
$obRRelatorio->setExercicioEntidade ( Sessao::getExercicio() );

$obRRelatorio->setExercicio     ( $arFiltroRelatorio['stExercicio'] );
$obRRelatorio->recuperaCabecalho( $arConfiguracao );
$obPDF->setSubTitulo            ( "Empenho N. ".$arFiltroRelatorio['inCodEmpenho']."/".$arFiltroRelatorio['stExercicio'] );
$obPDF->setUsuario              ( Sessao::getUsername() );
$obPDF->setEnderecoPrefeitura   ( $arConfiguracao );

//echo "<pre>";
//print_r ( sessao->transf );
//echo "</pre>";
//exit(0);

//Titulo: Dados do Empenho - Dotação
    $obPDF->addRecordSet( $rsVazio );
    $obPDF->setQuebraPaginaLista( true );
    $obPDF->setAlturaCabecalho(10);
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCabecalho("Dados do Empenho - Dotação", 100, 10);

    $obPDF->addRecordSet( $arRecordSet0 );
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlturaCabecalho(5);
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho("", 20, 8);
    $obPDF->addCabecalho("", 20, 8);
    $obPDF->addCabecalho("", 15, 8);
    $obPDF->addCabecalho("", 15, 8);
    $obPDF->addCabecalho("", 15, 8);
    $obPDF->addCabecalho("", 15, 8);

    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo("coluna1", 8 );
    $obPDF->addCampo("coluna2", 8 );
    $obPDF->addCampo("coluna3", 8 );
    $obPDF->addCampo("coluna4", 8 );
    $obPDF->addCampo("coluna5", 8 );
    $obPDF->addCampo("coluna6", 8 );

    $obPDF->addRecordSet( $arRecordSet1 );
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlturaCabecalho(5);
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho("", 15, 8);
    $obPDF->addCabecalho("", 5, 8);
    $obPDF->addCabecalho("", 80, 8);
//    $obPDF->addCabecalho("", 50, 8);

    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo("coluna1", 8 );
    $obPDF->addCampo("coluna2", 8 );
    $obPDF->addCampo("coluna3", 8 );
//    $obPDF->addCampo("coluna4", 8 );

//Linha Divisoria
    $obPDF->addRecordSet( $arRecordSet6 );
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlturaCabecalho(0);
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCabecalho("", 100, 9);
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCampo("coluna1", 9 );

//Titulo: Dados do Empenho - Credor
    $obPDF->addRecordSet( $rsVazio );
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlturaCabecalho(10);
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCabecalho("Dados do Empenho - Credor", 100, 10);

//Credor
    $obPDF->addRecordSet( $arRecordSet2 );
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlturaCabecalho(5);
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho("", 10, 8);
    $obPDF->addCabecalho("", 90, 8);

    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo("coluna1", 8 );
    $obPDF->addCampo("coluna2", 8 );

//Linha Divisoria
    $obPDF->addRecordSet( $arRecordSet6 );
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlturaCabecalho(0);
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCabecalho("", 100, 9);
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCampo("coluna1", 9 );

//Titulo: Dados do Empenho - Valores
    $obPDF->addRecordSet( $rsVazio );
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlturaCabecalho(10);
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCabecalho("Dados do Empenho - Valores", 100, 10);

//Valores
    $obPDF->addRecordSet( $arRecordSet3 );
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlturaCabecalho(5);
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho("", 20, 8);
    $obPDF->addCabecalho("", 20, 8);
    $obPDF->addCabecalho("", 40, 8);
    $obPDF->addCabecalho("", 20, 8);

    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo("coluna1", 8 );
    $obPDF->addCampo("coluna2", 8 );
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCampo("coluna3", 8 );
    $obPDF->addCampo("coluna4", 8 );

//Linha Divisoria
    $obPDF->addRecordSet( $arRecordSet6 );
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlturaCabecalho(0);
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCabecalho("", 100, 9);
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCampo("coluna1", 9 );

    $obPDF->addRecordSet( $arRecordSet4 );
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlturaCabecalho(15);
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCabecalho("Descrição do Empenho", 100, 10);

    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo("coluna1", 8 );

//Linha Divisoria
//  $obPDF->addRecordSet( $arRecordSet6 );
//  $obPDF->setQuebraPaginaLista( false );
//  $obPDF->setAlturaCabecalho(0);
//  $obPDF->setAlinhamento ( "C" );
//  $obPDF->addCabecalho("", 100, 9);
//  $obPDF->setAlinhamento ( "C" );
//  $obPDF->addCampo("coluna1", 9 );

//Ítens do Empenho
    $obPDF->addRecordSet( $rsVazio );
    $obPDF->setQuebraPaginaLista( true );
    $obPDF->setAlturaCabecalho(15);
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCabecalho("Itens do Empenho", 100, 10);

    $obPDF->addRecordSet( $arRecordSet7 );
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlturaCabecalho(15);
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCabecalho("Descrição", 60, 10);
    $obPDF->addCabecalho("Empenhado", 20, 10);
    $obPDF->addCabecalho("Liquidado", 20, 10);

    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo("coluna1", 8 );
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCampo("coluna2", 8 );
    $obPDF->addCampo("coluna2", 8 );

//Linha Divisoria
    $obPDF->addRecordSet( $arRecordSet6 );
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlturaCabecalho(0);
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCabecalho("", 100, 9);
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCampo("coluna1", 9 );
//Lançamentos
    $obPDF->addRecordSet( $rsVazio );
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlturaCabecalho(15);
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCabecalho("Lançamentos", 100, 10);

    $obPDF->addRecordSet( $arRecordSet5  );
    $obPDF->setAlturaCabecalho( 5             );
    $obPDF->setQuebraPaginaLista( false       );
    $obPDF->setAlinhamento( "C"               );
    $obPDF->addCabecalho("Data",        15, 10);
    $obPDF->setAlinhamento( "L"               );
    $obPDF->addCabecalho("Histórico",   40, 10);
    $obPDF->addCabecalho("Complemento", 15, 10);
    $obPDF->setAlinhamento( "R"               );
    $obPDF->addCabecalho("Valor",       10, 10);
    $obPDF->addCabecalho("Débito",      10, 10);
    $obPDF->addCabecalho("Crédito",     10, 10);

    $obPDF->setAlinhamento( "C"         );
    $obPDF->addCampo("data"         , 8 );
    $obPDF->setAlinhamento( "L"         );
    $obPDF->addCampo("historico"    , 8 );
    $obPDF->addCampo("complemento"  , 8 );
    $obPDF->setAlinhamento( "R"         );
    $obPDF->addCampo("valor"        , 8 );
    $obPDF->addCampo("debito"       , 8 );
    $obPDF->addCampo("credito"      , 8 );

$obPDF->show();
?>
