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
    * Página de Formulario de Manter Inventario
    * Data de Criação: 07/04/2009

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @ignore

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GA_ADM_COMPONENTES."ISelectPais.class.php");
include_once(CAM_GA_CGM_MAPEAMENTO."TAtributoCgm.class.php");
//include_once(CAM_FRAMEWORK."legado/cgmLegado.class.php");
include_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoAcao.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ManterCgm";
$pgFilt     = "FLProcurarCgm.php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";
$pgList     = "LS".$stPrograma.".php";

$stAcao         = $_REQUEST['stAcao'];
$stTipoPessoa   = $_REQUEST['stTipoPessoa'];
//Sessao::write('popup_cgm',array());

include($pgJs);

$obTAdministracaoAcao = new TAdministracaoAcao;
$obTAdministracaoAcao->setDado('cod_acao', 37);
$obTAdministracaoAcao->recuperaPermissao($rsPermissaoIncluir);
$obTAdministracaoAcao->setDado('cod_acao', 309);
$obTAdministracaoAcao->recuperaPermissao($rsPermissaoIncluirInterno);
$boPermissao = false;
if ( !$rsPermissaoIncluirInterno->eof() ) {
    $boPermissao = true;
}

$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setId( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setId( "stCtrl" );
$obHdnCtrl->setValue( $stCtrl );

$obHdnEval = new HiddenEval;
$obHdnEval->setName( "stEval" );

$obHdnTipoBusca = new Hidden;
$obHdnTipoBusca->setName( "stTipoBusca" );
$obHdnTipoBusca->setValue( $_REQUEST['stTipoBusca'] );

$obHdnCampoNum = new Hidden;
$obHdnCampoNum->setName( "campoNum" );
$obHdnCampoNum->setValue( $_REQUEST['campoNum'] );

//Define HIDDEN com o o nome do campo texto
$obHdnCampoNom = new Hidden;
$obHdnCampoNom->setName( "campoNom" );
$obHdnCampoNom->setValue( $_REQUEST['campoNom'] );

### Tipo de Cadastro ###

$obRdnPF = new Radio;
$obRdnPF->setRotulo  ( "Tipo de cadastro" );
$obRdnPF->setName    ( "boPessoa"   );
$obRdnPF->setLabel   ( "Física" );
$obRdnPF->setValue   ( "fisica" );
$obRdnPF->obEvento->setOnClick("buscaValor('montaDadosPorTipo');");
if($stTipoPessoa!='J')
    $obRdnPF->setChecked ( true               );
if($_REQUEST['stTipoBusca']=='juridica')
    $obRdnPF->setDisabled ( true               );

$obRdnPJ = new Radio;
$obRdnPJ->setRotulo  ( "Tipo de cadastro" );
$obRdnPJ->setName    ( "boPessoa" );
$obRdnPJ->setLabel   ( "Jurídica" );
$obRdnPJ->setValue   ( "juridica" );
$obRdnPJ->obEvento->setOnClick("buscaValor('montaDadosPorTipo');");
if($stTipoPessoa=='J')
    $obRdnPJ->setChecked ( true              );
if($_REQUEST['stTipoBusca']=='fisica')
    $obRdnPJ->setDisabled ( true              );

$arRdnTipoPessoa = array ( $obRdnPF, $obRdnPJ );

$obChkInterno = new CheckBox;
$obChkInterno->setRotulo('CGM Interno');
$obChkInterno->setValue('true');
$obChkInterno->setName  ('boInterno');
$obChkInterno->setId    ('boInterno');
$obChkInterno->setChecked(false);
$obChkInterno->obEvento->setOnClick("buscaValor('montaDadosPorTipo');");

### Dados para CGM ###

### Dados de endereço ###

$obISelectPais = new ISelectPais(1);
$obISelectPais->setName('pais');
$obISelectPais->setNull( false );

$obHdnMunicipio = new Hidden();
$obHdnMunicipio->setName('inCodMunicipio');
$obHdnMunicipio->setId  ('inCodMunicipio');

$obHdnEstado = new Hidden();
$obHdnEstado->setName('inCodEstado');
$obHdnEstado->setId  ('inCodEstado');

$obLabelEstado = new Label();
$obLabelEstado->setRotulo('Estado');
$obLabelEstado->setName('stEstado');
$obLabelEstado->setId  ('stEstado');

$obLabelMunicipio = new Label();
$obLabelMunicipio->setRotulo('Municipio');
$obLabelMunicipio->setName('stMunicipio');
$obLabelMunicipio->setId  ('stMunicipio');

$obBscLogradouro = new BuscaInner;
$obBscLogradouro->setMonitorarCampoCod(true);
$obBscLogradouro->setRotulo ( "Logradouro" );
$obBscLogradouro->setTitle  ( "Logradouro onde o trecho está localizado" );
$obBscLogradouro->setId     ( "campoInnerLogr" );
$obBscLogradouro->setValue  ( $stLogradouroNome );
$obBscLogradouro->setNull   ( false );
$obBscLogradouro->obCampoCod->setName  ( "inNumLogradouro" );
$obBscLogradouro->obCampoCod->setId    ( "inNumLogradouro" );
$obBscLogradouro->obCampoCod->setValue ( $dadosCgm["cod_logradouro"] );
$obBscLogradouro->obCampoCod->obEvento->setOnChange( "javascript:buscaValor('montaLogradouro');" );
$obBscLogradouro->setFuncaoBusca("abrePopUp('" . CAM_GT_CIM_POPUPS."logradouro/FLProcurarLogradouro.php','frm', 'inNumLogradouro','campoInnerLogr','Cgm','" . Sessao::getId() ."1','800','550','logradouro');");

$obTxtNumero = new Inteiro();
$obTxtNumero->setRotulo('Número');
$obTxtNumero->setName('inNumero');
$obTxtNumero->setId  ('inNumero');
$obTxtNumero->setNull ( false );

$obTxtComplemento = new TextBox();
$obTxtComplemento->setRotulo('Complemento');
$obTxtComplemento->setName('stComplemento');
$obTxtComplemento->setId  ('stComplemento');
$obTxtComplemento->setSize      (20);
$obTxtComplemento->setMaxLength (25);

$obTxtSelectBairro = new TextBoxSelect();
$obTxtSelectBairro->setRotulo               ( "Bairro"              );
$obTxtSelectBairro->setName                 ( "inCodigoBairro"      );
$obTxtSelectBairro->setTitle                ( "Selecione o bairro." );
$obTxtSelectBairro->setNull                 ( false                          );
$obTxtSelectBairro->obTextBox->setName      ( "inCodigoBairro"             );
$obTxtSelectBairro->obTextBox->setId        ( "inCodigoBairro"             );
$obTxtSelectBairro->obTextBox->setRotulo    ( "Bairro"                     );
$obTxtSelectBairro->obTextBox->setTitle     ( "Selecione o bairro"         );
$obTxtSelectBairro->obTextBox->setInteiro   ( true                           );
$obTxtSelectBairro->obTextBox->setNull      ( false                          );
$obTxtSelectBairro->obSelect->setName       ( "cmbBairro"               );
$obTxtSelectBairro->obSelect->setId         ( "cmbBairro"               );
$obTxtSelectBairro->obSelect->setCampoId    ( "cod_bairro"                 );
$obTxtSelectBairro->obSelect->setCampoDesc  ( "nom_bairro"                  );
$obTxtSelectBairro->obSelect->setStyle      ( "width: 220"                   );
$obTxtSelectBairro->obSelect->setNull       ( false                          );

$obSelectCEP = new Select();
$obSelectCEP->setRotulo     ( "CEP"              );
$obSelectCEP->setName       ( "cmbCEP"               );
$obSelectCEP->setId         ( "cmbCEP"               );
$obSelectCEP->setCampoId    ( "cod_cep"              );
$obSelectCEP->setCampoDesc  ( "nom_cep"              );
$obSelectCEP->setStyle      ( "width: 220"              );
$obSelectCEP->setNull       ( false                     );

### Dados de endereço para correspondência ###

$obISelectPaisCor = new ISelectPais(1);
$obISelectPaisCor->setName('paisCor');

$obHdnMunicipioCor = new Hidden();
$obHdnMunicipioCor->setName('inCodMunicipioCor');
$obHdnMunicipioCor->setId  ('inCodMunicipioCor');

$obHdnEstadoCor = new Hidden();
$obHdnEstadoCor->setName('inCodEstadoCor');
$obHdnEstadoCor->setId  ('inCodEstadoCor');

$obLabelEstadoCor = new Label();
$obLabelEstadoCor->setRotulo('Estado');
$obLabelEstadoCor->setName('stEstadoCor');
$obLabelEstadoCor->setId  ('stEstadoCor');

$obLabelMunicipioCor = new Label();
$obLabelMunicipioCor->setRotulo('Municipio');
$obLabelMunicipioCor->setName('stMunicipioCor');
$obLabelMunicipioCor->setId  ('stMunicipioCor');

$obBscLogradouroCor = new BuscaInner;
$obBscLogradouroCor->setMonitorarCampoCod(true);
$obBscLogradouroCor->setRotulo ( "Logradouro" );
$obBscLogradouroCor->setTitle  ( "Logradouro onde o trecho está localizado" );
$obBscLogradouroCor->setId     ( "campoInnerLogrCor" );
$obBscLogradouroCor->setValue  ( $stLogradouroNome );
$obBscLogradouroCor->obCampoCod->setName  ( "inNumLogradouroCor" );
$obBscLogradouroCor->obCampoCod->setId    ( "inNumLogradouroCor" );
$obBscLogradouroCor->obCampoCod->setValue ( $dadosCgm["cod_logradouro"] );
$obBscLogradouroCor->obCampoCod->obEvento->setOnChange( "javascript:buscaValor('montaLogradouro','&stCor=Cor');" );
$obBscLogradouroCor->setFuncaoBusca("abrePopUp('" . CAM_GT_CIM_POPUPS."logradouro/FLProcurarLogradouro.php','frm', 'inNumLogradouroCor','campoInnerLogrCor','Cgm','" . Sessao::getId() ."1','800','550','logradouro');");

$obTxtNumeroCor = new Inteiro();
$obTxtNumeroCor->setRotulo('Número');
$obTxtNumeroCor->setName('inNumeroCor');
$obTxtNumeroCor->setId  ('inNumeroCor');

$obTxtComplementoCor = new TextBox();
$obTxtComplementoCor->setRotulo('Complemento');
$obTxtComplementoCor->setName('stComplementoCor');
$obTxtComplementoCor->setId  ('stComplementoCor');
$obTxtComplementoCor->setSize      (20);
$obTxtComplementoCor->setMaxLength (25);

$obTxtSelectBairroCor = new TextBoxSelect();
$obTxtSelectBairroCor->setRotulo               ( "Bairro"              );
$obTxtSelectBairroCor->setName                 ( "inCodigoBairroCor"      );
$obTxtSelectBairroCor->setTitle                ( "Selecione o bairro." );
$obTxtSelectBairroCor->obTextBox->setName      ( "inCodigoBairroCor"             );
$obTxtSelectBairroCor->obTextBox->setId        ( "inCodigoBairroCor"             );
$obTxtSelectBairroCor->obTextBox->setRotulo    ( "Bairro"                     );
$obTxtSelectBairroCor->obTextBox->setTitle     ( "Selecione o bairro"         );
$obTxtSelectBairroCor->obTextBox->setInteiro   ( true                           );
$obTxtSelectBairroCor->obSelect->setName       ( "cmbBairroCor"               );
$obTxtSelectBairroCor->obSelect->setId         ( "cmbBairroCor"               );
$obTxtSelectBairroCor->obSelect->setCampoId    ( "cod_bairro"                 );
$obTxtSelectBairroCor->obSelect->setCampoDesc  ( "nom_bairro"                  );
$obTxtSelectBairroCor->obSelect->setStyle      ( "width: 220"                   );

$obSelectCEPCor = new Select();
$obSelectCEPCor->setRotulo     ( "CEP"              );
$obSelectCEPCor->setName       ( "cmbCEPCor"               );
$obSelectCEPCor->setId         ( "cmbCEPCor"               );
$obSelectCEPCor->setCampoId    ( "cod_cep"              );
$obSelectCEPCor->setCampoDesc  ( "nom_cep"              );
$obSelectCEPCor->setStyle      ( "width: 220"              );

### Dados de endereço para correspondência ###

$obTxtDDDTelResidencial = new Inteiro;
$obTxtDDDTelResidencial->setRotulo('Telefone residencial');
$obTxtDDDTelResidencial->setName('inDDDTelResidencial');
$obTxtDDDTelResidencial->setId  ('inDDDTelResidencial');
$obTxtDDDTelResidencial->setSize(4);
$obTxtDDDTelResidencial->setMaxLength(3);
$obTxtTelResidencial = new Inteiro;
$obTxtTelResidencial->setRotulo('');
$obTxtTelResidencial->setName('inTelResidencial');
$obTxtTelResidencial->setId  ('inTelResidencial');
$obTxtTelResidencial->setSize(12);
$obTxtTelResidencial->setMaxLength(30);

$obTxtDDDTelComercial = new Inteiro;
$obTxtDDDTelComercial->setRotulo('Telefone comercial');
$obTxtDDDTelComercial->setName('inDDDTelComercial');
$obTxtDDDTelComercial->setId  ('inDDDTelComercial');
$obTxtDDDTelComercial->setSize(4);
$obTxtDDDTelComercial->setMaxLength(3);
$obTxtTelComercial = new Inteiro;
$obTxtTelComercial->setRotulo('');
$obTxtTelComercial->setName('inTelComercial');
$obTxtTelComercial->setId  ('inTelComercial');
$obTxtTelComercial->setSize(12);
$obTxtTelComercial->setMaxLength(30);
$obLabelRamal = new Label();
$obLabelRamal->setRotulo('');
$obLabelRamal->setValue ('Ramal');
$obTxtTelComercialRamal = new Inteiro;
$obTxtTelComercialRamal->setRotulo('');
$obTxtTelComercialRamal->setName('inTelComercialRamal');
$obTxtTelComercialRamal->setId  ('inTelComercialRamal');
$obTxtTelComercialRamal->setSize(6);
$obTxtTelComercialRamal->setMaxLength(6);

$obTxtDDDTelCelular = new Inteiro;
$obTxtDDDTelCelular->setRotulo('Telefone celular');
$obTxtDDDTelCelular->setName('inDDDTelCelular');
$obTxtDDDTelCelular->setId  ('inDDDTelCelular');
$obTxtDDDTelCelular->setSize(4);
$obTxtDDDTelCelular->setMaxLength(3);
$obTxtTelCelular = new Inteiro;
$obTxtTelCelular->setRotulo('');
$obTxtTelCelular->setName('inTelCelular');
$obTxtTelCelular->setId  ('inTelCelular');
$obTxtTelCelular->setSize(12);
$obTxtTelCelular->setMaxLength(30);

$obTxtEmail = new TextBox();
$obTxtEmail->setRotulo    ( "e-mail"            );
$obTxtEmail->setTitle     ( "Informe o e-mail." );
$obTxtEmail->setName      ( "stEmail"           );
$obTxtEmail->setId        ( "stEmail"           );
$obTxtEmail->setSize      (30);
$obTxtEmail->setMaxLength (100);

$obTxtEmailAdicional = new TextBox();
$obTxtEmailAdicional->setRotulo    ( "e-mail adicional"           );
$obTxtEmailAdicional->setTitle     ( "Informe o e-mail adicional.");
$obTxtEmailAdicional->setName      ( "stEmailAdicional"           );
$obTxtEmailAdicional->setId        ( "stEmailAdicional"           );
$obTxtEmailAdicional->setSize      (30);
$obTxtEmailAdicional->setMaxLength (100);

### Atributos ###

$arAtributos = array();
$obTAtributoCgm = new TAtributoCgm();
$rsAtributos    = new RecordSet();
$obTAtributoCgm->recuperaRelacionamento( $rsAtributos );
while (!$rsAtributos->eof()) {
    $inCodAtributo  = $rsAtributos->getCampo("cod_atributo");
    $stNomAtributo  = $rsAtributos->getCampo("nom_atributo");
    $stTipo         = $rsAtributos->getCampo("tipo");
    $stValor        = $rsAtributos->getCampo("valor");
    $stValorPadrao  = $rsAtributos->getCampo("valor_padrao");
    $inNumCgm       = $rsAtributos->getCampo("numcgm");

    if ( $rsAtributos->getCampo("numCgm") ) {
        $valor = $stValor;
    } else {
        $valor = $stValorPadrao;
    }
    switch ($stTipo) {
        case "t":
            $obAtributo = new TextBox();
            $obAtributo->setRotulo    ( $stNomAtributo             );
            $obAtributo->setTitle     ( "Informe o $stNomAtributo.");
            $obAtributo->setName      ( "atributo[$inCodAtributo]" );
            $obAtributo->setId        ( "atributo[$inCodAtributo]" );
            $obAtributo->setValue     ( $valor );
            $obAtributo->setSize      (40);
        break;
        case "n":
            $obAtributo = new Inteiro();
            $obAtributo->setRotulo    ( $stNomAtributo             );
            $obAtributo->setTitle     ( "Informe o $stNomAtributo.");
            $obAtributo->setName      ( "atributo[$inCodAtributo]" );
            $obAtributo->setId        ( "atributo[$inCodAtributo]" );
            $obAtributo->setValue     ( $valor );
            $obAtributo->setSize      (40);
        break;
        case "l":
            $arTMP = explode("\n",$stValorPadrao);

            $obAtributo = new Select();
            $obAtributo->setRotulo    ( $stNomAtributo             );
            $obAtributo->setTitle     ( "Informe o $stNomAtributo.");
            $obAtributo->setName      ( "atributo[$inCodAtributo]" );
            $obAtributo->setId        ( "atributo[$inCodAtributo]" );
            $obAtributo->setStyle     ( "width: 220"               );
            $obAtributo->setValue     ( $valor                     );
            foreach($arTMP as $key)
                $obAtributo->addOption    ( $key, $key );
        break;
    }
    $arAtributos[] = $obAtributo;
    $rsAtributos->proximo();
}

$obSpnDadosCgm = new Span();
$obSpnDadosCgm->setId('spnDadosCgm');

$obBtnOk = new Ok;
$obBtnOk->setName ( "btnOk" );
$obBtnOk->setValue( "Ok" );

foreach($_REQUEST as $key=>$value)
    $stLink .= "&$key=$value";
$stLink .= "&tipoBusca={$_REQUEST['stTipoBusca']}";
$stProxPage = $pgFilt."?".Sessao::getId().$stLink;

$obBtnCancelar = new Button;
$obBtnCancelar->setName  ( "btnCancelar" );
$obBtnCancelar->setValue ( "Cancelar" );
$obBtnCancelar->setTipo  ( "button" );
$obBtnCancelar->obEvento->setOnClick( "Cancelar('".$stProxPage."');" );

$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnEval, true );
$obFormulario->addHidden( $obHdnTipoBusca );
$obFormulario->addHidden( $obHdnCampoNum );
$obFormulario->addHidden( $obHdnCampoNom );
$obFormulario->addHidden( $obHdnEstado );
$obFormulario->addHidden( $obHdnEstadoCor );
$obFormulario->addHidden( $obHdnMunicipio );
$obFormulario->addHidden( $obHdnMunicipioCor );
$obFormulario->addTitulo( 'Tipo de Cadastro' );
$obFormulario->agrupaComponentes ( $arRdnTipoPessoa );
if( $boPermissao )
    $obFormulario->addComponente ( $obChkInterno );
$obFormulario->addTitulo( 'Dados para CGM' );
$obFormulario->addSpan( $obSpnDadosCgm );
$obFormulario->addTitulo( 'Dados de endereço' );
$obFormulario->addComponente ( $obISelectPais );
$obFormulario->addComponente ( $obLabelEstado );
$obFormulario->addComponente ( $obLabelMunicipio );
$obFormulario->addComponente ( $obBscLogradouro );
$obFormulario->addComponente ( $obTxtNumero );
$obFormulario->addComponente ( $obTxtComplemento );
$obFormulario->addComponente ( $obTxtSelectBairro );
$obFormulario->addComponente ( $obSelectCEP );
$obFormulario->addTitulo( 'Dados de endereço para correspondência' );
$obFormulario->addComponente ( $obISelectPaisCor );
$obFormulario->addComponente ( $obLabelEstadoCor );
$obFormulario->addComponente ( $obLabelMunicipioCor );
$obFormulario->addComponente ( $obBscLogradouroCor );
$obFormulario->addComponente ( $obTxtNumeroCor );
$obFormulario->addComponente ( $obTxtComplementoCor );
$obFormulario->addComponente ( $obTxtSelectBairroCor );
$obFormulario->addComponente ( $obSelectCEPCor );
$obFormulario->addTitulo( 'Dados para contato' );
$obFormulario->agrupaComponentes ( array( $obTxtDDDTelResidencial, $obTxtTelResidencial ) );
$obFormulario->agrupaComponentes ( array( $obTxtDDDTelComercial, $obTxtTelComercial, $obLabelRamal, $obTxtTelComercialRamal ) );
$obFormulario->agrupaComponentes ( array( $obTxtDDDTelCelular, $obTxtTelCelular ) );
$obFormulario->addComponente ( $obTxtEmail );
$obFormulario->addComponente ( $obTxtEmailAdicional );
$obFormulario->addTitulo( 'Atributos' );
foreach($arAtributos as $obAtributo)
    $obFormulario->addComponente ( $obAtributo );

$obFormulario->defineBarra( array($obBtnOk, $obBtnCancelar), "left", "<b>*Campo obrigatório.</b>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;" );

$obFormulario->addIFrameOculto("oculto");
$obFormulario->obIFrame->setWidth("100%");
$obFormulario->obIFrame->setHeight("0");

$obFormulario->show();

$obIFrame = new IFrame;
$obIFrame->setName("telaMensagem");
$obIFrame->setWidth("100%");
$obIFrame->setHeight("65");
$obIFrame->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

echo "<script type='text/javascript'> buscaValor('montaDadosPorTipo'); </script>\n";

?>
