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

include_once(CAM_GPC_TCEMG_MAPEAMENTO.Sessao::getExercicio().'/TTCEMGReceitaIntra.class.php');

$arFiltros = Sessao::read('filtroRelatorio');
$obTTCEMGReceitaIntra = new TTCEMGReceitaIntra();

$obTTCEMGReceitaIntra->setDado('exercicio'   , Sessao::getExercicio() );
$obTTCEMGReceitaIntra->setDado('cod_entidade', implode(',',$arFiltros['inCodEntidadeSelecionado']));
$obTTCEMGReceitaIntra->setDado('mes'         , '12');
$obTTCEMGReceitaIntra->setDado('dt_inicial', '01/01/'.Sessao::getExercicio() );
$obTTCEMGReceitaIntra->setDado('dt_final', '31/12/'.Sessao::getExercicio()   );
$obTTCEMGReceitaIntra->recuperaTodos($rsArquivo);

$obExportador->roUltimoArquivo->addBloco($rsArquivo);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('mes');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('NUMERICO_ZEROS_ESQ');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
$obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('demais_receita_intra');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
$obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('cod_tipo');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('NUMERICO_ZEROS_ESQ');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
$obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('amortizacao_emprestimos');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

?>