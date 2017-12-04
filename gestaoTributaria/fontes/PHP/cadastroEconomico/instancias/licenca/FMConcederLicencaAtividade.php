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
    * Formulario para Licença - Atividade
    * Data de Criação   : 03/12/2004
    * @author Analista: Ricardo Lopes
    * @author Desenvolvedor: Fábio Bertoldi Rodrigues
    * @package URBEM
    * @subpackage Regra

    * $Id: FMConcederLicencaAtividade.php 63390 2015-08-24 19:17:05Z arthur $

    * Casos de uso: uc-05.02.12

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CEM_NEGOCIO."RCEMLicencaAtividade.class.php" );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMConfiguracao.class.php" );
include_once ( CAM_GT_CEM_COMPONENTES."MontaAtividade.class.php"   );
include_once ( CAM_GA_ADM_COMPONENTES."ITextBoxSelectDocumento.class.php" );
include_once ( CAM_GA_PROT_CLASSES."componentes/IPopUpProcesso.class.php" );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMLicencaAtividade.class.php"   );

//Define o nome dos arquivos PHP
$stPrograma      = "ManterLicenca";
$pgFilt          = "FL".$stPrograma.".php";
$pgFiltAlterar   = "FLAlterarLicenca.php";
$pgList          = "LS".$stPrograma.".php";
$pgForm          = "FM".$stPrograma.".php";
$pgFormAtividade = "FMConcederLicencaAtividade.php";
$pgFormEspecial  = "FMConcederLicencaEspecial.php";
$pgProc          = "PR".$stPrograma.".php";
$pgOcul          = "OC".$stPrograma.".php";
$pgJs            = "JS".$stPrograma.".js";

include_once( $pgJs );

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incAtiv";
}

// LIMPAR
Sessao::write( "atividades"	, array() );
Sessao::write( "lsElementos", array() );
Sessao::write( "horarios", array() );
Sessao::write( "lsElementos", array() );

$obRCEMConfiguracao = new RCEMConfiguracao;
$obRCEMConfiguracao->setCodigoModulo( 14 );
$obRCEMConfiguracao->setAnoExercicio( Sessao::getExercicio() );
$obRCEMConfiguracao->consultarConfiguracao();

// DEFINE OBJETOS DAS CLASSES
$obRCEMLicencaAtividade = new RCEMLicencaAtividade;
$obMontaAtividade       = new MontaAtividade;
$obMontaAtividade->setCadastroAtividade( false );

$arConfiguracao = array();
$obRCEMLicencaAtividade->recuperaConfiguracao( $arConfiguracao );

// OBJETOS HIDDEN
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName                    ( "stCtrl"                          );
$obHdnCtrl->setValue                   ( $request->get("stCtrl")           );

$obHdnAcao = new Hidden;
$obHdnAcao->setName                    ( "stAcao"                          );
$obHdnAcao->setValue                   ( $request->get("stAcao")           );

$obHdnEspecieLicenca = new Hidden;
$obHdnEspecieLicenca->setName          ( "stEspecieLicenca"                );
$obHdnEspecieLicenca->setValue         ( "Atividade"                       );

$obHdnCodigoLicenca = new Hidden;
$obHdnCodigoLicenca->setName           ( "inCodigoLicenca"                 );
$obHdnCodigoLicenca->setValue          ( $request->get("inCodigoLicenca")."/".$request->get("stExercicio") );

$obHdnInscricaoEconomica = new Hidden;
$obHdnInscricaoEconomica->setName      ( "inInscricaoEconomica"            );
$obHdnInscricaoEconomica->setValue     ( $request->get("inInscricaoEconomica") );

$obHdnExercicioProcesso = new Hidden;
$obHdnExercicioProcesso->setName       ( "hdnExercicioProcesso"            );
$obHdnExercicioProcesso->setValue      ( $request->get("stExercicioProcesso"));

$obHdnNumeroLicenca = new Hidden;
$obHdnNumeroLicenca->setName           ( "hdnNumeroLicenca"                );
$obHdnNumeroLicenca->setValue          ( $arConfiguracao['numero_licenca']   );

$obHdnMascaraLicenca = new Hidden;
$obHdnMascaraLicenca->setName          ( "hdnMascaraLicenca"               );
$obHdnMascaraLicenca->setValue         ( $arConfiguracao['mascara_licenca']  );

// DEFINE OBJETOS DO FORMULARIO - INCLUIR
// LABELS PARA ALTERACAO
$obLblCodigoLicenca = new Label;
$obLblCodigoLicenca->setRotulo         ( "Número da Licença"               );
$obLblCodigoLicenca->setName           ( "lblCodigoLicenca"                );
if ($arConfiguracao['numero_licenca'] == 2) {
    $obLblCodigoLicenca->setValue          ( $request->get("inCodigoLicenca")."/".$request->get("stExercicio") );
} else {
    $obLblCodigoLicenca->setValue          ( $request->get("inCodigoLicenca") );
}

$obLblInscricaoEconomica = new Label;
$obLblInscricaoEconomica->setRotulo    ( "Inscrição Econômica"             );
$obLblInscricaoEconomica->setName      ( "inInscricaoEconomica"            );
$obLblInscricaoEconomica->setValue     ( $request->get("inInscricaoEconomica") );

$obLblProcesso = new Label;
$obLblProcesso->setRotulo             ( "Processo"                         );
$obLblProcesso->setName               ( "inCodigoProcesso"                 );
$obLblProcesso->setValue              ( $request->get("inCodigoProcesso")  );

$inOcorrenciaLicencaTMP = '';
$stObservacao = '';
if ($stAcao == "atividade") {
    $obTCEMLicencaAtividade   = new TCEMLicencaAtividade;
    //$obTCEMLicenca   = new TCEMLicenca;
    $IE = $_REQUEST["inInscricaoEconomica"];
    $stFiltro = " WHERE LCA.inscricao_economica = ". $IE;
    $stOrdem  = " ORDER BY LCA.ocorrencia_licenca DESC limit 1 ";
    $obTCEMLicencaAtividade->recuperaRelacionamento ($rsLicenca,$stFiltro,$stOrdem);
    if ( $rsLicenca->getNumLinhas() < 1 ) {
        $inOcorrenciaLicencaTMP = 1 ;
    } else {
        $inOcorrenciaLicencaTMP = $rsLicenca->getCampo('ocorrencia_licenca');
        $stObservacao = $rsLicenca->getCampo ('observacao');
    }
}

$obHdnOcorrenciaLicenca = new Hidden;
$obHdnOcorrenciaLicenca->setName  ( "inOcorrenciaLicenca" );
$obHdnOcorrenciaLicenca->setValue ( $inOcorrenciaLicencaTMP );

//COMPONENTES PARA INCLUSAO E ALTERACAO
$obTxtNumeroLicenca = new TextBox;
$obTxtNumeroLicenca->setRotulo         ( "Número da Licença"               );
$obTxtNumeroLicenca->setTitle          ( "Número da Licença"               );
$obTxtNumeroLicenca->setName           ( "inNumeroLicenca"                 );
$obTxtNumeroLicenca->setValue          ( $request->get("inCodigoLicenca")  );
$obTxtNumeroLicenca->setSize           ( strlen( $arConfiguracao['mascara_licenca']) );
$obTxtNumeroLicenca->setMaxLength      ( strlen( $arConfiguracao['mascara_licenca']) );
$obTxtNumeroLicenca->setNull           ( true                              );
$obTxtNumeroLicenca->setInteiro        ( false                             );
$obTxtNumeroLicenca->obEvento->setOnKeyUp("mascaraDinamico('".$arConfiguracao['mascara_licenca']."', this, event);");

$obRCEMLicencaAtividade->obRCEMConfiguracao->consultarConfiguracao();
$obNumeroInscricao  = $obRCEMLicencaAtividade->obRCEMConfiguracao->getNumeroInscricao();
$stMascaraInscricao = $obRCEMLicencaAtividade->obRCEMConfiguracao->getMascaraInscricao();

$obBscInscricaoEconomica = new BuscaInner;
$obBscInscricaoEconomica->setNull            ( false                                            );
$obBscInscricaoEconomica->setRotulo          ( "Inscrição Econômica"                            );
$obBscInscricaoEconomica->setTitle           ( "Pessoa física ou jurídica cadastrada como inscrição econômica");
$obBscInscricaoEconomica->setId              ( "stInscricaoEconomica"                           );
$obBscInscricaoEconomica->setName            ( "stInscricaoEconomica"                           );
$obBscInscricaoEconomica->obCampoCod->setName( "inInscricaoEconomica"                           );
$obBscInscricaoEconomica->obCampoCod->setSize( strlen($stMascaraInscricao)                      );
$obBscInscricaoEconomica->obCampoCod->setMaxLength ( strlen($stMascaraInscricao)                );
$obBscInscricaoEconomica->obCampoCod->setMascara ( $stMascaraInscricao                          );
$obBscInscricaoEconomica->obCampoCod->obEvento->setOnChange( "buscaValor('buscaInscricao');"    );
$obBscInscricaoEconomica->obCampoCod->obEvento->setOnBlur( "buscaValor('buscaInscricao');"    );
$obBscInscricaoEconomica->setFuncaoBusca("abrePopUp('".CAM_GT_CEM_POPUPS."inscricaoeconomica/FLProcurarInscricaoEconomica.php','frm','inInscricaoEconomica','stInscricaoEconomica','todos','".Sessao::getId()."','800','550');");

$obTxtObservacao = new TextArea;
$obTxtObservacao->setName  	("stObservacao");
$obTxtObservacao->setRotulo	("Observações");
$obTxtObservacao->setTitle	("Observações referentes à licença");
$obTxtObservacao->setValue 	( $stObservacao );

$obCBoxEmissaoDocumento = new CheckBox;
$obCBoxEmissaoDocumento->setName 	( "boEmissaoDocumento" 	);
$obCBoxEmissaoDocumento->setLabel	( "Impressão Local"		);
$obCBoxEmissaoDocumento->setRotulo	( "Emissão de Alvará"	);

//documentos
$obITextBoxSelectDocumento = new ITextBoxSelectDocumento;
$obITextBoxSelectDocumento->setCodAcao( Sessao::read('acao') );
$obITextBoxSelectDocumento->obTextBoxSelectDocumento->setRotulo ( "Modelo do Alvará" );
$obITextBoxSelectDocumento->obTextBoxSelectDocumento->setNULL ( false );

$obDtDataInicio = new Data;
$obDtDataInicio->setName               ( "dtDataInicio"                    );
$obDtDataInicio->setValue              ( $request->get("dtDataInicio")     );
$obDtDataInicio->setRotulo             ( "Data de Início"                  );
$obDtDataInicio->setMaxLength          ( 20                                );
$obDtDataInicio->setSize               ( 10                                );
$obDtDataInicio->setNull               ( false                             );
$obDtDataInicio->obEvento->setOnChange ( "validaData1500( this );"         );

$obDtDataTermino = new Data;
$obDtDataTermino->setName              ( "dtDataTermino"                   );
$obDtDataTermino->setValue             ( $request->get("dtDataTermino")    );
$obDtDataTermino->setRotulo            ( "Data de Término"                 );
$obDtDataTermino->setMaxLength         ( 20                                );
$obDtDataTermino->setSize              ( 10                                );
$obDtDataTermino->setNull              ( true                              );
$obDtDataTermino->obEvento->setOnChange( "validaDataLicenca();"            );

$obBtnIncluirAtiv = new Button;
$obBtnIncluirAtiv->setName              ( "btnIncluirAtiv"      );
$obBtnIncluirAtiv->setValue             ( "Incluir"             );
$obBtnIncluirAtiv->setTipo              ( "button"              );
$obBtnIncluirAtiv->obEvento->setOnClick ( "incluirAtividade();" );
$obBtnIncluirAtiv->setDisabled          ( false                 );

$obBtnOK = new Ok;

$obBtnClean = new Button;
$obBtnClean->setName                    ( "btnClean"            );
$obBtnClean->setValue                   ( "Limpar"              );
$obBtnClean->setTipo                    ( "button"              );
$obBtnClean->obEvento->setOnClick       ( "limparFormularioAtividade();" );
$obBtnClean->setDisabled                ( false                 );

$botoesForm     = array ( $obBtnOK          , $obBtnClean      );
$botoesSpanAtiv = array ( $obBtnIncluirAtiv );

$obSpnListaAtividade = new Span;
$obSpnListaAtividade->setID("spnListaAtividade");

$obCmbAtividade = new Select;
$obCmbAtividade->setRotulo    ( "*Atividade"    );
$obCmbAtividade->setName      ( "cmbAtividade"  );
$obCmbAtividade->setId        ( "cmbAtividade"  );
$obCmbAtividade->addOption    ( "", "Selecione Atividade"   );
$obCmbAtividade->setCampoId   ( "cod_atividade" );
$obCmbAtividade->setCampoDesc ( "[cod_estrutural] - [nom_atividade]" );

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction ( $pgProc  );
$obForm->setTarget ( "oculto" );
//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm     ( $obForm                 );
$obFormulario->setAjuda    ( "uc-5.2.12"             );
$obFormulario->addHidden   ( $obHdnCtrl              );
$obFormulario->addHidden   ( $obHdnAcao              );
$obFormulario->addHidden   ( $obHdnEspecieLicenca    );
$obFormulario->addHidden   ( $obHdnExercicioProcesso );
$obFormulario->addHidden   ( $obHdnNumeroLicenca     );
$obFormulario->addHidden   ( $obHdnMascaraLicenca    );
$obFormulario->addHidden   ( $obHdnOcorrenciaLicenca );

$obFormulario->addTitulo   ( "Dados para Licença" );

if ($stAcao == "incAtiv") {
    if ($arConfiguracao['numero_licenca'] == 1) {
        $obFormulario->addComponente ( $obTxtNumeroLicenca      );
    }
    $obFormulario->addComponente ( $obBscInscricaoEconomica );

    $obPopUpProcesso = new IPopUpProcesso ( $obForm );
    $obPopUpProcesso->setNull ( true );
    $obPopUpProcesso->obCampoCod->setName("inCodigoProcesso");
    $obPopUpProcesso->montaHTML ( );
    $obFormulario->addComponente ( $obPopUpProcesso );

    $obFormulario->addComponente ( $obTxtObservacao			);
    $obFormulario->addTitulo     ( "Validade da Licença"    );
    $obFormulario->addComponente ( $obDtDataInicio          );
    $obFormulario->addComponente ( $obDtDataTermino         );
    $obFormulario->addTitulo     ( "Atividades"             );
    $obFormulario->addComponente ( $obCmbAtividade          );
    $obFormulario->defineBarra  ( $botoesSpanAtiv,'left','' );
    $obFormulario->addSpan       ( $obSpnListaAtividade     );

    $obITextBoxSelectDocumento->geraFormulario ( $obFormulario );
    $obFormulario->addComponente ( $obCBoxEmissaoDocumento	);

    $obFormulario->defineBarra   ( $botoesForm,'left',''    );

} elseif ($stAcao == "atividade") {
    $obFormulario->addHidden     ( $obHdnCodigoLicenca      );
    $obFormulario->addHidden     ( $obHdnInscricaoEconomica );
    $obFormulario->addComponente ( $obLblCodigoLicenca      );
    $obFormulario->addComponente ( $obLblInscricaoEconomica );
    $obFormulario->addComponente ( $obLblProcesso           );
    $obFormulario->addComponente ( $obTxtObservacao			);
    $obFormulario->addTitulo     ( "Validade da Licença"    );
    $obFormulario->addComponente ( $obDtDataInicio          );
    $obFormulario->addComponente ( $obDtDataTermino         );
    $obFormulario->addTitulo     ( "Atividades"             );
    $obFormulario->addComponente ( $obCmbAtividade          );
    $obFormulario->defineBarra  ( $botoesSpanAtiv,'left','' );
    $obFormulario->addSpan       ( $obSpnListaAtividade     );

    $obITextBoxSelectDocumento->geraFormulario ( $obFormulario );
    $obFormulario->addComponente ( $obCBoxEmissaoDocumento	);

    $obFormulario->Cancelar();
}

$obFormulario->show();

if ($stAcao == "incAtiv") {
    sistemaLegado::executaFrameOculto("document.frm.inInscricaoEconomica.focus();");
} else {
    $obMontaAtividade = new MontaAtividade;
    $obMontaAtividade->setInscricaoEconomica( $request->get("inInscricaoEconomica"));
    $obMontaAtividade->geraFormularioRestrito($js,"cmbAtividade");
    $js .= "recuperaAtividades();\n";

    sistemaLegado::executaFrameOculto($js);
}

?>