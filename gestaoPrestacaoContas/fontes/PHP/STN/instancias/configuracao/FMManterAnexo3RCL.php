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
 * Formulario de Vinculo de Despesa Pessoal
 *
 * @category    Urbem
 * @package     STN
 * @author      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
 * @author      Henrique Boaventura <henrique.boaventura@cnm.org.br>
 *
 * $Id:$
 */

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include CAM_FW_INCLUDE . 'cabecalho.inc.php';
include CAM_GF_ORC_COMPONENTES . 'IPopUpReceita.class.php';
include CAM_GPC_STN_NEGOCIO  . 'RSTNConfiguracao.class.php';
include 'JSManterAnexo3RCL.js';

$stAcao = $request->get('stAcao');

$pgOcul = 'OCManterAnexo3RCL.php';

Sessao::remove('receitas');
Sessao::remove('receitas_del');

//Instancia um objeto Form
$obForm = new Form;
$obForm->setAction('PRVincularDespesaPessoal.php');
$obForm->setTarget('oculto');

//Instancia um objeto hidden da acao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ('stAcao');
$obHdnAcao->setValue($stAcao);

//instancia o componente exercicio
$stExercicio = new Exercicio     ();
$stExercicio->setId              ('stExercicio');
$stExercicio->setObrigatorioBarra(true);
$stExercicio->setNull            (true);

// Define Objeto BuscaInner para Receita
$obBscReceita = new IPopUpReceita();
$obBscReceita->setId             ('stNomReceita');
$obBscReceita->setTitle          ('Digite o Reduzido da Receita.');
$obBscReceita->setNull           (true);
$obBscReceita->setObrigatorioBarra(true);

//recupera os tipos de receita
$obRSTNConfiguracao = new RSTNConfiguracao();
$obRSTNConfiguracao->listTipoReceitasAnexo3($rsTipo);

//defina um select para selecionar o tipo de receita
$obSlTipo = new Select        ();
$obSlTipo->setId              ('inCodTipo');
$obSlTipo->setName            ('inCodTipo');
$obSlTipo->setRotulo          ('Tipo da Receita');
$obSlTipo->setTitle           ('Selecione o tipo da receita.');
$obSlTipo->addOption          ('','Selecione');
$obSlTipo->setCampoId         ('cod_tipo');
$obSlTipo->setCampoDesc       ('descricao');
$obSlTipo->preencheCombo      ($rsTipo);
$obSlTipo->setObrigatorioBarra(true);

//instancia um botao para incluir na lista
$obBtIncluir = new Button         ();
$obBtIncluir->setName             ('btIncluir');
$obBtIncluir->setId               ('btIncluir');
$obBtIncluir->setValue            ('Incluir');
$obBtIncluir->obEvento->setOnClick("montaParametrosGET('incluirReceitaAnexo3');");

//instancia um botao para remover da lista
$obBtLimpar = new Button         ();
$obBtLimpar->setName             ('btLimpar');
$obBtLimpar->setId               ('btLimpar');
$obBtLimpar->setValue            ('Limpar');
$obBtLimpar->obEvento->setOnClick("return limparFormularioAux('" . Sessao::getExercicio().  "');");

//instancia um span para as despesas
$obSpnListaReceitas = new Span();
$obSpnListaReceitas->setId('spnLista');

//instancia um select para a demonstrar ou nao o IRRF
$obSlIRRF = new Select();
$obSlIRRF->setName    ('boIRRF');
$obSlIRRF->setId      ('boIRRF');
$obSlIRRF->setRotulo  ('Deduzir IRRF');
$obSlIRRF->setTitle   ('Selecione se deseja deduzir o IRRF.');
$obSlIRRF->addOption  ('','Selecione');
$obSlIRRF->addOption  ('1','Sim');
$obSlIRRF->addOption  ('0','Não');
$obSlIRRF->setNull    (false);

//Instancia um objeto Formulario
$obFormulario = new Formulario  ();
$obFormulario->addForm          ($obForm);
$obFormulario->addHidden        ($obHdnAcao);

$obFormulario->addTitulo        ('Vincular Receitas');

$obFormulario->addComponente    ($stExercicio);
$obFormulario->addComponente    ($obBscReceita);
$obFormulario->addComponente    ($obSlTipo);
$obFormulario->defineBarra      (array($obBtIncluir,$obBtLimpar));
$obFormulario->addSpan          ($obSpnListaReceitas);

$obFormulario->addComponente    ($obSlIRRF);

$obFormulario->ok               ();
$obFormulario->show             ();

$jsOnLoad = "montaParametrosGET('carregaReceitasAnexo3');";

include CAM_FW_INCLUDE . 'rodape.inc.php';
?>
