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
    * Página de Formulário para Definir Vencimentos
    * Data de Criação: 20/05/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Tonismar R. Bernardo

    * @ignore

    * $Id: FMManterVencimentos.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.03.03
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_ARR_NEGOCIO."RARRCalendarioFiscal.class.php");
include_once ( CAM_GT_ARR_NEGOCIO."RARRGrupoVencimento.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterVencimentos";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once ( $pgJS );

Sessao::write( 'vencimentos', array() );
Sessao::write( 'parcelamentos', array() );

//DEFINICAO DOS COMPONENTES DO FORMULARIO
$stCtrl = $_REQUEST["stCtrl"];

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( $stCtrl  );

$stAcao = $request->get('stAcao');
$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao  );

$obHdnInLinha = new Hidden;
$obHdnInLinha->setName  ( "inLinha" );
$obHdnInLinha->setValue ( $_REQUEST["inLinha"]  );

$obHdnInLinhaPar = new Hidden;
$obHdnInLinhaPar->setName  ( "inLinhaPar" );
$obHdnInLinhaPar->setValue ( $_REQUEST["inLinhaPar"]  );

$obHdnCodigoGrupo = new Hidden;
$obHdnCodigoGrupo->setName  ( "inCodigoGrupo" );
$obHdnCodigoGrupo->setValue ( $_REQUEST['inCodigoGrupo']  );

$obHdnDescricaoCredito = new Hidden;
$obHdnDescricaoCredito->setName  ( "stDescricaoCredito" );
$obHdnDescricaoCredito->setValue ( $_REQUEST['stDescricaoCredito'] );

$obHdnExercicio = new Hidden;
$obHdnExercicio->setName  ( "stExercicio" );
$obHdnExercicio->setValue ( $_REQUEST['stExercicio'] );

$obHdnDescricaoVencimento = new Hidden;
$obHdnDescricaoVencimento->setName  ( "stDescricaoVencimento" );
$obHdnDescricaoVencimento->setValue ( $_REQUEST['stDescricaoVencimento'] );

$obHdnValorIntegral = new Hidden;
$obHdnValorIntegral->setName  ( "dtValorIntegral" );
$obHdnValorIntegral->setValue ( $_REQUEST['dtValorIntegral'] );

$obHdnCodigoVencimento = new Hidden;
$obHdnCodigoVencimento->setName  ( "inCodigoVencimento" );
$obHdnCodigoVencimento->setValue ( $_REQUEST['inCodigoVencimento'] );

$obHdnFlagEditar = new Hidden;
$obHdnFlagEditar->setName  ( "flagEditar" );
$obHdnFlagEditar->setValue ( 'incluir'    );

$obLblDescricaoCredito = new Label;
$obLblDescricaoCredito->setTitle ( "Grupo de créditos para o qual o calendário foi definido." );
$obLblDescricaoCredito->setRotulo( "Grupo de Crédito" );
$obLblDescricaoCredito->setValue( $_REQUEST['inCodigoGrupoLst']."/".$_REQUEST['stExercicio']." - ".$_REQUEST['stDescricaoCredito'] );
$obLblDescricaoCredito->setId( 'stDescricaoCredito' );

$obLblDescricaoVencimento = new Label;
$obLblDescricaoVencimento->setRotulo( "Grupo de Vencimentos" );
$obLblDescricaoVencimento->setValue( $_REQUEST['stDescricaoVencimento'] );
$obLblDescricaoVencimento->setId( 'stDescricaoVencimento' );

$obLblValorIntegral = new Label;
$obLblValorIntegral->setTitle ( "Data limite para o recebimento do valor integral sem acréscimos." );
$obLblValorIntegral->setRotulo( "Vencimento Valor Integral" );
$obLblValorIntegral->setValue ($_REQUEST['dtValorIntegral'] );
$obLblValorIntegral->setId( 'dtValorIntegral' );

$obLblLimiteInicial = new Label;
$obLblLimiteInicial->setTitle ( "Limite inicial para o lançamento utilizar o grupo de vencimento." );
$obLblLimiteInicial->setRotulo( "Limite Inicial" );
$obLblLimiteInicial->setValue ($_REQUEST['inLimiteInicial'] );
$obLblLimiteInicial->setId( 'inLimiteInicial' );

$obLblLimiteFinal = new Label;
$obLblLimiteFinal->setTitle ( "Limite final para o lançamento utilizar o grupo de vencimento." );
$obLblLimiteFinal->setRotulo( "Limite Final" );
$obLblLimiteFinal->setValue ($_REQUEST['inLimiteFinal'] );
$obLblLimiteFinal->setId( 'inLimiteFinal' );

//VENCIMENTOS E DESCONTOS
$obTxtVencimento = new Data;
$obTxtVencimento->setTitle          ( "Data do vencimento para o desconto estabelecido." );
$obTxtVencimento->setName          ( "dtVencimento"   );
$obTxtVencimento->setRotulo        ( "Vencimento"     );
$obTxtVencimento->setValue         ( $_REQUEST["dtVencimento"]    );

$obTxtDesconto = new Moeda;
$obTxtDesconto->setTitle           ( "Desconto a ser fornecido até a data do vencimento." );
$obTxtDesconto->setName            ( "flDesconto"     );
$obTxtDesconto->setRotulo          ( "Desconto"       );
$obTxtDesconto->setMaxLength       ( 7                );
$obTxtDesconto->setSize            ( 7                );
$obTxtDesconto->setValue           ( $_REQUEST["flDesconto"]     );

$obRdoPercentual = new Radio;
$obRdoPercentual->setTitle   ( "Forma para definir o desconto." );
$obRdoPercentual->setName    ( "stFormaDesconto"    );
$obRdoPercentual->setId      ( "stFormaDescontoPer" );
$obRdoPercentual->setRotulo  ( "Forma de Desconto"  );
$obRdoPercentual->setLabel   ( "Percentual"         );
$obRdoPercentual->setValue   ( "per"                );
$obRdoPercentual->setChecked ( true                 );

$obRdoAbsoluto = new Radio;
$obRdoAbsoluto->setName    ( "stFormaDesconto"     );
$obRdoAbsoluto->setId      ( "stFormaDescontoAbs"  );
$obRdoAbsoluto->setRotulo  ( "Forma de Desconto"   );
$obRdoAbsoluto->setLabel   ( "Valor absoluto"      );
$obRdoAbsoluto->setValue   ( "abs"                 );

$obBtnIncluirVencimento = new Button;
$obBtnIncluirVencimento->setName              ( "btnIncluirVencimento" );
$obBtnIncluirVencimento->setValue             ( "Definir"              );
$obBtnIncluirVencimento->setTipo              ( "button"               );
$obBtnIncluirVencimento->obEvento->setOnClick ( "incluirVencimento();" );
$obBtnIncluirVencimento->setDisabled          ( false                  );

$obBtnLimparVencimento = new Button;
$obBtnLimparVencimento->setName               ( "btnLimparVencimento"  );
$obBtnLimparVencimento->setValue              ( "Limpar"              );
$obBtnLimparVencimento->setTipo               ( "button"              );
$obBtnLimparVencimento->obEvento->setOnClick  ( "buscaValor('limparVencimento');" );
$obBtnLimparVencimento->setDisabled           ( false                 );

$obSpnVencimento = new Span;
$obSpnVencimento->setId ( "lsVencimento" );

//PARCELAMENTO
$obTxtVencimentoParc = new Data;
$obTxtVencimentoParc->setTitle         ( "Data do vencimento de uma parcela." );
$obTxtVencimentoParc->setName          ( "dtVencimentoParc"   );
$obTxtVencimentoParc->setRotulo        ( "Vencimento"         );
$obTxtVencimentoParc->setValue         ( $_REQUEST["dtVencimentoParc"]    );

$obTxtDescontoParc = new Moeda;
$obTxtDescontoParc->setTitle           ( "Desconto a ser fornecido até a data do vencimento da parcela." );
$obTxtDescontoParc->setName            ( "flDescontoParc"     );
$obTxtDescontoParc->setRotulo          ( "Desconto"           );
$obTxtDescontoParc->setMaxLength       ( 7                    );
$obTxtDescontoParc->setSize            ( 7                    );
$obTxtDescontoParc->setValue           ( $_REQUEST["flDescontoParc"]      );

$obTxtVencimentoDesc = new Data;
$obTxtVencimentoDesc->setTitle         ( "Data do vencimento do desconto de uma parcela." );
$obTxtVencimentoDesc->setName          ( "dtVencimentoDesc"    );
$obTxtVencimentoDesc->setRotulo        ( "Vencimento Desconto" );
$obTxtVencimentoDesc->setValue         ( $_REQUEST["dtVencimentoDesc"]     );

$obRdoPercentualParc = new Radio;
$obRdoPercentualParc->setTitle   ( "Forma para definir o desconto." );
$obRdoPercentualParc->setName    ( "stFormaParcelamento");
$obRdoPercentualParc->setId      ( "stFormaParcelamentoPer");
$obRdoPercentualParc->setRotulo  ( "Forma de Desconto"  );
$obRdoPercentualParc->setLabel   ( "Percentual"         );
$obRdoPercentualParc->setValue   ( "perparc"            );
$obRdoPercentualParc->setChecked ( true                 );

$obRdoAbsolutoParc = new Radio;
$obRdoAbsolutoParc->setName    ( "stFormaParcelamento" );
$obRdoAbsolutoParc->setId      ( "stFormaParcelamentoAbs" );
$obRdoAbsolutoParc->setRotulo  ( "Forma de Desconto"   );
$obRdoAbsolutoParc->setLabel   ( "Valor Absoluto"      );
$obRdoAbsolutoParc->setValue   ( "absparc"             );

$obTxtQtdParcela = new TextBox;
$obTxtQtdParcela->setName      ( "cmbQtdParcelas"   );
$obTxtQtdParcela->setValue     ( 1 );
$obTxtQtdParcela->setInteiro   ( true );
$obTxtQtdParcela->setTitle     ( "Quantidade de parcelas a serem geradas." );
$obTxtQtdParcela->setRotulo    ( "Quantidade de Parcelas" );
$obTxtQtdParcela->setNull      ( true );
$obTxtQtdParcela->obEvento->setOnKeyPress ("mascaraDinamico('9', this, event);");

$obBtnIncluirVencimentoParc = new Button;
$obBtnIncluirVencimentoParc->setName              ( "btnIncluirParcelamento" );
$obBtnIncluirVencimentoParc->setValue             ( "Definir"                  );
$obBtnIncluirVencimentoParc->setTipo              ( "button"                   );
$obBtnIncluirVencimentoParc->obEvento->setOnClick ( "incluirParcelamento();" );
$obBtnIncluirVencimentoParc->setDisabled          ( false                      );

$obBtnLimparVencimentoParc = new Button;
$obBtnLimparVencimentoParc->setName               ( "btnLimparVencimentoParc"  );
$obBtnLimparVencimentoParc->setValue              ( "Limpar"                   );
$obBtnLimparVencimentoParc->setTipo               ( "button"                   );
$obBtnLimparVencimentoParc->obEvento->setOnClick  ( "buscaValor('limparParcelamento');" );
$obBtnLimparVencimentoParc->setDisabled           ( false                      );

$obSpnParcelamento = new Span;
$obSpnParcelamento->setId ( "lsParcelamento" );

$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( 'oculto' );

$obRARRGrupoVencimento = new RARRGrupoVencimento( new RARRCalendarioFiscal );
$obRARRGrupoVencimento->roRARRCalendarioFiscal->setAnoExercicio( $_REQUEST['stExercicio'] );
$obRARRGrupoVencimento->roRARRCalendarioFiscal->setCodigoGrupo( $_REQUEST['inCodigoGrupo'] );
$obRARRGrupoVencimento->setCodigoVencimento( $_REQUEST['inCodigoVencimento'] );

$rsDescontos = new RecordSet;
$obRARRGrupoVencimento->listarDesconto( $rsDescontos );

$inCount = "";
$arVencs = array();
while ( !$rsDescontos->eof() ) {
    $arTmp['inLinha']       = ++$inCount;
    $arTmp['boPercentagem'] = $rsDescontos->getCampo( 'percentual' );
    if ( $rsDescontos->getCampo( 'percentual' ) == "t") {
        $arTmp['flDesconto']    = number_format($rsDescontos->getCampo( 'valor' ),2,',','')."%";
    } else {
        $arTmp['flDesconto']    = "R$".number_format($rsDescontos->getCampo( 'valor' ),2,',','');
    }
    $arTmp['dtVencimento']  = $rsDescontos->getCampo( 'data_vencimento' );
    $arVencs[] = $arTmp;
    $rsDescontos->proximo();
}

Sessao::write( 'vencimentos', $arVencs );
$rsParcelas = new RecordSet;
$obRARRGrupoVencimento->listarParcela( $rsParcelas );
unset($arTmp);
$inCount = "";
$arParcel = array();
while ( !$rsParcelas->eof() ) {
    $arTmp['inLinhaPar']       = ++$inCount;
    $arTmp['boPercentagem'] = $rsParcelas->getCampo( 'percentual' );
    if ( $rsParcelas->getCampo( 'percentual' ) == "t" ) {
        $arTmp['flDesconto']    = number_format($rsParcelas->getCampo( 'valor' ),2,',','')."%";
    } else {
        $arTmp['flDesconto']    = "R$".number_format($rsParcelas->getCampo( 'valor' ),2,',','');
    }
    $arTmp['dtVencimento']      = $rsParcelas->getCampo( 'data_vencimento'          );
    if ( $rsParcelas->getCampo( 'data_vencimento_desconto' ) == "" ) {
        $arTmp['dtVencimentoDesc']  = "-";
    } else {
        $arTmp['dtVencimentoDesc']  = $rsParcelas->getCampo( 'data_vencimento_desconto' );
    }

    $arParcel[] = $arTmp;
    $rsParcelas->proximo();
}

Sessao::write( 'parcelamentos', $arParcel );

if ($_REQUEST["boUtilizarUnica"] == 't') {
    sistemaLegado::executaFrameOculto("buscaValor('listaTodos');");
} else {
    sistemaLegado::executaFrameOculto("buscaValor('listaParcelamento');");
}

$obFormulario = new Formulario;
$obFormulario->addForm  ( $obForm                   );
$obFormulario->addHidden( $obHdnAcao                );
$obFormulario->addHidden( $obHdnInLinha             );
$obFormulario->addHidden( $obHdnInLinhaPar          );
$obFormulario->addHidden( $obHdnCtrl                );
$obFormulario->addHidden( $obHdnCodigoGrupo         );
$obFormulario->addHidden( $obHdnDescricaoCredito    );
$obFormulario->addHidden( $obHdnExercicio           );
$obFormulario->addHidden( $obHdnDescricaoVencimento );
$obFormulario->addHidden( $obHdnValorIntegral       );
$obFormulario->addHidden( $obHdnCodigoVencimento    );
$obFormulario->addHidden( $obHdnFlagEditar          );

$obFormulario->addTitulo( "Dados para Vencimentos" );
$obFormulario->addComponente( $obLblDescricaoCredito );
$obFormulario->addComponente( $obLblDescricaoVencimento );
$obFormulario->addComponente( $obLblValorIntegral );
$obFormulario->addComponente( $obLblLimiteInicial );
$obFormulario->addComponente( $obLblLimiteFinal );

if ($_REQUEST["boUtilizarUnica"] == 't') {
    $obFormulario->addTitulo( "Vencimentos e Descontos" );
    $obFormulario->addComponente( $obTxtVencimento );
    $obFormulario->addComponente( $obTxtDesconto   );
    $obFormulario->agrupaComponentes( array( $obRdoPercentual, $obRdoAbsoluto ) );
    $obFormulario->agrupaComponentes( array( $obBtnIncluirVencimento, $obBtnLimparVencimento ) );
    $obFormulario->addSpan( $obSpnVencimento );
}

$obFormulario->addTitulo( "Parcelamento" );
$obFormulario->addComponente( $obTxtVencimentoParc );
$obFormulario->addComponente( $obTxtDescontoParc   );
$obFormulario->addComponente( $obTxtVencimentoDesc );
$obFormulario->agrupaComponentes( array( $obRdoPercentualParc, $obRdoAbsolutoParc ) );
$obFormulario->addComponente( $obTxtQtdParcela );
$obFormulario->agrupaComponentes( array( $obBtnIncluirVencimentoParc, $obBtnLimparVencimentoParc ) );
$obFormulario->addSpan( $obSpnParcelamento );

$obFormulario->Cancelar();
$obFormulario->Show();
