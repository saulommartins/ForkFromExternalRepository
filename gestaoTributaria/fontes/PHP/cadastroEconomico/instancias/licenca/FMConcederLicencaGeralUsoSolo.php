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
    * Formulario para Cadastro Economico >> Conceder Licenças Uso Solo
    * Data de Criação   : 07/04/2008
    * @author Analista      : Fábio Bertoldi Rodrigues
    * @author Desenvolvedor : André Machado
    * @package URBEM
    * @subpackage Regra

    * $Id: FMConcederLicencaGeralUsoSolo.php 63839 2015-10-22 18:08:07Z franver $

    * Casos de uso: uc-05.02.12

*/

/*
$Log$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CEM_NEGOCIO."RCEMLicencaDiversa.class.php"    );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMTipoLicencaDiversa.class.php");
include_once ( CAM_GA_CGM_COMPONENTES."IPopUpCGM.class.php"			);
include_once ( CAM_GA_ADM_COMPONENTES."ITextBoxSelectDocumento.class.php" );

//Define o nome dos arquivos PHP
$stPrograma     = "ConcederLicencaGeralUsoSolo";
$pgFilt         = "FL".$stPrograma.".php"       ;
$pgList         = "LS".$stPrograma.".php"       ;
$pgForm         = "FM".$stPrograma.".php"       ;
$pgFormTipo     = "FM".$stPrograma.".php"   ;
$pgProc         = "PR".$stPrograma.".php"       ;
$pgOcul         = "OC".$stPrograma.".php"       ;
$pgJs           = "JS".$stPrograma.".js"        ;

include_once( $pgJs );
;

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

//$sessao->transf4["lsElementos"]     = array();
//$sessao->transf4["inNumElementos"]  = 0;
Sessao::write('acao', '462');
$inCodAlvara = Sessao::read( "inCodDocumento" );

if ($stAcao == "alterar") {
    $_REQUEST["inCodigoTipoLicenca"] = $_REQUEST["inCodigoTipo"];
    Sessao::write( "inCodigoLicenca", $_REQUEST["inCodigoLicenca"] );
    Sessao::write( "inNumCGM", $_REQUEST["inNumCGM"] );
    $inCodCGM = $_REQUEST["inNumCGM"];
    $stNomCGM = $_REQUEST["stNomeCGM"];
}

/* Atributos Dinamicos */
$obRCEMLicencaDiversa = new RCEMLicencaDiversa;
$obRCEMLicencaDiversa->obRCadastroDinamico->setChavePersistenteValores  ( array ( "cod_tipo" => $_REQUEST["inCodigoTipoLicenca"] ) );
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
$obHdnCtrl->setValue                     ( $stCtrl                       );

$obHdnAcao = new Hidden;
$obHdnAcao->setName                      ( "stAcao"                      );
$obHdnAcao->setValue                     ( $stAcao                       );

$obHdnCodTipoLicenca = new Hidden;
$obHdnCodTipoLicenca->setName            ( "inCodigoTipoLicenca"            );
$obHdnCodTipoLicenca->setValue           ( $_REQUEST["inCodigoTipoLicenca"] );

$obHdnNomLogradouro = new Hidden;
$obHdnNomLogradouro->setName  ( "stNomeLogradouro" );
$obHdnNomLogradouro->setValue ( $stNomeLogradouro );

$obHdncodUF = new Hidden;
$obHdncodUF->setName ("inCodUF");
$obHdncodUF->setName ( $inCodUF );

$obHdnCodMunicipio = new Hidden;
$obHdnCodMunicipio->setName  ("inCodMunicipio");
$obHdnCodMunicipio->setValue ($inCodMunicipio);

$obHdnEval = new HiddenEval;
$obHdnEval->setName( "stEval" );
$obHdnEval->setValue( $stEval );

$obHdnNumAtributos = new HiddenEval;
$obHdnNumAtributos->setName     ( "inNumAtributos"  );
$obHdnNumAtributos->setId       ( "inNumAtributos"  );
$obHdnNumAtributos->setValue    ( 0                 );

$obRCEMTipoLicenca =  new RCEMTipoLicencaDiversa;
$obRCEMTipoLicenca->setCodigoTipoLicencaDiversa($_REQUEST["inCodigoTipoLicenca"]);
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

$obTxtCodigoLicenca = new TextBox;
$obTxtCodigoLicenca->setRotulo         ( "Número da Licença"               );
$obTxtCodigoLicenca->setTitle          ( "Número da Licença"               );
$obTxtCodigoLicenca->setName           ( "inCodigoLicenca"                 );
$obTxtCodigoLicenca->setId             ( "inCodigoLicenca"                 );
$obTxtCodigoLicenca->setValue          ( $_REQUEST["inCodigoLicenca"]  );
$obTxtCodigoLicenca->setSize           ( strlen( $arConfiguracao[mascara_licenca]) );
$obTxtCodigoLicenca->setMaxLength      ( strlen( $arConfiguracao[mascara_licenca]) );
$obTxtCodigoLicenca->setNull           ( true                              );
$obTxtCodigoLicenca->setInteiro        ( false                             );
$obTxtCodigoLicenca->obEvento->setOnKeyUp("mascaraDinamico('".$arConfiguracao[mascara_licenca]."', this, event);");

//INSCRICAO ECONOMICA
$obBscInscricaoEconomica = new BuscaInner;
$obBscInscricaoEconomica->setNull            ( false                                       );
$obBscInscricaoEconomica->setRotulo          ( "Inscrição Econômica"                       );
$obBscInscricaoEconomica->setTitle           ( "Pessoa física ou jurídica cadastrada como inscrição econômica");
$obBscInscricaoEconomica->setId              ( "stInscricaoEconomica"                      );
$obBscInscricaoEconomica->obCampoCod->setName( "inInscricaoEconomica"                      );
$obBscInscricaoEconomica->obCampoCod->setValue ( $inInscricaoEconomica );
$obBscInscricaoEconomica->obCampoCod->setSize( strlen($stMascaraInscricao)                 );
$obBscInscricaoEconomica->obCampoCod->setMaxLength ( strlen($stMascaraInscricao)           );
$obBscInscricaoEconomica->obCampoCod->setMascara ( $stMascaraInscricao                     );
$obBscInscricaoEconomica->obCampoCod->obEvento->setOnChange ( "buscaValor('buscaInscricao');" );
$obBscInscricaoEconomica->obCampoCod->obEvento->setOnBlur   ( "buscaValor('buscaInscricao');" );
$obBscInscricaoEconomica->setFuncaoBusca("abrePopUp('".CAM_GT_CEM_POPUPS."inscricaoeconomica/FLProcurarInscricaoEconomica.php','frm','inInscricaoEconomica','stInscricaoEconomica','todos','".Sessao::getId()."','800','550');");
include_once( $pgJs );

$obRadioVinculoImobiliario = new Radio;
$obRadioVinculoImobiliario->setName    ( "inVinculo" );
$obRadioVinculoImobiliario->setId      ( "inVinculo" );
$obRadioVinculoImobiliario->setRotulo  ( "Vínculo" );
$obRadioVinculoImobiliario->setValue   (  1  );
$obRadioVinculoImobiliario->setLabel   ( "Inscrição Imobiliária" );
$obRadioVinculoImobiliario->setNULL    ( false );
$obRadioVinculoImobiliario->setChecked ( false );
$obRadioVinculoImobiliario->obEvento->setOnChange("montaParametrosGET('montaVinculos".$obRadioVinculoImobiliario->getValue()."')");

$obRadioVinculoLogradouro  = new Radio;
$obRadioVinculoLogradouro->setName     ( "inVinculo" );
$obRadioVinculoLogradouro->setId       ( "inVinculo" );
$obRadioVinculoLogradouro->setRotulo   ( "Vínculo" );
$obRadioVinculoLogradouro->setValue    (  2  );
$obRadioVinculoLogradouro->setLabel    ( "Logradouro" );
$obRadioVinculoLogradouro->setNULL     ( false );
$obRadioVinculoLogradouro->setChecked  ( false );
$obRadioVinculoLogradouro->obEvento->setOnChange("montaParametrosGET('montaVinculos".$obRadioVinculoLogradouro->getValue()."')");

$spnVinculo = new Span;
$spnVinculo->setId ("spnVinculo");

// VALIDADE DA LICENÇA
$obDtDataInicio = new Data;
$obDtDataInicio->setName                ( "dtDataInicio"            );
$obDtDataInicio->setId                  ( "dtDataInicio"            );
$obDtDataInicio->setRotulo              ( "Data de Início"          );
$obDtDataInicio->setNull                ( false                     );
$obDtDataInicio->setMaxLength           ( 20                        );
$obDtDataInicio->setSize                ( 10                        );
$obDtDataInicio->setValue               ( $_REQUEST["dtDataInicio"] );
$obDtDataInicio->obEvento->setOnChange  ( "validaData1500( this );" );

$obDtDataTermino = new Data;
$obDtDataTermino->setName               ( "dtDataTermino"           );
$obDtDataTermino->setId                 ( "dtDataTermino"           );
$obDtDataTermino->setRotulo             ( "Data de Término"        );
$obDtDataTermino->setNull               ( true                      );
$obDtDataTermino->setMaxLength          ( 20                        );
$obDtDataTermino->setSize               ( 10                        );
$obDtDataTermino->setValue              ( $_REQUEST["dtDataTermino"]);
$obDtDataTermino->obEvento->setOnChange ( "validaDataLicenca();"    );

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

$obLblTipoLicenca = new Label;
$obLblTipoLicenca->setName      ( "stTipoLicenca"                       );
$obLblTipoLicenca->setValue     ( $rsTipoLicenca->getCampo("cod_tipo")." - ".$rsTipoLicenca->getCampo("nom_tipo") );
$obLblTipoLicenca->setRotulo    ( "Tipo de Licença" );
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
$obFormulario->addHidden                ( $obHdnEval,true               );

$obFormulario->addHidden                ( $obHdnCodDocumento            );
$obFormulario->addHidden                ( $obHdnCodTipoDocumento        );
$obFormulario->addHidden                ( $obHdnNomeDocumento           );
$obFormulario->addHidden                ( $obHdnNomeArquivo             );
$obFormulario->addHidden                ( $obHdnCodTipoLicenca          );
$obFormulario->addHidden                ( $obHdnNumAtributos            );
$obFormulario->addHidden                ( $obHdnCodMunicipio );
$obFormulario->addHidden                ( $obHdncodUF ) ;
$obFormulario->addHidden                ( $obHdnNomLogradouro );
$obFormulario->addTitulo                ( "Dados para Licença"          );
$obFormulario->addComponente            ( $obLblTipoLicenca             );
if ($arConfiguracao['numero_licenca'] == 1) {
    $obFormulario->addComponente        ( $obTxtCodigoLicenca           );
}

$obBscProcesso = new BuscaInner;
$obBscProcesso->setRotulo ( "Processo" );
$obBscProcesso->setTitle ( "Número do processo no protocolo que gerou a aprovação do loteamento" );
$obBscProcesso->setNull ( true );
$obBscProcesso->obCampoCod->setName ("inProcesso");
$obBscProcesso->obCampoCod->setId   ("inProcesso");
$obBscProcesso->obCampoCod->setValue( $inProcesso );
$obBscProcesso->obCampoCod->obEvento->setOnChange( "buscaValor('buscaProcesso');" );
$obBscProcesso->obCampoCod->obEvento->setOnKeyUp( "mascaraDinamico('".$stMascaraProcesso."', this, event);" );
$obBscProcesso->setFuncaoBusca( "abrePopUp('".CAM_GA_PROT_POPUPS."processo/FLBuscaProcessos.php','frm','inProcesso','campoInner2','','".Sessao::getId()."','800','550')" );

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
//$obTxtAreaUnidade->obEvento->setOnChange ( "buscaValor('calculaAreaTotal');" );

$obPopUpCGM = new IPopUpCGM( $obForm );
$obPopUpCGM->setNull ( false );
$obPopUpCGM->obCampoCod->setName ( "inNumCGM" );
$obPopUpCGM->setRotulo ( "CGM" );
$obPopUpCGM->setTitle ( "CGM" );

if ($stAcao == "alterar") {
    $obPopUpCGM->obCampoCod->setValue ( $inCodCGM );
    $obPopUpCGM->setValue ( $stNomCGM );
    $obPopUpCGM->obCampoCod->setDisabled( true );
}
$obFormulario->addComponente( $obPopUpCGM				);

//Observação
$obTxtObservacao = new TextArea;
$obTxtObservacao->setName   ("stObservacao");
$obTxtObservacao->setRotulo ("Observações");
$obTxtObservacao->setTitle  ("Observações referentes à licença");
$obTxtObservacao->setValue  ( $stObservacao );

$spnInsEconomica = new Span;
$spnInsEconomica->setId("spnInsEconomica");

if ($stAcao == "alterar") {
    $obTxtCodigoLicenca->setValue          ( $_REQUEST["inCodigoLicenca"] );
//Buscar Inscrição economica
    include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMUsoSoloEmpresa.class.php"              );
    $obTCEMUsoSoloEmpresa = new TCEMUsoSoloEmpresa;
    $stFiltro = " WHERE cod_licenca = ".$_REQUEST["inCodigoLicenca"]." AND exercicio = '".$_REQUEST["stExercicio"]."'";
    $obTCEMUsoSoloEmpresa->recuperaTodos( $rsUsoSoloEmpresa, $stFiltro);
    if ( !$rsUsoSoloEmpresa->Eof() ) {
        $inInscricaoEconomica = $rsUsoSoloEmpresa->getCampo( "inscricao_economica" );
        $obBscInscricaoEconomica->obCampoCod->setValue ( $inInscricaoEconomica );
    }

//Buscar Uso Solo Imobiliaria ou Logradouro
    include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMUsoSoloImovel.class.php"               );
    $obTCEMUsoSoloImovel = new TCEMUsoSoloImovel;
    $stFiltro = " WHERE cod_licenca = ".$_REQUEST["inCodigoLicenca"]." AND exercicio = '".$_REQUEST["stExercicio"]."'";
    $obTCEMUsoSoloImovel->recuperaTodos( $rsUsoSoloImovel, $stFiltro );

    $obRadioVinculoImobiliario->setDisabled( true );
    $obRadioVinculoLogradouro->setDisabled( true );

    if ( !$rsUsoSoloImovel->Eof() ) {
        $obRadioVinculoImobiliario->setChecked ( true );
        Sessao::write( "UsoSoloImovel", $rsUsoSoloImovel->getCampo("inscricao_municipal") );
        $jsOnload = "executaFuncaoAjax('montaVinculos1');";

    } else {
        include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMUsoSoloLogradouro.class.php"           );
        $obTCEMUsoSoloLogradouro = new TCEMUsoSoloLogradouro;
        $stFiltro = " WHERE cod_licenca = ".$_REQUEST["inCodigoLicenca"]." AND exercicio = '".$_REQUEST["stExercicio"]."'";
        $obTCEMUsoSoloLogradouro->recuperaTodos( $rsUsoSoloLogradouro, $stFiltro );

        $obRadioVinculoLogradouro->setChecked( true );
        Sessao::write( "UsoSoloLogradouro", $rsUsoSoloLogradouro->getCampo("cod_logradouro") );
        $jsOnload = "executaFuncaoAjax('montaVinculos2')";

    }

//Buscar Area
    include_once( CAM_GT_CEM_MAPEAMENTO."TCEMUsoSoloArea.class.php" );
    $obTCEMUsoSoloArea = new TCEMUsoSoloArea;
    $stFiltro = " WHERE cod_licenca = ".$_REQUEST["inCodigoLicenca"]." AND exercicio = '".$_REQUEST["stExercicio"]."'";
    $obTCEMUsoSoloArea->recuperaTodos( $rsUsoSoloArea, $stFiltro );

    $obTxtAreaUnidade->setValue      ( $rsUsoSoloArea->getCampo("area"));

//Buscar Processo
    include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMProcessoLicenca.class.php"             );
    $obTCEMProcessoLicenca = new TCEMProcessoLicenca;
    $stFiltro = " WHERE cod_licenca = ".$_REQUEST["inCodigoLicenca"]." AND exercicio = '".$_REQUEST["stExercicio"]."'";
    $obTCEMProcessoLicenca->recuperaTodos( $rsProcesso, $stFiltro );
    if ( !$rsProcesso->Eof() ) {
        $inProcesso = $rsProcesso->getCampo("cod_processo")."/".$rsProcesso->getCampo("exercicio_processo");
        $obBscProcesso->obCampoCod->setValue( $inProcesso );
    }

//Buscar Observação
    include_once( CAM_GT_CEM_MAPEAMENTO."TCEMLicencaObservacao.class.php" );
    $obTCEMLicencaObservacao = new TCEMLicencaObservacao;
    $stFiltro = " WHERE cod_licenca = ".$_REQUEST["inCodigoLicenca"]." AND exercicio = '".$_REQUEST["stExercicio"]."'";
    $obTCEMLicencaObservacao->recuperaTodos( $rsLicencaObservacao, $stFiltro );
    if ( !$rsLicencaObservacao->Eof() ) {
        $stObservacao = $rsLicencaObservacao->getCampo( "observacao" );
        $obTxtObservacao->setValue  ( $stObservacao );
    }

//Data Inicio, final  documento
    $obDtDataInicio->setValue               ( $_REQUEST["dtInicio"] );
    $obDtDataTermino->setValue              ( $_REQUEST["dtTermino"]);
    $inCodAlvara = $_REQUEST["cod_documento"];
    $obITextBoxSelectDocumento->setCodModeloDocumento ( $inCodAlvara );

//Atributos Dinamicos!!
    include_once ( CAM_GA_ADM_NEGOCIO."RCadastroDinamico.class.php");
    $obRCadastroDinamico = new RCadastroDinamico;
    $obRCadastroDinamico->setPersistenteAtributos( new TCEMAtributoTipoLicencaDiversa  );
    $obRCadastroDinamico->setPersistenteValores  ( new TCEMAtributoLicencaDiversaValor );
    $obRCadastroDinamico->setCodCadastro ( 4 );
    $obRCadastroDinamico->setChavePersistenteValores(
    array( "cod_licenca" => $inCodigoLicenca,
           "exercicio"   => Sessao::getExercicio(),
           "cod_tipo"    => $_REQUEST["inCodigoTipoLicenca"] )
    );
    $obRCadastroDinamico->recuperaAtributosSelecionadosValores($rsAtributos);
    $obMontaAtributosLicencaDiversa->setRecordSet  ( $rsAtributos );
}

$obFormulario->addSpan                  ( $spnInsEconomica );
$obFormulario->addComponente            ( $obBscInscricaoEconomica );
$obFormulario->addComponenteComposto    ( $obRadioVinculoImobiliario, $obRadioVinculoLogradouro );
$obFormulario->addSpan                  ( $spnVinculo );
$obFormulario->addComponente            ( $obTxtAreaUnidade );
$obFormulario->addComponente            ( $obBscProcesso ) ;
$obFormulario->addComponente            ( $obTxtObservacao );

$obFormulario->addTitulo                ( "Validade da Licença"         );
$obFormulario->addComponente            ( $obDtDataInicio               );
$obFormulario->addComponente            ( $obDtDataTermino              );
$obMontaAtributosLicencaDiversa->geraFormulario ($obFormulario);
$obITextBoxSelectDocumento->geraFormulario ( $obFormulario );
$obFormulario->addComponente            ( $obCBoxEmissaoDocumento           );

$obFormulario->ok();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
