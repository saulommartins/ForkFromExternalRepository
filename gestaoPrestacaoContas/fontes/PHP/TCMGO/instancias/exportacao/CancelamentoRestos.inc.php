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
    * Página de Include Oculta - Exportação Arquivos GF

    * Data de Criação   : 16/02/2007

    * @author Analista: Gelson
    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @ignore

    $Id: CancelamentoRestos.inc.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-06.04.00
*/

    $arFiltroRelatorio = Sessao::read('filtroRelatorio');
    $inMes = $arFiltroRelatorio['inMes'];

    /* SETA O FILTRO DO MES DE ACORDO COM O MES SELECIONADO */
    switch ($inMes) {
        case 1:
            $stFiltro = "and to_date( empenho_anulado.timestamp , 'yyyy-mm-dd' ) between '".Sessao::getExercicio()."-".$inMes."-01' and '".Sessao::getExercicio()."-".$inMes."-31' ";
        break;
        case 2:
            /* Comentado pois não teve como saber da onde e qual o valor recebido por essa propriedade da sessao */
            //$dia = ( sessao->exerciciempenho_anulado.o % 4 == 0 ) ? 29 : 28 ;
            $stFiltro = "and to_date( empenho_anulado.timestamp , 'yyyy-mm-dd' ) between '".Sessao::getExercicio()."-".$inMes."-01' and '".Sessao::getExercicio()."-".$inMes."-".$dia."' ";
        break;
        case 3:
            $stFiltro = "and to_date( empenho_anulado.timestamp , 'yyyy-mm-dd' ) between '".Sessao::getExercicio()."-".$inMes."-01' and '".Sessao::getExercicio()."-".$inMes."-31' ";
        break;
        case 4:
            $stFiltro = "and to_date( empenho_anulado.timestamp , 'yyyy-mm-dd' ) between '".Sessao::getExercicio()."-".$inMes."-01' and '".Sessao::getExercicio()."-".$inMes."-30' ";
        break;
        case 5:
            $stFiltro = "and to_date( empenho_anulado.timestamp , 'yyyy-mm-dd' ) between '".Sessao::getExercicio()."-".$inMes."-01' and '".Sessao::getExercicio()."-".$inMes."-31' ";
        break;
        case 6:
            $stFiltro = "and to_date( empenho_anulado.timestamp , 'yyyy-mm-dd' ) between '".Sessao::getExercicio()."-".$inMes."-01' and '".Sessao::getExercicio()."-".$inMes."-30' ";
        break;
        case 7:
            $stFiltro = "and to_date( empenho_anulado.timestamp , 'yyyy-mm-dd' ) between '".Sessao::getExercicio()."-".$inMes."-01' and '".Sessao::getExercicio()."-".$inMes."-31' ";
        break;
        case 8:
            $stFiltro = "and to_date( empenho_anulado.timestamp , 'yyyy-mm-dd' ) between '".Sessao::getExercicio()."-".$inMes."-01' and '".Sessao::getExercicio()."-".$inMes."-31' ";
        break;
        case 9:
            $stFiltro = "and to_date( empenho_anulado.timestamp , 'yyyy-mm-dd' ) between '".Sessao::getExercicio()."-".$inMes."-01' and '".Sessao::getExercicio()."-".$inMes."-30' ";
        break;
        case 10:
            $stFiltro = "and to_date( empenho_anulado.timestamp , 'yyyy-mm-dd' ) between '".Sessao::getExercicio()."-".$inMes."-01' and '".Sessao::getExercicio()."-".$inMes."-31' ";
        break;
        case 11:
            $stFiltro = "and to_date( empenho_anulado.timestamp , 'yyyy-mm-dd' ) between '".Sessao::getExercicio()."-".$inMes."-01' and '".Sessao::getExercicio()."-".$inMes."-30' ";
        break;
        case 12:
            $stFiltro = "and to_date( empenho_anulado.timestamp , 'yyyy-mm-dd' ) between '".Sessao::getExercicio()."-".$inMes."-01' and '".Sessao::getExercicio()."-".$inMes."-31' ";
        break;
    }

    $inCodEntidade = implode(',',$arFiltroRelatorio['inCodEntidade']);

    include_once( CAM_GPC_TPB_MAPEAMENTO."TTPBCancelamentoRestos.class.php" );
    $obTMapeamento = new TTPBCancelamentoRestos();

    $obTMapeamento->setDado( 'cod_entidade', $inCodEntidade );
    $obTMapeamento->recuperaNumeroEstorno( $arRecordSet[$stArquivo], $stFiltro );

    $obExportador->roUltimoArquivo->addBloco($arRecordSet[$stArquivo]);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("reservado_tse");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("ano_empenho_estornado");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
