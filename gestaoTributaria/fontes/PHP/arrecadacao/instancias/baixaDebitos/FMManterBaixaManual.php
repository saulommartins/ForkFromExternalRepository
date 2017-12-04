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
    * Página de Formulario para inclusao na tabela arrecadaçaõ tipo pagamento
    * Data de Criação   : 05/12/2005

    * @author Analista      : Fabio Bertoldi Rodrigues
    * @author Desenvolvedor : Marcelo B. Paulino

    * @ignore

    * $Id: FMManterBaixaManual.php 63839 2015-10-22 18:08:07Z franver $

    * Casos de uso: uc-05.03.11
*/

/*
$Log$
Revision 1.23  2007/06/13 14:01:29  cercato
Bug #9387#

Revision 1.22  2007/04/25 18:48:26  dibueno
Bug #9217#

Revision 1.21  2007/03/12 19:30:21  cercato
adicionada opcao para baixa da carne da divida.

Revision 1.20  2007/02/16 11:31:37  dibueno
Bug #8432#

Revision 1.19  2006/11/17 11:48:54  cercato
bug #7357#

Revision 1.18  2006/09/15 11:50:21  fabio
corrigidas tags de caso de uso

Revision 1.17  2006/09/15 10:55:12  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_ARR_NEGOCIO."RARRTipoPagamento.class.php" );
include_once ( CAM_GT_ARR_NEGOCIO."RARRConfiguracao.class.php"  );
include_once ( CAM_GT_MON_NEGOCIO."RMONAgencia.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterBaixaManual";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";
include_once( $pgJs );

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

Sessao::write( 'link', "" );

$obRARRConfiguracao = new RARRConfiguracao;
$obRARRConfiguracao->setAnoExercicio( Sessao::getExercicio() );
$obRARRConfiguracao->consultarMascaraProcesso( $stMascaraProcesso );

$rsTipoPagamento = new RecordSet;

$obRMONAgencia = new RMONAgencia();
$rsBanco = new recordSet();
$rsAgencia = new recordSet();

$obRMONAgencia->obRMONBanco->listarBanco($rsBanco);

//DEFINICAO DOS COMPONENTES
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnExercicio = new Hidden;
$obHdnExercicio->setName( "stExercicio" );
$obHdnExercicio->setValue( $_REQUEST["stExercicio"] );

$obHdnCodConvenio = new Hidden;
$obHdnCodConvenio->setName( "inCodConvenio" );
$obHdnCodConvenio->setValue( $_REQUEST["inCodConvenio"] );

$obHdnCodLancamento = new Hidden;
$obHdnCodLancamento->setName( "inCodLancamento" );
$obHdnCodLancamento->setValue( $_REQUEST["inCodLancamento"] );

$obHdnValorOriginal = new Hidden;
$obHdnValorOriginal->setName( "nuValorOriginal" );
$obHdnValorOriginal->setValue( $_REQUEST["nuValorParcela"] );

$obHdnValorTotal = new Hidden;
$obHdnValorTotal->setName( "nuValorTotal" );

$obHdnCodigoTipo = new Hidden;
$obHdnCodigoTipo->setName( "inCodigoTipo" );
$obHdnCodigoTipo->setValue( $_REQUEST["inCodigoTipo"] );

$obLblNumeracao   = new Label;
$obLblNumeracao->setRotulo( "Numeração" );
$obLblNumeracao->setValue( $_REQUEST['inNumeracao'] );

$obLblVencimento   = new Label;
$obLblVencimento->setRotulo( "Vencimento" );
$obLblVencimento->setValue( $_REQUEST['dtVencimento'] );

$obHdnVencimento = new Hidden;
$obHdnVencimento->setName ( "dtVencimento" );
$obHdnVencimento->setValue( $_REQUEST['dtVencimento'] );

$obHdnNumeracao = new Hidden;
$obHdnNumeracao->setName ( "inNumeracao" );
$obHdnNumeracao->setValue( $_REQUEST['inNumeracao'] );

$obLblValor = new Label;
$obLblValor->setRotulo( "Valor" );
$obLblValor->setValue ( "R$ ".number_format( $_REQUEST['nuValorParcela'], 2, ",", "." ) );

$obLblJuro = new Label;
$obLblJuro->setRotulo( "Juro" );
$obLblJuro->setId("Juro");
//$obLblJuro->setValue( 'R$ 0,00' );

$obLblMulta = new Label;
$obLblMulta->setRotulo( "Multa" );
$obLblMulta->setId("Multa");
//$obLblMulta->setValue( 'R$ 0,00' );

$obLblCorrecao = new Label;
$obLblCorrecao->setRotulo( "Correção" );
$obLblCorrecao->setId("Correcao");
//$obLblCorrecao->setValue( 'R$ 0,00' );

$obLblValorCorrigido = new Label;
$obLblValorCorrigido->setRotulo( "Valor Corrigido" );
$obLblValorCorrigido->setId("ValorCorrigido");
//$obLblValorCorrigido->setValue( "R$ ".number_format( $_REQUEST['nuValorParcela'], 2, ",", "." ) );

$obHdnNumeracaoMigrada = new Hidden;
$obHdnNumeracaoMigrada->setName ( "inNumeracaoMigrada" );
$obHdnNumeracaoMigrada->setValue( $_REQUEST['inNumeracaoMigrada'] );

$obHdnNumeroParcela = new Hidden;
$obHdnNumeroParcela->setName ( "inNrParcela" );
$obHdnNumeroParcela->setValue( $_REQUEST['inNrParcela'] );

$obHdnParcelaValida = new Hidden;
$obHdnParcelaValida->setName ( "boValida" );
$obHdnParcelaValida->setValue( $_REQUEST['boValida'] );

$obLblContribuinte   = new Label;
$obLblContribuinte->setRotulo    ( "Contribuinte" );
$obLblContribuinte->setValue     ( $_REQUEST['inNumCgm']." - ".$_REQUEST['stNomCgm'] );

if ( Sessao::read( 'stRefCred' ) != 'cgm' ) {
    $obLblInscricao   = new Label;
    if ( Sessao::read( 'stRefCred' ) == 'ii') {
        $stLabel = 'Inscrição Imobiliária';
        $obLblInscricao->setValue     ( $_REQUEST['inInscricao'] );
    } elseif ( Sessao::read( 'stRefCred' ) == 'ie' ) {
        $stLabel = 'Inscrição Econômica';
        $obLblInscricao->setValue     ( $_REQUEST['inInscricao'] );
    }

    $obLblInscricao->setRotulo    ( $stLabel  );

}

$obTxtBanco = new TextBox;
$obTxtBanco->setRotulo        ( "Banco"                            );
$obTxtBanco->setTitle         ( "Banco ao qual a agência pertence." );
$obTxtBanco->setName          ( "inNumbanco"                       );
$obTxtBanco->setValue         ( $inNumBanco                        );
$obTxtBanco->setSize          ( 10                                 );
$obTxtBanco->setMaxLength     ( 6                                  );
$obTxtBanco->setNull          ( false                              );
$obTxtBanco->setInteiro       ( true                               );
$obTxtBanco->obEvento->setOnChange ( "montaParametrosGET('preencheAgencia');" );

$obCmbBanco = new Select;
$obCmbBanco->setName          ( "cmbBanco"                   );
$obCmbBanco->addOption        ( "", "Selecione"              );
$obCmbBanco->setValue         ( $_REQUEST['inNumBanco']      );
$obCmbBanco->setCampoId       ( "num_banco"                  );
$obCmbBanco->setCampoDesc     ( "nom_banco"                  );
$obCmbBanco->preencheCombo    ( $rsBanco                     );
$obCmbBanco->setNull          ( false                        );
$obCmbBanco->setStyle         ( "width: 220px"               );
$obCmbBanco->obEvento->setOnChange ( "montaParametrosGET('preencheAgencia');"  );

$obTxtAgencia = new TextBox;
$obTxtAgencia->setRotulo        ( "Agência"                                     );
$obTxtAgencia->setTitle         ( "Agência bancária na qual a conta foi aberta." );
$obTxtAgencia->setName          ( "inNumAgencia"                                );
$obTxtAgencia->setValue         ( $inNumAgencia                                 );
$obTxtAgencia->setSize          ( 10                                            );
$obTxtAgencia->setMaxLength     ( 6                                             );
$obTxtAgencia->setNull          ( false                                         );
$obTxtAgencia->obEvento->setOnKeyPress( "return validar(event)" );

$obCmbAgencia = new Select;
$obCmbAgencia->setName          ( "cmbAgencia"                   );
$obCmbAgencia->addOption        ( "", "Selecione"                );
$obCmbAgencia->setValue         ( $_REQUEST['inNumAgencia']      );
$obCmbAgencia->setCampoId       ( "num_agencia"                  );
$obCmbAgencia->setCampoDesc     ( "nom_agencia"                  );
$obCmbAgencia->preencheCombo    ( $rsAgencia                     );
$obCmbAgencia->setNull          ( false                          );
$obCmbAgencia->setStyle         ( "width: 220px"                 );

$obHdnCgm = new Hidden;
$obHdnCgm->setName ( "inNumCgm" );
$obHdnCgm->setValue( $_REQUEST['inNumCgm'] );

$obDtPagamento = new Data;
$obDtPagamento->setName      ( "dtPagamento"       );
$obDtPagamento->setValue     ( $dtPagamento        );
$obDtPagamento->setRotulo    ( "Data do Pagamento" );
$obDtPagamento->setMaxLength ( 20                  );
$obDtPagamento->setSize      ( 10                  );
$obDtPagamento->setNull      ( false               );
$obDtPagamento->obEvento->setOnChange( "montaParametrosGET('atualizaValorCorrigido');" );

$obSpnValor = new Span;
$obSpnValor->setId( "spnValor" );

$obCmbTipo = new Select;
$obCmbTipo->setName          ( "stTipo"            );
$obCmbTipo->setRotulo        ( "Tipo de Pagamento" );
$obCmbTipo->setNull          ( false               );
$obCmbTipo->setCampoId       ( "[cod_tipo]-[pagamento]" );
$obCmbTipo->setCampoDesc     ( "nom_tipo"          );
$obCmbTipo->addOption        ( "", "Selecione"     );
$obCmbTipo->preencheCombo    ( $rsTipoPagamento    );
$obCmbTipo->setValue         ( $inCodigoTipo       );
$obCmbTipo->obEvento->setOnChange( "montaParametrosGET('buscaUtilizacaoTipoPagamento');" );

$obLblTipo = new Label;
$obLblTipo->setRotulo( "Tipo de Pagamento" );
$obLblTipo->setId    ( "stLblTipo" );
$obLblTipo->setValue ( '&nbsp;' );

$obBscProcesso = new BuscaInner;
$obBscProcesso->setRotulo ( "Processo" );
//$obBscProcesso->setTitle ( "Número do processo no protocolo que gerou a aprovação da baixa." );
$obBscProcesso->setNull ( true );
$obBscProcesso->obCampoCod->setName ("inProcesso");
$obBscProcesso->obCampoCod->setId   ("inProcesso");
$obBscProcesso->obCampoCod->setValue( $inProcesso );
$obBscProcesso->obCampoCod->obEvento->setOnChange( "buscaValor('buscaProcesso','');" );
$obBscProcesso->obCampoCod->obEvento->setOnKeyUp( "mascaraDinamico('".$stMascaraProcesso."', this, event);" );
$obBscProcesso->setFuncaoBusca( "abrePopUp('".CAM_GA_PROT_POPUPS."processo/FLBuscaProcessos.php','frm','inProcesso','campoInner2','','".Sessao::getId()."','800','550')" );

$obTxtObservacao = new TextArea;
$obTxtObservacao->setName ( "stObservacao" );
$obTxtObservacao->setRotulo ( "Observação" );
$obTxtObservacao->setValue ( "" );

/*
$obChkFechaBaixa = new CheckBox;
$obChkFechaBaixa->setName    ( "boFechaBaixa"                       );
$obChkFechaBaixa->setValue   ( "1"                                  );
$obChkFechaBaixa->setLabel   ( "Efetuar Fechamento de Baixa Manual" );
$obChkFechaBaixa->setNull    ( true                                 );
$obChkFechaBaixa->setChecked ( false                                );
*/

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgProc  );
$obForm->setTarget( "oculto" );

$obFormulario = new Formulario;
$obFormulario->addTitulo( "Dados para Baixa Manual de Débito" );
$obFormulario->addForm  ( $obForm             );
$obFormulario->addHidden( $obHdnCtrl          );
$obFormulario->addHidden( $obHdnAcao          );
$obFormulario->addHidden( $obHdnNumeracao     );
$obFormulario->addHidden( $obHdnCodLancamento );
$obFormulario->addHidden( $obHdnCgm           );
$obFormulario->addHidden( $obHdnValorOriginal );
$obFormulario->addHidden( $obHdnExercicio     );
$obFormulario->addHidden( $obHdnCodConvenio   );
$obFormulario->addHidden( $obHdnVencimento    );
$obFormulario->addHidden( $obHdnNumeroParcela );
$obFormulario->addHidden( $obHdnParcelaValida );
$obFormulario->addHidden( $obHdnValorTotal );
$obFormulario->addComponente( $obLblNumeracao      );
$obFormulario->addComponente( $obLblVencimento      );
$obFormulario->addComponente( $obLblValor          );
$obFormulario->addComponente( $obLblJuro           );
$obFormulario->addComponente( $obLblMulta          );
$obFormulario->addComponente( $obLblCorrecao       );
$obFormulario->addComponente( $obLblValorCorrigido );
$obFormulario->addComponente( $obLblContribuinte   );
if ( Sessao::read( 'stRefCred' ) != 'cgm' )
    $obFormulario->addComponente( $obLblInscricao);

$obFormulario->addComponenteComposto    ($obTxtBanco,$obCmbBanco      );
$obFormulario->addComponenteComposto    ($obTxtAgencia,$obCmbAgencia  );

$obFormulario->addComponente( $obDtPagamento     );

$obFormulario->addComponente( $obCmbTipo         );
$obFormulario->addComponente( $obLblTipo         );

$obFormulario->addSpan      ( $obSpnValor        );
$obFormulario->addComponente( $obBscProcesso     );
$obFormulario->addComponente( $obTxtObservacao   );
//$obFormulario->addComponente( $obChkFechaBaixa   );

$obBtnOK = new Ok;
$obBtnCancelar = new Cancelar;
$obBtnCancelar->obEvento->setOnClick ( "CancelarBaixa()" );
$obBtnLimpar = new Limpar;
$botoesForm = array ( $obBtnOK, $obBtnCancelar, $obBtnLimpar );

$obFormulario->defineBarra   ( $botoesForm, 'left', '' );
//$obFormulario->Ok();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
