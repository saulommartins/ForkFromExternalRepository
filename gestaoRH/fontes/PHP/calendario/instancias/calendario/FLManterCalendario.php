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
    * Data de Criação   : 19/08/2004

    * @author Analista: Leandro Oliveira
    * @author Desenvolvedor: Eduardo Martins

    * @ignore

    $Revision: 30859 $
    $Name$
    $Author: vandre $
    $Date: 2006-08-08 14:53:12 -0300 (Ter, 08 Ago 2006) $

    * Casos de uso :uc-04.02.02

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GRH_CAL_NEGOCIO."RCalendario.class.php");
include_once(CAM_GRH_CAL_NEGOCIO."RCalendarioFeriadoVariavel.class.php");

$stPrograma = "ManterCalendario";
$pgFilt  = "FL".$stPrograma.".php";
$pgForm  = "FM".$stPrograma.".php";
$pgProc  = "PR".$stPrograma.".php";
$pgOcul  = "OC".$stPrograma.".php";
$pgList  = "LS".$stPrograma.".php";
$pgListF = "LSManterCalendarioFeriado.php";
$pgJs    = "JS".$stPrograma.".js";

$stAcao = "consultar";
$stCaminho   = "../calendario/";

if ($stAnoCalendario <= 0) {
  $stAnoCalendario = date(Y);
}

$obRegra = new RCalendario;

Sessao::write('cod_calendar', $_REQUEST['inCodCalendar']);

$inCodCalendar = $_REQUEST['inCodCalendar'];
$obRegra->setCodCalendar( $inCodCalendar );
$obRegra->consultar ();
$stDescricao = $obRegra->getDescricao();

$obRegra->addFeriadoVariavel();
$obRegra->setCodCalendar( $inCodCalendar );
$obRegra->obTCalendario->setDado("cod_calendar", $inCodCalendar );
$obRegra->obTCalendario->setDado( "ano", $stAnoCalendario );
$obRegra->listarFeriados($rsFeriados,$stOrder="",$boTransacao);
$obRegra->commitFeriadoVariavel();

Sessao::write('link', array( 'filtro' => array(), 'pg' => '' , 'pos' => '', 'paginando' => false ));

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnCodCalendar = new Hidden;
$obHdnCodCalendar->setName( "inCodCalendar" );
$obHdnCodCalendar->setValue( $inCodCalendar );

$obLblCodCalendar = new Label;
$obLblCodCalendar->setRotulo        ( "Calendário" );
$obLblCodCalendar->setName          ( "inCodCalendar" );
$obLblCodCalendar->setValue         ( $inCodCalendar . " - " . $stDescricao );

$obCalendario = new Calendario;
$obCalendario->setComplementoLink( "&stAcao=" . $stAcao );
$obCalendario->setLink           ( $stCaminho . $pgListF );

$obCalendario->setRsFeriados     ( $rsFeriados );
$obCalendario->montaCalendario   ( $stAnoCalendario );

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction                  ( $pgList );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm              ( $obForm );
$obFormulario->addHidden            ( $obHdnAcao );
$obFormulario->addHidden            ( $obHdnCodCalendar );
$obFormulario->addHidden            ( $obHdnCtrl );
$obFormulario->addComponente        ( $obLblCodCalendar);
$obFormulario->addFormulario        ( $obCalendario );
$obFormulario->Voltar               ();
$obFormulario->show                 ();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
