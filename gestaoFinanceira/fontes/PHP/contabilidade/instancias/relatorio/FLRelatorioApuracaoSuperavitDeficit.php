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
  * Página de Filtro de Relatório de Apuração de Superavit/Deficit Financeiro
  * Data de Criação: 21/10/2015

  * @author Analista:      Valtair
  * @author Desenvolvedor: Franver Sarmento de Moraes
  * @ignore
  *
  * $Id: FLRelatorioApuracaoSuperavitDeficit.php 64186 2015-12-11 20:36:20Z franver $
  * $Revision: 64186 $
  * $Author: franver $
  * $Date: 2015-12-11 18:36:20 -0200 (Fri, 11 Dec 2015) $
*/
require_once "../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php";
require_once "../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php";
require_once CAM_GF_ORC_COMPONENTES.'ISelectMultiploEntidadeUsuario.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "RelatorioApuracaoSuperavitDeficit";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";
$pgGera = "OCGera".$stPrograma.".php";


$stCtrl = $request->get("stCtrl");
$stAcao = $request->get("stAcao");

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
$obForm = new Form;
$obForm->setAction(CAM_GF_CONT_INSTANCIAS.'relatorio/'.$pgGera);
$obForm->setTarget('telaPrincipal');

$obHdnCaminho = new Hidden;
$obHdnCaminho->setValue(CAM_GF_CONT_INSTANCIAS.'relatorio/'.$pgOcul);
$obHdnCaminho->setName("stCaminho");

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setId   ("stAcao");
$obHdnAcao->setName ("stAcao");
$obHdnAcao->setValue($stAcao);

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ("stCtrl");
$obHdnCtrl->setId   ("stCtrl");
$obHdnCtrl->setValue($stCtrl);

//Defini o objeto Entidades
$obISelectEntidade = new ISelectMultiploEntidadeUsuario();

$obExercicio = new Exercicio();
$obExercicio->setLabel(true);

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm );
$obFormulario->addHidden     ( $obHdnCtrl );
$obFormulario->addHidden     ( $obHdnAcao );
$obFormulario->addTitulo     ( "Relatório Apuração Superavit/Deficit Financeiro " );
$obFormulario->addComponente( $obISelectEntidade );
$obFormulario->addComponente( $obExercicio );
$obFormulario->OK();
$obFormulario->show();

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>