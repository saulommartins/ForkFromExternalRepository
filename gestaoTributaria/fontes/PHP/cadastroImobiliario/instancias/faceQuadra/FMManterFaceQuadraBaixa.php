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
    * Página de Formulário para baixa de face de quadra
    * Data de Criação   : 10/11/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @ignore

    * $Id: FMManterFaceQuadraBaixa.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.07
*/

/*
$Log$
Revision 1.8  2006/09/18 10:30:35  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "BaixarFaceQuadra";
$pgProc = "PRManterFaceQuadra.php";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

//Define o nome dos arquivos PHP
$stPrograma = "ManterFaceQuadra";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include( $pgJs );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao );

$obHdnCodigoFace = new Hidden;
$obHdnCodigoFace->setName  ( "inCodigoFace"             );
$obHdnCodigoFace->setValue ( $_REQUEST[ "inCodigoFace" ] );

$obHdnCodigoLocalizacao = new Hidden;
$obHdnCodigoLocalizacao->setName  ( "inCodigoLocalizacao"             );
$obHdnCodigoLocalizacao->setValue ( $_REQUEST[ "inCodigoLocalizacao" ] );

$obHdnValorComposto = new Hidden;
$obHdnValorComposto->setName  ( "inValorComposto"             );
$obHdnValorComposto->setValue ( $_REQUEST[ "inValorComposto" ] );

// Definição dos objetos para o formuário
$stValorComposto = $_REQUEST["inValorComposto"];
$obLblValorComposto = new Label;
$obLblValorComposto->setRotulo ( "Localização" );
$obLblValorComposto->setTitle  ( "Localização onde a face de quadra está localizada" );
$obLblValorComposto->setValue  ( $stValorComposto );

$stCodigoFace = $_REQUEST["inCodigoFace"];
$obLblCodigoFace = new Label;
$obLblCodigoFace->setRotulo ( "Código Face de Quadra" );
$obLblCodigoFace->setValue  ( $stCodigoFace );

$obTxtJustificativa = new TextArea;
if ($stAcao == "reativar") {
    $obTxtJustificativa->setRotulo        ( "Motivo da Reativação" );
    $obTxtJustificativa->setTitle         ( "Motivo da reativação" );
    $obTxtJustificativa->setName          ( "stJustReat" );
    $obTxtJustificativa->setId            ( "stJustReat" );
} else {
    $obTxtJustificativa->setRotulo        ( "Motivo da Baixa" );
    $obTxtJustificativa->setTitle         ( "Motivo da baixa" );
    $obTxtJustificativa->setName          ( "stJustificativa" );
    $obTxtJustificativa->setId            ( "stJustificativa" );
}
$obTxtJustificativa->setNull          ( false             );

$obLblJustificativa = new Label;
$obLblJustificativa->setRotulo ( "Motivo da Baixa" );
$obLblJustificativa->setTitle  ( "Motivo da baixa" );
$obLblJustificativa->setValue  ( $_REQUEST["stJustificativa"] );

$obHdnJustificativa = new Hidden;
$obHdnJustificativa->setName  ( "stJustificativa" );
$obHdnJustificativa->setValue ( $_REQUEST["stJustificativa"] );

$obLblDtInicio = new Label;
$obLblDtInicio->setRotulo ( "Data da Baixa" );
$obLblDtInicio->setValue  ( $_REQUEST["stDtInicio"] );

$obHdnTimestamp = new Hidden;
$obHdnTimestamp->setName  ( "stTimestamp" );
$obHdnTimestamp->setValue ( $_REQUEST[ "stTimestamp" ] );

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction            ( $pgProc );
$obForm->setTarget            ( "oculto" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm        ( $obForm                     );
$obFormulario->setAjuda       ( "UC-05.01.07"                  );
$obFormulario->addHidden      ( $obHdnCodigoFace            );
$obFormulario->addHidden      ( $obHdnValorComposto         );
$obFormulario->addHidden      ( $obHdnCodigoLocalizacao     );
$obFormulario->addHidden      ( $obHdnAcao                  );
$obFormulario->addTitulo      ( "Dados para face de quadra" );
$obFormulario->addComponente  ( $obLblValorComposto         );
$obFormulario->addComponente  ( $obLblCodigoFace            );

if ($stAcao == "reativar") {
    $obFormulario->addHidden      ( $obHdnJustificativa );
    $obFormulario->addHidden      ( $obHdnTimestamp );
    $obFormulario->addComponente  ( $obLblDtInicio );
    $obFormulario->addComponente  ( $obLblJustificativa );
}

$obFormulario->addComponente  ( $obTxtJustificativa );

$obFormulario->Cancelar ();
$obFormulario->setFormFocus   ( $obTxtJustificativa->getId());
$obFormulario->show  ();
?>
