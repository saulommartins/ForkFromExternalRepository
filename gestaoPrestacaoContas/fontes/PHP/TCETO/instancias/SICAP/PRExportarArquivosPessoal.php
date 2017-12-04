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
  * Página de Processamento para geração de arquivos TXT
  * Data de Criação: 07/01/2014

  * @author Analista:      Eduardo Paculski Schitz
  * @author Desenvolvedor: Franver Sarmento de Moraes
  *
  * @ignore
  * $Id: PRExportarArquivosPessoal.php 60674 2014-11-07 15:51:39Z franver $
  * $Date: 2014-11-07 13:51:39 -0200 (Fri, 07 Nov 2014) $
  * $Author: franver $
  * $Rev: 60674 $
  *
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CLA_EXPORTADOR;

$stPrograma = "ExportarArquivosPessoal";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

$arFiltros = Sessao::read('filtroRelatorio');

$inMes  = $arFiltros['inMes'];
$inExercicio = Sessao::getExercicio();

$stDataInicial = "01/".$inMes."/".$inExercicio;
$stDataFinal   = SistemaLegado::retornaUltimoDiaMes($inMes, $inExercicio);

$obExportador = new Exportador();
$stTipoDocumento = "TCE_TO";

foreach ($arFiltros['arArquivos'] as $stArquivo) {
    $obExportador->addArquivo($stArquivo."_".$inMes."_".$inExercicio.".txt");
    $obExportador->roUltimoArquivo->setTipoDocumento($stTipoDocumento);
    include(CAM_GPC_TCETO_INSTANCIAS."layoutArquivos/pessoal/".$stArquivo.".inc.php");
}

$obExportador->show();

?>