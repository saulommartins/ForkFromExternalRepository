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
    * Página de Filtro para Feriados, Exibe um Calendário
    * Data de Criação   : 11/08/2004

    * @author Analista: Leandro Oliveira
    * @author Desenvolvedor: Eduardo Martins

    * @ignore

    $Revision: 30859 $
    $Name$
    $Author: vandre $
    $Date: 2006-08-08 14:53:12 -0300 (Ter, 08 Ago 2006) $

    * Casos de uso :uc-04.02.01

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GRH_CAL_NEGOCIO."RCalendarioFeriado.class.php");
include_once(CAM_GRH_CAL_NEGOCIO."RCalendarioFeriadoVariavel.class.php");

$stPrograma = "ManterFeriado";
$pgFilt = "FL".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];

if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

$obRFeriado = new RFeriado;

Sessao::remove('filtroRelatorio');
Sessao::remove('link');

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

if ($stAnoCalendario < 2003) {
  $stAnoCalendario = date(Y);
}

$obTxtAno = new TextBox;
$obTxtAno->setName      ( "stAnoCalendario" );
$obTxtAno->setValue     ( $stAnoCalendario );
$obTxtAno->setRotulo    ( "Ano" );
$obTxtAno->setTitle     ( "Ano para exibição do calendário" );
$obTxtAno->setSize      ( 4 );
$obTxtAno->setMaxLength ( 4 );
$obTxtAno->setNull      ( false );
$obTxtAno->setMascara   ( '9999' );
$obTxtAno->obEvento->setOnKeyUp("mascaraDinamico('2059', this, event);");

$obRFeriado->obTFeriado->setDado( "ano", $stAnoCalendario );
$obRFeriado->listar( $rsFeriados,"","","" );

$obCalendario = new Calendario;
$obCalendario->setComplementoLink( "&stAcao=" . $stAcao );

if ($stAcao == 'incluir') {
    $obCalendario->setLink           ( $pgForm );
} else {
    $obCalendario->setLink           ( $pgList );
 }

$obCalendario->setRsFeriados     ( $rsFeriados );
$obCalendario->montaCalendario   ( $stAnoCalendario );
//$obCalendario->show();

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction                  ( $pgFilt );
//$obForm->setTarget                  ( "" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm              ( $obForm );
$obFormulario->addHidden            ( $obHdnAcao );
$obFormulario->addHidden            ( $obHdnCtrl );
$obFormulario->addComponente        ( $obTxtAno );
$obFormulario->addFormulario        ( $obCalendario );
$obFormulario->OK                   ();
$obFormulario->show                 ();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';s
?>
