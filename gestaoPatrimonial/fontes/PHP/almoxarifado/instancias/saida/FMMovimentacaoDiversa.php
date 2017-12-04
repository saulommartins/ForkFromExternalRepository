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
    * Página de Formulario
    * Data de criação : 02/12/2008

    * @author Analista: Diego Victoria
    * @author Programador: Diego Victoria

    * @ignore

    $Id: .php 35831 2008-11-20 20:35:21Z luiz $

    **/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GP_ALM_NEGOCIO."RAlmoxarifadoAlmoxarifado.class.php";
include_once CAM_GP_ALM_NEGOCIO."RAlmoxarifadoPermissaoCentroDeCustos.class.php";
include_once CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoAtributoRequisicaoItemValor.class.php";
include_once CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoAtributoEstoqueMaterialValor.class.php";
include_once CAM_GP_ALM_COMPONENTES."ISelectAlmoxarifado.class.php";
include_once CAM_GP_ALM_COMPONENTES."IMontaItemQuantidade.class.php";

Sessao::remove('transf3');
Sessao::write('arItens',array());

$stAcao              = $_REQUEST['stAcao'];

$obRAlmoxarifado = new RAlmoxarifadoAlmoxarifado;
$obRAlmoxarifado->setCodigo( $inCodAlmoxarifado );
$obRAlmoxarifado->listar( $rsAlmoxarifados );

//Define o nome dos arquivos PHP
$stPrograma = "MovimentacaoDiversa";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once ( $pgJS );

//DEFINICAO DOS COMPONENTES DO FORMULARIO
$obForm = new Form;
$obForm->setAction ( $pgProc  );
$obForm->setTarget ( 'oculto' );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setId    ( "stCtrl" );
$obHdnCtrl->setValue ( $stCtrl  );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao  );

$obLblExercicio = new Label;
$obLblExercicio->setRotulo( 'Exercício' );
$obLblExercicio->setId    ( 'stExercicio' );
$obLblExercicio->setValue ( Sessao::getExercicio() );

$obSelectAlmoxarifado = new ISelectAlmoxarifado;
$obSelectAlmoxarifado->setNull( false );
$obSelectAlmoxarifado->setId('inCodAlmoxarifado');
$obSelectAlmoxarifado->obEvento->setOnClick("montaParametrosGET('limpaItem');");

$obBscItem = new IMontaItemQuantidade($obForm, $obSelectAlmoxarifado, true);
$obBscItem->setPerecivel( true );
$obBscItem->obIMontaItemUnidade->obIPopUpCatalogoItem->setObrigatorioBarra(true);
$obBscItem->obIMontaItemUnidade->obIPopUpCatalogoItem->setComSaldo(true);
$obBscItem->obIMontaItemUnidade->obIPopUpCatalogoItem->setAlmoxarifadoOrigem($obSelectAlmoxarifado);
$obBscItem->obIMontaItemUnidade->obIPopUpCatalogoItem->obCampoCod->obEvento->setOnChange( "montaParametrosGET('verificaItemFrota','inCodItem, inCodAlmoxarifado');");
$obBscItem->obIMontaItemUnidade->obIPopUpCatalogoItem->obCampoCod->obEvento->setOnFocus("montaParametrosGET('verificaItemFrota','inCodItem,inCodAlmoxarifado');");

$obBscItem->obTxtQuantidade->setObrigatorioBarra(true);
$obBscItem->obTxtQuantidade->obEvento->setOnChange("montaParametrosGET('atualizaSaldo','nuQuantidade, stSaldo, inCodItem, inCodCentroCusto');");

$obBscItem->obCmbCentroCusto->setObrigatorioBarra(true);
$obBscItem->obCmbMarca->setObrigatorioBarra(true);

$obBscItem->obIMontaItemUnidade->obIPopUpCatalogoItem->obCampoCod->setId('inCodItem');
$obBscItem->obIMontaItemUnidade->obIPopUpCatalogoItem->obCampoCod->obEvento->setOnBlur('if (validaAlmoxarifado()) {'.$obBscItem->obIMontaItemUnidade->obIPopUpCatalogoItem->obCampoCod->obEvento->getOnBlur().'}');

$obTxtObservacao = new TextArea;
$obTxtObservacao->setName  ( "stObservacao" );
$obTxtObservacao->setRotulo( "Observação" );
$obTxtObservacao->setTitle ( "Informe a observação para requisição." );
$obTxtObservacao->setCols  ( 30 );
$obTxtObservacao->setRows  ( 3  );
$obTxtObservacao->setMaxCaracteres( 160 );
$obTxtObservacao->setValue ( $stObservacao );
$obTxtObservacao->setNull  ( true );
$obTxtObservacao->obEvento->setOnKeyUp("return  Contador(this, 160);");

$obLblRequisitante = new Label;
$obLblRequisitante->setRotulo ( 'Requisitante' );
$obLblRequisitante->setId     ( 'stRequisitante' );
$obLblRequisitante->setValue  ( Sessao::read('numCgm').' - '.Sessao::read('nomCgm') );

$obBscCGMSolicitante = new IPopUpCGM($obForm);
$obBscCGMSolicitante->setId                    ( 'stNomCGMSolicitante' );
$obBscCGMSolicitante->setRotulo                ( 'Solicitante'  );
$obBscCGMSolicitante->setTitle                 ( 'Informe o CGM do solicitante.' );
$obBscCGMSolicitante->setTipo                  ( 'geral' );

$obBscCGMSolicitante->setValue             ( Sessao::read('nomCgm')  );

$obBscCGMSolicitante->setNull                  ( false );
$obBscCGMSolicitante->obCampoCod->setSize      (10);
$obBscCGMSolicitante->obCampoCod->setName      ( 'inCGMSolicitante' );

$obBscCGMSolicitante->obCampoCod->setValue     ( Sessao::read('numCgm') );
$obBscCGMSolicitante->obCampoCod->obEvento->setOnChange( "executaFuncaoAjax('buscaSolicitante');" );

//$obBtnOk = new Ok(true);
$obBtnOk = new Ok();
$obBtnOk->obEvento->setOnClick("validaUsuarioSecundario('".$obBtnOk->obEvento->getOnClick()."');");

$stLocation = $pgList.'?'.Sessao::getId().'&stAcao='.$stAcao.$stFiltro;

$obBtnVoltar = new Button;
$obBtnVoltar->setName ( "btnVoltar" );
$obBtnVoltar->setValue(  $stAcao == "consultar" ? "Voltar" : "Cancelar" );
$obBtnVoltar->obEvento->setOnClick( "Cancelar('".$stLocation."');" );

$obBtnLimpar = new Button;
$obBtnLimpar->setName ( "btnOk" );
$obBtnLimpar->setValue(  "Limpar" );
$obBtnLimpar->obEvento->setOnClick( "Limpar();" );

$obSpnItens = new Span;
$obSpnItens->setId( "spnItens" );

$obHdUsuario = new Hidden;
$obHdUsuario->setName("stCGMUsuario");
$obHdUsuario->setId("stCGMUsuario");
$obHdUsuario->setValue( "" );

$obSpnDadosFrota = new Span;
$obSpnDadosFrota->setId( "spnDadosFrota" );

$obFormulario = new Formulario;
$obFormulario->addTitulo     ( 'Dados da Requisição'    );
$obFormulario->addForm       ( $obForm                  );
$obFormulario->setAjuda      ( "UC-03.03.10"            );
$obFormulario->addHidden     ( $obHdnAcao               );
$obFormulario->addHidden     ( $obHdnCtrl               );
$obFormulario->addHidden     ( $obHdUsuario             );
$obFormulario->addComponente ( $obLblExercicio          );
$obFormulario->addComponente ( $obSelectAlmoxarifado    );

if (( $stAcao == "anular" ) || ( $stAcao == "consultar" )) {
    $obFormulario->addComponente ( $obLblObservacao );
} else {
    $obFormulario->addComponente ( $obTxtObservacao );
}

$obFormulario->addComponente ( $obLblRequisitante    );
$obFormulario->addComponente ( $obBscCGMSolicitante  );
$obFormulario->addTitulo     ( 'Dados do Item'       );
$obBscItem->geraFormulario   ( $obFormulario         );
$obFormulario->addSpan       ( $obSpnDadosFrota      );
$obFormulario->Incluir('Item', array ( $obBscItem->obIMontaItemUnidade->obIPopUpCatalogoItem, $obBscItem->obCmbMarca, $obBscItem->obCmbCentroCusto, $obBscItem->obTxtQuantidade ),false,false,'', false );
$obFormulario->addSpan       ( $obSpnItens          );
$obFormulario->defineBarra( array( $obBtnOk, $obBtnLimpar), "left", "" );

$obFormulario->Show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
