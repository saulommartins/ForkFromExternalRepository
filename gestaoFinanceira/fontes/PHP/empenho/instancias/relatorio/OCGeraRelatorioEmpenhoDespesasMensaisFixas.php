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
    * Página de Geração de Relatório de Despesas Mensais Fixas
    * Data de Criação : 06/09/2006

    * @author Analista: Cleisson Barboza
    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @ignore

    $Revision: 30805 $
    $Name$
    $Autor: $
    $Date: 2008-01-15 12:00:12 -0200 (Ter, 15 Jan 2008) $

    * Casos de uso: uc-02.03.33
*/

/**

$Log$
Revision 1.1  2006/09/08 10:23:00  tonismar
relatório de despesas fixas

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GF_EMP_NEGOCIO."REmpenhoRelatorioDespesasMensaisFixas.class.php");

$obREmpenhoRelatorioDespesasMensaisFixas = new REmpenhoRelatorioDespesasMensaisFixas;
$obPDF           = new ListaPDF( "L" );
$arVazio         = new RecordSet;

$obREmpenhoRelatorioDespesasMensaisFixas->obRRelatorio->recuperaCabecalho ( $arConfiguracao );
$arFiltro = Sessao::read('filtroRelatorio');
$rsRecordSet0 = Sessao::read('rsRecordSet0');
$rsRecordSet1 = Sessao::read('rsRecordSet1');

$obPDF->setModulo                ( "Contabilidade - ".Sessao::getExercicio()   );
$obPDF->setTitulo                ( "Razão da Despesa " . $arFiltro['relatorio'] );

$subTitulo = "Periodicidade: ".$arFiltro['stDataInicial'] . " a " . $arFiltro['stDataFinal'];
$obPDF->setSubTitulo         ( $subTitulo  );

$obPDF->setUsuario           ( Sessao::getUsername() );
$obPDF->setEnderecoPrefeitura( $arConfiguracao );

$rsVazio = $rsVazio2 = new RecordSet;

$obPDF->setAcao( "DespesasMensaisFixas" );

$inCount = 0;

$obPDF->addFiltro( 'Entidade:', $arFiltro['inCodEntidade'] );
$obPDF->addFiltro( 'Exercício:', $arFiltro['stExercicio'] );
$obPDF->addFiltro( 'Data de Inclusão:', $arFiltro['stDataInicial'].' até '.$arFiltro['stDataFinal'] );
$obPDF->addFiltro( 'Tipo:', $arFiltro['inCodTipo'] );
if ($arFiltro['inContrato']) {
    $obPDF->addFiltro( 'Nr. Contrato:', $arFiltro['inContrato'] );
}
if ($arFiltro['inCodLocal']) {
    $obPDF->addFiltro( 'Local:', $arFiltro['inCodLocal'] );
}
if ($arFiltro['inCodDotacao']) {
    $obPDF->addFiltro( 'Dotação:', $arFiltro['inCodDotacao'] );
}
if ($arFiltro['inCodCredor']) {
    $obPDF->addFiltro( 'Credor:', $arFiltro['inCodCredor'] );
}
if ($arFiltro['inCodLocal']) {
    $obPDF->addFiltro( 'Local:', $arFiltro['inCodLocal'] );
}

if ( count($rsRecordSet0) > 0 ) {

    foreach ($rsRecordSet0 as $recordSet) {

        $obPDF->addRecordSet( $recordSet );
        $obPDF->setAlturaCabecalho(5);
        $obPDF->setAlinhamento ( "L" );
        $obPDF->addCabecalho("", 10, 8);
        $obPDF->addCabecalho("", 50, 8);

        $obPDF->setAlinhamento ( "L" );
        $obPDF->addCampo("coluna1", 8, 'b' );
        $obPDF->addCampo("coluna2", 8 );

        if ( is_object( $rsRecordSet1[$inCount] ) ) {
            $obPDF->addRecordSet( $rsRecordSet1[$inCount] );
            $obPDF->setAlturaCabecalho( 0             );
            $obPDF->setQuebraPaginaLista( false       );
            $obPDF->setAlinhamento( "C"               );
            $obPDF->addCabecalho("Empenho",     15, 10, 'b');
            $obPDF->addCabecalho("Data Empenho", 15, 10, 'b');
            $obPDF->setAlinhamento( "R"               );
            $obPDF->addCabecalho("Empenhado", 15, 10, 'b');
            $obPDF->addCabecalho("Liquidado",15, 10, 'b');
            $obPDF->addCabecalho("Pago",       15, 10, 'b');
            $obPDF->addCabecalho("A Pagar Liquidado", 20, 10, 'b');

            $obPDF->setAlinhamento( "C"         );
            $obPDF->addCampo("empenho"         , 8 );
            $obPDF->addCampo("dt_empenho"    , 8 );
            $obPDF->setAlinhamento( "R"         );
            $obPDF->addCampo("empenhado"  , 8 );
            $obPDF->addCampo("liquidado", 8 );
            $obPDF->addCampo("pago"       	, 8 );
            $obPDF->addCampo("pagar_liquidado"       	, 8 );
        } else {
            $rsVazio2 = new RecordSet;
            $obPDF->addRecordSet( $rsVazio2 );
            $obPDF->setAlturaCabecalho( 0             );
            $obPDF->setQuebraPaginaLista( false       );
            $obPDF->setAlinhamento( "C"               );
            $obPDF->addCabecalho("Empenho",     15, 10, 'b');
            $obPDF->addCabecalho("Data Empenho", 15, 10, 'b');
            $obPDF->setAlinhamento( "R"               );
            $obPDF->addCabecalho("Empenhado", 15, 10, 'b');
            $obPDF->addCabecalho("Liquidado",15, 10, 'b');
            $obPDF->addCabecalho("Pago",       15, 10, 'b');
            $obPDF->addCabecalho("A Pagar Liquidado", 20, 10, 'b');
        }
        $inCount++;
    }
}

//$arAssinaturas = Sessao::read('assinaturas');
//if ( count($arAssinaturas['selecionadas']) > 0 ) {
//       include_once CAM_FW_PDF."RAssinaturas.class.php";
//       $obRAssinaturas = new RAssinaturas;
//       $obRAssinaturas->setArAssinaturas( $arAssinaturas['selecionadas'] );
//       $obPDF->setAssinaturasDefinidas( $obRAssinaturas->getArAssinaturas() );
//       $obRAssinaturas->montaPDF( $obPDF );
//}

$obPDF->show();
