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
  * Layout exportação TCE-PE arquivo : 
  * Data de Criação

  * @author Analista:
  * @author Desenvolvedor:Lisiane Morais
  *
  * @ignore
  * $Id: FolhaPagamento.inc.php 61158 2014-12-12 10:44:35Z jean $
  * $Date: 2014-12-12 08:44:35 -0200 (Fri, 12 Dec 2014) $
  * $Author: jean $
  * $Rev: 61158 $
  *
*/

include_once('../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php');
include_once('../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php');
include_once CAM_GPC_TCEPE_MAPEAMENTO."TTCEPEFolhaPagamento.class.php";
include_once CAM_GRH_FOL_MAPEAMENTO.'TFolhaPagamentoPeriodoMovimentacao.class.php';

$boTransacao = new Transacao();
$rsRecordSet = new RecordSet();
$obTTCEPEFolhaPagamento = new TTCEPEFolhaPagamento();
$obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();


if ($inCodCopetencia < 10) {
    $inCodCompetencia = "0".$inCodCompetencia;
}

$dtCompetencia = $inCodCompetencia.'/'.Sessao::getExercicio();

$stFiltroPeriodo = " AND to_char(dt_final,'mm/yyyy') = '".$dtCompetencia."'";
$obTFolhaPagamentoPeriodoMovimentacao->recuperaPeriodoMovimentacao($rsPeriodoMovimentacao,$stFiltroPeriodo);

if ($inCodEntidade == SistemaLegado::pegaConfiguracao('cod_entidade_prefeitura',8,Sessao::getExercicio())) {
    $inCodEntidade = '';
}else{
    $inCodEntidade = "_".$inCodEntidade;
}

$obTTCEPEFolhaPagamento->setDado('exercicio'               , Sessao::getExercicio()  );
$obTTCEPEFolhaPagamento->setDado('stDataInicial'           , $stDataInicial  );
$obTTCEPEFolhaPagamento->setDado('stDataFinal'             , $stDataFinal  );
$obTTCEPEFolhaPagamento->setDado('stEntidade'              , $inCodEntidade  );
$obTTCEPEFolhaPagamento->setDado('inCodPeriodoMovimentacao', $rsPeriodoMovimentacao->getCampo('cod_periodo_movimentacao')  );

$obTTCEPEFolhaPagamento->recuperaFolhaPagamento($rsRecordSet, "" ,"" , $boTransacao );
$obExportador->roUltimoArquivo->addBloco($rsRecordSet);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("reservado_tce");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("matricula");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(15);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_cargo");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cpf");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(11);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("mes_folha");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("ano_folha");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_folha");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_fonte_recurso");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("horas_semanais");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("valor_previdencia");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("valor_irrf");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("reservado_tce");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);

?>
