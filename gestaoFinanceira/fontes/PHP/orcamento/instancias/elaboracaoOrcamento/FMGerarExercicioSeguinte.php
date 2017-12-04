<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos (urbem@cnm.org.br)      *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * Este programa é software livre; você pode redistribuí-lo e/ou modificá-lo  sob *
    * os termos da Licença Pública Geral GNU conforme publicada pela  Free  Software *
    * Foundation; tanto a versão 2 da Licença, como (a seu critério) qualquer versão *
    * posterior.                                                                     *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral  do  GNU  junto  com *
    * este programa; se não, escreva para  a  Free  Software  Foundation,  Inc.,  no *
    * endereço 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.               *
    *                                                                                *
    **********************************************************************************
*/
?>
<?php
/**
    * Página de Formulario para Gera Orcamento Seuinte
    * Data de Criação   : 25/07/2005

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    $Revision: 32675 $
    $Name$
    $Autor:$
    $Date: 2007-05-21 16:04:19 -0300 (Seg, 21 Mai 2007) $

    * Casos de uso: uc-02.01.31
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once CAM_GF_INCLUDE.'validaGF.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = 'GerarExercicioSeguinte';
$pgFilt = 'FL'.$stPrograma.'.php';
$pgList = 'LS'.$stPrograma.'.php';
$pgForm = 'FM'.$stPrograma.'.php';
$pgProc = 'PR'.$stPrograma.'.php';
$pgOcul = 'OC'.$stPrograma.'.php';
$pgJS   = 'JS'.$stPrograma.'.js';

include_once ($pgJS);

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if (empty($stAcao)) {
    $stAcao = 'incluir';
}

//*****************************************************//
// Define COMPONENTES DO FORMULARIO
//*****************************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction($pgProc);
$obForm->setTarget('oculto');

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ('stAcao');
$obHdnAcao->setValue($stAcao);

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ('stCtrl');
$obHdnCtrl->setValue('');

// Define Objeto Sim/Não para Receita
$obRdCopiarReceitaValor = new Radio();
$obRdCopiarReceitaValor->setRotulo           ('Receita');
$obRdCopiarReceitaValor->setLabel            ('Copiar Receitas com Valores Orçados');
$obRdCopiarReceitaValor->setName             ('stReceita');
$obRdCopiarReceitaValor->setId               ('stReceita');
$obRdCopiarReceitaValor->setValue            ('S');
$obRdCopiarReceitaValor->setTitle            ('Copiar as Receitas ?');
$obRdCopiarReceitaValor->setChecked          (true);
$obRdCopiarReceitaValor->obEvento->setOnClick("validaEventos(this, new Array('stMetasArrecadacao'), 'S');");

// Define Objeto Sim/Não para Receita
$obRdCopiarReceita = new Radio();
$obRdCopiarReceita->setRotulo           (' ');
$obRdCopiarReceita->setLabel            ('Copiar Receitas sem Valores Orçados');
$obRdCopiarReceita->setName             ('stReceita');
$obRdCopiarReceita->setId               ('stReceita');
$obRdCopiarReceita->setValue            ('SS');
$obRdCopiarReceita->setTitle            ('Copiar as Receitas ?');
$obRdCopiarReceita->obEvento->setOnClick("validaEventos(this, new Array('stMetasArrecadacao'), 'N');");

// Define Objeto Sim/Não para Receita
$obRdNaoCopiarReceita = new Radio();
$obRdNaoCopiarReceita->setRotulo           ('  ');
$obRdNaoCopiarReceita->setLabel            ('Não Copiar Receitas');
$obRdNaoCopiarReceita->setName             ('stReceita');
$obRdNaoCopiarReceita->setId               ('stReceita');
$obRdNaoCopiarReceita->setValue            ('N');
$obRdNaoCopiarReceita->setTitle            ('Copiar as Receitas ?');
$obRdNaoCopiarReceita->obEvento->setOnClick("validaEventos(this, new Array('stMetasArrecadacao'), 'N');");

// Define Objeto Sim/Não para Despesas
$obRdCopiarDespesaValor = new Radio();
$obRdCopiarDespesaValor->setRotulo           ('Despesa');
$obRdCopiarDespesaValor->setLabel            ('Copiar Despesas com Valores Orçados (Somente serão copiadas as despesas de ações que estejam validadas na LDO )');
$obRdCopiarDespesaValor->setName             ('stDespesa');
$obRdCopiarDespesaValor->setId               ('stDespesa');
$obRdCopiarDespesaValor->setValue            ('S');
$obRdCopiarDespesaValor->setTitle            ('Copiar as Despesas ?');
$obRdCopiarDespesaValor->setChecked          (true);
$obRdCopiarDespesaValor->obEvento->setOnClick("validaEventos(this, new Array('stMetasExecucaoDespesa'), 'S');");

// Define Objeto Sim/Não para Despesas
$obRdCopiarDespesa = new Radio();
$obRdCopiarDespesa->setRotulo           (' ');
$obRdCopiarDespesa->setLabel            ('Copiar Despesas sem Valores Orçados (Somente serão copiadas as despesas de ações que estejam validadas na LDO )');
$obRdCopiarDespesa->setName             ('stDespesa');
$obRdCopiarDespesa->setId               ('stDespesa');
$obRdCopiarDespesa->setValue            ('SS');
$obRdCopiarDespesa->setTitle            ('Copiar as Despesas ?');
$obRdCopiarDespesa->obEvento->setOnClick("validaEventos(this, new Array('stMetasExecucaoDespesa'), 'N');");

// Define Objeto Sim/Não para Despesas
$obRdNaoCopiarDespesa = new Radio();
$obRdNaoCopiarDespesa->setRotulo           ('  ');
$obRdNaoCopiarDespesa->setLabel            ('Não Copiar Despesas');
$obRdNaoCopiarDespesa->setName             ('stDespesa');
$obRdNaoCopiarDespesa->setId               ('stDespesa');
$obRdNaoCopiarDespesa->setValue            ('N');
$obRdNaoCopiarDespesa->setTitle            ('Copiar as Despesas ?');
$obRdNaoCopiarDespesa->obEvento->setOnClick("validaEventos(this, new Array('stMetasExecucaoDespesa'), 'N');");

// Define Objeto Sim/Não para Metas de Arrecadação
$obRdMetasArrecadacao = new SimNao();
$obRdMetasArrecadacao->setRotulo                       ('Metas de Arrecadação');
$obRdMetasArrecadacao->setName                         ('stMetasArrecadacao');
$obRdMetasArrecadacao->setId                           ('stMetasArrecadacao');
$obRdMetasArrecadacao->setTitle                        ('Copiar Metas de Arrecadação ?');
$obRdMetasArrecadacao->obRadioSim->obEvento->setOnClick("validaEventos(this, new Array('stReceita'), 'S');");

// Define Objeto Sim/Não para Metas de Arrecadação
$obRdMetasArrecadacaoDespesa = new SimNao();
$obRdMetasArrecadacaoDespesa->setRotulo                       ('Metas de Execução da Despesa');
$obRdMetasArrecadacaoDespesa->setName                         ('stMetasExecucaoDespesa');
$obRdMetasArrecadacaoDespesa->setId                           ('stMetasExecucaoDespesa');
$obRdMetasArrecadacaoDespesa->setTitle                        ('Copiar Metas de Arrecadação ?');
$obRdMetasArrecadacaoDespesa->obRadioSim->obEvento->setOnClick("validaEventos(this, new Array('stDespesa'), 'S');");

// Define Botão de Ok
$obBtnOk = new Ok();

$obHdnEval = new HiddenEval;
$obHdnEval->setName ('stEval');
$obHdnEval->setValue('BloqueiaFrames(true,false);');

//****************************************//
// Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm($obForm);
$obFormulario->addTitulo('Dados do Empenho');
$obFormulario->setAjuda ('UC-02.01.06');

$obFormulario->addHidden($obHdnCtrl);
$obFormulario->addHidden($obHdnAcao);
$obFormulario->addHidden($obHdnEval, true);

$obFormulario->addComponente($obRdCopiarReceitaValor);
$obFormulario->addComponente($obRdCopiarReceita);
$obFormulario->addComponente($obRdNaoCopiarReceita);
$obFormulario->addComponente($obRdMetasArrecadacao);
$obFormulario->addComponente($obRdCopiarDespesaValor);
$obFormulario->addComponente($obRdCopiarDespesa);
$obFormulario->addComponente($obRdNaoCopiarDespesa);
$obFormulario->addComponente($obRdMetasArrecadacaoDespesa);

$obFormulario->defineBarra(array($obBtnOk));
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>