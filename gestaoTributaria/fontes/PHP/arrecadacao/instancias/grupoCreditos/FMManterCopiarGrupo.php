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


include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRGrupoCredito.class.php"   );

//Define o nome dos arquivos PHP
$stPrograma = "ManterCopiarGrupo";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php?".Sessao::getId();
$pgJS   = "JS".$stPrograma.".js";

//include_once( $pgJS );

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "definir";
}

Sessao::write( "link", "" );

$obTARRGrupoCredito = new TARRGrupoCredito;
$obTARRGrupoCredito->recuperaListaExercicio($rsListaExercicios);

$stOnChange = "ajaxJavaScript('".$pgOcul."&inExercicio='+this.value,'preencheGrupo');";

$obCmbExercicioOrigem = new Select;
$obCmbExercicioOrigem->setRotulo       ( "Exercício de Origem" );
$obCmbExercicioOrigem->setTitle        ( "Informe o exercício de origem." );
$obCmbExercicioOrigem->setName         ( "cmbExercicio" );
$obCmbExercicioOrigem->addOption       ( "", "Selecione" );
$obCmbExercicioOrigem->setCampoId      ( "ano_exercicio" );
$obCmbExercicioOrigem->setCampoDesc    ( "ano_exercicio" );
$obCmbExercicioOrigem->preencheCombo   ( $rsListaExercicios );
$obCmbExercicioOrigem->setNULL         ( false );
$obCmbExercicioOrigem->obEvento->setOnChange( $stOnChange );

$rsListaGrupos = new RecordSet;
$obCmbGrupo = new Select;
$obCmbGrupo->setRotulo       ( "Grupo" );
$obCmbGrupo->setTitle        ( "Informe o grupo a ser convertido." );
$obCmbGrupo->setName         ( "cmbGrupos" );
$obCmbGrupo->addOption       ( "", "Selecione" );
$obCmbGrupo->setCampoId      ( "cod_grupo" );
$obCmbGrupo->setCampoDesc    ( "[cod_grupo] - [descricao]" );
$obCmbGrupo->preencheCombo   ( $rsListaGrupos );
$obCmbGrupo->setNULL         ( false );

$obTxtNovoExercicio = new Exercicio;
$obTxtNovoExercicio->setRotulo  ( 'Exercício de Destino');
$obTxtNovoExercicio->setTitle   ( 'Informe o exercício de destino.');
$obTxtNovoExercicio->setName    ( 'inNovoExercicio');
$obTxtNovoExercicio->setValue   ( '' );
$obTxtNovoExercicio->setNull    ( false );

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction ( $pgProc );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue ( $stCtrl );

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );

$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addTitulo( "Dados para Recadastramento de Tabelas" );
$obFormulario->addComponente ( $obCmbExercicioOrigem );
$obFormulario->addComponente ( $obCmbGrupo );
$obFormulario->addComponente ( $obTxtNovoExercicio );

$obSpnListaPermissao = new Span;
$obSpnListaPermissao->setID("spnErro");
$obFormulario->addSpan       ( $obSpnListaPermissao );

$obFormulario->ok();
$obFormulario->show();

?>
