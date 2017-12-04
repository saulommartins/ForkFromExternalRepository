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
    * Página de Busca do layout de geração do relatório Empenho por Modalidade
    * Data de Criação   : 22/03/2016

    * @author Analista: Dagiane Vieira
    * @author Desenvolvedor: Michel Teixeira

    * @package URBEM

    $Id: OCGeraRelatorioEmpenhoModalidade.php 64778 2016-03-31 13:51:44Z michel $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../config.php';
include_once CLA_MPDF;

$arDados        = Sessao::read('arDados');

$inCodEntidades = $arDados['inCodEntidade'];
$stDataInicial  = $arDados['stDataInicial'];
$stDataFinal    = $arDados['stDataFinal'];

//-------------------------------
// Preparando a chamada para o layout do relatório

//Arquivo MPDF LHEmpenhoModalidade.php
$obMPDF = new FrameWorkMPDF(2,10,13);
$obMPDF->setCodEntidades($inCodEntidades);
$obMPDF->setDataInicio($stDataInicial);
$obMPDF->setDataFinal($stDataFinal);
$obMPDF->setNomeRelatorio("Empenhos por Modalidade");
$obMPDF->setFormatoFolha("A4-L");

$obMPDF->setConteudo($arDados);

$obMPDF->gerarRelatorio();
?>
