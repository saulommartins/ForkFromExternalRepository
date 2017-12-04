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
    * Página de Formulario de Definição de Desoneração
    * Data de Criação   : 27/05/2005

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @ignore

    * $Id: FMManterDesoneracao.php 62186 2015-04-06 17:34:12Z jean $

    * Casos de uso: uc-05.03.04
*/

/*
$Log$
Revision 1.14  2006/10/02 09:11:43  domluc
#6973#

Revision 1.13  2006/09/15 11:50:40  fabio
corrigidas tags de caso de uso

Revision 1.12  2006/09/15 11:04:52  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once (CAM_GT_ARR_NEGOCIO."RARRDesoneracao.class.php"   );
include_once (CAM_GA_ADM_NEGOCIO."RCadastroDinamico.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterDesoneracao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once( $pgJS );

$obRARRDesoneracao = new RARRDesoneracao;
$obRARRDesoneracao->obRMONCredito->consultarMascaraCredito();
$stMascaraCredito = $obRARRDesoneracao->obRMONCredito->getMascaraCredito();

$obRARRDesoneracao->listarTipoDesoneracao( $rsTipo );

//DEFINICAO DOS COMPONENTES DO FORMULARIO
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( $_REQUEST["stCtrl"]  );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $_REQUEST["stAcao"]  );

$obHdnCodigoDesoneracao = new Hidden;
$obHdnCodigoDesoneracao->setName( "inCodigoDesoneracao" );
$obHdnCodigoDesoneracao->setValue( $_REQUEST["inCodigoDesoneracao"] );

if ($_REQUEST['stAcao'] == 'alterar') {
    $obHdnCodigoTipo = new Hidden;
    $obHdnCodigoTipo->setName( 'inCodigoTipo' );
    $obHdnCodigoTipo->setValue( $_REQUEST["inCodigoTipo"] );

    $obHdnCodigoCredito = new Hidden;
    $obHdnCodigoCredito->setName ( 'inCodigoCredito' );
    $obHdnCodigoCredito->setValue( $_REQUEST["inCodigoCredito"].'.'.$_REQUEST["inCodigoNatureza"].'.'.$_REQUEST["inCodigoGenero"].'.'.$_REQUEST["inCodigoEspecie"] );

    $obLblCodigoDesoneracao = new Label;
    $obLblCodigoDesoneracao->setTitle ( "Código da desoneração." );
    $obLblCodigoDesoneracao->setName( 'inCodigoDesoneracao'  );
    $obLblCodigoDesoneracao->setId  ( 'inCodigoDesoneracao'  );
    $obLblCodigoDesoneracao->setRotulo( "Código"             );
    $obLblCodigoDesoneracao->setValue ( $_REQUEST["inCodigoDesoneracao"] );

    $obLblTipo = new Label;
    $obLblTipo->setName  ( 'stTipo'              );
    $obLblTipo->setId    ( 'stTipo'              );
    $obLblTipo->setTitle ( "Tipo da desoneração." );
    $obLblTipo->setRotulo( "Tipo de Desoneração" );
    $obLblTipo->setValue ( $_REQUEST["stTipo"]   );

    $stCredito = $_REQUEST["inCodigoCredito"].'.'.$_REQUEST["inCodigoNatureza"].'.'.$_REQUEST["inCodigoGenero"].'.'.$_REQUEST["inCodigoEspecie"];
    $obLblCredito = new Label;
    $obLblCredito->setTitle  ( "Crédito para o qual a desoneração foi definida." );
    $obLblCredito->setName   ( 'stCredito' );
    $obLblCredito->setId     ( 'stCredito' );
    $obLblCredito->setRotulo ( 'Crédito'   );
    $obLblCredito->setValue  ( $stCredito  );
}

$obTxtTipo = new TextBox;
$obTxtTipo->setName               ( "inCodigoTipo"        );
$obTxtTipo->setRotulo             ( "Tipo de Desoneração" );
$obTxtTipo->setTitle              ( "Tipo da desoneração a ser criada." );
$obTxtTipo->setMaxLength          ( 7                   );
$obTxtTipo->setSize               ( 7                   );
$obTxtTipo->setValue              ( $_REQUEST["inCodigoTipo"]  );
$obTxtTipo->setInteiro            ( true                );
$obTxtTipo->setNull               ( false               );

$obCmbTipo = new Select;
$obCmbTipo->setName               ( "stTipo"               );
$obCmbTipo->setRotulo             ( "Tipo de Desoneração"  );
$obCmbTipo->setNull               ( false                  );
$obCmbTipo->setCampoId            ( "cod_tipo_desoneracao" );
$obCmbTipo->setCampoDesc          ( "descricao"            );
$obCmbTipo->addOption             ( "", "Selecione"        );
$obCmbTipo->preencheCombo         ( $rsTipo                );
$obCmbTipo->setValue              ( $_REQUEST["inCodigoTipo"] );

$obBscCredito = new BuscaInner;
$obBscCredito->setTitle  ( "Crédito para o qual a desoneração será definida." );
$obBscCredito->setNull             ( false             );
$obBscCredito->setRotulo           ( "Crédito"         );
$obBscCredito->setId               ( "stCredito"       );
$obBscCredito->obCampoCod->setName ( "inCodigoCredito" );
$obBscCredito->obCampoCod->setValue( $_REQUEST["inCodigoCredito"]  );
$obBscCredito->obCampoCod->setMinLength ( strlen($stMascaraCredito) );
$obBscCredito->obCampoCod->setMascara   ($stMascaraCredito          );
$obBscCredito->obCampoCod->setNull ( false             );
$obBscCredito->obCampoCod->setSize ( strlen($stMascaraCredito) );
$obBscCredito->obCampoCod->obEvento->setOnChange("buscaValor('buscaCredito');");
$obBscCredito->setFuncaoBusca      ( "abrePopUp('".CAM_GT_MON_POPUPS."credito/FLProcurarCredito.php','frm','inCodigoCredito','stCredito','todos','".Sessao::getId()."','800','550');");

$obDtInicio = new Data;
$obDtInicio->setTitle     ( "Data de início para solicitação de desoneração." );
$obDtInicio->setName      ( "dtInicio" );
$obDtInicio->setValue     ( $_REQUEST["dtInicio"]  );
$obDtInicio->setRotulo    ( "Início"   );
$obDtInicio->setMaxLength ( 20         );
$obDtInicio->setSize      ( 10         );
$obDtInicio->setNull      ( false      );

$obDtTermino = new Data;
$obDtTermino->setTitle     ( "Data de término para solicitação de desoneração." );
$obDtTermino->setName      ( "dtTermino" );
$obDtTermino->setValue     ( $_REQUEST["dtTermino"]  );
$obDtTermino->setRotulo    ( "Término"   );
$obDtTermino->setMaxLength ( 20          );
$obDtTermino->setSize      ( 10          );
$obDtTermino->setNull      ( false       );

$obDtExpiracao = new Data;
$obDtExpiracao->setName      ( "dtExpiracao"       );
$obDtExpiracao->setTitle     ( "Data em que a desoneração perderá sua validade." );
$obDtExpiracao->setValue     ( $_REQUEST["dtExpiracao"]        );
$obDtExpiracao->setRotulo    ( "Data de Expiração" );
$obDtExpiracao->setMaxLength ( 20                  );
$obDtExpiracao->setSize      ( 10                  );
$obDtExpiracao->setNull      ( false               );

if ($_REQUEST['inCodigoFormula'] && $_REQUEST['inCodigoModulo'] && $_REQUEST['inCodigoBiblioteca']) {
    $inCodigoFormula = sprintf( "%02d.%02d.%03d", $_REQUEST['inCodigoModulo'], $_REQUEST['inCodigoBiblioteca'], $_REQUEST['inCodigoFormula'] );

   SistemaLegado::executaFramePrincipal("buscaValor('buscaDados');");
}

$obBscFundamentacao = new BuscaInner;
$obBscFundamentacao->setTitle            ( "Norma que regulamenta a desoneração." );
$obBscFundamentacao->setNull             ( false                   );
$obBscFundamentacao->setRotulo           ( "Fundamentação Legal"   );
$obBscFundamentacao->setId               ( "stFundamentacao"       );
$obBscFundamentacao->obCampoCod->setName ( "inCodigoFundamentacao" );
$obBscFundamentacao->obCampoCod->setValue( $_REQUEST["inCodigoFundamentacao"]  );
$obBscFundamentacao->obCampoCod->setSize ( 9                       );
$obBscFundamentacao->obCampoCod->obEvento->setOnChange( "buscaValor('buscaLegal');" );
$obBscFundamentacao->setFuncaoBusca ( "abrePopUp('".CAM_GA_ADM_POPUPS."../../normas/popups/normas/FLNorma.php','frm','inCodigoFundamentacao','stFundamentacao','todos','".Sessao::getId()."','800','550');" );

$stMascaraCalculo = "99.99.999";

$obBscCalculo = new BuscaInner;
$obBscCalculo->setNull             ( false             );
$obBscCalculo->setTitle            ( "Função para identificar os contribuintes desonerados." );
$obBscCalculo->setRotulo           ( "Fórmula de Cálculo" );
$obBscCalculo->setId               ( "stFormula"  );
$obBscCalculo->obCampoCod->setName ( "inCodigoFormula" );
$obBscCalculo->obCampoCod->setValue( $inCodigoFormula  );
$obBscCalculo->obCampoCod->setInteiro ( true );
$obBscCalculo->obCampoCod->setNull ( false             );
$obBscCalculo->obCampoCod->setSize (  9                );
$obBscCalculo->obCampoCod->obEvento->setOnChange( "buscaValor('buscaFuncaoDefinirDesoneracao');" );
$obBscCalculo->setFuncaoBusca      (  "abrePopUp('".CAM_GA_ADM_POPUPS."funcao/FLBuscarFuncao.php?".Sessao::getId()."&stCodModulo=25&stCodBiblioteca=3&','frm','inCodigoFormula','stFormula','','".Sessao::getId()."','800','550');" );
$obBscCalculo->obCampoCod->obEvento->setOnKeyUp("mascaraDinamico('".$stMascaraCalculo."', this, event);");
$obBscCalculo->obCampoCod->setMinLength ( strlen($stMascaraCalculo) );

$obRdoRevogavelSim = new Radio;
$obRdoRevogavelSim->setTitle   ( "Define se a desoneração será revogável ou não." );
$obRdoRevogavelSim->setName    ( "boRevogavel"   );
$obRdoRevogavelSim->setRotulo  ( "Revogável"     );
$obRdoRevogavelSim->setLabel   ( "Sim"           );
$obRdoRevogavelSim->setValue   ( true            );
if ($_REQUEST['boRevogavel'] == "t" && $_REQUEST['stAcao'] == 'alterar') {
    $obRdoRevogavelSim->setChecked( true         );
}

$obRdoRevogavelNao = new Radio;
$obRdoRevogavelNao->setName    ( "boRevogavel" );
$obRdoRevogavelNao->setRotulo  ( "Revogável"   );
$obRdoRevogavelNao->setLabel   ( "Não"         );
$obRdoRevogavelNao->setValue   ( false         );
if ($_REQUEST['boRevogavel'] == "f" || !$_REQUEST['boRevogavel']) {
    $obRdoRevogavelNao->setChecked ( true          );
}

$obRdoProrrogavelSim = new Radio;
$obRdoProrrogavelSim->setTitle   ( "Define se a desoneração será prorrogável ou não." );
$obRdoProrrogavelSim->setName    ( "boProrrogavel"   );
$obRdoProrrogavelSim->setRotulo  ( "Prorrogável"     );
$obRdoProrrogavelSim->setLabel   ( "Sim"             );
$obRdoProrrogavelSim->setValue   ( true              );
if ($_REQUEST['boProrrogavel'] == "t" && $_REQUEST['stAcao'] == 'alterar') {
    $obRdoProrrogavelSim->setChecked( true           );
}

$obRdoProrrogavelNao = new Radio;
$obRdoProrrogavelNao->setName    ( "boProrrogavel" );
$obRdoProrrogavelNao->setRotulo  ( "Prorrogável"   );
$obRdoProrrogavelNao->setLabel   ( "Não"           );
$obRdoProrrogavelNao->setValue   ( false           );
if ($_REQUEST['boProrrogavel'] == "f"  || !$_REQUEST['boProrrogavel']) {
    $obRdoProrrogavelNao->setChecked ( true            );
}

$rsAtributosSelecionados = $rsAtributosDisponiveis = new RecordSet;
if ($_REQUEST['stAcao'] == "incluir") {
//    $obRARRDesoneracao->obRCadastroDinamico->setPersistenteAtributos( new TARRAtributoArrecadacao );
//    $obRARRDesoneracao->obRCadastroDinamico->recuperaAtributosSelecionados( $rsAtributosDisponiveis );
//    $obRARRDesoneracao->obRCadastroDinamico->recuperaAtributosDisponiveis  ( $rsAtributosDisponiveis  );
/* alteração para listar todos os atributos */
      $obRARRDesoneracao->buscarAtributosDisponiveis ( $rsAtributosDisponiveis ) ;
} else {
    $obRARRDesoneracao->obRCadastroDinamico->setChavePersistenteValores( array( "cod_desoneracao" => $_REQUEST["inCodigoDesoneracao"] ) );
    $obRARRDesoneracao->obRCadastroDinamico->recuperaAtributosDisponiveis  ( $rsAtributosDisponiveis  );
    $obRARRDesoneracao->obRCadastroDinamico->recuperaAtributosSelecionados ( $rsAtributosSelecionados );
}

//definicao dos combos de atributos
$obCmbAtributos = new SelectMultiplo();
$obCmbAtributos->setTitle  ( "Atributos que serão solicitados ao conceder a desoneração." );
$obCmbAtributos->setName   ( "inCodAtributoSelecionados" );
$obCmbAtributos->setRotulo ( "Atributos" );
$obCmbAtributos->setNull   ( true );

// lista de atributos disponiveis
$obCmbAtributos->SetNomeLista1 ( "inCodAtributoDisponiveis" );
$obCmbAtributos->setCampoId1   ( "cod_atributo" );
$obCmbAtributos->setCampoDesc1 ( "nom_atributo" );
$obCmbAtributos->SetRecord1    ( $rsAtributosDisponiveis );

// lista de atributos selecionados
$obCmbAtributos->SetNomeLista2 ( "inCodAtributoSelecionados" );
$obCmbAtributos->setCampoId2   ( "cod_atributo" );
$obCmbAtributos->setCampoDesc2 ( "nom_atributo" );
$obCmbAtributos->SetRecord2    ( $rsAtributosSelecionados );

//DEFINICAO DO FORMULARIO
$obForm = new Form;
$obForm->setAction            ( $pgProc );
$obForm->setTarget            ( "oculto" );

$obFormulario = new Formulario;
$obFormulario->addForm        ( $obForm      );
$obFormulario->addHidden      ( $obHdnAcao   );
$obFormulario->addHidden      ( $obHdnCtrl   );
$obFormulario->addHidden      ( $obHdnCodigoDesoneracao );

$obFormulario->addTitulo      ( "Dados para Desoneração" );
if ($_REQUEST['stAcao'] == 'alterar') {
    $obFormulario->addHidden( $obHdnCodigoCredito );
    $obFormulario->addHidden( $obHdnCodigoTipo    );
    $obFormulario->addComponente( $obLblCodigoDesoneracao );
    $obFormulario->addComponente( $obLblTipo );
    $obFormulario->addComponente( $obLblCredito );
} else {
    $obFormulario->addComponenteComposto($obTxtTipo, $obCmbTipo );
    $obFormulario->addComponente( $obBscCredito   );
}
$obFormulario->addComponente( $obDtInicio     );
$obFormulario->addComponente( $obDtTermino    );
$obFormulario->addComponente( $obDtExpiracao  );
$obFormulario->addComponente( $obBscFundamentacao );
$obFormulario->addComponente( $obBscCalculo   );
$obFormulario->agrupaComponentes( array ( $obRdoRevogavelSim, $obRdoRevogavelNao ) );
$obFormulario->agrupaComponentes( array ( $obRdoProrrogavelSim, $obRdoProrrogavelNao ) );
$obFormulario->addComponente( $obCmbAtributos );
$obFormulario->Ok();
$obFormulario->Show();
