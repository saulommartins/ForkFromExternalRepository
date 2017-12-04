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
    * Página de Formulário para impugnar edital
    * Data de Criação   : 13/11/2006

    * @author Analista: Gelson Wolowski Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * @ignore

    * Casos de uso : uc-03.05.27
*/

/*
$Log$
Revision 1.5  2007/06/29 15:09:13  hboaventura
Bug#9497#

Revision 1.4  2007/06/11 18:59:37  hboaventura
Bug #9146#

Revision 1.3  2007/03/01 13:10:52  tonismar
bug #8180

Revision 1.2  2006/11/27 12:02:36  hboaventura
Implementação do caso de uso 03.05.27
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GP_LIC_COMPONENTES."IPopUpNumeroEdital.class.php" );
include_once ( CAM_GA_PROT_CLASSES."componentes/IPopUpProcesso.class.php" );

//Definições padrões do framework
$stPrograma = "ManterImpugnacaoEdital";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');
$stCtrl = $request->get('stCtrl');

$stLocation = $pgList.'?'.Sessao::getId().'&stAcao='.$stAcao;

$obForm = new Form();
$obForm->setAction      ( $pgProc  );
$obForm->setTarget      ( "oculto" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao  );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( "" );

if ($_REQUEST["stAcao"] == "incluir") {
    $obPopUpProcesso = new IPopUpProcesso ( $obForm );
    $obPopUpProcesso->obCampoCod->setName("stCodigoProcesso");
    $obPopUpProcesso->obCampoCod->setId("stCodigoProcesso");
    $obPopUpProcesso->setRotulo("Processo da Impugnação");
    $obPopUpProcesso->setTitle("Informe o número do processo do protocolo que deu origem à impugnação");
    $obPopUpProcesso->setObrigatorioBarra(true);
}

$obLblNumeroEdital = new Label();
$obLblNumeroEdital->setRotulo( 'Número do Edital' );
$obLblNumeroEdital->setId( 'stNumEdital' );
$obLblNumeroEdital->setValue( $request->get('inNumEdital').'/'.$_REQUEST['stExercicio'] );

$obHdnNumeroEdital = new Hidden();
$obHdnNumeroEdital->setId( 'stNumeroEdital' );
$obHdnNumeroEdital->setName( 'stNumeroEdital' );
$obHdnNumeroEdital->setValue( $_REQUEST['inNumEdital'].'/'.$_REQUEST['stExercicio'] );

$obSpnNumeroLicitacao = new Span();
$obSpnNumeroLicitacao->setId( "inNumLicitacao" );

$obSpnListaProcesso = new Span();
$obSpnListaProcesso->setId( "spnListaProcesso" );

$obFormulario = new Formulario;
$obFormulario->addForm                  ( $obForm                          );
$obFormulario->setAjuda                 ( "UC-03.05.27"                    );
$obFormulario->addHidden                ( $obHdnCtrl                       );
$obFormulario->addHidden                ( $obHdnAcao                       );
$obFormulario->addHidden                ( $obHdnNumeroEdital               );
$obFormulario->addTitulo                ( "Dados do Edital"                );
$obFormulario->addComponente            ( $obLblNumeroEdital               );
$obFormulario->addSpan                  ( $obSpnNumeroLicitacao            );
if ($_REQUEST["stAcao"] == "incluir") {
    $obFormulario->addTitulo            ( "Processo da Impugnação"         );
    $obFormulario->addComponente        ( $obPopUpProcesso                 );
    $obFormulario->Incluir('ListaProcesso', array($obPopUpProcesso) );
}
$obFormulario->addSpan                  ( $obSpnListaProcesso              );
$obFormulario->Cancelar( $stLocation );
$obFormulario->show();

echo "<script>\n";
if ($stAcao == 'incluir') {
    echo "ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&stNumEdital=".$_REQUEST['inNumEdital']."/".$_REQUEST['stExercicio']."','carregaLabel');";
} else {
    echo "ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&stNumEdital=".$_REQUEST['inNumEdital']."/".$_REQUEST['stExercicio']."','carregaLabelAnular');";
}
echo "</script>";

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
