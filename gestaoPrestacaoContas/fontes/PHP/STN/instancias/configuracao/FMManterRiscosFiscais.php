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
    * Formulário de Demonstrativo de Riscos Fiscais
    * Data de Criação   : 01/06/2009

    * @author Analista      Tonismar Régis Bernardo <tonismar.bernardo@cnm.org.br>
    * @author Desenvolvedor Eduardo Paculski Schitz <eduardo.scritz@cnm.org.br>

    * @package URBEM
    * @subpackage

    * @ignore

    $Id:$
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once CAM_FW_INCLUDE . 'cabecalho.inc.php';
include_once CAM_GF_ORC_COMPONENTES."ISelectMultiploEntidadeUsuario.class.php";
include_once CAM_GPC_STN_CONTROLE.'CSTNConfiguracao.class.php';
include_once CAM_GPC_STN_NEGOCIO.'RSTNConfiguracao.class.php';
include_once CAM_GPC_STN_NEGOCIO.'RSTNIdentificadorRiscoFiscal.class.php';

$obModel = new RSTNConfiguracao();
$obController = new CSTNConfiguracao($obModel);

$stAcao = $request->get('stAcao');

//Define o nome dos arquivos PHP
$stPrograma = "ManterRiscosFiscais";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

$stAcao = $stAcao."Demonstrativo";

Sessao::remove('arProvidencia');
$stDescricaoRisco = '';
$flValor = '';
$flValorProvidencia = '';
$inCodIdentificador = '';

if ($stAcao == 'alterarDemonstrativo') {
    $obController->obModel->stExercicio        = $_REQUEST['stExercicio'];
    $obController->obModel->inCodEntidade      = $_REQUEST['inCodEntidade'];
    $obController->obModel->inCodRisco         = $_REQUEST['inCodRisco'];
    $obController->obModel->inCodIdentificador = $_REQUEST['inCodIdentificador'];
    $obController->obModel->buscaRiscoFiscal($rsRiscosFiscais);
    $rsRiscosFiscais->addFormatacao('valor', 'NUMERIC_BR');

    $stDescricaoRisco = $rsRiscosFiscais->getCampo('descricao');
    $flValor = $rsRiscosFiscais->getCampo('valor');
    $inCodIdentificador = $rsRiscosFiscais->getCampo('cod_identificador');

    $obController->obModel->listProvidencias($rsProvidencias);

    Sessao::write('arProvidencia', $rsProvidencias->arElementos);
}

//Instancia um objeto Form
$obForm = new Form;
$obForm->setAction($pgProc);
$obForm->setTarget('oculto');

//Instancia um objeto hidden da acao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ('stAcao');
$obHdnAcao->setValue($stAcao);

$obExercicio = new Exercicio;
$obExercicio->setName('stExercicio');
$obExercicio->setId  ('stExercicio');
$obExercicio->obEvento->setOnBlur("montaParametrosGET('buscaEntidades');");
if ($stAcao == 'alterarDemonstrativo') {
    $obExercicio->setValue($rsRiscosFiscais->getCampo('exercicio'));
    $obExercicio->setLabel(true);

    $obLblEntidade = new Label;
    $obLblEntidade->setValue($rsRiscosFiscais->getCampo('cod_entidade')." - ".$rsRiscosFiscais->getCampo('nom_cgm'));
    $obLblEntidade->setRotulo('*Entidade');

    $obHdnEntidade = new Hidden;
    $obHdnEntidade->setId('inCodEntidade');
    $obHdnEntidade->setName('inCodEntidade');
    $obHdnEntidade->setValue($rsRiscosFiscais->getCampo('cod_entidade'));

    $obHdnRisco = new Hidden;
    $obHdnRisco->setId('inCodRisco');
    $obHdnRisco->setName('inCodRisco');
    $obHdnRisco->setValue($rsRiscosFiscais->getCampo('cod_risco'));

} else {
    $obISelectEntidade = new ISelectMultiploEntidadeUsuario();
    $obISelectEntidade->SetNomeLista2('inCodEntidade');
}

//Informar descrição indicador
$obTextBoxDescricaoRisco = new TextBox;
$obTextBoxDescricaoRisco->setRotulo   ('Descrição do Risco');
$obTextBoxDescricaoRisco->setTitle    ('Descrição do Risco');
$obTextBoxDescricaoRisco->setName     ('stDescricaoRisco');
$obTextBoxDescricaoRisco->setId       ('stDescricaoRisco');
$obTextBoxDescricaoRisco->setNull     (false);
$obTextBoxDescricaoRisco->setMaxLength(100);
$obTextBoxDescricaoRisco->setSize     (100);
$obTextBoxDescricaoRisco->setValue    ($stDescricaoRisco);

//Instancia um textbox para o valor do cheque
$obFlValor = new Numerico;
$obFlValor->setName    ('flValor');
$obFlValor->setId      ('flValor');
$obFlValor->setRotulo  ('Valor');
$obFlValor->setTitle   ('Informe o valor');
$obFlValor->setNull    (false);
$obFlValor->setNegativo(false);
$obFlValor->setMinValue(0.01);
$obFlValor->setValue   ($flValor);

// Realiza a busca de todos identificadores
$obRSTNIdentificadorRiscoFiscal = new RSTNIdentificadorRiscoFiscal;
$obRSTNIdentificadorRiscoFiscal->listIdentificadores($rsIdentificadores);

// Define Objeto Select para Nome da Ação
$obCmbIdentificadores = new Select;
$obCmbIdentificadores->setName      ('inCodIdentificador');
$obCmbIdentificadores->setId        ('inCodIdentificador');
$obCmbIdentificadores->setRotulo    ('Identificador do Risco Fiscal');
$obCmbIdentificadores->setTitle     ('Selecione o identificador');
$obCmbIdentificadores->setCampoID   ('cod_identificador');
$obCmbIdentificadores->setCampoDesc ('[cod_identificador] - [descricao]');
$obCmbIdentificadores->addOption    ('', 'Selecione');
$obCmbIdentificadores->setStyle     ('width: 300px;');
$obCmbIdentificadores->setValue     ($inCodIdentificador);
$obCmbIdentificadores->preencheCombo($rsIdentificadores);

//Providencia
$obTextAreaProvidencia = new TextArea;
$obTextAreaProvidencia->setRotulo          ('Providência');
$obTextAreaProvidencia->setTitle           ('Providência');
$obTextAreaProvidencia->setName            ('stProvidencia');
$obTextAreaProvidencia->setId              ('stProvidencia');
$obTextAreaProvidencia->setNull            (true);
$obTextAreaProvidencia->setMaxCaracteres   (450);
$obTextAreaProvidencia->setObrigatorioBarra(true);

//Instancia um textbox para o valor do cheque
$obFlValorProvidencia = new Numerico;
$obFlValorProvidencia->setName            ('flValorProvidencia');
$obFlValorProvidencia->setId              ('flValorProvidencia');
$obFlValorProvidencia->setRotulo          ('Valor Providência');
$obFlValorProvidencia->setTitle           ('Informe o valor da providência');
$obFlValorProvidencia->setObrigatorioBarra(true);
$obFlValorProvidencia->setNegativo        (false);
$obFlValorProvidencia->setValue           ($flValorProvidencia);

//Instancia um botao incluir para incluir os dados do formulario na lista
$stCaminho = CAM_GPC_STN_INSTANCIAS.'configuracao/OCManterRiscosFiscais.php';
$obBtnIncluir = new Button;
$obBtnIncluir->setValue('Incluir');
$obBtnIncluir->obEvento->setOnClick("incluirProvidencia();");

//Instancia um botao para limpar o formulario
$obBtnLimpar = new Button();
$obBtnLimpar->setValue('Limpar');
$obBtnLimpar->setId   ('Limpar');
$obBtnLimpar->obEvento->setOnClick('limpaFormularioAux();');

$arBotoes = array($obBtnIncluir, $obBtnLimpar);

//Instancia um span para a lista
$obSpnLista = new Span();
$obSpnLista->setId('spnLista');

//Instancia um objeto Formulario
$obFormulario = new Formulario();
$obFormulario->addForm          ($obForm   );
$obFormulario->addHidden        ($obHdnAcao);
$obFormulario->addTitulo        ('Dados do Demonstrativo');
$obFormulario->addComponente    ($obExercicio);
if ($stAcao == 'incluirDemonstrativo') {
    $obFormulario->addComponente($obISelectEntidade);
} else {
    $obFormulario->addComponente($obLblEntidade);
    $obFormulario->addHidden    ($obHdnEntidade);
    $obFormulario->addHidden    ($obHdnRisco);
}
$obFormulario->addComponente    ($obTextBoxDescricaoRisco);
$obFormulario->addComponente    ($obFlValor);
$obFormulario->addComponente    ($obCmbIdentificadores);
$obFormulario->addTitulo        ('Providências');
$obFormulario->addComponente    ($obTextAreaProvidencia);
$obFormulario->addComponente    ($obFlValorProvidencia);
$obFormulario->agrupaComponentes($arBotoes);
$obFormulario->addSpan          ($obSpnLista);

// Caso deva-se incluir um risco fiscal, então haverá 2 botões: Ok e limpar. Foi usado um define barra pois é necessário limpar além dos campos
// do formulário, os dados da listagem que aparecem dentro de um span
if ($stAcao == 'incluirDemonstrativo') {
    $obOk = new Ok;
    $obLimpar = new Limpar;
    $obLimpar->obEvento->setOnClick('LimparForm();');

    $obFormulario->defineBarra(array($obOk, $obLimpar));

    // Se o formulário será usado para inclusão de dados, então deve-se carregar a listagem de entidades pelo exercício padrão ao carregar a tela
    $jsOnload  = "montaParametrosGET('buscaEntidades');";

} else {
    // Se for alteração ou exclusão, não deve-se ter o botão limpar e sim cancelar, com isso é montado o link de retorno para que não
    // seja perdido os dados do filtro na listagem
    $stCampos = '?stAcao='.$_GET['stAcao'].'&pg='.$_GET['pg'].'&pos='.$_GET['pos'];
    $obFormulario->Cancelar($pgList.$stCampos);

    // Caso seja alteração ou exclusão a ação, então deve-se carregar a lista de providencias para que seja mostrada na tela
    $jsOnload = "montaParametrosGET('montaListaProvidencia');";
}

$obFormulario->show();

include $pgJs;
include CAM_FW_INCLUDE.'rodape.inc.php';
?>
