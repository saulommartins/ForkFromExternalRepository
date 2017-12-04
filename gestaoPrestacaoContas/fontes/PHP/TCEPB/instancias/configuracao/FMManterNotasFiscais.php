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
/*
    * Formulário de Cadastro de Notas Fiscais
    * Data de Criação   : 17/09/2008

    * @author Analista      Tonismar Régis Bernardo
    * @author Desenvolvedor Alexandre Melo

    * @package URBEM
    * @subpackage

    * @ignore

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoFinanceira/fontes/PHP/orcamento/classes/componentes/ITextBoxSelectEntidadeUsuario.class.php';

$stPrograma = "ManterNotasFiscais";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');

//*****************************************************//
// Define COMPONENTES DO FORMULARIO
//*****************************************************//

//Instancia o formulário
$obForm = new Form;
$obForm->setAction($pgProc);
$obForm->setTarget("oculto");

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ("stAcao");
$obHdnAcao->setValue($_REQUEST['stAcao']);

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ("stCtrl");
$obHdnCtrl->setValue("");

$obHdnDataEmissao = new Hidden;
$obHdnDataEmissao->setName ("data_emissao");
$obHdnDataEmissao->setValue($_REQUEST['data_emissao']);

$obHdnCodNota = new Hidden;
$obHdnCodNota->setName ("inCodNota");
$obHdnCodNota->setValue($_REQUEST['inCodNota']);

$obHdnCodNotaLiquidacao = new Hidden;
$obHdnCodNotaLiquidacao->setName ("inCodNotaLiquidacao");
$obHdnCodNotaLiquidacao->setValue($_REQUEST['inCodNotaLiquidacao']);

$obHdnInscEstadual = new Hidden;
$obHdnInscEstadual->setName ("insc_estadual");
$obHdnInscEstadual->setValue($_REQUEST['insc_estadual']);

$obHdnVlTotal = new Hidden;
$obHdnVlTotal->setName ("nuVlTotal");
$obHdnVlTotal->setValue($_REQUEST['nuVlTotal']);

$obHdnDtLiquidacao = new Hidden;
$obHdnDtLiquidacao->setName ("dtLiquidacao");
$obHdnDtLiquidacao->setValue($_REQUEST['dtLiquidacao']);

$obTxtNota = new TextBox;
$obTxtNota->setName     ("inNumNota");
$obTxtNota->setId       ("inNumNota");
$obTxtNota->setValue    ($_REQUEST['inNumNota']);
$obTxtNota->setRotulo   ("Número da Nota");
$obTxtNota->setTitle    ("Informe o número da nota.");
$obTxtNota->setNull     (false);
$obTxtNota->setInteiro  (true);
$obTxtNota->setSize     (10);
$obTxtNota->setMaxLength(10);

$obEntidadeUsuario = new ITextBoxSelectEntidadeUsuario;
$obEntidadeUsuario->setNull(false);
if ($stAcao == 'alterar') {
    $obEntidadeUsuario->setCodEntidade($_REQUEST['inCodEntidade']);
    $obEntidadeUsuario->setLabel(true);
}

$obHdnCodEntidade = new Hidden;
$obHdnCodEntidade->setId   ("cod_entidade");
$obHdnCodEntidade->setName ("cod_entidade");
$obHdnCodEntidade->setValue($_REQUEST['cod_entidade']);

$obTxtSerie = new TextBox;
$obTxtSerie->setName     ("inNumSerie");
$obTxtSerie->setId       ("inNumSerie");
$obTxtSerie->setValue    ($_REQUEST['inNroSerie']);
$obTxtSerie->setRotulo   ("Série da Nota Fiscal");
$obTxtSerie->setTitle    ("Informe a série da nota.");
$obTxtSerie->setNull     (false);
$obTxtSerie->setInteiro  (false);
$obTxtSerie->setSize     (8);
$obTxtSerie->setMaxLength(8);

$obTxtAIDF = new TextBox;
$obTxtAIDF->setName     ("stAIFD");
$obTxtAIDF->setId       ("stAIDF");
$obTxtAIDF->setValue    ($_REQUEST['stAIDF']);
$obTxtAIDF->setRotulo   ("Número da AIDF");
$obTxtAIDF->setTitle    ("Informe o número da AIDF.");
$obTxtAIDF->setNull     (false);
$obTxtAIDF->setInteiro  (false);
$obTxtAIDF->setSize     (18);
$obTxtAIDF->setMaxLength(15);

$obDtEmissao = new Data;
$obDtEmissao->setName     ("dtEmissao");
$obDtEmissao->setId       ("dtEmissao");
$obDtEmissao->setRotulo   ("Data de Emissão");
$obDtEmissao->setValue    ($_REQUEST['dtEmissao']);
$obDtEmissao->setTitle    ('Informe a data de emissão.');
$obDtEmissao->setNull     (false);
$obDtEmissao->setSize     (10);
$obDtEmissao->setMaxLength(10);

$obTxtIncricaoMunicipal = new TextBox;
$obTxtIncricaoMunicipal->setName     ("inNumInscricaoMunicipal");
$obTxtIncricaoMunicipal->setId       ("inNumInscricaoMunicipal");
$obTxtIncricaoMunicipal->setValue    ($_REQUEST['inNumInscricaoMunicipal']);
$obTxtIncricaoMunicipal->setRotulo   ("Inscrição Municipal");
$obTxtIncricaoMunicipal->setTitle    ("Informe o número da Inscrição Municipal.");
$obTxtIncricaoMunicipal->setNull     (true);
$obTxtIncricaoMunicipal->setInteiro  (true);
$obTxtIncricaoMunicipal->setSize     (18);
$obTxtIncricaoMunicipal->setMaxLength(15);

$obTxtIncricaoEstadual = new TextBox;
$obTxtIncricaoEstadual->setName     ("inNumInscricaoEstadual");
$obTxtIncricaoEstadual->setId       ("inNumInscricaoEstadual");
$obTxtIncricaoEstadual->setValue    ($_REQUEST['inNumInscricaoEstadual']);
$obTxtIncricaoEstadual->setRotulo   ("Inscrição Estadual");
$obTxtIncricaoEstadual->setTitle    ("Informe o número da Inscrição Estadual.");
$obTxtIncricaoEstadual->setNull     (true);
$obTxtIncricaoEstadual->setInteiro  (true);
$obTxtIncricaoEstadual->setSize     (18);
$obTxtIncricaoEstadual->setMaxLength(15);

$obTxtExercicio = new TextBox;
$obTxtExercicio->setName     ("stExercicioEmpenho");
$obTxtExercicio->setRotulo   ("Exercício");
$obTxtExercicio->setTitle    ("Informe o exercício.");
$obTxtExercicio->setInteiro  (false);
$obTxtExercicio->setNull     (false);
$obTxtExercicio->setMaxLength(4);
$obTxtExercicio->setSize     (5);
if ($stAcao == 'alterar') {
    $obTxtExercicio->setValue($_REQUEST['stExercicio']);
    $obTxtExercicio->setLabel(true);
} else {
    $obTxtExercicio->setValue(Sessao::getExercicio());
}

$obBscEmpenho = new BuscaInner;
$obBscEmpenho->setTitle           ("Informe o número do empenho.");
$obBscEmpenho->setRotulo          ("Número do Empenho");
$obBscEmpenho->setId              ("stEmpenho");
$obBscEmpenho->setMostrarDescricao(true);
$obBscEmpenho->obCampoCod->setName("numEmpenho");
$obBscEmpenho->obCampoCod->setId  ("numEmpenho");
$obBscEmpenho->setObrigatorio     (true);
$stJsBlur = "montaParametrosGET('preencheInner','numEmpenho, inCodEntidade, stExercicioEmpenho, dtEmissao, stAcao');";
$stJsBusca = "
    if (jq('input#inCodEntidade').val() == '') {
        alertaAviso('Preencha o campo entidade!','form','erro','".Sessao::getId()."');
    } else {
        abrePopUp('".CAM_GF_EMP_POPUPS."empenho/FLProcurarEmpenho.php','frm','numEmpenho','stEmpenho','empenhoComplementar&inCodigoEntidade='+document.frm.inCodEntidade.value + '&dtFinal='+document.frm.dtEmissao.value + '&dtEmissao='+document.frm.dtEmissao.value,'".Sessao::getId()."','800','550');
    }
";
$obBscEmpenho->setFuncaoBusca($stJsBusca);
$obBscEmpenho->obCampoCod->obEvento->setOnBlur($stJsBlur);
$obBscEmpenho->obImagem->obEvento->setOnBlur  ($stJsBlur);

if ($stAcao == 'alterar') {
    $obBscEmpenho->obCampoCod->setValue($_REQUEST['inCodEmpenho'].'/'.$_REQUEST['stExercicio']);
    $obBscEmpenho->setLabel(true);
}

$spnLista = new Span;
$spnLista->setId('spnLista');

$obSpanLiquidacao = new Span;
$obSpanLiquidacao->setId('spanLiquidacao');

$obSpanAlterarNota = new Span;
$obSpanAlterarNota->setId('spanAlteraLiquidacao');

$obLabelTotalLiquidacao = new Label;
$obLabelTotalLiquidacao->setId    ('labelTotalLiquidacao');
$obLabelTotalLiquidacao->setRotulo("Valor Associado:");
$obLabelTotalLiquidacao->setValue ("&nbsp;");

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm  ($obForm);
$obFormulario->addHidden($obHdnAcao);
$obFormulario->addHidden($obHdnCtrl);
$obFormulario->addHidden($obHdnDataEmissao);
$obFormulario->addHidden($obHdnCodEntidade);
$obFormulario->addHidden($obHdnCodNotaLiquidacao);
$obFormulario->addHidden($obHdnCodNota);
$obFormulario->addHidden($obHdnDtLiquidacao);
$obFormulario->addHidden($obHdnInscEstadual);
$obFormulario->addHidden($obHdnVlTotal);

if (Sessao::getExercicio() < 2009) {
    $obFormulario->addTitulo("Dados dos empenhos do contrato");
} else {
    $obFormulario->addTitulo("Dados do empenho");
}
$obFormulario->addComponente($obTxtExercicio);
$obFormulario->addComponente($obEntidadeUsuario);
$obFormulario->addComponente($obBscEmpenho);
$obFormulario->addSpan      ($obSpanLiquidacao);
$obFormulario->addComponente($obLabelTotalLiquidacao);
$obFormulario->addSpan      ($obSpanAlterarNota);

if (Sessao::getExercicio() >= 2009) {
    $obFormulario->addTitulo("Inclusão Nota Fiscal");
} else {
    $obFormulario->addComponente($obTxtAIDF);
    $obFormulario->addComponente($obTxtIncricaoMunicipal);
    $obFormulario->addComponente($obTxtIncricaoEstadual);
}

$obFormulario->addComponente($obTxtNota);
$obFormulario->addComponente($obTxtSerie);
$obFormulario->addComponente($obDtEmissao);
$obFormulario->addSpan($spnLista);

if ($_REQUEST['stAcao'] == 'incluir') {
    $obFormulario->Cancelar($pgForm.'?'.Sessao::getId().'&stAcao='.$_REQUEST['stAcao'] );
} elseif ($_REQUEST['stAcao'] == 'alterar') {
    $obFormulario->Cancelar($pgFilt.'?'.Sessao::getId().'&stAcao='.$_REQUEST['stAcao'] );
}

$obFormulario->show();

if ($stAcao == 'alterar') {
    $jsOnload .= "montaParametrosGET('preencheInner','numEmpenho, inCodEntidade, stExercicioEmpenho, dtEmissao, dtLiquidacao, inCodNotaLiquidacao,stAcao');";
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
