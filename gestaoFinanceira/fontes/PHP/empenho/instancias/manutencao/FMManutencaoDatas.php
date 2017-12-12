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
    * Página de Alteração das datas
    * Data de Criação  : 08/06/2005

    * @author Analista: Diego B Victória
    * @author Desenvolvedor: Cleisson Barboza

    * @ignore

    $Revision: 30805 $
    $Name$
    $Autor: $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.03.16
*/

/*
$Log$
Revision 1.4  2006/07/05 20:48:49  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_EMP_NEGOCIO."REmpenhoEmpenho.class.php"     );
include_once ( CAM_GF_EMP_NEGOCIO."REmpenhoManutencaoDatas.class.php"     );

//Define o nome dos arquivos PHP
$stPrograma = "ManutencaoDatas";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

Sessao::write('inCodEntidade', $_REQUEST['inCodEntidade']);
Sessao::write('inCodEmpenho', $_REQUEST['inCodEmpenho']);
Sessao::write('stExercicioEmpenho', $_REQUEST['stExercicioEmpenho']);

$obREmpenhoEmpenho = new REmpenhoEmpenho;
$obREmpenhoEmpenho->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST['inCodEntidade'] );
$obREmpenhoEmpenho->obROrcamentoEntidade->setExercicio( $_REQUEST['stExercicioEmpenho'] );
$obErro = $obREmpenhoEmpenho->obROrcamentoEntidade->consultar($rsLista);
if ( !$obErro->ocorreu() ) {
    $stNomEntidade  = $obREmpenhoEmpenho->obROrcamentoEntidade->obRCGM->getNomCGM();
}
SistemaLegado::executaIFrameOculto("buscaDado('montaListas');         ");

$stFiltro = '';
if ( Sessao::read('filtro') ) {
    $arFiltro = Sessao::read('filtro');
    $stFiltro = '';
    foreach ($arFiltro as $stCampo => $stValor) {
        $stFiltro .= "&".$stCampo."=".@urlencode( $stValor );
    }
    $stFiltro .= '&pg='.Sessao::read('pg').'&pos='.Sessao::read('pos').'&paginando'.Sessao::read('paginando');
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
$obHdnAcao->setValue( $stAcao  );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( $stCtrl  );

// Define objeto Hidden para Codigo da Autorizacao
$obHdnCodEmpenho = new Hidden;
$obHdnCodEmpenho->setName ( "inCodEmpenho" );
$obHdnCodEmpenho->setValue( $_REQUEST['inCodEmpenho'] );

$obHdnExercicio = new Hidden;
$obHdnExercicio->setName ( "stExercicio" );
$obHdnExercicio->setValue( $_REQUEST['stExercicioEmpenho'] );

// Define objeto Hidden para Codigo da Entidade
$obHdnCodEntidade = new Hidden;
$obHdnCodEntidade->setName ( "inCodEntidade" );
$obHdnCodEntidade->setValue( $_REQUEST['inCodEntidade'] );
$obHdnCodEntidade->setId   ( "inCodEntidade" );

// Define objeto Label para Exercicio
$obLblExercicio = new Label;
$obLblExercicio->setRotulo( "Exercicio" );
$obLblExercicio->setValue ( $_REQUEST['stExercicioEmpenho'] );

//Define objeto label entidade
$obLblEntidade = new Label;
$obLblEntidade->setRotulo( "Entidade"                             );
$obLblEntidade->setId    ( "stNomeEntidade"                        );
$obLblEntidade->setValue ( $_REQUEST['inCodEntidade'].' - '.$stNomEntidade     );

if ($_REQUEST['inCodAutorizacao']) {
    $obLblAutorizacao = new Label;
    $obLblAutorizacao->setRotulo( "Autorização"       );
    $obLblAutorizacao->setId    ( "inCodAutorizacao" );
    $obLblAutorizacao->setValue ( $_REQUEST['inCodAutorizacao'] );

    // Define objeto Data para Data de Autorização
    $obDtAutorizacao = new Data;
    $obDtAutorizacao->setName     ( "stDtAutorizacao" );
    $obDtAutorizacao->setRotulo   ( "Data Autorização" );
    $obDtAutorizacao->setTitle    ( '' );
    $obDtAutorizacao->setNull     ( false );
    $obDtAutorizacao->setValue    ( $_REQUEST['stDtAutorizacao'] );
}
// Define objeto Label para Empenho
$obLblEmpenho = new Label;
$obLblEmpenho->setRotulo( "Empenho" );
$obLblEmpenho->setValue ( $_REQUEST['inCodEmpenho'] );

// Define objeto Data para Data de Empenho
$obDtEmpenho = new Data;
$obDtEmpenho->setName     ( "stDtEmpenho" );
$obDtEmpenho->setRotulo   ( "Data Empenho" );
$obDtEmpenho->setTitle    ( '' );
$obDtEmpenho->setNull     ( false );
$obDtEmpenho->setValue    ( $_REQUEST['stDtEmpenho'] );

$obSpnListaAnulacaoEmpenho = new Span;
$obSpnListaAnulacaoEmpenho->setId ( "spnListaAnulacaoEmpenho" );

$obSpnListaLiquidacao = new Span;
$obSpnListaLiquidacao-> setId   ( "spnListaLiquidacao" );

$obSpnListaAnulacaoLiquidacao = new Span;
$obSpnListaAnulacaoLiquidacao-> setId   ( "spnListaAnulacaoLiquidacao" );

$obSpnListaOrdemPagamento = new Span;
$obSpnListaOrdemPagamento-> setId   ( "spnListaOrdemPagamento" );

$obSpnListaAnulacaoOrdemPagamento = new Span;
$obSpnListaAnulacaoOrdemPagamento-> setId   ( "spnListaAnulacaoOrdemPagamento" );

$obSpnListaPagamento = new Span;
$obSpnListaPagamento-> setId   ( "spnListaPagamento" );

$obSpnListaAnulacaoPagamento = new Span;
$obSpnListaAnulacaoPagamento-> setId   ( "spnListaAnulacaoPagamento" );

$stLocation = $pgList.'?'.Sessao::getId().'&stAcao='.$stAcao.$stFiltro;

$obButtonCancelar = new Button;
$obButtonCancelar->setName  ( "Cancelar" );
$obButtonCancelar->setValue ( "Cancelar" );
$obButtonCancelar->obEvento->setOnClick("window.location='$stLocation';");

$obBtnOK = new Ok;
$botoesForm     = array ( $obBtnOK , $obButtonCancelar );

$obFormulario = new Formulario;
$obFormulario->addForm( $obForm                    );
$obFormulario->addTitulo( "Dados do empenho"       );
$obFormulario->addHidden( $obHdnCtrl               );
$obFormulario->addHidden( $obHdnAcao               );
$obFormulario->addHidden( $obHdnExercicio          );
$obFormulario->addHidden( $obHdnCodEmpenho         );
$obFormulario->addHidden( $obHdnCodEntidade        );

$obFormulario->addComponente( $obLblExercicio      );
$obFormulario->addComponente( $obLblEntidade       );
/*
if ($_REQUEST['inCodAutorizacao']) {
    $obFormulario->addComponente( $obLblAutorizacao    );
    $obFormulario->addComponente( $obDtAutorizacao     );
}
*/
$obFormulario->addComponente( $obLblEmpenho        );
$obFormulario->addComponente( $obDtEmpenho         );

$obFormulario->addTitulo( "Dados de Anulação de Empenho"    );
$obFormulario->addSpan  ( $obSpnListaAnulacaoEmpenho        );

$obFormulario->addTitulo( "Dados de Liquidacao"             );
$obFormulario->addSpan  ( $obSpnListaLiquidacao             );

$obFormulario->addTitulo( "Dados de Anulação da Liquidação" );
$obFormulario->addSpan  ( $obSpnListaAnulacaoLiquidacao     );

$obFormulario->addTitulo( "Dados de Ordem de Pagamento"     );
$obFormulario->addSpan  ( $obSpnListaOrdemPagamento         );

$obFormulario->addTitulo( "Dados de Anulação da Ordem de Pagamento" );
$obFormulario->addSpan  ( $obSpnListaAnulacaoOrdemPagamento         );

$obFormulario->addTitulo( "Dados de Pagamento" );
$obFormulario->addSpan  ( $obSpnListaPagamento         );

$obFormulario->addTitulo( "Dados de Anulação de Pagamento" );
$obFormulario->addSpan  ( $obSpnListaAnulacaoPagamento         );

$obFormulario->addIFrameOculto("oculto");
//$obFormulario->obIFrame->setWidth("100%");
//$obFormulario->obIFrame->setHeight("100");

$obFormulario->defineBarra( $botoesForm );
//$obFormulario->Voltar( $stLocation );
$obFormulario->show();

$obIFrame = new IFrame;
$obIFrame->setName("telaMensagem");
$obIFrame->setWidth("100%");
$obIFrame->setHeight("65");
$obIFrame->show();

include_once($pgJS);

?>
