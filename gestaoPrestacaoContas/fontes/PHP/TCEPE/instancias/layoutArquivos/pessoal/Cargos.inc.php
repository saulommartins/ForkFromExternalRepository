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
  * @author Desenvolvedor:
  *
  * @ignore
  * $Id: Cargos.inc.php 60559 2014-10-29 16:32:38Z carlos.silva $
  * $Date: 2014-10-29 14:32:38 -0200 (Wed, 29 Oct 2014) $
  * $Author: carlos.silva $
  * $Rev: 60559 $
  *
*/

include_once('../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php');
include_once('../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php');
include_once CAM_GPC_TCEPE_MAPEAMENTO."TTCEPECargo.class.php";
include_once CAM_GRH_ENT_MAPEAMENTO.'TEntidade.class.php';
include_once CAM_GRH_FOL_MAPEAMENTO.'TFolhaPagamentoPeriodoMovimentacao.class.php';


$boTransacao = new Transacao;
$rsRecordSet = new RecordSet;
$obTEntidade = new TEntidade;
$obTTCEPECargo = new TTCEPECargo;
$obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();

### Bloco de código para montar stEntidade
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
###

### Bloco de código para recuperar data final da competência
if ($inCodCopetencia < 10) {
    $inCodCompetencia = "0".$inCodCompetencia;
}

$stFiltroPeriodo = " AND to_char(dt_final,'mm/yyyy') = '".$inCodCompetencia.'/'.Sessao::getExercicio()."'";
$obTFolhaPagamentoPeriodoMovimentacao->recuperaPeriodoMovimentacao($rsPeriodoMovimentacao,$stFiltroPeriodo);
###


$obTTCEPECargo->setDado('exercicio', Sessao::getExercicio());
$obTTCEPECargo->setDado('entidade' , $stEntidade );
$obTTCEPECargo->setDado('dt_final' , SistemaLegado::dataToSql($rsPeriodoMovimentacao->getCampo('dt_final')));
$obTTCEPECargo->recuperaTodos($rsRecordSet, "" ,"" , $boTransacao );

$obExportador->roUltimoArquivo->addBloco($rsRecordSet);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("reservado_tce");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_cargo");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("descricao");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(200);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_escolaridade");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("horas_semanais");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("reservado_tce");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);

?>