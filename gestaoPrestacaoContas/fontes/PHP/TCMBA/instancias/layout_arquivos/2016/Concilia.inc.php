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
    * Exportação de Arquivos TCMBA
    * Data de Criação : 08/06/2016
    * @author Analista: Valtair Santos
    * @author Desenvolvedor: Michel Teixeira

    $Id: Concilia.inc.php 65710 2016-06-09 20:52:09Z michel $
*/

include_once CAM_GPC_TCMBA_MAPEAMENTO.Sessao::getExercicio()."/TTBAConcilia.class.php";

$obTTBAConcilia = new TTBAConcilia();
$obTTBAConcilia->setDado('inMes'       , $inMes );
$obTTBAConcilia->setDado('stEntidades' , $stEntidades );
$obTTBAConcilia->setDado('stExercicio' , Sessao::getExercicio() );
$obTTBAConcilia->setDado('stDtInicio'  , $stDataInicial);
$obTTBAConcilia->setDado('stDtFim'     , $stDataFinal);
$obTTBAConcilia->setDado('inCodGestora', $inCodUnidadeGestora );
$obTTBAConcilia->recuperaDadosTribunal($rsConciliacao);


$obExportador->roUltimoArquivo->addBloco($rsConciliacao);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("exercicio");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("unidade_gestora");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_estrutural");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("ALFANUMERICO_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(34);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("competencia");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("reservado");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_tipo_conciliacao");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("descricao");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("ALFANUMERICO_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(100);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("data_conciliacao");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_lancamento");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_conciliacao");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_tipo_pagamento");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_documento");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

?>