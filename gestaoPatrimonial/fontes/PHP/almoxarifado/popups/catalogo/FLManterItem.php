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
    * Página de Filtro Classificação Contábil
    * Data de Criação   : 10/11/2004

    * @author Analista: Diego Victoria
    * @author Desenvolvedor: Leandro André Zis

    * Casos de uso: uc-03.03.06
                    uc-03.03.16
                    uc-03.03.17

    $Id: FLManterItem.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GP_ALM_NEGOCIO."RAlmoxarifadoCatalogoItem.class.php");
include_once(CAM_GP_ALM_NEGOCIO."RAlmoxarifadoCatalogoClassificacao.class.php");
include_once(CAM_GP_ALM_COMPONENTES . "IMontaClassificacao.class.php");
include_once(CAM_GP_ALM_COMPONENTES."IMontaCatalogoClassificacao.class.php");

$stPrograma = "ManterItem";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$pgProx = $pgList;

include_once($pgJs );

//***********************************************/
// Limpa a variavel de sessão para o filtro
//***********************************************/
Sessao::remove('filtro');
Sessao::remove('link');

Sessao::write('transf4', array());

$obRAlmoxarifadoCatalogoItem = new RAlmoxarifadoCatalogoItem;

$stAcao = $request->get("stAcao");

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnServico = new Hidden;
$obHdnServico->setName( "stServico" );

$obHdnParametroDinamico = new Hidden;
$obHdnParametroDinamico->setName ( "boParametroDinamico" );
$obHdnParametroDinamico->setValue( $request->get('boParametroDinamico') );

$obHdnFiltroBusca = new Hidden;
$obHdnFiltroBusca->setName ( "stFiltroBusca" );
$obHdnFiltroBusca->setValue( $request->get('stFiltroBusca') );

$obHdnUnidadeNaoInformado= new Hidden;
$obHdnUnidadeNaoInformado->setName( "stUnidadeNaoInformado" );

$obHdnTipoNaoInformado= new Hidden;
$obHdnTipoNaoInformado->setName( "stTipoNaoInformado" );

$obHdnVerificaSaldo = new Hidden;
$obHdnVerificaSaldo->setName( "boVerificaSaldo" );

$obHdnAlmoxarifadoOrigem = new Hidden;
$obHdnAlmoxarifadoOrigem->setName( "inCodAlmoxarifadoOrigem" );
if ($request->get('inCodAlmoxarifado')) {
    $obHdnAlmoxarifadoOrigem->setValue($request->get('inCodAlmoxarifado') );
} else {
    $obHdnAlmoxarifadoOrigem->setValue($request->get('inCodAlmoxarifadoOrigem') );
}

$obHdnVerificaMovimentacaoItem = new Hidden;
$obHdnVerificaMovimentacaoItem->setName ( "boVerificaMovimentacaoItem" );
$obHdnVerificaMovimentacaoItem->setValue( $_REQUEST['boVerificaMovimentacaoItem'] );

$obHdnPreencheUnidadeNaoInformada = new Hidden;
$obHdnPreencheUnidadeNaoInformada->setName( "boPreencheUnidadeNaoInformada" );

$obHdnExibeTipo = new Hidden;
$obHdnExibeTipo->setName( 'boExibeTipo' );

$obHdnPreencheTipoNaoInformado = new Hidden;
$obHdnPreencheTipoNaoInformado->setName( 'boPreencheTipoNaoInformado' );
$boServico = $request->get("boServico");
if (isset($boServico)) {
    $obHdnServico->setValue( false );
} else {
    $obHdnServico->setValue( true  );
}

$boUnidadeNaoInformado = $_REQUEST["boUnidadeNaoInformado"];
if ($boUnidadeNaoInformado) {
    $obHdnUnidadeNaoInformado->setValue( true );
} else {
    $obHdnUnidadeNaoInformado->setValue( false  );
}

$boTipoNaoInformado = $request->get("boTipoNaoInformado");
if ($boTipoNaoInformado) {
    $obHdnTipoNaoInformado->setValue( true );
} else {
    $obHdnTipoNaoInformado->setValue( false  );
}

$boVerificaSaldo = $request->get("boVerificaSaldo");
if ($boVerificaSaldo) {
    $obHdnVerificaSaldo->setValue( true );
} else {
    $obHdnVerificaSaldo->setValue( false );
}

$boPreencheUnidadeNaoInformada = $_REQUEST["boPreencheUnidadeNaoInformada"];
if ($boPreencheUnidadeNaoInformada == 'true') {
    $obHdnPreencheUnidadeNaoInformada->setValue( 'true' );
} else {
    $obHdnPreencheUnidadeNaoInformada->setValue( 'false' );
}

$boExibeTipo = $_REQUEST["boExibeTipo"];
if ($boExibeTipo == 'true') {
    $obHdnExibeTipo->setValue( 'true' );
} else {
    $obHdnExibeTipo->setValue( 'false' );
}

$boPreencheTipoNaoInformado = $_REQUEST["boPreencheTipoNaoInformado"];
if ($boPreencheTipoNaoInformado == 'true') {
    $obHdnPreencheTipoNaoInformado->setValue( 'true' );
} else {
    $obHdnPreencheTipoNaoInformado->setValue( 'false' );
}

$obHdnAtivo = new Hidden;
$obHdnAtivo->setName( 'boAtivo' );
$boAtivo = $request->get('boAtivo');
if ($boAtivo == 'true') {
    $obHdnAtivo->setValue( 'true' );
}

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnForm = new Hidden;
$obHdnForm->setName( "nomForm" );
$obHdnForm->setValue( $_REQUEST['nomForm'] );

$obHdnCampoNum = new Hidden;
$obHdnCampoNum->setName( "campoNum" );
$obHdnCampoNum->setValue( $_REQUEST['campoNum'] );

//Define HIDDEN com o o nome do campo texto
$obHdnCampoNom = new Hidden;
$obHdnCampoNom->setName( "campoNom" );
$obHdnCampoNom->setValue( $_REQUEST['campoNom'] );

$obHdnUnidadeMedida = new Hidden;
$obHdnUnidadeMedida->setName ( 'stNomUnidade' );
$obHdnUnidadeMedida->setValue( $request->get('stNomUnidade') );

if ($_REQUEST['nomCampoUnidade']) {
    $obHdnUnidade = new Hidden;
    $obHdnUnidade->setName ( "nomCampoUnidade" );
    $obHdnUnidade->setValue( $_REQUEST['nomCampoUnidade'] );
}

if ($_REQUEST['boVerificaMovimentacaoItem']) {
    $boVerificaMovimentacaoItem = new Hidden;
    $boVerificaMovimentacaoItem->setName ( "boVerificaMovimentacaoItem" );
    $boVerificaMovimentacaoItem->setValue( $_REQUEST['boVerificaMovimentacaoItem'] );
}

if ( $request->get('stCodEstruturalReduzido') && $_GET['inCodClassificacao'] && $_GET['inCodCatalogo'] ) {
    $obHdnCodCatalogo = new Hidden;
    $obHdnCodCatalogo->setName( "inCodCatalogo" );
    $obHdnCodCatalogo->setValue( $_GET['inCodCatalogo'] );

    $obHdnChaveClassificacao = new Hidden;
    $obHdnChaveClassificacao->setName( "stChaveClassificacao" );
    $obHdnChaveClassificacao->setValue( $_GET['stCodEstruturalReduzido'] );

    $obIMontaCatalogoClassificacao = new IMontaCatalogoClassificacao;
    $obIMontaCatalogoClassificacao->obIMontaClassificacao->setCodEstruturalReduzido($_GET['stCodEstruturalReduzido']);
    $obIMontaCatalogoClassificacao->obIMontaClassificacao->setCodigoClassificacao($_GET['inCodClassificacao']);
    $obIMontaCatalogoClassificacao->obIMontaClassificacao->setUltimoNivelRequerido(true);
    $obIMontaCatalogoClassificacao->obIMontaClassificacao->setClassificacaoRequerida(true);
    $obIMontaCatalogoClassificacao->setCodCatalogo($_GET['inCodCatalogo']);
    $obIMontaCatalogoClassificacao->setReadOnly(true);

    $pgOcul  = CAM_GP_ALM_PROCESSAMENTO.'OCIMontaCatalogoClassificacao.php?'.$sessao->id;
    echo "<script>ajaxJavaScript( '".$pgOcul."&inCodCatalogo=".$_GET['inCodCatalogo']."', 'montaClassificacao');</script>";
} else {
    $obIMontaCatalogoClassificacao = new IMontaCatalogoClassificacao;
    $obIMontaCatalogoClassificacao->obITextBoxSelectCatalogo->setNaoPermiteManutencao(true);
    $obIMontaCatalogoClassificacao->obIMontaClassificacao->setUltimoNivelRequerido(false);
    $obIMontaCatalogoClassificacao->obIMontaClassificacao->setClassificacaoRequerida(false);
}

$obSpnListaAtributos = new Span;
$obSpnListaAtributos->setID('spnListaAtributos');

$obTxtCodItem = new TextBox();
$obTxtCodItem->setRotulo                  ( "Código Item"                       );
$obTxtCodItem->setName                    ( "inCodItem"                         );
$obTxtCodItem->setValue                   ( isset($inCodItem) ? $inCodItem : "" );
$obTxtCodItem->setTitle                   ( "Informe o código do item."         );

$obTxtDescricao = new TextBox();
$obTxtDescricao->setRotulo                ( "Descrição"                             );
$obTxtDescricao->setName                  ( "stDescricao"                           );
$obTxtDescricao->setValue                 ( isset($stDescricao) ? $stDescricao : "" );
$obTxtDescricao->setMaxLength             ( 80                                      );
$obTxtDescricao->setSize                  ( 50                                      );
$obTxtDescricao->setTitle                 ("Informe a descrição do item"            );
$obCmpTipoBusca = new TipoBusca( $obTxtDescricao );

$obRAlmoxarifadoCatalogoItem->obRAlmoxarifadoTipoItem->listar( $rsTipo ) ;
$arRdTipo = array();
$inCodTipo = 0;

$obRdTipo = new Radio;
$obRdTipo->setRotulo                      ( "Tipo"                                  );
$obRdTipo->setTitle                       ( "Selecione o tipo de item desejado."    );
$obRdTipo->setName                        ( "inCodTipo"                             );
$obRdTipo->setLabel                       ( "Todos"                                 );
$obRdTipo->setValue                       ( "0"                                     );
$obRdTipo->setChecked                     ( true                                    );
$obRdTipo->setNull                        ( false                                   );
$arRdTipo[] = $obRdTipo;

for ($i = 0; $i < $rsTipo->getNumLinhas(); $i++) {
   if ($rsTipo->getCampo('cod_tipo') != 0) {
      $obRdTipo = new Radio;
      $obRdTipo->setRotulo               ( "Tipo"                                      );
      $obRdTipo->setName                 ( "inCodTipo"                                 );
      $obRdTipo->setLabel                ( $rsTipo->getCampo('descricao')              );
      $obRdTipo->setValue                ( $rsTipo->getCampo('cod_tipo')               );

        if (isset($boServico)) {
          if ($boServico == false) {
             if ($rsTipo->getCampo('cod_tipo')==3) {
                  $obRdTipo->setDisabled ( true                                        );
              }
          }
      }

      $obRdTipo->setChecked              ( $inCodTipo == $rsTipo->getCampo('cod_tipo') );
      $obRdTipo->setNull                 ( false                                       );
      $arRdTipo[] = $obRdTipo;
      $rsTipo->proximo();
   }
}

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction                  ( $pgList );
//$obForm->setTarget                  ( "telaPrincipal" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm  ( $obForm            );
$obFormulario->addHidden( $obHdnAcao         );
$obFormulario->addHidden( $obHdnServico      );
$obFormulario->addHidden( $obHdnParametroDinamico );
$obFormulario->addHidden( $obHdnUnidadeNaoInformado );
$obFormulario->addHidden( $obHdnTipoNaoInformado );
$obFormulario->addHidden( $obHdnCtrl         );
$obFormulario->addHidden( $obHdnForm         );
$obFormulario->addHidden( $obHdnCampoNum     );
$obFormulario->addHidden( $obHdnCampoNom     );
$obFormulario->addHidden( $obHdnUnidadeMedida);
$obFormulario->addHidden( $obHdnVerificaSaldo);
$obFormulario->addHidden( $obHdnPreencheUnidadeNaoInformada );
$obFormulario->addHidden( $obHdnExibeTipo );
$obFormulario->addHidden( $obHdnAlmoxarifadoOrigem );
$obFormulario->addHidden( $obHdnVerificaMovimentacaoItem );
$obFormulario->addHidden( $obHdnPreencheTipoNaoInformado );
$obFormulario->addHidden( $obHdnAtivo );
$obFormulario->addHidden( $obHdnFiltroBusca );

if ($_REQUEST['boVerificaMovimentacaoItem']) {
    $obFormulario->addHidden( $boVerificaMovimentacaoItem );
}

if ($_REQUEST['nomCampoUnidade']) {
    $obFormulario->addHidden( $obHdnUnidade );
}
if ( $request->get('stCodEstruturalReduzido') && $_GET['inCodClassificacao'] && $_GET['inCodCatalogo'] ) {
    $obFormulario->addHidden( $obHdnCodCatalogo );
    $obFormulario->addHidden( $obHdnChaveClassificacao );
}

$obFormulario->addTitulo            ( "Dados para Filtro" );
$obIMontaCatalogoClassificacao->geraFormulario($obFormulario);
$obFormulario->addSpan              ( $obSpnListaAtributos    );
$obFormulario->addComponente        ( $obTxtCodItem );
$obFormulario->addComponente        ( $obCmpTipoBusca );
$obFormulario->agrupaComponentes    ( $arRdTipo );

$obIFrame = new IFrame;
$obIFrame->setName("telaMensagem");
$obIFrame->setWidth("100%");
$obIFrame->setHeight("50");

$obFormulario->OK();

$obFormulario->addIFrameOculto("oculto");
$obFormulario->obIFrame->setWidth("100%");
$obFormulario->obIFrame->setHeight("0");
$obFormulario->show();
$obIFrame->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
