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
    * Página de Formulario de Alteração de Domicílio Fiscal para uma Inscrição Econômica
    * Data de Criação   : 31/01/2005

    * @author  Tonismar Régis Bernardo

    * @ignore

    * $Id: FMManterInscricaoDomicilioAlteracao.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.02.10

*/

/*
$Log$
Revision 1.12  2006/09/15 14:33:01  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CEM_NEGOCIO."RCEMInscricaoEconomica.class.php" );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMEmpresaDeDireito.class.php"   );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMNaturezaJuridica.class.php"   );
include_once ( CAM_GT_CEM_COMPONENTES."MontaAtividade.class.php"     );

//Define o nome dos arquivos PHP
$stPrograma = "ManterInscricao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgNatAlt = "FM".$stPrograma."NaturezaAlteracao.php";
$pgSocAlt = "FM".$stPrograma."SociedadeAlteracao.php";
$pgAtvAlt = "FM".$stPrograma."AtividadeAlteracao.php";
$pgDomAlt = "FM".$stPrograma."DomicilioAlteracao.php";
$pgEleAlt = "FM".$stPrograma."ElementosAlteracao.php";
$pgHorAlt = "FM".$stPrograma."HorarioAlteracao.php";
$pgJS   = "JS".$stPrograma.".js";

include_once( $pgJS );

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

$obRCEMInscricaoEconomica = new RCEMEmpresaDeDireito;

if ($_REQUEST[ 'inInscricaoEconomica' ]) {
    $obRCEMInscricaoEconomica->setInscricaoEconomica( $_REQUEST[ 'inInscricaoEconomica' ] );
}

$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $stAcao  );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( 'stCtrl' );
$obHdnCtrl->setValue( $stCtrl );

$obHdnInscricaoEconomica = new Hidden;
$obHdnInscricaoEconomica->setName ( 'inInscricaoEconomica' );
$obHdnInscricaoEconomica->setValue( $_REQUEST[ 'inInscricaoEconomica' ] );

$obHdDescQuestao = new Hidden;
$obHdDescQuestao->setName ( 'stDescQuestao' );
$obHdDescQuestao->setValue( $_REQUEST[ 'stDescQuestao' ] );

$obBscProcesso = new BuscaInner;
$obBscProcesso->setRotulo ( "Processo" );
$obBscProcesso->setTitle ( "Processo do protocolo referente à alteração de domicílio fiscal de inscrição econômica" );
$obBscProcesso->setNull ( true );
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

$obLblInscricao = new Label;
$obLblInscricao->setRotulo( "Inscrição Econômica" );
$obLblInscricao->setValue ( $_REQUEST["inInscricaoEconomica"] );

$obLblCGM = new Label;
$obLblCGM->setRotulo ( "CGM" );
$obLblCGM->setValue ( $_REQUEST["inCGM"]." - ".$_REQUEST["stCGM"] );

//------------------------------------------------------------- BUSCA O ENDEREÇO FISCAL FIM
//------------------------------------------------------------------- SELECAO SPAN
$obSpnTipoDomicilio = new Span;
$obSpnTipoDomicilio->setID("spnTipoDomicilio");

$obRdbDomicilioFiscal = new Radio;
$obRdbDomicilioFiscal->setRotulo   ( "Tipo do Domicílio Fiscal" );
$obRdbDomicilioFiscal->setName     ( "boTipoDomicilio" );
$obRdbDomicilioFiscal->setValue    ( "IC" );
$obRdbDomicilioFiscal->setLabel    ( "Imóvel Cadastrado" );
$obRdbDomicilioFiscal->setNull     ( false );
$obRdbDomicilioFiscal->setChecked  ( $obRCEMDomicilio->getDomicilioExibir () == 'IC' );
$obRdbDomicilioFiscal->setTitle    ( "Define se o Domocílio Fiscal será indexado por Imóvel Cadastrado ou por um Endereço Informado"     );
$obRdbDomicilioFiscal->obEvento->setOnChange ( "buscaValor('BuscaIndexacao');" );

$obRdbDomicilioEndereco = new Radio;
$obRdbDomicilioEndereco->setRotulo       ( "Tipo do Domicílio Fiscal" );
$obRdbDomicilioEndereco->setName         ( "boTipoDomicilio"   );
$obRdbDomicilioEndereco->setValue        ( "EI" );
$obRdbDomicilioEndereco->setLabel        ( "Endereço Informado" );
$obRdbDomicilioEndereco->setNull         ( false   );
$obRdbDomicilioEndereco->setChecked      ( $obRCEMDomicilio->getDomicilioExibir () == 'EI' );
$obRdbDomicilioEndereco->setTitle        ( "Define se o Domocílio Fiscal será indexado por Imóvel Cadastrado ou por um Endereço Informado" );
$obRdbDomicilioEndereco->obEvento->setOnChange ( "buscaValor('BuscaIndexacao');");
//------------------------------------------------------------------- SELECAO SPAN FIM*/

$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( 'oculto' );

$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm );
$obFormulario->setAjuda      ( "UC-05.02.10");
$obFormulario->addTitulo     ( "Dados para Inscrição Econômica" );
$obFormulario->addHidden     ( $obHdnAcao );
$obFormulario->addHidden     ( $obHdnCtrl );
$obFormulario->addHidden     ( $obHdnInscricaoEconomica );
$obFormulario->addHidden     ( $obHdDescQuestao );
$obFormulario->addComponente ( $obLblCGM );
$obFormulario->addComponente ( $obLblInscricao );

//$obFormulario->addComponente ( $obBscProcesso );

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

//----------------------------------------------- SPAN
$obFormulario->addComponenteComposto ( $obRdbDomicilioFiscal, $obRdbDomicilioEndereco );
$obFormulario->addSpan               ( $obSpnTipoDomicilio    );
//-----------------------------------------------*/

$obFormulario->addComponente ( $obBscProcesso );

$obFormulario->Ok();
$obFormulario->show();

$stJsx = "buscaValor('BuscaIndexacao')";
sistemaLegado::executaFrameOculto( $stJsx );
