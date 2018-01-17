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
include_once CAM_GF_CONT_NEGOCIO . "RContabilidadePlanoContaAnalitica.class.php";
include_once CAM_GF_CONT_NEGOCIO . "RContabilidadePlanoBanco.class.php";
include_once CAM_GF_CONT_MAPEAMENTO . "TCampos.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "Campos";
$pgFilt = "FL" . $stPrograma . ".php";
$pgList = "LS" . $stPrograma . ".php";
$pgForm = "FM" . $stPrograma . ".php";
$pgProc = "PR" . $stPrograma . ".php";
$pgOcul = "OC" . $stPrograma . ".php";
$pgJS = "JS" . $stPrograma . ".js";
$pgCons = $pgFilt;

include_once($pgJS);

//Monta sessao com os valores do filtro
if (is_array(Sessao::read('linkPopUp'))) {
  $request = new Request(Sessao::read('linkPopUp'));
} else {
  $arLinkPopUp = array();
  foreach ($request->getAll() as $key => $valor) {
      $arLinkPopUp[$key] = $valor;
  }
  Sessao::write('linkPopUp', $arLinkPopUp);
}

$arRequest = $request->getAll();

$stLink = '';
if ($arRequest['stNomeCampo']) {
  $stLink.= '&stNomeCampo=' . $arRequest['stNomeCampo'];
}
if ($arRequest['stNomeTag']) {
  $stLink.= '&stNomeTag=' . $arRequest['stNomeTag'];
}
if ($arRequest['stNomeArquivo']) {
  $stLink.= '&stNomeArquivo=' . $arRequest['stNomeArquivo'];
}
$stLink.= "&campoNum=" . $arRequest['campoNum'];
$stLink.= "&campoNom=" . $arRequest['campoNom'];
$stLink.= "&nomForm=" . $arRequest['nomForm'];
$stLink.= "&campoTpReg=" . $arRequest['campoTpReg'];
$stLink.= "&campoCod=" . $arRequest['campoCod'];
$stLink.= "&campoSeq=" . $arRequest['campoSeq'];

$obTCampos = new Tcampos;
$obErro = $obTCampos->recuperaRegistros($rsLista, Sessao::getExercicio(), $arRequest['stNomeCampo'], $arRequest['stNomeTag'], $arRequest['stNomeArquivo']);

$obLista = new Lista;
$obLista->obPaginacao->setFiltro("&stLink=" . $stLink);
$obLista->setRecordSet($rsLista);

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth(5);
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Nome da Tag");
$obLista->ultimoCabecalho->setWidth(25);
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Nome do Campo");
$obLista->ultimoCabecalho->setWidth(40);
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Arquivo Pertencente");
$obLista->ultimoCabecalho->setWidth(10);
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth(5);
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo("nome_tag");
$obLista->ultimoDado->setAlinhamento('ESQUERDA');
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo("nome_campo");
$obLista->ultimoDado->setAlinhamento('ESQUERDA');
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo("nome_arquivo_pertencente");
$obLista->ultimoDado->setAlinhamento('ESQUERDA');
$obLista->commitDado();

$stAcao = "SELECIONAR";
$obLista->addAcao();
$obLista->ultimaAcao->setAcao($stAcao);
$obLista->ultimaAcao->setFuncao(true);
$obLista->ultimaAcao->setLink("JavaScript:insere();");
$obLista->ultimaAcao->addCampo("1", "nome_tag");
$obLista->ultimaAcao->addCampo("2", "nome_campo");
$obLista->ultimaAcao->addCampo("3", "tipo_registro");
$obLista->ultimaAcao->addCampo("4", "cod_arquivo");
$obLista->ultimaAcao->addCampo("5", "seq_arquivo");

$obLista->commitAcao();
$obLista->show();
