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
    * Página de Filtro para Relatório de Demonstrativo de Gastos com Pessoal
    * Data de Criação: 07/01/2015
    * @author Analista: 
    * @author Desenvolvedor: Arthur Cruz
    * @ignore
    *   
    * $Id: 
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GF_ORC_COMPONENTES.'ISelectMultiploEntidadeUsuario.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "RelatorioDespesaTotalPessoal";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";
$pgGera = "OCGera".$stPrograma.".php";

include_once $pgJS;

$stAcao = $request->get('stAcao');

$obForm = new Form;
$obForm->setAction(CAM_GPC_TCEMG_RELATORIOS.$pgGera);
$obForm->setTarget('telaPrincipal');

$obHdnCaminho = new Hidden;
$obHdnCaminho->setValue(CAM_GPC_TCEMG_RELATORIOS.$pgOcul);
$obHdnCaminho->setName("stCaminho");

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obCmbSituacao = new Select();
$obCmbSituacao->setName   ( "stSituacao" );
$obCmbSituacao->setId     ( "stSituacao" );
$obCmbSituacao->setRotulo ( "Situação"   );
$obCmbSituacao->setTitle  ( "Informe o Tipo de Situação." );
$obCmbSituacao->setNull   ( false );
$obCmbSituacao->addOption ( "","Selecione"           );
$obCmbSituacao->addOption ( "empenhado" ,"Empenhado" );
$obCmbSituacao->addOption ( "liquidado" ,"Liquidado" );
$obCmbSituacao->addOption ( "pago"      ,"Pago"      );

//Defini o objeto Entidades
$obISelectEntidade = new ISelectMultiploEntidadeUsuario();

$obCmbPeriodo = new Select;
$obCmbPeriodo->setRotulo            ( "Periodicidade");
$obCmbPeriodo->setName              ( "stPeriodicidade");
$obCmbPeriodo->addOption            ( ""         , "Selecione");
$obCmbPeriodo->addOption            ( "Mes"      , "Mensal");
$obCmbPeriodo->addOption            ( "Bimestre" , "Bimestral");
$obCmbPeriodo->addOption            ( "Trimestre"    , "Trimestral");
$obCmbPeriodo->addOption            ( "Quadrimestre" , "Quadrimestral");
$obCmbPeriodo->addOption            ( "Semestre" , "Semestral");
$obCmbPeriodo->setNull              ( false );
$obCmbPeriodo->setStyle             ( "width: 220px");
$obCmbPeriodo->obEvento->setOnChange( "buscaDado('preencheSpan')");

$spnCmbPeriodo = new Span();
$spnCmbPeriodo->setId( 'spnPeriodicidade' );

//****************************************//
//Monta FORMULARIO
//****************************************//

$obFormulario = new Formulario;
$obFormulario->addForm      ( $obForm );
$obFormulario->addTitulo    ( "Demonstrativo RCL" );
$obFormulario->addHidden    ( $obHdnAcao    );
$obFormulario->addHidden    ( $obHdnCtrl    );
$obFormulario->addHidden    ( $obHdnCaminho );

$obFormulario->addComponente( $obISelectEntidade );
$obFormulario->addComponente( $obCmbPeriodo  );
$obFormulario->addSpan      ( $spnCmbPeriodo );
$obFormulario->addComponente( $obCmbSituacao );

$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>