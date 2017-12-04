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
    * Página de Formulario da Retencao de Fonte

    * Data de Criação   : 26/10/2006

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato
    * @ignore

    * $Id: FMReterFonte.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.03.22

*/

/*
$Log$
Revision 1.4  2007/08/07 20:25:44  cercato
Bug#9837#

Revision 1.3  2007/07/05 13:24:42  cercato
Bug #9571#

Revision 1.2  2006/11/01 11:23:44  fabio
comentado componente de selecao de documentos

Revision 1.1  2006/10/30 13:00:16  cercato
*** empty log message ***

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLogradouro.class.php" );
include_once ( CAM_GT_CEM_COMPONENTES."MontaServico.class.php" );
include_once ( CAM_GA_ADM_COMPONENTES."ITextBoxSelectDocumento.class.php" );

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

//Define o nome dos arquivos PHP
$stPrograma    = "ReterFonte";
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

$stCtrl = "";
$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName   ( "stCtrl" );
$obHdnCtrl->setValue  ( $stCtrl  );

$obHdnInscricaoEconomica =  new Hidden;
$obHdnInscricaoEconomica->setName   ( "inInscricaoEconomica" );
$obHdnInscricaoEconomica->setValue  ( $_REQUEST["inInscricaoEconomica"] );

$obHdnCGM = new Hidden;
$obHdnCGM->setName ( "inNumCGM" );
$obHdnCGM->setValue ( $_REQUEST["inNumCGM"] );

$obTxtExercicio = new Exercicio;
$obTxtExercicio->setTitle ( "Informe o exercício." );
$obTxtExercicio->setName ( "stExercicio" );
$obTxtExercicio->setNull ( false );
$obTxtExercicio->obEvento->setOnChange( "buscaValor('alteraCompetencia');" );

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
$obCmbCompetencia->obEvento->setOnChange ( "buscaValor('alteraCompetencia');" );

$obLblCGMdoRetentor = new Label;
$obLblCGMdoRetentor->setRotulo    ( "CGM do Retentor" );
$obLblCGMdoRetentor->setName      ( "stLblCGM" );
$obLblCGMdoRetentor->setId        ( "stLblCGM" );
$obLblCGMdoRetentor->setValue     ( $_REQUEST["inNumCGM"]." - ".$_REQUEST["stNomCGM"] );

$obLblDomicilioFiscal = new Label;
$obLblDomicilioFiscal->setRotulo    ( "Domicílio Fiscal" );
$obLblDomicilioFiscal->setName      ( "stLblDomicilio" );
$obLblDomicilioFiscal->setId        ( "stLblDomicilio" );
$obLblDomicilioFiscal->setValue     ( $_REQUEST["stLogradouro"] );

$obLblCPF_CNPJ = new Label;
$obLblCPF_CNPJ->setRotulo    ( "CPF/CNPJ" );
$obLblCPF_CNPJ->setName      ( "stLblCPF" );
$obLblCPF_CNPJ->setId        ( "stLblCPF" );
$obLblCPF_CNPJ->setValue     ( $_REQUEST["stCpfCnpj"] );

$rsUF = new RecordSet;
$obRCIMLogradouro = new RCIMLogradouro;
$obRCIMLogradouro->listarUF( $rsUF );

$rsMunicipios = new RecordSet;

$obTxtCodUF = new TextBox;
$obTxtCodUF->setRotulo             ( "*Estado"               );
$obTxtCodUF->setName               ( "inCodigoUF"            );
//$obTxtCodUF->setValue              ( $inCodigoUF          );
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
//$obCmbUF->setValue              ( $inCodigoUF             );
$obCmbUF->setNull               ( true                    );
$obCmbUF->setStyle              ( "width: 220px"          );
$obCmbUF->obEvento->setOnChange ( "buscaValor('preencheMunicipio')" );

 $inCodigoMunicipio="";
$obTxtCodMunicipio = new TextBox;
$obTxtCodMunicipio->setRotulo    ( "*Munic&iacute;pio"  );
$obTxtCodMunicipio->setName      ( "inCodigoMunicipio"  );
//$obTxtCodMunicipio->setValue     ( $inCodigoMunicipio   );
$obTxtCodMunicipio->setSize      ( 8                    );
$obTxtCodMunicipio->setMaxLength ( 8                    );
$obTxtCodMunicipio->setNull      ( true                 );

$obCmbMunicipio = new Select;
$obCmbMunicipio->setName       ( "inCodMunicipio"      );
$obCmbMunicipio->addOption     ( "", "Selecione"       );
$obCmbMunicipio->setCampoId    ( "cod_municipio"       );
$obCmbMunicipio->setCampoDesc  ( "nom_municipio"       );
//$obCmbMunicipio->setValue      ( $inCodigoMunicipio );
$obCmbMunicipio->preencheCombo ( $rsMunicipios         );
$obCmbMunicipio->setNull       ( true                  );
$obCmbMunicipio->setStyle      ( "width: 220px"        );

$obBscCGM = new BuscaInner;
$obBscCGM->setRotulo         ( "*CGM do Prestador" );
$obBscCGM->setId             ( "stCGM" );
$obBscCGM->setNull           ( true );
$obBscCGM->obCampoCod->setName       ( "inCGM" );
$obBscCGM->obCampoCod->obEvento->setOnChange( "buscaValor('PreencheCGM');" );
$obBscCGM->setFuncaoBusca( "abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inCGM','stCGM','','".Sessao::getId()."','800','450');" );

$obDtEmissao = new Data;
$obDtEmissao->setName ( "dtEmissao" );
$obDtEmissao->setRotulo ( "*Data da Emissão" );
$obDtEmissao->setMaxLength ( 20 );
$obDtEmissao->setSize ( 10 );
$obDtEmissao->setNull ( true );
$obDtEmissao->obEvento->setOnChange( "buscaValor('validaData');" );

$obTxtSerie = new TextBox;
$obTxtSerie->setRotulo ( "*Série" );
$obTxtSerie->setName ( "inSerie" );
//$obTxtSerie->setValue ( $inSerie );
$obTxtSerie->setInteiro ( false );
$obTxtSerie->setNull ( true );

$obTxtNumeroDaNota = new TextBox;
$obTxtNumeroDaNota->setRotulo ( "*Número da Nota" );
$obTxtNumeroDaNota->setName ( "inNumeroNota" );
//$obTxtNumeroDaNota->setValue ( $inNumeroNota );
$obTxtNumeroDaNota->setInteiro ( true );
$obTxtNumeroDaNota->setNull ( true );

$obMontaServico = new MontaServico;
$obMontaServico->setCadastroServico( false );
$obMontaServico->setRotulo( "*Serviço" );

$obTxtAliquota = new Numerico;
$obTxtAliquota->setRotulo ( "*Alíquota (%)" );
$obTxtAliquota->setName ( "flAliquota" );
$obTxtAliquota->setId ( "flAliquota" );
$obTxtAliquota->setDecimais ( 2 );
$obTxtAliquota->setNull ( true );
$obTxtAliquota->setNegativo ( false );
$obTxtAliquota->setNaoZero ( true );
$obTxtAliquota->setSize ( 6 );
$obTxtAliquota->setMaxLength ( 6 );

$obTxtValorDeclarado = new Numerico;
$obTxtValorDeclarado->setRotulo ( "*Valor Declarado" );
$obTxtValorDeclarado->setName ( "flValorDeclarado" );
$obTxtValorDeclarado->setId ( "flValorDeclarado" );
$obTxtValorDeclarado->setDecimais ( 2 );
$obTxtValorDeclarado->setMaxValue ( 99999999999999.99 );
$obTxtValorDeclarado->setNull ( true );
$obTxtValorDeclarado->setNegativo ( false );
$obTxtValorDeclarado->setNaoZero ( true );
$obTxtValorDeclarado->setSize ( 20 );
$obTxtValorDeclarado->setMaxLength ( 20 );

$obTxtDeducao = new Numerico;
$obTxtDeducao->setRotulo ( "Dedução" );
$obTxtDeducao->setName ( "flDeducao" );
$obTxtDeducao->setId ( "flDeducao" );
$obTxtDeducao->setDecimais ( 2 );
$obTxtDeducao->setMaxValue ( 99999999999999.99 );
$obTxtDeducao->setNull ( true );
$obTxtDeducao->setNegativo ( false );
$obTxtDeducao->setNaoZero ( true );
$obTxtDeducao->setSize ( 20 );
$obTxtDeducao->setMaxLength ( 20 );

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

$botoesServico = array ( $obBtnIncluirServico , $obBtnLimparServico );

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

$obSpnData = new Span;
$obSpnData->setID("spnData");

$obBtnOK = new OK;

$obBtnLimpar = new Button;
$obBtnLimpar->setName              ( "btnLimpar" );
$obBtnLimpar->setValue             ( "Limpar" );
$obBtnLimpar->setTipo              ( "button" );
$obBtnLimpar->obEvento->setOnClick ( "LimparForm();" );
$obBtnLimpar->setDisabled          ( false );

$botoesBarra = array ( $obBtnOK , $obBtnLimpar );

$obChkEmissaoCarne = new CheckBox;
$obChkEmissaoCarne->setName    ( "boEmissaoCarne" );
$obChkEmissaoCarne->setValue   ( "1" );

$obChkEmissaoCarne->setRotulo ( "Emissão de Carnês" );
$obChkEmissaoCarne->setLabel   ( "Impressão Local" );
$obChkEmissaoCarne->setNull    ( true );
$obChkEmissaoCarne->setChecked ( true );

$rsModelos = new RecordSet;

include_once( CAM_GT_ARR_NEGOCIO."RARRCarne.class.php" );

$obRARRCarne = new RARRCarne;
$obRARRCarne->listarModeloDeCarne( $rsModelos, Sessao::read('acao') );

$obCmbModelo = new Select;
$obCmbModelo->setRotulo       ( "Modelo"    );
$obCmbModelo->setTitle        ( "Modelo de carne"    );
$obCmbModelo->setName         ( "cmbModelo" );
$obCmbModelo->addOption       ( "", "Selecione" );
$obCmbModelo->setCampoId      ( "[nom_arquivo]§[cod_modelo]" );
$obCmbModelo->setCampoDesc    ( "nom_modelo" );
$obCmbModelo->preencheCombo    ( $rsModelos );
$obCmbModelo->setStyle        ( "width: 100%;" );

$obTxtObservacao = new TextArea;
$obTxtObservacao->setName ( "stObservacao" );
$obTxtObservacao->setRotulo ( "Observações p/ Boleto" );
$obTxtObservacao->setTitle ( "Observações para o contribuinte." );
$obTxtObservacao->setValue ( "" );
$obTxtObservacao->setNull  ( true );
$obTxtObservacao->setCols ( 30 );
$obTxtObservacao->setRows ( 5 );
$obTxtObservacao->setMaxCaracteres(300);

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

$obFormulario->addTitulo     ( "Dados do Retentor" );
$obFormulario->addComponente ( $obLblCGMdoRetentor );
$obFormulario->addComponente ( $obLblDomicilioFiscal );
$obFormulario->addComponente ( $obLblCPF_CNPJ );

$obFormulario->addTitulo     ( "Dados do Prestador" );
$obFormulario->addComponente ( $obBscCGM );
$obFormulario->addComponenteComposto ( $obTxtCodUF, $obCmbUF );
$obFormulario->addComponenteComposto ( $obTxtCodMunicipio, $obCmbMunicipio );
$obFormulario->addComponente ( $obTxtSerie );
$obFormulario->addComponente ( $obTxtNumeroDaNota );
$obFormulario->addComponente ( $obTxtExercicio );
$obFormulario->addComponente ( $obCmbCompetencia );
$obFormulario->addComponente ( $obDtEmissao );

$obFormulario->addTitulo     ( "Dados do Serviço" );
$obMontaServico->geraFormulario ( $obFormulario );

$obFormulario->addComponente ( $obTxtAliquota );
$obFormulario->addComponente ( $obTxtValorDeclarado );
$obFormulario->addComponente ( $obTxtDeducao );

$obFormulario->defineBarra   ( $botoesServico, 'left', '' );
$obFormulario->addSpan       ( $obSpnListaServico );

$obFormulario->defineBarra ( $botoesNota, 'left', '' );
$obFormulario->addSpan ( $obSpnListaNota );
$obFormulario->addSpan       ( $obSpnData );
$obFormulario->addComponente ( $obTxtObservacao );
$obFormulario->addComponente ( $obChkEmissaoCarne );
$obFormulario->addComponente ( $obCmbModelo );

$obFormulario->Cancelar();
$obFormulario->show();

Sessao::write('servicos_retencao', array() );
Sessao::write('servicos_retencao_alterando', "" );
Sessao::write('notas_retencao', array() );
