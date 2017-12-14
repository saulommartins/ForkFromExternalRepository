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
 * Página de Filtro de Expansão das Despesas de Caráter Continuado
 * Data de Criação: 23/03/2009
 *
 *
 * @author Analista: Bruno
 * @author Programador: Pedro Vaz de Mello de Medeiros <pedro.medeiros>
 * @package GF
 * @subpackage LDO
 * @uc uc-02.10.05 - Manter Ajuste de Anexo
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

include_once CAM_GF_LDO_COMPONENTES . 'ISelectLDO.class.php';

$stAcao = $request->get('stAcao');

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName('stAcao');
$obHdnAcao->setValue($stAcao);

# Define form
$obForm = new Form;
$obForm->setAction('LSManterDespesaContinua.php');
$obForm->setTarget('telaPrincipal');

# Define formulário
$obFormulario = new Formulario;
$obFormulario->addForm($obForm);
$obFormulario->addTitulo('Dados para Filtro');

# Acrescenta hiddens ao formulário
$obFormulario->addHidden($obHdnAcao);

# Define o componente LDO
$obISelectLDO = new ISelectLDO();
$obISelectLDO->setVigenciaPPA(true);
$obISelectLDO->setObrigatorio(true);
$obFormulario->addComponente($obISelectLDO);

$obFormulario->ok();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
