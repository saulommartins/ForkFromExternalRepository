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
Arquivo de geracao do arquivo despesaTotalPessoalPE TCM/MG
    * Data de Criação   : 22/01/2009

    * @author Analista      Tonismar Régis Bernardo
    * @author Desenvolvedor Lucas Andrades Mendes

    * @package URBEM
    * @subpackage

    * @ignore

    $Id: despesaTotalPessoalPE.inc.php 63722 2015-10-01 21:07:31Z arthur $
    */

    include_once( CAM_GPC_TCEMG_MAPEAMENTO.Sessao::getExercicio().'/'.'FTCEMGDespesaTotalPessoalPE.class.php');
    include_once(CAM_FW_HTML."Bimestre.class.php");

    $arFiltros = Sessao::read('filtroRelatorio');

    foreach ($arDatasInicialFinal as $stDatas) {
        $obFTCEMGDespesaTotalPessoalPE = new FTCEMGDespesaTotalPessoalPE();
        $obFTCEMGDespesaTotalPessoalPE->setDado('exercicio'   , Sessao::read('exercicio'));
        $obFTCEMGDespesaTotalPessoalPE->setDado('cod_entidade', implode(',',$arFiltros['inCodEntidadeSelecionado']));
        $obFTCEMGDespesaTotalPessoalPE->setDado('mes', (INTEGER)(SUBSTR($stDatas['stDtInicial'],4,2)));
        $obFTCEMGDespesaTotalPessoalPE->setDado('data_inicial', $stDatas['stDtInicial']);
        $obFTCEMGDespesaTotalPessoalPE->setDado('data_final'  , $stDatas['stDtFinal']);
        $obFTCEMGDespesaTotalPessoalPE->recuperaTodos($rsDespesaTotalPessoal);
    
        $obExportador->roUltimoArquivo->addBloco($rsDespesaTotalPessoal);
            
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('mes');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('venc_vantagens');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('inativos');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('pensionistas');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('salario_familia');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('sub_prefeito');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('sub_vicepref');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('sub_secretarios');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('obrig_patronais');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('repasse_patronal');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('sent_jud_pessoal');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('indenizacao_demissao');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('inc_demissao_volunt');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('sent_judiciais_ant');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('inat_pens_cust_proprio');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('outras_despesas_pessoal');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('nada_declarar_pessoal');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('desp_exercicios_ant');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('exclusao_desp_ant');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    }

?>