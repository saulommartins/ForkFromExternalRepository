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
    * Filtro de Demonstrativo de Riscos Fiscais
    * Data de Criação   : 02/06/2009

    * @author Analista      Tonismar Régis Bernardo
    * @author Desenvolvedor Eduardo Paculski Schitz

    * @package URBEM
    * @subpackage

    * @ignore

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GF_ORC_COMPONENTES."ISelectMultiploEntidadeUsuario.class.php";

//Define o nome dos arquivos PHP
$stPrograma = 'ManterRiscosFiscais';
$pgFilt     = 'FL'.$stPrograma.'.php';
$pgList     = 'LS'.$stPrograma.'.php';
$pgForm     = 'FM'.$stPrograma.'.php';
$pgProc     = 'PR'.$stPrograma.'.php';
$pgOcul     = 'OC'.$stPrograma.'.php';
$pgJs       = 'JS'.$stPrograma.'.js';

Sessao::remove('filtro');

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction($pgList);
$obForm->settarget('telaPrincipal');

$obHdnAcao =  new Hidden;
$obHdnAcao->setName ('stAcao');
$obHdnAcao->setId   ('stAcao');
$obHdnAcao->setValue($_REQUEST['stAcao']);

$obExercicio = new Exercicio();
$obExercicio->setName('stExercicio');
$obExercicio->setId  ('stExercicio');
$obExercicio->obEvento->setOnBlur("montaParametrosGET('buscaEntidades');");

$obISelectEntidade = new ISelectMultiploEntidadeUsuario();
$obISelectEntidade->SetNomeLista2('inCodEntidade');

$obFormulario = new Formulario;
$obFormulario->addForm      ($obForm);
$obFormulario->addHidden    ($obHdnAcao);
$obFormulario->addTitulo    ('Dados para Filtro');
$obFormulario->addComponente($obExercicio);
$obFormulario->addComponente($obISelectEntidade);
$obFormulario->ok();
$obFormulario->show();

$jsOnload  = "montaParametrosGET('buscaEntidades');";

include CAM_FW_INCLUDE.'rodape.inc.php';
