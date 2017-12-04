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
  * Data de criação : 13/02/2007

  * @author Analista: Fábio Bertoldi
  * @author Programador: Fernando Piccini Cercato

    * $Id: FMConsultaInscricao.php 63911 2015-11-05 17:03:30Z carlos.silva $

  Caso de uso: uc-05.04.09
**/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaAtiva.class.php" );
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

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

// EFETUA ALTERACAO DA DATA BASE CASO HAJA COBRANÇA

#==============================================================

$inCodInscricao = $_REQUEST["inCodInscricao"];
$inExercicio    = $_REQUEST["inExercicio"];

#========================== INICIO LISTA DE LANÇAMENTOS ========
$obTDATDividaAtiva = new TDATDividaAtiva;
$dtDataBase = $_REQUEST['stDataBase'];
$obTDATDividaAtiva->setDado('data_base', $dtDataBase );

$stFiltroLancamentos = "WHERE dda.cod_inscricao = ".$inCodInscricao;
$stFiltroLancamentos .=" AND dda.exercicio = '".$inExercicio."'";
$obTDATDividaAtiva->listaConsultaValoresOrigemDivida( $rsListaLancamentos , $stFiltroLancamentos );
while ( !$rsListaLancamentos->Eof() ) {
    $rsListaLancamentos->setCampo( "nom_origem", str_replace( ";", "<BR>", $rsListaLancamentos->getCampo("nom_origem") ) );
    $rsListaLancamentos->proximo();
}
$rsListaLancamentos->setPrimeiroElemento();
$rsListaLancamentos->addFormatacao ('valor_lancado', 'NUMERIC_BR');
$rsListaLancamentos->addFormatacao ('valor_atualizado', 'NUMERIC_BR');
$tableLancamentos = new Table();
$tableLancamentos->setRecordset( $rsListaLancamentos );
$tableLancamentos->setSummary('Lista de Lançamentos');

#$tableCobrancas->setParametros( array( "num_parcelamento" ) );
//$tableLancamentos->setConditional( true , "#efefef" ); // lista zebrada
$tableLancamentos->Head->addCabecalho( 'Exercício', 10  );
$tableLancamentos->Head->addCabecalho( 'Crédito/Grupo de Crédito', 40  );
$tableLancamentos->Head->addCabecalho( 'Parcelas', 10  );
$tableLancamentos->Head->addCabecalho( 'Valor Lançado', 20  );
$tableLancamentos->Head->addCabecalho( 'Valor Atualizado', 20  );

$tableLancamentos->Body->addCampo( 'exercicio_original', 'C' );
$tableLancamentos->Body->addCampo( 'nom_origem' );
$tableLancamentos->Body->addCampo( 'total_parcelas', 'C' );
$tableLancamentos->Body->addCampo( 'valor_lancado', 'D' );
$tableLancamentos->Body->addCampo( 'valor_atualizado', 'D' );

$tableLancamentos->Foot->addSoma ( 'valor_lancado', "D" );
$tableLancamentos->Foot->addSoma ( 'valor_atualizado', "D" );

$tableLancamentos->montaHTML();
$stHtml = $tableLancamentos->getHtml();

$obSpanLancamentos = new Span;
$obSpanLancamentos->setId      ( "spnLancamentos" );
$obSpanLancamentos->setValue   ( $stHtml );

#========================== FIM LISTA DE LANÇAMENTOS ========

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
$tableCobrancas->setParametros      ( array( "num_parcelamento", "data_base", "dt_parcelamento", "motivo_cancelamento", "data_cancelamento", "usuario_cancelamento", "situacao" ) );
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
$obLblInscricaoAno->setRotulo   ( "Inscrição/Ano" );
$obLblInscricaoAno->setName     ( "stInscricaoAno" );
$obLblInscricaoAno->setValue    ( $_REQUEST['inCodInscricao']."/".$_REQUEST["inExercicio"] );
$obLblInscricaoAno->setTitle    ( "Inscrição/Ano" );

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

$obLblNorma = new Label;
$obLblNorma->setRotulo       ( "Fundamentação Legal" );
$obLblNorma->setName         ( "stNorma" );
$obLblNorma->setValue        ( $_REQUEST['inRemissaoCodNorma']." - ".$_REQUEST['stRemissaoNorma'] );
$obLblNorma->setTitle        ( "Fundamentação Legal" );

$obSpanCancelada = new Span;
$obSpanCancelada->setId('spnCancelada');

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

$obDtDataBase = new Data();
$obDtDataBase->setName('dtDataBase');
$obDtDataBase->setRotulo('Data base');
$obDtDataBase->setTitle('Data base para o atualização do valor.');
$obDtDataBase->setValue($dtDataBase);
$obDtDataBase->obEvento->setOnChange( "atualizaLancamentos(this);" );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( $stCtrl  );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao  );

$boBtnVoltar = new Voltar();
$boBtnVoltar->setName('btVoltar');
$boBtnVoltar->obEvento->setOnclick('Voltar()');

$stLocation  = CAM_GT_DAT_INSTANCIAS."consultas/OCRelatorioConsultaDivida.php?";
$stLocation .= Sessao::getId().'&stAcao='.$stAcao;
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
$stLocation .= "&dtCancelada=".$_REQUEST["dtCancelada"];
$stLocation .= "&inNumCgmCancelada=".$_REQUEST["inNumCgmCancelada"];
$stLocation .= "&stNomCgmCancelada=".$_REQUEST["stNomCgmCancelada"];
$stLocation .= "&";

$obButtonRelatorio = new Button;
$obButtonRelatorio->setName  ( "Relatorio" );
$obButtonRelatorio->setValue ( "Relatório" );
$obButtonRelatorio->obEvento->setOnClick( "document.frm.submit();" );

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
if ($_REQUEST['inRemissaoCodNorma']) {
    $obFormulario->addComponente ( $obLblNorma );
}

if ($_REQUEST['stSituacao'] == 'Cancelada') {
    $obLblMotivo = new Label;
    $obLblMotivo->setRotulo       ( "Motivo" );
    $obLblMotivo->setName         ( "stMotivo" );
    $obLblMotivo->setValue        ( $_REQUEST['stMotivo'] );
    $obLblMotivo->setTitle        ( "Motivo" );
    $obFormulario->addComponente ( $obLblMotivo );
}

$obFormulario->addComponente ( $obLblAutoridade );
if ( $_REQUEST['inCodProcesso'] )
    $obFormulario->addComponente ( $obLblProcesso   );
$obFormulario->addComponente ( $obDtDataBase );

$obFormulario->addSpan       ( $obSpanLancamentos   );
#$obFormulario->addSpan       ( $obSpanInscricoes    );
$obFormulario->addSpan       ( $obSpanCobranca );

$obFormulario->defineBarra( array( $obButtonRelatorio ), "left", "" );
$obFormulario->defineBarra( array($boBtnVoltar) );
$obFormulario->show();
