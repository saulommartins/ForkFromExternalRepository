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
    * Página oculta para gerar relatório
    * Data de Criação   : 30/06/2005

    * @author Analista: Dieine da Silva
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    $Revision: 31801 $
    $Name$
    $Autor:$
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.01.05
*/

/*
$Log$
Revision 1.6  2006/07/05 20:43:28  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../config.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_FW_INCLUDE."IncludeClasses.inc.php"      );
include_once( CAM_FW_PDF."RRelatorio.class.php"           );

$obRRelatorio = new RRelatorio;
$obPDF        = new ListaPDF("L");

$arFiltro = Sessao::read('filtroRelatorio');
$obPDF->addFiltro( "Exercício: ", $arFiltro['stExercicio'] );

$obRRelatorio->setExercicio  ( Sessao::getExercicio() );
$obRRelatorio->recuperaCabecalho( $arConfiguracao );
$obPDF->setModulo            ( "Orçamento Geral" );
$obPDF->setTitulo            ( "Relatório de Recursos" );
$obPDF->setUsuario           ( Sessao::getUsername() );
$obPDF->setEnderecoPrefeitura( $arConfiguracao );

// RecordSet para Relatorio
$obPDF->addRecordSet( Sessao::read('rsRelatorio') );
$obPDF->setAlinhamento ( "R" );
$obPDF->addCabecalho("Código"     ,  7, 10 );
$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho("Descrição"  , 35, 10 );
$obPDF->addCabecalho("Finalidade" , 58, 10 );

$obPDF->setAlinhamento ( "R" );
$obPDF->addCampo("cod_recurso", 8 );
$obPDF->setAlinhamento ( "L" );
$obPDF->addCampo("nom_recurso", 8 );
$obPDF->addCampo("finalidade" , 8 );

$obPDF->show();
?>
