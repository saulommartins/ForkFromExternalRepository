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
    * Pagina de filtro para o relatório Metas de execução orçamentaria
    * Data de Criação   : 28/08/2006

    * @author Desenvolvedor: Bruce Cruz de Sena

    * @ignore

    $Revision: 30762 $
    $Name$
    $Autor: $
    $Date: 2007-12-05 15:12:56 -0200 (Qua, 05 Dez 2007) $

    * Casos de uso: uc-02.01.33
*/

/*
$Log$
Revision 1.6  2007/08/15 18:47:39  bruce
Bug#9908#

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_ORC_COMPONENTES.'ISelectMultiploEntidadeUsuario.class.php';
include_once CAM_GF_ORC_COMPONENTES.'IPopUpRecurso.class.php';
include_once CAM_GF_ORC_COMPONENTES.'IIntervaloPopUpDotacao.class.php';
include_once CAM_FW_HTML.'MontaOrgaoUnidade.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "MetasExecucaoDespesa";
$pgFilt       = "FL".$stPrograma.".php";
$pgList       = "LS".$stPrograma.".php";
$pgForm       = "FM".$stPrograma.".php";
$pgProc       = "PR".$stPrograma.".php";
$pgOcul       = "OC".$stPrograma.".php";
$pgFiltroOcul = "OCFiltro".$stPrograma.".php";
$pgJS         = "JS".$stPrograma.".js";

$pgRelatorio = CAM_GF_ORC_INSTANCIAS.'relatorio/OCGeraMetasExecucaoDespesa.php';

// Componente para filtrar as entidades
$obSelMultEntidadeUsuario = new ISelectMultiploEntidadeUsuario;

// Exercicio
$obTxtExercicio = new Exercicio;
$obTxtExercicio->setRotulo('Exercício');
$obTxtExercicio->setTitle ('Informe o exercício.');
$obTxtExercicio->setName  ('stExercicio');
$obTxtExercicio->setId    ('stExercicio');
$obTxtExercicio->setValue (Sessao::getExercicio());
$obTxtExercicio->setNull  (false);

// Mostar ou não as Contas Sintéticas
$obSimNaoSinteticas = new SimNao();
$obSimNaoSinteticas->setRotulo  ('Demonstrar Sintéticas');
$obSimNaoSinteticas->setTitle   ('Informe se deseja ou não demonstrar as contas sintéticas.');
$obSimNaoSinteticas->setName    ('boDemonstrarSintéticas');
$obSimNaoSinteticas->setNull    (true);
$obSimNaoSinteticas->setChecked ('SIM');

// Filtro Orgão Unidade
$obMontaOrgaoUnidade = new MontaOrgaoUnidade;
$obMontaOrgaoUnidade->setActionAnterior ($pgFiltroOcul);
$obMontaOrgaoUnidade->setActionPosterior($pgRelatorio);
$obMontaOrgaoUnidade->setTarget('telaPrincipal');

// Pop Up de Recurso
include_once(CAM_GF_ORC_COMPONENTES."IMontaRecursoDestinacao.class.php");
$obIMontaRecursoDestinacao = new IMontaRecursoDestinacao;
$obIMontaRecursoDestinacao->setFiltro(true);

// Pop Uo de intervalo de Dotação
$obPopUpIntervaloDotacao = new IIntervaloPopUpDotacao($obSelMultEntidadeUsuario);

$obForm = new Form;
$obForm->setAction(CAM_GF_ORC_INSTANCIAS.'relatorio/OCGeraMetasExecucaoDespesa.php');
$obForm->setTarget('telaPrincipal');

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ('stAcao');
$obHdnAcao->setValue($stAcao);

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ('stCtrl');
$obHdnCtrl->setValue($stCtrl);

// Instanciação do objeto Lista de Assinaturas
// Limpa papeis das Assinaturas na Sessão
$arAssinaturas = Sessao::read('assinaturas');
$arAssinaturas['papeis'] = array();
Sessao::write('assinaturas',$arAssinaturas);

include_once( CAM_GA_ADM_COMPONENTES."IMontaAssinaturas.class.php");
$obMontaAssinaturas = new IMontaAssinaturas;
$obMontaAssinaturas->setEventosCmbEntidades($obSelMultEntidadeUsuario);

$obFormulario = new Formulario;
$obFormulario->addForm               ($obForm);
$obFormulario->setAjuda              ('UC-02.01.33');
$obFormulario->addHidden             ($obHdnAcao);
$obFormulario->addHidden             ($obHdnCtrl);
$obFormulario->addComponente         ($obSelMultEntidadeUsuario);
$obFormulario->addComponente         ($obTxtExercicio);
$obFormulario->addComponente         ($obSimNaoSinteticas);
$obMontaOrgaoUnidade->geraFormulario ($obFormulario);
$obIMontaRecursoDestinacao->geraFormulario($obFormulario);
$obFormulario->addComponente         ($obPopUpIntervaloDotacao);

// Injeção de código no formulário
$obMontaAssinaturas->geraFormulario($obFormulario);

$obFormulario->Ok();
$obFormulario->show();

?>
