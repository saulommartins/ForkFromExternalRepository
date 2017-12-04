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
  * Página de Processamento dos relatórios.
  * Data de Criação: 07/01/2014

  * @author Analista:      Eduardo Paculski Schitz
  * @author Desenvolvedor: Franver Sarmento de Moraes
  *
  * @ignore
  * $Id: PRExportarArquivosPessoal.php 60426 2014-10-21 11:54:26Z gelson $
  * $Date: 2014-10-21 09:54:26 -0200 (Tue, 21 Oct 2014) $
  * $Author: gelson $
  * $Rev: 60426 $
  *
*/
set_time_limit(0);

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GPC_TCEPE_MAPEAMENTO.'TTCEPEConfiguracaoUnidadeGestora.class.php';
include_once CLA_EXPORTADOR;

//Define o nome dos arquivos PHP
$stPrograma = "ExportarArquivosPessoal";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

$arFiltros = Sessao::read('filtroRelatorio');

$inCodEntidade   = $arFiltros["inCodEntidade"];
$inCodCompetencia = $arFiltros["inCodCompetencia"];

if($inCodCompetencia != 13) {
    $inTmsInicial  = mktime(0,0,0,$inCodCompetencia,01,Sessao::getExercicio());
    $stDataInicial = date  ('d/m/Y',$inTmsInicial);
    $inTmsFinal    = mktime(0,0,0,$inCodCompetencia+1,01,Sessao::getExercicio()) - 1;
    $stDataFinal   = date  ('d/m/Y',$inTmsFinal);
} else {
    $stDataInicial = '01/01/'.Sessao::getExercicio()-1;
    $stDataFinal   = '31/12/'.Sessao::getExercicio()-1;
}

//Recupera Unidade Gestora
$obTTCEPEConfiguracaoUnidadeGestora = new TTCEPEConfiguracaoUnidadeGestora;
$stFiltro  = " WHERE cod_modulo = 63 ";
$stFiltro .= " AND parametro = 'tcepe_codigo_unidade_gestora' ";
$stFiltro .= " AND exercicio = '".Sessao::getExercicio()."' ";
$stFiltro .= " AND cod_entidade = ".$inCodEntidade." ";
$obTTCEPEConfiguracaoUnidadeGestora->recuperaTodos($rsUnidadeGestora, $stFiltro);

while (!$rsUnidadeGestora->eof()) {
    $stUnidadeGestora = $rsUnidadeGestora->getCampo("valor");
    $rsUnidadeGestora->proximo();
}

$stUnidadeGestora = ($stUnidadeGestora!='') ? $stUnidadeGestora : "000000";

$obExportador = new Exportador();
$stTipoDocumento = "TCE_PE";

foreach ($arFiltros['arArquivos'] as $stArquivo) {

   $obExportador->addArquivo($stUnidadeGestora.str_pad($inCodCompetencia,2,'0', STR_PAD_LEFT).Sessao::getExercicio().$stArquivo.".txt");
   $obExportador->roUltimoArquivo->setTipoDocumento($stTipoDocumento);
   
   $stNomeArquivo = $stArquivo;
   include(CAM_GPC_TCEPE_INSTANCIAS."layoutArquivos/pessoal/".$stArquivo.".inc.php");


}

if ($arFiltros['stTipoExport'] == 'compactados') {
    $obExportador->setNomeArquivoZip('Pessoal'.$stUnidadeGestora.str_pad($inCodCompetencia,2,'0', STR_PAD_LEFT).Sessao::getExercicio().'.zip');
}

$obExportador->show();

?>