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
    * Página de Formulário para o cadastro de loteamento
    * Data de Criação   : 31/08/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Gustavo Passos Tourinho
                             Tonismar Régis Bernardo

    * @ignore

    * $Id: FMManterLoteamento.php 61287 2014-12-30 12:03:13Z evandro $

    * Casos de uso: uc-05.01.15
*/

/*
$Log$
Revision 1.16  2006/09/18 10:30:58  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GT_CIM_NEGOCIO."RCIMConfiguracao.class.php"     );
include_once(CAM_GT_CIM_NEGOCIO."RCIMLoteamento.class.php"       );
include_once(CAM_GT_CIM_COMPONENTES."MontaLocalizacao.class.php" );
include_once(CAM_GT_CIM_COMPONENTES."MontaLocalizacaoLoteamento.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterLoteamento";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include_once( $pgJs );

if ( empty( $_REQUEST['stAcao'] ) ) {
    $_REQUEST['stAcao'] = "incluir";
}

$obRCIMConfiguracao = new RCIMConfiguracao;
$obRCIMConfiguracao->consultarConfiguracao();
$obRCIMConfiguracao->setCodigoModulo( 12 );
$obRCIMConfiguracao->setAnoExercicio( Sessao::getExercicio() );
$obRCIMConfiguracao->consultarConfiguracao();
$stMascaraLote     = $obRCIMConfiguracao->getMascaraLote();
$obRCIMConfiguracao->consultarMascaraProcesso( $stMascaraProcesso );

if ($_REQUEST['stAcao'] == "incluir") {
    $obMontaLocalizacao = new MontaLocalizacao();
    $obMontaLocalizacao->setCadastroLocalizacao( false );
} elseif ($_REQUEST['stAcao'] == "alterar") {
    $obRegra = new RCIMLocalizacao();
    $obRegra->setCodigoLocalizacao( $_REQUEST['inCodLocalizacao'] );
    $obRegra->listarVigencias( $rsVigencia );

}

$obMontaLocalizacaoLoteamento = new MontaLocalizacaoLoteamento();
$obMontaLocalizacaoLoteamento->setCadastroLocalizacao( false );
$obMontaLocalizacaoLoteamento->boObrigatorio =  false ;

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $_REQUEST['stAcao'] );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

// Definição dos objetos para o formuário

$obTxtNomeLoteamento = new TextBox;
$obTxtNomeLoteamento->setRotulo       ( "Nome"               );
$obTxtNomeLoteamento->setTitle        ( "Nome do Loteamento" );
$obTxtNomeLoteamento->setName         ( "stNomLoteamento"    );
$obTxtNomeLoteamento->setId           ( "stNomLoteamento"    );
$obTxtNomeLoteamento->setValue        ( $_REQUEST['stNomLoteamento']     );
$obTxtNomeLoteamento->setSize         ( 80 );
$obTxtNomeLoteamento->setMaxLength    ( 80 );
$obTxtNomeLoteamento->setNull         ( false );

$obBscProcesso = new BuscaInner;
$obBscProcesso->setRotulo ( "Processo" );
$obBscProcesso->setTitle  ( "Número do processo no protocolo que gerou a aprovação do loteamento" );
$obBscProcesso->obCampoCod->setName ("inNumProcesso");
$obBscProcesso->obCampoCod->setValue( $inNumProcesso );
$obBscProcesso->obCampoCod->setSize ( strlen($stMascaraProcesso) );
$obBscProcesso->obCampoCod->setMaxLength( strlen($stMascaraProcesso) );
$obBscProcesso->obCampoCod->obEvento->setOnKeyUp("mascaraDinamico('".$stMascaraProcesso."', this, event);");
$obBscProcesso->obCampoCod->obEvento->setOnChange( "buscaValor('buscaProcesso');");
$obBscProcesso->setFuncaoBusca( "abrePopUp('".CAM_GA_PROT_POPUPS."processo/FLBuscaProcessos.php','frm','inNumProcesso','campoInner2','','".Sessao::getId()."','800','550')" );

$rsLote = new RecordSet();

$obCmbCodigoLoteamento = new Select;
$obCmbCodigoLoteamento->setName       ( "inNumLoteamento" );
$obCmbCodigoLoteamento->setTitle      ( "Lote que deu origem ao loteamento" );
$obCmbCodigoLoteamento->setNull       ( false                      );
$obCmbCodigoLoteamento->setRotulo     ( "Lote"                     );
$obCmbCodigoLoteamento->setStyle      ( "width: 150px"             );
$obCmbCodigoLoteamento->addOption     ( "", "Selecione"            );
$obCmbCodigoLoteamento->setCampoID    ( "cod_lote"                 );
$obCmbCodigoLoteamento->setCampoDesc  ( "valor"                    );
$obCmbCodigoLoteamento->preencheCombo ( $rsLote                    );
$obCmbCodigoLoteamento->obEvento->setOnClick("buscaValor('carregaLotes1');");
$obCmbCodigoLoteamento->obEvento->setOnBlur("return false;");

if ($_REQUEST['stAcao'] == "alterar") {
    $obRCIMLote = new RCIMLote;
    $obRCIMLote->setCodigoLote( $_REQUEST["inNumLoteOrigem"] );
    $obRCIMLote->obRCIMLocalizacao->setCodigoLocalizacao( $_REQUEST["inCodLocalizacao"] );
    $obRCIMLote->listarLotes( $rsLote );
    $rsLote->addStrPad('valor', strlen($stMascaraLote) , '0');

}

$obCmbCodigoLote = new Select;
$obCmbCodigoLote->setName       ( "inNumLote" );
$obCmbCodigoLote->setTitle      ( "Incluir um lote na lista abaixo" );
$obCmbCodigoLote->setRotulo     ( "*Lote"                     );
$obCmbCodigoLote->setStyle      ( "width: 150px"             );
$obCmbCodigoLote->addOption     ( "", "Selecione"            );
$obCmbCodigoLote->setCampoID    ( "[cod_lote]-[valor]"       );
$obCmbCodigoLote->setCampoDesc  ( "valor"                    );
$obCmbCodigoLote->preencheCombo ( $rsLote                    );
$obCmbCodigoLote->obEvento->setOnClick("buscaValor('carregaLotes2');");
$obCmbCodigoLote->obEvento->setOnBlur("return false;");

$obRdoSim = new Radio;
$obRdoSim->setRotulo  ( "Caucionado"   );
$obRdoSim->setValue   ( "S" );
$obRdoSim->setName    ( "boCaucionado" );
$obRdoSim->setLabel   ( "Sim" );

$obRdoNao = new Radio;
$obRdoNao->setRotulo  ( "Caucionado"   );
$obRdoNao->setValue   ( "N" );
$obRdoNao->setName    ( "boCaucionado" );
$obRdoNao->setLabel   ( "Não" );
$obRdoNao->setChecked ( true     );

$obDtAprovacao = new Data;
$obDtAprovacao->setRotulo  ( "Data de Aprovação"        );
$obDtAprovacao->setName    ( "dtAprovacao"              );
$obDtAprovacao->setNull    ( false );
$obDtAprovacao->setValue   ( $_REQUEST["dtAprovacao"]   );

$obDtLiberacao = new Data;
$obDtLiberacao->setRotulo  ( "Data de Liberação"        );
$obDtLiberacao->setName    ( "dtLiberacao"              );
$obDtLiberacao->setNull    ( false );
$obDtLiberacao->setValue   ( $_REQUEST["dtLiberacao"]   );

$inAreaComunitaria = number_format($_REQUEST['inAreaComunitaria'],2,",",".");
$obTxtAreaComunitaria = new TextBox;
$obTxtAreaComunitaria->setRotulo       ( "Área Comunitária"            );
$obTxtAreaComunitaria->setTitle        ( "Área destinada ao uso comum" );
$obTxtAreaComunitaria->setName         ( "inAreaComunitaria"           );
$obTxtAreaComunitaria->setValue        ( $inAreaComunitaria            );
$obTxtAreaComunitaria->setSize         ( 12 );
$obTxtAreaComunitaria->setMaxLength    ( 12 );
$obTxtAreaComunitaria->setNull         ( false );
$obTxtAreaComunitaria->setFloat        ( true  );

$inAreaLogradouro = number_format($_REQUEST['inAreaLogradouro'],2,",",".");
$obTxtAreaLogradouro = new TextBox;
$obTxtAreaLogradouro->setRotulo       ( "Área de Logradouros"             );
$obTxtAreaLogradouro->setTitle        ( "Área destinada para logradouros" );
$obTxtAreaLogradouro->setName         ( "inAreaLogradouro"                );
$obTxtAreaLogradouro->setValue        ( $inAreaLogradouro                 );
$obTxtAreaLogradouro->setSize         ( 12 );
$obTxtAreaLogradouro->setMaxLength    ( 12 );
$obTxtAreaLogradouro->setNull         ( false );
$obTxtAreaLogradouro->setFloat        ( true  );

$obLblQtdLote = new Label;
$obLblQtdLote->setRotulo       ( "Lotes Caucionados"               );
$obLblQtdLote->setTitle        ( "Quantidade de Lotes Caucionados" );
$obLblQtdLote->setId           ( "QtdLotes"                        );
$obLblQtdLote->setName         ( "inQtdLotes"                      );
$obLblQtdLote->setValue        ( $inQtdLotes                       );

$obLblLocalizacao = new Label;
$obLblLocalizacao->setRotulo   ( "Localização" );
$obLblLocalizacao->setTitle    ( "Localização dos Lotes" );
$obLblLocalizacao->setId       ( "Localizacao" );
$obLblLocalizacao->setName     ( "stLocalizacao" );
$obLblLocalizacao->setValue    ( $_REQUEST['stLocalizacao']  );

$obBtnIncluirLote = new Button;
$obBtnIncluirLote->setName              ( "btnIncluirLote" );
$obBtnIncluirLote->setValue             ( "Incluir" );
$obBtnIncluirLote->setTipo              ( "button" );
$obBtnIncluirLote->obEvento->setOnClick ( "incluiLote();" );
$obBtnIncluirLote->setDisabled          ( false );

$obBtnLimparLote = new Button;
$obBtnLimparLote->setName              ( "btnLimparLote" );
$obBtnLimparLote->setValue             ( "Limpar" );
$obBtnLimparLote->obEvento->setOnClick ( "limpaLotes();" );

$obSpnCampoLote = new Span;
$obSpnCampoLote->setId ( "spanCampoLote" );

$obSpnLotes = new Span;
$obSpnLotes->setId ( "spanLotes" );

if ($_REQUEST['stAcao'] == "alterar") {
    $obRCIMLoteamento = new RCIMLoteamento;
    if ($_REQUEST[ "inProcesso" ]) {
        $inCodigoProcesso = $_REQUEST[ "inProcesso" ]."/".$_REQUEST[ "stExercicio" ];
    }

    $obHdnCodigoLoteamento = new Hidden;
    $obHdnCodigoLoteamento->setName  ( "inCodigoLoteamento"             );
    $obHdnCodigoLoteamento->setValue ( $_REQUEST[ "inCodigoLoteamento" ] );

    $obHdnLoteOrigem = new Hidden;
    $obHdnLoteOrigem->setName ( "inNumLoteamento" );
    $obHdnLoteOrigem->setValue( $_REQUEST[ "inNumLoteamento" ] );

    $obHdnCodigoProcesso = new Hidden;
    $obHdnCodigoProcesso->setName ( "inProcesso" );
    $obHdnCodigoProcesso->setValue( $inCodigoProcesso  );

    $obLblCodigoProcesso = new Label;
    $obLblCodigoProcesso->setRotulo ( "Processo" );
    $obLblCodigoProcesso->setTitle  ( "Número de processo no protocolo que gerou a aprovação dos lotes" );
    $obLblCodigoProcesso->setName   ( "inCodigoProcesso" );
    $obLblCodigoProcesso->setValue  ( $_REQUEST['inProcesso']  );

    $obLblLoteOrigem = new Label;
    $obLblLoteOrigem->setRotulo     ( "Lote de Origem"    );
    $obLblLoteOrigem->setTitle      ( "Lote que deu origem ao loteamento" );
    $obLblLoteOrigem->setId         ( "inNumLoteamento"   );
    $obLblLoteOrigem->setName       ( "inNumLoteamento"   );
    $obLblLoteOrigem->setValue      ( STR_PAD($_REQUEST['inNumLoteOrigem'],strlen($stMascaraLote),'0',STR_PAD_LEFT) );

    $obRCIMLoteamento->setCodigoLoteamento ( $_REQUEST[ "inCodigoLoteamento" ] );
    $obRCIMLoteamento->listarLoteamentoLote( $rsLotes );
    $obRCIMLoteamento->obRCIMLote->obRCIMLocalizacao->setCodigoLocalizacao( $_REQUEST['inCodLocalizacao'] );

    $arLotesSessao = array();
    Sessao::write('lotes', $arLotesSessao);

    $inCount = 0;
    while ( !$rsLotes->eof() ) {
        if ( $rsLotes->getCampo( 'caucionado' ) == "t" ) {
            $stCaucionado = "Sim";
        } elseif ( $rsLotes->getCampo( 'caucionado' ) == "f" ) {
            $stCaucionado = "Não";
        }

        $arTMP['inLinha']                 = $inCount++;
        $arTMP['inCodLote']               = $rsLotes->getCampo( 'cod_lote' );
        $arTMP['inNumLote']               = STR_PAD($rsLotes->getCampo( 'valor' ),strlen($stMascaraLote),'0',STR_PAD_LEFT);
        $arTMP['boCaucionado']            = $stCaucionado;
        $arTMP['stLocalizacaoLoteamento'] = $rsLotes->getCampo( 'codigo_composto' );

        $arLotesSessao[] = $arTMP;
        Sessao::write('lotes', $arLotesSessao);
        $rsLotes->proximo();
    }
   SistemaLegado::executaFramePrincipal("buscaValor('listaLote');");
}

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction            ( $pgProc );
$obForm->setTarget            ( "oculto" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm        ( $obForm      );
$obFormulario->setAjuda ( "UC-05.01.15" );
$obFormulario->addHidden      ( $obHdnAcao   );
$obFormulario->addHidden      ( $obHdnCtrl   );

$obFormulario->addTitulo      ( "Dados para loteamento" );

$obFormulario->addComponente  ( $obTxtNomeLoteamento  );
if ($_REQUEST['stAcao'] == "alterar") {
    $obFormulario->addHidden  ( $obHdnCodigoLoteamento );
    $obFormulario->addHidden  ( $obHdnLoteOrigem       );
    $obFormulario->addComponente ( $obLblLocalizacao );
    $obFormulario->addComponente ( $obLblLoteOrigem );
} else {
    $obMontaLocalizacao->geraFormulario( $obFormulario );
    $obFormulario->addComponente( $obCmbCodigoLoteamento );
}
if (( $_REQUEST['stAcao'] == "alterar" ) && ( $_REQUEST[ "inProcesso" ] != "" )) {
    $obFormulario->addHidden     ( $obHdnCodigoProcesso   );
    $obFormulario->addComponente ( $obLblCodigoProcesso );
} else {
    $obFormulario->addComponente  ( $obBscProcesso        );
}
$obFormulario->addComponente  ( $obDtAprovacao        );
$obFormulario->addComponente  ( $obDtLiberacao        );
$obFormulario->addComponente  ( $obTxtAreaComunitaria );
$obFormulario->addComponente  ( $obTxtAreaLogradouro  );
$obFormulario->addComponente  ( $obLblQtdLote         );

$obFormulario->addTitulo      ( "Lotes"               );
$obMontaLocalizacaoLoteamento->geraFormulario( $obFormulario );
$obFormulario->addComponente  ( $obCmbCodigoLote      );
$obFormulario->agrupaComponentes( array( $obRdoSim, $obRdoNao ) );
$obFormulario->defineBarra    ( array( $obBtnIncluirLote, $obBtnLimparLote ), "left", "" );
$obFormulario->addSpan        ( $obSpnLotes           );

if ( $_REQUEST['stAcao'] == "incluir" )
    $obFormulario->OK    ();
else
    $obFormulario->Cancelar ();
$obFormulario->setFormFocus( $obTxtNomeLoteamento->getId() );
$obFormulario->show();
?>
