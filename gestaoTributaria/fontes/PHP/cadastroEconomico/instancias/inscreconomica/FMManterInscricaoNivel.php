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
    * Página de Formulario de Inclusao de Inscrição Econômica
    * Data de Criação   : 22/12/2004

    * @author  Tonismar Régis Bernardo

    * @ignore

    * $Id: FMManterInscricaoNivel.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.02.10

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CEM_NEGOCIO."RCEMInscricaoEconomica.class.php" );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMEmpresaDeFato.class.php"      );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMEmpresaDeDireito.class.php"   );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMAutonomo.class.php"           );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMCategoria.class.php"          );

//Define o nome dos arquivos PHP
$stPrograma = "ManterInscricao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgFormAlt = "FM".$stPrograma."Alt.php";
$pgFormNivel = "FM".$stPrograma."Nivel.php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once ($pgJS);

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

switch ($_REQUEST[ "inCodigoEnquadramento" ]) {
    case 1:
        $obRCEMInscricaoEconomica = new RCEMEmpresaDeFato;
    break;
    case 2:
        $obRCEMInscricaoEconomica = new RCEMEmpresaDeDireito;
    break;
    case 3:
        $obRCEMInscricaoEconomica = new RCEMAutonomo;
    break;
}
//$obRCEMInscricaoEconomica->obRCadastroDinamico->setCodCadastro( $_REQUEST[ "inCodigoEnquadramento" ] );

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $stAcao  );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( 'stCtrl' );
$obHdnCtrl->setValue( $stCtrl );

$obHdnCodigoEnquadramento = new Hidden;
$obHdnCodigoEnquadramento->setName  ( "inCodigoEnquadramento" );
$obHdnCodigoEnquadramento->setValue ( $_REQUEST[ "inCodigoEnquadramento" ] );

Sessao::write( "transf4", array( "inscricao" => array(), "sociedade" => array()) );
//DEFINICOES DE COMPONENTES

//ABA INSCRICAO ECONOMICA
$obBscCGM = new BuscaInner;
$obBscCGM->setRotulo( "CGM" );
$obBscCGM->setNull( false );
$obBscCGM->setId( "stNomCGM" );
$obBscCGM->obCampoCod->setName("inNumCGM");
$obBscCGM->obCampoCod->setValue( $inNumCGM );
//$obBscCGM->obCampoCod->obEvento->setOnFocus("if (this.value!=0) {buscaValor('buscaCGM');}");
$obBscCGM->obCampoCod->obEvento->setOnChange("busca('buscaCGM');");

if ($_REQUEST[ 'inCodigoEnquadramento' ] == 2) {
    $obBscCGM->setFuncaoBusca( "abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inNumCGM','stNomCGM','juridica','".Sessao::getId()."','800','550');" );
} else {
    $obBscCGM->setFuncaoBusca( "abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inNumCGM','stNomCGM','fisica','".Sessao::getId()."','800','550');" );
}

$obRCEMInscricaoEconomica->obRCEMConfiguracao->setAnoExercicio( Sessao::getExercicio() );
$obRCEMInscricaoEconomica->obRCEMConfiguracao->consultarConfiguracao();
$boNumeroInscricao  = $obRCEMInscricaoEconomica->obRCEMConfiguracao->getNumeroInscricao();
$stMascaraInscricao = $obRCEMInscricaoEconomica->obRCEMConfiguracao->getMascaraInscricao();

$obTxtNumeroInscricao = new TextBox;
$obTxtNumeroInscricao->setName      ( "inNumeroInscricao"   );
$obTxtNumeroInscricao->setRotulo    ( "Inscrição Econômica" );
$obTxtNumeroInscricao->setNull      ( false );
$obTxtNumeroInscricao->setSize      ( strlen( $stMascaraInscricao ) );
$obTxtNumeroInscricao->setMaxLength ( strlen( $stMascaraInscricao ) );
$obTxtNumeroInscricao->setMascara   ( $stMascaraInscricao );
$obTxtNumeroInscricao->obEvento->setOnKeyUp( "mascaraDinamico('".$stMascaraInscricao."', this, event);" );
$obTxtNumeroInscricao->setPreencheComZeros( 'E' );
$obTxtNumeroInscricao->obEvento->setOnChange("buscaValor('verificaInscricao');");

$obHdnSeqContabil = new Hidden;
$obHdnSeqContabil->setName  ( "inSequencia" );
$obHdnSeqContabil->setValue ( $inSequencia );

$obBscCGMContabil = new BuscaInner;
$obBscCGMContabil->setRotulo( "Responsável Contábil" );
$obBscCGMContabil->setId( "stNomCGMResponsavel" );
$obBscCGMContabil->obCampoCod->setName("inNumCGMResponsavel");
$obBscCGMContabil->obCampoCod->setValue( $inNumCGM );
$obBscCGMContabil->obCampoCod->obEvento->setOnChange("buscaValor('buscaRespContabil');");
$obBscCGMContabil->setFuncaoBusca( "abrePopUp('".CAM_GT_CEM_POPUPS."RespTecnico/LSProcurarRespTecnico.php','frm','inNumCGMResponsavel','stNomCGMResponsavel','todos','".Sessao::getId()."&inCodProfissao=inCodProfissao&inSequencia=inSequencia','800','550');" );

if ($_REQUEST[ "inCodigoEnquadramento" ] == 2)
    $obBscCGMContabil->setNull ( false );
else
    $obBscCGMContabil->setNull ( true );

$obHdnCodigoProfissao = new Hidden;
$obHdnCodigoProfissao->setName ( 'inCodProfissao' );
$obHdnCodigoProfissao->setValue( $inCodProfissao  );

$obTxtJunta = new TextBox;
$obTxtJunta->setName      ( "inRegistroJunta"   );
$obTxtJunta->setValue     ( $inRegistroJunta    );
$obTxtJunta->setInteiro   ( true );
$obTxtJunta->setRotulo    ( "Registro na Junta" );
$obTxtJunta->setNull      ( true );
$obTxtJunta->setSize      ( 13 );
$obTxtJunta->setMaxLength ( 13 );

$stMascaraNatureza = '999-9';
$obBscNatureza = new BuscaInner;
$obBscNatureza->setRotulo( "*Natureza Jurídica" );
$obBscNatureza->setId( "stNomeNatureza" );
$obBscNatureza->obCampoCod->setName("inCodigoNatureza");
$obBscNatureza->obCampoCod->setValue( $inCodigoNatureza );
$obBscNatureza->obCampoCod->setMascara( $stMascaraNatureza);
$obBscNatureza->obCampoCod->obEvento->setOnChange("buscaValor('buscaNatureza');");
$obBscNatureza->setFuncaoBusca( "abrePopUp('".CAM_GT_CEM_POPUPS."naturezajuridica/FLProcurarNaturezaJuridica.php','frm','inCodigoNatureza','stNomeNatureza','todos','".Sessao::getId()."','800','550');" );

$obDtAbertura = new Data;
$obDtAbertura->setName     ( "stDtAbertura" );
$obDtAbertura->setRotulo   ( "Data de Abertura" );
$obDtAbertura->setTitle    ( 'Data de Abertura da Inscrição Econômica' );
$obDtAbertura->setValue    (  $_REQUEST["stDtAbertura"]    );
$obDtAbertura->setNull     ( false );

$obRCEMCategoria = new RCEMCategoria;
$rsCategoria = new RecordSet;
$obRCEMCategoria->listarCategoria( $rsCategoria );

$obCmbCategoria = new Select;
$obCmbCategoria->setName               ( "stCategoria"         );
$obCmbCategoria->setRotulo             ( "Categoria"           );
$obCmbCategoria->setNull               ( false                 );
$obCmbCategoria->setCampoId            ( "cod_categoria"       );
$obCmbCategoria->setCampoDesc          ( "nom_categoria"       );
$obCmbCategoria->addOption             ( "", "Selecione"       );
$obCmbCategoria->preencheCombo          ( $rsCategoria          );
$obCmbCategoria->obEvento->setOnChange ( "preencheCodigoCategoria();" );

//------------------------------------------------------------------- SELECAO SPAN
$obSpnTipoDomicilio = new Span;
$obSpnTipoDomicilio->setID("spnTipoDomicilio");

$obRdbDomicilioFiscal = new Radio;
$obRdbDomicilioFiscal->setRotulo   ( "Tipo de Domicílio Fiscal" );
$obRdbDomicilioFiscal->setName     ( "boTipoDomicilio" );
$obRdbDomicilioFiscal->setValue    ( "IC" );
$obRdbDomicilioFiscal->setLabel    ( "Imóvel Cadastrado" );
$obRdbDomicilioFiscal->setNull     ( false );
$obRdbDomicilioFiscal->setChecked  ( true );
$obRdbDomicilioFiscal->setTitle    ( "Define se o Domocílio Fiscal será indexado por Imóvel Cadastrado ou por um Endereço Informado"     );
$obRdbDomicilioFiscal->obEvento->setOnChange ( "buscaValor('BuscaIndexacao');" );

$obRdbDomicilioEndereco = new Radio;
$obRdbDomicilioEndereco->setRotulo       ( "Tipo de Domicílio Fiscal" );
$obRdbDomicilioEndereco->setName         ( "boTipoDomicilio"   );
$obRdbDomicilioEndereco->setValue        ( "EI" );
$obRdbDomicilioEndereco->setLabel        ( "Endereço Informado" );
$obRdbDomicilioEndereco->setNull         ( false   );
$obRdbDomicilioEndereco->setChecked      ( '' );
$obRdbDomicilioEndereco->setTitle        ( "Define se o Domocílio Fiscal será indexado por Imóvel Cadastrado ou por um Endereço Informado" );
$obRdbDomicilioEndereco->obEvento->setOnChange ( "buscaValor('BuscaIndexacao');");
//------------------------------------------------------------------- SELECAO SPAN FIM*/

if ($stAcao == "incluir") {
    $obRCEMInscricaoEconomica->obRCadastroDinamico->recuperaAtributosSelecionados( $rsAtributos );
}

//---------------------------------------------------------------- ATRIBUTOS DE INSCRICAO ECONOMICA
$obMontaAtributos = new MontaAtributos;
$obMontaAtributos->setTitulo     ( "Atributos"  );
$obMontaAtributos->setName       ( "Atributo_"  );
$obMontaAtributos->setRecordSet  ( $rsAtributos );

$obBscProcesso = new BuscaInner;
$obBscProcesso->setRotulo ( "Processo" );
$obBscProcesso->setTitle ( "Processo do protocolo referente ao cadastro de inscrição econômica" );
$obBscProcesso->setNull ( true );
$obBscProcesso->obCampoCod->setName ("inNumProcesso");
$obBscProcesso->obCampoCod->setId   ("inNumProcesso");
$obBscProcesso->obCampoCod->setValue( $inNumProcesso );
$obBscProcesso->obCampoCod->obEvento->setOnChange( "buscaValor('buscaProcesso');" );
$obBscProcesso->obCampoCod->obEvento->setOnKeyUp( "mascaraDinamico('99999/9999', this, event);" );
$obBscProcesso->setFuncaoBusca( "abrePopUp('".CAM_GA_PROT_POPUPS."processo/FLBuscaProcessos.php','frm','inNumProcesso','campoInner2','','".Sessao::getId()."','800','550')" );

//---------------------------------------------------------------- ABA SOCIEDADE -------------------

$obLblCapitalSocial = new Label;
$obLblCapitalSocial->setRotulo( "*Capital Social (R$)" );
$obLblCapitalSocial->setId    ( "flCapitalSocial" );

$obBscSocio = new BuscaInner;
$obBscSocio->setRotulo( "*Sócio" );
$obBscSocio->setId( "stNomeSocio" );
$obBscSocio->obCampoCod->setName("inCodigoSocio");
$obBscSocio->obCampoCod->setValue( $inCodigoSocio );
$obBscSocio->obCampoCod->obEvento->setOnChange("busca('buscaSocio');");
$obBscSocio->setFuncaoBusca( "abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inCodigoSocio','stNomeSocio','todos','".Sessao::getId()."','800','550');" );

$obTxtQuota = new Moeda;
$obTxtQuota->setRotulo          ( "*Capital (R$)" );
$obTxtQuota->setName            ( "flQuota" );
$obTxtQuota->setValue           ( $flQuota  );
$obTxtQuota->setTitle           ( "Valor da quota do sócio (em Reais)" );
$obTxtQuota->setMaxLength       ( 10    );

$obSpnListaSocio = new Span;
$obSpnListaSocio->setId( "lsListaSocio" );

$obButtonIncluirSocio = new Button;
$obButtonIncluirSocio->setName             ( "btnIncluirSocio" );
$obButtonIncluirSocio->setValue            ( "Incluir" );
$obButtonIncluirSocio->obEvento->setOnClick ( "return incluirSocio();" );

$obButtonLimparSocio = new Button;
$obButtonLimparSocio->setName              ( "btnLimparSocio" );
$obButtonLimparSocio->setValue             ( "Limpar" );
$obButtonLimparSocio->obEvento->setOnClick  ( "limparSocio();" );

$obCheckSegueAtividade = new CheckBox;
$obCheckSegueAtividade->setName                ( "boSegueAtividade"                    );
$obCheckSegueAtividade->setValue               ( "1"                                   );
$obCheckSegueAtividade->setLabel               ( "Seguir para Cadastro de Atividades?" );
$obCheckSegueAtividade->setNull                ( true                                  );
$obCheckSegueAtividade->setChecked     ( true                                  );

$obBtnOK = new OK;

$onBtnLimpar = new Limpar;
$onBtnLimpar->obEvento->setOnClick( "busca('limparSpan');" );

$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( 'oculto' );

// DEFINICAO DO FORMULARIO
if ($_REQUEST[ "inCodigoEnquadramento" ] == 2) {
    $obFormulario = new FormularioAbas;
} else {
    $obFormulario = new Formulario;
}
$obFormulario->addForm             ( $obForm );
$obFormulario->setAjuda      ( "UC-05.02.10");
$obFormulario->addHidden           ( $obHdnAcao );
$obFormulario->addHidden           ( $obHdnCtrl );
$obFormulario->addHidden           ( $obHdnCodigoProfissao );
$obFormulario->addHidden           ( $obHdnCodigoEnquadramento );
$obFormulario->addHidden           ( $obHdnSeqContabil );
if ($_REQUEST[ "inCodigoEnquadramento" ] == 2) {
    $obFormulario->addAba              ( "Inscrição Econômica" );
}
$obFormulario->addTitulo           ( "Dados para Inscrição Econômica" );
$obFormulario->addComponente       ( $obBscCGM );
if ($boNumeroInscricao == "f") {
    $obFormulario->addComponente       ( $obTxtNumeroInscricao );
}
$obFormulario->addComponente       ( $obBscCGMContabil );

$obFormulario->addComponente       ( $obDtAbertura   );

if ($_REQUEST[ "inCodigoEnquadramento" ] == 2) {
    $obFormulario->addComponente       ( $obTxtJunta );
    $obFormulario->addComponente       ( $obBscNatureza );
    $obFormulario->addComponente       ( $obCmbCategoria );
}

$obFormulario->addComponente ( $obBscProcesso );
//----------------------------------------------- SPAN DOMICILIO
$obFormulario->addComponenteComposto ( $obRdbDomicilioFiscal, $obRdbDomicilioEndereco );
$obFormulario->addSpan ( $obSpnTipoDomicilio    );
//-----------------------------------------------*/

$obMontaAtributos->geraFormulario  ( $obFormulario );
if ($_REQUEST[ "inCodigoEnquadramento" ] == 2) {
    $obFormulario->addAba              ( "Sociedade" );
    $obFormulario->addTitulo           ( "Dados para Sociedade" );
    $obFormulario->addComponente       ( $obLblCapitalSocial );
    $obFormulario->addTitulo           ( "Sociedade" );
    $obFormulario->addComponente       ( $obBscSocio );
    $obFormulario->addComponente       ( $obTxtQuota );
    $obFormulario->agrupaComponentes   ( array( $obButtonIncluirSocio, $obButtonLimparSocio ) );
    $obFormulario->addSpan             ( $obSpnListaSocio );
}
$obFormulario->addDiv( 4, "componente" );
$obFormulario->addComponente       ( $obCheckSegueAtividade );
$obFormulario->fechaDiv();
$obFormulario->defineBarra( array( $obBtnOK , $onBtnLimpar ) );
$obFormulario->show();

$stJs .= "busca('BuscaIndexacao');";
$stJs .= "f.inNumCGM.focus();";
sistemaLegado::executaFrameOculto( $stJs );
