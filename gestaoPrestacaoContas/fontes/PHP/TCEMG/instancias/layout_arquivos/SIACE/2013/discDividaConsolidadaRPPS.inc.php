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

include_once( CAM_GPC_TCEMG_MAPEAMENTO.Sessao::getExercicio().'/TTCEMGDividaConsolidadaRPPS.class.php');

$arFiltros = Sessao::read('filtroRelatorio');

$stCodEntidadeRPPS = SistemaLegado::pegaConfiguracao("cod_entidade_rpps",8,Sessao::read('exercicio'),$boTransacao);

//VALIDAR O ARRAY PARA MANDAR PRA CONSULTA
if ( in_array($stCodEntidadeRPPS, $arFiltros['inCodEntidadeSelecionado']) ) {
    //Retira o valor da entidade RPPS do array de entidades
    $inCodEntidade = array_diff( $arFiltros['inCodEntidadeSelecionado'], array($stCodEntidadeRPPS) );
    $inCodEntidade = implode(",", $inCodEntidade);
}else{
    $inCodEntidade = implode(",", $arFiltros['inCodEntidadeSelecionado']);
}

//Arquivo é gerado só no ultimo Bimestre e equivale a todos o periodo do ano 01/01 a 31/12
$obTTCEMGDividaConsolidadaRPPS = new TTCEMGDividaConsolidadaRPPS();
$obTTCEMGDividaConsolidadaRPPS->setDado('exercicio'         , Sessao::read('exercicio'));
$obTTCEMGDividaConsolidadaRPPS->setDado('cod_entidade'      , $inCodEntidade);
$obTTCEMGDividaConsolidadaRPPS->setDado('cod_entidade_rpps' , $stCodEntidadeRPPS);

$obTTCEMGDividaConsolidadaRPPS->recuperaTodos($rsArquivo);

$obExportador->roUltimoArquivo->addBloco($rsArquivo);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('mes');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('NUMERICO_ZEROS_ESQ');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
$obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('div_contratual_demais');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('NUMERICO_ZEROS_ESQ');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
$obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('div_contratual_ppp');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('NUMERICO_ZEROS_ESQ');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
$obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('div_mobiliaria');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('NUMERICO_ZEROS_ESQ');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
$obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('op_credito_inf_12');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('NUMERICO_ZEROS_ESQ');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
$obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('outras');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('NUMERICO_ZEROS_ESQ');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
$obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('parc_contr_sociais_prev');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('NUMERICO_ZEROS_ESQ');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
$obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('parc_contr_sociais_demais');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('NUMERICO_ZEROS_ESQ');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
$obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('parc_tributos');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('NUMERICO_ZEROS_ESQ');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
$obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('parc_fgts');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('NUMERICO_ZEROS_ESQ');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
$obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('precatorios_post');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('NUMERICO_ZEROS_ESQ');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
$obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('div_contratual_demais_rpps');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('NUMERICO_ZEROS_ESQ');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
$obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('div_contratual_ppp_rpps');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('NUMERICO_ZEROS_ESQ');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
$obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('div_mobiliaria_rpps');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('NUMERICO_ZEROS_ESQ');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
$obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('op_credito_inf_12_rpps');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('NUMERICO_ZEROS_ESQ');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
$obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('outras_rpps');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('NUMERICO_ZEROS_ESQ');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
$obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('parc_contr_sociais_prev_rpps');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('NUMERICO_ZEROS_ESQ');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
$obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('parc_contr_sociais_demais_rpps');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('NUMERICO_ZEROS_ESQ');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
$obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('parc_tributos_rpps');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('NUMERICO_ZEROS_ESQ');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
$obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('parc_fgts_rpps');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('NUMERICO_ZEROS_ESQ');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
$obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('precatorios_post_rpps');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('NUMERICO_ZEROS_ESQ');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

unset($rsArquivo);
unset($obTTCEMGDividaConsolidadaRPPS);

?>