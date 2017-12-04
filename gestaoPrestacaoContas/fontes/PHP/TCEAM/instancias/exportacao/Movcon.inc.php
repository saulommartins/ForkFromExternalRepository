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
include_once CAM_GPC_TCEAM_MAPEAMENTO.'TTCEAMMovcon.class.php';

$obTMapeamento = new TTCEAMMovcon();

$obTMapeamento->setDado('exercicio'   , Sessao::getExercicio());
$obTMapeamento->setDado('cod_entidade', $stEntidades);
$obTMapeamento->setDado('mes'         , $inMes);
if (array_search( 'Movcon.txt', $arFiltro['arArquivosSelecionados'] ) !== FALSE && $inMes == 12 && count($arFiltro['inCodEntidade']) > 1) {
    $stCodEntidadesIncorporar = '';
    foreach ($arFiltro['inCodEntidade'] as $inCodEntidade) {
        if ($inCodEntidade != $inCodEntidadePrefeitura) {
            $stCodEntidadesIncorporar .= ','.$inCodEntidade;
        }
    }

    $stCodEntidadesIncorporar = substr($stCodEntidadesIncorporar, 1);

    if (!empty($stCodEntidadesIncorporar)) {
        $obTMapeamento->setDado('cod_entidade', $inCodEntidadePrefeitura );
        $obTMapeamento->setDado('boIncorporar', true );
        $obTMapeamento->setDado('stCodEntidadesIncorporar', $stCodEntidadesIncorporar );
    }
}
$obTMapeamento->recuperaSaldoContabilContas($rsRecordSet);

$obExportador->roUltimoArquivo->addBloco($rsRecordSet);
$obExportador->roUltimoArquivo->setTipoDocumento('TCE_AM');
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('reservado_tce');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('NUMERICO_ZEROS_ESQ');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('exercicio');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('NUMERICO_ZEROS_ESQ');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('conta_contabil');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('CARACTER_ESPACOS_DIR');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(34);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('tipo_movimento');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('NUMERICO_ZEROS_ESQ');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('vl_debito');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('vl_credito');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

?>
