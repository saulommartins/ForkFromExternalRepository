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
 * Página de Formulário do 02.10.03 - Manter Ação
 * Data de Criação: 10/03/2009
 * Copyright CNM - Confederação Nacional de Municípios
 *
 * @author Fellipe Esteves dos Santos <fellipe.santos>
 * @package GF
 * @subpackage LDO
 * @uc 02.10.03 - Manter Ação
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/Table.class.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GA_NORMAS_COMPONENTES . 'IPopUpNorma.class.php';

$stAcao = $_GET['stAcao'] ? $_GET['stAcao'] : $_POST['stAcao'];

$stPrograma = "ManterAcao";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".php";

include_once $pgJS;

$obForm = new Form;
$obForm->setAction($pgProc);
$obForm->setTarget('oculto');

$obFormulario = new Formulario();
$obFormulario->addForm($obForm);

$obHdnAcao = new Hidden();
$obHdnAcao->setName('stAcao');
$obHdnAcao->setValue($stAcao);
$obFormulario->addHidden($obHdnAcao);

$obHdnCtrl = new Hidden();
$obHdnCtrl->setName('stCtrl');
$obHdnCtrl->setValue($stCtrl);
$obFormulario->addHidden($obHdnCtrl);

$obFormulario->addTitulo('Dados para Exclusão');

$obIPopUpNorma = new IPopUpNorma();
$obIPopUpNorma->obInnerNorma->setTitle('Define norma de exclusão.');
$obIPopUpNorma->obInnerNorma->obCampoCod->stId = 'inCodNorma';
$obIPopUpNorma->obInnerNorma->setRotulo('Norma');
$obIPopUpNorma->obLblDataNorma->setRotulo('Data da Norma');
$obIPopUpNorma->setExibeDataNorma(true);
$obIPopUpNorma->setExibeDataPublicacao(true);
$obIPopUpNorma->geraFormulario($obFormulario);

$obHdnCodAcao = new Hidden();
$obHdnCodAcao->setName('inCodAcao');
$obHdnCodAcao->setValue($_REQUEST['inCodAcao']);
$obFormulario->addHidden($obHdnCodAcao);

$obHdnCodAcaoPPA = new Hidden();
$obHdnCodAcaoPPA->setName('inNumAcao');
$obHdnCodAcaoPPA->setValue($_REQUEST['inNumAcao']);
$obFormulario->addHidden($obHdnCodAcaoPPA);

$obHdnCodAcaoPPA = new Hidden();
$obHdnCodAcaoPPA->setName('inCodAcaoPPA');
$obHdnCodAcaoPPA->setValue($_REQUEST["inCodAcaoPPA"]);
$obFormulario->addHidden($obHdnCodAcaoPPA);

$obHdnAnoLDO = new Hidden();
$obHdnAnoLDO->setName('stAno');
$obHdnAnoLDO->setValue($_REQUEST['stAno']);
$obFormulario->addHidden($obHdnAnoLDO);

$stJs ="if (validarNorma()) confirmPopUp('Exlusão de Ação do LDO', 'Deseja realmente exluir esta ação (". $_REQUEST['stDescQuestao'] .")?', 'document.forms[0].submit()');";

$obBtnOK = new OK();
$obBtnOK->obEvento->setOnClick($stJs);

$obBtnCancelar = new Cancelar();
$obBtnCancelar->obEvento->setOnClick('cancelarAcao();');

$arBotoes = array($obBtnOK, $obBtnCancelar);

$obFormulario->defineBarra($arBotoes);

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
