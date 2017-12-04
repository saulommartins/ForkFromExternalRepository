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
    * Página de Formulario de Filtro para relatorio de Divida Ativa

    * Data de Criação   : 19/04/2007

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato
    * @ignore

    * $Id: FMRelatorioDevedores.php 60340 2014-10-14 19:43:20Z lisiane $

    *Casos de uso: uc-05.04.10

*/

/*
$Log$
Revision 1.1  2007/04/19 16:06:56  cercato
*** empty log message ***

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_ARR_COMPONENTES."MontaGrupoCredito.class.php" );
include_once ( CAM_GT_MON_NEGOCIO."RMONCredito.class.php" );
include_once ( CAM_GT_ARR_NEGOCIO."RARRConfiguracao.class.php" );

if ( empty( $_REQUEST['stAcao'] ) || $_REQUEST['stAcao'] == "incluir" ) {
    $_REQUEST['stAcao'] = "inscrever";
}

//Define o nome dos arquivos PHP
$stPrograma      = 'RelatorioDevedores';
$pgFilt          = 'FL'.$stPrograma.'.php';
$pgList          = 'LS'.$stPrograma.'.php';
$pgForm          = 'FM'.$stPrograma.'.php';
$pgProc          = 'PR'.$stPrograma.'.php';
$pgOcul          = 'OC'.$stPrograma.'.php';
$pgGera          = 'OCGera'.$stPrograma.'.php';
$pgJs            = 'JS'.$stPrograma.'.js';
include_once( $pgJs );

Sessao::remove( "arListaCredito" );
Sessao::remove( "arListaGrupoCredito" );

$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $_REQUEST['stAcao']  );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName   ( "stCtrl" );
$obHdnCtrl->setValue  ( '' );

$obHdnFiltro =  new Hidden;
$obHdnFiltro->setName   ( "stFiltro" );
$obHdnFiltro->setValue  ( $_REQUEST['stFiltro']  );


if($_REQUEST['stFiltro'] == 'credito') {
    $obRMONCredito = new RMONCredito;
    $obRMONCredito->consultarMascaraCredito();
    $stMascaraCredito = $obRMONCredito->getMascaraCredito();
    $obRARRConfiguracao = new RARRConfiguracao;
    $obRARRConfiguracao->setAnoExercicio ( Sessao::getExercicio() );
    $obRARRConfiguracao->consultarMascaraProcesso( $stMascaraProcesso );
 
    //DEFINICAO DOS COMPONENTES
    $obTxtExercicio = new TextBox;
    $obTxtExercicio->setName      ( "stExercicio" );
    $obTxtExercicio->setId        ( "stExercicio" );
    $obTxtExercicio->setRotulo    ( "Exercício" );
    $obTxtExercicio->setTitle     ( "Informe o exercício para filtro" );
    $obTxtExercicio->setSize      ( 4 );
    $obTxtExercicio->setMaxLength ( 4 );
    $obTxtExercicio->setNull      ( true );
   
    $obBscCredito = new BuscaInner;
    $obBscCredito->setRotulo                ( "Crédito"                     );
    $obBscCredito->setTitle                 ( "Crédito que será calculado." );
    $obBscCredito->setId                    ( "stCredito"                   );
    $obBscCredito->obCampoCod->setName      ( "inCodCredito"                );
    $obBscCredito->obCampoCod->setId        ( "inCodCredito"                );
    $obBscCredito->obCampoCod->setMaxLength ( strlen($stMascaraCredito)     );
    $obBscCredito->obCampoCod->setMinLength ( strlen($stMascaraCredito)     );
    $obBscCredito->obCampoCod->setMascara   ( $stMascaraCredito             );
    $obBscCredito->obCampoCod->obEvento->setOnChange("buscaValor('buscaCredito');");
    $obBscCredito->obCampoCod->obEvento->setOnBlur("validarCredito(this);");
    $obBscCredito->setFuncaoBusca( "abrePopUp('".CAM_GT_MON_POPUPS."credito/FLProcurarCredito.php','frm','inCodCredito','stCredito','todos','".Sessao::getId()."','800','550');" );
    
    $obBtnIncluirCredito = new Button;
    $obBtnIncluirCredito->setName              ( "btnIncluirCredito" );
    $obBtnIncluirCredito->setValue             ( "Incluir" );
    $obBtnIncluirCredito->setTipo              ( "button" );
    $obBtnIncluirCredito->obEvento->setOnClick ( "montaParametrosGET('incluirCredito', 'inCodCredito,stExercicio', true);" );
    $obBtnIncluirCredito->setDisabled          ( false );
    
    $obBtnLimparCredito = new Button;
    $obBtnLimparCredito->setName               ( "btnLimparCredito" );
    $obBtnLimparCredito->setValue              ( "Limpar" );
    $obBtnLimparCredito->setTipo               ( "button" );
    $obBtnLimparCredito->obEvento->setOnClick  ( "montaParametrosGET('limpaCredito');" );
    $obBtnLimparCredito->setDisabled           ( false );
    $botoesCredito = array ( $obBtnIncluirCredito, $obBtnLimparCredito );

    $obSpnListaCreditos = new Span;
    $obSpnListaCreditos->setID("spnListaCreditos");
    
} else {
    //DEFINICAO DOS COMPONENTES
    $obIPopUpGrupoCredito = new MontaGrupoCredito;
    $obIPopUpGrupoCredito->setRotulo ( "Grupo de Crédito" );
    $obIPopUpGrupoCredito->setTitulo ( "Informe o código do grupo de crédito." );
    
    $obBtnIncluirGrupoCredito = new Button;
    $obBtnIncluirGrupoCredito->setName              ( "btnIncluirGrupoCredito" );
    $obBtnIncluirGrupoCredito->setValue             ( "Incluir" );
    $obBtnIncluirGrupoCredito->setTipo              ( "button" );
    $obBtnIncluirGrupoCredito->obEvento->setOnClick ( "montaParametrosGET('incluirGrupoCredito', 'inCodGrupo', true);" );
    $obBtnIncluirGrupoCredito->setDisabled          ( false );
    
    $obBtnLimparGrupoCredito = new Button;
    $obBtnLimparGrupoCredito->setName               ( "btnLimparGrupoCredito" );
    $obBtnLimparGrupoCredito->setValue              ( "Limpar" );
    $obBtnLimparGrupoCredito->setTipo               ( "button" );
    $obBtnLimparGrupoCredito->obEvento->setOnClick  ( "montaParametrosGET('limpaGrupoCredito');" );
    $obBtnLimparGrupoCredito->setDisabled           ( false );
    $botoesGrupoCredito = array ( $obBtnIncluirGrupoCredito, $obBtnLimparGrupoCredito );
    
    $obSpnListaGrupos = new Span;
    $obSpnListaGrupos->setID("spnListaGrupos");
}

//DEFINICAO DOS COMPONENTES
$obCmbLimite = new Select;
$obCmbLimite->setName      ( "inLimite" );
$obCmbLimite->setRotulo    ( "N° de registros devedores" );
$obCmbLimite->setTitle     ( "Informe o número máximo de registros por Crédito/Grupo selecionado" );
$obCmbLimite->setOptions   ( array('0' => 'Todos',
                                  '10' => '10',
                                  '20' => '20',
                                  '30' => '30',
                                  '40' => '40',
                                  '50' => '50'));
$obCmbLimite->setValue     ( '0' );
$obCmbLimite->setNull      ( true );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( "PRRelatorioDevedores.php" );
$obForm->setTarget( "oculto" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm  ( $obForm );
$obFormulario->setAjuda ( "UC-05.04.10" );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnFiltro );

if($_REQUEST['stFiltro'] == 'credito') {
    $obFormulario->addTitulo("Dados para Filtro | Crédito");
    $obFormulario->addComponente( $obBscCredito );
    $obFormulario->addComponente($obTxtExercicio);
    
    $obFormulario->defineBarra ( $botoesCredito, 'left', '' );
    $obFormulario->addSpan( $obSpnListaCreditos );
    
    $obFormulario->addTitulo("Limite de registros Devedores");
    $obFormulario->addComponente($obCmbLimite);
} else {
    $obFormulario->addTitulo("Dados para Filtro | Grupos de Crédito");
    
    $obIPopUpGrupoCredito->geraFormulario ( $obFormulario, true, true );
    $obFormulario->defineBarra ( $botoesGrupoCredito, 'left', '' );
    $obFormulario->addSpan( $obSpnListaGrupos );
    
    $obFormulario->addTitulo("Limite de registros Devedores");
    $obFormulario->addComponente($obCmbLimite);
}

$obBtnOK = new Ok();
$obBtnOK->obEvento->setOnClick( "BloqueiaFrames(true, false); Salvar();" );

$onBtnLimpar = new Limpar;
$onBtnLimpar->obEvento->setOnClick( "Limpar();" );

$obFormulario->defineBarra( array( $obBtnOK , $onBtnLimpar ) );
$obFormulario->show();