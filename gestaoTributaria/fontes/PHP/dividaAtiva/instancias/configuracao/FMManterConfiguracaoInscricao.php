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
    * Página de Formulário da Configuração do modulo divida ativa
    * Data de Criação   : 04/05/2006

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato

    * @ignore

    * $Id: FMManterConfiguracaoInscricao.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.04.01
*/

/*
$Log$
Revision 1.10  2007/03/26 20:28:08  cercato
Bug #8886#

Revision 1.9  2007/02/28 20:24:45  cercato
Bug #8514#

Revision 1.8  2007/01/02 18:44:14  cercato
correcao no tamanho do campo credito.

Revision 1.7  2006/09/15 14:36:02  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GT_DAT_NEGOCIO."RDATConfiguracao.class.php" );
include_once ( CAM_GT_MON_MAPEAMENTO."TMONCredito.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracao";
$pgForm = "FM".$stPrograma."Inscricao.php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once( $pgJS );

$obRDATConfiguracao = new RDATConfiguracao;
$obErro = $obRDATConfiguracao->consultar();
if ( !$obErro->ocorreu() ) {
    $stValorReferencia = $obRDATConfiguracao->getUtilizarValorReferencia();
    $stTipoValorReferencia = $obRDATConfiguracao->getTipoValorReferencia();
    $inMoeda = $obRDATConfiguracao->getMoedaValorReferencia();
    $inIndicador = $obRDATConfiguracao->getIndicadorValorReferencia();
    $inValorReferencia = $obRDATConfiguracao->getValorReferencia();
    $stcmbReferencia = $obRDATConfiguracao->getLimiteValorReferencia();
    $stUtilizarCreditoDividaAtiva = $obRDATConfiguracao->getUtilizarCreditoDivida();
    $inCreditoDivida = $obRDATConfiguracao->getCreditoDivida();
    $stNumeracaoInscricao = $obRDATConfiguracao->getNumeracaoInscricao();
}

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) || $stAcao == "alterar" ) {
    $stAcao = "inscricao";
}

//DEFINICAO DOS COMPONENTES
$obForm  = new Form;
$obForm->setAction ( $pgProc );
$obForm->settarget ( "oculto" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue ( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );

$obHdnMoeda = new Hidden;
$obHdnMoeda->setName ( "inHdnMoeda" );
$obHdnMoeda->setValue ( $inMoeda );

$obHdnIndicador = new Hidden;
$obHdnIndicador->setName ( "inHdnIndicadorEconomico" );
$obHdnIndicador->setValue ( $inIndicador );

$obHdnValorReferencia = new Hidden;
$obHdnValorReferencia->setName ( "inValorReferencia" );
$obHdnValorReferencia->setValue ( $inValorReferencia );

$obHdnMinMaxReferencia = new Hidden;
$obHdnMinMaxReferencia->setName ( "stcmbReferencia" );
$obHdnMinMaxReferencia->setValue ( $stcmbReferencia );

/*
//verificar emissao de notificacoes
$obRdbEmissaoNotificacao = new Radio;
$obRdbEmissaoNotificacao->setRotulo   ( "Verificar Emissão de Notificações" );
$obRdbEmissaoNotificacao->setTitle    ( "Verificar se foram emitidas notificações antes da inscrição em dívida ativa" );
$obRdbEmissaoNotificacao->setName     ( "stEmissaoNotificacao" );
$obRdbEmissaoNotificacao->setLabel    ( "Sim" );
$obRdbEmissaoNotificacao->setValue    ( "sim" );
$obRdbEmissaoNotificacao->setChecked  ( $stEmissaoNotificacao == "sim" );
$obRdbEmissaoNotificacao->setNull     ( false );

//verificar emissao de notificacoes
$obRdbNEmissaoNotificacao = new Radio;
$obRdbNEmissaoNotificacao->setRotulo   ( "Verificar Emissão de Notificações" );
$obRdbNEmissaoNotificacao->setTitle    ( "Verificar se foram emitidas notificações antes da inscrição em dívida ativa" );
$obRdbNEmissaoNotificacao->setName     ( "stEmissaoNotificacao" );
$obRdbNEmissaoNotificacao->setLabel    ( "Não" );
$obRdbNEmissaoNotificacao->setValue    ( "nao" );
$obRdbNEmissaoNotificacao->setChecked  ( $stEmissaoNotificacao == "nao" );
$obRdbNEmissaoNotificacao->setNull     ( false );
*/

//utilizar valor de referencia
$obRdbValorReferencia = new Radio;
$obRdbValorReferencia->setRotulo   ( "Utilizar Valor de Referência" );
$obRdbValorReferencia->setTitle    ( "Opção de utilizar ou não valor de referência" );
$obRdbValorReferencia->setName     ( "stValorReferencia" );
$obRdbValorReferencia->setLabel    ( "Sim" );
$obRdbValorReferencia->setValue    ( "sim" );
$obRdbValorReferencia->setChecked  ( $stValorReferencia == "sim" );
$obRdbValorReferencia->setNull     ( false );
$obRdbValorReferencia->obEvento->setOnClick ("habilitaValorReferencia( 'true' );");

//utilizar valor de referencia
$obRdbNValorReferencia = new Radio;
$obRdbNValorReferencia->setRotulo   ( "Utilizar Valor de Referência" );
$obRdbNValorReferencia->setTitle    ( "Opção de utilizar ou não valor de referência" );
$obRdbNValorReferencia->setName     ( "stValorReferencia" );
$obRdbNValorReferencia->setLabel    ( "Não" );
$obRdbNValorReferencia->setValue    ( "nao" );
$obRdbNValorReferencia->setChecked  ( $stValorReferencia == "nao" );
$obRdbNValorReferencia->setNull     ( false );
$obRdbNValorReferencia->obEvento->setOnClick ("habilitaValorReferencia( 'false' );");

//tipo do valor de referencia
$obRdbTipoValorReferencia = new Radio;
$obRdbTipoValorReferencia->setRotulo   ( "Tipo do Valor de Referência" );
$obRdbTipoValorReferencia->setTitle    ( "Opção do tipo de valor de referência a ser utilizado" );
$obRdbTipoValorReferencia->setName     ( "stTipoValorReferencia" );
$obRdbTipoValorReferencia->setLabel    ( "Moeda" );
$obRdbTipoValorReferencia->setValue    ( "moeda" );
$obRdbTipoValorReferencia->setChecked  ( $stTipoValorReferencia == "moeda" );
$obRdbTipoValorReferencia->setNull     ( false );
$obRdbTipoValorReferencia->obEvento->setOnChange ( "buscaValor('montaTipoValorReferencia');");

//tipo do valor de referencia
$obRdbNTipoValorReferencia = new Radio;
$obRdbNTipoValorReferencia->setRotulo   ( "Tipo do Valor de Referência" );
$obRdbNTipoValorReferencia->setTitle    ( "Opção do tipo de valor de referência a ser utilizado" );
$obRdbNTipoValorReferencia->setName     ( "stTipoValorReferencia" );
$obRdbNTipoValorReferencia->setLabel    ( "Indicador Econômico" );
$obRdbNTipoValorReferencia->setValue    ( "indicador" );
$obRdbNTipoValorReferencia->setChecked  ( $stTipoValorReferencia == "indicador" );
$obRdbNTipoValorReferencia->setNull     ( false );
$obRdbNTipoValorReferencia->obEvento->setOnChange ( "buscaValor('montaTipoValorReferencia');");

//span tipo do valor de referencia
$obSpnTipoValor = new Span;
$obSpnTipoValor->setID("spnTipoValor");

/*
//utilizar credito de divida ativa
$obRdbUtilizarCreditoDividaAtiva = new Radio;
$obRdbUtilizarCreditoDividaAtiva->setRotulo   ( "Utilizar Crédito de Dívida Ativa" );
$obRdbUtilizarCreditoDividaAtiva->setTitle    ( "Opção de utilizar ou não crédito específico de dívida ativa" );
$obRdbUtilizarCreditoDividaAtiva->setName     ( "stUtilizarCreditoDividaAtiva" );
$obRdbUtilizarCreditoDividaAtiva->setLabel    ( "Sim" );
$obRdbUtilizarCreditoDividaAtiva->setValue    ( "sim" );
$obRdbUtilizarCreditoDividaAtiva->setChecked  ( $stUtilizarCreditoDividaAtiva == "sim" );
$obRdbUtilizarCreditoDividaAtiva->setNull     ( false );
$obRdbUtilizarCreditoDividaAtiva->obEvento->setOnClick ("habilitaCredito('true');");

//utilizar credito de divida ativa
$obRdbNUtilizarCreditoDividaAtiva = new Radio;
$obRdbNUtilizarCreditoDividaAtiva->setRotulo   ( "Utilizar Crédito de Dívida Ativa" );
$obRdbNUtilizarCreditoDividaAtiva->setTitle    ( "Opção de utilizar ou não crédito específico de dívida ativa" );
$obRdbNUtilizarCreditoDividaAtiva->setName     ( "stUtilizarCreditoDividaAtiva" );
$obRdbNUtilizarCreditoDividaAtiva->setLabel    ( "Não" );
$obRdbNUtilizarCreditoDividaAtiva->setValue    ( "nao" );
$obRdbNUtilizarCreditoDividaAtiva->setChecked  ( $stUtilizarCreditoDividaAtiva == "nao" );
$obRdbNUtilizarCreditoDividaAtiva->setNull     ( false );
$obRdbNUtilizarCreditoDividaAtiva->obEvento->setOnClick ("habilitaCredito('false');");

//credito de divida ativa
$obTMONCredito = new TMONCredito;
$obTMONCredito->recuperaMascaraCredito( $rsMascara );
if ( !$rsMascara->eof() ) {
    $stMascaraCredito = $rsMascara->getCampo("mascara_credito");
}

$obBscCredito = new BuscaInner;
$obBscCredito->setRotulo( "*Crédito de Dívida" );
$obBscCredito->setId( "stCreditoDivida" );
$obBscCredito->obCampoCod->setName("inCreditoDivida");
$obBscCredito->obCampoCod->setValue( $inCreditoDivida );
$obBscCredito->obCampoCod->setMaxLength ( strlen($stMascaraCredito) );
$obBscCredito->obCampoCod->setMinLength ( strlen($stMascaraCredito) );
$obBscCredito->obCampoCod->setMascara   ( $stMascaraCredito   );
$obBscCredito->obCampoCod->obEvento->setOnChange("buscaValor('buscaCreditoDivida');");
$obBscCredito->setFuncaoBusca( "abrePopUp('".CAM_GT_MON_POPUPS."credito/FLProcurarCredito.php','frm','inCreditoDivida','stCreditoDivida','todos','".Sessao::getId()."','800','550');" );
$obBscCredito->obCampoCod->setInteiro( false );

*/

//numeracao de inscricao
$obRdbNumeracaoInscricao = new Radio;
$obRdbNumeracaoInscricao->setRotulo   ( "Numeração de Inscrição" );
$obRdbNumeracaoInscricao->setTitle    ( "Seqüencia a ser seguida na numeração de inscrição" );
$obRdbNumeracaoInscricao->setName     ( "stNumeracaoInscricao" );
$obRdbNumeracaoInscricao->setLabel    ( "Seqüencial" );
$obRdbNumeracaoInscricao->setValue    ( "sequencial" );
$obRdbNumeracaoInscricao->setChecked  ( $stNumeracaoInscricao == "sequencial" );
$obRdbNumeracaoInscricao->setNull     ( false );

//numeracao de inscricao
$obRdbENumeracaoInscricao = new Radio;
$obRdbENumeracaoInscricao->setRotulo   ( "Numeração de Inscrição" );
$obRdbENumeracaoInscricao->setTitle    ( "Seqüencia a ser seguida na numeração de inscrição" );
$obRdbENumeracaoInscricao->setName     ( "stNumeracaoInscricao" );
$obRdbENumeracaoInscricao->setLabel    ( "Seqüencial por Exercício" );
$obRdbENumeracaoInscricao->setValue    ( "exercicio" );
$obRdbENumeracaoInscricao->setChecked  ( $stNumeracaoInscricao == "exercicio" );
$obRdbENumeracaoInscricao->setNull     ( false );

$obBtnLimpar = new Button;
$obBtnLimpar->setName               ( "btnLimpar" );
$obBtnLimpar->setValue              ( "Limpar" );
$obBtnLimpar->setTipo               ( "button" );
$obBtnLimpar->obEvento->setOnClick  ( "LimparInscricao();" );
$obBtnLimpar->setDisabled           ( false );

$obBtnOK = new OK;

$arBotoes = array( $obBtnOK, $obBtnLimpar );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm ( $obForm );
$obFormulario->addHidden ( $obHdnCtrl );
$obFormulario->addHidden ( $obHdnAcao );
$obFormulario->addHidden ( $obHdnMoeda );
$obFormulario->addHidden ( $obHdnIndicador );
$obFormulario->addHidden ( $obHdnValorReferencia );
$obFormulario->addHidden ( $obHdnMinMaxReferencia );
$obFormulario->addTitulo ( "Dados para Configuração da Inscrição" );
$obFormulario->addComponenteComposto ( $obRdbValorReferencia, $obRdbNValorReferencia );
$obFormulario->addComponenteComposto ( $obRdbTipoValorReferencia, $obRdbNTipoValorReferencia );
$obFormulario->addSpan ( $obSpnTipoValor );
//$obFormulario->addComponenteComposto ( $obRdbUtilizarCreditoDividaAtiva, $obRdbNUtilizarCreditoDividaAtiva );
//$obFormulario->addComponente ( $obBscCredito );
$obFormulario->addComponenteComposto ( $obRdbNumeracaoInscricao, $obRdbENumeracaoInscricao );

$obFormulario->defineBarra( $arBotoes );
$obFormulario->show();

if ($inMoeda || $inIndicador) {
    SistemaLegado::executaFrameOculto ( "buscaValor('montaTipoValorReferenciaPreenxer');" );
} else {
    SistemaLegado::executaFrameOculto ( "buscaValor('limpaArray');" );
}

?>
