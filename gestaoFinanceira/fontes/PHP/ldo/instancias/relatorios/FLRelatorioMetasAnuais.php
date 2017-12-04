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
 * Página de Filtro do 02.10.07 - Emitir Metas Anuais
 * Data de Criação: 13/03/2009
 * Copyright CNM - Confederação Nacional de Municípios
 *
 * @author Fellipe Esteves dos Santos <fellipe.santos>
 * @package gestaoFinanceira
 * @subpackage LDO
 * @uc 02.10.07 - Emitir Metas Anuais
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_LDO_COMPONENTES . 'ISelectLDO.class.php';
include_once CAM_GF_LDO_COMPONENTES . 'ISelectNotaExplicativa.class.php';
include_once CAM_GF_ORC_COMPONENTES . 'ITextBoxSelectEntidadeGeral.class.php';
include_once CAM_GA_ADM_COMPONENTES . 'IMontaAssinaturas.class.php';

$stAcao = $_GET['stAcao'] ? $_GET['stAcao'] : $_POST['stAcao'];

$stPrograma = 'RelatorioMetasAnuais';
$pgFilt     = 'FL' . $stPrograma . '.php';
$pgProc     = 'PR' . $stPrograma . '.php';
$pgJs       = 'JS' . $stPrograma . '.php';

$obForm = new Form();
$obForm->setAction($pgProc);
$obForm->setTarget('oculto');

$obFormulario = new Formulario();
$obFormulario->addForm($obForm);
$obFormulario->addTitulo('Dados para Filtro');

$obHdnAcao = new Hidden();
$obHdnAcao->setName('stAcao');
$obHdnAcao->setValue($stAcao);
$obFormulario->addHidden($obHdnAcao);

$obHdnCtrl = new Hidden();
$obHdnCtrl->setName('stCtrl');
$obHdnCtrl->setValue($stCtrl);
$obFormulario->addHidden($obHdnCtrl);

$obHdnEntidade = new Hidden();
$obHdnEntidade->setName('inCodEntidade');
$obHdnEntidade->setValue(true);
$obFormulario->addHidden($obHdnEntidade);

$obISelectLDO = new ISelectLDO();
$obISelectLDO->setNull(false);
$obFormulario->addComponente($obISelectLDO);

$obITextBoxSelectEntidade = new ITextBoxSelectEntidadeGeral();
$obITextBoxSelectEntidade->setNull(true);
$obITextBoxSelectEntidade->setCodEntidade($_REQUEST['inCodEntidade']);
$obITextBoxSelectEntidade->obTextBox->obEvento->setOnChange("javascript:montarEntidadesAssinaturas('stIncluirAssinaturasSim');");
$obITextBoxSelectEntidade->obSelect->obEvento->setOnChange("javascript:montarEntidadesAssinaturas('stIncluirAssinaturasSim');");
$obFormulario->addComponente($obITextBoxSelectEntidade);

$obISelectNotaExplicativa = new ISelectNotaExplicativa();
$obISelectNotaExplicativa->setNull(false);
$obFormulario->addComponente($obISelectNotaExplicativa);

$obMontaAssinaturas = new IMontaAssinaturas();
$obMontaAssinaturas->geraFormulario($obFormulario);

$obFormulario->ok();

$obFormulario->show();

include_once($pgJs);

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
