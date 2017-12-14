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

    * @author Tonismar R. Bernardo

    * @date: 17/03/2011

    * @ignore
*/
include_once( CAM_GPC_TCEAM_MAPEAMENTO."TTCEAMEmenta.class.php" );
$obTMapeamento = new TTCEAMEmenta();
$obTMapeamento->setDado('exercicio',Sessao::getExercicio());
$obTMapeamento->setDado('inMes'      , $inMes );
$obTMapeamento->setDado('entidades', $stEntidades );
if (array_search( 'Ementa.txt', $arFiltro['arArquivosSelecionados'] ) !== FALSE && $inMes == 12 && count($arFiltro['inCodEntidade']) > 1) {
    $stCodEntidadesIncorporar = '';
    foreach ($arFiltro['inCodEntidade'] as $inCodEntidade) {
        if ($inCodEntidade != $inCodEntidadePrefeitura) {
            $stCodEntidadesIncorporar .= ','.$inCodEntidade;
        }
    }

    $stCodEntidadesIncorporar = substr($stCodEntidadesIncorporar, 1);

    if (!empty($stCodEntidadesIncorporar)) {
        $obTMapeamento->setDado('entidades', $inCodEntidadePrefeitura );
        $obTMapeamento->setDado('boIncorporar', true );
        $obTMapeamento->setDado('stCodEntidadesIncorporar', $stCodEntidadesIncorporar );
    }
}

$obTMapeamento->recuperaTodos($rsRecordSet);

$obExportador->roUltimoArquivo->addBloco($rsRecordSet);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("reservado_tse");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(5);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_tipo_norma");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("[num_norma]");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("[cod_tipo]");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_unidade_descentralizada");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);
