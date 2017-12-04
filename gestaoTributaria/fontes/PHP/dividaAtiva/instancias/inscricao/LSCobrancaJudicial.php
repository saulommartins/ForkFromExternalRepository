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
  * Página de Lista de Cobranca Judicial
  * Data de criação : 11/09/2007

  * @author Analista: Fábio Bertoldi
  * @author Programador: Fernando Piccini Cercato

    * $Id: LSCobrancaJudicial.php 59612 2014-09-02 12:00:51Z gelson $

  Caso de uso: uc-05.04.02
**/

/*
$Log$
Revision 1.1  2007/09/11 20:44:13  cercato
*** empty log message ***

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaAtiva.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "CobrancaJudicial";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once ( $pgJS );

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
if ( empty( $_REQUEST['stAcao'] ) ) {
    $_REQUEST['stAcao'] = "cobrar";
}

//Define arquivos PHP para cada acao
switch ($_REQUEST['stAcao']) {
    case 'alterar'   : $pgProx = $pgForm; break;
    case 'excluir'   : $pgProx = $pgProc; break;
    DEFAULT          : $pgProx = $pgForm;
}

if ( is_array($link) ) {
    $_REQUEST = $link;
} else {
    foreach ($_REQUEST as $key => $valor) {
        $link[$key] = $valor;
    }
}

Sessao::write('link', $link);

//MONTAGEM DO FILTRO
$stFiltro = '';
if ($_REQUEST['inCGM']) {
    $stFiltro .= " ddc.numcgm = '".$_REQUEST['inCGM']."' AND ";
}

if ($_REQUEST['inCodInscricao']) {
    $arDadosInscricao = explode( "/", $_REQUEST['inCodInscricao'] );
    $stFiltro .= " dda.cod_inscricao = ".$arDadosInscricao[0];
    $stFiltro .= " AND dda.exercicio = ".$arDadosInscricao[1]." AND ";
}

if ($_REQUEST["inCodImovel"]) {
    $stFiltro .= " ddi.inscricao_municipal = ".$_REQUEST['inCodImovel']." AND ";
}

if ($_REQUEST["inInscricaoEconomica"]) {
    $stFiltro .= " dde.inscricao_economica = ".$_REQUEST['inInscricaoEconomica']." AND ";
}

if ( $stFiltro )
    $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );

$obTDATDividaAtiva = new TDATDividaAtiva;
$obTDATDividaAtiva->recuperaListaInscricoes( $rsListaInscricoes, $stFiltro );

$obLista = new Lista;
$obLista->setRecordSet( $rsListaInscricoes );
$obLista->setTitulo( "Registros de Incrição em Dívida Ativa" );
$obLista->setMostraPaginacao(false);

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Contribuinte" );
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Inscrição/Ano" );
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Data de Inscrição");
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento ("CENTRO");
$obLista->ultimoDado->setCampo( "[numcgm]/[nom_cgm]" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "[cod_inscricao]/[exercicio]" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setAlinhamento ("CENTRO");
$obLista->ultimoDado->setCampo( "[dt_inscricao_divida]" );
$obLista->commitDado();

$obChkParcela = new Checkbox;
$obChkParcela->setName  ( "boSelecionada" );
$obChkParcela->setValue ( "[cod_inscricao]§[exercicio]§[numcgm]§[num_parcelamento]" );

$obLista->addDadoComponente                    ( $obChkParcela );
$obLista->ultimoDado->setAlinhamento           ( 'CENTRO' );
$obLista->ultimoDado->setCampo                 ( "reemitir" );
$obLista->commitDadoComponente                 ();

$obChkTodosN = new Checkbox;
$obChkTodosN->setName                        ( "boTodos" );
$obChkTodosN->setId                          ( "boTodos" );
$obChkTodosN->setRotulo                      ( "Selecionar Todas" );
$obChkTodosN->obEvento->setOnChange          ( "selecionarTodos('n');" );
$obChkTodosN->montaHTML();

$obTabelaCheckboxN = new Tabela;
$obTabelaCheckboxN->addLinha();
$obTabelaCheckboxN->ultimaLinha->addCelula();
$obTabelaCheckboxN->ultimaLinha->ultimaCelula->setColSpan ( 2 );
$obTabelaCheckboxN->ultimaLinha->ultimaCelula->setClass   ( $obLista->getClassPaginacao() );
$obTabelaCheckboxN->ultimaLinha->ultimaCelula->addConteudo( "<div align='right'>Selecionar Todos".$obChkTodosN->getHTML()."&nbsp;</div>");
$obTabelaCheckboxN->ultimaLinha->commitCelula();
$obTabelaCheckboxN->commitLinha();
$obTabelaCheckboxN->montaHTML();

$obLista->montaHTML();

$stHtml = $obLista->getHTML();
$stHtml .= $obTabelaCheckboxN->getHTML();

$obSpanDados = new Span;
$obSpanDados->setId      ( "spnLista" );
$obSpanDados->setValue   ( $stHtml );

$obChkEmissao = new Checkbox;
$obChkEmissao->setName               ( "boEmissao" );
$obChkEmissao->setId                 ( "boEmissao" );
$obChkEmissao->setRotulo             ( "&nbsp;" );
$obChkEmissao->setLabel              ( "Emitir Termo de Processo Judicial" );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( $_REQUEST['stCtrl'] );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $_REQUEST['stAcao'] );

$obBtnOK = new OK;
$obBtnOK->setName              ( "btnOk" );
$obBtnOK->obEvento->setOnClick ( "validarListar();" );

$obBtnCancelar = new Cancelar;

$botoesSpanBotoes = array ( $obBtnOK, $obBtnCancelar );

$obForm = new Form;
$obForm->setAction ( $pgProc );
$obForm->settarget ( "oculto" );

$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm );
$obFormulario->addHidden     ( $obHdnCtrl );
$obFormulario->addHidden     ( $obHdnAcao );
$obFormulario->addTitulo     ( "Abertura de Cobrança Judicial" );
$obFormulario->addSpan       ( $obSpanDados );
$obFormulario->addComponente ( $obChkEmissao );
$obFormulario->defineBarra   ( $botoesSpanBotoes, 'left', '' );

$obFormulario->show();
