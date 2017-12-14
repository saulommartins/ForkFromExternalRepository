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
    * Data de Criação   : 03/02/2009

    * @author Analista      Tonismar Régis Bernardo
    * @author Desenvolvedor Lucas Andrades Mendes

    * @package URBEM
    * @subpackage

    * @ignore

    $Id:$
 */

    include_once( CAM_GPC_TCEMG_MAPEAMENTO . 'TTCEMGVariacaoPatrimonial.class.php');

    $arFiltros = Sessao::read('filtroRelatorio');

    if ($arFiltros['inPeriodo']<10) {
        $stMes = "0".$arFiltros['inPeriodo'];
    } else {
        $stMes = $arFiltros['inPeriodo'];
    }

    $dtPeriodoInicial = "01/".$stMes."/".Sessao::read('exercicio');
    $dtPeriodoFinal   = date("t",mktime(0,0,0,$arFiltros['inPeriodo'],1,Sessao::read('exercicio')))."/".$stMes."/".Sessao::read('exercicio');

    $filtro= 'cod_entidade IN  ('.implode(',',$arFiltros['inCodEntidadeSelecionado']).')';

    $obFTCEMGVariacaoPatrimonial = new TTCEMGVariacaoPatrimonial();
    $obFTCEMGVariacaoPatrimonial->setDado('stEntidade', implode(',',$arFiltros['inCodEntidadeSelecionado']));
    $obFTCEMGVariacaoPatrimonial->setDado('stExercicio'   , Sessao::read('exercicio'));
    $obFTCEMGVariacaoPatrimonial->setDado('mes'         , $arFiltros['inPeriodo']);

    $obFTCEMGVariacaoPatrimonial->recuperaRelacionamento($rsArquivo);

    $obExportador->roUltimoArquivo->addBloco($rsArquivo);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('mes');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('deficit');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('superavit');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
