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
  * Página Oculta para gerar o arquivo Anexo DCA I-D
  * Data de Criação: 07/07/2015

  * @author Analista:      Eduardo Paculski Schitz
  * @author Desenvolvedor: Franver Sarmento de Moraes
  *
  * @ignore
  * $Id: OCGeraRelatorioSiconfiAnexoDCAID.php 63044 2015-07-20 14:31:09Z franver $
  * $Date: 2015-07-20 11:31:09 -0300 (Mon, 20 Jul 2015) $
  * $Author: franver $
  * $Rev: 63044 $
  *
*/
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GF_ORC_MAPEAMENTO."FOrcamentoConsolidadoElemDesp.class.php";
include_once CLA_MPDF;

$rsRecordSet = new RecordSet();

$inEntidades = implode(',',$request->get('inCodEntidade'));

$stFiltro = 'WHERE (((empenhado_mes)-(liquidado_mes)-(anulado_mes)) <> 0.00 OR ((liquidado_mes)-(pago_mes)) <> 0.00 OR ((pago_mes)-(anulado_mes)) <> 0.00 OR ((empenhado_mes)-(anulado_mes)) <> 0.00 OR ((liquidado_mes)-(anulado_mes)) <> 0.00)';

$stOrder = ' classificacao ';

$obFOrcamentoConsolidadoElemDesp = new FOrcamentoConsolidadoElemDesp();
$obFOrcamentoConsolidadoElemDesp->setDado('exercicio'     , Sessao::getExercicio());
$obFOrcamentoConsolidadoElemDesp->setDado('stEntidade'    , $inEntidades);
$obFOrcamentoConsolidadoElemDesp->setDado('stDataInicial' , '01/01/'.Sessao::getExercicio());
$obFOrcamentoConsolidadoElemDesp->setDado('stDataFinal'   , '31/12/'.Sessao::getExercicio());
$obFOrcamentoConsolidadoElemDesp->setDado('inCodFuncao'   , 0);
$obFOrcamentoConsolidadoElemDesp->setDado('inCodSubFuncao', 0);
$obFOrcamentoConsolidadoElemDesp->recuperaAnexoDCAID( $rsRecordSet, $stFiltro, $stOrder );

$arDados['arDespesaOrcamentarias'] = $rsRecordSet->getElementos();

$obMPDF = new FrameWorkMPDF(6,66,4);
$obMPDF->setCodEntidades($inEntidades);
$obMPDF->setDataInicio("01/01/".Sessao::getExercicio());
$obMPDF->setDataFinal("31/12/".Sessao::getExercicio());
$obMPDF->setFormatoFolha("A4-L");

$obMPDF->setNomeRelatorio("Anexo DCA I-D");

$obMPDF->setConteudo($arDados);

$obMPDF->gerarRelatorio();
?>