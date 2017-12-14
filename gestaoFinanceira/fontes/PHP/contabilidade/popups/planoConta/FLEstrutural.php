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
    * Página de Listagem de Plano Conta
    * Data de Criação   : 12/05/2008

    * @author Analista: Tonismar RÃ©gis Bernardo
    * @author Desenvolvedor: Grasiele Torres

    * @ignore

    * $Id: FLEstrutural.php 31716 2008-08-05 13:13:32Z girardi $

    * Casos de uso: uc-02.02.02
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GF_CONT_NEGOCIO."RContabilidadePlanoConta.class.php");

$stPrograma = "PlanoConta";
$pgFilt = "FLEstrutural.php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgList = "LSEstrutural.php";
$pgJs   = "JS".$stPrograma.".js";

$obRegra = new RContabilidadePlanoConta;
$obRegra->recuperaMascaraConta( $stMascara );

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];

Sessao::remove('linkPopUp');

$obForm = new Form;
$obForm->setAction( $pgList );
$obForm->setTarget( "" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnForm = new Hidden;
$obHdnForm->setName( "nomForm" );
$obHdnForm->setValue( $_REQUEST['nomForm'] );

$obHdnCodIniEstrutural = new Hidden();
$obHdnCodIniEstrutural->setName( 'inCodIniEstrutural' );
$obHdnCodIniEstrutural->setValue( $_REQUEST['inCodIniEstrutural'] );

$obHdnExercicio = new Hidden();
$obHdnExercicio->setName("stExercicio");
if ( isset($_REQUEST['stExercicio']) ) {
    $obHdnExercicio->setValue($_REQUEST['stExercicio']);
}

$obHdnCampoNum = new Hidden;
$obHdnCampoNum->setName( "campoNum" );
$obHdnCampoNum->setValue( $_REQUEST['campoNum'] );

$obHdnCampoNom = new Hidden;
$obHdnCampoNom->setName( "campoNom" );
$obHdnCampoNom->setValue( $_REQUEST['campoNom'] );

$obHdnTipoBusca = new Hidden;
$obHdnTipoBusca->setName( "tipoBusca" );
$obHdnTipoBusca->setValue( $_REQUEST['tipoBusca'] );

$obHdnTipoBusca2 = new Hidden;
$obHdnTipoBusca2->setName( "tipoBusca2" );
$obHdnTipoBusca2->setValue( $_REQUEST['tipoBusca2'] );

// Objeto Hidden usado quando a for uma Consulta de Conta para o Bordero de Transferencia
$obHdnTipoTransacao = new Hidden;
$obHdnTipoTransacao->setName( "stTipoTransacao" );
$obHdnTipoTransacao->setValue( $_REQUEST['stTipoTransacao'] );

if ( $_GET[$_GET['stNomSelectMultiplo']] && is_array( $_GET[$_GET['stNomSelectMultiplo']] )) {
    $stEntidades = "";
    foreach ($_GET[$_GET['stNomSelectMultiplo']] as $key => $valor) {
        $stEntidades .= $valor.",";
    }
    $stEntidade = substr($stEntidades,0,strlen($stEntidades)-1);
} elseif ( $_REQUEST['inCodEntidade'] && !is_array($_REQUEST['inCodEntidade']) ) {
    $stEntidade = $_REQUEST['inCodEntidade'];
} else {
    $stEntidade = "";
}

$obHdnCodEntidade = new Hidden;
$obHdnCodEntidade->setName( "inCodEntidade" );
$obHdnCodEntidade->setValue( $stEntidade );

$obTxtDescricao = new TextBox;
$obTxtDescricao->setName      ( "stDescricao" );
$obTxtDescricao->setValue     ( $stDescricao );
$obTxtDescricao->setRotulo    ( "Descrição" );
$obTxtDescricao->setTitle     ( "Descrição do Plano" );
$obTxtDescricao->setSize      ( 80 );
$obTxtDescricao->setMaxLength ( 100 );
$obTxtDescricao->setNull      ( true );

$obTxtCodEstrutural = new TextBox;
$obTxtCodEstrutural->setName      ( "stCodEstrutural" );
$obTxtCodEstrutural->setRotulo    ( "Código Estrutural" );
$obTxtCodEstrutural->setMascara   ( $stMascara );
$obTxtCodEstrutural->setPreencheComZeros('D');
$obTxtCodEstrutural->obEvento->setOnKeyPress("return validaExpressao(this,event,'[0-9.]');");

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addTitulo            ( "Dados para Filtro" );
$obFormulario->addForm              ( $obForm );
$obFormulario->addHidden            ( $obHdnAcao );
$obFormulario->addHidden            ( $obHdnCtrl );
$obFormulario->addHidden            ( $obHdnForm );
$obFormulario->addHidden            ( $obHdnCampoNum );
$obFormulario->addHidden            ( $obHdnCampoNom );
$obFormulario->addHidden            ( $obHdnTipoBusca );
$obFormulario->addHidden            ( $obHdnTipoBusca2 );
$obFormulario->addHidden            ( $obHdnTipoTransacao );
$obFormulario->addHidden            ( $obHdnCodIniEstrutural );
$obFormulario->addHidden            ( $obHdnCodEntidade );
$obFormulario->addHidden            ( $obHdnExercicio );
$obFormulario->addComponente        ( $obTxtDescricao );
$obFormulario->addComponente        ( $obTxtCodEstrutural );
$obFormulario->OK                   ();
$obFormulario->show                 ();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
