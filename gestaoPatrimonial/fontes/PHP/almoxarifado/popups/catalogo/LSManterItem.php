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
    * Página de Formulário Almoxarifado
    * Data de Criação   : 28/10/2005

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Er Galvão Abbott

    * @ignore

    * Casos de uso: uc-03.03.06
                    uc-03.03.16
                    uc-03.03.17

    $Id: LSManterItem.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GP_ALM_NEGOCIO . "RAlmoxarifadoCatalogoItem.class.php");

$stPrograma = "ManterItem";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

if ( $request->get("pg") and  $request->get("pos") ) {
    $link["pg"]  = $_GET["pg"];
    $link["pos"]  = $_GET["pos"];
    Sessao::write('link', $link);
} elseif ( is_array(Sessao::read('link')) ) {
    $_GET = Sessao::read('link');
    $_REQUEST = Sessao::read('link');
    Sessao::write('link', '');
} else {
    foreach ($_REQUEST as $key => $valor) {
        $link[$key] = $valor;
    }
    Sessao::write('link', $link);
}

//DEFINIÇÃO DOS POSSÍVEIS COMPONENTES
//Unidade de Medida em Label
    $obLabelUnidadeMedida = new Label;
    $obLabelUnidadeMedida->setRotulo ( 'Unidade de Medida' );
    $obLabelUnidadeMedida->setId     ( 'stUnidadeMedida'   );
    $obLabelUnidadeMedida->setValue  ( '&nbsp;'            );

    $obHiddenCodUnidadeMedida = new Hidden;
    $obHiddenCodUnidadeMedida->setName  ( 'inCodUnidadeMedida'             );
    $obHiddenCodUnidadeMedida->setId    ( 'inCodUnidadeMedida'             );

    $obHiddenNomUnidadeMedida = new Hidden;
    $obHiddenNomUnidadeMedida->setName  ( 'stNomUnidade' );
    $obHiddenNomUnidadeMedida->setId    ( 'stNomUnidade' );

//Unidade de Medida em Combo
    include_once(CAM_GA_ADM_COMPONENTES . "ISelectUnidadeMedida.class.php");
    $obISelectUnidadeMedida = new ISelectUnidadeMedida;
    $obISelectUnidadeMedida->setObrigatorioBarra( true );
    $obISelectUnidadeMedida->setName  ( 'inCodUnidadeMedida' );
    $obISelectUnidadeMedida->setId    ( 'inCodUnidadeMedida' );
    $obISelectUnidadeMedida->setTitle ( 'Informe a Unidade de Medida.' );

//Tipo em Label
    $obLabelTipo = new Label;
    $obLabelTipo->setRotulo( 'Tipo'     );
    $obLabelTipo->setId    ( 'stTipo'   );
    $obLabelTipo->setValue ( '&nbsp;' );

    $obHiddenCodTipo = new Hidden;
    $obHiddenCodTipo->setName  ( 'inCodTipo' );
    $obHiddenCodTipo->setId    ( 'inCodTipo' );

    $obHiddenNomTipo = new Hidden;
    $obHiddenNomTipo->setName  ( 'stNomTipo' );
    $obHiddenNomTipo->setId    ( 'stNomTipo' );

//Tipo em Radio
    include_once(CAM_GP_ALM_NEGOCIO."RAlmoxarifadoTipoItem.class.php");
    $obTipoItem = new RAlmoxarifadoTipoItem;
    $obTipoItem->listar( $rsTipoItem );
    $arRdTipo = array();
    for ($i = 0; $i < $rsTipoItem->getNumLinhas(); $i++) {
        if ($rsTipoItem->getCampo('cod_tipo') != 0) {
            $obRdTipo = new Radio;
            $obRdTipo->setRotulo           ( "Tipo"                             );
            $obRdTipo->setName             ( "inCodTipo"                        );
            $obRdTipo->setId               ( "inCodTipo$i"                      );
            $obRdTipo->setLabel            ( $rsTipoItem->getCampo('descricao') );
            $obRdTipo->setValue            ( $rsTipoItem->getCampo('cod_tipo')  );
            $obRdTipo->setChecked          ( ( $i == 0 )                        );
            $obRdTipo->setDisabled         ( ( $rsTipoItem->getCampo('cod_tipo') == 3 ) );
            $obRdTipo->setObrigatorioBarra ( true                               );
            $arRdTipo[] = $obRdTipo;
            $rsTipoItem->proximo();
        }
    }

//MONTA HTML
$obFormularioUnidadeMedidaSelect = new Formulario;
$obFormularioUnidadeMedidaSelect->addComponente ( $obISelectUnidadeMedida );
$obFormularioUnidadeMedidaSelect->montaInnerHTML();
$stHtmlUnidadeMedidaSelect = $obFormularioUnidadeMedidaSelect->getHTML();

$obFormularioUnidadeMedidaLabel = new Formulario;
$obFormularioUnidadeMedidaLabel->addComponente ( $obLabelUnidadeMedida     );
$obFormularioUnidadeMedidaLabel->addHidden     ( $obHiddenCodUnidadeMedida );
$obFormularioUnidadeMedidaLabel->addHidden     ( $obHiddenNomUnidadeMedida );
$obFormularioUnidadeMedidaLabel->montaInnerHTML();
$stHtmlUnidadeMedidaLabel = $obFormularioUnidadeMedidaLabel->getHTML();

$obFormularioTipoRadio = new Formulario;
$obFormularioTipoRadio->agrupaComponentes ( $arRdTipo );
$obFormularioTipoRadio->montaInnerHTML();
$stHtmlTipoRadio = $obFormularioTipoRadio->getHTML();

$obFormularioTipoLabel = new Formulario;
$obFormularioTipoLabel->addComponente ( $obLabelTipo     );
$obFormularioTipoLabel->addHidden     ( $obHiddenCodTipo );
$obFormularioTipoLabel->addHidden     ( $obHiddenNomTipo );
$obFormularioTipoLabel->montaInnerHTML();
$stHtmlTipoLabel = $obFormularioTipoLabel->getHTML();

$obJavaScript = new JavaScript ();
$obJavaScript->addComponente( $obISelectUnidadeMedida );
$obJavaScript->montaJavaScript();
$jsValida = $obJavaScript->getInnerJavaScript();
$jsValida = str_replace( "\n", "", $jsValida );

$stFncJavaScript  = " function insereItem(num, nom, cod_uni, uni, cod_tipo, desc_tipo) {  \n";
$stFncJavaScript .= "     var sNum;                  \n";
$stFncJavaScript .= "     var sNom;                  \n";
$stFncJavaScript .= "     var sUni;                  \n";
$stFncJavaScript .= "     sNum = num;                \n";
$stFncJavaScript .= "     sNom = nom;                \n";
$stFncJavaScript .= "     sUni = uni;                \n";

$stFncJavaScript .= "     var boPreencheLabelUnidade = false; \n";
$stFncJavaScript .= "     var boPreencheLabelTipo    = false; \n";

$stFncJavaScript .= "     var boExibeUnidade = ".( ($request->get('nomCampoUnidade') != '') ? 'true' : 'false' )."; \n";
$stFncJavaScript .= "     var boExibeTipo    = ".( ($_REQUEST['boExibeTipo'] == 'true') ? 'true' : 'false' )."; \n";
$stFncJavaScript .= "     var boPreencheUnidadeNaoInformada = ".( ($_REQUEST['boPreencheUnidadeNaoInformada'] == 'true') ? 'true' : 'false' )."; \n";
$stFncJavaScript .= "     var boPreencheTipoNaoInformado    = ".( ($_REQUEST['boPreencheTipoNaoInformado'] == 'true' ) ? 'true' : 'false' )."; \n";

$stFncJavaScript .= "     window.opener.parent.frames['telaPrincipal'].document.getElementById('".$_REQUEST["campoNom"]."').innerHTML = sNom; \n";
$stFncJavaScript .= "     window.opener.parent.frames['telaPrincipal'].document.".$_REQUEST["nomForm"].".".$_REQUEST["campoNom"  ].".value = sNom; \n";
$stFncJavaScript .= "     window.opener.parent.frames['telaPrincipal'].document.".$_REQUEST["nomForm"].".".$_REQUEST["campoNum"  ].".value = sNum; \n";
$stFncJavaScript .= "     window.opener.parent.frames['telaPrincipal'].document.".$_REQUEST["nomForm"].".".$_REQUEST["campoNum"  ].".focus(); \n";

$stFncJavaScript .= "     if (boExibeUnidade) { \n";
$stFncJavaScript .= "         if ( boPreencheUnidadeNaoInformada && (cod_uni == '0-0') ) { \n";
$stFncJavaScript .= "             if (boExibeTipo) { \n";
$stFncJavaScript .= "                 if ( boPreencheTipoNaoInformado && (cod_tipo == '0') ) { \n";
$stFncJavaScript .= "                     window.opener.parent.frames['telaPrincipal'].document.getElementById('spnInformacoesItem').innerHTML = '".$stHtmlUnidadeMedidaSelect.$stHtmlTipoRadio."' \n";
$stFncJavaScript .= "                 } else { \n";
$stFncJavaScript .= "                     window.opener.parent.frames['telaPrincipal'].document.getElementById('spnInformacoesItem').innerHTML = '".$stHtmlUnidadeMedidaSelect.$stHtmlTipoLabel."' \n";
$stFncJavaScript .= "                     boPreencheLabelTipo = true; \n";
$stFncJavaScript .= "                 } \n";
$stFncJavaScript .= "             } else { \n";
$stFncJavaScript .= "                 window.opener.parent.frames['telaPrincipal'].document.getElementById('spnInformacoesItem').innerHTML = '".$stHtmlUnidadeMedidaSelect."' \n";
$stFncJavaScript .= "             } \n";
$stFncJavaScript .= "             window.opener.parent.frames['telaPrincipal'].document.frm.hdnUnidadeMedidaValida.value = '".$jsValida."' \n";
$stFncJavaScript .= "         } else { \n";
$stFncJavaScript .= "             if (boExibeTipo) { \n";
$stFncJavaScript .= "                 if ( boPreencheTipoNaoInformado && (cod_tipo == '0') ) { \n";
$stFncJavaScript .= "                     window.opener.parent.frames['telaPrincipal'].document.getElementById('spnInformacoesItem').innerHTML = '".$stHtmlUnidadeMedidaLabel.$stHtmlTipoRadio."' \n";
$stFncJavaScript .= "                     boPreencheLabelUnidade = true; \n";
$stFncJavaScript .= "                 } else { \n";
$stFncJavaScript .= "                     window.opener.parent.frames['telaPrincipal'].document.getElementById('spnInformacoesItem').innerHTML = '".$stHtmlUnidadeMedidaLabel.$stHtmlTipoLabel."' \n";
$stFncJavaScript .= "                     boPreencheLabelUnidade = true; \n";
$stFncJavaScript .= "                     boPreencheLabelTipo    = true; \n";
$stFncJavaScript .= "                 } \n";
$stFncJavaScript .= "             } else { \n";
$stFncJavaScript .= "              if (window.opener.parent.frames['telaPrincipal'].document.getElementById('spnInformacoesItem')) {\n";
$stFncJavaScript .= "                 window.opener.parent.frames['telaPrincipal'].document.getElementById('spnInformacoesItem').innerHTML = '".$stHtmlUnidadeMedidaLabel."' \n";
$stFncJavaScript .= "}";
$stFncJavaScript .= "                 boPreencheLabelUnidade = true; \n";
$stFncJavaScript .= "             } \n";
$stFncJavaScript .= "         }\n";

$stFncJavaScript .= "         if (boPreencheLabelUnidade) { \n";
$stFncJavaScript .= "          if (window.opener.parent.frames['telaPrincipal'].document.getElementById('stUnidadeMedida')) {";
$stFncJavaScript .= "             window.opener.parent.frames['telaPrincipal'].document.getElementById('stUnidadeMedida').innerHTML = sUni; \n";
$stFncJavaScript .= "             window.opener.parent.frames['telaPrincipal'].document.frm.stNomUnidade.value = sUni; \n";
$stFncJavaScript .= "          }";
$stFncJavaScript .= "          if (window.opener.parent.frames['telaPrincipal'].document.frm.inCodUnidadeMedida) {";
$stFncJavaScript .= "             window.opener.parent.frames['telaPrincipal'].document.frm.inCodUnidadeMedida.value = cod_uni; \n";
$stFncJavaScript .= "          }";
$stFncJavaScript .= "         } \n";
$stFncJavaScript .= "         if (boPreencheLabelTipo) {\n";
$stFncJavaScript .= "             window.opener.parent.frames['telaPrincipal'].document.getElementById('stTipo').innerHTML = desc_tipo; \n";
$stFncJavaScript .= "             window.opener.parent.frames['telaPrincipal'].document.frm.inCodTipo.value = cod_tipo; \n";
$stFncJavaScript .= "             window.opener.parent.frames['telaPrincipal'].document.frm.stNomTipo.value = desc_tipo; \n";
$stFncJavaScript .= "         }\n";

$stFncJavaScript .= "     }\n";

if (isset($_REQUEST['boParametroDinamico'])) {
    if (!(empty($_REQUEST['boParametroDinamico']))) {
        if ($_REQUEST['boParametroDinamico']=="true") {
           $stFncJS.="window.opener.ajaxJavaScript('".CAM_GP_ALM_POPUPS."catalogo/OCManterItem.php?".Sessao::getId()."";
           $stFncJS.="&boServico=&boPreencheUnidadeNaoInformada=false&boExibeTipo=false";
           $stFncJS.="&boPreencheTipoNaoInformado=false&boParametroDinamico=true&nomCampoUnidade=stUnidadeMedida";
           $stFncJS.="&stNomCampoCod=inCodItem&stIdCampoDesc=stNomItem&stNomForm=frm&inCodigo='+sNum,'buscaPopup');";
           $stFncJS.="setTimeout('window.close();',500);";
        }

    }
}

(isset($stFncJS))? $stFncJavaScript.= $stFncJS : $stFncJavaScript .= " window.close();\n";
$stFncJavaScript .= " }\n";

$obAtributos = new MontaAtributos;
$obAtributos->setName      ( "Atributos_" );
$obAtributos->recuperaVetor( $arChave    );

$stCaminho = CAM_GP_ALM_INSTANCIAS."catalogo/";

$stAcao = $request->get("stAcao");
if (empty( $stAcao )) {
    $stAcao = "alterar";
}

if ( $request->get('inCodigo')) {
    foreach ($_REQUEST as $key => $value) {
        $filtro[$key] = $value;
    }
    Sessao::write('filtro', $filtro);
} else {
    if ( Sessao::read('filtro') ) {
        foreach ( Sessao::read('filtro') as $key => $value ) {
            $_REQUEST[$key] = $value;
        }
    }
    Sessao::write('paginando', true);
}

$obRegra = new RAlmoxarifadoCatalogoItem;
$stFiltro = "";
$stLink   = "";

$rsLista = new RecordSet;
$obRegra->setCodigo    ( $request->get('inCodItem')      );
$obRegra->setDescricao ( $request->get('stHdnDescricao') );

$inCodigoAlmoxarifadoOrigem = $request->get('inCodAlmoxarifadoOrigem');
if ($inCodigoAlmoxarifadoOrigem) {
    $obRegra->setCodigoAlmoxarifado($_REQUEST['inCodAlmoxarifadoOrigem']);
}

$stServico = $request->get("stServico");
if ($stServico) {
   $obRegra->setServico(true);
} else {
   $obRegra->setServico(false);
}

$stUnidadeNaoInformado = $_REQUEST["stUnidadeNaoInformado"];
if ($stUnidadeNaoInformado) {
   $obRegra->setUnidadeNaoInformado(true);
} else {
   $obRegra->setUnidadeNaoInformado(false);
}

$stTipoNaoInformado = $request->get("stTipoNaoInformado");
if ($stTipoNaoInformado) {
   $obRegra->setTipoNaoInformado(true);
} else {
   $obRegra->setTipoNaoInformado(false);
}

$boVerificaSaldo = $request->get("boVerificaSaldo");
if ($boVerificaSaldo) {
    $obRegra->setVerificaSaldo(true);
} else {
    $obRegra->setVerificaSaldo(false);
}

$boAtivo = $request->get("boAtivo");

if ($boAtivo) {
    $obRegra->setAtivo(true);
}

$obRegra->obRAlmoxarifadoClassificacao->obRAlmoxarifadoCatalogo->setCodigo($request->get('inCodCatalogo') );
$obRegra->obRAlmoxarifadoClassificacao->setEstrutural($request->get('stChaveClassificacao'));

$obRegra->obRAlmoxarifadoTipoItem->setCodigo($request->get('inCodTipo'));

$rsCodClassificacao = new RecordSet;
if ( $request->get('stChaveClassificacao') ) {
   $obRegra->obRAlmoxarifadoClassificacao->recuperaCodigoClassificacao($rsCodClassificacao, $_REQUEST['stChaveClassificacao'], $_REQUEST['inCodCatalogo']);
   $obRegra->obRAlmoxarifadoClassificacao->setCodigo( $rsCodClassificacao->getCampo('cod_classificacao') );
}
//monta array de atributos dinamicos
foreach ($arChave as $key => $value) {
    $arChaves = preg_split( "/[^a-zA-Z0-9]/", $key );
    $inCodAtributo = $arChaves[0];
    if ( is_array($value) ) {
       $value = implode( "," , $value );
    }
    $obRegra->obRAlmoxarifadoClassificacao->obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo , $value );
}

$boVerificaMovimentacaoItem = $request->get('boVerificaMovimentacaoItem');
if ($boVerificaMovimentacaoItem) {
    $obRegra->setVerificarMovimentacaoItem(true);
} else {
    $obRegra->setVerificarMovimentacaoItem(false);
}
if ( $request->get('stFiltroBusca') ) {
    if ( strpos($_REQUEST['stFiltroBusca'], 'SomenteComMovimentacao')!==false ) {
        $obRegra->boSomenteComMovimentacao = true;
    }
}

$stOrder = " ORDER BY aci.descricao ";

$obRegra->listar( $rsLista , $stOrder );

$rsLista->addFormatacao('descricao', 'HTML');

$obLista = new Lista;
$obLista->setRecordSet( $rsLista );

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Catálogo");
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Classificação" );
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Código" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Tipo" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Descrição" );
$obLista->ultimoCabecalho->setWidth( 50 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Unidade" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "[cod_catalogo] - [desc_catalogo]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_estrutural" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_item" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "desc_tipo" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "[descricao]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "[nom_unidade]" );
$obLista->commitDado();

$stAcao = "SELECIONAR";
$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->setFuncao( true );
$obLista->ultimaAcao->setLink( "JavaScript:insereItem();" );
$obLista->ultimaAcao->addCampo("1","cod_item");
$obLista->ultimaAcao->addCampo("2","descricao");
if ( $request->get('nomCampoUnidade') ) {
    $obLista->ultimaAcao->addCampo("3","[cod_unidade]-[cod_grandeza]");
    $obLista->ultimaAcao->addCampo("4","nom_unidade");
    $obLista->ultimaAcao->addCampo("5","cod_tipo");
    $obLista->ultimaAcao->addCampo("6","desc_tipo");
}
$obLista->commitAcao();
$obLista->show();

$obFormulario = new Formulario;
$obFormulario->obJavaScript->addFuncao( $stFncJavaScript );
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
