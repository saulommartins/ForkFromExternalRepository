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
    * Página de Formulário para Inclusão/Alteração de Calendários
    * Data de Criação   : 28/06/2004

    * @author Analista: Leandro Oliveira
    * @author Desenvolvedor: Eduardo Martins

    * @ignore

    $Revision: 30895 $
    $Name$
    $Author: vandre $
    $Date: 2006-08-08 14:53:12 -0300 (Ter, 08 Ago 2006) $

    * Caso de uso: uc-04.02.02

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GRH_CAL_NEGOCIO."RCalendario.class.php");
include_once(CAM_GRH_CAL_NEGOCIO."RCalendarioFeriadoVariavel.class.php");

$stPrograma = "ManterCalendario";
$pgFilt = "FL".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$rsFeriadosDisponiveis  = new RecordSet;
$rsFeriadosSelecionados = new RecordSet;
$obRegra = new RCalendario;

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao        = $_REQUEST['stAcao'];
$inCodCalendar = $_REQUEST['inCodCalendar'];
$stDescricao   = $_REQUEST['stDescQuestao'];

if ( empty( $stAcao ) || $stAcao == 'incluir') {
    $stAcao = "incluir";
    $obRegra->listarFeriadosDisponiveis ( $rsFeriadosDisponiveis  );
}

if ($stAcao == 'alterar') {
    $obRegra->setCodCalendar( $inCodCalendar );
    $obRegra->listarFeriadosSelecionados( $rsFeriadosSelecionados );
    $obRegra->listarFeriadosDisponiveis ( $rsFeriadosDisponiveis  );
}

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnCodCalendar = new Hidden;
$obHdnCodCalendar->setName( "inCodCalendar" );
$obHdnCodCalendar->setValue( $inCodCalendar );

$obTxtDescricao = new TextBox;
$obTxtDescricao->setName      ( "stDescricao" );
$obTxtDescricao->setValue     ( $stDescricao );
$obTxtDescricao->setRotulo    ( "Descrição" );
$obTxtDescricao->setTitle     ( "Descrição do calendário" );
$obTxtDescricao->setSize      ( 50 );
$obTxtDescricao->setMaxLength ( 100 );
$obTxtDescricao->setNull      ( false );

$obCmbFeriados = new SelectMultiplo();
$obCmbFeriados->setName   ('inCodFeriados');
$obCmbFeriados->setRotulo ( "Feriados cadastrados" );
$obCmbFeriados->setNull   ( true );
$obCmbFeriados->setTitle  ( "Feriados" );

// lista de atributos disponiveis
$obCmbFeriados->SetNomeLista1 ('inCodFeriadosDisponiveis');
$obCmbFeriados->setCampoId1   ('[cod_feriado]_[tipoferiado]');
$obCmbFeriados->setCampoDesc1 ('[dt_feriado] - [descricao]');
$obCmbFeriados->setStyle1     ("width: 300px");
$obCmbFeriados->SetRecord1    ( $rsFeriadosDisponiveis );

// lista de atributos selecionados
$obCmbFeriados->SetNomeLista2 ('inCodFeriadosSelecionados');
$obCmbFeriados->setCampoId2   ('[cod_feriado]_[tipoferiado]');
$obCmbFeriados->setCampoDesc2 ('[dt_feriado] - [descricao]');
$obCmbFeriados->setStyle2     ("width: 300px");
$obCmbFeriados->SetRecord2    ( $rsFeriadosSelecionados );

//DEFINICAO DOS COMPONENTES2
$obForm = new Form;
$obForm->setAction                  ( $pgProc  );
$obForm->setTarget                  ( "oculto" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addTitulo            ( "Dados do calendário" );
$obFormulario->addForm              ( $obForm );
$obFormulario->addHidden            ( $obHdnAcao );
$obFormulario->addHidden            ( $obHdnCtrl );
$obFormulario->addHidden            ( $obHdnCodCalendar );
$obFormulario->addComponente        ( $obTxtDescricao );
$obFormulario->addComponente        ( $obCmbFeriados  );
$obFormulario->OK                   ();
$obFormulario->show                 ();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
