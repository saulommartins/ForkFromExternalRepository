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
    * Página de formulário para a alteração de edificação
    * Data de Criação   : 17/11/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
    * @author Desenvolvedor: Fábio Bertoldi Rodrigues

    * @ignore

    * $Id: FMManterEdificacaoAlteracao.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.11
*/

/*
$Log$
Revision 1.10  2006/09/18 10:30:30  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMEdificacao.class.php"   );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMConfiguracao.class.php" );

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

if ( empty( $_REQUEST['stAcao'] ) ) {
    $_REQUEST['stAcao'] = "alterar";
}
// DEFINE OBJETOS DAS CLASSES
$obRCIMEdificacao     = new RCIMEdificacao;
$obRCIMConfiguracao   = new RCIMConfiguracao;
$rsAtributos          = new RecordSet;

$obRCIMConfiguracao->setCodigoModulo( 12 );
$obRCIMConfiguracao->setAnoExercicio( Sessao::getExercicio() );
$obRCIMConfiguracao->consultarConfiguracao();
$stMascaraIM = $obRCIMConfiguracao->getMascaraIM();
$obRCIMConfiguracao->consultarMascaraProcesso( $stMascaraProcesso );

//RECUPERA E MONTA OS ATRIBUTOS DA EDIFICAÇÃO SELECIONADA
$arChaveAtributo = array( "cod_tipo" => $_REQUEST["inCodigoTipo"], "cod_construcao" => $_REQUEST["inCodigoConstrucao"] );
$obRCIMEdificacao->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributo );
$obRCIMEdificacao->obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributosEdificacao );

$obMontaAtributosEdificacao = new MontaAtributos;
$obMontaAtributosEdificacao->setTitulo     ( "Atributos"              );
$obMontaAtributosEdificacao->setName       ( "AtributoEdificacao_"    );
$obMontaAtributosEdificacao->setLabel      ( true );
$obMontaAtributosEdificacao->setRecordSet  ( $rsAtributosEdificacao );

if ( strtolower( $_REQUEST['stTipoUnidade'] ) == "dependente" ) {
    $obRCIMUnidadeAutonoma = new RCIMUnidadeAutonoma( new RCIMImovel( new RCIMLote) );
    $obRCIMUnidadeAutonoma->roRCIMImovel->setNumeroInscricao( $_REQUEST["stImovelCond"]);
    $obRCIMUnidadeAutonoma->verificaUnidadeAutonoma( $rsUnidadeAutonoma );
    if ( !$rsUnidadeAutonoma->eof() ) {
        $hdnCodigoConstrucaoAutonoma = $rsUnidadeAutonoma->getCampo( "cod_construcao" );
        $hdnCodigoTipoAutonoma       = $rsUnidadeAutonoma->getCampo( "cod_tipo"       );
    }
} elseif ( strtolower( $_REQUEST['stTipoUnidade'] ) == "autônoma" ) {
    $obRCIMUnidadeAutonoma = new RCIMUnidadeAutonoma( new RCIMImovel( new RCIMLote) );
    $obRCIMUnidadeAutonoma->obRCIMEdificacao->setCodigoConstrucao   ( $_REQUEST["inCodigoConstrucao"]   ) ;
    $obRCIMUnidadeAutonoma->obRCIMEdificacao->setCodigoTipo         ( $_REQUEST["inCodigoTipo"]         ) ;
    $obRCIMUnidadeAutonoma->roRCIMImovel->setNumeroInscricao        ( $_REQUEST["stImovelCond"]         ) ;

    $obRCIMUnidadeAutonoma->consultarTimestampUnidadeAutonoma();
    $tmTimestampUnidadeAutonoma = $obRCIMUnidadeAutonoma->getTimestampUnidadeAutonoma();
}

// recebe varaiveis de timestamp do processo(se haver) para comparação
// se timestamp do processo for igual a timestamp do imovel,
// este processo é o de inclusao, se nao for igual
// liberar campo de processo e inseri-lo com o mesmo timestamp do imovel
$rsProcessos = new Recordset;
$obRCIMEdificacao->setCodigoConstrucao($inCodigoConstrucao);
$obRCIMEdificacao->listarProcessos($rsProcessos);
$tmTimestampProcesso = $rsProcessos->getCampo("timestamp")  ;

//$obRCIMEdificacao->consultarEdificacao();
$tmTimestampEdificacao = $obRCIMEdificacao->getTimestampConstrucao()  ;
$boTimestampIgual      = $tmTimestampProcesso == $tmTimestampImovel;
// fim das variaveis de timestamp

// Preenche RecordSet
//$obRCIMTipoEdificacao->listarTiposEdificacao( $rsTipoEdificacao );

// OBJETOS HIDDEN
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName                      ( "stCtrl"                      );
$obHdnCtrl->setValue                     ( $_REQUEST['stCtrl']                       );

$obHdnAcao = new Hidden;
$obHdnAcao->setName                      ( "stAcao"                      );
$obHdnAcao->setValue                     ( $_REQUEST['stAcao']                       );

$obHdnCodigoConstrucao = new Hidden;
$obHdnCodigoConstrucao->setName          ( "hdnCodigoConstrucao"         );
$obHdnCodigoConstrucao->setValue         ( $_REQUEST['inCodigoConstrucao'] );

$obHdnAreaTotal = new Hidden;
$obHdnAreaTotal->setName                 ( "hdnAreaTotal"                    );
$obHdnAreaTotal->setValue                ( $_REQUEST['flAreaTotalEdificada'] );

$obHdnAreaTotalOriginal = new Hidden;
$obHdnAreaTotalOriginal->setName         ( "hdnAreaTotalOriginal"            );
$obHdnAreaTotalOriginal->setValue        ( $_REQUEST['flAreaTotalEdificada'] );

$obHdnUnidadeOriginal = new Hidden;
$obHdnUnidadeOriginal->setName           ( "hdnUnidadeOriginal"          );
$obHdnUnidadeOriginal->setValue          ( $_REQUEST['flAreaUnidade']    );

$obHdnCodigoTipo = new Hidden;
$obHdnCodigoTipo->setName                ( "hdnCodigoTipo"               );
$obHdnCodigoTipo->setValue               ( $_REQUEST['inCodigoTipo']                 );

$obHdnImovelCond = new Hidden;
$obHdnImovelCond->setName                ( "hdnImovelCond"               );
$obHdnImovelCond->setValue               ( $_REQUEST['stImovelCond']        );

$obStImovelCond = new Hidden;
$obStImovelCond->setName                 ( "stImovelCond"                );
$obStImovelCond->setValue                ( $_REQUEST['stImovelCond']                 );

$obHdnCodigoConstrucaoAutonoma = new Hidden;
$obHdnCodigoConstrucaoAutonoma->setName  ( "hdnCodigoConstrucaoAutonoma" );
$obHdnCodigoConstrucaoAutonoma->setValue ( $hdnCodigoConstrucaoAutonoma  );

$obHdnCodigoTipoAutonoma = new Hidden;
$obHdnCodigoTipoAutonoma->setName        ( "hdnCodigoTipoAutonoma"       );
$obHdnCodigoTipoAutonoma->setValue       ( $hdnCodigoTipoAutonoma        );

$obHdnTipoUnidade = new Hidden;
$obHdnTipoUnidade->setName               ( "hdnTipoUnidade"              );
$obHdnTipoUnidade->setValue              ( $_REQUEST['stTipoUnidade']    );

$obHdnVinculoEdificacao = new Hidden;
$obHdnVinculoEdificacao->setName         ( "hdnVinculoEdificacao"        );
$obHdnVinculoEdificacao->setId           ( "hdnVinculoEdificacao"        );
$obHdnVinculoEdificacao->setValue        ( $_REQUEST['boVinculoEdificacao']          );

$obHdnTimestamp = new Hidden;
$obHdnTimestamp->setName                 ( "hdnTimestamp"                );
$obHdnTimestamp->setValue                ( $tmTimestampEdificacao        );

$obHdnTimestampUnidadeAutonoma = new Hidden;
$obHdnTimestampUnidadeAutonoma->setName  ( "hdnTimestampUnidadeAutonoma" );
$obHdnTimestampUnidadeAutonoma->setValue ( $tmTimestampUnidadeAutonoma   );

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
$obLblTipoUnidade->setValue            ( $_REQUEST["stTipoUnidade"]          );

$obTxtCondominio = new TextBox;
$obTxtCondominio->setRotulo             ( "Condomínio"                       );
$obTxtCondominio->setTitle              ( "Condomínio com o qual a edificação está vinculada" );
$obTxtCondominio->setName               ( "stImovelCond"                     );
$obTxtCondominio->setValue              ( $_REQUEST["stImovelCond"]          );
$obTxtCondominio->setSize               ( 8                                  );
$obTxtCondominio->setMaxLength          ( 8                                  );
$obTxtCondominio->setNull               ( false                              );
$obTxtCondominio->setInteiro            ( true                               );

$obTxtInscricaoMunicipal = new TextBox;
$obTxtInscricaoMunicipal->setRotulo             ( "Inscrição Imobiliária"    );
$obTxtInscricaoMunicipal->setTitle              ( "Inscrição imobiliária com a qual a edificação está vinculada" );
$obTxtInscricaoMunicipal->setName               ( "stImovelCond"             );
$obTxtInscricaoMunicipal->setValue              ( $_REQUEST["stImovelCond"]  );
$obTxtInscricaoMunicipal->setSize               ( strlen($stMascaraIM)       );
$obTxtInscricaoMunicipal->setMaxLength          ( strlen($stMascaraIM)       );
$obTxtInscricaoMunicipal->setNull               ( false                      );
$obTxtInscricaoMunicipal->setInteiro            ( true                       );
$obTxtInscricaoMunicipal->obEvento->setOnChange ( "verificaUnidadeAutonoma();" );

$obLblTipoUnidade = new Label;
$obLblTipoUnidade->setRotulo           ( "Tipo de Unidade"                   );
$obLblTipoUnidade->setId               ( "stTipoUnidade"                     );
$obLblTipoUnidade->setValue            ( $_REQUEST['stTipoUnidade']                      );

$obDtDataConstrucao= new data;
$obDtDataConstrucao->setRotulo        ( "Data da Edificação"          );
$obDtDataConstrucao->setName          ( "stDtConstrucao"            );
$obDtDataConstrucao->setId            ( "stDtConstrucao"              );
$obDtDataConstrucao->setValue         ( $_REQUEST["stDtConstrucao"]   );
$obDtDataConstrucao->setNull          ( false                         );
$obDtDataConstrucao->obEvento->setOnChange( "validaDataConstrucao();" );

$obTxtAreaTotalEdificacao = new Numerico;
$obTxtAreaTotalEdificacao->setRotulo        ( "Área Total Edificada"          );
$obTxtAreaTotalEdificacao->setName          ( "flAreaTotalEdificada"            );
$obTxtAreaTotalEdificacao->setValue         ( $_REQUEST["flAreaTotalEdificada"] );
$obTxtAreaTotalEdificacao->setMaxValue      ( 999999999999.99               );
$obTxtAreaTotalEdificacao->setSize          ( 18                            );
$obTxtAreaTotalEdificacao->setMaxLength     ( 18                            );
$obTxtAreaTotalEdificacao->setNull          ( false                         );
$obTxtAreaTotalEdificacao->setNegativo      ( false                         );
$obTxtAreaTotalEdificacao->setNaoZero       ( true                          );
$obTxtAreaTotalEdificacao->obEvento->setOnChange ( "verificaAreaUnidade();" );

$obLblAreaTotalEdificada = new Label;
$obLblAreaTotalEdificada->setRotulo( "Área Total Edificada"             );
$obLblAreaTotalEdificada->setName  ( "flAreaTotalEdificada"             );
$obLblAreaTotalEdificada->setID    ( "flAreaTotalEdificada"             );
$obLblAreaTotalEdificada->setValue ( $_REQUEST["flAreaTotalEdificada"]  );

//$flAreaConstruida = number_format( $_REQUEST["flAreaConstruida"], 2, ',', '.');
$obTxtAreaConstruida = new Numerico;
$obTxtAreaConstruida->setRotulo        ( "Área da Edificação"          );
$obTxtAreaConstruida->setName          ( "flAreaConstruida"            );
$obTxtAreaConstruida->setId            ( "flAreaConstruida"            );
$obTxtAreaConstruida->setValue         ( $_REQUEST["flAreaConstruida"] );
$obTxtAreaConstruida->setMaxValue      ( 999999999999.99               );
$obTxtAreaConstruida->setSize          ( 18                            );
$obTxtAreaConstruida->setMaxLength     ( 18                            );
$obTxtAreaConstruida->setNull          ( false                         );
$obTxtAreaConstruida->setNegativo      ( false                         );
$obTxtAreaConstruida->setNaoZero       ( true                          );
$obTxtAreaConstruida->obEvento->setOnChange ( "verificaAreaUnidade();" );

//$flAreaUnidade = number_format( $_REQUEST["flAreaUnidade"], 2, ',', '.');
$obTxtAreaUnidade = new Numerico;
$obTxtAreaUnidade->setRotulo             ( "Área da Unidade"      );
$obTxtAreaUnidade->setName               ( "flAreaUnidade"        );
$obTxtAreaUnidade->setValue              ( $_REQUEST['flAreaUnidade']  );
$obTxtAreaUnidade->setMaxValue           ( 999999999999.99        );
$obTxtAreaUnidade->setSize               ( 18                     );
$obTxtAreaUnidade->setMaxLength          ( 18                     );
$obTxtAreaUnidade->setNull               ( false                  );
$obTxtAreaUnidade->setNegativo           ( false                  );
$obTxtAreaUnidade->setNaoZero            ( true                   );
$obTxtAreaUnidade->obEvento->setOnChange ( "buscaValor('calculaAreaTotal');" );

if ($_REQUEST["inCodigoProcesso"]) {
    $arProcesso = preg_split( "/[^a-zA-Z0-9]/", $stMascaraProcesso );
    $stProcesso = str_pad( $_REQUEST["inCodigoProcesso"], strlen( $arProcesso[0] ), "0", STR_PAD_LEFT );
    $stSeparador = preg_replace( "/[a-zA-Z0-9]/","", $stMascaraProcesso );
    $stProcesso .= $stSeparador.$_REQUEST["hdnAnoExercicioProcesso"];
}

if ($stProcesso) {
    $lblProcesso = new Label;
    $lblProcesso->setRotulo( "Processo"   );
    $lblProcesso->setName  ( "stProcesso" );
    $lblProcesso->setValue ( $stProcesso  );
} else {
    $obBscProcesso = new BuscaInner;
    $obBscProcesso->setRotulo ( "Processo" );
    $obBscProcesso->setTitle  ( "Processo do protocolo que regulariza esta edificação" );
    $obBscProcesso->obCampoCod->setName ("inCodigoProcesso");
    $obBscProcesso->obCampoCod->setValue( $inCodigoProcesso );
    $obBscProcesso->obCampoCod->obEvento->setOnKeyUp( "mascaraDinamico('".$stMascaraProcesso."', this, event);");
    $obBscProcesso->obCampoCod->setSize ( strlen( $stMascaraProcesso ) );
    $obBscProcesso->obCampoCod->setMaxLength( strlen( $stMascaraProcesso ) );
    $obBscProcesso->setFuncaoBusca( "abrePopUp('".CAM_GA_PROT_POPUPS."processo/FLBuscaProcessos.php','frm','inCodigoProcesso','campoInner2','','".Sessao::getId()."','800','550')" );
}

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
$obFormulario->addHidden ( $obHdnAreaTotal                );
$obFormulario->addHidden ( $obHdnAreaTotalOriginal        );
$obFormulario->addHidden ( $obHdnCodigoConstrucao         );
$obFormulario->addHidden ( $obHdnUnidadeOriginal          );
$obFormulario->addHidden ( $obHdnCodigoTipo               );
$obFormulario->addHidden ( $obHdnCodigoConstrucaoAutonoma );
$obFormulario->addHidden ( $obHdnCodigoTipoAutonoma       );
$obFormulario->addHidden ( $obHdnTipoUnidade              );
$obFormulario->addHidden ( $obHdnVinculoEdificacao        );
$obFormulario->addAba( "Edificação" );
$obFormulario->addTitulo   ( "Dados para edificação" );
$obFormulario->addHidden          ( $obStImovelCond             );
$obFormulario->addComponente      ( $obLblCodigoEdificacao      );
$obFormulario->addComponente      ( $obLblTipoEdificacao        );
if ($_REQUEST["boVinculoEdificacao"] == "Condomínio") {
    $obFormulario->addComponente  ( $obLblNomeCondominio        );
} elseif ($_REQUEST["boVinculoEdificacao"] == "Imóvel") {
    $obFormulario->addComponente  ( $obLblInscricaoImobiliaria  );
    $obFormulario->addComponente  ( $obLblTipoUnidade           );
}
$obFormulario->addComponente      ( $obDtDataConstrucao );
if ( $_REQUEST["boVinculoEdificacao"] == "Condomínio" )
    $obFormulario->addComponente      ( $obTxtAreaConstruida );
if ($_REQUEST["boVinculoEdificacao"] == "Imóvel") {
    $obFormulario->addComponente  ( $obLblAreaTotalEdificada        );
    $obFormulario->addHidden      ( $obHdnTimestampUnidadeAutonoma  );
}
if ( $_REQUEST["boVinculoEdificacao"] == "Imóvel" )
    $obFormulario->addComponente( $obTxtAreaUnidade );
if ($stProcesso != "" && !$boTimestampIgual) {
    $obFormulario->addComponente( $lblProcesso      );
} else {
    $obFormulario->addComponente( $obBscProcesso    );
    $obFormulario->addHidden    ( $obHdnTimestamp   );
}
$obFormulario->addAba( "Características" );
$obMontaAtributosEdificacao->geraFormulario ( $obFormulario );
$obFormulario->Cancelar ();
$obFormulario->setFormFocus( $obTxtAreaConstruida->getId() );
$obFormulario->show ();
?>
