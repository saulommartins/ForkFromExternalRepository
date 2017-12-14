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
    * Página de Include Oculta - Exportacão Arquivos

    * Data de Criacão   : 22/03/2011

    * @author Desenvolvedor: Davi Ritter Aroldi

    * @ignore
*/
include_once( CAM_GPC_TCEAM_MAPEAMENTO."TTCEAMLiquidacao.class.php" );
$obTMapeamento = new TTCEAMLiquidacao();
$obTMapeamento->setDado('exercicio'  , Sessao::getExercicio() );
$obTMapeamento->setDado('inMes'      , $inMes );
$obTMapeamento->setDado('stEntidades', $stEntidades );
if ($boIncorporarEmpenhos) {
    $obTMapeamento->setDado('boIncorporarEmpenhos', $boIncorporarEmpenhos );
    $obTMapeamento->setDado('stCodEntidadesIncorporadas', $stCodEntidadesIncorporadas );
    $obTMapeamento->setDado('stEntidades', $inCodEntidadePrefeitura );
}
$obTMapeamento->recuperaTodos($arRecordSet[$stArquivo]);

$obExportador->roUltimoArquivo->addBloco($arRecordSet[$stArquivo]);
$obExportador->roUltimoArquivo->setTipoDocumento('TCE_AM');
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("reservado_tce");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(5);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("exercicio_empenho");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("unidade_orcamentaria");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_empenho");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(10);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_liquidacao");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_YYYYMMDD");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("valor");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_status_anulacao");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_anulacao");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_YYYYMMDD");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
