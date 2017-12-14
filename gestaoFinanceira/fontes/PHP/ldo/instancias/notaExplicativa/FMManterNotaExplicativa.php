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
 * Página de Formulário de Nota Explicativa
 * Data de Criação: 16/02/2009
 *
 *
 * @author Marcio Medeiros <marcio.medeiros>
 * @package GF
 * @subpackage LDO
 * @uc uc-02.10.05 - Manter Ajuste de Anexo
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

include_once CAM_GF_LDO_VISAO . 'VLDOManterNotaExplicativa.class.php';
include_once CAM_GF_LDO_COMPONENTES . 'ISelectLDO.class.php';
//include_once CAM_GF_ORC_COMPONENTES . 'ISelectMultiploEntidadeGeral.class.php';
include_once CAM_GF_ORC_COMPONENTES . 'ITextBoxSelectEntidadeGeral.class.php';
include_once CAM_GF_ORC_COMPONENTES . 'ISelectMultiploEntidadeGeral.class.php';
include_once CAM_GF_LDO_COMPONENTES . 'ITextBoxSelectAnexo.class.php';

$stModulo = 'ManterNotaExplicativa';
$pgProc = 'PR' . $stModulo . '.php';
$pgJS   = 'JS' . $stModulo . '.php';
#include_once $pgJS;

# Define o objeto da ação stAcao
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];

# Obtem os dados restantes da Nota Explicativa.
if ($stAcao == 'alterar') {
    VLDOManterNotaExplicativa::recuperarInstancia()->recuperarDados($_REQUEST);
}

# Instancia form
$obForm = new Form();
$obForm->setAction($pgProc);
$obForm->setTarget('oculto');

# Define formulário
$obFormulario = new Formulario();
$obFormulario->addForm($obForm);
$obFormulario->addTitulo('Dados da Nota Explicativa');

# Define elementos Hidden
$obHdnAcao = new Hidden;
$obHdnAcao->setName('stAcao');
$obHdnAcao->setValue($stAcao);
$obFormulario->addHidden($obHdnAcao);

# Armazena o código da Nota Explicativa para alteração.
if ($stAcao != 'incluir') {
    $obHdnNotaExplicativa = new Hidden();
    $obHdnNotaExplicativa->setName('inCodNotaExplicativa');
    $obHdnNotaExplicativa->setValue($_REQUEST['inCodNotaExplicativa']);
    $obFormulario->addHidden($obHdnNotaExplicativa);
}

# Define o componente de seleção de LDO
$obISelectLDO = new ISelectLDO();
$obISelectLDO->setNULL(false);
$obISelectLDO->setMostrarTodos(true);
$obISelectLDO->setDisabled($stAcao != 'incluir');
if (isset($_REQUEST['inAnoLDO'])) {
    $obISelectLDO->setValue($_REQUEST['inAnoLDO']);
}
$obFormulario->addComponente($obISelectLDO);

# Define entidade da Nota Explicativa
$obITextBoxSelectEntidade = new ITextBoxSelectEntidadeGeral();
$obITextBoxSelectEntidade->setNull(false);
$obITextBoxSelectEntidade->setCodEntidade($_REQUEST['inCodEntidade']);
$obITextBoxSelectEntidade->obSelect->setDisabled($stAcao != 'incluir');
$obITextBoxSelectEntidade->obTextBox->setDisabled($stAcao != 'incluir');
$obFormulario->addComponente($obITextBoxSelectEntidade);

# Define Anexo
$obITextBoxSelectAnexo = new ITextBoxSelectAnexo();
$obITextBoxSelectAnexo->setNull(false);
$obITextBoxSelectAnexo->setCodAnexo($_REQUEST['inCodAnexo']);
$obITextBoxSelectAnexo->setDisabled($stAcao != 'incluir');
$obFormulario->addComponente($obITextBoxSelectAnexo);

// Texto da Nota Explicativa
$obTxtNotaExplicativa = new TextArea;
$obTxtNotaExplicativa->setId('stNotaExplicativa');
$obTxtNotaExplicativa->setName('stNotaExplicativa');
$obTxtNotaExplicativa->setRotulo('Texto da Nota Explicativa');
$obTxtNotaExplicativa->setValue(stripslashes($_REQUEST['stNotaExplicativa']));
$obTxtNotaExplicativa->setNull(true);
$obTxtNotaExplicativa->setRows(10);
$obTxtNotaExplicativa->setStyle('width:550px;');
$obFormulario->addComponente($obTxtNotaExplicativa);

$obFormulario->ok();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
