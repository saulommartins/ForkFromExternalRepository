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
    * Página de Formulario de Definição de Calendário Fiscal
    * Data de Criação   : 18/05/2005

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @ignore

    * $Id: FMManterCalendario.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.03.03
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_ARR_NEGOCIO."RARRCalendarioFiscal.class.php");
include_once ( CAM_GT_ARR_NEGOCIO."RARRGrupoVencimento.class.php" );
include_once ( CAM_GT_ARR_NEGOCIO."RARRGrupo.class.php"           );
include_once '../../../../../../gestaoTributaria/fontes/PHP/arrecadacao/classes/componentes/MontaGrupoCredito.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterCalendario";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once ( $pgJS );

Sessao::write( 'grupos', array() );

$obMontaGrupoCredito = new MontaGrupoCredito;
$obRARRCalendarioFiscal = new RARRCalendarioFiscal;
$obRARRGrupoVencimento  = new RARRGrupoVencimento( $obRARRCalendarioFiscal );

if ($_REQUEST['stAcao'] == "alterar") {

    $obHdnCodigoGrupo = new Hidden;
    $obHdnCodigoGrupo->setName  ( 'inCodigoCredito' );
    $obHdnCodigoGrupo->setValue ( $_REQUEST['inCodigoCredito']  );

    $obHdnDescricao = new Hidden;
    $obHdnDescricao->setName  ( 'stDescricaoCredito' );
    $obHdnDescricao->setValue ( $_REQUEST['stDescricaoCredito']  );

    $obHdnExercicio = new Hidden;
    $obHdnExercicio->setName  ( 'stExercicio' );
    $obHdnExercicio->setValue ( $_REQUEST['stExercicio'] );

    $stBuffer = $_REQUEST['inCodigoCreditoLst']." - ".$_REQUEST['stDescricaoCredito']."/".$_REQUEST['stExercicio'];

    $obLblGrupo = new Label;
    $obLblGrupo->setTitle ( "Grupo de créditos para o qual o calendário foi definido." );
    $obLblGrupo->setName  ( 'stGrupo' );
    $obLblGrupo->setValue ( $stBuffer );
    $obLblGrupo->setRotulo( 'Grupo'   );

    $obRARRCalendarioFiscal->setCodigoGrupo( $_REQUEST['inCodigoCredito'] );
    $obRARRCalendarioFiscal->setAnoExercicio( $_REQUEST['stExercicio'] );
    $obRARRCalendarioFiscal->consultarCalendario();

    $inMinLancamento = str_replace(".",",",$obRARRCalendarioFiscal->getValorMinimo());
    $inMinParcela    = str_replace(".",",",$obRARRCalendarioFiscal->getValorMinimoParcela());
    $inMinIntegral   = str_replace(".",",",$obRARRCalendarioFiscal->getValorMinimoIntegral());

    $rsVencimentos = new RecordSet;
    $obRARRGrupoVencimento->listarGrupoVencimento( $rsVencimentos );
    $inCount = 1;
    $arDados = array();
    while ( !$rsVencimentos->eof() ) {
        $arTmp['inLinha'] = $inCount;
        $arTmp['stDescricao'] = $rsVencimentos->getCampo( 'descricao' );
        $arTmp['dtDataVencimento'] = $rsVencimentos->getCampo( 'data_vencimento_parcela_unica' );
        $arTmp['inCodigo'] = $rsVencimentos->getCampo( 'cod_vencimento' );
        $arTmp['inLimiteInicial'] = $rsVencimentos->getCampo( 'limite_inicial' );
        $arTmp['inLimiteFinal'] = $rsVencimentos->getCampo( 'limite_final' );

        if ( $rsVencimentos->getCampo( 'utilizar_unica' ) == 't' )
            $stCota = "Sim";
        else
            $stCota = "Não";

        $arTmp['stUtilizarCotaUnica'] = $stCota;
        $arDados[] = $arTmp;
        $rsVencimentos->proximo();
        $inCount++;
    }

    Sessao::write( 'grupos', $arDados );
    sistemaLegado::executaFrameOculto("buscaValor('listaGrupo')");

    //DEFINICAO DOS ATRIBUTOS
    $arChaveAtributoCalendario = array( "cod_grupo" => $_REQUEST["inCodigoCredito"] );

    $obRARRCalendarioFiscal->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoCalendario );
    $obRARRCalendarioFiscal->obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributos );

}

// busca os grupos de crédito para o select
$rsGrupoCredito = new RecordSet;
$obRARRCalendarioFiscal->listarGrupoCredito( $rsGrupoCredito );

//DEFINICAO DOS COMPONENTES DO FORMULARIO
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( $_REQUEST["stCtrl"]  );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $_REQUEST["stAcao"]  );

$obTxtCodigoCredito = new TextBox;
$obTxtCodigoCredito->setTitle              ( "Grupo de créditos para o qual o calendário será definido." );
$obTxtCodigoCredito->setName               ( "inCodigoCredito"   );
$obTxtCodigoCredito->setRotulo             ( "Grupo de Créditos" );
$obTxtCodigoCredito->setMaxLength          ( 7                   );
$obTxtCodigoCredito->setSize               ( 7                   );
$obTxtCodigoCredito->setValue              ( $_REQUEST["inCodigoCredito"] );
$obTxtCodigoCredito->setInteiro            ( true                );
$obTxtCodigoCredito->setNull               ( false               );

$obCmbGrupoCredito = new Select;
$obCmbGrupoCredito->setName               ( "stGrupoCredito"              );
$obCmbGrupoCredito->setRotulo             ( "Grupo de Créditos"           );
$obCmbGrupoCredito->setNull               ( false                         );
$obCmbGrupoCredito->setCampoId            ( "cod_grupo"                   );
$obCmbGrupoCredito->setCampoDesc          ( "[descricao]/[ano_exercicio]" );
$obCmbGrupoCredito->addOption             ( "", "Selecione"               );
$obCmbGrupoCredito->preencheCombo         ( $rsGrupoCredito               );

$obTxtMinLancamento = new Moeda;
$obTxtMinLancamento->setTitle              ( "Valor mínimo para efetuar um lançamento." );
$obTxtMinLancamento->setName               ( "inMinLancamento"   );
$obTxtMinLancamento->setRotulo             ( "Mínimo para Emissão" );
$obTxtMinLancamento->setMaxLength          ( 7                   );
$obTxtMinLancamento->setSize               ( 7                   );
$obTxtMinLancamento->setValue              ( $inMinLancamento    );
$obTxtMinLancamento->setNull               ( false               );

$obTxtMinIntegral = new Moeda;
$obTxtMinIntegral->setName               ( "inMinIntegral"   );
$obTxtMinIntegral->setTitle              ( "Valor mínimo exigido para pagamento integral." );
$obTxtMinIntegral->setRotulo             ( "Mínimo para Integral" );
$obTxtMinIntegral->setMaxLength          ( 7                   );
$obTxtMinIntegral->setSize               ( 7                   );
$obTxtMinIntegral->setValue              ( $inMinIntegral      );
$obTxtMinIntegral->setNull               ( false               );

$obTxtMinParcela = new Moeda;
$obTxtMinParcela->setName               ( "inMinParcela"   );
$obTxtMinParcela->setTitle              ( "Valor mínimo exigido para pagamento de uma parcela." );
$obTxtMinParcela->setRotulo             ( "Mínimo para Parcela" );
$obTxtMinParcela->setMaxLength          ( 7                   );
$obTxtMinParcela->setSize               ( 7                   );
$obTxtMinParcela->setValue              ( $inMinParcela       );
$obTxtMinParcela->setNull               ( false               );

$obTxtDescricao = new TextBox;
$obTxtDescricao->setTitle     ( "Descrição do grupo de vencimentos.");
$obTxtDescricao->setName      ( "stDescricao"   );
$obTxtDescricao->setRotulo    ( "*Descrição"    );
$obTxtDescricao->setMaxLength ( 80              );
$obTxtDescricao->setSize      ( 50              );
$obTxtDescricao->setValue     ( $stDescricao    );

$obDtVencimento = new Data;
$obDtVencimento->setTitle     ( "Data de vencimento do valor integral." );
$obDtVencimento->setName      ( "dtDataVencimento" );
$obDtVencimento->setValue     ( $dtDataVencimento  );
$obDtVencimento->setRotulo    ( "*Vencimento Valor Integral" );
$obDtVencimento->setMaxLength ( 20 );
$obDtVencimento->setSize      ( 10 );

$obTxtLimiteInicial = new Moeda;
$obTxtLimiteInicial->setTitle              ( "Limite inicial para o lançamento utilizar o grupo de vencimento.");
$obTxtLimiteInicial->setName               ( "inLimiteInicial"   );
$obTxtLimiteInicial->setRotulo             ( "*Limite Inicial"   );
$obTxtLimiteInicial->setValue              ( $inLimiteInicial    );
$obTxtLimiteInicial->setNull               ( true               );

$obTxtLimiteFinal = new Moeda;
$obTxtLimiteFinal->setTitle              ( "Limite final para o lançamento utilizar o grupo de vencimento." );
$obTxtLimiteFinal->setName               ( "inLimiteFinal"     );
$obTxtLimiteFinal->setRotulo             ( "*Limite Final"     );
$obTxtLimiteFinal->setValue              ( $inLimiteFinal      );
$obTxtLimiteFinal->setNull               ( true               );

$obSpnGrupo = new Span;
$obSpnGrupo->setId ( "lsGrupo" );

$obHdnFlagEditar = new Hidden;
$obHdnFlagEditar->setName  ( "flagEditar" );
$obHdnFlagEditar->setValue ( 'incluir'    );

$obHdnInLinha = new Hidden;
$obHdnInLinha->setName  ( "inLinhaAux" );
$obHdnInLinha->setValue ( $_REQUEST["inLinha"]  );

$obBtnIncluirGrupo = new Button;
$obBtnIncluirGrupo->setName              ( "btnIncluirGrupo"     );
$obBtnIncluirGrupo->setValue             ( "Definir"             );
$obBtnIncluirGrupo->setTipo              ( "button"              );
$obBtnIncluirGrupo->obEvento->setOnClick ( "incluirGrupo();"     );
$obBtnIncluirGrupo->setDisabled          ( false                 );

$obBtnLimparGrupo = new Button;
$obBtnLimparGrupo->setName               ( "btnLimparGrupo"      );
$obBtnLimparGrupo->setValue              ( "Limpar"              );
$obBtnLimparGrupo->setTipo               ( "button"              );
$obBtnLimparGrupo->obEvento->setOnClick  ( "buscaValor('limparGrupos');" );
$obBtnLimparGrupo->setDisabled           ( false                 );

// ATRIBUTOS DINAMICOS

$rsAtributos = new RecordSet;

if ($_REQUEST["stAcao"] == "incluir") {
    $obRARRCalendarioFiscal->obRCadastroDinamico->recuperaAtributosSelecionados( $rsAtributos );
} else {
    $arChaveAtributoCalendario = array( "cod_grupo" => $_REQUEST["inCodigoCredito"], "ano_exercicio" => $_REQUEST["stExercicio"] );

    $obRARRCalendarioFiscal->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoCalendario );
    $obRARRCalendarioFiscal->obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributos );
}

$obMontaAtributos = new MontaAtributos;
$obMontaAtributos->setTitulo     ( "Atributos"  );
$obMontaAtributos->setName       ( "Atributo_"  );
$obMontaAtributos->setRecordSet  ( $rsAtributos );

$obCheckSegueVencimentos = new CheckBox;
$obCheckSegueVencimentos->setTitle   ( "Após a inclusão do calendário vai para a tela de definição de vencimentos." );
$obCheckSegueVencimentos->setName    ( "boSegueVencimentos"                  );
$obCheckSegueVencimentos->setValue   ( "1"                                   );
$obCheckSegueVencimentos->setLabel   ( "Seguir para definição de vencimentos?" );
$obCheckSegueVencimentos->setNull    ( true                                  );
$obCheckSegueVencimentos->setChecked ( true                                  );

$obUtilizarCotaUnicaSIM = new Radio;
$obUtilizarCotaUnicaSIM->setTitle   ( "Utilizar ou não cota(s) única(s) nos lançamentos que utilizem o grupo de vencimentos." );
$obUtilizarCotaUnicaSIM->setRotulo  ( "Utilizar Cota Única" );
$obUtilizarCotaUnicaSIM->setName    ( "boUtilizarCotaUnica" );
$obUtilizarCotaUnicaSIM->setValue   ( true );
$obUtilizarCotaUnicaSIM->setLabel   ( "Sim" );
$obUtilizarCotaUnicaSIM->setNull    ( false );
$obUtilizarCotaUnicaSIM->setChecked ( true  );
$obUtilizarCotaUnicaSIM->setId      ( "boUtilizarCotaUnicaSim" );

$obUtilizarCotaUnicaNAO = new Radio;
$obUtilizarCotaUnicaNAO->setName    ( "boUtilizarCotaUnica" );
$obUtilizarCotaUnicaNAO->setValue   ( false );
$obUtilizarCotaUnicaNAO->setLabel   ( "Não" );
$obUtilizarCotaUnicaNAO->setNull    ( false );
$obUtilizarCotaUnicaNAO->setId      ( "boUtilizarCotaUnicaNao" );

$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( 'oculto' );

$obFormulario = new Formulario();
$obFormulario->addForm  ( $obForm );
$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnAcao );

$obFormulario->addHidden( $obHdnFlagEditar          );
$obFormulario->addHidden( $obHdnInLinha             );

$obFormulario->addTitulo( "Dados para Calendário" );
if ($_REQUEST['stAcao'] == "alterar") {
    $obFormulario->addHidden( $obHdnCodigoGrupo );
    $obFormulario->addHidden( $obHdnDescricao );
    $obFormulario->addHidden( $obHdnExercicio );

    $obMontaGrupoCredito->obRARRGrupo->setCodGrupo( $_REQUEST['inCodigoCreditoLst'] );
    $obMontaGrupoCredito->obRARRGrupo->setDescricao( $_REQUEST['stDescricaoCredito'] );
    $obMontaGrupoCredito->obRARRGrupo->setExercicio( $_REQUEST['stExercicio'] );

    $obMontaGrupoCredito->geraFormulario( $obFormulario, false );
    //$obFormulario->addComponente( $obLblGrupo );
}
if ($_REQUEST['stAcao'] == "incluir") {
    //$obFormulario->addComponenteComposto( $obTxtCodigoCredito, $obCmbGrupoCredito );
    $obMontaGrupoCredito->geraFormulario( $obFormulario, true );
}

$obFormulario->addComponente( $obTxtMinLancamento );
$obFormulario->addComponente( $obTxtMinIntegral   );
$obFormulario->addComponente( $obTxtMinParcela    );
$obFormulario->addTitulo( "Grupos de Vencimentos" );
$obFormulario->addComponente( $obTxtDescricao     );
$obFormulario->addComponente( $obDtVencimento     );
$obFormulario->addComponente( $obTxtLimiteInicial );
$obFormulario->addComponente( $obTxtLimiteFinal   );
$obFormulario->agrupaComponentes ( array ($obUtilizarCotaUnicaSIM, $obUtilizarCotaUnicaNAO) );
$obFormulario->agrupaComponentes ( array( $obBtnIncluirGrupo, $obBtnLimparGrupo ) );
$obFormulario->addSpan( $obSpnGrupo );
$obMontaAtributos->geraFormulario( $obFormulario );
$obFormulario->addComponente( $obCheckSegueVencimentos );
$obFormulario->Cancelar();
$obFormulario->Show();
