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
    * Página de Formulário para o cadastro de lote
    * Data de Criação   : 29/12/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

    * @ignore

    * $Id: FMManterLote.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.08
*/

/*
$Log$
Revision 1.10  2006/09/18 10:30:54  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLoteRural.class.php"        );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLoteUrbano.class.php"       );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMConfiguracao.class.php"     );
include_once ( CAM_GT_CIM_COMPONENTES."MontaLocalizacao.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterLote";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";
include_once( $pgJs );

if ( empty( $_REQUEST['stAcao'] ) ) {
    $_REQUEST['stAcao'] = "incluir";
}
$arConfrontacoesSessao = array();
Sessao::write('confrontacoes', $arConfrontacoesSessao);

//[funcionalidade] => 178 ->Lote Urbano  193 ->Lote Rural
if ($_REQUEST["funcionalidade"] == 178) {
    $obRCIMLote = new RCIMLoteUrbano;
} elseif ($_REQUEST["funcionalidade"] == 193) {
    $obRCIMLote = new RCIMLoteRural;
}

$obRCIMConfiguracao = new RCIMConfiguracao;
$obRCIMConfiguracao->setCodigoModulo( 12 );
$obRCIMConfiguracao->setAnoExercicio( Sessao::getExercicio() );
$obRCIMConfiguracao->consultarConfiguracao();
$stMascaraLote = $obRCIMConfiguracao->getMascaraLote();
$obRCIMConfiguracao->consultarMascaraProcesso( $stMascaraProcesso );

$obMontaLocalizacao = new MontaLocalizacao;
$obMontaLocalizacao->setCadastroLocalizacao( false );

$obRCIMConfrontacao = new RCIMConfrontacao( $obRCIMLote );
$obRCIMConfrontacao->listarPontosCardeais( $rsListaPontosCardeais );

$obRCIMLote->listarUnidadeMedida( $rsUnidadeMedida );

if ($_REQUEST['stAcao'] == "incluir") {
   $obRCIMLote->obRCadastroDinamico->recuperaAtributosSelecionados( $rsAtributos );
} else {
    $obRCIMLote->setCodigoLote( $_REQUEST["inCodigoLote"] );
    $obRCIMLote->consultarLote();
    //DEFINICAO DOS ATRIBUTOS DE LOTE
    $arChaveAtributoLote =  array( "cod_lote"      => $_REQUEST["inCodigoLote"] );
    $obRCIMLote->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoLote );
    $obRCIMLote->obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributos );
}

$obMontaAtributos = new MontaAtributos;
$obMontaAtributos->setTitulo     ( "Atributos"  );
$obMontaAtributos->setName       ( "Atributo_"  );
$obMontaAtributos->setRecordSet  ( $rsAtributos );

//DEFINICAO DOS COMPONENTES
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $_REQUEST['stAcao'] );

$obHdMascaraLote = new Hidden;
$obHdMascaraLote->setName ( "hdnMascaraLote"   );
$obHdMascaraLote->setValue( $stMascaraLote     );

$obHdnAcaoConfrontacao = new Hidden;
$obHdnAcaoConfrontacao->setName( "stAcaoConfrontacao" );
$obHdnAcaoConfrontacao->setValue( $stAcaoConfrontacao );

$obHdnFuncionalidade = new Hidden;
$obHdnFuncionalidade->setName  ( "funcionalidade"            );
$obHdnFuncionalidade->setValue ( $_REQUEST["funcionalidade"] );

$obHdnTrecho = new Hidden;
$obHdnTrecho->setName( "stTrecho" );
$obHdnTrecho->setValue( $stTrecho );

$obHdnCodigoUF = new Hidden;
$obHdnCodigoUF->setName ( "inCodigoUF" );

$obHdnCodigoMunicipio = new Hidden;
$obHdnCodigoMunicipio->setName( "inCodigoMunicipio" );

//DADOS PARA ABA LOTE

$obTxtNumeroLote = new TextBox;
$obTxtNumeroLote->setName      ( "stNumeroLote"               );
$obTxtNumeroLote->setId        ( "stNumeroLote"               );
$obTxtNumeroLote->setMaxLength ( strlen( $stMascaraLote )     );
$obTxtNumeroLote->setSize      ( strlen( $stMascaraLote )     );
$obTxtNumeroLote->setNull      ( false                        );
$obTxtNumeroLote->setRotulo    ( "Número do Lote"             );
$obTxtNumeroLote->setMascara   ( $stMascaraLote               );
$obTxtNumeroLote->setValidaCaracteres( true                   );
//$obTxtNumeroLote->obEvento->setOnKeyUp("mascaraDinamico('".$stMascaraLote."', this, event);");
//if ( preg_match( "/^[:alnum:]/",$stMascaraLote ) ) {
//    $obTxtNumeroLote->setMinLength ( strlen( $stMascaraLote ) );
//}
//$obTxtNumeroLote->obEvento->setOnBlur ( "buscaValor( 'ValidaNumeroDoLote' );"  );
/* Lucas Stephanou   || 22/03/2005
Retirado para incluir numero do lote
sem zeros a esquerda no banco de dados
*/

//$obTxtNumeroLote->setPreencheComZeros( 'E' );

$obTxtAreaLote = new Numerico;
$obTxtAreaLote->setName      ( "flAreaLote" );
$obTxtAreaLote->setRotulo    ( "Área"       );
$obTxtAreaLote->setMaxLength ( 18           );
$obTxtAreaLote->setSize      ( 18           );
$obTxtAreaLote->setFloat     ( true         );
$obTxtAreaLote->setTitle     ( "Área total do lote em metros quadrados ou hectares" );
$obTxtAreaLote->setNull      ( false        );
$obTxtAreaLote->setNegativo  ( false        );
$obTxtAreaLote->setNaoZero   ( true         );
$obTxtAreaLote->setMaxValue  ( 999999999999.99 );

$obCmbUnidadeMedida = new Select;
$obCmbUnidadeMedida->setName      ( "stChaveUnidadeMedida"         );
$obCmbUnidadeMedida->setStyle     ( "width: 250px"                 );
$obCmbUnidadeMedida->setRotulo    ( "Área"                         );
$obCmbUnidadeMedida->setNull      ( false                          );
$obCmbUnidadeMedida->setCampoID   ( "[cod_unidade]-[cod_grandeza]" );
$obCmbUnidadeMedida->setCampoDesc ( "[nom_unidade] [simbolo]"      );
if ($_REQUEST["funcionalidade"] == 178) {
    $obCmbUnidadeMedida->setValue( "1-2" );
} elseif ($_REQUEST["funcionalidade"] == 193) {
    $obCmbUnidadeMedida->setValue( "3-2" );
}
$obCmbUnidadeMedida->preencheCombo ( $rsUnidadeMedida );

$obTxtProfundidadeMedia = new Numerico;
$obTxtProfundidadeMedia->setName      ( "flProfundidadeMedia" );
$obTxtProfundidadeMedia->setRotulo    ( "Profundidade Média"  );
$obTxtProfundidadeMedia->setNull      ( false                 );
$obTxtProfundidadeMedia->setNegativo  ( false                 );
$obTxtProfundidadeMedia->setNaoZero   ( true                  );
$obTxtProfundidadeMedia->setSize      ( 18                    );
$obTxtProfundidadeMedia->setMaxLength ( 18                    );
$obTxtProfundidadeMedia->setFloat     ( true                  );
$obTxtProfundidadeMedia->setTitle     ( "Informe a profundidade média do lote (em metros)" );
$obTxtProfundidadeMedia->setMaxValue  ( 999999999999.99 );

$dtdiaHOJE = date ("d/m/Y");
$obTxtDataInscricaoLote = new Data;
$obTxtDataInscricaoLote->setName                ( "dtDataInscricaoLote"         );
$obTxtDataInscricaoLote->setId                  ( "dtDataInscricaoLote"         );
$obTxtDataInscricaoLote->setRotulo              ( "Data de Inscrição"           );
$obTxtDataInscricaoLote->setNull                ( false                         );
$obTxtDataInscricaoLote->setValue               ( $dtdiaHOJE );
$obTxtDataInscricaoLote->obEvento->setOnChange  ( "javascript: buscaValor( 'validaDataInscricaoLote' );"  );

$obBscProcesso = new BuscaInner;
$obBscProcesso->setRotulo ( "Processo" );
$obBscProcesso->setTitle  ( "Processo do protocolo que formaliza" );
$obBscProcesso->obCampoCod->setName ("inNumProcesso");
$obBscProcesso->obCampoCod->setValue( $inNumProcesso );
$obBscProcesso->obCampoCod->setSize ( strlen($stMascaraProcesso) );
$obBscProcesso->obCampoCod->setMaxLength( strlen($stMascaraProcesso) );
$obBscProcesso->obCampoCod->obEvento->setOnKeyUp ("mascaraDinamico('".$stMascaraProcesso."', this, event);");
$obBscProcesso->obCampoCod->obEvento->setOnChange("buscaValor('buscaProcesso');" );
$obBscProcesso->setFuncaoBusca( "abrePopUp('".CAM_GA_PROT_POPUPS."processo/FLBuscaProcessos.php','frm','inNumProcesso','campoInner2','','".Sessao::getId()."','800','550')" );

$obBscBairro = new BuscaInner;
$obBscBairro->setRotulo ( "Bairro"                         );
$obBscBairro->setId     ( "innerBairroLote"                );
$obBscBairro->setNull   ( false                            );
$obBscBairro->setTitle  ( "Bairro em que o lote está localizado" );
$obBscBairro->obCampoCod->setName  ( "inCodigoBairroLote"  );
$obBscBairro->obCampoCod->setValue ( $inCodigoBairroLote   );
//$obBscBairro->obCampoCod->obEvento->setOnChange ( "BloqueiaFrames(true,false);buscaBairro();" );
$obBscBairro->obCampoCod->obEvento->setOnChange ( "buscaBairro();" );
$stBusca  = "abrePopUp('".CAM_GT_CIM_POPUPS."bairroSistema/FLProcurarBairro.php','frm','inCodigoBairroLote','innerBairroLote',''";
$stBusca .= " ,'".Sessao::getId()."','800','550')";
$obBscBairro->setFuncaoBusca ( $stBusca );

if ( $obRCIMConfiguracao->getNavegacaoAutomatico() == "ativo" )
    $boSeguir = true;
else
    $boSeguir = false;

$obChkSeguir = new checkbox;
$obChkSeguir->setName       ( "boSeguir"                            );
$obChkSeguir->setRotulo     ( " &nbsp; "                            );
$obChkSeguir->setLabel      ( "Seguir para o cadastro de imóvel"    );
$obChkSeguir->setChecked    ( $boSeguir );
//CONFRONTAÇÕES

$obCmbPontoCardeal = new Select;
$obCmbPontoCardeal->setName       ( "inCodigoPontoCardeal" );
$obCmbPontoCardeal->setRotulo     ( "Ponto Cardeal"        );
$obCmbPontoCardeal->setStyle      ( "width: 150px"         );
$obCmbPontoCardeal->setCampoId    ( "cod_ponto"            );
$obCmbPontoCardeal->setCampoDesc  ( "nom_ponto"            );
$obCmbPontoCardeal->preencheCombo ( $rsListaPontosCardeais );

$obRdoTipoTrecho = new Radio;
$obRdoTipoTrecho->setName   ( "stTipoConfrotacao"    );
$obRdoTipoTrecho->setId     ( "stTipoConfrontacaoTrecho");
$obRdoTipoTrecho->setLabel  ( "Trecho"               );
$obRdoTipoTrecho->setValue  ( "trecho"               );
$obRdoTipoTrecho->setRotulo ( "*Tipo"                );
$obRdoTipoTrecho->setTitle  ( "Tipo de confrontação" );
$obRdoTipoTrecho->setChecked ( true );
$obRdoTipoTrecho->obEvento->setOnChange( "javascript: montaConfrontacao( 'trecho' );" );

$obRdoTipoLote = new Radio;
$obRdoTipoLote->setName  ( "stTipoConfrotacao" );
$obRdoTipoLote->setId    ( "stTipoConfrontacaoLote");
$obRdoTipoLote->setLabel ( "Lote"              );
$obRdoTipoLote->setValue ( "lote"              );
$obRdoTipoLote->obEvento->setOnChange( "javascript: montaConfrontacao( 'lote' );" );

$obRdoTipoOutros = new Radio;
$obRdoTipoOutros->setName  ( "stTipoConfrotacao" );
$obRdoTipoOutros->setId    ( "stTipoConfrontacaoOutros");
$obRdoTipoOutros->setLabel ( "Outros"            );
$obRdoTipoOutros->setValue ( "outros"            );
$obRdoTipoOutros->obEvento->setOnChange( "javascript: montaConfrontacao( 'outros' );" );

$obTxtExtensao = new Numerico;
$obTxtExtensao->setName      ( "flExtensao" );
$obTxtExtensao->setRotulo    ( "*Extensão"  );
$obTxtExtensao->setNegativo  ( false        );
$obTxtExtensao->setSize      ( 10           );
$obTxtExtensao->setMaxlength ( 10           );
$obTxtExtensao->setTitle     ( "Extensão em metros da confrontação" );

$obTxtDescricaoOutros = new TextArea;
$obTxtDescricaoOutros->setName     ( "stDescricaoOutros" );
$obTxtDescricaoOutros->setRotulo   ( "Descrição"        );
$obTxtDescricaoOutros->setCols     ( 50                 );
$obTxtDescricaoOutros->setRows     ( 5                  );
$obTxtDescricaoOutros->setMaxCaracteres( 500            );

$obBtnIncluirConfrontacao = new Button;
$obBtnIncluirConfrontacao->setName( "btnIncluirTrecho" );
$obBtnIncluirConfrontacao->setValue( "Incluir" );
$obBtnIncluirConfrontacao->obEvento->setOnClick( "incluirConfrontacao();" );

$obBtnLimparConfrontacao = new Button;
$obBtnLimparConfrontacao->setName( "btnLimparConfrontacao" );
$obBtnLimparConfrontacao->setValue( "Limpar" );
$obBtnLimparConfrontacao->obEvento->setOnClick( "limparConfrontacao();" );

$obSpnConfrontacao = new Span;
$obSpnConfrontacao->setId( "spnConfrontacao" );

$obSpnListaConfrontacao = new Span;
$obSpnListaConfrontacao->setId( "lsListaConfrontacoes" );

$obBtnOK = new OK;

$onBtnLimpar = new Limpar;
$onBtnLimpar->setValue("Limpar");
$onBtnLimpar->obEvento->setOnClick( "limparFormulario();" );

//DEFINICAO DO FORM

$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

$obFormulario = new FormularioAbas;
$obFormulario->addForm            ( $obForm    );
$obFormulario->setAjuda ( "UC-05.01.08" );
$obFormulario->addHidden          ( $obHdnCtrl );
$obFormulario->addHidden          ( $obHdnFuncionalidade   );
$obFormulario->addHidden          ( $obHdnAcao             );
$obFormulario->addHidden          ( $obHdnAcaoConfrontacao );
$obFormulario->addHidden          ( $obHdMascaraLote       );
$obFormulario->addHidden          ( $obHdnTrecho           );
$obFormulario->addHidden          ( $obHdnCodigoUF         );
$obFormulario->addHidden          ( $obHdnCodigoMunicipio  );
$obFormulario->addAba             ( "Lote"                 );
$obFormulario->addTitulo          ( "Dados para lote"      );
$obFormulario->addComponente      ( $obTxtNumeroLote       );
if ($_REQUEST['stAcao'] == "incluir") {
    $obMontaLocalizacao->geraFormulario ( $obFormulario );
}
$obFormulario->agrupaComponentes  ( array( $obTxtAreaLote , $obCmbUnidadeMedida ) );
$obFormulario->addComponente      ( $obTxtProfundidadeMedia );
$obFormulario->addComponente      ( $obTxtDataInscricaoLote );
$obFormulario->addComponente      ( $obBscProcesso          );
$obFormulario->addComponente      ( $obBscBairro            );
$obFormulario->addAba             ( "Confrontações"         );
$obFormulario->addFuncaoAba       ( "atualizaComponente();"   );
$obFormulario->addTitulo          ( "Confrontações"         );
$obFormulario->addComponente      ( $obCmbPontoCardeal      );
$obFormulario->agrupaComponentes  ( array( $obRdoTipoTrecho, $obRdoTipoLote, $obRdoTipoOutros) );
$obFormulario->addComponente      ( $obTxtExtensao          );
$obFormulario->addspan            ( $obSpnConfrontacao      );
$obFormulario->defineBarraAba     ( array( $obBtnIncluirConfrontacao, $obBtnLimparConfrontacao ),"","" );
$obFormulario->addspan            ( $obSpnListaConfrontacao );
$obFormulario->addAba             ( "Características" );
$obFormulario->addFuncaoAba       ( "atualizaComponente();"   );
$obFormulario->addTitulo          ( "Características do lote" );
$obMontaAtributos->geraFormulario ( $obFormulario     );
$obFormulario->addDiv( 4, "componente" );

//verificar se existe permissao
$rsPermissao = new RecordSet();
include_once( CAM_GA_ADM_MAPEAMENTO."TAdministracaoAcao.class.php" );
$obTAdministracaoAcao = NEW TAdministracaoAcao();
$obTAdministracaoAcao->setDado("cod_acao", 738);
$obTAdministracaoAcao->recuperaPermissao($rsPermissao );
if ( !$rsPermissao->eof() ) {
    $obFormulario->addComponente      ( $obChkSeguir            );
}
$obFormulario->fechaDiv();
$obFormulario->defineBarra( array( $obBtnOK , $onBtnLimpar ) );
/*  Lucas Stephanou 22/03/2005
    Adicionado para dar foco ao textbox do numero do lote
*/

$obFormulario->setFormFocus($obTxtNumeroLote->getId());
$obFormulario->show();

SistemaLegado::executaFrameOculto( "javascript: montaConfrontacao( 'trecho' );" );
?>
