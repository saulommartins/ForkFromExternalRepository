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
 * Arquivo de geracao do arquivo receitaCapital TCM/MG
 * Data de Criação   : 05/03/2015

 * @author Analista      Ane Pereira
 * @author Desenvolvedor Michel Teixeira

 * @package URBEM
 * @subpackage

 * @ignore

 $Id: receitaCapital.inc.php 62741 2015-06-15 16:43:36Z franver $
 */
include_once CAM_GPC_TCEMG_MAPEAMENTO.Sessao::getExercicio().'/FTCEMGReceitaCapital.class.php';

$arFiltros = Sessao::read('filtroRelatorio');

$obFTCEMGReceitaCapital = new FTCEMGReceitaCapital();        
$obFTCEMGReceitaCapital->setDado('exercicio'    , Sessao::getExercicio());
$obFTCEMGReceitaCapital->setDado('cod_entidade' , implode(',',$arFiltros['inCodEntidadeSelecionado']));
$obFTCEMGReceitaCapital->setDado('mes'          , $arFiltros['inPeriodo']);
$obFTCEMGReceitaCapital->setDado('dt_inicial'   , $dataInicial);
$obFTCEMGReceitaCapital->setDado('dt_final'     , $dataFinal);

$obFTCEMGReceitaCapital->recuperaTodos($rsArquivo);

$obExportador->roUltimoArquivo->addBloco($rsArquivo);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('mes');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
$obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('cod_tipo');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
$obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('rec_alienacao');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
$obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('rec_amort');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
$obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('rec_transf_capital');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
$obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('rec_convenios');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('out_rec_cap');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('rec_ret_op_cred');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('rec_privat');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('rec_ref_divida');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('rec_out_op_cred');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('deducoes');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

unset($arFiltros);
unset($obFTCEMGReceitaCapital);
unset($rsArquivo);

?>