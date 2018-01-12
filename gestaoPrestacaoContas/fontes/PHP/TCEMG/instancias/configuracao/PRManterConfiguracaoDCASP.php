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

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GPC_TCEMG_MAPEAMENTO . 'TTCEMGCampoContaCorrente.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoDCASP";
$pgFilt = "FL" . $stPrograma . ".php";
$pgList = "LS" . $stPrograma . ".php";
$pgForm = "FM" . $stPrograma . ".php";
$pgProc = "PR" . $stPrograma . ".php";
$pgOcul = "OC" . $stPrograma . ".php";

$stAcao = $request->get('stAcao');
Sessao::setTrataExcecao(true);

$tipoRegistro = Sessao::read('tipoRegistro');
$codSequencial = Sessao::read('codSequencialCampo');
$contas = Sessao::read('contasContabeis');

/*
- ESTÁ SALVANDO APENAS O ULTIMO REGISTRO QUANDO EXISTEM MUITAS CONTAS (EX 2.1)
- IMPLEMENTAR A EXCLUSÃO QUANDO FOR INSERIR O REGISTRO, ELA NÃO ESTÁ FUNCIONANDO
- IMPLEMENTAR O SALVAMENTO DE CONTAS ORÇAMENTARIAS PARA OS OUTROS ARQUIVOS
*/

$TTCEMGCampoContaCorrente = new TTCEMGCampoContaCorrente();
foreach ($contas as $key => $value) {
  // $TTCEMGCampoContaCorrente->exclusao($boTransacao);

  $TTCEMGCampoContaCorrente->setDado('exercicio', $value['exercicio']);
  $TTCEMGCampoContaCorrente->setDado('tipo_registro', $_REQUEST['tipoRegistro']);
  $TTCEMGCampoContaCorrente->setDado('cod_arquivo', $_REQUEST['codArquivo']);
  $TTCEMGCampoContaCorrente->setDado('seq_arquivo', $_REQUEST['seqArquivo']);
  if ($_REQUEST['stNomeArquivo'] == 'BO' || $_REQUEST['stNomeArquivo'] == 'BF') {
    $TTCEMGCampoContaCorrente->setDado('conta_orc_despesa', $value['conta_contabil_despesa']);
    $TTCEMGCampoContaCorrente->setDado('cont_orc_receita', $value['conta_contabil_receita']);
    $TTCEMGCampoContaCorrente->setDado('conta_contabil', NULL);
  }
  else {
    $TTCEMGCampoContaCorrente->setDado('conta_orc_despesa', NULL);
    $TTCEMGCampoContaCorrente->setDado('cont_orc_receita', NULL);
    $TTCEMGCampoContaCorrente->setDado('conta_contabil', $value['conta_contabil']);
  }

  $TTCEMGCampoContaCorrente->inclusao($boTransacao);
}

SistemaLegado::alertaAviso($pgForm . "?" . Sessao::getId() . "&stAcao=$stAcao", "Configuração ", "incluir", "incluir_n", Sessao::getId(), "../");

Sessao::encerraExcecao();
