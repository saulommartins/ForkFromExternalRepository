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

    * $Id: FLManterElemento.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.02.05

*/

/*
$Log$
Revision 1.7  2006/09/15 14:32:46  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

//Define o nome dos arquivos PHP
$stPrograma  = "ManterElemento";
$pgFilt      = "FL".$stPrograma.".php";
$pgList      = "LS".$stPrograma.".php";
$pgForm      = "FM".$stPrograma.".php";
$pgFormBaixa = "FM".$stPrograma."Baix.php";
$pgProc      = "PR".$stPrograma.".php";
$pgOcul      = "OC".$stPrograma.".php";
$pgProx = $pgList;

Sessao::write( "link", "" );
$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

//DEFINICAO DOS COMPONENTES DO FORMULARIO
$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

// DEFINICAO DOS COMPONENTES DO FORMULARIO

$obTxtCodElemento = new TextBox;
$obTxtCodElemento->setRotulo       ( "Código"           );
$obTxtCodElemento->setName         ( "inCodigoElemento" );
$obTxtCodElemento->setValue        ( $inCodigoElemento  );
$obTxtCodElemento->setId           ( "codigoElemento" );
$obTxtCodElemento->setSize         ( 10 );
$obTxtCodElemento->setMaxLength    ( 10 );
$obTxtCodElemento->setInteiro      ( true  );
$obTxtCodElemento->setNull         ( true );

$obTxtNomElemento = new TextBox;
$obTxtNomElemento->setRotulo       ( "Nome"           );
$obTxtNomElemento->setName         ( "stNomeElemento" );
$obTxtNomElemento->setValue        ( $stNomeElemento  );
$obTxtNomElemento->setSize         ( 80 );
$obTxtNomElemento->setMaxLength    ( 80 );
$obTxtNomElemento->setNull         ( true );
$obTxtNomElemento->setTitle        ( "Nome do elemento para base de cálculo" );

//DEFINICAO DO FORMULARIO
$obForm = new Form;
$obForm->setAction            ( $pgList );
$obForm->setTarget            ( "" );

$obFormulario = new Formulario;
$obFormulario->setAjuda       ("UC-05.02.05"           );
$obFormulario->addForm        ( $obForm             );
$obFormulario->addHidden      ( $obHdnAcao          );
$obFormulario->addHidden      ( $obHdnCtrl          );
$obFormulario->addTitulo      ( "Dados para Filtro" );
$obFormulario->addComponente  ( $obTxtCodElemento   );
$obFormulario->addComponente  ( $obTxtNomElemento   );

$obFormulario->setFormFocus( $obTxtCodElemento->getid() );

$obFormulario->OK             ();
$obFormulario->show           ();

?>
