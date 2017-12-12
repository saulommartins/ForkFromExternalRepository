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
    * Página de formulário para o cadastro de logradouro
    * Data de Criação   : 13/09/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Fábio Bertoldi Rodrigues
                             Gustavo Passos Tourinho
                             Cassiano de Vasconcelos Ferreira

    * @ignore

    * $Id: FMManterLogradouro.php 63920 2015-11-09 12:18:49Z evandro $

    * Casos de uso: uc-05.01.04
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLogradouro.class.php"       );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLogradouro.class.php"       );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoLogradouro.class.php"       );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoTipoLogradouro.class.php" );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoUF.class.php"             );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMTrecho.class.php" );

$acao   = Sessao::read('acao');
$modulo = Sessao::read('modulo');

//Define o nome dos arquivos PHP
$stPrograma = "ProcurarLogradouro";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";
$pgBairro   = "../bairro/FMManterBairro.php?".Sessao::getId();
//$pgBairro   = "../bairro/FLProcurarBairro.php";

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

Sessao::write('acao', "783");
Sessao::write('modulo', "0");

$arTransf5Sessao = Sessao::read('sessao_transf5');
if ( is_array( $arTransf5Sessao ) ) {
    foreach ($arTransf5Sessao as $stChave => $stValor) {
        $_REQUEST[$stChave] = $stValor;
        $link[$key] = $valor;
    }
}

Sessao::write('link', $link);

include_once( $pgJs );

$arBairrosSessao = Sessao::read('bairros');
$arCepSessao     = Sessao::read('cep');

if ( !is_array( $arBairrosSessao ) and !is_array( $arCepSessao ) ) {
    Sessao::remove('sessao_transf6');
    Sessao::write('bairros', array());
    Sessao::write('cep'    , array());
}

// DEFINE OBJETOS DAS CLASSES
$obRCIMLogradouro = new RCIMLogradouro;
$obRCIMBairro     = new RCIMBairro;

//DEFINICAO DO IFRAME DA TELA DE MENSAGEM
$obIFrame = new IFrame;
$obIFrame->setName  ("oculto"   );
$obIFrame->setWidth ("100%"     );
$obIFrame->setHeight("0"      );

$obIFrame2 = new IFrame;
$obIFrame2->setName   ( "telaMensagem" );
$obIFrame2->setWidth  ( "100%"         );
$obIFrame2->setHeight ( "50"           );
//---------------------------------------------------------------------

//PREENCHE RECORDSET
$obRCIMLogradouro->listarUF( $rsUF );
$rsTipos = new RecordSet;
$obRCIMLogradouro->listarTipoLogradouro( $rsTipos );

if ($_REQUEST["inCodigoUF"]) {
    $obRCIMBairro->setCodigoUF( $_REQUEST["inCodigoUF"] );
    $obRCIMBairro->listarMunicipios( $rsMunicipios );
} else {
    $rsMunicipios = new RecordSet;
}

if ($_REQUEST["inCodigoMunicipio"]) {
    $obRCIMBairro->setCodigoUF( $_REQUEST["inCodigoUF"] );
    $obRCIMBairro->setCodigoMunicipio( $_REQUEST["inCodigoMunicipio"] );
    $obRCIMBairro->listarBairros( $rsBairros );
} else {
    $rsBairros = new RecordSet;
}

//DEFINICAO DOS COMPONENTES DE FORMULARIO

//--------------------------------------------------------- HIDDENS
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( $_REQUEST['stCtrl'] );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $_REQUEST['stAcao'] );

$obHdnCodLogradouro = new Hidden;
$obHdnCodLogradouro->setName  ( "inCodigoLogradouro"            );
$obHdnCodLogradouro->setValue ( $_REQUEST["inCodigoLogradouro"] );

$obHdnCampoNome = new Hidden;
$obHdnCampoNome->setName  ( "campoNom"       );
$obHdnCampoNome->setValue ( $_REQUEST["campoNom"] );

$obHdnCampoNum  = new Hidden;
$obHdnCampoNum->setName  ( "campoNum"       );
$obHdnCampoNum->setValue ( $_REQUEST["campoNum"] );

$obHdnPais = new Hidden;
$obHdnPais->setName  ( "inCodPais" );
$obHdnPais->setValue ( $_REQUEST["inCodPais"] );

$obHdnCadastro = new Hidden;
$obHdnCadastro->setName  ( "stCadastro"            );
$obHdnCadastro->setValue ( $_REQUEST["stCadastro"] );

//HIDDENS para incluir dados da nova regra de logradouro mas que nao são pertinentes a essa popUp
//Setando valores padrões para os campos
$obHdninCodNorma = new Hidden;
$obHdninCodNorma->setName  ( "inCodNorma" );
$obHdninCodNorma->setValue ( 0 );

$obHdnstDataInicial = new Hidden;
$obHdnstDataInicial->setName  ( "stDataInicial" );
$obHdnstDataInicial->setValue ( date("d/m/Y") );

$obHdnstDataFinal = new Hidden;
$obHdnstDataFinal->setName  ( "stDataFinal" );
$obHdnstDataFinal->setValue ( "" );

$obHdnstDescricaoNorma = new Hidden;
$obHdnstDescricaoNorma->setName  ( "stDescricaoNorma" );
$obHdnstDescricaoNorma->setValue ( "Não informado" );

if ($stAcao == 'renomear' || $stAcao == 'alterar') {

    $obHdnNomeAnterior = new Hidden;
    $obHdnNomeAnterior->setName  ( "hdnNomeAntigo"       );
    $obHdnNomeAnterior->setValue ( $_REQUEST["stNomeLogradouro"] );

    $obLblNomeAnterior = new Label;
    $obLblNomeAnterior->setRotulo ( "Nome Anterior"       );
    $obLblNomeAnterior->setName   ( "stNomeAntigo"        );
    $obLblNomeAnterior->setValue  ( $_REQUEST["stNomeLogradouro"]);

    $obHdnCodUF = new Hidden;
    $obHdnCodUF->setName  ( "inCodUF"               );
    $obHdnCodUF->setValue ( $_REQUEST["inCodigoUF"] );

    $obHdnCodMunicipio = new Hidden;
    $obHdnCodMunicipio->setName  ( "inCodMunicipio"               );
    $obHdnCodMunicipio->setValue ( $_REQUEST["inCodigoMunicipio"] );

    $obHdnNomeUF = new Hidden;
    $obHdnNomeUF->setName  ( "stNomeUF"               );
    $obHdnNomeUF->setValue ( $_REQUEST["stNomeUF"] );

    $obHdnNomeMunicipio = new Hidden;
    $obHdnNomeMunicipio->setName  ( "stNomeMunicipio"               );
    $obHdnNomeMunicipio->setValue ( $_REQUEST["stNomeMunicipio"] );
}

//-------------------------------------------------------- FIM HIDDENS

//-------------------------------------------------------- LABELS

$obRCIMBairro->setCodigoUF( $_REQUEST["inCodigoUF"] );
$obRCIMBairro->listarUF( $rsNomeEstados );
$stNomeEstado = $rsNomeEstados->getCampo("nom_uf");
$obLblNomeUF = new Label;
$obLblNomeUF->setRotulo ( "Estado"                 );
$obLblNomeUF->setName   ( "stNomeUF"               );
$obLblNomeUF->setValue  ( $stNomeEstado            );

$obRCIMBairro->setCodigoMunicipio( $_REQUEST["inCodigoMunicipio"] );
$obRCIMBairro->listarMunicipios( $rsNomeMunicipios );
$stNomeMunicipio = $rsNomeMunicipios->getCampo("nom_municipio");
$obLblNomeMunicipio = new Label;
$obLblNomeMunicipio->setRotulo ( "Munic&iacute;pio" );
$obLblNomeMunicipio->setName   ( "stNomeMunicipio"  );
$obLblNomeMunicipio->setValue  ( $stNomeMunicipio   );

$obLblCodLogradouro = new Label;
$obLblCodLogradouro->setRotulo ( "Código"                        );
$obLblCodLogradouro->setName   ( "inCodigoLogradouro"            );
$obLblCodLogradouro->setValue  ( $_REQUEST["inCodigoLogradouro"] );

//-------------------------------------------------------- FIM LABELS

//-------------------------------------------------------- TEXT BOX
$inProxCodLogradouro = null;
if ($stAcao == 'incluir') {
    $obTLogradouro= new TLogradouro();
    $obTLogradouro->proximoCod($inProxCodLogradouro);
}
$obTxtCodigoLogradouro = new TextBox;
$obTxtCodigoLogradouro->setRotulo    ( "Código do Logradouro"  );
$obTxtCodigoLogradouro->setName      ( "inCodLogradouro"       );
$obTxtCodigoLogradouro->setSize      ( 8                       );
$obTxtCodigoLogradouro->setMaxLength ( 8                       );
$obTxtCodigoLogradouro->setInteiro   ( true                    );
$obTxtCodigoLogradouro->setNull      ( false                     );
$obTxtCodigoLogradouro->setValue     ( $inProxCodLogradouro      );

$obTxtCodTipo = new TextBox;
$obTxtCodTipo->setRotulo    ( "Tipo"                    );
$obTxtCodTipo->setId        ( "inCodigoTipo"            );
$obTxtCodTipo->setName      ( "inCodigoTipo"            );
$obTxtCodTipo->setValue     ( $_REQUEST["inCodigoTipo"] );
$obTxtCodTipo->setSize      ( 8                         );
$obTxtCodTipo->setMaxLength ( 8                         );
$obTxtCodTipo->setNull      ( false                     );

$obTxtNome = new TextBox;
$obTxtNome->setRotulo    ( "Nome Atual"                  );
$obTxtNome->setTitle     ( "Nome do logradouro"          );
$obTxtNome->setName      ( "stNomeLogradouro"            );
$obTxtNome->setSize      ( 70                            );
$obTxtNome->setMaxLength ( 60                            );
$obTxtNome->setNull      ( false                         );
$obTxtNome->setValue     ( str_replace('\\', '', $_REQUEST["stNomeLogradouro"]));

$obBtnIncluirNovoBairro = new Button;
$obBtnIncluirNovoBairro->setName              ( "btnIncluirNovoBairro"   );
$obBtnIncluirNovoBairro->setValue             ( "Incluir Novo Bairro"    );
$obBtnIncluirNovoBairro->setTipo              ( "button"                 );
$obBtnIncluirNovoBairro->obEvento->setOnClick ( "incluirNovoBairro();"   );

$obTxtNovoBairro = new TextBox;
$obTxtNovoBairro->setRotulo    ( "Novo Bairro"  );
$obTxtNovoBairro->setName      ( "stNovoBairro" );
$obTxtNovoBairro->setSize      ( 60 );
$obTxtNovoBairro->setMaxLength ( 120 );
$obTxtNovoBairro->setNull      ( true );

$obTxtCodBairro = new TextBox;
$obTxtCodBairro->setRotulo    ( "*Bairro"                   );
$obTxtCodBairro->setName      ( "inCodigoBairro"            );
$obTxtCodBairro->setValue     ( $_REQUEST["inCodigoBairro"] );
$obTxtCodBairro->setSize      ( 8                           );
$obTxtCodBairro->setMaxLength ( 8                           );
$obTxtCodBairro->setInteiro   ( true                        );

$obTxtCEP = new CEP;
$obTxtCEP->setRotulo ( "*CEP"           );
$obTxtCEP->setName   ( "inCEP"          );

$obTxtInicial = new TextBox;
$obTxtInicial->setRotulo    ( "Número Inicial" );
$obTxtInicial->setName      ( "inInicial"             );
$obTxtInicial->setSize      ( 8                       );
$obTxtInicial->setMaxLength ( 6                       );
$obTxtInicial->setInteiro   ( true                    );

$obTxtFinal = new TextBox;
$obTxtFinal->setRotulo    ( "Número Final" );
$obTxtFinal->setName      ( "inFinal"             );
$obTxtFinal->setSize      ( 8                     );
$obTxtFinal->setMaxLength ( 6                     );
$obTxtFinal->setInteiro   ( true                  );

$obTxtCodUF = new TextBox;
$obTxtCodUF->setRotulo             ( "Estado"                 );
$obTxtCodUF->setName               ( "inCodigoUF"             );
$obTxtCodUF->setValue              ( $_REQUEST["inCodigoUF"]  );
$obTxtCodUF->setSize               ( 8                        );
$obTxtCodUF->setMaxLength          ( 8                        );
$obTxtCodUF->setNull               ( false                    );
$obTxtCodUF->setInteiro            ( true                     );
$obTxtCodUF->obEvento->setOnChange ( "preencheMunicipio('');" );

$obTxtCodMunicipio = new TextBox;
$obTxtCodMunicipio->setRotulo             ( "Município"             );
$obTxtCodMunicipio->setName               ( "inCodigoMunicipio"            );
$obTxtCodMunicipio->setValue              ( $_REQUEST["inCodigoMunicipio"] );
$obTxtCodMunicipio->setSize               ( 8                              );
$obTxtCodMunicipio->setMaxLength          ( 8                              );
$obTxtCodMunicipio->setNull               ( false                          );
$obTxtCodMunicipio->setInteiro            ( true                           );
$obTxtCodMunicipio->obEvento->setOnChange ( "preencheBairro();"            );

$obTxtCodLogradouro = new Label;
$obTxtCodLogradouro->setRotulo ( "Código"                        );
$obTxtCodLogradouro->setName   ( "inCodigoLogradouro"            );
$obTxtCodLogradouro->setValue  ( $_REQUEST["inCodigoLogradouro"] );

if ($stAcao == "alterar" || $stAcao == "renomear") {
    $stFiltro = ' WHERE cod_logradouro = '.$_REQUEST['inCodigoLogradouro'];
    $obTCIMTrecho = new TCIMTrecho();
    $obTCIMTrecho->retornaSomaExtensao($rsRecordSet, $stFiltro);

    $obLblExtensao = new Label;
    $obLblExtensao->setRotulo ( "Extensao" );
    $obLblExtensao->setName   ( "stExtensao"  );
    $obLblExtensao->setValue  ( $rsRecordSet->getCampo('extensao_total'  ));
}
//-------------------------------------------------------- FIM TEXT BOX

//-------------------------------------------------------- COMBOS
$obCmbUF = new Select;
$obCmbUF->setName               ( "inCodUF"                 );
$obCmbUF->addOption             ( "", "Selecione"           );
$obCmbUF->setCampoId            ( "cod_uf"                  );
$obCmbUF->setCampoDesc          ( "nom_uf"                  );
$obCmbUF->preencheCombo         ( $rsUF                     );
$obCmbUF->setValue	        ( $_REQUEST["inCodigoUF"]   );
$obCmbUF->setNull               ( false                     );
$obCmbUF->setStyle              ( "width: 220px" 	    );
$obCmbUF->obEvento->setOnChange ( "preencheMunicipio('');"  );

$obCmbMunicipio = new Select;
$obCmbMunicipio->setName                ( "inCodMunicipio"               );
$obCmbMunicipio->addOption              ( "", "Selecione"                );
$obCmbMunicipio->setCampoId             ( "cod_municipio"                );
$obCmbMunicipio->setCampoDesc           ( "nom_municipio"                );
$obCmbMunicipio->setValue               ( $_REQUEST["inCodigoMunicipio"] );
$obCmbMunicipio->preencheCombo          ( $rsMunicipios                  );
$obCmbMunicipio->setNull                ( false                          );
$obCmbMunicipio->setStyle               ( "width: 220px" 	         );
$obCmbMunicipio->obEvento->setOnChange  ( "preencheBairro();"            );

$obCmbTipo = new Select;
$obCmbTipo->setName       ( "inCodTipo"               );
$obCmbTipo->setValue      ( $_REQUEST["inCodigoTipo"] );
$obCmbTipo->addOption     ( "", "Selecione"           );
$obCmbTipo->setCampoId    ( "cod_tipo"                );
$obCmbTipo->setCampoDesc  ( "nom_tipo"                );
$obCmbTipo->preencheCombo ( $rsTipos                  );
$obCmbTipo->setNull       ( false                     );

$obCmbBairro = new Select;
$obCmbBairro->setName       ( "inCodBairro"               );
$obCmbBairro->addOption     ( "", "Selecione"             );
$obCmbBairro->setCampoId    ( "cod_bairro"                );
$obCmbBairro->setCampoDesc  ( "nom_bairro"                );
$obCmbBairro->setValue      ( $_REQUEST["inCodigoBairro"] );
$obCmbBairro->preencheCombo ( $rsBairros                  );
$obCmbBairro->setStyle      ( "width: 220px" 	         );
//-------------------------------------------------------- FIM COMBOS

//-------------------------------------------------------- BOTOES
$obBtnIncluirBairro = new Button;
$obBtnIncluirBairro->setName              ( "btnIncluirBairro"       );
$obBtnIncluirBairro->setValue             ( "Incluir"                );
$obBtnIncluirBairro->setTipo              ( "button"                 );
$obBtnIncluirBairro->obEvento->setOnClick ( "incluirBairro();"       );

$obBtnLimpar = new Button;
$obBtnLimpar->setName              ( "btnLimparBairro"        );
$obBtnLimpar->setValue             ( "Limpar"                 );
$obBtnLimpar->obEvento->setOnClick ( "limparBairro();"        );

$arBotoesBairro = array ($obBtnIncluirBairro, $obBtnLimpar );

$obBtnIncluirCEP = new Button;
$obBtnIncluirCEP->setName              ( "btnIncluirCEP" );
$obBtnIncluirCEP->setValue             ( "Incluir"       );
$obBtnIncluirCEP->setTipo              ( "button"        );
$obBtnIncluirCEP->obEvento->setOnClick ( "incluirCEP();" );

$obBtnLimparCEP = new Button;
$obBtnLimparCEP->setName              ( "btnLimparCEP" );
$obBtnLimparCEP->setValue             ( "Limpar"       );
$obBtnLimparCEP->obEvento->setOnClick ( "limparCEP();" );

$arBotoesCEP = array ( $obBtnIncluirCEP , $obBtnLimparCEP );

$obBtnOk = new OK;
$obBtnOk->obEvento->setOnClick("verificaCodigoLogradouro();");

$obBtnLimpar = new Limpar;
$obBtnLimpar->obEvento->setOnClick( "limparListas();" );
$obBtnCancelar = new Cancelar;
if ($stAcao == "incluir") {
    $obBtnCancelar->obEvento->setOnClick( "CancelarFormFL();" );
} else {
    $obBtnCancelar->obEvento->setOnClick( "CancelarForm();" );
}

$arBotaoAcao = array( $obBtnOk, $obBtnLimpar, $obBtnCancelar );

//-------------------------------------------------------- FIM BOTOES

//-------------------------------------------------------- RADIO BUTTONS
$obRdnTodos = new Radio;
$obRdnTodos->setRotulo  ( "Numeração" );
$obRdnTodos->setLabel   ( "Todos"            );
$obRdnTodos->setValue   ( "Todos"            );
$obRdnTodos->setChecked ( true               );
$obRdnTodos->setName    ( "boNumeracao"      );

$obRdnPares = new Radio;
$obRdnPares->setRotulo  ( "Numeração" );
$obRdnPares->setLabel   ( "Pares"            );
$obRdnPares->setValue   ( "Pares"            );
$obRdnPares->setChecked ( false              );
$obRdnPares->setName    ( "boNumeracao"      );

$obRdnImpares = new Radio;
$obRdnImpares->setRotulo  ( "Numeração"   );
$obRdnImpares->setLabel   ( "Ímpares"     );
$obRdnImpares->setValue   ( "Ímpares"     );
$obRdnImpares->setChecked ( false         );
$obRdnImpares->setName    ( "boNumeracao" );

$ArRdnCEP = array ($obRdnTodos,$obRdnPares,$obRdnImpares);

//-------------------------------------------------------- FIM RADIO BUTTONS

//-------------------------------------------------------- SPANS
$obSpnListarBairro = New Span;
$obSpnListarBairro->setId ( "spanListarBairro" );

$obSpnListarCEP = New Span;
$obSpnListarCEP->setId ( "spanListarCEP" );

$obSpnListarHistorico = New Span;
$obSpnListarHistorico->setId ( "spanListarHistorico" );

//-------------------------------------------------------- FIM SPANS
if ($stAcao <>'incluir') {

    //-------------------------------------------------------- OUTROS

        //MANTEM O CODIGO DO MUNICIPIO PARA RENOMEAR OU ALTERAR
        Sessao::remove('sessao_transf4');

        Sessao::write('cod_municipio', $_REQUEST['inCodigoMunicipio']);
        Sessao::write('cod_uf'       , $_REQUEST['inCodigoUF']);

        //BAIRRO
        $obRCIMLogradouro->setCodigoUF         ( $_REQUEST["inCodigoUF"]         );
        $obRCIMLogradouro->setCodigoLogradouro ( $_REQUEST["inCodigoLogradouro"] );
        $obRCIMLogradouro->listarBairroLogradouro ( $rsBairrosLogradouro );
        $obRCIMLogradouro->listarCEP              ( $rsCEPLogradouro     );

        $arBairrosSessao = $rsBairrosLogradouro->getElementos() ? $rsBairrosLogradouro->getElementos() : array();
        $arCepSessao     = $rsCEPLogradouro->getElementos() ? $rsCEPLogradouro->getElementos() : array();

        Sessao::write('bairros', $arBairrosSessao);
        Sessao::write('cep'    , $arCepSessao);

    //-------------------------------------------------------- FIM OUTROS
}

$obForm = new Form;
$obForm->setAction ( $pgProc  );
$obForm->setTarget ( 'oculto'  );

$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addTitulo ( "Dados para Logradouro" );

$obFormulario->addHidden ( $obHdnCtrl );
$obFormulario->addHidden ( $obHdnAcao );
$obFormulario->addHidden ( $obHdnCodLogradouro );
$obFormulario->addHidden ( $obHdnCampoNome );
$obFormulario->addHidden ( $obHdnCampoNum );
$obFormulario->addHidden ( $obHdnPais );
$obFormulario->addHidden ( $obHdnCadastro );
$obFormulario->addHidden ( $obHdninCodNorma );
$obFormulario->addHidden ( $obHdnstDataInicial );
$obFormulario->addHidden ( $obHdnstDataFinal );

if ($stAcao == 'renomear') {

$obFormulario->addHidden ( $obHdnNomeAnterior );
$obFormulario->addComponente ( $obLblNomeAnterior );

} elseif ($stAcao == 'alterar') {

    $obFormulario->addHidden        ( $obHdnCodUF         	);
    $obFormulario->addHidden        ( $obHdnCodMunicipio  	);
    $obFormulario->addHidden        ( $obHdnNomeUF         	);
    $obFormulario->addHidden        ( $obHdnNomeMunicipio  	);
    $obFormulario->addHidden        ( $obHdnNomeAnterior   	);
    $obFormulario->addComponente    ( $obLblCodLogradouro 	);

}

if ($stAcao == "incluir") {
    $obFormulario->addComponente         ( $obTxtCodigoLogradouro       );
}

$obFormulario->addComponenteComposto ( $obTxtCodTipo, $obCmbTipo );
$obFormulario->addComponente         ( $obTxtNome                );

if ($stAcao == "alterar" || $stAcao == "renomear") {

    $obFormulario->addComponente ( $obLblNomeUF        );
    $obFormulario->addComponente ( $obLblNomeMunicipio );
    $obFormulario->addComponente ( $obLblExtensao );
    $obFormulario->addSpan       ( $obSpnListarHistorico );

} elseif ($stAcao == "incluir") {

    $obFormulario->addComponenteComposto ( $obTxtCodUF, $obCmbUF               );
    $obFormulario->addComponenteComposto ( $obTxtCodMunicipio, $obCmbMunicipio );

}

$obFormulario->addTitulo             ( "Bairro" );
$obFormulario->agrupaComponentes     (array( $obTxtNovoBairro, $obBtnIncluirNovoBairro ));
$obFormulario->addComponenteComposto ( $obTxtCodBairro, $obCmbBairro );
$obFormulario->defineBarra           ( $arBotoesBairro,'center',''   );
$obFormulario->addSpan               ( $obSpnListarBairro            );

$obFormulario->addTitulo             ( "CEP"                         );
$obFormulario->addComponente         ( $obTxtCEP                     );
$obFormulario->addComponente         ( $obTxtInicial                 );
$obFormulario->addComponente         ( $obTxtFinal                   );
$obFormulario->agrupaComponentes     ( $ArRdnCEP );
$obFormulario->defineBarra           ( $arBotoesCEP,'center',''      );

$obFormulario->addSpan               ( $obSpnListarCEP            );

$obFormulario->defineBarra           ( $arBotaoAcao                  );

$obFormulario->show();
$obIFrame2->show();
$obIFrame->show();

if ($stAcao == 'alterar' || $stAcao == 'renomear') {
    sistemalegado::executaIFrameOculto("preencheInner();");
} else {
    sistemalegado::executaIFrameOculto("IniciaSessions();");
}

Sessao::write('acao'  , $acao);
Sessao::write('modulo', $modulo);

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
