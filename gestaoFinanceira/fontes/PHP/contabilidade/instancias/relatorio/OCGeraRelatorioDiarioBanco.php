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
  * Página de
  * Data de criação : 11/07/2005

    * @author Analista:
    * @author Programador: Fernando Zank Correa Evangelista

    * $Id: OCGeraRelatorioDiarioBanco.php 62669 2015-06-02 18:30:22Z lisiane $

    Caso de uso: uc-02.02.24
**/

include_once '../../../../../../config.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php" );

$obRRelatorio = new RRelatorio;
$obPDF        = new ListaPDF("L");
$rsVazio      = new RecordSet;

// Adicionar logo nos relatorios
$arFiltro = Sessao::read('filtroRelatorio');
if ( count( $arFiltro['inCodEntidade'] ) == 1 ) {
    $obRRelatorio->setCodigoEntidade( $arFiltro['inCodEntidade'][0] );
    $obRRelatorio->setExercicioEntidade ( Sessao::getExercicio() );
}
$rsRecordSet = Sessao::read('rsRecordSet');

$obRRelatorio->setExercicio  ( Sessao::getExercicio() );
$obRRelatorio->recuperaCabecalho( $arConfiguracao );
$obPDF->setModulo            ( "Relatorio"   );
 $obPDF->setTitulo            ( "DIÁRIO DE BANCOS" );
$obPDF->setSubTitulo         ( "Período: ". $arFiltro['stDataInicial']." até ". $arFiltro['stDataFinal'] );
$obPDF->setUsuario           ( Sessao::getUsername() );
$obPDF->setEnderecoPrefeitura( $arConfiguracao );

$obPDF->addRecordSet( $rsRecordSet );
$obPDF->addIndentacao  ("nivel","nom_conta","      ");

$obPDF->setAlinhamento ( "C" );
$obPDF->addCabecalho   ( "CÓDIGO REDUZIDO" , 15,10);
$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho   ( "CONTA" , 15,10);
$obPDF->addCabecalho   ( "DESCRIÇÃO DA CONTA" , 20,10);
$obPDF->setAlinhamento ( "R" );
$obPDF->addCabecalho   ( "SALDO ANTERIOR"   ,12, 10);
$obPDF->addCabecalho   ( "DÉBITOS"          ,12, 10);
$obPDF->addCabecalho   ( "CRÉDITOS"         ,12, 10);
$obPDF->addCabecalho   ( "SALDO ATUAL"      ,12, 10);

$obPDF->setAlinhamento ( "C" );
$obPDF->addCampo       ( "cod_plano"            , 8 );
$obPDF->setAlinhamento ( "L" );
$obPDF->addCampo       ( "cod_estrutural"            , 8 );
$obPDF->addCampo       ( "nom_conta"            , 8 );
$obPDF->setAlinhamento ( "R" );
$obPDF->addCampo       ( "vl_saldo_anterior"    , 8 );
$obPDF->addCampo       ( "vl_saldo_debitos"     , 8 );
$obPDF->addCampo       ( "vl_saldo_creditos"    , 8 );
$obPDF->addCampo       ( "vl_saldo_atual"       , 8 );

$obPDF->show();
