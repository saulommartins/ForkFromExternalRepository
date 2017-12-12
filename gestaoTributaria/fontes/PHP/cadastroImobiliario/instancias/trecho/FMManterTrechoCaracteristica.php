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
    * Página de alteração de características para o cadastro de trecho
    * Data de Criação   : 07/09/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Gustavo Passos Tourinho

    * @ignore

    * $Id: FMManterTrechoCaracteristica.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.06
*/

/*
$Log$
Revision 1.6  2006/09/18 10:31:51  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMTrecho.class.php"  );

//Define o nome dos arquivos PHP
$stPrograma = "BaixarTrecho";
$pgProc = "PRManterTrecho.php";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];
if ( empty( $stAcao ) ) {
    $stAcao = "historico";
}

$stPrograma = "ManterTrecho";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include( $pgJs );

$obRCIMTrecho = new RCIMTrecho;

//DEFINICAO DOS ATRIBUTOS
$arChaveAtributoTrecho =  array( "cod_trecho"      => $_REQUEST["inCodTrecho"],
                                 "cod_logradouro" => $_REQUEST["inCodLogradouro"] );
$obRCIMTrecho->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoTrecho );
$obRCIMTrecho->obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributos );

$obMontaAtributos = new MontaAtributos;
$obMontaAtributos->setTitulo     ( "Atributos"  );
$obMontaAtributos->setName       ( "Atributo_"  );
$obMontaAtributos->setRecordSet  ( $rsAtributos );

//DEFINICAO DOS COMPONENTES DE FORMULARIO
$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao );

$obHdnCodTrecho = new Hidden;
$obHdnCodTrecho->setName  ( "inCodTrecho"             );
$obHdnCodTrecho->setValue ( $_REQUEST[ "inCodTrecho" ] );

$obHdnCodLogradouro = new Hidden;
$obHdnCodLogradouro->setName  ( "inCodLogradouro"             );
$obHdnCodLogradouro->setValue ( $_REQUEST[ "inCodLogradouro" ] );

$obHdnNomLogradouro = new Hidden;
$obHdnNomLogradouro->setName ( "stNomeLogradouro" );
$obHdnNomLogradouro->setValue( $_REQUEST ["stNomeLogradouro"] );

// Definição dos objetos para o formuário
$stCodigoTrecho = $_REQUEST["inCodLogradouro"].".".$_REQUEST["inSequencia"];
$obLblCodTrecho = new Label;
$obLblCodTrecho->setRotulo ( "Código do Trecho" );
$obLblCodTrecho->setValue  ( $stCodigoTrecho );

$stNomeLogradouro = $_REQUEST["stNomeLogradouro"];
$obLblNomLogradouro = new Label;
$obLblNomLogradouro->setRotulo ( "Nome do Logradouro" );
$obLblNomLogradouro->setTitle  ( "Logradouro onde o trecho está localizado" );
$obLblNomLogradouro->setValue  ( $stNomeLogradouro );

$obLblExtensao = new Label;
$obLblExtensao->setRotulo ( "Extensão" );
$obLblExtensao->setTitle  ( "Extensão em metros do trecho" );
$obLblExtensao->setValue  ( $_REQUEST["flExtensao"] );

$obForm = new Form;
$obForm->setAction            ( $pgProc );
$obForm->setTarget            ( "oculto" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm        ( $obForm             );
$obFormulario->setAjuda ( "UC-05.01.06" );
$obFormulario->addHidden      ( $obHdnCodTrecho     );
$obFormulario->addHidden      ( $obHdnCodLogradouro );
$obFormulario->addHidden      ( $obHdnAcao          );
$obFormulario->addHidden      ( $obHdnNomLogradouro );
$obFormulario->addTitulo      ( "Dados para Trecho" );
$obFormulario->addComponente  ( $obLblCodTrecho     );
$obFormulario->addComponente  ( $obLblNomLogradouro );
$obFormulario->addComponente  ( $obLblExtensao      );
$obMontaAtributos->geraFormulario ( $obFormulario    );
$obFormulario->Cancelar ();
$obFormulario->show  ();
?>
