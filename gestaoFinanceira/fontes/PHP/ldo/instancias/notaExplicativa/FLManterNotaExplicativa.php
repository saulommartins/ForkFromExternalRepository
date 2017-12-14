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
 * Página de Filtro da Nota Explicativa
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
include_once CAM_GF_LDO_COMPONENTES . 'ITextBoxSelectAnexo.class.php';

$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName('stAcao');
$obHdnAcao->setValue($stAcao);

# Instancia form
$obForm = new Form;
$obForm->setAction('LSManterNotaExplicativa.php');
$obForm->setTarget('telaPrincipal');

# Define formulário
$obFormulario = new Formulario;
$obFormulario->addForm($obForm);
$obFormulario->addTitulo('Dados para Filtro');

# Acrescenta hiddens ao formulário
$obFormulario->addHidden($obHdnAcao);

# Define Anexo
$obITextBoxSelectAnexo = new ITextBoxSelectAnexo();
$obITextBoxSelectAnexo->setNull(false);
$obFormulario->addComponente($obITextBoxSelectAnexo);

# Obtem lista de Notas Explicativas
$rsListaNotaExplicativa = VLDOManterNotaExplicativa::recuperarInstancia()->recuperarListaNotaExplicativa();

$obFormulario->ok();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
