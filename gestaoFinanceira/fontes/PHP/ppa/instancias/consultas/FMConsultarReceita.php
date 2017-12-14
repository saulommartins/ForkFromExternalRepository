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
 * Página de Consulta de Receita
 * Data de Criação: 09/01/2009
 *
 *
 *
 * @author Marcio Medeiros <marcio.medeiros@cnm.org.br>
 *
 * $Id: $
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_PPA_CLASSES."negocio/RPPAManterReceita.class.php";
include_once CAM_GF_PPA_CLASSES."visao/VPPAManterReceita.class.php";

//Define o nome dos arquivos PHP
$stProjeto = 'ConsultarReceita';
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
$obHdnAcao->setName ("stAcao");
$obHdnAcao->setValue($stAcao);
$obFormulario->addHidden($obHdnAcao);

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ("stCtrl");
$obHdnCtrl->setValue($stCtrl);
$obFormulario->addHidden($obHdnCtrl);

// Exercício do PPA
$obHdnExercicio = new Hidden;
$obHdnExercicio->setName('stExercicio');
$obHdnExercicio->setId  ('stExercicio');
// Validar dados vindos da LSMenterReceita
$inCodPPA            = (int) $_GET['cod_ppa'];
$boDestinacaoRecurso = $_GET['destinacao_recurso'];
$inCodReceitaDados   = (int) $_GET['cod_receita_dados'];
$inCodEntidade       = (int) $_GET['cod_entidade'];
$stNomEntidade       = $_GET['nom_entidade'];
$inCodReceita        = (int) $_GET['cod_receita'];
$inCodConta   = (int) $_GET['cod_conta'];
$stExercicio         = $_GET['exercicio'];
$stPeriodo           = $_GET['periodo'];
$stDescricao         = $_GET['descricao'];
$stValorTotalReceita = str_replace('.', ',', $_GET['valor_total']);
$obFormulario->addTitulo("Dados da Lista dos resultados para consulta");
// Cod PPA
$obHdnCodPPA = new Hidden;
$obHdnCodPPA->setName ("inCodPPA");
$obHdnCodPPA->setId   ("inCodPPA");
$obHdnCodPPA->setValue($inCodPPA );
// Destinação de Recurso
$obDestinacaoRecurso = new Hidden;
$obDestinacaoRecurso->setName ("boDestinacaoRecurso");
$obDestinacaoRecurso->setId   ("boDestinacaoRecurso");
$obDestinacaoRecurso->setValue($boDestinacaoRecurso );

// Cod Entidade
$obHdnCodEntidade = new Hidden;
$obHdnCodEntidade->setName('inCodEntidade');
$obHdnCodEntidade->setValue( $inCodEntidade );
// Cod Receita Dados
$obHdnCodReceitaDados = new Hidden;
$obHdnCodReceitaDados->setName('inCodReceitaDados');
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
$obFormulario->addHidden($obDestinacaoRecurso);
$obFormulario->addHidden($obHdnCodReceitaDados);
$obFormulario->addHidden($obHdnCodEntidade);
$obFormulario->addHidden($obHdnCodReceita);
$obFormulario->addHidden($obHdnCodContaReceita);
$obFormulario->addHidden($obHdnExercicio);
// Label Exercício PPA
$obLblExercicioPPA = new Label();
$obLblExercicioPPA->setName  ('lblExercicioPPA');
$obLblExercicioPPA->setId    ('lblExercicioPPA');
$obLblExercicioPPA->setRotulo('PPA');
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
// Recurso
$obSpnRecurso = new Span();
$obSpnRecurso->setID('spnRecurso');
$obFormulario->addSpan($obSpnRecurso);
// Fontes de Recurso (Span 3)
$spnFonteRecurso = new Span();
$spnFonteRecurso->setId("spnFonteRecurso");
$obFormulario->addSpan ($spnFonteRecurso);
// Total da receita (total de recursos incluídos)
$obLblTotalReceita = new Label();
$obLblTotalReceita->setName  ('lblTotalReceita');
$obLblTotalReceita->setRotulo('Total Lançado');
$obLblTotalReceita->setId    ('lblTotalReceita');
$obFormulario->addComponente ( $obLblTotalReceita );

// Valor total de Receitas do PPA
$flValorTotalReceitasPPA = null;
if (isset($inCodPPA)) {
    $arParametros = array('inCodPPA'    => $inCodPPA,
                          'stExercicio' => $stExercicio);
    $flValorTotalReceitasPPA = $obVisao->recuperaValorTotalReceita($arParametros, false);
}
// Total de todas as Receitas no PPA
$obLblTotalReceitasPPA = new Label();
$obLblTotalReceitasPPA->setName  ('lblTotalReceitasPPA');
$obLblTotalReceitasPPA->setRotulo('Total de Receitas no PPA');
$obLblTotalReceitasPPA->setId    ('lblTotalReceitasPPA');
$obLblTotalReceitasPPA->setValue ($flValorTotalReceitasPPA);
$obFormulario->addComponente     ( $obLblTotalReceitasPPA);

$obButtonVoltar = new Button;
$obButtonVoltar->setName  ('Voltar');
$obButtonVoltar->setValue ('Voltar');
$obButtonVoltar->obEvento->setOnClick('CancelarCL();');
$obFormulario->defineBarra( array( $obButtonVoltar), "left", "" );

$obFormulario->show();
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
sistemaLegado::executaFrameOculto("montaParametrosGET('montaListaRecursos')");
?>
