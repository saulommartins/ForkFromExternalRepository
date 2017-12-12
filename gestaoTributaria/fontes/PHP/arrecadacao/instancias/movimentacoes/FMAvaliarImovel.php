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
  * Página de Formulário de Avaliar Imóvel
  * Data de criação : 10/06/2005

  * @author Analista: Fábio Bertoldi
  * @author Programador: tonismar R. Bernardo

  * @ignore

    * $Id: FMAvaliarImovel.php 59612 2014-09-02 12:00:51Z gelson $

  Caso de uso: uc-05.03.06
**/

/*
$Log$
Revision 1.20  2006/10/10 15:18:56  cercato
alterando formularios para retirar ITBI.

Revision 1.19  2006/09/15 11:14:47  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_ARR_NEGOCIO."RARRAvaliacaoImobiliaria.class.php"                                  );
include_once ( CAM_GT_ARR_NEGOCIO."RARRConfiguracao.class.php"                                          );
//Define o nome dos arquivos PHP
$stPrograma = "AvaliarImovel";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once ( $pgJS );

$obRARRConfiguracao = new RARRConfiguracao;
$obRARRConfiguracao->setCodModulo( 25 );
$obRARRConfiguracao->setExercicio( Sessao::getExercicio() );
$obRARRConfiguracao->consultar();
$boVerificacao = $obRARRConfiguracao->getValTransfImovel();

$obRARRAvaliacaoImobiliaria = new RARRAvaliacaoImobiliaria;
$obRARRAvaliacaoImobiliaria->obRCIMImovel->setNumeroInscricao( $_REQUEST['inInscricaoImobiliaria'] );
//$obRARRAvaliacaoImobiliaria->consultarAvaliacoes();
$obRARRAvaliacaoImobiliaria->setExercicio( Sessao::getExercicio() );
$obRARRAvaliacaoImobiliaria->listarAvaliacoesCalculada( $rsCalculado );
$obRARRAvaliacaoImobiliaria->listarAvaliacoesInformada( $rsInformado );

if ( $rsCalculado->getNumLinhas() > 0 ) {
    $flTerritorialCalculado = number_format($rsCalculado->getCampo( 'venal_territorial_calculado' ),2,',','.');
    $flPredialCalculado     = number_format($rsCalculado->getCampo( 'venal_predial_calculado'),2,',','.');
    $flTotalCalculado       = number_format($rsCalculado->getCampo( 'venal_total_calculado'),2,',','.');
}

if ( $rsInformado->getNumLinhas() > 0 ) {
    $flTerritorialInformado = number_format($rsInformado->getCampo( 'venal_territorial_informado' ),2,',','.');
    $flPredialInformado     = number_format($rsInformado->getCampo( 'venal_predial_informado' ),2,',','.');
    $flTotalInformado       = number_format($rsInformado->getCampo( 'venal_total_informado' ),2,',','.');
}

/*if ( $obRARRAvaliacaoImobiliaria->getValorVenalTerritorial() ) {
    $flTerritorial = number_format($obRARRAvaliacaoImobiliaria->getValorVenalTerritorial(),2,',','.');
    $flPredial     = number_format($obRARRAvaliacaoImobiliaria->getValorVenalPredial(),2,',','.');
    $flTotalCalculado = number_format($obRARRAvaliacaoImobiliaria->getValorVenalTotal(),2,',','.');
}*/

$obRARRAvaliacaoImobiliaria->setInNumCGM( Sessao::read('numCgm') );
$obRARRAvaliacaoImobiliaria->ListarPermissaoUsuario( $rsUsuario );
if ( $rsUsuario->eof() )
    $boPermissaoVenal = false;
else
    $boPermissaoVenal = true;

//DEFINICAO DOS COMPONENTES DO FORMULARIO
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( $_REQUEST["stCtrl"]  );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $_REQUEST["stAcao"]  );

$obHdnVerificacao = new Hidden;
$obHdnVerificacao->setName  ( "boVerificacao" );
$obHdnVerificacao->setValue ( $boVerificacao  );

$obHdnNumCGM = new Hidden;
$obHdnNumCGM->setName ( "inNumCGM" );
$obHdnNumCGM->setValue( $_REQUEST['inNumCGM']  );

$obHdnNomCGM = new Hidden;
$obHdnNomCGM->setName ( "stNomCGM" );
$obHdnNomCGM->setValue( $_REQUEST['stNomCGM']  );

$obHdnTerritorialCalculado = new Hidden;
$obHdnTerritorialCalculado->setName ( "flTerritorialCalculado" );
$obHdnTerritorialCalculado->setValue( $flTerritorialCalculado  );

$obHdnPredialCalculado = new Hidden;
$obHdnPredialCalculado->setName  ( "flPredialCalculado" );
$obHdnPredialCalculado->setValue ( $flPredialCalculado  );

$obHdnTotalCalculado = new Hidden;
$obHdnTotalCalculado->setName  ( "flTotalCalculado" );
$obHdnTotalCalculado->setValue ( $flTotalCalculado  );

$obHdnInscricaoImobiliaria = new Hidden;
$obHdnInscricaoImobiliaria->setName ( "inInscricaoImobiliaria" );
$obHdnInscricaoImobiliaria->setValue( $_REQUEST['inInscricaoImobiliaria'] );

$obHdnInCodTransferencia = new Hidden;
$obHdnInCodTransferencia->setName ('inCodigoTransferencia');
$obHdnInCodTransferencia->setValue ( $_REQUEST["inCodigoTransferencia"] );

$obHdnInBoAutorizacaoCalcular = new Hidden;
$obHdnInBoAutorizacaoCalcular->setName ('boAutorizacaoCalcular');
$obHdnInBoAutorizacaoCalcular->setValue ( $boAutorizacaoCalcular );

$obLblContribuinte = new Label;
$obLblContribuinte->setName  ( "stContribuinte" );
$obLblContribuinte->setRotulo( "Contribuinte"   );
$obLblContribuinte->setValue ( $_REQUEST['inNumCGM']." - ".$_REQUEST['stNomCGM'] );

$obLblInscricao = new Label;
$obLblInscricao->setName  ( "inInscricao" );
$obLblInscricao->setRotulo( "Inscrição Imobiliária" );
$obLblInscricao->setValue ( $_REQUEST["inInscricaoImobiliaria"] );

$obLblInformacados = new Label;
$obLblInformacados->setRotulo( "&nbsp;"             );
$obLblInformacados->setName  ( "stInformacados"     );
$obLblInformacados->setValue ( "Valores Informados" );

$obLblCalculados = new Label;
$obLblCalculados->setRotulo( "&nbsp;"             );
$obLblCalculados->setName  ( "stCalculados"       );
$obLblCalculados->setValue ( "Valores Calculados" );

$obLblEspaco = new Label;
$obLblEspaco->setRotulo( "&nbsp;" );
$obLblEspaco->setName  ( "stEspaco" );
$obLblEspaco->setValue ( "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" );

$obTxtTerritorial = new Moeda;
$obTxtTerritorial->setName               ( "flTerritorialInformado"   );
$obTxtTerritorial->setRotulo             ( "Valor Venal Territorial" );
$obTxtTerritorial->setMaxLength          ( 15                );
$obTxtTerritorial->setSize               ( 15                );
$obTxtTerritorial->setValue              ( $flTerritorialInformado    );
$obTxtTerritorial->obEvento->setOnChange( "buscaValor('calculaTotal')" );

if ($boVerificacao == "calculado" || !$boPermissaoVenal) {
    $obTxtTerritorial->setDisabled          ( true );
}

$obLblTerritorial = new Label;
$obLblTerritorial->setName   ( "lblTerritorial" );
$obLblTerritorial->setRotulo ( "Valor Venal Territorial" );
$obLblTerritorial->setValue  ( $flTerritorialCalculado );

$obTxtPredial = new Moeda;
$obTxtPredial->setName               ( "flPredialInformado" );
$obTxtPredial->setRotulo             ( "Valor Venal Predial" );
$obTxtPredial->setMaxLength          ( 15          );
$obTxtPredial->setSize               ( 15          );
$obTxtPredial->setValue              ( $flPredialInformado  );
$obTxtPredial->obEvento->setOnChange( "buscaValor('calculaTotal')" );
if ($boVerificacao == "calculado" || !$boPermissaoVenal) {
    $obTxtPredial->setDisabled          ( true );
}

$obLblPredial = new Label;
$obLblPredial->setName   ( "lblPredial" );
$obLblPredial->setRotulo ( "Valor Venal Predial" );
$obLblPredial->setValue  ( $flPredialCalculado );

$obTxtTotalInformado = new Textbox;
$obTxtTotalInformado->setRotulo ( "Valor Venal Total" );
$obTxtTotalInformado->setId     ( "flTotalInformado" );
$obTxtTotalInformado->setName   ( "flTotalInformado" );
$obTxtTotalInformado->setValue  ( $flTotalInformado  );
$obTxtTotalInformado->setMaxLength( 15          );
$obTxtTotalInformado->setSize     ( 15          );
$obTxtTotalInformado->setDisabled( true          );

$obLblTotalCalculado = new Label;
$obLblTotalCalculado->setRotulo ( "Valor Venal Total" );
//$obLblTotalCalculado->setId     ( "flTotalCalculado"  );
$obLblTotalCalculado->setName   ( "flTotalCalculado"  );
$obLblTotalCalculado->setValue  ( $flTotalCalculado   );

$rsAtributos = new RecordSet;

//if ( $stAcao == "incluir" && !$obRARRAvaliacaoImobiliaria->getValorVenalTerritorial() ) {
//    $obRARRAvaliacaoImobiliaria->obRCadastroDinamico->recuperaAtributosSelecionados( $rsAtributos );
//} else {
    //DEFINICAO DOS ATRIBUTOS
    $arChaveAtributo = array( "inscricao_municipal" => $_REQUEST["inInscricaoImobiliaria"] );

    $obRARRAvaliacaoImobiliaria->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributo );
    $obRARRAvaliacaoImobiliaria->obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributos );
//}

$obMontaAtributos = new MontaAtributos;
$obMontaAtributos->setTitulo     ( "Atributos"  );
$obMontaAtributos->setName       ( "Atributo_"  );
$obMontaAtributos->setRecordSet  ( $rsAtributos );

/*
$obChkItbi = new CheckBox;
$obChkItbi->setName                ( "boItbi"                    );
$obChkItbi->setValue               ( "1"                         );
$obChkItbi->setLabel               ( "Efetuar cálculo de ITBI" );
$obChkItbi->setNull                ( true                           );
$obChkItbi->setChecked             ( false                          );
$obChkItbi->obEvento->setOnChange  ( "habilitaSpanITBI(this.checked,".$_REQUEST['inInscricaoImobiliaria'].");");
*/

$obTxtVencimento = new Data;
$obTxtVencimento->setName               ( "dtVencimento" );
$obTxtVencimento->setRotulo             ( "Vencimento da Cobrança de ITBI" );
$obTxtVencimento->setValue              ( $_REQUEST["dtVencimento"] );

/*
$obTxtExercicio = new TextBox ;
$obTxtExercicio->setName        ( "stExercicio"     );
$obTxtExercicio->setId          ( "stExercicio"     );
$obTxtExercicio->setMaxLength   ( 4                 );
$obTxtExercicio->setSize        ( 6                 );
$obTxtExercicio->setRotulo      ( "Exercício"       );
$obTxtExercicio->setTitle       ( "Exercício para valores informados" );
$obTxtExercicio->setNull        ( false              );
$obTxtExercicio->setValue       ( $_REQUEST["stExercicio"] );*/

$obTxtExercicio = new Exercicio;
$obTxtExercicio->setNull        ( false              );

$obRdoCarneNao = new Radio;
$obRdoCarneNao->setRotulo   ( "Emissão de Carnês" );
$obRdoCarneNao->setName     ( "boCarne" );
$obRdoCarneNao->setLabel    ( "Não emitir" );
$obRdoCarneNao->setValue    ( "não" );
$obRdoCarneNao->setChecked  ( false );
$obRdoCarneNao->setNull     ( false );

$obRdoCarneSim = new Radio;
$obRdoCarneSim->setRotulo   ( "Emissão de Carnês" );
$obRdoCarneSim->setName     ( "boCarne" );
$obRdoCarneSim->setLabel    ( "Impressão local" );
$obRdoCarneSim->setValue    ( "sim" );
$obRdoCarneSim->setChecked  ( true );

//$obSpnITBI = new Span;
//$obSpnITBI->setId   ( "spnITBI" );

$obSpnProprietarios = new Span;
$obSpnProprietarios->setId                   ( "spnProprietarios"                          );

$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( 'oculto' );

SistemaLegado::executaFramePrincipal( "buscaValor('MontarListas');" );

$obFormulario = new Formulario;
$obFormulario->addForm  ( $obForm );
$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnVerificacao );
$obFormulario->addHidden( $obHdnNumCGM );
$obFormulario->addHidden( $obHdnNomCGM );
$obFormulario->addHidden( $obHdnInscricaoImobiliaria );
$obFormulario->addHidden( $obHdnTerritorialCalculado );
$obFormulario->addHidden( $obHdnPredialCalculado );
$obFormulario->addHidden( $obHdnTotalCalculado );
$obFormulario->addHidden(   $obHdnInCodTransferencia     );
$obFormulario->addHidden ( $obHdnInBoAutorizacaoCalcular );
$obFormulario->addTitulo   ( "Dados para Avaliação de Transferência" );
//$obFormulario->addComponente( $obLblContribuinte );
$obFormulario->addComponente( $obLblInscricao );
$obFormulario->addSpan                   ( $obSpnProprietarios                               );

$obFormulario->agrupaComponentes( array( $obLblInformacados, $obLblEspaco, $obLblCalculados  ) );
$obFormulario->agrupaComponentes( array( $obTxtTerritorial , $obLblEspaco, $obLblTerritorial ) );
$obFormulario->agrupaComponentes( array( $obTxtPredial     , $obLblEspaco, $obLblPredial     ) );
$obFormulario->agrupaComponentes( array( $obTxtTotalInformado , $obLblEspaco, $obLblTotalCalculado  ) );
$obFormulario->addComponente( $obTxtExercicio );
//$obFormulario->addComponente( $obChkItbi );
//$obFormulario->addSpan      ( $obSpnITBI );

$obMontaAtributos->geraFormulario( $obFormulario );
//$obFormulario->addComponente( $obTxtVencimento );
//$obFormulario->agrupaComponentes( array( $obRdoCarneNao, $obRdoCarneSim ) );
$obFormulario->Cancelar($pgList);
$obFormulario->Show();
if ( $obRARRAvaliacaoImobiliaria->getValorVenalTerritorial() ) {
    SistemaLegado::executaFramePrincipal("buscaValor('buscaFuncao')");
}
