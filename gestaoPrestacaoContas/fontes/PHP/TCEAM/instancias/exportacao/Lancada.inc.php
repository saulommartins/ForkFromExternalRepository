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
    * Página de Include Oculta - Exportação Arquivos
    *
    * Data de Criação: 10/03/2011
    *
    *
    * @author: Eduardo Paculski Schitz
    * @ignore
    *
*/
include_once CAM_GPC_TCEAM_MAPEAMENTO.'TTCEAMReceita.class.php';

$obTMapeamento = new TTCEAMReceita();

$obTMapeamento->setDado('exercicio'  , Sessao::getExercicio());
$obTMapeamento->setDado('stEntidades', $stEntidades);
$obTMapeamento->setDado('inMes'      , $inMes);
if (array_search( 'Lancada.txt', $arFiltro['arArquivosSelecionados'] ) !== FALSE && $inMes == 12 && count($arFiltro['inCodEntidade']) > 1) {
    $stCodEntidadesIncorporar = '';
    foreach ($arFiltro['inCodEntidade'] as $inCodEntidade) {
        if ($inCodEntidade != $inCodEntidadePrefeitura) {
            $stCodEntidadesIncorporar .= ','.$inCodEntidade;
        }
    }

    $stCodEntidadesIncorporar = substr($stCodEntidadesIncorporar, 1);

    if (!empty($stCodEntidadesIncorporar)) {
        $obTMapeamento->setDado('stEntidades', $inCodEntidadePrefeitura );
        $obTMapeamento->setDado('boIncorporar', true );
        $obTMapeamento->setDado('stCodEntidadesIncorporar', $stCodEntidadesIncorporar );
    }
}
$obTMapeamento->recuperaReceitaOrcamentariaLancada($rsRecordSet);

$obExportador->roUltimoArquivo->addBloco($rsRecordSet);
$obExportador->roUltimoArquivo->setTipoDocumento('TCE_AM');
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('reservado_tc');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('NUMERICO_ZEROS_ESQ');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(5);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('codigo_receita');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('NUMERICO_ZEROS_ESQ');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(9);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('tipo_atualizacao');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('NUMERICO_ZEROS_ESQ');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('vl_lancamento');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

?>
