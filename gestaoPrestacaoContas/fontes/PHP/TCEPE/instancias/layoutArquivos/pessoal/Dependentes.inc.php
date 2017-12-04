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
  * Layout exportação TCE-PE arquivo : Dependentes
  * Data de Criação: 29/10/2014

  * @author Analista: Dagiane Vieira
  * @author Desenvolvedor: Michel Teixeira
  *
  * @ignore
  * $Id: Dependentes.inc.php 60558 2014-10-29 16:01:58Z michel $
  * $Date: 2014-10-29 14:01:58 -0200 (Wed, 29 Oct 2014) $
  * $Author: michel $
  * $Rev: 60558 $
  *
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GPC_TCEPE_MAPEAMENTO.'TTCEPEDependentes.class.php';
include_once CAM_GRH_ENT_MAPEAMENTO.'TEntidade.class.php';
include_once CAM_GRH_FOL_MAPEAMENTO.'TFolhaPagamentoPeriodoMovimentacao.class.php';

$boTransacao = new Transacao();
$obTEntidade = new TEntidade();
$rsRecordSet = new RecordSet();

$stFiltro = " WHERE nspname = 'folhapagamento_".$inCodEntidade."'";
$obTEntidade->recuperaEsquemasCriados($rsEsquemas,$stFiltro);

$obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
$obTFolhaPagamentoPeriodoMovimentacao->setDado("mes", $inCodCompetencia     );
$obTFolhaPagamentoPeriodoMovimentacao->setDado("ano", Sessao::getExercicio());

$codEntidadePrefeitura = SistemaLegado::pegaConfiguracao("cod_entidade_prefeitura", 8, $stAno);

if ($codEntidadePrefeitura == $inCodEntidade || $rsEsquemas->getNumLinhas() < 1) {
    $stEntidade = "";
} else {
    $stEntidade = "_".$inCodEntidade;    
}

$stFiltro = " AND to_char(dt_final, 'mm/yyyy') = '".str_pad($inCodCompetencia,2,'0', STR_PAD_LEFT)."/".Sessao::getExercicio()."'";
$obTFolhaPagamentoPeriodoMovimentacao->recuperaPeriodoMovimentacao($rsPeriodoMovimentacao, $stFiltro, "", "", "folhapagamento".$stEntidade);

if ($rsPeriodoMovimentacao->getElementos() > 0 &&($codEntidadePrefeitura == $inCodEntidade || $rsEsquemas->getNumLinhas() > 1)) {
    $inCodMovimentacao = 0;
    
    foreach ($rsPeriodoMovimentacao->getElementos() as $arPeriodoMovimentacao ) {
        $inCodMovimentacao = $arPeriodoMovimentacao['cod_periodo_movimentacao'];
    }

    $obTTCEPEDependentes = new TTCEPEDependentes();
    $obTTCEPEDependentes->setDado('stEntidades'      , $stEntidade                                                           );
    $obTTCEPEDependentes->setDado('stMes'            , str_pad($inCodCompetencia,2,'0', STR_PAD_LEFT).Sessao::getExercicio() );
    $obTTCEPEDependentes->setDado('inCodMovimentacao', $inCodMovimentacao                                                    );
    $obTTCEPEDependentes->setDado('stExercicio'      , Sessao::getExercicio()                                                );
    $obTTCEPEDependentes->recuperaDependentes($rsRecordSet);
}

$obExportador->roUltimoArquivo->addBloco($rsRecordSet);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("reservado_tce");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("matricula");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(15);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cpf");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(11);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cpf_dependente");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(11);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nom_dependente");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(80);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_nascimento");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_parentesco");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_inicio");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("reservado_tce");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);
?>
