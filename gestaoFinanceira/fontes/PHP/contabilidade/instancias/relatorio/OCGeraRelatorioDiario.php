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
  * Arquivo Oculto
  * Data de criação : 13/03/2006

  * @author Analista: Diego Barbosa Victoria
  * @author Programador: Diego Barbosa Victoria

  * $Id: OCGeraRelatorioDiario.php 59612 2014-09-02 12:00:51Z gelson $

  Caso de uso: uc-02.02.23
**/

include_once '../../../../../../config.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php" );

$obRRelatorio = new RRelatorio;
$obPDF        = new ListaPDF('L');
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
$obPDF->setTitulo            ( "Diario Geral" );
$obPDF->setUsuario           ( Sessao::getUsername() );
$obPDF->setEnderecoPrefeitura( $arConfiguracao );
$obPDF->setPaginaInicial     ( $arFiltro['inUltimaPagina'] );

$inCount2=0;

$rsRecordSet = Sessao::read('rsRecordSet');

if ($rsRecordSet) {
foreach ($rsRecordSet as $stData=>$rsRecordSet) {

    $rsRecordSet->setIgnoraNumericVazio(true);

    $stCabecDeb = "Data: $stData ";
    for ($inCount=0; $inCount<44; $inCount++) {
        $stCabecDeb .= "  ";
    }
    $stCabecDeb .= "Conta Débito";
    for ($inCount=0; $inCount<50; $inCount++) {
        $stCabecDeb .= "  ";
    }
    $stCabecDeb .= "Histórico / Complemento";
    for ($inCount=0; $inCount<50; $inCount++) {
        $stCabecDeb .= "  ";
    }
    $stCabecCred = '';
    for ($inCount=0; $inCount<55; $inCount++) {
        $stCabecCred .= "  ";
    }
    $stCabecCred .= "Conta Crédito";
    $obPDF->setAlturaCabecalho(3);
    $obPDF->addRecordSet( $rsRecordSet );

    $obPDF->setQuebraPaginaLista( true  );
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho   ( $stCabecDeb  ,40, 10);
    $obPDF->addCabecalho   ( $stCabecCred ,40, 10);
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCabecalho   ( "                  Débito"   ,10, 10);
    $obPDF->addCabecalho   ( "                  Crédito"  ,10, 10);

    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo       ( "conta_debito"  , 8 );
    $obPDF->addCampo       ( "conta_credito"  , 8 , "" , "Arial" , "0", "255,255,255" , "0");
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCampo       ( "valor_debito"  , 8 );
    $obPDF->addCampo       ( "valor_credito" , 8 );

    $inCount2++;
}
}

$obPDF->show();

include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php" );
$obTAdministracaoConfiguracao   = new TAdministracaoConfiguracao;
$obTAdministracaoConfiguracao->setDado( "exercicio" , Sessao::getExercicio() );
$obTAdministracaoConfiguracao->setDado( "cod_modulo", 9 );
$obTAdministracaoConfiguracao->setDado( "parametro" , 'diario_ultima_pagina' );
$obTAdministracaoConfiguracao->setDado( "valor"     , ( $obPDF->PageNo() + $obPDF->getPaginaInicial() ) );
$obTAdministracaoConfiguracao->alteracao();
if ( $arFiltro['stDataFinal']=='31/12/'.Sessao::getExercicio() ) {
    $obTAdministracaoConfiguracao   = new TAdministracaoConfiguracao;
    $obTAdministracaoConfiguracao->setDado( "exercicio" , Sessao::getExercicio() );
    $obTAdministracaoConfiguracao->setDado( "cod_modulo", 9 );
    $obTAdministracaoConfiguracao->setDado( "parametro" , 'diario_ultima_pagina_exercicio' );
    $obTAdministracaoConfiguracao->setDado( "valor"     , ( $obPDF->PageNo() + $obPDF->getPaginaInicial() ) );
    $obTAdministracaoConfiguracao->alteracao();
}
