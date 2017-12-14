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
    * Formulario para Edificação
    * Data de Criação   : 14/04/2005
    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Marcelo B. Paulino
    * @author Desenvolvedor: Lizandro Kirst da Silva
    * @package URBEM
    * @subpackage Regra

    * $Id: FMManterElemento.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.02.05

*/

/*
$Log$
Revision 1.12  2006/09/15 14:32:46  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
//include_once("../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/HTML/Ajuda.class.php"   );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMElemento.class.php"  );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMAtividade.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterElemento";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";
include_once( $pgJs );

//Instancia objetos
$obRCEMElemento     = new RCEMElemento( new RCEMAtividade );
$rsTipoElemento     = new RecordSet;
$obMontaAtributos   = new MontaAtributos;

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

$rsAtributosDisponiveis = $rsAtributosSelecionados = new RecordSet;
if ($stAcao == "incluir") {
    //$obRCEMElemento->obRCadastroDinamico->setPersistenteAtributos ( new TCEMAtributoCadastroEconomico);
    $obErro = $obRCEMElemento->obRCadastroDinamico->recuperaAtributosSelecionados ( $rsAtributosDisponiveis );
} else {
    $inCodigoElemento = $_REQUEST["inCodigoElemento"];
    $obRCEMElemento->setCodigoElemento ( $inCodigoElemento );
    $obRCEMElemento->consultarElemento();
    $stNomeElemento = $obRCEMElemento->getNomeElemento();

    $obRCEMElemento->obRCadastroDinamico->setPersistenteAtributos( new TCEMAtributoElemento() );
    $obRCEMElemento->obRCadastroDinamico->setChavePersistenteValores    ( array( "cod_elemento" => $inCodigoElemento ) );
    $obRCEMElemento->obRCadastroDinamico->recuperaAtributosDisponiveis  ( $rsAtributosDisponiveis  );
    $obRCEMElemento->obRCadastroDinamico->recuperaAtributosSelecionados ( $rsAtributosSelecionados );

}

// OBJETOS HIDDEN
$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnCodigoElemento = new Hidden;
$obHdnCodigoElemento->setName( "inCodigoElemento" );
$obHdnCodigoElemento->setValue( $inCodigoElemento );

// DEFINICAO DOS COMPONENTES DO FORMULARIO
$obLblCodigoElemento = new Label;
$obLblCodigoElemento->setRotulo ( "Código" );
$obLblCodigoElemento->setValue  ( $inCodigoElemento  );

$obTxtNomElemento = new TextBox;
$obTxtNomElemento->setRotulo       ( "Nome"           );
$obTxtNomElemento->setName         ( "stNomeElemento" );
$obTxtNomElemento->setValue        ( $stNomeElemento  );
$obTxtNomElemento->setId           ( "nomeElemento" );
$obTxtNomElemento->setSize         ( 80 );
$obTxtNomElemento->setMaxLength    ( 80 );
$obTxtNomElemento->setNull         ( false );
$obTxtNomElemento->setTitle        ( "Nome do elemento para base de cálculo" );

//definicao dos combos de atributos
$obCmbAtributos = new SelectMultiplo();
$obCmbAtributos->setName   ( "inCodAtributoSelecionados" );
$obCmbAtributos->setRotulo ( "Atributos" );
$obCmbAtributos->setNull   ( false );
$obCmbAtributos->setTitle  ( "Atributos que serão solicitados na inclusão deste elemento" );

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

/*
$obAjuda = new Ajuda;
$obAjuda->setCodGestao($_REQUEST["cod_gestao_pass"]);
$obAjuda->setCodModulo($_REQUEST["modulo"]);
$obAjuda->setCasoUso("UC-5.2.9");*/

/*
$Log$
Revision 1.12  2006/09/15 14:32:46  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

//DEFINICAO DO FORMULARIO
$obForm = new Form;
$obForm->setAction            ( $pgProc );
$obForm->setTarget            ( "oculto" );

$obFormulario = new Formulario;
$obFormulario->addForm        ( $obForm      );

$obFormulario->setAjuda       ("UC-05.02.05"   );

$obFormulario->addHidden      ( $obHdnAcao   );
$obFormulario->addHidden      ( $obHdnCtrl   );
$obFormulario->addHidden      ( $obHdnCodigoElemento );

$obFormulario->addTitulo      ( "Dados para Elementos" );
if ($stAcao == "alterar") {
    $obFormulario->addComponente  ( $obLblCodigoElemento );
}
$obFormulario->addComponente  ( $obTxtNomElemento );
$obFormulario->addComponente  ( $obCmbAtributos   );

$obFormulario->setFormFocus( $obTxtNomElemento->getid() );

if ($stAcao == "incluir") {
    $obFormulario->OK();
} else {
    $obFormulario->Cancelar();
}

$obFormulario->show  ();

?>
