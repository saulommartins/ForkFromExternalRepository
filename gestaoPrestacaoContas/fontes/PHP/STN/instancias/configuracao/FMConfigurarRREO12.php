<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 20112 Confederação Nacional de Municípos                         *
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
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-11201, USA.       *
    *                                                                                *
    **********************************************************************************
*/
?>
<?php
/**
 * Formulario de Configuração do Anexo RREO 12
 *
 * @category    Urbem
 * @package     STN
 * @author      Carlos Adriano   <carlos.silva@cnm.org.br>
 * $Id: FMConfigurarRREO12.php 66695 2016-11-28 20:46:59Z carlos.silva $
 */

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include CAM_FW_INCLUDE . 'cabecalho.inc.php';
include CAM_GF_ORC_COMPONENTES . 'IPopUpReceita.class.php';



$pgOcul = 'OCConfigurarRREO12.php';
$pgProc = 'PRConfigurarRREO12.php';
$pgJs   = 'JSConfigurarRREO12.js';

include $pgJs;

$stAcao = $request->get('stAcao');

Sessao::remove('receitas');
Sessao::remove('receitas_del');

//Instancia um objeto Form
$obForm = new Form;
$obForm->setAction($pgProc);
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

//instancia um botao para incluir na lista
$obBtIncluir = new Button         ();
$obBtIncluir->setName             ('btIncluir');
$obBtIncluir->setId               ('btIncluir');
$obBtIncluir->setValue            ('Incluir');
$obBtIncluir->obEvento->setOnClick("montaParametrosGET('incluirReceitaAnexo12');");

//instancia um botao para remover da lista
$obBtLimpar = new Button         ();
$obBtLimpar->setName             ('btLimpar');
$obBtLimpar->setId               ('btLimpar');
$obBtLimpar->setValue            ('Limpar');
$obBtLimpar->obEvento->setOnClick("return limparFormularioAux('" . Sessao::getExercicio().  "');");

//instancia um span para as despesas
$obSpnListaReceitas = new Span();
$obSpnListaReceitas->setId('spnLista');

//Instancia um objeto Formulario
$obFormulario = new Formulario  ();
$obFormulario->addForm          ($obForm);
$obFormulario->addHidden        ($obHdnAcao);

$obFormulario->addTitulo        ('Vincular Receitas');

$obFormulario->addComponente    ($stExercicio);
$obFormulario->addComponente    ($obBscReceita);
$obFormulario->defineBarra      (array($obBtIncluir,$obBtLimpar));
$obFormulario->addSpan          ($obSpnListaReceitas);

$obFormulario->ok               ();
$obFormulario->show             ();

$jsOnLoad = "montaParametrosGET('carregaReceitasAnexo12');";

include CAM_FW_INCLUDE . 'rodape.inc.php';