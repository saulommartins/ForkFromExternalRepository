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
    * Página Oculta - Exportação Arquivos Principais

    * Data de Criação   : 10/02/2005

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Lucas Texeira Stephanou

    * @ignore

    $Id: OCExportacaoPrincipais.php 46941 2012-06-29 11:36:31Z tonismar $

    * Casos de uso: uc-02.08.01
*/

set_time_limit(0);

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_EXPORTADOR );
include_once( CAM_GPC_MANAD_NEGOCIO."RExportacaoMANAD.class.php" );
include_once( CAM_GPC_TCERS_MAPEAMENTO."TExportacaoTCERSConfiguracao.class.php" );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoConfiguracao.class.php"  );

SistemaLegado::BloqueiaFrames();

$stAcao = $request->get('stAcao');

$arFiltro = Sessao::read('filtroRelatorio');
$arFiltro = Sessao::write('exp_arFiltro',$arFiltro);

//Entidades
$stEntidades = implode(",",$arFiltro['arEntidadesSelecionadas']);

$obExportador = new Exportador();
$obExportador->addArquivo('MANAD.txt');
$obExportador->roUltimoArquivo->setTipoDocumento('MANAD');

if (isset($arFiltro['stAnoMes']) =='') {
    $arFiltro['stAnoMes'] = substr(str_replace("/", "", $arFiltro['stDataFinal']), 4);
}

$obRExportacaoMANAD = new RExportacaoMANAD;
$obRExportacaoMANAD->setCodEntidadesArray($arFiltro['arEntidadesSelecionadas']);
$obRExportacaoMANAD->setCodEntidades($stEntidades);
$obRExportacaoMANAD->setExercicio   ($arFiltro['stAnoMes']);
$obRExportacaoMANAD->setArquivos    ($arFiltro["arArquivosSelecionados"]);
$obRExportacaoMANAD->setDataInicial ($arFiltro['stDataInicial']);
$obRExportacaoMANAD->setDataFinal   ($arFiltro['stDataFinal']);
$obRExportacaoMANAD->geraBloco0     ($obExportador);
//$obRExportacaoMANAD->geraBlocoI     ($obExportador);
$obRExportacaoMANAD->geraBlocoK     ($obExportador);
$obRExportacaoMANAD->geraBlocoL     ($obExportador);
$obRExportacaoMANAD->geraBloco9     ($obExportador);

$obExportador->show();
SistemaLegado::LiberaFrames();

?>
