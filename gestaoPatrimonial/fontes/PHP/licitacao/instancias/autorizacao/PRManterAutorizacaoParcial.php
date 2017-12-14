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
    * Página de Processamento de Licitações para Autorização de Empenho Parcial
    * Data de Criação   : 25/09/2015

    * @author Analista: Gelson Wolowski Gonçalves
    * @author Desenvolvedor: Michel Teixeira

    * @ignore

    $Id: PRManterAutorizacaoParcial.php 65614 2016-06-02 13:10:59Z franver $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GP_LIC_MAPEAMENTO."TLicitacaoHomologacao.class.php";
include_once CAM_GP_LIC_MAPEAMENTO."TLicitacaoHomologacaoAnulada.class.php";
include_once CAM_GP_COM_MAPEAMENTO."TComprasSolicitacao.class.php";
include_once CAM_GF_EMP_MAPEAMENTO."TEmpenhoAutorizacaoEmpenho.class.php";
include_once CAM_GF_ORC_MAPEAMENTO."TOrcamentoReservaSaldos.class.php";
include_once CAM_GF_ORC_MAPEAMENTO."TOrcamentoReservaSaldosAnulada.class.php";
include_once CAM_GF_EMP_MAPEAMENTO."TEmpenhoAutorizacaoEmpenhoAssinatura.class.php";
include_once CAM_GF_EMP_NEGOCIO."REmpenhoAutorizacaoEmpenho.class.php";
include_once CAM_GF_ORC_MAPEAMENTO."TOrcamentoDespesa.class.php";
include_once CAM_GP_COM_MAPEAMENTO."TComprasMapaItemReserva.class.php";
include_once CAM_GF_ORC_MAPEAMENTO."TOrcamentoClassificacaoDespesa.class.php";
include_once CAM_GF_ORC_MAPEAMENTO."TOrcamentoDespesa.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ManterAutorizacaoParcial";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";
$pgGera     = CAM_GF_EMP_INSTANCIAS."autorizacao/OCGeraRelatorioAutorizacao.php";

Sessao::setTrataExcecao ( true );

$arItens = Sessao::read('arItens');
$arItens = (is_array($arItens)) ? $arItens : array();

$arAutorizacao = array();
$inCont = 0;

$obErro  = new Erro();
$arErros = array();

$arListaExcluidos = array();
$arListaDespesaFornecedor = array();

foreach ($arItens as $key => $arItem) {
    if(is_array($arItem['arItemDespesa']) && count($arItem['arItemDespesa']) >= 1){
        foreach( $arItem['arItemDespesa'] as $inDespesa => $arItemDespesa) {
            $nuQtdeItemDespesa = str_replace(",",".",str_replace(".","",$arItemDespesa['nuQtdeItem']));
            $stChaveDespesaFornecedor = $inDespesa.'.'.$arItem['stCodClassificacao'].'.'.$arItem['inCgmFornecedor'];

            $arItens[$key]['arItemDespesa'][$inDespesa]['nuQtdeItem'] = $nuQtdeItemDespesa;

            if($nuQtdeItemDespesa > 0){
                if(isset($arListaDespesaFornecedor[$stChaveDespesaFornecedor]['Itens'] ))
                    $arListaDespesaFornecedor[$stChaveDespesaFornecedor]['Itens'] = $arListaDespesaFornecedor[$stChaveDespesaFornecedor]['Itens'].','.$arItem['cod_item'];
                else
                    $arListaDespesaFornecedor[$stChaveDespesaFornecedor]['Itens'] = $arItem['cod_item'];
            }else{
                $arListaExcluidos[$stChaveDespesaFornecedor][] = $arItem['cod_item'];
            }
        }
    }
}

if(count($arListaDespesaFornecedor) == 0){
    $obErro->setDescricao("Informe no mínimo um(01) item para emitir autorização de empenho.");
} else {
    foreach ($arListaDespesaFornecedor as $chaveDespesaFornecedor => $arItensDespesaFornecedor) {
        $obTLicHomologacao = new TLicitacaoHomologacao();
        $obTOrcamentoReservaSaldos = new TOrcamentoReservaSaldos();

        list($inCodDespesa, $inCodConta, $inCgmFornecedor) = explode('.',$chaveDespesaFornecedor);

        $stFiltroHomologacao  = " AND licitacao.cod_licitacao  = ".$request->get('inCodLicitacao')."                \n";
        $stFiltroHomologacao .= " AND licitacao.exercicio      = '".Sessao::getExercicio()."'                       \n";
        $stFiltroHomologacao .= " AND licitacao.cod_entidade   = ".$request->get('inCodEntidade')."                 \n";
        $stFiltroHomologacao .= " AND licitacao.cod_modalidade = ".$request->get('inCodModalidade')."               \n";
        $stFiltroHomologacao .= " AND homologacao.homologado = true                                                 \n";
        if(count($arListaExcluidos[$chaveDespesaFornecedor])>0){
            $stListaExcluidos = implode(",",$arListaExcluidos[$chaveDespesaFornecedor]);
            $stFiltroHomologacao .= " AND solicitacao_item_dotacao.cod_item NOT IN (".$stListaExcluidos.")          \n";
        }

        $stFiltroHomologacao .= " AND (  (    solicitacao_item_dotacao.cod_despesa IS NOT NULL                      \n";
        $stFiltroHomologacao .= "         AND solicitacao_item_dotacao.cod_despesa = ".$inCodDespesa." )            \n";
        $stFiltroHomologacao .= "      OR solicitacao_item_dotacao.cod_despesa IS NULL                              \n";
        $stFiltroHomologacao .= "     )                                                                             \n";
        $stFiltroHomologacao .= " AND (  (    solicitacao_item_dotacao.cod_conta IS NOT NULL                        \n";
        $stFiltroHomologacao .= "         AND solicitacao_item_dotacao.cod_conta = ".$inCodConta." )                \n";
        $stFiltroHomologacao .= "      OR solicitacao_item_dotacao.cod_conta IS NULL                                \n";
        $stFiltroHomologacao .= "     )                                                                             \n";

        $stFiltroHomologacao .= " AND cotacao_fornecedor_item.cgm_fornecedor    = ".$inCgmFornecedor."              \n";

        $stFiltroItens = " AND mapa_item.cod_item                               IN (".$arItensDespesaFornecedor['Itens'].") \n";

        $obTLicHomologacao->recuperaGrupoAutEmpenho( $rsAutEmpenho, $stFiltroHomologacao.$stFiltroItens );

        $obTOrcamentoDespesa = new TOrcamentoDespesa();
        $stFiltroDespesa  =" \n";
        $stFiltroDespesa .= "and OD.cod_despesa = ".$inCodDespesa."          \n";
        $stFiltroDespesa .= "and OD.exercicio = '".Sessao::getExercicio()."' \n";
        $obTOrcamentoDespesa->recuperaRelacionamento($rsDespesa, $stFiltroDespesa);

        $obTOrcamentoClassificacaoDespesa = new TOrcamentoClassificacaoDespesa();
        $stFiltroDespesa  = "and cod_conta = ".$inCodConta."                 \n";
        $stFiltroDespesa .= "and exercicio = '".Sessao::getExercicio()."'    \n";
        $obTOrcamentoClassificacaoDespesa->recuperaRelacionamento($rsClassificacaoDespesa, $stFiltroDespesa);

        // data máxima para a entidade
        $data = $request->get('stDtAutorizacao');
        $ano = substr($data, 6, 4);
        $mes = substr($data, 3, 2);
        $dia = substr($data, 0, 2);
        $dataFormatadaEntidade = $ano.$mes.$dia;
    
        // data licitação
        $data1 = $request->get('inDataLicitacao');
        $ano1 = substr($data1, 6, 4);
        $mes1 = substr($data1, 3, 2);
        $dia1 = substr($data1, 0, 2);
        $dataFormatadaLicitacao = $ano1.$mes1.$dia1;

        $arCountItensAderidos = explode(',',$arItensDespesaFornecedor['Itens']);

        $stNomFornecedor = SistemaLegado::pegaDado("nom_cgm","sw_cgm","where numcgm=".$inCgmFornecedor);

        if($rsAutEmpenho->eof()){
            $obErro->setDescricao("Fornecedor ".$inCgmFornecedor." - ".$stNomFornecedor.", Não possui Cotações Homologadas para os itens(".$arItensDespesaFornecedor['Itens'].").");
        }elseif($rsAutEmpenho->getCampo('qtd_itens_homologados') <> count($arCountItensAderidos)){
            $obErro->setDescricao("Fornecedor ".$inCgmFornecedor." - ".$stNomFornecedor.", Não possui Cotações Homologadas para todos os itens(".$arItensDespesaFornecedor['Itens'].").");
        }

        if ($rsDespesa->eof())
            $obErro->setDescricao("Dotação Orçamentária dos itens(".$arItensDespesaFornecedor['Itens']."), não localizado no exercício de ".Sessao::getExercicio()."!");
        else{
            $inNumOrgao = $rsDespesa->getCampo('num_orgao');
            $inNumUnidade = $rsDespesa->getCampo('num_unidade');
        }

        if ($rsClassificacaoDespesa->eof())
            $obErro->setDescricao("Desdobramento dos itens(".$arItensDespesaFornecedor['Itens']."), não localizado no exercício de ".Sessao::getExercicio()."!");
        else
            $stMascaraClassificacao = $rsClassificacaoDespesa->getCampo('mascara_classificacao');

        if (($dataFormatadaEntidade-$dataFormatadaLicitacao) < 0) {
            $obErro->setDescricao("Data do Processo Licitatório superior à última autorização da entidade ".$request->get('inCodEntidade').".");
        } elseif ($dataFormatadaEntidade - (date("Y").date("m").date("d")) > 0) {
            $obErro->setDescricao("Data da Autorização deve ser menor ou igual a data atual. ");
        }
    
        if (!$obErro->ocorreu()) {
            while (!$rsAutEmpenho->eof() && !$obErro->ocorreu()) {
                // itens
                $stFiltroHomologacao_item  = $stFiltroHomologacao;
                $stFiltroHomologacao_item .= " AND NOT EXISTS
                                                   (
                                                        SELECT  1
                                                          FROM  empenho.item_pre_empenho_julgamento
                                                    INNER JOIN  empenho.item_pre_empenho
                                                            ON  item_pre_empenho.cod_pre_empenho  = item_pre_empenho_julgamento.cod_pre_empenho
                                                           AND  item_pre_empenho.exercicio        = item_pre_empenho_julgamento.exercicio
                                                           AND  item_pre_empenho.num_item         = item_pre_empenho_julgamento.num_item
                                                    INNER JOIN  compras.cotacao_item
                                                            ON  cotacao_item.cod_cotacao   = item_pre_empenho_julgamento.cod_cotacao
                                                           AND  cotacao_item.exercicio     = item_pre_empenho_julgamento.exercicio_julgamento
                                                           AND  cotacao_item.lote          = item_pre_empenho_julgamento.lote
                                                           AND  cotacao_item.cod_item      = item_pre_empenho_julgamento.cod_item
                                                         WHERE  item_pre_empenho_julgamento.exercicio_julgamento = cotacao_fornecedor_item.exercicio
                                                           AND  item_pre_empenho_julgamento.cod_cotacao          = cotacao_fornecedor_item.cod_cotacao
                                                           AND  item_pre_empenho_julgamento.cod_item             = cotacao_fornecedor_item.cod_item
                                                           AND  item_pre_empenho_julgamento.lote                 = cotacao_fornecedor_item.lote
                                                           AND  item_pre_empenho_julgamento.cgm_fornecedor       = cotacao_fornecedor_item.cgm_fornecedor
                                                      GROUP BY  item_pre_empenho_julgamento.exercicio_julgamento
                                                             ,  item_pre_empenho_julgamento.cod_cotacao
                                                             ,  item_pre_empenho_julgamento.cod_item
                                                             ,  item_pre_empenho_julgamento.lote
                                                             ,  item_pre_empenho_julgamento.cgm_fornecedor
                                                             ,  cotacao_item.quantidade
                                                        HAVING  coalesce(cotacao_item.quantidade, 0.00) - coalesce(sum(item_pre_empenho.quantidade), 0.00) = 0
                                                   )            \n";

                $stFiltroHomologacao_item .= " AND NOT EXISTS
                                                   (
                                                        SELECT  1
                                                          FROM  compras.cotacao_anulada
                                                         WHERE  cotacao_anulada.cod_cotacao = mapa_cotacao.cod_cotacao
                                                           AND  cotacao_anulada.exercicio   = mapa_cotacao.exercicio_cotacao
                                                   )            \n";

                $stFiltroHomologacao_item .= $stFiltroItens;

                $stOrdem = " ORDER BY catalogo_item.descricao   \n";

                $obTLicHomologacao->recuperaItensAgrupadosSolicitacaoLicitacao( $rsItensAutEmpenho, $stFiltroHomologacao_item, $stOrdem );

                #Reserva de Saldos por Autorização
                if( $rsItensAutEmpenho->eof() ){
                    $obTLicHomologacao->recuperaItensAgrupadosSolicitacaoLicitacaoMapa($rsSolicitacaoReserva, $stFiltroHomologacao_item, $stOrdem );

                    while (!$rsSolicitacaoReserva->eof() && !$obErro->ocorreu()) {
                        $vlCotacao = $rsSolicitacaoReserva->getCampo('vl_cotacao');

                        foreach ($arItens as $key => $arItem) {
                            if ( $arItem['cod_item'] == $rsSolicitacaoReserva->getCampo('cod_item') && $arItem['cod_cotacao'] == $rsSolicitacaoReserva->getCampo('cod_cotacao') ) {
                                $arCotacaoItem = (is_array($arItem['arCotacaoItem'])) ? $arItem['arCotacaoItem'] : array();
                                foreach ($arCotacaoItem as $chaveCotacao => $cotacaoItem) {
                                    if($cotacaoItem['cgm_fornecedor'] == $arItem['inCgmFornecedor']){
                                        $vlUnitarioItem = $cotacaoItem['vl_cotacao'] / $cotacaoItem['quantidade'];
                                        break;
                                    }
                                }

                                $nuQtdeItem = isset($arItem['arItemDespesa'][$inCodDespesa]) ? $arItem['arItemDespesa'][$inCodDespesa]['nuQtdeItem'] : $arItem['nuQtdeItem'];                               
                                $vlCotacao = $vlUnitarioItem * $nuQtdeItem;
                                break;
                            }
                        }

                        $obTOrcamentoDespesa = new TOrcamentoDespesa;
                        $obTOrcamentoDespesa->setDado( "cod_despesa", $inCodDespesa );
                        $obTOrcamentoDespesa->setDado( "exercicio"  , Sessao::getExercicio() );
                        $obTOrcamentoDespesa->recuperaSaldoDotacao( $rsSaldoDotacao );

                        if(!$rsSaldoDotacao->eof()){
                            if(!isset($arSaldoDotacao[$inCodDespesa])){
                                $arSaldoDotacao[$inCodDespesa]['saldo_inicial'] = $rsSaldoDotacao->getCampo('saldo_dotacao');
                                $arSaldoDotacao[$inCodDespesa]['vl_reserva']    = $vlCotacao;
                            }else
                                $arSaldoDotacao[$inCodDespesa]['vl_reserva']   += $vlCotacao;
                        }

                        # Mensagem do motivo da criação da Reserva de Saldo.
                        $stMsgReserva  = "Entidade: ".$rsSolicitacaoReserva->getCampo('cod_entidade')." - ".ucwords(strtolower($rsSolicitacaoReserva->getCampo('nom_entidade'))).", ";
                        $stMsgReserva .= "Mapa de Compras: ".$rsSolicitacaoReserva->getCampo('cod_mapa')."/".$rsSolicitacaoReserva->getCampo('exercicio_mapa').", ";
                        $stMsgReserva .= "Item: ".$rsSolicitacaoReserva->getCampo('cod_item').", ";
                        $stMsgReserva .= "Centro de Custo: ".$rsSolicitacaoReserva->getCampo('cod_centro')." ";
                        $stMsgReserva .= "(Origem da criação: ".SistemaLegado::pegaDado('nom_acao', 'administracao.acao', 'WHERE cod_acao = '.Sessao::read('acao')).").";

                        # Cria uma nova reserva de saldo que será utilizada agora no Mapa de Compras.
                        $obTOrcamentoReservaSaldos->setDado('exercicio' , $rsSolicitacaoReserva->getCampo('exercicio_mapa'));
                        $obTOrcamentoReservaSaldos->proximoCod($inCodReserva);

                        $obTOrcamentoReservaSaldos->setDado('cod_reserva'         , $inCodReserva);
                        $obTOrcamentoReservaSaldos->setDado('exercicio'           , $rsSolicitacaoReserva->getCampo('exercicio_mapa'));
                        $obTOrcamentoReservaSaldos->setDado('cod_despesa'         , $inCodDespesa);
                        $obTOrcamentoReservaSaldos->setDado('dt_validade_inicial' , date('d/m/Y'));
                        $obTOrcamentoReservaSaldos->setDado('dt_validade_final'   , '31/12/'.Sessao::getExercicio());
                        $obTOrcamentoReservaSaldos->setDado('dt_inclusao'         , date('d/m/Y'));
                        $obTOrcamentoReservaSaldos->setDado('vl_reserva'          , $vlCotacao);
                        $obTOrcamentoReservaSaldos->setDado('tipo'                , 'A');
                        $obTOrcamentoReservaSaldos->setDado('motivo'              , $stMsgReserva);

                        # Inclui na tabela compras.mapa_item_reserva, caso consiga fazer a reserva de saldos.
                        if ($obTOrcamentoReservaSaldos->incluiReservaSaldo() == true) {
                            $obTComprasMapaItemReserva = new TComprasMapaItemReserva;
                            $obTComprasMapaItemReserva->setDado('exercicio_mapa'        , $rsSolicitacaoReserva->getCampo('exercicio_mapa'));
                            $obTComprasMapaItemReserva->setDado('cod_mapa'              , $rsSolicitacaoReserva->getCampo('cod_mapa'));
                            $obTComprasMapaItemReserva->setDado('exercicio_solicitacao' , $rsSolicitacaoReserva->getCampo('exercicio_solicitacao'));
                            $obTComprasMapaItemReserva->setDado('cod_entidade'          , $rsSolicitacaoReserva->getCampo('cod_entidade'));
                            $obTComprasMapaItemReserva->setDado('cod_solicitacao'       , $rsSolicitacaoReserva->getCampo('cod_solicitacao'));
                            $obTComprasMapaItemReserva->setDado('cod_centro'            , $rsSolicitacaoReserva->getCampo('cod_centro'));
                            $obTComprasMapaItemReserva->setDado('cod_item'              , $rsSolicitacaoReserva->getCampo('cod_item'));
                            $obTComprasMapaItemReserva->setDado('lote'                  , $rsSolicitacaoReserva->getCampo('lote'));
                            $obTComprasMapaItemReserva->setDado('exercicio_reserva'     , $rsSolicitacaoReserva->getCampo('exercicio_mapa'));
                            $obTComprasMapaItemReserva->setDado('cod_reserva'           , $inCodReserva);
                            $obTComprasMapaItemReserva->setDado('cod_conta'             , $inCodConta);
                            $obTComprasMapaItemReserva->setDado('cod_despesa'           , $inCodDespesa);

                            $obErro = $obTComprasMapaItemReserva->inclusao( Sessao::getTransacao() );
                        } else{
                            $stSolicitacao = $rsSolicitacaoReserva->getCampo('cod_solicitacao').'/'.$rsSolicitacaoReserva->getCampo('exercicio_solicitacao');
                            $obErro->setDescricao('Não foi possível reservar saldo para o item '.$rsSolicitacaoReserva->getCampo('cod_item').', dotação '.$inCodDespesa.', solicitação '.$stSolicitacao.". Saldo da Dotação: ".number_format($arSaldoDotacao[$inCodDespesa]['saldo_inicial'],2,',','.'));
                            break;
                        }

                        $rsSolicitacaoReserva->proximo();
                    }
                }

                $rsAutEmpenho->proximo();
            }

            $rsAutEmpenho->setPrimeiroElemento();
            while (!$rsAutEmpenho->eof() && !$obErro->ocorreu()) {
                // itens
                $stFiltroHomologacao_item  = $stFiltroHomologacao;
                $stFiltroHomologacao_item .= " AND mapa_item.cod_item IN (".$arItensDespesaFornecedor['Itens'].") \n";
                $stFiltroHomologacao_item .= " AND NOT EXISTS
                                                    (
                                                        SELECT  1
                                                          FROM  empenho.item_pre_empenho_julgamento
                                                    INNER JOIN  empenho.item_pre_empenho
                                                            ON  item_pre_empenho.cod_pre_empenho  = item_pre_empenho_julgamento.cod_pre_empenho
                                                           AND  item_pre_empenho.exercicio        = item_pre_empenho_julgamento.exercicio
                                                           AND  item_pre_empenho.num_item         = item_pre_empenho_julgamento.num_item
                                                    INNER JOIN  compras.cotacao_item
                                                            ON  cotacao_item.cod_cotacao   = item_pre_empenho_julgamento.cod_cotacao
                                                           AND  cotacao_item.exercicio     = item_pre_empenho_julgamento.exercicio_julgamento
                                                           AND  cotacao_item.lote          = item_pre_empenho_julgamento.lote
                                                           AND  cotacao_item.cod_item      = item_pre_empenho_julgamento.cod_item
                                                         WHERE  item_pre_empenho_julgamento.exercicio_julgamento = cotacao_fornecedor_item.exercicio
                                                           AND  item_pre_empenho_julgamento.cod_cotacao          = cotacao_fornecedor_item.cod_cotacao
                                                           AND  item_pre_empenho_julgamento.cod_item             = cotacao_fornecedor_item.cod_item
                                                           AND  item_pre_empenho_julgamento.lote                 = cotacao_fornecedor_item.lote
                                                           AND  item_pre_empenho_julgamento.cgm_fornecedor       = cotacao_fornecedor_item.cgm_fornecedor
                                                      GROUP BY  item_pre_empenho_julgamento.exercicio_julgamento
                                                             ,  item_pre_empenho_julgamento.cod_cotacao
                                                             ,  item_pre_empenho_julgamento.cod_item
                                                             ,  item_pre_empenho_julgamento.lote
                                                             ,  item_pre_empenho_julgamento.cgm_fornecedor
                                                             ,  cotacao_item.quantidade
                                                        HAVING  coalesce(cotacao_item.quantidade, 0.00) - coalesce(sum(item_pre_empenho.quantidade), 0.00) = 0
                                                   )            \n";

                $stFiltroHomologacao_item .= " AND NOT EXISTS
                                                    (
                                                        SELECT 1
                                                          FROM compras.cotacao_anulada
                                                         WHERE cotacao_anulada.cod_cotacao  = mapa_cotacao.cod_cotacao
                                                           AND cotacao_anulada.exercicio    = mapa_cotacao.exercicio_cotacao
                                                    )           \n";

                $stOrdem = " ORDER BY catalogo_item.descricao   \n";
                $obTLicHomologacao->recuperaItensAgrupadosSolicitacaoLicitacao( $rsItensAutEmpenho, $stFiltroHomologacao_item, $stOrdem );

                $obTLicHomologacao = new TLicitacaoHomologacao();
                $obTLicHomologacao->recuperaItensAgrupadosSolicitacaoLicitacaoImp( $rsItensAutEmpenhoImp, $stFiltroHomologacao_item, $stOrdem );

                $arItensAutorizacaoImp = array();
                $arItensAutorizacaoImp[] = $rsItensAutEmpenhoImp->arElementos;

                unset($stFiltroHomologacao_item );

                while ( !$rsItensAutEmpenho->eof() && !$obErro->ocorreu() ) {
                    $obTOrcamentoReservaSaldosAnulada = new TOrcamentoReservaSaldosAnulada;
                    $obTOrcamentoReservaSaldosAnulada->setDado('cod_reserva' , $rsItensAutEmpenho->getCampo('cod_reserva'));
                    $obTOrcamentoReservaSaldosAnulada->setDado('exercicio'   , $rsItensAutEmpenho->getCampo('exercicio_solicitacao'));
                    $obTOrcamentoReservaSaldosAnulada->setDado('dt_anulacao' , date('d/m/Y'));
                    $obErro = $obTOrcamentoReservaSaldosAnulada->consultar();

                    $obExcecao = Sessao::getExcecao();
                    if (Sessao::getExcecao()->getDescricao() == "Nenhum registro encontrado!") {
                        Sessao::getExcecao()->setDescricao("");
                    }

                    if ( !$obTOrcamentoReservaSaldosAnulada->getDado ( 'motivo_anulacao' ) ) {
                        $obTOrcamentoReservaSaldosAnulada->setDado( 'motivo_anulacao' , 'Anulação Automática. Entidade: '.$rsAutEmpenho->getCampo( 'cod_entidade' ).' - '.$rsAutEmpenho->getCampo( 'nom_entidade' ).', Mapa de compras: '. $rsItensAutEmpenho->getCampo( 'cod_mapa' ) . '/'. $rsItensAutEmpenho->getCampo( 'exercicio_mapa' ) . '' );
                        $obErro = $obTOrcamentoReservaSaldosAnulada->inclusao( Sessao::getTransacao());
                    }
                    $rsItensAutEmpenho->proximo();
                }

                $rsAutEmpenho->proximo();
            }

            if (!$obErro->ocorreu()) {
                $stFiltroSolicitacaoLicitacao = $stFiltroHomologacao;
                $stFiltroSolicitacaoLicitacao.= "
                            AND NOT EXISTS
                                (
                                    SELECT  1
                                      FROM  compras.cotacao_anulada
                                     WHERE  cotacao_anulada.cod_cotacao = cotacao.cod_cotacao
                                       AND  cotacao_anulada.exercicio   = cotacao.exercicio
                                )

                            AND NOT EXISTS
                                (
                                    SELECT  1
                                      FROM  compras.solicitacao_anulacao
                                     WHERE  solicitacao_anulacao.cod_solicitacao = solicitacao.cod_solicitacao
                                       AND  solicitacao_anulacao.exercicio   = solicitacao.exercicio
                                       AND  solicitacao_anulacao.cod_entidade   = solicitacao.cod_entidade
                                )

                                  GROUP BY  solicitacao.cod_solicitacao
                                         ,  solicitacao.observacao
                                         ,  solicitacao.exercicio
                                         ,  solicitacao.cod_almoxarifado
                                         ,  solicitacao.cod_entidade
                                         ,  solicitacao.cgm_solicitante
                                         ,  solicitacao.cgm_requisitante
                                         ,  solicitacao.cod_objeto
                                         ,  solicitacao.prazo_entrega
                                         ,  solicitacao.timestamp";

                $obTLicHomologacao->recuperaSolicitacaoLicitacaoNaoAnulada( $rsSolicitacaoLicitacaoAtiva, $stFiltroSolicitacaoLicitacao );

                while (!$rsSolicitacaoLicitacaoAtiva->eof()) {
                    $observacaoSolicitacaoLicitacao .= $rsSolicitacaoLicitacaoAtiva->getCampo('observacao').'§§';
                    $rsSolicitacaoLicitacaoAtiva->proximo();
                }

                Sessao::write('observacaoSolicitacao',$observacaoSolicitacaoLicitacao);

                $inCountAutorizacao=0;
                $arVlReservaDespesa = array();
                foreach ($arItensAutorizacaoImp[$inCountAutorizacao] as $chave =>$dadosItens) {
                    $vlCotacao = $dadosItens['vl_cotacao'];
                    $qtdCotacao = $dadosItens['qtd_cotacao'];
                    foreach ($arItens as $key => $arItem) {
                        if ( $arItem['cod_item'] == $dadosItens['cod_item'] && $arItem['cod_cotacao'] == $dadosItens['cod_cotacao'] ) {
                            $arCotacaoItem = (is_array($arItem['arCotacaoItem'])) ? $arItem['arCotacaoItem'] : array();
                            foreach ($arCotacaoItem as $chaveCotacao => $cotacaoItem) {
                                if($cotacaoItem['cgm_fornecedor'] == $arItem['inCgmFornecedor']){
                                    $vlUnitarioItem = $cotacaoItem['vl_cotacao'] / $cotacaoItem['quantidade'];
                                    break;
                                }
                            }

                            $qtdCotacao = isset($arItem['arItemDespesa'][$inCodDespesa]) ? $arItem['arItemDespesa'][$inCodDespesa]['nuQtdeItem'] : $arItem['nuQtdeItem']; 
                            $vlCotacao = $vlUnitarioItem * $qtdCotacao;

                            break;
                        }
                    }

                    if(isset($arVlReservaDespesa[$inCodDespesa][$dadosItens['cod_conta']])){
                        $arVlReservaDespesa[$inCodDespesa][$inCodConta] += $vlCotacao;
                    }else{
                        $arVlReservaDespesa[$inCodDespesa][$inCodConta]  = $vlCotacao;
                    }

                    $inCountAutorizacao++;
                }

                $inCountAutorizacao = 0;
                $rsAutEmpenho->setPrimeiroElemento();

                while (!$rsAutEmpenho->eof() && !$obErro->ocorreu()) {
                    $obAutorizacaoEmpenho = new REmpenhoAutorizacaoEmpenho;
                    $obAutorizacaoEmpenho->boAutViaHomologacao = TRUE;
                    $obAutorizacaoEmpenho->setExercicio( Sessao::getExercicio() );
                    $obAutorizacaoEmpenho->obROrcamentoEntidade->setCodigoEntidade( $rsAutEmpenho->getCampo('cod_entidade') );
                    $obAutorizacaoEmpenho->obREmpenhoTipoEmpenho->setCodTipo( 0 );
                    $obAutorizacaoEmpenho->obROrcamentoDespesa->setCodDespesa( $inCodDespesa );
                    $obAutorizacaoEmpenho->obROrcamentoClassificacaoDespesa->setMascClassificacao( $stMascaraClassificacao );
                    $obAutorizacaoEmpenho->obRCGM->setNumCGM( $rsAutEmpenho->getCampo("fornecedor") );
                    $obAutorizacaoEmpenho->obRUsuario->obRCGM->setNumCGM( Sessao::read('numCgm') );
                    $obAutorizacaoEmpenho->obREmpenhoHistorico->setCodHistorico( 0 );
                    $obAutorizacaoEmpenho->obROrcamentoReserva->setDtValidadeInicial( $request->get('stDtAutorizacao') );
                    $obAutorizacaoEmpenho->obROrcamentoReserva->setDtValidadeFinal( '31/12/'.date('Y') );
                    $obAutorizacaoEmpenho->obROrcamentoReserva->setDtInclusao( $request->get('stDtAutorizacao') );
                    $obAutorizacaoEmpenho->setDescricao( $rsAutEmpenho->getCampo("cod_objeto")." - ".$rsAutEmpenho->getCampo("desc_objeto") );
                    $obAutorizacaoEmpenho->setDtAutorizacao( $request->get('stDtAutorizacao') );

                    $vlReserva = $arVlReservaDespesa[$inCodDespesa][$inCodConta];
                    $obAutorizacaoEmpenho->obROrcamentoReserva->setVlReserva( $vlReserva );
                    $obAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->setNumeroOrgao( $inNumOrgao );
                    $obAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoUnidadeOrcamentaria->setNumeroUnidade( $inNumUnidade );
                    $obAutorizacaoEmpenho->setCodCategoria ( 1 );

                    // atributo modalidade
                    // array para relação entre modalidade licitacao e atributo modalidade do empenho
                    $arModalidade = array(1 => 2, 2 => 3, 3 => 4, 4 => 0, 5 => 1, 6 => 11, 7 => 12,8 => 5,9 => 6, 10 => 13, 11 => 14);
                    $inAtribModalidade = $arModalidade[$rsAutEmpenho->getCampo("cod_modalidade")];
                    $obAutorizacaoEmpenho->obRCadastroDinamico->addAtributosDinamicos( '101' , $inAtribModalidade );

                    // atributo tipo credor
                    $obAutorizacaoEmpenho->obRCadastroDinamico->addAtributosDinamicos( '103' , 1 );

                    // atributo complementar
                    $obAutorizacaoEmpenho->obRCadastroDinamico->addAtributosDinamicos( '100' , 2 );

                    // atributo numero processo administrativo stNumeroProcesso
                    $obAutorizacaoEmpenho->obRCadastroDinamico->addAtributosDinamicos( '120' , $request->get('stNumeroProcesso') );
                    
                    // atributo exercicio processo administrativo stExercicioProcesso
                    $obAutorizacaoEmpenho->obRCadastroDinamico->addAtributosDinamicos( '121' , $request->get('stExercicioProcesso') );
                    $inNumItemCont = 1;

                    foreach ($arItensAutorizacaoImp[$inCountAutorizacao] as $chave =>$dadosItens) {
                        $vlCotacao = $dadosItens['vl_cotacao'];
                        $qtdCotacao = $dadosItens['qtd_cotacao'];
                        $inFornecedor = $dadosItens['fornecedor'];
                        foreach ($arItens as $key => $arItem) {
                            if ( $arItem['cod_item'] == $dadosItens['cod_item'] && $arItem['cod_cotacao'] == $dadosItens['cod_cotacao'] ) {
                                $arCotacaoItem = (is_array($arItem['arCotacaoItem'])) ? $arItem['arCotacaoItem'] : array();
                                foreach ($arCotacaoItem as $chaveCotacao => $cotacaoItem) {
                                    if($cotacaoItem['cgm_fornecedor'] == $arItem['inCgmFornecedor']){
                                        $vlUnitarioItem = $cotacaoItem['vl_cotacao'] / $cotacaoItem['quantidade'];
                                        break;
                                    }
                                }

                                $qtdCotacao = isset($arItem['arItemDespesa'][$inCodDespesa]) ? $arItem['arItemDespesa'][$inCodDespesa]['nuQtdeItem'] : $arItem['nuQtdeItem']; 
                                $vlCotacao = $vlUnitarioItem * $qtdCotacao;
                                $inFornecedor = $arItem['inCgmFornecedor'];

                                break;
                            }
                        }

                        // gerar autorização
                        $obAutorizacaoEmpenho->addItemPreEmpenho();
                        $obAutorizacaoEmpenho->roUltimoItemPreEmpenho->setCompra ( true );
                        $obAutorizacaoEmpenho->roUltimoItemPreEmpenho->setNumItem                                    ( $inNumItemCont++ );
                        $obAutorizacaoEmpenho->roUltimoItemPreEmpenho->setExercicioMapa                              ( $dadosItens['exercicio_mapa'] );
                        $obAutorizacaoEmpenho->roUltimoItemPreEmpenho->setQuantidade                                 ( $qtdCotacao );
                        $obAutorizacaoEmpenho->roUltimoItemPreEmpenho->setNomUnidade                                 ( $dadosItens['nom_unidade'] );
                        $obAutorizacaoEmpenho->roUltimoItemPreEmpenho->setValorTotal                                 ( $vlCotacao );
                        $obAutorizacaoEmpenho->roUltimoItemPreEmpenho->setNomItem                                    ( $dadosItens['descricao_completa'] );

                        //descricao_completa do item do catalogo concatenada com complemento do item na solicitacao
                        $complemento = "";

                        if (trim($dadosItens['descricao_completa'])) {
                            $complemento .= trim($dadosItens['descricao_completa'])." ";
                        }

                        if (trim($dadosItens['complemento'])) {
                            $complemento .= trim($dadosItens['complemento']);
                        }

                        $obAutorizacaoEmpenho->roUltimoItemPreEmpenho->setComplemento                                ( $complemento );
                        $obAutorizacaoEmpenho->roUltimoItemPreEmpenho->setCgmFornecedor                              ( $inFornecedor );
                        $obAutorizacaoEmpenho->roUltimoItemPreEmpenho->setExercicioJulgamento                        ( $dadosItens['exercicio'] );
                        $obAutorizacaoEmpenho->roUltimoItemPreEmpenho->setLoteCompras                                ( $dadosItens['lote'] );
                        $obAutorizacaoEmpenho->roUltimoItemPreEmpenho->setCodCotacao                                 ( $dadosItens['cod_cotacao'] );
                        $obAutorizacaoEmpenho->roUltimoItemPreEmpenho->setCodItem                                    ( $dadosItens['cod_item'] );
                        $obAutorizacaoEmpenho->roUltimoItemPreEmpenho->setCodItemPreEmp                              ( $dadosItens['cod_item'] );
                        $obAutorizacaoEmpenho->roUltimoItemPreEmpenho->setCodigoMarca                                ( $dadosItens['cod_marca'] );
                        $obAutorizacaoEmpenho->roUltimoItemPreEmpenho->obRUnidadeMedida->setCodUnidade               ( $dadosItens['cod_unidade'] );
                        $obAutorizacaoEmpenho->roUltimoItemPreEmpenho->obRUnidadeMedida->obRGrandeza->setCodGrandeza ( $dadosItens['cod_grandeza'] );
                        $obAutorizacaoEmpenho->roUltimoItemPreEmpenho->setSiglaUnidade                               ( $dadosItens['simbolo'] );
                    }

                    $obAutorizacaoEmpenho->setCodEntidade($request->get('inCodEntidade'));
                    $obAutorizacaoEmpenho->setTipoEmissao('R');
                    $obErro = $obAutorizacaoEmpenho->incluir(Sessao::getTransacao());

                    if ($obErro->ocorreu()) {
                        $arErros[] = $dadosItens['cod_item'].': '.$obErro->getDescricao();
                        break;
                    } else {
                        # Salvar Assinaturas configuráveis se houverem
                        $arAssinaturas = Sessao::read('assinaturas');

                        if (is_array($arAssinaturas) && count($arAssinaturas['selecionadas']) > 0) {
                            $arAssinatura = $arAssinaturas['selecionadas'];

                            $obTEmpenhoAutorizacaoEmpenhoAssinatura = new TEmpenhoAutorizacaoEmpenhoAssinatura;
                            $obTEmpenhoAutorizacaoEmpenhoAssinatura->setDado('exercicio'       , $obAutorizacaoEmpenho->getExercicio());
                            $obTEmpenhoAutorizacaoEmpenhoAssinatura->setDado('cod_entidade'    , $obAutorizacaoEmpenho->obROrcamentoEntidade->getCodigoEntidade());
                            $obTEmpenhoAutorizacaoEmpenhoAssinatura->setDado('cod_autorizacao' , $obAutorizacaoEmpenho->getCodAutorizacao());
                            $arPapel = $obTEmpenhoAutorizacaoEmpenhoAssinatura->arrayPapel();

                            foreach ($arAssinatura as $arAssina) {
                                if (isset($arAssina['papel'])) {
                                    if (is_numeric($arAssina['papel'])) {
                                        $inNumAssina = $arAssina['papel'];
                                    } else {
                                        $inNumAssina = $arPapel[$arAssina['papel']];
                                    }
                                }

                                $obTEmpenhoAutorizacaoEmpenhoAssinatura->setDado('num_assinatura', $inNumAssina);
                                $obTEmpenhoAutorizacaoEmpenhoAssinatura->setDado('numcgm'        , $arAssina['inCGM']);
                                $obTEmpenhoAutorizacaoEmpenhoAssinatura->setDado('cargo'         , $arAssina['stCargo']);
                                $obErro = $obTEmpenhoAutorizacaoEmpenhoAssinatura->inclusao(Sessao::getTransacao());
                            }

                            unset($obTEmpenhoAutorizacaoEmpenhoAssinatura);
                        }

                        # Armazena os dados da autorização em array para depois ser usado na impressão.
                        $arAutorizacao[$inCont++] = array(
                                                        "inCodAutorizacao"      => $obAutorizacaoEmpenho->getCodAutorizacao(),
                                                        "inCodPreEmpenho"       => $obAutorizacaoEmpenho->getCodPreEmpenho(),
                                                        "inCodEntidade"         => $obAutorizacaoEmpenho->obROrcamentoEntidade->getCodigoEntidade(),
                                                        "stDtAutorizacao"       => $obAutorizacaoEmpenho->getDtAutorizacao(),
                                                        "inCodDespesa"          => $obAutorizacaoEmpenho->obROrcamentoDespesa->getCodDespesa(),
                                                        "stExercicio"           => $obAutorizacaoEmpenho->getExercicio());
                    }
                    $inCountAutorizacao++;
                    $rsAutEmpenho->proximo();
                }
            }
        }

        if ($obErro->ocorreu())
            break;
    }
}

if (!$obErro->ocorreu()) {
   if (count($arAutorizacao) > 0) {
        if (count($arAutorizacao) == 1) {
            $stMsg = $arAutorizacao[0]['inCodAutorizacao']. "/".Sessao::getExercicio() ;
        } else {
            $inCont = count($arAutorizacao)-1;
            $stMsg = "Autorizações de ".$arAutorizacao[0]['inCodAutorizacao']."/".Sessao::getExercicio()." até ".$arAutorizacao[$inCont]['inCodAutorizacao']. "/".Sessao::getExercicio();
        }

        if (count($arErros) > 0) {
            $obErro->setDescricao("Nem todas as autorizações foram realizadas.");
        }

        if (!$obErro->ocorreu()) {
            # Grava no array as autorizações geradas.
            Sessao::write('arAutorizacao', $arAutorizacao);

            # Exibe a mensagem e redireciona para a tela de download.
            SistemaLegado::alertaAviso($pgGera.'?'.Sessao::getId(), $stMsg , "incluir", "aviso", Sessao::getId(), "../");
        }
    } else {
        $obErro->setDescricao($arErros[0]);
    } 
}

if ($obErro->ocorreu()) {
    SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),'n_incluir',"erro");
}

echo "<script>LiberaFrames(true,true);</script>";
Sessao::encerraExcecao();
?>
