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

    include_once ( CAM_GPC_TCEMG_MAPEAMENTO.Sessao::getExercicio()."/TTCEMGGestaoFiscalPL.class.php" );
    include_once(CAM_FW_HTML."Bimestre.class.php");

    $obBimestre = new Bimestre();
    $arFiltros = Sessao::read('filtroRelatorio');

    $obTTCEMGMedidas = new TTCEMGGestaoFiscalPL();

    //Retirando o mes da data inicial e final
    $inMeses = substr($obBimestre->getDataInicial($arFiltros['inPeriodo'],Sessao::read('exercicio')),3,2).",".substr($obBimestre->getDataFinal($arFiltros['inPeriodo'],Sessao::read('exercicio')),3,2);

    $stFiltro = " WHERE medidas.cod_mes IN (".$inMeses.")";
    $stFiltro .=" AND LOWER(poder_publico.nome) = '".$arFiltros['stTipoPoder']."'";

    $obTTCEMGMedidas->recuperaDados($rsArquivo, $stFiltro);

    $obExportador->roUltimoArquivo->addBloco($rsArquivo);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('cod_mes');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('medida');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(4000);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');

?>
