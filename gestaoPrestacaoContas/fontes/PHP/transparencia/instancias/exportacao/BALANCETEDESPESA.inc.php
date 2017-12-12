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
    * Página Oculta - Exportação Arquivos GPC

    * Data de Criação   : 19/11/2012

    * @author Analista: Gelson
    * @author Desenvolvedor: Jean

    * @ignore

    $Id:

    * Casos de uso:
*/

include_once CAM_GF_ORC_MAPEAMENTO."FOrcamentoBalanceteDespesa.class.php";

$stExercicio = substr($arFiltroRelatorio['stDataFinal'],6);

$obTMapeamento = new FOrcamentoBalanceteDespesa();
$obTMapeamento->setDado('stExercicio'   , $stExercicio);
$obTMapeamento->setDado('stDataInicial' , $arFiltroRelatorio['stDataInicial']);
$obTMapeamento->setDado('stDataFinal'   , $arFiltroRelatorio['stDataFinal']);
$obTMapeamento->setDado('stEntidades'   , $stEntidades);
$obTMapeamento->recuperaTransparencia($rsBalanceteDespesa);

$obExportador->roUltimoArquivo->addBloco($rsBalanceteDespesa);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_entidade");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_orgao");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(5);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_unidade");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(5);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_funcao");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_subfuncao");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_programa");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_pao");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(5);

# Validar com Valtair sobre o cod_elemento, que antes era enviado os 6 primeiros dígitos e agora está sendo enviado o cod_estrutural inteiro com 14 nro.
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_subelemento");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_recurso");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("saldo_inicial");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('atualizacao');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("credito_suplementar");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("credito_especial");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("credito_extraordinario");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("reducoes");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("suplementacoes");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("reducoes");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_empenho");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("liquidado_per");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("pago_per");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

?>
