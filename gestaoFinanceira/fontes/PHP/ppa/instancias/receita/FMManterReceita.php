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
    * Página de Formulario de Manter Receita
    * Data de Criação: 24/09/2008
    *
    *
    *
    * @author Marcio Medeiros <marcio.medeiros@cnm.org.br>

    * $Id: FMManterReceita.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_ORC_COMPONENTES . 'ISelectOrgao.class.php';
include_once CAM_GF_PPA_COMPONENTES . 'IPopUpRecurso.class.php';
include_once CAM_GF_PPA_COMPONENTES . 'ITextBoxSelectPPA.class.php';
include_once CAM_GF_LDO_COMPONENTES . 'IPopUpRubrica.class.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/normas/classes/componentes/IPopUpNorma.class.php';
include_once CAM_GF_PPA_CLASSES . 'negocio/RPPAManterReceita.class.php';
include_once CAM_GF_PPA_CLASSES . 'visao/VPPAManterReceita.class.php';
include_once CAM_GF_ORC_COMPONENTES    . 'ITextBoxSelectEntidadeGeral.class.php';

//Define o nome dos arquivos PHP
$stProjeto = 'ManterReceita';
$pgFilt    = 'FL'.$stProjeto.'.php';
$pgList    = 'LS'.$stProjeto.'.php';
$pgForm    = 'FM'.$stProjeto.'.php';
$pgProc    = 'PR'.$stProjeto.'.php';
$pgOcul    = 'OC'.$stProjeto.'.php';
$pgJS      = 'JS'.$stProjeto.'.php';

include_once( $pgJS );

$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
$inCodEntidade = 0;
if (isset($_REQUEST['inCodEntidade'])) {
    $inCodEntidade = (int) $_REQUEST['inCodEntidade'];
}

// Objeto controller
$obVisao = new VPPAManterReceita( new RPPAManterReceita );

//Instancia form
$obForm = new Form;
$obForm->setAction($pgProc);
$obForm->setTarget("oculto");

$obFormulario = new Formulario;
$obFormulario->addForm  ($obForm);

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ('stAcao');
$obHdnAcao->setId   ('stAcao');
$obHdnAcao->setValue($stAcao);
//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ('stCtrl');
$obHdnCtrl->setId   ('stCtrl');
$obHdnCtrl->setValue($stAcao);

// PPA
$obITextBoxSelectPPA = new ITextBoxSelectPPA; // ID: inCodPPATxt
$obITextBoxSelectPPA->setNull(false);
$stEventoPPA = 'confirmaExecutarEventosPPA(this.value);';
$obITextBoxSelectPPA->obTextBox->obEvento->setOnBlur ($stEventoPPA);
$obITextBoxSelectPPA->obSelect->obEvento->setOnChange($stEventoPPA);

// ENTIDADE
$obITextBoxSelectEntidade = new ITextBoxSelectEntidadeGeral();
$obITextBoxSelectEntidade->setNull(false);
if ($inCodEntidade > 0) {
    $obITextBoxSelectEntidade->setCodEntidade( $inCodEntidade );
}

# Define Destinação do recurso da ação.
$obHdnDestinacaoRecurso = new Hidden();
$obHdnDestinacaoRecurso->setID('boDestinacaoRecurso');
$obHdnDestinacaoRecurso->setName('boDestinacaoRecurso');
$obHdnDestinacaoRecurso->setValue($_REQUEST['destinacao_recurso']);
$obFormulario->addHidden($obHdnDestinacaoRecurso);

$obFormulario->addHidden($obHdnCtrl);
$obFormulario->addHidden($obHdnAcao);
// Exercício do PPA
$obHdnExercicio = new Hidden;
$obHdnExercicio->setName('stExercicio');
$obHdnExercicio->setId  ('stExercicio');

// Recurso (Span do popup do recurso)
$obSpnRecurso = new Span();
$obSpnRecurso->setID('spnRecurso');

// Componente de Recurso não posta mais o
$obHdnDescPopUpRecurso = new Hidden;
$obHdnDescPopUpRecurso->setName('hdnDescPopUpRecurso');
$obHdnDescPopUpRecurso->setId  ('hdnDescPopUpRecurso');
$obFormulario->addHidden($obHdnDescPopUpRecurso);

if ($stAcao == 'incluir') {
    // Persiste o cod PPA anteriormente selecionado
    $obHdnInCodPPAAnterior = new Hidden;
    $obHdnInCodPPAAnterior->setName ('inCodPPAAnterior');
    $obHdnInCodPPAAnterior->setId   ('inCodPPAAnterior');
    $obHdnInCodPPAAnterior->setValue('');
    $obFormulario->addHidden($obHdnInCodPPAAnterior);

    $obFormulario->addTitulo    ("Dados para Cadastro da Receita do PPA");
    // PPA
    $obFormulario->addComponente($obITextBoxSelectPPA);
    // RECEITA
    $obIPopUpRubrica = new IPopUpRubrica();
    $obIPopUpRubrica->setTitle('Informa a Receita');
    $obIPopUpRubrica->setRotulo('Receita');
    $obIPopUpRubrica->setNull(false);
    $obIPopUpRubrica->setDedutora(false);
    $obIPopUpRubrica->geraFormulario($obFormulario);

    // ENTIDADE
    $obFormulario->addComponente($obITextBoxSelectEntidade);
    // Se PPA Homologado, obrigatório Norma
    $spnNorma = new Span();
    $spnNorma->setId("spnNorma");
    $obFormulario->addSpan      ($spnNorma);
    // Span Recurso
    $obFormulario->addSpan($obSpnRecurso);
    $obHdnExercicio->setValue(Sessao::read('exercicio'));
    $obFormulario->addHidden($obHdnExercicio);

} elseif ($stAcao == 'alterar') {
    // Validar dados vindos da LSMenterReceita
    $inCodPPA            = $_REQUEST['cod_ppa'];
    $inCodReceitaDados   = $_REQUEST['cod_receita_dados'];
    $inCodEntidade       = $_REQUEST['cod_entidade'];
    $stNomEntidade       = $_REQUEST['nom_entidade'];
    $inCodNorma          = $_REQUEST['cod_norma'];
    $inCodReceita        = $_REQUEST['cod_receita'];
    $inCodConta   = $_REQUEST['cod_conta'];
    $stExercicio         = $_REQUEST['exercicio'];
    $stPeriodo           = $_REQUEST['periodo'];
    $stDescricao         = $_REQUEST['descricao'];
    $stValorTotalReceita = str_replace('.', ',', $_REQUEST['valor_total']);
    $obFormulario->addTitulo("Dados para Alteração de Ações da Receita");
    // Cod PPA
    $obHdnCodPPA = new Hidden;
    $obHdnCodPPA->setName ("inCodPPA");
    $obHdnCodPPA->setId   ("inCodPPA");
    $obHdnCodPPA->setValue($inCodPPA );
    // Cod Entidade
    $obHdnCodEntidade = new Hidden;
    $obHdnCodEntidade->setName('inCodEntidade');
    $obHdnCodEntidade->setId('inCodEntidade');
    $obHdnCodEntidade->setValue( $inCodEntidade );
    // Cod Receita Dados
    $obHdnCodReceitaDados = new Hidden;
    $obHdnCodReceitaDados->setName('inCodReceitaDados');
    $obHdnCodReceitaDados->setId('inCodReceitaDados');
    $obHdnCodReceitaDados->setValue( $inCodReceitaDados );
    // Cod Receita
    $obHdnCodReceita = new Hidden;
    $obHdnCodReceita->setName ("inCodReceita");
    $obHdnCodReceita->setId   ("inCodReceita");
    $obHdnCodReceita->setValue($inCodReceita);
    // Cod Conta Receita
    $obHdnCodContaReceita = new Hidden;
    $obHdnCodContaReceita->setName ("inCodConta");
    $obHdnCodContaReceita->setId   ("inCodConta");
    $obHdnCodContaReceita->setValue($inCodConta);
    // Exercicio
    $obHdnExercicio->setValue($stExercicio);
    // Add objs hidden
    $obFormulario->addHidden($obHdnCodPPA);
    $obFormulario->addHidden($obHdnCodReceitaDados);
    $obFormulario->addHidden($obHdnCodEntidade);
    $obFormulario->addHidden($obHdnCodReceita);
    $obFormulario->addHidden($obHdnCodContaReceita);
    $obFormulario->addHidden($obHdnExercicio);
    // Label Exercício PPA
    $obLblExercicioPPA = new Label();
    $obLblExercicioPPA->setName  ('lblExercicioPPA');
    $obLblExercicioPPA->setId    ('lblExercicioPPA');
    $obLblExercicioPPA->setRotulo('Exercício');
    $obLblExercicioPPA->setValue ($stPeriodo);
    $obFormulario->addComponente ($obLblExercicioPPA);
    // Label "Receita" -  descrição
    $obLblDescricaoReceita= new Label();
    $obLblDescricaoReceita->setName  ('lblDescricaoReceita');
    $obLblDescricaoReceita->setId    ('lblDescricaoReceita');
    $obLblDescricaoReceita->setRotulo('Receita');
    $obLblDescricaoReceita->setValue ($stDescricao);
    $obFormulario->addComponente     ($obLblDescricaoReceita);
    // Label Entidade
    $obLblEntidade = new Label();
    $obLblEntidade->setName     ('lblEntidade');
    $obLblEntidade->setId       ('lblEntidade');
    $obLblEntidade->setRotulo   ('Entidade');
    $obLblEntidade->setValue    ($stNomEntidade);
    $obFormulario->addComponente($obLblEntidade);
    // Campo "Total Previsto"
    $obLblTotalPrevisto = new Label();
    $obLblTotalPrevisto->setName  ('lblTotalPrevisto');
    $obLblTotalPrevisto->setId    ('lblTotalPrevisto');
    $obLblTotalPrevisto->setRotulo('Total Previsto');
    $obLblTotalPrevisto->setValue ($stValorTotalReceita);
    $obFormulario->addComponente  ($obLblTotalPrevisto);
    // Incluir o IPopUpNorma somente existir norma vinculada ao PPA (somente quando homologado)
    if ($inCodNorma > 0) {
        // Incluir o IPopUpNorma somente existir norma vinculada ao PPA (somente quando homologado)
        $obIPopUpNorma = new IPopUpNorma();
        $obIPopUpNorma->setExibeDataNorma(true);
        $obIPopUpNorma->obInnerNorma->obCampoCod->stId = 'inCodNorma';
        if ($obVisao->isPPAHomologado($inCodPPA)) {
            $obIPopUpNorma->setExibeDataPublicacao(true);
        }
        $obIPopUpNorma->geraFormulario($obFormulario);
    }
    $obFormulario->addTitulo    ("Dados para Alteração de Fontes de Recurso");
    $obIPopUpRecurso = new IPopUpRecurso($obForm);
    $obIPopUpRecurso->obInnerRecurso->stName = 'stDescricaoRecurso';
    $_REQUEST['destinacao_recurso'] == 't' ? $boDestinacaoRecurso = true
                                           : $boDestinacaoRecurso = false;
    if ($boDestinacaoRecurso) {
        $stRotuloRecurso = 'Recurso Destinação';
    } else {
        $stRotuloRecurso = 'Recurso';
    }
    $obIPopUpRecurso->obInnerRecurso->setRotulo($stRotuloRecurso);
    $obIPopUpRecurso->obInnerRecurso->setTitle($stRotuloRecurso);
    $obIPopUpRecurso->setTitle($stRotuloRecurso);
    $obIPopUpRecurso->setUtilizaDestinacao($boDestinacaoRecurso);
    $obFormulario->addComponente($obIPopUpRecurso);
    // ANO/VALOR
    $obValorTotal = new Radio;
    $obValorTotal->setName   ("boValor");
    $obValorTotal->setId     ("btnTotal");
    $obValorTotal->setRotulo ("Tipo de valor");
    $obValorTotal->setTitle  ("Informe se é valor total ou por ano");
    $obValorTotal->setValue  ("total");
    $obValorTotal->setLabel  ("Valor Total");
    $obValorTotal->setNull   (false);
    $obValorTotal->setChecked(false);
    $obValorTotal->obEvento->setOnChange("montaParametrosGET('montaValorRecurso');");
    // Valor por Ano
    $obValorAno = new Radio;
    $obValorAno->setName ("boValor");
    $obValorAno->setId   ("btnAno");
    $obValorAno->setValue("ano");
    $obValorAno->setLabel("Valor por ano");
    $obValorAno->setTitle("Valor por ano");
    $obValorAno->setNull (false);
    $obValorAno->obEvento->setOnChange("montaParametrosGET('montaValorRecurso');");
    $obFormulario->agrupaComponentes(array($obValorTotal, $obValorAno));
    // Receita total ou por ano (Span 1 ou 2)
    $spnValorReceita = new Span();
    $spnValorReceita->setId("spnValorReceita");
    $obFormulario->addSpan ($spnValorReceita);
    // Btn Incluir Recurso
    $obBtnIncluirRecurso = new Button;
    $obBtnIncluirRecurso->setName ("btnIncluirRecurso");
    $obBtnIncluirRecurso->setValue('Incluir');
    $obBtnIncluirRecurso->obEvento->setOnClick("incluirRecurso();");
    // Btn Limpar Recurso
    $obBtnLimparRecurso = new Button;
    $obBtnLimparRecurso->setName ("btnLimparRecurso");
    $obBtnLimparRecurso->setValue('Limpar');
    $obBtnLimparRecurso->obEvento->setOnClick("limparRecurso();");
    $obFormulario->defineBarra(array($obBtnIncluirRecurso, $obBtnLimparRecurso));
}
// Fontes de Recurso (Span 3)
$spnFonteRecurso = new Span();
$spnFonteRecurso->setId("spnFonteRecurso");
$obFormulario->addSpan ($spnFonteRecurso);
// Total da receita (total de recursos incluídos)
$obLblTotalReceita = new Label();
$obLblTotalReceita->setName  ('lblTotalReceita');
$obLblTotalReceita->setRotulo('Total desta Receita');
$obLblTotalReceita->setId    ('lblTotalReceita');

if ($stAcao == 'incluir') {
    $obLblTotalReceita->setValue('0,00');
} elseif ($stAcao == 'alterar') {
    $obLblTotalReceita->setValue($stValorTotalReceita);
}
$obFormulario->addComponente ( $obLblTotalReceita );

// Valor total de Receitas e Despesas do PPA
$stValorTotalReceitasPPA = '0,00';
$stValorTotalDespesasPPA = '0,00';

if (isset($inCodPPA)) {
    $arParametros = array('inCodPPA'    => $inCodPPA,
                          'stExercicio' => $stExercicio);

    $stValorTotalDespesasPPA = $obVisao->recuperaValorTotalPPA($arParametros, false);
    $stValorTotalReceitasPPA = $obVisao->recuperaValorTotalReceita($arParametros, false);
}
// Total de todas as Receitas no PPA
$obLblTotalReceitasPPA = new Label();
$obLblTotalReceitasPPA->setName  ('lblTotalReceitasPPA');
$obLblTotalReceitasPPA->setRotulo('Total de Receitas no PPA');
$obLblTotalReceitasPPA->setId    ('lblTotalReceitasPPA');
$obLblTotalReceitasPPA->setValue ($stValorTotalReceitasPPA);
$obFormulario->addComponente     ( $obLblTotalReceitasPPA);

// Total de todas as Receitas no PPA
$obLblTotalDespesasPPA = new Label();
$obLblTotalDespesasPPA->setName('lblTotalDespesasPPA');
$obLblTotalDespesasPPA->setRotulo('Total de Ações no PPA');
$obLblTotalDespesasPPA->setId('lblTotalDespesasPPA');
$obLblTotalDespesasPPA->setValue($stValorTotalDespesasPPA);
$obFormulario->addComponente($obLblTotalDespesasPPA);

// BOTÕES DE AÇÃO DO FORMULÁRIO (OK/LIMPAR)
$obBtnOk = new Ok();
$obBtnOk->obEvento->setOnClick('SalvarReceita();');
// Botão Limpar
$obBtnLimpar = new Button;
$obBtnLimpar->setName ('btnLimpar');
$obBtnLimpar->setValue('Limpar');
// Botão Cancelar
$obBtnCancelar = new Button;
$obBtnCancelar->setName ('btnCancelar');
$obBtnCancelar->setValue('Cancelar');
$obBtnCancelar->obEvento->setOnClick("CancelarCL();");
if ($stAcao == 'incluir') {
    $obBtnLimpar->obEvento->setOnClick('limparFormIncluiReceita();');
}

$arBtnForm   = array();
$arBtnForm[] = $obBtnOk;

if ($stAcao == 'incluir') {
    $arBtnForm[] = $obBtnLimpar;
} else {
    $arBtnForm[] = $obBtnCancelar;
}
$obFormulario->defineBarra($arBtnForm);
$obFormulario->show();
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
if ($stAcao == 'alterar') {
    sistemaLegado::executaFrameOculto("montaParametrosGET('montaListaAlteraRecurso')");
}
?>
