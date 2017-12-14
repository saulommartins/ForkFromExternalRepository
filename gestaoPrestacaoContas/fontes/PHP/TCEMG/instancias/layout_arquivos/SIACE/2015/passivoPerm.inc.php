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

    * Arquivo de geracao do arquivo sertTerceiros TCM/MG
    * Data de Criação   : 19/01/2009

    * @author Analista      Tonismar Régis Bernardo
    * @author Desenvolvedor André Machado

    * @package URBEM
    * @subpackage

    * @ignore

    $Id: $
*/

    include_once( CAM_GPC_TCEMG_MAPEAMENTO.Sessao::getExercicio().'/FTCEMGPassivoPerm.class.php');

    $arFiltros = Sessao::read('filtroRelatorio');

    SistemaLegado::retornaInicialFinalMesesPeriodicidade( $arDatas,"ano",$arFiltros['inPeriodo'],Sessao::getExercicio() );
    
    $obFTCEMGAtivoPerm = new FTCEMGPassivoPerm();

    foreach ($arDatas as $stDatas) {

        $obFTCEMGAtivoPerm->setDado('exercicio'    , Sessao::getExercicio() );
        $obFTCEMGAtivoPerm->setDado('cod_entidade' , implode(',',$arFiltros['inCodEntidadeSelecionado']) );
        $obFTCEMGAtivoPerm->setDado('data_inicial' , $stDatas['stDtInicial'] );
        $obFTCEMGAtivoPerm->setDado('data_final'   , $stDatas['stDtFinal']   );
    
        $rsTemp = new RecordSet;
        $obFTCEMGAtivoPerm->recuperaTodos($rsTemp);

        $obExportador->roUltimoArquivo->addBloco($rsTemp);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('mes');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('valoremp');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('valortransconcedidas');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('valorprovisaorpps');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('codtipo');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

    }

    unset($arDatas);
    unset($arFiltros);
    unset($obFTCEMGAtivoPerm);

?>