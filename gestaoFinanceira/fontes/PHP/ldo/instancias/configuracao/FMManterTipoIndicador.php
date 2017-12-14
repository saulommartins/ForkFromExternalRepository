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
 * Página de formulário de Cadastro de Tipo de Indicador
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

$stProjeto = 'ManterTipoIndicador';
$pgFilt = 'FL'.$stProjeto.'.php';
$pgList = 'LS'.$stProjeto.'.php';
$pgForm = 'FM'.$stProjeto.'.php';
$pgProc = 'PR'.$stProjeto.'.php';
$pgOcul = 'OC'.$stProjeto.'.php';
$pgJS   = 'JS'.$stProjeto.'.php';

$stAcao = $request->get('stAcao');

$obForm = new Form;
$obForm->setAction($pgProc);
$obForm->setTarget('oculto');

$obHdnAcao = new Hidden;
$obHdnAcao->setId   ('stAcao');
$obHdnAcao->setName ('stAcao');
$obHdnAcao->setValue('verificaSimilaridade');

$obHdnAcaoOriginal = new Hidden;
$obHdnAcaoOriginal->setId   ('stAcaoOriginal');
$obHdnAcaoOriginal->setName ('stAcaoOriginal');
$obHdnAcaoOriginal->setValue($stAcao);

$obHdnCodTipoIndicador = new Hidden;
$obHdnCodTipoIndicador->setId  ('inCodTipoIndicador');
$obHdnCodTipoIndicador->setName('inCodTipoIndicador');

$obStDescricao = new TextBox;
$obStDescricao->setId         ('stDescricao');
$obStDescricao->setName       ('stDescricao');
$obStDescricao->setRotulo     ('Descrição');
$obStDescricao->setTitle      ('Insira a descrição para o tipo de indicador.');
$obStDescricao->setSize       (40);
$obStDescricao->setMaxLength  (40);
$obStDescricao->setObrigatorio(true);

$obISelectUnidadeMedida = new ISelectUnidadeMedida;
$obISelectUnidadeMedida->setObrigatorio(true);

// Como vai alterar os dados, é inserido os dados nos campos
if ($stAcao == 'alterar') {
    // Para não passar uma string pelo GET, pega-se a descricao do tipo pela pesquisa atraves do seu codigo
    require_once CAM_GF_LDO_VISAO.'VLDOManterTipoIndicador.class.php';
    $arFiltro = array('inCodTipoIndicador' => $_GET['inCodTipoIndicador']);
    $rsTipoIndicador = VLDOManterTipoIndicador::recuperarInstancia()->recuperarRegra()->retornaDadosTipoIndicador($arFiltro);

    $obStDescricao->setValue         ($rsTipoIndicador->getCampo('descricao'));
    $obISelectUnidadeMedida->setValue($_GET['inCodUnidade'].'-'.$_GET['inCodGrandeza']);
    $obHdnCodTipoIndicador->setValue ($_GET['inCodTipoIndicador']);
}

$obFormulario = new Formulario;
$obFormulario->addForm      ($obForm);
$obFormulario->addHidden    ($obHdnAcao);
$obFormulario->addHidden    ($obHdnAcaoOriginal);
$obFormulario->addHidden($obHdnCodTipoIndicador);
$obFormulario->addTitulo    ('Dados para '.ucfirst($stAcao).' o Tipo de Indicador');
$obFormulario->addComponente($obStDescricao);
$obFormulario->addComponente($obISelectUnidadeMedida);

if ($stAcao == 'incluir') {
    $obFormulario->Ok();
} else {
    $obFormulario->Cancelar($pgList);
}

$obFormulario->show();

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
