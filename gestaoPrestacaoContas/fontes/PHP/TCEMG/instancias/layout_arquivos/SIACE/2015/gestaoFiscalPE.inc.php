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
    * Arquivo de geracao do arquivo gestaoFiscalPE TCM/MG
    * Data de Criação   : 09/03/2015

    * @author Analista      Dagiane Vieira
    * @author Desenvolvedor Michel Teixeira

    * @package URBEM
    * @subpackage

    * @ignore

    $Id: gestaoFiscalPE.inc.php 62778 2015-06-17 13:49:46Z jean $
    */

    include_once ( CAM_GPC_TCEMG_MAPEAMENTO.Sessao::getExercicio().'/TTCEMGGestaoFiscalPE.class.php' );

    $arFiltros = Sessao::read('filtroRelatorio');

    foreach ($arDatasInicialFinal as $stDatas) {
        $obTTCEMGGestaoFiscalPE = new TTCEMGGestaoFiscalPE();

        list($inDia, $inMes, $inAno) = explode('/',$stDatas['stDtInicial']);

        $stFiltro = " WHERE medidas.cod_mes = ".(integer)$inMes;

        $obTTCEMGGestaoFiscalPE->setDado('periodo', (integer)$inMes);
        $obTTCEMGGestaoFiscalPE->recuperaDados($rsArquivo, $stFiltro);

        $rsArquivo->setPrimeiroElemento();
    
        $obExportador->roUltimoArquivo->addBloco($rsArquivo);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('periodo');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('medida');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(10000);
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('contratacao_aro');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    }
?>