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

  * Página Oculta para gerar o arquivo Anexo DCA I-E
  * Data de Criação: 07/07/2015

  * @author Analista:      Ane 
  * @author Desenvolvedor: Evandro Melos
  *
  * @ignore
  * $Id: OCGeraRelatorioSiconfiAnexoDCAIF.php 62980 2015-07-14 17:06:33Z carlos.silva $
  * $Date: $
  * $Author: $
  * $Rev: $
  *
*/
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GF_ORC_MAPEAMENTO."FOrcamentoConsolidadoElemDesp.class.php";
include_once CLA_MPDF;

$rsRecordSet = new RecordSet();

$inEntidades = implode(',',$request->get('inCodEntidade'));

$obFOrcamentoConsolidadoElemDesp = new FOrcamentoConsolidadoElemDesp();
$obFOrcamentoConsolidadoElemDesp->setDado('exercicio'    , Sessao::getExercicio());
$obFOrcamentoConsolidadoElemDesp->setDado('cod_entidade' , $inEntidades);
$obFOrcamentoConsolidadoElemDesp->setDado('data_final'   , $request->get('stDataFinal'));
$obFOrcamentoConsolidadoElemDesp->recuperaAnexoDCAIF( $rsRecordSet, "", "",$boTransacao );

$arDados['arDespElementos'] = $rsRecordSet->getElementos();

$obMPDF = new FrameWorkMPDF(6,66,6);
$obMPDF->setCodEntidades($inEntidades);
$obMPDF->setDataInicio("01/01/".Sessao::getExercicio());
$obMPDF->setDataFinal("31/12/".Sessao::getExercicio());
$obMPDF->setFormatoFolha("A4-L");

$obMPDF->setNomeRelatorio("Anexo DCA I-F");

$obMPDF->setConteudo($arDados);

$obMPDF->gerarRelatorio();
?>