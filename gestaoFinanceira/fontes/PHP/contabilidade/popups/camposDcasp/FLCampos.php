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
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once(CAM_GF_CONT_NEGOCIO . "RContabilidadePlanoConta.class.php");

$stPrograma = "Campos";
$pgFilt = "FL" . $stPrograma . ".php";
$pgForm = "FM" . $stPrograma . ".php";
$pgProc = "PR" . $stPrograma . ".php";
$pgOcul = "OC" . $stPrograma . ".php";
$pgList = "LS" . $stPrograma . ".php";
$pgJs = "JS" . $stPrograma . ".js";

Sessao::remove('linkPopUp');

$obForm = new Form;
$obForm->setAction($pgList);
$obForm->setTarget("");

$obHdnForm = new Hidden;
$obHdnForm->setName("nomForm");
$obHdnForm->setValue($_REQUEST['nomForm']);

$obHdnCampoNum = new Hidden;
$obHdnCampoNum->setName("campoNum");
$obHdnCampoNum->setValue($request->get('campoNum'));

$obHdnCampoNom = new Hidden;
$obHdnCampoNom->setName("campoNom");
$obHdnCampoNom->setValue($request->get('campoNom'));

$obHdnCampoTpReg = new Hidden;
$obHdnCampoTpReg->setName("campoTpReg");
$obHdnCampoTpReg->setValue($request->get('campoTpReg'));

$obHdnCampoCod = new Hidden;
$obHdnCampoCod->setName("campoCod");
$obHdnCampoCod->setValue($request->get('campoCod'));

$obHdnCampoSeqArquivo = new Hidden;
$obHdnCampoSeqArquivo->setName("campoSeq");
$obHdnCampoSeqArquivo->setValue($request->get('campoSeq'));

$obTxtNomeArquivo = new TextBox;
$obTxtNomeArquivo->setName("stNomeArquivo");
$obTxtNomeArquivo->setRotulo("Nome do Arquivo");
$obTxtNomeArquivo->setNull(true);
$obTxtNomeArquivo->setLabel(true);
$obTxtNomeArquivo->setValue($request->get('tipoBusca'));

$obTxtNomeCampo = new TextBox;
$obTxtNomeCampo->setName("stNomeCampo");
$obTxtNomeCampo->setRotulo("Nome do Campo");
$obTxtNomeCampo->setTitle("Nome do campo do DCASP");
$obTxtNomeCampo->setSize(50);
$obTxtNomeCampo->setMaxLength(100);
$obTxtNomeCampo->setNull(true);

$obTxtNomeTag = new TextBox;
$obTxtNomeTag->setName("stNomeTag");
$obTxtNomeTag->setRotulo("Nome da Tag");
$obTxtNomeTag->setTitle("Nome da Tag do DCASP");
$obTxtNomeTag->setSize(30);
$obTxtNomeTag->setMaxLength(100);
$obTxtNomeTag->setNull(true);

$stContaAtivo = Sessao::read('stContaAtivo');

if (isset($stContaAtivo) && !empty($stContaAtivo)) {
  $obTxtCodEstrutural->setValue($stContaAtivo);
  $obTxtCodEstrutural->setReadOnly(true);
}

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addTitulo("Dados para Filtro");
$obFormulario->addForm($obForm);
$obFormulario->addHidden($obHdnForm);
$obFormulario->addHidden($obHdnCampoNum);
$obFormulario->addHidden($obHdnCampoNom);
$obFormulario->addHidden($obHdnCampoTpReg);
$obFormulario->addHidden($obHdnCampoCod);
$obFormulario->addHidden($obHdnCampoSeqArquivo);
$obFormulario->addComponente($obTxtNomeArquivo);
$obFormulario->addComponente($obTxtNomeCampo);
$obFormulario->addComponente($obTxtNomeTag);
$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
