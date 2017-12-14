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
    * Página de Formulario de Calculo ISS

    * Data de Criação   : 13/10/2006

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato
    * @ignore

    * $Id: FMEscriturarReceita.php 59927 2014-09-22 17:45:59Z lisiane $

    *Casos de uso: uc-05.03.22

*/

/*
$Log$
Revision 1.5  2007/10/02 18:48:04  cercato
Ticket#10305#

Revision 1.4  2007/07/05 15:25:15  cercato
Bug #9571#

Revision 1.3  2006/12/18 16:25:44  cercato
Bug #7859#

Revision 1.2  2006/11/22 13:02:29  cercato
bug #7573#

Revision 1.1  2006/10/26 14:04:28  cercato
*** empty log message ***

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

//Define o nome dos arquivos PHP
$stPrograma    = "EscriturarReceita";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgForm        = "FM".$stPrograma.".php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php?".Sessao::getId();
$pgJs          = "JS".$stPrograma.".js";

include_once( $pgJs );

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $stAcao  );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName   ( "stCtrl" );
$obHdnCtrl->setValue  ( $stCtrl  );

$obHdnInscricaoEconomica =  new Hidden;
$obHdnInscricaoEconomica->setName   ( "inInscricaoEconomica" );
$obHdnInscricaoEconomica->setValue  ( $_REQUEST["inInscricaoEconomica"] );

$obHdnCGM = new Hidden;
$obHdnCGM->setName ( "inNumCGM" );
$obHdnCGM->setValue ( $_REQUEST["inNumCGM"] );

$obHdnAtividade = new Hidden;
$obHdnAtividade->setName ( "inCodAtividade" );
$obHdnAtividade->setValue ( $_REQUEST["inCodAtividade"] );

$obHdnModalidade = new Hidden;
$obHdnModalidade->setName ( "inCodModalidade" );
$obHdnModalidade->setValue ( $_REQUEST["inCodModalidade"] );

$obHdnEscrituracao = new Hidden;
$obHdnEscrituracao->setName ( "stEscrituracao" );
$obHdnEscrituracao->setValue ( $_REQUEST["stEscrituracao"] );

$obLblInscricaoEconomica = new Label;
$obLblInscricaoEconomica->setRotulo    ( "Inscrição Econômica" );
$obLblInscricaoEconomica->setName      ( "stInscricaoEconomica" );
$obLblInscricaoEconomica->setId        ( "stInscricaoEconomica" );
$obLblInscricaoEconomica->setValue     ( $_REQUEST["inInscricaoEconomica"] );

$obLblCGMdoPrestador = new Label;
$obLblCGMdoPrestador->setRotulo    ( "CGM do Prestador" );
$obLblCGMdoPrestador->setName      ( "stLblCGM" );
$obLblCGMdoPrestador->setId        ( "stLblCGM" );
$obLblCGMdoPrestador->setValue     ( $_REQUEST["inNumCGM"]." - ".$_REQUEST["stNomCGM"] );

$obLblAtividade = new Label;
$obLblAtividade->setRotulo    ( "Atividade" );
$obLblAtividade->setName      ( "stAtividade" );
$obLblAtividade->setId        ( "stAtividade" );
if ( $_REQUEST["inCodAtividade"] )
    $obLblAtividade->setValue     ( $_REQUEST["inCodAtividade"]." - ".$_REQUEST["stNomAtividade"] );

$obLblModalidade = new Label;
$obLblModalidade->setRotulo    ( "Modalidade" );
$obLblModalidade->setName      ( "stModalidade" );
$obLblModalidade->setId        ( "stModalidade" );
$obLblModalidade->setValue     ( $_REQUEST["inCodModalidade"]." - ".$_REQUEST["stNomModalidade"] );

$obDtEmissao = new Data;
$obDtEmissao->setName ( "dtEmissao" );
$obDtEmissao->setRotulo ( "*Data da Emissão" );
$obDtEmissao->setMaxLength ( 20 );
$obDtEmissao->setSize ( 10 );
$obDtEmissao->setNull ( true );
$obDtEmissao->obEvento->setOnChange( "buscaValor('validaData');" );

$obChkReterFonte = new CheckBox;
$obChkReterFonte->setName    ( "boReterFonte" );
$obChkReterFonte->setValue   ( "1" );
$obChkReterFonte->setLabel   ( "Faturamento com Retenção na Fonte" );
$obChkReterFonte->setNull    ( true );
$obChkReterFonte->setChecked ( false );
$obChkReterFonte->obEvento->setOnChange( "buscaValor('montaRetencao');" );

$arDadosCompetencia = array();
$arDadosCompetencia[0]["cod_competencia"] = '01';
$arDadosCompetencia[0]["descricao"] = "Janeiro";
$arDadosCompetencia[1]["cod_competencia"] = '02';
$arDadosCompetencia[1]["descricao"] = "Fevereiro";
$arDadosCompetencia[2]["cod_competencia"] = '03';
$arDadosCompetencia[2]["descricao"] = "Março";
$arDadosCompetencia[3]["cod_competencia"] = '04';
$arDadosCompetencia[3]["descricao"] = "Abril";
$arDadosCompetencia[4]["cod_competencia"] = '05';
$arDadosCompetencia[4]["descricao"] = "Maio";
$arDadosCompetencia[5]["cod_competencia"] = '06';
$arDadosCompetencia[5]["descricao"] = "Junho";
$arDadosCompetencia[6]["cod_competencia"] = '07';
$arDadosCompetencia[6]["descricao"] = "Julho";
$arDadosCompetencia[7]["cod_competencia"] = '08';
$arDadosCompetencia[7]["descricao"] = "Agosto";
$arDadosCompetencia[8]["cod_competencia"] = '09';
$arDadosCompetencia[8]["descricao"] = "Setembro";
$arDadosCompetencia[9]["cod_competencia"] = '10';
$arDadosCompetencia[9]["descricao"] = "Outubro";
$arDadosCompetencia[10]["cod_competencia"] = '11';
$arDadosCompetencia[10]["descricao"] = "Novembro";
$arDadosCompetencia[11]["cod_competencia"] = '12';
$arDadosCompetencia[11]["descricao"] = "Dezembro";

$rsCompetencia = new RecordSet;
$rsCompetencia->preenche( $arDadosCompetencia );

$obCmbCompetencia = new Select;
$obCmbCompetencia->setName               ( "stCompetencia" );
$obCmbCompetencia->setRotulo             ( "Competência" );
$obCmbCompetencia->setNull               ( false );
$obCmbCompetencia->setCampoId            ( "cod_competencia" );
$obCmbCompetencia->setCampoDesc          ( "descricao" );
$obCmbCompetencia->addOption             ( "", "Selecione" );
$obCmbCompetencia->preencheCombo         ( $rsCompetencia );
if ($_REQUEST["stEscrituracao"] != "smov") {
    $obCmbCompetencia->obEvento->setOnChange( "buscaValor('alteraCompetencia');" );
}

$obTxtExercicio = new Exercicio;
$obTxtExercicio->setTitle ( "Informe o exercício." );
$obTxtExercicio->setName ( "stExercicio" );
$obTxtExercicio->setNull ( false );
$obTxtExercicio->obEvento->setOnChange( "buscaValor('alteraCompetencia');" );

$obSpn1 = new Span;
$obSpn1->setID("spn1");

$obTxtSerie = new TextBox;
$obTxtSerie->setRotulo ( "*Série" );
$obTxtSerie->setName ( "inSerie" );
$obTxtSerie->setValue ( $inSerie );
$obTxtSerie->setInteiro ( false );
$obTxtSerie->setNull ( true );

$obTxtNumeroDaNota = new TextBox;
$obTxtNumeroDaNota->setRotulo ( "*Número da Nota" );
$obTxtNumeroDaNota->setName ( "inNumeroNota" );
$obTxtNumeroDaNota->setValue ( $inNumeroNota );
$obTxtNumeroDaNota->setInteiro ( true );
$obTxtNumeroDaNota->setNull ( true );

//botoes do Servico
$obBtnIncluirServico = new Button;
$obBtnIncluirServico->setName              ( "btnIncluirServico" );
$obBtnIncluirServico->setValue             ( "Incluir" );
$obBtnIncluirServico->setTipo              ( "button" );
$obBtnIncluirServico->obEvento->setOnClick ( "incluirServico();" );
$obBtnIncluirServico->setDisabled          ( false );

$obBtnLimparServico = new Button;
$obBtnLimparServico->setName               ( "btnLimparServico" );
$obBtnLimparServico->setValue              ( "Limpar" );
$obBtnLimparServico->setTipo               ( "button" );
$obBtnLimparServico->obEvento->setOnClick  ( "buscaValor('limpaServico');" );
$obBtnLimparServico->setDisabled           ( false );

$botoesServico = array ( $obBtnIncluirServico , $obBtnLimparServico  );

$obSpnListaServico = new Span;
$obSpnListaServico->setID("spnListaServico");

//botoes Nota
$obBtnIncluirNota = new Button;
$obBtnIncluirNota->setName              ( "btnIncluirNota" );
$obBtnIncluirNota->setValue             ( "Incluir" );
$obBtnIncluirNota->setTipo              ( "button" );
$obBtnIncluirNota->obEvento->setOnClick ( "incluirNota();" );
$obBtnIncluirNota->setDisabled          ( false );

$obBtnLimparNota = new Button;
$obBtnLimparNota->setName               ( "btnLimparNota" );
$obBtnLimparNota->setValue              ( "Limpar" );
$obBtnLimparNota->setTipo               ( "button" );
$obBtnLimparNota->obEvento->setOnClick  ( "buscaValor('limpaNota');" );
$obBtnLimparNota->setDisabled           ( false );

$botoesNota = array ( $obBtnIncluirNota , $obBtnLimparNota );

$obSpnListaNota = new Span;
$obSpnListaNota->setID("spnListaNota");

$obSpnCarne = new Span;
$obSpnCarne->setID("spnCarne");

$obSpnData = new Span;
$obSpnData->setID("spnData");

$obBtnOK = new OK;

if ( $_REQUEST['stNomModalidade'] =='' ) {
    $obBtnOK->setDisabled(true);
}

$obBtnLimpar = new Button;
$obBtnLimpar->setName              ( "btnLimpar" );
$obBtnLimpar->setValue             ( "Limpar" );
$obBtnLimpar->setTipo              ( "button" );
if ($_REQUEST["stEscrituracao"] != "smov") {
    $obBtnLimpar->obEvento->setOnClick ( "LimparForm();" );
} else {
    $obBtnLimpar->obEvento->setOnClick ( "LimparForm2();" );
}

$obBtnLimpar->setDisabled          ( false );

$obBtnCancelar = new Button;
$obBtnCancelar->setName              ( "btnCancelar" );
$obBtnCancelar->setValue             ( "Cancelar" );
$obBtnCancelar->setTipo              ( "button" );
$obBtnCancelar->obEvento->setOnClick("Cancelar('".$pgList."?".Sessao::getId()."&stAcao=".substr($_REQUEST['stAcao'],0,strlen($_REQUEST['stAcao'])-1)."','telaPrincipal');");

$botoesBarra = array ( $obBtnOK , $obBtnLimpar , $obBtnCancelar);

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction ( $pgProc );
$obForm->settarget ( "oculto" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm );
$obFormulario->setAjuda      ( "UC-05.03.22" );
$obFormulario->addHidden     ( $obHdnAcao );
$obFormulario->addHidden     ( $obHdnCtrl );
$obFormulario->addHidden     ( $obHdnInscricaoEconomica );
$obFormulario->addHidden     ( $obHdnCGM );
$obFormulario->addHidden     ( $obHdnAtividade );
$obFormulario->addHidden     ( $obHdnModalidade );
$obFormulario->addHidden     ( $obHdnEscrituracao );
$obFormulario->addTitulo     ( "Dados para Avaliação de Faturamento" );

$obFormulario->addComponente ( $obLblInscricaoEconomica );
$obFormulario->addComponente ( $obLblCGMdoPrestador );
$obFormulario->addComponente ( $obLblAtividade );
$obFormulario->addComponente ( $obLblModalidade );
$obFormulario->addComponente ( $obTxtExercicio );
$obFormulario->addComponente ( $obCmbCompetencia );
if ($_REQUEST["stEscrituracao"] == "nota") {
    $obFormulario->addComponente ( $obTxtSerie );
    $obFormulario->addComponente ( $obTxtNumeroDaNota );
}

if ($_REQUEST["stEscrituracao"] != "smov") {
    $obFormulario->addComponente ( $obDtEmissao );
    $obFormulario->addComponente ( $obChkReterFonte );
    $obFormulario->addSpan ( $obSpn1 );
    $obFormulario->defineBarra   ( $botoesServico, 'left', '' );
    $obFormulario->addSpan       ( $obSpnListaServico );
}

if ($_REQUEST["stEscrituracao"] == "nota") {
    $obFormulario->defineBarra ( $botoesNota, 'left', '' );
    $obFormulario->addSpan ( $obSpnListaNota );
}

if ($_REQUEST["stEscrituracao"] != "smov") {
    $obFormulario->addSpan       ( $obSpnData );
    $obFormulario->addSpan       ( $obSpnCarne );
}

$obFormulario->defineBarra( $botoesBarra );
$obFormulario->show();

if ($_REQUEST["stEscrituracao"] != "smov") {
    sistemaLegado::executaFrameOculto("buscaValor('montaRetencao');");
}
if ( $_REQUEST['stNomModalidade'] =='' ) {
    SistemaLegado::exibeAviso( "Necessário definir a modalidade da atividade.", "", "");
    exit;
}

Sessao::write( "servicos_retencao", array() );
Sessao::write( "servicos_retencao_alterando", "" );
Sessao::write( "servicos_retencao_comrt", array() );
Sessao::write( "notas_retencao_comrt", array() );
Sessao::write( "servicos_retencao_alterando_comrt", "" );
Sessao::write( "servicos_retencao_semrt", array() );
Sessao::write( "notas_retencao_semrt", array() );
Sessao::write( "servicos_retencao_alterando_semrt", "" );
