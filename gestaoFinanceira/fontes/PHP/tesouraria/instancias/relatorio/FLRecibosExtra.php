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
  * Página de Formulario de relatório Emitir Recibo Extra
  * Data de Criação   : 25/11/2005
  * @author Analista: Cleisson Barboza
  * @author Desenvolvedor: Fernando Zank Correa Evangelista
  * @ignore
  * $Id: FLRecibosExtra.php 66615 2016-10-04 19:38:37Z carlos.silva $
  * Casos de uso: uc-02.04.32
  */

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once CAM_GF_EMP_COMPONENTES.'IPopUpCredor.class.php';
require_once CAM_GF_ORC_COMPONENTES.'ISelectMultiploEntidadeUsuario.class.php';
require_once CAM_GF_ORC_COMPONENTES.'IPopUpRecurso.class.php';
require_once CAM_GF_ORC_COMPONENTES.'IMontaRecursoDestinacao.class.php';
require_once CAM_GF_CONT_COMPONENTES.'IPopUpContaBanco.class.php';
require_once CAM_GF_CONT_COMPONENTES.'IPopUpContaAnalitica.class.php';
require_once CAM_GA_ADM_COMPONENTES.'IMontaAssinaturas.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "RecibosExtra";
$pgFilt  = "FL".$stPrograma.".php";
$pgList  = "LS".$stPrograma.".php";
$pgForm  = "FM".$stPrograma.".php";
$pgProc  = "PR".$stPrograma.".php";
$pgOcul  = "OC".$stPrograma.".php";
$pgOcRel = "OCGeraRelatorio".$stPrograma."Birt.php";
$pgJS    = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');

$obForm = new Form;
$obForm->setAction($pgOcRel);
$obForm->setTarget('telaPrincipal');

$obISelectEntidadeUsuario = new ISelectMultiploEntidadeUsuario;

$obTxtExercicio = new exercicio;
$obTxtExercicio->setTitle('Informe o exercício.');

$obPeriodicidade = new Periodicidade;
$obPeriodicidade->setExercicio(Sessao::getExercicio());
$obPeriodicidade->setValue    (4);
$obPeriodicidade->setRotulo   ('Periodicidade da Emissão');
$obPeriodicidade->setTitle    ('Informe a periodicidade de emissão.');
$obPeriodicidade->setIdComponente ('1');
$obPeriodicidade->setNull       (false);

$obPeriodicidadeBaixa = new Periodicidade;
$obPeriodicidadeBaixa->setExercicio(Sessao::getExercicio());
$obPeriodicidadeBaixa->setValue    (4);
$obPeriodicidadeBaixa->setRotulo   ('Periodicidade da Baixa');
$obPeriodicidadeBaixa->setTitle    ('Informe a periodicidade da baixa.');
$obPeriodicidadeBaixa->setIdComponente ('2');

$obICredor = new IPopUpCredor($obForm);
$obICredor->setNull(true);

$obIMontaRecursoDestinacao = new IMontaRecursoDestinacao;
$obIMontaRecursoDestinacao->setFiltro(true);

$obIContaBanco = new IPopUpContaBanco;
$obIContaBanco->setNull  (true);
$obIContaBanco->setRotulo('Conta Caixa/Banco');

$obIContaAnalitica = new IPopUpContaAnalitica();
$obIContaAnalitica->setRotulo('Conta de Receita/Despesa');
$obIContaAnalitica->setTitle ('');
$obIContaAnalitica->setNull  (true);

$obRdbTipoDemonstracao = new Radio;
$obRdbTipoDemonstracao->setRotulo ('Demonstrar');
$obRdbTipoDemonstracao->setName   ('stTipoDemonstracao');
$obRdbTipoDemonstracao->setValue  ('todos');
$obRdbTipoDemonstracao->setLabel  ('Receitas e Despesas');
$obRdbTipoDemonstracao->setChecked(true);
$obRdbTipoDemonstracao->setNull   (false);

$obRdbTipoDemonstracao2 = new Radio;
$obRdbTipoDemonstracao2->setRotulo('Demonstrar');
$obRdbTipoDemonstracao2->setName  ('stTipoDemonstracao');
$obRdbTipoDemonstracao2->setValue ('R');
$obRdbTipoDemonstracao2->setLabel ('Somente Receitas');
$obRdbTipoDemonstracao2->setNull  (false);

$obRdbTipoDemonstracao3 = new Radio;
$obRdbTipoDemonstracao3->setRotulo('Demonstrar');
$obRdbTipoDemonstracao3->setName  ('stTipoDemonstracao');
$obRdbTipoDemonstracao3->setValue ('D');
$obRdbTipoDemonstracao3->setLabel ('Somente Despesas');
$obRdbTipoDemonstracao3->setNull  (false);

$obMontaAssinaturas = new IMontaAssinaturas;
$obMontaAssinaturas->setOpcaoAssinaturas   (false);
$obMontaAssinaturas->setCampoEntidades     ('inCodEntidade');
$obMontaAssinaturas->setFuncaoJS           ();
$obMontaAssinaturas->setEventosCmbEntidades($obISelectEntidadeUsuario);

$obCbmBaixado = new Select;
$obCbmBaixado->setRotulo('Baixado');
$obCbmBaixado->setId    ('stBaixado');
$obCbmBaixado->setName  ('stBaixado');
$obCbmBaixado->addOption('todos', 'Todos');
$obCbmBaixado->addOption('sim', 'Sim');
$obCbmBaixado->addOption('nao', 'Nao');
$obCbmBaixado->setNull  (false);

$obCbmOrdenacao = new Select;
$obCbmOrdenacao->setRotulo('Ordenação');
$obCbmOrdenacao->setId    ('stOrdenacao');
$obCbmOrdenacao->setName  ('stOrdenacao');
$obCbmOrdenacao->addOption(''       , 'Selecione');
$obCbmOrdenacao->addOption('credor' , 'Credor');
$obCbmOrdenacao->addOption('recurso', 'Recurso');
$obCbmOrdenacao->addOption('caixa'  , 'Conta Caixa/Banco');
$obCbmOrdenacao->addOption('receita', 'Conta de Receita/Despesa');
$obCbmOrdenacao->addOption('baixa'  , 'Data de Baixa');
$obCbmOrdenacao->setNull  (true);

//DEFINICAO DO FORMULARIO

$obFormulario = new Formulario;
$obFormulario->addForm  ($obForm);
$obFormulario->addTitulo('Dados para Filtro');

$obFormulario->addComponente($obISelectEntidadeUsuario);
$obFormulario->addComponente($obTxtExercicio);
$obFormulario->addComponente($obPeriodicidade);
$obFormulario->addComponente($obPeriodicidadeBaixa);
$obFormulario->addComponente($obICredor);
$obIMontaRecursoDestinacao->geraFormulario($obFormulario);
$obFormulario->addComponente($obIContaBanco);
$obFormulario->addComponente($obIContaAnalitica);
$obFormulario->agrupaComponentes(array($obRdbTipoDemonstracao, $obRdbTipoDemonstracao2, $obRdbTipoDemonstracao3));
$obFormulario->addComponente($obCbmBaixado);
$obFormulario->addComponente($obCbmOrdenacao);

$obMontaAssinaturas->geraFormulario($obFormulario);

$obFormulario->Ok();
$obFormulario->show();

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
