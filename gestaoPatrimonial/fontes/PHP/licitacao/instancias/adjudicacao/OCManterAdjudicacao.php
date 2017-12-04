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
    * Página de Formulario de Manter Adjudicacao
    * Data de Criação: 23/10/2006

    * @author Analista: Anelise Schwengber
    * @author Desenvolvedor: Andre Almeida

    * @ignore

    * $Id: OCManterAdjudicacao.php 63865 2015-10-27 13:55:57Z franver $

    * Casos de uso: uc-03.05.20
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/Table.class.php';
include_once ( CAM_GP_LIC_MAPEAMENTO."TLicitacaoAdjudicacao.class.php" );
include_once ( CAM_GP_COM_MAPEAMENTO."TComprasJulgamentoItem.class.php" );

$stCtrl = $_REQUEST['stCtrl'];

function configuracoesIniciais()
{
    Sessao::write('itensAdjudicacao', array());
}

function carregaItensBanco()
{
    include_once(TLIC."TLicitacaoEdital.class.php");
    $obTLicitacaoEdital = new TLicitacaoEdital();
    $obTLicitacaoEdital->setDado('cod_licitacao' , $_REQUEST["inCodLicitacao"]);
    $obTLicitacaoEdital->setDado('exercicio'     , $_REQUEST["stExercicioLicitacao"]);
    $obTLicitacaoEdital->setDado('cod_modalidade', $_REQUEST["inCodModalidade"]);
    $boRetorno = true;

    $obTLicitacaoEdital->recuperaEditalSuspender($rsEdital);

    if ($rsEdital->getCampo('situacao') != 'Suspenso') {
        $obTLicitacaoAdjudicacao = new TLicitacaoAdjudicacao;

        $obTLicitacaoAdjudicacao->obTLicitacaoCotacaoLicitacao->obTLicitacaoLicitacao->setDado("cod_licitacao" , $_REQUEST["inCodLicitacao"]);
        $obTLicitacaoAdjudicacao->obTLicitacaoCotacaoLicitacao->obTLicitacaoLicitacao->setDado("cod_modalidade", $_REQUEST["inCodModalidade"]);
        $obTLicitacaoAdjudicacao->obTLicitacaoCotacaoLicitacao->obTLicitacaoLicitacao->setDado("cod_entidade"  , $_REQUEST["inCodEntidade"]);
        $obTLicitacaoAdjudicacao->obTLicitacaoCotacaoLicitacao->obTLicitacaoLicitacao->setDado("exercicio"    , $_REQUEST["stExercicioLicitacao"]);

        $obTLicitacaoAdjudicacao->recuperaItensComStatus( $rsItens );

        $itensAdjudicacao = array();
        $inId = 0;

        if ( $rsItens->getNumLinhas() > 0 ) {
            foreach ($rsItens->arElementos as $item) {
                $itensAdjudicacao[$inId]['inId'] = $inId;
                $itensAdjudicacao[$inId]['numAdjudicacao']         = $item["num_adjudicacao"];
                $itensAdjudicacao[$inId]['codEntidade']            = $item["cod_entidade"];
                $itensAdjudicacao[$inId]['codModalidade']          = $item["cod_modalidade"];
                $itensAdjudicacao[$inId]['codLicitacao']           = $item["cod_licitacao"];
                $itensAdjudicacao[$inId]['licitacaoExercicio']     = $item["licitacao_exercicio"];
                $itensAdjudicacao[$inId]['cotacaoExercicio']       = $item["cotacao_exercicio"];
                $itensAdjudicacao[$inId]['codCotacao']             = $item["cod_cotacao"];
                $itensAdjudicacao[$inId]['lote']                   = $item["lote"];
                $itensAdjudicacao[$inId]['codItem']                = $item["cod_item"];
                $itensAdjudicacao[$inId]['descricaoItem']          = $item["descricao"];
                $itensAdjudicacao[$inId]['lote']                   = $item["lote"];
                $itensAdjudicacao[$inId]['quantidade']             = $item["quantidade"];
                $itensAdjudicacao[$inId]['valorTotal']             = $item["vl_total"];
                $itensAdjudicacao[$inId]['status']                 = $item["status"];
                $itensAdjudicacao[$inId]['cgmFornecedor']          = $item["cgm_fornecedor"];
                $itensAdjudicacao[$inId]['justificativa_anulacao'] = $item["justificativa_anulacao"];
                $itensAdjudicacao[$inId]['boAnuladoBanco']         = ( $item["num_adjudicacao_anulada"] != "" ) ? true : false;
                $itensAdjudicacao[$inId]['boAlterado']             = "false";
                $itensAdjudicacao[$inId]['codDocumento']           = $item["cod_documento"];
                $itensAdjudicacao[$inId]['codTipoDocumento']       = $item["cod_tipo_documento"];

                include_once ( CAM_GP_COM_MAPEAMENTO . 'TComprasMapaItem.class.php' );
                $obTComprasMapaItem = new TComprasMapaItem();
                $itemComplemento = "";
                $obTComprasMapaItem->setDado('cod_mapa'     , $item["cod_mapa"]);
                $obTComprasMapaItem->setDado('cod_item'     , $item["cod_item"]);
                $obTComprasMapaItem->setDado('exercicio'    , $item["exercicio_mapa"]);
                $obTComprasMapaItem->setDado('cod_entidade' , $item["cod_entidade"]);
                $obTComprasMapaItem->recuperaComplementoItemMapa( $rsItemComplemento );

                $rsItemComplemento->setPrimeiroElemento();
                While (!$rsItemComplemento->eof()) {
                    if ($itemComplemento == "") {
                        $itemComplemento= $rsItemComplemento->getCampo('complemento');
                    } else {
                        $itemComplemento= $itemComplemento ." \n <br>".$rsItemComplemento->getCampo('complemento');
                    }

                    $rsItemComplemento->proximo();
                }

                $itensAdjudicacao[$inId]['complemento']            = $itemComplemento;

                $itensAdjudicacao[$inId]['valorUnitario']          = number_format($item["vl_unitario"], 2, ",", ".");

                $itensAdjudicacao[$inId]['valorCotacao']           = $item["vl_cotacao"];

                $itensAdjudicacao[$inId]['nomUnidade']             = $item["nom_unidade"];

                $itensAdjudicacao[$inId]['valorReferencia']        = number_format($item["vl_unitario_referencia"], 2, ",", ".");

                $inId = $inId + 1;
            }

            Sessao::write('itensAdjudicacao', $itensAdjudicacao);
            $boRetorno = true;
        }
    } else {
        $boRetorno = false;
        echo "alertaAviso('Edital ".$rsEdital->getCampo('num_edital')."/".$rsEdital->getCampo('exercicio')." está suspenso!', 'form','erro','".Sessao::getId()."');";
    }

    return $boRetorno;
}

function limpaSessao()
{
     Sessao::write('itensAdjudicacao', array());
}

function preencheObjeto()
{
    $obTLicitacaoLicitacao = new TLicitacaoLicitacao;

    $obTLicitacaoLicitacao->setDado( "cod_licitacao" , $_GET["inCodLicitacao"]       );
    $obTLicitacaoLicitacao->setDado( "cod_modalidade", $_GET["inCodModalidade"]      );
    $obTLicitacaoLicitacao->setDado( "cod_entidade"  , $_GET["inCodEntidade"]        );
    $obTLicitacaoLicitacao->setDado( "exercicio"     , $_GET["stExercicioLicitacao"] );

    $obTLicitacaoLicitacao->recuperaObjetoLicitacao( $rsObjeto );

    $stDescricao = addslashes(str_replace('\r\n', '\n', preg_replace('/(\r\n|\n|\r)/', ' ', $rsObjeto->getCampo('descricao'))));
    
    $stJs = "d.getElementById('objeto').innerHTML = '".$stDescricao."';\n";

    return $stJs;
}

function limpaObjeto()
{
    $stJs = "d.getElementById('objeto').innerHTML = '&nbsp;';\n";

    return $stJs;
}

function montaSpnItens()
{
    $rsItens = new RecordSet;
    $rsItens->preenche(  Sessao::read('itensAdjudicacao') );
    $rsItens->addFormatacao('quantidade', 'NUMERIC_BR_4');

    $table = new Table;
    $table->setRecordset   ( $rsItens );
    $table->setSummary     ( 'Itens'  );
    //$table->setConditional ( true , "#E4E4E4" );

    $table->Head->addCabecalho( 'Item'             , 35 );
    $table->Head->addCabecalho( 'Lote'             ,  5 );
    $table->Head->addCabecalho( 'Qtde.'            , 10 );
    $table->Head->addCabecalho( 'Valor Ref.'       , 10 );
    $table->Head->addCabecalho( 'Valor'            , 10 );
    $table->Head->addCabecalho( 'Status'           , 25 );
    //$table->Head->addCabecalho( 'Selecione'        ,  5 );

    $table->Body->addCampo( '[codItem] - [descricaoItem]<br>[complemento]' );
    $table->Body->addCampo( 'lote'            , 'D'       );
    $table->Body->addCampo( 'quantidade'      , 'D'       );
    $table->Body->addCampo( 'valorReferencia' , 'D'       );
    $table->Body->addCampo( 'valorCotacao'    , 'D'       );
    $table->Body->addCampo( 'status'          , 'C'       );

    // Bloqueia o Header
    if ( $rsItens->getNumLinhas() >= 5 ) {
        $table->setHeadFixed(true);
        $table->setBodyHeight(150);
    }
    
    /*
    // Conforme solicitado pelo ticket #22715, até 2ª ordem nao permitir o usuário a selecionar parcialmente os itens.
    $obRdnAdjudicarItem = new Radio();
    $obRdnAdjudicarItem->setValue( "inId" );
    $obRdnAdjudicarItem->setName( "boAdjudicarItemInId" );
    $obRdnAdjudicarItem->setNull( false );
    $obRdnAdjudicarItem->obEvento->setOnClick("montaParametrosGET( 'carregaClassificacao', 'boAdjudicarItemInId' );");
    
    $table->Body->addComponente( $obRdnAdjudicarItem, false );
    */
    
    $table->montaHTML(true);
    $stHtml = $table->getHTML();

    $rsItens->setPrimeiroElemento();

    $boSugerirData = true;
    $boMostraBotoes = true;
    while ( !$rsItens->eof() ) {
        if ( $rsItens->getCampo('status') == 'Homologado' ) {
            $boMostraBotoes = false;
        }
        $rsItens->proximo();
    }

    if ( $rsItens->getNumLinhas()>=0 && $boMostraBotoes ) {
        $obFormulario = new Formulario;

        $obBtnAdjudicar = new Button;
        $obBtnAdjudicar->setName ( "btnAdjudicarTodos" );
        $obBtnAdjudicar->setValue( "Adjudicar" );
        $obBtnAdjudicar->setTipo ( "button" );
        $obBtnAdjudicar->obEvento->setOnClick ( "montaParametrosGET( 'adjudicarTodos', 'hdnCgmFornecedor, btnAdjudicarTodos' );" );

        $obBtnCancelarAdjudicacao = new Button;
        $obBtnCancelarAdjudicacao->setName ( "btnCancelarAdjudicacaoTodos" );
        $obBtnCancelarAdjudicacao->setValue( "Cancelar Adjudicação" );
        $obBtnCancelarAdjudicacao->setTipo ( "button" );
        $obBtnCancelarAdjudicacao->obEvento->setOnClick ( "montaParametrosGET( 'cancelarAdjudicacaoTodos', 'hdnCgmFornecedor, btnCancelarAdjudicacaoTodos' );" );

        $obFormulario->defineBarra( array ( $obBtnAdjudicar, new label(), $obBtnCancelarAdjudicacao ),"right","" );
        $obFormulario->montaInnerHTML();
        $stHtml .= $obFormulario->getHTML();
    }

    $stJs = "jQuery('#spnItensAdjudicacao').html('".$stHtml."'); \n";

    return $stJs;
}

function montaSpnClassificacao($inId = "")
{
    $itensAdjudicacao =  Sessao::read('itensAdjudicacao');

    if ($inId != "") {
        $obTComprasJulgamentoItem = new TComprasJulgamentoItem;

        $obTComprasJulgamentoItem->setDado( "exercicio"  , $itensAdjudicacao[$inId]['licitacaoExercicio']  );
        $obTComprasJulgamentoItem->setDado( "cod_cotacao", $itensAdjudicacao[$inId]['codCotacao'] );
        $obTComprasJulgamentoItem->setDado( "cod_item"   , $itensAdjudicacao[$inId]['codItem'] );

        $obTComprasJulgamentoItem->recuperaClassificacaoItens( $rsClassificacaoItens, " AND julgamento_item.ordem = 1 " );

        $obLista = new Lista;
        $obLista->setTitulo( "Classificação" );
        $obLista->setMostraPaginacao( false );
        $obLista->setRecordSet( $rsClassificacaoItens );

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Seq" );
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Fornecedor" );
        $obLista->ultimoCabecalho->setWidth( 50 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "CNPJ/CPF" );
        $obLista->ultimoCabecalho->setWidth( 20 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Valor Cotado" );
        $obLista->ultimoCabecalho->setWidth( 15 );
        $obLista->commitCabecalho();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "nom_cgm" );
        $obLista->ultimoDado->setAlinhamento( "ESQUERDA" );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "cnpj_cpf" );
        $obLista->ultimoDado->setAlinhamento( "DIREITA" );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "vl_cotacao" );
        $obLista->ultimoDado->setAlinhamento( "DIREITA" );
        $obLista->commitDado();

        $obLista->montaHTML();
        $stHtml = $obLista->getHTML();
        $stHtml = str_replace( "\n", "", $stHtml);
        $stHtml = str_replace( "  ", "", $stHtml);
        $stHtml = str_replace( "'" , "\\'", $stHtml);

        $obHdnCgmFornecedor = new Hidden;
        $obHdnCgmFornecedor->setName  ( "hdnCgmFornecedor" );
        $obHdnCgmFornecedor->setValue ( $rsClassificacaoItens->arElementos[0]['cgm_fornecedor'] );

        $obTxtAreaJustificativaAnulacao = new TextArea;
        $obTxtAreaJustificativaAnulacao->setRotulo( "Justificativa da Anulação" );
        $obTxtAreaJustificativaAnulacao->setName( "stJustificativaAnulacao" );
        $obTxtAreaJustificativaAnulacao->setId( "stJustificativaAnulacao" );
        $obTxtAreaJustificativaAnulacao->setReadOnly( true );
        $obTxtAreaJustificativaAnulacao->setValue( $itensAdjudicacao[$inId]['justificativa_anulacao'] );

        $obSpnAnularItem = new Span;
        $obSpnAnularItem->setId ( "spnAnularItem" );

        $obChkIrProximo = new CheckBox;
        $obChkIrProximo->setRotulo('Ir para o próximo item');
        $obChkIrProximo->setName('boProximoItem');
        $obChkIrProximo->setChecked( $_GET['boProximoItem'] );
        if ( count( $itensAdjudicacao ) <= $inId+1 ) {
            $obChkIrProximo->setDisabled( true );
            $obChkIrProximo->setChecked( false );
        } else {
            $obChkIrProximo->setChecked( $_GET['boProximoItem'] );
        }

        $obBtnAdjudicar = new Button;
        $obBtnAdjudicar->setName ( "btnAdjudicar" );
        $obBtnAdjudicar->setValue( "Adjudicar" );
        $obBtnAdjudicar->setTipo ( "button" );
        $obBtnAdjudicar->obEvento->setOnClick ( "montaParametrosGET( 'adjudicarItem', 'boAdjudicarItemInId, boProximoItem, hdnCgmFornecedor' );" );
        if ( $itensAdjudicacao[$inId]['status'] != "A Adjudicar" || $rsClassificacaoItens->getNumLinhas() < 1 ) {
            $obBtnAdjudicar->setDisabled( true );
        }

        $obBtnCancelar = new Button;
        $obBtnCancelar->setName ( "btnCancelarAdjudicacao" );
        $obBtnCancelar->setValue( "Cancelar a Adjudicação" );
        $obBtnCancelar->setTipo ( "button" );
        $obBtnCancelar->obEvento->setOnClick ( "montaParametrosGET( 'cancelarAdjudicacao', 'boAdjudicarItemInId, boProximoItem' );" );
        if ( $itensAdjudicacao[$inId]['status'] != "Adjudicado" || $rsClassificacaoItens->getNumLinhas() < 1 ) {
            $obBtnCancelar->setDisabled( true );
        }

        $obBtnAnular = new Button;
        $obBtnAnular->setName ( "btnAnularItem" );
        $obBtnAnular->setValue( "Anular Item" );
        $obBtnAnular->setTipo ( "button" );
        $obBtnAnular->obEvento->setOnClick ( "montaParametrosGET( 'montaAnularItem', 'boAdjudicarItemInId, boProximoItem' );" );
        if ( ($itensAdjudicacao[$inId]['status'] != "A Adjudicar" && $itensAdjudicacao[$inId]['status'] != "Adjudicado" )  || ( $rsClassificacaoItens->getNumLinhas() < 1 ) ) {
            $obBtnAnular->setDisabled( true );
        }

        $obFormulario = new Formulario;
        $obFormulario->addHidden( $obHdnCgmFornecedor );
        $obFormulario->addSpan( $obSpnAnularItem );
        if ($itensAdjudicacao[$inId]['status'] == "Anulado") {
            $obFormulario->addComponente( $obTxtAreaJustificativaAnulacao );
        }
        $obFormulario->addComponente( $obChkIrProximo );
        $obFormulario->defineBarra( array ( $obBtnAdjudicar , $obBtnCancelar, $obBtnAnular ),"","" );
        $obFormulario->montaInnerHTML();
        $stHtml .= $obFormulario->getHTML();

    } else {
        $stHtml = "";
    }

    $stJs = "document.getElementById('spnClassificacao').innerHTML = '".$stHtml."';\n";

    return $stJs;
}

function adjudicarItem($inId, $inCgmFornecedor)
{
    $itensAdjudicacao = Sessao::read('itensAdjudicacao');
    $itensAdjudicacao[$inId]['status']        = "Adjudicado";
    $itensAdjudicacao[$inId]['boAlterado']    = "true";
    $itensAdjudicacao[$inId]['cgmFornecedor'] = $inCgmFornecedor;
    Sessao::write('itensAdjudicacao', $itensAdjudicacao);
}

function proximoItem()
{
    $stJs .= montaSpnItens();
    $inId = "";

    $inId = $_GET['boAdjudicarItemInId'] + 1;

    $itensAdjudicacao = Sessao::read('itensAdjudicacao');
    if ($itensHomologacao[$inId] != "") {
        if ($_GET['boProximoItem'] == "on") {
            $stJs .= "document.frm.boAdjudicarItemInId[$inId].checked = true;\n";
        }
        $stJs .= montaSpnClassificacao( $inId );
    } else {
        $stJs .= "document.getElementById('spnClassificacao').innerHTML = '';\n";
    }

    return $stJs;
}

function cancelarAdjudicacao($inId)
{
    $itensAdjudicacao = Sessao::read('itensAdjudicacao');
    $itensAdjudicacao[$inId]['status']     = "A Adjudicar";
    $itensAdjudicacao[$inId]['boAlterado'] = "true";
    Sessao::write('itensAdjudicacao', $itensAdjudicacao);
}

function montaAnularItem()
{
    $obTxtAreaJustificativa = new TextArea;
    $obTxtAreaJustificativa->setRotulo( "Justificativa da Anulação" );
    $obTxtAreaJustificativa->setName( "stJustificativa da Anulação" );
    $obTxtAreaJustificativa->setId( "stJustificativa" );

    $obBtnOkAnular = new Button;
    $obBtnOkAnular->setName ( "btnOkAnular" );
    $obBtnOkAnular->setValue( "Ok" );
    $obBtnOkAnular->setTipo ( "button" );
    $obBtnOkAnular->setStyle( "width: 60px" );
    $obBtnOkAnular->obEvento->setOnClick ( "montaParametrosGET( 'anularItem', 'boAdjudicarItemInId, boProximoItem, stJustificativa' );" );

    $obBtnCancelarAnular = new Button;
    $obBtnCancelarAnular->setName ( "btnCancelarAnular" );
    $obBtnCancelarAnular->setValue( "Cancelar" );
    $obBtnCancelarAnular->setTipo ( "button" );
    $obBtnCancelarAnular->obEvento->setOnClick ( "montaParametrosGET( 'cancelarAnularItem', 'boAdjudicarItemInId, boProximoItem' );" );

    $obFormulario = new Formulario;
    $obFormulario->addComponente( $obTxtAreaJustificativa );
    $obFormulario->defineBarra( array ( $obBtnOkAnular , $obBtnCancelarAnular ),"","" );
    $obFormulario->montaInnerHTML();
    $stHtml .= $obFormulario->getHTML();

    $stJs .= "document.getElementById('spnAnularItem').innerHTML = '".$stHtml."';\n";

    return $stJs;
}

function anularItem($inId, $stJustificativaAnulacao)
{
    $itensAdjudicacao = Sessao::read('itensAdjudicacao');
    $itensAdjudicacao[$inId]['status']                 = "Anulado";
    $itensAdjudicacao[$inId]['boAlterado']             = "true";
    $itensAdjudicacao[$inId]['justificativa_anulacao'] =  $stJustificativaAnulacao;
    Sessao::write('itensAdjudicacao', $itensAdjudicacao);
}

function cancelarAnularItem()
{
    $stJs .= "document.getElementById('spnAnularItem').innerHTML = '';\n";

    return $stJs;
}

function limpaDocumento()
{
    $stJs .= "if(document.getElementById('stCodDocumento')) document.getElementById('stCodDocumento').value = '';\n";
    $stJs .= "if(document.getElementById('stCodDocumentoTxt')) document.getElementById('stCodDocumentoTxt').value = '';\n";

    return $stJs;
}

function alterarStatusItens($inCgmFornecedor, $stStatus)
{
    $itensAdjudicacao = Sessao::read('itensAdjudicacao');
    $inCountAdjuicacao = count($itensAdjudicacao);
    for ($inCount=0; $inCount<$inCountAdjuicacao; $inCount++) {
        $itensAdjudicacao[$inCount]['status']        = $stStatus;
        $itensAdjudicacao[$inCount]['boAlterado']    = "true";
    }
    Sessao::write('itensAdjudicacao', $itensAdjudicacao);
    $stJs .= montaSpnItens();

    return $stJs;
}

function sugereData()
{
    $boSugerirData = true;
    $rsItens = new RecordSet;
    $rsItens->preenche(  Sessao::read('itensAdjudicacao') );
    $rsItens->addFormatacao('quantidade', 'NUMERIC_BR_4');

    while ( !$rsItens->eof() ) {
        if (strtolower($rsItens->getCampo('status')) != "a adjudicar") {
            $boSugerirData = false;
        }
        $rsItens->proximo();
    }

    if ($rsItens->getNumLinhas() >= 1) {
        if ($boSugerirData) {
            include_once ( CAM_GP_LIC_MAPEAMENTO."TLicitacaoEdital.class.php" );

            $rsItens->setPrimeiroElemento();
            $stFiltro = " AND ll.cod_licitacao = ".$rsItens->getCampo("codLicitacao")."
                          AND le.cod_modalidade = ".$rsItens->getCampo("codModalidade")."
                          AND le.cod_entidade = ".$rsItens->getCampo("codEntidade")."
                          AND le.exercicio_licitacao = '".$rsItens->getCampo("licitacaoExercicio")."'";

            $obTLicitacaoEdital = new TLicitacaoEdital();
            $obTLicitacaoEdital->recuperaListaEdital($rsEdital, $stFiltro);

            $dtDataAberturaPropostas = date('d/m/Y', strtotime($rsEdital->getCampo('dt_abertura_propostas')." ".$rsEdital->getCampo('hora_abertura_propostas')));
            $dtHoraAberturaPropostas = date('H:i', strtotime($rsEdital->getCampo('dt_abertura_propostas')." ".$rsEdital->getCampo('hora_abertura_propostas')));
        } else {
            $stFiltro = " WHERE cod_licitacao = ".$_REQUEST["inCodLicitacao"]."
                            AND cod_modalidade = ".$_REQUEST["inCodModalidade"]."
                            AND cod_entidade = ".$_REQUEST["inCodEntidade"]."
                            AND exercicio_licitacao = '".$_REQUEST["stExercicioLicitacao"]."'";

            $obTLicitacaoAdjudicacao = new TLicitacaoAdjudicacao();
            $obTLicitacaoAdjudicacao->recuperaTodos($rsAdjudicacao, $stFiltro);

            $arTimestamp = explode(" ", $rsAdjudicacao->getCampo("timestamp"));

            $dtDataAberturaPropostas = date('d/m/Y', strtotime($rsAdjudicacao->getCampo("timestamp")));
            $dtHoraAberturaPropostas = date('H:i', strtotime($rsAdjudicacao->getCampo("timestamp")));
        }
        $stJs  = "jQuery('#stDtAdjudicacao').val('$dtDataAberturaPropostas');\n";
        $stJs .= "jQuery('#stHoraAdjudicacao').val('$dtHoraAberturaPropostas');\n";
    }

    return $stJs;
}

switch ($stCtrl) {
    case "adjudicarTodos":
        $js = alterarStatusItens( $_GET['hdnCgmFornecedor'] , 'Adjudicado');
    break;
    case "cancelarAdjudicacaoTodos":
        $js = alterarStatusItens( $_GET['hdnCgmFornecedor'] , 'A Adjudicar' );
    break;
    case "configuracoesIniciais":
        configuracoesIniciais();
        $js = montaSpnItens();
    break;
    case "carregaItensBanco":
        $js = isset($js) ? $js : "";
        if ($_REQUEST["inCodLicitacao"]) {
            if (carregaItensBanco()) {
                $js .= preencheObjeto();
            }
        } else {
            $js .= limpaSessao();
            $js .= limpaObjeto();
            $js .= limpaDocumento();
        }
        $js .= montaSpnClassificacao();
        $js .= sugereData();
        $js .= montaSpnItens();
    break;
    case "montaSpnItens":
        $js = montaSpnItens();
    break;
    case "carregaClassificacao":
        $js = montaSpnClassificacao( $_REQUEST['boAdjudicarItemInId'] );
    break;
    case "limpaSpans":
        $js  = limpaSessao();
        $js .= limpaObjeto();
        $js .= montaSpnClassificacao();
        $js .= montaSpnItens();
    break;
    case "adjudicarItem":
        $js  = adjudicarItem( $_GET['boAdjudicarItemInId'], $_GET['hdnCgmFornecedor'] );
        $js .= proximoItem();
    break;
    case "cancelarAdjudicacao":
        $js  = cancelarAdjudicacao( $_GET['boAdjudicarItemInId'] );
        $js .= proximoItem();
    break;
    case "montaAnularItem":
        $js = montaAnularItem();
    break;
    case "anularItem":
        $js  = anularItem( $_GET['boAdjudicarItemInId'], stripslashes($_GET['stJustificativa']) );
        $js .= proximoItem();
    break;
    case "cancelarAnularItem":
        $js = cancelarAnularItem();
    break;
    case "limparTela":
        $js  = limpaSessao();
        $js .= limpaObjeto();
        $js .= montaSpnClassificacao();
        $js .= montaSpnItens();
    break;
}

if ($js) {
    echo $js;
}

?>
