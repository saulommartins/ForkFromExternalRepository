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
  * Página de Consulta da Divida Ativa
  * Data de criação : 20/08/2007

  * @author Analista: Fábio Bertoldi
  * @author Programador: Fernando Piccini Cercato

    * $Id: FMConsultaInscricaoDocumento.php 63839 2015-10-22 18:08:07Z franver $

  Caso de uso: uc-05.04.09
**/

/*
$Log$
Revision 1.2  2007/09/14 14:08:19  cercato
correcao na emissao de documentos a partir da consulta da divida.

Revision 1.1  2007/08/20 19:03:57  cercato
*** empty log message ***

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaAtiva.class.php" );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaDocumento.class.php" );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATModalidade.class.php" );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRCreditoGrupo.class.php" );
include_once ( CAM_GA_ADM_COMPONENTES."ITextBoxSelectDocumento.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ConsultaInscricao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php?".Sessao::getId();
$pgForm = "FM".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once ( $pgJS );
;

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
if ( empty( $_REQUEST['stAcao']) ) {
    $_REQUEST['stAcao'] = "incluir";
}

// EFETUA ALTERACAO DA DATA BASE CASO HAJA COBRANÇA

#==============================================================

$inCodInscricao = $_REQUEST["inCodInscricao"];
$inExercicio    = $_REQUEST["inExercicio"];
$arDadosSessao = Sessao::read('dados');

$arDadosSessao["ExercicioDA"] = $inExercicio;
$arDadosSessao["InscricaoDA"] = $inCodInscricao;

Sessao::write('dados', $arDadosSessao);

#========================== INICIO LISTA DE LANÇAMENTOS ========
$obTDATDividaAtiva = new TDATDividaAtiva;
$dtDataBase = $_REQUEST['stDataInscDiv'];
$obTDATDividaAtiva->setDado('data_base', $dtDataBase );

$obTDATDividaDocumento = new TDATDividaDocumento;
$obTDATDividaDocumento->listaDocumentoConsultaDASemCobranca( $rsListaDocSemCobranca, $inCodInscricao, $inExercicio );
$obTDATDividaDocumento->listaDocumentoConsultaDAComCobranca( $rsListaDocComCobranca, $inCodInscricao, $inExercicio );

//inicio lista de documentos referentes a inscricao
$obLista = new Table;
$obLista->setRecordSet( $rsListaDocSemCobranca );
$obLista->setSummary('Documentos Referentes à Inscrição');

$obLista->Head->addCabecalho( 'Documento' , 70 );
$obLista->Head->addCabecalho( 'Emissão' , 10 );

$obLista->Body->addCampo( '[cod_documento] - [nome_documento]', "E" );
$obLista->Body->addCampo( 'dt_emissao', "C" );
//boImprimir ->boolean que diz se deve imprimir ou não o documento
$obLista->Body->addAcao('imprimir', 'imprimirDocumentos( %s,%s,%s,%s,%s,%s,%s,%s );' , array( 'cod_documento', 'cod_tipo_documento','nome_arquivo_agt','nome_arquivo_swx','nome_documento','num_documento','num_parcelamento','exercicio' ), 'boimprimir' );

$obLista->montaHTML();
$stHtml = $obLista->getHtml();

$obSpanDocumentoInscricao = new Span;
$obSpanDocumentoInscricao->setId      ( "spnDocumentoInscricao" );
$obSpanDocumentoInscricao->setValue   ( $stHtml );

//inicio lista de documentos referentes a cobranca
$obLista = new Table;
$obLista->setRecordSet( $rsListaDocComCobranca );
$obLista->setSummary( "Documentos Referentes à Cobrança" );

$obLista->Head->addCabecalho( 'Documento' , 70 );
$obLista->Head->addCabecalho( 'Emissão' , 10 );

$obLista->Body->addCampo( '[cod_documento] - [nome_documento]', "E" );
$obLista->Body->addCampo( 'dt_emissao', "C" );

$obLista->Body->addAcao('imprimir', 'imprimirDocumentos( %s,%s,%s,%s,%s,%s,%s,%s );' , array( 'cod_documento', 'cod_tipo_documento','nome_arquivo_agt','nome_arquivo_swx','nome_documento','num_documento','num_parcelamento','exercicio' ), 'boimprimir' );

$obLista->montaHTML();
$stHtml = $obLista->getHtml();

$obSpanDocumentoCobranca = new Span;
$obSpanDocumentoCobranca->setId      ( "spnDocumentoCobranca" );
$obSpanDocumentoCobranca->setValue   ( $stHtml );
#=================== INICIO LISTA DE COBRANÇAS ====

$stFiltro = "";
if ($_REQUEST["inCodInscricao"] && $_REQUEST["inExercicio"]) {
    $stFiltro = " WHERE ddp.cod_inscricao = ".$_REQUEST["inCodInscricao"]." AND ddp.exercicio = '".$_REQUEST["inExercicio"]."'";
}

$obTDATDividaAtiva->ListaConsultaCobrancas( $rsListaCobrancas, $stFiltro );

$rsListaCobrancas->addFormatacao("valor_parcelamento","NUMERIC_BR");
$rsListaCobrancas->setPrimeiroElemento();

$tableCobrancas = new TableTree();
$tableCobrancas->setRecordset       ( $rsListaCobrancas );
$tableCobrancas->setSummary         ('Lista de Cobranças');
$tableCobrancas->setArquivo         ( 'FMConsultaInscricaoDetalheCobranca.php' );
$tableCobrancas->setParametros      ( array( "num_parcelamento", "data_base", "dt_parcelamento" ) );
//$tableCobrancas->setConditional     ( true , "#efefef" ); // lista zebrada

$tableCobrancas->Head->addCabecalho ( 'Cobrança', 8  );
$tableCobrancas->Head->addCabecalho ( 'Modalidade', 12 );
$tableCobrancas->Head->addCabecalho ( 'Data', 10  );
$tableCobrancas->addCondicionalTree ( 'ativar_lista', 't' );
$tableCobrancas->Head->addCabecalho ( 'Cobrança', 12  );
$tableCobrancas->Head->addCabecalho ( 'Modalidade', 12  );
$tableCobrancas->Head->addCabecalho ( 'Data', 8  );
$tableCobrancas->Head->addCabecalho ( 'Usuário', 30  );
$tableCobrancas->Head->addCabecalho ( 'Parcelas', 7  );
$tableCobrancas->Head->addCabecalho ( 'Situação', 8  );
$tableCobrancas->Head->addCabecalho ( 'Valor', 10  );

$tableCobrancas->Body->addCampo     ( 'numero_parcelamento', 'C' );
$tableCobrancas->Body->addCampo     ( '[cod_modalidade] - [descricao_modalidade]' );
$tableCobrancas->Body->addCampo     ( 'dt_parcelamento', 'C' );
$tableCobrancas->Body->addCampo     ( '[numcgm_usuario] - [nomcgm_usuario]' );
$tableCobrancas->Body->addCampo     ( 'qtd_parcelas', 'C' );
$tableCobrancas->Body->addCampo     ( 'situacao', 'C' );
$tableCobrancas->Body->addCampo     ( 'valor_parcelamento', 'D' );

$tableCobrancas->montaHTML();
$stHtml = $tableCobrancas->getHtml();

$obSpanCobranca = new Span;
$obSpanCobranca->setId      ( "spnCobranca" );
$obSpanCobranca->setValue   ( $stHtml );
#=================== FIM LISTA DE COBRANÇAS ====

$obLblContribuinte = new Label;
$obLblContribuinte->setRotulo   ( "Contribuinte" );
$obLblContribuinte->setName     ( "stContribuinte" );
$obLblContribuinte->setValue    ( $_REQUEST['inNumCGMContrib']." - ".$_REQUEST["inNomCGMContrib"] );
$obLblContribuinte->setTitle    ( "Contribuinte" );

$obLblInscricaoAno = new Label;
$obLblInscricaoAno->setRotulo   ( "Inscrição / Ano" );
$obLblInscricaoAno->setName     ( "stInscricaoAno" );
$obLblInscricaoAno->setValue    ( $_REQUEST['inCodInscricao']." / ".$_REQUEST["inExercicio"] );
$obLblInscricaoAno->setTitle    ( "Inscrição / Ano" );

$obLblDataInscricao = new Label;
$obLblDataInscricao->setRotulo  ( "Data de Inscrição" );
$obLblDataInscricao->setName    ( "stDataInscricao" );
$obLblDataInscricao->setValue   ( $_REQUEST['stDataInscDiv'] );
$obLblDataInscricao->setTitle   ( "Data de Inscrição" );

$obLblInscricaoMunic = new Label;
$obLblInscricaoMunic->setRotulo ( "Inscrição Imobiliária"    );
$obLblInscricaoMunic->setName   ( "inInscMunic"              );
$obLblInscricaoMunic->setValue  ( $_REQUEST['inInscMunic']   );
$obLblInscricaoMunic->setTitle  ( "Inscrição Imobiliária"    );

$obLblInscricaoEcono = new Label;
$obLblInscricaoEcono->setRotulo ( "Inscrição Econômica" );
$obLblInscricaoEcono->setName   ( "inInscEcon" );
$obLblInscricaoEcono->setValue  ( $_REQUEST['inInscEcon'] );
$obLblInscricaoEcono->setTitle  ( "Inscrição Econômica" );

$obLblSituacao = new Label;
$obLblSituacao->setRotulo       ( "Situação" );
$obLblSituacao->setName         ( "stSituacao" );
$obLblSituacao->setValue        ( $_REQUEST['stSituacao'] );
$obLblSituacao->setTitle        ( "Situação" );

$obLblAutoridade = new Label;
$obLblAutoridade->setRotulo     ( "Autoridade Competente" );
$obLblAutoridade->setName       ( "stAutorComp" );
$obLblAutoridade->setValue      ( $_REQUEST['inNumCGMAutorid']." - ".$_REQUEST["inNomCGMAutorid"] );
$obLblAutoridade->setTitle      ( "Autoridade Competente" );

$obLblProcesso = new Label;
$obLblProcesso->setRotulo       ( "Processo" );
$obLblProcesso->setName         ( "stProcesso" );
$obLblProcesso->setValue        ( $_REQUEST['inCodProcesso']." / ".$_REQUEST["inExercicioProcesso"] );
$obLblProcesso->setTitle        ( "Autoridade Competente" );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( $_REQUEST['stCtrl']  );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $_REQUEST['stAcao']  );

$boBtnVoltar = new Voltar();
$boBtnVoltar->setName('btVoltar');
$boBtnVoltar->obEvento->setOnclick('Voltar()');

$stLocation  = CAM_GT_DAT_INSTANCIAS."consultas/OCGeraRelatorioConsultaDivida.php?";
$stLocation .= Sessao::getId().'&stAcao='.$_REQUEST['stAcao'];
$stLocation .= '&inCodInscricao='.$inCodInscricao;
$stLocation .= '&inExercicio='.$inExercicio;
$stLocation .= "&dtDataBase=".$dtDataBase;
$stLocation .= "&inNumCGMContrib=".$_REQUEST['inNumCGMContrib'];
$stLocation .= "&inNomCGMContrib=".$_REQUEST["inNomCGMContrib"];
$stLocation .= "&inInscMunic=".$_REQUEST['inInscMunic'];
$stLocation .= "&inInscEcon=".$_REQUEST['inInscEcon'];
$stLocation .= "&inNumCGMAutorid=".$_REQUEST['inNumCGMAutorid'];
$stLocation .= "&inNomCGMAutorid=".$_REQUEST["inNomCGMAutorid"];
$stLocation .= "&stSituacao=".$_REQUEST['stSituacao'];
$stLocation .= "&inCodProcesso=".$_REQUEST['inCodProcesso'];
$stLocation .= "&inExercicioProcesso=".$_REQUEST["inExercicioProcesso"];
$stLocation .= "&";

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName("stCaminho");
$obHdnCaminho->setValue( $stLocation );

$obForm = new Form;
$obForm->setAction ( CAM_FW_POPUPS."relatorio/OCRelatorio.php" );
$obForm->setTarget ( "oculto" );

$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm );
$obFormulario->addHidden     ( $obHdnCtrl );
$obFormulario->addHidden     ( $obHdnAcao );
$obFormulario->addHidden     ( $obHdnCaminho );
$obFormulario->addTitulo     ( "Dados para Consulta" );

$obFormulario->addComponente ( $obLblContribuinte );
$obFormulario->addComponente ( $obLblInscricaoAno );
$obFormulario->addComponente ( $obLblDataInscricao );
if ( $_REQUEST['inInscMunic'] )
    $obFormulario->addComponente ( $obLblInscricaoMunic );

if ( $_REQUEST['inInscEcon'] )
    $obFormulario->addComponente ( $obLblInscricaoEcono );

$obFormulario->addComponente ( $obLblSituacao );
$obFormulario->addComponente ( $obLblAutoridade );
if ( $_REQUEST['inCodProcesso'] )
    $obFormulario->addComponente ( $obLblProcesso   );

$obFormulario->addSpan       ( $obSpanDocumentoInscricao );
$obFormulario->addSpan       ( $obSpanCobranca );
$obFormulario->addSpan       ( $obSpanDocumentoCobranca );

$obFormulario->defineBarra( array($boBtnVoltar) );
$obFormulario->show();
