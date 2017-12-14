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
 * Página de Formulario de Incluir Notas Explicativas do STN
 *
 * Data de Criação: 23/06/2009
 * @author Analista      : Tonismar Regis Bernardo <tonismar.bernardo@cnm.org.br>
 * @author Desenvolvedor : Henrique Girardi dos Santos <henrique.santos@cnm.org.br>
 * $Id: $
*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once CAM_GA_ADM_MAPEAMENTO.'TAdministracaoAcao.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterNotasExplicativas";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js";

require $pgJS;
Sessao::write('arValores', array());

//*****************************************************//
// Define COMPONENTES DO FORMULARIO
//*****************************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction($pgProc);
$obForm->setTarget('oculto');

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName('stAcao');
$obHdnAcao->setValue($_GET['stAcao']);

// Hidden padrão de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setId  ('stCtrl');
$obHdnCtrl->setName('stCtrl');

// Recebe o valor selecionado da combo de anexo para a manipulação do texto da combo no OC
$obHdnAnexo = new Hidden;
$obHdnAnexo->setId  ('stAnexo');
$obHdnAnexo->setName('stAnexo');

// Hidden que guarda o id da listagem quando algum item é selecionado para ser consultado/alterado
$obHdnId = new Hidden();
$obHdnId->setName('stHdnId');
$obHdnId->setId  ('stHdnId');

// Guarda o código do módulo do programa para quando for dado OK, possa ter acesso a esse dado no PR
$obHdnModulo = new Hidden();
$obHdnModulo->setName ('inModulo');
$obHdnModulo->setId   ('inModulo');
$obHdnModulo->setValue($_GET['modulo']);

// Guarda o código da funcionalidade do programa para quando for dado OK, possa ter acesso a esse dado no PR
$obHdnFuncionalidade = new Hidden();
$obHdnFuncionalidade->setName ('inFuncionalidade');
$obHdnFuncionalidade->setId   ('inFuncionalidade');
$obHdnFuncionalidade->setValue($_GET['funcionalidade']);

// Realiza a busca de todos os relatórios do STN da tabela de ações, não pegando as ações da configuração
$rsAnexo = new RecordSet;
$stCondicao  = "\n AND M.cod_modulo = ".$_GET['modulo']." \n AND A.cod_funcionalidade <> ".$_GET['funcionalidade'];
$stOrdem = "\n ORDER BY F.cod_funcionalidade, A.nom_acao";
$obTAdministracaAcao = new TAdministracaoAcao;
$obTAdministracaAcao->recuperaRelacionamento($rsAnexo, $stCondicao, $stOrdem);

// Define Objeto Select para Nome da Ação
$obCmbAnexo = new Select;
$obCmbAnexo->setName      ('inCodAcao');
$obCmbAnexo->setId        ('inCodAcao');
$obCmbAnexo->setRotulo    ('Anexo');
$obCmbAnexo->setTitle     ('Selecione o anexo');
$obCmbAnexo->setCampoID   ('cod_acao');
$obCmbAnexo->setCampoDesc ('[nom_funcionalidade] - [nom_acao]');
$obCmbAnexo->addOption    ('', 'Selecione');
$obCmbAnexo->setStyle     ('width: 300px;');
$obCmbAnexo->preencheCombo($rsAnexo);
$obCmbAnexo->obEvento->setOnChange('jq(\'#stAnexo\').val(this[this.selectedIndex].text);');

//Define o objeto TEXT para Codigo do Empenho Inicial
$obTxtDtInicial = new Data;
$obTxtDtInicial->setName  ('stDtInicial');
$obTxtDtInicial->setId    ('stDtInicial');
$obTxtDtInicial->setTitle ('Informe a data inicial e final da nota explicativa');
$obTxtDtInicial->setRotulo('*Data da Nota');

//Define objeto Label
$obLblData = new Label;
$obLblData->setValue('a');

//Define o objeto TEXT para Codigo do Empenho Inicial
$obTxtDtFinal = new Data;
$obTxtDtFinal->setName  ('stDtFinal');
$obTxtDtFinal->setId    ('stDtFinal');
$obTxtDtFinal->setTitle ('Informe a data inicial e final da nota explicativa');
$obTxtDtFinal->setRotulo('*Data da Nota');

// Define Objeto TextBox para Nota Explicativa
$obTxtNotaExplicativa = new TextArea;
$obTxtNotaExplicativa->setName  ('stNotaExplicativa');
$obTxtNotaExplicativa->setId    ('stNotaExplicativa');
$obTxtNotaExplicativa->setRotulo('*Nota Explicativa');
$obTxtNotaExplicativa->setTitle ('Digite o texto da nota explicativa');
$obTxtNotaExplicativa->setNull  (true);
$obTxtNotaExplicativa->setRows  (5);
$obTxtNotaExplicativa->setCols  (40);

// Define Objeto Button para Incluir Item
// O evento click do botão foi setado no JS via jQuery para poder ser manipulado de melhor forma posteriormente dependendo da ação
// do usuário
$obBtnIncluir = new Button;
$obBtnIncluir->setId   ('incluir');
$obBtnIncluir->setName ('incluir');
$obBtnIncluir->setValue('Incluir');
$obBtnIncluir->obEvento->setOnClick("jq('#inCodAcao').focus();");

// Define Objeto Button para Limpar Item
$obBtnLimpar = new Button;
$obBtnLimpar->setId   ('limpar');
$obBtnLimpar->setValue('Limpar');
$obBtnLimpar->obEvento->setOnClick("limparCadastro(); jq('#inCodAcao').focus();");

//Span da Listagem de itens de Itens Incluídos
$obSpnLista = new Span;
$obSpnLista->setId('spnListaItens');

//****************************************//
// Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm          ($obForm);
$obFormulario->addTitulo        ('Dados para Cadastro de Notas Explicativas');
$obFormulario->addHidden        ($obHdnAcao);
$obFormulario->addHidden        ($obHdnCtrl);
$obFormulario->addHidden        ($obHdnAnexo);
$obFormulario->addHidden        ($obHdnId);
$obFormulario->addHidden        ($obHdnFuncionalidade);
$obFormulario->addHidden        ($obHdnModulo);
$obFormulario->addComponente    ($obCmbAnexo);
$obFormulario->agrupaComponentes(array($obTxtDtInicial, $obLblData, $obTxtDtFinal));
$obFormulario->addComponente    ($obTxtNotaExplicativa);
$obFormulario->agrupaComponentes(array($obBtnIncluir, $obBtnLimpar));
$obFormulario->addSpan          ($obSpnLista);
$obFormulario->Cancelar         ($pgForm.'?'.Sessao::getId().'&'.http_build_query($_REQUEST));
$obFormulario->show();

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
