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
 * Página de formulário de consulta de Ação
 * Data de Criação: 24/10/2008

 * Copyright CNM - Confederação Nacional de Municípios

 * @author Analista      : Heleno Menezes dos Santos
 * @author Desenvolvedor : Pedro Vaz de Mello de Medeiros

 * @package URBEM
 * @ignore

 * $Id: FMConsultarAcao.php 36117 2008-11-28 20:37:16Z pedro.medeiros $

 * Caso de Uso: uc-02.09.04
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

include_once CAM_GF_PPA_NEGOCIO . 'RPPAManterAcao.class.php';
include_once CAM_GF_ORC_NEGOCIO . 'ROrcamentoEntidade.class.php';

include_once CAM_GF_PPA_VISAO   . 'VPPAManterAcao.class.php';

include_once CAM_GF_PPA_COMPONENTES . 'IPopUpFuncao.class.php';
include_once CAM_GF_ORC_COMPONENTES . 'ITextBoxSelectEntidadeUsuario.class.php';

# Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ? $_GET['stAcao'] : $_POST['stAcao'];

if (empty($stAcao)) {
    $stAcao = 'incluir';
}

# Define o nome dos arquivos PHP
$stProjeto = 'ConsultarAcao';
$pgFilt = 'FL' . $stProjeto . '.php';
$pgList = 'LS' . $stProjeto . '.php';
$pgForm = 'FM' . $stProjeto . '.php';
$pgProc = 'PR' . $stProjeto . '.php';
$pgOcul = 'OC' . $stProjeto . '.php';
$pgJS   = 'JS' . $stProjeto . '.php';

include_once $pgJS;

$obRPPAManterAcao = new RPPAManterAcao();
$obVPPAManterAcao = new VPPAManterAcao( $obRPPAManterAcao );

$rsAcao = $obVPPAManterAcao->recuperaAcao($_REQUEST);

# Recupera todos os dados da ação e relacionados.
if (!$rsAcao->eof()) {
    $_REQUEST['inNumPrograma']              = trim($rsAcao->getCampo('num_programa'));
    $_REQUEST['inCodAcao']                  = trim($rsAcao->getCampo('cod_acao'));
    $_REQUEST['inCodTipo']                  = trim($rsAcao->getCampo('cod_tipo'));
    $_REQUEST['inCodRegiao']                = trim($rsAcao->getCampo('cod_regiao'));
    $_REQUEST['stRegiao']                   = trim($rsAcao->getCampo('nom_regiao'));
    $_REQUEST['inCodProduto']               = trim($rsAcao->getCampo('cod_produto'));
    $_REQUEST['stDscProduto']               = trim($rsAcao->getCampo('dsc_produto'));
    $_REQUEST['stUnidadeMedida']            = trim($rsAcao->getCampo('cod_unidade_medida'));
    $_REQUEST['inCodEntidade']              = trim($rsAcao->getCampo('cod_entidade'));
    $_REQUEST['stDscEntidade']              = trim($rsAcao->getCampo('nom_entidade'));
    $_REQUEST['stDscUnidade']               = trim($rsAcao->getCampo('nom_unidade'));
    $_REQUEST['tsAcaoDados']                = trim($rsAcao->getCampo('ultimo_timestamp_acao_dados'));
    $_REQUEST['stUnidadeOrcamentaria']      = trim($rsAcao->getCampo('unidade_orcamentaria'));
    $_REQUEST['stNomUnidadeOrcamentaria']   = trim($rsAcao->getCampo('nom_unidade'));
    $_REQUEST['inCGM']                      = trim($rsAcao->getCampo('numcgm'));
    $_REQUEST['stNomServidor']              = trim($rsAcao->getCampo('nom_cgm'));
    $_REQUEST['inCodFuncao']                = trim($rsAcao->getCampo('cod_funcao'));
    $_REQUEST['stDscFuncao']                = trim($rsAcao->getCampo('dsc_funcao'));
    $_REQUEST['inCodSubFuncao']             = trim($rsAcao->getCampo('cod_subfuncao'));
    $_REQUEST['stDscSubFuncao']             = trim($rsAcao->getCampo('dsc_subfuncao'));
    $_REQUEST['inCodOrgao']                 = trim($rsAcao->getCampo('cod_orgao'));
    $_REQUEST['stNomOrgao']                 = trim($rsAcao->getCampo('nom_orgao'));
    $_REQUEST['boHomologado']               = $rsAcao->getCampo('homologado') == 't' ? true : false;
    $_REQUEST['inCodNorma']                 = trim($rsAcao->getCampo('cod_norma'));
}

# Recupera todos os dados de quantidade da ação.
$rsQuantidade = $obVPPAManterAcao->recuperaQuantidades($_REQUEST);

while (!$rsQuantidade->eof()) {
    $stAtributo = 'flQuantidadeAno' . $rsQuantidade->getCampo('ano');
    $_REQUEST[$stAtributo] = $rsQuantidade->getCampo('valor');
    $rsQuantidade->proximo();
}

# Definição de dados ocultos
$obHdnAcao = new Hidden();
$obHdnAcao->setName('stAcao');
$obHdnAcao->setValue($stAcao);

$obHdnCtrl = new Hidden();
$obHdnCtrl->setName('stCtrl');
$obHdnCtrl->setValue($stCtrl);

$obHdnProg = new Hidden();
$obHdnProg->setName('inNumPrograma');
$obHdnProg->setValue($_REQUEST['inNumPrograma']);

$obHdnAcao = new Hidden();
$obHdnAcao->setName('inCodAcao');
$obHdnAcao->setValue($_REQUEST['inCodAcao']);

# Definição do form
$obForm = new Form();
$obForm->setAction($pgList);
$obForm->setTarget('oculto');
$obForm->setEncType('multipart/form-data');

# Definição do Formulário
$obFormulario = new Formulario();
$obFormulario->addForm($obForm);
$obFormulario->addHidden($obHdnAcao);
$obFormulario->addHidden($obHdnCtrl);
$obFormulario->addHidden($obHdnProg);

$obFormulario->addTitulo('Detalhamento das Ações');

# Define label que exibe tipo da ação.
switch ($_REQUEST['inCodTipo']) {
case 1:
    $stDscTipo = 'Projeto';
    break;
case 2:
    $stDscTipo = 'Atividade';
    break;
case 3:
    $stDscTipo = 'Operação Especial';
    break;
}

$obLblTipo = new Label();
$obLblTipo->setRotulo('Tipo');
$obLblTipo->setValue($stDscTipo);
$obFormulario->addComponente($obLblTipo);

$obLblAcao = new Label();
$obLblAcao->setRotulo('Ação');
$obLblAcao->setValue(sprintf('%03d', $_REQUEST['inCodAcao']) . ' - ' . $_REQUEST['stDescricao']);
$obFormulario->addComponente($obLblAcao);

$obLblEntidade = new Label();
$obLblEntidade->setRotulo('Entidade');
$obLblEntidade->setValue($_REQUEST['inCodEntidade'] . ' - ' . $_REQUEST['stDscEntidade']);
$obFormulario->addComponente($obLblEntidade);

$obLblRegiao = new Label();
$obLblRegiao->setRotulo('Região de Abrangência');
$obLblRegiao->setValue($_REQUEST['inCodRegiao'] . ' - ' . $_REQUEST['stRegiao']);
$obFormulario->addComponente($obLblRegiao);

# Recupera dados do produto.
$arProduto = $obVPPAManterAcao->recuperaProduto($_REQUEST);

if ($arProduto) {
    $stLstProduto = $obVPPAManterAcao->listaProduto($arProduto, false, true);
} else {
    $stLstProduto = '&nbsp;';
}

# Define produto e seus atributos em lista.
$obSpnListaProduto = new Span();
$obSpnListaProduto->setID('spnListaProduto');
$obSpnListaProduto->setValue($stLstProduto);
$obFormulario->addSpan($obSpnListaProduto);

$obLblUnidadeOrcamentaria = new Label();
$obLblUnidadeOrcamentaria->setRotulo('Unidade Orçamentária Responsável');
$stCampo = $_REQUEST['stUnidadeOrcamentaria'] . ' / ' . $_REQUEST['stNomOrgao'] . ' - ' . $_REQUEST['stNomUnidadeOrcamentaria'];
$obLblUnidadeOrcamentaria->setValue($stCampo);
$obFormulario->addComponente($obLblUnidadeOrcamentaria);

$obLblResponsavel = new Label();
$obLblResponsavel->setRotulo('Servidor Responsável');
$obLblResponsavel->setValue($_REQUEST['inCGM'] . ' - ' . $_REQUEST['stNomServidor']);
$obFormulario->addComponente($obLblResponsavel);

$obLblFuncao = new Label();
$obLblFuncao->setRotulo('Função');
$obLblFuncao->setValue($_REQUEST['inCodFuncao'] . ' - ' . $_REQUEST['stDscFuncao']);
$obFormulario->addComponente($obLblFuncao);

$obLblSubFuncao = new Label();
$obLblSubFuncao->setRotulo('Subfunção');
$obLblSubFuncao->setValue($_REQUEST['inCodSubFuncao'] . ' - ' . $_REQUEST['stDscSubFuncao']);
$obFormulario->addComponente($obLblSubFuncao);

# Recupera dados dos recursos.
$rsRecursos    = $obVPPAManterAcao->recuperaRecursos($_REQUEST);
$stLstRecursos = '&nbsp;';

if (!$rsRecursos->eof()) {
    $arRecursos = $rsRecursos->getElementos();
    $stLstRecursos = $obVPPAManterAcao->listaRecursos($arRecursos, false, true, true);
}

# Define lista de recursos.
$obSpnListaRecurso = new Span();
$obSpnListaRecurso->setID('spnListaRecurso');
$obSpnListaRecurso->setValue($stLstRecursos);
$obFormulario->addSpan($obSpnListaRecurso);

#
# Define campos dos totais
#

# Define componentes contendo total e subtotal da receita.
$obHdnSubtotalReceita = new Hidden();
$obHdnSubtotalReceita->setID('flSubtotalReceita');
$obHdnSubtotalReceita->setValue(0.0);
$obFormulario->addHidden($obHdnSubtotalReceita);

$obLblTotalReceita = new Label();
$obLblTotalReceita->setRotulo('Total da Receita');
$obLblTotalReceita->setID('stTotalReceita');
$obFormulario->addComponente($obLblTotalReceita);

# Define componentes contendo total e subtotal do PPA.
$obHdnSubtotalPPA = new Hidden();
$obHdnSubtotalPPA->setID('flSubtotalPPA');
$obHdnSubtotalPPA->setValue(0.0);
$obFormulario->addHidden($obHdnSubtotalPPA);

# Define campo contendo Total da Despesa (total do PPA).
$obLblTotalPPA = new Label();
$obLblTotalPPA->setRotulo('Total da Despesa');
$obLblTotalPPA->setID('stTotalPPA');
$obFormulario->addComponente($obLblTotalPPA);

# Define campo contendo quantidade total.
$obLblQuantidadeTotal = new Label();
$obLblQuantidadeTotal->setRotulo('Quantidade Total');
$obLblQuantidadeTotal->setID('stQuantidadeTotal');
$obFormulario->addComponente($obLblQuantidadeTotal);

# Define campo contendo total da ação.
$obHdnTotalAcao = new Hidden();
$obHdnTotalAcao->setName('flTotalAcao');
$obHdnTotalAcao->setValue(0.0);
$obFormulario->addHidden($obHdnTotalAcao);

$obLblTotalAcao = new Label();
$obLblTotalAcao->setID('stTotalAcao');
$obLblTotalAcao->setRotulo('Total da Ação');
$obFormulario->addComponente($obLblTotalAcao);

# Define componentes contendo total e subtotal do programa.
$obHdnSubtotalProg = new Hidden();
$obHdnSubtotalProg->setID('flSubtotalProg');
$obHdnSubtotalProg->setValue(0.0);
$obFormulario->addHidden($obHdnSubtotalProg);

$obLblTotalProg = new Label();
$obLblTotalProg->setID('stTotalPrograma');
$obLblTotalProg->setRotulo('Total do Programa');
$obFormulario->addComponente($obLblTotalProg);

# Define campo contendo Diferença a Lançar (total acumulado).
$obLblTotalAcumulado = new Label();
$obLblTotalAcumulado->setID('stTotalAcumulado');
$obLblTotalAcumulado->setRotulo('Diferença a Lançar');
$obFormulario->addComponente($obLblTotalAcumulado);

# Define botão de retorno.
$obBtnRetornar = new Button();
$obBtnRetornar->setValue('Retornar');
$obBtnRetornar->obEvento->setOnClick('retornarConsulta();');
$obFormulario->defineBarra(array($obBtnRetornar));

$obFormulario->show();

$jsOnLoad = 'atualiza();';

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
