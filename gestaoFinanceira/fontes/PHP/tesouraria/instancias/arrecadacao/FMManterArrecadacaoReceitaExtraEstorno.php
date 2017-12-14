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
    * Página de Formulário para Filtro de Estorno de Arrecadação Extra Orçamentárias
    * Data de Criação   : 14/09/2006
    *
    * @author Analista: Cleisson Barboza
    * @author Desenvolvedor: Anderson C. Konze

    * @ignore

    $Id: FMManterArrecadacaoReceitaExtraEstorno.php 59612 2014-09-02 12:00:51Z gelson $

    $Revision: 30668 $
    $Name$
    $Author: cako $
    $Date: 2007-12-05 15:12:56 -0200 (Qua, 05 Dez 2007) $

    * Casos de uso: uc-02.04.26

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GF_TES_NEGOCIO . "RTesourariaArrecadacao.class.php" );
include_once( CAM_GF_TES_NEGOCIO . "RTesourariaBoletim.class.php" );
include_once( CLA_IAPPLETTERMINAL );
include_once CAM_GF_TES_COMPONENTES . 'ISaldoCaixa.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterArrecadacaoReceitaExtra";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OCManterArrecadacaoReceitaExtraEstorno.php";
$pgJs   = "JS".$stPrograma.".js";

$jsOnload = "";

$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( "alterar" );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( $request->get('stCtrl') );

$obHdnDtBoletimArrecadacao = new Hidden;
$obHdnDtBoletimArrecadacao->setName ( "dtBoletimArrecadacao" );
$obHdnDtBoletimArrecadacao->setValue(  $_GET['dtBoletimArrecadacao'] );

$stHdnValor = "
    var stValorEstorno;
    stValorEstorno = document.frm.nuValorEstorno.value;
    while (stValorEstorno.indexOf('.')>0) {
        stValorEstorno = stValorEstorno.replace('.','');
    }
    stValorEstorno = stValorEstorno.replace(',','.');
    if (stValorEstorno <= 0) {
         erro = true;
         mensagem += '@O valor a ser estornado deve ser maior que 0,00!';
    }

    var stValorMaxEstorno = document.frm.nuValorMaxEstorno.value;
    if ( parseFloat(stValorEstorno) > parseFloat(stValorMaxEstorno) ) {
        erro = true;
        mensagem = '@O valor do estorno é maior que o valor a estornar!';
    }
    ";

$obHdnEval = new HiddenEval;
$obHdnEval->setName( "stEval" );
$obHdnEval->setValue( $stHdnValor );

////////////////////////////////

$obLblRecibo = new Label();
$obLblRecibo->setRotulo      ( 'Número Recibo');
$obLblRecibo->setValue       ( $_GET['inCodRecibo'] );

$obArrecadacao = new RTesourariaTransferencia ( new RTesourariaBoletim );
$obArrecadacao->obRContabilidadeLancamentoValorEstornada->obRContabilidadeLancamento->obRContabilidadeLote->setCodLote( $_REQUEST['inCodLote'] );
$obArrecadacao->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST['inCodEntidade'] );
$obArrecadacao->roRTesourariaBoletim->setExercicio( Sessao::getExercicio() );
$obArrecadacao->obRContabilidadeLancamentoValorEstornada->obRContabilidadeLancamento->obRContabilidadeLote->setTipo( $_REQUEST['stTipo'] );
$obArrecadacao->getBoletimTransferencia( $rsBoletim );

$obLblBoletim = new Label();
$obLblBoletim->setRotulo( "*Número do Boletim" );
$obLblBoletim->setId    ( "LblCodBoletimAberto" );
$obLblBoletim->setValue ( $rsBoletim->getCampo('cod_boletim') );

$obLblDtBoletim = new Label();
$obLblDtBoletim->setRotulo( "*Data do Boletim" );
$obLblDtBoletim->setId    ( "LblDtBoletimAberto" );
$obLblDtBoletim->setValue ( $rsBoletim->getCampo('dt_boletim'));

require_once( CAM_GF_TES_COMPONENTES . 'ISelectBoletim.class.php' );
$obISelectBoletim = new ISelectBoletim;
$obISelectBoletim->obBoletim->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST['inCodEntidade']  );
$obISelectBoletim->obBoletim->setExercicio( Sessao::getExercicio() );
$obISelectBoletim->setRotulo ('Boletim do Estorno');

$obLblEntidade = new Label();
$obLblEntidade->setRotulo ('Entidade');
$obLblEntidade->setValue ( $_GET['inCodEntidade']." - ".$_GET['stNomEntidade'] );

$obHdnEntidade = new Hidden;
$obHdnEntidade->setName ("inCodEntidade" );
$obHdnEntidade->setId ( 'inCodEntidade' );
$obHdnEntidade->setValue ( $_GET['inCodEntidade'] );

$obLblCredor = new Label();
$obLblCredor->setRotulo ('Credor');
if($_GET['inCodCredor'])
    $obLblCredor->setValue ( $_GET['inCodCredor']." - ".$_GET['stNomCredor'] );

$obLblRecurso = new Label();
$obLblRecurso->setRotulo ('Recurso');
if($_GET['stMascRecurso'])
    $obLblRecurso->setValue ( $_GET['stMascRecurso']." - ".$_GET['stNomRecurso'] );

$obLblContaCredito = new Label();
$obLblContaCredito->setRotulo ('Conta de Receita');
$obLblContaCredito->setValue ($_GET['inCodPlanoDebito']." - ".$_GET['stNomContaDebito'] );

$obHdnContaCredito = new Hidden;
$obHdnContaCredito->setName ('inCodPlanoCredito' );
$obHdnContaCredito->setValue ( $_GET['inCodPlanoCredito'] );

$obHdnNomContaCredito = new Hidden;
$obHdnNomContaCredito->setName ('stNomContaCredito' );
$obHdnNomContaCredito->setValue ( $_GET['stNomContaCredito'] );

$obHdnContaDebito = new Hidden;
$obHdnContaDebito->setName ('inCodPlanoDebito' );
$obHdnContaDebito->setValue ( $_GET['inCodPlanoDebito'] );

$obHdnNomContaDebito = new Hidden;
$obHdnNomContaDebito->setName ('stNomContaDebito' );
$obHdnNomContaDebito->setValue ( $_GET['stNomContaDebito'] );

$obLblContaDebito = new Label();
$obLblContaDebito->setRotulo ('Conta Caixa/Banco');
$obLblContaDebito->setValue ($_GET['inCodPlanoCredito']." - ".$_GET['stNomContaCredito'] );

// Define Objeto para busca do histórico
$obBscHistorico = new BuscaInner();
$obBscHistorico->setRotulo                 ( "Histórico Padrão"           );
$obBscHistorico->setTitle                  ( "Informe o histórico padrão.");
$obBscHistorico->setId                     ( "stNomHistorico"             );
//$obBscHistorico->setValue                  ( $stNomHistorico              );
$obBscHistorico->setNull                   ( false                        );
$obBscHistorico->obCampoCod->setName       ( "inCodHistorico"             );
$obBscHistorico->obCampoCod->setId         ( "inCodHistorico"             );
$obBscHistorico->obCampoCod->setSize       ( 10                           );
$obBscHistorico->obCampoCod->setMaxLength  ( 5                            );
//$obBscHistorico->obCampoCod->setValue      ( $inCodHistorico              );
$obBscHistorico->obCampoCod->setAlign      ( "left"                       );
$obBscHistorico->obImagem->setId           ( "imgHistorico"               );
$obBscHistorico->setFuncaoBusca            ("abrePopUp('".CAM_GF_CONT_POPUPS."historicoPadrao/FLHistoricoPadrao.php','frm','inCodHistorico','stNomHistorico','','".Sessao::getId()."','800','550');");
$obBscHistorico->setValoresBusca           ( CAM_GF_CONT_POPUPS.'historicoPadrao/OCHistoricoPadrao.php?'.Sessao::getId(), $obForm->getName() );

$obHdnLote = new Hidden;
$obHdnLote->setName ('inCodLote');
$obHdnLote->setValue ( $_GET['inCodLote'] );

$obLblValorArrecadado = new Label;
$obLblValorArrecadado->setRotulo ('Valor arrecadado');
$obLblValorArrecadado->setId ( 'lblValorArrecadado' );
$obLblValorArrecadado->setValue ( $_GET['nuValorArrecadado'] );

$obLblValorEstornado = new Label;
$obLblValorEstornado->setRotulo ('Valor estornado');
$obLblValorEstornado->setId ('lblValorEstornado' );

// Define Obeto Numerico para valor do estorno
$obTxtValor = new Numerico();
$obTxtValor->setRotulo   ("*Valor"                    );
$obTxtValor->setTitle    ("Informe o valor a estornar");
$obTxtValor->setName     ("nuValorEstorno"            );
$obTxtValor->setId       ("nuValorEstorno"            );
$obTxtValor->setNull     (false                       );
$obTxtValor->setDecimais (2                           );
$obTxtValor->setNegativo (false                       );
$obTxtValor->setNull     (true                        );
$obTxtValor->setSize     (17                          );
$obTxtValor->setMaxLength(17                          );
$obTxtValor->setMinValue (0.01                        );

$obHdnValorMaxEstorno = new Hidden;
$obHdnValorMaxEstorno->setName('nuValorMaxEstorno');
$obHdnValorMaxEstorno->setId(  'nuValorMaxEstorno');

$obHdnValorEstornado = new Hidden;
$obHdnValorEstornado->setName('nuValorEstornado');
$obHdnValorEstornado->setId(  'nuValorEstornado');

$obHdnValorArrecadado = new Hidden;
$obHdnValorArrecadado->setName('nuValorArrecadado');
$obHdnValorArrecadado->setId(  'nuValorArrecadado');

// Define Objeto TextArea para observações
$obTxtObs = new TextArea;
$obTxtObs->setName   ( "stObservacoes" );
$obTxtObs->setId     ( "stObservacoes" );
$obTxtObs->setRotulo ( "Observações"   );
$obTxtObs->setTitle  ( "Informe as observações do estorno de arrecadação." );
$obTxtObs->setNull   ( true            );
$obTxtObs->setRows   ( 2               );
$obTxtObs->setCols   ( 100             );
$obTxtObs->setMaxCaracteres    ( 170 );

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );

$obIAppletTerminal = new IAppletTerminal( $obForm );

$obFormulario->addTitulo    ( "Dados para Estorno de Pagamentos Extras"     );
$obFormulario->addHidden    ( $obHdnAcao                    );
$obFormulario->addHidden    ( $obHdnCtrl                    );
$obFormulario->addHidden    ( $obIAppletTerminal            );
$obFormulario->addHidden    ( $obHdnEval, true              );

$obFormulario->addHidden    ( $obHdnEntidade                );
$obFormulario->addHidden    ( $obHdnContaDebito             );
$obFormulario->addHidden    ( $obHdnContaCredito            );
$obFormulario->addHidden    ( $obHdnLote                    );
$obFormulario->addHidden    ( $obHdnValorMaxEstorno         );
$obFormulario->addHidden    ( $obHdnValorArrecadado         );
$obFormulario->addHidden    ( $obHdnValorEstornado          );
$obFormulario->addHidden    ( $obHdnNomContaDebito          );
$obFormulario->addHidden    ( $obHdnNomContaCredito         );
$obFormulario->addHidden    ( $obHdnDtBoletimArrecadacao    );

$obFormulario->addComponente( $obLblRecibo                  );
$obFormulario->addComponente( $obLblBoletim                 );
$obFormulario->addComponente( $obLblDtBoletim               );
$obFormulario->addComponente ( $obISelectBoletim     );
$obFormulario->addComponente( $obLblEntidade                );
$obFormulario->addComponente( $obLblCredor                  );
$obFormulario->addComponente( $obLblRecurso                 );
$obFormulario->addComponente( $obLblContaCredito            );
$obFormulario->addComponente( $obLblContaDebito             );
$obFormulario->addComponente( $obBscHistorico               );
$obFormulario->addComponente( $obLblValorArrecadado         );
$obFormulario->addComponente( $obLblValorEstornado          );
$obFormulario->addComponente( $obTxtValor                   );
$obFormulario->addComponente( $obTxtObs                     );

$obOk  = new Ok;
$obOk->setId ("Ok");

$stLocation = $pgList.'?'.Sessao::getId();
$obFormulario->Cancelar( $stLocation );

$obFormulario->show();

SistemaLegado::executaFrameOculto(" ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&inCodLote=".$_GET['inCodLote']."&inCodEntidade=".$_GET['inCodEntidade']."&stTipo=".$_GET['stTipo']."&nuValorArrecadado=".$_GET['nuValorArrecadado']."','verificaValorEstornado');");
$ISaldoCaixa = new ISaldoCaixa();
$ISaldoCaixa->inCodEntidade = $_REQUEST['inCodEntidade'];
$jsOnload .= $ISaldoCaixa->montaSaldo();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
