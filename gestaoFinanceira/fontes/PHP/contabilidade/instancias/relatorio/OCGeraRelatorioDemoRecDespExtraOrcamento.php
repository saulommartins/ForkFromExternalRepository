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
    * Pagina oculta para gerar relatorio de Demonstração Receita e Despesa Extra-Orçamentária
    * Data de Criação   : 12/05/2005

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Cleisson da Silva Barboza

    * @ignore

    * $Id: OCGeraRelatorioDemoRecDespExtraOrcamento.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.02.15
*/

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

$obRRelatorio->setExercicio  ( Sessao::getExercicio() );
$obRRelatorio->recuperaCabecalho( $arConfiguracao );
$obPDF->setModulo            ( "Relatorio"   );
$obPDF->setAcao             ( "Demonstrativo da Receita e Despesa Extra-Orçamentária" );
$dtPeriodo = "Período: " . $arFiltro['stDataInicial']." a ".$arFiltro['stDataFinal'] ."  ".$arFiltro['relatorio'];
$obPDF->setSubTitulo   ( $dtPeriodo  );
$obPDF->setUsuario           ( Sessao::getUsername() );
$obPDF->setEnderecoPrefeitura( $arConfiguracao );

$obPDF->addRecordSet( Sessao::read('rsRecordSet') );
$obPDF->addIndentacao  ("nivel","nom_conta","      ");

$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho   ( "" , 50, 10);
$obPDF->setAlinhamento ( "R" );
$obPDF->addCabecalho   ( "EXERCÍCIO ANTERIOR"   ,12, 10);
$obPDF->addCabecalho   ( "DÉBITO"          ,12, 10);
$obPDF->addCabecalho   ( "CRÉDITO"         ,12, 10);
$obPDF->addCabecalho   ( "EXERCÍCIO SEGUINTE"      ,12, 10);

$obPDF->setAlinhamento ( "L" );
$obPDF->addCampo       ( "nom_conta"            , 8 );
$obPDF->setAlinhamento ( "R" );
$obPDF->addCampo       ( "vl_saldo_anterior"    , 8 );
$obPDF->addCampo       ( "vl_saldo_debitos"     , 8 );
$obPDF->addCampo       ( "vl_saldo_creditos"    , 8 );
$obPDF->addCampo       ( "vl_saldo_atual"       , 8 );

$obPDF->show();
?>
