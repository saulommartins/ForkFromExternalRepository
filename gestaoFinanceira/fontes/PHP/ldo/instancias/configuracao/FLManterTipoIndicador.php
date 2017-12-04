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
 * Página de filtro do Tipo de Indicador
 * Data de Criação: 09/01/2009
 * Copyright CNM - Confederação Nacional de Municípios
 *
 * @author Analista     : Tonismar Regis Bernardo     <tonismar.bernardo@cnm.org.br>
 * @author Desenvolvedor: Henrique Girardi dos Santos <henrique.santos@cnm.org.br>
 * @package gestaoFinanceira
 * @subpackage ldo
 * @uc uc-02.10.02
 */

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once CAM_GA_ADM_COMPONENTES.'ISelectUnidadeMedida.class.php';

$stModulo = 'ManterTipoIndicador';
$pgProc   = 'PR'.$stModulo.'.php';
$pgJS     = 'JS'.$stModulo.'.php';
$pgOcul   = 'OC'.$stModulo.'.php';
$pgList   = 'LS'.$stModulo.'.php';

$stAcao = $request->get('stAcao');
Sessao::remove('arFiltro');

$obHdnAcao = new Hidden;
$obHdnAcao->setId   ('stAcao');
$obHdnAcao->setName ('stAcao');
$obHdnAcao->setValue($stAcao);

$obForm = new Form;
$obForm->setAction($pgList);
$obForm->setTarget('telaPrincipal');

$obStDescricao = new TextBox;
$obStDescricao->setId       ('stDescricao');
$obStDescricao->setName     ('stDescricao');
$obStDescricao->setRotulo   ('Descriçao');
$obStDescricao->setTitle    ('Insira a descrição para o tipo de indicador.');
$obStDescricao->setSize     (40);
$obStDescricao->setMaxLength(40);

$obStDescricaoBusca = new TipoBusca($obStDescricao);

$obISelectUnidadeMedida = new ISelectUnidadeMedida;

$obFormulario = new Formulario;
$obFormulario->addForm      ($obForm);
$obFormulario->addHidden    ($obHdnAcao);
$obFormulario->addTitulo    ('Dados para filtrar o Tipo de Indicador');
$obFormulario->addComponente($obStDescricaoBusca);
$obFormulario->addComponente($obISelectUnidadeMedida);
$obFormulario->ok();
$obFormulario->show();

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
