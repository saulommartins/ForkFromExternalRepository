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
    * Página de Consulta de Suplementação
    * Data de Criação: 23/05/2005

    * @author Analista: Dieine
    * @author Desenvolvedor: Marcelo B. Paulino

    * @ignore

    $Revision: 31000 $
    $Name$
    $Author: cleisson $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.01.24
*/

/*
$Log$
Revision 1.7  2006/07/05 20:42:23  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoSuplementacao.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ConsultarSuplementacoes";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once($pgJS);

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}
$stFiltro = '';
$arFiltro = Sessao::read('filtro');
foreach ($arFiltro as $stCampo => $stValor) {
    $stFiltro .= $stCampo."=".$stValor."&";
}
$stFiltro .= "pg=".Sessao::read('pg')."&";
$stFiltro .= "pos=".Sessao::read('pos')."&";
$stFiltro .= "stAcao=".$_REQUEST['stAcao'];

$obROrcamentoSuplementacao = new ROrcamentoSuplementacao;

$obROrcamentoSuplementacao->setExercicio       ( Sessao::getExercicio() );
$obROrcamentoSuplementacao->setCodSuplementacao( $_GET['inCodSuplementacao'] );
$obROrcamentoSuplementacao->consultar();

//RECUPERA VALORES DA SUPLEMENTACAO
$stDtSuplementacao   = $obROrcamentoSuplementacao->getDtLancamento();
$stNorma             = $obROrcamentoSuplementacao->obRNorma->getCodNorma()."/".Sessao::getExercicio();
$stTipoSuplementacao = $obROrcamentoSuplementacao->getNomTipo();
$nuValorTotal        = number_format( $obROrcamentoSuplementacao->getVlTotal(), 2, ',', '.' );
$stDtAnulacao        = $obROrcamentoSuplementacao->getDtAnulacao();
if ($stDtAnulacao != "") {
    $stStatus = "Anulada";
} else {
    $stStatus = "Ativa";
}

//MONTA LISTA DE SUPLEMENTACOES SUPLEMENTADAS E REDUZIDAS
$inCount    = 0;
$inCountNeg = 0;

if ($obROrcamentoSuplementacao->getDespesaSuplementada()) {
    foreach ( $obROrcamentoSuplementacao->getDespesaSuplementada() as $obSuplementada ) {
        $arSup[$inCount]['dotacao']  = $obSuplementada->getCodDespesa()." - ".$obSuplementada->getDescricao();
        $arSup[$inCount++]['valor']  = $obSuplementada->getValorOriginal();
        if ( $obSuplementada->getSaldoDotacao() < 0 ) {
            $arNeg[$inCountNeg]['dotacao'] = $obSuplementada->getCodDespesa()." - ".$obSuplementada->getDescricao();
            $arNeg[$inCountNeg++]['valor']   = $obSuplementada->getSaldoDotacao();
        }
    }
    Sessao::write('arSup',$arSup);
}
$inCount = 0;
if ($obROrcamentoSuplementacao->getDespesaReducao() ) {
    foreach ( $obROrcamentoSuplementacao->getDespesaReducao() as $obReduzida ) {
        $arRed[$inCount]['dotacao']  = $obReduzida->getCodDespesa()." - ".$obReduzida->getDescricao();
        $arRed[$inCount++]['valor']  = $obReduzida->getValorOriginal();
        if ( $obReduzida->getSaldoDotacao() < 0 ) {
            $arNeg[$inCountNeg]['dotacao'] = $obReduzida->getCodDespesa()." - ".$obReduzida->getDescricao();
            $arNeg[$inCountNeg++]['valor']   = $obReduzida->getSaldoDotacao();
        }
    }
    Sessao::write('arRed',$arRed);
}
Sessao::write('arNeg',$arNeg);

SistemaLegado::executaFramePrincipal( "buscaDado('montaListaSuplementacoes');" );

/****************************************/
//Define COMPONENTES DO FORMULARIO
/****************************************/

//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgList );
$obForm->setTarget( "telaPrincipal" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao  );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( $stCtrl  );

// Define objeto Hidden para Codigo da Autorizacao
$obHdnCodSuplementacao = new Hidden;
$obHdnCodSuplementacao->setName ( "inCodSuplementacao"        );
$obHdnCodSuplementacao->setValue( $_GET['inCodSuplementacao'] );

// Define objeto Label para Lei/Decreto
$obLblLeiDecreto = new Label;
$obLblLeiDecreto->setRotulo( "Lei/Decreto" );
$obLblLeiDecreto->setValue ( $stNorma      );

//Define objeto label para Data de Suplementação
$obLblDtSuplementacao = new Label;
$obLblDtSuplementacao->setRotulo( "Data da Suplementação" );
$obLblDtSuplementacao->setId    ( "stDtSuplementacao"     );
$obLblDtSuplementacao->setValue ( $stDtSuplementacao      );

//Define objeto label para Tipo de Suplementação
$obLblTipoSuplementacao = new Label;
$obLblTipoSuplementacao->setRotulo( "Tipo de Suplementação" );
$obLblTipoSuplementacao->setId    ( "stTipoSuplementacao"   );
$obLblTipoSuplementacao->setValue ( $stTipoSuplementacao    );

//Define objeto label para Valor Total
$obLblValorTotal = new Label;
$obLblValorTotal->setRotulo( "Valor Total"  );
$obLblValorTotal->setId    ( "nuValorTotal" );
$obLblValorTotal->setValue ( $nuValorTotal  );

//Define objeto label para Status da Suplementação
$obLblStatus = new Label;
$obLblStatus->setRotulo( 'Status da Suplementação' );
$obLblStatus->setId    ( 'stStatus'                );
$obLblStatus->setValue ( $stStatus                 );

//Define objeto label para Data da Anulação
$obLblDtAnulacao = new Label;
$obLblDtAnulacao->setRotulo( "Data de Anulação" );
$obLblDtAnulacao->setId    ( "stDtAnulacao"     );
$obLblDtAnulacao->setValue ( $stDtAnulacao      );

$obSpnListaSuplementacoesSup = new Span;
$obSpnListaSuplementacoesSup->setId ( "spnListaSuplementacoesSup" );

$obSpnListaSuplementacoesRed = new Span;
$obSpnListaSuplementacoesRed->setId ( "spnListaSuplementacoesRed" );

$obSpnListaAnulacoes = new Span;
$obSpnListaAnulacoes->setId ( "spnListaAnulacoes" );

$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );

$obFormulario->addTitulo( "Tela de Consulta de Suplementações" );
$obFormulario->addHidden( $obHdnCtrl                  );
$obFormulario->addHidden( $obHdnAcao                  );
$obFormulario->addHidden( $obHdnCodSuplementacao      );

$obFormulario->addComponente( $obLblLeiDecreto        );
$obFormulario->addComponente( $obLblDtSuplementacao   );
$obFormulario->addComponente( $obLblTipoSuplementacao );
$obFormulario->addComponente( $obLblValorTotal        );
$obFormulario->addComponente( $obLblStatus            );
$obFormulario->addComponente( $obLblDtAnulacao        );

$obFormulario->addSpan  ( $obSpnListaSuplementacoesSup );
$obFormulario->addSpan  ( $obSpnListaSuplementacoesRed );

//$obFormulario->addTitulo( "Dotações Negativas na Anualação da Suplementação" );
$obFormulario->addSpan  ( $obSpnListaAnulacoes         );

$stLocation = $pgList.'?'.Sessao::getId().'&stAcao='.$stAcao.$stFiltro;
$obButtonVoltar = new Button;
$obButtonVoltar->setName  ( "Voltar" );
$obButtonVoltar->setValue ( "Voltar" );
$obButtonVoltar->obEvento->setOnClick("Cancelar('".$stLocation."');");

$obFormulario->defineBarra( array( $obButtonVoltar ), "left", "" );
//$obFormulario->Voltar( $stLocation );
$obFormulario->show();

?>
