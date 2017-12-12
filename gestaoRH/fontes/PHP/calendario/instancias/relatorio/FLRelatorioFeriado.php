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
    * Página de Filtro de Feriado
    * Data de Criação   : 03/09/2004

    * @author Analista: Leandro Oliveira
    * @author Desenvolvedor: Eduardo Martins

    * @ignore

    $Revision: 30859 $
    $Name$
    $Author: vandre $
    $Date: 2006-08-08 14:53:12 -0300 (Ter, 08 Ago 2006) $

    * Casos de uso :uc-04.02.03

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GRH_CAL_NEGOCIO."RCalendario.class.php");
include_once(CAM_GRH_CAL_NEGOCIO."RCalendarioFeriadoVariavel.class.php");

$stPrograma = "RelatorioFeriado";
$pgFilt = "FL".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include_once ($pgJs);

$obRegra = new RFeriadoVariavel;

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];

Sessao::remove('link');

$obForm = new Form;
$obForm->setAction( "../../../../../../gestaoAdministrativa/fontes/PHP/framework/popups/relatorio/OCRelatorio.php" );
$obForm->setTarget( "oculto" );

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName("stCaminho");
$obHdnCaminho->setValue( "../../../../../../gestaoRH/fontes/PHP/calendario/instancias/relatorio/OCRelatorioFeriado.php" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obDtDataInicial = new Data;
$obDtDataInicial->setName      ( "dtDataInicial" );
$obDtDataInicial->setValue     ( $dtDataInicial );
$obDtDataInicial->setRotulo    ( "Período" );
$obDtDataInicial->setTitle     ( "Período de filtragem" );
$obDtDataInicial->setSize      ( 10 );
$obDtDataInicial->setMaxLength ( 10 );
$obDtDataInicial->setValue     ( "01/01/" . date(Y) );
$obDtDataInicial->setNull      ( false );

$obLblA = new Label;
$obLblA->setRotulo (" Intervalo ");
$obLblA->setValue  ("&nbsp;&nbsp;a&nbsp;&nbsp;");

$obDtDataFinal  = new Data;
$obDtDataFinal->setName      ( "dtDataFinal" );
$obDtDataFinal->setValue     ( $dtDataFinal );
$obDtDataFinal->setSize      ( 10 );
$obDtDataFinal->setMaxLength ( 10 );
$obDtDataFinal->setValue     ( "31/12/" . date(Y) );
$obDtDataFinal->setNull      ( false );

$obHdnValidaData = new HiddenEval;
$obHdnValidaData->setName( "stValidaData" );
$obHdnValidaData->setValue( "if (document.frm.dtDataInicial.value > document.frm.dtDataFinal.value) {erro = true; mensagem += '@No período a data final ('+ document.frm.dtDataFinal.value +') dever ser maior que a data inicial ('+ document.frm.dtDataInicial.value+')!';}" );

$obCmbTipo = new Select();
$obCmbTipo->setRotulo        ( "Tipo" );
$obCmbTipo->setName          ( "stTipo" );
$obCmbTipo->setNull          ( true );
$obCmbTipo->addOption        ( ''        ,'Todos'    );
$obCmbTipo->addOption        ( 'Fixo'    ,'Fixo'     );
$obCmbTipo->addOption        ( 'Variavel','Variável' );
$obCmbTipo->addOption        ( 'Ponto facultativo','Ponto facultativo' );
$obCmbTipo->addOption        ( 'Dia compensado','Dia compensado' );
$obCmbTipo->obEvento->setOnChange ('habilitaCombo(this)' );

$obCmbAbrangencia = new Select();
$obCmbAbrangencia->setRotulo ( "Abrangência" );
$obCmbAbrangencia->setName   ( "stAbrangencia" );
$obCmbAbrangencia->setNull   ( true );
$obCmbAbrangencia->addOption ( '' ,'Todos'     );
$obCmbAbrangencia->addOption ( 'F','Federal'   );
$obCmbAbrangencia->addOption ( 'E','Estadual'  );
$obCmbAbrangencia->addOption ( 'M','Municipal' );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addTitulo            ( "Filtro do relatório" );
$obFormulario->addForm              ( $obForm );
$obFormulario->addHidden            ( $obHdnAcao );
$obFormulario->addHidden            ( $obHdnValidaData,true);
$obFormulario->addHidden            ( $obHdnCtrl );
$obFormulario->addHidden            ( $obHdnCaminho );
$obFormulario->agrupaComponentes    ( array( $obDtDataInicial, $obLblA ,$obDtDataFinal ));
$obFormulario->addComponente        ( $obCmbTipo );
$obFormulario->addComponente        ( $obCmbAbrangencia );
$obFormulario->OK                   ();
$obFormulario->show                 ();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
