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

//$obRegra->obRCadastroDinamico->obRModulo->setCodModulo(15);
//$obRegra->obRCadastroDinamico->verificaModulo();

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];
if ( empty( $stAcao ) || $stAcao=="incluir") {
    $stAcao = "incluir";
    //print_r( $obRegra );
    $obRegra->obRCadastroDinamico->setPersistenteAtributos ( new TAdministracaoAtributoDinamico );
    $obRegra->obRCadastroDinamico->recuperaAtributos( $rsAtributosDisponiveis );

//} elseif ($stAcao == "alterar") {
} elseif ($stAcao) {
    $obRegra->setCodTipoNorma( $_GET['inCodTipoNorma'] );
    $obRegra->obRCadastroDinamico->setChavePersistenteValores( array("cod_tipo_norma"=>$obRegra->getCodTipoNorma() ) );
    $obRegra->obRCadastroDinamico->recuperaAtributosDisponiveis ( $rsAtributosDisponiveis  );
    $obRegra->obRCadastroDinamico->recuperaAtributosSelecionados( $rsAtributosSelecionados );

    $obRegra->consultar( $rsTipoNorma );
    $stNomeTipoNorma = $obRegra->getNomeTipoNorma();

}

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnCodTipoNorma = new Hidden;
$obHdnCodTipoNorma->setName( "inCodTipoNorma" );
$obHdnCodTipoNorma->setValue( $_GET['inCodTipoNorma'] );

$obTxtNome = new TextBox;
$obTxtNome->setRotulo        ( "Nome" );
$obTxtNome->setName          ( "stNomeTipoNorma" );
$obTxtNome->setValue         ( $stNomeTipoNorma );
$obTxtNome->setSize          ( 40 );
$obTxtNome->setMaxLength     ( 40 );
$obTxtNome->setNull          ( false );
$obTxtNome->setTitle  		 ( "Informe o nome do tipo da norma" );

$obCmbAtributos = new SelectMultiplo();
$obCmbAtributos->setName   ('inCodAtributos');
$obCmbAtributos->setRotulo ( "Atributos" );
$obCmbAtributos->setNull   ( true );
$obCmbAtributos->setTitle  ( "Atributos" );

// lista de atributos disponiveis
$obCmbAtributos->SetNomeLista1 ('inCodAtributosDisponiveis');
$obCmbAtributos->setCampoId1   ('cod_atributo');
$obCmbAtributos->setCampoDesc1 ('nom_atributo');
$obCmbAtributos->SetRecord1    ( $rsAtributosDisponiveis );

// lista de atributos selecionados
$obCmbAtributos->SetNomeLista2 ('inCodAtributosSelecionados');
$obCmbAtributos->setCampoId2   ('cod_atributo');
$obCmbAtributos->setCampoDesc2 ('nom_atributo');
$obCmbAtributos->SetRecord2    ( $rsAtributosSelecionados );

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction                  ( $pgProc );
$obForm->setTarget                  ( "oculto" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm              ( $obForm );
$obFormulario->setAjuda             ( "UC-01.04.01" );
$obFormulario->addHidden            ( $obHdnAcao );
$obFormulario->addHidden            ( $obHdnCtrl );
$obFormulario->addHidden            ( $obHdnCodTipoNorma );

$obFormulario->addTitulo            ( "Dados para tipo de norma" );

$obFormulario->addComponente        ( $obTxtNome );
$obFormulario->addComponente        ( $obCmbAtributos );

$obFormulario->OK                   ();
$obFormulario->show                 ();

include_once($pgJs);

if ($stAcao == "incluir") {
    $js .= "focusIncluir();";
    sistemaLegado::executaFrameOculto($js);
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
