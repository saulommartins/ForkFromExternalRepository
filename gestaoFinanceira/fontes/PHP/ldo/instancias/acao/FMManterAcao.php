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
 * Página de Formulário do 02.10.03 - Manter Ação
 * Data de Criação: 16/02/2009
 * Copyright CNM - Confederação Nacional de Municípios
 *
 * @author Fellipe Esteves dos Santos <fellipe.santos>
 * @package GF
 * @subpackage LDO
 * @uc 02.10.03 - Manter Ação
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/Table.class.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_LDO_VISAO          . 'VLDOManterLDO.class.php';
include_once CAM_GF_LDO_VISAO          . 'VLDOManterAcao.class.php';
include_once CAM_GF_LDO_COMPONENTES    . 'IPopUpAcao.class.php';
include_once CAM_GF_LDO_COMPONENTES    . 'IPopUpRubrica.class.php';
include_once CAM_GF_PPA_COMPONENTES    . 'IPopUpFuncao.class.php';
include_once CAM_GF_PPA_COMPONENTES    . 'IPopUpSubFuncao.class.php';
include_once CAM_GF_PPA_COMPONENTES    . 'IPopUpRecurso.class.php';
include_once CAM_GF_PPA_COMPONENTES    . 'MontaOrgaoUnidade.class.php';
include_once CAM_GF_PPA_COMPONENTES    . 'ITextBoxSelectTipoAcao.class.php';
include_once CAM_GF_ORC_COMPONENTES    . 'ITextBoxSelectEntidadeGeral.class.php';
include_once CAM_GA_NORMAS_COMPONENTES . 'IPopUpNorma.class.php';

VLDOManterLDO::recuperarInstancia()->recuperarPPA();

$rsLDO = VLDOManterLDO::recuperarInstancia()->recuperarLDO();

$stAcao = $_GET['stAcao'] ? $_GET['stAcao'] : $_POST['stAcao'];

if (empty($stAcao)) {
    $stAcao = 'incluir';
}

switch ($stAcao) {
    case 'incluir':
        $stTitulo = 'Dados para Cadastro da Ação - '.$rsLDO->getCampo('ano');
    break;
    case 'alterar':
        $stTitulo = 'Dados para Alteração -'.$rsLDO->getCampo('ano');
    break;
    case 'excluir':
        $stTitulo = 'Dados para Exclusão - '.$rsLDO->getCampo('ano');
    break;
}

$stProjeto = 'ManterAcao';
$pgFilt    = 'FL' . $stProjeto . '.php';
$pgList    = 'LS' . $stProjeto . '.php';
$pgForm    = 'FM' . $stProjeto . '.php';
$pgProc    = 'PR' . $stProjeto . '.php';
$pgOcul    = 'OC' . $stProjeto . '.php';
$pgJS      = 'JS' . $stProjeto . '.php';

$arParametros = array();

$obForm = new Form();
$obForm->setAction($pgProc);
$obForm->setTarget('oculto');
$obForm->setEncType('multipart/form-data');

$obFormulario = new Formulario();
$obFormulario->addForm($obForm);

$obHdnAcao = new Hidden();
$obHdnAcao->setName('stAcao');
$obHdnAcao->setValue($stAcao);
$obFormulario->addHidden($obHdnAcao);

$obHdnCtrl = new Hidden();
$obHdnCtrl->setName('stCtrl');
$obHdnCtrl->setValue($stCtrl);
$obFormulario->addHidden($obHdnCtrl);

$arParametros['stAno'] = $rsLDO->getCampo('ano');

$obFormulario->addTitulo($stTitulo);

if ($stAcao == 'alterar') {
    Sessao::write('link', null);

    $rsAcaoPPA = VLDOManterAcao::recuperarInstancia()->recuperarAcaoPPA($_REQUEST);

    $obLblNumAcao = new Label();
    $obLblNumAcao->setRotulo('Acao');
    $obLblNumAcao->setTitle('Informe o Ação.');
    $obLblNumAcao->setValue($rsAcaoPPA->getCampo('cod_acao'));
    $obFormulario->addComponente($obLblNumAcao);

    $obHdnCodAcao = new Hidden();
    $obHdnCodAcao->setName('inCodAcao');
    $obHdnCodAcao->setValue($_REQUEST['inCodAcao']);
    $obFormulario->addHidden($obHdnCodAcao);

    $obHdnCodAcaoPPA = new Hidden();
    $obHdnCodAcaoPPA->setName('inCodAcaoPPA');
    $obHdnCodAcaoPPA->setValue($rsAcaoPPA->getCampo('cod_acao'));
    $obFormulario->addHidden($obHdnCodAcaoPPA);

    $arParametros['inCodPPA']        = $rsAcaoPPA->getCampo('cod_ppa');
    $arParametros['inNumAcao']       = $rsAcaoPPA->getCampo('num_acao');
    $arParametros['inCodAcaoPPA']    = $rsAcaoPPA->getCampo('cod_acao');
    $arParametros['inNumPrograma']   = $rsAcaoPPA->getCampo('num_programa');
    $arParametros['stNomPrograma']   = $rsAcaoPPA->getCampo('identificacao');
    $arParametros['stDiagnostico']   = $rsAcaoPPA->getCampo('diagnostico');
    $arParametros['stObjetivo']      = $rsAcaoPPA->getCampo('objetivo');
    $arParametros['stDiretrizes']    = $rsAcaoPPA->getCampo('diretriz');
    $arParametros['stPublico']       = $rsAcaoPPA->getCampo('publico_alvo');
    $arParametros['stNatureza']      = $rsAcaoPPA->getCampo('continuo') ? 'Continuo' : 'Temporário';
    $arParametros['inCodFuncao']     = $rsAcaoPPA->getCampo('cod_funcao');
    $arParametros['stNomFuncao']     = $rsAcaoPPA->getCampo('desc_funcao');
    $arParametros['inCodSubfuncao']  = $rsAcaoPPA->getCampo('cod_subfuncao');
    $arParametros['stNomSubfuncao']  = $rsAcaoPPA->getCampo('desc_subfuncao');
    $arParametros['inCodTipoAcao']   = $rsAcaoPPA->getCampo('cod_tipo');

    $rsAcaoLDO = VLDOManterAcao::recuperarInstancia()->recuperarAcaoLDO($_REQUEST);

    $arParametros['stListaPrograma'] = VLDOManterAcao::recuperarInstancia()->montarPrograma($arParametros);
    $arParametros['stListaRecurso']  = VLDOManterAcao::recuperarInstancia()->montarRecurso($_REQUEST);

    $arParametros['inCodOrgao']      = $rsAcaoLDO->getCampo('num_orgao');
    $arParametros['inCodEntidade']   = $rsAcaoLDO->getCampo('cod_entidade');
    $arParametros['inCodUnidade']    = $rsAcaoLDO->getCampo('num_unidade');
    $arParametros['inCodConta']      = $rsAcaoLDO->getCampo('cod_conta');
    $arParametros['stNomConta']      = $rsAcaoLDO->getCampo('nom_conta');
    $arParametros['stAno']           = $rsAcaoLDO->getCampo('ano');

    $arParametros['stUnidadeOrcamentaria'] = $rsAcaoLDO->getCampo('unidade_orcamentaria');
} else {
    $obIPopUpAcao = new IPopUpAcao($obForm);
    $obIPopUpAcao->setTitle('Informe a Ação');
    $obIPopUpAcao->setExibePrograma(true);
    $obIPopUpAcao->setNull(false);
    $obIPopUpAcao->geraFormulario($obFormulario);
}

$arParametros['flTotalAcoes']   = VLDOManterAcao::recuperarInstancia()->montarTotalAcoes($arParametros);
$arParametros['flTotalReceita'] = VLDOManterAcao::recuperarInstancia()->montarTotalReceita($arParametros);

$obHdnAno = new Hidden();
$obHdnAno->setName('stAno');
$obHdnAno->setValue($arParametros['stAno']);
$obFormulario->addHidden($obHdnAno);

$obSpnPrograma = new Span;
$obSpnPrograma->setID("spnPrograma");
$obSpnPrograma->setValue($arParametros['stListaPrograma']);
$obFormulario->addSpan($obSpnPrograma);

if (VLDOManterLDO::recuperarInstancia()->recuperarLDOHomologado()) {
    $obIPopUpNorma = new IPopUpNorma();
    $obIPopUpNorma->obInnerNorma->setTitle('Informe a norma.');
    $obIPopUpNorma->obInnerNorma->setRotulo('Norma');
    $obIPopUpNorma->obInnerNorma->obCampoCod->stId = 'inCodNorma';
    $obIPopUpNorma->obLblDataNorma->setRotulo('*Data da Norma');
    $obIPopUpNorma->obLblDataNorma->setTitle('Data da norma');
    $obIPopUpNorma->setExibeDataNorma(true);
    $obIPopUpNorma->setExibeDataPublicacao(true);
    $obIPopUpNorma->geraFormulario($obFormulario);
}

$obFormulario->addTitulo('Classificação Institucional');

$obITextBoxSelectEntidade = new ITextBoxSelectEntidadeGeral();
$obITextBoxSelectEntidade->setNull(false);
$obITextBoxSelectEntidade->setCodEntidade($arParametros['inCodEntidade']);
$obFormulario->addComponente($obITextBoxSelectEntidade);

$obMontaOrgaoUnidade = new MontaOrgaoUnidade();
$obMontaOrgaoUnidade->setRotulo('Informe a unidade responsável');
$obMontaOrgaoUnidade->setValue($arParametros['stUnidadeOrcamentaria']);
$obMontaOrgaoUnidade->setCodOrgao($arParametros['inCodOrgao']);
$obMontaOrgaoUnidade->setCodUnidade($arParametros['inCodUnidade']);
$obMontaOrgaoUnidade->setActionPosterior($pgProc);
$obMontaOrgaoUnidade->setTarget('oculto');
$obMontaOrgaoUnidade->setNull(false);
$obMontaOrgaoUnidade->geraFormulario($obFormulario);

$obFormulario->addTitulo('Classificação Econômica');

$obIPopUpRubrica = new IPopUpRubrica();
$obIPopUpRubrica->setTitle('Informa a rubrica');
$obIPopUpRubrica->setRotulo('*Rubrica');
$obIPopUpRubrica->obCampoCod->setValue($arParametros['inCodConta']);
$obIPopUpRubrica->setValue($arParametros['stNomConta']);
$obIPopUpRubrica->setDedutora(false);
$obIPopUpRubrica->setTipoDedutora('despesa');
$obIPopUpRubrica->geraFormulario($obFormulario);

$obIPopUpRecurso = new IPopUpRecurso($obForm);
$obIPopUpRecurso->obInnerRecurso->setTitle('Informe o recurso');
$obIPopUpRecurso->obInnerRecurso->setRotulo('*Recurso');
$obIPopUpRecurso->geraFormulario($obFormulario);

$obNumValorRecurso = new Numerico;
$obNumValorRecurso->setRotulo    ('*Valor');
$obNumValorRecurso->setTitle     ('Informe o valor do recurso');
$obNumValorRecurso->setName      ('flValorRecurso');
$obNumValorRecurso->setId        ('flValorRecurso');
$obNumValorRecurso->setDecimais  (2);
$obNumValorRecurso->setMaxValue  (999999999999.99);
$obNumValorRecurso->setNegativo  (false);
$obNumValorRecurso->setNaoZero   (false);
$obNumValorRecurso->setSize      (20);
$obNumValorRecurso->setMaxLength (12);
$obFormulario->addComponente($obNumValorRecurso);

$obBtnIncluir = new Button();
$obBtnIncluir->setName('btnIncluir');
$obBtnIncluir->setValue('Incluir');
$obBtnIncluir->obEvento->setOnClick("inserirRecurso()");

$obBtnLimpar = new Button();
$obBtnLimpar->setName('btnLimpar');
$obBtnLimpar->setValue('Limpar');
$obBtnLimpar->obEvento->setOnClick('limparRecurso()');

$arBotoes = array($obBtnIncluir, $obBtnLimpar);
$obFormulario->defineBarra($arBotoes);

$obSpnListaRecurso = new Span();
$obSpnListaRecurso->setID('spnListaRecurso');
$obSpnListaRecurso->setValue($arParametros['stListaRecurso']);
$obFormulario->addSpan($obSpnListaRecurso);

$obHdnTotalAcoes = new Hidden();
$obHdnTotalAcoes->setId('flTotalAcoes');
$obHdnTotalAcoes->setName('flTotalAcoes');
$obHdnTotalAcoes->setValue($arParametros['flTotalAcoes']);
$obFormulario->addHidden($obHdnTotalAcoes);

$obHdnTotalReceita = new Hidden();
$obHdnTotalReceita->setId('flTotalReceita');
$obHdnTotalReceita->setName('flTotalReceita');
$obHdnTotalReceita->setValue($arParametros['flTotalReceita']);
$obFormulario->addHidden($obHdnTotalReceita);

$obHdnTotalAcao = new Hidden();
$obHdnTotalAcao->setId('flTotalAcao');
$obHdnTotalAcao->setName('flTotalAcao');
$obHdnTotalAcao->setValue('0.0');
$obFormulario->addHidden($obHdnTotalAcao);

$obLblTotalAcao = new Label();
$obLblTotalAcao->setId('lbTotalAcao');
$obLblTotalAcao->setTitle('Total da ação');
$obLblTotalAcao->setRotulo('Total da Ação');
$obLblTotalAcao->setValue('0');
$obFormulario->addComponente($obLblTotalAcao);

$obHdnTotalDisponivel = new Hidden();
$obHdnTotalDisponivel->setId('flTotalDisponivel');
$obHdnTotalDisponivel->setName('flTotalDisponivel');
$obHdnTotalDisponivel->setValue($arParametros['flTotalReceita']);
$obFormulario->addHidden($obHdnTotalDisponivel);

$obLblTotalDisponivel = new Label();
$obLblTotalDisponivel->setId('lbTotalDisponivel');
$obLblTotalDisponivel->setTitle('Total da receita disponivel');
$obLblTotalDisponivel->setRotulo('Total da Receita Disponivel');
$obLblTotalDisponivel->setValue($arParametros['flTotalReceita']);
$obFormulario->addComponente($obLblTotalDisponivel);

$obHdnDiferenca = new Hidden();
$obHdnDiferenca->setId('flTotalDiferenca');
$obHdnDiferenca->setName('flTotalDiferenca');
$obHdnDiferenca->setValue(0.0);
$obFormulario->addHidden($obHdnDiferenca);

$obLblDiferenca = new Label();
$obLblDiferenca->setId('lbTotalDiferenca');
$obLblDiferenca->setTitle('Diferença a lançar');
$obLblDiferenca->setRotulo('Diferença a Lançar');
$obFormulario->addComponente($obLblDiferenca);

$obBtnOK = new Ok(true);

if ($stAcao == 'incluir') {
    $obBtnLimpar = new Button();
    $obBtnLimpar->setValue('Limpar');
    $obBtnLimpar->obEvento->setOnClick('limparAcao()');
} else {
    $obBtnLimpar = new Cancelar();
    $obBtnLimpar->obEvento->setOnClick('cancelarAcao();');
}

$arBotoes = array($obBtnOK, $obBtnLimpar);

$obFormulario->defineBarra($arBotoes);

$obFormulario->show();

$jsOnLoad = 'atualizarValores(true);';

include_once $pgJS;

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
