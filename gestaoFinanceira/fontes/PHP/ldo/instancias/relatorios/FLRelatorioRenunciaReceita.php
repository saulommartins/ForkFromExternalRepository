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
 * Página de filtro de Estimativa e Compensação da Renúncia de Receita
 * Data de Criação: 23/03/2009
 *
 *
 * @author Marcio Medeiros <marcio.medeiros>
 * @package GF
 * @subpackage LDO
 * @uc uc-02.10.17 - Estimativa e Compensação da Renúncia de Receita
 */
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_LDO_COMPONENTES . 'ISelectLDO.class.php';
include_once CAM_GF_LDO_COMPONENTES . 'ISelectNotaExplicativa.class.php';
require_once CAM_GA_ADM_COMPONENTES . 'IMontaAssinaturas.class.php';
include_once CAM_GF_LDO_UTIL        . 'LDORelatorio.class.php';

$stAcao = $_GET['stAcao'] ? $_GET['stAcao'] : $_POST['stAcao'];

$pgProc = 'PRRelatorioRenunciaReceita.php';

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
$obHdnCtrl->setValue($stAcao);
$obFormulario->addHidden($obHdnCtrl);

$obHdnAcaoAnexo = new Hidden();
$obHdnAcaoAnexo->setName('inCodAcaoAnexo');
$obHdnAcaoAnexo->setValue($_REQUEST['acao']);
$obFormulario->addHidden($obHdnAcaoAnexo);

// Enditade
$stEntidades = LDORelatorio::montarEntidades();
$obHdnCodEntidade = new Hidden();
$obHdnCodEntidade->setName('inCodEntidade');
$obHdnCodEntidade->setId('inCodEntidade');
$obHdnCodEntidade->setValue($stEntidades);
$obFormulario->addHidden($obHdnCodEntidade);

// LDO
$obISelectLDO = new ISelectLDO();
$obISelectLDO->setVigenciaPPA(true);
$obISelectLDO->setObrigatorio(true);
if (isset($_REQUEST['inAnoLDO'])) {
    $obISelectLDO->setValue($_REQUEST['inAnoLDO']);
}
$obFormulario->addComponente($obISelectLDO);
// Nota Explicativa
$obISelectNotaExplicativa = new ISelectNotaExplicativa();
$obISelectNotaExplicativa->setObrigatorio(true);
if (isset($_REQUEST['inCodNotaExplicativa'])) {
    $obISelectNotaExplicativa->setValue($_REQUEST['inCodNotaExplicativa']);
}
$obFormulario->addComponente($obISelectNotaExplicativa);
// Assinaturas
$obMontaAssinaturas = new IMontaAssinaturas();
$obMontaAssinaturas->geraFormulario($obFormulario);

$obFormulario->ok();

$obFormulario->show();
?>
