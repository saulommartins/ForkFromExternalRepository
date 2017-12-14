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
    * Página de Formulário de Adiantamentos/Subvenções
    * Data de Criação : 12/10/2006

    * @author Analista:
    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @ignore

    $Id: FLManterAdiantamentosSubvencoes.php 59612 2014-09-02 12:00:51Z gelson $

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2007-08-10 10:27:08 -0300 (Sex, 10 Ago 2007) $

    * Casos de uso: uc-02.03.31
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_ORC_COMPONENTES.'ISelectMultiploEntidadeUsuario.class.php'                    );
include_once ( CAM_GA_CGM_COMPONENTES."IPopUpCGMVinculado.class.php"                                );
//Define o nome dos arquivos PHP
$stPrograma = "ManterAdiantamentosSubvencoes";

$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js";

if ( empty( $_REQUEST['stAcao'] ) ) {
    $stAcao = "incluir";
} else {
    $stAcao = $request->get('stAcao');
}

$obForm = new Form;
$obForm->setAction ( $pgList         );
$obForm->setTarget ( "telaPrincipal" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao"   );
$obHdnAcao->setValue( $stAcao   );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl"   );
$obHdnCtrl->setValue( ""        );

$obSelMultEntidadeUsuario = new ISelectMultiploEntidadeUsuario;

$obExercicio = new Exercicio();
$obExercicio->setTitle( "Informe o exercício." );
$obPeriodicidade = new Periodicidade();
$obPeriodicidade->setExercicio( Sessao::getExercicio() );
$obPeriodicidade->setValidaExercicio( true );

//Define o objeto TEXT para Codigo do Empenho Inicial
$obTxtCodEmpenhoInicial = new TextBox;
$obTxtCodEmpenhoInicial->setName     ( "inCodEmpenhoInicial" );
$obTxtCodEmpenhoInicial->setRotulo   ( "Número do Empenho"   );
$obTxtCodEmpenhoInicial->setTitle    ( "Informe o número do empenho." );
$obTxtCodEmpenhoInicial->setInteiro  ( true                  );
$obTxtCodEmpenhoInicial->setNull     ( true                  );

//Define objeto Label
$obLblEmpenho = new Label;
$obLblEmpenho->setValue( "a" );

//Define o objeto TEXT para Codigo do Empenho Final
$obTxtCodEmpenhoFinal = new TextBox;
$obTxtCodEmpenhoFinal->setName     ( "inCodEmpenhoFinal" );
$obTxtCodEmpenhoFinal->setRotulo   ( "Número do Empenho" );
$obTxtCodEmpenhoFinal->setTitle    ( "Informe o número do empenho." );
$obTxtCodEmpenhoFinal->setInteiro  ( true                );
$obTxtCodEmpenhoFinal->setNull     ( true                );

$obBscCredor = new IPopUpCGMVinculado( $obForm );
$obBscCredor->setTabelaVinculo       ( 'empenho.responsavel_adiantamento' );
$obBscCredor->setCampoVinculo        ( 'numcgm' );
$obBscCredor->setNomeVinculo         ( 'Credor' );
$obBscCredor->setRotulo              ( 'Credor' );
$obBscCredor->setName                ( 'stNomCredor');
$obBscCredor->setId                  ( 'stNomCredor');
$obBscCredor->setTitle               ( 'Informe o credor.');
$obBscCredor->obCampoCod->setName    ( "inCodFornecedor"   );
$obBscCredor->obCampoCod->setId      ( "inCodFornecedor"   );
$obBscCredor->obCampoCod->setNull    ( true               );
$obBscCredor->setNull                ( true               );

$obFormulario = new Formulario();
$obFormulario->addForm( $obForm );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addTitulo( 'Dados para Filtro para Prestação de Contas de Adiantamentos/Subvenções' );
$obFormulario->addComponente( $obSelMultEntidadeUsuario );
$obFormulario->addComponente( $obExercicio );
$obFormulario->addComponente( $obPeriodicidade );
$obFormulario->agrupaComponentes( array( $obTxtCodEmpenhoInicial, $obLblEmpenho, $obTxtCodEmpenhoFinal ) );
$obFormulario->addComponente( $obBscCredor );
$obFormulario->Ok();
$obFormulario->Show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
