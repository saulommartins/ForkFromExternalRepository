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
  * Página de Lista de Emissao
  * Data de criação : 26/09/2006

  * @author Analista: Fábio Bertoldi
  * @author Programador: Fernando Piccini Cercato

    * $Id: LSManterEmissao.php 63083 2015-07-22 19:44:59Z evandro $

  Caso de uso: uc-05.04.03
**/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaDocumento.class.php" );
include_once(CAM_FRAMEWORK."/request/Request.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterEmissao";
$pgFilt = "FL".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once( $pgJS );
//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
if ( empty( $_REQUEST['stAcao'] ) ) {
    $_REQUEST['stAcao'] = "incluir";
}

//MANTEM FILTRO E PAGINACAO
$stLink .= "&stAcao=".$_REQUEST['stAcao'];
if ( isset($_GET["pg"]) &&  isset($_GET["pos"]) ) {
    $stLink.= "&pg=".$_GET["pg"]."&pos=".$_GET["pos"];
    $link["pg"]  = $_GET["pg"];
    $link["pos"] = $_GET["pos"];
}

//USADO QUANDO EXISTIR FILTRO
//NA FL O VAR LINK DEVE SER RESETADA
if ( is_array(isset($link)) ) {
    $_REQUEST = $link;
} else {
    foreach ($_REQUEST as $key => $valor) {
        $link[$key] = $valor;
    }
}

Sessao::write('link', $link);
Sessao::write('stLink', $stLink);
Sessao::remove( 'dados_emissao' );
//MONTAGEM DO FILTRO
$stFiltro = " divida_remissao.cod_inscricao IS NULL AND ";
/* PARAMETROS VINDOS DA EMISSAO DA INSCRICAO DE DIVIDA */
if ($_REQUEST["inNumeroParcelamento"]) {
    $stFiltro .= " \n dp.num_parcelamento in ( ".$_REQUEST['inNumeroParcelamento']." ) AND ";
}

if ( $request->get('inExercicio') ) {
    $stFiltro .= " \n ddp.exercicio = '".$_REQUEST['inExercicio']."' AND ";
}

if ($request->get('inCodInscricao') ) {
    $arDados = explode( "/", $_REQUEST['inCodInscricao'] );
    $stFiltro .= " \n ddp.cod_inscricao = ".$arDados[0]." AND ";
    $stFiltro .= " \n ddp.exercicio = ".$arDados[1]." AND ";
}

if ( $request->get('stTipoModalidade') == "emissao" ) {
    $stFiltro .= " \n ded.timestamp IS NULL AND ";
}else
if ( $request->get('stTipoModalidade') == "reemissao" ) {
    $stFiltro .= " \n ded.timestamp IS NOT NULL AND ";
}

if ( $request->get('inInscricaoEconomica') ) {
    $stFiltro .= " \n dde.inscricao_economica = ".$_REQUEST['inInscricaoEconomica']." AND ";
}

if ( $request->get('inCodImovel') ) {
    $stFiltro .= " \n ddi.inscricao_municipal = ".$_REQUEST['inCodImovel']." AND ";
}

if ( $request->get('inCGM') ) {
    $stFiltro .= " \n ddc.numcgm IN (".$_REQUEST['inCGM'].") AND ";
}

if ( $request->get("stDocumentos") ) {
    $stFiltro .= " \n ddd.cod_documento IN ( ".$_REQUEST['stDocumentos']." ) AND ";
}

if ( $request->get("stTipoDocumentos") ) {
    $stFiltro .= " \n ddd.cod_tipo_documento IN ( ".$_REQUEST['stTipoDocumentos']." ) AND ";
}

if ($request->get("inNumModalidade")) {
    $stFiltro .= " \n modalidade.cod_modalidade = ".$_REQUEST['inNumModalidade']." AND ";
}

if ($request->get("stDataInscricao")) {
    $stFiltro .= " \n divida_ativa.dt_inscricao = TO_DATE('".$_REQUEST["stDataInscricao"]."','dd/mm/yyyy') AND ";
}

if ( $stFiltro )
    $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );

$stOrdem = " ORDER BY ddd.num_parcelamento, ddd.cod_documento";

$obTDATDividaDocumento = new TDATDividaDocumento;

/* 
    Validacao para quando a listagem vier da acao Gestão Tributária :: Dívida Ativa :: Cobrança Administrativa :: Cobrar Dívida Ativa
    já que o agrupamento pelo cod de inscrição da divida traz valores pra cada divida e queremos agrupar pela cobrança
    seta a coluna do SELECT da consulta como vazio para realizar o agrupamento pela cobrança 
*/
if ($request->get("stOrigemFormulario") == 'cobranca_divida'){
    $obTDATDividaDocumento->setDado('cod_inscricao_divida_ativa','');
}else{
    $obTDATDividaDocumento->setDado('cod_inscricao_divida_ativa','divida_ativa.cod_inscricao AS cod_inscricao_divida_ativa,');
}
  
$obTDATDividaDocumento->recuperaListaDocumentoLS( $rsDocumentos, $stFiltro, $stOrdem );

$obForm = new Form;
$obForm->setAction ( $pgForm );
$obFormulario = new FormularioAbas;
$obFormulario->addForm  ( $obForm );

if ($_REQUEST['stOrigemFormulario'] == 'inscricao_divida') {
    $obFormulario->addTitulo  ( "Emissão de termo de inscrição em dívida" );
}

$obLista = new Lista;
$obLista->setRecordSet( $rsDocumentos );
$obLista->setMostraPaginacao(false);

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Contribuinte");
$obLista->ultimoCabecalho->setWidth( 35 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
if ($_REQUEST['stOrigemFormulario'] == 'inscricao_divida') {
    $obLista->ultimoCabecalho->addConteudo( "Inscrição/Ano" );
}else{
    $obLista->ultimoCabecalho->addConteudo( "Cobrança/Ano" );
}
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Documento" );
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Emissão");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("");
$obLista->ultimoCabecalho->setWidth( 3 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "[numcgm] - [nom_cgm]" );
$obLista->commitDado();

$obLista->addDado();
if ($_REQUEST['stOrigemFormulario'] == 'inscricao_divida') {
    $obLista->ultimoDado->setCampo( "[cod_inscricao_divida_ativa]/[exercicio_divida_ativa]" );
}else{
    $obLista->ultimoDado->setCampo( "[numero_parcelamento]/[exercicio_cobranca]" );
}
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "[num_documento] - [nome_documento]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "[data_emissao]" );
$obLista->ultimoDado->setAlinhamento( "CENTRO" );
$obLista->commitDado();

$obChkEmitir = new Checkbox;
$obChkEmitir->setName ( "nboEmitir" );
$obChkEmitir->setValue ( "[nome_arquivo_agt]&[nome_arquivo_swx]&[nome_documento]&[numcgm]&[numero_parcelamento]&[exercicio]&[num_parcelamento]&[cod_documento]&[num_emissao]&[cod_tipo_documento]&[num_documento]" );

$obLista->addDadoComponente ( $obChkEmitir );
$obLista->ultimoDado->setAlinhamento ( 'CENTRO' );
$obLista->ultimoDado->setCampo ( "emitir" );
$obLista->commitDadoComponente ();

$obChkTodosN = new Checkbox;
if ( $_REQUEST['stOrigemFormulario'] == 'inscricao_divida' )
    $obChkTodosN->setChecked ( true );
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

$stHtmlTmp  = $obLista->getHTML();
$stHtmlTmp .= $obTabelaCheckboxN->getHTML();

$obSpanLista = new Span;
$obSpanLista->setId       ( 'spnListaNormais' );
$obSpanLista->setValue    ( $stHtmlTmp );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
if (isset($stCtrl)) {
    $obHdnCtrl->setValue ( $stCtrl  );
}

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $_REQUEST['stAcao']  );

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName("stCaminho");
$obHdnCaminho->setValue( CAM_GT_DAT_INSTANCIAS."emissao/OCGeraRelatorio.php" );

if ($_REQUEST['stOrigemFormulario'] == 'inscricao_divida') {
    $js = "	selecionarTodos('y'); ";
    sistemaLegado::executaFrameOculto( $js );
}

$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnCaminho );
$obFormulario->addSpan  ( $obSpanLista );
$obFormulario->Cancelar( $pgFilt );

$obFormulario->show();
