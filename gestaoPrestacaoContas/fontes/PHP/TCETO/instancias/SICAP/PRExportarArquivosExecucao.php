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
  * Página de Processamento para geração de arquivos XML
  * Data de Criação: 07/01/2014

  * @author Analista:      Eduardo Paculski Schitz
  * @author Desenvolvedor: Franver Sarmento de Moraes
  *
  * @ignore
  * $Id: PRExportarArquivosExecucao.php 60692 2014-11-10 13:41:36Z evandro $
  * $Date: 2014-11-10 11:41:36 -0200 (Mon, 10 Nov 2014) $
  * $Author: evandro $
  * $Rev: 60692 $
  *
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GPC_TCEAL_NEGOCIO."RExportacaoExecucao.class.php";

$stPrograma = "ExportarArquivosExecucao";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

$inBimestre  = $_REQUEST['bimestre'];
$inExercicio = Sessao::getExercicio();
$inCodEntidade = $_REQUEST['inCodEntidade'];

SistemaLegado::periodoInicialFinalBimestre($stDataInicial, $stDataFinal, $inBimestre, $inExercicio);

$obRExportacaoExecucao = new RExportacaoExecucao;

foreach ($_REQUEST['arArquivos'] as $stArquivo) {
   
    $stVersao="1.0";
    
    include(CAM_GPC_TCETO_INSTANCIAS."layoutArquivos/execucao/".$stArquivo.".inc.php");
    $obRExportacaoExecucao->geraDocumentoXML($arResult, $stArquivo, $stVersao);

    unset($obTMapeamento, $stNomeArquivo, $idCount);
    $arResult = null;
}

if (count($_REQUEST['arArquivos']) > 1) {
    $obRExportacaoExecucao->doZipArquivos();
}

SistemaLegado::mudaFramePrincipal($pgList);

?>