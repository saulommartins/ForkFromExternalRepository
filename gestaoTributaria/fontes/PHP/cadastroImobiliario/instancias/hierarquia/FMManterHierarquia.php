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
    * Página de Formulário para o cadastro de hierarquias
    * Data de Criação   : 25/06/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

    * @ignore

    * $Id: FMManterHierarquia.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.02
*/

/*
$Log$
Revision 1.7  2007/02/05 12:02:22  cercato
Bug #7336#

Revision 1.6  2006/09/18 10:30:39  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMNivel.class.php"       );

//Define o nome dos arquivos PHP
$stPrograma = "ManterHierarquia";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OCManterVigencia.php";
$pgJS   = "JS".$stPrograma.".js";

include_once ($pgJS);

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

$obRCIMNivel = new RCIMNivel;

$rsAtributosDisponiveis  = new RecordSet;
$rsAtributosSelecionados = new RecordSet;
$rsListaClassificacao    = new RecordSet;
$rsListaVigencias        = new RecordSet;

$obRCadastroDinamico    = new RCadastroDinamico;
$obRCadastroDinamico->setCodCadastro    ( 1 ) ;
if ($stAcao == "incluir") {
    $obRCadastroDinamico->recuperaAtributosSelecionados( $rsAtributosDisponiveis );
    $obRCIMNivel->recuperaVigenciaAtual( $rsVigenciaAtual );
    $obRCIMNivel->setCodigoVigencia( $rsVigenciaAtual->getCampo( "cod_vigencia" ) );
    $obRCIMNivel->recuperaUltimoNivel    ( $rsUltimoNivel );
    $stNivelSuperior = $rsUltimoNivel->getCampo( "nom_nivel" );
    $obRCIMNivel->setCodigoVigencia("");
} else {
    $obRCadastroDinamico->setPersistenteAtributos( new TCIMAtributoNivel );
    $inCodigoVigencia = $_REQUEST["inCodigoVigencia"];
    $inCodigoNivel    = $_REQUEST["inCodigoNivel"];
    $obRCIMNivel->setCodigoVigencia ( $inCodigoVigencia );
    $obRCIMNivel->setCodigoNivel    ( $inCodigoNivel    );
    $obRCIMNivel->consultarNivel();
    $obRCIMNivel->listarVigencias($rsListaVigencias);
    $stDataVigencia = $rsListaVigencias->getCampo("dtinicio");
    $stNomeNivel = $obRCIMNivel->getNomeNivel();
    $stMascaraNivel = $obRCIMNivel->getMascara();
    $obRCIMNivel->recuperaUltimoNivel(  $rsUltimoNivel );
    $stNivelSuperior = $rsUltimoNivel->getCampo( "nom_nivel" );
    $obRCadastroDinamico->setChavePersistenteValores( array("cod_nivel"=> $inCodigoNivel,  "cod_vigencia" => $inCodigoVigencia ) );
    $obRCadastroDinamico->recuperaAtributosDisponiveis  ( $rsAtributosDisponiveis  );
    $obRCadastroDinamico->recuperaAtributosSelecionados ( $rsAtributosSelecionados );
}

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $_REQUEST['stAcao'] );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( $_REQUEST['stCtrl'] );

//    Alterado por Lucas Stephanou em  28/03/2005
 //   por ser decrapted, agora o usuario podera escolher a vigencia do nivel
$obHdnVigencia = new Hidden;
$obHdnVigencia->setName  ( "inCodigoVigencia" );
$obHdnVigencia->setValue ( $_REQUEST['inCodigoVigencia']  );

$obHdnNivel = new Hidden;
$obHdnNivel->setName  ( "inCodigoNivel" );
$obHdnNivel->setValue ( $_REQUEST['inCodigoNivel']  );

/* Combo de Vigencias */

$rsTodasVigencias = new Recordset;
$obRCIMNivel->listarVigencias($rsTodasVigencias);

$obCmbVigencias = new Select;
$obCmbVigencias->setName        ( 'inCodigoVigencia');
$obCmbVigencias->setId          ( 'inCodigoVigencia');
$obCmbVigencias->setId          ( 'inCodigoVigencia');
$obCmbVigencias->setNull        ( false             );
$obCmbVigencias->setRotulo      ( 'Vigência'        );
$obCmbVigencias->setCampoId     ( 'cod_vigencia'    );
$obCmbVigencias->setCampoDesc   ( 'dtinicio'        );
$obCmbVigencias->preencheCombo  ( $rsTodasVigencias );
$obCmbVigencias->obEvento->setOnChange("buscaValor('UltimoNivel');");

// Fim do Combo de Vigencias

$obTxtNome = new TextBox;
$obTxtNome->setName      ( "stNomeNivel" );
$obTxtNome->setId        ( "stNomeNivel" );
$obTxtNome->setSize      ( 81 );
$obTxtNome->setMaxLength ( 80 );
$obTxtNome->setNull      ( false );
$obTxtNome->setRotulo    ( "Nome" );
$obTxtNome->setValue     ( $stNomeNivel );

$obLblNivelSuperior = new Label;
$obLblNivelSuperior->setName    ( "stNivelSuperior" );
$obLblNivelSuperior->setId      ( "stNivelSuperior" );
$obLblNivelSuperior->setRotulo  ( "Nível Superior"  );
$obLblNivelSuperior->setValue   ( $stNivelSuperior  );

$obLblVigencia = new Label;
$obLblVigencia->setName         ( "stCodVigencia"   );
$obLblVigencia->setRotulo       ( "Vigência"        );
$obLblVigencia->setValue        ( $stDataVigencia   );

$obTxtMascara = new TextBox;
$obTxtMascara->setName      ( "stMascaraNivel" );
$obTxtMascara->setSize      ( 11 );
$obTxtMascara->setMaxLength ( 10 );
$obTxtMascara->setNull      ( false );
$obTxtMascara->setRotulo    ( "Máscara" );
$obTxtMascara->setAlfaNumerico ( true );
$obTxtMascara->setAcento       ( false );
$obTxtMascara->setValue     ( $stMascaraNivel );

//definicao dos combos de atributos
$obCmbAtributos = new SelectMultiplo();
$obCmbAtributos->setName   ( "inCodAtributoSelecionados" );
$obCmbAtributos->setRotulo ( "Atributos" );
$obCmbAtributos->setNull   ( true );
$obCmbAtributos->setTitle  ( "Atributos que serão solicitados na inclusão de Condomínio" );

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

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgProc  );
$obForm->setTarget( "oculto" );

//DEFINICAO DO FORMULARIO
$obFomulario = new Formulario;
$obFomulario->addForm       ( $obForm );
$obFomulario->setAjuda ( "UC-05.01.02" );
$obFomulario->addTitulo     ( "Dados para Nível" );
$obFomulario->addHidden     ( $obHdnAcao );
$obFomulario->addHidden     ( $obHdnCtrl );

if ($stAcao == "alterar") {
    $obFomulario->addHidden     ( $obHdnVigencia );
}
$obFomulario->addHidden     ( $obHdnNivel    );

if ($stAcao == "incluir") {
    $obFomulario->addComponente ( $obCmbVigencias);
} else {
    $obFomulario->addComponente ( $obLblVigencia );
}
$obFomulario->addComponente ( $obTxtNome );

//if ($stNivelSuperior) {
    $obFomulario->addComponente ( $obLblNivelSuperior );
//}
$obFomulario->addComponente ( $obTxtMascara );
$obFomulario->addComponente ( $obCmbAtributos );

if ($stAcao == "incluir") {
    $obFomulario->OK();
} else {
    $obFomulario->Cancelar();
}

if ($stAcao == "incluir") {
    $obFomulario->setFormFocus( $obCmbVigencias->getId() );
} elseif ($stAcao == "alterar") {
    $obFomulario->setFormFocus( $obTxtNome->getId() );
}
$obFomulario->show();

?>
