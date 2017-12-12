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
    * Formulario para Licença - Especial
    * Data de Criação   : 04/12/2004
    * @author Analista: Ricardo Lopes
    * @author Desenvolvedor: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Lizandro Kirst da Silva
    * @package URBEM
    * @subpackage Regra

    * $Id: FMConcederLicencaEspecial.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.02.12

*/

/*
$Log$
Revision 1.13  2006/10/17 16:13:41  dibueno
Utilização de componentes para BuscaInners e adição do campo para observação

Revision 1.12  2006/09/15 14:33:14  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CEM_NEGOCIO."RCEMLicencaEspecial.class.php" );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMConfiguracao.class.php"    );
include_once ( CAM_GT_CEM_COMPONENTES."MontaAtividade.class.php"  );
include_once ( CAM_GA_ADM_COMPONENTES."ITextBoxSelectDocumento.class.php" );
include_once ( CAM_GA_PROT_CLASSES."componentes/IPopUpProcesso.class.php" );

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
    $stAcao = "incEsp";
}

$obRCEMConfiguracao = new RCEMConfiguracao;
$obRCEMConfiguracao->setCodigoModulo( 14 );
$obRCEMConfiguracao->setAnoExercicio( Sessao::getExercicio() );
$obRCEMConfiguracao->consultarConfiguracao();

$obRCEMLicencaEspecial = new RCEMLicencaEspecial;
$arConfiguracao = array();
$obRCEMLicencaEspecial->recuperaConfiguracao( $arConfiguracao);

// OBJETOS HIDDEN
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName                    ( "stCtrl"                          );
$obHdnCtrl->setValue                   ( $request->get("stCtrl")           );

$obHdnAcao = new Hidden;
$obHdnAcao->setName                    ( "stAcao"                          );
$obHdnAcao->setValue                   ( $request->get("stAcao")           );

$obHdnEspecieLicenca = new Hidden;
$obHdnEspecieLicenca->setName          ( "stEspecieLicenca"                );
$obHdnEspecieLicenca->setValue         ( "Especial"                        );

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
$obLblProcesso->setRotulo              ( "Processo"                        );
$obLblProcesso->setName                ( "inCodigoProcesso"                );
$obLblProcesso->setValue               ( $request->get("inCodigoProcesso")     );

//COMPONENTES PARA INCLUSAO E ALTERACAO
$obTxtCodigoLicenca = new TextBox;
$obTxtCodigoLicenca->setRotulo         ( "Número da Licença"               );
$obTxtCodigoLicenca->setTitle          ( "Número da Licença"               );
$obTxtCodigoLicenca->setName           ( "inCodigoLicenca"                 );
$obTxtCodigoLicenca->setValue          ( $request->get("inCodigoLicenca")      );
$obTxtCodigoLicenca->setSize           ( strlen( $arConfiguracao['mascara_licenca']) );
$obTxtCodigoLicenca->setMaxLength      ( strlen( $arConfiguracao['mascara_licenca']) );
$obTxtCodigoLicenca->setNull           ( false                             );
$obTxtCodigoLicenca->setInteiro        ( false                             );
$obTxtCodigoLicenca->obEvento->setOnKeyUp("mascaraDinamico('".$arConfiguracao['mascara_licenca']."', this, event);");

$obRCEMLicencaEspecial->obRCEMConfiguracao->consultarConfiguracao();
$obNumeroInscricao  = $obRCEMLicencaEspecial->obRCEMConfiguracao->getNumeroInscricao();
$stMascaraInscricao = $obRCEMLicencaEspecial->obRCEMConfiguracao->getMascaraInscricao();

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
$obTxtObservacao->setValue 	( $request->get("stObservacao") );

$obCBoxEmissaoDocumento = new CheckBox;
$obCBoxEmissaoDocumento->setName 	( "boEmissaoDocumento" 	);
$obCBoxEmissaoDocumento->setLabel	( "Impressão Local"		);
$obCBoxEmissaoDocumento->setRotulo	( "Emissão de Alvará"	);

//documentos
$obITextBoxSelectDocumento = new ITextBoxSelectDocumento;
$obITextBoxSelectDocumento->setCodAcao( Sessao::read('acao') );
$obITextBoxSelectDocumento->setCodModeloDocumento ( $request->get("inCodAlvara") );
$obITextBoxSelectDocumento->obTextBoxSelectDocumento->setRotulo ( "*Modelo do Alvará" );
$obITextBoxSelectDocumento->obTextBoxSelectDocumento->setNULL ( true );

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
$obDtDataTermino->setValue             ( $request->get("dtDataTermino")        );
$obDtDataTermino->setRotulo            ( "Data de Término"                 );
$obDtDataTermino->setMaxLength         ( 20                                );
$obDtDataTermino->setSize              ( 10                                );
$obDtDataTermino->setNull              ( false                             );
$obDtDataTermino->obEvento->setOnChange( "validaDataLicenca();"            );

$obCheckDomingo = new CheckBox;
$obCheckDomingo->setName               ( "dia1"                            );
$obCheckDomingo->setRotulo             ( "Dia da Semana"                   );
$obCheckDomingo->setValue              ( "1"                               );
$obCheckDomingo->setLabel              ( "Domingo"                         );
$obCheckDomingo->setNull               ( true                              );

$obCheckSegunda = new CheckBox;
$obCheckSegunda->setName               ( "dia2"                            );
$obCheckSegunda->setRotulo             ( "Dia da Semana"                   );
$obCheckSegunda->setValue              ( "2"                               );
$obCheckSegunda->setLabel              ( "Segunda-feira"                   );
$obCheckSegunda->setNull               ( true                              );

$obCheckTerca = new CheckBox;
$obCheckTerca->setName                 ( "dia3"                            );
$obCheckTerca->setRotulo               ( "Dia da Semana"                   );
$obCheckTerca->setValue                ( "3"                               );
$obCheckTerca->setLabel                ( "Terça-feira"                     );
$obCheckTerca->setNull                 ( true                              );

$obCheckQuarta = new CheckBox;
$obCheckQuarta->setName                ( "dia4"                            );
$obCheckQuarta->setRotulo              ( "Dia da Semana"                   );
$obCheckQuarta->setValue               ( "4"                               );
$obCheckQuarta->setLabel               ( "Quarta-feira"                    );
$obCheckQuarta->setNull                ( true                              );

$obCheckQuinta = new CheckBox;
$obCheckQuinta->setName                ( "dia5"                            );
$obCheckQuinta->setRotulo              ( "Dia da Semana"                   );
$obCheckQuinta->setValue               ( "5"                               );
$obCheckQuinta->setLabel               ( "Quinta-feira"                    );
$obCheckQuinta->setNull                ( true                              );

$obCheckSexta = new CheckBox;
$obCheckSexta->setName                 ( "dia6"                            );
$obCheckSexta->setRotulo               ( "Dia da Semana"                   );
$obCheckSexta->setValue                ( "6"                               );
$obCheckSexta->setLabel                ( "Sexta-feira"                     );
$obCheckSexta->setNull                 ( true                              );

$obCheckSabado = new CheckBox;
$obCheckSabado->setName                ( "dia7"                            );
$obCheckSabado->setRotulo              ( "Dia da Semana"                   );
$obCheckSabado->setValue               ( "7"                               );
$obCheckSabado->setLabel               ( "Sábado"                          );
$obCheckSabado->setNull                ( true                              );

$obHrHorarioInicio = new Hora;
$obHrHorarioInicio->setName            ( "hrHorarioInicio"                 );
$obHrHorarioInicio->setValue           ( $request->get("hrHorarioInicio")  );
$obHrHorarioInicio->setRotulo          ( "*Horário de Início"              );
$obHrHorarioInicio->setMaxLength       ( 20                                );
$obHrHorarioInicio->setSize            ( 10                                );
$obHrHorarioInicio->setNull            ( true                              );

$obHrHorarioTermino = new Hora;
$obHrHorarioTermino->setName           ( "hrHorarioTermino"                );
$obHrHorarioTermino->setValue          ( $request->get("hrHorarioTermino")    );
$obHrHorarioTermino->setRotulo         ( "*Horário de Término"             );
$obHrHorarioTermino->setMaxLength      ( 20                                );
$obHrHorarioTermino->setSize           ( 10                                );
$obHrHorarioTermino->setNull           ( true                              );

// DEFINE BOTOES
$obBtnIncluirDias = new Button;
$obBtnIncluirDias->setName              ( "btnIncluirDias"      );
$obBtnIncluirDias->setValue             ( "Incluir"             );
$obBtnIncluirDias->setTipo              ( "button"              );
$obBtnIncluirDias->obEvento->setOnClick ( "incluirHorario();"   );
$obBtnIncluirDias->setDisabled          ( false                 );

$obBtnLimparDias = new Button;
$obBtnLimparDias->setName               ( "btnLimparDias"       );
$obBtnLimparDias->setValue              ( "Limpar"              );
$obBtnLimparDias->setTipo               ( "button"              );
$obBtnLimparDias->obEvento->setOnClick  ( "limparHorario();"    );
$obBtnLimparDias->setDisabled           ( false                 );

$obBtnIncluirAtiv = new Button;
$obBtnIncluirAtiv->setName              ( "btnIncluirAtiv"      );
$obBtnIncluirAtiv->setValue             ( "Incluir"             );
$obBtnIncluirAtiv->setTipo              ( "button"              );
$obBtnIncluirAtiv->obEvento->setOnClick ( "incluirAtividade();" );
$obBtnIncluirAtiv->setDisabled          ( false                 );

$obBtnLimparAtiv = new Button;
$obBtnLimparAtiv->setName               ( "btnLimparAtiv"       );
$obBtnLimparAtiv->setValue              ( "Limpar"              );
$obBtnLimparAtiv->setTipo               ( "button"              );
$obBtnLimparAtiv->obEvento->setOnClick  ( "limparAtividade();"  );
$obBtnLimparAtiv->setDisabled           ( false                 );

$obBtnOK = new Ok;

$obBtnClean = new Button;
$obBtnClean->setName                    ( "btnClean"            );
$obBtnClean->setValue                   ( "Limpar"              );
$obBtnClean->setTipo                    ( "button"              );
$obBtnClean->obEvento->setOnClick       ( "limparFormularioEspecial();" );
$obBtnClean->setDisabled                ( false                 );

$botoesSpanDias = array ( $obBtnIncluirDias , $obBtnLimparDias );
$botoesForm     = array ( $obBtnOK          , $obBtnClean      );
$botoesSpanAtiv = array ( $obBtnIncluirAtiv , $obBtnLimparAtiv );

$obSpnListaHorario = new Span;
$obSpnListaHorario->setId("spnListaHorario"  );

$obSpnListaAtividade = new Span;
$obSpnListaAtividade->setId("spnListaAtividade");

$obSpnMontaAtividade = new Span;
$obSpnMontaAtividade->setId("spnMontaAtividade");
$obSpnMontaAtividade->setValue ("Aguardando Inscrição Economica");

$obCmbAtividade = new Select;
$obCmbAtividade->setRotulo    ( "Atividade"     );
$obCmbAtividade->setName      ( "cmbAtividade"  );
$obCmbAtividade->setId        ( "cmbAtividade"  );
$obCmbAtividade->addOption    ( "", "Selecione Atividade"   );
$obCmbAtividade->setCampoId   ( "cod_atividade" );
$obCmbAtividade->setCampoDesc ( "[cod_estrutural] - [nom_atividade]" );
$obCmbAtividade->setStyle     ( "width:250px"     );

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction ( $pgProc  );
$obForm->setTarget ( "oculto" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm     ( $obForm                 );
$obFormulario->setAjuda      ( "UC-05.02.12");
$obFormulario->addHidden   ( $obHdnCtrl              );
$obFormulario->addHidden   ( $obHdnAcao              );
$obFormulario->addHidden   ( $obHdnEspecieLicenca    );
$obFormulario->addHidden   ( $obHdnExercicioProcesso );
$obFormulario->addHidden   ( $obHdnNumeroLicenca     );
$obFormulario->addHidden   ( $obHdnMascaraLicenca    );

$obFormulario->addTitulo   ( "Dados para Licença" );

if ($stAcao == "incEsp") {
    if ($arConfiguracao[ 'numero_licenca' ] == 1) {
        $obFormulario->addComponente ( $obTxtCodigoLicenca      );
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
    // monta atividade
    $obFormulario->addComponente ( $obCmbAtividade          );
    $obFormulario->defineBarra   ( $botoesSpanAtiv,'left','' );
    $obFormulario->addSpan       ( $obSpnListaAtividade     );
    $obFormulario->addTitulo     ( "Horário de Exercício das Atividades" );
    $obFormulario->addComponente ( $obCheckDomingo          );
    $obFormulario->addComponente ( $obCheckSegunda          );
    $obFormulario->addComponente ( $obCheckTerca            );
    $obFormulario->addComponente ( $obCheckQuarta           );
    $obFormulario->addComponente ( $obCheckQuinta           );
    $obFormulario->addComponente ( $obCheckSexta            );
    $obFormulario->addComponente ( $obCheckSabado           );
    $obFormulario->addComponente ( $obHrHorarioInicio       );
    $obFormulario->addComponente ( $obHrHorarioTermino      );
    $obFormulario->defineBarra  ( $botoesSpanDias,'left','' );
    $obFormulario->addSpan       ( $obSpnListaHorario       );

    $obITextBoxSelectDocumento->geraFormulario ( $obFormulario );
    $obFormulario->addComponente ( $obCBoxEmissaoDocumento	);

    $obFormulario->defineBarra   ( $botoesForm,'left',''    );

} elseif ($stAcao == "especial") {
    $obFormulario->addHidden     ( $obHdnCodigoLicenca      );
    $obFormulario->addHidden     ( $obHdnInscricaoEconomica );
    $obFormulario->addComponente ( $obLblCodigoLicenca      );
    $obFormulario->addComponente ( $obLblInscricaoEconomica );
    $obFormulario->addComponente ( $obLblProcesso           );
    $obFormulario->addTitulo     ( "Validade da Licença"    );
    $obFormulario->addComponente ( $obDtDataInicio          );
    $obFormulario->addComponente ( $obDtDataTermino         );
    $obFormulario->addTitulo     ( "Atividades"             );
//    $obMontaAtividade->geraFormulario ( $obFormulario       );
    $obFormulario->addComponente ( $obCmbAtividade          );
    $obFormulario->defineBarra  ( $botoesSpanAtiv,'left','' );
    $obFormulario->addSpan       ( $obSpnListaAtividade     );
    $obFormulario->addTitulo     ( "Horário de Exercício das Atividades" );
    $obFormulario->addComponente ( $obCheckDomingo          );
    $obFormulario->addComponente ( $obCheckSegunda          );
    $obFormulario->addComponente ( $obCheckTerca            );
    $obFormulario->addComponente ( $obCheckQuarta           );
    $obFormulario->addComponente ( $obCheckQuinta           );
    $obFormulario->addComponente ( $obCheckSexta            );
    $obFormulario->addComponente ( $obCheckSabado           );
    $obFormulario->addComponente ( $obHrHorarioInicio       );
    $obFormulario->addComponente ( $obHrHorarioTermino      );
    $obFormulario->defineBarra  ( $botoesSpanDias,'left','' );
    $obFormulario->addSpan       ( $obSpnListaHorario       );
    $obITextBoxSelectDocumento->geraFormulario ( $obFormulario );
    $obFormulario->addComponente ( $obCBoxEmissaoDocumento	);
    $obFormulario->Cancelar();
}
$obFormulario->show();

if ($stAcao == "incEsp") {
    sistemaLegado::executaFrameOculto("document.frm.inInscricaoEconomica.focus();");
} else {
    $obMontaAtividade = new MontaAtividade;
    $obMontaAtividade->setInscricaoEconomica($_REQUEST["inInscricaoEconomica"]);
    $obMontaAtividade->geraFormularioRestrito($js,"cmbAtividade");
    $js .= "recuperaAtividades();\n";
    $js .= "document.frm.dtDataInicio.focus();\n";

    sistemaLegado::executaFrameOculto($js);
}

?>
