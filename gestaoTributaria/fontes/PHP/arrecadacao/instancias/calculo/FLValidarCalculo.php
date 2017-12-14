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
    * Página de Formulario para Validar Cálculo simulado
    * Data de criação : 21/10/2008

    * @author Analista: Fabio Bertold Rodrigues
    * @author Programador: André Machado

    * $Id: FLEfetuarLancamentos.php 32939 2008-09-03 21:14:50Z domluc $

    Caso de uso: uc-05.03.05
**/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_MON_NEGOCIO."RMONCredito.class.php" );
include_once ( CAM_GT_ARR_COMPONENTES."MontaGrupoCredito.class.php" );

//Define o nome dos arquivos PHP
$stPrograma      = "ValidarCalculo";
$pgFilt          = "FL".$stPrograma.".php";
$pgList          = "LS".$stPrograma.".php";
$pgForm          = "FM".$stPrograma.".php";
$pgProc          = "PR".$stPrograma.".php";
$pgOcul          = "OC".$stPrograma.".php";
$pgJs            = "JS".$stPrograma.".js";
//include_once( $pgJs );

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

Sessao::write( "link", "" );

// OBJETOS HIDDEN
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( $_REQUEST["stCtrl"]  );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao  );

// DEFINE OBJETOS DO FORMULARIO
$obMontaGrupoCredito = new MontaGrupoCredito;

$obForm = new Form;
$obForm->setAction           ( $pgList  );
//$obForm->setTarget           ( "principal" );
//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm              );
$obFormulario->addHidden     ( $obHdnCtrl           );
$obFormulario->addHidden     ( $obHdnAcao           );
$obFormulario->addTitulo     ( "Dados para Cálculo" );
$obMontaGrupoCredito->geraFormulario( $obFormulario, true, true );

//$obFormulario->addComponente ( $obBscGrupoCredito   );

$obFormulario->ok();
$obFormulario->show();

?>
