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
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GPC_TCEMG_MAPEAMENTO . 'TTCEMGConfiguracaoDCASPRegistro.class.php';
include_once CAM_GPC_TCEMG_MAPEAMENTO . 'TTCEMGConfiguracaoDCASPRecurso.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoDCASP";
$pgFilt = "FL" . $stPrograma . ".php";
$pgList = "LS" . $stPrograma . ".php";
$pgForm = "FM" . $stPrograma . ".php";
$pgProc = "PR" . $stPrograma . ".php";
$pgOcul = "OC" . $stPrograma . ".php";
$pgJs = "JS" . $stPrograma . ".js";

$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];

function removerConta($request) {
  $tipoConta = $request->get('tipo_conta');
  $codEstrutural = $request->get('cod_estrutural');
  $arContas = Sessao::read($tipoConta);
  $arContasNew = array();
  foreach ($arContas as $conta) {
    if ($conta["cod_estrutural"] == $codEstrutural) continue;
    $arContasNew[] = $conta;
  }
  Sessao::write($tipoConta, $arContasNew);
  if ($tipoConta == CAM_GPC_TCEMG_DCASP_CONF_DESPESA) {
    $stJs  = montaListaDespesas();
  }
  elseif ($tipoConta == CAM_GPC_TCEMG_DCASP_CONF_RECEITA) {
    $stJs  = montaListaReceitas();
  }
  else {
    $stJs  = montaListaContabeis();
  }
  return $stJs;
}

function adicionarConta($request) {
	$grupo = ($request->get('inDescGrupo') != "" && $request->get('inDescGrupo') != NULL ? $request->get('inDescGrupo') : $request->get('grupo'));
	$nomeArquivo = ($request->get('stNomeArquivo') != "" && $request->get('stNomeArquivo') != NULL ? $request->get('stNomeArquivo') : $request->get('nome_arquivo'));
	if ($grupo == "") {
		return "alertaAviso('Informe um grupo de conta!','form','erro','" . Sessao::getId() .
			"');\n";
	}else if ($nomeArquivo == "") {
		return "alertaAviso('Informe o Nome do Campo!','form','erro','".Sessao::getId()."');\n";
	}

	$exercicio = Sessao::getExercicio();
	$tipoConta = $request->get('stTipoConta');

	//Lista de códigos cadastrados para cada entidade
	$TTCEMGConfiguracaoDCASPRegistro = new TTCEMGConfiguracaoDCASPRegistro;
	$rsContas = new RecordSet();
	if ($tipoConta == CAM_GPC_TCEMG_DCASP_CONF_DESPESA) {
		$TTCEMGConfiguracaoDCASPRegistro->recuperaContasOrcamentariasDespesa($rsContas, $exercicio,
			$grupo, "", "");
	}
	elseif ($tipoConta == CAM_GPC_TCEMG_DCASP_CONF_RECEITA) {
		$TTCEMGConfiguracaoDCASPRegistro->recuperaContasOrcamentariasReceita($rsContas,
			$exercicio, $grupo, "", "");
	}
	else {
		$TTCEMGConfiguracaoDCASPRegistro->recuperaContasContabeis($rsContas,
			$exercicio, $grupo, "", "");
	}
	if (count($rsContas->getElementos())<=0) {
		return "alertaAviso('Nenhuma conta encontrada!','form','erro','".Sessao::getId()."');\n";
	}
	carregaDadosSessao($tipoConta, $nomeArquivo, $rsContas);

	return montaListagemContas($tipoConta);
}

function adicionarRecurso($request) {
	if ($request->get("inCodRecurso")<=0) {
		return "alertaAviso('Nenhuma recurso encontrado!','form','erro','".Sessao::getId()."');\n";
	}
	$arRecursos = Sessao::read('arRecursos');
	$arRecurso = array(
		"cod_recurso" => $request->get("inCodRecurso"),
		"nom_recurso" => $request->get("stDescricaoRecurso")
	);
	if (!in_array($arRecurso, $arRecursos)) {
		$arRecursos[] = $arRecurso;
	}
	Sessao::write('arRecursos', $arRecursos);
	echo montaListaRecursos();
}

function removerRecurso($request) {
	$arRecursos = Sessao::read('arRecursos');
	$arRecursosNew = array();
	foreach ($arRecursos as $recurso) {
		if ($recurso["cod_recurso"] == $request->get("cod_recurso")) continue;
		$arRecursosNew[] = $recurso;
	}
	Sessao::write('arRecursos', $arRecursosNew);
	echo montaListaRecursos();
}

function montaListaRecursos () {

	$arRecursos = Sessao::read('arRecursos');
	if (count($arRecursos)<=0) {
		return;
	}
	$rsRecursos = new RecordSet();
	$rsRecursos->preenche($arRecursos);

	$obListaRecursos = new Lista();
	$obListaRecursos->setMostraPaginacao(false);
	$obListaRecursos->setTitulo('Lista de Recursos');
	$obListaRecursos->setRecordSet($rsRecursos);


	$obListaRecursos->addCabecalho('', 1);
	$obListaRecursos->addCabecalho('Id', 1);
	$obListaRecursos->addCabecalho('Recursos', 10);
	$obListaRecursos->addCabecalho('Excluir', 1);

	$obListaRecursos->addDado();
	$obListaRecursos->ultimoDado->setCampo( "cod_recurso" );
	$obListaRecursos->ultimoDado->setAlinhamento( 'CENTRO' );
	$obListaRecursos->commitDado();

	$obListaRecursos->addDado();
	$obListaRecursos->ultimoDado->setCampo( "nom_recurso" );
	$obListaRecursos->ultimoDado->setAlinhamento( 'ESQUERDA' );
	$obListaRecursos->commitDado();


	$obListaRecursos->addAcao();
	$obListaRecursos->ultimaAcao->setAcao("EXCLUIR");
	$obListaRecursos->ultimaAcao->setFuncaoAjax(true);
	$obListaRecursos->ultimaAcao->setLink("JavaScript:executaFuncaoAjax('removerRecurso')");
	$obListaRecursos->ultimaAcao->addCampo("1", "cod_recurso");
	$obListaRecursos->ultimaAcao->addCampo("2", "nom_recurso");
	$obListaRecursos->commitAcao();

	$obListaRecursos->montaInnerHTML();

	$stHTML = $obListaRecursos->getHTML();
	$stJs  = "jQuery('#spnRecurso').html('');";
	$stJs .= "jQuery('#spnRecurso').html('" . $stHTML . "');";
	return $stJs;
}

function montaListaReceitas () {
	$stJs  = "jQuery('#spnReceita').html('');";
	$tipoConta = CAM_GPC_TCEMG_DCASP_CONF_RECEITA;
	$contas = Sessao::read($tipoConta);
	if (count($contas)<=0) {
		return $stJs;
	}
	$rsContas = new RecordSet();
	$rsContas->preenche($contas);


	$obListaReceita = new Lista();
	$obListaReceita->setMostraPaginacao(false);
	$obListaReceita->setTitulo('Lista de Contas Orçamentárias de Receita');
	$obListaReceita->setRecordSet($rsContas);

	if ($rsContas->getElementos() != "") {
		$obListaReceita->addCabecalho('', 1);
		$obListaReceita->addCabecalho('Contas Orçamentárias', 10);
		$obListaReceita->addCabecalho('Excluir', 1);

		$obListaReceita->addDado();
		$obListaReceita->ultimoDado->setAlinhamento('ESQUERDA');
		$obListaReceita->ultimoDado->setCampo('[cod_estrutural] - [descricao]');
		$obListaReceita->commitDadoComponente();

		$obListaReceita->addAcao();
		$obListaReceita->ultimaAcao->setAcao("EXCLUIR");
		$obListaReceita->ultimaAcao->setFuncaoAjax(true);
		$obListaReceita->ultimaAcao->setLink("JavaScript:executaFuncaoAjax('removerConta')");
		$obListaReceita->ultimaAcao->addCampo("cod_estrutural", "cod_estrutural");
		$obListaReceita->ultimaAcao->addCampo("tipo_conta", "tipo_conta");
		$obListaReceita->commitAcao();
	}

	$obListaReceita->montaInnerHTML();
	$stHTML = $obListaReceita->getHTML();
	$stJs .= "jQuery('#spnReceita').html('" . $stHTML . "');";
	return $stJs;
}

function montaListaDespesas () {
	$stJs  = "jQuery('#spnDespesa').html('');";
	$tipoConta = CAM_GPC_TCEMG_DCASP_CONF_DESPESA;
	$contas = Sessao::read($tipoConta);

	if (count($contas)<=0) {
		return $stJs;
	}
	$rsContas = new RecordSet();
	$rsContas->preenche($contas);

	$obListaDespesa = new Lista();
	$obListaDespesa->setMostraPaginacao(false);
	$obListaDespesa->setTitulo('Lista de Contas Orçamentárias de Despesas');
	$obListaDespesa->setRecordSet($rsContas);

	$obListaDespesa->addCabecalho('', 1);
	$obListaDespesa->addCabecalho('Contas Orçamentárias', 10);
	$obListaDespesa->addCabecalho('Excluir', 1);

	$obListaDespesa->addDado();
	$obListaDespesa->ultimoDado->setAlinhamento('ESQUERDA');
	$obListaDespesa->ultimoDado->setCampo('[cod_estrutural] - [descricao]');
	$obListaDespesa->commitDadoComponente();

	$obListaDespesa->addAcao();
	$obListaDespesa->ultimaAcao->setAcao("EXCLUIR");
	$obListaDespesa->ultimaAcao->setFuncaoAjax(true);
	$obListaDespesa->ultimaAcao->setLink("JavaScript:executaFuncaoAjax('removerConta')");
	$obListaDespesa->ultimaAcao->addCampo("cod_estrutural", "cod_estrutural");
	$obListaDespesa->ultimaAcao->addCampo("tipo_conta", "tipo_conta");
	$obListaDespesa->commitAcao();


	$obListaDespesa->montaInnerHTML();
	$stHTML = $obListaDespesa->getHTML();

	$stJs .= "jQuery('#spnDespesa').html('" . $stHTML . "');";
	return $stJs;
}

function montaListaContabeis () {
	$stJs  = "jQuery('#spnContabil').html('');";
	$tipoConta = CAM_GPC_TCEMG_DCASP_CONF_CONTABIL;
	$contas = Sessao::read($tipoConta);
	if (count($contas)<=0) {
		return $stJs;
	}
	$rsContas = new RecordSet();
	$rsContas->preenche($contas);

	$obLista = new Lista();
	$obLista->setMostraPaginacao(false);
	$obLista->setTitulo('Lista de Contas Contábeis');
	$obLista->setRecordSet($rsContas);

	$obLista->addCabecalho('', 1);
	$obLista->addCabecalho('Contas Contábeis', 10);
	$obLista->addCabecalho('Excluir', 1);

	$obLista->addDado();
	$obLista->ultimoDado->setAlinhamento('ESQUERDA');
	$obLista->ultimoDado->setCampo('[cod_estrutural] - [descricao]');
	$obLista->commitDadoComponente();

	$obLista->addAcao();
	$obLista->ultimaAcao->setAcao("EXCLUIR");
	$obLista->ultimaAcao->setFuncaoAjax(true);
	$obLista->ultimaAcao->setLink("JavaScript:executaFuncaoAjax('removerConta')");
	$obLista->ultimaAcao->addCampo("cod_estrutural", "cod_estrutural");
	$obLista->ultimaAcao->addCampo("tipo_conta", "tipo_conta");
	$obLista->commitAcao();

	$obLista->montaInnerHTML();
	$stHTML = $obLista->getHTML();

	$stJs .= "jQuery('#spnContabil').html('" . $stHTML . "');";
	return $stJs;
}


function montaListagemContas($tipoConta) {

	if ($tipoConta == CAM_GPC_TCEMG_DCASP_CONF_DESPESA) {
		$stJs  = montaListaDespesas();
	}
	elseif ($tipoConta == CAM_GPC_TCEMG_DCASP_CONF_RECEITA) {
		$stJs  = montaListaReceitas();
	}
	else {
		$stJs  = montaListaContabeis();
	}
	return $stJs;
}

function carregaDadosSessao($tipoConta, $nomeArquivo, $rsContas) {

	$contas = Sessao::read($tipoConta);
	foreach ($rsContas->getElementos() as $key => $dado) {
		$arrConta = array(
		    "cod_conta"      => $dado['cod_conta'],
		    "cod_estrutural" => $dado['cod_estrutural'],
		    "descricao"      => $dado['descricao'],
		    "nome_arquivo"   => $nomeArquivo,
		    "tipo_conta"     => $tipoConta
		);
		if (!in_array($arrConta, $contas)) {
			$contas[] = $arrConta;
		}
	}
	Sessao::write($tipoConta, $contas);
}

function carregaDadosSessaoRecurso($rsRecurso) {

	$arRecursos = Sessao::read('arRecursos');
	foreach ($rsRecurso->getElementos() as $key => $dado) {
		$arRecurso = array(
			"cod_recurso" => $dado['cod_recurso'],
			"nom_recurso" => $dado['nom_recurso'],
		);
		if (!in_array($arRecurso, $arRecursos)) {
			$arRecursos[] = $arRecurso;
		}
	}
	Sessao::write('arRecursos', $arRecursos);


}
function carregaDados ($request) {

	Sessao::write(CAM_GPC_TCEMG_DCASP_CONF_DESPESA, array());
	Sessao::write(CAM_GPC_TCEMG_DCASP_CONF_RECEITA, array());
	Sessao::write(CAM_GPC_TCEMG_DCASP_CONF_CONTABIL, array());
	Sessao::write('arRecursos',array());
	$tipoConta = $request->get('stTipoConta');
	$nomeArquivo = ($request->get('stNomeArquivo') != "" && $request->get('stNomeArquivo') != NULL ? $request->get('stNomeArquivo') : $request->get('nome_arquivo'));
	$nomeCampo = $request->get('inCodCampo');
	if ($nomeArquivo=="" || $nomeCampo =="" || $tipoConta =="" ) {
		return;
	}

	$exercicio = Sessao::getExercicio();

	//Lista de códigos cadastrados para cada entidade
	$TTCEMGConfiguracaoDCASPRegistro = new TTCEMGConfiguracaoDCASPRegistro;
	$rsContaDespesa = new RecordSet();
	$rsContaReceita = new RecordSet();
	$rsContaContabil = new RecordSet();
	$rsRecurso = new RecordSet();
	$TTCEMGConfiguracaoDCASPRegistro->recuperaContasOrcamentariasDespesa($rsContaDespesa, $exercicio,
		"", $nomeArquivo, $nomeCampo);
	carregaDadosSessao(CAM_GPC_TCEMG_DCASP_CONF_DESPESA, $nomeArquivo, $rsContaDespesa);

	$TTCEMGConfiguracaoDCASPRegistro->recuperaContasOrcamentariasReceita($rsContaReceita, $exercicio,
		"", $nomeArquivo, $nomeCampo);
	carregaDadosSessao(CAM_GPC_TCEMG_DCASP_CONF_RECEITA, $nomeArquivo, $rsContaReceita);

	$TTCEMGConfiguracaoDCASPRegistro->recuperaContasContabeis($rsContaContabil, $exercicio, "",
		$nomeArquivo, $nomeCampo);
	carregaDadosSessao(CAM_GPC_TCEMG_DCASP_CONF_CONTABIL, $nomeArquivo, $rsContaContabil);


	$TTCEMGConfiguracaoDCASPRecurso = new TTCEMGConfiguracaoDCASPRecurso;
	$TTCEMGConfiguracaoDCASPRecurso->recuperaRecurso($rsRecurso, $exercicio, $nomeArquivo,
		$nomeCampo);
	carregaDadosSessaoRecurso($rsRecurso);

	$stJs = montaListagemContas(CAM_GPC_TCEMG_DCASP_CONF_DESPESA, $nomeArquivo);
	$stJs.= montaListagemContas(CAM_GPC_TCEMG_DCASP_CONF_RECEITA, $nomeArquivo);
	$stJs.= montaListagemContas(CAM_GPC_TCEMG_DCASP_CONF_CONTABIL, $nomeArquivo);
	$stJs.= montaListaRecursos();

	return $stJs;
}
// Acoes por pagina
switch ($stCtrl) {
  case "carregaDados":
    $stJs = carregaDados($request);
  break;
  case "adicionarConta":
	$stJs = adicionarConta($request);
	break;
  case "removerConta":
	$stJs = removerConta($request);
	break;
  case "adicionarRecurso":
	$stJs = adicionarRecurso($request);
	break;
  case "removerRecurso":
	$stJs = removerRecurso($request);
	break;
}

if ($stJs) {
  echo $stJs;
}
