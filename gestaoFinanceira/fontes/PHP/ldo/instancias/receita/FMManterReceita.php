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
 * Pagina de formulario tipo do uc-02.10.04
 * Data de Criação: 17/02/209
 * Copyright CNM - Confederação Nacional de Municípios
 *
 * @author <analista> Bruno Ferreira Santos <bruno.ferreira>
 * @author <desenvolvedor> Jânio Eduardo Vasconcellos de Magalhães <janio.magalhaes>
 * @package GF
 * @subpackage ldo
 * @uc uc-02.10.04
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GA_NORMAS_COMPONENTES . 'IPopUpNorma.class.php';
include_once CAM_GF_LDO_COMPONENTES    . 'IPopUpReceita.class.php';
include_once CAM_GF_LDO_VISAO          . 'VLDOManterReceita.class.php';
include_once CAM_GF_LDO_VISAO          . 'VLDOManterLDO.class.php';
include_once CAM_GF_PPA_VISAO          . 'VPPAUtils.class.php';
include_once CAM_GF_LDO_VISAO          . 'VLDOManterLDO.class.php';

VLDOManterLDO::recuperarInstancia()->recuperarPPA();

$obVPPAUtils = new VPPAUtils;
//Define o nome dos arquivos PHP
$stPrograma = "ManterReceita";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".php";

include_once $pgJS;

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];

if (empty($stAcao)) {
    die('Erro: ação não definida em FLManterReceita!');
}

$rsLDO = VLDOManterLDO::recuperarInstancia()->recuperarLDO();

$obHdnAnoLDO = new Hidden();
$obHdnAnoLDO->setName('stAnoLDO');
$obHdnAnoLDO->setValue($rsLDO->getCampo('ano'));

if ($stAcao=='alterar') {
    $obHdnNumReceita = new Hidden();
    $obHdnNumReceita->setName('inNumReceita');
    $obHdnNumReceita->setValue($_REQUEST['inNumReceita']);

    $obHdnCodReceita = new Hidden();
    $obHdnCodReceita->setName('inCodReceita');
    $obHdnCodReceita->setValue($_REQUEST['inCodReceita']);

    $obHdnCodPPA = new Hidden();
    $obHdnCodPPA->setName('inCodPPA');
    $obHdnCodPPA->setValue($_REQUEST['inCodPPA']);

    $obHdnDescricaoReceita = new Hidden();
    $obHdnDescricaoReceita->setName('stReceitaDados');
    $obHdnDescricaoReceita->setValue($_REQUEST['inReceitaDados']);

    $obLblTlDescReceita = new Label();
    $obLblTlDescReceita->setRotulo('Receita');
    $obLblTlDescReceita->setTitle('Descricao da Receita');
    $obLblTlDescReceita->setID('lbReceita');
    $obLblTlDescReceita->setValue($_REQUEST['inNumReceita'].' - '.$_REQUEST['stDescricao']);

    $obLblTlTotalReceita = new Label();
    $obLblTlTotalReceita->setRotulo('Total Previsto');
    $obLblTlTotalReceita->setTitle('Total Previsto');
    $obLblTlTotalReceita->setID('lbPrevisto');
    $obLblTlTotalReceita->setValue($_REQUEST['inTotalReceita']);
    $stJs  = VLDOManterReceita::recuperarInstancia()->exibirRecurso($_REQUEST);
    $stJs .= VLDOManterReceita::recuperarInstancia()->listarRecurso($_REQUEST);
}

# Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" ); //oculto - telaPrincipal

$obIPopUpReceita = new IPopUpReceita($obForm);
$obIPopUpReceita->obInnerReceita->setNull(false);
$obIPopUpReceita->setExibeRecurso(true);
$obIPopUpReceita->setExibeValorReceita(true);
$obIPopUpReceita->obInnerReceita->obCampoCod->obEvento->setOnBlur("exibirRecurso();");

$obHdnCodReceitaLista =  new Hidden;
$obHdnCodReceitaLista->setName ('inCodReceitaLista');
$obHdnCodReceitaLista->setID('inCodReceitaLista');
$obHdnCodReceitaLista->setValue($_REQUEST['inNumReceita']);

$obHdnAcao = new Hidden();
$obHdnAcao->setName('stAcao');
$obHdnAcao->setValue($stAcao);

//Informar Valor recurso
$obTxtValorRecurso	 = new Numerico;
$obTxtValorRecurso->setRotulo    ('*Valor Recurso');
$obTxtValorRecurso->setTitle     ('Valor Recurso');
$obTxtValorRecurso->setName      ('flValorRecurso');
$obTxtValorRecurso->setId        ('flValorRecurso');
$obTxtValorRecurso->setDecimais  (2);
$obTxtValorRecurso->setMaxValue  (999999999999.99);
$obTxtValorRecurso->setNull      (true);
$obTxtValorRecurso->setNegativo  (false);
$obTxtValorRecurso->setNaoZero   (false);
$obTxtValorRecurso->setSize      (20);
$obTxtValorRecurso->setMaxLength (12);

//botoes do CGM servidor
$obBtnIncluirRecurso = new Button;
$obBtnIncluirRecurso->setName              ('btnIncluirRecurso');
$obBtnIncluirRecurso->setValue             ('Incluir');
$obBtnIncluirRecurso->setTipo              ('button');
$obBtnIncluirRecurso->obEvento->setOnClick ("inserirRecurso();" );
$obBtnIncluirRecurso->setDisabled          (false);

$obBtnLimparRecurso = new Button;
$obBtnLimparRecurso->setName               ('btnLimparRecurso');
$obBtnLimparRecurso->setValue              ('Limpar');
$obBtnLimparRecurso->setTipo               ('button');
$obBtnLimparRecurso->obEvento->setOnClick  ("limparRecurso();");
$obBtnLimparRecurso->setDisabled           (false);

$botoesRecurso = array ( $obBtnIncluirRecurso , $obBtnLimparRecurso);

$obLblTlReceita = new Label();
$obLblTlReceita->setRotulo('Total da Receita');
$obLblTlReceita->setTitle('Total da Receita');
$obLblTlReceita->setID('lbTotalReceita');
if ($_REQUEST['inValorTotal']) {
    $obLblTlReceita->setValue($obVPPAUtils->floatToStr($_REQUEST['inValorTotal']));
} else $obLblTlReceita->setValue('0');

$obLblTlLancado = new Label();
$obLblTlLancado->setRotulo('Total Lançado');
$obLblTlLancado->setTitle('Total Lançado');
$obLblTlLancado->setID('lbTotalRecurso');
if ($_REQUEST['inTotalLancado']) {
    $obLblTlLancado->setValue($obVPPAUtils->floatToStr($_REQUEST['inTotalLancado']));
} else $obLblTlLancado->setValue('0');

$arLabelTotais = array ( $obLblTlReceita , $obLblTlLancado);

$obSpnRecurso = new Span();
$obSpnRecurso->setID('spnRecurso');

$obSpnListaRecurso = new Span();
$obSpnListaRecurso->setID('spnListaRecurso');

$obFormulario = new Formulario();
$obFormulario->addForm($obForm);

if ($stAcao=='alterar') {
    $obFormulario->addTitulo('Dados para Alteração da receita - '.$rsLDO->getCampo('ano'));
    $obFormulario->addHidden($obHdnNumReceita);
    $obFormulario->addHidden($obHdnCodReceita);
    $obFormulario->addHidden($obHdnCodPPA);
    $obFormulario->addComponente($obLblTlDescReceita);
    $obFormulario->addComponente($obLblTlTotalReceita);
} else {
    $obFormulario->addTitulo('Dados para Cadastro da receita da LDO - '.$rsLDO->getCampo('ano'));
    $obIPopUpReceita->geraFormulario  ($obFormulario);
}

if (VLDOManterLDO::recuperarInstancia()->recuperarLDOHomologado()) {
    $obIPopUpNorma = new IPopUpNorma();
    $obIPopUpNorma->obInnerNorma->setTitle('Define norma.');
    $obIPopUpNorma->obInnerNorma->obCampoCod->stId = 'inCodNorma';
    $obIPopUpNorma->obInnerNorma->setRotulo("Número da Norma");
    $obIPopUpNorma->obLblDataNorma->setRotulo( "Data da Norma" );
    $obIPopUpNorma->obLblDataPublicacao->setRotulo( "Data da Publicação" );
    $obIPopUpNorma->setExibeDataNorma(true);
    $obIPopUpNorma->geraFormulario($obFormulario);
}

$obFormulario->addHidden($obHdnCodReceitaLista);
$obFormulario->addHidden($obHdnAcao);
$obFormulario->addHidden($obHdnAnoLDO);
$obFormulario->addSpan($obSpnRecurso);
$obFormulario->addSpan($obSpnRecurso);
$obFormulario->addSpan($obSpnListaRecurso);
$obFormulario->addComponente($obLblTlReceita);
$obFormulario->addComponente($obLblTlLancado);
$obBtnOK = new Ok;

if ($stAcao == 'incluir') {
    $obBtnLimpar = new Button();
    $obBtnLimpar->setValue('Limpar');
    $obBtnLimpar->obEvento->setOnClick('limparReceita()');
} else {
    $obBtnLimpar = new Cancelar();
    $obBtnLimpar->obEvento->setOnClick('cancelarReceita();');
}

$arBotoes = array($obBtnOK, $obBtnLimpar);

$obFormulario->defineBarra($arBotoes);

$obFormulario->show();
if ($stAcao=='alterar') {
    sistemaLegado::executaFrameOculto($stJs);
}
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
