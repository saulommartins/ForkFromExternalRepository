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
  * Página Oculta para Geração do Relatório de Apuração de Superavit/Deficit Financeiro
  * Data de Criação: 21/10/2015

  * @author Analista:      Valtair
  * @author Desenvolvedor: Franver Sarmento de Moraes
  * @ignore
  *
  * $Id: OCGeraRelatorioApuracaoSuperavitDeficit.php 64186 2015-12-11 20:36:20Z franver $
  * $Revision: 64186 $
  * $Author: franver $
  * $Date: 2015-12-11 18:36:20 -0200 (Fri, 11 Dec 2015) $
*/
set_time_limit(0);
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
require_once CAM_GF_CONT_NEGOCIO."RContabilidadeRelatorioApuracaoSuperavitDeficitFinanceiro.class.php";
require_once CLA_MPDF;

$arCodEntidades = implode(',',$request->get('inCodEntidade'));

$obRApuracaoSuperavitDeficit = new RContabilidadeRelatorioApuracaoSuperavitDeficitFinanceiro();
$obRApuracaoSuperavitDeficit->setExercicio($request->get('stExercicio'));
$obRApuracaoSuperavitDeficit->setCodEntidades($arCodEntidades);
$obRApuracaoSuperavitDeficit->geraRecordSet($rsRecordSet);

$arDados = array('arApuracaoContabilidade' => $rsRecordSet['arApuracaoContabilidade'],
                 'arApuracaoExecucao'      => $rsRecordSet['arApuracaoExecucao'],
                 'arApuracaoExecucaoTotal' => $rsRecordSet['arApuracaoExecucaoTotal']);


$obMPDF = new FrameWorkMPDF(2,9,20);
$obMPDF->setCodEntidades($arCodEntidades);
$obMPDF->setFormatoFolha("A4-L");

$obMPDF->setNomeRelatorio("Apuração Superavit/Deficit Financeiro");

$obMPDF->setConteudo($arDados);

$obMPDF->gerarRelatorio();

?>