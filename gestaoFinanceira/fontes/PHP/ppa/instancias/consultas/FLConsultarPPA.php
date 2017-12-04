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
    * Filtro para consulta de PPA
    * Data de Criação   : 21/05/2009

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
include_once CAM_GF_PPA_COMPONENTES."ITextBoxSelectPPA.class.php";

$stAcao = $request->get('stAcao');
$stCtrl = $_REQUEST['stCtrl'];

//Define o nome dos arquivos PHP
$stPrograma = "ConsultarPPA";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php?".Sessao::getId();
$pgJs       = "JS".$stPrograma.".js";

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction($pgList);
$obForm->settarget("telaPrincipal");

$obHdnAcao =  new Hidden;
$obHdnAcao->setName ("stAcao");
$obHdnAcao->setValue($stAcao);

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName ("stCtrl" );
$obHdnCtrl->setValue($stCtrl);

$obITextBoxSelectPPA = new ITextBoxSelectPPA();
$obITextBoxSelectPPA->setPreencheUnico(true);

$obRdTodos = new Radio;
$obRdTodos->setName   ('boHomologado');
$obRdTodos->setRotulo ('Status');
$obRdTodos->setTitle  ('Selecione o status para o filtro');
$obRdTodos->setValue  ('n');
$obRdTodos->setLabel  ('Todos');
$obRdTodos->setNull   (true);
$obRdTodos->setChecked(true);

$obRdHomologado = new Radio;
$obRdHomologado->setName   ('boHomologado');
$obRdHomologado->setValue  ('t');
$obRdHomologado->setLabel  ('Homologado');
$obRdHomologado->setNull   (true);

$obRdNaoHomologado = new Radio;
$obRdNaoHomologado->setName   ('boHomologado');
$obRdNaoHomologado->setValue  ('f');
$obRdNaoHomologado->setLabel  ('Não Homologado');
$obRdNaoHomologado->setNull   (true);

$obRadioHomologado = array($obRdTodos, $obRdHomologado, $obRdNaoHomologado);

$obFormulario = new Formulario;
$obFormulario->addForm          ($obForm);
$obFormulario->addHidden        ($obHdnAcao);
$obFormulario->addHidden        ($obHdnCtrl);
$obFormulario->addTitulo        ('Dados para Filtro');
$obFormulario->addComponente    ($obITextBoxSelectPPA);
$obFormulario->agrupaComponentes($obRadioHomologado);
$obFormulario->ok();
$obFormulario->show();
