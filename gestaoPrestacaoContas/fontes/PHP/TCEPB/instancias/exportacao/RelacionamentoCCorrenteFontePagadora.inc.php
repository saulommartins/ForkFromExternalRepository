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
 * Arquivo que gera os dados de relacionamento das contas bancárias com a fonte pagadora
 * Data de Criação   : 18/02/2009

 * @author Analista      Tonismar Regis Bernardo
 * @author Desenvolvedor Eduardo Paculski Schitz

 * @package URBEM
 * @subpackage

 * @ignore

 $Id:$
 */

    include_once( CAM_GPC_TPB_MAPEAMENTO."TTPBRelacaoContaBancariaFontePagadora.class.php" );
    
    $obTTPBRelacaoContaBancariaFontePagadora = new TTPBRelacaoContaBancariaFontePagadora();
    $obTTPBRelacaoContaBancariaFontePagadora->setDado('exercicio'  , Sessao::getExercicio() );
    
    $obTTPBRelacaoContaBancariaFontePagadora->recuperaRelacionamentoCCorrenteFontePagadora($arRecordSet[$stArquivo]);
    
    $obExportador->roUltimoArquivo->addBloco($arRecordSet[$stArquivo]);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("reservado_tce");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_conta_corrente");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(12);
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_tipo");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("reservado_tce");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);

?>