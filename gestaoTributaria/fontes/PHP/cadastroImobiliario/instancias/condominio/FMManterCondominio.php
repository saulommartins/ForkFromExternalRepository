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
    * Página de Formulário da Caonfiguração do cadastro imobiliario
    * Data de Criação   : 05/04/2005

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Marcelo Boezio Paulino

    * @ignore

    * $Id: FMManterCondominio.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.14
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMCondominio.class.php"      );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMConfiguracao.class.php"    );
include_once (CAM_GT_CIM_COMPONENTES."MontaLocalizacao.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterCondominio";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";
include_once( $pgJs );

//Instancia objetos
$obRCIMCondominio   = new RCIMCondominio;
$rsTipoCondominio   = new RecordSet;
$obMontaAtributos   = new MontaAtributos;
$obRCIMConfiguracao = new RCIMConfiguracao;

//Recupera mascara do processo
$obRCIMConfiguracao->setCodigoModulo( 12 );
$obRCIMConfiguracao->setAnoExercicio( Sessao::getExercicio() );
$obRCIMConfiguracao->consultarConfiguracao();
$obRCIMConfiguracao->consultarMascaraProcesso( $stMascaraProcesso );
$stMascaraLote = $obRCIMConfiguracao->getMascaraLote();

$stAcao = $request->get('stAcao');

if ($stAcao == "incluir") {
    $obMontaLocalizacao = new MontaLocalizacao();
    $obMontaLocalizacao->setCadastroLocalizacao( false );
    $obMontaLocalizacao->SetObrigatorio(false);
}

if ($stAcao == "alterar") {
    $obMontaLocalizacao = new MontaLocalizacao();
    $obMontaLocalizacao->setCadastroLocalizacao( false );

    $obRegra = new RCIMLocalizacao();
    $obRegra->setCodigoLocalizacao( $_REQUEST['inCodLocalizacao'] );
    $obRegra->listarVigencias( $rsVigencia );

//--------------------- RECUPERA LISTA DE LOTES
    $obMontaLocalizacao = new MontaLocalizacao();
    $obMontaLocalizacao->setCadastroLocalizacao( false );
    $obMontaLocalizacao->SetObrigatorio(false);

    $obRCIMCondominio->setCodigoCondominio ( $_REQUEST[ "inCodigoCondominio" ] );
    $obRCIMCondominio->listarLotesCondominio ( $rsLotes );

    $arLotesSessao = array();
    Sessao::write('lotes', $arLotesSessao);

    $inCount = 0;
    while ( !$rsLotes->eof() ) {

        $arTMP['inLinha']                 = $inCount++;
        $arTMP['inCodLote']               = $rsLotes->getCampo( 'cod_lote' );
        $arTMP['inNumLote']               = STR_PAD($rsLotes->getCampo( 'valor' ),strlen($stMascaraLote),'0',STR_PAD_LEFT);

        //$arTMP['inNumLote']               = $rsLotes->getCampo ('valor');
        $arTMP['stLocalizacaoLoteamento'] = $rsLotes->getCampo( 'codigo_composto' );
        $arTMP['inImoveis']               = $rsLotes->getCampo( 'imovel'          );
        $arLotesSessao[]                  = $arTMP;
        $rsLotes->proximo();
    }
    Sessao::write('lotes', $arLotesSessao);
    SistemaLegado::executaFramePrincipal("buscaValor('listaLote');");

//--------------------- RECUPERA LISTA DE LOTES FIM

}

$arProcesso = preg_split( "/[^a-zA-Z0-9]/", $stMascaraProcesso );
if ($_REQUEST['inCodigoProcesso']) {
    $stProcesso = str_pad( $_REQUEST["inCodigoProcesso"], strlen( $arProcesso[0] ), "0", STR_PAD_LEFT );
    $stSeparador = preg_replace( "/[a-zA-Z0-9]/","", $stMascaraProcesso );
    $stProcesso .= $stSeparador.$_REQUEST["inExercicio"];
}

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

// OBJETOS HIDDEN
$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdninNumLote = new Hidden;
$obHdninNumLote->setName( 'inCodLote' );
$obHdninNumLote->setValue('');

$obHdninCodCondominio = new Hidden;
$obHdninCodCondominio->setName( 'inCodigoCondominio' );
$obHdninCodCondominio->setValue( $_REQUEST['inCodigoCondominio'] );

// DEFINICAO DOS COMPONENTES DO FORMULARIO
if ($stAcao == 'incluir') {
    $arLotesSessao = array();
    Sessao::write('lotes', $arLotesSessao);
}
$rsLote = new RecordSet();

$obBscLote = new BuscaInner;
$obBscLote->setRotulo               ( "Lote"                         );
$obBscLote->setTitle  ( "Lotes referentes à Localização selecionada" );
$obBscLote->setNull                 ( true                           );
$obBscLote->obCampoCod->setName     ("inNumLote"                     );
$obBscLote->obCampoCod->setMaxLength( strlen( $stMascaraLote )       );
$obBscLote->obCampoCod->setSize     ( strlen( $stMascaraLote )+1     );
$obBscLote->obCampoCod->setInteiro  ( false );
//$obBscLote->obCampoCod->obEvento->setOnKeyUp( "mascaraDinamico('".$stMascaraLote."', this, event);" );
$obBscLote->setFuncaoBusca( "abrePopUp('".CAM_GT_CIM_POPUPS."lote/FLBuscaLote.php','frm','inNumLote','inCodLote','juridica','".Sessao::getId()."','800','550')" );

$obTxtNomCondominio = new TextBox;
$obTxtNomCondominio->setRotulo       ( "Nome"               );
$obTxtNomCondominio->setName         ( "stNomCondominio"    );
$obTxtNomCondominio->setId           ( "stNomCondominio"    );
$obTxtNomCondominio->setValue        ( $_REQUEST['stNomCondominio'] );
$obTxtNomCondominio->setSize         ( 80 );
$obTxtNomCondominio->setMaxLength    ( 80 );
$obTxtNomCondominio->setNull         ( false );

$obTxtTipoCondominio = new TextBox;
$obTxtTipoCondominio->setRotulo      ( "Tipo"                    );
$obTxtTipoCondominio->setName        ( "inCodigoTipo"            );
$obTxtTipoCondominio->setTitle       ( "Tipo de condomínio"      );
$obTxtTipoCondominio->setValue       ( $_REQUEST["inCodigoTipo"] );
$obTxtTipoCondominio->setSize        ( 8                         );
$obTxtTipoCondominio->setMaxLength   ( 8                         );
$obTxtTipoCondominio->setNull        ( true                      );
$obTxtTipoCondominio->setInteiro     ( true                      );

$obRCIMCondominio->listarTiposCondominio( $rsTipoCondominio );
$obCmbTipoCondominio = new Select;
$obCmbTipoCondominio->setName        ( "cmbTipoCondominio"       );
$obCmbTipoCondominio->setRotulo      ( "Tipo"                    );
$obCmbTipoCondominio->addOption      ( "", "Selecione"           );
$obCmbTipoCondominio->setCampoId     ( "cod_tipo"                );
$obCmbTipoCondominio->setCampoDesc   ( "nom_tipo"                );
$obCmbTipoCondominio->preencheCombo  ( $rsTipoCondominio         );
$obCmbTipoCondominio->setValue       ( $_REQUEST["inCodigoTipo"] );
$obCmbTipoCondominio->setNull        ( false                     );
$obCmbTipoCondominio->setStyle       ( "width: 220px"            );

$obBscCGM = new BuscaInner;
$obBscCGM->setRotulo              ( "CGM"                                  );
$obBscCGM->setTitle               ( "CGM de pessoa jurídica do condomínio" );
$obBscCGM->setNull                ( true                                   );
$obBscCGM->setId                  ( "campoInner"                           );
$obBscCGM->obCampoCod->setName    ( "inNumCGM"                             );
$obBscCGM->setValue  ( $_REQUEST["stNomCGM"]);
$obBscCGM->obCampoCod->setValue   ( $_REQUEST["inNumCGM"]                  );
$obBscCGM->obCampoCod->obEvento->setOnChange("buscaCGM('$tipoCGM');" );
$obBscCGM->setFuncaoBusca("abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inNumCGM','campoInner','juridica','".Sessao::getId()."','800','550')" );

$obTxtAreaTotalComum = new TextBox;
$obTxtAreaTotalComum->setRotulo       ( "Área Total Comum"    );
$obTxtAreaTotalComum->setName         ( "inAreaTotalComum"    );
$obTxtAreaTotalComum->setTitle        ( "Área total comum do condomínio" );
$obTxtAreaTotalComum->setValue        ( $_REQUEST['inAreaTotalComum']     );
$obTxtAreaTotalComum->setSize         ( 10 );
$obTxtAreaTotalComum->setMaxLength    ( 10 );
$obTxtAreaTotalComum->setFloat        ( true );
$obTxtAreaTotalComum->setNull         ( false );

$obBscProcesso = new BuscaInner;
$obBscProcesso->setRotulo ( "Processo" );
$obBscProcesso->setTitle  ( "Processo do protocolo que formaliza este condomínio" );
$obBscProcesso->obCampoCod->setName ("inProcesso");
$obBscProcesso->obCampoCod->setValue( $stProcesso );
$obBscProcesso->obCampoCod->obEvento->setOnKeyUp("mascaraDinamico('".$stMascaraProcesso."', this, event);");
$obBscProcesso->obCampoCod->obEvento->setOnChange( "buscaValor('buscaProcesso');" );
$obBscProcesso->obCampoCod->setSize ( strlen($stMascaraProcesso) );
$obBscProcesso->obCampoCod->setMaxLength ( strlen($stMascaraProcesso) );
$obBscProcesso->setFuncaoBusca( "abrePopUp('".CAM_GA_PROT_POPUPS."processo/FLBuscaProcessos.php','frm','inProcesso','campoInner2','','".Sessao::getId()."','800','550')" );

// mostra atributos selecionados
if ($stAcao == "incluir") {
    $obRCIMCondominio->obRCadastroDinamico->recuperaAtributosSelecionados( $rsAtributos );
} else {
    $arChaveAtributoCondominio =  array( "cod_face"    => $_REQUEST["inCodigoFace"],
                                         "cod_localizacao" => $_REQUEST["inCodigoLocalizacao"] );
    $obRCIMCondominio->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoCondominio );
    $obRCIMCondominio->obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributos );
}
$obMontaAtributos = new MontaAtributos;
$obMontaAtributos->setTitulo     ( "Atributos"  );
$obMontaAtributos->setName       ( "Atributo_"  );
$obMontaAtributos->setRecordSet  ( $rsAtributos );

if ($stAcao == 'inclusao') {
$obRadioCondominio = new Radio;
$obRadioCondominio->setName      ( "stProximaPagina"            );
$obRadioCondominio->setRotulo    ( "Seguir para o cadastro de:" );
$obRadioCondominio->setValue     ( "condominio"                 );
$obRadioCondominio->setLabel     ( "Condomínio"                 );
$obRadioCondominio->setNull      ( false                        );
$obRadioCondominio->setChecked   ( true                         );

$obRadioEdificacao = new Radio;
$obRadioEdificacao->setName      ( "stProximaPagina"            );
$obRadioEdificacao->setRotulo    ( "Seguir para o cadastro de:" );
$obRadioEdificacao->setValue     ( "edificacao"                 );
$obRadioEdificacao->setLabel     ( "Edificação"                 );
$obRadioEdificacao->setNull      ( false                        );
$obRadioEdificacao->setChecked   ( false                        );

$obRadioConstrucao = new Radio;
$obRadioConstrucao->setName      ( "stProximaPagina"            );
$obRadioConstrucao->setRotulo    ( "Seguir para o cadastro de:" );
$obRadioConstrucao->setValue     ( "construcao"                 );
$obRadioConstrucao->setLabel     ( "Construção"                 );
$obRadioConstrucao->setNull      ( false                        );
$obRadioConstrucao->setChecked   ( false                        );
}
//------------------------------------------------------------ COMPONENTES DO SPAN
/*
$obCmbCodigoLote = new Select;
$obCmbCodigoLote->setName       ( "inNumLote" );
$obCmbCodigoLote->setTitle      ( ""                   );
$obCmbCodigoLote->setRotulo     ( "Lote"               );
$obCmbCodigoLote->setStyle      ( "width: 150px"       );
$obCmbCodigoLote->addOption     ( "", "Selecione"      );
$obCmbCodigoLote->setCampoID    ( "[cod_lote]-[valor]" );
$obCmbCodigoLote->setCampoDesc  ( "valor"              );
$obCmbCodigoLote->preencheCombo ( $rsLote              );
$obCmbCodigoLote->obEvento->setOnClick("buscaValor('carregaLotes1');");
$obCmbCodigoLote->obEvento->setOnBlur("return false;");
*/

$obBtnIncluirLote = new Button;
$obBtnIncluirLote->setName              ( "btnIncluirLote" );
$obBtnIncluirLote->setValue             ( "Incluir" );
$obBtnIncluirLote->setTipo              ( "button" );
$obBtnIncluirLote->obEvento->setOnClick ( "buscaValor('incluiLote');" );
$obBtnIncluirLote->setDisabled          ( false );

$obBtnLimparLote = new Button;
$obBtnLimparLote->setName              ( "btnLimparLote" );
$obBtnLimparLote->setValue             ( "Limpar" );
$obBtnLimparLote->obEvento->setOnClick ( "buscaValor('limpaLotes');" );

//NA AÇÃO INCLUIR NÃO ESTÁ SENDO UTILIZADO O METÓDO OK DA CLASSE FORMULARIO PARA QUE SE POSSA SETAR A FUNÇÃO
//LIMPAPAGINA. ESTA FUNÇÃO RETIRA DA SESSÃO OS LOTES JÁ INCLUÍDOS. NA VERSÃO ANTERIOR A TELA ERA LIMPA MAS
//COMO MANTINHA OS CALORES EM SESSÃO RETORNAVA ERRO NA INCLUSÃO SE FOSSE INCLUIDO UM LOTE QUE JÁ HAVIA SIDO INCLUIDO
//NA LISTA ANTES DESTA SER LIMPADA
$obOk  = new Ok();
$obLimpar  = new Limpar();
$obLimpar->obEvento->setOnClick("buscaValor('limpaPagina');");

$obSpnLotes = new Span;
$obSpnLotes->setId ( "spanLotes" );

//---------------------------------------------------------------------------------

//DEFINICAO DO FORMULARIO
$obForm = new Form;
$obForm->setAction            ( $pgProc );
$obForm->setTarget            ( "oculto" );

$obFormulario = new Formulario;
$obFormulario->addForm        ( $obForm      );
$obFormulario->setAjuda       ( "UC-05.01.14" );
$obFormulario->addHidden      ( $obHdnAcao   );
$obFormulario->addHidden      ( $obHdnCtrl   );
$obFormulario->addHidden      ( $obHdninNumLote );
$obFormulario->addHidden      ( $obHdninCodCondominio );

$obFormulario->addTitulo            ( "Dados para condomínio" );
$obFormulario->addComponente        ( $obTxtNomCondominio   );
$obFormulario->addComponenteComposto( $obTxtTipoCondominio, $obCmbTipoCondominio );
$obFormulario->addComponente        ( $obBscCGM             );
$obFormulario->addComponente        ( $obTxtAreaTotalComum  );
$obFormulario->addComponente        ( $obBscProcesso        );
$obMontaAtributos->geraFormulario   ( $obFormulario         );
if ($stAcao == 'inclusao') {
   $obFormulario->agrupaComponentes    ( array( $obRadioCondominio, $obRadioEdificacao, $obRadioConstrucao) );
}

$obFormulario->addTitulo      ( "Lotes"               );
$obMontaLocalizacao->geraFormulario( $obFormulario );
$obFormulario->addComponente($obBscLote);
//$obSpnCompLotes = new Span();
//$obSpnCompLotes->setId( 'spanCompLotes' );
//$obFormulario->addSpan( $obSpnCompLotes );
//$obFormulario->addComponente  ( $obCmbCodigoLote      );
$obFormulario->defineBarra    ( array( $obBtnIncluirLote, $obBtnLimparLote ), "left", "" );
$obFormulario->addSpan        ( $obSpnLotes           );

if ($stAcao == "incluir") {
    //EXPLICAÇÃO DO MOTIVO PARA A NÃO UTILIZAÇÃO DO MÉTODO OK está NA DEFINIÇÃO DOS BOTÕES
    $obFormulario->defineBarra( array( $obOk, $obLimpar ) );
} else {
    $obFormulario->Cancelar();
}

$obFormulario->setFormFocus( $obTxtNomCondominio->getId() );
$obFormulario->show  ();
?>
