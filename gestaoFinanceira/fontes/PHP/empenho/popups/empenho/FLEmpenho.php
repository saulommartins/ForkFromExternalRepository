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
    * Página de Filtro da popup de Empenho
    * Data de Criação   : 04/05/2005

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    $Id: FLEmpenho.php 59612 2014-09-02 12:00:51Z gelson $

    $Revision: 31087 $
    $Name$
    $Autor: $
    $Date: 2007-12-05 15:12:56 -0200 (Qua, 05 Dez 2007) $

    * Casos de uso: uc-02.03.03
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$stPrograma = "Empenho";
$pgFilt = "FL".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');

Sessao::remove('linkPopUp');

$inCodEmpenhoInicial = '';
$inCodEmpenhoFinal = '';

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

$obHdnCampoExercicio = new Hidden;
$obHdnCampoExercicio->setName  ( 'stCampoExercicio'            );
$obHdnCampoExercicio->setValue ( $_REQUEST['stCampoExercicio'] );

$obHdnTipoBusca = new Hidden;
$obHdnTipoBusca->setName( "tipoBusca" );
$obHdnTipoBusca->setValue( $_REQUEST['tipoBusca'] );

$obHdnCodEntidade = new Hidden;
$obHdnCodEntidade->setName( "inCodEntidade" );
$obHdnCodEntidade->setValue( $_REQUEST['inCodEntidade'] );

$obHdnExercicioEmpenho = new Hidden;
$obHdnExercicioEmpenho->setName( "stExercicioEmpenho" );
$obHdnExercicioEmpenho->setValue( $_REQUEST['stExercicioEmpenho'] );

//Define o objeto TEXT para Codigo do Empenho Inicial
$obTxtCodEmpenhoInicial = new TextBox;
$obTxtCodEmpenhoInicial->setName     ( "inCodEmpenhoInicial" );
$obTxtCodEmpenhoInicial->setValue    ( $inCodEmpenhoInicial  );
$obTxtCodEmpenhoInicial->setRotulo   ( "Número do Empenho"   );
$obTxtCodEmpenhoInicial->setInteiro  ( true                  );
$obTxtCodEmpenhoInicial->setNull     ( true                  );

//Define objeto Label
$obLblEmpenho = new Label;
$obLblEmpenho->setValue( "a" );

//Define o objeto TEXT para Codigo do Empenho Final
$obTxtCodEmpenhoFinal = new TextBox;
$obTxtCodEmpenhoFinal->setName     ( "inCodEmpenhoFinal" );
$obTxtCodEmpenhoFinal->setValue    ( $inCodEmpenhoFinal  );
$obTxtCodEmpenhoFinal->setRotulo   ( "Número do Empenho" );
$obTxtCodEmpenhoFinal->setInteiro  ( true                );
$obTxtCodEmpenhoFinal->setNull     ( true                );

// Define objeto Data para Periodo
$obDtInicial = new Data;
$obDtInicial->setName     ( "stDtInicial" );
$obDtInicial->setRotulo   ( "Período" );
$obDtInicial->setTitle    ( '' );
$obDtInicial->setNull     ( true );

// Define Objeto Label
$obLabel = new Label;
$obLabel->setValue( " até " );

// Define objeto Data para validade final
$obDtFinal = new Data;
$obDtFinal->setName     ( "stDtFinal" );
$obDtFinal->setRotulo   ( "Período" );
$obDtFinal->setTitle    ( '' );
$obDtFinal->setNull     ( true );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addTitulo            ( "Dados para filtro"   );
$obFormulario->addForm              ( $obForm               );
$obFormulario->addHidden            ( $obHdnAcao            );
$obFormulario->addHidden            ( $obHdnCtrl            );
$obFormulario->addHidden            ( $obHdnForm            );
$obFormulario->addHidden            ( $obHdnCampoNum        );
$obFormulario->addHidden            ( $obHdnCampoNom        );
$obFormulario->addHidden            ( $obHdnCampoExercicio  );
$obFormulario->addHidden            ( $obHdnTipoBusca       );
$obFormulario->addHidden            ( $obHdnExercicioEmpenho);
$obFormulario->addHidden            ( $obHdnCodEntidade     );
$obFormulario->agrupaComponentes    ( array($obTxtCodEmpenhoInicial, $obLblEmpenho, $obTxtCodEmpenhoFinal ) );
$obFormulario->agrupaComponentes    ( array($obDtInicial, $obLabel, $obDtFinal ) );
$obFormulario->OK                   ();
$obFormulario->show                 ();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
