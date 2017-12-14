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
    * Página de Levantamento fiscal por serviço
    * Data de Criacao: 13/08/2008

    * @author Analista      : Heleno Menezes dos Santos
    * @author Desenvolvedor : Jânio Eduardo Vasconcellos de Magalhães

    * @package URBEM
    * @subpackage Formulario

    *Casos de uso:

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

//Define o nome dos arquivos PHP
$stPrograma    = "ManterServico";
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

//Cod. Tipo Fiscalização
$obHdnInTipoFiscalizacao = new Hidden();
$obHdnInTipoFiscalizacao->setName( "inTipoFiscalizacao" );
$obHdnInTipoFiscalizacao->setValue( $_REQUEST['inTipoFiscalizacao'] );

//Cod. Processo Fiscal
$obHdnInCodProcesso = new Hidden();
$obHdnInCodProcesso->setName( "inCodProcesso" );
$obHdnInCodProcesso->setId( "inCodProcesso" );
$obHdnInCodProcesso->setValue( $_REQUEST['inCodProcesso'] );

//Cod. Inscricao
$obHdnInIncricao = new Hidden();
$obHdnInIncricao->setName( "inInscricao" );
$obHdnInIncricao->setId( "inIncricao" );
$obHdnInIncricao->setValue( $_REQUEST['inInscricao']);

//Cod. Inicio
$obHdnInInicio = new Hidden();
$obHdnInInicio->setName( "inInicio" );
$obHdnInInicio->setId( "inInicio" );
$obHdnInInicio->setValue( $_REQUEST['inInicio']);

//Cod. Termino
$obHdnInTermino = new Hidden();
$obHdnInTermino->setName( "inTermino" );
$obHdnInTermino->setId( "inTermino" );
$obHdnInTermino->setValue( $_REQUEST['inTermino']);

//Cod. controle reter
$obHdnInReter = new Hidden();
$obHdnInReter->setName( "boReter" );
$obHdnInReter->setId( "boReter" );

//cod_atividade
$obHdnInAtividade = new Hidden();
$obHdnInAtividade->setName( "inCodAtividade" );
$obHdnInAtividade->setId( "inCodAtividade" );
$obHdnInAtividade->setValue( $_REQUEST['inCodAtividade']  );

//cod_modalidade
$obHdnInModalidade = new Hidden();
$obHdnInModalidade->setName( "inModalidade" );
$obHdnInModalidade->setId( "inModalidade" );
$obHdnInModalidade->setValue( $_REQUEST['inCodModalidade']  );

//Tipo Fiscalizacao
$obTipoFiscalizacao = new Label;
$obTipoFiscalizacao->setRotulo( "Tipo de Fiscalização" );
$obTipoFiscalizacao->setName( "stTipoFiscalizacao" );
$obTipoFiscalizacao->setId( "stTipoFiscalizacao" );
$obTipoFiscalizacao->setValue( "01 - Fiscalização Tributária do ISSQN");

//Processo Fiscal
$obProcessoFiscal = new Label;
$obProcessoFiscal->setRotulo( "Processo Fiscal" );
$obProcessoFiscal->setName( "inProcessoFiscal" );
$obProcessoFiscal->setId( "inProcessoFiscal" );
$obProcessoFiscal->setValue($_REQUEST['inCodProcesso'] );

//Inscricao Economica
$obInscricaoEconomica = new Label();
$obInscricaoEconomica->setRotulo( "Inscrição Econômica" );
$obInscricaoEconomica->setName( "stInscricaoEconomica" );
$obInscricaoEconomica->setValue( $_REQUEST['inInscricao'] );

//atividade
$obAtividade = new Label();
$obAtividade->setRotulo( "Atividade" );
$obAtividade->setName( "stAtividade" );
$obAtividade->setValue($_REQUEST['inNomAtividade'] );

//modalidade
$obModalidade = new Label();
$obModalidade->setRotulo( "Modalidade de Lançamento" );
$obModalidade->setName( "stModalidade" );
if (!$_REQUEST['inNomModalidade']) {
    $_REQUEST['inNomModalidade'] = "Não definida";
}
$obModalidade->setValue(  $_REQUEST['inNomModalidade'] );

//Emissão
$obDtEmissao = new Data;
$obDtEmissao->setName      ( "dtEmissao" );
$obDtEmissao->setRotulo    ( "*Data da Emissão" );
$obDtEmissao->setTitle     ( "Data em que foi efetuado o serviço" );
$obDtEmissao->setMaxLength ( 20 );
$obDtEmissao->setSize      ( 10 );
$obDtEmissao->setNull      ( false );
$obDtEmissao->obEvento->setOnChange( "buscaValor('validaData');" );

//controlador do span
$obChkReterFonte = new CheckBox;
$obChkReterFonte->setName    ( "boReterFonte" );
$obChkReterFonte->setId    ( "boReterFonte" );
$obChkReterFonte->setValue   ( 1 );
$obChkReterFonte->setLabel   ( "Faturamento com Retenção na Fonte" );
$obChkReterFonte->setNull    ( true );
$obChkReterFonte->setChecked ( false );
$obChkReterFonte->obEvento->setOnChange( "buscaValor('montaRetencao');" );

$arDadosCompetencia = array();
$arDadosCompetencia[0]["cod_competencia"] = 1;
$arDadosCompetencia[0]["descricao"] = "Janeiro";
$arDadosCompetencia[1]["cod_competencia"] = 2;
$arDadosCompetencia[1]["descricao"] = "Fevereiro";
$arDadosCompetencia[2]["cod_competencia"] = 3;
$arDadosCompetencia[2]["descricao"] = "Março";
$arDadosCompetencia[3]["cod_competencia"] = 4;
$arDadosCompetencia[3]["descricao"] = "Abril";
$arDadosCompetencia[4]["cod_competencia"] = 5;
$arDadosCompetencia[4]["descricao"] = "Maio";
$arDadosCompetencia[5]["cod_competencia"] = 6;
$arDadosCompetencia[5]["descricao"] = "Junho";
$arDadosCompetencia[6]["cod_competencia"] = 7;
$arDadosCompetencia[6]["descricao"] = "Julho";
$arDadosCompetencia[7]["cod_competencia"] = 8;
$arDadosCompetencia[7]["descricao"] = "Agosto";
$arDadosCompetencia[8]["cod_competencia"] = 9;
$arDadosCompetencia[8]["descricao"] = "Setembro";
$arDadosCompetencia[9]["cod_competencia"] = 10;
$arDadosCompetencia[9]["descricao"] = "Outubro";
$arDadosCompetencia[10]["cod_competencia"] = 11;
$arDadosCompetencia[10]["descricao"] = "Novembro";
$arDadosCompetencia[11]["cod_competencia"] = 12;
$arDadosCompetencia[11]["descricao"] = "Dezembro";

$rsCompetencia = new RecordSet;
$rsCompetencia->preenche( $arDadosCompetencia );

$obCmbCompetencia = new Select;
$obCmbCompetencia->setName               ( "stCompetencia" );
$obCmbCompetencia->setRotulo             ( "Competência" );
$obCmbCompetencia->setTitle              ( "Competência a ser declarada" );
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
$obTxtExercicio->setValue ( null );
$obTxtExercicio->obEvento->setOnChange( "buscaValor('validaExercicio');" );

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
$obBtnIncluirServico->obEvento->setOnClick ( "incluirServicoLista();" );
$obBtnIncluirServico->setDisabled          ( false );

$obBtnLimparServico = new Button;
$obBtnLimparServico->setName               ( "btnLimparServico" );
$obBtnLimparServico->setValue              ( "Limpar" );
$obBtnLimparServico->setTipo               ( "button" );
$obBtnLimparServico->obEvento->setOnClick  ( "buscaValor('limpaServico');" );
$obBtnLimparServico->setDisabled           ( false );

$botoesServico = array ( $obBtnIncluirServico , $obBtnLimparServico );

$obSpnListaServico = new Span;
$obSpnListaServico->setID("spnListaServico");

$obTxtValorMercadoria = new Moeda;
$obTxtValorMercadoria->setName               ( "flValorMercadoria" );
$obTxtValorMercadoria->setRotulo             ( "Dedução Legal" );
$obTxtValorMercadoria->setTitle              ( "Valor em Mercadoria/Material." );
$obTxtValorMercadoria->setMaxLength          ( 15                );
$obTxtValorMercadoria->setSize               ( 15                );
$obTxtValorMercadoria->setValue              ( $flValorMercadoria?$flValorMercadoria:'0,00' );
$obTxtValorMercadoria->setNULL               ( false );

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

$obBtnOK = new OK;

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

$botoesBarra = array ( $obBtnOK , $obBtnLimpar );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction ( $pgProc  );
$obForm->settarget ( "oculto" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm                               );
$obFormulario->addHidden     ( $obHdnAcao                            );
$obFormulario->addHidden     ( $obHdnCtrl                            );
$obFormulario->addHidden     ( $obHdnInTipoFiscalizacao              );
$obFormulario->addHidden     ( $obHdnInCodProcesso                   );
$obFormulario->addHidden     ( $obHdnInReter                         );
$obFormulario->addHidden     ( $obHdnInIncricao                      );
$obFormulario->addHidden     ( $obHdnInInicio 			     );
$obFormulario->addHidden     ( $obHdnInTermino 			     );
$obFormulario->addHidden     ( $obHdnInAtividade                     );
$obFormulario->addHidden     ( $obHdnInModalidade                    );
$obFormulario->addTitulo     ("Dados Para declaração de Lançamentos" );
$obFormulario->addComponente ( $obTipoFiscalizacao                   );
$obFormulario->addComponente ( $obProcessoFiscal                     );
$obFormulario->addComponente ( $obInscricaoEconomica                 );
$obFormulario->addComponente ( $obAtividade                          );
$obFormulario->addComponente ( $obModalidade                         );
$obFormulario->addComponente ( $obTxtExercicio                       );
$obFormulario->addComponente ( $obCmbCompetencia                     );
$obFormulario->addComponente ( $obDtEmissao                          );
$obFormulario->addComponente ( $obChkReterFonte                      );
$obFormulario->addSpan       ( $obSpn1                               );
$obFormulario->defineBarra   ( $botoesServico, 'left', ''            );
$obFormulario->addSpan       ( $obSpnListaServico                    );
$obFormulario->defineBarra   ( $botoesBarra                          );
$obFormulario->show();

sistemaLegado::executaFrameOculto("buscaValor('montaRetencao');");

Sessao::write( "servicos_retencao", array() );
Sessao::write( "servicos_retencao_alterando", "" );
Sessao::write( "servicos_retencao_comrt", array() );
Sessao::write( "notas_retencao_comrt", array() );
Sessao::write( "servicos_retencao_alterando_comrt", "" );
Sessao::write( "servicos_retencao_semrt", array() );
Sessao::write( "notas_retencao_semrt", array() );
Sessao::write( "servicos_retencao_alterando_semrt", "" );

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
