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
    * Página de processamento oculto para o relatório de corretagem
    * Data de Criação   : 30/03/2005

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Marcelo Boezio Paulino

    * @ignore

    * $Id: OCGeraRelatorioCorretagem.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.21
*/

/*
$Log$
Revision 1.7  2006/09/18 10:31:34  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
//include_once("../../../includes/Constante.inc.php");
//include_once("../../../includes/tabelas.inc.php");
//include_once( CAM_INCLUDES."IncludeClasses.inc.php" );
include_once( CAM_FW_PDF."RRelatorio.class.php" );
include_once( CAM_FW_PDF."ListaPDF.class.php" );

$arFiltro = Sessao::read('filtroRelatorio');
$obRRelatorio = new RRelatorio;
if ($arFiltro['stTipoCorretagem'] != 'corretor') {
    $obPDF = new ListaPDF("L");
    $widthCGM = '40';
} else {
    $obPDF = new ListaPDF();
    $widthCGM = '45';
}

$obRRelatorio->setExercicio  ( Sessao::getExercicio() );
$obRRelatorio->recuperaCabecalho( $arConfiguracao );
$obPDF->setModulo            ( "Relatorio:"   );
$obPDF->setTitulo            ( "Corretagem:" );
$obPDF->setSubTitulo         ( "Exercício - ".Sessao::getExercicio() );
$obPDF->setUsuario           ( Sessao::getUsername() );
$obPDF->setEnderecoPrefeitura( $arConfiguracao );

$obPDF->addRecordSet( Sessao::read('sessao_transf5'));

$obPDF->setAlinhamento ( "L" );
if ($arFiltro['stTipoCorretagem'] == 'todos') {
    $obPDF->addCabecalho( "TIPO", 10, 10 );
}
$obPDF->addCabecalho   ( "CGM"   ,$widthCGM, 10 );
$obPDF->addCabecalho   ( "CRECI" ,15, 10 );
if ($arFiltro['stTipoCorretagem'] != 'corretor') {
    $obPDF->addCabecalho( "RESPONSÁVEL"   ,40, 10 );
}

$obPDF->setAlinhamento ( "L" );
if ($arFiltro['stTipoCorretagem'] == 'todos') {
    $obPDF->addCampo   ( "tipo"        , 8 );
}
$obPDF->addCampo       ( "cgm"         , 8 );
$obPDF->addCampo       ( "creci"       , 8 );
if ($arFiltro['stTipoCorretagem'] != 'corretor') {
    $obPDF->addCampo   ( "responsavel" , 8 );
}

$obPDF->addFiltro( 'Tipo de Corretagem' , $sessao->nomFiltro['tipo_corretagem'][$arFiltro['stTipoCorretagem']] );
$obPDF->addFiltro( 'Nome'               , $arFiltro['stNomCGM']                                                );
$obPDF->addFiltro( 'CGM Inicial'        , $arFiltro['inCGMInicio']                                             );
$obPDF->addFiltro( 'CGM Final'          , $arFiltro['inCGMTermino']                                            );
$obPDF->addFiltro( 'Ordenação'          , $sessao->nomFiltro['ordenacao'][$arFiltro[ 'stOrder']]               );

$rsDados = Sessao::read('sessao_transf5');

$rsTotais = new RecordSet;
$arTeste  = array();
$inTotalRegs = $rsDados->getNumLinhas();
if ( $inTotalRegs < 0)
    $inTotalRegs = 0;

$arTeste[0]["total"] = $inTotalRegs;
$rsTotais->preenche( $arTeste );

$obPDF->addRecordSet         ($rsTotais);

$obPDF->setQuebraPaginaLista ( false         );
$obPDF->setAlinhamento       ( "L"           );
$obPDF->addCabecalho         ( "Total de registros", 100, 10  );

$obPDF->setAlinhamento       ( "L"           );
$obPDF->addCampo             ( "total",  8  );

$obPDF->show();
?>
