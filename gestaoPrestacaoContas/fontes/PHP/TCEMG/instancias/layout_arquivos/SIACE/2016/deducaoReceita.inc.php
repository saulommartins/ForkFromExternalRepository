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
    * Arquivo de geracao do arquivo deducaoReceita TCM/MG
    * Data de Criação   : 07/10/2013
    * @author Analista      Silvia
    * @author Desenvolvedor Evandro Melos
    * 
    * $Id: deducaoReceita.inc.php 63441 2015-08-28 13:32:42Z lisiane $
    * $Date: 2015-08-28 10:32:42 -0300 (Sex, 28 Ago 2015) $
    * $Rev: 63441 $
    * $Author: lisiane $
    * 
*/
include_once CAM_GPC_TCEMG_MAPEAMENTO.Sessao::getExercicio().'/FTCEMGDeducaoReceita.class.php';

$arFiltros = Sessao::read('filtroRelatorio');

foreach ($arDatasInicialFinal as $stDatas) {
    $obFTCEMGDeducaoReceita = new FTCEMGDeducaoReceita();
    $obFTCEMGDeducaoReceita->setDado('exercicio'    , Sessao::getExercicio() );
    $obFTCEMGDeducaoReceita->setDado('cod_entidade' , implode(',',$arFiltros['inCodEntidadeSelecionado']));
    $obFTCEMGDeducaoReceita->setDado('dt_inicial'   , $stDatas['stDtInicial'] );
    $obFTCEMGDeducaoReceita->setDado('dt_final'     , $stDatas['stDtFinal'] );
    $obFTCEMGDeducaoReceita->recuperaTodos($rsDeducoesReceita); 

    $obExportador->roUltimoArquivo->addBloco($rsDeducoesReceita);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("mes");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('NUMERICO_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(02);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_tipo");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('NUMERICO_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(02);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("valor");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(16);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
}

unset($obFTCEMGDeducaoReceita);
unset($rsDeducoesReceita);
?>
