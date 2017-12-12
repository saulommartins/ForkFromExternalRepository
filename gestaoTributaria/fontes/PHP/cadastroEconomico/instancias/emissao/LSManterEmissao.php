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
  * Data de criação : 10/10/2006

  * @author Analista: Fábio Bertoldi
  * @author Programador: Diego Bueno Coelho

    * $Id: LSManterEmissao.php 63405 2015-08-25 14:14:49Z arthur $

  Caso de uso: uc-05.02.12
**/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GT_CEM_MAPEAMENTO."TCEMLicencaDocumento.class.php";

//Define o nome dos arquivos PHP
$stParametros .= "&stAcao=".$request->get('stAcao');
$stParametros .= "&stTipoLicenca=2";
$stParametros .= "&inInscricaoEconomica=".$request->get('inInscricaoEconomica');
$stParametros .= "&inCodLicenca=".$request->get('inCodLicenca');
$stParametros .= "&inNumeroLicenca=".$request->get('inCodLicenca');
$stParametros .= "&inExercicio=".$request->get('inExercicio');
$stParametros .= "&stTipoModalidade=alteracao";
$stParametros .= "&stCodAcao=".$request->get('stCodAcao');
$stParametros .= "&inOcorrenciaLicenca=".$request->get('inOcorrenciaLicenca');
$stParametros .= "&stOrigemFormulario=conceder_licenca";
$stParametros .= "&stTipoLicenca=".$request->get('inInscricaoEconomica');
$stParametros .= "&inInscricaoEconomica=".$request->get('inInscricaoEconomica');
$stParametros .= "&inCodigoTipoDocumento=".$request->get('inCodTipoDocumento2');
$stParametros .= "&stCodigoDocumentoTxt=".$request->get('stCodDocumentoTxt2');
$stParametros .= "&stNomeDocumento=".$request->get('stNomeDocumento');
$stParametros .= "&stNomeArquivo=".$request->get('stNomeArquivo');
$stParametros .= "&inCodigoDocumento=".$request->get('stCodDocumento2')."&";

$pgFilt = CAM_GT_CEM_INSTANCIAS."licenca/LSManterLicenca.php?".Sessao::getId().$stParametros;

$stPrograma = "ManterEmissao";
$pgForm = "FM".$stPrograma.".php?".Sessao::getId().$stParametros;
$pgList = "LS".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once( $pgJS );

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
if ( empty( $request->get('stAcao') ) ) {
    $request->set('stAcao', 'incluir');
}

//MANTEM FILTRO E PAGINACAO
$link = Sessao::read( "link" );
$stLink .= "&stAcao=".$_REQUEST['stAcao'];
if ($_GET["pg"] and  $_GET["pos"]) {
    $stLink.= "&pg=".$_GET["pg"]."&pos=".$_GET["pos"];
    $link["pg"]  = $_GET["pg"];
    $link["pos"] = $_GET["pos"];
}

Sessao::write( 'link'  , $link );
Sessao::write( 'stLink', $stLink );

//MONTAGEM DO FILTRO
if ( $request->get('stCodAcao') )
    $stFiltro = " amad.cod_acao = ". $request->get('stCodAcao') ." AND ";
else
    $stFiltro = " amad.cod_acao = ". Sessao::read('acao')." AND ";

/* PARAMETROS VINDOS DA EMISSAO DA INSCRICAO DE DIVIDA */
if ($request->get('inExercicio')) {
    $stFiltro .= " \n eld.exercicio = ".$request->get('inExercicio')." AND ";
}

if ($request->get('inNumeroLicenca')) {
    $stFiltro .= " \n eld.cod_licenca = ".$request->get('inNumeroLicenca')." AND ";
}
if ($request->get('inCodLicenca')) {
    $stFiltro .= " \n eld.cod_licenca = ".$request->get('inCodLicenca')." AND ";
}

if ($request->get('inOcorrenciaLicenca')) {
    $stFiltro .= " \n lca.ocorrencia_licenca = ".$request->get('inOcorrenciaLicenca')." AND ";
}

if ($request->get('stTipoModalidade') == "emissao") {
    $stFiltro .= " \n ded.timestamp IS NULL AND ";
} elseif ($request->get('stTipoModalidade') == "reemissao" || $request->get('stTipoModalidade') == 'alteracao') {
    $stFiltro .= " \n ded.timestamp IS NOT NULL AND ";
}

if ($request->get('stCodDocumento')) {
    $stFiltro .= " \n ddd.num_documento = ".$request->get('stCodDocumento')." AND ";
}

if ( $stFiltro )
    $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );

$obTCEMLicencaDocumento = new TCEMLicencaDocumento;
$stOrdem = " limit 1 ";
$obTCEMLicencaDocumento->recuperaListaDocumentoLS( $rsDocumentos, $stFiltro, $stOrdem );

$obForm = new Form;
$obForm->setAction ( $pgForm );
$obFormulario = new FormularioAbas;
$obFormulario->addForm  ( $obForm );

if ($request->get('stOrigemFormulario') == 'conceder_licenca' || $request->get('stOrigemFormulario') == 'alterar_licenca') {
    $obFormulario->addTitulo  ( "Emissão de Alvará" );
}

$obLista = new Lista;
$obLista->setRecordSet( $rsDocumentos );

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Licença");
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Documento" );
$obLista->ultimoCabecalho->setWidth( 35 );
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
$obLista->ultimoDado->setCampo( "[cod_licenca] / [exercicio]" );
$obLista->ultimoDado->setAlinhamento( "CENTRO" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "nome_documento" );
$obLista->ultimoDado->setAlinhamento( "ESQUERDA" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "[data_emissao]" );
$obLista->ultimoDado->setAlinhamento( "CENTRO" );
$obLista->commitDado();

$obChkEmitir = new Checkbox;
$obChkEmitir->setName ( "nboEmitir" );
$obChkEmitir->setValue ( "[nome_arquivo_agt]-[nome_arquivo_swx]-[nome_documento]-[inscricao_economica]-[exercicio]-[num_alvara]-[cod_documento]-[cod_tipo_documento]-[cod_licenca]-[ocorrencia_licenca]" );

$obLista->addDadoComponente ( $obChkEmitir );
$obLista->ultimoDado->setAlinhamento ( 'CENTRO' );
$obLista->ultimoDado->setCampo ( "emitir" );
$obLista->commitDadoComponente ();

$obChkTodosN = new Checkbox;
if ( $_REQUEST['stOrigemFormulario'] == 'conceder_licenca' || $_REQUEST['stOrigemFormulario'] == 'alterar_licenca')
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
$obHdnCtrl->setValue ( $_REQUEST['stCtrl']  );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $_REQUEST['stAcao']  );

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName("stCaminho");
$obHdnCaminho->setValue( CAM_GT_DAT_INSTANCIAS."emissao/OCGeraRelatorio.php" );

if ($_REQUEST['stOrigemFormulario'] == 'conceder_licenca' || $_REQUEST['stOrigemFormulario'] == 'alterar_licenca') {
    $js = "	selecionarTodos('y'); ";
    sistemaLegado::executaFrameOculto( $js );
}

$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnCaminho );
$obFormulario->addSpan  ( $obSpanLista );
$obFormulario->Cancelar();

$obFormulario->show();

?>