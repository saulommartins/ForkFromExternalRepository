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
  * Página de Formulario para EXECUTAR CALCULOS  - MODULO ARRECADACAO
  * Data de criação : 01/06/2005

  * @author Analista: Fabio Bertold Rodrigues
  * @author Programador: Lucas Teixeira Stephanou

    * $Id: FMExecutarCalculoCredito.php 59612 2014-09-02 12:00:51Z gelson $

  Caso de uso: uc-05.03.05
**/

/*
$Log$
Revision 1.26  2007/04/16 18:06:22  cercato
Bug #9132#

Revision 1.25  2007/03/09 20:45:26  dibueno
Bug #8623#

Revision 1.24  2007/03/07 21:00:18  cassiano
Bug #8441#

Revision 1.23  2007/02/15 16:57:31  dibueno
Limpar variavel de sessao ao carregar formulario

Revision 1.22  2007/02/05 12:58:39  cercato
Bug #7341#

Revision 1.21  2006/09/15 11:50:26  fabio
corrigidas tags de caso de uso

Revision 1.20  2006/09/15 10:57:57  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_MON_NEGOCIO."RMONCredito.class.php" );
include_once ( CAM_GT_ARR_NEGOCIO."RARRConfiguracao.class.php" );

//Define o nome dos arquivos PHP
$stPrograma      = "ManterCalculos";
$pgFilt          = "FL".$stPrograma.".php";
$pgList          = "LS".$stPrograma.".php";
$pgForm          = "FM".$stPrograma.".php";
$pgProc          = "PRManterCalculo.php";
$pgOcul          = "OCManterCalculo.php";
$pgJs            = "JSManterCalculo.js";
include_once( $pgJs );

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

Sessao::write('parcelas', null );
Sessao::write('link', "" );

// instancia objeto
$obRMONCredito = new RMONCredito;
// pegar mascara de credito
$obRMONCredito->consultarMascaraCredito();
$stMascaraCredito = $obRMONCredito->getMascaraCredito();
$obRARRConfiguracao = new RARRConfiguracao;
$obRARRConfiguracao->setAnoExercicio ( Sessao::getExercicio() );
$obRARRConfiguracao->consultarMascaraProcesso( $stMascaraProcesso );

// OBJETOS HIDDEN
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( $_REQUEST["stCtrl"]  );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao  );

$obHdnCodModulo = new Hidden;
$obHdnCodModulo->setName  ( "inCodModulo" );
$obHdnCodModulo->setValue ( $_REQUEST["inCodModulo"] );

$obBscCredito = new BuscaInner;
$obBscCredito->setRotulo    ( "Crédito"         );
$obBscCredito->setTitle     ( "Crédito que será calculado."   );
$obBscCredito->setId        ( "stCredito"       );
$obBscCredito->setNull      ( false             );
$obBscCredito->obCampoCod->setName      ("inCodCredito"             );
$obBscCredito->obCampoCod->setId        ("inCodCredito"             );
$obBscCredito->obCampoCod->setValue     ( $_REQUEST["inCodCredito"] );
$obBscCredito->obCampoCod->setMaxLength ( strlen($stMascaraCredito) );
$obBscCredito->obCampoCod->setMinLength ( strlen($stMascaraCredito) );
$obBscCredito->obCampoCod->setMascara   ($stMascaraCredito          );
$obBscCredito->obCampoCod->obEvento->setOnChange("buscaValor('BuscaDoCredito');");
$obBscCredito->obCampoCod->obEvento->setOnBlur("validarCredito(this);");
$obBscCredito->setFuncaoBusca( "abrePopUp('".CAM_GT_MON_POPUPS."credito/FLProcurarCredito.php','frm','inCodCredito','stCredito','todos','".Sessao::getId()."','800','550');" );

$obRdbParcial = new Radio;
$obRdbParcial->setRotulo   ( "Tipo de Cálculo" );
$obRdbParcial->setTitle    ( "Tipo de cálculo a ser efetuado." );
$obRdbParcial->setName     ( "stTipoCalculo"   );
$obRdbParcial->setId       ( "stTipoCalculo"   );
$obRdbParcial->setLabel    ( "Parcial"         );
$obRdbParcial->setValue    ( "parcial"         );
$obRdbParcial->setNull     ( false             );
$obRdbParcial->obEvento->setOnChange	( "buscaValor('mudaTipoCalculoCredito')" );
#$obRdbParcial->obEvento->setOnChange    ( "buscaValor('montaParcelamento')" );

$obRdbIndividual = new Radio;
$obRdbIndividual->setRotulo   ( "Tipo de Cálculo" );
$obRdbIndividual->setName     ( "stTipoCalculo"   );
$obRdbIndividual->setId       ( "stTipoCalculo"   );
$obRdbIndividual->setLabel    ( "Individual"      );
$obRdbIndividual->setValue    ( "individual"      );
$obRdbIndividual->setNull     ( false             );
$obRdbIndividual->obEvento->setOnChange    ( "buscaValor('mudaTipoCalculoCredito')" );
#$obRdbIndividual->obEvento->setOnChange	( "buscaValor('montaParcelamento')" );

$obRdbCGM = new Radio;
$obRdbCGM->setTitle    ( "Filtro a ser utilizado para o cálculo." );
$obRdbCGM->setRotulo   ( "Filtrar por"  );
$obRdbCGM->setName     ( "stFiltraPor"  );
$obRdbCGM->setId       ( "stFiltraPor1" );
$obRdbCGM->setLabel    ( "CGM"          );
$obRdbCGM->setValue    ( "cgm"          );
$obRdbCGM->setNull     ( false          );
$obRdbCGM->obEvento->setOnChange( "buscaValor('mudaFiltraPor')" );

$obRdbInscricaoImobialiaria = new Radio;
$obRdbInscricaoImobialiaria->setRotulo   ( "Filtrar por"           );
$obRdbInscricaoImobialiaria->setName     ( "stFiltraPor"           );
$obRdbInscricaoImobialiaria->setId       ( "stFiltraPor2"          );
$obRdbInscricaoImobialiaria->setLabel    ( "Inscrição Imobiliária" );
$obRdbInscricaoImobialiaria->setValue    ( "imobiliaria"           );
$obRdbInscricaoImobialiaria->setNull     ( false                   );
$obRdbInscricaoImobialiaria->obEvento->setOnChange( "buscaValor('mudaFiltraPor')" );

$obRdbInscricaoEconomica = new Radio;
$obRdbInscricaoEconomica->setRotulo   ( "Filtrar por"         );
$obRdbInscricaoEconomica->setName     ( "stFiltraPor"         );
$obRdbInscricaoEconomica->setId       ( "stFiltraPor3"        );
$obRdbInscricaoEconomica->setLabel    ( "Inscrição Econômica" );
$obRdbInscricaoEconomica->setValue    ( "economica"           );
$obRdbInscricaoEconomica->setNull     ( false                 );
$obRdbInscricaoEconomica->obEvento->setOnChange( "buscaValor('mudaFiltraPor')" );

$obRdbNao = new Radio;
$obRdbNao->setTitle    ( "Informe se deverão ser feitos lançamentos após a execução do cálculo." );
$obRdbNao->setRotulo   ( "Efetuar Lançamentos" );
$obRdbNao->setName     ( "efetuar_lancamentos_radio" );
$obRdbNao->setId       ( "efetuar_lancamentos_radio" );
$obRdbNao->setLabel    ( "Não"                 );
$obRdbNao->setValue    ( "nao"                 );
$obRdbNao->setNull     ( false                 );
$obRdbNao->setDisabled ( true                  );

$obHdnEfetuarLancamento = new Hidden;
$obHdnEfetuarLancamento->setName  ('efetuar_lancamentos');
$obHdnEfetuarLancamento->setValue ( 'sim' );

$obRdbSim = new Radio;
$obRdbSim->setRotulo   ( "Efetuar Lançamentos" );
$obRdbSim->setName     ( "efetuar_lancamentos_radio" );
$obRdbSim->setId       ( "efetuar_lancamentos_radio" );
$obRdbSim->setLabel    ( "Sim"                 );
$obRdbSim->setValue    ( "sim"                 );
$obRdbSim->setNull     ( false                 );
$obRdbSim->setChecked  ( true                  );
$obRdbSim->setDisabled ( true                  );

$obRdbEmissaoNaoEmitir = new Radio;
$obRdbEmissaoNaoEmitir->setRotulo   ( "Emissão de Carnês"                            );
$obRdbEmissaoNaoEmitir->setTitle    ( "Informe se deverá ou não ser emitido carnê de cobrança." );
$obRdbEmissaoNaoEmitir->setName     ( "emissao_carnes"                               );
$obRdbEmissaoNaoEmitir->setId       ( "emissao_carnes"                               );
$obRdbEmissaoNaoEmitir->setLabel    ( "Não Emitir"                                   );
$obRdbEmissaoNaoEmitir->setValue    ( "nao_emitir"                                   );
$obRdbEmissaoNaoEmitir->setNull     ( false                                          );
$obRdbEmissaoNaoEmitir->setChecked  ( true                                           );
$obRdbEmissaoNaoEmitir->obEvento->setOnChange( "montaModeloCarne();"  );

$obRdbEmissaoLocal = new Radio;
$obRdbEmissaoLocal->setRotulo   ( "Emissão de Carnês"                            );
$obRdbEmissaoLocal->setName     ( "emissao_carnes"                               );
$obRdbEmissaoLocal->setId       ( "emissao_carnes"                               );
$obRdbEmissaoLocal->setLabel    ( "Impressão Local"                              );
$obRdbEmissaoLocal->setValue    ( "local"                                         );
$obRdbEmissaoLocal->setNull     ( false                                          );
$obRdbEmissaoLocal->setChecked  ( false                                          );
$obRdbEmissaoLocal->obEvento->setOnChange( "montaModeloCarne();"  );

$obRdbEmissaoGrafica = new Radio;
$obRdbEmissaoGrafica->setRotulo   ( "Emissão de Carnês"                            );
$obRdbEmissaoGrafica->setName     ( "emissao_carnes"                               );
$obRdbEmissaoGrafica->setId       ( "emissao_carnes"                               );
$obRdbEmissaoGrafica->setLabel    ( "Gráfica"                                      );
$obRdbEmissaoGrafica->setValue    ( "grafica"                                      );
$obRdbEmissaoGrafica->setNull     ( false                                          );
$obRdbEmissaoGrafica->setChecked  ( false                                          );
$obRdbEmissaoGrafica->obEvento->setOnChange( "montaModeloCarne();"  );

$obBscProcesso = new BuscaInner;
$obBscProcesso->setRotulo ( "Processo" );
//$obBscProcesso->setTitle ( "Número do processo no protocolo que gerou a aprovação da baixa" );
$obBscProcesso->setNull ( true );
$obBscProcesso->setTitle ("Processo referente ao cálculo.");
$obBscProcesso->obCampoCod->setName ("inProcesso");
$obBscProcesso->obCampoCod->setId   ("inProcesso");
$obBscProcesso->obCampoCod->setValue( $_REQUEST["inProcesso"] );
$obBscProcesso->obCampoCod->obEvento->setOnChange( "buscaValor('buscaProcesso','');" );
$obBscProcesso->obCampoCod->setSize ( strlen($stMascaraProcesso) );
$obBscProcesso->obCampoCod->setMaxLength( strlen($stMascaraProcesso) );
$obBscProcesso->obCampoCod->obEvento->setOnKeyUp ("mascaraDinamico('".$stMascaraProcesso."', this, event);");
$obBscProcesso->setFuncaoBusca( "abrePopUp('".CAM_GA_PROT_POPUPS."processo/FLBuscaProcessos.php','frm','inProcesso','campoInner2','','".Sessao::getId()."','800','550')" );

$obTxtObservacao = new TextArea;
$obTxtObservacao->setTitle ("Observações referentes ao cálculo disponíveis para o contribuinte.");
$obTxtObservacao->setName ( "stObservacao" );
$obTxtObservacao->setRotulo ( "Observações p/ Boleto" );
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

// span para filtros
$obSpnFiltros = new Span;
$obSpnFiltros->setId   ( "spnFiltros" );
$obSpnFiltros->setValue( "&nbsp;"     );

$obSpnModelo = new Span;
$obSpnModelo->setId( "spnModelo");
$obSpnModelo->setValue( "");

$obSpnModoParcelamento = new Span;
$obSpnModoParcelamento->setId( "spnModoParcelamento");
$obSpnModoParcelamento->setValue( "");

$obSpnParcelas = new Span;
$obSpnParcelas->setId( "spnParcelas");
$obSpnParcelas->setValue( "");

$obHdnNumeroDomicilio = new Hidden;
$obHdnNumeroDomicilio->setName  ( "stNumeroDomicilio" );
$obHdnNumeroDomicilio->setId ( "stNumeroDomicilio" );

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction( $pgProc  );
$obForm->setTarget( "oculto" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm                  );
$obFormulario->addHidden     ( $obHdnCtrl               );
$obFormulario->addHidden     ( $obHdnAcao               );
$obFormulario->addHidden     ( $obHdnCodModulo          );
$obFormulario->addHidden	 ( $obHdnEfetuarLancamento );
$obFormulario->addHidden     ( $obHdnNumeroDomicilio );

$obFormulario->addTitulo     ( "Dados para Cálculo"     );
$obFormulario->addComponente ( $obBscCredito            );
$obFormulario->addComponente ( $obTxtExercicio          );

$obFormulario->agrupaComponentes( array( $obRdbParcial, $obRdbIndividual) );
$obFormulario->agrupaComponentes( array( $obRdbCGM, $obRdbInscricaoImobialiaria, $obRdbInscricaoEconomica) );
$obFormulario->addSpan       ( $obSpnFiltros  );
$obFormulario->addSpan       ( $obSpnModoParcelamento );
$obFormulario->addSpan       ( $obSpnParcelas );
$obFormulario->addComponente ( $obBscProcesso );
$obFormulario->addComponente ( $obTxtObservacao );
$obFormulario->addComponente ( $obTxtObservacaoInterna );
$obFormulario->agrupaComponentes( array( $obRdbNao, $obRdbSim) );
$obFormulario->agrupaComponentes( array( $obRdbEmissaoNaoEmitir, $obRdbEmissaoLocal, $obRdbEmissaoGrafica) );
$obFormulario->addSpan       ( $obSpnModelo            );

$obFormulario->Ok();
$obFormulario->setFormFocus($obBscCredito->obCampoCod->getId());
$obFormulario->show();

SistemaLegado::executaFrameOculto("buscaValor('emissao');");
?>
