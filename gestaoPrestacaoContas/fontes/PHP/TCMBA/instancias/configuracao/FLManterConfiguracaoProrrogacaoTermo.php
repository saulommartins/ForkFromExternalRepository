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
  * Página de Filtro de Configuração de Prorrogação de Termo de Parceria/Subvenção/OSCIP
  * Data de Criação: 21/10/2015

  * @author Analista: 
  * @author Desenvolvedor: Franver Sarmento de Moraes
  * @ignore
  *
  * $Id: FLManterConfiguracaoProrrogacaoTermo.php 63828 2015-10-21 20:04:39Z franver $
  * $Revision: 63828 $
  * $Author: franver $
  * $Date: 2015-10-21 18:04:39 -0200 (Wed, 21 Oct 2015) $
*/
require_once "../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php";
require_once "../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php";
require_once CAM_GF_ORC_COMPONENTES."ITextBoxSelectEntidadeGeral.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoProrrogacaoTermo";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

$stCtrl = $request->get("stCtrl");
$stAcao = $request->get("stAcao");
$stExercicioProcesso = $request->get('stExercicioProcesso', Sessao::getExercicio());

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
//Instancia o formulário
$obForm = new Form();
$obForm->setAction($pgForm);
$obForm->setTarget("telaPrincipal");

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden();
$obHdnAcao->setId   ( "stAcao" );
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//Define o objeto de controle
$obHdnCtrl = new Hidden();
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setId   ( "stCtrl" );
$obHdnCtrl->setValue( $stCtrl );

$obTxtExercicioProcesso = new TextBox();
$obTxtExercicioProcesso->setRotulo   ( "Exercício do Processo");
$obTxtExercicioProcesso->setTitle    ( "Informe o exercício do Processo."	);
$obTxtExercicioProcesso->setName     ( "stExercicioProcesso" );
$obTxtExercicioProcesso->setId       ( "stExercicioProcesso" );
$obTxtExercicioProcesso->setValue    ( $stExercicioProcesso );
$obTxtExercicioProcesso->setInteiro  ( false );
$obTxtExercicioProcesso->setNull     ( false );
$obTxtExercicioProcesso->setMaxLength( 4 );
$obTxtExercicioProcesso->setSize     ( 5 );

$obITextBoxSelectEntidadeGeral = new ITextBoxSelectEntidadeGeral();
$obITextBoxSelectEntidadeGeral->setNull( false );
$obITextBoxSelectEntidadeGeral->obTextBox->obEvento->setOnChange( " if(this.value != '') { montaParametrosGET('preencheTermos');} \n" );
$obITextBoxSelectEntidadeGeral->obSelect->obEvento->setOnChange ( " if(this.value != '') { montaParametrosGET('preencheTermos');} \n" );

$obCmbTermoParceria = new Select();
$obCmbTermoParceria->setRotulo("Termo de Parceira/Subvenção/OSCIP");
$obCmbTermoParceria->setTitle("Selecione um Termo de Parceria/Subvenção/OSCIP, para adicionar uma prorrogação.");
$obCmbTermoParceria->setName("stNumeroProcesso");
$obCmbTermoParceria->setId("stNumeroProcesso");
$obCmbTermoParceria->addOption("","Selecione");
$obCmbTermoParceria->setNull( false );

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario();
$obFormulario->addForm       ( $obForm );
$obFormulario->addHidden     ( $obHdnCtrl );
$obFormulario->addHidden     ( $obHdnAcao );
$obFormulario->addTitulo     ( "Dados para Filtro de Termos de Parceria/Subvenção/OSCIP" );
$obFormulario->addComponente ( $obTxtExercicioProcesso );
$obFormulario->addComponente ( $obITextBoxSelectEntidadeGeral );
$obFormulario->addComponente ( $obCmbTermoParceria );
$obFormulario->OK();
$obFormulario->show();

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
