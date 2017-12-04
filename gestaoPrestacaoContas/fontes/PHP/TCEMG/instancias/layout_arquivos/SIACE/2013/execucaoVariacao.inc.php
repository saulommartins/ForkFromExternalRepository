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
    * Arquivo de geracao do arquivo execucaoVariacao
    * Data de Criação   : 20/01/2009

    * @author Analista      Tonismar Régis Bernardo
    * @author Desenvolvedor Alexandre Melo

    * @package URBEM
    * @subpackage

    * @ignore

    $Id:$
    */

    include_once( CAM_GPC_TCEMG_MAPEAMENTO.Sessao::getExercicio().'/TTCEMGExecucaoVariacao.class.php');

    $arFiltros = Sessao::read('filtroRelatorio');

    $obTTCEMGConsideracaoExecucaoVariacao = new TTCEMGExecucaoVariacao();
    $obTTCEMGConsideracaoExecucaoVariacao->setDado('exercicio'   , Sessao::getExercicio());
    
    switch ($arFiltros['inPeriodo']) {
        case 1: $obTTCEMGConsideracaoExecucaoVariacao->setDado('cod_mes', "1,2");
               break;
        case 2: $obTTCEMGConsideracaoExecucaoVariacao->setDado('cod_mes', "3,4");
           break;
        case 3: $obTTCEMGConsideracaoExecucaoVariacao->setDado('cod_mes', "5,6");
               break;
        case 4: $obTTCEMGConsideracaoExecucaoVariacao->setDado('cod_mes', "7,8");
           break;
        case 5: $obTTCEMGConsideracaoExecucaoVariacao->setDado('cod_mes', "9,10");
               break;
        case 6: $obTTCEMGConsideracaoExecucaoVariacao->setDado('cod_mes', "11,12");
               break;
    }
    
    $obTTCEMGConsideracaoExecucaoVariacao->recuperaDadosBimestre($rsArquivo);
    
    $obExportador->roUltimoArquivo->addBloco($rsArquivo);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('mes');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('cons_adm_dir');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('CARACTER_ESPACOS_DIR');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4000);
            
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('cons_aut');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('CARACTER_ESPACOS_DIR');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4000);
        
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('cons_fund');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('CARACTER_ESPACOS_DIR');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4000);
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('cons_empe_est_dep');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('CARACTER_ESPACOS_DIR');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4000);
        
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('cons_dem_ent');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('CARACTER_ESPACOS_DIR');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4000);
    
?>