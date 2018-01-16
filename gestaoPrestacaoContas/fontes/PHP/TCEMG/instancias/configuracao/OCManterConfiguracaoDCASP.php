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
include_once CAM_GPC_TCEMG_MAPEAMENTO . 'TTCEMGCampoContaCorrente.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoDCASP";
$pgFilt = "FL" . $stPrograma . ".php";
$pgList = "LS" . $stPrograma . ".php";
$pgForm = "FM" . $stPrograma . ".php";
$pgProc = "PR" . $stPrograma . ".php";
$pgOcul = "OC" . $stPrograma . ".php";
$pgJs = "JS" . $stPrograma . ".js";

$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];

function montaListagem() {
  global $request;

  Sessao::write('tipoRegistro', $request->get('t­i­p­o­R­e­g­i­s­t­r­o­'));
  Sessao::write('codSequencialCampo', $request->get('c­o­d­A­r­q­u­i­v­o­'));

  Sessao::write('contas', array());
  Sessao::write('contasExcluidas', array());

  return montaListagemContas();
}

function montaListagemContas() {
  global $request;
  $stJs = "jQuery('#spnContas').html('');";

  $nomeArquivo = (!empty($request->get('stNomeArquivo')) && $request->get('stNomeArquivo') != NULL ? $request->get('stNomeArquivo') : $request->get('nome_arquivo'));
  $grupo = (!empty($request->get('inDescGrupo')) && $request->get('inDescGrupo') != NULL ? $request->get('inDescGrupo') : $request->get('grupo'));
  $exercicio = Sessao::getExercicio();
  $boTransacao = new Transacao();
  $tipoConta = (!empty($request->get('stTipoConta')) && $request->get('stTipoConta') != NULL ? $request->get('stTipoConta') : $request->get('tipo_conta'));

  //Lista de códigos cadastrados para cada entidade
  $TTCEMGCampoContaCorrente = new TTCEMGCampoContaCorrente;
  if ($tipoConta == 'OrcamentariaDespesa') {
    $contasOrcDespesaExcluidas = Sessao::read('contasExcluidas');
    $excluidasDespesa = (!empty($contasOrcDespesaExcluidas) ? $contasOrcDespesaExcluidas : '');

    $TTCEMGCampoContaCorrente->recuperaContasOrcamentariasDespesa($rsContaOrcDespesa, $exercicio, $grupo, $nomeArquivo, $excluidasDespesa, $boTransacao);

    $obListaDespesa = new Lista();
    $obListaDespesa->setMostraPaginacao(false);
    $obListaDespesa->setTitulo('Lista de Contas Orçamentárias de Despesas');
    $obListaDespesa->setRecordSet($rsContaOrcDespesa);

    if (!empty($rsContaOrcDespesa->getElementos())) {
      $arrConta = array();
      foreach ($rsContaOrcDespesa->getElementos() as $key => $dado) {
        $arrConta[$dado['cod_conta']]['cod_conta'] = $dado['cod_conta'];
        $arrConta[$dado['cod_conta']]['conta_orc_despesa'] = $dado['cod_estrutural'];
        $arrConta[$dado['cod_conta']]['nom_conta'] = $dado['descricao'];
        $arrConta[$dado['cod_conta']]['exercicio'] = $dado['exercicio'];
        $arrConta[$dado['cod_conta']]['grupo'] = $dado['grupo'];
        $arrConta[$dado['cod_conta']]['nome_arquivo'] = $dado['nome_arquivo'];
        $arrConta[$dado['cod_conta']]['tipo_conta'] = 'Despesa';
      }
      Sessao::write('contas', $arrConta);

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
      $obListaDespesa->ultimaAcao->addCampo("1", "cod_conta");
      $obListaDespesa->commitAcao();

      $obListaDespesa->ultimaAcao->addCampo('&exercicio', 'exercicio');
      $obListaDespesa->ultimaAcao->addCampo("&grupo", 'grupo');
      $obListaDespesa->ultimaAcao->addCampo('&nome_arquivo', 'nome_arquivo');
      $obListaDespesa->ultimaAcao->addCampo('&tipo_conta', 'tipo_conta');
    }

    $obListaDespesa->montaInnerHTML();
    $stHTML = $obListaDespesa->getHTML();
    $stJs.= "jQuery('#spnContas').html('" . $stHTML . "');";
  }
  elseif ($tipoConta == 'OrcamentariaReceita') {
    $contasOrcReceitaExcluidas = Sessao::read('contasExcluidas');
    $excluidasReceita = (!empty($contasOrcReceitaExcluidas) ? $contasOrcReceitaExcluidas : '');

    $TTCEMGCampoContaCorrente->recuperaContasOrcamentariasReceita($rsContaOrcReceita, $exercicio, $grupo, $nomeArquivo, $excluidasReceita, $boTransacao);

    $obListaReceita = new Lista();
    $obListaReceita->setMostraPaginacao(false);
    $obListaReceita->setTitulo('Lista de Contas Orçamentárias de Receitas');
    $obListaReceita->setRecordSet($rsContaOrcReceita);

    if (!empty($rsContaOrcReceita->getElementos())) {
      $arrConta = array();
      foreach ($rsContaOrcReceita->getElementos() as $key => $dado) {
        $arrConta[$dado['cod_conta']]['cod_conta'] = $dado['cod_conta'];
        $arrConta[$dado['cod_conta']]['conta_orc_receita'] = $dado['cod_estrutural'];
        $arrConta[$dado['cod_conta']]['nom_conta'] = $dado['descricao'];
        $arrConta[$dado['cod_conta']]['exercicio'] = $dado['exercicio'];
        $arrConta[$dado['cod_conta']]['grupo'] = $dado['grupo'];
        $arrConta[$dado['cod_conta']]['nome_arquivo'] = $dado['nome_arquivo'];
        $arrConta[$dado['cod_conta']]['tipo_conta'] = 'Receita';
      }
      Sessao::write('contas', $arrConta);

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
      $obListaReceita->ultimaAcao->addCampo("1", "cod_conta");
      $obListaReceita->commitAcao();

      $obListaReceita->ultimaAcao->addCampo('&exercicio', 'exercicio');
      $obListaReceita->ultimaAcao->addCampo("&grupo", 'grupo');
      $obListaReceita->ultimaAcao->addCampo('&nome_arquivo', 'nome_arquivo');
      $obListaReceita->ultimaAcao->addCampo('&tipo_conta', 'tipo_conta');
    }

    $obListaReceita->montaInnerHTML();
    $stHTML = $obListaReceita->getHTML();
    $stJs.= "jQuery('#spnContas').html('" . $stHTML . "');";
  }
  else {
    $contasContabeisExcluidas = Sessao::read('contasExcluidas');
    $excluidas = (!empty($contasContabeisExcluidas) ? $contasContabeisExcluidas : '');

    $TTCEMGCampoContaCorrente->recuperaContasContabeis($rsContaContabil, $exercicio, $grupo, $nomeArquivo, $excluidas, $boTransacao);

    $obLista = new Lista();
    $obLista->setMostraPaginacao(false);
    $obLista->setTitulo('Lista de Contas Contábeis');
    $obLista->setRecordSet($rsContaContabil);

    if (!empty($rsContaContabil->getElementos())) {
      $arrConta = array();
      foreach ($rsContaContabil->getElementos() as $key => $dado) {
        $arrConta[$dado['cod_conta']]['cod_conta'] = $dado['cod_conta'];
        $arrConta[$dado['cod_conta']]['conta_contabil'] = $dado['cod_estrutural'];
        $arrConta[$dado['cod_conta']]['nom_conta'] = $dado['nom_conta'];
        $arrConta[$dado['cod_conta']]['exercicio'] = $dado['exercicio'];
        $arrConta[$dado['cod_conta']]['grupo'] = $dado['grupo'];
        $arrConta[$dado['cod_conta']]['nome_arquivo'] = $dado['nome_arquivo'];
        $arrConta[$dado['cod_conta']]['tipo_conta'] = 'Contábil';
      }
      Sessao::write('contas', $arrConta);

      $obLista->addCabecalho('', 1);
      $obLista->addCabecalho('Contas Contábeis', 10);
      $obLista->addCabecalho('Excluir', 1);

      $obLista->addDado();
      $obLista->ultimoDado->setAlinhamento('ESQUERDA');
      $obLista->ultimoDado->setCampo('[cod_estrutural] - [nom_conta]');
      $obLista->commitDadoComponente();

      $obLista->addAcao();
      $obLista->ultimaAcao->setAcao("EXCLUIR");
      $obLista->ultimaAcao->setFuncaoAjax(true);
      $obLista->ultimaAcao->setLink("JavaScript:executaFuncaoAjax('removerConta')");
      $obLista->ultimaAcao->addCampo("1", "cod_conta");
      $obLista->commitAcao();

      $obLista->ultimaAcao->addCampo('&exercicio', 'exercicio');
      $obLista->ultimaAcao->addCampo("&grupo", 'grupo');
      $obLista->ultimaAcao->addCampo('&nome_arquivo', 'nome_arquivo');
      $obLista->ultimaAcao->addCampo('&tipo_conta', 'tipo_conta');
    }

    $obLista->montaInnerHTML();
    $stHTML = $obLista->getHTML();
    $stJs.= "jQuery('#spnContas').html('" . $stHTML . "');";
  }

  return $stJs;
}

function removerConta() {
  global $request;

  $codConta = $request->get('cod_conta');
  $contas = Sessao::read('contasExcluidas');
  $contas[] = $codConta;
  Sessao::write('contasExcluidas', $contas);

  return montaListagemContas();
}

// Acoes por pagina
switch ($stCtrl) {
  case "montaListagem":
    $stJs = montaListagem();
  break;
  case "montaListagemContas":
    $stJs = montaListagemContas();
  break;
  case "removerConta":
    $stJs = removerConta();
  break;
}

if ($stJs) {
  echo $stJs;
}
