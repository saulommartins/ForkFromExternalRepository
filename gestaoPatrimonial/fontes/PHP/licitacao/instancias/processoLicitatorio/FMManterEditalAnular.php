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
    * Página de Filtro de Manter Edital
    * Data de Criação   :23/10/2006

    * @author Analista: Cleisson Barbosa
    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @ignore

    $Revision: 23930 $
    $Name$
    $Author: andre.almeida $
    $Date: 2007-07-12 11:50:04 -0300 (Qui, 12 Jul 2007) $

    * Casos de uso: uc-03.05.16
*/

/**
$Log$
Revision 1.3  2007/07/12 14:50:04  andre.almeida
Bug #8487#

Revision 1.2  2007/01/24 12:01:09  tonismar
bug #8075

Revision 1.1  2006/10/27 10:21:11  tonismar
Manter Edital

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GP_LIC_COMPONENTES. "IPopUpNumeroEdital.class.php" );
include_once ( CAM_GP_LIC_COMPONENTES. "ILabelNumeroLicitacao.class.php" );

$stPrograma = "ManterEdital";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";
$pgFormAnular = "FM".$stPrograma."Anular.php";

$stAcao = $request->get('stAcao');
$stCtrl = $_REQUEST['stCtrl'];

if ($_REQUEST['stNumEdital']) {
    $jsOnLoad = "ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&stNumEdital=".$_REQUEST['stNumEdital']."', 'carregaLabel' );";
}

//DEFINICAO DOS COMPONENTES DO FORMULARIO
$obForm = new Form();
$obForm->setAction ( $pgProc  );
$obForm->setTarget ( 'oculto' );

$obHdnCtrl = new Hidden();
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( $stCtrl  );

$obHdnAcao = new Hidden();
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao  );

$obHdnNumEdital = new Hidden();
$obHdnNumEdital->setName  ( "stNumEdital" );
$obHdnNumEdital->setValue ( $_REQUEST['stNumEdital']  );

$obLblNumEdital = new Label();
$obLblNumEdital->setId( 'stNumEdital' );
$obLblNumEdital->setValue( $_REQUEST['stNumEdital'] );
$obLblNumEdital->setRotulo( 'Número do Edital' );

$obSpnNumeroLicitacao = new Span();
$obSpnNumeroLicitacao->setId( 'inNumLicitacao' );

$obTxtJustificativa = new TextArea();
$obTxtJustificativa->setName( 'stJustificativa' );
$obTxtJustificativa->setRotulo( 'Justificativa' );
$obTxtJustificativa->setNull( false );
$obTxtJustificativa->setCols( 150 );
$obTxtJustificativa->setRows( 5 );

$obFormulario = new Formulario();
$obFormulario->addForm( $obForm );
$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnNumEdital );
$obFormulario->addTitulo( 'Dados do Edital' );
$obFormulario->addComponente( $obLblNumEdital );
$obFormulario->addSpan( $obSpnNumeroLicitacao );
$obFormulario->addComponente( $obTxtJustificativa );
$obFormulario->Cancelar( $pgList."?".Sessao::getId()."&stAcao=".$stAcao.$stFiltro );
$obFormulario->Show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
