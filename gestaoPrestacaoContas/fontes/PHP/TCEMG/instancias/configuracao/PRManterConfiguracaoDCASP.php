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
include_once CAM_GPC_TCEMG_MAPEAMENTO . 'TTCEMGConfiguracaoDCASPRegistro.class.php';
include_once CAM_GPC_TCEMG_MAPEAMENTO . 'TTCEMGConfiguracaoDCASPRecurso.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoDCASP";
$pgFilt = "FL" . $stPrograma . ".php";
$pgList = "LS" . $stPrograma . ".php";
$pgForm = "FM" . $stPrograma . ".php";
$pgProc = "PR" . $stPrograma . ".php";
$pgOcul = "OC" . $stPrograma . ".php";


$exercicio = Sessao::getExercicio();

$boTransacao = new Transacao();

removerTodasContas($request, $boTransacao, $exercicio);

inserirContas($request, $boTransacao, $exercicio, CAM_GPC_TCEMG_DCASP_CONF_DESPESA);

inserirContas($request, $boTransacao, $exercicio, CAM_GPC_TCEMG_DCASP_CONF_RECEITA);

inserirContas($request, $boTransacao, $exercicio, CAM_GPC_TCEMG_DCASP_CONF_CONTABIL);

removerTodosRecursos($request, $boTransacao, $exercicio);

inserirRecursos($request, $boTransacao, $exercicio);


function removerTodasContas($request, $boTransacao, $exercicio) {
	$customWhere = ' WHERE seq_arquivo = ' . $request->get('seqArquivo'). " AND exercicio = '"
		.$exercicio."'";
	$TTCEMGConfiguracaoDCASPRegistro = new TTCEMGConfiguracaoDCASPRegistro();
	$TTCEMGConfiguracaoDCASPRegistro->setCustomWhere($customWhere);
	$TTCEMGConfiguracaoDCASPRegistro->exclusao($boTransacao);
	return;
}

function inserirContas($request, $boTransacao, $exercicio, $tipoConta) {
	$contas = Sessao::read($tipoConta);
	if (empty($contas)) {
		return;
	}
	$TTCEMGConfiguracaoDCASPRegistro = new TTCEMGConfiguracaoDCASPRegistro();
	foreach ($contas as $key => $value) {
		$TTCEMGConfiguracaoDCASPRegistro->proximoCod($cod);
		$TTCEMGConfiguracaoDCASPRegistro->setDado('cod_registro', $cod);
		$TTCEMGConfiguracaoDCASPRegistro->setDado('exercicio', $exercicio);
		$TTCEMGConfiguracaoDCASPRegistro->setDado('tipo_registro', $request->get('tipoRegistro'));
		$TTCEMGConfiguracaoDCASPRegistro->setDado('cod_arquivo', $request->get('codArquivo'));
		$TTCEMGConfiguracaoDCASPRegistro->setDado('seq_arquivo', $request->get('seqArquivo'));
		if ($tipoConta == CAM_GPC_TCEMG_DCASP_CONF_DESPESA) {
			$TTCEMGConfiguracaoDCASPRegistro->setDado('conta_orc_despesa',
				$value['cod_estrutural']);
			$TTCEMGConfiguracaoDCASPRegistro->setDado('conta_orc_receita', null);
			$TTCEMGConfiguracaoDCASPRegistro->setDado('conta_contabil', null);
		} elseif ($tipoConta == CAM_GPC_TCEMG_DCASP_CONF_RECEITA) {
			$TTCEMGConfiguracaoDCASPRegistro->setDado('conta_orc_despesa', null);
			$TTCEMGConfiguracaoDCASPRegistro->setDado('conta_orc_receita',
				$value['cod_estrutural']);
			$TTCEMGConfiguracaoDCASPRegistro->setDado('conta_contabil', null);
		} else {
			$TTCEMGConfiguracaoDCASPRegistro->setDado('conta_orc_despesa', null);
			$TTCEMGConfiguracaoDCASPRegistro->setDado('conta_orc_receita', null);
			$TTCEMGConfiguracaoDCASPRegistro->setDado('conta_contabil', $value['cod_estrutural']);
		}

		$TTCEMGConfiguracaoDCASPRegistro->inclusao($boTransacao);
	}
	return;
}


function removerTodosRecursos($request, $boTransacao, $exercicio) {
	$customWhere = ' WHERE seq_arquivo = ' . $request->get('seqArquivo'). " AND exercicio = '"
		.$exercicio."'";
	$TTCEMGConfiguracaoDCASPRecurso = new TTCEMGConfiguracaoDCASPRecurso();
	$TTCEMGConfiguracaoDCASPRecurso->setCustomWhere($customWhere);
	$TTCEMGConfiguracaoDCASPRecurso->exclusao($boTransacao);
	return;
}
function inserirRecursos($request, $boTransacao, $exercicio) {
	$recursos = Sessao::read('arRecursos');
	if (empty($recursos)) {
		return;
	}
	$TTCEMGConfiguracaoDCASPRecurso = new TTCEMGConfiguracaoDCASPRecurso();
	foreach ($recursos as $key => $value) {

		$TTCEMGConfiguracaoDCASPRecurso->setDado('cod_recurso', $value["cod_recurso"]);
		$TTCEMGConfiguracaoDCASPRecurso->setDado('exercicio', $exercicio);
		$TTCEMGConfiguracaoDCASPRecurso->setDado('tipo_registro', $request->get('tipoRegistro'));
		$TTCEMGConfiguracaoDCASPRecurso->setDado('cod_arquivo', $request->get('codArquivo'));
		$TTCEMGConfiguracaoDCASPRecurso->setDado('seq_arquivo', $request->get('seqArquivo'));

		$TTCEMGConfiguracaoDCASPRecurso->inclusao($boTransacao);
	}
	return;
}
