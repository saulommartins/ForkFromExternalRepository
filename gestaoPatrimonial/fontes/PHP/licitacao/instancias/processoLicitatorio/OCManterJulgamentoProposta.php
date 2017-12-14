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
    * Pagina Oculta para Formulário de
    * Data de Criação   : 05/10/2006

    * @author Analista: Gelson
    * @author Desenvolvedor: Bruce Cruz de Sena

    * @ignore

    * $Id: OCManterJulgamentoProposta.php 66389 2016-08-22 20:37:03Z carlos.silva $

    * Casos de uso: uc-03.05.26

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GP_COM_MAPEAMENTO.'TComprasCotacaoFornecedorItem.class.php'                     );
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/Table.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterJulgamentoProposta";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgPror = "PO".$stPrograma.".php";

$stCtrl = $_REQUEST['stCtrl'] ;

function recuperaItens($inCodMapa = '', $stExercicioMapa ='')
{
    $stJs = '';
    $arFornecedores = array();
    $inPos = 0;

    if ( ($inCodMapa) and ( $stExercicioMapa ) ) {

        /// buscando dados do mapa
        include_once ( CAM_GP_COM_MAPEAMENTO. 'TComprasMapa.class.php' );
        $obTComprasMapa = new TComprasMapa;
        $obTComprasMapa->setDado ( 'cod_mapa',  $inCodMapa       );
        $obTComprasMapa->setDado ( 'exercicio', $stExercicioMapa );
        $obTComprasMapa->consultar();

        $rsRecordSet = new Recordset;

        include_once ( CAM_GP_COM_MAPEAMENTO.'TComprasCotacaoItem.class.php');
        $obTComprasCotacaoItem = new TComprasCotacaoItem;

        switch ( $obTComprasMapa->getDado( 'cod_tipo_licitacao' )) {
            case 1://// este mapa sera licitado por item
               $stFiltro = "
               where mapa_cotacao.cod_mapa = $inCodMapa
                 and mapa_cotacao.exercicio_mapa = '$stExercicioMapa'

                -- NÃO PODE TRAZER O MAPA COM COTAÇÃO ANULADA
                AND NOT EXISTS
                (
                    SELECT  1
                      FROM  compras.cotacao_anulada
                     WHERE  cotacao_anulada.cod_cotacao = mapa_cotacao.cod_cotacao
                       AND  cotacao_anulada.exercicio = mapa_cotacao.exercicio_cotacao
                )

                -- TRAZ SOMENTE OS ITENS QUE TENHAM COTAÇÃO PARA SER JULGADO.
                AND EXISTS
                (
                    SELECT  1
                      FROM  compras.cotacao_fornecedor_item
                     WHERE  cotacao_item.exercicio   = cotacao_fornecedor_item.exercicio
                       AND  cotacao_item.cod_cotacao = cotacao_fornecedor_item.cod_cotacao
                       AND  cotacao_item.cod_item    = cotacao_fornecedor_item.cod_item
                       AND  cotacao_item.lote        = cotacao_fornecedor_item.lote
                )
                ";
               $stOrdem  = " cotacao_item.lote ";
               $obTComprasCotacaoItem->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrdem );

                include_once ( CAM_GP_COM_MAPEAMENTO."TComprasMapaItem.class.php"          );
                include_once ( CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoCatalogoItem.class.php" );

                $obTAlmoxarifadoCatalogoItem = new TAlmoxarifadoCatalogoItem;
                $obTComprasMapaItem          = new TComprasMapaItem;

                /// recuperando os fornecedores que se inscreveram pra cada item
                while (!$rsRecordSet->eof()) {
                   $inCodigoItem = $rsRecordSet->getCampo('cod_item');

                   $arFornecedores[$inCodigoItem] = recuperaFornecedoresPorItem( $stExercicioMapa,
                                                                                 $inCodMapa,
                                                                                 $rsRecordSet->getCampo( 'cod_item' ),
                                                                                 $rsRecordSet->getCampo( 'lote'     ),
                                                                                 $rsRecordSet->getCampo( 'tipo'     )
                                                                               );
                    $rsRecordSet->setCampo('codigo', $inCodigoItem );

                     // Recupera o valor de referência do ítem.
                    $obTComprasMapaItem->setDado("exercicio" , $stExercicioMapa);
                    $obTComprasMapaItem->setDado("cod_mapa"  , $inCodMapa );
                    $obTComprasMapaItem->setDado("cod_item"  , $rsRecordSet->getCampo('cod_item'));
                    $obTComprasMapaItem->recuperaValorReferenciaItem($rsItemValorReferencia);

                     // Recupera o nome de referência do ítem.
                    $obTComprasMapaItem->setDado("cod_item"  , $rsRecordSet->getCampo('cod_item'));
                    $obTComprasMapaItem->recuperaNomeItem($rsItemNomeReferencia);

                    // Recupera complemento de refência do ítem.
                    $itemComplemento = "";
                    $obTComprasMapaItem->setDado('cod_mapa'  , $inCodMapa);
                    $obTComprasMapaItem->setDado('cod_item'  , $rsRecordSet->getCampo('cod_item'));
                    $obTComprasMapaItem->setDado('exercicio' , $stExercicioMapa);
                    $obTComprasMapaItem->recuperaComplementoItemMapa( $rsItemComplemento );

                    $rsItemComplemento->setPrimeiroElemento();
                    While (!$rsItemComplemento->eof()) {
                        if ($itemComplemento == "") {
                            $itemComplemento= $rsItemComplemento->getCampo('complemento');
                        } else {
                            $itemComplemento= $itemComplemento ." <br\>".$rsItemComplemento->getCampo('complemento');
                        }
                        $rsItemComplemento->proximo();
                    }

                    // Recupera o valor da última compra do ítem.
                    $obTAlmoxarifadoCatalogoItem->setDado('cod_item'  , $rsRecordSet->getCampo('cod_item'));
                    $obTAlmoxarifadoCatalogoItem->setDado('exercicio' , $stExercicioMapa);
                    $obTAlmoxarifadoCatalogoItem->recuperaValorItemUltimaCompra($rsItemUltimaCompra);

                    $rsRecordSet->setCampo ( 'valor_referencia'    , $rsItemValorReferencia->getCampo('vl_referencia') );
                    $rsRecordSet->setCampo ( 'descricao_item'    , $rsRecordSet->getCampo('cod_item')." - ".$rsItemNomeReferencia->getCampo('nome')." <br />".$itemComplemento);
                    $rsRecordSet->setCampo ( 'valor_ultima_compra' , $rsItemUltimaCompra->getCampo('vl_unitario_ultima_compra') );

                    $itemComplemento = "";
                    $obTComprasMapaItem->recuperaComplementoItemMapa($rsComplementoItem);
                    While (!$rsComplementoItem->eof()) {
                        if ($itemComplemento == "") {
                            $itemComplemento= $rsComplementoItem->getCampo('complemento');
                        } else {
                            $itemComplemento= $itemComplemento ." <br/>".$rsComplementoItem->getCampo('complemento');
                        }
                        $rsComplementoItem->proximo();
                    }
                    $rsRecordSet->setCampo ( 'complemento' , $itemComplemento );

                    $rsRecordSet->proximo();
                }

               $rsRecordSet->setPrimeiroElemento();

               Sessao::write('arFornecedores', $arFornecedores);

               if ( $rsRecordSet->getNumLinhas() > 0 ) {

                   $stJs = montaSpanItens( $rsRecordSet, $obTComprasMapa->getDado( 'cod_tipo_licitacao' ) );
               } else {
                    $stJs .= "alertaAviso('Mapa de Compras " . $inCodMapa . "/".$stExercicioMapa . " não possui cotaçãoes.','n_erro','erro','".Sessao::getId()."');\n";
                    $stJs .= "jQuery('#spnItens').html('');\n";
                    $stJs .= "jQuery('#spnLabels').html('');\n";
               }
            break;

            case 2: //// por lote
            case 3: /// por Preço Global (estão juntos pq quando for global, todos os itens estarão no mesmo lote )

                $obTComprasCotacaoItem->recuperaLotes( $rsRecordSet, $inCodMapa, $stExercicioMapa );

                include_once ( CAM_GP_COM_MAPEAMENTO."TComprasMapaItem.class.php" );

                $obTComprasMapaItem = new TComprasMapaItem;
                $obTComprasMapaItem->setDado("exercicio" , $stExercicioMapa);
                $obTComprasMapaItem->setDado("cod_mapa"  , $inCodMapa      );

                /// recuperando os fornecedores de cada lote
                $inLote = '';
                while ( !$rsRecordSet->eof() ) {

                    $arFornecedores[$rsRecordSet->getCampo( 'lote' )] =  recuperaFornecedoresPorLote
                                                                        (
                                                                            $rsRecordSet->getCampo( 'exercicio'),
                                                                            $rsRecordSet->getCampo( 'cod_cotacao'),
                                                                            $rsRecordSet->getCampo( 'lote'),
                                                                            $rsRecordSet->getCampo( 'tipo')
                                                                        );

                    $obTComprasMapaItem->setDado("lote", $rsRecordSet->getCampo('lote'));
                    $obTComprasMapaItem->recuperaValorReferenciaLote($rsItemValorReferencia);
                    $vlTotalLoteReferencia = 0;

                    while (!$rsItemValorReferencia->eof()) {
                        $vlTotalLoteReferencia = $vlTotalLoteReferencia + $rsItemValorReferencia->getCampo('vl_referencia');
                        $rsItemValorReferencia->proximo();
                    }

                    $rsRecordSet->setCampo('valor_referencia', $vlTotalLoteReferencia);
                    $rsRecordSet->proximo();
                }

               $rsRecordSet->setPrimeiroElemento();

               Sessao::write('arFornecedores', $arFornecedores);
               $stJs = montaSpanLotes( $rsRecordSet );
            break;
        }
    }

    return $stJs;
}

function montaSpanLotes(&$rsRecordSet)
{
    while ( !$rsRecordSet->eof() ) {
        $rsRecordSet->setCampo ( 'codigo',  $rsRecordSet->getCampo( 'lote' ) );
        $rsRecordSet->proximo ();
    }
    $rsRecordSet->setPrimeiroElemento();
    $rsRecordSet->addFormatacao ( 'quantidade'       , 'NUMERIC_BR_4' );
    $rsRecordSet->addFormatacao ( 'valor_referencia' , 'NUMERIC_BR' );

    // Montagem Lista
    $obLista = new Lista;
    $obLista->setTitulo          ( 'Lotes'      );
    $obLista->setMostraPaginacao ( false        );
    $obLista->setRecordset       ( $rsRecordSet );

    // Cabeçalho da lista
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo ( "&nbsp;"     );
    $obLista->ultimoCabecalho->setWidth    ( 3            );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo    ( 'Lote' );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo    ( 'Cotação' );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo    ( 'Itens Solicitados' );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo    ( 'Soma da Qtde dos Itens' );
    $obLista->ultimoCabecalho->setWidth       ( 20      );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo    ( 'Valor Referência' );
    $obLista->ultimoCabecalho->setWidth       ( 35      );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo    ( 'Selecione' );
    $obLista->ultimoCabecalho->setWidth       ( 5      );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("RIGHT");
    $obLista->ultimoDado->setCampo( 'lote' );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("RIGHT");
    $obLista->ultimoDado->setCampo( 'cod_cotacao' );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("RIGHT");
    $obLista->ultimoDado->setCampo( 'numero_itens' );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("RIGHT");
    $obLista->ultimoDado->setCampo( 'quantidade' );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("RIGHT");
    $obLista->ultimoDado->setCampo( 'valor_referencia' );
    $obLista->commitDado();

    $obChkSelecione = new Radio;
    $obChkSelecione->setName              ( "chkseleciona"    );
    $obChkSelecione->setId                ( "chkseleciona"    );
    $obChkSelecione->setValue             ( 'codigo'          );
    $obChkSelecione->obEvento->setOnClick ( "selecionaItem_Lote( this, 'lote' ); jQuery('#Ok').attr('disabled', false);" );

    $obLista->addDadoComponente          ( $obChkSelecione );
    $obLista->ultimoDado->setAlinhamento ( "CENTER"           );
    $obLista->ultimoDado->setCampo       ( "selecionar"       );
    $obLista->commitDadoComponente();

    $obLista->montaHTML();
    $stHtml = $obLista->getHTML();
    $stHtml = str_replace("\n","",$stHtml);
    $stHtml = str_replace("  ","",$stHtml);
    $stHtml = str_replace("'","\\'",$stHtml);
    $stJs = "jQuery('#spnItens').html('".$stHtml."');";

    return $stJs;
}

function montaSpanItens($rsRecordSet = false, $inTipoLicitacao = 1)
{
    /// isto serve para montar a listagem vazia
    if (!$rsRecordSet) { $rsRecordSet = new Recordset; }

    
    $rsRecordSet->addFormatacao('quantidade'         , 'NUMERIC_BR_4');
    $rsRecordSet->addFormatacao('valor_referencia'   , 'NUMERIC_BR');
    $rsRecordSet->addFormatacao('valor_ultima_compra', 'NUMERIC_BR');
    
    // Montagem Lista
    $obLstItens = new Lista;
    $obLstItens->setTitulo          ( 'Itens'      );
    $obLstItens->setMostraPaginacao ( false        );
    $obLstItens->setRecordset       ( $rsRecordSet );

    // Cabeçalho da lista
    $obLstItens->addCabecalho();
    $obLstItens->ultimoCabecalho->addConteudo ( "&nbsp;"     );
    $obLstItens->ultimoCabecalho->setWidth    ( 3            );
    $obLstItens->commitCabecalho();

    $obLstItens->addCabecalho();
    $obLstItens->ultimoCabecalho->addConteudo    ( 'Itens' );
    $obLstItens->commitCabecalho();

    $obLstItens->addCabecalho();
    $obLstItens->ultimoCabecalho->addConteudo    ( 'Quantidade' );
    $obLstItens->ultimoCabecalho->setWidth       ( 10      );
    $obLstItens->commitCabecalho();

    $obLstItens->addCabecalho();
    $obLstItens->ultimoCabecalho->addConteudo    ( 'Valor de Referência' );
    $obLstItens->ultimoCabecalho->setWidth       ( 10      );
    $obLstItens->commitCabecalho();

    $obLstItens->addCabecalho();
    $obLstItens->ultimoCabecalho->addConteudo    ( 'Valor da Última Compra' );
    $obLstItens->ultimoCabecalho->setWidth       ( 10      );
    $obLstItens->commitCabecalho();

    $obLstItens->addCabecalho();
    $obLstItens->ultimoCabecalho->addConteudo    ( 'Selecione' );
    $obLstItens->ultimoCabecalho->setWidth       ( 5      );
    $obLstItens->commitCabecalho();

    $obLstItens->addDado();
    $obLstItens->ultimoDado->setCampo( "descricao_item" );
    $obLstItens->commitDado();

    $obLstItens->addDado();
    $obLstItens->ultimoDado->setAlinhamento ( 'DIREITA' );
    $obLstItens->ultimoDado->setCampo( 'quantidade' );
    $obLstItens->commitDado();

    $obLstItens->addDado();
    $obLstItens->ultimoDado->setAlinhamento ( 'DIREITA' );
    $obLstItens->ultimoDado->setCampo( 'valor_referencia' );
    $obLstItens->commitDado();

    $obLstItens->addDado();
    $obLstItens->ultimoDado->setAlinhamento ( 'DIREITA' );
    $obLstItens->ultimoDado->setCampo( 'valor_ultima_compra' );
    $obLstItens->commitDado();

    $obChkSelecione = new Radio;
    $obChkSelecione->setId                  ( "chkseleciona_"                       );
    $obChkSelecione->setName                ( "chkseleciona_"                       );
    $obChkSelecione->obEvento->setOnClick   ( "selecionaItem_Lote( this, 'item' ); jQuery('#Ok').attr('disabled', disabled);" );

    $obChkSelecione->setValue               ( "codigo"                              );
    $obLstItens->addDadoComponente          ( $obChkSelecione                       );

    $obLstItens->ultimoDado->setAlinhamento ( 'CENTRO'                              );

    $obLstItens->ultimoDado->setCampo                 ( "selecionar"       );
    $obLstItens->commitDadoComponente       (                                       );

    $obLstItens->montaHTML();
    $stHtml = $obLstItens->getHTML();
    $stHtml = str_replace("\n","",$stHtml);
    $stHtml = str_replace("  ","",$stHtml);
    $stHtml = str_replace("'","\\'",$stHtml);

    $stJs = "jQuery('#spnItens').html('".$stHtml."');";

    return $stJs;
}

/*
Busca os dados dos fornecedores para item e faz a classificação de cada um
*/
function recuperaFornecedoresPorItem($stExercicio= '', $inCod_mapa = '', $inCod_item = '', $inLote)
{
    $rsRecordSet = new Recordset;

    if ($stExercicio) {
        include_once ( CAM_GP_COM_MAPEAMENTO.'TComprasCotacaoFornecedorItem.class.php');
        $obTComprasCotacaoFornecedorItem = new TComprasCotacaoFornecedorItem;
        $stFiltro = " where mapa_cotacao.exercicio_mapa = '$stExercicio'
                        and mapa_cotacao.cod_mapa          = $inCod_mapa
                        and cotacao_item.cod_item          = $inCod_item
                        and cotacao_item.lote              = $inLote
                        AND NOT EXISTS
                        (
                           SELECT  1
                             FROM  compras.cotacao_anulada
                            WHERE  cotacao_anulada.cod_cotacao = mapa_cotacao.cod_cotacao
                              AND  cotacao_anulada.exercicio = mapa_cotacao.exercicio_cotacao
                        ) ";

        /// ordenando por valor de cotacao o fornecedor com menor vaor para o item ganha

        # Quando o cod_tipo_objeto for = 4, sugere o maior preço como vencedor.
        $stOrderBy = "julgamento_item.ordem, status, vl_total ";
        $stOrderBy .= ($_REQUEST['inCodTipoObjeto'] == 4) ? "DESC" : "ASC";

        $obTComprasCotacaoFornecedorItem->recuperaRelacionamento( $rsRecordSet , $stFiltro, $stOrderBy );

        $inOrdem = 1;
        while ( !$rsRecordSet->eof() ) {
            $rsRecordSet->setCampo ( 'ordem', $inOrdem );
            if ( $rsRecordSet->getCampo ( 'status' ) == 'classificado' ) {
                 $rsRecordSet->setCampo ( 'cod_status', 0 ) ;
            } else {
                 $rsRecordSet->setCampo ( 'cod_status', 1 ) ;
            }

            $rsRecordSet->setCampo ( 'justificativa'       , $rsRecordSet->getCampo('justificativa') );
            $inOrdem++;
            $rsRecordSet->proximo();
        }
        $rsRecordSet->setPrimeiroElemento();
    }

    return $rsRecordSet->getElementos();
}

/*
Busca os itens de cada fornecedor para quando o mapa for por lote ou global
*/
function recuperaItensFornecedores($stExercicioMapa, $inCodCotacao, $inLote, $inCgmFornecedor)
{
    if ($stExercicioMapa) {
        include_once ( CAM_GP_COM_MAPEAMENTO.'TComprasCotacaoFornecedorItem.class.php');
        $obTComprasCotacaoFornecedorItem = new TComprasCotacaoFornecedorItem;
        $stFiltro = "where cotacao_fornecedor_item.exercicio         = '$stExercicioMapa'
                       and cotacao_fornecedor_item.cod_cotacao       = $inCodCotacao
                       and cotacao_fornecedor_item.lote              = $inLote
                       and cotacao_fornecedor_item.cgm_fornecedor    = $inCgmFornecedor ";

        $obTComprasCotacaoFornecedorItem->recuperaItensFornecedorLote( $rsRecordSet , $stFiltro  );
    }

    return $rsRecordSet->getElementos();
}

function recuperaFornecedoresPorLote($stExercicio, $inCodCotacao , $inLote)
{
    $arItens = array();
    $rsRecordSet = new Recordset;
    include_once ( CAM_GP_COM_MAPEAMENTO.'TComprasCotacaoItem.class.php'                           );
    include_once ( CAM_GP_COM_MAPEAMENTO.'TComprasCotacaoFornecedorItem.class.php'                 );
    include_once ( CAM_GP_COM_MAPEAMENTO.'TComprasCotacaoFornecedorItemDesclassificacao.class.php' );

    $obTComprasCotacaoItem = new TComprasCotacaoItem;
    $obTComprasCotacaoItem->setDado ( 'exercicio'   , $stExercicio  );
    $obTComprasCotacaoItem->setDado ( 'cod_cotacao' , $inCodCotacao );
    $obTComprasCotacaoItem->setDado ( 'lote'        , $inLote       );
    $obTComprasCotacaoItem->recuperaFornecedoresPorLote( $rsRecordSet, '', " order by status, ordem, vl_total " );

    $obTComprasCotacaoFornecedorItem = new TComprasCotacaoFornecedorItem;
    $obTComprasCotacaoFornecedorItem->obTComprasCotacaoItem = & $obTComprasCotacaoItem;

    $obTComprasCotacaoFornecedorItemDesclassificacao = new TComprasCotacaoFornecedorItemDesclassificacao;

    ///buscando os detalhes de cada iten
    $inOrdem = 1;
    while ( !$rsRecordSet->eof() ) {
        $rsRecordSet->setCampo ('ordem', $inOrdem);
        if ( $rsRecordSet->getCampo ( 'status' ) == 'classificado' ) {
             $rsRecordSet->setCampo ( 'cod_status', 0 ) ;
             $rsRecordSet->setCampo ( 'justificativa', $rsRecordSet->getCampo('justificativa') );
        } else {

             $rsRecordSet->setCampo ( 'cod_status', 1 ) ;

             ///// buscando a justificativa
             $obTComprasCotacaoFornecedorItemDesclassificacao->setDado ( 'cgm_fornecedor', $rsRecordSet->getCampo ('cgm_fornecedor') );
             $obTComprasCotacaoFornecedorItemDesclassificacao->setDado ( 'cod_cotacao'   , $inCodCotacao                             );
             $obTComprasCotacaoFornecedorItemDesclassificacao->setDado ( 'lote'          , $inLote                                   );
             $obTComprasCotacaoFornecedorItemDesclassificacao->setDado ( 'exercicio'     , $stExercicio                              );
             $obTComprasCotacaoFornecedorItemDesclassificacao->consultar();
             if ($obTComprasCotacaoFornecedorItemDesclassificacao->getDado('justificativa')) {
                 $rsRecordSet->setCampo ( 'justificativa', $obTComprasCotacaoFornecedorItemDesclassificacao->getDado ( 'justificativa' ) );
             } else {
                 $inCodCgmFornecedor = $rsRecordSet->getCampo ('cgm_fornecedor');

                 include_once CAM_GP_COM_MAPEAMENTO.'TComprasFornecedor.class.php';
                 $obTComprasFornecedor = new TComprasFornecedor();
                 $obTComprasFornecedor->setDado("cgm_fornecedor", $inCodCgmFornecedor);
                 $obTComprasFornecedor->recuperaListaFornecedor( $rsFornecedor );

                 if ($rsFornecedor->getCampo('status') == 'Inativo') {
                     $rsRecordSet->setCampo ( 'justificativa', 'Fornecedor Inativo.' );
                 }
             }
        }
        $inOrdem++;
        $rsRecordSet->proximo();
    }

    $rsRecordSet->setPrimeiroElemento();

    return $rsRecordSet->getElementos();
}

/*Apenas exibe os dados dos fornedores
 quando a montagem da lista for feita por esta função o mapa é por item cod_tipo_licitacao = 1
*/
function montaSpanFornecedoresItem($codItem)
{
    $rsRecordSet = new Recordset;

    Sessao::write('stTipoBusca', 1);
    $arFornecedores = Sessao::read('arFornecedores');

    $numeroClassificados = 0;

    // Verifica o número de classificados.
    foreach ($arFornecedores[$codItem] as $chave => $registros) {
        if ($registros['cod_status'] == 0) {
            $numeroClassificados++;
            $posicaoVencedor = $chave;
        }
    }

    // Se existir apenas 1 classificado, seta como vencedor.
    if ($numeroClassificados == 1) {
        $arFornecedores[$codItem][$posicaoVencedor]['status'] = "vencedor";
    }

    if ($arFornecedores[$codItem][0]['julgado'] != 'true') {
        // Validação para setar os empatados na primeira vez (ainda sem julgamento).
        foreach ($arFornecedores as $arChaveDados => $arDados) {
            foreach ($arDados as $chave => $registro) {

                if (strtolower($arFornecedores[$arChaveDados][$chave]['status']) != "vencedor" &&
                    strtolower($arFornecedores[$arChaveDados][$chave]['status']) != "desclassificado")
                {
                    $vlVencedor = $registro['vl_total'];
                    $status     = $registro['status'];
                    $tipo       = $registro['tipo'];
                    $chave2=$chave+1;
                    if ($tipo == 'N') {
                        $keyFornPadrao = $chave;
                    }
                    if (isset($arDados[$chave2])) {
                        if ($vlVencedor == $arDados[$chave2]['vl_total'] &&
                            strtolower($arFornecedores[$arChaveDados][$chave2]['status']) == "classificado" &&
                            $arFornecedores[$arChaveDados][$chave2]['alterado'] != true)
                        {
                            $arFornecedores[$arChaveDados][$chave]['status']   = "empatado";
                            $arFornecedores[$arChaveDados][$chave2]['status'] = "empatado";
                        } else {

                            if (($tipo == 'N' and $arDados[$chave2]['tipo'] != 'N') and
                                ((($arDados[$chave2]['vl_total'] - $vlVencedor)/$vlVencedor) <= 0.1) and
                                strtolower($arFornecedores[$arChaveDados][$chave2]['status']) == "classificado" and
                                $arFornecedores[$arChaveDados][$chave2]['alterado'] != true)
                            {
                                    $arFornecedores[$arChaveDados][$chave]['status']   = "empatado";
                                    $arFornecedores[$arChaveDados][$chave2]['status'] = "empatado";
                                    $vlFornCompara = $arDados[$chave2]['vl_total'];
                            } elseif (($keyFornPadrao) and ($tipo != 'N' and $arDados[$chave2]['tipo'] != 'N') and
                                ((($arDados[$chave2]['vl_total'] - $arDados[$keyFornPadrao]['vl_total'])/$arDados[$keyFornPadrao]['vl_total']) <= 0.1) and
                                strtolower($arFornecedores[$arChaveDados][$chave2]['status']) == "classificado" and
                                $arFornecedores[$arChaveDados][$chave2]['alterado'] != true)
                            {
                                    $arFornecedores[$arChaveDados][$chave2]['status'] = "remanescente";
                                    $vlFornCompara = $arDados[$chave2]['vl_total'];
                            }
                        }
                    }
                }
            }
        }
    }

    // Seta o Vencedor caso o primeiro fornecedor tenha o preço mais baixo e não esteja empatado.
    if ($arFornecedores[$codItem][0]['status'] != "empatado" && $arFornecedores[$codItem][0]['status'] != "desclassificado")
        $arFornecedores[$codItem][0]['status'] = "vencedor";

    if (is_array($arFornecedores)) {
        $rsRecordSet->preenche($arFornecedores[$codItem]);
    }

    // Formatação dos textos para ser usado na listagem.
    while (!$rsRecordSet->eof()) {
        $rsRecordSet->setCampo('descricao' , stripslashes($rsRecordSet->getCampo('descricao')));
        $rsRecordSet->setCampo('status'    , ucfirst($rsRecordSet->getCampo('status')));
        $rsRecordSet->setCampo('cod_item'  , $codItem);
        $rsRecordSet->proximo();
    }

    $rsRecordSet->setPrimeiroElemento();

    if( !strstr($_REQUEST['stAcao'],'excluir') )
        $table = new TableTree();
    else
        $table = new Table();

    $rsRecordSet->addFormatacao ( 'vl_unitario', 'NUMERIC_BR' );
    $rsRecordSet->addFormatacao ( 'vl_total', 'NUMERIC_BR' );

    $table->setRecordset   ( $rsRecordSet  );
    $table->setSummary     ( 'Fornedores'  );
    //$table->setConditional ( true , "#ddd" );
    if( !strstr($_REQUEST['stAcao'],'excluir') )
        $table->setArquivo( CAM_GP_LIC_INSTANCIAS . 'processoLicitatorio/OCManterJulgamentoProposta.php' );

    $table->Head->addCabecalho( 'Participante'   , 60 );
    $table->Head->addCabecalho( 'Marca'          , 30 );
    $table->Head->addCabecalho( 'Valor Unitário' ,  5 );
    $table->Head->addCabecalho( 'Valor Total   ' , 5  );
    $table->Head->addCabecalho( 'Status'         , 5  );

    $table->Body->addCampo( 'nom_cgm'    );
    $table->Body->addCampo( 'descricao'  );
    $table->Body->addCampo( 'vl_unitario');
    $table->Body->addCampo( 'vl_total'   );
    $table->Body->addCampo( 'status'     );

    if ( !strstr($_REQUEST['stAcao'],'excluir') ) {
        $stParamAdicionais  = "&stCtrl=montaDetalheItemFornecedor";
        $table->setComplementoParametros( $stParamAdicionais );
        $table->setParametros( array('ordem', 'cod_item') );
    }

    $table->montaHTML();
    $stHTML = $table->getHtml();
    $stHTML = str_replace("\n","",$stHTML);
    $stHTML = str_replace("  ","",$stHTML);
    $stHTML = str_replace("'","\\'",$stHTML);

    $stJs = "jQuery('#spnFornecedores').html('".$stHTML."');";

    Sessao::write('arFornecedores', $arFornecedores);

    return $stJs;
}

function montaDetalheItemFornecedor($inOrdem, $inCodItem)
{
    $arFornecedores = Sessao::read('arFornecedores');

    $inPos = 0;
    while (( $inPos <= count( $arFornecedores[$inCodItem] )) and ($arFornecedores[$inCodItem][$inPos]['ordem'] != $inOrdem)) {
        $inPos++;
    }

    $arRegistro = $arFornecedores[$inCodItem][$inPos];

    $arRegistro['vl_total'   ] = number_format($arRegistro['vl_total']   , 2, ',', '.' );
    $arRegistro['vl_unitario'] = number_format($arRegistro['vl_unitario'], 2, ',', '.' );
    $arRegistro['quantidade' ] = number_format($arRegistro['quantidade'] , 4, ',', '.' );

    if ($arRegistro['tipo'] == 'N') {
        $tipoFornecedor = "Empresa Padrão";
    } elseif ($arRegistro['tipo'] == 'M') {
        $tipoFornecedor = "Microempresa";
    } else {
        $tipoFornecedor = "Empresa de Pequeno Porte";
    }

    $obHdnFornecedor = new Hidden;
    $obHdnFornecedor->setName  ( 'hdnFornecedor' );
    $obHdnFornecedor->setId    ( 'hdnFornecedor' );
    $obHdnFornecedor->setValue ( $arRegistro['cgm_fornecedor'] );

    $obHdnCodItem = new Hidden;
    $obHdnCodItem->setName  ( 'inCodItem' );
    $obHdnCodItem->setId    ( 'inCodItem' );
    $obHdnCodItem->setValue ( $inCodItem  );

    $obHdnOrdem = new Hidden;
    $obHdnOrdem->setName  ( 'ordem'  );
    $obHdnOrdem->setId    ( 'ordem'  );
    $obHdnOrdem->setValue ( $inOrdem );

    $lblParticipante = new Label;
    $lblParticipante->setId     ( 'lblParticipante' );
    $lblParticipante->setRotulo ( 'Participante'    );
    $lblParticipante->setValue  ( $arRegistro['nom_cgm']);

    $lblTipoFornecedor = new Label;
    $lblTipoFornecedor->setId     ( 'lblTipoFornecedor'  );
    $lblTipoFornecedor->setRotulo ( 'Tipo de Fornecedor' );
    $lblTipoFornecedor->setValue  ( $tipoFornecedor      );

    $lblItem = new Label;
    $lblItem->setId     ( 'lblItem' );
    $lblItem->setRotulo ( 'Item'    );
    $lblItem->setValue  ( $arRegistro['item'] );

    $lblLote = new Label;
    $lblLote->setId     ( 'lblLote' );
    $lblLote->setRotulo ( 'Lote'    );
    $lblLote->setValue  ( $arRegistro['lote']    );

    $lblQuantidade = new Label;
    $lblQuantidade->setId     ( 'lblQuantidade' );
    $lblQuantidade->setRotulo ( 'Quantidade'    );
    $lblQuantidade->setValue  ( $arRegistro['quantidade']   );

    $lblValorUnitario = new Label;
    $lblValorUnitario->setId     ( 'lblvalorUni' );
    $lblValorUnitario->setRotulo ( 'Valor Unitário'    );
    $lblValorUnitario->setValue  ( $arRegistro['vl_unitario'] );

    $lblValorTotal = new Label;
    $lblValorTotal->setId     ( 'lblvalorTot' );
    $lblValorTotal->setRotulo ( 'Valor Total'    );
    $lblValorTotal->setValue  ( $arRegistro['vl_total']   );

    $inCodStatus = (($arRegistro['status'] == 'classificado') || ($arRegistro['status'] == 'vencedor') || ($arRegistro['status'] == 'empatado'))? 0 : 1  ;
    $obCmbStatus = new Select;
    $obCmbStatus->setName       ( "stStatus"             );
    $obCmbStatus->setId         ( "stStatus"             );
    $obCmbStatus->setRotulo     ( "Status"               );
    $obCmbStatus->setValue      ( $inCodStatus           );
    $obCmbStatus->setNull       ( false                  );
    $obCmbStatus->addOption     ( "0", "Classificado"    );
    $obCmbStatus->addOption     ( "1", "Desclassificado" );
    $obCmbStatus->obEvento->setOnChange (" if (jQuery(this).val() == 1) { jQuery('#stClassificacao').attr('disabled', 'disabled'); } else { jQuery('#stClassificacao').attr('disabled', ''); } ");

    $inTotalFornecedor = count($arFornecedorItem);

    $obCmbClassificacao = new Select;
    $obCmbClassificacao->setName   ( "stClassificacao" );
    $obCmbClassificacao->setId     ( "stClassificacao" );
    $obCmbClassificacao->setRotulo ( "Classificação"   );
    $obCmbClassificacao->setNull   ( false             );

    $inIdOrdem = $arRegistro['ordem'];

    if ($inIdOrdem > 1) {
        $obCmbClassificacao->addOption ($inIdOrdem, $inIdOrdem);
        $obCmbClassificacao->addOption ($inIdOrdem-1, $inIdOrdem-1);
    }

    $obCmbClassificacao->setValue($arRegistro['ordem']);

    if ($arRegistro['ordem'] == 1) {
        $obLblClassificacao = new Label;
        $obLblClassificacao->setRotulo ( 'Classificação' );
        $obLblClassificacao->setId     ( "stClassificacao" );
        $obLblClassificacao->setName   ( "stClassificacao" );
        $obLblClassificacao->setValue  ( $arRegistro['ordem'] );
    }

    $obTxtJustificativa = new TextArea;
    $obTxtJustificativa->setId     ( 'txtJustificativa' );
    $obTxtJustificativa->setName   ( 'txtJustificativa' );
    $obTxtJustificativa->setRotulo ( 'Justificativa'    );
    $obTxtJustificativa->setValue  ( $arRegistro['justificativa'] );

    $obFormulario = new Formulario();
    $obFormulario->addHidden( $obHdnOrdem   );
    $obFormulario->addHidden( $obHdnFornecedor );
    $obFormulario->addHidden( $obHdnCodItem );
    $obFormulario->addComponente( $lblParticipante    );
    $obFormulario->addComponente( $lblTipoFornecedor  );
    $obFormulario->addComponente( $lblItem            );
    $obFormulario->addComponente( $lblLote            );
    $obFormulario->addComponente( $lblQuantidade      );
    $obFormulario->addComponente( $lblValorUnitario   );
    $obFormulario->addComponente( $lblValorTotal      );
    $obFormulario->addComponente( $obCmbStatus        );

    $obOk = new Ok;
    $obOk->setName ( 'btoOk' );
    $obOk->setValue ( 'Salvar' );

    if ($arRegistro['ordem'] == 1) {
        $obFormulario->addComponente( $obLblClassificacao );

        $obOk->obEvento->setOnClick( "var codItem = jQuery('#inCodItem').val();
                                  var fornecedor = jQuery('#hdnFornecedor').val();
                                  var status = jQuery('#stStatus option:selected').val();
                                  var justificativa = jQuery('#txtJustificativa').val();
                                  var classificacao = jQuery('#stClassificacao').text();
                                  executaFuncaoAjax('salvarAlteracaoDados','&inCodItem='+codItem+'&hdnFornecedor='+fornecedor+'&stStatus='+status+'&txtJustificativa='+justificativa+'&stClassificacao='+classificacao);"
                                );

    } else {
        $obFormulario->addComponente ( $obCmbClassificacao );

        $obOk->obEvento->setOnClick( "var codItem = jQuery('#inCodItem').val();
                                  var fornecedor = jQuery('#hdnFornecedor').val();
                                  var status = jQuery('#stStatus option:selected').val();
                                  var justificativa = jQuery('#txtJustificativa').val();
                                  var classificacao = jQuery('#stClassificacao option:selected').val();
                                  executaFuncaoAjax('salvarAlteracaoDados','&inCodItem='+codItem+'&hdnFornecedor='+fornecedor+'&stStatus='+status+'&txtJustificativa='+justificativa+'&stClassificacao='+classificacao);"
                                );

    }

    $obFormulario->addComponente( $obTxtJustificativa );
    $obFormulario->defineBarra( array( $obOk ) );
    $obFormulario->show();
}

function montaClusterLabels($stMapaCompras = '', $boExigeLicitacao = true)
{
    if ($stMapaCompras) {
        $obForm = new Form;

        $obFormulario = new Formulario();

        list ( $inCodMapa , $stExercicioMapa ) = explode ( '/' , $stMapaCompras);
        list ( $inCodLicitacaoBusca , $stExercicioLicitacao ) = explode ( '/' , $_REQUEST['inCodLicitacao']);

        $stExercicioMapa = ($stExercicioMapa == '') ? Sessao::getExercicio() : $stExercicioMapa;

        include_once ( CAM_GP_COM_MAPEAMENTO.'TComprasMapa.class.php' );
        $obTComprasMapa = new TComprasMapa;

        if ($boExigeLicitacao) {
            $stFiltro = " and licitacao.cod_mapa = $inCodMapa and licitacao.exercicio = '$stExercicioMapa' ";
            $obTComprasMapa->recuperaMapaLicitacao ( $rsMapa , $stFiltro );
            if ( isset($rsMapa->getElementos) && $rsMapa->getElementos > 0 ) {
                $stErro = "Já existe um adjudicação para este mapa   $inCodMapa/$stExercicioMapa ";
            }
        } else {
            //neste caso o mapa não pode estar em processo licitatorio
            include_once ( CAM_GP_LIC_MAPEAMENTO . "TLicitacaoLicitacao.class.php" ) ;
            $obTLicitacaoLicitacao = new TLicitacaoLicitacao;

            $stFiltro = "\n  where not exists ( select 1
                              from licitacao.licitacao_anulada
                             where licitacao_anulada.cod_licitacao  = licitacao.cod_licitacao
                               and licitacao_anulada.cod_modalidade = licitacao.cod_modalidade
                               and licitacao_anulada.cod_entidade   = licitacao.cod_entidade
                               and licitacao_anulada.exercicio      = licitacao.exercicio )
                               and licitacao.cod_mapa = $inCodMapa
                               and licitacao.exercicio_mapa = '$stExercicioMapa'  ";

             $obTLicitacaoLicitacao->recuperaTodos ( $rsLicitacoes, $stFiltro );

             if ( $rsLicitacoes->getNumLinhas ( ) > 0 ) {
                 $stErro = "Já existe um processo licitatório para este mapa   $inCodMapa/$stExercicioMapa ";
             }
        }

        if ( isset($stErro) ) {
            $stJs = $stJs .= "alertaAviso('$stErro ','n_erro','erro','".Sessao::getId()."');\n";
        } else {

            $obTComprasMapa->setDado ( 'exercicio', $stExercicioMapa );
            $obTComprasMapa->setDado ( 'cod_mapa',  $inCodMapa       );
            $obTComprasMapa->consultar();

            if ( !$obTComprasMapa->getDado( 'cod_tipo_licitacao' ) ) {
                $stErro = "Código de mapa não encontrado.";
            } else {

                $boJulgar = true ;
                if ( $obTComprasMapa->getDado ( 'cod_tipo_licitacao' ) != 1 ) {

                    include_once ( CAM_GP_COM_MAPEAMENTO.'TComprasCotacao.class.php' );
                    $stFiltro = " where quantidade_itens >= quantidade_itens_fornecedor
                                    and mapa_cotacao.cod_mapa = $inCodMapa
                                    and mapa_cotacao.exercicio_mapa = '$stExercicioMapa'  " ;

                    $obTComprasCotacao = new TComprasCotacao;
                    $obTComprasCotacao->recuperaQuantidadeItensCotacaoFornecedor( $rsQuanti , $stFiltro );

                    if ( !($rsQuanti->getNumLinhas () > 0) ) {
                        $stErro = "Mapa de Compras  $inCodMapa /$stExercicioMapa contém itens que não foram cotados por todos os fornecedores.";
                    }
                }
            }

            if ( isset($stErro) ) {
                $stJs .= "alertaAviso('$stErro','n_erro','erro','".Sessao::getId()."');\n";
                $stJs .= "jQuery('#spnLabels').html('');\n";
                $stJs .= "jQuery('#spnItens').html('');\n";
            } else {

                require_once ( CAM_GP_LIC_COMPONENTES . 'IClusterLabelsMapa.class.php' );

                if ($boExigeLicitacao) {
                    $obCluster = new IClusterLabelMapa ( $obForm , $inCodMapa , $stExercicioMapa );

                    $stFiltroLicitacao = " AND licitacao.cod_licitacao =".$inCodLicitacaoBusca;
                    $stFiltroLicitacao .= " AND licitacao.cod_modalidade =".$_REQUEST['inCodModalidade'];

                    $obCluster->setFiltro($stFiltroLicitacao);
                    $obCluster->geraFormulario ( $obFormulario );
                } else {
                    include_once( CAM_GP_COM_MAPEAMENTO.'TComprasCompraDireta.class.php' );
                    $obTComprasCompraDireta = new TComprasCompraDireta();
                    $obTComprasCompraDireta->setDado('cod_mapa',$inCodMapa);
                    $obTComprasCompraDireta->setDado('exercicio_mapa',$stExercicioMapa);
                    $obTComprasCompraDireta->recuperaCompraDiretaPorMapa( $rsCompraDireta );

                    $obLblObjeto = new Label();
                    $obLblObjeto->setRotulo('Objeto');
                    $obLblObjeto->setValue( $rsCompraDireta->getCampo( 'cod_objeto' ) . ' - ' . stripslashes(nl2br(str_replace('\r\n', '\n', preg_replace('/(\r\n|\n|\r)/', ' ', $rsCompraDireta->getCampo('objeto'))))) );

                    $obLblMapaCompra = new Label();
                    $obLblMapaCompra->setRotulo( 'Mapa de Compras' );
                    $obLblMapaCompra->setValue( $rsCompraDireta->getCampo( 'cod_mapa' ).'/'.$rsCompraDireta->getCampo( 'exercicio_mapa' ) );

                    $obLblModalidade = new Label();
                    $obLblModalidade->setRotulo( 'Modalidade' );
                    $obLblModalidade->setValue( $rsCompraDireta->getCampo( 'cod_modalidade' ).' - '.$rsCompraDireta->getCampo( 'modalidade' ) );

                    $obLblEntidade = new Label();
                    $obLblEntidade->setRotulo( 'Entidade' );
                    $obLblEntidade->setValue( $rsCompraDireta->getCampo( 'cod_entidade' ).' - '.$rsCompraDireta->getCampo( 'entidade' ) );

                    $obLblCompraDireta = new Label();
                    $obLblCompraDireta->setRotulo( 'Compra Direta' );
                    $obLblCompraDireta->setValue( $rsCompraDireta->getCampo( 'cod_compra_direta' ) );

                    $obFormulario->addComponente( $obLblEntidade    );
                    $obFormulario->addComponente( $obLblMapaCompra  );
                    $obFormulario->addComponente( $obLblCompraDireta );
                    $obFormulario->addComponente( $obLblObjeto      );
                    $obFormulario->addComponente( $obLblModalidade  );
                }
                $obFormulario->montaInnerHTML();
                $stHtml = $obFormulario->getHTML();
                $stJs  = "jQuery('#spnLabels').html('" . $stHtml . "');\n";

                $stJs .= recuperaItens ($inCodMapa , $stExercicioMapa  );
            }
        }
    } else {
        $stJs  = "jQuery('#spnLabels').html('');\n";
        $stJs  = "jQuery('#spnItens').html('');\n";
    }
    $stJs .= "jQuery('#spnFornecedores').html('');\n";

    return $stJs;
}

function montaSpanLoteFornecedor($inLote, $inCgmForncedor)
{
    $rsRecordSet = new Recordset;
    Sessao::write('stTipoBusca', 2);

    $arFornecedores = Sessao::read('arFornecedores');
    if ( is_array( $arFornecedores ) ) {
        $rsRecordSet->preenche( $arFornecedores[$codItem]);
    }

    $rsRecordSet->setCampo( 'cod_item', $codItem, true );

    // Montagem Lista
    $obLista = new Lista;
    $obLista->setTitulo          ( 'Resultado'  );
    $obLista->setMostraPaginacao ( false       );
    $obLista->setRecordset       ( $rsRecordSet );

    // Cabeçalho da lista
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo ( "&nbsp;"     );
    $obLista->ultimoCabecalho->setWidth    ( 3            );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo ( "Item");
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo ( "Marca"     );
    $obLista->ultimoCabecalho->setWidth    ( 30          );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo ( "Valor Unitário"     );
    $obLista->ultimoCabecalho->setWidth    ( 5            );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo ( "Valor Total" );
    $obLista->ultimoCabecalho->setWidth    ( 5             );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo ( "Status"     );
    $obLista->ultimoCabecalho->setWidth    ( 5            );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo ( "Ação" );
    $obLista->ultimoCabecalho->setWidth    ( 5      );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( 'item'  );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( 'marca' );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( 'vl_unitario'  );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( 'vl_total' );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( 'status' );
    $obLista->commitDado();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao ( 'classificacao' );
    $obLista->ultimaAcao->setFuncao( true );
    $obLista->ultimaAcao->setLink ( "JavaScript:alterar();" );
    $obLista->ultimaAcao->addCampo( "1","ordem"     );
    $obLista->ultimaAcao->addCampo( "2","cod_item"  );
    $obLista->commitAcao();

    $obLista->montaHTML();
    $stHtml = $obLista->getHTML();
    $stHtml = str_replace("\n","",$stHtml);
    $stHtml = str_replace("  ","",$stHtml);
    $stHtml = str_replace("'","\\'",$stHtml);

    $stJs = "jQuery('#spnFornecedores').html('".$stHtml."');";

    return $stJs;
}

function montaDetalheLoteFornecedor($inLote, $inCgm)
{
    $rsRecordSet = new Recordset;
    $arFornecedores = Sessao::read('arFornecedores');

    if ( is_array( $arFornecedores ) ) {
        $rsRecordSet->preenche( $arFornecedores[$inLote]);
    }

    while ( (!$rsRecordSet->eof() ) and ( $rsRecordSet->getCampo( 'cgm_fornecedor' ) != $inCgm) ) {
        $rsRecordSet->proximo();
    }

    if ($rsRecordSet->getCampo( 'tipo' ) == 'N') {
        $tipoEmpresa = "Empresa Padrão";
    } elseif ($rsRecordSet->getCampo( 'tipo' ) == 'M') {
        $tipoEmpresa = "Microempresa";
    } else {
        $tipoEmpresa = "Empresa de Pequeno Porte";
    }

    $obLblLote = new label;
    $obLblLote->setRotulo ( 'Lote' );
    $obLblLote->setValue  ( $rsRecordSet->getCampo( 'lote' ) );

    $obLblFornecedor = new label;
    $obLblFornecedor->setRotulo ( 'Fornecedor' );
    $obLblFornecedor->setValue  ( $rsRecordSet->getCampo( 'cgm_fornecedor' ) . ' - ' .  $rsRecordSet->getCampo( 'nom_cgm' ) );

    $obLblValor = new label;
    $obLblValor->setRotulo ( 'Valor total' );
    $obLblValor->setValue  ( $rsRecordSet->getCampo( 'vl_total' ) );

    $obHdnTableRow = new Hidden;
    $obHdnTableRow->setName ( 'hdnTableRow' );
    $obHdnTableRow->setId ( 'hdnTableRow' );
    $obHdnTableRow->setValue ( $_REQUEST['linha_table_tree'] );

    $obHdnLote = new Hidden;
    $obHdnLote->setId  ( 'hdnLote' );
    $obHdnLote->setName  ( 'hdnLote' );
    $obHdnLote->setValue ( $inLote   );

    $obHdnFornecedor = new Hidden;
    $obHdnFornecedor->setId    ( 'hdnFornecedor' );
    $obHdnFornecedor->setName  ( 'hdnFornecedor' );
    $obHdnFornecedor->setValue ( $rsRecordSet->getCampo( 'cgm_fornecedor' ) );

    $lblTipoFornecedor = new Label;
    $lblTipoFornecedor->setId ( 'lblTipoFornecedor' );
    $lblTipoFornecedor->setRotulo ('Tipo de Fornecedor' );
    $lblTipoFornecedor->setValue ( $tipoEmpresa );

    $inCodStatus = (($rsRecordSet->getCampo('status') == 'classificado') || ($rsRecordSet->getCampo('status') == 'vencedor') || ($rsRecordSet->getCampo('status') == 'empatado'))? 0 : 1  ;
    $obCmbStatus = new Select;
    $obCmbStatus->setName       ( "stStatus"             );
    $obCmbStatus->setId         ( "stStatus"             );
    $obCmbStatus->setRotulo     ( "Status"               );
    $obCmbStatus->setValue      ( $inCodStatus           );
    $obCmbStatus->setNull       ( false                  );
    $obCmbStatus->addOption     ( "0", "Classificado"    );
    $obCmbStatus->addOption     ( "1", "Desclassificado" );
    $obCmbStatus->obEvento->setOnChange (" if (jQuery(this).val() == 1) { jQuery('#stClassificacao').attr('disabled', 'disabled'); } else { jQuery('#stClassificacao').attr('disabled', ''); } ");

    $inTotalFornecedor = count($arFornecedores[$inLote]);

    $obCmbClassificacao = new Select;
    $obCmbClassificacao->setName   ( "stClassificacao" );
    $obCmbClassificacao->setId     ( "stClassificacao" );
    $obCmbClassificacao->setRotulo ( "Classificação"   );
    $obCmbClassificacao->setNull   ( false             );

    $inIdOrdem = $rsRecordSet->getCampo('ordem');

    if ($inIdOrdem > 1) {
        $obCmbClassificacao->addOption ($inIdOrdem, $inIdOrdem);
        $obCmbClassificacao->addOption ($inIdOrdem-1, $inIdOrdem-1);
    }

    $obCmbClassificacao->setValue($rsRecordSet->getCampo('ordem'));

    if ($rsRecordSet->getCampo('ordem') == 1) {
        $obLblClassificacao = new Label;
        $obLblClassificacao->setRotulo ( 'Classificação' );
        $obLblClassificacao->setValue  ( $rsRecordSet->getCampo( 'ordem' ) );
    }

    $obTxtJustificativa = new TextArea;
    $obTxtJustificativa->setRotulo ( 'Justificativa'            );
    $obTxtJustificativa->setName   ( 'txtJustificativa'         );
    $obTxtJustificativa->setId     ( 'txtJustificativa'         );
    $obTxtJustificativa->setValue  ( $rsRecordSet->getCampo('justificativa') );

    $obForm = new Form;
    $obForm->setId('frm2');
    $obForm->setName('frm2');

    $obFormulario = new Formulario();
    $obFormulario->addForm( $obForm );
    $obFormulario->addHidden( $obHdnTableRow   );
    $obFormulario->addHidden( $obHdnLote       );
    $obFormulario->addHidden( $obHdnFornecedor );
    $obFormulario->addComponente ( $obLblLote          );
    $obFormulario->addComponente ( $obLblFornecedor    );
    $obFormulario->addComponente ( $lblTipoFornecedor   );
    $obFormulario->addComponente ( $obLblValor         );
    $obFormulario->addComponente ( $obCmbStatus        );

    if ($rsRecordSet->getCampo('ordem') == 1) {
        $obFormulario->addComponente( $obLblClassificacao );
    }else
        $obFormulario->addComponente ( $obCmbClassificacao );

    $obFormulario->addComponente ( $obTxtJustificativa );

    $obOk = new Ok;
    $obOk->setName ( 'btoOk' );
    $obOk->setValue ( 'Salvar' );
    $obOk->obEvento->setOnClick( "var lote = jQuery('#hdnLote').val();
                                  var tableRow = jQuery('#hdnTableRow').val();
                                  var fornecedor = jQuery('#hdnFornecedor').val();
                                  var status = jQuery('#stStatus').val();
                                  var justificativa = jQuery('#txtJustificativa').val();
                                  executaFuncaoAjax('salvarAlteracaoDados','&hdnLote='+lote+'&hdnTableRow='+tableRow+'&hdnFornecedor='+fornecedor+'&stStatus='+status+'&txtJustificativa='+justificativa);"
                                );

    $obFormulario->defineBarra( array( $obOk ) );
    $obFormulario->show();

    // Desabilita o select de classificação quando o fornecedor estiver desclassificado.
    if ($inCodStatus == 1) {
        echo "<script>jQuery('#stClassificacao').attr('disabled', 'disabled');</script>";
    }

}

function montaSpanFornecedoresLote($inLote)
{
    $rsRecordSet = new Recordset;

    $numeroClassificados = 0;
    $arFornecedores = Sessao::read('arFornecedores');

    // Verifica o número de classificados.
    foreach ($arFornecedores[$inLote] as $chave => $registros) {
        if ($registros['cod_status'] == 0) {
            $numeroClassificados++;
            $posicaoVencedor = $chave;
        }
    }

    // Se existir apenas 1 classificado, seta como vencedor.
    if ($numeroClassificados == 1) {
        $arFornecedores[$inLote][$posicaoVencedor]['status'] = "vencedor";
    }

    if ($arFornecedores[$inLote][0]['julgado'] != 'true') {
        // Validação para setar os empatados na primeira vez.
        foreach ($arFornecedores as $arChaveDados => $arDados) {
            foreach ($arDados as $chave => $registro) {

                if (strtolower($arFornecedores[$arChaveDados][$chave]['status']) != "vencedor" &&
                    strtolower($arFornecedores[$arChaveDados][$chave]['status']) != "desclassificado")
                {
                    $vlVencedor = $registro['vl_total'];
                    $status     = $registro['status'];
                    $tipo       = $registro['tipo'];
                    $chave2=$chave+1;

                    if ($tipo == 'N') {
                        $keyFornPadrao = $chave;
                    }

                    if (isset($arDados[$chave2])) {
                        if ($vlVencedor == $arDados[$chave2]['vl_total'] &&
                            strtolower($arFornecedores[$arChaveDados][$chave2]['status']) == "classificado" &&
                            $arFornecedores[$arChaveDados][$chave2]['alterado'] != true)
                        {
                            $arFornecedores[$arChaveDados][$chave]['status']   = "empatado";
                            $arFornecedores[$arChaveDados][$chave2]['status'] = "empatado";
                        } else {

                            if (($tipo == 'N' and $arDados[$chave2]['tipo'] != 'N') and
                                ((($arDados[$chave2]['vl_total'] - $vlVencedor)/$vlVencedor) <= 0.1) and
                                strtolower($arFornecedores[$arChaveDados][$chave2]['status']) == "classificado" and
                                $arFornecedores[$arChaveDados][$chave2]['alterado'] != true)
                            {
                                    $arFornecedores[$arChaveDados][$chave]['status']   = "empatado";
                                    $arFornecedores[$arChaveDados][$chave2]['status'] = "empatado";
                                    $vlFornCompara = $arDados[$chave2]['vl_total'];
                            } elseif (($keyFornPadrao) and ($tipo != 'N' and $arDados[$chave2]['tipo'] != 'N') and
                                ((($arDados[$chave2]['vl_total'] - $arDados[$keyFornPadrao]['vl_total'])/$arDados[$keyFornPadrao]['vl_total']) <= 0.1) and
                                strtolower($arFornecedores[$arChaveDados][$chave2]['status']) == "classificado" and
                                $arFornecedores[$arChaveDados][$chave2]['alterado'] != true)
                            {
                                    //$arFornecedores[$arChaveDados][$chave]['status']   = "empatado";
                                    $arFornecedores[$arChaveDados][$chave2]['status'] = "remanescente";
                                    $vlFornCompara = $arDados[$chave2]['vl_total'];
                            }
                        }
                    }
                }
            }
        }
    }
    // Seta o Vencedor caso o primeiro fornecedor tenha o preço mais baixo e não esteja empatado.
    if ($arFornecedores[$inLote][0]['status'] != "empatado" && $arFornecedores[$inLote][0]['status'] != "desclassificado") {
        foreach ($arFornecedores[$inLote] as $key => $value) {
            $key++;
            $arFornecedores[$inLote][0]['status'] = "vencedor";
        }
    }

    if (is_array($arFornecedores)) {
        $rsRecordSet->preenche($arFornecedores[$inLote]);
    }

    // Formatação dos textos para ser usado na listagem.
    while (!$rsRecordSet->eof()) {
        $rsRecordSet->setCampo('descricao',  stripslashes($rsRecordSet->getCampo('descricao')));
        $rsRecordSet->setCampo('status',  ucfirst($rsRecordSet->getCampo('status')));
        $rsRecordSet->proximo();
    }

    $rsRecordSet->setPrimeiroElemento();

    //$rsRecordSet->addFormatacao ( 'vl_total', 'NUMERIC_BR' );

    $table = new TableTree();
    $table->setRecordset   ( $rsRecordSet  );
    $table->setSummary     ( 'Fornedores'  );
    //$table->setConditional ( true , "#ddd" );

    $table->setArquivo( CAM_GP_LIC_INSTANCIAS . 'processoLicitatorio/OCManterJulgamentoProposta.php' );

    $stParamAdicionais  = "&stCtrl=montaDetalheLoteFornecedor";
    $table->setComplementoParametros( $stParamAdicionais );
    $table->setParametros( array('lote', 'cgm_fornecedor' ) );

    $table->Head->addCabecalho( 'Fornecedor' , 60  );
    $table->Head->addCabecalho( 'Valor Total', 60  );
    $table->Head->addCabecalho( 'Status'     , 60  );

    $table->Body->addCampo( '[cgm_fornecedor] - [nom_cgm] ' , 'E');
    $table->Body->addCampo( 'vl_total' , 'D');
    $table->Body->addCampo( 'status'  );

    $table->montaHTML();
    $stHTML = $table->getHtml();
    $stHTML = str_replace("\n","",$stHTML);
    $stHTML = str_replace("  ","",$stHTML);
    $stHTML = str_replace("'","\\'",$stHTML);

    $stJs = "jQuery('#spnFornecedores').html('" . $stHTML . "');";

    Sessao::write('arFornecedores', $arFornecedores);

    return $stJs;
}

switch ($stCtrl) {

    case 'montaDetalheItemFornecedor':
        $stJs = montaDetalheItemFornecedor(  $_GET['ordem'] , $_GET['cod_item'] );
    break;

    case 'montaClusterLabels':
        $stJs = montaClusterLabels( $_REQUEST['stMapaCompras'] );
    break;

    case 'montaClusterLabelsDispensa':
        $stJs = montaClusterLabels( $_REQUEST['stMapaCompras'], false );
    break;

    case 'montaSpanFornecedores':

        if ($_REQUEST['tipoBusca'] == 'lote') {
            $stJs = montaSpanFornecedoresLote( $_REQUEST['codigo'] );
        } else {
           $stJs = montaSpanFornecedoresItem( $_REQUEST['codigo'] );
        }
    break;

    case 'montaDetalheLoteFornecedor':
        montaDetalheLoteFornecedor( $_REQUEST['lote'], $_REQUEST['cgm_fornecedor']  ) ;
    break;

    case 'validaDataJulgamento':
        $stDtEmissao = Sessao::read('stDtEmissao');
        $arDataJulgamento = explode('/',$_REQUEST['stDataEmissao']);
        $arDataCompraDireta = explode('/',$stDtEmissao );

        $inDataJulgamento  = $arDataJulgamento[2] . $arDataJulgamento[1] . $arDataJulgamento[0];
        $inDataCompraDireta = $arDataCompraDireta[2] . $arDataCompraDireta[1] . $arDataCompraDireta[0];

        // Não pode ser menor que a data da Compra Direta.
        if ($inDataJulgamento < $inDataCompraDireta) {
            $stJs  = "jQuery('#stDataEmissao').val('".$stDtEmissao."');";
            $stJs .= "jQuery('#stDataEmissao').focus();";
            $stJs .=  "alertaAviso('A data do Julgamento deve ser maior ou igual a data da Compra Direta.','n_erro','erro','".Sessao::getId()."');\n";
        }
        // Não pode ser maior que a data corrente.
        if (!SistemaLegado::comparaDatas(date('d/m/Y'), $_REQUEST['stDataEmissao'], true)) {
            $stJs  = "jQuery('#stDataEmissao').val('".$stDtEmissao."');";
            $stJs .= "jQuery('#stDataEmissao').focus();";
            $stJs .=  "alertaAviso('A data do Julgamento deve ser menor ou igual a data atual.','n_erro','erro','".Sessao::getId()."');\n";
        }

        echo $stJs;
        break;

    case 'salvarAlteracaoDados':

        # Atribui o índice do array a variável. Quando for por lote, passa o id do lote, ao contrário o id do ítem.
        $inCodChave      = (!empty($_REQUEST['inCodItem'])        ? $_REQUEST['inCodItem']        : $_REQUEST['hdnLote']);
        $inCodStatus     = (isset($_REQUEST['stStatus'])          ? $_REQUEST['stStatus']         : "");
        $inCodFornecedor = (!empty($_REQUEST['hdnFornecedor'])    ? $_REQUEST['hdnFornecedor']    : "");
        $stJustificativa = (!empty($_REQUEST['txtJustificativa']) ? $_REQUEST['txtJustificativa'] : "");
        $stClassificacao = (!empty($_REQUEST['stClassificacao'])  ? $_REQUEST['stClassificacao']  : "");

        $arFornecedores = Sessao::read('arFornecedores');

        if (is_array($arFornecedores)) {

            $arAux = array();
            foreach ($arFornecedores[$inCodChave] as $inId => $registros) {

                # Atualiza o array quando o fornecedor for o alterado.
                if ($registros['cgm_fornecedor'] == $inCodFornecedor) {

                    # Tratamento para fornecedores classificados
                    if ($inCodStatus == 0) {

                        # Teste incluido para verificar se foi informado uma justificativa quando o usuário troca a ordem da classificação do fornecedor.
                        if ($registros['ordem'] != 1) {
                            if (($registros['ordem'] != $stClassificacao) && empty($stJustificativa)) {
                                $stJs .= "alertaAviso('Digite a justificativa para a troca da ordem de classificação.','n_erro','erro','".Sessao::getId()."');\n";
                            } elseif ($registros['ordem'] == $stClassificacao) {
                                if (($registros['status'] == "empatado") && (empty($stJustificativa))) {
                                    # Força o usuário a colocar uma mensagem quando o fornecedor estiver empatado e estiver sofrendo uma reclassificação.
                                    $stJs .= "alertaAviso('Digite a justificativa para desempatar o fornecedor.','n_erro','erro','".Sessao::getId()."');\n";
                                } else {
                                    # Atualiza a classificação quando o fornecedor era desclassificado e passou a ser classificado.
                                    $arFornecedores[$inCodChave][$inId]['status']        = ($inCodStatus == 0 ) ? 'classificado' : 'desclassificado';
                                    $arFornecedores[$inCodChave][$inId]['ordem']         = $stClassificacao;
                                    $arFornecedores[$inCodChave][$inId]['cod_status']    = $inCodStatus;
                                    $arFornecedores[$inCodChave][$inId]['justificativa'] = $stJustificativa;

                                    if ($registros['status'] == "empatado")
                                        $arFornecedores[$inCodChave][$inId]['alterado'] = true;
                                }
                            } else {

                                # Faz um backup dos dados do fornecedor anterior para trocar sua posição posteriormente na classificação.
                                $arAux = $arFornecedores[$inCodChave][($inId-1)];

                                # Joga o fornecedor alterado para o novo índice do array conforme sua nova classificação.
                                $arFornecedores[$inCodChave][($inId-1)] = $arFornecedores[$inCodChave][$inId];

                                # Atualiza os dados da nova posição com os setados pelo usuário na tela.
                                $arFornecedores[$inCodChave][($inId-1)]['status']        = ($inCodStatus == 1) ? 'desclassificado' : ($inCodStatus == 0 && $stClassificacao == 1) ? 'vencedor' : 'classificado';
                                $arFornecedores[$inCodChave][($inId-1)]['ordem']         = $stClassificacao;
                                $arFornecedores[$inCodChave][($inId-1)]['cod_status']    = $inCodStatus;
                                $arFornecedores[$inCodChave][($inId-1)]['justificativa'] = $stJustificativa;
                                $arFornecedores[$inCodChave][($inId-1)]['alterado']      = true;

                                # Atualiza o índice do array com os dados do fornecedor da antiga classificação, trocando sua ordem.
                                $arFornecedores[$inCodChave][$inId] = $arAux;
                                $arFornecedores[$inCodChave][$inId]['status']   = (($arAux['status'] == "empatado") || ($arAux['status'] == "desclassificado")) ? $arAux['status'] : "classificado";
                                $arFornecedores[$inCodChave][$inId]['ordem']    = $stClassificacao+1;
                                $arFornecedores[$inCodChave][$inId]['alterado'] = true;
                            }
                        } elseif ($registros['ordem'] == 1) {
                                $arFornecedores[$inCodChave][$inId]['status']        = ($inCodStatus == 0 ) ? 'classificado' : 'desclassificado';
                                $arFornecedores[$inCodChave][$inId]['ordem']         = 1;
                                $arFornecedores[$inCodChave][$inId]['cod_status']    = $inCodStatus;
                                $arFornecedores[$inCodChave][$inId]['justificativa'] = $stJustificativa;
                        } else {
                            $arFornecedores[$inCodChave][$inId]['justificativa'] = $stJustificativa;
                        }
                    }
                    /* Tratamento para fornecedores desclassificados */
                    else if ($inCodStatus == 1) {

                        # Atualiza o fornecedor com as informações setadas na tela.
                        $arFornecedores[$inCodChave][$inId]['status']        = "desclassificado";
                        $arFornecedores[$inCodChave][$inId]['cod_status']    = $inCodStatus;
                        $arFornecedores[$inCodChave][$inId]['justificativa'] = $stJustificativa;

                        # Faz uma cópia dos dados do fornecedor que será desclassificado para colocá-lo na última posição no array.
                        $arAux = $arFornecedores[$inCodChave][$inId];

                        # Detela o índice do array do forncedor desclassificado.
                        unset($arFornecedores[$inCodChave][$inId]);

                        $arNewFornecedores = array();

                        # Cria outro array ordenando os classificados e sua classificação.
                        foreach ($arFornecedores[$inCodChave] as $chave => $valor) {
                            $key = count($arNewFornecedores);
                            $arNewFornecedores[$key]          = $valor;
                            $arNewFornecedores[$key]['ordem'] = $key+1;
                        }

                        # Atualiza o array do lote com as novas classificações.
                        $arFornecedores[$inCodChave] = $arNewFornecedores;

                        # Coloca a última classificação ao fornecedor desclassificado.
                        $arAux['ordem'] = (count($arFornecedores[$inCodChave])+1);

                        # Adiciona o fornecedor desclassificado na última posição do array.
                        array_push($arFornecedores[$inCodChave], $arAux);
                    }
                }
            }
        }

        if (empty($stJs)) {
            Sessao::write('arFornecedores', $arFornecedores);
            $stJs .= " var oc = $('".$_REQUEST['hdnTableRow']."_menos').getAttribute('onclick'); eval(oc);";

            # Verifica qual o tipo do julgamento que está sendo tratado e chama a função correspondente.
            if (isset($_REQUEST['inCodItem']) && !empty($_REQUEST['inCodItem']))
                $stJs = montaSpanFornecedoresItem($_REQUEST['inCodItem']);
            else
                $stJs .= montaSpanFornecedoresLote($_REQUEST['hdnLote']);
        }
    break;
}

if ($stJs) {
    echo $stJs;
}

?>
