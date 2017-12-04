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
 * Página de processamento do Mapa de Compras
 * Data de Criação: 02/10/2006

 * @author Analista: Cleisson Barbosa
 * @author Desenvolvedor: Anderson C. Konze

 * @ignore

 * Casos de uso: uc-03.04.05

 $Id: PRManterMapaCompras.php 63738 2015-10-02 17:54:55Z michel $

 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

include_once CAM_GP_COM_MAPEAMENTO."TComprasCompraDireta.class.php";
include_once CAM_GP_COM_MAPEAMENTO.'TComprasMapa.class.php';
include_once CAM_GP_COM_MAPEAMENTO.'TComprasMapaSolicitacao.class.php';
include_once CAM_GP_COM_MAPEAMENTO.'TComprasMapaItem.class.php';
include_once CAM_GP_COM_MAPEAMENTO.'TComprasMapaItemDotacao.class.php';
include_once CAM_GP_COM_MAPEAMENTO.'TComprasMapaItemAnulacao.class.php';
include_once CAM_GP_COM_MAPEAMENTO.'TComprasMapaItemReserva.class.php';
include_once CAM_GP_COM_MAPEAMENTO.'TComprasMapaSolicitacaoAnulacao.class.php';
include_once CAM_GP_COM_MAPEAMENTO.'TComprasSolicitacao.class.php';
include_once CAM_GP_COM_MAPEAMENTO.'TComprasSolicitacaoItemDotacao.class.php';
include_once CAM_GP_COM_MAPEAMENTO.'TComprasSolicitacaoHomologadaReserva.class.php';
include_once CAM_GP_LIC_MAPEAMENTO."TLicitacaoLicitacao.class.php";
include_once CAM_GF_ORC_MAPEAMENTO.'TOrcamentoReservaSaldos.class.php';
include_once CAM_GF_ORC_MAPEAMENTO.'TOrcamentoReservaSaldosAnulada.class.php';

$link = Sessao::read('link');
$link = (is_array($link)) ? $link : array();
$stLink = "&pg=".$link['pg']."&pos=".$link['pos'];

$stPrograma = "ManterMapaCompras";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";
$pgGera = "OCGera".$stPrograma.".php";

$stAcao = $request->get('stAcao');

# Função para incluir o item, dotação e reserva.
function incluiItemMapa(&$obTComprasMapaSolicitacao, $inChave, $registro_item , $inCodTipoLicitacao = 0)
{
    $arChave = explode('-',$inChave);
    $inCodItem        = $arChave[0];
    $inCodCentroCusto = $arChave[1];

    # Inclusão do item na compras.mapa_item
    $obTComprasMapaItem = new TComprasMapaItem;
    $obTComprasMapaItem->obTComprasMapaSolicitacao = $obTComprasMapaSolicitacao;
    $obTComprasMapaItem->setDado('cod_centro'			 , $inCodCentroCusto);
    $obTComprasMapaItem->setDado('cod_item'				 , $inCodItem);
    $obTComprasMapaItem->setDado('cod_entidade'			 , $obTComprasMapaSolicitacao->getDado('cod_entidade'));
    $obTComprasMapaItem->setDado('cod_solicitacao'		 , $obTComprasMapaSolicitacao->getDado('cod_solicitacao'));
    $obTComprasMapaItem->setDado('exercicio_solicitacao' , str_replace("'", "", $obTComprasMapaSolicitacao->getDado('exercicio_solicitacao')));
    $obTComprasMapaItem->setDado('quantidade'			 , $registro_item['quantidade_mapa']);
    $obTComprasMapaItem->setDado('vl_total'				 , $registro_item['valor_total_mapa']);
    $obTComprasMapaItem->setDado('cod_mapa'				 , $obTComprasMapaSolicitacao->getDado('cod_mapa'));
    $obTComprasMapaItem->setDado('exercicio'			 , str_replace("'", "", $obTComprasMapaSolicitacao->getDado('exercicio')));

    if ($inCodTipoLicitacao == 2) {
        # Licitação será feita por lote
        if (!$registro_item['lote']) {
            $obTComprasMapaItem->setDado('lote', 1);
        } else {
            $obTComprasMapaItem->setDado('lote', $registro_item['lote']);
        }
    } else {
        $obTComprasMapaItem->setDado( 'lote', 0 );
    }

    # Efetiva a inclusão do Item no Mapa de Compras.
    $obTComprasMapaItem->inclusao();
}

# Função que exclui o item do Mapa.
function excluirItemMapa($arItemExcluido, $inCodMapa, $stExercicioMapa)
{
    $obTComprasMapaSolicitacao		  = new TComprasMapaSolicitacao;
    $obTComprasMapaItem				  = new TComprasMapaItem;
    $obTComprasMapaItemAnulacao		  = new TComprasMapaItemAnulacao;
    $obTComprasMapaItemDotacao		  = new TComprasMapaItemDotacao;
    $obTComprasMapaItemReserva		  = new TComprasMapaItemReserva;
    $obTOrcamentoReservaSaldos        = new TOrcamentoReservaSaldos;
    $obTOrcamentoReservaSaldosAnulada = new TOrcamentoReservaSaldosAnulada;

    foreach ($arItemExcluido as $registro) {
        # Anula as reservas de saldos do mapa e restaurando a reserva da solicitação;
        $obTComprasMapaItemReserva->setDado('cod_mapa'              , $inCodMapa);
        $obTComprasMapaItemReserva->setDado('exercicio_mapa'        , $stExercicioMapa);
        $obTComprasMapaItemReserva->setDado('cod_entidade'          , $registro['cod_entidade']);
        $obTComprasMapaItemReserva->setDado('cod_solicitacao'       , $registro['cod_solicitacao']);
        $obTComprasMapaItemReserva->setDado('exercicio_solicitacao' , $registro['exercicio_solicitacao']);
        $obTComprasMapaItemReserva->setDado('cod_item'              , $registro['cod_item']);
        $obTComprasMapaItemReserva->setDado('cod_centro'            , $registro['cod_centro']);
        $obTComprasMapaItemReserva->setDado('lote'				    , $registro['lote']);
        $obTComprasMapaItemReserva->setDado('cod_despesa'           , $registro['cod_despesa']);
        $obTComprasMapaItemReserva->setDado('cod_conta'             , $registro['cod_conta']);
        $obTComprasMapaItemReserva->recuperaPorChave($rsMapaItemReserva);

        //Se a Reserva de Saldo foi realizada por Mapa de Compras, Faz Anulação da Reserva.
        if (is_numeric($registro['cod_reserva']) && $rsMapaItemReserva->getNumLinhas() > 0) {
            # Dados para montar as mensagens de anulação de reserva de saldo.
            $inNumCgm      = SistemaLegado::pegaDado('numcgm','orcamento.entidade', "where cod_entidade = ".$registro['cod_entidade']." and exercicio = '".Sessao::getExercicio()."'");
            $stNomEntidade = SistemaLegado::pegaDado('nom_cgm','sw_cgm', "where numcgm =".$inNumCgm);
            $stEntidade    = $registro['cod_entidade']." - ".ucwords(strtolower($stNomEntidade));
            $stAcaoOrigem  = SistemaLegado::pegaDado('nom_acao', 'administracao.acao', 'WHERE cod_acao = '.Sessao::read('acao'));
            # Fim dados mensagem.

            $stMsgReservaAnulada  = "Entidade: ".$stEntidade.", ";
            $stMsgReservaAnulada .= "Mapa de Compras: ".$inCodMapa."/".$stExercicioMapa.", ";
            $stMsgReservaAnulada .= "Item: ".$registro['cod_item'].", ";
            $stMsgReservaAnulada .= "Centro de Custo: ".$registro['cod_centro']." ";
            $stMsgReservaAnulada .= "(Origem da anulação: ".$stAcaoOrigem.").";

            # Anulando a Reserva de Saldo criada pelo Mapa de Compras
            $obTOrcamentoReservaSaldosAnulada = new TOrcamentoReservaSaldosAnulada;
            $obTOrcamentoReservaSaldosAnulada->setDado('cod_reserva'     , $registro['cod_reserva']);
            $obTOrcamentoReservaSaldosAnulada->setDado('exercicio'       , $registro['exercicio_reserva']);
            $obTOrcamentoReservaSaldosAnulada->setDado('dt_anulacao'     , date('d/m/Y'));
            $obTOrcamentoReservaSaldosAnulada->setDado('motivo_anulacao' , $stMsgReservaAnulada);
            $obTOrcamentoReservaSaldosAnulada->inclusao();
        }

        # Verifica se existe Reserva de Saldo criada pela Solicitação.
        if (is_numeric($registro['cod_reserva_solicitacao'])) {
            if (is_numeric($registro['cod_reserva'])) {
                # Verifica se a reserva da Solicitação está anulada.
                $obTOrcamentoReservaSaldosAnulada = new TOrcamentoReservaSaldosAnulada;
                $obTOrcamentoReservaSaldosAnulada->setDado('cod_reserva' , $registro['cod_reserva_solicitacao']);
                $obTOrcamentoReservaSaldosAnulada->setDado('exercicio'   , $registro['exercicio_reserva_solicitacao']);
                $obTOrcamentoReservaSaldosAnulada->recuperaPorChave($rsReservaSaldosAnulada);

                # Caso exista uma anulação para a reserva da Solicitação e tiver saldo, exclui a anulação e atualiza o valor da reserva da Solicitação.
                if ($rsReservaSaldosAnulada->getNumLinhas() > 0) {
                    $obTOrcamentoReservaSaldosAnulada->exclusao();

                    # A anulação da reserva de saldo é excluída e a reserva da Solicitação volta a ter o valor da reserva do Mapa excluida.
                    $nuVlReservaSaldoSolicitacao = $registro['vl_reserva'];
                } else {
                    $obTOrcamentoReservaSaldos = new TOrcamentoReservaSaldos;
                    $obTOrcamentoReservaSaldos->setDado('cod_reserva' , $registro['cod_reserva_solicitacao']);
                    $obTOrcamentoReservaSaldos->setDado('exercicio'   , $registro['exercicio_reserva_solicitacao']);
                    $obTOrcamentoReservaSaldos->recuperaPorChave($rsReservasSaldosSolicitacao);

                    # Atualiza o valor da reserva da solicitação
                    $nuVlReservaSaldoSolicitacao = $rsReservasSaldosSolicitacao->getCampo('vl_reserva') + $registro['vl_reserva'];
                }

                $obTOrcamentoReservaSaldos = new TOrcamentoReservaSaldos;
                $obTOrcamentoReservaSaldos->setDado('cod_reserva' , $registro['cod_reserva_solicitacao']);
                $obTOrcamentoReservaSaldos->setDado('exercicio'   , $registro['exercicio_reserva_solicitacao']);
                $obTOrcamentoReservaSaldos->setDado('vl_reserva'  , $nuVlReservaSaldoSolicitacao);

                if (!$obTOrcamentoReservaSaldos->alteraReservaSaldo()) {
                    trataErroTransacao('Não foi possível alterar a reserva da Solicitação de Compras para o item '.$registro['cod_item']);
                }
            }
        }

        # Excluindo  da tabela mapa_item_reserva os itens das solicitações excluidas do mapa
        $obTComprasMapaItemReserva->exclusao();

        # Excluindo  da tabela mapa_item os itens das solicitações excluidas do mapa
        $obTComprasMapaItem->setDado('exercicio'             , $stExercicioMapa);
        $obTComprasMapaItem->setDado('cod_entidade'          , $registro['cod_entidade']);
        $obTComprasMapaItem->setDado('cod_solicitacao'       , $registro['cod_solicitacao']);
        $obTComprasMapaItem->setDado('cod_mapa'              , $inCodMapa);
        $obTComprasMapaItem->setDado('exercicio_solicitacao' , $registro['exercicio_solicitacao']);
        $obTComprasMapaItem->setDado('cod_item'              , $registro['cod_item']);
        $obTComprasMapaItem->setDado('cod_centro'            , $registro['cod_centro']);
        $obTComprasMapaItem->setDado('lote'                  , $registro['lote']);

        # Excluindo da tabela mapa_item_dotacao os itens das solicitações excluidas do mapa
        $obTComprasMapaItemDotacao->setDado('exercicio'             , $stExercicioMapa);
        $obTComprasMapaItemDotacao->setDado('cod_entidade'          , $registro['cod_entidade']);
        $obTComprasMapaItemDotacao->setDado('cod_solicitacao'       , $registro['cod_solicitacao']);
        $obTComprasMapaItemDotacao->setDado('cod_mapa'              , $inCodMapa);
        $obTComprasMapaItemDotacao->setDado('exercicio_solicitacao' , $registro['exercicio_solicitacao']);
        $obTComprasMapaItemDotacao->setDado('cod_item'              , $registro['cod_item']);
        $obTComprasMapaItemDotacao->setDado('cod_centro'            , $registro['cod_centro']);
        $obTComprasMapaItemDotacao->setDado('lote'                  , $registro['lote']);
        $obTComprasMapaItemDotacao->setDado('cod_despesa'           , $registro['cod_despesa']);
        $obTComprasMapaItemDotacao->setDado('cod_conta'             , $registro['cod_conta']);
        $obTComprasMapaItemDotacao->exclusao();

        # Caso exista alguma anulação anterior, exclui da base.
        $obTComprasMapaItemAnulacao->setDado('exercicio'             , $obTComprasMapaItem->getDado('exercicio'));
        $obTComprasMapaItemAnulacao->setDado('cod_entidade'          , $obTComprasMapaItem->getDado('cod_entidade'));
        $obTComprasMapaItemAnulacao->setDado('cod_solicitacao'       , $obTComprasMapaItem->getDado('cod_solicitacao'));
        $obTComprasMapaItemAnulacao->setDado('cod_mapa'              , $obTComprasMapaItem->getDado('cod_mapa'));
        $obTComprasMapaItemAnulacao->setDado('cod_centro'            , $obTComprasMapaItem->getDado('cod_centro'));
        $obTComprasMapaItemAnulacao->setDado('cod_item'              , $obTComprasMapaItem->getDado('cod_item'));
        $obTComprasMapaItemAnulacao->setDado('exercicio_solicitacao' , $obTComprasMapaItem->getDado('exercicio_solicitacao'));
        $obTComprasMapaItemAnulacao->setDado('lote'                  , $obTComprasMapaItem->getDado('lote'));
        $obTComprasMapaItemAnulacao->setDado('cod_despesa'           , $registro['cod_despesa']);
        $obTComprasMapaItemAnulacao->setDado('cod_conta'             , $registro['cod_conta']);
        $obTComprasMapaItemAnulacao->exclusao();

        $stFiltroDotacao  = " WHERE  exercicio             = '".$stExercicioMapa."'";
        $stFiltroDotacao .= "   AND  cod_entidade          = ".$registro['cod_entidade'];
        $stFiltroDotacao .= "   AND  cod_solicitacao       = ".$registro['cod_solicitacao'];
        $stFiltroDotacao .= "   AND  cod_mapa              = ".$inCodMapa;
        $stFiltroDotacao .= "   AND  exercicio_solicitacao = '".$registro['exercicio_solicitacao']."'";
        $stFiltroDotacao .= "   AND  cod_item              = ".$registro['cod_item'];
        $stFiltroDotacao .= "   AND  cod_centro            = ".$registro['cod_centro'];
        $stFiltroDotacao .= "   AND  lote                  = ".$registro['lote'];

        $obTComprasMapaItemDotacao->recuperaTodos($rsItensDotacao, $stFiltroDotacao);

        if ($rsItensDotacao->getNumLinhas() > 0) {
           $obTComprasMapaItem->consultar();

           $quantidadeAtualizada = $obTComprasMapaItem->getDado('quantidade') - $registro['quantidade'];
           $valorAtualizado      = $obTComprasMapaItem->getDado('vl_total') - $registro['vl_total'];
           $obTComprasMapaItem->setDado('quantidade', number_format($quantidadeAtualizada,'4','.',','));
           $obTComprasMapaItem->setDado('vl_total', number_format($valorAtualizado,'2','.',','));
           $obTComprasMapaItem->alteracao();
        } else {
           $obTComprasMapaItem->exclusao();
        }
    }
}

# Função para excluir a Solicitação de Compras do Mapa e todos seus respectivos itens.
function excluirSolicitacaoMapa($arSolicitacaoExcluida, $inCodMapa)
{
    $obTComprasMapaItem                = new TComprasMapaItem;
    $obTComprasMapaItemAnulacao		   = new TComprasMapaItemAnulacao;
    $obTComprasMapaItemDotacao         = new TComprasMapaItemDotacao;
    $obTComprasMapaItemReserva         = new TComprasMapaItemReserva;
    $obTComprasMapaSolicitacao         = new TComprasMapaSolicitacao;
    $obTComprasMapaSolicitacaoAnulacao = new TComprasMapaSolicitacaoAnulacao;

    foreach ($arSolicitacaoExcluida as $registro) {

        # Excluindo da tabela mapa_item os itens das solicitações excluidas do mapa
        $obTComprasMapaItem->setDado('exercicio'             , $registro['exercicio']);
        $obTComprasMapaItem->setDado('cod_entidade'          , $registro['cod_entidade']);
        $obTComprasMapaItem->setDado('cod_solicitacao'       , $registro['cod_solicitacao']);
        $obTComprasMapaItem->setDado('cod_mapa'              , $inCodMapa);
        $obTComprasMapaItem->setDado('exercicio_solicitacao' , $registro['exercicio_solicitacao']);

        # Exclui todas anulações dos itens da solicitação excluída.
        $obTComprasMapaItemAnulacao->setDado('exercicio'             , $obTComprasMapaItem->getDado('exercicio'));
        $obTComprasMapaItemAnulacao->setDado('cod_entidade'          , $obTComprasMapaItem->getDado('cod_entidade'));
        $obTComprasMapaItemAnulacao->setDado('cod_solicitacao'       , $obTComprasMapaItem->getDado('cod_solicitacao'));
        $obTComprasMapaItemAnulacao->setDado('cod_mapa'              , $obTComprasMapaItem->getDado('cod_mapa'));
        $obTComprasMapaItemAnulacao->setDado('exercicio_solicitacao' , $obTComprasMapaItem->getDado('exercicio_solicitacao'));
        $obTComprasMapaItemAnulacao->exclusao();

        # Exclui todas as reservas dos itens da solicitação excluída.
        $obTComprasMapaItemReserva->setDado('exercicio_mapa'        , $obTComprasMapaItem->getDado('exercicio'));
        $obTComprasMapaItemReserva->setDado('exercicio_solicitacao' , $registro['exercicio_solicitacao']);
        $obTComprasMapaItemReserva->setDado('cod_mapa'              , $inCodMapa);
        $obTComprasMapaItemReserva->setDado('cod_entidade'          , $registro['cod_entidade']);
        $obTComprasMapaItemReserva->setDado('cod_solicitacao'       , $registro['cod_solicitacao']);
        $obTComprasMapaItemReserva->exclusao();

        # Exclui todas as dotações dos itens da solicitação excluída.
        $obTComprasMapaItemDotacao->setDado('exercicio'             , $registro['exercicio']);
        $obTComprasMapaItemDotacao->setDado('cod_entidade'          , $registro['cod_entidade']);
        $obTComprasMapaItemDotacao->setDado('cod_solicitacao'       , $registro['cod_solicitacao']);
        $obTComprasMapaItemDotacao->setDado('cod_mapa'              , $inCodMapa);
        $obTComprasMapaItemDotacao->setDado('exercicio_solicitacao' , $registro['exercicio_solicitacao']);
        $obTComprasMapaItemDotacao->exclusao();

        # Exclusão de todos os itens da solicitação excluída.
        $obTComprasMapaItem->exclusao();

        # Exclusão de anulação da solicitação.
        $obTComprasMapaSolicitacaoAnulacao->setDado('cod_mapa'              , $inCodMapa);
        $obTComprasMapaSolicitacaoAnulacao->setDado('exercicio'             , $obTComprasMapaItem->getDado('exercicio'));
        $obTComprasMapaSolicitacaoAnulacao->setDado('cod_solicitacao'       , $registro['cod_solicitacao']);
        $obTComprasMapaSolicitacaoAnulacao->setDado('cod_entidade'          , $registro['cod_entidade']);
        $obTComprasMapaSolicitacaoAnulacao->setDado('exercicio_solicitacao' , $registro['exercicio_solicitacao']);
        $obTComprasMapaSolicitacaoAnulacao->exclusao();

        # Exclusão do vinculo de Solicitação com o Mapa.
        $obTComprasMapaSolicitacao->setDado('exercicio'             , $registro['exercicio']);
        $obTComprasMapaSolicitacao->setDado('cod_entidade'          , $registro['cod_entidade']);
        $obTComprasMapaSolicitacao->setDado('cod_solicitacao'       , $registro['cod_solicitacao']);
        $obTComprasMapaSolicitacao->setDado('cod_mapa'              , $inCodMapa);
        $obTComprasMapaSolicitacao->setDado('exercicio_solicitacao' , $registro['exercicio_solicitacao']);
        $obTComprasMapaSolicitacao->exclusao();
    }

}

#Trata erro na Transação(rollback)
function trataErroTransacao($stDescricao = '')
{
    SistemaLegado::LiberaFrames(true,true);
    Sessao::getExcecao()->setDescricao($stDescricao);
    Sessao::getExcecao()->tratarErro();
}

# Controles de ações.

# Inclusão do Mapa de Compras.
if ($stAcao == 'incluir') {
    $arSolicitacao = Sessao::read('solicitacoes');

    $boRegistroPreco = $request->get('boRegistroPreco');

    if (count($arSolicitacao) == 0) {
        SistemaLegado::LiberaFrames(true,true);
        SistemaLegado::exibeAviso('É preciso incluir ao menos uma solicitação para salvar o mapa.', "n_incluir", "erro");
    } elseif (is_array($arSolicitacao) && count($arSolicitacao) > 0) {

        Sessao::setTrataExcecao(true);

        $rsMinTimestamp = new RecordSet;

        # Filtro para buscar o menor timestamp das solicitações vinculadas ao Mapa.
        if (is_array(Sessao::read('solicitacoes'))) {
            $stFiltro .= " AND ( \n";
            foreach (Sessao::read('solicitacoes') as $registro) {
                $stFiltro .= " (       	 cod_solicitacao = ".$registro['cod_solicitacao']." 	  \n";
                $stFiltro .= "		AND  cod_entidade    = ".$registro['cod_entidade']." 		  \n";
                $stFiltro .= "		AND  exercicio       = '".$registro['exercicio_solicitacao']."' \n";
                $stFiltro .= " ) OR";
            }

            # Retira o último OR do Filtro, desnecessário.
            $stFiltro = substr($stFiltro, 0, strlen($stFiltro)-2);

            $stFiltro .= "     ) ";

            $obTComprasSolicitacao = new TComprasSolicitacao;
            $obTComprasSolicitacao->recuperaMinTimestamp($rsMinTimestamp, $stFiltro);
        }

        # Inclusão do Mapa de Compras
        $obTComprasMapa = new TComprasMapa;
        $obTComprasMapa->setDado('exercicio'          , Sessao::getExercicio());
        $obTComprasMapa->setDado('cod_objeto'         , $request->get('stObjeto'));
        $obTComprasMapa->setDado('cod_tipo_licitacao' , $request->get('inCodTipoLicitacao'));
        $obTComprasMapa->setDado('timestamp'          , $rsMinTimestamp->getCampo('timestamp'));
        $obTComprasMapa->inclusao();

        # Recupera a chave recém inclusa no mapa de compras.
        $inCodMapa       = $obTComprasMapa->getDado('cod_mapa');
        $stExercicioMapa = $obTComprasMapa->getDado('exercicio');

        $inCodTipoLicitacao = $request->get('inCodTipoLicitacao');

        # Percorre o array de Solicitaçao.
        foreach ($arSolicitacao as $registro) {
            # Inclui o vínculo de Solicitação com Mapa de Compras.
            $obTComprasMapaSolicitacao  = new TComprasMapaSolicitacao;
            $obTComprasMapaSolicitacao->setDado('exercicio'             , $stExercicioMapa);
            $obTComprasMapaSolicitacao->setDado('cod_mapa'              , $inCodMapa);
            $obTComprasMapaSolicitacao->setDado('exercicio_solicitacao' , $registro['exercicio_solicitacao']);
            $obTComprasMapaSolicitacao->setDado('cod_entidade'          , $registro['cod_entidade']);
            $obTComprasMapaSolicitacao->setDado('cod_solicitacao'       , $registro['cod_solicitacao']);
            $obTComprasMapaSolicitacao->setDado('timestamp'             , date('Y-m-d H:m:s.ms'));
            $obTComprasMapaSolicitacao->inclusao();

            # Recupera os itens da solicitação.
            $arValores = Sessao::read('itens');

            $arValoresAux = $arValores;
            $arAgrupadoValores = array();

            if (is_array($arValores)) {
                # Laço que agrupa itens por id e centro de custo para somar o
                # o total de quantidade e valor.
                foreach ($arValores as $inChave => $arDados) {
                    $stChave = $arDados['cod_item'].'-'.$arDados['cod_centro'].'-'.$arDados['inId_solicitacao'];
                    if (!isset($arAgrupadoValores[$stChave])) {
                        $arAgrupadoValores[$stChave]['valor_total_mapa']       = $arDados['valor_total_mapa'];
                        $arAgrupadoValores[$stChave]['quantidade_mapa']        = $arDados['quantidade_mapa'];
                        $arAgrupadoValores[$stChave]['inId']                   = $arDados['inId'];
                        $arAgrupadoValores[$stChave]['inId_solicitacao']       = $arDados['inId_solicitacao'];
                        $arAgrupadoValores[$stChave]['cod_item']               = $arDados['cod_item'];
                        $arAgrupadoValores[$stChave]['item']                   = $arDados['item'];
                        $arAgrupadoValores[$stChave]['complemento']            = $arDados['complemento'];
                        $arAgrupadoValores[$stChave]['nom_unidade']            = $arDados['nom_unidade'];
                        $arAgrupadoValores[$stChave]['cod_entidade']           = $arDados['cod_entidade'];
                        $arAgrupadoValores[$stChave]['cod_solicitacao']        = $arDados['cod_solicitacao'];
                        $arAgrupadoValores[$stChave]['centro_custo']           = $arDados['centro_custo'];
                        $arAgrupadoValores[$stChave]['cod_centro']             = $arDados['cod_centro'];
                        $arAgrupadoValores[$stChave]['exercicio_solicitacao']  = $arDados['exercicio_solicitacao'];
                        $arAgrupadoValores[$stChave]['lote']                   = $arDados['lote'];
                        $arAgrupadoValores[$stChave]['cod_despesa']            = $arDados['cod_despesa'];
                        $arAgrupadoValores[$stChave]['vl_reserva']             = $arDados['vl_reserva'];
                        $arAgrupadoValores[$stChave]['vl_reserva_homologacao'] = $arDados['vl_reserva_homologacao'];
                        $arAgrupadoValores[$stChave]['cod_reserva']            = $arDados['cod_reserva'];
                        $arAgrupadoValores[$stChave]['exercicio_reserva']      = $arDados['exercicio_reserva'];
                        $arAgrupadoValores[$stChave]['boReserva']              = $arDados['boReserva'];
                        $arAgrupadoValores[$stChave]['incluir']                = $arDados['incluir'];
                        foreach ($arValoresAux as $inChaveAux => $arDadosAux) {
                            if (($arDados['inId'] != $arDadosAux['inId'])
                                && ($arDados['cod_item'] == $arDadosAux['cod_item']
                                && $arDados['cod_centro'] == $arDadosAux['cod_centro']
                                && $arDados['cod_solicitacao'] == $arDadosAux['cod_solicitacao'])) {

                                $stChave = $arDados['cod_item'].'-'.$arDados['cod_centro'].'-'.$arDados['inId_solicitacao'];
                                $arAgrupadoValores[$stChave]['valor_total_mapa'] += $arDadosAux['valor_total_mapa'];
                                $arAgrupadoValores[$stChave]['quantidade_mapa'] += $arDadosAux['quantidade_mapa'];

                                unset($arValoresAux[$inChaveAux]);
                            }
                        }
                    }
                }
            }

            # Inclui os dados do item da Solicitação no Mapa de Compras.
            foreach ($arAgrupadoValores as $inChave => $registro_item) {
                $arChave = explode('-',$inChave );
                $inCodItem        = $arChave[0];
                $inCodCentroCusto = $arChave[1];
                $inIdSolicitacao  = $arChave[2];

                if ($inIdSolicitacao == $registro['inId']) {
                    incluiItemMapa($obTComprasMapaSolicitacao, $inChave, $registro_item, $inCodTipoLicitacao);
                }
            }

            # Percorre o array de itens para controlar dotação/reservas.
            foreach ($arValores as $inChave => $registro_item) {

                if ($registro_item['inId_solicitacao'] == $registro['inId']) {

                    # Caso tenha sido informado os dados da dotação, inclui na mapa_item_dotacao e cria as reservas de saldo para o Mapa de Compras.
                    if (is_numeric($registro_item['cod_despesa']) && is_numeric($registro_item['cod_conta'])) {

                        # Verificando se precisa incluir na tabela compras.solicitacao_item_dotacao
                        if (($registro_item['boDotacao'] == 'F')) {

                            $obTComprasSolicitacaoItemDotacao = new TComprasSolicitacaoItemDotacao;
                            $obTComprasSolicitacaoItemDotacao->setDado('exercicio'       , $registro_item['exercicio_solicitacao']);
                            $obTComprasSolicitacaoItemDotacao->setDado('cod_entidade'    , $registro_item['cod_entidade']);
                            $obTComprasSolicitacaoItemDotacao->setDado('cod_solicitacao' , $registro_item['cod_solicitacao']);
                            $obTComprasSolicitacaoItemDotacao->setDado('cod_item'        , $registro_item['cod_item']);
                            $obTComprasSolicitacaoItemDotacao->setDado('cod_centro'      , $registro_item['cod_centro']);
                            $obTComprasSolicitacaoItemDotacao->recuperaDadosDotacao($rsRecordSetItemDotacao);

                            if ($rsRecordSetItemDotacao->getNumLinhas() <= 0) {
                                # Inclusão na tabela compras.solicitacao_item_dotacao
                                $obTComprasSolicitacaoItemDotacao = new TComprasSolicitacaoItemDotacao;
                                $obTComprasSolicitacaoItemDotacao->obTOrcamentoReservaSaldos = & $obTOrcamentoReservaSaldos;
                                $obTComprasSolicitacaoItemDotacao->setDado('exercicio'       , $registro_item['exercicio_solicitacao']);
                                $obTComprasSolicitacaoItemDotacao->setDado('cod_entidade'    , $registro_item['cod_entidade']);
                                $obTComprasSolicitacaoItemDotacao->setDado('cod_solicitacao' , $registro_item['cod_solicitacao']);
                                $obTComprasSolicitacaoItemDotacao->setDado('cod_item'        , $registro_item['cod_item']);
                                $obTComprasSolicitacaoItemDotacao->setDado('cod_centro'      , $registro_item['cod_centro']);
                                $obTComprasSolicitacaoItemDotacao->setDado('vl_reserva'      , $registro_item['vl_reserva']);
                                $obTComprasSolicitacaoItemDotacao->setDado('quantidade'      , $registro_item['quantidade_mapa']);
                                $obTComprasSolicitacaoItemDotacao->setDado('cod_conta'       , $registro_item['cod_conta']);
                                $obTComprasSolicitacaoItemDotacao->setDado('cod_despesa'     , $registro_item['cod_despesa']);
                                $obTComprasSolicitacaoItemDotacao->inclusao();
                            }
                        }

                        # Inclusão na Mapa Item Dotação: compras.mapa_item_dotacao
                        $obTComprasMapaItemDotacao = new TComprasMapaItemDotacao;
                        $obTComprasMapaItemDotacao->obTComprasMapaSolicitacao = & $obTComprasMapaSolicitacao;
                        $obTComprasMapaItemDotacao->setDado('cod_centro'            , $registro_item['cod_centro']);
                        $obTComprasMapaItemDotacao->setDado('cod_item'              , $registro_item['cod_item']);
                        $obTComprasMapaItemDotacao->setDado('cod_conta'             , $registro_item['cod_conta']);
                        $obTComprasMapaItemDotacao->setDado('cod_despesa'           , $registro_item['cod_despesa']);
                        $obTComprasMapaItemDotacao->setDado('cod_entidade'          , $registro_item['cod_entidade']);
                        $obTComprasMapaItemDotacao->setDado('cod_solicitacao'       , $registro_item['cod_solicitacao']);
                        $obTComprasMapaItemDotacao->setDado('exercicio_solicitacao' , $registro_item['exercicio_solicitacao']);
                        $obTComprasMapaItemDotacao->setDado('quantidade'            , $registro_item['quantidade_mapa']);
                        $obTComprasMapaItemDotacao->setDado('vl_dotacao'            , $registro_item['valor_total_mapa']);
                        $obTComprasMapaItemDotacao->setDado('cod_mapa'              , $inCodMapa);
                        $obTComprasMapaItemDotacao->setDado('exercicio'             , $stExercicioMapa);

                        if ($inCodTipoLicitacao == 2) {
                            # Mapa de Compra será feita por lote.
                            if (!$registro_item['lote']) {
                                $obTComprasMapaItemDotacao->setDado('lote', 1);
                            } else {
                                $obTComprasMapaItemDotacao->setDado('lote', $registro_item['lote']);
                            }
                        } else {
                            $obTComprasMapaItemDotacao->setDado('lote', 0);
                        }

                        # Efetiva a inclusão.
                        $obTComprasMapaItemDotacao->inclusao();

                        $obTOrcamentoReservaSaldos = new TOrcamentoReservaSaldos;

                        # Pega a configuração do Compras sobre Reserva Rígida.
                        $boReservaRigida = (SistemaLegado::pegaConfiguracao('reserva_rigida', 35) == 'true');

                        if($boRegistroPreco=='true'){
                            $boReservaRigida = false;
                        }

                        # Dados para montar as mensagens de inclusão/anulação de reserva de saldo.
                        $inNumCgm      = SistemaLegado::pegaDado('numcgm','orcamento.entidade', "where cod_entidade =".$registro_item['cod_entidade']." and exercicio = '".Sessao::getExercicio()."'");
                        $stNomEntidade = SistemaLegado::pegaDado('nom_cgm','sw_cgm', "where numcgm =".$inNumCgm);
                        $stEntidade    = $registro_item['cod_entidade']." - ".ucwords(strtolower($stNomEntidade));
                        $stAcaoOrigem  = SistemaLegado::pegaDado('nom_acao', 'administracao.acao', 'WHERE cod_acao = '.Sessao::read('acao'));
                        $stSolicitacao = $registro_item['cod_solicitacao'].'/'.$registro_item['exercicio_solicitacao'];
                        # Fim dados mensagem.

                        if (is_numeric($registro_item['cod_reserva'])&&$boRegistroPreco=='false'&&$boReservaRigida) {
                            # A reserva que já é referente a solicitação este deve ser abatida e será lançada outra para o mapa
                            $flValor = $registro_item['vl_reserva_homologacao'] - $registro_item['vl_reserva'];

                            # Caso o valor tenha sido modificado no Mapa de Compras e não for utilizado todo o saldo da Solicitação, atualiza o saldo da Reserva da Solicitação.
                            if ($flValor > 0) {
                                $obTOrcamentoReservaSaldos->setDado('cod_reserva' , $registro_item['cod_reserva']);
                                $obTOrcamentoReservaSaldos->setDado('exercicio'   , $registro_item['exercicio_reserva']);
                                $obTOrcamentoReservaSaldos->setDado('vl_reserva'  , $flValor);
                                $obTOrcamentoReservaSaldos->alteraReservaSaldo();
                            } elseif (($flValor <= 0) || ($registro_item['quantidade_mapa'] == $registro_item['quantidade_maxima'])) {
                                # Verifica se é necessário anular de vez a reserva feita na homologação pois se a reserva feita no mapa pode ser menor mas mesmo assim
                                # o mapa pode ter usado toda a quantidade solicitada.
                                $obTOrcamentoReservaSaldosAnulada = new TOrcamentoReservaSaldosAnulada;
                                $obTOrcamentoReservaSaldosAnulada->setDado('cod_reserva' , $registro_item['cod_reserva']);
                                $obTOrcamentoReservaSaldosAnulada->setDado('exercicio'   , $registro_item['exercicio_reserva']);
                                $obTOrcamentoReservaSaldosAnulada->recuperaPorChave($rsReservaSaldosAnulada, $stFiltro);

                                # Antes de anular a reserva, verifica se a mesma já não foi anulada em outro processo.
                                if ($rsReservaSaldosAnulada->getNumLinhas() <= 0) {
                                    $stMsgReservaAnulada  = "Entidade: ".$stEntidade.", ";
                                    $stMsgReservaAnulada .= "Solicitação de Compras: ".$stSolicitacao.", ";
                                    $stMsgReservaAnulada .= "Item: ".$registro_item['cod_item'].", ";
                                    $stMsgReservaAnulada .= "Centro de Custo: ".$registro_item['cod_centro']." ";
                                    $stMsgReservaAnulada .= "(Origem da anulação: ".$stAcaoOrigem.").";

                                    $obTOrcamentoReservaSaldosAnulada->setDado('dt_anulacao'     , date('d/m/Y'));
                                    $obTOrcamentoReservaSaldosAnulada->setDado('motivo_anulacao' , $stMsgReservaAnulada);
                                    $obTOrcamentoReservaSaldosAnulada->inclusao();
                                }
                            }
                        }

                        //Se a Reserva for na Autorização, Anula Reservas de Saldos criadas
                        if(is_numeric($registro_item['cod_reserva'])&&!$boReservaRigida){
                            $obTOrcamentoReservaSaldosAnulada = new TOrcamentoReservaSaldosAnulada;
                            $obTOrcamentoReservaSaldosAnulada->setDado('cod_reserva' , $registro_item['cod_reserva']);
                            $obTOrcamentoReservaSaldosAnulada->setDado('exercicio'   , $registro_item['exercicio_reserva']);
                            $obTOrcamentoReservaSaldosAnulada->recuperaPorChave($rsReservaSaldosAnulada);

                            # Antes de anular a reserva, verifica se a mesma já não foi anulada em outro processo.
                            if ($rsReservaSaldosAnulada->getNumLinhas() <= 0) {
                                $stMsgReservaAnulada  = "Entidade: ".$stEntidade.", ";
                                $stMsgReservaAnulada .= "Solicitação de Compras: ".$stSolicitacao.", ";
                                $stMsgReservaAnulada .= "Item: ".$registro_item['cod_item'].", ";
                                $stMsgReservaAnulada .= "Centro de Custo: ".$registro_item['cod_centro']." ";
                                $stMsgReservaAnulada .= "(Origem da anulação: ".$stAcaoOrigem.").";

                                $obTOrcamentoReservaSaldosAnulada->setDado('dt_anulacao'     , date('d/m/Y'));
                                $obTOrcamentoReservaSaldosAnulada->setDado('motivo_anulacao' , $stMsgReservaAnulada);
                                $obTOrcamentoReservaSaldosAnulada->inclusao();
                            }
                        }

                        # Tenta criar a nova reserva que será usada pelo Mapa de Compras.
                        if ($registro_item['vl_reserva'] > 0 && $boRegistroPreco=='false' && $boReservaRigida) {
                            # Mensagem do motivo da criação da Reserva de Saldo.
                            $stMsgReserva  = "Entidade: ".$stEntidade.", ";
                            $stMsgReserva .= "Mapa de Compras: ".$inCodMapa."/".$stExercicioMapa.", ";
                            $stMsgReserva .= "Item: ".$registro_item['cod_item'].", ";
                            $stMsgReserva .= "Centro de Custo: ".$registro_item['cod_centro']." ";
                            $stMsgReserva .= "(Origem da criação: ".$stAcaoOrigem.").";

                            # Cria uma nova reserva de saldo que será utilizada agora no Mapa de Compras.
                            $obTOrcamentoReservaSaldos->setDado('exercicio' , $obTComprasMapaSolicitacao->getDado('exercicio'));
                            $obTOrcamentoReservaSaldos->proximoCod($inCodReserva);

                            $obTOrcamentoReservaSaldos->setDado('cod_reserva'         , $inCodReserva);
                            $obTOrcamentoReservaSaldos->setDado('exercicio'           , $obTComprasMapaSolicitacao->getDado('exercicio'));
                            $obTOrcamentoReservaSaldos->setDado('cod_despesa'         , $registro_item['cod_despesa']);
                            $obTOrcamentoReservaSaldos->setDado('dt_validade_inicial' , date('d/m/Y'));
                            $obTOrcamentoReservaSaldos->setDado('dt_validade_final'   , '31/12/'.Sessao::getExercicio());
                            $obTOrcamentoReservaSaldos->setDado('dt_inclusao'         , date('d/m/Y'));
                            $obTOrcamentoReservaSaldos->setDado('vl_reserva'          , $registro_item['vl_reserva']);
                            $obTOrcamentoReservaSaldos->setDado('tipo'                , 'A');
                            $obTOrcamentoReservaSaldos->setDado('motivo'              , $stMsgReserva);

                            # Inclui na tabela compras.mapa_item_reserva, caso consiga fazer a reserva de saldos.
                            if ($obTOrcamentoReservaSaldos->incluiReservaSaldo() == true) {
                                $obTComprasMapaItemReserva = new TComprasMapaItemReserva;
                                $obTComprasMapaItemReserva->setDado('exercicio_mapa'        , $obTComprasMapaItemDotacao->getDado('exercicio'));
                                $obTComprasMapaItemReserva->setDado('cod_mapa'              , $obTComprasMapaItemDotacao->getDado('cod_mapa'));
                                $obTComprasMapaItemReserva->setDado('exercicio_solicitacao' , $obTComprasMapaItemDotacao->getDado('exercicio_solicitacao'));
                                $obTComprasMapaItemReserva->setDado('cod_entidade'          , $obTComprasMapaItemDotacao->getDado('cod_entidade'));
                                $obTComprasMapaItemReserva->setDado('cod_solicitacao'       , $obTComprasMapaItemDotacao->getDado('cod_solicitacao'));
                                $obTComprasMapaItemReserva->setDado('cod_centro'            , $obTComprasMapaItemDotacao->getDado('cod_centro'));
                                $obTComprasMapaItemReserva->setDado('cod_item'              , $obTComprasMapaItemDotacao->getDado('cod_item'));
                                $obTComprasMapaItemReserva->setDado('lote'                  , $obTComprasMapaItemDotacao->getDado('lote'));
                                $obTComprasMapaItemReserva->setDado('exercicio_reserva'     , $obTOrcamentoReservaSaldos->getDado('exercicio'));
                                $obTComprasMapaItemReserva->setDado('cod_reserva'           , $obTOrcamentoReservaSaldos->getDado('cod_reserva'));
                                $obTComprasMapaItemReserva->setDado('cod_conta'             , $obTComprasMapaItemDotacao->getDado('cod_conta'));
                                $obTComprasMapaItemReserva->setDado('cod_despesa'           , $obTComprasMapaItemDotacao->getDado('cod_despesa'));
                                $obTComprasMapaItemReserva->inclusao();
                            } elseif ($boReservaRigida) {
                                # O mapa não pode ser salvo SE:
                                # Se o sistema esta configurado com reserva_rigida = sim e o não tinha saldo pra reserva
                                # Se já havia reserva feita na Homologação e a nova reserva não pode ser feita por falta de saldos (isso só acontece se a nova reserva for maior que a antiga)
                                Sessao::getExcecao()->setDescricao('Não foi possível reservar saldo para o item '.$registro_item['cod_item'].' da Solicitação '.$stSolicitacao.".");
                                Sessao::getExcecao()->tratarErro();
                            }
                        }
                    }
                }
            }
        }

        if ($request->get('boEmitirMapa') == 'true') {
            $pgProximo = $pgGera."?".Sessao::getId()."&boEmitirMapa=true&inCodMapa=".$inCodMapa."&stExercicioMapa=".$stExercicioMapa."&boMostraDado=".$request->get('boMostraDado')."&stDataMapa=".$rsMinTimestamp->getCampo('data');
        } else {
            $pgProximo = $pgForm;
        }

        $stMensagem = $inCodMapa.'/'.$stExercicioMapa;

        Sessao::encerraExcecao();
        SistemaLegado::alertaAviso($pgProximo, $stMensagem, "incluir", "aviso", Sessao::getId(), "../");
    }

} elseif ($stAcao == 'alterar') {
    $inCodMapa          = $request->get('inCodMapa');
    $stExercicioMapa    = Sessao::getExercicio();
    $inCodTipoLicitacao = $request->get('inCodTipoLicitacao');
    $boRegistroPreco    = $request->get('boRegistroPreco');
    # Pega a configuração do Compras sobre Reserva Rígida.
    $boReservaRigida = (SistemaLegado::pegaConfiguracao('reserva_rigida', 35) == 'true');

    if($boRegistroPreco=='true'){
        $boReservaRigida = false;
    }

    # Validação para o mapa ter ao mínimo uma solicitação vinculada.
    if (count(Sessao::read('solicitacoes')) == 0) {
        SistemaLegado::LiberaFrames(true,true);
        SistemaLegado::exibeAviso( 'É preciso incluir ao menos uma solicitação para salvar o mapa.'   ,"n_incluir","erro");
    } else {
        Sessao::setTrataExcecao(true);

        $rsMinTimestamp = new RecordSet;

        # Filtro para buscar o menor timestamp das solicitações vinculadas ao Mapa.
        if (is_array(Sessao::read('solicitacoes'))) {
            $stFiltro .= " AND ( \n";
            foreach (Sessao::read('solicitacoes') as $registro) {
                $stFiltro .= " (       	 cod_solicitacao = ".$registro['cod_solicitacao']." 	  \n";
                $stFiltro .= "		AND  cod_entidade    = ".$registro['cod_entidade']." 		  \n";
                $stFiltro .= "		AND  exercicio       = '".$registro['exercicio_solicitacao']."' \n";
                $stFiltro .= " ) OR";
            }

            # Retira o último OR do Filtro, desnecessário.
            $stFiltro = substr($stFiltro, 0, strlen($stFiltro)-2);

            $stFiltro .= "     ) ";

            $obTComprasSolicitacao = new TComprasSolicitacao;
            $obTComprasSolicitacao->recuperaMinTimestamp($rsMinTimestamp, $stFiltro);
        }

        # Alteração dos dados principais do Mapa de Compras.
        $obTComprasMapa = new TComprasMapa;
        $obTComprasMapa->setDado('exercicio'          , $stExercicioMapa);
        $obTComprasMapa->setDado('cod_objeto'         , $request->get('stObjeto'));
        $obTComprasMapa->setDado('cod_tipo_licitacao' , $inCodTipoLicitacao);
        $obTComprasMapa->setDado('cod_mapa'           , $inCodMapa);
        $obTComprasMapa->setDado('timestamp'          , $rsMinTimestamp->getCampo('timestamp'));
        $obTComprasMapa->alteracao();

        $obTComprasMapaItem                = new TComprasMapaItem;
        $obTComprasMapaItemAnulacao        = new TComprasMapaItemAnulacao;
        $obTComprasMapaItemDotacao         = new TComprasMapaItemDotacao;
        $obTComprasMapaItemReserva         = new TComprasMapaItemReserva;
        $obTComprasMapaSolicitacao         = new TComprasMapaSolicitacao;
        $obTComprasMapaSolicitacaoAnulacao = new TComprasMapaSolicitacaoAnulacao;

        # Controle para excluir os itens que tenham sido removidos do Mapa.
        $arItemExcluido = Sessao::read('itens_excluidos');
        if (is_array($arItemExcluido) && count($arItemExcluido) > 0) {
            excluirItemMapa($arItemExcluido, $inCodMapa, $stExercicioMapa);
        }

        # Controle para excluir as solicitações que tenham sido removidas do Mapa.
        $arSolicitacaoExcluida = Sessao::read('solicitacoes_excluidas');

        if (is_array($arSolicitacaoExcluida) && count($arSolicitacaoExcluida) > 0) {
            excluirSolicitacaoMapa($arSolicitacaoExcluida, $inCodMapa);
        }

        $arSolicitacoes = Sessao::read('solicitacoes');

        # Inclui ou Altera na tabela mapa_solicitacao
        if (is_array($arSolicitacoes)) {
            foreach ($arSolicitacoes as $chave =>$registro) {
                $obTComprasMapaSolicitacao->setDado('exercicio'             , $stExercicioMapa);
                $obTComprasMapaSolicitacao->setDado('cod_mapa'              , $inCodMapa);
                $obTComprasMapaSolicitacao->setDado('exercicio_solicitacao' , $registro['exercicio_solicitacao']);
                $obTComprasMapaSolicitacao->setDado('cod_entidade'          , $registro['cod_entidade']);
                $obTComprasMapaSolicitacao->setDado('cod_solicitacao'       , $registro['cod_solicitacao']);
                $obTComprasMapaSolicitacao->setDado('timestamp'             , date('Y-m-d H:m:s.ms'));

                if ($registro['incluir']) {
                    $obTComprasMapaSolicitacao->inclusao();
                } else {
                    $obTComprasMapaSolicitacao->alteracao();
                }
            }
        }

        $arValores = Sessao::read('itens');

        $arValoresAux = $arValores;
        $arAgrupadoValores = array();

        # Manipulação do array unificado dos itens de todas solicitações.
        if (is_array($arValores)) {

            # Agrupa os itens repetidos que possuem dotação ou centro de custos diferentes
            foreach ($arValores as $inChave => $arDados) {
                $stChave = $arDados['cod_item'].'-'.$arDados['cod_centro'].'-'.$arDados['cod_solicitacao'];

                if (!isset($arAgrupadoValores[$stChave])) {
                    $arAgrupadoValores[$stChave]['valor_total_mapa']       = $arDados['valor_total_mapa'];
                    $arAgrupadoValores[$stChave]['quantidade_mapa']        = $arDados['quantidade_mapa'];
                    $arAgrupadoValores[$stChave]['inId']                   = $arDados['inId'];
                    $arAgrupadoValores[$stChave]['inId_solicitacao']       = $arDados['inId_solicitacao'];
                    $arAgrupadoValores[$stChave]['cod_item']               = $arDados['cod_item'];
                    $arAgrupadoValores[$stChave]['item']                   = $arDados['item'];
                    $arAgrupadoValores[$stChave]['complemento']            = $arDados['complemento'];
                    $arAgrupadoValores[$stChave]['nom_unidade']            = $arDados['nom_unidade'];
                    $arAgrupadoValores[$stChave]['cod_entidade']           = $arDados['cod_entidade'];
                    $arAgrupadoValores[$stChave]['cod_solicitacao']        = $arDados['cod_solicitacao'];
                    $arAgrupadoValores[$stChave]['centro_custo']           = $arDados['centro_custo'];
                    $arAgrupadoValores[$stChave]['cod_centro']             = $arDados['cod_centro'];
                    $arAgrupadoValores[$stChave]['exercicio_solicitacao']  = $arDados['exercicio_solicitacao'];
                    $arAgrupadoValores[$stChave]['lote']                   = $arDados['lote'];
                    $arAgrupadoValores[$stChave]['dotacao']            	   = $arDados['dotacao'];
                    $arAgrupadoValores[$stChave]['cod_despesa']            = $arDados['cod_despesa'];
                    $arAgrupadoValores[$stChave]['cod_conta']              = $arDados['cod_conta'];
                    $arAgrupadoValores[$stChave]['vl_reserva']             = $arDados['vl_reserva'];
                    $arAgrupadoValores[$stChave]['vl_reserva_homologacao'] = $arDados['vl_reserva_homologacao'];
                    $arAgrupadoValores[$stChave]['cod_reserva']            = $arDados['cod_reserva'];
                    $arAgrupadoValores[$stChave]['exercicio_reserva']      = $arDados['exercicio_reserva'];
                    $arAgrupadoValores[$stChave]['boReserva']              = $arDados['boReserva'];
                    $arAgrupadoValores[$stChave]['incluir']                = $arDados['incluir'];

                    foreach ($arValoresAux as $inChaveAux => $arDadosAux) {
                        if (($arDados['inId'] != $arDadosAux['inId'])
                            && ($arDados['cod_item'] == $arDadosAux['cod_item']
                            && $arDados['cod_centro'] == $arDadosAux['cod_centro']
                            && $arDados['cod_solicitacao'] == $arDadosAux['cod_solicitacao'])) {

                            $stChave = $arDados['cod_item'].'-'.$arDados['cod_centro'].'-'.$arDados['cod_solicitacao'];

                            $arAgrupadoValores[$stChave]['valor_total_mapa'] += $arDadosAux['valor_total_mapa'];
                            $arAgrupadoValores[$stChave]['quantidade_mapa']  += $arDadosAux['quantidade_mapa'];

                            unset($arValoresAux[$inChaveAux]);
                        }
                    }
                }
            }

            # Esse codigo foi refatorado apenas para atualizar quantidade e valor dos itens que tenham mesmo id e centro de custo ou inserir novos itens caso tenham sido adicionado em novas solicitações.
            foreach ($arAgrupadoValores as $inChave => $registro_item) {
                # Atualiza as quantidades e valores dos itens agrupados por id e centro de custo.
                $obTComprasMapaItem->obTComprasMapaSolicitacao = & $obTComprasMapaSolicitacao;
                $obTComprasMapaItem->setDado('cod_centro'            , $registro_item['cod_centro']);
                $obTComprasMapaItem->setDado('cod_item'              , $registro_item['cod_item']);
                $obTComprasMapaItem->setDado('quantidade'            , $registro_item['quantidade_mapa']);
                $obTComprasMapaItem->setDado('vl_total'              , $registro_item['valor_total_mapa']);
                $obTComprasMapaItem->setDado('cod_entidade'          , $registro_item['cod_entidade']);
                $obTComprasMapaItem->setDado('cod_solicitacao'       , $registro_item['cod_solicitacao']);
                $obTComprasMapaItem->setDado('exercicio_solicitacao' , $registro_item['exercicio_solicitacao']);
                $obTComprasMapaItem->setDado('cod_mapa'              , $inCodMapa);
                $obTComprasMapaItem->setDado('exercicio'             , $stExercicioMapa);

                if ($inCodTipoLicitacao == 2) {
                    # Mapa de Compras será feita por lote
                    if (!$registro_item['lote']) {
                        $obTComprasMapaItem->setDado('lote', 1);
                    } else {
                        $obTComprasMapaItem->setDado('lote', $registro_item['lote']);
                    }
                } else {
                    $obTComprasMapaItem->setDado('lote', 0);
                }

                if ($registro_item['incluir']) {
                    $obTComprasMapaItem->inclusao();
                } else {
                    $obTComprasMapaItem->alteracao();
                }
            }

            # Verificação se é necessário incluir dotações, criar reservas e atualizar dotações e reservas da Solicitação de Compras.
            foreach ($arValores as $inChave => $registro_item) {
                $boIncluiSolicitacaoDotacao = false;

                $obTComprasSolicitacaoItemDotacao = new TComprasSolicitacaoItemDotacao;
                # Verifica se necessita atualizar a tabela compras.solicitacao_item_dotacao quando o item não tinha dotação na solicitação e foi informado no Mapa de Compras.

                # Inclusão do item na compras.mapa_item
                if ((is_numeric($registro_item['cod_despesa'])) && (is_numeric($registro_item['cod_conta']))) {
                    # Caso o item não tenha dotação vinculada, inclui na tabela compras.solicitacao_item_dotacao.
                    if (strtoupper($registro_item['boDotacao']) == 'F') {
                        $obTComprasSolicitacaoItemDotacao->obTOrcamentoReservaSaldos = & $obTOrcamentoReservaSaldos;
                        $obTComprasSolicitacaoItemDotacao->setDado('exercicio'       , $registro_item['exercicio_solicitacao']);
                        $obTComprasSolicitacaoItemDotacao->setDado('cod_entidade'    , $registro_item['cod_entidade']);
                        $obTComprasSolicitacaoItemDotacao->setDado('cod_solicitacao' , $registro_item['cod_solicitacao']);
                        $obTComprasSolicitacaoItemDotacao->setDado('cod_item'        , $registro_item['cod_item']);
                        $obTComprasSolicitacaoItemDotacao->setDado('cod_centro'      , $registro_item['cod_centro']);
                        $obTComprasSolicitacaoItemDotacao->setDado('vl_reserva'      , $registro_item['vl_reserva']);
                        $obTComprasSolicitacaoItemDotacao->setDado('cod_despesa'     , $registro_item['cod_despesa']);
                        $obTComprasSolicitacaoItemDotacao->setDado('cod_conta'       , $registro_item['cod_conta']);
                        $obTComprasSolicitacaoItemDotacao->setDado('quantidade'      , $registro_item['quantidade_mapa']);
                        $obTComprasSolicitacaoItemDotacao->inclusao();

                        $boIncluiSolicitacaoDotacao = true;
                    }

                    # Inclui um novo item em compras.mapa_item_dotacao.
                    $obTComprasMapaItemDotacao = new TComprasMapaItemDotacao;
                    $obTComprasMapaItemDotacao->obTComprasMapaSolicitacao = & $obTComprasMapaSolicitacao;
                    $obTComprasMapaItemDotacao->setDado('cod_centro'                , $registro_item['cod_centro']);
                    $obTComprasMapaItemDotacao->setDado('cod_item'                  , $registro_item['cod_item']);
                    $obTComprasMapaItemDotacao->setDado('cod_conta'                 , $registro_item['cod_conta']);
                    $obTComprasMapaItemDotacao->setDado('cod_despesa'               , $registro_item['cod_despesa']);
                    $obTComprasMapaItemDotacao->setDado('cod_entidade'              , $registro_item['cod_entidade']);
                    $obTComprasMapaItemDotacao->setDado('cod_solicitacao'           , $registro_item['cod_solicitacao']);
                    $obTComprasMapaItemDotacao->setDado('exercicio_solicitacao'     , $registro_item['exercicio_solicitacao']);
                    $obTComprasMapaItemDotacao->setDado('quantidade'                , $registro_item['quantidade_mapa']);
                    $obTComprasMapaItemDotacao->setDado('vl_dotacao'                , $registro_item['valor_total_mapa']);
                    $obTComprasMapaItemDotacao->setDado('cod_mapa'                  , $inCodMapa);
                    $obTComprasMapaItemDotacao->setDado('exercicio'                 , $stExercicioMapa);
                    $obTComprasMapaItemDotacao->recuperaPorChave($rsMapaItemDotacao);

                    if ($inCodTipoLicitacao == 2) {
                        # Licitação será feita por lote
                        if (!$registro_item['lote']) {
                            $obTComprasMapaItemDotacao->setDado('lote', 1);
                        } else {
                            $obTComprasMapaItemDotacao->setDado('lote', $registro_item['lote']);
                        }
                    } else {
                        $obTComprasMapaItemDotacao->setDado('lote', 0);
                    }

                    # Caso tenha inserido dotação na Solicitação é por que foi informada no Mapa, então cadastra no Mapa.
                    if ($boIncluiSolicitacaoDotacao == true || $rsMapaItemDotacao->getNumLinhas() < 1) {
                        $obTComprasMapaItemDotacao->inclusao();
                    } else {
                        $obTComprasMapaItemDotacao->alteracao();
                    }

                    $obTOrcamentoReservaSaldos        = new TOrcamentoReservaSaldos;
                    $obTOrcamentoReservaSaldosAnulada = new TOrcamentoReservaSaldosAnulada;

                    # Verificando se é necessário anular a reserva de saldos feita na homologação pois neste caso toda a quantidade do item já foi
                    # pra mapa então mesmo que o valor da reserva do mapa seja menor que a da homologação está não sera usada por isso será anulada

                    # Dados para montar as mensagens de inclusão/anulação de reserva de saldo.
                    $inNumCgm      = SistemaLegado::pegaDado('numcgm','orcamento.entidade', "where cod_entidade =".$registro_item['cod_entidade']." and exercicio = '".Sessao::getExercicio()."'");
                    $stNomEntidade = SistemaLegado::pegaDado('nom_cgm','sw_cgm', "where numcgm =".$inNumCgm);
                    $stEntidade    = $registro_item['cod_entidade']." - ".ucwords(strtolower($stNomEntidade));
                    $stSolicitacao = $registro_item['cod_solicitacao'].'/'.$registro_item['exercicio_solicitacao'];
                    $stAcaoOrigem  = SistemaLegado::pegaDado('nom_acao', 'administracao.acao', 'WHERE cod_acao = '.Sessao::read('acao'));
                    # Fim dados mensagem.

                    # Caso exista uma reserva criada na Solicitação de Compras.
                    if (is_numeric($registro_item['cod_reserva_solicitacao'])&&$boRegistroPreco=='false'&&$boReservaRigida) {
                        # Se a quantidade foi alterada no Mapa de Compras.
                        if (($registro_item['quantidade_mapa'] != $registro_item['quantidade_mapa_original']) ||
                            ($registro_item['valor_total_mapa'] != $registro_item['valor_total_mapa_original'])){

                            $nuQtdSaldoSolicitacao = ($registro_item['quantidade_solicitada'] - $registro_item['quantidade_atendida']) - $registro_item['quantidade_mapa'];
                            $nuVlrSaldoSolicitacao = ($registro_item['valor_total_mapa'] / $registro_item['quantidade_mapa']) * $nuQtdSaldoSolicitacao;

                            if ($nuQtdSaldoSolicitacao > 0) {
                                if($registro_item['cod_reserva']!=$registro_item['cod_reserva_solicitacao']){
                                    if($registro_item['valor_total_mapa'] > $registro_item['valor_total_mapa_original']){
                                        # Verifica se a reserva da Solicitação está anulada.
                                        $obTOrcamentoReservaSaldosAnulada = new TOrcamentoReservaSaldosAnulada;
                                        $obTOrcamentoReservaSaldosAnulada->setDado('cod_reserva' , $registro_item['cod_reserva_solicitacao']);
                                        $obTOrcamentoReservaSaldosAnulada->setDado('exercicio'   , $registro_item['exercicio_reserva_solicitacao']);
                                        $obTOrcamentoReservaSaldosAnulada->recuperaPorChave($rsReservaSaldosAnulada);
                                        # Caso exista uma anulação para a reserva da Solicitação e tiver saldo, exclui a anulação e atualiza o valor da reserva da Solicitação.
                                        if ($rsReservaSaldosAnulada->getNumLinhas() > 0) {
                                            $obTOrcamentoReservaSaldosAnulada->exclusao();
                                        }

                                        $obTOrcamentoReservaSaldos = new TOrcamentoReservaSaldos;
                                        $obTOrcamentoReservaSaldos->setDado('cod_reserva' , $registro_item['cod_reserva_solicitacao']);
                                        $obTOrcamentoReservaSaldos->setDado('exercicio'   , $registro_item['exercicio_reserva_solicitacao']);
                                        $obTOrcamentoReservaSaldos->setDado('vl_reserva'  , $nuVlrSaldoSolicitacao);

                                        if (!$obTOrcamentoReservaSaldos->alteraReservaSaldo()) {
                                            trataErroTransacao($nuQtdSaldoSolicitacao.' '.$nuVlrSaldoSolicitacao.' Não foi possível alterar a reserva da Solicitação de Compras para o item '.$registro_item['cod_item']);
                                        }

                                        $obTOrcamentoReservaSaldosAnulada = new TOrcamentoReservaSaldosAnulada;
                                        $obTOrcamentoReservaSaldosAnulada->setDado('cod_reserva' , $registro_item['cod_reserva']);
                                        $obTOrcamentoReservaSaldosAnulada->setDado('exercicio'   , $registro_item['exercicio_reserva']);
                                        $obTOrcamentoReservaSaldosAnulada->recuperaPorChave($rsReservaSaldosAnulada);
                                        if ($rsReservaSaldosAnulada->getNumLinhas() > 0) {
                                            $obTOrcamentoReservaSaldosAnulada->exclusao();
                                        }

                                        # Altera a reserva do Mapa de Compras.
                                        $obTOrcamentoReservaSaldos = new TOrcamentoReservaSaldos;
                                        $obTOrcamentoReservaSaldos->setDado('cod_reserva' , $registro_item['cod_reserva']);
                                        $obTOrcamentoReservaSaldos->setDado('exercicio'   , $registro_item['exercicio_reserva']);
                                        $obTOrcamentoReservaSaldos->setDado('vl_reserva'  , $registro_item['valor_total_mapa']);

                                        if (!$obTOrcamentoReservaSaldos->alteraReservaSaldo()) {
                                            trataErroTransacao('Não foi possível alterar a reserva do Mapa de Compras para o item '.$registro_item['cod_item']);
                                        }
                                    }else{
                                        $obTOrcamentoReservaSaldosAnulada = new TOrcamentoReservaSaldosAnulada;
                                        $obTOrcamentoReservaSaldosAnulada->setDado('cod_reserva' , $registro_item['cod_reserva']);
                                        $obTOrcamentoReservaSaldosAnulada->setDado('exercicio'   , $registro_item['exercicio_reserva']);
                                        $obTOrcamentoReservaSaldosAnulada->recuperaPorChave($rsReservaSaldosAnulada);
                                        if ($rsReservaSaldosAnulada->getNumLinhas() > 0) {
                                            $obTOrcamentoReservaSaldosAnulada->exclusao();
                                        }

                                        # Altera a reserva do Mapa de Compras.
                                        $obTOrcamentoReservaSaldos = new TOrcamentoReservaSaldos;
                                        $obTOrcamentoReservaSaldos->setDado('cod_reserva' , $registro_item['cod_reserva']);
                                        $obTOrcamentoReservaSaldos->setDado('exercicio'   , $registro_item['exercicio_reserva']);
                                        $obTOrcamentoReservaSaldos->setDado('vl_reserva'  , $registro_item['valor_total_mapa']);

                                        if (!$obTOrcamentoReservaSaldos->alteraReservaSaldo()) {
                                            trataErroTransacao('Não foi possível alterar a reserva do Mapa de Compras para o item '.$registro_item['cod_item']);
                                        }

                                        # Verifica se a reserva da Solicitação está anulada.
                                        $obTOrcamentoReservaSaldosAnulada = new TOrcamentoReservaSaldosAnulada;
                                        $obTOrcamentoReservaSaldosAnulada->setDado('cod_reserva' , $registro_item['cod_reserva_solicitacao']);
                                        $obTOrcamentoReservaSaldosAnulada->setDado('exercicio'   , $registro_item['exercicio_reserva_solicitacao']);
                                        $obTOrcamentoReservaSaldosAnulada->recuperaPorChave($rsReservaSaldosAnulada);
                                        # Caso exista uma anulação para a reserva da Solicitação e tiver saldo, exclui a anulação e atualiza o valor da reserva da Solicitação.
                                        if ($rsReservaSaldosAnulada->getNumLinhas() > 0) {
                                            $obTOrcamentoReservaSaldosAnulada->exclusao();
                                        }

                                        $obTOrcamentoReservaSaldos = new TOrcamentoReservaSaldos;
                                        $obTOrcamentoReservaSaldos->setDado('cod_reserva' , $registro_item['cod_reserva_solicitacao']);
                                        $obTOrcamentoReservaSaldos->setDado('exercicio'   , $registro_item['exercicio_reserva_solicitacao']);
                                        $obTOrcamentoReservaSaldos->setDado('vl_reserva'  , $nuVlrSaldoSolicitacao);

                                        if (!$obTOrcamentoReservaSaldos->alteraReservaSaldo()) {
                                            trataErroTransacao($nuQtdSaldoSolicitacao.' '.$nuVlrSaldoSolicitacao.' Não foi possível alterar a reserva da Solicitação de Compras para o item '.$registro_item['cod_item']);
                                        }
                                    }
                                }else{
                                    # Verifica se a reserva da Solicitação está anulada.
                                    $obTOrcamentoReservaSaldosAnulada = new TOrcamentoReservaSaldosAnulada;
                                    $obTOrcamentoReservaSaldosAnulada->setDado('cod_reserva' , $registro_item['cod_reserva_solicitacao']);
                                    $obTOrcamentoReservaSaldosAnulada->setDado('exercicio'   , $registro_item['exercicio_reserva_solicitacao']);
                                    $obTOrcamentoReservaSaldosAnulada->recuperaPorChave($rsReservaSaldosAnulada);
                                    # Caso exista uma anulação para a reserva da Solicitação e tiver saldo, exclui a anulação e atualiza o valor da reserva da Solicitação.
                                    if ($rsReservaSaldosAnulada->getNumLinhas() > 0) {
                                        $obTOrcamentoReservaSaldosAnulada->exclusao();
                                    }

                                    $obTOrcamentoReservaSaldos = new TOrcamentoReservaSaldos;
                                    $obTOrcamentoReservaSaldos->setDado('cod_reserva' , $registro_item['cod_reserva_solicitacao']);
                                    $obTOrcamentoReservaSaldos->setDado('exercicio'   , $registro_item['exercicio_reserva_solicitacao']);
                                    $obTOrcamentoReservaSaldos->setDado('vl_reserva'  , $nuVlrSaldoSolicitacao);

                                    if (!$obTOrcamentoReservaSaldos->alteraReservaSaldo()) {
                                        trataErroTransacao($nuQtdSaldoSolicitacao.' '.$nuVlrSaldoSolicitacao.' Não foi possível alterar a reserva da Solicitação de Compras para o item '.$registro_item['cod_item']);
                                    }

                                    # Mensagem do motivo da criação da Reserva de Saldo.
                                    $stMsgReserva  = "Entidade: ".$stEntidade.", ";
                                    $stMsgReserva .= "Mapa de Compras: ".$inCodMapa."/".$stExercicioMapa.", ";
                                    $stMsgReserva .= "Item: ".$registro_item['cod_item'].", ";
                                    $stMsgReserva .= "Centro de Custo: ".$registro_item['cod_centro']." ";
                                    $stMsgReserva .= "(Origem da criação: ".$stAcaoOrigem.").";

                                    $obTOrcamentoReservaSaldos = new TOrcamentoReservaSaldos;
                                    $obTOrcamentoReservaSaldos->proximoCod($inCodReserva);
                                    $obTOrcamentoReservaSaldos->setDado('cod_reserva'         , $inCodReserva);
                                    $obTOrcamentoReservaSaldos->setDado('vl_reserva'          , $registro_item['valor_total_mapa']);
                                    $obTOrcamentoReservaSaldos->setDado('exercicio'           , $obTComprasMapaSolicitacao->getDado('exercicio'));
                                    $obTOrcamentoReservaSaldos->setDado('cod_despesa'         , $registro_item['dotacao']);
                                    $obTOrcamentoReservaSaldos->setDado('dt_validade_inicial' , date('d/m/Y'));
                                    $obTOrcamentoReservaSaldos->setDado('dt_validade_final'   , '31/12/'.Sessao::getExercicio());
                                    $obTOrcamentoReservaSaldos->setDado('dt_inclusao'         , date('d/m/Y'));
                                    $obTOrcamentoReservaSaldos->setDado('tipo'                , 'A');
                                    $obTOrcamentoReservaSaldos->setDado('motivo'              , $stMsgReserva);

                                    # Inclui na tabela compras.mapa_item_reserva, se conseguir criar a reserva de saldos.
                                    if ($obTOrcamentoReservaSaldos->incluiReservaSaldo()) {
                                       $obTComprasMapaItemReserva = new TComprasMapaItemReserva;
                                       $obTComprasMapaItemReserva->setDado('exercicio_mapa'       , $obTComprasMapaItemDotacao->getDado('exercicio'));
                                       $obTComprasMapaItemReserva->setDado('cod_mapa'             , $obTComprasMapaItemDotacao->getDado('cod_mapa'));
                                       $obTComprasMapaItemReserva->setDado('exercicio_solicitacao', $obTComprasMapaItemDotacao->getDado('exercicio_solicitacao'));
                                       $obTComprasMapaItemReserva->setDado('cod_entidade'         , $obTComprasMapaItemDotacao->getDado('cod_entidade'));
                                       $obTComprasMapaItemReserva->setDado('cod_solicitacao'      , $obTComprasMapaItemDotacao->getDado('cod_solicitacao'));
                                       $obTComprasMapaItemReserva->setDado('cod_centro'           , $obTComprasMapaItemDotacao->getDado('cod_centro'));
                                       $obTComprasMapaItemReserva->setDado('cod_item'             , $obTComprasMapaItemDotacao->getDado('cod_item'));
                                       $obTComprasMapaItemReserva->setDado('lote'                 , $obTComprasMapaItemDotacao->getDado('lote'));
                                       $obTComprasMapaItemReserva->setDado('exercicio_reserva'    , $obTOrcamentoReservaSaldos->getDado('exercicio'));
                                       $obTComprasMapaItemReserva->setDado('cod_reserva'          , $obTOrcamentoReservaSaldos->getDado('cod_reserva'));
                                       $obTComprasMapaItemReserva->setDado('cod_conta'            , $obTComprasMapaItemDotacao->getDado('cod_conta'));
                                       $obTComprasMapaItemReserva->setDado('cod_despesa'          , $obTComprasMapaItemDotacao->getDado('cod_despesa'));
                                       $obTComprasMapaItemReserva->inclusao();
                                    } else{
                                        trataErroTransacao('Não foi possível reservar saldo para o item '.$registro_item['cod_item'].' da Solicitação '.$stSolicitacao.'.');
                                    }
                                }
                            } elseif ($nuQtdSaldoSolicitacao == 0) {
                                $obTOrcamentoReservaSaldosAnulada = new TOrcamentoReservaSaldosAnulada;
                                $obTOrcamentoReservaSaldosAnulada->setDado('cod_reserva' , $registro_item['cod_reserva_solicitacao']);
                                $obTOrcamentoReservaSaldosAnulada->setDado('exercicio'   , $registro_item['exercicio_reserva_solicitacao']);
                                $obTOrcamentoReservaSaldosAnulada->recuperaPorChave($rsReservaSaldosAnulada);

                                if ($rsReservaSaldosAnulada->getNumLinhas() < 1) {
                                    $stMsgReservaAnulada  = "Entidade: ".$stEntidade.", ";
                                    $stMsgReservaAnulada .= "Solicitação de Compras: ".$stSolicitacao.", ";
                                    $stMsgReservaAnulada .= "Item: ".$registro_item['cod_item'].", ";
                                    $stMsgReservaAnulada .= "Centro de Custo: ".$registro_item['cod_centro']." ";
                                    $stMsgReservaAnulada .= "(Origem da anulação: ".$stAcaoOrigem.").";

                                    # Verifica se a reserva do Mapa atende toda reserva da Solicitação, então anula a reserva da Solicitação.
                                    $obTOrcamentoReservaSaldosAnulada = new TOrcamentoReservaSaldosAnulada;
                                    $obTOrcamentoReservaSaldosAnulada->setDado('cod_reserva'     , $registro_item['cod_reserva_solicitacao']);
                                    $obTOrcamentoReservaSaldosAnulada->setDado('exercicio'       , $registro_item['exercicio_reserva_solicitacao']);
                                    $obTOrcamentoReservaSaldosAnulada->setDado('dt_anulacao'     , date('d/m/Y'));
                                    $obTOrcamentoReservaSaldosAnulada->setDado('motivo_anulacao' , $stMsgReservaAnulada);
                                    $obTOrcamentoReservaSaldosAnulada->inclusao();
                                }

                                if($registro_item['cod_reserva']==$registro_item['cod_reserva_solicitacao']){
                                    # Mensagem do motivo da criação da Reserva de Saldo.
                                    $stMsgReserva  = "Entidade: ".$stEntidade.", ";
                                    $stMsgReserva .= "Mapa de Compras: ".$inCodMapa."/".$stExercicioMapa.", ";
                                    $stMsgReserva .= "Item: ".$registro_item['cod_item'].", ";
                                    $stMsgReserva .= "Centro de Custo: ".$registro_item['cod_centro']." ";
                                    $stMsgReserva .= "(Origem da criação: ".$stAcaoOrigem.").";

                                    $obTOrcamentoReservaSaldos = new TOrcamentoReservaSaldos;
                                    $obTOrcamentoReservaSaldos->proximoCod($inCodReserva);
                                    $obTOrcamentoReservaSaldos->setDado('cod_reserva'         , $inCodReserva);
                                    $obTOrcamentoReservaSaldos->setDado('vl_reserva'          , $registro_item['valor_total_mapa']);
                                    $obTOrcamentoReservaSaldos->setDado('exercicio'           , $obTComprasMapaSolicitacao->getDado('exercicio'));
                                    $obTOrcamentoReservaSaldos->setDado('cod_despesa'         , $registro_item['dotacao']);
                                    $obTOrcamentoReservaSaldos->setDado('dt_validade_inicial' , date('d/m/Y'));
                                    $obTOrcamentoReservaSaldos->setDado('dt_validade_final'   , '31/12/'.Sessao::getExercicio());
                                    $obTOrcamentoReservaSaldos->setDado('dt_inclusao'         , date('d/m/Y'));
                                    $obTOrcamentoReservaSaldos->setDado('tipo'                , 'A');
                                    $obTOrcamentoReservaSaldos->setDado('motivo'              , $stMsgReserva);

                                    # Inclui na tabela compras.mapa_item_reserva, se conseguir criar a reserva de saldos.
                                    if ($obTOrcamentoReservaSaldos->incluiReservaSaldo()) {
                                       $obTComprasMapaItemReserva = new TComprasMapaItemReserva;
                                       $obTComprasMapaItemReserva->setDado('exercicio_mapa'       , $obTComprasMapaItemDotacao->getDado('exercicio'));
                                       $obTComprasMapaItemReserva->setDado('cod_mapa'             , $obTComprasMapaItemDotacao->getDado('cod_mapa'));
                                       $obTComprasMapaItemReserva->setDado('exercicio_solicitacao', $obTComprasMapaItemDotacao->getDado('exercicio_solicitacao'));
                                       $obTComprasMapaItemReserva->setDado('cod_entidade'         , $obTComprasMapaItemDotacao->getDado('cod_entidade'));
                                       $obTComprasMapaItemReserva->setDado('cod_solicitacao'      , $obTComprasMapaItemDotacao->getDado('cod_solicitacao'));
                                       $obTComprasMapaItemReserva->setDado('cod_centro'           , $obTComprasMapaItemDotacao->getDado('cod_centro'));
                                       $obTComprasMapaItemReserva->setDado('cod_item'             , $obTComprasMapaItemDotacao->getDado('cod_item'));
                                       $obTComprasMapaItemReserva->setDado('lote'                 , $obTComprasMapaItemDotacao->getDado('lote'));
                                       $obTComprasMapaItemReserva->setDado('exercicio_reserva'    , $obTOrcamentoReservaSaldos->getDado('exercicio'));
                                       $obTComprasMapaItemReserva->setDado('cod_reserva'          , $obTOrcamentoReservaSaldos->getDado('cod_reserva'));
                                       $obTComprasMapaItemReserva->setDado('cod_conta'            , $obTComprasMapaItemDotacao->getDado('cod_conta'));
                                       $obTComprasMapaItemReserva->setDado('cod_despesa'          , $obTComprasMapaItemDotacao->getDado('cod_despesa'));
                                       $obTComprasMapaItemReserva->inclusao();
                                    } else{
                                        trataErroTransacao('Não foi possível reservar saldo para o item '.$registro_item['cod_item'].' da Solicitação '.$stSolicitacao.'.');
                                    }
                                }else{
                                    $obTOrcamentoReservaSaldosAnulada = new TOrcamentoReservaSaldosAnulada;
                                    $obTOrcamentoReservaSaldosAnulada->setDado('cod_reserva' , $registro_item['cod_reserva']);
                                    $obTOrcamentoReservaSaldosAnulada->setDado('exercicio'   , $registro_item['exercicio_reserva']);
                                    $obTOrcamentoReservaSaldosAnulada->recuperaPorChave($rsReservaSaldosAnulada);
                                    if ($rsReservaSaldosAnulada->getNumLinhas() > 0) {
                                        $obTOrcamentoReservaSaldosAnulada->exclusao();
                                    }

                                    # Altera a reserva do Mapa de Compras.
                                    $obTOrcamentoReservaSaldos = new TOrcamentoReservaSaldos;
                                    $obTOrcamentoReservaSaldos->setDado('cod_reserva' , $registro_item['cod_reserva']);
                                    $obTOrcamentoReservaSaldos->setDado('exercicio'   , $registro_item['exercicio_reserva']);
                                    $obTOrcamentoReservaSaldos->setDado('vl_reserva'  , $registro_item['valor_total_mapa']);

                                    if (!$obTOrcamentoReservaSaldos->alteraReservaSaldo()) {
                                        trataErroTransacao('Não foi possível alterar a reserva do Mapa de Compras para o item '.$registro_item['cod_item']);
                                    }
                                }
                            }
                        }else{
                            $obTOrcamentoReservaSaldosAnulada = new TOrcamentoReservaSaldosAnulada;
                            $obTOrcamentoReservaSaldosAnulada->setDado('cod_reserva' , $registro_item['cod_reserva']);
                            $obTOrcamentoReservaSaldosAnulada->setDado('exercicio'   , $registro_item['exercicio_reserva']);
                            $obTOrcamentoReservaSaldosAnulada->recuperaPorChave($rsReservaSaldosAnulada);

                            if ($rsReservaSaldosAnulada->getNumLinhas() > 0) {
                                $obTOrcamentoReservaSaldosAnulada->exclusao();
                            }

                            # Verifica se há saldo para exclusão da Anulação da reserva do Mapa de Compras.
                            $obTOrcamentoReservaSaldos = new TOrcamentoReservaSaldos;
                            $obTOrcamentoReservaSaldos->setDado('cod_reserva' , $registro_item['cod_reserva']);
                            $obTOrcamentoReservaSaldos->setDado('exercicio'   , $registro_item['exercicio_reserva']);
                            $obTOrcamentoReservaSaldos->setDado('vl_reserva'  , $registro_item['valor_total_mapa']);

                            if (!$obTOrcamentoReservaSaldos->alteraReservaSaldo()) {
                                trataErroTransacao('Não foi possível alterar a reserva do Mapa de Compras para o item '.$registro_item['cod_item']);
                            }
                        }
                    } else {
                        # Se não existir cod_reserva e tiver algum valor setado para reserva, cria uma nova reserva de saldo para o Mapa de Compras.
                        if ($registro_item['valor_total_mapa'] > 0 && !is_numeric($registro_item['cod_reserva']) && $boRegistroPreco=='false' && $boReservaRigida) {
                            $boReservaHomologacao = true;

                            # Mensagem do motivo da criação da Reserva de Saldo.
                            $stMsgReserva  = "Entidade: ".$stEntidade.", ";
                            $stMsgReserva .= "Mapa de Compras: ".$inCodMapa."/".$stExercicioMapa.", ";
                            $stMsgReserva .= "Item: ".$registro_item['cod_item'].", ";
                            $stMsgReserva .= "Centro de Custo: ".$registro_item['cod_centro']." ";
                            $stMsgReserva .= "(Origem da criação: ".$stAcaoOrigem.").";

                            $obTOrcamentoReservaSaldos->proximoCod($inCodReserva);
                            $obTOrcamentoReservaSaldos->setDado('cod_reserva'         , $inCodReserva);
                            $obTOrcamentoReservaSaldos->setDado('vl_reserva'          , $registro_item['valor_total_mapa']);
                            $obTOrcamentoReservaSaldos->setDado('exercicio'           , $obTComprasMapaSolicitacao->getDado('exercicio'));
                            $obTOrcamentoReservaSaldos->setDado('cod_despesa'         , $registro_item['dotacao']);
                            $obTOrcamentoReservaSaldos->setDado('dt_validade_inicial' , date('d/m/Y'));
                            $obTOrcamentoReservaSaldos->setDado('dt_validade_final'   , '31/12/'.Sessao::getExercicio());
                            $obTOrcamentoReservaSaldos->setDado('dt_inclusao'         , date('d/m/Y'));
                            $obTOrcamentoReservaSaldos->setDado('tipo'                , 'A');
                            $obTOrcamentoReservaSaldos->setDado('motivo'              , $stMsgReserva);

                            # Inclui na tabela compras.mapa_item_reserva, se conseguir criar a reserva de saldos.
                            if ($obTOrcamentoReservaSaldos->incluiReservaSaldo()) {
                               $obTComprasMapaItemReserva = new TComprasMapaItemReserva;
                               $obTComprasMapaItemReserva->setDado('exercicio_mapa'       , $obTComprasMapaItemDotacao->getDado('exercicio'));
                               $obTComprasMapaItemReserva->setDado('cod_mapa'             , $obTComprasMapaItemDotacao->getDado('cod_mapa'));
                               $obTComprasMapaItemReserva->setDado('exercicio_solicitacao', $obTComprasMapaItemDotacao->getDado('exercicio_solicitacao'));
                               $obTComprasMapaItemReserva->setDado('cod_entidade'         , $obTComprasMapaItemDotacao->getDado('cod_entidade'));
                               $obTComprasMapaItemReserva->setDado('cod_solicitacao'      , $obTComprasMapaItemDotacao->getDado('cod_solicitacao'));
                               $obTComprasMapaItemReserva->setDado('cod_centro'           , $obTComprasMapaItemDotacao->getDado('cod_centro'));
                               $obTComprasMapaItemReserva->setDado('cod_item'             , $obTComprasMapaItemDotacao->getDado('cod_item'));
                               $obTComprasMapaItemReserva->setDado('lote'                 , $obTComprasMapaItemDotacao->getDado('lote'));
                               $obTComprasMapaItemReserva->setDado('exercicio_reserva'    , $obTOrcamentoReservaSaldos->getDado('exercicio'));
                               $obTComprasMapaItemReserva->setDado('cod_reserva'          , $obTOrcamentoReservaSaldos->getDado('cod_reserva'));
                               $obTComprasMapaItemReserva->setDado('cod_conta'            , $obTComprasMapaItemDotacao->getDado('cod_conta'));
                               $obTComprasMapaItemReserva->setDado('cod_despesa'          , $obTComprasMapaItemDotacao->getDado('cod_despesa'));
                               $obTComprasMapaItemReserva->inclusao();
                            } elseif (($boReservaRigida) || ($boReservaHomologacao)) {
                                # O mapa não pode ser salvo SE:
                                # Se o sistema esta configurado com reserva_rigida = sim e o não tinha saldo pra reserva
                                # Se já havia reserva feita na Homologação e a nova reserva não pode ser feita por falta de saldos (isso só acontece se a nova reserva for maior que a antiga)
                                trataErroTransacao('Não foi possível reservar saldo para o item '.$registro_item['cod_item'].' da Solicitação '.$stSolicitacao.'.');
                            } else {
                                trataErroTransacao("Não foi possível reservar saldo para o item ".$registro_item['cod_item']."/".$registro_item['cod_solicitacao']." devido a falta de saldo. ");
                            }
                        } elseif ($registro_item['valor_total_mapa'] > 0 && is_numeric($registro_item['cod_reserva']) && $boRegistroPreco=='false' && $boReservaRigida) {
                            $obTOrcamentoReservaSaldosAnulada = new TOrcamentoReservaSaldosAnulada;
                            $obTOrcamentoReservaSaldosAnulada->setDado('cod_reserva' , $registro_item['cod_reserva']);
                            $obTOrcamentoReservaSaldosAnulada->setDado('exercicio'   , $registro_item['exercicio_reserva']);
                            $obTOrcamentoReservaSaldosAnulada->recuperaPorChave($rsReservaSaldosAnulada);

                            if ($rsReservaSaldosAnulada->getNumLinhas() > 0) {
                                $obTOrcamentoReservaSaldosAnulada->exclusao();
                            }

                            # Altera a reserva do Mapa de Compras caso seja modificado algum valor no Mapa.
                            $obTOrcamentoReservaSaldos = new TOrcamentoReservaSaldos;
                            $obTOrcamentoReservaSaldos->setDado('cod_reserva' , $registro_item['cod_reserva']);
                            $obTOrcamentoReservaSaldos->setDado('exercicio'   , $registro_item['exercicio_reserva']);
                            $obTOrcamentoReservaSaldos->setDado('vl_reserva'  , $registro_item['valor_total_mapa']);

                            if (!$obTOrcamentoReservaSaldos->alteraReservaSaldo()) {
                                trataErroTransacao('Não foi possível alterar a reserva do Mapa de Compras para o item '.$registro_item['cod_item']);
                            }
                        }
                    }

                    //Se a Reserva for na Autorização, Anula Reservas de Saldos criadas
                    if(!$boReservaRigida && ( is_numeric($registro_item['cod_reserva_solicitacao']) || is_numeric($registro_item['cod_reserva']) )){
                        if(is_numeric($registro_item['cod_reserva_solicitacao'])){
                            $obTOrcamentoReservaSaldosAnulada = new TOrcamentoReservaSaldosAnulada;
                            $obTOrcamentoReservaSaldosAnulada->setDado('cod_reserva' , $registro_item['cod_reserva_solicitacao']);
                            $obTOrcamentoReservaSaldosAnulada->setDado('exercicio'   , $registro_item['exercicio_reserva_solicitacao']);
                            $obTOrcamentoReservaSaldosAnulada->recuperaPorChave($rsReservaSaldosAnulada);

                            if ($rsReservaSaldosAnulada->getNumLinhas() < 1) {
                                $stMsgReservaAnulada  = "Entidade: ".$stEntidade.", ";
                                $stMsgReservaAnulada .= "Solicitação de Compras: ".$stSolicitacao.", ";
                                $stMsgReservaAnulada .= "Item: ".$registro_item['cod_item'].", ";
                                $stMsgReservaAnulada .= "Centro de Custo: ".$registro_item['cod_centro']." ";
                                $stMsgReservaAnulada .= "(Origem da anulação: ".$stAcaoOrigem.").";

                                $obTOrcamentoReservaSaldosAnulada->setDado('dt_anulacao'     , date('d/m/Y'));
                                $obTOrcamentoReservaSaldosAnulada->setDado('motivo_anulacao' , $stMsgReservaAnulada);
                                $obTOrcamentoReservaSaldosAnulada->inclusao();
                            }
                        }
                        if(is_numeric($registro_item['cod_reserva'])){
                            $obTOrcamentoReservaSaldosAnulada = new TOrcamentoReservaSaldosAnulada;
                            $obTOrcamentoReservaSaldosAnulada->setDado('cod_reserva' , $registro_item['cod_reserva']);
                            $obTOrcamentoReservaSaldosAnulada->setDado('exercicio'   , $registro_item['exercicio_reserva']);
                            $obTOrcamentoReservaSaldosAnulada->recuperaPorChave($rsReservaSaldosAnulada);

                            if ($rsReservaSaldosAnulada->getNumLinhas() < 1) {
                                $stMsgReservaAnulada  = "Entidade: ".$stEntidade.", ";
                                $stMsgReservaAnulada .= "Solicitação de Compras: ".$stSolicitacao.", ";
                                $stMsgReservaAnulada .= "Item: ".$registro_item['cod_item'].", ";
                                $stMsgReservaAnulada .= "Centro de Custo: ".$registro_item['cod_centro']." ";
                                $stMsgReservaAnulada .= "(Origem da anulação: ".$stAcaoOrigem.").";

                                $obTOrcamentoReservaSaldosAnulada->setDado('dt_anulacao'     , date('d/m/Y'));
                                $obTOrcamentoReservaSaldosAnulada->setDado('motivo_anulacao' , $stMsgReservaAnulada);
                                $obTOrcamentoReservaSaldosAnulada->inclusao();
                            }
                        }
                    }
                }
            }
        }

        $stMensagem = $inCodMapa."/".$stExercicioMapa."  $stMensagem ";

        if ($request->get('boEmitirMapa') == 'true') {
            $pgProximo = $pgGera."?".Sessao::getId()."&inCodMapa=".$inCodMapa."&stExercicioMapa=".$stExercicioMapa."&boMostraDado=".$request->get('boMostraDado')."&stDataMapa=".$rsMinTimestamp->getCampo('data');
        } else {
            $pgProximo = $pgList."?".Sessao::getId()."&stAcao=$stAcao$stLink";
        }

        SistemaLegado::alertaAviso($pgProximo ,$stMensagem ,"incluir","aviso", Sessao::getId(), "../");
        Sessao::encerraExcecao();
    }

# Fim da alteração
} elseif ($stAcao == 'excluir') {

    Sessao::setTrataExcecao(true);

    $inCodMapa       = $request->get('cod_mapa');
    $stExercicioMapa = $request->get('exercicio');

    if (is_numeric($inCodMapa) && is_numeric($stExercicioMapa)) {
        $obTOrcamentoReservaSaldos        = new TOrcamentoReservaSaldos;
        $obTOrcamentoReservaSaldosAnulada = new TOrcamentoReservaSaldosAnulada;
        $obTComprasMapaItemReserva        = new TComprasMapaItemReserva;

        # Recupera as reservas de saldos do mapa
        $stFiltro  = "    WHERE  mapa_item_reserva.cod_mapa       =  ".$inCodMapa."         \n";
        $stFiltro .= "      AND  mapa_item_reserva.exercicio_mapa = '".$stExercicioMapa."'  \n";
        $stFiltro .= " 																		\n";
        $stFiltro .= " GROUP BY  mapa_item_reserva.exercicio_reserva                        \n";
        $stFiltro .= "        ,  mapa_item_reserva.cod_reserva                              \n";
        $stFiltro .= "        ,  mapa_item_reserva.cod_despesa                              \n";
        $stFiltro .= "        ,  mapa_item_reserva.cod_conta                                \n";
        $stFiltro .= "        ,  mapa_item_reserva.cod_item                                 \n";
        $stFiltro .= "        ,  mapa_item_reserva.cod_centro                               \n";
        $stFiltro .= "        ,  mapa_item_reserva.cod_entidade                             \n";
        $stFiltro .= "        ,  solicitacao_homologada_reserva.cod_reserva                 \n";
        $stFiltro .= "        ,  solicitacao_homologada_reserva.exercicio                   \n";
        $stFiltro .= "        ,  reserva_saldos_solicitacao.vl_reserva                      \n";
        $stFiltro .= "        ,  reserva_saldos.vl_reserva                                  \n";
        $stFiltro .= "        ,  mapa_item_reserva.cod_despesa								\n";
        $stFiltro .= "        ,  mapa_item_reserva.cod_conta								\n";
        $obTComprasMapaItemReserva->recuperaReservas($rsReservas, $stFiltro);

        while (!$rsReservas->eof()) {
            # Verifica se existe Reserva de Saldo criada pela Solicitação.
            if ($rsReservas->getCampo('cod_reserva_solicitacao')) {

                # Verifica se a reserva da Solicitação está anulada.
                $obTOrcamentoReservaSaldosAnulada = new TOrcamentoReservaSaldosAnulada;
                $obTOrcamentoReservaSaldosAnulada->setDado('cod_reserva' , $rsReservas->getCampo('cod_reserva_solicitacao'));
                $obTOrcamentoReservaSaldosAnulada->setDado('exercicio'   , $rsReservas->getCampo('exercicio_reserva_solicitacao'));
                $obTOrcamentoReservaSaldosAnulada->recuperaPorChave($rsReservaSaldosAnulada);

                # Caso exista uma anulação para a reserva da Solicitação e tiver saldo, exclui a anulação e atualiza o valor da reserva da Solicitação.
                if ($rsReservaSaldosAnulada->getNumLinhas() > 0) {
                    $obTOrcamentoReservaSaldosAnulada->exclusao();

                    # A anulação da reserva de saldo é excluída e a reserva da Solicitação volta a ter o valor da reserva do Mapa excluida.
                    $nuVlReservaSaldoSolicitacao = $rsReservas->getCampo('vl_reserva');
                } else {
                    $obTOrcamentoReservaSaldos = new TOrcamentoReservaSaldos;
                    $obTOrcamentoReservaSaldos->setDado('cod_reserva' , $rsReservas->getCampo('cod_reserva_solicitacao'));
                    $obTOrcamentoReservaSaldos->setDado('exercicio'   , $rsReservas->getCampo('exercicio_reserva_solicitacao'));
                    $obTOrcamentoReservaSaldos->recuperaPorChave($rsReservasSaldosSolicitacao);

                    # Atualiza o valor da reserva da solicitação
                    $nuVlReservaSaldoSolicitacao = $rsReservasSaldosSolicitacao->getCampo('vl_reserva') + $rsReservas->getCampo('vl_reserva');
                }

                $obTOrcamentoReservaSaldos = new TOrcamentoReservaSaldos;
                $obTOrcamentoReservaSaldos->setDado('cod_reserva' , $rsReservas->getCampo('cod_reserva_solicitacao'));
                $obTOrcamentoReservaSaldos->setDado('exercicio'   , $rsReservas->getCampo('exercicio_reserva_solicitacao'));
                $obTOrcamentoReservaSaldos->setDado('vl_reserva'  , $nuVlReservaSaldoSolicitacao);

                if (!$obTOrcamentoReservaSaldos->alteraReservaSaldo()) {
                    $stMensagem = 'Não foi possível alterar a reserva da Solicitação de Compras para o item '.$rsReservas->getCampo('cod_item');
                }
            }

            # Dados para montar as mensagens de anulação de reserva de saldo.
            $inNumCgm      = SistemaLegado::pegaDado('numcgm','orcamento.entidade', "where cod_entidade = ".$rsReservas->getCampo('cod_entidade')." and exercicio = '".Sessao::getExercicio()."'");
            $stNomEntidade = SistemaLegado::pegaDado('nom_cgm','sw_cgm', "where numcgm =".$inNumCgm);
            $stEntidade    = $rsReservas->getCampo('cod_entidade')." - ".ucwords(strtolower($stNomEntidade));
            $stAcaoOrigem  = SistemaLegado::pegaDado('nom_acao', 'administracao.acao', 'WHERE cod_acao = '.Sessao::read('acao'));
            # Fim dados mensagem.

            $stMsgReservaAnulada  = "Entidade: ".$stEntidade.", ";
            $stMsgReservaAnulada .= "Mapa de Compras: ".$inCodMapa."/".$stExercicioMapa.", ";
            $stMsgReservaAnulada .= "Item: ".$rsReservas->getCampo('cod_item').", ";
            $stMsgReservaAnulada .= "Centro de Custo: ".$rsReservas->getCampo('cod_centro')." ";
            $stMsgReservaAnulada .= "(Origem da anulação: ".$stAcaoOrigem.").";

            # Anulando a Reserva de Saldo criada pelo Mapa de Compras
            $obTOrcamentoReservaSaldosAnulada = new TOrcamentoReservaSaldosAnulada;
            $obTOrcamentoReservaSaldosAnulada->setDado('cod_reserva'     , $rsReservas->getCampo('cod_reserva'));
            $obTOrcamentoReservaSaldosAnulada->setDado('exercicio'       , $rsReservas->getCampo('exercicio_reserva'));
            $obTOrcamentoReservaSaldosAnulada->setDado('dt_anulacao'     , date('d/m/Y'));
            $obTOrcamentoReservaSaldosAnulada->setDado('motivo_anulacao' , $stMsgReservaAnulada);
            $obTOrcamentoReservaSaldosAnulada->inclusao();

            $rsReservas->proximo();
        }

        # Apaga as reservas existentes para itens do Mapa.
        $obTComprasMapaItemReserva->setDado('exercicio_mapa' , $stExercicioMapa);
        $obTComprasMapaItemReserva->setDado('cod_mapa'       , $inCodMapa);
        $obTComprasMapaItemReserva->exclusao();

        # Exclui as anulações do itens que sejam do Mapa de Compras.
        $obTComprasMapaItemAnulacao = new TComprasMapaItemAnulacao;
        $obTComprasMapaItemAnulacao->setDado('cod_mapa' , $inCodMapa);
        $obTComprasMapaItemAnulacao->setDado('exercicio', $stExercicioMapa);
        $obTComprasMapaItemAnulacao->exclusao();

        # Exclui as anulações para solicitações que sejam do Mapa de Compras.
        $obTComprasMapaSolicitacaoAnulacao = new TComprasMapaSolicitacaoAnulacao;
        $obTComprasMapaSolicitacaoAnulacao->setDado('cod_mapa'  , $inCodMapa);
        $obTComprasMapaSolicitacaoAnulacao->setDado('exercicio' , $stExercicioMapa);
        $obTComprasMapaSolicitacaoAnulacao->exclusao();

        # Exclui as dotações dos itens do Mapa de Compras.
        $obTComprasMapaItemDotacao = new TComprasMapaItemDotacao;
        $obTComprasMapaItemDotacao->setDado('cod_mapa' , $inCodMapa);
        $obTComprasMapaItemDotacao->setDado('exercicio', $stExercicioMapa);
        $obTComprasMapaItemDotacao->exclusao();

        # Exclui os itens do Mapa de Compras.
        $obTComprasMapaItem = new TComprasMapaItem;
        $obTComprasMapaItem->setDado('cod_mapa' , $inCodMapa);
        $obTComprasMapaItem->setDado('exercicio', $stExercicioMapa);
        $obTComprasMapaItem->exclusao();

        # Exclui o vínculo entre Solicitação de Compras e Mapa de Compras.
        $obTComprasMapaSolicitacao = new TComprasMapaSolicitacao;
        $obTComprasMapaSolicitacao->setDado('cod_mapa' , $inCodMapa);
        $obTComprasMapaSolicitacao->setDado('exercicio', $stExercicioMapa);
        $obTComprasMapaSolicitacao->exclusao();

        # Exclui o Mapa de Compras.
        $obTComprasMapa = new TComprasMapa;
        $obTComprasMapa->setDado('cod_mapa' , $inCodMapa);
        $obTComprasMapa->setDado('exercicio', $stExercicioMapa);
        $obTComprasMapa->exclusao();

        SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=excluir", "Mapa ". $inCodMapa.'/'.$stExercicioMapa ,"alterar","aviso", Sessao::getId(), "../");
    }

    Sessao::encerraExcecao();

} elseif ($stAcao == 'anular') {
    Sessao::setTrataExcecao(true);

    $inCodMapa	     = $request->get('inCodMapa');
    $stExercicioMapa = $request->get('stExercicioMapa');
    $stMotivo	     = $request->get('stMotivo');
    $boRegistroPreco = $request->get('boRegistroPreco');
    # Pega a configuração do Compras sobre Reserva Rígida.
    $boReservaRigida = (SistemaLegado::pegaConfiguracao('reserva_rigida', 35) == 'true');

    if($boRegistroPreco=='true'){
        $boReservaRigida = false;
    }

    if (is_numeric($inCodMapa) && is_numeric($stExercicioMapa)) {
        $obTComprasMapa = new TComprasMapa;
        $stFiltro  = " AND  mapa.cod_mapa  = ".$inCodMapa;
        $stFiltro .= " AND  mapa.exercicio = '".$stExercicioMapa."'";
        $obTComprasMapa->recuperaMapasAnulacao($rsRecordSet,$stFiltro);

        # Validação para ver se o Mapa existe e é possível de anulação.
        if ($rsRecordSet->getNumLinhas() > 0) {
            $obTComprasMapaSolicitacaoAnulacao = new TComprasMapaSolicitacaoAnulacao;
            $obTOrcamentoReservaSaldos         = new TOrcamentoReservaSaldos;
            $obTOrcamentoReservaSaldosAnulada  = new TOrcamentoReservaSaldosAnulada;
            $obTComprasMapaItemAnulacao        = new TComprasMapaItemAnulacao;
            $obTComprasMapaItemReserva         = new TComprasMapaItemReserva;
            $obTComprasMapa                    = new TComprasMapa;

            $rsExclusaoItensReserva = new RecordSet;
            $rsItensReserva         = new RecordSet;

            $boAnulacao = false;

            $arSolicitacao = Sessao::read('solicitacoes');

            if (is_numeric($inCodMapa) && is_numeric($stExercicioMapa)) {

                foreach ($arSolicitacao as $solicitacao) {
                    if ($solicitacao['anulada']) {

                        $numcgm      = SistemaLegado::pegaDado('numcgm','orcamento.entidade', "where cod_entidade =".$solicitacao['cod_entidade']." and exercicio = '".Sessao::getExercicio()."'");
                        $nomEntidade = SistemaLegado::pegaDado('nom_cgm','sw_cgm', "where numcgm =".$numcgm);

                        # Inclusão na tabela compras.mapa_solicitacao_anulacao
                        $obTComprasMapaSolicitacaoAnulacao->setDado('exercicio'             , $stExercicioMapa);
                        $obTComprasMapaSolicitacaoAnulacao->setDado('cod_mapa'              , $inCodMapa);
                        $obTComprasMapaSolicitacaoAnulacao->setDado('exercicio_solicitacao' , $solicitacao['exercicio_solicitacao']);
                        $obTComprasMapaSolicitacaoAnulacao->setDado('cod_entidade'          , $solicitacao['cod_entidade']);
                        $obTComprasMapaSolicitacaoAnulacao->setDado('cod_solicitacao'       , $solicitacao['cod_solicitacao']);
                        $obTComprasMapaSolicitacaoAnulacao->setDado('timestamp'             , date('Y-m-d H:m:s.ms'));
                        $obTComprasMapaSolicitacaoAnulacao->setDado('motivo'                , stripslashes($stMotivo));
                        $obTComprasMapaSolicitacaoAnulacao->inclusao($boTransacao);

                        $arItem = Sessao::read('itens');

                        # Percorre os itens da solicitação que estão em Mapa.
                        foreach ($arItem as $item) {
                            if (($item['anulado']) &&
                                ($item['inId_solicitacao'] == $solicitacao['inId']) &&
                                ($item['quantidadeAnular'] > 0)) {
                                $boAnulacao = true;
                                # Inclusão na tabela compras.mapa_item_anulacao
                                $obTComprasMapaItemAnulacao->obTComprasMapaSolicitacaoAnulacao = & $obTComprasMapaSolicitacaoAnulacao;

                                $obTComprasMapaItemAnulacao->setDado('exercicio'             , $stExercicioMapa);
                                $obTComprasMapaItemAnulacao->setDado('cod_mapa'              , $inCodMapa);
                                $obTComprasMapaItemAnulacao->setDado('exercicio_solicitacao' , $solicitacao['exercicio_solicitacao']);
                                $obTComprasMapaItemAnulacao->setDado('cod_entidade'          , $solicitacao['cod_entidade']);
                                $obTComprasMapaItemAnulacao->setDado('cod_solicitacao'       , $solicitacao['cod_solicitacao']);
                                $obTComprasMapaItemAnulacao->setDado('cod_centro'            , $item['cod_centro']);
                                $obTComprasMapaItemAnulacao->setDado('cod_item'              , $item['cod_item']);
                                $obTComprasMapaItemAnulacao->setDado('lote'                  , $item['lote']);
                                $obTComprasMapaItemAnulacao->setDado('quantidade'            , $item['quantidadeAnular']);
                                $obTComprasMapaItemAnulacao->setDado('vl_total'              , $item['valorAnular']);
                                $obTComprasMapaItemAnulacao->setDado('cod_conta'             , $item['cod_conta']);
                                $obTComprasMapaItemAnulacao->setDado('cod_despesa'           , $item['cod_despesa']);

                                $obTComprasMapaItemAnulacao->inclusao($boTransacao);

                                # Verificar se tem reserva de saldos, se tiver altera a reserva ou anula a reserva, dependendo do valor setado no programa.
                                if (is_numeric($item['cod_reserva']) && ($item['valorAnular'] > 0)) {
                                    $obTOrcamentoReservaSaldos->setDado('cod_reserva' , $item['cod_reserva']);
                                    $obTOrcamentoReservaSaldos->setDado('exercicio'   , $item['exercicio_reserva']);
                                    $obTOrcamentoReservaSaldos->recuperaPorChave($rsReservaSaldo);

                                    $nuSaldoAtual = $rsReservaSaldo->getCampo('vl_reserva');

                                    if ($nuSaldoAtual > $item['valorAnular']) {
                                        $obTOrcamentoReservaSaldos->setDado('vl_reserva' , ($nuSaldoAtual - $item['valorAnular']));
                                        $obTOrcamentoReservaSaldos->alteraReservaSaldo();
                                    } else {
                                        # Verifica se já não existe uma anulação para essa reserva.
                                        $obTOrcamentoReservaSaldosAnulada->setDado('cod_reserva' , $item['cod_reserva']);
                                        $obTOrcamentoReservaSaldosAnulada->setDado('exercicio'   , $item['exercicio_reserva']);
                                        $obTOrcamentoReservaSaldosAnulada->recuperaPorChave($rsReservaSaldoAnulada);

                                        # Se a reserva for menor ou igual o valor a anular e não houver uma anulação já cadastrada, é incluida um anulação para a mesma.
                                        if ($rsReservaSaldoAnulada->getNumLinhas() < 1) {

                                            # Dados para montar as mensagens de anulação de reserva de saldo.
                                            $inNumCgm      = SistemaLegado::pegaDado('numcgm','orcamento.entidade', "where cod_entidade =".$item['cod_entidade']." and exercicio = '".Sessao::getExercicio()."'");
                                            $stNomEntidade = SistemaLegado::pegaDado('nom_cgm','sw_cgm', "where numcgm =".$inNumCgm);
                                            $stEntidade    = $item['cod_entidade']." - ".ucwords(strtolower($stNomEntidade));
                                            $stAcaoOrigem  = SistemaLegado::pegaDado('nom_acao', 'administracao.acao', 'WHERE cod_acao = '.Sessao::read('acao'));
                                            # Fim dados mensagem.

                                            $stMsgReservaAnulada  = "Entidade: ".$stEntidade.", ";
                                            $stMsgReservaAnulada .= "Mapa de Compras: ".$inCodMapa."/".$stExercicioMapa.", ";
                                            $stMsgReservaAnulada .= "Item: ".$item['cod_item'].", ";
                                            $stMsgReservaAnulada .= "Centro de Custo: ".$item['cod_centro']." ";
                                            $stMsgReservaAnulada .= "(Origem da anulação: ".$stAcaoOrigem.").";

                                            $obTOrcamentoReservaSaldosAnulada->setDado('dt_anulacao'     , date('d/m/Y'));
                                            $obTOrcamentoReservaSaldosAnulada->setDado('motivo_anulacao' , $stMsgReservaAnulada);
                                            $obTOrcamentoReservaSaldosAnulada->inclusao($boTransacao);
                                        }
                                    }

                                    # Rotina que faz o equilíbrio de saldo da reserva da Solicitação de Compras.
                                    if (is_numeric($item['cod_reserva_solicitacao']) &&
                                        is_numeric($item['exercicio_reserva_solicitacao'])){

                                        # Verifica se a reserva da Solicitação está anulada.
                                        $obTOrcamentoReservaSaldosAnulada = new TOrcamentoReservaSaldosAnulada;
                                        $obTOrcamentoReservaSaldosAnulada->setDado('cod_reserva' , $item['cod_reserva_solicitacao']);
                                        $obTOrcamentoReservaSaldosAnulada->setDado('exercicio'   , $item['exercicio_reserva_solicitacao']);
                                        $obTOrcamentoReservaSaldosAnulada->recuperaPorChave($rsReservaSaldosAnulada);

                                        $obTOrcamentoReservaSaldos = new TOrcamentoReservaSaldos;
                                        $obTOrcamentoReservaSaldos->setDado('cod_reserva' , $item['cod_reserva_solicitacao']);
                                        $obTOrcamentoReservaSaldos->setDado('exercicio'   , $item['exercicio_reserva_solicitacao']);
                                        $obTOrcamentoReservaSaldos->recuperaPorChave($rsReservaSaldosSolicitacao);

                                        # Caso exista uma anulação para a reserva da Solicitação e tiver saldo, exclui a anulação e atualiza o valor da reserva da Solicitação.
                                        if ($rsReservaSaldosAnulada->getNumLinhas() > 0) {

                                            # Exclui a anulação da reserva da Solicitação, pois nesse momento ela passará a ter saldo.
                                            $obTOrcamentoReservaSaldosAnulada->exclusao($boTransacao);

                                            # Altera a reserva da Solicitação com o valor anulado do Mapa.
                                            $obTOrcamentoReservaSaldos->setDado('vl_reserva' , $item['valorAnular']);
                                        } else {
                                            # Altera a reserva da Solicitação com o valor anulado do Mapa.
                                            $obTOrcamentoReservaSaldos->setDado('vl_reserva' , ($rsReservaSaldosSolicitacao->getCampo('vl_reserva') + $item['valorAnular']));
                                        }

                                        if (!$obTOrcamentoReservaSaldos->alteraReservaSaldo()) {
                                            $stMensagem = 'Não foi possível alterar a reserva da Solicitação de Compras para o item '.$item['cod_item'];
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                $stFiltro  = " AND  mapa.cod_mapa  = ".$inCodMapa;
                $stFiltro .= " AND  mapa.exercicio = '".$stExercicioMapa."'";

                # Verifica se os itens em mapa de compra devem ser excluidos em MapaItensReservas
                $obTComprasMapa->recuperaMapaItemPermissaoExclusaoReserva($rsExclusaoItensReserva, $stFiltro);

                while (!$rsExclusaoItensReserva->eof()) {
                    if ($rsExclusaoItensReserva->getCampo('saldo') == 0) {
                        $obTComprasMapaItemReserva->setDado('exercicio_mapa'        , $rsExclusaoItensReserva->getCampo('exercicio'));
                        $obTComprasMapaItemReserva->setDado('cod_mapa'              , $rsExclusaoItensReserva->getCampo('cod_mapa'));
                        $obTComprasMapaItemReserva->setDado('exercicio_solicitacao' , $rsExclusaoItensReserva->getCampo('exercicio_solicitacao'));
                        $obTComprasMapaItemReserva->setDado('cod_entidade'          , $rsExclusaoItensReserva->getCampo('cod_entidade'));
                        $obTComprasMapaItemReserva->setDado('cod_solicitacao'       , $rsExclusaoItensReserva->getCampo('cod_solicitacao'));
                        $obTComprasMapaItemReserva->setDado('cod_centro'            , $rsExclusaoItensReserva->getCampo('cod_centro'));
                        $obTComprasMapaItemReserva->setDado('cod_item'              , $rsExclusaoItensReserva->getCampo('cod_item'));
                        $obTComprasMapaItemReserva->setDado('lote'                  , $rsExclusaoItensReserva->getCampo('lote'));
                        $obTComprasMapaItemReserva->setDado('cod_despesa'           , $rsExclusaoItensReserva->getCampo('cod_despesa'));
                        $obTComprasMapaItemReserva->setDado('cod_conta'             , $rsExclusaoItensReserva->getCampo('cod_conta'));

                        $obTComprasMapaItemReserva->exclusao( $boTransacao );
                    }

                    $rsExclusaoItensReserva->proximo();
                }

                $stMensagem = "Mapa anulado ".$inCodMapa."/".$stExercicioMapa."  $stMensagem ";

                if ($boAnulacao) {
                    SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=anular", $stMensagem,"anular","aviso", Sessao::getId(), "../");
                } else {
                    SistemaLegado::LiberaFrames(true,true);
                    SistemaLegado::exibeAviso('É preciso anular ao menos um item para salvar a anulação', "n_incluir", "erro");
                }
            }

        } else {
            Sessao::write('acao', 1606);

            $obTComprasCompraDireta = new TComprasCompraDireta;

            $stFiltro  = " WHERE NOT EXISTS ( SELECT 1                                                                              \n";
            $stFiltro .= "                      FROM compras.compra_direta_anulacao                                                 \n";
            $stFiltro .= "                     WHERE compra_direta_anulacao.cod_compra_direta  = compra_direta.cod_compra_direta    \n";
            $stFiltro .= "                       AND compra_direta_anulacao.cod_entidade       = compra_direta.cod_entidade         \n";
            $stFiltro .= "                       AND compra_direta_anulacao.exercicio_entidade = compra_direta.exercicio_entidade   \n";
            $stFiltro .= "                       AND compra_direta_anulacao.cod_modalidade     = compra_direta.cod_modalidade     ) \n";
            $stFiltro .= "   AND compra_direta.cod_mapa       =  ".$inCodMapa."                                                     \n";
            $stFiltro .= "   AND compra_direta.exercicio_mapa = '".$stExercicioMapa."'                                              \n";
            $obTComprasCompraDireta->recuperaTodos($rsRecordSet, $stFiltro);

            if ($rsRecordSet->getNumLinhas() > 0) {
                $inCodCompraDireta = $rsRecordSet->getCampo('cod_compra_direta').'/'.Sessao::getExercicio();
                SistemaLegado::LiberaFrames(true,true);
                SistemaLegado::exibeAviso('Este mapa possui vínculo com a Compra Direta ('.$inCodCompraDireta.')', 'n_incluir', 'erro');
            } else {
                $obTLicitacaoLicitacao = new TLicitacaoLicitacao;

                $stFiltro  = "  WHERE                                                                                    \n";
                $stFiltro .= "        NOT EXISTS ( SELECT 1                                                              \n";
                $stFiltro .= "                       FROM licitacao.licitacao_anulada                                    \n";
                $stFiltro .= "                      WHERE licitacao_anulada.cod_licitacao  = licitacao.cod_licitacao     \n";
                $stFiltro .= "                        AND licitacao_anulada.cod_modalidade = licitacao.cod_modalidade    \n";
                $stFiltro .= "                        AND licitacao_anulada.cod_entidade   = licitacao.cod_entidade      \n";
                $stFiltro .= "                        AND licitacao_anulada.exercicio      = licitacao.exercicio       ) \n";
                $stFiltro .= "    AND licitacao.exercicio_mapa = '".$stExercicioMapa."'                                  \n";
                $stFiltro .= "    AND licitacao.cod_mapa       =  ".$inCodMapa."                                         \n";
                $obTLicitacaoLicitacao->recuperaTodos($rsRecordSet, $stFiltro);

                if ($rsRecordSet->getNumLinhas() > 0) {
                    $stLicitacao = $rsRecordSet->getCampo('cod_licitacao').'/'.$rsRecordSet->getCampo('exercicio');
                    SistemaLegado::LiberaFrames(true,true);
                    SistemaLegado::exibeAviso('Este mapa possui vínculo com a Licitação ('.$stLicitacao.')', 'n_incluir', 'erro');
                }
            }
        }
    }

    Sessao::encerraExcecao();
}

?>
