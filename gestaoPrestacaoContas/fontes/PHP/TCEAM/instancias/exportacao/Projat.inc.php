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
    * Data de Criação: 14/03/2011
    *
    *
    * @author: Tonismar R. Bernardo
    * @ignore
    *
*/
include_once CAM_GF_PPA_MAPEAMENTO . 'TPPA.class.php';
include_once CAM_GPC_TCEAM_MAPEAMENTO . 'TTCEAMPPA.class.php';

$obPPA = new TPPA();
$stFiltro = " WHERE '".Sessao::getExercicio()."' between ano_inicio and ano_final";
$obPPA->recuperaPPA( $rsPPA, $stFiltro );
$obMapeamento = new TTCEAMPPA();
$stFiltro = ' WHERE cod_ppa = '.$rsPPA->getCampo('cod_ppa');
$obMapeamento->recuperaTodos( $rsAcoes, $stFiltro );

$rsAcoes->setPrimeiroElemento();

$obExportador->roUltimoArquivo->addBloco($rsAcoes);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('reservado_tce');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('NUMERICO_ZEROS_ESQ');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(5);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('num_acao');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('NUMERICO_ZEROS_ESQ');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(7);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('titulo');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('CARACTER_ESPACOS_DIR');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(100);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('objetivo');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('CARACTER_ESPACOS_DIR');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(240);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('cod_tipo');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('NUMERICO_ZEROS_ESQ');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
