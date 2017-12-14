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
    * Página de Formulario para EFETUAR LANCAMENTOS  - MODULO ARRECADACAO
    * Data de criação : 01/06/2005

    * @author Analista: Fabio Bertold Rodrigues
    * @author Programador: Marcelo Boezzio Paulino

    * $Id: FMEfetuarLancamentosManual.php 59612 2014-09-02 12:00:51Z gelson $

    Caso de uso: uc-05.03.05
**/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_MON_NEGOCIO."RMONCredito.class.php" );
include_once ( CAM_GT_ARR_NEGOCIO."RARRConfiguracao.class.php" );
include_once( CAM_GT_ARR_NEGOCIO."RARRCarne.class.php" );
include_once '../../../../../../gestaoTributaria/fontes/PHP/arrecadacao/classes/componentes/MontaGrupoCredito.class.php';

//Define o nome dos arquivos PHP
$stPrograma      = "EfetuarLancamentos";
$pgFilt          = "FL".$stPrograma.".php";
$pgList          = "LS".$stPrograma.".php";
$pgForm          = "FM".$stPrograma.".php";
$pgProc          = "PR".$stPrograma.".php";
$pgFormManual = "FM".$stPrograma."Manual.php";
$pgOcul          = "OCManterCalculo.php";
$pgJs            = "JSManterCalculo.js";
include_once( $pgJs );

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

Sessao::remove( "sessao_transf6" );
Sessao::write( "link", "" );
// instancia objeto
$obMontaGrupoCredito = new MontaGrupoCredito;
$obRMONCredito = new RMONCredito;
//$obRModeloDocumento = new RModeloDocumento;
// pegar mascara de credito
$obRMONCredito->consultarMascaraCredito();
$stMascaraCredito = $obRMONCredito->getMascaraCredito();
$obRARRConfiguracao = new RARRConfiguracao;
$obRARRConfiguracao->setAnoExercicio ( Sessao::getExercicio() );
$obRARRConfiguracao->consultarMascaraProcesso( $stMascaraProcesso );

Sessao::write( 'parcelas', array() );
// OBJETOS HIDDEN
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( $stCtrl  );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao  );

$obHdnNome = new Hidden;
$obHdnNome->setID    ("stNomeTMP");
$obHdnNome->setName  ( "stNomeTMP" );

$obHdnCodModulo = new Hidden;
$obHdnCodModulo->setName  ( "inCodModulo" );
$obHdnCodModulo->setValue ( $_REQUEST["inCodModulo"] );

$obHdnTipoManual = new Hidden;
$obHdnTipoManual->setName  ( "inCodModulo" );
$obHdnTipoManual->setValue ( $_REQUEST["boTipoLancamentoManual"] );

$obHdnExercicio = new Hidden;
$obHdnExercicio->setName  ( "inExercicio" );

$obHdnFormLancamentoManual = new Hidden;
$obHdnFormLancamentoManual->setName ( "FormLancamentoManual" );
$obHdnFormLancamentoManual->setValue ( $_REQUEST["boTipoLancamentoManual"]  );

// DEFINE OBJETOS DO FORMULARIO

$obBscCredito = new BuscaInner;
$obBscCredito->setRotulo( "*Crédito" );
$obBscCredito->setTitle( "Busca crédito." );
$obBscCredito->setId( "stCredito" );
$obBscCredito->obCampoCod->setName("inCodCredito");
$obBscCredito->obCampoCod->setId  ("inCodCredito");
$obBscCredito->setMonitorarCampoCod (true);
$obBscCredito->obCampoCod->setValue( $inCodCredito );
$obBscCredito->obCampoCod->setMaxLength ( strlen($stMascaraCredito) );
$obBscCredito->obCampoCod->setMinLength ( strlen($stMascaraCredito) );
$obBscCredito->obCampoCod->setMascara   ($stMascaraCredito          );
$obBscCredito->obCampoCod->obEvento->setOnChange("buscaValor('BuscaDoCredito');");
$obBscCredito->setFuncaoBusca( "abrePopUp('".CAM_GT_MON_POPUPS."credito/LSProcurarCredito.php','frm','inCodCredito','stCredito','todos','".Sessao::getId()."','800','550');" );

$obRdoCarneNao = new Radio;
$obRdoCarneNao->setTitle    ( "Informe se deverá ou não ser emitido carnê de cobrança." );
$obRdoCarneNao->setRotulo   ( "Emissão de Carnês" );
$obRdoCarneNao->setName     ( "emissao_carnes" );
$obRdoCarneNao->setLabel    ( "Não emitir" );
$obRdoCarneNao->setValue    ( "não" );
$obRdoCarneNao->setChecked  ( true );
$obRdoCarneNao->setNull     ( false );
$obRdoCarneNao->obEvento->setOnChange ( "buscaValor('montaModeloCarne');"   );

$obRdoCarneSim = new Radio;
$obRdoCarneSim->setName      ( "emissao_carnes" );
$obRdoCarneSim->setLabel     ( "Impressão local" );
$obRdoCarneSim->setValue     ( "local" );
$obRdoCarneSim->setChecked   ( false );
$obRdoCarneSim->obEvento->setOnChange ( "buscaValor('montaModeloCarne');"   );

$obRdoCarneGrafica = new Radio;
$obRdoCarneGrafica->setName         ( "boCarne" );
$obRdoCarneGrafica->setLabel        ( "Gráfica" );
$obRdoCarneGrafica->setValue        ( "Gráfica" );
$obRdoCarneGrafica->setChecked      ( false );

$obRdoFiltroCGM = new Radio;
$obRdoFiltroCGM->setTitle   ( "Filtro a ser utilizado para o cálculo." );
$obRdoFiltroCGM->setRotulo   ( "Filtrar Por" );
$obRdoFiltroCGM->setName    ( "stReferencia" );
$obRdoFiltroCGM->setLabel     ( "CGM" );
$obRdoFiltroCGM->setValue    ( "cgm" );
$obRdoFiltroCGM->setChecked  ( true );
$obRdoFiltroCGM->setNull      ( false );
$obRdoFiltroCGM->obEvento->setOnChange ( "buscaValor('referencia');"   );

$obRdoFiltroImobiliaria = new Radio;
$obRdoFiltroImobiliaria->setName   ( "stReferencia" );
$obRdoFiltroImobiliaria->setLabel    ( "Inscrição Imobiliária" );
$obRdoFiltroImobiliaria->setValue    ( "ii" );
$obRdoFiltroImobiliaria->setChecked  ( false );
$obRdoFiltroImobiliaria->obEvento->setOnChange ( "buscaValor('referencia');"   );

$obRdoFiltroEconomica = new Radio;
$obRdoFiltroEconomica->setName   ( "stReferencia" );
$obRdoFiltroEconomica->setLabel    ( "Inscrição Econômica" );
$obRdoFiltroEconomica->setValue    ( "ie" );
$obRdoFiltroEconomica->setChecked  ( false );
$obRdoFiltroEconomica->obEvento->setOnChange ( "buscaValor('referencia');"   );

$obBscProcesso = new BuscaInner;
$obBscProcesso->setRotulo ( "Processo" );
$obBscProcesso->setTitle ( "Processo referente ao lançamento manual." );
$obBscProcesso->setNull ( true );
$obBscProcesso->obCampoCod->setName ("inProcesso");
$obBscProcesso->obCampoCod->setId   ("inProcesso");
$obBscProcesso->obCampoCod->setValue( $inProcesso );
$obBscProcesso->obCampoCod->obEvento->setOnChange( "buscaValor('buscaProcesso','');" );
$obBscProcesso->obCampoCod->setSize ( strlen($stMascaraProcesso) );
$obBscProcesso->obCampoCod->setMaxLength( strlen($stMascaraProcesso) );
$obBscProcesso->obCampoCod->obEvento->setOnKeyUp ("mascaraDinamico('".$stMascaraProcesso."', this, event);");
$obBscProcesso->setFuncaoBusca( "abrePopUp('".CAM_GA_PROT_POPUPS."processo/FLBuscaProcessos.php','frm','inProcesso','campoInner2','','".Sessao::getId()."','800','550')" );

$obTxtObservacao = new TextArea;
$obTxtObservacao->setName ( "stObservacao" );
$obTxtObservacao->setRotulo ( "Observações p/ Boleto" );
$obTxtObservacao->setTitle ( "Observações referentes ao cálculo disponíveis para o contribuinte." );
$obTxtObservacao->setValue ( "" );
$obTxtObservacao->setNull    ( true );
$obTxtObservacao->setCols   ( 30 );
$obTxtObservacao->setRows  ( 5 );
$obTxtObservacao->setMaxCaracteres(300);

$obTxtObservacaoInterna = new TextArea;
$obTxtObservacaoInterna->setName ( "stObservacaoInterna" );
$obTxtObservacaoInterna->setTitle ( "Observações referentes ao cálculo disponíveis apenas no sistema." );
$obTxtObservacaoInterna->setRotulo ( "Observações Internas" );
$obTxtObservacaoInterna->setValue ( "" );
$obTxtObservacaoInterna->setNull    ( true );
$obTxtObservacaoInterna->setCols   ( 30 );
$obTxtObservacaoInterna->setRows  ( 5 );

$obTxtExercicio = new Exercicio;
$obTxtExercicio->setName('inExercicioCalculo');

$obRARRCarne = new RARRCarne;
$obRARRCarne->listarModeloDeCarne( $rsModelos, Sessao::read('acao') );

$obTxtValor = new Numerico;
$obTxtValor->setRotulo  ( 'Valor');
$obTxtValor->setTitle   ( 'Valor referente ao Indicador Econômico');
$obTxtValor->setName    ( 'inValor');
$obTxtValor->setValue   ( $valor );
$obTxtValor->setDecimais ( 2 );
$obTxtValor->setMaxValue  ( 9999999.99 );
$obTxtValor->setNull      ( false );
$obTxtValor->setNegativo  ( false );
$obTxtValor->setNaoZero   ( true );
$obTxtValor->setSize    ( 12 );
$obTxtValor->setMaxLength ( 12 );

$obLblTotal = new Label;
$obLblTotal->setRotulo ('Valor Total (R$)');
$obLblTotal->setTitle ( "Valor do crédito a ser lançado." );
$obLblTotal->setId ('obValorTotal');
$obLblTotal->setValue ('0,00');
$obHdnTotal = new Hidden;
$obHdnTotal->setName ('obHdnValorTotal');
$obHdnTotal->setValue ($valor);

//SPANs
$obSpnCreditos = new Span;
$obSpnCreditos->setId  ( "spnCreditos" );

//SPAN FILTRO
$obSpnReferencia = new Span;
$obSpnReferencia->setId ( "spnReferencia" );

//SPAN MODO PARCELAMENTO
$obSpnModoParcelamento = new Span;
$obSpnModoParcelamento->setId ( "spnModoParcelamento" );

//SPAN PARCELAS
$obSpnParcelas = new Span;
$obSpnParcelas->setId ( 'spnParcelas' );

$spnTotalCreditos = new Span;
$spnTotalCreditos->setId ( 'spnTotalCreditos' );

$spnCarne = new Span;
$spnCarne->setId ( 'spnModelo' );

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction           ( $pgProc  );
$obForm->setTarget           ( "oculto" );
//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm              );
$obFormulario->addHidden     ( $obHdnCtrl           );
$obFormulario->addHidden     ( $obHdnAcao           );
$obFormulario->addHidden     ( $obHdnNome           );
$obFormulario->addHidden     ( $obHdnCodModulo      );
$obFormulario->addHidden     ( $obHdnFormLancamentoManual );
$obFormulario->addHidden     ( $obHdnExercicio );
$obFormulario->addTitulo     ( "Dados para Cálculo" );

if ($_REQUEST["boTipoLancamentoManual"] == 'Crédito') {
    $obFormulario->addComponente ( $obBscCredito   );
    $obFormulario->addComponente ( $obTxtValor );
    $obFormulario->addComponente ( $obTxtExercicio );
} else {
    $obMontaGrupoCredito->setTipo ("lancamento");
    $obMontaGrupoCredito->geraFormulario( $obFormulario, true, true );
    $obFormulario->addSpan ( $obSpnCreditos );
    $obFormulario->addComponente ( $obLblTotal );
    $obFormulario->addHidden ( $obHdnTotal );

}
    $obFormulario->addTitulo ('Filtros para Cálculo' );
    $obFormulario->agrupaComponentes( array( $obRdoFiltroCGM, $obRdoFiltroImobiliaria,$obRdoFiltroEconomica ) );
    $obFormulario->addSpan ( $obSpnReferencia );

    $obFormulario->addSpan ( $obSpnModoParcelamento );
    $obFormulario->addSpan ( $obSpnParcelas );

$obFormulario->addComponente ( $obBscProcesso );
$obFormulario->addComponente ( $obTxtObservacao );
$obFormulario->addComponente ( $obTxtObservacaoInterna );
$obFormulario->agrupaComponentes( array( $obRdoCarneNao, $obRdoCarneSim ) );
$obFormulario->addSpan ( $spnCarne );

$obBtnOK = new Ok;
$onBtnLimpar = new Limpar;
$onBtnLimpar->obEvento->setOnClick( "limpaLancamento()" );

$obFormulario->defineBarra( array( $obBtnOK , $onBtnLimpar ) );

$obFormulario->show();

echo "<script>
            jQuery(document).ready(function () {
                montaParametrosGET('montaReferenciaParcelamentoAjax','stReferencia');
            });
      </script>";
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
