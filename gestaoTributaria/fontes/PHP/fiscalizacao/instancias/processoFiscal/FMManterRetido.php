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
    * Página de Levantamento fiscal com retenção
    * Data de Criacao: 18/08/2008

    * @author Analista      : Heleno Menezes dos Santos
    * @author Desenvolvedor : Jânio Eduardo Vasconcellos de Magalhães

    * @package URBEM
    * @subpackage Formulario

    *Casos de uso:

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLogradouro.class.php" );
include_once ( CAM_GT_CEM_COMPONENTES."MontaServico.class.php" );

//$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

//Define o nome dos arquivos PHP
$stPrograma    = "ManterRetido";
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
$obCmbCompetencia->obEvento->setOnChange ( "buscaValor('alteraCompetencia');" );

if ($_REQUEST["stEscrituracao"] != "smov") {
    $obCmbCompetencia->obEvento->setOnChange( "buscaValor('alteraCompetencia');" );
}

$obBscCGM = new BuscaInner;
$obBscCGM->setRotulo         ( "*CGM do Prestador" );
$obBscCGM->setId             ( "stCGM" );
$obBscCGM->setNull ( truee );
$obBscCGM->setTitle       ( "CGM do retentor do serviço" );
$obBscCGM->obCampoCod->setName       ( "inCGM" );
$obBscCGM->obCampoCod->obEvento->setOnChange( "buscaValor('PreencheCGM');" );
$obBscCGM->setFuncaoBusca( "abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inCGM','stCGM','','".Sessao::getId()."','800','450');" );

$rsUF = new RecordSet;
$obRCIMLogradouro = new RCIMLogradouro;
$obRCIMLogradouro->listarUF( $rsUF );

$rsMunicipios = new RecordSet;

$obTxtCodUF = new TextBox;
$obTxtCodUF->setRotulo             ( "*Estado"               );
 $obTxtCodUF->setTitle             ( "Estado do prestador de serviço" );
$obTxtCodUF->setName               ( "inCodigoUF"            );
$obTxtCodUF->setValue              ( $inCodigoUF             );
$obTxtCodUF->setSize               ( 8                       );
$obTxtCodUF->setMaxLength          ( 8                       );
$obTxtCodUF->setNull               ( true                    );
$obTxtCodUF->obEvento->setOnChange ( "buscaValor('preencheMunicipio')" );

$obCmbUF = new Select;
$obCmbUF->setName               ( "inCodUF"               );
$obCmbUF->addOption             ( "", "Selecione"         );
$obCmbUF->setCampoId            ( "cod_uf"                );
$obCmbUF->setCampoDesc          ( "nom_uf"                );
$obCmbUF->preencheCombo         ( $rsUF                   );
$obCmbUF->setValue              ( $inCodigoUF             );
$obCmbUF->setNull               ( true                    );
$obCmbUF->setStyle              ( "width: 220px"          );
$obCmbUF->obEvento->setOnChange ( "buscaValor('preencheMunicipio')" );

$obTxtCodMunicipio = new TextBox;
$obTxtCodMunicipio->setRotulo    ( "*Munic&iacute;pio"  );
$obTxtCodMunicipio->setTitle           ( "Munic&iacute;pio onde ocorreu a retenção" );
$obTxtCodMunicipio->setName      ( "inCodigoMunicipio" );
$obTxtCodMunicipio->setValue     ( $inCodigoMunicipio  );
$obTxtCodMunicipio->setSize      ( 8                   );
$obTxtCodMunicipio->setMaxLength ( 8                   );
$obTxtCodMunicipio->setNull      ( true                );

$obCmbMunicipio = new Select;
$obCmbMunicipio->setName       ( "inCodMunicipio"   );
$obCmbMunicipio->addOption     ( "", "Selecione"    );
$obCmbMunicipio->setCampoId    ( "cod_municipio"    );
$obCmbMunicipio->setCampoDesc  ( "nom_municipio"    );
$obCmbMunicipio->setValue      ( $inCodigoMunicipio );
$obCmbMunicipio->preencheCombo ( $rsMunicipios      );
$obCmbMunicipio->setNull       ( true               );
$obCmbMunicipio->setStyle      ( "width: 220px"     );

$obTxtExercicio = new Exercicio;
$obTxtExercicio->setTitle ( "Informe o exercício." );
$obTxtExercicio->setName ( "stExercicio" );
$obTxtExercicio->setNull ( false );
$obTxtExercicio->setValue ( null );
$obTxtExercicio->obEvento->setOnChange( "buscaValor('validaExercicio');" );

$obTxtSerie = new TextBox;
$obTxtSerie->setRotulo ( "*Série" );
$obTxtSerie->setTitle ( "Número de serie da nota fiscal" );
$obTxtSerie->setName ( "inSerie" );
$obTxtSerie->setValue ( $inSerie );
$obTxtSerie->setMaxLength ( 10 );
$obTxtSerie->setInteiro ( false );
$obTxtSerie->setNull ( true );

$obTxtNumeroDaNota = new TextBox;
$obTxtNumeroDaNota->setRotulo ( "*Número da Nota" );
$obTxtNumeroDaNota->setTitle ( "Nùmero da nota fiscal" );
$obTxtNumeroDaNota->setName ( "inNumeroNota" );
$obTxtNumeroDaNota->setValue ( $inNumeroNota );
$obTxtNumeroDaNota->setInteiro ( true );
$obTxtNumeroDaNota->setNull ( true );

$obDtEmissao = new Data;
$obDtEmissao->setName ( "dtEmissao" );
$obDtEmissao->setTitle ( "Data de emissão da nota fiscal" );
$obDtEmissao->setRotulo ( "*Data da Emissão" );
$obDtEmissao->setMaxLength ( 20 );
$obDtEmissao->setSize ( 10 );
$obDtEmissao->setNull ( true );
$obDtEmissao->obEvento->setOnChange( "buscaValor('validaData');" );

$obMontaServico = new MontaServico;
//$obMontaServico->obTxtChaveServico->setTitle("teste");
$obMontaServico->setCadastroServico( false );

$obMontaServico->setCodigoAtividade( $_REQUEST["inCodAtividade"] );
$obMontaServico->setRotulo( "*Serviço" );

$obTxtAliquota = new TextBox;
$obTxtAliquota->setRotulo ( "*Alíquota (%)" );
$obTxtAliquota->setName ( "flAliquota" );
$obTxtAliquota->setId ( "flAliquota" );
$obTxtAliquota->setInteiro ( true );
$obTxtAliquota->setNull ( true );
$obTxtAliquota->setNaoZero ( true );
$obTxtAliquota->setSize ( 6 );
$obTxtAliquota->setTitle ( "Alíquota a incidir sobre o serviço" );
$obTxtAliquota->setMaxLength ( 6 );

$obTxtValorDeclarado = new Numerico;
$obTxtValorDeclarado->setRotulo ( "*Valor Declarado" );
$obTxtValorDeclarado->setName ( "flValorDeclarado" );
$obTxtValorDeclarado->setId ( "flValorDeclarado" );
$obTxtValorDeclarado->setTitle ( "Valor declarado para o serviço" );
$obTxtValorDeclarado->setDecimais ( 2 );
$obTxtValorDeclarado->setMaxValue ( 99999999999999.99 );
$obTxtValorDeclarado->setNull ( true );
$obTxtValorDeclarado->setNegativo ( false );
$obTxtValorDeclarado->setNaoZero ( true );
$obTxtValorDeclarado->setSize ( 20 );
$obTxtValorDeclarado->setMaxLength ( 20 );

$obTxtDeducao = new Numerico;
$obTxtDeducao->setRotulo ( "Dedução Incondicional" );
$obTxtDeducao->setName ( "flDeducao" );
$obTxtDeducao->setId ( "flDeducao" );
$obTxtDeducao->setTitle ( "Deduções que possam incidir sobre o serviço." );
$obTxtDeducao->setDecimais ( 2 );
$obTxtDeducao->setMaxValue ( 99999999999999.99 );
$obTxtDeducao->setNull ( true );
$obTxtDeducao->setNegativo ( false );
$obTxtDeducao->setNaoZero ( false );
$obTxtDeducao->setSize ( 20 );
$obTxtDeducao->setMaxLength ( 20 );

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

$obSpnListaNota = new Span;
$obSpnListaNota->setID("spnListaNota");

$obBtnOK = new OK;

$obBtnLimpar = new Button;
$obBtnLimpar->setName              ( "btnLimpar" );
$obBtnLimpar->setValue             ( "Limpar" );
$obBtnLimpar->setTipo              ( "button" );
$obBtnLimpar->obEvento->setOnClick ( "LimparForm();" );
$obBtnLimpar->setDisabled          ( false );

$botoesBarra = array ( $obBtnOK , $obBtnLimpar );

$obSpnBotaoNota = new Span;
$obSpnBotaoNota->setID("botaoNota");

$obSpn1 = new Span;
$obSpn1->setID("spn1");

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction ( $pgProc );
$obForm->settarget ( "oculto" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm );
$obFormulario->addHidden     ( $obHdnAcao );
$obFormulario->addHidden     ( $obHdnCtrl );
$obFormulario->addHidden( $obHdnInTipoFiscalizacao );
$obFormulario->addHidden( $obHdnInCodProcesso );
$obFormulario->addHidden($obHdnInIncricao );
$obFormulario->addHidden( $obHdnInAtividade);
$obFormulario->addHidden( $obHdnInModalidade );
$obFormulario->addHidden($obHdnInInicio );
$obFormulario->addHidden($obHdnInTermino );

$obFormulario->addTitulo     ( "Dados para declaração de Lançamentos" );
$obFormulario->addComponente( $obTipoFiscalizacao);
$obFormulario->addComponente($obProcessoFiscal);
$obFormulario->addComponente( $obInscricaoEconomica);
$obFormulario->addComponente($obAtividade);
$obFormulario->addComponente($obModalidade);

$obFormulario->addTitulo     ( "Dados do Prestador" );
$obFormulario->addComponente ( $obBscCGM );
$obFormulario->addComponenteComposto ( $obTxtCodUF, $obCmbUF );
$obFormulario->addComponenteComposto ( $obTxtCodMunicipio, $obCmbMunicipio );
$obFormulario->addTitulo     ( "Dados da Nota" );
$obFormulario->addComponente ( $obTxtExercicio );
$obFormulario->addComponente ( $obCmbCompetencia );
$obFormulario->addComponente ( $obTxtSerie );
$obFormulario->addComponente ( $obTxtNumeroDaNota );
$obFormulario->addComponente ( $obDtEmissao );
$obFormulario->addTitulo     ( "Dados do Serviço" );
$obMontaServico->geraFormulario ( $obFormulario );
$obFormulario->addComponente ( $obTxtAliquota );
$obFormulario->addComponente ( $obTxtValorDeclarado );
$obFormulario->addComponente ( $obTxtDeducao );
$obFormulario->defineBarra   ( $botoesServico, 'left', '' );

$obFormulario->addSpan ( $obSpn1 );
$obFormulario->addSpan       ( $obSpnListaServico );

$obFormulario->addSpan ( $obSpnBotaoNota );
//$obFormulario->defineBarra ( $botoesNota, 'left', '' );
$obFormulario->addSpan ( $obSpnListaNota );

$obFormulario->Cancelar();
$obFormulario->show();

Sessao::write('servicos_retencao', array() );
Sessao::write('servicos_retencao_alterando', "" );
Sessao::write('notas_retencao', array() );
