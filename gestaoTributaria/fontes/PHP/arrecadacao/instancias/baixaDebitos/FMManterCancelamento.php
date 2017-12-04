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
    * Página de Formulario para Definicao de Permissao para Cancelamentos

    * Data de Criação   : 26/07/2007

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato
    * @ignore

    * $Id: FMManterCancelamento.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.03.10

*/

/*
$Log$
Revision 1.1  2007/07/27 13:16:25  cercato
Bug#9762#

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GA_CGM_COMPONENTES."IPopUpCGM.class.php" );

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) || $stAcao == "incluir" ) {
    $stAcao = "inscrever";
}

//Define o nome dos arquivos PHP
$stPrograma    = "ManterCancelamento";
$pgForm        = "FM".$stPrograma.".php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJs          = "JS".$stPrograma.".js";

include_once( $pgJs );

Sessao::write( 'link', "" );

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $stAcao  );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName   ( "stCtrl" );
$obHdnCtrl->setValue  ( $stCtrl  );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction ( $pgProc );
$obForm->settarget ( "oculto" );

$obBtnIncluirCGM = new Button;
$obBtnIncluirCGM->setName              ( "btnIncluirCredito" );
$obBtnIncluirCGM->setValue             ( "Incluir" );
$obBtnIncluirCGM->setTipo              ( "button" );
$obBtnIncluirCGM->obEvento->setOnClick ( "buscaValor('IncluirCGM');" );
$obBtnIncluirCGM->setDisabled          ( false );

$obBtnLimparCGM = new Button;
$obBtnLimparCGM->setName               ( "btnLimparCredito" );
$obBtnLimparCGM->setValue              ( "Limpar" );
$obBtnLimparCGM->setTipo               ( "button" );
$obBtnLimparCGM->obEvento->setOnClick  ( "buscaValor('limparCGM');" );
$obBtnLimparCGM->setDisabled           ( false );

$botoesCredito = array ( $obBtnIncluirCGM , $obBtnLimparCGM );

$obSpnListaCGM = new Span;
$obSpnListaCGM->setID("spnListaCGM");

$obPopUpCGM = new IPopUpCGM( $obForm );
$obPopUpCGM->setNull ( true );
$obPopUpCGM->setRotulo ( "*CGM" );
$obPopUpCGM->setTitle ( "Informe o número do CGM." );

$obBtnOk = new Ok;

$obBtnLimpar = new Limpar;
$obBtnLimpar->obEvento->setOnClick  ( "buscaValor('limparForm');" );

$botoes = array ( $obBtnOk , $obBtnLimpar );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm );
$obFormulario->setAjuda      ( "UC-05.03.10" );
$obFormulario->addHidden     ( $obHdnAcao );
$obFormulario->addHidden     ( $obHdnCtrl );
$obFormulario->addTitulo     ("Dados para Filtro");
$obFormulario->addComponente ( $obPopUpCGM );
$obFormulario->addSpan       ( $obSpnListaCGM );
$obFormulario->defineBarra   ( $botoesCredito, "left", "" );
$obFormulario->defineBarra   ( $botoes );
$obFormulario->show();

Sessao::write( 'listaCGM', array() );

$stJs = 'f.inCGM.focus();';
$stJs .= "buscaValor('MostraLista');";

sistemaLegado::executaFrameOculto ( $stJs );
