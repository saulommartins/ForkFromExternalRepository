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
    * Página de Formulario Relatório de Extrato da Divida Ativa

    * Data de Criação   : 25/11/2008

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato
    * @ignore

    * $Id: $

    *Casos de uso: uc-05.04.10

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_MON_COMPONENTES."IPopUpCredito.class.php" );

if ( empty( $_REQUEST['stAcao'] ) ) {
    $_REQUEST['stAcao'] = 'incluir';
}

//Define o nome dos arquivos PHP
$stPrograma    = "ExtratoDivida";
$pgForm        = "FM".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php?".Sessao::getId();

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $_REQUEST['stAcao']  );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName   ( "stCtrl" );
$obHdnCtrl->setValue  ( $_REQUEST['stCtrl']  );

$obExercicio = new Exercicio;
$obExercicio->setName ( "inExercicio" );

$obIPopUpCredito = new IPopUpCredito;
$obIPopUpCredito->setNull( true );

$obCmbTipo = new Select;
$obCmbTipo->setName      ( "stTipoRelatorio"                );
$obCmbTipo->setRotulo    ( "Tipo de Relatório"              );
$obCmbTipo->setTitle     ( "Selecione o tipo de relatório"  );
$obCmbTipo->addOption    ( ""          , "Selecione"        );
$obCmbTipo->addOption    ( "analitico" , "Analítico"        );
$obCmbTipo->addOption    ( "sintetico" , "Sintético"        );
$obCmbTipo->setCampoDesc ( "stTipo"                         );
$obCmbTipo->setNull      ( false                            );
$obCmbTipo->setStyle     ( "width: 200px"                   );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction ( $pgForm );
$obForm->settarget ( "oculto" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm ( $obForm );
$obFormulario->setAjuda ( "UC-05.04.10" );
$obFormulario->addHidden ( $obHdnAcao );
$obFormulario->addHidden ( $obHdnCtrl );
$obFormulario->addTitulo ( "Dados para Filtro" );
$obFormulario->addComponente ( $obExercicio );
$obIPopUpCredito->geraFormulario ( $obFormulario );
$obFormulario->addComponente ( $obCmbTipo );

$obFormulario->ok(true);
$obFormulario->show();
