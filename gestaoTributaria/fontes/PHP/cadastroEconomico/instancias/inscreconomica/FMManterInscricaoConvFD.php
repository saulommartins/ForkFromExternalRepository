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

    * @author  Diego Bueno Coelho

    * @ignore

    * $Id: FMManterInscricaoConvFD.php 63839 2015-10-22 18:08:07Z franver $

    * Casos de uso: uc-05.02.10

*/

/*
$Log$
Revision 1.13  2007/05/09 13:04:47  cercato
Bug #9247#

Revision 1.12  2006/09/15 14:33:01  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

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

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $stAcao  );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( 'stCtrl' );
$obHdnCtrl->setValue( $stCtrl );

//------------------------------------------------------------- BUSCA OS DADOS DA EMPRESA

    //$obRCEMInscricaoEconomica = new RCEMEmpresaDeFato;
    $obRCEMInscricaoEconomica = new RCEMEmpresaDeDireito;

    $inInscricao = $_REQUEST["inInscricaoEconomica"];
    $obHdnInscricaoEconomica = new Hidden;
    $obHdnInscricaoEconomica->setName ( 'inNumeroInscricao' );
    $obHdnInscricaoEconomica->setValue( $inInscricao );

    $inEnquadramento = $_REQUEST[ "inCodigoEnquadramento" ];
    $obHdnEnquadramento = new Hidden;
    $obHdnEnquadramento->setName ( 'inCodigoEnquadramento' );
    $obHdnEnquadramento->setValue( $inEnquadramento        );

    $inCGM = $_REQUEST[ "inCGM" ];
    $obHdnCGM = new Hidden;
    $obHdnCGM->setName ( 'inNumCGM' );
    $obHdnCGM->setValue( $inCGM     );

    $stCGM = $_REQUEST[ "stCGM" ];
    $obLblCGM = new Label;
    $obLblCGM->setRotulo( "CGM Pessoa Física"  );
    $obLblCGM->setValue ( $inCGM." - ".$stCGM );

    $obRCEMInscricaoEconomica->setInscricaoEconomica( $_REQUEST["inInscricaoEconomica"] );
    $obRCEMInscricaoEconomica->listarInscricao( $rsInscricao );

    $obRCEMInscricaoEconomica->obRCEMConfiguracao->consultarConfiguracao();
    $stMascaraInscricao = $obRCEMInscricaoEconomica->obRCEMConfiguracao->getMascaraInscricao();

    $inTmpInscricao = str_pad( $inInscricao, strlen($stMascaraInscricao), 0, STR_PAD_LEFT);

    $obLblInscricao = new Label;
    $obLblInscricao->setRotulo( "Inscrição Econômica" );
    //$obLblInscricao->setValue ( $inTmpInscricao );
    $obLblInscricao->setValue ( $_REQUEST["inInscricaoEconomica"] );

    $obLblDtAbertura = new Label;
    $obLblDtAbertura->setRotulo( "Data de Abertura" );
    $obLblDtAbertura->setValue   ( $_REQUEST['stDtAbertura'] );

    //echo 'DO BANCO: '.$rsInscricao->getCampo( "cod_natureza"        );
    $inCodigoDomicilio = $rsInscricao->getCampo( "inscricao_municipal" );
    $inRegistroJunta   = $rsInscricao->getCampo( "num_registro_junta"  );
    $inCodigoNatureza  = $rsInscricao->getCampo( "cod_natureza"        );
    $inCodigoCategoria = $rsInscricao->getCampo( "cod_categoria"       );
    $inNumCGM          = $rsInscricao->getCampo( "resp_numcgm"         );
    $stNomCGM          = $rsInscricao->getCampo( "nom_cgm"             );
    $stNomNatureza     = $rsInscricao->getCampo( "nom_natureza"        );
    $inCodigoProfissao = $rsInscricao->getCampo( "cod_profissao"       );
    $inSequencia       = $rsInscricao->getCampo( "sequencia"           );

    $obHdnCodigoProfissao = new Hidden;
    $obHdnCodigoProfissao->setName ( 'inCodProfissao'   );
    $obHdnCodigoProfissao->setValue( $inCodigoProfissao );
//------------------------------------------------------------- BUSCA OS DADOS DA EMPRESA FIM

//------------------------------------------------------------- BUSCA O ENDEREÇO FISCAL
include_once ( CAM_GT_CEM_NEGOCIO."RCEMDomicilio.class.php"          );
$obRCEMDomicilio = new RCEMDomicilio;

$obRCEMDomicilio->setInscricaoEconomica ( $_REQUEST["inInscricaoEconomica"] );
$obRCEMDomicilio->verificaDomicilioAtual ( );

if ( $obRCEMDomicilio->getDomicilioExibir () == 'IC' ) { //se for Domicilio Fiscal

    //echo "DOMICILIO FISCAL";

    $obHdnCodigoDomicilio = new Hidden;
    $obHdnCodigoDomicilio->setName  ('inCodDomicilioFiscal');
    $obHdnCodigoDomicilio->setValue ( $obRCEMDomicilio->getDomicilioFiscal () );

} else { //se for Domicilio Informado

    //echo "DOMICILIO INFORMADO";

    $obHdnCEP = new Hidden;
    $obHdnCEP->setName  ('stCEP');
    $obHdnCEP->setValue ( $obRCEMDomicilio->getCEP () );

    $obHdnCaixaPostal = new Hidden;
    $obHdnCaixaPostal->setName  ('stCaixaPostal');
    $obHdnCaixaPostal->setValue ( $obRCEMDomicilio->getCaixaPostal () );

    $obHdnCodMunicipio = new Hidden;
    $obHdnCodMunicipio->setName  ('inCodigoMunicipio');
    $obHdnCodMunicipio->setValue ( $obRCEMDomicilio->getCodMunicipio () );

    $obHdnComplemento = new Hidden;
    $obHdnComplemento->setName  ('stComplemento');
    $obHdnComplemento->setValue ( $obRCEMDomicilio->getComplemento () );

    $obHdnNumero = new Hidden;
    $obHdnNumero->setName  ('HdninNumero');
    $obHdnNumero->setValue ( $obRCEMDomicilio->getNumero () );

    $obHdnCodBairro = new Hidden;
    $obHdnCodBairro->setName  ('HdninCodBairro');
    $obHdnCodBairro->setValue ( $obRCEMDomicilio->getCodBairro () );

    $obHdnCodUF = new Hidden;
    $obHdnCodUF->setName  ('inCodigoUF');
    $obHdnCodUF->setValue ( $obRCEMDomicilio->getCodUF () );

    $obHdnCodLogradouro = new Hidden;
    $obHdnCodLogradouro->setName  ('HdninNumLogradouro');
    $obHdnCodLogradouro->setValue ( $obRCEMDomicilio->getCodLogradouro () );

    $obHdnNomLogradouro = new Hidden;
    $obHdnNomLogradouro->setName  ('HdnstNomLogradouro');
    $obHdnNomLogradouro->setValue ( $obRCEMDomicilio->getNomLogradouro () );

    $obHdnNomMunicipio = new Hidden;
    $obHdnNomMunicipio->setName  ('stMunicipio');
    $obHdnNomMunicipio->setValue ( $obRCEMDomicilio->getNomMunicipio () );

    $obHdnNomUF = new Hidden;
    $obHdnNomUF->setName  ('stUF');
    $obHdnNomUF->setValue ( $obRCEMDomicilio->getNomUF () );
}
//------------------------------------------------------------- BUSCA O ENDEREÇO FISCAL FIM

Sessao::write( "transf4", array( "inscricao"    => array(),
                          "sociedade"    => array()) );
//DEFINICOES DE COMPONENTES

//pegar nome responsavel
$obRCGM = new RCGM;
$obRCGM->setNumCgm( $rsInscricao->getCampo( "resp_numcgm") );
$obRCGM->consultar($rsCgm);
$stNomResponsavel = $obRCGM->getNomCgm();

$obBscCGMContabil = new BuscaInner;
$obBscCGMContabil->setRotulo( "Responsável Contábil" );
$obBscCGMContabil->setId( "stNomCGMResponsavel" );
$obBscCGMContabil->setValue( $stNomResponsavel );
$obBscCGMContabil->setNull( false );
$obBscCGMContabil->obCampoCod->setName("inNumCGMResponsavel");
$obBscCGMContabil->obCampoCod->setValue( $inNumCGM );
$obBscCGMContabil->obCampoCod->obEvento->setOnChange("buscaValor('buscaRespContabil');");
$obBscCGMContabil->obCampoCod->obEvento->setOnBlur  ("buscaValor('buscaRespContabil');");
$obBscCGMContabil->setFuncaoBusca( "abrePopUp('".CAM_GT_CEM_POPUPS."RespTecnico/FLProcurarRespTecnico.php','frm','inNumCGMResponsavel','stNomCGMResponsavel','todos','".Sessao::getId()."&inCodProfissao=inCodProfissao','800','550');" );

$obBscCGM = new BuscaInner;
$obBscCGM->setRotulo( "CGM Pessoa Jurídica" );
$obBscCGM->setId( "stNomCGMJuridica2" );
$obBscCGM->setNull( false );
$obBscCGM->obCampoCod->setName("inNumCGMJuridica2");
$obBscCGM->obCampoCod->setValue( $inNumCGMJuridica2 );
//$obBscCGM->obCampoCod->obEvento->setOnFocus("if (this.value!=0) {buscaValor('buscaCGM');}");
$obBscCGM->obCampoCod->obEvento->setOnChange("busca('buscaCGMJuridica');");
$obBscCGM->setFuncaoBusca( "abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inNumCGMJuridica2','stNomCGMJuridica2','juridica','".Sessao::getId()."','800','550');" );

$obTxtJunta = new TextBox;
$obTxtJunta->setName      ( "inRegistroJunta"   );
$obTxtJunta->setValue     ( $inRegistroJunta    );
$obTxtJunta->setInteiro   ( true );
$obTxtJunta->setRotulo    ( "Registro na Junta" );
$obTxtJunta->setNull      ( true );
$obTxtJunta->setSize      ( 13 );
$obTxtJunta->setMaxLength ( 13 );

$stMascaraNatureza = "999-9";
$obBscNatureza = new BuscaInner;
$obBscNatureza->setRotulo( "*Natureza Jurídica" );
$obBscNatureza->setId( "stNomeNatureza" );
$obBscNatureza->obCampoCod->setName("inCodigoNatureza");
$obBscNatureza->obCampoCod->setValue( $inCodigoNatureza );
$obBscNatureza->obCampoCod->setMascara( $stMascaraNatureza);
$obBscNatureza->obCampoCod->setMaxLength( strlen($stMascaraNatureza));
$obBscNatureza->obCampoCod->setMinLength( strlen($stMascaraNatureza));
$obBscNatureza->setValue( $stNomNatureza );
//$obBscCGM->obCampoCod->obEvento->setOnFocus("if (this.value!=0) {buscaValor('buscaCGM');}");
$obBscNatureza->obCampoCod->obEvento->setOnChange("buscaValor('buscaNatureza');");
$obBscNatureza->setFuncaoBusca( "abrePopUp( '" . CAM_GT_CEM_POPUPS ."naturezajuridica/FLProcurarNaturezaJuridica.php','frm','inCodigoNatureza','stNomeNatureza','todos','".Sessao::getId()."','800','550');" );

$obRCEMCategoria = new RCEMCategoria;
$rsCategoria = new RecordSet;
$obRCEMCategoria->listarCategoria( $rsCategoria );

$obCmbCategoria = new Select;
$obCmbCategoria->setName               ( "stCategoria"          );
$obCmbCategoria->setValue              ( $inCodigoCategoria     );
$obCmbCategoria->setRotulo             ( "Categoria"            );
$obCmbCategoria->setNull               ( false                  );
$obCmbCategoria->setCampoId            ( "cod_categoria"        );
$obCmbCategoria->setCampoDesc          ( "nom_categoria"        );
$obCmbCategoria->addOption             ( "", "Selecione"        );
$obCmbCategoria->preencheCombo         ( $rsCategoria           );
$obCmbCategoria->obEvento->setOnChange ( "preencheCodigoCategoria();" );

$obRCIMConfiguracao = new RCIMConfiguracao;
$obRCIMConfiguracao->consultarConfiguracao();
$obRCIMConfiguracao->setCodigoModulo( 12 );
$obRCIMConfiguracao->setAnoExercicio( Sessao::getExercicio() );
$obRCIMConfiguracao->consultarConfiguracao();
$stMascaraIM = $obRCIMConfiguracao->getMascaraIM();
$obRCIMConfiguracao->consultarMascaraProcesso( $stMascaraProcesso );

$obBscProcesso = new BuscaInner;
$obBscProcesso->setRotulo ( "Processo" );
$obBscProcesso->setTitle ( "Número do Processo no Protocolo que Gerou a Aprovação do Loteamento" );
$obBscProcesso->setNull ( true );
$obBscProcesso->obCampoCod->setName ("inNumProcesso");
$obBscProcesso->obCampoCod->setId   ("inNumProcesso");
$obBscProcesso->obCampoCod->setValue( $inProcesso );
$obBscProcesso->obCampoCod->obEvento->setOnChange( "buscaValor('buscaProcesso');" );
$obBscProcesso->obCampoCod->obEvento->setOnKeyUp( "mascaraDinamico('".$stMascaraProcesso."', this, event);" );
$obBscProcesso->setFuncaoBusca( "abrePopUp('".CAM_GA_PROT_POPUPS."processo/FLBuscaProcessos.php','frm','inNumProcesso','campoInner2','','".Sessao::getId()."','800','550')" );

//------------------------------------------------------------------- SELECAO SPAN
$obSpnTipoDomicilio = new Span;
$obSpnTipoDomicilio->setID("spnTipoDomicilio");

$obRdbDomicilioFiscal = new Radio;
$obRdbDomicilioFiscal->setRotulo   ( "Tipo de Domicílio Fiscal" );
$obRdbDomicilioFiscal->setName     ( "boTipoDomicilio" );
$obRdbDomicilioFiscal->setValue    ( "IC" );
$obRdbDomicilioFiscal->setLabel    ( "Imóvel Cadastrado" );
$obRdbDomicilioFiscal->setNull     ( false );
$obRdbDomicilioFiscal->setChecked  ( $obRCEMDomicilio->getDomicilioExibir () == 'IC' );
$obRdbDomicilioFiscal->setTitle    ( "Define se o Domocílio Fiscal será indexado por Imóvel Cadastrado ou por um Endereço Informado"     );
$obRdbDomicilioFiscal->obEvento->setOnChange ( "buscaValor('BuscaIndexacao');" );

$obRdbDomicilioEndereco = new Radio;
$obRdbDomicilioEndereco->setRotulo       ( "Tipo de Domicílio Fiscal" );
$obRdbDomicilioEndereco->setName         ( "boTipoDomicilio"   );
$obRdbDomicilioEndereco->setValue        ( "EI" );
$obRdbDomicilioEndereco->setLabel        ( "Endereço Informado" );
$obRdbDomicilioEndereco->setNull         ( false   );
$obRdbDomicilioEndereco->setChecked      ( $obRCEMDomicilio->getDomicilioExibir () == 'EI' );
$obRdbDomicilioEndereco->setTitle        ( "Define se o Domocílio Fiscal será indexado por Imóvel Cadastrado ou por um Endereço Informado" );
$obRdbDomicilioEndereco->obEvento->setOnChange ( "buscaValor('BuscaIndexacao');");
//------------------------------------------------------------------- SELECAO SPAN FIM*/

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
//---------------------------------------------------------------- SPAN SOCIEDADE

//---------------------------------------------------------------- ATRIBUTOS
//$arChaveAtributoInscricao =  array( "inscricao_economica" => $inInscricao );
$arChaveAtributoInscricao =  array( "inscricao_economica" => $_REQUEST["inInscricaoEconomica"] );
$obRCEMInscricaoEconomica->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoInscricao );
$obRCEMInscricaoEconomica->obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributos );

$obMontaAtributos = new MontaAtributos;
$obMontaAtributos->setTitulo     ( "Atributos"  );
$obMontaAtributos->setName       ( "Atributo_"  );
$obMontaAtributos->setRecordSet  ( $rsAtributos );
//---------------------------------------------------------------- ATRIBUTOS FIM

$obHdnSeqContabil = new Hidden;
$obHdnSeqContabil->setName  ( "inSequencia" );
$obHdnSeqContabil->setValue ( $inSequencia );

//################################################### FORMULARIO #####################

$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( 'oculto' );

    $obFormulario = new FormularioAbas;

$obFormulario->addForm              ( $obForm );
$obFormulario->setAjuda             ( "UC-05.02.10");
$obFormulario->addHidden            ( $obHdnAcao );
$obFormulario->addHidden            ( $obHdnCtrl );
$obFormulario->addHidden            ( $obHdnInscricaoEconomica );
$obFormulario->addHidden            ( $obHdnCGM );
$obFormulario->addHidden            ( $obHdnSeqContabil );

if ( $obRCEMDomicilio->getDomicilioExibir () == 'IC' ) {
    $obFormulario->addHidden ( $obHdnCodigoDomicilio );
} else {
    $obFormulario->addHidden ( $obHdnCaixaPostal );
    $obFormulario->addHidden ( $obHdnCodMunicipio );
    $obFormulario->addHidden ( $obHdnCEP );
    $obFormulario->addHidden ( $obHdnComplemento );
    $obFormulario->addHidden ( $obHdnCodBairro );
    $obFormulario->addHidden ( $obHdnNumero );
    $obFormulario->addHidden ( $obHdnCodUF );
    $obFormulario->addHidden ( $obHdnCodLogradouro );
    $obFormulario->addHidden ( $obHdnNomLogradouro );
    $obFormulario->addHidden ( $obHdnNomMunicipio );
    $obFormulario->addHidden ( $obHdnNomUF );
}

$obFormulario->addAba              ( "Inscrição Econômica" );
$obFormulario->addTitulo           ( "Dados para Inscrição Econômica" );

$obFormulario->addComponente       ( $obLblCGM );
$obFormulario->addComponente       ( $obBscCGM );
$obFormulario->addComponente       ( $obLblInscricao );
$obFormulario->addComponente       ( $obLblDtAbertura );
$obFormulario->addComponente       ( $obBscCGMContabil );
$obFormulario->addHidden           ( $obHdnCodigoProfissao );

$obFormulario->addComponente       ( $obTxtJunta );
$obFormulario->addComponente       ( $obBscNatureza );
$obFormulario->addComponente       ( $obCmbCategoria );
//$obFormulario->addComponente       ( $obBscProcesso );

//AQUI ENTRA O COMBO DE PROCESSO

//----------------------------------------------- SPAN
$obFormulario->addComponenteComposto ( $obRdbDomicilioFiscal, $obRdbDomicilioEndereco );
$obFormulario->addSpan               ( $obSpnTipoDomicilio    );
//-----------------------------------------------*/

$obMontaAtributos->geraFormulario  ( $obFormulario );

    $obFormulario->addAba              ( "Sociedade" );
    $obFormulario->addTitulo           ( "Dados para Sociedade" );
    $obFormulario->addComponente       ( $obLblCapitalSocial );
    $obFormulario->addTitulo           ( "Sociedade" );
    $obFormulario->addComponente       ( $obBscSocio );
    $obFormulario->addComponente       ( $obTxtQuota );
    $obFormulario->agrupaComponentes   ( array( $obButtonIncluirSocio, $obButtonLimparSocio ) );
    $obFormulario->addSpan             ( $obSpnListaSocio );

$obFormulario->addDiv( 2, "componente" );
$obFormulario->addComponente       ( $obCheckSegueAtividade );
$obFormulario->fechaDiv();
$obFormulario->defineBarra( array( $obBtnOK , $onBtnLimpar ) );
$obFormulario->show();

$stJs .= "buscaValor('BuscaIndexacao')";
sistemaLegado::executaFrameOculto( $stJs );
