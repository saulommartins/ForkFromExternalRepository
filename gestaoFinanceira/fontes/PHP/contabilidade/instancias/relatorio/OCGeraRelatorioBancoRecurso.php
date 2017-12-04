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
    * Data de Criação   : 25/11/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo
    * @author Desenvolvedor: Gelson W. Gonçalves

    * @ignore

    * $Id: OCGeraRelatorioBancoRecurso.php 64186 2015-12-11 20:36:20Z franver $

    * Casos de uso: uc-02.02.18
*/

include_once '../../../../../../config.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php" );

$obRRelatorio = new RRelatorio;
$obPDF        = new ListaPDF    ( "L" );

// Adicionar logo nos relatorios
$arFiltro = Sessao::read('filtroRelatorio');
if ( count( $arFiltro['inCodEntidade'] ) == 1 ) {
    $obRRelatorio->setCodigoEntidade( $arFiltro['inCodEntidade'][0] );
    $obRRelatorio->setExercicioEntidade ( Sessao::getExercicio() );
}

$rsBancoRecurso = Sessao::read('rsBancoRecurso');

$obRRelatorio->setExercicio     ( Sessao::getExercicio() );
$obRRelatorio->recuperaCabecalho( $arConfiguracao );
$obPDF->setModulo               ( "Relatorio" );
$obPDF->setTitulo               ( "Plano de Contas Banco/Recurso" );
$obPDF->setSubTitulo            ( "Exercicio - ".Sessao::getExercicio() );
$obPDF->setUsuario              ( Sessao::getUsername() );
$obPDF->setEnderecoPrefeitura   ( $arConfiguracao );
$obPDF->addRecordSet            ( $rsBancoRecurso );

//$obPDF->setAlinhamento          ( "L" );
//$obPDF->addCabecalho            ( "ESTRUTURAL", 10, 10);
//$obPDF->setAlinhamento          ( "R" );
//$obPDF->addCabecalho            ( "RED"       ,  4, 10);
//$obPDF->setAlinhamento          ( "L" );
//$obPDF->addCabecalho            ( "DESCRIÇÃO" , 38, 10);
//$obPDF->addCabecalho            ( "RECURSO"   , 28, 10);
//$obPDF->addCabecalho            ( "BANCO"     ,  6, 10);
//$obPDF->addCabecalho            ( "AG"        ,  4, 10);
//$obPDF->addCabecalho            ( "CONTA"     ,  10, 10);
//
//$obPDF->setAlinhamento          ( "L" );
//$obPDF->addCampo                ( "cod_estrutural", 8 );
//$obPDF->setAlinhamento          ( "R" );
//$obPDF->addCampo                ( "cod_plano", 8 );
//$obPDF->setAlinhamento          ( "L" );
//$obPDF->addCampo                ( "nom_conta", 8 );
//$obPDF->addCampo                ( "[cod_recurso] [nom_recurso]", 8 );
//$obPDF->addCampo                ( "num_banco", 8 );
//$obPDF->addCampo                ( "num_agencia", 8 );
//$obPDF->addCampo                ( "conta_corrente", 8 );

$obPDF->setAlinhamento          ( "L" );
$obPDF->addCabecalho            ( "ESTRUTURAL", 10, 10);
$obPDF->setAlinhamento          ( "R" );
$obPDF->addCabecalho            ( "RED"       ,  4, 10);
$obPDF->setAlinhamento          ( "L" );
$obPDF->addCabecalho            ( "DESCRIÇÃO" , 32, 10);
$obPDF->addCabecalho            ( "RECURSO"   , 35, 10);
$obPDF->addCabecalho            ( "BANCO"     ,  6, 10);
$obPDF->addCabecalho            ( "AG"        ,  4, 10);
$obPDF->addCabecalho            ( "CONTA"     ,  9, 10);

$obPDF->setAlinhamento          ( "L" );
$obPDF->addCampo                ( "cod_estrutural", 6 );
$obPDF->setAlinhamento          ( "R" );
$obPDF->addCampo                ( "cod_plano", 6 );
$obPDF->setAlinhamento          ( "L" );
$obPDF->addCampo                ( "nom_conta", 6 );
$obPDF->addCampo                ( "[cod_recurso] - [nom_recurso]", 6 );
$obPDF->addCampo                ( "num_banco", 6 );
$obPDF->addCampo                ( "num_agencia", 6 );
$obPDF->addCampo                ( "conta_corrente", 6 );

$obPDF->show();
?>
