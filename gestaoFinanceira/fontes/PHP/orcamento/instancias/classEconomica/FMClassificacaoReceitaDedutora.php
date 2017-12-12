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
    * Página de Formulario de Inclusao/Alteracao de Fornecedores
    * Data de Criação  : 08/10/2007

    * @author Desenvolvedor: Anderson cAko Konze

    $Id: FMClassificacaoReceitaDedutora.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.01.04
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once( CAM_GF_INCLUDE."validaGF.inc.php");
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoClassificacaoReceita.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ClassificacaoReceitaDedutora";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

//include_once ($pgJS);

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

$obROrcamentoClassificacaoReceita = new ROrcamentoClassificacaoReceita;
$obROrcamentoClassificacaoReceita->setDedutora( true );

//Recupera Mascara da Classificao de Receita
$mascClassificacao = $obROrcamentoClassificacaoReceita->recuperaMascara();

if ($stAcao == 'alterar') {
    $inCodClassificacao = $_GET['stMascClassReceita'];
    $stDescricao        = $_GET['stDescricao'];
    $inCodNorma         = $_GET['inCodNorma'];
}

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnCodConta = new Hidden;
$obHdnCodConta->setName ( "inCodConta" );
$obHdnCodConta->setValue( $_REQUEST['inCodConta'] );

$obHdnCodReceita = new Hidden;
$obHdnCodReceita->setName ( "inCodClassificacao" );
$obHdnCodReceita->setValue( $inCodClassificacao );

$obHdnMascClassificacao = new Hidden;
$obHdnMascClassificacao->setName ( "stMascClassificacao" );
$obHdnMascClassificacao->setValue( $mascClassificacao );

$obTxtCodReceita = new TextBox;
$obTxtCodReceita->setName     ( "inCodClassificacao" );
$obTxtCodReceita->setValue    ( $inCodClassificacao );
$obTxtCodReceita->setRotulo   ( "Código" );
$obTxtCodReceita->setSize     ( strlen($mascClassificacao) );
$obTxtCodReceita->setMaxLength( strlen($mascClassificacao) );
$obTxtCodReceita->setNull     ( false );
$obTxtCodReceita->setTitle    ( "Código da Classificação de Receita" );
$obTxtCodReceita->obEvento->setOnKeyUp("mascaraDinamico('".$mascClassificacao."', this, event);");
$obTxtCodReceita->obEvento->setOnChange("frm.Ok.disabled = true;buscaValor('mascaraClassificacao','".$pgOcul."','".$pgProc."','oculto','".Sessao::getId()."')");

$obLblCodReceita = new Label;
$obLblCodReceita->setRotulo( "Código" );
$obLblCodReceita->setValue( $inCodClassificacao );

$obTxtDescReceita = new TextBox;
$obTxtDescReceita->setName     ( "stDescricao" );
$obTxtDescReceita->setValue    ( $stDescricao );
$obTxtDescReceita->setRotulo   ( "Descrição" );
$obTxtDescReceita->setSize     ( 80 );
$obTxtDescReceita->setMaxLength( 80 );
$obTxtDescReceita->setNull     ( false );
$obTxtDescReceita->setTitle    ( "Descrição da classificação de receita" );

//Define o objeto TEXT e COMBO para armezenar a BASE LEGAL (norma)
$obTxtNorma = new TextBox;
$obTxtNorma->setName      ( "inCodNorma" );
$obTxtNorma->setInteiro   ( false );
$obTxtNorma->setRotulo    ( "Base Legal" );
$obTxtNorma->setTitle     ( "Selecione a base legal." );
$obTxtNorma->setSize      ( 10 );
$obTxtNorma->setMaxLength ( 10 );
$obTxtNorma->setValue     ( $inCodNorma );
$obTxtNorma->setNull      ( false );

$obROrcamentoClassificacaoReceita->obRNorma->listar( $rsNorma );
$obCmbNorma = new Select;
$obCmbNorma->setName      ( "stNomNorma" );
$obCmbNorma->setRotulo    ( "Base Legal" );
$obCmbNorma->setStyle     ( "width: 250px" );
$obCmbNorma->addOption    ( "", "Selecione" );
$obCmbNorma->setCampoId   ( "cod_norma" );
$obCmbNorma->setCampoDesc ( "nom_norma" );
$obCmbNorma->setValue     ( $inCodNorma );
$obCmbNorma->preencheCombo( $rsNorma );
$obCmbNorma->setNull      ( false );
$obCmbNorma->setTitle     ( 'Norma que cria o concurso' );

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->setAjuda ( "UC-02.01.04"                       );

$obFormulario->addHidden( $obHdnCtrl                          );
$obFormulario->addHidden( $obHdnAcao                          );
$obFormulario->addHidden( $obHdnMascClassificacao             );
$obFormulario->addHidden( $obHdnCodConta                      );

$obFormulario->addTitulo( "Dados para Classificação de Receita Dedutora" );
if ($stAcao == 'alterar') {
    $obFormulario->addHidden( $obHdnCodReceita                );
    $obFormulario->addComponente( $obLblCodReceita            );
} else {
    $obFormulario->addComponente( $obTxtCodReceita            );
}
$obFormulario->addComponente( $obTxtDescReceita               );
$obFormulario->addComponenteComposto( $obTxtNorma,$obCmbNorma );

//Define os botões de ação do formulário
$obBtnOK = new OK;

$obBtnLimpar = new Button;
$obBtnLimpar->setName( "btnLimpar" );
$obBtnLimpar->setValue( "Limpar" );
$obBtnLimpar->obEvento->setOnClick ( "document.frm.reset();" );

$stLocation = $pgList.'?'.Sessao::getId().'&stAcao='.$stAcao;

$arBtn = array();
$arBtn[] = $obBtnOK;
$arBtn[] = $obBtnLimpar;
if ($stAcao=='alterar') {
    $obFormulario->Cancelar($stLocation);
} else {
$obFormulario->defineBarra( $arBtn );
}

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
