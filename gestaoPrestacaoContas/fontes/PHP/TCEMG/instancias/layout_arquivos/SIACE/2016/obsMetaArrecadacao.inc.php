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
    * Arquivo de geracao do arquivo obsMetaArrecadacao.txt
    * Data de Criação   : 21/01/2009

    * @author Analista      Tonismar Régis Bernardo
    * @author Desenvolvedor Alexandre Melo

    * @package URBEM
    * @subpackage

    * @ignore

    $Id:$
    */

    include_once( CAM_GPC_TCEMG_MAPEAMENTO.Sessao::getExercicio().'/TTCEMGObsMetaArrecadacao.class.php');

    $arFiltros = Sessao::read('filtroRelatorio');

    $obTTCEMGObsMetaArrecadacao = new TTCEMGObsMetaArrecadacao();
    $obTTCEMGObsMetaArrecadacao->setDado('exercicio'   , Sessao::getExercicio());
    $obTTCEMGObsMetaArrecadacao->setDado('periodo'     , ($arFiltros['inPeriodo']*2));

    $obTTCEMGObsMetaArrecadacao->recuperaDadosArquivo($rsArquivo);
    $obExportador->roUltimoArquivo->addBloco($rsArquivo);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('periodo');
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('observacao');

?>
