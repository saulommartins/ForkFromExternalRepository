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
 * Página de formulário Renúncia de Receita
 * Data de Criação: 23/03/2009
 *
 *
 * @author Marcio Medeiros <marcio.medeiros>
 * @package GF
 * @subpackage LDO
 * @uc uc-02.10.16 - Manter Compensação da Renúncia de Receita
 */

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once CAM_GF_PPA_COMPONENTES.'ITextBoxSelectPPA.class.php';
require_once CAM_GF_LDO_VISAO.'VLDOManterRenunciaReceita.class.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/funcoesLegado.lib.php';

$stModulo = 'ManterRenunciaReceita';
$pgProc   = 'PR' . $stModulo . '.php';
$pgJS     = 'JS' . $stModulo . '.php';
$pgOcul   = 'OC' . $stModulo . '.php';
$pgList   = 'LS' . $stModulo . '.php';
require_once $pgJS;

$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];

//Instancia form
$obForm = new Form;
$obForm->setAction($pgProc);
$obForm->setTarget("oculto");

$obFormulario = new Formulario;
$obFormulario->addForm($obForm);

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ("stAcao");
$obHdnAcao->setValue($stAcao);
// Define a ação para o OC (utilizado pelo montaParametrosGET)
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ("stCtrl");
$obHdnCtrl->setValue($stAcao);

$obFormulario->addHidden($obHdnAcao);
$obFormulario->addHidden($obHdnCtrl);

$rsRenunciaReceita = new RecordSet;
if (isset($_REQUEST['inCodPPA'])) {
    $rsRenunciaReceita = VLDOManterRenunciaReceita::recuperarInstancia()->recuperarRenunciaReceita($_REQUEST);
    $stTributo          = $rsRenunciaReceita->getCampo('tributo');
    $stModalidade       = $rsRenunciaReceita->getCampo('modalidade');
    $stSetoresProgramas = $rsRenunciaReceita->getCampo('setores_programas');
    $stCompensacao      = $rsRenunciaReceita->getCampo('compensacao');
} else {
    $rsRenunciaReceita->preenche(array());
}

if ($stAcao == 'alterar') {
    $obHdnCodCompensacao = new Hidden;
    $obHdnCodCompensacao->setName ('inCodCompensacao');
    $obHdnCodCompensacao->setValue($_REQUEST['inCodCompensacao']);
    $obFormulario->addHidden($obHdnCodCompensacao);

    $obHdnInAnoLdoOriginal = new Hidden;
    $obHdnInAnoLdoOriginal->setName ("inAnoLdoOriginal");
    $obHdnInAnoLdoOriginal->setValue($_REQUEST['inAnoLDO']);
    $obFormulario->addHidden($obHdnInAnoLdoOriginal);
}

$obFormulario->addTitulo('Dados para ' . $stAcao . ' Compensação da Renúncia de Receita');

// Monta o select para buscar o PPA e de acordo com sua escolha preenche a combo de exercício, usando os valores iniciais e finais para
// preencher a combo de exercicios. Isso é feito via JS pelo arquivo JS do programa, sendo chamado pelo método montaExercicio()
$obITextBoxSelectPPA = new ITextBoxSelectPPA;
$obITextBoxSelectPPA->setPreencheUnico(true);
$obITextBoxSelectPPA->setNull(false);
$obITextBoxSelectPPA->setHomologado(true);
$obITextBoxSelectPPA->obTextBox->obEvento->setOnChange("montaParametrosGET('montaExercicio');");
$obITextBoxSelectPPA->obSelect->obEvento->setOnChange("montaParametrosGET('montaExercicio');");
if (isset($_REQUEST['inCodPPA'])) {
    $obITextBoxSelectPPA->setLabel(true);
    $obITextBoxSelectPPA->obTextBox->setValue($_REQUEST['inCodPPA']);
    $obITextBoxSelectPPA->obTextBox->setLabel(true);
    $obITextBoxSelectPPA->obSelect->setValue($_REQUEST['inCodPPA']);
    $obITextBoxSelectPPA->obSelect->setLabel(true);
}
$obITextBoxSelectPPA->geraFormulario($obFormulario);

// Monta o componente que receberá valores de acordo com o que for selecionado pelo select de PPA
$obSelectExercicio = new Select;
$obSelectExercicio->setId('inAnoLDO');
$obSelectExercicio->setName('inAnoLDO');
$obSelectExercicio->setTitle('Selecione o exercício.');
$obSelectExercicio->setRotulo('Exercício LDO');
$obSelectExercicio->addOption('', 'Selecione');
$obSelectExercicio->setNull(false);
$obSelectExercicio->setStyle('width: 100px');
if (isset($_REQUEST['inCodPPA'])) {
    $obSelectExercicio->setLabel(true);
    $obSelectExercicio->setCampoID('ano');
    $obSelectExercicio->setCampoDesc('ano_ldo');
    $obSelectExercicio->preencheCombo(VLDOManterRenunciaReceita::recuperarInstancia()->recuperarRegra()->recuperaExercicioPPA($_REQUEST['inCodPPA']));
    $obSelectExercicio->setValue($_REQUEST['inAnoLDO']);
}
$obFormulario->addComponente($obSelectExercicio);

// Tributo
$obTxtTributo = new TextBox;
$obTxtTributo->setName     ( 'stTributo' );
$obTxtTributo->setId       ( 'stTributo' );
$obTxtTributo->setRotulo   ( "Tributo" );
$obTxtTributo->setSize     ( 80 );
$obTxtTributo->setMaxLength( 250 );
$obTxtTributo->setNull     ( false );
$obTxtTributo->setTitle    ( 'Informe o Tributo' );
$obTxtTributo->setObrigatorio( true );
if (isset($stTributo)) {
    $obTxtTributo->setValue($stTributo);
}
$obFormulario->addComponente($obTxtTributo);

// Modalidade
$obTxtModalidade = new TextBox;
$obTxtModalidade->setName     ( 'stModalidade' );
$obTxtModalidade->setId       ( 'stModalidade' );
$obTxtModalidade->setRotulo   ( "Modalidade" );
$obTxtModalidade->setSize     ( 80 );
$obTxtModalidade->setMaxLength( 250 );
$obTxtModalidade->setNull     ( false );
$obTxtModalidade->setTitle    ( 'Informe a Modalidade' );
$obTxtModalidade->setObrigatorio( true );
if (isset($stModalidade)) {
    $obTxtModalidade->setValue($stModalidade);
}
$obFormulario->addComponente($obTxtModalidade);

// Setores/Programas/Beneficiários
$obTxtSetorPrograma = new TextBox;
$obTxtSetorPrograma->setName     ( 'stSetorProgramas' );
$obTxtSetorPrograma->setId       ( 'stSetorProgramas' );
$obTxtSetorPrograma->setRotulo   ( "Setores/Programas/Beneficiários" );
$obTxtSetorPrograma->setSize     ( 80 );
$obTxtSetorPrograma->setMaxLength( 250 );
$obTxtSetorPrograma->setNull     ( false );
$obTxtSetorPrograma->setTitle    ( 'Informe Setores/Programas/Beneficiários' );
$obTxtSetorPrograma->setObrigatorio( true );
if (isset($stSetoresProgramas)) {
    $obTxtSetorPrograma->setValue($stSetoresProgramas);
}
$obFormulario->addComponente($obTxtSetorPrograma);

// Renúncia de Receita provista para o ano atual
$obNumValorAnoLDO = new Numerico;
$obNumValorAnoLDO->setRotulo    ('Renúncia de receita prevista para ano atual');
$obNumValorAnoLDO->setTitle     ('Informe a Renúncia de Receita prevista para ano atual');
$obNumValorAnoLDO->setName      ('flValorAnoLDO');
$obNumValorAnoLDO->setId        ('flValorAnoLDO');
$obNumValorAnoLDO->setDecimais  (2);
$obNumValorAnoLDO->setMaxValue  (999999999999.99);
$obNumValorAnoLDO->setNegativo  (false);
$obNumValorAnoLDO->setNaoZero   (false);
$obNumValorAnoLDO->setSize      (14);
$obNumValorAnoLDO->setMaxLength (12);
$obNumValorAnoLDO->setObrigatorio( true );
if (isset($_REQUEST['flValorAnoLDO'])) {
    $stValor = LDOString::retornarValorMonetario($_REQUEST['flValorAnoLDO']);
    $obNumValorAnoLDO->setValue($stValor);
}
$obFormulario->addComponente($obNumValorAnoLDO);

// Renúncia de Receita provista para o ano + 1
$obNumValorAnoLDO1 = new Numerico;
$obNumValorAnoLDO1->setRotulo    ('Renúncia de receita prevista para ano + 1');
$obNumValorAnoLDO1->setTitle     ('Informe a Renúncia de receita prevista para ano + 1');
$obNumValorAnoLDO1->setName      ('flValorAnoLDO1');
$obNumValorAnoLDO1->setId        ('flValorAnoLDO1');
$obNumValorAnoLDO1->setDecimais  (2);
$obNumValorAnoLDO1->setMaxValue  (999999999999.99);
$obNumValorAnoLDO1->setNegativo  (false);
$obNumValorAnoLDO1->setNaoZero   (false);
$obNumValorAnoLDO1->setSize      (14);
$obNumValorAnoLDO1->setMaxLength (12);
$obNumValorAnoLDO1->setObrigatorio( true );
if (isset($_REQUEST['flValorAnoLDO1'])) {
    $stValor = LDOString::retornarValorMonetario($_REQUEST['flValorAnoLDO1']);
    $obNumValorAnoLDO1->setValue($stValor);
}
$obFormulario->addComponente($obNumValorAnoLDO1);

// Renúncia de Receita provista para o ano + 2
$obNumValorAnoLDO2 = new Numerico;
$obNumValorAnoLDO2->setRotulo    ('Renúncia de receita prevista para ano + 2');
$obNumValorAnoLDO2->setTitle     ('Informe a Renúncia de receita prevista para ano + 2');
$obNumValorAnoLDO2->setName      ('flValorAnoLDO2');
$obNumValorAnoLDO2->setId        ('flValorAnoLDO2');
$obNumValorAnoLDO2->setDecimais  (2);
$obNumValorAnoLDO2->setMaxValue  (999999999999.99);
$obNumValorAnoLDO2->setNegativo  (false);
$obNumValorAnoLDO2->setNaoZero   (false);
$obNumValorAnoLDO2->setSize      (14);
$obNumValorAnoLDO2->setMaxLength (12);
$obNumValorAnoLDO2->setObrigatorio( true );
if (isset($_REQUEST['flValorAnoLDO2'])) {
    $stValor = LDOString::retornarValorMonetario($_REQUEST['flValorAnoLDO2']);
    $obNumValorAnoLDO2->setValue($stValor);
}
$obFormulario->addComponente($obNumValorAnoLDO2);

// Modalidade
$obTxtCompensacao = new TextBox;
$obTxtCompensacao->setName     ( 'stCompensacao' );
$obTxtCompensacao->setId       ( 'stCompensacao' );
$obTxtCompensacao->setRotulo   ( "Compensação" );
$obTxtCompensacao->setSize     ( 80 );
$obTxtCompensacao->setMaxLength( 250 );
$obTxtCompensacao->setNull     ( false );
$obTxtCompensacao->setTitle    ( 'Informe a Compensação' );
$obTxtCompensacao->setObrigatorio( true );
if (isset($stCompensacao)) {
    $obTxtCompensacao->setValue($stCompensacao);
}
$obFormulario->addComponente($obTxtCompensacao);

// BOTÕES DE AÇÃO DO FORMULÁRIO (OK/LIMPAR)
if ($stAcao == 'incluir') {
    $obFormulario->OK(true);
} else {
    $stLocation = $pgList.'?'.$sessao->id.'&stAcao='.$stAcao.'&pg='.$_REQUEST['pg'].'&pos='.$_REQUEST['pos'].'&inAnoLDO='.$_REQUEST['inAnoLDO'];
    $obFormulario->Cancelar( $stLocation );
}
$obFormulario->show();

// Caso venha apenas um item no select do ppa, ele já vem setado, com isso nao estava preenchendo o exercicio da ldo
if ($stAcao != 'alterar') {
    $jsOnLoad .= "if (jq('#inCodPPATxt').val() != '') {
                    montaParametrosGET('montaExercicio');
                 }";
}

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
