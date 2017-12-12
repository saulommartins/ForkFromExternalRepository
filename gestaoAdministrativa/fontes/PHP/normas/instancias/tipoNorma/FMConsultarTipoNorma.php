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
* Arquivo de instância para tipo de normas
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 15580 $
$Name$
$Author: cassiano $
$Date: 2006-09-18 08:26:31 -0300 (Seg, 18 Set 2006) $

Casos de uso: uc-01.04.01
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GA_NORMAS_NEGOCIO."RTipoNorma.class.php");

$stPrograma = "ManterTipoNorma";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$rsAtributosDisponiveis = $rsAtributosSelecionados = new RecordSet;
$obRegra = new RTipoNorma;

$obRegra->obRCadastroDinamico->obRModulo->setCodModulo(15);
$obRegra->obRCadastroDinamico->verificaModulo();

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

/*
if ( empty( $stAcao ) || $stAcao=="incluir") {
    $stAcao = "incluir";
    //print_r( $obRegra );
    $obRegra->obRCadastroDinamico->recuperaAtributos( $rsAtributosDisponiveis );

//} elseif ($stAcao == "alterar") {
} elseif ($stAcao) {
*/
    $obRegra->setCodTipoNorma( $_GET['inCodTipoNorma'] );
    $obRegra->obRCadastroDinamico->setChavePersistenteValores( array("cod_tipo_norma"=>$obRegra->getCodTipoNorma() ) );
    $obRegra->obRCadastroDinamico->recuperaAtributosDisponiveis ( $rsAtributosDisponiveis  );
    $obRegra->obRCadastroDinamico->recuperaAtributosSelecionados( $rsAtributosSelecionados );

    $obRegra->consultar( $rsTipoNorma );
    $stNomeTipoNorma = $obRegra->getNomeTipoNorma();
/*
    //$obRegra->listar( $rsTeste );
}
*/
$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnCodTipoNorma = new Hidden;
$obHdnCodTipoNorma->setName( "inCodTipoNorma" );
$obHdnCodTipoNorma->setValue( $_GET['inCodTipoNorma'] );

$obLblNome = new Label;
$obLblNome->setRotulo        ( "Nome" );
$obLblNome->setName          ( "stNomeTipoNorma" );
$obLblNome->setValue         ( $stNomeTipoNorma );

$obCmbAtributos = new Select;
$obCmbAtributos->setRotulo        ( "Atributos Selecionados" );
$obCmbAtributos->setName          ( "inCodAtributosSelecionados" );
$obCmbAtributos->setStyle         ( "width: 250px");
$obCmbAtributos->setCampoID       ( "cod_atributo" );
$obCmbAtributos->setCampoDesc     ( "nom_atributo" );
$obCmbAtributos->setValue         ( $inCodAtributosSelecionados );
$obCmbAtributos->setNull          ( true );
$obCmbAtributos->setMultiple      ( true );
$obCmbAtributos->setSize          ( 10 );
$obCmbAtributos->preencheCombo    ( $rsAtributosSelecionados );

$obBtnVoltar = new Button;
$obBtnVoltar->setName( "btnVoltar" );
$obBtnVoltar->setValue( "Voltar" );
$obBtnVoltar->setTipo( "submit" );
$obBtnVoltar->obEvento->setOnClick ( "history.back(-1);" );

//DEFINICAO DO FORMULARIO
$obForm = new Form;
$obForm->setAction                  ( $pgProc );
$obFormulario->setAjuda             ( "UC-01.04.01" );
$obForm->setTarget                  ( "oculto" );

$obFormulario = new Formulario;
$obFormulario->addForm              ( $obForm );
$obFormulario->addHidden            ( $obHdnAcao );
$obFormulario->addHidden            ( $obHdnCtrl );
$obFormulario->addHidden            ( $obHdnCodTipoNorma );

$obFormulario->addTitulo            ( "Dados para tipo de norma" );

$obFormulario->addComponente        ( $obLblNome );
$obFormulario->addComponente        ( $obCmbAtributos );

$obFormulario->defineBarra( array( $obBtnVoltar ), "", "" );

//$obFormulario->OK                   ();
$obFormulario->show                 ();

//include_once($pgJs);
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
