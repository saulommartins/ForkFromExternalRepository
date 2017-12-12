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

/**
 * Arquivo de instância para Relatorio
 * Data de Criação: 25/07/2005

 * @author Analista: Cassiano
 * @author Desenvolvedor: Cassiano

 $Id: OCGeraRelatorioOrgao.php 59612 2014-09-02 12:00:51Z gelson $

 Casos de uso: uc-01.05.02
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_FW_PDF."RRelatorio.class.php";

$obRRelatorio = new RRelatorio;
$obPDF        = new ListaPDF('L');

$obRRelatorio->setExercicio  ( Sessao::getExercicio() );
$obRRelatorio->recuperaCabecalho( $arConfiguracao );
$obPDF->setModulo            ( "Organograma - Orgão " );
$obPDF->setTitulo            ( "Relatório de Orgãos" );
$obPDF->setSubTitulo         ( "Exercício: ".Sessao::getExercicio() );
$obPDF->setUsuario           ( Sessao::getUsername() );
$obPDF->setEnderecoPrefeitura( $arConfiguracao );   

$rsOrgao = Sessao::read('rsOrgao');

$obPDF->addRecordSet( $rsOrgao );

$obPDF->setAlinhamento ( "C" );
$obPDF->addCabecalho("CÓDIGO"      ,  8 , 10);
$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho("DESCRIÇÃO"   , 40 , 10);
$obPDF->addCabecalho("CRIAÇÃO"     , 12 , 10);
$obPDF->addCabecalho("RESPONSÁVEL" , 25 , 10);
$obPDF->addCabecalho("E-MAIL"      , 20 , 10);
$obPDF->addIndentacao("nivel"      ,"descricao","    ");

$obPDF->setAlinhamento ( "C" );
$obPDF->addCampo("cod_orgao" , 8);
$obPDF->setAlinhamento ( "L" );
$obPDF->addCampo("descricao" , 8);
$obPDF->addCampo("criacao"   , 8);
$obPDF->addCampo("nom_cgm"   , 8);
$obPDF->addCampo("e_mail"    , 8);

$obPDF->show();

?>
