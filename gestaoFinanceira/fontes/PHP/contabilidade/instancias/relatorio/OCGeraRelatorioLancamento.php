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
set_time_limit(0);
/**
    * Página de Formulario de Seleção de Impressora para Relatorio
    * Data de Criação   : 26/11/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    * $Id: OCGeraRelatorioLancamento.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.02.21
*/

include_once '../../../../../../config.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php" );

$obRRelatorio = new RRelatorio;
$obPDF        = new ListaPDF(  );
$rsVazio      = new RecordSet;

// Adicionar logo nos relatorios
$arFiltro = Sessao::read('filtroRelatorio');
if ( count( $arFiltro['inCodEntidade'] ) == 1 ) {
    $obRRelatorio->setCodigoEntidade( $arFiltro['inCodEntidade'][0] );
    $obRRelatorio->setExercicioEntidade ( Sessao::getExercicio() );
}

$obRRelatorio->setExercicio  ( Sessao::getExercicio() );
$obRRelatorio->recuperaCabecalho( $arConfiguracao );
$obPDF->setModulo            ( "Relatorio"   );
$obPDF->setTitulo            ( "Lancamentos" );
$obPDF->setSubTitulo         ( "Exercicio - ".Sessao::getExercicio() );
$obPDF->setUsuario           ( Sessao::getUsername() );
$obPDF->setEnderecoPrefeitura( $arConfiguracao );

$obPDF->addRecordSet( $rsVazio );
$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho   ( "LOTE" , 6, 10);
$obPDF->addCabecalho   ( "DATA" ,10, 10);
$obPDF->addCabecalho   ( "SEQ"  , 6, 10);
$obPDF->addCabecalho   ( "DÉBITO / CRÉDITO / HISTÓRICO",68, 10);
$obPDF->setAlinhamento ( "R" );
$obPDF->addCabecalho   ( "VALOR",10, 10);

$rsLancamento = Sessao::read('rsLancamento');
if ($rsLancamento==null) {
    $rsLancamento = new RecordSet;
}
$obPDF->addRecordSet( $rsLancamento );
$obPDF->setQuebraPaginaLista( false );
$obPDF->addIndentacao  ("inNivel","stDescricao","          ");

$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho   ( "", 6, 10 );
$obPDF->addCabecalho   ( "",10, 10 );
$obPDF->addCabecalho   ( "", 6, 10 );
$obPDF->addCabecalho   ( "",12, 10 );
$obPDF->addCabecalho   ( "",56, 10 );
$obPDF->addCabecalho   ( "",10, 10 );

$obPDF->setAlinhamento ( "L" );
$obPDF->addCampo       ( "inCodLote"    , 8 );
$obPDF->addCampo       ( "stDtLote"     , 8 );
$obPDF->setAlinhamento ( "R" );
$obPDF->addCampo       ( "inSequencia"  , 8 );
$obPDF->setAlinhamento ( "L" );
$obPDF->addCampo       ( "stTipoECodigo", 8 );
$obPDF->addCampo       ( "stDescricao"  , 8 );
$obPDF->setAlinhamento ( "R" );
$obPDF->addCampo       ( "nuValor"      , 8 );

$obPDF->show();
?>
