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
   /*
    * Arquivo de geracao do arquivo DespesaIntra TCM/MG
    * Data de Criação   : 19/01/2009

    * @author Analista      Tonismar Régis Bernardo
    * @author Desenvolvedor André Machado

    * @package URBEM
    * @subpackage

    * @ignore

    $Id: DespesaIntra.inc.php 62748 2015-06-16 14:07:21Z michel $
    */

    include_once( CAM_GPC_TCEMG_MAPEAMENTO.Sessao::getExercicio().'/TTCEMGDespesaIntra.class.php');

    $arFiltros   = Sessao::read('filtroRelatorio');
    Sistemalegado::retornaInicialFinalMesesPeriodicidade($arDtInicialFinal,'anual',$arFiltros['inPeriodo'],Sessao::getExercicio());
    
    $obTTCEMGDespesaIntra = new TTCEMGDespesaIntra();
    foreach ($arDtInicialFinal as $stDatas) {
        $obTTCEMGDespesaIntra->setDado('exercicio'   , Sessao::getExercicio()                               );
        $obTTCEMGDespesaIntra->setDado('cod_entidade', implode(',',$arFiltros['inCodEntidadeSelecionado'])  );
        $obTTCEMGDespesaIntra->setDado('bimestre'    , '6'                                                  );
        $obTTCEMGDespesaIntra->setDado('dataInicial' , $stDatas['stDtInicial']                              );
        $obTTCEMGDespesaIntra->setDado('dataFinal'   , $stDatas['stDtFinal']                                );   
    
        $obTTCEMGDespesaIntra->recuperaDadosArquivo($rsDespesaIntra);
        
        $obExportador->roUltimoArquivo->addBloco($rsDespesaIntra);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('bimestre');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('NUMERICO_ZEROS_ESQ');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('demais_despesas_intra');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(16);
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('cod_tipo');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('NUMERICO_ZEROS_ESQ');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('juros_encargos_divida');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(16);
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    }
 
?>