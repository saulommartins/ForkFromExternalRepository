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
    * Página de Consulta de Feriados
    * Data de Criação   : 13/08/2004

    * @author Analista: Leandro Oliveira
    * @author Desenvolvedor: Eduardo Martins

    * @ignore

    $Revision: 30547 $
    $Name$
    $Author: vandre $
    $Date: 2006-08-08 14:53:12 -0300 (Ter, 08 Ago 2006) $

    * Casos de uso :uc-04.02.01

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GRH_CAL_NEGOCIO."RCalendarioFeriado.class.php");

$stPrograma = "ManterFeriado";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$obRFeriado = new RFeriado;

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

    $obRFeriado->setCodFeriado( $_GET['inCodFeriado'] );

    $obRFeriado->listar( $rsFeriado );
    $dtData        =  $obRFeriado->getDtFeriado();
    $stDescricao   =  $obRFeriado->getDescricao();
    $stTipoFeriado =  $obRFeriado->getTipoFeriado();
    $srAbrangencia =  $obRFeriado->getAbrangencia();

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnCodFeriado = new Hidden;
$obHdnCodFeriado->setName( "inCodFeriado" );
$obHdnCodFeriado->setValue( $_GET['inCodFeriado'] );

$obLblData = new Label;
$obLblData->setRotulo        ( "Data" );
$obLblData->setName          ( "dtData" );
$obLblData->setValue         ( $dtData );

$obLblDescricao = new Label;
$obLblDescricao->setRotulo        ( "Descrição" );
$obLblDescricao->setName          ( "stDescricao" );
$obLblDescricao->setValue         ( $stDescricao );

$obLblTipoFeriado = new Label;
/*$obLblTipoFeriado->setRotulo        ( "Tipo Feriado" );
$obLblTipoFeriado->setName          ( "stTipoFeriado" );
$obLblTipoFeriado->setValue         ( $stTipoFeriado );*/

$obLblAbrangencia = new Label;
$obLblAbrangencia->setRotulo        ( "Abrangência" );
$obLblAbrangencia->setName          ( "stAbrangencia" );
$obLblAbrangencia->setValue         ( $stAbrangecia );

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction                  ( $pgFilt );
$obForm->setTarget                  ( "telaPrincipal" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm              ( $obForm );
$obFormulario->addHidden            ( $obHdnAcao );
$obFormulario->addHidden            ( $obHdnCtrl );
$obFormulario->addHidden            ( $obHdnCodFeriado );

$obFormulario->addTitulo            ( "Dados do feriado" );

$obFormulario->addComponente        ( $obLblData );
$obFormulario->addComponente        ( $obLblDescricao );
//$obFormulario->addComponente        ( $obLblTipoFeriado );
$obFormulario->addComponente        ( $obLblAbrangencia );

$obFormulario->Voltar               ();
$obFormulario->show                 ();

//include_once($pgJs);
include_once '../../../includes/rodape.php';
?>
