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
    * Página de Listagem de Receita
    * Data de Criação   : 25/03/2006

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @ignore

    * $Id: FLReceita.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.02.02,uc-02.04.04
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GF_ORC_NEGOCIO."ROrcamentoConfiguracao.class.php");

$stPrograma = "Receita";
$pgFilt = "FL".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$obRegra = new ROrcamentoConfiguracao;
$obRegra->setExercicio( Sessao::getExercicio() );
$obRegra->consultarConfiguracao( $boTransacao );
$stMascReceita = $obRegra->getMascClassificacaoReceita();

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];

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

$obHdnCampoNum = new Hidden;
$obHdnCampoNum->setName( "campoNum" );
$obHdnCampoNum->setValue( $_REQUEST['campoNum'] );

$obHdnCampoNom = new Hidden;
$obHdnCampoNom->setName( "campoNom" );
$obHdnCampoNom->setValue( $_REQUEST['campoNom'] );

$obHdnTipoBusca = new Hidden;
$obHdnTipoBusca->setName( "tipoBusca" );
$obHdnTipoBusca->setValue( $_REQUEST['tipoBusca'] );

$obHdnCodEntidade = new Hidden;
$obHdnCodEntidade->setName( "inCodEntidade" );
$obHdnCodEntidade->setValue( $_REQUEST['inCodEntidade'] );

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
$obTxtCodEstrutural->setMascara   ( $stMascReceita );
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
$obFormulario->addHidden            ( $obHdnCodEntidade );
$obFormulario->addComponente        ( $obTxtDescricao );
$obFormulario->addComponente        ( $obTxtCodEstrutural );
$obFormulario->OK                   ();
$obFormulario->show                 ();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
