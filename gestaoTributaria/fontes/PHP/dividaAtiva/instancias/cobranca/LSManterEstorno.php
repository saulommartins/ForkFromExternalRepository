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
    * Página de lista para o cadastro de transferência de proipriedade
    * Data de Criação   : 27/02/2007

    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Fernando Piccini Cercato

    * @ignore

    * $Id: LSManterEstorno.php 63839 2015-10-22 18:08:07Z franver $

    * Casos de uso: uc-05.04.04
*/

/*
$Log$
Revision 1.9  2007/08/14 16:00:12  dibueno
*** empty log message ***

Revision 1.8  2007/08/09 20:55:20  cercato
apresentando a cobranca na lista de estorno de cobranca.

Revision 1.7  2007/07/19 21:01:25  cercato
Bug #9705#

Revision 1.6  2007/07/19 13:45:08  cercato
correcao na rotina de estorno. (agrupando documentos de uma mesma cobranca).

Revision 1.5  2007/07/17 13:37:47  cercato
correcao para rotina de cancelamento de divida.

Revision 1.4  2007/04/24 19:32:48  cercato
inserindo campo "cobranca" no filtro de estorno

Revision 1.3  2007/03/27 14:47:31  cercato
Bug #8891#

Revision 1.2  2007/03/26 21:27:53  cercato
Bug #8891#

Revision 1.1  2007/02/27 19:53:30  cercato
*** empty log message ***

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaDocumento.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterCobranca";
$pgProc = "PR".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once ( $pgJS );

$stAcao = $request->get('stAcao');

if ( empty( $stAcao ) ) {
    $stAcao = "Estornar";
}

//MANTEM FILTRO E PAGINACAO
$stLink .= "&stAcao=".$stAcao."&stMotivo=".$_REQUEST['stMotivo'];
Sessao::write('stLink', $stLink);

if ($_GET["pg"] and  $_GET["pos"]) {
    $link['pg']  = $_GET['pg'];
    $link['pos'] = $_GET['pos'];
}

Sessao::Write('link', $link);

//USADO QUANDO EXISTIR FILTRO
//NA FL O VAR LINK DEVE SER RESETADA
$arTransf4Sessao = Sessao::read('sessao_transf4');
if ( is_array($arTransf4Sessao) ) {
    $_REQUEST = $arTransf4Sessao;
} else {
    foreach ($_REQUEST as $key => $valor) {
        $arTransf4Sessao[$key] = $valor;
    }
}

Sessao::write('sessao_transf4', $arTransf4Sessao);

//MONTA O FILTRO
$stFiltro = "\n ddcanc.cod_inscricao IS NULL \n AND";

if ($_REQUEST["inNrParcelamento"]) {
    $arParcelamento = explode( "/", $_REQUEST["inNrParcelamento"] );
    $stFiltro .= " dparc.numero_parcelamento = ".$arParcelamento[0]." AND dparc.exercicio = '".$arParcelamento[1]."' AND ";
}

if ($_REQUEST["inCGM"]) {
    $stFiltro .= " ddc.numcgm = ".$_REQUEST["inCGM"]." AND ";
}

if ($_REQUEST["stCodDocumento"]) {
    $stFiltro .= " dd.cod_documento = ".$_REQUEST["stCodDocumento"]." AND ";
}

if ($_REQUEST["ValorParcela"]) {
    $stFiltro .= " vlr.valor = ".str_replace ( ',', '.', str_replace ( '.', '', $_REQUEST["ValorParcela"] ) )." AND ";
}

if ($_REQUEST["NroParcela"]) {
    $stFiltro .= " tot.parcela = ".$_REQUEST["NroParcela"]." AND ";
}

if ($_REQUEST["NroParcelaAtraso"]) {
    $stFiltro .= " tot_vencida.parcela = ".$_REQUEST["NroParcelaAtraso"]." AND ";
}

if ($_REQUEST["NroDiasAtraso"]) {
    $stFiltro .= " (to_char(now() - dp.dt_vencimento_parcela, 'dddd')::integer) = ".$_REQUEST["NroDiasAtraso"]." AND ";
}

if ($stFiltro) {
    $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
}

$obTDATDividaDocumento = new TDATDividaDocumento;
$obTDATDividaDocumento->recuperaListaCobrancaEstornar( $rsListaDocumentos, $stFiltro );

$arDocumentosTMP = array();
$inTotalDocumentos = 0;
if ( !$rsListaDocumentos->Eof() ) {
    $arDocumentos = $rsListaDocumentos->getElementos();

    for ( $inX=0; $inX<count($arDocumentos); $inX++ ) {
        $boNaLista = false;
        for ($inY=0; $inY<$inTotalDocumentos; $inY++) {
            if ($arDocumentosTMP[$inY]["num_parcelamento"] == $arDocumentos[$inX]["num_parcelamento"]) {
                for ($inZ=0; $inZ<$arDocumentosTMP[$inY]["qtdtermos"]; $inZ++) {
                    if ($arDocumentosTMP[$inY]["termos"][$inZ] == $arDocumentos[$inX]["num_documento"]) {
                        $boNaLista = true;
                        break;
                    }
                }

                if (!$boNaLista) {
                    $arDocumentosTMP[$inY]["num_documento"] .= "<br>".$arDocumentos[$inX]["num_documento"];
                    $arDocumentosTMP[$inY]["termos"][ $arDocumentosTMP[$inY]["qtdtermos"] ] = $arDocumentos[$inX]["num_documento"];
                    $arDocumentosTMP[$inY]["qtdtermos"]++;
                }

                $boNaLista = true;
                break;
            }
        }

        if (!$boNaLista) {
            $arDocumentosTMP[$inTotalDocumentos] = $arDocumentos[$inX];
            $arDocumentosTMP[$inTotalDocumentos]["num_documento"] = $arDocumentos[$inX]["num_documento"];
            $arDocumentosTMP[$inTotalDocumentos]["termos"][0] = $arDocumentos[$inX]["num_documento"];
            $arDocumentosTMP[$inTotalDocumentos]["qtdtermos"] = 1;
            $inTotalDocumentos++;
        }
    }
}

$rsListaDocumentos->preenche($arDocumentosTMP);
$rsListaDocumentos->setPrimeiroElemento();

$stCaminho = CAM_GT_DAT_INSTANCIAS."cobranca/";

$obLista = new Lista;
$obLista->setRecordSet( $rsListaDocumentos );
$obLista->setTitulo("Registros de Cobrança");
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Termo de Parcelamento");
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Inscrição" );
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Cobrança" );
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Parcelas" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Parcelas em Atraso" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Número de Dias em Atraso" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Valor" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 3 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "num_documento" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "inscricao" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "[numero_parcelamento]/[exercicio_cobranca]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "qtd_parcelas" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "qtd_parcelas_vencidas" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "dias_atraso" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "valor_parcelamento" );
$obLista->commitDado();

$obChkParcela = new Checkbox;
$obChkParcela->setName  ( "boSelecionada" );
$obChkParcela->setValue ( "[num_parcelamento]-[numero_parcelamento]/[exercicio_cobranca]" );

$obLista->addDadoComponente                    ( $obChkParcela );
$obLista->ultimoDado->setAlinhamento           ( 'CENTRO' );
$obLista->ultimoDado->setCampo                 ( "reemitir" );
$obLista->commitDadoComponente                 ();

$obChkTodosN = new Checkbox;
$obChkTodosN->setName                        ( "boTodos" );
$obChkTodosN->setId                          ( "boTodos" );
$obChkTodosN->setRotulo                      ( "Selecionar Todas" );
$obChkTodosN->obEvento->setOnChange          ( "selecionarTodos('n');" );
$obChkTodosN->montaHTML();

$obTxtMotivo = new TextArea;
$obTxtMotivo->setName       ( 'stMotivo' );
$obTxtMotivo->setNull       ( false );
$obTxtMotivo->setTitle      ( "Informe o motivo para a estorno da divida ativa." );
$obTxtMotivo->setRotulo     ("Motivo Estorno");
//$obTxtMotivo->setValue      ($_REQUEST['stMotivo']);
$obTxtMotivo->montaHTML();

$obFormTemp = new Formulario;
//$obFormTemp->agrupaComponentes($obLblMotivo, $obTxtMotivo);
$obFormTemp->addComponente($obTxtMotivo);
$obFormTemp->montaInnerHTML();

$obTabelaCheckboxN = new Tabela;
$obTabelaCheckboxN->addLinha();
$obTabelaCheckboxN->ultimaLinha->addCelula();
$obTabelaCheckboxN->ultimaLinha->ultimaCelula->setColSpan ( 2 );
$obTabelaCheckboxN->ultimaLinha->ultimaCelula->setClass   ( $obLista->getClassPaginacao() );
$obTabelaCheckboxN->ultimaLinha->ultimaCelula->addConteudo( "<div align='right'>Selecionar Todos".$obChkTodosN->getHTML()."&nbsp;</div>");
$obTabelaCheckboxN->ultimaLinha->commitCelula();
$obTabelaCheckboxN->commitLinha();

$obTabelaCheckboxN->addLinha();
$obTabelaCheckboxN->ultimaLinha->addCelula();
$obTabelaCheckboxN->ultimaLinha->ultimaCelula->setColSpan ( 2 );
$obTabelaCheckboxN->ultimaLinha->ultimaCelula->setClass   ( $obLista->getClassPaginacao() );
//$obTabelaCheckboxN->ultimaLinha->ultimaCelula->addConteudo( "<div align='LEFT'>Motivo Estorno".$obTxtMotivo->getHTML()."&nbsp;</div>");
$obTabelaCheckboxN->ultimaLinha->ultimaCelula->addConteudo( "<div align='LEFT'>".$obFormTemp->getHTML()."&nbsp;</div>");
$obTabelaCheckboxN->ultimaLinha->commitCelula();
$obTabelaCheckboxN->commitLinha();
$obTabelaCheckboxN->montaHTML();

$obLista->montaHTML();

$stHtml = $obLista->getHTML();
$stHtml .= $obTabelaCheckboxN->getHTML();

$obSpanDados = new Span;
$obSpanDados->setId      ( "spnLista" );
$obSpanDados->setValue   ( $stHtml );

$obBtnOK = new OK;
$obBtnOK->setName              ( "btnOk" );
$obBtnOK->obEvento->setOnClick ( "validarListarEstorno();"    );

$obBtnCancelar = new Cancelar;

$botoesSpanBotoes = array ( $obBtnOK, $obBtnCancelar );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( $stCtrl  );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao  );

$obForm = new Form;
$obForm->setAction ( $pgProc );
$obForm->settarget ( "oculto" );

$obFormulario = new Formulario;

$obFormulario->addForm       ( $obForm );
$obFormulario->addHidden     ( $obHdnCtrl );
$obFormulario->addHidden     ( $obHdnAcao );
$obFormulario->addSpan       ( $obSpanDados );

//$obFormulario->addComponente ($obTxtMotivo);
$obFormulario->defineBarra   ( $botoesSpanBotoes, 'left', '' );
$obFormulario->show();

?>
