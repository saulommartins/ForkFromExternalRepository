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
    * Formulario para Cadastro Economico >> Conceder licencas Diversas
    * Data de Criação   : 04/04/2008
    * @author Analista      : Fábio Bertoldi Rodrigues
    * @author Desenvolvedor : André Machado
    * @package URBEM
    * @subpackage Regra

    * $Id: FMConcederLicencaGeralTipo.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.02.12

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CEM_NEGOCIO."RCEMLicencaDiversa.class.php"    );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMTipoLicencaDiversa.class.php");
include_once ( CAM_GT_CEM_NEGOCIO."RCEMElemento.class.php"          );
include_once ( CAM_GA_CGM_COMPONENTES."IPopUpCGM.class.php"			);
include_once ( CAM_GA_ADM_COMPONENTES."ITextBoxSelectDocumento.class.php" );
include_once ( CAM_GA_PROT_CLASSES."componentes/IPopUpProcesso.class.php" );

//Define o nome dos arquivos PHP
$stPrograma     = "ConcederLicencaGeralTipo";
$pgFilt         = "FL".$stPrograma.".php"       ;
$pgList         = "LS".$stPrograma.".php"       ;
$pgForm         = "FM".$stPrograma.".php"       ;
$pgFormTipo     = "FM".$stPrograma.".php"   ;
$pgProc         = "PR".$stPrograma.".php"       ;
$pgOcul         = "OC".$stPrograma.".php"       ;
$pgJs           = "JS".$stPrograma.".js"        ;

include_once( $pgJs );

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

Sessao::write( "lsElementos", array() );
Sessao::write( "inNumElementos", 0 );

$inCodAlvara = Sessao::read( "inCodDocumento" );

// instancia objeto para listagem de elementos
$obRCEMElementos = new RCEMElemento($obAtividadeTmp);
$obRCEMElementos->referenciaTipoLicencaDiversa( new RCEMTipoLicencaDiversa);
$obRCEMElementos->roRCEMTipoLicencaDiversa->setCodigoTipoLicencaDiversa($request->get("inCodigoTipoLicenca"));
$obRCEMElementos->listarElementoTipoLicencaDiversa($rsElementosLicencaDiversa);

/* Atributos Dinamicos */
$obRCEMLicencaDiversa = new RCEMLicencaDiversa;
$obRCEMLicencaDiversa->obRCadastroDinamico->setChavePersistenteValores  ( array ( "cod_tipo" => $request->get("inCodigoTipoLicenca") ) );
$obRCEMLicencaDiversa->obRCadastroDinamico->recuperaAtributosSelecionados( $rsAtributosLicencaDiversa );

$obMontaAtributosLicencaDiversa = new MontaAtributos;
$obMontaAtributosLicencaDiversa->setTitulo     ( "Atributos"              );
$obMontaAtributosLicencaDiversa->setName       ( "AtributoLicenca_"    );
$obMontaAtributosLicencaDiversa->setRecordSet  ( $rsAtributosLicencaDiversa );

$arConfiguracao = array();
$obRCEMLicencaDiversa->recuperaConfiguracao( $arConfiguracao );

// OBJETOS HIDDEN
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName                      ( "stCtrl"                      );
$obHdnCtrl->setValue                     ( $request->get("stCtrl")       );

$obHdnAcao = new Hidden;
$obHdnAcao->setName                      ( "stAcao"                      );
$obHdnAcao->setValue                     ( $stAcao                       );

$obHdnCodTipoLicenca = new Hidden;
$obHdnCodTipoLicenca->setName            ( "inCodigoTipoLicenca"            );
$obHdnCodTipoLicenca->setValue           ( $request->get("inCodigoTipoLicenca"));

$obHdnEval = new Hidden;
$obHdnEval->setName( "stEval" );
$obHdnEval->setValue( $request->get("stEval") );

$obHdnNumAtributos = new HiddenEval;
$obHdnNumAtributos->setName     ( "inNumAtributos"  );
$obHdnNumAtributos->setId       ( "inNumAtributos"  );
$obHdnNumAtributos->setValue    ( 0                 );

$obHdnboElemento = new Hidden;
$obHdnboElemento->setId     ( "boElemento" );
$obHdnboElemento->setName   ( "boElemento" );

// SPANS
$obSpnAtributosElemento = new Span;
$obSpnAtributosElemento->setId      ( "spnAtributosElemento"    );

$obSpnListaElementos    = new Span;
$obSpnListaElementos->setId         ("spnListaElementos"        );

//DEFINIÇÃO DOS COMPONENTES
// DADOS PARA LICENÇA

$obRCEMTipoLicenca =  new RCEMTipoLicencaDiversa;
$obRCEMTipoLicenca->setCodigoTipoLicencaDiversa($request->get("inCodigoTipoLicenca"));
$obRCEMTipoLicenca->consultar( $rsTipoLicenca );

$obRCEMTipoLicenca->recuperaTipoModeloDocumento( $rsModeloLicenca );

$obHdnCodDocumento = new Hidden;
$obHdnCodDocumento->setName     ('inCodigoDocumento');
$obHdnCodDocumento->setValue    ( $rsModeloLicenca->getCampo('cod_documento') );

$obHdnCodTipoDocumento = new Hidden;
$obHdnCodTipoDocumento->setName ('inCodigoTipoDocumento');
$obHdnCodTipoDocumento->setValue( $rsModeloLicenca->getCampo('cod_tipo_documento') );

$obHdnNomeDocumento = new Hidden;
$obHdnNomeDocumento->setName ('stNomeDocumento');
$obHdnNomeDocumento->setValue( $rsModeloLicenca->getCampo('nome_documento') );

$obHdnNomeArquivo = new Hidden;
$obHdnNomeArquivo->setName ('stNomeArquivo');
$obHdnNomeArquivo->setValue( $rsModeloLicenca->getCampo('nome_arquivo_agt') );

$obLblTipoLicenca = new Label;
$obLblTipoLicenca->setName      ( "stTipoLicenca"                       );
$obLblTipoLicenca->setValue     ( $rsTipoLicenca->getCampo("cod_tipo")." - ".$rsTipoLicenca->getCampo("nom_tipo") );
$obLblTipoLicenca->setRotulo    ( "Tipo de Licença"                     );

$obTxtCodigoLicenca = new TextBox;
$obTxtCodigoLicenca->setRotulo         ( "Número da Licença"               );
$obTxtCodigoLicenca->setTitle          ( "Número da Licença"               );
$obTxtCodigoLicenca->setName           ( "inCodigoLicenca"                 );
$obTxtCodigoLicenca->setValue          ( $request->get("inCodigoLicenca")  );
$obTxtCodigoLicenca->setSize           ( strlen( $arConfiguracao['mascara_licenca']) );
$obTxtCodigoLicenca->setMaxLength      ( strlen( $arConfiguracao['mascara_licenca']) );
$obTxtCodigoLicenca->setNull           ( true                              );
$obTxtCodigoLicenca->setInteiro        ( false                             );
$obTxtCodigoLicenca->obEvento->setOnKeyUp("mascaraDinamico('".$arConfiguracao['mascara_licenca']."', this, event);");

//Observação
$obTxtObservacao = new TextArea;
$obTxtObservacao->setName   ("stObservacao");
$obTxtObservacao->setRotulo ("Observações");
$obTxtObservacao->setTitle  ("Observações referentes à licença");
$obTxtObservacao->setValue  ( $request->get("stObservacao") );

// VALIDADE DA LICENÇA
$obDtDataInicio = new Data;
$obDtDataInicio->setName                ( "dtDataInicio"            );
$obDtDataInicio->setId                  ( "dtDataInicio"            );
$obDtDataInicio->setRotulo              ( "Data de Início"          );
$obDtDataInicio->setNull                ( false                     );
$obDtDataInicio->setMaxLength           ( 20                        );
$obDtDataInicio->setSize                ( 10                        );
$obDtDataInicio->setValue               ( $request->get("dtDataInicio"));
$obDtDataInicio->obEvento->setOnChange  ( "validaData1500( this );" );

$obDtDataTermino = new Data;
$obDtDataTermino->setName               ( "dtDataTermino"           );
$obDtDataTermino->setId                 ( "dtDataTermino"           );
$obDtDataTermino->setRotulo             ( "Data de Término"        );
$obDtDataTermino->setNull               ( true                      );
$obDtDataTermino->setMaxLength          ( 20                        );
$obDtDataTermino->setSize               ( 10                        );
$obDtDataTermino->setValue              ( $request->get("dtDataTermino"));
$obDtDataTermino->obEvento->setOnChange ( "validaDataLicenca();"    );

// ABA ****** ELEMENTOS PARA BASE DE CALCULO
$obTxtElementos = new TextBox;
$obTxtElementos->setName  ( "stCodigoElemento"  );
$obTxtElementos->setId    ( "stCodigoElemento"  );
$obTxtElementos->setTitle ( "*Elemento"         );
$obTxtElementos->setRotulo( "*Elemento"         );

$obCmbElementos  = new Select;
$obCmbElementos->setName         ( "cmbElementos"                   );
$obCmbElementos->setId           ( "cmbElementos"                   );
$obCmbElementos->addOption       ( "", "Selecione"                  );
$obCmbElementos->setTitle        ( "Tipo de Licença"                );
$obCmbElementos->setCampoId      ( "cod_elemento"                   );
$obCmbElementos->setCampoDesc    ( "nom_elemento"                   );
$obCmbElementos->preencheCombo   ( $rsElementosLicencaDiversa       );
$obCmbElementos->setValue        ( $request->get("stCodigoElemento"));
$obCmbElementos->setNull         ( true                             );
$obCmbElementos->setStyle        ( "width: 220px"                   );
$obCmbElementos->obEvento->setOnChange("montaParametrosGET('montaAtributosElementos');");

$obHdnNomElemento = new Hidden;
$obHdnNomElemento->setName  ("stNomElemento");
$obHdnNomElemento->setId    ("stNomElemento");

$obITextBoxSelectDocumento = new ITextBoxSelectDocumento;
$obITextBoxSelectDocumento->setCodAcao( Sessao::read('acao') );
$obITextBoxSelectDocumento->setCodModeloDocumento ( $inCodAlvara );
$obITextBoxSelectDocumento->obTextBoxSelectDocumento->setRotulo ( "Modelo do Alvará" );
$obITextBoxSelectDocumento->obTextBoxSelectDocumento->setNULL ( false );
$obITextBoxSelectDocumento->setDisabledSelectDoc( "True" );

$obCBoxEmissaoDocumento = new CheckBox;
$obCBoxEmissaoDocumento->setName    ( "boEmissaoDocumento"  );
$obCBoxEmissaoDocumento->setLabel   ( "Impressão Local"     );
$obCBoxEmissaoDocumento->setRotulo  ( "Emissão de Alvará"   );

$obCBoxEmissaoDocumento2 = new CheckBox;
$obCBoxEmissaoDocumento2->setName    ( "boEmissaoDocumento"  );
$obCBoxEmissaoDocumento2->setLabel   ( "Impressão Local"     );
$obCBoxEmissaoDocumento2->setRotulo  ( "Emissão de Alvará "   );

//DEFINICAO DO FORM

$obForm = new Form;
$obForm->setAction ( $pgProc );
$obForm->setTarget ( "oculto" );

//DEFINICAO DO FORMULARIO
$obFormulario = new FormularioAbas;
$obFormulario->addForm                  ( $obForm                       );
$obFormulario->setAjuda                 ( "UC-05.02.12");
$obFormulario->addHidden                ( $obHdnCtrl                    );
$obFormulario->addHidden                ( $obHdnAcao                    );
$obFormulario->addHidden                ( $obHdnEval                    );
$obFormulario->addHidden                ( $obHdnboElemento              );
$obFormulario->addHidden                ( $obHdnCodDocumento            );
$obFormulario->addHidden                ( $obHdnCodTipoDocumento        );
$obFormulario->addHidden                ( $obHdnNomeDocumento           );
$obFormulario->addHidden                ( $obHdnNomeArquivo             );
$obFormulario->addHidden                ( $obHdnCodTipoLicenca          );
$obFormulario->addHidden                ( $obHdnNomElemento             );
$obFormulario->addHidden                ( $obHdnNumAtributos            );
$obFormulario->addAba                   ( "Licença"                     );
$obFormulario->addTitulo                ( "Dados para Licença"          );
$obFormulario->addComponente            ( $obLblTipoLicenca             );
if ($arConfiguracao['numero_licenca'] == 1) {
    $obFormulario->addComponente        ( $obTxtCodigoLicenca           );
}

$obPopUpCGM = new IPopUpCGM( $obForm );
$obPopUpCGM->setNull ( false );
$obPopUpCGM->obCampoCod->setName ( "inNumCGM" );
$obPopUpCGM->setRotulo ( "CGM" );
$obPopUpCGM->setTitle ( "CGM" );

$obFormulario->addComponente            ( $obPopUpCGM				    );

$obPopUpProcesso = new IPopUpProcesso ( $obForm );
$obPopUpProcesso->setNull ( true );
$obPopUpProcesso->obCampoCod->setName("inCodigoProcesso");
$obFormulario->addComponente ( $obPopUpProcesso );

$obFormulario->addComponente            ( $obTxtObservacao              );
$obFormulario->addTitulo                ( "Validade da Licença"         );
$obFormulario->addComponente            ( $obDtDataInicio               );
$obFormulario->addComponente            ( $obDtDataTermino              );
$obMontaAtributosLicencaDiversa->geraFormulario ($obFormulario);
$obITextBoxSelectDocumento->geraFormulario ( $obFormulario );
$obFormulario->addComponente            ( $obCBoxEmissaoDocumento           );

$obFormulario->addAba                   ( "Elementos para Base de Cálculo"  );
$obFormulario->addComponenteComposto    ( $obTxtElementos,$obCmbElementos   );
$obFormulario->addSpan                  ( $obSpnAtributosElemento           );
$obFormulario->addSpan                  ( $obSpnListaElementos              );

$obFormulario->addComponente            ( $obCBoxEmissaoDocumento2          );
$obFormulario->ok();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
