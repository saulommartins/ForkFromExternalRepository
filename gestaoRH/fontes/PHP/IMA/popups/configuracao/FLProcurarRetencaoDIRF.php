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
    * Formulário
    * Data de Criação: 19/01/2009

    * @author Desenvolvedor: Rafael Garbin

    * Casos de uso: uc-04.08.14

    $Id:
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$stPrograma = "ProcurarRetencaoDIRF";
$pgFilt = "FL".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

#$pgOculRetencaoDIRF = "'".CAM_GRH_IMA_PROCESSAMENTO."OCProcurarRetencaoDIRF.php?".Sessao::getId()."'";
#$jsOnLoad = "ajaxJavaScript(".$pgOculRetencaoDIRF.",'validaParametros');";

Sessao::remove("link");
$stAcao = $request->get('stAcao');

$obIFrame = new IFrame;
$obIFrame->setName("oculto");
$obIFrame->setWidth("100%");
$obIFrame->setHeight("50");

$obIFrame2 = new IFrame;
$obIFrame2->setName   ( "telaMensagem");
$obIFrame2->setWidth  ( "100%"        );
$obIFrame2->setHeight ( "50"          );

$obForm = new Form;
$obForm->setAction( $pgList );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnCampoNum = new Hidden;
$obHdnCampoNum->setName( "campoNum" );
$obHdnCampoNum->setValue( $_REQUEST["campoNum"] );

$obHdnCampoNom = new Hidden;
$obHdnCampoNom->setName( "campoNom" );
$obHdnCampoNom->setValue( $_REQUEST["campoNom"] );

$obHdnTipoPrestador = new Hidden;
$obHdnTipoPrestador->setName( "stTipoPrestador" );
$obHdnTipoPrestador->setValue( $_REQUEST["stTipoPrestador"] );

$obLblTipoPrestador = new Label;
$obLblTipoPrestador->setRotulo( "Tipo de Prestador" );
$obLblTipoPrestador->setId    ( "lblTipoPrestador" );
$obLblTipoPrestador->setValue ( (trim($_REQUEST["stTipoPrestador"])=="P"?"Pessoa Física":"Pessoa Jurídica") );

$obTxtCodigoDIRF = new TextBox;
$obTxtCodigoDIRF->setName    ( "inCodDIRF" );
$obTxtCodigoDIRF->setId      ( "inCodDIRF" );
$obTxtCodigoDIRF->setValue   ( $_REQUEST["inCodDIRF"]  );
$obTxtCodigoDIRF->setRotulo  ( "Código" );
$obTxtCodigoDIRF->setTitle   ( "Informe o Código de Retenção da DIRF" );
$obTxtCodigoDIRF->setSize    ( 8 );
$obTxtCodigoDIRF->setInteiro ( true );

$obTxtDescricaoDIRF = new TextBox;
$obTxtDescricaoDIRF->setName   ( "stDescricaoDIRF" );
$obTxtDescricaoDIRF->setId     ( "stDescricaoDIRF" );
$obTxtDescricaoDIRF->setValue  ( $_REQUEST["stDescricaoDIRF"] );
$obTxtDescricaoDIRF->setRotulo ( "Descrição" );
$obTxtDescricaoDIRF->setTitle  ( "Informe a descrição da Retenção da DIRF" );
$obTxtDescricaoDIRF->setSize   ( 80  );

$obTxtExercicio = new TextBox;
$obTxtExercicio->setName      ( "inExercicio" );
$obTxtExercicio->setValue     ( $_REQUEST["inExercicio"] );
$obTxtExercicio->setRotulo    ( "Exercício" );
$obTxtExercicio->setTitle     ( "Informe o exercício para filtro" );
$obTxtExercicio->setSize      ( 4 );
$obTxtExercicio->setMaxLength ( 4 );
$obTxtExercicio->setNull      ( true );
$obTxtExercicio->setReadOnly  ( true );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addTitulo       ( "Dados para Filtro do código da retenção da DIRF" );
$obFormulario->addForm         ( $obForm );
$obFormulario->addHidden       ( $obHdnAcao );
$obFormulario->addHidden       ( $obHdnCtrl );
$obFormulario->addHidden       ( $obHdnCampoNum );
$obFormulario->addHidden       ( $obHdnCampoNom );
$obFormulario->addHidden       ( $obHdnTipoPrestador );
$obFormulario->addComponente   ( $obTxtCodigoDIRF );
$obFormulario->addComponente   ( $obTxtDescricaoDIRF );
$obFormulario->addComponente   ( $obLblTipoPrestador );
$obFormulario->addComponente   ( $obTxtExercicio );
$obFormulario->OK();
$obFormulario->show();
$obIFrame2->show();
$obIFrame->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
