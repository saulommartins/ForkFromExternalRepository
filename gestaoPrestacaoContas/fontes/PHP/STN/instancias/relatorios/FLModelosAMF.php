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
 * Página de Filtro: Demonstrativo AMF
 * Data de Criação   : 08/10/2007

 * @author Analista      Tonismar Régis Bernardo     <tonismar.bernardo@cnm.org.br>
 * @author Desenvolvedor Henrique Boaventura         <henrique.boaventura@cnm.org.br>

 $Id: FLModelosAMF.php 59612 2014-09-02 12:00:51Z gelson $

 */

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/HTML/Bimestre.class.php';
include_once CAM_GF_PPA_COMPONENTES.'ITextBoxSelectPPA.class.php';
include_once CAM_GA_ADM_COMPONENTES.'IMontaAssinaturas.class.php';
include_once CAM_GF_LDO_MAPEAMENTO.'TLDOTipoIndicadores.class.php';

Sessao::write('arValores', array());
$pgOcul     = 'OCModelosAMF.php';
$stJs       = '';

$inLen = strlen($_REQUEST['stAcao']);
$inNumero = substr($_REQUEST['stAcao'], $inLen-1, $inLen);

$obHdnAcao = new Hidden;
$obHdnAcao->setName ('stAcao');
$obHdnAcao->setValue($_REQUEST['stAcao']);

$obForm = new Form;
$obForm->setTarget('telaPrincipal');
$obForm->setAction('OCGeraAMFDemonstrativo'.$inNumero.'.php');

$obFormulario = new Formulario;
$obFormulario->addForm($obForm);
$obFormulario->addHidden($obHdnAcao);
$obFormulario->addTitulo('Dados para o filtro');

// Instanciação do objeto Lista de Assinaturas
// Limpa papeis das Assinaturas na Sessão
$arAssinaturas = Sessao::read('assinaturas');
$arAssinaturas['papeis'] = array();
Sessao::write('assinaturas',$arAssinaturas);

$obMontaAssinaturas = new IMontaAssinaturas;

if ($request->get('stAcao') != 'demons5' && $request->get('stAcao') != 'demons6' && $request->get('stAcao') != 'demons2' && $request->get('stAcao') != 'demons3') {
    $stCampo = 'valor';
    $stTabela = 'administracao.configuracao';
    $stFiltro  = " WHERE parametro = 'cod_entidade_prefeitura' ";
    $stFiltro .= " AND exercicio = '".Sessao::getExercicio()."' ";
    $stFiltro .= " AND cod_modulo = 8 ";
    $inCodEntidadePrefeitura = SistemaLegado::pegaDado($stCampo, $stTabela, $stFiltro);

    $obHdnCodEntidade = new Hidden;
    $obHdnCodEntidade->setName('inCodEntidade');
    $obHdnCodEntidade->setId('inCodEntidade');
    $obHdnCodEntidade->setValue($inCodEntidadePrefeitura);
    $obFormulario->addHidden($obHdnCodEntidade);
} else {
    // Monta o componente de select multiplo das entidades
    require CAM_GF_ORC_COMPONENTES.'ISelectMultiploEntidadeUsuario.class.php';
    $obISelectMultiploEntidadeUsuario = new ISelectMultiploEntidadeUsuario;
    $obISelectMultiploEntidadeUsuario->SetNomeLista2('inCodEntidade');
    $obFormulario->addComponente($obISelectMultiploEntidadeUsuario);

    $obMontaAssinaturas->setEventosCmbEntidades($obISelectMultiploEntidadeUsuario);
}

//Instancia um objeto do componente ITextBoxSelectPPA
$obITextBoxSelectPPA = new ITextBoxSelectPPA();
$obITextBoxSelectPPA->setNull(false);
$obITextBoxSelectPPA->setPreencheUnico(true);
$obITextBoxSelectPPA->obSelect->obEvento->setOnChange("montaParametrosGET('preencheLDO');");
$obITextBoxSelectPPA->obTextBox->obEvento->setOnChange("montaParametrosGET('preencheLDO');");

$obITextBoxSelectPPA->geraFormulario($obFormulario);

//Instancia um objeto Select
$obCmbExercicioLDO = new Select();
$obCmbExercicioLDO->setName  ('stExercicio');
$obCmbExercicioLDO->setId    ('stExercicio');
$obCmbExercicioLDO->setRotulo('Exercício LDO');
$obCmbExercicioLDO->setTitle ('Informe o exercicio da LDO');
$obCmbExercicioLDO->setNull  (false);
$obCmbExercicioLDO->addOption('','Selecione');
$obFormulario->addComponente($obCmbExercicioLDO);

$obTLDOTipoIndicadores = new TLDOTipoIndicadores;
$obTLDOTipoIndicadores->recuperaTodos($rsTipoIndicadores);

if ($_REQUEST['stAcao'] == 'demons1' || $_REQUEST['stAcao'] == 'demons2') {
    $obFormulario->addTitulo('Vincular Indicadores');
    //Instancia um objeto Select
    $obCmbIndicadorPIB = new Select();
    $obCmbIndicadorPIB->setName      ('inCodPIB');
    $obCmbIndicadorPIB->setId        ('inCodPIB');
    $obCmbIndicadorPIB->setRotulo    ('PIB');
    $obCmbIndicadorPIB->setTitle     ('Informe o Indicador do PIB');
    $obCmbIndicadorPIB->setNull      (false);
    $obCmbIndicadorPIB->setCampoId   ('cod_tipo_indicador');
    $obCmbIndicadorPIB->setCampoDesc ('descricao');
    $obCmbIndicadorPIB->addOption    ('','Selecione');
    $obCmbIndicadorPIB->preencheCombo($rsTipoIndicadores);
    $obFormulario->addComponente($obCmbIndicadorPIB);
}

if ($_REQUEST['stAcao'] == 'demons1' || $_REQUEST['stAcao'] == 'demons3') {
    //Instancia um objeto Select
    $obCmbIndicadorInflacao = new Select();
    $obCmbIndicadorInflacao->setName      ('inCodInflacao');
    $obCmbIndicadorInflacao->setId        ('inCodInflacao');
    $obCmbIndicadorInflacao->setRotulo    ('Inflação');
    $obCmbIndicadorInflacao->setTitle     ('Informe o Indicador da Inflação');
    $obCmbIndicadorInflacao->setNull      (false);
    $obCmbIndicadorInflacao->setCampoId   ('cod_tipo_indicador');
    $obCmbIndicadorInflacao->setCampoDesc ('descricao');
    $obCmbIndicadorInflacao->addOption    ('','Selecione');
    $obCmbIndicadorInflacao->preencheCombo($rsTipoIndicadores);
    $obFormulario->addComponente($obCmbIndicadorInflacao);
}

// porém caso a ação seja demons5, é necessário realizar alguns procedimentos para a realização de outros filtros
if ($_REQUEST['stAcao'] == 'demons5') {
    //Busca os recursos cadastrados previamente
    require CAM_GPC_STN_MAPEAMENTO."TSTNRecursoRREOAnexo14.class.php";
    $obTSTN = new TSTNRecursoRREOAnexo14();
    $obTSTN->setDado('exercicio',Sessao::getExercicio());
    $obTSTN->recuperaRelacionamento($rsRecurso);

    //adiciona os valores na sessao
    $arValores = array();
    $i = 0;
    while (!$rsRecurso->eof()) {
        $arValores[$i]['inCodRecurso'] = $rsRecurso->getCampo('cod_recurso');
        $arValores[$i]['stDescricaoRecurso'] = $rsRecurso->getCampo('nom_recurso');
        $i++;
        $rsRecurso->proximo();
    }
    Sessao::write('arValores', $arValores);
    $stJs .= "montaParametrosGET('montaLista','');";

    require CAM_GF_ORC_COMPONENTES.'IMontaRecursoDestinacao.class.php';
    $obIMontaRecursoDestinacao = new IMontaRecursoDestinacao;
    $obIMontaRecursoDestinacao->setFiltro(true);
    $obIMontaRecursoDestinacao->setRotulo('Recurso de Alienação de Ativos');
    $obIMontaRecursoDestinacao->setObrigatorioBarra(true);
    $obIMontaRecursoDestinacao->geraFormulario($obFormulario);

    //Span da Listagem de itens
    $obSpnLista = new Span;
    $obSpnLista->setID('spnLista');

    // Define Objeto Button para Incluir Item na lista
    $obBtnIncluir = new Button;
    $obBtnIncluir->setValue('Incluir');
    $obBtnIncluir->obEvento->setOnClick("montaParametrosGET('incluirLista','inCodRecurso,stDescricaoRecurso');frm.inCodRecurso.focus()");
    $obFormulario->addComponente($obBtnIncluir);
    $obFormulario->addSpan($obSpnLista);

    $obOk = new Ok;
    $obOk->setId('Ok');
    $obOk->obEvento->setOnClick("if ( Valida() ) {montaParametrosGET('Valida');}");

    $obLimpar = new Button;
    $obLimpar->setValue('Limpar');
    $obLimpar->obEvento->setOnClick('frm.reset();');

    $obFormulario->addTitulo('Dados para as assinaturas');
    $obMontaAssinaturas->geraFormulario($obFormulario);

    $obFormulario->defineBarra(array($obOk, $obLimpar));

} else {
    $obFormulario->addTitulo('Dados para as assinaturas');
    $obMontaAssinaturas->geraFormulario($obFormulario);
    $obFormulario->Ok();
}
$stJs .= "montaParametrosGET('preencheLDO');";

$obFormulario->show();
$jsOnLoad = $stJs;

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
