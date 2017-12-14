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
    * classe de mapeamento da tabela compras.nota_fiscal_fornecedor
    * Data de Criação: 12/07/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    $Id: TComprasNotaFiscalFornecedor.class.php 65632 2016-06-03 21:37:05Z michel $
  */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

/**
  * @package URBEM
  * @subpackage Mapeamento
*/
class TComprasNotaFiscalFornecedor extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TComprasNotaFiscalFornecedor()
    {
        parent::Persistente();
        $this->setTabela("compras.nota_fiscal_fornecedor");

        $this->setCampoCod('cod_nota');
        $this->setComplementoChave('cgm_fornecedor');

        $this->AddCampo('cgm_fornecedor'        ,'integer'  ,true  ,''   ,true , true );
        $this->AddCampo('cod_nota'              ,'integer'  ,true  ,''   ,true , false);
        $this->AddCampo('tipo_natureza'         ,'char'     ,true  ,'1'  ,false, true );
        $this->AddCampo('cod_natureza'          ,'integer'  ,true  ,''   ,false, true );
        $this->AddCampo('num_lancamento'        ,'integer'  ,true  ,''   ,false, true );
        $this->AddCampo('exercicio_lancamento'  ,'char'     ,true  ,'4'  ,false, true );
        $this->AddCampo('num_serie'             ,'char'     ,true  ,'9'  ,false, false);
        $this->AddCampo('num_nota'              ,'integer'  ,true  ,''   ,false, false);
        $this->AddCampo('dt_nota'               ,'date'     ,false ,''   ,false, false);
        $this->AddCampo('observacao'            ,'char'     ,false ,'200',false, false);
        $this->AddCampo('tipo'                  ,'char'     ,true  ,'1'  ,false, true );
    }

    public function recuperaItensNotaOrdemCompra(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaItensNotaOrdemCompra",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaItensNotaOrdemCompra()
    {
        $stSql.= "
                SELECT ordem_item.exercicio
                     , ordem_item.cod_entidade
                     , ordem_item.cod_ordem
                     , ordem_item.cod_pre_empenho
                     , ordem_item.num_item
                     , ordem_item.cod_marca
                     , ordem.exercicio_empenho
                     , item_pre_empenho.nom_item
                     , unidade_medida.nom_unidade
                     , centro_custo.cod_centro
                     , centro_custo.nom_centro
                     , centro_custo.cod_item
                     , CASE WHEN catalogo.cod_item IS NULL
                            THEN TRUE
                            ELSE catalogo.ativo
                       END AS ativo
                     , ( ordem_item.quantidade - COALESCE(ordem_item_anulacao.quantidade,0) ) AS solicitado_oc
                     , COALESCE(lancamento_material.quantidade, 0) AS atendido_oc
                     , ( ordem_item.quantidade - COALESCE(ordem_item_anulacao.quantidade,0) ) - (COALESCE(lancamento_material.quantidade, 0)) AS qtde_disponivel_oc
                     , ((item_pre_empenho.vl_total - COALESCE(SUM(empenho_anulado_item.vl_anulado),0))/ item_pre_empenho.quantidade) AS vl_empenhado
                     , centro_custo.bo_item

                  FROM compras.ordem

            INNER JOIN compras.ordem_item
                    ON ordem_item.cod_entidade = ordem.cod_entidade
                   AND ordem_item.exercicio    = ordem.exercicio
                   AND ordem_item.cod_ordem    = ordem.cod_ordem
                   AND ordem_item.tipo         = ordem.tipo

             LEFT JOIN compras.ordem_item_anulacao
                    ON ordem_item_anulacao.exercicio    = ordem_item.exercicio
                   AND ordem_item_anulacao.cod_entidade = ordem_item.cod_entidade
                   AND ordem_item_anulacao.cod_ordem    = ordem_item.cod_ordem
                   AND ordem_item_anulacao.num_item     = ordem_item.num_item
                   AND ordem_item_anulacao.tipo         = ordem_item.tipo

            INNER JOIN empenho.item_pre_empenho
                    ON item_pre_empenho.exercicio       = ordem.exercicio_empenho
                   AND item_pre_empenho.cod_pre_empenho = ordem_item.cod_pre_empenho
                   AND item_pre_empenho.num_item        = ordem_item.num_item

             LEFT JOIN empenho.empenho_anulado_item
                    ON empenho_anulado_item.exercicio       = item_pre_empenho.exercicio
                   AND empenho_anulado_item.cod_pre_empenho = item_pre_empenho.cod_pre_empenho
                   AND empenho_anulado_item.cod_entidade    = ordem.cod_entidade
                   AND empenho_anulado_item.num_item        = item_pre_empenho.num_item

             LEFT JOIN (SELECT item_pre_empenho_julgamento.exercicio
                             , item_pre_empenho_julgamento.cod_pre_empenho
                             , item_pre_empenho_julgamento.num_item
                             , item_pre_empenho_julgamento.cod_item
                             , centro_custo.cod_centro
                             , centro_custo.descricao AS nom_centro
                             , TRUE AS bo_item
                          FROM empenho.item_pre_empenho_julgamento
                    INNER JOIN compras.julgamento_item
                            ON julgamento_item.exercicio      = item_pre_empenho_julgamento.exercicio_julgamento
                           AND julgamento_item.cod_cotacao    = item_pre_empenho_julgamento.cod_cotacao
                           AND julgamento_item.cod_item       = item_pre_empenho_julgamento.cod_item
                           AND julgamento_item.cgm_fornecedor = item_pre_empenho_julgamento.cgm_fornecedor
                           AND julgamento_item.lote           = item_pre_empenho_julgamento.lote
                    INNER JOIN compras.mapa_cotacao
                            ON mapa_cotacao.cod_cotacao       = julgamento_item.cod_cotacao
                           AND mapa_cotacao.exercicio_cotacao = julgamento_item.exercicio
                    INNER JOIN compras.mapa_item
                            ON mapa_item.exercicio = mapa_cotacao.exercicio_mapa
                           AND mapa_item.cod_mapa  = mapa_cotacao.cod_mapa
                           AND mapa_item.cod_item  = julgamento_item.cod_item
                    INNER JOIN almoxarifado.centro_custo
                            ON centro_custo.cod_centro = mapa_item.cod_centro
                         WHERE julgamento_item.ordem = 1

                     UNION ALL

                        SELECT ordem_item.exercicio
                             , ordem_item.cod_pre_empenho
                             , ordem_item.num_item
                             , CASE WHEN ordem_item.cod_item IS NOT NULL
                                    THEN ordem_item.cod_item
                                    ELSE lancamento_ordem.cod_item
                               END AS cod_item
                             , centro_custo.cod_centro
                             , centro_custo.descricao AS nom_centro
                             , CASE WHEN ordem_item.cod_item IS NULL AND lancamento_ordem.cod_item IS NOT NULL
                                    THEN FALSE
                                    ELSE TRUE
                               END AS bo_item
                          FROM compras.ordem_item
                     LEFT JOIN compras.ordem_item_anulacao
                            ON ordem_item_anulacao.exercicio             = ordem_item.exercicio
                           AND ordem_item_anulacao.cod_entidade          = ordem_item.cod_entidade
                           AND ordem_item_anulacao.cod_ordem             = ordem_item.cod_ordem
                           AND ordem_item_anulacao.exercicio_pre_empenho = ordem_item.exercicio_pre_empenho
                           AND ordem_item_anulacao.cod_pre_empenho       = ordem_item.cod_pre_empenho
                           AND ordem_item_anulacao.num_item              = ordem_item.num_item
                           AND ordem_item_anulacao.tipo                  = ordem_item.tipo
                     LEFT JOIN empenho.item_pre_empenho_julgamento
                            ON item_pre_empenho_julgamento.exercicio       = ordem_item.exercicio
                           AND item_pre_empenho_julgamento.cod_pre_empenho = ordem_item.cod_pre_empenho
                           AND item_pre_empenho_julgamento.num_item        = ordem_item.num_item
                     LEFT JOIN almoxarifado.lancamento_ordem
                            ON lancamento_ordem.exercicio             = ordem_item.exercicio
                           AND lancamento_ordem.cod_entidade          = ordem_item.cod_entidade
                           AND lancamento_ordem.cod_ordem             = ordem_item.cod_ordem
                           AND lancamento_ordem.tipo                  = ordem_item.tipo
                           AND lancamento_ordem.cod_pre_empenho       = ordem_item.cod_pre_empenho
                           AND lancamento_ordem.exercicio_pre_empenho = ordem_item.exercicio_pre_empenho
                           AND lancamento_ordem.num_item              = ordem_item.num_item
                    INNER JOIN almoxarifado.centro_custo
                            ON centro_custo.cod_centro = ordem_item.cod_centro
                            OR ( ordem_item.cod_centro IS NULL AND centro_custo.cod_centro = lancamento_ordem.cod_centro )
                         WHERE ordem_item_anulacao.cod_ordem IS NULL
                           AND item_pre_empenho_julgamento.num_item IS NULL
                           AND ordem_item.tipo = '".$this->getDado('tipo')."'
                      GROUP BY ordem_item.exercicio
                             , ordem_item.cod_pre_empenho
                             , ordem_item.num_item
                             , ordem_item.cod_item
                             , centro_custo.cod_centro
                             , centro_custo.descricao
                             , lancamento_ordem.cod_item
                       ) AS  centro_custo
                    ON centro_custo.exercicio       = item_pre_empenho.exercicio
                   AND centro_custo.cod_pre_empenho = item_pre_empenho.cod_pre_empenho
                   AND centro_custo.num_item        = item_pre_empenho.num_item

             LEFT JOIN ( SELECT cod_item
                              , ativo
                           FROM almoxarifado.catalogo_item
                       ) as catalogo
                    ON catalogo.cod_item = centro_custo.cod_item

             LEFT JOIN (    SELECT lancamento_material.cod_item
                                 , SUM( lancamento_material.quantidade ) AS quantidade
                                 , nota_fiscal_fornecedor_ordem.exercicio AS exercicio
                                 , nota_fiscal_fornecedor_ordem.cod_ordem
                                 , nota_fiscal_fornecedor_ordem.cod_entidade
                              FROM compras.nota_fiscal_fornecedor
                        INNER JOIN almoxarifado.lancamento_material
                                ON lancamento_material.exercicio_lancamento = nota_fiscal_fornecedor.exercicio_lancamento
                               AND lancamento_material.cod_natureza         = nota_fiscal_fornecedor.cod_natureza
                               AND lancamento_material.tipo_natureza        = nota_fiscal_fornecedor.tipo_natureza
                               AND lancamento_material.num_lancamento       = nota_fiscal_fornecedor.num_lancamento
                        INNER JOIN almoxarifado.catalogo_item
                                ON catalogo_item.cod_item = lancamento_material.cod_item
                        INNER JOIN compras.nota_fiscal_fornecedor_ordem
                                ON nota_fiscal_fornecedor_ordem.cod_nota       = nota_fiscal_fornecedor.cod_nota
                               AND nota_fiscal_fornecedor_ordem.cgm_fornecedor = nota_fiscal_fornecedor.cgm_fornecedor
                          GROUP BY lancamento_material.cod_item
                                 , nota_fiscal_fornecedor_ordem.exercicio
                                 , nota_fiscal_fornecedor_ordem.cod_ordem
                                 , nota_fiscal_fornecedor_ordem.cod_entidade
                       ) AS lancamento_material
                    ON lancamento_material.exercicio    = ordem.exercicio
                   AND lancamento_material.cod_ordem    = ordem.cod_ordem
                   AND lancamento_material.cod_entidade = ordem.cod_entidade
                   AND lancamento_material.cod_item     = centro_custo.cod_item

            INNER JOIN administracao.unidade_medida
                    ON unidade_medida.cod_grandeza = item_pre_empenho.cod_grandeza
                   AND unidade_medida.cod_unidade  = item_pre_empenho.cod_unidade";

        $stFiltro.= " (ROUND( ( item_pre_empenho.vl_total - COALESCE(empenho_anulado_item.vl_anulado,0 ) ) / item_pre_empenho.quantidade,2 ) > 0) \n\t\t\tAND ";

        if( $this->getDado('exercicio') )
            $stFiltro.= " ordem.exercicio = '".$this->getDado('exercicio')."' \n\t\t\tAND ";
        if( $this->getDado('cod_ordem') )
            $stFiltro.= " ordem.cod_ordem = ".$this->getDado('cod_ordem')." \n\t\t\tAND ";
        if( $this->getDado('cod_entidade') )

        $stFiltro.= " ordem.cod_entidade IN (".$this->getDado('cod_entidade').") \n\t\t\tAND ";
        $stFiltro.= " ordem.tipo = '".$this->getDado('tipo')."' \n\t\t\tAND ";

        if ($stFiltro) {
            $stSql.= ' WHERE '.substr($stFiltro,0,strlen($stFiltro)-4);
        }

        $stSql .= " GROUP BY ordem_item.exercicio
                           , ordem_item.cod_entidade
                           , ordem_item.cod_ordem
                           , ordem_item.cod_pre_empenho
                           , ordem_item.num_item
                           , ordem.exercicio_empenho
                           , item_pre_empenho.nom_item
                           , unidade_medida.nom_unidade
                           , centro_custo.cod_centro
                           , centro_custo.nom_centro
                           , centro_custo.cod_item
                           , ordem_item.quantidade
                           , ordem_item_anulacao.quantidade
                           , lancamento_material.quantidade
                           , item_pre_empenho.vl_total
                           , catalogo.ativo
                           , item_pre_empenho.quantidade
                           , ordem_item.cod_marca
                           , catalogo.cod_item
                           , centro_custo.bo_item ";

        return $stSql;
    }
}
?>
