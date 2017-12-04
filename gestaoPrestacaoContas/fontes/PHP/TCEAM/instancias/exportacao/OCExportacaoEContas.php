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
    * Página de Filtro - Exportação Arquivos EContas

    * Data de Criação   : 21/05/2014

    * @author Analista: Valtair Santos
    * @author Desenvolvedor: Michel Teixeira

    * @ignore

    $Id: OCExportacaoEContas.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_EXPORTADOR;

//Define o nome dos arquivos PHP
$stPrograma = 'ExportacaoEContas';
$pgFilt     = 'FL'.$stPrograma.'.php';
$pgList     = 'LS'.$stPrograma.'.php';
$pgForm     = 'FM'.$stPrograma.'.php';
$pgProc     = 'PR'.$stPrograma.'.php';
$pgOcul     = 'OC'.$stPrograma.'.php';
$pgJS       = 'JS'.$stPrograma.'.js';

SistemaLegado::BloqueiaFrames();

$stAcao             = $_REQUEST['stAcao'];
$arFiltro           = Sessao::read('filtroRelatorio');

$stEntidades = implode(',', $arFiltro['inCodEntidade']);

$inMes=  str_pad( $arFiltro['inMes'], 2, '0', STR_PAD_LEFT );

$stDataFinal= SistemaLegado::retornaUltimoDiaMes($inMes,Sessao::getExercicio() );
$stDataInicial = '01/'.$inMes.'/'.Sessao::getExercicio();

$stTipoDocumento = 'TCE_AM';
$obExportador    = new Exportador();

foreach ($arFiltro['arArquivosSelecionados'] as $stArquivo) {
    $obExportador->addArquivo($stArquivo);
    $obExportador->roUltimoArquivo->setTipoDocumento($stTipoDocumento);
    include substr($stArquivo,0,strpos($stArquivo,'.txt')).'.php';
    unset($obTMapeamento, $rsRecordSet,$stEntidade);
    $arRecordSet = null;
}

if ($arFiltro['stTipoExport'] == 'compactados') {
        $obExportador->setNomeArquivoZip('ExportacaoArquivosEContas.zip');
}

$obExportador->show();
SistemaLegado::LiberaFrames();
?>
