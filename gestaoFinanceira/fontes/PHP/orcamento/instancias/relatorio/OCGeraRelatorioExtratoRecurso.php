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
    * Data de Criação   : 04/07/2005

    * @author Analista: Dieine da Silva
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    $Revision: 31801 $
    $Name$
    $Autor:$
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.01.29
*/

/*
$Log$
Revision 1.6  2006/07/05 20:43:28  cleisson
Adicionada tag Log aos arquivos

*/
include_once '../../../../../../config.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php"       );

$obRRelatorio = new RRelatorio;
$obPDF        = new ListaPDF("L");
$rsVazio      = new RecordSet;

$arFiltro = Sessao::read('filtroRelatorio');

$obRRelatorio->setExercicio  ( Sessao::getExercicio() );
$obRRelatorio->recuperaCabecalho( $arConfiguracao );
$obPDF->setModulo            ( "Orcamento Geral" . Sessao::getExercicio() );
$obPDF->setTitulo            ( "Extrato de Recurso" );
$obPDF->setSubTitulo         ( "Situação até: " . $arFiltro['stDataFinal'] );
$obPDF->setUsuario           ( Sessao::getUsername() );
$obPDF->setEnderecoPrefeitura( $arConfiguracao );

//Corrente
$rsRecordSet = Sessao::read('rsRecordSet');
$rsRecordSet->addFormatacao("saldo_anterior", "NUMERIC_BR_NULL" );
$rsRecordSet->addFormatacao("arrecadado"    , "NUMERIC_BR_NULL" );
$rsRecordSet->addFormatacao("pago"          , "NUMERIC_BR_NULL" );
$rsRecordSet->addFormatacao("pago_rp"       , "NUMERIC_BR_NULL" );
$rsRecordSet->addFormatacao("sub_total"     , "NUMERIC_BR_NULL" );
$rsRecordSet->addFormatacao("saldo_atual"   , "NUMERIC_BR_NULL" );

$obPDF->addRecordSet( $rsRecordSet );
$obPDF->setAlinhamento ( "R" );
$obPDF->addCabecalho("Recurso"        ,  6, 10 );
$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho("Descrição"      , 36, 10 );
$obPDF->setAlinhamento ( "R" );
$obPDF->addCabecalho("Saldo Anterior" , 10, 10 );
$obPDF->addCabecalho("Arrecadações"   , 12, 10 );
$obPDF->addCabecalho("Pagamentos Emp.", 12, 10 );
$obPDF->addCabecalho("Pagamentos RP"  , 12, 10 );
$obPDF->addCabecalho("Saldo Atual"    , 12, 10 );

$obPDF->setAlinhamento ( "R" );
$obPDF->addCampo("cod_recurso"   , 8 );
$obPDF->setAlinhamento ( "L" );
$obPDF->addCampo("nom_recurso"   , 8 );
$obPDF->setAlinhamento ( "R" );
$obPDF->addCampo("saldo_anterior", 8 );
$obPDF->addCampo("arrecadado"    , 8 );
$obPDF->addCampo("pago"          , 8 );
$obPDF->addCampo("pago_rp"       , 8 );
$obPDF->addCampo("sub_total"     , 8 );

$obPDF->show();

?>
