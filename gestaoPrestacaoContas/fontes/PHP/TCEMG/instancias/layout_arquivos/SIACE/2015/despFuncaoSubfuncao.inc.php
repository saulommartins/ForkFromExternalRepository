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
* Arquivo de geracao do arquivo sertTerceiros TCM/MG
* Data de Criação   : 20/01/2009

* @author Analista      Tonismar Régis Bernardo
* @author Desenvolvedor Eduardo Paculski Schitz

* @package URBEM
* @subpackage

* @ignore

$Id: despFuncaoSubfuncao.inc.php 63314 2015-08-17 13:48:57Z franver $
*/
include_once CAM_GPC_TCEMG_MAPEAMENTO.Sessao::getExercicio().'/FTCEMGDespFuncaoSubfuncao.class.php';

$arFiltros = Sessao::read('filtroRelatorio');

$rsDespFuncaoSubFuncao = new RecordSet();
$obFTCEMGDespFuncaoSubfuncao = new FTCEMGDespFuncaoSubfuncao();
foreach ($arDatasInicialFinal as $stDatas) {
    $obFTCEMGDespFuncaoSubfuncao->setDado('exercicio'   , Sessao::read('exercicio'));
    $obFTCEMGDespFuncaoSubfuncao->setDado('cod_entidade', implode(',',$arFiltros['inCodEntidadeSelecionado']));    
    $obFTCEMGDespFuncaoSubfuncao->setDado('data_inicial', $stDatas['stDtInicial'] );
    $obFTCEMGDespFuncaoSubfuncao->setDado('data_final'  , $stDatas['stDtFinal'] );    
    $obFTCEMGDespFuncaoSubfuncao->recuperaTodos($rsDespFuncaoSubFuncao);

    $obExportador->roUltimoArquivo->addBloco($rsDespFuncaoSubFuncao);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('periodo');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(2);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('cod_vinculo');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(2);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('vl_inicial');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(16);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('vl_atualizada');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(16);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('vl_empenhado');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(16);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('vl_liquidado');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(16);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('vl_anulada');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(16);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('cod_funcao');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(2);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('cod_subfuncao');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(3);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('cod_entidade_relacionada');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(33);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
}

unset($rsDespFuncaoSubFuncao);
unset($obFTCEMGDespFuncaoSubfuncao);
?>
