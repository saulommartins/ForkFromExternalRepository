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
    * Tela de Filtro para gerar o relatorio de Comparativo de Despesa com Receita
    * Data de Criação: 02/09/2009
    * @author Analista      Tonismar Régis Bernardo <tonismar.bernardo@cnm.org.br>
    * @author Desenvolvedor Henrique Girardi dos Santos <henrique.santos@cnm.org.br>
    * @package URBEM
    * @subpackage ORCAMENTO
    * @ignore
*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once CAM_GF_ORC_COMPONENTES.'IMontaRecursoDestinacao.class.php';
require_once CAM_GF_ORC_COMPONENTES.'ISelectMultiploEntidadeUsuario.class.php';

$stProjeto = 'ComparativoDespesaReceita';
$pgFilt    = 'FL'.$stProjeto.'.php';
$pgList    = 'LS'.$stProjeto.'.php';
$pgForm    = 'FM'.$stProjeto.'.php';
$pgProc    = 'PR'.$stProjeto.'.php';
$pgOcul    = 'OC'.$stProjeto.'.php';
$pgOcGera  = 'OCGera'.$stProjeto.'.php';
$pgJS      = 'JS'.$stProjeto.'.js';

require $pgJS;

$boDestinacaoRecurso = SistemaLegado::pegaDado('valor', 'administracao.configuracao', ' WHERE parametro = \'recurso_destinacao\' AND cod_modulo = 8');

$obForm = new Form;
$obForm->setAction($pgOcGera);
$obForm->setTarget('telaPrincipal');

if ($boDestinacaoRecurso) {

    $obHdnAcao = new Hidden;
    $obHdnAcao->setName ('stAcao');
    $obHdnAcao->setId   ('stAcao');
    $obHdnAcao->setValue($stAcao);

    $obHdnCtrl = new Hidden;
    $obHdnCtrl->setId   ('stCtrl');
    $obHdnCtrl->setName ('stCtrl');
    $obHdnCtrl->setValue($stCtrl);

    $obEntidade = new ISelectMultiploEntidadeUsuario;
    $obExercicio = new Exercicio;
    $obIMontaRecursoDestinacao = new IMontaRecursoDestinacao;
    $obIMontaRecursoDestinacao->setObrigatorioDestinacao(false);

    $obFormulario = new Formulario;
    $obFormulario->addForm      ($obForm);
    $obFormulario->addHidden    ($obHdnAcao);
    $obFormulario->addHidden    ($obHdnCtrl);
    $obFormulario->addComponente($obEntidade);
    $obFormulario->addComponente($obExercicio);
    $obIMontaRecursoDestinacao->geraFormulario($obFormulario);

    $obFormulario->Ok();

} else {

    $obLabel = new Label;
    $obLabel->setRotulo('AVISO');
    $obLabel->setValue('RELATÓRIO DESTINADO APENAS QUANDO USADO DESTINAÇÃO DE RECURSO.');
    $obFormulario = new Formulario;
    $obFormulario->addComponente($obLabel);
}

$obFormulario->show();

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
