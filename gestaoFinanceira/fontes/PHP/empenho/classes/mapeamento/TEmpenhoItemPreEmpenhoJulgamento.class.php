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
    * Classe de mapeamento da tabela EMPENHO.ITEM_PRE_EMPENHO_MAPA
    * Data de Criação: 18/01/2007

    * @author Analista: Lucas Teixeira Stephanou
    * @author Desenvolvedor: Lucas Teixeira Stephanou

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TEmpenhoItemPreEmpenhoJulgamento.class.php 64194 2015-12-14 18:20:21Z jean $

    * Casos de uso: uc-02.03.03, uc-02.03.02, uc-03.05.21
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TEmpenhoItemPreEmpenhoJulgamento extends Persistente
{
    /**
    * Método Construtor
    * @access Private
    */
    public function __construct()
    {
        parent::Persistente();
        $this->setTabela('empenho.item_pre_empenho_julgamento');

        $this->setCampoCod('');
        $this->setComplementoChave('cod_pre_empenho,exercicio,num_item');

        $this->AddCampo('cod_pre_empenho'       , 'integer' , true, ''  , true  , true  );
        $this->AddCampo('exercicio'             , 'char'    , true, '04', true  , true  );
        $this->AddCampo('num_item'              , 'integer' , true, ''  , true  , false );
        $this->AddCampo('cod_item'              , 'integer' , true, ''  , true  , false );
        $this->AddCampo('cod_cotacao'           , 'integer' , true, ''  , false , true  );
        $this->AddCampo('exercicio_julgamento'  , 'char'    , true, '4' , false , true  );
        $this->AddCampo('lote'                  , 'integer' , true, ''  , false , true  );
        $this->AddCampo('cgm_fornecedor'        , 'integer' , true, ''  , false , true  );
    }

    function recuperaCentroCustoMapaItem(&$rsRecordSet, $stFiltro = '', $boTransacao = '')
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if( $this->getDado('exercicio') != "" )
            $stFiltro  .= " AND pre_empenho.exercicio = '".$this->getDado('exercicio')."'               \n";
        if( $this->getDado('cod_pre_empenho') != "" )
            $stFiltro .= " AND pre_empenho.cod_pre_empenho = ".$this->getDado('cod_pre_empenho')."      \n";
        if( $this->getDado('num_item') != "" )
            $stFiltro .= " AND item_pre_empenho_julgamento.num_item = ".$this->getDado('num_item')."    \n";

        if( $stFiltro )
            $stFiltro = " WHERE ".substr($stFiltro,5,strlen($stFiltro)-4);

        $stOrder  = "ORDER BY autorizacao_empenho.exercicio         \n";
        $stOrder .= "       , autorizacao_empenho.cod_autorizacao   \n";
        $stOrder .= "       , item_pre_empenho_julgamento.num_item  \n";

        $stSql = $this->montaRecuperaCentroCustoMapaItem().$stFiltro.$stOrder;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    function montaRecuperaCentroCustoMapaItem()
    {
        $stSql  = " SELECT autorizacao_empenho.cod_autorizacao
                         , autorizacao_empenho.exercicio
                         , autorizacao_empenho.cod_entidade
                         , autorizacao_empenho.cod_pre_empenho
                         , item_pre_empenho_julgamento.cod_cotacao
                         , item_pre_empenho_julgamento.exercicio_julgamento
                         , mapa_item.cod_mapa
                         , mapa_item.exercicio AS exercicio_mapa
                         , mapa_item.cod_solicitacao
                         , mapa_item.exercicio_solicitacao
                         , item_pre_empenho_julgamento.num_item
                         , item_pre_empenho_julgamento.cod_item
                         , CASE WHEN item_pre_empenho.cod_centro IS NOT NULL THEN
                                    item_pre_empenho.cod_centro
                                ELSE
                                    mapa_item.cod_centro
                           END AS cod_centro

                      FROM compras.julgamento 

                INNER JOIN compras.julgamento_item
                        ON julgamento.exercicio    = julgamento_item.exercicio
                       AND julgamento.cod_cotacao  = julgamento_item.cod_cotacao

                INNER JOIN empenho.item_pre_empenho_julgamento
                        ON julgamento_item.exercicio      = item_pre_empenho_julgamento.exercicio
                       AND julgamento_item.cod_cotacao    = item_pre_empenho_julgamento.cod_cotacao
                       AND julgamento_item.cod_item       = item_pre_empenho_julgamento.cod_item
                       AND julgamento_item.lote           = item_pre_empenho_julgamento.lote
                       AND julgamento_item.cgm_fornecedor = item_pre_empenho_julgamento.cgm_fornecedor

                INNER JOIN empenho.item_pre_empenho
                        ON item_pre_empenho_julgamento.cod_pre_empenho = item_pre_empenho.cod_pre_empenho
                       AND item_pre_empenho_julgamento.exercicio       = item_pre_empenho.exercicio
                       AND item_pre_empenho_julgamento.num_item        = item_pre_empenho.num_item

                INNER JOIN empenho.pre_empenho
                        ON item_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                       AND item_pre_empenho.exercicio       = pre_empenho.exercicio

                INNER JOIN empenho.autorizacao_empenho
                        ON pre_empenho.exercicio       = autorizacao_empenho.exercicio
                       AND pre_empenho.cod_pre_empenho = autorizacao_empenho.cod_pre_empenho

                INNER JOIN compras.cotacao_fornecedor_item
                        ON cotacao_fornecedor_item.exercicio      = julgamento_item.exercicio
                       AND cotacao_fornecedor_item.cod_cotacao    = julgamento_item.cod_cotacao
                       AND cotacao_fornecedor_item.cod_item       = julgamento_item.cod_item
                       AND cotacao_fornecedor_item.cgm_fornecedor = julgamento_item.cgm_fornecedor
                       AND cotacao_fornecedor_item.lote           = julgamento_item.lote

                INNER JOIN compras.cotacao_item
                        ON cotacao_fornecedor_item.exercicio   = cotacao_item.exercicio
                       AND cotacao_fornecedor_item.cod_cotacao = cotacao_item.cod_cotacao
                       AND cotacao_fornecedor_item.cod_item    = cotacao_item.cod_item
                       AND cotacao_fornecedor_item.lote        = cotacao_item.lote

                INNER JOIN compras.mapa_cotacao
                        ON cotacao_item.cod_cotacao = mapa_cotacao.cod_cotacao
                       AND cotacao_item.exercicio   = mapa_cotacao .exercicio_cotacao

                INNER JOIN compras.mapa
                        ON mapa_cotacao.cod_mapa       = mapa.cod_mapa
                       AND mapa_cotacao.exercicio_mapa = mapa.exercicio
            
                INNER JOIN compras.mapa_item
                        ON mapa_item.exercicio = mapa.exercicio
                       AND mapa_item.cod_mapa  = mapa.cod_mapa
                       AND mapa_item.cod_item  = cotacao_fornecedor_item.cod_item
                       AND mapa_item.lote      = cotacao_fornecedor_item.lote

                INNER JOIN compras.mapa_solicitacao
                        ON mapa_solicitacao.exercicio             = mapa_item.exercicio
                       AND mapa_solicitacao.cod_entidade          = mapa_item.cod_entidade
                       AND mapa_solicitacao.cod_solicitacao       = mapa_item.cod_solicitacao
                       AND mapa_solicitacao.cod_mapa              = mapa_item.cod_mapa
                       AND mapa_solicitacao.exercicio_solicitacao = mapa_item.exercicio_solicitacao ";

        return $stSql;
    }
}
