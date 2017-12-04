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
    * Página de Formulário para Inclusão de dia compensado
    * Data de Criação   : 28/04/2005

    * @author Analista: Leandro Oliveira
    * @author Desenvolvedor: Vandré Miguel Ramos

    * @ignore

    $Revision: 30895 $
    $Name$
    $Author: vandre $
    $Date: 2006-08-08 14:53:12 -0300 (Ter, 08 Ago 2006) $

    * Casos de uso :uc-04.02.05

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GRH_CAL_NEGOCIO."RCalendarioFeriado.class.php");

$stPrograma = "ManterDiaCompensado";
$pgFilt = "FL".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stAbrangencia = "";

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

$dtData = $_REQUEST['dtData'];

if ($stAcao == 'alterar') {
    $obRFeriado = new RFeriado;
    $obRFeriado->setCodFeriado( $_REQUEST['inCodFeriado'] );
    $obRFeriado->setdtFeriado ($_REQUEST['dt_feriado']);

    $obRFeriado->listar( $rsFeriado,"","","");

    $inCodFeriado  = $rsFeriado->getCampo('cod_feriado');
    $dtData        = $rsFeriado->getCampo('dt_feriado');
    $stDescricao   = $rsFeriado->getCampo('descricao');
    $stTipoFeriado = $rsFeriado->getCampo('tipoferiado');
}

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnCodFeriado = new Hidden;
$obHdnCodFeriado->setName( "inCodFeriado" );
$obHdnCodFeriado->setValue( $inCodFeriado );

$obHdnDtFeriado = new Hidden;
$obHdnDtFeriado->setName( "dtData" );
$obHdnDtFeriado->setValue( $dtData );

$obLbDtData = new Label;
$obLbDtData->setName      ( "lbData" );
$obLbDtData->setValue     ( $dtData );
$obLbDtData->setRotulo    ( "Data" );
$obLbDtData->setTitle     ( "Data do dia compensado" );

$obTxtDescricao = new TextBox;
$obTxtDescricao->setName      ( "stDescricao" );
$obTxtDescricao->setValue     ( $stDescricao );
$obTxtDescricao->setRotulo    ( "Descrição" );
$obTxtDescricao->setTitle     ( "Descrição do dia compensado" );
$obTxtDescricao->setSize      ( 50 );
$obTxtDescricao->setMaxLength ( 100 );
$obTxtDescricao->setNull      ( false );

//DEFINICAO DOS COMPONENTES2
$obForm = new Form;
$obForm->setAction                  ( $pgProc  );
$obForm->setTarget                  ( "oculto" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addTitulo            ( "Dados do dia compensado" );
$obFormulario->addForm              ( $obForm );
$obFormulario->addHidden            ( $obHdnAcao );
$obFormulario->addHidden            ( $obHdnDtFeriado );
$obFormulario->addHidden            ( $obHdnCtrl );
$obFormulario->addHidden            ( $obHdnCodFeriado );
$obFormulario->addComponente        ( $obLbDtData );
$obFormulario->addComponente        ( $obTxtDescricao );
$obFormulario->OK                   ();
$obFormulario->show                 ();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
