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
set_time_limit(120);
/**
    * Página de oculta de geração de relaório
    * Data de Criação   : 23/06/2005

    * @author Analista: Dieine da Silva
    * @author Desenvolvedor: Cleisson Barboza

    * @ignore

    $Revision: 31801 $
    $Name$
    $Autor:$
    $Date: 2007-03-01 17:08:58 -0300 (Qui, 01 Mar 2007) $

    * Casos de uso: uc-02.01.32
*/

/*
$Log$
Revision 1.5  2007/03/01 20:00:32  luciano
#8509#

Revision 1.4  2006/07/05 20:43:28  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../config.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoRelatorioRazaoDespesa.class.php");

$obRRazaoDespesa = new ROrcamentoRelatorioRazaoDespesa;
$obPDF           = new ListaPDF( "L" );
$arVazio         = new RecordSet;

$obRRazaoDespesa->obRRelatorio->recuperaCabecalho ( $arConfiguracao );

$arFiltro = Sessao::read('filtroRelatorio');

$obPDF->setModulo                ( "Contabilidade - ".Sessao::getExercicio()   );
$obPDF->setTitulo                ( "Razão da Despesa " . $arFiltro['relatorio'] );

$subTitulo = "Periodicidade: ".$arFiltro['stDataInicial'] . " a " . $arFiltro['stDataFinal'];
$obPDF->setSubTitulo         ( $subTitulo  );

$obPDF->setUsuario           ( Sessao::getUsername() );
$obPDF->setEnderecoPrefeitura( $arConfiguracao );

$rsVazio = $rsVazio2 = new RecordSet;

// Primeira parte

$obPDF->setAcao( "Razão da Despesa" );

$inCount=0;

$arRecordSet = Sessao::read('arRecordSet');
$arRecordSet1 = Sessao::read('arRecordSet1');

if ( count($arRecordSet) > 0 ) {

foreach ($arRecordSet as $recordSet) {

    $obPDF->addRecordSet( $recordSet );
    $obPDF->setAlturaCabecalho(5);
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho("", 10, 8);
    $obPDF->addCabecalho("", 50, 8);
    $obPDF->addCabecalho("", 25, 8);
    $obPDF->addCabecalho("", 15, 8);

    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo("coluna1", 8 );
    $obPDF->addCampo("coluna2", 8 );
    $obPDF->addCampo("coluna3", 8 );
    $obPDF->addCampo("coluna4", 8 );

    //Linha Divisoria
    $obPDF->addRecordSet( $rsVazio );
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlturaCabecalho(-3);
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCabecalho("___________________________________________________________________________________________________________________________________________________________", 100, 9);

    //Lançamentos
    $obPDF->addRecordSet( $rsVazio );
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlturaCabecalho(0);
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCabecalho("Movimentações", 100, 10);

    if ( is_object( $arRecordSet1[$inCount] ) ) {
        $obPDF->addRecordSet( $arRecordSet1[$inCount] );
    } else {
        $rsVazio2 = new RecordSet;
        $obPDF->addRecordSet( $rsVazio2 );
    }

    $obPDF->setAlturaCabecalho( 0             );
    $obPDF->setQuebraPaginaLista( false       );
    $obPDF->setAlinhamento( "C"               );
    $obPDF->addCabecalho("Data",        10, 10);
    $obPDF->setAlinhamento( "L"               );
    $obPDF->addCabecalho("Histórico",   20, 10);
    $obPDF->addCabecalho("Complemento", 15, 10);
    $obPDF->addCabecalho("Contrapartida",40, 10);
    $obPDF->setAlinhamento( "R"               );
    $obPDF->addCabecalho("Valor",       10, 10);

    $obPDF->setAlinhamento( "C"         );
    $obPDF->addCampo("data"         , 8 );
    $obPDF->setAlinhamento( "L"         );
    $obPDF->addCampo("historico"    , 8 );
    $obPDF->addCampo("complemento"  , 8 );

    $obPDF->addCampo("contrapartida", 8 );

    $obPDF->setAlinhamento( "R"         );
    $obPDF->addCampo("valor"       	, 8 );
    $inCount++;
}

}
$arAssinaturas = Sessao::read('assinaturas');

if ( count($arAssinaturas['selecionadas']) > 0 ) {
    include_once( CAM_FW_PDF."RAssinaturas.class.php" );
    $obRAssinaturas = new RAssinaturas;
    $obRAssinaturas->setArAssinaturas( $arAssinaturas['selecionadas'] );
    $obPDF->setAssinaturasDefinidas( $obRAssinaturas->getArAssinaturas() );
    //$obRAssinaturas->montaPDF( $obPDF );
}

$obPDF->show();
?>
