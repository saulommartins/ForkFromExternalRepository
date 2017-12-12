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
* Arquivo de instância para popup de normas
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 22985 $
$Name$
$Author: andre.almeida $
$Date: 2007-05-30 18:21:17 -0300 (Qua, 30 Mai 2007) $

Casos de uso: uc-01.04.02
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GA_NORMAS_NEGOCIO."RNorma.class.php");

$stPrograma = "Norma";
$pgFilt = "FL".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include_once( $pgJs );

$obRegra = new RNorma;
$obRegra->obRTipoNorma->listar( $rsTipoNorma );

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');

Sessao::remove('linkPopUp');

$obIFrame = new IFrame;
$obIFrame->setName("oculto");
$obIFrame->setWidth("100%");
$obIFrame->setHeight("50");

$obIFrame2 = new IFrame;
$obIFrame2->setName   ( "telaMensagem");
$obIFrame2->setWidth  ( "100%"        );
$obIFrame2->setHeight ( "50"          );

$obForm = new Form;
$obForm->setAction( $pgList );

//Utilizado através do componente IBuscaInnerNorma***

$obHdnRetonaNumExercicio = new Hidden;
$obHdnRetonaNumExercicio->setName( "boRetornaNumExercicio" );
$obHdnRetonaNumExercicio->setValue( $request->get('boRetornaNumExercicio') );

$obHdnComponente = new Hidden;
$obHdnComponente->setName( "boComponente" );
if (isset($boComponente)) {
    $obHdnComponente->setValue( $boComponente );
}

$inCodTipoNorma = $request->get('inCodTipoNormaTxt');
//***************************************************

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
if (isset($stAcao)) {
$obHdnAcao->setValue( $stAcao );
}

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnForm = new Hidden;
$obHdnForm->setName( "nomForm" );
$obHdnForm->setValue( $_REQUEST["nomForm"] );

$obHdnCampoNum = new Hidden;
$obHdnCampoNum->setName( "campoNum" );
$obHdnCampoNum->setValue( $request->get('campoNum') );

$obHdnCampoNom = new Hidden;
$obHdnCampoNom->setName( "campoNom" );
$obHdnCampoNom->setValue( $request->get('campoNom') );

$obHdnTipoBusca = new Hidden;
$obHdnTipoBusca->setName( "tipoBusca" );
$obHdnTipoBusca->setValue( $request->get('tipoBusca') );

$obHdnBoExibeDataNorma = new Hidden;
$obHdnBoExibeDataNorma->setName( "boExibeDataNorma" );
$obHdnBoExibeDataNorma->setValue( $request->get("boExibeDataNorma") );

$obHdnBoExibeDataPublicacao = new Hidden;
$obHdnBoExibeDataPublicacao->setName( "boExibeDataPublicacao" );
$obHdnBoExibeDataPublicacao->setValue( $request->get("boExibeDataPublicacao") );

// Define Objeto TextBox para o Nome da norma
$obTxtNomNorma = new TextBox;
$obTxtNomNorma->setName   ( "stNomeNorma" );
$obTxtNomNorma->setId     ( "stNomeNorma" );
if (isset($stNomeNorma)) {
    $obTxtNomNorma->setValue  ( $stNomeNorma  );
}
$obTxtNomNorma->setRotulo ( "Nome da Norma"      );
$obTxtNomNorma->setTitle  ( "Informe o nome da Norma" );
$obTxtNomNorma->setSize  ( 80  );

// Define Objeto TextBox para o Nome da norma
$obTxtDescricaoNorma = new TextBox;
$obTxtDescricaoNorma->setName   ( "stDescricaoNorma" );
$obTxtDescricaoNorma->setId     ( "stDescricaoNorma" );
if (isset($stDescricaoNorma)) {
$obTxtDescricaoNorma->setValue  ( $stDescricaoNorma  );
}
$obTxtDescricaoNorma->setRotulo ( "Descrição da Norma"      );
$obTxtDescricaoNorma->setTitle  ( "Informe a descrição da Norma" );
$obTxtDescricaoNorma->setSize   ( 80  );

// Define Objeto TextBox para Codigo do tipo da norma
$obTxtCodTipoNorma = new TextBox;
$obTxtCodTipoNorma->setName   ( "inCodTipoNorma" );
$obTxtCodTipoNorma->setId     ( "inCodTipoNorma" );
if (isset($inCodTipoNorma)) {
    $obTxtCodTipoNorma->setValue  ( $inCodTipoNorma  );
}
$obTxtCodTipoNorma->setRotulo ( "Tipo"      );
$obTxtCodTipoNorma->setTitle  ( "Informe o tipo da Norma" );
$obTxtCodTipoNorma->setInteiro( true  );
$obTxtCodTipoNorma->setNull   ( true  );

// Define Objeto Select para Nome do Tipo da norma
$obCmbNomTipoNorma = new Select;
$obCmbNomTipoNorma->setName      ( "stNomTipoNorma"  );
$obCmbNomTipoNorma->setId        ( "stNomTipoNorma"  );
if (isset($inCodTipoNorma)) {
$obCmbNomTipoNorma->setValue     ( $inCodTipoNorma  );
}
$obCmbNomTipoNorma->addOption    ( "", "Selecione" );
$obCmbNomTipoNorma->setRotulo ( "Tipo"      );
$obCmbNomTipoNorma->obEvento->setOnChange( "limparCampos();" );
$obCmbNomTipoNorma->setCampoId   ( "cod_tipo_norma" );
$obCmbNomTipoNorma->setCampoDesc ( "nom_tipo_norma" );
$obCmbNomTipoNorma->setStyle  ( "width: 520" );
$obCmbNomTipoNorma->preencheCombo( $rsTipoNorma  );
$obCmbNomTipoNorma->setNull   ( false );

//Utilizado através do componente IBuscaInnerNorma***
if (isset($boComponente)) {
    $obTxtCodTipoNorma->setReadOnly(true);
    $obCmbNomTipoNorma->setDisabled(true);
}
//***************************************************

// Define objeto TextBox Para exercicio
$obTxtExercicio = new TextBox;
$obTxtExercicio->setName      ( "stExercicio" );
$obTxtExercicio->setValue     ( Sessao::getExercicio() );
$obTxtExercicio->setRotulo    ( "Exercício" );
$obTxtExercicio->setTitle     ( "Informe o exercício para filtro" );
$obTxtExercicio->setSize      ( 4  );
$obTxtExercicio->setMaxLength ( 4  );
$obTxtExercicio->setNull      ( true );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addTitulo             ( "Dados para Filtro" );
$obFormulario->addForm               ( $obForm );
$obFormulario->addHidden             ( $obHdnAcao );

$obFormulario->addHidden             ( $obHdnRetonaNumExercicio );
$obFormulario->addHidden             ( $obHdnCtrl );
$obFormulario->addHidden             ( $obHdnForm );
$obFormulario->addHidden             ( $obHdnCampoNum );
$obFormulario->addHidden             ( $obHdnCampoNom );
$obFormulario->addHidden             ( $obHdnTipoBusca );
$obFormulario->addHidden             ( $obHdnBoExibeDataNorma );
$obFormulario->addHidden             ( $obHdnBoExibeDataPublicacao );
$obFormulario->addComponenteComposto ( $obTxtCodTipoNorma, $obCmbNomTipoNorma );
//Utilizado através do componente IBuscaInnerNorma***************
if (isset($boComponente)) {
   $obFormulario->addHidden             ( $obHdnComponente );
}
   $obFormulario->addComponente         ( $obTxtNomNorma );
   $obFormulario->addComponente         ( $obTxtDescricaoNorma );
//***************************************************************

$obFormulario->addComponente         ( $obTxtExercicio );
$obFormulario->OK                    ();
$obFormulario->show                  ();
$obIFrame2->show();
$obIFrame->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
