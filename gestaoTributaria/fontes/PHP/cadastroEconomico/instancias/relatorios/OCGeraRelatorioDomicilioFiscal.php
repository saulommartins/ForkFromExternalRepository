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


    * Filtro para Relatorio de Domicilio Fiscal
    * Data de Criação   : 09/09/2014    
    * @author Desenvolvedor: Evandro Melos
    * @package URBEM    
    * $Id: OCGeraRelatorioDomicilioFiscal.php 59807 2014-09-12 12:31:14Z evandro $

*/

include_once '../../../../../../config.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_MPDF;

$obMPDF = new FrameWorkMPDF(5,14,2);
$obMPDF->setCodEntidades($inCodEntidades);
$obMPDF->setNomeRelatorio("Relatório de Domicilio Fiscal");
$obMPDF->setFormatoFolha("A4");

$arDados = Sessao::read('arDomicilioFiscal');
$obMPDF->setConteudo($arDados);

$obMPDF->gerarRelatorio();

?>
