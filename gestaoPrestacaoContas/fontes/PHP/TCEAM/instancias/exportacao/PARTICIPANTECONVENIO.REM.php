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
    * Página de Include Oculta - Exportação Arquivos Econtas - PARTICIPANTECONVENIO.REM.txt
    *
    * Data de Criação: 15/07/2014
    *
    * @author: Lisiane Morais
    *
    $Id: PARTICIPANTECONVENIO.REM.php 59815 2014-09-12 17:15:45Z arthur $
    *
    * @ignore
    *
*/
include_once CAM_GPC_TCEAM_MAPEAMENTO.'TTCEAMParticipanteConvenio.class.php';

$boTransacao = new Transacao();

$arFiltro = Sessao::read('filtroRelatorio');
$stEntidade = implode(",", $arFiltro["inCodEntidade"]);

$obTTCEAMParticipanteConvenio = new TTCEAMParticipanteConvenio();
$obTTCEAMParticipanteConvenio->setDado('exercicio'   , Sessao::getExercicio());
$obTTCEAMParticipanteConvenio->setDado('cod_entidade', $stEntidade);
$obTTCEAMParticipanteConvenio->setDado('mes'         , $arFiltro["inMes"]);
$obTTCEAMParticipanteConvenio->recuperaParticipanteConvenio($rsRecordSet, "", "", $boTransacao);

$obExportador->roUltimoArquivo->addBloco($rsRecordSet);
$obExportador->roUltimoArquivo->setTipoDocumento('TCE_AM');

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('cod_cic_psticipante');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('NUMERICO_ZEROS_ESQ');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('tp_pessoa_participante');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('NUMERICO_ZEROS_ESQ');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('nm_participante');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('CARACTER_ESPACOS_DIR');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(50);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('vl_participacao');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('vl_percentual_participacao');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('NUMERICO_ZEROS_ESQ');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(7);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('nu_certidao_casan');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('CARACTER_ESPACOS_DIR');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(60);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("data_certidao_casan");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_YYYYMMDD");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_validadecertidao_casan");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_YYYYMMDD");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('nu_certidao_celesc');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('CARACTER_ESPACOS_DIR');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(60);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_certidao_celesc");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_YYYYMMDD");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_validade_certidao_celesc");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_YYYYMMDD");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nu_certidao_ipesc");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(60);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_certidao_ipesc");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_YYYYMMDD");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_validade_certidao_ipesc");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_YYYYMMDD");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nu_certidao_fazenda_municipal");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(60);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_certidao_fazenda_municipal");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_YYYYMMDD");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_validade_certidao_fazenda_municipal");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_YYYYMMDD");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nu_certidao_fazenda_federal");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(60);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_certidao_fazenda_federal");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_YYYYMMDD");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_validade_certidao_fazenda_federal");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_YYYYMMDD");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nu_certidao_cndt");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(60);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_certidao_cndt");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_YYYYMMDD");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_validade_certidao_cndt");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_YYYYMMDD");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nu_certidao_outras");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(60);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_certidao_outras");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_YYYYMMDD");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_validade_certidao_outras");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_YYYYMMDD");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('num_convenio');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('CARACTER_ESPACOS_DIR');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('tp_esferaconvenio');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('CARACTER_ESPACOS_DIR');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

?>