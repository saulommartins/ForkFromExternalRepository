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

    * Arquivo de geracao do arquivo deducaoReceita TCM/MG
    * Data de Criação   : 07/10/2013
    * @author Analista      Silvia
    * @author Desenvolvedor Evandro Melos
*/

    include_once( CAM_GPC_TCEMG_MAPEAMENTO.Sessao::getExercicio().'/FTCEMGDeducaoReceita.class.php');

    $arFiltros = Sessao::read('filtroRelatorio');

    //Arquivo só é gerado no ultimo bimestre valor do "mes" fixo em 12 com data do inicio do exercicio e data final do exercicio
    $obFTCEMGDeducaoReceita = new FTCEMGDeducaoReceita();
    $obFTCEMGDeducaoReceita->setDado('exercicio'    , Sessao::read('exercicio')     );
    $obFTCEMGDeducaoReceita->setDado('cod_entidade' , implode(',',$arFiltros['inCodEntidadeSelecionado']));
    $obFTCEMGDeducaoReceita->setDado('dt_inicial'   , "01/01/".Sessao::getExercicio() );
    $obFTCEMGDeducaoReceita->setDado('dt_final'     , "31/12/".Sessao::getExercicio() );

    $obFTCEMGDeducaoReceita->recuperaTodos($rsDeducoesReceita);

    $obExportador->roUltimoArquivo->addBloco($rsDeducoesReceita);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("mes");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_tipo");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("valor");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('CARACTER_ESPACOS_DIR');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

?>
