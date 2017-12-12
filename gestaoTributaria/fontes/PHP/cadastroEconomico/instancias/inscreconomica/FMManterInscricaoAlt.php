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
    * Página de Formulario de Alteracao de Inscrição Econômica
    * Data de Criação   : 12/01/2005

    * @author  Tonismar Régis Bernardo

    * @ignore

    * $Id: FMManterInscricaoAlt.php 63376 2015-08-21 18:55:42Z arthur $

    * Casos de uso: uc-05.02.10

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GT_CEM_NEGOCIO."RCEMInscricaoEconomica.class.php";
include_once CAM_GT_CEM_NEGOCIO."RCEMEmpresaDeFato.class.php";
include_once CAM_GT_CEM_NEGOCIO."RCEMEmpresaDeDireito.class.php";
include_once CAM_GT_CEM_NEGOCIO."RCEMAutonomo.class.php";
include_once CAM_GT_CEM_NEGOCIO."RCEMCategoria.class.php";
include_once CAM_GA_CGM_NEGOCIO."RCGM.class.php";

$stAcao = $request->get('stAcao');
$link = Sessao::read( "link" );
$stLink = "&pg=".$link["pg"]."&pos=".$link["pos"]."&stAcao=".$stAcao;

//Define o nome dos arquivos PHP
$stPrograma = "ManterInscricao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php?".Sessao::getId().$stLink;
$pgForm = "FM".$stPrograma.".php";
$pgFormNivel = "FM".$stPrograma."Nivel.php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once ($pgJS);
$stJs = "";
//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

//------------------------------------------------------------- BUSCA OS DADOS DA EMPRESA
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

    $inInscricao = $_REQUEST["inInscricaoEconomica"];

    $obHdnInscricaoEconomica = new Hidden;
    $obHdnInscricaoEconomica->setName ( 'inInscricaoEconomica' );
    $obHdnInscricaoEconomica->setValue( $inInscricao );

    //ABA INSCRICAO ECONOMICA

    $inCGM = $_REQUEST[ "inCGM" ];
    $obBscCGM = new BuscaInner;
    $obBscCGM->setRotulo( "CGM" );
    $obBscCGM->setNull( false );
    $obBscCGM->setId( "stNomCGM" );
    $obBscCGM->obCampoCod->setName("inNumCGM");
    $obBscCGM->obCampoCod->setValue( $inCGM );
    $obBscCGM->obCampoCod->obEvento->setOnChange("busca('buscaCGM');");
    if ($_REQUEST['inCodigoEnquadramento']==2) {
        $obBscCGM->setFuncaoBusca( "abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inNumCGM','stNomCGM','juridica','".Sessao::getId()."','800','550');" );
    } else {
        $obBscCGM->setFuncaoBusca( "abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inNumCGM','stNomCGM','fisica','".Sessao::getId()."','800','550');" );
    }

    $inEnquadramento = $_REQUEST[ "inCodigoEnquadramento" ];
    $obHdnEnquadramento = new Hidden;
    $obHdnEnquadramento->setName ( 'inCodigoEnquadramento' );
    $obHdnEnquadramento->setValue( $inEnquadramento        );

    $obHdnCGM = new Hidden;
    $obHdnCGM->setName ( 'inNumCGM' );
    $obHdnCGM->setValue( $inCGM     );

    $stCGM = $_REQUEST[ "stCGM" ];

    $obHdnDescCGM = new Hidden;
    $obHdnDescCGM->setName ( 'stDescCGM' );
    $obHdnDescCGM->setValue( $stCGM );

    $obLblCGM = new Label;
    $obLblCGM->setRotulo( "CGM"  );
    $obLblCGM->setValue ( $inCGM." - ".$stCGM );

    $obRCEMInscricaoEconomica->setInscricaoEconomica( $_REQUEST["inInscricaoEconomica"] );
    $obRCEMInscricaoEconomica->listarInscricao( $rsInscricao );

    $obRCEMInscricaoEconomica->obRCEMConfiguracao->consultarConfiguracao();
    $stMascaraInscricao = $obRCEMInscricaoEconomica->obRCEMConfiguracao->getMascaraInscricao();

    $inTmpInscricao = str_pad( $inInscricao, strlen($stMascaraInscricao), 0, STR_PAD_LEFT);

    $obLblInscricao = new Label;
    $obLblInscricao->setRotulo( "Inscrição Econômica" );
    $obLblInscricao->setValue ( $inTmpInscricao );

    $obDtAbertura = new Data;
    $obDtAbertura->setName      ( "stDtAbertura"   );
    $obDtAbertura->setValue     ( $_REQUEST['stDtAbertura']    );
    $obDtAbertura->setRotulo    ( "Data de Abertura" );
    $obDtAbertura->setTitle     ( "Data de Abertura da Inscrição Econômica" );
    $obDtAbertura->setNull      ( false );
    
    $inCodigoDomicilio = $rsInscricao->getCampo( "inscricao_municipal" );
    $inRegistroJunta   = $rsInscricao->getCampo( "num_registro_junta"  );
    $inCodigoNatureza  = $rsInscricao->getCampo( "cod_natureza"        );
    $inCodigoCategoria = $rsInscricao->getCampo( "cod_categoria"       );
    $inNumCGM          = $rsInscricao->getCampo( "resp_numcgm"         );
    $stNomCGM          = $rsInscricao->getCampo( "nom_cgm"             );
    $stNomNatureza     = $rsInscricao->getCampo( "nom_natureza"        );
    $inCodigoProfissao = $rsInscricao->getCampo( "cod_profissao"       );
    $inSequencia       = $rsInscricao->getCampo( "sequencia" );
//------------------------------------------------------------- BUSCA OS DADOS DA EMPRESA FIM
$obBscProcesso = new BuscaInner;
$obBscProcesso->setRotulo ( "Processo" );
$obBscProcesso->setTitle ( "Processo do protocolo referente ao cadastro de inscrição econômica" );
$obBscProcesso->setNull ( true );
$obBscProcesso->obCampoCod->setMaxLength("12");
$obBscProcesso->obCampoCod->setName ("inNumProcesso");
$obBscProcesso->obCampoCod->setId   ("inNumProcesso");
$obBscProcesso->obCampoCod->setValue( $inNumProcesso );
$obBscProcesso->obCampoCod->obEvento->setOnChange( "buscaValor('buscaProcesso');" );
$obBscProcesso->obCampoCod->obEvento->setOnKeyUp( "mascaraDinamico('99999/9999', this, event);" );
$obBscProcesso->setFuncaoBusca( "abrePopUp('".CAM_GA_PROT_POPUPS."processo/FLBuscaProcessos.php','frm','inNumProcesso','campoInner2','','".Sessao::getId()."','800','550')" );

//------------------------------------------------------------- BUSCA O ENDEREÇO FISCAL
include_once ( CAM_GT_CEM_NEGOCIO."RCEMDomicilio.class.php"          );
$obRCEMDomicilio = new RCEMDomicilio;

$obRCEMDomicilio->setInscricaoEconomica ( $_REQUEST["inInscricaoEconomica"] );
$obRCEMDomicilio->verificaDomicilioAtual ( );

if ( $obRCEMDomicilio->getDomicilioExibir () == 'IC' ) { //se for Domicilio Fiscal

    $obHdnCodigoDomicilio = new Hidden;
    $obHdnCodigoDomicilio->setName  ('inCodDomicilioFiscal');
    $obHdnCodigoDomicilio->setValue ( $obRCEMDomicilio->getDomicilioFiscal () );

} else { //se for Domicilio Informado

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

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $stAcao  );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( 'stCtrl' );
$obHdnCtrl->setValue( $_REQUEST["stCtrl"] );

//pegar nome responsavel
$obRCGM = new RCGM;
$obRCGM->setNumCgm($rsInscricao->getCampo( "resp_numcgm") );
$obRCGM->consultar($rsCgm);
$stNomResponsavel = $obRCGM->getNomCgm();

if ($_REQUEST[ 'inCodigoEnquadramento' ] == 2) {
    if ($inCodigoCategoria!="") {
        $stJs.= "frm.stCategoria.selectedIndex = ".$inCodigoCategoria.";";
    }
}

$obTxtNumeroInscricao = new TextBox;
$obTxtNumeroInscricao->setName      ( "inNumeroInscricao"   );
$obTxtNumeroInscricao->setInteiro   ( true );
$obTxtNumeroInscricao->setRotulo    ( "Número de Inscrição" );
$obTxtNumeroInscricao->setNull      ( false );
$obTxtNumeroInscricao->setSize      ( 10 );
$obTxtNumeroInscricao->setMaxLength ( 10 );

$obBscCGMContabil = new BuscaInner;
$obBscCGMContabil->setRotulo( "Responsável Contábil" );
$obBscCGMContabil->setId( "stNomCGMResponsavel" );
$obBscCGMContabil->obCampoCod->setName("inNumCGMResponsavel");
$obBscCGMContabil->obCampoCod->setValue( $inNumCGM );
$obBscCGMContabil->setValue( $stNomResponsavel );
$obBscCGMContabil->obCampoCod->obEvento->setOnChange("buscaValor('buscaRespContabil');");
$obBscCGMContabil->setFuncaoBusca( "abrePopUp('".CAM_GT_CEM_POPUPS."RespTecnico/LSProcurarRespTecnico.php','frm','inNumCGMResponsavel','stNomCGMResponsavel','todos','".Sessao::getId()."','800','550');" );
if ($_REQUEST[ "inCodigoEnquadramento" ] == 2)
    $obBscCGMContabil->setNull ( false );
else
    $obBscCGMContabil->setNull ( true );

$obHdnCodigoProfissao = new Hidden;
$obHdnCodigoProfissao->setName ( 'inCodProfissao'   );
$obHdnCodigoProfissao->setValue( $inCodigoProfissao );

$obHdnSequencia = new Hidden;
$obHdnSequencia->setName ( 'inSequencia'   );
$obHdnSequencia->setValue( $inSequencia );

$obTxtJunta = new TextBox;
$obTxtJunta->setName      ( "inRegistroJunta"   );
$obTxtJunta->setValue     ( $inRegistroJunta    );
$obTxtJunta->setInteiro   ( true );
$obTxtJunta->setRotulo    ( "Registro na Junta" );
$obTxtJunta->setNull      ( true );
$obTxtJunta->setSize      ( 10 );
$obTxtJunta->setMaxLength ( 10 );

$stMascaraNatureza = "999-9";
$obBscNatureza = new BuscaInner;
$obBscNatureza->setRotulo( "Natureza Jurídica" );
$obBscNatureza->setId( "stNomeNatureza" );
$obBscNatureza->setNull(false);
$obBscNatureza->obCampoCod->setName("inCodigoNatureza");
$obBscNatureza->obCampoCod->setValue( $inCodigoNatureza );
$obBscNatureza->obCampoCod->setMascara( $stMascaraNatureza);
$obBscNatureza->obCampoCod->setMaxLength( strlen($stMascaraNatureza));
$obBscNatureza->obCampoCod->setMinLength( strlen($stMascaraNatureza));
$obBscNatureza->setValue( $stNomNatureza );
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

$arChaveAtributoInscricao =  array( "inscricao_economica" => $inInscricao );
$obRCEMInscricaoEconomica->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoInscricao );
$obRCEMInscricaoEconomica->obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributos );

$obMontaAtributos = new MontaAtributos;
$obMontaAtributos->setTitulo     ( "Atributos"  );
$obMontaAtributos->setName       ( "Atributo_"  );
$obMontaAtributos->setRecordSet  ( $rsAtributos );

$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( 'oculto' );

$obFormulario = new Formulario;
$obFormulario->addForm             ( $obForm );
$obFormulario->setAjuda      ( "UC-05.02.10");

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

$obFormulario->addHidden           ( $obHdnAcao );
$obFormulario->addHidden           ( $obHdnCtrl );
$obFormulario->addHidden           ( $obHdnSequencia );
$obFormulario->addHidden           ( $obHdnCodigoProfissao );
$obFormulario->addHidden           ( $obHdnInscricaoEconomica );
$obFormulario->addHidden           ( $obHdnEnquadramento );
//$obFormulario->addHidden           ( $obHdnCGM );
$obFormulario->addHidden           ( $obHdnDescCGM );
$obFormulario->addTitulo           ( "Dados para Inscrição Econômica" );
//$obFormulario->addComponente       ( $obLblCGM );
$obFormulario->addComponente       ( $obLblInscricao );
$obFormulario->addComponente       ( $obBscCGM );
$obFormulario->addComponente       ( $obBscCGMContabil );
$obFormulario->addComponente       ( $obDtAbertura );

if ($_REQUEST[ 'inCodigoEnquadramento' ] == 2) {
    $obFormulario->addComponente       ( $obTxtJunta );
    $obFormulario->addComponente       ( $obBscNatureza );
    $obFormulario->addComponente       ( $obCmbCategoria );
}

$obFormulario->addComponente       ( $obBscProcesso );

//----------------------------------------------- SPAN
$obFormulario->addComponenteComposto ( $obRdbDomicilioFiscal, $obRdbDomicilioEndereco );
$obFormulario->addSpan               ( $obSpnTipoDomicilio    );
//-----------------------------------------------*/

$obMontaAtributos->geraFormulario  ( $obFormulario );

if ($stAcao == "incluir") {
    $obFormulario->OK();
} else {
    $obFormulario->Cancelar( $pgList );
}
$obFormulario->show();

$stJs .= "busca('BuscaIndexacao');";
$stJs .= "f.inNumCGMResponsavel.focus();";
$stJs .= "d.getElementById('stNomCGM').innerHTML = f.stDescCGM.value;";
sistemaLegado::executaFrameOculto( $stJs );

?>