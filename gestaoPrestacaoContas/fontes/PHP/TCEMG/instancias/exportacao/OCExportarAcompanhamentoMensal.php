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
 * Arquivo oculto - Exportação arquivos Planejamento TCE/MG
 *
 * @category    Urbem
 * @package     TCE/MG
 * @author      Eduardo Schitz   <eduardo.schitz@cnm.org.br>
 * $Id: OCExportarAcompanhamentoMensal.php 65926 2016-06-30 14:19:47Z franver $
 */

set_time_limit(0);
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_EXPORTADOR;
include_once CAM_GPC_TCEMG_NEGOCIO.'RTCEMGExportarAcompanhamentoMensal.class.php';
include_once CAM_GPC_TCEMG_MAPEAMENTO.'TTCEMGConfigurarIDE.class.php';


SistemaLegado::BloqueiaFrames();

$stAcao = $request->get('stAcao');
$arFiltro = Sessao::read('filtroRelatorio');

Sessao::write('exp_arFiltro',$arFiltro);

//Recebe as entidades selecionadas no filtro e concatena elas separando por ','
$stEntidades    = implode(",",$arFiltro['arEntidadesSelecionadas']);
$stDataFinal= SistemaLegado::retornaUltimoDiaMes($arFiltro['stMes'],Sessao::getExercicio() );
if ($arFiltro['stMes'] < 10) {
   $arFiltro['stMes']=  str_pad( $arFiltro['stMes'], 2, '0', STR_PAD_LEFT );
}
$stDataInicial = '01/'.$arFiltro['stMes'].'/'.Sessao::getExercicio();

$stMes = $arFiltro['stMes'];

$obRTCEMGExportarAcompanhamentoMensal = new RTCEMGExportarAcompanhamentoMensal;
$obRTCEMGExportarAcompanhamentoMensal->setArquivos    ($arFiltro["arArquivosSelecionados"]);
$obRTCEMGExportarAcompanhamentoMensal->setExercicio   (Sessao::getExercicio());
$obRTCEMGExportarAcompanhamentoMensal->setMes         ($arFiltro['stMes']);
$obRTCEMGExportarAcompanhamentoMensal->setCodEntidades($stEntidades);
$obRTCEMGExportarAcompanhamentoMensal->setDataInicial ($stDataInicial);
$obRTCEMGExportarAcompanhamentoMensal->setDataFinal   ($stDataFinal);
$obRTCEMGExportarAcompanhamentoMensal->geraRecordset  ($arRecordSetArquivos);

$obExportador = new Exportador();

foreach ($arFiltro['arArquivosSelecionados'] as $stArquivo) {
   $boAddArquivo = TRUE;
   
   if($boAddArquivo){
      $obExportador->addArquivo($stArquivo);
      $stNomeArquivo = trim($stArquivo, '.csv');

      include_once(CAM_GPC_TCEMG_INSTANCIAS."layout_arquivos/acompanhamentoMesal/".Sessao::getExercicio()."/".$stArquivo.".inc.php");
   }
}


if ( $arFiltro['stTipoExport'] == 'compactados'){
    $obTTCEMGConfigurarIDE = new TTCEMGConfigurarIDE;
    $obTTCEMGConfigurarIDE->setDado('exercicio', Sessao::getExercicio());
    $obTTCEMGConfigurarIDE->setDado('entidades', $stEntidades);
    $obTTCEMGConfigurarIDE->recuperaDadosExportacao($rsRecordSet);
    
    if ($rsRecordSet->inNumLinhas > 0) {
        $inCodMunicipio = str_pad($rsRecordSet->getCampo('codmunicipio'), 5, '0', STR_PAD_LEFT);
        $inCodOrgao = str_pad($rsRecordSet->getCampo('codorgao'), 2, '0', STR_PAD_LEFT);
        $inMes = str_pad($request->get('stMes'), 2, '0', STR_PAD_LEFT);
    } else {
        SistemaLegado::alertaAviso("FLExportarArquivosPlanejamento.php?".Sessao::getId()."&stAcao=$stAcao", "É necessário configurar a IDE para gerar um arquivo compactado.", "", "aviso", Sessao::getId(), "../");
        die;
    }
    
    $obExportador->setNomeArquivoZip('AM_'.$inCodMunicipio.'_'.$inCodOrgao.'_'.$inMes.'_'.Sessao::getExercicio().'.zip');
}

$obExportador->show();
SistemaLegado::LiberaFrames();

?>