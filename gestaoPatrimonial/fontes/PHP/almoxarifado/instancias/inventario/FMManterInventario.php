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
    * Página de Formulario de Manter Inventario
    * Data de Criação: 01/10/2007

    * @author Analista: Anelise Schwengber
    * @author Desenvolvedor: Andre Almeida

    * @ignore

    $Id:$

    * Casos de uso: uc-03.03.15
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterInventario";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";
$pgList     = "LS".$stPrograma.".php";

$stAcao = $request->get('stAcao');

Sessao::write('inventario',array());

include($pgJs);

$stParametros = "&stExercicio=".$_REQUEST['stExercicio']."&inCodInventario=".$_REQUEST['inCodInventario']."&inCodAlmoxarifado=".$_REQUEST['inCodAlmoxarifado']."&stAcao=".$request->get('stAcao')."";

$jsOnLoad = "executaFuncaoAjax( 'operacoesIniciais', '".$stParametros."' );";

$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setId( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obLblExercicio = new Label();
$obLblExercicio->setName('stExercicio');
$obLblExercicio->setRotulo('Exercício');

if ($stAcao == 'incluir') {
    $obLblExercicio->setValue( Sessao::getExercicio() );
} else {
    $obLblExercicio->setValue( $_REQUEST['stExercicio'] );
}

$obSpnAlmoxarifadoCatalogo = new Span();
$obSpnAlmoxarifadoCatalogo->setId ( "spnAlmoxarifadoCatalogo" );
if ($stAcao == "anular") {
    $obTxtAreaMotivo = new TextArea();
    $obTxtAreaMotivo->setName('stMotivo');
    $obTxtAreaMotivo->setId('stMotivo');
    $obTxtAreaMotivo->setRotulo('Motivo');
    $obTxtAreaMotivo->setTitle('Informe o motivo para anulação.');
}

if ($stAcao == "incluir" or $stAcao == "alterar") {
    $obSpnDadosClassificacao = new Span();
    $obSpnDadosClassificacao->setId ( "spnDadosClassificacao" );

    $obSpnClassificacaoBloqueada = new Span();
    $obSpnClassificacaoBloqueada->setId ( "spnDetalhesClassificacaoBloqueada" );
}

$obSpnListaClassificacoesBloqueadas = new Span();
$obSpnListaClassificacoesBloqueadas->setId("spnListaClassificacoesBloqueadas");

if ($stAcao != "incluir" or $stAcao != "alterar") {
    $obSpnListaItensInventariados = new Span();
    $obSpnListaItensInventariados->setId ( "spnListaItensInventariados" );
}

$obBtnOk = new Ok;
$obBtnOk->setName ( "btnOk" );
$obBtnOk->setValue( "Ok" );

$obBtnLimparTela = new Button;
$obBtnLimparTela->setName ( "btnLimparTela" );
$obBtnLimparTela->setValue( "Limpar" );
$obBtnLimparTela->setTipo ( "button" );
$obBtnLimparTela->obEvento->setOnClick ( "executaFuncaoAjax('limpartela', '&stAcao=".$_REQUEST['stAcao']."');" );

$stProxPage = $pgList."?".Sessao::getId()."&stAcao=".$_REQUEST['stAcao'];

$obBtnCancelar = new Button;
$obBtnCancelar->setName  ( "btnCancelar" );
$obBtnCancelar->setValue ( "Cancelar" );
$obBtnCancelar->setTipo  ( "button" );
$obBtnCancelar->obEvento->setOnClick( "Cancelar('".$stProxPage."');" );

$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addTitulo( 'Dados do Inventário' );
$obFormulario->addComponente( $obLblExercicio );
$obFormulario->addSpan( $obSpnAlmoxarifadoCatalogo );
if ($stAcao == "anular") {
    $obFormulario->addComponente( $obTxtAreaMotivo );
}
if ($stAcao == "incluir" or $stAcao == "alterar") {
    $obFormulario->addSpan( $obSpnDadosClassificacao );
    $obFormulario->addSpan( $obSpnClassificacaoBloqueada );
}
$obFormulario->addSpan( $obSpnListaClassificacoesBloqueadas );

if ($stAcao != "incluir" or $stAcao != "alterar") {
    $obFormulario->addSpan( $obSpnListaItensInventariados );
}

if ($_REQUEST['stAcao'] == 'incluir') {
    $obFormulario->defineBarra( array($obBtnOk, $obBtnLimparTela), "left", "<b>*Campo obrigatório.</b>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;" );
} else {
    $obFormulario->defineBarra( array($obBtnOk, $obBtnCancelar), "left", "<b>*Campo obrigatório.</b>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;" );
}

$obFormulario->show();

if ($stAcao == "processar") {
    $stJs = "jQuery('#Ok').removeAttr('disabled');";
}

if ($stJs)
    SistemaLegado::executaFrameOculto($stJs);

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
