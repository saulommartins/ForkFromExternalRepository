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
    * Página de Filtro de Calendario
    * Data de Criação   : 06/09/2004

    * @author Analista: Leandro Oliveira
    * @author Desenvolvedor: Eduardo Martins

    * @ignore

    $Revision: 30859 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

    * Casos de uso :uc-04.02.04

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GRH_CAL_NEGOCIO."RCalendario.class.php");
include_once(CAM_GRH_CAL_NEGOCIO."RCalendarioFeriadoVariavel.class.php");

$stPrograma = "RelatorioCalendario";
$pgFilt = "FL".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$obRegra = new RCalendario;

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];

Sessao::remove('link');

$obForm = new Form;
$obForm->setAction( "../../../../../../gestaoAdministrativa/fontes/PHP/framework/popups/relatorio/OCRelatorio.php" );
$obForm->setTarget( "oculto" );

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName("stCaminho");
$obHdnCaminho->setValue( CAM_GRH_CAL_INSTANCIAS.'relatorio/OCRelatorioCalendario.php' );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obBscCalendario = new BuscaInner;
$obBscCalendario->setRotulo            ( "Calendário"                      );
$obBscCalendario->setTitle             ( "Informe o código do calendario".Sessao::getEntidade().""  );
$obBscCalendario->setNull              ( false                             );
$obBscCalendario->setId                ( "stDescricao"                      );
$obBscCalendario->obCampoCod->setName  ( "inCodCalendar"                   );
$obBscCalendario->obCampoCod->setValue ( $inCodCalendar                    );
$obBscCalendario->obCampoCod->setAlign ( "right"                           );
$obBscCalendario->obCampoCod->obEvento->setOnBlur("buscaValor( 'buscaCalendario','".$pgOcul."','".$obForm->getAction()."','oculto','".Sessao::getId()."' );");
$obBscCalendario->setFuncaoBusca( "abrePopUp('../../popups/relatorio/FLProcurarCalendario.php','frm','inCodCalendar','stDescricao','','".Sessao::getId()."','800','550')" );

$obDtDataInicial = new Data;
$obDtDataInicial->setName      ( "dtDataInicial" );
$obDtDataInicial->setValue     ( $dtDataInicial );
$obDtDataInicial->setRotulo    ( "Período" );
$obDtDataInicial->setTitle     ( "Período de filtragem" );
$obDtDataInicial->setValue     ( "01/01/" . date(Y) );
$obDtDataInicial->setNull      ( false );

$obLblA = new Label;
$obLblA->setRotulo (" Intervalo ");
$obLblA->setValue  ("&nbsp;&nbsp;a&nbsp;&nbsp;");

$obDtDataFinal  = new Data;
$obDtDataFinal->setName      ( "dtDataFinal" );
$obDtDataFinal->setValue     ( $dtDataFinal );
$obDtDataFinal->setValue     ( "31/12/" . date(Y) );
$obDtDataFinal->setNull      ( false );

$obHdnValidaData = new HiddenEval;
$obHdnValidaData->setName( "stValidaData" );
$obHdnValidaData->setValue( "if (document.frm.dtDataInicial.value > document.frm.dtDataFinal.value) {erro = true; mensagem += '@No período a data final ('+ document.frm.dtDataFinal.value +') dever ser maior que a data inicial ('+ document.frm.dtDataInicial.value+')!';}" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addTitulo            ( "Filtro do relatório" );
$obFormulario->addForm              ( $obForm );
$obFormulario->addHidden            ( $obHdnAcao );
$obFormulario->addHidden            ( $obHdnCtrl );
$obFormulario->addHidden            ( $obHdnValidaData,true);
$obFormulario->addHidden            ( $obHdnCaminho );
$obFormulario->addComponente        ( $obBscCalendario );
$obFormulario->agrupaComponentes    ( array( $obDtDataInicial, $obLblA ,$obDtDataFinal ));
$obFormulario->OK                   ();
$obFormulario->show                 ();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
