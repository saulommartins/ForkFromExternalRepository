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
 * Página de formulário de Configuração Inicial LDO
 * Data de Criação: 02/02/2009
 * Copyright CNM - Confederação Nacional de Municípios
 *
 * @author Heleno Menezes dos Santos <heleno.santos>
 * @author Desenvolvedor: Janilson Mendes P. da Silva <janilson.silva>
 * @package gestaoFinanceira
 * @subpackage ldo
 * @uc uc-02.11.03
 */

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once CAM_GF_LDO_VISAO.'VLDOManterConfiguracao.class.php';

$stProjeto = 'ManterConfiguracao';
$pgFilt = 'FL'.$stProjeto.'.php';
$pgList = 'LS'.$stProjeto.'.php';
$pgForm = 'FM'.$stProjeto.'.php';
$pgProc = 'PR'.$stProjeto.'.php';
$pgOcul = 'OC'.$stProjeto.'.php';
$pgJS   = 'JS'.$stProjeto.'.php';

require_once $pgJS;

Sessao::remove('arLista');
$stAcao = 'alterar';

$obForm = new Form;
$obForm->setAction($pgProc);
$obForm->setTarget('oculto');

$obHdnAcao = new Hidden;
$obHdnAcao->setName ('stAcao');
$obHdnAcao->setValue($stAcao);

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ('stCtrl');
$obHdnCtrl->setValue($stAcao);

$obHdnDscTipo = new Hidden;
$obHdnDscTipo->setId  ('stDscTipo');
$obHdnDscTipo->setName('stDscTipo');

$obExercicio = new Exercicio;
$obExercicio->setId         ('inAnoLDO');
$obExercicio->setName       ('inAnoLDO');
$obExercicio->setRotulo     ('*Exercício');
$obExercicio->setObrigatorio(false);
$obExercicio->setValue      (null);

$rsListaTipoIndicador = new RecordSet;
$obVLDOManterConfiguracao = VLDOManterConfiguracao::recuperarInstancia();
$rsListaTipoIndicador = $obVLDOManterConfiguracao->recuperarListaTipoIndicador();
$rsListaTipoIndicador->setPrimeiroElemento();
$arElementos = $rsListaTipoIndicador->getElementos();

foreach ($arElementos as $inKey => $arDados) {
    $arElementos[$inKey]['linha'] = $inKey+1;
}
$rsListaTipoIndicador->preenche($arElementos);

$stOnChage  = "var arIndicador = this.value.split('_'); jq('#inCodTipoIndicador').val(arIndicador[0]); jq('#inLinha').val(arIndicador[1]); ";
$stOnChage .= "jq.post('".$pgOcul."', {'stCtrl':'montaSimboloTipoIndicador', 'inCodTipoIndicador':arIndicador[0]}, '', 'script');";
$obSelTipoIndicador = new Select;
$obSelTipoIndicador->setRotulo     ('*Tipo');
$obSelTipoIndicador->setName       ('inCodTipoIndicadorLinha');
$obSelTipoIndicador->setId         ('inCodTipoIndicadorLinha');
$obSelTipoIndicador->setCampoId    ('[cod_tipo_indicador]_[linha]');
$obSelTipoIndicador->setCampoDesc  ('descricao');
$obSelTipoIndicador->addOption     (0, 'Selecione');
$obSelTipoIndicador->preencheCombo ($rsListaTipoIndicador);
$obSelTipoIndicador->setTitle      ('Selecione o tipo do Indicador.');
$obSelTipoIndicador->setObrigatorio(false);
$obSelTipoIndicador->obEvento->setOnChange($stOnChage);

$obHdnIndicador = new Hidden;
$obHdnIndicador->setId  ('inCodTipoIndicador');
$obHdnIndicador->setName('inCodTipoIndicador');

$obHdnLinha = new Hidden;
$obHdnLinha->setId  ('inLinha');
$obHdnLinha->setName('inLinha');

$obTxtIndiceIndicador = new Numerico;
$obTxtIndiceIndicador->setRotulo     ('*Índice');
$obTxtIndiceIndicador->setName       ('flIndice');
$obTxtIndiceIndicador->setId         ('flIndice');
$obTxtIndiceIndicador->setTitle      ('Especifique o valor do Índice.');
$obTxtIndiceIndicador->setMaxLength  (14);
$obTxtIndiceIndicador->setSize       (12);
$obTxtIndiceIndicador->setObrigatorio(false);

$obLblSimbolo = new Label;

$obBtnIncluirIndicador = new Button;
$obBtnIncluirIndicador->setName ('Incluir');
$obBtnIncluirIndicador->setValue('Incluir');
$obBtnIncluirIndicador->obEvento->setOnClick('incluirIndicador()');

$obBtnLimparIndicador = new Button;
$obBtnLimparIndicador->setName ('Limpar');
$obBtnLimparIndicador->setValue('Limpar');
$obBtnLimparIndicador->obEvento->setOnClick("limparIndicador()");

$obHdnLista = new Hidden;
$obHdnLista->setId  ('arLista');
$obHdnLista->setName('arLista');

// Adiciona o span onde fica localizado a listagem do tipo de indicador
// Quando carregado os dados, e insere o array de dados ja cadastrados do banco no hidden arLista em um formato de string json
$obSpnIndicadores = new Span;
$obSpnIndicadores->setId('spnListaIndicadores');

$obFormulario = new Formulario;
$obFormulario->addForm      ($obForm);
$obFormulario->addHidden    ($obHdnAcao);
$obFormulario->addHidden    ($obHdnCtrl);
$obFormulario->addHidden    ($obHdnIndicador);
$obFormulario->addHidden    ($obHdnLinha);
$obFormulario->addHidden    ($obHdnDscTipo);
$obFormulario->addHidden    ($obHdnLista);
$obFormulario->addTitulo    ('Dados para Configuração Inicial da LDO');
$obFormulario->addTitulo    ('Indicadores');
$obFormulario->addComponente($obExercicio);
$obFormulario->addComponente($obSelTipoIndicador);
$obFormulario->agrupaComponentes(array($obTxtIndiceIndicador, $obLblSimbolo));
$obFormulario->defineBarra  (array($obBtnIncluirIndicador , $obBtnLimparIndicador), '', '');
$obFormulario->addSpan      ($obSpnIndicadores);
$obFormulario->cancelar     ($pgForm.'?stAcao='.$stAcao);
$obFormulario->show();

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
