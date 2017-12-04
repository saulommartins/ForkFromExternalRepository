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
    * Página de Filtro de IPopUpMapaCompras
    * Data de Criação   :23/10/2006

    * @author Analista: Cleisson Barbosa
    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @ignore

    $Revision: 21056 $
    $Name$
    $Author: bruce $
    $Date: 2007-03-15 12:00:25 -0300 (Qui, 15 Mar 2007) $

    * Casos de uso: uc-03.04.05
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GP_COM_COMPONENTES."IMontaSolicitacao.class.php"                                    );
include_once ( CAM_GP_COM_COMPONENTES."IPopUpObjeto.class.php"                                         );
include_once ( CAM_GF_ORC_COMPONENTES."IPopUpDotacao.class.php"                                        );
include_once ( CAM_GF_ORC_COMPONENTES."ISelectMultiploEntidadeGeral.class.php"                         );
include_once ( CAM_GP_ALM_COMPONENTES."IPopUpCentroCustoUsuario.class.php"                             );
include_once ( CAM_GP_ALM_COMPONENTES."IMontaItemUnidade.class.php"                                    );

$stPrograma = "BuscaMapaCompras";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include_once( $pgJs );

$stAcao = $request->get("stAcao");

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnCampoNom = new Hidden;
$obHdnCampoNom->setName( "campoNom" );
$obHdnCampoNom->setValue( $_REQUEST['campoNom']);

$obHdnCampoNum = new Hidden;
$obHdnCampoNum->setName( "campoNum" );
$obHdnCampoNum->setValue( $_REQUEST['campoNum']);

$obHdnTipoBusca = new Hidden;
$obHdnTipoBusca->setName  ( 'stTipoBusca'           );
$obHdnTipoBusca->setValue (  $_REQUEST['stTipoBusca'] );

$obHdnExercicio = new Hidden;
$obHdnExercicio->setName ( 'stExercicioMapa' );
$obHdnExercicio->setValue ( $_REQUEST['stExercicioMapa'] );

$obHdnAutEmp = new Hidden;
$obHdnAutEmp->setName ( 'boAutEmp' );
$obHdnAutEmp->setValue ( $_REQUEST['boAutEmp'] );

$obForm = new Form;
$obForm->setAction ( $pgList  );

//// SelectMultiploNetidadesGeral
$obISelectEntidadeGeral = new ISelectMultiploEntidadeGeral();
$obISelectEntidadeGeral->setNull ( true );

/// codigo da solicitação
$obTextCodSolicitacao = new TextBox;
$obTextCodSolicitacao->setName  ( 'txtCodSolicitacao'                );
$obTextCodSolicitacao->setID    ( 'txtCodSolicitacao'                );
$obTextCodSolicitacao->setRotulo( 'Código da Solicitação'            );
$obTextCodSolicitacao->setTitle ( 'Informe o código da solicitação.' );
$obTextCodSolicitacao->setInteiro ( true );

////objeto
$obObjeto = new TextBox;
$obObjeto->setName  ( 'stObjeto' );
$obObjeto->setRotulo ("Objeto");
$obObjeto->setID    ( 'stObjeto' );
$obObjeto->setTitle ( 'Informe o código do objeto' );
$obObjeto->setInteiro   ( true );

/// Periodicidade
$obPeriodicidade = new Periodicidade();
$obPeriodicidade->setExercicio      ( Sessao::getExercicio());
$obPeriodicidade->setValue          ( 4                 );
$obPeriodicidade->obDataInicial->setName    ( "stDtInicial" );
$obPeriodicidade->obDataFinal->setName      ( "stDtFinal" );

/// item
$obTxtItem = new TextBox;
$obTxtItem->setRotulo("Codigo do item");
$obTxtItem->setName  ( 'inCodItem'                 );
$obTxtItem->setID    ( 'inCodItem'                 );
$obTxtItem->setTitle ( 'Informe o código do item.' );
$obTxtItem->setInteiro   ( true );

/// Dotacao
$obTxtDotacao = new TextBox;
$obTxtDotacao->setRotulo("Dotação");
$obTxtDotacao->setName  ( 'inCodDotacao'                 );
$obTxtDotacao->setID    ( 'inCodDotacao'                 );
$obTxtDotacao->setTitle ( 'Informe o código da dotação.' );
$obTxtDotacao->setInteiro   ( true );

//centro de custo
$obTxtCentroCusto = new TextBox;
$obTxtCentroCusto->setRotulo("Centro de Custo");
$obTxtCentroCusto->setName  ( 'inCodCentroCusto'                 );
$obTxtCentroCusto->setID    ( 'inCodCentroCusto'                 );
$obTxtCentroCusto->setTitle ( 'Informe o código do Centro de Custo.' );
$obTxtCentroCusto->setInteiro   ( true );

$obFormulario = new Formulario;
$obFormulario->addForm($obForm);
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnCampoNom  );
$obFormulario->addHidden( $obHdnCampoNum  );
$obFormulario->addHidden( $obHdnTipoBusca );
$obFormulario->addHidden( $obHdnExercicio );
$obFormulario->addHidden( $obHdnAutEmp    );
$obFormulario->addTitulo( "Dados para filtro" );

$obFormulario->addComponente ( $obISelectEntidadeGeral );
$obFormulario->addComponente ( $obTextCodSolicitacao   );
$obFormulario->addComponente ( $obObjeto               );
$obFormulario->addComponente ( $obPeriodicidade        );
$obFormulario->addComponente ( $obTxtItem              );
$obFormulario->addComponente ( $obTxtDotacao           );
$obFormulario->addComponente ( $obTxtCentroCusto       );

$obFormulario->ok();
$obFormulario->show();

?>
