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
    * Página de formulário para o cadastro de edificação
    * Data de Criação   : 17/11/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
    * @author Desenvolvedor: Fábio Bertoldi Rodrigues

    * @ignore

    * $Id: FMManterEdificacaoVinculo.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.11
*/

/*
$Log$
Revision 1.22  2006/09/18 10:30:30  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMEdificacao.class.php"     );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMTipoEdificacao.class.php" );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMConfiguracao.class.php"   );

//Define o nome dos arquivos PHP
$stPrograma    = "ManterEdificacao";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgForm        = "FM".$stPrograma.".php";
$pgFormVinculo = "FM".$stPrograma."Vinculo.php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJs          = "JS".$stPrograma.".js";
include_once( $pgJs );

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

// DEFINE OBJETOS DAS CLASSES
$obRCIMEdificacao     = new RCIMEdificacao;
$obRCIMTipoEdificacao = new RCIMTipoEdificacao;
$obRCIMConfiguracao   = new RCIMConfiguracao;
$rsTipoEdificacao     = new RecordSet;
$rsAtributos          = new RecordSet;

$obRCIMConfiguracao->consultarConfiguracao();
$obRCIMConfiguracao->setCodigoModulo( 12 );
$obRCIMConfiguracao->setAnoExercicio( Sessao::getExercicio() );
$obRCIMConfiguracao->consultarConfiguracao();
$stMascaraIM = $obRCIMConfiguracao->getMascaraIM();
$obRCIMConfiguracao->consultarMascaraProcesso( $stMascaraProcesso );

if ($stAcao == "alterar") {
    $obRCIMEdificacao = new RCIMEdificacao;
    $obRCIMEdificacao->obRCadastroDinamico->setChavePersistenteValores( array( "cod_tipo" => $_REQUEST["inCodigoTipo"] ) );
    $obRCIMEdificacao->obRCadastroDinamico->recuperaAtributosSelecionados( $rsAtributosEdifcicacao );

    $obMontaAtributosEdificacao = new MontaAtributos;
    $obMontaAtributosEdificacao->setTitulo     ( "Atributos"              );
    $obMontaAtributosEdificacao->setName       ( "AtributoEdificacao_"    );
    $obMontaAtributosEdificacao->setRecordSet  ( $rsAtributosEdifcicacao );
}

// Preenche RecordSet
$obRCIMTipoEdificacao->listarTiposEdificacao( $rsTipoEdificacao );
//echo "Num Linhas".$rsTipoEdificacao[]
// OBJETOS HIDDEN
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName                      ( "stCtrl"                      );
$obHdnCtrl->setValue                     ( $_REQUEST['stCtrl']           );

$obHdnAcao = new Hidden;
$obHdnAcao->setName                      ( "stAcao"                      );
$obHdnAcao->setValue                     ( $_REQUEST['stAcao']           );

//HIDDEN PARA A VALIDACAO DOS ATRIBUTOS NA INCLUSÃO
$obHdnEval = new HiddenEval;
$obHdnEval->setName( "stEval" );
$obHdnEval->setValue( "" );

$obHdnCodigoConstrucao = new Hidden;
$obHdnCodigoConstrucao->setName          ( "hdnCodigoConstrucao"         );
$obHdnCodigoConstrucao->setValue         ( $_REQUEST['inCodigoConstrucao']           );

$obHdnCodigoTipo = new Hidden;
$obHdnCodigoTipo->setName                ( "hdnCodigoTipo"               );
$obHdnCodigoTipo->setValue               ( $_REQUEST['inCodigoTipo']                 );

$obHdnCodigoTipoAutonoma = new Hidden;
$obHdnCodigoTipoAutonoma->setName                ( "hdnCodigoTipoAutonoma"               );
$obHdnCodigoTipoAutonoma->setValue               ( $_REQUEST["inCodigoTipoAutonoma"]     );

$obHdnImovelCond = new Hidden;
$obHdnImovelCond->setName                ( "hdnImovelCond"               );
$obHdnImovelCond->setValue               ( $_REQUEST['stImovelCond']                 );

$obStImovelCond = new Hidden;
$obStImovelCond->setName                 ( "stImovelCond"                );
$obStImovelCond->setValue                ( $_REQUEST['stImovelCond']                 );

$obHdnNumero = new Hidden;
$obHdnNumero->setName                    ( "stNumero"                     );
$obHdnNumero->setValue                   ( $_REQUEST['stNumero']                      );

$obHdnAreaConstruida = new Hidden;
$obHdnAreaConstruida->setName            ( "hdnAreaConstruida"            );
$obHdnAreaConstruida->setValue           ( $_REQUEST['flAreaConstruida']              );

$obHdnAreaUnidade = new Hidden;
$obHdnAreaUnidade->setName            ( "hdnAreaUnidade"                );
$obHdnAreaUnidade->setValue           ( $_REQUEST["flAreaUnidade"]      );

$obHdnAreaTotal = new Hidden;
$obHdnAreaTotal->setName                 ( "hdnAreaTotal"                 );
$obHdnAreaTotal->setValue                ( $_REQUEST['flAreaTotal']                   );

$obHdnAreaTotalOriginal = new Hidden;
$obHdnAreaTotalOriginal->setName         ( "hdnAreaTotalOriginal"         );
$obHdnAreaTotalOriginal->setValue        ( $_REQUEST['flAreaTotal']       );

$obHdnComplemento = new Hidden;
$obHdnComplemento->setName               ( "stComplemento"                );
$obHdnComplemento->setValue              ( $_REQUEST['stComplemento']                 );

$obHdnCodigoConstrucaoAutonoma = new Hidden;
$obHdnCodigoConstrucaoAutonoma->setName  ( "hdnCodigoConstrucaoAutonoma" );
$obHdnCodigoConstrucaoAutonoma->setValue ( $_REQUEST['inCodigoConstrucaoAutonoma']  );

//$obHdnCodigoTipoAutonoma = new Hidden;
//$obHdnCodigoTipoAutonoma->setName        ( "hdnCodigoTipoAutonoma"       );
//$obHdnCodigoTipoAutonoma->setValue       ( $hdnCodigoTipoAutonoma        );

$obHdnTipoUnidade = new Hidden;
$obHdnTipoUnidade->setName               ( "hdnTipoUnidade"              );
$obHdnTipoUnidade->setValue              ( $_REQUEST['stTipoUnidade']               );

$obHdnVinculoEdificacao = new Hidden;
$obHdnVinculoEdificacao->setName         ( "hdnVinculoEdificacao"        );
$obHdnVinculoEdificacao->setValue        ( $_REQUEST['boVinculoEdificacao']          );

$obHdnAdicionarEdificacao = new Hidden;
$obHdnAdicionarEdificacao->setName       ( "hdnAdicionarEdificacao"      );
$obHdnAdicionarEdificacao->setValue      ( $_REQUEST['boAdicionarEdificacao']        );

$obHdnListaDependentes = new Hidden;
$obHdnListaDependentes->setName         ( "hdnListaDependentes"         );
$obHdnListaDependentes->setValue        ( false                         );

$obHdnDtConstrucao = new Hidden;
$obHdnDtConstrucao->setName             ( "stDtConstrucao"               );
$obHdnDtConstrucao->setValue            ( $_REQUEST['stDtConstrucao']                );

// DEFINE OBJETOS DO FORMULARIO - INCLUIR

// LABELS PARA ALTERACAO, BAIXA E HISTORICO
$obLblCodigoEdificacao = new Label;
$obLblCodigoEdificacao->setRotulo      ( "Código"                            );
$obLblCodigoEdificacao->setName        ( "inCodigoConstrucao"                );
$obLblCodigoEdificacao->setValue       ( $_REQUEST["inCodigoConstrucao"]     );

$obLblTipoEdificacao = new Label;
$obLblTipoEdificacao->setRotulo        ( "Tipo de Edificação"                );
$obLblTipoEdificacao->setName          ( "stTipoEdificacao"                  );
$obLblTipoEdificacao->setValue         ( $_REQUEST["stTipoEdificacao"]       );

$obLblNomeCondominio = new Label;
$obLblNomeCondominio->setRotulo        ( "Condomínio"                        );
$obLblNomeCondominio->setName          ( "stImovelCond"                      );
$obLblNomeCondominio->setValue         ( $_REQUEST["stImovelCond"]           );

$obLblInscricaoImobiliaria = new Label;
$obLblInscricaoImobiliaria->setRotulo  ( "Inscrição Imobiliária"             );
$obLblInscricaoImobiliaria->setName    ( "stImovelCond"                      );
$obLblInscricaoImobiliaria->setValue   ( $_REQUEST["stImovelCond"]           );

$obLblTipoUnidade = new Label;
$obLblTipoUnidade->setRotulo           ( "Tipo de unidade"                   );
$obLblTipoUnidade->setName             ( "stTipoUnidade"                     );
$obLblTipoUnidade->setID               ( "stTipoUnidade"                     );
$obLblTipoUnidade->setValue            ( $_REQUEST["stTipoUnidade"]          );

$obBscCondominio = new BuscaInner;
$obBscCondominio->setRotulo              ( "Condomínio"                              );
$obBscCondominio->setTitle               ( "Condomínio com o qual a construção está vinculada" );
$obBscCondominio->setNull                ( true                                      );
$obBscCondominio->setId                  ( "campoInnerCond"                          );
$obBscCondominio->obCampoCod->setName    ( "stImovelCond"                            );
$obBscCondominio->obCampoCod->setValue   ( $_REQUEST["inCodigoCondominio"]           );
$obBscCondominio->obCampoCod->obEvento->setOnChange("buscaValor('buscaCondominio');" );
$obBscCondominio->setFuncaoBusca("abrePopUp('../../popups/condominio/FLProcurarCondominio.php','frm','stImovelCond'
                           ,'campoInnerCond','','".Sessao::getId()."','800','550')" );

$obBscInscricaoMunicipal = new BuscaInner;
$obBscInscricaoMunicipal->setNull             ( false );
$obBscInscricaoMunicipal->setRotulo           ( "Inscrição Imobiliária" );
$obBscInscricaoMunicipal->setTitle            ( "Inscrição imobiliária com a qual a edificação está vinculada" );
$obBscInscricaoMunicipal->obCampoCod->setName ( "stImovelCond" );
$obBscInscricaoMunicipal->obCampoCod->setId   ( "stImovelCond" );
$obBscInscricaoMunicipal->obCampoCod->obEvento->setOnChange ( "BloqueiaFrames(true,false);verificaUnidadeAutonoma();" );
$obBscInscricaoMunicipal->obCampoCod->obEvento->setOnBlur   ( "verificaUnidadeAutonomaOnBlur();" );
$obBscInscricaoMunicipal->obCampoCod->setValue( $_REQUEST["inInscImovel"] );
$obBscInscricaoMunicipal->obCampoCod->setSize ( strlen($stMascaraIM) );
$obBscInscricaoMunicipal->obCampoCod->setMaxLength ( strlen($stMascaraIM) );
$obBscInscricaoMunicipal->setFuncaoBusca      ( "abrePopUp('../../popups/imovel/FLProcurarImovel.php','frm','stImovelCond','stNumeroDomicilio','todos','".Sessao::getId()."','800','550');");

$obLblTipoUnidade = new Label;
$obLblTipoUnidade->setRotulo           ( "Tipo de Unidade"                   );
$obLblTipoUnidade->setId               ( "stTipoUnidade"                     );
$obLblTipoUnidade->setValue            ( $_REQUEST['stTipoUnidade']          );

$obTxtTipoEdificacao = new TextBox;
$obTxtTipoEdificacao->setRotulo        ( "Tipo de Edificação"                );
$obTxtTipoEdificacao->setName          ( "inCodigoTipo"                      );
$obTxtTipoEdificacao->setValue         ( $_REQUEST["inCodigoTipo"]           );
$obTxtTipoEdificacao->setSize          ( 8                                   );
$obTxtTipoEdificacao->setMaxLength     ( 8                                   );
$obTxtTipoEdificacao->setNull          ( false                               );
$obTxtTipoEdificacao->setInteiro       ( true                                );
$obTxtTipoEdificacao->obEvento->setOnChange("montaAtributosEdificacao();");

$obCmbTipoEdificacao = new Select;
$obCmbTipoEdificacao->setRotulo        ( "Tipo de Edificação"             );
$obCmbTipoEdificacao->setName          ( "cmbTipoEdificacao"              );
$obCmbTipoEdificacao->addOption        ( "", "Selecione"                  );
$obCmbTipoEdificacao->setCampoId       ( "cod_tipo"                       );
$obCmbTipoEdificacao->setCampoDesc     ( "nom_tipo"                       );
$obCmbTipoEdificacao->preencheCombo    ( $rsTipoEdificacao                );
$obCmbTipoEdificacao->setValue         ( $_REQUEST["inCodigoTipo"]        );
$obCmbTipoEdificacao->setNull          ( false                            );
$obCmbTipoEdificacao->setStyle         ( "width: 220px"                   );
$obCmbTipoEdificacao->obEvento->setOnChange("montaAtributosEdificacao();" );

$obDtConstrucao = new Data;
$obDtConstrucao->setName     ( "stDtConstrucao" );
$obDtConstrucao->setRotulo   ( "Data de Edificação" );
$obDtConstrucao->setTitle    ( 'Data de construção da edificação' );
$dtdiaHOJE = date ("d/m/Y");
if ($_REQUEST["data_construcao"])
    $obDtConstrucao->setValue    (  $_REQUEST["stDtConstrucao"]    );
else
    $obDtConstrucao->setValue    ( $dtdiaHOJE );

$obDtConstrucao->setNull     ( false );
$obDtConstrucao->obEvento->setOnChange( "validaDataConstrucao();" );

$obLblAreaTotalEdificada = new Label;
$obLblAreaTotalEdificada->setRotulo( "Área Total Edificada"             );
$obLblAreaTotalEdificada->setName  ( "flAreaTotalEdificada"             );
$obLblAreaTotalEdificada->setID    ( "flAreaTotalEdificada"             );
$obLblAreaTotalEdificada->setValue ( $_REQUEST['flAreaTotal']           );

$obLblDTInicio = new Label;
$obLblDTInicio->setRotulo( "Data da Baixa"              );
$obLblDTInicio->setName  ( "stDTInicio"                 );
$obLblDTInicio->setValue ( $_REQUEST["stDTInicio"]      );

$obLblDTConstrucao = new Label;
$obLblDTConstrucao->setRotulo( "Data de Construção"         );
$obLblDTConstrucao->setName  ( "stDtConstrucao"             );
$obLblDTConstrucao->setValue ( $_REQUEST["stDtConstrucao"]  );

$obTxtAreaConstruida = new Numerico;
$obTxtAreaConstruida->setRotulo        ( "Área da Edificação"          );
$obTxtAreaConstruida->setName          ( "flAreaConstruida"            );
$obTxtAreaConstruida->setValue         ( $_REQUEST["flAreaConstruida"] );
$obTxtAreaConstruida->setMaxValue      ( 999999999999.99               );
$obTxtAreaConstruida->setSize          ( 18                            );
$obTxtAreaConstruida->setMaxLength     ( 18                            );
$obTxtAreaConstruida->setNull          ( false                         );
$obTxtAreaConstruida->setNegativo      ( false                         );
$obTxtAreaConstruida->setNaoZero       ( true                          );
if ($inCodigoConstrucao) {
    $obTxtAreaConstruida->setReadOnly  ( true                          );
}

$obTxtAreaUnidade = new Numerico;
$obTxtAreaUnidade->setRotulo             ( "Área da Unidade"          );
$obTxtAreaUnidade->setName               ( "flAreaUnidade"            );
$obTxtAreaUnidade->setId                 ( "flAreaUnidade"            );
$obTxtAreaUnidade->setValue              ( $_REQUEST["flAreaUnidade"] );
$obTxtAreaUnidade->setMaxValue           ( 999999999999.99            );
$obTxtAreaUnidade->setSize               ( 18                         );
$obTxtAreaUnidade->setMaxLength          ( 18                         );
$obTxtAreaUnidade->setNull               ( false                      );
$obTxtAreaUnidade->setNegativo           ( false                      );
$obTxtAreaUnidade->setNaoZero            ( true                       );
$obTxtAreaUnidade->obEvento->setOnChange ( "buscaValor('calculaAreaTotal');" );

$obSpnUnidadesDependentes = new Span;
$obSpnUnidadesDependentes->setId("spnUnidadesDependentes");

$obTxtJustificativa = new TextArea;
if ($stAcao == "reativar") {
    $obTxtJustificativa->setRotulo         ( "Motivo da Reativação" );
    $obTxtJustificativa->setTitle          ( "Motivo da reativação" );
    $obTxtJustificativa->setName           ( "stJustReat" );
} else {
    $obTxtJustificativa->setRotulo         ( "Motivo da Baixa" );
    $obTxtJustificativa->setTitle          ( "Motivo da baixa" );
    $obTxtJustificativa->setName           ( "stJustificativa" );
}
$obTxtJustificativa->setNull           ( false             );

$obLblJustificativa = new Label;
$obLblJustificativa->setRotulo( "Motivo da Baixa"                 );
$obLblJustificativa->setName  ( "stJustificativa"                 );
$obLblJustificativa->setValue ( $_REQUEST["stJustificativa"]      );

$obHdnJustificativa = new Hidden;
$obHdnJustificativa->setName   ( "stJustificativa"                );
$obHdnJustificativa->setValue  ( $_REQUEST["stJustificativa"]     );

$obHdnTimestamp = new Hidden;
$obHdnTimestamp->setName   ( "stTimestamp"                );
$obHdnTimestamp->setValue  ( $_REQUEST["stTimestamp"]     );

$obSpnAtributosEdificacao = new Span;
$obSpnAtributosEdificacao->setId( "lsAtributosEdificacao" );

$obBscProcesso = new BuscaInner;
$obBscProcesso->setRotulo ( "Processo" );
$obBscProcesso->setTitle ( "Número do processo no protocolo que gerou a aprovação do loteamento" );
$obBscProcesso->setNull ( true );
$obBscProcesso->obCampoCod->setName ("inProcesso");
$obBscProcesso->obCampoCod->setId   ("inProcesso");
if( $_REQUEST['inCodigoProcesso'])
    $obBscProcesso->obCampoCod->setValue( $_REQUEST['inCodigoProcesso'].'/'.$_REQUEST['hdnAnoExercicioProcesso'] );

$obBscProcesso->obCampoCod->obEvento->setOnChange( "buscaValor('buscaProcesso');" );
$obBscProcesso->obCampoCod->obEvento->setOnKeyUp( "mascaraDinamico('".$stMascaraProcesso."', this, event);" );
$obBscProcesso->setFuncaoBusca( "abrePopUp('".CAM_GA_PROT_POPUPS."processo/FLBuscaProcessos.php','frm','inProcesso','campoInner2','','".Sessao::getId()."','800','550')" );

$obRadioEdificacao = new Radio;
$obRadioEdificacao->setName     ( "stProximaPagina"                             );
$obRadioEdificacao->setRotulo   ( " &nbsp; "                                    );
$obRadioEdificacao->setValue    ( "edificacao"                                  );
$obRadioEdificacao->setLabel    ( "Incluir nova edificação"                     );
//$obRadioEdificacao->setNull     ( false                                         );
$obRadioEdificacao->setChecked  ( true                                          );

$obRadioUnidade = new Radio;
$obRadioUnidade->setName        ( "stProximaPagina"                             );
$obRadioUnidade->setRotulo      ( " &nbsp; "                                );
$obRadioUnidade->setValue       ( "unidade"                                     );
$obRadioUnidade->setLabel       ( "Incluir nova unidade na mesma edificação"    );
//$obRadioUnidade->setNull        ( false                                         );
$obRadioUnidade->setChecked     ( false                                         );

$obRadioConstrucao = new Radio;
$obRadioConstrucao->setName     ( "stProximaPagina"                             );
$obRadioConstrucao->setRotulo   ( " &nbsp; "                                    );
$obRadioConstrucao->setValue    ( "construcao"                                  );
$obRadioConstrucao->setLabel    ( "Seguir para o cadastro de construção"        );
//$obRadioConstrucao->setNull     ( false                                         );
$obRadioConstrucao->setChecked  ( false                                         );

$obBtnOK = new OK;

$obBtnLimparCond = new Button;
$obBtnLimparCond->setName              ( "btnLimparCond" );
$obBtnLimparCond->setValue             ( "Limpar"        );
$obBtnLimparCond->obEvento->setOnClick ( "LimparCond();" );

$obBtnLimparInsc = new Button;
$obBtnLimparInsc->setName              ( "btnLimparInsc" );
$obBtnLimparInsc->setValue             ( "Limpar"        );
$obBtnLimparInsc->obEvento->setOnClick ( "LimparInsc();" );

$obHdnCampoNumDom = new Hidden;
$obHdnCampoNumDom->setName( "stNumeroDomicilio" );
$obHdnCampoNumDom->setID  ( "stNumeroDomicilio" );

//DEFINICAO DO FORM

$obForm = new Form;
$obForm->setAction ( $pgProc  );
$obForm->setTarget ( "oculto" );

//DEFINICAO DO FORMULARIO
$obFormulario = new FormularioAbas;
$obFormulario->addForm   ( $obForm                        );
$obFormulario->setAjuda ( "UC-05.01.11" );
$obFormulario->addHidden ( $obHdnCtrl                     );
$obFormulario->addHidden ( $obHdnAcao                     );
$obFormulario->addHidden ( $obHdnCampoNumDom              );
$obFormulario->addHidden ( $obHdnCodigoConstrucao         );
$obFormulario->addHidden ( $obHdnCodigoTipo               );
$obFormulario->addHidden ( $obHdnCodigoTipoAutonoma       );
$obFormulario->addHidden ( $obHdnCodigoConstrucaoAutonoma );
$obFormulario->addHidden ( $obHdnTipoUnidade              );
$obFormulario->addHidden ( $obHdnVinculoEdificacao        );
$obFormulario->addHidden ( $obHdnAdicionarEdificacao      );
$obFormulario->addHidden ( $obHdnAreaConstruida           );
$obFormulario->addHidden ( $obHdnAreaUnidade              );
$obFormulario->addHidden ( $obHdnAreaTotal                );
$obFormulario->addHidden ( $obHdnAreaTotalOriginal        );
$obFormulario->addHidden ( $obHdnListaDependentes         );
$obFormulario->addHidden ( $obHdnEval, true               );
if ( ($stAcao != "baixar") && ($stAcao != "reativar") ) {
    $obFormulario->addAba( "Edificação" );
    $obFormulario->addFuncaoAba ( "atualizaComponente();" );
} else {
    $obFormulario->addHidden( $obHdnDtConstrucao );
}
$obFormulario->addTitulo   ( "Dados para edificação" );

if ($stAcao == "incluir") {
    $obFormulario->addHidden                 ( $obHdnImovelCond                           );
    if ($_REQUEST["boMesma"] == "true") {
        $obFormulario->addComponente( $obLblCodigoEdificacao );
    }
    if ($_REQUEST["boVinculoEdificacao"] == "Condomínio") {
        $obFormulario->addComponente         ( $obBscCondominio                           );
        $obFormulario->addComponenteComposto ( $obTxtTipoEdificacao, $obCmbTipoEdificacao );
        $obFormulario->addComponente         ( $obDtConstrucao                            );
        $obFormulario->addComponente         ( $obTxtAreaConstruida                       );
        $obFormulario->addComponente         ( $obBscProcesso                             );

    } elseif ($_REQUEST["boVinculoEdificacao"] == "Imóvel") {
        if ($_REQUEST['boAdicionar'] == true) {
            $obFormulario->addComponente     ( $obLblCodigoEdificacao );
        }
        $obFormulario->addComponente         ( $obBscInscricaoMunicipal                   );
        $obFormulario->addComponente         ( $obLblTipoUnidade                          );
        $obFormulario->addComponenteComposto ( $obTxtTipoEdificacao, $obCmbTipoEdificacao );
        $obFormulario->addComponente         ( $obDtConstrucao                            );
        $obFormulario->addComponente         ( $obLblAreaTotalEdificada                   );
        $obFormulario->addComponente         ( $obTxtAreaUnidade                          );
        $obFormulario->addComponente         ( $obBscProcesso                             );

    }
    $obFormulario->addAba( "Características" );
    $obFormulario->addFuncaoAba ( "atualizaComponente();" );
    $obFormulario->addSpan               ( $obSpnAtributosEdificacao );
    $obFormulario->addDiv( 4, "componente" );
    $obFormulario->addComponente         ( $obRadioEdificacao                         );

    $obFormulario->addComponente         ( $obRadioUnidade                            );

//verificar se existe permissao
$rsPermissao = new RecordSet();
include_once( CAM_GA_ADM_MAPEAMENTO."TAdministracaoAcao.class.php" );
$obTAdministracaoAcao = NEW TAdministracaoAcao();
$obTAdministracaoAcao->setDado("cod_acao", 751);
$obTAdministracaoAcao->recuperaPermissao($rsPermissao );
if ( !$rsPermissao->eof() ) {
    $obFormulario->addComponente         ( $obRadioConstrucao                         );
}
    $obFormulario->fechaDiv();

    if ($_REQUEST["boVinculoEdificacao"] == "Imóvel") {
        $obFormulario->defineBarra( array( $obBtnOK, $obBtnLimparInsc ) );
    } else {
        $obFormulario->defineBarra( array( $obBtnOK, $obBtnLimparCond ) );
    }
} elseif ($stAcao == "alterar") {
    $obFormulario->addHidden          ( $obStImovelCond             );
    if ($_REQUEST["boVinculoEdificacao"] == "Condomínio") {
        $obFormulario->addComponente  ( $obLblNomeCondominio        );
    } elseif ($_REQUEST["boVinculoEdificacao"] == "Imóvel") {
        $obFormulario->addComponente  ( $obLblInscricaoImobiliaria  );
        $obFormulario->addComponente  ( $obLblTipoUnidade           );
    }
    if ($_REQUEST["boVinculoEdificacao"] == "Imóvel") {
        $obFormulario->addComponente  ( $obTxtAreaUnidade           );
    }
    $obMontaAtributosEdificacao->geraFormulario ( $obFormulario               );
    $obFormulario->Cancelar ();
} elseif ($stAcao == "reativar") {
    $obFormulario->addHidden          ( $obHdnJustificativa         );
    $obFormulario->addHidden          ( $obHdnTimestamp             );
    $obFormulario->addHidden          ( $obStImovelCond             );
    $obFormulario->addComponente      ( $obLblCodigoEdificacao      );
    $obFormulario->addComponente      ( $obLblTipoEdificacao        );
    if ($_REQUEST["boVinculoEdificacao"] == "Condomínio") {
        $obFormulario->addComponente  ( $obLblNomeCondominio        );
    } elseif ($_REQUEST["boVinculoEdificacao"] == "Imóvel") {
        $obFormulario->addComponente  ( $obLblInscricaoImobiliaria  );
        $obFormulario->addComponente  ( $obLblTipoUnidade           );
    }

    $obFormulario->addComponente      ( $obLblDTConstrucao          );
    $obFormulario->addComponente      ( $obLblDTInicio              );
    $obFormulario->addComponente      ( $obLblJustificativa         );
    $obFormulario->addComponente      ( $obBscProcesso              );
    $obFormulario->addComponente      ( $obTxtJustificativa         );

    $obFormulario->Cancelar ();
} elseif ($stAcao == "baixar") {
    $obFormulario->addHidden          ( $obStImovelCond             );
    $obFormulario->addComponente      ( $obLblCodigoEdificacao      );
    $obFormulario->addComponente      ( $obLblTipoEdificacao        );
    if ($_REQUEST["boVinculoEdificacao"] == "Condomínio") {
        $obFormulario->addComponente  ( $obLblNomeCondominio        );
    } elseif ($_REQUEST["boVinculoEdificacao"] == "Imóvel") {
        $obFormulario->addComponente  ( $obLblInscricaoImobiliaria  );
        $obFormulario->addComponente  ( $obLblTipoUnidade           );
    }

    $obFormulario->addComponente      ( $obLblDTConstrucao          );
    $obFormulario->addComponente      ( $obBscProcesso              );
    $obFormulario->addComponente      ( $obTxtJustificativa         );
    //if ($stTipoUnidade == "Autônoma") {
    //    $obFormulario->addSpan ( $obSpnUnidadesDependentes    );
    //}
    $obFormulario->Cancelar ();

} elseif ($stAcao == "historico") {
    $obFormulario->addHidden          ( $obStImovelCond             );
    $obFormulario->addComponente      ( $obLblCodigoEdificacao      );
    $obFormulario->addComponente      ( $obLblTipoEdificacao        );
    if ($_REQUEST["boVinculoEdificacao"] == "Condomínio") {
        $obFormulario->addComponente  ( $obLblNomeCondominio        );
    } elseif ($_REQUEST["boVinculoEdificacao"] == "Imóvel") {
        $obFormulario->addComponente  ( $obLblInscricaoImobiliaria  );
        $obFormulario->addComponente  ( $obLblTipoUnidade           );
    }
    $obMontaAtributos->geraFormulario ( $obFormulario               );
    $obFormulario->Cancelar ();

}
if ($stAcao == "incluir") {
    $obFormulario->setFormFocus( $obBscInscricaoMunicipal->obCampoCod->getid() );
    if ($_REQUEST['boVinculoEdificacao'] == "Condomínio") {
        $js .= "buscaValor('buscaCondominio');";
    }
    SistemaLegado::executaFramePrincipal($js);
} elseif ($stAcao == "alterar") {
    $obFormulario->setFormFocus( $obTxtAreaUnidade->getid() );
    if ($_REQUEST["stTipoUnidade"] == "Autônoma") {
        SistemaLegado::executaFramePrincipal("habilitaSpnTotalEdificacao();");
    }
} elseif ($stAcao == "baixar") {
    $obFormulario->setFormFocus( $obBscProcesso->obCampoCod->getid() );
    //SistemaLegado::executaFramePrincipal("habilitaSpnUnidadesDependentes();");
}

$obFormulario->show();
?>
