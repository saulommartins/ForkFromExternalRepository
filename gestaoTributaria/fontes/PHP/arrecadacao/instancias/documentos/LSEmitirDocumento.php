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
  * Página de Lista de Emissão de Documentos
  * Data de criação : 23/05/2007

  * @author Analista: Fábio Bertoldi
  * @author Programador: Fernando Piccini Cercato

  * @ignore

    * $Id: LSEmitirDocumento.php 59612 2014-09-02 12:00:51Z gelson $

  Caso de uso: uc-05.03.11
**/

/*
$Log$
Revision 1.1  2007/10/09 18:48:59  cercato
 Ticket#9281#

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GT_ARR_MAPEAMENTO."TARRDocumento.class.php" );

//Definicao dos nomes de arquivos
$stPrograma = "EmitirDocumento";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once( $pgJS );

$stAcao = $request->get('stAcao');
$stLink = "&stAcao=".$stAcao;

$link = Sessao::read("link", array());

if (isset($_GET["pg"]) && isset($_GET["pos"])) {
    $stLink .= "&pg=".$_GET["pg"]."&pos=".$_GET["pos"];
    $link["pg"]  = $_GET["pg"];
    $link["pos"] = $_GET["pos"];
}

if (count($link) > 0) {
    $_REQUEST = $link;
} else {
    foreach ($_REQUEST as $key => $valor) {
        $link[$key] = $valor;
    }
}

Sessao::write("link", $link);

$stFiltro = "";

$rsListaDocumentos = new RecordSet;

if ($request->get("cmbTipoDocumento") == "1§6§3") { //conforme o tipo do documento pode usar outros tipos de filtro

    if ($request->get("inCGM")) {
        $stFiltro .= " COALESCE( prop_imovel.numcgm, eco.numcgm, documento_cgm.numcgm ) = ".$request->get("inCGM")." AND ";
    }

    if ($request->get("inCodImovel")) {
        $stFiltro .= " documento_imovel.inscricao_municipal = ".$request->get("inCodImovel")." AND ";
    }

    if ($request->get("inInscricaoEconomica")) {
        $stFiltro .= " documento_empresa.inscricao_economica = ".$request->get("inInscricaoEconomica")." AND ";
    }

    if ($stFiltro)
        $stFiltro = " WHERE ".substr( $stFiltro, 0, -4 );

    $obTARRDocumento = new TARRDocumento;
    $obTARRDocumento->recuperaListaCertidao( $rsListaDocumentos, $stFiltro );

    $arTMP2 = array();
    $inTotal = 0;
    $arTMP = $rsListaDocumentos->getElementos();

    for ( $inX=0; $inX<count($arTMP); $inX++ ) {
        $boIncluir = true;
        for ($inY=0; $inY<$inTotal; $inY++) {
            if (
                    ( $arTMP[$inX]["cod_documento"] == $arTMP2[$inY]["cod_documento"] ) &&
                    ( $arTMP[$inX]["num_documento"] == $arTMP2[$inY]["num_documento"] ) &&
                    ( $arTMP[$inX]["exercicio"] == $arTMP2[$inY]["exercicio"] )
               ) {
                $boIncluir = false;
                break;
            }
        }

        if ($boIncluir) {
            $arTMP2[$inTotal] = $arTMP[$inX];
            $inTotal++;
        }
    }

    $rsListaDocumentos->preenche( $arTMP2 );
}

$obLista = new Lista;
$obLista->setRecordSet( $rsListaDocumentos );
$obLista->setTitulo( "Lista de Documentos" );
$obLista->setMostraPaginacao(false);

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Documento" );
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Nome" );
$obLista->ultimoCabecalho->setWidth( 25 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Inscrição Imobiliária");
$obLista->ultimoCabecalho->setWidth( 8 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Inscrição Econômica");
$obLista->ultimoCabecalho->setWidth( 8 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Data de Emissão");
$obLista->ultimoCabecalho->setWidth( 8 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 4 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento ("CENTRO");
$obLista->ultimoDado->setCampo( "[cod_documento] - [descricao]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "[numcgm] - [contribuinte]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "inscricao_municipal" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "inscricao_economica" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "dt_emissao" );
$obLista->commitDado();

$obChkParcela = new Checkbox;
$obChkParcela->setName  ( "boSelecionada" );
$obChkParcela->setValue ( "[num_documento]§[exercicio]§[cod_documento]§[cod_tipo_documento]" );

$obLista->addDadoComponente          ( $obChkParcela );
$obLista->ultimoDado->setAlinhamento ( 'CENTRO' );
$obLista->ultimoDado->setCampo       ( "reemitir" );
$obLista->commitDadoComponente       ();

$obChkTodosN = new Checkbox;
$obChkTodosN->setName               ( "boTodos" );
$obChkTodosN->setId                 ( "boTodos" );
$obChkTodosN->setRotulo             ( "Selecionar Todas" );
$obChkTodosN->obEvento->setOnChange ( "selecionarTodos('n');" );
$obChkTodosN->montaHTML();

$obTabelaCheckboxN = new Tabela;
$obTabelaCheckboxN->addLinha();
$obTabelaCheckboxN->ultimaLinha->addCelula();
$obTabelaCheckboxN->ultimaLinha->ultimaCelula->setColSpan ( 2 );
$obTabelaCheckboxN->ultimaLinha->ultimaCelula->setClass   ( $obLista->getClassPaginacao() );
$obTabelaCheckboxN->ultimaLinha->ultimaCelula->addConteudo( "<div align='right'>Selecionar Todos".$obChkTodosN->getHTML()."&nbsp;</div>");
$obTabelaCheckboxN->ultimaLinha->commitCelula();
$obTabelaCheckboxN->commitLinha();
$obTabelaCheckboxN->montaHTML();

$obLista->montaHTML();

$stHtml = $obLista->getHTML();
$stHtml .= $obTabelaCheckboxN->getHTML();

$obSpanDados = new Span;
$obSpanDados->setId      ( "spnLista" );
$obSpanDados->setValue   ( $stHtml );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( $_REQUEST["stCtrl"]  );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao  );

$obBtnOK = new OK;
$obBtnOK->setName              ( "btnOk" );
$obBtnOK->obEvento->setOnClick ( "validarListar();"    );

$obBtnCancelar = new Cancelar;

$botoesSpanBotoes = array ( $obBtnOK, $obBtnCancelar );

$obForm = new Form;
$obForm->setAction ( $pgForm );

$obFormulario = new Formulario;
$obFormulario->addForm     ( $obForm );
$obFormulario->addHidden   ( $obHdnCtrl );
$obFormulario->addHidden   ( $obHdnAcao );
$obFormulario->addTitulo   ( "Emitir Documentos" );
$obFormulario->addSpan     ( $obSpanDados );
$obFormulario->defineBarra ( $botoesSpanBotoes, 'left', '' );

$obFormulario->show();
