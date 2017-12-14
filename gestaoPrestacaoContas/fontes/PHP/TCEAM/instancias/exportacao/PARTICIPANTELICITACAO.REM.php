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
    * Página de Include Oculta - Exportação Arquivos Econtas - CERTIDAO.REM.txt
    *
    * Data de Criação: 28/05/2014
    *
    * @author: Evandro Melos
    *
    $Id: PARTICIPANTELICITACAO.REM.php 59612 2014-09-02 12:00:51Z gelson $
    *
    * @ignore
    *
*/
include_once CAM_GPC_TCEAM_MAPEAMENTO.'TTCEAMParticip.class.php';

$boTransacao = new Transacao();

$arFiltro = Sessao::read('filtroRelatorio');
$stEntidade = implode(",", $arFiltro["inCodEntidade"]);

$obTMapeamento = new TTCEAMParticip();
$obTMapeamento->setDado('exercicio'   , Sessao::getExercicio());
$obTMapeamento->setDado('cod_entidade', $stEntidade);
$obTMapeamento->setDado('mes'         , $arFiltro["inMes"]);
$obTMapeamento->recuperaParticipanteLicitacaoEConta($rsRecordSet, "", "", $boTransacao);

$obExportador->roUltimoArquivo->addBloco($rsRecordSet);
$obExportador->roUltimoArquivo->setTipoDocumento('TCE_AM');

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('processo_licitatorio');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('CARACTER_ESPACOS_DIR');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(18);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('cpf_cnpj');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('NUMERICO_ZEROS_ESQ');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('tipo_pessoa');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('NUMERICO_ZEROS_ESQ');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('nom_cgm');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('CARACTER_ESPACOS_DIR');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(50);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('tipo_participacao');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('NUMERICO_ZEROS_ESQ');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('cgc_consorcio');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('NUMERICO_ZEROS_ESQ');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('tipo_convidado');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('CARACTER_ESPACOS_DIR');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

?>