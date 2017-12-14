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
  * $Id: OCGeraRelatorioSiconfiAnexoDCAIG.php 62933 2015-07-09 14:18:16Z franver $
  * $Date: 2015-07-09 11:18:16 -0300 (Thu, 09 Jul 2015) $
  * $Author: franver $
  * $Rev: 62933 $
  *
*/
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GPC_SICONFI_MAPEAMENTO."FSICONFIRelatorioAnexoDCAIG.class.php";
include_once CLA_MPDF;

$rsRecordSet = new RecordSet();

$inEntidades = implode(',',$request->get('inCodEntidade'));

$stOrder = ' cod_funcao, cod_subfuncao ';

$obFSICONFIRelatorioAnexoDCAIG = new FSICONFIRelatorioAnexoDCAIG();
$obFSICONFIRelatorioAnexoDCAIG->setDado('exercicio'     , Sessao::getExercicio());
$obFSICONFIRelatorioAnexoDCAIG->setDado('stEntidades'   , $inEntidades);
$obFSICONFIRelatorioAnexoDCAIG->setDado('stDataFinal'   , '31/12/'.Sessao::getExercicio());
$obFSICONFIRelatorioAnexoDCAIG->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder );

$arDados['arDespesaFuncoes'] = $rsRecordSet->getElementos();

$obMPDF = new FrameWorkMPDF(6,66,7);
$obMPDF->setCodEntidades($inEntidades);
$obMPDF->setDataInicio("01/01/".Sessao::getExercicio());
$obMPDF->setDataFinal("31/12/".Sessao::getExercicio());
$obMPDF->setFormatoFolha("A4-L");
$obMPDF->setNomeRelatorio("Anexo DCA I-G");

$obMPDF->setConteudo($arDados);

$obMPDF->gerarRelatorio();
?>