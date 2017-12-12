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
    * Página Oculta - Exportação Arquivos GF

    * Data de Criação   : 18/01/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @ignore

    $Id: OCExportacaoBalancete.php 65481 2016-05-25 13:12:58Z michel $

    * Casos de uso: uc-06.04.00
*/

set_time_limit(0);

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GPC_TGO_MAPEAMENTO."TTGOConfiguracaoEntidade.class.php";
include_once CLA_EXPORTADOR;

SistemaLegado::BloqueiaFrames();

$stAcao = $request->get('stAcao');

$arFiltroRelatorio = Sessao::read('filtroRelatorio');

$inMes = $arFiltroRelatorio['inMes'];

$arFiltroRelatorio['stDataInicial'] = '01/'.$inMes.'/'.Sessao::getExercicio();
$arFiltroRelatorio['stDataFinal']   = SistemaLegado::retornaUltimoDiaMes($inMes, Sessao::getExercicio());

$stDataInicial = $arFiltroRelatorio['stDataInicial'];
$stDataFinal   = $arFiltroRelatorio['stDataFinal'];

Sessao::write('filtroRelatorio', $arFiltroRelatorio);

$stEntidades    = implode(",",$arFiltroRelatorio['inCodEntidade']);
$arUnidadesGestoras = array();

$obTConfiguracao = new TTGOConfiguracaoEntidade();
$obTConfiguracao->setDado('parametro','tc_codigo_unidade_gestora');

foreach ($arFiltroRelatorio['inCodEntidade'] as $inCodEntidade) {
    $obTConfiguracao->setDado('cod_entidade', $inCodEntidade );
    $obTConfiguracao->consultar();
    if ( trim($arUnidadesGestoras[ $obTConfiguracao->getDado('valor') ]) ) {
        $arUnidadesGestoras[ $obTConfiguracao->getDado('valor') ] .= ',';
    }
    $arUnidadesGestoras[ $obTConfiguracao->getDado('valor') ] .= $inCodEntidade;
}

$stTipoDocumento = "TCM_GO";
$stTipoExportacao = "Balancete";

$obExportador    = new Exportador();

foreach ($arFiltroRelatorio["arArquivosSelecionados"] as $stArquivo) {
    $arArquivo = explode( '.',$stArquivo );
    $arArquivo[0] = strtoupper($arArquivo[0]);

    if ($stArquivo == 'Ide.txt' OR $stArquivo == 'Orgao.txt') {
        $obExportador->addArquivo($arArquivo[0].'.'.$arArquivo[1]);
    } elseif ($stArquivo == 'CON.txt') {
        $obExportador->addArquivo('CON'.$inMes.substr(Sessao::getExercicio(),2,2).'.txt');
    } else {
        $obExportador->addArquivo($arArquivo[0].$inMes.substr(Sessao::getExercicio(),2,2).'.'.$arArquivo[1]);
    }

    $obExportador->roUltimoArquivo->setTipoDocumento($stTipoDocumento);

    if ($stArquivo == 'CON.txt')
        $stArquivo = 'CONArq.txt';

    if(Sessao::getExercicio() >= 2016) {
        include (CAM_GPC_TGO_INSTANCIAS."layout_arquivos/balancete/".Sessao::getExercicio()."/".substr($stArquivo,0,strpos($stArquivo,'.txt')).".inc.php");
    }else {
        include( substr($stArquivo,0,strpos($stArquivo,'.txt')) . ".inc.php");
    }

    $arRecordSet = null;
}

if ($arFiltroRelatorio['stTipoExport'] == 'compactados') {
    $obExportador->setNomeArquivoZip($stTipoExportacao.Sessao::getExercicio().$inMes.'.zip');
}

$obExportador->show();
SistemaLegado::LiberaFrames();
