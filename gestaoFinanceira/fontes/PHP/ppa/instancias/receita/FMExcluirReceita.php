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
 * Página de exclusao de Receita
 * Data de Criação: 23/12/2008
 *
 *
 * @author Marcio Medeiros <marcio.medeiros@cnm.org.br>
 *
 * $Id: $
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/normas/classes/componentes/IPopUpNorma.class.php';
include_once CAM_GF_PPA_CLASSES."negocio/RPPAManterReceita.class.php";
include_once CAM_GF_PPA_CLASSES."visao/VPPAManterReceita.class.php";
include_once CAM_GF_PPA_VISAO.'/VPPAUtils.class.php';

//Define o nome dos arquivos PHP
$stProjeto = 'ManterReceita';
$pgFilt    = 'FL'.$stProjeto.'.php';
$pgList    = 'LS'.$stProjeto.'.php';
$pgForm    = 'FM'.$stProjeto.'.php';
$pgProc    = 'PR'.$stProjeto.'.php';
$pgOcul    = 'OC'.$stProjeto.'.php';
$pgJS      = 'JS'.$stProjeto.'.php';

include_once( $pgJS );

$stAcao = 'excluir';

$obVPPAUtils = new VPPAUtils;
// Objeto controller
$obVisao = new VPPAManterReceita( new RPPAManterReceita );
//Instancia o formulário
$obForm = new Form;
$obForm->setAction($pgProc);
$obForm->setTarget("oculto");
//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ("stAcao");
$obHdnAcao->setId   ("stAcao");
$obHdnAcao->setValue($stAcao);
//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ("stCtrl");
$obHdnCtrl->setId   ("stCtrl");
$obHdnCtrl->setValue($stAcao);

// Criar Formulário
$obFormulario = new Formulario;
$obFormulario->addForm  ($obForm);
$obFormulario->addHidden($obHdnCtrl);
$obFormulario->addHidden($obHdnAcao);
// Exercício do PPA
$obHdnExercicio = new Hidden;
$obHdnExercicio->setName('stExercicio');
$obHdnExercicio->setId  ('stExercicio');

$inCodPPA           = $_GET['cod_ppa'];
$inCodReceita       = $_GET['cod_receita'];
$inCodConta         = $_GET['cod_conta'];
$stExercicio        = $_GET['exercicio'];
$stDescricaoReceita = $_GET['descricao']; // Apenas para o Label
$inCodEntidade      = $_GET['cod_entidade'];
$stNomEntidade      = $_GET['nom_entidade'];

$stValorTotalReceita = str_replace('.', ',', $_GET['valor_total']);
$flValorTotalReceita = $_GET['valor_total'];
$obFormulario->addTitulo("Dados para Exclusão da Receita");
// Cod PPA
$obHdnCodPPA = new Hidden;
$obHdnCodPPA->setName ("inCodPPA");
$obHdnCodPPA->setId   ("inCodPPA");
$obHdnCodPPA->setValue($inCodPPA );
// Cod Receita
$obHdnCodReceita = new Hidden;
$obHdnCodReceita->setName ("inCodReceita");
$obHdnCodReceita->setId   ("inCodReceita");
$obHdnCodReceita->setValue($inCodReceita);
// Cod Entidade
$obHdnCodEntidade = new Hidden;
$obHdnCodEntidade->setName('inCodEntidade');
$obHdnCodEntidade->setValue( $inCodEntidade );
// Cod Conta Receita
$obHdnCodContaReceita = new Hidden;
$obHdnCodContaReceita->setName ("inCodConta");
$obHdnCodContaReceita->setId   ("inCodConta");
$obHdnCodContaReceita->setValue($inCodConta);
// Exercicio
$obHdnExercicio->setValue($stExercicio);
// Add objs hidden
$obFormulario->addHidden($obHdnCodPPA);
$obFormulario->addHidden($obHdnCodReceita);
$obFormulario->addHidden($obHdnCodContaReceita);
$obFormulario->addHidden($obHdnCodEntidade);
$obFormulario->addHidden($obHdnExercicio);

// Label Exercício PPA
$obLblExercicioPPA = new Label();
$obLblExercicioPPA->setName  ('lblExercicioPPA');
$obLblExercicioPPA->setId    ('lblExercicioPPA');
$obLblExercicioPPA->setRotulo('Exercício');
$obLblExercicioPPA->setValue ($_REQUEST['periodo']);
$obFormulario->addComponente ($obLblExercicioPPA);
// Label "Receita" -  descrição
$obLblDescricaoReceita= new Label();
$obLblDescricaoReceita->setName  ('lblDescricaoReceita');
$obLblDescricaoReceita->setId    ('lblDescricaoReceita');
$obLblDescricaoReceita->setRotulo('Receita');
$obLblDescricaoReceita->setValue ($stDescricaoReceita);
$obFormulario->addComponente     ($obLblDescricaoReceita);
// Label Entidade
$obLblEntidade = new Label();
$obLblEntidade->setName     ('lblEntidade');
$obLblEntidade->setId       ('lblEntidade');
$obLblEntidade->setRotulo   ('Entidade');
$obLblEntidade->setValue    ($stNomEntidade);
$obFormulario->addComponente($obLblEntidade);
// Campo "Total Previsto"
$obLblTotalPrevisto = new Label();
$obLblTotalPrevisto->setName  ('lblTotalPrevisto');
$obLblTotalPrevisto->setId    ('lblTotalPrevisto');
$obLblTotalPrevisto->setRotulo('Total Previsto');
$stValorTotalReceita = $obVPPAUtils->floatToStr($flValorTotalReceita);
$obLblTotalPrevisto->setValue ($stValorTotalReceita);
$obFormulario->addComponente  ($obLblTotalPrevisto);
// Incluir o IPopUpNorma somente existir norma vinculada ao PPA (somente quando homologado)
$obIPopUpNorma = new IPopUpNorma();
$obIPopUpNorma->setExibeDataNorma(true);
$obIPopUpNorma->obInnerNorma->obCampoCod->stId = 'inCodNorma';
#$stEventoNorma = 'verificarCadastroReceitaNorma();';
#$obIPopUpNorma->obInnerNorma->obCampoCod->obEvento->onBlur = $stEventoNorma;
$obIPopUpNorma->setExibeDataPublicacao(true);
$obIPopUpNorma->geraFormulario($obFormulario);

// Total da receita (total de recursos incluídos)
$obLblTotalReceita = new Label();
$obLblTotalReceita->setName  ('lblTotalReceita');
$obLblTotalReceita->setRotulo('Total desta Receita');
$obLblTotalReceita->setId    ('lblTotalReceita');
$obLblTotalReceita->setValue ($stValorTotalReceita);
$obFormulario->addComponente ( $obLblTotalReceita );

// Valor total de Receitas do PPA
$flValorTotalReceitasPPA = null;
$arParametros = array('inCodPPA'    => $inCodPPA,
                      'stExercicio' => $stExercicio);
$flValorTotalReceitasPPA = $obVisao->recuperaValorTotalReceita($arParametros, false);

// Total de todas as Receitas no PPA
$obLblTotalReceitasPPA = new Label();
$obLblTotalReceitasPPA->setName  ('lblTotalReceitasPPA');
$obLblTotalReceitasPPA->setRotulo('Total de Receitas no PPA');
$obLblTotalReceitasPPA->setId    ('lblTotalReceitasPPA');
$obLblTotalReceitasPPA->setValue ($flValorTotalReceitasPPA);
$obFormulario->addComponente     ( $obLblTotalReceitasPPA);

// BOTÕES DE AÇÃO DO FORMULÁRIO (OK/LIMPAR)
$obBtnOk = new Button;
$obBtnOk->setName ("Ok");
$obBtnOk->setId   ("Ok");
$obBtnOk->setValue("Ok");
$obBtnOk->obEvento->setOnClick('excluirReceita();');
// Botão Cancelar
$obBtnCancelar = new Button;
$obBtnCancelar->setName ('btnCancelar');
$obBtnCancelar->setValue('Cancelar');
$obBtnCancelar->obEvento->setOnClick("CancelarCL();");
// Array botoes
$arBtnForm   = array();
$arBtnForm[] = $obBtnOk;
$arBtnForm[] = $obBtnCancelar;

$obFormulario->defineBarra($arBtnForm);
$obFormulario->show();
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
