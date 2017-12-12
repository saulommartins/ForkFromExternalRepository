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
    * Página de Formulário para alteração de características de face de quadra
    * Data de Criação   : 10/11/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @ignore

    * $Id: FMManterFaceQuadraCaracteristica.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.07
*/

/*
$Log$
Revision 1.6  2006/09/18 10:30:35  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMFaceQuadra.class.php"  );

//Define o nome dos arquivos PHP
$stPrograma = "BaixarFaceQuadra";
$pgProc = "PRManterFaceQuadra.php";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];
if ( empty( $stAcao ) ) {
    $stAcao = "historico";
}

$stPrograma = "ManterFaceQuadra";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include( $pgJs );

$obRCIMFaceQuadra = new RCIMFaceQuadra;

//DEFINICAO DOS ATRIBUTOS
$arChaveAtributoFaceQuadra =  array( "cod_face"    => $_REQUEST["inCodigoFace"],
                                 "cod_localizacao" => $_REQUEST["inCodigoLocalizacao"] );
$obRCIMFaceQuadra->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoFaceQuadra );
$obRCIMFaceQuadra->obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributos );

$obMontaAtributos = new MontaAtributos;
$obMontaAtributos->setTitulo     ( "Atributos"  );
$obMontaAtributos->setName       ( "Atributo_"  );
$obMontaAtributos->setRecordSet  ( $rsAtributos );

//DEFINICAO DOS COMPONENTES DE FORMULARIO
$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao );

$obHdnCodigoFace = new Hidden;
$obHdnCodigoFace->setName  ( "inCodigoFace"             );
$obHdnCodigoFace->setValue ( $_REQUEST[ "inCodigoFace" ] );

$obHdnValorComposto = new Hidden;
$obHdnValorComposto->setName  ( "inValorComposto"             );
$obHdnValorComposto->setValue ( $_REQUEST[ "inValorComposto" ] );

$obHdnCodigoLocalizacao = new Hidden;
$obHdnCodigoLocalizacao->setName ( "inCodigoLocalizacao" );
$obHdnCodigoLocalizacao->setValue( $_REQUEST ["inCodigoLocalizacao"] );

// definicao dos objetos do formulario
$inCodigoFace = $_REQUEST["inCodigoFace"];
$obLblCodigoFace = new Label;
$obLblCodigoFace->setRotulo ( "Código Face de Quadra" );
$obLblCodigoFace->setValue  ( $inCodigoFace );

$inValorComposto = $_REQUEST["inValorComposto"];
$obLblValorComposto = new Label;
$obLblValorComposto->setRotulo ( "Localização" );
$obLblValorComposto->setTitle  ( "Localização onde a face de quadra está localizada" );
$obLblValorComposto->setValue  ( $inValorComposto );

$obForm = new Form;
$obForm->setAction            ( $pgProc );
$obForm->setTarget            ( "oculto" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm        ( $obForm             );
$obFormulario->setAjuda ( "UC-05.01.07" );
$obFormulario->addHidden      ( $obHdnValorComposto );
$obFormulario->addHidden      ( $obHdnCodigoFace     );
$obFormulario->addHidden      ( $obHdnAcao          );
$obFormulario->addHidden      ( $obHdnCodigoLocalizacao );
$obFormulario->addTitulo      ( "Dados para face de quadra" );
$obFormulario->addComponente  ( $obLblValorComposto );
$obFormulario->addComponente  ( $obLblCodigoFace     );
$obMontaAtributos->geraFormulario ( $obFormulario    );
$obFormulario->Cancelar ();
$obFormulario->show  ();

?>
