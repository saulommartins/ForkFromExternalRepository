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
    * PÃ¡gina Oculta - ExportaÃ§Ã£o Arquivos

    * Data de CriaÃ§Ã£o   : 03/03/2011

    * @author: Tonismar RÃ©gis Bernardo

    * @ignore
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GPC_TCEAM_MAPEAMENTO.'TTCEAMConfiguracaoEntidade.class.php';
include_once CLA_EXPORTADOR;

//Define o nome dos arquivos PHP
$stPrograma = 'Exportacao';
$pgFilt     = 'FL'.$stPrograma.'.php';
$pgList     = 'LS'.$stPrograma.'.php';
$pgForm     = 'FM'.$stPrograma.'.php';
$pgProc     = 'PR'.$stPrograma.'.php';
$pgOcul     = 'OC'.$stPrograma.'.php';
$pgJS       = 'JS'.$stPrograma.'.js';

SistemaLegado::BloqueiaFrames();

$stAcao             = $_REQUEST['stAcao'];
$arFiltro           = Sessao::read('filtroRelatorio');
$arUnidadesGestoras = array();

$inTmsInicial  = mktime(0,0,0,$inMes,01,Sessao::getExercicio());
$stDataInicial = date  ('d/m/Y',$inTmsInicial);
$inTmsFinal    = mktime(0,0,0,$inMes+1,01,Sessao::getExercicio()) - 1;
$stDataFinal   = date  ('d/m/Y',$inTmsFinal);

$stEntidades = implode(',', $arFiltro['inCodEntidade']);

if ($arFiltro['stAcao'] == 'informes') {
    $inMes = $arFiltro['inMes'];
}

//verifica se deve realizar a incorporação dos empenhos das entidades que não são prefeitura
$inCodEntidadePrefeitura = SistemaLegado::pegaConfiguracao('cod_entidade_prefeitura', 8, Sessao::getExercicio());
$boIncorporarEmpenhos = ( array_search( 'Empenho.txt', $arFiltro['arArquivosSelecionados'] ) !== FALSE || array_search( 'Liquida.txt', $arFiltro['arArquivosSelecionados'] ) !== FALSE || array_search( 'Pagamto.txt', $arFiltro['arArquivosSelecionados'] ) !== FALSE )  && array_search( $inCodEntidadePrefeitura, $arFiltro['inCodEntidade'] ) !== FALSE && $inMes == 12 ;

$stTipoDocumento = 'TCE_AM';
$obExportador    = new Exportador();

$obTConfiguracao = new TTCEAMConfiguracaoEntidade();
$obTConfiguracao->setDado('parametro','tceam_codigo_unidade_gestora');
foreach ($arFiltro['inCodEntidade'] as $inCodEntidade) {
    $obTConfiguracao->setDado('cod_entidade', $inCodEntidade);
    $obTConfiguracao->consultar();

    if ( trim($arUnidadesGestoras[$obTConfiguracao->getDado('valor')])) {
        $arUnidadesGestoras[$obTConfiguracao->getDado('valor')] .= ',';
    }
    $arUnidadesGestoras[$obTConfiguracao->getDado('valor')] .= $inCodEntidade;
}

//incorporando empenhos de outras entidades
if ($boIncorporarEmpenhos) {
    include_once(CAM_GPC_TCEAM_NEGOCIO."RTCEAMEmpenhoIncorporacao.class.php");

    $stCodEntidadesIncorporadas = '';
    foreach ($arFiltro['inCodEntidade'] as $inCodEntidade) {
        if ($inCodEntidade != $inCodEntidadePrefeitura) {
            $stCodEntidadesIncorporadas .= ','.$inCodEntidade;
        }
    }

    $stCodEntidadesIncorporadas = substr($stCodEntidadesIncorporadas, 1);

    $obRTCEAMEmpenhoIncorporacao = new RTCEAMEmpenhoIncorporacao();
    $obRTCEAMEmpenhoIncorporacao->incorporarEmpenhos($stCodEntidadesIncorporadas);

}

foreach ($arFiltro['arArquivosSelecionados'] as $stArquivo) {
    $obExportador->addArquivo($stArquivo);
    $obExportador->roUltimoArquivo->setTipoDocumento($stTipoDocumento);
    include substr($stArquivo,0,strpos($stArquivo,'.txt')).'.inc.php';
    unset($obTMapeamento, $rsRecordSet,$stEntidade);
    $arRecordSet = null;
}

//deletando emnpenhos incorporados
if ($boIncorporarEmpenhos) {
    $obRTCEAMEmpenhoIncorporacao->deletarIncorporacao();
}

if ($arFiltro['stTipoExport'] == 'compactados') {
    if ($arFiltro['stAcao'] == 'orcamento') {
        $obExportador->setNomeArquivoZip('ExportacaoArquivosOrcamento.zip');
    }
}

$obExportador->show();
SistemaLegado::LiberaFrames();
?>
