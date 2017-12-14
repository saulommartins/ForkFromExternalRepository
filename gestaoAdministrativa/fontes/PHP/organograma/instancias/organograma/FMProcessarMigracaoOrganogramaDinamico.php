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
  * Página de Formulário para Migrar Organograma
  * Data de criação: 14/04/2009
  *
  *
  * @author Analista: Gelson Wolowski   <gelson.goncalves@cnm.org.br>
  * @author Programador: Diogo Zarpelon <diogo.zarpelon@cnm.org.br>
  *
  *
  * $Id:$
  *
  */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$stPrograma = "ProcessarMigracaoOrganogramaDinamico";
$pgForm     = "FM".$stPrograma.".php";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

$inNumCgm = Sessao::read('numCgm');

$jsOnload = "montaParametrosGET('verificaStatus');";

$obForm = new Form;
$obForm->setAction ( $pgProc  );
$obForm->setTarget ( "oculto" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName     ( "stAcao"            );
$obHdnAcao->setValue    ( $_REQUEST['stAcao'] );

$obLblStatus = new Label;
$obLblStatus->setRotulo ( "Configuração da Migração de Organograma" );
$obLblStatus->setName   ( "stStatusConfiguracao" );
$obLblStatus->setId     ( "stStatusConfiguracao" );

$obSpanMsg = new Span;
$obSpanMsg->setId    ( "obSpanMsg" );

$obBtnOk = new Ok(true);
$obBtnOk->setId( 'Ok' );

$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm           );
$obFormulario->addHidden     ( $obHdnAcao        );
$obFormulario->addTitulo     ( "Status"          );
$obFormulario->addComponente ( $obLblStatus      );
$obFormulario->addSpan       ( $obSpanMsg        );
$obFormulario->defineBarra   ( array( $obBtnOk ) );

if ($inNumCgm <> 0) {
  $jsOnload  = "alertaAviso('Essa rotina só pode ser executada pelo <strong>Administrador</strong> do Sistema.', 'aviso', 'aviso', '".Sessao::getId()."'); ";
  $jsOnload .= "loadingModal(true, false, 'Ação Bloqueada');";
  $jsOnload .= "jQuery('#loadingModal').css('display', 'none');";
} else {
  # Constroi o formulário.
  $obFormulario->show();
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
