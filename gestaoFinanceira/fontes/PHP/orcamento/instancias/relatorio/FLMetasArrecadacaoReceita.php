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
    * Página de Filtro para relatorio de metas de arrecadação
    * Data de Criação   : 24/08/2006

    * @author Analista: Cleisson Barboza
    * @author Desenvolvedor: Rodrigo

    * @ignore

    * Casos de uso: uc-02.01.34
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/HTML/Exercicio.class.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/HTML/SimNao.class.php';
include_once CAM_GF_ORC_COMPONENTES.'ISelectMultiploEntidadeUsuario.class.php';
include_once CAM_GF_ORC_COMPONENTES.'IPopUpRecurso.class.php';
include_once CAM_GF_ORC_COMPONENTES.'IIntervaloPopUpDotacao.class.php';
include_once CAM_GF_ORC_COMPONENTES.'IIntervaloPopUpEstruturalReceita.class.php';
include_once CAM_GF_ORC_NEGOCIO.'ROrcamentoEntidade.class.php';
include_once CAM_GF_ORC_COMPONENTES.'IMontaRecursoDestinacao.class.php';
include_once CAM_GF_ORC_COMPONENTES.'IIntervaloPopUpEstruturalReceita.class.php';
include_once CAM_GA_ADM_COMPONENTES.'IMontaAssinaturas.class.php';

Sessao::remove('filtroRelatorio');
Sessao::remove('inUnidadesMedidasMetas');
Sessao::remove('rsDadosAnexo');

$obROrcamentoEntidade = new ROrcamentoEntidade;
$obROrcamentoEntidade->setExercicio(Sessao::getExercicio());
$obROrcamentoEntidade->obRCGM->setNumCGM(Sessao::read('numCgm'));
$stOrdem = ' ORDER BY cod_entidade';
$obROrcamentoEntidade->listarUsuariosEntidade($rsEntidades, $stOrdem);
$arNomFiltro = Sessao::Read('filtroNomRelatorio');
while (!$rsEntidades->eof()) {
    $arNomFiltro['entidade'][$rsEntidades->getCampo('cod_entidade')] = $rsEntidades->getCampo('nom_cgm');
    $rsEntidades->proximo();
}
$rsEntidades->setPrimeiroElemento();

$stPrograma = 'RelatorioMetasArrecadacaoReceita';
if (empty($stAcao)) {
    $stAcao = 'incluir';
}
$pgOcul = 'OC'.$stPrograma.'.php';
$pgJS   = 'JS'.$stPrograma.'.js';

$obForm = new Form;
$obForm->setAction(CAM_GF_ORC_INSTANCIAS.'relatorio/OCGeraRelatorioMetasArrecadacaoReceita.php');
$obForm->setTarget('telaPrincipal');

//Definição dos componentes
$obISelectEntidadeUsuario = new ISelectMultiploEntidadeUsuario();
$obExercicio              = new Exercicio();
$obSimNao                 = new SimNao();
$obIntervaloEstrutural    = new IIntervaloPopUpEstruturalReceita();

$obIMontaRecursoDestinacao = new IMontaRecursoDestinacao;
$obIMontaRecursoDestinacao->setFiltro(true);

$obIntervaloPopUpEstruturalReceita = new IIntervaloPopUpEstruturalReceita($boDedutora = true);
$obIntervaloPopUpEstruturalReceita->obIPopUpEstruturalReceitaInicial->obCampoCod->setName('stCodReceitaDedutoraInicial');
$obIntervaloPopUpEstruturalReceita->obIPopUpEstruturalReceitaFinal->obCampoCod->setName('stCodReceitaDedutoraFinal');
$obIntervaloPopUpEstruturalReceita->obIPopUpEstruturalReceitaInicial->obCampoCod->setId('stDescricaoReceitaDedutoraInicial');
$obIntervaloPopUpEstruturalReceita->obIPopUpEstruturalReceitaFinal->obCampoCod->setId('stDescricaoReceitaDedutoraFinal');

$obSimNao->setRotulo('*Demonstrar Sintéticas');
$obSimNao->setChecked('N');

$obHdnAcao = new Hidden;
$obHdnAcao->setName ('stAcao');
$obHdnAcao->setValue($stAcao);

$obHdnStCtrl = new Hidden;
$obHdnStCtrl->setName ('stCtrl');

// Instanciação do objeto Lista de Assinaturas
// Limpa papeis das Assinaturas na Sessão
$arAssinaturas = Sessao::read('assinaturas');
$arAssinaturas['papeis'] = array();
Sessao::write('assinaturas',$arAssinaturas);

$obMontaAssinaturas = new IMontaAssinaturas;
$obMontaAssinaturas->setEventosCmbEntidades($obISelectEntidadeUsuario);

$obFormulario = new Formulario;
$obFormulario->addForm      ($obForm);
$obFormulario->addHidden    ($obHdnAcao);
$obFormulario->addHidden    ($obHdnStCtrl);
$obFormulario->setAjuda     ('UC-02.01.34');
$obFormulario->addTitulo    ('Dados para o filtro');
$obFormulario->addComponente($obISelectEntidadeUsuario);
$obFormulario->addComponente($obExercicio);
$obFormulario->addComponente($obSimNao);
$obFormulario->addComponente($obIntervaloEstrutural);
if (Sessao::getExercicio() == '2008') {
    $obFormulario->addComponente($obIntervaloPopUpEstruturalReceita);
}
$obIMontaRecursoDestinacao->geraFormulario($obFormulario);

// Injeção de código no formulário
$obMontaAssinaturas->geraFormulario($obFormulario);

$obFormulario->Ok();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
