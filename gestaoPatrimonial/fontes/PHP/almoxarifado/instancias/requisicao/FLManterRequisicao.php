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
    * Página de Filtro de Requisição
    * Data de Criação   : 03/03/2006

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @ignore

    * Casos de uso: uc-03.03.10
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once( CAM_GP_ALM_NEGOCIO."RAlmoxarifadoAlmoxarifado.class.php" );
include_once( CAM_GP_ALM_COMPONENTES."IPopUpItem.class.php");
include_once( CAM_GP_ALM_COMPONENTES."IPopUpCentroCusto.class.php");
include_once( CAM_GP_ALM_COMPONENTES."IPopUpMarca.class.php");
include_once( CAM_GP_ALM_COMPONENTES."ISelectMultiploAlmoxarifado.class.php" );

$stLink = "";
Sessao::remove('link');
Sessao::remove('stLink');

//Define o nome dos arquivos PHP
$stPrograma = "ManterRequisicao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once($pgJS);

$rsAlmoxarifados = new RecordSet;
$obRAlmoxarifado = new RAlmoxarifadoAlmoxarifado;

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
        $stAcao = "alterar";
}

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $stAcao  );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName   ( "stCtrl" );
$obHdnCtrl->setValue  ( $stCtrl  );

$obForm = new Form;
$obForm->setAction ( $pgList  );

$obTxtExercicio = new TextBox;
$obTxtExercicio->setRotulo ( "Exercício"   );
$obTxtExercicio->setName   ( "stExercicio" );
$obTxtExercicio->setValue  ( Sessao::getExercicio() );
$obTxtExercicio->setSize ( 5 );
$obTxtExercicio->setMaxLength ( 4 );
$obTxtExercicio->setTitle ( "Informe o exercício." );

$obSelectAlmoxarifado = new ISelectMultiploAlmoxarifado;

/*//Define o objeto SelectMultiplo para armazenar os ELEMENTOS
$obCmbAlmoxarifados = new SelectMultiplo();
$obCmbAlmoxarifados->setName   ('inCodAlmoxarifado');
$obCmbAlmoxarifados->setRotulo ( "Almoxarifado" );
$obCmbAlmoxarifados->setTitle  ( "Selecione os almoxarifados." );

// lista de atributos disponiveis
$obCmbAlmoxarifados->SetNomeLista1 ('inCodAlmoxarifadoDisponivel');
$obCmbAlmoxarifados->setCampoId1   ( 'codigo' );
$obCmbAlmoxarifados->setCampoDesc1 ( '[codigo]-[nom_a]' );
$obCmbAlmoxarifados->SetRecord1    ( $rsAlmoxarifados );
$rsRecordset = new RecordSet;

// lista de atributos selecionados
$obCmbAlmoxarifados->SetNomeLista2 ('inCodAlmoxarifado');
$obCmbAlmoxarifados->setCampoId2   ('codigo');
$obCmbAlmoxarifados->setCampoDesc2 ('[codigo]-[nom_a]');
$obCmbAlmoxarifados->SetRecord2    ( $rsRecordset );*/

$obTxtCodRequisicao = new TextBox;
$obTxtCodRequisicao->setRotulo ( "Requisição"      );
$obTxtCodRequisicao->setName   ( "inCodRequisicao" );
$obTxtCodRequisicao->setValue  ( $inCodRequisicao  );
$obTxtCodRequisicao->setSize ( 5 );
$obTxtCodRequisicao->setMaxLength ( 10 );
$obTxtCodRequisicao->setTitle ( "Informe o código da requisição." );

$obTxtObservacao = new TextBox;
$obTxtObservacao->setRotulo( 'Observação'     );
$obTxtObservacao->setName  ( 'stObservacao'  );
$obTxtObservacao->setSize  ( 50              );
$obTxtObservacao->setMaxLength( 160           );
$obTxtObservacao->setTitle ( 'Informe a observação' );
$obTxtObservacao->setValue ( $stObservacao );

include_once ( CAM_GA_CGM_COMPONENTES."IPopUpCGMVinculado.class.php" );

$obCGMRequisitante = new IPopUpCGMVinculado( $obForm );
$obCGMRequisitante->setTabelaVinculo ('administracao.usuario' );
$obCGMRequisitante->setCampoVinculo ( 'numcgm' );
$obCGMRequisitante->setRotulo ( 'Requisitante' );
$obCGMRequisitante->setTitle  ( 'Informe o requisitante.' );
$obCGMRequisitante->setNomeVinculo ( 'CGM do requisitante' );
$obCGMRequisitante->setName   ( 'stNomCGMRequisitante');
$obCGMRequisitante->setId     ( 'stNomCGMRequisitante');
$obCGMRequisitante->obCampoCod->setName ( 'inCGMRequisitante' );
$obCGMRequisitante->obCampoCod->setId   ( 'inCGMRequisitante' );
$obCGMRequisitante->obCampoCod->setSize ( 10 );
$obCGMRequisitante->obCampoCod->setNull ( true                      );
$obCGMRequisitante->setNull ( true                      );

$obBscCGMSolicitante = new IPopUpCGM($obForm);
$obBscCGMSolicitante->setId                    ( 'stNomCGMSolicitante' );
$obBscCGMSolicitante->setRotulo                ( 'Solicitante'  );
$obBscCGMSolicitante->setTitle                 ( 'Informe o CGM do solicitante.' );
$obBscCGMSolicitante->setTipo                  ( 'geral' );
$obBscCGMSolicitante->setValue             ( $stNomCGMSolicitante );
$obBscCGMSolicitante->setNull                  ( true );
$obBscCGMSolicitante->obCampoCod->setSize      (10);
$obBscCGMSolicitante->obCampoCod->setName      ( 'inCGMSolicitante' );
$obBscCGMSolicitante->obCampoCod->setValue ( $inCGMSolicitante );
$obBscCGMSolicitante->obCampoCod->obEvento->setOnChange( "executaFuncaoAjax('buscaSolicitante');" );
if ($stAcao == "consultar") {
    $obLblStatus = new Label;
    $obLblStatus->setRotulo ( 'Status'  );
    $obLblStatus->setId     ( 'stStatus' );
    $obLblStatus->setValue  ( $stStatus );
}

$obCmbObservacao = new TipoBusca( $obTxtObservacao );
$obCmbObservacao->obCmbTipoBusca->setValue ( 'contem' );

$obBscItem = new IPopUpItem($obForm);
$obBscItem->setNull( true );
$obBscItem->setServico( false );
$obBscItem->setRetornaUnidade( false );

$obBscMarca = new IPopUpMarca($obForm);
$obBscMarca->setNull(true);

$obBscCentroCusto = new IPopUpCentroCusto($obForm);
$obBscCentroCusto->setNull(true);

$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->setAjuda("UC-03.03.10");
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addTitulo( "Dados para Filtro" );
$obFormulario->addComponente( $obTxtExercicio );
$obFormulario->addComponente ( $obSelectAlmoxarifado );
$obFormulario->addComponente( $obTxtCodRequisicao );
$obFormulario->addComponente( $obCmbObservacao );
$obFormulario->addComponente( $obCGMRequisitante );
$obFormulario->addComponente ( $obBscCGMSolicitante );
$obFormulario->addComponente( $obBscItem );
$obFormulario->addComponente( $obBscMarca );
$obFormulario->addComponente( $obBscCentroCusto );
$obFormulario->Ok();
$obFormulario->Show();
