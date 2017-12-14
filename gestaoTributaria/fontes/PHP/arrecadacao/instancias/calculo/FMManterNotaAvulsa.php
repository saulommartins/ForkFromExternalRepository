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
    * Página de Formulario Nota Avulsa

    * Data de Criação   : 20/06/2008

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato
    * @ignore

    * $Id: $

    *Casos de uso: uc-05.03.22

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_ARR_NEGOCIO."RARRCarne.class.php" );

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

//Define o nome dos arquivos PHP
$stPrograma    = "ManterNotaAvulsa";
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
$obHdnCtrl->setValue  ( $request->get("stCtrl")  );

$obHdnInscricaoEconomica =  new Hidden;
$obHdnInscricaoEconomica->setName   ( "inInscricaoEconomica" );
$obHdnInscricaoEconomica->setValue  ( $request->get("inInscricaoEconomica") );

$obHdnCGM = new Hidden;
$obHdnCGM->setName ( "inNumCGM" );
$obHdnCGM->setValue ( $request->get("inNumCGM") );

$obHdnAtividade = new Hidden;
$obHdnAtividade->setName ( "inCodAtividade" );
$obHdnAtividade->setValue ( $request->get("inCodAtividade") );

$obHdnModalidade = new Hidden;
$obHdnModalidade->setName ( "inCodModalidade" );
$obHdnModalidade->setValue ( $request->get("inCodModalidade") );

$obDtEmissao = new Data;
$obDtEmissao->setName ( "dtEmissao" );
$obDtEmissao->setRotulo ( "*Data da Emissão" );
$obDtEmissao->setMaxLength ( 20 );
$obDtEmissao->setSize ( 10 );
$obDtEmissao->setNull ( true );
$obDtEmissao->setValue ( date("d/m/Y") );
$obDtEmissao->obEvento->setOnChange( "buscaValor('validaData');" );

$obLblInscricaoEconomica = new Label;
$obLblInscricaoEconomica->setRotulo    ( "Inscrição Econômica" );
$obLblInscricaoEconomica->setName      ( "stInscricaoEconomica" );
$obLblInscricaoEconomica->setId        ( "stInscricaoEconomica" );
$obLblInscricaoEconomica->setValue     ( $request->get("inInscricaoEconomica") );

$obLblCGMdoPrestador = new Label;
$obLblCGMdoPrestador->setRotulo    ( "CGM do Prestador" );
$obLblCGMdoPrestador->setName      ( "stLblCGM" );
$obLblCGMdoPrestador->setId        ( "stLblCGM" );
$obLblCGMdoPrestador->setValue     ( $request->get("inNumCGM")." - ".$request->get("stNomCGM") );

$obLblAtividade = new Label;
$obLblAtividade->setRotulo    ( "Atividade" );
$obLblAtividade->setName      ( "stAtividade" );
$obLblAtividade->setId        ( "stAtividade" );
if ( $request->get("inCodAtividade") )
    $obLblAtividade->setValue     ( $request->get("inCodAtividade")." - ".$request->get("stNomAtividade") );

$obLblModalidade = new Label;
$obLblModalidade->setRotulo    ( "Modalidade" );
$obLblModalidade->setName      ( "stModalidade" );
$obLblModalidade->setId        ( "stModalidade" );
$obLblModalidade->setValue     ( $request->get("inCodModalidade")." - ".$request->get("stNomModalidade") );

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
$obCmbCompetencia->obEvento->setOnChange( "buscaValor('alteraCompetencia');" );

$obTxtExercicio = new Exercicio;
$obTxtExercicio->setTitle ( "Informe o exercício." );
$obTxtExercicio->setName ( "stExercicio" );
$obTxtExercicio->setNull ( false );
$obTxtExercicio->obEvento->setOnChange( "buscaValor('alteraCompetencia');" );

$obTomadorServicoCGM = new BuscaInner;
$obTomadorServicoCGM->setRotulo ( "Tomador de Serviços" );
$obTomadorServicoCGM->setTitle ( "Informe o CGM do tomador de serviços." );
$obTomadorServicoCGM->setId ( "stCGM" );
$obTomadorServicoCGM->setNull( false );
$obTomadorServicoCGM->obCampoCod->setName ( "inCGM" );
$obTomadorServicoCGM->obCampoCod->obEvento->setOnBlur( "buscaValor('PreencheCGM');" );
$obTomadorServicoCGM->setFuncaoBusca( "abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inCGM','stCGM','','".Sessao::getId()."','800','450');" );

$obSpn1 = new Span;
$obSpn1->setID("spn1");

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

$obTxtObservacaoNF = new TextArea;
$obTxtObservacaoNF->setName ( "stObservacaoNF" );
$obTxtObservacaoNF->setRotulo ( "Observações p/ Nota" );
$obTxtObservacaoNF->setTitle ( "Observações para o contribuinte." );
$obTxtObservacaoNF->setValue ( "" );
$obTxtObservacaoNF->setNull  ( true );
$obTxtObservacaoNF->setCols ( 30 );
$obTxtObservacaoNF->setRows ( 5 );
$obTxtObservacaoNF->setMaxCaracteres(400);

$botoesServico = array ( $obBtnIncluirServico , $obBtnLimparServico );

$obSpnListaServico = new Span;
$obSpnListaServico->setID("spnListaServico");

$obSpnCarne = new Span;
$obSpnCarne->setID("spnCarne");

$obSpnData = new Span;
$obSpnData->setID("spnData");

$obSpnEmpresaTomador = new Span;
$obSpnEmpresaTomador->setID("spnEmpresaTomador");

$obBtnOK = new OK;

$obBtnLimpar = new Button;
$obBtnLimpar->setName              ( "btnLimpar" );
$obBtnLimpar->setValue             ( "Limpar" );
$obBtnLimpar->setTipo              ( "button" );
$obBtnLimpar->obEvento->setOnClick ( "LimparForm();" );
$obBtnLimpar->setDisabled          ( false );

$botoesBarra = array ( $obBtnOK , $obBtnLimpar );

$obTxtObservacao = new TextArea;
$obTxtObservacao->setName ( "stObservacao" );
$obTxtObservacao->setRotulo ( "Observações p/ Boleto" );
$obTxtObservacao->setTitle ( "Observações para o contribuinte." );
$obTxtObservacao->setValue ( "" );
$obTxtObservacao->setNull  ( true );
$obTxtObservacao->setCols ( 30 );
$obTxtObservacao->setRows ( 5 );
$obTxtObservacao->setMaxCaracteres(400);

$obChkEmissaoCarne = new CheckBox;
$obChkEmissaoCarne->setName    ( "boEmissaoCarne" );
$obChkEmissaoCarne->setValue   ( "1" );
$obChkEmissaoCarne->setRotulo ( "Emissão de Carnês" );
$obChkEmissaoCarne->setLabel   ( "Impressão Local" );
$obChkEmissaoCarne->setNull    ( true );
$obChkEmissaoCarne->setChecked ( true );

$obRARRCarne = new RARRCarne;
$obRARRCarne->listarModeloDeCarne( $rsModelos, Sessao::read('acao') );

$obCmbModelo =  new Select;
$obCmbModelo->setRotulo        ( "*Modelo de Carnê" );
$obCmbModelo->setName          ( "stArquivo" );
$obCmbModelo->setStyle         ( "width: 200px");
$obCmbModelo->setCampoID       ( "[nom_arquivo]§[cod_modelo]" );
$obCmbModelo->setCampoDesc     ( "nom_modelo" );
$obCmbModelo->addOption        ( "", "Selecione" );
$obCmbModelo->setNull          ( true );
$obCmbModelo->preencheCombo    ( $rsModelos );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction ( $pgProc );
$obForm->settarget ( "oculto" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm );
$obFormulario->addHidden     ( $obHdnAcao );
$obFormulario->addHidden     ( $obHdnCtrl );
$obFormulario->addHidden     ( $obHdnInscricaoEconomica );
$obFormulario->addHidden     ( $obHdnCGM );
$obFormulario->addHidden     ( $obHdnAtividade );
$obFormulario->addHidden     ( $obHdnModalidade );
$obFormulario->addTitulo     ( "Dados para Avaliação de Faturamento" );

$obFormulario->addComponente ( $obLblInscricaoEconomica );
$obFormulario->addComponente ( $obLblCGMdoPrestador );
$obFormulario->addComponente ( $obLblAtividade );
$obFormulario->addComponente ( $obLblModalidade );
$obFormulario->addComponente ( $obTxtExercicio );
$obFormulario->addComponente ( $obCmbCompetencia );
$obFormulario->addComponente ( $obTomadorServicoCGM );
$obFormulario->addSpan ( $obSpnEmpresaTomador );
$obFormulario->addComponente ( $obDtEmissao );

$obFormulario->addSpan ( $obSpn1 );
$obFormulario->defineBarra   ( $botoesServico, 'left', '' );
$obFormulario->addSpan       ( $obSpnListaServico );
$obFormulario->addSpan       ( $obSpnData );

$obFormulario->addComponente ( $obTxtObservacaoNF );
$obFormulario->addComponente ( $obTxtObservacao );
$obFormulario->addComponente ( $obChkEmissaoCarne );
$obFormulario->addComponente ( $obCmbModelo );

$obFormulario->defineBarra( $botoesBarra );
$obFormulario->show();

sistemaLegado::executaFrameOculto("buscaValor('montaRetencao');");

Sessao::write( "servicos_retencao", array() );
Sessao::write( "servicos_retencao_alterando", "" );

Sessao::write( "servicos_retencao_semrt", array() );
Sessao::write( "servicos_retencao_alterando_semrt", "" );
